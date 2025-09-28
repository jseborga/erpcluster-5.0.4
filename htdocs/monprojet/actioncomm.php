<?php
/* Copyright (C) 2005      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
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
 *	\file       htdocs/projet/ganttview.php
 *	\ingroup    projet
 *	\brief      Gantt diagramm of a project
 */


require ("../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
//require_once DOL_DOCUMENT_ROOT.'/monprojet/class/html.formfile.class.php';

require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskbase.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskdepends.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettasktimedoc.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/taskext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/taskv.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/contratadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/guarantees.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskcontrat.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projectext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projetpayment.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projetpaymentdeduction.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskpayment.class.php';

require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/monprojet.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/utils.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/dict.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/doc.lib.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

$objuser = new User($db);

$id=GETPOST('projectid','int');
$action = GETPOST('action','alpha');
//action
if ($action == 'add')
{
	$object = new Projectext($db);
	$object->fetch($id);
	$aUser = GETPOST('aUser');
	foreach($aUser as $key => $value)
	{
		if ($value)
		{
			$aArray[$key] = $key;
		}
	}
	if (count($aArray))
		$_SESSION['aUseraction'] = serialize($aArray);
	else
		unset($_SESSION['aUseraction']);
	header('location: '.DOL_URL_ROOT.'/comm/action/card.php?action=create&socid='.$object->socid.'&projectid='.$object->id.'&backtopage=1&percentage=1');
	exit;
}


/*
 * View
 */
if ($action == 'create')
{
	$form=new Form($db);
	$formother     = new FormOther($db);

	$projectstatic = new Projectext($db);
	$projectstatic->fetch($id);
	$userWrite  = $projectstatic->restrictedProjectAreaadd($user,'write');
	foreach (array('internal', 'external') as $source)
	{
		$userRole = $projectstatic->liste_contact(4, $source);
		$num = count($userRole);
		$nblinks = 0;
		while ($nblinks < $num)
		{
			$aUser[$userRole[$nblinks]['id']] = $userRole[$nblinks]['lastname'].' '.$userRole[$nblinks]['firstname'];
			$nblinks++;
		}
	}

	$arrayofcss=array('/monprojet/css/style.css','/monprojet/css/jsgantt.css');
	$help_url="EN:Module_Projects|FR:Module_Projets|ES:M&oacute;dulo_Proyectos";
	llxHeader("",$langs->trans("Tasks"),$help_url,'',0,0,$arrayofjs,$arrayofcss);

//verificamos los items que contiene
	$modetask = 0;


	dol_fiche_head();


	$head=project_prepare_head($projectstatic);
	dol_fiche_head($head, $tab, $langs->trans("Project"),0,($projectstatic->public?'projectpub':'project'));

	$param=($mode=='mine'?'&mode=mine':'');

	print '<table class="border" width="100%">';

	//$linkback = '<a href="'.DOL_URL_ROOT.'/monprojet/list.php">'.$langs->trans("BackToList").'</a>';

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
	//print $form->showrefnav($projectstatic, 'ref', $linkback, 1, 'ref', 'ref', '', $param);
	print $projectstatic->ref;
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("Label").'</td><td>'.$projectstatic->title.'</td></tr>';

	print '</table>';
	print '<br>';
	dol_fiche_end();
	print load_fiche_titre($langs->trans('Seleccione los contactos'));
	dol_fiche_head();

	print '<form name="crea_comm" action="' . $_SERVER["PHP_SELF"] . '?id='.$id.'" method="POST">';
	print '<input type="hidden" name="token" value="' . $_SESSION ['newtoken'] . '">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="projectid" value="' . $projectstatic->id . '">' . "\n";

	print '<table class="border centpercent">';

	if ($id)
	{
		foreach ($aUser AS $i => $value)
		{
			print '<tr>';
			print '<td>'.$value.'</td>';
			print '<td>'.'<input type="checkbox" name="aUser['.$i.']">'.'</td>';
			print '</tr>';
		}
		print '<tr>';
		print '<td>'.'<input type="submit" class="butAction" name="" value="'.$langs->trans('Crear evento').'">'.'</td>';
		print '</tr>';
	}

	print '</table>';
	dol_fiche_end();
}

llxFooter();

$db->close();
