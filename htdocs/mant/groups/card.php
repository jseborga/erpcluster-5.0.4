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
 *   	\file       mant/mgroups_card.php
 *		\ingroup    mant
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-04-24 14:28
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
dol_include_once('/mant/class/mgroups.class.php');
dol_include_once('/mant/lib/mant.lib.php');

// Load traductions files requiredby by page
$langs->load("mant");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$cancel     = GETPOST('cancel');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_fk_asset_group=GETPOST('search_fk_asset_group','int');
$search_label=GETPOST('search_label','alpha');
$search_description=GETPOST('search_description','alpha');
$search_useful_life=GETPOST('search_useful_life','alpha');
$search_percent=GETPOST('search_percent','alpha');
$search_account_accounting=GETPOST('search_account_accounting','alpha');
$search_account_spending=GETPOST('search_account_spending','alpha');
$search_active=GETPOST('search_active','int');



if (empty($action) && empty($id) && empty($ref)) $action='view';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
//$result = restrictedArea($user, 'mant', $id);


$object = new Mgroups($db);
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('mgroups'));



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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/mant/groups/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}		
		if ($id > 0 || ! empty($ref)) $ret = $object->fetch($id,$ref);
		$action='';
	}
	
	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/mant/groups/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */
		
		$object->entity=GETPOST('entity','int');
		$object->ref=GETPOST('ref','alpha');
		$object->fk_asset_group=GETPOST('fk_asset_group','int');
		$object->label=GETPOST('label','alpha');
		$object->description=GETPOST('description','alpha');
		$object->useful_life=GETPOST('useful_life','alpha');
		$object->percent=GETPOST('percent','alpha');
		$object->account_accounting=GETPOST('account_accounting','alpha');
		$object->account_spending=GETPOST('account_spending','alpha');
		$object->active=GETPOST('active','int');
		$object->fk_user_create = $user->id;
		$object->fk_user_mod = $user->id;
		$object->datec = dol_now();
		$object->datem = dol_now();
		$object->tms = dol_now();
		

		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$object->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/mant/groups/card.php?id='.$result,1);
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

	// Action to update record
	if ($action == 'update')
	{
		$error=0;

		
		$object->ref=GETPOST('ref','alpha');
		$object->fk_asset_group=GETPOST('fk_asset_group','int')+0;
		$object->label=GETPOST('label','alpha');
		$object->description=GETPOST('description','alpha');
		$object->useful_life=GETPOST('useful_life','alpha')+0;
		$object->percent=GETPOST('percent','alpha')+0;
		$object->account_accounting=GETPOST('account_accounting','alpha');
		$object->account_spending=GETPOST('account_spending','alpha');
		$object->active=GETPOST('active','int');
		$object->fk_user_mod = $user->id;
		$object->datem = dol_now();
		$object->tms = dol_now();
		

		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
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
	if ($action == 'confirm_delete')
	{
		$result=$object->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/mant/groups/list.php',1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else setEventMessages($object->error, null, 'errors');
		}
	}
}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('','MyPageName','');

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


// Part to create
if ($action == 'create')
{
	print load_fiche_titre($langs->trans("NewMyModule"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.GETPOST('ref').'"></td></tr>';
	if ($conf->assets->enabled)
		print '<tr><td>'.$langs->trans("Fieldfk_asset_group").'</td><td><input class="flat" type="text" name="fk_asset_group" value="'.GETPOST('fk_asset_group').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.GETPOST('label').'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fielddescription").'</td><td><input class="flat" type="text" name="description" value="'.GETPOST('description').'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fielduseful_life").'</td><td><input class="flat" type="text" name="useful_life" value="'.GETPOST('useful_life').'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldpercent").'</td><td><input class="flat" type="text" name="percent" value="'.GETPOST('percent').'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldaccount_accounting").'</td><td><input class="flat" type="text" name="account_accounting" value="'.GETPOST('account_accounting').'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldaccount_spending").'</td><td><input class="flat" type="text" name="account_spending" value="'.GETPOST('account_spending').'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldactive").'</td><td>';
	print $form->selectyesno('active',GETPOST('active'),1);
	print '</td></tr>';

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
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	
	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.$object->ref.'" required></td></tr>';
	if ($conf->assets->enabled)
		print '<tr><td>'.$langs->trans("Fieldfk_asset_group").'</td><td><input class="flat" type="text" name="fk_asset_group" value="'.$object->fk_asset_group.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.$object->label.'" required></td></tr>';
	print '<tr><td>'.$langs->trans("Fielddescription").'</td><td><input class="flat" type="text" name="description" value="'.$object->description.'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fielduseful_life").'</td><td><input class="flat" type="text" name="useful_life" value="'.$object->useful_life.'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldpercent").'</td><td><input class="flat" type="text" name="percent" value="'.$object->percent.'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldaccount_accounting").'</td><td><input class="flat" type="text" name="account_accounting" value="'.$object->account_accounting.'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldaccount_spending").'</td><td><input class="flat" type="text" name="account_spending" value="'.$object->account_spending.'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldactive").'</td><td>';
	print $form->selectyesno('active',$object->active,1);
	print '</td></tr>';

	print '</table>';
	
	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}



// Part to show record
if ($object->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create')))
{
	$res = $object->fetch_optionals($object->id, $extralabels);

	$head = groups_prepare_head($object);
	dol_fiche_head($head, 'order', $langs->trans("Groups"), 0, 'group');
	
	print load_fiche_titre($langs->trans("Mant"));
	
	dol_fiche_head();

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('DeleteMyOjbect'), $langs->trans('ConfirmDeleteMyObject'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}
	
	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>'.$object->label.'</td></tr>';
	// 
	print '<tr><td>'.$langs->trans("Fieldref").'</td><td>'.$object->ref.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldfk_asset_group").'</td><td>'.$object->fk_asset_group.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldlabel").'</td><td>'.$object->label.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fielddescription").'</td><td>'.$object->description.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fielduseful_life").'</td><td>'.$object->useful_life.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldpercent").'</td><td>'.$object->percent.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldaccount_accounting").'</td><td>'.$object->account_accounting.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldaccount_spending").'</td><td>'.$object->account_spending.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldactive").'</td><td>'.($object->active?img_picto('','switch_on'):img_picto('','switch_off')).'</td></tr>';

	print '</table>';
	
	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->mant->group->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->mant->group->del)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
		}
	}
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	// Show links to link elements
	//$linktoelem = $form->showLinkToObjectBlock($object, null, array('mgroups'));
	//$somethingshown = $form->showLinkedObjectBlock($object, $linktoelem);

}


// End of page
llxFooter();
$db->close();
