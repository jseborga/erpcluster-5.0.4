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
 *   	\file       almacen/stockmouvementtemp_list.php
 *		\ingroup    almacen
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-03-13 16:16
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
dol_include_once('/almacen/class/stockmouvementtemp.class.php');

// Load traductions files requiredby by page
$langs->load("almacen");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_fk_product=GETPOST('search_fk_product','int');
$search_fk_entrepot=GETPOST('search_fk_entrepot','int');
$search_fk_type_mov=GETPOST('search_fk_type_mov','int');
$search_value=GETPOST('search_value','alpha');
$search_quant=GETPOST('search_quant','alpha');
$search_price=GETPOST('search_price','alpha');
$search_balance_peps=GETPOST('search_balance_peps','alpha');
$search_balance_ueps=GETPOST('search_balance_ueps','alpha');
$search_price_peps=GETPOST('search_price_peps','alpha');
$search_price_ueps=GETPOST('search_price_ueps','alpha');
$search_type_mouvement=GETPOST('search_type_mouvement','int');
$search_fk_user_author=GETPOST('search_fk_user_author','int');
$search_label=GETPOST('search_label','alpha');
$search_fk_origin=GETPOST('search_fk_origin','int');
$search_origintype=GETPOST('search_origintype','alpha');
$search_inventorycode=GETPOST('search_inventorycode','alpha');
$search_batch=GETPOST('search_batch','alpha');
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
$hookmanager->initHooks(array('stockmouvementtemplist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('almacen');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Load object if id or ref is provided as parameter
$object=new Stockmouvementtemp($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Definition of fields for list
$arrayfields=array(
    
't.entity'=>array('label'=>$langs->trans("Fieldentity"), 'checked'=>1),
't.ref'=>array('label'=>$langs->trans("Fieldref"), 'checked'=>1),
't.fk_product'=>array('label'=>$langs->trans("Fieldfk_product"), 'checked'=>1),
't.fk_entrepot'=>array('label'=>$langs->trans("Fieldfk_entrepot"), 'checked'=>1),
't.fk_type_mov'=>array('label'=>$langs->trans("Fieldfk_type_mov"), 'checked'=>1),
't.value'=>array('label'=>$langs->trans("Fieldvalue"), 'checked'=>1),
't.quant'=>array('label'=>$langs->trans("Fieldquant"), 'checked'=>1),
't.price'=>array('label'=>$langs->trans("Fieldprice"), 'checked'=>1),
't.balance_peps'=>array('label'=>$langs->trans("Fieldbalance_peps"), 'checked'=>1),
't.balance_ueps'=>array('label'=>$langs->trans("Fieldbalance_ueps"), 'checked'=>1),
't.price_peps'=>array('label'=>$langs->trans("Fieldprice_peps"), 'checked'=>1),
't.price_ueps'=>array('label'=>$langs->trans("Fieldprice_ueps"), 'checked'=>1),
't.type_mouvement'=>array('label'=>$langs->trans("Fieldtype_mouvement"), 'checked'=>1),
't.fk_user_author'=>array('label'=>$langs->trans("Fieldfk_user_author"), 'checked'=>1),
't.label'=>array('label'=>$langs->trans("Fieldlabel"), 'checked'=>1),
't.fk_origin'=>array('label'=>$langs->trans("Fieldfk_origin"), 'checked'=>1),
't.origintype'=>array('label'=>$langs->trans("Fieldorigintype"), 'checked'=>1),
't.inventorycode'=>array('label'=>$langs->trans("Fieldinventorycode"), 'checked'=>1),
't.batch'=>array('label'=>$langs->trans("Fieldbatch"), 'checked'=>1),
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
$search_fk_product='';
$search_fk_entrepot='';
$search_fk_type_mov='';
$search_value='';
$search_quant='';
$search_price='';
$search_balance_peps='';
$search_balance_ueps='';
$search_price_peps='';
$search_price_ueps='';
$search_type_mouvement='';
$search_fk_user_author='';
$search_label='';
$search_fk_origin='';
$search_origintype='';
$search_inventorycode='';
$search_batch='';
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
			header("Location: ".dol_buildpath('/almacen/list.php',1));
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
		$sql .= " t.tms,";
		$sql .= " t.datem,";
		$sql .= " t.fk_product,";
		$sql .= " t.fk_entrepot,";
		$sql .= " t.fk_type_mov,";
		$sql .= " t.value,";
		$sql .= " t.quant,";
		$sql .= " t.price,";
		$sql .= " t.balance_peps,";
		$sql .= " t.balance_ueps,";
		$sql .= " t.price_peps,";
		$sql .= " t.price_ueps,";
		$sql .= " t.type_mouvement,";
		$sql .= " t.fk_user_author,";
		$sql .= " t.label,";
		$sql .= " t.fk_origin,";
		$sql .= " t.origintype,";
		$sql .= " t.inventorycode,";
		$sql .= " t.batch,";
		$sql .= " t.eatby,";
		$sql .= " t.sellby,";
		$sql .= " t.statut";


// Add fields for extrafields
foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."stock_mouvement_temp as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."stock_mouvement_temp_extrafields as ef on (u.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_entity) $sql.= natural_search("entity",$search_entity);
if ($search_ref) $sql.= natural_search("ref",$search_ref);
if ($search_fk_product) $sql.= natural_search("fk_product",$search_fk_product);
if ($search_fk_entrepot) $sql.= natural_search("fk_entrepot",$search_fk_entrepot);
if ($search_fk_type_mov) $sql.= natural_search("fk_type_mov",$search_fk_type_mov);
if ($search_value) $sql.= natural_search("value",$search_value);
if ($search_quant) $sql.= natural_search("quant",$search_quant);
if ($search_price) $sql.= natural_search("price",$search_price);
if ($search_balance_peps) $sql.= natural_search("balance_peps",$search_balance_peps);
if ($search_balance_ueps) $sql.= natural_search("balance_ueps",$search_balance_ueps);
if ($search_price_peps) $sql.= natural_search("price_peps",$search_price_peps);
if ($search_price_ueps) $sql.= natural_search("price_ueps",$search_price_ueps);
if ($search_type_mouvement) $sql.= natural_search("type_mouvement",$search_type_mouvement);
if ($search_fk_user_author) $sql.= natural_search("fk_user_author",$search_fk_user_author);
if ($search_label) $sql.= natural_search("label",$search_label);
if ($search_fk_origin) $sql.= natural_search("fk_origin",$search_fk_origin);
if ($search_origintype) $sql.= natural_search("origintype",$search_origintype);
if ($search_inventorycode) $sql.= natural_search("inventorycode",$search_inventorycode);
if ($search_batch) $sql.= natural_search("batch",$search_batch);
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
if ($search_fk_product != '') $params.= '&amp;search_fk_product='.urlencode($search_fk_product);
if ($search_fk_entrepot != '') $params.= '&amp;search_fk_entrepot='.urlencode($search_fk_entrepot);
if ($search_fk_type_mov != '') $params.= '&amp;search_fk_type_mov='.urlencode($search_fk_type_mov);
if ($search_value != '') $params.= '&amp;search_value='.urlencode($search_value);
if ($search_quant != '') $params.= '&amp;search_quant='.urlencode($search_quant);
if ($search_price != '') $params.= '&amp;search_price='.urlencode($search_price);
if ($search_balance_peps != '') $params.= '&amp;search_balance_peps='.urlencode($search_balance_peps);
if ($search_balance_ueps != '') $params.= '&amp;search_balance_ueps='.urlencode($search_balance_ueps);
if ($search_price_peps != '') $params.= '&amp;search_price_peps='.urlencode($search_price_peps);
if ($search_price_ueps != '') $params.= '&amp;search_price_ueps='.urlencode($search_price_ueps);
if ($search_type_mouvement != '') $params.= '&amp;search_type_mouvement='.urlencode($search_type_mouvement);
if ($search_fk_user_author != '') $params.= '&amp;search_fk_user_author='.urlencode($search_fk_user_author);
if ($search_label != '') $params.= '&amp;search_label='.urlencode($search_label);
if ($search_fk_origin != '') $params.= '&amp;search_fk_origin='.urlencode($search_fk_origin);
if ($search_origintype != '') $params.= '&amp;search_origintype='.urlencode($search_origintype);
if ($search_inventorycode != '') $params.= '&amp;search_inventorycode='.urlencode($search_inventorycode);
if ($search_batch != '') $params.= '&amp;search_batch='.urlencode($search_batch);
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
if (! empty($arrayfields['t.fk_product']['checked'])) print_liste_field_titre($arrayfields['t.fk_product']['label'],$_SERVER['PHP_SELF'],'t.fk_product','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_entrepot']['checked'])) print_liste_field_titre($arrayfields['t.fk_entrepot']['label'],$_SERVER['PHP_SELF'],'t.fk_entrepot','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_type_mov']['checked'])) print_liste_field_titre($arrayfields['t.fk_type_mov']['label'],$_SERVER['PHP_SELF'],'t.fk_type_mov','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.value']['checked'])) print_liste_field_titre($arrayfields['t.value']['label'],$_SERVER['PHP_SELF'],'t.value','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.quant']['checked'])) print_liste_field_titre($arrayfields['t.quant']['label'],$_SERVER['PHP_SELF'],'t.quant','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.price']['checked'])) print_liste_field_titre($arrayfields['t.price']['label'],$_SERVER['PHP_SELF'],'t.price','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.balance_peps']['checked'])) print_liste_field_titre($arrayfields['t.balance_peps']['label'],$_SERVER['PHP_SELF'],'t.balance_peps','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.balance_ueps']['checked'])) print_liste_field_titre($arrayfields['t.balance_ueps']['label'],$_SERVER['PHP_SELF'],'t.balance_ueps','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.price_peps']['checked'])) print_liste_field_titre($arrayfields['t.price_peps']['label'],$_SERVER['PHP_SELF'],'t.price_peps','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.price_ueps']['checked'])) print_liste_field_titre($arrayfields['t.price_ueps']['label'],$_SERVER['PHP_SELF'],'t.price_ueps','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.type_mouvement']['checked'])) print_liste_field_titre($arrayfields['t.type_mouvement']['label'],$_SERVER['PHP_SELF'],'t.type_mouvement','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_author']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_author']['label'],$_SERVER['PHP_SELF'],'t.fk_user_author','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.label']['checked'])) print_liste_field_titre($arrayfields['t.label']['label'],$_SERVER['PHP_SELF'],'t.label','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_origin']['checked'])) print_liste_field_titre($arrayfields['t.fk_origin']['label'],$_SERVER['PHP_SELF'],'t.fk_origin','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.origintype']['checked'])) print_liste_field_titre($arrayfields['t.origintype']['label'],$_SERVER['PHP_SELF'],'t.origintype','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.inventorycode']['checked'])) print_liste_field_titre($arrayfields['t.inventorycode']['label'],$_SERVER['PHP_SELF'],'t.inventorycode','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.batch']['checked'])) print_liste_field_titre($arrayfields['t.batch']['label'],$_SERVER['PHP_SELF'],'t.batch','',$params,'',$sortfield,$sortorder);
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
if (! empty($arrayfields['t.fk_product']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_product" value="'.$search_fk_product.'" size="10"></td>';
if (! empty($arrayfields['t.fk_entrepot']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_entrepot" value="'.$search_fk_entrepot.'" size="10"></td>';
if (! empty($arrayfields['t.fk_type_mov']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_type_mov" value="'.$search_fk_type_mov.'" size="10"></td>';
if (! empty($arrayfields['t.value']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_value" value="'.$search_value.'" size="10"></td>';
if (! empty($arrayfields['t.quant']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_quant" value="'.$search_quant.'" size="10"></td>';
if (! empty($arrayfields['t.price']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_price" value="'.$search_price.'" size="10"></td>';
if (! empty($arrayfields['t.balance_peps']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_balance_peps" value="'.$search_balance_peps.'" size="10"></td>';
if (! empty($arrayfields['t.balance_ueps']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_balance_ueps" value="'.$search_balance_ueps.'" size="10"></td>';
if (! empty($arrayfields['t.price_peps']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_price_peps" value="'.$search_price_peps.'" size="10"></td>';
if (! empty($arrayfields['t.price_ueps']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_price_ueps" value="'.$search_price_ueps.'" size="10"></td>';
if (! empty($arrayfields['t.type_mouvement']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_type_mouvement" value="'.$search_type_mouvement.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_author']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_author" value="'.$search_fk_user_author.'" size="10"></td>';
if (! empty($arrayfields['t.label']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_label" value="'.$search_label.'" size="10"></td>';
if (! empty($arrayfields['t.fk_origin']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_origin" value="'.$search_fk_origin.'" size="10"></td>';
if (! empty($arrayfields['t.origintype']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_origintype" value="'.$search_origintype.'" size="10"></td>';
if (! empty($arrayfields['t.inventorycode']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_inventorycode" value="'.$search_inventorycode.'" size="10"></td>';
if (! empty($arrayfields['t.batch']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_batch" value="'.$search_batch.'" size="10"></td>';
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
