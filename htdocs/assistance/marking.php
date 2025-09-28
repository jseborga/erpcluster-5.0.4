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
 *   	\file       /assistancedef_page.php
 *		\ingroup
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2015-10-12 13:25
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

// Change this following line to use the correct relative path (../, ../../, etc)
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
dol_include_once('/assistance/class/assistancedef.class.php');
dol_include_once('/assistance/class/typemarkingext.class.php');
dol_include_once('/adherents/class/adherent.class.php');
dol_include_once('/adherents/class/adherent_type.class.php');
require_once DOL_DOCUMENT_ROOT.'/core/lib/member.lib.php';

/******************************************************/
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';


require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';


require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

dol_include_once('/assistance/class/html.formadd.class.php');
dol_include_once('/assistance/class/typemarkingext.class.php');
dol_include_once('/assistance/lib/assistance.lib.php');
dol_include_once('/assistance/lib/utils.lib.php');

require_once(DOL_DOCUMENT_ROOT."/orgman/class/pdepartamentext.class.php");
require_once(DOL_DOCUMENT_ROOT."/orgman/class/csources.class.php");
require_once(DOL_DOCUMENT_ROOT."/orgman/class/cpartida.class.php");
require_once(DOL_DOCUMENT_ROOT."/orgman/class/partidaproductext.class.php");

dol_include_once('/assistance/class/adherentext.class.php');
dol_include_once('/assistance/class/assistanceext.class.php');
dol_include_once('/assistance/class/puser.class.php');
dol_include_once('/assistance/class/licencesext.class.php');
dol_include_once('/assistance/class/assistancedef.class.php');
require_once DOL_DOCUMENT_ROOT.'/core/lib/member.lib.php';

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");
$langs->load("members");

// Get parameters
$idd = GETPOST('idd','int');
$id		= GETPOST('rowid','int');//id del member o contact
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


// Protection if external user
if ($user->societe_id > 0)
{
	accessforbidden();
}

// Load object if id or ref is provided as parameter
$object=new Assistancedef($db);
//revisamos por el id member
if ($id>0)
{
	$result=$object->fetchall('','',1,0,array('fk_reg'=>$id,'statut'=>1));
	if ($result < 0)
	{
		dol_print_error($db);
	}
	else
	{
		foreach($object->lines AS $j => $obj)
		{
			if ($obj->fk_reg == $id && $obj->statut == 1 && $obj->type_reg == 'm')
				$idd = $obj->id;
		}
		if($idd && empty($action))
			$action='view';
	}
}

if (($idd > 0) && $action != 'add')
{
	$result=$object->fetch($idd);
	if ($result < 0)
		dol_print_error($db);
	else
	{
		$id = $object->fk_reg;
		if(empty($action))
			$action= 'view';
	}
}

if (empty($action) && empty($idd)) $action='create';

$typemarking = new Typemarkingext($db);
$adherent=new Adherent($db);
$membert=new AdherentType($db);

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('assistancedef'));
$extrafields = new ExtraFields($db);


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
	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/assistance/marking.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */

		$object->fk_reg=GETPOST('rowid','int');
		$object->type_reg='m';
		$object->type_marking=GETPOST('type_marking','alpha');
		$aditional_time=GETPOST('aditional_time','int');
		if ($aditional_time>0)
			$object->aditional_time=GETPOST('aditional_time','int');
		$object->fk_user_create=$user->id;
		$object->fk_user_mod=$user->id;
		$object->date_create=dol_now();
		$object->tms=dol_now();
		$object->statut=1;

		if (empty($object->type_marking))
		{
			$error++;
			setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Typemarking")),'errors');
		}

		if (! $error)
		{
			$result=$object->create($user);
			if ($result > 0)
			{
		// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/assistance/marking.php?idd='.$result,1);
				header("Location: ".$urltogo);
				exit;
			}
			{
		  // Creation KO
				if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else  setEventMessages($object->error, null, 'errors');
				$action='create';
			}
		}
		else
		{
			$action='create';
		}
	}

	// Cancel
	if ($action == 'update' && GETPOST('cancel')) $action='view';

	// Action to update record
	if ($action == 'update' && ! GETPOST('cancel'))
	{
		$error=0;

		$object->type_marking=GETPOST('type_marking','alpha');
		$aditional_time=GETPOST('aditional_time','int');
		if ($aditional_time>0)
			$object->aditional_time=GETPOST('aditional_time','int');
		$object->fk_user_mod=$user->id;
		$object->tms = dol_now();
		$object->statut=1;

		if (empty($object->type_marking))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Typemarking")),null,'errors');
		}

		if (! $error)
		{
			$result=$object->update($user);
			if ($result > 0)
			{
				$action='view';
			}
			else
			{
		// Creation KO
				if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else setEventMessages($object->error, null, 'errors');
				$action='edit';
			}
		}
		else
		{
			$action='edit';
		}
	}

	// Action to delete
	if ($action == 'confirm_delete' && $user->rights->assistance->def->del)
	{
		$result=$object->delete($user);
		if ($result > 0)
		{
		// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/assistence/marking.php?id='.$id,1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null,$object->errors,'errors');
			else setEventMessages($object->error,null,'errors');
		}
	}
}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('',$langs->trans('Marking'),'');

$form=new Form($db);


// Put here content of your page

// Example : Adding jquery code
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	function init_myfunc()
	{
		jQuery("#myid").removeAttr(\'disabled\');
		jQuery("#myid").attr(\'disabled\',\'disabled\');
	}
	init_myfunc();
	jQuery("#mybutton").click(function() {
		init_myfunc();
	});
});
</script>';


//mostramos al miembro
if ($id>0)
{
	$adherent->fetch($id);
	$result=$membert->fetch($adherent->typeid);
	if ($result > 0)
	{
	/*
	 * Affichage onglets
	 */
	if (! empty($conf->notification->enabled))
		$langs->load("mails");

	$head = member_prepare_head($adherent);

	$form=new Form($db);

	dol_fiche_head($head, 'marking', $langs->trans("Member"),0,'user');

	print '<table class="border" width="100%">';

	$linkback = '<a href="'.DOL_URL_ROOT.'/adherents/list.php">'.$langs->trans("BackToList").'</a>';

		// Ref
	print '<tr><td width="20%">'.$langs->trans("Ref").'</td>';
	print '<td class="valeur">';
	print $form->showrefnav($adherent, 'rowid', $linkback);
	print '</td></tr>';

		// Login
	if (empty($conf->global->ADHERENT_LOGIN_NOT_REQUIRED))
	{
		print '<tr><td>'.$langs->trans("Login").' / '.$langs->trans("Id").'</td><td class="valeur">'.$adherent->login.'&nbsp;</td></tr>';
	}

		// Morphy
	print '<tr><td>'.$langs->trans("Nature").'</td><td class="valeur" >'.$adherent->getmorphylib().'</td>';
		/*print '<td rowspan="'.$rowspan.'" align="center" valign="middle" width="25%">';
	 print $form->showphoto('memberphoto',$object);
	 print '</td>';*/
	 print '</tr>';

		// Type
	 print '<tr><td>'.$langs->trans("Type").'</td><td class="valeur">'.$membert->getNomUrl(1)."</td></tr>\n";

		// Company
	 print '<tr><td>'.$langs->trans("Company").'</td><td class="valeur">'.$adherent->societe.'</td></tr>';

		// Civility
	 print '<tr><td>'.$langs->trans("UserTitle").'</td><td class="valeur">'.$adherent->getCivilityLabel().'&nbsp;</td>';
	 print '</tr>';

		// Lastname
	 print '<tr><td>'.$langs->trans("Lastname").'</td><td class="valeur">'.$adherent->lastname.'&nbsp;</td>';
	 print '</tr>';

		// Firstname
	 print '<tr><td>'.$langs->trans("Firstname").'</td><td class="valeur">'.$adherent->firstname.'&nbsp;</td>';
	 print '</tr>';

		// Status
	 print '<tr><td>'.$langs->trans("Status").'</td><td class="valeur">'.$adherent->getLibStatut(4).'</td></tr>';

	 print '</table>';
	 dol_fiche_end();
	}



	// Part to create
	if ($action == 'create' && $user->rights->assistance->mark->write)
	{
		print_fiche_titre($langs->trans("New"));

		print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
		print '<input type="hidden" name="action" value="add">';
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
		print '<input type="hidden" name="rowid" value="'.$id.'">';

		dol_fiche_head();

		print '<table class="border centpercent">'."\n";
		print '<tr><td class="fieldrequired" width="20%">'.$langs->trans("Typemarking").'</td><td>';
		print $typemarking->select_typemarking($type_marking,'type_marking','',35,1,0,'required','','ref','detail');
		print '</td></tr>';

		print '<tr><td class="fieldrequired">'.$langs->trans("Aditionaltime").'</td><td>';
		print '<input type="number" min="0" max="60" name="aditional_time" value="'.$aditional_time.'">'.' '.$langs->trans('Minutes');
		print '</td></tr>';

		print '</table>'."\n";

		dol_fiche_end();

		print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'">';
	//print '&nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

		print '</form>';
	}

	// Part to edit record
	if (($id && $idd) && $action == 'edit' && $user->rights->assistance->mark->write)
	{

		print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';

		dol_fiche_head();

		print '<input type="hidden" name="action" value="update">';
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
		print '<input type="hidden" name="rowid" value="'.$id.'">';
		print '<input type="hidden" name="idd" value="'.$object->id.'">';

		print '<table class="border centpercent">'."\n";
		print '<tr><td class="fieldrequired" width="20%">'.$langs->trans("Typemarking").'</td><td>';
		print $typemarking->select_typemarking($object->type_marking,'type_marking','',35,1,0,'required','','ref','detail');
		print '</td></tr>';

		print '<tr><td class="fieldrequired">'.$langs->trans("Aditionaltime").'</td><td>';
		print '<input type="number" min="0" step="1" max="60" name="aditional_time" value="'.$object->aditional_time.'">'.' '.$langs->trans('Minutes');
		print '</td></tr>';
		print '</table>';

		dol_fiche_end();

		print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Save").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

		print '</form>';
	}

	// Part to show record
	if (($id && $idd) && (empty($action) || $action == 'view'))
	{

		dol_fiche_head();

		$res = $typemarking->fetch('',$object->type_marking);
		print '<table class="border centpercent">'."\n";
		print '<tr><td width="20%">'.$langs->trans("Typemarking").'</td><td>';
		print $typemarking->detail;
		print '</td></tr>';
		print '<tr><td width="20%">'.$langs->trans("Mark").'</td><td>';
		print $typemarking->mark;
		print '</td></tr>';
		print '<tr><td>'.$langs->trans("Aditionaltime").'</td><td>';
		print (is_null($object->aditional_time)?$typemarking->additional_time:$object->aditional_time).' '.$langs->trans('Minutes');
		print '</td></tr>';

		print '</table>';

		dol_fiche_end();


	// Buttons
		print '<div class="tabsAction">'."\n";
		$parameters=array();
		$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);
	// Note that $action and $object may have been modified by hook
		if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

		if (empty($reshook))
		{
			if ($user->rights->assistance->def->mod)
			{
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?idd='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
			}

			if ($user->rights->assistance->mark->del)
			{
				if ($conf->use_javascript_ajax && empty($conf->dol_use_jmobile))
				{
					print '<div class="inline-block divButAction"><span id="action-delete" class="butActionDelete">'.$langs->trans('Delete').'</span></div>'."\n";
				}
				else
				{
					print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
				}
			}
		}
		print '</div>'."\n";

		//el archivo membermarking_list.tpl.php  debe listar las marcaciones del miembro revisadas y aprobadas y debe contener las columnas
		//fecha
		//primera entrada
		//primera salida
		//segunda entrada
		//segunda salida
		//mostrando los retrasos o abandonos segun corresponda
		//Tomar como ejemplo el archivo /assistance/assistance/list.php

	}
	dol_fiche_head();
	include DOL_DOCUMENT_ROOT.'/assistance/tpl/membermarking_list.tpl.php';
	dol_fiche_end();
}

// End of page
llxFooter();
$db->close();
