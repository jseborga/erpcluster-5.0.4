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
 *   	\file       orgman/tablesdefdet_card.php
 *		\ingroup    orgman
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-03-27 10:44
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

$search_fk_table_def=GETPOST('search_fk_table_def','int');
$search_ref=GETPOST('search_ref','alpha');
$search_label=GETPOST('search_label','alpha');
$search_description=GETPOST('search_description','alpha');
$search_range_ini=GETPOST('search_range_ini','int');
$search_range_fin=GETPOST('search_range_fin','int');
$search_active=GETPOST('search_active','int');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_status=GETPOST('search_status','int');



if (empty($action) && empty($id) && empty($ref)) $action='view';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
//$result = restrictedArea($user, 'orgman', $id);


$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($objectdet->table_element);

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('tablesdefdet'));




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/


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
	print '<input type="hidden" name="id" value="'.$id.'">';
	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_table_def").'</td><td><input class="flat" type="text" name="fk_table_def" value="'.GETPOST('fk_table_def').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.GETPOST('ref').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.GETPOST('label').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fielddescription").'</td><td><input class="flat" type="text" name="description" value="'.GETPOST('description').'"></td></tr>';
	if ($object->with_limit)
	{
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrange_ini").'</td><td><input class="flat" type="text" name="range_ini" value="'.GETPOST('range_ini').'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrange_fin").'</td><td><input class="flat" type="text" name="range_fin" value="'.GETPOST('range_fin').'"></td></tr>';
	}
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td>';
	print $form->selectyesno('active',GETPOST('active'),1);
	print '</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.GETPOST('fk_user_create').'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.GETPOST('fk_user_mod').'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.GETPOST('status').'"></td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}



// Part to edit record
if ($idr && $action == 'edit')
{
	print load_fiche_titre($langs->trans("MyModule"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$objectdet->fk_table_def.'">';
	print '<input type="hidden" name="idr" value="'.$objectdet->id.'">';
	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_table_def").'</td><td><input class="flat" type="text" name="fk_table_def" value="'.$objectdet->fk_table_def.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.(GETPOST('ref')?GETPOST('ref'):$objectdet->ref).'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.(GETPOST('label')?GETPOST('label'):$objectdet->label).'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fielddescription").'</td><td><input class="flat" type="text" name="description" value="'.(GETPOST('description')?GETPOST('description'):$objectdet->description).'"></td></tr>';
	if ($object->with_limit)
	{
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrange_ini").'</td><td><input class="flat" type="text" name="range_ini" value="'.(GETPOST('range_ini')?GETPOST('range_ini'):$objectdet->range_ini).'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrange_fin").'</td><td><input class="flat" type="text" name="range_fin" value="'.(GETPOST('range_fin')?GETPOST('range_fin'):$objectdet->range_fin).'"></td></tr>';
	}
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td>';
	print $form->selectyesno('active',(GETPOST('active')?GETPOST('active'):$objectdet->active),1);
	print '</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.$objectdet->fk_user_create.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.$objectdet->fk_user_mod.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.$objectdet->status.'"></td></tr>';

	print '</table>';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}



// Part to show record
if ($objectdet->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create')))
{
	$res = $objectdet->fetch_optionals($objectdet->id, $extralabels);


	print load_fiche_titre($langs->trans("Tablesdefdet"));

	dol_fiche_head();

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $objectdet->fk_table_def.'&idr='.$objectdet->id, $langs->trans('DeleteTabledefdet'), $langs->trans('ConfirmDeleteTabledefdet'), 'confirm_delete', '', 0, 2);
		print $formconfirm;
	}

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>'.$objectdet->label.'</td></tr>';
	//
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_table_def").'</td><td>'.$objectdet->fk_table_def.'</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td>'.$objectdet->ref.'</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td>'.$objectdet->label.'</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fielddescription").'</td><td>'.$objectdet->description.'</td></tr>';
	if ($object->with_limit)
	{
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrange_ini").'</td><td>'.$objectdet->range_ini.'</td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrange_fin").'</td><td>'.$objectdet->range_fin.'</td></tr>';
	}
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td>'.($objectdet->active?$langs->trans('Yes'):$langs->trans('Not')).'</td></tr>';
	$objUser->fetch($objectdet->fk_user_create);
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td>'.$objUser->getNomUrl(1).'</td></tr>';
	$objUser->fetch($objectdet->fk_user_mod);
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td>'.$objUser->getNomUrl(1).'</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td>'.$objectdet->getLibStatut(6).'</td></tr>';

	print '</table>';

	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$objectdet,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		print '<a class="butAction" href="' . dol_buildpath('/orgman/tables/list.php',1).'">' . $langs->trans('Return') . '</a>';

		if ($object->status==0)
		{
		if ($user->rights->orgman->tab->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$objectdet->fk_table_def.'&idr='.$objectdet->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->orgman->tab->del)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$objectdet->fk_table_def.'&idr='.$objectdet->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
		}
		}
	}
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	// Show links to link elements
	//$linktoelem = $form->showLinkToObjectBlock($object, null, array('tablesdefdet'));
	//$somethingshown = $form->showLinkedObjectBlock($object, $linktoelem);

}


// End of page
llxFooter();
$db->close();
