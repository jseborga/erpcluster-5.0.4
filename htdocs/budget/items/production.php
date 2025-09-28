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
dol_include_once('/budget/class/itemsregion.class.php');
dol_include_once('/budget/class/ctypeitemext.class.php');
dol_include_once('/budget/class/puvariablesext.class.php');
dol_include_once('/budget/class/itemsproduct.class.php');
dol_include_once('/budget/class/itemsproductregion.class.php');
dol_include_once('/budget/class/itemsproduction.class.php');
dol_include_once('/user/class/user.class.php');
dol_include_once('/product/class/product.class.php');
dol_include_once('/budget/lib/budget.lib.php');
dol_include_once('/budget/lib/utils.lib.php');

dol_include_once('/orgman/class/cregiongeographic.class.php');
dol_include_once('/orgman/class/productregionprice.class.php');
dol_include_once('/orgman/class/cclasfin.class.php');

// Load traductions files requiredby by page
$langs->load("budget");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$ref 		= GETPOST('ref','alpha');
if (empty($ref)) $ref = NULL;

$idr		= GETPOST('idr','int');
$action		= GETPOST('action','alpha');
$cancel     = GETPOST('cancel');
$confirm    = GETPOST('confirm');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');

//vamos a colocar en session si esta seleccionado
if (isset($_GET['fk_region']) || isset($_POST['fk_region']))
{
	$_SESSION['selitem'][$id]['fk_region'] = GETPOST('fk_region');
}
if (isset($_GET['fk_sector']) || isset($_POST['fk_sector']))
{
	$_SESSION['selitem'][$id]['fk_sector'] = GETPOST('fk_sector');
}

$fk_region = $_SESSION['selitem'][$id]['fk_region'];
$fk_sector = $_SESSION['selitem'][$id]['fk_sector'];
if (empty($fk_region))
{
	if(isset($_SESSION['selitem']['fk_region']))
		$fk_region = $_SESSION['selitem']['fk_region'];
}

if (empty($fk_sector))
{
	if(isset($_SESSION['selitem']['fk_sector']))
		$fk_sector = $_SESSION['selitem']['fk_sector'];
}

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

$nDecimal = ($conf->global->ITEMS_DEFAULT_NUMBER_DECIMAL?$conf->global->ITEMS_DEFAULT_NUMBER_DECIMAL:8);
if (empty($nDecimal)) $nDecimal = 8;
$aGroup = get_group_structure();

if (empty($action) && empty($id) && empty($ref)) $action='view';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
//$result = restrictedArea($user, 'budget', $id);


$object = new Itemsgroupext($db);
$objItem = new Itemsext($db);
$objItemsregion = new Itemsregion($db);
$objCtypeitem = new Ctypeitemext($db);
$extrafields = new ExtraFields($db);
$objItemsproduct = new Itemsproduct($db);
$objItemsproduction = new Itemsproduction($db);
$objUser = new User($db);
$objProduct = new Product($db);
$objPuvariables = new Puvariablesext($db);
$objTmp = new PuvariablesLine($db);
$objItemsproductregion = new Itemsproductregion($db);

$objCregiongeographic = new Cregiongeographic($db);
$objCclasfin = new Cclasfin($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals
if ($object->fk_item>0)
{
	$objItem->fetch($object->fk_item);
}
// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('items'));



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$now=dol_now();

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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/items/production.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}
		$action='';
	}

	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/items/production.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */

		$objItemsproduction->fk_item=$object->fk_item;
		$objItemsproduction->fk_variable=GETPOST('fk_variable','int');
		$objItemsproduction->fk_items_product=GETPOST('fk_items_product','int');
		$objItemsproduction->quantity=GETPOST('quantity','alpha');
		$objItemsproduction->fk_user_create=GETPOST('fk_user_create','int');
		$objItemsproduction->fk_user_mod=GETPOST('fk_user_mod','int');
		$objItemsproduction->status=GETPOST('status','int');

		if (empty($objItemsproduction->ref))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objItemsproduction->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/items/production.php?id='.$id,1);
				header("Location: ".$urltogo);
				exit;
			}
			{
				// Creation KO
				if (! empty($objItemsproduction->errors)) setEventMessages(null, $objItemsproduction->errors, 'errors');
				else  setEventMessages($objItemsproduction->error, null, 'errors');
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
		$variable = GETPOST('variable');
		$commander = GETPOST('commander');
		$aUnits = GETPOST('aUnits');

		$db->begin();
		foreach ($variable AS $fk_items_product => $aData)
		{
			$res = $objItemsproduct->fetch($fk_items_product);
			$resr = $objItemsproductregion->fetch(0,$fk_items_product,$fk_region,$fk_sector);
			if ($resr==1)
			{
				if ($objItemsproductregion->fk_item_product == $commander)
					$objItemsproductregion->commander = 1;
				else
					$objItemsproductregion->commander = 0;
				$objItemsproductregion->fk_user_mod = $user->id;
				$objItemsproductregion->datem = $now;
				$objItemsproductregion->tms = $now;
				$res = $objItemsproductregion->update($user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($objItemsproductregion->error,$objItemsproductregion->errors,'errors');
				}
			}
			else
			{
				$error++;
				setEventMessages($objItemsproductregion->error,$objItemsproductregion->errors,'errors');
			}
			if (!$error)
			{
				foreach ($aData AS $j => $value)
				{
					if(empty($value)) $value=0;

					//vamos a buscar si esta registrado la combinación
					$resip = $objItemsproduction->fetch(0,$object->fk_item,$j,$fk_items_product,$fk_region,$fk_sector);
					if ($resip==1)
					{
						//actualizamos
						$objItemsproduction->quantity=$value+0;
						if (empty($objItemsproduction->quantity))$objItemsproduction->quantity=0;
					}
					elseif(empty($resip))
					{
						//creamos
						$objItemsproduction->fk_item=$object->fk_item;
						$objItemsproduction->fk_variable=$j;
						$objItemsproduction->fk_items_product=$fk_items_product;
						$objItemsproduction->fk_region=$fk_region;
						$objItemsproduction->fk_sector=$fk_sector;
						$objItemsproduction->quantity=$value+0;
						if (empty($objItemsproduction->quantity))$objItemsproduction->quantity=0;
						$objItemsproduction->fk_user_create=$user->id;
						$objItemsproduction->datec=$now;
						$objItemsproduction->active = 1;
					}
					else
					{
						$error++;
						setEventMessages($objItemsproduction->error,$objItemsproduction->errors,'errors');
					}
					$objItemsproduction->fk_user_mod=$user->id;
					$objItemsproduction->datem=$now;
					$objItemsproduction->tms=$now;
					$objItemsproduction->status=1;
					if ($resip==1)
					{
						$result=$objItemsproduction->update($user);
					}
					if (empty($resip))
					{
						$result=$objItemsproduction->create($user);
					}

					if ($result<=0)
					{
						$error++;
						setEventMessages($objItemsproduction->error,$objItemsproduction->errors,'errors');
					}
				}
			}
			if ($aUnits[$fk_items_product])
			{
				//actualizamos el commandante
				//$res = $objItemsproduct->fetch($fk_items_product);
				if ($res==1)
				{
					$objItemsproduct->units = $aUnits[$fk_items_product]+0;
					$objItemsproduct->fk_user_mod = $user->id;
					$objItemsproduct->datem = $now;
					$objItemsproduct->tms = $now;
					$res = $objItemsproduct->update($user);
					if ($res <=0)
					{
						$error++;
						setEventMessages($objItemsproduct->error,$objItemsproduct->errors,'errors');
					}
				}
			}
		}

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
		$result=$objItemsproduction->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/budget/items/production.php?id='.$id,1));
			exit;
		}
		else
		{
			if (! empty($objItemsproduction->errors)) setEventMessages(null, $objItemsproduction->errors, 'errors');
			else setEventMessages($objItemsproduction->error, null, 'errors');
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
//armamos las instituiones en un array
$res = $objCclasfin->fetchAll('ASC','t.label',0,0,array('active'=>1),'AND,$filter');
if ($res>0)
{
	$lines = $objCclasfin->lines;
	foreach ($lines AS $j => $line)
		$aInstitutional[$line->id] = $line->label.' ('.$line->ref.')';
}

/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/
$aArrcss= array('/budget/css/style.css');
$help_url='EN:Module_Budget_En|FR:Module_Budget|ES:M&oacute;dulo_Presupuesto';

llxHeader("",$langs->trans("Items"),$help_url,'','','',$aArrjs,$aArrcss);

$form=new Formv($db);



// Part to show record
if ($object->id > 0)
{
	$res = $object->fetch_optionals($object->id, $extralabels);
	$head = budgetitem_prepare_head($object,$user);

	dol_fiche_head($head, 'production', $langs->trans("Item"),0,'item');
	$linkback = '<a href="'.DOL_URL_ROOT.'/budget/items/list.php">'.$langs->trans("BackToList").'</a>';

	$shownav = 1;
	if ($user->societe_id && ! in_array('budget', explode(',',$conf->global->MAIN_MODULES_FOR_EXTERNAL))) $shownav=0;
	$object->picto = 'projecttask';
	dol_banner_tab($object, 'ref', $linkback, $shownav, 'ref');

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('DeleteMyOjbect'), $langs->trans('ConfirmDeleteMyObject'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}

	if (! empty($conf->use_javascript_ajax))
	{
		print "\n".'<script type="text/javascript">';
		print '$(document).ready(function () {
			$("#fk_region").change(function() {
				document.formitem.action.value="";
				document.formitem.submit();
			});
			$("#fk_sector").change(function() {
				document.formitem.action.value="";
				document.formitem.submit();
			});
		});';
		print '</script>'."\n";
	}
	print '<form name="formitem" method="post" action="'.$_SERVER['PHP_SELF'].'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';

	print '<table class="border centpercent">'."\n";
	//print '<tr><td>'.$langs->trans("Fieldref").'</td><td>'.$object->ref.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldversion").'</td><td>'.$object->version.'</td></tr>';

	//detail
	print '<tr><td>'.$langs->trans("Fielddetail").'</td><td>'.$object->detail.'</td></tr>';
	//print '<tr><td>'.$langs->trans("Fieldfk_unit").'</td><td>'.$object->fk_unit.'</td></tr>';
	// unidad
	$objTmp = new Puvariablesline($db);
	$objTmp->fk_unit = $object->fk_unit;
	print '<tr><td>'.$langs->trans("Fieldfk_unit").'</td><td>'.$objTmp->getLabelOfUnit().'</td></tr>';

	print '<tr><td>'.$langs->trans('Forinstitution').'</td><td>';
	print $form->selectarray('fk_sector',$aInstitutional,$fk_sector,1);
	print '</td></tr>';
	print '<tr><td>'.$langs->trans('Forregion').'</td><td>';
	print $form->selectarray('fk_region',$aRegiongeographic,$fk_region,1);
	print '</td></tr>';

	$resir = $objItemsregion->fetch(0,$object->fk_item,$fk_region,$fk_sector);
	if ($resir==1)
		print '<tr><td>'.$langs->trans("Fieldhour_production").'</td><td>'.$objItemsregion->hour_production.'</td></tr>';
	//amount
	//vamos a sumar los registrados si el estado esta en 0

	if ($object->status == 0)
	{
		$filter = " AND t.fk_item = ".$object->fk_item;
		$resip = $objItemsproduct->fetchAll('','',0,0,array(),'AND',$filter);
		$totalCost=0;
		$totalCostnoprod=0;
		if ($resip>0)
		{
			$lines = $objItemsproduct->lines;
			foreach ($lines AS $j => $line)
			{
				$resipr=$objItemsproductregion->fetch(0,$line->id,$fk_region,$fk_sector);
				if ($resipr==1)
				{
						if ($line->group_structure == 'MQ')
						{
							$objItemsproductregion->cost_direct = $objItemsproductregion->performance*($objItemsproductregion->price_productive/100)*$objItemsproductregion->amount+$objItemsproductregion->performance*(1-($objItemsproductregion->price_productive/100))*$objItemsproductregion->price_improductive;
						}
						else
						{
							$objItemsproductregion->cost_direct = $objItemsproductregion->performance*$objItemsproductregion->amount;
						}


					$totalCost+= $objItemsproductregion->cost_direct;
					$totalCostnoprod+= $objItemsproductregion->amount_noprod;
				}
			}
		}
	}
	else
	{
		$totalCost=$objItemsregion->amount;
		$totalCostnoprod=$objItemsregion->amount_noprod;
	}

	print '</table>';
	print '</form>';
	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
	if (empty($reshook))
	{
		print '<div class="inline-block divButAction"><a class="butAction" href="'.dol_buildpath('/budget/items/list.php',1).'">'.$langs->trans("Return").'</a></div>'."\n";
	}
	print '</div>'."\n";
	if ($fk_region>0 && $fk_sector>0)
		include DOL_DOCUMENT_ROOT.'/budget/items/tpl/itemsproduction_card.tpl.php';
}


// End of page
llxFooter();
$db->close();
