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
 *   	\file       budget/budgettaskproduct_list.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-04-20 11:31
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

$search_fk_budget_task=GETPOST('search_fk_budget_task','int');
$search_ref=GETPOST('search_ref','alpha');
$search_group_structure=GETPOST('search_group_structure','alpha');
$search_fk_product=GETPOST('search_fk_product','int');
$search_fk_product_budget=GETPOST('search_fk_product_budget','int');
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
$search_fk_object=GETPOST('search_fk_object','int');
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
if (! $sortfield) $sortfield="t.rowid"; // Set here default search field
if (! $sortorder) $sortorder="ASC";

// Protection if external user
$socid=0;
if ($user->societe_id > 0)
{
	$socid = $user->societe_id;
	//accessforbidden();
}

// Initialize technical object to manage context to save list fields
$contextpage=GETPOST('contextpage','aZ')?GETPOST('contextpage','aZ'):'budgettaskproductlist';

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

	't.fk_budget_task'=>array('label'=>$langs->trans("Fieldfk_budget_task"), 'align'=>'align="left"', 'checked'=>0),
	't.ref'=>array('label'=>$langs->trans("Fieldref"), 'align'=>'align="left"', 'checked'=>1),
	't.group_structure'=>array('label'=>$langs->trans("Fieldgroup_structure"), 'align'=>'align="left"', 'checked'=>1),
	't.fk_product'=>array('label'=>$langs->trans("Fieldfk_product"), 'align'=>'align="left"', 'checked'=>0),
	't.fk_product_budget'=>array('label'=>$langs->trans("Fieldfk_product_budget"), 'align'=>'align="left"', 'checked'=>1),
	't.label'=>array('label'=>$langs->trans("Fieldlabel"), 'align'=>'align="left"', 'checked'=>1),
	't.formula'=>array('label'=>$langs->trans("Fieldformula"), 'align'=>'align="left"', 'checked'=>1),
	't.units'=>array('label'=>$langs->trans("Fieldunits"), 'align'=>'align="left"', 'checked'=>0),
	't.commander'=>array('label'=>$langs->trans("Fieldcommander"), 'align'=>'align="left"', 'checked'=>1),
	't.performance'=>array('label'=>$langs->trans("Fieldperformance"), 'align'=>'align="left"', 'checked'=>0),
	't.price_productive'=>array('label'=>$langs->trans("Fieldprice_productive"), 'align'=>'align="left"', 'checked'=>0),
	't.price_improductive'=>array('label'=>$langs->trans("Fieldprice_improductive"), 'align'=>'align="left"', 'checked'=>0),
	't.active'=>array('label'=>$langs->trans("Fieldactive"), 'align'=>'align="left"', 'checked'=>1),
	't.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'align'=>'align="left"', 'checked'=>0),
	't.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'align'=>'align="left"', 'checked'=>0),
	't.fk_object'=>array('label'=>$langs->trans("Fieldfk_object"), 'align'=>'align="left"', 'checked'=>0),
	't.status'=>array('label'=>$langs->trans("Fieldstatus"), 'align'=>'align="left"', 'checked'=>1),


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
$object=new Budgettaskproduct($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}
$objUser = new User($db);



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

		$search_fk_budget_task='';
		$search_ref='';
		$search_group_structure='';
		$search_fk_product='';
		$search_fk_product_budget='';
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
		$search_fk_object='';
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

//$help_url="EN:Module_Customers_Orders|FR:Module_Commandes_Clients|ES:Módulo_Pedidos_de_clientes";
$help_url='';
$title = $langs->trans('Producttask');

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


$sql = "SELECT";
$sql.= " t.rowid,";

$sql .= " t.fk_budget_task,";
$sql .= " t.ref,";
$sql .= " t.group_structure,";
$sql .= " t.fk_product,";
$sql .= " t.fk_product_budget,";
$sql .= " t.label,";
$sql .= " t.formula,";
$sql .= " t.units,";
$sql .= " t.commander,";
$sql .= " t.performance,";
$sql .= " t.price_productive,";
$sql .= " t.price_improductive,";
$sql .= " t.active,";
$sql .= " t.fk_user_create,";
$sql .= " t.fk_user_mod,";
$sql .= " t.fk_object,";
$sql .= " t.datec,";
$sql .= " t.datem,";
$sql .= " t.tms,";
$sql .= " t.status";


// Add fields from extrafields
foreach ($extrafields->attribute_label as $key => $val) $sql.=($extrafields->attribute_type[$key] != 'separate' ? ",ef.".$key.' as options_'.$key : '');
// Add fields from hooks
	$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."budget_task_product as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."budget_task_product_extrafields as ef on (t.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
$sql.= " AND t.fk_budget_task = ".$idr;
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_fk_budget_task) $sql.= natural_search("fk_budget_task",$search_fk_budget_task);
if ($search_ref) $sql.= natural_search("ref",$search_ref);
if ($search_group_structure) $sql.= natural_search("group_structure",$search_group_structure);
if ($search_fk_product) $sql.= natural_search("fk_product",$search_fk_product);
if ($search_fk_product_budget) $sql.= natural_search("fk_product_budget",$search_fk_product_budget);
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
if ($search_fk_object) $sql.= natural_search("fk_object",$search_fk_object);
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

$sql.= $db->plimit($limit+1, $offset);

dol_syslog($script_file, LOG_DEBUG);
$resql=$db->query($sql);
if (! $resql)
{
	dol_print_error($db);
	exit;
}

	$head = budgettask_prepare_head($objectdet,$user,$action);
	dol_fiche_head($head, 'supplies', $langs->trans("Budgetprices"),0,'item');


$num = $db->num_rows($resql);

// Direct jump if only one record found
if ($num == 1 && ! empty($conf->global->MAIN_SEARCH_DIRECT_OPEN_IF_ONLY_ONE) && $search_all)
{
	$obj = $db->fetch_object($resql);
	$id = $obj->rowid;
	header("Location: ".DOL_URL_ROOT.'/budgettaskproduct/card.php?id='.$id);
	exit;
}

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
if (! empty($arrayfields['t.fk_budget_task']['checked'])) print_liste_field_titre($arrayfields['t.fk_budget_task']['label'],$_SERVER['PHP_SELF'],'t.fk_budget_task','',$params,$arrayfields['t.fk_budget_task']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.ref']['checked'])) print_liste_field_titre($arrayfields['t.ref']['label'],$_SERVER['PHP_SELF'],'t.ref','',$params,$arrayfields['t.ref']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.group_structure']['checked'])) print_liste_field_titre($arrayfields['t.group_structure']['label'],$_SERVER['PHP_SELF'],'t.group_structure','',$params,$arrayfields['t.group_structure']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_product']['checked'])) print_liste_field_titre($arrayfields['t.fk_product']['label'],$_SERVER['PHP_SELF'],'t.fk_product','',$params,$arrayfields['t.fk_product']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_product_budget']['checked'])) print_liste_field_titre($arrayfields['t.fk_product_budget']['label'],$_SERVER['PHP_SELF'],'t.fk_product_budget','',$params,$arrayfields['t.fk_product_budget']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.label']['checked'])) print_liste_field_titre($arrayfields['t.label']['label'],$_SERVER['PHP_SELF'],'t.label','',$params,$arrayfields['t.label']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.formula']['checked'])) print_liste_field_titre($arrayfields['t.formula']['label'],$_SERVER['PHP_SELF'],'t.formula','',$params,$arrayfields['t.formula']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.units']['checked'])) print_liste_field_titre($arrayfields['t.units']['label'],$_SERVER['PHP_SELF'],'t.units','',$params,$arrayfields['t.units']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.commander']['checked'])) print_liste_field_titre($arrayfields['t.commander']['label'],$_SERVER['PHP_SELF'],'t.commander','',$params,$arrayfields['t.commander']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.performance']['checked'])) print_liste_field_titre($arrayfields['t.performance']['label'],$_SERVER['PHP_SELF'],'t.performance','',$params,$arrayfields['t.performance']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.price_productive']['checked'])) print_liste_field_titre($arrayfields['t.price_productive']['label'],$_SERVER['PHP_SELF'],'t.price_productive','',$params,$arrayfields['t.price_productive']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.price_improductive']['checked'])) print_liste_field_titre($arrayfields['t.price_improductive']['label'],$_SERVER['PHP_SELF'],'t.price_improductive','',$params,$arrayfields['t.price_improductive']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.active']['checked'])) print_liste_field_titre($arrayfields['t.active']['label'],$_SERVER['PHP_SELF'],'t.active','',$params,$arrayfields['t.active']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_create']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_create']['label'],$_SERVER['PHP_SELF'],'t.fk_user_create','',$params,$arrayfields['t.fk_user_create']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_mod']['label'],$_SERVER['PHP_SELF'],'t.fk_user_mod','',$params,$arrayfields['t.fk_user_mod']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_object']['checked'])) print_liste_field_titre($arrayfields['t.fk_object']['label'],$_SERVER['PHP_SELF'],'t.fk_object','',$params,$arrayfields['t.fk_object']['align'],$sortfield,$sortorder);
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

// Fields title search
print '<tr class="liste_titre">';
//
if (! empty($arrayfields['t.fk_budget_task']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_budget_task']['align'].'><input type="text" class="flat" name="search_fk_budget_task" value="'.$search_fk_budget_task.'" size="10"></td>';
if (! empty($arrayfields['t.ref']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.ref']['align'].'><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
if (! empty($arrayfields['t.group_structure']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.group_structure']['align'].'><input type="text" class="flat" name="search_group_structure" value="'.$search_group_structure.'" size="10"></td>';
if (! empty($arrayfields['t.fk_product']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_product']['align'].'><input type="text" class="flat" name="search_fk_product" value="'.$search_fk_product.'" size="10"></td>';
if (! empty($arrayfields['t.fk_product_budget']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_product_budget']['align'].'><input type="text" class="flat" name="search_fk_product_budget" value="'.$search_fk_product_budget.'" size="10"></td>';
if (! empty($arrayfields['t.label']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.label']['align'].'><input type="text" class="flat" name="search_label" value="'.$search_label.'" size="10"></td>';
if (! empty($arrayfields['t.formula']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.formula']['align'].'><input type="text" class="flat" name="search_formula" value="'.$search_formula.'" size="10"></td>';
if (! empty($arrayfields['t.units']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.units']['align'].'><input type="text" class="flat" name="search_units" value="'.$search_units.'" size="10"></td>';
if (! empty($arrayfields['t.commander']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.commander']['align'].'><input type="text" class="flat" name="search_commander" value="'.$search_commander.'" size="10"></td>';
if (! empty($arrayfields['t.performance']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.performance']['align'].'><input type="text" class="flat" name="search_performance" value="'.$search_performance.'" size="10"></td>';
if (! empty($arrayfields['t.price_productive']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.price_productive']['align'].'><input type="text" class="flat" name="search_price_productive" value="'.$search_price_productive.'" size="10"></td>';
if (! empty($arrayfields['t.price_improductive']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.price_improductive']['align'].'><input type="text" class="flat" name="search_price_improductive" value="'.$search_price_improductive.'" size="10"></td>';
if (! empty($arrayfields['t.active']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.active']['align'].'><input type="text" class="flat" name="search_active" value="'.$search_active.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_create']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_user_create']['align'].'><input type="text" class="flat" name="search_fk_user_create" value="'.$search_fk_user_create.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_user_mod']['align'].'><input type="text" class="flat" name="search_fk_user_mod" value="'.$search_fk_user_mod.'" size="10"></td>';
if (! empty($arrayfields['t.fk_object']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_object']['align'].'><input type="text" class="flat" name="search_fk_object" value="'.$search_fk_object.'" size="10"></td>';
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

		// Show here line of result
		print '<tr '.$bc[$var].'>';
		// LIST_OF_TD_FIELDS_LIST
		foreach ($arrayfields as $key => $value) {
			if (!empty($arrayfields[$key]['checked'])) {
				//$key2 = str_replace('t.', '', $key);
				$aKey = explode('.',$key);
				$key2 = $aKey[1];
				if ($key2 == 'ref')
				{
					$objBudgettaskproduct->id = $obj->rowid;
					$objBudgettaskproduct->fk_budget_task = $obj->fk_budget_task;
					$objBudgettaskproduct->ref = $obj->ref;
					$objBudgettaskproduct->label = $obj->label;
					$obj->$key2 = $objBudgettaskproduct->getNomUrladd();
				}
				if ($key2 == 'fk_product')
				{
					if ($obj->$key2>0)
					{
						$objProduct->fetch($obj->$key2);
						$obj->$key2 = $objProduct->getNomUrl(1);
					}
				}
				if ($key2 == 'fk_product_budget')
				{
					if ($obj->$key2 > 0)
					{
						$resdb=$objprodb->fetch($obj->$key2);
						if ($resdb==1)
							$obj->$key2 = $objprodb->getNomUrl(1);
					}
				}
				if ($key2 == 'active')
				{
					$img = 'switch_off';
					if ($obj->$key2) $img = 'switch_on';
					$obj->$key2 = img_picto('',$img);
				}
				if ($key2=='commander')
				{
					$obj->$key2 = ($obj->$key2?$langs->trans('Yes'):$langs->trans('Not'));
				}
				if ($key2 == 'status')
				{
					$object->status = $obj->$key2;
					$obj->$key2 = $object->getLibStatut(3);
				}
				if ($key2 == 'fk_user_create' || $key2 == 'fk_user_mod')
				{
					$res = $objUser->fetch($obj->$key2);
					if ($res == 1)
						$obj->$key2 = $objUser->getNomUrl(1);
				}

				print '<td>' . $obj->$key2 . '</td>';
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

		print '</td>';
		if (! $i) $totalarray['nbfield']++;

		print '</tr>';
	}
	$i++;
}

$db->free($resql);

$parameters=array('arrayfields'=>$arrayfields, 'sql'=>$sql);
$reshook=$hookmanager->executeHooks('printFieldListFooter',$parameters);    // Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;

print '</table>'."\n";
print '</div>'."\n";

print '</form>'."\n";
