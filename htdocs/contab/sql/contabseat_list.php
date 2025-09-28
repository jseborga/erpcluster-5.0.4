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
 *   	\file       contab/contabseat_list.php
 *		\ingroup    contab
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-11-04 14:51
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
dol_include_once('/contab/class/contabseat.class.php');

// Load traductions files requiredby by page
$langs->load("contab");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_lote=GETPOST('search_lote','alpha');
$search_sblote=GETPOST('search_sblote','alpha');
$search_doc=GETPOST('search_doc','alpha');
$search_currency=GETPOST('search_currency','int');
$search_type_seat=GETPOST('search_type_seat','int');
$search_type_numeric=GETPOST('search_type_numeric','alpha');
$search_sequential=GETPOST('search_sequential','alpha');
$search_seat_month=GETPOST('search_seat_month','alpha');
$search_seaty_year=GETPOST('search_seaty_year','int');
$search_seat_year=GETPOST('search_seat_year','int');
$search_debit_total=GETPOST('search_debit_total','alpha');
$search_credit_total=GETPOST('search_credit_total','alpha');
$search_history=GETPOST('search_history','alpha');
$search_manual=GETPOST('search_manual','int');
$search_fk_user_creator=GETPOST('search_fk_user_creator','int');
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
$hookmanager->initHooks(array('contabseatlist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('contab');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Load object if id or ref is provided as parameter
$object=new Contabseat($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Definition of fields for list
$arrayfields=array(
    
't.entity'=>array('label'=>$langs->trans("Fieldentity"), 'checked'=>1),
't.lote'=>array('label'=>$langs->trans("Fieldlote"), 'checked'=>1),
't.sblote'=>array('label'=>$langs->trans("Fieldsblote"), 'checked'=>1),
't.doc'=>array('label'=>$langs->trans("Fielddoc"), 'checked'=>1),
't.currency'=>array('label'=>$langs->trans("Fieldcurrency"), 'checked'=>1),
't.type_seat'=>array('label'=>$langs->trans("Fieldtype_seat"), 'checked'=>1),
't.type_numeric'=>array('label'=>$langs->trans("Fieldtype_numeric"), 'checked'=>1),
't.sequential'=>array('label'=>$langs->trans("Fieldsequential"), 'checked'=>1),
't.seat_month'=>array('label'=>$langs->trans("Fieldseat_month"), 'checked'=>1),
't.seaty_year'=>array('label'=>$langs->trans("Fieldseaty_year"), 'checked'=>1),
't.seat_year'=>array('label'=>$langs->trans("Fieldseat_year"), 'checked'=>1),
't.debit_total'=>array('label'=>$langs->trans("Fielddebit_total"), 'checked'=>1),
't.credit_total'=>array('label'=>$langs->trans("Fieldcredit_total"), 'checked'=>1),
't.history'=>array('label'=>$langs->trans("Fieldhistory"), 'checked'=>1),
't.manual'=>array('label'=>$langs->trans("Fieldmanual"), 'checked'=>1),
't.fk_user_creator'=>array('label'=>$langs->trans("Fieldfk_user_creator"), 'checked'=>1),
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
	
$search_entity='';
$search_lote='';
$search_sblote='';
$search_doc='';
$search_currency='';
$search_type_seat='';
$search_type_numeric='';
$search_sequential='';
$search_seat_month='';
$search_seaty_year='';
$search_seat_year='';
$search_debit_total='';
$search_credit_total='';
$search_history='';
$search_manual='';
$search_fk_user_creator='';
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
		$sql .= " t.date_seat,";
		$sql .= " t.lote,";
		$sql .= " t.sblote,";
		$sql .= " t.doc,";
		$sql .= " t.currency,";
		$sql .= " t.type_seat,";
		$sql .= " t.type_numeric,";
		$sql .= " t.sequential,";
		$sql .= " t.seat_month,";
		$sql .= " t.seaty_year,";
		$sql .= " t.seat_year,";
		$sql .= " t.debit_total,";
		$sql .= " t.credit_total,";
		$sql .= " t.history,";
		$sql .= " t.manual,";
		$sql .= " t.fk_user_creator,";
		$sql .= " t.date_creator,";
		$sql .= " t.state";


// Add fields for extrafields
foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."contab_seat as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."contab_seat_extrafields as ef on (u.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_entity) $sql.= natural_search("entity",$search_entity);
if ($search_lote) $sql.= natural_search("lote",$search_lote);
if ($search_sblote) $sql.= natural_search("sblote",$search_sblote);
if ($search_doc) $sql.= natural_search("doc",$search_doc);
if ($search_currency) $sql.= natural_search("currency",$search_currency);
if ($search_type_seat) $sql.= natural_search("type_seat",$search_type_seat);
if ($search_type_numeric) $sql.= natural_search("type_numeric",$search_type_numeric);
if ($search_sequential) $sql.= natural_search("sequential",$search_sequential);
if ($search_seat_month) $sql.= natural_search("seat_month",$search_seat_month);
if ($search_seaty_year) $sql.= natural_search("seaty_year",$search_seaty_year);
if ($search_seat_year) $sql.= natural_search("seat_year",$search_seat_year);
if ($search_debit_total) $sql.= natural_search("debit_total",$search_debit_total);
if ($search_credit_total) $sql.= natural_search("credit_total",$search_credit_total);
if ($search_history) $sql.= natural_search("history",$search_history);
if ($search_manual) $sql.= natural_search("manual",$search_manual);
if ($search_fk_user_creator) $sql.= natural_search("fk_user_creator",$search_fk_user_creator);
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
    
if ($search_entity != '') $params.= '&amp;search_entity='.urlencode($search_entity);
if ($search_lote != '') $params.= '&amp;search_lote='.urlencode($search_lote);
if ($search_sblote != '') $params.= '&amp;search_sblote='.urlencode($search_sblote);
if ($search_doc != '') $params.= '&amp;search_doc='.urlencode($search_doc);
if ($search_currency != '') $params.= '&amp;search_currency='.urlencode($search_currency);
if ($search_type_seat != '') $params.= '&amp;search_type_seat='.urlencode($search_type_seat);
if ($search_type_numeric != '') $params.= '&amp;search_type_numeric='.urlencode($search_type_numeric);
if ($search_sequential != '') $params.= '&amp;search_sequential='.urlencode($search_sequential);
if ($search_seat_month != '') $params.= '&amp;search_seat_month='.urlencode($search_seat_month);
if ($search_seaty_year != '') $params.= '&amp;search_seaty_year='.urlencode($search_seaty_year);
if ($search_seat_year != '') $params.= '&amp;search_seat_year='.urlencode($search_seat_year);
if ($search_debit_total != '') $params.= '&amp;search_debit_total='.urlencode($search_debit_total);
if ($search_credit_total != '') $params.= '&amp;search_credit_total='.urlencode($search_credit_total);
if ($search_history != '') $params.= '&amp;search_history='.urlencode($search_history);
if ($search_manual != '') $params.= '&amp;search_manual='.urlencode($search_manual);
if ($search_fk_user_creator != '') $params.= '&amp;search_fk_user_creator='.urlencode($search_fk_user_creator);
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
if (! empty($arrayfields['t.entity']['checked'])) print_liste_field_titre($arrayfields['t.entity']['label'],$_SERVER['PHP_SELF'],'t.entity','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.lote']['checked'])) print_liste_field_titre($arrayfields['t.lote']['label'],$_SERVER['PHP_SELF'],'t.lote','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.sblote']['checked'])) print_liste_field_titre($arrayfields['t.sblote']['label'],$_SERVER['PHP_SELF'],'t.sblote','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.doc']['checked'])) print_liste_field_titre($arrayfields['t.doc']['label'],$_SERVER['PHP_SELF'],'t.doc','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.currency']['checked'])) print_liste_field_titre($arrayfields['t.currency']['label'],$_SERVER['PHP_SELF'],'t.currency','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.type_seat']['checked'])) print_liste_field_titre($arrayfields['t.type_seat']['label'],$_SERVER['PHP_SELF'],'t.type_seat','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.type_numeric']['checked'])) print_liste_field_titre($arrayfields['t.type_numeric']['label'],$_SERVER['PHP_SELF'],'t.type_numeric','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.sequential']['checked'])) print_liste_field_titre($arrayfields['t.sequential']['label'],$_SERVER['PHP_SELF'],'t.sequential','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.seat_month']['checked'])) print_liste_field_titre($arrayfields['t.seat_month']['label'],$_SERVER['PHP_SELF'],'t.seat_month','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.seaty_year']['checked'])) print_liste_field_titre($arrayfields['t.seaty_year']['label'],$_SERVER['PHP_SELF'],'t.seaty_year','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.seat_year']['checked'])) print_liste_field_titre($arrayfields['t.seat_year']['label'],$_SERVER['PHP_SELF'],'t.seat_year','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.debit_total']['checked'])) print_liste_field_titre($arrayfields['t.debit_total']['label'],$_SERVER['PHP_SELF'],'t.debit_total','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.credit_total']['checked'])) print_liste_field_titre($arrayfields['t.credit_total']['label'],$_SERVER['PHP_SELF'],'t.credit_total','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.history']['checked'])) print_liste_field_titre($arrayfields['t.history']['label'],$_SERVER['PHP_SELF'],'t.history','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.manual']['checked'])) print_liste_field_titre($arrayfields['t.manual']['label'],$_SERVER['PHP_SELF'],'t.manual','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_creator']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_creator']['label'],$_SERVER['PHP_SELF'],'t.fk_user_creator','',$params,'',$sortfield,$sortorder);
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
if (! empty($arrayfields['t.entity']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_entity" value="'.$search_entity.'" size="10"></td>';
if (! empty($arrayfields['t.lote']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_lote" value="'.$search_lote.'" size="10"></td>';
if (! empty($arrayfields['t.sblote']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_sblote" value="'.$search_sblote.'" size="10"></td>';
if (! empty($arrayfields['t.doc']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_doc" value="'.$search_doc.'" size="10"></td>';
if (! empty($arrayfields['t.currency']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_currency" value="'.$search_currency.'" size="10"></td>';
if (! empty($arrayfields['t.type_seat']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_type_seat" value="'.$search_type_seat.'" size="10"></td>';
if (! empty($arrayfields['t.type_numeric']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_type_numeric" value="'.$search_type_numeric.'" size="10"></td>';
if (! empty($arrayfields['t.sequential']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_sequential" value="'.$search_sequential.'" size="10"></td>';
if (! empty($arrayfields['t.seat_month']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_seat_month" value="'.$search_seat_month.'" size="10"></td>';
if (! empty($arrayfields['t.seaty_year']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_seaty_year" value="'.$search_seaty_year.'" size="10"></td>';
if (! empty($arrayfields['t.seat_year']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_seat_year" value="'.$search_seat_year.'" size="10"></td>';
if (! empty($arrayfields['t.debit_total']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_debit_total" value="'.$search_debit_total.'" size="10"></td>';
if (! empty($arrayfields['t.credit_total']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_credit_total" value="'.$search_credit_total.'" size="10"></td>';
if (! empty($arrayfields['t.history']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_history" value="'.$search_history.'" size="10"></td>';
if (! empty($arrayfields['t.manual']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_manual" value="'.$search_manual.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_creator']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_creator" value="'.$search_fk_user_creator.'" size="10"></td>';
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
