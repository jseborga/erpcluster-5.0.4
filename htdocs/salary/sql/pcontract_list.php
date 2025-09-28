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
 *   	\file       salary/pcontract_list.php
 *		\ingroup    salary
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-12-01 11:06
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
dol_include_once('/salary/class/pcontract.class.php');

// Load traductions files requiredby by page
$langs->load("salary");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_ref=GETPOST('search_ref','alpha');
$search_fk_user=GETPOST('search_fk_user','int');
$search_fk_departament=GETPOST('search_fk_departament','int');
$search_fk_charge=GETPOST('search_fk_charge','int');
$search_fk_regional=GETPOST('search_fk_regional','int');
$search_fk_proces=GETPOST('search_fk_proces','int');
$search_fk_cc=GETPOST('search_fk_cc','int');
$search_fk_account=GETPOST('search_fk_account','int');
$search_fk_unit=GETPOST('search_fk_unit','int');
$search_basic=GETPOST('search_basic','alpha');
$search_basic_fixed=GETPOST('search_basic_fixed','alpha');
$search_nivel=GETPOST('search_nivel','alpha');
$search_bonus_old=GETPOST('search_bonus_old','int');
$search_hours=GETPOST('search_hours','int');
$search_nua_afp=GETPOST('search_nua_afp','int');
$search_afp=GETPOST('search_afp','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_state=GETPOST('search_state','int');


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
$hookmanager->initHooks(array('pcontractlist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('salary');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Load object if id or ref is provided as parameter
$object=new Pcontract($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Definition of fields for list
$arrayfields=array(
    
't.ref'=>array('label'=>$langs->trans("Fieldref"), 'checked'=>1),
't.fk_user'=>array('label'=>$langs->trans("Fieldfk_user"), 'checked'=>1),
't.fk_departament'=>array('label'=>$langs->trans("Fieldfk_departament"), 'checked'=>1),
't.fk_charge'=>array('label'=>$langs->trans("Fieldfk_charge"), 'checked'=>1),
't.fk_regional'=>array('label'=>$langs->trans("Fieldfk_regional"), 'checked'=>1),
't.fk_proces'=>array('label'=>$langs->trans("Fieldfk_proces"), 'checked'=>1),
't.fk_cc'=>array('label'=>$langs->trans("Fieldfk_cc"), 'checked'=>1),
't.fk_account'=>array('label'=>$langs->trans("Fieldfk_account"), 'checked'=>1),
't.fk_unit'=>array('label'=>$langs->trans("Fieldfk_unit"), 'checked'=>1),
't.basic'=>array('label'=>$langs->trans("Fieldbasic"), 'checked'=>1),
't.basic_fixed'=>array('label'=>$langs->trans("Fieldbasic_fixed"), 'checked'=>1),
't.nivel'=>array('label'=>$langs->trans("Fieldnivel"), 'checked'=>1),
't.bonus_old'=>array('label'=>$langs->trans("Fieldbonus_old"), 'checked'=>1),
't.hours'=>array('label'=>$langs->trans("Fieldhours"), 'checked'=>1),
't.nua_afp'=>array('label'=>$langs->trans("Fieldnua_afp"), 'checked'=>1),
't.afp'=>array('label'=>$langs->trans("Fieldafp"), 'checked'=>1),
't.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'checked'=>1),
't.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'checked'=>1),
't.state'=>array('label'=>$langs->trans("Fieldstate"), 'checked'=>1),

    
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
	
$search_ref='';
$search_fk_user='';
$search_fk_departament='';
$search_fk_charge='';
$search_fk_regional='';
$search_fk_proces='';
$search_fk_cc='';
$search_fk_account='';
$search_fk_unit='';
$search_basic='';
$search_basic_fixed='';
$search_nivel='';
$search_bonus_old='';
$search_hours='';
$search_nua_afp='';
$search_afp='';
$search_fk_user_create='';
$search_fk_user_mod='';
$search_state='';

	
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
			header("Location: ".dol_buildpath('/salary/list.php',1));
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

		$sql .= " t.ref,";
		$sql .= " t.fk_user,";
		$sql .= " t.fk_departament,";
		$sql .= " t.fk_charge,";
		$sql .= " t.fk_regional,";
		$sql .= " t.fk_proces,";
		$sql .= " t.fk_cc,";
		$sql .= " t.fk_account,";
		$sql .= " t.fk_unit,";
		$sql .= " t.date_ini,";
		$sql .= " t.date_fin,";
		$sql .= " t.basic,";
		$sql .= " t.basic_fixed,";
		$sql .= " t.nivel,";
		$sql .= " t.bonus_old,";
		$sql .= " t.hours,";
		$sql .= " t.nua_afp,";
		$sql .= " t.afp,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.date_create,";
		$sql .= " t.date_mod,";
		$sql .= " t.tms,";
		$sql .= " t.state";


// Add fields for extrafields
foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."p_contract as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."p_contract_extrafields as ef on (u.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_ref) $sql.= natural_search("ref",$search_ref);
if ($search_fk_user) $sql.= natural_search("fk_user",$search_fk_user);
if ($search_fk_departament) $sql.= natural_search("fk_departament",$search_fk_departament);
if ($search_fk_charge) $sql.= natural_search("fk_charge",$search_fk_charge);
if ($search_fk_regional) $sql.= natural_search("fk_regional",$search_fk_regional);
if ($search_fk_proces) $sql.= natural_search("fk_proces",$search_fk_proces);
if ($search_fk_cc) $sql.= natural_search("fk_cc",$search_fk_cc);
if ($search_fk_account) $sql.= natural_search("fk_account",$search_fk_account);
if ($search_fk_unit) $sql.= natural_search("fk_unit",$search_fk_unit);
if ($search_basic) $sql.= natural_search("basic",$search_basic);
if ($search_basic_fixed) $sql.= natural_search("basic_fixed",$search_basic_fixed);
if ($search_nivel) $sql.= natural_search("nivel",$search_nivel);
if ($search_bonus_old) $sql.= natural_search("bonus_old",$search_bonus_old);
if ($search_hours) $sql.= natural_search("hours",$search_hours);
if ($search_nua_afp) $sql.= natural_search("nua_afp",$search_nua_afp);
if ($search_afp) $sql.= natural_search("afp",$search_afp);
if ($search_fk_user_create) $sql.= natural_search("fk_user_create",$search_fk_user_create);
if ($search_fk_user_mod) $sql.= natural_search("fk_user_mod",$search_fk_user_mod);
if ($search_state) $sql.= natural_search("state",$search_state);


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
    
if ($search_ref != '') $params.= '&amp;search_ref='.urlencode($search_ref);
if ($search_fk_user != '') $params.= '&amp;search_fk_user='.urlencode($search_fk_user);
if ($search_fk_departament != '') $params.= '&amp;search_fk_departament='.urlencode($search_fk_departament);
if ($search_fk_charge != '') $params.= '&amp;search_fk_charge='.urlencode($search_fk_charge);
if ($search_fk_regional != '') $params.= '&amp;search_fk_regional='.urlencode($search_fk_regional);
if ($search_fk_proces != '') $params.= '&amp;search_fk_proces='.urlencode($search_fk_proces);
if ($search_fk_cc != '') $params.= '&amp;search_fk_cc='.urlencode($search_fk_cc);
if ($search_fk_account != '') $params.= '&amp;search_fk_account='.urlencode($search_fk_account);
if ($search_fk_unit != '') $params.= '&amp;search_fk_unit='.urlencode($search_fk_unit);
if ($search_basic != '') $params.= '&amp;search_basic='.urlencode($search_basic);
if ($search_basic_fixed != '') $params.= '&amp;search_basic_fixed='.urlencode($search_basic_fixed);
if ($search_nivel != '') $params.= '&amp;search_nivel='.urlencode($search_nivel);
if ($search_bonus_old != '') $params.= '&amp;search_bonus_old='.urlencode($search_bonus_old);
if ($search_hours != '') $params.= '&amp;search_hours='.urlencode($search_hours);
if ($search_nua_afp != '') $params.= '&amp;search_nua_afp='.urlencode($search_nua_afp);
if ($search_afp != '') $params.= '&amp;search_afp='.urlencode($search_afp);
if ($search_fk_user_create != '') $params.= '&amp;search_fk_user_create='.urlencode($search_fk_user_create);
if ($search_fk_user_mod != '') $params.= '&amp;search_fk_user_mod='.urlencode($search_fk_user_mod);
if ($search_state != '') $params.= '&amp;search_state='.urlencode($search_state);

	
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
if (! empty($arrayfields['t.ref']['checked'])) print_liste_field_titre($arrayfields['t.ref']['label'],$_SERVER['PHP_SELF'],'t.ref','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user']['checked'])) print_liste_field_titre($arrayfields['t.fk_user']['label'],$_SERVER['PHP_SELF'],'t.fk_user','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_departament']['checked'])) print_liste_field_titre($arrayfields['t.fk_departament']['label'],$_SERVER['PHP_SELF'],'t.fk_departament','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_charge']['checked'])) print_liste_field_titre($arrayfields['t.fk_charge']['label'],$_SERVER['PHP_SELF'],'t.fk_charge','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_regional']['checked'])) print_liste_field_titre($arrayfields['t.fk_regional']['label'],$_SERVER['PHP_SELF'],'t.fk_regional','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_proces']['checked'])) print_liste_field_titre($arrayfields['t.fk_proces']['label'],$_SERVER['PHP_SELF'],'t.fk_proces','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_cc']['checked'])) print_liste_field_titre($arrayfields['t.fk_cc']['label'],$_SERVER['PHP_SELF'],'t.fk_cc','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_account']['checked'])) print_liste_field_titre($arrayfields['t.fk_account']['label'],$_SERVER['PHP_SELF'],'t.fk_account','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_unit']['checked'])) print_liste_field_titre($arrayfields['t.fk_unit']['label'],$_SERVER['PHP_SELF'],'t.fk_unit','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.basic']['checked'])) print_liste_field_titre($arrayfields['t.basic']['label'],$_SERVER['PHP_SELF'],'t.basic','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.basic_fixed']['checked'])) print_liste_field_titre($arrayfields['t.basic_fixed']['label'],$_SERVER['PHP_SELF'],'t.basic_fixed','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.nivel']['checked'])) print_liste_field_titre($arrayfields['t.nivel']['label'],$_SERVER['PHP_SELF'],'t.nivel','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.bonus_old']['checked'])) print_liste_field_titre($arrayfields['t.bonus_old']['label'],$_SERVER['PHP_SELF'],'t.bonus_old','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.hours']['checked'])) print_liste_field_titre($arrayfields['t.hours']['label'],$_SERVER['PHP_SELF'],'t.hours','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.nua_afp']['checked'])) print_liste_field_titre($arrayfields['t.nua_afp']['label'],$_SERVER['PHP_SELF'],'t.nua_afp','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.afp']['checked'])) print_liste_field_titre($arrayfields['t.afp']['label'],$_SERVER['PHP_SELF'],'t.afp','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_create']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_create']['label'],$_SERVER['PHP_SELF'],'t.fk_user_create','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_mod']['label'],$_SERVER['PHP_SELF'],'t.fk_user_mod','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.state']['checked'])) print_liste_field_titre($arrayfields['t.state']['label'],$_SERVER['PHP_SELF'],'t.state','',$params,'',$sortfield,$sortorder);

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
if (! empty($arrayfields['t.ref']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user" value="'.$search_fk_user.'" size="10"></td>';
if (! empty($arrayfields['t.fk_departament']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_departament" value="'.$search_fk_departament.'" size="10"></td>';
if (! empty($arrayfields['t.fk_charge']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_charge" value="'.$search_fk_charge.'" size="10"></td>';
if (! empty($arrayfields['t.fk_regional']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_regional" value="'.$search_fk_regional.'" size="10"></td>';
if (! empty($arrayfields['t.fk_proces']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_proces" value="'.$search_fk_proces.'" size="10"></td>';
if (! empty($arrayfields['t.fk_cc']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_cc" value="'.$search_fk_cc.'" size="10"></td>';
if (! empty($arrayfields['t.fk_account']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_account" value="'.$search_fk_account.'" size="10"></td>';
if (! empty($arrayfields['t.fk_unit']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_unit" value="'.$search_fk_unit.'" size="10"></td>';
if (! empty($arrayfields['t.basic']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_basic" value="'.$search_basic.'" size="10"></td>';
if (! empty($arrayfields['t.basic_fixed']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_basic_fixed" value="'.$search_basic_fixed.'" size="10"></td>';
if (! empty($arrayfields['t.nivel']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_nivel" value="'.$search_nivel.'" size="10"></td>';
if (! empty($arrayfields['t.bonus_old']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_bonus_old" value="'.$search_bonus_old.'" size="10"></td>';
if (! empty($arrayfields['t.hours']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_hours" value="'.$search_hours.'" size="10"></td>';
if (! empty($arrayfields['t.nua_afp']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_nua_afp" value="'.$search_nua_afp.'" size="10"></td>';
if (! empty($arrayfields['t.afp']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_afp" value="'.$search_afp.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_create']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_create" value="'.$search_fk_user_create.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_mod" value="'.$search_fk_user_mod.'" size="10"></td>';
if (! empty($arrayfields['t.state']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_state" value="'.$search_state.'" size="10"></td>';

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
