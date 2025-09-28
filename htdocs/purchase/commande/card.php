<?php
/* Copyright (C) 2004-2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005      Eric	Seigne          <eric.seigne@ryxeo.com>
 * Copyright (C) 2005-2016 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2010-2015 Juanjo Menent        <jmenent@2byte.es>
 * Copyright (C) 2011-2015 Philippe Grand       <philippe.grand@atoo-net.com>
 * Copyright (C) 2012      Marcos García        <marcosgdf@gmail.com>
 * Copyright (C) 2013      Florian Henry        <florian.henry@open-concept.pro>
 * Copyright (C) 2014      Ion Agorria          <ion@agorria.com>
 *
 * This	program	is free	software; you can redistribute it and/or modify
 * it under	the	terms of the GNU General Public	License	as published by
 * the Free	Software Foundation; either	version	2 of the License, or
 * (at your	option)	any	later version.
 *
 * This	program	is distributed in the hope that	it will	be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A	PARTICULAR PURPOSE.	 See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */

/**
 *	\file		htdocs/fourn/commande/card.php
 *	\ingroup	supplier, order
 *	\brief		Card supplier order
 */

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/modules/supplier_order/modules_commandefournisseur.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.getutil.class.php';

require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.commande.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.product.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/fourn.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';

require_once DOL_DOCUMENT_ROOT.'/purchase/class/purchaserequestdetext.class.php';

if (! empty($conf->supplier_proposal->enabled))
	require DOL_DOCUMENT_ROOT . '/supplier_proposal/class/supplier_proposal.class.php';
if (!empty($conf->produit->enabled))
	require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
if (!empty($conf->projet->enabled)) {
	require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';
}
if ($conf->poa->enabled)
{
	dol_include_once('/poa/class/poaobjetiveext.class.php');
	dol_include_once('/poa/class/poastructureext.class.php');
	dol_include_once('/poa/class/poaactivityext.class.php');
	dol_include_once('/poa/class/poapoaext.class.php');
	dol_include_once('/poa/class/poaprevext.class.php');
	dol_include_once('/poa/class/poapartidapreext.class.php');
	dol_include_once('/poa/class/poapartidacomext.class.php');
	dol_include_once('/poa/class/poapartidapredetext.class.php');
	dol_include_once('/poa/class/poaprevlog.class.php');
	dol_include_once('/poa/class/poaprocessext.class.php');
	dol_include_once('/poa/class/poaprocesscontratext.class.php');
	dol_include_once('/poa/lib/poa.lib.php');
}
if ($conf->orgman->enabled)
{
	dol_include_once('/orgman/class/cpartida.class.php');
	dol_include_once('/orgman/class/partidaproduct.class.php');
}

require_once NUSOAP_PATH.'/nusoap.php';     // Include SOAP

//require_once DOL_DOCUMENT_ROOT.'/purchase/class/html.formext.class.php';
require_once DOL_DOCUMENT_ROOT.'/productext/class/productadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/purchase/class/fournisseurcommandeext.class.php';
require_once DOL_DOCUMENT_ROOT.'/purchase/class/commandefournisseuradd.class.php';
require_once DOL_DOCUMENT_ROOT.'/purchase/class/commandefournisseurdetadd.class.php';
require_once(DOL_DOCUMENT_ROOT."/purchase/class/unitconv.class.php");

require_once DOL_DOCUMENT_ROOT.'/purchase/lib/purchase.lib.php';
require_once DOL_DOCUMENT_ROOT.'/fiscal/class/tvadefext.class.php';
require_once DOL_DOCUMENT_ROOT.'/fiscal/lib/fiscal.lib.php';

$langs->load('admin');
$langs->load('orders');
$langs->load('sendings');
$langs->load('companies');
$langs->load('bills');
$langs->load('propal');
$langs->load('supplier_proposal');
$langs->load('deliveries');
$langs->load('products');
$langs->load('stocks');
$langs->load('purchase');

if (!empty($conf->incoterm->enabled)) $langs->load('incoterm');

$id 			= GETPOST('id','int');
$ref 			= GETPOST('ref','alpha');
$action 		= GETPOST('action','alpha');
$confirm		= GETPOST('confirm','alpha');
$comclientid 	= GETPOST('comid','int');
$socid			= GETPOST('socid','int');
$projectid		= GETPOST('projectid','int');
$cancel         = GETPOST('cancel','alpha');
$lineid         = GETPOST('lineid', 'int');

if (!isset($_SESSION['period_year'])) $_SESSION['period_year'] = date('Y');
$gestion = $_SESSION['period_year'];
$period_year = $_SESSION['period_year'];

$origin = GETPOST('origin', 'alpha');
$originid = (GETPOST('originid', 'int') ? GETPOST('originid', 'int') : GETPOST('origin_id', 'int')); // For backward compatibility
// Security check
if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user, 'fournisseur', $socid, '', 'commande');


//PDF
$hidedetails = (GETPOST('hidedetails','int') ? GETPOST('hidedetails','int') : (! empty($conf->global->MAIN_GENERATE_DOCUMENTS_HIDE_DETAILS) ? 1 : 0));
$hidedesc 	 = (GETPOST('hidedesc','int') ? GETPOST('hidedesc','int') : (! empty($conf->global->MAIN_GENERATE_DOCUMENTS_HIDE_DESC) ?  1 : 0));
$hideref 	 = (GETPOST('hideref','int') ? GETPOST('hideref','int') : (! empty($conf->global->MAIN_GENERATE_DOCUMENTS_HIDE_REF) ? 1 : 0));

$datelivraison=dol_mktime(GETPOST('liv_hour','int'), GETPOST('liv_min','int'), GETPOST('liv_sec','int'), GETPOST('liv_month','int'), GETPOST('liv_day','int'),GETPOST('liv_year','int'));

//array defined
$aArraytype = array(1=>$langs->trans('Contract'),2=>$langs->trans('Purchaseorder'),3=>$langs->trans('Serviceorder'),4=>$langs->trans('Other'));
$aTypeprocess = array(1=>array('WELL' => $langs->trans('Goods')),0=>array('OTHERSERVICE'=>$langs->trans('Otherservice'),'SERVICE'=>$langs->trans('Service')));
$aTerm = array(1=>$langs->trans('D.C.'),2=>$langs->trans('D.H.'),3=>$langs->trans('Fixed term'));


// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('ordersuppliercard','globalcard'));

$object = new FournisseurCommandeext($db);
$objecttmp = new FournisseurCommandeext($db);
//tabla adicional al commande fournisseur
$objectadd = new Commandefournisseuradd($db);
$objectdetadd = new Commandefournisseurdetadd($db);
$extrafields = new ExtraFields($db);
$productadd = new Productadd($db);
$tvadef = new Tvadefext($db);
$unitconv = new Unitconv($db);

// fetch optionals attributes and labels
$extralabels=$extrafields->fetch_name_optionals_label($object->table_element);

// Load object
if ($id > 0 || ! empty($ref))
{
	$ret = $object->fetch_($id, $ref);
	if ($ret < 0) dol_print_error($db,$object->error);
	$ret = $objectadd->fetch(0,$object->id);
	if ($ret > 0)
	{
		$object->fk_poa_prev = $objectadd->fk_poa_prev;
	}
	$ret = $object->fetch_thirdparty();
	if ($ret < 0) dol_print_error($db,$object->error);
}
elseif (! empty($socid) && $socid > 0)
{
	$fourn = new Fournisseur($db);
	$ret=$fourn->fetch($socid);
	if ($ret < 0) dol_print_error($db,$object->error);
	$object->socid = $fourn->id;
	$ret = $object->fetch_thirdparty();
	if ($ret < 0) dol_print_error($db,$object->error);
}

$permissionnote=$user->rights->fournisseur->commande->creer;	// Used by the include of actions_setnotes.inc.php
$permissiondellink=$user->rights->fournisseur->commande->creer;	// Used by the include of actions_dellink.inc.php
$permissiontoedit=$user->rights->fournisseur->commande->creer;	// Used by the include of actions_lineupdown.inc.php


/*
 * Actions
 */

$parameters=array('socid'=>$socid);
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);
    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	if ($cancel) $action='';

	include DOL_DOCUMENT_ROOT.'/core/actions_setnotes.inc.php';	// Must be include, not include_once

	include DOL_DOCUMENT_ROOT.'/core/actions_dellink.inc.php';		// Must be include, not include_once

	include DOL_DOCUMENT_ROOT.'/core/actions_lineupdown.inc.php';	// Must be include, not include_once

	if ($action == 'setref_supplier' && $user->rights->fournisseur->commande->creer)
	{
		$result=$object->setValueFrom('ref_supplier',GETPOST('ref_supplier','alpha'));
		if ($result < 0) setEventMessages($object->error, $object->errors, 'errors');
		else $object->ref_supplier = GETPOST('ref_supplier','alpha');	// The setValueFrom does not set new property of object
	}

	// Set incoterm
	if ($action == 'set_incoterms' && $user->rights->fournisseur->commande->creer)
	{
		$result = $object->setIncoterms(GETPOST('incoterm_id', 'int'), GETPOST('location_incoterms', 'alpha'));
		if ($result < 0) setEventMessages($object->error, $object->errors, 'errors');
	}

	// payment conditions
	if ($action == 'setconditions' && $user->rights->fournisseur->commande->creer)
	{
		$result=$object->setPaymentTerms(GETPOST('cond_reglement_id','int'));
		if ($result < 0) setEventMessages($object->error, $object->errors, 'errors');
	}

	// payment mode
	if ($action == 'setmode' && $user->rights->fournisseur->commande->creer)
	{
		$result = $object->setPaymentMethods(GETPOST('mode_reglement_id','int'));
		if ($result < 0) setEventMessages($object->error, $object->errors, 'errors');
	}

	// Multicurrency Code
	else if ($action == 'setmulticurrencycode' && $user->rights->fournisseur->commande->creer) {
		$result = $object->setMulticurrencyCode(GETPOST('multicurrency_code', 'alpha'));
	}

	// Multicurrency rate
	else if ($action == 'setmulticurrencyrate' && $user->rights->fournisseur->commande->creer) {
		$result = $object->setMulticurrencyRate(price2num(GETPOST('multicurrency_tx')));
	}

	// bank account
	if ($action == 'setbankaccount' && $user->rights->fournisseur->commande->creer)
	{
		$result=$object->setBankAccount(GETPOST('fk_account', 'int'));
		if ($result < 0) setEventMessages($object->error, $object->errors, 'errors');
	}

	// date of delivery
	if ($action == 'setdate_livraison' && $user->rights->fournisseur->commande->creer)
	{
		$result=$object->set_date_livraison($user,$datelivraison);
		if ($result < 0) setEventMessages($object->error, $object->errors, 'errors');
	}

	// Set project
	if ($action ==	'classin' && $user->rights->fournisseur->commande->creer)
	{
		$result=$object->setProject($projectid);
		if ($result < 0) setEventMessages($object->error, $object->errors, 'errors');
	}

	if ($action == 'setremisepercent' && $user->rights->fournisseur->commande->creer)
	{
		$result = $object->set_remise($user, $_POST['remise_percent']);
		if ($result < 0) setEventMessages($object->error, $object->errors, 'errors');
	}

	if ($action == 'reopen')	// no test on permission here, permission to use will depends on status
	{
		if (in_array($object->statut, array(1, 2, 3, 4, 5, 6, 7, 9)))
		{
			if ($object->statut == 1) $newstatus=0;	// Validated->Draft
			else if ($object->statut == 2) $newstatus=0;	// Approved->Draft
			else if ($object->statut == 3) $newstatus=2;	// Ordered->Approved
			else if ($object->statut == 4) $newstatus=3;
			else if ($object->statut == 5)
			{
				//$newstatus=2;    // Ordered
				// TODO Can we set it to submited ?
				//$newstatus=3;  // Submited
				// TODO If there is at least one reception, we can set to Received->Received partially
				$newstatus=4;  // Received partially

			}
			else if ($object->statut == 6) $newstatus=2;	// Canceled->Approved
			else if ($object->statut == 7) $newstatus=3;	// Canceled->Process running
			else if ($object->statut == 9) $newstatus=1;	// Refused->Validated
			else $newstatus = 2;

			//print "old status = ".$object->statut.' new status = '.$newstatus;
			$db->begin();

			$result = $object->setStatus($user, $newstatus);
			if ($result > 0)
			{
				// Currently the "Re-open" also remove the billed flag because there is no button "Set unpaid" yet.
				$sql = 'UPDATE '.MAIN_DB_PREFIX.'commande_fournisseur';
				$sql.= ' SET billed = 0';
				$sql.= ' WHERE rowid = '.$object->id;

				$resql=$db->query($sql);

				if ($newstatus == 0)
				{
					$sql = 'UPDATE '.MAIN_DB_PREFIX.'commande_fournisseur';
					$sql.= ' SET fk_user_approve = null, fk_user_approve2 = null, date_approve = null, date_approve2 = null';
					$sql.= ' WHERE rowid = '.$object->id;

					$resql=$db->query($sql);
				}

				$db->commit();

				header('Location: '.$_SERVER["PHP_SELF"].'?id='.$object->id);
				exit;
			}
			else
			{
				$db->rollback();

				setEventMessages($object->error, $object->errors, 'errors');
			}
		}
	}

	/*
	 * Classify supplier order as billed
	 */
	if ($action == 'classifybilled' && $user->rights->fournisseur->commande->creer)
	{
		$ret=$object->classifyBilled($user);
		if ($ret < 0) {
			setEventMessages($object->error, $object->errors, 'errors');
		}
	}

	// Add a product line
	if ($action == 'addline' && $user->rights->fournisseur->commande->creer)
	{
		$langs->load('errors');
		$error = 0;
		$lUnit = false;

		$ret1 = $objectadd->fetch('',$object->id);
		if ($ret1 < 0)
		{
			dol_print_error($db,$objectadd->error);
			exit;
		}
		$discount = GETPOST('discount');
		// Set if we used free entry or predefined product
		$predef='';
		$product_desc=(GETPOST('dp_desc')?GETPOST('dp_desc'):'');
		$date_start=dol_mktime(GETPOST('date_start'.$predef.'hour'), GETPOST('date_start'.$predef.'min'), GETPOST('date_start' . $predef . 'sec'), GETPOST('date_start'.$predef.'month'), GETPOST('date_start'.$predef.'day'), GETPOST('date_start'.$predef.'year'));
		$date_end=dol_mktime(GETPOST('date_end'.$predef.'hour'), GETPOST('date_end'.$predef.'min'), GETPOST('date_end' . $predef . 'sec'), GETPOST('date_end'.$predef.'month'), GETPOST('date_end'.$predef.'day'), GETPOST('date_end'.$predef.'year'));
		if (GETPOST('prod_entry_mode') == 'free')
		{
			$idprod=0;
			$price_ht = GETPOST('price_ht');
			$tva_tx = (GETPOST('tva_tx') ? GETPOST('tva_tx') : 0);
		}
		else
		{
			$idprod=(GETPOST('idprod', 'int')?GETPOST('idprod', 'int'):GETPOST('idprodfournprice', 'int'));
			if (empty($idprod))
			{
				$product = new Product($db);
				$product->fetch($idprod,GETPOST('search_idprodfournprice'));
				if ($product->ref == GETPOST('search_idprodfournprice'))
					$idprod = $product->id;
				if ($product->fk_unit == GETPOST('fk_unit'))
					$lUnit = true;
			}
			else
			{
				$product = new Product($db);
				$product->fetch($idprod);
				if ($product->fk_unit == GETPOST('fk_unit'))
					$lUnit = true;
			}
			$price_ht = '';
			$tva_tx = '';
		}

		$qty = GETPOST('qty'.$predef);
		$remise_percent=GETPOST('remise_percent'.$predef);
		$discount=GETPOST('discount');
		// Extrafields
		$extrafieldsline = new ExtraFields($db);
		$extralabelsline = $extrafieldsline->fetch_name_optionals_label($object->table_element_line);
		$array_options = $extrafieldsline->getOptionalsFromPost($extralabelsline, $predef);
		// Unset extrafield
		if (is_array($extralabelsline)) {
			// Get extra fields
			foreach ($extralabelsline as $key => $value) {
				unset($_POST["options_" . $key]);
			}
		}

		if (GETPOST('prod_entry_mode')=='free' && GETPOST('price_ht') < 0 && $qty < 0)
		{
			setEventMessages($langs->trans('ErrorBothFieldCantBeNegative', $langs->transnoentitiesnoconv('UnitPrice'), $langs->transnoentitiesnoconv('Qty')), null, 'errors');
			$error++;
		}
		if (GETPOST('prod_entry_mode')=='free'  && ! GETPOST('idprodfournprice') && GETPOST('type') < 0)
		{
			setEventMessages($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Type')), null, 'errors');
			$error++;
		}
		if (GETPOST('prod_entry_mode')=='free' && GETPOST('price_ht')==='' && GETPOST('price_ttc')==='')
		// Unit price can be 0 but not ''
		{
			setEventMessages($langs->trans($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('UnitPrice'))), null, 'errors');
			$error++;
		}
		if (GETPOST('prod_entry_mode')=='free' && ! GETPOST('dp_desc'))
		{
			setEventMessages($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Description')), null, 'errors');
			$error++;
		}
		if (! GETPOST('qty'))
		{
			setEventMessages($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Qty')), null, 'errors');
			$error++;
		}
		//revisamos la unidad de medida
		if ($idprod>0 && !$lUnit)
		{
			$filterstatic = " AND t.fk_product = ".$idprod;
			$res = $unitconv->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic);
			if ($res<=0)
			{
				setEventMessages($langs->trans('ErrorNotUnitDefinedForTheProductx'),null,'errors');
				$error++;
			}
			else
			{
				foreach($unitconv->lines AS $j =>$line)
				{
					if ($line->fk_unit_ext == GETPOST('fk_unit'))
						$lUnit = true;
				}
				if (!$lUnit)
				{
					setEventMessages($langs->trans('ErrorNotUnitDefinedForTheProduct'),null,'errors');
					$error++;
				}
			}
		}
		// Ecrase $pu par celui	du produit
		// Ecrase $desc	par	celui du produit
		// Ecrase $txtva  par celui du produit
		if ((GETPOST('prod_entry_mode') != 'free') && empty($error))
		// With combolist mode idprodfournprice is > 0 or -1. With autocomplete, idprodfournprice is > 0 or ''
		{
			$productsupplier=new ProductFournisseur($db);
			//buscamos el producto
			if (!empty($conf->produit->enabled))
			{
				$product = new Product($db);
				$product->fetch($idprod,(GETPOST('search_idprodfournprice')?GETPOST('search_idprodfournprice'):null));
				if ($product->ref == GETPOST('search_idprodfournprice'))
					$idprod = $product->id;
				$_POST['idprodfournprice'] = $idprod;
				$productadd->fetch(0,$idprod);
			}

			$productsupplier = new ProductFournisseur($db);
			$retarray = $productsupplier->list_product_fournisseur_price($idprod, '', '');
			//verificamos si tiene precios el proveedor
			$lProduct = false;
			if (empty($retarray)) $lProduct = true;
			foreach ((array) $retarray AS $k => $array)
			{
				if ($array->fk_fourn == $object->socid)
				{
					$lProduct = true;
					$fourn_ref = $array->fourn_ref;
				}
			}
			$result=$product->fetch($idprod);
			if ($result <= 0)
			{
				$error++;
				setEventMessage($product->error, 'errors');
			}
			if (! $error)
			{
				//agregamos producto al proveedor
				include DOL_DOCUMENT_ROOT.'/purchase/function/addproductfourn.php';
			}


			if (empty($conf->global->SUPPLIER_ORDER_WITH_NOPRICEDEFINED))
			{
				$idprod=0;
				if (GETPOST('idprodfournprice') == -1 || GETPOST('idprodfournprice') == '') $idprod=-99;
				// Same behaviour than with combolist. When not select idprodfournprice is now -99 (to avoid conflict with next action that may return -1, -2, ...)
			}

			if (GETPOST('idprodfournprice') > 0)
			{
				//$idprod=$productsupplier->get_buyprice(GETPOST('idprodfournprice'), $qty);
				// Just to see if a price exists for the quantity. Not used to found vat.
			}
			if ($idprod > 0)
			{

				$res=$productsupplier->fetch($idprod);
				$label = $productsupplier->label;

				$desc = $productsupplier->description;
				if (trim($product_desc) != trim($desc)) $desc = dol_concatdesc($desc, $product_desc);

				$type = $productsupplier->type;

				$tva_tx	= get_default_tva($object->thirdparty, $mysoc, $productsupplier->id, GETPOST('idprodfournprice'));
				$tva_npr = get_default_npr($object->thirdparty, $mysoc, $productsupplier->id, GETPOST('idprodfournprice'));
				if (empty($tva_tx)) $tva_npr=0;
				$localtax1_tx= get_localtax($tva_tx, 1, $mysoc, $object->thirdparty, $tva_npr);
				$localtax2_tx= get_localtax($tva_tx, 2, $mysoc, $object->thirdparty, $tva_npr);
				if (!empty(GETPOST('fk_unit'))) $productsupplier->fk_unit = GETPOST('fk_unit');

				$tva_tx = 0;
				//$type = $productsupplier->type;
				$price_base_type = 'HT';
				if ($conf->global->PRICE_TAXES_INCLUDED)
				{
					$price_base_type = 'TTC';
					$pu_ttc = GETPOST('price_ttc');
					$pu = $pu_ttc;
					//$ht = $ttc / (1 + ($tva_tx / 100));
					$ht = $pu_ttc;
				}
				else
				{
					$price_base_type = 'HT';
					$ht = GETPOST('price_ht');
					$pu = $ht;
					$pu_ttc = $ht;
				}

						//procesamos el calculo de los impuestos
				$tvacalc = array();
				$tvaht = array();
				$tvattc = array();
				$tvatx = array();
				$pu = GETPOST('price_ht');
				$price_base_type = 'HT';
				if ($conf->global->PRICE_TAXES_INCLUDED)
				{
					$price_base_type = 'TTC';
					$pu = GETPOST('price_ttc');
				}
				$k = 1;
				$lines = new stdClass();
				$lines->price = $pu;
				$lines->qty = $qty;
				$lines->fk_product = $idprod;
				$lines->fk_unit = GETPOST('fk_unit');
				$discount = $discount+0;
				if (empty($lines->fk_unit)) $lines->fk_unit = $productsupplier->fk_unit;
				if (!empty(GETPOST('fk_unit'))) $lines->fk_unit = GETPOST('fk_unit');
				else $lines->fk_unit = $productsupplier->fk_unit;
				$type = ($productsupplier->type?$productsupplier->type:0);
				$amount_ice = GETPOST('amount_ice');
				//if ($productadd->sel_ice)
				//{
				//	if ($amount_ice <=0)
				//	{
				//		$error++;
				//		setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Amountice")), 'errors');
				//	}
				//}
				//else
				if (empty($amount_ice)) $amount_ice = 0;

				include DOL_DOCUMENT_ROOT.'/fiscal/include/calclinefiscal.inc.php';
				//agregamos producto al proveedor
				//include DOL_DOCUMENT_ROOT.'/purchase/function/addproductfourn.php';
				//
				$result=$object->addlineadd(
					$desc,
					$lines->subprice,
					//$productsupplier->fourn_pu,
					$lines->qty,
					$lines->tva_tx,
					$lines->localtax1_tx,
					$lines->localtax2_tx,
					$lines->fk_product,
					$idprodfournprice,
					$fourn_ref,
					$remise_percent,
					$price_base_type,
					$lines->price,
					$type,
					$tva_npr,
					'',
					$date_start,
					$date_end,
					$array_options,
					$lines->fk_unit,
					$lines
				);
				if ($result <= 0)
				{
					$error++;
					setEventMessages($objectc->error,$object->errors,'errors');
				}
				else
				{
												//creamos el registro adicional
					$objectdetadd->initAsSpecimen();
					$objectdetadd->fk_commande_fournisseurdet = $result;
					$objectdetadd->fk_departament = GETPOST('fk_departament')+0;
					$objectdetadd->fk_object = 0;
					$objectdetadd->object = '';
					$objectdetadd->fk_fabrication = GETPOST('fk_fabrication')+0;
					$objectdetadd->fk_fabricationdet = GETPOST('fk_fabricationdet')+0;
					$objectdetadd->fk_projet = GETPOST('fk_projet')+0;
					$objectdetadd->fk_projet_task = GETPOST('fk_projet_task')+0;
					$objectdetadd->fk_jobs = GETPOST('fk_jobs')+0;
					$objectdetadd->fk_jobsdet = GETPOST('fk_jobsdet')+0;
					$objectdetadd->fk_structure = GETPOST('fk_structure')+0;
					$objectdetadd->fk_poa = GETPOST('fk_poa')+0;
					$objectdetadd->partida = GETPOST('partida');
					$objectdetadd->amount_ice = GETPOST('amount_ice')+0;
					$objectdetadd->discount = $discount;
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
			}
			if ($idprod == -99 || $idprod == 0)
			{
				// Product not selected
				$error++;
				$langs->load("errors");
				setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("ProductOrService")).' '.$langs->trans("or").' '.$langs->trans("NoPriceDefinedForThisSupplier"), null, 'errors');
			}
			if ($idprod == -1)
			{
				// Quantity too low
				$error++;
				$langs->load("errors");
				setEventMessages($langs->trans("ErrorQtyTooLowForThisSupplier"), null, 'errors');
			}
		}
		elseif((GETPOST('price_ht')!=='' || GETPOST('price_ttc')!=='') && empty($error))
		{
			$pu_ht = price2num($price_ht, 'MU');
			$pu_ttc = price2num(GETPOST('price_ttc'), 'MU');
			$tva_npr = (preg_match('/\*/', $tva_tx) ? 1 : 0);
			$tva_tx = str_replace('*', '', $tva_tx);
			$label = (GETPOST('dp_desc') ? GETPOST('dp_desc') : '');
			$desc = $product_desc;
			$type = GETPOST('type');

			$fk_unit= GETPOST('units', 'alpha');

			$tva_tx = price2num($tva_tx);
			// When vat is text input field

			// Local Taxes
			$localtax1_tx= get_localtax($tva_tx, 1, $mysoc, $object->thirdparty);
			$localtax2_tx= get_localtax($tva_tx, 2, $mysoc, $object->thirdparty);

			$price_base_type = 'HT';
			if ($conf->global->PRICE_TAXES_INCLUDED)
			{
				$price_base_type = 'TTC';
				$pu_ttc = GETPOST('price_ttc');
				$pu = $pu_ttc;
				//$ht = $ttc / (1 + ($tva_tx / 100));
				$ht = $pu_ttc;
			}
			else
			{
				$price_base_type = 'HT';
				$ht = GETPOST('price_ht');
				$pu = $ht;
				$pu_ttc = $ht;
			}

						//procesamos el calculo de los impuestos
			$tvacalc = array();
			$tvaht = array();
			$tvattc = array();
			$tvatx = array();
			$pu = GETPOST('price_ht');
			$price_base_type = 'HT';
			if ($conf->global->PRICE_TAXES_INCLUDED)
			{
				$price_base_type = 'TTC';
				$pu = GETPOST('price_ttc');
			}
			$k = 1;
			$lines = new stdClass();
			$lines->label = $label;
			$lines->price = $pu;
			$lines->qty = $qty;
			$lines->fk_product = $idprod;
			$lines->fk_unit = GETPOST('fk_unit');
			if (empty($lines->fk_unit)) $lines->fk_unit = $productsupplier->fk_unit;
			if (!empty(GETPOST('fk_unit'))) $lines->fk_unit = GETPOST('fk_unit');
			else $lines->fk_unit = $productsupplier->fk_unit;
			$type = ($productsupplier->type?$productsupplier->type:1);

			include DOL_DOCUMENT_ROOT.'/fiscal/include/calclinefiscal.inc.php';

			$result=$object->addlineadd(
				$desc,
				$lines->subprice,
					//$productsupplier->fourn_pu,
				$lines->qty,
				$lines->tva_tx,
				$lines->localtax1_tx,
				$lines->localtax2_tx,
				$lines->fk_product,
				$idprodfournprice,
				$fourn_ref,
				$remise_percent,
				$price_base_type,
				$lines->price,
				$type,
				$tva_npr,
				'',
				$date_start,
				$date_end,
				$array_options,
				$lines->fk_unit,
				$lines
			);
			if ($result <= 0)
			{
				$error++;
				setEventMessages($objectc->error,$object->errors,'errors');
			}
			else
			{
												//creamos el registro adicional
				$objectdetadd->initAsSpecimen();
				$objectdetadd->fk_commande_fournisseurdet = $result;
				$objectdetadd->fk_departament = GETPOST('fk_departament')+0;
				$objectdetadd->fk_object = 0;
				$objectdetadd->object = '';
				$objectdetadd->fk_fabrication = GETPOST('fk_fabrication')+0;
				$objectdetadd->fk_fabricationdet = GETPOST('fk_fabricationdet')+0;
				$objectdetadd->fk_projet = GETPOST('fk_projet')+0;
				$objectdetadd->fk_projet_task = GETPOST('fk_projet_task')+0;
				$objectdetadd->fk_jobs = GETPOST('fk_jobs')+0;
				$objectdetadd->fk_jobsdet = GETPOST('fk_jobsdet')+0;
				$objectdetadd->fk_structure = GETPOST('fk_structure')+0;
				$objectdetadd->fk_poa = GETPOST('fk_poa')+0;
				$objectdetadd->partida = GETPOST('partida');
				$objectdetadd->amount_ice = GETPOST('amount_ice')+0;
				$objectdetadd->discount = $discount+0;
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


			//	    	$result=$object->addline($desc, $ht, $qty, $tva_tx, $localtax1_tx, $localtax2_tx, 0, 0, '', $remise_percent, $price_base_type, $ttc, $type,'','', $date_start, $date_end, $array_options, $fk_unit);
		}
		//actualizamos la cabecera
		if (!$error)
		{
			$error = update_total_commande($id);
		}
		//print "xx".$tva_tx; exit;
		if (! $error && $result > 0)
		{
			$ret=$object->fetch_($object->id);    // Reload to get new records

			// Define output language
			if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
			{
				$outputlangs = $langs;
				$newlang = '';
				if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
				if ($conf->global->MAIN_MULTILANGS && empty($newlang))	$newlang = $object->thirdparty->default_lang;
				if (! empty($newlang)) {
					$outputlangs = new Translate("", $conf);
					$outputlangs->setDefaultLang($newlang);
				}
				$model=$object->modelpdf;
				$ret = $object->fetch_($id); // Reload to get new records

				$result=$object->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);
				if ($result < 0) dol_print_error($db,$result);
			}

			unset($_POST ['prod_entry_mode']);

			unset($_POST['qty']);
			unset($_POST['type']);
			unset($_POST['remise_percent']);
			unset($_POST['pu']);
			unset($_POST['price_ht']);
			unset($_POST['multicurrency_price_ht']);
			unset($_POST['price_ttc']);
			unset($_POST['tva_tx']);
			unset($_POST['label']);
			unset($localtax1_tx);
			unset($localtax2_tx);
			unset($_POST['np_marginRate']);
			unset($_POST['np_markRate']);
			unset($_POST['dp_desc']);
			unset($_POST['idprodfournprice']);

			unset($_POST['date_starthour']);
			unset($_POST['date_startmin']);
			unset($_POST['date_startsec']);
			unset($_POST['date_startday']);
			unset($_POST['date_startmonth']);
			unset($_POST['date_startyear']);
			unset($_POST['date_endhour']);
			unset($_POST['date_endmin']);
			unset($_POST['date_endsec']);
			unset($_POST['date_endday']);
			unset($_POST['date_endmonth']);
			unset($_POST['date_endyear']);
		}
		else
		{
			setEventMessages($object->error, $object->errors, 'errors');
		}
	}

	/*
	 *	Updating a line in the order
	 */
	if ($action == 'updateline' && $user->rights->fournisseur->commande->creer &&	! GETPOST('cancel'))
	{
		$objectadd->fetch(0,$object->id);

		$tva_tx = GETPOST('tva_tx');
		$qty = GETPOST('qty');
		$discount = GETPOST('discount');
		$remise_percent = GETPOST('remise_percent');
		$product_desc = GETPOST('product_desc');
		if ($lineid)
		{
			$lines = new CommandeFournisseurLigne($db);
			$res = $lines->fetch($lineid);
			$idprod = $lines->fk_product;
			$desc = $lines->desc;
			if (!$res) dol_print_error($db);
			if (empty($lines->label) && $idprod>0)
			{
				$product = new Product($db);
				$product->fetch($idprod);
				if (empty($lines->ref)) $lines->ref = $product->ref;
				if (empty($lines->label)) $lines->label = $product->label;
			}
			if (empty($lines->label) && ($idprod<=0 || is_null($idprod)))
			{
				$lines->label = $lines->desc;
			}
		}

		$date_start=dol_mktime(GETPOST('date_starthour'), GETPOST('date_startmin'), GETPOST('date_startsec'), GETPOST('date_startmonth'), GETPOST('date_startday'), GETPOST('date_startyear'));
		$date_end=dol_mktime(GETPOST('date_endhour'), GETPOST('date_endmin'), GETPOST('date_endsec'), GETPOST('date_endmonth'), GETPOST('date_endday'), GETPOST('date_endyear'));

		$localtax1_tx=get_localtax($tva_tx,1,$mysoc,$object->thirdparty);
		$localtax2_tx=get_localtax($tva_tx,2,$mysoc,$object->thirdparty);

		// Extrafields Lines
		$extrafieldsline = new ExtraFields($db);
		$extralabelsline = $extrafieldsline->fetch_name_optionals_label($object->table_element_line);
		$array_options = $extrafieldsline->getOptionalsFromPost($extralabelsline);
		// Unset extrafield POST Data
		if (is_array($extralabelsline)) {
			foreach ($extralabelsline as $key => $value) {
				unset($_POST["options_" . $key]);
			}
		}

		$productsupplier = new ProductFournisseur($db);

		if ($idprod>0)
		{
			$res=$productsupplier->fetch($idprod);
			$label = $productsupplier->label;
			$desc = $productsupplier->description;

			if (trim($product_desc) != trim($desc)) $desc = dol_concatdesc($desc, $product_desc);
			$type = $productsupplier->type;
			if (!empty(GETPOST('fk_unit'))) $productsupplier->fk_unit = GETPOST('fk_unit');
		}
		else
		{
			if (!empty(GETPOST('product_desc'))) $desc = GETPOST('product_desc');
		}
		$tva_tx = 0;
				//$type = $productsupplier->type;

						//procesamos el calculo de los impuestos
		$tvacalc = array();
		$tvaht = array();
		$tvattc = array();
		$tvatx = array();
		$pu = GETPOST('price_ht');
		$price_base_type = 'HT';
		if ($conf->global->PRICE_TAXES_INCLUDED)
		{
			$price_base_type = 'TTC';
			$pu = GETPOST('price_ttc');
		}

		$k = 1;
		$lines->qty = $qty;
		$lines->price = $pu;
		$lines->fk_unit = GETPOST('fk_unit');
		if (empty($lines->fk_unit)) $lines->fk_unit = $productsupplier->fk_unit;
		if (!empty(GETPOST('fk_unit'))) $lines->fk_unit = GETPOST('fk_unit');
		else $lines->fk_unit = $productsupplier->fk_unit;
		$type = ($productsupplier->type?$productsupplier->type:0);

		include DOL_DOCUMENT_ROOT.'/fiscal/include/calclinefiscal.inc.php';

		$result=$object->updatelineadd(
			$lineid,
			$desc,
			$lines->subprice,
					//$productsupplier->fourn_pu,
			$lines->qty,
			$lines->tva_tx,
			$lines->localtax1_tx,
			$lines->localtax2_tx,
			$lines->fk_product,
			$idprodfournprice,
			$fourn_ref,
			$lines->remise_percent,
			$price_base_type,
			$lines->price,
			$type,
			$tva_npr,
			'',
			$date_start,
			$date_end,
			$array_options,
			$lines->fk_unit,
			$lines
		);

		if ($result <= 0)
		{
			$error++;
			setEventMessages($objectc->error,$object->errors,'errors');
		}
		else
		{
			//actualizamos el registro adicional
			$res = $objectdetadd->fetch(0,$lineid);
			if ($res == 1)
			{
				//$objectdetadd->fk_object = 0;
				//$objectdetadd->object = '';
				$objectdetadd->fk_fabrication = GETPOST('fk_fabrication')+0;
				$objectdetadd->fk_fabricationdet = GETPOST('fk_fabricationdet')+0;
				$objectdetadd->fk_projet = GETPOST('fk_projet')+0;
				$objectdetadd->fk_projet_task = GETPOST('fk_projet_task')+0;
				$objectdetadd->fk_jobs = GETPOST('fk_jobs')+0;
				$objectdetadd->fk_jobsdet = GETPOST('fk_jobsdet')+0;
				$objectdetadd->fk_structure = GETPOST('fk_structure')+0;
				$objectdetadd->fk_poa = GETPOST('fk_poa')+0;
				$objectdetadd->partida = GETPOST('partida');
				$objectdetadd->amount_ice = GETPOST('amount_ice')+0;
				$objectdetadd->discount = $discount;
				$objectdetadd->fk_user_mod = $user->id;
				$objectdetadd->datem = dol_now();
				$objectdetadd->tms = dol_now();
				$objectdetadd->status = 1;
												//echo '<hr>resdetadd '.
				$resdetadd = $objectdetadd->update($user);
				if ($resdetadd<=0)
				{
					$error++;
					setEventMessages($objectdetadd->error, $objectdetadd->errors,'errors');
				}
			}
		}



		if (!$error)
		{
			$error = update_total_commande($id);
		}

		unset($_POST['qty']);
		unset($_POST['type']);
		unset($_POST['idprodfournprice']);
		unset($_POST['remmise_percent']);
		unset($_POST['dp_desc']);
		unset($_POST['np_desc']);
		unset($_POST['pu']);
		unset($_POST['tva_tx']);
		unset($_POST['date_start']);
		unset($_POST['date_end']);
		unset($_POST['units']);
		unset($localtax1_tx);
		unset($localtax2_tx);

		unset($_POST['date_starthour']);
		unset($_POST['date_startmin']);
		unset($_POST['date_startsec']);
		unset($_POST['date_startday']);
		unset($_POST['date_startmonth']);
		unset($_POST['date_startyear']);
		unset($_POST['date_endhour']);
		unset($_POST['date_endmin']);
		unset($_POST['date_endsec']);
		unset($_POST['date_endday']);
		unset($_POST['date_endmonth']);
		unset($_POST['date_endyear']);
		unset($_POST['discount']);
		if ($result	>= 0)
		{
			setEventMessages($langs->trans('Updatesuccesfull'),null,'mesgs');
			if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
			{
				$outputlangs = $langs;
				$newlang = '';
				if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
				if ($conf->global->MAIN_MULTILANGS && empty($newlang))	$newlang = $object->thirdparty->default_lang;
				if (! empty($newlang)) {
					$outputlangs = new Translate("", $conf);
					$outputlangs->setDefaultLang($newlang);
				}
				$model=$object->modelpdf;
				$ret = $object->fetch_($id); // Reload to get new records

				$result=$object->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);
				if ($result < 0) dol_print_error($db,$result);
			}
		}
		else
		{
			dol_print_error($db,$object->error);
			exit;
		}
	}

	// Remove a product line
	if ($action == 'confirm_deleteline' && $confirm == 'yes' && $user->rights->fournisseur->commande->creer)
	{
		$result = $object->deleteline($lineid);
		if ($result <=0) $error++;
		$objectdetadd->fetch(0,$lineid);
		$res = $objectdetadd->delete($user);
		if ($res <=0) $error++;

		if (!$error)
		{
			$error = update_total_commande($id);
		}

		if ($result > 0 && !$error)
		{
			// Define output language
			$outputlangs = $langs;
			$newlang = '';
			if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id'))
				$newlang = GETPOST('lang_id');
			if ($conf->global->MAIN_MULTILANGS && empty($newlang))
				$newlang = $object->thirdparty->default_lang;
			if (! empty($newlang)) {
				$outputlangs = new Translate("", $conf);
				$outputlangs->setDefaultLang($newlang);
			}
			if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE)) {
				$ret = $object->fetch_($object->id); // Reload to get new records
				$object->generateDocument($object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
			}

			header('Location: '.$_SERVER["PHP_SELF"].'?id='.$object->id);
			exit;
		}
		else
		{
			setEventMessages($object->error, $object->errors, 'errors');
			/* Fix bug 1485 : Reset action to avoid asking again confirmation on failure */
			$action='';
		}
	}

	// Validate
	if ($action == 'confirm_valid' && $confirm == 'yes' && ((empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->fournisseur->commande->creer))
		|| (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->fournisseur->supplier_order_advance->validate)))
)
	{
		$now = dol_now();
		$db->begin();
		if ($conf->global->PURCHASE_INTEGRATED_POA)
		{
			//verificamos si tiene fk_poa_prev creado
			//
			$objpoa = new Poapoaext($db);
			$objactivity = new Poaactivityext($db);
			$objPrev = new Poaprevext($db);
			$objppp = new Poapartidapreext($db);
			$objppc = new Poapartidacomext($db);
			$objPoaprocesscontrat = new Poaprocesscontratext($db);
			$objProcess = new Poaprocessext($db);

			if (empty($objectadd->fk_poa_prev))
			{
				//no existe preventivo
				//se debe crear o mandar errort
				$error++;
				setEventMessages($langs->trans('El proceso esta integrado al POA, se debe cumplir el ciclo de compra'),null,'errors');

				/*
				//recuperamos el poa para obtener la estructura del mismo
				$res = $objpoa->fetch(GETPOST('fk_poa'));
				if ($res<=0)
				{
					$error++;
					setEventMessages($objpoa->error,$objpoa->errors,'errors');
				}
				//debemos crear la actividad si no esta creado
				$subaction = 'addactivity';
				include DOL_DOCUMENT_ROOT.'/poa/activity/inc/abm_activity.inc.php';
				if ($idact <=0)
				{
					$error++;
				}
				if (!$error && $idact > 0)
				{
				//creamos el preventivo
					$objPrev = new Poaprevext($db);

					$objPrev->entity = $conf->entity;
					$objPrev->gestion = $period_year;
					$objPrev->fk_pac = GETPOST('fk_pac')+0;
					$objPrev->fk_father = GETPOST('fk_father')+0;
					$objPrev->fk_area = $object->fk_departament;
					$objPrev->fk_poa_activity = $idact;
					$objPrev->origin = 'purchaserequest';
					$objPrev->originid = $object->id;
					$objPrev->code_requirement = $objactivity->code_requirement;
					$objPrev->label = $objactivity->label;
					$objPrev->pseudonym = $objactivity->pseudonym;
					$objPrev->nro_preventive = $objactivity->nro_activity;
					$objPrev->date_preventive = $objactivity->date_activity;
					$objPrev->amount = $objactivity->amount+0;
					$objPrev->priority = $objactivity->priority;
					$objPrev->fk_user_create = $user->id;
					$objPrev->fk_user_mod = $user->id;
					$objPrev->datec = $now;
					$objPrev->datem = $now;
					$objPrev->statut = 0;
					$objPrev->status_plan = 0;
					$objPrev->status_pres = 0;
					$objPrev->active = 1;

					$idprev = $objPrev->create($user);
					if ($idprev <=0)
					{
						$error++;
						setEventMessages($objPrev->error,$objPrev->errors,'errors');
					}
					if (!$error)
					{
					//procesamos registro de log
						$objPoaprevlog = new Poaprevlog($db);
						$objPoaprevlog->fk_poa_prev = $idprev;
						$objPoaprevlog->refaction = $action;
						$objPoaprevlog->description = GETPOST('description','alpha');
						$objPoaprevlog->status = $objPrev->statut;
						$objPoaprevlog->fk_user_create = $user->id;
						$objPoaprevlog->fk_user_mod = $user->id;
						$objPoaprevlog->datec = $now;
						$objPoaprevlog->datem = $now;
						$objPoaprevlog->tms = $now;
						$res = $objPoaprevlog->create($user);
						if ($res <=0)
						{
							$error++;
							setEventMessages($objPoaprevlog->error,$objPoaprevlog->errors,'errors');
						}
					}
					if (!$error)
					{

						//cargamos las partidas afectadas
						$aPartida = GETPOST('aPartida');
						$aPartidadet = GETPOST('aPartidadet');
						$aPartidaorig = GETPOST('aPartidaorig');
						$sumAmount = 0;
						foreach ($aPartida AS $codepartida => $value)
						{
							$sumAmount+= $value;
							$objppp->fk_poa_prev = $idprev;
							$objppp->fk_structure = $objpoa->fk_structure;
							$objppp->fk_poa = $objpoa->id;
							$objppp->partida = $codepartida;
							$objppp->amount = $value;
							$objppp->fk_user_create = $user->id;
							$objppp->fk_user_mod = $user->id;
							$objppp->datec = $now;
							$objppp->datem = $now;
							$objppp->tms = $now;
							$objppp->statut = 1;
							$objppp->active = 1;
							$idppp = $objppp->create($user);
							if ($idppp<=0)
							{
								$error++;
								setEventMessages($objppp->error,$objppp->errors,'errors');
							}
							//guardamos el detallde de la partida
							if (!$error)
							{
								$objpppd = new Poapartidapredetext($db);
								foreach ($aPartidaorig[$codepartida] AS $fk_product => $aData)
								{
									foreach ($aData AS $lineid => $value)
									{
									//recuperamos la linea de objectdet para obtener el detalle
										$objectdet->fetch($lineid);
										$objpppd->fk_poa_partida_pre = $idppp;
										$objpppd->fk_product = $fk_product;
										$objpppd->fk_contrat = 0;
										$objpppd->fk_contrato = 0;
										$objpppd->fk_poa_partida_com = 0;
										$objpppd->origin = 'purchaserequest';
										$objpppd->originid = $lineid;
										$objpppd->quant = $objectdet->qty;
										$objpppd->amount_base = $value;
										$objpppd->detail = $objectdet->description;
										$objpppd->quant_adj = 0;
										$objpppd->amount = 0;
										$objpppd->fk_user_create = $user->id;
										$objpppd->fk_user_mod = $user->id;
										$objpppd->datec = $now;
										$objpppd->datem = $now;
										$objpppd->tms = $now;
										$objpppd->statut = 1;

										$idpppd = $objpppd->create($user);
										if ($idpppd<=0)
										{
											$error++;
											setEventMessages($objpppd->error,$objpppd->errors,'errors');
										}
									}
								}
							}
						}
					}
					//actualizamos el idprev en la actividad
					if ($idact>0)
					{
						$objactivity->fetch($idact);
						$objactivity->fk_prev = $idprev;
						$objactivity->amount = $sumAmount;
						$resact = $objactivity->update($user);
						if ($resact <=0)
						{
							$error++;
							setEventMessages($objactivity->error,$objactivity->errors,'errors');
						}
					}
					if (!$error)
					{
						$res = $objPrev->fetch($idprev);
						if ($res > 0)
						{
							$objPrev->amount = $sumAmount;
							$res = $objPrev->update($user);
							if ($res <=0)
							{
								$error++;
								setEventMessages($obprev->error,$objPrev->errors,'errors');
							}
						}
						else
						{
							$error++;
							setEventMessages($obprev->error,$objPrev->errors,'errors');
						}
					}
				}
				*/
			}
			else
				$idprev = $objectadd->fk_poa_prev;

			if ($idprev && !$error)
			{
				$objPoapartidapredet = new Poapartidapredetext($db);
				//procedemos a cambiar a comprometido
				//recuperamos la lista de partidapre y partidapredet
				$objPrev->fetch($idprev);
				$objPrev->fetch_lines();
				$aPartidapredet = array();
				$aOriginid = array();
				$aOrigin = array();
				if (count($objPrev->lines)>0)
				{
					$lines = $objPrev->lines;
					//armamos un array de las cosas a adquirir
					foreach ($lines AS $j => $line)
					{
						//se cambio de S a N, N es cuando ya existe contrato
						$resd = $objPoapartidapredet->getlist($line->id,0,0,'N');
						foreach ((array) $objPoapartidapredet->array AS $k => $obj)
						{
							$aPartidapredet[$line->id][$obj->id]['rowid'] = $obj->id;
							$aPartidapredet[$line->id][$obj->id]['fk_poa_partida_pre'] = $obj->fk_poa_partida_pre;
							$aPartidapredet[$line->id][$obj->id]['fk_product'] = $obj->fk_product;
							$aPartidapredet[$line->id][$obj->id]['quant'] = $obj->quant;
							$aPartidapredet[$line->id][$obj->id]['amount_base'] = $obj->amount_base;
							$aPartidapredet[$line->id][$obj->id]['origin'] = $obj->origin;
							$aPartidapredet[$line->id][$obj->id]['originid'] = $obj->originid;
							$aOriginid[$obj->originid] = $line->id;
							$aOrigindetid[$obj->originid] = $obj->id;
							$aOrigin[$obj->originid] = $obj->origin;
						}
					}
				}
				//recuperamos las cantidades y valores contratados
				//$object->fetch_lines;
				$aComp = array();
				if (count($object->lines)>0)
				{
					//recorremos y armamos un aray para comparar con aPartidapredet
					foreach ($object->lines AS $j => $line)
					{
						//echo 'recuepracada linea '.$line->id;
						//buscamos su tabla adicional
						$resadd = $objectdetadd->fetch(0,$line->id);
						//echo '<hr>'.$aOrigindetid[$objectdetadd->fk_object].' '.$objectdetadd->fk_object;
						//print_r($aOrigindetid);
						//echo '<hr>condic '.$aOriginid[$objectdetadd->fk_object].' && '.$aOrigin[$objectdetadd->fk_object].' == '.$objectdetadd->object;
						if ($aOrigindetid[$objectdetadd->fk_object] && $aOrigin[$objectdetadd->fk_object] == $objectdetadd->object)
						{
							//echo '<hr>fkobject '.$objectdetadd->fk_object;
							//print_r($aPartidapredet);
							//echo '<hr>total '.$line->total_ttc;
							//echo '<hr>cond '.$line->total_ttc.' <= '.$aPartidapredet[$aOriginid[$objectdetadd->fk_object]][$aOrigindetid[$objectdetadd->fk_object]]['amount_base'];
							if ($line->total_ttc <= $aPartidapredet[$aOriginid[$objectdetadd->fk_object]][$aOrigindetid[$objectdetadd->fk_object]]['amount_base'])
							{
								$aComp[$aPartidapredet[$aOriginid[$objectdetadd->fk_object]][$aOrigindetid[$objectdetadd->fk_object]]['fk_poa_partida_pre']][$aOrigindetid[$objectdetadd->fk_object]]['quant'] = $line->qty;
								$aComp[$aPartidapredet[$aOriginid[$objectdetadd->fk_object]][$aOrigindetid[$objectdetadd->fk_object]]['fk_poa_partida_pre']][$aOrigindetid[$objectdetadd->fk_object]]['amount'] = $line->total_ttc;
								$aPartidacomp[$aPartidapredet[$aOriginid[$objectdetadd->fk_object]][$aOrigindetid[$objectdetadd->fk_object]]['fk_poa_partida_pre']]['amount']+= $line->total_ttc;
							}
							else
							{
								$error++;
								setEventMessages($langs->trans('El precio adjudicado es mayor al precio base del producto').' '.$line->detail,null,'errors');
							}
						}
						else
						{
							$error++;
							setEventMessages($langs->trans('No existe el producto/servicio o no existe saldo presupuestario'),null,'errors');
						}

					}
				}
				//primero creamos registro en tabla poa_process_contrat
				if (!$error)
				{
					$filterprocess = " AND t.fk_poa_prev =".$idprev;
					$res = $objProcess->fetchAll('','',0,0,array(1=>1),'AND',$filterprocess,true);
					if ($res<0)
					{
						$error=101;
						setEventMessages($objProcess->error,$objProcess->errors,'errors');
					}
					elseif($res == 1)
					{
						//buscamos
						$resc = $objPoaprocesscontrat->fetchAll('','',0,0,array(1=>1),'AND'," AND t.fk_poa_process = ".$objProcess->id." AND t.fk_contrat = ".$object->id,true);
						if ($resc == 1)
							$fk_process_contrat = $objPoaprocesscontrat->id;
						elseif ($resc < 0)
						{
							$error=1011;
							setEventMessages($objPoaprocesscontrat->error,$objPoaprocesscontrat->errors,'errors');
						}
						else
						{
							$objPoaprocesscontrat->fk_poa_process = $objProcess->id;
							$objPoaprocesscontrat->fk_contrat = $object->id;
							$objPoaprocesscontrat->date_create = $object->date;
							$objPoaprocesscontrat->fk_user_create = $user->id;
							$objPoaprocesscontrat->fk_user_mod = $user->id;
							$objPoaprocesscontrat->datem = $now;
							$objPoaprocesscontrat->tms = $now;
							$objPoaprocesscontrat->statut = 1;
							$fk_process_contrat = $objPoaprocesscontrat->create($user);
							if($fk_process_contrat<=0)
							{
								$error=102;
								setEventMessages($objPoaprocesscontrat->error,$objPoaprocesscontrat->errors,'errors');
							}
						}
						if (!$error)
						{
							//creamos en tabla poa_partida_com

							foreach ($aPartidacomp AS $i => $row)
							{
								//buscamos en poa_partida_pre
								$res = $objppp->fetch($i);
								if ($res==1)
								{
									//buscamos el comprometido
									$filterppc = " AND t.fk_poa_partida_pre = ".$i;
									$filterppc.= " AND t.fk_poa_prev = ".$idprev;
									$filterppc.= " AND t.fk_poa_partida_pre = ".$i;
									$filterppc.= " AND t.fk_structure = ".$objppp->fk_structure;
									$filterppc.= " AND t.fk_poa = ".$objppp->fk_poa;
									$filterppc.= " AND t.fk_contrat = ".$fk_process_contrat;
									$filterppc.= " AND t.partida = ".$objppp->partida;
									$resc = $objppc->fetchAll('','',0,0,array(1=>1),'AND',$filterppc,true);
									if ($resc == 1)
									{
										$resppc = $objppc->id;
										//actualizamos
										$objppc->amount = $row['amount'];
										$objppc->fk_user_mod = $user->id;
										$objppc->datem = $now;
										$objppc->tms = $now;
										$objppc->statut = 1;
										$objppc->active = 1;
										$resppc_ = $objppc->update($user);
										if ($resppc_<=0)
										{
											$error=103;
											setEventMessages($objppc->error,$objppc->errors,'errors');
										}
									}
									elseif($resc < 0)
									{
										$error=1021;
										setEventMessages($objppc->error,$objppc->errors,'errors');
									}
									else
									{
										$objppc->fk_poa_partida_pre = $i;
										$objppc->fk_poa_prev = $idprev;
										$objppc->fk_structure = $objppp->fk_structure;
										$objppc->fk_poa = $objppp->fk_poa;
										$objppc->fk_contrat = $fk_process_contrat;
										$objppc->fk_contrato = $object->id;
										$objppc->partida = $objppp->partida;
										$objppc->amount = $row['amount'];
										$objppc->fk_user_create = $user->id;
										$objppc->fk_user_mod = $user->id;
										$objppc->date_create = $now;
										$objppc->datec = $now;
										$objppc->datem = $now;
										$objppc->tms = $now;
										$objppc->statut = 1;
										$objppc->active = 1;
										$resppc = $objppc->create($user);
										if ($resppc<=0)
										{
											$error=103;
											setEventMessages($objppc->error,$objppc->errors,'errors');
										}
									}
								}
								else
								{
									$error=104;
									setEventMessages($objppp->error,$objppp->errors,'errors');
								}
								//procedemos a actualizar con el comprometido en la tabla poa_partida_pre_det
								$aData = $aComp[$i];
								foreach ($aData AS $j => $row)
								{
									//buscamos
									$res = $objPoapartidapredet->fetch($j);
									if ($res == 1)
									{
										$objPoapartidapredet->fk_contrat = $fk_process_contrat;
										$objPoapartidapredet->fk_contrato = $object->id;
										$objPoapartidapredet->fk_poa_partida_com = $resppc;
										$objPoapartidapredet->quant_adj = $row['quant'];
										$objPoapartidapredet->amount = $row['amount'];
										$objPoapartidapredet->fk_user_mod = $user->id;
										$objPoapartidapredet->datem = $now;
										$objPoapartidapredet->statut = 2;
										$resdet = $objPoapartidapredet->update($user);
										if ($resdet<=0)
										{
											$error=105;
											setEventMessages($objPoapartidapredet->error,$objPoapartidapredet->errors,'errors');
										}
									}
									else
									{
										$error=106;
										setEventMessages($objPoapartidapre->error,$objPoapartidapre->errors,'errors');
									}
								}
							}
						}
					}
				}
				//cambiamos de estado el preventivo
				//echo '<hr>errorfinal '.$error;exit;
				if (!$error)
				{
					$objPrev->statut = 2;
					$objPrev->fk_user_mod = $user->id;
					$objPrev->datem = $now;
					$res = $objPrev->update($user);
					if ($res <=0)
					{
						$error++;
						setEventMessages($objPrev->error,$objPrev->errors,'errors');
					}
				}
			}
		}
		//exit;
		//validacion
		if (!$error)
		{
			$object->date_commande=dol_now();
			$result = $object->valid($user);
			if ($result>= 0)
			{
				$db->commit();
				$action = '';
			// Define output language
				if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
				{
					$outputlangs = $langs;
					$newlang = '';
					if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
					if ($conf->global->MAIN_MULTILANGS && empty($newlang))	$newlang = $object->thirdparty->default_lang;
					if (! empty($newlang)) {
						$outputlangs = new Translate("", $conf);
						$outputlangs->setDefaultLang($newlang);
					}
					$model=$object->modelpdf;
					$ret = $object->fetch_($id);
				// Reload to get new records

					$result=$object->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);
					if ($result < 0) dol_print_error($db,$result);
				}
			}
			else
			{
				$action = '';
				$db->rollback();
				setEventMessages($object->error, $object->errors, 'errors');
			}

		// If we have permission, and if we don't need to provide the idwarehouse, we go directly on approved step
			if (empty($conf->global->SUPPLIER_ORDER_NO_DIRECT_APPROVE) && $user->rights->fournisseur->commande->approuver && ! (! empty($conf->global->STOCK_CALCULATE_ON_SUPPLIER_VALIDATE_ORDER) && $object->hasProductsOrServices(1)))
			{
				$action='confirm_approve';
			// can make standard or first level approval also if permission is set
			}
		}
	}

	if (($action == 'confirm_approve' || $action == 'confirm_approve2') && $confirm == 'yes' && $user->rights->fournisseur->commande->approuver)
	{
		$idwarehouse=GETPOST('idwarehouse', 'int');

		$qualified_for_stock_change=0;
		if (empty($conf->global->STOCK_SUPPORTS_SERVICES))
		{
			$qualified_for_stock_change=$object->hasProductsOrServices(2);
		}
		else
		{
			$qualified_for_stock_change=$object->hasProductsOrServices(1);
		}

		// Check parameters
		if (! empty($conf->stock->enabled) && ! empty($conf->global->STOCK_CALCULATE_ON_SUPPLIER_VALIDATE_ORDER) && $qualified_for_stock_change)	// warning name of option should be STOCK_CALCULATE_ON_SUPPLIER_APPROVE_ORDER
		{
			if (! $idwarehouse || $idwarehouse == -1)
			{
				$error++;
				setEventMessages($langs->trans('ErrorFieldRequired',$langs->transnoentitiesnoconv("Warehouse")), null, 'errors');
				$action='';
			}
		}

		if (! $error)
		{
			$result	= $object->approve($user, $idwarehouse, ($action=='confirm_approve2'?1:0));
			if ($result > 0)
			{
				if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE)) {
					$outputlangs = $langs;
					$newlang = '';
					if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
					if ($conf->global->MAIN_MULTILANGS && empty($newlang))	$newlang = $object->thirdparty->default_lang;
					if (! empty($newlang)) {
						$outputlangs = new Translate("", $conf);
						$outputlangs->setDefaultLang($newlang);
					}
					$object->generateDocument($object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
				}
				header("Location: ".$_SERVER["PHP_SELF"]."?id=".$object->id);
				exit;
			}
			else
			{
				setEventMessages($object->error, $object->errors, 'errors');
			}
		}
	}

	if ($action == 'confirm_refuse' &&	$confirm == 'yes' && $user->rights->fournisseur->commande->approuver)
	{
		$result = $object->refuse($user);
		if ($result > 0)
		{
			header("Location: ".$_SERVER["PHP_SELF"]."?id=".$object->id);
			exit;
		}
		else
		{
			setEventMessages($object->error, $object->errors, 'errors');
		}
	}

	if ($action == 'confirm_commande' && $confirm	== 'yes' &&	$user->rights->fournisseur->commande->commander)
	{
		$result = $object->commande($user, $_REQUEST["datecommande"],	$_REQUEST["methode"], $_REQUEST['comment']);
		if ($result > 0)
		{
			if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
			{
				$outputlangs = $langs;
				$newlang = '';
				if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
				if ($conf->global->MAIN_MULTILANGS && empty($newlang))	$newlang = $object->thirdparty->default_lang;
				if (! empty($newlang)) {
					$outputlangs = new Translate("", $conf);
					$outputlangs->setDefaultLang($newlang);
				}
				$object->generateDocument($object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
			}
			$action = '';
		}
		else
		{
			setEventMessages($object->error, $object->errors, 'errors');
		}
	}


	if ($action == 'confirm_delete' && $confirm == 'yes' && $user->rights->fournisseur->commande->supprimer)
	{
		$res = $objectadd->fetch(0,$object->id);
		$db->begin();
		if ($res>0)
		{
			$res = $objectadd->delete($user);
			if ($res <=0)
			{
				$error++;
				setEventMessages($objectadd->error,$objectadd->errors,'errors');
			}
		}
		//recorremos los items para anular en purchase request si existe
		$lines = $object->lines;
		$objPurchaserequestdet = new Purchaserequestdetext($db);
		foreach ((array) $lines AS $j => $line)
		{
			$res = $objectdetadd->fetch(0,$line->id);
			if ($res == 1)
			{
				if ($objectdetadd->object == 'purchaserequestdet')
				{
					$res = $objPurchaserequestdet->fetch($objectdetadd->fk_object);
					if ($res ==1)
					{
						$objPurchaserequestdet->fk_commande_fournisseurdet = 0;
						$objPurchaserequestdet->fk_user_mod = $user->id;
						$objPurchaserequestdet->datem = dol_now();
						$res = $objPurchaserequestdet->update_commandedet($user);
						if ($res <=0)
						{
							$error++;
							setEventMessages($objPurchaserequestdet->error,$objPurchaserequestdet->errors,'errors');
						}
					}
				}
				//borramos en objectdetadd
				$res = $objectdetadd->delete($user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($objectdetadd->error,$objectdetadd->errors,'errors');
				}
			}
		}
		if (!$error)
		{
			$result=$object->delete($user);
			if ($result <=0)
			{
				$error++;
				setEventMessages($object->error,$object->errors,'errors');
			}
		}

		if (!$error)
		{
			$db->commit();
			header("Location: ".DOL_URL_ROOT.'/purchase/commande/list.php');
			exit;
		}
		else
		{
			$db->rollback();
		}
		$action = '';
	}

	// Action clone object
	if ($action == 'confirm_clone' && $confirm == 'yes' && $user->rights->fournisseur->commande->creer)
	{
		if (1==0 && ! GETPOST('clone_content') && ! GETPOST('clone_receivers'))
		{
			setEventMessages($langs->trans("NoCloneOptionsSpecified"), null, 'errors');
		}
		else
		{
			if ($object->id > 0)
			{
				$result=$object->createFromClone();
				if ($result > 0)
				{
					header("Location: ".$_SERVER['PHP_SELF'].'?id='.$result);
					exit;
				}
				else
				{
					setEventMessages($object->error, $object->errors, 'errors');
					$action='';
				}
			}
		}
	}

	// Set status of reception (complete, partial, ...)
	if ($action == 'livraison' && $user->rights->fournisseur->commande->receptionner)
	{
		if (GETPOST("type") != '')
		{
			$subaction = GETPOST('subaction');
			if ($subaction)
			{
				$date_liv = dol_now();
				$_GET['comment'] = $langs->trans('Actualización del estado de forma automática');
			}
			else
				$date_liv = dol_mktime(GETPOST('rehour'),GETPOST('remin'),GETPOST('resec'),GETPOST("remonth"),GETPOST("reday"),GETPOST("reyear"));

			$result = $object->Livraison($user, $date_liv, GETPOST("type"), GETPOST("comment"));
			if ($result > 0)
			{
				$langs->load("deliveries");
				setEventMessages($langs->trans("DeliveryStateSaved"), null);
				$action = '';
			}
			else if($result == -3)
			{
				setEventMessages($object->error, $object->errors, 'errors');
			}
			else
			{
				setEventMessages($object->error, $object->errors, 'errors');
			}
		}
		else
		{
			setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentities("Delivery")), null, 'errors');
		}
	}

	if ($action == 'confirm_cancel' && $confirm == 'yes' &&	$user->rights->fournisseur->commande->commander)
	{
		$result	= $object->cancel($user);
		if ($result > 0)
		{
			header("Location: ".$_SERVER["PHP_SELF"]."?id=".$object->id);
			exit;
		}
		else
		{
			setEventMessages($object->error, $object->errors, 'errors');
		}
	}

	if ($action == 'builddoc' && $user->rights->fournisseur->commande->creer)
	{
		// Build document

		// Save last template used to generate document
		if (GETPOST('model')) $object->setDocModel($user, GETPOST('model','alpha'));

		$outputlangs = $langs;
		if (GETPOST('lang_id'))
		{
			$outputlangs = new Translate("",$conf);
			$outputlangs->setDefaultLang(GETPOST('lang_id'));
		}
		$result= $object->generateDocument($object->modelpdf,$outputlangs, $hidedetails, $hidedesc, $hideref);
		if ($result	<= 0)
		{
			setEventMessages($object->error, $object->errors, 'errors');
			$action='';
		}
	}

	// Delete file in doc form
	if ($action == 'remove_file' && $object->id > 0 && $user->rights->fournisseur->commande->creer)
	{
		require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
		$langs->load("other");
		$upload_dir =	$conf->fournisseur->commande->dir_output;
		$file =	$upload_dir	. '/' .	GETPOST('file');
		$ret=dol_delete_file($file,0,0,0,$object);
		if ($ret) setEventMessages($langs->trans("FileWasRemoved", GETPOST('urlfile')), null, 'mesgs');
		else setEventMessages($langs->trans("ErrorFailToDeleteFile", GETPOST('urlfile')), null, 'errors');
	}

	if ($action == 'update_extras')
	{
		// Fill array 'array_options' with data from add form
		$extralabels=$extrafields->fetch_name_optionals_label($object->table_element);
		$ret = $extrafields->setOptionalsFromPost($extralabels,$object,GETPOST('attribute'));
		if ($ret < 0) $error++;

		if (! $error)
		{
			// Actions on extra fields (by external module or standard code)
			// TODO le hook fait double emploi avec le trigger !!
			$hookmanager->initHooks(array('supplierorderdao'));
			$parameters=array('id'=>$object->id);

			$reshook=$hookmanager->executeHooks('insertExtraFields',$parameters,$object,$action); // Note that $action and $object may have been modified by some hooks

			if (empty($reshook))
			{
				if (empty($conf->global->MAIN_EXTRAFIELDS_DISABLED)) // For avoid conflicts if trigger used
				{
					$result=$object->insertExtraFields();

					if ($result < 0)
					{
						$error++;
					}

				}
			}
			else if ($reshook < 0) $error++;
		}
		else
		{
			$action = 'edit_extras';
		}
	}

	include DOL_DOCUMENT_ROOT.'/core/actions_printing.inc.php';


	/*
	 * Create an order
	 */
	if ($action == 'add' && $user->rights->fournisseur->commande->creer)
	{
		$now = dol_now();
		$error=0;
		$lSelectitem = false;
		$fk_type_adj = 0;
		$selreg = GETPOST('selreg');
		if ($socid <1)
		{
			setEventMessages($langs->trans('ErrorFieldRequired',$langs->transnoentities('Supplier')), null, 'errors');
			$action='create';
			$error++;
		}
		if (! $error)
		{
			$db->begin();

			// Creation commande
			$object->ref_supplier  	= GETPOST('refsupplier');
			$object->socid         	= $socid;
			$object->cond_reglement_id = GETPOST('cond_reglement_id');
			$object->mode_reglement_id = GETPOST('mode_reglement_id');
			$object->fk_account        = GETPOST('fk_account', 'int');
			$object->note_private	= GETPOST('note_private');
			$object->note_public   	= GETPOST('note_public');
			$object->date_livraison = $datelivraison;
			$object->fk_incoterms = GETPOST('incoterm_id', 'int');
			$object->total_ht = 0;
			$object->total_ttc = 0;
			$object->location_incoterms = GETPOST('location_incoterms', 'alpha');
			$object->multicurrency_code = GETPOST('multicurrency_code', 'alpha');
			$object->multicurrency_tx = GETPOST('originmulticurrency_tx', 'int');
			$object->fk_project       = GETPOST('projectid');

			//agregando a la tabla adicional
			$predef = '';
			$date_ini=dol_mktime(12, 0,0, GETPOST('date_ini'.$predef.'month'), GETPOST('date_ini'.$predef.'day'), GETPOST('date_ini'.$predef.'year'));
			$date_fin=dol_mktime(12, 0,0, GETPOST('date_fin'.$predef.'month'), GETPOST('date_fin'.$predef.'day'), GETPOST('date_fin'.$predef.'year'));

			$objectadd->ref_contrat = GETPOST('ref_contrat','alpha');
			$objectadd->term = GETPOST('term','int')+0;
			$objectadd->ref_term = GETPOST('ref_term','int')+0;
			$objectadd->type = GETPOST('type','alpha');
			$objectadd->advance = GETPOST('advance','int')+0;
			$objectadd->order_proceed = GETPOST('order_proceed','int')+0;
			$objectadd->designation_fiscal = GETPOST('designation_fiscal','int')+0;
			$objectadd->designation_supervisor = GETPOST('designation_supervisor','int')+0;
			$objectadd->date_ini = $date_ini;
			$objectadd->date_fin = $date_fin;
			$objectadd->code_facture = GETPOST('code_facture');
			$objectadd->code_type_purchase = GETPOST('code_type_purchase');
			$objectadd->delivery_place = GETPOST('delivery_place');
			$objectadd->discount = 0;
			$objectadd->datec = dol_now();
			$objectadd->fk_user_create = $user->id;
			$objectadd->fk_user_mod = $user->id;
			$objectadd->date_create = dol_now();
			$objectadd->date_mod = dol_now();
			$objectadd->tms = dol_now();
			$objectadd->status = 1;

			// Fill array 'array_options' with data from add form
			if (! $error)
			{
				$ret = $extrafields->setOptionalsFromPost($extralabels,$object);
				if ($ret < 0) $error++;
			}

			if ($conf->global->PURCHASE_ADD_DETAIL_CONTRAT)
			{
				//validación de campos
				if (empty($objectadd->ref_contrat))
				{
					$error++;
					setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Documentnumber")), null, 'errors');
				}
				if ($objectadd->type<0)
				{
					$error++;
					setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Type")), null, 'errors');
				}
				if (empty($objectadd->term))
				{
					$error++;
					setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Term")), null, 'errors');
				}
				if ($objectadd->ref_term<0)
				{
					$error++;
					setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Typeofterm")), null, 'errors');
				}

				if ($objectadd->date_ini<=0)
				{
					$error++;
					setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Dateini")), null, 'errors');
				}
				if ($objectadd->date_fin<=0)
				{
					$error++;
					setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Datefin")), null, 'errors');
				}
				if (empty($objectadd->delivery_place))
				{
					$error++;
					setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Deliveryplace")), null, 'errors');
				}
			}

			if (!$error)
			{
				// If creation from another object of another module (Example: origin=propal, originid=1)
				if (! empty($origin) && ! empty($originid))
				{
					if ($origin == 'order' || $origin == 'commande')
					{
						$element = $subelement = 'commande';
						$subelementobject='commandefournisseurdet';
					}
					elseif ($origin == 'purchaserequest')
					{
						$element = 'purchase';
						$subelement = 'purchaserequestext';
						$subelementobject='purchaserequestdet';
					}
					else
					{
						$element = 'supplier_proposal';
						$subelement = 'supplier_proposal';
						$element = 'purchase';
						$subelement = 'supplier_proposalext';
						$subelementobject='supplierproposaldet';
					}

					$object->origin = $origin;
					$object->origin_id = $originid;

					// Possibility to add external linked objects with hooks
					$object->linked_objects [$object->origin] = $object->origin_id;
					$other_linked_objects = GETPOST('other_linked_objects', 'array');
					if (! empty($other_linked_objects)) {
						$object->linked_objects = array_merge($object->linked_objects, $other_linked_objects);
					}

					$id = $object->create($user);
					if ($id > 0)
					{
						$objectadd->fk_object = $originid;
						$objectadd->object = $origin;
						$objectadd->fk_commande_fournisseur = $id;
						$objectadd->fk_departament = 0;
						$objectadd->fk_poa_prev = 0;
						$resadd = $objectadd->create($user);
						if ($resadd<=0)
						{
							setEventMessages($objectadd->error,$objectadd->errors,'errors');
							$error++;
						}
						dol_include_once('/' . $element . '/class/' . $subelement . '.class.php');

						$classname = 'SupplierProposal';
						if ($origin == 'purchaserequest') $classname = 'Purchaserequestext';
						$srcobject = new $classname($db);

						dol_syslog("Try to find source object origin=" . $object->origin . " originid=" . $object->origin_id . " to add lines");
						$result = $srcobject->fetch($object->origin_id);
						if ($origin == 'purchaserequest')
						{
							//cambiamos de estado_purchase
							$srcobject->status_purchase=1;
							$ressrc = $srcobject->update($user);
							if ($ressrc<=0)
							{
								$error++;
								setEventMessages($srcobject->error,$srcobject->errors,'errors');
							}
						}
						if ($result > 0)
						{
							//verificamos el tipo de adjudicacion

							if ($conf->global->PURCHASE_INTEGRATED_POA && $conf->poa->enabled)
							{
								if ($srcobject->fk_poa_prev)
								{
									$elementpoa = 'poa';
									$subelementpoaprocess = 'poaprocessext';
									dol_include_once('/' . $elementpoa . '/class/' . $subelementpoaprocess . '.class.php');
									$classname = 'Poaprocessext';
									$objectpoasrc = new $classname($db);
									$respoa = $objectpoasrc->fetchAll('','',0,0,array(1=>1),'AND'," AND fk_poa_prev = ".$srcobject->fk_poa_prev,true);
									if ($respoa>0)
									{
										if ($objectpoasrc->fk_type_adj != 3) $lSelectitem = true;
										$fk_type_adj = $objectpoasrc->fk_type_adj;
									}
								}
							}

							if ($srcobject->fk_departament)
							{
								//actualizamos en objectadd
								$restmp = $objectadd->fetch($resadd);
								if ($restmp==1)
								{
									$objectadd->fk_poa_prev = $srcobject->fk_poa_prev+0;
									$objectadd->fk_departament = $srcobject->fk_departament+0;
									$restmp = $objectadd->update($user);
									if ($restmp <=0)
									{
										$error++;
										setEventMessages($objectadd->error,$objectadd->errors,'errors');
									}
								}
							}
							$object->set_date_livraison($user, $srcobject->date_livraison);
							$object->set_id_projet($user, $srcobject->fk_project);

							$lines = $srcobject->lines;
							if (empty($lines) && method_exists($srcobject, 'fetch_linesadd'))
							{
								$srcobject->fetch_linesadd();
								$lines = $srcobject->lines;
							}
							if (empty($lines) && method_exists($srcobject, 'fetch_lines'))
							{
								$srcobject->fetch_lines();
								$lines = $srcobject->lines;
							}
							$fk_parent_line = 0;
							$num = count($lines);

							$productsupplier = new ProductFournisseur($db);
							for($i = 0; $i < $num; $i ++)
							{
								//if (empty($lines[$i]->subprice) || $lines[$i]->qty <= 0)
								if ($lines[$i]->qty <= 0)
									continue;
								if ($lSelectitem)
								{
									if (count($selreg)<=0)
									{
										$error=51;
										setEventMessages($langs->trans('No existe seleccion de productos'),null,'errors');
									}
									else
									{
										if($selreg[$lines[$i]->id] != 'on')
											continue;
									}
								}
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
									$lines[$i]->fetch_optionals($lines[$i]->id);
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
										$resul=$object->addlineadd(
											$desc,
											$lines[$i]->subprice,
											//$productsupplier->fourn_pu,
											$lines[$i]->qty,
											$lines[$i]->tva_tx,
											$lines[$i]->localtax1_tx,
											$lines[$i]->localtax2_tx,
											$lines[$i]->fk_product,
											$idprodfournprice,
											$fourn_ref,
											$lines[$i]->remise_percent,
											$price_base_type,
											$lines[$i]->price,
											$type,
											$tva_npr,
											'',
											$date_start,
											$date_end,
											$array_options,
											$lines[$i]->fk_unit,
											$lines[$i]
										);
										if ($resul <= 0)
										{
											$error++;
											setEventMessages($objectc->error,$object->errors,'errors');
										}
										else
										{
												//creamos el registro adicional
											$objectdetadd->initAsSpecimen();
											$objectdetadd->fk_commande_fournisseurdet = $resul;
											$objectdetadd->fk_object = $lines[$i]->id+0;
											$objectdetadd->object = $subelementobject;
											$objectdetadd->fk_fabrication = $lines[$i]->fk_fabrication+0;
											$objectdetadd->fk_fabricationdet = $lines[$i]->fk_fabricationdet+0;
											$objectdetadd->fk_projet = $lines[$i]->fk_projet+0;
											$objectdetadd->fk_projet_task = $lines[$i]->fk_projet_task+0;
											$objectdetadd->fk_jobs = $lines[$i]->fk_jobs+0;
											$objectdetadd->fk_jobsdet = $lines[$i]->fk_jobsdet+0;
											$objectdetadd->fk_structure = $lines[$i]->fk_structure+0;
											$objectdetadd->fk_poa = $lines[$i]->fk_poa+0;
											$objectdetadd->partida = $lines[$i]->partida;
											$objectdetadd->amount_ice = $lines[$i]->amount_ice+0;
											$objectdetadd->discount = $lines[$i]->discount+0;;
											$objectdetadd->fk_user_create = $user->id;
											$objectdetadd->fk_user_mod = $user->id;
											$objectdetadd->datec = $now;
											$objectdetadd->datem = $now;
											$objectdetadd->tms = $now;
											$objectdetadd->status = 1;
												//echo '<hr>resdetadd '.
											$resdetadd = $objectdetadd->create($user);
											if ($resdetadd<=0)
											{
												$error++;
												setEventMessages($objectdetadd->error, $objectdetadd->errors,'errors');
											}

											if (!$error)
											{
												//actualizamos en purchase_requestdet
												$objPurchaserequestdet = new Purchaserequestdetext($db);
												$resrdet = $objPurchaserequestdet->fetch($lines[$i]->id);
												if ($resrdet==1)
												{
													$objPurchaserequestdet->fk_commande_fournisseurdet = $resul;
													$objPurchaserequestdet->fk_user_mod = $user->id;
													$objPurchaserequestdet->datem = $now;
													$resrdet = $objPurchaserequestdet->update_commandedet($user);

													if ($resrdet <=0)
													{
														$error++;
														setEventMessages($objPurchaserequestdet->error,$objPurchaserequestdet->errors,'errors');
													}
												}
											}
										}
									}
									else
									{
										$resul = $object->addline(
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

							// Add link between elements


							// Hooks
							$parameters = array('objFrom' => $srcobject);
							$reshook = $hookmanager->executeHooks('createFrom', $parameters, $object, $action); // Note that $action and $object may have been

							if ($reshook < 0)
								$error ++;
						} else {
							setEventMessages($srcobject->error, $srcobject->errors, 'errors');
							$error ++;
						}
					} else {
						setEventMessages($object->error, $object->errors, 'errors');
						$error ++;
					}
				}
				else
				{
					$id = $object->create($user);
					$objectadd->fk_commande_fournisseur = $id;
					$resadd = $objectadd->create($user);
					if ($resadd<=0)
					{
						setEventMessages($objectadd->error,$objectadd->errors,'errors');
						$error++;
					}
					if ($id < 0)
					{
						$error++;
						setEventMessages($object->error, $object->errors, 'errors');
					}
				}
			}
			//calculamos el total
			if (!$error)
			{
				$error = update_total_commande($id);
			}

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

	/*
	 * Send mail
	 */

	// Actions to send emails
	$actiontypecode='AC_SUP_ORD';
	$trigger_name='ORDER_SUPPLIER_SENTBYMAIL';
	$paramname='id';
	$mode='emailfromsupplierorder';
	include DOL_DOCUMENT_ROOT.'/core/actions_sendmails.inc.php';


	if ($action == 'webservice' && GETPOST('mode', 'alpha') == "send" && ! GETPOST('cancel'))
	{
		$ws_url         = $object->thirdparty->webservices_url;
		$ws_key         = $object->thirdparty->webservices_key;
		$ws_user        = GETPOST('ws_user','alpha');
		$ws_password    = GETPOST('ws_password','alpha');
		$ws_entity      = GETPOST('ws_entity','int');
		$ws_thirdparty  = GETPOST('ws_thirdparty','int');

		// NS and Authentication parameters
		$ws_ns='http://www.dolibarr.org/ns/';
		$ws_authentication=array(
			'dolibarrkey'=>$ws_key,
			'sourceapplication'=>'DolibarrWebServiceClient',
			'login'=>$ws_user,
			'password'=>$ws_password,
			'entity'=>$ws_entity
		);

		//Is sync supplier web services module activated? and everything filled?
		if (empty($conf->syncsupplierwebservices->enabled)) {
			setEventMessages($langs->trans("WarningModuleNotActive",$langs->transnoentities("Module2650Name")), null, 'mesgs');
		} else if (empty($ws_url) || empty($ws_key)) {
			setEventMessages($langs->trans("ErrorWebServicesFieldsRequired"), null, 'errors');
		} else if (empty($ws_user) || empty($ws_password) || empty($ws_thirdparty)) {
			setEventMessages($langs->trans("ErrorFieldsRequired"),null, 'errors');
		}
		else
		{
			//Create SOAP client and connect it to order
			$soapclient_order = new nusoap_client($ws_url."/webservices/server_order.php");
			$soapclient_order->soap_defencoding='UTF-8';
			$soapclient_order->decodeUTF8(false);

			//Create SOAP client and connect it to product/service
			$soapclient_product = new nusoap_client($ws_url."/webservices/server_productorservice.php");
			$soapclient_product->soap_defencoding='UTF-8';
			$soapclient_product->decodeUTF8(false);

			//Prepare the order lines from order
			$order_lines = array();
			foreach ($object->lines as $line)
			{
				$ws_parameters = array('authentication' => $ws_authentication, 'id' => '', 'ref' => $line->ref_supplier);
				$result_product = $soapclient_product->call("getProductOrService", $ws_parameters, $ws_ns, '');

				if ($result_product["result"]["result_code"] == "OK")
				{
					$order_lines[] = array(
						'desc'          => $line->product_desc,
						'type'          => $line->product_type,
						'product_id'    => $result_product["product"]["id"],
						'vat_rate'      => $line->tva_tx,
						'qty'           => $line->qty,
						'price'         => $line->price,
						'unitprice'     => $line->subprice,
						'total_net'     => $line->total_ht,
						'total_vat'     => $line->total_tva,
						'total'         => $line->total_ttc,
						'date_start'    => $line->date_start,
						'date_end'      => $line->date_end,
					);
				}
			}

			//Prepare the order header
			$order = array(
				'thirdparty_id' => $ws_thirdparty,
				'date'          => dol_print_date(dol_now(),'dayrfc'),
				'total_net'     => $object->total_ht,
				'total_var'     => $object->total_tva,
				'total'         => $object->total_ttc,
				'lines'         => $order_lines
			);

			$ws_parameters = array('authentication'=>$ws_authentication, 'order' => $order);
			$result_order = $soapclient_order->call("createOrder", $ws_parameters, $ws_ns, '');

			if (empty($result_order["result"]["result_code"])) //No result, check error str
			{
				setEventMessages($langs->trans("SOAPError")." '".$soapclient_order->error_str."'", null, 'errors');
			}
			else if ($result_order["result"]["result_code"] != "OK") //Something went wrong
			{
				setEventMessages($langs->trans("SOAPError")." '".$result_order["result"]["result_code"]."' - '".$result_order["result"]["result_label"]."'", null, 'errors');
			}
			else
			{
				setEventMessages($langs->trans("RemoteOrderRef")." ".$result_order["ref"], null, 'mesgs');
			}
		}
	}

	if (! empty($conf->global->MAIN_DISABLE_CONTACTS_TAB) && $user->rights->fournisseur->commande->creer)
	{
		if ($action == 'addcontact')
		{
			if ($object->id > 0)
			{
				$contactid = (GETPOST('userid') ? GETPOST('userid') : GETPOST('contactid'));
				$result = $object->add_contact($contactid, $_POST["type"], $_POST["source"]);
			}

			if ($result >= 0)
			{
				header("Location: ".$_SERVER['PHP_SELF']."?id=".$object->id);
				exit;
			}
			else
			{
				if ($object->error == 'DB_ERROR_RECORD_ALREADY_EXISTS')
				{
					$langs->load("errors");
					setEventMessages($langs->trans("ErrorThisContactIsAlreadyDefinedAsThisType"), null, 'errors');
				}
				else
				{
					setEventMessages($object->error, $object->errors, 'errors');
				}
			}
		}

		// bascule du statut d'un contact
		else if ($action == 'swapstatut' && $object->id > 0)
		{
			$result=$object->swapContactStatus(GETPOST('ligne'));
		}

		// Efface un contact
		else if ($action == 'deletecontact' && $object->id > 0)
		{
			$result = $object->delete_contact($_GET["lineid"]);

			if ($result >= 0)
			{
				header("Location: ".$_SERVER['PHP_SELF']."?id=".$object->id);
				exit;
			}
			else {
				dol_print_error($db);
			}
		}
	}
}


/*
 * View
 */
$title = $langs->trans('Order');
$morejs = array('/purchase/js/purchase.js');
$morecss = array('/purchase/css/style.css','/purchase/css/bootstrap.min.css','/includes/jquery/plugins/datatables/media/css/dataTables.bootstrap.css','/includes/jquery/plugins/datatables/media/css/jquery.dataTables.css',);
$morecss = array('/purchase/css/style.css','/includes/jquery/plugins/datatables/media/css/dataTables.bootstrap.css','/includes/jquery/plugins/datatables/media/css/jquery.dataTables.css',);
llxHeader('',$title,'','','','',$morejs,$morecss,0,0);


$form =	new	Formv($db);
$getUtil =	new	getUtil($db);
//$formf = new Formfad_d($db);
$formfile = new FormFile($db);
$formorder = new FormOrder($db);
$productstatic = new Product($db);


/* *************************************************************************** */
/*                                                                             */
/* Mode vue et edition                                                         */
/*                                                                             */
/* *************************************************************************** */

$now=dol_now();
if ($action=='create')
{
	print load_fiche_titre($langs->trans('NewOrder'));

	dol_htmloutput_events();

	$societe='';
	if ($socid>0)
	{
		$societe=new Societe($db);
		$societe->fetch($socid);
	}

	if (! empty($origin) && ! empty($originid))
	{
		$lViewdet = 0;
		// Parse element/subelement (ex: project_task)
		$element = $subelement = $origin;
		if (preg_match('/^([^_]+)_([^_]+)/i', $origin, $regs)) {
			$element = $regs [1];
			$subelement = $regs [2];
		}
		if ($origin == 'purchaserequest')
		{
			$lViewdet = 1;
			$element = 'purchase';
			$subelement = 'purchaserequestext';
			//elementos para poa
			if ($conf->global->PURCHASE_INTEGRATED_POA)
			{
				if ($conf->poa->enabled)
				{
					$elementpoa = 'poa';
					$subelementpoa='poaprevext';
					$subelementpoaprocess='poaprocessext';
				}
			}
		}
		else
		{
			$element = 'supplier_proposal';
			$subelement = 'supplier_proposal';
		}
		dol_include_once('/' . $element . '/class/' . $subelement . '.class.php');

		$classname = 'SupplierProposal';
		if ($origin == 'purchaserequest') $classname = 'Purchaserequestext';

		$objectsrc = new $classname($db);
		$objectsrc->fetch($originid);
		if (empty($objectsrc->lines) && method_exists($objectsrc, 'fetch_lines'))
			$objectsrc->fetch_lines();
		$objectsrc->fetch_thirdparty();

		if ($conf->global->PURCHASE_INTEGRATED_POA)
		{
			if ($conf->poa->enabled)
			{
				if ($objectsrc->fk_poa_prev)
				{
					dol_include_once('/' . $elementpoa . '/class/' . $subelementpoaprocess . '.class.php');
					$classname = 'Poaprocessext';
					$objectpoasrc = new $classname($db);
					$respoa = $objectpoasrc->fetchAll('','',0,0,array(1=>1),'AND'," AND fk_poa_prev = ".$objectsrc->fk_poa_prev,true);
					if ($respoa>0)
					{
						if ($objectpoasrc->fk_type_adj != 3) $lViewdet = 2;
						$objectsrc->fk_type_adj = $objectpoasrc->fk_type_adj;
					}
				}
			}
		}
		// Replicate extrafields
		$objectsrc->fetch_optionals($originid);
		$object->array_options = $objectsrc->array_options;

		$projectid = (! empty($objectsrc->fk_project) ? $objectsrc->fk_project : '');
		$ref_client = (! empty($objectsrc->ref_client) ? $objectsrc->ref_client : '');

		$soc = $objectsrc->client;
		$cond_reglement_id	= (!empty($objectsrc->cond_reglement_id)?$objectsrc->cond_reglement_id:(!empty($soc->cond_reglement_id)?$soc->cond_reglement_id:1));
		$mode_reglement_id	= (!empty($objectsrc->mode_reglement_id)?$objectsrc->mode_reglement_id:(!empty($soc->mode_reglement_id)?$soc->mode_reglement_id:0));
		$fk_account         = (! empty($objectsrc->fk_account)?$objectsrc->fk_account:(! empty($soc->fk_account)?$soc->fk_account:0));
		$availability_id	= (!empty($objectsrc->availability_id)?$objectsrc->availability_id:(!empty($soc->availability_id)?$soc->availability_id:0));
		$shipping_method_id = (! empty($objectsrc->shipping_method_id)?$objectsrc->shipping_method_id:(! empty($soc->shipping_method_id)?$soc->shipping_method_id:0));
		$demand_reason_id	= (!empty($objectsrc->demand_reason_id)?$objectsrc->demand_reason_id:(!empty($soc->demand_reason_id)?$soc->demand_reason_id:0));
		$remise_percent		= (!empty($objectsrc->remise_percent)?$objectsrc->remise_percent:(!empty($soc->remise_percent)?$soc->remise_percent:0));
		$remise_absolue		= (!empty($objectsrc->remise_absolue)?$objectsrc->remise_absolue:(!empty($soc->remise_absolue)?$soc->remise_absolue:0));
		$dateinvoice		= empty($conf->global->MAIN_AUTOFILL_DATE)?-1:'';
		$datedelivery = (! empty($objectsrc->date_livraison) ? $objectsrc->date_livraison : '');

		if (!empty($conf->multicurrency->enabled))
		{
			if (!empty($objectsrc->multicurrency_code)) $currency_code = $objectsrc->multicurrency_code;
			if (!empty($conf->global->MULTICURRENCY_USE_ORIGIN_TX) && !empty($objectsrc->multicurrency_tx))	$currency_tx = $objectsrc->multicurrency_tx;
		}

		$note_private = $object->getDefaultCreateValueFor('note_private', (! empty($objectsrc->note_private) ? $objectsrc->note_private : null));
		$note_public = $object->getDefaultCreateValueFor('note_public', (! empty($objectsrc->note_public) ? $objectsrc->note_public : null));

		// Object source contacts list
		$srccontactslist = $objectsrc->liste_contact(- 1, 'external', 1);

	}
	else
	{
		$cond_reglement_id 	= $societe->cond_reglement_supplier_id;
		$mode_reglement_id 	= $societe->mode_reglement_supplier_id;

		if (!empty($conf->multicurrency->enabled) && !empty($soc->multicurrency_code)) $currency_code = $soc->multicurrency_code;

		$note_private = $object->getDefaultCreateValueFor('note_private');
		$note_public = $object->getDefaultCreateValueFor('note_public');
	}
	if (! empty($conf->use_javascript_ajax))
	{
		print "\n".'<script type="text/javascript">';
		print '$(document).ready(function () {
			id_te_private=8;
			id_ef15=1;
			is_private='.$private.';
			if (is_private) {
				$(".datfiscal").show();
				$(".datnfiscal").hide();
			} else {
				$(".datfiscal").hide();
				$(".datnfiscal").show();
			}

			$("#code_facture").change(function() {
				document.add.action.value="create";
				document.add.submit();
			});
		});';
		print '</script>'."\n";
	}
	print '<form name="add" action="'.$_SERVER["PHP_SELF"].'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="socid" value="' . $soc->id . '">' . "\n";
	print '<input type="hidden" name="remise_percent" value="' . $soc->remise_percent . '">';
	print '<input type="hidden" name="origin" value="' . $origin . '">';
	print '<input type="hidden" name="originid" value="' . $originid . '">';
	if (!empty($currency_tx)) print '<input type="hidden" name="originmulticurrency_tx" value="' . $currency_tx . '">';

	dol_fiche_head('');

	print '<table class="border" width="100%">';

	// Ref
	print '<tr><td class="titlefieldcreate">'.$langs->trans('Ref').'</td><td>'.$langs->trans('Draft').'</td></tr>';

	if ($conf->global->PURCHASE_ADD_DETAIL_CONTRAT)
	{
		// Ref contrat
		print '<tr><td class="fieldrequired">'.$langs->trans('Documentnumber').'</td><td><input name="ref_contrat" type="text" value="'.GETPOST('ref_contrat').'" required></td>';
		print '</tr>';
		// type contrat
		print '<tr><td class="fieldrequired">'.$langs->trans('Documenttype').'</td><td>';
		print $form->selectarray('type',$aArraytype,GETPOST('type'),1);
		print '</td>';
		print '</tr>';
		// term
		print '<tr><td class="fieldrequired">'.$langs->trans('Term').'</td><td><input name="term" type="text" value="'.GETPOST('term').'" required></td>';
		print '</tr>';
		// ref term
		print '<tr><td class="fieldrequired">'.$langs->trans('Typeofterm').'</td><td>';
		print $form->selectarray('ref_term',$aTerm,GETPOST('ref_term'),1);
		print '</td>';
		print '</tr>';
		// advance
		print '<tr><td >'.$langs->trans('Advancepayment').'</td><td>';
		print $form->selectyesno('advance',GETPOST('advance'),1);
		print '</td>';
		print '</tr>';
		// order proced
		print '<tr><td>'.$langs->trans('Ordertoproceed').'</td><td>';
		print $form->selectyesno('order_proceed',GETPOST('order_proceed'),1);
		print '</td>';
		print '</tr>';
		// designation fiscal
		print '<tr><td>'.$langs->trans('Designationfiscal').'</td><td>';
		print $form->selectyesno('designation_fiscal',GETPOST('designation_fiscal'),1);
		print '</td>';
		print '</tr>';
		// designation supervisor
		print '<tr><td>'.$langs->trans('Designationsupervisor').'</td><td>';
		print $form->selectyesno('designation_supervisor',GETPOST('designation_supervisor'),1);
		print '</td>';
		print '</tr>';
		// date ini
		print '<tr><td class="fieldrequired">'.$langs->trans('Dateini').'</td><td>';
		print $form->select_date($date_ini,'date_ini',0,0);
		print '</td>';
		print '</tr>';
		// date fin
		print '<tr><td class="fieldrequired">'.$langs->trans('Datefin').'</td><td>';
		print $form->select_date($date_fin,'date_fin',0,0);
		print '</td>';
		print '</tr>';
		// Lugar de entrega
		print '<tr><td class="fieldrequired">'.$langs->trans('Deliveryplace').'</td><td>';
		print '<input type="text" name="delivery_place" value="'.GETPOST('delivery_place').'" required>';
		print '</td>';
		print '</tr>';
	}

	// code facture
	print '<tr><td class="fieldrequired">'.$langs->trans('Typefiscal').'</td><td colspan="2">';
	//print $form->load_type_facture('type_facture', -1, $seller, $buyer);
	$typefilter = 0;
	if (empty($code_facture)) $code_facture = ($conf->global->FISCAL_CODE_FACTURE_PURCHASE?$conf->global->FISCAL_CODE_FACTURE_PURCHASE:'STDC');
		print $form->load_type_facture('code_facture',(GETPOST('code_facture')?GETPOST('code_facture'):$code_facture),0, 'code', false,$typefilter);
	//load_tvaadd('tva_tx', (isset($_POST["tva_tx"])?$_POST["tva_tx"]:-1), $seller, $buyer);
		print '</td></tr>';

	//code type facture
		print '<tr><td class="fieldrequired">'.$langs->trans('Purchasedestination').'</td><td colspan="2">';
		print $form->load_type_purchase('code_type_purchase',(GETPOST('code_type_purchase')?GETPOST('code_type_purchase'):$conf->global->PURCHASE_DESTINATION_CODE_PRODUCT),0, 'code', false);
		print '</td></tr>';

	// Third party
		print '<tr><td class="fieldrequired">'.$langs->trans('Supplier').'</td>';
		print '<td>';

		if ($socid > 0)
		{
			print $societe->getNomUrl(1);
			print '<input type="hidden" name="socid" value="'.$socid.'">';
		}
		else
		{
			$filtertype = 's.client = 1 OR s.client = 3';
			$filtertype = 's.fournisseur = 1';
		//print $form->select_company_v('', 'socid', $filtertype, 0, 0, 1, 2, '', 1, array(),0,'','');
			print $form->select_company(GETPOST('socid','int'), 'socid', 's.fournisseur = 1', 'SelectThirdParty');

		}
		print '</td>';

	// Ref supplier
		print '<tr><td>'.$langs->trans('RefSupplier').'</td><td><input name="refsupplier" type="text"></td>';
		print '</tr>';

		print '</td></tr>';

	// Payment term
		print '<tr><td class="nowrap">'.$langs->trans('PaymentConditionsShort').'</td><td colspan="2">';
		$form->select_conditions_paiements(isset($_POST['cond_reglement_id'])?$_POST['cond_reglement_id']:$cond_reglement_id,'cond_reglement_id');
		print '</td></tr>';

	// Payment mode
		print '<tr><td>'.$langs->trans('PaymentMode').'</td><td colspan="2">';
		$form->select_types_paiements(isset($_POST['mode_reglement_id'])?$_POST['mode_reglement_id']:$mode_reglement_id,'mode_reglement_id');
		print '</td></tr>';

	// Planned delivery date
		print '<tr><td>';
		print $langs->trans('DateDeliveryPlanned');
		print '</td>';
		print '<td>';
		$usehourmin=0;
		if (! empty($conf->global->SUPPLIER_ORDER_USE_HOUR_FOR_DELIVERY_DATE)) $usehourmin=1;
		$form->select_date($datelivraison?$datelivraison:-1,'liv_',$usehourmin,$usehourmin,'',"set");
		print '</td></tr>';

	// Bank Account
		if (! empty($conf->global->BANK_ASK_PAYMENT_BANK_DURING_SUPPLIER_ORDER) && ! empty($conf->banque->enabled))
		{
			$langs->load("bank");
			print '<tr><td>' . $langs->trans('BankAccount') . '</td><td colspan="2">';
			$form->select_comptes($fk_account, 'fk_account', 0, '', 1);
			print '</td></tr>';
		}

	// Project
		if (! empty($conf->projet->enabled))
		{
			$formproject = new FormProjets($db);

			$langs->load('projects');
			print '<tr><td>' . $langs->trans('Project') . '</td><td colspan="2">';
			$formproject->select_projects((empty($conf->global->PROJECT_CAN_ALWAYS_LINK_TO_ALL_SUPPLIERS)?$societe->id:-1), $projectid, 'projectid', 0, 0, 1, 1);
			print '</td></tr>';
		}

	// Incoterms
		if (!empty($conf->incoterm->enabled))
		{
			print '<tr>';
			print '<td><label for="incoterm_id">'.$form->textwithpicto($langs->trans("IncotermLabel"), $object->libelle_incoterms, 1).'</label></td>';
			print '<td colspan="3" class="maxwidthonsmartphone">';
			print $form->select_incoterms((!empty($object->fk_incoterms) ? $object->fk_incoterms : ''), (!empty($object->location_incoterms)?$object->location_incoterms:''));
			print '</td></tr>';
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

		print '<tr><td>'.$langs->trans('NotePublic').'</td>';
		print '<td>';
		$doleditor = new DolEditor('note_public', isset($note_public) ? $note_public : GETPOST('note_public'), '', 80, 'dolibarr_notes', 'In', 0, false, true, ROWS_3, 70);
		print $doleditor->Create(1);
		print '</td>';
	//print '<textarea name="note_public" wrap="soft" cols="60" rows="'.ROWS_5.'"></textarea>';
		print '</tr>';

		print '<tr><td>'.$langs->trans('NotePrivate').'</td>';
		print '<td>';
		$doleditor = new DolEditor('note_private', isset($note_private) ? $note_private : GETPOST('note_private'), '', 80, 'dolibarr_notes', 'In', 0, false, true, ROWS_3, 70);
		print $doleditor->Create(1);
		print '</td>';
	//print '<td><textarea name="note_private" wrap="soft" cols="60" rows="'.ROWS_5.'"></textarea></td>';
		print '</tr>';

		if (! empty($origin) && ! empty($originid) && is_object($objectsrc)) {

			print "\n<!-- " . $classname . " info -->";
			print "\n";
			print '<input type="hidden" name="amount"         value="' . $objectsrc->total_ht . '">' . "\n";
			print '<input type="hidden" name="total"          value="' . $objectsrc->total_ttc . '">' . "\n";
			print '<input type="hidden" name="tva"            value="' . $objectsrc->total_tva . '">' . "\n";
			print '<input type="hidden" name="origin"         value="' . $objectsrc->element . '">';
			print '<input type="hidden" name="originid"       value="' . $objectsrc->id . '">';

			$newclassname = $classname;
			print '<tr><td>' . $langs->trans($newclassname) . '</td><td colspan="2">' . $objectsrc->getNomUrl(1) . '</td></tr>';
			print '<tr><td>' . $langs->trans('TotalHT') . '</td><td colspan="2">' . price($objectsrc->total_ht) . '</td></tr>';
			print '<tr><td>' . $langs->trans('TotalVAT') . '</td><td colspan="2">' . price($objectsrc->total_tva) . "</td></tr>";
		if ($mysoc->localtax1_assuj == "1" || $objectsrc->total_localtax1 != 0) 		// Localtax1 RE
		{
			print '<tr><td>' . $langs->transcountry("AmountLT1", $mysoc->country_code) . '</td><td colspan="2">' . price($objectsrc->total_localtax1) . "</td></tr>";
		}

		if ($mysoc->localtax2_assuj == "1" || $objectsrc->total_localtax2 != 0) 		// Localtax2 IRPF
		{
			print '<tr><td>' . $langs->transcountry("AmountLT2", $mysoc->country_code) . '</td><td colspan="2">' . price($objectsrc->total_localtax2) . "</td></tr>";
		}

		print '<tr><td>' . $langs->trans('TotalTTC') . '</td><td colspan="2">' . price($objectsrc->total_ttc) . "</td></tr>";

		if (!empty($conf->multicurrency->enabled))
		{
			print '<tr><td>' . $langs->trans('MulticurrencyTotalHT') . '</td><td colspan="2">' . price($objectsrc->multicurrency_total_ht) . '</td></tr>';
			print '<tr><td>' . $langs->trans('MulticurrencyTotalVAT') . '</td><td colspan="2">' . price($objectsrc->multicurrency_total_tva) . '</td></tr>';
			print '<tr><td>' . $langs->trans('MulticurrencyTotalTTC') . '</td><td colspan="2">' . price($objectsrc->multicurrency_total_ttc) . '</td></tr>';
		}
	}

	// Other options
	$parameters=array();
	$reshook=$hookmanager->executeHooks('formObjectOptions',$parameters,$object,$action); // Note that $action and $object may have been modified by hook

	if (empty($reshook) && ! empty($extrafields->attribute_label))
	{
		print $object->showOptionals($extrafields,'edit');
	}

	// Bouton "Create Draft"
	print "</table>\n";

	dol_fiche_end();

	if ($lViewdet == 2)
	{
		if (! empty($origin) && ! empty($originid) && is_object($objectsrc))
		{
			$title = $langs->trans('ProductsAndServices');
			print load_fiche_titre($title);

			print '<table class="noborder" width="100%">';
			$objectsrc->printOriginLinesList();

			print '</table>';
		}
	}

	print '<div class="center">';
	print '<input type="submit" class="button" name="bouton" value="'.$langs->trans('CreateDraft').'">';
	print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	print '<input type="button" class="button" value="' . $langs->trans("Cancel") . '" onClick="javascript:history.go(-1)">';
	print '</div>';

	print "</form>\n";

	// Show origin lines
	if (! empty($origin) && ! empty($originid) && is_object($objectsrc))
	{
		if ($lViewdet != 2)
		{
			$title = $langs->trans('ProductsAndServices');
			print load_fiche_titre($title);

			print '<table class="noborder" width="100%">';
			$objectsrc->printOriginLinesList();

			print '</table>';
		}
	}
}
elseif (! empty($object->id))
{
	$objectadd->fetch(0,$object->id);
	$societe = new Fournisseur($db);
	$result=$societe->fetch($object->socid);
	if ($result < 0) dol_print_error($db);

	//agregamos variables de objectadd a object
	$object->fk_departament = $objectadd->fk_departament+0;
	$object->fk_poa = $objectadd->fk_poa+0;

	$author	= new User($db);
	$author->fetch($object->user_author_id);

	$res=$object->fetch_optionals($object->id,$extralabels);

	$head = purchase_prepare_head($object);

	$title=$langs->trans("SupplierOrder");
	dol_fiche_head($head, 'card', $title, 0, 'order');


	$formconfirm='';

	/*
	 * Confirmation de la suppression de la commande
	 */
	if ($action	== 'delete')
	{
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('DeleteOrder'), $langs->trans('ConfirmDeleteOrder'), 'confirm_delete', '', 0, 2);

	}

	// Clone confirmation
	if ($action == 'clone')
	{
		// Create an array for form
		$formquestion=array(
				//array('type' => 'checkbox', 'name' => 'update_prices',   'label' => $langs->trans("PuttingPricesUpToDate"),   'value' => 1)
		);
		// Paiement incomplet. On demande si motif = escompte ou autre
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id,$langs->trans('CloneOrder'),$langs->trans('ConfirmCloneOrder',$object->ref),'confirm_clone',$formquestion,'yes',1);

	}

	/*
	 * Confirmation de la validation
	 */
	if ($action	== 'valid')
	{
		$object->date_commande=dol_now();

		// We check if number is temporary number
		if (preg_match('/^[\(]?PROV/i',$object->ref) || empty($object->ref)) // empty should not happened, but when it occurs, the test save life
		{
			$newref = $object->getNextNumRef($object->thirdparty);
		}
		else $newref = $object->ref;

		if ($newref < 0)
		{
			setEventMessages($object->error, $object->errors, 'errors');
			$action='';
		}
		else
		{
			$text=$langs->trans('ConfirmValidateOrder',$newref);
			if (! empty($conf->notification->enabled))
			{
				require_once DOL_DOCUMENT_ROOT .'/core/class/notify.class.php';
				$notify=new	Notify($db);
				$text.='<br>';
				$text.=$notify->confirmMessage('ORDER_SUPPLIER_VALIDATE', $object->socid, $object);
			}

			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('ValidateOrder'), $text, 'confirm_valid', '', 0, 1);
		}
	}

	/*
	 * Confirm approval
	 */
	if ($action	== 'approve' || $action	== 'approve2')
	{
		$qualified_for_stock_change=0;
		if (empty($conf->global->STOCK_SUPPORTS_SERVICES))
		{
			$qualified_for_stock_change=$object->hasProductsOrServices(2);
		}
		else
		{
			$qualified_for_stock_change=$object->hasProductsOrServices(1);
		}

		$formquestion=array();
		if (! empty($conf->stock->enabled) && ! empty($conf->global->STOCK_CALCULATE_ON_SUPPLIER_VALIDATE_ORDER) && $qualified_for_stock_change)
		{
			$langs->load("stocks");
			require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
			$formproduct=new FormProduct($db);
			$formquestion=array(
					//'text' => $langs->trans("ConfirmClone"),
					//array('type' => 'checkbox', 'name' => 'clone_content',   'label' => $langs->trans("CloneMainAttributes"),   'value' => 1),
					//array('type' => 'checkbox', 'name' => 'update_prices',   'label' => $langs->trans("PuttingPricesUpToDate"),   'value' => 1),
				array('type' => 'other', 'name' => 'idwarehouse',   'label' => $langs->trans("SelectWarehouseForStockIncrease"),   'value' => $formproduct->selectWarehouses(GETPOST('idwarehouse'),'idwarehouse','',1))
			);
		}
		$text=$langs->trans("ConfirmApproveThisOrder",$object->ref);
		if (! empty($conf->notification->enabled))
		{
			require_once DOL_DOCUMENT_ROOT .'/core/class/notify.class.php';
			$notify=new	Notify($db);
			$text.='<br>';
			$text.=$notify->confirmMessage('ORDER_SUPPLIER_APPROVE', $object->socid, $object);
		}

		$formconfirm = $form->formconfirm($_SERVER['PHP_SELF']."?id=".$object->id, $langs->trans("ApproveThisOrder"), $text, "confirm_".$action, $formquestion, 1, 1, 240);
	}

	/*
	 * Confirmation de la desapprobation
	 */
	if ($action	== 'refuse')
	{
		$formconfirm = $form->formconfirm($_SERVER['PHP_SELF']."?id=$object->id",$langs->trans("DenyingThisOrder"),$langs->trans("ConfirmDenyingThisOrder",$object->ref),"confirm_refuse", '', 0, 1);

	}

	/*
	 * Confirmation de l'annulation
	 */
	if ($action	== 'cancel')
	{
		$formconfirm = $form->formconfirm($_SERVER['PHP_SELF']."?id=$object->id",$langs->trans("Cancel"),$langs->trans("ConfirmCancelThisOrder",$object->ref),"confirm_cancel", '', 0, 1);

	}

	/*
	 * Confirmation de l'envoi de la commande
	 */
	if ($action	== 'commande')
	{
		$date_com = dol_mktime(GETPOST('rehour'),GETPOST('remin'),GETPOST('resec'),GETPOST("remonth"),GETPOST("reday"),GETPOST("reyear"));
		$formconfirm = $form->formconfirm($_SERVER['PHP_SELF']."?id=".$object->id."&datecommande=".$date_com."&methode=".$_POST["methodecommande"]."&comment=".urlencode($_POST["comment"]), $langs->trans("MakeOrder"),$langs->trans("ConfirmMakeOrder",dol_print_date($date_com,'day')),"confirm_commande",'',0,2);

	}

	// Confirmation to delete line
	if ($action == 'ask_deleteline')
	{
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id.'&lineid='.$lineid, $langs->trans('DeleteProductLine'), $langs->trans('ConfirmDeleteProductLine'), 'confirm_deleteline', '', 0, 1);
	}

	if (!$formconfirm)
	{
		$parameters=array('lineid'=>$lineid);
		$reshook = $hookmanager->executeHooks('formConfirm', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
		if (empty($reshook)) $formconfirm.=$hookmanager->resPrint;
		elseif ($reshook > 0) $formconfirm=$hookmanager->resPrint;
	}

	// Print form confirm
	print $formconfirm;

	/*
	 *	Commande
	*/
	$nbrow=8;
	if (! empty($conf->projet->enabled))	$nbrow++;

	//Local taxes
	if($mysoc->localtax1_assuj=="1") $nbrow++;
	if($mysoc->localtax2_assuj=="1") $nbrow++;

	print '<table class="border" width="100%">';

	$linkback = '<a href="'.DOL_URL_ROOT.'/purchase/commande/list.php'.(! empty($socid)?'?socid='.$socid:'').'">'.$langs->trans("BackToList").'</a>';

	// Ref
	print '<tr><td class="titlefield">'.$langs->trans("Ref").'</td>';
	print '<td colspan="2">';
	print $form->showrefnav($object, 'ref', $linkback, 1, 'ref', 'ref');
	print '</td>';
	print '</tr>';

	// Ref supplier ocultado
	//print '<tr><td>';
	//print $form->editfieldkey("RefSupplier",'ref_supplier',$object->ref_supplier,$object,$user->rights->fournisseur->commande->creer);
	//print '</td><td colspan="2">';
	//print $form->editfieldval("RefSupplier",'ref_supplier',$object->ref_supplier,$object,$user->rights->fournisseur->commande->creer);
	//print '</td></tr>';

	//mostrar que tipo de doc fiscal
	print '<tr><td>'.$langs->trans("Typefiscal")."</td>";
	$form->load_type_facture('type_facture', $objectadd->code_facture,0,$campo='code', true);
	foreach ($form->type_facture_code AS $j => $code)
	{
		if ($code == $objectadd->code_facture)
			print '<td colspan="2">'.$form->type_facture_label[$j].'</td>';
	}
	print '</tr>';
	print '<tr><td>'.$langs->trans("Purchasedestination")."</td>";
	$form->load_type_purchase('type_purchase', $objectadd->code_type_purchase,0,$campo='code', true);
	foreach ($form->type_purchase_code AS $j => $code)
	{
		if ($code == $objectadd->code_type_purchase)
			print '<td colspan="2">'.$form->type_purchase_label[$j].'</td>';
	}
	print '</tr>';

	// Fournisseur
	print '<tr><td>'.$langs->trans("Supplier")."</td>";
	print '<td colspan="2">'.$object->thirdparty->getNomUrl(1,'supplier').'</td>';
	print '</tr>';

	// Statut
	print '<tr>';
	print '<td>'.$langs->trans("Status").'</td>';
	print '<td colspan="2">';
	print $object->getLibStatut(4);
	print "</td></tr>";

	// Date
	if ($object->methode_commande_id > 0)
	{
		print '<tr><td>'.$langs->trans("Date").'</td><td colspan="2">';
		if ($object->date_commande)
		{
			print dol_print_date($object->date_commande,"dayhourtext")."\n";
		}
		print "</td></tr>";

		if ($object->methode_commande)
		{
			print '<tr><td>'.$langs->trans("Method").'</td><td colspan="2">'.$object->getInputMethod().'</td></tr>';
		}
	}

	// Author
	print '<tr><td>'.$langs->trans("AuthorRequest").'</td>';
	print '<td colspan="2">'.$author->getNomUrl(1).'</td>';
	print '</tr>';

	// Conditions de reglement par defaut
	$langs->load('bills');
	print '<tr><td class="nowrap">';
	print '<table width="100%" class="nobordernopadding"><tr><td class="nowrap">';
	print $langs->trans('PaymentConditions');
	print '<td>';
	if ($action != 'editconditions') print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=editconditions&amp;id='.$object->id.'">'.img_edit($langs->trans('SetConditions'),1).'</a></td>';
	print '</tr></table>';
	print '</td><td colspan="2">';
	if ($action == 'editconditions')
	{
		$form->form_conditions_reglement($_SERVER['PHP_SELF'].'?id='.$object->id,  $object->cond_reglement_id,'cond_reglement_id');
	}
	else
	{
		$form->form_conditions_reglement($_SERVER['PHP_SELF'].'?id='.$object->id,  $object->cond_reglement_id,'none');
	}
	print "</td>";
	print '</tr>';

	// Mode of payment
	$langs->load('bills');
	print '<tr><td class="nowrap">';
	print '<table width="100%" class="nobordernopadding"><tr><td class="nowrap">';
	print $langs->trans('PaymentMode');
	print '</td>';
	if ($action != 'editmode') print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=editmode&amp;id='.$object->id.'">'.img_edit($langs->trans('SetMode'),1).'</a></td>';
	print '</tr></table>';
	print '</td><td colspan="2">';
	if ($action == 'editmode')
	{
		$form->form_modes_reglement($_SERVER['PHP_SELF'].'?id='.$object->id,$object->mode_reglement_id,'mode_reglement_id');
	}
	else
	{
		$form->form_modes_reglement($_SERVER['PHP_SELF'].'?id='.$object->id,$object->mode_reglement_id,'none');
	}
	print '</td></tr>';

	// Multicurrency
	if (! empty($conf->multicurrency->enabled))
	{
		// Multicurrency code
		print '<tr>';
		print '<td width="25%">';
		print '<table class="nobordernopadding" width="100%"><tr><td>';
		print fieldLabel('Currency','multicurrency_code');
		print '</td>';
		if ($action != 'editmulticurrencycode' && ! empty($object->brouillon))
			print '<td align="right"><a href="' . $_SERVER["PHP_SELF"] . '?action=editmulticurrencycode&amp;id=' . $object->id . '">' . img_edit($langs->transnoentitiesnoconv('SetMultiCurrencyCode'), 1) . '</a></td>';
		print '</tr></table>';
		print '</td><td colspan="5">';
		if ($action == 'editmulticurrencycode') {
			$form->form_multicurrency_code($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->multicurrency_code, 'multicurrency_code');
		} else {
			$form->form_multicurrency_code($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->multicurrency_code, 'none');
		}
		print '</td></tr>';

		// Multicurrency rate
		print '<tr>';
		print '<td width="25%">';
		print '<table class="nobordernopadding" width="100%"><tr><td>';
		print fieldLabel('CurrencyRate','multicurrency_tx');
		print '</td>';
		if ($action != 'editmulticurrencyrate' && ! empty($object->brouillon))
			print '<td align="right"><a href="' . $_SERVER["PHP_SELF"] . '?action=editmulticurrencyrate&amp;id=' . $object->id . '">' . img_edit($langs->transnoentitiesnoconv('SetMultiCurrencyCode'), 1) . '</a></td>';
		print '</tr></table>';
		print '</td><td colspan="5">';
		if ($action == 'editmulticurrencyrate') {
			$form->form_multicurrency_rate($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->multicurrency_tx, 'multicurrency_tx', $object->multicurrency_code);
		} else {
			$form->form_multicurrency_rate($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->multicurrency_tx, 'none', $object->multicurrency_code);
		}
		print '</td></tr>';
	}

	// Bank Account
	if (! empty($conf->global->BANK_ASK_PAYMENT_BANK_DURING_SUPPLIER_ORDER) && ! empty($conf->banque->enabled))
	{
		print '<tr><td class="nowrap">';
		print '<table width="100%" class="nobordernopadding"><tr><td class="nowrap">';
		print $langs->trans('BankAccount');
		print '<td>';
		if ($action != 'editbankaccount' && $user->rights->fournisseur->commande->creer)
			print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=editbankaccount&amp;id='.$object->id.'">'.img_edit($langs->trans('SetBankAccount'),1).'</a></td>';
		print '</tr></table>';
		print '</td><td colspan="3">';
		if ($action == 'editbankaccount') {
			$form->formSelectAccount($_SERVER['PHP_SELF'].'?id='.$object->id, $object->fk_account, 'fk_account', 1);
		} else {
			$form->formSelectAccount($_SERVER['PHP_SELF'].'?id='.$object->id, $object->fk_account, 'none');
		}
		print '</td>';
		print '</tr>';
	}

	// Delivery date planed
	print '<tr><td>';
	print '<table class="nobordernopadding" width="100%"><tr><td>';
	print $langs->trans('DateDeliveryPlanned');
	print '</td>';
	if ($action != 'editdate_livraison') print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=editdate_livraison&amp;id='.$object->id.'">'.img_edit($langs->trans('SetDeliveryDate'),1).'</a></td>';
	print '</tr></table>';
	print '</td><td colspan="2">';
	if ($action == 'editdate_livraison')
	{
		print '<form name="setdate_livraison" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'" method="post">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="setdate_livraison">';
		$usehourmin=0;
		if (! empty($conf->global->SUPPLIER_ORDER_USE_HOUR_FOR_DELIVERY_DATE)) $usehourmin=1;
		$form->select_date($object->date_livraison?$object->date_livraison:-1,'liv_',$usehourmin,$usehourmin,'',"setdate_livraison");
		print '<input type="submit" class="button" value="'.$langs->trans('Modify').'">';
		print '</form>';
	}
	else
	{
		$usehourmin='day';
		if (! empty($conf->global->SUPPLIER_ORDER_USE_HOUR_FOR_DELIVERY_DATE)) $usehourmin='dayhour';
		print $object->date_livraison ? dol_print_date($object->date_livraison, $usehourmin) : '&nbsp;';
		if ($object->hasDelay() && ! empty($object->date_livraison)) {
			print ' '.img_picto($langs->trans("Late").' : '.$object->showDelay(), "warning");
		}
	}
	print '</td></tr>';


	// Delivery delay (in days)
	print '<tr>';
	print '<td>'.$langs->trans('NbDaysToDelivery').'&nbsp;'.img_picto($langs->trans('DescNbDaysToDelivery'), 'info', 'style="cursor:help"').'</td>';
	print '<td>'.$object->getMaxDeliveryTimeDay($langs).'</td>';
	print '</tr>';

	// Project
	if (! empty($conf->projet->enabled))
	{
		$langs->load('projects');
		print '<tr><td>';
		print '<table class="nobordernopadding" width="100%"><tr><td>';
		print $langs->trans('Project');
		print '</td>';
		if ($action != 'classify') print '<td align="right"><a href="'.$_SERVER['PHP_SELF'].'?action=classify&amp;id='.$object->id.'">'.img_edit($langs->trans('SetProject')).'</a></td>';
		print '</tr></table>';
		print '</td><td colspan="2">';
		//print "$object->id, $object->socid, $object->fk_project";
		if ($action == 'classify')
		{
			$form->form_project($_SERVER['PHP_SELF'].'?id='.$object->id, (empty($conf->global->PROJECT_CAN_ALWAYS_LINK_TO_ALL_SUPPLIERS)?$object->socid:-1), $object->fk_project, 'projectid', 0, 0, 1);
		}
		else
		{
			$form->form_project($_SERVER['PHP_SELF'].'?id='.$object->id, $object->socid, $object->fk_project, 'none', 0, 0);
		}
		print '</td>';
		print '</tr>';
	}

	// Incoterms
	if (!empty($conf->incoterm->enabled))
	{
		print '<tr><td>';
		print '<table width="100%" class="nobordernopadding"><tr><td>';
		print $langs->trans('IncotermLabel');
		print '<td><td align="right">';
		if ($user->rights->fournisseur->commande->creer) print '<a href="'.DOL_URL_ROOT.'/purchase/commande/card.php?id='.$object->id.'&action=editincoterm">'.img_edit().'</a>';
		else print '&nbsp;';
		print '</td></tr></table>';
		print '</td>';
		print '<td colspan="3">';
		if ($action != 'editincoterm')
		{
			print $form->textwithpicto($object->display_incoterms(), $object->libelle_incoterms, 1);
		}
		else
		{
			print $form->select_incoterms((!empty($object->fk_incoterms) ? $object->fk_incoterms : ''), (!empty($object->location_incoterms)?$object->location_incoterms:''), $_SERVER['PHP_SELF'].'?id='.$object->id);
		}
		print '</td></tr>';
	}

	// Other attributes
	$cols = 3;
	include DOL_DOCUMENT_ROOT . '/core/tpl/extrafields_view.tpl.php';

	// Total
	if ($objectadd->code_facture == 'STDC')
	{
		print '<tr><td>'.$langs->trans("AmountHT").'</td>';
		print '<td colspan="2">'.price($object->total_ht,'',$langs,1,-1,-1,$conf->currency).'</td>';
		print '</tr>';

		// Total VAT
		print '<tr><td>'.$langs->trans("AmountVAT").'</td><td colspan="2">'.price($object->total_tva,'',$langs,1,-1,-1,$conf->currency).'</td>';
		print '</tr>';
	}
	// Amount Local Taxes
	if ($mysoc->localtax1_assuj=="1" || $object->total_localtax1 != 0 || $objectadd->code_facture != 'STDC')
	 //Localtax1
	{
		print '<tr><td>'.$langs->transcountry("AmountLT1",$mysoc->country_code).'</td>';
		print '<td colspan="2">'.price($object->total_localtax1,'',$langs,1,-1,-1,$conf->currency).'</td>';
		print '</tr>';
	}
	if ($mysoc->localtax2_assuj=="1" || $object->total_localtax2 != 0 || $objectadd->code_facture != 'STDC')
	//Localtax2
	{
		print '<tr><td>'.$langs->transcountry("AmountLT2",$mysoc->country_code).'</td>';
		print '<td colspan="2">'.price($object->total_localtax2,'',$langs,1,-1,-1,$conf->currency).'</td>';
		print '</tr>';
	}

	// Total TTC
	print '<tr><td>'.$langs->trans("AmountTTC").'</td><td colspan="2">'.price($object->total_ttc,'',$langs,1,-1,-1,$conf->currency).'</td>';
	print '</tr>';

	if (!empty($conf->multicurrency->enabled))
	{
		// Multicurrency Amount HT
		print '<tr><td height="10">' . fieldLabel('MulticurrencyAmountHT','multicurrency_total_ht') . '</td>';
		print '<td class="nowrap" colspan="2">' . price($object->multicurrency_total_ht, '', $langs, 0, - 1, - 1, (!empty($object->multicurrency_code) ? $object->multicurrency_code : $conf->currency)) . '</td>';
		print '</tr>';

		// Multicurrency Amount VAT
		print '<tr><td height="10">' . fieldLabel('MulticurrencyAmountVAT','multicurrency_total_tva') . '</td>';
		print '<td class="nowrap" colspan="2">' . price($object->multicurrency_total_tva, '', $langs, 0, - 1, - 1, (!empty($object->multicurrency_code) ? $object->multicurrency_code : $conf->currency)) . '</td>';
		print '</tr>';

		// Multicurrency Amount TTC
		print '<tr><td height="10">' . fieldLabel('MulticurrencyAmountTTC','multicurrency_total_ttc') . '</td>';
		print '<td class="nowrap" colspan="2">' . price($object->multicurrency_total_ttc, '', $langs, 0, - 1, - 1, (!empty($object->multicurrency_code) ? $object->multicurrency_code : $conf->currency)) . '</td>';
		print '</tr>';
	}

	print "</table><br>";

	if (! empty($conf->global->MAIN_DISABLE_CONTACTS_TAB))
	{
		$blocname = 'contacts';
		$title = $langs->trans('ContactsAddresses');
		include DOL_DOCUMENT_ROOT.'/core/tpl/bloc_showhide.tpl.php';
	}

	if (! empty($conf->global->MAIN_DISABLE_NOTES_TAB))
	{
		$blocname = 'notes';
		$title = $langs->trans('Notes');
		include DOL_DOCUMENT_ROOT.'/core/tpl/bloc_showhide.tpl.php';
	}	if (! empty($conf->use_javascript_ajax))
	{
		print "\n".'<script type="text/javascript">';
		print '$(document).ready(function () {
			id_te_private=8;
			id_ef15=1;
			is_private='.$private.';
			if (is_private) {
				$(".datfiscal").show();
				$(".datnfiscal").hide();
			} else {
				$(".datfiscal").hide();
				$(".datnfiscal").show();
			}

			$("#code_facture").change(function() {
				document.add.action.value="create";
				document.add.submit();
			});
		});';
		print '</script>'."\n";
	}

	/*
	 * Lines
	 */
	//$result = $object->getLinesArray();
	if (! empty($conf->use_javascript_ajax))
	{
		print "\n".'<script type="text/javascript">';
		print '$(document).ready(function () {
			$("#select_type").change(function() {
				document.addproduct.action.value="refresh";
				document.addproduct.submit();
			});
			$("#fk_projet").change(function() {
				document.addproduct.action.value="refresh";
				document.addproduct.submit();
			});
			$("#fk_jobs").change(function() {
				document.addproduct.action.value="refresh";
				document.addproduct.submit();
			});
			$("#idprodfournprice").change(function() {
				document.addproduct.action.value="refresh";
				document.addproduct.submit();
			});
			$("#fk_structure").change(function() {
				document.addproduct.action.value="refresh";
				document.addproduct.submit();
			});
			$("#partida").change(function() {
				document.addproduct.action.value="refresh";
				document.addproduct.submit();
			});
		});';
		print '</script>'."\n";
	}
	if ($action =='refresh')
	{
		if (!empty($lineid)) $action = 'editline';
	}

	print '	<form name="addproduct" id="addproduct" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.(($action != 'editline')?'#add':'#line_'.GETPOST('lineid')).'" method="POST">
	<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">
	<input type="hidden" name="action" value="' . (($action != 'editline') ? 'addline' : 'updateline') . '">
	<input type="hidden" name="mode" value="">
	<input type="hidden" name="id" value="'.$object->id.'">
	<input type="hidden" name="socid" value="'.$societe->id.'">
	';
	print '<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery("#idprodfournprice").change(function() {
			if (jQuery("#idprodfournprice").val() > 0) jQuery("#dp_desc").focus();
		}); });
		</script>';

		if (! empty($conf->use_javascript_ajax) && $object->statut == 0) {
			include DOL_DOCUMENT_ROOT . '/core/tpl/ajaxrow.tpl.php';
		}

		print '<table id="tablelines" class="noborder noshadow" width="100%">';

	// Add free products/services form
		global $forceall, $senderissupplier, $dateSelector;
		$forceall=1; $senderissupplier=1; $dateSelector=0;

	// Show object lines
		$inputalsopricewithtax=0;
		if (! empty($object->lines))
			$ret = $object->printObjectLinesadd($action, $societe, $mysoc, $lineid, 1);


		$num = count($object->lines);

	// Form to add new line
		if ($object->statut == 0 && $user->rights->fournisseur->commande->creer)
		{
			$lAdd = true;
			if ($conf->global->PURCHASE_INTEGRATED_POA)
			{
				if ($object->fk_poa_prev>0)
					$lAdd = false;
			}
			if ($lAdd)
			{
				if ($action != 'editline')
				{
					$var = true;

				// Add free products/services
					$object->formAddObjectLineadd(1, $societe, $mysoc);

					$parameters = array();
					$reshook = $hookmanager->executeHooks('formAddObjectLine', $parameters, $object, $action);
				// Note that $action and $object may have been modified by hook
				}
			}
		}
		print '</table>';

		print '</form>';

		dol_fiche_end();


	//Action presend
		if (GETPOST('modelselected')) {
			$action = 'presend';
		}
		if ($action == 'presend')
		{
			$ref = dol_sanitizeFileName($object->ref);
			include_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
			$fileparams = dol_most_recent_file($conf->fournisseur->commande->dir_output . '/' . $ref, preg_quote($ref, '/').'[^\-]+');
			$file=$fileparams['fullname'];

		// Define output language
			$outputlangs = $langs;
			$newlang = '';
			if ($conf->global->MAIN_MULTILANGS && empty($newlang) && ! empty($_REQUEST['lang_id']))
				$newlang = $_REQUEST['lang_id'];
			if ($conf->global->MAIN_MULTILANGS && empty($newlang))
				$newlang = $object->thirdparty->default_lang;

			if (!empty($newlang))
			{
				$outputlangs = new Translate('', $conf);
				$outputlangs->setDefaultLang($newlang);
				$outputlangs->load('commercial');
			}

		// Build document if it not exists
			if (! $file || ! is_readable($file))
			{
				$result= $object->generateDocument(GETPOST('model')?GETPOST('model'):$object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
				if ($result <= 0)
				{
					dol_print_error($db,$result);
					exit;
				}
				$fileparams = dol_most_recent_file($conf->fournisseur->commande->dir_output . '/' . $ref, preg_quote($ref, '/').'[^\-]+');
				$file=$fileparams['fullname'];
			}

			print '<div class="clearboth"></div>';
			print '<br>';
			print load_fiche_titre($langs->trans('SendOrderByMail'));

			dol_fiche_head('');

		// Cree l'objet formulaire mail
			include_once DOL_DOCUMENT_ROOT.'/core/class/html.formmail.class.php';
			$formmail = new FormMail($db);
			$formmail->param['langsmodels']=(empty($newlang)?$langs->defaultlang:$newlang);
			$formmail->fromtype = 'user';
			$formmail->fromid   = $user->id;
			$formmail->fromname = $user->getFullName($langs);
			$formmail->frommail = $user->email;
			$formmail->trackid='sor'.$object->id;
		if (! empty($conf->global->MAIN_EMAIL_ADD_TRACK_ID) && ($conf->global->MAIN_EMAIL_ADD_TRACK_ID & 2))	// If bit 2 is set
		{
			include DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
			$formmail->frommail=dolAddEmailTrackId($formmail->frommail, 'sor'.$object->id);
		}
		$formmail->withfrom=1;
		$liste=array();
		foreach ($object->thirdparty->thirdparty_and_contact_email_array(1) as $key=>$value)	$liste[$key]=$value;
		$formmail->withto=GETPOST("sendto")?GETPOST("sendto"):$liste;
		$formmail->withtocc=$liste;
		$formmail->withtoccc=(! empty($conf->global->MAIN_EMAIL_USECCC)?$conf->global->MAIN_EMAIL_USECCC:false);
		$formmail->withtopic=$outputlangs->trans('SendOrderRef','__REF__');
		$formmail->withfile=2;
		$formmail->withbody=1;
		$formmail->withdeliveryreceipt=1;
		$formmail->withcancel=1;

		$object->fetch_projet();
		// Tableau des substitutions
		$formmail->setSubstitFromObject($object);
		$formmail->substit['__ORDERREF__']=$object->ref;                  	// For backward compatibility
		$formmail->substit['__ORDERSUPPLIERREF__']=$object->ref_supplier;	// For backward compatibility
		$formmail->substit['__SUPPLIERORDERREF__']=$object->ref_supplier;

		//Find the good contact adress
		$custcontact='';
		$contactarr=array();
		$contactarr=$object->liste_contact(-1,'external');

		if (is_array($contactarr) && count($contactarr)>0) {
			foreach($contactarr as $contact) {
				if ($contact['libelle']==$langs->trans('TypeContact_order_supplier_external_BILLING')) {
					require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
					$contactstatic=new Contact($db);
					$contactstatic->fetch($contact['id']);
					$custcontact=$contactstatic->getFullName($langs,1);
				}
			}

			if (!empty($custcontact)) {
				$formmail->substit['__CONTACTCIVNAME__']=$custcontact;
			}
		}

		// Tableau des parametres complementaires
		$formmail->param['action']='send';
		$formmail->param['models']='order_supplier_send';
		$formmail->param['models_id']=GETPOST('modelmailselected','int');
		$formmail->param['orderid']=$object->id;
		$formmail->param['returnurl']=$_SERVER["PHP_SELF"].'?id='.$object->id;

		// Init list of files
		if (GETPOST("mode")=='init')
		{
			$formmail->clear_attached_files();
			$formmail->add_attached_files($file,basename($file),dol_mimetype($file));
		}

		// Show form
		print $formmail->get_form();

		dol_fiche_end();
	}
	// Action webservice
	elseif ($action == 'webservice' && GETPOST('mode', 'alpha') != "send" && ! GETPOST('cancel'))
	{
		$mode        = GETPOST('mode', 'alpha');
		$ws_url      = $object->thirdparty->webservices_url;
		$ws_key      = $object->thirdparty->webservices_key;
		$ws_user     = GETPOST('ws_user','alpha');
		$ws_password = GETPOST('ws_password','alpha');

		// NS and Authentication parameters
		$ws_ns = 'http://www.dolibarr.org/ns/';
		$ws_authentication = array(
			'dolibarrkey'=>$ws_key,
			'sourceapplication'=>'DolibarrWebServiceClient',
			'login'=>$ws_user,
			'password'=>$ws_password,
			'entity'=>''
		);

		print load_fiche_titre($langs->trans('CreateRemoteOrder'),'');

		//Is everything filled?
		if (empty($ws_url) || empty($ws_key)) {
			setEventMessages($langs->trans("ErrorWebServicesFieldsRequired"), null, 'errors');
			$mode = "init";
			$error_occurred = true; //Don't allow to set the user/pass if thirdparty fields are not filled
		} else if ($mode != "init" && (empty($ws_user) || empty($ws_password))) {
			setEventMessages($langs->trans("ErrorFieldsRequired"), null, 'errors');
			$mode = "init";
		}

		if ($mode == "init")
		{
			//Table/form header
			print '<table class="border" width="100%">';
			print '<form action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'" method="post">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="webservice">';
			print '<input type="hidden" name="mode" value="check">';

			if ($error_occurred)
			{
				print "<br>".$langs->trans("ErrorOccurredReviseAndRetry")."<br>";
				print '<input class="button" type="submit" id="cancel" name="cancel" value="'.$langs->trans("Cancel").'">';
			}
			else
			{
				$textinput_size = "50";
				// Webservice url
				print '<tr><td>'.$langs->trans("WebServiceURL").'</td><td colspan="3">'.dol_print_url($ws_url).'</td></tr>';
				//Remote User
				print '<tr><td>'.$langs->trans("User").'</td><td><input size="'.$textinput_size.'" type="text" name="ws_user"></td></tr>';
				//Remote Password
				print '<tr><td>'.$langs->trans("Password").'</td><td><input size="'.$textinput_size.'" type="text" name="ws_password"></td></tr>';
				//Submit button
				print '<tr><td align="center" colspan="2">';
				print '<input class="button" type="submit" id="ws_submit" name="ws_submit" value="'.$langs->trans("CreateRemoteOrder").'">';
				print ' &nbsp; &nbsp; ';
				//Cancel button
				print '<input class="button" type="submit" id="cancel" name="cancel" value="'.$langs->trans("Cancel").'">';
				print '</td></tr>';
			}

			//End table/form
			print '</form>';
			print '</table>';
		}
		elseif ($mode == "check")
		{
			$ws_entity = '';
			$ws_thirdparty = '';
			$error_occurred = false;

			//Create SOAP client and connect it to user
			$soapclient_user = new nusoap_client($ws_url."/webservices/server_user.php");
			$soapclient_user->soap_defencoding='UTF-8';
			$soapclient_user->decodeUTF8(false);

			//Get the thirdparty associated to user
			$ws_parameters = array('authentication'=>$ws_authentication, 'id' => '', 'ref'=>$ws_user);
			$result_user = $soapclient_user->call("getUser", $ws_parameters, $ws_ns, '');
			$user_status_code = $result_user["result"]["result_code"];

			if ($user_status_code == "OK")
			{
				//Fill the variables
				$ws_entity = $result_user["user"]["entity"];
				$ws_authentication['entity'] = $ws_entity;
				$ws_thirdparty = $result_user["user"]["fk_thirdparty"];
				if (empty($ws_thirdparty))
				{
					setEventMessages($langs->trans("RemoteUserMissingAssociatedSoc"), null, 'errors');
					$error_occurred = true;
				}
				else
				{
					//Create SOAP client and connect it to product/service
					$soapclient_product = new nusoap_client($ws_url."/webservices/server_productorservice.php");
					$soapclient_product->soap_defencoding='UTF-8';
					$soapclient_product->decodeUTF8(false);

					// Iterate each line and get the reference that uses the supplier of that product/service
					$i = 0;
					foreach ($object->lines as $line) {
						$i = $i + 1;
						$ref_supplier = $line->ref_supplier;
						$line_id = $i."º) ".$line->product_ref.": ";
						if (empty($ref_supplier)) {
							continue;
						}
						$ws_parameters = array('authentication' => $ws_authentication, 'id' => '', 'ref' => $ref_supplier);
						$result_product = $soapclient_product->call("getProductOrService", $ws_parameters, $ws_ns, '');
						if (!$result_product)
						{
							setEventMessages($line_id.$langs->trans("SOAPError")." ".$soapclient_product->error_str." - ".$soapclient_product->response, null, 'errors');
							$error_occurred = true;
							break;
						}

						// Check the result code
						$status_code = $result_product["result"]["result_code"];
						if (empty($status_code)) //No result, check error str
						{
							setEventMessages($langs->trans("SOAPError")." '".$soapclient_order->error_str."'", null, 'errors');
						}
						else if ($status_code != "OK") //Something went wrong
						{
							if ($status_code == "NOT_FOUND")
							{
								setEventMessages($line_id.$langs->trans("SupplierMissingRef")." '".$ref_supplier."'", null, 'warnings');
							}
							else
							{
								setEventMessages($line_id.$langs->trans("ResponseNonOK")." '".$status_code."' - '".$result_product["result"]["result_label"]."'", null, 'errors');
								$error_occurred = true;
								break;
							}
						}


						// Ensure that price is equal and warn user if it's not
						$supplier_price = price($result_product["product"]["price_net"]); //Price of client tab in supplier dolibarr
						$local_price = NULL; //Price of supplier as stated in product suppliers tab on this dolibarr, NULL if not found

						$product_fourn = new ProductFournisseur($db);
						$product_fourn_list = $product_fourn->list_product_fournisseur_price($line->fk_product);
						if (count($product_fourn_list)>0)
						{
							foreach($product_fourn_list as $product_fourn_line)
							{
								//Only accept the line where the supplier is the same at this order and has the same ref
								if ($product_fourn_line->fourn_id == $object->socid && $product_fourn_line->fourn_ref == $ref_supplier) {
									$local_price = price($product_fourn_line->fourn_price);
								}
							}
						}

						if ($local_price != NULL && $local_price != $supplier_price) {
							setEventMessages($line_id.$langs->trans("RemotePriceMismatch")." ".$supplier_price." - ".$local_price, null, 'warnings');
						}

						// Check if is in sale
						if (empty($result_product["product"]["status_tosell"])) {
							setEventMessages($line_id.$langs->trans("ProductStatusNotOnSellShort")." '".$ref_supplier."'", null, 'warnings');
						}
					}
				}

			}
			elseif ($user_status_code == "PERMISSION_DENIED")
			{
				setEventMessages($langs->trans("RemoteUserNotPermission"), null, 'errors');
				$error_occurred = true;
			}
			elseif ($user_status_code == "BAD_CREDENTIALS")
			{
				setEventMessages($langs->trans("RemoteUserBadCredentials"), null, 'errors');
				$error_occurred = true;
			}
			else
			{
				setEventMessages($langs->trans("ResponseNonOK")." '".$user_status_code."'", null, 'errors');
				$error_occurred = true;
			}

			//Form
			print '<form action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'" method="post">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="webservice">';
			print '<input type="hidden" name="mode" value="send">';
			print '<input type="hidden" name="ws_user" value="'.$ws_user.'">';
			print '<input type="hidden" name="ws_password" value="'.$ws_password.'">';
			print '<input type="hidden" name="ws_entity" value="'.$ws_entity.'">';
			print '<input type="hidden" name="ws_thirdparty" value="'.$ws_thirdparty.'">';
			if ($error_occurred)
			{
				print "<br>".$langs->trans("ErrorOccurredReviseAndRetry")."<br>";
			}
			else
			{
				print '<input class="button" type="submit" id="ws_submit" name="ws_submit" value="'.$langs->trans("Confirm").'">';
				print ' &nbsp; &nbsp; ';
			}
			print '<input class="button" type="submit" id="cancel" name="cancel" value="'.$langs->trans("Cancel").'">';
			print '</form>';
		}
	}
	/*
	 * Show buttons
	 */
	else
	{
		// Boutons actions

		if ($user->societe_id == 0 && $action != 'editline' && $action != 'delete')
		{
			print '<div	class="tabsAction">';

			$parameters = array();
			$reshook = $hookmanager->executeHooks('addMoreActionsButtons', $parameters, $object, $action); // Note that $action and $object may have been
			// modified by hook
			if (empty($reshook))
			{

				// Validate
				if ($object->statut == 0 && $num > 0)
				{
					if ((empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->fournisseur->commande->creer))
						|| (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->fournisseur->supplier_order_advance->validate)))
					{
						$tmpbuttonlabel=$langs->trans('Validate');
						if ($user->rights->fournisseur->commande->approuver && empty($conf->global->SUPPLIER_ORDER_NO_DIRECT_APPROVE)) $tmpbuttonlabel = $langs->trans("ValidateAndApprove");

						print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=valid">';
						print $tmpbuttonlabel;
						print '</a>';
					}
				}
				// Create event
				if ($conf->agenda->enabled && ! empty($conf->global->MAIN_ADD_EVENT_ON_ELEMENT_CARD)) 	// Add hidden condition because this is not a "workflow" action so should appears somewhere else on page.
				{
					print '<div class="inline-block divButAction"><a class="butAction" href="' . DOL_URL_ROOT . '/comm/action/card.php?action=create&amp;origin=' . $object->element . '&amp;originid=' . $object->id . '&amp;socid=' . $object->socid . '">' . $langs->trans("AddAction") . '</a></div>';
				}

				// Modify
				if ($object->statut == 1)
				{
					if ($user->rights->fournisseur->commande->commander)
					{
						print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=reopen">'.$langs->trans("Modify").'</a>';
					}
				}

				// Approve
				if ($object->statut == 1)
				{
					if ($user->rights->fournisseur->commande->approuver)
					{
						if (! empty($conf->global->SUPPLIER_ORDER_DOUBLE_APPROVAL) && $conf->global->MAIN_FEATURES_LEVEL > 0 && $object->total_ht >= $conf->global->SUPPLIER_ORDER_DOUBLE_APPROVAL && ! empty($object->user_approve_id))
						{
							print '<a class="butActionRefused" href="#" title="'.dol_escape_htmltag($langs->trans("FirstApprovalAlreadyDone")).'">'.$langs->trans("ApproveOrder").'</a>';
						}
						else
						{
							print '<a class="butAction"	href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=approve">'.$langs->trans("ApproveOrder").'</a>';
						}
					}
					else
					{
						print '<a class="butActionRefused" href="#" title="'.dol_escape_htmltag($langs->trans("NotAllowed")).'">'.$langs->trans("ApproveOrder").'</a>';
					}
				}

				// Second approval (if option SUPPLIER_ORDER_DOUBLE_APPROVAL is set)
				if (! empty($conf->global->SUPPLIER_ORDER_DOUBLE_APPROVAL) && $conf->global->MAIN_FEATURES_LEVEL > 0 && $object->total_ht >= $conf->global->SUPPLIER_ORDER_DOUBLE_APPROVAL)
				{
					if ($object->statut == 1)
					{
						if ($user->rights->fournisseur->commande->approve2)
						{
							if (! empty($object->user_approve_id2))
							{
								print '<a class="butActionRefused" href="#" title="'.dol_escape_htmltag($langs->trans("SecondApprovalAlreadyDone")).'">'.$langs->trans("Approve2Order").'</a>';
							}
							else
							{
								print '<a class="butAction"	href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=approve2">'.$langs->trans("Approve2Order").'</a>';
							}
						}
						else
						{
							print '<a class="butActionRefused" href="#" title="'.dol_escape_htmltag($langs->trans("NotAllowed")).'">'.$langs->trans("Approve2Order").'</a>';
						}
					}
				}

				// Refuse
				if ($object->statut == 1)
				{
					if ($user->rights->fournisseur->commande->approuver || $user->rights->fournisseur->commande->approve2)
					{
						print '<a class="butAction"	href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=refuse">'.$langs->trans("RefuseOrder").'</a>';
					}
					else
					{
						print '<a class="butActionRefused" href="#" title="'.dol_escape_htmltag($langs->trans("NotAllowed")).'">'.$langs->trans("RefuseOrder").'</a>';
					}
				}

				// Send
				if (in_array($object->statut, array(2, 3, 4, 5)))
				{
					if ($user->rights->fournisseur->commande->commander)
					{
						print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=presend&amp;mode=init">'.$langs->trans('SendByMail').'</a>';
					}
				}

				// Reopen
				if (in_array($object->statut, array(2)))
				{
					$buttonshown=0;
					if (! $buttonshown && $user->rights->fournisseur->commande->approuver)
					{
						if (empty($conf->global->SUPPLIER_ORDER_REOPEN_BY_APPROVER_ONLY)
							|| (! empty($conf->global->SUPPLIER_ORDER_REOPEN_BY_APPROVER_ONLY) && $user->id == $object->user_approve_id))
						{
							print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=reopen">'.$langs->trans("Disapprove").'</a>';
							$buttonshown++;
						}
					}
					if (! $buttonshown && $user->rights->fournisseur->commande->approve2 && ! empty($conf->global->SUPPLIER_ORDER_DOUBLE_APPROVAL))
					{
						if (empty($conf->global->SUPPLIER_ORDER_REOPEN_BY_APPROVER2_ONLY)
							|| (! empty($conf->global->SUPPLIER_ORDER_REOPEN_BY_APPROVER2_ONLY) && $user->id == $object->user_approve_id2))
						{
							print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=reopen">'.$langs->trans("Disapprove").'</a>';
						}
					}
				}
				if (in_array($object->statut, array(3, 4, 5, 6, 7, 9)))
				{
					if ($user->rights->fournisseur->commande->commander)
					{
						print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=reopen">'.$langs->trans("ReOpen").'</a>';
					}
				}

				// Ship
				if (! empty($conf->stock->enabled) && ! empty($conf->global->STOCK_CALCULATE_ON_SUPPLIER_DISPATCH_ORDER))
				{
					if (in_array($object->statut, array(3,4))) {
						if ($conf->fournisseur->enabled && $user->rights->fournisseur->commande->receptionner) {
							print '<div class="inline-block divButAction"><a class="butAction" href="' . DOL_URL_ROOT . '/purchase/commande/dispatch.php?id=' . $object->id . '">' . $langs->trans('OrderDispatch') . '</a></div>';
						} else {
							print '<div class="inline-block divButAction"><a class="butActionRefused" href="#" title="' . dol_escape_htmltag($langs->trans("NotAllowed")) . '">' . $langs->trans('OrderDispatch') . '</a></div>';
						}
					}
				}

				// Create bill
				if (! empty($conf->facture->enabled))
				{
					if (! empty($conf->fournisseur->enabled) && ($object->statut >= 2 && $object->billed != 1))  // 2 means accepted
					{
						if ($user->rights->fournisseur->facture->creer)
						{
							print '<a class="butAction" href="'.DOL_URL_ROOT.'/purchase/facture/card.php?action=create&amp;origin='.$object->element.'&amp;originid='.$object->id.'&amp;socid='.$object->socid.'">'.$langs->trans("CreateBill").'</a>';
						}

						if ($user->rights->fournisseur->commande->creer && $object->statut >= 2 && !empty($object->linkedObjectsIds['invoice_supplier']))
						{
							print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=classifybilled">'.$langs->trans("ClassifyBilled").'</a>';
						}
					}

				}

				// Create a remote order using WebService only if module is activated
				if (! empty($conf->syncsupplierwebservices->enabled) && $object->statut >= 2) // 2 means accepted
				{
					print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=webservice&amp;mode=init">'.$langs->trans('CreateRemoteOrder').'</a>';
				}

				// Clone
				if ($user->rights->fournisseur->commande->creer)
				{
					print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&amp;socid='.$object->socid.'&amp;action=clone&amp;object=order">'.$langs->trans("ToClone").'</a>';
				}

				// Cancel
				if ($object->statut == 2)
				{
					if ($user->rights->fournisseur->commande->commander)
					{
						print '<a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=cancel">'.$langs->trans("CancelOrder").'</a>';
					}
				}

				// Delete
				if ($user->rights->fournisseur->commande->supprimer)
				{
					print '<a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans("Delete").'</a>';
				}
			}
			print "</div>";




			$lVal = true;
			if ($conf->global->PURCHASE_INTEGRATED_POA)
			{
				$lVal = false;
				if ($conf->poa->enabled)
				{
					//obtenemos las reformulaciones
					require_once DOL_DOCUMENT_ROOT.'/poa/class/poareformulatedext.class.php';

					$objPoareformulated = new Poareformulatedext($db);
					$objPoa = new Poapoaext($db);
					list($aOfa,$aOfonea,$aOfrefa) = $objPoareformulated ->reformulated($period_year);

					$objPartida 		= new Cpartida($db);
					$partidaproduct 	= new Partidaproduct($db);
					$objpoa 		= new Poapoaext($db);
					$objPoaprev 		= new Poaprevext($db);
					$objPoastructure 	= new Poastructureext($db);
					$objPoaobjetive 	= new Poaobjetiveext($db);

					$aArray = explode(',',$conf->global->POA_SERVICE_TYPE_CODEPARTIDA);
					foreach ((array) $aArray AS $j => $value)
					{
						$aTypeservice[$value] = $value;
					}
					if ($object->fk_poa_prev > 0)
					{
						//recuperamos el que esta registrado
						$res = $objPoaprev->fetch($object->fk_poa_prev);
						$res = $objPoaprev->fetch_lines();
						if ($res > 0)
						{
							$lines = $objPoaprev->lines;
							foreach ($lines AS $j => $line)
							{
								$aStructure[$line->fk_structure] = $line->fk_structure;
								$aPoa[$line->fk_poa] = $line->fk_poa;
								$aObjetive[$line->fk_poa_objetive] = $line->fk_poa_objetive;
								$fk_structure = $line->fk_structure;
								$fk_poa = $line->fk_poa;
							}
							if (count($aStructure)==1)
							{
								$res = $objPoa->fetch($fk_poa);

								if ($res >0)
								{
									$res = $objPoaobjetive->fetch($objPoa->fk_poa_objetive);
									$level = $objPoaobjetive->level;
									$aObjetive[$level] = array('label'=>$objPoaobjetive->label,'sigla'=>$objPoaobjetive->sigla);
									$fk_father = $objPoaobjetive->fk_father;
									if ($fk_father>0)
									{
										$lLoop = true;
										while ($lLoop==true){
											$res = $objPoaobjetive->fetch($fk_father);
											if ($res <=0)
											{
												$lLoop = false;
												setEventMessages($objPoaobjetive->error,$objPoaobjetive->errors,'errors');
											}
											$level = $objPoaobjetive->level;
											$aObjetive[$level] = array('label'=>$objPoaobjetive->label,'sigla'=>$objPoaobjetive->sigla);
											$fk_father = $objPoaobjetive->fk_father;
											if (empty($fk_father)) $lLoop = false;
										}
									}
								}
							}
						}
						//mostramos tanto para planificación y para presupuestos
						if ($user->rights->poa->prev->valplan && $objPoaprev->status_plan == 0)
						{
							print '	<form name="valplan" id="valplan" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'" method="POST">
							<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">
							<input type="hidden" name="action" value="valplan">
							<input type="hidden" name="mode" value="">
							<input type="hidden" name="id" value="'.$object->id.'">
							<input type="hidden" name="idr" value="'.$object->fk_poa_prev.'">
							';
						}
						dol_fiche_head();
						print '<table width="100%">';
						print '<tr class="liste_titre">';
						print_liste_field_titre($langs->trans('Planning'),$_SERVER['PHP_SELF'],'','',$params,'align="center" colspan="6"',$sortfield,$sortorder);
						print '</tr>';
						print '<tr class="liste_titre">';
						print_liste_field_titre($langs->trans('Obj.Gestion'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
						print_liste_field_titre($langs->trans('Obj.Especific'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
						print_liste_field_titre($langs->trans('CodeOperation'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
						print_liste_field_titre($langs->trans('Detail'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
						print_liste_field_titre($langs->trans('Statut'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
						//print_liste_field_titre($langs->trans('Action'),$_SERVER['PHP_SELF'],'','',$params,'align="right"',$sortfield,$sortorder);
						print '</tr>';
						print '<tr>';
						$aArray = $aObjetive[1];
						print '<td>';
						print $aArray['sigla'];
						print '</td>';
						$aArray = $aObjetive[2];
						print '<td>';
						print $aArray['sigla'];
						print '</td>';
						$aArray = $aObjetive[3];
						print '<td>';
						print $aArray['sigla'];
						print '</td>';
						print '<td>';
						print $aArray['label'];
						print '</td>';
						print '<td>';
						print $objPoaprev->getLibStatutplan(2);
						print '</td>';
						//print '<td align="right">';
						//if ($user->rights->poa->prev->valplan && $objPoaprev->status_plan == 0)
						//	print '<input type="submit" name="submit" value="'.$langs->trans('Approve').'">';
						//print '</td>';

						print '</tr>';
						print '</table>';
						dol_fiche_end();
						if ($user->rights->poa->prev->valplan && $objPoaprev->status_plan == 0) print '</form>';

						//presupuestario
						if ($user->rights->poa->prev->valpres && $objPoaprev->status_pres == 0)
						{

							print '	<form name="valpres" id="valpres" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'" method="POST">
							<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">
							<input type="hidden" name="action" value="valpres">
							<input type="hidden" name="mode" value="">
							<input type="hidden" name="id" value="'.$object->id.'">
							<input type="hidden" name="idr" value="'.$object->fk_poa_prev.'">
							';
						}

						dol_fiche_head();
						print '<table width="100%">';
						print '<tr class="liste_titre">';
						print_liste_field_titre($langs->trans('Budgeting'),$_SERVER['PHP_SELF'],'','',$params,'align="center" colspan="8"',$sortfield,$sortorder);
						print '</tr>';
						print '<tr class="liste_titre">';
						print_liste_field_titre($langs->trans('Preventive'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
						print_liste_field_titre($langs->trans('Date'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
						print_liste_field_titre($langs->trans('Name'),$_SERVER['PHP_SELF'],'','',$params,'colspan="2"',$sortfield,$sortorder);
						print_liste_field_titre($langs->trans('Pac'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
						print_liste_field_titre($langs->trans('Total'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
						print_liste_field_titre($langs->trans('Statut'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
						//print_liste_field_titre($langs->trans('Action'),$_SERVER['PHP_SELF'],'','',$params,'align="right"',$sortfield,$sortorder);
						print '</tr>';
						print '<tr>';
						print '<td>'.$objPoaprev->nro_preventive.'</td>';
						print '<td>'.dol_print_date($objPoaprev->date_preventive,'day').'</td>';
						print '<td colspan="2">'.$objPoaprev->label.'</td>';
						print '<td>'.$objPoaprev->fk_pac.'</td>';
						print '<td align="right">'.price($objPoaprev->amount).'</td>';
						print '<td>';
						print $objPoaprev->getLibStatutpres(2);
						print '</td>';
						//print '<td align="right">';
						//if ($user->rights->poa->prev->valpres && $objPoaprev->status_pres == 0)
						//	print '<input type="submit" name="submit" value="'.$langs->trans('Approve').'">';
						//print '</td>';
						print '</tr>';
						print '<tr class="liste_titre">';
						print_liste_field_titre($langs->trans('Catprog'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
						print_liste_field_titre($langs->trans('Partida'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
						print_liste_field_titre($langs->trans('Label'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
						print_liste_field_titre($langs->trans('Approved'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
						print_liste_field_titre($langs->trans('Preventive'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
						print_liste_field_titre($langs->trans('Balance'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
						print_liste_field_titre('',$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
						print '</tr>';
						foreach ($objPoaprev->lines AS $j => $line)
						{
							$objPoastructure->fetch($line->fk_structure);
							$objPartida->fetch(0,$line->partida,$objPoaprev->gestion);
							$objPoa->fetch($line->fk_poa);
							$nPresup = $objPoa->amount;
							$nReformap = $aOfa[$line->fk_structure][$line->fk_poa][$line->partida];
							$nTotalAp = $nPresup+$nReformap;
							//$res = $objPoaprev->get_sum_catprog_partida($period_year, $line->fk_structure,$line->partida,0);
							$res = $objPoaprev->get_sum_str_poa_partida($period_year, $line->fk_structure,$line->fk_poa,$line->partida,0);
							if ($res < 0)
							{

								$error++;
								setEventMessages($objPoaprev->error,$objPoaprev->errors,'errors');
							}
							$nPreventive = $objPoaprev->aSum[$line->fk_structure][$line->fk_poa][$line->partida]+0;

							print '<tr>';
							print '<td>'.$objPoastructure->sigla.'</td>';
							print '<td>'.$line->partida.'</td>';
							print '<td>'.$objPartida->label.'</td>';
							print '<td>'.price($nTotalAp).'</td>';
							print '<td>'.price($nPreventive).'</td>';
							print '<td>'.price(price2num($nTotalAp-$nPreventive,'MT')).'</td>';
							//if (price2num($nTotalAp-$nPreventive,'MT')-$line->amount < 0)
							//	print '<td align="right" class="textcolors">'.price($line->amount).'</td>';
							//else
							//print '<td align="right">'.price($line->amount).'</td>';
							print '<td>'.'</td>';
							print '</tr>';

						}
						//recuperamos las lineas del preventivo

						print '</table>';
						dol_fiche_end();
						if ($user->rights->poa->prev->valpres && $objPoaprev->status_pres == 0) print '</form>';
					}
					else
					{
						$codePartida = '';
						$aStructure = array();
						$aType = array();
						$aPartidalabel = array();
						$lCateg = true;
						//mostramos un resumen de las partidas afectadas
						if (count($object->lines)>0)
						{
							//agrupamos por partida presupuestaria
							foreach ($object->lines AS $j => $line)
							{
								//vamos a buscar en la tabla adicional para agregar a line
								$resadd = $objectdetadd->fetch(0,$line->id);
								if ($resadd>0)
								{
									$line->fk_fabrication = $objectdetadd->fk_fabrication;
									$line->fk_fabricationdet = $objectdetadd->fk_fabricationdet;
									$line->fk_projet = $objectdetadd->fk_projet;
									$line->fk_projet_task = $objectdetadd->fk_projet_task;
									$line->fk_jobs = $objectdetadd->fk_jobs;
									$line->fk_jobsdet = $objectdetadd->fk_jobsdet;
									$line->fk_structure = $objectdetadd->fk_structure;
									$line->fk_poa = $objectdetadd->fk_poa;
									$line->partida = $objectdetadd->partida;
								}
								//$partidaproduct->fetch($line->fk_product);
								$respar = $objPartida->fetch(0,$line->partida,$period_year);
								if ($respar>0)
								{
									$aType[$objPartida->type] = $objPartida->type;
									$aPartidalabel[$line->partida] = $objPartida->label;
								}

								$aPartida[$line->partida]+= $line->total_ttc;
								if (!empty($codePartida)) $codePartida.= ',';
								$codePartida.= "'".$line->partida."'";
								//creamos una variable para insertar en poapartidapredet
								$aPartidadet[$line->partida][$line->fk_product] += $line->total_ttc;
								$aPartidaorig[$line->partida][$line->fk_product][$line->id] = $line->total_ttc;
								$aStructure[$line->fk_structure] = $line->fk_structure;
								$aPoa[$line->fk_poa] = $line->fk_poa;
							}
							if (count($aStructure)==1)
							{
								foreach ($aStructure AS $fk_structure)
								{
							//
								}
							}
							else
							{
								setEventMessages($langs->trans('No se puede validar la Solicitud de Compra ya que se esta utilizando diferentes categorias programaticas'),null,'warnings');
								$lCateg = false;
							}
							if (count($aPoa)==1)
							{
								foreach ($aPoa AS $fk_poa)
								{
							//
								}
							}
							else
							{
								setEventMessages($langs->trans('No se puede validar la Solicitud de Compra ya que se esta utilizando diferentes poas'),null,'warnings');
								$lCateg = false;
							}
							if (count($aType)>1)
							{
								setEventMessages($langs->trans('No se puede validar la Solicitud de Compra ya que se esta utilizando diferentes tipos de partidas'),null,'warnings');
								$lCateg = false;
							}
							else
							{
								foreach ($aType AS $type)
								{
							//
								}
								//vamos a determinar que tipo de proceso es 0=servicio 1=bienes
								if ($aTypeservice[$type]) $aSeltype = $aTypeprocess[0];
								else $aSeltype= $aTypeprocess[1];
							}
							if ($object->status == 0)
							//	print load_fiche_titre($langs->trans("Resumen"));
							//if ($object->status == 1)
								print load_fiche_titre($langs->trans("Generación Certificación Presupuestaria"));

							//vamos a buscar la categoria programatica que tenga los recursos
							//$filterpoa = " AND t.partida IN (".$codePartida.")";
							//$filterpoa.= " AND t.fk_area = ".$object->fk_departament;
							//$filterpoa.= " AND t.fk_structure = ".$fk_structure;
							//$respoa = $objpoa->fetchAll('ASC', 'sigla', 0, 0,array('entity'=>$conf->entity,'gestion'=>$period_year),'AND',$filterpoa);

							$options = '';
							if ($lCateg)
							{
								$selected = '';
								if (count($aPoa) > 1)
								{
									$options.= '<option value="0">'.$langs->trans('Select').'</option>';
								}
								else
								{
									foreach ($aPoa AS $i)
									{
										$respoa = $objpoa->fetch($i);
										if ($respoa == 1)
										{
											$fk_poa = $objpoa->id;
											$objPoastructure->fetch($objpoa->fk_structure);
											if (GETPOST('fk_poa') == $i) $selected = ' selected="selected"';
											$options.= '<option value="'.$i.'" '.$selected.'>'.$objPoastructure->sigla.' - '.$objpoa->label.'</option>';
										}
									}
								}
							}
							if ($respoa>1)
								$fk_poa = GETPOST('fk_poa','int');
							if ($lCateg)
							{
								if (! empty($conf->use_javascript_ajax))
								{
									print "\n".'<script type="text/javascript">';
									print '$(document).ready(function () {
										$("#fk_poa").change(function() {
											document.addprev.action.value="createval";
											document.addprev.submit();
										});
									});';
									print '</script>'."\n";
								}
								print '	<form name="addprev" id="addprev" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'" method="POST">';
								print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
								print '<input type="hidden" name="action" value="createval">';
								print '<input type="hidden" name="id" value="'.$object->id.'">';
								print '<input type="hidden" name="socid" value="'.$societe->id.'">';


								dol_fiche_head();
								print '<table width="100%">';
								print '<tr class="liste_titre">';
								print '<td>'.$langs->trans('Cat.Prog.').'</td>';
								print '<td colspan="4">'.'<select id="fk_poa" name="fk_poa">'.$options.'</select></td>';
								print '</tr>';

								//requirementtype
								print '<tr><td class="fieldrequired">'.$langs->trans('Requirementtype').'</td><td colspan="2">';
								//print select_requirementtype($object->code_requirement,'code_requirement','',1,0,'code');
								print $form->selectarray('code_requirement',$aSeltype,GETPOST('code_requirement'),(count($aSeltype)>1?1:0));
								print '</td></tr>';
								dol_include_once('/poa/class/poastructureext.class.php');

								print '<tr class="liste_titre">';
								print '<td>'.$langs->trans('Obj. Gestion').'</td>';
								print '<td colspan="2">'.$langs->trans('Obj. Especifico').'</td>';
								print '<td>'.$langs->trans('Cat.Prog.').'</td>';
								print '<td>'.$langs->trans('Nombre.').'</td>';
								print '</tr>';

								//buscamos los objetivos
								$objpoa->fetch($fk_poa);
								//$objpoaobj = new Poaobjetiveext($db);
								//$objpoaobjtmp = new Poaobjetiveext($db);
								//$objpoastr = new Poastructureext($db);
								$resstr = $objPoastructure->fetch($objpoa->fk_structure);
								$resobj = $objPoaobjetive->fetch($objpoa->fk_poa_objetive);
								if ($resobj>0)
								{
									//buscamos todos los level
									$cOperation = $objPoaobjetive->sigla .' : '.$objPoaobjetive->label;
									$fk_father = $objPoaobjetive->fk_father;
									$lLoop = true;
									while ($lLoop)
									{
										if ($fk_father >0)
										{
											$resobj = $objPoaobjetive->fetch($fk_father);
											if ($resobj>0)
											{
												if ($objPoaobjetive->level == 2)
													$cUnidad = '<br>'.$objPoaobjetive->sigla.' : '.$objPoaobjetive->label;
												elseif($objPoaobjetive->level == 1)
													$cGestion = '<br>'.$objPoaobjetive->sigla.' : '.$objPoaobjetive->label;
												$fk_father = $objPoaobjetive->fk_father;
											}
											else
											{
												$lLoop = false;
											}
										}
										else
										{
											$lLoop = false;
										}
									}
								}
								print '<tr>';
								print '<td>'.$cGestion.'</td>';
								print '<td colspan="2">'.$cUnidad.'</td>';
								print '<td>'.$objPoastructure->sigla.'</td>';
								print '<td>'.$objPoastructure->label.'</td>';
								print '</tr>';


								print '<tr class="liste_titre">';
								print '<td>'.$langs->trans('Partida').'</td>';
								print '<td align="right">'.$langs->trans('Presupuesto').'</td>';
								print '<td align="right">'.$langs->trans('Preventivo').'</td>';
								print '<td align="right">'.$langs->trans('Saldo').'</td>';
								print '<td align="right">'.$langs->trans('Amount').'</td>';
								print '</tr>';
								$lValidate = true;
								foreach ($aPartida AS $codepartida => $value)
								{
									if ($respoa>0)
									{
										$nPresup = $objpoa->amount;
										$nReformap = $aOfa[$objpoa->fk_structure][$objpoa->id][$codepartida];
										$nTotalAp = $nPresup+$nReformap;

										//if (!empty($codepartida)) $objpartida->fetch(0,$codepartida,$period_year);
										$objPoaprev->get_sum_catprog_partida($period_year, $fk_structure,$codepartida,1);
										print '<tr>';
										//print '<td>'.$linepoa->sigla.' '.$linepoa->label.'</td>';
										if (!empty($codepartida))
										{
										//$objpartida->fetch(0,$codepartida,$period_year);
											print '<td>'.$codepartida.' - '.$aPartidalabel[$codepartida].'</td>';
										}
										else
											print '<td>'.$langs->trans('No definido').'</td>';
										//determinaremos el saldo que existe por catprog y partida
										print '<td align="right">'.price($nTotalAp).'</td>';
										print '<td align="right">'.price($objPoaprev->aSum[$line->fk_structure][$codepartida]).'</td>';
										print '<td align="right">'.price($nTotalAp-$objPoaprev->aSum[$objpoa->fk_structure][$codepartida]).'</td>';
										print '<td align="right">'.price($value).'</td>';
										$balance = $nTotalAp-$objPoaprev->aSum[$objpoa->fk_structure][$codepartida]-$value;
										if ($balance <0) $lValidate = false;
										print '</tr>';
										//agregamos en input para guardar en poapartidapre
										print '<input type="hidden" name="aPartida['.$codepartida.']" value="'.$value.'">';
										//agregamos en input para guardar en poapartidapredet
										foreach ($aPartidadet[$codepartida] AS $fk_product => $valuedet)
										{
											print '<input type="hidden" name="aPartidadet['.$codepartida.']['.$fk_product.']" value="'.$valuedet.'">';
										}
										foreach ($aPartidaorig[$codepartida] AS $fk_product => $aLine)
										{
											foreach ($aLine AS $fk_line => $value)
											{
												print '<input type="hidden" name="aPartidaorig['.$codepartida.']['.$fk_product.']['.$fk_line.']" value="'.$value.'">';
											}
										}
									}
									else
									{
										//if (!empty($codepartida)) $objpartida->fetch(0,$codepartida,$period_year);
										print '<tr>';
										print '<td>'.$langs->trans('Sin categoria programatica').'</td>';
										if (!empty($codepartida))
										{
										//$objpartida->fetch(0,$codepartida,$period_year);
											print '<td>'.$codepartida.' - '.$aPartidalabel[$codepartida].'</td>';
										}
										else
											print '<td>'.$langs->trans('No definido').'</td>';
										print '<td align="right">'.price(0).'</td>';
										print '<td align="right">'.price(0).'</td>';
										print '<td align="right">'.price(0).'</td>';
										print '<td align="right">'.price($value).'</td>';

										print '</tr>';

									}
								}

								print '</table>';
								dol_fiche_end();

								if ($user->rights->purchase->req->val)
								{
									print '<div class="center">';
									if ($lValidate)
										print '<input type="submit" class="butAction" name="addvalidate" value="'.$langs->trans("Validate").'">';
									else
										setEventMessages($langs->trans('No existe saldo suficiente para validar'),null,'warnings');

									print ' &nbsp; <a href="'.DOL_URL_ROOT.'/purchase/request/list.php'.'" name="return" class="butAction">'.$langs->trans("Return").'</a>';
									print '</div>';
								}

								print '</form>';
							}
						}
					}
					// Buttons
					print '<div class="tabsAction">'."\n";

					if ($object->status == 1 && empty($object->status_process))
					{
						if ($objPoaprev->status_plan && $objPoaprev->status_pres)
						{
							if ($user->rights->poa->proc->write)
							{
								$lnk = DOL_URL_ROOT.'/poa/process/process.php?id='.$object->id.'&fk_poa_prev='.$objPoaprev->id.'&action=create';
								print '<div class="inline-block divButAction"><a class="butAction" href="'.$lnk.'">'.$langs->trans("Starthiringprocess").'</a></div>'."\n";
							}
						}
					}
					print '</div>'."\n";

				}
				else
				{
					setEventMessages($langs->trans('Debe activar el modulo POA'),null,'warnings');
				}
			}
		}

		print "<br>";


		print '<div class="fichecenter"><div class="fichehalfleft">';

		/*
		 * Documents generes
		 */
		$comfournref = dol_sanitizeFileName($object->ref);
		$file =	$conf->fournisseur->dir_output . '/commande/' . $comfournref .	'/'	. $comfournref . '.pdf';
		$relativepath =	$comfournref.'/'.$comfournref.'.pdf';
		$filedir = $conf->fournisseur->dir_output	. '/commande/' .	$comfournref;
		$urlsource=$_SERVER["PHP_SELF"]."?id=".$object->id;
		$genallowed=$user->rights->fournisseur->commande->creer;
		$delallowed=$user->rights->fournisseur->commande->supprimer;

		print $formfile->showdocuments('commande_fournisseur',$comfournref,$filedir,$urlsource,$genallowed,$delallowed,$object->modelpdf,1,0,0,0,0,'','','',$object->thirdparty->default_lang);
		$somethingshown=$formfile->numoffiles;

		// Linked object block
		//$type='source'
		//$type='target'
		$res = $getUtil->get_element_element($object->id,'order_supplier',$type='target');
		if ($res > 0)
		{
			$object->type_element = 'target';
			$object->listObject = $getUtil->lines;
		}
		$somethingshown = $form->showLinkedObjectBlockpurchase($object);

		// Show links to link elements
		//$linktoelem = $form->showLinkToObjectBlock($object);
		//if ($linktoelem) print '<br>'.$linktoelem;


		print '</div><div class="fichehalfright"><div class="ficheaddleft">';


		if ($user->rights->fournisseur->commande->commander && $object->statut == 2)
		{
			/*
			 * Commander (action=commande)
			 */
			print '<!-- form to record supplier order -->'."\n";
			print '<form name="commande" action="card.php?id='.$object->id.'&amp;action=commande" method="post">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden"	name="action" value="commande">';
			print load_fiche_titre($langs->trans("ToOrder"),'','');
			print '<table class="border" width="100%">';
			//print '<tr class="liste_titre"><td colspan="2">'.$langs->trans("ToOrder").'</td></tr>';
			print '<tr><td>'.$langs->trans("OrderDate").'</td><td>';
			$date_com = dol_mktime(0, 0, 0, GETPOST('remonth'), GETPOST('reday'), GETPOST('reyear'));
			print $form->select_date($date_com,'',1,1,'',"commande",1,0,1);
			print '</td></tr>';

			print '<tr><td>'.$langs->trans("OrderMode").'</td><td>';
			$formorder->selectInputMethod(GETPOST('methodecommande'), "methodecommande", 1);
			print '</td></tr>';

			print '<tr><td>'.$langs->trans("Comment").'</td><td><input size="40" type="text" name="comment" value="'.GETPOST('comment').'"></td></tr>';
			print '<tr><td align="center" colspan="2"><input type="submit" class="button" value="'.$langs->trans("ToOrder").'"></td></tr>';
			print '</table>';
			print '</form>';
			print "<br>";
		}

		if ($user->rights->fournisseur->commande->receptionner	&& ($object->statut == 3 || $object->statut == 4))
		{

			/*
			 * Receptionner (action=livraison)
			 */
			print '<!-- form to record supplier order received -->'."\n";
			print '<form action="card.php?id='.$object->id.'" method="post">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden"	name="action" value="livraison">';
			print load_fiche_titre($langs->trans("Receive"),'','');
			print '<table class="border" width="100%">';
			//print '<tr class="liste_titre"><td colspan="2">'.$langs->trans("Receive").'</td></tr>';
			print '<tr><td>'.$langs->trans("DeliveryDate").'</td><td>';
			print $form->select_date(dol_now(),'',1,1,'',"commande",1,0,1);
			print "</td></tr>\n";

			print "<tr><td>".$langs->trans("Delivery")."</td><td>\n";
			$liv = array();
			$liv[''] = '&nbsp;';
			$liv['tot']	= $langs->trans("TotalWoman");
			$liv['par']	= $langs->trans("PartialWoman");
			$liv['nev']	= $langs->trans("NeverReceived");
			$liv['can']	= $langs->trans("Canceled");

			print $form->selectarray("type",$liv);

			print '</td></tr>';
			print '<tr><td>'.$langs->trans("Comment").'</td><td><input size="40" type="text" name="comment"></td></tr>';
			print '<tr><td align="center" colspan="2"><input type="submit" class="button" value="'.$langs->trans("Receive").'"></td></tr>';
			print "</table>\n";
			print "</form>\n";
			print "<br>";
		}

		// List of actions on element
		include_once DOL_DOCUMENT_ROOT.'/core/class/html.formactions.class.php';
		$formactions=new FormActions($db);
		$somethingshown=$formactions->showactions($object,'order_supplier',$socid,0,'listaction'.($genallowed?'largetitle':''));


		// List of actions on element
		/* Hidden because" available into "Log" tab
		print '<br>';
		include_once DOL_DOCUMENT_ROOT.'/core/class/html.formactions.class.php';
		$formactions=new FormActions($db);
		$somethingshown=$formactions->showactions($object,'order_supplier',$socid);
		*/

		print '</div></div></div>';
	}

	print '</td></tr></table>';
}


// End of page
llxFooter();

$db->close();
