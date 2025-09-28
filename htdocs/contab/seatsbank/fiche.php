<?php
/* Copyright (C) 2001-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2003      Jean-Louis Bergamo   <jlb@j1b.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2004      Christophe Combelles <ccomb@free.fr>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2010-2011 Juanjo Menent        <jmenent@@2byte.es>
 * Copyright (C) 2012      Marcos García         <marcosgdf@gmail.com>
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
 *	    \file       htdocs/compta/bank/account.php
 *		\ingroup    banque
 *		\brief      List of details of bank transactions for an account
 */

require('../../main.inc.php');
require_once DOL_DOCUMENT_ROOT.'/core/lib/bank.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/sociales/class/chargesociales.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/paiement/class/paiement.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/tva/class/tva.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/paiementfourn.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
require_once DOL_DOCUMENT_ROOT."/compta/deplacement/class/deplacement.class.php";

//require_once(DOL_DOCUMENT_ROOT."/core/class/html.form.class.php");
//require_once DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php";
require_once DOL_DOCUMENT_ROOT."/contab/lib/contab.lib.php";
require_once DOL_DOCUMENT_ROOT."/contab/class/contabaccountingext.class.php";
require_once DOL_DOCUMENT_ROOT."/contab/class/contabspendingaccount.class.php";
require_once DOL_DOCUMENT_ROOT."/contab/class/contabperiodoext.class.php";

//asientos
require_once DOL_DOCUMENT_ROOT."/contab/class/contabseatext.class.php";
require_once DOL_DOCUMENT_ROOT."/contab/class/contabseatdetext.class.php";
require_once DOL_DOCUMENT_ROOT."/contab/class/contabseatflagext.class.php";

$langs->load("contab");
$langs->load("banks");
$langs->load("categories");
$langs->load("bills");

if (!$user->rights->contab->seat->read)
	accessforbidden();

$id = (GETPOST('id','int') ? GETPOST('id','int') : GETPOST('account','int'));
$ref = GETPOST('ref','alpha');
$action=GETPOST('action','alpha');
$confirm=GETPOST('confirm','alpha');
$typeop=GETPOST('typeop','int');

$date_startmonth= GETPOST('date_startmonth');
$date_startday  = GETPOST('date_startday');
$date_startyear = GETPOST('date_startyear');
$date_endmonth  = GETPOST('date_endmonth');
$date_endday    = GETPOST('date_endday');
$date_endyear   = GETPOST('date_endyear');
$group_seat     = GETPOST('group_seat');
$type_seat      = GETPOST('type_seat');
$date_seatmonth = GETPOST('date_seatmonth');
$date_seatday   = GETPOST('date_seatday');
$date_seatyear  = GETPOST('date_seatyear');
$history = GETPOST('history');

$lProcesaSeat   = false;

// Security check
$fieldvalue = (! empty($id) ? $id : (! empty($ref) ? $ref :''));
$fieldtype = (! empty($ref) ? 'ref' :'rowid');
if ($user->societe_id) $socid=$user->societe_id;
$result=restrictedArea($user,'banque',$fieldvalue,'bank_account','','',$fieldtype);

$paiementtype=GETPOST('paiementtype','alpha',3);
$req_nb=GETPOST("req_nb",'',3);
$thirdparty=GETPOST("thirdparty",'',3);
$req_desc=GETPOST("req_desc",'',3);
$req_debit=GETPOST("req_debit",'',3);
$req_credit=GETPOST("req_credit",'',3);

$vline=GETPOST("vline");
$page=GETPOST('page','int');
$negpage=GETPOST('negpage','int');
if ($negpage)
{
	$page=$_GET["nbpage"] - $negpage;
	if ($page > $_GET["nbpage"]) $page = $_GET["nbpage"];
}
$year_current = strftime("%Y",dol_now());
$pastmonth = strftime("%m",dol_now()) - 1;
$pastmonthyear = $year_current;
if ($pastmonth == 0)
{
	$pastmonth = 12;
	$pastmonthyear--;
}
//echo '<hr>seat '.$date_seatmonth.' '.$date_seatday.' '.$date_seatyear;
$date_seat=dol_mktime(0, 0, 0, $date_seatmonth, $date_seatday, $date_seatyear);
$date_start=dol_mktime(0, 0, 0, $date_startmonth, $date_startday, $date_startyear);
$date_end=dol_mktime(23, 59, 59, $date_endmonth, $date_endday, $date_endyear);

if (empty($date_start) || empty($date_end)) // We define date_start and date_end
{
	$date_start=dol_get_first_day($pastmonthyear,$pastmonth,false); $date_end=dol_get_last_day($pastmonthyear,$pastmonth,false);
}

$mesg='';
$object = new Account($db);
$form = new Form($db);

$societestatic=new Societe($db);
$chargestatic=new ChargeSociales($db);
$memberstatic=new Adherent($db);
$paymentstatic=new Paiement($db);
$paymentsupplierstatic=new PaiementFourn($db);
$paymentvatstatic=new TVA($db);
$bankstatic=new Account($db);
$banklinestatic=new AccountLine($db);
$facturestatic=new Facture($db);
$facturefournstatic=new FactureFournisseur($db);
$contabaccounting=new Contabaccountingext($db);
$deplacementstatic=new Deplacement($db);

/*
 * Action
 */
$dateop=-1;

if (($action == 'save' ) && ! isset($_POST["cancel"]) && $user->rights->contab->seat->write)
{
	include_once DOL_DOCUMENT_ROOT."/contab/lib/addseats.lib.php";
	if (empty($error))
	{
		$mesg = '<div>'.$langs->trans('Proceso concluido').'</div>';
		$action = 'fin';
	}
	else
	{
		$mesg = '<div>'.$langs->trans('Error, en el Proceso').'</div>';
		$action = 'create';
	}
}

if ($action == 'add' && $id && ! isset($_POST["cancel"]) && $user->rights->contab->seat->write)
{
	if (price2num($_POST["credit"]) > 0)
	{
		$amount = price2num($_POST["credit"]);
	}
	else
	{
		$amount = - price2num($_POST["debit"]);
	}

	$dateop = dol_mktime(12,0,0,$_POST["opmonth"],$_POST["opday"],$_POST["opyear"]);
	$operation=$_POST["operation"];
	$num_chq=$_POST["num_chq"];
	$label=$_POST["label"];
	$cat1=$_POST["cat1"];

	if (! $dateop)    $mesg=$langs->trans("ErrorFieldRequired",$langs->trans("Date"));
	if (! $operation) $mesg=$langs->trans("ErrorFieldRequired",$langs->trans("Type"));
	if (! $amount)    $mesg=$langs->trans("ErrorFieldRequired",$langs->trans("Amount"));

	if (! $mesg)
	{
		$object->fetch($id);
		$insertid = $object->addline($dateop, $operation, $label, $amount, $num_chq, $cat1, $user);
		if ($insertid > 0)
		{
			header("Location: ".$_SERVER['PHP_SELF']."?id=".$id."&action=addline");
			exit;
		}
		else
		{
			$mesg=$object->error;
		}
	}
	else
	{
		$action='addline';
	}
}
//generate
if ($action == 'generate' && $user->rights->contab->seat->write)
{
	$error = 0;
    //validamos el periodo
	$objPeriod = new Contabperiodoext($db);
	$return = $objPeriod->fetch_open($date_seatmonth,$date_seatyear,$date_seat);
	if ($return != 1)
	{
		$error++;
		$mesg='<div class="error">'.$langs->trans('Errorperiodclosenotvalidated').'</div>';
		$action = 'create';
	}
	else
	{
		$seat_month = $date_seatmonth;
		$seat_year  = $date_seatyear;
		$lote = '00300';
		$sblote = '001';

		$nom=$langs->trans("SellsJournal");
		$nomlink='';
		$periodlink='';
		$exportlink='';
		$builddate=time();
		$description=$langs->trans("DescSellsJournal").'<br>';
		if (! empty($conf->global->FACTURE_DEPOSITS_ARE_JUST_PAYMENTS)) $description.= $langs->trans("DepositsAreNotIncluded");
		else  $description.= $langs->trans("DepositsAreIncluded");
		$period=$form->select_date($date_start,'date_start',0,0,0,'',1,0,1).' - '.$form->select_date($date_end,'date_end',0,0,0,'',1,0,1);
	//report_header($nom,$nomlink,$period,$periodlink,$description,$builddate,$exportlink);

		$p = explode(":", $conf->global->MAIN_INFO_SOCIETE_COUNTRY);
		$idpays = $p[0];

		$sql = "SELECT b.rowid, b.dateo as do, b.datev as dv, fk_account, ba.account_number, ";
		$sql.= " b.amount, b.label, b.rappro, b.num_releve, b.num_chq, b.fk_type,";
		$sql.= " ba.rowid as bankid, ba.ref as bankref, ba.label as banklabel";
		if ($mode_search)
		{
			$sql.= ", s.rowid as socid, s.nom as thirdparty";
		}
	/*
	 if ($mode_search && ! empty($conf->adherent->enabled))
	 {

	 }
	 if ($mode_search && ! empty($conf->tax->enabled))
	 {

	 }
	*/
	 $sql.= " FROM ".MAIN_DB_PREFIX."bank_account as ba";
	 $sql.= " INNER JOIN ".MAIN_DB_PREFIX."bank as b ON b.fk_account = ba.rowid ";
	 if ($mode_search)
	 {
	 	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."bank_url as bu1 ON bu1.fk_bank = b.rowid AND bu1.type='company'";
	 	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe as s ON bu1.url_id = s.rowid";
	 }
	 if ($mode_search && ! empty($conf->tax->enabled))
	 {
	 	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."bank_url as bu2 ON bu2.fk_bank = b.rowid AND bu2.type='payment_vat'";
	 	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."tva as t ON bu2.url_id = t.rowid";
	 }
	 if ($mode_search && ! empty($conf->adherent->enabled))
	 {
	    // TODO Mettre jointure sur adherent pour recherche sur un adherent
	    //$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."bank_url as bu3 ON bu3.fk_bank = b.rowid AND bu3.type='company'";
	    //$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe as s ON bu3.url_id = s.rowid";
	 }
	 $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."contab_seat_flag AS csf ON b.rowid = csf.table_id ";
	 $sql.= " WHERE ";
	 $sql.= " ba.entity = ".$conf->entity;
	//$sql.= " AND b.amount > 0";
	 $sql.= " AND ( (csf.table_nom = ''  OR csf.table_nom IS NULL) )";
	//para cobros
	 if ($typeop == 2)
	  $sql.= " AND b.amount > 0 ";//para cobros
	elseif ($typeop == 1)
	  $sql.= " AND b.amount < 0 ";//para pagos
	elseif ($typeop == 3)
	  $sql.= " AND b.amount <> 0 ";// //ambos

	if ($date_start && $date_end) $sql .= " AND b.dateo >= '".$db->idate($date_start)."' AND b.dateo <= '".$db->idate($date_end)."'";
	//$sql.= $sql_rech;
	if ($group_seat == 1)
		$sql.= " ORDER BY b.rowid";
	if ($group_seat == 2)
		$sql.= " ORDER BY b.dateo";
	//echo '<hr>'.$sql;

	dol_syslog("sql=".$sql);
	$result = $db->query($sql);
	if ($result)
	{
	    //por dia
		$tabday   = array();
		$tabcdday = array();
		$tabccday = array();
		$tabmdday = array();
		$tabmcday = array();

	    //por documento
		$tabdoc   = array();
		$tabcddoc = array();
		$tabccdoc = array();
		$tabmddoc = array();
		$tabmcdoc = array();

	    //por periodo
		$tabper   = array();
		$tabcdper = array();
		$tabccper = array();
		$tabmdper = array();
		$tabmcper = array();


		$num = $db->num_rows($result);
		$i=0;
		$resligne=array();
		$aTransfer = array();
		$_SESSION['table_nom'] = 'bank';
		while ($i < $num)
		{
			$obj = $db->fetch_object($result);
		//echo '<hr>ini rowid '.$obj->rowid;
			if (!$aTransfer[$obj->rowid])
			{
		    //documentoa
				$tabdoc[$obj->rowid]["date"] = $obj->do;
				$tabdoc[$obj->rowid]["ref"] = $obj->rowid;
				$tabdoc[$obj->rowid]["type"] = $obj->fk_type;
				$tabdoc[$obj->rowid]["label"] = $obj->banklabel;

		    //day
				$tabday[$obj->do]["date"] = $obj->do;
				$tabday[$obj->do]["type"] = $obj->fk_type;
				$tabday[$obj->do]["label"] = $obj->banklabel;

		    //por periodo //agrupado por mes
				list($anio,$mes,$dia) = explode('-',$obj->do);
				$mesanio = $mes.$anio;
				$tabper[$mesanio]['date'] = $mesanio;
				$tabper[$mesanio]['type'] = $obj->type;
				$tabper[$mesanio]['mes'] = $mes;
				$tabper[$mesanio]['anio'] = $anio;
				$tabper[$mesanio]["label"] = $obj->banklabel;

				if ($group_seat == 1)
					$aArrayTabDoc[$obj->rowid][$obj->rowid] = $obj->rowid;
				elseif ($group_seat == 2)
					$aArrayTabDoc[$obj->do][$obj->rowid] = $obj->rowid;
				elseif ($group_seat == 3)
					$aArrayTabDoc[$mesanio][$obj->rowid] = $obj->rowid;


				$lDebito = false;
		    //ingresos a la cuenta
		    //echo '<hr>amount '.$obj->amount;
				if ($obj->amount > 0)
				{
					$lDebito = true;
			//ingrsos
					$tabcddoc[$obj->rowid][$obj->account_number] += $obj->amount;
					$tabcdday[$obj->do][$obj->account_number] += $obj->amount;
					$tabcdper[$mesanio][$obj->account_number] += $obj->amount;
				}
				else
				{
			//salidas
					$tabccdoc[$obj->rowid][$obj->account_number] += $obj->amount;
					$tabccday[$obj->do][$obj->account_number] += $obj->amount;
					$tabccper[$mesanio][$obj->account_number] += $obj->amount;

				}
		    // echo '<br>ldebito '.$lDebito;
		    // echo '<br>cuenta '.$obj->account_number;
		    // echo '<br>monto '.$tabccdoc[$obj->rowid][$obj->account_number];
		    // Add links after description para contracuenta
				$links = $object->get_url($obj->rowid);
				$account_number = '';
				$suma_cc_total = 0;
				$suma_cd_total = 0;
				$lPagoCliente = false;
				$lPagoProveedor = false;
				if ($obj->fk_type == 'SOLD')
				{
			//movimiento de saldo inicial
					$account_number = $conf->global->CONTAB_ACCOUNT_CAPITAL;
					$suma_cc_total = $obj->amount*-1;
				}
				else
				{
					foreach($links as $key=>$val)
					{
						if ($links[$key]['type']=='payment')
						{
							$lPagoCliente = true;
				//buscamos el pago
							$paymentstatic->fetch($links[$key]['url_id']);
							$sqlp = 'SELECT pf.fk_facture, pf.amount';
							$sqlp.= ' FROM '.MAIN_DB_PREFIX.'paiement_facture as pf, '.MAIN_DB_PREFIX.'facture as f';
							$sqlp.= ' WHERE pf.fk_facture = f.rowid AND fk_paiement = '.$links[$key]['url_id'];
							$resqlp = $db->query($sqlp);
							$amountpaiement=0;
							if ($resqlp)
							{
								$i2=0;
								$num2=$db->num_rows($resqlp);

								while ($i2 < $num2)
								{
									$objap = $db->fetch_object($resqlp);
					//echo '<br>paiment '.$objap->amount;
									$amountpaiement+=$objap->amount;
									$i2++;
								}
							}
							$suma_cc_total += $amountpaiement*-1;
				// $aFacture = array();
				// $aFacture = $paymentstatic->getBillsArray();
				// //echo '<br>::: '.$links[$key]['type'].'<br>';
				// //print_r($aFacture);
				// foreach((array) $aFacture AS $i1 => $fk_facture)
				//   {
				//     $facturestatic->fetch($fk_facture);
				//     //print_r($facturestatic);
				//$suma_cc_total += $facturestatic->total_ttc * -1;
				//			  }
						}
						else if ($links[$key]['type']=='payment_supplier')
						{
							$lPagoProveedor = true;
				//buscamos a quien se pago
							$paymentsupplierstatic->id=$links[$key]['url_id'];
							$paymentsupplierstatic->fetch($links[$key]['url_id']);
							$suma_cd_total += $paymentsupplierstatic->montant;
				// $aFacture = array();
				// $aFacture = $paymentsupplierstatic->getBillsArray('');
				// echo '<pre>';
				// print_r($paymentsupplierstatic);
				// echo '</pre>';

				// foreach((array) $aFacture AS $i1 => $fk_facture)
				//   {
				//     $facturefournstatic->fetch($fk_facture);
				//     echo '<pre>';
				//     print_r($facturefournstatic);
				//     echo '</pre>';

				//     echo '<br>supplier '.$facturefournstatic->total_ttc;
				//     $suma_cd_total += $facturefournstatic->total_ttc;
				//   }
						}
			    else if ($links[$key]['type']=='advance') //anticipos
			    {
				//buscamos el pago
			    	$paymentstatic->fetch($links[$key]['url_id']);
			    	$suma_cc_total += $paymentstatic->amount*-1;
			    }
			    else if ($links[$key]['type']=='deplacement') //gasto honorario
			    {
				//buscamos el gasto
			    	$deplacementstatic->fetch($links[$key]['url_id']);
			    	$suma_cd_total += $deplacementstatic->km;
				//buscamos la cuenta contable del tipo de gasto
			    	$contabspendingaccount = new Contabspendingaccount($db);
			    	$rescsa = $contabspendingaccount->fetch('',$deplacementstatic->type);
			    	if ($rescsa > 0)
			    	{
			    		$account_number = '';
			    		$fk_account  = $contabspendingaccount->fk_account;
				    //buscamos al cuenta
			    		$resaccount = $contabaccounting->fetch($fk_account);
			    		if ($resaccount > 0)
			    			$account_number = $contabaccounting->ref;
			    	}
			    }
			    else if ($links[$key]['type']=='company')
			    {
			    	$societestatic->fetch($links[$key]['url_id']);
			    	$account_number_c = $societestatic->code_compta;
			    	if (empty($societestatic->code_compta))
			    	{
			    		$account_number_c = $conf->global->COMPTA_ACCOUNT_CUSTOMER;
			    	}
			    	$account_number_p = $societestatic->code_compta_fournisseur;
			    	if (empty($societestatic->code_compta))
			    	{
			    		$account_number_p = $conf->global->COMPTA_ACCOUNT_SUPPLIER;
			    	}
			    }
			    else if ($links[$key]['type']=='payment_sc')
			    {
			    	print '<span>'.$langs->trans('Error, no esta definido para contabilizar, contactarse con el Administrador').'</span>';
			    	exit;
			    }
			    else if ($links[$key]['type']=='payment_vat')
			    {
			    	$paymentvatstatic->id=$links[$key]['url_id'];
				//buscamos
			    	$paymentvatstatic->fetch($links[$key]['url_id']);
			    	if ($paymentvatstatic->id == $links[$key]['url_id'])
			    	{
			    		$account_number = $conf->global->COMPTA_VAT_ACCOUNT;
			    		$suma_cd_total += $paymentvatstatic->amount;
			    	}
			    }
			    else if ($links[$key]['type']=='banktransfert')
			    {
				// Do not show link to transfer ince there is no transfer card (avoid confusion). Can already be accessed from transaction detail.
			    	if ($obj->amount > 0)
			    	{
			    		$aTransfer[$links[$key]['url_id']] = $links[$key]['url_id'];
			    		$banklinestatic->fetch($links[$key]['url_id']);
			    		$bankstatic->fetch($banklinestatic->fk_account);
			    		$account_number = $bankstatic->account_number;
				    //$suma_cd_total += $bankstatic->amount;
			    		$suma_cc_total += $banklinestatic->amount;

			    	}
			    	else
			    	{
			    		$aTransfer[$links[$key]['url_id']] = $links[$key]['url_id'];
			    		$banklinestatic->fetch($links[$key]['url_id']);
			    		$bankstatic->fetch($banklinestatic->fk_account);

			    		$account_number = $bankstatic->account_number;
				    //$suma_cc_total += $bankstatic->amount;
			    		$suma_cd_total += $banklinestatic->amount;
			    	}
				//para no repetir en un nuevo asiento
			    	if ($group_seat == 1)
			    		$aArrayTabDoc[$obj->rowid][$links[$key]['url_id']] = $links[$key]['url_id'];
			    	elseif ($group_seat == 2)
			    		$aArrayTabDoc[$obj->do][$links[$key]['url_id']] = $links[$key]['url_id'];
			    	elseif ($group_seat == 3)
			    		$aArrayTabDoc[$mesanio][$links[$key]['url_id']] = $links[$key]['url_id'];
				//fin

				//var_dump($links);
			    }
			    else if ($links[$key]['type']=='member')
			    {

			    }
			    else if ($links[$key]['type']=='sc')
			    {

			    }
			    else
			    {
				// Show link with label $links[$key]['label']
			    	if (! empty($objp->label) && ! empty($links[$key]['label'])) print ' - ';
			    	print '<a href="'.$links[$key]['url'].$links[$key]['url_id'].'">';
			    	if (preg_match('/^\((.*)\)$/i',$links[$key]['label'],$reg))
			    	{
				    // Label generique car entre parentheses. On l'affiche en le traduisant
			    		if ($reg[1]=='paiement') $reg[1]='Payment';
			    		print ' '.$langs->trans($reg[1]);
			    	}
			    	else
			    	{
			    		print ' '.$links[$key]['label'];
			    	}
			    	print '</a>';
			    }
			}
			if (empty($links) && $obj->fk_type == 'LIQ')
			{
			    //es un registro de aumento de caja/banco simple
				if ($lDebito)
				{
					$account_number = $conf->global->CONTAB_PAYABLES_UNDEFINED;
					$suma_cc_total = $obj->amount*-1;
				}
				else
				{
					$account_number = $conf->global->CONTAB_RECEIVABLES_UNDEFINED;
					$suma_cd_total = $obj->amount*-1;
				}
			}
		}
		if ($lPagoCliente == true)
			$account_number = $account_number_c;
		elseif ($lPagoProveedor == true)
			$account_number = $account_number_p;
		if (empty($account_number))
		{
			//no se tiene cuenta contable error
			$error++;
		}
		if ($lDebito)
		{
			$tabccdoc[$obj->rowid][$account_number] += $suma_cc_total;
			$tabccday[$obj->do][$account_number]    += $suma_cc_total;
			$tabccper[$mesanio][$account_number]    += $suma_cc_total;
			$tabccsocietedoc[$obj->rowid][$account_number][$societestatic->id]['ref'] = $societestatic->ref;
			$tabccsocietedoc[$obj->rowid][$account_number][$societestatic->id]['amount'] += $suma_cc_total;
			$tabccsocieteday[$obj->rowid][$account_number][$societestatic->id]['ref'] = $societestatic->ref;
			$tabccsocieteday[$obj->rowid][$account_number][$societestatic->id]['amount'] += $suma_cc_total;
			$tabccsocieteper[$obj->rowid][$account_number][$societestatic->id]['ref'] = $societestatic->ref;
			$tabccsocieteper[$obj->rowid][$account_number][$societestatic->id]['amount'] += $suma_cc_total;
		}
		else
		{
			$tabcddoc[$obj->rowid][$account_number] += $suma_cd_total;
			$tabcdday[$obj->do][$account_number]    += $suma_cd_total;
			$tabcdper[$mesanio][$account_number]    += $suma_cd_total;
			$tabcdsocietedoc[$obj->rowid][$account_number][$societestatic->id]['ref'] = $societestatic->ref;
			$tabcdsocietedoc[$obj->rowid][$account_number][$societestatic->id]['amount'] += $suma_cd_total;
			$tabcdsocieteday[$obj->rowid][$account_number][$societestatic->id]['ref'] = $societestatic->ref;
			$tabcdsocieteday[$obj->rowid][$account_number][$societestatic->id]['amount'] += $suma_cd_total;
			$tabcdsocieteper[$obj->rowid][$account_number][$societestatic->id]['ref'] = $societestatic->ref;
			$tabcdsocieteper[$obj->rowid][$account_number][$societestatic->id]['amount'] += $suma_cd_total;

			// $tabcdsocietedoc[$obj->rowid][$account_number][$societestatic->id]['ref'] = $societestatic->ref;
			// $tabcdsocietedoc[$obj->rowid][$account_number][$societestatic->id]['amount'] += $suma_cc_total;
			// $tabcdsocieteday[$obj->rowid][$account_number][$societestatic->id]['ref'] = $societestatic->ref;
			// $tabcdsocieteday[$obj->rowid][$account_number][$societestatic->id]['amount'] += $suma_cc_total;
			// $tabcdsocieteper[$obj->rowid][$account_number][$societestatic->id]['ref'] = $societestatic->ref;
			// $tabcdsocieteper[$obj->rowid][$account_number][$societestatic->id]['amount'] += $suma_cc_total;

		}
	}
	else
	{
		    //si esta registrado es transferencia y no se puede repetir
		    //echo '<hr>agregando '.$obj->rowid;
		if ($group_seat == 1)
			$aArrayTabDoc[$obj->rowid][$obj->rowid] = $obj->rowid;
		elseif ($group_seat == 2)
			$aArrayTabDoc[$obj->do][$obj->rowid] = $obj->rowid;
		elseif ($group_seat == 3)
			$aArrayTabDoc[$mesanio][$obj->rowid] = $obj->rowid;

	}


	$i++;
}
}
else
{
	dol_print_error($db);
}
$action = 'list';
}
}


if ($action == 'confirm_delete' && $confirm == 'yes' && $user->rights->banque->modifier)
{
	$accline=new AccountLine($db);
	$result=$accline->fetch(GETPOST("rowid"));
	$result=$accline->delete();
}


/*
 * View
 */

$aArrcss = array('/contab/css/style.css');

llxHeader("",$langs->trans("Managementcontab"),''/*helpurl*/,'','','',''/*js*/,$aArrcss);

//verificamos parametros que es necesario definir
if (empty($conf->global->CONTAB_ACCOUNT_CAPITAL) ||
	empty($conf->global->CONTAB_PAYABLES_UNDEFINED) ||
	empty($conf->global->CONTAB_RECEIVABLES_UNDEFINED))
{
	$mesg = '<div class="error">'.$langs->trans('Youneedtodefinetheparametersforprocessing').' <br>CONTAB_ACCOUNT_CAPITAL<br>CONTAB_PAYABLES_UNDEFINED<br>CONTAB_RECEIVABLES_UNDEFINED'.'</div>';
	dol_htmloutput_mesg($mesg);
	exit;
}
//revision de numeracion de asiento
if (empty($conf->global->CONTAB_TSE_TYPENUMERIC) ||
	empty($conf->global->CONTAB_TSE_EGRESO) ||
	empty($conf->global->CONTAB_TSE_INGRESO) ||
	empty($conf->global->CONTAB_TSE_TRASPASO))
{
	$mesg = '<div class="error">'.$langs->trans('Youneedtodefinetheparametersforprocessing').' <br>CONTAB_TSE_TYPENUMERIC<br>CONTAB_TSE_EGRESO<br>CONTAB_TSE_INGRESO<br>CONTAB_TSE_TRASPASO'.'</div>';
	dol_htmloutput_mesg($mesg);
	exit;
}

$form = new Form($db);
//variables fijas por bancos
$type_seat = 3;
$loked = 1;

if ($action == 'create' && $user->rights->contab->seat->write)
{
	print_fiche_titre($langs->trans("Newseatsbank"));

	print "<form action=\"fiche.php\" method=\"post\">\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="generate">';
	print '<input type="hidden" name="type_seat" value="'.$type_seat.'">';
	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

    // date seat
	print '<tr><td class="fieldrequired">'.$langs->trans('Dateseat').'</td><td colspan="2">';
	$form->select_date($date_seat,'date_seat','','','',"crea_seat",1,1);
	print '</td></tr>';
    //lote sblote doc
	print '<tr><td class="fieldrequired">'.$langs->trans('Number').'</td><td colspan="2">';
	print $object->lote.' '.$object->sblote.' '.$object->doc;
	print '</td></tr>';

    //group by seats
	print '<tr><td class="fieldrequired">'.$langs->trans('Generatesseat').'</td><td colspan="2">';
	print select_group_seats($group_seat,'group_seat','','',1);
	print '</td></tr>';

    //type seat
	print '<tr><td class="fieldrequired">'.$langs->trans('Typeseat').'</td><td colspan="2">';
	print select_type_seat($type_seat,'type_seat','','',1,$loked);
	print '</td></tr>';
    // date start
	print '<tr><td class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="2">';
	$form->select_date($date_start,'date_start','','','',"crea_seat",1,1);
	print '</td></tr>';
    // date end
	print '<tr><td class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="2">';
	$form->select_date($date_end,'date_end','','','',"crea_seat",1,1);
	print '</td></tr>';

    // type operation cobros pagos
	print '<tr><td class="fieldrequired">'.$langs->trans('Typeoperation').'</td><td colspan="2">';
	print '<input type="radio" id="typeop" name="typeop" value="1">'.$langs->trans('Payments').'&nbsp;'.'<input type="radio" id="typeop" name="typeop" value="2">'.$langs->trans('Collections').'&nbsp;'.'<input type="radio" id="typeop" name="typeop" value="3">'.$langs->trans('Both');
	print '</td></tr>';

    //history
	print '<tr><td class="fieldrequired">'.$langs->trans('Glosa').'</td><td colspan="2">';
	print '<input id="history" type="text" value="'.$object->history.'" name="history" size="38" maxlength="40">';
	print '</td></tr>';

	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

	print '</form>';
}
else
{
	if ($action == 'list')
      //if ($id > 0 || ! empty($ref))
	{
		if ($error)
		{
			$mesg='<div class="error">'.$langs->trans('Errortheseathaserrorsledgeraccount').'</div>';
			dol_htmloutput_mesg($mesg);
		}
	//vairables totales
		$sumaDebe  = 0;
		$sumaHaber = 0;
		if ($group_seat == 1)
		{
	    //documento
			print '<table class="noborder" width="100%">';
			print '<tr class="liste_titre">';
	    //print "<td>".$langs->trans("JournalNum")."</td>";
			print '<td>'.$langs->trans('Date').'</td>';
			print '<td>'.$langs->trans('Piece').' ('.$langs->trans('Docum').')</td>';
			print '<td>'.$langs->trans('Account').'</td>';
			print '<td>'.$langs->trans('Type').'</td>';
			print '<td align="right">'.$langs->trans('Debit').'</td>';
			print '<td align="right">'.$langs->trans('Credit').'</td>';
			print "</tr>\n";

			$var=true;

			$bankstatic=new Account($db);
	    //$companystatic=new Client($db);

			foreach ($tabdoc as $key => $val)
			{
				$bankstatic->id=$key;
				$bankstatic->ref=$val["ref"];
				$bankstatic->type=$val["type"];
				$bankstatic->label=$val['label'];
		// $companystatic->id=$tabcompany[$key]['id'];
		// $companystatic->name=$tabcompany[$key]['name'];
		// $companystatic->client=$tabcompany[$key]['client'];

				$lines = array(
					array(
						'var' => $tabcddoc[$key],
						'label' => $langs->trans('Account').' '.$bankstatic->label,
						'nomtcheck' => true,
						'inv' => true
						),
					array(
						'var' => $tabccdoc[$key],
						'label' => $langs->trans('Products'),
						)
					);

				foreach ($lines as $line)
				{

					foreach ($line['var'] as $k => $mt)
					{
			//buscamos la cuenta contable
						$contabaccounting->fetch('',$k);
			//echo '<br>k '.$k.' '.$mt;
						if (isset($line['nomtcheck']) || $mt)
						{
							print "<tr ".$bc[$var]." >";
			    //print "<td>".$conf->global->COMPTA_JOURNAL_SELL."</td>";
							print "<td>".dol_print_date($db->jdate($val["date"]))."</td>";
							print "<td>".$bankstatic->id."</td>";
							if ($contabaccounting->ref == $k)
								print "<td>".$k."</td><td>".$contabaccounting->cta_name."</td>";
							else
								print "<td>".$k."</td><td>&nbsp;</td>";
							$aAsiento[$k]['date']      = $val['date'];
							$aAsiento[$k]['groupseat'] = $group_seat;
							$aAsiento[$k]['account']   = $k;
			    //echo '<hr>mt '.$mt;
							if (isset($line['inv']))
							{
								print '<td align="right">'.($mt>=0?price($mt):'')."</td>";
								print '<td align="right">'.($mt<0?price(-$mt):'')."</td>";
								$aAsiento[$k]['deudor']   = ($mt>=0?$k:'');
								$aAsiento[$k]['acreedor'] = ($mt<0?$k:'');
								$aAsiento[$k]['amount']   = ($mt<0?price2num(-$mt,'MU'):price2num($mt,'MU'));
								$sumaDebe  += ($mt>=0?$mt:0);
								$sumaHaber += ($mt<0?-$mt:0);
							}
							else
							{
								print '<td align="right">'.($mt>=0?price($mt):'')."</td>";
								print '<td align="right">'.($mt<0?price(-$mt):'')."</td>";
								$aAsiento[$k]['deudor']   = ($mt>=0?$k:'');
								$aAsiento[$k]['acreedor'] = ($mt<0?$k:'');
								$aAsiento[$k]['amount']   = ($mt<0?price2num(-$mt,'MU'):price2num($mt,'MU'));
								$sumaDebe  += ($mt>=0?$mt:0);
								$sumaHaber += ($mt<0?-$mt:0);
							}
							print "</tr>";
						}
					}
				}
				$aArraySeat[$key] = $aAsiento;
				$aAsiento = array();
				$var = !$var;
			}
			$classRead = 'liste_total';
			if (price($sumaDebe) != price($sumaHaber))
			{
				$classRead = 'bgread';
				$sep = True;
				$lProcesaSeat = false;
			}
			else
			{
				$lProcesaSeat = true;
			}
			print '<tr class="'.$classRead.'"><td align="left" colspan="4">';
			if ($sep) print '&nbsp;';
			else print $langs->trans("CurrentBalance");
			print '</td>';
			print '<td align="right" nowrap>'.price($sumaDebe).'</td>';
			print '<td align="right" nowrap>'.price($sumaHaber).'</td>';
			print '</tr>';

			print "</table>";
		}

	//dia
		if ($group_seat == 2)
		{
	    //documento
			print '<table class="noborder" width="100%">';
			print '<tr class="liste_titre">';
	    //print "<td>".$langs->trans("JournalNum")."</td>";
			print '<td>'.$langs->trans('Date').'</td>';
	    //print '<td>'.$langs->trans('Piece').' ('.$langs->trans('Docum').')</td>';
			print '<td>'.$langs->trans('Account').'</td>';
			print '<td>'.$langs->trans('Type').'</td>';
			print '<td align="right">'.$langs->trans('Debit').'</td>';
			print '<td align="right">'.$langs->trans('Credit').'</td>';
			print "</tr>\n";

			$var=true;

			$bankstatic=new Account($db);
	    //$companystatic=new Client($db);

			foreach ($tabday as $key => $val)
			{
				$bankstatic->id=$key;
				$bankstatic->ref=$val["ref"];
				$bankstatic->type=$val["type"];
				$bankstatic->label=$val['label'];
		// $companystatic->id=$tabcompany[$key]['id'];
		// $companystatic->name=$tabcompany[$key]['name'];
		// $companystatic->client=$tabcompany[$key]['client'];

				$lines = array(
					array(
						'var' => $tabcdday[$key],
						'label' => $langs->trans('Account').' '.$bankstatic->label,
						'nomtcheck' => true,
						'inv' => true
						),
					array(
						'var' => $tabccday[$key],
						'label' => $langs->trans('Products'),
						)
					);

				foreach ($lines as $line)
				{
					foreach ($line['var'] as $k => $mt)
					{
			//buscamos la cuenta contable
						$contabaccounting->fetch('',$k);
			//echo '<br>k '.$k.' '.$mt;
						if (isset($line['nomtcheck']) || $mt)
						{
							print "<tr ".$bc[$var]." >";
			    //print "<td>".$conf->global->COMPTA_JOURNAL_SELL."</td>";
							print "<td>".$val["date"]."</td>";
			    //print "<td>".$bankstatic->id."</td>";
							if ($contabaccounting->ref == $k)
								print "<td>".$k."</td><td>".$contabaccounting->cta_name."</td>";
							else
								print "<td>".$k."</td><td>&nbsp;</td>";
							$aAsiento[$k]['date']      = $val['date'];
							$aAsiento[$k]['groupseat'] = $group_seat;
							$aAsiento[$k]['account']   = $k;


							if (isset($line['inv']))
							{
								print '<td align="right">'.($mt>=0?price($mt):'')."</td>";
								print '<td align="right">'.($mt<0?price(-$mt):'')."</td>";
								$aAsiento[$k]['deudor']   = ($mt>=0?$k:'');
								$aAsiento[$k]['acreedor'] = ($mt<0?$k:'');
								$aAsiento[$k]['amount']   = ($mt<0?price2num(-$mt,'MU'):price2num($mt,'MU'));

								$sumaDebe  += ($mt>=0?$mt:0);
								$sumaHaber += ($mt<0?-$mt:0);
							}
							else
							{
								print '<td align="right">'.($mt>=0?price($mt):'')."</td>";
								print '<td align="right">'.($mt<0?price(-$mt):'')."</td>";
								$aAsiento[$k]['deudor']   = ($mt>=0?$k:'');
								$aAsiento[$k]['acreedor'] = ($mt<0?$k:'');
								$aAsiento[$k]['amount']   = ($mt<0?price2num(-$mt,'MU'):price2num($mt,'MU'));

								$sumaDebe  += ($mt>=0?$mt:0);
								$sumaHaber += ($mt<0?-$mt:0);
							}

							print "</tr>";
						}
					}
				}
				$aArraySeat[$key] = $aAsiento;
				$aAsiento = array();
				$var = !$var;
			}
			$classRead = 'liste_total';
			if (price($sumaDebe) != price($sumaHaber))
			{
				$classRead = 'bgread';
				$sep = True;
				$lProcesaSeat = false;
			}
			else
			{
				$lProcesaSeat = true;
			}
			print '<tr class="'.$classRead.'"><td align="left" colspan="3">';
			if ($sep) print '&nbsp;';
			else print $langs->trans("CurrentBalance");
			print '</td>';
			print '<td align="right" nowrap>'.price($sumaDebe).'</td>';
			print '<td align="right" nowrap>'.price($sumaHaber).'</td>';
			print '</tr>';

			print "</table>";
		}


	//periodo
		if ($group_seat == 3)
		{
	    //documento
			print '<table class="noborder" width="100%">';
			print '<tr class="liste_titre">';
	    //print "<td>".$langs->trans("JournalNum")."</td>";
			print '<td>'.$langs->trans('Period').'</td>';
	    //print '<td>'.$langs->trans('Piece').' ('.$langs->trans('Docum').')</td>';
			print '<td>'.$langs->trans('Account').'</td>';
			print '<td>'.$langs->trans('Name').'</td>';
			print '<td align="right">'.$langs->trans('Debit').'</td>';
			print '<td align="right">'.$langs->trans('Credit').'</td>';
			print "</tr>\n";

			$var=true;

			$bankstatic=new Account($db);
	    //$companystatic=new Client($db);

			foreach ($tabper as $key => $val)
			{
				$bankstatic->id=$key;
				$bankstatic->ref=$val["ref"];
				$bankstatic->type=$val["type"];
				$bankstatic->label=$val['label'];
		// $companystatic->id=$tabcompany[$key]['id'];
		// $companystatic->name=$tabcompany[$key]['name'];
		// $companystatic->client=$tabcompany[$key]['client'];

				$lines = array(
					array(
						'var' => $tabcdper[$key],
						'label' => $langs->trans('Account').' '.$bankstatic->label,
						'nomtcheck' => true,
						'inv' => true
						),
					array(
						'var' => $tabccper[$key],
						'label' => $langs->trans('Products'),
						)
					);

				foreach ($lines as $line)
				{
					foreach ($line['var'] as $k => $mt)
					{
			//buscamos la cuenta contable
						$contabaccounting->fetch('',$k);
			//echo '<br>k '.$k.' '.$mt;
						if (isset($line['nomtcheck']) || $mt)
						{
							print "<tr ".$bc[$var]." >";
			    //print "<td>".$conf->global->COMPTA_JOURNAL_SELL."</td>";
							print "<td>".$val["date"]."</td>";
			    //print "<td>".$bankstatic->id."</td>";
							if ($contabaccounting->ref == $k)
								print "<td>".$k."</td><td>".$contabaccounting->cta_name."</td>";
							else
								print "<td>".$k."</td><td>&nbsp;</td>";
							$aAsiento[$k]['date']      = $val['date'];
							$aAsiento[$k]['groupseat'] = $group_seat;
							$aAsiento[$k]['account']   = $k;


							if (isset($line['inv']))
							{
								print '<td align="right">'.($mt>=0?price($mt):'')."</td>";
								print '<td align="right">'.($mt<0?price(-$mt):'')."</td>";
								$aAsiento[$k]['deudor']   = ($mt>=0?$k:'');
								$aAsiento[$k]['acreedor'] = ($mt<0?$k:'');
								$aAsiento[$k]['amount']   = ($mt<0?price2num(-$mt,'MU'):price2num($mt,'MU'));
								$sumaDebe  += ($mt>=0?$mt:0);
								$sumaHaber += ($mt<0?-$mt:0);
							}
							else
							{
								print '<td align="right">'.($mt>=0?price($mt):'')."</td>";
								print '<td align="right">'.($mt<0?price(-$mt):'')."</td>";
								$aAsiento[$k]['deudor']   = ($mt>=0?$k:'');
								$aAsiento[$k]['acreedor'] = ($mt<0?$k:'');
								$aAsiento[$k]['amount']   = ($mt<0?price2num(-$mt,'MU'):price2num($mt,'MU'));
								$sumaDebe  += ($mt>=0?$mt:0);
								$sumaHaber += ($mt<0?-$mt:0);
							}
							print "</tr>";
						}
					}
				}
				$aArraySeat[$key] = $aAsiento;
				$aAsiento = array();
				$var = !$var;
			}
			$classRead = 'liste_total';
			if (price($sumaDebe) != price($sumaHaber))
			{
				$classRead = 'bgread';
				$sep = True;
				$lProcesaSeat = false;
			}
			else
			{
				$lProcesaSeat = true;
			}
			print '<tr class="'.$classRead.'"><td align="left" colspan="3">';
			if ($sep) print '&nbsp;';
			else print $langs->trans("CurrentBalance");
			print '</td>';
			print '<td align="right" nowrap>'.price($sumaDebe).'</td>';
			print '<td align="right" nowrap>'.price($sumaHaber).'</td>';
			print '</tr>';

			print "</table>";
		}
		print "\n</div>\n";
		if ($lProcesaSeat == true && !empty($aArraySeat) && empty($error))
		{
			$_SESSION['aArraySeat'] = $aArraySeat;
			$_SESSION['aArrayTabdoc']  = $aArrayTabDoc;
			print '<div>';
			print "<form action=\"fiche.php\" method=\"post\">\n";
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="save">';
			print '<input type="hidden" name="type_seat" value="'.$type_seat.'">';
			print '<input type="hidden" name="date_seat" value="'.$date_seat.'">';
			print '<input type="hidden" name="seat_month" value="'.$seat_month.'">';
			print '<input type="hidden" name="seat_year" value="'.$seat_year.'">';
			print '<input type="hidden" name="lote" value="'.$lote.'">';
			print '<input type="hidden" name="sblote" value="'.$sblote.'">';

			dol_htmloutput_mesg($mesg);

			print '<table class="border" width="100%">';

	    //history
			print '<tr><td class="fieldrequired">'.$langs->trans('Glosa').'</td><td colspan="2">';
			print '<input id="history" type="text" value="'.$history.'" name="history" size="38" maxlength="240">';
			print '</td></tr>';

			print '</table>';

			print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

			print '</form>';

			print '</div>';
		}
	}
}
if ($action == 'fin')
{
	print_fiche_titre($langs->trans("Newseatsbank"));

	dol_htmloutput_mesg($mesg);

	print '<p>';
	print $langs->trans('Accounting entry generated correctly');
	print '</p>';

}

llxFooter();

$db->close();
?>
