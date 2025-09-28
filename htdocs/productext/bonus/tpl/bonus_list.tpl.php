<?php
/* Copyright (C) 2007-2016 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2014-2016 Juanjo Menent        <jmenent@2byte.es>
 * Copyright (C) 2016      Jean-François Ferry	<jfefe@aternatik.fr>
 * Copyright (C) 2017      Nicolas ZABOURI	<info@inovea-conseil.com>
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
 *   	\file       productext/bonus_list.php
 *		\ingroup    productext
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-03-20 18:45
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


$search_all=trim(GETPOST("sall"));

$search_fk_product_bonus=GETPOST('search_fk_product_bonus','int');
$search_qty_ini=GETPOST('search_qty_ini','alpha');
$search_qty_fin=GETPOST('search_qty_fin','alpha');
$search_active=GETPOST('search_active','int');
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
if (empty($page)) $page=0;
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortfield) $sortfield="t.qty_ini"; // Set here default search field
if (! $sortorder) $sortorder="ASC";

// Protection if external user
$socid=0;
if ($user->societe_id > 0)
{
	$socid = $user->societe_id;
	//accessforbidden();
}

// Initialize technical object to manage context to save list fields
$contextpage=GETPOST('contextpage','aZ')?GETPOST('contextpage','aZ'):'productextbonuslist';

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('productextlist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('productext');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// List of fields to search into when doing a "search in all"
$fieldstosearchall = array(
	't.ref'=>'Ref',
	't.note_public'=>'NotePublic',
);
if (empty($user->socid)) $fieldstosearchall["t.note_private"]="NotePrivate";

//definimos si es con limite o condicional
$typebonus = $objCbonustype->type;
$checked = false;
if ($objCbonustype->type == 'L') $checked = true;
$type_value = $objProductbonus->type_value;
// Definition of fields for list
$fieldqty_ini = $langs->trans('Fieldqty_ini');
$fieldqty_fin = $langs->trans('Fieldqty_fin');
if ($type_value == 'V')
{
	$fieldqty_ini = $langs->trans('Fieldvalue_ini');
	$fieldqty_fin = $langs->trans('Fieldvalue_fin');
}
$arrayfields=array(

	't.fk_product_bonus'=>array('label'=>$langs->trans("Fieldfk_product_bonus"), 'align'=>'align="left"', 'checked'=>0),
	't.qty_ini'=>array('label'=>$fieldqty_ini, 'align'=>'align="left"', 'checked'=>1),
	't.qty_fin'=>array('label'=>$fieldqty_fin, 'align'=>'align="left"', 'checked'=>$checked),
	't.active'=>array('label'=>$langs->trans("Fieldactive"), 'align'=>'align="left"', 'checked'=>1),
	't.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'align'=>'align="left"', 'checked'=>0),
	't.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'align'=>'align="left"', 'checked'=>0),
	't.status'=>array('label'=>$langs->trans("Fieldstatus"), 'align'=>'align="left"', 'checked'=>1),


	//'t.entity'=>array('label'=>$langs->trans("Entity"), 'checked'=>1, 'enabled'=>(! empty($conf->multicompany->enabled) && empty($conf->multicompany->transverse_mode))),
	't.datec'=>array('label'=>$langs->trans("DateCreationShort"), 'align'=>'align="left"', 'checked'=>0, 'position'=>500),
	't.tms'=>array('label'=>$langs->trans("DateModificationShort"), 'align'=>'align="left"', 'checked'=>0, 'position'=>500),
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

		$search_fk_product_bonus='';
		$search_qty_ini='';
		$search_qty_fin='';
		$search_active='';
		$search_fk_user_create='';
		$search_fk_user_mod='';
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
$title = $langs->trans($objProductbonus->ref.' - '.$objProductbonus->label);

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

$sql .= " t.fk_product_bonus,";
$sql .= " t.qty_ini,";
$sql .= " t.qty_fin,";
$sql .= " t.active,";
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
$sql.= " FROM ".MAIN_DB_PREFIX."bonus as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."bonus_extrafields as ef on (t.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";
$sql.= " AND t.fk_product_bonus = ".$idr;

if ($search_fk_product_bonus) $sql.= natural_search("fk_product_bonus",$search_fk_product_bonus);
if ($search_qty_ini) $sql.= natural_search("qty_ini",$search_qty_ini);
if ($search_qty_fin) $sql.= natural_search("qty_fin",$search_qty_fin);
if ($search_active) $sql.= natural_search("active",$search_active);
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
	header("Location: ".DOL_URL_ROOT.'/bonus/card.php?id='.$id);
	exit;
}



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
if ($user->rights->productext->supprimer) $arrayofmassactions['delete']=$langs->trans("Delete");
if ($massaction == 'presend') $arrayofmassactions=array();
$massactionbutton=$form->selectMassAction('', $arrayofmassactions);

print '<form method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
if ($action == 'create')
	print '<input type="hidden" name="action" value="add">';
elseif($action == 'edit')
{
	print '<input type="hidden" name="idrd" value="'.$idrd.'">';
	print '<input type="hidden" name="action" value="update">';
}
else
	print '<input type="hidden" name="action" value="list">';
print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
print '<input type="hidden" name="contextpage" value="'.$contextpage.'">';
print '<input type="hidden" name="id" value="'.$id.'">';
print '<input type="hidden" name="idr" value="'.$idr.'">';

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
if (! empty($arrayfields['t.fk_product_bonus']['checked'])) print_liste_field_titre($arrayfields['t.fk_product_bonus']['label'],$_SERVER['PHP_SELF'],'t.fk_product_bonus','',$params,$arrayfields['t.fk_product_bonus']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.qty_ini']['checked'])) print_liste_field_titre($arrayfields['t.qty_ini']['label'],$_SERVER['PHP_SELF'],'t.qty_ini','',$params,$arrayfields['t.qty_ini']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.qty_fin']['checked'])) print_liste_field_titre($arrayfields['t.qty_fin']['label'],$_SERVER['PHP_SELF'],'t.qty_fin','',$params,$arrayfields['t.qty_fin']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.active']['checked'])) print_liste_field_titre($arrayfields['t.active']['label'],$_SERVER['PHP_SELF'],'t.active','',$params,$arrayfields['t.active']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_create']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_create']['label'],$_SERVER['PHP_SELF'],'t.fk_user_create','',$params,$arrayfields['t.fk_user_create']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_mod']['label'],$_SERVER['PHP_SELF'],'t.fk_user_mod','',$params,$arrayfields['t.fk_user_mod']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($arrayfields['t.status']['label'],$_SERVER['PHP_SELF'],'t.status','',$params,$arrayfields['t.status']['align'],$sortfield,$sortorder);

//if (! empty($arrayfields['t.field1']['checked'])) print_liste_field_titre($arrayfields['t.field1']['label'],$_SERVER['PHP_SELF'],'t.field1','',$param,$arrayfields['t.field1']['align'],$sortfield,$sortorder);
//if (! empty($arrayfields['t.field2']['checked'])) print_liste_field_titre($arrayfields['t.field2']['label'],$_SERVER['PHP_SELF'],'t.field2','',$param,$arrayfields['t.field1']['align'],$sortfield,$sortorder);
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
$reshook=$hookmanager->executeHooks('printFieldListTitle',$parameters);
// Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;
if (! empty($arrayfields['t.datec']['checked']))  print_liste_field_titre($arrayfields['t.datec']['label'],$_SERVER["PHP_SELF"],"t.datec","",$param,'align="center" class="nowrap"',$sortfield,$sortorder);
if (! empty($arrayfields['t.tms']['checked']))    print_liste_field_titre($arrayfields['t.tms']['label'],$_SERVER["PHP_SELF"],"t.tms","",$param,'align="center" class="nowrap"',$sortfield,$sortorder);
//if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($langs->trans("Status"),$_SERVER["PHP_SELF"],"t.status","",$param,'align="center"',$sortfield,$sortorder);
print_liste_field_titre($selectedfields, $_SERVER["PHP_SELF"],"",'','','align="right"',$sortfield,$sortorder,'maxwidthsearch ');
print '</tr>'."\n";

if ($action != 'create' && $action != 'edit')
{
// Fields title search
	print '<tr class="liste_titre">';
//
	if (! empty($arrayfields['t.fk_product_bonus']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_product_bonus']['align'].'><input type="text" class="flat" name="search_fk_product_bonus" value="'.$search_fk_product_bonus.'" size="10"></td>';
	if (! empty($arrayfields['t.qty_ini']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.qty_ini']['align'].'><input type="text" class="flat" name="search_qty_ini" value="'.$search_qty_ini.'" size="10"></td>';
	if (! empty($arrayfields['t.qty_fin']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.qty_fin']['align'].'><input type="text" class="flat" name="search_qty_fin" value="'.$search_qty_fin.'" size="10"></td>';
	if (! empty($arrayfields['t.active']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.active']['align'].'><input type="text" class="flat" name="search_active" value="'.$search_active.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_user_create']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_user_create']['align'].'><input type="text" class="flat" name="search_fk_user_create" value="'.$search_fk_user_create.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_user_mod']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.fk_user_mod']['align'].'><input type="text" class="flat" name="search_fk_user_mod" value="'.$search_fk_user_mod.'" size="10"></td>';
	if (! empty($arrayfields['t.status']['checked'])) print '<td class="liste_titre" '.$arrayfields['t.status']['align'].'><input type="text" class="flat" name="search_status" value="'.$search_status.'" size="10"></td>';

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

if ($action == 'create')
{
	$obj = new Stdclass();
		// Show here line of result
		print '<tr '.$bc[$var].'>';
		// LIST_OF_TD_FIELDS_LIST
		foreach ($arrayfields as $key => $value) {
			if (!empty($arrayfields[$key]['checked']))
			{
				//$key2 = str_replace('t.', '', $key);
				$aKey = explode('.',$key);
				$key2 = $aKey[1];
				if ($key2 == 'ref')
				{
					$object->id = $obj->rowid;
					$object->ref = $obj->ref;
					$object->label = $obj->label;
					$obj->$key2 = $object->getNomUrl();
				}
				elseif ($key2 == 'active')
				{
					print '<td>';
					print $form->selectyesno('active',GETPOST('active'),1);
					print '</td>';
				}
				elseif ($key2 == 'status')
				{
					$object->status = $obj->$key2;
					$obj->$key2 = $object->getLibStatut(3);
				}
				elseif ($key2 == 'fk_user_create' || $key2 == 'fk_user_mod')
				{
					$res = $objUser->fetch($obj->$key2);
					if ($res == 1)
						$obj->$key2 = $objUser->getNomUrl(1);
				}
				else
				{
					$obj->$key2 = '<input type="text" name="'.$key2.'" value="'.GETPOST($key2).'">';
				}
				if ($key2 != 'active')
					print '<td>' . $obj->$key2 . '</td>';
				if (!$i)
					$totalarray['nbfield'] ++;
			}
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
		// Action column
		print '<td class="nowrap" align="center">';
		print '<input type="submit" class="butAction" value="'.$langs->trans('Save').'">';
		print '</td>';
		if (! $i) $totalarray['nbfield']++;

		print '</tr>';
}
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
		if ($obj->rowid != $idrd)
			print '<tr '.$bc[$var].'>';
		else
			print '<tr class="trselect">';
		// LIST_OF_TD_FIELDS_LIST
		foreach ($arrayfields as $key => $value) {
			if (!empty($arrayfields[$key]['checked'])) {
				//$key2 = str_replace('t.', '', $key);
				$aKey = explode('.',$key);
				$key2 = $aKey[1];
				if ($key2 == 'qty_ini')
				{
					$objBonus->fk_product = $id;
					$objBonus->idr = $idr;
					$objBonus->ref = $obj->rowid;
					$objBonus->id = $obj->rowid;

					$obj->$key2 = $objBonus->getNomUrltpl(1);
				}
				if ($key2 == 'active')
				{
					$img = 'switch_off';
					if ($obj->$key2) $img = 'switch_on';
					$obj->$key2 = img_picto('',$img);
				}
				if ($key2 == 'status')
				{
					$object->status = $obj->$key2;
					$obj->$key2 = $object->getLibStatut(3);
				}
				if ($key2 == 'fk_user_create' || $key2 == 'fk_user_mod')
				{
					$res = $objUser->fetch($obj->$key2);
					if ($res == 1)
						$obj->$key2 = $objUser->getNomUrl(1);
				}

				print '<td>' . $obj->$key2 . '</td>';
				if (!$i)
					$totalarray['nbfield'] ++;
			}
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
		if ($user->rights->productext->bonus->write)
		{
			print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$idr.'&idrd='.$obj->rowid.'&action=edit">'.img_picto($langs->trans('Edit'),'edit').'</a>';
		}
		if ($user->rights->productext->bonus->write)
		{
			print '&nbsp;&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$idr.'&idrd='.$obj->rowid.'&action=delete">'.img_picto($langs->trans('Delete'),'delete').'</a>';
		}
		print '</td>';
		if (! $i) $totalarray['nbfield']++;

		print '</tr>';
	}
	$i++;
}

$db->free($resql);

$parameters=array('arrayfields'=>$arrayfields, 'sql'=>$sql);
$reshook=$hookmanager->executeHooks('printFieldListFooter',$parameters);    // Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;

print '</table>'."\n";
print '</div>'."\n";

print '</form>'."\n";


/* **************************************** */
/*                                          */
/* Barre d'action                           */
/*                                          */
/* **************************************** */

print "<div class=\"tabsAction\">\n";

if ($user->rights->productext->bonus->write)
	print '<a class="butAction" href="carddet.php?id='.$id.'&idr='.$idr.'&action=create">'.$langs->trans("New").'</a>';
else
	print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("New")."</a>";
print "</div>";

//vamos a mostrar los bonus_det si se selecciona
if ($idrd>0)
	include DOL_DOCUMENT_ROOT.'/productext/bonus/tpl/bonusdet_list.tpl.php';