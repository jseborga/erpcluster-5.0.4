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
 *   	\file       priceunits/budgettask_list.php
 *		\ingroup    priceunits
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-10-20 14:47
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
dol_include_once('/priceunits/class/budgettask.class.php');

// Load traductions files requiredby by page
$langs->load("priceunits");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_fk_budget=GETPOST('search_fk_budget','int');
$search_fk_task=GETPOST('search_fk_task','int');
$search_fk_task_parent=GETPOST('search_fk_task_parent','int');
$search_label=GETPOST('search_label','alpha');
$search_description=GETPOST('search_description','alpha');
$search_duration_effective=GETPOST('search_duration_effective','alpha');
$search_planned_workload=GETPOST('search_planned_workload','alpha');
$search_progress=GETPOST('search_progress','int');
$search_priority=GETPOST('search_priority','int');
$search_fk_user_creat=GETPOST('search_fk_user_creat','int');
$search_fk_user_valid=GETPOST('search_fk_user_valid','int');
$search_fk_statut=GETPOST('search_fk_statut','int');
$search_note_private=GETPOST('search_note_private','alpha');
$search_note_public=GETPOST('search_note_public','alpha');
$search_rang=GETPOST('search_rang','int');
$search_model_pdf=GETPOST('search_model_pdf','alpha');


$search_myfield=GETPOST('search_myfield');
$optioncss = GETPOST('optioncss','alpha');

// Load variable for pagination
$limit = GETPOST("limit")?GETPOST("limit","int"):$conf->liste_limit;
$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha');
$page = GETPOST('page','int');
if ($page == -1) { $page = 0; }
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

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('budgettasklist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('priceunits');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Load object if id or ref is provided as parameter
$object=new Budgettask($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Definition of fields for list
$arrayfields=array(
    
't.entity'=>array('label'=>$langs->trans("Fieldentity"), 'checked'=>1),
't.ref'=>array('label'=>$langs->trans("Fieldref"), 'checked'=>1),
't.fk_budget'=>array('label'=>$langs->trans("Fieldfk_budget"), 'checked'=>1),
't.fk_task'=>array('label'=>$langs->trans("Fieldfk_task"), 'checked'=>1),
't.fk_task_parent'=>array('label'=>$langs->trans("Fieldfk_task_parent"), 'checked'=>1),
't.label'=>array('label'=>$langs->trans("Fieldlabel"), 'checked'=>1),
't.description'=>array('label'=>$langs->trans("Fielddescription"), 'checked'=>1),
't.duration_effective'=>array('label'=>$langs->trans("Fieldduration_effective"), 'checked'=>1),
't.planned_workload'=>array('label'=>$langs->trans("Fieldplanned_workload"), 'checked'=>1),
't.progress'=>array('label'=>$langs->trans("Fieldprogress"), 'checked'=>1),
't.priority'=>array('label'=>$langs->trans("Fieldpriority"), 'checked'=>1),
't.fk_user_creat'=>array('label'=>$langs->trans("Fieldfk_user_creat"), 'checked'=>1),
't.fk_user_valid'=>array('label'=>$langs->trans("Fieldfk_user_valid"), 'checked'=>1),
't.fk_statut'=>array('label'=>$langs->trans("Fieldfk_statut"), 'checked'=>1),
't.note_private'=>array('label'=>$langs->trans("Fieldnote_private"), 'checked'=>1),
't.note_public'=>array('label'=>$langs->trans("Fieldnote_public"), 'checked'=>1),
't.rang'=>array('label'=>$langs->trans("Fieldrang"), 'checked'=>1),
't.model_pdf'=>array('label'=>$langs->trans("Fieldmodel_pdf"), 'checked'=>1),

    
    //'t.entity'=>array('label'=>$langs->trans("Entity"), 'checked'=>1, 'enabled'=>(! empty($conf->multicompany->enabled) && empty($conf->multicompany->transverse_mode))),
    't.datec'=>array('label'=>$langs->trans("DateCreationShort"), 'checked'=>0, 'position'=>500),
    't.tms'=>array('label'=>$langs->trans("DateModificationShort"), 'checked'=>0, 'position'=>500),
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
if (! GETPOST('confirmmassaction')) { $massaction=''; }

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter.x") ||GETPOST("button_removefilter")) // All test are required to be compatible with all browsers
{
	
$search_entity='';
$search_ref='';
$search_fk_budget='';
$search_fk_task='';
$search_fk_task_parent='';
$search_label='';
$search_description='';
$search_duration_effective='';
$search_planned_workload='';
$search_progress='';
$search_priority='';
$search_fk_user_creat='';
$search_fk_user_valid='';
$search_fk_statut='';
$search_note_private='';
$search_note_public='';
$search_rang='';
$search_model_pdf='';

	
	$search_date_creation='';
	$search_date_update='';
	$search_array_options=array();
}


if (empty($reshook))
{
    // Mass actions. Controls on number of lines checked
    $maxformassaction=1000;
    if (! empty($massaction) && count($toselect) < 1)
    {
        $error++;
        setEventMessages($langs->trans("NoLineChecked"), null, "warnings");
    }
    if (! $error && count($toselect) > $maxformassaction)
    {
        setEventMessages($langs->trans('TooManyRecordForMassAction',$maxformassaction), null, 'errors');
        $error++;
    }
    
	// Action to delete
	if ($action == 'confirm_delete')
	{
		$result=$object->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/priceunits/list.php',1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null,$object->errors,'errors');
			else setEventMessages($object->error,null,'errors');
		}
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
llxHeader('', $title, $help_url);

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
		$sql .= " t.fk_budget,";
		$sql .= " t.fk_task,";
		$sql .= " t.fk_task_parent,";
		$sql .= " t.datec,";
		$sql .= " t.tms,";
		$sql .= " t.dateo,";
		$sql .= " t.datee,";
		$sql .= " t.datev,";
		$sql .= " t.label,";
		$sql .= " t.description,";
		$sql .= " t.duration_effective,";
		$sql .= " t.planned_workload,";
		$sql .= " t.progress,";
		$sql .= " t.priority,";
		$sql .= " t.fk_user_creat,";
		$sql .= " t.fk_user_valid,";
		$sql .= " t.fk_statut,";
		$sql .= " t.note_private,";
		$sql .= " t.note_public,";
		$sql .= " t.rang,";
		$sql .= " t.model_pdf";


// Add fields for extrafields
foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."budget_task as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."budget_task_extrafields as ef on (u.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_entity) $sql.= natural_search("entity",$search_entity);
if ($search_ref) $sql.= natural_search("ref",$search_ref);
if ($search_fk_budget) $sql.= natural_search("fk_budget",$search_fk_budget);
if ($search_fk_task) $sql.= natural_search("fk_task",$search_fk_task);
if ($search_fk_task_parent) $sql.= natural_search("fk_task_parent",$search_fk_task_parent);
if ($search_label) $sql.= natural_search("label",$search_label);
if ($search_description) $sql.= natural_search("description",$search_description);
if ($search_duration_effective) $sql.= natural_search("duration_effective",$search_duration_effective);
if ($search_planned_workload) $sql.= natural_search("planned_workload",$search_planned_workload);
if ($search_progress) $sql.= natural_search("progress",$search_progress);
if ($search_priority) $sql.= natural_search("priority",$search_priority);
if ($search_fk_user_creat) $sql.= natural_search("fk_user_creat",$search_fk_user_creat);
if ($search_fk_user_valid) $sql.= natural_search("fk_user_valid",$search_fk_user_valid);
if ($search_fk_statut) $sql.= natural_search("fk_statut",$search_fk_statut);
if ($search_note_private) $sql.= natural_search("note_private",$search_note_private);
if ($search_note_public) $sql.= natural_search("note_public",$search_note_public);
if ($search_rang) $sql.= natural_search("rang",$search_rang);
if ($search_model_pdf) $sql.= natural_search("model_pdf",$search_model_pdf);


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
$nbtotalofrecords = 0;
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
	$result = $db->query($sql);
	$nbtotalofrecords = $db->num_rows($result);
}	

$sql.= $db->plimit($limit+1, $offset);


dol_syslog($script_file, LOG_DEBUG);
$resql=$db->query($sql);
if ($resql)
{
    $num = $db->num_rows($resql);
    
    $params='';
    if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;
    
if ($search_entity != '') $params.= '&amp;search_entity='.urlencode($search_entity);
if ($search_ref != '') $params.= '&amp;search_ref='.urlencode($search_ref);
if ($search_fk_budget != '') $params.= '&amp;search_fk_budget='.urlencode($search_fk_budget);
if ($search_fk_task != '') $params.= '&amp;search_fk_task='.urlencode($search_fk_task);
if ($search_fk_task_parent != '') $params.= '&amp;search_fk_task_parent='.urlencode($search_fk_task_parent);
if ($search_label != '') $params.= '&amp;search_label='.urlencode($search_label);
if ($search_description != '') $params.= '&amp;search_description='.urlencode($search_description);
if ($search_duration_effective != '') $params.= '&amp;search_duration_effective='.urlencode($search_duration_effective);
if ($search_planned_workload != '') $params.= '&amp;search_planned_workload='.urlencode($search_planned_workload);
if ($search_progress != '') $params.= '&amp;search_progress='.urlencode($search_progress);
if ($search_priority != '') $params.= '&amp;search_priority='.urlencode($search_priority);
if ($search_fk_user_creat != '') $params.= '&amp;search_fk_user_creat='.urlencode($search_fk_user_creat);
if ($search_fk_user_valid != '') $params.= '&amp;search_fk_user_valid='.urlencode($search_fk_user_valid);
if ($search_fk_statut != '') $params.= '&amp;search_fk_statut='.urlencode($search_fk_statut);
if ($search_note_private != '') $params.= '&amp;search_note_private='.urlencode($search_note_private);
if ($search_note_public != '') $params.= '&amp;search_note_public='.urlencode($search_note_public);
if ($search_rang != '') $params.= '&amp;search_rang='.urlencode($search_rang);
if ($search_model_pdf != '') $params.= '&amp;search_model_pdf='.urlencode($search_model_pdf);

	
    if ($optioncss != '') $param.='&optioncss='.$optioncss;
    // Add $param from extra fields
    foreach ($search_array_options as $key => $val)
    {
        $crit=$val;
        $tmpkey=preg_replace('/search_options_/','',$key);
        if ($val != '') $param.='&search_options_'.$tmpkey.'='.urlencode($val);
    } 

    print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $params, $sortfield, $sortorder, '', $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);


	print '<form method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
    if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
    print '<input type="hidden" name="action" value="list">';
	print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
	print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
	
    if ($sall)
    {
        foreach($fieldstosearchall as $key => $val) $fieldstosearchall[$key]=$langs->trans($val);
        print $langs->trans("FilterOnInto", $all) . join(', ',$fieldstosearchall);
    }
    
    $moreforfilter = '';
    $moreforfilter.='<div class="divsearchfield">';
    $moreforfilter.= $langs->trans('MyFilter') . ': <input type="text" name="search_myfield" value="'.dol_escape_htmltag($search_myfield).'">';
    $moreforfilter.= '</div>';
    
	if (! empty($moreforfilter))
	{
		print '<div class="liste_titre liste_titre_bydiv centpercent">';
		print $moreforfilter;
    	$parameters=array();
    	$reshook=$hookmanager->executeHooks('printFieldPreListTitle',$parameters);    // Note that $action and $object may have been modified by hook
	    print $hookmanager->resPrint;
	    print '</div>';
	}

    $varpage=empty($contextpage)?$_SERVER["PHP_SELF"]:$contextpage;
    $selectedfields=$form->multiSelectArrayWithCheckbox('selectedfields', $arrayfields, $varpage);	// This also change content of $arrayfields
	
	print '<table class="liste '.($moreforfilter?"listwithfilterbefore":"").'">';

    // Fields title
    print '<tr class="liste_titre">';
    // 
if (! empty($arrayfields['t.entity']['checked'])) print_liste_field_titre($arrayfields['t.entity']['label'],$_SERVER['PHP_SELF'],'t.entity','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.ref']['checked'])) print_liste_field_titre($arrayfields['t.ref']['label'],$_SERVER['PHP_SELF'],'t.ref','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_budget']['checked'])) print_liste_field_titre($arrayfields['t.fk_budget']['label'],$_SERVER['PHP_SELF'],'t.fk_budget','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_task']['checked'])) print_liste_field_titre($arrayfields['t.fk_task']['label'],$_SERVER['PHP_SELF'],'t.fk_task','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_task_parent']['checked'])) print_liste_field_titre($arrayfields['t.fk_task_parent']['label'],$_SERVER['PHP_SELF'],'t.fk_task_parent','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.label']['checked'])) print_liste_field_titre($arrayfields['t.label']['label'],$_SERVER['PHP_SELF'],'t.label','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.description']['checked'])) print_liste_field_titre($arrayfields['t.description']['label'],$_SERVER['PHP_SELF'],'t.description','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.duration_effective']['checked'])) print_liste_field_titre($arrayfields['t.duration_effective']['label'],$_SERVER['PHP_SELF'],'t.duration_effective','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.planned_workload']['checked'])) print_liste_field_titre($arrayfields['t.planned_workload']['label'],$_SERVER['PHP_SELF'],'t.planned_workload','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.progress']['checked'])) print_liste_field_titre($arrayfields['t.progress']['label'],$_SERVER['PHP_SELF'],'t.progress','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.priority']['checked'])) print_liste_field_titre($arrayfields['t.priority']['label'],$_SERVER['PHP_SELF'],'t.priority','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_creat']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_creat']['label'],$_SERVER['PHP_SELF'],'t.fk_user_creat','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_valid']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_valid']['label'],$_SERVER['PHP_SELF'],'t.fk_user_valid','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_statut']['checked'])) print_liste_field_titre($arrayfields['t.fk_statut']['label'],$_SERVER['PHP_SELF'],'t.fk_statut','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.note_private']['checked'])) print_liste_field_titre($arrayfields['t.note_private']['label'],$_SERVER['PHP_SELF'],'t.note_private','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.note_public']['checked'])) print_liste_field_titre($arrayfields['t.note_public']['label'],$_SERVER['PHP_SELF'],'t.note_public','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.rang']['checked'])) print_liste_field_titre($arrayfields['t.rang']['label'],$_SERVER['PHP_SELF'],'t.rang','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.model_pdf']['checked'])) print_liste_field_titre($arrayfields['t.model_pdf']['label'],$_SERVER['PHP_SELF'],'t.model_pdf','',$params,'',$sortfield,$sortorder);

    //if (! empty($arrayfields['t.field1']['checked'])) print_liste_field_titre($arrayfields['t.field1']['label'],$_SERVER['PHP_SELF'],'t.field1','',$params,'',$sortfield,$sortorder);
    //if (! empty($arrayfields['t.field2']['checked'])) print_liste_field_titre($arrayfields['t.field2']['label'],$_SERVER['PHP_SELF'],'t.field2','',$params,'',$sortfield,$sortorder);
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
    $reshook=$hookmanager->executeHooks('printFieldListTitle',$parameters);    // Note that $action and $object may have been modified by hook
    print $hookmanager->resPrint;
	if (! empty($arrayfields['t.datec']['checked']))  print_liste_field_titre($arrayfields['t.datec']['label'],$_SERVER["PHP_SELF"],"t.datec","",$param,'align="center" class="nowrap"',$sortfield,$sortorder);
	if (! empty($arrayfields['t.tms']['checked']))    print_liste_field_titre($arrayfields['t.tms']['label'],$_SERVER["PHP_SELF"],"t.tms","",$param,'align="center" class="nowrap"',$sortfield,$sortorder);
	//if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($langs->trans("Status"),$_SERVER["PHP_SELF"],"t.status","",$param,'align="center"',$sortfield,$sortorder);
	print_liste_field_titre($selectedfields, $_SERVER["PHP_SELF"],"",'','','align="right"',$sortfield,$sortorder,'maxwidthsearch ');
    print '</tr>'."\n";

    // Fields title search
	print '<tr class="liste_titre">';
	// 
if (! empty($arrayfields['t.entity']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_entity" value="'.$search_entity.'" size="10"></td>';
if (! empty($arrayfields['t.ref']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
if (! empty($arrayfields['t.fk_budget']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_budget" value="'.$search_fk_budget.'" size="10"></td>';
if (! empty($arrayfields['t.fk_task']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_task" value="'.$search_fk_task.'" size="10"></td>';
if (! empty($arrayfields['t.fk_task_parent']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_task_parent" value="'.$search_fk_task_parent.'" size="10"></td>';
if (! empty($arrayfields['t.label']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_label" value="'.$search_label.'" size="10"></td>';
if (! empty($arrayfields['t.description']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_description" value="'.$search_description.'" size="10"></td>';
if (! empty($arrayfields['t.duration_effective']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_duration_effective" value="'.$search_duration_effective.'" size="10"></td>';
if (! empty($arrayfields['t.planned_workload']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_planned_workload" value="'.$search_planned_workload.'" size="10"></td>';
if (! empty($arrayfields['t.progress']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_progress" value="'.$search_progress.'" size="10"></td>';
if (! empty($arrayfields['t.priority']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_priority" value="'.$search_priority.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_creat']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_creat" value="'.$search_fk_user_creat.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_valid']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_valid" value="'.$search_fk_user_valid.'" size="10"></td>';
if (! empty($arrayfields['t.fk_statut']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_statut" value="'.$search_fk_statut.'" size="10"></td>';
if (! empty($arrayfields['t.note_private']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_note_private" value="'.$search_note_private.'" size="10"></td>';
if (! empty($arrayfields['t.note_public']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_note_public" value="'.$search_note_public.'" size="10"></td>';
if (! empty($arrayfields['t.rang']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_rang" value="'.$search_rang.'" size="10"></td>';
if (! empty($arrayfields['t.model_pdf']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_model_pdf" value="'.$search_model_pdf.'" size="10"></td>';

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
    $searchpitco=$form->showFilterAndCheckAddButtons(0);
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
            /*
            if (! empty($arrayfields['t.field1']['checked'])) 
            {
                print '<td>'.$obj->field1.'</td>';
    		    if (! $i) $totalarray['nbfield']++;
            }
            if (! empty($arrayfields['t.field2']['checked'])) 
            {
                print '<td>'.$obj->field2.'</td>';
    		    if (! $i) $totalarray['nbfield']++;
            }*/
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
            print '<td></td>';
            if (! $i) $totalarray['nbfield']++;

            print '</tr>';
        }
        $i++;
    }
    
    $db->free($resql);

	$parameters=array('sql' => $sql);
	$reshook=$hookmanager->executeHooks('printFieldListFooter',$parameters);    // Note that $action and $object may have been modified by hook
	print $hookmanager->resPrint;

	print "</table>\n";
	print "</form>\n";
	
	$db->free($result);
}
else
{
    $error++;
    dol_print_error($db);
}


// End of page
llxFooter();
$db->close();
