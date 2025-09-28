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
 *   	\file       mant/mequipmenthistorial_list.php
 *		\ingroup    mant
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-04-11 08:33
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

dol_include_once('/mant/class/mequipmenthistorial.class.php');

// Load traductions files requiredby by page
$langs->load("mant");
$langs->load("other");

$massaction=GETPOST('massaction','alpha');
$show_files=GETPOST('show_files','int');
$confirm=GETPOST('confirm','alpha');
$toselect = GETPOST('toselect', 'array');

$id			= GETPOST('id','int');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');

$search_all=trim(GETPOST("sall"));

$search_fk_equipment=GETPOST('search_fk_equipment','int');
$search_ref_ext=GETPOST('search_ref_ext','alpha');
$search_accountant=GETPOST('search_accountant','alpha');
$search_accountant_last=GETPOST('search_accountant_last','alpha');
$search_description=GETPOST('search_description','alpha');
$search_pc_ip=GETPOST('search_pc_ip','alpha');
$search_origin=GETPOST('search_origin','alpha');
$search_originid=GETPOST('search_originid','int');
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
if (empty($page) || $page == -1) { $page = 0; }
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortfield) $sortfield="t.rowid"; // Set here default search field
if (! $sortorder) $sortorder="DESC";

// Protection if external user
$socid=0;
if ($user->societe_id > 0)
{
	$socid = $user->societe_id;
	//accessforbidden();
}

// Initialize technical object to manage context to save list fields
$contextpage=GETPOST('contextpage','aZ')?GETPOST('contextpage','aZ'):'mantequipmenthistorylist';

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

	't.ref_ext'=>array('label'=>$langs->trans("Fieldref_ext"), 'checked'=>1),
	't.accountant'=>array('label'=>$langs->trans("Currentcounter"), 'checked'=>1),
	't.accountant_last'=>array('label'=>$langs->trans("Previouscounter"), 'checked'=>1),
	't.description'=>array('label'=>$langs->trans("Fielddescription"), 'checked'=>1),
	't.pc_ip'=>array('label'=>$langs->trans("Fieldpc_ip"), 'checked'=>0),
	't.origin'=>array('label'=>$langs->trans("Fieldorigin"), 'checked'=>0),
	't.originid'=>array('label'=>$langs->trans("Fieldoriginid"), 'checked'=>0),
	't.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'checked'=>0),
	't.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'checked'=>0),
	't.status'=>array('label'=>$langs->trans("Fieldstatus"), 'checked'=>0),


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
$objecth=new Mequipmenthistorial($db);
if (($idr > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$objecth->fetch($idr,$ref);
	if ($result < 0) dol_print_error($db);
}




/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

if (GETPOST('cancel')) { $action='list'; $massaction=''; }
if (! GETPOST('confirmmassaction') && $massaction != 'presend' && $massaction != 'confirm_presend') { $massaction=''; }


/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

$now=dol_now();

$form=new Form($db);

//$help_url="EN:Module_Customers_Orders|FR:Module_Commandes_Clients|ES:Módulo_Pedidos_de_clientes";
$help_url='';
$title = $langs->trans('Historyaccountant');

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

$sql .= " t.fk_equipment,";
$sql .= " t.ref_ext,";
$sql .= " t.accountant,";
$sql .= " t.accountant_last,";
$sql .= " t.description,";
$sql .= " t.pc_ip,";
$sql .= " t.origin,";
$sql .= " t.originid,";
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
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);
// Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."m_equipment_historial as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."m_equipment_historial_extrafields as ef on (t.rowid = ef.fk_object)";
$sql.= " WHERE t.fk_equipment = ".$object->id;
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_fk_equipment) $sql.= natural_search("fk_equipment",$search_fk_equipment);
if ($search_ref_ext) $sql.= natural_search("ref_ext",$search_ref_ext);
if ($search_accountant) $sql.= natural_search("accountant",$search_accountant);
if ($search_accountant_last) $sql.= natural_search("accountant_last",$search_accountant_last);
if ($search_description) $sql.= natural_search("description",$search_description);
if ($search_pc_ip) $sql.= natural_search("pc_ip",$search_pc_ip);
if ($search_origin) $sql.= natural_search("origin",$search_origin);
if ($search_originid) $sql.= natural_search("originid",$search_originid);
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
	if (in_array($typ, array('int','double'))) $mode=1;
	// Search on a numeric
	if ($val && ( ($crit != '' && ! in_array($typ, array('select'))) || ! empty($crit)))
	{
		$sql .= natural_search('ef.'.$tmpkey, $crit, $mode);
	}
}
// Add where from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListWhere',$parameters);
// Note that $action and $object may have been modified by hook
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
	header("Location: ".DOL_URL_ROOT.'/mequipmenthistorial/card.php?id='.$id);
	exit;
}

$arrayofselected=is_array($toselect)?$toselect:array();

$params='';
if (! empty($contextpage) && $contextpage != $_SERVER["PHP_SELF"]) $params.='&contextpage='.$contextpage;
$params.= '&id='.$object->id;
if ($limit > 0 && $limit != $conf->liste_limit) $params.='&limit='.$limit;
if ($search_field1 != '') $params.= '&amp;search_field1='.urlencode($search_field1);
if ($search_field2 != '') $params.= '&amp;search_field2='.urlencode($search_field2);
if ($optioncss != '') $params.='&optioncss='.$optioncss;
// Add $param from extra fields
foreach ($search_array_options as $key => $val)
{
	$crit=$val;
	$tmpkey=preg_replace('/search_options_/','',$key);
	if ($val != '') $params.='&search_options_'.$tmpkey.'='.urlencode($val);
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
print '<input type="hidden" name="id" value="'.$object->id.'">';
if ($action == 'create')
	print '<input type="hidden" name="action" value="add">';
elseif ($action == 'edit')
{
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="idr" value="'.$objecth->id.'">';
}
else
	print '<input type="hidden" name="action" value="list">';
print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
print '<input type="hidden" name="contextpage" value="'.$contextpage.'">';

print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $params, $sortfield, $sortorder, '', $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);

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
$selectedfields=$form->multiSelectArrayWithCheckbox('selectedfields', $arrayfields, $varpage);
// This also change content of $arrayfields

print '<div class="div-table-responsive">';
print '<table class="tagtable liste'.($moreforfilter?" listwithfilterbefore":"").'">'."\n";

// Fields title
print '<tr class="liste_titre">';
//
if (! empty($arrayfields['t.fk_equipment']['checked'])) print_liste_field_titre($arrayfields['t.fk_equipment']['label'],$_SERVER['PHP_SELF'],'t.fk_equipment','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.ref_ext']['checked'])) print_liste_field_titre($arrayfields['t.ref_ext']['label'],$_SERVER['PHP_SELF'],'t.ref_ext','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.accountant']['checked'])) print_liste_field_titre($arrayfields['t.accountant']['label'],$_SERVER['PHP_SELF'],'t.accountant','',$params,' align="right"',$sortfield,$sortorder);
if (! empty($arrayfields['t.accountant_last']['checked'])) print_liste_field_titre($arrayfields['t.accountant_last']['label'],$_SERVER['PHP_SELF'],'t.accountant_last','',$params,' align="right"',$sortfield,$sortorder);
if (! empty($arrayfields['t.description']['checked'])) print_liste_field_titre($arrayfields['t.description']['label'],$_SERVER['PHP_SELF'],'t.description','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.pc_ip']['checked'])) print_liste_field_titre($arrayfields['t.pc_ip']['label'],$_SERVER['PHP_SELF'],'t.pc_ip','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.origin']['checked'])) print_liste_field_titre($arrayfields['t.origin']['label'],$_SERVER['PHP_SELF'],'t.origin','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.originid']['checked'])) print_liste_field_titre($arrayfields['t.originid']['label'],$_SERVER['PHP_SELF'],'t.originid','',$params,'',$sortfield,$sortorder);
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
			print_liste_field_titre($extralabels[$key],$_SERVER["PHP_SELF"],"ef.".$key,"",$params,($align?'align="'.$align.'"':''),$sortfield,$sortorder);
		}
	}
}
// Hook fields
$parameters=array('arrayfields'=>$arrayfields);
$reshook=$hookmanager->executeHooks('printFieldListTitle',$parameters);
// Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;
if (! empty($arrayfields['t.datec']['checked']))  print_liste_field_titre($arrayfields['t.datec']['label'],$_SERVER["PHP_SELF"],"t.datec","",$params,'align="center" class="nowrap"',$sortfield,$sortorder);
if (! empty($arrayfields['t.tms']['checked']))    print_liste_field_titre($arrayfields['t.tms']['label'],$_SERVER["PHP_SELF"],"t.tms","",$params,'align="center" class="nowrap"',$sortfield,$sortorder);
//if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($langs->trans("Status"),$_SERVER["PHP_SELF"],"t.status","",$param,'align="center"',$sortfield,$sortorder);
print_liste_field_titre($selectedfields, $_SERVER["PHP_SELF"],"",'','','align="right"',$sortfield,$sortorder,'maxwidthsearch ');
print '</tr>'."\n";

if ($action != 'create' && $action != 'edit')
{
// Fields title search
	print '<tr class="liste_titre">';
//
	if (! empty($arrayfields['t.fk_equipment']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_equipment" value="'.$search_fk_equipment.'" size="10"></td>';
	if (! empty($arrayfields['t.ref_ext']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ref_ext" value="'.$search_ref_ext.'" size="10"></td>';
	if (! empty($arrayfields['t.accountant']['checked'])) print '<td align="right" class="liste_titre"><input type="text" class="flat" name="search_accountant" value="'.$search_accountant.'" size="10"></td>';
	if (! empty($arrayfields['t.accountant_last']['checked'])) print '<td align="right" class="liste_titre"><input type="text" class="flat" name="search_accountant_last" value="'.$search_accountant_last.'" size="10"></td>';
	if (! empty($arrayfields['t.description']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_description" value="'.$search_description.'" size="10"></td>';
	if (! empty($arrayfields['t.pc_ip']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_pc_ip" value="'.$search_pc_ip.'" size="10"></td>';
	if (! empty($arrayfields['t.origin']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_origin" value="'.$search_origin.'" size="10"></td>';
	if (! empty($arrayfields['t.originid']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_originid" value="'.$search_originid.'" size="10"></td>';
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
	$searchpitco=$form->showFilterAndCheckAddButtons($massactionbutton?0:0, 'checkforselect', 1);
	print $searchpitco;
	print '</td>';
	print '</tr>'."\n";
}

$i=0;
$var=true;
$totalarray=array();

if ($action == 'create')
{
		// Show here line of result
	print '<tr '.$bc[$var].'>';
	if (! empty($arrayfields['t.ref_ext']['checked']))
	{
		print '<td>'.'<input type="text" class="flat" name="ref_ext" value="'.GETPOST('ref_ext').'" required>'.'</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.accountant']['checked']))
	{
		$min = ($object->accountant_last?$object->accountant_last:$object->accountant);
		print '<td align="right">'.'<input type="number" min="'.$min.'" step="any" class="flat" name="accountant" value="'.GETPOST('accountant').'">'.'</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.accountant_last']['checked']))
	{
		print '<td align="right">'.'<input type="number" min="0" step="any" class="flat" name="accountant_last" value="'.$min.'" readonly>'.'</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.description']['checked']))
	{
		print '<td>'.'<input type="text" class="flat" name="description" value="'.GETPOST('description').'">'.'</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.pc_ip']['checked']))
	{
		print '<td>'.'</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.origin']['checked']))
	{
		print '<td>'.'</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.originid']['checked']))
	{
		print '<td>'.'</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.fk_user_create']['checked']))
	{
		print '<td>'.'</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.fk_user_mod']['checked']))
	{
		print '<td>'.'</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.status']['checked']))
	{
		print '<td>'.'</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	print '<td>';
	print '<input type="submit" name="submit" value="'.$langs->trans('Save').'">';
	print '</td>';
	print '</tr>';
}
while ($i < min($num, $limit))
{
	$obj = $db->fetch_object($resql);
	if ($obj)
	{
		$var = !$var;

		// Show here line of result
		print '<tr '.$bc[$var].'>';
		// LIST_OF_TD_FIELDS_LIST
		if ($action == 'edit' && $idr == $obj->rowid)
		{
			if (! empty($arrayfields['t.ref_ext']['checked']))
			{
				print '<td>'.'<input type="text" class="flat" name="ref_ext" value="'.$obj->ref_ext.'">'.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.accountant']['checked']))
			{
				print '<td align="right">'.'<input type="number" min="0" step="any" class="flat" name="accountant" value="'.$obj->accountant.'">'.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.accountant_last']['checked']))
			{
				print '<td align="right">'.'<input type="number" min="0" step="any" class="flat" name="accountant_last" value="'.$obj->accountant_last.'">'.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.description']['checked']))
			{
				print '<td>'.'<input type="text" class="flat" name="description" value="'.$obj->description.'">'.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.pc_ip']['checked']))
			{
				print '<td>'.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.origin']['checked']))
			{
				print '<td>'.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.originid']['checked']))
			{
				print '<td>'.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.fk_user_create']['checked']))
			{
				print '<td>'.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.fk_user_mod']['checked']))
			{
				print '<td>'.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.status']['checked']))
			{
				print '<td>'.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			print '<td>';
			print '<input type="submit" name="submit" value="'.$langs->trans('Save').'">';
			print '</td>';
		}
		else
		{
			if (! empty($arrayfields['t.ref_ext']['checked']))
			{
				print '<td>'.$obj->ref_ext.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.accountant']['checked']))
			{
				print '<td align="right">'.$obj->accountant.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.accountant_last']['checked']))
			{
				print '<td align="right">'.$obj->accountant_last.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.description']['checked']))
			{
				print '<td>'.$obj->description.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.pc_ip']['checked']))
			{
				print '<td>'.$obj->pc_ip.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.origin']['checked']))
			{
				print '<td>'.$obj->origin.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.originid']['checked']))
			{
				print '<td>'.$obj->originid.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.fk_user_create']['checked']))
			{
				print '<td>'.$obj->fk_user_create.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.fk_user_mod']['checked']))
			{
				print '<td>'.$obj->fk_user_mod.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.status']['checked']))
			{
				print '<td>'.$obj->status.'</td>';
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
			//if ($massactionbutton || $massaction)
			// If we are in select mode (massactionbutton defined) or if we have already selected and sent an action ($massaction) defined
			//{
			//	$selected=0;
			//	if (in_array($obj->rowid, $arrayofselected)) $selected=1;
			//	print '<input id="cb'.$obj->rowid.'" class="flat checkforselect" type="checkbox" name="toselect[]" value="'.$obj->rowid.'"'.($selected?' checked="checked"':'').'>';
			//}
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
$reshook=$hookmanager->executeHooks('printFieldListFooter',$parameters);    // Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;

print '</table>'."\n";
print '</div>'."\n";

print '</form>'."\n";

	// Buttons
print '<div class="tabsAction">'."\n";
$parameters=array();
$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$objobjetive,$action);
	// Note that $action and $objobjetive may have been modified by hook
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
if (empty($reshook))
{
	if ($user->rights->mant->equ->write)
	{
		print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&action=create">'.$langs->trans("New").'</a></div>'."\n";
	}
}
print '</div>'."\n";

