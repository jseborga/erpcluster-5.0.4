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
 *   	\file       orgman/tablesdef_card.php
 *		\ingroup    orgman
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-03-23 17:02
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

include_once DOL_DOCUMENT_ROOT.'/orgman/lib/orgman.lib.php';
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
dol_include_once('/orgman/class/tablesdefext.class.php');
dol_include_once('/orgman/class/tablesdefdetext.class.php');
dol_include_once('/user/class/user.class.php');


// Load traductions files requiredby by page
$langs->load("orgman");
$langs->load("other");

// Get parameters
$id			= GETPOST('id');
$idr 		= GETPOST('idr', 'int');
$action		= GETPOST('action','alpha');
$cancel     = GETPOST('cancel');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');

/*

$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_label=GETPOST('search_label','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_active=GETPOST('search_active','int');
$search_status=GETPOST('search_status','int');
*/
$search_all=trim(GETPOST("sall"));

$search_entity=GETPOST('search_entity','int');
$search_tabledb=GETPOST('search_tabledb','alpha');
$search_codetable=GETPOST('search_codetable','int');
$search_ref=GETPOST('search_ref','alpha');
$search_label=GETPOST('search_label','alpha');
$search_description=GETPOST('search_description','alpha');
$search_type=GETPOST('search_type','alpha');
$search_range_ini=GETPOST('search_range_ini','int');
$search_range_fin=GETPOST('search_range_fin','int');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_active=GETPOST('search_active','int');







if (empty($action) && empty($id) && empty($ref)) $action='view';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
//$result = restrictedArea($user, 'orgman', $id);


$object = new Tablesdefext($db);
$objectdet = new Tablesdefdetext($db);
$extrafields = new ExtraFields($db);
$objUser = new User($db);

if($idr>0)
{
	$objectdet->fetch($idr);
}

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($objectdet->table_element);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('tablesdef'));



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/
$now = dol_now();
$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	if ($cancel)
	{
		if ($action != 'addlink')
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/orgman/tables/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}
		if ($idr > 0) $ret = $objectdet->fetch($idr);
		$action='';
	}

	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/orgman/tables/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */

		$objectdet->fk_table_def=$id;
		$objectdet->ref=GETPOST('ref','alpha');
		$objectdet->label=GETPOST('label','alpha');
		$objectdet->description=GETPOST('description','alpha');
		$objectdet->range_ini=GETPOST('range_ini','int');
		if (empty($objectdet->range_ini)) $objectdet->range_ini = 0;
		$objectdet->range_fin=GETPOST('range_fin','int');
		if (empty($objectdet->range_fin)) $objectdet->range_fin = 0;
		$objectdet->active=GETPOST('active','int');
		$objectdet->fk_user_create=$user->id;
		$objectdet->fk_user_mod=$user->id;
		$objectdet->datec = $now;
		$objectdet->datem = $now;
		$objectdet->datem = $now;
		$objectdet->status=1;



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
				$urltogo=$backtopage?$backtopage:dol_buildpath('/orgman/tables/carddet.php?id='.$id,1);
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


		$objectdet->fk_table_def=$id;
		$objectdet->ref=GETPOST('ref','alpha');
		$objectdet->label=GETPOST('label','alpha');
		$objectdet->description=GETPOST('description','alpha');
		$objectdet->range_ini=GETPOST('range_ini','int');
		if (empty($objectdet->range_ini)) $objectdet->range_ini = 0;
		$objectdet->range_fin=GETPOST('range_fin','int');
		if (empty($objectdet->range_fin)) $objectdet->range_fin = 0;
		$objectdet->active=GETPOST('active','int');
		$objectdet->fk_user_mod=$user->id;
		$objectdet->datem = $now;
		$objectdet->datem = $now;
		$objectdet->status=1;

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
			header("Location: ".dol_buildpath('/orgman/tables/carddet.php?id='.$id,1));
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

llxHeader('',$langs->trans('Specialtables'),'');

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

// Part to show record
if ($object->id > 0 )
{

	$res = $object->fetch_optionals($object->id, $extralabels);
	print load_fiche_titre($langs->trans("Specialtables"));
	$head = tables_prepare_head($object);


	dol_fiche_head($head, 'carddet', $langs->trans('Specialtables'), 0, 'setup');
	//dol_fiche_head();


	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>'.$object->label.'</td></tr>';
	//
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td>'.$object->entity.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldref").'</td><td>'.$object->ref.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldlabel").'</td><td>'.$object->label.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td>'.$object->fk_user_create.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td>'.$object->fk_user_mod.'</td></tr>';
	print '<tr><td>'.$langs->trans("Withlimit").'</td><td>'.($object->with_limit?$langs->trans('Yes'):$langs->trans('Not')).'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldactive").'</td><td>'.($object->active?$langs->trans('Yes'):$langs->trans('Not')).'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldstatus").'</td><td>'.$object->getLibStatut(6).'</td></tr>';


	print '</table>';

	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{

	}
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	// Show links to link elements
	//$linktoelem = $form->showLinkToObjectBlock($object, null, array('tablesdef'));
	//$somethingshown = $form->showLinkedObjectBlock($object, $linktoelem);



	if ($action == 'create' || $asction == 'edit' || $action == 'delete' || $action== 'add' || $idr>0 )
	{
		//include DOL_DOCUMENT_ROOT.'/orgman/tables/tpl/preportsalarydet_card.tpl.php';
		include DOL_DOCUMENT_ROOT.'/orgman/tables/tpl/tablesdefdet_card.tpl.php';

	}
	else
		//include DOL_DOCUMENT_ROOT.'/orgman/tables/tpl/preportsalarydet_list.tpl.php';
		include DOL_DOCUMENT_ROOT.'/orgman/tables/tpl/tablesdefdet_list.tpl.php';



}


// End of page
//llxFooter();
//$db->close();
