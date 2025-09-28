<?php
/* Copyright (C) 2010 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2012 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	\file       htdocs/projet/note.php
 *	\ingroup    project
 *	\brief      Fiche d'information sur un projet
 */

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projectext.class.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/utils.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/verifcontact.lib.php';
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projethistorial.class.php';

//para envio email
require_once DOL_DOCUMENT_ROOT.'/core/lib/emailing.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';
//require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/comm/mailing/class/mailing.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';


$langs->load('projects');

$action=GETPOST('action');
$subaction=GETPOST('subaction');
$id = GETPOST('id','int');
$ref= GETPOST('ref');

$mine = $_REQUEST['mode']=='mine' ? 1 : 0;
//if (! $user->rights->projet->all->lire) $mine=1;	// Special for projects

$object = new Projectext($db);
$objecth = new Projethistorial($db);

include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once

// Security check
$socid=0;
if ($user->societe_id > 0) $socid=$user->societe_id;
$result = restrictedArea($user, 'projet', $id);

$permissionnote=$user->rights->projet->creer;	// Used by the include of actions_setnotes.inc.php


/*
 * Actions
 */

include DOL_DOCUMENT_ROOT.'/core/actions_setnotes.inc.php';	// Must be include, not includ_once

//preparamos el envio de correo
if ($subaction == 'send_public')
  {
    $error = 0;
    $to = '';
    $aVerif = verifcontactprojet($user,$object,$ret='res');
    foreach ($aVerif[3] AS $j => $aEmail)
      {
	foreach ($aEmail AS $email)
	  {
	    if (!empty($to)) $to.= ',';
	    $to.= $email;
	  }
      }
    $db->begin();
    //preparamos el registro como historial
    $objecth->fk_projet = $object->id;
    $objecth->fk_task = 0;
    $objecth->email_send = $user->email;
    $objecth->emails = $to;
    $objecth->message = GETPOST('subject').': '.$user->lastname.' '.$user->firstname.' '.$langs->trans('Send').': '.GETPOST('note_public');
    $objecth->fk_user_create = $user->id;
    $objecth->date_create = dol_now();
    $objecth->tms = dol_now();
    $objecth->statut = 1;
    $res = $objecth->create($user);
    if (!$res>0)
      $error++;
    if (!$error)
      {
	$from = $user->email;
	//$to = 'ramiroques@gmail.com';
	$subject = GETPOST('subject');
	$body = $user->lastname.' '.$user->firstname.': '.GETPOST('note_public');
	if ($conf->global->MONPROJET_MESSAGE_SENDMAIL)
	  {
	    $aRes = send_email($from,$to,$subject,$body);
	    $res = $aRes[0];
	    if (!$res>0)
	      $error++;
	  }
      }
    if (!$error)
      $db->commit();
    else
      $db->rollback();
    unset($_POST);
  }
/*
 * View
 */

$title=$langs->trans("Project").' - '.$langs->trans("Note").' - '.$object->ref.' '.$object->name;
if (! empty($conf->global->MAIN_HTML_TITLE) && preg_match('/projectnameonly/',$conf->global->MAIN_HTML_TITLE) && $object->name) $title=$object->ref.' '.$object->name.' - '.$langs->trans("Note");
$help_url="EN:Module_Projects|FR:Module_Projets|ES:M&oacute;dulo_Proyectos";
llxHeader("",$title,$help_url);

$form = new Form($db);
$userstatic=new User($db);

$now=dol_now();

if ($id > 0 || ! empty($ref))
{
	// To verify role of users
	//$userAccess = $object->restrictedProjectArea($user,'read');
	$userWrite  = $object->restrictedProjectArea($user,'write');
	//$userDelete = $object->restrictedProjectArea($user,'delete');
	//print "userAccess=".$userAccess." userWrite=".$userWrite." userDelete=".$userDelete;

	$head = project_prepare_head($object);
	dol_fiche_head($head, 'notess', $langs->trans('Project'), 0, ($object->public?'projectpub':'project'));

	print '<table class="border" width="100%">';

	$linkback = '<a href="'.DOL_URL_ROOT.'/projet/list.php">'.$langs->trans("BackToList").'</a>';

	// Ref
	print '<tr><td width="30%">'.$langs->trans("Ref").'</td><td>';
	// Define a complementary filter for search of next/prev ref.
	if (! $user->rights->projet->all->lire)
	{
		$projectsListId = $object->getProjectsAuthorizedForUser($user,$mine,0);
		$object->next_prev_filter=" rowid in (".(count($projectsListId)?join(',',array_keys($projectsListId)):'0').")";
	}
	print $form->showrefnav($object, 'ref', $linkback, 1, 'ref', 'ref');
	print '</td></tr>';

	// Label
	print '<tr><td>'.$langs->trans("Label").'</td><td>'.$object->title.'</td></tr>';

	// Third party
	print '<tr><td>'.$langs->trans("ThirdParty").'</td><td>';
	if ($object->thirdparty->id > 0) print $object->thirdparty->getNomUrl(1);
	else print'&nbsp;';
	print '</td></tr>';

	// Visibility
	print '<tr><td>'.$langs->trans("Visibility").'</td><td>';
	if ($object->public) print $langs->trans('SharedProject');
	else print $langs->trans('PrivateProject');
	print '</td></tr>';

	// Statut
	print '<tr><td>'.$langs->trans("Status").'</td><td>'.$object->getLibStatut(4).'</td></tr>';

	// Date start
	print '<tr><td>'.$langs->trans("DateStart").'</td><td>';
	print dol_print_date($object->date_start,'day');
	print '</td></tr>';

	// Date end
	print '<tr><td>'.$langs->trans("DateEnd").'</td><td>';
	print dol_print_date($object->date_end,'day');
	print '</td></tr>';

	print "</table>";

	print '<br>';

	$colwidth=30;
	include DOL_DOCUMENT_ROOT.'/core/tpl/notes.tpl.php';

	dol_fiche_end();

	//incluimos el envio de notas por correo
	if ($user->rights->monprojet->notep->send && $action != 'list')
	  {
	    dol_fiche_head();
	    include DOL_DOCUMENT_ROOT.'/monprojet/tpl/sendnote.tpl.php';
	    dol_fiche_end();
	    /*
	     * Actions
	     */
	    print '<div class="tabsAction">';
	    print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=list'.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('Viewmessages').'</a>';
	    
	    print '</div>';

	  }
	//ver el historial
	if ($action == 'list')
	  {
	    $filter = array(1=>1);
	    $filterstatic = " AND fk_projet = ".$object->id;
	    $filterstatic = " AND fk_task = 0";
	    $res = $objecth->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic);
	    if ($res>0)
	      {
		dol_fiche_head();
		print '<table class="liste" width="100%">';
		
		print '<tr class="liste_titre">';
		print_liste_field_titre($langs->trans("Date"),$_SERVER["PHP_SELF"],"p.ref","",$param,"",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("From"),$_SERVER["PHP_SELF"],"p.ref","",$param,"",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("To"),$_SERVER["PHP_SELF"],"p.title","",$param,"",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Message"),$_SERVER["PHP_SELF"],"s.nom","",$param,"",$sortfield,$sortorder);
		print '</tr>';
		$var = true;
		foreach ($objecth->lines AS $j => $obj)
		  {
		    $var = !$var;
		    print "<tr $b[$var]>";
		    print '<td>';
		    print dol_print_date($obj->date_create,'day');
		    print '</td>';
		    print '<td>';
		    print $obj->email_send;
		    print '</td>';
		    print '<td>';
		    print $obj->emails;
		    print '</td>';
		    print '<td>';
		    print $obj->message;
		    print '</td>';
		    print '</tr>';
		  }

		print '</table>';
		dol_fiche_end();
	      }
	    /*
	     * Actions
	     */
	    print '<div class="tabsAction">';
	    print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.$param.'&backtopage='.urlencode($_SERVER['PHP_SELF'].'?id='.$object->id).'">'.$langs->trans('Sendmessage').'</a>';
	    
	    print '</div>';

	  }
 }

llxFooter();

$db->close();
