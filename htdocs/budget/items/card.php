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
dol_include_once('/budget/lib/utils.lib.php');

require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
// Load traductions files requiredby by page
$langs->load("budget");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$ref 		= GETPOST('ref','alpha');
if (empty($ref)) $ref = NULL;
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

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

if ($action != 'add' && $action != 'update')
	// Load object
	include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  
if ($action == 'update')
	$res = $object->fetch($id);
// Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

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
		if ($id > 0 || ! empty($ref)) $ret = $object->fetch($id,$ref);
		$action='';
	}
	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/items/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */
		$fk_item=0;

		$object->entity=$conf->entity;
		$object->ref=GETPOST('ref','alpha');
		$object->ref_ext=GETPOST('ref_ext','alpha');
		$object->version = 1;
		$object->fk_type_item=GETPOST('fk_type_item','int');
		if (empty($object->fk_type_item))$object->fk_type_item = 0;
		$object->type=GETPOST('type','int');
		$object->detail=GETPOST('detail','alpha');
		$object->fk_unit=GETPOST('fk_unit','int');
		$object->fk_parent=GETPOST('fk_parent','int');
		if (empty($object->fk_parent)) $object->fk_parent = 0;
		$object->especification=GETPOST('especification','alpha');
		$object->plane=GETPOST('plane','alpha');
		$object->quant=GETPOST('quant','alpha');
		if (empty($object->quant))$object->quant = 0;
		$object->amount=GETPOST('amount','alpha');
		if (empty($object->amount))$object->amount = 0;
		$object->manual_performance=GETPOST('manual_performance','int');
		if (empty($object->manual_performance))$object->manual_performance = 0;
		$object->fk_user_create = $user->id;
		$object->fk_user_mod 	= $user->id;
		$object->datec=$now;
		$object->datem=$now;
		$object->status=0;

		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}
		if (empty($object->detail))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Detail")), null, 'errors');
		}
		//if ($object->fk_type_item <=0)
		//{
		//	$error++;
		//	setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldfk_type_item")), null, 'errors');
		//}
		if ($object->fk_unit <=0)
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldfk_unit")), null, 'errors');
		}


		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if ($object->type==0)
		{
			//vamos a cargar idem en el objItem
			$objItem->entity=$conf->entity;
			$objItem->ref=GETPOST('ref','alpha');
			$objItem->ref_ext=GETPOST('ref_ext','alpha');
			$objItem->version = 1;
			$objItem->fk_type_item=GETPOST('fk_type_item','int');
			if (empty($objItem->fk_type_item))$objItem->fk_type_item = 0;
			$objItem->type=GETPOST('type','int');
			$objItem->detail=GETPOST('detail','alpha');
			$objItem->fk_unit=GETPOST('fk_unit','int');
			$objItem->fk_parent=GETPOST('fk_parent','int');
			if (empty($objItem->fk_parent)) $objItem->fk_parent = 0;
			$objItem->especification=GETPOST('especification','alpha');
			$objItem->plane=GETPOST('plane','alpha');
			$objItem->quant=GETPOST('quant','alpha');
			if (empty($objItem->quant))$objItem->quant = 0;
			$objItem->amount=GETPOST('amount','alpha');
			if (empty($objItem->amount))$objItem->amount = 0;
			$objItem->manual_performance=GETPOST('manual_performance','int');
			if (empty($objItem->manual_performance))$objItem->manual_performance = 0;
			$objItem->fk_user_create = $user->id;
			$objItem->fk_user_mod 	= $user->id;
			$objItem->datec=$now;
			$objItem->datem=$now;
			$objItem->status=0;
			if (! $error)
			{
				$fk_item=$objItem->create($user);
				if ($fk_item <= 0)
				{
				// Creation KO
					if (! empty($objItem->errors)) setEventMessages(null, $objItem->errors, 'errors');
					else  setEventMessages($objItem->error, null, 'errors');
					$action='create';
				}
			}
			else
			{
				$action='create';
			}
		}

		if (! $error)
		{
			$object->fk_item = $fk_item;
			$result=$object->create($user);
			if ($result > 0)
			{
				//vamos a actualizar el ref
				$object->fetch($result);
				$object->ref = $object->ref.$object->rowid;
				$object->update($user);
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/items/card.php?id='.$result,1);
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

	if ($action == 'clonever')
	{
		$db->begin();
		//clonar un item como nueva version
		$objTmp = new Itemsgroup($db);
		$objTmp->fetch($object->id);
		$objTmp->id=0;
		$objTmp->version = $objTmp->version+1;
		$objTmp->fk_user_create = $user->id;
		$objTmp->fk_user_mod = $user->id;
		$objTmp->datec = $now;
		$objTmp->datem = $now;
		$objTmp->tms = $now;
		$objTmp->status=0;
		$resc = $objTmp->create($user);
		if ($resc>0)
		{
			if ($object->fk_item>0)
			{
				$filter = " AND t.fk_item = ".$object->fk_item;
			//vamos a clonar items_product, items_product_region, items_production
				$resip = $objItemsproduct->fetchAll('','',0,0,array(),'AND',$filter);
				if ($resip>0)
				{
					$lines = $objItemsproduct->lines;
					foreach ($lines AS $j => $line)
					{
						$objItemsproduct->fetch($line->id);
						$objItemsproduct->id=0;
						$objItemsproduct->fk_item = $resc;
						$objItemsproduct->fk_user_create = $user->id;
						$objItemsproduct->fk_user_mod = $user->id;
						$objItemsproduct->datec = $now;
						$objItemsproduct->datem = $now;
						$objItemsproduct->tms = $now;
						$objItemsproduct->status=1;
						$resip = $objItemsproduct->create($user);
					//vamos a buscar los items_product_region
						$filteripr = " AND t.fk_item_product = ".$line->id;
						$resipr = $objItemsproductregion->fetchAll('','',0,0,array(),'AND',$filteripr);
						if ($resipr>0)
						{
							$linesipr = $objItemsproductregion->lines;
							foreach ($linesipr AS $k => $lineipr)
							{
								$objItemsproductregion->fetch($lineipr->id);
								$objItemsproductregion->id=0;
								$objItemsproductregion->fk_item_product = $resip;
								$objItemsproductregion->fk_user_create = $user->id;
								$objItemsproductregion->fk_user_mod = $user->id;
								$objItemsproductregion->datec = $now;
								$objItemsproductregion->datem = $now;
								$objItemsproductregion->tms = $now;
								$objItemsproductregion->status=1;
								$restmp = $objItemsproductregion->create($user);
								if ($restmp<=0)
								{
									$error++;
									setEventMessages($objItemsproductregion->error,$objItemsproductregion->errors,'errors');
								}
							}
						}
					//vamos a buscar los items_production
						$filteripr = " AND t.fk_items_product = ".$line->id;
						$filteripr.= " AND t.fk_item = ".$object->fk_item;
						$resipr = $objItemsproduction->fetchAll('','',0,0,array(),'AND',$filteripr);
						if ($resipr>0)
						{
							$linesipr = $objItemsproduction->lines;
							foreach ($linesipr AS $k => $lineipr)
							{
								$objItemsproduction->fetch($lineipr->id);
								$objItemsproduction->id=0;
								$objItemsproduction->fk_item = $resc;
								$objItemsproduction->fk_items_product = $resip;
								$objItemsproduction->fk_user_create = $user->id;
								$objItemsproduction->fk_user_mod = $user->id;
								$objItemsproduction->datec = $now;
								$objItemsproduction->datem = $now;
								$objItemsproduction->tms = $now;
								$objItemsproduction->status=1;
								$restmp = $objItemsproduction->create($user);
								if ($restmp<=0)
								{
									$error++;
									setEventMessages($objItemsproduction->error,$objItemsproduction->errors,'errors');
								}
							}
						}
					}
				}
			}
		}
		else
		{
			$error++;
			setEventMessages($objTmp->error,$objTmp->errors,'errors');
		}
		//cambiamos de estado a $object
		$object->status = -1;
		$object->fk_user_mod = $user;
		$object->datem = $now;
		$object->datem = $now;
		$res = $object->update($user);
		if ($res<=0)
		{
			$error++;
			setEventMessages($object->error,$object->errors,'errors');
		}

		if (!$error)
		{
			$db->commit();
			header('Location: '.$_SERVER['PHP_SELF'].'?id='.$resc);
			exit;
		}
		else
			$db->rollback();
		$action = '';
	}
	// Action to update record
	if ($action == 'update')
	{
		$error=0;
		$db->begin();
		$fk_item = $object->fk_item;

		$object->entity=$conf->entity;
		$object->ref=GETPOST('ref','alpha');
		$object->ref_ext=GETPOST('ref_ext','alpha');
		if (empty($object->version)) $object->version=1;
		$object->fk_type_item=GETPOST('fk_type_item','int');
		if (empty($object->fk_type_item))$object->fk_type_item = 0;

		$object->type=GETPOST('type','int');
		if (empty($object->type)) $object->type = 0;
		//verificamos el cambio de nombr del item
		$detail = GETPOST('detail','alpha');
		$lUpdateitem = false;
		if ($object->detail != $detail) $lUpdateitem = true;
		$object->detail=GETPOST('detail','alpha');

		if (isset($_POST['fk_item']) && $object->type==0)
		{
			$object->fk_item= GETPOST('fk_item');
			$fk_item = $object->fk_item;
		}
		$object->fk_parent=GETPOST('fk_parent','int');
		if (empty($object->fk_parent)) $object->fk_parent = 0;

		if ($object->type == 0)
		{
			$fk_unit = GETPOST('fk_unit','int');
			if ($fk_unit != $object->fk_unit) $lUpdateitem = true;
			$object->fk_unit=GETPOST('fk_unit','int');
			$especification = GETPOST('especification','alpha');
			if ($especification != $object->especification) $lUpdateitem = true;
			$object->especification=GETPOST('especification','alpha');
			$object->plane=GETPOST('plane','alpha');
			$object->quant=GETPOST('quant','alpha');
			if (empty($object->quant))$object->quant = 0;
			$object->amount=GETPOST('amount','alpha');
			if (empty($object->amount))$object->amount = 0;
			$object->manual_performance=GETPOST('manual_performance','int');
			if (empty($object->manual_performance))$object->manual_performance = 0;
		}
		else
		{
			$object->fk_unit=0;
			$object->especification='';
			$object->plane='';
			$object->quant=0;
			$object->amount=0;
			$object->manual_performance=0;
		}
		$object->fk_user_mod 	= $user->id;
		$object->datem=$now;

		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}
		if (empty($object->detail))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Detail")), null, 'errors');
		}
		if ($object->type == 0 && $object->fk_unit <=0)
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldfk_unit")), null, 'errors');
		}
		if ($object->type==0)
		{
			$lAdditem=true;
			if ($object->fk_item>0)
			{
				$lAdditem=false;
				$objItem->fetch($object->fk_item);
			}

			$objItem->entity=$conf->entity;
			$objItem->ref=GETPOST('ref','alpha');
			$objItem->ref_ext=GETPOST('ref_ext','alpha');
			if (empty($objItem->version)) $objItem->version=1;
			$objItem->fk_type_item=GETPOST('fk_type_item','int');
			if (empty($objItem->fk_type_item))$objItem->fk_type_item = 0;
			$objItem->type=GETPOST('type','int');
			$objItem->detail=GETPOST('detail','alpha');
			$objItem->fk_unit=GETPOST('fk_unit','int');
			$objItem->fk_parent=GETPOST('fk_parent','int');
			if (empty($objItem->fk_parent)) $objItem->fk_parent = 0;
			$objItem->especification=GETPOST('especification','alpha');
			$objItem->plane=GETPOST('plane','alpha');
			$objItem->quant=GETPOST('quant','alpha');
			if (empty($objItem->quant))$objItem->quant = 0;
			$objItem->amount=GETPOST('amount','alpha');
			if (empty($objItem->amount))$objItem->amount = 0;
			$objItem->manual_performance=GETPOST('manual_performance','int');
			if (empty($objItem->manual_performance))$objItem->manual_performance = 0;
			$objItem->fk_user_mod 	= $user->id;
			$objItem->datem=$now;
			if ($lAdditem)
			{
				$objItem->fk_user_create = $user->id;
				$objItem->datec = $now;
				$fk_item = $objItem->create($user);
				if ($fk_item <= 0)
				{
					// Creation KO
					if (! empty($objItem->errors)) setEventMessages(null, $objItem->errors, 'errors');
					else  setEventMessages($objItem->error, null, 'errors');
					$action='create';
				}
			}
			else
			{
				$resitem = $objItem->update($user);
				if ($resitem <= 0)
				{
					// Creation KO
					if (! empty($objItem->errors)) setEventMessages(null, $objItem->errors, 'errors');
					else  setEventMessages($objItem->error, null, 'errors');
					$action='create';
				}

			}
		}
		if ($lUpdateitem && $object->type == 0)
		{
			//actualizamos el nombre del items
			$res = $objItem->fetch($object->fk_item);
			if ($res==1)
			{
				$objItem->detail = $object->detail;
				$objItem->fk_unit = $object->fk_unit;
				$objItem->especification = $object->especification;
				$res = $objItem->update($user);
				if ($res<=0)
				{
					$error++;
					if (! empty($objItem->errors)) setEventMessages(null, $objItem->errors, 'errors');
					else setEventMessages($objItem->error, null, 'errors');
				}
			}
		}

		if (! $error)
		{
			$object->fk_item = $fk_item;
			$result=$object->update($user);
			if ($result > 0)
			{
				$action='view';
			}
			else
			{
				$error++;
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


		if (!$error) $db->commit();
		else $db->rollback();
	}

	// Action to delete
	if ($action == 'confirm_delete')
	{
		$result=$object->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/budget/items/list.php',1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else setEventMessages($object->error, null, 'errors');
		}
	}
	// Action to validate
	if ($action == 'confirm_validate')
	{
		$object->status = ($object->status?0:1);
		$object->fk_user_mod = $user->id;
		$object->datem = $now;
		$object->tms = $now;
		$result = $object->update($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordUpdate", null, 'mesgs');
			header("Location: ".dol_buildpath('/budget/items/card.php?id='.$id,1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else setEventMessages($object->error, null, 'errors');
		}
		$action = '';
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

$aType=array();
$aType[]=($langs->trans('Yesgroup'));
$aType[]=($langs->trans('Notgroup'));

//parent
//$filter = " AND t.status = 1";
$filter = '';
if ($id) $filter.= " AND t.rowid NOT IN (".$id.")";
$filter.= " AND t.type=1";
$res = $objTmpgroup->fetchAll('ASC','t.detail',0,0,array(),'AND',$filter);
$aItems=array();
if ($res>0)
{
	$lines = $objTmpgroup->lines;
	foreach ($lines AS $j => $line)
		$aItems[$line->id] = $line->detail.' ('.$line->ref.')';
}
$optionsext = '';
foreach ($aType AS $j => $line)
{
	$selected = '';
	if((GETPOST('label_print')?GETPOST('label_print'):$object->type)==$line)
		$selected = 'selected';
	$optionsext.= '<option value="'.$j.'" '.$selected.' >'.$line.'</option>';
}

if (! empty($conf->use_javascript_ajax))
{
	print "\n".'<script type="text/javascript">';
	print '$(document).ready(function () { $("#manual_performance").change(function() { document.formitem.action.value="'.$action.'";
	document.formitem.submit(); }); $("#type").change(function() { document.formitem.action.value="'.$action.'";
	document.formitem.submit(); }); $("#fk_item").change(function() { document.formitem.action.value="'.$action.'";
	document.formitem.submit(); }); });';
	print '</script>'."\n";
}

// Part to create
if ($action == 'create')
{
	print load_fiche_titre($langs->trans("Items"));

	print '<form name="formitem" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	print '<tr><td width="20%" class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat minwidth100" type="text" name="ref" value="'.GETPOST('ref').'" placeholder="'.$langs->trans('Ref').'" maxlength="30"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldref_ext").'</td><td><input class="flat" type="text" name="ref_ext" value="'.GETPOST('ref_ext').'"></td></tr>';

		//print '<tr>';
		//print '<td class="fieldrequired">'.$langs->trans("Fieldfk_type_item").'</td><td>';
		//print '<select name="fk_type_item" >'.$optionstype.'</select>';;
		//print '</td>';
		//print '</tr>';

	print '<tr>';
	print '<td width="20%" class="fieldrequired">'.$langs->trans("Itsgroup").'</td><td>';
	print $form->selectyesno('type',GETPOST('type'),1);
	print '</td>';
	print '</tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Fielddetail").'</td><td><input class="maxwidth100onsmartphone quatrevingtpercent" type="text" name="detail" value="'.GETPOST('detail').'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit").'</td><td><input class="flat" type="text" name="fk_unit" value="'.GETPOST('fk_unit').'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldfk_parent").'</td><td>';
	print $form->selectarray('fk_parent',$aItems,GETPOST('fk_parent'),1);
	print '</td></tr>';

	if ($object->type==0 || GETPOST('type')==0)
	{
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_item").'</td><td>';
		print $form->select_items_v((GETPOST('fk_item')?GETPOST('fk_item'):$object->fk_item), 'fk_item', 'I', 30, 0, 1, 2, '', 1, array(),0,'','');
		print '</td></tr>';
	}

	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit").'</td><td>';
	print $form->selectUnits(GETPOST('fk_unit'),'fk_unit');
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("Fieldespecification").'</td><td>';
	print '<textarea name="especification" id="address" class="quatrevingtpercent" rows="2" wrap="soft">';
	print GETPOST('especification');
	print '</textarea></td></tr>';
		//print '<tr><td>'.$langs->trans("Fieldplane").'</td><td><input class="flat" type="text" name="plane" value="'.GETPOST('plane').'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldquant").'</td><td><input class="flat" type="number" min="0" step="any" name="quant" value="'.(GETPOST('quant')?GETPOST('quant'):1).'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldamount").'</td><td><input class="flat" type="number" min="0" step="any"  name="amount" value="'.GETPOST('amount').'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldmanual_performance").'</td><td>';
	print $form->selectyesno('manual_performance',GETPOST('manual_performance'),1);
	print '</td></tr>';
	if($manual_performance)
	{
		print '<tr><td>'.$langs->trans("Fieldhour_production").'</td><td><input class="flat" type="number" min="0" name="hour_production" value="'.(GETPOST('hour_production')?GETPOST('hour_production'):$object->hour_production).'"></td></tr>';
	}

//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.GETPOST('fk_user_create').'"></td></tr>';
//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.GETPOST('fk_user_mod').'"></td></tr>';
//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.GETPOST('status').'"></td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}

// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	print load_fiche_titre($langs->trans("Items"));

	print '<form name="formitem" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td><input class="flat" type="text" name="entity" value="'.$object->entity.'"></td></tr>';
	print '<tr><td width="20%" class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.$object->ref.'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldref_ext").'</td><td><input class="flat" type="text" name="ref_ext" value="'.$object->ref_ext.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_type_item").'</td><td><input class="flat" type="text" name="fk_type_item" value="'.$object->fk_type_item.'"></td></tr>';

	$fk_type_item=(GETPOST('fk_type_item')?GETPOST('fk_type_item'):$object->fk_type_item);
		//print '<tr>';
		//print '<td class="fieldrequired">'.$langs->trans("Fieldfk_type_item").'</td><td>';

		//print '<select name="fk_type_item" value="'.$object->fk_type_item.'">'.$optionstype.'</select>';;
		//print '</td>';
		//print '</tr>';

	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype").'</td><td><input class="flat" type="text" name="type" value="'.$object->type.'"></td></tr>';
	if (isset($_POST['type']))
		$type = GETPOST('type');
	else
		$type = $object->type;
	print '<tr>';
	print '<td class="fieldrequired">'.$langs->trans("Itsgroup").'</td><td>';
	print $form->selectyesno('type',$type,1);
	print '</td>';
	print '</tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Fielddetail").'</td><td><input class="maxwidth100onsmartphone quatrevingtpercent" type="text" name="detail" value="'.$object->detail.'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldfk_parent").'</td><td>';
	print $form->selectarray('fk_parent',$aItems,(GETPOST('fk_parent')?GETPOST('fk_parent'):$object->fk_parent),1);
	print '</td></tr>';

	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit").'</td><td><input class="flat" type="text" name="fk_unit" value="'.$object->fk_unit.'"></td></tr>';
	//vamos a relacionar con items
	if ($type==0)
	{
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_item").'</td><td>';
		print $form->select_items_v((GETPOST('fk_item')?GETPOST('fk_item'):$object->fk_item), 'fk_item', 'I', 30, 0, 1, 2, '', 1, array(),0,'','');
		print '</td></tr>';
	}

	if ($type==0)
	{
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit").'</td><td>';
		print $form->selectUnits((GETPOST('fk_unit')?GETPOST('fk_unit'):$object->fk_unit),'fk_unit');
		print '</td></tr>';

		print '<tr><td>'.$langs->trans("Fieldespecification").'</td><td>';
		print '<textarea name="especification" id="address" class="quatrevingtpercent" rows="2" wrap="soft">';
		print (GETPOST('especification')?GETPOST('especification'):$objItem->especification);
		print '</textarea></td></tr>';

		//print '<tr><td>'.$langs->trans("Fieldplane").'</td><td><input class="flat" type="text" name="plane" value="'.$object->plane.'"></td></tr>';
		print '<tr><td>'.$langs->trans("Fieldquant").'</td><td><input class="flat" type="number" min="0" step="any" name="quant" value="'.(GETPOST('quant')?GETPOST('quant'):$object->quant).'"></td></tr>';
		print '<tr><td>'.$langs->trans("Fieldamount").'</td><td><input class="flat" type="number" min="0" step="any" name="amount" value="'.$object->amount.'"></td></tr>';

		if (isset($_POST['manual_performance']))
			$manual_performance = GETPOST('manual_performance');
		else
			$manual_performance = $object->manual_performance;

		print '<tr><td>'.$langs->trans("Fieldmanual_performance").'</td><td>';
		print $form->selectyesno('manual_performance',$manual_performance,1);
		print '</td></tr>';
		if($manual_performance)
		{
			print '<tr><td>'.$langs->trans("Fieldhour_production").'</td><td><input class="flat" type="number" min="0" name="hour_production" value="'.(GETPOST('hour_production')?GETPOST('hour_production'):$object->hour_production).'"></td></tr>';
		}
	}
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.$object->fk_user_create.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.$object->fk_user_mod.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.$object->status.'"></td></tr>';

	print '</table>';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}



// Part to show record
if ($object->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create')))
{
	$res = $object->fetch_optionals($object->id, $extralabels);
	$head = budgetitem_prepare_head($object,$user);

	//print load_fiche_titre($langs->trans("Item"));

	dol_fiche_head($head, 'card', $langs->trans("Item"),0,'item');

	$linkback = '<a href="'.DOL_URL_ROOT.'/budget/items/list.php">'.$langs->trans("BackToList").'</a>';

	$shownav = 1;
	if ($user->societe_id && ! in_array('budget', explode(',',$conf->global->MAIN_MODULES_FOR_EXTERNAL))) $shownav=0;
	$object->picto = 'projecttask';
	dol_banner_tab($object, 'ref', $linkback, $shownav, 'ref');


	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('Deleteitem'), $langs->trans('ConfirmDeleteitem'), 'confirm_delete', '', 0, 2);
		print $formconfirm;
	}
	if ($action == 'validate') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, ($object->status?$langs->trans('Novalidateitem'):$langs->trans('Validateitem')), ($object->status?$langs->trans('ConfirmNovalidateitem'):$langs->trans('ConfirmValidateitem')), 'confirm_validate', '', 0, 2);
		print $formconfirm;
	}

	print '<table class="border centpercent">'."\n";
	print '<tr><td>'.$langs->trans("Fieldref").'</td><td>'.$object->ref.'</td></tr>';
	if (!empty($object->ref_ext))
		print '<tr><td>'.$langs->trans("Fieldref_ext").'</td><td>'.$object->ref_ext.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldversion").'</td><td>'.$object->version.'</td></tr>';

	if($object->fk_type_item>0)
	{
		$resdet=$objCtypeitem->fetch($object->fk_type_item);
		if($resdet)
		{
			$objCtypeitem->ref=$objCtypeitem->code;
			print '<tr><td>'.$langs->trans("Fieldfk_type_item").'</td><td>'.$objCtypeitem->getNomUrl().'</td></tr>';
		}
	}

		// type
	print '<tr><td>'.$langs->trans("Itsgroup").'</td><td>'.($object->type?$langs->trans("Yes"):$langs->trans("Not")).'</td></tr>';
		//detail
	print '<tr><td>'.$langs->trans("Fielddetail").'</td><td>'.$object->detail.'</td></tr>';
	$restmp = $objTmpgroup->fetch($object->fk_parent);
	if ($restmp==1)
		print '<tr><td>'.$langs->trans("Fieldfk_parent").'</td><td>'.$objTmpgroup->getNomUrl().'</td></tr>';
		// unidad
	if (!$object->type)
	{
		//vamos a recuperar al item principal
		//$objItem->fetch($object->fk_item);
		//print '<tr><td>'.$langs->trans("Fieldfk_item").'</td><td>'.$objItem->detail.'</td></tr>';
		$objTmp = new ItemsgroupLineext($db);
		$objTmp->fk_unit = $object->fk_unit;
		print '<tr><td>'.$langs->trans("Fieldfk_unit").'</td><td>'.$langs->trans($objTmp->getLabelOfUnit()).'</td></tr>';

			//despecification
		if(!empty($objItem->especification))
			print '<tr><td>'.$langs->trans("Fieldespecification").'</td><td>'.$objItem->especification.'</td></tr>';
			//plane
		if(!empty($objItem->plane))
			print '<tr><td>'.$langs->trans("Fieldplane").'</td><td>'.$objItem->plane.'</td></tr>';
			//quant
		print '<tr><td>'.$langs->trans("Fieldquant").'</td><td>'.$object->quant.'</td></tr>';
			//manual_performance
		print '<tr><td>'.$langs->trans("Fieldmanual_performance").'</td><td>'.($object->manual_performance?$langs->trans('Yes'):$langs->trans('Not')).'</td></tr>';
	}
		//manual_performance
	print '<tr><td>'.$langs->trans("Fieldstatus").'</td><td>'.$object->getLibStatut(6).'</td></tr>';

	print '</table>';

	dol_fiche_end();
		// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');



	if (empty($reshook))
	{


		print '<div class="inline-block divButAction"><a class="butAction" href="'.dol_buildpath('/budget/items/list.php',1).'">'.$langs->trans("Return").'</a></div>'."\n";
		if ($object->status == 1 && $user->rights->budget->ite->version)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=clonever">'.$langs->trans("Newversion").'</a></div>'."\n";
		}

		if ($object->status == 0 && $user->rights->budget->ite->crear)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}
		if ($object->status == 0 && $user->rights->budget->ite->val)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=validate">'.$langs->trans('Validate').'</a></div>'."\n";
		}
		if ($object->status == 1 && $user->rights->budget->ite->val)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=validate">'.$langs->trans('Novalidate').'</a></div>'."\n";
		}

		if ($object->status == 0 && $user->rights->budget->ite->del)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
		}

	}
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	// Show links to link elements
	//$linktoelem = $form->showLinkToObjectBlock($object, null, array('items'));
	//$somethingshown = $form->showLinkedObjectBlock($object, $linktoelem);

}


// End of page
llxFooter();
$db->close();
