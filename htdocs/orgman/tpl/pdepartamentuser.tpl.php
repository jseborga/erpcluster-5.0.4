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
 *   	\file       orgman/pdepartamentuser_list.php
 *		\ingroup    orgman
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-03-28 12:17
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
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
dol_include_once('/orgman/class/pdepartamentuser.class.php');

// Load traductions files requiredby by page
$langs->load("orgman");
$langs->load("other");

// Get parameters
$idr			= GETPOST('idr','int');

$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_fk_departament=GETPOST('search_fk_departament','int');
$search_fk_user=GETPOST('search_fk_user','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_active=GETPOST('search_active','int');
$search_privilege=GETPOST('search_privilege','int');


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

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('pdepartamentuserlist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('orgman');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Load object if id or ref is provided as parameter
$objdepuser=new Pdepartamentuser($db);
if ($idr > 0 && $action != 'add')
{
	$result=$objdepuser->fetch($idr);
	if ($result < 0) dol_print_error($db);
}
$objuser = new User($db);

// Definition of fields for list
$arrayfields=array(

	't.fk_user'=>array('label'=>$langs->trans("Fieldfk_member"), 'checked'=>1),
	't.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'checked'=>0),
	't.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'checked'=>0),
	't.active'=>array('label'=>$langs->trans("Fieldactive"), 'checked'=>1),
	't.privilege'=>array('label'=>$langs->trans("Fieldprivilege"), 'checked'=>0),


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

	$search_fk_departament='';
	$search_fk_user='';
	$search_fk_user_create='';
	$search_fk_user_mod='';
	$search_active='';
	$search_privilege='';


	$search_date_creation='';
	$search_date_update='';
	$search_array_options=array();
}

//obtenemos los la registrado en la tabla pdeparamentuser
$resuser = $objdepuser->fetchAll('','',0,0,array(1=>1),'AND');
$idsMember = '';
if ($resuser>0)
{
	foreach ($objdepuser->lines AS $j => $lines)
	{
		if ($idsMember) $idsMember.= ',';
		$idsMember.= $lines->fk_user;
	}
}
if ($idsMember)
	$filterMember = " d.statut = 1 AND d.rowid NOT IN (".$idsMember.")";
/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

$now=dol_now();


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

$sql .= " t.fk_departament,";
$sql .= " t.fk_user,";
$sql .= " t.fk_user_create,";
$sql .= " t.fk_user_mod,";
$sql .= " t.datec,";
$sql .= " t.datem,";
$sql .= " t.tms,";
$sql .= " t.active,";
$sql .= " t.privilege";
$sql .= " ,u.lastname";
$sql .= " ,u.firstname";
$sql .= " ,u.login";


// Add fields for extrafields
foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);
  // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."p_departament_user as t";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."adherent as u ON t.fk_user = u.rowid ";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."p_departament_user_extrafields as ef on (u.rowid = ef.fk_object)";
$sql.= " WHERE t.fk_departament = ".$id;
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_fk_departament) $sql.= natural_search("fk_departament",$search_fk_departament);
if ($search_fk_user) $sql.= natural_search(array('t.fk_user','lastname','firstname','login'),$search_fk_user);
if ($search_fk_user_create) $sql.= natural_search("fk_user_create",$search_fk_user_create);
if ($search_fk_user_mod) $sql.= natural_search("fk_user_mod",$search_fk_user_mod);
if ($search_active==0 || $search_active == 1) $sql.= natural_search("active",$search_active);
if ($search_privilege) $sql.= natural_search("privilege",$search_privilege);


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
$reshook=$hookmanager->executeHooks('printFieldListWhere',$parameters);
// Note that $action and $object may have been modified by hook
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
	$params="&id=".$object->id;
	if ($limit > 0 && $limit != $conf->liste_limit)
	{
	 	$param.='&limit='.$limit;
	 	$params.='&limit='.$limit;
	}

	if ($search_fk_departament != '') $params.= '&amp;search_fk_departament='.urlencode($search_fk_departament);
	if ($search_fk_user != '') $params.= '&amp;search_fk_user='.urlencode($search_fk_user);
	if ($search_fk_user_create != '') $params.= '&amp;search_fk_user_create='.urlencode($search_fk_user_create);
	if ($search_fk_user_mod != '') $params.= '&amp;search_fk_user_mod='.urlencode($search_fk_user_mod);
	if ($search_active != '') $params.= '&amp;search_active='.urlencode($search_active);
	if ($search_privilege != '') $params.= '&amp;search_privilege='.urlencode($search_privilege);


	if ($optioncss != '') $param.='&optioncss='.$optioncss;
	// Add $param from extra fields
	foreach ($search_array_options as $key => $val)
	{
		$crit=$val;
		$tmpkey=preg_replace('/search_options_/','',$key);
		if ($val != '') $param.='&search_options_'.$tmpkey.'='.urlencode($val);
	}




	print '<form method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
	if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
	print '<input type="hidden" name="id" id="id" value="'.$object->id.'">';
	if ($action == 'create')
		print '<input type="hidden" name="action" value="add">';
	elseif($action == 'edit')
	{
		print '<input type="hidden" name="action" value="update">';
		print '<input type="hidden" name="idr" value="'.GETPOST('idr').'">';
	}
	else
		print '<input type="hidden" name="action" value="list">';
	print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
	print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';

	print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $params, $sortfield, $sortorder, '', $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);

	if ($sall)
	{
		foreach($fieldstosearchall as $key => $val) $fieldstosearchall[$key]=$langs->trans($val);
		print $langs->trans("FilterOnInto", $all) . join(', ',$fieldstosearchall);
	}

	$moreforfilter = '';
	//$moreforfilter.='<div class="divsearchfield">';
	//$moreforfilter.= $langs->trans('MyFilter') . ': <input type="text" name="search_myfield" value="'.dol_escape_htmltag($search_myfield).'">';
	//$moreforfilter.= '</div>';

	if (! empty($moreforfilter))
	{
		print '<div class="liste_titre liste_titre_bydiv centpercent">';
		print $moreforfilter;
		$parameters=array();
		$reshook=$hookmanager->executeHooks('printFieldPreListTitle',$parameters);
		 // Note that $action and $object may have been modified by hook
		print $hookmanager->resPrint;
		print '</div>';
	}

	$varpage=empty($contextpage)?$_SERVER["PHP_SELF"]:$contextpage;
	$selectedfields=$form->multiSelectArrayWithCheckbox('selectedfields', $arrayfields, $varpage);	// This also change content of $arrayfields

	print '<table class="liste '.($moreforfilter?"listwithfilterbefore":"").'">';

	// Fields title
	print '<tr class="liste_titre">';
	//
	if (! empty($arrayfields['t.fk_departament']['checked'])) print_liste_field_titre($arrayfields['t.fk_departament']['label'],$_SERVER['PHP_SELF'],'t.fk_departament','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.fk_user']['checked'])) print_liste_field_titre($arrayfields['t.fk_user']['label'],$_SERVER['PHP_SELF'],'t.fk_user','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.fk_user_create']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_create']['label'],$_SERVER['PHP_SELF'],'t.fk_user_create','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.fk_user_mod']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_mod']['label'],$_SERVER['PHP_SELF'],'t.fk_user_mod','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.active']['checked'])) print_liste_field_titre($arrayfields['t.active']['label'],$_SERVER['PHP_SELF'],'t.active','',$params,'',$sortfield,$sortorder);
	if (! empty($arrayfields['t.privilege']['checked'])) print_liste_field_titre($arrayfields['t.privilege']['label'],$_SERVER['PHP_SELF'],'t.privilege','',$params,'',$sortfield,$sortorder);

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
	$reshook=$hookmanager->executeHooks('printFieldListTitle',$parameters);
	// Note that $action and $object may have been modified by hook
	print $hookmanager->resPrint;
	if (! empty($arrayfields['t.datec']['checked']))  print_liste_field_titre($arrayfields['t.datec']['label'],$_SERVER["PHP_SELF"],"t.datec","",$param,'align="center" class="nowrap"',$sortfield,$sortorder);
	if (! empty($arrayfields['t.tms']['checked']))    print_liste_field_titre($arrayfields['t.tms']['label'],$_SERVER["PHP_SELF"],"t.tms","",$param,'align="center" class="nowrap"',$sortfield,$sortorder);
	//if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($langs->trans("Status"),$_SERVER["PHP_SELF"],"t.status","",$param,'align="center"',$sortfield,$sortorder);
	print_liste_field_titre($selectedfields, $_SERVER["PHP_SELF"],"",'','','align="right"',$sortfield,$sortorder,'maxwidthsearch ');
	print '</tr>'."\n";

	if ($action !='create' && $action != 'edit')
	{
	// Fields title search
		print '<tr class="liste_titre">';
	//
		if (! empty($arrayfields['t.fk_departament']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_departament" value="'.$search_fk_departament.'" size="10"></td>';
		if (! empty($arrayfields['t.fk_user']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user" value="'.$search_fk_user.'" size="10"></td>';
		if (! empty($arrayfields['t.fk_user_create']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_create" value="'.$search_fk_user_create.'" size="10"></td>';
		if (! empty($arrayfields['t.fk_user_mod']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_mod" value="'.$search_fk_user_mod.'" size="10"></td>';
		if (! empty($arrayfields['t.active']['checked']))
		{
			print '<td class="liste_titre">';
			print $form->selectyesno('search_active',$search_active,1,false,1);
			print '</td>';
		}
		if (! empty($arrayfields['t.privilege']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_privilege" value="'.$search_privilege.'" size="10"></td>';

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
		$searchpitco=$form->showFilterAndCheckAddButtons(0);
		print $searchpitco;
		print '</td>';
		print '</tr>'."\n";
	}

	$i=0;
	$var=true;
	$totalarray=array();

	while ($i < min($num, $limit))
	{
		$obj = $db->fetch_object($resql);
		if ($obj)
		{
			$exclude[$obj->fk_user] = $obj->fk_user;
			$var = !$var;
			// Show here line of result
			print '<tr '.$bc[$var].'>';
			if ($action == 'edit' && $idr == $obj->rowid)
			{
				// LIST_OF_TD_FIELDS_LIST
				if (! empty($arrayfields['t.fk_user']['checked']))
				{
					print '<td>'.$form->select_member(GETPOST('fk_user'),'fk_user', " d.statut = 1",1,0,0,array(),0).'</td>';
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
				if (! empty($arrayfields['t.active']['checked']))
				{
					print '<td>'.$form->selectyesno('active',GETPOST('active'),1).'</td>';
					if (! $i) $totalarray['nbfield']++;
				}
				print '<td><input class="button" type="submit" name="submit" value="'.$langs->trans('Save').'">';
			}
			else
			{
				// LIST_OF_TD_FIELDS_LIST

				if (! empty($arrayfields['t.fk_user']['checked']))
				{
					$res = $objAdherent->fetch($obj->fk_user);
					if ($res == 1)
						print '<td>'.$objAdherent->getNomUrl(1).' '.$objAdherent->lastname.' '.$objAdherent->firstname.'</td>';
					else
						print '<td></td>';
					if (! $i) $totalarray['nbfield']++;
				}
				if (! empty($arrayfields['t.fk_user_create']['checked']))
				{
					$res = $objuser->fetch($obj->fk_user_create);
					if ($res == 1)
						print '<td>'.$objuser->getNomUrl(1).'</td>';
					else
						print '<td></td>';
					if (! $i) $totalarray['nbfield']++;
				}
				if (! empty($arrayfields['t.fk_user_mod']['checked']))
				{
					$res = $objuser->fetch($obj->fk_user_mod);
					if ($res == 1)
						print '<td>'.$objuser->getNomUrl(1).'</td>';
					else
						print '<td></td>';
					if (! $i) $totalarray['nbfield']++;
				}
				if (! empty($arrayfields['t.active']['checked']))
				{
					$img = 'switch_off';
					if ($obj->active) $img = 'switch_on';

					if ($user->rights->orgman->dpto->write)
						print '<td>'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&idr='.$obj->rowid.'&action=active">'.img_picto('',$img).'</a>'.'</td>';
					else
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
				print '<td>';
				if ($user->rights->orgman->dpto->del)
					print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&idr='.$obj->rowid.'&action=delete">'.img_picto($langs->trans('Delete'),'delete').'</a>';
				print '</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			print '</tr>';
		}
		$i++;
	}
	  // Confirm delete third party
	if ($action == 'delete')
	{
		$form = new Form($db);
		$objAdherent->fetch($objdepuser->fk_user);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&idr='.$objdepuser->id,$langs->trans("Deletemember"),$langs->trans("ConfirmDeletemember").': '.$objAdherent->lastname.' '.$objAdherent->firstname,"confirm_delete",'',0,1);
		if ($ret == 'html') print '<br>';
	}


	if ($action == 'create')
	{
		print '<tr '.$bc[$var].'>';
				// LIST_OF_TD_FIELDS_LIST
		if (! empty($arrayfields['t.fk_user']['checked']))
		{
			print '<td>';
			//$form->select_users(GETPOST('fk_user'),'fk_user',1,$exclude,0,'','',0);
			print $form->select_member('','fk_user', $filterMember,1,0,0,array(),0);
			print '</td>';
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
		if (! empty($arrayfields['t.active']['checked']))
		{
			print '<td>'.$form->selectyesno('active',(GETPOST('active')?GETPOST('active'):1),1).'</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		print '<td><input class="button" type="submit" name="submit" value="'.$langs->trans('Save').'">';
		print '</tr>';
	}
	$db->free($resql);

	$parameters=array('sql' => $sql);
	$reshook=$hookmanager->executeHooks('printFieldListFooter',$parameters);
	// Note that $action and $object may have been modified by hook
	print $hookmanager->resPrint;

	print "</table>\n";
	print "</form>\n";


	/* ************************************************************************** */
	/*                                                                            */
	/* Barre d'action                                                             */
	/*                                                                            */
	/* ************************************************************************** */

	print "<div class=\"tabsAction\">\n";

	if ($action != 'create' && $asction != 'edit')
	{
		if ($user->rights->orgman->dpto->write)
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&limit='.$limit.'&action=create">'.$langs->trans("New").'</a>';
		else
			print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("New")."</a>";
	}
	print "</div>";


	$db->free($result);
}
else
{
	$error++;
	dol_print_error($db);
}
