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
 *	\file       htdocs/salary/charge/fiche.php
 *	\ingroup    Charges
 *	\brief      Page fiche salary charges
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/pcharge.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$langs->load("salary@salary");

$action=GETPOST('action');

$id        = GETPOST("rowid");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");

$mesg = '';

$object  = new Pcharge($db);

/*
 * Actions
 */

// Add
if ($action == 'add' && $user->rights->salary->charge->creer)
{
	$object->ref     = $_POST["ref"];
	$object->detail  = GETPOST('detail');
	$object->skills  = GETPOST('skills');
	$object->entity  = $conf->entity;

	if ($object->ref) 
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
		$mesg='<div class="error">'.$langs->trans("Errorrefnamerequired").'</div>';
	$action="create";   // Force retour sur page creation
}
}


// Delete charge
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->salary->charge->del)
{
	$object->fetch($_REQUEST["id"]);
	$result=$object->delete($user);
	if ($result > 0)
	{
		header("Location: ".DOL_URL_ROOT.'/salary/charge/liste.php');
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
		$object->ref          = $_POST["ref"];
		$object->detail  = GETPOST('detail');
		$object->skills  = GETPOST('skills');
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

if ($action == 'create' && $user->rights->salary->charge->creer)
{
	print_fiche_titre($langs->trans("Newcharge"));
	
	print "<form action=\"fiche.php\" method=\"post\">\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';
	
	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

	// ref
	print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
	print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="50" maxlength="50">';
	print '</td></tr>';
	
	//detail
	print '<tr><td>'.$langs->trans('Details').'</td><td colspan="2">';
	print '<textarea class="flat" name="detail" id="detail" cols="40" rows="'.ROWS_3.'">';
	print $object->detail;
	print '</textarea>';
	print '</td></tr>';
	
	//Skills
	print '<tr><td>'.$langs->trans('Skills').'</td><td colspan="2">';
	print '<textarea class="flat" name="skills" id="skills" cols="40" rows="'.ROWS_3.'">'; 
	print $object->skills;
	print '</textarea>';
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
	  	
	  	dol_fiche_head($head, 'card', $langs->trans("Charge"), 0, 'salary');
	  	
	  /*
	   * Confirmation de la validation
	   */
	  if ($action == 'validate')
	  {
	  	$object->fetch(GETPOST('id'));
		  //cambiando a validado
	  	$object->statut = 1;
	  	$object->ref = $object->codref;
		  //update
	  	$object->update($user);
	  	$action = '';
		  //header("Location: fiche.php?id=".$_GET['id']);
	  	
	  }
	  
	  // Confirm delete third party
	  if ($action == 'delete')
	  {
	  	$form = new Form($db);
	  	$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deletecharge"),$langs->trans("Confirmdeletecharge",$object->ref.' '.$object->detail),"confirm_delete",'',0,2);
	  	if ($ret == 'html') print '<br>';
	  }
	  
	  print '<table class="border" width="100%">';


	  // ref
	  print '<tr><td width="20%">'.$langs->trans('Ref').'</td>';
	  $linkback = '<a href="'.DOL_URL_ROOT.'/salary/charge/liste.php">'.$langs->trans("BackToList").'</a>';

	  print '<td class="valeur"  colspan="2">';
	  print $form->showrefnav($object, 'id', $linkback);
	  print '</td></tr>';

	  print '<tr><td>'.$langs->trans('Name').'</td><td colspan="2">';
	  print $object->codref;
	  print '</td></tr>';
	  
	  //detail
	  print '<tr><td>'.$langs->trans('Details').'</td><td colspan="2">';
	  print '<textarea class="flat" name="detail" id="detail" cols="40" rows="'.ROWS_3.'" disabled="disabled">';
	  print $object->detail;
	  print '</textarea>';
	  print '</td></tr>';
	  
	  //Skills
	  print '<tr><td>'.$langs->trans('Skills').'</td><td colspan="2">';
	  print '<textarea class="flat" name="skills" id="skills" cols="40" rows="'.ROWS_3.'" disabled="disabled">'; 
	  print $object->skills;
	  print '</textarea>';
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
	  	if ($user->rights->salary->charge->creer)
	  		print "<a class=\"butAction\" href=\"fiche.php?action=create\">".$langs->trans("Createnew")."</a>";
	  	else
	  		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";

	  	if ($user->rights->salary->charge->creer)
	  		print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
	  	else
	  		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";
	  	
	  	if ($user->rights->salary->charge->del)
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
	  	
	  // ref
	  	print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
	  	print '<input id="ref" type="text" value="'.$object->codref.'" name="ref" size="50" maxlength="50">';
	  	print '</td></tr>';
	  	
	  //detail
	  	print '<tr><td>'.$langs->trans('Details').'</td><td colspan="2">';
	  	print '<textarea class="flat" name="detail" id="detail" cols="40" rows="'.ROWS_3.'">';
	  	print $object->detail;
	  	print '</textarea>';
	  	print '</td></tr>';
	  	
	  //Skills
	  	print '<tr><td>'.$langs->trans('Skills').'</td><td colspan="2">';
	  	print '<textarea class="flat" name="skills" id="skills" cols="40" rows="'.ROWS_3.'">'; 
	  	print $object->skills;
	  	print '</textarea>';
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
