<?php
/* Copyright (C) 2004-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2009 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2009 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2012      Marcos García        <marcosgdf@gmail.com>
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
 *       \file       htdocs/fourn/commande/note.php
 *       \ingroup    commande
 *       \brief      Fiche note commande
 */

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/fourn.lib.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.commande.class.php';
require_once DOL_DOCUMENT_ROOT.'/advancepayment/class/paiementfournadvanceext.class.php';
if ($conf->purchase->enabled)
	require_once DOL_DOCUMENT_ROOT.'/purchase/lib/purchase.lib.php';

$langs->load('advancepayment@advancepayment');
$langs->load("banks");
$langs->load("bills");
$langs->load("categories");
$langs->load("companies");
$langs->load("margins");
$langs->load("salaries");
$langs->load("loan");
$langs->load("donations");
$langs->load("trips");
$langs->load("members");
$langs->load("compta");
$langs->load("orders");
$langs->load("suppliers");
$langs->load("companies");
$langs->load('stocks');


$id = GETPOST('facid','int')?GETPOST('facid','int'):GETPOST('id','int');
$ref = GETPOST('ref');
$action = GETPOST('action');
$confirm = GETPOST('confirm');
$cancel = GETPOST('cancel');
// Security check
if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user, 'fournisseur', $id, '', 'commande');

$object = new CommandeFournisseur($db);
$object->fetch($id, $ref);

$permissionnote=$user->rights->fournisseur->commande->creer;	// Used by the include of actions_setnotes.inc.php


/*
 * Actions
 */
if ($cancel == $langs->trans('Cancel'))
{
	$action = '';
}
if ($action == 'add_paiement' || ($action == 'confirm_paiement' && $confirm=='yes'))
{
	if ($action == 'confirm_paiement')
		$_POST = unserialize($_SESSION['aPost']);
	$error = 0;
	$datepaye = dol_mktime(12, 0, 0, GETPOST('remonth'), GETPOST('reday'), GETPOST('reyear'));
	$paiement_id = 0;
	$totalpayment = GETPOST('amount');
	$atleastonepaymentnotnull = 0;

		// Check parameters
	if ($_POST['paiementid'] <= 0)
	{
		setEventMessage($langs->transnoentities('ErrorFieldRequired',$langs->transnoentities('PaymentMode')), 'errors');
		$error++;
	}

	if (! empty($conf->banque->enabled))
	{
			// If bank module is on, account is required to enter a payment
		if (GETPOST('accountid') <= 0)
		{
			setEventMessage($langs->transnoentities('ErrorFieldRequired',$langs->transnoentities('Account')), 'errors');
			$error++;
		}
	}

	if (empty($totalpayment) && empty($atleastonepaymentnotnull))
	{
		setEventMessage($langs->transnoentities('ErrorFieldRequired',$langs->trans('PaymentAmount')), 'errors');
		$error++;
	}

	if (empty($datepaye))
	{
		setEventMessage($langs->transnoentities('ErrorFieldRequired',$langs->transnoentities('Date')), 'errors');
		$error++;
	}
	if ($error)
	{
		$action = 'create';
	}
}
if ($action == 'add_paiement')
{
	if ($error)
	{
		$action = 'create';
	}
		// Le reste propre a cette action s'affiche en bas de page.
}


if ($action == 'confirm_paiement' && $confirm == 'yes')
{
	$error=0;

	$datepaye = dol_mktime(12, 0, 0, GETPOST('remonth'), GETPOST('reday'), GETPOST('reyear'));

	if (! $error)
	{
		$now = dol_now();
			// Creation de la ligne paiement
		$paiement = new PaiementFournadvanceext($db);
		$numref = $paiement->getNextNumRef($soc,'next');
		$db->begin();
		$_POST = unserialize($_SESSION['aPost']);
		$datepaye = dol_mktime(12, 0, 0, GETPOST('remonth'), GETPOST('reday'), GETPOST('reyear'));
		$accountid = GETPOST('accountid');
		$amounts[$id] = GETPOST('amount');
		$paiement->entity = $conf->entity;
		$paiement->ref = $numref;
		$paiement->datep     = $datepaye;
		$paiement->datepaye     = $datepaye;
		$paiement->amount      = GETPOST('amount');
		$paiement->amounts = $amounts;
		$paiement->fk_paiement  = $_POST['paiementid'];
		$paiement->paiementid   = $_POST['paiementid'];
		$paiement->num_paiement = $_POST['num_paiement'];
		$paiement->note         = $_POST['comment'];
		$paiement->tms = $now;
		$paiement->datec = $now;
		$paiement->fk_user_author = $user->id;
		$paiement->fk_soc = GETPOST('socid');
		$paiement->origin = 'SupplierOrder';
		$paiement->originid = $id;
		$paiement->fk_bank = 0;
		$paiement->statut = 1;
		$paiement->multicurrency_amount = 0;

		if (! $error)
		{
			$paiement_id = $paiement->create($user,(GETPOST('closepaidinvoices')=='on'?1:0));
			if ($paiement_id < 0)
			{
				setEventMessage($paiement->error, 'errors');
				$error++;
			}
		}
		if (! $error)
		{
			$text = $langs->trans('SupplierAdvancePayment');
			$result=$paiement->addPaymentToBankadd($user,'payment_supplier_advance',$text,$accountid,'','');
			if ($result < 0)
			{
				setEventMessage($paiement->error, 'errors');
				$error++;
			}
		}
		//actualizamos
			// Define output language
		if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
		{
			$outputlangs = $langs;
			$newlang = '';
			if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
			if ($conf->global->MAIN_MULTILANGS && empty($newlang))  $newlang = $object->thirdparty->default_lang;
			if (! empty($newlang)) {
				$outputlangs = new Translate("", $conf);
				$outputlangs->setDefaultLang($newlang);
			}
			$model=$paiement->modelpdf;
			if (empty($model)) $model = 'paymentfournbo';
			$ret = $paiement->fetch($paiement_id);

			$result=$paiement->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);
			if ($result < 0) dol_print_error($db,$result);
		}
		if ($result <0 )
		{
			$error++;
			setEventMessages($langs->trans('Error al crear documento'),null,'errors');
		}
		if (! $error)
		{
			$db->commit();

			//$ret=$object->fetch_($object->id);    // Reload to get new records



			$loc = DOL_URL_ROOT.'/advancepayment/fourn/card.php?id='.$paiement_id;

			header('Location: '.$loc);
			exit;
		}
		else
		{
			$db->rollback();
		}
	}
}

if ($action == 'confirm_add_vincule' && $confirm == 'yes')
{
	require_once DOL_DOCUMENT_ROOT.'/advancepayment/class/paiementfournext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';
	$account = new Account($db);
	$accountline = new AccountLine($db);
	$objadv = new Paiementfournadvanceext($db);


	$error=0;
	$aPost = unserialize($_SESSION['aPost']);
	$_POST = $aPost[$id];
	$aFacture = GETPOST('aFacture');
	$db->begin();
	foreach ($aFacture AS $fk_paiement => $fk_facture)
	{
		if ($fk_facture > 0)
		{
			//recuperamos el pago
			$objadv->fetch($fk_paiement);
			$accountline->fetch($objadv->fk_bank);
			$now = dol_now();
			// Creation de la ligne paiement
			$paiement = new PaiementFournext($db);

			$accountid = GETPOST('accountid');
			$amounts[$fk_facture] = $objadv->amount;
			$paiement->entity = $conf->entity;
			$paiement->ref = $numref;
			$paiement->datep     = ($objadv->datep?$objadv->datep:$accountline->dateo);
			$paiement->datepaye     = ($objadv->datep?$objadv->datep:$accountline->dateo);
			$paiement->amount      = $objadv->amount;
			$paiement->amounts = $amounts;
			$paiement->fk_paiement  = $objadv->fk_paiement;
			$paiement->paiementid   = $objadv->fk_paiement;
			$paiement->num_paiement = $objadv->num_paiement;;
			$paiement->note         = $objadv->note;
			$paiement->tms = $now;
			$paiement->datec = $now;
			$paiement->fk_user_author = $user->id;
			$paiement->fk_soc = $objadv->fk_soc;
			$paiement->fk_bank = $objadv->fk_bank;
			$paiement->statut = 1;
			$paiement->multicurrency_amount = 0;
			
			if (! $error)
			{
				$paiement_id = $paiement->createadd($user,(GETPOST('closepaidinvoices')=='on'?1:0));
				if ($paiement_id < 0)
				{
					setEventMessage($paiement->error, 'errors');
					$error++;
				}
			}
			if (!$error)
			{
				$objadv->fk_facture = $fk_facture;
				$res = $objadv->update($user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($objadv->error,$objadv->errors,'errors');
				}
			}
		}
	}

	if (! $error)
	{
		$db->commit();

		$loc = $_SERVER['PHP_SELF'].'?id='.$id;
		header('Location: '.$loc);
		exit;
	}
	else
	{
		$db->rollback();
	}
	$action = '';
}

/*
 * View
 */
$help_url='EN:Module_Suppliers_Orders|FR:CommandeFournisseur|ES:Módulo_Pedidos_a_proveedores';
llxHeader('',$langs->trans("Order"),$help_url);

$form = new Form($db);

/* *************************************************************************** */
/*                                                                             */
/* Mode vue et edition                                                         */
/*                                                                             */
/* *************************************************************************** */

$now=dol_now();

if ($id > 0 || ! empty($ref))
{
	if ($result >= 0)
	{
		$soc = new Societe($db);
		$soc->fetch($object->socid);

		$author = new User($db);
		$author->fetch($object->user_author_id);

		if ($conf->purchase->enabled)
			$head = purchase_prepare_head($object);
		else
			$head = ordersupplier_prepare_head($object);

		$title=$langs->trans("SupplierOrder");
		dol_fiche_head($head, 'advancepayment', $title, 0, 'order');


		/*
		 *   Commande
		 */
		print '<table class="border" width="100%">';

		$linkback = '<a href="'.DOL_URL_ROOT.'/fourn/commande/list.php'.(! empty($socid)?'?socid='.$socid:'').'">'.$langs->trans("BackToList").'</a>';

		// Ref
		print '<tr><td class="titlefield">'.$langs->trans("Ref").'</td>';
		print '<td colspan="2">';
		print $form->showrefnav($object, 'ref', $linkback, 1, 'ref', 'ref');
		print '</td>';
		print '</tr>';

		// Fournisseur
		print '<tr><td>'.$langs->trans("Supplier")."</td>";
		print '<td colspan="2">'.$soc->getNomUrl(1,'supplier').'</td>';
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

		print "</table>";

		print '<br>';
		dol_fiche_end();

		$colwidth=20;
		include DOL_DOCUMENT_ROOT.'/advancepayment/fourn/tpl/listfourn.tpl.php';
	}
	else
	{
		/* Order not found */
		$langs->load("errors");
		print $langs->trans("ErrorRecordNotFound");
	}
}


llxFooter();

$db->close();
