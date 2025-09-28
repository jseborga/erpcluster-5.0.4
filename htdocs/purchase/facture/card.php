<?php
/* Copyright (C) 2002-2005	Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015	Laurent Destailleur 	<eldy@users.sourceforge.net>
 * Copyright (C) 2004		Christophe Combelles	<ccomb@free.fr>
 * Copyright (C) 2005		Marc Barilley			<marc@ocebo.fr>
 * Copyright (C) 2005-2013	Regis Houssin			<regis.houssin@capnetworks.com>
 * Copyright (C) 2010-2014	Juanjo Menent			<jmenent@2byte.es>
 * Copyright (C) 2013-2015	Philippe Grand			<philippe.grand@atoo-net.com>
 * Copyright (C) 2013       Florian Henry		  	<florian.henry@open-concept.pro>
 * Copyright (C) 2014       Marcos García           <marcosgdf@gmail.com>
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
 *	\file       htdocs/fourn/facture/card.php
 *	\ingroup    facture, fournisseur
 *	\brief      Page for supplier invoice card (view, edit, validate)
 */

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.getutil.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/modules/supplier_invoice/modules_facturefournisseur.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/fourn.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';

require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/paiementfourn.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

require_once DOL_DOCUMENT_ROOT.'/purchase/class/fournisseur.factureext.class.php';
require_once DOL_DOCUMENT_ROOT.'/purchase/class/facturefournadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/purchase/class/facturefourndetadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/purchase/class/ctypepurchase.class.php';

//require_once DOL_DOCUMENT_ROOT.'/purchase/class/html.formext.class.php';

if ($conf->fiscal->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/fiscal/class/tvadefext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/fiscal/lib/fiscal.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/fiscal/class/entity.class.php';
	require_once DOL_DOCUMENT_ROOT.'/fiscal/class/entityaddext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/fiscal/class/ctypefacture.class.php';
	require_once DOL_DOCUMENT_ROOT.'/fiscal/class/facturefourndetfiscalext.class.php';
}
if ($conf->productext->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/productext/class/productadd.class.php';
}
require_once DOL_DOCUMENT_ROOT.'/purchase/lib/purchase.lib.php';
if (!empty($conf->produit->enabled))
	require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
if (!empty($conf->projet->enabled)) {
	require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';
}


$langs->load('purchase');
$langs->load('monprojet');
$langs->load('bills');
$langs->load('compta');
$langs->load('suppliers');
$langs->load('companies');
$langs->load('products');
$langs->load('banks');

if (!empty($conf->incoterm->enabled)) $langs->load('incoterm');

$id			= (GETPOST('facid','int') ? GETPOST('facid','int') : GETPOST('id','int'));
$idd 		= GETPOST('idd','int');
$action		= GETPOST("action");
$confirm	= GETPOST("confirm");
$ref		= GETPOST('ref','alpha');
$cancel     = GETPOST('cancel','alpha');
$lineid     = GETPOST('lineid', 'int');
$projectid  = GETPOST('projectid','int');
$code_facture = GETPOST('code_facture');

//PDF
$hidedetails = (GETPOST('hidedetails','int') ? GETPOST('hidedetails','int') : (! empty($conf->global->MAIN_GENERATE_DOCUMENTS_HIDE_DETAILS) ? 1 : 0));
$hidedesc 	 = (GETPOST('hidedesc','int') ? GETPOST('hidedesc','int') : (! empty($conf->global->MAIN_GENERATE_DOCUMENTS_HIDE_DESC) ?  1 : 0));
$hideref 	 = (GETPOST('hideref','int') ? GETPOST('hideref','int') : (! empty($conf->global->MAIN_GENERATE_DOCUMENTS_HIDE_REF) ? 1 : 0));

// Security check
$socid='';
if (! empty($user->societe_id)) $socid=$user->societe_id;
$result = restrictedArea($user, 'fournisseur', $id, 'facture_fourn', 'facture');

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('invoicesuppliercard','globalcard'));

$object 		= new FactureFournisseurext($db);
$objtmp 		= new FactureFournisseurext($db);
$extrafields 	= new ExtraFields($db);
$tvadef 		= new Tvadefext($db);
$objectadd 		= new Facturefournadd($db);
$objtmpadd 		= new Facturefournadd($db);
$objectdetadd 	= new Facturefourndetadd($db);
$objectdetfiscal = new Facturefourndetfiscalext($db);
$productadd 	= new Productadd($db);
$societe 		= new Societe($db);
$objentity 		= new Entity($db);
$objentityadd 	= new Entityaddext($db);
$getutil 		= new getUtil($db);
$objTypefacture = new Ctypefacture($db);

// fetch optionals attributes and labels
$extralabels=$extrafields->fetch_name_optionals_label($object->table_element);

// Load object
if ($id > 0 || ! empty($ref))
{
	$ret=$object->fetch($id, $ref);
	$object->fetch_thirdparty();
	if (empty($id) && !empty($ref)) $id = $object->id;
}

$permissionnote=$user->rights->fournisseur->facture->creer;	// Used by the include of actions_setnotes.inc.php
$permissiondellink=$user->rights->fournisseur->facture->creer;	// Used by the include of actions_dellink.inc.php
$permissionedit=$user->rights->fournisseur->facture->creer; // Used by the include of actions_lineupdown.inc.php


/*
 * Actions
 */

$parameters=array('socid'=>$socid);
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	if ($cancel) $action='';
	include DOL_DOCUMENT_ROOT.'/core/actions_setnotes.inc.php';
	include DOL_DOCUMENT_ROOT.'/core/actions_dellink.inc.php';
	include DOL_DOCUMENT_ROOT.'/core/actions_lineupdown.inc.php';
	include DOL_DOCUMENT_ROOT.'/purchase/facture/inc/crud.tpl.php';
}


/*
 *	View
 */

$form = new Formv($db);
//$formf = new Formfad_d($db);
$formfile = new FormFile($db);
$bankaccountstatic=new Account($db);
$paymentstatic=new PaiementFourn($db);

$title = $langs->trans('Supplier invoice');
$morejs = array('/purchase/js/purchase.js');
$morecss = array('/purchase/css/style.css','/includes/jquery/plugins/datatables/media/css/dataTables.bootstrap.css','/includes/jquery/plugins/datatables/media/css/jquery.dataTables.css',);
llxHeader('',$title,'','','','',$morejs,$morecss,0,0);

// Mode creation
if ($action == 'create' || $action == 'addconf')
{
	$lCodefacture = true;
	$lCodepurchase = true;
	$lCodeaccount = true;
	$facturestatic = new FactureFournisseur($db);
	$extralabels = $extrafields->fetch_name_optionals_label($facturestatic->table_element);

	print load_fiche_titre($langs->trans('NewBill'));

	dol_htmloutput_events();

	$currency_code = $conf->currency;

	$societe='';
	if (GETPOST('socid') > 0)
	{
		$societe=new Societe($db);
		$societe->fetch(GETPOST('socid','int'));
		$nit = $societe->tva_intra;
		if (!empty($conf->multicurrency->enabled) && !empty($societe->multicurrency_code)) $currency_code = $societe->multicurrency_code;
	}

	if (GETPOST('origin') && GETPOST('originid'))
	{
		// Parse element/subelement (ex: project_task)
		$element = $subelement = GETPOST('origin');

		if ($element == 'project')
		{
			$projectid=GETPOST('originid');
			$element = 'projet';
		}
		elseif($element == 'projetpaiement')
		{
			$ppaiementid=GETPOST('originid');
			$fk_commande = GETPOST('fk_commande');
			$element = 'monprojet';
			$subelement = 'projetpaiement';
			require_once DOL_DOCUMENT_ROOT.'/'.$element.'/class/'.$subelement.'.class.php';
			$classname = ucfirst($subelement);
			$objectsrc = new $classname($db);
			$objectsrc->fetch(GETPOST('originid'));
			$objectsrc->fetch_thirdparty();
			$projectid = $objectsrc->fk_projet;
			$soc = $objectsrc->fk_soc;
			$_POST['socid'] = $objectsrc->fk_soc;
			$societe=new Societe($db);
			$societe->fetch($objectsrc->fk_soc);
			$nit = $societe->tva_intra;

			//revisamos si viene el pedido
			if ($fk_commande>0)
			{
				$lCodefacture = false;
				$element = 'fourn'; $subelement = 'fournisseur.commande';
				$elementadd = 'purchase'; $subelementadd = 'commandefournisseuradd';
				require_once DOL_DOCUMENT_ROOT.'/'.$elementadd.'/class/'.$subelementadd.'.class.php';
				$classnameadd = ucFirst($subelementadd);
				$objectsrcadd = new $classnameadd($db);
				$objectsrcadd->fetch(0,$fk_commande);
				$code_facture = $objectsrcadd->code_facture;
			}
		}
		elseif (in_array($element,array('order_supplier')))
		{
			// For compatibility
			if ($element == 'order')    {
				$element = $subelement = 'commande';
			}
			if ($element == 'propal')   {
				$element = 'comm/propal'; $subelement = 'propal';
			}
			if ($element == 'contract') {
				$element = $subelement = 'contrat';
			}
			if ($element == 'order_supplier') {
				$element = 'fourn'; $subelement = 'fournisseur.commande';
				$elementadd = 'purchase'; $subelementadd = 'commandefournisseuradd';
				require_once DOL_DOCUMENT_ROOT.'/'.$elementadd.'/class/'.$subelementadd.'.class.php';
				$classnameadd = ucFirst($subelementadd);
				$objectsrcadd = new $classnameadd($db);
				$objectsrcadd->fetch(0,GETPOST('originid'));
				$code_facture = $objectsrcadd->code_facture;
			}
			require_once DOL_DOCUMENT_ROOT.'/'.$element.'/class/'.$subelement.'.class.php';
			$classname = ucfirst($subelement);
			if ($classname == 'Fournisseur.commande') $classname='CommandeFournisseur';
			$objectsrc = new $classname($db);
			$objectsrc->fetch(GETPOST('originid'));
			$objectsrc->fetch_thirdparty();

			$projectid			= (!empty($objectsrc->fk_project)?$objectsrc->fk_project:'');
			//$ref_client			= (!empty($objectsrc->ref_client)?$object->ref_client:'');

			$soc = $objectsrc->thirdparty;
			$cond_reglement_id 	= (!empty($objectsrc->cond_reglement_id)?$objectsrc->cond_reglement_id:(!empty($soc->cond_reglement_supplier_id)?$soc->cond_reglement_supplier_id:1));
			$mode_reglement_id 	= (!empty($objectsrc->mode_reglement_id)?$objectsrc->mode_reglement_id:(!empty($soc->mode_reglement_supplier_id)?$soc->mode_reglement_supplier_id:0));
			$fk_account         = (! empty($objectsrc->fk_account)?$objectsrc->fk_account:(! empty($soc->fk_account)?$soc->fk_account:0));
			$remise_percent 	= (!empty($objectsrc->remise_percent)?$objectsrc->remise_percent:(!empty($soc->remise_percent)?$soc->remise_percent:0));
			$remise_absolue 	= (!empty($objectsrc->remise_absolue)?$objectsrc->remise_absolue:(!empty($soc->remise_absolue)?$soc->remise_absolue:0));
			$dateinvoice		= empty($conf->global->MAIN_AUTOFILL_DATE)?-1:'';

			if (!empty($conf->multicurrency->enabled))
			{
				if (!empty($objectsrc->multicurrency_code)) $currency_code = $objectsrc->multicurrency_code;
				if (!empty($conf->global->MULTICURRENCY_USE_ORIGIN_TX) && !empty($objectsrc->multicurrency_tx))	$currency_tx = $objectsrc->multicurrency_tx;
			}

			$datetmp=dol_mktime(12,0,0,$_POST['remonth'],$_POST['reday'],$_POST['reyear']);
			$dateinvoice=($datetmp==''?(empty($conf->global->MAIN_AUTOFILL_DATE)?-1:''):$datetmp);
			$datetmp=dol_mktime(12,0,0,$_POST['echmonth'],$_POST['echday'],$_POST['echyear']);
			$datedue=($datetmp==''?-1:$datetmp);
		}
		elseif($element == 'requestcashdeplacement')
		{
			$lCodefacture = false;
			$lCodepurchase = false;
			$classname = ucfirst($subelement);
			//viene de un descargo modulo finint
			require_once DOL_DOCUMENT_ROOT.'/finint/class/requestcashdeplacementext.class.php';
			$objectsrc = new Requestcashdeplacementext($db);
			$res = $objectsrc->fetch(GETPOST('originid'));
			$objectsrc->fetch_lines();

			$objectsrc->total_ttc = $objectsrc->lines[0]->price;
			//solo para efectos de mostrar el calculo del iva y el total_ht
			$lines = new stdClass();
			$tvacalc = array();
			$tvaht = array();
			$tvattc = array();
			$tvatx = array();
			$k = 1;
			$pu = price2num($objectsrc->lines[0]->subprice);
			$price_base_type = 'HT';
			if ($conf->global->PRICE_TAXES_INCLUDED)
			{
				$pu = price2num($objectsrc->lines[0]->price);
				$price_base_type = 'TTC';
			}
			$lines->fk_unit = $objectsrc->lines[0]->fk_unit;
			$lines->qty = $objectsrc->lines[0]->qty;
			$qty = $objectsrc->lines[0]->qty;
			$lines->price = $objectsrc->lines[0]->price;
			$lines->fk_product = 0;
			$amount_ice = 0;
			$discount = 0;
			if ($discount>0) $remise_percent = 0;
			$objectadd->code_facture = $objectsrc->code_facture;

			include DOL_DOCUMENT_ROOT.'/fiscal/include/calclinefiscal.inc.php';
			$objectsrc->total_tva = $lines->total_tva;
			$objectsrc->total_ht = $lines->total_ht;

			if ($res==0)
				setEventMessages($langs->trans('Errornorecordexist'),null,'errors');
			elseif ($res < 0)
				setEventMessages($objectsrc->error,$objectsrc->errors,'errors');
			else
			{
				$projectid			= (!empty($objectsrc->fk_projet_dest)?$objectsrc->fk_projet_dest:'');
				$projecttaskid		= (!empty($objectsrc->fk_projet_task_dest)?$objectsrc->fk_projet_task_dest:'');
				//$ref_client			= (!empty($objectsrc->ref_client)?$object->ref_client:'');

				$soc = $objectsrc->fk_soc;
				$_POST['socid'] = $objectsrc->fk_soc;
				if (isset($_GET['socid']) || isset($_POST['socid']))
				{
					$objectsrc->fk_soc = GETPOST('socid');
				}
				$_POST['socid'] = $objectsrc->fk_soc;
				if ($objectsrc->fk_soc>0)
				{
					$societe=new Societe($db);
					$societe->fetch($objectsrc->fk_soc);
					if (!empty($conf->multicurrency->enabled) && !empty($societe->multicurrency_code)) $currency_code = $societe->multicurrency_code;
					if ($societe->tva_intra != $objectsrc->fourn_nit)
					{
						//buscamos el nit correcto
						require_once DOL_DOCUMENT_ROOT.'/finint/class/societeext.class.php';
						$soc = new Societeext($db);
						$filtersoc = " AND (t.code_client = '".$objectsrc->fourn_nit."'";
						$filtersoc.= " OR t.code_fournisseur = '".$objectsrc->fourn_nit."'";
						$filtersoc.= " OR t.tva_intra = '".$objectsrc->fourn_nit."')";
						$ress = $soc->fetchAll('','',0,0,array(1=>1),'AND',$filtersoc,true);
						if ($ress ==1)
						{
							$_POST['socid'] = $soc->id;
							$societe->fetch($soc->id);
							$nit = $societe->tva_intra;
						}
						elseif($res>1)
						{
							foreach ($soc->lines AS $s => $datasoc)
							{
								$_POST['socid'] = $datasoc->id;
								$nit = $datasoc->tva_intra;
							}
						}
						else
						{
							$_POST['socid'] = 0;
							setEventMessages($langs->trans('Theproviderisnotdefined'),null,'errors');
						}
					}
				}
				else
				{
					if ($objectsrc->fourn_nit)
					{
						//buscamos por el nit en societe
						require_once DOL_DOCUMENT_ROOT.'/finint/class/societeext.class.php';
						$soc = new Societeext($db);
						$filtersoc = " AND (t.code_client = '".$objectsrc->fourn_nit."'";
						$filtersoc.= " OR t.code_fournisseur = '".$objectsrc->fourn_nit."'";
						$filtersoc.= " OR t.tva_intra = '".$objectsrc->fourn_nit."')";
						$ress = $soc->fetchAll('','',0,0,array(1=>1),'AND',$filtersoc,true);
						if ($ress ==1)
						{
							$_POST['socid'] = $soc->id;
							$societe->fetch($soc->id);
							$nit = $societe->tva_intra;
						}
						elseif($res>1)
						{
							foreach ($soc->lines AS $s => $datasoc)
							{
								$_POST['socid'] = $datasoc->id;
								$nit = $datasoc->tva_intra;
							}
						}
						else
						{
							$_POST['socid'] = 0;
							setEventMessages($langs->trans('Theproviderisnotdefined'),null,'errors');
						}
					}
				}
				$cond_reglement_id 	= (!empty($objectsrc->cond_reglement_id)?$objectsrc->cond_reglement_id:(!empty($soc->cond_reglement_supplier_id)?$soc->cond_reglement_supplier_id:1));
				$mode_reglement_id 	= (!empty($objectsrc->mode_reglement_id)?$objectsrc->mode_reglement_id:(!empty($soc->mode_reglement_supplier_id)?$soc->mode_reglement_supplier_id:0));
				$fk_account         = (! empty($objectsrc->fk_account_from)?$objectsrc->fk_account_from:(! empty($soc->fk_account)?$soc->fk_account:0));
				if ($fk_account) $lCodeaccount = false;
				$remise_percent 	= (!empty($objectsrc->remise_percent)?$objectsrc->remise_percent:(!empty($soc->remise_percent)?$soc->remise_percent:0));
				$remise_absolue 	= (!empty($objectsrc->remise_absolue)?$objectsrc->remise_absolue:(!empty($soc->remise_absolue)?$soc->remise_absolue:0));
				$dateinvoice		= empty($conf->global->MAIN_AUTOFILL_DATE)?-1:'';

				if (!empty($conf->multicurrency->enabled))
				{
					if (!empty($objectsrc->multicurrency_code)) $currency_code = $objectsrc->multicurrency_code;
					if (!empty($conf->global->MULTICURRENCY_USE_ORIGIN_TX) && !empty($objectsrc->multicurrency_tx))	$currency_tx = $objectsrc->multicurrency_tx;
				}

				$datetmp=dol_mktime(12,0,0,$_POST['remonth'],$_POST['reday'],$_POST['reyear']);
				$datetmp= $objectsrc->fourn_date;
				$dateinvoice=($datetmp==''?(empty($conf->global->MAIN_AUTOFILL_DATE)?-1:''):$datetmp);
				$datetmp=dol_mktime(12,0,0,$_POST['echmonth'],$_POST['echday'],$_POST['echyear']);
				$datedue=($datetmp==''?-1:$datetmp);
				$code_facture = $objectsrc->code_facture;
				$code_type_purchase = $objectsrc->code_type_purchase;

				$nit = $objectsrc->fourn_nit;
				$nfiscal = $objectsrc->fourn_facture;
				$num_autoriz = $objectsrc->fourn_numaut;
				$cod_control = $objectsrc->fourn_codecont;
				$_POST['label'] = $objectsrc->detail;

			}
		}
	}
	else
	{
		$cond_reglement_id 	= $societe->cond_reglement_supplier_id;
		$mode_reglement_id 	= $societe->mode_reglement_supplier_id;
		$fk_account         = $societe->fk_account;
		$datetmp=dol_mktime(12,0,0,$_POST['remonth'],$_POST['reday'],$_POST['reyear']);
		$dateinvoice=($datetmp==''?(empty($conf->global->MAIN_AUTOFILL_DATE)?-1:''):$datetmp);
		$datetmp=dol_mktime(12,0,0,$_POST['echmonth'],$_POST['echday'],$_POST['echyear']);
		$datedue=($datetmp==''?-1:$datetmp);

		if (!empty($conf->multicurrency->enabled) && !empty($soc->multicurrency_code)) $currency_code = $soc->multicurrency_code;
	}


	if ($action == 'addconf')
	{
		$aPost[$object->id] = $_POST;
		$datefacture=dol_mktime(12,0,0,$_POST['remonth'],$_POST['reday'],$_POST['reyear']);
		//vamos a validar
		$error=0;
		if (empty(GETPOST('code_facture'))|| GETPOST('code_facture')=='-1')
		{
			setEventMessage($langs->trans('ErrorFieldRequired',$langs->transnoentities('Typefiscal')), 'errors');
			$action='create';
			$_GET['socid']=$_POST['socid'];
			$error++;
		}
		if (empty(GETPOST('code_type_purchase'))|| GETPOST('code_type_purchase')=='-1')
		{
			setEventMessage($langs->trans('ErrorFieldRequired',$langs->transnoentities('Purchasedestination')), 'errors');
			$action='create';
			$_GET['socid']=$_POST['socid'];
			$error++;
		}
		if (GETPOST('socid','int')<1)
		{
			setEventMessages($langs->trans('ErrorFieldRequired',$langs->transnoentities('Supplier')), null, 'errors');
			$action='create';
			$error++;
		}
		$lNit = false;
		if (GETPOST('code_facture') && GETPOST('code_facture') != '-1')
		{
			$res = $objTypefacture->fetch(0,GETPOST('code_facture'));
			if ($res == 1)
			{
				if ($objTypefacture->nit_required) $lNit = true;
			}
		}
		if ($lNit)
		{
			//vamos a validar el codigo de control
			$code_control = dol_strtoupper(GETPOST('cod_control'));
			if ($code_control != '0')
			{
				$aCodecontrol = explode('-',$code_control);

				if (dol_strlen($code_control)<11 || dol_strlen($code_control)>14)
				{
					$error++;
					setEventMessages($langs->trans('Complete el codigo de control'),null,'errors');
				}
				if (count($aCodecontrol)>0)
				{
					foreach ($aCodecontrol AS $j => $data)
					{
						if (dol_strlen($data)<2 || dol_strlen($data)>2)
						{
							$error++;
							setEventMessages($langs->trans('Falta o sobra caracteres en código de control'),null,'errors');
						}
					}
				}
			}
			if (empty(GETPOST('nit_company','alpha')))
			{
				setEventMessages($langs->trans('ErrorFieldRequired',$langs->transnoentities('Nitcompany')), null, 'errors');
				$action='create';
				$error++;
			}
			if (empty(GETPOST('nfiscal','alpha')))
			{
				setEventMessages($langs->trans('ErrorFieldRequired',$langs->transnoentities('Nfiscal')), null, 'errors');
				$action='create';
				$error++;
			}
			if (empty(GETPOST('num_autoriz','alpha')))
			{
				setEventMessages($langs->trans('ErrorFieldRequired',$langs->transnoentities('Numautoriz')), null, 'errors');
				$action='create';
				$error++;
			}
		}
		if ($datefacture == '')
		{
			setEventMessages($langs->trans('ErrorFieldRequired',$langs->transnoentities('DateFacture')), null, 'errors');
			$action='create';
			$_GET['socid']=$_POST['socid'];
			$error++;
		}

		if (!$error)
		{
			$_SESSION['aPost'] = serialize($aPost);
		//verificamos el tipo de factura a crear
			$obj = fetch_type_facture('',GETPOST('code_facture'));
			$label_facture = '';
			if ($obj->code == GETPOST('code_facture'))
			{
				$label_facture = $obj->label;
			}
			$formquestion=array(
				array('type' => 'hidden', 'name' => 'origin',   'label' => $langs->trans("Origin"),   'value' => GETPOST('origin')),
				array('type' => 'hidden', 'name' => 'originid',   'label' => $langs->trans("Originid"),   'value' => GETPOST('originid')),
				array('type' => 'hidden', 'name' => 'socid',   'label' => $langs->trans("Origin"),   'value' => GETPOST('socid')),
			);
			$help = '<br>'.$langs->trans('Not be able to undo the operation');
			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('Confirmcreate'), $langs->trans('ConfirmCreatefacture').' '.$langs->trans('As').' <b>'.$label_facture.'</b>'.$help, 'confirm_add', '', 0, 1);
			print $formconfirm;
		}
	}
	$private = 1;
	if ($code_facture)
	{
		if ($code_facture != $conf->global->FISCAL_CODE_FACTURE_PURCHASE)
			$private = 0;
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
			$("#socid").change(function() {
				document.add.action.value="create";
				document.add.submit();
			});

			$("#code_facture").change(function() {
				document.add.action.value="create";
				document.add.submit();
			});
		});';
		print '</script>'."\n";
	}

	print '<form name="add" action="'.$_SERVER["PHP_SELF"].'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="addconf">';
	print '<input type="hidden" name="origin" value="'.GETPOST('origin').'">';
	print '<input type="hidden" name="originid" value="'.GETPOST('originid').'">';
	print '<input type="hidden" name="fk_commande" value="'.$fk_commande.'">';
	if (!empty($currency_tx)) print '<input type="hidden" name="originmulticurrency_tx" value="' . $currency_tx . '">';

	dol_fiche_head();

	print '<table class="border" width="100%">';

	// Ref
	print '<tr><td class="titlefieldcreate">'.$langs->trans('Ref').'</td><td>'.$langs->trans('Draft').'</td></tr>';

	// code facture
	print '<tr><td class="fieldrequired">'.$langs->trans('Typefiscal').'</td><td colspan="2">';
	//print $form->load_type_facture('type_facture', -1, $seller, $buyer);
	$typefilter = 0;
	//si se desea habilitar un registro por defecto segun la configuracion descomentar la siguiente linea
	//if (empty($code_facture)) $code_facture = $conf->global->FISCAL_CODE_FACTURE_PURCHASE;
	if ($lCodefacture)
	{
		print $form->load_type_facture('code_facture',(GETPOST('code_facture')?GETPOST('code_facture'):$code_facture),1, 'code', false,$typefilter);
		print ' '.info_admin($langs->trans("Tipo de factura registrado en el módulo Fiscal/TipodeFactura  que establece los impuestos a aplicar en el registro de esta factura"),1);
	}
	else
	{
		$typefacture = new Ctypefacture($db);
		$typefacture->fetch(0,$code_facture);
		print $typefacture->label;
		print '<input type="hidden" name="code_facture" value="'.$code_facture.'">';
	}
	//load_tvaadd('tva_tx', (isset($_POST["tva_tx"])?$_POST["tva_tx"]:-1), $seller, $buyer);
	print '</td></tr>';

	//code type facture
	if($lCodepurchase)
	{
		//$code_type_purchase = $conf->global->PURCHASE_DESTINATION_CODE_PRODUCT;
		print '<tr><td class="fieldrequired">'.$langs->trans('Purchasedestination').'</td><td colspan="2">';
		print $form->load_type_purchase('code_type_purchase',(GETPOST('code_type_purchase')?GETPOST('code_type_purchase'):$code_type_purchase),1, 'code', false);
		print ' '.info_admin($langs->trans("Seleccione un clasificador para poder relacionar la información posteriormente segun “para que” se realizo la compra” (Ejemplo: Activo Fijo realizara una marca sobre esta factura para poder dar de alta el activo en el módulo de Activo Fijo"),1);
		print '</td></tr>';
	}
	else
	{
		//require_once DOL_DOCUMENT_ROOT.'/purchase/class/ctypepurchase.class.php';
		$ctypepurchase = new Ctypepurchase($db);
		$restype = $ctypepurchase->fetch(0,$code_type_purchase);
		print '<tr><td class="fieldrequired">'.$langs->trans('Purchasedestination').'</td><td colspan="2">';
		print '<input type="hidden" name="code_type_purchase" value="'.$code_type_purchase.'">';
		if ($restype>0)
			print $ctypepurchase->label;
		else
		{
			print '<input type="hidden" name="code_type_purchase" value="">';
			print $langs->trans('Errornorecordexist');
		}
		print '</td></tr>';
	}
	// Third party
	print '<tr><td class="fieldrequired">'.$langs->trans('Supplier').'</td>';
	print '<td>';

	if (GETPOST('socid') > 0)
	{
		print $societe->getNomUrl(1);
		print '<input type="hidden" name="socid" value="'.GETPOST('socid','int').'">';
	}
	else
	{
		print $form->select_company(GETPOST('socid','int'), 'socid', 's.fournisseur = 1', 'SelectThirdParty');
	}
	print '</td></tr>';

	// Ref supplier
	//print '<tr><td class="fieldrequired">'.$langs->trans('RefSupplier').'</td><td><input name="ref_supplier" value="'.(isset($_POST['ref_supplier'])?$_POST['ref_supplier']:'').'" type="text"></td>';
	//print '</tr>';
	if ($conf->fiscal->enabled)
	{
		$res = $objentity->fetchAll('','',0,0,array(),'AND');
		$showempty = false;
		if ($res>0) $showempty = true;
		list($nb,$options) = $objentityadd->select_entity($campo='nit',(GETPOST('nit_company')?GETPOST('nit_company'):$conf->global->MAIN_INFO_TVAINTRA),1,$showempty);
		if ($nb<0) $lViewedit = false;
		print "<tr $bc[$var]>";
		print '<td>';
		print $langs->trans('Nitcompany').'</td><td>';
		print '<select name="nit_company">'.$options.'</select>';
		print '</td>';
		print '</tr>';
	}

	$fieldrequired = '';
	if ($code_facture == $conf->global->FISCAL_CODE_FACTURE_PURCHASE) $fieldrequired = 'fieldrequired';
	// NIT
	print '<tr class="datfiscal">';
	print '<td class="'.$fieldrequired.'">'.$langs->trans('Datos de la factura').'</td><td><input size="13" maxlength="50" name="nit" value="'.(GETPOST('nit')?GETPOST('nit'):$nit).'" type="text" placeholder="'.$langs->trans('NIT').'" onKeyPress="return checkIt(event)">';
	print '<input name="nfiscal" size="9" maxlength="10" id="nfiscal" value="'.(GETPOST('nfiscal')?GETPOST('nfiscal'):$nfiscal).'" type="text" placeholder="'.$langs->trans('Facturenumber').'" onKeyPress="return checkIt(event)">';
	print '<input name="num_autoriz" size="12" maxlength="30" id="num_autoriz" value="'.(GETPOST('num_autoriz')?GETPOST('num_autoriz'):$num_autoriz).'" type="text" placeholder="'.$langs->trans('Numberautoriz').'" onKeyPress="return checkIt(event)">';
	print '<input name="cod_control" size="14" maxlength="16" id="cod_control" value="'.(isset($_POST['cod_control'])?$_POST['cod_control']:$cod_control).'" type="text" placeholder="'.$langs->trans('Codcontrol').'" pattern="[A-F0-9-]{1,14}" title="'.$langs->trans('OnlylettersfromABCDEFnumbers0123456789andhyphen').' '.$langs->trans('Youmustenteraccordingtotheformat').' AA-BB-CC-00-11 '.'">';
	print '<input name="ndui" size="12" id="ndui" maxlength="20" value="'.(isset($_POST['ndui'])?$_POST['ndui']:$ndui).'" type="text" placeholder="'.$langs->trans('Dui').'">';

	print '</td>';
	print '</tr>';

	print '<tr class="datnfiscal"><td class="'.$fieldrequired.'">'.$langs->trans('Datos requeridos').'</td><td><input name="nit_" value="'.(isset($_POST['nit'])?$_POST['nit']:$nit).'" type="text" placeholder="'.$langs->trans('CI/Documents').'">';
	print '<input name="nfiscal_" id="nfiscal" value="0" type="hidden">';
	print '<input name="num_autoriz_" id="num_autoriz" value="0" type="hidden">';
	print '<input name="cod_control_" id="cod_control" value="" type="hidden">';
	print '</td>';
	print '</tr>';

	print '<tr><td valign="top" class="fieldrequired">'.$langs->trans('Type').'</td><td colspan="2">';
	print '<table class="nobordernopadding">'."\n";

	// Standard invoice
	print '<tr height="18"><td width="16px" valign="middle">';
	print '<input type="radio" name="type" value="0"'.($_POST['type']==0?' checked':'').'>';
	print '</td><td valign="middle">';
	$desc=$form->textwithpicto($langs->trans("InvoiceStandardAsk"),$langs->transnoentities("InvoiceStandardDesc"),1);
	print $desc;
	print '</td></tr>'."\n";

	/*
	 // Deposit
	print '<tr height="18"><td width="16px" valign="middle">';
	print '<input type="radio" name="type" value="3"'.($_POST['type']==3?' checked':'').'>';
	print '</td><td valign="middle">';
	$desc=$form->textwithpicto($langs->trans("InvoiceDeposit"),$langs->transnoentities("InvoiceDepositDesc"),1);
	print $desc;
	print '</td></tr>'."\n";

	// Proforma
	if (! empty($conf->global->FACTURE_USE_PROFORMAT))
	{
	print '<tr height="18"><td width="16px" valign="middle">';
	print '<input type="radio" name="type" value="4"'.($_POST['type']==4?' checked':'').'>';
	print '</td><td valign="middle">';
	$desc=$form->textwithpicto($langs->trans("InvoiceProForma"),$langs->transnoentities("InvoiceProFormaDesc"),1);
	print $desc;
	print '</td></tr>'."\n";
	}

	// Replacement
	print '<tr height="18"><td valign="middle">';
	print '<input type="radio" name="type" value="1"'.($_POST['type']==1?' checked':'');
	if (! $options) print ' disabled';
	print '>';
	print '</td><td valign="middle">';
	$text=$langs->trans("InvoiceReplacementAsk").' ';
	$text.='<select class="flat" name="fac_replacement"';
	if (! $options) $text.=' disabled';
	$text.='>';
	if ($options)
	{
	$text.='<option value="-1">&nbsp;</option>';
	$text.=$options;
	}
	else
	{
	$text.='<option value="-1">'.$langs->trans("NoReplacableInvoice").'</option>';
	}
	$text.='</select>';
	$desc=$form->textwithpicto($text,$langs->transnoentities("InvoiceReplacementDesc"),1);
	print $desc;
	print '</td></tr>';

	// Credit note
	print '<tr height="18"><td valign="middle">';
	print '<input type="radio" name="type" value="2"'.($_POST['type']==2?' checked':'');
	if (! $optionsav) print ' disabled';
	print '>';
	print '</td><td valign="middle">';
	$text=$langs->transnoentities("InvoiceAvoirAsk").' ';
	//	$text.='<input type="text" value="">';
	$text.='<select class="flat" name="fac_avoir"';
	if (! $optionsav) $text.=' disabled';
	$text.='>';
	if ($optionsav)
	{
	$text.='<option value="-1">&nbsp;</option>';
	$text.=$optionsav;
	}
	else
	{
	$text.='<option value="-1">'.$langs->trans("NoInvoiceToCorrect").'</option>';
	}
	$text.='</select>';
	$desc=$form->textwithpicto($text,$langs->transnoentities("InvoiceAvoirDesc"),1);
	print $desc;
	print '</td></tr>'."\n";
	*/
	print '</table>';
	print '</td></tr>';

	// Label
	print '<tr><td>'.$langs->trans('Shortdescription').'</td><td><input size="30" name="label" value="'.dol_escape_htmltag(GETPOST('label')).'" type="text"></td></tr>';

	// Date creation
	print '<tr><td>'.$langs->trans('DateCreation').'</td><td>';
	print dol_print_date(dol_now(),'dayhour');
	print '</td></tr>';

	// Date invoice
	print '<tr><td class="fieldrequired">'.$langs->trans('DateFacture').'</td><td>';
	$form->select_date($dateinvoice,'','','','',"add",1,1);
	print '</td></tr>';

	// Due date
	print '<tr><td>'.$langs->trans('DateMaxPayment').'</td><td>';
	$form->select_date($datedue,'ech','','','',"add",1,1);
	print '</td></tr>';

	// Payment term
	print '<tr><td class="nowrap">'.$langs->trans('PaymentConditionsShort').'</td><td colspan="2">';
	$form->select_conditions_paiements(isset($_POST['cond_reglement_id'])?$_POST['cond_reglement_id']:$cond_reglement_id, 'cond_reglement_id');
	print '</td></tr>';

	// Payment mode
	print '<tr><td>'.$langs->trans('PaymentMode').'</td><td colspan="2">';
	$form->select_types_paiements(isset($_POST['mode_reglement_id'])?$_POST['mode_reglement_id']:$mode_reglement_id, 'mode_reglement_id', 'DBIT');
	print '</td></tr>';


	// Bank Account
	if ($lCodeaccount)
	{
		print '<tr><td>'.$langs->trans('BankAccount').'</td><td colspan="2">';
		$form->select_comptes($fk_account, 'fk_account', 0, '', 1);
		print '</td></tr>';
	}
	else
	{
		print '<input type="hidden" name="fk_account" value="'.$fk_account.'">';
	}

	// Multicurrency
	if (! empty($conf->multicurrency->enabled))
	{
		print '<tr>';
		print '<td>'.fieldLabel('Currency','multicurrency_code').'</td>';
		print '<td colspan="2" class="maxwidthonsmartphone">';
		print $form->selectMultiCurrency($currency_code, 'multicurrency_code');
		print '</td></tr>';
	}

	// Project
	if (! empty($conf->projet->enabled))
	{
		$formproject = new FormProjets($db);

		$langs->load('projects');
		print '<tr><td>' . $langs->trans('Project') . '</td><td colspan="2">';
		$formproject->select_projects((empty($conf->global->PROJECT_CAN_ALWAYS_LINK_TO_ALL_SUPPLIERS)?$societe->id:-1), $projectid, 'projectid', 0, 0, 1, 1);

		if ($projecttaskid)
			print '<input type="hidden" name="projettaskid" value="'.$projecttaskid.'">';
		print '</td></tr>';
	}

	// Incoterms
	if (!empty($conf->incoterm->enabled))
	{
		print '<tr>';
		print '<td><label for="incoterm_id">'.$form->textwithpicto($langs->trans("IncotermLabel"), $objectsrc->libelle_incoterms, 1).'</label></td>';
		print '<td colspan="3" class="maxwidthonsmartphone">';
		print $form->select_incoterms((!empty($objectsrc->fk_incoterms) ? $objectsrc->fk_incoterms : ''), (!empty($objectsrc->location_incoterms)?$objectsrc->location_incoterms:''));
		print '</td></tr>';
	}

	// Public note
	print '<tr><td>'.$langs->trans('NotePublic').'</td>';
	print '<td>';
	$note_public = $object->getDefaultCreateValueFor('note_public');
	$doleditor = new DolEditor('note_public', $note_public, '', 80, 'dolibarr_notes', 'In', 0, false, true, ROWS_3, 70);
	print $doleditor->Create(1);
	print '</td>';
   // print '<td><textarea name="note" wrap="soft" cols="60" rows="'.ROWS_5.'"></textarea></td>';
	print '</tr>';

	// Private note
	print '<tr><td>'.$langs->trans('NotePrivate').'</td>';
	print '<td>';
	$note_private = $object->getDefaultCreateValueFor('note_private');
	$doleditor = new DolEditor('note_private', $note_private, '', 80, 'dolibarr_notes', 'In', 0, false, true, ROWS_3, 70);
	print $doleditor->Create(1);
	print '</td>';
	// print '<td><textarea name="note" wrap="soft" cols="60" rows="'.ROWS_5.'"></textarea></td>';
	print '</tr>';

	if (empty($reshook) && ! empty($extrafields->attribute_label))
	{
		print $object->showOptionals($extrafields, 'edit');
	}

	if (is_object($objectsrc))
	{
		print "\n<!-- ".$classname." info -->";
		print "\n";
		print '<input type="hidden" name="amount"         value="'.$objectsrc->total_ht.'">'."\n";
		print '<input type="hidden" name="total"          value="'.$objectsrc->total_ttc.'">'."\n";
		print '<input type="hidden" name="tva"            value="'.$objectsrc->total_tva.'">'."\n";
		print '<input type="hidden" name="origin"         value="'.$objectsrc->element.'">';
		print '<input type="hidden" name="originid"       value="'.$objectsrc->id.'">';

		$txt=$langs->trans($classname);
		if ($classname=='CommandeFournisseur') {
			$langs->load('orders');
			$txt=$langs->trans("SupplierOrder");
		}
		if ($classname=='Requestcashdeplacement') {
			$langs->load('finint');
			$txt=$langs->trans("Finint");
		}
		print '<tr><td>'.$txt.'</td><td colspan="2">'.$objectsrc->getNomUrl(1);
		// We check if Origin document (id and type is known) has already at least one invoice attached to it
		$objectsrc->fetchObjectLinked($originid,$origin,'','invoice_supplier');
		$cntinvoice=count($objectsrc->linkedObjects['invoice_supplier']);
		if ($cntinvoice>=1)
		{
			setEventMessages('WarningBillExist', null, 'warnings');
			echo ' ('.$langs->trans('LatestRelatedBill').end($objectsrc->linkedObjects['invoice_supplier'])->getNomUrl(1).')';
		}
		print '</td></tr>';
		print '<tr><td>'.$langs->trans('TotalHT').'</td><td colspan="2">'.price($objectsrc->total_ht).'</td></tr>';
		print '<tr><td>'.$langs->trans('TotalVAT').'</td><td colspan="2">'.price($objectsrc->total_tva)."</td></tr>";
		if ($mysoc->country_code=='ES')
		{
			if ($mysoc->localtax1_assuj=="1" || $object->total_localtax1 != 0) //Localtax1
			{
				print '<tr><td>'.$langs->transcountry("AmountLT1",$mysoc->country_code).'</td><td colspan="2">'.price($objectsrc->total_localtax1)."</td></tr>";
			}

			if ($mysoc->localtax2_assuj=="1" || $object->total_localtax2 != 0) //Localtax2
			{
				print '<tr><td>'.$langs->transcountry("AmountLT2",$mysoc->country_code).'</td><td colspan="2">'.price($objectsrc->total_localtax2)."</td></tr>";
			}
		}
		print '<tr><td>'.$langs->trans('TotalTTC').'</td><td colspan="2">'.price($objectsrc->total_ttc)."</td></tr>";

		if (!empty($conf->multicurrency->enabled))
		{
			print '<tr><td>' . $langs->trans('MulticurrencyTotalHT') . '</td><td colspan="2">' . price($objectsrc->multicurrency_total_ht) . '</td></tr>';
			print '<tr><td>' . $langs->trans('MulticurrencyTotalVAT') . '</td><td colspan="2">' . price($objectsrc->multicurrency_total_tva) . "</td></tr>";
			print '<tr><td>' . $langs->trans('MulticurrencyTotalTTC') . '</td><td colspan="2">' . price($objectsrc->multicurrency_total_ttc) . "</td></tr>";
		}
	}
	else
	{
		// TODO more bugs
		if (1==2 && ! empty($conf->global->PRODUCT_SHOW_WHEN_CREATE))
		{
			print '<tr class="liste_titre">';
			print '<td>&nbsp;</td>';
			print '<td>'.$langs->trans('Label').'</td>';
			print '<td align="right">'.$langs->trans('PriceUHT').'</td>';
			print '<td align="right">'.$langs->trans('VAT').'</td>';
			print '<td align="right">'.$langs->trans('Qty').'</td>';
			print '<td align="right">'.$langs->trans('PriceUTTC').'</td>';
			print '</tr>';

			for ($i = 1 ; $i < 9 ; $i++)
			{
				$value_qty = '1';
				$value_tauxtva = '';
				print '<tr><td>'.$i.'</td>';
				print '<td><input size="50" name="label'.$i.'" value="'.$value_label.'" type="text"></td>';
				print '<td align="right"><input type="text" size="8" name="amount'.$i.'" value="'.$value_pu.'"></td>';
				print '<td align="right">';
				print $form->load_tva('tauxtva'.$i,$value_tauxtva,$societe,$mysoc);
				print '</td>';
				print '<td align="right"><input type="text" size="3" name="qty'.$i.'" value="'.$value_qty.'"></td>';
				print '<td align="right"><input type="text" size="8" name="amountttc'.$i.'" value=""></td></tr>';
			}
		}
	}

	// Other options
	$parameters=array('colspan' => ' colspan="6"');
	$reshook=$hookmanager->executeHooks('formObjectOptions',$parameters,$object,$action); // Note that $action and $object may have been modified by hook

	// Bouton "Create Draft"
	print "</table>\n";

	dol_fiche_end();

	print '<div class="center">';
	print '<input type="submit" class="button" name="bouton" value="'.$langs->trans('CreateDraft').'">';
	print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	print '<input type="button" class="button" value="' . $langs->trans("Cancel") . '" onClick="javascript:history.go(-1)">';
	print '</div>';

	print "</form>\n";


	// Show origin lines
	if (is_object($objectsrc))
	{
		if ($objectsrc->element != 'requestcashdeplacement')
		{
			print '<br>';

			$title=$langs->trans('ProductsAndServices');
			print load_fiche_titre($title);

			print '<table class="noborder" width="100%">';

			$objectsrc->printOriginLinesList();

			print '</table>';
		}
	}
}
else
{
	if ($id > 0 || ! empty($ref))
	{
		/* *************************************************************************** */
		/*                                                                             */
		/* Fiche en mode visu ou edition                                               */
		/*                                                                             */
		/* *************************************************************************** */

		$now=dol_now();

		$productstatic = new Product($db);

		$object->fetch($id,$ref);
		$result=$object->fetch_thirdparty();
		if ($result < 0) dol_print_error($db);
		$resultadd = $objectadd->fetch('',$id);
		if ($resultadd < 0) dol_print_error($db);

		$societe = new Fournisseur($db);
		$result=$societe->fetch($object->socid);
		if ($result < 0) dol_print_error($db);

		// fetch optionals attributes and labels
		$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

		/*
		 *	View card
		 */
		$head = purchase_facturefourn_prepare_head($object);
		$titre=$langs->trans('SupplierInvoice');

		dol_fiche_head($head, 'card', $titre, 0, 'bill');

		// Clone confirmation
		if ($action == 'clone')
		{
			// Create an array for form
			$formquestion=array(
			//'text' => $langs->trans("ConfirmClone"),
			//array('type' => 'checkbox', 'name' => 'clone_content',   'label' => $langs->trans("CloneMainAttributes"),   'value' => 1)
			);
			// Paiement incomplet. On demande si motif = escompte ou autre
			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id,$langs->trans('CloneInvoice'),$langs->trans('ConfirmCloneInvoice',$object->ref),'confirm_clone',$formquestion,'yes', 1);
		}

		// Confirmation de la validation
		if ($action == 'valid')
		{
			 // on verifie si l'objet est en numerotation provisoire
			$objectref = substr($object->ref, 1, 4);
			if ($objectref == 'PROV')
			{
				$savdate=$object->date;
				$numref = $object->getNextNumRef($societe);
			}
			else
			{
				$numref = $object->ref;
			}

			$text=$langs->trans('ConfirmValidateBill',$numref);
			/*if (! empty($conf->notification->enabled))
			{
				require_once DOL_DOCUMENT_ROOT .'/core/class/notify.class.php';
				$notify=new Notify($db);
				$text.='<br>';
				$text.=$notify->confirmMessage('BILL_SUPPLIER_VALIDATE',$object->socid, $object);
			}*/
			$formquestion=array();

			$qualified_for_stock_change=0;
			if (empty($conf->global->STOCK_SUPPORTS_SERVICES))
			{
				$qualified_for_stock_change=$object->hasProductsOrServices(2);
			}
			else
			{
				$qualified_for_stock_change=$object->hasProductsOrServices(1);
			}

			if (! empty($conf->stock->enabled) && ! empty($conf->global->STOCK_CALCULATE_ON_SUPPLIER_BILL) && $qualified_for_stock_change)
			{
				$langs->load("stocks");
				require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
				$formproduct=new FormProduct($db);
				$formquestion=array(
				//'text' => $langs->trans("ConfirmClone"),
				//array('type' => 'checkbox', 'name' => 'clone_content',   'label' => $langs->trans("CloneMainAttributes"),   'value' => 1),
				//array('type' => 'checkbox', 'name' => 'update_prices',   'label' => $langs->trans("PuttingPricesUpToDate"),   'value' => 1),
					array('type' => 'other', 'name' => 'idwarehouse',   'label' => $langs->trans("SelectWarehouseForStockIncrease"),   'value' => $formproduct->selectWarehouses(GETPOST('idwarehouse'),'idwarehouse','',1)));
			}

			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('ValidateBill'), $text, 'confirm_valid', $formquestion, 1, 1, 240);

		}

		// Confirmation set paid
		if ($action == 'paid')
		{
			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('ClassifyPaid'), $langs->trans('ConfirmClassifyPaidBill', $object->ref), 'confirm_paid', '', 0, 1);

		}

		// Confirmation de la suppression de la facture fournisseur
		if ($action == 'delete')
		{
			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('DeleteBill'), $langs->trans('ConfirmDeleteBill'), 'confirm_delete', '', 0, 1);

		}
		if ($action == 'deletepaiement')
		{
			$payment_id = GETPOST('paiement_id');
			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id.'&paiement_id='.$payment_id, $langs->trans('DeletePayment'), $langs->trans('ConfirmDeletePayment'), 'confirm_delete_paiement', '', 0, 1);

		}

		// Confirmation to delete line
		if ($action == 'ask_deleteline')
		{
			$formconfirm=$form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id.'&lineid='.$lineid, $langs->trans('DeleteProductLine'), $langs->trans('ConfirmDeleteProductLine'), 'confirm_deleteline', '', 0, 1);
		}

		if (!$formconfirm) {
			$parameters=array('lineid'=>$lineid);
			$reshook = $hookmanager->executeHooks('formConfirm', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
			if (empty($reshook)) $formconfirm.=$hookmanager->resPrint;
			elseif ($reshook > 0) $formconfirm=$hookmanager->resPrint;
		}

		// Print form confirm
		print $formconfirm;


		/**
		 * 	Invoice
		 */
		print '<table class="border" width="100%">';

		$linkback = '<a href="'.DOL_URL_ROOT.'/fourn/facture/list.php'.(! empty($socid)?'?socid='.$socid:'').'">'.$langs->trans("BackToList").'</a>';

		// Ref
		print '<tr><td class="titlefield nowrap">'.$langs->trans("Ref").'</td><td colspan="4">';
		print $form->showrefnav($object, 'ref', $linkback, 1, 'ref', 'ref');
		print '</td>';
		print "</tr>\n";

		// Ref supplier
		//print '<tr><td>'.$form->editfieldkey("RefSupplier",'ref_supplier',$object->ref_supplier,$object,($object->statut<FactureFournisseur::STATUS_CLOSED && $user->rights->fournisseur->facture->creer)).'</td><td colspan="4">';
		//print $form->editfieldval("RefSupplier",'ref_supplier',$object->ref_supplier,$object,($object->statut<FactureFournisseur::STATUS_CLOSED && $user->rights->fournisseur->facture->creer));
		//print '</td></tr>';

		// Third party
		print '<tr><td>'.$langs->trans('Supplier').'</td><td colspan="4">'.$societe->getNomUrl(1,'supplier');
		print ' &nbsp; (<a href="'.DOL_URL_ROOT.'/fourn/facture/list.php?socid='.$object->socid.'">'.$langs->trans('OtherBills').'</a>)</td>';
		print '</tr>';

		// Type
		/*
		print '<tr><td>'.$langs->trans('Type').'</td><td colspan="4">';
		print $object->getLibType();
		if ($object->type == FactureFournisseur::TYPE_REPLACEMENT)
		{
			$facreplaced=new FactureFournisseur($db);
			$facreplaced->fetch($object->fk_facture_source);
			print ' ('.$langs->transnoentities("ReplaceInvoice",$facreplaced->getNomUrl(1)).')';
		}
		if ($object->type == FactureFournisseur::TYPE_CREDIT_NOTE)
		{
			$facusing=new FactureFournisseur($db);
			$facusing->fetch($object->fk_facture_source);
			print ' ('.$langs->transnoentities("CorrectInvoice",$facusing->getNomUrl(1)).')';
		}

		$facidavoir=$object->getListIdAvoirFromInvoice();
		if (count($facidavoir) > 0)
		{
			print ' ('.$langs->transnoentities("InvoiceHasAvoir");
			$i=0;
			foreach($facidavoir as $id)
			{
				if ($i==0) print ' ';
				else print ',';
				$facavoir=new FactureFournisseur($db);
				$facavoir->fetch($id);
				print $facavoir->getNomUrl(1);
			}
			print ')';
		}
		if (isset($facidnext) && $facidnext > 0)
		{
			$facthatreplace=new FactureFournisseur($db);
			$facthatreplace->fetch($facidnext);
			print ' ('.$langs->transnoentities("ReplacedByInvoice",$facthatreplace->getNomUrl(1)).')';
		}
		print '</td></tr>';
		*/
		print '<tr><td>'.$langs->trans('Typefacture').'</td><td colspan="4">';
		$restf = $objTypefacture->fetch(0,$objectadd->code_facture);
		if ($restf>0)
			print $objTypefacture->label;
		print '</td></tr>';

		print '<tr><td>'.$langs->trans('Destino').'</td><td colspan="4">';
		//require_once DOL_DOCUMENT_ROOT.'/purchase/class/ctypepurchase.class.php';
		$ctypepurchase = new Ctypepurchase($db);
		$restype = $ctypepurchase->fetch(0,$objectadd->code_type_purchase);
		if ($restype>0)
			print $ctypepurchase->label;
		print '</td></tr>';

		// Label
		print '<tr><td>'.$form->editfieldkey("Label",'label',$object->label,$object,($user->rights->fournisseur->facture->creer)).'</td>';
		print '<td colspan="3">'.$form->editfieldval("Label",'label',$object->label,$object,($user->rights->fournisseur->facture->creer)).'</td>';

		/*
		 * List of payments
		 */
		$nbrows=9; $nbcols=3;
		if (! empty($conf->projet->enabled)) $nbrows++;
		if (! empty($conf->banque->enabled)) { $nbrows++; $nbcols++; }
		if (! empty($conf->incoterm->enabled)) $nbrows++;

		// Local taxes
		if ($societe->localtax1_assuj=="1") $nbrows++;
		if ($societe->localtax2_assuj=="1") $nbrows++;

		print '<td rowspan="'.$nbrows.'" valign="top">';

		$sql = 'SELECT p.datep as dp, p.ref, p.num_paiement, p.rowid, p.fk_bank,';
		$sql.= ' c.id as paiement_type,';
		$sql.= ' pf.amount,';
		$sql.= ' ba.rowid as baid, ba.ref as baref, ba.label';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'paiementfourn as p';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'bank as b ON p.fk_bank = b.rowid';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'bank_account as ba ON b.fk_account = ba.rowid';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_paiement as c ON p.fk_paiement = c.id';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'paiementfourn_facturefourn as pf ON pf.fk_paiementfourn = p.rowid';
		$sql.= ' WHERE pf.fk_facturefourn = '.$object->id;
		$sql.= ' ORDER BY p.datep, p.tms';

		$result = $db->query($sql);
		if ($result)
		{
			$num = $db->num_rows($result);
			$i = 0; $totalpaye = 0;
			print '<table class="nobordernopadding paymenttable" width="100%">';
			print '<tr class="liste_titre">';
			print '<td>'.$langs->trans('Payments').'</td>';
			print '<td>'.$langs->trans('Date').'</td>';
			print '<td>'.$langs->trans('Type').'</td>';
			if (! empty($conf->banque->enabled)) print '<td align="right">'.$langs->trans('BankAccount').'</td>';
			print '<td align="right">'.$langs->trans('Amount').'</td>';
			print '<td width="18">&nbsp;</td>';
			print '</tr>';

			$var=true;
			if ($num > 0)
			{
				while ($i < $num)
				{
					$objp = $db->fetch_object($result);
					$var=!$var;
					print '<tr '.$bc[$var].'><td>';
					$paymentstatic->id=$objp->rowid;
					$paymentstatic->datepaye=$db->jdate($objp->dp);
					$paymentstatic->ref=$objp->ref;
					$paymentstatic->num_paiement=$objp->num_paiement;
					$paymentstatic->payment_code=$objp->payment_code;
					print $paymentstatic->getNomUrl(1);
					print '</td>';
					print '<td>'.dol_print_date($db->jdate($objp->dp), 'day') . '</td>';
					print '<td>';
					print $form->form_modes_reglement(null, $objp->paiement_type,'none').' '.$objp->num_paiement;
					print '</td>';
					if (! empty($conf->banque->enabled))
					{
						$bankaccountstatic->id=$objp->baid;
						$bankaccountstatic->ref=$objp->baref;
						$bankaccountstatic->label=$objp->baref;
						print '<td align="right">';
						if ($objp->baid > 0) print $bankaccountstatic->getNomUrl(1,'transactions');
						print '</td>';
					}
					print '<td align="right">'.price($objp->amount).'</td>';
					print '<td align="center">';
					if ($object->statut == FactureFournisseur::STATUS_VALIDATED && $object->paye == 0 && $user->societe_id == 0)
						{
							print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=deletepaiement&paiement_id='.$objp->rowid.'">';
							print img_delete();
							print '</a>';
						}
						print '</td>';
						print '</tr>';
						$totalpaye += $objp->amount;
						$i++;
					}
				}
				else
				{
					print '<tr '.$bc[$var].'><td colspan="'.$nbcols.'" class="opacitymedium">'.$langs->trans("None").'</td><td></td><td></td></tr>';
				}

				if ($object->paye == 0)
				{
					print '<tr><td colspan="'.$nbcols.'" align="right">'.$langs->trans('AlreadyPaid').' :</td><td align="right"><b>'.price($totalpaye).'</b></td><td></td></tr>';
					print '<tr><td colspan="'.$nbcols.'" align="right">'.$langs->trans("Billed").' :</td><td align="right" style="border: 1px solid;">'.($objTypefacture->retention?price($object->total_ht):price($object->total_ttc)).'</td><td></td></tr>';
					if ($objTypefacture->retention)
						$resteapayer = $object->total_ht - $totalpaye;
					else
						$resteapayer = $object->total_ttc - $totalpaye;

					print '<tr><td colspan="'.$nbcols.'" align="right">'.$langs->trans('RemainderToPay').' :</td>';
					print '<td align="right" style="border: 1px solid;" bgcolor="#f0f0f0"><b>'.price($resteapayer).'</b></td><td></td></tr>';
				}
				print '</table>';
				$db->free($result);
			}
			else
			{
				dol_print_error($db);
			}
			print '</td>';

			print '</tr>';

			$form_permission = $object->statut<FactureFournisseur::STATUS_CLOSED && $user->rights->fournisseur->facture->creer && $object->getSommePaiement() <= 0;

		// Date
			print '<tr><td>'.$form->editfieldkey("Date",'datef',$object->datep,$object,$form_permission,'datepicker').'</td><td colspan="3">';
			print $form->editfieldval("Date",'datef',$object->datep,$object,$form_permission,'datepicker');
			print '</td>';

		// Due date
			print '<tr><td>'.$form->editfieldkey("DateMaxPayment",'date_lim_reglement',$object->date_echeance,$object,$form_permission,'datepicker').'</td><td colspan="3">';
			print $form->editfieldval("DateMaxPayment",'date_lim_reglement',$object->date_echeance,$object,$form_permission,'datepicker');
			if ($action != 'editdate_lim_reglement' && $object->hasDelay()) {
				print img_warning($langs->trans('Late'));
			}
			print '</td>';

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
				$form->form_modes_reglement($_SERVER['PHP_SELF'].'?id='.$object->id, $object->mode_reglement_id, 'mode_reglement_id', 'DBIT');
			}
			else
			{
				$form->form_modes_reglement($_SERVER['PHP_SELF'].'?id='.$object->id, $object->mode_reglement_id, 'none', 'DBIT');
			}
			print '</td></tr>';

		// Multicurrency
			if (! empty($conf->multicurrency->enabled))
			{
			// Multicurrency code
				print '<tr>';
				print '<td>';
				print '<table class="nobordernopadding" width="100%"><tr><td>';
				print fieldLabel('Currency','multicurrency_code');
				print '</td>';
				if ($action != 'editmulticurrencycode' && ! empty($object->brouillon))
					print '<td align="right"><a href="' . $_SERVER["PHP_SELF"] . '?action=editmulticurrencycode&amp;id=' . $object->id . '">' . img_edit($langs->transnoentitiesnoconv('SetMultiCurrencyCode'), 1) . '</a></td>';
				print '</tr></table>';
				print '</td><td colspan="3">';
				if ($action == 'editmulticurrencycode') {
					$form->form_multicurrency_code($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->multicurrency_code, 'multicurrency_code');
				} else {
					$form->form_multicurrency_code($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->multicurrency_code, 'none');
				}
				print '</td></tr>';

			// Multicurrency rate
				print '<tr>';
				print '<td>';
				print '<table class="nobordernopadding" width="100%"><tr><td>';
				print fieldLabel('CurrencyRate','multicurrency_tx');
				print '</td>';
				if ($action != 'editmulticurrencyrate' && ! empty($object->brouillon))
					print '<td align="right"><a href="' . $_SERVER["PHP_SELF"] . '?action=editmulticurrencyrate&amp;id=' . $object->id . '">' . img_edit($langs->transnoentitiesnoconv('SetMultiCurrencyCode'), 1) . '</a></td>';
				print '</tr></table>';
				print '</td><td colspan="3">';
				if ($action == 'editmulticurrencyrate') {
					$form->form_multicurrency_rate($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->multicurrency_tx, 'multicurrency_tx', $object->multicurrency_code);
				} else {
					$form->form_multicurrency_rate($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->multicurrency_tx, 'none', $object->multicurrency_code);
				}
				print '</td></tr>';
			}

		// Bank Account
			print '<tr><td class="nowrap">';
			print '<table width="100%" class="nobordernopadding"><tr><td class="nowrap">';
			print $langs->trans('BankAccount');
			print '<td>';
			if ($action != 'editbankaccount' && $user->rights->fournisseur->facture->creer)
				print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=editbankaccount&amp;id='.$object->id.'">'.img_edit($langs->trans('SetBankAccount'),1).'</a></td>';
			print '</tr></table>';
			print '</td><td colspan="3">';
			if ($action == 'editbankaccount') {
				$form->formSelectAccount($_SERVER['PHP_SELF'].'?id='.$object->id, $object->fk_account, 'fk_account', 1);
			} else {
				$form->formSelectAccount($_SERVER['PHP_SELF'].'?id='.$object->id, $object->fk_account, 'none');
			}
			print "</td>";
			print '</tr>';

		// Status
			$alreadypaid=$object->getSommePaiement();
			print '<tr><td>'.$langs->trans('Status').'</td><td colspan="3">'.$object->getLibStatut(4,$alreadypaid).'</td></tr>';

			if ($objectadd->code_facture == $conf->global->FISCAL_CODE_FACTURE_PURCHASE || $objectadd->code_facture == $conf->global->FISCAL_CODE_FACTURE_ENERGY)
			{
				if ($objectadd->fk_facture_fourn == $object->id)
				{
					$object->total_ht = $objectadd->amountfiscal - $object->total_tva;
				}
			//print '<tr><td>'.$langs->trans('AmountHT').'</td><td align="right">'.price(price2num($object->total_ht,'MU'),1,$langs,0,-1,-1,$conf->currency).'</td><td colspan="2" align="left">&nbsp;</td></tr>';
				print '<tr><td>'.$langs->trans('AmountVAT').'</td><td colspan="3" align="right">'.price(price2num($object->total_tva,'MU'),1,$langs,0,-1,-1,$conf->currency).'</td><td colspan="2" align="left">';
			//if (GETPOST('calculationrule')) $calculationrule=GETPOST('calculationrule','alpha');
			//else $calculationrule=(empty($conf->global->MAIN_ROUNDOFTOTAL_NOT_TOTALOFROUND)?'totalofround':'roundoftotal');
			//if ($calculationrule == 'totalofround') $calculationrulenum=1;
			//else  $calculationrulenum=2;
			//$s=$langs->trans("ReCalculate").' ';
			//$s.='<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=calculate&calculationrule=totalofround">'.$langs->trans("Mode1").'</a>';
			//$s.=' / ';
			//$s.='<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=calculate&calculationrule=roundoftotal">'.$langs->trans("Mode2").'</a>';
			//print $form->textwithtooltip($s, $langs->trans("CalculationRuleDesc",$calculationrulenum).'<br>'.$langs->trans("CalculationRuleDescSupplier"), 2, 1, img_picto('','help'));
				print '</td></tr>';
			}
			else
			{
			//buscamos en facture fourn det fiscal
				$objectdetfiscal->get_sum_taxes($id);
				foreach ($objectdetfiscal->aData AS $code_facture => $data)
				{
					if($code_facture != $conf->global->FISCAL_CODE_FACTURE_PURCHASE && $code_facture != $conf->global->FISCAL_CODE_FACTURE_ENERGY)
					{
						$objfacture = fetch_type_tva(0,$code_facture);
						print '<tr><td>'.(strlen($objfacture->label)>45?dol_trunc($objfacture->label,45):$objfacture->label).'</td><td colspan="3" align="right">'.price(price2num($data['total_tva'],'MU'),1,$langs,0,-1,-1,$conf->currency).'</td><td colspan="2" align="left">&nbsp;</td></tr>';
					}
				}
			//print '<tr><td>'.$langs->trans('Importeneto').'</td><td align="right">'.price(price2num($object->total_ht,'MU'),1,$langs,0,-1,-1,$conf->currency).'</td><td colspan="2" align="left">&nbsp;</td></tr>';

			}

		// Amount
		//print '<tr><td>'.$langs->trans('AmountHT').'</td><td colspan="3">'.price($object->total_ht,1,$langs,0,-1,-1,$conf->currency).'</td></tr>';
		//print '<tr><td>'.$langs->trans('AmountVAT').'</td><td>'.price($object->total_tva,1,$langs,0,-1,-1,$conf->currency).'</td><td colspan="2" align="left">';
		//if (GETPOST('calculationrule')) $calculationrule=GETPOST('calculationrule','alpha');
		//else $calculationrule=(empty($conf->global->MAIN_ROUNDOFTOTAL_NOT_TOTALOFROUND)?'totalofround':'roundoftotal');
		//if ($calculationrule == 'totalofround') $calculationrulenum=1;
		//else  $calculationrulenum=2;
		//$s=$langs->trans("ReCalculate").' ';
		//$s.='<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=calculate&calculationrule=totalofround">'.$langs->trans("Mode1").'</a>';
		//$s.=' / ';
		//$s.='<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=calculate&calculationrule=roundoftotal">'.$langs->trans("Mode2").'</a>';
		//print $form->textwithtooltip($s, $langs->trans("CalculationRuleDesc",$calculationrulenum).'<br>'.$langs->trans("CalculationRuleDescSupplier"), 2, 1, img_picto('','help'));
		//print '</td></tr>';

		// Amount Local Taxes
		//TODO: Place into a function to control showing by country or study better option
		if ($societe->localtax1_assuj=="1") //Localtax1
		{
			print '<tr><td>'.$langs->transcountry("AmountLT1",$societe->country_code).'</td>';
			print '<td colspan="3">'.price($object->total_localtax1,1,$langs,0,-1,-1,$conf->currency).'</td>';
			print '</tr>';
		}
		if ($societe->localtax2_assuj=="1") //Localtax2
		{
			print '<tr><td>'.$langs->transcountry("AmountLT2",$societe->country_code).'</td>';
			print '<td colspan="3">'.price($object->total_localtax2,1,$langs,0,-1,-1,$conf->currency).'</td>';
			print '</tr>';
		}
		//buscamos la tabla adicional
		if ($objectadd->fk_facture_fourn == $object->id && $objectadd->code_facture == $conf->global->FISCAL_CODE_FACTURE_PURCHASE)
		{
				//mostramos los datos adicionales
			print '<tr><td>'.$langs->trans('Base amount for tax credit').'</td><td colspan="3" align="right">'.price($objectadd->amountfiscal,1,$langs,0,-1,-1,$conf->currency).'</td><td colspan="2" align="left">&nbsp;</td></tr>';
				//print '<tr><td>'.$langs->trans('').'</td><td align="right">'.price($objectadd->amountnofiscal,1,$langs,0,-1,-1,$conf->currency).'</td><td colspan="2" align="left">&nbsp;</td></tr>';
		}
		//se debe mostrar segun el tipo de factura
		if ($objTypefacture->retention)
		{
			print '<tr><td>'.$langs->trans('Amount').'</td><td colspan="3" align="right">'.price($object->total_ht,1,$langs,0,-1,-1,$conf->currency).'</td></tr>';
		}
		else
			print '<tr><td>'.$langs->trans('AmountTTC').'</td><td colspan="3" align="right">'.price($object->total_ttc,1,$langs,0,-1,-1,$conf->currency).'</td></tr>';

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

		// Project
		if (! empty($conf->projet->enabled))
		{
			$langs->load('projects');
			print '<tr>';
			print '<td>';

			print '<table class="nobordernopadding" width="100%"><tr><td>';
			print $langs->trans('Project');
			print '</td>';
			if ($action != 'classify')
			{
				print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=classify&amp;id='.$object->id.'">';
				print img_edit($langs->trans('SetProject'),1);
				print '</a></td>';
			}
			print '</tr></table>';

			print '</td><td colspan="3">';
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
			if ($user->rights->fournisseur->facture->creer) print '<a href="'.DOL_URL_ROOT.'/fourn/facture/card.php?facid='.$object->id.'&action=editincoterm">'.img_edit().'</a>';
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
		$cols = 4;
		include DOL_DOCUMENT_ROOT . '/core/tpl/extrafields_view.tpl.php';

		print '</table><br>';

		if (! empty($conf->global->MAIN_DISABLE_CONTACTS_TAB))
		{
			$blocname = 'contacts';
			$title = $langs->trans('ContactsAddresses');
			include DOL_DOCUMENT_ROOT.'/core/tpl/bloc_showhide.tpl.php';
		}

		if (! empty($conf->global->MAIN_DISABLE_NOTES_TAB))
		{
			$colwidth=20;
			$blocname = 'notes';
			$title = $langs->trans('Notes');
			include DOL_DOCUMENT_ROOT.'/core/tpl/bloc_showhide.tpl.php';
		}


		/*
		 * Lines
		 */
		//$result = $object->getLinesArray();


		print '	<form name="addproduct" id="addproduct" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.(($action != 'editline')?'#add':'#line_'.GETPOST('lineid')).'" method="POST">
		<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">
		<input type="hidden" name="action" value="' . (($action != 'editline') ? 'addline' : 'updateline') . '">
		<input type="hidden" name="mode" value="">
		<input type="hidden" name="id" value="'.$object->id.'">
		<input type="hidden" name="socid" value="'.$societe->id.'">
		';

		if (! empty($conf->use_javascript_ajax) && $object->statut == FactureFournisseur::STATUS_DRAFT) {
			include DOL_DOCUMENT_ROOT . '/core/tpl/ajaxrow.tpl.php';
		}

		print '<table id="tablelines" class="noborder noshadow" width="100%">';

		global $forceall, $senderissupplier, $dateSelector, $inputalsopricewithtax;
		$forceall=1; $senderissupplier=1; $dateSelector=0; $inputalsopricewithtax=1;

		//pasamos la variable al objeto principal
		$object->retention = $objTypefacture->retention;
		//Asignamos valor 0 a fk_request_cash
		$fk_request_cash_det = 0;
		// Show object lines
		if (! empty($object->lines))
		{
			//recuperamos de la tabla adicional para enviar datos
			$fk_request_cash_det = $object->printObjectLinesadd($action, $societe, $mysoc, $lineid, 1);
		}

		$num=count($object->lines);

		// Form to add new line
		if ($object->statut == FactureFournisseur::STATUS_DRAFT && $user->rights->fournisseur->facture->creer)
			{
				if ($action != 'editline' && !$fk_request_cash_det )
				{
					$var = true;

				// Add free products/services
					$object->formAddObjectLineadd(1, $societe, $mysoc);

					$parameters = array();
				$reshook = $hookmanager->executeHooks('formAddObjectLine', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
			}
		}

		print '</table>';

		print '</form>';

		dol_fiche_end();

		if ($action != 'presend')
		{
			/*
			 * Boutons actions
			 */

			print '<div class="tabsAction">';

			$parameters = array();
			$reshook = $hookmanager->executeHooks('addMoreActionsButtons', $parameters, $object, $action);

			if (empty($reshook))
			{
				$lButtonpay = true;
				//verificamos si su origen es finint
				$res = $getutil->get_element_element($object->id,$object->element,'target');
				if ($res==1 && $getutil->lines[0]->sourcetype == 'requestcashdeplacement')
				{
					$lButtonpay = false;
				}
				elseif(count($getutil->lines)>0)
				{
					//recorremos para ver si es de finint
					foreach ((array) $getutil->lines AS $j => $linef)
					{
						if ($linef->sourcetype == 'requestcashdeplacement')
							$lButtonpay = false;
					}
				}
				// Modify a validated invoice with no payments
				if ($lButtonpay && $object->statut == FactureFournisseur::STATUS_VALIDATED && $action != 'edit' && $object->getSommePaiement() == 0 && $user->rights->fournisseur->facture->creer)
					{
						print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&amp;action=edit">'.$langs->trans('Modify').'</a>';
					}

				// Reopen a standard paid invoice
					if (($object->type == FactureFournisseur::TYPE_STANDARD || $object->type == FactureFournisseur::TYPE_REPLACEMENT) && ($object->statut == 2 || $object->statut == 3))
				// A paid invoice (partially or completely)
						{
					if (! $facidnext && $object->close_code != 'replaced')	// Not replaced by another invoice
					{
						print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&amp;action=reopen">'.$langs->trans('ReOpen').'</a>';
					}
					else
					{
						print '<span class="butActionRefused" title="'.$langs->trans("DisabledBecauseReplacedInvoice").'">'.$langs->trans('ReOpen').'</span>';
					}
				}

				// Send by mail
				if ($lButtonpay && ($object->statut == FactureFournisseur::STATUS_VALIDATED || $object->statut == FactureFournisseur::STATUS_CLOSED))
					{
						if (empty($conf->global->MAIN_USE_ADVANCED_PERMS) || $user->rights->fournisseur->supplier_invoice_advance->send)
						{
							print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&amp;action=presend&amp;mode=init">'.$langs->trans('SendByMail').'</a>';
						}
						else print '<a class="butActionRefused" href="#">'.$langs->trans('SendByMail').'</a>';
					}

				//$lButtonpay = true;
				//verificamos si su origen es finint
				//$res = $getutil->get_element_element($object->id,$object->element,'target');

					if ($res==1 && $getutil->lines[0]->sourcetype == 'requestcashdeplacement')
					{
						$lButtonpay = false;
						if ($conf->finint->enabled)
						{
						//buscamos para habilitar el boton de ir al descargo
							require_once DOL_DOCUMENT_ROOT.'/finint/class/requestcashdeplacementext.class.php';
							require_once DOL_DOCUMENT_ROOT.'/finint/class/requestcashext.class.php';
							$objreq = new Requestcashdeplacementext($db);
							$objreq->fetch($getutil->lines[0]->fk_source);
						// Make payments
							if ($object->fk_statut == 1 && $user->rights->finint->efe->leer)
							{
								$objcash = new Requestcash($db);
								$objcash->fetch($objreq->fk_request_cash);
						//print '<a class="butAction" href="'.DOL_URL_ROOT.'/finint/request/card.php?id='.$objreq->fk_request_cash.'&amp;action=discharg">'.$objcash->ref.'</a>';
								print $objcash->getNomUrl(1,'', 0, 24, '','&action=discharg');
							// must use facid because id is for payment id not invoice
							}
						}

					}
					elseif(count($getutil->lines)>0)
					{
					//recorremos para ver si es de finint
						foreach ((array) $getutil->lines AS $j => $linef)
						{
							if ($linef->sourcetype == 'requestcashdeplacement')
							{
								$lButtonpay = false;
								$fk_request_cash = $linef->fk_source;
							}
						}
						if ($conf->finint->enabled && $fk_request_cash > 0)
						{
					//buscamos para habilitar el boton de ir al descargo
							require_once DOL_DOCUMENT_ROOT.'/finint/class/requestcashdeplacementext.class.php';
							require_once DOL_DOCUMENT_ROOT.'/finint/class/requestcashext.class.php';
							$objreq = new Requestcashdeplacementext($db);
							$objreq->fetch($$fk_request_cash);
					// Make payments
							if ($object->fk_statut == 1 && $user->rights->finint->efe->leer)
							{
								$objcash = new Requestcash($db);
								$objcash->fetch($objreq->fk_request_cash);
								print $objcash->getNomUrl(1,'', 0, 24, '','&action=discharg');
							}
						}

					}
					if ($lButtonpay)
					{
					// Make payments
						if ($action != 'edit' && $object->statut == FactureFournisseur::STATUS_VALIDATED && $object->paye == 0  && $user->societe_id == 0)
							{
								print '<a class="butAction" href="paiement.php?facid='.$object->id.'&amp;action=create'.($object->fk_account>0?'&amp;accountid='.$object->fk_account:'').'">'.$langs->trans('DoPayment').'</a>';
							// must use facid because id is for payment id not invoice
							}

					// Classify paid
							if ($action != 'edit' && $object->statut == FactureFournisseur::STATUS_VALIDATED && $object->paye == 0  && $user->societe_id == 0)
								{
									print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=paid"';
									print '>'.$langs->trans('ClassifyPaid').'</a>';

						//print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=paid">'.$langs->trans('ClassifyPaid').'</a>';
								}
							}
				// Validate
							if ($action != 'edit' && $object->statut == FactureFournisseur::STATUS_DRAFT)
								{
									if (count($object->lines))
									{
										if ((empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->fournisseur->facture->creer))
											|| (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->fournisseur->supplier_invoice_advance->validate)))
										{
											print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=valid"';
											print '>'.$langs->trans('Validate').'</a>';
										}
										else
										{
											print '<a class="butActionRefused" href="#" title="'.dol_escape_htmltag($langs->trans("NotAllowed")).'"';
											print '>'.$langs->trans('Validate').'</a>';
										}
									}
								}

				// Create event
				if ($conf->agenda->enabled && ! empty($conf->global->MAIN_ADD_EVENT_ON_ELEMENT_CARD)) 	// Add hidden condition because this is not a "workflow" action so should appears somewhere else on page.
				{
					print '<div class="inline-block divButAction"><a class="butAction" href="' . DOL_URL_ROOT . '/comm/action/card.php?action=create&amp;origin=' . $object->element . '&amp;originid=' . $object->id . '&amp;socid=' . $object->socid . '">' . $langs->trans("AddAction") . '</a></div>';
				}


				// Clone
				if ($lButtonpay && $action != 'edit' && $user->rights->fournisseur->facture->creer)
				{
					print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=clone&amp;socid='.$object->socid.'">'.$langs->trans('ToClone').'</a>';
				}

				// Delete
				if ($action != 'edit' && $user->rights->fournisseur->facture->supprimer)
				{
					if ($object->getSommePaiement()) {
						print '<div class="inline-block divButAction"><a class="butActionRefused" href="#" title="' . $langs->trans("DisabledBecausePayments") . '">' . $langs->trans('Delete') . '</a></div>';
					} else {
						print '<a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a>';
					}
				}
				//listar pedidos del proveedor
				if ($object->statut == FactureFournisseur::STATUS_DRAFT && $action != 'edit' && $user->rights->purchase->com->leer)
					{

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
						print '<a data-toggle="modal" href="#selorder" class="butAction">'.$langs->trans('Selectorder').'</a>';

						print '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'">';
						print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
						print '<input type="hidden" name="action" value="importitem">';
						print '<input type="hidden" name="element" value="order_supplier">';
						print '<input type="hidden" name="mode" value="">';
						print '<input type="hidden" name="id" value="'.$object->id.'">';
						print '<input type="hidden" name="socid" value="'.$societe->id.'">';

						print '<div id="selorder" class="modal fade in" style="display: none;">';
						print '<div class="modal-dialog">';
						print '<div class="modal-content">';
						print '<div class="modal-header">';
						print '<a data-dismiss="modal" class="close">×</a>';
						print '<h3>'.$langs->trans('Orders').'</h3>';
						print '</div>';
						print '<div class="modal-body">';
						print '<h4>'.$langs->trans('Seleccione los items').'</h4>';
						include DOL_DOCUMENT_ROOT.'/purchase/tpl/listorder.tpl.php';
						print '</div>';
						print '<div class="modal-footer">';
						print '<button class="btn btn-success" name="save">Guardar</button>';
						print '<a href="#" data-dismiss="modal" class="btn">Cerrar</a>';
						print '</div>';
						print '</div>';
						print '</div>';
						print '</div>';
						print '</form>';
					}
					print '</div>';
					print '<br>';

					if ($action != 'edit')
					{
						print '<div class="fichecenter"><div class="fichehalfleft">';
					//print '<table width="100%"><tr><td width="50%" valign="top">';
					//print '<a name="builddoc"></a>'; // ancre

					/*
					 * Documents generes
					*/

					$ref=dol_sanitizeFileName($object->ref);
					$subdir = get_exdir($object->id,2,0,0,$object,'invoice_supplier').$ref;
					$filedir = $conf->fournisseur->facture->dir_output.'/'.get_exdir($object->id,2,0,0,$object,'invoice_supplier').$ref;
					$urlsource=$_SERVER['PHP_SELF'].'?id='.$object->id;
					$genallowed=$user->rights->fournisseur->facture->creer;
					$delallowed=$user->rights->fournisseur->facture->supprimer;
					$modelpdf=(! empty($object->modelpdf)?$object->modelpdf:(empty($conf->global->INVOICE_SUPPLIER_ADDON_PDF)?'':$conf->global->INVOICE_SUPPLIER_ADDON_PDF));

					print $formfile->showdocuments('facture_fournisseur',$subdir,$filedir,$urlsource,$genallowed,$delallowed,$modelpdf,1,0,0,40,0,'','','',$societe->default_lang);

					$somethingshown=$formfile->numoffiles;

					// Linked object block
					$somethingshown = $form->showLinkedObjectBlock($object);

					// Show links to link elements
					$linktoelem = $form->showLinkToObjectBlock($object,array('supplier_order'));
					if ($linktoelem) print '<br>'.$linktoelem;


					print '</div><div class="fichehalfright"><div class="ficheaddleft">';
					//print '</td><td valign="top" width="50%">';
					//print '<br>';

					// List of actions on element
					include_once DOL_DOCUMENT_ROOT.'/core/class/html.formactions.class.php';
					$formactions=new FormActions($db);
					$somethingshown=$formactions->showactions($object,'invoice_supplier',$socid,0,'listaction'.($genallowed?'largetitle':''));

					print '</div></div></div>';
					//print '</td></tr></table>';
				}
			}
			else
			{
				echo 'no ingresa ';
			}
		}

		/*
		 * Show mail form
		 */
		if (GETPOST('modelselected')) {
			$action = 'presend';
		}
		if ($action == 'presend')
		{
			$ref = dol_sanitizeFileName($object->ref);
			include_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
			$fileparams = dol_most_recent_file($conf->fournisseur->facture->dir_output.'/'.get_exdir($object->id,2,0,0,$object,'invoice_supplier').$ref, preg_quote($ref,'/').'([^\-])+');
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
				$outputlangs->load('bills');
			}

			// Build document if it not exists
			if (! $file || ! is_readable($file))
			{
				$result = $object->generateDocument(GETPOST('model')?GETPOST('model'):$object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
				if ($result <= 0)
				{
					dol_print_error($db,$object->error,$object->errors);
					exit;
				}
				$fileparams = dol_most_recent_file($conf->fournisseur->facture->dir_output.'/'.get_exdir($object->id,2,0,0,$object,'invoice_supplier').$ref, preg_quote($ref,'/').'([^\-])+');
				$file=$fileparams['fullname'];
			}

			print '<div class="clearboth"></div>';
			print '<br>';
			print load_fiche_titre($langs->trans('SendBillByMail'));

			dol_fiche_head('');

			// Cree l'objet formulaire mail
			include_once DOL_DOCUMENT_ROOT.'/core/class/html.formmail.class.php';
			$formmail = new FormMail($db);
			$formmail->param['langsmodels']=(empty($newlang)?$langs->defaultlang:$newlang);
			$formmail->fromtype = 'user';
			$formmail->fromid   = $user->id;
			$formmail->fromname = $user->getFullName($langs);
			$formmail->frommail = $user->email;
			$formmail->trackid='sin'.$object->id;
			if (! empty($conf->global->MAIN_EMAIL_ADD_TRACK_ID) && ($conf->global->MAIN_EMAIL_ADD_TRACK_ID & 2))	// If bit 2 is set
			{
				include DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
				$formmail->frommail=dolAddEmailTrackId($formmail->frommail, 'sin'.$object->id);
			}
			$formmail->withfrom=1;
			$liste=array();
			foreach ($object->thirdparty->thirdparty_and_contact_email_array(1) as $key=>$value)	$liste[$key]=$value;
			$formmail->withto=GETPOST("sendto")?GETPOST("sendto"):$liste;
			$formmail->withtocc=$liste;
			$formmail->withtoccc=$conf->global->MAIN_EMAIL_USECCC;
			$formmail->withtopic=$outputlangs->trans('SendBillRef','__REF__');
			$formmail->withfile=2;
			$formmail->withbody=1;
			$formmail->withdeliveryreceipt=1;
			$formmail->withcancel=1;
			// Tableau des substitutions
			$formmail->setSubstitFromObject($object);
			$formmail->substit['__SUPPLIERINVREF__']=$object->ref;

			//Find the good contact adress
			$custcontact='';
			$contactarr=array();
			$contactarr=$object->liste_contact(-1,'external');

			if (is_array($contactarr) && count($contactarr)>0) {
				foreach($contactarr as $contact) {
					if ($contact['libelle']==$langs->trans('TypeContact_invoice_supplier_external_BILLING')) {
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
			$formmail->param['models']='invoice_supplier_send';
			$formmail->param['models_id']=GETPOST('modelmailselected','int');
			$formmail->param['facid']=$object->id;
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
	}
}

//if ($action == 'viewgr' || $action == 'viewit' || $action=='viewre')
//{
print '
<!-- ./wrapper -->

<!-- Bootstrap 3.3.6 -->

<script src="../js/bootstrap.min.js"></script>
<!-- FastClick -->
<script src="../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../js/app.min.js"></script>
<!-- Sparkline -->
<script src="../plugins/sparkline/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="../plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="../plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- SlimScroll 1.3.0 -->
<script src="../plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- ChartJS 1.0.1 -->
<script src="../plugins/chartjs/Chart.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="../js/pages/dashboard2.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../js/demo.js"></script>
';
//}
// End of page
llxFooter();
$db->close();
