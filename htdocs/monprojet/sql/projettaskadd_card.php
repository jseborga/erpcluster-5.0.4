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
 *   	\file       monprojet/projettaskadd_card.php
 *		\ingroup    monprojet
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-09-19 08:47
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
dol_include_once('/monprojet/class/projettaskadd.class.php');

// Load traductions files requiredby by page
$langs->load("monprojet");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_fk_task=GETPOST('search_fk_task','int');
$search_fk_contrat=GETPOST('search_fk_contrat','int');
$search_c_grupo=GETPOST('search_c_grupo','int');
$search_level=GETPOST('search_level','int');
$search_c_view=GETPOST('search_c_view','int');
$search_fk_unit=GETPOST('search_fk_unit','int');
$search_fk_type=GETPOST('search_fk_type','int');
$search_fk_item=GETPOST('search_fk_item','int');
$search_unit_budget=GETPOST('search_unit_budget','int');
$search_unit_program=GETPOST('search_unit_program','alpha');
$search_unit_declared=GETPOST('search_unit_declared','alpha');
$search_unit_ejecuted=GETPOST('search_unit_ejecuted','alpha');
$search_unit_amount=GETPOST('search_unit_amount','alpha');
$search_detail_close=GETPOST('search_detail_close','alpha');
$search_order_ref=GETPOST('search_order_ref','int');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_statut=GETPOST('search_statut','int');



// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Projettaskadd($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('projettaskadd'));
$extrafields = new ExtraFields($db);



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
	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */
		
	$object->fk_task=GETPOST('fk_task','int');
	$object->fk_contrat=GETPOST('fk_contrat','int');
	$object->c_grupo=GETPOST('c_grupo','int');
	$object->level=GETPOST('level','int');
	$object->c_view=GETPOST('c_view','int');
	$object->fk_unit=GETPOST('fk_unit','int');
	$object->fk_type=GETPOST('fk_type','int');
	$object->fk_item=GETPOST('fk_item','int');
	$object->unit_budget=GETPOST('unit_budget','int');
	$object->unit_program=GETPOST('unit_program','alpha');
	$object->unit_declared=GETPOST('unit_declared','alpha');
	$object->unit_ejecuted=GETPOST('unit_ejecuted','alpha');
	$object->unit_amount=GETPOST('unit_amount','alpha');
	$object->detail_close=GETPOST('detail_close','alpha');
	$object->order_ref=GETPOST('order_ref','int');
	$object->fk_user_create=GETPOST('fk_user_create','int');
	$object->fk_user_mod=GETPOST('fk_user_mod','int');
	$object->statut=GETPOST('statut','int');

		

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
				$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/list.php',1);
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

		
	$object->fk_task=GETPOST('fk_task','int');
	$object->fk_contrat=GETPOST('fk_contrat','int');
	$object->c_grupo=GETPOST('c_grupo','int');
	$object->level=GETPOST('level','int');
	$object->c_view=GETPOST('c_view','int');
	$object->fk_unit=GETPOST('fk_unit','int');
	$object->fk_type=GETPOST('fk_type','int');
	$object->fk_item=GETPOST('fk_item','int');
	$object->unit_budget=GETPOST('unit_budget','int');
	$object->unit_program=GETPOST('unit_program','alpha');
	$object->unit_declared=GETPOST('unit_declared','alpha');
	$object->unit_ejecuted=GETPOST('unit_ejecuted','alpha');
	$object->unit_amount=GETPOST('unit_amount','alpha');
	$object->detail_close=GETPOST('detail_close','alpha');
	$object->order_ref=GETPOST('order_ref','int');
	$object->fk_user_create=GETPOST('fk_user_create','int');
	$object->fk_user_mod=GETPOST('fk_user_mod','int');
	$object->statut=GETPOST('statut','int');

		

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
			header("Location: ".dol_buildpath('/monprojet/list.php',1));
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
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_task").'</td><td><input class="flat" type="text" name="fk_task" value="'.GETPOST('fk_task').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_contrat").'</td><td><input class="flat" type="text" name="fk_contrat" value="'.GETPOST('fk_contrat').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldc_grupo").'</td><td><input class="flat" type="text" name="c_grupo" value="'.GETPOST('c_grupo').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlevel").'</td><td><input class="flat" type="text" name="level" value="'.GETPOST('level').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldc_view").'</td><td><input class="flat" type="text" name="c_view" value="'.GETPOST('c_view').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit").'</td><td><input class="flat" type="text" name="fk_unit" value="'.GETPOST('fk_unit').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_type").'</td><td><input class="flat" type="text" name="fk_type" value="'.GETPOST('fk_type').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_item").'</td><td><input class="flat" type="text" name="fk_item" value="'.GETPOST('fk_item').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldunit_budget").'</td><td><input class="flat" type="text" name="unit_budget" value="'.GETPOST('unit_budget').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldunit_program").'</td><td><input class="flat" type="text" name="unit_program" value="'.GETPOST('unit_program').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldunit_declared").'</td><td><input class="flat" type="text" name="unit_declared" value="'.GETPOST('unit_declared').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldunit_ejecuted").'</td><td><input class="flat" type="text" name="unit_ejecuted" value="'.GETPOST('unit_ejecuted').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldunit_amount").'</td><td><input class="flat" type="text" name="unit_amount" value="'.GETPOST('unit_amount').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddetail_close").'</td><td><input class="flat" type="text" name="detail_close" value="'.GETPOST('detail_close').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldorder_ref").'</td><td><input class="flat" type="text" name="order_ref" value="'.GETPOST('order_ref').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.GETPOST('fk_user_create').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.GETPOST('fk_user_mod').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatut").'</td><td><input class="flat" type="text" name="statut" value="'.GETPOST('statut').'"></td></tr>';

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
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_task").'</td><td><input class="flat" type="text" name="fk_task" value="'.$object->fk_task.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_contrat").'</td><td><input class="flat" type="text" name="fk_contrat" value="'.$object->fk_contrat.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldc_grupo").'</td><td><input class="flat" type="text" name="c_grupo" value="'.$object->c_grupo.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlevel").'</td><td><input class="flat" type="text" name="level" value="'.$object->level.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldc_view").'</td><td><input class="flat" type="text" name="c_view" value="'.$object->c_view.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit").'</td><td><input class="flat" type="text" name="fk_unit" value="'.$object->fk_unit.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_type").'</td><td><input class="flat" type="text" name="fk_type" value="'.$object->fk_type.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_item").'</td><td><input class="flat" type="text" name="fk_item" value="'.$object->fk_item.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldunit_budget").'</td><td><input class="flat" type="text" name="unit_budget" value="'.$object->unit_budget.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldunit_program").'</td><td><input class="flat" type="text" name="unit_program" value="'.$object->unit_program.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldunit_declared").'</td><td><input class="flat" type="text" name="unit_declared" value="'.$object->unit_declared.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldunit_ejecuted").'</td><td><input class="flat" type="text" name="unit_ejecuted" value="'.$object->unit_ejecuted.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldunit_amount").'</td><td><input class="flat" type="text" name="unit_amount" value="'.$object->unit_amount.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddetail_close").'</td><td><input class="flat" type="text" name="detail_close" value="'.$object->detail_close.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldorder_ref").'</td><td><input class="flat" type="text" name="order_ref" value="'.$object->order_ref.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.$object->fk_user_create.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.$object->fk_user_mod.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatut").'</td><td><input class="flat" type="text" name="statut" value="'.$object->statut.'"></td></tr>';

	print '</table>';
	
	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}



// Part to show record
if ($id && (empty($action) || $action == 'view' || $action == 'delete'))
{
	print load_fiche_titre($langs->trans("MyModule"));
    
	dol_fiche_head();

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('DeleteMyOjbect'), $langs->trans('ConfirmDeleteMyObject'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}
	
	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_task").'</td><td>$object->fk_task</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_contrat").'</td><td>$object->fk_contrat</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldc_grupo").'</td><td>$object->c_grupo</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlevel").'</td><td>$object->level</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldc_view").'</td><td>$object->c_view</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit").'</td><td>$object->fk_unit</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_type").'</td><td>$object->fk_type</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_item").'</td><td>$object->fk_item</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldunit_budget").'</td><td>$object->unit_budget</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldunit_program").'</td><td>$object->unit_program</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldunit_declared").'</td><td>$object->unit_declared</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldunit_ejecuted").'</td><td>$object->unit_ejecuted</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldunit_amount").'</td><td>$object->unit_amount</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddetail_close").'</td><td>$object->detail_close</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldorder_ref").'</td><td>$object->order_ref</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td>$object->fk_user_create</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td>$object->fk_user_mod</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatut").'</td><td>$object->statut</td></tr>';

	print '</table>';
	
	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->monprojet->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->monprojet->delete)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
		}
	}
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	//$somethingshown=$form->showLinkedObjectBlock($object);
	//$linktoelem = $form->showLinkToObjectBlock($object);
	//if ($linktoelem) print '<br>'.$linktoelem;

}


// End of page
llxFooter();
$db->close();
