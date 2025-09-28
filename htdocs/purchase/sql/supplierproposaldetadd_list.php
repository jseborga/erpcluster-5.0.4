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
 *   	\file       purchase/supplierproposaldetadd_list.php
 *		\ingroup    purchase
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-05-04 13:57
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
dol_include_once('/purchase/class/supplierproposaldetadd.class.php');

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

$search_fk_supplier_proposaldet=GETPOST('search_fk_supplier_proposaldet','int');
$search_fk_object=GETPOST('search_fk_object','int');
$search_object=GETPOST('search_object','alpha');
$search_fk_fabrication=GETPOST('search_fk_fabrication','int');
$search_fk_fabricationdet=GETPOST('search_fk_fabricationdet','int');
$search_fk_projet=GETPOST('search_fk_projet','int');
$search_fk_projet_task=GETPOST('search_fk_projet_task','int');
$search_fk_jobs=GETPOST('search_fk_jobs','int');
$search_fk_jobsdet=GETPOST('search_fk_jobsdet','int');
$search_fk_structure=GETPOST('search_fk_structure','int');
$search_fk_poa=GETPOST('search_fk_poa','int');
$search_partida=GETPOST('search_partida','alpha');
$search_amount_ice=GETPOST('search_amount_ice','alpha');
$search_discount=GETPOST('search_discount','alpha');
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
    
't.fk_supplier_proposaldet'=>array('label'=>$langs->trans("Fieldfk_supplier_proposaldet"), 'checked'=>1),
't.fk_object'=>array('label'=>$langs->trans("Fieldfk_object"), 'checked'=>1),
't.object'=>array('label'=>$langs->trans("Fieldobject"), 'checked'=>1),
't.fk_fabrication'=>array('label'=>$langs->trans("Fieldfk_fabrication"), 'checked'=>1),
't.fk_fabricationdet'=>array('label'=>$langs->trans("Fieldfk_fabricationdet"), 'checked'=>1),
't.fk_projet'=>array('label'=>$langs->trans("Fieldfk_projet"), 'checked'=>1),
't.fk_projet_task'=>array('label'=>$langs->trans("Fieldfk_projet_task"), 'checked'=>1),
't.fk_jobs'=>array('label'=>$langs->trans("Fieldfk_jobs"), 'checked'=>1),
't.fk_jobsdet'=>array('label'=>$langs->trans("Fieldfk_jobsdet"), 'checked'=>1),
't.fk_structure'=>array('label'=>$langs->trans("Fieldfk_structure"), 'checked'=>1),
't.fk_poa'=>array('label'=>$langs->trans("Fieldfk_poa"), 'checked'=>1),
't.partida'=>array('label'=>$langs->trans("Fieldpartida"), 'checked'=>1),
't.amount_ice'=>array('label'=>$langs->trans("Fieldamount_ice"), 'checked'=>1),
't.discount'=>array('label'=>$langs->trans("Fielddiscount"), 'checked'=>1),
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


// Load object if id or ref is provided as parameter
$object=new Supplierproposaldetadd($db);
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
    	
$search_fk_supplier_proposaldet='';
$search_fk_object='';
$search_object='';
$search_fk_fabrication='';
$search_fk_fabricationdet='';
$search_fk_projet='';
$search_fk_projet_task='';
$search_fk_jobs='';
$search_fk_jobsdet='';
$search_fk_structure='';
$search_fk_poa='';
$search_partida='';
$search_amount_ice='';
$search_discount='';
$search_fk_user_create='';
$search_fk_user_mod='';
$search_status='';

    	
    	$search_date_creation='';
    	$search_date_update='';
        $toselect='';
        $search_array_options=array();
    }

    // Mass actions
    $objectclass='Skeleton';
    $objectlabel='Skeleton';
    $permtoread = $user->rights->supplierproposaldetadd->read;
    $permtodelete = $user->rights->supplierproposaldetadd->delete;
    $uploaddir = $conf->supplierproposaldetadd->dir_output;
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

		$sql .= " t.fk_supplier_proposaldet,";
		$sql .= " t.fk_object,";
		$sql .= " t.object,";
		$sql .= " t.fk_fabrication,";
		$sql .= " t.fk_fabricationdet,";
		$sql .= " t.fk_projet,";
		$sql .= " t.fk_projet_task,";
		$sql .= " t.fk_jobs,";
		$sql .= " t.fk_jobsdet,";
		$sql .= " t.fk_structure,";
		$sql .= " t.fk_poa,";
		$sql .= " t.partida,";
		$sql .= " t.amount_ice,";
		$sql .= " t.discount,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.datec,";
		$sql .= " t.datem,";
		$sql .= " t.tms,";
		$sql .= " t.status";


// Add fields from extrafields
foreach ($extrafields->attribute_label as $key => $val) $sql.=($extrafields->attribute_type[$key] != 'separate' ? ",ef.".$key.' as options_'.$key : '');
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."supplier_proposaldet_add as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."supplier_proposaldet_add_extrafields as ef on (t.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_fk_supplier_proposaldet) $sql.= natural_search("fk_supplier_proposaldet",$search_fk_supplier_proposaldet);
if ($search_fk_object) $sql.= natural_search("fk_object",$search_fk_object);
if ($search_object) $sql.= natural_search("object",$search_object);
if ($search_fk_fabrication) $sql.= natural_search("fk_fabrication",$search_fk_fabrication);
if ($search_fk_fabricationdet) $sql.= natural_search("fk_fabricationdet",$search_fk_fabricationdet);
if ($search_fk_projet) $sql.= natural_search("fk_projet",$search_fk_projet);
if ($search_fk_projet_task) $sql.= natural_search("fk_projet_task",$search_fk_projet_task);
if ($search_fk_jobs) $sql.= natural_search("fk_jobs",$search_fk_jobs);
if ($search_fk_jobsdet) $sql.= natural_search("fk_jobsdet",$search_fk_jobsdet);
if ($search_fk_structure) $sql.= natural_search("fk_structure",$search_fk_structure);
if ($search_fk_poa) $sql.= natural_search("fk_poa",$search_fk_poa);
if ($search_partida) $sql.= natural_search("partida",$search_partida);
if ($search_amount_ice) $sql.= natural_search("amount_ice",$search_amount_ice);
if ($search_discount) $sql.= natural_search("discount",$search_discount);
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
    header("Location: ".DOL_URL_ROOT.'/supplierproposaldetadd/card.php?id='.$id);
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
if (! empty($arrayfields['t.fk_supplier_proposaldet']['checked'])) print_liste_field_titre($arrayfields['t.fk_supplier_proposaldet']['label'],$_SERVER['PHP_SELF'],'t.fk_supplier_proposaldet','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_object']['checked'])) print_liste_field_titre($arrayfields['t.fk_object']['label'],$_SERVER['PHP_SELF'],'t.fk_object','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.object']['checked'])) print_liste_field_titre($arrayfields['t.object']['label'],$_SERVER['PHP_SELF'],'t.object','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_fabrication']['checked'])) print_liste_field_titre($arrayfields['t.fk_fabrication']['label'],$_SERVER['PHP_SELF'],'t.fk_fabrication','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_fabricationdet']['checked'])) print_liste_field_titre($arrayfields['t.fk_fabricationdet']['label'],$_SERVER['PHP_SELF'],'t.fk_fabricationdet','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_projet']['checked'])) print_liste_field_titre($arrayfields['t.fk_projet']['label'],$_SERVER['PHP_SELF'],'t.fk_projet','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_projet_task']['checked'])) print_liste_field_titre($arrayfields['t.fk_projet_task']['label'],$_SERVER['PHP_SELF'],'t.fk_projet_task','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_jobs']['checked'])) print_liste_field_titre($arrayfields['t.fk_jobs']['label'],$_SERVER['PHP_SELF'],'t.fk_jobs','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_jobsdet']['checked'])) print_liste_field_titre($arrayfields['t.fk_jobsdet']['label'],$_SERVER['PHP_SELF'],'t.fk_jobsdet','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_structure']['checked'])) print_liste_field_titre($arrayfields['t.fk_structure']['label'],$_SERVER['PHP_SELF'],'t.fk_structure','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_poa']['checked'])) print_liste_field_titre($arrayfields['t.fk_poa']['label'],$_SERVER['PHP_SELF'],'t.fk_poa','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.partida']['checked'])) print_liste_field_titre($arrayfields['t.partida']['label'],$_SERVER['PHP_SELF'],'t.partida','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount_ice']['checked'])) print_liste_field_titre($arrayfields['t.amount_ice']['label'],$_SERVER['PHP_SELF'],'t.amount_ice','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.discount']['checked'])) print_liste_field_titre($arrayfields['t.discount']['label'],$_SERVER['PHP_SELF'],'t.discount','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_create']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_create']['label'],$_SERVER['PHP_SELF'],'t.fk_user_create','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_mod']['label'],$_SERVER['PHP_SELF'],'t.fk_user_mod','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($arrayfields['t.status']['label'],$_SERVER['PHP_SELF'],'t.status','',$params,'',$sortfield,$sortorder);

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
if (! empty($arrayfields['t.fk_supplier_proposaldet']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_supplier_proposaldet" value="'.$search_fk_supplier_proposaldet.'" size="10"></td>';
if (! empty($arrayfields['t.fk_object']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_object" value="'.$search_fk_object.'" size="10"></td>';
if (! empty($arrayfields['t.object']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_object" value="'.$search_object.'" size="10"></td>';
if (! empty($arrayfields['t.fk_fabrication']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_fabrication" value="'.$search_fk_fabrication.'" size="10"></td>';
if (! empty($arrayfields['t.fk_fabricationdet']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_fabricationdet" value="'.$search_fk_fabricationdet.'" size="10"></td>';
if (! empty($arrayfields['t.fk_projet']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_projet" value="'.$search_fk_projet.'" size="10"></td>';
if (! empty($arrayfields['t.fk_projet_task']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_projet_task" value="'.$search_fk_projet_task.'" size="10"></td>';
if (! empty($arrayfields['t.fk_jobs']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_jobs" value="'.$search_fk_jobs.'" size="10"></td>';
if (! empty($arrayfields['t.fk_jobsdet']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_jobsdet" value="'.$search_fk_jobsdet.'" size="10"></td>';
if (! empty($arrayfields['t.fk_structure']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_structure" value="'.$search_fk_structure.'" size="10"></td>';
if (! empty($arrayfields['t.fk_poa']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_poa" value="'.$search_fk_poa.'" size="10"></td>';
if (! empty($arrayfields['t.partida']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_partida" value="'.$search_partida.'" size="10"></td>';
if (! empty($arrayfields['t.amount_ice']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount_ice" value="'.$search_amount_ice.'" size="10"></td>';
if (! empty($arrayfields['t.discount']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_discount" value="'.$search_discount.'" size="10"></td>';
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
