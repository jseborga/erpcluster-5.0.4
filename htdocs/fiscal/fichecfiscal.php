<?php
/* Copyright (C) 2002-2005	Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2004-2013	Laurent Destailleur 	<eldy@users.sourceforge.net>
 * Copyright (C) 2004		Christophe Combelles	<ccomb@free.fr>
 * Copyright (C) 2005		Marc Barilley			<marc@ocebo.fr>
 * Copyright (C) 2005-2013	Regis Houssin			<regis.houssin@capnetworks.com>
 * Copyright (C) 2010-2014	Juanjo Menent			<jmenent@2byte.es>
 * Copyright (C) 2013		Philippe Grand			<philippe.grand@atoo-net.com>
 * Copyright (C) 2013       Florian Henry		  	<florian.henry@open-concept.pro>
 * Copyright (C) 2014       Marcos García           <marcosgdf@gmail.com>
 * Copyright (C) 2016       Ramiro Queso            <ramiro@gmail.com>
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

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';

require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/modules/supplier_invoice/modules_facturefournisseur.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/paiementfourn.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/fourn.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
if (!empty($conf->produit->enabled))
	require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
if (!empty($conf->projet->enabled)) {
	require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';
}
if (!empty($conf->purchase->enabled)) {
	require_once DOL_DOCUMENT_ROOT.'/purchase/lib/purchase.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/purchase/class/facturefournadd.class.php';
	require_once DOL_DOCUMENT_ROOT.'/purchase/class/facturefournadd.class.php';
	require_once DOL_DOCUMENT_ROOT.'/purchase/class/facturefourndetadd.class.php';
}

require_once DOL_DOCUMENT_ROOT.'/fiscal/class/entity.class.php';
require_once DOL_DOCUMENT_ROOT.'/fiscal/class/entityaddext.class.php';
require_once DOL_DOCUMENT_ROOT.'/fiscal/class/facturefourndetfiscalext.class.php';

require_once DOL_DOCUMENT_ROOT.'/purchase/class/fournisseur.factureext.class.php';

$langs->load('bills');
$langs->load('compta');
$langs->load('suppliers');
$langs->load('companies');
$langs->load('products');
$langs->load('banks');

$id			= (GETPOST('facid','int') ? GETPOST('facid','int') : GETPOST('id','int'));
$action		= GETPOST("action");
$confirm	= GETPOST("confirm");
$ref		= GETPOST('ref','alpha');

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
$extrafields 	= new ExtraFields($db);
$factureadd 	= new Facturefournadd($db);
$objentity 		= new Entity($db);
$objentityadd 	= new Entityaddext($db);
$objectdetadd = new Facturefourndetadd($db);

// fetch optionals attributes and labels
$extralabels=$extrafields->fetch_name_optionals_label($object->table_element);

// Load object
if ($id > 0 || ! empty($ref))
{
	$ret=$object->fetch($id, $ref);
}

$permissionnote=$user->rights->fournisseur->facture->creer;	// Used by the include of actions_setnotes.inc.php


/*
 * Actions
 */

$parameters=array('socid'=>$socid);
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

include DOL_DOCUMENT_ROOT.'/core/actions_setnotes.inc.php';	// Must be include, not includ_once

// Action clone object
if ($action == 'addcfiscal')
{
	$factureadd->code_facture = GETPOST('code_facture');
	$factureadd->code_type_purchase = GETPOST('code_type_purchase');
	$factureadd->nit_company = GETPOST('nit_company');
	$factureadd->fk_facture_fourn = $id;
	$factureadd->code_facture = GETPOST('code_facture');
	$factureadd->code_type_purchase = GETPOST('code_type_purchase');
	$factureadd->nfiscal = GETPOST('nfiscal');
	$factureadd->ndui = GETPOST('ndui');
	$factureadd->nit = GETPOST('nit');
	$factureadd->razsoc = GETPOST('razsoc');
	$factureadd->datec = $datec;
	$factureadd->num_autoriz = GETPOST('num_autoriz');
	$factureadd->cod_control = GETPOST('cod_control');
	$factureadd->amountfiscal = GETPOST('amountfiscal');
	$factureadd->amountnofiscal = GETPOST('amountnofiscal');
	$factureadd->amount_ice = GETPOST('amount_ice')+0;
	$factureadd->discount = GETPOST('discount')+0;
	$factureadd->tms = dol_now();

	$result=$factureadd->create($user);
	if ($result > 0)
	{
		setEventMessages($langs->trans('Saverecord'),null, 'mesgs');
		header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
		exit;
	}
	else
	{
		$langs->load("errors");
		setEventMessages($factureadd->error,$factureadd->errors, 'errors');
		$action='editv';
	}
}

if ($action == 'updatecfiscal')
{
	$factureadd->fetch(GETPOST('idr'));
	if ($factureadd->id == GETPOST('idr'))
	{
		//$factureadd->code_facture = GETPOST('code_facture');
		//$factureadd->code_type_purchase = GETPOST('code_type_purchase');
		$factureadd->nit_company = GETPOST('nit_company');
		$factureadd->nfiscal = GETPOST('nfiscal');
		$factureadd->ndui = GETPOST('ndui');
		$factureadd->nit = GETPOST('nit');
		$factureadd->razsoc = GETPOST('razsoc');
		//$factureadd->datec = $datec;
		$factureadd->num_autoriz = GETPOST('num_autoriz');
		$factureadd->cod_control = GETPOST('cod_control');
		$factureadd->amountfiscal = GETPOST('amountfiscal');
		$factureadd->amountnofiscal = GETPOST('amountnofiscal');
		$factureadd->amount_ice = GETPOST('amount_ice')+0;
		$factureadd->discount = GETPOST('discount')+0;
		$factureadd->tms = dol_now();

		$result=$factureadd->update($user);
		if ($result <=0)
		{
			setEventMessages($factureadd->error,$factureadd->errors, 'errors');
			$error++;
		}
		if (!$error)
		{
			header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
			exit;
		}
		else
			$action='editv';
	}
}




// Link invoice to order
if (GETPOST('linkedOrder')) {
	$object->fetch($id);
	$object->fetch_thirdparty();
	$result = $object->add_object_linked('order_supplier', GETPOST('linkedOrder'));
}

// Add file in email form
if (GETPOST('addfile'))
{
	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

	// Set tmp user directory TODO Use a dedicated directory for temp mails files
	$vardir=$conf->user->dir_output."/".$user->id;
	$upload_dir_tmp = $vardir.'/temp';

	dol_add_file_process($upload_dir_tmp,0,0);
	$action='presend';
}

// Remove file in email form
if (! empty($_POST['removedfile']))
{
	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

	// Set tmp user directory
	$vardir=$conf->user->dir_output."/".$user->id;
	$upload_dir_tmp = $vardir.'/temp';

	// TODO Delete only files that was uploaded from email form
	dol_remove_file_process($_POST['removedfile'],0);
	$action='presend';
}

// Send mail
if ($action == 'send' && ! $_POST['addfile'] && ! $_POST['removedfile'] && ! $_POST['cancel'])
{
	$langs->load('mails');

	$object->fetch($id);
	$result=$object->fetch_thirdparty();
	if ($result > 0)
	{
//        $ref = dol_sanitizeFileName($object->ref);
//        $file = $conf->fournisseur->facture->dir_output.'/'.get_exdir($object->id,2).$ref.'/'.$ref.'.pdf';

//        if (is_readable($file))
//        {
		if ($_POST['sendto'])
		{
				// Le destinataire a ete fourni via le champ libre
			$sendto = $_POST['sendto'];
			$sendtoid = 0;
		}
		elseif ($_POST['receiver'] != '-1')
		{
				// Recipient was provided from combo list
				if ($_POST['receiver'] == 'thirdparty') // Id of third party
				{
					$sendto = $object->client->email;
					$sendtoid = 0;
				}
				else	// Id du contact
				{
					$sendto = $object->client->contact_get_property($_POST['receiver'],'email');
					$sendtoid = $_POST['receiver'];
				}
			}

			if (dol_strlen($sendto))
			{
				$langs->load("commercial");

				$from = $_POST['fromname'] . ' <' . $_POST['frommail'] .'>';
				$replyto = $_POST['replytoname']. ' <' . $_POST['replytomail'].'>';
				$message = $_POST['message'];
				$sendtocc = $_POST['sendtocc'];
				$deliveryreceipt = $_POST['deliveryreceipt'];

				if ($action == 'send')
				{
					if (dol_strlen($_POST['subject'])) $subject=$_POST['subject'];
					else $subject = $langs->transnoentities('CustomerOrder').' '.$object->ref;
					$actiontypecode='AC_SUP_INV';
					$actionmsg = $langs->transnoentities('MailSentBy').' '.$from.' '.$langs->transnoentities('To').' '.$sendto;
					if ($message)
					{
						if ($sendtocc) $actionmsg = dol_concatdesc($actionmsg, $langs->transnoentities('Bcc') . ": " . $sendtocc);
						$actionmsg = dol_concatdesc($actionmsg, $langs->transnoentities('MailTopic') . ": " . $subject);
						$actionmsg = dol_concatdesc($actionmsg, $langs->transnoentities('TextUsedInTheMessageBody') . ":");
						$actionmsg = dol_concatdesc($actionmsg, $message);
					}
					$actionmsg2=$langs->transnoentities('Action'.$actiontypecode);
				}

				// Create form object
				include_once DOL_DOCUMENT_ROOT.'/core/class/html.formmail.class.php';
				$formmail = new FormMail($db);

				$attachedfiles=$formmail->get_attached_files();
				$filepath = $attachedfiles['paths'];
				$filename = $attachedfiles['names'];
				$mimetype = $attachedfiles['mimes'];

				// Send mail
				require_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';
				$mailfile = new CMailFile($subject,$sendto,$from,$message,$filepath,$mimetype,$filename,$sendtocc,'',$deliveryreceipt,-1);
				if ($mailfile->error)
				{
					setEventMessage($mailfile->error,'errors');
				}
				else
				{
					$result=$mailfile->sendfile();
					if ($result)
					{
						$mesg=$langs->trans('MailSuccessfulySent',$mailfile->getValidAddress($from,2),$mailfile->getValidAddress($sendto,2));		// Must not contain "
						setEventMessage($mesg);

						$error=0;

						// Initialisation donnees
						$object->sendtoid		= $sendtoid;
						$object->actiontypecode	= $actiontypecode;
						$object->actionmsg		= $actionmsg;
						$object->actionmsg2		= $actionmsg2;
						$object->fk_element		= $object->id;
						$object->elementtype	= $object->element;

						// Appel des triggers
						include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
						$interface=new Interfaces($db);
						$result=$interface->run_triggers('BILL_SUPPLIER_SENTBYMAIL',$object,$user,$langs,$conf);
						if ($result < 0) {
							$error++; $object->errors=$interface->errors;
						}
						// Fin appel triggers

						if ($error)
						{
							dol_print_error($db);
						}
						else
						{
							// Redirect here
							// This avoid sending mail twice if going out and then back to page
							header('Location: '.$_SERVER["PHP_SELF"].'?id='.$object->id);
							exit;
						}
					}
					else
					{
						$langs->load("other");
						if ($mailfile->error)
						{
							$mesg.=$langs->trans('ErrorFailedToSendMail',$from,$sendto);
							$mesg.='<br>'.$mailfile->error;
						}
						else
						{
							$mesg.='No mail sent. Feature is disabled by option MAIN_DISABLE_ALL_MAILS';
						}
						setEventMessage($mesg, 'errors');
					}
				}
			}

			else
			{
				$langs->load("other");
				setEventMessage($langs->trans('ErrorMailRecipientIsEmpty'), 'errors');
				dol_syslog('Recipient email is empty');
			}
/*        }
		else
		{
			$langs->load("errors");
			$mesg='<div class="error">'.$langs->trans('ErrorCantReadFile',$file).'</div>';
			dol_syslog('Failed to read file: '.$file);
		}*/
	}
	else
	{
		$langs->load("other");
		setEventMessage($langs->trans('ErrorFailedToReadEntity',$langs->trans("Invoice")), 'errors');
		dol_syslog('Unable to read data from the invoice. The invoice file has perhaps not been generated.');
	}

	//$action = 'presend';
}

// Build document
elseif ($action == 'builddoc')
{
	// Save modele used
	$object->fetch($id);
	$object->fetch_thirdparty();

	// Save last template used to generate document
	if (GETPOST('model')) $object->setDocModel($user, GETPOST('model','alpha'));

	$outputlangs = $langs;
	$newlang=GETPOST('lang_id','alpha');
	if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$object->client->default_lang;
	if (! empty($newlang))
	{
		$outputlangs = new Translate("",$conf);
		$outputlangs->setDefaultLang($newlang);
	}
	$result = $object->generateDocument($object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
	if ($result	<= 0)
	{
		dol_print_error($db,$result);
		exit;
	}
}
// Make calculation according to calculationrule
elseif ($action == 'calculate')
{
	$calculationrule=GETPOST('calculationrule');

	$object->fetch($id);
	$object->fetch_thirdparty();
	$result=$object->update_price(0, (($calculationrule=='totalofround')?'0':'1'), 0, $object->thirdparty);
	if ($result	<= 0)
	{
		dol_print_error($db,$result);
		exit;
	}
}
// Delete file in doc form
elseif ($action == 'remove_file')
{
	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

	if ($object->fetch($id))
	{
		$object->fetch_thirdparty();
		$upload_dir =	$conf->fournisseur->facture->dir_output . "/";
		$file =	$upload_dir	. '/' .	GETPOST('file');
		$ret=dol_delete_file($file,0,0,0,$object);
		if ($ret) setEventMessage($langs->trans("FileWasRemoved", GETPOST('urlfile')));
		else setEventMessage($langs->trans("ErrorFailToDeleteFile", GETPOST('urlfile')), 'errors');
	}
}

elseif ($action == 'update_extras')
{
	// Fill array 'array_options' with data from add form
	$extralabels=$extrafields->fetch_name_optionals_label($object->table_element);
	$ret = $extrafields->setOptionalsFromPost($extralabels,$object,GETPOST('attribute'));
	if ($ret < 0) $error++;

	if (!$error)
	{
		// Actions on extra fields (by external module or standard code)
		// FIXME le hook fait double emploi avec le trigger !!
		$hookmanager->initHooks(array('supplierinvoicedao'));
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

if (! empty($conf->global->MAIN_DISABLE_CONTACTS_TAB) && $user->rights->fournisseur->facture->creer)
{
	if ($action == 'addcontact')
	{
		$result = $object->fetch($id);

		if ($result > 0 && $id > 0)
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
				setEventMessage($langs->trans("ErrorThisContactIsAlreadyDefinedAsThisType"), 'errors');
			}
			else
			{
				setEventMessage($object->error, 'errors');
			}
		}
	}

	// bascule du statut d'un contact
	else if ($action == 'swapstatut')
	{
		if ($object->fetch($id))
		{
			$result=$object->swapContactStatus(GETPOST('ligne'));
		}
		else
		{
			dol_print_error($db);
		}
	}

	// Efface un contact
	else if ($action == 'deletecontact')
	{
		$object->fetch($id);
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


/*
 *	View
 */

$form = new Form($db);
$formfile = new FormFile($db);
$bankaccountstatic=new Account($db);
$title = $langs->trans('FactureFournisseur');
$morejs = array();
$morecss = array('/fiscal/css/style.css',);
llxHeader('',$title,'','','','',$morejs,$morecss,0,0);


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
	$resadd = $factureadd->fetch(0,$object->id);

	$societe = new Fournisseur($db);
	$result=$societe->fetch($object->socid);
	if ($result < 0) dol_print_error($db);

		// fetch optionals attributes and labels
	$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

		//	View card
	$head = purchase_facturefourn_prepare_head($object);

	$titre=$langs->trans('SupplierInvoice');

	dol_fiche_head($head, 'Fiscal', $titre, 0, 'bill');
	$formConfirm = '';
		// Confirmation de la suppression d'une ligne produit
	if ($action == 'confirm_delete_line')
	{
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id.'&lineid='.$_GET["lineid"], $langs->trans('DeleteProductLine'), $langs->trans('ConfirmDeleteProductLine'), 'confirm_delete_line', '', 1, 1);
	}

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
			if (! empty($conf->global->FAC_FORCE_DATE_VALIDATION))
			{
				$object->date=dol_now();
					//TODO: Possibly will have to control payment information into suppliers
					//$object->date_lim_reglement=$object->calculate_date_lim_reglement();
			}
			$numref = $object->getNextNumRef($societe);
		}
		else
		{
			$numref = $object->ref;
		}

		$text=$langs->trans('ConfirmValidateBill',$numref);
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

	if (!$formconfirm) {
		$parameters=array('lineid'=>$lineid);
		$formconfirm=$hookmanager->executeHooks('formConfirm',$parameters,$object,$action);
			   // Note that $action and $object may have been modified by hook
	}

		// Print form confirm
	print $formconfirm;

		// 	Invoice
	print '<table class="border" width="100%">';

	$linkback = '<a href="'.DOL_URL_ROOT.'/fourn/facture/list.php'.(! empty($socid)?'?socid='.$socid:'').'">'.$langs->trans("BackToList").'</a>';

		// Ref
	print '<tr><td class="nowrap" width="20%">'.$langs->trans("Ref").'</td><td colspan="4">';
	print $form->showrefnav($object, 'ref', $linkback, 1, 'ref', 'ref');
	print '</td>';
	print "</tr>\n";

		// Ref supplier
	print '<tr><td>'.$form->editfieldkey("RefSupplier",'ref_supplier',$object->ref_supplier,$object,($object->statut<2 && $user->rights->fournisseur->facture->creer)).'</td><td colspan="4">';
	print $form->editfieldval("RefSupplier",'ref_supplier',$object->ref_supplier,$object,($object->statut<2 && $user->rights->fournisseur->facture->creer));
	print '</td></tr>';

		// Third party
	print '<tr><td>'.$langs->trans('Supplier').'</td><td colspan="4">'.$societe->getNomUrl(1,'supplier');
	print ' &nbsp; (<a href="'.DOL_URL_ROOT.'/fourn/facture/list.php?socid='.$object->socid.'">'.$langs->trans('OtherBills').'</a>)</td>';
	print '</tr>';

		// Type
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

		// Label
	print '<tr><td>'.$form->editfieldkey("Label",'label',$object->label,$object,($object->statut<2 && $user->rights->fournisseur->facture->creer)).'</td>';
	print '<td colspan="3">'.$form->editfieldval("Label",'label',$object->label,$object,($object->statut<2 && $user->rights->fournisseur->facture->creer)).'</td>';

		/*
		 * List of payments
		 */
		$nbrows=9; $nbcols=2;
		if (! empty($conf->projet->enabled)) $nbrows++;
		if (! empty($conf->banque->enabled)) { $nbrows++; $nbcols++; }

		// Local taxes
		if ($societe->localtax1_assuj=="1") $nbrows++;
		if ($societe->localtax2_assuj=="1") $nbrows++;

		print '<td rowspan="'.$nbrows.'" valign="top">';

		print '</td>';

		print '</tr>';

		// Date
		print '<tr><td>'.$form->editfieldkey("Date",'datef',$object->datep,$object,($object->statut<2 && $user->rights->fournisseur->facture->creer && $object->getSommePaiement() <= 0),'datepicker').'</td><td colspan="3">';
		print $form->editfieldval("Date",'datef',$object->datep,$object,($object->statut<2 && $user->rights->fournisseur->facture->creer && $object->getSommePaiement() <= 0),'datepicker');
		print '</td>';


		// Status
		$alreadypaid=$object->getSommePaiement();
		print '<tr><td>'.$langs->trans('Status').'</td><td colspan="3">'.$object->getLibStatut(4,$alreadypaid).'</td></tr>';


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
				$form->form_project($_SERVER['PHP_SELF'].'?id='.$object->id, empty($conf->global->PROJECT_CAN_ALWAYS_LINK_TO_ALL_SUPPLIERS)?$object->socid:'-1', $object->fk_project, 'projectid');
			}
			else
			{
				$form->form_project($_SERVER['PHP_SELF'].'?id='.$object->id, $object->socid, $object->fk_project, 'none');
			}
			print '</td>';
			print '</tr>';
		}

		// Other attributes
		$cols = 4;
		include DOL_DOCUMENT_ROOT . '/core/tpl/extrafields_view.tpl.php';

		print '</table>';

		if (! empty($conf->global->MAIN_DISABLE_CONTACTS_TAB))
		{
			print '<br>';
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

		// Show object lines
        if (! empty($object->lines))
        {
        	//recuperamos de la tabla adicional para enviar datos
        	$ret = $object->printObjectLinesadd($action, $societe, $mysoc, $lineid, 1);
        }

        $num=count($object->lines);

		// Form to add new line
        if ($object->statut == FactureFournisseur::STATUS_DRAFT && $user->rights->fournisseur->facture->creer)
        {
        	//if ($action != 'editline')
        	//{
        	//	$var = true;

				// Add free products/services
        	//	$object->formAddObjectLineadd(1, $societe, $mysoc);

        	//	$parameters = array();
			//	$reshook = $hookmanager->executeHooks('formAddObjectLine', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
			//}
        }

        print '</table>';

        print '</form>';

        dol_fiche_end();


		//registro de la factura fiscal




		//registro de la factura fiscal
        if ($conf->fiscal->enabled)
        {
        	dol_fiche_head();

			//facturefournadd

			//buscamos el vdosing
        	if ($action == 'editv' || $action == 'create')
        	{
        		print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
        		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
        		if ($action == 'editv')
        			print '<input type="hidden" name="action" value="updatecfiscal">';
        		else
        			print '<input type="hidden" name="action" value="addcfiscal">';

        		print '<input type="hidden" name="facid" value="'.$object->id.'">'."\n";
        		print '<input type="hidden" name="idr" value="'.$factureadd->id.'">'."\n";
        		print '<input type="hidden" id="total" name="total" value="'.$object->total_ttc.'">'."\n";
        	}
        	print '<table class="noborder" width="100%">';

        	print "<tr class=\"liste_titre\">";
        	print_liste_field_titre($langs->trans("Factura").'/<br>'.$langs->trans("Nro. DUI"),"", "","","","");
        	print_liste_field_titre($langs->trans("Nit"),"", "","","","");
        	print_liste_field_titre($langs->trans("Name"),"", "","","","");
        	print_liste_field_titre($langs->trans("Date"),"", "","","","");
        	print_liste_field_titre($langs->trans("Autorizationnumber"),"", "",'','','align="center"');
        	print_liste_field_titre($langs->trans("Controlcode"),"", "",'','','align="center"');
        	print_liste_field_titre($langs->trans("Nosujetoaimp"),"", "",'','','align="right"');
        	print_liste_field_titre($langs->trans("Descuento"),"", "",'','','align="right"');
        	print_liste_field_titre($langs->trans("Baseimpcf"),"", "",'','','align="right"');
        	print_liste_field_titre($langs->trans("ICE"),"", "",'','','align="right"');
        	print_liste_field_titre($langs->trans("TotalIva"),"", "",'','','align="right"');
        	print_liste_field_titre($langs->trans("Action"),"", "",'','','align="right"');
        	print "</tr>\n";
        	$var=true;
        	$lViewedit = true;
        	if ($action == 'editv' || $action=='create')
        	{
        		$res = $objentity->fetchAll('','',0,0,array(),'AND');
        		$showempty = false;
        		if ($res>0) $showempty = true;
        		list($nb,$options) = $objentityadd->select_entity($campo='nit',($factureadd->nit_company?$factureadd->nit_company:$conf->global->MAIN_INFO_TVAINTRA),1,$showempty);
        		if ($nb<0) $lViewedit = false;
        		print "<tr $bc[$var]>";
        		print '<td colspan="11">';
        		print $langs->trans('Nitcompany').' ';
        		print '<select name="nit_company">'.$options.'</select>';
        		print '</td>';
        		print '</tr>';
        	}
        	else
        	{
        		print "<tr $bc[$var]>";
        		print '<td colspan="11">';
        		print $langs->trans('Nitcompany').': ';
        		print $factureadd->nit_company;
        		print '</td>';
        		print '</tr>';

        	}
        	if ($factureadd->status == 2)
        		print '<tr style="color:#ff0000;">';
        	else
        		print "<tr $bc[$var]>";
        	if ($lViewedit && ($action == 'editv' || $action=='create'))
        	{
        		print '<td>'.'<input type="text" name="nfiscal" value="'.$factureadd->nfiscal.'" size="5" placeholder="'.$langs->trans('Facture').'">';
        		print '<br><input type="text" name="ndui" value="'.$factureadd->ndui.'" size="5" placeholder="'.$langs->trans('Nro. Dui').'">';
        		print '</td>';
        		print '<td>'.'<input type="text" name="nit" value="'.$factureadd->nit.'" size="8"></td>';
        		print '<td>'.'<textarea name="razsoc" cols="10" rows="2">'.$factureadd->razsoc.'</textarea></td>';
        		print '<td>';
        		$form->select_date($object->datep,'re','','','',"crea_commande",1,0);
				//print $form->load_tva('tauxtva',$object->lines[$i]->tva_tx,$societe,$mysoc);
        		print '</td>';
        		print '<td align="center">'.'<input type="text" name="num_autoriz" value="'.$factureadd->num_autoriz.'" size="8"></td>';
        		print '<td align="center">'.'<input type="text"  name="cod_control" value="'.$factureadd->cod_control.'" size="7"></td>';
        		print '<td align="right">'.'<input type="number" id="amountnofiscal" class="len60" step="any" name="amountnofiscal" value="'.$factureadd->amountnofiscal.'" size="7"></td>';
        		print '<td align="right">'.'<input type="number" id="discount" class="len60" step="any" name="discount" value="'.$factureadd->discount.'" size="7"></td>';
        		print '<td align="right">'.'<input type="number" id="amountfiscal" class="len60" step="any" name="amountfiscal" value="'.$factureadd->amountfiscal.'" size="7"></td>';
        		print '<td align="right">'.'<input type="number" id="amount_ice" class="len60" step="any" name="amount_ice" value="'.$factureadd->amount_ice.'" size="7"></td>';
        		print '<td align="right">'.'<span id="niva">'.price($object->total_tva).'</span>'.'</td>';
        		print '<td align="right">'.'<input class="butAction" type="submit" name="save" value="'.$langs->trans('Update').'">'.'</td>';
        	}
        	else
        	{
        		print '<td>'.img_picto($langs->trans("Invoice"),DOL_URL_ROOT.'/ventas/img/invoice.png','',1).' '.$factureadd->nfiscal.'</a></td>';
        		print '<td>'.$factureadd->nit.'</td>';
        		print '<td>'.$factureadd->razsoc.'</td>';
        		print '<td>'.dol_print_date($factureadd->datec,'day').'</td>';
        		print '<td align="center">'.$factureadd->num_autoriz.'</td>';
        		print '<td align="center">'.$factureadd->cod_control.'</td>';
			//print '<td align="right">'.price($object->total_ttc).'</td>';
        		print '<td align="right">'.price($factureadd->amountnofiscal).'</td>';
        		print '<td align="right">'.price($factureadd->discount).'</td>';
        		print '<td align="right">'.price($factureadd->amountfiscal).'</td>';
        		print '<td align="right">'.price($factureadd->amount_ice).'</td>';
        		print '<td align="right">'.price($object->total_tva).'</td>';
        		if ($user->rights->fiscal->cfiscal->mod && $object->fk_statut==1)
        		{
        			print '<td align="right">'.'<a href="'.$_SERVER['PHP_SELF'].'?facid='.$object->id.'&action=editv">'.img_picto($langs->trans('Edit'),'edit').'</a></td>';
        		}
        		else
        			print '<td></td>';
        	}
        	print "</tr>\n";

					 //$db->free($result);

        	print "</table>";
        	if ($action == 'editv')
        	{
        		print '</form>';
        	}

        	dol_fiche_end();
        }


        if ($action != 'presend')
        {
			/*
			 * Boutons actions
			 */

			print '<div class="tabsAction">';


			// Reopen a standard paid invoice
			if ($object->fk_statut >= 1 && $factureadd->fk_facture_fourn != $object->id)
			{


				print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?facid=' . $object->id . '&amp;action=create">' . $langs->trans('Facture') . '</a></div>';
			}

		}
		/*
		 * Show mail form
		*/
		if ($action == 'presend')
		{
			$ref = dol_sanitizeFileName($object->ref);
			include_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
			$fileparams = dol_most_recent_file($conf->fournisseur->facture->dir_output.'/'.get_exdir($object->id,2).$ref, preg_quote($ref, '/').'([^\-])+');
			$file=$fileparams['fullname'];

			// Define output language
			$outputlangs = $langs;
			$newlang = '';
			if ($conf->global->MAIN_MULTILANGS && empty($newlang) && ! empty($_REQUEST['lang_id']))
				$newlang = $_REQUEST['lang_id'];
			if ($conf->global->MAIN_MULTILANGS && empty($newlang))
				$newlang = $object->client->default_lang;

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
					dol_print_error($db,$result);
					exit;
				}
				$fileparams = dol_most_recent_file($conf->fournisseur->facture->dir_output.'/'.get_exdir($object->id,2).$ref, preg_quote($ref, '/').'([^\-])+');
				$file=$fileparams['fullname'];
			}

			print '<br>';
			print_titre($langs->trans('SendBillByMail'));

			// Cree l'objet formulaire mail
			include_once DOL_DOCUMENT_ROOT.'/core/class/html.formmail.class.php';
			$formmail = new FormMail($db);
			$formmail->param['langsmodels']=(empty($newlang)?$langs->defaultlang:$newlang);
			$formmail->fromtype = 'user';
			$formmail->fromid   = $user->id;
			$formmail->fromname = $user->getFullName($langs);
			$formmail->frommail = $user->email;
			$formmail->withfrom=1;
			$liste=array();
			foreach ($object->thirdparty->thirdparty_and_contact_email_array(1) as $key=>$value)	$liste[$key]=$value;
			$formmail->withto=GETPOST("sendto")?GETPOST("sendto"):$liste;
			$formmail->withtocc=$liste;
			$formmail->withtoccc=$conf->global->MAIN_EMAIL_USECCC;
			$formmail->withtopic=$outputlangs->trans('SendBillRef','__FACREF__');
			$formmail->withfile=2;
			$formmail->withbody=1;
			$formmail->withdeliveryreceipt=1;
			$formmail->withcancel=1;
			// Tableau des substitutions
			$formmail->substit['__FACREF__']=$object->ref;
			$formmail->substit['__SIGNATURE__']=$user->signature;
			$formmail->substit['__PERSONALIZED__']='';
			$formmail->substit['__CONTACTCIVNAME__']='';

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

			print '<br>';
		}
	}



// End of page
	llxFooter();
	$db->close();
