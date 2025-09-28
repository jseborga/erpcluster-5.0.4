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
 *   	\file       budget/budgettaskresource_list.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-04-23 17:06
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
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_code_structure=GETPOST('search_code_structure','alpha');
$search_fk_product=GETPOST('search_fk_product','int');
$search_fk_product_budget=GETPOST('search_fk_product_budget','int');
$search_fk_budget_task_comple=GETPOST('search_fk_budget_task_comple','int');
$search_detail=GETPOST('search_detail','alpha');
$search_fk_unit=GETPOST('search_fk_unit','int');
$search_quant=GETPOST('search_quant','alpha');
$search_percent_prod=GETPOST('search_percent_prod','alpha');
$search_amount_noprod=GETPOST('search_amount_noprod','alpha');
$search_amount=GETPOST('search_amount','alpha');
$search_rang=GETPOST('search_rang','int');
$search_priority=GETPOST('search_priority','int');
$search_formula=GETPOST('search_formula','alpha');
$search_formula_res=GETPOST('search_formula_res','alpha');
$search_formula_quant=GETPOST('search_formula_quant','alpha');
$search_formula_factor=GETPOST('search_formula_factor','alpha');
$search_formula_prod=GETPOST('search_formula_prod','alpha');
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
if (! $sortfield) $sortfield="t.code_structure,t.ref"; // Set here default search field
if (! $sortorder) $sortorder="ASC";

// Protection if external user
$socid=0;
if ($user->societe_id > 0)
{
	$socid = $user->societe_id;
	//accessforbidden();
}
// Initialize technical object to manage context to save list fields
$contextpage=GETPOST('contextpage','aZ')?GETPOST('contextpage','aZ'):'budgettaskresourcelist';

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
	't.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'align'=>'align="left"', 'checked'=>0),
	't.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'align'=>'align="left"', 'checked'=>0),
	't.code_structure'=>array('label'=>$langs->trans("Fieldcode_structure"), 'align'=>'align="left"', 'checked'=>1),
	't.fk_product'=>array('label'=>$langs->trans("Fieldfk_product"), 'align'=>'align="left"', 'checked'=>0),
	't.fk_product_budget'=>array('label'=>$langs->trans("Fieldfk_product_budget"), 'align'=>'align="left"', 'checked'=>1),
	't.fk_budget_task_comple'=>array('label'=>$langs->trans("Fieldfk_budget_task_comple"), 'align'=>'align="left"', 'checked'=>0),
	't.detail'=>array('label'=>$langs->trans("Fielddetail"), 'align'=>'align="left"', 'checked'=>1),
	't.fk_unit'=>array('label'=>$langs->trans("Fieldfk_unit"), 'align'=>'align="left"', 'checked'=>1),
	't.formula'=>array('label'=>$langs->trans("Fieldformula"), 'align'=>'align="left"', 'checked'=>1),
	't.quant'=>array('label'=>$langs->trans("Fieldquant"), 'align'=>'align="left"', 'checked'=>1),
	't.percent_prod'=>array('label'=>$langs->trans("Fieldpercent_prod"), 'align'=>'align="right"', 'checked'=>1),
	't.amount_noprod'=>array('label'=>$langs->trans("Fieldamount_noprod"), 'align'=>'align="right"', 'checked'=>1),
	't.amount'=>array('label'=>$langs->trans("Fieldamount"), 'align'=>'align="right"', 'checked'=>1),
	't.rang'=>array('label'=>$langs->trans("Fieldrang"), 'align'=>'align="left"', 'checked'=>0),
	't.priority'=>array('label'=>$langs->trans("Fieldpriority"), 'align'=>'align="left"', 'checked'=>0),
	't.formula_res'=>array('label'=>$langs->trans("Fieldformula_res"), 'align'=>'align="left"', 'checked'=>0),
	't.formula_quant'=>array('label'=>$langs->trans("Fieldformula_quant"), 'align'=>'align="left"', 'checked'=>0),
	't.formula_factor'=>array('label'=>$langs->trans("Fieldformula_factor"), 'align'=>'align="left"', 'checked'=>0),
	't.formula_prod'=>array('label'=>$langs->trans("Fieldformula_prod"), 'align'=>'align="left"', 'checked'=>0),
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

		$search_fk_budget_task='';
		$search_ref='';
		$search_fk_user_create='';
		$search_fk_user_mod='';
		$search_code_structure='';
		$search_fk_product='';
		$search_fk_product_budget='';
		$search_fk_budget_task_comple='';
		$search_detail='';
		$search_fk_unit='';
		$search_quant='';
		$search_percent_prod='';
		$search_amount_noprod='';
		$search_amount='';
		$search_rang='';
		$search_priority='';
		$search_formula='';
		$search_formula_res='';
		$search_formula_quant='';
		$search_formula_factor='';
		$search_formula_prod='';
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
$title = $langs->trans('Supplies');

// Put here content of your page

$sql = "SELECT";
$sql.= " t.rowid,";

$sql .= " t.fk_budget_task,";
$sql .= " t.ref,";
$sql .= " t.fk_user_create,";
$sql .= " t.fk_user_mod,";
$sql .= " t.code_structure,";
$sql .= " t.fk_product,";
$sql .= " t.fk_product_budget,";
$sql .= " t.fk_budget_task_comple,";
$sql .= " t.detail,";
$sql .= " t.fk_unit,";
$sql .= " t.quant,";
$sql .= " t.percent_prod,";
$sql .= " t.amount_noprod,";
$sql .= " t.amount,";
$sql .= " t.rang,";
$sql .= " t.priority,";
$sql .= " t.formula,";
$sql .= " t.formula_res,";
$sql .= " t.formula_quant,";
$sql .= " t.formula_factor,";
$sql .= " t.formula_prod,";
$sql .= " t.date_create,";
$sql .= " t.date_mod,";
$sql .= " t.tms,";
$sql .= " t.status";


// Add fields from extrafields
foreach ($extrafields->attribute_label as $key => $val) $sql.=($extrafields->attribute_type[$key] != 'separate' ? ",ef.".$key.' as options_'.$key : '');
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."budget_task_resource as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."budget_task_resource_extrafields as ef on (t.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
$sql.= " AND t.fk_budget_task = ".$object->id;
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_fk_budget_task) $sql.= natural_search("fk_budget_task",$search_fk_budget_task);
if ($search_ref) $sql.= natural_search("ref",$search_ref);
if ($search_fk_user_create) $sql.= natural_search("fk_user_create",$search_fk_user_create);
if ($search_fk_user_mod) $sql.= natural_search("fk_user_mod",$search_fk_user_mod);
if ($search_code_structure) $sql.= natural_search("code_structure",$search_code_structure);
if ($search_fk_product) $sql.= natural_search("fk_product",$search_fk_product);
if ($search_fk_product_budget) $sql.= natural_search("fk_product_budget",$search_fk_product_budget);
if ($search_fk_budget_task_comple) $sql.= natural_search("fk_budget_task_comple",$search_fk_budget_task_comple);
if ($search_detail) $sql.= natural_search("detail",$search_detail);
if ($search_fk_unit) $sql.= natural_search("fk_unit",$search_fk_unit);
if ($search_quant) $sql.= natural_search("quant",$search_quant);
if ($search_percent_prod) $sql.= natural_search("percent_prod",$search_percent_prod);
if ($search_amount_noprod) $sql.= natural_search("amount_noprod",$search_amount_noprod);
if ($search_amount) $sql.= natural_search("amount",$search_amount);
if ($search_rang) $sql.= natural_search("rang",$search_rang);
if ($search_priority) $sql.= natural_search("priority",$search_priority);
if ($search_formula) $sql.= natural_search("formula",$search_formula);
if ($search_formula_res) $sql.= natural_search("formula_res",$search_formula_res);
if ($search_formula_quant) $sql.= natural_search("formula_quant",$search_formula_quant);
if ($search_formula_factor) $sql.= natural_search("formula_factor",$search_formula_factor);
if ($search_formula_prod) $sql.= natural_search("formula_prod",$search_formula_prod);
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

print '<form method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
print '<input type="hidden" name="action" value="list">';
print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
print '<input type="hidden" name="contextpage" value="'.$contextpage.'">';
print '<input type="hidden" name="id" value="'.$id.'">';

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
if (! empty($arrayfields['t.fk_budget_task']['checked'])) print_liste_field_titre($arrayfields['t.fk_budget_task']['label'],$_SERVER['PHP_SELF'],'t.fk_budget_task','',$params,$arrayfields['t.fk_budget_task']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.ref']['checked'])) print_liste_field_titre($arrayfields['t.ref']['label'],$_SERVER['PHP_SELF'],'t.ref','',$params,$arrayfields['t.ref']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_create']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_create']['label'],$_SERVER['PHP_SELF'],'t.fk_user_create','',$params,$arrayfields['t.fk_user_create']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_mod']['label'],$_SERVER['PHP_SELF'],'t.fk_user_mod','',$params,$arrayfields['t.fk_user_mod']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.code_structure']['checked'])) print_liste_field_titre($arrayfields['t.code_structure']['label'],$_SERVER['PHP_SELF'],'t.code_structure','',$params,$arrayfields['t.code_structure']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_product']['checked'])) print_liste_field_titre($arrayfields['t.fk_product']['label'],$_SERVER['PHP_SELF'],'t.fk_product','',$params,$arrayfields['t.fk_product']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_product_budget']['checked'])) print_liste_field_titre($arrayfields['t.fk_product_budget']['label'],$_SERVER['PHP_SELF'],'t.fk_product_budget','',$params,$arrayfields['t.fk_product_budget']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_budget_task_comple']['checked'])) print_liste_field_titre($arrayfields['t.fk_budget_task_comple']['label'],$_SERVER['PHP_SELF'],'t.fk_budget_task_comple','',$params,$arrayfields['t.fk_budget_task_comple']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.detail']['checked'])) print_liste_field_titre($arrayfields['t.detail']['label'],$_SERVER['PHP_SELF'],'t.detail','',$params,$arrayfields['t.detail']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_unit']['checked'])) print_liste_field_titre($arrayfields['t.fk_unit']['label'],$_SERVER['PHP_SELF'],'t.fk_unit','',$params,$arrayfields['t.fk_unit']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.formula']['checked'])) print_liste_field_titre($arrayfields['t.formula']['label'],$_SERVER['PHP_SELF'],'t.formula','',$params,$arrayfields['t.formula']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.quant']['checked'])) print_liste_field_titre($arrayfields['t.quant']['label'],$_SERVER['PHP_SELF'],'t.quant','',$params,$arrayfields['t.quant']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.percent_prod']['checked'])) print_liste_field_titre($arrayfields['t.percent_prod']['label'],$_SERVER['PHP_SELF'],'t.percent_prod','',$params,$arrayfields['t.percent_prod']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.amount_noprod']['checked'])) print_liste_field_titre($arrayfields['t.amount_noprod']['label'],$_SERVER['PHP_SELF'],'t.amount_noprod','',$params,$arrayfields['t.amount_noprod']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.amount']['checked'])) print_liste_field_titre($arrayfields['t.amount']['label'],$_SERVER['PHP_SELF'],'t.amount','',$params,$arrayfields['t.amount']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.rang']['checked'])) print_liste_field_titre($arrayfields['t.rang']['label'],$_SERVER['PHP_SELF'],'t.rang','',$params,$arrayfields['t.rang']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.priority']['checked'])) print_liste_field_titre($arrayfields['t.priority']['label'],$_SERVER['PHP_SELF'],'t.priority','',$params,$arrayfields['t.priority']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.formula_res']['checked'])) print_liste_field_titre($arrayfields['t.formula_res']['label'],$_SERVER['PHP_SELF'],'t.formula_res','',$params,$arrayfields['t.formula_res']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.formula_quant']['checked'])) print_liste_field_titre($arrayfields['t.formula_quant']['label'],$_SERVER['PHP_SELF'],'t.formula_quant','',$params,$arrayfields['t.formula_quant']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.formula_factor']['checked'])) print_liste_field_titre($arrayfields['t.formula_factor']['label'],$_SERVER['PHP_SELF'],'t.formula_factor','',$params,$arrayfields['t.formula_factor']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.formula_prod']['checked'])) print_liste_field_titre($arrayfields['t.formula_prod']['label'],$_SERVER['PHP_SELF'],'t.formula_prod','',$params,$arrayfields['t.formula_prod']['align'],$sortfield,$sortorder);
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
if (! empty($arrayfields['t.fk_budget_task']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_budget_task']['align'].'><input type="text" class="flat" name="search_fk_budget_task" value="'.$search_fk_budget_task.'" size="10"></td>';
if (! empty($arrayfields['t.ref']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.ref']['align'].'><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_create']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_user_create']['align'].'><input type="text" class="flat" name="search_fk_user_create" value="'.$search_fk_user_create.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_user_mod']['align'].'><input type="text" class="flat" name="search_fk_user_mod" value="'.$search_fk_user_mod.'" size="10"></td>';
if (! empty($arrayfields['t.code_structure']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.code_structure']['align'].'><input type="text" class="flat" name="search_code_structure" value="'.$search_code_structure.'" size="10"></td>';
if (! empty($arrayfields['t.fk_product']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_product']['align'].'><input type="text" class="flat" name="search_fk_product" value="'.$search_fk_product.'" size="10"></td>';
if (! empty($arrayfields['t.fk_product_budget']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_product_budget']['align'].'><input type="text" class="flat" name="search_fk_product_budget" value="'.$search_fk_product_budget.'" size="10"></td>';
if (! empty($arrayfields['t.fk_budget_task_comple']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_budget_task_comple']['align'].'><input type="text" class="flat" name="search_fk_budget_task_comple" value="'.$search_fk_budget_task_comple.'" size="10"></td>';
if (! empty($arrayfields['t.detail']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.detail']['align'].'><input type="text" class="flat" name="search_detail" value="'.$search_detail.'" size="10"></td>';
if (! empty($arrayfields['t.fk_unit']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_unit']['align'].'><input type="text" class="flat" name="search_fk_unit" value="'.$search_fk_unit.'" size="10"></td>';
if (! empty($arrayfields['t.quant']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.quant']['align'].'><input type="text" class="flat" name="search_quant" value="'.$search_quant.'" size="10"></td>';
if (! empty($arrayfields['t.percent_prod']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.percent_prod']['align'].'><input type="text" class="flat" name="search_percent_prod" value="'.$search_percent_prod.'" size="10"></td>';
if (! empty($arrayfields['t.amount_noprod']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.amount_noprod']['align'].'><input type="text" class="flat" name="search_amount_noprod" value="'.$search_amount_noprod.'" size="10"></td>';
if (! empty($arrayfields['t.amount']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.amount']['align'].'><input type="text" class="flat" name="search_amount" value="'.$search_amount.'" size="10"></td>';
if (! empty($arrayfields['t.rang']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.rang']['align'].'><input type="text" class="flat" name="search_rang" value="'.$search_rang.'" size="10"></td>';
if (! empty($arrayfields['t.priority']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.priority']['align'].'><input type="text" class="flat" name="search_priority" value="'.$search_priority.'" size="10"></td>';
if (! empty($arrayfields['t.formula']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.formula']['align'].'><input type="text" class="flat" name="search_formula" value="'.$search_formula.'" size="10"></td>';
if (! empty($arrayfields['t.formula_res']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.formula_res']['align'].'><input type="text" class="flat" name="search_formula_res" value="'.$search_formula_res.'" size="10"></td>';
if (! empty($arrayfields['t.formula_quant']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.formula_quant']['align'].'><input type="text" class="flat" name="search_formula_quant" value="'.$search_formula_quant.'" size="10"></td>';
if (! empty($arrayfields['t.formula_factor']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.formula_factor']['align'].'><input type="text" class="flat" name="search_formula_factor" value="'.$search_formula_factor.'" size="10"></td>';
if (! empty($arrayfields['t.formula_prod']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.formula_prod']['align'].'><input type="text" class="flat" name="search_formula_prod" value="'.$search_formula_prod.'" size="10"></td>';
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
$searchpitco=$form->showFilterAndCheckAddButtons($massactionbutton?0:0, 'checkforselect', 1);
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
while ($i < min($num, $limit))
{
	$obj = $db->fetch_object($resql);
	if ($obj)
	{
		$var = !$var;

		// Show here line of result

		if($aStrbudget[$object->fk_budget]['aStrcatgroup'][$obj->code_structure] == 'MA' && !empty($color_ma))
			print '<tr bgcolor="'.$color_ma.'">';
		elseif($aStrbudget[$object->fk_budget]['aStrcatgroup'][$obj->code_structure] == 'MO' && !empty($color_mo))
			print '<tr bgcolor="'.$color_mo.'">';
		elseif($aStrbudget[$object->fk_budget]['aStrcatgroup'][$obj->code_structure] == 'MQ' && !empty($color_mq))
			print '<tr bgcolor="'.$color_mq.'">';
		else
			print '<tr '.$bc[$var].'>';

		// LIST_OF_TD_FIELDS_LIST
		foreach ($arrayfields as $key => $value) {
			if (!empty($arrayfields[$key]['checked'])) {
				//$key2 = str_replace('t.', '', $key);
				$aKey = explode('.',$key);
				$key2 = $aKey[1];
				if ($key2 == 'ref')
				{
					$objBudgettaskresource->id = $obj->rowid;
					$objBudgettaskresource->fk_budget_task = $obj->fk_budget_task;
					$objBudgettaskresource->ref = $obj->ref;
					$objBudgettaskresource->label = $obj->label;
					$obj->$key2 = $objBudgettaskresource->getNomUrladd();
				}
				if ($key2 == 'code_structure')
				{
					$img = 'switch_off';
					if ($obj->$key2) $img = 'switch_on';
					$obj->$key2 = $aStrbudget[$object->fk_budget]['aStrcatgroup'][$obj->$key2];
				}
				if ($key2 == 'fk_unit')
				{
					$objTmp = new BudgettaskresourceLineext($db);
					$objTmp->fk_unit = $obj->$key2;
					$obj->$key2 = $objTmp->getLabelOfUnit('short');
				}
				if ($key2 == 'active')
				{
					$img = 'switch_off';
					if ($obj->$key2) $img = 'switch_on';
					$obj->$key2 = img_picto('',$img);
				}
				if ($key2 == 'status')
				{
					$objBudgettaskresource->status = $obj->$key2;
					$obj->$key2 = $objBudgettaskresource->getLibStatut(3);
				}
				if ($key2 == 'fk_user_create' || $key2 == 'fk_user_mod')
				{
					$res = $objUser->fetch($obj->$key2);
					if ($res == 1)
						$obj->$key2 = $objUser->getNomUrl(1);
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


		// Action column
		print '<td class="nowrap" align="center">';

		if ($user->rights->budget->budi->crear)
		{
			$lView=true;
			$aStrbudget[$object->fk_budget]['aStrcatgroup'][$obj->code_structure].' '.$obj->code_structure;
			if ($aStrbudget[$object->fk_budget]['aStrcatgroup'][$obj->code_structure] == 'MQ')
			{
				if (!$object->manual_performance) $lView=false;
			}

			if ($lView && $object->status ==0)
			{
				print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$obj->fk_item.'&idr='.$obj->rowid.'&action=editline'.'">'.img_picto('','edit').'</a>';
			}
		}

		if ($user->rights->budget->tasksupp->del)
		{
			print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&idr='.$obj->rowid.'">'.img_picto('','delete').'</a>';
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

