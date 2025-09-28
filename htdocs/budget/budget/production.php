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
dol_include_once('/budget/class/budgetext.class.php');
dol_include_once('/budget/class/itemsext.class.php');
dol_include_once('/budget/class/budgettaskresourceext.class.php');
dol_include_once('/budget/lib/budget.lib.php');
dol_include_once('/budget/class/puvariablesext.class.php');
dol_include_once('/budget/class/budgettaskproduction.class.php');
dol_include_once('/product/class/product.class.php');

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
$objPuvariables = new Puvariables($db);
$objProduct = new Product($db);
$objBudgettaskproduction=new Budgettaskproduction($db);
$objBudgettaskadd = new Budgettaskaddext($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('budgettask'));

$aStrbudget = unserialize($_SESSION['aStrbudget']);
if ($object->id) $objBudget->fetch($object->fk_budget);

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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/budget/production.php?id='.$id,1);
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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/budget/production.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */

		$object->entity=GETPOST('entity','int');
		$object->ref=GETPOST('ref','alpha');
		$object->fk_budget=GETPOST('fk_budget','int');
		$object->fk_task=GETPOST('fk_task','int');
		$object->fk_task_parent=GETPOST('fk_task_parent','int');
		$object->label=GETPOST('label','alpha');
		$object->description=GETPOST('description','alpha');
		$object->duration_effective=GETPOST('duration_effective','alpha');
		$object->planned_workload=GETPOST('planned_workload','alpha');
		$object->progress=GETPOST('progress','int');
		$object->priority=GETPOST('priority','int');
		$object->fk_user_creat=GETPOST('fk_user_creat','int');
		$object->fk_user_valid=GETPOST('fk_user_valid','int');
		$object->fk_statut=GETPOST('fk_statut','int');
		$object->note_private=GETPOST('note_private','alpha');
		$object->note_public=GETPOST('note_public','alpha');
		$object->rang=GETPOST('rang','int');
		$object->model_pdf=GETPOST('model_pdf','alpha');

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

	// Action to update record
	if ($action == 'update')
	{
		$error=0;
		$error=0;
		$variable = GETPOST('variable');
		$commander = GETPOST('commander');
		$aUnits = GETPOST('aUnits');

		$db->begin();
		foreach ($variable AS $fk_product_budget => $aData)
		{
			$res = $objBudgettaskresource->fetch(0,null,$id,$fk_product_budget);
			if ($res==1)
			{
				if ($objBudgettaskresource->fk_product_budget == $commander)
					$objBudgettaskresource->commander = 1;
				else
					$objBudgettaskresource->commander = 0;
				$objBudgettaskresource->fk_user_mod = $user->id;
				$objBudgettaskresource->datem = $now;
				$objBudgettaskresource->tms = $now;
				$res = $objBudgettaskresource->update($user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($objBudgettaskresource->error,$objBudgettaskresource->errors,'errors');
				}
			}
			if (!$error)
			{
				foreach ($aData AS $j => $value)
				{
					if(empty($value)) $value=0;

					//vamos a buscar si esta registrado la combinación item, fk_variable=$j, $fk_product_budget
					$resip = $objBudgettaskproduction->fetch(0,$id,$j,$fk_product_budget);
					if ($resip==1)
					{
						//actualizamos
						$objBudgettaskproduction->quantity=$value+0;
						if (empty($objBudgettaskproduction->quantity))$objBudgettaskproduction->quantity=0;
					}
					elseif(empty($resip))
					{
					//creamos
						$objBudgettaskproduction->fk_item=$id;
						$objBudgettaskproduction->fk_variable=$j;
						$objBudgettaskproduction->fk_items_product=$fk_items_product;
						$objBudgettaskproduction->quantity=$value+0;
						if (empty($objBudgettaskproduction->quantity))$objBudgettaskproduction->quantity=0;
						$objBudgettaskproduction->fk_user_create=$user->id;
						$objBudgettaskproduction->datec=$now;
						$objBudgettaskproduction->active = 1;
					}
					else
					{
						$error++;
						setEventMessages($objBudgettaskproduction->error,$objBudgettaskproduction->errors,'errors');
					}
					$objBudgettaskproduction->fk_user_mod=$user->id;
					$objBudgettaskproduction->datem=$now;
					$objBudgettaskproduction->tms=$now;
					$objBudgettaskproduction->status=1;
					if ($resip==1)
					{
						$result=$objBudgettaskproduction->update($user);
					}
					if (empty($resip))
					{
						$result=$objBudgettaskproduction->create($user);
					}

					if ($result<=0)
					{
						$error++;
						setEventMessages($objBudgettaskproduction->error,$objBudgettaskproduction->errors,'errors');
					}
				}
			}
			if ($aUnits[$fk_items_product])
			{
				//actualizamos las unidades
				//$res = $objItemsproduct->fetch($fk_items_product);
				if ($res==1)
				{
					$objBudgettaskresource->units = $aUnits[$fk_items_product]+0;
					$objBudgettaskresource->fk_user_mod = $user->id;
					$objBudgettaskresource->datem = $now;
					$objBudgettaskresource->tms = $now;
					$res = $objBudgettaskresource->update($user);
					if ($res <=0)
					{
						$error++;
						setEventMessages($objBudgettaskresource->error,$objBudgettaskresource->errors,'errors');
					}
				}
			}
		}
		//echo '<hr>err '.$error;exit;
		if (!$error)
		{
			$db->commit();
			setEventMessages($langs->trans('Saverecords'),null,'mesgs');
			header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id);
			exit;
		}
		else
		{
			$db->rollback();
		}
		$action = 'edit';
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



// Part to show record
if ($object->id > 0)
{
	$objBudgettaskadd->fetch(0,$object->id);
	$res = $object->fetch_optionals($object->id, $extralabels);
	$head = budgettask_prepare_head($object, $user);
	$titre=$langs->trans("Budgettask");
	$picto='budget';
	$getcard = 'production';
	dol_fiche_head($head, $getcard, $titre, 0, $picto);


	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('DeleteMyOjbect'), $langs->trans('ConfirmDeleteMyObject'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}

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
	//print '<tr><td>'.$langs->trans("Fieldfk_statut").'</td><td>'.$object->libStatut($object->fk_statut,6).'</td></tr>';
	//print '<tr><td>'.$langs->trans("Fieldnote_private").'</td><td>'.$object->note_private.'</td></tr>';
	//print '<tr><td>'.$langs->trans("Fieldnote_public").'</td><td>'.$object->note_public.'</td></tr>';
	//print '<tr><td>'.$langs->trans("Fieldrang").'</td><td>'.$object->rang.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodel_pdf").'</td><td>'.$object->model_pdf.'</td></tr>';

	print '</table>';

	dol_fiche_end();


	//incluimos las pestanas de configuración

	include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/budgettaskproduction_card.tpl.php';

}


// End of page
llxFooter();
$db->close();
