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
 *   	\file       budget/budget_list.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-02-21 18:04
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
dol_include_once('/budget/class/budget.class.php');

// Load traductions files requiredby by page
$langs->load("budget");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_fk_soc=GETPOST('search_fk_soc','int');
$search_fk_projet=GETPOST('search_fk_projet','int');
$search_fk_calendar=GETPOST('search_fk_calendar','int');
$search_fk_country=GETPOST('search_fk_country','int');
$search_fk_departament=GETPOST('search_fk_departament','int');
$search_fk_city=GETPOST('search_fk_city','int');
$search_data_type=GETPOST('search_data_type','alpha');
$search_location=GETPOST('search_location','alpha');
$search_ref=GETPOST('search_ref','alpha');
$search_version=GETPOST('search_version','alpha');
$search_type_structure=GETPOST('search_type_structure','alpha');
$search_title=GETPOST('search_title','alpha');
$search_description=GETPOST('search_description','alpha');
$search_fk_user_creat=GETPOST('search_fk_user_creat','int');
$search_public=GETPOST('search_public','int');
$search_fk_statut=GETPOST('search_fk_statut','int');
$search_fk_opp_status=GETPOST('search_fk_opp_status','int');
$search_opp_percent=GETPOST('search_opp_percent','alpha');
$search_fk_user_close=GETPOST('search_fk_user_close','int');
$search_note_private=GETPOST('search_note_private','alpha');
$search_note_public=GETPOST('search_note_public','alpha');
$search_opp_amount=GETPOST('search_opp_amount','alpha');
$search_budget_amount=GETPOST('search_budget_amount','alpha');
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
$hookmanager->initHooks(array('budgetlist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('budget');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Load object if id or ref is provided as parameter
$object=new Budget($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Definition of fields for list
$arrayfields=array(
    
't.entity'=>array('label'=>$langs->trans("Fieldentity"), 'checked'=>1),
't.fk_soc'=>array('label'=>$langs->trans("Fieldfk_soc"), 'checked'=>1),
't.fk_projet'=>array('label'=>$langs->trans("Fieldfk_projet"), 'checked'=>1),
't.fk_calendar'=>array('label'=>$langs->trans("Fieldfk_calendar"), 'checked'=>1),
't.fk_country'=>array('label'=>$langs->trans("Fieldfk_country"), 'checked'=>1),
't.fk_departament'=>array('label'=>$langs->trans("Fieldfk_departament"), 'checked'=>1),
't.fk_city'=>array('label'=>$langs->trans("Fieldfk_city"), 'checked'=>1),
't.data_type'=>array('label'=>$langs->trans("Fielddata_type"), 'checked'=>1),
't.location'=>array('label'=>$langs->trans("Fieldlocation"), 'checked'=>1),
't.ref'=>array('label'=>$langs->trans("Fieldref"), 'checked'=>1),
't.version'=>array('label'=>$langs->trans("Fieldversion"), 'checked'=>1),
't.type_structure'=>array('label'=>$langs->trans("Fieldtype_structure"), 'checked'=>1),
't.title'=>array('label'=>$langs->trans("Fieldtitle"), 'checked'=>1),
't.description'=>array('label'=>$langs->trans("Fielddescription"), 'checked'=>1),
't.fk_user_creat'=>array('label'=>$langs->trans("Fieldfk_user_creat"), 'checked'=>1),
't.public'=>array('label'=>$langs->trans("Fieldpublic"), 'checked'=>1),
't.fk_statut'=>array('label'=>$langs->trans("Fieldfk_statut"), 'checked'=>1),
't.fk_opp_status'=>array('label'=>$langs->trans("Fieldfk_opp_status"), 'checked'=>1),
't.opp_percent'=>array('label'=>$langs->trans("Fieldopp_percent"), 'checked'=>1),
't.fk_user_close'=>array('label'=>$langs->trans("Fieldfk_user_close"), 'checked'=>1),
't.note_private'=>array('label'=>$langs->trans("Fieldnote_private"), 'checked'=>1),
't.note_public'=>array('label'=>$langs->trans("Fieldnote_public"), 'checked'=>1),
't.opp_amount'=>array('label'=>$langs->trans("Fieldopp_amount"), 'checked'=>1),
't.budget_amount'=>array('label'=>$langs->trans("Fieldbudget_amount"), 'checked'=>1),
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
$search_fk_soc='';
$search_fk_projet='';
$search_fk_calendar='';
$search_fk_country='';
$search_fk_departament='';
$search_fk_city='';
$search_data_type='';
$search_location='';
$search_ref='';
$search_version='';
$search_type_structure='';
$search_title='';
$search_description='';
$search_fk_user_creat='';
$search_public='';
$search_fk_statut='';
$search_fk_opp_status='';
$search_opp_percent='';
$search_fk_user_close='';
$search_note_private='';
$search_note_public='';
$search_opp_amount='';
$search_budget_amount='';
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
			header("Location: ".dol_buildpath('/budget/list.php',1));
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
		$sql .= " t.fk_soc,";
		$sql .= " t.fk_projet,";
		$sql .= " t.fk_calendar,";
		$sql .= " t.fk_country,";
		$sql .= " t.fk_departament,";
		$sql .= " t.fk_city,";
		$sql .= " t.data_type,";
		$sql .= " t.location,";
		$sql .= " t.datec,";
		$sql .= " t.tms,";
		$sql .= " t.dateo,";
		$sql .= " t.datee,";
		$sql .= " t.ref,";
		$sql .= " t.version,";
		$sql .= " t.type_structure,";
		$sql .= " t.title,";
		$sql .= " t.description,";
		$sql .= " t.fk_user_creat,";
		$sql .= " t.public,";
		$sql .= " t.fk_statut,";
		$sql .= " t.fk_opp_status,";
		$sql .= " t.opp_percent,";
		$sql .= " t.date_close,";
		$sql .= " t.fk_user_close,";
		$sql .= " t.note_private,";
		$sql .= " t.note_public,";
		$sql .= " t.opp_amount,";
		$sql .= " t.budget_amount,";
		$sql .= " t.model_pdf";


// Add fields for extrafields
foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."budget as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."budget_extrafields as ef on (u.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_entity) $sql.= natural_search("entity",$search_entity);
if ($search_fk_soc) $sql.= natural_search("fk_soc",$search_fk_soc);
if ($search_fk_projet) $sql.= natural_search("fk_projet",$search_fk_projet);
if ($search_fk_calendar) $sql.= natural_search("fk_calendar",$search_fk_calendar);
if ($search_fk_country) $sql.= natural_search("fk_country",$search_fk_country);
if ($search_fk_departament) $sql.= natural_search("fk_departament",$search_fk_departament);
if ($search_fk_city) $sql.= natural_search("fk_city",$search_fk_city);
if ($search_data_type) $sql.= natural_search("data_type",$search_data_type);
if ($search_location) $sql.= natural_search("location",$search_location);
if ($search_ref) $sql.= natural_search("ref",$search_ref);
if ($search_version) $sql.= natural_search("version",$search_version);
if ($search_type_structure) $sql.= natural_search("type_structure",$search_type_structure);
if ($search_title) $sql.= natural_search("title",$search_title);
if ($search_description) $sql.= natural_search("description",$search_description);
if ($search_fk_user_creat) $sql.= natural_search("fk_user_creat",$search_fk_user_creat);
if ($search_public) $sql.= natural_search("public",$search_public);
if ($search_fk_statut) $sql.= natural_search("fk_statut",$search_fk_statut);
if ($search_fk_opp_status) $sql.= natural_search("fk_opp_status",$search_fk_opp_status);
if ($search_opp_percent) $sql.= natural_search("opp_percent",$search_opp_percent);
if ($search_fk_user_close) $sql.= natural_search("fk_user_close",$search_fk_user_close);
if ($search_note_private) $sql.= natural_search("note_private",$search_note_private);
if ($search_note_public) $sql.= natural_search("note_public",$search_note_public);
if ($search_opp_amount) $sql.= natural_search("opp_amount",$search_opp_amount);
if ($search_budget_amount) $sql.= natural_search("budget_amount",$search_budget_amount);
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
if ($search_fk_soc != '') $params.= '&amp;search_fk_soc='.urlencode($search_fk_soc);
if ($search_fk_projet != '') $params.= '&amp;search_fk_projet='.urlencode($search_fk_projet);
if ($search_fk_calendar != '') $params.= '&amp;search_fk_calendar='.urlencode($search_fk_calendar);
if ($search_fk_country != '') $params.= '&amp;search_fk_country='.urlencode($search_fk_country);
if ($search_fk_departament != '') $params.= '&amp;search_fk_departament='.urlencode($search_fk_departament);
if ($search_fk_city != '') $params.= '&amp;search_fk_city='.urlencode($search_fk_city);
if ($search_data_type != '') $params.= '&amp;search_data_type='.urlencode($search_data_type);
if ($search_location != '') $params.= '&amp;search_location='.urlencode($search_location);
if ($search_ref != '') $params.= '&amp;search_ref='.urlencode($search_ref);
if ($search_version != '') $params.= '&amp;search_version='.urlencode($search_version);
if ($search_type_structure != '') $params.= '&amp;search_type_structure='.urlencode($search_type_structure);
if ($search_title != '') $params.= '&amp;search_title='.urlencode($search_title);
if ($search_description != '') $params.= '&amp;search_description='.urlencode($search_description);
if ($search_fk_user_creat != '') $params.= '&amp;search_fk_user_creat='.urlencode($search_fk_user_creat);
if ($search_public != '') $params.= '&amp;search_public='.urlencode($search_public);
if ($search_fk_statut != '') $params.= '&amp;search_fk_statut='.urlencode($search_fk_statut);
if ($search_fk_opp_status != '') $params.= '&amp;search_fk_opp_status='.urlencode($search_fk_opp_status);
if ($search_opp_percent != '') $params.= '&amp;search_opp_percent='.urlencode($search_opp_percent);
if ($search_fk_user_close != '') $params.= '&amp;search_fk_user_close='.urlencode($search_fk_user_close);
if ($search_note_private != '') $params.= '&amp;search_note_private='.urlencode($search_note_private);
if ($search_note_public != '') $params.= '&amp;search_note_public='.urlencode($search_note_public);
if ($search_opp_amount != '') $params.= '&amp;search_opp_amount='.urlencode($search_opp_amount);
if ($search_budget_amount != '') $params.= '&amp;search_budget_amount='.urlencode($search_budget_amount);
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
if (! empty($arrayfields['t.fk_soc']['checked'])) print_liste_field_titre($arrayfields['t.fk_soc']['label'],$_SERVER['PHP_SELF'],'t.fk_soc','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_projet']['checked'])) print_liste_field_titre($arrayfields['t.fk_projet']['label'],$_SERVER['PHP_SELF'],'t.fk_projet','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_calendar']['checked'])) print_liste_field_titre($arrayfields['t.fk_calendar']['label'],$_SERVER['PHP_SELF'],'t.fk_calendar','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_country']['checked'])) print_liste_field_titre($arrayfields['t.fk_country']['label'],$_SERVER['PHP_SELF'],'t.fk_country','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_departament']['checked'])) print_liste_field_titre($arrayfields['t.fk_departament']['label'],$_SERVER['PHP_SELF'],'t.fk_departament','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_city']['checked'])) print_liste_field_titre($arrayfields['t.fk_city']['label'],$_SERVER['PHP_SELF'],'t.fk_city','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.data_type']['checked'])) print_liste_field_titre($arrayfields['t.data_type']['label'],$_SERVER['PHP_SELF'],'t.data_type','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.location']['checked'])) print_liste_field_titre($arrayfields['t.location']['label'],$_SERVER['PHP_SELF'],'t.location','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.ref']['checked'])) print_liste_field_titre($arrayfields['t.ref']['label'],$_SERVER['PHP_SELF'],'t.ref','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.version']['checked'])) print_liste_field_titre($arrayfields['t.version']['label'],$_SERVER['PHP_SELF'],'t.version','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.type_structure']['checked'])) print_liste_field_titre($arrayfields['t.type_structure']['label'],$_SERVER['PHP_SELF'],'t.type_structure','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.title']['checked'])) print_liste_field_titre($arrayfields['t.title']['label'],$_SERVER['PHP_SELF'],'t.title','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.description']['checked'])) print_liste_field_titre($arrayfields['t.description']['label'],$_SERVER['PHP_SELF'],'t.description','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_creat']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_creat']['label'],$_SERVER['PHP_SELF'],'t.fk_user_creat','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.public']['checked'])) print_liste_field_titre($arrayfields['t.public']['label'],$_SERVER['PHP_SELF'],'t.public','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_statut']['checked'])) print_liste_field_titre($arrayfields['t.fk_statut']['label'],$_SERVER['PHP_SELF'],'t.fk_statut','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_opp_status']['checked'])) print_liste_field_titre($arrayfields['t.fk_opp_status']['label'],$_SERVER['PHP_SELF'],'t.fk_opp_status','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.opp_percent']['checked'])) print_liste_field_titre($arrayfields['t.opp_percent']['label'],$_SERVER['PHP_SELF'],'t.opp_percent','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_close']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_close']['label'],$_SERVER['PHP_SELF'],'t.fk_user_close','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.note_private']['checked'])) print_liste_field_titre($arrayfields['t.note_private']['label'],$_SERVER['PHP_SELF'],'t.note_private','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.note_public']['checked'])) print_liste_field_titre($arrayfields['t.note_public']['label'],$_SERVER['PHP_SELF'],'t.note_public','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.opp_amount']['checked'])) print_liste_field_titre($arrayfields['t.opp_amount']['label'],$_SERVER['PHP_SELF'],'t.opp_amount','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.budget_amount']['checked'])) print_liste_field_titre($arrayfields['t.budget_amount']['label'],$_SERVER['PHP_SELF'],'t.budget_amount','',$params,'',$sortfield,$sortorder);
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
if (! empty($arrayfields['t.fk_soc']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_soc" value="'.$search_fk_soc.'" size="10"></td>';
if (! empty($arrayfields['t.fk_projet']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_projet" value="'.$search_fk_projet.'" size="10"></td>';
if (! empty($arrayfields['t.fk_calendar']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_calendar" value="'.$search_fk_calendar.'" size="10"></td>';
if (! empty($arrayfields['t.fk_country']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_country" value="'.$search_fk_country.'" size="10"></td>';
if (! empty($arrayfields['t.fk_departament']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_departament" value="'.$search_fk_departament.'" size="10"></td>';
if (! empty($arrayfields['t.fk_city']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_city" value="'.$search_fk_city.'" size="10"></td>';
if (! empty($arrayfields['t.data_type']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_data_type" value="'.$search_data_type.'" size="10"></td>';
if (! empty($arrayfields['t.location']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_location" value="'.$search_location.'" size="10"></td>';
if (! empty($arrayfields['t.ref']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
if (! empty($arrayfields['t.version']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_version" value="'.$search_version.'" size="10"></td>';
if (! empty($arrayfields['t.type_structure']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_type_structure" value="'.$search_type_structure.'" size="10"></td>';
if (! empty($arrayfields['t.title']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_title" value="'.$search_title.'" size="10"></td>';
if (! empty($arrayfields['t.description']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_description" value="'.$search_description.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_creat']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_creat" value="'.$search_fk_user_creat.'" size="10"></td>';
if (! empty($arrayfields['t.public']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_public" value="'.$search_public.'" size="10"></td>';
if (! empty($arrayfields['t.fk_statut']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_statut" value="'.$search_fk_statut.'" size="10"></td>';
if (! empty($arrayfields['t.fk_opp_status']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_opp_status" value="'.$search_fk_opp_status.'" size="10"></td>';
if (! empty($arrayfields['t.opp_percent']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_opp_percent" value="'.$search_opp_percent.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_close']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_close" value="'.$search_fk_user_close.'" size="10"></td>';
if (! empty($arrayfields['t.note_private']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_note_private" value="'.$search_note_private.'" size="10"></td>';
if (! empty($arrayfields['t.note_public']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_note_public" value="'.$search_note_public.'" size="10"></td>';
if (! empty($arrayfields['t.opp_amount']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_opp_amount" value="'.$search_opp_amount.'" size="10"></td>';
if (! empty($arrayfields['t.budget_amount']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_budget_amount" value="'.$search_budget_amount.'" size="10"></td>';
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
