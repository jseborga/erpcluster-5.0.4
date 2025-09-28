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
 *	\file       htdocs/contab/period/fiche.php
 *	\ingroup    Periodos contables
 *	\brief      Page fiche contab period
 */

require("../../main.inc.php");

require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT.'/accountancy/class/html.formventilation.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabspendingaccount.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabaccountingext.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/lib/contab.lib.php';

require_once(DOL_DOCUMENT_ROOT."/contab/class/accountingaccountext.class.php");
require_once(DOL_DOCUMENT_ROOT."/contab/class/accountingaccountadd.class.php");

// require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
// require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$langs->load("contab@contab");

$action=GETPOST('action');

$id        = GETPOST("rowid");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");

if (! $sortfield) $sortfield="p.period_month";
if (! $sortorder) $sortorder="DESC";

$mesg = '';

$object = new Contabspendingaccount($db);
$objAccounting = new Accountingaccountext($db);

/*
 * Actions
 */

// Add
if ($action == 'add' && $user->rights->contab->crearspending)
{
	$object->ref        = GETPOST('ref');
	$object->fk_account = GETPOST('fk_account');
	$object->entity     = $conf->entity;
	$object->state      = 0;
	if ($object->ref && $object->fk_account) {
		$id = $object->create($user);
		if ($id > 0)
		{
			setEventMessages($langs->trans('Saverecord'),null,'mesgs');
			header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
			exit;
		}
		else
			setEventMessages($object->error,$object->errors,'errors');
		$action = 'create';
		$mesg='<div class="error">'.$object->error.'</div>';
	}
	else {
		$mesg='<div class="error">'.$langs->trans("Errorrefrequired").'</div>';
		$action="create";
		// Force retour sur page creation
	}
}


/*
 * Confirmation de la re validation
 */
if ($action == 'revalidate')
{
	$object->fetch($_REQUEST["id"]);
	//cambiando a validar
	$object->statut = 0;
	//update
	$object->update($user);
	header("Location: fiche.php?id=".$_GET['id']);
}

// Delete period
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->contab->delspending)
{
	$object->fetch($_REQUEST["id"]);
	$result=$object->delete($user);
	if ($result > 0)
	{
		header("Location: ".DOL_URL_ROOT.'/contab/spending/liste.php');
		exit;
	}
	else
	{
		$mesg='<div class="error">'.$object->error.'</div>';
		$action='';
	}
}

// Modification entrepot
if ($action == 'update' && $_POST["cancel"] <> $langs->trans("Cancel"))
{
	if ($object->fetch($_POST["id"]))
	{
		$object->ref        = GETPOST('ref');
		$object->fk_account = GETPOST('fk_account');
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
$formventilation = new Formventilation($db);

$help_url='EN:Module_Contab_En|FR:Module_Contab|ES:M&oacute;dulo_Contab';
llxHeader("",$langs->trans("Managementaccounting"),$help_url);

if ($action == 'create' && $user->rights->contab->crearperiod)
{
	print_fiche_titre($langs->trans("Newspendingaccount"));

	print "<form action=\"fiche.php\" method=\"post\">\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

  // spending
	print '<tr><td width="20%" class="fieldrequired">'.$langs->trans('Spending').'</td><td colspan="2">';
	print $form->select_type_fees(GETPOST('ref','alpha'),'ref',1);
	print '</td></tr>';

  // account
	print '<tr><td class="fieldrequired">'.$langs->trans('Account').'</td><td colspan="2">';
	//print $objectaccount->select_account($object->fk_account,'fk_account','',0,1,2,2);
	print $formventilation->select_account(GETPOST('fk_account'),'fk_account',1);
	print '</td></tr>';

	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

	print '</form>';
}
else
{
	if ($_GET["id"])
	{
		dol_htmloutput_mesg($mesg);

		$result = $object->fetch($_GET["id"]);
		if ($result < 0)
		{
			dol_print_error($db);
		}


	  /*
	   * Affichage fiche
	   */
	  if ($action <> 'edit' && $action <> 're-edit')
	  {
	  //$head = fabrication_prepare_head($object);

	  	dol_fiche_head($head, 'card', $langs->trans("Spending accounts"), 0, 'contab');

	  /*
	   * Confirmation de la validation
	   */
	  if ($action == 'validate')
	  {
	  	$object->fetch(GETPOST('id'));
		  //cambiando a validado
	  	$object->state = 1;
		  //update
	  	$object->update($user);
	  	$action = '';
		  //header("Location: fiche.php?id=".$_GET['id']);

	  }

	  /*
	   * Close period
	   */
	  if ($action == 'close')
	  {
	  	$object->fetch(GETPOST('id'));
		  //cambiando a validado
	  	$object->state = 2;
		  //update
	  	$object->update($user);
	  	$action = '';
		  //header("Location: fiche.php?id=".$_GET['id']);

	  }

	  /*
	   * Open period
	   */
	  if ($action == 'open')
	  {
	  	$object->fetch(GETPOST('id'));
		  //cambiando a validado
	  	$object->statut = 1;
		  //update
	  	$object->update($user);
	  	$action = '';
		  //header("Location: fiche.php?id=".$_GET['id']);

	  }

	  // Confirm delete third party
	  if ($action == 'delete')
	  {
	  	$form = new Form($db);
	  	$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deleteperiodaccounting"),$langs->trans("Confirmdeleteperiodaccounting",$object->period_month.' '.$object->period_year),"confirm_delete",'',0,2);
	  	if ($ret == 'html') print '<br>';
	  }

	  print '<table class="border" width="100%">';

	  // spending
	  print '<tr><td width="20%">'.$langs->trans('Spending').'</td><td colspan="2">';
	  $form->load_cache_types_fees();
	  print $form->cache_types_fees[$object->ref];
	  print '</td></tr>';

	  // account
	  print '<tr><td>'.$langs->trans('Account').'</td><td colspan="2">';
	  $objectaccount->fetch($object->fk_account);
	  print $objectaccount->cta_name;
	  print '</td></tr>';

	  // Statut
	  print '<tr><td>'.$langs->trans("Status").'</td><td colspan="3">'.LibState($object->state,4).'</td></tr>';

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
	  	if ($user->rights->contab->crearspending && $object->state == 0)
	  		print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
	  	else
	  		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

	  	if (($object->state==0 ) && $user->rights->contab->delspending)
	  		print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
	  	else
	  		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
		  // Valid
	  	if ($object->state == 0 && $user->rights->contab->valspending)
	  	{
	  		print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=validate">'.$langs->trans('Validate').'</a>';
	  	}
		  // ReValid
	  	if ($object->state == 1 && $user->rights->contab->valspending)
	  	{
	  		print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=revalidate">'.$langs->trans('Return').'</a>';
	  	}
	  }

	  print "</div>";

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

	  // month
	  	print '<tr><td width="20%" class="fieldrequired">'.$langs->trans('Month').'</td><td colspan="2">';
	  	print '<input id="period_month" type="text" value="'.$object->period_month.'" name="period_month">';
	  	print '</td></tr>';
	  // year
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Year').'</td><td colspan="2">';
	  	print '<input id="period_year" type="text" value="'.$object->period_year.'" name="period_year">';
	  	print '</td></tr>';

	  //date ini
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Datestart').'</td><td colspan="2">';
	  	$form->select_date($object->date_ini,'date_ini','','','',"crea_commande",1,1);
	  	print '</td></tr>';

	  //date end
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Dateend').'</td><td colspan="2">';
	  	$form->select_date($object->date_fin,'date_fin','','','',"crea_commande",1,1);
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
