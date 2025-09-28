<?php
/* Copyright (C) 2007-2016 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2014-2016 Juanjo Menent        <jmenent@2byte.es>
 * Copyright (C) 2016      Jean-François Ferry	<jfefe@aternatik.fr>
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
 *   	\file       purchase/facturefournadd_list.php
 *		\ingroup    purchase
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-04-25 21:23
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
dol_include_once('/purchase/class/facturefournadd.class.php');

// Load traductions files requiredby by page
$langs->load("purchase");
$langs->load("other");

$action=GETPOST('action','alpha');
$massaction=GETPOST('massaction','alpha');
$show_files=GETPOST('show_files','int');
$confirm=GETPOST('confirm','alpha');
$toselect = GETPOST('toselect', 'array');

$id			= GETPOST('id','int');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');

$search_all=trim(GETPOST("sall"));

$search_fk_facture_fourn=GETPOST('search_fk_facture_fourn','int');
$search_object=GETPOST('search_object','alpha');
$search_fk_object=GETPOST('search_fk_object','int');
$search_fk_projet_task=GETPOST('search_fk_projet_task','int');
$search_code_facture=GETPOST('search_code_facture','alpha');
$search_code_type_purchase=GETPOST('search_code_type_purchase','alpha');
$search_nit_company=GETPOST('search_nit_company','alpha');
$search_nfiscal=GETPOST('search_nfiscal','int');
$search_ndui=GETPOST('search_ndui','alpha');
$search_num_autoriz=GETPOST('search_num_autoriz','alpha');
$search_nit=GETPOST('search_nit','alpha');
$search_razsoc=GETPOST('search_razsoc','alpha');
$search_cod_control=GETPOST('search_cod_control','alpha');
$search_codqr=GETPOST('search_codqr','alpha');
$search_amountfiscal=GETPOST('search_amountfiscal','alpha');
$search_amountnofiscal=GETPOST('search_amountnofiscal','alpha');
$search_amount_ice=GETPOST('search_amount_ice','alpha');
$search_discount=GETPOST('search_discount','alpha');
$search_localtax3=GETPOST('search_localtax3','alpha');
$search_localtax4=GETPOST('search_localtax4','alpha');
$search_localtax5=GETPOST('search_localtax5','alpha');
$search_localtax6=GETPOST('search_localtax6','alpha');
$search_localtax7=GETPOST('search_localtax7','alpha');
$search_localtax8=GETPOST('search_localtax8','alpha');
$search_localtax9=GETPOST('search_localtax9','alpha');


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

// Initialize technical object to manage context to save list fields
$contextpage=GETPOST('contextpage','aZ')?GETPOST('contextpage','aZ'):'purchaselist';

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('purchaselist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('purchase');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// List of fields to search into when doing a "search in all"
$fieldstosearchall = array(
    't.ref'=>'Ref',
    't.note_public'=>'NotePublic',
);
if (empty($user->socid)) $fieldstosearchall["t.note_private"]="NotePrivate";

// Definition of fields for list
$arrayfields=array(
    
't.fk_facture_fourn'=>array('label'=>$langs->trans("Fieldfk_facture_fourn"), 'checked'=>1),
't.object'=>array('label'=>$langs->trans("Fieldobject"), 'checked'=>1),
't.fk_object'=>array('label'=>$langs->trans("Fieldfk_object"), 'checked'=>1),
't.fk_projet_task'=>array('label'=>$langs->trans("Fieldfk_projet_task"), 'checked'=>1),
't.code_facture'=>array('label'=>$langs->trans("Fieldcode_facture"), 'checked'=>1),
't.code_type_purchase'=>array('label'=>$langs->trans("Fieldcode_type_purchase"), 'checked'=>1),
't.nit_company'=>array('label'=>$langs->trans("Fieldnit_company"), 'checked'=>1),
't.nfiscal'=>array('label'=>$langs->trans("Fieldnfiscal"), 'checked'=>1),
't.ndui'=>array('label'=>$langs->trans("Fieldndui"), 'checked'=>1),
't.num_autoriz'=>array('label'=>$langs->trans("Fieldnum_autoriz"), 'checked'=>1),
't.nit'=>array('label'=>$langs->trans("Fieldnit"), 'checked'=>1),
't.razsoc'=>array('label'=>$langs->trans("Fieldrazsoc"), 'checked'=>1),
't.cod_control'=>array('label'=>$langs->trans("Fieldcod_control"), 'checked'=>1),
't.codqr'=>array('label'=>$langs->trans("Fieldcodqr"), 'checked'=>1),
't.amountfiscal'=>array('label'=>$langs->trans("Fieldamountfiscal"), 'checked'=>1),
't.amountnofiscal'=>array('label'=>$langs->trans("Fieldamountnofiscal"), 'checked'=>1),
't.amount_ice'=>array('label'=>$langs->trans("Fieldamount_ice"), 'checked'=>1),
't.discount'=>array('label'=>$langs->trans("Fielddiscount"), 'checked'=>1),
't.localtax3'=>array('label'=>$langs->trans("Fieldlocaltax3"), 'checked'=>1),
't.localtax4'=>array('label'=>$langs->trans("Fieldlocaltax4"), 'checked'=>1),
't.localtax5'=>array('label'=>$langs->trans("Fieldlocaltax5"), 'checked'=>1),
't.localtax6'=>array('label'=>$langs->trans("Fieldlocaltax6"), 'checked'=>1),
't.localtax7'=>array('label'=>$langs->trans("Fieldlocaltax7"), 'checked'=>1),
't.localtax8'=>array('label'=>$langs->trans("Fieldlocaltax8"), 'checked'=>1),
't.localtax9'=>array('label'=>$langs->trans("Fieldlocaltax9"), 'checked'=>1),

    
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


// Load object if id or ref is provided as parameter
$object=new Facturefournadd($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}




/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

if (GETPOST('cancel')) { $action='list'; $massaction=''; }
if (! GETPOST('confirmmassaction') && $massaction != 'presend' && $massaction != 'confirm_presend') { $massaction=''; }

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
    // Selection of new fields
    include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

    // Purge search criteria
    if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter.x") ||GETPOST("button_removefilter")) // All tests are required to be compatible with all browsers
    {
    	
$search_fk_facture_fourn='';
$search_object='';
$search_fk_object='';
$search_fk_projet_task='';
$search_code_facture='';
$search_code_type_purchase='';
$search_nit_company='';
$search_nfiscal='';
$search_ndui='';
$search_num_autoriz='';
$search_nit='';
$search_razsoc='';
$search_cod_control='';
$search_codqr='';
$search_amountfiscal='';
$search_amountnofiscal='';
$search_amount_ice='';
$search_discount='';
$search_localtax3='';
$search_localtax4='';
$search_localtax5='';
$search_localtax6='';
$search_localtax7='';
$search_localtax8='';
$search_localtax9='';

    	
    	$search_date_creation='';
    	$search_date_update='';
        $toselect='';
        $search_array_options=array();
    }

    // Mass actions
    $objectclass='Skeleton';
    $objectlabel='Skeleton';
    $permtoread = $user->rights->facturefournadd->read;
    $permtodelete = $user->rights->facturefournadd->delete;
    $uploaddir = $conf->facturefournadd->dir_output;
    include DOL_DOCUMENT_ROOT.'/core/actions_massactions.inc.php';
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

		$sql .= " t.fk_facture_fourn,";
		$sql .= " t.object,";
		$sql .= " t.fk_object,";
		$sql .= " t.fk_projet_task,";
		$sql .= " t.code_facture,";
		$sql .= " t.code_type_purchase,";
		$sql .= " t.nit_company,";
		$sql .= " t.nfiscal,";
		$sql .= " t.ndui,";
		$sql .= " t.num_autoriz,";
		$sql .= " t.nit,";
		$sql .= " t.razsoc,";
		$sql .= " t.cod_control,";
		$sql .= " t.codqr,";
		$sql .= " t.amountfiscal,";
		$sql .= " t.amountnofiscal,";
		$sql .= " t.amount_ice,";
		$sql .= " t.discount,";
		$sql .= " t.datec,";
		$sql .= " t.tms,";
		$sql .= " t.localtax3,";
		$sql .= " t.localtax4,";
		$sql .= " t.localtax5,";
		$sql .= " t.localtax6,";
		$sql .= " t.localtax7,";
		$sql .= " t.localtax8,";
		$sql .= " t.localtax9";


// Add fields from extrafields
foreach ($extrafields->attribute_label as $key => $val) $sql.=($extrafields->attribute_type[$key] != 'separate' ? ",ef.".$key.' as options_'.$key : '');
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."facture_fourn_add as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."facture_fourn_add_extrafields as ef on (t.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_fk_facture_fourn) $sql.= natural_search("fk_facture_fourn",$search_fk_facture_fourn);
if ($search_object) $sql.= natural_search("object",$search_object);
if ($search_fk_object) $sql.= natural_search("fk_object",$search_fk_object);
if ($search_fk_projet_task) $sql.= natural_search("fk_projet_task",$search_fk_projet_task);
if ($search_code_facture) $sql.= natural_search("code_facture",$search_code_facture);
if ($search_code_type_purchase) $sql.= natural_search("code_type_purchase",$search_code_type_purchase);
if ($search_nit_company) $sql.= natural_search("nit_company",$search_nit_company);
if ($search_nfiscal) $sql.= natural_search("nfiscal",$search_nfiscal);
if ($search_ndui) $sql.= natural_search("ndui",$search_ndui);
if ($search_num_autoriz) $sql.= natural_search("num_autoriz",$search_num_autoriz);
if ($search_nit) $sql.= natural_search("nit",$search_nit);
if ($search_razsoc) $sql.= natural_search("razsoc",$search_razsoc);
if ($search_cod_control) $sql.= natural_search("cod_control",$search_cod_control);
if ($search_codqr) $sql.= natural_search("codqr",$search_codqr);
if ($search_amountfiscal) $sql.= natural_search("amountfiscal",$search_amountfiscal);
if ($search_amountnofiscal) $sql.= natural_search("amountnofiscal",$search_amountnofiscal);
if ($search_amount_ice) $sql.= natural_search("amount_ice",$search_amount_ice);
if ($search_discount) $sql.= natural_search("discount",$search_discount);
if ($search_localtax3) $sql.= natural_search("localtax3",$search_localtax3);
if ($search_localtax4) $sql.= natural_search("localtax4",$search_localtax4);
if ($search_localtax5) $sql.= natural_search("localtax5",$search_localtax5);
if ($search_localtax6) $sql.= natural_search("localtax6",$search_localtax6);
if ($search_localtax7) $sql.= natural_search("localtax7",$search_localtax7);
if ($search_localtax8) $sql.= natural_search("localtax8",$search_localtax8);
if ($search_localtax9) $sql.= natural_search("localtax9",$search_localtax9);


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
$nbtotalofrecords = '';
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
	$result = $db->query($sql);
	$nbtotalofrecords = $db->num_rows($result);
}	

$sql.= $db->plimit($limit+1, $offset);

dol_syslog($script_file, LOG_DEBUG);
$resql=$db->query($sql);
if (! $resql)
{
    dol_print_error($db);
    exit;
}

$num = $db->num_rows($resql);

// Direct jump if only one record found
if ($num == 1 && ! empty($conf->global->MAIN_SEARCH_DIRECT_OPEN_IF_ONLY_ONE) && $search_all)
{
    $obj = $db->fetch_object($resql);
    $id = $obj->rowid;
    header("Location: ".DOL_URL_ROOT.'/facturefournadd/card.php?id='.$id);
    exit;
}

llxHeader('', $title, $help_url);

$arrayofselected=is_array($toselect)?$toselect:array();

$param='';
if (! empty($contextpage) && $contextpage != $_SERVER["PHP_SELF"]) $param.='&contextpage='.$contextpage;
if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;
if ($search_field1 != '') $param.= '&amp;search_field1='.urlencode($search_field1);
if ($search_field2 != '') $param.= '&amp;search_field2='.urlencode($search_field2);
if ($optioncss != '') $param.='&optioncss='.$optioncss;
// Add $param from extra fields
foreach ($search_array_options as $key => $val)
{
    $crit=$val;
    $tmpkey=preg_replace('/search_options_/','',$key);
    if ($val != '') $param.='&search_options_'.$tmpkey.'='.urlencode($val);
} 

$arrayofmassactions =  array(
    'presend'=>$langs->trans("SendByMail"),
    'builddoc'=>$langs->trans("PDFMerge"),
);
if ($user->rights->purchase->supprimer) $arrayofmassactions['delete']=$langs->trans("Delete");
if ($massaction == 'presend') $arrayofmassactions=array();
$massactionbutton=$form->selectMassAction('', $arrayofmassactions);

print '<form method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
print '<input type="hidden" name="action" value="list">';
print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
print '<input type="hidden" name="contextpage" value="'.$contextpage.'">';

print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, '', $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);

if ($sall)
{
    foreach($fieldstosearchall as $key => $val) $fieldstosearchall[$key]=$langs->trans($val);
    print $langs->trans("FilterOnInto", $sall) . join(', ',$fieldstosearchall);
}

$moreforfilter = '';
$moreforfilter.='<div class="divsearchfield">';
$moreforfilter.= $langs->trans('MyFilter') . ': <input type="text" name="search_myfield" value="'.dol_escape_htmltag($search_myfield).'">';
$moreforfilter.= '</div>';

$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldPreListTitle',$parameters);    // Note that $action and $object may have been modified by hook
if (empty($reshook)) $moreforfilter .= $hookmanager->resPrint;
else $moreforfilter = $hookmanager->resPrint;

if (! empty($moreforfilter))
{
	print '<div class="liste_titre liste_titre_bydiv centpercent">';
	print $moreforfilter;
    print '</div>';
}

$varpage=empty($contextpage)?$_SERVER["PHP_SELF"]:$contextpage;
$selectedfields=$form->multiSelectArrayWithCheckbox('selectedfields', $arrayfields, $varpage);	// This also change content of $arrayfields

print '<div class="div-table-responsive">';
print '<table class="tagtable liste'.($moreforfilter?" listwithfilterbefore":"").'">'."\n";

// Fields title
print '<tr class="liste_titre">';
// 
if (! empty($arrayfields['t.fk_facture_fourn']['checked'])) print_liste_field_titre($arrayfields['t.fk_facture_fourn']['label'],$_SERVER['PHP_SELF'],'t.fk_facture_fourn','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.object']['checked'])) print_liste_field_titre($arrayfields['t.object']['label'],$_SERVER['PHP_SELF'],'t.object','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_object']['checked'])) print_liste_field_titre($arrayfields['t.fk_object']['label'],$_SERVER['PHP_SELF'],'t.fk_object','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_projet_task']['checked'])) print_liste_field_titre($arrayfields['t.fk_projet_task']['label'],$_SERVER['PHP_SELF'],'t.fk_projet_task','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.code_facture']['checked'])) print_liste_field_titre($arrayfields['t.code_facture']['label'],$_SERVER['PHP_SELF'],'t.code_facture','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.code_type_purchase']['checked'])) print_liste_field_titre($arrayfields['t.code_type_purchase']['label'],$_SERVER['PHP_SELF'],'t.code_type_purchase','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.nit_company']['checked'])) print_liste_field_titre($arrayfields['t.nit_company']['label'],$_SERVER['PHP_SELF'],'t.nit_company','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.nfiscal']['checked'])) print_liste_field_titre($arrayfields['t.nfiscal']['label'],$_SERVER['PHP_SELF'],'t.nfiscal','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.ndui']['checked'])) print_liste_field_titre($arrayfields['t.ndui']['label'],$_SERVER['PHP_SELF'],'t.ndui','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.num_autoriz']['checked'])) print_liste_field_titre($arrayfields['t.num_autoriz']['label'],$_SERVER['PHP_SELF'],'t.num_autoriz','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.nit']['checked'])) print_liste_field_titre($arrayfields['t.nit']['label'],$_SERVER['PHP_SELF'],'t.nit','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.razsoc']['checked'])) print_liste_field_titre($arrayfields['t.razsoc']['label'],$_SERVER['PHP_SELF'],'t.razsoc','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.cod_control']['checked'])) print_liste_field_titre($arrayfields['t.cod_control']['label'],$_SERVER['PHP_SELF'],'t.cod_control','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.codqr']['checked'])) print_liste_field_titre($arrayfields['t.codqr']['label'],$_SERVER['PHP_SELF'],'t.codqr','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amountfiscal']['checked'])) print_liste_field_titre($arrayfields['t.amountfiscal']['label'],$_SERVER['PHP_SELF'],'t.amountfiscal','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amountnofiscal']['checked'])) print_liste_field_titre($arrayfields['t.amountnofiscal']['label'],$_SERVER['PHP_SELF'],'t.amountnofiscal','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount_ice']['checked'])) print_liste_field_titre($arrayfields['t.amount_ice']['label'],$_SERVER['PHP_SELF'],'t.amount_ice','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.discount']['checked'])) print_liste_field_titre($arrayfields['t.discount']['label'],$_SERVER['PHP_SELF'],'t.discount','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.localtax3']['checked'])) print_liste_field_titre($arrayfields['t.localtax3']['label'],$_SERVER['PHP_SELF'],'t.localtax3','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.localtax4']['checked'])) print_liste_field_titre($arrayfields['t.localtax4']['label'],$_SERVER['PHP_SELF'],'t.localtax4','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.localtax5']['checked'])) print_liste_field_titre($arrayfields['t.localtax5']['label'],$_SERVER['PHP_SELF'],'t.localtax5','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.localtax6']['checked'])) print_liste_field_titre($arrayfields['t.localtax6']['label'],$_SERVER['PHP_SELF'],'t.localtax6','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.localtax7']['checked'])) print_liste_field_titre($arrayfields['t.localtax7']['label'],$_SERVER['PHP_SELF'],'t.localtax7','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.localtax8']['checked'])) print_liste_field_titre($arrayfields['t.localtax8']['label'],$_SERVER['PHP_SELF'],'t.localtax8','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.localtax9']['checked'])) print_liste_field_titre($arrayfields['t.localtax9']['label'],$_SERVER['PHP_SELF'],'t.localtax9','',$params,'',$sortfield,$sortorder);

//if (! empty($arrayfields['t.field1']['checked'])) print_liste_field_titre($arrayfields['t.field1']['label'],$_SERVER['PHP_SELF'],'t.field1','',$param,'',$sortfield,$sortorder);
//if (! empty($arrayfields['t.field2']['checked'])) print_liste_field_titre($arrayfields['t.field2']['label'],$_SERVER['PHP_SELF'],'t.field2','',$param,'',$sortfield,$sortorder);
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
if (! empty($arrayfields['t.fk_facture_fourn']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_facture_fourn" value="'.$search_fk_facture_fourn.'" size="10"></td>';
if (! empty($arrayfields['t.object']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_object" value="'.$search_object.'" size="10"></td>';
if (! empty($arrayfields['t.fk_object']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_object" value="'.$search_fk_object.'" size="10"></td>';
if (! empty($arrayfields['t.fk_projet_task']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_projet_task" value="'.$search_fk_projet_task.'" size="10"></td>';
if (! empty($arrayfields['t.code_facture']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_code_facture" value="'.$search_code_facture.'" size="10"></td>';
if (! empty($arrayfields['t.code_type_purchase']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_code_type_purchase" value="'.$search_code_type_purchase.'" size="10"></td>';
if (! empty($arrayfields['t.nit_company']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_nit_company" value="'.$search_nit_company.'" size="10"></td>';
if (! empty($arrayfields['t.nfiscal']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_nfiscal" value="'.$search_nfiscal.'" size="10"></td>';
if (! empty($arrayfields['t.ndui']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ndui" value="'.$search_ndui.'" size="10"></td>';
if (! empty($arrayfields['t.num_autoriz']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_num_autoriz" value="'.$search_num_autoriz.'" size="10"></td>';
if (! empty($arrayfields['t.nit']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_nit" value="'.$search_nit.'" size="10"></td>';
if (! empty($arrayfields['t.razsoc']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_razsoc" value="'.$search_razsoc.'" size="10"></td>';
if (! empty($arrayfields['t.cod_control']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_cod_control" value="'.$search_cod_control.'" size="10"></td>';
if (! empty($arrayfields['t.codqr']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_codqr" value="'.$search_codqr.'" size="10"></td>';
if (! empty($arrayfields['t.amountfiscal']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amountfiscal" value="'.$search_amountfiscal.'" size="10"></td>';
if (! empty($arrayfields['t.amountnofiscal']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amountnofiscal" value="'.$search_amountnofiscal.'" size="10"></td>';
if (! empty($arrayfields['t.amount_ice']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount_ice" value="'.$search_amount_ice.'" size="10"></td>';
if (! empty($arrayfields['t.discount']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_discount" value="'.$search_discount.'" size="10"></td>';
if (! empty($arrayfields['t.localtax3']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_localtax3" value="'.$search_localtax3.'" size="10"></td>';
if (! empty($arrayfields['t.localtax4']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_localtax4" value="'.$search_localtax4.'" size="10"></td>';
if (! empty($arrayfields['t.localtax5']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_localtax5" value="'.$search_localtax5.'" size="10"></td>';
if (! empty($arrayfields['t.localtax6']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_localtax6" value="'.$search_localtax6.'" size="10"></td>';
if (! empty($arrayfields['t.localtax7']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_localtax7" value="'.$search_localtax7.'" size="10"></td>';
if (! empty($arrayfields['t.localtax8']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_localtax8" value="'.$search_localtax8.'" size="10"></td>';
if (! empty($arrayfields['t.localtax9']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_localtax9" value="'.$search_localtax9.'" size="10"></td>';

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
$searchpitco=$form->showFilterAndCheckAddButtons($massactionbutton?1:0, 'checkforselect', 1);
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
        print '<td class="nowrap" align="center">';
	    if ($massactionbutton || $massaction)   // If we are in select mode (massactionbutton defined) or if we have already selected and sent an action ($massaction) defined
        {
	        $selected=0;
			if (in_array($obj->rowid, $arrayofselected)) $selected=1;
			print '<input id="cb'.$obj->rowid.'" class="flat checkforselect" type="checkbox" name="toselect[]" value="'.$obj->rowid.'"'.($selected?' checked="checked"':'').'>';
        }
	    print '</td>';
        if (! $i) $totalarray['nbfield']++;

        print '</tr>';
    }
    $i++;
}

// Show total line
if (isset($totalarray['totalhtfield']))
{
    print '<tr class="liste_total">';
    $i=0;
    while ($i < $totalarray['nbfield'])
    {
        $i++;
        if ($i == 1)
        {
            if ($num < $limit) print '<td align="left">'.$langs->trans("Total").'</td>';
            else print '<td align="left">'.$langs->trans("Totalforthispage").'</td>';
        }
        elseif ($totalarray['totalhtfield'] == $i) print '<td align="right">'.price($totalarray['totalht']).'</td>';
        elseif ($totalarray['totalvatfield'] == $i) print '<td align="right">'.price($totalarray['totalvat']).'</td>';
        elseif ($totalarray['totalttcfield'] == $i) print '<td align="right">'.price($totalarray['totalttc']).'</td>';
        else print '<td></td>';
    }
    print '</tr>';
}

$db->free($resql);

$parameters=array('arrayfields'=>$arrayfields, 'sql'=>$sql);
$reshook=$hookmanager->executeHooks('printFieldListFooter',$parameters);    // Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;

print '</table>'."\n";
print '</div>'."\n";

print '</form>'."\n";


if ($massaction == 'builddoc' || $action == 'remove_file' || $show_files)
{
    // Show list of available documents
    $urlsource=$_SERVER['PHP_SELF'].'?sortfield='.$sortfield.'&sortorder='.$sortorder;
    $urlsource.=str_replace('&amp;','&',$param);

    $filedir=$diroutputmassaction;
    $genallowed=$user->rights->facture->lire;
    $delallowed=$user->rights->facture->lire;

    print $formfile->showdocuments('massfilesarea_purchase','',$filedir,$urlsource,0,$delallowed,'',1,1,0,48,1,$param,$title,'');
}
else
{
    print '<br><a name="show_files"></a><a href="'.$_SERVER["PHP_SELF"].'?show_files=1'.$param.'#show_files">'.$langs->trans("ShowTempMassFilesArea").'</a>';
}


// End of page
llxFooter();
$db->close();
