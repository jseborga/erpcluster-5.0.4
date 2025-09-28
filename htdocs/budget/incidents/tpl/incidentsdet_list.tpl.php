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
 *   	\file       budget/incidentsdet_list.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-05-11 10:19
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

$lUpdate = true;
$search_all=trim(GETPOST("sall"));

$search_fk_incident=GETPOST('search_fk_incident','int');
$search_type=GETPOST('search_type','alpha');
$search_fk_object=GETPOST('search_fk_object','int');
$search_object=GETPOST('search_object','int');
$search_label=GETPOST('search_label','alpha');
$search_sequen=GETPOST('search_sequen','int');
$search_value_one=GETPOST('search_value_one','alpha');
$search_value_two=GETPOST('search_value_two','alpha');
$search_value_three=GETPOST('search_value_three','alpha');
$search_value_four=GETPOST('search_value_four','alpha');
$search_quantity=GETPOST('search_quantity','int');
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
//if ($type=='copiesplane')
$contextpage=GETPOST('contextpage','aZ')?GETPOST('contextpage','aZ'):'budgetincidents'.$type.'list';

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('budgetlist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('budget');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// List of fields to search into when doing a "search in all"
$fieldstosearchall = array(
	't.ref'=>'Ref',
	't.note_public'=>'NotePublic',
);
if (empty($user->socid)) $fieldstosearchall["t.note_private"]="NotePrivate";

// Definition of fields for list

$arrayfields['t.fk_incident']=array('label'=>$langs->trans("Fieldfk_incident"), 'align'=>'align="left"', 'checked'=>0);
$arrayfields['t.type']=array('label'=>$langs->trans("Fieldtype"), 'align'=>'align="left"', 'checked'=>0);
$arrayfields['t.fk_object']=array('label'=>$langs->trans("Fieldfk_object"), 'align'=>'align="left"', 'checked'=>0);
$arrayfields['t.object']=array('label'=>$langs->trans("Fieldobject"), 'align'=>'align="left"', 'checked'=>0);
$arrayfields['t.sequen']=array('label'=>$langs->trans("Fieldsequen"), 'align'=>'align="left"', 'checked'=>1);
$arrayfields['t.label']=array('label'=>$langs->trans("Fieldlabel"), 'align'=>'align="left"', 'checked'=>1);
$titleone='n';
$titletwo='n';
$lOne=true;
$lTwo=true;
$lThree=true;
$lFour=true;
$lFive=true;
$lSix=true;
$lSeven=true;
$lQuant=false;
$lUnit=false;
$titlequant=$langs->trans("Fieldquantity");
if ($type=='inactivity' || $type=='benefits')
{
	$nCheck=2;
	$titleone = $langs->trans('Dayswithoutproduction');
	$titletwo = $langs->trans('Paidjourneys');
}
if ($type=='subsidies')
{
	if (empty($typetwo))
	{
		$nCheck=4;
		$titleone = $langs->trans('Percentutilization');
		$titletwo = $langs->trans('Salarymin');
		$titlethree = $langs->trans('Monthduration');
		$titlefour = $langs->trans('Annualamount');
		$lFour=false;
	}
	else
	{
		$nCheck=4;
		$titleone = $langs->trans('Workpercent');
		$titletwo = $langs->trans('Liquidsalary');
		$titlethree = $langs->trans('Journalmonth');
		$titlefour = $langs->trans('Ponderation');
		$lThree=false;
		$lFour=false;
		$lQuant=true;
	}
}
if ($type=='occupational')
{
	if (empty($typetwo))
	{
		$nCheck=2;
		$titleone = $langs->trans('Averagecostman');
		$titletwo = $langs->trans('Averagecostmanmonth');
		$lTwo=false;
	}
	else
	{
		$nCheck=4;
		$titleone = $langs->trans('Quantitybyannual');
		$titletwo = $langs->trans('Priceunit');
		$titlethree = $langs->trans('Byannual');
		$titlefour = $langs->trans('Bymonth');
		$lThree=false;
		$lFour=false;
	}
}

if ($type=='contribution')
{
	$nCheck=3;
	$titleone = $langs->trans('Percentpatronal');
	$titletwo = $langs->trans('Percentlaboral');
	$titlethree = $langs->trans('Percenttotal');
	$lThree=false;
	$lFour=false;

}
if ($type=='antiquity')
{
	$nCheck=1;
	$titleone = $langs->trans('Fieldvalue');
}
if ($type=='minortools')
{
	$lUnit=true;
	$nCheck=4;
	$titleone = $langs->trans('Quantityyear');
	$titletwo = $langs->trans('Duration');
	$titlethree = $langs->trans('Price');
	$titlefour = $langs->trans('Costyear');
	$lFour=false;
}
if ($type=='copiesplane')
{
	$nCheck=3;
	$titleone = $langs->trans('Averagecost');
	$titletwo = $langs->trans('Numberoftenders');
	$titlethree = $langs->trans('Asignpercent');
	$lThree=false;
}
if ($type=='propossal')
{
	$nCheck=5;
	$titleone = $langs->trans('Averagecost');
	$titletwo = $langs->trans('Time');
	$titlethree = $langs->trans('Totalcost');
	$titlefour = $langs->trans('Numberoftenders');
	$titlefive = $langs->trans('Asignpercent');
	$lFive=false;
	$lThree=false;
}
if ($type=='legaldoc')
{
	$nCheck=5;
	$titleone = $langs->trans('Averagecost');
	$titletwo = $langs->trans('Num.');
	$titlethree = $langs->trans('Totalcost');
	$titlefour = $langs->trans('Numberoftenders');
	$titlefive = $langs->trans('Asignpercent');
	$lFive=false;
	$lThree=false;
}
if ($type=='guarantees')
{
	$nCheck=5;
	$titleone = $langs->trans('Percent');
	$titletwo = $langs->trans('Amountsecured');
	$titlethree = $langs->trans('Numberoftenders');
	$titlefour = $langs->trans('Commissions');
	$titlefive = $langs->trans('Asignpercent');
	$lFour=false;
	$lFive=false;
	$lTwo=false;
}
if ($type=='operation' || $type=='administrative')
{
	$nCheck=5;
	$titleone = $langs->trans('Averagecostmonth');
	$titletwo = $langs->trans('Workingmonth');
	$titlethree = $langs->trans('Annualamount');
	$titlefour = $langs->trans('Costtotal');
	$titlefive = $langs->trans('Asignpercent');
	$lThree=false;
	$lFour=false;
	$lFive=false;
}
if ($type=='mobilization'||$type=='traffic'||$type=='risk')
{
	$nCheck=4;
	$titleone = $langs->trans('Averagecostmonth');
	$titletwo = $langs->trans('Costyear');
	$titlethree = $langs->trans('Costtotal');
	$titlefour = $langs->trans('Asignpercent');
	$lTwo=false;
	$lThree=false;
	$lFour=false;
}
if ($type=='faenas')
{
	$nCheck=4;
	$titleone = $langs->trans('Priceunit');
	$titletwo = $langs->trans('Quantity');
	$titlethree = $langs->trans('Costtotal');
	$titlefour = $langs->trans('Asignpercent');
	$lThree=false;
	$lFour=false;
}
if ($type=='costmo')
{
	$nCheck=7;
	$titleone = $langs->trans('Averagesalarymonth');
	$titletwo = $langs->trans('Socialbenefits');
	$titlethree = $langs->trans('Feedingcostmonth');
	$titlefour = $langs->trans('IVAmonth');
	$titlefive = $langs->trans('Totalcostmonth');
	$titlesix = $langs->trans('Costhourmonth');
	$titleseven = $langs->trans('Costohoursecondcurrency');
	$lOne=false;
	$lTwo=false;
	$lThree=false;
	$lFour=false;
	$lFive=false;
	$lSix=false;
	$lSeven=false;
}
$arrayfields['t.fk_unit']=array('label'=>$langs->trans("Fieldfk_unit"), 'align'=>'align="left"', 'checked'=>($lUnit?1:0));
$arrayfields['t.value_one']=array('label'=>$titleone, 'align'=>'align="right"', 'checked'=>1);
$arrayfields['t.value_two']=array('label'=>$titletwo, 'align'=>'align="right"', 'checked'=>(($nCheck>=2 && $nCheck<=7)?1:0));
$arrayfields['t.value_three']=array('label'=>$titlethree, 'align'=>'align="right"', 'checked'=>(($nCheck>=3 && $nCheck<=7)?1:0));
$arrayfields['t.value_four']=array('label'=>$titlefour, 'align'=>'align="right"', 'checked'=>(($nCheck>=4 && $nCheck<=7)?1:0));
$arrayfields['t.value_five']=array('label'=>$titlefive, 'align'=>'align="right"', 'checked'=>(($nCheck>=5 && $nCheck<=7)?1:0));
$arrayfields['t.value_six']=array('label'=>$titlesix, 'align'=>'align="right"', 'checked'=>(($nCheck==6||$nCheck==7)?1:0));
$arrayfields['t.value_seven']=array('label'=>$titleseven, 'align'=>'align="right"', 'checked'=>($nCheck==7?1:0));
$arrayfields['t.quantity']=array('label'=>$titlequant, 'align'=>'align="right"', 'checked'=>($lQuant?1:0));
$arrayfields['t.fk_user_create']=array('label'=>$langs->trans("Fieldfk_user_create"), 'align'=>'align="left"', 'checked'=>0);
$arrayfields['t.fk_user_mod']=array('label'=>$langs->trans("Fieldfk_user_mod"), 'align'=>'align="left"', 'checked'=>0);
$arrayfields['t.status']=array('label'=>$langs->trans("Fieldstatus"), 'align'=>'align="left"', 'checked'=>0);


	//'t.entity'=>array('label'=>$langs->trans("Entity"), 'checked'=>1, 'enabled'=>(! empty($conf->multicompany->enabled) && empty($conf->multicompany->transverse_mode))),
$arrayfields['t.datec']=array('label'=>$langs->trans("DateCreationShort"), 'align'=>'align="left"', 'checked'=>0, 'position'=>500);
$arrayfields['t.tms']=array('label'=>$langs->trans("DateModificationShort"), 'align'=>'align="left"', 'checked'=>0, 'position'=>500);
	//'t.statut'=>array('label'=>$langs->trans("Status"), 'checked'=>1, 'position'=>1000),

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

		$search_fk_incident='';
		$search_type='';
		$search_fk_object='';
		$search_object='';
		$search_ref='';
		$search_label='';
		$search_sequen='';
		$search_value_one='';
		$search_value_two='';
		$search_value_three='';
		$search_value_four='';
		$search_quantity='';
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
$title = $langs->trans('Incidents');

// Put here content of your page

$sql = "SELECT";
$sql.= " t.rowid,";

$sql .= " t.fk_incident,";
$sql .= " t.type,";
$sql .= " t.fk_object,";
$sql .= " t.object,";
$sql .= " t.label,";
$sql .= " t.fk_unit,";
$sql .= " t.sequen,";
$sql .= " t.value_one,";
$sql .= " t.value_two,";
$sql .= " t.value_three,";
$sql .= " t.value_four,";
$sql .= " t.value_five,";
$sql .= " t.quantity,";
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
$sql.= " FROM ".MAIN_DB_PREFIX."incidents_det as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."incidents_det_extrafields as ef on (t.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
$sql.= " AND t.fk_incident = ".$id;
if (empty($typetwo))
	$sql.= " AND t.type = '".$type."'";
else
	$sql.= " AND t.type = '".$typetwo."'";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_fk_incident) $sql.= natural_search("fk_incident",$search_fk_incident);
if ($search_type) $sql.= natural_search("type",$search_type);
if ($search_fk_object) $sql.= natural_search("fk_object",$search_fk_object);
if ($search_object) $sql.= natural_search("object",$search_object);
if ($search_label) $sql.= natural_search("label",$search_label);
if ($search_sequen) $sql.= natural_search("sequen",$search_sequen);
if ($search_value_one) $sql.= natural_search("value_one",$search_value_one);
if ($search_value_two) $sql.= natural_search("value_two",$search_value_two);
if ($search_value_three) $sql.= natural_search("value_three",$search_value_three);
if ($search_value_four) $sql.= natural_search("value_four",$search_value_four);
if ($search_quantity) $sql.= natural_search("quantity",$search_quantity);
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
//if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
//{
//	$result = $db->query($sql);
//	$nbtotalofrecords = $db->num_rows($result);
//}

//$sql.= $db->plimit($limit+1, $offset);

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
	header("Location: ".DOL_URL_ROOT.'/incidentsdet/card.php?id='.$id);
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
if ($user->rights->budget->supprimer) $arrayofmassactions['delete']=$langs->trans("Delete");
if ($massaction == 'presend') $arrayofmassactions=array();
$massactionbutton=$form->selectMassAction('', $arrayofmassactions);

print '<form method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';

if ($action == 'create' || $action == 'createtwo')
	print '<input type="hidden" name="action" value="add">';
elseif ($action == 'edit')
{
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="idr" value="'.$idr.'">';
	print '<input type="hidden" name="type" value="'.$type.'">';
	$lUpdate = false;
}
else print '<input type="hidden" name="action" value="list">';
print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
print '<input type="hidden" name="contextpage" value="'.$contextpage.'">';
print '<input type="hidden" name="id" value="'.$object->id.'">';
if ($typetwo) print '<input type="hidden" name="typetwo" value="'.$typetwo.'">';
if ($subaction) print '<input type="hidden" name="subaction" value="'.$subaction.'">';
//print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, '', $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);

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
if (! empty($arrayfields['t.fk_incident']['checked'])) print_liste_field_titre($arrayfields['t.fk_incident']['label'],$_SERVER['PHP_SELF'],'t.fk_incident','',$params,$arrayfields['t.fk_incident']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.type']['checked'])) print_liste_field_titre($arrayfields['t.type']['label'],$_SERVER['PHP_SELF'],'t.type','',$params,$arrayfields['t.type']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_object']['checked'])) print_liste_field_titre($arrayfields['t.fk_object']['label'],$_SERVER['PHP_SELF'],'t.fk_object','',$params,$arrayfields['t.fk_object']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.object']['checked'])) print_liste_field_titre($arrayfields['t.object']['label'],$_SERVER['PHP_SELF'],'t.object','',$params,$arrayfields['t.object']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.sequen']['checked'])) print_liste_field_titre($arrayfields['t.sequen']['label'],$_SERVER['PHP_SELF'],'t.sequen','',$params,$arrayfields['t.sequen']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.label']['checked'])) print_liste_field_titre($arrayfields['t.label']['label'],$_SERVER['PHP_SELF'],'t.label','',$params,$arrayfields['t.label']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_unit']['checked'])) print_liste_field_titre($arrayfields['t.fk_unit']['label'],$_SERVER['PHP_SELF'],'t.fk_unit','',$params,$arrayfields['t.fk_unit']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.value_one']['checked'])) print_liste_field_titre($arrayfields['t.value_one']['label'],$_SERVER['PHP_SELF'],'t.value_one','',$params,$arrayfields['t.value_one']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.value_two']['checked'])) print_liste_field_titre($arrayfields['t.value_two']['label'],$_SERVER['PHP_SELF'],'t.value_two','',$params,$arrayfields['t.value_two']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.value_three']['checked'])) print_liste_field_titre($arrayfields['t.value_three']['label'],$_SERVER['PHP_SELF'],'t.value_three','',$params,$arrayfields['t.value_three']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.value_four']['checked'])) print_liste_field_titre($arrayfields['t.value_four']['label'],$_SERVER['PHP_SELF'],'t.value_four','',$params,$arrayfields['t.value_four']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.value_five']['checked'])) print_liste_field_titre($arrayfields['t.value_five']['label'],$_SERVER['PHP_SELF'],'t.value_five','',$params,$arrayfields['t.value_five']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.value_six']['checked'])) print_liste_field_titre($arrayfields['t.value_six']['label'],$_SERVER['PHP_SELF'],'t.value_six','',$params,$arrayfields['t.value_six']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.value_seven']['checked'])) print_liste_field_titre($arrayfields['t.value_seven']['label'],$_SERVER['PHP_SELF'],'t.value_seven','',$params,$arrayfields['t.value_seven']['align'],$sortfield,$sortorder);

if (! empty($arrayfields['t.quantity']['checked'])) print_liste_field_titre($arrayfields['t.quantity']['label'],$_SERVER['PHP_SELF'],'t.quantity','',$params,$arrayfields['t.quantity']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_create']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_create']['label'],$_SERVER['PHP_SELF'],'t.fk_user_create','',$params,$arrayfields['t.fk_user_create']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_mod']['label'],$_SERVER['PHP_SELF'],'t.fk_user_mod','',$params,$arrayfields['t.fk_user_mod']['align'],$sortfield,$sortorder);
if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($arrayfields['t.status']['label'],$_SERVER['PHP_SELF'],'t.status','',$params,$arrayfields['t.status']['align'],$sortfield,$sortorder);

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
unset($obj);

if ((!empty($typetwo) && $subaction == $typetwo && $action == 'createtwo') || (empty($typetwo) && empty($subaction) && $action=='create'))
{
	$var = !$var;

		// Show here line of result
	print '<tr '.$bc[$var].'>';
		// LIST_OF_TD_FIELDS_LIST
	$lAddbutton=true;
	foreach ($arrayfields as $key => $value) {
		if (!empty($arrayfields[$key]['checked'])) {
				//$key2 = str_replace('t.', '', $key);
			$aKey = explode('.',$key);
			$key2 = $aKey[1];
			if ($key2 == 'active')
			{
				$img = 'switch_off';
				if ($obj->$key2) $img = 'switch_on';
				$obj->$key2 = img_picto('',$img);
			}
			if ($key2 == 'status')
			{
				$objIncidentsdet->status = $obj->$key2;
				$obj->$key2 = $objIncidentsdet->getLibStatut(3);
			}
			if ($key2 == 'fk_user_create' || $key2 == 'fk_user_mod')
			{
				$res = $objUser->fetch($obj->$key2);
				if ($res == 1)
					$obj->$key2 = $objUser->getNomUrl(1);
			}
			if ($key2=='type')
				print '<input type="hidden" class="flat" name="'.$key2.'" value="'. ($typetwo?$typetwo:$type) . '">';
			elseif ($key2=='sequen')
			{
					//vamos a buscar la secuencia correspondiente
				$filter = " AND t.fk_incident = ".$object->id;
				$filter.= " AND t.type = '".($typetwo?$typetwo:$type)."'";
				$max = 0;
				$res = $objIncidentsdet->fetchAll('DESC','t.sequen',0,0,array(),'AND',$filter);
				if ($res>0)
				{
					$lines = $objIncidentsdet->lines;
					foreach ($lines AS $j => $line)
					{
						if (empty($max)) $max = $line->sequen + 1;
					}
				}
				elseif($res==0)
					$max = 1;
				else
				{
					$error++;
					setEventMessages($objIncidentsdet->error,$objIncidentsdet->errors,'errors');
				}
				print '<td '.$arrayfields[$key]['align'].'>' .'<input type="text" class="flat" name="'.$key2.'" value="'. $max . '" readonly></td>';
			}
			elseif ($key2=='label')
			{
				if ($type=='antiquity')
				{
					print '<td>';
					if ($aArray[$max])
					{
						print '<input type="text" name="label" value="'.$aArray[$max].'" readonly>';
					}
					else
					{
						$lAddbutton=false;
						$lOne=false;
					}
					print '</td>';
				}
				elseif ($type=='costmo')
				{
					print '<td>';
					if (is_array($aPolitic) && count($aPolitic)>0)
					{
						print $form->selectarray('label',$aPolitic,GETPOST('label'));
					}
					else $lAddbutton=false;
					print '</td>';
				}
				else
					print '<td>' .'<input type="text" class="flat" name="'.$key2.'" value="'. $obj->$key2 . '" autofocus></td>';
			}
			elseif($key2=='fk_unit')
			{
				if ($lUnit)
				{
					print '<td>';
					print $form->selectUnits(GETPOST('fk_unit'),'fk_unit',1);
					print '</td>';
				}
			}
			elseif( $key2=='value_one'|| $key2=='value_two' || $key2=='value_three' || $key2=='value_four' || $key2=='value_five' || $key2=='value_six' || $key2=='value_seven' || $key2=='quantity')
			{
				if ($key2=='value_one'&&$lOne) print '<td '.$arrayfields[$key]['align'].'>' .'<input type="number" class="flat len80" step="any" name="'.$key2.'" value="'. $obj->$key2 . '"></td>';
				elseif ($key2=='value_two'&&$lTwo) print '<td '.$arrayfields[$key]['align'].'>' .'<input type="number" class="flat len80" step="any" name="'.$key2.'" value="'. $obj->$key2 . '"></td>';
				elseif ($key2=='value_three'&&$lThree) print '<td '.$arrayfields[$key]['align'].'>' .'<input type="number" class="flat len80" step="any" name="'.$key2.'" value="'. $obj->$key2 . '"></td>';
				elseif ($key2=='value_four'&&$lFour) print '<td '.$arrayfields[$key]['align'].'>' .'<input type="number" class="flat len80" step="any" name="'.$key2.'" value="'. $obj->$key2 . '"></td>';
				elseif ($key2=='value_five'&&$lFive) print '<td '.$arrayfields[$key]['align'].'>' .'<input type="number" class="flat len80" step="any" name="'.$key2.'" value="'. $obj->$key2 . '"></td>';
				elseif ($key2=='value_six'&&$lSix) print '<td '.$arrayfields[$key]['align'].'>' .'<input type="number" class="flat len80" step="any" name="'.$key2.'" value="'. $obj->$key2 . '"></td>';
				elseif ($key2=='value_seven'&&$lSeven) print '<td '.$arrayfields[$key]['align'].'>' .'<input type="number" class="flat len80" step="any" name="'.$key2.'" value="'. $obj->$key2 . '"></td>';
				else
					print '<td></td>';
			}
			if (!$i)
				$totalarray['nbfield'] ++;
		}
	}
	print '<td nowrap>';
	if ($lAddbutton)
		print '<input class="butAction" type="submit" value="'.$langs->trans('Save').'">';
	print '<input class="butAction" type="submit" name="cancel" value="'.$langs->trans('Cancel').'">';
	print '</td>';
	print '</tr>';
}

$i=0;
$var=true;
$totalarray=array();
while ($i < $num)
{
	$obj = $db->fetch_object($resql);
	if ($obj)
	{
		$var = !$var;
		// Show here line of result
		print '<tr '.$bc[$var].'>';
		if ($action == 'edit' && $idr== $obj->rowid)
		{
			// LIST_OF_TD_FIELDS_LIST
			$lAddbutton=true;
			foreach ($arrayfields as $key => $value) {
				if (!empty($arrayfields[$key]['checked'])) {
				//$key2 = str_replace('t.', '', $key);
					$aKey = explode('.',$key);
					$key2 = $aKey[1];
					if ($key2 == 'active')
					{
						$img = 'switch_off';
						if ($obj->$key2) $img = 'switch_on';
						$obj->$key2 = img_picto('',$img);
					}
					if ($key2 == 'status')
					{
						$objIncidentsdet->status = $obj->$key2;
						$obj->$key2 = $objIncidentsdet->getLibStatut(3);
					}
					if ($key2 == 'fk_user_create' || $key2 == 'fk_user_mod')
					{
						$res = $objUser->fetch($obj->$key2);
						if ($res == 1)
							$obj->$key2 = $objUser->getNomUrl(1);
					}
					if ($key2=='type')
						print '<input type="hidden" class="flat" name="'.$key2.'" value="'. ($typetwo?$typetwo:$type) . '">';
					elseif ($key2=='sequen')
					{
						$max = $obj->sequen;
						print '<td '.$arrayfields[$key]['align'].'>' .'<input type="text" class="flat" name="'.$key2.'" value="'. $max . '" readonly></td>';
					}
					elseif ($key2=='label')
					{
						if ($type=='antiquity')
						{
							print '<td>';
							if ($aArray[$max])
							{
								print '<input type="text" name="label" value="'.$aArray[$max].'" readonly>';

							}
							else
							{
								$lAddbutton=false;
								$lOne=false;
							}
							print '</td>';
						}
						elseif ($type=='costmo')
						{
							print '<td>';
							print $obj->label;
							print '<input type="hidden" name="label" value="'.$obj->label.'">';
							print '</td>';
						}
						else
							print '<td>' .'<input type="text" class="flat" name="'.$key2.'" value="'. $obj->$key2 . '" autofocus></td>';
					}
					elseif($key2=='fk_unit')
					{
						if ($lUnit)
						{
							print '<td>';
							print $form->selectUnits(GETPOST('fk_unit'),'fk_unit',1);
							print '</td>';
						}
					}
					elseif( $key2=='value_one'|| $key2=='value_two' || $key2=='value_three' || $key2=='value_four' || $key2=='value_five' || $key2=='value_six' || $key2=='value_seven' || $key2=='quantity')
					{
						if ($key2=='value_one'&&$lOne) print '<td '.$arrayfields[$key]['align'].'>' .'<input type="number" class="flat len80" step="any" name="'.$key2.'" value="'. $obj->$key2 . '"></td>';
						elseif ($key2=='value_two'&&$lTwo) print '<td '.$arrayfields[$key]['align'].'>' .'<input type="number" class="flat len80" step="any" name="'.$key2.'" value="'. $obj->$key2 . '"></td>';
						elseif ($key2=='value_three'&&$lThree) print '<td '.$arrayfields[$key]['align'].'>' .'<input type="number" class="flat len80" step="any" name="'.$key2.'" value="'. $obj->$key2 . '"></td>';
						elseif ($key2=='value_four'&&$lFour) print '<td '.$arrayfields[$key]['align'].'>' .'<input type="number" class="flat len80" step="any" name="'.$key2.'" value="'. $obj->$key2 . '"></td>';
						elseif ($key2=='value_five'&&$lFive) print '<td '.$arrayfields[$key]['align'].'>' .'<input type="number" class="flat len80" step="any" name="'.$key2.'" value="'. $obj->$key2 . '"></td>';
						elseif ($key2=='value_six'&&$lSix) print '<td '.$arrayfields[$key]['align'].'>' .'<input type="number" class="flat len80" step="any" name="'.$key2.'" value="'. $obj->$key2 . '"></td>';
						elseif ($key2=='value_seven'&&$lSeven) print '<td '.$arrayfields[$key]['align'].'>' .'<input type="number" class="flat len80" step="any" name="'.$key2.'" value="'. $obj->$key2 . '"></td>';
						else
							print '<td></td>';
					}
					if (!$i)
						$totalarray['nbfield'] ++;
				}
			}
			print '<td nowrap>';
			if ($lAddbutton)
				print '<input class="butAction" type="submit" value="'.$langs->trans('Save').'">';
			print '<input class="butAction" type="submit" name="cancel" value="'.$langs->trans('Cancel').'">';
			print '</td>';
			print '</tr>';
		}
		else
		{
			// LIST_OF_TD_FIELDS_LIST
			foreach ($arrayfields as $key => $value) {
				$align='left';
				if (!empty($arrayfields[$key]['checked'])) {
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
					if ($key2 == 'active')
					{
						$img = 'switch_off';
						if ($obj->$key2) $img = 'switch_on';
						$obj->$key2 = img_picto('',$img);
					}
					if ($key2 == 'fk_unit')
					{
						$objTmp = new IncidentsdetLineext($db);
						$objTmp->fk_unit = $obj->$key2;
						$obj->$key2 = $objTmp->getLabelOfUnit('short');
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
					if ($key2=='value_one' || $key2=='value_two' || $key2=='value_three'|| $key2=='value_four'|| $key2=='value_five' || $key2=='value_six' || $key2=='value_seven')
					{
						$align="right";
						if ($type=='subsidies' && empty($typetwo) && $key2=='value_three')
						{
						//$obj->$key2 = $obj->value_two/30;
						}
						elseif ($type=='subsidies' && $typetwo && $key2=='value_three')
						{
							$obj->$key2 = $obj->value_two/30;
						}
						if ($type=='subsidies' && empty($typetwo) && $key2=='value_four')
						{
							$obj->$key2 = $obj->value_one*$obj->value_two*$obj->value_three / 100;
						}
						elseif ($type=='subsidies' && $typetwo && $key2=='value_four')
						{
						//calculo ponderado
							$obj->$key2 = $obj->value_one*$obj->value_two / 100;
							if($object->status==0 && $lUpdate)
							{
								//vamos a actualizar los valores de la ponderación
								$objIncidentsdettmp->fetch($obj->rowid);
								$objIncidentsdettmp->res_four=$obj->$key2;
								$restmp = $objIncidentsdettmp->update($user);
							}
						}
						elseif($type=='minortools' && $key2=='value_four')
						{
							if ($obj->value_two>0)
								$obj->$key2 = $obj->value_three * $obj->value_one / $obj->value_two;
						//vamos a actualziar
							if ($object->status==0 && $lUpdate)
							{
							//vamos a actualizar los valores
								$objIncidentsdettmp->fetch($obj->rowid);
								$objIncidentsdettmp->res_four=$obj->$key2;
								$restmp = $objIncidentsdettmp->update($user);
							}
						}
						elseif($type=='copiesplane' && $key2=='value_three')
						{
							if ($object->cost_direct>0)
								$obj->$key2 = $obj->value_one * $obj->value_two / $object->cost_direct*100;
							//vamos a actualziar
							if ($object->status==0 && $lUpdate)
							{
								//vamos a actualizar los valores
								$objIncidentsdettmp->fetch($obj->rowid);
								$objIncidentsdettmp->res_three=$obj->$key2;
								$restmp = $objIncidentsdettmp->update($user);
							}
						}
						elseif($type=='propossal'|| $type=='legaldoc')
						{
							if ($key2 == 'value_three')
							{
								$obj->$key2 = $obj->value_one * $obj->value_two + 0;
								//vamos a actualziar
								if ($object->status==0 && $lUpdate)
								{
									//vamos a actualizar los valores
									$objIncidentsdettmp->fetch($obj->rowid);
									$objIncidentsdettmp->res_three=$obj->$key2;
									$restmp = $objIncidentsdettmp->update($user);
								}
							}
							if($key2=='value_five')
							{
								$obj->$key2 = ($obj->value_one * $obj->value_two * $obj->value_four)/$object->cost_direct*100;
								//vamos a actualziar
								if ($object->status==0 && $lUpdate)
								{
									//vamos a actualizar los valores
									$objIncidentsdettmp->fetch($obj->rowid);
									$objIncidentsdettmp->res_five=$obj->$key2;
									$restmp = $objIncidentsdettmp->update($user);
								}
							}
						}
						elseif($type=='guarantees')
						{
							if ($key2=='value_two')
							{
								$obj->$key2 = $obj->value_one * $object->cost_direct;
							//vamos a actualziar
								if ($object->status==0 && $lUpdate)
								{
								//vamos a actualizar los valores
									$objIncidentsdettmp->fetch($obj->rowid);
									$objIncidentsdettmp->res_two=$obj->$key2;
									$restmp = $objIncidentsdettmp->update($user);
								}
							}
							if ($key2=='value_four')
							{
								$obj->$key2 = $obj->value_one * $object->cost_direct * $obj->value_three * $object->commission;
							//vamos a actualziar
								if ($object->status==0 && $lUpdate)
								{
								//vamos a actualizar los valores
									$objIncidentsdettmp->fetch($obj->rowid);
									$objIncidentsdettmp->res_four=$obj->$key2;
									$restmp = $objIncidentsdettmp->update($user);
								}
							}
							if ($key2=='value_five')
							{
								$obj->$key2 = price2num(($obj->value_one * $object->cost_direct * $obj->value_three * $object->commission)/$object->cost_direct*100,5);
							//vamos a actualziar
								if ($object->status==0 && $lUpdate)
								{
								//vamos a actualizar los valores
									$objIncidentsdettmp->fetch($obj->rowid);
									$objIncidentsdettmp->res_five=$obj->$key2;
									$restmp = $objIncidentsdettmp->update($user);
								}
							}
						}
						elseif($type=='operation' || $type=='administrative')
						{
							if ($key2=='value_three')
							{
								$obj->$key2 = $obj->value_one * $obj->value_two;
							//vamos a actualziar
								if ($object->status==0 && $lUpdate)
								{
								//vamos a actualizar los valores
									$objIncidentsdettmp->fetch($obj->rowid);
									$objIncidentsdettmp->res_three=$obj->$key2;
									$restmp = $objIncidentsdettmp->update($user);
								}
							}
							if ($key2=='value_four')
							{
								$obj->$key2 = $obj->value_one * $obj->value_two*$object->time_duration;
							//vamos a actualziar
								if ($object->status==0 && $lUpdate)
								{
								//vamos a actualizar los valores
									$objIncidentsdettmp->fetch($obj->rowid);
									$objIncidentsdettmp->res_four=$obj->$key2;
									$restmp = $objIncidentsdettmp->update($user);
								}
							}
							if ($key2=='value_five')
							{
								$obj->$key2 = $obj->value_one * $obj->value_two*$object->time_duration/$object->cost_direct*100;
							//vamos a actualziar
								if ($object->status==0 && $lUpdate)
								{
								//vamos a actualizar los valores
									$objIncidentsdettmp->fetch($obj->rowid);
									$objIncidentsdettmp->res_five=$obj->$key2;
									$restmp = $objIncidentsdettmp->update($user);
								}
							}
						}
					//elseif($type=="mobilization")
						elseif($type=="mobilization" || $type=='traffic' || $type=='risk')
						{
							if ($key2=='value_two')
							{
								$obj->$key2 = $obj->value_one * 12;
							//vamos a actualziar
								if ($object->status==0 && $lUpdate)
								{
								//vamos a actualizar los valores
									$objIncidentsdettmp->fetch($obj->rowid);
									$objIncidentsdettmp->res_two=$obj->$key2;
									$restmp = $objIncidentsdettmp->update($user);
								}
							}
							if ($key2=='value_three')
							{
								$obj->$key2 = $obj->value_one * 12 * $object->time_duration;
							//vamos a actualziar
								if ($object->status==0 && $lUpdate)
								{
								//vamos a actualizar los valores
									$objIncidentsdettmp->fetch($obj->rowid);
									$objIncidentsdettmp->res_three=$obj->$key2;
									$restmp = $objIncidentsdettmp->update($user);
								}
							}
							if ($key2=='value_four')
							{
								$obj->$key2 = $obj->value_one * 12 * $object->time_duration / $object->cost_direct * 100;
							//vamos a actualziar
								if ($object->status==0 && $lUpdate)
								{
								//vamos a actualizar los valores
									$objIncidentsdettmp->fetch($obj->rowid);
									$objIncidentsdettmp->res_four=$obj->$key2;
									$restmp = $objIncidentsdettmp->update($user);
								}
							}
						}
						elseif($type=="faenas")
						{
							if ($key2=='value_three')
							{
								$obj->$key2 = $obj->value_one * $obj->value_two;
							//vamos a actualziar
								if ($object->status==0 && $lUpdate)
								{
								//vamos a actualizar los valores
									$objIncidentsdettmp->fetch($obj->rowid);
									$objIncidentsdettmp->res_three=$obj->$key2;
									$restmp = $objIncidentsdettmp->update($user);
								}
							}
							if ($key2=='value_four')
							{
								$obj->$key2 = $obj->value_one * $obj->value_two / $object->cost_direct * 100;
							//vamos a actualziar
								if ($object->status==0 && $lUpdate)
								{
								//vamos a actualizar los valores
									$objIncidentsdettmp->fetch($obj->rowid);
									$objIncidentsdettmp->res_four=$obj->$key2;
									$restmp = $objIncidentsdettmp->update($user);
								}
							}

						}
						elseif($type=="costmo")
						{
							if ($key2=='value_one')
							{
								$obj->$key2 = $aPoliticsalarymonth[$obj->label];
								//vamos a actualziar
								if ($object->status==0 && $lUpdate)
								{
									//vamos a actualizar los valores
									$objIncidentsdettmp->fetch($obj->rowid);
									$objIncidentsdettmp->res_three=$obj->$key2;
									$restmp = $objIncidentsdettmp->update($user);
								}
							}
							if ($key2=='value_two')
							{
								$obj->$key2 = $aPoliticsalarymonth[$obj->label]*$nBenesoc/100;
								//vamos a actualziar
								if ($object->status==0 && $lUpdate)
								{
								//vamos a actualizar los valores
									$objIncidentsdettmp->fetch($obj->rowid);
									$objIncidentsdettmp->res_two=$obj->$key2;
									$restmp = $objIncidentsdettmp->update($user);
								}
							}
							if ($key2=='value_three')
							{
								$obj->$key2 = $aOccupational[$obj->label];
								$obj->$key2 = $nOccupational;
								//vamos a actualziar
								if ($object->status==0 && $lUpdate)
								{
									//vamos a actualizar los valores
									$objIncidentsdettmp->fetch($obj->rowid);
									$objIncidentsdettmp->res_three=$obj->$key2;
									$restmp = $objIncidentsdettmp->update($user);
								}
							}
							if ($key2=='value_four')
							{
								$obj->$key2 = ($aPoliticsalarymonth[$obj->label]+($aPoliticsalarymonth[$obj->label]*$nBenesoc/100)+$nOccupational)*(13/(100-13));
								//vamos a actualziar
								if ($object->status==0 && $lUpdate)
								{
									//vamos a actualizar los valores
									$objIncidentsdettmp->fetch($obj->rowid);
									$objIncidentsdettmp->res_four=$obj->$key2;
									$restmp = $objIncidentsdettmp->update($user);
								}
							}
							if ($key2=='value_five')
							{
								$obj->$key2 = ($aPoliticsalarymonth[$obj->label]+($aPoliticsalarymonth[$obj->label]*$nBenesoc/100)+$nOccupational)+($aPoliticsalarymonth[$obj->label]+($aPoliticsalarymonth[$obj->label]*$nBenesoc/100)+$nOccupational)*(13/(100-13));
								//vamos a actualziar
								if ($object->status==0 && $lUpdate)
								{
									//vamos a actualizar los valores
									$objIncidentsdettmp->fetch($obj->rowid);
									$objIncidentsdettmp->res_five=$obj->$key2;
									$restmp = $objIncidentsdettmp->update($user);
								}
							}
							if ($key2=='value_six')
							{
								$obj->$key2 = (($aPoliticsalarymonth[$obj->label]+($aPoliticsalarymonth[$obj->label]*$nBenesoc/100)+$nOccupational)+($aPoliticsalarymonth[$obj->label]+($aPoliticsalarymonth[$obj->label]*$nBenesoc/100)+$nOccupational)*(13/(100-13)))/(25*8);
								//vamos a actualziar
								if ($object->status==0 && $lUpdate)
								{
									//vamos a actualizar los valores
									$objIncidentsdettmp->fetch($obj->rowid);
									$objIncidentsdettmp->res_six=$obj->$key2;
									$restmp = $objIncidentsdettmp->update($user);
								}
							}
							if ($key2=='value_seven')
							{
								if ($object->exchange_rate>0)
									$obj->$key2 = ((($aPoliticsalarymonth[$obj->label]+($aPoliticsalarymonth[$obj->label]*$nBenesoc/100)+$nOccupational)+($aPoliticsalarymonth[$obj->label]+($aPoliticsalarymonth[$obj->label]*$nBenesoc/100)+$nOccupational)*(13/(100-13)))/(25*8))/$object->exchange_rate;
								//vamos a actualziar
								if ($object->status==0 && $lUpdate)
								{
									//vamos a actualizar los valores
									$objIncidentsdettmp->fetch($obj->rowid);
									$objIncidentsdettmp->res_seven=$obj->$key2;
									$restmp = $objIncidentsdettmp->update($user);
								}
							}

						}

						if ($type=='contribution'&& $key2=='value_three')
							$obj->$key2 = $obj->value_one + $obj->value_two;
						if ($type=='occupational'&& $key2=='value_two' && empty($typetwo))
						{
							$obj->$key2 = $obj->value_one * 25;
							if ($object->status==0 && $lUpdate)
							{
							//vamos a actualizar los valores
								$objIncidentsdettmp->fetch($obj->rowid);
								$objIncidentsdettmp->res_two=$obj->$key2;
								$restmp = $objIncidentsdettmp->update($user);
							}
						}
						if ($type=='occupational'&& $key2=='value_three' && $typetwo=='security')
							$obj->$key2 = $obj->value_one * $obj->value_two;
						if ($type=='occupational'&& $key2=='value_four' && $typetwo=='security')
							$obj->$key2 = $obj->value_one * $obj->value_two / 12;

					//$obj->$key2 = price2num($obj->$key2,'MT');
						$aTotal[$key2]+= $obj->$key2;
					/////////////////////////
					}
					if ($key2=='quantity')
					{
						if ($lQuant && $type=='subsidies' && !empty($typetwo))
						{
							$align='right';
							$obj->$key2 = $object->njobs*$obj->value_one/100;
							$obj->$key2 = price2num($obj->$key2,'MT');
							$aTotal[$key2]+=$obj->$key2;
						}
					}
					if ($type=='antiquity' && $key2=='sequen')
					{
					//solo para total antiguedad
						$aTotalant[$obj->$key2]=$obj->value_one;
					}

					if ($key2=='value_one' || $key2=='value_two' || $key2=='value_three'|| $key2=='value_four'|| $key2=='value_five'|| $key2=='value_six'|| $key2=='value_seven')
						print '<td align="'.$align.'">' . number_format(price2num($obj->$key2,5),4) . '</td>';
					else
						print '<td align="'.$align.'">' . $obj->$key2 . '</td>';
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
	}
		// Action column
	print '<td class="nowrap" align="center">';
	if (empty($action))
	{
		if ($type !='costmo')
			print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$obj->rowid.'&action=edit">'.img_picto('','edit').'</a>';
		print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$obj->rowid.'&type='.$type.'&action=delete">'.img_picto('','delete').'</a>';
	}
	print '</td>';
	if (! $i) $totalarray['nbfield']++;

	print '</tr>';
}
$i++;
}

if ($type=='antiquity')
{
	$incident = 0;
	$amountanual= $aTotalant[1]/100*$aTotalant[2]*$aTotalant[3]*$aTotalant[4]*12/100;
	if ($object->ponderation>0) $incident=$amountanual / $object->ponderation;
	$obj = new Stdclass();
			// Show here line of result
	$var = !$var;
	print '<tr '.$bc[$var].'>';
		// LIST_OF_TD_FIELDS_LIST
	foreach ($arrayfields as $key => $value)
	{
		$align='left';
		if (!empty($arrayfields[$key]['checked'])) {
				//$key2 = str_replace('t.', '', $key);
			$aKey = explode('.',$key);
			$key2 = $aKey[1];

			if ($key2== 'sequen') $obj->sequen=5;
			if ($key2== 'label') $obj->label=$aArrayadd[5];
			if ($key2== 'value_one') $obj->value_one=price2num($amountanual,'MT');
			print '<td '.$arrayfields[$key]['align'].'>' . $obj->$key2 . '</td>';
			if (!$i)
				$totalarray['nbfield'] ++;

		}
	}
	print '<td class="nowrap" align="center">';
	print '</td>';
	print '</tr>';
}


if (is_array($aTotal) && count($aTotal)>0)
{
	print '<tr class="liste_total">';
	$i=0;

	foreach ($arrayfields as $key => $value) {
		$align='left';
		if (!empty($arrayfields[$key]['checked']))
		{
			$aKey = explode('.',$key);
			$key2 = $aKey[1];
			if ($aTotal[$key2])
			{
				print '<td align="right">'.price2num($aTotal[$key2],3).'</td>';
			}
			else
			{
				print '<td></td>';
			}
		}

	}
	print '</tr>';
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
print '</div>'."\n";

print '</form>'."\n";


print '<div class="tabsAction">';
if ($user->rights->budget->tea->crear)
{
	if (empty($action))
		print '<a class="butAction" href="' . dol_buildpath('/budget/incidents/'.$type.'.php',1).'?id='.$object->id.'&action='.($typetwo?'createtwo':'create').''.($typetwo?'&subaction='.$typetwo:'').'">' . $langs->trans('New') . '</a>';
}
print '</div>';