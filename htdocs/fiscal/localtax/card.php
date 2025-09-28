<?php
/* Copyright (C) 2011-2014      Juanjo Menent <jmenent@2byte.es>
 * Copyright (C) 2015			Marcos García <marcosgdf@gmail.com>
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
 *	    \file       htdocs/compta/localtax/card.php
 *      \ingroup    tax
 *		\brief      Page of second or third tax payments (like IRPF for spain, ...)
 */

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/localtax/class/localtax.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';

//require_once DOL_DOCUMENT_ROOT.'/fiscal/class/tvaadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/fiscal/class/localtaxdet.class.php';
require_once DOL_DOCUMENT_ROOT.'/fiscal/class/ctypetva.class.php';
require_once DOL_DOCUMENT_ROOT.'/fiscal/class/ctypetvadet.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/vat.lib.php';


$langs->load("compta");
$langs->load("banks");
$langs->load("bills");

$id=GETPOST("id",'int');
$action=GETPOST("action","alpha");
$refund=GETPOST("refund","int");
$localTaxType = GETPOST('localTaxType');

if (empty($refund)) $refund=0;

$lttype=GETPOST('localTaxType', 'int');

// Security check
$socid = isset($_GET["socid"])?$_GET["socid"]:'';
if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user, 'tax', '', '', 'charges');

$localtax = new Localtax($db);
$objectdet = new Localtaxdet($db);
$objTypetva = new Ctypetva($db);
$objTypetvadet = new Ctypetvadet($db);
// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('localtaxvatcard','globalcard'));


/**
 * Actions
 */

//add payment of localtax
if($_POST["cancel"] == $langs->trans("Cancel")){
	header("Location: reglement.php?localTaxType=".$lttype);
	exit;
}

if ($action == 'add' && $_POST["cancel"] <> $langs->trans("Cancel"))
{

	$db->begin();

	$datev=dol_mktime(12,0,0, $_POST["datevmonth"], $_POST["datevday"], $_POST["datevyear"]);
	$datep=dol_mktime(12,0,0, $_POST["datepmonth"], $_POST["datepday"], $_POST["datepyear"]);

	$localtax->accountid=GETPOST("accountid");
	$localtax->paymenttype=GETPOST("paiementtype");
	$localtax->datev=$datev;
	$localtax->datep=$datep;
	$amounts = GETPOST('amounts');

	$amount = price2num(GETPOST("amount"));
	if ($refund == 1) {
		$amount= -$amount;
	}
	$amount_ant = $amount;
	$lModif = false;
	if (count($amounts)>0 && empty($refund))
	{
		foreach($amounts AS $j => $value)
		{
			if ($value > 0)
			{
				$amountadd+= $value;
			}
		}

		$amount+= $amountadd;
		$lModif = true;
	}
	$localtax->amount=price2num($amount);
	$localtax->label=GETPOST("label");
	$localtax->ltt=$lttype;

	$ret=$localtax->addPayment($user);
	if ($ret <=0)
	{
		$error++;
		setEventMessages($localtax->error,$localtax->errors,'errors');
	}
		//agregamos si existe valores adicionales
	if (count($amounts)>0)
	{
		foreach($amounts AS $j => $value)
		{
			if ($value > 0)
			{
				$objectdet->fk_localtax = $ret;
				$objectdet->fk_typetvadet = $j;
				$objectdet->amount = $value;
				$objectdet->fk_user_create = $user->id;
				$objectdet->fk_user_mod = $user->id;
				$objectdet->datec = dol_now();
				$objectdet->datem = dol_now();
				$objectdet->tms = dol_now();
				$objectdet->status = 1;
				$res = $objectdet->create($user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($objectdet->error,$objectdet->errors,'errors');
				}
			}
		}
	}

	if (!$error)
	{
		if ($lModif)
		{
			$objecttmp = new Localtax($db);
			$res = $objecttmp->fetch($ret);
			if ($res ==1)
			{
				$objecttmp->amount = $amount_ant;
				$objecttmp->fk_user_modif = $user->id;
				$res = $objecttmp->update($user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($objecttmp->error,$objecttmp->errors,'errors');
				}
			}
			else
			{
				$error++;
				setEventMessages($objecttmp->error,$objecttmp->errors,'errors');
			}
		}
	}
	if (!$error)
	{
		$db->commit();
		header("Location: reglement.php?localTaxType=".$lttype);
		exit;
	}
	else
	{
		$db->rollback();
		setEventMessages($localtax->error, $localtax->errors, 'errors');
		$_GET["action"]="create";
	}
}

//delete payment of localtax
if ($action == 'delete')
{
	$result=$localtax->fetch($id);

	if ($localtax->rappro == 0)
	{
		$db->begin();

		$ret=$localtax->delete($user);
		if ($ret > 0)
		{
			if ($localtax->fk_bank)
			{
				$accountline=new AccountLine($db);
				$result=$accountline->fetch($localtax->fk_bank);
				if ($result > 0) $result=$accountline->delete($user);	// $result may be 0 if not found (when bank entry was deleted manually and fk_bank point to nothing)
			}

			if ($result >= 0)
			{
				$db->commit();
				header("Location: ".DOL_URL_ROOT.'/compta/localtax/reglement.php?localTaxType='.$localtax->ltt);
				exit;
			}
			else
			{
				$localtax->error=$accountline->error;
				$db->rollback();
				setEventMessages($localtax->error, $localtax->errors, 'errors');
			}
		}
		else
		{
			$db->rollback();
			setEventMessages($localtax->error, $localtax->errors, 'errors');
		}
	}
	else
	{
		$mesg='Error try do delete a line linked to a conciliated bank transaction';
		setEventMessages($mesg, null, 'errors');
	}
}


/*
*	View
*/

llxHeader();

$form = new Formv($db);

if ($id)
{
	$vatpayment = new Localtax($db);
	$result = $vatpayment->fetch($id);

	if ($result <= 0)
	{
		dol_print_error($db);
		exit;
	}
}


if ($action == 'create')
{
	$text = '';
	if ($localTaxType)
	{
		$objTypetva->fetch($localTaxType);
		$text = $objTypetva->code.' '.$objTypeTva->label;
		$textlabel = $langs->trans('Payment').' '.$text;
	}
	print load_fiche_titre($langs->transcountry($langs->trans('Newpayment').' '.$text,$mysoc->country_code));

	if ($conf->use_javascript_ajax)
	{
		print "\n".'<script type="text/javascript" language="javascript">';
		print '$(document).ready(function () {
			$("#localTaxType").change(function() {
				document.add.action.value="create";
				document.add.submit();
			});
		})';
		print '</script>'."\n";
	}

	print '<form name="add" action="'.$_SERVER["PHP_SELF"].'" name="formlocaltax" method="post">'."\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	//print '<input type="hidden" name="localTaxType" value="'.$lttype.'">';
	print '<input type="hidden" name="action" value="add">';

	dol_fiche_head();

	print '<table class="border" width="100%">';

	print "<tr>";
	print '<td class="fieldrequired">'.$langs->trans("Typetva").'</td><td>';
	print $form->load_type_tva('localTaxType', GETPOST('localTaxType'), 1,'rowid', false,0);
	print '</td></tr>';

	print "<tr>";
	print '<td class="fieldrequired">'.$langs->trans("DatePayment").'</td><td>';
	print $form->select_date($datep,"datep",'','','','add',1,1);
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("DateValue").'</td><td>';
	print $form->select_date($datev,"datev",'','','','add',1,1);
	print '</td></tr>';

	// Label
	print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input name="label" class="minwidth200" value="'.$textlabel.'"></td></tr>';

	// Amount
	print '<tr><td class="fieldrequired">'.$langs->trans("Amount").'</td><td><input name="amount" size="10" value="'.GETPOST("amount").'"></td></tr>';

	if (! empty($conf->banque->enabled))
	{
		print '<tr><td class="fieldrequired">'.$langs->trans("Account").'</td><td>';
		$form->select_comptes($_POST["accountid"],"accountid",0,"courant=1",1);  // Affiche liste des comptes courant
		print '</td></tr>';

		print '<tr><td class="fieldrequired">'.$langs->trans("PaymentMode").'</td><td>';
		$form->select_types_paiements(GETPOST("paiementtype"), "paiementtype");
		print "</td>\n";
		print "</tr>";

		// Number
		print '<tr><td>'.$langs->trans('Numero');
		print ' <em>('.$langs->trans("ChequeOrTransferNumber").')</em>';
		print '<td><input name="num_payment" type="text" value="'.GETPOST("num_payment").'"></td></tr>'."\n";
	}
	// Other attributes
	$parameters=array('colspan' => ' colspan="1"');
	$reshook=$hookmanager->executeHooks('formObjectOptions',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook

	print '</table>';

	$filter = " AND t.fk_type_tva = ".$localTaxType;
	$res = $objTypetvadet->fetchAll('ASC','ref',0,0,array('status'=>1),'AND',$filter);
	if ($res > 0)
	{
		$amounts = GETPOST('amounts');
		print '<table id="tabledet" class="border" width="100%">';
		print '<tr class="liste_titre">';
		print '<td>'.$langs->trans('Ref').'</td>';
		print '<td>'.$langs->trans('Label').'</td>';
		print '<td>'.$langs->trans('Amount').'</td>';
		print '</tr>';
		foreach ($objTypetvadet->lines AS $j => $line)
		{
			$var = !$var;
			print "<tr $bc[$var]>";
			print '<td>'.$line->ref.'</td>';
			print '<td>'.$line->label.'</td>';
			print '<td>';
			print '<input type="number" min="0" step="any" name="amounts['.$line->id.']" value="'.$amounts[$line->id].'">';
			print '</td>';
			print '</tr>';
		}
		print '</table>';
	}

	dol_fiche_end();

	print '<div class="center">';
	print '<input type="submit" class="button" value="'.$langs->trans("Save").'">';
	print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}


/* ************************************************************************** */
/*                                                                            */
/* Barre d'action                                                             */
/*                                                                            */
/* ************************************************************************** */

if ($id)
{
	$h = 0;
	$head[$h][0] = DOL_URL_ROOT.'/compta/localtax/card.php?id='.$vatpayment->id;
	$head[$h][1] = $langs->trans('Card');
	$head[$h][2] = 'card';
	$h++;

	dol_fiche_head($head, 'card', $langs->trans("Taxpayment"), 0, 'payment');


	print '<table class="border" width="100%">';

	print "<tr>";
	print '<td width="25%">'.$langs->trans("Ref").'</td><td colspan="3">';
	print $vatpayment->ref;
	print '</td></tr>';
	$res = $objTypetva->fetch($vatpayment->ltt);
	print "<tr>";
	print '<td>'.$langs->trans("Typevat").'</td><td colspan="3">';
	print $objTypetva->code.' '.$objTypetva->label;
	print '</td></tr>';

	print "<tr>";
	print '<td>'.$langs->trans("DatePayment").'</td><td colspan="3">';
	print dol_print_date($vatpayment->datep,'day');
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("DateValue").'</td><td colspan="3">';
	print dol_print_date($vatpayment->datev,'day');
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("Amount").'</td><td colspan="3">'.price($vatpayment->amount).'</td></tr>';

	if (! empty($conf->banque->enabled))
	{
		if ($vatpayment->fk_account > 0)
		{
			$bankline=new AccountLine($db);
			$bankline->fetch($vatpayment->fk_bank);

			print '<tr>';
			print '<td>'.$langs->trans('BankTransactionLine').'</td>';
			print '<td colspan="3">';
			print $bankline->getNomUrl(1,0,'showall');
			print '</td>';
			print '</tr>';
		}
	}

	// Other attributes
	$parameters=array('colspan' => ' colspan="3"');
	$reshook=$hookmanager->executeHooks('formObjectOptions',$parameters,$vatpayment,$action);    // Note that $action and $object may have been modified by hook

	print '</table>';
	//recuperamos si tiene registro det
	$filter = " AND t.fk_localtax = ".$id;
	$res = $objectdet->fetchAll('','',0,0,array(1=>1),'AND',$filter);
	$total = 0;
	if ($res > 0)
	{
		$lines = $objectdet->lines;
		print '<br>';
		print '<table id="tabledet" class="border" width="100%">';
		print '<tr class="liste_titre">';
		print '<td>'.$langs->trans('Ref').'</td>';
		print '<td>'.$langs->trans('Label').'</td>';
		print '<td align="right">'.$langs->trans('Amount').'</td>';
		print '</tr>';
		foreach ($lines AS $j => $line)
		{
			$objTypetvadet->fetch($line->fk_typetvadet);
			$var = !$var;
			print "<tr $bc[$var]>";
			print '<td>'.$objTypetvadet->ref.'</td>';
			print '<td>'.$objTypetvadet->label.'</td>';
			print '<td align="right">'.price($line->amount).'</td>';
			print '</tr>';
			$total += $line->amount;
		}
		print '<tr class="liste_total">';
		print '<td>'.'</td>';
		print '<td align="right">'.$langs->trans('Partial').'</td>';
		print '<td align="right">'.price($total).'</td>';
		print '</tr>';
		$totalGeneral = $vatpayment->amount + $total;
		print '<tr class="liste_total">';
		print '<td>'.$langs->trans('Totalgeneral').'</td>';
		print '<td>&nbsp;</td>';
		print '<td align="right">'.price($totalGeneral).'</td>';
		print '</tr>';

		print '</table>';

	}
	dol_fiche_end();


	/*
	* Boutons d'actions
	*/
	print "<div class=\"tabsAction\">\n";
	if ($vatpayment->rappro == 0)
		print '<a class="butActionDelete" href="card.php?id='.$vatpayment->id.'&action=delete">'.$langs->trans("Delete").'</a>';
	else
		print '<a class="butActionRefused" href="#" title="'.$langs->trans("LinkedToAConcialitedTransaction").'">'.$langs->trans("Delete").'</a>';
	print "</div>";
}

llxFooter();
$db->close();

