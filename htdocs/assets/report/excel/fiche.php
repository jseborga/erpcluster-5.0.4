<?php
/* Copyright (C) 2003-2006	Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2004-2013	Laurent Destailleur		<eldy@users.sourceforge.net>
 * Copyright (C) 2005		Marc Barilley / Ocebo	<marc@ocebo.com>
 * Copyright (C) 2005-2013	Regis Houssin			<regis.houssin@capnetworks.com>
 * Copyright (C) 2006		Andre Cianfarani		<acianfa@free.fr>
 * Copyright (C) 2010-2013	Juanjo Menent			<jmenent@2byte.es>
 * Copyright (C) 2011		Philippe Grand			<philippe.grand@atoo-net.com>
 * Copyright (C) 2012-2013	Christophe Battarel		<christophe.battarel@altairis.fr>
 * Copyright (C) 2012		Marcos García			<marcosgdf@gmail.com>
 * Copyright (C) 2013		Florian Henry			<florian.henry@open-concept.pro>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file htdocs/commande/fiche.php
 * \ingroup commande
 * \brief Page to show customer order
 */
if (!isset($_GET['dol_hide_topmenu']))
{
	$_GET['dol_hide_topmenu']=1;
	$_GET['dol_hide_leftmenu']=1;
}

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/modules/commande/modules_commande.php';
require_once DOL_DOCUMENT_ROOT . '/commande/class/commande.class.php';
require_once DOL_DOCUMENT_ROOT . '/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT . '/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT . '/product/stock/class/entrepot.class.php';
require_once DOL_DOCUMENT_ROOT . '/ventas/lib/account.lib.php';
require_once DOL_DOCUMENT_ROOT . '/ventas/lib/ventas.lib.php';
require_once DOL_DOCUMENT_ROOT . '/ventas/lib/price.lib.php';

//require_once DOL_DOCUMENT_ROOT . '/ventas/class/bankurladvance.class.php';
require_once DOL_DOCUMENT_ROOT . '/compta/paiement/class/paiement.class.php';
require_once DOL_DOCUMENT_ROOT . '/ventas/class/advance.class.php';
require_once DOL_DOCUMENT_ROOT . '/ventas/class/commandeadd.class.php';
require_once DOL_DOCUMENT_ROOT . '/ventas/class/commandesale.class.php';
require_once DOL_DOCUMENT_ROOT . '/ventas/class/html.formadd.class.php';
require_once DOL_DOCUMENT_ROOT . '/ventas/class/html.formv.class.php';
require_once DOL_DOCUMENT_ROOT . '/ventas/class/commandebankstatus.class.php';
require_once DOL_DOCUMENT_ROOT . '/ventas/class/factureadd.class.php';
require_once DOL_DOCUMENT_ROOT . '/ventas/class/html.formfileadd.class.php';
require_once(DOL_DOCUMENT_ROOT.'/ventas/class/bankstatus.class.php');
//require_once DOL_DOCUMENT_ROOT . '/ventas/class/vdossing.class.php';
require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';


if (!empty($conf->fabrication->enabled))
	require_once(DOL_DOCUMENT_ROOT.'/fabrication/class/commandeventa.class.php');

if (! empty($conf->propal->enabled))
	require DOL_DOCUMENT_ROOT . '/comm/propal/class/propal.class.php';
if (! empty($conf->projet->enabled)) {
	require DOL_DOCUMENT_ROOT . '/projet/class/project.class.php';
	require_once DOL_DOCUMENT_ROOT . '/core/class/html.formprojet.class.php';
}
require_once DOL_DOCUMENT_ROOT . '/core/class/doleditor.class.php';

$langs->load('orders');
$langs->load('sendings');
$langs->load('companies');
$langs->load('bills');
$langs->load('propal');
$langs->load('deliveries');
$langs->load('products');
//$conf->margin->enabled = 0;
if (! empty($conf->margin->enabled))
	$langs->load('margins');

if (empty($_SESSION['uid']))
{
	header('Location: '.DOL_URL_ROOT.'/ventas/index.php');
	exit;
}

$id = (GETPOST('id', 'int') ? GETPOST('id', 'int') : GETPOST('orderid', 'int'));
$ref = GETPOST('ref', 'alpha');
$socid = GETPOST('socid', 'int');
$action = GETPOST('action', 'alpha');
$confirm = GETPOST('confirm', 'alpha');
$lineid = GETPOST('lineid', 'int');
$origin = GETPOST('origin', 'alpha');
$originid = (GETPOST('originid', 'int') ? GETPOST('originid', 'int') : GETPOST('origin_id', 'int'));
 // For backward compatibility
$end = GETPOST('end');

$mesg = GETPOST('mesg');
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;
// PDF
$hidedetails = (GETPOST('hidedetails', 'int') ? GETPOST('hidedetails', 'int') : (! empty($conf->global->MAIN_GENERATE_DOCUMENTS_HIDE_DETAILS) ? 1 : 0));
$hidedesc = (GETPOST('hidedesc', 'int') ? GETPOST('hidedesc', 'int') : (! empty($conf->global->MAIN_GENERATE_DOCUMENTS_HIDE_DESC) ? 1 : 0));
$hideref = (GETPOST('hideref', 'int') ? GETPOST('hideref', 'int') : (! empty($conf->global->MAIN_GENERATE_DOCUMENTS_HIDE_REF) ? 1 : 0));

// Security check
if (! empty($user->societe_id))
	$socid = $user->societe_id;
if (!$user->rights->ventas->commande->creer)
	accessforbidden();

//$result = restrictedArea($user, 'ventas', $id);

//verificacion de version
$version = substr($conf->global->MAIN_VERSION_LAST_INSTALL,0,5);
if (!empty($conf->global->MAIN_VERSION_LAST_UPGRADE))
	$version = substr($conf->global->MAIN_VERSION_LAST_UPGRADE,0,5);
//cambiamos la version de texto a numero
$version = substr($version,0,3) * 1;
$lVersion = false;
if ($version >= 3.7)
	$lVersion = true;

$object      = new Commandeadd($db);
$objectadd   = new Commandeadd($db);
$objectsale  = new Commandesale($db);
$objentrepot = new Entrepot($db);
$extrafields = new ExtraFields($db);
if (!empty($conf->fabrication->enabled))
	$objectVenta = new Commandeventa($db);
$objectA     = new Account($db);
$objectAl    = new AccountLine($db);
$objuser     = new User($db);
$objsoc      = new Societe($db);
$objfactureadd = new Factureadd($db);
$commandebankstatus = new Commandebankstatus($db);
$objbankstat = new Bankstatus($db);
//verificamos el status de la cuenta
$resbank = $objbankstat->fetch_banklast($_SESSION['fkCajaid']);

//recuperando los saldos
$datesession = $objbankstat->date_register;
$datefin = dol_now();
$saldoBankUser = saldoAccount($_SESSION['fkCajaid'],$_SESSION['uid'],'',$datesession,$datefin);
$saldoBankUser += $objbankstat->amount+$objbankstat->amount_open;
if ($resbank <= 0)
{
	$datession = dol_now();
	$saldoBankUser = 0;
}

//$saldoBankUser = saldoAccount($_SESSION['fkCajaid'],$_SESSION['uid']);
//$saldoBankUser += 0;
$saldoBank = saldoAccount($_SESSION['fkCajaid']);
$saldoBank += 0;

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

//verificacion de versiones dolibarr
$version = substr($conf->global->MAIN_VERSION_LAST_INSTALL,0,5);
if (!empty($conf->global->MAIN_VERSION_LAST_UPGRADE))
	$version = substr($conf->global->MAIN_VERSION_LAST_UPGRADE,0,5);
//cambiamos la version de texto a numero
$version = substr($version,0,3) * 1;
$lVersion = false;
if ($version >= 3.7)
	$lVersion = true;
// Load object
if ($id > 0 || ! empty($ref)) {
	$ret = $object->fetch($id, $ref);
	$ret = $object->fetch_thirdparty();
	$objectadd->fetch($id,$ref);
}

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('ordercard'));

$permissionnote = $user->rights->ventas->commande->creer; // Used by the include of actions_setnotes.inc.php

/*
 * Actions
 */

$parameters = array('socid' => $socid);
$reshook = $hookmanager->executeHooks('doActions', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks

include DOL_DOCUMENT_ROOT . '/core/actions_setnotes.inc.php'; // Must be include, not includ_once

if ($action == 'addadvance' && $id && ! isset($_POST["cancel"]) && $user->rights->ventas->adv->crear)
{
	if (price2num($_POST["credit"]) > 0)
	{
		$amount = price2num($_POST["credit"]);
	}
	else
	{
		$amount = - price2num($_POST["debit"]);
	}
	$objuser->fetch($_SESSION['uid']);
	$dateop = dol_now();
	$operation=$_POST["operation"];
	$num_chq=$_POST["num_chq"];
	$label=$_POST["label"];
	$cat1=$_POST["cat1"];
	$cajaid = $_POST['cajaid'];
	if (empty($cajaid)) $cajaid = $_SESSION['fkCajaid'];
	if ($operation == 'CB')
	//tarjeta de credito
		$cajaid = $_SESSION['fk_banktcid'];
	if ($operation == 'CHQ')
	 //tarjeta de credito
		$cajaid = $_SESSION['fk_bankid'];
	$socid = GETPOST('socid');

	if (! $dateop)    $mesg=$langs->trans("ErrorFieldRequired",$langs->trans("Date"));
	if (! $operation) $mesg=$langs->trans("ErrorFieldRequired",$langs->trans("Type"));
	if (! $amount)    $mesg=$langs->trans("ErrorFieldRequired",$langs->trans("Amount"));
	// echo '<hr> '.$dateop.' '.$operation.' '.$label.' '.$amount.' '.$num_chq.' '.$cat1.' '.$cajaid.' '.$mesg;
	if (! $mesg)
	{
		$db->begin();

	// //actualizamos el commande
	// $objadd = new CommandeAdd($db);
	// $objadd->fetch($id);
	// $notepublic = $objadd->note_public;
	// $notepublic.= ' | '.$label.' '.$langs->trans('Foramount').' '.price($amount);

	// $object->note_public = $notepublic;
	// $res = $objadd->set_note_public($user,$notepublic);
	// if (!$res > 0)
	//   $error++;
	// //creamos el pago
	// // Creation of payment line
	// $paiement = new Advance($db);
	// $paiement->datepaye     = $datepaye;
	// $paiement->amounts      = array($amount);
	// $paiement->paiementid   = dol_getIdFromCode($db,$operation,'c_paiement');
	// $paiement->num_paiement = $num_chq;
	// $paiement->note         = $label;
	// $user->id = $_SESSION['uid'];
	// $paiement_id = $paiement->create($user, (GETPOST('closepaidinvoices')=='on'?1:0));
	// if ($paiement_id < 0)
	//   {
	//     setEventMessage($paiement->error.' xxxxx', 'errors');
	//     $error++;
	//   }
	//fin creacion del pago
	//si no error creamos el registro en bank

		if (!$error)
		{
		//echo '<hr>pai '.$paiement_id.' '.$cajaid;
		$objectA->fetch($cajaid); //tabla account
		$insertid = $objectA->addline($dateop, $operation, $label, $amount, $num_chq, $cat1, $objuser);
		// echo '<hr>insertid '.$insertid;
		// exit;
		if ($insertid > 0)
		{
			$error = 0;
				// $result=$objectA->update_fk_bank($insertid);
				// if ($result <= 0)
		//   {
				//     $error++;
				//     dol_print_error($db);
		//   }

		// if (! $error)
		//   {
		// 	//creamos el
		// 	//$result=$objectA->add_url_line($bank_line_id_from, $bank_line_id_to, DOL_URL_ROOT.'/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert');
		//   }
		// // Creation of payment line
		// $amounts = array($amount);
		// $bankurladv = new Bankurladvance($db);
		// $paiement_id = $bankurladv->createpaiement($dateop,$amount,4,'',$label);
		// // $paiement = new Paiement($db);
		// // $paiement->datepaye     = $dateop;
		// // $paiement->amounts      = $amounts;   // Array with all payments dispatching
		// // // $paiement->paiementid   = dol_getIdFromCode($db,$_POST['paiementcode'],'c_paiement');
		// // $paiement->paiementid   = 4;
		// // $paiement->num_paiement = $_POST['num_paiement'];
		// // $paiement->note         = $_POST['comment'];

		// if (! $error)
		//   {
		//     // $paiement_id = $paiement->create($user, (GETPOST('closepaidinvoices')=='on'?1:0));
		//   }

			if (! $error)
			{
		// $bankurladv = new Bankurladvance($db);
				$url='';
				$mode = 'advance';
				$url=DOL_URL_ROOT.'/compta/bank/ligne.php?rowid=';
				$result=$objectA->add_url_line($insertid, $insertid, $url, '(advance)', $mode);
				if ($result <= 0)
				{
					$error++;
					dol_print_error($db);
				}
			//company
				$societestatic = new Societe($db);
				$societestatic->fetch($socid);
				$result=$objectA->add_url_line($insertid,
					$socid,
					DOL_URL_ROOT.'/comm/'.($lVersion==true?'card':'fiche').'.php?socid=',
					$societestatic->name,
					'company'
					);
				if ($result <= 0)
				{
					$error++;
					dol_print_error($db);
				}

			}
			if (!$error)
			{
				$commandebankstatus->initAsSpecimen();
				$commandebankstatus->fk_commande = $id;
				$commandebankstatus->fk_bank_status = $_SESSION['fkBankStatus'];
				$commandebankstatus->fk_bank = $insertid;
				$commandebankstatus->detail = $label;
				$commandebankstatus->exchange = $conf->global->VENTA_EXCHANGE_RATE;
				$commandebankstatus->fk_user_create = $user->id;
				$commandebankstatus->date_create = dol_now();
				$commandebankstatus->tms = dol_now();
				$commandebankstatus->statut = 1;
				$res = $commandebankstatus->create($user);
				if (!$res>0)
					$error++;
			}
			if (! $error)
			{
				if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
					commande_pdf_create($db, $object, $object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);

				$db->commit();
				header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
				exit;
			}
			else
			{
				$db->rollback();
				$mesg=$errmsg;
			}
		}
		else
		{
			$db->rollback();
			$mesg=$object->error;
		}
	}
	else
	{
		$action='advance';
	}
}
else
	$action= 'advance';
}

// Action clone object
if ($action == 'confirm_clone' && $confirm == 'yes' && $user->rights->ventas->commande->creer)
{
	if (1==0 && ! GETPOST('clone_content') && ! GETPOST('clone_receivers'))
	{
		$mesg='<div class="error">'.$langs->trans("NoCloneOptionsSpecified").'</div>';
	}
	else
	{
		if ($object->id > 0)
		{
			// Because createFromClone modifies the object, we must clone it so that we can restore it later
			$orig = dol_clone($object);

			$result=$object->createFromClone($socid);
			if ($result > 0)
			{
				header("Location: ".$_SERVER['PHP_SELF'].'?id='.$result);
				exit;
			}
			else
			{
				setEventMessage($object->error, 'errors');
				$object = $orig;
				$action='';
			}
		}
	}
}

// Reopen a closed order
else if ($action == 'reopen' && $user->rights->ventas->commande->creer) {
	if ($object->statut == 3) {
		$result = $object->set_reopen($user);
		if ($result > 0)
		{
			header('Location: '.$_SERVER["PHP_SELF"].'?id='.$object->id);
			exit;
		}
		else
		{
			setEventMessage($object->error, 'errors');
		}
	}
}

// Suppression de la commande
else if ($action == 'confirm_delete' && $confirm == 'yes' && $user->rights->ventas->delcommande)
{

	$result = $object->delete($user);
	if ($result > 0) {
		header('Location: '.DOL_URL_ROOT.'/ventas/affIndex.php?menu=facturation');
		exit;
	}
	else {
		setEventMessage($object->error, 'errors');
	}
}

// Remove a product line
else if ($action == 'confirm_deleteline' && $confirm == 'yes' && $user->rights->ventas->commande->delitem)
{
	$result = $object->deleteline($lineid);
	if ($result > 0)
	{
		// Define output language
		$outputlangs = $langs;
		$newlang = '';
		if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id'))
			$newlang = GETPOST('lang_id');
		if ($conf->global->MAIN_MULTILANGS && empty($newlang))
			$newlang = $object->client->default_lang;
		if (! empty($newlang))
		{
			$outputlangs = new Translate("", $conf);
			$outputlangs->setDefaultLang($newlang);
		}
		if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE)) {
			$ret = $object->fetch($object->id);
			 // Reload to get new records
			commande_pdf_create($db, $object, $object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
		}
		header('Location: '.$_SERVER["PHP_SELF"].'?id='.$object->id);
		exit;
	}
	else
	{
		setEventMessage($object->error, 'errors');
	}
}

// Categorisation dans projet
else if ($action == 'classin' && $user->rights->ventas->commande->creer) {
	$object->setProject(GETPOST('projectid'));
}

// Add order
else if ($action == 'add' && $user->rights->ventas->commande->creer)
{
	$ref_client = GETPOST('ref_client');
	$fk_entrepot = GETPOST('fk_entrepot');
	$datecommande = dol_mktime(12, 0, 0, GETPOST('remonth'), GETPOST('reday'), GETPOST('reyear'));
	if (!$user->admin)
		$datecommande = dol_now();
	$datelivraison = dol_mktime(GETPOST('liv_hour'), GETPOST('liv_min'), 0, GETPOST('liv_month'), GETPOST('liv_day'), GETPOST('liv_year'));
	if ($datecommande == '') {
		$mesg = '<div class="error">' . $langs->trans('ErrorFieldRequired', $langs->transnoentities('Date')) . '</div>';
		$action = 'create';
		$error ++;
	}
	if ($datelivraison == '') {
		$mesg = '<div class="error">' . $langs->trans('ErrorFieldRequired', $langs->transnoentities('Datelivraison')) . '</div>';
		$action = 'create';
		$error ++;
	}
	if ($datelivraison < $datecommande) {
		$mesg = '<div class="error">' . $langs->trans('Error').': '.$langs->transnoentities('Datelivraison').' '.$langs->trans('Itpredates').' '.$langs->transnoentities('Datecommande') . '</div>';
		$action = 'create';
		$error ++;
	}

	if ($socid < 1) {
		setEventMessage($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Customer")), 'errors');
		$action = 'create';
		$error ++;
	}
	if ($fk_entrepot < 1) {
		setEventMessage($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Deliveryentrepot")), 'errors');
		$action = 'create';
		$error ++;
	}
	if (! $error)
	{
		$object->socid = $socid;
		$object->fetch_thirdparty();

		$db->begin();

		$object->date_commande = $datecommande;
		$object->note_private = GETPOST('note_private');
		$object->note_public = GETPOST('note_public');
		$object->source = GETPOST('source_id');
		$object->fk_project = GETPOST('projectid');
		$object->ref_client = GETPOST('ref_client');
		$object->modelpdf = GETPOST('model');
		$object->cond_reglement_id = GETPOST('cond_reglement_id');
		$object->mode_reglement_id = GETPOST('mode_reglement_id');
		$object->availability_id = GETPOST('availability_id');
		$object->demand_reason_id = GETPOST('demand_reason_id');
		$object->date_livraison = $datelivraison;
		$object->fk_delivery_address = GETPOST('fk_address');
		$object->contactid = GETPOST('contactidp');

		// If creation from another object of another module (Example: origin=propal, originid=1)
		if (! empty($origin) && ! empty($originid))
		{
			// Parse element/subelement (ex: project_task)
			$element = $subelement = $origin;
			if (preg_match('/^([^_]+)_([^_]+)/i', $origin, $regs)) {
				$element = $regs [1];
				$subelement = $regs [2];
			}

			// For compatibility
			if ($element == 'order') {
				$element = $subelement = 'commande';
			}
			if ($element == 'propal') {
				$element = 'comm/propal';
				$subelement = 'propal';
			}
			if ($element == 'contract') {
				$element = $subelement = 'contrat';
			}

			$object->origin = $origin;
			$object->origin_id = $originid;

			// Possibility to add external linked objects with hooks
			$object->linked_objects [$object->origin] = $object->origin_id;
			$other_linked_objects = GETPOST('other_linked_objects', 'array');
			if (! empty($other_linked_objects)) {
				$object->linked_objects = array_merge($object->linked_objects, $other_linked_objects);
			}
			// Fill array 'array_options' with data from add form
			$ret = $extrafields->setOptionalsFromPost($extralabels, $object);
			if ($ret < 0)
				$error ++;
			if (! $error)
			{
				$object_id = $object->create($user);

				if ($object_id > 0)
				{
					dol_include_once('/' . $element . '/class/' . $subelement . '.class.php');

					$classname = ucfirst($subelement);
					$srcobject = new $classname($db);

					dol_syslog("Try to find source object origin=" . $object->origin . " originid=" . $object->origin_id . " to add lines");
					$result = $srcobject->fetch($object->origin_id);
					if ($result > 0)
					{
						$lines = $srcobject->lines;
						if (empty($lines) && method_exists($srcobject, 'fetch_lines'))
						{
							$srcobject->fetch_lines();
							$lines = $srcobject->lines;
						}

						$fk_parent_line = 0;
						$num = count($lines);

						for($i = 0; $i < $num; $i ++)
						{
							$label = (! empty($lines [$i]->label) ? $lines [$i]->label : '');
							$desc = (! empty($lines [$i]->desc) ? $lines [$i]->desc : $lines [$i]->libelle);
							$product_type = (! empty($lines [$i]->product_type) ? $lines [$i]->product_type : 0);

							// Dates
							// TODO mutualiser
							$date_start = $lines [$i]->date_debut_prevue;
							if ($lines [$i]->date_debut_reel)
								$date_start = $lines [$i]->date_debut_reel;
							if ($lines [$i]->date_start)
								$date_start = $lines [$i]->date_start;
							$date_end = $lines [$i]->date_fin_prevue;
							if ($lines [$i]->date_fin_reel)
								$date_end = $lines [$i]->date_fin_reel;
							if ($lines [$i]->date_end)
								$date_end = $lines [$i]->date_end;

								// Reset fk_parent_line for no child products and special product
							if (($lines [$i]->product_type != 9 && empty($lines [$i]->fk_parent_line)) || $lines [$i]->product_type == 9) {
								$fk_parent_line = 0;
							}

							// Extrafields
							if (empty($conf->global->MAIN_EXTRAFIELDS_DISABLED) && method_exists($lines [$i], 'fetch_optionals'))
							{
								// For avoid conflicts if
								// trigger used
								$lines [$i]->fetch_optionals($lines [$i]->rowid);
								$array_option = $lines [$i]->array_options;
							}

							$result = $object->addline($desc, $lines [$i]->subprice, $lines [$i]->qty, $lines [$i]->tva_tx, $lines [$i]->localtax1_tx, $lines [$i]->localtax2_tx, $lines [$i]->fk_product, $lines [$i]->remise_percent, $lines [$i]->info_bits, $lines [$i]->fk_remise_except, 'HT', 0, $date_start, $date_end, $product_type, $lines [$i]->rang, $lines [$i]->special_code, $fk_parent_line, $lines [$i]->fk_fournprice, $lines [$i]->pa_ht, $label, $array_option);

							if ($result < 0) {
								$error ++;
								break;
							}

							// Defined the new fk_parent_line
							if ($result > 0 && $lines [$i]->product_type == 9) {
								$fk_parent_line = $result;
							}
						}

						// Hooks
						$parameters = array('objFrom' => $srcobject);
						$reshook = $hookmanager->executeHooks('createFrom', $parameters, $object, $action); // Note that $action and $object may have been

						if ($reshook < 0)
							$error ++;
					} else {
						$mesg = $srcobject->error;
						$error ++;
					}
				} else {
					$mesg = $object->error;
					$error ++;
				}
			} else {
				// Required extrafield left blank, error message already defined by setOptionalsFromPost()
				$action = 'create';
			}
		} else {
			// Fill array 'array_options' with data from add form
			$ret = $extrafields->setOptionalsFromPost($extralabels, $object);
			if ($ret < 0)
				$error ++;

			if (! $error)
			{
				$object_id = $object->create($user);
				// If some invoice's lines already known
				$NBLINES = 8;
				for($i = 1; $i <= $NBLINES; $i ++) {
					if ($_POST ['idprod' . $i]) {
						$xid = 'idprod' . $i;
						$xqty = 'qty' . $i;
						$xremise = 'remise_percent' . $i;
						$object->add_product($_POST [$xid], $_POST [$xqty], $_POST [$xremise]);
					}
				}
			}
		}
		// Insert default contacts if defined
		if ($object_id > 0) {
			if (GETPOST('contactidp')) {
				$result = $object->add_contact(GETPOST('contactidp'), 'CUSTOMER', 'external');
				if ($result < 0) {
					$mesg = '<div class="error">' . $langs->trans("ErrorFailedToAddContact") . '</div>';
					$error ++;
				}
			}

			$id = $object_id;
			$action = '';
		}
		if ($object_id > 0 && ! $error)
		{
			//registro de command sale
			//agregamos el registro en commande_sale
			$objectsale->fk_commande     = $object_id;
			$objectsale->fk_entrepot_end = $fk_entrepot;
			$objectsale->date_livraison = $datelivraison;
			$objectsale->fk_entrepot     = $_SESSION['fkEntrepotid']+0;
			$objectsale->fk_subsidiary   = $_SESSION['fkSubsidiaryid']+0;
			$objectsale->amount_advance  = 0;
			$objectsale->statut = 1;
			$objectsale->tms = dol_now();
			$res = $objectsale->create($user);
			if ($res <= 0) $error++;
		}
		// End of object creation, we show it
		if ($object_id > 0 && ! $error)
		{
			$db->commit();
			header('Location: ' . $_SERVER["PHP_SELF"] . '?id=' . $object_id);
			exit();
		} else {
			$db->rollback();
			$action = 'create';
			if (! $mesg)
				$mesg = '<div class="error">' . $object->error . '</div>';
		}
	}
}

else if ($action == 'confirm_classifybilled' && $user->rights->ventas->commande->creer)
{
	//cambiamos de estado
	$res = $object->cloture($user);
	if ($res < 0)
		setEventMessage($object->error, 'errors');
	else
	{
		$ret=$object->classifyBilled();
		if ($ret < 0) {
			setEventMessage($object->error, 'errors');
		}
	}
}

// Positionne ref commande client
else if ($action == 'set_ref_client' && $user->rights->ventas->commande->creer) {
	$objectadd->set_ref_clientadd($user, GETPOST('ref_client'));
}

else if ($action == 'setremise' && $user->rights->ventas->commande->creer) {
	$objectadd->set_remiseadd($user, GETPOST('remise'));
}

else if ($action == 'setabsolutediscount' && $user->rights->ventas->commande->creer) {
	if (GETPOST('remise_id')) {
		if ($object->id > 0) {
			$object->insert_discount(GETPOST('remise_id'));
		} else {
			dol_print_error($db, $object->error);
		}
	}
}

else if ($action == 'setdate' && $user->rights->ventas->commande->creer) {
	// print "x ".$_POST['liv_month'].", ".$_POST['liv_day'].", ".$_POST['liv_year'];
	$date = dol_mktime(0, 0, 0, GETPOST('order_month'), GETPOST('order_day'), GETPOST('order_year'));

	$result = $objectadd->set_dateadd($user, $date);
	if ($result < 0) {
		$mesg = '<div class="error">' . $object->error . '</div>';
	}
}

else if ($action == 'setdate_livraison' && $user->rights->ventas->commande->creer) {
  //print "x ".$_POST['liv_month'].", ".$_POST['liv_day'].", ".$_POST['liv_year'];
	$datelivraison = dol_mktime(GETPOST('liv_hour'), GETPOST('liv_min'), 0, GETPOST('liv_month'), GETPOST('liv_day'), GETPOST('liv_year'));
	$result = $objectadd->set_date_livraisonadd($user, $datelivraison);
	if ($result < 0) {
		$mesg = '<div class="error">' . $object->error . '</div>';
	}
}

else if ($action == 'setmode' && $user->rights->ventas->commande->creer) {
	$result = $object->setPaymentMethods(GETPOST('mode_reglement_id', 'int'));
	if ($result < 0)
		dol_print_error($db, $object->error);
}

else if ($action == 'setavailability' && $user->rights->ventas->commande->creer) {
	$result = $object->availability(GETPOST('availability_id'));
	if ($result < 0)
		dol_print_error($db, $object->error);
}

else if ($action == 'setdemandreason' && $user->rights->ventas->commande->creer) {
	$result = $object->demand_reason(GETPOST('demand_reason_id'));
	if ($result < 0)
		dol_print_error($db, $object->error);
}

else if ($action == 'setconditions' && $user->rights->ventas->commande->creer) {
	$result = $object->setPaymentTerms(GETPOST('cond_reglement_id', 'int'));
	if ($result < 0) {
		dol_print_error($db, $object->error);
	} else {
		if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE)) {
			// Define output language
			$outputlangs = $langs;
			$newlang = GETPOST('lang_id', 'alpha');
			if ($conf->global->MAIN_MULTILANGS && empty($newlang))
				$newlang = $object->client->default_lang;
			if (! empty($newlang)) {
				$outputlangs = new Translate("", $conf);
				$outputlangs->setDefaultLang($newlang);
			}

			$ret = $object->fetch($object->id); // Reload to get new records
			commande_pdf_create($db, $object, $object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
		}
	}
}

else if ($action == 'setremisepercent' && $user->rights->ventas->commande->creer) {
	$result = $object->set_remise($user, GETPOST('remise_percent'));
}

else if ($action == 'setremiseabsolue' && $user->rights->ventas->commande->creer) {
	$result = $object->set_remise_absolue($user, GETPOST('remise_absolue'));
}

// Add a new line
else if ($action == 'addline' && $user->rights->ventas->commande->creer)
{
	$langs->load('errors');
	$error = 0;

	// Set if we used free entry or predefined product
	$predef='';
	$product_desc=(GETPOST('dp_desc')?GETPOST('dp_desc'):'');
	if (GETPOST('prod_entry_mode') == 'free')
	{
		$idprod=0;
		$price_ht = GETPOST('price_ht');
		$tva_tx = (GETPOST('tva_tx') ? GETPOST('tva_tx') : 0);
	}
	else
	{
		$idprod=GETPOST('idprod', 'int');
		$price_ht = '';
		$tva_tx = '';
	}
	$qty = GETPOST('qty' . $predef);
	$remise_percent = GETPOST('remise_percent' . $predef);

	// Extrafields
	$extrafieldsline = new ExtraFields($db);
	$extralabelsline = $extrafieldsline->fetch_name_optionals_label($object->table_element_line);
	$array_option = $extrafieldsline->getOptionalsFromPost($extralabelsline, $predef);
	// Unset extrafield
	if (is_array($extralabelsline)) {
		// Get extra fields
		foreach ($extralabelsline as $key => $value) {
			unset($_POST ["options_" . $key]);
		}
	}

	if (empty($idprod) && ($price_ht < 0) && ($qty < 0))
	{
		setEventMessage($langs->trans('ErrorBothFieldCantBeNegative', $langs->transnoentitiesnoconv('UnitPriceHT'), $langs->transnoentitiesnoconv('Qty')), 'errors');
		$error ++;
	}
	if (GETPOST('prod_entry_mode') == 'free' && empty($idprod) && GETPOST('type') < 0)
	{
		setEventMessage($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Type')), 'errors');
		$error ++;
	}
	if (GETPOST('prod_entry_mode') == 'free' && empty($idprod) && (! ($price_ht >= 0) || $price_ht == '')) 	// Unit price can be 0 but not ''
	{
		setEventMessage($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("UnitPriceHT")), 'errors');
		$error ++;
	}
	if ($qty == '') {
		setEventMessage($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Qty')), 'errors');
		$error ++;
	}
	if (GETPOST('prod_entry_mode') == 'free' && empty($idprod) && empty($product_desc)) {
		setEventMessage($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Description')), 'errors');
		$error ++;
	}

	if (! $error && ($qty >= 0) && (! empty($product_desc) || ! empty($idprod)))
	{
		// Clean parameters
		$date_start=dol_mktime(GETPOST('date_start'.$predef.'hour'), GETPOST('date_start'.$predef.'min'), 0, GETPOST('date_start'.$predef.'month'), GETPOST('date_start'.$predef.'day'), GETPOST('date_start'.$predef.'year'));
		$date_end=dol_mktime(GETPOST('date_start'.$predef.'hour'), GETPOST('date_start'.$predef.'min'), 0, GETPOST('date_end'.$predef.'month'), GETPOST('date_end'.$predef.'day'), GETPOST('date_end'.$predef.'year'));
		$price_base_type = (GETPOST('price_base_type', 'alpha')?GETPOST('price_base_type', 'alpha'):'HT');

		// Ecrase $pu par celui du produit
		// Ecrase $desc par celui du produit
		// Ecrase $txtva par celui du produit
		// Ecrase $base_price_type par celui du produit
		if (! empty($idprod))
		{
			$prod = new Product($db);
			$prod->fetch($idprod);
			$label = ((GETPOST('product_label') && GETPOST('product_label') != $prod->label) ? GETPOST('product_label') : '');

			// Update if prices fields are defined
			$tva_tx = get_default_tva($mysoc, $object->client, $prod->id);
			$tva_npr = get_default_npr($mysoc, $object->client, $prod->id);

				// multiprix
			if (! empty($conf->global->PRODUIT_MULTIPRICES) && ! empty($object->client->price_level))
			{
				$pu_ht = $prod->multiprices [$object->client->price_level];
				$pu_ttc = $prod->multiprices_ttc [$object->client->price_level];
				$price_min = $prod->multiprices_min [$object->client->price_level];
				$price_base_type = $prod->multiprices_base_type [$object->client->price_level];
				if (isset($prod->multiprices_tva_tx[$object->client->price_level])) $tva_tx=$prod->multiprices_tva_tx[$object->client->price_level];
				if (isset($prod->multiprices_recuperableonly[$object->client->price_level])) $tva_npr=$prod->multiprices_recuperableonly[$object->client->price_level];
			}
			elseif (! empty($conf->global->PRODUIT_CUSTOMER_PRICES))
			{
				require_once DOL_DOCUMENT_ROOT . '/product/class/productcustomerprice.class.php';

				$prodcustprice = new Productcustomerprice($db);

				$filter = array('t.fk_product' => $prod->id,'t.fk_soc' => $object->thirdparty->id);

				$result = $prodcustprice->fetch_all('', '', 0, 0, $filter);
				if ($result >= 0) {
					if (count($prodcustprice->lines) > 0) {
						$found = true;
						$pu_ht = price($prodcustprice->lines[0]->price);
						$pu_ttc = price($prodcustprice->lines[0]->price_ttc);
						$price_base_type = $prodcustprice->lines[0]->price_base_type;
						$prod->tva_tx = $prodcustprice->lines[0]->tva_tx;
					} else {
						$pu_ht = $prod->price;
						$pu_ttc = $prod->price_ttc;
						$price_min = $prod->price_min;
						$price_base_type = $prod->price_base_type;
					}
				} else {
					setEventMessage($prodcustprice->error,'errors');
				}
			}
			else
			{
				$pu_ht = $prod->price;
				$pu_ttc = $prod->price_ttc;
				$price_min = $prod->price_min;
				$price_base_type = $prod->price_base_type;
			}
			//exit;
				// if price ht is forced (ie: calculated by margin rate and cost price)
			if (! empty($price_ht)) {
				$pu_ht = price2num($price_ht, 'MU');
				$pu_ttc = price2num($pu_ht * (1 + ($tva_tx / 100)), 'MU');
				if ($conf->global->VENTA_PRICE_TAXES_INCLUDED)
					$price_base_type = 'TTC';
			}

				// On reevalue prix selon taux tva car taux tva transaction peut etre different
				// de ceux du produit par defaut (par exemple si pays different entre vendeur et acheteur).
			elseif ($tva_tx != $prod->tva_tx)
			{
				if (empty($tva_tx)) $tva_tx = $prod->tva_tx;
				//if ($price_base_type != 'HT') {
				if ($conf->global->VENTA_PRICE_TAXES_INCLUDED)
					$price_base_type = 'TTC';
				//if ($price_base_type != 'HT') {
				//	$pu_ht = price2num($pu_ttc / (1 + ($tva_tx / 100)), 'MU');
				//} else {
				//	$pu_ttc = price2num($pu_ht * (1 + ($tva_tx / 100)), 'MU');
				//}
			}
			$desc = '';

				// Define output language
			if (! empty($conf->global->MAIN_MULTILANGS) && ! empty($conf->global->PRODUIT_TEXTS_IN_THIRDPARTY_LANGUAGE)) {
				$outputlangs = $langs;
				$newlang = '';
				if (empty($newlang) && GETPOST('lang_id'))
					$newlang = GETPOST('lang_id');
				if (empty($newlang))
					$newlang = $object->client->default_lang;
				if (! empty($newlang)) {
					$outputlangs = new Translate("", $conf);
					$outputlangs->setDefaultLang($newlang);
				}

				$desc = (! empty($prod->multilangs [$outputlangs->defaultlang] ["description"])) ? $prod->multilangs [$outputlangs->defaultlang] ["description"] : $prod->description;
			} else {
				$desc = $prod->description;
			}

			$desc = dol_concatdesc($desc, $product_desc);

				// Add custom code and origin country into description
			if (empty($conf->global->MAIN_PRODUCT_DISABLE_CUSTOMCOUNTRYCODE) && (! empty($prod->customcode) || ! empty($prod->country_code))) {
				$tmptxt = '(';
				if (! empty($prod->customcode))
					$tmptxt .= $langs->transnoentitiesnoconv("CustomCode") . ': ' . $prod->customcode;
				if (! empty($prod->customcode) && ! empty($prod->country_code))
					$tmptxt .= ' - ';
				if (! empty($prod->country_code))
					$tmptxt .= $langs->transnoentitiesnoconv("CountryOrigin") . ': ' . getCountry($prod->country_code, 0, $db, $langs, 0);
				$tmptxt .= ')';
				$desc = dol_concatdesc($desc, $tmptxt);
			}

			$type = $prod->type;
		}
		else
		{
			//registro free
			$pu_ht   = price2num($price_ht, 'MU');
			$pu_ttc  = price2num(GETPOST('price_ttc'), 'MU');
			$tva_npr = (preg_match('/\*/', $tva_tx) ? 1 : 0);
			$tva_tx  = str_replace('*', '', $tva_tx);
			$label   = (GETPOST('product_label') ? GETPOST('product_label') : '');
			$desc    = $product_desc;
			$type    = GETPOST('type');

				//cambiando valores para impresion
			if ($conf->global->VENTA_PRICE_TAXES_INCLUDED)
			{
				$price_base_type = 'TTC';
				$pu_ttc = $pu_ht;
				//$pu_ht = price2num($pu_ttc / (1 + ($tva_tx / 100)), 'MT');
			}
		}

		// Margin
		$fournprice = (GETPOST('fournprice' . $predef) ? GETPOST('fournprice' . $predef) : '');
		$buyingprice = (GETPOST('buying_price' . $predef) ? GETPOST('buying_price' . $predef) : '');

		// Local Taxes
		$localtax1_tx = get_localtax($tva_tx, 1, $object->client);
		$localtax2_tx = get_localtax($tva_tx, 2, $object->client);

		$desc = dol_htmlcleanlastbr($desc);

		$info_bits = 0;
		if ($tva_npr)
			$info_bits |= 0x01;

		if (! empty($price_min) && (price2num($pu_ht) * (1 - price2num($remise_percent) / 100) < price2num($price_min)))
		{
			$mesg = $langs->trans("CantBeLessThanMinPrice", price(price2num($price_min, 'MU'), 0, $langs, 0, 0, - 1, $conf->currency));
			setEventMessage($mesg, 'errors');
		}
		else
		{
			//echo '0price ht  '.$pu_ht.' ttc '.$pu_ttc;
			// Insert line
			$result = $object->addlinev($desc, $pu_ht, $qty, $tva_tx, $localtax1_tx, $localtax2_tx, $idprod, $remise_percent, $info_bits, 0, $price_base_type, $pu_ttc, $date_start, $date_end, $type, - 1, 0, GETPOST('fk_parent_line'), $fournprice, $buyingprice, $label, $array_option);
			//echo '<br>1 price ht  '.$pu_ht.' ttc '.$pu_ttc;
			//volvemos a actualizar para corregir el valor ttc
			//$resultup = $object->updateline($result, $desc, $pu_ht, $qty, $remise_percent, $tva_tx, $localtax1_tx, $localtax2_tx, $price_base_type, $info_bits, $date_start, $date_end, $type, GETPOST('fk_parent_line'), 0, $fournprice, $buyingprice, $label, 0, $array_option);
			if ($result > 0) {
				$ret = $object->fetch($object->id); // Reload to get new records

				if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE)) {
					// Define output language
					$outputlangs = $langs;
					$newlang = GETPOST('lang_id', 'alpha');
					if (! empty($conf->global->MAIN_MULTILANGS) && empty($newlang))
						$newlang = $object->client->default_lang;
					if (! empty($newlang)) {
						$outputlangs = new Translate("", $conf);
						$outputlangs->setDefaultLang($newlang);
					}

					//commande_pdf_create($db, $object, $object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
				}

				unset($_POST ['prod_entry_mode']);

				unset($_POST ['qty']);
				unset($_POST ['type']);
				unset($_POST ['remise_percent']);
				unset($_POST ['price_ht']);
				unset($_POST ['price_ttc']);
				unset($_POST ['tva_tx']);
				unset($_POST ['product_ref']);
				unset($_POST ['product_label']);
				unset($_POST ['product_desc']);
				unset($_POST ['fournprice']);
				unset($_POST ['buying_price']);
				unset($_POST ['np_marginRate']);
				unset($_POST ['np_markRate']);
				unset($_POST ['dp_desc']);
				unset($_POST ['idprod']);

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
			} else {
				setEventMessage($object->error, 'errors');
			}
		}
	}
}

/*
 *  Mise a jour d'une ligne dans la commande
*/
else if ($action == 'updateligne' && $user->rights->ventas->commande->creer && GETPOST('save') == $langs->trans('Save')) {
	// Clean parameters
	$date_start='';
	$date_end='';
	$date_start=dol_mktime(GETPOST('date_starthour'), GETPOST('date_startmin'), 0, GETPOST('date_startmonth'), GETPOST('date_startday'), GETPOST('date_startyear'));
	$date_end=dol_mktime(GETPOST('date_starthour'), GETPOST('date_startmin'), 0, GETPOST('date_endmonth'), GETPOST('date_endday'), GETPOST('date_endyear'));
	$description=dol_htmlcleanlastbr(GETPOST('product_desc'));
	$pu_ht=GETPOST('price_ht');
	$vat_rate=(GETPOST('tva_tx')?GETPOST('tva_tx'):0);

	//cambiando valores segun variable
	if ($conf->global->VENTA_PRICE_TAXES_INCLUDED)
	{
		$price_base_type = 'ttc';
		$pu_ttc = $pu_ht;
		//$pu_ht = price2num($pu_ttc / $line->qty,'MU');
		$pu_ht = price2num($pu_ttc / (1 + ($vat_rate / 100)), 'MT');
	}
	// Define info_bits
	$info_bits = 0;
	if (preg_match('/\*/', $vat_rate))
		$info_bits |= 0x01;

		// Define vat_rate
	$vat_rate = str_replace('*', '', $vat_rate);
	$localtax1_rate = get_localtax($vat_rate, 1, $object->client);
	$localtax2_rate = get_localtax($vat_rate, 2, $object->client);

	// Add buying price
	$fournprice = (GETPOST('fournprice') ? GETPOST('fournprice') : '');
	$buyingprice = (GETPOST('buying_price') ? GETPOST('buying_price') : '');

	// Extrafields Lines
	$extrafieldsline = new ExtraFields($db);
	$extralabelsline = $extrafieldsline->fetch_name_optionals_label($object->table_element_line);
	$array_option = $extrafieldsline->getOptionalsFromPost($extralabelsline);
	// Unset extrafield POST Data
	if (is_array($extralabelsline)) {
		foreach ($extralabelsline as $key => $value) {
			unset($_POST ["options_" . $key]);
		}
	}

	// Check minimum price
	$productid = GETPOST('productid', 'int');
	if (! empty($productid)) {
		$product = new Product($db);
		$product->fetch($productid);

		$type = $product->type;

		$price_min = $product->price_min;
		if (! empty($conf->global->PRODUIT_MULTIPRICES) && ! empty($object->client->price_level))
			$price_min = $product->multiprices_min [$object->client->price_level];

		$label = ((GETPOST('update_label') && GETPOST('product_label')) ? GETPOST('product_label') : '');

		if ($price_min && (price2num($pu_ht) * (1 - price2num(GETPOST('remise_percent')) / 100) < price2num($price_min)))
		{
			setEventMessage($langs->trans("CantBeLessThanMinPrice", price(price2num($price_min, 'MU'), 0, $langs, 0, 0, - 1, $conf->currency)), 'errors');
			$error ++;
		}
	}
	else
	{
		$type = GETPOST('type');
		$label = (GETPOST('product_label') ? GETPOST('product_label') : '');

		// Check parameters
		if (GETPOST('type') < 0) {
			setEventMessage($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Type")), 'errors');
			$error ++;
		}
	}

	if (! $error)
	{
		$result = $object->updateline(GETPOST('lineid'), $description, $pu_ht, GETPOST('qty'), GETPOST('remise_percent'), $vat_rate, $localtax1_rate, $localtax2_rate, 'HT', $info_bits, $date_start, $date_end, $type, GETPOST('fk_parent_line'), 0, $fournprice, $buyingprice, $label, 0, $array_option);

		if ($result >= 0)
		{
			if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
			{
				// Define output language
				$outputlangs = $langs;
				$newlang = '';
				if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id'))
					$newlang = GETPOST('lang_id');
				if ($conf->global->MAIN_MULTILANGS && empty($newlang))
					$newlang = $object->client->default_lang;
				if (! empty($newlang)) {
					$outputlangs = new Translate("", $conf);
					$outputlangs->setDefaultLang($newlang);
				}

				$ret = $object->fetch($object->id); // Reload to get new records
				//commande_pdf_create($db, $object, $object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
			}

			unset($_POST ['qty']);
			unset($_POST ['type']);
			unset($_POST ['productid']);
			unset($_POST ['remise_percent']);
			unset($_POST ['price_ht']);
			unset($_POST ['price_ttc']);
			unset($_POST ['tva_tx']);
			unset($_POST ['product_ref']);
			unset($_POST ['product_label']);
			unset($_POST ['product_desc']);
			unset($_POST ['fournprice']);
			unset($_POST ['buying_price']);
		} else {
			setEventMessage($object->error, 'errors');
		}
	}
}

else if ($action == 'updateligne' && $user->rights->ventas->commande->creer && GETPOST('cancel') == $langs->trans('Cancel'))
{
	header('Location: ' . $_SERVER['PHP_SELF'] . '?id=' . $object->id); // Pour reaffichage de la fiche en cours d'edition
	exit();
}

else if ($action == 'confirm_validate' && $confirm == 'yes' && $user->rights->ventas->valcommande)
{
	$idwarehouse = GETPOST('idwarehouse');
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
	if (! empty($conf->stock->enabled) && ! empty($conf->global->STOCK_CALCULATE_ON_VALIDATE_ORDER) && $qualified_for_stock_change)
	{
		if (! $idwarehouse || $idwarehouse == -1)
		{
			$error++;
			$mesgs[]='<div class="error">'.$langs->trans('ErrorFieldRequired',$langs->transnoentitiesnoconv("Warehouse")).'</div>';
			$action='';
		}
	}

	if (! $error)
	{
		$result = $objectadd->validadd($user, $idwarehouse);

		if ($result >= 0) {
			// Define output language
			$outputlangs = $langs;
			$newlang = '';
			if ($conf->global->MAIN_MULTILANGS && empty($newlang) && ! empty($_REQUEST['lang_id']))
				$newlang = $_REQUEST['lang_id'];
			if ($conf->global->MAIN_MULTILANGS && empty($newlang))
				$newlang = $object->client->default_lang;
			if (! empty($newlang)) {
				$outputlangs = new Translate("", $conf);
				$outputlangs->setDefaultLang($newlang);
			}
			if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
				commande_pdf_create($db, $objectadd, $objectadd->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
		}
	}
	header('Location: ' . $_SERVER['PHP_SELF'] . '?id=' . $object->id);
  // Pour reaffichage de la fiche en cours d'edition
	exit();

}

// Go back to draft status
else if ($action == 'confirm_modif' && $user->rights->ventas->commande->edit)
{
	$idwarehouse = GETPOST('idwarehouse');

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
	if (! empty($conf->stock->enabled) && ! empty($conf->global->STOCK_CALCULATE_ON_VALIDATE_ORDER) && $qualified_for_stock_change)
	{
		if (! $idwarehouse || $idwarehouse == -1)
		{
			$error++;
			$mesgs[]='<div class="error">'.$langs->trans('ErrorFieldRequired',$langs->transnoentitiesnoconv("Warehouse")).'</div>';
			$action='';
		}
	}

	if (! $error)
	{
		$result = $object->set_draft($user, $idwarehouse);
		if ($result >= 0)
		{
			// Define output language
			$outputlangs = $langs;
			$newlang = '';
			if ($conf->global->MAIN_MULTILANGS && empty($newlang) && ! empty($_REQUEST['lang_id']))
				$newlang = $_REQUEST['lang_id'];
			if ($conf->global->MAIN_MULTILANGS && empty($newlang))
				$newlang = $object->client->default_lang;
			if (! empty($newlang))
			{
				$outputlangs = new Translate("", $conf);
				$outputlangs->setDefaultLang($newlang);
			}
			if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
			{
				$ret = $object->fetch($object->id); // Reload to get new records
				commande_pdf_create($db, $object, $object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
			}
		}
	}
}

else if ($action == 'confirm_shipped' && $confirm == 'yes' && $user->rights->commande->cloturer)
{
	$result = $object->cloture($user);
	if ($result < 0)
	{
		setEventMessage($object->error, 'errors');
	}
}

else if ($action == 'confirm_cancel' && $confirm == 'yes' && $user->rights->commande->valider)
{
	$idwarehouse = GETPOST('idwarehouse');

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
	if (! empty($conf->stock->enabled) && ! empty($conf->global->STOCK_CALCULATE_ON_VALIDATE_ORDER) && $qualified_for_stock_change)
	{
		if (! $idwarehouse || $idwarehouse == -1)
		{
			$error++;
			$mesgs[]='<div class="error">'.$langs->trans('ErrorFieldRequired',$langs->transnoentitiesnoconv("Warehouse")).'</div>';
			$action='';
		}
	}

	if (! $error) {
		$result = $object->cancel($idwarehouse);

		if ($result < 0) {
			setEventMessage($object->error, 'errors');
		}
	}
}

/*
 * Ordonnancement des lignes
*/

else if ($action == 'up' && $user->rights->ventas->commande->creer)
{
	$object->line_up(GETPOST('rowid'));

	// Define output language
	$outputlangs = $langs;
	$newlang = '';
	if ($conf->global->MAIN_MULTILANGS && empty($newlang) && ! empty($_REQUEST['lang_id']))
		$newlang = $_REQUEST['lang_id'];
	if ($conf->global->MAIN_MULTILANGS && empty($newlang))
		$newlang = $object->client->default_lang;
	if (! empty($newlang))
	{
		$outputlangs = new Translate("", $conf);
		$outputlangs->setDefaultLang($newlang);
	}

	if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
		commande_pdf_create($db, $object, $object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);

	header('Location: ' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '#' . GETPOST('rowid'));
	exit();
}

else if ($action == 'down' && $user->rights->ventas->commande->creer)
{
	$object->line_down(GETPOST('rowid'));

	// Define output language
	$outputlangs = $langs;
	$newlang = '';
	if ($conf->global->MAIN_MULTILANGS && empty($newlang) && ! empty($_REQUEST['lang_id']))
		$newlang = $_REQUEST['lang_id'];
	if ($conf->global->MAIN_MULTILANGS && empty($newlang))
		$newlang = $object->client->default_lang;
	if (! empty($newlang)) {
		$outputlangs = new Translate("", $conf);
		$outputlangs->setDefaultLang($newlang);
	}
	if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
		commande_pdf_create($db, $object, $object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);

	header('Location: ' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '#' . GETPOST('rowid'));
	exit();
}

else if ($action == 'builddoc') // In get or post
{
	/*
	 * Generate order document
	 * define into /core/modules/commande/modules_commande.php
	 */

	// Save last template used to generate document
	if (GETPOST('model'))
		$object->setDocModel($user, GETPOST('model', 'alpha'));

		// Define output language
	$outputlangs = $langs;
	$newlang = '';
	if ($conf->global->MAIN_MULTILANGS && empty($newlang) && ! empty($_REQUEST['lang_id']))
		$newlang = $_REQUEST['lang_id'];
	if ($conf->global->MAIN_MULTILANGS && empty($newlang))
		$newlang = $object->client->default_lang;
	if (! empty($newlang)) {
		$outputlangs = new Translate("", $conf);
		$outputlangs->setDefaultLang($newlang);
	}
	$result = commande_pdf_create($db, $object, $object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);

	if ($result <= 0) {
		dol_print_error($db, $result);
		exit();
	}
}

// Remove file in doc form
else if ($action == 'remove_file') {
	if ($object->id > 0) {
		require_once DOL_DOCUMENT_ROOT . '/core/lib/files.lib.php';

		$langs->load("other");
		$upload_dir = $conf->commande->dir_output;
		$file = $upload_dir . '/' . GETPOST('file');
		$ret = dol_delete_file($file, 0, 0, 0, $object);
		if ($ret)
			setEventMessage($langs->trans("FileWasRemoved", GETPOST('urlfile')));
		else
			setEventMessage($langs->trans("ErrorFailToDeleteFile", GETPOST('urlfile')), 'errors');
		$action = '';
	}
}

// Print file
else if ($action == 'print_file' and $user->rights->printipp->read) {
	require_once DOL_DOCUMENT_ROOT . '/core/class/dolprintipp.class.php';
	$printer = new dolPrintIPP($db, $conf->global->PRINTIPP_HOST, $conf->global->PRINTIPP_PORT, $user->login, $conf->global->PRINTIPP_USER, $conf->global->PRINTIPP_PASSWORD);
	$printer->print_file(GETPOST('file', 'alpha'), GETPOST('printer', 'alpha'));
	setEventMessage($langs->trans("FileWasSentToPrinter", GETPOST('file')));
	$action = '';
}

else if ($action == 'update_extras') {
	// Fill array 'array_options' with data from update form
	$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);
	$ret = $extrafields->setOptionalsFromPost($extralabels, $object, GETPOST('attribute'));
	if ($ret < 0)
		$error ++;

	if (! $error) {
		// Actions on extra fields (by external module or standard code)
		// FIXME le hook fait double emploi avec le trigger !!
		$hookmanager->initHooks(array('orderdao'));
		$parameters = array('id' => $object->id);
		$reshook = $hookmanager->executeHooks('insertExtraFields', $parameters, $object, $action); // Note that $action and $object may have been modified by
		 // some hooks
		if (empty($reshook)) {
			$result = $object->insertExtraFields();
			if ($result < 0) {
				$error ++;
			}
		} else if ($reshook < 0)
		$error ++;
	}

	if ($error)
		$action = 'edit_extras';
}

/*
 * Add file in email form
*/
if (GETPOST('addfile'))
{
	require_once DOL_DOCUMENT_ROOT . '/core/lib/files.lib.php';

	// Set tmp user directory TODO Use a dedicated directory for temp mails files
	$vardir = $conf->user->dir_output . "/" . $user->id;
	$upload_dir_tmp = $vardir . '/temp';

	dol_add_file_process($upload_dir_tmp, 0, 0);
	$action = 'presend';
}

/*
 * Remove file in email form
*/
if (GETPOST('removedfile'))
{
	require_once DOL_DOCUMENT_ROOT . '/core/lib/files.lib.php';

	// Set tmp user directory
	$vardir = $conf->user->dir_output . "/" . $user->id;
	$upload_dir_tmp = $vardir . '/temp';

	// TODO Delete only files that was uploaded from email form
	dol_remove_file_process(GETPOST('removedfile'), 0);
	$action = 'presend';
}

/*
 * Send mail
*/
if ($action == 'send' && ! GETPOST('addfile') && ! GETPOST('removedfile') && ! GETPOST('cancel'))
{
	$langs->load('mails');

	if ($object->id > 0) {
		// $ref = dol_sanitizeFileName($object->ref);
		// $file = $conf->commande->dir_output . '/' . $ref . '/' . $ref . '.pdf';

		// if (is_readable($file))
		// {
		if (GETPOST('sendto')) {
			// Le destinataire a ete fourni via le champ libre
			$sendto = GETPOST('sendto');
			$sendtoid = 0;
		} elseif (GETPOST('receiver') != '-1') {
			// Recipient was provided from combo list
			if (GETPOST('receiver') == 'thirdparty') 			// Id of third party
			{
				$sendto = $object->client->email;
				$sendtoid = 0;
			} else 			// Id du contact
			{
				$sendto = $object->client->contact_get_property(GETPOST('receiver'), 'email');
				$sendtoid = GETPOST('receiver');
			}
		}

		if (dol_strlen($sendto)) {
			$langs->load("commercial");

			$from = GETPOST('fromname') . ' <' . GETPOST('frommail') . '>';
			$replyto = GETPOST('replytoname') . ' <' . GETPOST('replytomail') . '>';
			$message = GETPOST('message');
			$sendtocc = GETPOST('sendtocc');
			$deliveryreceipt = GETPOST('deliveryreceipt');

			if ($action == 'send') {
				if (dol_strlen(GETPOST('subject')))
					$subject = GETPOST('subject');
				else
					$subject = $langs->transnoentities('Order') . ' ' . $object->ref;
				$actiontypecode = 'AC_COM';
				$actionmsg = $langs->transnoentities('MailSentBy') . ' ' . $from . ' ' . $langs->transnoentities('To') . ' ' . $sendto . ".\n";
				if ($message) {
					$actionmsg .= $langs->transnoentities('MailTopic') . ": " . $subject . "\n";
					$actionmsg .= $langs->transnoentities('TextUsedInTheMessageBody') . ":\n";
					$actionmsg .= $message;
				}
				$actionmsg2 = $langs->transnoentities('Action' . $actiontypecode);
			}

			// Create form object
			include_once DOL_DOCUMENT_ROOT . '/core/class/html.formmail.class.php';
			$formmail = new FormMail($db);

			$attachedfiles = $formmail->get_attached_files();
			$filepath = $attachedfiles ['paths'];
			$filename = $attachedfiles ['names'];
			$mimetype = $attachedfiles ['mimes'];

			// Send mail
			require_once DOL_DOCUMENT_ROOT . '/core/class/CMailFile.class.php';
			$mailfile = new CMailFile($subject, $sendto, $from, $message, $filepath, $mimetype, $filename, $sendtocc, '', $deliveryreceipt, - 1);
			if ($mailfile->error) {
				$mesg = '<div class="error">' . $mailfile->error . '</div>';
			}
			else
			{
				$result = $mailfile->sendfile();
				if ($result)
				{
					$mesg = $langs->trans('MailSuccessfulySent', $mailfile->getValidAddress($from, 2), $mailfile->getValidAddress($sendto, 2));

					$error = 0;

					// Initialisation donnees
					$object->sendtoid = $sendtoid;
					$object->actiontypecode = $actiontypecode;
					$object->actionmsg = $actionmsg;
					$object->actionmsg2 = $actionmsg2;
					$object->fk_element = $object->id;
					$object->elementtype = $object->element;

					// Appel des triggers
					include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
					$interface = new Interfaces($db);
					$result = $interface->run_triggers('ORDER_SENTBYMAIL', $object, $user, $langs, $conf);
					if ($result < 0) {
						$error ++;
						$this->errors = $interface->errors;
					}
					// Fin appel triggers

					if ($error) {
						dol_print_error($db);
					} else {
						// Redirect here
						// This avoid sending mail twice if going out and then back to page
						header('Location: ' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '&mesg=' . urlencode($mesg));
						exit();
					}
				} else {
					$langs->load("other");
					$mesg = '<div class="error">';
					if ($mailfile->error) {
						$mesg .= $langs->trans('ErrorFailedToSendMail', $from, $sendto);
						$mesg .= '<br>' . $mailfile->error;
					} else {
						$mesg .= 'No mail sent. Feature is disabled by option MAIN_DISABLE_ALL_MAILS';
					}
					$mesg .= '</div>';
				}
			}
		}
		else
		{
			$langs->load("errors");
			$mesg = '<div class="error">' . $langs->trans('ErrorCantReadFile', $file) . '</div>';
			dol_syslog('Failed to read file: ' . $file);
		}
	}
	else
	{
		$langs->load("other");
		$mesg = '<div class="error">' . $langs->trans('ErrorFailedToReadEntity', $langs->trans("Order")) . '</div>';
		dol_syslog($langs->trans('ErrorFailedToReadEntity', $langs->trans("Order")));
	}
}

if (! $error && ! empty($conf->global->MAIN_DISABLE_CONTACTS_TAB) && $user->rights->ventas->commande->creer) {
	if ($action == 'addcontact') {
		if ($object->id > 0) {
			$contactid = (GETPOST('userid') ? GETPOST('userid') : GETPOST('contactid'));
			$result = $object->add_contact($contactid, GETPOST('type'), GETPOST('source'));
		}

		if ($result >= 0) {
			header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $object->id);
			exit();
		} else {
			if ($object->error == 'DB_ERROR_RECORD_ALREADY_EXISTS') {
				$langs->load("errors");
				$mesg = '<div class="error">' . $langs->trans("ErrorThisContactIsAlreadyDefinedAsThisType") . '</div>';
			} else {
				$mesg = '<div class="error">' . $object->error . '</div>';
			}
		}
	}

	// bascule du statut d'un contact
	else if ($action == 'swapstatut') {
		if ($object->id > 0) {
			$result = $object->swapContactStatus(GETPOST('ligne'));
		} else {
			dol_print_error($db);
		}
	}

	// Efface un contact
	else if ($action == 'deletecontact') {
		$result = $object->delete_contact($lineid);

		if ($result >= 0) {
			header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $object->id);
			exit();
		} else {
			dol_print_error($db);
		}
	}
}

/*
 *	View
 */


//llxHeader('', $langs->trans('Order'), 'EN:Customers_Orders|FR:Commandes_Clients|ES:Pedidos de clientes');
//modificado rqc
$arrayofjs=array('/ventas/javascript/fontsize.js');
$arrayofcss=array('/ventas/css/style.css');
top_htmlhead($head,$langs->trans("Point of sale"),0,0,$arrayofjs,$arrayofcss);
//fin modificado rqc

$form = new Form($db);
$formv = new Formv($db);
$formadd = new Formadd($db);
$formfile = new FormFileAdd($db);
$formorder = new FormOrder($db);

//modificado rqc
print '<div class="rounded0">'."\n";
include_once DOL_DOCUMENT_ROOT.'/ventas/tpl/menu2.tpl.php';
print '</div>'."\n";

print '<div class="contentBox1">';
//fin modificado rqc


/**
 * *******************************************************************
 *
 * Mode creation
 *
 * *******************************************************************
 */
if ($action == 'list' && $user->rights->ventas->commande->creer)
{
	//recuperamos los pedidos pendientes de la sucursal
	//$aCommande = list_commande_sale($_SESSION['fkSubsidiaryid']);
	//recuperamos los pedidos pendientes por almacen destino
	$aCommande = list_commande_entrepot($_SESSION['fkEntrepotid']);
	//armamos la tabla
	print_barre_liste($langs->trans("Listcommande"), $page, "fiche.php", "", $sortfield, $sortorder,'',$num);

	print '<table class="noborder" width="100%">';
	print '<thead>';
	print "<tr class=\"liste_titre\">";


	print_liste_field_titre($langs->trans("Codigo"),"", "","","","");
	print_liste_field_titre($langs->trans("Cliente"),"", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Advance"),"", "","","",'align="right"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Date"),"", "","","",'align="center"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Datelivraison"),"", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Observacion (Nota publica)"),"", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Total"),"", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Factura Final"),"", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Balance"),"", "",'','','align="right"',$sortfield,$sortorder);

	print "</tr>\n";
	print '</thead>';
	$num = count($aCommande);
	$i = 0;
	if ($num)
	{
		$var=True;
		foreach ($aCommande AS $i => $objp)
		{
		//while ($i < $num)
		//{
			//$objp = $aCommande[$i];
			if ($objp->billed != 1)
			{
				$var=!$var;
				$objsoc->fetch($objp->fk_soc);
				print "<tr $bc[$var]>";
				$object->fetch($objp->rowid);
				$object->fetchObjectLinked();
				$nAdvance = 0;
				$nFacture = 0;
				foreach((array) $object->linkedObjects AS $nametype => $aData)
				{
					foreach ((array) $aData AS $k => $obj_)
					{
						if ($nametype == 'facture')
						{
							if ($obj_->type == 3)
								$nAdvance+= $obj_->total_ttc;
							else
								$nFacture+= $obj_->total_ttc;
						}
					}
				}
				$cEnd = '';
				$balance = price2num($objp->total_ttc - $nAdvance - $nFacture,'MT');
				if ($balance <= 0)
					$cEnd = '&end=1';
				print '<td><a href="fiche.php?id='.$objp->rowid.'&action=sel'.$cEnd.'">'.img_object($langs->trans("ShowWarehouse"),'stock').' '.$objp->ref.'</a></td>';
				if ($objsoc->id == $objp->fk_soc)
					print '<td>'.$objsoc->name.'</td>';
				else
					print '<td>&nbsp;</td>';

				print '<td align="right">'.price($nAdvance).'</td>';
				print '<td align="center">'.dol_print_date($objp->date_commande,'day').'</td>';
				print '<td>'.dol_print_date($objp->date_livraison,'day').'</td>';
				print '<td>'.$objp->note_public.'</td>';
				print '<td>'.price(price2num($objp->total_ttc,'MT')).'</td>';
				print '<td>'.price(price2num($nFacture,'MT')).'</td>';
				print '<td align="right">'.price(price2num($objp->total_ttc - $nAdvance - $nFacture,'MT')).'</td>';
				print '<td>'.$i.'</td>';
				print "</tr>\n";
			}
			//$i++;
		}
	}
	print '</table>';
	/* ************************************************************************** */
	/*                                                                            */
	/* Barre d'action                                                             */
	/*                                                                            */
	/* ************************************************************************** */

	print "<div class=\"tabsAction\">\n";
	if ($user->rights->ventas->commande->creer)
		print '<a class="butAction" href="fiche.php?action=create">'.$langs->trans("Create").'</a>';
	else
		print '<a class="butActionRefused" href="#">'.$langs->trans("Modify").'</a>';
	print "</div>";
}
elseif ($action == 'create' && $user->rights->ventas->commande->creer)
{
	print_fiche_titre($langs->trans('CreateOrder'));

	dol_htmloutput_mesg($mesg, $mesgs, 'error');

	$soc = new Societe($db);
	if ($socid > 0)
		$res = $soc->fetch($socid);

	if (! empty($origin) && ! empty($originid))
	{
		// Parse element/subelement (ex: project_task)
		$element = $subelement = $origin;
		if (preg_match('/^([^_]+)_([^_]+)/i', $origin, $regs)) {
			$element = $regs [1];
			$subelement = $regs [2];
		}

		if ($element == 'project') {
			$projectid = $originid;
		} else {
			// For compatibility
			if ($element == 'order' || $element == 'commande') {
				$element = $subelement = 'commande';
			}
			if ($element == 'propal') {
				$element = 'comm/propal';
				$subelement = 'propal';
			}
			if ($element == 'contract') {
				$element = $subelement = 'contrat';
			}

			dol_include_once('/' . $element . '/class/' . $subelement . '.class.php');

			$classname = ucfirst($subelement);
			$objectsrc = new $classname($db);
			$objectsrc->fetch($originid);
			if (empty($objectsrc->lines) && method_exists($objectsrc, 'fetch_lines'))
				$objectsrc->fetch_lines();
			$objectsrc->fetch_thirdparty();

			// Replicate extrafields
			$objectsrc->fetch_optionals($originid);
			$object->array_options = $objectsrc->array_options;

			$projectid = (! empty($objectsrc->fk_project) ? $objectsrc->fk_project : '');
			$ref_client = (! empty($objectsrc->ref_client) ? $objectsrc->ref_client : '');

			$soc = $objectsrc->client;
			$cond_reglement_id	= (!empty($objectsrc->cond_reglement_id)?$objectsrc->cond_reglement_id:(!empty($soc->cond_reglement_id)?$soc->cond_reglement_id:1));
			$mode_reglement_id	= (!empty($objectsrc->mode_reglement_id)?$objectsrc->mode_reglement_id:(!empty($soc->mode_reglement_id)?$soc->mode_reglement_id:0));
			$availability_id	= (!empty($objectsrc->availability_id)?$objectsrc->availability_id:(!empty($soc->availability_id)?$soc->availability_id:0));
			$demand_reason_id	= (!empty($objectsrc->demand_reason_id)?$objectsrc->demand_reason_id:(!empty($soc->demand_reason_id)?$soc->demand_reason_id:0));
			$remise_percent		= (!empty($objectsrc->remise_percent)?$objectsrc->remise_percent:(!empty($soc->remise_percent)?$soc->remise_percent:0));
			$remise_absolue		= (!empty($objectsrc->remise_absolue)?$objectsrc->remise_absolue:(!empty($soc->remise_absolue)?$soc->remise_absolue:0));
			$dateinvoice		= empty($conf->global->MAIN_AUTOFILL_DATE)?-1:'';

			$datedelivery = (! empty($objectsrc->date_livraison) ? $objectsrc->date_livraison : '');

			$note_private = (! empty($objectsrc->note_private) ? $objectsrc->note_private : (! empty($objectsrc->note_private) ? $objectsrc->note_private : ''));
			$note_public = (! empty($objectsrc->note_public) ? $objectsrc->note_public : '');

			// Object source contacts list
			$srccontactslist = $objectsrc->liste_contact(- 1, 'external', 1);
		}
	}
	else
	{
		$cond_reglement_id  = $soc->cond_reglement_id;
		$mode_reglement_id  = $soc->mode_reglement_id;
		$availability_id    = $soc->availability_id;
		$demand_reason_id   = $soc->demand_reason_id;
		$remise_percent     = $soc->remise_percent;
		$remise_absolue     = 0;
		$dateinvoice        = empty($conf->global->MAIN_AUTOFILL_DATE)?-1:'';
		$projectid          = 0;
	}
	$absolute_discount=$soc->getAvailableDiscounts();

	$nbrow = 10;

	print '<form name="crea_commande" action="' . $_SERVER["PHP_SELF"] . '" method="POST">';
	print '<input type="hidden" name="token" value="' . $_SESSION ['newtoken'] . '">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="socid" value="' . $soc->id . '">' . "\n";
	print '<input type="hidden" name="remise_percent" value="' . $soc->remise_percent . '">';
	print '<input type="hidden" name="origin" value="' . $origin . '">';
	print '<input type="hidden" name="originid" value="' . $originid . '">';

	print '<table class="border" width="100%">';

	// Reference
	print '<tr><td class="fieldrequired">' . $langs->trans('Ref') . '</td><td colspan="2">' . $langs->trans("Draft") . '</td></tr>';

	// Reference client
	print '<tr><td class="fieldrequired">' . $langs->trans('RefCustomer').' ('.$langs->trans('Celular') . ')</td><td colspan="2">';
	if (!empty($conf->global->MAIN_USE_PROPAL_REFCLIENT_FOR_ORDER))
		print '<input type="text" name="ref_client" value="'.$ref_client.'" required></td>';
	else
		print '<input type="text" name="ref_client" value="'.(!empty($ref_client)?$ref_client:$soc->phone).'" required></td>';
	print '</tr>';

	// Client
	print '<tr>';
	print '<td class="fieldrequired">' . $langs->trans('Customer').' ('.$langs->trans('NIT') . ')</td>';
	if ($socid > 0) {
		print '<td colspan="2">';
		print $soc->getNomUrl(1);
		print '<input type="hidden" name="socid" value="' . $soc->id . '">';
		print '</td>';
	} else {
		print '<td colspan="2">';
		print $formadd->select_client('', 'socid', 's.client = 1 OR s.client = 3', 1);
		print '&nbsp;';
		print '<a href="'.$_SESSION['DOL_URL_ROOT'].'/ventas/pedidos/soc.php?leftmenu=customers&action=create&type=c&backtopage='.$_SERVER['PHP_SELF'].'?action=create">Nuevo</a>';

		print '</td>';
	}
	print '</tr>' . "\n";

	/*
	 * Contact de la commande
	*/
	if ($socid > 0)
	{
		print "<tr><td>" . $langs->trans("DefaultContact") . '</td><td colspan="2">';
		$form->select_contacts($soc->id, $setcontact, 'contactidp', 1, $srccontactslist);
		print '</td></tr>';

		// Ligne info remises tiers
		print '<tr><td>' . $langs->trans('Discounts') . '</td><td colspan="2">';
		if ($soc->remise_percent)
			print $langs->trans("CompanyHasRelativeDiscount", $soc->remise_percent);
		else
			print $langs->trans("CompanyHasNoRelativeDiscount");
		print '. ';
		$absolute_discount = $soc->getAvailableDiscounts();
		if ($absolute_discount)
			print $langs->trans("CompanyHasAbsoluteDiscount", price($absolute_discount), $langs->trans("Currency" . $conf->currency));
		else
			print $langs->trans("CompanyHasNoAbsoluteDiscount");
		print '.';
		print '</td></tr>';
	}
	// Date
	print '<tr><td class="fieldrequired">' . $langs->trans('Date') . '</td><td colspan="2">';
	if ($user->admin)
		$form->select_date($datecommande, 're', '', '', '', "crea_commande", 1, 1);
	else
		print dol_print_date(dol_now(),'dayhour');
	print '</td></tr>';

	// Date de livraison
	print '<tr><td class="fieldrequired">'.$langs->trans("DeliveryDate").'</td><td colspan="2">';
	if (empty($datedelivery))
	{
		if (! empty($conf->global->DATE_LIVRAISON_WEEK_DELAY)) $datedelivery = time() + ((7*$conf->global->DATE_LIVRAISON_WEEK_DELAY) * 24 * 60 * 60);
		else $datedelivery=empty($conf->global->MAIN_AUTOFILL_DATE)?-1:dol_now();
	}
	$form->select_date($datelivraison, 'liv_', 1, 1, '', "crea_commande", 1, 1);
	print "</td></tr>";

	//entrepot end
	print '<tr><td class="fieldrequired">'.$langs->trans("Deliverysubsidiary").'</td><td colspan="2">';
	print select_entrepot($fk_entrepot,'fk_entrepot','');
	print '</td></tr>';
	// Conditions de reglement
	print '<tr><td class="nowrap">' . $langs->trans('PaymentConditionsShort') . '</td><td colspan="2">';
	$form->select_conditions_paiements($cond_reglement_id, 'cond_reglement_id', - 1, 1);
	print '</td></tr>';

	// Mode de reglement
	print '<tr><td>' . $langs->trans('PaymentMode') . '</td><td colspan="2">';
	$form->select_types_paiements($mode_reglement_id, 'mode_reglement_id');
	print '</td></tr>';

	// // Delivery delay
	// print '<tr><td>' . $langs->trans('AvailabilityPeriod') . '</td><td colspan="2">';
	// $form->selectAvailabilityDelay($availability_id, 'availability_id', '', 1);
	// print '</td></tr>';

	// What trigger creation
	print '<tr><td>' . $langs->trans('Source') . '</td><td colspan="2">';
	$form->selectInputReason($demand_reason_id, 'demand_reason_id', '', 1);
	print '</td></tr>';

	// Project
	if (! empty($conf->projet->enabled) && $socid > 0) {
		$formproject = new FormProjets($db);

		print '<tr><td>' . $langs->trans('Project') . '</td><td colspan="2">';
		$numprojet = $formproject->select_projects($soc->id, $projectid);
		if ($numprojet == 0) {
			print ' &nbsp; <a href="' . DOL_URL_ROOT . '/projet/fiche.php?socid=' . $soc->id . '&action=create">' . $langs->trans("AddProject") . '</a>';
		}
		print '</td></tr>';
	}

	// Other attributes
	$parameters = array('objectsrc' => $objectsrc,'colspan' => ' colspan="3"');
	$reshook = $hookmanager->executeHooks('formObjectOptions', $parameters, $object, $action); // Note that $action and $object may have been modified by /
	if (empty($reshook) && ! empty($extrafields->attribute_label)) {
		print $object->showOptionals($extrafields, 'edit');
	}

	// Template to use by default
	print '<tr><td>' . $langs->trans('Model') . '</td>';
	print '<td colspan="2">';
	include_once DOL_DOCUMENT_ROOT . '/core/modules/commande/modules_commande.php';
	$liste = ModelePDFCommandes::liste_modeles($db);
	print $form->selectarray('model', $liste, $conf->global->COMMANDE_ADDON_PDF);
	print "</td></tr>";

	// Note publique
	print '<tr>';
	print '<td class="border" valign="top">' . $langs->trans('NotePublic') . '</td>';
	print '<td valign="top" colspan="2">';

	$doleditor = new DolEditor('note_public', $note_public, '', 80, 'dolibarr_notes', 'In', 0, false, true, ROWS_3, 70);
	print $doleditor->Create(1);
	// print '<textarea name="note_public" wrap="soft" cols="70" rows="'.ROWS_3.'">'.$note_public.'</textarea>';
	print '</td></tr>';

	// Note privee
	if (empty($user->societe_id))
	{
		print '<tr>';
		print '<td class="border" valign="top">' . $langs->trans('NotePrivate') . '</td>';
		print '<td valign="top" colspan="2">';

		$doleditor = new DolEditor('note_private', $note_private, '', 80, 'dolibarr_notes', 'In', 0, false, true, ROWS_3, 70);
		print $doleditor->Create(1);
		// print '<textarea name="note" wrap="soft" cols="70" rows="'.ROWS_3.'">'.$note_private.'</textarea>';
		print '</td></tr>';
	}

	if (! empty($origin) && ! empty($originid) && is_object($objectsrc))
	{
		// TODO for compatibility
		if ($origin == 'contrat') {
			// Calcul contrat->price (HT), contrat->total (TTC), contrat->tva
			$objectsrc->remise_absolue = $remise_absolue;
			$objectsrc->remise_percent = $remise_percent;
			$objectsrc->update_price(1);
		}

		print "\n<!-- " . $classname . " info -->";
		print "\n";
		print '<input type="hidden" name="amount"         value="' . $objectsrc->total_ht . '">' . "\n";
		print '<input type="hidden" name="total"          value="' . $objectsrc->total_ttc . '">' . "\n";
		print '<input type="hidden" name="tva"            value="' . $objectsrc->total_tva . '">' . "\n";
		print '<input type="hidden" name="origin"         value="' . $objectsrc->element . '">';
		print '<input type="hidden" name="originid"       value="' . $objectsrc->id . '">';

		$newclassname = $classname;
		if ($newclassname == 'Propal')
			$newclassname = 'CommercialProposal';
		print '<tr><td>' . $langs->trans($newclassname) . '</td><td colspan="2">' . $objectsrc->getNomUrl(1) . '</td></tr>';
		print '<tr><td>' . $langs->trans('TotalHT') . '</td><td colspan="2">' . price($objectsrc->total_ht) . '</td></tr>';
		print '<tr><td>' . $langs->trans('TotalVAT') . '</td><td colspan="2">' . price($objectsrc->total_tva) . "</td></tr>";
		if ($mysoc->localtax1_assuj == "1") 		// Localtax1 RE
		{
			print '<tr><td>' . $langs->transcountry("AmountLT1", $mysoc->country_code) . '</td><td colspan="2">' . price($objectsrc->total_localtax1) . "</td></tr>";
		}

		if ($mysoc->localtax2_assuj == "1") 		// Localtax2 IRPF
		{
			print '<tr><td>' . $langs->transcountry("AmountLT2", $mysoc->country_code) . '</td><td colspan="2">' . price($objectsrc->total_localtax2) . "</td></tr>";
		}

		print '<tr><td>' . $langs->trans('TotalTTC') . '</td><td colspan="2">' . price($objectsrc->total_ttc) . "</td></tr>";
	}
	else
	{
		if (! empty($conf->global->PRODUCT_SHOW_WHEN_CREATE)) {
			/*
			 * Services/produits predefinis
			*/
			$NBLINES = 8;

			print '<tr><td colspan="3">';

			print '<table class="noborder">';
			print '<tr><td>' . $langs->trans('ProductsAndServices') . '</td>';
			print '<td>' . $langs->trans('Qty') . '</td>';
			print '<td>' . $langs->trans('ReductionShort') . '</td>';
			print '</tr>';
			for($i = 1; $i <= $NBLINES; $i ++) {
				print '<tr><td>';
				// multiprix
				if (! empty($conf->global->PRODUIT_MULTIPRICES))
					print $form->select_produits('', 'idprod' . $i, '', $conf->product->limit_size, $soc->price_level);
				else
					print $form->select_produits('', 'idprod' . $i, '', $conf->product->limit_size);
				print '</td>';
				print '<td><input type="text" size="3" name="qty' . $i . '" value="1"></td>';
				print '<td><input type="text" size="3" name="remise_percent' . $i . '" value="' . $soc->remise_percent . '">%</td></tr>';
			}

			print '</table>';
			print '</td></tr>';
		}
	}

	print '</table>';

	// Button "Create Draft"
	print '<br><center><input type="submit" class="button" name="bouton" value="' . $langs->trans('CreateDraft') . '"></center>';

	print '</form>';

	// Show origin lines
	if (! empty($origin) && ! empty($originid) && is_object($objectsrc)) {
		$title = $langs->trans('ProductsAndServices');
		print_titre($title);

		print '<table class="noborder" width="100%">';

		$objectsrc->printOriginLinesList();

		print '</table>';
	}
}
else
{
	/* *************************************************************************** */
	/*                                                                             */
	/* Mode vue et edition                                                         */
	/*                                                                             */
	/* *************************************************************************** */
	$now = dol_now();

	if ($object->id > 0)
	{
		dol_htmloutput_mesg($mesg, $mesgs);

		$objectadd->fetch($object->id);
		$product_static = new Product($db);
		$objectsale->fetch_commande($object->id);
		$soc = new Societe($db);
		$soc->fetch($object->socid);

		$author = new User($db);
		$author->fetch($object->user_author_id);
		//$head = commande_prepare_head($object);
		//dol_fiche_head($head, 'order', $langs->trans("CustomerOrder"), 0, 'order');
		print '<div class="fiche">';
		$formconfirm = '';
		/*
		 * Confirmation de la suppression de la commande
		*/
		if ($action == 'delete') {
			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('DeleteOrder'), $langs->trans('ConfirmDeleteOrder'), 'confirm_delete', '', 0, 1);
		}

		/*
		 * Confirmation suppression advance
		 */
		if ($action == 'deleteadvance' && $user->rights->ventas->adv->del)
		{
			$rowid = GETPOST('rowid');
			$objectAl->fetch(GETPOST('rowid'));
			$objectAl->delete($user);
		}

		/*
		 * Confirmation de la validation
		*/
		if ($action == 'validate')
		{
			// on verifie si l'objet est en numerotation provisoire
			$ref = substr($object->ref, 1, 4);
			if ($ref == 'PROV') {
				$numref = $object->getNextNumRef($soc);
			} else {
				$numref = $object->ref;
			}

			$text = $langs->trans('ConfirmValidateOrder', $numref);
			if (! empty($conf->notification->enabled))
			{
				require_once DOL_DOCUMENT_ROOT . '/core/class/notify.class.php';
				$notify = new Notify($db);
				$text .= '<br>';
				$text .= $notify->confirmMessage('ORDER_VALIDATE', $object->socid);
			}

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
			if (! empty($conf->stock->enabled) && ! empty($conf->global->STOCK_CALCULATE_ON_VALIDATE_ORDER) && $qualified_for_stock_change)
			{
				$langs->load("stocks");
				require_once DOL_DOCUMENT_ROOT . '/product/class/html.formproduct.class.php';
				$formproduct = new FormProduct($db);
				$formquestion = array(
									// 'text' => $langs->trans("ConfirmClone"),
									// array('type' => 'checkbox', 'name' => 'clone_content', 'label' => $langs->trans("CloneMainAttributes"), 'value'
									// => 1),
									// array('type' => 'checkbox', 'name' => 'update_prices', 'label' => $langs->trans("PuttingPricesUpToDate"),
									// 'value' => 1),
					array('type' => 'other','name' => 'idwarehouse','label' => $langs->trans("SelectWarehouseForStockDecrease"),'value' => $formproduct->selectWarehouses(GETPOST('idwarehouse')?GETPOST('idwarehouse'):'ifone', 'idwarehouse', '', 1)));
			}

			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('ValidateOrder'), $text, 'confirm_validate', $formquestion, 0, 1, 220);
		}
		// Confirm back to draft status
		if ($action == 'modif')
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

			$text=$langs->trans('ConfirmUnvalidateOrder',$object->ref);
			$formquestion=array();
			if (! empty($conf->stock->enabled) && ! empty($conf->global->STOCK_CALCULATE_ON_VALIDATE_ORDER) && $qualified_for_stock_change)
			{
				$langs->load("stocks");
				require_once DOL_DOCUMENT_ROOT . '/product/class/html.formproduct.class.php';
				$formproduct = new FormProduct($db);
				$formquestion = array(
									// 'text' => $langs->trans("ConfirmClone"),
									// array('type' => 'checkbox', 'name' => 'clone_content', 'label' => $langs->trans("CloneMainAttributes"), 'value'
									// => 1),
									// array('type' => 'checkbox', 'name' => 'update_prices', 'label' => $langs->trans("PuttingPricesUpToDate"),
									// 'value' => 1),
					array('type' => 'other','name' => 'idwarehouse','label' => $langs->trans("SelectWarehouseForStockIncrease"),'value' => $formproduct->selectWarehouses(GETPOST('idwarehouse')?GETPOST('idwarehouse'):'ifone', 'idwarehouse', '', 1)));
			}

			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('UnvalidateOrder'), $text, 'confirm_modif', $formquestion, "yes", 1, 220);
		}

		/*
		 * Confirmation de la cloture
		*/
		if ($action == 'shipped') {
			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('CloseOrder'), $langs->trans('ConfirmCloseOrder'), 'confirm_shipped', '', 0, 1);
		}

		/*
		 * Confirmation de l'annulation
		 */
		if ($action == 'cancel')
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

			$text=$langs->trans('ConfirmCancelOrder',$object->ref);
			$formquestion=array();
			if (! empty($conf->stock->enabled) && ! empty($conf->global->STOCK_CALCULATE_ON_VALIDATE_ORDER) && $qualified_for_stock_change)
			{
				$langs->load("stocks");
				require_once DOL_DOCUMENT_ROOT . '/product/class/html.formproduct.class.php';
				$formproduct = new FormProduct($db);
				$formquestion = array(
									// 'text' => $langs->trans("ConfirmClone"),
									// array('type' => 'checkbox', 'name' => 'clone_content', 'label' => $langs->trans("CloneMainAttributes"), 'value'
									// => 1),
									// array('type' => 'checkbox', 'name' => 'update_prices', 'label' => $langs->trans("PuttingPricesUpToDate"),
									// 'value' => 1),
					array('type' => 'other','name' => 'idwarehouse','label' => $langs->trans("SelectWarehouseForStockIncrease"),'value' => $formproduct->selectWarehouses(GETPOST('idwarehouse')?GETPOST('idwarehouse'):'ifone', 'idwarehouse', '', 1)));
			}

			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('Cancel'), $text, 'confirm_cancel', $formquestion, 0, 1);
		}

		/*
		 * Confirmation de la suppression d'une ligne produit
		 */
		if ($action == 'ask_deleteline')
		{
			$formconfirm=$form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id.'&lineid='.$lineid, $langs->trans('DeleteProductLine'), $langs->trans('ConfirmDeleteProductLine'), 'confirm_deleteline', '', 0, 1);
		}
		if ($action == 'classifybilled')
		{
			$formconfirm=$form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id.'&end=1', $langs->trans('FinishOrder'), $langs->trans('ConfirmFinishOrder'), 'confirm_classifybilled', '', 0, 1);
		}

		// Clone confirmation
		if ($action == 'clone')
		{
			// Create an array for form
			$formquestion = array(
								// 'text' => $langs->trans("ConfirmClone"),
								// array('type' => 'checkbox', 'name' => 'clone_content', 'label' => $langs->trans("CloneMainAttributes"), 'value' =>
								// 1),
								// array('type' => 'checkbox', 'name' => 'update_prices', 'label' => $langs->trans("PuttingPricesUpToDate"), 'value'
								// => 1),
				array('type' => 'other','name' => 'socid','label' => $langs->trans("SelectThirdParty"),'value' => $form->select_company(GETPOST('socid', 'int'), 'socid', '(s.client=1 OR s.client=3)')));
			// Paiement incomplet. On demande si motif = escompte ou autre
			$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('CloneOrder'), $langs->trans('ConfirmCloneOrder', $object->ref), 'confirm_clone', $formquestion, 'yes', 1);
		}

		if (! $formconfirm) {
			$parameters = array('lineid' => $lineid);
			$formconfirm = $hookmanager->executeHooks('formConfirm', $parameters, $object, $action); // Note that $action and $object may have been modified
			// by hook
		}

		// Print form confirm
		print $formconfirm;
		/*
		 *   Commande
		*/
		$nbrow = 9;
		if (! empty($conf->projet->enabled))
			$nbrow ++;

			// Local taxes
		if ($mysoc->localtax1_assuj == "1")
			$nbrow ++;
		if ($mysoc->localtax2_assuj == "1")
			$nbrow ++;

		print '<table class="border" width="100%">';

		$linkback = '<a href="' . DOL_URL_ROOT . '/commande/liste.php' . (! empty($socid) ? '?socid=' . $socid : '') . '">' . $langs->trans("BackToList") . '</a>';

		// Ref
		// print '<tr><td width="18%">' . $langs->trans('Ref') . '</td>';
		// print '<td colspan="3">';
		// print $form->showrefnav($object, 'ref', $linkback, 1, 'ref', 'ref');
		// print '</td>';
		// print '</tr>';

		// Ref commande client
		print '<tr><td>';
		print '<table class="nobordernopadding" width="100%"><tr><td class="nowrap">';
		print $langs->trans('RefCustomer') . '</td><td align="left">';
		print '</td>';
		if ($action != 'refcustomer' && $object->brouillon)
			print '<td align="right"><a href="' . $_SERVER['PHP_SELF'] . '?action=refcustomer&amp;id=' . $object->id . '">' . img_edit($langs->trans('Modify')) . '</a></td>';
		print '</tr></table>';
		print '</td><td colspan="3">';
		if ($user->rights->ventas->commande->creer && $action == 'refcustomer')
		{
			print '<form action="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '" method="post">';
			print '<input type="hidden" name="token" value="' . $_SESSION ['newtoken'] . '">';
			print '<input type="hidden" name="action" value="set_ref_client">';
			print '<input type="text" class="flat" size="20" name="ref_client" value="' . $object->ref_client . '">';
			print ' <input type="submit" class="button" value="' . $langs->trans('Modify') . '">';
			print '</form>';
		} else {
			print $object->ref_client;
		}
		print '</td>';
		print '</tr>';

		// Third party
		print '<tr><td>' . $langs->trans('Company') . '</td>';
		print '<td colspan="3">' . $soc->getNomUrl(1) . '</td>';
		print '</tr>';

		if (! empty($conf->global->FACTURE_DEPOSITS_ARE_JUST_PAYMENTS))
		{
			$filterabsolutediscount = "fk_facture_source IS NULL"; // If we want deposit to be substracted to payments only and not to total of final
																 // invoice
			$filtercreditnote = "fk_facture_source IS NOT NULL"; // If we want deposit to be substracted to payments only and not to total of final invoice
		}
		else
		{
			$filterabsolutediscount = "fk_facture_source IS NULL OR (fk_facture_source IS NOT NULL AND description='(DEPOSIT)')";
			$filtercreditnote = "fk_facture_source IS NOT NULL AND description <> '(DEPOSIT)'";
		}

		// Relative and absolute discounts
		$addrelativediscount = '<a href="' . DOL_URL_ROOT . '/comm/remise.php?id=' . $soc->id . '&backtopage=' . urlencode($_SERVER["PHP_SELF"]) . '?facid=' . $object->id . '">' . $langs->trans("EditRelativeDiscounts") . '</a>';
		$addabsolutediscount = '<a href="' . DOL_URL_ROOT . '/comm/remx.php?id=' . $soc->id . '&backtopage=' . urlencode($_SERVER["PHP_SELF"]) . '?facid=' . $object->id . '">' . $langs->trans("EditGlobalDiscounts") . '</a>';
		$addcreditnote = '<a href="' . DOL_URL_ROOT . '/ventas/facture.php?action=create&socid=' . $soc->id . '&type=2&backtopage=' . urlencode($_SERVER["PHP_SELF"]) . '?facid=' . $object->id . '">' . $langs->trans("AddCreditNote") . '</a>';

		print '<tr><td>' . $langs->trans('Discounts') . '</td><td colspan="3">';
		if ($soc->remise_percent)
			print $langs->trans("CompanyHasRelativeDiscount", $soc->remise_percent);
		else
			print $langs->trans("CompanyHasNoRelativeDiscount");
		print '. ';
		$absolute_discount = $soc->getAvailableDiscounts('', 'fk_facture_source IS NULL');
		$absolute_creditnote = $soc->getAvailableDiscounts('', 'fk_facture_source IS NOT NULL');
		$absolute_discount = price2num($absolute_discount, 'MT');
		$absolute_creditnote = price2num($absolute_creditnote, 'MT');
		if ($absolute_discount) {
			if ($object->statut > 0) {
				print $langs->trans("CompanyHasAbsoluteDiscount", price($absolute_discount), $langs->transnoentities("Currency" . $conf->currency));
			} else {
				// Remise dispo de type remise fixe (not credit note)
				print '<br>';
				$form->form_remise_dispo($_SERVER["PHP_SELF"] . '?id=' . $object->id, 0, 'remise_id', $soc->id, $absolute_discount, $filterabsolutediscount);
			}
		}
		if ($absolute_creditnote) {
			print $langs->trans("CompanyHasCreditNote", price($absolute_creditnote), $langs->transnoentities("Currency" . $conf->currency)) . '. ';
		}
		if (! $absolute_discount && ! $absolute_creditnote)
			print $langs->trans("CompanyHasNoAbsoluteDiscount") . '.';
		print '</td></tr>';

		// Date
		print '<tr><td>';
		print '<table class="nobordernopadding" width="100%"><tr><td>';
		print $langs->trans('Date');
		print '</td>';

		if ($action != 'editdate' && $object->brouillon)
			print '<td align="right"><a href="' . $_SERVER["PHP_SELF"] . '?action=editdate&amp;id=' . $object->id . '">' . img_edit($langs->trans('SetDate'), 1) . '</a></td>';
		print '</tr></table>';
		print '</td><td colspan="3">';
		if ($action == 'editdate') {
			print '<form name="setdate" action="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '" method="post">';
			print '<input type="hidden" name="token" value="' . $_SESSION ['newtoken'] . '">';
			print '<input type="hidden" name="action" value="setdate">';
			$form->select_date($object->date, 'order_', '', '', '', "setdate");
			print '<input type="submit" class="button" value="' . $langs->trans('Modify') . '">';
			print '</form>';
		} else {
			print $object->date ? dol_print_date($object->date, 'day') : '&nbsp;';
		}
		print '</td>';
		print '</tr>';

		// Delivery date planed
		print '<tr><td height="10">';
		print '<table class="nobordernopadding" width="100%"><tr><td>';
		print $langs->trans('DateDeliveryPlanned');
		print '</td>';
		if ($action != 'editdate_livraison')
			print '<td align="right"><a href="' . $_SERVER["PHP_SELF"] . '?action=editdate_livraison&amp;id=' . $object->id . '">' . img_edit($langs->trans('SetDeliveryDate'), 1) . '</a></td>';
		print '</tr></table>';
		print '</td><td colspan="3">';
		if ($action == 'editdate_livraison') {
			print '<form name="setdate_livraison" action="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '" method="post">';
			print '<input type="hidden" name="token" value="' . $_SESSION ['newtoken'] . '">';
			print '<input type="hidden" name="action" value="setdate_livraison">';
			$form->select_date($object->date_livraison ? $object->date_livraison : - 1, 'liv_', 1, 1, '', "setdate_livraison");
			print '<input type="submit" class="button" value="' . $langs->trans('Modify') . '">';
			print '</form>';
		} else {
			print $object->date_livraison ? dol_print_date($object->date_livraison, 'dayhour') : '&nbsp;';
		}
		print '</td>';
		print '</tr>';

		//delivery entrepot
		//print '<tr><td height="10">';
		$objentrepot->fetch($objectsale->fk_entrepot_end);
		print '<tr><td height="10">';
		print '<table class="nobordernopadding" width="100%"><tr><td>';
		print $langs->trans('Deliverysubsidiary');
		print '</td>';
		if ($action != 'editdeliveryentrepot' && $objectsale->fk_entrepot_end)
			print '<td align="right"><a href="' . $_SERVER["PHP_SELF"] . '?action=editdeliveryentrepot&amp;id=' . $object->id . '">' . img_edit($langs->trans('SetConditions'), 1) . '</a></td>';
		print '</tr></table>';
		print '</td><td colspan="3">';
		if ($action == 'editdeliveryentrepot') {
			$formv->form_entrepot_sel($_SERVER['PHP_SELF'] . '?id=' . $object->id.'fk_entrepot='.$objectsale->fk_entrepot_end, $objectsale->fk_entrepot_end, 'fk_entrepot', 1);
		}
		else
		{
			if ($objentrepot->id == $objectsale->fk_entrepot_end)
				print $objentrepot->lieu;
			else
				print '';
		}
		print '</td>';

		print '</tr>';

		// Mode of payment
		print '<tr><td height="10">';
		print '<table class="nobordernopadding" width="100%"><tr><td>';
		print $langs->trans('PaymentMode');
		print '</td>';
		if ($action != 'editmode' && $object->brouillon)
			print '<td align="right"><a href="' . $_SERVER["PHP_SELF"] . '?action=editmode&amp;id=' . $object->id . '">' . img_edit($langs->trans('SetMode'), 1) . '</a></td>';
		print '</tr></table>';
		print '</td><td colspan="3">';
		if ($action == 'editmode') {
			$form->form_modes_reglement($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->mode_reglement_id, 'mode_reglement_id');
		} else {
			$form->form_modes_reglement($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->mode_reglement_id, 'none');
		}
		print '</td></tr>';

		// Availability
		print '<tr><td height="10">';
		print '<table class="nobordernopadding" width="100%"><tr><td>';
		print $langs->trans('AvailabilityPeriod');
		print '</td>';
		if ($action != 'editavailability' && $object->brouillon)
			print '<td align="right"><a href="' . $_SERVER["PHP_SELF"] . '?action=editavailability&amp;id=' . $object->id . '">' . img_edit($langs->trans('SetAvailability'), 1) . '</a></td>';
		print '</tr></table>';
		print '</td><td colspan="3">';
		if ($action == 'editavailability') {
			$form->form_availability($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->availability_id, 'availability_id', 1);
		} else {
			$form->form_availability($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->availability_id, 'none', 1);
		}
		print '</td></tr>';

		// Source
		print '<tr><td height="10">';
		print '<table class="nobordernopadding" width="100%"><tr><td>';
		print $langs->trans('Source');
		print '</td>';
		if ($action != 'editdemandreason' && ! empty($object->brouillon))
			print '<td align="right"><a href="' . $_SERVER["PHP_SELF"] . '?action=editdemandreason&amp;id=' . $object->id . '">' . img_edit($langs->trans('SetDemandReason'), 1) . '</a></td>';
		print '</tr></table>';
		print '</td><td colspan="3">';
		if ($action == 'editdemandreason') {
			$form->formInputReason($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->demand_reason_id, 'demand_reason_id', 1);
		} else {
			$form->formInputReason($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->demand_reason_id, 'none');
		}
		// Removed because using dictionary is an admin feature, not a user feature. Ther is already the "star" to show info to admin users.
		// This is to avoid too heavy screens and have an uniform look and feel for all screens.
		// print '</td><td>';
		// print '<a href="'.DOL_URL_ROOT.'/admin/dict.php?id=22&origin=order&originid='.$object->id.'">'.$langs->trans("DictionarySource").'</a>';
		print '</td></tr>';

		// Project
		if (! empty($conf->projet->enabled) && $abc)
		{
			$langs->load('projects');
			print '<tr><td height="10">';
			print '<table class="nobordernopadding" width="100%"><tr><td>';
			print $langs->trans('Project');
			print '</td>';
			if ($action != 'classify')
				print '<td align="right"><a href="' . $_SERVER['PHP_SELF'] . '?action=classify&amp;id=' . $object->id . '">' . img_edit($langs->trans('SetProject')) . '</a></td>';
			print '</tr></table>';
			print '</td><td colspan="3">';
			// print "$object->id, $object->socid, $object->fk_project";
			if ($action == 'classify') {
				$form->form_project($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->socid, $object->fk_project, 'projectid');
			} else {
				$form->form_project($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->socid, $object->fk_project, 'none');
			}
			print '</td></tr>';
		}

		if ($soc->outstanding_limit) {
			// Outstanding Bill
			print '<tr><td>';
			print $langs->trans('OutstandingBill');
			print '</td><td align=right colspan=3>';
			print price($soc->get_OutstandingBill()) . ' / ';
			print price($soc->outstanding_limit, 0, '', 1, - 1, - 1, $conf->currency);
			print '</td>';
			print '</tr>';
		}

		// Other attributes (TODO Move this into an include)
		$res = $object->fetch_optionals($object->id, $extralabels);
		$parameters = array('colspan' => ' colspan="3"');
		$reshook = $hookmanager->executeHooks('formObjectOptions', $parameters, $object, $action); // Note that $action and $object may have been modified by

		if (empty($reshook) && ! empty($extrafields->attribute_label))
		{
			foreach ($extrafields->attribute_label as $key => $label)
			{
				if ($action == 'edit_extras')
				{
					$value = (isset($_POST ["options_" . $key]) ? $_POST ["options_" . $key] : $object->array_options ["options_" . $key]);
				} else {
					$value = $object->array_options ["options_" . $key];
				}
				if ($extrafields->attribute_type [$key] == 'separate') {
					print $extrafields->showSeparator($key);
				}
				else
				{
					print '<tr><td';
					if (! empty($extrafields->attribute_required [$key]))
						print ' class="fieldrequired"';
					print '>' . $label . '</td><td colspan="5">';
					// Convert date into timestamp format
					if (in_array($extrafields->attribute_type [$key], array('date','datetime'))) {
						$value = isset($_POST ["options_" . $key]) ? dol_mktime($_POST ["options_" . $key . "hour"], $_POST ["options_" . $key . "min"], 0, $_POST ["options_" . $key . "month"], $_POST ["options_" . $key . "day"], $_POST ["options_" . $key . "year"]) : $db->jdate($object->array_options ['options_' . $key]);
					}

					if ($action == 'edit_extras' && $user->rights->ventas->commande->creer && GETPOST('attribute') == $key) {
						print '<form enctype="multipart/form-data" action="' . $_SERVER["PHP_SELF"] . '" method="post" name="formsoc">';
						print '<input type="hidden" name="action" value="update_extras">';
						print '<input type="hidden" name="attribute" value="' . $key . '">';
						print '<input type="hidden" name="token" value="' . $_SESSION ['newtoken'] . '">';
						print '<input type="hidden" name="id" value="' . $object->id . '">';

						print $extrafields->showInputField($key, $value);

						print '<input type="submit" class="button" value="' . $langs->trans('Modify') . '">';
						print '</form>';
					} else {
						print $extrafields->showOutputField($key, $value);
						if ($object->statut == 0 && $user->rights->ventas->commande->creer)
							print '<a href="' . $_SERVER['PHP_SELF'] . '?id=' . $object->id . '&action=edit_extras&attribute=' . $key . '">' . img_picto('', 'edit') . ' ' . $langs->trans('Modify') . '</a>';
					}
					print '</td></tr>' . "\n";
				}
			}
		}

		$rowspan = 4;
		if ($mysoc->localtax1_assuj == "1")
			$rowspan ++;
		if ($mysoc->localtax2_assuj == "1")
			$rowspan ++;

			// Total HT
		print '<tr><td>' . $langs->trans('AmountHT') . '</td>';
		print '<td align="right">' . price($object->total_ht, 1, '', 1, - 1, - 1, $conf->currency) . '</td>';

		// Margin Infos
		if (! empty($conf->margin->enabled)) {
			print '<td valign="top" width="50%" colspan="2" rowspan="' . $rowspan . '">';
			//$object->displayMarginInfos();
			print '</td>';
		} else
		print '<td width="50%" colspan="2" rowspan="' . $rowspan . '"></td>';

		print '</tr>';

		// Total TVA
		print '<tr><td>' . $langs->trans('AmountVAT') . '</td><td align="right">' . price($object->total_tva, 1, '', 1, - 1, - 1, $conf->currency) . '</td></tr>';

		// Amount Local Taxes
		if ($mysoc->localtax1_assuj == "1") 		// Localtax1 RE
		{
			print '<tr><td>' . $langs->transcountry("AmountLT1", $mysoc->country_code) . '</td>';
			print '<td align="right">' . price($object->total_localtax1, 1, '', 1, - 1, - 1, $conf->currency) . '</td></tr>';
		}
		if ($mysoc->localtax2_assuj == "1") 		// Localtax2 IRPF
		{
			print '<tr><td>' . $langs->transcountry("AmountLT2", $mysoc->country_code) . '</td>';
			print '<td align="right">' . price($object->total_localtax2, 1, '', 1, - 1, - 1, $conf->currency) . '</td></tr>';
		}

		// Total TTC
		print '<tr><td>' . $langs->trans('AmountTTC') . '</td><td align="right">' . price($object->total_ttc, 1, '', 1, - 1, - 1, $conf->currency) . '</td></tr>';

		// Statut
		print '<tr><td>' . $langs->trans('Status') . '</td><td>' . $object->getLibStatut(4) . '</td></tr>';

		print '</table><br>';
		print "\n";

		if (! empty($conf->global->MAIN_DISABLE_CONTACTS_TAB)) {
			$blocname = 'contacts';
			$title = $langs->trans('ContactsAddresses');
			include DOL_DOCUMENT_ROOT . '/core/tpl/bloc_showhide.tpl.php';
		}

		if (! empty($conf->global->MAIN_DISABLE_NOTES_TAB)) {
			$blocname = 'notes';
			$title = $langs->trans('Notes');
			include DOL_DOCUMENT_ROOT . '/core/tpl/bloc_showhide.tpl.php';
		}

		/*
		 * Lines
		 */
		$result = $object->getLinesArray();

		print '	<form name="addproduct" id="addproduct" action="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . (($action != 'editline') ? '#add' : '#line_' . GETPOST('lineid')) . '" method="POST">
		<input type="hidden" name="token" value="' . $_SESSION ['newtoken'] . '">
		<input type="hidden" name="action" value="' . (($action != 'editline') ? 'addline' : 'updateligne') . '">
		<input type="hidden" name="mode" value="">
		<input type="hidden" name="id" value="' . $object->id . '">
		';

		if (! empty($conf->use_javascript_ajax) && $object->statut == 0) {
			include DOL_DOCUMENT_ROOT . '/core/tpl/ajaxrow.tpl.php';
		}

		print '<table id="tablelines" class="noborder noshadow" width="100%">';
		// Show object lines
		if (! empty($object->lines))
			//$ret = $object->printObjectLines($action, $mysoc, $soc, $lineid, 1);
			$ret = printObjectLinesAdd($action, $mysoc, $soc, $lineid, 1);

		$numlines = count($object->lines);

		/*
		 * Form to add new line
		 */
		if ($object->statut == 0 && $user->rights->ventas->commande->creer)
		{
			if ($action != 'editline')
			{
				$var = true;

				// Add free products/services
				$objectadd->formAddObjectLineadd(1, $mysoc, $soc);

				$parameters = array();
				$reshook = $hookmanager->executeHooks('formAddObjectLine', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
			}
		}
		print '</table>';

		print "</form>\n";

		dol_fiche_end();

		/*
		 * Boutons actions
		*/
		if ($action != 'presend' && $action != 'editline')
		{

			//verificacion de disponibilidad de facturas
			//para modseller de tipo 1
			$modseller = 1;
			$sql = "SELECT t.rowid, t.series, t.num_ini, t.num_fin, t.num_ult, t.date_val, ";
			$sql.= " num_autoriz, t.chave ";
			$sql.= " FROM ".MAIN_DB_PREFIX."v_dosing AS t ";
			$sql.= " WHERE ";
			$sql.= " t.entity = ".$conf->entity;
			$sql.= " AND t.fk_subsidiaryid = ".$_SESSION['fkSubsidiaryid'];
	 //manual
			$sql.= " AND t.lote = 1 ";
			$sql.= " AND active = 1 ";
			$res1=$db->query($sql);
			if ($res1)
			{
				if ($db->num_rows($res1))
				{
					$objd = $db->fetch_object($res1);
					$objd->date_val = $db->jdate($objd->date_val);
				}
			}
//revisamos la dosificacion y numeracion de factura
			$_SESSION['numaut'] = $objd->num_autoriz;
			$_SESSION['dateEmission'] = dol_print_date($objd->date_val,'day');
			$lPrintFacture = true;
//boton para finalizar venta
			if ($objd->num_ult)
			{
				$newnumfac = $objd->num_ult + 1;
				if ($modseller == 1)
					$newnumfac = $_SESSION['numfactsel'];
	//verificamos segun el tipo de modseller
				if ($modseller == 1)
				{
					if ($newnumfac > 0 && ($newnumfac < $objd->num_ini || $newnumfac > $objd->num_fin))
					{
						$lPrintFacture = false;
			//$mesgmanual='<div class="error">'.$langs->trans('Alerta, el numero de factura esta fuera del permitido').'</div>';
						$mesgmanual.=$langs->trans('Alerta, el numero de factura esta fuera del permitido');
						$_SESSION['rf'] = false;
						$action = '';
					}
		//verificamos si existe registrado
					$resvfiscal = $objvfiscal->fetch_num_fac($_SESSION['numaut'],$newnumfac);
					if ($resvfiscal == 1)
					{
						if ($objvfiscal->nfiscal == $newnumfac)
						{
							$lPrintFacture = false;
				//$mesgmanual='<div class="error">'.$langs->trans('Alerta, el numero de factura existe').'</div>';
							if (!empty($mesgmanual)) $mesgmanual.='<br>';
							$mesgmanual.=$langs->trans('Alerta, el numero de factura existe');
							$_SESSION['rf'] = false;
							$action = '';
						}
					}
				}
		//vemos la fecha actual
				$aActual = dol_getdate(dol_now());
				$dActual = 	dol_mktime(0, 0, 0, $aActual['mon'],  $aActual['mday'],  $aActual['year']);
				if ($objd->date_val > $dActual)
				{
					if ($modseller == 1)
					{
						$arrayDosing['dosing_man'] = true;
						unset($_SESSION['arrayErrDosing']['dosing_man']);
					}
					$_SESSION['arrayDosing'] = $arrayDosing;
				}
				elseif ($dActual == $objd->date_val)
				{
					$mesg='<div class="errorfooter">'.$langs->trans('Alerta, hoy es el ultimo dia para facturar, comunicarse con el administrador').'</div>';
					$arrayErrDosing['dosing_aut'] = $mesg;
					$arrayErrDosing['dosing_man'] = $mesg;
					$_SESSION['arrayErrDosing'] = $arrayErrDosing;
				}
				else
				{
					if ($_SESSION['lpp'] != 2)
					{
						$mesg='<div class="errorfooter">'.$langs->trans('Alerta, la fecha limite de emision esta vencido, comunicarse con el administrador').'</div>';
						if ($modseller == 2)
						{
							$arrayDosing['dosing_aut'] = false;
							$arrayErrDosing['dosing_aut'] = $mesg;
						}
						if ($modseller == 1)
						{
							$arrayDosing['dosing_man'] = false;
							$arrayErrDosing['dosing_man'] = $mesg;
						}
						$_SESSION['arrayErrDosing'] = $arrayErrDosing;
						$_SESSION['arrayDosing'] = $arrayDosing;
					}
					else
						unset($_SESSION['arrayErrDosing']);
				}

			}
			else
			{
				$newnumfac = $objd->num_ini;
			}

	//para modseler 2
			//para modseller de tipo 1
			$modseller = 2;
			$sql = "SELECT t.rowid, t.series, t.num_ini, t.num_fin, t.num_ult, t.date_val, ";
			$sql.= " num_autoriz, t.chave ";
			$sql.= " FROM ".MAIN_DB_PREFIX."v_dosing AS t ";
			$sql.= " WHERE ";
			$sql.= " t.entity = ".$conf->entity;
			$sql.= " AND t.fk_subsidiaryid = ".$_SESSION['fkSubsidiaryid'];
	 //automatico
			$sql.= " AND t.lote = 2 ";
			$sql.= " AND t.series = '".$series."' ";
			$sql.= " AND active = 1 ";
			$res1=$db->query($sql);
			if ($res1)
			{
				if ($db->num_rows($res1))
				{
					$objd = $db->fetch_object($res1);
					$objd->date_val = $db->jdate($objd->date_val);
				}
			}
//revisamos la dosificacion y numeracion de factura
			$_SESSION['numaut'] = $objd->num_autoriz;
			$_SESSION['dateEmission'] = dol_print_date($objd->date_val,'day');
			$lPrintFacture = true;
//boton para finalizar venta
			if ($objd->num_ult)
			{
				$newnumfac = $objd->num_ult + 1;
		//echo 'result |'.$newnumfac.'|  |'.($objd->num_fin-5 < 0?$objd->numfin:$objd->num_fin-5);
		//automatico
				if ($modseller == 2)
				{
					if ($newnumfac >= ($objd->num_fin-10 < 0?$objd->numfin:$objd->num_fin-10) && $newnumfac < $objd->num_fin || $newnumfac == $objd->num_fin)
					{
			//print $mesg='<div class="error">'.$langs->trans('aviso, le quedan pocas facturas, comunicarse con el administrador').'</div>';
					}
				}
		//vemos la fecha actual
				$aActual = dol_getdate(dol_now());
				$dActual = 	dol_mktime(0, 0, 0, $aActual['mon'],  $aActual['mday'],  $aActual['year']);
				if ($objd->date_val > $dActual)
				{
			//no se hace nada
					if ($modseller == 2)
					{
						$arrayDosing['dosing_aut'] = true;
						unset($_SESSION['arrayErrDosing']['dosing_aut']);
					}
					$_SESSION['arrayDosing'] = $arrayDosing;
				}
				elseif ($dActual == $objd->date_val)
				{
					$mesg='<div class="errorfooter">'.$langs->trans('Alerta, hoy es el ultimo dia para facturar, comunicarse con el administrador').'</div>';
					$arrayErrDosing['dosing_aut'] = $mesg;
					$_SESSION['arrayErrDosing'] = $arrayErrDosing;
				}
				else
				{
					if ($_SESSION['lpp'] != 2)
					{
						$mesg='<div class="errorfooter">'.$langs->trans('Alerta, la fecha limite de emision esta vencido, comunicarse con el administrador').'</div>';
						if ($modseller == 2)
						{
							$arrayDosing['dosing_aut'] = false;
							$arrayErrDosing['dosing_aut'] = $mesg;
						}
						$_SESSION['arrayErrDosing'] = $arrayErrDosing;
						$_SESSION['arrayDosing'] = $arrayDosing;
					}
					else
						unset($_SESSION['arrayErrDosing']);
				}

			}
			else
			{
				$newnumfac = $objd->num_ini;
			}

			//fin verificacion de facturas


			print '<div class="tabsAction">';
			$parameters = array();
			$reshook = $hookmanager->executeHooks('addMoreActionsButtons', $parameters, $object, $action);
			if (empty($reshook))
			{
				//finalizar el pedido
				if ($end)
				{
					if ($object->statut == 1 && $object->total_ttc >= 0 && $numlines > 0 && $user->rights->ventas->valcommande)
					{
						print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '&amp;action=classifybilled&end=1">' . $langs->trans('CompleteOrder') . '</a></div>';
					}
				}
				else
				{
				// Valid
					if ($object->statut == 0 && $object->total_ttc >= 0 && $numlines > 0 && $user->rights->ventas->valcommande) {
						print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '&amp;action=validate">' . $langs->trans('Validate') . '</a></div>';
					}
				// Edit
					if ($object->statut == 1 && $user->rights->ventas->commande->edit) {
						print '<div class="inline-block divButAction"><a class="butAction" href="fiche.php?id=' . $object->id . '&amp;action=modif">' . $langs->trans('Modify') . '</a></div>';
					}

				// advanced
					if ($object->statut > 0 & $abc)
					{
						if ((empty($conf->global->MAIN_USE_ADVANCED_PERMS) || $user->rights->ventas->adv->crear))
						{
							print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=advance">'.$langs->trans('Advanceregistration').'</a>';
						}
						else
							print '<a class="butActionRefused" href="#">'.$langs->trans('Advanceregistration').'</a>';

					}

				// // Create event
				// if ($conf->agenda->enabled && ! empty($conf->global->MAIN_ADD_EVENT_ON_ELEMENT_CARD)) 				// Add hidden condition because this is not a
				//   // "workflow" action so should appears somewhere else on
				//   // page.
				//   {
				// 	print '<a class="butAction" href="' . DOL_URL_ROOT . '/comm/action/fiche.php?action=create&amp;origin=' . $object->element . '&amp;originid=' . $object->id . '&amp;socid=' . $object->socid . '">' . $langs->trans("AddAction") . '</a>';
				//   }
				// Send
				// if ($object->statut > 0) {
				//   if ((empty($conf->global->MAIN_USE_ADVANCED_PERMS) || $user->rights->commande->order_advance->send)) {
				// 	print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '&amp;action=presend&amp;mode=init">' . $langs->trans('SendByMail') . '</a></div>';
				//   } else
				// 	print '<div class="inline-block divButAction"><a class="butActionRefused" href="#">' . $langs->trans('SendByMail') . '</a></div>';
				// }

				// Ship
					$numshipping = 0;
					if (! empty($conf->expedition->enabled)) {
						$numshipping = $object->nb_expedition();

						if ($object->statut > 0 && $object->statut < 3 && $object->getNbOfProductsLines() > 0) {
							if (($conf->expedition_bon->enabled && $user->rights->expedition->creer) || ($conf->livraison_bon->enabled && $user->rights->expedition->livraison->creer)) {
								if ($user->rights->expedition->creer) {
									print '<div class="inline-block divButAction"><a class="butAction" href="' . DOL_URL_ROOT . '/expedition/shipment.php?id=' . $object->id . '">' . $langs->trans('ShipProduct') . '</a></div>';
								} else {
									print '<div class="inline-block divButAction"><a class="butActionRefused" href="#" title="' . dol_escape_htmltag($langs->trans("NotAllowed")) . '">' . $langs->trans('ShipProduct') . '</a></div>';
								}
							} else {
								$langs->load("errors");
								print '<div class="inline-block divButAction"><a class="butActionRefused" href="#" title="' . dol_escape_htmltag($langs->trans("ErrorModuleSetupNotComplete")) . '">' . $langs->trans('ShipProduct') . '</a></div>';
							}
						}
					}
				// Create intervention
					if ($conf->ficheinter->enabled) {
						$langs->load("interventions");

						if ($object->statut > 0 && $object->statut < 3 && $object->getNbOfServicesLines() > 0) {
							if ($user->rights->ficheinter->creer) {
								print '<div class="inline-block divButAction"><a class="butAction" href="' . DOL_URL_ROOT . '/fichinter/fiche.php?action=create&amp;origin=' . $object->element . '&amp;originid=' . $object->id . '&amp;socid=' . $object->socid . '">' . $langs->trans('AddIntervention') . '</a></div>';
							} else {
								print '<div class="inline-block divButAction"><a class="butActionRefused" href="#" title="' . dol_escape_htmltag($langs->trans("NotAllowed")) . '">' . $langs->trans('AddIntervention') . '</a></div>';
							}
						}
					}

				// Reopen a closed order
					if ($object->statut == 3 && $user->rights->ventas->commande->creer) {
						print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?id=' . $object->id . '&amp;action=reopen">' . $langs->trans('ReOpen') . '</a></div>';
					}

				// Create contract
					if ($conf->contrat->enabled && ($object->statut == 1 || $object->statut == 2)) {
						$langs->load("contracts");

						if ($user->rights->contrat->creer) {
							print '<div class="inline-block divButAction"><a class="butAction" href="' . DOL_URL_ROOT . '/contrat/fiche.php?action=create&amp;origin=' . $object->element . '&amp;originid=' . $object->id . '&amp;socid=' . $object->socid . '">' . $langs->trans('AddContract') . '</a></div>';
						}
					}

				// Create bill and Classify billed
				// Note: Even if module invoice is not enabled, we should be able to use button "Classified billed"
					if ($object->statut > 0 && ! $object->billed)
					{
						if (!empty($conf->facture->enabled) && $user->rights->ventas->sale->crear )
						{
							$lFacture = true;

						//verificamos que este disponible
							if ($_SESSION['arrayDosing']['dosing_aut'])
							{
								print '<div class="inline-block divButAction"><a class="butAction" href="' . DOL_URL_ROOT . '/ventas/affIndex.php?menu=facturation&id=NOUV&modseller=2&lp=1'.'&idc='.$object->id.'">' . $langs->trans("Finalinvoiceautomatic") . '</a></div>';
							}
							if ($_SESSION['arrayDosing']['dosing_man'])
							{
								print '<div class="inline-block divButAction"><a class="butAction" href="' . DOL_URL_ROOT . '/ventas/affIndex.php?menu=facturation&id=NOUV&modseller=1'.'&idc='.$object->id.'">' . $langs->trans("Finalinvoicemanual") . '</a></div>';
							}
						}
						if (! empty($conf->facture->enabled) && $user->rights->ventas->fact->crear && empty($conf->global->WORKFLOW_DISABLE_CREATE_INVOICE_FROM_ORDER))
						{
							if ($_SESSION['arrayDosing']['dosing_man'])
							{
								print '<div class="inline-block divButAction"><a class="butAction" href="' . DOL_URL_ROOT . '/ventas/factureant.php?action=create&amp;origin=' . $object->element . '&amp;originid=' . $object->id . '&amp;socid=' . $object->socid . '&amp;modseller=1">' . $langs->trans("Advancefacturemanual") . '</a></div>';
							}
							if ($_SESSION['arrayDosing']['dosing_aut'])
							{
								print '<div class="inline-block divButAction"><a class="butAction" href="' . DOL_URL_ROOT . '/ventas/factureant.php?action=create&amp;origin=' . $object->element . '&amp;originid=' . $object->id . '&amp;socid=' . $object->socid . '&amp;modseller=2">' . $langs->trans("Advancefactureautomatic") . '</a></div>';
							}
						}
						if ($user->rights->ventas->commande->creer && $object->statut > 2 && empty($conf->global->WORKFLOW_DISABLE_CLASSIFY_BILLED_FROM_ORDER) && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT)) {
							print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '&amp;action=classifybilled">' . $langs->trans("ClassifyBilled") . '</a></div>';
						}
					}

				// Set to shipped
					if (($object->statut == 1 || $object->statut == 2) && $user->rights->commande->cloturer) {
						print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '&amp;action=shipped">' . $langs->trans('ClassifyShipped') . '</a></div>';
					}

				// Clone
					if ($user->rights->ventas->commande->creer) {
				//	print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?id=' . $object->id . '&amp;socid=' . $object->socid . '&amp;action=clone&amp;object=order">' . $langs->trans("ToClone") . '</a></div>';
					}

				// Cancel order
					if ($object->statut == 1 && $user->rights->commande->annuler) {
						print '<div class="inline-block divButAction"><a class="butActionDelete" href="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '&amp;action=cancel">' . $langs->trans('Cancel') . '</a></div>';
					}

				// Delete order
					if ($user->rights->commande->supprimer) {
						if ($numshipping == 0) {
							print '<div class="inline-block divButAction"><a class="butActionDelete" href="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '&amp;action=delete">' . $langs->trans('Delete') . '</a></div>';
						} else {
							print '<div class="inline-block divButAction"><a class="butActionRefused" href="#" title="' . $langs->trans("ShippingExist") . '">' . $langs->trans("Delete") . '</a></div>';
						}
					}
				}
			}
			print '</div>';
		}
		print '<br>';
		if ($action != 'presend')
		{
			print '<div class="fichecenter"><div class="fichehalfleft">';
			// print '<table width="100%"><tr><td width="50%" valign="top">';
			// print '<a name="builddoc"></a>'; // ancre

			/*
			 * Documents generes
			*/
			$comref = dol_sanitizeFileName($object->ref);
			$file = $conf->commande->dir_output . '/' . $comref . '/' . $comref . '.pdf';
			$relativepath = $comref . '/' . $comref . '.pdf';
			$filedir = $conf->commande->dir_output . '/' . $comref;
			$urlsource = $_SERVER["PHP_SELF"] . "?id=" . $object->id.'&perm=1';
			$genallowed = $user->rights->ventas->commande->creer;
			$delallowed = $user->rights->commande->supprimer;
			$somethingshown = $formfile->show_documentsadd('commande', $comref, $filedir, $urlsource, $genallowed, $delallowed, $object->modelpdf, 1, 0, 0, 28, 0, '', '', '', $soc->default_lang);

			/*
			 * Linked object block
			 */
			$_SESSION['onlyCommande'] = true;
			$somethingshown = $objectadd->showLinkedObjectBlock();
			$_SESSION['onlyCommande'] = true;
			print '</div><div class="fichehalfright"><div class="ficheaddleft">';
			// print '</td><td valign="top" width="50%">';

			// List of actions on element
			include_once DOL_DOCUMENT_ROOT . '/core/class/html.formactions.class.php';
			$formactions = new FormActions($db);
			$somethingshown = $formactions->showactions($object, 'order', $socid);

			// print '</td></tr></table>';
			print '</div></div></div>';
		}

		//registro de anticipo
		//lista anticipos
		if ($user->rights->ventas->adv->crear && $object->statut == 1)
		{
			print '<table class="notopnoleftnoright" width="100%">';
			print '<tr>';
			print '<td align="left" colspan="10"><b>'.$langs->trans("Advances").'</b></td>';
			print '</tr>';

			print '<tr class="liste_titre">';
			print '<td>'.$langs->trans("Date").'</td>';
			print '<td>'.$langs->trans("Type").'</td>';
			print '<td>'.$langs->trans("Numero").'</td>';
			print '<td>'.$langs->trans("Description").'</td>';
			//print '<td>'.$langs->trans("ThirdParty").'</td>';
			//print '<td align="right">'.$langs->trans("Debit").'</td>';
			print '<td align="right">'.$langs->trans("Amount").'</td>';
			//print '<td align="right" width="80">'.$langs->trans("BankBalance").'</td>';
			print '<td align="center" width="60">';
			if ($object->type != 2 && $object->rappro) print $langs->trans("AccountStatementShort");
			else print '&nbsp;';
			print '</td>';
			print '</tr>';

			$sql = "SELECT b.rowid, b.dateo as do, b.datev as dv,";
			$sql.= " b.amount, b.label, b.rappro, b.num_releve, b.num_chq, b.fk_type";
			if ($mode_search)
			{
				$sql.= ", s.rowid as socid, s.nom as thirdparty";
			}
			$sql.= " FROM ".MAIN_DB_PREFIX."bank_account as ba";
			$sql.= ", ".MAIN_DB_PREFIX."bank as b";
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


			$sql.= " WHERE b.fk_account=".$_SESSION['fkCajaid'];
			$sql.= " AND b.num_chq = '".$object->ref."' ";
			$sql.= " AND b.fk_account = ba.rowid";
			$sql.= " AND ba.entity = ".$conf->entity;
			$sql.= $sql_rech;
			$sql.= $db->order("b.datev, b.datec", "ASC");  // We add date of creation to have correct order when everything is done the same day
			$sql.= $db->plimit($limitsql, 0);

			dol_syslog("account.php get transactions - sql=".$sql, LOG_DEBUG);
			$result = $db->query($sql);
			if ($result)
			{
				$now=dol_now();
				$nows=dol_print_date($now,'%Y%m%d');

			//$form->load_cache_types_paiements();
			//$form->cache_types_paiements

				$var=true;

				$num = $db->num_rows($result);
				$i = 0; $total = 0; $sep = 0;

				while ($i < $num)
				{
					$objp = $db->fetch_object($result);
					$total = price2num($total + $objp->amount,'MT');
					if ($i >= ($viewline * (($totalPages-$page)-1)))
					{
						$var=!$var;

				// Is it a transaction in future ?
						$dos=dol_print_date($db->jdate($objp->do),'%Y%m%d');
				//print "dos=".$dos." nows=".$nows;
				if ($dos > $nows && !$sep)		// Yes, we show a subtotal
				{
					$sep = 1 ;
					print '<tr class="liste_total"><td colspan="5">';
					print $langs->trans("CurrentBalance");
					print '</td>';
					print '<td align="right" nowrap><b>'.price($total - $objp->amount).'</b></td>';
					print "<td>&nbsp;</td>";
					print '</tr>';
				}

				print '<tr '.$bc[$var].'>';

				print '<td nowrap="nowrap">'.dol_print_date($db->jdate($objp->do),"day")."</td>\n";
				print "</td>\n";

				// Payment type
				print "<td nowrap>";
				$label=($langs->trans("PaymentTypeShort".$objp->fk_type)!="PaymentTypeShort".$objp->fk_type)?$langs->trans("PaymentTypeShort".$objp->fk_type):$objp->fk_type;

				if ($objp->fk_type == 'SOLD') $label='&nbsp;';
				print $label;
				print "</td>\n";

				// Num
				print '<td nowrap>'.($objp->num_chq?$objp->num_chq:"")."</td>\n";

				// Description
				print '<td>';
				// Show generic description
				if (preg_match('/^\((.*)\)$/i',$objp->label,$reg))
				{
					// Generic description because between (). We show it after translating.
					print $langs->trans($reg[1]);
				}
				else
				{
					print dol_trunc($objp->label,60);
				}
				print '</td>';

				// Add third party column
				// print '<td>';
				// print '</td>';

				// Amount
				if ($objp->amount < 0)
				{
					//					print '<td align="right" nowrap="nowrap">'.price($objp->amount * -1).'</td><td>&nbsp;</td>'."\n";
				}
				else
				{
					print '<td align="right" nowrap="nowrap">&nbsp;'.price($objp->amount).'</td>'."\n";
				}


				// Transaction reconciliated or edit link
				if ($objp->rappro && $object->canBeConciliated() > 0)  // If line not conciliated and account can be conciliated
				{
					print '<td align="center" nowrap>';
					print '<a href="'.DOL_URL_ROOT.'/compta/bank/ligne.php?rowid='.$objp->rowid.'&amp;account='.$object->id.'&amp;page='.$page.'">';
					print img_edit();
					print '</a>';
					print "&nbsp; ";
					print '<a href="releve.php?num='.$objp->num_releve.'&amp;account='.$object->id.'">'.$objp->num_releve.'</a>';
					print "</td>";
				}
				else
				{
					print '<td align="center">';
					if ($user->rights->ventas->adv->del)
					{
						print '<a href="'.$_SERVER["PHP_SELF"].'?action=deleteadvance&amp;rowid='.$objp->rowid.'&amp;id='.$object->id.'&amp;page='.$page.'">';
						print img_delete();
						print '</a>';
					}
					print '</td>';
				}

				print "</tr>";
			}

			$i++;
		}
			//$db->free($result);
	}
			//print '</table>';
}
		//fin lista anticipos


		// Form to add a transaction with no invoice
if ($user->rights->ventas->adv->crear && $action == 'advance' && $object->statut == 1)
{
	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="addadvance">';
	print '<input type="hidden" name="vline" value="'.$vline.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	print '<input type="hidden" name="socid" value="'.$object->socid.'">';
	print '<input type="hidden" name="cajaid" value="'.$_SESSION['fkCajaid'].'">';
	print '<input type="hidden" name="num_chq" value="'.$object->ref.'">';
	print '<input type="hidden" name="total_ttc" value="'.$object->total_ttc.'">';

	print '<tr '.$bc[false].'>';
	print '<td nowrap="nowrap">';
	print date('d-m-Y');
			//$form->select_date($dateop,'op',0,0,0,'transaction','','',1);
	print '</td>';
	print '<td nowrap="nowrap">';
			//		$form->select_types_paiements((GETPOST('operation')?GETPOST('operation'):($objectA->courant == 2 ? 'LIQ' : '')),'operation','1,2',2,1);
	$form->select_types_paiements('LIQ','operation','1,2',2,1);
	print '</td><td>';
	print '<input name="num_chq" class="flat" type="text" size="10" value="'.$object->ref.'" disabled="disabled"></td>';
	print '<td>';
	print '<input name="label" class="flat" type="text" size="34"  value="'.$langs->trans('Advance by product order').' '.$langs->trans('Number').' '.$object->ref.'" >';
	if ($nbcategories)
	{
		print '<br>'.$langs->trans("Category").': <select class="flat" name="cat1">'.$options.'</select>';
	}
	print '</td>';
			// print '<td align=right><input name="debit" class="flat" type="text" size="4" value="'.GETPOST("debit").'"></td>';
	print '<td align=right>';
	$balance = $object->total_ttc - $total;
	print '<input name="credit" class="flat" type="number" min="0" max="'.$balance.'" step="any" value="'.GETPOST("credit").'"></td>';
	print '<td colspan="2" align="center">';
	print '<input type="submit" name="save" class="button" value="'.$langs->trans("Add").'"><br>';
	print '<input type="submit" name="cancel" class="button" value="'.$langs->trans("Cancel").'">';
	print '</td></tr>';
	print "</form>";

	print '<tr class="noborder"><td colspan="8">&nbsp;</td></tr>'."\n";
			// Show total
	if ($page == 0 && ! $mode_search)
	{
		print '<tr class="liste_total"><td align="left" colspan="5">';
		if ($sep) print '&nbsp;';
		else print $langs->trans("CurrentBalance");
		print '</td>';
		print '<td align="right" nowrap>'.price($total).'</td>';
		print '<td>&nbsp;</td>';
		print '</tr>';
	}

	print '</table>';
}
		//fin registro anticipo

/*
 * Action presend
 */
if ($action == 'presend')
{
	$ref = dol_sanitizeFileName($object->ref);
	include_once DOL_DOCUMENT_ROOT . '/core/lib/files.lib.php';
	$fileparams = dol_most_recent_file($conf->commande->dir_output . '/' . $ref, preg_quote($ref, '/'));
	$file = $fileparams ['fullname'];

			// Build document if it not exists
	if (! $file || ! is_readable($file)) {
				// Define output language
		$outputlangs = $langs;
		$newlang = '';
		if ($conf->global->MAIN_MULTILANGS && empty($newlang) && ! empty($_REQUEST['lang_id']))
			$newlang = $_REQUEST['lang_id'];
		if ($conf->global->MAIN_MULTILANGS && empty($newlang))
			$newlang = $object->client->default_lang;
		if (! empty($newlang)) {
			$outputlangs = new Translate("", $conf);
			$outputlangs->setDefaultLang($newlang);
		}

		$result = commande_pdf_create($db, $object, GETPOST('model') ? GETPOST('model') : $object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
		if ($result <= 0) {
			dol_print_error($db, $result);
			exit();
		}
		$fileparams = dol_most_recent_file($conf->commande->dir_output . '/' . $ref, preg_quote($ref, '/'));
		$file = $fileparams ['fullname'];
	}

	print '<br>';
	print_titre($langs->trans('SendOrderByMail'));

			// Cree l'objet formulaire mail
	include_once DOL_DOCUMENT_ROOT . '/core/class/html.formmail.class.php';
	$formmail = new FormMail($db);
	$formmail->fromtype = 'user';
	$formmail->fromid = $user->id;
	$formmail->fromname = $user->getFullName($langs);
	$formmail->frommail = $user->email;
	$formmail->withfrom = 1;
	$liste = array();
	foreach ($object->thirdparty->thirdparty_and_contact_email_array(1) as $key => $value)
		$liste [$key] = $value;
	$formmail->withto = GETPOST('sendto') ? GETPOST('sendto') : $liste;
	$formmail->withtocc = $liste;
	$formmail->withtoccc = $conf->global->MAIN_EMAIL_USECCC;
	if (empty($object->ref_client)) {
		$formmail->withtopic = $langs->trans('SendOrderRef', '__ORDERREF__');
	} else if (! empty($object->ref_client)) {
		$formmail->withtopic = $langs->trans('SendOrderRef', '__ORDERREF__(__REFCLIENT__)');
	}
	$formmail->withfile = 2;
	$formmail->withbody = 1;
	$formmail->withdeliveryreceipt = 1;
	$formmail->withcancel = 1;
			// Tableau des substitutions
	$formmail->substit ['__ORDERREF__'] = $object->ref;
	$formmail->substit ['__SIGNATURE__'] = $user->signature;
	$formmail->substit ['__REFCLIENT__'] = $object->ref_client;
	$formmail->substit ['__PERSONALIZED__'] = '';
	$formmail->substit ['__CONTACTCIVNAME__'] = '';

	$custcontact = '';
	$contactarr = array();
	$contactarr = $object->liste_contact(- 1, 'external');

	if (is_array($contactarr) && count($contactarr) > 0) {
		foreach ($contactarr as $contact) {
			if ($contact ['libelle'] == $langs->trans('TypeContact_commande_external_CUSTOMER')) {
				$contactstatic = new Contact($db);
				$contactstatic->fetch($contact ['id']);
				$custcontact = $contactstatic->getFullName($langs, 1);
			}
		}

		if (! empty($custcontact)) {
			$formmail->substit ['__CONTACTCIVNAME__'] = $custcontact;
		}
	}

			// Tableau des parametres complementaires
	$formmail->param ['action'] = 'send';
	$formmail->param ['models'] = 'order_send';
	$formmail->param ['orderid'] = $object->id;
	$formmail->param ['returnurl'] = $_SERVER["PHP_SELF"] . '?id=' . $object->id;

			// Init list of files
	if (GETPOST("mode") == 'init') {
		$formmail->clear_attached_files();
		$formmail->add_attached_files($file, basename($file), dol_mimetype($file));
	}

			// Show form
	print $formmail->get_form();

	print '<br>';
}
}
}
print '</div>';
llxFooter();
$db->close();
