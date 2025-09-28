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
 *   	\file       mant/mjobsresource_list.php
 *		\ingroup    mant
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-04-18 17:38
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

dol_include_once('/mant/class/mjobsresource.class.php');

// Load traductions files requiredby by page
$langs->load("mant");
$langs->load("other");

$toselect = GETPOST('toselect', 'array');

$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');

$search_all=trim(GETPOST("sall"));

$search_fk_jobs=GETPOST('search_fk_jobs','int');
$search_ref=GETPOST('search_ref','alpha');
$search_fk_sol_almacen=GETPOST('search_fk_sol_almacen','int');
$search_fk_sol_almacendet=GETPOST('search_fk_sol_almacendet','int');
$search_fk_jobs_program=GETPOST('search_fk_jobs_program','alpha');
$search_fk_product=GETPOST('search_fk_product','int');
$search_type_cost=GETPOST('search_type_cost','alpha');
$search_description=GETPOST('search_description','alpha');
$search_quant=GETPOST('search_quant','alpha');
$search_fk_unit=GETPOST('search_fk_unit','int');
$search_price=GETPOST('search_price','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_status=GETPOST('search_status','int');
if (empty($dater)) $dater = dol_now();

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
	't.dater'=>array('label'=>$langs->trans("Fielddate"), 'checked'=>1),
	't.fk_sol_almacen'=>array('label'=>$langs->trans("Fieldfk_sol_almacen"), 'checked'=>1),
	't.fk_sol_almacendet'=>array('label'=>$langs->trans("Fieldfk_sol_almacendet"), 'checked'=>0),
	't.fk_jobs_program'=>array('label'=>$langs->trans("Fieldfk_jobs_program"), 'checked'=>1),
	't.fk_product'=>array('label'=>$langs->trans("Fieldfk_product"), 'checked'=>1),
	't.type_cost'=>array('label'=>$langs->trans("Fieldtype_cost"), 'checked'=>1),
	't.description'=>array('label'=>$langs->trans("Fielddescription"), 'checked'=>1),
	't.quant'=>array('label'=>$langs->trans("Fieldquant"), 'checked'=>1),
	't.fk_unit'=>array('label'=>$langs->trans("Fieldfk_unit"), 'checked'=>1),
	't.price'=>array('label'=>$langs->trans("Fieldprice"), 'checked'=>1),
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
$objJobsresource=new Mjobsresource($db);
if (($idr > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$objJobsresource->fetch($idr,$ref);
	if ($result < 0) dol_print_error($db);
}
$aTypecost = array('MO' =>$langs->trans('Mano de Obra'),'MA'=>$langs->trans('Materiales'),'EQ'=>$langs->trans('Equipos'));

//armamos el optionsprogram
$optionsprogram = '';
$filter = " AND t.fk_jobs = ".$id;
$res = $objectprogram->fetchAll('ASC','t.ref',0,0,array(1=>1),'AND',$filter);
if ($res >0)
{
	$lines = $objectprogram->lines;
	foreach ($lines AS $j => $line)
	{
		$selected = '';
		if (GETPOST('fk_jobs_program') == $line->id)
		{
			$selected = ' selected';
		}
		$optionsprogram.= '<option value="'.$line->id.'" '.$selected.'>'.$line->ref.' '.dol_trunc($line->description,40).'</option>';
	}
}

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

$now=dol_now();

$form=new Form($db);

//$help_url="EN:Module_Customers_Orders|FR:Module_Commandes_Clients|ES:Módulo_Pedidos_de_clientes";
$help_url='';
$title = $langs->trans('Resources');

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

$sql .= " t.fk_jobs,";
$sql .= " t.ref,";
$sql .= " t.fk_sol_almacen,";
$sql .= " t.fk_sol_almacendet,";
$sql .= " t.fk_jobs_program,";
$sql .= " t.fk_product,";
$sql .= " t.dater,";
$sql .= " t.type_cost,";
$sql .= " t.description,";
$sql .= " t.quant,";
$sql .= " t.fk_unit,";
$sql .= " t.price,";
$sql .= " t.fk_user_create,";
$sql .= " t.fk_user_mod,";
$sql .= " t.datec,";
$sql .= " t.datem,";
$sql .= " t.tms,";
$sql .= " t.status";
$sql.= " , p.ref AS refprogram, p.description AS descriptionprogram ";

// Add fields from extrafields
foreach ($extrafields->attribute_label as $key => $val) $sql.=($extrafields->attribute_type[$key] != 'separate' ? ",ef.".$key.' as options_'.$key : '');
// Add fields from hooks
	$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."m_jobs_resource as t";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."m_jobs_program as p ON t.fk_jobs_program = p.rowid";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."m_jobs_resource_extrafields as ef on (t.rowid = ef.fk_object)";
$sql.= " WHERE t.fk_jobs = ".$object->id;
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_fk_jobs) $sql.= natural_search("fk_jobs",$search_fk_jobs);
if ($search_ref) $sql.= natural_search("ref",$search_ref);
if ($search_fk_sol_almacen) $sql.= natural_search("fk_sol_almacen",$search_fk_sol_almacen);
if ($search_fk_sol_almacendet) $sql.= natural_search("fk_sol_almacendet",$search_fk_sol_almacendet);
if ($search_fk_jobs_program) $sql.= natural_search("p.description",$search_fk_jobs_program);
if ($search_fk_product) $sql.= natural_search("fk_product",$search_fk_product);
if ($search_type_cost) $sql.= natural_search("type_cost",$search_type_cost);
if ($search_description) $sql.= natural_search("description",$search_description);
if ($search_quant) $sql.= natural_search("quant",$search_quant);
if ($search_fk_unit) $sql.= natural_search("fk_unit",$search_fk_unit);
if ($search_price) $sql.= natural_search("price",$search_price);
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
	header("Location: ".DOL_URL_ROOT.'/mjobsresource/card.php?id='.$id);
	exit;
}


$arrayofselected=is_array($toselect)?$toselect:array();

$param='';
$param.= "&id=".$object->id;
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

if ($action == 'edit' || $action == 'createma')
{
	if (! empty($conf->use_javascript_ajax))
	{
		print "\n".'<script type="text/javascript">';
		print '$(document).ready(function () {
			$("#fk_sol_almacendet").change(function() {
				document.searchFormList.action.value="'.$action.'";
				document.searchFormList.submit();
			});
		});';
		print '</script>'."\n";
	}
}
print '<form method="POST" id="searchFormList" name="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
if ($action == 'createma' || $action == 'createmo' || $action == 'createeq')
	print '<input type="hidden" name="action" value="add">';
elseif ($action == 'edit')
{
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="idr" value="'.$idr.'">';
}
else
	print '<input type="hidden" name="action" value="list">';
print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
print '<input type="hidden" name="contextpage" value="'.$contextpage.'">';
print '<input type="hidden" name="id" value="'.$object->id.'">';
print '<input type="hidden" name="actionant" value="'.$action.'">';

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
if (! empty($arrayfields['t.fk_jobs']['checked'])) print_liste_field_titre($arrayfields['t.fk_jobs']['label'],$_SERVER['PHP_SELF'],'t.fk_jobs','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.ref']['checked'])) print_liste_field_titre($arrayfields['t.ref']['label'],$_SERVER['PHP_SELF'],'t.ref','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.dater']['checked'])) print_liste_field_titre($arrayfields['t.dater']['label'],$_SERVER['PHP_SELF'],'t.dater','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_jobs_program']['checked'])) print_liste_field_titre($arrayfields['t.fk_jobs_program']['label'],$_SERVER['PHP_SELF'],'t.fk_jobs_program','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_sol_almacen']['checked'])) print_liste_field_titre($arrayfields['t.fk_sol_almacen']['label'],$_SERVER['PHP_SELF'],'t.fk_sol_almacen','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_sol_almacendet']['checked'])) print_liste_field_titre($arrayfields['t.fk_sol_almacendet']['label'],$_SERVER['PHP_SELF'],'t.fk_sol_almacendet','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_product']['checked'])) print_liste_field_titre($arrayfields['t.fk_product']['label'],$_SERVER['PHP_SELF'],'t.fk_product','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.type_cost']['checked'])) print_liste_field_titre($arrayfields['t.type_cost']['label'],$_SERVER['PHP_SELF'],'t.type_cost','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.description']['checked'])) print_liste_field_titre($arrayfields['t.description']['label'],$_SERVER['PHP_SELF'],'t.description','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.quant']['checked'])) print_liste_field_titre($arrayfields['t.quant']['label'],$_SERVER['PHP_SELF'],'t.quant','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_unit']['checked'])) print_liste_field_titre($arrayfields['t.fk_unit']['label'],$_SERVER['PHP_SELF'],'t.fk_unit','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.price']['checked'])) print_liste_field_titre($arrayfields['t.price']['label'],$_SERVER['PHP_SELF'],'t.price','',$param,'align="right"',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_create']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_create']['label'],$_SERVER['PHP_SELF'],'t.fk_user_create','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_mod']['label'],$_SERVER['PHP_SELF'],'t.fk_user_mod','',$param,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($arrayfields['t.status']['label'],$_SERVER['PHP_SELF'],'t.status','',$param,'',$sortfield,$sortorder);

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
//print_liste_field_titre($selectedfields, $_SERVER["PHP_SELF"],"",'',$param,'align="right"',$sortfield,$sortorder,'maxwidthsearch ');
print_liste_field_titre($langs->trans('Action'), $_SERVER["PHP_SELF"],"",'',$param,'align="right"',$sortfield,$sortorder,'maxwidthsearch ');
print '</tr>'."\n";

if ($action != 'createma' && $action != 'createeq' && $action != 'createmo' && $abc)
{
// Fields title search
	print '<tr class="liste_titre">';
//
	if (! empty($arrayfields['t.fk_jobs']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_jobs" value="'.$search_fk_jobs.'" size="10"></td>';
	if (! empty($arrayfields['t.ref']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
	if (! empty($arrayfields['t.dater']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_dater" value="'.$search_dater.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_sol_almacen']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_sol_almacen" value="'.$search_fk_sol_almacen.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_sol_almacendet']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_sol_almacendet" value="'.$search_fk_sol_almacendet.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_product']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_product" value="'.$search_fk_product.'" size="10"></td>';
	if (! empty($arrayfields['t.type_cost']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_type_cost" value="'.$search_type_cost.'" size="10"></td>';
	if (! empty($arrayfields['t.description']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_description" value="'.$search_description.'" size="10"></td>';
	if (! empty($arrayfields['t.quant']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_quant" value="'.$search_quant.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_unit']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_unit" value="'.$search_fk_unit.'" size="10"></td>';
	if (! empty($arrayfields['t.price']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_price" value="'.$search_price.'" size="10"></td>';
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
// Action column
	print '<td class="liste_titre" align="right">';
	$searchpitco=$form->showFilterAndCheckAddButtons(0, 'checkforselect', 1);
	print $searchpitco;
	print '</td>';
	print '</tr>'."\n";
}

$i=0;
$var=true;
$totalarray=array();
$objJobsresourceline = new MjobsresourceLine($db);
if ($action == 'createma') $aTypecost= array('MA'=>$langs->trans('Materials'));
if ($action == 'createmo') $aTypecost= array('MO'=>$langs->trans('Manpower'));
if ($action == 'createeq') $aTypecost= array('EQ'=>$langs->trans('Equipment'));

if ($action == 'createma' ||$action == 'createeq'||$action == 'createmo')
{
	print '<tr '.$bc[$var].'>';
		// LIST_OF_TD_FIELDS_LIST

	if (! empty($arrayfields['t.ref']['checked']))
	{
		$newnum = $num +1;
		print '<td>'.'<input type="text" name="ref" value="'.$newnum.'" readonly size="2">'.'</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.dater']['checked']))
	{
		print '<td>';
		$form->select_date($dater,'dr_',0,0,1);
		print '</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.fk_jobs_program']['checked']))
	{
		print '<td>';
		print '<select name="fk_jobs_program">'.$optionsprogram.'</select>';
		print '</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if ($action == 'createma')
	{
		$lSolalmacen = true;
		if (! empty($arrayfields['t.fk_sol_almacen']['checked']))
		{
			$fk_sol_almacendet = GETPOST('fk_sol_almacendet');

			//listamos los pedidos a almacen
			$filtersol = " AND t.fk_jobs = ".$id;
			$ressol=$objSolalmacendet->fetchAll('','',0,0,array(1=>1),'AND',$filtersol);
			$optionsdet = '<option value="">'.$langs->trans('Select').'</option>';
			foreach ((array) $objSolalmacendet->lines AS $j => $line)
			{
				$selected = '';
				$objProduct->fetch($line->fk_product);
				$aSolalmacen[$line->fk_almacen]=$line->fk_almacen;
				if ($fk_sol_almacendet == $line->id)
				{
					$objProduct->fetch($line->fk_product);
					$fk_unit = $objProduct->fk_unit;
					$fk_sol_almacen = $line->fk_almacen;
					$price = $line->price;
					$selected = ' selected';
				}
				$optionsdet.= '<option value="'.$line->id.'" '.$selected.'>'.$objProduct->ref.' '.$objProduct->label.'</option>';
			}

			if ($aSolalmacen)
			{
				$idsSolalmacen = implode(',',$aSolalmacen);
			}
			if (empty($idsSolalmacen))
			{
				$lSolalmacen = false;
				$idsSolalmacen = 0;
			}
			$filtersol = " AND t.rowid IN (".$idsSolalmacen.")";
			$ressol=$objSolalmacen->fetchAll('','',0,0,array(1=>1),'AND',$filtersol);
			$options = '';
			foreach ((array) $objSolalmacen->lines AS $j => $line)
			{
				$selected = '';
				if ($fk_sol_almacen == $line->id) $selected = ' selected';
				$options.= '<option value="'.$line->id.'" '.$selected.'>'.$line->ref.'</option>';
			}
			print '<td>'.'<select name="fk_sol_almacen" autofocus>'.$options.'</select>'.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}

		if (! empty($arrayfields['t.fk_product']['checked']))
		{
			print '<td>'.'<select id="fk_sol_almacendet" name="fk_sol_almacendet">'.$optionsdet.'</select>'.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
	}
	else
	{
		if (! empty($arrayfields['t.fk_sol_almacen']['checked']))
		{
			print '<td>'.'<input type="hidden" name="fk_sol_almacen" value="0">'.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}

		if (! empty($arrayfields['t.fk_product']['checked']))
		{
			print '<td>'.'<input type="hidden" name="fk_sol_almacendet" value="0">'.'</td>';
			if (! $i) $totalarray['nbfield']++;
		}

	}

	if (! empty($arrayfields['t.type_cost']['checked']))
	{
		print '<td>'.$form->selectarray('type_cost',$aTypecost,GETPOST('type_cost')).'</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.description']['checked']))
	{
		print '<td>'.'<input type="text" name="description" value="'.GETPOST('description').'" required>'.'</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.quant']['checked']))
	{
		print '<td>'.'<input type="number" min="0" step="any" name="quant" value="'.GETPOST('quant').'" required>'.'</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.fk_unit']['checked']))
	{
		print '<td>'.$form->selectUnits($fk_unit,'fk_unit',1).'</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.price']['checked']))
	{
		print '<td align="right">'.'<input type="number" min="0" step="any" name="price" value="'.(GETPOST('price')>0?GETPOST('price'):$price).'" '.($action=='createma'?'readonly':' required').'>'.'</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.status']['checked']))
	{
		print '<td>'.'</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	print '<td class="nowrap">';
	if ($action == 'createma' && !$lSolalmacen)
	{
		setEventMessages($langs->trans('Youdonothavewarehouseordersforthisworkorder'),null,'warnings');
	}
	else
	{
		print '<input class="butAction" type="submit" value="'.$langs->trans('Keep').'">';
	}
	//print '<input type="submit" class="butAction" name="cancel" value="'.$langs->trans('Cancel').'">';
	print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$id.'">'.$langs->trans('Cancel').'</a>';
	print '</td>';
	print '</tr>';
}

while ($i < min($num, $limit))
{
	$obj = $db->fetch_object($resql);
	if ($obj)
	{
		$var = !$var;
		$objJobsresource->id = $obj->id;
		$objJobsresource->status = $obj->status;
		$objJobsresourceline->fk_unit = $obj->fk_unit;
		// Show here line of result
		print '<tr '.$bc[$var].'>';
		// LIST_OF_TD_FIELDS_LIST
		if ($action == 'edit' && $idr == $obj->id)
		{
			if (! empty($arrayfields['t.ref']['checked']))
			{
				print '<td>'.'(PROV)'.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.dater']['checked']))
			{
				print '<td>';
				$form->select_date(($dater?$dater:$obj->dater),'dr_',0,0,1);
				print '</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.fk_jobs_program']['checked']))
			{
				print '<td>';
				print '<select name="fk_jobs_program">'.$optionsprogram.'</select>';
				print '</td>';
				if (! $i) $totalarray['nbfield']++;
			}

			if ($action == 'createma')
			{
				if (! empty($arrayfields['t.fk_sol_almacen']['checked']))
				{
			//listamos los pedidos a almacen
					$filtersol = " AND t.fk_jobs = ".$id;
					$ressol=$objSolalmacendet->fetchAll('','',0,0,array(1=>1),'AND',$filtersol);
					$optionsdet = '<option value="">'.$langs->trans('Select').'</option>';
					foreach ((array) $objSolalmacendet->lines AS $j => $line)
					{
						$objProduct->fetch($line->fk_product);
						$aSolalmacen[$line->fk_almacen]=$line->fk_almacen;
						$optionsdet.= '<option value="'.$line->id.'">'.$objProduct->ref.' '.$objProduct->label.'</option>';
					}
					$idsSolalmacen = implode(',',$aSolalmacen);
					if (empty($idsSolalmacen)) $idsSolalmacen = 0;
					$filtersol = " AND t.rowid IN (".$idsSolalmacen.")";
					$ressol=$objSolalmacen->fetchAll('','',0,0,array(1=>1),'AND',$filtersol);
					$options = '';
					foreach ((array) $objSolalmacen->lines AS $j => $line)
					{
						$options.= '<option value="'.$line->id.'">'.$line->ref.'</option>';
					}
					print '<td>'.'<select name="fk_sol_almacen">'.$options.'</select>'.'</td>';
					if (! $i) $totalarray['nbfield']++;
				}

				if (! empty($arrayfields['t.fk_product']['checked']))
				{
					print '<td>'.'<select id="fk_sol_almacendet" name="fk_sol_almacendet">'.$optionsdet.'</select>'.'</td>';
					if (! $i) $totalarray['nbfield']++;
				}
			}
			else
			{
				if (! empty($arrayfields['t.fk_sol_almacen']['checked']))
				{
					print '<td>'.'<input type="hidden" name="fk_sol_almacen" value="0">'.'</td>';
					if (! $i) $totalarray['nbfield']++;
				}

				if (! empty($arrayfields['t.fk_product']['checked']))
				{
					print '<td>'.'<input type="hidden" name="fk_sol_almacendet" value="0">'.'</td>';
					if (! $i) $totalarray['nbfield']++;
				}

			}

			if (! empty($arrayfields['t.type_cost']['checked']))
			{
				print '<td>'.$form->selectarray('type_cost',$aTypecost,GETPOST('type_cost')).'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.description']['checked']))
			{
				print '<td>'.'<input type="text" name="description" value="'.GETPOST('description').'">'.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.quant']['checked']))
			{
				print '<td>'.'<input type="number" min="0" step="any" name="quant" value="'.GETPOST('quant').'">'.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.fk_unit']['checked']))
			{
				print '<td>'.$form->selectUnits(GETPOST('fk_unit'),'fk_unit',1).'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.price']['checked']))
			{
				print '<td align="right">'.'<input type="number" min="0" step="any" name="price" value="'.GETPOST('price').'">'.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.status']['checked']))
			{
				print '<td>'.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			print '<td>'.'<input type="submit" value="'.$langs->trans('Keep').'">'.'</td>';
		}
		else
		{
			if (! empty($arrayfields['t.ref']['checked']))
			{
				print '<td>'.$obj->ref.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.dater']['checked']))
			{
				print '<td>'.dol_print_date($obj->dater,'day').'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.fk_jobs_program']['checked']))
			{
				print '<td>';
				print $obj->descriptionprogram;
				print '</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.fk_sol_almacen']['checked']))
			{
				if ($obj->fk_sol_almacen>0)
				{
					$objSol->fetch($obj->fk_sol_almacen);
					print '<td>'.$objSol->getNomUrl().'</td>';
				}
				else
					print '<td>'.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.fk_product']['checked']))
			{
				if ($obj->fk_product>0)
				{
					$objProduct->fetch($obj->fk_product);
					print '<td>'.$objProduct->getNomUrl().'</td>';
				}
				else
					print '<td>'.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.type_cost']['checked']))
			{
				print '<td>'.$obj->type_cost.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.description']['checked']))
			{
				print '<td>'.$obj->description.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.quant']['checked']))
			{
				print '<td>'.$obj->quant.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.fk_unit']['checked']))
			{

				print '<td>'.$langs->trans($objJobsresourceline->getLabelOfUnit()).'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.price']['checked']))
			{
				print '<td align="right">'.price($obj->price).'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.status']['checked']))
			{
				print '<td>'.$objJobsresource->getLibStatut(6).'</td>';
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

		// Action column
			print '<td class="nowrap" align="center">';
			if ($massactionbutton || $massaction)
		   // If we are in select mode (massactionbutton defined) or if we have already selected and sent an action ($massaction) defined
			{
				$selected=0;
				if (in_array($obj->rowid, $arrayofselected)) $selected=1;
			//print '<input id="cb'.$obj->rowid.'" class="flat checkforselect" type="checkbox" name="toselect[]" value="'.$obj->rowid.'"'.($selected?' checked="checked"':'').'>';
			}
			print '</td>';
			if (! $i) $totalarray['nbfield']++;
		}
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

/* **************************** */
/*                                            */
/* Barre d'action                       */
/*                                            */
/* **************************** */

print "<div class=\"tabsAction\">\n";

if ($action == '')
{
		 		//programacion de trabajos
	if ($object->status == 4 && $user->rights->mant->jobs->regjobs )
	{
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=createma&amp;id='.$object->id.'">'.$langs->trans('Materials').'</a>';
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=createeq&amp;id='.$object->id.'">'.$langs->trans('Equipment').'</a>';
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=createmo&amp;id='.$object->id.'">'.$langs->trans('Manpower').'</a>';
	}

		  		//ejecutar trabajo

		  		//impres ot
	if ($object->status == 5)
	{
			//print '<a class="butAction" href="'.DOL_URL_ROOT.'/mant/jobs/fiche_excel.php'.'?id='.$object->id.'">'.$langs->trans('Excel').'</a>';
	}

		  		// open jobs
	if (($object->status == 5 || $object->status == 8) && $user->rights->mant->jobs->openwork)
	{
			//print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=openwork">'.$langs->trans('Openwork').'</a>';
	}

}

print '</div>';
