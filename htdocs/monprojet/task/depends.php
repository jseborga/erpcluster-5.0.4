<?php
/* Copyright (C) 2005		Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2006-2010	Laurent Destailleur		<eldy@users.sourceforge.net>
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
 *	\file       htdocs/projet/tasks/contact.php
 *	\ingroup    project
 *	\brief      Actors of a task
 */

require ("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskdepends.class.php';
//require_once DOL_DOCUMENT_ROOT.'/monprojet/class/html.formaddmon.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/taskext.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

$langs->load("projects");
$langs->load("companies");

$id=GETPOST('id','int');
$ref=GETPOST('ref','alpha');
$action=GETPOST('action','alpha');
$confirm=GETPOST('confirm','alpha');
$withproject=GETPOST('withproject','int');
$project_ref=GETPOST('project_ref','alpha');

// Security check
$socid=0;
if ($user->societe_id > 0) $socid = $user->societe_id;
//$result = restrictedArea($user, 'projet', $id, 'projet_task');
if (! $user->rights->projet->lire) accessforbidden();

$object = new Task($db);
$objadd = new Taskext($db);
$projectstatic = new Project($db);
$objprojtd = new Projettaskdepends($db);
$societe = new Societe($db);

/*
 * Actions
 */

// Add new contact
if ($action == 'adddepends' && $user->rights->monprojet->depe->crear)
  {
    $result = $object->fetch($id);
    
    if ($result > 0 && $id > 0)
      {
	list($id1,$fk_task_depends) = explode('_',GETPOST('task_depends'));
	$objprojtd->fk_task = $id;
	$objprojtd->fk_task_depends = $fk_task_depends;
	$objprojtd->fk_user_create = $user->id;
	$objprojtd->date_create = dol_now();
	$objprojtd->fk_user_modif = $user->id;
	$objprojtd->tms = dol_now();
	$objprojtd->statut = 1;
	$result = $objprojtd->create($user);
      }

    if ($result >= 0)
      {
	header("Location: ".$_SERVER["PHP_SELF"]."?id=".$object->id.($withproject?'&withproject=1':''));
	exit;
      }
    else
      {
	if ($object->error == 'DB_ERROR_RECORD_ALREADY_EXISTS')
	  {
	    $langs->load("errors");
	    $mesg = '<div class="error">'.$langs->trans("ErrorThisDependsIsAlreadyDefined").'</div>';
	  }
	else
	  {
	    $mesg = '<div class="error">'.$object->error.'</div>';
	  }
      }
  }

// bascule du statut d'un contact
if ($action == 'swapstatut' && $user->rights->projet->creer)
{
	if ($object->fetch($id))
	{
	    $result=$object->swapContactStatus(GETPOST('ligne'));
	}
	else
	{
		dol_print_error($db);
	}
}

// Efface un contact
if ($action == 'deleteline' && $user->rights->monprojet->depe->del)
{
	$object->fetch($id);
	if($objprojtd->fetch(GETPOST('lineid'))>0)
	  $result = $objprojtd->delete($user);

	if ($result >= 0)
	{
		header("Location: ".$_SERVER["PHP_SELF"]."?id=".$object->id.($withproject?'&withproject=1':''));
		exit;
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
		}
		else
		{
			header("Location: ".DOL_URL_ROOT.'/projet/tasks.php?id='.$projectstatic->id.($withproject?'&withproject=1':'').(empty($mode)?'':'&mode='.$mode));
			exit;
		}
	}
}

/*
 * View
 */

llxHeader('', $langs->trans("Task"));

$form = new Formv($db);
$formother = new FormOther($db);
$formcompany   = new FormCompany($db);
$contactstatic = new Contact($db);
$userstatic = new User($db);


/* *************************************************************************** */
/*                                                                             */
/* Mode vue et edition                                                         */
/*                                                                             */
/* *************************************************************************** */

if ($id > 0 || ! empty($ref))
{
	if ($object->fetch($id) > 0)
	{
	  $objadd->fetch($id,$ref);
		$result=$projectstatic->fetch($object->fk_project);
		if (! empty($projectstatic->socid)) $societe->fetch($projectstatic->socid);

		$object->project = dol_clone($projectstatic);

		$userWrite  = $projectstatic->restrictedProjectArea($user,'write');

		if ($withproject)
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
    		// if (! empty($projectstatic->societe->id)) print $projectstatic->societe->getNomUrl(1);
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

    		//print '<br>';
		}

		// To verify role of users
		//$userAccess = $projectstatic->restrictedProjectArea($user); // We allow task affected to user even if a not allowed project
		//$arrayofuseridoftask=$object->getListContactId('internal');

		dol_htmloutput_mesg($mesg);

		$head = task_prepare_head($object);
		dol_fiche_head($head, 'Dependences', $langs->trans("Task"), 0, 'projecttask');


		/*
		 *   Projet synthese pour rappel
		 */
		print '<table class="border" width="100%">';

		$param=(GETPOST('withproject')?'&withproject=1':'');
		$linkback=GETPOST('withproject')?'<a href="'.DOL_URL_ROOT.'/monprojet/tasks.php?id='.$projectstatic->id.'">'.$langs->trans("BackToList").'</a>':'';
		
		// Ref
		print '<tr><td width="30%">'.$langs->trans('Ref').'</td><td colspan="3">';
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
		print '</td></tr>';
		
		// Label
		print '<tr><td>'.$langs->trans("Label").'</td><td>'.$object->label.'</td></tr>';

		// Project
		if (empty($withproject))
		{
    		print '<tr><td>'.$langs->trans("Project").'</td><td>';
    		print $projectstatic->getNomUrl(1);
    		print '</td></tr>';

    		// Customer
    		print "<tr><td>".$langs->trans("ThirdParty")."</td>";
    		print '<td colspan="3">';
    		if ($projectstatic->societe->id > 0) print $projectstatic->societe->getNomUrl(1);
    		else print '&nbsp;';
    		print '</td></tr>';
		}

		print "</table>";

		dol_fiche_end();

		/*
		 * Lignes de contacts
		 */
		print '<br>';
/*
		// Contacts lines (modules that overwrite templates must declare this into descriptor)
		$dirtpls=array_merge($conf->modules_parts['tpl'],array('/core/tpl'));
		foreach($dirtpls as $reldir)
		{
		    $res=@include dol_buildpath($reldir.'/contacts.tpl.php');
		    if ($res) break;
		}
*/
		print '<table class="noborder" width="100%">';

		/*
		 * Ajouter une ligne de contact
		 * Non affiche en mode modification de ligne
		 */
		if ($action != 'editline' && $user->rights->projet->creer && $object->fk_statut == 0)
		  {
		    print '<tr class="liste_titre">';
		    print '<td>'.$langs->trans("Depends").'</td>';
		    print '<td colspan="3" align="right">'.$langs->trans('Action').'</td>';
		    print "</tr>\n";
		    
		    $var = false;
		    
		    print '<form action="'.$_SERVER["PHP_SELF"].'?id='.$id.'" method="POST">';
		    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		    print '<input type="hidden" name="action" value="adddepends">';
		    print '<input type="hidden" name="source" value="internal">';
		    print '<input type="hidden" name="id" value="'.$id.'">';
		    if ($withproject) print '<input type="hidden" name="withproject" value="'.$withproject.'">';
		    
		    // Ligne ajout pour contact interne
		    print "<tr ".$bc[$var].">";
		    
		    print '<td class="nowrap">';
		    print $formother->selectProjectTasks($object->fk_task_parent, $projectstatic->id, 'task_depends', ($user->admin?0:1), 0, 0, 0, $object->id);
		    print '</td>';
		    print '<td align="right" colspan="3" ><input type="submit" class="button" value="'.$langs->trans("Add").'"></td>';
		    print '</tr>';
		    
		    print '</form>';
		  }

		// Liste des contacts lies
		print '<tr class="liste_titre">';
		print '<td>'.$langs->trans("Depends").'</td>';
		print '<td align="center">'.$langs->trans("Status").'</td>';
		print '<td colspan="2">&nbsp;</td>';
		print "</tr>\n";

		$companystatic = new Societe($db);
		$var = true;

		$objprojettaskdepend = new Projettaskdepends($db);
		$objprojettaskdepend->getlist($id);
		if (count($objprojettaskdepend->array)>0)
		  {
		    foreach($objprojettaskdepend->array AS $j => $objdata)
		      {
			$var = !$var;
			print '<tr '.$bc[$var].' valign="top">';

			// buscamos la tarea
			print '<td align="left">';
			$objtasks = new Task($db);
			if ($objtasks->fetch($objdata->fk_task_depends)>0)
			    print $objtasks->label;
			else
			  print '&nbsp;';
			print '</td>';

			// Statut
			print '<td align="center">';
			// Activation desativation du contact
			if ($object->statut >= 0) print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=swapstatut&ligne='.$objdata->id.($withproject?'&withproject=1':'').'">';
			print $objprojtd->LibStatut($objdata->statut,1);
			if ($object->statut >= 0) print '</a>';
			print '</td>';

			// Icon update et delete
			print '<td align="center" nowrap>';
			if ($user->rights->projet->creer && $object->fk_statut == 0)
			  {
			    print '&nbsp;';
			    print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=deleteline&lineid='.$objdata->id.($withproject?'&withproject=1':'').'">';
			    print img_delete();
			    print '</a>';
			  }
			print '</td>';
			
			print "</tr>\n";
		      }
		  }
		print "</table>";

	}
	else
	{
		print "ErrorRecordNotFound";
	}
}


llxFooter();

$db->close();
