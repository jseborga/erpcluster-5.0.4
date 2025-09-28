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
 *   	\file       orgman/partidacolateral_list.php
 *		\ingroup    orgman
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-07-12 08:20
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


$search_all=trim(GETPOST("sall"));

$search_code_partida=GETPOST('search_code_partida','alpha');
$search_code_colateral=GETPOST('search_code_colateral','alpha');
$search_label=GETPOST('search_label','alpha');
$search_percent=GETPOST('search_percent','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_active=GETPOST('search_active','int');


$search_myfield=GETPOST('search_myfield');
$optioncss = GETPOST('optioncss','alpha');

// Load variable for pagination
$limit = GETPOST("limit")?GETPOST("limit","int"):$conf->liste_limit;
$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha');
$page = GETPOST('page','int');
if ($page == -1) { $page = 0; }
if (empty($page)) $page=0;
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
//if (! $sortfield) $sortfield="t.code_colateral"; // Set here default search field
//if (! $sortorder) $sortorder="ASC";

// Protection if external user
$socid=0;
if ($user->societe_id > 0)
{
	$socid = $user->societe_id;
	//accessforbidden();
}

// Initialize technical object to manage context to save list fields
$contextpage=GETPOST('contextpage','aZ')?GETPOST('contextpage','aZ'):'orgmanpartidacolaterallist';

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('orgmanlist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('orgman');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// List of fields to search into when doing a "search in all"
$fieldstosearchall = array(
	't.ref'=>'Ref',
	't.note_public'=>'NotePublic',
	);
if (empty($user->socid)) $fieldstosearchall["t.note_private"]="NotePrivate";

// Definition of fields for list
$arrayfields=array(

	't.code_partida'=>array('label'=>$langs->trans("Fieldcode_partida"), 'checked'=>0),
	't.code_colateral'=>array('label'=>$langs->trans("Fieldcode_colateral"), 'checked'=>1),
	't.label'=>array('label'=>$langs->trans("Fieldlabel"), 'checked'=>1),
	't.percent'=>array('label'=>$langs->trans("Fieldpercent"), 'checked'=>1),
	't.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'checked'=>0),
	't.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'checked'=>0),
	't.active'=>array('label'=>$langs->trans("Fieldactive"), 'checked'=>1),


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

		$search_code_partida='';
		$search_code_colateral='';
		$search_label='';
		$search_percent='';
		$search_fk_user_create='';
		$search_fk_user_mod='';
		$search_active='';


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
$title = $langs->trans('Partidas colaterales');

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
$sql .= " t.code_partida,";
$sql .= " t.code_colateral,";
$sql .= " t.label,";
$sql .= " t.percent,";
$sql .= " t.fk_user_create,";
$sql .= " t.fk_user_mod,";
$sql .= " t.datec,";
$sql .= " t.datem,";
$sql .= " t.tms,";
$sql .= " t.active";


// Add fields from extrafields
foreach ($extrafields->attribute_label as $key => $val) $sql.=($extrafields->attribute_type[$key] != 'separate' ? ",ef.".$key.' as options_'.$key : '');
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."partida_colateral as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."partida_colateral_extrafields as ef on (t.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
$sql.= " AND t.code_partida = ".$object->code;
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_code_partida) $sql.= natural_search("code_partida",$search_code_partida);
if ($search_code_colateral) $sql.= natural_search("code_colateral",$search_code_colateral);
if ($search_label) $sql.= natural_search("label",$search_label);
if ($search_percent) $sql.= natural_search("percent",$search_percent);
if ($search_fk_user_create) $sql.= natural_search("fk_user_create",$search_fk_user_create);
if ($search_fk_user_mod) $sql.= natural_search("fk_user_mod",$search_fk_user_mod);
if ($search_active) $sql.= natural_search("active",$search_active);


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
//$sql.=$db->order($sortfield,$sortorder);
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
	header("Location: ".DOL_URL_ROOT.'/partidacolateral/card.php?id='.$id);
	exit;
}


$arrayofselected=is_array($toselect)?$toselect:array();

$param='&id='.$id;
$params='&id='.$id;
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
if ($user->rights->orgman->supprimer) $arrayofmassactions['delete']=$langs->trans("Delete");
if ($massaction == 'presend') $arrayofmassactions=array();
$massactionbutton=$form->selectMassAction('', $arrayofmassactions);

print '<form method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';

if ($action == 'createcolateral')
	print '<input type="hidden" name="action" value="addcolateral">';
elseif($action == 'editcolateral')
{
	print '<input type="hidden" name="action" value="updatecolateral">';
	print '<input type="hidden" name="idr" value="'.$idr.'">';
}
else
	print '<input type="hidden" name="action" value="list">';
print '<input type="hidden" name="id" value="'.$id.'">';
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
if (! empty($arrayfields['t.code_partida']['checked'])) print_liste_field_titre($arrayfields['t.code_partida']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.code_colateral']['checked'])) print_liste_field_titre($arrayfields['t.code_colateral']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.label']['checked'])) print_liste_field_titre($arrayfields['t.label']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.percent']['checked'])) print_liste_field_titre($arrayfields['t.percent']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_create']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_create']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_mod']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.active']['checked'])) print_liste_field_titre($arrayfields['t.active']['label'],$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);

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
print_liste_field_titre('', $_SERVER["PHP_SELF"],"",'','','align="right"',$sortfield,$sortorder,'maxwidthsearch ');
print '</tr>'."\n";

if ($action != 'createcolateral' && $action != 'editcolateral' && $abc)
{
// Fields title search
	print '<tr class="liste_titre">';
//
	if (! empty($arrayfields['t.code_partida']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_code_partida" value="'.$search_code_partida.'" size="10"></td>';
	if (! empty($arrayfields['t.code_colateral']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_code_colateral" value="'.$search_code_colateral.'" size="10"></td>';
	if (! empty($arrayfields['t.label']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_label" value="'.$search_label.'" size="10"></td>';
	if (! empty($arrayfields['t.percent']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_percent" value="'.$search_percent.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_user_create']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_create" value="'.$search_fk_user_create.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_user_mod']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_mod" value="'.$search_fk_user_mod.'" size="10"></td>';
	if (! empty($arrayfields['t.active']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_active" value="'.$search_active.'" size="10"></td>';

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
	$searchpitco=$form->showFilterAndCheckAddButtons($massactionbutton?1:0, 'checkforselect', 1);
	print $searchpitco;
	print '</td>';
	print '</tr>'."\n";
}

$i=0;
$var=true;
$totalarray=array();

if ($action == 'createcolateral')
{
	print '<tr '.$bc[$var].'>';
		// LIST_OF_TD_FIELDS_LIST
	if (! empty($arrayfields['t.code_colateral']['checked']))
	{
		print '<td>'.'<input type="text" name="code_colateral" value="'.GETPOST('code_colateral').'" autofocus>'.'</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.label']['checked']))
	{
		print '<td>'.'<input type="text" name="label" value="'.GETPOST('label').'">'.'</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.percent']['checked']))
	{
		print '<td>'.'<input type="number" MIN="0" MAX="100" step="any" name="percent" value="'.GETPOST('percent').'">'.'</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.active']['checked']))
	{
		print '<td>';
		print $form->selectyesno('active',(GETPOST('active')?GETPOST('active'):1),1);
		print  '</td>';
	}
	print '<td nowrap>';
	print '<input class="butAction" type="submit" value="'.$langs->trans('Save').'">';
	print '&nbsp;<input class="butActionDelete" type="submit" name="cancel" value="'.$langs->trans('Return').'">';
	print  '</td>';
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
		if ($idr == $obj->rowid && $action == 'editcolateral')
		{
			if (! empty($arrayfields['t.code_colateral']['checked']))
			{
				print '<td>'.'<input type="text" name="code_colateral" value="'.$obj->code_colateral.'">'.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.label']['checked']))
			{
				print '<td>'.'<input type="text" name="label" value="'.$obj->label.'">'.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.percent']['checked']))
			{
				print '<td>'.'<input type="text" name="percent" value="'.$obj->percent.'">'.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.active']['checked']))
			{
				print '<td>';
				print $form->selectyesno('active',$obj->active,1);
				print  '</td>';
			}
			print '<td>';
			print '<input type="submit" value="'.$langs->trans('Save').'">';
			print  '</td>';

		}
		else
		{
			if (! empty($arrayfields['t.code_colateral']['checked']))
			{
				print '<td>'.$obj->code_colateral.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.label']['checked']))
			{
				print '<td>'.$obj->label.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.percent']['checked']))
			{
				print '<td>'.$obj->percent.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.active']['checked']))
			{
				$img = 'switch_off';
				if ($obj->active) $img = 'switch_on';
				print '<td>'.img_picto('',$img).'</td>';
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

			print '</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		print '</tr>';
	}
	$i++;
}

$db->free($resql);

$parameters=array('arrayfields'=>$arrayfields, 'sql'=>$sql);
$reshook=$hookmanager->executeHooks('printFieldListFooter',$parameters);
print $hookmanager->resPrint;

print '</table>'."\n";
print '</div>'."\n";

print '</form>'."\n";

	// Buttons
print '<div class="tabsAction">'."\n";
$parameters=array();
$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$objPartidacolateral,$action);
	// Note that $action and $object may have been modified by hook
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	if ($user->rights->orgman->part->write)
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&ido='.$ido.'&ids='.$ids.'&action=createcolateral">'.$langs->trans("Newcolateral").'</a>';
	else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Newcolateral")."</a>";
}
print '</div>'."\n";