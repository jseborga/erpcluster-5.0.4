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
 *   	\file       finint/requestcash_card.php
 *		\ingroup    finint
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-11-15 10:13
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
dol_include_once('/finint/class/requestcash.class.php');

// Load traductions files requiredby by page
$langs->load("finint");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_fk_projet=GETPOST('search_fk_projet','int');
$search_fk_account=GETPOST('search_fk_account','int');
$search_fk_account_from=GETPOST('search_fk_account_from','int');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_assigned=GETPOST('search_fk_user_assigned','int');
$search_fk_user_authorized=GETPOST('search_fk_user_authorized','int');
$search_fk_user_approved=GETPOST('search_fk_user_approved','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_fk_type_cash=GETPOST('search_fk_type_cash','int');
$search_fk_type=GETPOST('search_fk_type','alpha');
$search_fk_categorie=GETPOST('search_fk_categorie','int');
$search_detail=GETPOST('search_detail','alpha');
$search_description=GETPOST('search_description','alpha');
$search_document=GETPOST('search_document','alpha');
$search_document_discharg=GETPOST('search_document_discharg','alpha');
$search_amount=GETPOST('search_amount','alpha');
$search_amount_approved=GETPOST('search_amount_approved','alpha');
$search_amount_authorized=GETPOST('search_amount_authorized','alpha');
$search_amount_out=GETPOST('search_amount_out','alpha');
$search_amount_close=GETPOST('search_amount_close','alpha');
$search_model_pdf=GETPOST('search_model_pdf','alpha');
$search_nro_chq=GETPOST('search_nro_chq','alpha');
$search_status=GETPOST('search_status','int');



// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Requestcash($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('requestcash'));
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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/finint/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */
		
	$object->entity=GETPOST('entity','int');
	$object->ref=GETPOST('ref','alpha');
	$object->fk_projet=GETPOST('fk_projet','int');
	$object->fk_account=GETPOST('fk_account','int');
	$object->fk_account_from=GETPOST('fk_account_from','int');
	$object->fk_user_create=GETPOST('fk_user_create','int');
	$object->fk_user_assigned=GETPOST('fk_user_assigned','int');
	$object->fk_user_authorized=GETPOST('fk_user_authorized','int');
	$object->fk_user_approved=GETPOST('fk_user_approved','int');
	$object->fk_user_mod=GETPOST('fk_user_mod','int');
	$object->fk_type_cash=GETPOST('fk_type_cash','int');
	$object->fk_type=GETPOST('fk_type','alpha');
	$object->fk_categorie=GETPOST('fk_categorie','int');
	$object->detail=GETPOST('detail','alpha');
	$object->description=GETPOST('description','alpha');
	$object->document=GETPOST('document','alpha');
	$object->document_discharg=GETPOST('document_discharg','alpha');
	$object->amount=GETPOST('amount','alpha');
	$object->amount_approved=GETPOST('amount_approved','alpha');
	$object->amount_authorized=GETPOST('amount_authorized','alpha');
	$object->amount_out=GETPOST('amount_out','alpha');
	$object->amount_close=GETPOST('amount_close','alpha');
	$object->model_pdf=GETPOST('model_pdf','alpha');
	$object->nro_chq=GETPOST('nro_chq','alpha');
	$object->status=GETPOST('status','int');

		

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
				$urltogo=$backtopage?$backtopage:dol_buildpath('/finint/list.php',1);
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
	$object->ref=GETPOST('ref','alpha');
	$object->fk_projet=GETPOST('fk_projet','int');
	$object->fk_account=GETPOST('fk_account','int');
	$object->fk_account_from=GETPOST('fk_account_from','int');
	$object->fk_user_create=GETPOST('fk_user_create','int');
	$object->fk_user_assigned=GETPOST('fk_user_assigned','int');
	$object->fk_user_authorized=GETPOST('fk_user_authorized','int');
	$object->fk_user_approved=GETPOST('fk_user_approved','int');
	$object->fk_user_mod=GETPOST('fk_user_mod','int');
	$object->fk_type_cash=GETPOST('fk_type_cash','int');
	$object->fk_type=GETPOST('fk_type','alpha');
	$object->fk_categorie=GETPOST('fk_categorie','int');
	$object->detail=GETPOST('detail','alpha');
	$object->description=GETPOST('description','alpha');
	$object->document=GETPOST('document','alpha');
	$object->document_discharg=GETPOST('document_discharg','alpha');
	$object->amount=GETPOST('amount','alpha');
	$object->amount_approved=GETPOST('amount_approved','alpha');
	$object->amount_authorized=GETPOST('amount_authorized','alpha');
	$object->amount_out=GETPOST('amount_out','alpha');
	$object->amount_close=GETPOST('amount_close','alpha');
	$object->model_pdf=GETPOST('model_pdf','alpha');
	$object->nro_chq=GETPOST('nro_chq','alpha');
	$object->status=GETPOST('status','int');

		

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
			header("Location: ".dol_buildpath('/finint/list.php',1));
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.GETPOST('ref').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_projet").'</td><td><input class="flat" type="text" name="fk_projet" value="'.GETPOST('fk_projet').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_account").'</td><td><input class="flat" type="text" name="fk_account" value="'.GETPOST('fk_account').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_account_from").'</td><td><input class="flat" type="text" name="fk_account_from" value="'.GETPOST('fk_account_from').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.GETPOST('fk_user_create').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_assigned").'</td><td><input class="flat" type="text" name="fk_user_assigned" value="'.GETPOST('fk_user_assigned').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_authorized").'</td><td><input class="flat" type="text" name="fk_user_authorized" value="'.GETPOST('fk_user_authorized').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_approved").'</td><td><input class="flat" type="text" name="fk_user_approved" value="'.GETPOST('fk_user_approved').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.GETPOST('fk_user_mod').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_type_cash").'</td><td><input class="flat" type="text" name="fk_type_cash" value="'.GETPOST('fk_type_cash').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_type").'</td><td><input class="flat" type="text" name="fk_type" value="'.GETPOST('fk_type').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_categorie").'</td><td><input class="flat" type="text" name="fk_categorie" value="'.GETPOST('fk_categorie').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddetail").'</td><td><input class="flat" type="text" name="detail" value="'.GETPOST('detail').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddescription").'</td><td><input class="flat" type="text" name="description" value="'.GETPOST('description').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddocument").'</td><td><input class="flat" type="text" name="document" value="'.GETPOST('document').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddocument_discharg").'</td><td><input class="flat" type="text" name="document_discharg" value="'.GETPOST('document_discharg').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td><input class="flat" type="text" name="amount" value="'.GETPOST('amount').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_approved").'</td><td><input class="flat" type="text" name="amount_approved" value="'.GETPOST('amount_approved').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_authorized").'</td><td><input class="flat" type="text" name="amount_authorized" value="'.GETPOST('amount_authorized').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_out").'</td><td><input class="flat" type="text" name="amount_out" value="'.GETPOST('amount_out').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_close").'</td><td><input class="flat" type="text" name="amount_close" value="'.GETPOST('amount_close').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodel_pdf").'</td><td><input class="flat" type="text" name="model_pdf" value="'.GETPOST('model_pdf').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnro_chq").'</td><td><input class="flat" type="text" name="nro_chq" value="'.GETPOST('nro_chq').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.GETPOST('status').'"></td></tr>';

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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.$object->ref.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_projet").'</td><td><input class="flat" type="text" name="fk_projet" value="'.$object->fk_projet.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_account").'</td><td><input class="flat" type="text" name="fk_account" value="'.$object->fk_account.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_account_from").'</td><td><input class="flat" type="text" name="fk_account_from" value="'.$object->fk_account_from.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.$object->fk_user_create.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_assigned").'</td><td><input class="flat" type="text" name="fk_user_assigned" value="'.$object->fk_user_assigned.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_authorized").'</td><td><input class="flat" type="text" name="fk_user_authorized" value="'.$object->fk_user_authorized.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_approved").'</td><td><input class="flat" type="text" name="fk_user_approved" value="'.$object->fk_user_approved.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.$object->fk_user_mod.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_type_cash").'</td><td><input class="flat" type="text" name="fk_type_cash" value="'.$object->fk_type_cash.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_type").'</td><td><input class="flat" type="text" name="fk_type" value="'.$object->fk_type.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_categorie").'</td><td><input class="flat" type="text" name="fk_categorie" value="'.$object->fk_categorie.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddetail").'</td><td><input class="flat" type="text" name="detail" value="'.$object->detail.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddescription").'</td><td><input class="flat" type="text" name="description" value="'.$object->description.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddocument").'</td><td><input class="flat" type="text" name="document" value="'.$object->document.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddocument_discharg").'</td><td><input class="flat" type="text" name="document_discharg" value="'.$object->document_discharg.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td><input class="flat" type="text" name="amount" value="'.$object->amount.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_approved").'</td><td><input class="flat" type="text" name="amount_approved" value="'.$object->amount_approved.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_authorized").'</td><td><input class="flat" type="text" name="amount_authorized" value="'.$object->amount_authorized.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_out").'</td><td><input class="flat" type="text" name="amount_out" value="'.$object->amount_out.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_close").'</td><td><input class="flat" type="text" name="amount_close" value="'.$object->amount_close.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodel_pdf").'</td><td><input class="flat" type="text" name="model_pdf" value="'.$object->model_pdf.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnro_chq").'</td><td><input class="flat" type="text" name="nro_chq" value="'.$object->nro_chq.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.$object->status.'"></td></tr>';

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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td>$object->ref</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_projet").'</td><td>$object->fk_projet</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_account").'</td><td>$object->fk_account</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_account_from").'</td><td>$object->fk_account_from</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td>$object->fk_user_create</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_assigned").'</td><td>$object->fk_user_assigned</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_authorized").'</td><td>$object->fk_user_authorized</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_approved").'</td><td>$object->fk_user_approved</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td>$object->fk_user_mod</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_type_cash").'</td><td>$object->fk_type_cash</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_type").'</td><td>$object->fk_type</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_categorie").'</td><td>$object->fk_categorie</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddetail").'</td><td>$object->detail</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddescription").'</td><td>$object->description</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddocument").'</td><td>$object->document</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddocument_discharg").'</td><td>$object->document_discharg</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td>$object->amount</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_approved").'</td><td>$object->amount_approved</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_authorized").'</td><td>$object->amount_authorized</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_out").'</td><td>$object->amount_out</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_close").'</td><td>$object->amount_close</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodel_pdf").'</td><td>$object->model_pdf</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnro_chq").'</td><td>$object->nro_chq</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td>$object->status</td></tr>';

	print '</table>';
	
	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->finint->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->finint->delete)
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
