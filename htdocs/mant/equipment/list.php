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
 *   	\file       mant/mequipment_list.php
 *		\ingroup    mant
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-04-07 18:07
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
dol_include_once('/mant/class/mequipmentext.class.php');

// Load traductions files requiredby by page
$langs->load("mant");
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
$search_label=GETPOST('search_label','alpha');
$search_metered=GETPOST('search_metered','int');
$search_accountant=GETPOST('search_accountant','int');
$search_fk_unit=GETPOST('search_fk_unit','int');
$search_margin=GETPOST('search_margin','int');
$search_trademark=GETPOST('search_trademark','alpha');
$search_model=GETPOST('search_model','alpha');
$search_anio=GETPOST('search_anio','alpha');
$search_fk_location=GETPOST('search_fk_location','int');
$search_fk_asset=GETPOST('search_fk_asset','int');
$search_hour_cost=GETPOST('search_hour_cost','alpha');
$search_fk_equipment_program=GETPOST('search_fk_equipment_program','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_active=GETPOST('search_active','int');
$search_status=GETPOST('search_status','int');


$search_myfield=GETPOST('search_myfield');
$optioncss = GETPOST('optioncss','alpha');

// Load variable for pagination
$limit = GETPOST("limit")?GETPOST("limit","int"):$conf->liste_limit;
$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha');
$page = GETPOST('page','int');
if (empty($page) || $page == -1) { $page = 0; }
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
$contextpage=GETPOST('contextpage','aZ')?GETPOST('contextpage','aZ'):'mantlist';

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('mantlist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('mant');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// List of fields to search into when doing a "search in all"
$fieldstosearchall = array(
	't.ref'=>'Ref',
	't.note_public'=>'NotePublic',
	);
if (empty($user->socid)) $fieldstosearchall["t.note_private"]="NotePrivate";

// Definition of fields for list
$arrayfields=array(

	't.ref'=>array('label'=>$langs->trans("Fieldref"), 'checked'=>1),
	't.ref_ext'=>array('label'=>$langs->trans("Fieldref_ext"), 'checked'=>1),
	't.label'=>array('label'=>$langs->trans("Fieldlabel"), 'checked'=>1),
	't.metered'=>array('label'=>$langs->trans("Fieldmetered"), 'checked'=>1),
	't.accountant'=>array('label'=>$langs->trans("Fieldaccountant"), 'checked'=>1),
	't.fk_unit'=>array('label'=>$langs->trans("Fieldfk_unit"), 'checked'=>1),
	't.margin'=>array('label'=>$langs->trans("Fieldmargin"), 'checked'=>1),
	't.trademark'=>array('label'=>$langs->trans("Fieldtrademark"), 'checked'=>0),
	't.model'=>array('label'=>$langs->trans("Fieldmodel"), 'checked'=>0),
	't.anio'=>array('label'=>$langs->trans("Fieldanio"), 'checked'=>0),
	't.fk_location'=>array('label'=>$langs->trans("Fieldfk_location"), 'checked'=>1),
	't.fk_asset'=>array('label'=>$langs->trans("Fieldfk_asset"), 'checked'=>0),
	't.hour_cost'=>array('label'=>$langs->trans("Fieldhour_cost"), 'checked'=>1),
	't.fk_equipment_program'=>array('label'=>$langs->trans("Fieldfk_equipment_program"), 'checked'=>0),
	't.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'checked'=>0),
	't.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'checked'=>0),
	't.active'=>array('label'=>$langs->trans("Fieldactive"), 'checked'=>1),
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
$object=new Mequipmentext($db);
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
		$search_label='';
		$search_metered='';
		$search_accountant='';
		$search_fk_unit='';
		$search_margin='';
		$search_trademark='';
		$search_model='';
		$search_anio='';
		$search_fk_location='';
		$search_fk_asset='';
		$search_hour_cost='';
		$search_fk_equipment_program='';
		$search_fk_user_create='';
		$search_fk_user_mod='';
		$search_active='';
		$search_status='';


		$search_date_creation='';
		$search_date_update='';
		$toselect='';
		$search_array_options=array();
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
$title = $langs->trans('Teams');

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
$sql .= " t.label,";
$sql .= " t.metered,";
$sql .= " t.accountant,";
$sql .= " t.fk_unit,";
$sql .= " t.margin,";
$sql .= " t.trademark,";
$sql .= " t.model,";
$sql .= " t.anio,";
$sql .= " t.fk_location,";
$sql .= " t.fk_asset,";
$sql .= " t.hour_cost,";
$sql .= " t.fk_equipment_program,";
$sql .= " t.fk_user_create,";
$sql .= " t.fk_user_mod,";
$sql .= " t.datec,";
$sql .= " t.datem,";
$sql .= " t.tms,";
$sql .= " t.active,";
$sql .= " t.status";


// Add fields from extrafields
foreach ($extrafields->attribute_label as $key => $val) $sql.=($extrafields->attribute_type[$key] != 'separate' ? ",ef.".$key.' as options_'.$key : '');
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."m_equipment as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."m_equipment_extrafields as ef on (t.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_entity) $sql.= natural_search("entity",$search_entity);
if ($search_ref) $sql.= natural_search("ref",$search_ref);
if ($search_ref_ext) $sql.= natural_search("ref_ext",$search_ref_ext);
if ($search_label) $sql.= natural_search("label",$search_label);
if ($search_metered) $sql.= natural_search("metered",$search_metered);
if ($search_accountant) $sql.= natural_search("accountant",$search_accountant);
if ($search_fk_unit) $sql.= natural_search("fk_unit",$search_fk_unit);
if ($search_margin) $sql.= natural_search("margin",$search_margin);
if ($search_trademark) $sql.= natural_search("trademark",$search_trademark);
if ($search_model) $sql.= natural_search("model",$search_model);
if ($search_anio) $sql.= natural_search("anio",$search_anio);
if ($search_fk_location) $sql.= natural_search("fk_location",$search_fk_location);
if ($search_fk_asset) $sql.= natural_search("fk_asset",$search_fk_asset);
if ($search_hour_cost) $sql.= natural_search("hour_cost",$search_hour_cost);
if ($search_fk_equipment_program) $sql.= natural_search("fk_equipment_program",$search_fk_equipment_program);
if ($search_fk_user_create) $sql.= natural_search("fk_user_create",$search_fk_user_create);
if ($search_fk_user_mod) $sql.= natural_search("fk_user_mod",$search_fk_user_mod);
if ($search_active) $sql.= natural_search("active",$search_active);
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
	header("Location: ".DOL_URL_ROOT.'/mequipment/card.php?id='.$id);
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
if ($user->rights->mant->supprimer) $arrayofmassactions['delete']=$langs->trans("Delete");
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
//$moreforfilter.='<div class="divsearchfield">';
//$moreforfilter.= $langs->trans('MyFilter') . ': <input type="text" name="search_myfield" value="'.dol_escape_htmltag($search_myfield).'">';
//$moreforfilter.= '</div>';

$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldPreListTitle',$parameters);
// Note that $action and $object may have been modified by hook
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
if (! empty($arrayfields['t.label']['checked'])) print_liste_field_titre($arrayfields['t.label']['label'],$_SERVER['PHP_SELF'],'t.label','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.metered']['checked'])) print_liste_field_titre($arrayfields['t.metered']['label'],$_SERVER['PHP_SELF'],'t.metered','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.accountant']['checked'])) print_liste_field_titre($arrayfields['t.accountant']['label'],$_SERVER['PHP_SELF'],'t.accountant','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_unit']['checked'])) print_liste_field_titre($arrayfields['t.fk_unit']['label'],$_SERVER['PHP_SELF'],'t.fk_unit','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.margin']['checked'])) print_liste_field_titre($arrayfields['t.margin']['label'],$_SERVER['PHP_SELF'],'t.margin','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.trademark']['checked'])) print_liste_field_titre($arrayfields['t.trademark']['label'],$_SERVER['PHP_SELF'],'t.trademark','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.model']['checked'])) print_liste_field_titre($arrayfields['t.model']['label'],$_SERVER['PHP_SELF'],'t.model','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.anio']['checked'])) print_liste_field_titre($arrayfields['t.anio']['label'],$_SERVER['PHP_SELF'],'t.anio','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_location']['checked'])) print_liste_field_titre($arrayfields['t.fk_location']['label'],$_SERVER['PHP_SELF'],'t.fk_location','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_asset']['checked'])) print_liste_field_titre($arrayfields['t.fk_asset']['label'],$_SERVER['PHP_SELF'],'t.fk_asset','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.hour_cost']['checked'])) print_liste_field_titre($arrayfields['t.hour_cost']['label'],$_SERVER['PHP_SELF'],'t.hour_cost','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_equipment_program']['checked'])) print_liste_field_titre($arrayfields['t.fk_equipment_program']['label'],$_SERVER['PHP_SELF'],'t.fk_equipment_program','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_create']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_create']['label'],$_SERVER['PHP_SELF'],'t.fk_user_create','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_mod']['label'],$_SERVER['PHP_SELF'],'t.fk_user_mod','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.active']['checked'])) print_liste_field_titre($arrayfields['t.active']['label'],$_SERVER['PHP_SELF'],'t.active','',$params,'',$sortfield,$sortorder);
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
if (! empty($arrayfields['t.entity']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_entity" value="'.$search_entity.'" size="10"></td>';
if (! empty($arrayfields['t.ref']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
if (! empty($arrayfields['t.ref_ext']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref_ext" value="'.$search_ref_ext.'" size="10"></td>';
if (! empty($arrayfields['t.label']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_label" value="'.$search_label.'" size="10"></td>';
if (! empty($arrayfields['t.metered']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_metered" value="'.$search_metered.'" size="10"></td>';
if (! empty($arrayfields['t.accountant']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_accountant" value="'.$search_accountant.'" size="10"></td>';
if (! empty($arrayfields['t.fk_unit']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_unit" value="'.$search_fk_unit.'" size="10"></td>';
if (! empty($arrayfields['t.margin']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_margin" value="'.$search_margin.'" size="10"></td>';
if (! empty($arrayfields['t.trademark']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_trademark" value="'.$search_trademark.'" size="10"></td>';
if (! empty($arrayfields['t.model']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_model" value="'.$search_model.'" size="10"></td>';
if (! empty($arrayfields['t.anio']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_anio" value="'.$search_anio.'" size="10"></td>';
if (! empty($arrayfields['t.fk_location']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_location" value="'.$search_fk_location.'" size="10"></td>';
if (! empty($arrayfields['t.fk_asset']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_asset" value="'.$search_fk_asset.'" size="10"></td>';
if (! empty($arrayfields['t.hour_cost']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_hour_cost" value="'.$search_hour_cost.'" size="10"></td>';
if (! empty($arrayfields['t.fk_equipment_program']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_equipment_program" value="'.$search_fk_equipment_program.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_create']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_create" value="'.$search_fk_user_create.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_mod" value="'.$search_fk_user_mod.'" size="10"></td>';
if (! empty($arrayfields['t.active']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_active" value="'.$search_active.'" size="10"></td>';
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
$reshook=$hookmanager->executeHooks('printFieldListOption',$parameters);
// Note that $action and $object may have been modified by hook
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
$searchpitco=$form->showFilterAndCheckAddButtons($massactionbutton?0:0, 'checkforselect', 1);
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
		$object->id = $obj->rowid;
		$object->ref = $obj->ref;
		$object->label = $obj->label;
		$object->status = $obj->status;

		$var = !$var;

		// Show here line of result
		print '<tr '.$bc[$var].'>';
		// LIST_OF_TD_FIELDS_LIST

		if (! empty($arrayfields['t.ref']['checked']))
		{
			print '<td>'.$object->getNomUrl().'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.ref_ext']['checked']))
		{
			print '<td>'.$obj->ref_ext.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.label']['checked']))
		{
			print '<td>'.$obj->label.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.metered']['checked']))
		{
			print '<td>'.($obj->metered?$langs->trans('Yes'):$langs->trans('No')).'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.accountant']['checked']))
		{
			print '<td>'.$obj->accountant.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.fk_unit']['checked']))
		{
			print '<td>'.$obj->fk_unit.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.margin']['checked']))
		{
			print '<td>'.$obj->margin.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.trademark']['checked']))
		{
			print '<td>'.$obj->trademark.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.model']['checked']))
		{
			print '<td>'.$obj->model.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.anio']['checked']))
		{
			print '<td>'.$obj->anio.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.fk_location']['checked']))
		{
			print '<td>'.$obj->fk_location.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.fk_asset']['checked']))
		{
			print '<td>'.$obj->fk_asset.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.hour_cost']['checked']))
		{
			print '<td>'.$obj->hour_cost.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.fk_equipment_program']['checked']))
		{
			print '<td>'.$obj->fk_equipment_program.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.active']['checked']))
		{
			$img = 'switch_off';
			if ($obj->active) $img = 'switch_on';
			print '<td>'.img_picto('',$img).'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		if (! empty($arrayfields['t.status']['checked']))
		{
			print '<td>'.$object->getLibStatut(4).'</td>';
			if (! $i) $totalarray['nbfield']++;
		}

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
		$reshook=$hookmanager->executeHooks('printFieldListValue',$parameters);
		 // Note that $action and $object may have been modified by hook
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
$reshook=$hookmanager->executeHooks('printFieldListFooter',$parameters);
// Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;

print '</table>'."\n";
print '</div>'."\n";

print '</form>'."\n";




// End of page
llxFooter();
$db->close();
