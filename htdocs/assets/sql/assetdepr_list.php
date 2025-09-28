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
 *   	\file       asset/assetdepr_list.php
 *		\ingroup    asset
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-01-24 09:46
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
dol_include_once('/asset/class/assetdepr.class.php');

// Load traductions files requiredby by page
$langs->load("asset");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_fk_asset=GETPOST('search_fk_asset','int');
$search_ref=GETPOST('search_ref','alpha');
$search_factor_update=GETPOST('search_factor_update','alpha');
$search_time_consumed=GETPOST('search_time_consumed','alpha');
$search_amount_update=GETPOST('search_amount_update','alpha');
$search_amount_depr=GETPOST('search_amount_depr','alpha');
$search_amount_depr_acum=GETPOST('search_amount_depr_acum','alpha');
$search_amount_depr_acum_update=GETPOST('search_amount_depr_acum_update','alpha');
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
$hookmanager->initHooks(array('assetdeprlist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('asset');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Load object if id or ref is provided as parameter
$object=new Assetdepr($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Definition of fields for list
$arrayfields=array(
    
't.fk_asset'=>array('label'=>$langs->trans("Fieldfk_asset"), 'checked'=>1),
't.ref'=>array('label'=>$langs->trans("Fieldref"), 'checked'=>1),
't.factor_update'=>array('label'=>$langs->trans("Fieldfactor_update"), 'checked'=>1),
't.time_consumed'=>array('label'=>$langs->trans("Fieldtime_consumed"), 'checked'=>1),
't.amount_update'=>array('label'=>$langs->trans("Fieldamount_update"), 'checked'=>1),
't.amount_depr'=>array('label'=>$langs->trans("Fieldamount_depr"), 'checked'=>1),
't.amount_depr_acum'=>array('label'=>$langs->trans("Fieldamount_depr_acum"), 'checked'=>1),
't.amount_depr_acum_update'=>array('label'=>$langs->trans("Fieldamount_depr_acum_update"), 'checked'=>1),
't.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'checked'=>1),
't.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'checked'=>1),
't.status'=>array('label'=>$langs->trans("Fieldstatus"), 'checked'=>1),

    
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
	
$search_fk_asset='';
$search_ref='';
$search_factor_update='';
$search_time_consumed='';
$search_amount_update='';
$search_amount_depr='';
$search_amount_depr_acum='';
$search_amount_depr_acum_update='';
$search_fk_user_create='';
$search_fk_user_mod='';
$search_status='';

	
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
			header("Location: ".dol_buildpath('/asset/list.php',1));
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

		$sql .= " t.fk_asset,";
		$sql .= " t.ref,";
		$sql .= " t.date_ini,";
		$sql .= " t.date_end,";
		$sql .= " t.factor_update,";
		$sql .= " t.time_consumed,";
		$sql .= " t.amount_update,";
		$sql .= " t.amount_depr,";
		$sql .= " t.amount_depr_acum,";
		$sql .= " t.amount_depr_acum_update,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.datec,";
		$sql .= " t.dateu,";
		$sql .= " t.tms,";
		$sql .= " t.status";


// Add fields for extrafields
foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."asset_depr as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."asset_depr_extrafields as ef on (u.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_fk_asset) $sql.= natural_search("fk_asset",$search_fk_asset);
if ($search_ref) $sql.= natural_search("ref",$search_ref);
if ($search_factor_update) $sql.= natural_search("factor_update",$search_factor_update);
if ($search_time_consumed) $sql.= natural_search("time_consumed",$search_time_consumed);
if ($search_amount_update) $sql.= natural_search("amount_update",$search_amount_update);
if ($search_amount_depr) $sql.= natural_search("amount_depr",$search_amount_depr);
if ($search_amount_depr_acum) $sql.= natural_search("amount_depr_acum",$search_amount_depr_acum);
if ($search_amount_depr_acum_update) $sql.= natural_search("amount_depr_acum_update",$search_amount_depr_acum_update);
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
    
if ($search_fk_asset != '') $params.= '&amp;search_fk_asset='.urlencode($search_fk_asset);
if ($search_ref != '') $params.= '&amp;search_ref='.urlencode($search_ref);
if ($search_factor_update != '') $params.= '&amp;search_factor_update='.urlencode($search_factor_update);
if ($search_time_consumed != '') $params.= '&amp;search_time_consumed='.urlencode($search_time_consumed);
if ($search_amount_update != '') $params.= '&amp;search_amount_update='.urlencode($search_amount_update);
if ($search_amount_depr != '') $params.= '&amp;search_amount_depr='.urlencode($search_amount_depr);
if ($search_amount_depr_acum != '') $params.= '&amp;search_amount_depr_acum='.urlencode($search_amount_depr_acum);
if ($search_amount_depr_acum_update != '') $params.= '&amp;search_amount_depr_acum_update='.urlencode($search_amount_depr_acum_update);
if ($search_fk_user_create != '') $params.= '&amp;search_fk_user_create='.urlencode($search_fk_user_create);
if ($search_fk_user_mod != '') $params.= '&amp;search_fk_user_mod='.urlencode($search_fk_user_mod);
if ($search_status != '') $params.= '&amp;search_status='.urlencode($search_status);

	
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
if (! empty($arrayfields['t.fk_asset']['checked'])) print_liste_field_titre($arrayfields['t.fk_asset']['label'],$_SERVER['PHP_SELF'],'t.fk_asset','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.ref']['checked'])) print_liste_field_titre($arrayfields['t.ref']['label'],$_SERVER['PHP_SELF'],'t.ref','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.factor_update']['checked'])) print_liste_field_titre($arrayfields['t.factor_update']['label'],$_SERVER['PHP_SELF'],'t.factor_update','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.time_consumed']['checked'])) print_liste_field_titre($arrayfields['t.time_consumed']['label'],$_SERVER['PHP_SELF'],'t.time_consumed','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount_update']['checked'])) print_liste_field_titre($arrayfields['t.amount_update']['label'],$_SERVER['PHP_SELF'],'t.amount_update','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount_depr']['checked'])) print_liste_field_titre($arrayfields['t.amount_depr']['label'],$_SERVER['PHP_SELF'],'t.amount_depr','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount_depr_acum']['checked'])) print_liste_field_titre($arrayfields['t.amount_depr_acum']['label'],$_SERVER['PHP_SELF'],'t.amount_depr_acum','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount_depr_acum_update']['checked'])) print_liste_field_titre($arrayfields['t.amount_depr_acum_update']['label'],$_SERVER['PHP_SELF'],'t.amount_depr_acum_update','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_create']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_create']['label'],$_SERVER['PHP_SELF'],'t.fk_user_create','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_mod']['label'],$_SERVER['PHP_SELF'],'t.fk_user_mod','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($arrayfields['t.status']['label'],$_SERVER['PHP_SELF'],'t.status','',$params,'',$sortfield,$sortorder);

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
if (! empty($arrayfields['t.fk_asset']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_asset" value="'.$search_fk_asset.'" size="10"></td>';
if (! empty($arrayfields['t.ref']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
if (! empty($arrayfields['t.factor_update']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_factor_update" value="'.$search_factor_update.'" size="10"></td>';
if (! empty($arrayfields['t.time_consumed']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_time_consumed" value="'.$search_time_consumed.'" size="10"></td>';
if (! empty($arrayfields['t.amount_update']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount_update" value="'.$search_amount_update.'" size="10"></td>';
if (! empty($arrayfields['t.amount_depr']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount_depr" value="'.$search_amount_depr.'" size="10"></td>';
if (! empty($arrayfields['t.amount_depr_acum']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount_depr_acum" value="'.$search_amount_depr_acum.'" size="10"></td>';
if (! empty($arrayfields['t.amount_depr_acum_update']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount_depr_acum_update" value="'.$search_amount_depr_acum_update.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_create']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_create" value="'.$search_fk_user_create.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_mod" value="'.$search_fk_user_mod.'" size="10"></td>';
if (! empty($arrayfields['t.status']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_status" value="'.$search_status.'" size="10"></td>';

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
