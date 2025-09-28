<?php
/* Copyright (C) 2007-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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
 *   	\file       purchase/purchaserequest_card.php
 *		\ingroup    purchase
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-03-10 09:46
 */

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test
//if (! defined('NOSTYLECHECK'))   define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');				// If this page is public (can be called outside logged session)

// Change this following line to use the correct relative path (../, ../../, etc)
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php');
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.getutil.class.php');
require_once(DOL_DOCUMENT_ROOT.'/user/class/user.class.php');
require_once(DOL_DOCUMENT_ROOT.'/product/class/product.class.php');
require_once(DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php');

require_once DOL_DOCUMENT_ROOT . '/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/modules/supplier_proposal/modules_supplier_proposal.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/supplier_proposal.lib.php';


dol_include_once('/purchase/class/purchaserequestext.class.php');
dol_include_once('/purchase/class/purchaserequestdetext.class.php');

dol_include_once('/purchase/class/supplierproposalext.class.php');
dol_include_once('/purchase/class/supplierproposaladd.class.php');
dol_include_once('/purchase/class/supplierproposaldetadd.class.php');

dol_include_once('/purchase/lib/purchase.lib.php');
if (! empty($conf->monprojet->enabled))
{
	dol_include_once('/monprojet/class/projectext.class.php');
	dol_include_once('/monprojet/class/html.formprojetext.class.php');
	dol_include_once('/monprojet/lib/verifcontact.lib.php');
}
if ($conf->poa->enabled)
{
	dol_include_once('/poa/class/poaobjetiveext.class.php');
	dol_include_once('/poa/class/poastructureext.class.php');
	dol_include_once('/poa/class/poaactivityext.class.php');
	dol_include_once('/poa/class/poapoaext.class.php');
	dol_include_once('/poa/class/poaprevsegext.class.php');
	dol_include_once('/poa/class/poaprevext.class.php');
	dol_include_once('/poa/class/poapartidapreext.class.php');
	dol_include_once('/poa/class/poapartidapredetext.class.php');
	dol_include_once('/poa/class/poapartidacomext.class.php');
	dol_include_once('/poa/class/poaprocesscontratext.class.php');
	dol_include_once('/poa/class/poaprocessext.class.php');
	dol_include_once('/poa/lib/poa.lib.php');
}
if ($conf->orgman->enabled)
{
	dol_include_once('/orgman/class/partidaproduct.class.php');
	dol_include_once('/orgman/class/cpartida.class.php');
}
// Load traductions files requiredby by page
$langs->load("poa");
$langs->load("purchase");
$langs->load("other");
$langs->load('companies');
$langs->load('supplier_proposal');
$langs->load('compta');
$langs->load('bills');
$langs->load('propal');
$langs->load('orders');
$langs->load('products');
$langs->load("deliveries");
$langs->load('sendings');
if (! empty($conf->margin->enabled))
	$langs->load('margins');

// Get parameters
$id		= GETPOST('id','int');
$idreg		= GETPOST('idreg','int');
$action		= GETPOST('action','alpha');
$confirm	= GETPOST('confirm','alpha');
$invite 		= GETPOST('invite','alpha');
$backtopage 	= GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_ref_ext=GETPOST('search_ref_ext','alpha');
$search_ref_int=GETPOST('search_ref_int','alpha');
$search_fk_projet=GETPOST('search_fk_projet','int');
$search_fk_departament=GETPOST('search_fk_departament','int');
$search_fk_user_author=GETPOST('search_fk_user_author','int');
$search_fk_user_modif=GETPOST('search_fk_user_modif','int');
$search_fk_user_valid=GETPOST('search_fk_user_valid','int');
$search_fk_user_cloture=GETPOST('search_fk_user_cloture','int');
$search_note_private=GETPOST('search_note_private','alpha');
$search_note_public=GETPOST('search_note_public','alpha');
$search_model_pdf=GETPOST('search_model_pdf','alpha');
$search_fk_shipping_method=GETPOST('search_fk_shipping_method','int');
$search_import_key=GETPOST('search_import_key','alpha');
$search_extraparams=GETPOST('search_extraparams','alpha');
$search_status=GETPOST('search_status','int');

verify_year();
$period_year = $_SESSION['period_year'];

$aTypeprocess = array(1=>array('WELL' => $langs->trans('Goods')),0=>array('OTHERSERVICE'=>$langs->trans('Otherservice'),'SERVICE'=>$langs->trans('Service')));
// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Purchaserequestext($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}
$objuser = new User($db);
$objectdet=new Purchaserequestdetext($db);
$product = new Product($db);
$objSociete = new Societe($db);
// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('purchaserequest'));
$extrafields = new ExtraFields($db);

$aItemTransf = array();
$itemTransf = array();
$transf = array();
if (! empty($_SESSION['invitSupplier'])) $itemTransf=json_decode($_SESSION['invitSupplier'],true);
//if (! empty($_SESSION['transfo'])) $transf=json_decode($_SESSION['transfo'],true);


/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	//aprovacion del cuadro comparativo
	//generacion de pedidos a proveedor
	//registro de la contratacion en el Poa
	//registro de las partidas comprometidas en poapartidacom

	if ($action == 'confirm_appcomp' && $confirm == 'yes' && $user->rights->purchase->sup->valcomp)
	{
		$code_type_purchase = GETPOST('code_type_purchase','alpha');
		$now = dol_now();
		$aPost = unserialize($_SESSION['aSupplierapp']);
		$_POST = $aPost[$object->id];
		$aPurchaselinesoc = GETPOST('purchaselinesoc');
		$aPurchase = $aPurchaselinesoc[$object->id];
		foreach ((array) $aPurchase AS $fk_line => $fk_supplier)
			$aData[$fk_supplier][$fk_line] = $fk_line;
		//echo '<pre>';
		//print_r($aPurchaselinesoc);

		//print_r($aData);
		//echo '<pre>';exit;
		//procedemos a crear los pedidos
		if (count($aData)>0)
		{
			$new = dol_now();
			$aProdppd = array();
			$db->begin();
			require_once DOL_DOCUMENT_ROOT.'/productext/class/productadd.class.php';
			require_once DOL_DOCUMENT_ROOT.'/purchase/class/fournisseurcommandeext.class.php';
			require_once DOL_DOCUMENT_ROOT.'/purchase/class/commandefournisseuradd.class.php';
			require_once DOL_DOCUMENT_ROOT.'/purchase/class/commandefournisseurdetadd.class.php';

			$objectc = new Fournisseurcommandeext($db);
			$objectadd = new Commandefournisseuradd($db);
			$objectdetadd = new Commandefournisseurdetadd($db);
			$objSupplier = new Supplierproposalext($db);
			$objSupplieradd = new Supplierproposaladd($db);
			$objSupplierdetadd = new Supplierproposaldetadd($db);
			//array para la linea de preventivos
			$aPartidapre = array();
			if ($conf->poa->enabled && $conf->global->PURCHASE_INTEGRATED_POA)
			{
				//buscamos el inicio de proceso
				$fk_process = 0;
				if ($object->fk_poa_prev > 0)
				{
					$objPoaprev = new Poaprevext($db);
					$res = $objPoaprev->fetch($object->fk_poa_prev);
					if ($res == 1)
					{
						//cambiamos de estado a comprometido
						if ($objPoaprev->statut == 1)
						{
							$objPoaprev->statut = 2;
							$objPoaprev->fk_user_mod = $user->id;
							$objPoaprev->datem = dol_now();
							$res = $objPoaprev->update($user);
							if ($res <=0)
							{
								$error++;
								setEventMessages($objPoaprev->error,$objPoaprev->errors,'errors');
							}
						}
					}

					$search_prev = $object->fk_poa_prev;
					$objPrevseg = new Poaprevsegext($db);
					$res = $objPrevseg->get_idprocess($search_prev);
					if ($res>0) $fk_process = $objPrevseg->idProcess;
					//recuperamos la lista de partidas pre
					$objPartidapre 		= new Poapartidapreext($db);
					$objPartidacom	= new Poapartidacomext($db);
					$objPartidapredet 	= new Poapartidapredetext($db);
					$respp = $objPartidapre->fetchAll('ASC','rowid',0,0,array('statut'=>1),'AND'," AND t.fk_poa_prev = ".$object->fk_poa_prev);
					if ($respp>0)
					{
						$linepre = $objPartidapre->lines;

						foreach ($linepre AS $j => $line)
						{
							$aPartidapre[$line->id] = $line->id;
							//vamos a armar los productos de cada partida para su actualizacion de adjudicacion
							$resppd = $objPartidapredet->fetchAll('ASC','rowid',0,0,array('statut'=>1),'AND'," AND t.fk_poa_partida_pre = ".$line->id);
							if ($resppd>0)
							{
								foreach ($objPartidapredet->lines AS $k => $linek)
								{
									$aProdppd[$linek->originid] = $linek->id;
								}
							}
							//vamos a crear registro vacio en poapartidacom
							$objPartidacom->initAsSpecimen();
							$objPartidacom->fk_poa_partida_pre = $line->id;
							$objPartidacom->fk_poa_prev = $line->fk_poa_prev;
							$objPartidacom->fk_structure = $line->fk_structure;
							$objPartidacom->fk_poa = $line->fk_poa;
							$objPartidacom->fk_contrat = 0;
							$objPartidacom->fk_contrato = 0;
							$objPartidacom->partida = $line->partida;
							$objPartidacom->amount = 0;
							$objPartidacom->fk_user_create = $user->id;
							$objPartidacom->fk_user_mod = $user->id;
							$objPartidacom->date_create = $new;
							$objPartidacom->datec = $new;
							$objPartidacom->datem = $new;
							$objPartidacom->tms = $new;
							$objPartidacom->statut = 1;
							$objPartidacom->active = 1;
							$respc = $objPartidacom->create($user);
							if ($respc <=0)
							{
								$error++;
								setEventMessages($objPartidacom->error,$objPartidacom->errors,'errors');
							}
						}
					}
					//cambiamos de estado el preventivo

				}
			}
			foreach($aData AS $fk_supplier => $row)
			{
				if ($fk_supplier<=0) continue;
				//echo '<hr>registros aprobados para el proveedor '.
				$nRegapp = count ($row);
				$origin = 'supplier_proposal';
				$originid = $fk_supplier;
				$resa = $objSupplier->fetch($fk_supplier);
				$resb = $objSupplieradd->fetch(0,$fk_supplier);
				// Creation commande
				$objectc->ref 			= '(PROV)'.$objSupplier->id;
				$objectc->origin 		= $origin;
				$objectc->originid 		= $originid;
				$objectc->ref_supplier  	= $objSupplier->ref;
				$objectc->socid         		= $objSupplier->socid;
				$objectc->fk_soc         		= $objSupplier->socid;
				$objectc->cond_reglement_id 	= $objSupplier->fk_cond_reglement;
				$objectc->mode_reglement_id = $objSupplier->fk_mode_reglement;
				$objectc->fk_account        	= $objSupplier->fk_account;
				$objectc->note_private	 	= $objSupplier->note_private;
				$objectc->note_public   	= $objSupplier->note_public;
				$objectc->date_livraison 	= $objSupplier->date_livraison;
				$objectc->fk_incoterms 	= $objSupplier->incoterm_id;
				$objectc->total_ht 		= $objSupplier->total_ht;
				$objectc->total_ttc 		= $objSupplier->total_ttc;
				$objectc->location_incoterms 	= $objSupplier->location_incoterms;
				$objectc->multicurrency_code = $objSupplier->multicurrency_code;
				$objectc->multicurrency_tx 	= $objSupplier->originmulticurrency_tx;
				$objectc->fk_project       	= $objSupplier->projectid;
				//links
				$objectc->linked_objects [$objectc->origin] = $objectc->origin_id;
				$other_linked_objects = GETPOST('other_linked_objects', 'array');
				if (! empty($other_linked_objects)) {
					$objectc->linked_objects = array_merge($objectc->linked_objects, $other_linked_objects);
				}

				//agregando a la tabla adicional

				$objectadd->code_facture = ($objSupplieradd->code_facture?$objSupplieradd->code_facture:$conf->global->FISCAL_CODE_FACTURE_PURCHASE);
				$objectadd->code_type_purchase = ($objSupplieradd->code_type_purchase?$objSupplieradd->code_type_purchase:($code_type_purchase?$code_type_purchase:GETPOST('code_type_purchase')));
				$objectadd->discount = 0;
				$objectadd->datec = dol_now();
				$objectadd->fk_user_create = $user->id;
				$objectadd->fk_user_mod = $user->id;
				$objectadd->date_create = dol_now();
				$objectadd->date_mod = dol_now();
				$objectadd->tms = dol_now();
				$objectadd->status = 1;

				//echo '<hr>errr '.$error;
				if (! $error)
				{
					if (! empty($origin) && ! empty($originid))
					{
						if ($origin == 'order' || $origin == 'commande')
						{
							$element = $subelement = 'commande';
							$subelementdetreg = 'commandefournisseurdet';
						}
						else
						{
							$element = 'supplier_proposal';
							$subelement = 'supplier_proposal';
							$element = 'purchase';
							$subelement = 'supplierproposalext';
							$subelementdetadd = 'supplierproposaldetadd';
							$subelementdetreg = 'supplierproposaldet';
						}

						$objectc->origin = $origin;
						$objectc->origin_id = $originid;

						// Possibility to add external linked objects with hooks
						$objectc->linked_objects [$objectc->origin] = $objectc->origin_id;
						$other_linked_objects = GETPOST('other_linked_objects', 'array');
						if (! empty($other_linked_objects)) {
							$objectc->linked_objects = array_merge($objectc->linked_objects, $other_linked_objects);
						}
						$resppc = 0;
						//echo '<hr>idccreate '.
						$idc = $objectc->create($user);
						if ($conf->poa->enabled && $conf->global->PURCHASE_INTEGRATED_POA)
						{
							//vamos a crear el registro de en poaprocesscontrat
							$objPoaprocesscontrat = new Poaprocesscontrat($db);
							$objPoaprocesscontrat->fk_poa_process = $fk_process;
							$objPoaprocesscontrat->fk_contrat = $idc;
							$objPoaprocesscontrat->date_create = $now;
							$objPoaprocesscontrat->fk_user_create = $user->id;
							$objPoaprocesscontrat->fk_user_mod = $user->id;
							$objPoaprocesscontrat->datec = $now;
							$objPoaprocesscontrat->datem = $now;
							$objPoaprocesscontrat->tms = $now;
							$objPoaprocesscontrat->statut = 1;
							$resppc = $objPoaprocesscontrat->create($user);
							if ($resppc <=0)
							{
								$error++;
								setEventMessages($objPoaprocesscontrat->error,$objPoaprocesscontrat->errors,'errors');
							}
						}
						if ($idc > 0 && !$error)
						{
							$objectadd->fk_commande_fournisseur = $idc;
							//echo '<hr>resaddx '.
							$resadd = $objectadd->create($user);
							if ($resadd<=0)
							{
								setEventMessages($objectadd->error,$objectadd->errors,'errors');
								$error++;
							}
							//echo '<hr>errx '.$error;

							dol_include_once('/' . $element . '/class/' . $subelement . '.class.php');

							$classname = 'SupplierProposalext';
							$srcobject = new $classname($db);

							//para la tabla det
							dol_include_once('/' . $element . '/class/' . $subelementadd . '.class.php');
							$classname = 'Supplierproposaldetadd';
							$srcobjectdet = new $classname($db);

							dol_syslog("Try to find source object origin=" . $objectc->origin . " originid=" . $objectc->origin_id . " to add lines");
							$result = $srcobject->fetch($objectc->origin_id);
							if ($result > 0)
							{
								$objectc->set_date_livraison($user, $srcobject->date_livraison);
								$objectc->set_id_projet($user, $srcobject->fk_project);

								//$lines = $srcobject->lines;
								//if (empty($lines) && method_exists($srcobject, 'fetch_linesadd'))
								//echo '<HR>consultaANTES DE ';
								//echo '<hr>resmet '.$resmet = method_exists($srcobject, 'fetch_linesadd');
								if (method_exists($srcobject, 'fetch_linesadd'))
								{
									//echo '<hr>GENERA NUEVAMENTE LINES';
									//echo '<hr>rrrr '.
									$srcobject->fetch_linesadd();
									$lines = $srcobject->lines;
								}
								//echo '<pre>';
								//print_r($lines);
								//echo '</pre>';
								$fk_parent_line = 0;
								//echo '<hr>NUMERO DE REGDETALLE '.
								$num = count($lines);
								$productsupplier = new ProductFournisseur($db);
								//vamos a verificar la cantidad de productos aceptados
								$nProd = 0;
								for($i = 0; $i < $num; $i ++)
								{
									//buscamos la tabla adicional para comparar
									$srcobjectdet->fetch(0,$lines[$i]->id);
									if (!$row[$srcobjectdet->fk_object]) continue;
									if (empty($lines[$i]->subprice) || $lines[$i]->qty <= 0) continue;
									$nProd++;
									$label = (! empty($lines[$i]->label) ? $lines[$i]->label : '');
									$desc = (! empty($lines[$i]->desc) ? $lines[$i]->desc : $lines[$i]->libelle);
									$product_type = (! empty($lines[$i]->product_type) ? $lines[$i]->product_type : 0);

									// Reset fk_parent_line for no child products and special product
									if (($lines[$i]->product_type != 9 && empty($lines[$i]->fk_parent_line)) || $lines[$i]->product_type == 9) {
										$fk_parent_line = 0;
									}

									// Extrafields
									if (empty($conf->global->MAIN_EXTRAFIELDS_DISABLED) && method_exists($lines[$i], 'fetch_optionals'))
									{
										$lines[$i]->fetch_optionals($lines[$i]->rowid);
										$array_option = $lines[$i]->array_options;
									}

									$result = $productsupplier->find_min_price_product_fournisseur($lines[$i]->fk_product, $lines[$i]->qty);
									if ($result>=0)
									{
										$tva_tx = $lines[$i]->tva_tx;

										if ($origin=="commande")
										{
											$soc=new societe($db);
											$soc->fetch($socid);
											$tva_tx=get_default_tva($soc, $mysoc, $lines[$i]->fk_product, $productsupplier->product_fourn_price_id);
										}

										if ($conf->fiscal->enabled)
										{
											$pu = $lines[$i]->subprice;
											$price_base_type = 'HT';
											if ($conf->global->PRICE_TAXES_INCLUDED)
											{
												$price_base_type = 'TTC';
												$pu = $lines[$i]->price;
											}
											$k = 1;
											$lines[$i]->price = $pu;
											$qty = $lines[$i]->qty;
											//$lines[$i]->fk_product = $idprod;
											$lines[$i]->fk_unit = $lines[$i]->fk_unit;
											if (empty($lines[$i]->fk_unit)) $lines[$i]->fk_unit = $productsupplier->fk_unit;
											$type = ($productsupplier->type?$productsupplier->type:0);
											$remise_percent = $lines[$i]->remise_percent;
											$discount = $lines[$i]->remise;

											include DOL_DOCUMENT_ROOT.'/fiscal/include/calclinesfiscal.inc.php';
											//agregamos producto al proveedor
											//include DOL_DOCUMENT_ROOT.'/purchase/function/addproductfourn.php';

											$result=$objectc->addlineadd(	$desc,$lines[$i]->subprice,
												$lines[$i]->qty,$lines[$i]->tva_tx,$lines[$i]->localtax1_tx,$lines[$i]->localtax2_tx,
												$lines[$i]->fk_product,$idprodfournprice,
												$fourn_ref,$lines[$i]->remise_percent,
												$price_base_type,$lines[$i]->price,
												$type,$tva_npr,'',$date_start,$date_end,$array_options,
												$lines[$i]->fk_unit,$lines[$i]);
											//echo '<hr>antesde '.$result.' '.$error;
											if ($result <= 0)
											{
												$error++;
												setEventMessages($objectc->error,$objectc->errors,'errors');
											}
											else
											{
												//creamos el registro adicional
												$objectdetadd->initAsSpecimen();
												$objectdetadd->fk_commande_fournisseurdet = $result;
												$objectdetadd->fk_object = $lines[$i]->id;
												$objectdetadd->object = $subelementdetreg;
												$objectdetadd->fk_fabrication = $lines[$i]->fk_fabrication;
												$objectdetadd->fk_fabricationdet = $lines[$i]->fk_fabricationdet;
												$objectdetadd->fk_projet = $lines[$i]->fk_projet;
												$objectdetadd->fk_projet_task = $lines[$i]->fk_projet_task;
												$objectdetadd->fk_jobs = $lines[$i]->fk_jobs;
												$objectdetadd->fk_jobsdet = $lines[$i]->fk_jobsdet;
												$objectdetadd->fk_structure = $lines[$i]->fk_structure;
												$objectdetadd->fk_poa = $lines[$i]->fk_poa;
												$objectdetadd->partida = $lines[$i]->partida;
												$objectdetadd->amount_ice = $lines[$i]->amount_ice;
												$objectdetadd->discount = $lines[$i]->discount;
												$objectdetadd->fk_user_create = $user->id;
												$objectdetadd->fk_user_mod = $user->id;
												$objectdetadd->datec = dol_now();
												$objectdetadd->datem = dol_now();
												$objectdetadd->tms = dol_now();
												$objectdetadd->status = 1;
												//echo '<hr>resdetadd '.
												$resdetadd = $objectdetadd->create($user);
												if ($resdetadd<=0)
												{
													$error++;
													setEventMessages($objectdetadd->error, $objectdetadd->errors,'errors');
												}
											}
											if ($conf->poa->enabled && $conf->global->PURCHASE_INTEGRATED_POA)
											{
												//actualizamos poapartidapredet
												//con los valores del contrato
												//echo '<hr>lineid '.$lines[$i]->id;
												$resadd = $objSupplierdetadd->fetch(0,$lines[$i]->id);
												//print_r($aProdppd);
												if ($resadd>0)
												{
													//echo '<hr>busca '.$objSupplierdetadd->fk_object;
													//echo '<hr>respppdy '.
													$respppd = $objPartidapredet->fetch($aProdppd[$objSupplierdetadd->fk_object]);
												}
												if ($respppd==1)
												{
													$objPartidacom->fetch(0,$objPartidapredet->fk_poa_partida_pre);
													$objPartidapredet->fk_contrat = $resppc;
													$objPartidapredet->fk_contrato = $idc;

													$objPartidapredet->quant_adj = $lines[$i]->qty;
													$objPartidapredet->amount = $lines[$i]->total_ttc;
													$objPartidapredet->fk_poa_partida_com = $objPartidacom->id;
													$objPartidapredet->fk_user_mod = $user->id;
													$objPartidapredet->datem = $now;
													//echo '<hr>respppdx '.
													$respppd = $objPartidapredet->update($user);
													if ($respppd<=0)
													{
														$error++;
														setEventMessages($objPartidapredet->error,$objPartidapredet->errors,'errors');
													}
												}
											}
											//echo '<hr>errx '.$error;
										}
										else
										{
											$result = $objectc->addline(
												$desc,
												$lines[$i]->subprice,
												$lines[$i]->qty,
												$tva_tx,
												$lines[$i]->localtax1_tx,
												$lines[$i]->localtax2_tx,
												$lines[$i]->fk_product > 0 ? $lines[$i]->fk_product : 0,
												$productsupplier->product_fourn_price_id,
												$productsupplier->ref_supplier,
												$lines[$i]->remise_percent,
												'HT',
												0,
												$lines[$i]->product_type,
												'',
												'',
												null,
												null,
												array(),
												$lines[$i]->fk_unit
											);
										}
									}
									if ($result < 0) {
										$error++;
										break;
									}
									// Defined the new fk_parent_line
									if ($result > 0 && $lines[$i]->product_type == 9) {
										$fk_parent_line = $result;
									}
								}
								$lClose = false;
								if ($nProd == $nRegapp)
								{
									$lClose = true;
									$cClose = 'T';
								}
								elseif ($nProd >0 && $nProd<$nRegapp)
								{
									$lClose = true;
									$cClose = 'P';
								}
							} else {
								setEventMessages($srcobject->error, $srcobject->errors, 'errors');
								$error ++;
							}
						} else {
							setEventMessages($objectc->error, $object->errors, 'errors');
							$error ++;
						}
					}
					else
					{
						$idc = $objectc->create($user);
						$objectadd->fk_commande_fournisseur = $id;
						$resadd = $objectadd->create($user);
						if ($resadd<=0)
						{
							setEventMessages($objectadd->error,$objectadd->errors,'errors');
							$error=995;
						}
						if ($idc < 0)
						{
							$error=996;
							setEventMessages($objectc->error, $objectc->errors, 'errors');
						}
					}
				}
				//calculamos el total
				if (!$error)
				{
					echo '<hr>recuperaerr '.$error = update_total_commande($idc);

				}
				if (!$error)
				{
					if ($conf->poa->enabled && $conf->global->PURCHASE_INTEGRATED_POA)
					{
						if ($object->fk_poa_prev > 0)
						{
					//print_r($aPartidapre);
					//echo '<hr>ids '.
							$idsPartidapre = implode(',',$aPartidapre);
					//actualizamos el comprometido por el total por partida
							$filterdet = " AND t.fk_contrato = ".$idc;
							$filterdet.= " AND t.fk_poa_partida_pre IN (".$idsPartidapre.")";
					//echo '<hr>resppdup '.
							$resppdup = $objPartidapredet->fetchAll('','',0,0,array('statut'=>1),'AND',$filterdet);
					//echo '<pre>';
					//print_r($objPartidapredet->lines);
					//echo '</pre>';
							$aLine = array();
							if ($resppdup>0)
							{
								foreach ($objPartidapredet->lines AS $l => $linedet)
								{
									$aLine[$linedet->fk_poa_partida_com]['amount']+=$linedet->amount;
									$aLine[$linedet->fk_poa_partida_com]['fk_contrat']=$linedet->fk_contrat;
									$aLine[$linedet->fk_poa_partida_com]['fk_contrato']=$linedet->fk_contrato;
								}
							}
							foreach ((array) $aLine AS $fk_partida_com => $data)
							{
								$rescom = $objPartidacom->fetch($fk_partida_com);
								if ($rescom==1)
								{
									$objPartidacom->amount = $data['amount'];
									$objPartidacom->fk_contrat = $data['fk_contrat'];
									$objPartidacom->fk_contrato = $data['fk_contrato'];
									$objPartidacom->fk_user_mod = $user->id;
									$objPartidacom->datem = dol_now();
							//print_r($objPartidacom);
							//echo '<hr>rescomup '.
									$rescomup = $objPartidacom->update($user);
									if ($rescomup<=0)
									{
										$error=997;
										setEventMessages($objPartidacom->error,$objPartidacom->errors,'errors');
									}

								}
							}
						}
					}
				}
				//echo '<hr>antesdefin '.$error;
				if (!$error)
				{
					//validamos el registro
					echo '<hr>buscamos antes de actualizar '.$idc;
					$objectctmp = new Fournisseurcommandeext($db);
					$objectctmp->fetch($idc);
					$objectctmp->date_commande=dol_now();
					//echo '<hr>result '.
					$result = $objectctmp->valid($user);
					if ($result <=0)
					{
						$error=998;
						setEventMessages($objectctmp->error,$objectctmp->errors,'errors');
					}
					if (! $error)
					{
						$idwarehouse = 0;
						//($action=='confirm_approve2'?1:0)
						$result	= $objectctmp->approve($user, $idwarehouse, 0);
						if ($result <= 0)
						{
							$error=999;
							setEventMessages($object->error, $object->errors, 'errors');
						}
					}
				}
				//verificamos si aceptamos la toalidad o no de supplier
				if ($lClose)
				{
					//El requirimiento se cambiara a aceptado en su totalidad
					if ($cClose == 'T')
						$note = $langs->trans('Aceptación y aprobación por la totalidad de su oferta según pedido Nro. ').$objectctmp->ref;
					if ($cClose == 'P')
						$note = $langs->trans('Aceptación y aprobación parcial de su oferta según pedido Nro. ').$objectctmp->ref;

					//echo '<hr>rescloture '.
					$res = $objSupplier->cloture($user, 2, $note);
					if ($res <=0)
					{
						$error=1001;
						setEventMessages($objSupplier->error,$objSupplier->errors,'errors');
					}
				}
			}
			if (!$error)
			{
				//cambiamos de estado_process a 3
				if ($object->status_process == 2)
				{
					$object->status_process = 3;
					$res = $object->update($user);
					if ($res <=0)
					{
						$error++;
						setEventMessages($object->error,$object->errors,'errors');
					}
				}
			}
			//echo '<hr>errfinal '.$error;
			//exit;
			//para finalizar
			if ($error)
			{
				$langs->load("errors");
				$db->rollback();
				$action='create';
				$_GET['socid']=$_POST['socid'];
			}
			else
			{
				$db->commit();
				header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
				exit;
			}
		}
	}

	// confirm enable supplier
	if ($action == 'confirm_enablecomp' && $confirm == 'yes' && $user->rights->purchase->sup->compena)
	{
		if ($object->id)
		{
			$object->status_process = 2;
			$res = $object->update($user);
			if ($res <=0)
			{
				$error++;
				setEventMessages($object->error,$object->errors,'errors');
			}
		}
		if (!$error) {
			setEventMessages($langs->trans('Price comparison enabled'),null,'mesgs');
			header('Location: ' .$_SERVER['PHP_SELF'] . '?id='.$object->id);
			exit();
		} else {
			$langs->load("errors");
			setEventMessages($langs->trans($object->error), null, 'errors');
		}
		$action = '';
	}

	//confirm_add
	if ($action == 'confirm_add' && $confirm == 'yes')
	{
		$_POST = unserialize($_SESSION['aPostsup']);
		$object->fetch_lines();
		$origin = 'purchaserequest';
		$originid = $object->id;

		$objSupplier = new Supplierproposalext($db);
		$objSupplieradd = new Supplierproposaladd($db);
		$objSupplierdetadd = new Supplierproposaldetadd($db);
		$db->begin();
		foreach ((array) $itemTransf AS $j => $data)
		{
			$socid = $data['fk_soc'];

			$objSupplier->socid = $socid;
			$objSupplier->fetch_thirdparty();

			$date_delivery = dol_mktime(12, 0, 0, GETPOST('liv_month'), GETPOST('liv_day'), GETPOST('liv_year'));

			if ($socid < 1) {
				setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Supplier")), null, 'errors');
				$action = 'create';
				$error++;
			}
			if (! $error)
			{

				// Si on a selectionne une demande a copier, on realise la copie

				$objSupplier->ref = GETPOST('ref');
				$objSupplier->date_livraison = $date_delivery;
				$objSupplier->demand_reason_id = GETPOST('demand_reason_id');
				$objSupplier->shipping_method_id = GETPOST('shipping_method_id', 'int');
				$objSupplier->cond_reglement_id = GETPOST('cond_reglement_id');
				$objSupplier->mode_reglement_id = GETPOST('mode_reglement_id');
				$objSupplier->fk_account = GETPOST('fk_account', 'int');
				$objSupplier->fk_project = GETPOST('projectid');
				$objSupplier->modelpdf = GETPOST('model');
				$objSupplier->author = $user->id; // deprecated
				$objSupplier->note = GETPOST('note');

				$objSupplier->origin = 'purchase_request';
				$objSupplier->origin_id = $object->id;
				$objSupplier->status = 0;
				// Multicurrency
				if (!empty($conf->multicurrency->enabled))
				{
					$objSupplier->multicurrency_code = GETPOST('multicurrency_code', 'alpha');
				}

				// Possibility to add external linked objects with hooks
				$objSupplier->linked_objects [$objSupplier->origin] = $objSupplier->origin_id;
				$other_linked_objects = GETPOST('other_linked_objects', 'array');
				if (! empty($other_linked_objects)) {
					$objSupplier->linked_objects = array_merge($objSupplier->linked_objects, $other_linked_objects);
				}

				for($i = 1; $i <= $conf->global->PRODUCT_SHOW_WHEN_CREATE; $i ++)
				{
				///	if ($_POST['idprod' . $i]) {
				//		$xid = 'idprod' . $i;
				//		$xqty = 'qty' . $i;
				//		$xremise = 'remise' . $i;
				//		$objSupplier->add_product($_POST[$xid], $_POST[$xqty], $_POST[$xremise]);
				//	}
				}

				// Fill array 'array_options' with data from add form
				$ret = $extrafields->setOptionalsFromPost($extralabels, $object);
				if ($ret < 0) {
					$error++;
					$action = 'create';
				}

				if (! $error)
				{
					if ($origin && $originid)
					{
						$element = 'purchase_request';
						$subelement = 'purchase_request';
						$subelementdet = 'purchaserequestdetext';
						$objSupplier->origin = $origin;
						$objSupplier->origin_id = $originid;

						// Possibility to add external linked objects with hooks
						$objSupplier->linked_objects [$objSupplier->origin] = $objSupplier->origin_id;
						if (is_array($_POST['other_linked_objects']) && ! empty($_POST['other_linked_objects'])) {
							$objSupplier->linked_objects = array_merge($objSupplier->linked_objects, $_POST['other_linked_objects']);
						}

						$idr = $objSupplier->create($user);

						//vamos a crear su adicional a supplier_propossal_add
						$objSupplieradd->fk_supplier_proposal = $idr;
						$objSupplieradd->fk_purchase_request = $id;
						$objSupplieradd->code_facture = GETPOST('code_facture','alpha');
						$objSupplieradd->code_type_purchase = GETPOST('code_type_purchase','alpha');
						$objSupplieradd->fk_user_create = $user->id;
						$objSupplieradd->fk_user_mod = $user->id;
						$objSupplieradd->datec = dol_now();
						$objSupplieradd->datem = dol_now();
						$objSupplieradd->tms = dol_now();
						$objSupplieradd->status = 1;
						$res = $objSupplieradd->create($user);
						if ($res <=0)
						{
							$error++;
							setEventMessages($objSupplieradd->error,$objSupplieradd->errors,'errors');
						}
						if ($idr > 0 && !$error)
						{
							if ($element == 'purchase_request')
							{
								$element = 'purchase';
								$subelement = 'purchaserequestext';
								$subelementdet = 'purchaserequestdetext';
								$subelementdetreg = 'purchaserequestdet';
							}
							dol_include_once('/' . $element . '/class/' . $subelement . '.class.php');

							$classname = ucfirst($subelement);
							$srcobject = new $classname($db);

							dol_syslog("Try to find source object origin=" . $objSupplier->origin . " originid=" . $objSupplier->origin_id . " to add lines");
							$result = $srcobject->fetch($objSupplier->origin_id);

							if ($result > 0)
							{
								$lines = $srcobject->lines;
								if (empty($lines) && method_exists($srcobject, 'fetch_lines'))
								{
									$srcobject->fetch_lines();
									$lines = $srcobject->lines;
								}
								$fk_parent_line=0;
								$num=count($lines);
								for ($i=0;$i<$num;$i++)
								{
									$label=(! empty($lines[$i]->label)?$lines[$i]->label:'');
									$desc=(! empty($lines[$i]->desc)?$lines[$i]->desc:$lines[$i]->libelle);

									// Positive line
									$product_type = ($lines[$i]->product_type ? $lines[$i]->product_type : 0);
									if ($product_type <0) $product_type=0;
									// Reset fk_parent_line for no child products and special product
									if (($lines[$i]->product_type != 9 && empty($lines[$i]->fk_parent_line)) || $lines[$i]->product_type == 9) {
										$fk_parent_line = 0;
									}

									// Extrafields
									if (empty($conf->global->MAIN_EXTRAFIELDS_DISABLED) && method_exists($lines[$i], 'fetch_optionals')) {
										$lines[$i]->fetch_optionals($lines[$i]->rowid);
										$array_options = $lines[$i]->array_options;
									}
									$price_type = 'HT';
									if ($conf->globlal->PRICE_TAXES_INCLUDED)
										$price_type = 'TTC';
									$lines[$i]->remise = 0;
									$lines[$i]->remise_percent = 0;

									$result = $objSupplier->addlineadd($desc, $lines[$i]->subprice, $lines[$i]->qty, $lines[$i]->tva_tx, $lines[$i]->localtax1_tx, $lines[$i]->localtax2_tx, $lines[$i]->fk_product, $lines[$i]->remise_percent, $price_type, 0, $lines[$i]->info_bits, $product_type, $lines[$i]->rang, $lines[$i]->special_code, $fk_parent_line, $lines[$i]->fk_fournprice, $lines[$i]->pa_ht, $label, $array_options,'',$lines[$i]);
									//$result = $objSupplier->addlineadd($desc, $pu_ht, $qty, $txtva, $txlocaltax1=0, $txlocaltax2=0, $fk_product=0, $remise_percent=0, $price_base_type='HT', $pu_ttc=0, $info_bits=0, $type=0, $rang=-1, $special_code=0, $fk_parent_line=0, $fk_fournprice=0, $pa_ht=0, $label='',$array_option=0, $ref_fourn='',$lines)
									//echo '<hr>lineidsss '.$result;
									if ($result > 0) {
										$lineid = $result;
									} else {
										$lineid = 0;
										$error++;

									}
									if (!$error)
									{
										$objSupplierdetadd->fk_supplier_proposaldet = $result;
										$objSupplierdetadd->fk_object = $lines[$i]->rowid;
										$objSupplierdetadd->object = $subelementdetreg;
										$objSupplierdetadd->fk_fabrication = $lines[$i]->fk_fabrication+0;
										$objSupplierdetadd->fk_fabricationdet = $lines[$i]->fk_fabricationdet+0;
										$objSupplierdetadd->fk_projet = $lines[$i]->fk_projet+0;
										$objSupplierdetadd->fk_projet_task = $lines[$i]->fk_projet_task+0;
										$objSupplierdetadd->fk_jobs = $lines[$i]->fk_jobs+0;
										$objSupplierdetadd->fk_jobsdet = $lines[$i]->fk_jobsdet+0;
										$objSupplierdetadd->fk_structure = $lines[$i]->fk_structure+0;
										$objSupplierdetadd->fk_poa = $lines[$i]->fk_poa+0;
										$objSupplierdetadd->partida = $lines[$i]->partida;
										$objSupplierdetadd->amount_ice = $lines[$i]->amount_ice+0;
										$objSupplierdetadd->discount = $lines[$i]->discount+0;
										$objSupplierdetadd->fk_user_create = $user->id;
										$objSupplierdetadd->fk_user_mod = $user->id;
										$objSupplierdetadd->datec = dol_now();
										$objSupplierdetadd->datem = dol_now();
										$objSupplierdetadd->tms = dol_now();
										$objSupplierdetadd->status = 1;
										$res = $objSupplierdetadd->create($user);
										if ($res <=0)
										{
											$error++;
											setEventMessages($objSupplierdetadd->error,$objSupplierdetadd->errors,'errors');
										}
									}
									// Defined the new fk_parent_line
									if ($result > 0 && $lines[$i]->product_type == 9) {
										$fk_parent_line = $result;
									}
								}

								// Hooks
								$parameters = array('objFrom' => $srcobject);
								$reshook = $hookmanager->executeHooks('createFrom', $parameters, $object, $action);
								// Note that $action and $object may have been
								                                                                               // modified by hook
								if ($reshook < 0)
									$error ++;
							} else {
								setEventMessages($srcobject->error, $srcobject->errors, 'errors');
								$error ++;
							}
						} else {
							setEventMessages($objSupplier->error, $objSupplier->errors, 'errors');
							$error ++;
						}
					}
					// Standard creation
					else
					{
						$id = $objSupplier->create($user);
					}
				}
			}
		}
		if (!$error)
		{
			unset($_SESSION['invitSupplier']);
			$db->commit();
			header('Location: ' . $_SERVER["PHP_SELF"] . '?id=' . $id);
			exit();
		}
		else
		{
			setEventMessages($objSupplier->error, $objSupplier->errors, 'errors');
			$db->rollback();
			$action='create';
		}
	}


	//transfer session
	if ($invite == $langs->trans('Invite') && $action == 'add')
	{
		$action = 'valadd';
	}
	if ($action == "add" && ! $cancel)
	{
		$fk_soc = GETPOST('socid');
		if (! GETPOST("socid"))
		{
			setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Company")), 'errors');
			$error++;
			$action='create';
		}
		else
		{
			if (count(array_keys($itemTransf)) > 0) $idreg=max(array_keys($itemTransf)) + 1;
			else $idreg=1;
			if ($fk_soc>0)
			{
				$objSociete->fetch($fk_soc);
				//verificamos que sea un producto unico
				foreach ((array) $itemTransf AS $j => $data)
				{
					if ($fk_soc == $data['fk_soc'])
						$idreg = $j;
				}
				$itemTransf[$idreg]=array('id'=>$idreg, 'fk_soc'=>$fk_soc,'label'=> $objSociete->nom);
				$_SESSION['invitSupplier']=json_encode($itemTransf);
				header("Location: ".$_SERVER['PHP_SELF']."?id=".$id."&action=create");
				exit;
			}
			else
			{
				setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Company")), 'errors');
				$action = 'create';
			}
		}
	}
	if ($action == 'delitem')
	{
		if (! empty($itemTransf[$idreg])) unset($itemTransf[$idreg]);
		if (count($itemTransf) > 0) $_SESSION['invitSupplier']=json_encode($itemTransf);
		else unset($_SESSION['invitSupplier']);
		header("Location: ".$_SERVER['PHP_SELF']."?id=".$id."&action=create");
		exit;
	}
	// action delitem
	if ($action == 'clean')
	{
		unset($_SESSION['invitSupplier']);
		header("Location: ".$_SERVER['PHP_SELF']."?id=".$id."&action=create");
		exit;
	}

}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/
$morejs = array('/purchase/js/purchase.js');
$morecss = array('/purchase/css/style.css','/includes/jquery/plugins/datatables/media/css/dataTables.bootstrap.css','/includes/jquery/plugins/datatables/media/css/jquery.dataTables.css',);
llxHeader('',$title,'','','','',$morejs,$morecss,0,0);

//llxHeader('',$langs->trans('Purchaserequest'),'');

$form=new Formv($db);
$getutil = new getUtil($db);

if ($conf->monprojet->enabled)
{
	$formproject = new FormProjetsext($db);
}


// Put here content of your page

// Example : Adding jquery code
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	function init_myfunc()
	{
		jQuery("#myid").removeAttr(\'disabled\');
		jQuery("#myid").attr(\'disabled\',\'disabled\');
	}
	init_myfunc();
	jQuery("#mybutton").click(function() {
		init_myfunc();
	});
});
</script>';


// Part to create
if ($action == 'create' || $action == 'valadd')
{
	if ($action == 'valadd') {
		$aPost = $_POST;
		$_SESSION['aPostsup'] = serialize($aPost);
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $id, $langs->trans('Invite suppliers'), $langs->trans('ConfirmInvite suppliers selected'), 'confirm_add', '', 1, 2);
		print $formconfirm;
	}

	//print load_fiche_titre($langs->trans("Purchaserequest"));
	$head=purchase_request_prepare_head($object);
	dol_fiche_head($head, 'supplier', $langs->trans("Purchaserequest"),0,'purchaserequest');

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';

	print '<table class="border centpercent">'."\n";
	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans("Company"),$_SERVER['PHP_SELF'].'?id='.$id, "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Action"),$_SERVER['PHP_SELF'].'?id='.$id, "","","","",$sortfield,$sortorder);
	print '</tr>';

	if ($action == 'create')
	{
		print "<tr $bc[$var]>";
		print '<td width="80%">';
		$i = 1;
		print $form->select_company('','socid',$filter = '',1,	$showtype = 0,$forcecombo = 0,array(),0,'minwidth100','');
		print '<input type="text" style="border:none;" id="labelproduct" name="labelproduct" value="" readonly>';
		print '</td>';

		print '<td width="5%" align="right">';
		print '<center><input type="submit" class="button" value="'.$langs->trans('Add').'"></center>';
		print '</td>';
		print '</tr>';
	}
	//listamos los ya grabados
	foreach ((array) $itemTransf AS $j => $data)
	{
		$var = !$var;
		print "<tr $bc[$var]>";
		print '<td width="80%">';
		$i = 1;
		print $data['label'];
		print '</td>';
		print '<td width="5%" align="right">';
		print '<a href= "'.$_SERVER['PHP_SELF'].'?id='.$id.'&idreg='.$j.'&action=delitem">'.img_picto('','delete').'</a>';
		print '</td>';
		print '</tr>';

	}

	print '</table>'."\n";
	dol_fiche_end();

	if (count($itemTransf)>0)
	{
		dol_fiche_head();
		print '<table class="border" width="100%">';

		// Terms of payment
		print '<tr><td class="nowrap">' . $langs->trans('PaymentConditionsShort') . '</td><td colspan="2">';
		$form->select_conditions_paiements(GETPOST('cond_reglement_id') > 0 ? GETPOST('cond_reglement_id') : $soc->cond_reglement_id, 'cond_reglement_id', -1, 1);
		print '</td></tr>';

	// Mode of payment
		print '<tr><td>' . $langs->trans('PaymentMode') . '</td><td colspan="2">';
		$form->select_types_paiements(GETPOST('mode_reglement_id') > 0 ? GETPOST('mode_reglement_id') : $soc->mode_reglement_id, 'mode_reglement_id');
		print '</td></tr>';

    // Bank Account
		if (! empty($conf->global->BANK_ASK_PAYMENT_BANK_DURING_PROPOSAL) && ! empty($conf->banque->enabled)) {
			print '<tr><td>' . $langs->trans('BankAccount') . '</td><td colspan="2">';
			$form->select_comptes(GETPOST('fk_account')>0 ? GETPOST('fk_account','int') : $fk_account, 'fk_account', 0, '', 1);
			print '</td></tr>';
		}

    // Shipping Method
		if (! empty($conf->expedition->enabled)) {
			print '<tr><td>' . $langs->trans('SendingMethod') . '</td><td colspan="2">';
			print $form->selectShippingMethod(GETPOST('shipping_method_id') > 0 ? GETPOST('shipping_method_id', 'int') : $shipping_method_id, 'shipping_method_id', '', 1);
			print '</td></tr>';
		}

	// Delivery date (or manufacturing)
		print '<tr><td>' . $langs->trans("DeliveryDate") . '</td>';
		print '<td colspan="2">';
		$datedelivery = dol_mktime(0, 0, 0, GETPOST('liv_month'), GETPOST('liv_day'), GETPOST('liv_year'));
		if ($conf->global->DATE_LIVRAISON_WEEK_DELAY != "") {
			$tmpdte = time() + ((7 * $conf->global->DATE_LIVRAISON_WEEK_DELAY) * 24 * 60 * 60);
			$syear = date("Y", $tmpdte);
			$smonth = date("m", $tmpdte);
			$sday = date("d", $tmpdte);
			$form->select_date($syear."-".$smonth."-".$sday, 'liv_', '', '', '', "addask");
		} else {
			$form->select_date($datedelivery ? $datedelivery : -1, 'liv_', '', '', '', "addask", 1, 1);
		}
		print '</td></tr>';


	// Model
		print '<tr>';
		print '<td>' . $langs->trans("DefaultModel") . '</td>';
		print '<td colspan="2">';
		$liste = ModelePDFSupplierProposal::liste_modeles($db);
		print $form->selectarray('model', $liste, ($conf->global->SUPPLIER_PROPOSAL_ADDON_PDF_ODT_DEFAULT ? $conf->global->SUPPLIER_PROPOSAL_ADDON_PDF_ODT_DEFAULT : $conf->global->SUPPLIER_PROPOSAL_ADDON_PDF));
		print "</td></tr>";

	// Project
		if (! empty($conf->projet->enabled) && $socid > 0) {

			$formproject = new FormProjets($db);

			$projectid = 0;
			if ($origin == 'project')
				$projectid = ($originid ? $originid : 0);

			print '<tr>';
			print '<td class="tdtop">' . $langs->trans("Project") . '</td><td colspan="2">';

			$numprojet = $formproject->select_projects($soc->id, $projectid);
			if ($numprojet == 0) {
				$langs->load("projects");
				print ' &nbsp; <a href="../projet/card.php?socid=' . $soc->id . '&action=create">' . $langs->trans("AddProject") . '</a>';
			}
			print '</td>';
			print '</tr>';
		}

	// Multicurrency
		if (! empty($conf->multicurrency->enabled))
		{
			print '<tr>';
			print '<td>'.fieldLabel('Currency','multicurrency_code').'</td>';
			print '<td colspan="3" class="maxwidthonsmartphone">';
			print $form->selectMultiCurrency($currency_code, 'multicurrency_code');
			print '</td></tr>';
		}

	// Other attributes
		$parameters = array('colspan' => ' colspan="3"');
		$reshook = $hookmanager->executeHooks('formObjectOptions', $parameters, $object, $action);
	// Note that $action and $object may have been modified
	                                                                                           // by
	                                                                                           // hook
		if (empty($reshook) && ! empty($extrafields->attribute_label)) {
			print $object->showOptionals($extrafields, 'edit');
		}
		print '</table>';
		dol_fiche_end();
	}

	print '<div class="center">';
	print '<input type="submit" class="butAction" name="invite" value="'.$langs->trans("Invite").'">';
	print '<a class="butAction" href= "'.$_SERVER['PHP_SELF'].'?id='.$id.'&action=clean">'.$langs->trans('Clean').'</a>';
	print '</div>';

	print '</form>';
}



// Part to show record
//if ($id && (empty($action) || $action == 'createval' || $action == 'view' || $action == 'delete' || $action == 'editline' || $action == 'validate' || $action == 'refresh'))
if ($id>0 && $action != 'create')
{
	//print load_fiche_titre($langs->trans("Purchaserequest"));
	$head=purchase_request_prepare_head($object);
	dol_fiche_head($head, 'supplier', $langs->trans("Purchaserequest"),0,'purchaserequest');

	$res = $getutil->get_element_element($object->id,$object->element,'source');
	$lInvit = true;


	// Confirmation to delete line
	if ($action == 'ask_deleteline')
	{
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id.'&lineid='.$lineid, $langs->trans('DeleteProductLine'), $langs->trans('ConfirmDeleteProductLine'), 'confirm_deleteline', '', 0, 1);
		print $formconfirm;
	}

	if ($action == 'enablecomp') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('Enable price comparison'), $langs->trans('ConfirmEnable price comparison'), 'confirm_enablecomp', '', 0, 1);
		print $formconfirm;
	}

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('DeleteMyOjbect'), $langs->trans('ConfirmDeleteMyObject'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}
	//aprobar comparación precios
	if ($action == 'appcomp') {
		$aPurchaselinesoc = GETPOST('purchaselinesoc');
		$aPurchaseproduct = $aPurchaselinesoc[$object->id];
		$aCompany = array();
		foreach ((array) $aPurchaseproduct AS $j => $value)
		{
			if ($value>0)
				$aCompany[$value] = $value;
			else
				$aNoselect[$j]=$j;
		}
		$nLine = count($aCompany);
		$nNoline = count($aNoselect);
		if ($nNoline>0)
			$notext = $nNoline.' '.$langs->trans('productos no adjudicados');
		$aPost[$id] = $_POST;
		$_SESSION['aSupplierapp'] = serialize($aPost);
		require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
		$formproduct=new FormProduct($db);

		$formquestion=array(
					//'text' => $langs->trans("ConfirmClone"),
					//array('type' => 'checkbox', 'name' => 'clone_content',   'label' => $langs->trans("CloneMainAttributes"),   'value' => 1),
					//array('type' => 'checkbox', 'name' => 'update_prices',   'label' => $langs->trans("PuttingPricesUpToDate"),   'value' => 1),
			array('type' => 'other', 'name' => 'idcountcompany',   'label' => $langs->trans("Se crearán pedidos a Proveedor en base a la selección"),   'value' => $nLine.' '.$langs->trans('Company')),
			array('type' => 'other', 'name' => 'idnoproduct',   'label' => $langs->trans("Productos que no seran adjudicados"),   'value' => $nNoline),
			array('type' => 'other', 'name' => 'code_type_purchase',   'label' => $langs->trans("Purchasedestination"),   'value' =>$form->load_type_purchase('code_type_purchase',(GETPOST('code_type_purchase')?GETPOST('code_type_purchase'):'STOCK'),0, 'code', false)),


		);
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('Approvepricecomparision'), $langs->trans('ConfirmApprovepricecomparison'), 'confirm_appcomp', $formquestion, 0, 2);
		print $formconfirm;
	}

	if ($action == 'validate') {
		$aPost[$id] = $_POST;
		$_SESSION['aPurchaseValidate'] = serialize($aPost);
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('Validatepurchaserequest'), $langs->trans('ConfirmValidatepurchaserequest'), 'confirm_validate', '', 0, 1);
		print $formconfirm;
	}
	if ($action == 'valplan' && $conf->poa->enabled) {
		$aPost[$id] = $_POST;
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id.'&idr='.GETPOST('idr'), $langs->trans('Validatepreventiveplanification'), $langs->trans('ConfirmValidatepreventiveplanification'), 'confirm_valplan', '', 0, 1);
		print $formconfirm;
	}
	if ($action == 'valpres' && $conf->poa->enabled) {
		$aPost[$id] = $_POST;
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id.'&idr='.GETPOST('idr'), $langs->trans('Validatepreventivebudget'), $langs->trans('ConfirmValidatepreventivebudget'), 'confirm_valpres', '', 0, 1);
		print $formconfirm;
	}

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	print '<tr><td>'.$langs->trans("Fieldref").'</td><td>'.$object->ref.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldref_ext").'</td><td>'.$object->ref_ext.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldref_int").'</td><td>'.$object->ref_int.'</td></tr>';
	if ($monprojet->enabled)
	{
		print '<tr><td>'.$langs->trans("Fieldfk_projet").'</td><td>';
		$projectstatic->fetch($object->fk_projet);
		$projectstatic->getNomUrl(1);
		print '</td></tr>';
	}
	print '<tr><td>'.$langs->trans("Fieldarea_request").'</td><td>';
	if ($conf->orgman->enabled)
	{
		require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentext.class.php';
		$departament = new Pdepartamentext($db);
		$departament->fetch($object->fk_departament);
		print $departament->getNomUrl(1);
	}
	else
	{
		$getutil->fetch_departament($object->fk_departament);
		print $getutil->ref;
	}
	print '</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldfk_user_author").'</td><td>';
	$objuser->fetch($object->fk_user_author);
	print $objuser->getNomUrl(1);
	print '</td></tr>';
	if ($conf->global->PURCHASE_INTEGRATED_POA)
	{
		if ($conf->poa->enabled)
		{
			if ($object->fk_poa_prev > 0)
			{
				$objPoaprev = new Poaprevext($db);
				$objPoaprev->fetch($object->fk_poa_prev);
				print '<tr><td>'.$langs->trans("Preventive").'</td><td>';
				print $objPoaprev->getNomUrl();
				print '</td></tr>';

			}
		}
	}

	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_modif").'</td><td>$object->fk_user_modif</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_valid").'</td><td>$object->fk_user_valid</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_cloture").'</td><td>$object->fk_user_cloture</td></tr>';
	if ($user->rights->purchase->req->viewnp)
	{
		print '<tr><td>'.$langs->trans("Fieldnote_private").'</td><td>'.$object->note_private.'</td></tr>';
	}
	print '<tr><td>'.$langs->trans("Fieldnote_public").($conf->global->PURCHASE_INTEGRATED_POA?'/'.$langs->trans('Nombre Preventivo'):'').'</td><td>'.$object->note_public.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_shipping_method").'</td><td>$object->fk_shipping_method</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldimport_key").'</td><td>$object->import_key</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldextraparams").'</td><td>$object->extraparams</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldstatus").'</td><td>';
	print $object->getLibStatut(1);
	print '</td></tr>';

	print '</table>';

	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);
	 // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($object->status == 1 && $object->status_process == 2)
		{
			if ($user->rights->purchase->req->crearsp && $lInvit)
			{
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=create">'.$langs->trans("Invite suppliers").'</a></div>'."\n";
			}
		}
	}
	print '</div>'."\n";


	if(count($getutil->lines)>0)
	{
		print load_fiche_titre($langs->trans("Supplierproposal"));

		dol_fiche_head();
		print '<table class="tagtable liste'.($moreforfilter?" listwithfilterbefore":"").'">';
		print '<tr class="liste_titre">';
		print_liste_field_titre($langs->trans('Ref'),$_SERVER["PHP_SELF"],'','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Company'),$_SERVER["PHP_SELF"],'','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Datevalid'),$_SERVER["PHP_SELF"],'','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Uservalid'),$_SERVER["PHP_SELF"],'','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Status'),$_SERVER["PHP_SELF"],'','',$param,'',$sortfield,$sortorder);
		print '</tr>';
		$lines = $getutil->lines;
		foreach ($lines AS $j => $linef)
		{
			$idr = $linef->fk_target;
			$element = $linef->targettype;
			if (trim($element) == 'supplier_proposal')
			{
				$objsrc = new Supplierproposalext($db);
				$objsrc->fetch($idr);
				$objSupplieradd = new Supplierproposaladd($db);
				$objSupplieradd->fetch(0,$idr);

				$objuser->fetch($objsrc->user_valid_id);
				print '<tr>';
				print '<td>';

				print $objsrc->getNomUrladd();
				print '</td>';
				print '<td>';
				//societe
				$objSociete->fetch($objsrc->socid);
				print $objSociete->getNomUrl(1);
				print '</td>';
				print '<td>';
				print dol_print_date($objsrc->datev,'dayhour');
				print '</td>';
				print '<td>';
				if ($objuser->id == $objsrc->user_valid_id)
					print $objuser->getNomUrl(1);
				print '</td>';
				print '<td>';
				if ($objSupplieradd->fk_supplier_proposal == $idr)
					print $objSupplieradd->getLibStatut(6);
				print '</td>';
				print '</tr>';
			}
		}
		print '</table>';
		dol_fiche_end();

	// Buttons
		print '<div class="tabsAction">'."\n";
		if ($object->status == 1 && $object->status_process == 1)
		{
			$now = dol_now();
			$lReg = false;
			//$dif = $now - $object->date_delivery;
			//echo $dif .' '.dol_print_date($now,'dayhour').' '.dol_print_date($object->date_delivery,'dayhour');
			if ($now > $object->date_delivery) $lReg = true;
			if ($user->rights->purchase->sup->compena && $lReg)
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=enablecomp">'.$langs->trans("Enable price comparison").'</a></div>'."\n";
		}
		print '</div>'."\n";

	}

	//elaborar cuadro comparativo
	if($object->status_process == 2 && count($getutil->lines)>0)
	{
		$objtmp = new Supplierproposaldetadd($db);
		$object->fetch_lines();
		$linesprod = $object->lines;
		$lines = $getutil->lines;
		print load_fiche_titre($langs->trans("Pricecomparison"));

		dol_fiche_head();
		if ($object->status == 1 && $object->status_process == 2 && $user->rights->purchase->sup->valcomp)
		{
			print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
			print '<input type="hidden" name="action" value="appcomp">';
			print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
			print '<input type="hidden" name="id" value="'.$object->id.'">';
		}
		print '<table class="tagtable liste'.($moreforfilter?" listwithfilterbefore":"").'">';
		print '<tr class="liste_titre">';
		print_liste_field_titre($langs->trans('Ref'),$_SERVER["PHP_SELF"],'','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Product'),$_SERVER["PHP_SELF"],'','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Unit'),$_SERVER["PHP_SELF"],'','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Qty'),$_SERVER["PHP_SELF"],'','',$param,'align="right"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Price'),$_SERVER["PHP_SELF"],'','',$param,' align="right"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Total'),$_SERVER["PHP_SELF"],'','',$param,' align="right"',$sortfield,$sortorder);
		$aSuppliersoc = array();
		foreach ($lines AS $j => $linef)
		{
			$idr = $linef->fk_target;
			$element = $linef->targettype;
			if (trim($element) == 'supplier_proposal')
			{
				$objsrc = new Supplierproposalext($db);
				$objsrc->fetch($idr);
				$objsrc->fetch_linesadd();
				$linesprodsoc = $objsrc->lines;
				$objSupplieradd = new Supplierproposaladd($db);
				$objSupplieradd->fetch(0,$idr);
				$objSociete->fetch($objsrc->socid);
				$aSoc[$objsrc->socid] = $idr;
				print_liste_field_titre($objSociete->getNomUrl(1),$_SERVER["PHP_SELF"],'','',$param,' align="right"',$sortfield,$sortorder);
				//armamos en un array la lista de productos por proponente
				foreach((array) $linesprodsoc AS $k => $obj)
				{
					//buscamos en la tabla adicional
					$res = $objtmp->fetch(0,$obj->id);
					if ($res)
					{
						$aSuppliersoc[$objsrc->socid][$objtmp->fk_object]['fk_product'] = $obj->fk_product;
						$aSuppliersoc[$objsrc->socid][$objtmp->fk_object]['fk_supplier_proposal'] = $obj->fk_supplier_proposal;
						$aSuppliersoc[$objsrc->socid][$objtmp->fk_object]['qty'] = $obj->qty;
						$aSuppliersoc[$objsrc->socid][$objtmp->fk_object]['price'] = $obj->price;
						$aSuppliersoc[$objsrc->socid][$objtmp->fk_object]['subprice'] = $obj->subprice;
						$aSuppliersoc[$objsrc->socid][$objtmp->fk_object]['total_ttc'] = $obj->total_ttc;
						$aSuppliersoc[$objsrc->socid][$objtmp->fk_object]['total_ht'] = $obj->total_ht;
					}

				}
			}
		}
		print_liste_field_titre($langs->trans('Lowest price'),$_SERVER["PHP_SELF"],'','',$param,' align="right"',$sortfield,$sortorder);

		//armamos las empresas que presentaron propuesta
		print '</tr>';
		//armamos los productos y la lista de precios por proponente
		$lowprice = 0;
		$selSoc = 0;
		$aSelprod = array();
		$aSelsupplier = array();
		$var = true;
		$aPurchaselinesoc = GETPOST('purchaselinesoc');
		if (isset($_POST['purchaselinesoc']) || isset($_GET['purchaselinesoc']))
		{
			$aPurchaseproduct = $aPurchaselinesoc[$object->id];
		}
		foreach ($linesprod AS $j => $line)
		{
			$var = !$var;
			print "<tr $bc[$var]>";
			print '<td>'.$line->refline.'</td>';
			print '<td>'.($line->fk_product?$line->label:$line->description).'</td>';
			print '<td>'.$langs->trans($line->unit).'</td>';
			print '<td align="right">'.$line->qty.'</td>';
			print '<td align="right">'.price($line->price).'</td>';
			$total_ttc = price2num($line->qty * $line->price,'MT');
			print '<td align="right">'.price($total_ttc).'</td>';
			$lowprice = 0;
			foreach ((array) $aSoc AS $fk_soc => $fk_supplierprop)
			{
				if (empty($lowprice))
				{
					if ($aSuppliersoc[$fk_soc][$line->id]['total_ttc']>0)
					{
						$lowprice = $aSuppliersoc[$fk_soc][$line->id]['total_ttc'];
						$selSoc = $fk_soc;
						$aSelprod[$line->id] = $selSoc;
						$aSelsupplier[$line->id] = $aSuppliersoc[$fk_soc][$line->id]['fk_supplier_proposal'];
					}
				}
				elseif ($aSuppliersoc[$fk_soc][$line->id]['total_ttc']>0 && $aSuppliersoc[$fk_soc][$line->id]['total_ttc'] < $lowprice)
				{
					$lowprice = $aSuppliersoc[$fk_soc][$line->id]['total_ttc'];
					$selSoc = $fk_soc;
					$aSelprod[$line->id] = $selSoc;
					$aSelsupplier[$line->id] = $aSuppliersoc[$fk_soc][$line->id]['fk_supplier_proposal'];
				}
			}
			foreach ((array) $aSoc AS $fk_soc => $fk_supplierprop)
			{
				$class = '';
				if ($aSelprod[$line->id] == $fk_soc) $class = 'style="background:#6b95b3;"';
					print '<td align="right" '.$class.'>'.price($aSuppliersoc[$fk_soc][$line->id]['total_ttc']).'</td>';
				}
				print '<td align="center">';
				if ($object->status == 1 && $object->status_process == 2 && $user->rights->purchase->sup->valcomp)
				{
					$options = '<option value="0">'.$langs->trans('Select').'</option>';
					foreach ((array) $aSoc AS $k => $fk)
					{
						$selected = '';
						if (count($aPurchaseproduct)>0)
						{
							if ($aPurchaseproduct[$line->id] == $fk) $selected = ' selected';
						}
						else
						{
							if ($aSelprod[$line->id] == $k) $selected = ' selected';
						}
						$objSociete->fetch($k);
						$options.= '<option value="'.$fk.'" '.$selected.'>'.$objSociete->nom.'</option>';
					}
				//REVISARRRRRR
					print '<select name="purchaselinesoc['.$line->fk_purchase_request.']['.$line->id.']">'.$options.'</select>';
				}
				else
				{
					$objSociete->fetch($aSelprod[$line->id]);
					print $objSociete->getNomUrl(1);
				}
				print '</td>';
				print '</tr>';
			}
			print '</table>';
			dol_fiche_end();

			if ($object->status == 1 && $object->status_process == 2 && $user->rights->purchase->sup->valcomp)
			{
				print '<div class="center">';
				print '<input type="submit" class="butAction" name="submit" value="'.$langs->trans("Approvepricecomparision").'">';
				print '</div>';
			}
			print '</form>';
		// Buttons
			print '<div class="tabsAction">33333'."\n";
			if ($object->status == 1 && $object->status_process == 1)
			{
			//vamos a verificar la hora para habilitar la comparación
				if ($user->rights->purchase->sup->compena)
					print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=enablecomp">'.$langs->trans("Enable price comparison").'</a></div>'."\n";
			}
			print '</div>'."\n";

		}

	// Example 2 : Adding links to objects
	//$somethingshown=$form->showLinkedObjectBlock($object);
	//$linktoelem = $form->showLinkToObjectBlock($object);
	//if ($linktoelem) print '<br>'.$linktoelem;

	}


// End of page
	llxFooter();
	$db->close();
