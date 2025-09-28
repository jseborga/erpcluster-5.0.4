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
 *   	\file       budget/budgettaskresource_card.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-01-16 08:46
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
dol_include_once('/budget/class/budgettaskresource.class.php');

// Load traductions files requiredby by page
$langs->load("budget");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_fk_budget_task=GETPOST('search_fk_budget_task','int');
$search_ref=GETPOST('search_ref','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_code_structure=GETPOST('search_code_structure','alpha');
$search_fk_product=GETPOST('search_fk_product','int');
$search_fk_product_budget=GETPOST('search_fk_product_budget','int');
$search_fk_budget_task_comple=GETPOST('search_fk_budget_task_comple','int');
$search_detail=GETPOST('search_detail','alpha');
$search_fk_unit=GETPOST('search_fk_unit','int');
$search_quant=GETPOST('search_quant','alpha');
$search_percent_prod=GETPOST('search_percent_prod','alpha');
$search_amount_noprod=GETPOST('search_amount_noprod','alpha');
$search_amount=GETPOST('search_amount','alpha');
$search_rang=GETPOST('search_rang','int');
$search_priority=GETPOST('search_priority','int');
$search_formula=GETPOST('search_formula','alpha');
$search_formula_res=GETPOST('search_formula_res','alpha');
$search_formula_quant=GETPOST('search_formula_quant','alpha');
$search_formula_factor=GETPOST('search_formula_factor','alpha');
$search_formula_prod=GETPOST('search_formula_prod','alpha');
$search_status=GETPOST('search_status','int');



// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Budgettaskresource($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('budgettaskresource'));
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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */
		
	$object->fk_budget_task=GETPOST('fk_budget_task','int');
	$object->ref=GETPOST('ref','alpha');
	$object->fk_user_create=GETPOST('fk_user_create','int');
	$object->fk_user_mod=GETPOST('fk_user_mod','int');
	$object->code_structure=GETPOST('code_structure','alpha');
	$object->fk_product=GETPOST('fk_product','int');
	$object->fk_product_budget=GETPOST('fk_product_budget','int');
	$object->fk_budget_task_comple=GETPOST('fk_budget_task_comple','int');
	$object->detail=GETPOST('detail','alpha');
	$object->fk_unit=GETPOST('fk_unit','int');
	$object->quant=GETPOST('quant','alpha');
	$object->percent_prod=GETPOST('percent_prod','alpha');
	$object->amount_noprod=GETPOST('amount_noprod','alpha');
	$object->amount=GETPOST('amount','alpha');
	$object->rang=GETPOST('rang','int');
	$object->priority=GETPOST('priority','int');
	$object->formula=GETPOST('formula','alpha');
	$object->formula_res=GETPOST('formula_res','alpha');
	$object->formula_quant=GETPOST('formula_quant','alpha');
	$object->formula_factor=GETPOST('formula_factor','alpha');
	$object->formula_prod=GETPOST('formula_prod','alpha');
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
				$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/list.php',1);
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

		
	$object->fk_budget_task=GETPOST('fk_budget_task','int');
	$object->ref=GETPOST('ref','alpha');
	$object->fk_user_create=GETPOST('fk_user_create','int');
	$object->fk_user_mod=GETPOST('fk_user_mod','int');
	$object->code_structure=GETPOST('code_structure','alpha');
	$object->fk_product=GETPOST('fk_product','int');
	$object->fk_product_budget=GETPOST('fk_product_budget','int');
	$object->fk_budget_task_comple=GETPOST('fk_budget_task_comple','int');
	$object->detail=GETPOST('detail','alpha');
	$object->fk_unit=GETPOST('fk_unit','int');
	$object->quant=GETPOST('quant','alpha');
	$object->percent_prod=GETPOST('percent_prod','alpha');
	$object->amount_noprod=GETPOST('amount_noprod','alpha');
	$object->amount=GETPOST('amount','alpha');
	$object->rang=GETPOST('rang','int');
	$object->priority=GETPOST('priority','int');
	$object->formula=GETPOST('formula','alpha');
	$object->formula_res=GETPOST('formula_res','alpha');
	$object->formula_quant=GETPOST('formula_quant','alpha');
	$object->formula_factor=GETPOST('formula_factor','alpha');
	$object->formula_prod=GETPOST('formula_prod','alpha');
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
			header("Location: ".dol_buildpath('/budget/list.php',1));
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_budget_task").'</td><td><input class="flat" type="text" name="fk_budget_task" value="'.GETPOST('fk_budget_task').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.GETPOST('ref').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.GETPOST('fk_user_create').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.GETPOST('fk_user_mod').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode_structure").'</td><td><input class="flat" type="text" name="code_structure" value="'.GETPOST('code_structure').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td><input class="flat" type="text" name="fk_product" value="'.GETPOST('fk_product').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product_budget").'</td><td><input class="flat" type="text" name="fk_product_budget" value="'.GETPOST('fk_product_budget').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_budget_task_comple").'</td><td><input class="flat" type="text" name="fk_budget_task_comple" value="'.GETPOST('fk_budget_task_comple').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddetail").'</td><td><input class="flat" type="text" name="detail" value="'.GETPOST('detail').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit").'</td><td><input class="flat" type="text" name="fk_unit" value="'.GETPOST('fk_unit').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldquant").'</td><td><input class="flat" type="text" name="quant" value="'.GETPOST('quant').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpercent_prod").'</td><td><input class="flat" type="text" name="percent_prod" value="'.GETPOST('percent_prod').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_noprod").'</td><td><input class="flat" type="text" name="amount_noprod" value="'.GETPOST('amount_noprod').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td><input class="flat" type="text" name="amount" value="'.GETPOST('amount').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrang").'</td><td><input class="flat" type="text" name="rang" value="'.GETPOST('rang').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpriority").'</td><td><input class="flat" type="text" name="priority" value="'.GETPOST('priority').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula").'</td><td><input class="flat" type="text" name="formula" value="'.GETPOST('formula').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula_res").'</td><td><input class="flat" type="text" name="formula_res" value="'.GETPOST('formula_res').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula_quant").'</td><td><input class="flat" type="text" name="formula_quant" value="'.GETPOST('formula_quant').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula_factor").'</td><td><input class="flat" type="text" name="formula_factor" value="'.GETPOST('formula_factor').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula_prod").'</td><td><input class="flat" type="text" name="formula_prod" value="'.GETPOST('formula_prod').'"></td></tr>';
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_budget_task").'</td><td><input class="flat" type="text" name="fk_budget_task" value="'.$object->fk_budget_task.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.$object->ref.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.$object->fk_user_create.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.$object->fk_user_mod.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode_structure").'</td><td><input class="flat" type="text" name="code_structure" value="'.$object->code_structure.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td><input class="flat" type="text" name="fk_product" value="'.$object->fk_product.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product_budget").'</td><td><input class="flat" type="text" name="fk_product_budget" value="'.$object->fk_product_budget.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_budget_task_comple").'</td><td><input class="flat" type="text" name="fk_budget_task_comple" value="'.$object->fk_budget_task_comple.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddetail").'</td><td><input class="flat" type="text" name="detail" value="'.$object->detail.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit").'</td><td><input class="flat" type="text" name="fk_unit" value="'.$object->fk_unit.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldquant").'</td><td><input class="flat" type="text" name="quant" value="'.$object->quant.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpercent_prod").'</td><td><input class="flat" type="text" name="percent_prod" value="'.$object->percent_prod.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_noprod").'</td><td><input class="flat" type="text" name="amount_noprod" value="'.$object->amount_noprod.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td><input class="flat" type="text" name="amount" value="'.$object->amount.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrang").'</td><td><input class="flat" type="text" name="rang" value="'.$object->rang.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpriority").'</td><td><input class="flat" type="text" name="priority" value="'.$object->priority.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula").'</td><td><input class="flat" type="text" name="formula" value="'.$object->formula.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula_res").'</td><td><input class="flat" type="text" name="formula_res" value="'.$object->formula_res.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula_quant").'</td><td><input class="flat" type="text" name="formula_quant" value="'.$object->formula_quant.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula_factor").'</td><td><input class="flat" type="text" name="formula_factor" value="'.$object->formula_factor.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula_prod").'</td><td><input class="flat" type="text" name="formula_prod" value="'.$object->formula_prod.'"></td></tr>';
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_budget_task").'</td><td>$object->fk_budget_task</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td>$object->ref</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td>$object->fk_user_create</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td>$object->fk_user_mod</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode_structure").'</td><td>$object->code_structure</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td>$object->fk_product</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product_budget").'</td><td>$object->fk_product_budget</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_budget_task_comple").'</td><td>$object->fk_budget_task_comple</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddetail").'</td><td>$object->detail</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit").'</td><td>$object->fk_unit</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldquant").'</td><td>$object->quant</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpercent_prod").'</td><td>$object->percent_prod</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_noprod").'</td><td>$object->amount_noprod</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td>$object->amount</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrang").'</td><td>$object->rang</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpriority").'</td><td>$object->priority</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula").'</td><td>$object->formula</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula_res").'</td><td>$object->formula_res</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula_quant").'</td><td>$object->formula_quant</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula_factor").'</td><td>$object->formula_factor</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula_prod").'</td><td>$object->formula_prod</td></tr>';
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
		if ($user->rights->budget->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->budget->delete)
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
