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
 *   	\file       mant/mequipmentprogram_list.php
 *		\ingroup    mant
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-04-07 18:08
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

$action 	= GETPOST('action','alpha');
$massaction 	= GETPOST('massaction','alpha');
$show_files 	= GETPOST('show_files','int');
$confirm	= GETPOST('confirm','alpha');
$toselect 	= GETPOST('toselect', 'array');

$id		= GETPOST('id','int');
$backtopage 	= GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');

$search_all 	= trim(GETPOST("sall"));

$search_fk_equipment=GETPOST('search_fk_equipment','int');
$search_fk_type_repair=GETPOST('search_fk_type_repair','alpha');
$search_accountant=GETPOST('search_accountant','int');
$search_description=GETPOST('search_description','alpha');
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
	't.fk_type_repair'=>'Ref',
	't.note_public'=>'NotePublic',
	);
if (empty($user->socid)) $fieldstosearchall["t.note_private"]="NotePrivate";

// Definition of fields for list
$arrayfields=array(
	
	't.fk_type_repair'=>array('label'=>$langs->trans("Typerepair"), 'checked'=>1),
	't.fk_parent_previous'=>array('label'=>$langs->trans("Typerepairprevious"), 'checked'=>1),
	't.accountant'=>array('label'=>$langs->trans("Fieldaccountant"), 'checked'=>1),
	't.description'=>array('label'=>$langs->trans("Fielddescription"), 'checked'=>1),
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


// Load object if id or fk_type_repair is provided as parameter
$objectp=new Mequipmentprogram($db);
if ($idr > 0 && $action != 'add')
{
	$result=$objectp->fetch($idr);
	if ($result < 0) dol_print_error($db);
}




/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

if (GETPOST('cancel')) { $action='list'; $massaction=''; }

/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

$now=dol_now();

$form=new Form($db);

//$help_url="EN:Module_Customers_Orders|FR:Module_Commandes_Clients|ES:Módulo_Pedidos_de_clientes";
$help_url='';
$title = $langs->trans('Programmed');

// Put here content of your page


$sql = "SELECT";
$sql.= " t.rowid,";

$sql .= " t.fk_equipment,";
$sql .= " t.fk_parent_previous,";
$sql .= " t.fk_type_repair,";
$sql .= " t.accountant,";
$sql .= " t.description,";
$sql .= " t.fk_user_create,";
$sql .= " t.fk_user_mod,";
$sql .= " t.datec,";
$sql .= " t.datem,";
$sql .= " t.tms,";
$sql .= " t.active";
$sql .= " , r.ref, r.label";

// Add fields from extrafields
foreach ($extrafields->attribute_label as $key => $val) $sql.=($extrafields->attribute_type[$key] != 'separate' ? ",ef.".$key.' as options_'.$key : '');
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."m_equipment_program as t";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."m_type_repair AS r ON t.fk_type_repair = r.rowid ";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."m_equipment_program_extrafields as ef on (t.rowid = ef.fk_object)";
$sql.= " WHERE t.fk_equipment = " .$object->id;
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_fk_equipment) $sql.= natural_search("fk_equipment",$search_fk_equipment);
if ($search_fk_type_repair) $sql.= natural_search("fk_type_repair",$search_fk_type_repair);
if ($search_accountant) $sql.= natural_search("accountant",$search_accountant);
if ($search_description) $sql.= natural_search("description",$search_description);
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
print '<input type="hidden" name="id" value="'.$id.'">';
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

print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, '', $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);

if ($sall)
{
	foreach($fieldstosearchall as $key => $val) $fieldstosearchall[$key]=$langs->trans($val);
	print $langs->trans("FilterOnInto", $sall) . join(', ',$fieldstosearchall);
}

$mofk_type_repairorfilter = '';
//$mofk_type_repairorfilter.='<div class="divsearchfield">';
//$mofk_type_repairorfilter.= $langs->trans('MyFilter') . ': <input type="text" name="search_myfield" value="'.dol_escape_htmltag($search_myfield).'">';
//$mofk_type_repairorfilter.= '</div>';

$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldPreListTitle',$parameters);    // Note that $action and $object may have been modified by hook
if (empty($reshook)) $mofk_type_repairorfilter .= $hookmanager->resPrint;
else $mofk_type_repairorfilter = $hookmanager->resPrint;

if (! empty($mofk_type_repairorfilter))
{
	print '<div class="liste_titre liste_titre_bydiv centpercent">';
	print $mofk_type_repairorfilter;
	print '</div>';
}

$varpage=empty($contextpage)?$_SERVER["PHP_SELF"]:$contextpage;
$selectedfields=$form->multiSelectArrayWithCheckbox('selectedfields', $arrayfields, $varpage);	// This also change content of $arrayfields

print '<div class="div-table-responsive">';
print '<table class="tagtable liste'.($mofk_type_repairorfilter?" listwithfilterbefore":"").'">'."\n";

// Fields title
print '<tr class="liste_titre">';
// 
if (! empty($arrayfields['t.fk_equipment']['checked'])) print_liste_field_titre($arrayfields['t.fk_equipment']['label'],$_SERVER['PHP_SELF'],'t.fk_equipment','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_type_repair']['checked'])) print_liste_field_titre($arrayfields['t.fk_type_repair']['label'],$_SERVER['PHP_SELF'],'t.fk_type_repair','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_parent_previous']['checked'])) print_liste_field_titre($arrayfields['t.fk_parent_previous']['label'],$_SERVER['PHP_SELF'],'t.fk_type_repair','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.accountant']['checked'])) print_liste_field_titre($arrayfields['t.accountant']['label'],$_SERVER['PHP_SELF'],'t.accountant','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.description']['checked'])) print_liste_field_titre($arrayfields['t.description']['label'],$_SERVER['PHP_SELF'],'t.description','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_create']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_create']['label'],$_SERVER['PHP_SELF'],'t.fk_user_create','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_mod']['label'],$_SERVER['PHP_SELF'],'t.fk_user_mod','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.active']['checked'])) print_liste_field_titre($arrayfields['t.active']['label'],$_SERVER['PHP_SELF'],'t.active','',$params,'',$sortfield,$sortorder);

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

if ($action != 'create' && $action != 'edit')
{
// Fields title search
	print '<tr class="liste_titre">';
// 
	if (! empty($arrayfields['t.fk_equipment']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_equipment" value="'.$search_fk_equipment.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_type_repair']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_type_repair" value="'.$search_fk_type_repair.'" size="10"></td>';
	if (! empty($arrayfields['t.fk_parent_previous']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_parent_previous" value="'.$search_fk_parent_previous.'" size="10"></td>';
	if (! empty($arrayfields['t.accountant']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_accountant" value="'.$search_accountant.'" size="10"></td>';
	if (! empty($arrayfields['t.description']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_description" value="'.$search_description.'" size="10"></td>';
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
	$searchpitco=$form->showFilterAndCheckAddButtons($massactionbutton?1:0, 'checkforselect', 1);
	print $searchpitco;
	print '</td>';
	print '</tr>'."\n";
}    

$objtyperepair = new Mtyperepair($db);

//obtenemos los registrados para fk_parent_previous
$objectptmp = new Mequipmentprogram($db);
$objtyperepairtmp = new Mtyperepair($db);
$filterstatic = " AND t.fk_equipment = ".$object->id;
$objectptmp->fetchAll('ASC','t.fk_type_repair', 0, 0, array(1=>1),'AND',$filterstaic);
$aTypeRepair = array();
$aMEPTypeRepair = array();
foreach ((array) $objectptmp->lines AS $j => $line)
{
	$aTypeRepair[$line->fk_type_repair] = $line->fk_type_repair;
	$res = $objtyperepairtmp->fetch($line->fk_type_repair);
	if ($res>0)
		$aMEPTypeRepair[$line->id] = $objtyperepairtmp->ref.' - '.$objtyperepairtmp->label;
}
$filterstatic = '';
$objtyperepairtmp->fetchAll('ASC','t.ref', 0, 0, array('status'=>1),'AND',$filterstaic);

$options = '<option value="0"></option>';

foreach ((array) $objtyperepairtmp->lines AS $j => $line)
{
	if (!$aTypeRepair[$line->id])
		$options.= '<option value="'.$line->id.'">'.$line->ref.' - '.$line->label.'</option>';
}

$i=0;
$var=true;
$totalarray=array();
if ($action == 'create')
{
	$var = !$var;

	// Show here line of result
	print '<tr '.$bc[$var].'>';
	if (! empty($arrayfields['t.fk_type_repair']['checked'])) 
	{
		print '<td>'.'<select name="fk_type_repair">'.$options.'</select>'.'</td>';
		//print '<td>'.'<input type="text" class="flat" name="fk_type_repair" value="'.GETPOST('fk_type_repair','alpha').'">'.'</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.fk_parent_previous']['checked'])) 
	{
		print '<td>';
		print $form->selectarray('fk_parent_previous',$aMEPTypeRepair,GETPOST('fk_parent_previous'));
		print '</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.accountant']['checked'])) 
	{
		print '<td>'.'<input type="number" min="0" class="flat" name="accountant" value="'.GETPOST('accountant'.'int').'">'.'</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.description']['checked'])) 
	{
		print '<td>'.'<input type="text" class="flat" name="description" value="'.GETPOST('description'.'alpha').'">'.'</td>';
		if (! $i) $totalarray['nbfield']++;
	}
	if (! empty($arrayfields['t.active']['checked'])) 
	{
		print '<td>';
		print $form->selectyesno('active',(GETPOST('active')?GETPOST('active'):1),1);
		print '</td>';
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
		if ($obj->id == $fkEquipmentProgram)
			print '<tr class="mark">';
		else
			print '<tr '.$bc[$var].'>';
		// LIST_OF_TD_FIELDS_LIST
		if ($action == 'edit' && $idr == $obj->rowid)
		{
			if (! empty($arrayfields['t.fk_type_repair']['checked'])) 
			{
				print '<td>'.'<input type="text" class="flat" name="fk_type_repair" value="'.$obj->fk_type_repair.'">'.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.fk_parent_previous']['checked'])) 
			{
				print '<td>'.'<select name="fk_parent_previous">'.$options.'</select>'.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.accountant']['checked'])) 
			{
				print '<td>'.'<input type="text" class="flat" name="accountant" value="'.$obj->accountant.'">'.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.description']['checked'])) 
			{
				print '<td>'.'<input type="text" class="flat" name="description" value="'.$obj->description.'">'.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.active']['checked'])) 
			{
				print '<td>';
				print $form->selectyesno('active',$obj->active,1);
				print '</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			print '<td>';
			print '<input type="submit" name="submit" value="'.$langs->trans('Save').'">';
			print '</td>';
		}
		else
		{
			if (! empty($arrayfields['t.fk_type_repair']['checked'])) 
			{
				$rest=$objtyperepair->fetch($obj->fk_type_repair);
				if ($rest>0)
					print '<td>'.$objtyperepair->getNomUrl().'</td>';
				else
					print '<td></td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.fk_parent_previous']['checked'])) 
			{
				$res = $objectptmp->fetch($obj->fk_parent_previous);
				if ($res >0)
				{
					$rest=$objtyperepair->fetch($objectptmp->fk_type_repair);
					if ($rest>0)
						print '<td>'.$objtyperepair->getNomUrl().'</td>';
					else
						print '<td></td>';
				}
				else
					print '<td>'.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.accountant']['checked'])) 
			{
				print '<td>'.$obj->accountant.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.description']['checked'])) 
			{
				print '<td>'.$obj->description.'</td>';
				if (! $i) $totalarray['nbfield']++;
			}
			if (! empty($arrayfields['t.active']['checked'])) 
			{
				print '<td>'.($obj->active?$langs->trans('Yes'):$langs->trans('No')).'</td>';
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
			{
				//$selected=0;
				//if (in_array($obj->rowid, $arrayofselected)) $selected=1;
				//print '<input id="cb'.$obj->rowid.'" class="flat checkforselect" type="checkbox" name="toselect[]" value="'.$obj->rowid.'"'.($selected?' checked="checked"':'').'>';
			}
			if ($user->rights->mant->equ->write)
			{
				print '<a hfk_type_repair="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$obj->rowid.'&action=edit">'.img_picto($langs->trans('Edit'),'edit').'</a>';
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
		print '<div class="inline-block divButAction"><a class="butAction" hfk_type_repair="'.$_SERVER['PHP_SELF'].'?id='.$id.'&action=create">'.$langs->trans("New").'</a></div>'."\n";
	}
}
print '</div>'."\n";

if ($massaction == 'builddoc' || $action == 'remove_file' || $show_files)
{
	// Show list of available documents
	$urlsource=$_SERVER['PHP_SELF'].'?sortfield='.$sortfield.'&sortorder='.$sortorder;
	$urlsource.=str_replace('&amp;','&',$param);

	$filedir=$diroutputmassaction;
	$genallowed=$user->rights->facture->lire;
	$delallowed=$user->rights->facture->lire;

	print $formfile->showdocuments('massfilesarea_mant','',$filedir,$urlsource,0,$delallowed,'',1,1,0,48,1,$param,$title,'');
}
else
{
	print '<br><a name="show_files"></a><a hfk_type_repair="'.$_SERVER["PHP_SELF"].'?show_files=1'.$param.'#show_files">'.$langs->trans("ShowTempMassFilesArea").'</a>';
}
