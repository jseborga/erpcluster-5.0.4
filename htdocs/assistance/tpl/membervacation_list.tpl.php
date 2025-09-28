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
 *   	\file       assistance/membervacation_list.php
 *		\ingroup    assistance
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-09-12 12:30
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

$action=GETPOST('action','alpha');
$massaction=GETPOST('massaction','alpha');
$show_files=GETPOST('show_files','int');
$confirm=GETPOST('confirm','alpha');
$toselect = GETPOST('toselect', 'array');

$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');

$search_all=trim(GETPOST("sall"));

$search_fk_member=GETPOST('search_fk_member','int');
$search_period_year=GETPOST('search_period_year','int');
$search_days_assigned=GETPOST('search_days_assigned','int');
$search_days_used=GETPOST('search_days_used','int');
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
if (! $sortorder) $sortorder="ASC";

// Protection if external user
$socid=0;
if ($user->societe_id > 0)
{
	$socid = $user->societe_id;
	//accessforbidden();
}

// Initialize technical object to manage context to save list fields
$contextpage=GETPOST('contextpage','aZ')?GETPOST('contextpage','aZ'):'membervacationlist';

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('assistancelist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('assistance');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// List of fields to search into when doing a "search in all"
$fieldstosearchall = array(
	't.ref'=>'Ref',
	't.note_public'=>'NotePublic',
);
if (empty($user->socid)) $fieldstosearchall["t.note_private"]="NotePrivate";

// Definition of fields for list
$arrayfields=array(

	't.fk_member'=>array('label'=>$langs->trans("Fieldfk_member"), 'checked'=>0),
	't.date_ini'=>array('label'=>$langs->trans("Fieldvalidfrom"), 'checked'=>1),
	't.date_fin'=>array('label'=>$langs->trans("Fieldvaliduntil"), 'checked'=>1),
	't.period_year'=>array('label'=>$langs->trans("Fieldgestion"), 'checked'=>1),
	't.days_assigned'=>array('label'=>$langs->trans("Fielddays_assigned"), 'checked'=>1),
	't.days_used'=>array('label'=>$langs->trans("Fielddays_used"), 'checked'=>1),
	't.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'checked'=>0),
	't.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'checked'=>0),
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

		$search_fk_member='';
		$search_period_year='';
		$search_days_assigned='';
		$search_days_used='';
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
$title = $langs->trans('Assignedvacation');

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

$sql .= " t.fk_member,";
$sql .= " t.date_ini,";
$sql .= " t.date_fin,";
$sql .= " t.period_year,";
$sql .= " t.days_assigned,";
$sql .= " t.days_used,";
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
$sql.= " FROM ".MAIN_DB_PREFIX."member_vacation as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."member_vacation_extrafields as ef on (t.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
$sql.= " AND t.fk_member = ".$id;
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_fk_member) $sql.= natural_search("fk_member",$search_fk_member);
if ($search_period_year) $sql.= natural_search("period_year",$search_period_year);
if ($search_days_assigned) $sql.= natural_search("days_assigned",$search_days_assigned);
if ($search_days_used) $sql.= natural_search("days_used",$search_days_used);
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

//$sql.= $db->plimit($limit+1, $offset);

dol_syslog($script_file, LOG_DEBUG);
$resql=$db->query($sql);
if (! $resql)
{
	dol_print_error($db);
	exit;
}

$num = $db->num_rows($resql);


$arrayofselected=is_array($toselect)?$toselect:array();

$param='';
if (! empty($contextpage) && $contextpage != $_SERVER["PHP_SELF"]) $param.='&contextpage='.$contextpage;
//if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;
if ($search_period_year != '') $param.= '&amp;search_period_year='.urlencode($search_period_year);
if ($search_days_assigned != '') $param.= '&amp;search_days_assigned='.urlencode($search_days_assigned);
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
if ($user->rights->assistance->supprimer) $arrayofmassactions['delete']=$langs->trans("Delete");
if ($massaction == 'presend') $arrayofmassactions=array();
$massactionbutton=$form->selectMassAction('', $arrayofmassactions);

if ($action == 'validate' && $idr)
{
	$objMembervacation->fetch($idr);
	$formquestion = array(0=>array('type'=>'hidden','label'=>'idr','name'=>'idr','value'=>$idr));

	$formconfirm=$form->form_confirm($_SERVER["PHP_SELF"]."?rowid=".$object->id,
		$langs->trans("Validate"),
		$langs->trans('ConfirmValidate').' '.$langs->trans('Vacation').' '.$langs->trans('the').' '.$objMembervacation->days_assigned.' '.$langs->trans('days').' '.$langs->trans('formanagement').' '.$objMembervacation->period_year,
		"confirm_validate",
		$formquestion,1,2);
	print $formconfirm;
}



print '<form method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
if ($action == 'create')
	print '<input type="hidden" name="action" value="add">';
elseif($action == 'edit')
{
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="idr" value="'.$idr.'">';
}
else
	print '<input type="hidden" name="action" value="list">';
print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
print '<input type="hidden" name="contextpage" value="'.$contextpage.'">';
print '<input type="hidden" name="rowid" value="'.$id.'">';

print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, '', $num, $nbtotalofrecords, 'title_companies', 0, '', '');

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

//print '<div class="div-table-responsive">';
print '<table class="tagtable liste'.($moreforfilter?" listwithfilterbefore":"").'">'."\n";

// Fields title
print '<tr class="liste_titre">';
//
if (! empty($arrayfields['t.fk_member']['checked'])) print_liste_field_titre($arrayfields['t.fk_member']['label'],$_SERVER['PHP_SELF'],'t.fk_member','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.date_ini']['checked'])) print_liste_field_titre($arrayfields['t.date_ini']['label'],$_SERVER['PHP_SELF'],'t.date_ini','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.date_fin']['checked'])) print_liste_field_titre($arrayfields['t.date_fin']['label'],$_SERVER['PHP_SELF'],'t.date_fin','',$params,'',$sortfield,$sortorder);

if (! empty($arrayfields['t.period_year']['checked'])) print_liste_field_titre($arrayfields['t.period_year']['label'],$_SERVER['PHP_SELF'],'t.period_year','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.days_assigned']['checked'])) print_liste_field_titre($arrayfields['t.days_assigned']['label'],$_SERVER['PHP_SELF'],'t.days_assigned','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.days_used']['checked'])) print_liste_field_titre($arrayfields['t.days_used']['label'],$_SERVER['PHP_SELF'],'t.days_used','',$params,'',$sortfield,$sortorder);
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
print_liste_field_titre('', $_SERVER["PHP_SELF"],"",'','','align="right"',$sortfield,$sortorder,'maxwidthsearch ');
print '</tr>'."\n";


if ($action != 'create' && $action != 'edit' && $abc)
{
// Fields title search
	print '<tr class="liste_titre">';
//
	if (! empty($arrayfields['t.fk_member']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_member" value="'.$search_fk_member.'" size="10"></td>';
	if (! empty($arrayfields['t.period_year']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_period_year" value="'.$search_period_year.'" size="10"></td>';
	if (! empty($arrayfields['t.days_assigned']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_days_assigned" value="'.$search_days_assigned.'" size="10"></td>';
	if (! empty($arrayfields['t.days_used']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_days_used" value="'.$search_days_used.'" size="10"></td>';
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
$aVacacion = array();
if ($action == 'delete') {
	$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?rowid='.$id.'&idr=' . $idr, $langs->trans('DeleteVacation'), $langs->trans('ConfirmDeleteVacation'), 'confirm_delete', '', 0, 1);
	print $formconfirm;
}

if ($action == 'create')
{
	print '<tr '.$bc[$var].'>';
			// LIST_OF_TD_FIELDS_LIST
	if (! empty($arrayfields['t.fk_member']['checked']))
	{
		print '<td>'.'</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.date_ini']['checked']))
	{
		print '<td>';
		print $form->select_date((GETPOST('dateini')?GETPOST('dateini'):$db->jdate($obj->date_ini)),'di_',0,0,1);

		print '</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.date_fin']['checked']))
	{
		print '<td>';
		print $form->select_date((GETPOST('datefin')?GETPOST('datefin'):$db->jdate($obj->date_fin)),'df_',0,0,1);

		print '</td>';
		if (! $i) $totalarray['nbfield']++;
	}

	if (! empty($arrayfields['t.period_year']['checked']))
	{
		print '<td>'.'<input type="number" min="0" name="period_year" value="'.(GETPOST('period_year')?GETPOST('period_year'):$obj->period_year).'">'.'</td>';

		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.days_assigned']['checked']))
	{
		print '<td>'.'<input type="number" min="0" name="days_assigned" value="'.(GETPOST('days_assigned')?GETPOST('days_assigned'):$obj->days_assigned).'">'.'</td>';

		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.days_used']['checked']))
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
	print '<td></td>';
			// Action column
	print '<td class="nowrap" align="right">';
	print '<input class="butAction" type="submit" value="'.$langs->trans('Keep').'">';
	print '<input class="butAction" type="submit" name="cancel" value="'.$langs->trans('Return').'">';
	print '</td>';
	if (! $i) $totalarray['nbfield']++;
	print '</tr>';
}

while ($i < min($num, $limit))
{
	$obj = $db->fetch_object($resql);
	if ($obj)
	{
		$var = !$var;
		if ($obj->status)
		{
		//obtenemos cuanto se utilizara con esta vacacion asignada
			$nUsed = 0;
			$filterdet = " AND t.fk_member_vacation = ".$obj->rowid;
			$resdet = $objMembervacationdet->fetchAll('','',0,0,array(1=>1),'AND',$filterdet);
			if ($resdet)
			{
				foreach ($objMembervacationdet->lines AS $k => $linek)
				{
					$nUsed+= $linek->day_used;
				}
			}
			$obj->days_used = $nUsed;
		}
		// Show here line of result
		print '<tr '.$bc[$var].'>';
		if ($action == 'edit' && $idr == $obj->id)
		{
			// LIST_OF_TD_FIELDS_LIST
			if (! empty($arrayfields['t.fk_member']['checked']))
			{
				print '<td>'.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.date_ini']['checked']))
			{
				print '<td>';
				print $form->select_date((GETPOST('dateini')?GETPOST('dateini'):$db->jdate($obj->date_ini)),'di_',0,0,1);
				print '</td>';

				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.date_fin']['checked']))
			{
				print '<td>';
				print $form->select_date((GETPOST('datefin')?GETPOST('datefin'):$db->jdate($obj->date_fin)),'df_',0,0,1);

				print '</td>';
				if (! $i) $totalarray['nbfield']++;
			}

			if (! empty($arrayfields['t.period_year']['checked']))
			{
				print '<td>'.'<input type="number" min="0" name="period_year" value="'.(GETPOST('period_year')?GETPOST('period_year'):$obj->period_year).'">'.'</td>';

				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.days_assigned']['checked']))
			{
				print '<td>'.'<input type="number" min="0" name="days_assigned" value="'.(GETPOST('days_assigned')?GETPOST('days_assigned'):$obj->days_assigned).'">'.'</td>';

				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.days_used']['checked']))
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
			// Action column
			print '<td class="nowrap" align="right">';
			print '<input class="butAction" type="submit" value="'.$langs->trans('Keep').'">';
			print '<input class="butAction" type="submit" name="cancel" value="'.$langs->trans('Return').'">';
			print '</td>';
			if (! $i) $totalarray['nbfield']++;
		}
		else
		{

			// LIST_OF_TD_FIELDS_LIST
			if (! empty($arrayfields['t.fk_member']['checked']))
			{
				print '<td>'.$object->lastname.' '.$object->firstname.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.date_ini']['checked']))
			{
				print '<td>'.dol_print_date($db->jdate($obj->date_ini),'day').'</td>';
				$aVacacion[$i]['inicio'] = $obj->date_ini;
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.date_fin']['checked']))
			{
				print '<td>'.dol_print_date($db->jdate($obj->date_fin),'day').'</td>';
				$aVacacion[$i]['fin'] = $obj->date_fin;
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.period_year']['checked']))
			{
				print '<td>'.$obj->period_year.'</td>';
				$aVacacion[$i]['gestion'] = $obj->period_year;
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.days_assigned']['checked']))
			{
				print '<td>'.$obj->days_assigned.'</td>';
				$aVacacion[$i]['asignados'] =$obj->days_assigned;
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.days_used']['checked']))
			{
				print '<td>'.$obj->days_used.'</td>';
				$aVacacion[$i]['usados'] =$obj->days_used;
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.fk_user_create']['checked']))
			{
				$objUser->fetch($obj->fk_user_create);
				print '<td>'.$objUser->getNomUrl(1).'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.fk_user_mod']['checked']))
			{
				$objUser->fetch($obj->fk_user_mod);
				print '<td>'.$objUser->getNomUrl(1).'</td>';
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
		// Status

			if (! empty($arrayfields['t.status']['checked']))
			{

				$objMembervacation->status = $obj->status;

				print '<td align="right">'.$objMembervacation->getLibStatut(3).'</td>';
				$aVacacion[$i]['estado'] = $objMembervacation->getLibStatut(0);
			}

			// Action column
			print '<td class="nowrap" align="right">';
			if ($user->rights->assistance->vac->del && $obj->status == 0)
			{
				print '<a href="'.$_SERVER['PHP_SELF'].'?rowid='.$id.'&idr='.$obj->rowid.'&action=delete"">'.img_picto($langs->trans('Delete'),'delete').'</a>';
				print '&nbsp;';
			}
			if ($obj->status==0 && $user->rights->assistance->vac->app)
			{
				if ($obj->date_ini && $obj->date_fin)
					print '<a href="'.$_SERVER['PHP_SELF'].'?rowid='.$id.'&idr='.$obj->rowid.'&action=validate"">'.img_picto($langs->trans('Validate'),'info').'</a>';
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
			if ($num < $limit && empty($offset)) print '<td align="left">'.$langs->trans("Total").'</td>';
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
//print '</div>'."\n";

print '</form>'."\n";


	// Buttons
print '<div class="tabsAction">'."\n";
$parameters=array();
$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	if ($user->rights->assistance->vac->write)
	{
		print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?rowid='.$object->id.'&amp;action=create">'.$langs->trans("Createnew").'</a></div>'."\n";
	}
}
print '</div>';

$_SESSION['aVacacion'] = serialize($aVacacion);

// End of page
