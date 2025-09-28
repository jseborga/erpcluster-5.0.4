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
 *   	\file       budget/items_card.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-04-17 16:51
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
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php');
dol_include_once('/budget/class/itemsext.class.php');
dol_include_once('/budget/class/itemsgroupext.class.php');
dol_include_once('/budget/class/itemsproduct.class.php');
dol_include_once('/budget/class/itemsproductregion.class.php');
dol_include_once('/budget/class/itemsproduction.class.php');
dol_include_once('/budget/class/ctypeitemext.class.php');
dol_include_once('/budget/class/puvariablesext.class.php');
dol_include_once('/budget/lib/budget.lib.php');

dol_include_once('/orgman/class/cregiongeographic.class.php');

// Load traductions files requiredby by page
$langs->load("budget");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$cancel     = GETPOST('cancel');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_ref_ext=GETPOST('search_ref_ext','alpha');
$search_fk_type_item=GETPOST('search_fk_type_item','int');
$search_type=GETPOST('search_type','int');
$search_detail=GETPOST('search_detail','alpha');
$search_fk_unit=GETPOST('search_fk_unit','int');
$search_especification=GETPOST('search_especification','alpha');
$search_plane=GETPOST('search_plane','alpha');
$search_quant=GETPOST('search_quant','alpha');
$search_amount=GETPOST('search_amount','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_status=GETPOST('search_status','int');

$fk_unit = GETPOST('units', 'int');
$fk_type_item = GETPOST('fk_type_item', 'int');




if (empty($action) && empty($id) && empty($ref)) $action='view';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
//$result = restrictedArea($user, 'budget', $id);


$object = new Itemsgroupext($db);
$objItem = new Itemsext($db);
$objTmp = new Itemsext($db);
$objTmpgroup = new Itemsgroupext($db);
$objCtypeitem = new Ctypeitemext($db);
$extrafields = new ExtraFields($db);
$objItemsproduct = new Itemsproduct($db);
$objItemsproductregion = new Itemsproductregion($db);
$objItemsproduction = new Itemsproduction($db);
$objPuvariables = new Puvariablesext($db);
$objCregiongeographic = new Cregiongeographic($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('items'));

if ($id>0 && $object->type==0)
{
	$res = $objItem->fetch($object->fk_item);
}

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$now=dol_now();

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	if ($cancel)
	{
		if ($action != 'addlink')
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/items/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}
		$action='';
	}

	// Action to add record
	if ($action == 'confirm_processfactor')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/items/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}
		$fk_variable = GETPOST('fk_variable');
		$fk_region = GETPOST('fk_region');
		$factor = GETPOST('factor','int');
		$error=0;
		//vamos a recuperar todas las variables de la region
		$filter = " AND t.fk_variable = ".$fk_variable;
		$filter.= " AND t.fk_region = ".$fk_region;
		$res = $objItemsproduction->fetchAll('','',0,0,array(),'AND',$filter);
		if ($res > 0)
		{
			$db->begin();
			$lines = $objItemsproduction->lines;
			foreach ($lines AS $j => $line)
			{
				if (!$error)
				{
					$res = $objItemsproduction->fetch ($line->id);
					if ($res == 1)
					{
						//actualizamos
						$objItemsproduction->quantity = $factor;
						$objItemsproduction->fk_user_mod = $user->id;
						$objItemsproduction->datem = $now;
						$objItemsproduction->tms = $now;
						$res = $objItemsproduction->update($user);
						if ($res <=0)
						{
							$error++;
							setEventMessages($objItemsproduction->error,$objItemsproduction->errors,'errors');
						}
					}
				}
			}
		if (!$error)
		{
			$db->commit();
			setEventMessages($langs->trans('Satisfactoryupdate'),null,'mesgs');
			header('Location: '.DOL_URL_ROOT.'/budget/items/list.php');
			exit;
		}
		else
			$db->rollback();
		$action = '';


		}


	}


}
//armamos las regiones en un array
$filter='';
$res = $objCregiongeographic->fetchAll('ASC','t.label',0,0,array('status'=>1),'AND,$filter');
if ($res>0)
{
	$lines = $objCregiongeographic->lines;
	foreach ($lines AS $j => $line)
		$aRegiongeographic[$line->id] = $line->label.' ('.$line->ref.')';
}

//vamos a recuperar todas la svariables
$filter = " AND t.status = 1";
$res = $objPuvariables->fetchAll('ASC','t.ref',0,0,array(),'AND',$filter);
if ($res > 0)
{
	$lines = $objPuvariables->lines;
	foreach ($lines AS $j => $line)
	{
		$aVariables[$line->id] = $line->ref.' '.$line->label;
	}
}

/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('','Items','');

$form=new Formv($db);


// Put here content of your page


//  fk_type_item
$filterstatic.= " ";
$num = $objCtypeitem->fetchAll('ASC','t.code',0,0,array(),'AND',$filterstatic);
if($num>0)
{
	$optionstype = '';
	$optionstype.= '<option value=-1>'.''.'</option>';
	$lines = $objCtypeitem->lines;
	foreach ($lines AS $i=>$line)
	{
		$selected = '';
		if (
			(GETPOST('fk_type_item')?GETPOST('fk_type_item'):$object->fk_type_item) == $line->id)
		{
			$selected = ' selected';
		}
		$optionstype.= '<option value="'.$line->id.'" '.$selected.'>'.$line->code.' - '.$line->label.'</option>';
	}
}

// type

if (! empty($conf->use_javascript_ajax))
{
	print "\n".'<script type="text/javascript">';
	print '$(document).ready(function () { $("#manual_performance").change(function() { document.formitem.action.value="'.$action.'";
	document.formitem.submit(); }); $("#type").change(function() { document.formitem.action.value="'.$action.'";
	document.formitem.submit(); }); $("#fk_item").change(function() { document.formitem.action.value="'.$action.'";
	document.formitem.submit(); }); });';
	print '</script>'."\n";
}

if ($action == 'processfactor')
{
	$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?fk_region=' . GETPOST('fk_region').'&fk_variable='.GETPOST('fk_variable').'&factor='.GETPOST('factor','int'), $langs->trans('Processfactor'), $langs->trans('ConfirmProcessfactor'), 'confirm_processfactor', '', 0, 2);
	print $formconfirm;
}
// Part to create
if ($action == 'create' || $action == 'processfactor')
{
	print load_fiche_titre($langs->trans("Items"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="processfactor">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$id.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";

	//region
	print '<tr><td class="fieldrequired">'.$langs->trans("Selectregion").'</td><td>';
	print $form->selectarray('fk_region',$aRegiongeographic,GETPOST('fk_region'),1);
	print '</td></tr>';

	//variable
	print '<tr><td class="fieldrequired">'.$langs->trans("Selectvariable").'</td><td>';
	print $form->selectarray('fk_variable',$aVariables,GETPOST('fk_variable'),1);
	print '</td></tr>';
	//factor
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvaluenew").'</td><td><input class="flat" type="number" min="0" step="any" name="factor" value="'.GETPOST('factor').'"></td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Process").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}



// End of page
llxFooter();
$db->close();
