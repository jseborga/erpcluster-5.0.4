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
 *   	\file       budget/budgettaskproduct_card.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-04-19 09:09
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



$search_fk_budget_task=GETPOST('search_fk_budget_task','int');
$search_ref=GETPOST('search_ref','alpha');
$search_group_structure=GETPOST('search_group_structure','alpha');
$search_fk_product=GETPOST('search_fk_product','int');
$search_fk_product_budget=GETPOST('search_fk_product_budget','int');
$search_label=GETPOST('search_label','alpha');
$search_formula=GETPOST('search_formula','alpha');
$search_units=GETPOST('search_units','int');
$search_commander=GETPOST('search_commander','int');
$search_price_productive=GETPOST('search_price_productive','alpha');
$search_price_improductive=GETPOST('search_price_improductive','alpha');
$search_active=GETPOST('search_active','int');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_fk_object=GETPOST('search_fk_object','int');
$search_status=GETPOST('search_status','int');



if (empty($action) && empty($id) && empty($ref)) $action='view';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
//$result = restrictedArea($user, 'budget', $id);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($objBudgettaskproduct->table_element);

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('budgettaskproduct'));



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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}
		if ($id > 0 || ! empty($ref)) $ret = $objBudgettaskproduct->fetch($id,$ref);
		$action='';
	}

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

	$objBudgettaskproduct->fk_budget_task=GETPOST('fk_budget_task','int');
	$objBudgettaskproduct->ref=GETPOST('ref','alpha');
	$objBudgettaskproduct->group_structure=GETPOST('group_structure','alpha');
	$objBudgettaskproduct->fk_product=GETPOST('fk_product','int');
	$objBudgettaskproduct->fk_product_budget=GETPOST('fk_product_budget','int');
	$objBudgettaskproduct->label=GETPOST('label','alpha');
	$objBudgettaskproduct->formula=GETPOST('formula','alpha');
	$objBudgettaskproduct->units=GETPOST('units','int');
	$objBudgettaskproduct->commander=GETPOST('commander','int');
	$objBudgettaskproduct->price_productive=GETPOST('price_productive','alpha');
	$objBudgettaskproduct->price_improductive=GETPOST('price_improductive','alpha');
	$objBudgettaskproduct->active=GETPOST('active','int');
	$objBudgettaskproduct->fk_user_create=GETPOST('fk_user_create','int');
	$objBudgettaskproduct->fk_user_mod=GETPOST('fk_user_mod','int');
	$objBudgettaskproduct->fk_object=GETPOST('fk_object','int');
	$objBudgettaskproduct->status=GETPOST('status','int');



		if (empty($objBudgettaskproduct->ref))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objBudgettaskproduct->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/list.php',1);
				header("Location: ".$urltogo);
				exit;
			}
			{
				// Creation KO
				if (! empty($objBudgettaskproduct->errors)) setEventMessages(null, $objBudgettaskproduct->errors, 'errors');
				else  setEventMessages($objBudgettaskproduct->error, null, 'errors');
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


	$objBudgettaskproduct->fk_budget_task=GETPOST('fk_budget_task','int');
	$objBudgettaskproduct->ref=GETPOST('ref','alpha');
	$objBudgettaskproduct->group_structure=GETPOST('group_structure','alpha');
	$objBudgettaskproduct->fk_product=GETPOST('fk_product','int');
	$objBudgettaskproduct->fk_product_budget=GETPOST('fk_product_budget','int');
	$objBudgettaskproduct->label=GETPOST('label','alpha');
	$objBudgettaskproduct->formula=GETPOST('formula','alpha');
	$objBudgettaskproduct->units=GETPOST('units','int');
	$objBudgettaskproduct->commander=GETPOST('commander','int');
	$objBudgettaskproduct->price_productive=GETPOST('price_productive','alpha');
	$objBudgettaskproduct->price_improductive=GETPOST('price_improductive','alpha');
	$objBudgettaskproduct->active=GETPOST('active','int');
	$objBudgettaskproduct->fk_user_create=GETPOST('fk_user_create','int');
	$objBudgettaskproduct->fk_user_mod=GETPOST('fk_user_mod','int');
	$objBudgettaskproduct->fk_object=GETPOST('fk_object','int');
	$objBudgettaskproduct->status=GETPOST('status','int');



		if (empty($objBudgettaskproduct->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objBudgettaskproduct->update($user);
			if ($result > 0)
			{
				$action='view';
			}
			else
			{
				// Creation KO
				if (! empty($objBudgettaskproduct->errors)) setEventMessages(null, $objBudgettaskproduct->errors, 'errors');
				else setEventMessages($objBudgettaskproduct->error, null, 'errors');
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
		$result=$objBudgettaskproduct->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/budget/list.php',1));
			exit;
		}
		else
		{
			if (! empty($objBudgettaskproduct->errors)) setEventMessages(null, $objBudgettaskproduct->errors, 'errors');
			else setEventMessages($objBudgettaskproduct->error, null, 'errors');
		}
	}
}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

// Part to create
if ($action == 'create')
{
	print load_fiche_titre($langs->trans("NewMyModule"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$id.'">';
	print '<input type="hidden" name="idr" value="'.$idr.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_budget_task").'</td><td><input class="flat" type="text" name="fk_budget_task" value="'.GETPOST('fk_budget_task').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.GETPOST('ref').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldgroup_structure").'</td><td><input class="flat" type="text" name="group_structure" value="'.GETPOST('group_structure').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td><input class="flat" type="text" name="fk_product" value="'.GETPOST('fk_product').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product_budget").'</td><td><input class="flat" type="text" name="fk_product_budget" value="'.GETPOST('fk_product_budget').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.GETPOST('label').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula").'</td><td><input class="flat" type="text" name="formula" value="'.GETPOST('formula').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldunits").'</td><td><input class="flat" type="text" name="units" value="'.GETPOST('units').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcommander").'</td><td><input class="flat" type="text" name="commander" value="'.GETPOST('commander').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice_productive").'</td><td><input class="flat" type="text" name="price_productive" value="'.GETPOST('price_productive').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice_improductive").'</td><td><input class="flat" type="text" name="price_improductive" value="'.GETPOST('price_improductive').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td><input class="flat" type="text" name="active" value="'.GETPOST('active').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.GETPOST('fk_user_create').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.GETPOST('fk_user_mod').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_object").'</td><td><input class="flat" type="text" name="fk_object" value="'.GETPOST('fk_object').'"></td></tr>';
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
	print '<input type="hidden" name="id" value="'.$id.'">';
	print '<input type="hidden" name="idr" value="'.$idr.'">';
	print '<input type="hidden" name="idrd" value="'.$objBudgettaskproduct->id.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_budget_task").'</td><td><input class="flat" type="text" name="fk_budget_task" value="'.$objBudgettaskproduct->fk_budget_task.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.$objBudgettaskproduct->ref.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldgroup_structure").'</td><td><input class="flat" type="text" name="group_structure" value="'.$objBudgettaskproduct->group_structure.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td><input class="flat" type="text" name="fk_product" value="'.$objBudgettaskproduct->fk_product.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product_budget").'</td><td><input class="flat" type="text" name="fk_product_budget" value="'.$objBudgettaskproduct->fk_product_budget.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.$objBudgettaskproduct->label.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula").'</td><td><input class="flat" type="text" name="formula" value="'.$objBudgettaskproduct->formula.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldunits").'</td><td><input class="flat" type="text" name="units" value="'.$objBudgettaskproduct->units.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcommander").'</td><td><input class="flat" type="text" name="commander" value="'.$objBudgettaskproduct->commander.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice_productive").'</td><td><input class="flat" type="text" name="price_productive" value="'.$objBudgettaskproduct->price_productive.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice_improductive").'</td><td><input class="flat" type="text" name="price_improductive" value="'.$objBudgettaskproduct->price_improductive.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td><input class="flat" type="text" name="active" value="'.$objBudgettaskproduct->active.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.$objBudgettaskproduct->fk_user_create.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.$objBudgettaskproduct->fk_user_mod.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_object").'</td><td><input class="flat" type="text" name="fk_object" value="'.$objBudgettaskproduct->fk_object.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.$objBudgettaskproduct->status.'"></td></tr>';

	print '</table>';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}



// Part to show record
if ($objBudgettaskproduct->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create')))
{
    $res = $objBudgettaskproduct->fetch_optionals($objBudgettaskproduct->id, $extralabels);


	print load_fiche_titre($langs->trans("MyModule"));

	dol_fiche_head();

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $objBudgettaskproduct->id, $langs->trans('DeleteMyOjbect'), $langs->trans('ConfirmDeleteMyObject'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>'.$objBudgettaskproduct->label.'</td></tr>';
	//
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_budget_task").'</td><td>'.$objBudgettaskproduct->fk_budget_task.'</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td>'.$objBudgettaskproduct->ref.'</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldgroup_structure").'</td><td>'.$objBudgettaskproduct->group_structure.'</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td>'.$objBudgettaskproduct->fk_product.'</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product_budget").'</td><td>'.$objBudgettaskproduct->fk_product_budget.'</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td>'.$objBudgettaskproduct->label.'</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula").'</td><td>'.$objBudgettaskproduct->formula.'</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldunits").'</td><td>'.$objBudgettaskproduct->units.'</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcommander").'</td><td>'.$objBudgettaskproduct->commander.'</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice_productive").'</td><td>'.$objBudgettaskproduct->price_productive.'</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice_improductive").'</td><td>'.$objBudgettaskproduct->price_improductive.'</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td>'.$objBudgettaskproduct->active.'</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td>'.$objBudgettaskproduct->fk_user_create.'</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td>'.$objBudgettaskproduct->fk_user_mod.'</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_object").'</td><td>'.$objBudgettaskproduct->fk_object.'</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td>'.$objBudgettaskproduct->status.'</td></tr>';

	print '</table>';

	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->budget->tasksupp->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='$id.'&idr='.$idr.'&idrd='.$objBudgettaskproduct->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->budget->tasksupp->del)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='$id.'&idr='.$idr.'&idrd='.$objBudgettaskproduct->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
		}
	}
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	// Show links to link elements
	//$linktoelem = $form->showLinkToObjectBlock($object, null, array('budgettaskproduct'));
	//$somethingshown = $form->showLinkedObjectBlock($object, $linktoelem);

}


