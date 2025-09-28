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
require_once DOL_DOCUMENT_ROOT.'/contab/class/html.formaccount.class.php';

require_once DOL_DOCUMENT_ROOT.'/contab/class/contabperiodoext.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabseatflag.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';

require_once DOL_DOCUMENT_ROOT.'/contab/class/contab.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabtransaction.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/accountingaccountaux.class.php';
require_once DOL_DOCUMENT_ROOT.'/fiscal/class/entity.class.php';


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

if (!isset($_SESSION['period_year']))
	$_SESSION['period_year'] = strftime("%Y",dol_now());
$period_year = $_SESSION['period_year'];

if (! $sortfield) $sortfield="p.period_month";
if (! $sortorder) $sortorder="DESC";

$mesg = '';

$object         = new Contabseatext($db);
$objdet         = new Contabseatdetext($db);
$objAccounting  = new AccountingAccountext($db);
$objContabTrans = new Contabtransaction($db);
$objAccountingaux = new Accountingaccountaux($db);

$objContab=new Contab($db);
$formfile = new FormFile($db);
$ObjEntity = new Entity($db);

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
	//$objAccounting->fetch(GETPOST('debit_account'));
	//if ($objAccounting->id == GETPOST('debit_account'))
	$objdet->debit_account  = GETPOST('debit_account');
	//$objAccounting->fetch(GETPOST('credit_account'));
	//if ($objAccounting->id == GETPOST('credit_account'))
	$objdet->credit_account = GETPOST('credit_account');
	$objdet->debit_detail = GETPOST("debit_detail");
	$objdet->credit_detail = GETPOST("debit_detail");
	$objdet->ref_ext_auxd = GETPOST("ref_ext_auxd");
	$objdet->ref_ext_auxc = GETPOST("ref_ext_auxc");
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
	print_r($_POST);
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
		$objdet->ref_ext_auxd = GETPOST("ref_ext_auxd");
		$objdet->ref_ext_auxc = GETPOST("ref_ext_auxc");
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

		$result = $object->fetch($id);
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
				$action = '';
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
			$aDetalle = array();
	  		// date seat
			print '<tr><td width="20%">'.$langs->trans('Dateseat').'</td><td colspan="2">';
			print dol_print_date($object->date_seat,'daytext');
			$aDetalle['fecha'] = $object->date_seat;
			print '</td></tr>';
	  		//ref
	  		if (!empty($object->ref))
	  		{
				print '<tr><td>'.$langs->trans('Ref').'</td><td colspan="2">';
				print $object->ref;
				$aDetalle['ref'] = $object->ref;
				print '</td></tr>';
			}
	  		//lote sblote doc
			print '<tr><td>'.$langs->trans('Number').'</td><td colspan="2">';
			print $object->lote.' '.$object->sblote.' '.$object->doc;
			$aDetalle['numero'] = $object->lote.' '.$object->sblote.' '.$object->doc;
			print '</td></tr>';
	  		//currency
			print '<tr><td>'.$langs->trans('Currency').'</td><td colspan="2">';
			print currency_name($object->currency,1);
			$aDetalle['divisa'] = currency_name($object->currency,1).' ('.$object->currency.($object->currency != $langs->getCurrencySymbol($object->currency) ? ' - '.$langs->getCurrencySymbol($object->currency) : ')'.')');
			print ' ('.$object->currency;
			print ($object->currency != $langs->getCurrencySymbol($object->currency) ? ' - '.$langs->getCurrencySymbol($object->currency) : '');
			print ')';
			print '</td></tr>';
	  		//type seat
			print '<tr><td>'.$langs->trans('Typeseat').'</td><td colspan="2">';
			print select_type_seat($object->type_seat,'type_seat','','',1,1);
			$aDetalle['tipo'] = select_type_seat($object->type_seat,'type_seat','','',1,1);
			print '</td></tr>';
	  		//type transaction
			print '<tr><td>'.$langs->trans('Typetransaction').'</td><td colspan="2">';
			$restr = $objContabTrans->fetch(0,$object->codtr);
			if ($restr)
				print $objContabTrans->getNomUrl().' - '.$objContabTrans->label;
			print '</td></tr>';


	 		 //history
			print '<tr><td>'.$langs->trans('Glosa').'</td><td colspan="2">';
			print $object->history;
			$aDetalle['glosa'] = $object->history;
			$aDetalle['codTransaccion'] = $object->codtr;
			$aDetalle['fechaComprobante'] = $object->date_seat;
			$aDetalle['respaldo'] = $object->document_backing;
			$aDetalle['beneficiario'] = $object->beneficiary;
			$aDetalle['validado'] = $object->ref;
			$entity = $conf->entity;
			$res = $objContabTrans->fetchAll("","",0,0,array(1=>1),"AND","AND t.ref = ".$object->codtr." AND t.entity = ".$entity,true);
			if($res > 0){
				$aDetalle['codTransaccionResp'] = $objContabTrans->label ." (".$objContabTrans->type.")";
			}else{
				$aDetalle['codTransaccionResp'] = "";
			}




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
						$("#debit_account").change(function() {
							document.formc.action.value="";
							document.formc.submit();
						});
						$("#credit_account").change(function() {
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

					print '<div class="form-group">';
					print '<label for="" class="col-lg-2 control-label">'.$langs->trans('Debitaccountaux').'</label>';
					print '<div class="col-lg-10">';
					$filter = " AND aa.account_number = '".(GETPOST('debit_account')?GETPOST('debit_account'):$objdet->debit_account)."'";
					print $form_account->select_accountaux((GETPOST('ref_ext_auxd')?GETPOST('ref_ext_auxd'):$objdet->ref_ext_auxd),'ref_ext_auxd', 1, array(), 1, 1, '', '',$filter);
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

					print '<div class="form-group">';
					print '<label for="" class="col-lg-2 control-label">'.$langs->trans('Creditaccountaux').'</label>';
					print '<div class="col-lg-10">';
					$filter = " AND aa.account_number = '".(GETPOST('credit_account')?GETPOST('credit_account'):$objdet->credit_account)."'";
					print $form_account->select_accountaux((GETPOST('ref_ext_auxc')?GETPOST('ref_ext_auxc'):$objdet->ref_ext_auxc),'ref_ext_auxc', 1, array(), 1, 1, '', '',$filter);
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

				$aLineas = array();
				print '<table class="noborder" width="100%">';
				print '<tr class="liste_titre">';
				print '<td>'.$langs->trans('Ref').'</td>';
				print '<td align="center">'.$langs->trans('Typeseat').'</td>';
				print '<td>'.$langs->trans('Debitaccount').'</td>';
				print '<td>'.$langs->trans('Creditaccount').'</td>';
				//print '<td>'.$langs->trans('Label').'</td>';
				print '<td align="right">'.$langs->trans('Partial').'</td>';
				print '<td align="right">'.$langs->trans('Amountdebit').'</td>';
				print '<td align="right">'.$langs->trans('Amountcredit').'</td>';
				//print '<td>'.$langs->trans('History').'</td>';
				if ($object->status == 0)
					print '<td align="center">'.$langs->trans('Action').'</td>';
				print '</tr>';

				$filter = " AND t.fk_contab_seat = ".$id;
				$num = $objdet->fetchAll('DESC','debit_account,credit_account',0,0,array(1=>1),'AND',$filter);
				$lines = $objdet->lines;
				$aAccountgroup = array();
				$aAccountdet = array();
				//vamos a agrupar las cuentas
				if ($num >0)
				{
					$i = 0;
					foreach ($lines AS $i => $objc)
					{
						if (!empty($objc->debit_account))
							$objc->type_seat = 1;
						if (!empty($objc->credit_account))
							$objc->type_seat = 2;
						if (!empty($objc->debit_account) && !empty($objc->credit_account))
							$objc->type_seat = 3;
						if ($objc->type_seat == 1)
						{
							$aAccountgroup[$objc->type_seat][$objc->debit_account]['d']+=$objc->amount;
							if ($objc->ref_ext_auxd && !is_null($objc->ref_ext_auxd) && $objc->ref_ext_auxd!='-1')
								$aAccountdet[$objc->type_seat][$objc->debit_account][$objc->id] = $objc;
						}
						if ($objc->type_seat == 2)
						{
							$aAccountgroup[$objc->type_seat][$objc->credit_account]['c']+=$objc->amount;
							if ($objc->ref_ext_auxc&& !is_null($objc->ref_ext_auxc) && $objc->ref_ext_auxc!='-1')
								$aAccountdet[$objc->type_seat][$objc->credit_account][$objc->id] = $objc;
						}
						if ($objc->type_seat == 3)
						{
							$aAccountgroup[$objc->type_seat][$objc->debit_account]['d']+=$objc->amount;
							$aAccountgroup[$objc->type_seat][$objc->credit_account]['c']+=$objc->amount;
							if ($objc->ref_ext_auxd && !is_null($objc->ref_ext_auxd) && $objc->ref_ext_auxd!='-1')
								$aAccountdet[$objc->type_seat][$objc->debit_account][$objc->id] = $objc;
							if ($objc->ref_ext_auxc && !is_null($objc->ref_ext_auxc) && $objc->ref_ext_auxc!='-1')
							$aAccountdet[$objc->type_seat][$objc->credit_account][$objc->id] = $objc;
						}
					}
				}
				//define variables para suma total
				$sumDebit = 0;
				$sumCredit = 0;
				$aLineas = array();
				$i=0;
				foreach ($aAccountgroup AS $type_seat => $aData)
				{
						//buscamos la cuenta
					foreach ($aData AS $account => $aRow)
					{
						//armamos las sub cuentas si corresponde
						$aAux = $aAccountdet[$type_seat][$account];
						$nAux = count($aAux);
						$var=!$var;
						print "<tr ".$bc[$var].">";
						print '<td nowrap="nowrap">';
						if ($object->status==0 && empty($nAux))
						{
							print '<a href="fiche.php?id='.$object->id.'&aid='.$objc->id.'&action=editline">'.img_object($langs->trans("Showaccountseat"),"account")." ".$objc->id."</a></td>\n";
						}
						else
						{
							print $objc->id."</td>\n";
						}
						print '<td align="center">';
						print select_seat($type_seat,'type_seat','','',1,1);
						$aLineas [$i]['tipo'] = select_seat($type_seat,'type_seat','','',1,1);
						print '</td>';
						$aLineas [$i]['tipo'] = select_seat($type_seat,'type_seat','','',1,1);
						if ($type_seat == 1)
						{
							$resaux = $objAccounting->fetch('',$account,1);
							print '<td>'.$objAccounting->getNomUrladd().' '.$objAccounting->label.'</td>';
							$aLineas [$i]['cuentaDebito'] = $objAccounting->account_number;
							$aLineas [$i]['labelDebito'] = $objAccounting->label;
							$aLineas [$i]['cuenta'] = $objAccounting->account_number;
							$aLineas [$i]['label'] = $objAccounting->label;


							print '<td>'.'</td>';
						}
						if ($type_seat == 2)
						{
							print '<td>'.'</td>';
							$resaux=$objAccounting->fetch('',$account,1);
							$aLineas [$i]['cuentaCredito'] = $objAccounting->account_number;
							$aLineas [$i]['labelCredito'] = $objAccounting->label;
							$aLineas [$i]['cuenta'] = $objAccounting->account_number;
							$aLineas [$i]['label'] = $objAccounting->label;
							print '<td>'.$objAccounting->getNomUrladd().' '.$objAccounting->label.'</td>';
						}
						if ($type_seat == 3)
						{
							if ($aRow['d']>0)
							{
								$resaux = $objAccounting->fetch('',$account,1);
								print '<td>'.$objAccounting->getNomUrladd().' '.$objAccounting->label.'</td>';
								$aLineas [$i]['cuentaDebito'] = $objAccounting->account_number;
								$aLineas [$i]['labelDebito'] = $objAccounting->label;
							}
							if ($aRow['c']>0)
							{
								$resaux=$objAccounting->fetch('',$account,1);
								$aLineas [$i]['cuentaCredito'] = $objAccounting->account_number;
								$aLineas [$i]['labelCredito'] = $objAccounting->label;
								print '<td>'.$objAccounting->getNomUrladd().' '.$objAccounting->label.'</td>';
							}
						}

						//print "<td>".$objc->debit_detail."</td>\n";
						$aLineas [$i]['detalleDebito'] = $objc->debit_detail;
						//print "<td>".$objc->credit_detail."</td>\n";
						$aLineas [$i]['detalleCredito'] = $objc->credit_detail;
						print '<td></td>';
						if ($type_seat == 1)
						{
							print '<td align="right">'.price($aRow['d'])."</td>\n";
							print '<td></td>';
							$aLineas [$i]['montoDebito'] = $aRow['d'];
							$sumDebit+= $aRow['d'];
						}
						if ($type_seat == 2)
						{
							print '<td></td>';
							print '<td align="right">'.price($aRow['c'])."</td>\n";
							$aLineas [$i]['montoCredito'] = $aRow['c'];
							$sumCredit+= $aRow['c'];
						}
						if ($type_seat == 3)
						{
							print '<td align="right">'.price($aRow['d'])."</td>\n";
							print '<td align="right">'.price($aRow['c'])."</td>\n";
							$aLineas [$i]['montoDebito'] = $aRow['d'];
							$aLineas [$i]['montoCredito'] = $aRow['c'];
							$sumDebit  += $aRow['d'];
							$sumCredit+= $aRow['c'];

						}

						//print "<td>".dol_trunc($objc->history,20)."</td>\n";
						$aLineas [$i]['historia'] = $objc->history;
						print '</tr>';
						$i++;
						foreach ((array) $aAux AS $j => $objc)
						{
								//inicio aux
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
							$aLineas [$i]['ref'] = $objc->id;
							print '<td align="center">';
							print select_seat($objc->type_seat,'type_seat','','',1,1);
							$aLineas [$i]['tipo'] = select_seat($objc->type_seat,'type_seat','','',1,1);
							print '</td>';


							if ($objc->type_seat == 1)
							{
								$resaux = $objAccountingaux->fetch('',$objc->ref_ext_auxd,1);
								print '<td>'.$objAccountingaux->getNomUrl().' '.$objAccountingaux->label.'</td>';
								$aLineas [$i]['cuentaDebito'] = $objc->ref_ext_auxd;
								$aLineas [$i]['labelDebito'] = $objAccountingaux->label;
								$aLineas [$i]['cuenta'] = $objc->ref_ext_auxd;
								$aLineas [$i]['label'] = $objAccountingaux->label;
								print '<td>'.'</td>';
							}
							if ($objc->type_seat == 2)
							{
								print '<td>'.'</td>';
								$resaux = $objAccountingaux->fetch('',$objc->ref_ext_auxc,1);
								$aLineas [$i]['cuentaCredito'] = $objc->ref_ext_auxc;
								$aLineas [$i]['labelCredito'] = $objAccountingaux->label;
								$aLineas [$i]['cuenta'] = $objc->ref_ext_auxc;
								$aLineas [$i]['label'] = $objAccountingaux->label;
								print '<td>'.$objAccountingaux->getNomUrl().' '.$objAccountingaux->label.'</td>';
							}
							if ($objc->type_seat == 3)
							{
								$resaux = $objAccountingaux->fetch('',$objc->ref_ext_auxd,1);
								print '<td>'.$objAccountingaux->getNomUrl().' '.$objAccountingaux->label.'</td>';
								$aLineas [$i]['cuentaDebito'] = $objc->ref_ext_auxd;
								$aLineas [$i]['labelDebito'] = $objAccountingaux->label;

								$resaux = $objAccountingaux->fetch('',$objc->ref_ext_auxc,1);
								$aLineas [$i]['cuentaCredito'] = $objc->ref_ext_auxc;
								$aLineas [$i]['labelCredito'] = $objAccountingaux->label;
								print '<td>'.$objAccountingaux->getNomUrl().' '.$objAccountingaux->label.'</td>';
							}

							//print "<td>".$objc->debit_detail."</td>\n";
							$aLineas [$i]['detalleDebito'] = $objc->debit_detail;
							//print "<td>".$objc->credit_detail."</td>\n";
							$aLineas [$i]['detalleCredito'] = $objc->credit_detail;

							if ($objc->type_seat == 1){
								$objc->amountdebit = $objc->amount;
							//$aLineas [$i]['montoDebito'] = $objc->amountdebit;
							}

							if ($objc->type_seat == 2){
								$objc->amountcredit = $objc->amount;
							//$aLineas [$i]['montoCredito'] = $objc->amountcredit;
							}

							if ($objc->type_seat == 3)
							{
								$objc->amountcredit = $objc->amount;
								$objc->amountdebit = $objc->amount;
							}

							print '<td align="right">'.price($objc->amount)."</td>\n";
							$aLineas [$i]['partial'] = $objc->amount;
							print '<td></td>';
							print '<td></td>';

							//print "<td>".dol_trunc($objc->history,20)."</td>\n";
							$aLineas [$i]['historia'] = $objc->history;
							print '<td align="center">';
							if ($object->status==0)
							{
								print '<a href="fiche.php?id='.$object->id.'&aid='.$objc->id.'&action=deleteline">'.img_picto($langs->trans("Delete"),"delete")."</a></td>\n";
							}
							print '</td>';
							print '</tr>';
							$i++;
							//fin aux
						}
					}
				}
				//mostramos totales
				print '<tr class="titre_total">';
				print '<td colspan="5">'.$langs->trans('Total').'</td>';
				print '<td align="right">'.price(price2num($sumDebit,'MT')).'</td>';
				print '<td align="right">'.price(price2num($sumCredit,'MT')).'</td>';
				print '</tr>';

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

	$entity = $conf->entity;
	if($ObjEntity->fetch($entity) > 0){
		$labelEntity = $ObjEntity->label;
	}else{
		$labelEntity = "";
	}
	$aDetalle['entidad'] = $labelEntity;

	$aReporte = array(1=>$aDetalle,2=>$aLineas,3=>$sumDebit,4=>$sumCredit);
	$_SESSION['aReporte'] = serialize($aReporte);

	print '<div class="tabsAction">'."\n";
//print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=excel">'.$langs->trans("Hoja Electronica").'</a>';
	print '</div>'."\n";
	print '<table width="100%"><tr><td width="50%" valign="top">';
	print '<a name="builddoc"></a>';

	/*Aqui estaba el reporte*/
	$filename='contab/'.$period_year.'/seat/'.$object->id;
	$filedir=$conf->contab->dir_output.'/contab/'.$period_year.'/seat/'.$object->id;

	$modelpdf = "seat";

	$outputlangs = $langs;
	$newlang = '';
	if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
	if ($conf->global->MAIN_MULTILANGS && empty($newlang))  $newlang = $object->thirdparty->default_lang;
	if (! empty($newlang)) {
		$outputlangs = new Translate("", $conf);
		$outputlangs->setDefaultLang($newlang);
	}
	$objContab->id = $object->id;
	$objContab->status = $object->status;
	$objContab->ref = $object->ref;
	//$result=$object->generateDocument($modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
	$result=$objContab->generateDocument($modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
	if ($result < 0) dol_print_error($db,$result);

	$urlsource=$_SERVER['PHP_SELF'];
//$genallowed=$user->rights->assistance->lic->hiddemdoc;
//$delallowed=$user->rights->assistance->lic->deldoc;
	$genallowed = 0;
	$delallowed = 0;
	print $formfile->showdocuments('contab',$filename,$filedir,$urlsource,$genallowed,$delallowed,$modelpdf,1,0,0,28,0,'','','',$soc->default_lang);

	$somethingshown=$formfile->numoffiles;

	print '</td></tr></table>';

	llxFooter();

	$db->close();
	?>
