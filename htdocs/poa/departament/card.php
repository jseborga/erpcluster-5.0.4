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
 *	\file       htdocs/salary/departament/card.php
 *	\ingroup    Departaments
 *	\brief      Page card salary departament
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentext.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/lib/departament.lib.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$langs->load("orgman@orgman");

$action=GETPOST('action');

$id        = GETPOST("rowid");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");

$mesg = '';

if (!$user->rights->orgman->dpto->lire) accessforbidden();

$object = new Pdepartamentext($db);

/*
 * Actions
 */

// Add
if ($action == 'add' && $user->rights->orgman->dpto->write)
{

	$object->ref          = GETPOST('ref','alpha');
	$object->label        = GETPOST('label','alpha');
	$object->entity       = $conf->entity;
	$object->fk_father    = GETPOST("fk_father")+0;
	$object->fk_user_resp = GETPOST("fk_user_resp")+0;

	$object->fk_user_create = $user->id;
	$object->fk_user_mod = $user->id;
	$object->datec = dol_now();
	$object->datem = dol_now();
	$object->tms = dol_now();
	$object->active = 1;
	$object->status = 1;
	if ($object->ref) 
	{
		$id = $object->create($user);
		if ($id > 0)
		{
			header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
			exit;
		}
		$action = 'create';
		$mesg='<div class="error">'.$object->error.'</div>';
	}
	else
	{
		$mesg='<div class="error">'.$langs->trans("Errorrefnamerequired").'</div>';
		$action="create";   
	// Force retour sur page creation
	}
}


// Delete period
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->orgman->dpto->del)
{
	$object->fetch($_REQUEST["id"]);
	$result=$object->delete($user);
	if ($result > 0)
	{
		header("Location: ".DOL_URL_ROOT.'/salary/departament/liste.php');
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
		$object->label        = $_POST["label"];
		$object->fk_father    = $_POST["fk_father"];
		$object->fk_user_resp = $_POST["fk_user_resp"];
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

$form=new Formv($db);

$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
llxHeader("",$langs->trans("Almacen"),$help_url);

if ($action == 'create' && $user->rights->orgman->dpto->write)
{
	print_fiche_titre($langs->trans("Newdepartament"));

	print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

	// ref
	print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
	print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="27" maxlength="30">';
	print '</td></tr>';
	// label
	print '<tr><td class="fieldrequired">'.$langs->trans('Label').'</td><td colspan="2">';
	print '<input id="label" type="text" value="'.$object->label.'" name="label" maxlength="255">';
	print '</td></tr>';
	// father
	print '<tr><td class="fieldrequired">'.$langs->trans('Father').'</td><td colspan="2">';
	print $form->select_departament($object->fk_father,'fk_father','','',1);
	print '</td></tr>';

	//responsable
	print '<tr><td>'.$langs->trans('Responsible').'</td><td colspan="2">';
	print $form->select_users($object->fk_user_resp,'fk_user_resp',1,array(1),0);
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


	// Affichage fiche
		if ($action <> 'edit' && $action <> 're-edit')
		{
	  	$head = departament_prepare_head($object);

		dol_fiche_head($head, 'card', $langs->trans("Departament"), 0, 'orgman');

	// Confirmation de la validation
			if ($action == 'validate')
			{
				$object->fetch(GETPOST('id'));
		  //cambiando a validado
				$object->ref = $object->codref;
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


	  // ref
	  // print '<tr><td width="20%">'.$langs->trans('Ref').'</td><td colspan="2">';
	  // print $object->ref;
	  // print '</td></tr>';

			print '<tr><td width="20%">'.$langs->trans('Ref').'</td>';
			$linkback = '<a href="'.DOL_URL_ROOT.'/salary/departament/liste.php">'.$langs->trans("BackToList").'</a>';

			print '<td class="valeur"  colspan="2">';
			print $form->showrefnav($object, 'id', $linkback);
			print '</td></tr>';

	  //ref
			print '<tr><td width="20%">'.$langs->trans('Ref').'</td><td colspan="2">';
			print $object->ref;
			print '</td></tr>';
	  //label
			print '<tr><td width="20%">'.$langs->trans('Label').'</td><td colspan="2">';
			print $object->label;
			print '</td></tr>';

	  // father
			$objectF = new Pdepartamentext($db);
			$objectF->fetch($object->fk_father);
			print '<tr><td>'.$langs->trans('Father').'</td><td colspan="2">';
			if ($objectF->id == $object->fk_father)
				print $objectF->getNomUrl(1);
			else
				print '&nbsp;';
			print '</td></tr>';

	  //respon
			$objUser = new User($db);
			$objUser->fetch($object->fk_user_resp);
			print '<tr><td>'.$langs->trans('Responsible').'</td><td colspan="2">';
			If ($objUser->id == $object->fk_user_resp)
			print $objUser->name.' '.$objUser->firstname;
			else
				print '&nbsp;';
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
				if ($user->rights->orgman->dpto->write)
					print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=create">'.$langs->trans("New").'</a>';
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("New")."</a>";

				if ($user->rights->orgman->dpto->write)
					print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=edit&id='.$object->id.'">'.$langs->trans("Modify").'</a>';
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

				if ($user->rights->orgman->dpto->del)
					print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=delete&id='.$object->id.'">'.$langs->trans("Delete").'</a>';
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
			}	  
			print "</div>";		
		}


		// Edition fiche
		if (($action == 'edit' || $action == 're-edit') && 1)
		{
			print_fiche_titre($langs->trans("ApplicationsEdit"), $mesg);

			print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="update">';
			print '<input type="hidden" name="id" value="'.$object->id.'">';

			print '<table class="border" width="100%">';

	  // ref
			print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">';
			print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="27" maxlength="30">';
			print '</td></tr>';
	  // label
			print '<tr><td class="fieldrequired">'.$langs->trans('Label').'</td><td colspan="2">';
			print '<input id="ref" type="text" value="'.$object->label.'" name="label" maxlength="255">';
			print '</td></tr>';
	  // father
			print '<tr><td class="fieldrequired">'.$langs->trans('Father').'</td><td colspan="2">';
			print $form->select_departament($object->fk_father,'fk_father','','',1);
			print '</td></tr>';

	  //responsable
			print '<tr><td>'.$langs->trans('Responsible').'</td><td colspan="2">';
			print $form->select_users($object->fk_user_resp,'fk_user_resp',0,array(1),0);
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
