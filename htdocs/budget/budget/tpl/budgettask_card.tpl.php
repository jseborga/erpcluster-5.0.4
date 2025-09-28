<?php
/* Copyright (C) 2007-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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
 *   	\file       budget/budgettask_card.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-04-20 16:28
 */

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test
//if (! defined('NOSTYLECHECK'))   define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');				// If this page is public (can be called outside logged session)

$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_fk_budget=GETPOST('search_fk_budget','int');
$search_fk_task=GETPOST('search_fk_task','int');
$search_fk_task_parent=GETPOST('search_fk_task_parent','int');
$search_label=GETPOST('search_label','alpha');
$search_description=GETPOST('search_description','alpha');
$search_duration_effective=GETPOST('search_duration_effective','alpha');
$search_planned_workload=GETPOST('search_planned_workload','alpha');
$search_progress=GETPOST('search_progress','int');
$search_priority=GETPOST('search_priority','int');
$search_fk_user_creat=GETPOST('search_fk_user_creat','int');
$search_fk_user_valid=GETPOST('search_fk_user_valid','int');
$search_fk_statut=GETPOST('search_fk_statut','int');
$search_note_private=GETPOST('search_note_private','alpha');
$search_note_public=GETPOST('search_note_public','alpha');
$search_rang=GETPOST('search_rang','int');
$search_model_pdf=GETPOST('search_model_pdf','alpha');



if (empty($action) && empty($id) && empty($ref)) $action='view';



// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($objectdet->table_element);


// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('budgettask'));



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	if ($cancel)
	{
		if ($action != 'addlink')
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}
		if ($id > 0 || ! empty($ref)) $ret = $objectdet->fetch($id,$ref);
		$action='';
	}

	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */

		$objectdet->entity=GETPOST('entity','int');
		$objectdet->ref=GETPOST('ref','alpha');
		$objectdet->fk_budget=GETPOST('fk_budget','int');
		$objectdet->fk_task=GETPOST('fk_task','int');
		$objectdet->fk_task_parent=GETPOST('fk_task_parent','int');
		$objectdet->label=GETPOST('label','alpha');
		$objectdet->description=GETPOST('description','alpha');
		$objectdet->duration_effective=GETPOST('duration_effective','alpha');
		$objectdet->planned_workload=GETPOST('planned_workload','alpha');
		$objectdet->progress=GETPOST('progress','int');
		$objectdet->priority=GETPOST('priority','int');
		$objectdet->fk_user_creat=GETPOST('fk_user_creat','int');
		$objectdet->fk_user_valid=GETPOST('fk_user_valid','int');
		$objectdet->fk_statut=GETPOST('fk_statut','int');
		$objectdet->note_private=GETPOST('note_private','alpha');
		$objectdet->note_public=GETPOST('note_public','alpha');
		$objectdet->rang=GETPOST('rang','int');
		$objectdet->model_pdf=GETPOST('model_pdf','alpha');



		if (empty($objectdet->ref))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objectdet->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/list.php',1);
				header("Location: ".$urltogo);
				exit;
			}
			{
				// Creation KO
				if (! empty($objectdet->errors)) setEventMessages(null, $objectdet->errors, 'errors');
				else  setEventMessages($objectdet->error, null, 'errors');
				$action='create';
			}
		}
		else
		{
			$action='create';
		}
	}

	// Action to update record
	if ($action == 'update')
	{
		$error=0;


		$objectdet->entity=GETPOST('entity','int');
		$objectdet->ref=GETPOST('ref','alpha');
		$objectdet->fk_budget=GETPOST('fk_budget','int');
		$objectdet->fk_task=GETPOST('fk_task','int');
		$objectdet->fk_task_parent=GETPOST('fk_task_parent','int');
		$objectdet->label=GETPOST('label','alpha');
		$objectdet->description=GETPOST('description','alpha');
		$objectdet->duration_effective=GETPOST('duration_effective','alpha');
		$objectdet->planned_workload=GETPOST('planned_workload','alpha');
		$objectdet->progress=GETPOST('progress','int');
		$objectdet->priority=GETPOST('priority','int');
		$objectdet->fk_user_creat=GETPOST('fk_user_creat','int');
		$objectdet->fk_user_valid=GETPOST('fk_user_valid','int');
		$objectdet->fk_statut=GETPOST('fk_statut','int');
		$objectdet->note_private=GETPOST('note_private','alpha');
		$objectdet->note_public=GETPOST('note_public','alpha');
		$objectdet->rang=GETPOST('rang','int');
		$objectdet->model_pdf=GETPOST('model_pdf','alpha');



		if (empty($objectdet->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objectdet->update($user);
			if ($result > 0)
			{
				$action='view';
			}
			else
			{
				// Creation KO
				if (! empty($objectdet->errors)) setEventMessages(null, $objectdet->errors, 'errors');
				else setEventMessages($objectdet->error, null, 'errors');
				$action='edit';
			}
		}
		else
		{
			$action='edit';
		}
	}

	// Action to delete
	if ($action == 'confirm_delete')
	{
		$result=$objectdet->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/budget/list.php',1));
			exit;
		}
		else
		{
			if (! empty($objectdet->errors)) setEventMessages(null, $objectdet->errors, 'errors');
			else setEventMessages($objectdet->error, null, 'errors');
		}
	}
}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/


// Put here content of your page




// Part to create
if ($action == 'create')
{
	print load_fiche_titre($langs->trans("NewMyModule"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td><input class="flat" type="text" name="entity" value="'.GETPOST('entity').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.GETPOST('ref').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_budget").'</td><td><input class="flat" type="text" name="fk_budget" value="'.GETPOST('fk_budget').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_task").'</td><td><input class="flat" type="text" name="fk_task" value="'.GETPOST('fk_task').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_task_parent").'</td><td><input class="flat" type="text" name="fk_task_parent" value="'.GETPOST('fk_task_parent').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.GETPOST('label').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fielddescription").'</td><td><input class="flat" type="text" name="description" value="'.GETPOST('description').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldduration_effective").'</td><td><input class="flat" type="text" name="duration_effective" value="'.GETPOST('duration_effective').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldplanned_workload").'</td><td><input class="flat" type="text" name="planned_workload" value="'.GETPOST('planned_workload').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprogress").'</td><td><input class="flat" type="text" name="progress" value="'.GETPOST('progress').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpriority").'</td><td><input class="flat" type="text" name="priority" value="'.GETPOST('priority').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_creat").'</td><td><input class="flat" type="text" name="fk_user_creat" value="'.GETPOST('fk_user_creat').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_valid").'</td><td><input class="flat" type="text" name="fk_user_valid" value="'.GETPOST('fk_user_valid').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_statut").'</td><td><input class="flat" type="text" name="fk_statut" value="'.GETPOST('fk_statut').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnote_private").'</td><td><input class="flat" type="text" name="note_private" value="'.GETPOST('note_private').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnote_public").'</td><td><input class="flat" type="text" name="note_public" value="'.GETPOST('note_public').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrang").'</td><td><input class="flat" type="text" name="rang" value="'.GETPOST('rang').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodel_pdf").'</td><td><input class="flat" type="text" name="model_pdf" value="'.GETPOST('model_pdf').'"></td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}



// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	print load_fiche_titre($langs->trans("MyModule"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$objectdet->id.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	//
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td><input class="flat" type="text" name="entity" value="'.$objectdet->entity.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.$objectdet->ref.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_budget").'</td><td><input class="flat" type="text" name="fk_budget" value="'.$objectdet->fk_budget.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_task").'</td><td><input class="flat" type="text" name="fk_task" value="'.$objectdet->fk_task.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_task_parent").'</td><td><input class="flat" type="text" name="fk_task_parent" value="'.$objectdet->fk_task_parent.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.$objectdet->label.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fielddescription").'</td><td><input class="flat" type="text" name="description" value="'.$objectdet->description.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldduration_effective").'</td><td><input class="flat" type="text" name="duration_effective" value="'.$objectdet->duration_effective.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldplanned_workload").'</td><td><input class="flat" type="text" name="planned_workload" value="'.$objectdet->planned_workload.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprogress").'</td><td><input class="flat" type="text" name="progress" value="'.$objectdet->progress.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpriority").'</td><td><input class="flat" type="text" name="priority" value="'.$objectdet->priority.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_creat").'</td><td><input class="flat" type="text" name="fk_user_creat" value="'.$objectdet->fk_user_creat.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_valid").'</td><td><input class="flat" type="text" name="fk_user_valid" value="'.$objectdet->fk_user_valid.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_statut").'</td><td><input class="flat" type="text" name="fk_statut" value="'.$objectdet->fk_statut.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnote_private").'</td><td><input class="flat" type="text" name="note_private" value="'.$objectdet->note_private.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnote_public").'</td><td><input class="flat" type="text" name="note_public" value="'.$objectdet->note_public.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrang").'</td><td><input class="flat" type="text" name="rang" value="'.$objectdet->rang.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodel_pdf").'</td><td><input class="flat" type="text" name="model_pdf" value="'.$objectdet->model_pdf.'"></td></tr>';

	print '</table>';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}

// Part to show record
if ($object->id > 0)
{
	$res = $objectdet->fetch_optionals($objectdet->id, $extralabels);
	$head = productbudget_prepare_head($objectdet,$user,$action);
	dol_fiche_head($head, 'card', $langs->trans("Budgetprices"),0,'item');

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $objectdet->id, $langs->trans('DeleteMyOjbect'), $langs->trans('ConfirmDeleteMyObject'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>'.$objectdet->label.'</td></tr>';
	//
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td>'.$objectdet->entity.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldref").'</td><td>'.$objectdet->ref.'</td></tr>';
	//print '<tr><td>'.$langs->trans("Fieldfk_budget").'</td><td>'.$objectdet->fk_budget.'</td></tr>';
	//vamos a buscar el origen del item
	if ($objectdet->fk_task>0)
	{
		$resitem = $items->fetch($objectdet->fk_task);
		if ($resitem==1)
			print '<tr><td>'.$langs->trans("Fieldfk_task").'</td><td>'.$items->getNomUrladd(1).'</td></tr>';
		else
			print '<tr><td>'.$langs->trans("Fieldfk_task").'</td><td>'.$objectdet->fk_task.'</td></tr>';
	}
	//print '<tr><td>'.$langs->trans("Fieldfk_task_parent").'</td><td>'.$objectdet->fk_task_parent.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldlabel").'</td><td>'.$objectdet->label.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fielddescription").'</td><td>'.$objectdet->description.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldduration_effective").'</td><td>'.$objectdet->duration_effective.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldplanned_workload").'</td><td>'.$objectdet->planned_workload.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprogress").'</td><td>'.$objectdet->progress.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpriority").'</td><td>'.$objectdet->priority.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_creat").'</td><td>'.$objectdet->fk_user_creat.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_valid").'</td><td>'.$objectdet->fk_user_valid.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_statut").'</td><td>'.$objectdet->fk_statut.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnote_private").'</td><td>'.$objectdet->note_private.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnote_public").'</td><td>'.$objectdet->note_public.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrang").'</td><td>'.$objectdet->rang.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodel_pdf").'</td><td>'.$objectdet->model_pdf.'</td></tr>';

	print '</table>';

	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$objectdet,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->budget->task->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$objectdet->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->budget->task->del)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$objectdet->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
		}
	}
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	// Show links to link elements
	//$linktoelem = $form->showLinkToObjectBlock($object, null, array('budgettask'));
	//$somethingshown = $form->showLinkedObjectBlock($object, $linktoelem);

}

