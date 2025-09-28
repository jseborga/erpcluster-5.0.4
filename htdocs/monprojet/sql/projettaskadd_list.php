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
 *   	\file       monprojet/projettaskadd_list.php
 *		\ingroup    monprojet
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-09-19 08:47
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
dol_include_once('/monprojet/class/projettaskadd.class.php');

// Load traductions files requiredby by page
$langs->load("monprojet");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_fk_task=GETPOST('search_fk_task','int');
$search_fk_contrat=GETPOST('search_fk_contrat','int');
$search_c_grupo=GETPOST('search_c_grupo','int');
$search_level=GETPOST('search_level','int');
$search_c_view=GETPOST('search_c_view','int');
$search_fk_unit=GETPOST('search_fk_unit','int');
$search_fk_type=GETPOST('search_fk_type','int');
$search_fk_item=GETPOST('search_fk_item','int');
$search_unit_budget=GETPOST('search_unit_budget','int');
$search_unit_program=GETPOST('search_unit_program','alpha');
$search_unit_declared=GETPOST('search_unit_declared','alpha');
$search_unit_ejecuted=GETPOST('search_unit_ejecuted','alpha');
$search_unit_amount=GETPOST('search_unit_amount','alpha');
$search_detail_close=GETPOST('search_detail_close','alpha');
$search_order_ref=GETPOST('search_order_ref','int');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_statut=GETPOST('search_statut','int');


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
$hookmanager->initHooks(array('projettaskaddlist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('monprojet');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Load object if id or ref is provided as parameter
$object=new Projettaskadd($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Definition of fields for list
$arrayfields=array(
    
't.fk_task'=>array('label'=>$langs->trans("Fieldfk_task"), 'checked'=>1),
't.fk_contrat'=>array('label'=>$langs->trans("Fieldfk_contrat"), 'checked'=>1),
't.c_grupo'=>array('label'=>$langs->trans("Fieldc_grupo"), 'checked'=>1),
't.level'=>array('label'=>$langs->trans("Fieldlevel"), 'checked'=>1),
't.c_view'=>array('label'=>$langs->trans("Fieldc_view"), 'checked'=>1),
't.fk_unit'=>array('label'=>$langs->trans("Fieldfk_unit"), 'checked'=>1),
't.fk_type'=>array('label'=>$langs->trans("Fieldfk_type"), 'checked'=>1),
't.fk_item'=>array('label'=>$langs->trans("Fieldfk_item"), 'checked'=>1),
't.unit_budget'=>array('label'=>$langs->trans("Fieldunit_budget"), 'checked'=>1),
't.unit_program'=>array('label'=>$langs->trans("Fieldunit_program"), 'checked'=>1),
't.unit_declared'=>array('label'=>$langs->trans("Fieldunit_declared"), 'checked'=>1),
't.unit_ejecuted'=>array('label'=>$langs->trans("Fieldunit_ejecuted"), 'checked'=>1),
't.unit_amount'=>array('label'=>$langs->trans("Fieldunit_amount"), 'checked'=>1),
't.detail_close'=>array('label'=>$langs->trans("Fielddetail_close"), 'checked'=>1),
't.order_ref'=>array('label'=>$langs->trans("Fieldorder_ref"), 'checked'=>1),
't.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'checked'=>1),
't.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'checked'=>1),
't.statut'=>array('label'=>$langs->trans("Fieldstatut"), 'checked'=>1),

    
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
	
$search_fk_task='';
$search_fk_contrat='';
$search_c_grupo='';
$search_level='';
$search_c_view='';
$search_fk_unit='';
$search_fk_type='';
$search_fk_item='';
$search_unit_budget='';
$search_unit_program='';
$search_unit_declared='';
$search_unit_ejecuted='';
$search_unit_amount='';
$search_detail_close='';
$search_order_ref='';
$search_fk_user_create='';
$search_fk_user_mod='';
$search_statut='';

	
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
			header("Location: ".dol_buildpath('/monprojet/list.php',1));
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

		$sql .= " t.fk_task,";
		$sql .= " t.fk_contrat,";
		$sql .= " t.c_grupo,";
		$sql .= " t.level,";
		$sql .= " t.c_view,";
		$sql .= " t.fk_unit,";
		$sql .= " t.fk_type,";
		$sql .= " t.fk_item,";
		$sql .= " t.unit_budget,";
		$sql .= " t.unit_program,";
		$sql .= " t.unit_declared,";
		$sql .= " t.unit_ejecuted,";
		$sql .= " t.unit_amount,";
		$sql .= " t.detail_close,";
		$sql .= " t.order_ref,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.date_create,";
		$sql .= " t.tms,";
		$sql .= " t.statut";


// Add fields for extrafields
foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."projet_task_add as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."projet_task_add_extrafields as ef on (u.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_fk_task) $sql.= natural_search("fk_task",$search_fk_task);
if ($search_fk_contrat) $sql.= natural_search("fk_contrat",$search_fk_contrat);
if ($search_c_grupo) $sql.= natural_search("c_grupo",$search_c_grupo);
if ($search_level) $sql.= natural_search("level",$search_level);
if ($search_c_view) $sql.= natural_search("c_view",$search_c_view);
if ($search_fk_unit) $sql.= natural_search("fk_unit",$search_fk_unit);
if ($search_fk_type) $sql.= natural_search("fk_type",$search_fk_type);
if ($search_fk_item) $sql.= natural_search("fk_item",$search_fk_item);
if ($search_unit_budget) $sql.= natural_search("unit_budget",$search_unit_budget);
if ($search_unit_program) $sql.= natural_search("unit_program",$search_unit_program);
if ($search_unit_declared) $sql.= natural_search("unit_declared",$search_unit_declared);
if ($search_unit_ejecuted) $sql.= natural_search("unit_ejecuted",$search_unit_ejecuted);
if ($search_unit_amount) $sql.= natural_search("unit_amount",$search_unit_amount);
if ($search_detail_close) $sql.= natural_search("detail_close",$search_detail_close);
if ($search_order_ref) $sql.= natural_search("order_ref",$search_order_ref);
if ($search_fk_user_create) $sql.= natural_search("fk_user_create",$search_fk_user_create);
if ($search_fk_user_mod) $sql.= natural_search("fk_user_mod",$search_fk_user_mod);
if ($search_statut) $sql.= natural_search("statut",$search_statut);


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
    
if ($search_fk_task != '') $params.= '&amp;search_fk_task='.urlencode($search_fk_task);
if ($search_fk_contrat != '') $params.= '&amp;search_fk_contrat='.urlencode($search_fk_contrat);
if ($search_c_grupo != '') $params.= '&amp;search_c_grupo='.urlencode($search_c_grupo);
if ($search_level != '') $params.= '&amp;search_level='.urlencode($search_level);
if ($search_c_view != '') $params.= '&amp;search_c_view='.urlencode($search_c_view);
if ($search_fk_unit != '') $params.= '&amp;search_fk_unit='.urlencode($search_fk_unit);
if ($search_fk_type != '') $params.= '&amp;search_fk_type='.urlencode($search_fk_type);
if ($search_fk_item != '') $params.= '&amp;search_fk_item='.urlencode($search_fk_item);
if ($search_unit_budget != '') $params.= '&amp;search_unit_budget='.urlencode($search_unit_budget);
if ($search_unit_program != '') $params.= '&amp;search_unit_program='.urlencode($search_unit_program);
if ($search_unit_declared != '') $params.= '&amp;search_unit_declared='.urlencode($search_unit_declared);
if ($search_unit_ejecuted != '') $params.= '&amp;search_unit_ejecuted='.urlencode($search_unit_ejecuted);
if ($search_unit_amount != '') $params.= '&amp;search_unit_amount='.urlencode($search_unit_amount);
if ($search_detail_close != '') $params.= '&amp;search_detail_close='.urlencode($search_detail_close);
if ($search_order_ref != '') $params.= '&amp;search_order_ref='.urlencode($search_order_ref);
if ($search_fk_user_create != '') $params.= '&amp;search_fk_user_create='.urlencode($search_fk_user_create);
if ($search_fk_user_mod != '') $params.= '&amp;search_fk_user_mod='.urlencode($search_fk_user_mod);
if ($search_statut != '') $params.= '&amp;search_statut='.urlencode($search_statut);

	
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
    $moreforfilter.= $langs->trans('MyFilter') . ': <input type="text" name="search_myfield" value="'.dol_escpae_htmltag($search_myfield).'">';
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
if (! empty($arrayfields['t.fk_task']['checked'])) print_liste_field_titre($arrayfields['t.fk_task']['label'],$_SERVER['PHP_SELF'],'t.fk_task','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_contrat']['checked'])) print_liste_field_titre($arrayfields['t.fk_contrat']['label'],$_SERVER['PHP_SELF'],'t.fk_contrat','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.c_grupo']['checked'])) print_liste_field_titre($arrayfields['t.c_grupo']['label'],$_SERVER['PHP_SELF'],'t.c_grupo','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.level']['checked'])) print_liste_field_titre($arrayfields['t.level']['label'],$_SERVER['PHP_SELF'],'t.level','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.c_view']['checked'])) print_liste_field_titre($arrayfields['t.c_view']['label'],$_SERVER['PHP_SELF'],'t.c_view','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_unit']['checked'])) print_liste_field_titre($arrayfields['t.fk_unit']['label'],$_SERVER['PHP_SELF'],'t.fk_unit','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_type']['checked'])) print_liste_field_titre($arrayfields['t.fk_type']['label'],$_SERVER['PHP_SELF'],'t.fk_type','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_item']['checked'])) print_liste_field_titre($arrayfields['t.fk_item']['label'],$_SERVER['PHP_SELF'],'t.fk_item','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.unit_budget']['checked'])) print_liste_field_titre($arrayfields['t.unit_budget']['label'],$_SERVER['PHP_SELF'],'t.unit_budget','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.unit_program']['checked'])) print_liste_field_titre($arrayfields['t.unit_program']['label'],$_SERVER['PHP_SELF'],'t.unit_program','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.unit_declared']['checked'])) print_liste_field_titre($arrayfields['t.unit_declared']['label'],$_SERVER['PHP_SELF'],'t.unit_declared','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.unit_ejecuted']['checked'])) print_liste_field_titre($arrayfields['t.unit_ejecuted']['label'],$_SERVER['PHP_SELF'],'t.unit_ejecuted','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.unit_amount']['checked'])) print_liste_field_titre($arrayfields['t.unit_amount']['label'],$_SERVER['PHP_SELF'],'t.unit_amount','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.detail_close']['checked'])) print_liste_field_titre($arrayfields['t.detail_close']['label'],$_SERVER['PHP_SELF'],'t.detail_close','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.order_ref']['checked'])) print_liste_field_titre($arrayfields['t.order_ref']['label'],$_SERVER['PHP_SELF'],'t.order_ref','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_create']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_create']['label'],$_SERVER['PHP_SELF'],'t.fk_user_create','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_mod']['label'],$_SERVER['PHP_SELF'],'t.fk_user_mod','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.statut']['checked'])) print_liste_field_titre($arrayfields['t.statut']['label'],$_SERVER['PHP_SELF'],'t.statut','',$params,'',$sortfield,$sortorder);

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
if (! empty($arrayfields['t.fk_task']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_task" value="'.$search_fk_task.'" size="10"></td>';
if (! empty($arrayfields['t.fk_contrat']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_contrat" value="'.$search_fk_contrat.'" size="10"></td>';
if (! empty($arrayfields['t.c_grupo']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_c_grupo" value="'.$search_c_grupo.'" size="10"></td>';
if (! empty($arrayfields['t.level']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_level" value="'.$search_level.'" size="10"></td>';
if (! empty($arrayfields['t.c_view']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_c_view" value="'.$search_c_view.'" size="10"></td>';
if (! empty($arrayfields['t.fk_unit']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_unit" value="'.$search_fk_unit.'" size="10"></td>';
if (! empty($arrayfields['t.fk_type']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_type" value="'.$search_fk_type.'" size="10"></td>';
if (! empty($arrayfields['t.fk_item']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_item" value="'.$search_fk_item.'" size="10"></td>';
if (! empty($arrayfields['t.unit_budget']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_unit_budget" value="'.$search_unit_budget.'" size="10"></td>';
if (! empty($arrayfields['t.unit_program']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_unit_program" value="'.$search_unit_program.'" size="10"></td>';
if (! empty($arrayfields['t.unit_declared']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_unit_declared" value="'.$search_unit_declared.'" size="10"></td>';
if (! empty($arrayfields['t.unit_ejecuted']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_unit_ejecuted" value="'.$search_unit_ejecuted.'" size="10"></td>';
if (! empty($arrayfields['t.unit_amount']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_unit_amount" value="'.$search_unit_amount.'" size="10"></td>';
if (! empty($arrayfields['t.detail_close']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_detail_close" value="'.$search_detail_close.'" size="10"></td>';
if (! empty($arrayfields['t.order_ref']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_order_ref" value="'.$search_order_ref.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_create']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_create" value="'.$search_fk_user_create.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_mod" value="'.$search_fk_user_mod.'" size="10"></td>';
if (! empty($arrayfields['t.statut']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_statut" value="'.$search_statut.'" size="10"></td>';

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
