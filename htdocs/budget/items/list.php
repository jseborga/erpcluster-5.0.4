<?php
/* Copyright (C) 2007-2016 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2014-2016 Juanjo Menent        <jmenent@2byte.es>
 * Copyright (C) 2016      Jean-François Ferry	<jfefe@aternatik.fr>
 * Copyright (C) 2017      Nicolas ZABOURI	<info@inovea-conseil.com>
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
 *   	\file       budget/items_list.php
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
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
//dol_include_once('/budget/class/items.class.php');
dol_include_once('/budget/class/itemsext.class.php');
dol_include_once('/budget/class/itemsgroupext.class.php');
dol_include_once('/budget/class/itemsproductext.class.php');
dol_include_once('/budget/class/itemsregionext.class.php');
dol_include_once('/budget/class/ctypeitemext.class.php');
dol_include_once('/orgman/class/cregiongeographic.class.php');
dol_include_once('/orgman/class/cclasfin.class.php');
dol_include_once('/user/class/user.class.php');

// Load traductions files requiredby by page
$langs->load("budget");
$langs->load("other");

$action=GETPOST('action','alpha');
$massaction=GETPOST('massaction','alpha');
$show_files=GETPOST('show_files','int');
$confirm=GETPOST('confirm','alpha');
$toselect = GETPOST('toselect', 'array');

$id			= GETPOST('id','int');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');
$validate = GETPOST('validate','alpha');
$novalidate = GETPOST('novalidate','alpha');
$activate = GETPOST('activate','alpha');
$noactivate = GETPOST('noactivate','alpha');
$search_all=trim(GETPOST("sall"));

$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_ref_ext=GETPOST('search_ref_ext','alpha');
$search_version=GETPOST('search_version','int');
$search_fk_type_item=GETPOST('search_fk_type_item','int');
$search_fk_parent=GETPOST('search_fk_parent','alpha');
$search_type=GETPOST('search_type','int');
$search_detail=GETPOST('search_detail','alpha');
$search_fk_unit=GETPOST('search_fk_unit','alpha');
$search_especification=GETPOST('search_especification','alpha');
$search_plane=GETPOST('search_plane','alpha');
$search_quant=GETPOST('search_quant','alpha');
$search_amount=GETPOST('search_amount','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','alpha');
$search_fk_user_mod=GETPOST('search_fk_user_mod','alpha');
$search_status=GETPOST('search_status','int');
$search_active=GETPOST('search_active','int');
if (isset($_POST['search_type']) ||isset($_GET['search_type'])) 
	$search_type=GETPOST('search_type');
else
	$search_type = -1;

$search_myfield=GETPOST('search_myfield');
$optioncss = GETPOST('optioncss','alpha');

if (isset($_POST['search_fk_region'])) $_SESSION['selitem']['fk_region'] = GETPOST('search_fk_region','int');
if (isset($_POST['search_fk_sector'])) $_SESSION['selitem']['fk_sector'] = GETPOST('search_fk_sector','int');

$search_fk_region=$_SESSION['selitem']['fk_region'];
$search_fk_sector=$_SESSION['selitem']['fk_sector'];


$aStatus=array();
$aStatus[9]=$langs->trans('All');
$aStatus[1]=$langs->trans('Validated');
$aStatus[0]=$langs->trans('Draft');
$aStatus[-1]=$langs->trans('Annulled');

// Load variable for pagination
$limit = GETPOST("limit")?GETPOST("limit","int"):$conf->liste_limit;
$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha');
$page = GETPOST('page','int');
if (empty($page) || $page == -1) { $page = 0; }
if (empty($page)) $page=0;
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortfield) $sortfield="g.ref"; // Set here default search field
if (! $sortorder) $sortorder="ASC";

// Protection if external user
$socid=0;
if ($user->societe_id > 0)
{
	$socid = $user->societe_id;
	//accessforbidden();
}

// Initialize technical object to manage context to save list fields
$contextpage=GETPOST('contextpage','aZ')?GETPOST('contextpage','aZ'):'budgetitemsregionlist';

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('budgetlist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('budget');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// List of fields to search into when doing a "search in all"
$fieldstosearchall = array(
	't.ref'=>'Ref',
	't.note_public'=>'NotePublic',
);
if (empty($user->socid)) $fieldstosearchall["t.note_private"]="NotePrivate";

// Definition of fields for list
$arrayfields=array(

	'g.entity'=>array('label'=>$langs->trans("Fieldentity"), 'align'=>'align="left"', 'checked'=>0),
	'g.ref'=>array('label'=>$langs->trans("Fieldref"), 'align'=>'align="left"', 'checked'=>1),
	'g.ref_ext'=>array('label'=>$langs->trans("Fieldref_ext"), 'align'=>'align="left"', 'checked'=>0),
	'g.version'=>array('label'=>$langs->trans("Fieldversion"), 'align'=>'align="left"', 'checked'=>1),
	//'t.fk_type_item'=>array('label'=>$langs->trans("Fieldfk_type_item"), 'align'=>'align="left"', 'checked'=>1),
	'g.fk_parent'=>array('label'=>$langs->trans("Fieldfk_parent"), 'align'=>'align="left"', 'checked'=>1),
	'i.fk_region'=>array('label'=>$langs->trans("Fieldfk_region"), 'align'=>'align="left"', 'checked'=>1),
	'i.fk_sector'=>array('label'=>$langs->trans("Fieldfk_sector"), 'align'=>'align="left"', 'checked'=>1),
	'g.type'=>array('label'=>$langs->trans("Fieldgroup"), 'align'=>'align="left"', 'checked'=>1),
	'g.detail'=>array('label'=>$langs->trans("Fielddetail"), 'align'=>'align="left"', 'checked'=>1),
	'g.fk_unit'=>array('label'=>$langs->trans("Fieldfk_unit"), 'align'=>'align="left"', 'checked'=>1),
	't.especification'=>array('label'=>$langs->trans("Fieldespecification"), 'align'=>'align="left"', 'checked'=>0),
	't.plane'=>array('label'=>$langs->trans("Fieldplane"), 'align'=>'align="left"', 'checked'=>0),
	't.quant'=>array('label'=>$langs->trans("Fieldquant"), 'align'=>'align="left"', 'checked'=>1),
	//'i.amount_noprod'=>array('label'=>$langs->trans("Fieldcost_improductive"), 'align'=>'align="right"', 'checked'=>1),
	'i.amount'=>array('label'=>$langs->trans("Fieldcost_direct"), 'align'=>'align="right"', 'checked'=>1),
	//'t.amount'=>array('label'=>$langs->trans("Fieldamount"), 'align'=>'align="left"', 'checked'=>1),
	't.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'align'=>'align="left"', 'checked'=>0),
	't.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'align'=>'align="left"', 'checked'=>0),
	'i.active'=>array('label'=>$langs->trans("Fieldactive"), 'align'=>'align="center"', 'checked'=>1),
	'i.status'=>array('label'=>$langs->trans("Fieldstatus"), 'align'=>'align="left"', 'checked'=>1),


	//'t.entity'=>array('label'=>$langs->trans("Entity"), 'checked'=>1, 'enabled'=>(! empty($conf->multicompany->enabled) && empty($conf->multicompany->transverse_mode))),
	't.datec'=>array('label'=>$langs->trans("DateCreationShort"), 'align'=>'align="left"', 'checked'=>0, 'position'=>500),
	't.tms'=>array('label'=>$langs->trans("DateModificationShort"), 'align'=>'align="left"', 'checked'=>0, 'position'=>500),
	//'t.statut'=>array('label'=>$langs->trans("Status"), 'checked'=>1, 'position'=>1000),
);
// Extra fields
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label))
{
	foreach($extrafields->attribute_label as $key => $val)
	{
		$arrayfields["ef.".$key]=array('label'=>$extrafields->attribute_label[$key], 'checked'=>$extrafields->attribute_list[$key], 'position'=>$extrafields->attribute_pos[$key], 'enabled'=>$extrafields->attribute_perms[$key]);
	}
}


// Load object if id or ref is provided as parameter
$object=new Itemsgroupext($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}
$objItemsgroup = new Itemsgroupext($db);
$objUser = new User($db);
$objCregiongeographic = new Cregiongeographic($db);
$objCclasfin = new Cclasfin($db);
$objCtypeitem=new Ctypeitemext($db);
$objItemsproduct = new Itemsproductext($db);
$objItems = new Itemsext($db);
$objItemsregion = new Itemsregionext($db);

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

if (GETPOST('cancel')) { $action='list'; $massaction=''; }
if (! GETPOST('confirmmassaction') && $massaction != 'presend' && $massaction != 'confirm_presend') { $massaction=''; }

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	// Selection of new fields
	include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

	// Purge search criteria
	if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter.x") ||GETPOST("button_removefilter")) // All tests are required to be compatible with all browsers
	{

		$search_entity='';
		$search_ref='';
		$search_ref_ext='';
		$search_version='';
		$search_fk_type_item='';
		$search_fk_parent='';
		$search_fk_region='';
		$search_fk_sector='';
		$search_type=-1;
		$search_detail='';
		$search_fk_unit='';
		$search_especification='';
		$search_plane='';
		$search_quant='';
		$search_amount='';
		$search_fk_user_create='';
		$search_fk_user_mod='';
		$search_status=9;
		$_SESSION['selitem']['fk_region']='';
		$_SESSION['selitem']['fk_sector']='';

		$search_date_creation='';
		$search_date_update='';
		$toselect='';
		$search_array_options=array();
		$search_active = -1;
	}
	if($action == 'list' && ($validate == $langs->trans('Validateselected') || $novalidate == $langs->trans('Novalidateselected'))  && $user->rights->budget->ite->val)
	{
		if($validate == $langs->trans('Validateselected')) $status = 1;
		elseif($novalidate == $langs->trans('Novalidateselected')) $status = 0;
		//vamos a actualizar de golpe todos los seleccionados
		$aList = GETPOST('toselect');
		$db->begin();
		//$a=1;
		foreach ((array) $aList AS $j => $fk)
		{
			if (!$error)
			{
				$res = $object->fetch($fk);
				if ($res == 1)
				{
					//echo '<hr>a '.$a;
					//$a++;
					$object->status=$status;
					$res = $object->update($user);
					if ($res<=0)
					{
						$error++;
						setEventMessages($object->error,$object->errors,'errors');
					}
					//vamos a buscar en items
					if ($object->fk_item>0)
					{
						$res = $objItems->fetch($object->fk_item);
						if ($res==1)
						{
							$objItems->status = $status;
							$res = $objItems->update($user);
							if ($res<=0)
							{
								$error++;
								setEventMessages($objItems->error,$objItems->errors,'errors');
							}
						}
						if (!$error)
						{
							//vamos a buscar itemsregion
							$filterregion = " AND t.fk_item = ".$object->fk_item;
							if ($search_fk_region>0) $filterregion.= " AND t.fk_region = ".$search_fk_region;
							if ($search_fk_sector>0) $filterregion.= " AND t.fk_sector = ".$search_fk_sector;
							//echo '<hr>resitemsregion '.
							$res = $objItemsregion->fetchAll('','',0,0,array(),'AND',$filterregion);
							if ($res>0)
							{
								$linesreg = $objItemsregion->lines;
								foreach ($linesreg AS $k =>$linereg)
								{
									$res = $objItemsregion->fetch($linereg->id);
									if ($res == 1)
									{
										$linereg->id;
										$objItemsregion->status = $status;
										$res = $objItemsregion->update($user);
										if ($res<=0)
										{
											$error++;
											setEventMessages($objItemsregion->error,$objItemsregion->errors,'errors');
										}
									}
								}
							}
						}
					}
					else

					{

					}
				}
			}
		}
		///echo '<hr>fin '.$error;exit;
		if (!$error)
		{
			$db->commit();
			setEventMessages($langs->trans('Satisfactoryupdate'),null,'mesgs');
			header('Location: '.$_SERVER['PHP_SELF']);
			exit;
		}
		else
		{
			$db->rollback();
			$action = '';
		}
	}

	if($action == 'list' && ($activate == $langs->trans('Activateselected') || $noactivate == $langs->trans('Donotactivateselected'))  && $user->rights->budget->ite->val)
	{
		if($activate == $langs->trans('Activateselected')) $active = 1;
		elseif($noactivate == $langs->trans('Donotactivateselected')) $active = 0;
		//vamos a actualizar de golpe todos los seleccionados
		$aList = GETPOST('toselect');
		$db->begin();
		//$a=1;
		foreach ((array) $aList AS $j => $fk)
		{
			if (!$error)
			{
				$res = $object->fetch($fk);
				if ($res == 1)
				{
					//echo '<hr>a '.$a;
					//$a++;
					//$object->active=$active;
					//$res = $object->update($user);
					//if ($res<=0)
					//{
					//	$error++;
					//	setEventMessages($object->error,$object->errors,'errors');
					//}
					//vamos a buscar en items
					if ($object->fk_item>0)
					{
						//$res = $objItems->fetch($object->fk_item);
						//if ($res==1)
						//{
						//	$objItems->activate = $activate;
						//	$res = $objItems->update($user);
						//	if ($res<=0)
						//	{
						//		$error++;
						//		setEventMessages($objItems->error,$objItems->errors,'errors');
						//	}
						//}
						if (!$error)
						{
							//vamos a buscar itemsregion
							$filterregion = " AND t.fk_item = ".$object->fk_item;
							if ($search_fk_region>0) $filterregion.= " AND t.fk_region = ".$search_fk_region;
							if ($search_fk_sector>0) $filterregion.= " AND t.fk_sector = ".$search_fk_sector;
							//echo '<hr>resitemsregion '.
							$res = $objItemsregion->fetchAll('','',0,0,array(),'AND',$filterregion);
							if ($res>0)
							{
								$linesreg = $objItemsregion->lines;
								foreach ($linesreg AS $k =>$linereg)
								{
									$res = $objItemsregion->fetch($linereg->id);
									if ($res == 1)
									{
										//$linereg->id;
										$objItemsregion->active = $active;
										$res = $objItemsregion->update($user);
										if ($res<=0)
										{
											$error++;
											setEventMessages($objItemsregion->error,$objItemsregion->errors,'errors');
										}
									}
								}
							}
						}
					}
					else

					{

					}
				}
			}
		}
		///echo '<hr>fin '.$error;exit;
		if (!$error)
		{
			$db->commit();
			setEventMessages($langs->trans('Satisfactoryupdate'),null,'mesgs');
			header('Location: '.$_SERVER['PHP_SELF']);
			exit;
		}
		else
		{
			$db->rollback();
			$action = '';
		}
	}
	// Mass actions
	//$objectclass='Skeleton';
	//$objectlabel='Skeleton';
	//$permtoread = $user->rights->items->read;
	//$permtodelete = $user->rights->items->delete;
	//$uploaddir = $conf->items->dir_output;
	//include DOL_DOCUMENT_ROOT.'/core/actions_massactions.inc.php';
}


//vamos a armar el options para region geographic

$filter = " AND t.status = 1";
$res = $objCregiongeographic->fetchAll('ASC','t.ref',0,0,array(),'AND',$filter);
$optionsregion= '<option value=""></option>';
if ($res>0)
{
	//if ($res == 1) $optionsregion = '';
	$lines = $objCregiongeographic->lines;
	foreach ($lines AS $j => $line)
	{
		$selected = '';
		if ($search_fk_region == $line->id) $selected = ' selected';
		$optionsregion.= '<option value="'.$line->id.'" '.$selected.'>'.$line->ref.' - '.$line->label;
	}
}
$filter = " AND t.active = 1";
$res = $objCclasfin->fetchAll('ASC','t.ref',0,0,array(),'AND',$filter);
$optionssector= '<option value=""></option>';
if ($res>0)
{
	//if ($res == 1) $optionssector = '';
	$lines = $objCclasfin->lines;
	foreach ($lines AS $j => $line)
	{
		$selected = '';
		if ($search_fk_sector == $line->id) $selected = ' selected';
		$optionssector.= '<option value="'.$line->id.'" '.$selected.'>'.$line->ref.' - '.$line->label;
	}
}
$aSino = array(0=>$langs->trans('Not'),1=>$langs->trans('Yes'));
/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

$now=dol_now();

$form=new Form($db);

//$help_url="EN:Module_Customers_Orders|FR:Module_Commandes_Clients|ES:Módulo_Pedidos_de_clientes";
$help_url='';
$title = $langs->trans('Items');

// Put here content of your page

// Example : Adding jquery code
print '<script type="text/javascript" language="javascript">';
print 'jQuery(document).ready(function() {';
print '	function init_myfunc()';
print '	{';
print '		jQuery("#myid").removeAttr("disabled");';
print '		jQuery("#myid").attr("disabled","disabled");';
print '}';
print 'init_myfunc();';
print 'jQuery("#mybutton").click(function() {';
print '	init_myfunc();';
print '	});';
print '	});';
print '</script>';


$sql  = "SELECT";
$sql .= " g.rowid AS rowidg,";
$sql .= " t.rowid,";
$sql .= " g.entity,";
$sql .= " g.ref,";
$sql .= " g.ref_ext,";
$sql .= " g.version,";
$sql .= " t.fk_type_item,";
$sql .= " g.fk_parent,";
$sql .= " g.type,";
$sql .= " g.type AS typeo,";
$sql .= " g.detail,";
$sql .= " g.fk_item,";
$sql .= " g.fk_unit,";
$sql .= " t.detail AS detailitem,";
$sql .= " t.especification,";
$sql .= " t.plane,";
$sql .= " t.quant,";
		//$sql .= " t.amount,";
$sql .= " t.fk_user_create,";
$sql .= " t.fk_user_mod,";
$sql .= " t.datec,";
$sql .= " t.datem,";
$sql .= " t.tms,";
$sql .= " i.status";
$sql.= " , u.lastname AS lastname";
$sql.= " , u.firstname AS firstname";
$sql.= " , c.label AS labelunit";
$sql.= " , c.short_label AS shortlabelunit";
$sql.= " , i.fk_region";
$sql.= " , i.fk_sector";
$sql.= " , i.hour_production";
$sql.= " , i.amount_noprod";
$sql.= " , i.amount";
$sql.= " , i.active AS active";
$sql.= " , i.status AS statusregion";
$sql.= " , r.ref AS refregion";
$sql.= " , r.label AS labelregion";
$sql.= " , f.ref AS refsector";
$sql.= " , f.label AS labelsector";
$sql.= " , ti.code AS reftypeitem";
$sql.= " , ti.label AS labeltypeitem";


// Add fields from extrafields
foreach ($extrafields->attribute_label as $key => $val) $sql.=($extrafields->attribute_type[$key] != 'separate' ? ",ef.".$key.' as options_'.$key : '');
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);
// Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."items_group as g";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."items as t ON g.fk_item = t.rowid";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."items_extrafields as ef on (t.rowid = ef.fk_object)";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX.'items_region AS i ON t.rowid = i.fk_item';
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX.'items_group AS ig ON g.fk_parent = ig.rowid';

$sql.= " LEFT JOIN ".MAIN_DB_PREFIX.'user AS u ON t.fk_user_create = u.rowid';
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX.'c_units AS c ON t.fk_unit = c.rowid';
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_region_geographic AS r ON i.fk_region = r.rowid";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_clasfin AS f ON i.fk_sector = f.rowid";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_type_item AS ti ON t.fk_type_item = ti.rowid";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_entity) $sql.= natural_search("entity",$search_entity);
if ($search_ref) $sql.= natural_search("g.ref",$search_ref);
if ($search_ref_ext) $sql.= natural_search("ref_ext",$search_ref_ext);
if ($search_version) $sql.= natural_search("g.version",$search_version);
if ($search_fk_type_item) $sql.= natural_search("fk_type_item",$search_fk_type_item);
//if ($search_fk_region) $sql.= natural_search(array("i.fk_region","r.ref","r.label"),$search_fk_region);
//if ($search_fk_sector) $sql.= natural_search(array("i.fk_sector","f.ref","f.label"),$search_fk_sector);
if ($search_fk_region) $sql.= natural_search("fk_region",$search_fk_region);
if ($search_fk_sector) $sql.= natural_search("fk_sector",$search_fk_sector);
if ($search_fk_parent) $sql.= natural_search(array("g.fk_parent","ig.ref"),$search_fk_parent);
if ($search_type != -1) $sql.= natural_search("g.type",$search_type,2);
if ($search_detail) $sql.= natural_search("t.detail",$search_detail);
if ($search_fk_unit) $sql.= natural_search(array("t.fk_unit","c.label","c.short_label"),$search_fk_unit);
if ($search_especification) $sql.= natural_search("especification",$search_especification);
if ($search_plane) $sql.= natural_search("plane",$search_plane);
if ($search_quant) $sql.= natural_search("quant",$search_quant);
if ($search_amount) $sql.= natural_search("amount",$search_amount);
if ($search_fk_user_create) $sql.= natural_search(array("t.fk_user_create","u.lastname","u.firstname"),$search_fk_user_create);
if ($search_fk_user_mod) $sql.= natural_search(array("t.fk_user_mod","u.firstname","u.lastname"),$search_fk_user_mod);
if ($search_active!=-1) $sql.= natural_search("i.active",$search_active);
if ($search_status!=9) $sql.= natural_search("i.status",$search_status);
if ($sall)          $sql.= natural_search(array_keys($fieldstosearchall), $sall);
// Add where from extra fields
foreach ($search_array_options as $key => $val)
{
	$crit=$val;
	$tmpkey=preg_replace('/search_options_/','',$key);
	$typ=$extrafields->attribute_type[$tmpkey];
	$mode=0;
	if (in_array($typ, array('int','double'))) $mode=1;    // Search on a numeric
	if ($val && ( ($crit != '' && ! in_array($typ, array('select'))) || ! empty($crit)))
	{
		$sql .= natural_search('ef.'.$tmpkey, $crit, $mode);
	}
}
// Add where from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListWhere',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.=$db->order($sortfield,$sortorder);
//$sql.= $db->plimit($conf->liste_limit+1, $offset);

// Count total nb of records
$nbtotalofrecords = '';
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
	$result = $db->query($sql);
	$nbtotalofrecords = $db->num_rows($result);
}

$sql.= $db->plimit($limit+1, $offset);

dol_syslog($script_file, LOG_DEBUG);
$resql=$db->query($sql);
if (! $resql)
{
	dol_print_error($db);
	exit;
}

$num = $db->num_rows($resql);

// Direct jump if only one record found
if ($num == 1 && ! empty($conf->global->MAIN_SEARCH_DIRECT_OPEN_IF_ONLY_ONE) && $search_all)
{
	$obj = $db->fetch_object($resql);
	$id = $obj->rowid;
	header("Location: ".DOL_URL_ROOT.'/items/card.php?id='.$id);
	exit;
}

llxHeader('', $title, $help_url);

$arrayofselected=is_array($toselect)?$toselect:array();

$param='';
if (! empty($contextpage) && $contextpage != $_SERVER["PHP_SELF"]) $param.='&contextpage='.$contextpage;
if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;
if ($search_status != '') $param.= '&amp;search_status='.urlencode($search_status);
if ($search_field2 != '') $param.= '&amp;search_field2='.urlencode($search_field2);
if ($optioncss != '') $param.='&optioncss='.$optioncss;
// Add $param from extra fields
foreach ($search_array_options as $key => $val)
{
	$crit=$val;
	$tmpkey=preg_replace('/search_options_/','',$key);
	if ($val != '') $param.='&search_options_'.$tmpkey.'='.urlencode($val);
}

$arrayofmassactions =  array(
	'presend'=>$langs->trans("SendByMail"),
	'builddoc'=>$langs->trans("PDFMerge"),
);
if ($user->rights->budget->supprimer) $arrayofmassactions['delete']=$langs->trans("Delete");
if ($massaction == 'presend') $arrayofmassactions=array();
$massactionbutton=$form->selectMassAction('', $arrayofmassactions);

print '<form method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
print '<input type="hidden" name="action" value="list">';
print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
print '<input type="hidden" name="contextpage" value="'.$contextpage.'">';

print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, '', $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);

if ($sall)
{
	foreach($fieldstosearchall as $key => $val) $fieldstosearchall[$key]=$langs->trans($val);
	print $langs->trans("FilterOnInto", $sall) . join(', ',$fieldstosearchall);
}

$moreforfilter = '';
$moreforfilter.='<div class="divsearchfield">';
//$moreforfilter.= $langs->trans('MyFilter') . ': <input type="text" name="search_myfield" value="'.dol_escape_htmltag($search_myfield).'">';
$moreforfilter.= '</div>';

$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldPreListTitle',$parameters);    // Note that $action and $object may have been modified by hook
if (empty($reshook)) $moreforfilter .= $hookmanager->resPrint;
else $moreforfilter = $hookmanager->resPrint;

if (! empty($moreforfilter))
{
	print '<div class="liste_titre liste_titre_bydiv centpercent">';
	print $moreforfilter;
	print '</div>';
}

$varpage=empty($contextpage)?$_SERVER["PHP_SELF"]:$contextpage;
$selectedfields=$form->multiSelectArrayWithCheckbox('selectedfields', $arrayfields, $varpage);	// This also change content of $arrayfields

print '<div class="div-table-responsive">';
print '<table style="font-size:13px;" class="liste '.($moreforfilter?"listwithfilterbefore":"").'">';
// Fields title
print '<tr class="liste_titre">';
//
if (! empty($arrayfields['g.entity']['checked'])) print_liste_field_titre($arrayfields['g.entity']['label'],$_SERVER['PHP_SELF'],'g.entity','',$params,$arrayfields['g.entity']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['g.ref']['checked'])) print_liste_field_titre($arrayfields['g.ref']['label'],$_SERVER['PHP_SELF'],'g.ref','',$params,$arrayfields['g.ref']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['g.ref_ext']['checked'])) print_liste_field_titre($arrayfields['g.ref_ext']['label'],$_SERVER['PHP_SELF'],'g.ref_ext','',$params,$arrayfields['g.ref_ext']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['g.version']['checked'])) print_liste_field_titre($arrayfields['g.version']['label'],$_SERVER['PHP_SELF'],'g.version','',$params,$arrayfields['g.version']['align'],$sortfield,$sortorder);

//if (! empty($arrayfields['t.fk_type_item']['checked'])) print_liste_field_titre($arrayfields['t.fk_type_item']['label'],$_SERVER['PHP_SELF'],'t.fk_type_item','',$params,$arrayfields['t.fk_type_item']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['g.fk_parent']['checked'])) print_liste_field_titre($arrayfields['g.fk_parent']['label'],$_SERVER['PHP_SELF'],'g.fk_parent','',$params,$arrayfields['g.fk_parent']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['i.fk_region']['checked'])) print_liste_field_titre($arrayfields['i.fk_region']['label'],$_SERVER['PHP_SELF'],'i.fk_region','',$params,$arrayfields['i.fk_region']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['i.fk_sector']['checked'])) print_liste_field_titre($arrayfields['i.fk_sector']['label'],$_SERVER['PHP_SELF'],'i.fk_sector','',$params,$arrayfields['i.fk_sector']['align'],$sortfield,$sortorder);

if (! empty($arrayfields['g.type']['checked'])) print_liste_field_titre($arrayfields['g.type']['label'],$_SERVER['PHP_SELF'],'g.type','',$params,$arrayfields['g.type']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['g.detail']['checked'])) print_liste_field_titre($arrayfields['g.detail']['label'],$_SERVER['PHP_SELF'],'g.detail','',$params,$arrayfields['g.detail']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['g.fk_unit']['checked'])) print_liste_field_titre($arrayfields['g.fk_unit']['label'],$_SERVER['PHP_SELF'],'g.fk_unit','',$params,$arrayfields['g.fk_unit']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.especification']['checked'])) print_liste_field_titre($arrayfields['t.especification']['label'],$_SERVER['PHP_SELF'],'t.especification','',$params,$arrayfields['t.especification']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.plane']['checked'])) print_liste_field_titre($arrayfields['t.plane']['label'],$_SERVER['PHP_SELF'],'t.plane','',$params,$arrayfields['t.plane']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.quant']['checked'])) print_liste_field_titre($arrayfields['t.quant']['label'],$_SERVER['PHP_SELF'],'t.quant','',$params,$arrayfields['t.quant']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['i.amount_noprod']['checked'])) print_liste_field_titre($arrayfields['i.amount_noprod']['label'],$_SERVER['PHP_SELF'],'i.amount_noprod','',$params,$arrayfields['i.amount_noprod']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['i.amount']['checked'])) print_liste_field_titre($arrayfields['i.amount']['label'],$_SERVER['PHP_SELF'],'i.amount','',$params,$arrayfields['i.amount']['align'],$sortfield,$sortorder);

if (! empty($arrayfields['t.amount']['checked'])) print_liste_field_titre($arrayfields['t.amount']['label'],$_SERVER['PHP_SELF'],'t.amount','',$params,$arrayfields['t.amount']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_create']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_create']['label'],$_SERVER['PHP_SELF'],'t.fk_user_create','',$params,$arrayfields['t.fk_user_create']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_mod']['label'],$_SERVER['PHP_SELF'],'t.fk_user_mod','',$params,$arrayfields['t.fk_user_mod']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['i.active']['checked'])) print_liste_field_titre($arrayfields['i.active']['label'],$_SERVER['PHP_SELF'],'i.active','',$params,$arrayfields['i.active']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['i.status']['checked'])) print_liste_field_titre($arrayfields['i.status']['label'],$_SERVER['PHP_SELF'],'i.status','',$params,$arrayfields['i.status']['align'],$sortfield,$sortorder);

//if (! empty($arrayfields['t.field1']['checked'])) print_liste_field_titre($arrayfields['t.field1']['label'],$_SERVER['PHP_SELF'],'t.field1','',$param,$arrayfields['t.field1']['align'],$sortfield,$sortorder);
//if (! empty($arrayfields['t.field2']['checked'])) print_liste_field_titre($arrayfields['t.field2']['label'],$_SERVER['PHP_SELF'],'t.field2','',$param,$arrayfields['t.field1']['align'],$sortfield,$sortorder);
// Extra fields
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label))
{
	foreach($extrafields->attribute_label as $key => $val)
	{
		if (! empty($arrayfields["ef.".$key]['checked']))
		{
			$align=$extrafields->getAlignFlag($key);
			print_liste_field_titre($extralabels[$key],$_SERVER["PHP_SELF"],"ef.".$key,"",$param,($align?'align="'.$align.'"':''),$sortfield,$sortorder);
		}
	}
}
// Hook fields
$parameters=array('arrayfields'=>$arrayfields);
$reshook=$hookmanager->executeHooks('printFieldListTitle',$parameters);
// Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;
if (! empty($arrayfields['t.datec']['checked']))  print_liste_field_titre($arrayfields['t.datec']['label'],$_SERVER["PHP_SELF"],"t.datec","",$param,'align="center" class="nowrap"',$sortfield,$sortorder);
if (! empty($arrayfields['t.tms']['checked']))    print_liste_field_titre($arrayfields['t.tms']['label'],$_SERVER["PHP_SELF"],"t.tms","",$param,'align="center" class="nowrap"',$sortfield,$sortorder);
//if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($langs->trans("Status"),$_SERVER["PHP_SELF"],"t.status","",$param,'align="center"',$sortfield,$sortorder);
print_liste_field_titre($selectedfields, $_SERVER["PHP_SELF"],"",'','','align="right"',$sortfield,$sortorder,'maxwidthsearch ');
print '</tr>'."\n";

// Fields title search
print '<tr class="liste_titre">';
//
if (! empty($arrayfields['g.entity']['checked'])) print '<td class="liste_titre" '.$arrayfields['g.entity']['align'].'><input type="text" class="flat" name="search_entity" value="'.$search_entity.'" size="10"></td>';
if (! empty($arrayfields['g.ref']['checked'])) print '<td class="liste_titre" '.$arrayfields['g.ref']['align'].'><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
if (! empty($arrayfields['g.ref_ext']['checked'])) print '<td class="liste_titre" '.$arrayfields['g.ref_ext']['align'].'><input type="text" class="flat" name="search_ref_ext" value="'.$search_ref_ext.'" size="10"></td>';
if (! empty($arrayfields['g.version']['checked'])) print '<td class="liste_titre" '.$arrayfields['g.version']['align'].'><input type="text" class="flat" name="search_version" value="'.$search_version.'" size="5"></td>';

//if (! empty($arrayfields['t.fk_type_item']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_type_item']['align'].'><input type="text" class="flat" name="search_fk_type_item" value="'.$search_fk_type_item.'" size="10"></td>';
if (! empty($arrayfields['g.fk_parent']['checked'])) print '<td class="liste_titre" '.$arrayfields['g.fk_parent']['align'].'><input type="text" class="flat" name="search_fk_parent" value="'.$search_fk_parent.'" size="10"></td>';
if (! empty($arrayfields['i.fk_region']['checked']))
{
	print '<td>';
	print '<select name="search_fk_region" style="max-width:65px;">'.$optionsregion.'</select>';
	print '</td>';
}
if (! empty($arrayfields['i.fk_sector']['checked']))
{
	print '<td>';
	print '<select name="search_fk_sector" style="max-width:65px;">'.$optionssector.'</select>';
	print '</td>';
}
if (! empty($arrayfields['g.type']['checked']))
{
	print '<td class="liste_titre" '.$arrayfields['g.type']['align'].'>';
	print $form->selectarray('search_type',$aSino,$search_type,1);
	print '</td>';
}
if (! empty($arrayfields['g.detail']['checked'])) print '<td class="liste_titre" '.$arrayfields['g.detail']['align'].'><input type="text" class="flat" name="search_detail" value="'.$search_detail.'" size="10"></td>';
if (! empty($arrayfields['g.fk_unit']['checked'])) print '<td class="liste_titre" '.$arrayfields['g.fk_unit']['align'].'><input type="text" class="flat" name="search_fk_unit" value="'.$search_fk_unit.'" size="10"></td>';
if (! empty($arrayfields['t.especification']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.especification']['align'].'><input type="text" class="flat" name="search_especification" value="'.$search_especification.'" size="10"></td>';
if (! empty($arrayfields['t.plane']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.plane']['align'].'><input type="text" class="flat" name="search_plane" value="'.$search_plane.'" size="10"></td>';
if (! empty($arrayfields['t.quant']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.quant']['align'].'><input type="text" class="flat" name="search_quant" value="'.$search_quant.'" size="10"></td>';
if (! empty($arrayfields['i.amount_noprod']['checked'])) print '<td class="liste_titre" '.$arrayfields['i.amount_noprod']['align'].'><input type="text" class="flat" name="search_amount_noprod" value="'.$search_amount_noprod.'" size="10"></td>';
if (! empty($arrayfields['i.amount']['checked'])) print '<td class="liste_titre" '.$arrayfields['i.amount']['align'].'><input type="text" class="flat" name="search_amount" value="'.$search_amount.'" size="10"></td>';

if (! empty($arrayfields['t.amount']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.amount']['align'].'><input type="text" class="flat" name="search_amount" value="'.$search_amount.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_create']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_user_create']['align'].'><input type="text" class="flat" name="search_fk_user_create" value="'.$search_fk_user_create.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_user_mod']['align'].'><input type="text" class="flat" name="search_fk_user_mod" value="'.$search_fk_user_mod.'" size="10"></td>';
//if (! empty($arrayfields['t.status']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.status']['align'].'><input type="text" class="flat" name="search_status" value="'.$search_status.'" size="10"></td>';
if (! empty($arrayfields['i.active']['checked']))
{
	print '<td class="liste_titre" '.$arrayfields['t.fk_user_mod']['align'].'>';
	print $form->selectyesno('search_active',$search_active,1,false,1);
	print '</td>';
}

if (! empty($arrayfields['i.status']['checked'])){
	print '<td class="liste_titre center">';
	print $form->selectarray('search_status', $aStatus, $search_status, 0);
		//print $form->selectarray('search_status', $aStatus, $search_status, 0, 0, 0, '', 0, 0, 0, '', 'maxwidth100');
	print '</td>';

}


//if (! empty($arrayfields['t.field1']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_field1" value="'.$search_field1.'" size="10"></td>';
//if (! empty($arrayfields['t.field2']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_field2" value="'.$search_field2.'" size="10"></td>';
// Extra fields
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label))
{
	foreach($extrafields->attribute_label as $key => $val)
	{
		if (! empty($arrayfields["ef.".$key]['checked']))
		{
			$align=$extrafields->getAlignFlag($key);
			$typeofextrafield=$extrafields->attribute_type[$key];
			print '<td class="liste_titre'.($align?' '.$align:'').'">';
			if (in_array($typeofextrafield, array('varchar', 'int', 'double', 'select')))
			{
				$crit=$val;
				$tmpkey=preg_replace('/search_options_/','',$key);
				$searchclass='';
				if (in_array($typeofextrafield, array('varchar', 'select'))) $searchclass='searchstring';
				if (in_array($typeofextrafield, array('int', 'double'))) $searchclass='searchnum';
				print '<input class="flat'.($searchclass?' '.$searchclass:'').'" size="4" type="text" name="search_options_'.$tmpkey.'" value="'.dol_escape_htmltag($search_array_options['search_options_'.$tmpkey]).'">';
			}
			print '</td>';
		}
	}
}
// Fields from hook
$parameters=array('arrayfields'=>$arrayfields);
$reshook=$hookmanager->executeHooks('printFieldListOption',$parameters);    // Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;
if (! empty($arrayfields['t.datec']['checked']))
{
	// Date creation
	print '<td class="liste_titre">';
	print '</td>';
}
if (! empty($arrayfields['t.tms']['checked']))
{
	// Date modification
	print '<td class="liste_titre">';
	print '</td>';
}

/*if (! empty($arrayfields['u.statut']['checked']))
{
	// Status
	print '<td class="liste_titre" align="center">';
	print $form->selectarray('search_statut', array('-1'=>'','0'=>$langs->trans('Disabled'),'1'=>$langs->trans('Enabled')),$search_statut);
	print '</td>';
}*/
// Action column
print '<td class="liste_titre" align="right">';
$searchpitco=$form->showFilterAndCheckAddButtons($massactionbutton?1:0, 'checkforselect', 1);
print $searchpitco;
print '</td>';
print '</tr>'."\n";


$i=0;
$var=true;
$totalarray=array();
while ($i < min($num, $limit))
{
	$obj = $db->fetch_object($resql);
	if ($obj)
	{
		$var = !$var;
		$type = $obj->type;
		// Show here line of result
		print '<tr '.$bc[$var].'>';
		// LIST_OF_TD_FIELDS_LIST
		foreach ($arrayfields as $key => $value) {
			$align='';
			if (!empty($arrayfields[$key]['checked'])) {
				//$key2 = str_replace('t.', '', $key);
				$aKey = explode('.',$key);
				$key2 = $aKey[1];
				if ($key2 == 'ref')
				{
					$objItemsgroup->id = $obj->rowidg;
					$objItemsgroup->ref = $obj->ref;
					$objItemsgroup->label = $obj->label;
					$obj->$key2 = $objItemsgroup->getNomUrladd();
				}
				if ($key2 == 'active')
				{
					if (empty($obj->typeo) || is_null($obj->typeo) || $obj->typeo == 0)
					{
						$active = $langs->trans('Not');
						if ($obj->$key2) $active = $langs->trans('Yes');
						$obj->$key2 = $active;
					}
					else
						$obj->$key2='';
				}
				if ($key2 == 'status')
				{
					$objItemsregion->status = $obj->$key2;
					$obj->$key2 = $objItemsregion->getLibStatut(6);
				}
				if ($key2 == 'fk_user_create' || $key2 == 'fk_user_mod')
				{
					$res = $objUser->fetch($obj->$key2);
					if ($res == 1)
						$obj->$key2 = $objUser->getNomUrl(1);
				}
				if ($key2 == 'fk_parent')
				{
					if ($obj->$key2>0)
					{
						$object->fetch($obj->$key2);
						$obj->$key2 = $object->getNomUrladd();
					}
					else
						$obj->$key2= '';
				}
				if ($key2 == 'fk_region')
				{
					$objCregiongeographic->id = $obj->fk_region;
					$objCregiongeographic->ref = $obj->refregion;
					$objCregiongeographic->label = $obj->labelregion;
					$obj->$key2= $objCregiongeographic->getNomUrl();
				}
				if ($key2 == 'fk_sector')
				{
					$objCclasfin->id = $obj->fk_sector;
					$objCclasfin->ref = $obj->refsector;
					$objCclasfin->label = $obj->labelsector;
					$obj->$key2= $objCclasfin->getNomUrl();
				}
				if ($key2 == 'fk_type_item')
				{
					$objCtypeitem->id = $obj->fk_type_item;
					$objCtypeitem->ref = $obj->reftypeitem;
					$objCtypeitem->label = $obj->labeltypeitem;
					$obj->$key2= $objCtypeitem->getNomUrl();
				}
				if ($key2 == 'type')
				{
					$obj->$key2 = ($obj->$key2?$langs->trans('Yes'):$langs->trans('Not'));
				}
				if ($key2=='quant') $align=' align="right"';
				if ($key2=='amount') $align=' align="right"';
				if ($key2 == 'fk_unit')
				{
					$obj->$key2 = $obj->shortlabelunit;
				}
				if ($key2 == 'detail')
				{
					if ($type==0) $obj->$key2 = $obj->detailitem;

				}
				print '<td '.$arrayfields[$key]['align'].'>' . $obj->$key2 . '</td>';
				if (!$i)
					$totalarray['nbfield'] ++;
			}
		}
		// Extra fields
		if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label))
		{
			foreach($extrafields->attribute_label as $key => $val)
			{
				if (! empty($arrayfields["ef.".$key]['checked']))
				{
					print '<td';
					$align=$extrafields->getAlignFlag($key);
					if ($align) print ' align="'.$align.'"';
					print '>';
					$tmpkey='options_'.$key;
					print $extrafields->showOutputField($key, $obj->$tmpkey, '', 1);
					print '</td>';
					if (! $i) $totalarray['nbfield']++;
				}
			}
		}
		// Fields from hook
		$parameters=array('arrayfields'=>$arrayfields, 'obj'=>$obj);
		$reshook=$hookmanager->executeHooks('printFieldListValue',$parameters);    // Note that $action and $object may have been modified by hook
		print $hookmanager->resPrint;
		// Date creation
		if (! empty($arrayfields['t.datec']['checked']))
		{
			print '<td align="center">';
			print dol_print_date($db->jdate($obj->date_creation), 'dayhour');
			print '</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		// Date modification
		if (! empty($arrayfields['t.tms']['checked']))
		{
			print '<td align="center">';
			print dol_print_date($db->jdate($obj->date_update), 'dayhour');
			print '</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		// Status
		/*
		if (! empty($arrayfields['u.statut']['checked']))
		{
		  $userstatic->statut=$obj->statut;
		  print '<td align="center">'.$userstatic->getLibStatut(3).'</td>';
		}*/

		// Action column
		print '<td class="nowrap" align="center">';
		if ($massactionbutton || $massaction)
		// If we are in select mode (massactionbutton defined) or if we have already selected and sent an action ($massaction) defined
		{
			$selected=0;
			if (in_array($obj->rowidg, $arrayofselected)) $selected=1;
			print '<input id="cb'.$obj->rowidg.'" class="flat checkforselect" type="checkbox" name="toselect[]" value="'.$obj->rowidg.'"'.($selected?' checked="checked"':'').'>';
		}
		print '</td>';
		if (! $i) $totalarray['nbfield']++;

		print '</tr>';
	}
	$i++;
}

// Show total line
if (isset($totalarray['totalhtfield']))
{
	print '<tr class="liste_total">';
	$i=0;
	while ($i < $totalarray['nbfield'])
	{
		$i++;
		if ($i == 1)
		{
			if ($num < $limit && empty($offset)) print '<td align="left">'.$langs->trans("Total").'</td>';
			else print '<td align="left">'.$langs->trans("Totalforthispage").'</td>';
		}
		elseif ($totalarray['totalhtfield'] == $i) print '<td align="right">'.price($totalarray['totalht']).'</td>';
		elseif ($totalarray['totalvatfield'] == $i) print '<td align="right">'.price($totalarray['totalvat']).'</td>';
		elseif ($totalarray['totalttcfield'] == $i) print '<td align="right">'.price($totalarray['totalttc']).'</td>';
		else print '<td></td>';
	}
	print '</tr>';
}

$db->free($resql);

$parameters=array('arrayfields'=>$arrayfields, 'sql'=>$sql);
$reshook=$hookmanager->executeHooks('printFieldListFooter',$parameters);    // Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;

print '</table>'."\n";
print '</div>'."\n";
if ($user->rights->budget->ite->val)
{
	print '<div class="tabsAction">';
	print '<input type="submit" class="butAction" name="validate" value="'.$langs->trans('Validateselected').'">';
	print '<input type="submit" class="butAction" name="novalidate" value="'.$langs->trans('Novalidateselected').'">';
	print '<input type="submit" class="butAction" name="activate" value="'.$langs->trans('Activateselected').'">';
	print '<input type="submit" class="butAction" name="noactivate" value="'.$langs->trans('Donotactivateselected').'">';
	print '</div>';
}
print '</form>'."\n";


print '<div class="tabsAction">';
if ($user->rights->budget->ite->crear)
{
	print '<a class="butAction" href="' . dol_buildpath('/budget/items/card.php',1).'?action=create">' . $langs->trans('Create') . '</a>';
	print '<a class="butAction" href="' . dol_buildpath('/budget/items/clone.php',1).'?action=create">' . $langs->trans('Clone') . '</a>';
	print '<a class="butAction" href="' . dol_buildpath('/budget/items/recalculate.php',1).'?action=create">' . $langs->trans('Recalculate') . '</a>';
}
if ($user->rights->budget->ite->upload)
{
	print '<a class="butAction" href="' . dol_buildpath('/budget/items/import.php',1).'?action=create">' . $langs->trans('Uploadfileitem') . '</a>';
	print '<a class="butAction" href="' . dol_buildpath('/budget/items/importresource.php',1).'?action=create">' . $langs->trans('Uploadfileresource') . '</a>';
	print '<a class="butAction" href="' . dol_buildpath('/budget/items/importperformance.php',1).'?action=create">' . $langs->trans('Uploadfileperformance') . '</a>';
}
if ($user->rights->budget->ite->fact)
{
	print '<a class="butAction" href="' . dol_buildpath('/budget/items/changefactor.php',1).'?action=create">' . $langs->trans('Changefactorproduction') . '</a>';
}
print '</div>';

if ($user->rights->budget->ite->exp)
{
	print '<div class="tabsAction">';
	print '<a class="butAction" href="' . dol_buildpath('/budget/items/exporttot.php',1).'?action=export">' . $langs->trans('Exportall') . '</a>';
	print '<a class="butAction" href="' . dol_buildpath('/budget/items/export.php',1).'?action=export">' . $langs->trans('Exportitem') . '</a>';
	print '<a class="butAction" href="' . dol_buildpath('/budget/items/export.php',1).'?action=exportresource">' . $langs->trans('Exportresource') . '</a>';
	print '</div>';
}
// End of page
llxFooter();
$db->close();
