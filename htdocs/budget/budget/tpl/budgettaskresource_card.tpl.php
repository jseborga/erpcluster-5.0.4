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
*					Initialy built by build_class_from_table on 2018-04-23 17:06
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



if (empty($action) && empty($id) && empty($ref)) $action='view';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
//$result = restrictedArea($user, 'budget', $id);



// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($objBudgettaskresource->table_element);

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('budgettaskresource'));

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
	print '<input type="hidden" name="id" value="'.$objBudgettaskresource->fk_budget_task.'">';
	print '<input type="hidden" name="idr" value="'.$objBudgettaskresource->id.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_budget_task").'</td><td><input class="flat" type="text" name="fk_budget_task" value="'.$objBudgettaskresource->fk_budget_task.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.$objBudgettaskresource->ref.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.$objBudgettaskresource->fk_user_create.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.$objBudgettaskresource->fk_user_mod.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode_structure").'</td><td>';
	print $aStrbudget[$object->fk_budget]['aStrcatgroup'][$objBudgettaskresource->code_structure];
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td>';
	$objProduct->fetch($objBudgettaskresource->fk_product);
	print $objProduct->getNomUrl(1);
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product_budget").'</td><td>';
	$objProductbudget->fetch($objBudgettaskresource->fk_product_budget);
	print $objProductbudget->getNomUrl(1);
	print '</td></tr>';
	//print '<tr><td>'.$langs->trans("Fieldfk_budget_task_comple").'</td><td><input class="flat" type="text" name="fk_budget_task_comple" value="'.$objBudgettaskresource->fk_budget_task_comple.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fielddetail").'</td><td><input class="flat" type="text" name="detail" value="'.$objBudgettaskresource->detail.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit").'</td><td><input class="flat" type="text" name="fk_unit" value="'.$objBudgettaskresource->fk_unit.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldquant").'</td><td><input class="flat" type="text" name="quant" value="'.$objBudgettaskresource->quant.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpercent_prod").'</td><td><input class="flat" type="text" name="percent_prod" value="'.$objBudgettaskresource->percent_prod.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_noprod").'</td><td><input class="flat" type="text" name="amount_noprod" value="'.$objBudgettaskresource->amount_noprod.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td><input class="flat" type="text" name="amount" value="'.$objBudgettaskresource->amount.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrang").'</td><td><input class="flat" type="text" name="rang" value="'.$objBudgettaskresource->rang.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpriority").'</td><td><input class="flat" type="text" name="priority" value="'.$objBudgettaskresource->priority.'"></td></tr>';
	if ($aStrbudget[$object->fk_budget]['aStrcatgroup'][$objBudgettaskresource->code_structure] == 'MQ')
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula").'</td><td><input class="flat" type="text" name="formula" value="'.$objBudgettaskresource->formula.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula_res").'</td><td><input class="flat" type="text" name="formula_res" value="'.$objBudgettaskresource->formula_res.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula_quant").'</td><td><input class="flat" type="text" name="formula_quant" value="'.$objBudgettaskresource->formula_quant.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula_factor").'</td><td><input class="flat" type="text" name="formula_factor" value="'.$objBudgettaskresource->formula_factor.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula_prod").'</td><td><input class="flat" type="text" name="formula_prod" value="'.$objBudgettaskresource->formula_prod.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.$objBudgettaskresource->status.'"></td></tr>';

	print '</table>';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}



// Part to show record
if ($objBudgettaskresource->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create')))
{
	$res = $objBudgettaskresource->fetch_optionals($objBudgettaskresource->id, $extralabels);


	print load_fiche_titre($langs->trans("Budgettaskresource"));

	dol_fiche_head();

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $objBudgettaskresource->fk_budget_task.'&idr='.$objBudgettaskresource->id, $langs->trans('DeleteBudgettaskresource'), $langs->trans('ConfirmDeleteBudgettaskresource'), 'confirm_delete', '', 0, 2);
		print $formconfirm;
	}

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>'.$objBudgettaskresource->label.'</td></tr>';
	//
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_budget_task").'</td><td>'.$objBudgettaskresource->fk_budget_task.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldref").'</td><td>'.$objBudgettaskresource->ref.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td>'.$objBudgettaskresource->fk_user_create.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td>'.$objBudgettaskresource->fk_user_mod.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldcode_structure").'</td><td>'.$aStrbudget[$object->fk_budget]['aStrcatgroup'][$objBudgettaskresource->code_structure].'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td>'.$objBudgettaskresource->fk_product.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldfk_product_budget").'</td><td>'.$objBudgettaskresource->fk_product_budget.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_budget_task_comple").'</td><td>'.$objBudgettaskresource->fk_budget_task_comple.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fielddetail").'</td><td>'.$objBudgettaskresource->detail.'</td></tr>';
	$objBudgettaskresourceline = new Budgettaskresourcelineext($db);
	$objBudgettaskresourceline->fk_unit = $objBudgettaskresource->fk_unit;
	print '<tr><td>'.$langs->trans("Fieldfk_unit").'</td><td>'.$objBudgettaskresourceline->getLabelOfUnit().'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldquant").'</td><td>'.$objBudgettaskresource->quant.'</td></tr>';
	//print '<tr><td>'.$langs->trans("Fieldpercent_prod").'</td><td>'.$objBudgettaskresource->percent_prod.'</td></tr>';
	//print '<tr><td>'.$langs->trans("Fieldamount_noprod").'</td><td>'.$objBudgettaskresource->amount_noprod.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldamount").'</td><td>'.$objBudgettaskresource->amount.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrang").'</td><td>'.$objBudgettaskresource->rang.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpriority").'</td><td>'.$objBudgettaskresource->priority.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldperformance").'</td><td>'.$objBudgettaskresource->performance.'</td></tr>';

	print '<tr><td>'.$langs->trans("Fieldformula").'</td><td>'.$objBudgettaskresource->formula.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula_res").'</td><td>'.$objBudgettaskresource->formula_res.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula_quant").'</td><td>'.$objBudgettaskresource->formula_quant.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula_factor").'</td><td>'.$objBudgettaskresource->formula_factor.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldformula_prod").'</td><td>'.$objBudgettaskresource->formula_prod.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldstatus").'</td><td>'.$objBudgettaskresource->getLibStatut(6).'</td></tr>';

	print '</table>';

	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($objBudget->fk_statut == 0)
		{
			if ($object->fk_statut == 0)
			{
				if ($user->rights->budget->budr->mod)
				{
					print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$objBudgettaskresource->fk_budget_task.'&idr='.$objBudgettaskresource->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
				}

				if ($user->rights->budget->budr->del)
				{
					print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$objBudgettaskresource->fk_budget_task.'&idr='.$objBudgettaskresource->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
				}
			}
		}
	}
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	// Show links to link elements
	//$linktoelem = $form->showLinkToObjectBlock($object, null, array('budgettaskresource'));
	//$somethingshown = $form->showLinkedObjectBlock($object, $linktoelem);

}
