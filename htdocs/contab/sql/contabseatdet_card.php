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
 *   	\file       contab/contabseatdet_card.php
 *		\ingroup    contab
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-11-04 14:58
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
dol_include_once('/contab/class/contabseatdet.class.php');

// Load traductions files requiredby by page
$langs->load("contab");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_fk_contab_seat=GETPOST('search_fk_contab_seat','int');
$search_debit_account=GETPOST('search_debit_account','alpha');
$search_debit_detail=GETPOST('search_debit_detail','alpha');
$search_credit_account=GETPOST('search_credit_account','alpha');
$search_credit_detail=GETPOST('search_credit_detail','alpha');
$search_dcd=GETPOST('search_dcd','int');
$search_dcc=GETPOST('search_dcc','int');
$search_amount=GETPOST('search_amount','alpha');
$search_history=GETPOST('search_history','alpha');
$search_sequence=GETPOST('search_sequence','alpha');
$search_fk_standard_seat=GETPOST('search_fk_standard_seat','int');
$search_type_seat=GETPOST('search_type_seat','int');
$search_routines=GETPOST('search_routines','alpha');
$search_value02=GETPOST('search_value02','alpha');
$search_value03=GETPOST('search_value03','alpha');
$search_value04=GETPOST('search_value04','alpha');
$search_rate=GETPOST('search_rate','alpha');
$search_fk_user_creator=GETPOST('search_fk_user_creator','int');
$search_state=GETPOST('search_state','int');



// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Contabseatdet($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('contabseatdet'));
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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/contab/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */
		
	$object->fk_contab_seat=GETPOST('fk_contab_seat','int');
	$object->debit_account=GETPOST('debit_account','alpha');
	$object->debit_detail=GETPOST('debit_detail','alpha');
	$object->credit_account=GETPOST('credit_account','alpha');
	$object->credit_detail=GETPOST('credit_detail','alpha');
	$object->dcd=GETPOST('dcd','int');
	$object->dcc=GETPOST('dcc','int');
	$object->amount=GETPOST('amount','alpha');
	$object->history=GETPOST('history','alpha');
	$object->sequence=GETPOST('sequence','alpha');
	$object->fk_standard_seat=GETPOST('fk_standard_seat','int');
	$object->type_seat=GETPOST('type_seat','int');
	$object->routines=GETPOST('routines','alpha');
	$object->value02=GETPOST('value02','alpha');
	$object->value03=GETPOST('value03','alpha');
	$object->value04=GETPOST('value04','alpha');
	$object->rate=GETPOST('rate','alpha');
	$object->fk_user_creator=GETPOST('fk_user_creator','int');
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
				$urltogo=$backtopage?$backtopage:dol_buildpath('/contab/list.php',1);
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

		
	$object->fk_contab_seat=GETPOST('fk_contab_seat','int');
	$object->debit_account=GETPOST('debit_account','alpha');
	$object->debit_detail=GETPOST('debit_detail','alpha');
	$object->credit_account=GETPOST('credit_account','alpha');
	$object->credit_detail=GETPOST('credit_detail','alpha');
	$object->dcd=GETPOST('dcd','int');
	$object->dcc=GETPOST('dcc','int');
	$object->amount=GETPOST('amount','alpha');
	$object->history=GETPOST('history','alpha');
	$object->sequence=GETPOST('sequence','alpha');
	$object->fk_standard_seat=GETPOST('fk_standard_seat','int');
	$object->type_seat=GETPOST('type_seat','int');
	$object->routines=GETPOST('routines','alpha');
	$object->value02=GETPOST('value02','alpha');
	$object->value03=GETPOST('value03','alpha');
	$object->value04=GETPOST('value04','alpha');
	$object->rate=GETPOST('rate','alpha');
	$object->fk_user_creator=GETPOST('fk_user_creator','int');
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
			header("Location: ".dol_buildpath('/contab/list.php',1));
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_contab_seat").'</td><td><input class="flat" type="text" name="fk_contab_seat" value="'.GETPOST('fk_contab_seat').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddebit_account").'</td><td><input class="flat" type="text" name="debit_account" value="'.GETPOST('debit_account').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddebit_detail").'</td><td><input class="flat" type="text" name="debit_detail" value="'.GETPOST('debit_detail').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcredit_account").'</td><td><input class="flat" type="text" name="credit_account" value="'.GETPOST('credit_account').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcredit_detail").'</td><td><input class="flat" type="text" name="credit_detail" value="'.GETPOST('credit_detail').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddcd").'</td><td><input class="flat" type="text" name="dcd" value="'.GETPOST('dcd').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddcc").'</td><td><input class="flat" type="text" name="dcc" value="'.GETPOST('dcc').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td><input class="flat" type="text" name="amount" value="'.GETPOST('amount').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldhistory").'</td><td><input class="flat" type="text" name="history" value="'.GETPOST('history').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldsequence").'</td><td><input class="flat" type="text" name="sequence" value="'.GETPOST('sequence').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_standard_seat").'</td><td><input class="flat" type="text" name="fk_standard_seat" value="'.GETPOST('fk_standard_seat').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_seat").'</td><td><input class="flat" type="text" name="type_seat" value="'.GETPOST('type_seat').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldroutines").'</td><td><input class="flat" type="text" name="routines" value="'.GETPOST('routines').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalue02").'</td><td><input class="flat" type="text" name="value02" value="'.GETPOST('value02').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalue03").'</td><td><input class="flat" type="text" name="value03" value="'.GETPOST('value03').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalue04").'</td><td><input class="flat" type="text" name="value04" value="'.GETPOST('value04').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrate").'</td><td><input class="flat" type="text" name="rate" value="'.GETPOST('rate').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_creator").'</td><td><input class="flat" type="text" name="fk_user_creator" value="'.GETPOST('fk_user_creator').'"></td></tr>';
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_contab_seat").'</td><td><input class="flat" type="text" name="fk_contab_seat" value="'.$object->fk_contab_seat.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddebit_account").'</td><td><input class="flat" type="text" name="debit_account" value="'.$object->debit_account.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddebit_detail").'</td><td><input class="flat" type="text" name="debit_detail" value="'.$object->debit_detail.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcredit_account").'</td><td><input class="flat" type="text" name="credit_account" value="'.$object->credit_account.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcredit_detail").'</td><td><input class="flat" type="text" name="credit_detail" value="'.$object->credit_detail.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddcd").'</td><td><input class="flat" type="text" name="dcd" value="'.$object->dcd.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddcc").'</td><td><input class="flat" type="text" name="dcc" value="'.$object->dcc.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td><input class="flat" type="text" name="amount" value="'.$object->amount.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldhistory").'</td><td><input class="flat" type="text" name="history" value="'.$object->history.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldsequence").'</td><td><input class="flat" type="text" name="sequence" value="'.$object->sequence.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_standard_seat").'</td><td><input class="flat" type="text" name="fk_standard_seat" value="'.$object->fk_standard_seat.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_seat").'</td><td><input class="flat" type="text" name="type_seat" value="'.$object->type_seat.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldroutines").'</td><td><input class="flat" type="text" name="routines" value="'.$object->routines.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalue02").'</td><td><input class="flat" type="text" name="value02" value="'.$object->value02.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalue03").'</td><td><input class="flat" type="text" name="value03" value="'.$object->value03.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalue04").'</td><td><input class="flat" type="text" name="value04" value="'.$object->value04.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrate").'</td><td><input class="flat" type="text" name="rate" value="'.$object->rate.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_creator").'</td><td><input class="flat" type="text" name="fk_user_creator" value="'.$object->fk_user_creator.'"></td></tr>';
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_contab_seat").'</td><td>$object->fk_contab_seat</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddebit_account").'</td><td>$object->debit_account</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddebit_detail").'</td><td>$object->debit_detail</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcredit_account").'</td><td>$object->credit_account</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcredit_detail").'</td><td>$object->credit_detail</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddcd").'</td><td>$object->dcd</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddcc").'</td><td>$object->dcc</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td>$object->amount</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldhistory").'</td><td>$object->history</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldsequence").'</td><td>$object->sequence</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_standard_seat").'</td><td>$object->fk_standard_seat</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_seat").'</td><td>$object->type_seat</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldroutines").'</td><td>$object->routines</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalue02").'</td><td>$object->value02</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalue03").'</td><td>$object->value03</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalue04").'</td><td>$object->value04</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrate").'</td><td>$object->rate</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_creator").'</td><td>$object->fk_user_creator</td></tr>';
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
		if ($user->rights->contab->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->contab->delete)
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
