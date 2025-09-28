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
 *   	\file       monprojet/projettaskresource_list.php
 *		\ingroup    monprojet
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-02-07 12:23
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
dol_include_once('/monprojet/class/projettaskresource.class.php');

// Load traductions files requiredby by page
$langs->load("monprojet");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_fk_projet_task=GETPOST('search_fk_projet_task','int');
$search_ref=GETPOST('search_ref','alpha');
$search_ref_ext=GETPOST('search_ref_ext','alpha');
$search_fk_object=GETPOST('search_fk_object','int');
$search_object=GETPOST('search_object','alpha');
$search_fk_objectdet=GETPOST('search_fk_objectdet','int');
$search_objectdet=GETPOST('search_objectdet','alpha');
$search_fk_stock_mouvement=GETPOST('search_fk_stock_mouvement','int');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_group_resource=GETPOST('search_group_resource','alpha');
$search_type_resource=GETPOST('search_type_resource','alpha');
$search_code_structure=GETPOST('search_code_structure','alpha');
$search_fk_product=GETPOST('search_fk_product','int');
$search_fk_product_projet=GETPOST('search_fk_product_projet','int');
$search_fk_product_budget=GETPOST('search_fk_product_budget','int');
$search_fk_facture_fourn=GETPOST('search_fk_facture_fourn','int');
$search_fk_projet_paiement=GETPOST('search_fk_projet_paiement','int');
$search_detail=GETPOST('search_detail','alpha');
$search_fk_unit=GETPOST('search_fk_unit','int');
$search_quant=GETPOST('search_quant','alpha');
$search_percent_prod=GETPOST('search_percent_prod','alpha');
$search_amount_noprod=GETPOST('search_amount_noprod','alpha');
$search_amount=GETPOST('search_amount','alpha');
$search_rang=GETPOST('search_rang','int');
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
$hookmanager->initHooks(array('projettaskresourcelist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('monprojet');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Load object if id or ref is provided as parameter
$object=new Projettaskresource($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Definition of fields for list
$arrayfields=array(
    
't.fk_projet_task'=>array('label'=>$langs->trans("Fieldfk_projet_task"), 'checked'=>1),
't.ref'=>array('label'=>$langs->trans("Fieldref"), 'checked'=>1),
't.ref_ext'=>array('label'=>$langs->trans("Fieldref_ext"), 'checked'=>1),
't.fk_object'=>array('label'=>$langs->trans("Fieldfk_object"), 'checked'=>1),
't.object'=>array('label'=>$langs->trans("Fieldobject"), 'checked'=>1),
't.fk_objectdet'=>array('label'=>$langs->trans("Fieldfk_objectdet"), 'checked'=>1),
't.objectdet'=>array('label'=>$langs->trans("Fieldobjectdet"), 'checked'=>1),
't.fk_stock_mouvement'=>array('label'=>$langs->trans("Fieldfk_stock_mouvement"), 'checked'=>1),
't.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'checked'=>1),
't.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'checked'=>1),
't.group_resource'=>array('label'=>$langs->trans("Fieldgroup_resource"), 'checked'=>1),
't.type_resource'=>array('label'=>$langs->trans("Fieldtype_resource"), 'checked'=>1),
't.code_structure'=>array('label'=>$langs->trans("Fieldcode_structure"), 'checked'=>1),
't.fk_product'=>array('label'=>$langs->trans("Fieldfk_product"), 'checked'=>1),
't.fk_product_projet'=>array('label'=>$langs->trans("Fieldfk_product_projet"), 'checked'=>1),
't.fk_product_budget'=>array('label'=>$langs->trans("Fieldfk_product_budget"), 'checked'=>1),
't.fk_facture_fourn'=>array('label'=>$langs->trans("Fieldfk_facture_fourn"), 'checked'=>1),
't.fk_projet_paiement'=>array('label'=>$langs->trans("Fieldfk_projet_paiement"), 'checked'=>1),
't.detail'=>array('label'=>$langs->trans("Fielddetail"), 'checked'=>1),
't.fk_unit'=>array('label'=>$langs->trans("Fieldfk_unit"), 'checked'=>1),
't.quant'=>array('label'=>$langs->trans("Fieldquant"), 'checked'=>1),
't.percent_prod'=>array('label'=>$langs->trans("Fieldpercent_prod"), 'checked'=>1),
't.amount_noprod'=>array('label'=>$langs->trans("Fieldamount_noprod"), 'checked'=>1),
't.amount'=>array('label'=>$langs->trans("Fieldamount"), 'checked'=>1),
't.rang'=>array('label'=>$langs->trans("Fieldrang"), 'checked'=>1),
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
	
$search_fk_projet_task='';
$search_ref='';
$search_ref_ext='';
$search_fk_object='';
$search_object='';
$search_fk_objectdet='';
$search_objectdet='';
$search_fk_stock_mouvement='';
$search_fk_user_create='';
$search_fk_user_mod='';
$search_group_resource='';
$search_type_resource='';
$search_code_structure='';
$search_fk_product='';
$search_fk_product_projet='';
$search_fk_product_budget='';
$search_fk_facture_fourn='';
$search_fk_projet_paiement='';
$search_detail='';
$search_fk_unit='';
$search_quant='';
$search_percent_prod='';
$search_amount_noprod='';
$search_amount='';
$search_rang='';
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

		$sql .= " t.fk_projet_task,";
		$sql .= " t.ref,";
		$sql .= " t.ref_ext,";
		$sql .= " t.date_resource,";
		$sql .= " t.fk_object,";
		$sql .= " t.object,";
		$sql .= " t.fk_objectdet,";
		$sql .= " t.objectdet,";
		$sql .= " t.fk_stock_mouvement,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.group_resource,";
		$sql .= " t.type_resource,";
		$sql .= " t.code_structure,";
		$sql .= " t.fk_product,";
		$sql .= " t.fk_product_projet,";
		$sql .= " t.fk_product_budget,";
		$sql .= " t.fk_facture_fourn,";
		$sql .= " t.fk_projet_paiement,";
		$sql .= " t.detail,";
		$sql .= " t.fk_unit,";
		$sql .= " t.quant,";
		$sql .= " t.percent_prod,";
		$sql .= " t.amount_noprod,";
		$sql .= " t.amount,";
		$sql .= " t.rang,";
		$sql .= " t.date_create,";
		$sql .= " t.date_mod,";
		$sql .= " t.tms,";
		$sql .= " t.status";


// Add fields for extrafields
foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."projet_task_resource as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."projet_task_resource_extrafields as ef on (u.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_fk_projet_task) $sql.= natural_search("fk_projet_task",$search_fk_projet_task);
if ($search_ref) $sql.= natural_search("ref",$search_ref);
if ($search_ref_ext) $sql.= natural_search("ref_ext",$search_ref_ext);
if ($search_fk_object) $sql.= natural_search("fk_object",$search_fk_object);
if ($search_object) $sql.= natural_search("object",$search_object);
if ($search_fk_objectdet) $sql.= natural_search("fk_objectdet",$search_fk_objectdet);
if ($search_objectdet) $sql.= natural_search("objectdet",$search_objectdet);
if ($search_fk_stock_mouvement) $sql.= natural_search("fk_stock_mouvement",$search_fk_stock_mouvement);
if ($search_fk_user_create) $sql.= natural_search("fk_user_create",$search_fk_user_create);
if ($search_fk_user_mod) $sql.= natural_search("fk_user_mod",$search_fk_user_mod);
if ($search_group_resource) $sql.= natural_search("group_resource",$search_group_resource);
if ($search_type_resource) $sql.= natural_search("type_resource",$search_type_resource);
if ($search_code_structure) $sql.= natural_search("code_structure",$search_code_structure);
if ($search_fk_product) $sql.= natural_search("fk_product",$search_fk_product);
if ($search_fk_product_projet) $sql.= natural_search("fk_product_projet",$search_fk_product_projet);
if ($search_fk_product_budget) $sql.= natural_search("fk_product_budget",$search_fk_product_budget);
if ($search_fk_facture_fourn) $sql.= natural_search("fk_facture_fourn",$search_fk_facture_fourn);
if ($search_fk_projet_paiement) $sql.= natural_search("fk_projet_paiement",$search_fk_projet_paiement);
if ($search_detail) $sql.= natural_search("detail",$search_detail);
if ($search_fk_unit) $sql.= natural_search("fk_unit",$search_fk_unit);
if ($search_quant) $sql.= natural_search("quant",$search_quant);
if ($search_percent_prod) $sql.= natural_search("percent_prod",$search_percent_prod);
if ($search_amount_noprod) $sql.= natural_search("amount_noprod",$search_amount_noprod);
if ($search_amount) $sql.= natural_search("amount",$search_amount);
if ($search_rang) $sql.= natural_search("rang",$search_rang);
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
    
if ($search_fk_projet_task != '') $params.= '&amp;search_fk_projet_task='.urlencode($search_fk_projet_task);
if ($search_ref != '') $params.= '&amp;search_ref='.urlencode($search_ref);
if ($search_ref_ext != '') $params.= '&amp;search_ref_ext='.urlencode($search_ref_ext);
if ($search_fk_object != '') $params.= '&amp;search_fk_object='.urlencode($search_fk_object);
if ($search_object != '') $params.= '&amp;search_object='.urlencode($search_object);
if ($search_fk_objectdet != '') $params.= '&amp;search_fk_objectdet='.urlencode($search_fk_objectdet);
if ($search_objectdet != '') $params.= '&amp;search_objectdet='.urlencode($search_objectdet);
if ($search_fk_stock_mouvement != '') $params.= '&amp;search_fk_stock_mouvement='.urlencode($search_fk_stock_mouvement);
if ($search_fk_user_create != '') $params.= '&amp;search_fk_user_create='.urlencode($search_fk_user_create);
if ($search_fk_user_mod != '') $params.= '&amp;search_fk_user_mod='.urlencode($search_fk_user_mod);
if ($search_group_resource != '') $params.= '&amp;search_group_resource='.urlencode($search_group_resource);
if ($search_type_resource != '') $params.= '&amp;search_type_resource='.urlencode($search_type_resource);
if ($search_code_structure != '') $params.= '&amp;search_code_structure='.urlencode($search_code_structure);
if ($search_fk_product != '') $params.= '&amp;search_fk_product='.urlencode($search_fk_product);
if ($search_fk_product_projet != '') $params.= '&amp;search_fk_product_projet='.urlencode($search_fk_product_projet);
if ($search_fk_product_budget != '') $params.= '&amp;search_fk_product_budget='.urlencode($search_fk_product_budget);
if ($search_fk_facture_fourn != '') $params.= '&amp;search_fk_facture_fourn='.urlencode($search_fk_facture_fourn);
if ($search_fk_projet_paiement != '') $params.= '&amp;search_fk_projet_paiement='.urlencode($search_fk_projet_paiement);
if ($search_detail != '') $params.= '&amp;search_detail='.urlencode($search_detail);
if ($search_fk_unit != '') $params.= '&amp;search_fk_unit='.urlencode($search_fk_unit);
if ($search_quant != '') $params.= '&amp;search_quant='.urlencode($search_quant);
if ($search_percent_prod != '') $params.= '&amp;search_percent_prod='.urlencode($search_percent_prod);
if ($search_amount_noprod != '') $params.= '&amp;search_amount_noprod='.urlencode($search_amount_noprod);
if ($search_amount != '') $params.= '&amp;search_amount='.urlencode($search_amount);
if ($search_rang != '') $params.= '&amp;search_rang='.urlencode($search_rang);
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
if (! empty($arrayfields['t.fk_projet_task']['checked'])) print_liste_field_titre($arrayfields['t.fk_projet_task']['label'],$_SERVER['PHP_SELF'],'t.fk_projet_task','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.ref']['checked'])) print_liste_field_titre($arrayfields['t.ref']['label'],$_SERVER['PHP_SELF'],'t.ref','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.ref_ext']['checked'])) print_liste_field_titre($arrayfields['t.ref_ext']['label'],$_SERVER['PHP_SELF'],'t.ref_ext','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_object']['checked'])) print_liste_field_titre($arrayfields['t.fk_object']['label'],$_SERVER['PHP_SELF'],'t.fk_object','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.object']['checked'])) print_liste_field_titre($arrayfields['t.object']['label'],$_SERVER['PHP_SELF'],'t.object','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_objectdet']['checked'])) print_liste_field_titre($arrayfields['t.fk_objectdet']['label'],$_SERVER['PHP_SELF'],'t.fk_objectdet','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.objectdet']['checked'])) print_liste_field_titre($arrayfields['t.objectdet']['label'],$_SERVER['PHP_SELF'],'t.objectdet','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_stock_mouvement']['checked'])) print_liste_field_titre($arrayfields['t.fk_stock_mouvement']['label'],$_SERVER['PHP_SELF'],'t.fk_stock_mouvement','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_create']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_create']['label'],$_SERVER['PHP_SELF'],'t.fk_user_create','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_mod']['label'],$_SERVER['PHP_SELF'],'t.fk_user_mod','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.group_resource']['checked'])) print_liste_field_titre($arrayfields['t.group_resource']['label'],$_SERVER['PHP_SELF'],'t.group_resource','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.type_resource']['checked'])) print_liste_field_titre($arrayfields['t.type_resource']['label'],$_SERVER['PHP_SELF'],'t.type_resource','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.code_structure']['checked'])) print_liste_field_titre($arrayfields['t.code_structure']['label'],$_SERVER['PHP_SELF'],'t.code_structure','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_product']['checked'])) print_liste_field_titre($arrayfields['t.fk_product']['label'],$_SERVER['PHP_SELF'],'t.fk_product','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_product_projet']['checked'])) print_liste_field_titre($arrayfields['t.fk_product_projet']['label'],$_SERVER['PHP_SELF'],'t.fk_product_projet','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_product_budget']['checked'])) print_liste_field_titre($arrayfields['t.fk_product_budget']['label'],$_SERVER['PHP_SELF'],'t.fk_product_budget','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_facture_fourn']['checked'])) print_liste_field_titre($arrayfields['t.fk_facture_fourn']['label'],$_SERVER['PHP_SELF'],'t.fk_facture_fourn','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_projet_paiement']['checked'])) print_liste_field_titre($arrayfields['t.fk_projet_paiement']['label'],$_SERVER['PHP_SELF'],'t.fk_projet_paiement','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.detail']['checked'])) print_liste_field_titre($arrayfields['t.detail']['label'],$_SERVER['PHP_SELF'],'t.detail','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_unit']['checked'])) print_liste_field_titre($arrayfields['t.fk_unit']['label'],$_SERVER['PHP_SELF'],'t.fk_unit','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.quant']['checked'])) print_liste_field_titre($arrayfields['t.quant']['label'],$_SERVER['PHP_SELF'],'t.quant','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.percent_prod']['checked'])) print_liste_field_titre($arrayfields['t.percent_prod']['label'],$_SERVER['PHP_SELF'],'t.percent_prod','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount_noprod']['checked'])) print_liste_field_titre($arrayfields['t.amount_noprod']['label'],$_SERVER['PHP_SELF'],'t.amount_noprod','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount']['checked'])) print_liste_field_titre($arrayfields['t.amount']['label'],$_SERVER['PHP_SELF'],'t.amount','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.rang']['checked'])) print_liste_field_titre($arrayfields['t.rang']['label'],$_SERVER['PHP_SELF'],'t.rang','',$params,'',$sortfield,$sortorder);
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
if (! empty($arrayfields['t.fk_projet_task']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_projet_task" value="'.$search_fk_projet_task.'" size="10"></td>';
if (! empty($arrayfields['t.ref']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
if (! empty($arrayfields['t.ref_ext']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref_ext" value="'.$search_ref_ext.'" size="10"></td>';
if (! empty($arrayfields['t.fk_object']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_object" value="'.$search_fk_object.'" size="10"></td>';
if (! empty($arrayfields['t.object']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_object" value="'.$search_object.'" size="10"></td>';
if (! empty($arrayfields['t.fk_objectdet']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_objectdet" value="'.$search_fk_objectdet.'" size="10"></td>';
if (! empty($arrayfields['t.objectdet']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_objectdet" value="'.$search_objectdet.'" size="10"></td>';
if (! empty($arrayfields['t.fk_stock_mouvement']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_stock_mouvement" value="'.$search_fk_stock_mouvement.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_create']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_create" value="'.$search_fk_user_create.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_mod" value="'.$search_fk_user_mod.'" size="10"></td>';
if (! empty($arrayfields['t.group_resource']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_group_resource" value="'.$search_group_resource.'" size="10"></td>';
if (! empty($arrayfields['t.type_resource']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_type_resource" value="'.$search_type_resource.'" size="10"></td>';
if (! empty($arrayfields['t.code_structure']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_code_structure" value="'.$search_code_structure.'" size="10"></td>';
if (! empty($arrayfields['t.fk_product']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_product" value="'.$search_fk_product.'" size="10"></td>';
if (! empty($arrayfields['t.fk_product_projet']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_product_projet" value="'.$search_fk_product_projet.'" size="10"></td>';
if (! empty($arrayfields['t.fk_product_budget']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_product_budget" value="'.$search_fk_product_budget.'" size="10"></td>';
if (! empty($arrayfields['t.fk_facture_fourn']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_facture_fourn" value="'.$search_fk_facture_fourn.'" size="10"></td>';
if (! empty($arrayfields['t.fk_projet_paiement']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_projet_paiement" value="'.$search_fk_projet_paiement.'" size="10"></td>';
if (! empty($arrayfields['t.detail']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_detail" value="'.$search_detail.'" size="10"></td>';
if (! empty($arrayfields['t.fk_unit']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_unit" value="'.$search_fk_unit.'" size="10"></td>';
if (! empty($arrayfields['t.quant']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_quant" value="'.$search_quant.'" size="10"></td>';
if (! empty($arrayfields['t.percent_prod']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_percent_prod" value="'.$search_percent_prod.'" size="10"></td>';
if (! empty($arrayfields['t.amount_noprod']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount_noprod" value="'.$search_amount_noprod.'" size="10"></td>';
if (! empty($arrayfields['t.amount']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount" value="'.$search_amount.'" size="10"></td>';
if (! empty($arrayfields['t.rang']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_rang" value="'.$search_rang.'" size="10"></td>';
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
