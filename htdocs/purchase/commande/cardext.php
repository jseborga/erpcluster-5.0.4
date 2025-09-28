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
	dol_include_once('/orgman/class/pdepartamentext.class.php');
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
$aArraytype = array(1=>$langs->trans('Contract'),2=>$langs->trans('Purchase order'),3=>$langs->trans('Service order'),4=>$langs->trans('Other'));
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
if ($conf->orgman->enabled)
	$objDepartament = new Pdepartamentext($db);

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


/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/
$now = dol_now();
$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$objectadd,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	if ($cancel)
	{
		if ($action != 'addlink')
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/purchase/commande/cardext.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}
		if ($id > 0 || ! empty($ref)) $ret = $objectadd->fetch($id,$ref);
		$action='';
	}

	// Action to update record
	if ($action == 'update')
	{
		$error=0;


		$objectadd->fk_commande_fournisseur=$id;
		//$objectadd->object=GETPOST('object','alpha');
		//$objectadd->fk_object=GETPOST('fk_object','int');
		$objectadd->fk_departament=GETPOST('fk_departament','int');
		if (empty($objectadd->fk_departament)) $objectadd->fk_departament = 0;
		$objectadd->ref_contrat=GETPOST('ref_contrat','alpha');
		$objectadd->term=GETPOST('term','int');
		$objectadd->ref_term=GETPOST('ref_term','int');
		$objectadd->type=GETPOST('type','alpha');
		$objectadd->advance=GETPOST('advance','int');
		$objectadd->order_proceed=GETPOST('order_proceed','int');
		$objectadd->delivery_place=GETPOST('delivery_place','alpha');
		$objectadd->designation_fiscal=GETPOST('designation_fiscal','int');
		$objectadd->designation_supervisor=GETPOST('designation_supervisor','int');
		//$objectadd->fk_poa_prev=GETPOST('fk_poa_prev','int');
		//$objectadd->code_facture=GETPOST('code_facture','alpha');
		//$objectadd->code_type_purchase=GETPOST('code_type_purchase','alpha');
		//$objectadd->amountfiscal=GETPOST('amountfiscal','alpha');
		//$objectadd->amountnofiscal=GETPOST('amountnofiscal','alpha');
		//$objectadd->discount=GETPOST('discount','alpha');
		//$objectadd->fk_user_create=GETPOST('fk_user_create','int');
		$objectadd->fk_user_mod=$user->id;
		$objectadd->datem = $now;
		//$objectadd->status=GETPOST('status','int');

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

		if (! $error)
		{
			$result=$objectadd->update($user);
			if ($result > 0)
			{
				$action='';
			}
			else
			{
				// Creation KO
				if (! empty($objectadd->errors)) setEventMessages(null, $objectadd->errors, 'errors');
				else setEventMessages($objectadd->error, null, 'errors');
				$action='edit';
			}
		}
		else
		{
			$action='edit';
		}
	}

	// Action to delete
	if ($action == 'confirm_delete' && $abc)
	{
		//$result=$objectadd->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/purchase/list.php',1));
			exit;
		}
		else
		{
			if (! empty($objectadd->errors)) setEventMessages(null, $objectadd->errors, 'errors');
			else setEventMessages($objectadd->error, null, 'errors');
		}
	}
}





//array defined
$aArraytype = array(1=>$langs->trans('Contract'),2=>$langs->trans('Purchase order'),3=>$langs->trans('Service order'),4=>$langs->trans('Other'));
$aTypeprocess = array(1=>array('WELL' => $langs->trans('Goods')),0=>array('OTHERSERVICE'=>$langs->trans('Otherservice'),'SERVICE'=>$langs->trans('Service')));
$aTerm = array(1=>$langs->trans('D.C.'),2=>$langs->trans('D.H.'),3=>$langs->trans('Fixed term'));


/*
 * View
 */
$title = $langs->trans('Order');
$morejs = array('/purchase/js/purchase.js');
$morecss = array('/purchase/css/style.css','/purchase/css/bootstrap.min.css','/includes/jquery/plugins/datatables/media/css/dataTables.bootstrap.css','/includes/jquery/plugins/datatables/media/css/jquery.dataTables.css',);
$morecss = array('/purchase/css/style.css','/includes/jquery/plugins/datatables/media/css/dataTables.bootstrap.css','/includes/jquery/plugins/datatables/media/css/jquery.dataTables.css',);
llxHeader('',$title,'','','','',$morejs,$morecss,0,0);


$form =	new	Formv($db);
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

if (! empty($object->id) && empty($action))
{
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
	dol_fiche_head($head, 'cardext', $title, 0, 'order');


	$formconfirm='';

	/*
	 * Confirmation de la suppression de la commande
	 */


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

	//datos del contrato
	print '<tr class="liste_titre">';
	print '<td colspan="3">'.$langs->trans("Detailcontract").'</td>';
	print "</tr>";

	if ($conf->global->PURCHASE_ADD_DETAIL_CONTRAT)
	{
		$objectadd->fetch(0,$object->id);
		// Ref contrat
		print '<tr><td>'.$langs->trans('Documentnumber').'</td><td>'.$objectadd->ref_contrat.'</td>';
		print '</tr>';
		// type contrat
		print '<tr><td>'.$langs->trans('Documenttype').'</td><td>';
		print $aArraytype[$objectadd->type];
		print '</td>';
		print '</tr>';
		// term
		print '<tr><td>'.$langs->trans('Term').'</td><td>'.$objectadd->term.'</td>';
		print '</tr>';
		// ref term
		print '<tr><td>'.$langs->trans('Typeofterm').'</td><td>';
		print $aTerm[$objectadd->ref_term];
		print '</td>';
		print '</tr>';

		// ref term
		if ($conf->orgman->enabled)
		{
			print '<tr><td>'.$langs->trans('Departament').'</td><td>';
			if ($objectadd->fk_departament>0)
			{
				$objDepartament->fetch($objectadd->fk_departament);
				print $objDepartament->getNomUrl().' '.$objDepartament->label;
			}
			print '</td>';
			print '</tr>';
		}
		// advance
		print '<tr><td>'.$langs->trans('Advancepayment').'</td><td>';
		print ($objectadd->advance?$langs->trans('Yes'):$langs->trans('No'));
		print '</td>';
		print '</tr>';
		// order proced
		print '<tr><td>'.$langs->trans('Ordertoproceed').'</td><td>';
		print ($objectadd->order_proceed?$langs->trans('Yes'):$langs->trans('No'));
		print '</td>';
		print '</tr>';
		// designation fiscal
		print '<tr><td>'.$langs->trans('Designationfiscal').'</td><td>';
		print ($objectadd->designation_fiscal?$langs->trans('Yes'):$langs->trans('No'));
		print '</td>';
		print '</tr>';
		// designation supervisor
		print '<tr><td>'.$langs->trans('Designationsupervisor').'</td><td>';
		print ($objectadd->designation_supervisor?$langs->trans('Yes'):$langs->trans('No'));
		print '</td>';
		print '</tr>';
		// date ini
		print '<tr><td>'.$langs->trans('Dateini').'</td><td>';
		print dol_print_date($objectadd->date_ini,'day');
		print '</td>';
		print '</tr>';
		// date fin
		print '<tr><td>'.$langs->trans('Datefin').'</td><td>';
		print dol_print_date($objectadd->date_fin,'day');
		print '</td>';
		print '</tr>';
		// date fin
		print '<tr><td>'.$langs->trans('Deliveryplace').'</td><td>';
		print $objectadd->delivery_place;
		print '</td>';
		print '</tr>';

	}
	print "</table><br>";
}
if ($object->id == $objectadd->fk_commande_fournisseur && $action=='edit')
{
	print load_fiche_titre($langs->trans('Order'));

	dol_htmloutput_events();

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
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="id" value="' . $object->id . '">';

	dol_fiche_head('');

	print '<table class="border" width="100%">';

	// Ref
	print '<tr><td class="titlefieldcreate">'.$langs->trans('Ref').'</td><td>'.$object->ref.'</td></tr>';

	if ($conf->global->PURCHASE_ADD_DETAIL_CONTRAT)
	{
		// Ref contrat
		print '<tr><td class="fieldrequired">'.$langs->trans('Documentnumber').'</td><td><input name="ref_contrat" type="text" value="'.(GETPOST('ref_contrat')?GETPOST('ref_contrat'):$objectadd->ref_contrat).'" required></td>';
		print '</tr>';
		// type contrat
		print '<tr><td class="fieldrequired">'.$langs->trans('Documenttype').'</td><td>';
		print $form->selectarray('type',$aArraytype,(GETPOST('type')?GETPOST('type'):$objectadd->type),1);
		print '</td>';
		print '</tr>';
		// term
		print '<tr><td class="fieldrequired">'.$langs->trans('Term').'</td><td><input name="term" type="text" value="'.(GETPOST('term')?GETPOST('term'):$objectadd->term).'" required></td>';
		print '</tr>';
		// ref term
		print '<tr><td class="fieldrequired">'.$langs->trans('Typeofterm').'</td><td>';
		print $form->selectarray('ref_term',$aTerm,(GETPOST('ref_term')?GETPOST('ref_term'):$objectadd->ref_term),1);
		print '</td>';
		print '</tr>';
		// Departament
		if ($conf->orgman->enabled)
		{
			print '<tr><td>'.$langs->trans('Departament').'</td><td>';
			print $form->select_departament($objectadd->fk_departament,'fk_departament','',0,1,'',0);
			print '</td>';
			print '</tr>';
		}
		// advance
		print '<tr><td >'.$langs->trans('Advancepayment').'</td><td>';
		print $form->selectyesno('advance',(GETPOST('advance')?GETPOST('advance'):$objectadd->advance),1);
		print '</td>';
		print '</tr>';
		// order proced
		print '<tr><td>'.$langs->trans('Ordertoproceed').'</td><td>';
		print $form->selectyesno('order_proceed',(GETPOST('order_proceed')?GETPOST('order_proceed'):$objectadd->order_proceed),1);
		print '</td>';
		print '</tr>';
		// designation fiscal
		print '<tr><td>'.$langs->trans('Designationfiscal').'</td><td>';
		print $form->selectyesno('designation_fiscal',(GETPOST('designation_fiscal')?GETPOST('designation_fiscal'):$objectadd->designation_fiscal),1);
		print '</td>';
		print '</tr>';
		// designation supervisor
		print '<tr><td>'.$langs->trans('Designationsupervisor').'</td><td>';
		print $form->selectyesno('designation_supervisor',(GETPOST('designation_supervisor')?GETPOST('designation_supervisor'):$objectadd->designation_supervisor),1);
		print '</td>';
		print '</tr>';
		// date ini
		print '<tr><td class="fieldrequired">'.$langs->trans('Dateini').'</td><td>';
		print $form->select_date(($date_ini?$date_ini:$objectadd->date_ini),'date_ini',0,0);
		print '</td>';
		print '</tr>';
		// date fin
		print '<tr><td class="fieldrequired">'.$langs->trans('Datefin').'</td><td>';
		print $form->select_date(($date_fin?$date_fin:$objectadd->date_fin),'date_fin',0,0);
		print '</td>';
		print '</tr>';
		// Lugar de entrega
		print '<tr><td class="fieldrequired">'.$langs->trans('Deliveryplace').'</td><td>';
		print '<input type="text" name="delivery_place" value="'.(GETPOST('delivery_place')?GETPOST('delivery_place'):$objectadd->delivery_place).'" required>';
		print '</td>';
		print '</tr>';
	}

	// Bouton "Create Draft"
	print "</table>\n";

	dol_fiche_end();

	print '<div class="center">';
	print '<input type="submit" class="button" name="bouton" value="'.$langs->trans('Keep').'">';
	print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	print '<input type="button" class="button" value="' . $langs->trans("Cancel") . '" onClick="javascript:history.go(-1)">';
	print '</div>';
	print "</form>\n";
}


print '<div	class="tabsAction">';

$parameters = array();
$reshook = $hookmanager->executeHooks('addMoreActionsButtons', $parameters, $objectadd, $action);
			// Note that $action and $object may have been
			// modified by hook
if (empty($reshook))
{

				// Modify
	if ($object->statut == 0)
	{
		if ($user->rights->fournisseur->commande->commander)
		{
			print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a>';
		}
	}
}
print '</div>';

// End of page
llxFooter();

$db->close();
