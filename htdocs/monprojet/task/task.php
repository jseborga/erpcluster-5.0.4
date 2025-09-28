<?php
/* Copyright (C) 2005		Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2006-2014	Laurent Destailleur		<eldy@users.sourceforge.net>
 * Copyright (C) 2010-2012	Regis Houssin			<regis.houssin@capnetworks.com>
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
 *	\file       htdocs/projet/tasks/task.php
 *	\ingroup    project
 *	\brief      Page of a project task
 */

require ("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';

require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/modules/project/task/modules_task.php';
if ($conf->budget->enabled)
{
	//require_once DOL_DOCUMENT_ROOT.'/budget/class/html.formadd.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/itemsext.class.php';
	dol_include_once('/budget/class/typeitemext.class.php');
	//dol_include_once('/budget/class/html.formadd.class.php');
}
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/taskext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskaddext.class.php';
//require_once DOL_DOCUMENT_ROOT.'/monprojet/class/html.formaddmon.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/monprojet.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/html.formotheradd.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/verifcontact.lib.php';

$langs->load("projects");
$langs->load("companies");

$id=GETPOST('id','int');
$ref=GETPOST('ref','alpha');
$action=GETPOST('action','alpha');
$confirm=GETPOST('confirm','alpha');
$withproject=GETPOST('withproject','int');
$project_ref=GETPOST('project_ref','alpha');
$planned_workload=GETPOST('planned_workloadhour')*3600+GETPOST('planned_workloadmin')*60;

// Security check
$socid=0;
$lDisabled = false;

if ($user->societe_id > 0) $socid = $user->societe_id;
if (! $user->rights->projet->lire) accessforbidden();

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
//echo '<hr>enviando';
$hookmanager->initHooks(array('projecttaskcard','globalcard','monprojet','doActions'));
// echo '<pre>';
// print_r($hookmanager);
// echo '</pre>';
$object = new Task($db);
$objadd = new Taskext($db);
$extrafields = new ExtraFields($db);
$projectstatic = new Project($db);
$objecttaskadd = new Projettaskaddext($db);
//priceunit
if ($conf->budget->enabled)
{
	$typeitem = new Typeitemext($db);
	$items = new Itemsext($db);
	//$formadd=new FormAdd($db);
}
// fetch optionals attributes and labels
$extralabels=$extrafields->fetch_name_optionals_label($object->table_element);


/*
 * Actions
 */

if ($action == 'update' && ! $_POST["cancel"] && $user->rights->projet->creer)
{
	$error=0;
	if (! $error)
	{
		$db->begin();
	//buscamos en items
	// $items->fetch('',$ref);
	// if ($items->ref == $ref)
	//   $label = $items->detail;
		$label = GETPOST('label');
		$object->fetch($id,$ref);
		$res=$object->fetch_optionals($object->id,$extralabels);

		$tmparray=explode('_',$_POST['task_parent']);
		$task_parent=$tmparray[1];
		if (empty($task_parent)) $task_parent = 0;
		// If task_parent is ''

		$object->label = $label;
		$object->description = $_POST['description'];
		$object->fk_task_parent = $task_parent;
		$object->planned_workload = $planned_workload;
		$_POST['options_unit_declared'] = $object->array_options['options_unit_declared']+0;
		$_POST['options_fk_item'] = $object->array_options['options_fk_item']+0;
		if (!$_POST['options_c_grupo'])
		{
			$object->date_start = dol_mktime($_POST['dateohour'],$_POST['dateomin'],0,$_POST['dateomonth'],$_POST['dateoday'],$_POST['dateoyear'],'user');
			$object->date_end = dol_mktime($_POST['dateehour'],$_POST['dateemin'],0,$_POST['dateemonth'],$_POST['dateeday'],$_POST['dateeyear'],'user');
			$object->progress = $_POST['progress']+0;
		}
	// Fill array 'array_options' with data from add form
		$ret = $extrafields->setOptionalsFromPost($extralabels,$object);
		if ($ret < 0)
			$error++;
		if (! $error)
		{
			$result=$object->update($user,1);
			if ($result < 0)
			{
				$error++;
				setEventMessages($object->error,$object->errors,'errors');
			}
		//actualizamos en la nueva tabla
		//buscamos el item
			$res = $objecttaskadd->fetch('',$id);
			if ($res>0 && $objecttaskadd->fk_task == $id)
			{
				$objecttaskadd->fk_item = GETPOST('fk_item')+0;
				if (!empty(GETPOST('ref_item')))
				{
					$items->fetch('',GETPOST('ref_item'));
					if ($items->ref == GETPOST('ref_item'))
						$objecttaskadd->fk_item = $items->id;
				}
				$objecttaskadd->fk_type = GETPOST('fk_type')+0;
				$objecttaskadd->c_grupo = $_POST['options_c_grupo'];
				$objecttaskadd->c_view = $_POST['options_c_view']+0;
				$objecttaskadd->fk_unit = $_POST['options_fk_unit'];
				$objecttaskadd->unit_program = $_POST['options_unit_program'];
				$objecttaskadd->unit_amount = $_POST['options_unit_amount'];
				$objecttaskadd->fk_user_mod = $user->id;
				$objecttaskadd->tms = dol_now();
				$res = $objecttaskadd->update($user);
				if ($res<=0)
				{
					setEventMessages($objecttaskadd->error,$objecttaskadd->errors,'errors');
					$error++;
				}
			}
			else
			{
				$objecttaskadd->fk_task = $id;
				if (!empty(GETPOST('ref_item')))
				{
					$items->fetch('',GETPOST('ref_item'));
					if ($items->ref == GETPOST('ref_item'))
						$objecttaskadd->fk_item = $items->id;
				}
				else
					$objecttaskadd->fk_item = 0;	
				$objecttaskadd->fk_type = GETPOST('fk_type')+0;
				$objecttaskadd->c_grupo = $_POST['options_c_grupo'];
				$objecttaskadd->c_view = $_POST['options_c_view']+0;
				$objecttaskadd->fk_unit = $_POST['options_fk_unit'];
				$objecttaskadd->unit_program = $_POST['options_unit_program'];
				$objecttaskadd->unit_amount = $_POST['options_unit_amount'];
				$objecttaskadd->fk_user_create = $user->id;
				$objecttaskadd->fk_user_mod = $user->id;
				$objecttaskadd->date_create = dol_now();
				$objecttaskadd->tms = dol_now();
				$objecttaskadd->statut = 1;
				$res = $objecttaskadd->create($user);
				if ($res<=0)
				{
					setEventMessages($objecttaskadd->error,$objecttaskadd->errors,'errors');
					$error++;
				}
			}
		}
		if (!$error)
		{
			setEventMessages($langs->trans('Saverecord'),null,'mesgs');
			$db->commit();
			$action = '';
		}
		else
			$db->rollback();
	}
	else
	{
		$action='edit';
	}
}

if ($action == 'confirm_delete' && $confirm == "yes" && $user->rights->projet->supprimer)
{
	if ($object->fetch($id,$ref) >= 0)
	{
		$result=$projectstatic->fetch($object->fk_project);
		$projectstatic->fetch_thirdparty();

		if ($object->delete($user) > 0)
		{
			header('Location: '.DOL_URL_ROOT.'/monprojet/tasks.php?id='.$projectstatic->id.($withproject?'&withproject=1':''));
			exit;
		}
		else
		{
			setEventMessages($object->error,$object->errors,'errors');
			$action='';
		}
	}
}

// Retreive First Task ID of Project if withprojet is on to allow project prev next to work
if (! empty($project_ref) && ! empty($withproject))
{
	if ($projectstatic->fetch('',$project_ref) > 0)
	{
		$tasksarray=$object->getTasksArray(0, 0, $projectstatic->id, $socid, 0);
		if (count($tasksarray) > 0)
		{
			$id=$tasksarray[0]->id;
		}
		else
		{
			header("Location: ".DOL_URL_ROOT.'/projet/tasks.php?id='.$projectstatic->id.(empty($mode)?'':'&mode='.$mode));
		}
	}
}

// Build doc
if ($action == 'builddoc' && $user->rights->projet->creer)
{
	$object->fetch($id,$ref);

	// Save last template used to generate document
	if (GETPOST('model')) $object->setDocModel($user, GETPOST('model','alpha'));

	$outputlangs = $langs;
	if (GETPOST('lang_id'))
	{
		$outputlangs = new Translate("",$conf);
		$outputlangs->setDefaultLang(GETPOST('lang_id'));
	}
	$result= $object->generateDocument($object->modelpdf, $outputlangs);
	if ($result <= 0)
	{
		dol_print_error($db,$result);
		exit;
	}
}

// Delete file in doc form
if ($action == 'remove_file' && $user->rights->projet->creer)
{
	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

	if ($object->fetch($id,$ref) >= 0 )
	{
		$langs->load("other");
		$upload_dir =	$conf->projet->dir_output;
		$file =	$upload_dir	. '/' .	GETPOST('file');

		$ret=dol_delete_file($file);
		if ($ret) setEventMessage($langs->trans("FileWasRemoved", GETPOST('urlfile')));
		else setEventMessage($langs->trans("ErrorFailToDeleteFile", GETPOST('urlfile')), 'errors');
	}
}


/*
 * View
*/


llxHeader('', $langs->trans("Task"));

$form = new Formv($db);
//if ($conf->budget->enabled)
//	$formadd = new FormAdd($db);
$formother = new FormOther($db);
$formfile = new FormFile($db);

if ($id > 0 || ! empty($ref))
{
	if ($object->fetch($id,$ref) > 0)
	{
		$objadd->fetch($id,$ref);

		$objecttaskadd->fetch('',$object->id);

		$res=$object->fetch_optionals($object->id,$extralabels);
		$res=$objadd->fetch_optionals($objadd->id,$extralabels);
		//	if ($object->array_options['options_c_grupo'] == 1)
		if ($objecttaskadd->c_grupo == 1)
			$lDisabled = true;
		if ($action == 'createrefr')
		{
			//require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
			//$tmparray=getProperty(GETPOST('country_id','int'),'all',$db,$langs,0);
			$tmparray['label'] = GETPOST('label');
			$tmparray['task_parent'] = GETPOST('task_parent');
			$tmparray['userid'] = GETPOST('userid');
			$tmparray['options_c_grupo'] = GETPOST('options_c_grupo');

			if ($tmparray['options_c_grupo'] == 1)
				$lDisabled = true;
			$object->array_options['options_c_grupo'] = $tmparray['options_c_grupo'];
			$objecttaskadd->c_grupo = $tmparray['options_c_grupo'];
			$object->label = $tmparray['label'];
			$object->fk_task_parent = $tmparray['task_parent'];
			$userid = $tmparray['userid'];
			$action='edit';
		}
		//projecto
		$result=$projectstatic->fetch($object->fk_project);

		if (! empty($projectstatic->socid)) $projectstatic->fetch_thirdparty();

		$object->project = dol_clone($projectstatic);

		$userWrite  = $projectstatic->restrictedProjectArea($user,'write');

		if (! empty($withproject))
		{
		// Tabs for project
			$tab='tasks';
			$head=project_prepare_head($projectstatic);
			dol_fiche_head($head, $tab, $langs->trans("Project"),0,($projectstatic->public?'projectpub':'project'));

			$param=($mode=='mine'?'&mode=mine':'');

			print '<table class="border" width="100%">';

		// Ref
			print '<tr><td width="30%">';
			print $langs->trans("Ref");
			print '</td><td>';
		// Define a complementary filter for search of next/prev ref.
			if (! $user->rights->projet->all->lire)
			{
				$projectsListId = $projectstatic->getProjectsAuthorizedForUser($user,$mine,0);
				$projectstatic->next_prev_filter=" rowid in (".(count($projectsListId)?join(',',array_keys($projectsListId)):'0').")";
			}
			print $form->showrefnav($projectstatic,'project_ref','',1,'ref','ref','',$param.'&withproject=1');
			print '</td></tr>';

			print '<tr><td>'.$langs->trans("Label").'</td><td>'.$projectstatic->title.'</td></tr>';

		// print '<tr><td>'.$langs->trans("ThirdParty").'</td><td>';
		// if (! empty($projectstatic->thirdparty->id)) print $projectstatic->thirdparty->getNomUrl(1);
		// else print '&nbsp;';
		// print '</td>';
		// print '</tr>';

		// // Visibility
		// print '<tr><td>'.$langs->trans("Visibility").'</td><td>';
		// if ($projectstatic->public) print $langs->trans('SharedProject');
		// else print $langs->trans('PrivateProject');
		// print '</td></tr>';

		// // Statut
		// print '<tr><td>'.$langs->trans("Status").'</td><td>'.$projectstatic->getLibStatut(4).'</td></tr>';

		// // Date start
		// print '<tr><td>'.$langs->trans("DateStart").'</td><td>';
		// print dol_print_date($projectstatic->date_start,'day');
		// print '</td></tr>';

		// // Date end
		// print '<tr><td>'.$langs->trans("DateEnd").'</td><td>';
		// print dol_print_date($projectstatic->date_end,'day');
		// print '</td></tr>';

			print '</table>';

			dol_fiche_end();
		}


	  // To verify role of users
	  //$userAccess = $projectstatic->restrictedProjectArea($user); // We allow task affected to user even if a not allowed project
	  //$arrayofuseridoftask=$object->getListContactId('internal');

		$head=task_prepare_head($object);

		if ($action == 'edit' && $user->rights->projet->creer)
		{
			print "\n".'<script type="text/javascript" language="javascript">';
			print '$(document).ready(function () {
				$("#options_c_grupo").change(function() {
					document.form.action.value="createrefr";
					document.form.submit();
				});
			});';
			print '</script>'."\n";

			print '<form method="POST" id="form" name="form" action="'.$_SERVER["PHP_SELF"].'">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="update">';
			print '<input type="hidden" name="withproject" value="'.$withproject.'">';
			print '<input type="hidden" name="id" value="'.$object->id.'">';
			print '<input type="hidden" name="options_fk_item" value="'.$object->array_options['options_fk_item'].'">';

			dol_fiche_head($head, 'task_task', $langs->trans("Task"),0,'projecttask');

			print '<table class="border" width="100%">';

		  // Ref
			print '<tr><td width="30%">'.$langs->trans("Ref").'</td>';
			print '<td>'.$object->ref.'</td></tr>';
		  //nueva forma de crear tareas en base a items
		  // print '<tr><td><span class="fieldrequired">'.$langs->trans("Ref").'</span></td>';
		  // print '<td>';
		  // print $formadd->select_item($object->ref,'ref','',1);
		  // print '</td></tr>';
		  //fin nueva forma

		  // Label
			print '<tr><td>'.$langs->trans("Label").'</td>';
			print '<td><input size="30" name="label" value="'.$object->label.'"></td></tr>';

		  // Project
			if (empty($withproject))
			{
				print '<tr><td>'.$langs->trans("Project").'</td><td colspan="3">';
				print $projectstatic->getNomUrl(1);
				print '</td></tr>';

		  // Third party
				print '<td>'.$langs->trans("ThirdParty").'</td><td colspan="3">';
				if ($projectstatic->societe->id) print $projectstatic->societe->getNomUrl(1);
				else print '&nbsp;';
				print '</td></tr>';
			}
		  // Task parent
			$formotheradd = new FormOtherAdd($db);
			print '<tr><td>'.$langs->trans("ChildOfTask").'</td><td>';
			$formotheradd->selectProjectTasks_($object->fk_task_parent, $projectstatic->id, 'task_parent', ($user->admin?0:1), 0, 0, 0, $object->id);
			print '</td></tr>';
			echo 'asdfasdf';
		//echo '<hr>asdfasdfasdf';exit;
		  //agregamos el grupo
			print '<tr><td>'.$langs->trans("Group").'</td><td>';
			print $form->selectyesno('options_c_grupo',$objecttaskadd->c_grupo,1,'');
			print '</td></tr>';

		  //tareas internas o externas
			print '<tr><td>'.$langs->trans("Internaltask").'</td><td>';
			print $form->selectyesno('options_c_view',$objecttaskadd->c_view,1,'');
			print '</td></tr>';

			if (!$lDisabled)
			{
		  // Date start
				print '<tr><td>'.$langs->trans("DateStart").'</td><td>';
				print $form->select_date($object->date_start,'dateo',1,1,0,'',1,0,1);
				print '</td></tr>';

		  // Date end
				print '<tr><td>'.$langs->trans("DateEnd").'</td><td>';
				print $form->select_date($object->date_end?$object->date_end:-1,'datee',1,1,0,'',1,0,1);
				print '</td></tr>';
			}
		  // Planned workload
		  // print '<tr><td>'.$langs->trans("PlannedWorkload").'</td><td>';
		  // print $form->select_duration('planned_workload',$object->planned_workload,0,'text');
		  // print '</td></tr>';
			if (!$lDisabled)
			{
		  // Progress declared
				print '<tr><td>'.$langs->trans("ProgressDeclared").'</td><td colspan="3">';
				print $formother->select_percent($object->progress,'progress');
				print '</td></tr>';
			}
		  // Description
			print '<tr><td valign="top">'.$langs->trans("Description").'</td>';
			print '<td>';
			print '<textarea name="description" wrap="soft" cols="80" rows="'.ROWS_3.'">'.$object->description.'</textarea>';
			print '</td></tr>';

		  // Other options
			if (!$lDisabled)
			{
		  //$parameters=array('newaction'=>'ejecutetask');
				$parameters=array('newaction'=>'addextra');
				$reshook=$hookmanager->executeHooks('formObjectOptions',$parameters,$object,$action); 
		  // Note that $action and $object may have been modified by hook
				if (empty($reshook) && ! empty($extrafields->attribute_label))
				{
					print $object->showOptionals($extrafields,'edit');
				}
			}
			print '</table>';

			dol_fiche_end();

			print '<div align="center">';
			print '<input type="submit" class="button" name="update" value="'.$langs->trans("Modify").'"> &nbsp; ';
			print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
			print '<div>';

			print '</form>';
		}
		else
		{
		// Fiche tache en mode visu
			$param=($withproject?'&withproject=1':'');
			$linkback=$withproject?'<a href="'.DOL_URL_ROOT.'/monprojet/tasks.php?id='.$projectstatic->id.'">'.$langs->trans("BackToList").'</a>':'';

			dol_fiche_head($head, 'task_task', $langs->trans("Task"),0,'projecttask');

			if ($action == 'delete')
			{
				print $form->formconfirm($_SERVER["PHP_SELF"]."?id=".$_GET["id"].'&withproject='.$withproject,
					$langs->trans("DeleteATask"),
					$langs->trans("ConfirmDeleteATask"),"confirm_delete",'',0,2);
			}

			print '<table class="border" width="100%">';

		  // Ref
			print '<tr><td width="30%">';
			print $langs->trans("Ref");
			print '</td><td colspan="3">';

			if (! GETPOST('withproject') || empty($projectstatic->id))
			{
				$projectsListId = $projectstatic->getProjectsAuthorizedForUser($user,$mine,1);
				$object->next_prev_filter=" fk_projet in (".$projectsListId.")";
				$objadd->next_prev_filter=" fk_projet in (".$projectsListId.")";
			}
			else
			{
				$object->next_prev_filter=" fk_projet = ".$projectstatic->id;
				$objadd->next_prev_filter=" fk_projet = ".$projectstatic->id;
			}
			print $form->showrefnavadd($objadd,'id',$linkback,1,'ref','ref','',$param);
			print '</td>';
			print '</tr>';

		  // Label
			print '<tr><td>'.$langs->trans("Label").'</td><td colspan="3">'.$object->label.'</td></tr>';

		  // Project
			if (empty($withproject))
			{
				print '<tr><td>'.$langs->trans("Project").'</td><td colspan="3">';
				print $projectstatic->getNomUrl(1);
				print '</td></tr>';

		  // Third party
				print '<td>'.$langs->trans("ThirdParty").'</td><td colspan="3">';
				if ($projectstatic->societe->id) print $projectstatic->societe->getNomUrl(1);
				else print '&nbsp;';
				print '</td></tr>';
			}
		  //group
			print '<tr>';
			print '<td>';
			print $langs->trans('Group');
			print '</td>';
			print '<td>';
			print ($objecttaskadd->c_grupo==1?$langs->trans('Yes'):$langs->trans('Not'));
			print '</td>';
			print '</tr>';

		  //internalview
			print '<tr>';
			print '<td>';
			print $langs->trans('Internaltask');
			print '</td>';
			print '<td>';
			print ($objecttaskadd->c_view==1?$langs->trans('Yes'):$langs->trans('Not'));
			print '</td>';
			print '</tr>';

			if (!$lDisabled)
			{
		  // Date start
				print '<tr><td>'.$langs->trans("DateStart").'</td><td colspan="3">';
				print dol_print_date($object->date_start,'dayhour');
				print '</td></tr>';

		  // Date end
				print '<tr><td>'.$langs->trans("DateEnd").'</td><td colspan="3">';
				print dol_print_date($object->date_end,'dayhour');
				print '</td></tr>';

		  // Planned workload
		  // print '<tr><td>'.$langs->trans("PlannedWorkload").'</td><td colspan="3">';
		  // print convertSecondToTime($object->planned_workload,'allhourmin');
		  // print '</td></tr>';

		  // Progress declared
				print '<tr><td>'.$langs->trans("ProgressDeclared").'</td><td colspan="3">';
				print $object->progress.' %';
				print '</td></tr>';

		  // Progress calculated
				print '<tr><td>'.$langs->trans("ProgressCalculated").'</td><td colspan="3">';
				if ($object->planned_workload)
				{
					$tmparray=$object->getSummaryOfTimeSpent();
					if ($tmparray['total_duration'] > 0) print round($tmparray['total_duration'] / $object->planned_workload * 100, 2).' %';
					else print '0 %';
				}
				else print '';
				print '</td></tr>';
			}
		  // Description
			print '<td valign="top">'.$langs->trans("Description").'</td><td colspan="3">';
			print nl2br($object->description);
			print '</td></tr>';

		  // Other options
			if (!$lDisabled)
			{
				$parameters=array('newaction'=>'view');
				$reshook=$hookmanager->executeHooks('doActions',$parameters,$objadd,$action);
		   // Note that $action and $object may have been modified by hook
				if (empty($reshook) && ! empty($extrafields->attribute_label))
				{
					print $object->showOptionals($extrafields);
				}
			}
			print '</table>';

			dol_fiche_end();
		}


		if ($action != 'edit')
		{
			/*
			 * Actions
			*/
			print '<div class="tabsAction">';

			// Modify
			if ($user->rights->monprojet->task->mod && $object->fk_statut == 0)
			{
				print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&amp;action=edit&amp;withproject='.$withproject.'">'.$langs->trans('Modify').'</a>';
			}
			else
			{
				print '<a class="butActionRefused" href="#" title="'.$langs->trans("NotAllowed").'">'.$langs->trans('Modify').'</a>';
			}

			// Delete
			if ($user->rights->monprojet->task->del && ! $object->hasChildren() && $object->fk_statut == 0)
			{
				print '<a class="butActionDelete" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&amp;action=delete&amp;withproject='.$withproject.'">'.$langs->trans('Delete').'</a>';
			}
			else
			{
				print '<a class="butActionRefused" href="#" title="'.$langs->trans("NotAllowed").'">'.$langs->trans('Delete').'</a>';
			}

			print '</div>';

			print '<table width="100%"><tr><td width="50%" valign="top">';
			print '<a name="builddoc"></a>'; // ancre

			/*
			 * Documents generes
			 */
			$filename=dol_sanitizeFileName($projectstatic->ref). "/". dol_sanitizeFileName($object->ref);
			$filedir=$conf->projet->dir_output . "/" . dol_sanitizeFileName($projectstatic->ref). "/" .dol_sanitizeFileName($object->ref);
			$urlsource=$_SERVER["PHP_SELF"]."?id=".$object->id;
			$genallowed=($user->rights->projet->lire);
			$delallowed=($user->rights->projet->creer);

			$var=true;

			$somethingshown=$formfile->show_documents('project_task',$filename,$filedir,$urlsource,$genallowed,$delallowed,$object->modelpdf);



			print '</td></tr></table>';
		}
	}
}


llxFooter();
$db->close();
