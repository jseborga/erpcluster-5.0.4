<?php
/* Copyright (C) 2010-2012 Regis Houssin  <regis.houssin@capnetworks.com>
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
 *	\file       htdocs/projet/tasks/note.php
 *	\ingroup    project
 *	\brief      Page to show information on a task
 */

require ("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/taskext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskelement.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/monprojet.lib.php';

$langs->load('projects');
$langs->load('monprojet@monprojet');

$action=GETPOST('action','alpha');
$confirm=GETPOST('confirm','alpha');
$mine = $_REQUEST['mode']=='mine' ? 1 : 0;
//if (! $user->rights->projet->all->lire) $mine=1;	// Special for projects
$id  = GETPOST('id','int');
$idr = GETPOST('idr','int');
$ref = GETPOST('ref', 'alpha');
$withproject=GETPOST('withproject','int');
$project_ref = GETPOST('project_ref','alpha');

// Security check
$socid=0;
if ($user->societe_id > 0) $socid = $user->societe_id;
if (!$user->rights->projet->lire) accessforbidden();
//$result = restrictedArea($user, 'projet', $id, '', 'task'); // TODO ameliorer la verification

$object = new Task($db);
$objadd = new Taskext($db);
$projectstatic = new Project($db);
$elements = new Projettaskelement($db);

if ($id > 0 || ! empty($ref))
{
	if ($object->fetch($id,$ref) > 0)
	{
		$objadd->fetch($id,$ref);
		$projectstatic->fetch($object->fk_project);
		if (! empty($projectstatic->socid)) $projectstatic->fetch_thirdparty();
		
		$object->project = dol_clone($projectstatic);
	}
	else
	{
		dol_print_error($db);
	}
}


// Retreive First Task ID of Project if withprojet is on to allow project prev next to work
if (! empty($project_ref) && ! empty($withproject))
{
	if ($projectstatic->fetch(0,$project_ref) > 0)
	{
		$tasksarray=$object->getTasksArray(0, 0, $projectstatic->id, $socid, 0);
		if (count($tasksarray) > 0)
		{
			$id=$tasksarray[0]->id;
			$object->fetch($id);
		}
		else
		{
			header("Location: ".DOL_URL_ROOT.'/projet/tasks.php?id='.$projectstatic->id.(empty($mode)?'':'&mode='.$mode));
		}
	}
}

$permissionnote=($user->rights->projet->creer || $user->rights->projet->all->creer);
$permissionnote=($user->rights->monprojet->notepri->crear || $user->rights->monprojet->notepub->crear);

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('attachment'));

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	//action giveback
	if ($action == 'addattach' && $user->rights->monprojet->att->crear)
	{
		if ($object->id == $id)
		{
		//creamos
			$elements->fk_task = $id;
			$elements->attachment = GETPOST('attachment','alpha');
			$elements->fk_user_create = $user->id;
			$elements->date_create = dol_now();
			$elements->tms = dol_now();
			$elements->statut = 1;	    
			$res = $elements->create($user);
			if (!$res > 0)
			{
				setEventMessages(null, $langs->trans("ErrorIDoNotRunCorrecty"), 'errors');
			}
			else
			{
		// update OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/task/attachment.php?id='.$id.'&withproject=1',1);
				header("Location: ".$urltogo);
			}
		}
		$action = '';
		unset($_POST);
	}

	if ($action == 'confirm_delete' && $_REQUEST['confirm'] == 'yes' && $user->rights->monprojet->att->del)
	{
		if ($object->id == $id)
		{
			$res = $elements->fetch(GETPOST('idr'));
			if ($res > 0 && $elements->id == GETPOST('idr'))
			{
				$res = $elements->delete($user);
				if ($res <= 0)
				{
					setEventMessages($elements->error,$elements->errors, 'errors');
				}
			}
			else
			{
				// update OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/task/attachment.php?id='.$id.'&withproject=1',1);
				header("Location: ".$urltogo);
			}
		}
		$action = '';
		unset($_POST);
	}
}
/*
 * View
 */

llxHeader('', $langs->trans("Task"));

$form = new Formv($db);
$userstatic = new User($db);

$now=dol_now();

if ($object->id > 0)
{
	$userWrite  = $projectstatic->restrictedProjectArea($user,'write');
	if ($conf->global->MONPROJET_USE_WITHPROJECT)
		$withproject = 1;
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
		
	// Project
		print '<tr><td>'.$langs->trans("Label").'</td><td>'.$projectstatic->title.'</td></tr>';
		
	// // Company
	// print '<tr><td>'.$langs->trans("ThirdParty").'</td><td>';
	// if (! empty($projectstatic->thirdparty->id)) print $projectstatic->thirdparty->getNomUrl(1);
	// else print '&nbsp;';
	// 	print '</td>';
	// 	print '</tr>';

	// 	// Visibility
	// 	print '<tr><td>'.$langs->trans("Visibility").'</td><td>';
	// 	if ($projectstatic->public) print $langs->trans('SharedProject');
	// 	else print $langs->trans('PrivateProject');
	// 	print '</td></tr>';

	// 	// Statut
	// 	print '<tr><td>'.$langs->trans("Status").'</td><td>'.$projectstatic->getLibStatut(4).'</td></tr>';

	//    	// Date start
	// 	print '<tr><td>'.$langs->trans("DateStart").'</td><td>';
	// 	print dol_print_date($projectstatic->date_start,'day');
	// 	print '</td></tr>';

	// 	// Date end
	// 	print '<tr><td>'.$langs->trans("DateEnd").'</td><td>';
	// 	print dol_print_date($projectstatic->date_end,'day');
	// 	print '</td></tr>';

		print '</table>';

		dol_fiche_end();
	}

	$head = task_prepare_head($object);
	dol_fiche_head($head, 'task_attach', $langs->trans('Task'), 0, 'projecttask');

	print '<table class="border" width="100%">';

	$param=(GETPOST('withproject')?'&withproject=1':'');
	$linkback=GETPOST('withproject')?'<a href="'.DOL_URL_ROOT.'/projet/tasks.php?id='.$projectstatic->id.'">'.$langs->trans("BackToList").'</a>':'';

	// Ref
	print '<tr><td width="30%">'.$langs->trans("Ref").'</td><td>';
	if (empty($withproject) || empty($projectstatic->id))
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
	print '</td></tr>';

	// Label
	print '<tr><td>'.$langs->trans("Label").'</td><td>'.$object->label.'</td></tr>';

	// Project
	if (empty($withproject))
	{
		print '<tr><td>'.$langs->trans("Project").'</td><td colspan="3">';
		print $projectstatic->getNomUrl(1);
		print '</td></tr>';

		// Third party
		print '<tr><td>'.$langs->trans("ThirdParty").'</td><td>';
		if ($projectstatic->thirdparty->id > 0) print $projectstatic->thirdparty->getNomUrl(1);
		else print'&nbsp;';
		print '</td></tr>';
	}

	print "</table>";

	print '<br>';

	dol_fiche_end();

	//lista los elementos
	dol_fiche_head();
	$filter[1] = 1;
	$filterstatic = " AND t.fk_task = ".$id;
	$elements->fetchAll($sortorder,$sortfield,$limit,$offset,$filter,'AND',$filterstatic);
	print '<table class="border" width="100%">';
		// Fields title
	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans('Attachments'),$_SERVER['PHP_SELF'],'t.attachment','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Action'),'','','',$param,'align="right"');
	print '</tr>';
	foreach ((array) $elements->lines AS $j => $lines)
	{
		print '<tr>';
		print '<td>'.select_attachment($lines->attachment,'attachment','',0,1).'</td>';
		print '<td align="right">';
		if ($user->rights->monprojet->att->crear && $object->fk_statut == 0)

			print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$lines->id.'&action=delete&withproject=1">'.img_picto($langs->trans('Delete'),'delete').'</a>';
		else
			print '&nbsp;';
		print '</td>';
		print '</tr>';
	}
	print '</table>';
	if ($user->rights->monprojet->att->crear && $object->fk_statut == 0)
		include DOL_DOCUMENT_ROOT.'/monprojet/tpl/addattach.tpl.php';
	dol_fiche_end();
}


llxFooter();
$db->close();
