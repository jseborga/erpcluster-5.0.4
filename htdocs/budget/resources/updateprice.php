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
 *   	\file       budget/itemsproduct_card.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-06-05 12:20
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
dol_include_once('/budget/class/itemsproductext.class.php');
dol_include_once('/budget/class/itemsproductregion.class.php');
dol_include_once('/budget/class/itemsext.class.php');

dol_include_once('/orgman/class/cregiongeographic.class.php');
dol_include_once('/orgman/class/cclasfin.class.php');

// Load traductions files requiredby by page
$langs->load("budget");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$cancel     = GETPOST('cancel');
$confirm = GETPOST('confirm');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');
$refproduct = GETPOST('refproduct','alpha');
$fk_region = GETPOST('fk_region','int');

$search_fk_item=GETPOST('search_fk_item','int');
$search_ref=GETPOST('search_ref','alpha');
$search_group_structure=GETPOST('search_group_structure','alpha');
$search_fk_product=GETPOST('search_fk_product','int');
$search_fk_unit=GETPOST('search_fk_unit','int');
$search_label=GETPOST('search_label','alpha');
$search_formula=GETPOST('search_formula','alpha');
$search_active=GETPOST('search_active','int');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_status=GETPOST('search_status','int');
$search_fk_region=GETPOST('search_fk_region','alpha');
$search_fk_sector=GETPOST('search_fk_sector','alpha');



if (empty($action) && empty($id) && empty($ref)) $action='view';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
//$result = restrictedArea($user, 'budget', $id);


$object = new Itemsproduct($db);
$extrafields = new ExtraFields($db);
$objCregiongeographic = new Cregiongeographic($db);
$objCclasfin = new Cclasfin($db);
$objItemsproductregion = new Itemsproductregion($db);
$objItems = new Itemsext($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('itemsproduct'));

//armamos las regiones en un array
$filter='';
$res = $objCregiongeographic->fetchAll('ASC','t.label',0,0,array('status'=>1),'AND,$filter');
if ($res>0)
{
	$lines = $objCregiongeographic->lines;
	foreach ($lines AS $j => $line)
	{
		$aRegiongeographic[$line->id] = $line->label.' ('.$line->ref.')';
		$aRegionref[$line->id] = $line->ref;
	}
}

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
		if ($id > 0 || ! empty($ref)) $ret = $object->fetch($id,$ref);
		$action='';
	}

	// Action to add record
	if ($action == 'confirm_add' && $confirm = 'yes' && $user->rights->budget->ite->updateres)
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}
		$aPost = unserialize($_SESSION['updatePrice']);
		$_POST = $aPost[$refproduct];
		$error=0;
		//vamos a actualizar los precios
		$aIds = $_POST['ids'];
		if (is_array($aIds) && count($aIds)>0)
		{
			$db->begin();
			foreach ($aIds AS $id)
			{
				$res = $objItemsproductregion->fetch($id);

				if ($res==1)
				{
					$objItemsproductregion->amount = GETPOST('newprice');
					if ($group_structure == 'MQ')
						$objItemsproductregion->amount_noprod = GETPOST('newpricenoprod');
					$res = $objItemsproductregion->update($user);
					if ($res <=0)
					{
						$error++;
						setEventMessages($objItemsproductregion->error,$objItemsproductregion->errors,'errors');
					}
				}
			}
			if (!$error)
			{
				$db->commit();
				setEventmessages($langs->trans('Successfullupdate'),null,'mesgs');
				header('Location: '.DOL_URL_ROOT.'/budget/resources/list.php?search_ref='.$refproduct.'&search_fk_region='.$aRegionref[$fk_region]);
				exit;
			}
			else
			{
				$db->rollback();
				$action = 'create';
			}
		}
	}
}



//armamos las instituiones en un array
$res = $objCclasfin->fetchAll('ASC','t.label',0,0,array('active'=>1),'AND,$filter');
if ($res>0)
{
	$lines = $objCclasfin->lines;
	foreach ($lines AS $j => $line)
		$aInstitutional[$line->id] = $line->label.' ('.$line->ref.')';
}

$selectProduct = array();
$group_structure = '';
//armamos los productos a actualizar
$filter = " AND status = 1";
$res = $object->fetchAll('ASC','t.ref',0,0,array('active'=>1),'AND,$filter');
if ($res>0)
{
	$lines = $object->lines;
	foreach ($lines AS $j => $line)
	{
		if (!empty($refproduct))
		{
			if ($line->ref == $refproduct)
			{
				$group_structure = $line->group_structure;
				$objItems->fetch($line->fk_item);
				if ($fk_region>0)
				{
					//buscamos en items product region
					$filter = " AND t.fk_item_product = ".$line->id;
					$filter.= " AND t.fk_region = ".$fk_region;
					$res = $objItemsproductregion->fetchAll('','',0,0,array(),'AND',$filter);
					if ($res > 0)
					{
						$linesipr = $objItemsproductregion->lines;
						foreach ($linesipr AS $k => $lineipr)
						{
							$selectProduct[$line->id]['id']= $lineipr->id;
							$selectProduct[$line->id]['item']= $objItems->getNomUrl();
							$selectProduct[$line->id]['label']= $objItems->detail;
							$selectProduct[$line->id]['amount']= $lineipr->amount;
							$selectProduct[$line->id]['amountnoprod']= $lineipr->amount_noprod;
						}
					}
					else
					{
						$selectProduct[$line->id]['item']= $objItems->getNomUrl();
						$selectProduct[$line->id]['label']= $objItems->detail;
					}
				}
				else
				{
					$selectProduct[$line->id]['item']= $objItems->getNomUrl();
					$selectProduct[$line->id]['label']= $objItems->detail;
				}
			}
		}
		$objTmp = new ItemsproductLineext($db);
		$objTmp->fk_unit = $line->fk_unit;
		$aProduct[$line->ref] = $line->ref.' - '.$line->label.' ('.$objTmp->getLabelOfUnit('short').')';
	}
}
/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('',$langs->trans('Updatepriceresource'),'');

$form=new Form($db);

// Part to create
if ($action == 'create' || $action == 'add')
{

	if ($action == 'add')
	{
		$aPost[$refproduct] = $_POST;
		$_SESSION['updatePrice'] = serialize($aPost);
		$formquestion = array(array('type'=>'hidden','name'=>'refproduct','value'=>$refproduct),array('type'=>'hidden','name'=>'group_structure','value'=>GETPOST('group_structure')));

		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?fk_region=' . $fk_region, $langs->trans('Updateprices'), $langs->trans('ConfirmUpdateprices').' '.$langs->trans('the').' '.$refproduct.' '.$langs->trans('For').' '.$aRegiongeographic[$fk_region], 'confirm_add', $formquestion, 1, 2);
		print $formconfirm;
	}

	if (! empty($conf->use_javascript_ajax))
	{
		print "\n".'<script type="text/javascript">';
		print '$(document).ready(function () {
			$("#refproduct").change(function() {
				document.formitem.action.value="'.$action.'";
				document.formitem.submit();
			});
			$("#fk_region").change(function() {
				document.formitem.action.value="'.$action.'";
				document.formitem.submit();
			});
		});';
		print '</script>'."\n";
	}
	print load_fiche_titre($langs->trans("Updatepriceresource"));

	print '<form name="formitem" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="group_structure" value="'.$group_structure.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	print '<tr><td class="fieldrequired">'.$langs->trans("Selecttheresource").'</td><td>';
	print $form->selectarray('refproduct',$aProduct,GETPOST('refproduct'),1);
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Selecttheregion").'</td><td>';
	print $form->selectarray('fk_region',$aRegiongeographic,GETPOST('fk_region'),1);
	print '</td></tr>';

	if (is_array($selectProduct) && count($selectProduct)>0)
	{
		print '<tr><td>'.$langs->trans("Registeredprices").'</td><td>';
		print '<table class="centpercent">';
		foreach ((array) $selectProduct AS $j => $data)
		{
			print '<input type="hidden" name="ids['.$data['id'].']" value="'.$data['id'].'">';
			print '<tr><td>'.$data['item'].'</td>';
			print '<td>'.$data['label'].'</td>';
			print '<td>'.$data['amount'].'</td>';
			$_POST['newprice'] = $data['amount'];
			if ($group_structure =='MQ')
			{
				print '<td>'.$data['amountnoprod'].'</td>';
				$_POST['newpricenoprod'] = $data['amountnoprod'];
			}
			print '</td></tr>';
		}
		print '</table>';
		print '</td></tr>';
	}

	print '<tr><td class="fieldrequired">'.$langs->trans("Newproductiveprice").'</td><td>';
	print '<input type="number" min="0" step="any" name="newprice" value="'.GETPOST('newprice').'">';
	print '</td></tr>';

	if ($group_structure =='MQ')
	{
		print '<tr><td class="fieldrequired">'.$langs->trans("Newunproductiveprice").'</td><td>';
		print '<input type="number"  min="0" step="any" name="newpricenoprod" value="'.GETPOST('newpricenoprod').'">';
		print '</td></tr>';
	}

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Updateprice").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}

// End of page
llxFooter();
$db->close();
