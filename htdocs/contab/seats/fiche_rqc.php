<?php
/* Copyright (C) 2013-2013 Ramiro Queso        <ramiro@ubuntu-bo.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
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
 *	\file       htdocs/contab/seats/fiche.php
 *	\ingroup    Asiento manual
 *	\brief      Page fiche contab seats
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabseatext.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabseatdetext.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabaccountingext.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/accountingaccountext.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/accountingaccountadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabtransaction.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/html.formaccount.class.php';

require_once DOL_DOCUMENT_ROOT.'/contab/class/contabperiodoext.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabseatflag.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';


// require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
// require_once(DOL_DOCUMENT_ROOT."/product/stock/class/entrepot.class.php");
//require_once(DOL_DOCUMENT_ROOT."/almacen/class/commonobject_.class.php");
//require_once(DOL_DOCUMENT_ROOT."/core/class/html.formcompany.class.php");
require_once(DOL_DOCUMENT_ROOT."/contab/lib/contab.lib.php");

$langs->load("contab");

if (!$user->rights->contab->seatma->read)
	accessforbidden();

$action=GETPOST('action');

$id        = GETPOST("id");
$aid        = GETPOST("aid");
$type_seat        = GETPOST("type_seat");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");

if (! $sortfield) $sortfield="p.period_month";
if (! $sortorder) $sortorder="DESC";

$mesg = '';

$object = new Contabseatext($db);
$objdet = new Contabseatdetext($db);
$objAccounting = new AccountingAccountext($db);
$objTransaction = new Contabtransaction($db);

//$objAccount    = new Contabaccountingext($db);
if ($id>0) $object->fetch($id);
$now = dol_now();
/*
 * Actions
 */
// Add
if ($action == 'add' && $user->rights->contab->seatma->write)
{

	$date_seat  = dol_mktime(12, 0, 0, GETPOST('date_seatmonth'),  GETPOST('date_seatday'),  GETPOST('date_seatyear'));
	//validamos el periodo
	$objPeriod = new Contabperiodoext($db);
	$return = $objPeriod->fetch_open(GETPOST('date_seatmonth'),GETPOST('date_seatyear'),$date_seat);
	if ($objPeriod->statut != 1)
	{
		$error++;
		$mesg='<div class="error">'.$langs->trans('Errorperiodclosenotvalidated').'</div>';
		$action = 'create';
	}
	else
	{
		$object->entity = $conf->entity;
		$object->date_seat = $date_seat;
		$object->lote   = "(PROV)";
		$object->sblote = "(PROV)";
		$object->doc    = "(PROV)";
		$object->ref    = "(PROV)";
		$object->currency  = GETPOST("currency");
		$object->type_seat = GETPOST("type_seat");
		$object->seat_month = GETPOST("date_seatmonth");
		$object->seat_year = GETPOST("date_seatyear");
		$object->seaty_year = GETPOST("date_seatyear");
		$object->sequential='0';
		$object->type_numeric = rand(1,100);
		$object->debit_total  = 0;
		$object->credit_total = 0;
		$object->history      = GETPOST("history");
		$object->manual       = 1;
		$object->fk_user_create = $user->id;
		$object->fk_user_mod = $user->id;
		$object->datec = $now;
		$object->datem = $now;
		$object->status        = 0;
		if ($object->date_seat && $object->currency)
		{
			$id = $object->create($user);

			if ($id > 0)
			{
				header("Location: fiche.php?id=".$id);
				exit;
			}
			else
			{
				setEventMessages($object->error,$object->errors,'errors');

			}
			$action = 'create';
			$mesg='<div class="error">'.$object->error.'</div>';
		}
		else
		{
			$mesg='<div class="error">'.$langs->trans("Errorperiodmonthyearrequired").'</div>';
			$action="create";
		}
	}
}

if ($action == 'confirm_validate')
{
	$object->fetch(GETPOST('id'));
	$ref = substr($object->lote, 1, 4);
	if ($ref == 'PROV')
	{
		list($numlote,$numsblote,$numdoc) = $object->getNextNumRef($object);
	}
	else
	{
		$numlote   = $object->lote;
		$numsblote = $object->sblote;
		$numdoc    = $object->doc;
	}

		  //cambiando a validado
	$object->status = 1;
	$object->lote = $numlote;
	$object->sblote = $numsblote;
	$object->doc = $numdoc;
	if (empty($object->ref)||is_null($object->ref) || substr($object->ref, 1, 4) == 'PROV') $object->ref = $object->lote.$object->sblote.$object->doc;
	if (empty($object->lote))
	{
		$error++;
		setEventMessages($langs->trans('No esta activo la numeracion de asientos, revise'),null,'errors');
		  //update
	}
	if (!$error)
	{
		$res = $object->update($user);
		if ($res <=0)
		{
			$error++;
			setEventMessages($object->error,$object->errors,'errors');
		}
	}
	$action = '';
		  //header("Location: fiche.php?id=".$_GET['id']);
}

// Adddet
if ($action == 'adddet' && $user->rights->contab->seatma->write)
{
	$error = '';
	$res = $object->fetch($id);
	$sequence = $objdet->fetch_sequence($id);

	$objdet->fk_contab_seat = GETPOST("id");
	$objdet->type_seat = GETPOST("type_seat");
	$objAccounting->fetch(GETPOST('debit_account'));
	if ($objAccounting->id == GETPOST('debit_account'))
		$objdet->debit_account  = $objAccounting->account_number;
	$objAccounting->fetch(GETPOST('credit_account'));
	if ($objAccounting->id == GETPOST('credit_account'))
		$objdet->credit_account = $objAccounting->account_number;
	$objdet->debit_detail = GETPOST("debit_detail");
	$objdet->credit_detail = GETPOST("debit_detail");
	$objdet->ddc = 0;
	$objdet->dcc = 0;
	$objdet->amount = GETPOST("amount");
	$objdet->amountdebit = GETPOST("amountdebit");
	$objdet->amountcredit = GETPOST("amountcredit");
	$objdet->history = GETPOST("history");
	$objdet->sequence = $sequence;
	$objdet->fk_standard_seat = 0;
	$objdet->routines = 'Manual';
	$objdet->value02 = GETPOST("amount")+0;
	$objdet->value03 = GETPOST("amount")+0;
	$objdet->value04 = GETPOST("amount")+0;
	$objdet->date_rate = $object->date_seat;
	$objdet->rate = 0;
	$objdet->fk_user_create = $user->id;
	$objdet->fk_user_mod = $user->id;
	$objdet->datec = $now;
	$objdet->datem = $now;
	$objdet->status        = 0;
	if (empty($objdet->debit_account) && empty($objdet->credit_account))
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Debitaccount")), null, 'errors');
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Creditaccount")), null, 'errors');
		$action="adddet";
	}

	if ($objdet->type_seat == 1)
	{
		//verificamos que el tipo de asiento debito se cumple
		if (!empty($objdet->credit_account))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Creditaccount")), null, 'errors');
			$action="adddet";
		}
		$objdet->amount = GETPOST('amountdebit');
	}
	if ($objdet->type_seat == 2)
	{
	//verificamos que el tipo de asiento debito se cumple
		if (!empty($objdet->debit_account))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Debitaccount")), null, 'errors');
			$action="adddet";
		}
		$objdet->amount = GETPOST('amountcredit');

	}
	if ($objdet->type_seat == 3)
	{
		//verificamos que el tipo de asiento debito se cumple
		if (empty($objdet->debit_account) || empty($objdet->credit_account))
		{
			$error++;
			setEventMessages($langs->trans("Erroraccountrequired"), null, 'errors');
			$action="adddet";
		}
		$objdet->amount = GETPOST('amountdebit');
		if (empty($amount))
			$objdet->amount = GETPOST('amountcredit');
	}
	if (!empty($objdet->type_seat) && !empty($objdet->amount) && empty($error))
	{
		$aid = $objdet->create($user);
		if ($aid > 0)
		{
			header("Location: fiche.php?id=".$id);
			exit;
		}
		$action = 'adddet';
		setEventMessages($objdet->error,$objdet->errors,'errors');
	}
	else
	{
		$error++;
		$action="adddet";
	}
}

// Updatedet

if ($action == 'updatedet' && $user->rights->contab->seatma->write)
{
	$error = '';
	$result = $objdet->fetch($aid);
	if ($result > 0)
	{
		$db->begin();
		if (isset($_GET['amount']) || isset($_POST['amount']))
			$amount = GETPOST('amount')+0;
		$objdet->type_seat = GETPOST("type_seat");
		$objdet->debit_account = GETPOST("debit_account");
		$objdet->credit_account = GETPOST("credit_account");
		$objdet->debit_detail = GETPOST("debit_detail");
		$objdet->credit_detail = GETPOST("credit_detail");
		$objdet->ddc = 0;
		$objdet->dcc = 0;
		$objdet->amount = $amountamount+0;
		$objdet->amountdebit  = GETPOST("amountdebit");
		$objdet->amountcredit = GETPOST("amountcredit");
		$objdet->history = GETPOST("history");
		$objdet->fk_standard_seat = 0;
		$objdet->routines = 'Manual';
		$objdet->value02 = $amount+0;
		$objdet->value03 = $amount+0;
		$objdet->value04 = $amount+0;

		$objdet->date_rate = $object->date_seat;
		$objdet->fk_user_mod = $user->id;
		$objdet->datem = $now;
		$object->status        = 0;

		if (empty($objdet->debit_account) && empty($objdet->credit_account))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Debitaccount")), null, 'errors');
			$action="editline";
			// Force retour sur page creation
		}

		if ($objdet->type_seat == 1)
		{
			//verificamos que el tipo de asiento debito se cumple
			if (!empty($objdet->credit_account))
			{
				$error++;
				setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Debitaccount")), null, 'errors');
				$action="editline";
				// Force retour sur page creation
			}
			$objdet->amount = GETPOST('amountdebit');
		}
		if ($objdet->type_seat == 2)
		{
			//verificamos que el tipo de asiento debito se cumple
			if (!empty($objdet->debit_account))
			{
				$error++;
				setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Creditaccount")), null, 'errors');
				$action="editline";
		// Force retour sur page creation
			}
			$objdet->amount = GETPOST('amountcredit');
		}
		if ($objdet->type_seat == 3)
		{
			//verificamos que el tipo de asiento debito se cumple
			if (empty($objdet->debit_account) || empty($objdet->credit_account))
			{
				$error++;
				setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Debitandcreditaccount")), null, 'errors');
				$action="editline";
			// Force retour sur page creation
			}
			$amount = GETPOST('amountdebit');
			if (empty($amount))
				$amount = GETPOST('amountcredit');
		}
		if (!$error)
		{
			$res = $objdet->update($user);
			if ($res <=0)
			{
				$error++;
				setEventMessages($objdet->error,$objdet->errors,'errors');
			}
		}
		else
		{
			$action = 'editline';
			//$action="create";
		}
		if (!$error)
		{
			$db->commit();
			$action = '';
			$aid = 0;
			$type_seat='';
		}
		else
			$db->rollback();
	}
}

// Delete account
if ($action == 'deleteline' && $user->rights->contab->seatma->del)
{
	$object->fetch($_REQUEST["id"]);
	$objdet->fetch($_REQUEST["aid"]);

	$result=$objdet->delete($user);
	if ($result > 0)
	{
		setEventMessages($langs->trans('Deleterecord'),null,'mesgs');
		header("Location: ".DOL_URL_ROOT.'/contab/seats/fiche.php?id='.$id);
		exit;
	}
}


// Delete seat
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->contab->seatma->del)
{
	$error = 0;
  //borrando los flags
	$objseatflag = new Contabseatflag($db);
	$aArray = $objseatflag->get_list($_REQUEST["id"]);
	if (is_array($aArray))
	{
		foreach ((array) $aArray AS $idflag)
		{
			$objseatflag->fetch($idflag);
			$resflag = $objseatflag->delete($user);
			if ($resflag < 0)
				$error++;
		}
	}
	else
		if ($aArray < 0)
			$error++;
		if (empty($error))
		{
			$object = new Contabseat($db);
			$object->fetch($_REQUEST["id"]);
			$result=$object->delete($user);
			if ($result > 0)
			{
				header("Location: ".DOL_URL_ROOT.'/contab/seats/liste.php');
				exit;
			}
			else
			{
				$mesg='<div class="error">'.$object->error.'</div>';
				$action='';
			}
		}
		else
		{
			$mesg='<div class="error">'.$langs->trans('Error check and confirm the deletion of the seat again').'</div>';
			$action='';
		}
	}

// Modification entrepot
	if ($action == 'update' && $_POST["cancel"] <> $langs->trans("Cancel"))
	{
		$date_ini  = dol_mktime(12, 0, 0, GETPOST('date_inimonth'),  GETPOST('date_iniday'),  GETPOST('date_iniyear'));
		$date_fin  = dol_mktime(12, 0, 0, GETPOST('date_finmonth'),  GETPOST('date_finday'),  GETPOST('date_finyear'));

		$object = new Contabperiodo($db);
		if ($object->fetch($_POST["id"]))
		{
			$object->period_month = $_POST["period_month"];
			$object->period_year  = $_POST["period_year"];
			$object->date_ini     = $date_ini;
			$object->date_fin     = $date_fin;
			if ( $object->update($_POST["id"], $user) > 0)
			{
				$action = '';
				$_GET["id"] = $_POST["id"];
		//$mesg = '<div class="ok">Fiche mise a jour</div>';
			}
			else
			{
				$action = 'edit';
				$_GET["id"] = $_POST["id"];
				$mesg = '<div class="error">'.$object->error.'</div>';
			}
		}
		else
		{
			$action = 'edit';
			$_GET["id"] = $_POST["id"];
			$mesg = '<div class="error">'.$object->error.'</div>';
		}
	}

	if ($_POST["cancel"] == $langs->trans("Cancel"))
	{
		$action = '';
		$_GET["id"] = $_POST["id"];
	}



/*
 * View
 */

$form=new Form($db);
$form_account = new Formaccount($db);
$title = $langs->trans('Managementseats');
$help_url='EN:Module_Contab_En|FR:Module_Contab|ES:M&oacute;dulo_Contab';
//llxHeader("",$langs->trans("Managementseats"),$help_url);
$morejs = array();
$morecss = array('/contab/css/style.css','/contab/css/bootstrap.min.css','/includes/jquery/plugins/datatables/media/css/dataTables.bootstrap.css','/includes/jquery/plugins/datatables/media/css/jquery.dataTables.css',);
llxHeader('',$title,$help_url,'','','',$morejs,$morecss,0,0);

if ($action == 'create' && $user->rights->contab->seatma->write)
{
	print_fiche_titre($langs->trans("Newseats"));

	print "<form action=\"fiche.php\" method=\"post\">\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

  // date seat
	print '<tr><td class="fieldrequired">'.$langs->trans('Dateseat').'</td><td colspan="2">';
	$form->select_date($object->date_seat,'date_seat','','','',"crea_seat",1,1);
	print '</td></tr>';
  //lote sblote doc
	print '<tr><td class="fieldrequired">'.$langs->trans('Number').'</td><td colspan="2">';
	print $object->lote.' '.$object->sblote.' '.$object->doc;
	print '</td></tr>';

  //currency
	print '<tr><td class="fieldrequired">'.$langs->trans('Currency').'</td><td colspan="2">';
	print select_currency($object->currency,'currency','','',1);
	print '</td></tr>';
  //type seat
	print '<tr><td class="fieldrequired">'.$langs->trans('Typeseat').'</td><td colspan="2">';
	print select_type_seat($object->type_seat,'type_seat','','',1);
	print '</td></tr>';
  //history
	print '<tr><td class="fieldrequired">'.$langs->trans('Glosa').'</td><td colspan="2">';
	print '<input id="history" type="text" value="'.$object->history.'" name="history" size="20" maxlength="40">';
	print '</td></tr>';

	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

	print '</form>';
}
else
{
	if ($id)
	{
		dol_htmloutput_mesg($mesg);

		$result = $object->fetch($_GET["id"]);
		if ($result < 0)
		{
			dol_print_error($db);
		}


		if ($action <> 'edit' && $action <> 're-edit')
		{
			//$head = fabrication_prepare_head($object);
			dol_fiche_head($head, 'card', $langs->trans("Seats"), 0, 'contab');

			if ($action == 'validate') {
				$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('Validateseat'), $langs->trans('ConfirmValidateseat'), 'confirm_validate', '', 0, 1);
				print $formconfirm;
			}


			if ($action == 'revalidate')
			{
				$object->fetch(GETPOST('id'));
				//cambiando a validado
				$object->status = 0;
				//update
				$object->update($user);
				//header("Location: fiche.php?id=".$_GET['id']);
			}

			// Confirm delete third party
			if ($action == 'delete')
			{
				$form = new Form($db);
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deleteseat"),$langs->trans("Confirmdeleteseat",$object->lote.' '.$object->sblote.' '.$object->doc),"confirm_delete",'',0,2);
				if ($ret == 'html') print '<br>';
			}


			print '<table class="border" width="100%">';

	  // date seat
			print '<tr><td width="20%">'.$langs->trans('Dateseat').'</td><td colspan="2">';
			print dol_print_date($object->date_seat,'daytext');
			print '</td></tr>';
	  //lote sblote doc
			print '<tr><td>'.$langs->trans('Number').'</td><td colspan="2">';
			print $object->lote.' '.$object->sblote.' '.$object->doc;
			print '</td></tr>';
	  //currency
			print '<tr><td>'.$langs->trans('Currency').'</td><td colspan="2">';
			print currency_name($object->currency,1);
			print ' ('.$object->currency;
			print ($object->currency != $langs->getCurrencySymbol($object->currency) ? ' - '.$langs->getCurrencySymbol($object->currency) : '');
			print ')';
			print '</td></tr>';
	  //type seat
			print '<tr><td>'.$langs->trans('Typeseat').'</td><td colspan="2">';
			print select_type_seat($object->type_seat,'type_seat','','',1,1);
			print '</td></tr>';
	  //type transaction
			print '<tr><td>'.$langs->trans('Typetransaction').'</td><td colspan="2">';
			$objTransaction->fetch('',$object->codtr);
			print $objTransaction->label.' '.$objTransaction->type;
			print '</td></tr>';
	  //history
			print '<tr><td>'.$langs->trans('Glosa').'</td><td colspan="2">';
			print $object->history;
			print '</td></tr>';
	  // Statut
	  //	  print '<tr><td>'.$langs->trans("Status").'</td><td colspan="3">'.$object->getLibStatut(4).'</td></tr>';

			print "</table>";

			print '</div>';


			/* ************************************************************************** */
			/*                                                                            */
			/* Barre d'action                                                             */
			/*                                                                            */
			/* ************************************************************************** */

			print "<div class=\"tabsAction\">\n";

			if ($action == '')
			{
				print '<a class="butAction" href="'.DOL_URL_ROOT.'/contab/seats/list.php'.'">'.$langs->trans("Return").'</a>';

				if ($user->rights->contab->seatma->write && $object->status == 0)
					print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

				if (($object->status==0 ) && $user->rights->contab->seatma->del)
					print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
		  // Valid
				if ($object->status == 0 && $user->rights->contab->seatma->val)
				{
					$resdouble = $objdet->double_entry($id);
					If ($resdouble > 0)
					{
						print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=validate">'.$langs->trans('Validate').'</a>';
					}
					else
					{
						print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Validate")."</a>";
					}
				}
		  // ReValid
				if ($object->status == 1 && $user->rights->contab->seatma->val)
				{
					print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=revalidate">'.$langs->trans('Donotvalidate').'</a>';
				}
		  // delete
				if ($object->status == 1 && $user->rights->contab->seatma->del)
				{
					print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a>';
				}

			}

			print "</div>";

	  //lista de cuentas
	  // contab_seat_det
					  //registro nuevo
			if ($object->status == 0)
			{
				dol_fiche_head();
				if (!empty($aid))
				{
					$objdet->fetch($aid);
					$type_seat = $objdet->type_seat;
				}
				if ($conf->use_javascript_ajax)
				{
					print "\n".'<script type="text/javascript" language="javascript">';
					print '$(document).ready(function () {
						$("#selecttype_seat").change(function() {
							document.formc.action.value="";
							document.formc.submit();
						});
					})';
					print '</script>'."\n";
				}

				print '<form class="form-horizontal" name="formc" role="form" method="post" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'">';
				print '<input type="hidden" name="id" value="'.$id.'">';
				print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
				if (!empty($aid))
				{
					print '<input type="hidden" name="aid" value="'.$objdet->id.'">';
					print '<input type="hidden" name="action" value="updatedet">';
				}
				else
				{
					print '<input type="hidden" name="action" value="adddet">';
				}

				print '<div class="form-group">';
				print '<label for="" class="col-lg-2 control-label">'.$langs->trans('Typeseat').'</label>';
				print '<div class="col-lg-10">';
				print select_seat((GETPOST('type_seat')?GETPOST('type_seat'):$objdet->type_seat),'type_seat','','',1);
				print '</div>';
				print "</div>\n";


			//$objaccounting = new Contabaccounting($db);
					// $listaccount = $objAccounting->array;
				if ($type_seat == 1 ||$type_seat == 3 )
				{
					print '<div class="form-group">';
					print '<label for="" class="col-lg-2 control-label">'.$langs->trans('Debitaccount').'</label>';
					print '<div class="col-lg-10">';
					$filter = " AND ta.cta_class = 'A'";
					print $form_account->select_account((GETPOST('debit_account')?GETPOST('debit_account'):$objdet->debit_account),'debit_account', 1, array(), 1, 1, '', '',$filter);
					print '</div>';
					print "</div>\n";
				}
				if ($type_seat == 2 ||$type_seat == 3 )
				{
					print '<div class="form-group">';
					print '<label for="" class="col-lg-2 control-label">'.$langs->trans('Creditaccount').'</label>';
					print '<div class="col-lg-10">';
					print $form_account->select_account((GETPOST('credit_account')?GETPOST('credit_account'):$objdet->credit_account),'credit_account', 1, array(), 1, 1, '', '',$filter);
					print '</div>';
					print "</div>\n";
				}
				if ($type_seat == 1 ||$type_seat == 3 )
				{
					print '<div class="form-group">';
					print '<label for="" class="col-lg-2 control-label">'.$langs->trans('Debitdetail').'</label>';
					print '<div class="col-lg-10">';
					print '<input type="text" class="form-control" id="debit_detail" name="debit_detail" value="'.$objdet->debit_detail.'" placeholder="'.$langs->trans('Debitdetail').'" >';
					print '</div>';
					print "</div>\n";
				}
				if ($type_seat == 2 ||$type_seat == 3 )
				{
					print '<div class="form-group">';
					print '<label for="" class="col-lg-2 control-label">'.$langs->trans('Creditdetail').'</label>';
					print '<div class="col-lg-10">';
					print '<input type="text" class="form-control" id="credit_detail" name="credit_detail" value="'.$objdet->credit_detail.'" placeholder="'.$langs->trans('Creditdetail').'" >';
					print '</div>';
					print "</div>\n";
				}
				if ($type_seat == 1 ||$type_seat == 3 )
				{
					print '<div class="form-group">';
					print '<label for="" class="col-lg-2 control-label">'.$langs->trans('Amountdebit').'</label>';
					print '<div class="col-lg-10">';
					print '<input type="number" class="form-control" step="any" min="0" id="amountdebit" name="amountdebit" value="'.$objdet->amount.'" placeholder="'.$langs->trans('Amountdebit').'" >';
					print '</div>';
					print "</div>\n";
				}
				if ($type_seat == 2 ||$type_seat == 3 )
				{
					print '<div class="form-group">';
					print '<label for="" class="col-lg-2 control-label">'.$langs->trans('Amountcredit').'</label>';
					print '<div class="col-lg-10">';
					print '<input type="number" class="form-control" step="any" min="0" id="amountcredit" name="amountcredit" value="'.$objdet->amount.'" placeholder="'.$langs->trans('Amountcredit').'" >';
					print '</div>';
					print "</div>\n";
				}
				print '<div class="form-group">';
				print '<label for="" class="col-lg-2 control-label">'.$langs->trans('History').'</label>';
				print '<div class="col-lg-10">';
				print '<input type="text" class="form-control" id="history" name="history" value="'.(GETPOST('history')?GETPOST('history'):$object->history).'" placeholder="'.$langs->trans('History').'" >';
				print '</div>';
				print "</div>\n";

				print '<center><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
				print '</center>';
				print '</form>';

				dol_fiche_end();
			}


			//print '<div>';

			dol_fiche_head($head, 'card', $langs->trans("Listeaccount"), 0, 'contab');

			if (! empty($conf->contab->enabled) && $user->rights->contab->seatma->write)
			{
				print '<table class="noborder" width="100%">';
				print '<tr class="liste_titre">';
				print '<td>'.$langs->trans('Ref').'</td>';
				print '<td align="center">'.$langs->trans('Typeseat').'</td>';
				print '<td>'.$langs->trans('Debitaccount').'</td>';
				print '<td>'.$langs->trans('Creditaccount').'</td>';
				print '<td>'.$langs->trans('Debitdetail').'</td>';
				print '<td>'.$langs->trans('Creditdetail').'</td>';
				print '<td>'.$langs->trans('Amountdebit').'</td>';
				print '<td>'.$langs->trans('Amountcredit').'</td>';
				print '<td>'.$langs->trans('History').'</td>';
				if ($object->status == 0)
					print '<td align="center">'.$langs->trans('Action').'</td>';
				print '</tr>';

				$filter = " AND t.fk_contab_seat = ".$id;
				$num = $objdet->fetchAll('ASC','rowid',0,0,array(1=>1),'AND',$filter);

				//define variables para suma total
				$sumDebit = 0;
				$sumCredit = 0;
				if ($num)
				{
					$lines = $objdet->lines;
					$var=true;
					$i = 0;
					foreach ($lines AS $i => $objc)
					{
						if (!empty($objc->debit_account))
							$objc->type_seat = 1;
						if (!empty($objc->credit_account))
							$objc->type_seat = 2;
						if (!empty($objc->debit_account) && !empty($objc->credit_account))
							$objc->type_seat = 3;


						$var=!$var;
						print "<tr ".$bc[$var].">";
						print '<td nowrap="nowrap">';
						if ($object->status==0)
						{
							print '<a href="fiche.php?id='.$object->id.'&aid='.$objc->id.'&action=editline">'.img_object($langs->trans("Showaccountseat"),"account")." ".$objc->id."</a></td>\n";
						}
						else
						{
							print $objc->id."</td>\n";
						}
						print '<td align="center">';
						print select_seat($objc->type_seat,'type_seat','','',1,1);
						print '</td>';


						if (!empty($objc->debit_account))
						{
							$objc->type_seat = 1;
							$objAccounting->fetch('',$objc->debit_account);
								//$objc->debit_detail = $objAccount->cta_name;
							print '<td>'.$objAccounting->getNomUrladd().'</td>';
						}
						else
						{
							print '<td>&nbsp;</td>';
						}
						if (!empty($objc->credit_account))
						{
							$objc->type_seat = 2;
							$objAccounting->fetch('',$objc->credit_account);
								//$objc->credit_detail = $objAccount->cta_name;

							print '<td>'.$objAccounting->getNomUrladd().'</td>';
						}
						else
						{
							print '<td>&nbsp;</td>';
						}
						if (!empty($objc->debit_account) && !empty($objc->credit_account))
							$objc->type_seat = 3;

						print "<td>".$objc->debit_detail."</td>\n";
						print "<td>".$objc->credit_detail."</td>\n";
						if ($objc->type_seat == 1)
							$objc->amountdebit = $objc->amount;
						if ($objc->type_seat == 2)
							$objc->amountcredit = $objc->amount;
						if ($objc->type_seat == 3)
						{
							$objc->amountcredit = $objc->amount;
							$objc->amountdebit = $objc->amount;
						}
						print '<td align="right">'.price($objc->amountdebit)."</td>\n";
						print '<td align="right">'.price($objc->amountcredit)."</td>\n";
			  //sumando totales
						$sumDebit  += $objc->amountdebit;
						$sumCredit += $objc->amountcredit;

						print "<td>".dol_trunc($objc->history,20)."</td>\n";
						print '<td align="center">';
						if ($object->status==0)
						{
							print '<a href="fiche.php?id='.$object->id.'&aid='.$objc->id.'&action=deleteline">'.img_picto($langs->trans("Delete"),"delete")."</a></td>\n";
						}
						print '</td>';

						print '</tr>';
						$i++;
					}
					print '<tr class="liste_total"><td class="liste_total" colspan="6">'.$langs->trans("Total").'</td>';
					print '<td class="liste_total" align="right">'.price($sumDebit).'</td>';
					print '<td class="liste_total" align="right">'.price($sumCredit).'</td>';
					print '<td class="liste_total" colspan="2">&nbsp;</td>';
					print '</tr>';

				}
				else
				{
				}
				print "</table>";

			}
		}
	  /*
	   * Edition fiche
	   */
	  if (($action == 'edit' || $action == 're-edit') && 1)
	  {
	  	print_fiche_titre($langs->trans("ApplicationsEdit"), $mesg);

	  	print '<form action="fiche.php" method="POST">';
	  	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	  	print '<input type="hidden" name="action" value="update">';
	  	print '<input type="hidden" name="id" value="'.$object->id.'">';

	  	print '<table class="border" width="100%">';

	  // date seat
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Dateseat').'</td><td colspan="2">';
	  	$form->select_date($object->date_seat,'date_seat','','','',"crea_seat",1,1);
	  	print '</td></tr>';
	  //currency
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Currency').'</td><td colspan="2">';
	  	print select_currency($object->currency,'currency','','',1);
	  	print '</td></tr>';
	  //type seat
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Typeseat').'</td><td colspan="2">';
	  	print select_type_seat($object->type_seat,'type_seat','','',1);
	  	print '</td></tr>';
	  //history
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Glosa').'</td><td colspan="2">';
	  	print '<input id="history" type="text" value="'.$object->history.'" name="history" size="20" maxlength="40">';
	  	print '</td></tr>';

	  	print '</table>';

	  	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
	  	print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';

	  	print '</form>';

	  }
	}
}


llxFooter();

$db->close();
?>
