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
 *   	\file       monprojet/projetproduct_list.php
 *		\ingroup    monprojet
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-09-16 12:52
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
dol_include_once('/monprojet/class/projetproduct.class.php');

// Load traductions files requiredby by page
$langs->load("monprojet");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_fk_projet=GETPOST('search_fk_projet','int');
$search_fk_product=GETPOST('search_fk_product','int');
$search_ref=GETPOST('search_ref','alpha');
$search_ref_ext=GETPOST('search_ref_ext','alpha');
$search_fk_parent=GETPOST('search_fk_parent','int');
$search_fk_categorie=GETPOST('search_fk_categorie','int');
$search_label=GETPOST('search_label','alpha');
$search_description=GETPOST('search_description','alpha');
$search_fk_country=GETPOST('search_fk_country','int');
$search_price=GETPOST('search_price','alpha');
$search_price_ttc=GETPOST('search_price_ttc','alpha');
$search_price_min=GETPOST('search_price_min','alpha');
$search_price_min_ttc=GETPOST('search_price_min_ttc','alpha');
$search_price_base_type=GETPOST('search_price_base_type','alpha');
$search_tva_tx=GETPOST('search_tva_tx','alpha');
$search_recuperableonly=GETPOST('search_recuperableonly','int');
$search_localtax1_tx=GETPOST('search_localtax1_tx','alpha');
$search_localtax1_type=GETPOST('search_localtax1_type','alpha');
$search_localtax2_tx=GETPOST('search_localtax2_tx','alpha');
$search_localtax2_type=GETPOST('search_localtax2_type','alpha');
$search_fk_user_author=GETPOST('search_fk_user_author','int');
$search_fk_user_modif=GETPOST('search_fk_user_modif','int');
$search_fk_product_type=GETPOST('search_fk_product_type','int');
$search_pmp=GETPOST('search_pmp','alpha');
$search_finished=GETPOST('search_finished','int');
$search_fk_unit=GETPOST('search_fk_unit','int');
$search_cost_price=GETPOST('search_cost_price','alpha');
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
$hookmanager->initHooks(array('projetproductlist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('monprojet');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Load object if id or ref is provided as parameter
$object=new Projetproduct($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Definition of fields for list
$arrayfields=array(
    
't.fk_projet'=>array('label'=>$langs->trans("Fieldfk_projet"), 'checked'=>1),
't.fk_product'=>array('label'=>$langs->trans("Fieldfk_product"), 'checked'=>1),
't.ref'=>array('label'=>$langs->trans("Fieldref"), 'checked'=>1),
't.ref_ext'=>array('label'=>$langs->trans("Fieldref_ext"), 'checked'=>1),
't.fk_parent'=>array('label'=>$langs->trans("Fieldfk_parent"), 'checked'=>1),
't.fk_categorie'=>array('label'=>$langs->trans("Fieldfk_categorie"), 'checked'=>1),
't.label'=>array('label'=>$langs->trans("Fieldlabel"), 'checked'=>1),
't.description'=>array('label'=>$langs->trans("Fielddescription"), 'checked'=>1),
't.fk_country'=>array('label'=>$langs->trans("Fieldfk_country"), 'checked'=>1),
't.price'=>array('label'=>$langs->trans("Fieldprice"), 'checked'=>1),
't.price_ttc'=>array('label'=>$langs->trans("Fieldprice_ttc"), 'checked'=>1),
't.price_min'=>array('label'=>$langs->trans("Fieldprice_min"), 'checked'=>1),
't.price_min_ttc'=>array('label'=>$langs->trans("Fieldprice_min_ttc"), 'checked'=>1),
't.price_base_type'=>array('label'=>$langs->trans("Fieldprice_base_type"), 'checked'=>1),
't.tva_tx'=>array('label'=>$langs->trans("Fieldtva_tx"), 'checked'=>1),
't.recuperableonly'=>array('label'=>$langs->trans("Fieldrecuperableonly"), 'checked'=>1),
't.localtax1_tx'=>array('label'=>$langs->trans("Fieldlocaltax1_tx"), 'checked'=>1),
't.localtax1_type'=>array('label'=>$langs->trans("Fieldlocaltax1_type"), 'checked'=>1),
't.localtax2_tx'=>array('label'=>$langs->trans("Fieldlocaltax2_tx"), 'checked'=>1),
't.localtax2_type'=>array('label'=>$langs->trans("Fieldlocaltax2_type"), 'checked'=>1),
't.fk_user_author'=>array('label'=>$langs->trans("Fieldfk_user_author"), 'checked'=>1),
't.fk_user_modif'=>array('label'=>$langs->trans("Fieldfk_user_modif"), 'checked'=>1),
't.fk_product_type'=>array('label'=>$langs->trans("Fieldfk_product_type"), 'checked'=>1),
't.pmp'=>array('label'=>$langs->trans("Fieldpmp"), 'checked'=>1),
't.finished'=>array('label'=>$langs->trans("Fieldfinished"), 'checked'=>1),
't.fk_unit'=>array('label'=>$langs->trans("Fieldfk_unit"), 'checked'=>1),
't.cost_price'=>array('label'=>$langs->trans("Fieldcost_price"), 'checked'=>1),
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
	
$search_fk_projet='';
$search_fk_product='';
$search_ref='';
$search_ref_ext='';
$search_fk_parent='';
$search_fk_categorie='';
$search_label='';
$search_description='';
$search_fk_country='';
$search_price='';
$search_price_ttc='';
$search_price_min='';
$search_price_min_ttc='';
$search_price_base_type='';
$search_tva_tx='';
$search_recuperableonly='';
$search_localtax1_tx='';
$search_localtax1_type='';
$search_localtax2_tx='';
$search_localtax2_type='';
$search_fk_user_author='';
$search_fk_user_modif='';
$search_fk_product_type='';
$search_pmp='';
$search_finished='';
$search_fk_unit='';
$search_cost_price='';
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

		$sql .= " t.fk_projet,";
		$sql .= " t.fk_product,";
		$sql .= " t.ref,";
		$sql .= " t.ref_ext,";
		$sql .= " t.datec,";
		$sql .= " t.tms,";
		$sql .= " t.fk_parent,";
		$sql .= " t.fk_categorie,";
		$sql .= " t.label,";
		$sql .= " t.description,";
		$sql .= " t.fk_country,";
		$sql .= " t.price,";
		$sql .= " t.price_ttc,";
		$sql .= " t.price_min,";
		$sql .= " t.price_min_ttc,";
		$sql .= " t.price_base_type,";
		$sql .= " t.tva_tx,";
		$sql .= " t.recuperableonly,";
		$sql .= " t.localtax1_tx,";
		$sql .= " t.localtax1_type,";
		$sql .= " t.localtax2_tx,";
		$sql .= " t.localtax2_type,";
		$sql .= " t.fk_user_author,";
		$sql .= " t.fk_user_modif,";
		$sql .= " t.fk_product_type,";
		$sql .= " t.pmp,";
		$sql .= " t.finished,";
		$sql .= " t.fk_unit,";
		$sql .= " t.cost_price,";
		$sql .= " t.status";


// Add fields for extrafields
foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."projet_product as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."projet_product_extrafields as ef on (u.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_fk_projet) $sql.= natural_search("fk_projet",$search_fk_projet);
if ($search_fk_product) $sql.= natural_search("fk_product",$search_fk_product);
if ($search_ref) $sql.= natural_search("ref",$search_ref);
if ($search_ref_ext) $sql.= natural_search("ref_ext",$search_ref_ext);
if ($search_fk_parent) $sql.= natural_search("fk_parent",$search_fk_parent);
if ($search_fk_categorie) $sql.= natural_search("fk_categorie",$search_fk_categorie);
if ($search_label) $sql.= natural_search("label",$search_label);
if ($search_description) $sql.= natural_search("description",$search_description);
if ($search_fk_country) $sql.= natural_search("fk_country",$search_fk_country);
if ($search_price) $sql.= natural_search("price",$search_price);
if ($search_price_ttc) $sql.= natural_search("price_ttc",$search_price_ttc);
if ($search_price_min) $sql.= natural_search("price_min",$search_price_min);
if ($search_price_min_ttc) $sql.= natural_search("price_min_ttc",$search_price_min_ttc);
if ($search_price_base_type) $sql.= natural_search("price_base_type",$search_price_base_type);
if ($search_tva_tx) $sql.= natural_search("tva_tx",$search_tva_tx);
if ($search_recuperableonly) $sql.= natural_search("recuperableonly",$search_recuperableonly);
if ($search_localtax1_tx) $sql.= natural_search("localtax1_tx",$search_localtax1_tx);
if ($search_localtax1_type) $sql.= natural_search("localtax1_type",$search_localtax1_type);
if ($search_localtax2_tx) $sql.= natural_search("localtax2_tx",$search_localtax2_tx);
if ($search_localtax2_type) $sql.= natural_search("localtax2_type",$search_localtax2_type);
if ($search_fk_user_author) $sql.= natural_search("fk_user_author",$search_fk_user_author);
if ($search_fk_user_modif) $sql.= natural_search("fk_user_modif",$search_fk_user_modif);
if ($search_fk_product_type) $sql.= natural_search("fk_product_type",$search_fk_product_type);
if ($search_pmp) $sql.= natural_search("pmp",$search_pmp);
if ($search_finished) $sql.= natural_search("finished",$search_finished);
if ($search_fk_unit) $sql.= natural_search("fk_unit",$search_fk_unit);
if ($search_cost_price) $sql.= natural_search("cost_price",$search_cost_price);
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
    
if ($search_fk_projet != '') $params.= '&amp;search_fk_projet='.urlencode($search_fk_projet);
if ($search_fk_product != '') $params.= '&amp;search_fk_product='.urlencode($search_fk_product);
if ($search_ref != '') $params.= '&amp;search_ref='.urlencode($search_ref);
if ($search_ref_ext != '') $params.= '&amp;search_ref_ext='.urlencode($search_ref_ext);
if ($search_fk_parent != '') $params.= '&amp;search_fk_parent='.urlencode($search_fk_parent);
if ($search_fk_categorie != '') $params.= '&amp;search_fk_categorie='.urlencode($search_fk_categorie);
if ($search_label != '') $params.= '&amp;search_label='.urlencode($search_label);
if ($search_description != '') $params.= '&amp;search_description='.urlencode($search_description);
if ($search_fk_country != '') $params.= '&amp;search_fk_country='.urlencode($search_fk_country);
if ($search_price != '') $params.= '&amp;search_price='.urlencode($search_price);
if ($search_price_ttc != '') $params.= '&amp;search_price_ttc='.urlencode($search_price_ttc);
if ($search_price_min != '') $params.= '&amp;search_price_min='.urlencode($search_price_min);
if ($search_price_min_ttc != '') $params.= '&amp;search_price_min_ttc='.urlencode($search_price_min_ttc);
if ($search_price_base_type != '') $params.= '&amp;search_price_base_type='.urlencode($search_price_base_type);
if ($search_tva_tx != '') $params.= '&amp;search_tva_tx='.urlencode($search_tva_tx);
if ($search_recuperableonly != '') $params.= '&amp;search_recuperableonly='.urlencode($search_recuperableonly);
if ($search_localtax1_tx != '') $params.= '&amp;search_localtax1_tx='.urlencode($search_localtax1_tx);
if ($search_localtax1_type != '') $params.= '&amp;search_localtax1_type='.urlencode($search_localtax1_type);
if ($search_localtax2_tx != '') $params.= '&amp;search_localtax2_tx='.urlencode($search_localtax2_tx);
if ($search_localtax2_type != '') $params.= '&amp;search_localtax2_type='.urlencode($search_localtax2_type);
if ($search_fk_user_author != '') $params.= '&amp;search_fk_user_author='.urlencode($search_fk_user_author);
if ($search_fk_user_modif != '') $params.= '&amp;search_fk_user_modif='.urlencode($search_fk_user_modif);
if ($search_fk_product_type != '') $params.= '&amp;search_fk_product_type='.urlencode($search_fk_product_type);
if ($search_pmp != '') $params.= '&amp;search_pmp='.urlencode($search_pmp);
if ($search_finished != '') $params.= '&amp;search_finished='.urlencode($search_finished);
if ($search_fk_unit != '') $params.= '&amp;search_fk_unit='.urlencode($search_fk_unit);
if ($search_cost_price != '') $params.= '&amp;search_cost_price='.urlencode($search_cost_price);
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
if (! empty($arrayfields['t.fk_projet']['checked'])) print_liste_field_titre($arrayfields['t.fk_projet']['label'],$_SERVER['PHP_SELF'],'t.fk_projet','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_product']['checked'])) print_liste_field_titre($arrayfields['t.fk_product']['label'],$_SERVER['PHP_SELF'],'t.fk_product','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.ref']['checked'])) print_liste_field_titre($arrayfields['t.ref']['label'],$_SERVER['PHP_SELF'],'t.ref','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.ref_ext']['checked'])) print_liste_field_titre($arrayfields['t.ref_ext']['label'],$_SERVER['PHP_SELF'],'t.ref_ext','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_parent']['checked'])) print_liste_field_titre($arrayfields['t.fk_parent']['label'],$_SERVER['PHP_SELF'],'t.fk_parent','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_categorie']['checked'])) print_liste_field_titre($arrayfields['t.fk_categorie']['label'],$_SERVER['PHP_SELF'],'t.fk_categorie','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.label']['checked'])) print_liste_field_titre($arrayfields['t.label']['label'],$_SERVER['PHP_SELF'],'t.label','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.description']['checked'])) print_liste_field_titre($arrayfields['t.description']['label'],$_SERVER['PHP_SELF'],'t.description','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_country']['checked'])) print_liste_field_titre($arrayfields['t.fk_country']['label'],$_SERVER['PHP_SELF'],'t.fk_country','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.price']['checked'])) print_liste_field_titre($arrayfields['t.price']['label'],$_SERVER['PHP_SELF'],'t.price','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.price_ttc']['checked'])) print_liste_field_titre($arrayfields['t.price_ttc']['label'],$_SERVER['PHP_SELF'],'t.price_ttc','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.price_min']['checked'])) print_liste_field_titre($arrayfields['t.price_min']['label'],$_SERVER['PHP_SELF'],'t.price_min','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.price_min_ttc']['checked'])) print_liste_field_titre($arrayfields['t.price_min_ttc']['label'],$_SERVER['PHP_SELF'],'t.price_min_ttc','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.price_base_type']['checked'])) print_liste_field_titre($arrayfields['t.price_base_type']['label'],$_SERVER['PHP_SELF'],'t.price_base_type','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.tva_tx']['checked'])) print_liste_field_titre($arrayfields['t.tva_tx']['label'],$_SERVER['PHP_SELF'],'t.tva_tx','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.recuperableonly']['checked'])) print_liste_field_titre($arrayfields['t.recuperableonly']['label'],$_SERVER['PHP_SELF'],'t.recuperableonly','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.localtax1_tx']['checked'])) print_liste_field_titre($arrayfields['t.localtax1_tx']['label'],$_SERVER['PHP_SELF'],'t.localtax1_tx','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.localtax1_type']['checked'])) print_liste_field_titre($arrayfields['t.localtax1_type']['label'],$_SERVER['PHP_SELF'],'t.localtax1_type','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.localtax2_tx']['checked'])) print_liste_field_titre($arrayfields['t.localtax2_tx']['label'],$_SERVER['PHP_SELF'],'t.localtax2_tx','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.localtax2_type']['checked'])) print_liste_field_titre($arrayfields['t.localtax2_type']['label'],$_SERVER['PHP_SELF'],'t.localtax2_type','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_author']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_author']['label'],$_SERVER['PHP_SELF'],'t.fk_user_author','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_modif']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_modif']['label'],$_SERVER['PHP_SELF'],'t.fk_user_modif','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_product_type']['checked'])) print_liste_field_titre($arrayfields['t.fk_product_type']['label'],$_SERVER['PHP_SELF'],'t.fk_product_type','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.pmp']['checked'])) print_liste_field_titre($arrayfields['t.pmp']['label'],$_SERVER['PHP_SELF'],'t.pmp','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.finished']['checked'])) print_liste_field_titre($arrayfields['t.finished']['label'],$_SERVER['PHP_SELF'],'t.finished','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_unit']['checked'])) print_liste_field_titre($arrayfields['t.fk_unit']['label'],$_SERVER['PHP_SELF'],'t.fk_unit','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.cost_price']['checked'])) print_liste_field_titre($arrayfields['t.cost_price']['label'],$_SERVER['PHP_SELF'],'t.cost_price','',$params,'',$sortfield,$sortorder);
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
if (! empty($arrayfields['t.fk_projet']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_projet" value="'.$search_fk_projet.'" size="10"></td>';
if (! empty($arrayfields['t.fk_product']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_product" value="'.$search_fk_product.'" size="10"></td>';
if (! empty($arrayfields['t.ref']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
if (! empty($arrayfields['t.ref_ext']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref_ext" value="'.$search_ref_ext.'" size="10"></td>';
if (! empty($arrayfields['t.fk_parent']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_parent" value="'.$search_fk_parent.'" size="10"></td>';
if (! empty($arrayfields['t.fk_categorie']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_categorie" value="'.$search_fk_categorie.'" size="10"></td>';
if (! empty($arrayfields['t.label']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_label" value="'.$search_label.'" size="10"></td>';
if (! empty($arrayfields['t.description']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_description" value="'.$search_description.'" size="10"></td>';
if (! empty($arrayfields['t.fk_country']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_country" value="'.$search_fk_country.'" size="10"></td>';
if (! empty($arrayfields['t.price']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_price" value="'.$search_price.'" size="10"></td>';
if (! empty($arrayfields['t.price_ttc']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_price_ttc" value="'.$search_price_ttc.'" size="10"></td>';
if (! empty($arrayfields['t.price_min']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_price_min" value="'.$search_price_min.'" size="10"></td>';
if (! empty($arrayfields['t.price_min_ttc']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_price_min_ttc" value="'.$search_price_min_ttc.'" size="10"></td>';
if (! empty($arrayfields['t.price_base_type']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_price_base_type" value="'.$search_price_base_type.'" size="10"></td>';
if (! empty($arrayfields['t.tva_tx']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_tva_tx" value="'.$search_tva_tx.'" size="10"></td>';
if (! empty($arrayfields['t.recuperableonly']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_recuperableonly" value="'.$search_recuperableonly.'" size="10"></td>';
if (! empty($arrayfields['t.localtax1_tx']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_localtax1_tx" value="'.$search_localtax1_tx.'" size="10"></td>';
if (! empty($arrayfields['t.localtax1_type']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_localtax1_type" value="'.$search_localtax1_type.'" size="10"></td>';
if (! empty($arrayfields['t.localtax2_tx']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_localtax2_tx" value="'.$search_localtax2_tx.'" size="10"></td>';
if (! empty($arrayfields['t.localtax2_type']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_localtax2_type" value="'.$search_localtax2_type.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_author']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_author" value="'.$search_fk_user_author.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_modif']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_modif" value="'.$search_fk_user_modif.'" size="10"></td>';
if (! empty($arrayfields['t.fk_product_type']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_product_type" value="'.$search_fk_product_type.'" size="10"></td>';
if (! empty($arrayfields['t.pmp']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_pmp" value="'.$search_pmp.'" size="10"></td>';
if (! empty($arrayfields['t.finished']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_finished" value="'.$search_finished.'" size="10"></td>';
if (! empty($arrayfields['t.fk_unit']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_unit" value="'.$search_fk_unit.'" size="10"></td>';
if (! empty($arrayfields['t.cost_price']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_cost_price" value="'.$search_cost_price.'" size="10"></td>';
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
