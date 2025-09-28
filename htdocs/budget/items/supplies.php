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
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';

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
dol_include_once('/budget/class/productasset.class.php');
dol_include_once('/budget/class/originsupplies.class.php');

dol_include_once('/orgman/class/cregiongeographic.class.php');
dol_include_once('/orgman/class/productregionprice.class.php');
dol_include_once('/orgman/class/cclasfin.class.php');
dol_include_once('/orgman/class/cdepartementsregion.class.php');

dol_include_once('/categories/class/categorie.class.php');

//vamos a vincular a la clase productext
if ($conf->productext->enabled)
	dol_include_once('/productext/class/productregionprice.class.php');

// Load traductions files requiredby by page
$langs->load("budget");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
if (empty($action))
{
	$ref 		= GETPOST('ref','alpha');
	if (empty($ref)) $ref = NULL;
}
$idr		= GETPOST('idr','int');
$fk_product = GETPOST('fk_product');
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
$objCtypeitem = new Ctypeitemext($db);
$extrafields = new ExtraFields($db);
$objItemsproduct = new Itemsproduct($db);
$objUser = new User($db);
$objProduct = new Product($db);
$objProductasset = new Productasset($db);
$objPuvariables = new Puvariables($db);
$objItemsproduction = new Itemsproduction($db);
$objItemsregion = new Itemsregion($db);
$objOriginsupplies = new Originsupplies($db);

$objItemsproductregion = new Itemsproductregion($db);
$objCregiongeographic = new Cregiongeographic($db);
$objCdepartementsregion =  new Cdepartementsregion($db);
$objCclasfin = new Cclasfin($db);
$objCategorie = new Categorie($db);

if ($conf->productext->enabled) $objProductregionprice = new Productregionprice($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';
// Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals
if ($object->fk_item>0)
{
	$objItem->fetch($object->fk_item);
}
// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('items'));

if ($idr>0)
{
	$objItemsproduct->fetch($idr);
	$fk_product=GETPOST('fk_product')?GETPOST('fk_product'):$objItemsproduct->fk_product;
	if ($fk_region > 0 && $fk_sector > 0)
	{
		$objItemsproductregion->fetch(0,$idr,$fk_region,$fk_sector);
	}
}

$aStrbudget = unserialize($_SESSION['aStrbudget']);

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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/items/supplies.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}
		if ($idr > 0 ) $ret = $objItemsproduct->fetch($id);
		$action='';
	}

	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/items/supplies.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;
		//vamos a verificar si existe formula en productasset
		if (GETPOST('fk_product')>0)
		{
			$resass = $objProductasset->fetch(0,GETPOST('fk_product'));
			if ($resass==1 && $objProductasset->formula)
				$_POST['formula'] = $objProductasset->formula;
		}
		/* object_prop_getpost_prop */
		$db->begin();

		//vamos a buscar si existe

		$filter = " AND t.fk_item = ".$object->fk_item;
		$filter.= " AND (";
		if (GETPOST('fk_product')>0)
			$filter.= " t.fk_product= ".GETPOST('fk_product');
		else
		{
			$filter.= " AND t.ref='".GETPOST('ref')."'";
		}
		$filter.= ")";
		$res = $objItemsproduct->fetchAll('','',0,0,array(),'AND',$filter,true);
		if ($res == 1)
		{
			$result = $objItemsproduct->id;
		}
		else
		{
			$objItemsproduct->ref=dol_string_nospecial(trim(STRTOUPPER(GETPOST('ref','alpha'))));
			$objItemsproduct->fk_item=$object->fk_item;
			$objItemsproduct->group_structure=GETPOST('group_structure','alpha');
			$objItemsproduct->fk_product=GETPOST('fk_product','int');
			if ($objItemsproduct->fk_product <0)$objItemsproduct->fk_product=0;
			if ($objItemsproduct->fk_product>0)
			{
				$objProduct->fetch($objItemsproduct->fk_product);
			}
			$objItemsproduct->label=(GETPOST('label','alpha')?GETPOST('label','alpha'):($objItemsproduct->fk_product?$objProduct->label:''));
			if (empty($objItemsproduct->fk_product)) $objItemsproduct->fk_product = 0;
			$objItemsproduct->units=GETPOST('units','int');
			$objItemsproduct->commander=0;
			$objItemsproduct->fk_unit=GETPOST('fk_unit','int');
			if (empty($objItemsproduct->fk_unit))$objItemsproduct->fk_unit=0;
			$objItemsproduct->formula=GETPOST('formula','alpha');
			$objItemsproduct->active=1;
			$objItemsproduct->fk_user_create=$user->id;
			$objItemsproduct->fk_user_mod=$user->id;
			$objItemsproduct->datec = $now;
			$objItemsproduct->datem = $now;
			$objItemsproduct->tms = $now;
			$objItemsproduct->status=1;

			if (empty($objItemsproduct->ref) && $objItemsproduct->fk_product>0)
			{
			//buscamos el producto y reemplazamos ref por objProduct->ref;
				$resprod = $objProduct->fetch($objItemsproduct->fk_product);
				if ($resprod==1)
					$objItemsproduct->ref = $objProduct->ref;
			}

			if (empty($objItemsproduct->group_structure) || $objItemsproduct->group_structure=='-1')
			{
				$error++;
				setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldgroup_structure")), null, 'errors');
			}
			if (empty($objItemsproduct->ref))
			{
				$error++;
				setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
			}
			if ($objItemsproduct->fk_product <=0)
			{
			//validamos que tenga valor el label
				if (empty($objItemsproduct->label))
				{
					$error++;
					setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldlabel")), null, 'errors');
				}
			}
			if (! $error)
			{
				$result=$objItemsproduct->create($user);
				if ($result <= 0)
				{
				// Creation KO
					if (! empty($objItemsproduct->errors)) setEventMessages(null, $objItemsproduct->errors, 'errors');
					else  setEventMessages($objItemsproduct->error, null, 'errors');
					$action='create';
				}
			}
		}
		if (!$error)
		{
			//creamos en la tabla items_product_region
			$objItemsproductregion->fk_item_product = $result;
			$objItemsproductregion->fk_region = $fk_region;
			$objItemsproductregion->fk_sector = $fk_sector;
			$objItemsproductregion->units = GETPOST('units','int');
			if ($objItemsproductregion->units <=0)$objItemsproductregion->units=0;
			$objItemsproductregion->commander = GETPOST('commander','int');
			if ($objItemsproductregion->commander <=0)$objItemsproductregion->commander=0;
			$objItemsproductregion->performance=price2num(GETPOST('performance','int'),$nDecimal);
			if (empty($objItemsproductregion->performance))$objItemsproductregion->performance=0;
			$objItemsproductregion->price_productive=price2num(GETPOST('price_productive','int'),$nDecimal);
			if ($objItemsproduct->group_structure!='MQ') $objItemsproductregion->price_productive=100;
			if (empty($objItemsproductregion->price_productive))$objItemsproductregion->price_productive=0;
			$objItemsproductregion->price_improductive=price2num(GETPOST('price_improductive','int'),$nDecimal);
			if (empty($objItemsproductregion->price_improductive))$objItemsproductregion->price_improductive=0;
			$objItemsproductregion->amount_noprod=price2num(GETPOST('amount_noprod','int'),$nDecimal);
			if (empty($objItemsproductregion->amount_noprod))$objItemsproductregion->amount_noprod=0;
			$objItemsproductregion->amount=price2num(GETPOST('amount','int'),$nDecimal);
			$objItemsproductregion->fk_origin=GETPOST('fk_origin','int');
			$objItemsproductregion->percent_origin=GETPOST('percent_origin','int');
			if (empty($objItemsproductregion->fk_origin)) $objItemsproductregion->fk_origin = 0;
			if (empty($objItemsproductregion->percent_origin)) $objItemsproductregion->percent_origin = 100;

			if (empty($objItemsproductregion->amount))
			{
				if(GETPOST('amount_new','int')>0)
					$objItemsproductregion->amount=GETPOST('amount_new','int');
				else
					$objItemsproductregion->amount=0;
			}
			//vamos a buscar en product_asset
			if($objItemsproduct->group_structure == 'MQ' && $objItemsproduct->fk_product>0)
			{
				//if ($object->manual_performance)
				//{
				$resass = $objProductasset->fetch(0,$objItemsproduct->fk_product);
				if ($resass==1)
				{
					if (empty($objItemsproductregion->amount_noprod))
						$objItemsproductregion->amount_noprod = $objProductasset->cost_pu_improductive;
					if (empty($objItemsproductregion->amount))
						$objItemsproductregion->amount = $objProductasset->cost_pu_productive;
				}
				//}
			}
			$objItemsproductregion->cost_direct = 0;
			$objItemsproductregion->fk_user_create = $user->id;
			$objItemsproductregion->fk_user_mod = $user->id;
			$objItemsproductregion->datec = $now;
			$objItemsproductregion->datem = $now;
			$objItemsproductregion->tms = $now;
			$objItemsproductregion->status = 1;
			$resr = $objItemsproductregion->create($user);
			if ($resr<=0)
			{
				$error++;
				setEventMessages($objItemsproductregion->error,$objItemsproductregion->errors,'errors');
			}
		}
		if (!$error)
		{
			$db->commit();
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/items/supplies.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}
		else
		{
			$db->rollback();
			$action = 'create';
		}
	}

	// Action to update record
	if ($action == 'update')
	{
		$error=0;
		//vamos a verificar si existe formula en productasset
		if (GETPOST('fk_product')>0)
		{
			$resass = $objProductasset->fetch(0,GETPOST('fk_product'));
			if ($resass==1 && $objProductasset->formula)
			{
				$formula = GETPOST('formula','alpha');
				if (empty($formula))
					$_POST['formula'] = $objProductasset->formula;
			}
		}

		$db->begin();
		//actualizamos production
		$var = GETPOST('var');
		if (isset($_POST['var']) && count($var)>0)
		{
			foreach ($var AS $j => $data)
			{
				foreach ($data AS $k => $value)
				{
					$res = $objItemsproduction->fetch(0,$object->fk_item,$j,$k,$fk_region,$fk_sector);
					if ($res==1)
					{
						//actualizamos
						$objItemsproduction->fk_user_mod = $user->id;
					}
					elseif($res==0)
					{
						//creamos
						$objItemsproduction->fk_item = $object->fk_item;
						$objItemsproduction->fk_variable = $j;
						$objItemsproduction->fk_items_product = $k;
						$objItemsproduction->fk_region = $fk_region;
						$objItemsproduction->fk_sector = $fk_sector;
						$objItemsproduction->active = 1;
						$objItemsproduction->fk_user_create=$user->id;
						$objItemsproduction->datec=$now;
						$objItemsproduction->status=1;
					}
					//echo '<hr>'.$id.' '.$j.' '.$k;
					//echo '<br>val '.
					$objItemsproduction->quantity=$value;
					if (empty($objItemsproduction->quantity)) $objItemsproduction->quantity=0;
					$objItemsproduction->datem=$now;
					$objItemsproduction->tms=$now;
					if ($res==1) $ress = $objItemsproduction->update($user);
					elseif($res==0) $ress = $objItemsproduction->create($user);
					if ($ress <=0)
					{
						$error++;
						setEventMessages($objItemsproduction->error,$objItemsproduction->errors,'errors');
					}
				}
			}
		}
		if (!$error)
		{
			$objItemsproduct->ref=dol_string_nospecial(trim(strtoupper(GETPOST('ref','alpha'))));
			$objItemsproduct->fk_item=$object->fk_item;
			$objItemsproduct->group_structure=GETPOST('group_structure','alpha');
			$objItemsproduct->fk_product=GETPOST('fk_product','int');
			if (empty($objItemsproduct->fk_product))$objItemsproduct->fk_product=0;
			$objItemsproduct->label=GETPOST('label','alpha');
			$objItemsproduct->units=GETPOST('units','int');
			if ($objItemsproduct->units <=0)$objItemsproduct->units=0;
			$objItemsproduct->fk_unit=GETPOST('fk_unit','int');
			if (empty($objItemsproduct->fk_unit))$objItemsproduct->fk_unit=0;
			$objItemsproduct->performance=price2num(GETPOST('performance','int'),$nDecimal);
			if (empty($objItemsproduct->performance))$objItemsproduct->performance=0;
			$objItemsproduct->formula=GETPOST('formula','alpha');
			$objItemsproduct->fk_user_mod=$user->id;
			$objItemsproduct->datem = $now;
			$objItemsproduct->tms = $now;

			if (empty($objItemsproduct->group_structure) || $objItemsproduct->group_structure=='-1')
			{
				$error++;
				setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldgroup_structure")), null, 'errors');
			}
			if (empty($objItemsproduct->ref))
			{
				$error++;
				setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
			}
			if ($objItemsproduct->fk_product <=0)
			{
				//validamos que tenga valor el label
				if (empty($objItemsproduct->label))
				{
					$error++;
					setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldlabel")), null, 'errors');
				}
			}
		}
		if (! $error)
		{
			$result=$objItemsproduct->update($user);
			if ($result <= 0)
			{
				$error++;
				// Creation KO
				if (! empty($objItemsproduct->errors)) setEventMessages(null, $objItemsproduct->errors, 'errors');
				else setEventMessages($objItemsproduct->error, null, 'errors');
				$action='edit';
			}
		}
		else
		{
			$action='edit';
		}


		if (!$error)
		{
			$lAdd=true;
			//buscamos si existe registro
			$resreg = $objItemsproductregion->fetch(0,$objItemsproduct->id,$fk_region,$fk_sector);
			if ($resreg==1)
			{
				$lAdd=false;
			//creamos en la tabla items_product_region
			//$objItemsproductregion->fk_item_product = $result;
			}
			else
			{
				$objItemsproductregion->fk_item_product = $objItemsproduct->id;
				$objItemsproductregion->fk_region = $fk_region;
				$objItemsproductregion->fk_sector = $fk_sector;
				$objItemsproductregion->fk_user_create = $user->id;
				$objItemsproductregion->datec = $now;
			}
			$objItemsproductregion->units = GETPOST('units','int');
			if ($objItemsproductregion->units <=0)$objItemsproductregion->units=0;
			$objItemsproductregion->commander = GETPOST('commander','int');
			if ($objItemsproductregion->commander <=0)$objItemsproductregion->commander=0;
			$objItemsproductregion->performance=price2num(GETPOST('performance','int'),$nDecimal);
			if (empty($objItemsproductregion->performance))$objItemsproductregion->performance=0;
			$objItemsproductregion->price_productive=price2num(GETPOST('price_productive','int'),$nDecimal);
			if ($objItemsproduct->group_structure!='MQ') $objItemsproductregion->price_productive=100;
			if (empty($objItemsproductregion->price_productive))$objItemsproductregion->price_productive=0;
			$objItemsproductregion->price_improductive=price2num(GETPOST('price_improductive','int'),$nDecimal);
			if (empty($objItemsproductregion->price_improductive))$objItemsproductregion->price_improductive=0;
			$objItemsproductregion->amount_noprod=price2num(GETPOST('amount_noprod','int'),$nDecimal);
			if (empty($objItemsproductregion->amount_noprod))$objItemsproductregion->amount_noprod=0;
			$objItemsproductregion->amount=price2num(GETPOST('amount','int'),$nDecimal);
			$objItemsproductregion->fk_origin=GETPOST('fk_origin','int');
			$objItemsproductregion->percent_origin=GETPOST('percent_origin','int');
			if (empty($objItemsproductregion->fk_origin)) $objItemsproductregion->fk_origin = 0;
			if (empty($objItemsproductregion->percent_origin)) $objItemsproductregion->percent_origin = 100;

			if (empty($objItemsproductregion->amount))
			{
				if(GETPOST('amount_new','int')>0)
					$objItemsproductregion->amount=GETPOST('amount_new','int');
				else
					$objItemsproductregion->amount=0;
			}

			//vamos a buscar en product_asset
			if($objItemsproduct->group_structure == 'MQ' && $objItemsproduct->fk_product>0)
			{
				$resass = $objProductasset->fetch(0,$objItemsproduct->fk_product);
				if ($resass==1)
				{
					if (empty($objItemsproductregion->amount_noprod))
						$objItemsproductregion->amount_noprod = price2num($objProductasset->cost_pu_improductive,$nDecimal);
					if (empty($objItemsproductregion->amount))
						$objItemsproductregion->amount = price2num($objProductasset->cost_pu_productive,$nDecimal);
				}
			}
			$objItemsproductregion->cost_direct=0;
			$objItemsproductregion->fk_user_mod = $user->id;
			$objItemsproductregion->datem = $now;
			$objItemsproductregion->tms = $now;
			$objItemsproductregion->status = 1;
			if ($lAdd) $resr = $objItemsproductregion->create($user);
			else $resr = $objItemsproductregion->update($user);
			if ($resr<=0)
			{
				$error++;
				setEventMessages($objItemsproductregion->error,$objItemsproductregion->errors,'errors');
			}
		}
		//echo $error;exit;
		if (!$error) $db->commit();
		else $db->rollback();
	}

	// Action to update record
	if ($action == 'updateline')
	{
		$error=0;
		//vamos a verificar si existe formula en productasset
		if (GETPOST('fk_product')>0)
		{
			$resass = $objProductasset->fetch(0,GETPOST('fk_product'));
			if ($resass==1 && $objProductasset->formula)
			{
				$formula = GETPOST('formula','alpha');
				if (empty($formula))
					$_POST['formula'] = $objProductasset->formula;
			}
		}
		$db->begin();
		if (!$error)
		{
			//$objItemsproduct->ref=GETPOST('ref','alpha');
			//$objItemsproduct->fk_item=$id;
			//$objItemsproduct->group_structure=GETPOST('group_structure','alpha');
			//$objItemsproduct->fk_product=GETPOST('fk_product','int');
			//if ($objItemsproduct->fk_product <0)$objItemsproduct->fk_product=0;
			//$objItemsproduct->label=GETPOST('label','alpha');
			$objItemsproduct->units=GETPOST('units','int');
			if ($objItemsproduct->units <=0)$objItemsproduct->units=0;
			$objItemsproduct->units=GETPOST('units','int');
			$objItemsproduct->fk_unit=GETPOST('fk_unit','int');
			if (empty($objItemsproduct->fk_unit))$objItemsproduct->fk_unit=0;
			$objItemsproduct->commander=GETPOST('commander','int');
			if (empty($objItemsproduct->commander))$objItemsproduct->commander=0;
			$objItemsproduct->performance=price2num(GETPOST('performance','int'),$nDecimal);
			if (empty($objItemsproduct->performance))$objItemsproduct->performance=0;
			$objItemsproduct->price_productive=GETPOST('price_productive','int');
			if (empty($objItemsproduct->price_productive))$objItemsproduct->price_productive=0;
			$objItemsproduct->price_improductive=GETPOST('price_improductive','int');
			if (empty($objItemsproduct->price_improductive))$objItemsproduct->price_improductive=0;
			$objItemsproduct->fk_user_mod=$user->id;
			$objItemsproduct->datem = $now;
			$objItemsproduct->tms = $now;

			if (empty($objItemsproduct->group_structure))
			{
				$error++;
				setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldgroup_structure")), null, 'errors');
			}
			if (empty($objItemsproduct->ref))
			{
				$error++;
				setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
			}
			if ($objItemsproduct->fk_product <=0)
			{
				//validamos que tenga valor el label
				if (empty($objItemsproduct->label))
				{
					$error++;
					setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldlabel")), null, 'errors');
				}
			}
		}

		if (!$error)
		{
			$lAdd=true;
			//buscamos si existe registro
			$resreg = $objItemsproductregion->fetch(0,$objItemsproduct->id,$fk_region,$fk_sector);
			if ($resreg==1)
			{
				$lAdd=false;
			//creamos en la tabla items_product_region
			//$objItemsproductregion->fk_item_product = $result;
			}
			else
			{
				$objItemsproductregion->fk_item_product = $objItemsproduct->id;
				$objItemsproductregion->fk_region = $fk_region;
				$objItemsproductregion->fk_sector = $fk_sector;
				$objItemsproductregion->fk_user_create = $user->id;
				$objItemsproductregion->datec = $now;
				$objItemsproductregion->amount_noprod=price2num(GETPOST('amount_noprod','int'),$nDecimal);
				if (empty($objItemsproductregion->amount_noprod))$objItemsproductregion->amount_noprod=0;
				$objItemsproductregion->amount=price2num(GETPOST('amount','int'),$nDecimal);
				if (empty($objItemsproductregion->amount))$objItemsproductregion->amount=0;
			}
			$objItemsproductregion->units = GETPOST('units','int');
			if ($objItemsproductregion->units <=0)$objItemsproductregion->units=0;
			$objItemsproductregion->commander = GETPOST('commander','int');
			if ($objItemsproductregion->commander <=0)$objItemsproductregion->commander=0;
			$objItemsproductregion->performance=price2num(GETPOST('performance','int'),$nDecimal);
			if (empty($objItemsproductregion->performance))$objItemsproductregion->performance=0;
			$objItemsproductregion->price_productive=price2num(GETPOST('price_productive','int'),$nDecimal);
			if (empty($objItemsproductregion->price_productive))$objItemsproductregion->price_productive=0;
			$objItemsproductregion->price_improductive=price2num(GETPOST('price_improductive','int'),$nDecimal);
			if (empty($objItemsproductregion->price_improductive))$objItemsproductregion->price_improductive=0;
			$objItemsproductregion->fk_origin=GETPOST('fk_origin','int');
			$objItemsproductregion->percent_origin=GETPOST('percent_origin','int');
			if (empty($objItemsproductregion->fk_origin)) $objItemsproductregion->fk_origin = 0;
			if (empty($objItemsproductregion->percent_origin)) $objItemsproductregion->percent_origin = 100;
			//$objItemsproductregion->amount_noprod=GETPOST('amount_noprod','int');
			//if (empty($objItemsproductregion->amount_noprod))$objItemsproductregion->amount_noprod=0;
			//$objItemsproductregion->amount=GETPOST('amount','int');
			//if (empty($objItemsproductregion->amount))$objItemsproductregion->amount=0;

			$objItemsproductregion->fk_user_mod = $user->id;
			$objItemsproductregion->datem = $now;
			$objItemsproductregion->tms = $now;
			$objItemsproductregion->status = 1;
			if ($lAdd) $resr = $objItemsproductregion->create($user);
			else $resr = $objItemsproductregion->update($user);
			if ($resr<=0)
			{
				$error++;
				setEventMessages($objItemsproductregion->error,$objItemsproductregion->errors,'errors');
			}
		}

		if (! $error)
		{
			$result=$objItemsproduct->update($user);
			if ($result > 0)
			{
				$action='view';
			}
			else
			{
				// Creation KO
				if (! empty($objItemsproduct->errors)) setEventMessages(null, $objItemsproduct->errors, 'errors');
				else setEventMessages($objItemsproduct->error, null, 'errors');
				$action='editline';
			}
		}
		else
		{
			$action='editline';
		}
		if (!$error)
		{
			$res = $objItemsproductregion->fetch(0,$objItemsproduct->id,$fk_region,$fk_sector);
			if ($res==1)
			{
				//creamos en la tabla items_product_region
				//$objItemsproductregion->fk_item_product = $result;
				//$objItemsproductregion->fk_region = $fk_region;
				//$objItemsproductregion->fk_sector = $fk_sector;
				$objItemsproductregion->units = GETPOST('units','int');
				if ($objItemsproductregion->units <=0)$objItemsproductregion->units=0;
				$objItemsproductregion->commander = GETPOST('commander','int');
				if ($objItemsproductregion->commander <=0)$objItemsproductregion->commander=0;
				$objItemsproductregion->performance=price2num(GETPOST('performance','int'),$nDecimal);
				if (empty($objItemsproductregion->performance))$objItemsproductregion->performance=0;
				$objItemsproductregion->price_productive=price2num(GETPOST('price_productive','int'),$nDecimal);
				if (empty($objItemsproductregion->price_productive))$objItemsproductregion->price_productive=0;
				$objItemsproductregion->price_improductive=price2num(GETPOST('price_improductive','int'),$nDecimal);
				if (empty($objItemsproductregion->price_improductive))$objItemsproductregion->price_improductive=0;
				$objItemsproductregion->amount_noprod=price2num(GETPOST('amount_noprod','int'),$nDecimal);
				if (empty($objItemsproductregion->amount_noprod))$objItemsproductregion->amount_noprod=0;
				$objItemsproductregion->amount=price2num(GETPOST('amount','int'),$nDecimal);
				if (empty($objItemsproductregion->amount))$objItemsproductregion->amount=0;
				$objItemsproductregion->fk_origin=GETPOST('fk_origin','int');
				$objItemsproductregion->percent_origin=GETPOST('percent_origin','int');
				if (empty($objItemsproductregion->fk_origin)) $objItemsproductregion->fk_origin = 0;
				if (empty($objItemsproductregion->percent_origin)) $objItemsproductregion->percent_origin = 100;

				$objItemsproductregion->fk_user_mod = $user->id;
				$objItemsproductregion->datem = $now;
				$objItemsproductregion->tms = $now;
				$objItemsproductregion->status = 1;
				$resr = $objItemsproductregion->update($user);
				if ($resr<=0)
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
		}
		if (!$error) $db->commit();
		else $db->rollback();
	}

	// Action to delete
	if ($action == 'confirm_delete')
	{
		//echo '<hr>empieza borrado ';
		//eliminamos todos los registros de itemsproductregion
		$db->begin();
		$filter =  " AND t.fk_items_product = ".$objItemsproduct->id;
		$res = $objItemsproductregion->fetchAll('','',0,0,array(),'AND',$filter);
		if ($res >0)
		{
			$lines = $objItemsproductregion->lines;
			foreach ($lines AS $j => $line)
			{
				$objItemsproductregion->fetch($line->id);
				$res = $objItemsproductregion->delete($user);
				if ($res<=0)
				{
					$error++;
					setEventMessages($objItemsproduct->error,$objItemsproduct->errors,'errors');
				}
			}
		}
		if (!$error)
		{
			$result=$objItemsproduct->delete($user);
			if ($result <= 0)
			{
				if (! empty($objItemsproduct->errors)) setEventMessages(null, $objItemsproduct->errors, 'errors');
				else setEventMessages($objItemsproduct->error, null, 'errors');
			}
		}
		if (!$error)
		{
			$db->commit();
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/budget/items/supplies.php?id='.$id,1));
			exit;
		}
		else
			$db->rollback();
		$action = '';
	}

	if ($fk_region>0 && $fk_sector>0)
	{
		//vamos a verificar y crear si no existe en items_region
		$resregion = $objItemsregion->fetch (0,$object->fk_item,$fk_region,$fk_sector);
		if (empty($resregion))
		{
			$db->begin();
			//creamos
			$objItemsregion->fk_item = $object->fk_item;
			$objItemsregion->fk_region = $fk_region;
			$objItemsregion->fk_sector = $fk_sector;
			$objItemsregion->hour_production = 0;
			$objItemsregion->amount_noprod = 0;
			$objItemsregion->amount = 0;
			$objItemsregion->fk_user_create = $user->id;
			$objItemsregion->fk_user_mod = $user->id;
			$objItemsregion->datec = $now;
			$objItemsregion->datem = $now;
			$objItemsregion->tms = $now;
			$objItemsregion->status = 1;
			$resregion = $objItemsregion->create($user);
			if ($resregion<=0)
			{
				$error++;
				setEventMessages($objItemsregion->error,$objItemsregion->errors,'errors');
			}
			if (!$error) $db->commit();
			else $db->rollback();
		}
		//vamos a verificar y cargar si no existe en items product region
		$filter = " AND t.fk_item = ".$object->fk_item;
		$res = $objItemsproduct->fetchAll('','',0,0,array(),'AND',$filter);
		if ($res > 0)
		{
			$db->begin();
			$lines = $objItemsproduct->lines;
			foreach ($lines AS $j => $line)
			{
				if (!$error)
				{
					$res = $objItemsproductregion->fetch(0,$line->id,$fk_region,$fk_sector);
					if ($res==0)
					{
						//creamos
						$objItemsproductregion->fk_item_product = $line->id;
						$objItemsproductregion->fk_region = $fk_region;
						$objItemsproductregion->fk_sector = $fk_sector;
						$objItemsproductregion->amount_noprod=0;
						$objItemsproductregion->amount=0;
						$objItemsproductregion->cost_direct=0;
						$objItemsproductregion->units = 0;
						$objItemsproductregion->commander = 0;
						$objItemsproductregion->performance=0;
						$objItemsproductregion->hour_production=0;
						$objItemsproductregion->price_productive=0;
						$objItemsproductregion->price_improductive=0;
						$objItemsproductregion->fk_origin=0;
						$objItemsproductregion->percent_origin=100;
						$objItemsproductregion->fk_user_create = $user->id;
						$objItemsproductregion->fk_user_mod = $user->id;
						$objItemsproductregion->datec = $now;
						$objItemsproductregion->datem = $now;
						$objItemsproductregion->tms = $now;
						$objItemsproductregion->status = 1;
						$resr = $objItemsproductregion->create($user);
						if ($resr<=0)
						{
							$error++;
							setEventMessages($objItemsproductregion->error,$objItemsproductregion->errors,'errors');
						}
					}
				}
			}
			if (!$error) $db->commit();
			else $db->rollback();
		}
		//vamos a verificar y cargar si no existe en items production
		$filter = " AND t.fk_item = ".$object->fk_item;
		$res = $objItemsproduction->fetchAll('','',0,0,array(),'AND',$filter);
		if ($res > 0)
		{
			$db->begin();
			$lines = $objItemsproduction->lines;
			foreach ($lines AS $j => $line)
			{
				if (!$error)
				{
				//buscamos
					$res = $objItemsproduction->fetch(0, $object->fk_item,$line->fk_variable,$line->fk_items_product,$fk_region,$fk_sector);
					if ($res==0)
					{
					//creamos
						$objItemsproduction->fk_item = $object->fk_item;
						$objItemsproduction->fk_variable = $line->fk_variable;
						$objItemsproduction->fk_items_product = $line->fk_items_product;
						$objItemsproduction->fk_region = $fk_region;
						$objItemsproduction->fk_sector = $fk_sector;
						$objItemsproduction->quantity=$line->quantity+0;
						$objItemsproduction->active=1;
						$objItemsproduction->fk_user_create = $user->id;
						$objItemsproduction->fk_user_mod = $user->id;
						$objItemsproduction->datec = $now;
						$objItemsproduction->datem = $now;
						$objItemsproduction->tms = $now;
						$objItemsproduction->status = 1;
						$resr = $objItemsproduction->create($user);
						if ($resr<=0)
						{
							$error++;
							setEventMessages($objItemsproduction->error,$objItemsproduction->errors,'errors');
						}
					}
				}
			}
			if (!$error) $db->commit();
			else $db->rollback();
		}
	}
}

//armamos el origen en array
$filter = " AND group_structure = '".$objItemsproduct->group_structure."'";
$filter.= " AND t.entity = ".$conf->entity;
$res = $objOriginsupplies->fetchAll('ASC','label',0,0,array(),'AND',$filter);
$aOrigin=array();
if ($res>0)
{
	$lines = $objOriginsupplies->lines;
	foreach ($lines AS $j => $line)
		$aOrigin[$line->id] = $line->label.' ('.$line->ref.')';
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
//recuperamos todas las provincias por region
$aDepartement = array();
if ($fk_region>0)
{
	$filter = " AND t.fk_region_geographic = ".$fk_region;
	$res = $objCdepartementsregion->fetchAll('','',0,0,array(),'AND',$filter);
	if ($res >0)
	{
		$lines = $objCdepartementsregion->lines;
		foreach ($lines AS $j => $line)
			$aDepartement[$line->fk_departement] = $line->fk_departement;
	}
}
//armamos los precios del producto
$lCurrency = false;
$exchange_rate = $conf->global->ITEMS_DEFAULT_EXCHANGE_RATE;
if ($conf->global->ITEMS_DEFAULT_BASE_CURRENCY != $conf->currency)
{
	$lCurrency = true;
	if (empty($exchange_rate))
		setEventMessages($langs->trans('Theexchangerateisnotdefined'),null,'warnings');
}

$options = '<option value=""></option>';
if ($fk_product>0)
{
	$filter = " AND t.fk_product = ".$fk_product;
	$resprod = $objProductregionprice->fetchAll('ASC','date_create',0,0,array(),'AND',$filter);
	if ($resprod>0)
	{
		$aTmp = array();
		$lines = $objProductregionprice->lines;
		foreach ($lines AS $j => $line)
		{
			//vamos a buscar la region geografica
			$resg = $objCregiongeographic->fetch($line->fk_region_geographic);
			if ($resg==1)
				$aTmp[$objCregiongeographic->label][$line->date_create]=$line->price;
			else
				$aTmp[$j][$line->date_create]=$line->price;
		}
		//ordenamos el array
		ksort($aTmp);
		$aArrayprice= array();
		$lPriceselected=false;
		foreach ($aTmp  AS $j => $data)
		{
			$options.= '<optgroup label="'.$j.'">';
			foreach ($data AS $k => $value)
			{
				$valueor = $value;
				if ($lCurrency)
				{
					$value = $value / $exchange_rate;
				}
				$selected = '';
				if (
					(GETPOST('amount')?GETPOST('amount'):$objItemsproductregion->amount) === $value)
				{
					$selected = ' selected';
					if ($objItemsproductregion->amount == $value) $lPriceselected= true;
				}
				$options.= '<option value="'.$value.'" '.$selected.'>'.dol_print_date($k,'day').' - '.$value.'</option>';
			}
			$options.= '</optgroup>';
		}
	}
}

/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/
$aArrjs = array();
$help_url='EN:Module_Budget_En|FR:Module_Budget|ES:M&oacute;dulo_Budget';
$aArrcss = array('/budget/css/style-desktop.css');
llxHeader("",$langs->trans("Items"),$help_url,'','','',$aArrjs,$aArrcss);

$form=new Formv($db);


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
	$res = $object->fetch_optionals($object->id, $extralabels);
	$head = budgetitem_prepare_head($object,$user);

	//print load_fiche_titre($langs->trans("Item"));

	dol_fiche_head($head, 'supplies', $langs->trans("Item"),0,'item');

	$linkback = '<a href="'.DOL_URL_ROOT.'/budget/items/list.php">'.$langs->trans("BackToList").'</a>';

	$shownav = 1;
	if ($user->societe_id && ! in_array('budget', explode(',',$conf->global->MAIN_MODULES_FOR_EXTERNAL))) $shownav=0;
	$object->picto = 'projecttask';
	dol_banner_tab($object, 'ref', $linkback, $shownav, 'ref');

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id.'&idr='.$idr, $langs->trans('DeleteItemsresource'), $langs->trans('ConfirmDeleteItemsresource'), 'confirm_delete', '', 0, 2);
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
	//print '<tr><td>'.$langs->trans("Fieldref_ext").'</td><td>'.$object->ref_ext.'</td></tr>';

	// fk_type_item
	//if($object->fk_type_item>0)
	//{
	//	$resdet=$objCtypeitem->fetch($object->fk_type_item);
	//	if($resdet)
	//	{
	//		print '<tr><td>'.$langs->trans("Fieldfk_type_item").'</td><td>'.$objCtypeitem->getNomUrl().'</td></tr>';
	//	}
	//}

	// type
	//print '<tr><td>'.$langs->trans("Fieldtype").'</td><td>'.$object->type.'</td></tr>';
	//print '<tr><td>'.$langs->trans("Itsgroup").'</td><td>'.($object->type?$langs->trans("Yes"):$langs->trans("Not")).'</td></tr>';
	//detail
	print '<tr><td>'.$langs->trans("Fielddetail").'</td><td>'.$object->detail.'</td></tr>';
	//print '<tr><td>'.$langs->trans("Fieldfk_unit").'</td><td>'.$object->fk_unit.'</td></tr>';
	// unidad
	$objTmp = new Puvariablesline($db);
	$objTmp->fk_unit = $object->fk_unit;
	print '<tr><td>'.$langs->trans("Fieldfk_unit").'</td><td>'.$objTmp->getLabelOfUnit().'</td></tr>';
	//manual_performance
	//print '<tr><td>'.$langs->trans("Fieldmanual_performance").'</td><td>'.($object->manual_performance?$langs->trans('Yes'):$langs->trans('Not')).'</td></tr>';
	print '<tr><td>'.$langs->trans('Forinstitution').'</td><td>';
	print $form->selectarray('fk_sector',$aInstitutional,$fk_sector,1);
	print '</td></tr>';
	print '<tr><td>'.$langs->trans('Forregion').'</td><td>';
	print $form->selectarray('fk_region',$aRegiongeographic,$fk_region,1);
	print '</td></tr>';
	$lViewprod=false;
	if ($fk_region>0 && $fk_sector>0)
	{
		$resir = $objItemsregion->fetch(0,$object->fk_item,$fk_region,$fk_sector);
		if ($resir==1)
			print '<tr><td>'.$langs->trans("Fieldhour_production").'</td><td>'.$objItemsregion->hour_production.'</td></tr>';
		$lViewprod=true;
	}
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
						//echo '<br>'.$objItemsproductregion->performance.'*('.$objItemsproductregion->price_productive.'/100)*'.$objItemsproductregion->amount.'+'.$objItemsproductregion->performance.'*(1-('.$objItemsproductregion->price_productive.'/100))*'.$objItemsproductregion->amount_noprod;
						$objItemsproductregion->cost_direct = price2num($objItemsproductregion->performance*($objItemsproductregion->price_productive/100)*$objItemsproductregion->amount+$objItemsproductregion->performance*(1-($objItemsproductregion->price_productive/100))*$objItemsproductregion->amount_noprod,$nDecimal);
					}
					else
					{
						$objItemsproductregion->cost_direct = price2num($objItemsproductregion->performance*$objItemsproductregion->amount,$nDecimal);
					}

					//echo '<hr>suma '.$objItemsproductregion->cost_direct;
					$totalCost+= $objItemsproductregion->cost_direct;
					$totalCostnoprod+= $objItemsproductregion->amount_noprod;
				}
			}
		}
		$res = $objItemsregion->fetch(0,$object->fk_item,$fk_region,$fk_sector);
		if ($res == 1)
		{
			//$objItemsregion->id;
			$objItemsregion->amount = $totalCost;
			$objItemsregion->amount_noprod = $totalCostnoprod;
			$objItemsregion->fk_user_mod = $user->id;
			$objItemsregion->datem = $now;
			$objItemsregion->tms = $now;
			$res = $objItemsregion->update($user);
			if ($res<=0)
			{
				$error++;
				setEventMessages($objItemsregion->error,$objItemsregion->errors,'errors');
			}
		}
	}
	else
	{
		$totalCost=$objItemsregion->amount;
		$totalCostnoprod=$objItemsregion->amount_noprod;
	}
	print '<tr><td>'.$langs->trans("Fieldcost_direct").'</td><td>'.price(price2num($totalCost,$nDecimal)).'</td></tr>';
	//despecification
	//print '<tr><td>'.$langs->trans("Fieldespecification").'</td><td>'.$object->especification.'</td></tr>';
	//plane
	//print '<tr><td>'.$langs->trans("Fieldplane").'</td><td>'.$object->plane.'</td></tr>';
	//quant
	//print '<tr><td>'.$langs->trans("Fieldquant").'</td><td>'.$object->quant.'</td></tr>';
	//amount
	//print '<tr><td>'.$langs->trans("Fieldamount").'</td><td>'.$object->amount.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td>'.$object->fk_user_create.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td>'.$object->fk_user_mod.'</td></tr>';
	//print '<tr><td>'.$langs->trans("Fieldstatus").'</td><td>'.$object->getLibStatut(6).'</td></tr>';

	print '</table>';
	print '</form>';
	dol_fiche_end();


	if ($id && $lViewprod)
	{
		if ($idr>0) $objItemsproductregion->fetch(0,$idr,$fk_region,$fk_sector);
		if($action == 'create' || (empty($action) && $idr>0) || ($idr>0 && $action == 'edit') || ($idr>0 && $action == 'delete'))
			include DOL_DOCUMENT_ROOT.'/budget/items/tpl/itemsproduct_card.tpl.php';
		else
			include DOL_DOCUMENT_ROOT.'/budget/items/tpl/itemsproduct_list.tpl.php';

		//vamos a listar en que items mas se encuentra
		include DOL_DOCUMENT_ROOT.'/budget/items/tpl/itemsgroup_list.tpl.php';
	}
}


// End of page
llxFooter();
$db->close();
