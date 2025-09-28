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
 *   	\file       budget/budgettask_card.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-04-23 16:46
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
dol_include_once('/budget/class/budgettaskext.class.php');
dol_include_once('/budget/class/budgettaskaddext.class.php');
dol_include_once('/budget/class/budgetgeneral.class.php');

dol_include_once('/budget/class/budgetext.class.php');
dol_include_once('/budget/class/itemsext.class.php');
dol_include_once('/budget/class/budgettaskresourceext.class.php');
dol_include_once('/budget/class/productbudgetext.class.php');
dol_include_once('/product/class/product.class.php');
dol_include_once('/budget/lib/budget.lib.php');


// Load traductions files requiredby by page
$langs->load("budget");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$idr = GETPOST('idr','int');
$action		= GETPOST('action','alpha');
$cancel     = GETPOST('cancel');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_fk_budget=GETPOST('search_fk_budget','int');
$search_fk_task=GETPOST('search_fk_task','int');
$search_fk_task_parent=GETPOST('search_fk_task_parent','int');
$search_label=GETPOST('search_label','alpha');
$search_description=GETPOST('search_description','alpha');
$search_duration_effective=GETPOST('search_duration_effective','alpha');
$search_planned_workload=GETPOST('search_planned_workload','alpha');
$search_progress=GETPOST('search_progress','int');
$search_priority=GETPOST('search_priority','int');
$search_fk_user_creat=GETPOST('search_fk_user_creat','int');
$search_fk_user_valid=GETPOST('search_fk_user_valid','int');
$search_fk_statut=GETPOST('search_fk_statut','int');
$search_note_private=GETPOST('search_note_private','alpha');
$search_note_public=GETPOST('search_note_public','alpha');
$search_rang=GETPOST('search_rang','int');
$search_model_pdf=GETPOST('search_model_pdf','alpha');



if (empty($action) && empty($id) && empty($ref)) $action='view';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
//$result = restrictedArea($user, 'budget', $id);


$object = new Budgettaskext($db);
$extrafields = new ExtraFields($db);
$objBudget = new Budgetext($db);
$objItems = new Itemsext($db);
$objBudgettaskresource = new Budgettaskresourceext($db);
$objProduct = new Product($db);
$objProductbudget = new Productbudgetext($db);
$objBudgettaskadd = new Budgettaskaddext($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('budgettask'));
if ($id)
{
	$res = get_structure_budget($object->fk_budget);
}
$aStrbudget = unserialize($_SESSION['aStrbudget']);

if ($id>0)
{
	$objBudget->fetch($object->fk_budget);
}
if ($idr>0)
{
	$objBudgettaskresource->fetch($idr);
}
/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/budget/supplies.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}
		if ($id > 0 || ! empty($ref)) $ret = $objBudgettaskresource->fetch($id,$ref);
		$action='';
	}

	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/budget/supplies.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */

		$objBudgettaskresource->fk_budget_task=GETPOST('fk_budget_task','int');
		$objBudgettaskresource->ref=GETPOST('ref','alpha');
		$objBudgettaskresource->fk_user_create=GETPOST('fk_user_create','int');
		$objBudgettaskresource->fk_user_mod=GETPOST('fk_user_mod','int');
		$objBudgettaskresource->code_structure=GETPOST('code_structure','alpha');
		$objBudgettaskresource->fk_product=GETPOST('fk_product','int');
		$objBudgettaskresource->fk_product_budget=GETPOST('fk_product_budget','int');
		$objBudgettaskresource->fk_budget_task_comple=GETPOST('fk_budget_task_comple','int');
		$objBudgettaskresource->detail=GETPOST('detail','alpha');
		$objBudgettaskresource->fk_unit=GETPOST('fk_unit','int');
		$objBudgettaskresource->quant=GETPOST('quant','alpha');
		$objBudgettaskresource->percent_prod=GETPOST('percent_prod','alpha');
		$objBudgettaskresource->amount_noprod=GETPOST('amount_noprod','alpha');
		$objBudgettaskresource->amount=GETPOST('amount','alpha');
		$objBudgettaskresource->rang=GETPOST('rang','int');
		$objBudgettaskresource->priority=GETPOST('priority','int');
		$objBudgettaskresource->formula=GETPOST('formula','alpha');
		$objBudgettaskresource->formula_res=GETPOST('formula_res','alpha');
		$objBudgettaskresource->formula_quant=GETPOST('formula_quant','alpha');
		$objBudgettaskresource->formula_factor=GETPOST('formula_factor','alpha');
		$objBudgettaskresource->formula_prod=GETPOST('formula_prod','alpha');
		$objBudgettaskresource->status=GETPOST('status','int');



		if (empty($objBudgettaskresource->ref))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objBudgettaskresource->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/list.php',1);
				header("Location: ".$urltogo);
				exit;
			}
			{
				// Creation KO
				if (! empty($objBudgettaskresource->errors)) setEventMessages(null, $objBudgettaskresource->errors, 'errors');
				else  setEventMessages($objBudgettaskresource->error, null, 'errors');
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


		$objBudgettaskresource->fk_budget_task=$id;
		$objBudgettaskresource->ref=GETPOST('ref','alpha');
		$objBudgettaskresource->fk_user_mod=GETPOST('fk_user_mod','int');
		//$objBudgettaskresource->code_structure=GETPOST('code_structure','alpha');
		//$objBudgettaskresource->fk_product=GETPOST('fk_product','int');
		//$objBudgettaskresource->fk_product_budget=GETPOST('fk_product_budget','int');
		//$objBudgettaskresource->fk_budget_task_comple=GETPOST('fk_budget_task_comple','int');
		$objBudgettaskresource->detail=GETPOST('detail','alpha');
		$objBudgettaskresource->fk_unit=GETPOST('fk_unit','int');
		$objBudgettaskresource->quant=GETPOST('quant','alpha');
		$objBudgettaskresource->percent_prod=GETPOST('percent_prod','alpha');
		$objBudgettaskresource->amount_noprod=GETPOST('amount_noprod','alpha');
		$objBudgettaskresource->amount=GETPOST('amount','alpha');
		//$objBudgettaskresource->rang=GETPOST('rang','int');
		//$objBudgettaskresource->priority=GETPOST('priority','int');
		$objBudgettaskresource->formula=GETPOST('formula','alpha');
		//$objBudgettaskresource->formula_res=GETPOST('formula_res','alpha');
		//$objBudgettaskresource->formula_quant=GETPOST('formula_quant','alpha');
		//$objBudgettaskresource->formula_factor=GETPOST('formula_factor','alpha');
		//$objBudgettaskresource->formula_prod=GETPOST('formula_prod','alpha');
		//$objBudgettaskresource->status=GETPOST('status','int');



		if (empty($objBudgettaskresource->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objBudgettaskresource->update($user);
			if ($result > 0)
			{
				$action='view';
			}
			else
			{
				// Creation KO
				if (! empty($objBudgettaskresource->errors)) setEventMessages(null, $objBudgettaskresource->errors, 'errors');
				else setEventMessages($objBudgettaskresource->error, null, 'errors');
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
		if ($objBudgettaskresource->id)
		{
			$db->begin();
			$filter = " AND t.fk_budget_task = ".$objBudgettaskproduction->fk_budget_task;
			$filter.= " AND t.fk_product_budget = ".$objBudgettaskproduction->fk_product_budget;
			$res = $objBudgettaskproduction->fetchAll('','',0,0,array(),'AND',$filter);
			if ($res>0)
			{
				$lines = $objBudgettaskproduction->lines;
				foreach ($lines AS $j => $line)
				{
					$res = $objBudgettaskproduction->fetch($line->id);
					if ($res==1)
					{
						$res = $objBudgettaskproduction->delete($user);
						if ($res<=0)
						{
							$error++;
							setEventMessages($objBudgettaskproduction->error,$objBudgettaskproduction->errors,'errors');
						}
					}
				}
			}
			$result=$objBudgettaskresource->delete($user);
			if ($result <= 0)
			{
				$error++;
				if (! empty($objBudgettaskresource->errors)) setEventMessages(null, $objBudgettaskresource->errors, 'errors');
				else setEventMessages($objBudgettaskresource->error, null, 'errors');
			}
			if (!$error)
			{
				$db->commit();
				setEventMessages("RecordDeleted", null, 'mesgs');
				header("Location: ".dol_buildpath('/budget/budget/supplies.php?id='.$id,1));
				exit;
			}
			else
			{
				$db->rollback();
				$action = '';
			}
		}
	}
}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('',$langs->trans('Budgettaskresource'),'');

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
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_budget").'</td><td><input class="flat" type="text" name="fk_budget" value="'.GETPOST('fk_budget').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_task").'</td><td><input class="flat" type="text" name="fk_task" value="'.GETPOST('fk_task').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_task_parent").'</td><td><input class="flat" type="text" name="fk_task_parent" value="'.GETPOST('fk_task_parent').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.GETPOST('label').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fielddescription").'</td><td><input class="flat" type="text" name="description" value="'.GETPOST('description').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldduration_effective").'</td><td><input class="flat" type="text" name="duration_effective" value="'.GETPOST('duration_effective').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldplanned_workload").'</td><td><input class="flat" type="text" name="planned_workload" value="'.GETPOST('planned_workload').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprogress").'</td><td><input class="flat" type="text" name="progress" value="'.GETPOST('progress').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpriority").'</td><td><input class="flat" type="text" name="priority" value="'.GETPOST('priority').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_creat").'</td><td><input class="flat" type="text" name="fk_user_creat" value="'.GETPOST('fk_user_creat').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_valid").'</td><td><input class="flat" type="text" name="fk_user_valid" value="'.GETPOST('fk_user_valid').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_statut").'</td><td><input class="flat" type="text" name="fk_statut" value="'.GETPOST('fk_statut').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnote_private").'</td><td><input class="flat" type="text" name="note_private" value="'.GETPOST('note_private').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnote_public").'</td><td><input class="flat" type="text" name="note_public" value="'.GETPOST('note_public').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrang").'</td><td><input class="flat" type="text" name="rang" value="'.GETPOST('rang').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodel_pdf").'</td><td><input class="flat" type="text" name="model_pdf" value="'.GETPOST('model_pdf').'"></td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}



// Part to edit record
if ($abc && ($id || $ref) && $action == 'edit')
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
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_budget").'</td><td><input class="flat" type="text" name="fk_budget" value="'.$object->fk_budget.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_task").'</td><td><input class="flat" type="text" name="fk_task" value="'.$object->fk_task.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_task_parent").'</td><td><input class="flat" type="text" name="fk_task_parent" value="'.$object->fk_task_parent.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.$object->label.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fielddescription").'</td><td><input class="flat" type="text" name="description" value="'.$object->description.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldduration_effective").'</td><td><input class="flat" type="text" name="duration_effective" value="'.$object->duration_effective.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldplanned_workload").'</td><td><input class="flat" type="text" name="planned_workload" value="'.$object->planned_workload.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprogress").'</td><td><input class="flat" type="text" name="progress" value="'.$object->progress.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpriority").'</td><td><input class="flat" type="text" name="priority" value="'.$object->priority.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_creat").'</td><td><input class="flat" type="text" name="fk_user_creat" value="'.$object->fk_user_creat.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_valid").'</td><td><input class="flat" type="text" name="fk_user_valid" value="'.$object->fk_user_valid.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_statut").'</td><td><input class="flat" type="text" name="fk_statut" value="'.$object->fk_statut.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnote_private").'</td><td><input class="flat" type="text" name="note_private" value="'.$object->note_private.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnote_public").'</td><td><input class="flat" type="text" name="note_public" value="'.$object->note_public.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrang").'</td><td><input class="flat" type="text" name="rang" value="'.$object->rang.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodel_pdf").'</td><td><input class="flat" type="text" name="model_pdf" value="'.$object->model_pdf.'"></td></tr>';

	print '</table>';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}

// Part to show record
if ($object->id > 0)
{
	$objBudgettaskadd->fetch(0,$object->id);
	$res = $object->fetch_optionals($object->id, $extralabels);
	$head = budgettask_prepare_head($object, $user);
	$titre=$langs->trans("Budgettask");
	$picto='budget';
	$getcard = 'supplies';
	dol_fiche_head($head, $getcard, $titre, 0, $picto);



	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>'.$object->label.'</td></tr>';
	//
//print '<tr><td>'.$langs->trans("Fieldentity").'</td><td>'.$object->entity.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldref").'</td><td>'.$object->ref.'</td></tr>';
	$objBudget->fetch($object->fk_budget);
	print '<tr><td>'.$langs->trans("Fieldfk_budget").'</td><td>'.$objBudget->getNomUrl(1).'</td></tr>';
	if ($object->fk_task>0)
	{
		$objItems->fetch($object->fk_task);
		print '<tr><td>'.$langs->trans("Fieldfk_task").'</td><td>'.$objItems->getNomUrl(1).'</td></tr>';
	}
	//print '<tr><td>'.$langs->trans("Fieldfk_task_parent").'</td><td>'.$object->fk_task_parent.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldlabel").'</td><td>'.$object->label.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldhour_production").'</td><td>'.price2num($objBudgettaskadd->hour_production,$general->decimal_total).'</td></tr>';
	//print '<tr><td>'.$langs->trans("Fielddescription").'</td><td>'.$object->description.'</td></tr>';
	//print '<tr><td>'.$langs->trans("Fieldduration_effective").'</td><td>'.$object->duration_effective.'</td></tr>';
	//print '<tr><td>'.$langs->trans("Fieldplanned_workload").'</td><td>'.$object->planned_workload.'</td></tr>';
	//print '<tr><td>'.$langs->trans("Fieldprogress").'</td><td>'.$object->progress.'</td></tr>';
	//print '<tr><td>'.$langs->trans("Fieldpriority").'</td><td>'.$object->priority.'</td></tr>';
	//print '<tr><td>'.$langs->trans("Fieldfk_user_creat").'</td><td>'.$object->fk_user_creat.'</td></tr>';
//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_valid").'</td><td>'.$object->fk_user_valid.'</td></tr>';
	//..print '<tr><td>'.$langs->trans("Fieldfk_statut").'</td><td>'.$object->libStatut($object->fk_statut,6).'</td></tr>';
	//..print '<tr><td>'.$langs->trans("Fieldnote_private").'</td><td>'.$object->note_private.'</td></tr>';
	//print '<tr><td>'.$langs->trans("Fieldnote_public").'</td><td>'.$object->note_public.'</td></tr>';
//print '<tr><td>'.$langs->trans("Fieldrang").'</td><td>'.$object->rang.'</td></tr>';
//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodel_pdf").'</td><td>'.$object->model_pdf.'</td></tr>';

	print '</table>';

	dol_fiche_end();


	//incluimos las pestanas de configuración
	if ($idr>0)
		include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/budgettaskresource_card.tpl.php';
	else
		include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/budgettaskresource_list.tpl.php';

}


// End of page
llxFooter();
$db->close();
