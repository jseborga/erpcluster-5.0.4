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
 *	\file       htdocs/salary/generic/fiche.php
 *	\ingroup    Generic table
 *	\brief      Page fiche salary generic table
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pgenerictableext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pgenericfieldext.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/lib/salary.lib.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$langs->load("salary@salary");

$action=GETPOST('action');

$id        = GETPOST("id");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");

$mesg = '';
$mesgerror = '';
$error = '';

$object  = new Pgenerictableext($db);
$objectf = new Pgenericfieldext($db);

/*
 * Actions
 */

// Add
if ($action == 'add' && $user->rights->salary->generic->creer)
{
	$object->table_cod  = $_POST["table_cod"];
	$object->entity     = $conf->entity;
	$object->table_name = GETPOST('table_name');
	$object->field_name = GETPOST('field_name');
	$object->sequen     = GETPOST('sequen');
	$object->limits     = GETPOST('limits');
	$object->type_value = GETPOST('type_value');
	$object->state      = 0;
	$object->ref = $object->table_cod.'|'.$object->sequen;

	if (empty($object->table_cod))
	{
		$error++;
		$mesgerror.= '<br>'.$langs->trans('Errortablecodrequired');
	}
	if (empty($object->table_name))
	{
		$error++;
		$mesgerror.= '<br>'.$langs->trans('Errortablenamerequired');
	}
	if (empty($object->field_name))
	{
		$error++;
		$mesgerror.= '<br>'.$langs->trans('Errorfieldnamerequired');
	}
	if ($object->limits <=0)
	{
		$error++;
		$mesgerror.= '<br>'.$langs->trans('Errorlimitrequired');
	}
	if ($object->type_value <=0)
	{
		$error++;
		$mesgerror.= '<br>'.$langs->trans('Errortypevaluerequired');
	}
	if (empty($object->sequen))
	{
		$error++;
		$mesgerror.= '<br>'.$langs->trans('Errorsequenrequired');
	}

	if ($object->table_cod && empty($error))
	{
		$id = $object->create($user);
		if ($id > 0)
		{
			header("Location: fiche.php?id=".$id);
			exit;
		}
		$action = 'create';
		$mesg='<div class="error">'.$object->error.'</div>';
	}
	else
	{
		if ($error)
			$mesg='<div class="error">'.$mesgerror.'</div>';
		else
			$mesg='<div class="error">'.$langs->trans("Errorrefnamerequired").'</div>';
	$action="create";   // Force retour sur page creation
}
}


// Delete concept
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->salary->generic->del)
{
	$object->fetch($id);
	$result=$object->delete($user);
	if ($result > 0)
	{
		header("Location: ".DOL_URL_ROOT.'/salary/generic/liste.php');
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
		$object->table_cod  = $_POST["table_cod"];
		$object->table_name = GETPOST('table_name');
		$object->field_name = GETPOST('field_name');
		$object->sequen     = GETPOST('sequen');
		$object->limits     = GETPOST('limits');
		$object->type_value = GETPOST('type_value');

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

$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Managementsalary"),$help_url);

if ($action == 'create' && $user->rights->salary->generic->creer)
{
	print_fiche_titre($langs->trans("Create generic table"));

	print "<form action=\"fiche.php\" method=\"post\">\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

	// table cod
	print '<tr><td class="fieldrequired">'.$langs->trans('Tablecod').'</td><td colspan="2">';
	print '<input id="table_cod" type="text" value="'.$object->table_cod.'" name="table_cod" size="3" maxlength="3">';
	print '</td></tr>';
	// table name
	print '<tr><td class="fieldrequired">'.$langs->trans('Tablename').'</td><td colspan="2">';
	print '<input id="table_name" type="text" value="'.$object->table_name.'" name="table_name" size="30" maxlength="40">';
	print '</td></tr>';

	// field name
	print '<tr><td class="fieldrequired">'.$langs->trans('Fieldname').'</td><td colspan="2">';
	print '<input id="field_name" type="text" value="'.$object->field_name.'" name="field_name" size="30" maxlength="20">';
	print '</td></tr>';

	//limits
	print '<tr><td class="fieldrequired">'.$langs->trans('Islimit').'</td><td colspan="2">';
	print select_yesno($object->limits,'limits','','',1);
	print '</td></tr>';

	//type_value
	print '<tr><td class="fieldrequired">'.$langs->trans('Typevalue').'</td><td colspan="2">';
	print select_typevalue($object->type_value,'type_value','','',1);
	print '</td></tr>';

	// sequen
	print '<tr><td class="fieldrequired">'.$langs->trans('Sequen').'</td><td colspan="2">';
	print '<input id="sequen" type="text" value="'.$object->sequen.'" name="sequen" size="2" maxlength="2">';
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

	  	dol_fiche_head($head, 'card', $langs->trans("Generictable"), 0, 'salary');

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

	  // Confirm delete third party
	  if ($action == 'delete')
	  {
	  	$form = new Form($db);
	  	$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deleteperiodaccounting"),$langs->trans("Confirmdeleteperiodaccounting",$object->period_month.' '.$object->period_year),"confirm_delete",'',0,2);
	  	if ($ret == 'html') print '<br>';
	  }

	  print '<table class="border" width="100%">';

	  // // table cod
	  // print '<tr><td>'.$langs->trans('Tablecod').'</td><td colspan="2">';
	  // print $object->table_cod;
	  // print '</td></tr>';

	  // ref
	  print '<tr><td width="20%">'.$langs->trans('Tablecod').'</td>';

	  $linkback = '<a href="'.DOL_URL_ROOT.'/salary/generic/liste.php">'.$langs->trans("BackToList").'</a>';

	  print '<td class="valeur"  colspan="2">';
	  print $form->showrefnav($object, 'id', $linkback);
	  print '</td></tr>';


	  // table name
	  print '<tr><td>'.$langs->trans('Tablename').'</td><td colspan="2">';
	  print $object->table_name;
	  print '</td></tr>';

	  // field name
	  print '<tr><td>'.$langs->trans('Fieldname').'</td><td colspan="2">';
	  print $object->field_name;
	  print '</td></tr>';

	  //limits
	  print '<tr><td>'.$langs->trans('Islimit').'</td><td colspan="2">';
	  print select_yesno($object->limits,'limits','','',1,1);
	  print '</td></tr>';

	  //type_value
	  print '<tr><td>'.$langs->trans('Typevalue').'</td><td colspan="2">';
	  print select_typevalue($object->type_value,'type_value','','',1,1);
	  print '</td></tr>';

	  // sequen
	  print '<tr><td>'.$langs->trans('Sequen').'</td><td colspan="2">';
	  print $object->sequen;
	  print '</td></tr>';

	  // state
	  print '<tr><td>'.$langs->trans('Status').'</td><td colspan="2">';
	  print libState($object->state,5);
	  print '</td></tr>';

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
	  	if ($user->rights->salary->generic->creer)
	  		print "<a class=\"butAction\" href=\"fiche.php?action=create\">".$langs->trans("Createnew")."</a>";
	  	else
	  		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";
	  	if ($user->rights->salary->generic->creer && $object->state == 0)
	  		print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
	  	else
	  		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";
	  	if ($user->rights->salary->generic->val && $object->state == 0)
	  		print "<a class=\"butAction\" href=\"fiche.php?action=validate&id=".$object->id."\">".$langs->trans("Validate")."</a>";

	  	if ($user->rights->salary->generic->del && $object->state == 0)
	  		print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
	  	else
	  		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
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


	  // table cod
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Tablecod').'</td><td colspan="2">';
	  	print '<input id="table_cod" type="text" value="'.$object->table_cod.'" name="table_cod" size="3" maxlength="3">';
	  	print '</td></tr>';
	  // table name
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Tablename').'</td><td colspan="2">';
	  	print '<input id="table_name" type="text" value="'.$object->table_name.'" name="table_name" size="30" maxlength="40">';
	  	print '</td></tr>';

	  // field name
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Fieldname').'</td><td colspan="2">';
	  	print '<input id="field_name" type="text" value="'.$object->field_name.'" name="field_name" size="30" maxlength="20">';
	  	print '</td></tr>';

	  //limits
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Islimit').'</td><td colspan="2">';
	  	print select_yesno($object->limits,'limits','','',1);
	  	print '</td></tr>';

	  //type_value
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Typevalue').'</td><td colspan="2">';
	  	print select_typevalue($object->type_value,'type_value','','',1);
	  	print '</td></tr>';

	  // sequen
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Sequen').'</td><td colspan="2">';
	  	print '<input id="sequen" type="text" value="'.$object->sequen.'" name="sequen" size="2" maxlength="2">';
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
