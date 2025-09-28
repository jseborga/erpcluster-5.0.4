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
 *   	\file       contab/contabvision_list.php
 *		\ingroup    contab
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-11-08 09:05
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
dol_include_once('/contab/class/contabvision.class.php');

// Load traductions files requiredby by page
$langs->load("contab");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_sequence=GETPOST('search_sequence','int');
$search_account=GETPOST('search_account','alpha');
$search_account_sup=GETPOST('search_account_sup','alpha');
$search_detail_managment=GETPOST('search_detail_managment','alpha');
$search_cta_normal=GETPOST('search_cta_normal','alpha');
$search_cta_column=GETPOST('search_cta_column','int');
$search_cta_class=GETPOST('search_cta_class','int');
$search_cta_identifier=GETPOST('search_cta_identifier','alpha');
$search_cta_operation=GETPOST('search_cta_operation','int');
$search_cta_balances=GETPOST('search_cta_balances','int');
$search_cta_totalvis=GETPOST('search_cta_totalvis','int');
$search_name_vision=GETPOST('search_name_vision','alpha');
$search_line=GETPOST('search_line','alpha');
$search_fk_accountini=GETPOST('search_fk_accountini','int');
$search_fk_accountfin=GETPOST('search_fk_accountfin','int');
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
$hookmanager->initHooks(array('contabvisionlist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('contab');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Load object if id or ref is provided as parameter
$object=new Contabvision($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Definition of fields for list
$arrayfields=array(
    
't.entity'=>array('label'=>$langs->trans("Fieldentity"), 'checked'=>1),
't.ref'=>array('label'=>$langs->trans("Fieldref"), 'checked'=>1),
't.sequence'=>array('label'=>$langs->trans("Fieldsequence"), 'checked'=>1),
't.account'=>array('label'=>$langs->trans("Fieldaccount"), 'checked'=>1),
't.account_sup'=>array('label'=>$langs->trans("Fieldaccount_sup"), 'checked'=>1),
't.detail_managment'=>array('label'=>$langs->trans("Fielddetail_managment"), 'checked'=>1),
't.cta_normal'=>array('label'=>$langs->trans("Fieldcta_normal"), 'checked'=>1),
't.cta_column'=>array('label'=>$langs->trans("Fieldcta_column"), 'checked'=>1),
't.cta_class'=>array('label'=>$langs->trans("Fieldcta_class"), 'checked'=>1),
't.cta_identifier'=>array('label'=>$langs->trans("Fieldcta_identifier"), 'checked'=>1),
't.cta_operation'=>array('label'=>$langs->trans("Fieldcta_operation"), 'checked'=>1),
't.cta_balances'=>array('label'=>$langs->trans("Fieldcta_balances"), 'checked'=>1),
't.cta_totalvis'=>array('label'=>$langs->trans("Fieldcta_totalvis"), 'checked'=>1),
't.name_vision'=>array('label'=>$langs->trans("Fieldname_vision"), 'checked'=>1),
't.line'=>array('label'=>$langs->trans("Fieldline"), 'checked'=>1),
't.fk_accountini'=>array('label'=>$langs->trans("Fieldfk_accountini"), 'checked'=>1),
't.fk_accountfin'=>array('label'=>$langs->trans("Fieldfk_accountfin"), 'checked'=>1),
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
	
$search_entity='';
$search_ref='';
$search_sequence='';
$search_account='';
$search_account_sup='';
$search_detail_managment='';
$search_cta_normal='';
$search_cta_column='';
$search_cta_class='';
$search_cta_identifier='';
$search_cta_operation='';
$search_cta_balances='';
$search_cta_totalvis='';
$search_name_vision='';
$search_line='';
$search_fk_accountini='';
$search_fk_accountfin='';
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
			header("Location: ".dol_buildpath('/contab/list.php',1));
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
		$sql .= " t.sequence,";
		$sql .= " t.account,";
		$sql .= " t.account_sup,";
		$sql .= " t.detail_managment,";
		$sql .= " t.cta_normal,";
		$sql .= " t.cta_column,";
		$sql .= " t.cta_class,";
		$sql .= " t.cta_identifier,";
		$sql .= " t.cta_operation,";
		$sql .= " t.cta_balances,";
		$sql .= " t.cta_totalvis,";
		$sql .= " t.name_vision,";
		$sql .= " t.line,";
		$sql .= " t.fk_accountini,";
		$sql .= " t.fk_accountfin,";
		$sql .= " t.statut";


// Add fields for extrafields
foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."contab_vision as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."contab_vision_extrafields as ef on (u.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_entity) $sql.= natural_search("entity",$search_entity);
if ($search_ref) $sql.= natural_search("ref",$search_ref);
if ($search_sequence) $sql.= natural_search("sequence",$search_sequence);
if ($search_account) $sql.= natural_search("account",$search_account);
if ($search_account_sup) $sql.= natural_search("account_sup",$search_account_sup);
if ($search_detail_managment) $sql.= natural_search("detail_managment",$search_detail_managment);
if ($search_cta_normal) $sql.= natural_search("cta_normal",$search_cta_normal);
if ($search_cta_column) $sql.= natural_search("cta_column",$search_cta_column);
if ($search_cta_class) $sql.= natural_search("cta_class",$search_cta_class);
if ($search_cta_identifier) $sql.= natural_search("cta_identifier",$search_cta_identifier);
if ($search_cta_operation) $sql.= natural_search("cta_operation",$search_cta_operation);
if ($search_cta_balances) $sql.= natural_search("cta_balances",$search_cta_balances);
if ($search_cta_totalvis) $sql.= natural_search("cta_totalvis",$search_cta_totalvis);
if ($search_name_vision) $sql.= natural_search("name_vision",$search_name_vision);
if ($search_line) $sql.= natural_search("line",$search_line);
if ($search_fk_accountini) $sql.= natural_search("fk_accountini",$search_fk_accountini);
if ($search_fk_accountfin) $sql.= natural_search("fk_accountfin",$search_fk_accountfin);
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
    
if ($search_entity != '') $params.= '&amp;search_entity='.urlencode($search_entity);
if ($search_ref != '') $params.= '&amp;search_ref='.urlencode($search_ref);
if ($search_sequence != '') $params.= '&amp;search_sequence='.urlencode($search_sequence);
if ($search_account != '') $params.= '&amp;search_account='.urlencode($search_account);
if ($search_account_sup != '') $params.= '&amp;search_account_sup='.urlencode($search_account_sup);
if ($search_detail_managment != '') $params.= '&amp;search_detail_managment='.urlencode($search_detail_managment);
if ($search_cta_normal != '') $params.= '&amp;search_cta_normal='.urlencode($search_cta_normal);
if ($search_cta_column != '') $params.= '&amp;search_cta_column='.urlencode($search_cta_column);
if ($search_cta_class != '') $params.= '&amp;search_cta_class='.urlencode($search_cta_class);
if ($search_cta_identifier != '') $params.= '&amp;search_cta_identifier='.urlencode($search_cta_identifier);
if ($search_cta_operation != '') $params.= '&amp;search_cta_operation='.urlencode($search_cta_operation);
if ($search_cta_balances != '') $params.= '&amp;search_cta_balances='.urlencode($search_cta_balances);
if ($search_cta_totalvis != '') $params.= '&amp;search_cta_totalvis='.urlencode($search_cta_totalvis);
if ($search_name_vision != '') $params.= '&amp;search_name_vision='.urlencode($search_name_vision);
if ($search_line != '') $params.= '&amp;search_line='.urlencode($search_line);
if ($search_fk_accountini != '') $params.= '&amp;search_fk_accountini='.urlencode($search_fk_accountini);
if ($search_fk_accountfin != '') $params.= '&amp;search_fk_accountfin='.urlencode($search_fk_accountfin);
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
if (! empty($arrayfields['t.sequence']['checked'])) print_liste_field_titre($arrayfields['t.sequence']['label'],$_SERVER['PHP_SELF'],'t.sequence','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.account']['checked'])) print_liste_field_titre($arrayfields['t.account']['label'],$_SERVER['PHP_SELF'],'t.account','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.account_sup']['checked'])) print_liste_field_titre($arrayfields['t.account_sup']['label'],$_SERVER['PHP_SELF'],'t.account_sup','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.detail_managment']['checked'])) print_liste_field_titre($arrayfields['t.detail_managment']['label'],$_SERVER['PHP_SELF'],'t.detail_managment','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.cta_normal']['checked'])) print_liste_field_titre($arrayfields['t.cta_normal']['label'],$_SERVER['PHP_SELF'],'t.cta_normal','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.cta_column']['checked'])) print_liste_field_titre($arrayfields['t.cta_column']['label'],$_SERVER['PHP_SELF'],'t.cta_column','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.cta_class']['checked'])) print_liste_field_titre($arrayfields['t.cta_class']['label'],$_SERVER['PHP_SELF'],'t.cta_class','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.cta_identifier']['checked'])) print_liste_field_titre($arrayfields['t.cta_identifier']['label'],$_SERVER['PHP_SELF'],'t.cta_identifier','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.cta_operation']['checked'])) print_liste_field_titre($arrayfields['t.cta_operation']['label'],$_SERVER['PHP_SELF'],'t.cta_operation','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.cta_balances']['checked'])) print_liste_field_titre($arrayfields['t.cta_balances']['label'],$_SERVER['PHP_SELF'],'t.cta_balances','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.cta_totalvis']['checked'])) print_liste_field_titre($arrayfields['t.cta_totalvis']['label'],$_SERVER['PHP_SELF'],'t.cta_totalvis','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.name_vision']['checked'])) print_liste_field_titre($arrayfields['t.name_vision']['label'],$_SERVER['PHP_SELF'],'t.name_vision','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.line']['checked'])) print_liste_field_titre($arrayfields['t.line']['label'],$_SERVER['PHP_SELF'],'t.line','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_accountini']['checked'])) print_liste_field_titre($arrayfields['t.fk_accountini']['label'],$_SERVER['PHP_SELF'],'t.fk_accountini','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_accountfin']['checked'])) print_liste_field_titre($arrayfields['t.fk_accountfin']['label'],$_SERVER['PHP_SELF'],'t.fk_accountfin','',$params,'',$sortfield,$sortorder);
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
if (! empty($arrayfields['t.entity']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_entity" value="'.$search_entity.'" size="10"></td>';
if (! empty($arrayfields['t.ref']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
if (! empty($arrayfields['t.sequence']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_sequence" value="'.$search_sequence.'" size="10"></td>';
if (! empty($arrayfields['t.account']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_account" value="'.$search_account.'" size="10"></td>';
if (! empty($arrayfields['t.account_sup']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_account_sup" value="'.$search_account_sup.'" size="10"></td>';
if (! empty($arrayfields['t.detail_managment']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_detail_managment" value="'.$search_detail_managment.'" size="10"></td>';
if (! empty($arrayfields['t.cta_normal']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_cta_normal" value="'.$search_cta_normal.'" size="10"></td>';
if (! empty($arrayfields['t.cta_column']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_cta_column" value="'.$search_cta_column.'" size="10"></td>';
if (! empty($arrayfields['t.cta_class']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_cta_class" value="'.$search_cta_class.'" size="10"></td>';
if (! empty($arrayfields['t.cta_identifier']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_cta_identifier" value="'.$search_cta_identifier.'" size="10"></td>';
if (! empty($arrayfields['t.cta_operation']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_cta_operation" value="'.$search_cta_operation.'" size="10"></td>';
if (! empty($arrayfields['t.cta_balances']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_cta_balances" value="'.$search_cta_balances.'" size="10"></td>';
if (! empty($arrayfields['t.cta_totalvis']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_cta_totalvis" value="'.$search_cta_totalvis.'" size="10"></td>';
if (! empty($arrayfields['t.name_vision']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_name_vision" value="'.$search_name_vision.'" size="10"></td>';
if (! empty($arrayfields['t.line']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_line" value="'.$search_line.'" size="10"></td>';
if (! empty($arrayfields['t.fk_accountini']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_accountini" value="'.$search_fk_accountini.'" size="10"></td>';
if (! empty($arrayfields['t.fk_accountfin']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_accountfin" value="'.$search_fk_accountfin.'" size="10"></td>';
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
