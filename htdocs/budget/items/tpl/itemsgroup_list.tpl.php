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
 *   	\file       budget/itemsgroup_list.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-05-29 08:14
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


$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_ref_ext=GETPOST('search_ref_ext','alpha');
$search_version=GETPOST('search_version','int');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_fk_parent=GETPOST('search_fk_parent','int');
$search_type=GETPOST('search_type','int');
$search_detail=GETPOST('search_detail','alpha');
$search_fk_unit=GETPOST('search_fk_unit','int');
$search_quant=GETPOST('search_quant','alpha');
$search_amount=GETPOST('search_amount','alpha');
$search_manual_performance=GETPOST('search_manual_performance','int');
$search_fk_item=GETPOST('search_fk_item','int');
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
$contextpage=GETPOST('contextpage','aZ')?GETPOST('contextpage','aZ'):'budgetitemsgrouplisttpllist';

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

	//'t.entity'=>array('label'=>$langs->trans("Fieldentity"), 'align'=>'align="left"', 'checked'=>0),
	't.ref'=>array('label'=>$langs->trans("Fieldref"), 'align'=>'align="left"', 'checked'=>1),
	//'t.ref_ext'=>array('label'=>$langs->trans("Fieldref_ext"), 'align'=>'align="left"', 'checked'=>1),
	//'t.version'=>array('label'=>$langs->trans("Fieldversion"), 'align'=>'align="left"', 'checked'=>0),
	//'t.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'align'=>'align="left"', 'checked'=>0),
	//'t.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'align'=>'align="left"', 'checked'=>0),
	//'t.fk_parent'=>array('label'=>$langs->trans("Fieldfk_parent"), 'align'=>'align="left"', 'checked'=>1),
	//'t.type'=>array('label'=>$langs->trans("Fieldtype"), 'align'=>'align="left"', 'checked'=>1),
	't.detail'=>array('label'=>$langs->trans("Fielddetail"), 'align'=>'align="left"', 'checked'=>1),
	//'t.fk_unit'=>array('label'=>$langs->trans("Fieldfk_unit"), 'align'=>'align="left"', 'checked'=>0),
	//'t.quant'=>array('label'=>$langs->trans("Fieldquant"), 'align'=>'align="left"', 'checked'=>0),
	//'t.amount'=>array('label'=>$langs->trans("Fieldamount"), 'align'=>'align="left"', 'checked'=>0),
	't.manual_performance'=>array('label'=>$langs->trans("Fieldmanual_performance"), 'align'=>'align="left"', 'checked'=>1),
	//'t.fk_item'=>array('label'=>$langs->trans("Fieldfk_item"), 'align'=>'align="left"', 'checked'=>0),
	//'t.status'=>array('label'=>$langs->trans("Fieldstatus"), 'align'=>'align="left"', 'checked'=>0),


	//'t.entity'=>array('label'=>$langs->trans("Entity"), 'checked'=>1, 'enabled'=>(! empty($conf->multicompany->enabled) && empty($conf->multicompany->transverse_mode))),
	//'t.datec'=>array('label'=>$langs->trans("DateCreationShort"), 'align'=>'align="left"', 'checked'=>0, 'position'=>500),
	//'t.tms'=>array('label'=>$langs->trans("DateModificationShort"), 'align'=>'align="left"', 'checked'=>0, 'position'=>500),
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
$object=new Itemsgroup($db);
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

		$search_entity='';
		$search_ref='';
		$search_ref_ext='';
		$search_version='';
		$search_fk_user_create='';
		$search_fk_user_mod='';
		$search_fk_parent='';
		$search_type='';
		$search_detail='';
		$search_fk_unit='';
		$search_quant='';
		$search_amount='';
		$search_manual_performance='';
		$search_fk_item='';
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
$title = $langs->trans('Relateditems');

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

		$sql .= " t.entity,";
		$sql .= " t.ref,";
		$sql .= " t.ref_ext,";
		$sql .= " t.version,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.fk_parent,";
		$sql .= " t.type,";
		$sql .= " t.detail,";
		$sql .= " t.fk_unit,";
		$sql .= " t.quant,";
		$sql .= " t.amount,";
		$sql .= " t.manual_performance,";
		$sql .= " t.fk_item,";
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
$sql.= " FROM ".MAIN_DB_PREFIX."items_group as t";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."items as i ON t.fk_item = i.rowid";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."items_product as ip ON ip.fk_item = i.rowid";

if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."items_group_extrafields as ef on (t.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
$sql.= " AND ip.ref = '".$objItemsproduct->ref."'";
$sql.= " AND t.rowid NOT IN (".$id.")";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_entity) $sql.= natural_search("entity",$search_entity);
if ($search_ref) $sql.= natural_search("ref",$search_ref);
if ($search_ref_ext) $sql.= natural_search("ref_ext",$search_ref_ext);
if ($search_version) $sql.= natural_search("version",$search_version);
if ($search_fk_user_create) $sql.= natural_search("fk_user_create",$search_fk_user_create);
if ($search_fk_user_mod) $sql.= natural_search("fk_user_mod",$search_fk_user_mod);
if ($search_fk_parent) $sql.= natural_search("fk_parent",$search_fk_parent);
if ($search_type) $sql.= natural_search("type",$search_type);
if ($search_detail) $sql.= natural_search("detail",$search_detail);
if ($search_fk_unit) $sql.= natural_search("fk_unit",$search_fk_unit);
if ($search_quant) $sql.= natural_search("quant",$search_quant);
if ($search_amount) $sql.= natural_search("amount",$search_amount);
if ($search_manual_performance) $sql.= natural_search("manual_performance",$search_manual_performance);
if ($search_fk_item) $sql.= natural_search("fk_item",$search_fk_item);
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

$num = $db->num_rows($resql);

// Direct jump if only one record found
if ($num == 1 && ! empty($conf->global->MAIN_SEARCH_DIRECT_OPEN_IF_ONLY_ONE) && $search_all)
{
	$obj = $db->fetch_object($resql);
	$id = $obj->rowid;
	header("Location: ".DOL_URL_ROOT.'/itemsgroup/card.php?id='.$id);
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

print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, '', $num, $nbtotalofrecords, '', 0, '', '');

if ($sall)
{
	foreach($fieldstosearchall as $key => $val) $fieldstosearchall[$key]=$langs->trans($val);
	print $langs->trans("FilterOnInto", $sall) . join(', ',$fieldstosearchall);
}

$moreforfilter = '';

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
if (! empty($arrayfields['t.entity']['checked'])) print_liste_field_titre($arrayfields['t.entity']['label'],$_SERVER['PHP_SELF'],'t.entity','',$params,$arrayfields['t.entity']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.ref']['checked'])) print_liste_field_titre($arrayfields['t.ref']['label'],$_SERVER['PHP_SELF'],'t.ref','',$params,$arrayfields['t.ref']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.ref_ext']['checked'])) print_liste_field_titre($arrayfields['t.ref_ext']['label'],$_SERVER['PHP_SELF'],'t.ref_ext','',$params,$arrayfields['t.ref_ext']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.version']['checked'])) print_liste_field_titre($arrayfields['t.version']['label'],$_SERVER['PHP_SELF'],'t.version','',$params,$arrayfields['t.version']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_create']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_create']['label'],$_SERVER['PHP_SELF'],'t.fk_user_create','',$params,$arrayfields['t.fk_user_create']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_mod']['label'],$_SERVER['PHP_SELF'],'t.fk_user_mod','',$params,$arrayfields['t.fk_user_mod']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_parent']['checked'])) print_liste_field_titre($arrayfields['t.fk_parent']['label'],$_SERVER['PHP_SELF'],'t.fk_parent','',$params,$arrayfields['t.fk_parent']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.type']['checked'])) print_liste_field_titre($arrayfields['t.type']['label'],$_SERVER['PHP_SELF'],'t.type','',$params,$arrayfields['t.type']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.detail']['checked'])) print_liste_field_titre($arrayfields['t.detail']['label'],$_SERVER['PHP_SELF'],'t.detail','',$params,$arrayfields['t.detail']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_unit']['checked'])) print_liste_field_titre($arrayfields['t.fk_unit']['label'],$_SERVER['PHP_SELF'],'t.fk_unit','',$params,$arrayfields['t.fk_unit']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.quant']['checked'])) print_liste_field_titre($arrayfields['t.quant']['label'],$_SERVER['PHP_SELF'],'t.quant','',$params,$arrayfields['t.quant']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.amount']['checked'])) print_liste_field_titre($arrayfields['t.amount']['label'],$_SERVER['PHP_SELF'],'t.amount','',$params,$arrayfields['t.amount']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.manual_performance']['checked'])) print_liste_field_titre($arrayfields['t.manual_performance']['label'],$_SERVER['PHP_SELF'],'t.manual_performance','',$params,$arrayfields['t.manual_performance']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_item']['checked'])) print_liste_field_titre($arrayfields['t.fk_item']['label'],$_SERVER['PHP_SELF'],'t.fk_item','',$params,$arrayfields['t.fk_item']['align'],$sortfield,$sortorder);
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

if ($abc)
{
// Fields title search
	print '<tr class="liste_titre">';
//
	if (! empty($arrayfields['t.entity']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.entity']['align'].'><input type="text" class="flat" name="search_entity" value="'.$search_entity.'" size="10"></td>';
	if (! empty($arrayfields['t.ref']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.ref']['align'].'><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
	if (! empty($arrayfields['t.ref_ext']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.ref_ext']['align'].'><input type="text" class="flat" name="search_ref_ext" value="'.$search_ref_ext.'" size="10"></td>';
	if (! empty($arrayfields['t.version']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.version']['align'].'><input type="text" class="flat" name="search_version" value="'.$search_version.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_user_create']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_user_create']['align'].'><input type="text" class="flat" name="search_fk_user_create" value="'.$search_fk_user_create.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_user_mod']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_user_mod']['align'].'><input type="text" class="flat" name="search_fk_user_mod" value="'.$search_fk_user_mod.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_parent']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_parent']['align'].'><input type="text" class="flat" name="search_fk_parent" value="'.$search_fk_parent.'" size="10"></td>';
	if (! empty($arrayfields['t.type']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.type']['align'].'><input type="text" class="flat" name="search_type" value="'.$search_type.'" size="10"></td>';
	if (! empty($arrayfields['t.detail']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.detail']['align'].'><input type="text" class="flat" name="search_detail" value="'.$search_detail.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_unit']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_unit']['align'].'><input type="text" class="flat" name="search_fk_unit" value="'.$search_fk_unit.'" size="10"></td>';
	if (! empty($arrayfields['t.quant']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.quant']['align'].'><input type="text" class="flat" name="search_quant" value="'.$search_quant.'" size="10"></td>';
	if (! empty($arrayfields['t.amount']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.amount']['align'].'><input type="text" class="flat" name="search_amount" value="'.$search_amount.'" size="10"></td>';
	if (! empty($arrayfields['t.manual_performance']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.manual_performance']['align'].'><input type="text" class="flat" name="search_manual_performance" value="'.$search_manual_performance.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_item']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_item']['align'].'><input type="text" class="flat" name="search_fk_item" value="'.$search_fk_item.'" size="10"></td>';
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
$searchpitco=$form->showFilterAndCheckAddButtons($massactionbutton?0:0, 'checkforselect', 1);
print $searchpitco;
print '</td>';
print '</tr>'."\n";
}

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
					$object->id = $obj->rowid;
					$object->ref = $obj->ref;
					$object->label = $obj->label;
					$obj->$key2 = $object->getNomUrl();
				}
				if ($key2 == 'active')
				{
					$img = 'switch_off';
					if ($obj->$key2) $img = 'switch_on';
					$obj->$key2 = img_picto('',$img);
				}
				if ($key2 == 'manual_performance')
				{
					$img = $langs->trans('Not');
					if ($obj->$key2) $img = $langs->trans('Yes');
					$obj->$key2 = $img;
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

