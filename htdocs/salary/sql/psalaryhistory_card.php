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
 *   	\file       salary/psalaryhistory_card.php
 *		\ingroup    salary
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-01-09 12:17
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
dol_include_once('/salary/class/psalaryhistory.class.php');

// Load traductions files requiredby by page
$langs->load("salary");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_fk_salary_present=GETPOST('search_fk_salary_present','int');
$search_fk_proces=GETPOST('search_fk_proces','int');
$search_fk_type_fol=GETPOST('search_fk_type_fol','int');
$search_fk_concept=GETPOST('search_fk_concept','int');
$search_fk_period=GETPOST('search_fk_period','int');
$search_fk_user=GETPOST('search_fk_user','int');
$search_fk_cc=GETPOST('search_fk_cc','int');
$search_sequen=GETPOST('search_sequen','int');
$search_type=GETPOST('search_type','int');
$search_cuota=GETPOST('search_cuota','int');
$search_semana=GETPOST('search_semana','int');
$search_amount_inf=GETPOST('search_amount_inf','alpha');
$search_amount=GETPOST('search_amount','alpha');
$search_hours_info=GETPOST('search_hours_info','int');
$search_hours=GETPOST('search_hours','int');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_fk_account=GETPOST('search_fk_account','int');
$search_payment_state=GETPOST('search_payment_state','int');
$search_state=GETPOST('search_state','int');



// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Psalaryhistory($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('psalaryhistory'));
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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/salary/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */
		
	$object->entity=GETPOST('entity','int');
	$object->fk_salary_present=GETPOST('fk_salary_present','int');
	$object->fk_proces=GETPOST('fk_proces','int');
	$object->fk_type_fol=GETPOST('fk_type_fol','int');
	$object->fk_concept=GETPOST('fk_concept','int');
	$object->fk_period=GETPOST('fk_period','int');
	$object->fk_user=GETPOST('fk_user','int');
	$object->fk_cc=GETPOST('fk_cc','int');
	$object->sequen=GETPOST('sequen','int');
	$object->type=GETPOST('type','int');
	$object->cuota=GETPOST('cuota','int');
	$object->semana=GETPOST('semana','int');
	$object->amount_inf=GETPOST('amount_inf','alpha');
	$object->amount=GETPOST('amount','alpha');
	$object->hours_info=GETPOST('hours_info','int');
	$object->hours=GETPOST('hours','int');
	$object->fk_user_create=GETPOST('fk_user_create','int');
	$object->fk_user_mod=GETPOST('fk_user_mod','int');
	$object->fk_account=GETPOST('fk_account','int');
	$object->payment_state=GETPOST('payment_state','int');
	$object->state=GETPOST('state','int');

		

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
				$urltogo=$backtopage?$backtopage:dol_buildpath('/salary/list.php',1);
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

		
	$object->entity=GETPOST('entity','int');
	$object->fk_salary_present=GETPOST('fk_salary_present','int');
	$object->fk_proces=GETPOST('fk_proces','int');
	$object->fk_type_fol=GETPOST('fk_type_fol','int');
	$object->fk_concept=GETPOST('fk_concept','int');
	$object->fk_period=GETPOST('fk_period','int');
	$object->fk_user=GETPOST('fk_user','int');
	$object->fk_cc=GETPOST('fk_cc','int');
	$object->sequen=GETPOST('sequen','int');
	$object->type=GETPOST('type','int');
	$object->cuota=GETPOST('cuota','int');
	$object->semana=GETPOST('semana','int');
	$object->amount_inf=GETPOST('amount_inf','alpha');
	$object->amount=GETPOST('amount','alpha');
	$object->hours_info=GETPOST('hours_info','int');
	$object->hours=GETPOST('hours','int');
	$object->fk_user_create=GETPOST('fk_user_create','int');
	$object->fk_user_mod=GETPOST('fk_user_mod','int');
	$object->fk_account=GETPOST('fk_account','int');
	$object->payment_state=GETPOST('payment_state','int');
	$object->state=GETPOST('state','int');

		

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
			header("Location: ".dol_buildpath('/salary/list.php',1));
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td><input class="flat" type="text" name="entity" value="'.GETPOST('entity').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_salary_present").'</td><td><input class="flat" type="text" name="fk_salary_present" value="'.GETPOST('fk_salary_present').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_proces").'</td><td><input class="flat" type="text" name="fk_proces" value="'.GETPOST('fk_proces').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_type_fol").'</td><td><input class="flat" type="text" name="fk_type_fol" value="'.GETPOST('fk_type_fol').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_concept").'</td><td><input class="flat" type="text" name="fk_concept" value="'.GETPOST('fk_concept').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_period").'</td><td><input class="flat" type="text" name="fk_period" value="'.GETPOST('fk_period').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user").'</td><td><input class="flat" type="text" name="fk_user" value="'.GETPOST('fk_user').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_cc").'</td><td><input class="flat" type="text" name="fk_cc" value="'.GETPOST('fk_cc').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldsequen").'</td><td><input class="flat" type="text" name="sequen" value="'.GETPOST('sequen').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype").'</td><td><input class="flat" type="text" name="type" value="'.GETPOST('type').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcuota").'</td><td><input class="flat" type="text" name="cuota" value="'.GETPOST('cuota').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldsemana").'</td><td><input class="flat" type="text" name="semana" value="'.GETPOST('semana').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_inf").'</td><td><input class="flat" type="text" name="amount_inf" value="'.GETPOST('amount_inf').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td><input class="flat" type="text" name="amount" value="'.GETPOST('amount').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldhours_info").'</td><td><input class="flat" type="text" name="hours_info" value="'.GETPOST('hours_info').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldhours").'</td><td><input class="flat" type="text" name="hours" value="'.GETPOST('hours').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.GETPOST('fk_user_create').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.GETPOST('fk_user_mod').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_account").'</td><td><input class="flat" type="text" name="fk_account" value="'.GETPOST('fk_account').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpayment_state").'</td><td><input class="flat" type="text" name="payment_state" value="'.GETPOST('payment_state').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstate").'</td><td><input class="flat" type="text" name="state" value="'.GETPOST('state').'"></td></tr>';

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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td><input class="flat" type="text" name="entity" value="'.$object->entity.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_salary_present").'</td><td><input class="flat" type="text" name="fk_salary_present" value="'.$object->fk_salary_present.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_proces").'</td><td><input class="flat" type="text" name="fk_proces" value="'.$object->fk_proces.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_type_fol").'</td><td><input class="flat" type="text" name="fk_type_fol" value="'.$object->fk_type_fol.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_concept").'</td><td><input class="flat" type="text" name="fk_concept" value="'.$object->fk_concept.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_period").'</td><td><input class="flat" type="text" name="fk_period" value="'.$object->fk_period.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user").'</td><td><input class="flat" type="text" name="fk_user" value="'.$object->fk_user.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_cc").'</td><td><input class="flat" type="text" name="fk_cc" value="'.$object->fk_cc.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldsequen").'</td><td><input class="flat" type="text" name="sequen" value="'.$object->sequen.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype").'</td><td><input class="flat" type="text" name="type" value="'.$object->type.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcuota").'</td><td><input class="flat" type="text" name="cuota" value="'.$object->cuota.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldsemana").'</td><td><input class="flat" type="text" name="semana" value="'.$object->semana.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_inf").'</td><td><input class="flat" type="text" name="amount_inf" value="'.$object->amount_inf.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td><input class="flat" type="text" name="amount" value="'.$object->amount.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldhours_info").'</td><td><input class="flat" type="text" name="hours_info" value="'.$object->hours_info.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldhours").'</td><td><input class="flat" type="text" name="hours" value="'.$object->hours.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.$object->fk_user_create.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.$object->fk_user_mod.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_account").'</td><td><input class="flat" type="text" name="fk_account" value="'.$object->fk_account.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpayment_state").'</td><td><input class="flat" type="text" name="payment_state" value="'.$object->payment_state.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstate").'</td><td><input class="flat" type="text" name="state" value="'.$object->state.'"></td></tr>';

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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td>$object->entity</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_salary_present").'</td><td>$object->fk_salary_present</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_proces").'</td><td>$object->fk_proces</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_type_fol").'</td><td>$object->fk_type_fol</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_concept").'</td><td>$object->fk_concept</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_period").'</td><td>$object->fk_period</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user").'</td><td>$object->fk_user</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_cc").'</td><td>$object->fk_cc</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldsequen").'</td><td>$object->sequen</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype").'</td><td>$object->type</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcuota").'</td><td>$object->cuota</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldsemana").'</td><td>$object->semana</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_inf").'</td><td>$object->amount_inf</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td>$object->amount</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldhours_info").'</td><td>$object->hours_info</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldhours").'</td><td>$object->hours</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td>$object->fk_user_create</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td>$object->fk_user_mod</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_account").'</td><td>$object->fk_account</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpayment_state").'</td><td>$object->payment_state</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstate").'</td><td>$object->state</td></tr>';

	print '</table>';
	
	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->salary->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->salary->delete)
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
