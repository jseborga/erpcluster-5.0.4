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
 *   	\file       budget/itemsproduct_list.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-04-27 17:07
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


$search_all=trim(GETPOST("sall"));

$search_ref=GETPOST('search_ref','alpha');
$search_fk_item=GETPOST('search_fk_item','int');
$search_group_structure=GETPOST('search_group_structure','alpha');
$search_fk_product=GETPOST('search_fk_product','int');
$search_label=GETPOST('search_label','alpha');
$search_formula=GETPOST('search_formula','alpha');
$search_units=GETPOST('search_units','int');
$search_commander=GETPOST('search_commander','int');
$search_performance=GETPOST('search_performance','alpha');
$search_price_productive=GETPOST('search_price_productive','alpha');
$search_price_improductive=GETPOST('search_price_improductive','alpha');
$search_active=GETPOST('search_active','int');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_status=GETPOST('search_status','int');


$search_myfield=GETPOST('search_myfield');
$optioncss = GETPOST('optioncss','alpha');

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
if (! $sortfield) $sortfield="t.group_structure,t.ref"; // Set here default search field
if (! $sortorder) $sortorder="ASC";

// Protection if external user
$socid=0;
if ($user->societe_id > 0)
{
	$socid = $user->societe_id;
	//accessforbidden();
}

// Initialize technical object to manage context to save list fields
$contextpage=GETPOST('contextpage','aZ')?GETPOST('contextpage','aZ'):'budgetitemsproductlist';

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

	't.ref'=>array('label'=>$langs->trans("Fieldref"), 'align'=>'align="left"', 'checked'=>1),
	't.fk_item'=>array('label'=>$langs->trans("Fieldfk_item"), 'align'=>'align="left"', 'checked'=>0),
	't.group_structure'=>array('label'=>$langs->trans("Fieldgroup_structure"), 'align'=>'align="left"', 'checked'=>1),
	't.fk_product'=>array('label'=>$langs->trans("Fieldfk_product"), 'align'=>'align="left"', 'checked'=>1),
	't.label'=>array('label'=>$langs->trans("Fieldlabel"), 'align'=>'align="left"', 'checked'=>1),
	't.fk_unit'=>array('label'=>$langs->trans("Fieldfk_unit"), 'align'=>'align="left"', 'checked'=>1),
	't.formula'=>array('label'=>$langs->trans("Fieldformula"), 'align'=>'align="left"', 'checked'=>0),
	'r.fk_origin'=>array('label'=>$langs->trans("Fieldfk_origin"), 'align'=>'align="right"', 'checked'=>1),
	'r.percent_origin'=>array('label'=>$langs->trans("Fieldpercent_origin"), 'align'=>'align="right"', 'checked'=>1),
	'r.units'=>array('label'=>$langs->trans("Fieldquantity"), 'align'=>'align="right"', 'checked'=>1),
	'r.commander'=>array('label'=>$langs->trans("Fieldcommander"), 'align'=>'align="right"', 'checked'=>1),
	'r.performance'=>array('label'=>$langs->trans("Fieldperformance"), 'align'=>'align="right"', 'checked'=>1),
	'r.price_productive'=>array('label'=>$langs->trans("Fieldprice_productive"), 'align'=>'align="right"', 'checked'=>1),
	'r.price_improductive'=>array('label'=>$langs->trans("Fieldprice_improductive"), 'align'=>'align="right"', 'checked'=>1),
	'r.amount_noprod'=>array('label'=>$langs->trans("Fieldcost_improductive"), 'align'=>'align="right"', 'checked'=>1),
	'r.amount'=>array('label'=>$langs->trans("Fieldcost_productive"), 'align'=>'align="right"', 'checked'=>1),
	'r.cost_direct'=>array('label'=>$langs->trans("Fieldcost_direct"), 'align'=>'align="right"', 'checked'=>1),
	't.active'=>array('label'=>$langs->trans("Fieldactive"), 'align'=>'align="left"', 'checked'=>1),
	't.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'align'=>'align="left"', 'checked'=>0),
	't.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'align'=>'align="left"', 'checked'=>0),
	't.status'=>array('label'=>$langs->trans("Fieldstatus"), 'align'=>'align="left"', 'checked'=>0),


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

		$search_ref='';
		$search_fk_item='';
		$search_group_structure='';
		$search_fk_product='';
		$search_label='';
		$search_formula='';
		$search_units='';
		$search_commander='';
		$search_performance='';
		$search_price_productive='';
		$search_price_improductive='';
		$search_active='';
		$search_fk_user_create='';
		$search_fk_user_mod='';
		$search_status='';


		$search_date_creation='';
		$search_date_update='';
		$toselect='';
		$search_array_options=array();
	}

}



/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

$now=dol_now();

$form=new Form($db);

//$help_url="EN:Module_Customers_Orders|FR:Module_Commandes_Clients|ES:Módulo_Pedidos_de_clientes";
$help_url='';
$title = $langs->trans('MyModuleListTitle');

// Put here content of your page


$sql = "SELECT";
$sql.= " t.rowid,";

$sql .= " t.ref,";
$sql .= " t.fk_item,";
$sql .= " t.group_structure,";
$sql .= " t.fk_product,";
$sql .= " t.label,";
$sql .= " t.fk_unit,";
$sql .= " t.formula,";
$sql .= " t.active,";
$sql .= " t.fk_user_create,";
$sql .= " t.fk_user_mod,";
$sql .= " t.datec,";
$sql .= " t.datem,";
$sql .= " t.tms,";
$sql .= " t.status,";
$sql .= " r.units,";
$sql .= " r.commander,";
$sql .= " r.performance,";
$sql .= " r.price_productive,";
$sql .= " r.price_improductive,";
$sql .= " r.amount_noprod,";
$sql .= " r.amount, ";
$sql .= " r.cost_direct,";
$sql .= " r.fk_origin,";
$sql .= " r.percent_origin ";

// Add fields from extrafields
foreach ($extrafields->attribute_label as $key => $val) $sql.=($extrafields->attribute_type[$key] != 'separate' ? ",ef.".$key.' as options_'.$key : '');
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);
// Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."items_product as t";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."items_product_region AS r ON r.fk_item_product = t.rowid ";

if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."items_product_extrafields as ef on (t.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
$sql.= " AND t.fk_item = ".$object->fk_item;
if ($fk_region>0) $sql.= " AND r.fk_region = ".$fk_region;
if ($fk_sector>0) $sql.= " AND r.fk_sector = ".$fk_sector;
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_ref) $sql.= natural_search("ref",$search_ref);
if ($search_fk_item) $sql.= natural_search("fk_item",$search_fk_item);
if ($search_group_structure) $sql.= natural_search("group_structure",$search_group_structure);
if ($search_fk_product) $sql.= natural_search("fk_product",$search_fk_product);
if ($search_label) $sql.= natural_search("label",$search_label);
if ($search_formula) $sql.= natural_search("formula",$search_formula);
if ($search_units) $sql.= natural_search("units",$search_units);
if ($search_commander) $sql.= natural_search("commander",$search_commander);
if ($search_performance) $sql.= natural_search("performance",$search_performance);
if ($search_price_productive) $sql.= natural_search("price_productive",$search_price_productive);
if ($search_price_improductive) $sql.= natural_search("price_improductive",$search_price_improductive);
if ($search_active) $sql.= natural_search("active",$search_active);
if ($search_fk_user_create) $sql.= natural_search("fk_user_create",$search_fk_user_create);
if ($search_fk_user_mod) $sql.= natural_search("fk_user_mod",$search_fk_user_mod);
if ($search_status) $sql.= natural_search("status",$search_status);


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

//$sql.= $db->plimit($limit+1, $offset);

dol_syslog($script_file, LOG_DEBUG);
$resql=$db->query($sql);
if (! $resql)
{
	dol_print_error($db);
	exit;
}

$num = $db->num_rows($resql);

$arrayofselected=is_array($toselect)?$toselect:array();

$param='';
if (! empty($contextpage) && $contextpage != $_SERVER["PHP_SELF"]) $param.='&contextpage='.$contextpage;
if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;
if ($search_field1 != '') $param.= '&amp;search_field1='.urlencode($search_field1);
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

if (! empty($conf->use_javascript_ajax))
{
	print "\n".'<script type="text/javascript">';
	print '$(document).ready(function () { ';

	if(!$object->manual_performance)
	{
		print ' $("#units").change(function() { document.formsup.action.value="'.$action.'"; 			document.formsup.submit(); 	}); $("#performance").change(function() { document.formsup.action.value="'.$action.'"; 	document.formsup.submit(); 	}); });';
	}
	print '</script>'."\n";
}


print '<form name="formsup" method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
print '<input type="hidden" name="id" value="'.$id.'">';
if($action == 'editline')
{
	print '<input type="hidden" name="action" value="updateline">';
	print '<input type="hidden" name="idr" value="'.$idr.'">';
}
else
	print '<input type="hidden" name="action" value="list">';
print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
print '<input type="hidden" name="contextpage" value="'.$contextpage.'">';

//print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, '', $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);

if ($sall)
{
	foreach($fieldstosearchall as $key => $val) $fieldstosearchall[$key]=$langs->trans($val);
	print $langs->trans("FilterOnInto", $sall) . join(', ',$fieldstosearchall);
}

$moreforfilter = '';
//$moreforfilter.='<div class="divsearchfield">';
//$moreforfilter.= $langs->trans('MyFilter') . ': <input type="text" name="search_myfield" value="'.dol_escape_htmltag($search_myfield).'">';
//$moreforfilter.= '</div>';

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
print '<table class="tagtable liste'.($moreforfilter?" listwithfilterbefore":"").'">'."\n";

// Fields title
print '<tr class="liste_titre">';
//
if (! empty($arrayfields['t.ref']['checked'])) print_liste_field_titre($arrayfields['t.ref']['label'],$_SERVER['PHP_SELF'],'t.ref','',$params,$arrayfields['t.ref']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_item']['checked'])) print_liste_field_titre($arrayfields['t.fk_item']['label'],$_SERVER['PHP_SELF'],'t.fk_item','',$params,$arrayfields['t.fk_item']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.group_structure']['checked'])) print_liste_field_titre($arrayfields['t.group_structure']['label'],$_SERVER['PHP_SELF'],'t.group_structure','',$params,$arrayfields['t.group_structure']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_product']['checked'])) print_liste_field_titre($arrayfields['t.fk_product']['label'],$_SERVER['PHP_SELF'],'t.fk_product','',$params,$arrayfields['t.fk_product']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.label']['checked'])) print_liste_field_titre($arrayfields['t.label']['label'],$_SERVER['PHP_SELF'],'t.label','',$params,$arrayfields['t.label']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_unit']['checked'])) print_liste_field_titre($arrayfields['t.fk_unit']['label'],$_SERVER['PHP_SELF'],'t.fk_unit','',$params,$arrayfields['t.fk_unit']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.formula']['checked'])) print_liste_field_titre($arrayfields['t.formula']['label'],$_SERVER['PHP_SELF'],'t.formula','',$params,$arrayfields['t.formula']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['r.fk_origin']['checked'])) print_liste_field_titre($arrayfields['r.fk_origin']['label'],$_SERVER['PHP_SELF'],'r.fk_origin','',$params,$arrayfields['r.fk_origin']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['r.percent_origin']['checked'])) print_liste_field_titre($arrayfields['r.percent_origin']['label'],$_SERVER['PHP_SELF'],'r.percent_origin','',$params,$arrayfields['r.percent_origin']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['r.units']['checked'])) print_liste_field_titre($arrayfields['r.units']['label'],$_SERVER['PHP_SELF'],'r.units','',$params,$arrayfields['r.units']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['r.commander']['checked'])) print_liste_field_titre($arrayfields['r.commander']['label'],$_SERVER['PHP_SELF'],'r.commander','',$params,$arrayfields['r.commander']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['r.performance']['checked'])) print_liste_field_titre($arrayfields['r.performance']['label'],$_SERVER['PHP_SELF'],'r.performance','',$params,$arrayfields['r.performance']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['r.price_productive']['checked'])) print_liste_field_titre($arrayfields['r.price_productive']['label'],$_SERVER['PHP_SELF'],'r.price_productive','',$params,$arrayfields['r.price_productive']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['r.price_improductive']['checked'])) print_liste_field_titre($arrayfields['r.price_improductive']['label'],$_SERVER['PHP_SELF'],'r.price_improductive','',$params,$arrayfields['r.price_improductive']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['r.amount_noprod']['checked'])) print_liste_field_titre($arrayfields['r.amount_noprod']['label'],$_SERVER['PHP_SELF'],'r.amount_noprod','',$params,$arrayfields['r.amount_noprod']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['r.amount']['checked'])) print_liste_field_titre($arrayfields['r.amount']['label'],$_SERVER['PHP_SELF'],'r.amount','',$params,$arrayfields['r.amount']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['r.cost_direct']['checked'])) print_liste_field_titre($arrayfields['r.cost_direct']['label'],$_SERVER['PHP_SELF'],'r.cost_direct','',$params,$arrayfields['r.cost_direct']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.active']['checked'])) print_liste_field_titre($arrayfields['t.active']['label'],$_SERVER['PHP_SELF'],'t.active','',$params,$arrayfields['t.active']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_create']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_create']['label'],$_SERVER['PHP_SELF'],'t.fk_user_create','',$params,$arrayfields['t.fk_user_create']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_mod']['label'],$_SERVER['PHP_SELF'],'t.fk_user_mod','',$params,$arrayfields['t.fk_user_mod']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($arrayfields['t.status']['label'],$_SERVER['PHP_SELF'],'t.status','',$params,$arrayfields['t.status']['align'],$sortfield,$sortorder);

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

/*
// Fields title search
print '<tr class="liste_titre">';
//
if (! empty($arrayfields['t.ref']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.ref']['align'].'><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
if (! empty($arrayfields['t.fk_item']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_item']['align'].'><input type="text" class="flat" name="search_fk_item" value="'.$search_fk_item.'" size="10"></td>';
if (! empty($arrayfields['t.group_structure']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.group_structure']['align'].'><input type="text" class="flat" name="search_group_structure" value="'.$search_group_structure.'" size="10"></td>';
if (! empty($arrayfields['t.fk_product']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_product']['align'].'><input type="text" class="flat" name="search_fk_product" value="'.$search_fk_product.'" size="10"></td>';
if (! empty($arrayfields['t.label']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.label']['align'].'><input type="text" class="flat" name="search_label" value="'.$search_label.'" size="10"></td>';
if (! empty($arrayfields['t.formula']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.formula']['align'].'><input type="text" class="flat" name="search_formula" value="'.$search_formula.'" size="10"></td>';
if (! empty($arrayfields['t.units']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.units']['align'].'><input type="text" class="flat" name="search_units" value="'.$search_units.'" size="10"></td>';
if (! empty($arrayfields['t.commander']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.commander']['align'].'><input type="text" class="flat" name="search_commander" value="'.$search_commander.'" size="10"></td>';
if (! empty($arrayfields['r.performance']['checked'])) print '<td class="liste_titre" '.$arrayfields['r.performance']['align'].'><input type="text" class="flat" name="search_performance" value="'.$search_performance.'" size="10"></td>';
if (! empty($arrayfields['r.price_productive']['checked'])) print '<td class="liste_titre" '.$arrayfields['r.price_productive']['align'].'><input type="text" class="flat" name="search_price_productive" value="'.$search_price_productive.'" size="10"></td>';
if (! empty($arrayfields['r.price_improductive']['checked'])) print '<td class="liste_titre" '.$arrayfields['r.price_improductive']['align'].'><input type="text" class="flat" name="search_price_improductive" value="'.$search_price_improductive.'" size="10"></td>';
if (! empty($arrayfields['t.active']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.active']['align'].'><input type="text" class="flat" name="search_active" value="'.$search_active.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_create']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_user_create']['align'].'><input type="text" class="flat" name="search_fk_user_create" value="'.$search_fk_user_create.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_user_mod']['align'].'><input type="text" class="flat" name="search_fk_user_mod" value="'.$search_fk_user_mod.'" size="10"></td>';
if (! empty($arrayfields['t.status']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.status']['align'].'><input type="text" class="flat" name="search_status" value="'.$search_status.'" size="10"></td>';

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
// Action column
print '<td class="liste_titre" align="right">';
$searchpitco=$form->showFilterAndCheckAddButtons($massactionbutton?1:0, 'checkforselect', 1);
print $searchpitco;
print '</td>';
print '</tr>'."\n";
*/

//RECUPERAMOS LOS COLORES
$color_ma = $conf->global->ITEMS_COLOR_CATEGORY_MA;
$color_mo= $conf->global->ITEMS_COLOR_CATEGORY_MO;
$color_mq = $conf->global->ITEMS_COLOR_CATEGORY_MQ;


$i=0;
$var=true;
$totalarray=array();
$totalCost=0;
while ($i < min($num, $limit))
{
	$obj = $db->fetch_object($resql);
	if ($obj)
	{
		$var = !$var;
		// Show here line of result
		if($obj->group_structure == 'MA' && !empty($color_ma))
			print '<tr bgcolor="'.$color_ma.'">';
		elseif($obj->group_structure == 'MO' && !empty($color_mo))
			print '<tr bgcolor="'.$color_mo.'">';
		elseif($obj->group_structure == 'MQ' && !empty($color_mq))
			print '<tr bgcolor="'.$color_mq.'">';
		else
			print '<tr '.$bc[$var].'>';
		if ($action == 'editline' && $idr == $obj->rowid)
		{
			$objItemsregion->fetch(0,$object->id,$fk_region,$fk_sector);
			if($objItemsregion->hour_production>0)
			{
				if (isset($_POST['units']) && GETPOST('units')!=GETPOST('unitstmp'))
				{
					$obj->performance = GETPOST('units')/$objItemsregion->hour_production;
					$_POST['performance'] = GETPOST('units')/$objItemsregion->hour_production;
				}
				elseif(isset($_POST['performance']) && GETPOST('performance')!=GETPOST('performancetmp'))
				{
					$obj->units = ceil(GETPOST('performance')/$objItemsregion->hour_production);
					$_POST['units'] = ceil(GETPOST('performance')/$objItemsregion->hour_production);
				}
			}

			//vamos a establecer valor a dos variables
			if (!isset($_POST['units']))
			{
				$unitstmp= $obj->units;
				$performancetmp= $obj->performance;
			}
			else
			{
				$unitstmp = GETPOST('units');
				$performancetmp = GETPOST('performance');
			}
			print '<input type="hidden" name="unitstmp" value="'.$unitstmp.'">';
			print '<input type="hidden" name="performancetmp" value="'.$performancetmp.'">';
			// LIST_OF_TD_FIELDS_LIST
			foreach ($arrayfields as $key => $value)
			{
				if (!empty($arrayfields[$key]['checked']))
				{
					//$key2 = str_replace('t.', '', $key);
					$aKey = explode('.',$key);
					$key2 = $aKey[1];

					if ($key2 == 'ref')
					{
						$objItemsproduct->id = $obj->rowid;
						$objItemsproduct->fk_item = $obj->fk_item;
						$objItemsproduct->ref = $obj->ref;
						$objItemsproduct->label = $obj->label;
						$obj->$key2 = $objItemsproduct->getNomUrl();
					}
					if ($key2 == 'active')
					{
						$img = 'switch_off';
						if ($obj->$key2) $img = 'switch_on';
						$obj->$key2 = img_picto('',$img);
					}
					if ($key2 == 'status')
					{
						$objItemsproduct->status = $obj->$key2;
						$obj->$key2 = $objItemsproduct->getLibStatut(3);
					}
					if ($key2 == 'fk_user_create' || $key2 == 'fk_user_mod')
					{
						$res = $objUser->fetch($obj->$key2);
						if ($res == 1)
							$obj->$key2 = $objUser->getNomUrl(1);
					}
					if ($key2 == 'fk_origin')
					{
						if ($obj->group_structure== 'MO')
						{
							//vamos a buscar la categoria
							$aCat = $objCategorie->containing($obj->fk_product, 'product', $mode='id');
							if (is_array($aCat))
							{
								$aCategdef = array($conf->global->ITEMS_DEFAULT_CATEGORY_MA=>$conf->global->ITEMS_DEFAULT_CATEGORY_MA,$conf->global->ITEMS_DEFAULT_CATEGORY_MO=>$conf->global->ITEMS_DEFAULT_CATEGORY_MO,$conf->global->ITEMS_DEFAULT_CATEGORY_MQ=>$conf->global->ITEMS_DEFAULT_CATEGORY_MQ);
								$lNewdef= false;
								foreach ($aCat AS $j => $fk_categorie)
								{
									if (!$aCategdef[$fk_categorie]) $lNewdef=true;
								}
							}
							print '<td>';
							if ($lNewdef)
								print $langs->trans('Specialized');
							else
								print $langs->trans('Notspecialized');
							print '</td>';
						}
						else
						{
							print '<td>';
							print $form->select_country((GETPOST('fk_origin')?GETPOST('fk_origin'):($obj->$key2?$obj->$key2:$mysoc->country_id)),'fk_origin');
							print '</td>';
						}
					}
					elseif($key2!= 'fk_unit' && $key2!= 'units' && $key2!= 'commander' && $key2!= 'performance' && $key2!= 'price_productive' && $key2!= 'price_improductive' && $key2!='amount' && $key2!='percent_origin')
						print '<td>' . $obj->$key2 . '</td>';
					else
					{
						if (($key2=='commander' && $objItemsproduct->group_structure!='MQ') ||($key2=='units' && $object->manual_performance))
						{
							print '<td>' .  '</td>';
						}
						else
						{
							if ($key2=='fk_unit')
								print '<td>'.$form->selectUnits($obj->fk_unit,'fk_unit',1).'</td>';
							else
							{
								$cDecimal='0.';
								for ($a=1; $a<=$nDecimal;$a++)
								{
									if ($a==$nDecimal) $cDecimal.='1';
									else $cDecimal.='0';
								}
								print '<td>' . '<input class="len80" id="'.$key2.'" type="number" min="0" step="'.$cDecimal.'" name="'.$key2.'" value="'.price2num((GETPOST($key2)?GETPOST($key2):$obj->$key2),$nDecimal) . '"></td>';
							//print '<td>' . '<input id="'.$key2.'" type="text" name="'.$key2.'" value="'.(GETPOST($key2)?GETPOST($key2):$obj->$key2) . '" pattern="[0-9]" size="7"></td>';
							}
						}

					}
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
			$reshook=$hookmanager->executeHooks('printFieldListValue',$parameters);

		// Note that $action and $object may have been modified by hook
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

		// Action column
			print '<td class="nowrap" align="center">';
			print '<input type="submit" class="butAction" value="'.$langs->trans('Save').'">';
			print '</td>';
			if (! $i) $totalarray['nbfield']++;

			print '</tr>';
		}
		else
		{
			// LIST_OF_TD_FIELDS_LIST
			foreach ($arrayfields as $key => $value) {

				if (!empty($arrayfields[$key]['checked']))
				{
					$align='';
					if (!empty($arrayfields[$key]['align']))
						$align = $arrayfields[$key]['align'];
					//$key2 = str_replace('t.', '', $key);
					$aKey = explode('.',$key);
					$key2 = $aKey[1];
					if ($key2 == 'ref')
					{
						$objItemsproduct->id = $obj->rowid;
						$objItemsproduct->fk_item = $obj->fk_item;
						$objItemsproduct->fk_item = $id;
						$objItemsproduct->ref = $obj->ref;
						$objItemsproduct->label = $obj->label;
						$obj->$key2 = $objItemsproduct->getNomUrl();
					}
					if ($key2 == 'performance' ||$key2 == 'price_productive' || $key2 == 'price_improductive' || $key2=='amount_noprod'|| $key2=='amount')
					{
						$obj->$key2 = price2num($obj->$key2,$nDecimal);
					}
					if ($key2=='commander')
					{
						if ($obj->$key2) $obj->$key2=$langs->trans('Yes');
						else $obj->$key2 = '';
					}
					if ($key2=='fk_unit')
					{
						$objTmp = new Puvariablesline($db);
						$objTmp->fk_unit = $obj->$key2;
						$obj->$key2=$langs->trans($objTmp->getLabelOfUnit('short'));
					}
					if ($key2=='fk_origin')
					{
						if ($obj->group_structure== 'MO')
						{
							//vamos a buscar la categoria
							$aCat = $objCategorie->containing($obj->fk_product, 'product', $mode='id');
							if (is_array($aCat))
							{
								$aCategdef = array($conf->global->ITEMS_DEFAULT_CATEGORY_MA=>$conf->global->ITEMS_DEFAULT_CATEGORY_MA,$conf->global->ITEMS_DEFAULT_CATEGORY_MO=>$conf->global->ITEMS_DEFAULT_CATEGORY_MO,$conf->global->ITEMS_DEFAULT_CATEGORY_MQ=>$conf->global->ITEMS_DEFAULT_CATEGORY_MQ);
								$lNewdef= false;
								foreach ($aCat AS $j => $fk_categorie)
								{
									if (!$aCategdef[$fk_categorie]) $lNewdef=true;
								}
							}
							if ($lNewdef)
								$obj->$key2 =  $langs->trans('Specialized');
							else
								$obj->$key2 =  $langs->trans('Notspecialized');
						}
						else
						{
							if (empty($obj->$key2))
							{
								//vamos a actualizar el valor en este registro segun el producto
								$restmp = $objProduct->fetch($obj->fk_product);
								if ($restmp==1)
								{
									$obj->$key2 = $objProduct->country_id;
									$objTmp = new Itemsproductregion($db);
									$restmp = $objTmp->fetch($obj->rowid);
									if ($restmp == 1)
									{
										$objTmp->fk_origin = $objProduct->country_id;
										$restmp = $objTmp->update($user);
										if ($restmp<=0)
										{
											$error++;
											setEventMessages($objTmp->error,$objTmp->errors,'errors');
										}
									}
								}
							}
							if ($obj->$key2)
							{
								$tmparray=getCountry($obj->$key2,'all');
								$country_code=$tmparray['code'];
								$country=$tmparray['label'];


								$img=picto_from_langcode($country_code);
								$obj->$key2 =  $img?$img.' ':'';
								$obj->$key2 .= getCountry($country_code,1);
							}
						}
					}
					if ($key2 == 'fk_product')
					{
						if ($obj->$key2 >0)
						{
							$objProduct->fetch($obj->$key2);
							$obj->$key2 = $objProduct->getNomUrl();
						}
						else
							$obj->$key2='';
					}

					if ($key2 == 'active')
					{
						$img = 'switch_off';
						if ($obj->$key2) $img = 'switch_on';
						$obj->$key2 = img_picto('',$img);
					}
					if ($key2 == 'status')
					{
						$objItemsproduct->status = $obj->$key2;
						$obj->$key2 = $objItemsproduct->getLibStatut(3);
					}
					if ($key2 == 'fk_user_create' || $key2 == 'fk_user_mod')
					{
						$res = $objUser->fetch($obj->$key2);
						if ($res == 1)
							$obj->$key2 = $objUser->getNomUrl(1);
					}

					if ($key2=='cost_direct')
					{
						if ($obj->group_structure == 'MQ')
						{
							//echo '<hr>list '.$obj->performance.'*('.$obj->price_productive.'/100)*'.$obj->amount.'+'.$obj->performance.'*(1-('.$obj->price_productive.'/100))*'.$obj->amount_noprod;
							$obj->$key2 = price2num($obj->performance*($obj->price_productive/100)*$obj->amount+$obj->performance*(1-($obj->price_productive/100))*$obj->amount_noprod,$nDecimal);
						}
						else
						{
							$obj->$key2 = price2num($obj->performance*$obj->amount,$nDecimal);
						}
						$totalCost+= $obj->$key2;
					}

					print '<td '.$align.'>'.$obj->$key2 . '</td>';
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
			$reshook=$hookmanager->executeHooks('printFieldListValue',$parameters);
		// Note that $action and $object may have been modified by hook
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

		// Action column
			print '<td class="nowrap" align="center">';
			if ($user->rights->budget->ite->writepro)
			{
				$lView=true;
				if ($obj->group_structure == 'MQ')
				{
					if (!$object->manual_performance) $lView=false;
				}

				if ($lView && $object->status ==0)
				{
					print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$obj->rowid.'&action=editline'.'">'.img_picto('','edit').'</a>';
				}
			}
			print '</td>';
			if (! $i) $totalarray['nbfield']++;
		}
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

print '</form>'."\n";


print '<div class="tabsAction">';
print '<div class="inline-block divButAction"><a class="butAction" href="'.dol_buildpath('/budget/items/list.php',1).'">'.$langs->trans("Return").'</a></div>'."\n";

if ($user->rights->budget->ite->crear && $object->status == 0)
	print '<a class="butAction" href="' . dol_buildpath('/budget/items/supplies.php?id='.$id.'&action=create',1).'">' . $langs->trans('Create') . '</a>';
print '</div>';
