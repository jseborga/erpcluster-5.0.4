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
 *   	\file       purchase/purchaserequest_list.php
 *		\ingroup    purchase
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-04-26 14:08
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
dol_include_once('/purchase/class/purchaserequest.class.php');

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

$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_ref_ext=GETPOST('search_ref_ext','alpha');
$search_ref_int=GETPOST('search_ref_int','alpha');
$search_fk_projet=GETPOST('search_fk_projet','int');
$search_fk_poa_prev=GETPOST('search_fk_poa_prev','int');
$search_fk_departament=GETPOST('search_fk_departament','int');
$search_fk_user_author=GETPOST('search_fk_user_author','int');
$search_fk_user_modif=GETPOST('search_fk_user_modif','int');
$search_fk_user_valid=GETPOST('search_fk_user_valid','int');
$search_fk_user_cloture=GETPOST('search_fk_user_cloture','int');
$search_note_private=GETPOST('search_note_private','alpha');
$search_note_public=GETPOST('search_note_public','alpha');
$search_model_pdf=GETPOST('search_model_pdf','alpha');
$search_origin=GETPOST('search_origin','alpha');
$search_originid=GETPOST('search_originid','int');
$search_fk_shipping_method=GETPOST('search_fk_shipping_method','int');
$search_import_key=GETPOST('search_import_key','alpha');
$search_extraparams=GETPOST('search_extraparams','alpha');
$search_status=GETPOST('search_status','int');
$search_status_process=GETPOST('search_status_process','int');


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
    
't.entity'=>array('label'=>$langs->trans("Fieldentity"), 'checked'=>1),
't.ref'=>array('label'=>$langs->trans("Fieldref"), 'checked'=>1),
't.ref_ext'=>array('label'=>$langs->trans("Fieldref_ext"), 'checked'=>1),
't.ref_int'=>array('label'=>$langs->trans("Fieldref_int"), 'checked'=>1),
't.fk_projet'=>array('label'=>$langs->trans("Fieldfk_projet"), 'checked'=>1),
't.fk_poa_prev'=>array('label'=>$langs->trans("Fieldfk_poa_prev"), 'checked'=>1),
't.fk_departament'=>array('label'=>$langs->trans("Fieldfk_departament"), 'checked'=>1),
't.fk_user_author'=>array('label'=>$langs->trans("Fieldfk_user_author"), 'checked'=>1),
't.fk_user_modif'=>array('label'=>$langs->trans("Fieldfk_user_modif"), 'checked'=>1),
't.fk_user_valid'=>array('label'=>$langs->trans("Fieldfk_user_valid"), 'checked'=>1),
't.fk_user_cloture'=>array('label'=>$langs->trans("Fieldfk_user_cloture"), 'checked'=>1),
't.note_private'=>array('label'=>$langs->trans("Fieldnote_private"), 'checked'=>1),
't.note_public'=>array('label'=>$langs->trans("Fieldnote_public"), 'checked'=>1),
't.model_pdf'=>array('label'=>$langs->trans("Fieldmodel_pdf"), 'checked'=>1),
't.origin'=>array('label'=>$langs->trans("Fieldorigin"), 'checked'=>1),
't.originid'=>array('label'=>$langs->trans("Fieldoriginid"), 'checked'=>1),
't.fk_shipping_method'=>array('label'=>$langs->trans("Fieldfk_shipping_method"), 'checked'=>1),
't.import_key'=>array('label'=>$langs->trans("Fieldimport_key"), 'checked'=>1),
't.extraparams'=>array('label'=>$langs->trans("Fieldextraparams"), 'checked'=>1),
't.status'=>array('label'=>$langs->trans("Fieldstatus"), 'checked'=>1),
't.status_process'=>array('label'=>$langs->trans("Fieldstatus_process"), 'checked'=>1),

    
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
$object=new Purchaserequest($db);
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
    	
$search_entity='';
$search_ref='';
$search_ref_ext='';
$search_ref_int='';
$search_fk_projet='';
$search_fk_poa_prev='';
$search_fk_departament='';
$search_fk_user_author='';
$search_fk_user_modif='';
$search_fk_user_valid='';
$search_fk_user_cloture='';
$search_note_private='';
$search_note_public='';
$search_model_pdf='';
$search_origin='';
$search_originid='';
$search_fk_shipping_method='';
$search_import_key='';
$search_extraparams='';
$search_status='';
$search_status_process='';

    	
    	$search_date_creation='';
    	$search_date_update='';
        $toselect='';
        $search_array_options=array();
    }

    // Mass actions
    $objectclass='Skeleton';
    $objectlabel='Skeleton';
    $permtoread = $user->rights->purchaserequest->read;
    $permtodelete = $user->rights->purchaserequest->delete;
    $uploaddir = $conf->purchaserequest->dir_output;
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

		$sql .= " t.entity,";
		$sql .= " t.ref,";
		$sql .= " t.ref_ext,";
		$sql .= " t.ref_int,";
		$sql .= " t.fk_projet,";
		$sql .= " t.fk_poa_prev,";
		$sql .= " t.fk_departament,";
		$sql .= " t.tms,";
		$sql .= " t.datec,";
		$sql .= " t.date_valid,";
		$sql .= " t.date_cloture,";
		$sql .= " t.fk_user_author,";
		$sql .= " t.fk_user_modif,";
		$sql .= " t.fk_user_valid,";
		$sql .= " t.fk_user_cloture,";
		$sql .= " t.note_private,";
		$sql .= " t.note_public,";
		$sql .= " t.model_pdf,";
		$sql .= " t.origin,";
		$sql .= " t.originid,";
		$sql .= " t.date_delivery,";
		$sql .= " t.date_livraison,";
		$sql .= " t.fk_shipping_method,";
		$sql .= " t.import_key,";
		$sql .= " t.extraparams,";
		$sql .= " t.datem,";
		$sql .= " t.status,";
		$sql .= " t.status_process";


// Add fields from extrafields
foreach ($extrafields->attribute_label as $key => $val) $sql.=($extrafields->attribute_type[$key] != 'separate' ? ",ef.".$key.' as options_'.$key : '');
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."purchase_request as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."purchase_request_extrafields as ef on (t.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_entity) $sql.= natural_search("entity",$search_entity);
if ($search_ref) $sql.= natural_search("ref",$search_ref);
if ($search_ref_ext) $sql.= natural_search("ref_ext",$search_ref_ext);
if ($search_ref_int) $sql.= natural_search("ref_int",$search_ref_int);
if ($search_fk_projet) $sql.= natural_search("fk_projet",$search_fk_projet);
if ($search_fk_poa_prev) $sql.= natural_search("fk_poa_prev",$search_fk_poa_prev);
if ($search_fk_departament) $sql.= natural_search("fk_departament",$search_fk_departament);
if ($search_fk_user_author) $sql.= natural_search("fk_user_author",$search_fk_user_author);
if ($search_fk_user_modif) $sql.= natural_search("fk_user_modif",$search_fk_user_modif);
if ($search_fk_user_valid) $sql.= natural_search("fk_user_valid",$search_fk_user_valid);
if ($search_fk_user_cloture) $sql.= natural_search("fk_user_cloture",$search_fk_user_cloture);
if ($search_note_private) $sql.= natural_search("note_private",$search_note_private);
if ($search_note_public) $sql.= natural_search("note_public",$search_note_public);
if ($search_model_pdf) $sql.= natural_search("model_pdf",$search_model_pdf);
if ($search_origin) $sql.= natural_search("origin",$search_origin);
if ($search_originid) $sql.= natural_search("originid",$search_originid);
if ($search_fk_shipping_method) $sql.= natural_search("fk_shipping_method",$search_fk_shipping_method);
if ($search_import_key) $sql.= natural_search("import_key",$search_import_key);
if ($search_extraparams) $sql.= natural_search("extraparams",$search_extraparams);
if ($search_status) $sql.= natural_search("status",$search_status);
if ($search_status_process) $sql.= natural_search("status_process",$search_status_process);


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
    header("Location: ".DOL_URL_ROOT.'/purchaserequest/card.php?id='.$id);
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
if (! empty($arrayfields['t.entity']['checked'])) print_liste_field_titre($arrayfields['t.entity']['label'],$_SERVER['PHP_SELF'],'t.entity','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.ref']['checked'])) print_liste_field_titre($arrayfields['t.ref']['label'],$_SERVER['PHP_SELF'],'t.ref','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.ref_ext']['checked'])) print_liste_field_titre($arrayfields['t.ref_ext']['label'],$_SERVER['PHP_SELF'],'t.ref_ext','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.ref_int']['checked'])) print_liste_field_titre($arrayfields['t.ref_int']['label'],$_SERVER['PHP_SELF'],'t.ref_int','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_projet']['checked'])) print_liste_field_titre($arrayfields['t.fk_projet']['label'],$_SERVER['PHP_SELF'],'t.fk_projet','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_poa_prev']['checked'])) print_liste_field_titre($arrayfields['t.fk_poa_prev']['label'],$_SERVER['PHP_SELF'],'t.fk_poa_prev','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_departament']['checked'])) print_liste_field_titre($arrayfields['t.fk_departament']['label'],$_SERVER['PHP_SELF'],'t.fk_departament','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_author']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_author']['label'],$_SERVER['PHP_SELF'],'t.fk_user_author','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_modif']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_modif']['label'],$_SERVER['PHP_SELF'],'t.fk_user_modif','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_valid']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_valid']['label'],$_SERVER['PHP_SELF'],'t.fk_user_valid','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_cloture']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_cloture']['label'],$_SERVER['PHP_SELF'],'t.fk_user_cloture','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.note_private']['checked'])) print_liste_field_titre($arrayfields['t.note_private']['label'],$_SERVER['PHP_SELF'],'t.note_private','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.note_public']['checked'])) print_liste_field_titre($arrayfields['t.note_public']['label'],$_SERVER['PHP_SELF'],'t.note_public','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.model_pdf']['checked'])) print_liste_field_titre($arrayfields['t.model_pdf']['label'],$_SERVER['PHP_SELF'],'t.model_pdf','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.origin']['checked'])) print_liste_field_titre($arrayfields['t.origin']['label'],$_SERVER['PHP_SELF'],'t.origin','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.originid']['checked'])) print_liste_field_titre($arrayfields['t.originid']['label'],$_SERVER['PHP_SELF'],'t.originid','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_shipping_method']['checked'])) print_liste_field_titre($arrayfields['t.fk_shipping_method']['label'],$_SERVER['PHP_SELF'],'t.fk_shipping_method','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.import_key']['checked'])) print_liste_field_titre($arrayfields['t.import_key']['label'],$_SERVER['PHP_SELF'],'t.import_key','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.extraparams']['checked'])) print_liste_field_titre($arrayfields['t.extraparams']['label'],$_SERVER['PHP_SELF'],'t.extraparams','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($arrayfields['t.status']['label'],$_SERVER['PHP_SELF'],'t.status','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.status_process']['checked'])) print_liste_field_titre($arrayfields['t.status_process']['label'],$_SERVER['PHP_SELF'],'t.status_process','',$params,'',$sortfield,$sortorder);

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
if (! empty($arrayfields['t.entity']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_entity" value="'.$search_entity.'" size="10"></td>';
if (! empty($arrayfields['t.ref']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
if (! empty($arrayfields['t.ref_ext']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref_ext" value="'.$search_ref_ext.'" size="10"></td>';
if (! empty($arrayfields['t.ref_int']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref_int" value="'.$search_ref_int.'" size="10"></td>';
if (! empty($arrayfields['t.fk_projet']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_projet" value="'.$search_fk_projet.'" size="10"></td>';
if (! empty($arrayfields['t.fk_poa_prev']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_poa_prev" value="'.$search_fk_poa_prev.'" size="10"></td>';
if (! empty($arrayfields['t.fk_departament']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_departament" value="'.$search_fk_departament.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_author']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_author" value="'.$search_fk_user_author.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_modif']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_modif" value="'.$search_fk_user_modif.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_valid']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_valid" value="'.$search_fk_user_valid.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_cloture']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_cloture" value="'.$search_fk_user_cloture.'" size="10"></td>';
if (! empty($arrayfields['t.note_private']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_note_private" value="'.$search_note_private.'" size="10"></td>';
if (! empty($arrayfields['t.note_public']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_note_public" value="'.$search_note_public.'" size="10"></td>';
if (! empty($arrayfields['t.model_pdf']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_model_pdf" value="'.$search_model_pdf.'" size="10"></td>';
if (! empty($arrayfields['t.origin']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_origin" value="'.$search_origin.'" size="10"></td>';
if (! empty($arrayfields['t.originid']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_originid" value="'.$search_originid.'" size="10"></td>';
if (! empty($arrayfields['t.fk_shipping_method']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_shipping_method" value="'.$search_fk_shipping_method.'" size="10"></td>';
if (! empty($arrayfields['t.import_key']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_import_key" value="'.$search_import_key.'" size="10"></td>';
if (! empty($arrayfields['t.extraparams']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_extraparams" value="'.$search_extraparams.'" size="10"></td>';
if (! empty($arrayfields['t.status']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_status" value="'.$search_status.'" size="10"></td>';
if (! empty($arrayfields['t.status_process']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_status_process" value="'.$search_status_process.'" size="10"></td>';

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
