<?php
/* Copyright (C) 2002-2006 Rodolphe Quiedeville  <rodolphe@quiedeville.org>
 * Copyright (C) 2004      Eric Seigne           <eric.seigne@ryxeo.com>
 * Copyright (C) 2004-2013 Laurent Destailleur   <eldy@users.sourceforge.net>
 * Copyright (C) 2005      Marc Barilley / Ocebo <marc@ocebo.com>
 * Copyright (C) 2005-2012 Regis Houssin         <regis.houssin@capnetworks.com>
 * Copyright (C) 2006      Andre Cianfarani      <acianfa@free.fr>
 * Copyright (C) 2010-2012 Juanjo Menent         <jmenent@2byte.es>
 * Copyright (C) 2012      Christophe Battarel   <christophe.battarel@altairis.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file       htdocs/compta/facture.php
 *	\ingroup    facture
 *	\brief      Page to create/see an invoice
 */

  //require '../main.inc.php';
require("../../main.inc.php");

require_once DOL_DOCUMENT_ROOT . '/fiscal/class/facturedetfiscal.class.php';
require_once DOL_DOCUMENT_ROOT . '/compta/facture/class/facture.class.php';



$langs->load('bills');
$langs->load('companies');
$langs->load('products');
$langs->load('main');
$langs->load('ventas@ventas');

if (! empty($conf->margin->enabled))
	$langs->load('margins');

$sall=trim(GETPOST('sall'));
$projectid=(GETPOST('projectid')?GETPOST('projectid','int'):0);

$id=(GETPOST('id','int')?GETPOST('id','int'):GETPOST('facid','int'));  // For backward compatibility
$ref=GETPOST('ref','alpha');
$socid=GETPOST('socid','int');
$action=GETPOST('action','alpha');
$confirm=GETPOST('confirm','alpha');
$lineid=GETPOST('lineid','int');
$userid=GETPOST('userid','int');
$search_ref=GETPOST('sf_ref')?GETPOST('sf_ref','alpha'):GETPOST('search_ref','alpha');
$search_societe=GETPOST('search_societe','alpha');
$search_montant_ht=GETPOST('search_montant_ht','alpha');
$search_montant_ttc=GETPOST('search_montant_ttc','alpha');
$origin=GETPOST('origin','alpha');
$originid=(GETPOST('originid','int')?GETPOST('originid','int'):GETPOST('origin_id','int')); // For backward compatibility

//PDF
$hidedetails = (GETPOST('hidedetails','int') ? GETPOST('hidedetails','int') : (! empty($conf->global->MAIN_GENERATE_DOCUMENTS_HIDE_DETAILS) ? 1 : 0));
$hidedesc 	 = (GETPOST('hidedesc','int') ? GETPOST('hidedesc','int') : (! empty($conf->global->MAIN_GENERATE_DOCUMENTS_HIDE_DESC) ?  1 : 0));
$hideref 	 = (GETPOST('hideref','int') ? GETPOST('hideref','int') : (! empty($conf->global->MAIN_GENERATE_DOCUMENTS_HIDE_REF) ? 1 : 0));

// Security check
$fieldid = (! empty($ref)?'facnumber':'rowid');
if ($user->societe_id) $socid=$user->societe_id;

$result = restrictedArea($user, 'facture', $id,'','','fk_soc',$fieldid);

// Nombre de ligne pour choix de produit/service predefinis
$NBLINES=4;

$usehm=(! empty($conf->global->MAIN_USE_HOURMIN_IN_DATE_RANGE)?$conf->global->MAIN_USE_HOURMIN_IN_DATE_RANGE:0);

$object = new Facture($db);

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
include_once DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php';
$hookmanager=new HookManager($db);
$hookmanager->initHooks(array('invoicecard'));



/*
 * View
 */

llxHeader('',$langs->trans('Bill'),'EN:Customers_Invoices|FR:Factures_Clients|ES:Facturas_a_clientes');

$form = new Form($db);
$now=dol_now();

//recuperamos

$sql = 'SELECT f.rowid,f.facnumber,f.ref_client,f.ref_ext,f.ref_int,f.type,f.fk_soc,f.amount';
$sql.= ', f.tva, f.localtax1, f.localtax2, f.total, f.total_ttc, f.revenuestamp';
$sql.= ', f.remise_percent, f.remise_absolue, f.remise';
$sql.= ', f.datef as df, f.date_pointoftax';
$sql.= ', f.date_lim_reglement as dlr';
$sql.= ', f.datec as datec';
$sql.= ', f.date_valid as datev';
$sql.= ', f.tms as datem';
$sql.= ', f.note_private, f.note_public, f.fk_statut, f.paye, f.close_code, f.close_note, f.fk_user_author, f.fk_user_valid, f.model_pdf';
$sql.= ', f.fk_facture_source';
$sql.= ', f.fk_mode_reglement, f.fk_cond_reglement, f.fk_projet, f.extraparams';
$sql.= ', f.situation_cycle_ref, f.situation_counter, f.situation_final';
$sql.= ', f.fk_account';
$sql.= ", f.fk_multicurrency, f.multicurrency_code, f.multicurrency_tx, f.multicurrency_total_ht, f.multicurrency_total_tva, f.multicurrency_total_ttc";
$sql.= ', f.fk_incoterms, f.location_incoterms';
$sql.= ' FROM '.MAIN_DB_PREFIX.'facture as f';
$sql.= ' WHERE f.entity = '.$conf->entity;
$sql.= ' AND f.fk_statut >= 1';
//$sql.= ' AND f.rowid >154084 and f.rowid <= 200000';
$result = $db->query($sql);
$error=0;
if ($result)
{
	$num = $db->num_rows($result);
	if ($num)
	{
		require_once DOL_DOCUMENT_ROOT.'/fiscal/class/facturedetfiscalext.class.php';
		$objectdetfiscal = new Facturedetfiscalext($db);

		$jl = 0;
		while ($jl < $num)
		{
			$obj = $db->fetch_object($result);
			$res = $object->fetch($obj->rowid);
			echo '<hr>rowid '.$obj->rowid;
			if ($res ==1)
			{
				$lines = $object->lines;
				foreach ($lines as $i => $val)
				{
					if (!$error)
					{
						$tvacalc = array();
						$tvaht = array();
						$tvattc = array();
						$tvatx = array();

						if ($conf->fiscal->enabled)
						{
						//procesamos el calculo de los impuestos
							$k = 1;
							$qty = $lines[$i]->qty;
							$pu = $lines[$i]->subprice;
							$price_base_type = 'HT';
							if ($conf->global->PRICE_TAXES_INCLUDED)
							{
								$price_base_type = 'TTC';
								$pu = $lines[$i]->price;
								if ($pu <=0 || is_null($pu))
								{
									echo '  newpu '.$pu = $lines[$i]->total_ttc / $lines[$i]->qty;
								}
							}
							$discount = $lines[$i]->remise;
							$remise_percent = $lines[$i]->remise_percent;
							$objectadd = new Stdclass();
							$objectadd->code_facture = $conf->global->FISCAL_CODE_FACTURE_SALES;
							include DOL_DOCUMENT_ROOT.'/fiscal/include/calclinesfiscal.inc.php';
						}

						$newinvoiceline=$lines[$i];
						$rowid = ($newinvoiceline->rowid?$newinvoiceline->rowid:$newinvoiceline->id);

						if (($newinvoiceline->info_bits & 0x01) == 0)
					// We keep only lines with first bit = 0
						{
						// Reset fk_parent_line for no child products and special product
							if (($newinvoiceline->product_type != 9 && empty($newinvoiceline->fk_parent_line)) || $newinvoiceline->product_type == 9) {
								$fk_parent_line = 0;
							}

							$newinvoiceline->fk_parent_line=$fk_parent_line;
							if ($rowid>0)
							{
							//vamos a actualizar el registro fiscal
								if ($conf->fiscal->enabled)
								{
									foreach ((array) $tvacalc AS $code => $value)
									{
									//buscamos
										$filterfiscal = " AND t.fk_facturedet = ".$rowid;
										$filterfiscal.= " AND t.code_tva = '".$code."'";
										$resfiscal = $objectdetfiscal->fetchAll('','',0,0,array(1=>1),'AND',$filterfiscal,true);
										if ($resfiscal ==1)
										{
										//actualizamos
											$objectdetfiscal->tva_tx = $tvatx[$code];
											$objectdetfiscal->total_tva = $value;
											$objectdetfiscal->total_ht = $tvaht[$code];
											$objectdetfiscal->total_ttc = $tvattc[$code];
											$objectdetfiscal->amount_base = $pricebase;
											$objectdetfiscal->fk_user_mod = $user->id;
											$objectdetfiscal->date_mod = dol_now();
											$objectdetfiscal->tms = dol_now();
											$objectdetfiscal->status = 1;
											echo '|resupdate '.$resf = $objectdetfiscal->update($user);
											if ($resf <=0)
											{
												$error++;
												setEventMessages($objectdetfiscal->error,$objectdetfiscal->errors,'errors');
												break;
											}
										}
										elseif(empty($resfiscal))
										{
								//creamos
											$objectdetfiscal->fk_facturedet = $rowid;
											$objectdetfiscal->code_tva = $code;
											$objectdetfiscal->tva_tx = $tvatx[$code];
											$objectdetfiscal->total_tva = $value;
											$objectdetfiscal->total_ht = $tvaht[$code];
											$objectdetfiscal->total_ttc = $tvattc[$code];
											$objectdetfiscal->amount_base = $pricebase;
											$objectdetfiscal->fk_user_create = $user->id;
											$objectdetfiscal->fk_user_mod = $user->id;
											$objectdetfiscal->date_create = $object->date;
											$objectdetfiscal->date_mod = dol_now();
											$objectdetfiscal->tms = dol_now();
											$objectdetfiscal->status = 1;
											echo '|rescreate '.$resf = $objectdetfiscal->create($user);
											if ($resf <=0)
											{
												$error++;
												setEventMessages($objectdetfiscal->error,$objectdetfiscal->errors,'errors');
												break;
											}

										}
										else
										{
											$error++;
											setEventMessages($objectdetfiscal->error,$objectdetfiscal->errors,'errors');
										}
									}
								}
							}
						}
					}
				}
			}
			else
			{
				echo '<hr>nose encontro '.$obj->rowid.' - '.$res;
				$error++;
				setEventMessages($object->error,$object->errors,'errors');
			}
			$jl++;
			echo ' errr '.$error;
		}
	}
}
echo '<hr>fin'.$error;
dol_htmloutput_mesg('',$mesgs);

llxFooter();
$db->close();
?>
