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
 *   	\file       budget/budgetincidents_list.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-05-23 09:41
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

$search_entity=GETPOST('search_entity','int');
$search_fk_budget=GETPOST('search_fk_budget','int');
$search_ref=GETPOST('search_ref','alpha');
$search_label=GETPOST('search_label','alpha');
$search_code_parameter=GETPOST('search_code_parameter','alpha');
$search_fk_region=GETPOST('search_fk_region','int');
$search_day_year=GETPOST('search_day_year','int');
$search_day_efective=GETPOST('search_day_efective','int');
$search_day_journal=GETPOST('search_day_journal','int');
$search_day_num=GETPOST('search_day_num','int');
$search_salary_min=GETPOST('search_salary_min','alpha');
$search_njobs=GETPOST('search_njobs','int');
$search_cost_direct=GETPOST('search_cost_direct','alpha');
$search_time_duration=GETPOST('search_time_duration','int');
$search_exchange_rate=GETPOST('search_exchange_rate','alpha');
$search_ponderation=GETPOST('search_ponderation','alpha');
$search_commission=GETPOST('search_commission','alpha');
$search_incident=GETPOST('search_incident','alpha');
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
$contextpage=GETPOST('contextpage','aZ')?GETPOST('contextpage','aZ'):'budgetbudgetincidentstpllist';

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

't.entity'=>array('label'=>$langs->trans("Fieldentity"), 'align'=>'align="left"', 'checked'=>0),
't.fk_budget'=>array('label'=>$langs->trans("Fieldfk_budget"), 'align'=>'align="left"', 'checked'=>0),
't.ref'=>array('label'=>$langs->trans("Fieldref"), 'align'=>'align="left"', 'checked'=>1),
't.label'=>array('label'=>$langs->trans("Fieldlabel"), 'align'=>'align="left"', 'checked'=>1),
//'t.code_parameter'=>array('label'=>$langs->trans("Fieldcode_parameter"), 'align'=>'align="left"', 'checked'=>1),
//'t.fk_region'=>array('label'=>$langs->trans("Fieldfk_region"), 'align'=>'align="left"', 'checked'=>0),
't.day_year'=>array('label'=>$langs->trans("Fieldday_year"), 'align'=>'align="left"', 'checked'=>1),
't.day_efective'=>array('label'=>$langs->trans("Fieldday_efective"), 'align'=>'align="left"', 'checked'=>1),
't.day_journal'=>array('label'=>$langs->trans("Fieldday_journal"), 'align'=>'align="left"', 'checked'=>1),
't.day_num'=>array('label'=>$langs->trans("Fieldday_num"), 'align'=>'align="left"', 'checked'=>1),
't.salary_min'=>array('label'=>$langs->trans("Fieldsalary_min"), 'align'=>'align="left"', 'checked'=>1),
't.njobs'=>array('label'=>$langs->trans("Fieldnjobs"), 'align'=>'align="left"', 'checked'=>1),
't.cost_direct'=>array('label'=>$langs->trans("Fieldcost_direct"), 'align'=>'align="right"', 'checked'=>1),
't.time_duration'=>array('label'=>$langs->trans("Fieldtime_duration"), 'align'=>'align="center"', 'checked'=>1),
't.exchange_rate'=>array('label'=>$langs->trans("Fieldexchange_rate"), 'align'=>'align="right"', 'checked'=>1),
't.ponderation'=>array('label'=>$langs->trans("Fieldponderation"), 'align'=>'align="right"', 'checked'=>1),
't.commission'=>array('label'=>$langs->trans("Fieldcommission"), 'align'=>'align="right"', 'checked'=>1),
't.incident'=>array('label'=>$langs->trans("Fieldincident"), 'align'=>'align="right"', 'checked'=>1),
't.active'=>array('label'=>$langs->trans("Fieldactive"), 'align'=>'align="left"', 'checked'=>1),
't.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'align'=>'align="left"', 'checked'=>0),
't.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'align'=>'align="left"', 'checked'=>0),
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
$search_fk_budget='';
$search_ref='';
$search_label='';
$search_code_parameter='';
$search_fk_region='';
$search_day_year='';
$search_day_efective='';
$search_day_journal='';
$search_day_num='';
$search_salary_min='';
$search_njobs='';
$search_cost_direct='';
$search_time_duration='';
$search_exchange_rate='';
$search_ponderation='';
$search_commission='';
$search_incident='';
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
$title = $langs->trans('Budgetincidents');

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
		$sql .= " t.fk_budget,";
		$sql .= " t.ref,";
		$sql .= " t.label,";
		$sql .= " t.code_parameter,";
		$sql .= " t.fk_region,";
		$sql .= " t.day_year,";
		$sql .= " t.day_efective,";
		$sql .= " t.day_journal,";
		$sql .= " t.day_num,";
		$sql .= " t.salary_min,";
		$sql .= " t.njobs,";
		$sql .= " t.cost_direct,";
		$sql .= " t.time_duration,";
		$sql .= " t.exchange_rate,";
		$sql .= " t.ponderation,";
		$sql .= " t.commission,";
		$sql .= " t.incident,";
		$sql .= " t.active,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
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
$sql.= " FROM ".MAIN_DB_PREFIX."budget_incidents as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."budget_incidents_extrafields as ef on (t.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
$sql.= " AND t.fk_budget = ".$object->id;
$sql.= " AND t.fk_region = ".$object->fk_region;
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_entity) $sql.= natural_search("entity",$search_entity);
if ($search_fk_budget) $sql.= natural_search("fk_budget",$search_fk_budget);
if ($search_ref) $sql.= natural_search("ref",$search_ref);
if ($search_label) $sql.= natural_search("label",$search_label);
if ($search_code_parameter) $sql.= natural_search("code_parameter",$search_code_parameter);
if ($search_fk_region) $sql.= natural_search("fk_region",$search_fk_region);
if ($search_day_year) $sql.= natural_search("day_year",$search_day_year);
if ($search_day_efective) $sql.= natural_search("day_efective",$search_day_efective);
if ($search_day_journal) $sql.= natural_search("day_journal",$search_day_journal);
if ($search_day_num) $sql.= natural_search("day_num",$search_day_num);
if ($search_salary_min) $sql.= natural_search("salary_min",$search_salary_min);
if ($search_njobs) $sql.= natural_search("njobs",$search_njobs);
if ($search_cost_direct) $sql.= natural_search("cost_direct",$search_cost_direct);
if ($search_time_duration) $sql.= natural_search("time_duration",$search_time_duration);
if ($search_exchange_rate) $sql.= natural_search("exchange_rate",$search_exchange_rate);
if ($search_ponderation) $sql.= natural_search("ponderation",$search_ponderation);
if ($search_commission) $sql.= natural_search("commission",$search_commission);
if ($search_incident) $sql.= natural_search("incident",$search_incident);
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
	header("Location: ".DOL_URL_ROOT.'/budgetincidents/card.php?id='.$id);
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
if (! empty($arrayfields['t.entity']['checked'])) print_liste_field_titre($arrayfields['t.entity']['label'],$_SERVER['PHP_SELF'],'t.entity','',$params,$arrayfields['t.entity']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_budget']['checked'])) print_liste_field_titre($arrayfields['t.fk_budget']['label'],$_SERVER['PHP_SELF'],'t.fk_budget','',$params,$arrayfields['t.fk_budget']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.ref']['checked'])) print_liste_field_titre($arrayfields['t.ref']['label'],$_SERVER['PHP_SELF'],'t.ref','',$params,$arrayfields['t.ref']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.label']['checked'])) print_liste_field_titre($arrayfields['t.label']['label'],$_SERVER['PHP_SELF'],'t.label','',$params,$arrayfields['t.label']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.code_parameter']['checked'])) print_liste_field_titre($arrayfields['t.code_parameter']['label'],$_SERVER['PHP_SELF'],'t.code_parameter','',$params,$arrayfields['t.code_parameter']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_region']['checked'])) print_liste_field_titre($arrayfields['t.fk_region']['label'],$_SERVER['PHP_SELF'],'t.fk_region','',$params,$arrayfields['t.fk_region']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.day_year']['checked'])) print_liste_field_titre($arrayfields['t.day_year']['label'],$_SERVER['PHP_SELF'],'t.day_year','',$params,$arrayfields['t.day_year']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.day_efective']['checked'])) print_liste_field_titre($arrayfields['t.day_efective']['label'],$_SERVER['PHP_SELF'],'t.day_efective','',$params,$arrayfields['t.day_efective']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.day_journal']['checked'])) print_liste_field_titre($arrayfields['t.day_journal']['label'],$_SERVER['PHP_SELF'],'t.day_journal','',$params,$arrayfields['t.day_journal']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.day_num']['checked'])) print_liste_field_titre($arrayfields['t.day_num']['label'],$_SERVER['PHP_SELF'],'t.day_num','',$params,$arrayfields['t.day_num']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.salary_min']['checked'])) print_liste_field_titre($arrayfields['t.salary_min']['label'],$_SERVER['PHP_SELF'],'t.salary_min','',$params,$arrayfields['t.salary_min']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.njobs']['checked'])) print_liste_field_titre($arrayfields['t.njobs']['label'],$_SERVER['PHP_SELF'],'t.njobs','',$params,$arrayfields['t.njobs']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.cost_direct']['checked'])) print_liste_field_titre($arrayfields['t.cost_direct']['label'],$_SERVER['PHP_SELF'],'t.cost_direct','',$params,$arrayfields['t.cost_direct']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.time_duration']['checked'])) print_liste_field_titre($arrayfields['t.time_duration']['label'],$_SERVER['PHP_SELF'],'t.time_duration','',$params,$arrayfields['t.time_duration']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.exchange_rate']['checked'])) print_liste_field_titre($arrayfields['t.exchange_rate']['label'],$_SERVER['PHP_SELF'],'t.exchange_rate','',$params,$arrayfields['t.exchange_rate']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.ponderation']['checked'])) print_liste_field_titre($arrayfields['t.ponderation']['label'],$_SERVER['PHP_SELF'],'t.ponderation','',$params,$arrayfields['t.ponderation']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.commission']['checked'])) print_liste_field_titre($arrayfields['t.commission']['label'],$_SERVER['PHP_SELF'],'t.commission','',$params,$arrayfields['t.commission']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.incident']['checked'])) print_liste_field_titre($arrayfields['t.incident']['label'],$_SERVER['PHP_SELF'],'t.incident','',$params,$arrayfields['t.incident']['align'],$sortfield,$sortorder);
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

// Fields title search
print '<tr class="liste_titre">';
//
if (! empty($arrayfields['t.entity']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.entity']['align'].'><input type="text" class="flat" name="search_entity" value="'.$search_entity.'" size="10"></td>';
if (! empty($arrayfields['t.fk_budget']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_budget']['align'].'><input type="text" class="flat" name="search_fk_budget" value="'.$search_fk_budget.'" size="10"></td>';
if (! empty($arrayfields['t.ref']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.ref']['align'].'><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
if (! empty($arrayfields['t.label']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.label']['align'].'><input type="text" class="flat" name="search_label" value="'.$search_label.'" size="10"></td>';
if (! empty($arrayfields['t.code_parameter']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.code_parameter']['align'].'><input type="text" class="flat" name="search_code_parameter" value="'.$search_code_parameter.'" size="10"></td>';
if (! empty($arrayfields['t.fk_region']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_region']['align'].'><input type="text" class="flat" name="search_fk_region" value="'.$search_fk_region.'" size="10"></td>';
if (! empty($arrayfields['t.day_year']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.day_year']['align'].'><input type="text" class="flat" name="search_day_year" value="'.$search_day_year.'" size="10"></td>';
if (! empty($arrayfields['t.day_efective']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.day_efective']['align'].'><input type="text" class="flat" name="search_day_efective" value="'.$search_day_efective.'" size="10"></td>';
if (! empty($arrayfields['t.day_journal']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.day_journal']['align'].'><input type="text" class="flat" name="search_day_journal" value="'.$search_day_journal.'" size="10"></td>';
if (! empty($arrayfields['t.day_num']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.day_num']['align'].'><input type="text" class="flat" name="search_day_num" value="'.$search_day_num.'" size="10"></td>';
if (! empty($arrayfields['t.salary_min']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.salary_min']['align'].'><input type="text" class="flat" name="search_salary_min" value="'.$search_salary_min.'" size="10"></td>';
if (! empty($arrayfields['t.njobs']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.njobs']['align'].'><input type="text" class="flat" name="search_njobs" value="'.$search_njobs.'" size="10"></td>';
if (! empty($arrayfields['t.cost_direct']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.cost_direct']['align'].'><input type="text" class="flat" name="search_cost_direct" value="'.$search_cost_direct.'" size="10"></td>';
if (! empty($arrayfields['t.time_duration']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.time_duration']['align'].'><input type="text" class="flat" name="search_time_duration" value="'.$search_time_duration.'" size="10"></td>';
if (! empty($arrayfields['t.exchange_rate']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.exchange_rate']['align'].'><input type="text" class="flat" name="search_exchange_rate" value="'.$search_exchange_rate.'" size="10"></td>';
if (! empty($arrayfields['t.ponderation']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.ponderation']['align'].'><input type="text" class="flat" name="search_ponderation" value="'.$search_ponderation.'" size="10"></td>';
if (! empty($arrayfields['t.commission']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.commission']['align'].'><input type="text" class="flat" name="search_commission" value="'.$search_commission.'" size="10"></td>';
if (! empty($arrayfields['t.incident']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.incident']['align'].'><input type="text" class="flat" name="search_incident" value="'.$search_incident.'" size="10"></td>';
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
			$lNumber=false;
			$lNumberthree=false;
			$lNumberfour=false;
			if (!empty($arrayfields[$key]['checked'])) {
				//$key2 = str_replace('t.', '', $key);
				$aKey = explode('.',$key);
				$key2 = $aKey[1];
				if ($key2 == 'ref')
				{
					$objBudgetincidents->id = $obj->rowid;
					$objBudgetincidents->fk_budget = $obj->fk_budget;
					$objBudgetincidents->ref = $obj->ref;
					$objBudgetincidents->label = $obj->label;
					$obj->$key2 = $objBudgetincidents->getNomUrltpl();
				}
				if ($key2 == 'active')
				{
					$img = 'switch_off';
					if ($obj->$key2) $img = 'switch_on';
					$obj->$key2 = img_picto('',$img);
				}
				if ($key2 == 'salary_min') $lNumber=true;
				if ($key2 == 'cost_direct') $lNumber=true;
				if ($key2 == 'incidents') $lNumber=true;
				if ($key2 == 'exchange_rate') $lNumber=true;
				if ($key2 == 'ponderation') $lNumberthree=true;
				if ($key2 == 'commission') $lNumberfour=true;
				if ($key2 == 'status')
				{
					$objBudgetincidents->status = $obj->$key2;
					$obj->$key2 = $objBudgetincidents->getLibStatut(3);
				}
				if ($key2 == 'fk_user_create' || $key2 == 'fk_user_mod')
				{
					$res = $objUser->fetch($obj->$key2);
					if ($res == 1)
						$obj->$key2 = $objUser->getNomUrl(1);
				}
				if($lNumber) $obj->$key2 = price($obj->$key2,$general->decimal_total);
				if($lNumberthree) $obj->$key2 = price($obj->$key2,3);
				if($lNumberfour) $obj->$key2 = price($obj->$key2,4);
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
		if ($massactionbutton || $massaction)   // If we are in select mode (massactionbutton defined) or if we have already selected and sent an action ($massaction) defined
		{
			$selected=0;
			if (in_array($obj->rowid, $arrayofselected)) $selected=1;
			print '<input id="cb'.$obj->rowid.'" class="flat checkforselect" type="checkbox" name="toselect[]" value="'.$obj->rowid.'"'.($selected?' checked="checked"':'').'>';
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

print '</form>'."\n";

	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);
	 // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->budget->bud->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=import">'.$langs->trans("Import").'</a></div>'."\n";
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=create">'.$langs->trans('Create').'</a></div>'."\n";
		}
	}
	print '</div>'."\n";
