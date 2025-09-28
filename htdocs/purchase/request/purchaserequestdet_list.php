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
 *   	\file       purchase/purchaserequestdet_list.php
 *		\ingroup    purchase
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-02-02 15:00
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
dol_include_once('/purchase/class/purchaserequestdet.class.php');

// Load traductions files requiredby by page
$langs->load("purchase");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_fk_purchase_request=GETPOST('search_fk_purchase_request','int');
$search_fk_parent_line=GETPOST('search_fk_parent_line','int');
$search_fk_product=GETPOST('search_fk_product','int');
$search_label=GETPOST('search_label','alpha');
$search_description=GETPOST('search_description','alpha');
$search_fk_remise_except=GETPOST('search_fk_remise_except','int');
$search_tva_tx=GETPOST('search_tva_tx','alpha');
$search_localtax1_tx=GETPOST('search_localtax1_tx','alpha');
$search_localtax1_type=GETPOST('search_localtax1_type','alpha');
$search_localtax2_tx=GETPOST('search_localtax2_tx','alpha');
$search_localtax2_type=GETPOST('search_localtax2_type','alpha');
$search_qty=GETPOST('search_qty','alpha');
$search_fk_unit=GETPOST('search_fk_unit','int');
$search_remise_percent=GETPOST('search_remise_percent','alpha');
$search_remise=GETPOST('search_remise','alpha');
$search_price=GETPOST('search_price','alpha');
$search_subprice=GETPOST('search_subprice','alpha');
$search_total_ht=GETPOST('search_total_ht','alpha');
$search_total_tva=GETPOST('search_total_tva','alpha');
$search_total_localtax1=GETPOST('search_total_localtax1','alpha');
$search_total_localtax2=GETPOST('search_total_localtax2','alpha');
$search_total_ttc=GETPOST('search_total_ttc','alpha');
$search_product_type=GETPOST('search_product_type','int');
$search_info_bits=GETPOST('search_info_bits','int');
$search_buy_price_ht=GETPOST('search_buy_price_ht','alpha');
$search_fk_product_fournisseur_price=GETPOST('search_fk_product_fournisseur_price','int');
$search_special_code=GETPOST('search_special_code','int');
$search_rang=GETPOST('search_rang','int');
$search_ref_fourn=GETPOST('search_ref_fourn','alpha');
$search_fk_multicurrency=GETPOST('search_fk_multicurrency','int');
$search_multicurrency_code=GETPOST('search_multicurrency_code','alpha');
$search_multicurrency_subprice=GETPOST('search_multicurrency_subprice','alpha');
$search_multicurrency_total_ht=GETPOST('search_multicurrency_total_ht','alpha');
$search_multicurrency_total_tva=GETPOST('search_multicurrency_total_tva','alpha');
$search_multicurrency_total_ttc=GETPOST('search_multicurrency_total_ttc','alpha');
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
$hookmanager->initHooks(array('purchaserequestdetlist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('purchase');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Load object if id or ref is provided as parameter
$object=new Purchaserequestdet($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Definition of fields for list
$arrayfields=array(
    
't.fk_purchase_request'=>array('label'=>$langs->trans("Fieldfk_purchase_request"), 'checked'=>1),
't.fk_parent_line'=>array('label'=>$langs->trans("Fieldfk_parent_line"), 'checked'=>1),
't.fk_product'=>array('label'=>$langs->trans("Fieldfk_product"), 'checked'=>1),
't.label'=>array('label'=>$langs->trans("Fieldlabel"), 'checked'=>1),
't.description'=>array('label'=>$langs->trans("Fielddescription"), 'checked'=>1),
't.fk_remise_except'=>array('label'=>$langs->trans("Fieldfk_remise_except"), 'checked'=>1),
't.tva_tx'=>array('label'=>$langs->trans("Fieldtva_tx"), 'checked'=>1),
't.localtax1_tx'=>array('label'=>$langs->trans("Fieldlocaltax1_tx"), 'checked'=>1),
't.localtax1_type'=>array('label'=>$langs->trans("Fieldlocaltax1_type"), 'checked'=>1),
't.localtax2_tx'=>array('label'=>$langs->trans("Fieldlocaltax2_tx"), 'checked'=>1),
't.localtax2_type'=>array('label'=>$langs->trans("Fieldlocaltax2_type"), 'checked'=>1),
't.qty'=>array('label'=>$langs->trans("Fieldqty"), 'checked'=>1),
't.fk_unit'=>array('label'=>$langs->trans("Fieldfk_unit"), 'checked'=>1),
't.remise_percent'=>array('label'=>$langs->trans("Fieldremise_percent"), 'checked'=>1),
't.remise'=>array('label'=>$langs->trans("Fieldremise"), 'checked'=>1),
't.price'=>array('label'=>$langs->trans("Fieldprice"), 'checked'=>1),
't.subprice'=>array('label'=>$langs->trans("Fieldsubprice"), 'checked'=>1),
't.total_ht'=>array('label'=>$langs->trans("Fieldtotal_ht"), 'checked'=>1),
't.total_tva'=>array('label'=>$langs->trans("Fieldtotal_tva"), 'checked'=>1),
't.total_localtax1'=>array('label'=>$langs->trans("Fieldtotal_localtax1"), 'checked'=>1),
't.total_localtax2'=>array('label'=>$langs->trans("Fieldtotal_localtax2"), 'checked'=>1),
't.total_ttc'=>array('label'=>$langs->trans("Fieldtotal_ttc"), 'checked'=>1),
't.product_type'=>array('label'=>$langs->trans("Fieldproduct_type"), 'checked'=>1),
't.info_bits'=>array('label'=>$langs->trans("Fieldinfo_bits"), 'checked'=>1),
't.buy_price_ht'=>array('label'=>$langs->trans("Fieldbuy_price_ht"), 'checked'=>1),
't.fk_product_fournisseur_price'=>array('label'=>$langs->trans("Fieldfk_product_fournisseur_price"), 'checked'=>1),
't.special_code'=>array('label'=>$langs->trans("Fieldspecial_code"), 'checked'=>1),
't.rang'=>array('label'=>$langs->trans("Fieldrang"), 'checked'=>1),
't.ref_fourn'=>array('label'=>$langs->trans("Fieldref_fourn"), 'checked'=>1),
't.fk_multicurrency'=>array('label'=>$langs->trans("Fieldfk_multicurrency"), 'checked'=>1),
't.multicurrency_code'=>array('label'=>$langs->trans("Fieldmulticurrency_code"), 'checked'=>1),
't.multicurrency_subprice'=>array('label'=>$langs->trans("Fieldmulticurrency_subprice"), 'checked'=>1),
't.multicurrency_total_ht'=>array('label'=>$langs->trans("Fieldmulticurrency_total_ht"), 'checked'=>1),
't.multicurrency_total_tva'=>array('label'=>$langs->trans("Fieldmulticurrency_total_tva"), 'checked'=>1),
't.multicurrency_total_ttc'=>array('label'=>$langs->trans("Fieldmulticurrency_total_ttc"), 'checked'=>1),
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
	
$search_fk_purchase_request='';
$search_fk_parent_line='';
$search_fk_product='';
$search_label='';
$search_description='';
$search_fk_remise_except='';
$search_tva_tx='';
$search_localtax1_tx='';
$search_localtax1_type='';
$search_localtax2_tx='';
$search_localtax2_type='';
$search_qty='';
$search_fk_unit='';
$search_remise_percent='';
$search_remise='';
$search_price='';
$search_subprice='';
$search_total_ht='';
$search_total_tva='';
$search_total_localtax1='';
$search_total_localtax2='';
$search_total_ttc='';
$search_product_type='';
$search_info_bits='';
$search_buy_price_ht='';
$search_fk_product_fournisseur_price='';
$search_special_code='';
$search_rang='';
$search_ref_fourn='';
$search_fk_multicurrency='';
$search_multicurrency_code='';
$search_multicurrency_subprice='';
$search_multicurrency_total_ht='';
$search_multicurrency_total_tva='';
$search_multicurrency_total_ttc='';
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
			header("Location: ".dol_buildpath('/purchase/list.php',1));
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

		$sql .= " t.fk_purchase_request,";
		$sql .= " t.fk_parent_line,";
		$sql .= " t.fk_product,";
		$sql .= " t.label,";
		$sql .= " t.description,";
		$sql .= " t.fk_remise_except,";
		$sql .= " t.tva_tx,";
		$sql .= " t.localtax1_tx,";
		$sql .= " t.localtax1_type,";
		$sql .= " t.localtax2_tx,";
		$sql .= " t.localtax2_type,";
		$sql .= " t.qty,";
		$sql .= " t.fk_unit,";
		$sql .= " t.remise_percent,";
		$sql .= " t.remise,";
		$sql .= " t.price,";
		$sql .= " t.subprice,";
		$sql .= " t.total_ht,";
		$sql .= " t.total_tva,";
		$sql .= " t.total_localtax1,";
		$sql .= " t.total_localtax2,";
		$sql .= " t.total_ttc,";
		$sql .= " t.product_type,";
		$sql .= " t.info_bits,";
		$sql .= " t.buy_price_ht,";
		$sql .= " t.fk_product_fournisseur_price,";
		$sql .= " t.special_code,";
		$sql .= " t.rang,";
		$sql .= " t.ref_fourn,";
		$sql .= " t.fk_multicurrency,";
		$sql .= " t.multicurrency_code,";
		$sql .= " t.multicurrency_subprice,";
		$sql .= " t.multicurrency_total_ht,";
		$sql .= " t.multicurrency_total_tva,";
		$sql .= " t.multicurrency_total_ttc,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.datec,";
		$sql .= " t.datem,";
		$sql .= " t.tms,";
		$sql .= " t.status";


// Add fields for extrafields
foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."purchase_requestdet as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."purchase_requestdet_extrafields as ef on (u.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_fk_purchase_request) $sql.= natural_search("fk_purchase_request",$search_fk_purchase_request);
if ($search_fk_parent_line) $sql.= natural_search("fk_parent_line",$search_fk_parent_line);
if ($search_fk_product) $sql.= natural_search("fk_product",$search_fk_product);
if ($search_label) $sql.= natural_search("label",$search_label);
if ($search_description) $sql.= natural_search("description",$search_description);
if ($search_fk_remise_except) $sql.= natural_search("fk_remise_except",$search_fk_remise_except);
if ($search_tva_tx) $sql.= natural_search("tva_tx",$search_tva_tx);
if ($search_localtax1_tx) $sql.= natural_search("localtax1_tx",$search_localtax1_tx);
if ($search_localtax1_type) $sql.= natural_search("localtax1_type",$search_localtax1_type);
if ($search_localtax2_tx) $sql.= natural_search("localtax2_tx",$search_localtax2_tx);
if ($search_localtax2_type) $sql.= natural_search("localtax2_type",$search_localtax2_type);
if ($search_qty) $sql.= natural_search("qty",$search_qty);
if ($search_fk_unit) $sql.= natural_search("fk_unit",$search_fk_unit);
if ($search_remise_percent) $sql.= natural_search("remise_percent",$search_remise_percent);
if ($search_remise) $sql.= natural_search("remise",$search_remise);
if ($search_price) $sql.= natural_search("price",$search_price);
if ($search_subprice) $sql.= natural_search("subprice",$search_subprice);
if ($search_total_ht) $sql.= natural_search("total_ht",$search_total_ht);
if ($search_total_tva) $sql.= natural_search("total_tva",$search_total_tva);
if ($search_total_localtax1) $sql.= natural_search("total_localtax1",$search_total_localtax1);
if ($search_total_localtax2) $sql.= natural_search("total_localtax2",$search_total_localtax2);
if ($search_total_ttc) $sql.= natural_search("total_ttc",$search_total_ttc);
if ($search_product_type) $sql.= natural_search("product_type",$search_product_type);
if ($search_info_bits) $sql.= natural_search("info_bits",$search_info_bits);
if ($search_buy_price_ht) $sql.= natural_search("buy_price_ht",$search_buy_price_ht);
if ($search_fk_product_fournisseur_price) $sql.= natural_search("fk_product_fournisseur_price",$search_fk_product_fournisseur_price);
if ($search_special_code) $sql.= natural_search("special_code",$search_special_code);
if ($search_rang) $sql.= natural_search("rang",$search_rang);
if ($search_ref_fourn) $sql.= natural_search("ref_fourn",$search_ref_fourn);
if ($search_fk_multicurrency) $sql.= natural_search("fk_multicurrency",$search_fk_multicurrency);
if ($search_multicurrency_code) $sql.= natural_search("multicurrency_code",$search_multicurrency_code);
if ($search_multicurrency_subprice) $sql.= natural_search("multicurrency_subprice",$search_multicurrency_subprice);
if ($search_multicurrency_total_ht) $sql.= natural_search("multicurrency_total_ht",$search_multicurrency_total_ht);
if ($search_multicurrency_total_tva) $sql.= natural_search("multicurrency_total_tva",$search_multicurrency_total_tva);
if ($search_multicurrency_total_ttc) $sql.= natural_search("multicurrency_total_ttc",$search_multicurrency_total_ttc);
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
    
if ($search_fk_purchase_request != '') $params.= '&amp;search_fk_purchase_request='.urlencode($search_fk_purchase_request);
if ($search_fk_parent_line != '') $params.= '&amp;search_fk_parent_line='.urlencode($search_fk_parent_line);
if ($search_fk_product != '') $params.= '&amp;search_fk_product='.urlencode($search_fk_product);
if ($search_label != '') $params.= '&amp;search_label='.urlencode($search_label);
if ($search_description != '') $params.= '&amp;search_description='.urlencode($search_description);
if ($search_fk_remise_except != '') $params.= '&amp;search_fk_remise_except='.urlencode($search_fk_remise_except);
if ($search_tva_tx != '') $params.= '&amp;search_tva_tx='.urlencode($search_tva_tx);
if ($search_localtax1_tx != '') $params.= '&amp;search_localtax1_tx='.urlencode($search_localtax1_tx);
if ($search_localtax1_type != '') $params.= '&amp;search_localtax1_type='.urlencode($search_localtax1_type);
if ($search_localtax2_tx != '') $params.= '&amp;search_localtax2_tx='.urlencode($search_localtax2_tx);
if ($search_localtax2_type != '') $params.= '&amp;search_localtax2_type='.urlencode($search_localtax2_type);
if ($search_qty != '') $params.= '&amp;search_qty='.urlencode($search_qty);
if ($search_fk_unit != '') $params.= '&amp;search_fk_unit='.urlencode($search_fk_unit);
if ($search_remise_percent != '') $params.= '&amp;search_remise_percent='.urlencode($search_remise_percent);
if ($search_remise != '') $params.= '&amp;search_remise='.urlencode($search_remise);
if ($search_price != '') $params.= '&amp;search_price='.urlencode($search_price);
if ($search_subprice != '') $params.= '&amp;search_subprice='.urlencode($search_subprice);
if ($search_total_ht != '') $params.= '&amp;search_total_ht='.urlencode($search_total_ht);
if ($search_total_tva != '') $params.= '&amp;search_total_tva='.urlencode($search_total_tva);
if ($search_total_localtax1 != '') $params.= '&amp;search_total_localtax1='.urlencode($search_total_localtax1);
if ($search_total_localtax2 != '') $params.= '&amp;search_total_localtax2='.urlencode($search_total_localtax2);
if ($search_total_ttc != '') $params.= '&amp;search_total_ttc='.urlencode($search_total_ttc);
if ($search_product_type != '') $params.= '&amp;search_product_type='.urlencode($search_product_type);
if ($search_info_bits != '') $params.= '&amp;search_info_bits='.urlencode($search_info_bits);
if ($search_buy_price_ht != '') $params.= '&amp;search_buy_price_ht='.urlencode($search_buy_price_ht);
if ($search_fk_product_fournisseur_price != '') $params.= '&amp;search_fk_product_fournisseur_price='.urlencode($search_fk_product_fournisseur_price);
if ($search_special_code != '') $params.= '&amp;search_special_code='.urlencode($search_special_code);
if ($search_rang != '') $params.= '&amp;search_rang='.urlencode($search_rang);
if ($search_ref_fourn != '') $params.= '&amp;search_ref_fourn='.urlencode($search_ref_fourn);
if ($search_fk_multicurrency != '') $params.= '&amp;search_fk_multicurrency='.urlencode($search_fk_multicurrency);
if ($search_multicurrency_code != '') $params.= '&amp;search_multicurrency_code='.urlencode($search_multicurrency_code);
if ($search_multicurrency_subprice != '') $params.= '&amp;search_multicurrency_subprice='.urlencode($search_multicurrency_subprice);
if ($search_multicurrency_total_ht != '') $params.= '&amp;search_multicurrency_total_ht='.urlencode($search_multicurrency_total_ht);
if ($search_multicurrency_total_tva != '') $params.= '&amp;search_multicurrency_total_tva='.urlencode($search_multicurrency_total_tva);
if ($search_multicurrency_total_ttc != '') $params.= '&amp;search_multicurrency_total_ttc='.urlencode($search_multicurrency_total_ttc);
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
if (! empty($arrayfields['t.fk_purchase_request']['checked'])) print_liste_field_titre($arrayfields['t.fk_purchase_request']['label'],$_SERVER['PHP_SELF'],'t.fk_purchase_request','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_parent_line']['checked'])) print_liste_field_titre($arrayfields['t.fk_parent_line']['label'],$_SERVER['PHP_SELF'],'t.fk_parent_line','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_product']['checked'])) print_liste_field_titre($arrayfields['t.fk_product']['label'],$_SERVER['PHP_SELF'],'t.fk_product','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.label']['checked'])) print_liste_field_titre($arrayfields['t.label']['label'],$_SERVER['PHP_SELF'],'t.label','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.description']['checked'])) print_liste_field_titre($arrayfields['t.description']['label'],$_SERVER['PHP_SELF'],'t.description','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_remise_except']['checked'])) print_liste_field_titre($arrayfields['t.fk_remise_except']['label'],$_SERVER['PHP_SELF'],'t.fk_remise_except','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.tva_tx']['checked'])) print_liste_field_titre($arrayfields['t.tva_tx']['label'],$_SERVER['PHP_SELF'],'t.tva_tx','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.localtax1_tx']['checked'])) print_liste_field_titre($arrayfields['t.localtax1_tx']['label'],$_SERVER['PHP_SELF'],'t.localtax1_tx','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.localtax1_type']['checked'])) print_liste_field_titre($arrayfields['t.localtax1_type']['label'],$_SERVER['PHP_SELF'],'t.localtax1_type','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.localtax2_tx']['checked'])) print_liste_field_titre($arrayfields['t.localtax2_tx']['label'],$_SERVER['PHP_SELF'],'t.localtax2_tx','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.localtax2_type']['checked'])) print_liste_field_titre($arrayfields['t.localtax2_type']['label'],$_SERVER['PHP_SELF'],'t.localtax2_type','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.qty']['checked'])) print_liste_field_titre($arrayfields['t.qty']['label'],$_SERVER['PHP_SELF'],'t.qty','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_unit']['checked'])) print_liste_field_titre($arrayfields['t.fk_unit']['label'],$_SERVER['PHP_SELF'],'t.fk_unit','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.remise_percent']['checked'])) print_liste_field_titre($arrayfields['t.remise_percent']['label'],$_SERVER['PHP_SELF'],'t.remise_percent','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.remise']['checked'])) print_liste_field_titre($arrayfields['t.remise']['label'],$_SERVER['PHP_SELF'],'t.remise','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.price']['checked'])) print_liste_field_titre($arrayfields['t.price']['label'],$_SERVER['PHP_SELF'],'t.price','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.subprice']['checked'])) print_liste_field_titre($arrayfields['t.subprice']['label'],$_SERVER['PHP_SELF'],'t.subprice','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.total_ht']['checked'])) print_liste_field_titre($arrayfields['t.total_ht']['label'],$_SERVER['PHP_SELF'],'t.total_ht','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.total_tva']['checked'])) print_liste_field_titre($arrayfields['t.total_tva']['label'],$_SERVER['PHP_SELF'],'t.total_tva','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.total_localtax1']['checked'])) print_liste_field_titre($arrayfields['t.total_localtax1']['label'],$_SERVER['PHP_SELF'],'t.total_localtax1','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.total_localtax2']['checked'])) print_liste_field_titre($arrayfields['t.total_localtax2']['label'],$_SERVER['PHP_SELF'],'t.total_localtax2','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.total_ttc']['checked'])) print_liste_field_titre($arrayfields['t.total_ttc']['label'],$_SERVER['PHP_SELF'],'t.total_ttc','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.product_type']['checked'])) print_liste_field_titre($arrayfields['t.product_type']['label'],$_SERVER['PHP_SELF'],'t.product_type','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.info_bits']['checked'])) print_liste_field_titre($arrayfields['t.info_bits']['label'],$_SERVER['PHP_SELF'],'t.info_bits','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.buy_price_ht']['checked'])) print_liste_field_titre($arrayfields['t.buy_price_ht']['label'],$_SERVER['PHP_SELF'],'t.buy_price_ht','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_product_fournisseur_price']['checked'])) print_liste_field_titre($arrayfields['t.fk_product_fournisseur_price']['label'],$_SERVER['PHP_SELF'],'t.fk_product_fournisseur_price','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.special_code']['checked'])) print_liste_field_titre($arrayfields['t.special_code']['label'],$_SERVER['PHP_SELF'],'t.special_code','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.rang']['checked'])) print_liste_field_titre($arrayfields['t.rang']['label'],$_SERVER['PHP_SELF'],'t.rang','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.ref_fourn']['checked'])) print_liste_field_titre($arrayfields['t.ref_fourn']['label'],$_SERVER['PHP_SELF'],'t.ref_fourn','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_multicurrency']['checked'])) print_liste_field_titre($arrayfields['t.fk_multicurrency']['label'],$_SERVER['PHP_SELF'],'t.fk_multicurrency','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.multicurrency_code']['checked'])) print_liste_field_titre($arrayfields['t.multicurrency_code']['label'],$_SERVER['PHP_SELF'],'t.multicurrency_code','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.multicurrency_subprice']['checked'])) print_liste_field_titre($arrayfields['t.multicurrency_subprice']['label'],$_SERVER['PHP_SELF'],'t.multicurrency_subprice','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.multicurrency_total_ht']['checked'])) print_liste_field_titre($arrayfields['t.multicurrency_total_ht']['label'],$_SERVER['PHP_SELF'],'t.multicurrency_total_ht','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.multicurrency_total_tva']['checked'])) print_liste_field_titre($arrayfields['t.multicurrency_total_tva']['label'],$_SERVER['PHP_SELF'],'t.multicurrency_total_tva','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.multicurrency_total_ttc']['checked'])) print_liste_field_titre($arrayfields['t.multicurrency_total_ttc']['label'],$_SERVER['PHP_SELF'],'t.multicurrency_total_ttc','',$params,'',$sortfield,$sortorder);
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
if (! empty($arrayfields['t.fk_purchase_request']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_purchase_request" value="'.$search_fk_purchase_request.'" size="10"></td>';
if (! empty($arrayfields['t.fk_parent_line']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_parent_line" value="'.$search_fk_parent_line.'" size="10"></td>';
if (! empty($arrayfields['t.fk_product']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_product" value="'.$search_fk_product.'" size="10"></td>';
if (! empty($arrayfields['t.label']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_label" value="'.$search_label.'" size="10"></td>';
if (! empty($arrayfields['t.description']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_description" value="'.$search_description.'" size="10"></td>';
if (! empty($arrayfields['t.fk_remise_except']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_remise_except" value="'.$search_fk_remise_except.'" size="10"></td>';
if (! empty($arrayfields['t.tva_tx']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_tva_tx" value="'.$search_tva_tx.'" size="10"></td>';
if (! empty($arrayfields['t.localtax1_tx']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_localtax1_tx" value="'.$search_localtax1_tx.'" size="10"></td>';
if (! empty($arrayfields['t.localtax1_type']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_localtax1_type" value="'.$search_localtax1_type.'" size="10"></td>';
if (! empty($arrayfields['t.localtax2_tx']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_localtax2_tx" value="'.$search_localtax2_tx.'" size="10"></td>';
if (! empty($arrayfields['t.localtax2_type']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_localtax2_type" value="'.$search_localtax2_type.'" size="10"></td>';
if (! empty($arrayfields['t.qty']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_qty" value="'.$search_qty.'" size="10"></td>';
if (! empty($arrayfields['t.fk_unit']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_unit" value="'.$search_fk_unit.'" size="10"></td>';
if (! empty($arrayfields['t.remise_percent']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_remise_percent" value="'.$search_remise_percent.'" size="10"></td>';
if (! empty($arrayfields['t.remise']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_remise" value="'.$search_remise.'" size="10"></td>';
if (! empty($arrayfields['t.price']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_price" value="'.$search_price.'" size="10"></td>';
if (! empty($arrayfields['t.subprice']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_subprice" value="'.$search_subprice.'" size="10"></td>';
if (! empty($arrayfields['t.total_ht']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_total_ht" value="'.$search_total_ht.'" size="10"></td>';
if (! empty($arrayfields['t.total_tva']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_total_tva" value="'.$search_total_tva.'" size="10"></td>';
if (! empty($arrayfields['t.total_localtax1']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_total_localtax1" value="'.$search_total_localtax1.'" size="10"></td>';
if (! empty($arrayfields['t.total_localtax2']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_total_localtax2" value="'.$search_total_localtax2.'" size="10"></td>';
if (! empty($arrayfields['t.total_ttc']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_total_ttc" value="'.$search_total_ttc.'" size="10"></td>';
if (! empty($arrayfields['t.product_type']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_product_type" value="'.$search_product_type.'" size="10"></td>';
if (! empty($arrayfields['t.info_bits']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_info_bits" value="'.$search_info_bits.'" size="10"></td>';
if (! empty($arrayfields['t.buy_price_ht']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_buy_price_ht" value="'.$search_buy_price_ht.'" size="10"></td>';
if (! empty($arrayfields['t.fk_product_fournisseur_price']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_product_fournisseur_price" value="'.$search_fk_product_fournisseur_price.'" size="10"></td>';
if (! empty($arrayfields['t.special_code']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_special_code" value="'.$search_special_code.'" size="10"></td>';
if (! empty($arrayfields['t.rang']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_rang" value="'.$search_rang.'" size="10"></td>';
if (! empty($arrayfields['t.ref_fourn']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref_fourn" value="'.$search_ref_fourn.'" size="10"></td>';
if (! empty($arrayfields['t.fk_multicurrency']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_multicurrency" value="'.$search_fk_multicurrency.'" size="10"></td>';
if (! empty($arrayfields['t.multicurrency_code']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_multicurrency_code" value="'.$search_multicurrency_code.'" size="10"></td>';
if (! empty($arrayfields['t.multicurrency_subprice']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_multicurrency_subprice" value="'.$search_multicurrency_subprice.'" size="10"></td>';
if (! empty($arrayfields['t.multicurrency_total_ht']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_multicurrency_total_ht" value="'.$search_multicurrency_total_ht.'" size="10"></td>';
if (! empty($arrayfields['t.multicurrency_total_tva']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_multicurrency_total_tva" value="'.$search_multicurrency_total_tva.'" size="10"></td>';
if (! empty($arrayfields['t.multicurrency_total_ttc']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_multicurrency_total_ttc" value="'.$search_multicurrency_total_ttc.'" size="10"></td>';
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
