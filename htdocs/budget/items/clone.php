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
dol_include_once('/budget/class/itemsregion.class.php');

dol_include_once('/budget/class/itemsgroupext.class.php');
dol_include_once('/budget/class/itemsproduct.class.php');
dol_include_once('/budget/class/itemsproductregion.class.php');
dol_include_once('/budget/class/itemsproduction.class.php');
dol_include_once('/budget/class/ctypeitemext.class.php');
dol_include_once('/budget/class/puvariablesext.class.php');
dol_include_once('/budget/lib/budget.lib.php');

dol_include_once('/orgman/class/cregiongeographic.class.php');
dol_include_once('/orgman/class/cclasfin.class.php');
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
$objItems = new Itemsext($db);
$objTmp = new Itemsext($db);
$objTmpgroup = new Itemsgroupext($db);
$objCtypeitem = new Ctypeitemext($db);
$extrafields = new ExtraFields($db);
$objItemsregion = new Itemsregion($db);
$objItemsproduct = new Itemsproduct($db);
$objItemsproductregion = new Itemsproductregion($db);
$objItemsproduction = new Itemsproduction($db);
$objCregiongeographic = new Cregiongeographic($db);
$objCclasfin = new Cclasfin($db);

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
		if ($id > 0 || ! empty($ref)) $ret = $object->fetch($id,$ref);
		$action='';
	}
	if ($action == 'add')
	{
		$fk_region_ini = GETPOST('fk_region_ini');
		$fk_region_fin = GETPOST('fk_region_fin');
		$fk_sector_ini = GETPOST('fk_sector_ini');
		$fk_sector_fin = GETPOST('fk_sector_fin');
		if (($fk_region_ini == $fk_region_fin) || ($fk_region_ini <=0 || $fk_region_fin <=0))
		{
			$error++;
			setEventMessages($langs->trans('Selecttheregionscorrectly'),null,'errors');
		}
		if (($fk_sector_ini == $fk_sector_fin && $fk_region_ini == $fk_region_fin) || ($fk_sector_ini <=0 || $fk_sector_fin <=0))
		{
			$error++;
			setEventMessages($langs->trans('Selectthesectorcorrectly'),null,'errors');
		}
		if (!$error)
		{
		$db->begin();
		$objItemsregiontmp = new Itemsregion($db);
		$objItemsproductregiontmp = new Itemsproductregion($db);
		$objItemsproductiontmp = new Itemsproduction($db);
		//vamos a recuperar todos los items
		//$filter = " AND t.status = 1 ";
		$res = $objItems->fetchAll('','',0,0,array(),'AND',$filter);
		if ($res > 0)
		{
			$lines = $objItems->lines;
			foreach ($lines AS $j => $line)
			{
				//recuperamos la lista de itemsproduct
				$filter = " AND t.fk_item = ".$line->id;
				$res = $objItemsproduct->fetchAll('','',0,0,array(),'AND',$filter);
				unset($aItemsproduct);
				if ($res>0)
				{
					$linesprod = $objItemsproduct->lines;
					foreach ($linesprod AS $m => $lineprod)
					{
						$aItemsproduct[$lineprod->id] = $lineprod->id;
					}
				}
				if (!$error)
				{
					$filter = " AND t.fk_item = ".$line->id;
					$filter.= " AND t.fk_region = ".$fk_region_ini;
					$filter.= " AND t.fk_sector = ".$fk_sector_ini;
					$res = $objItemsregion->fetchAll('','',0,0,array(),'AND',$filter);
					if ($res>0)
					{
						$linesreg = $objItemsregion->lines;
						foreach ($linesreg AS $k => $linereg)
						{
							if (!$error)
							{
								//vamos a clonar cada uno
								$res = $objItemsregion->fetch($linereg->id);
								if ($res==1)
								{
									//vamos a buscar en itemsregion si existe con la nueva region
									$restmp = $objItemsregiontmp->fetch(0, $line->id,$fk_region_fin,$fk_sector_fin);
									if (empty($restmp))
									{
										$objItemsregion->id=0;
										$objItemsregion->fk_region = $fk_region_fin;
										$objItemsregion->fk_sector = $fk_sector_fin;
										//variables fijas
										$objItemsregion->fk_user_create = $user->id;
										$objItemsregion->fk_user_mod = $user->id;
										$objItemsregion->datec = $now;
										$objItemsregion->datem = $now;
										$objItemsregion->tms = $now;

										$res = $objItemsregion->create($user);
										if ($res<=0)
										{
											$error=1;
											setEventMessages($objItemsregion->error,$objItemsregion->errors,'errors');
										}
									}
								}
							}
						}
					}
					//segundo paso
					//vamos a procesar la tabla items_product_region
					$filter = " AND t.fk_item_product IN (".implode(',',$aItemsproduct).")";
					$filter.= " AND t.fk_region = ".$fk_region_ini;
					$filter.= " AND t.fk_sector = ".$fk_sector_ini;
					$res = $objItemsproductregion->fetchAll('','',0,0,array(),'AND',$filter);
					if ($res>0)
					{
						$linesreg = $objItemsproductregion->lines;
						foreach ($linesreg AS $k => $linereg)
						{
							if (!$error)
							{
								$res = $objItemsproductregion->fetch($linereg->id);
								if ($res==1)
								{
									//buscamos
									$restmp = $objItemsproductregiontmp->fetch(0, $linereg->fk_item_product,$fk_region_fin,$fk_sector_fin);
									if (empty($restmp))
									{
										$objItemsproductregion->id=0;
										$objItemsproductregion->fk_region = $fk_region_fin;
										$objItemsproductregion->fk_sector = $fk_sector_fin;

										//variables fijas
										$objItemsproductregion->fk_user_create = $user->id;
										$objItemsproductregion->fk_user_mod = $user->id;
										$objItemsproductregion->datec = $now;
										$objItemsproductregion->datem = $now;
										$objItemsproductregion->tms = $now;

										$res = $objItemsproductregion->create($user);
										if ($res<=0)
										{
											$error=2;
											setEventMessages($objItemsproductregion->error,$objItemsproductregion->errors,'errors');
										}
									}
								}
							}
						}
					}
					//tercer paso
					//vamos a procesar la tabla items_production
					$filter = " AND t.fk_item = ".$line->id;
					$filter.= " AND t.fk_region = ".$fk_region_ini;
					$filter.= " AND t.fk_sector = ".$fk_sector_ini;
					$res = $objItemsproduction->fetchAll('','',0,0,array(),'AND',$filter);
					if ($res>0)
					{
						$linesreg = $objItemsproduction->lines;
						foreach ($linesreg AS $k => $linereg)
						{
							if (!$error)
							{
								$res = $objItemsproduction->fetch($linereg->id);
								if ($res==1)
								{
									//buscamos
									//echo '<hr>'.$line->id.','.$linereg->fk_variable.','.$linereg->fk_items_product.','.$fk_region_fin.','.$linereg->fk_sector.'|';
									$restmp = $objItemsproductiontmp->fetch(0, $line->id,$linereg->fk_variable,$linereg->fk_items_product,$fk_region_fin,$fk_sector_fin);
									if (empty($restmp))
									{
										$objItemsproduction->id=0;
										$objItemsproduction->fk_region = $fk_region_fin;
										$objItemsproduction->fk_sector = $fk_sector_fin;

										//variables fijas
										$objItemsproduction->fk_user_create = $user->id;
										$objItemsproduction->fk_user_mod = $user->id;
										$objItemsproduction->datec = $now;
										$objItemsproduction->datem = $now;
										$objItemsproduction->tms = $now;

										$res = $objItemsproduction->create($user);
										if ($res<=0)
										{
											$error=3;
											setEventMessages($objItemsproduction->error,$objItemsproduction->errors,'errors');
										}
									}
									elseif($restmp<=0)
									{
										$error=99;
										setEventMessages($objItemsproductiontmp->error,$objItemsproductiontmp->errors,'errors');
									}
								}
								elseif($res<0)
								{
									$error=100;
									setEventMessages($objItemsproduction->error,$objItemsproduction->errors,'errors');
								}
							}
						}
					}
					elseif($res<0)
					{
						$error=101;
						setEventMessages($objItemsproduction->error,$objItemsproduction->errors,'errors');
					}
				}
			}
		}
		//echo '<hr>fin '.$error;exit;
		if (!$error)
		{
			$db->commit();
			setEventMessages($langs->trans('Successfulcloning'),null,'mesgs');
			header('Location: '.DOL_URL_ROOT.'/budget/items/list.php');
			exit;
		}
		else
			$db->rollback();
		}
		$action = 'create';
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

llxHeader('','Cloneitems','');

$form=new Formv($db);



// Part to create
if ($action == 'create')
{
	print load_fiche_titre($langs->trans("Cloneitems"));

	print '<form name="formitem" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	print '<tr><td>'.$langs->trans('Fromregion').'</td><td>';
	print $form->selectarray('fk_region_ini',$aRegiongeographic,GETPOST('fk_region_ini'),1);
	print '</td></tr>';

	print '<tr><td>'.$langs->trans('Frominstitutional').'</td><td>';
	print $form->selectarray('fk_sector_ini',$aInstitutional,GETPOST('fk_sector_ini'),1);
	print '</td></tr>';

	print '<tr><td>'.$langs->trans('Forregion').'</td><td>';
	print $form->selectarray('fk_region_fin',$aRegiongeographic,GETPOST('fk_region_fin'),1);
	print '</td></tr>';

	print '<tr><td>'.$langs->trans('Forinstitutional').'</td><td>';
	print $form->selectarray('fk_sector_fin',$aInstitutional,GETPOST('fk_sector_fin'),1);
	print '</td></tr>';


	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}

// End of page
llxFooter();
$db->close();
