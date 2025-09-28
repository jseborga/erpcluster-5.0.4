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
 *   	\file       salary/puser_list.php
 *		\ingroup    salary
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-01-09 10:18
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
dol_include_once('/salary/class/puser.class.php');

// Load traductions files requiredby by page
$langs->load("salary");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_fk_user=GETPOST('search_fk_user','int');
$search_firstname=GETPOST('search_firstname','alpha');
$search_lastname=GETPOST('search_lastname','alpha');
$search_lastnametwo=GETPOST('search_lastnametwo','alpha');
$search_state_marital=GETPOST('search_state_marital','alpha');
$search_docum=GETPOST('search_docum','alpha');
$search_issued_in=GETPOST('search_issued_in','alpha');
$search_sex=GETPOST('search_sex','alpha');
$search_phone_emergency=GETPOST('search_phone_emergency','alpha');
$search_blood_type=GETPOST('search_blood_type','alpha');
$search_dependents=GETPOST('search_dependents','int');


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

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('puserlist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('salary');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Load object if id or ref is provided as parameter
$object=new Puser($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Definition of fields for list
$arrayfields=array(
    
't.fk_user'=>array('label'=>$langs->trans("Fieldfk_user"), 'checked'=>1),
't.firstname'=>array('label'=>$langs->trans("Fieldfirstname"), 'checked'=>1),
't.lastname'=>array('label'=>$langs->trans("Fieldlastname"), 'checked'=>1),
't.lastnametwo'=>array('label'=>$langs->trans("Fieldlastnametwo"), 'checked'=>1),
't.state_marital'=>array('label'=>$langs->trans("Fieldstate_marital"), 'checked'=>1),
't.docum'=>array('label'=>$langs->trans("Fielddocum"), 'checked'=>1),
't.issued_in'=>array('label'=>$langs->trans("Fieldissued_in"), 'checked'=>1),
't.sex'=>array('label'=>$langs->trans("Fieldsex"), 'checked'=>1),
't.phone_emergency'=>array('label'=>$langs->trans("Fieldphone_emergency"), 'checked'=>1),
't.blood_type'=>array('label'=>$langs->trans("Fieldblood_type"), 'checked'=>1),
't.dependents'=>array('label'=>$langs->trans("Fielddependents"), 'checked'=>1),

    
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
	
$search_fk_user='';
$search_firstname='';
$search_lastname='';
$search_lastnametwo='';
$search_state_marital='';
$search_docum='';
$search_issued_in='';
$search_sex='';
$search_phone_emergency='';
$search_blood_type='';
$search_dependents='';

	
	$search_date_creation='';
	$search_date_update='';
	$search_array_options=array();
}


if (empty($reshook))
{
    // Mass actions. Controls on number of lines checked
    $maxformassaction=1000;
    if (! empty($massaction) && count($toselect) < 1)
    {
        $error++;
        setEventMessages($langs->trans("NoLineChecked"), null, "warnings");
    }
    if (! $error && count($toselect) > $maxformassaction)
    {
        setEventMessages($langs->trans('TooManyRecordForMassAction',$maxformassaction), null, 'errors');
        $error++;
    }
    
	// Action to delete
	if ($action == 'confirm_delete')
	{
		$result=$object->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/salary/list.php',1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null,$object->errors,'errors');
			else setEventMessages($object->error,null,'errors');
		}
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
$title = $langs->trans('MyModuleListTitle');
llxHeader('', $title, $help_url);

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

		$sql .= " t.fk_user,";
		$sql .= " t.firstname,";
		$sql .= " t.lastname,";
		$sql .= " t.lastnametwo,";
		$sql .= " t.state_marital,";
		$sql .= " t.docum,";
		$sql .= " t.issued_in,";
		$sql .= " t.sex,";
		$sql .= " t.phone_emergency,";
		$sql .= " t.blood_type,";
		$sql .= " t.dependents";


// Add fields for extrafields
foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."p_user as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."p_user_extrafields as ef on (u.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_fk_user) $sql.= natural_search("fk_user",$search_fk_user);
if ($search_firstname) $sql.= natural_search("firstname",$search_firstname);
if ($search_lastname) $sql.= natural_search("lastname",$search_lastname);
if ($search_lastnametwo) $sql.= natural_search("lastnametwo",$search_lastnametwo);
if ($search_state_marital) $sql.= natural_search("state_marital",$search_state_marital);
if ($search_docum) $sql.= natural_search("docum",$search_docum);
if ($search_issued_in) $sql.= natural_search("issued_in",$search_issued_in);
if ($search_sex) $sql.= natural_search("sex",$search_sex);
if ($search_phone_emergency) $sql.= natural_search("phone_emergency",$search_phone_emergency);
if ($search_blood_type) $sql.= natural_search("blood_type",$search_blood_type);
if ($search_dependents) $sql.= natural_search("dependents",$search_dependents);


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
    if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;
    
if ($search_fk_user != '') $params.= '&amp;search_fk_user='.urlencode($search_fk_user);
if ($search_firstname != '') $params.= '&amp;search_firstname='.urlencode($search_firstname);
if ($search_lastname != '') $params.= '&amp;search_lastname='.urlencode($search_lastname);
if ($search_lastnametwo != '') $params.= '&amp;search_lastnametwo='.urlencode($search_lastnametwo);
if ($search_state_marital != '') $params.= '&amp;search_state_marital='.urlencode($search_state_marital);
if ($search_docum != '') $params.= '&amp;search_docum='.urlencode($search_docum);
if ($search_issued_in != '') $params.= '&amp;search_issued_in='.urlencode($search_issued_in);
if ($search_sex != '') $params.= '&amp;search_sex='.urlencode($search_sex);
if ($search_phone_emergency != '') $params.= '&amp;search_phone_emergency='.urlencode($search_phone_emergency);
if ($search_blood_type != '') $params.= '&amp;search_blood_type='.urlencode($search_blood_type);
if ($search_dependents != '') $params.= '&amp;search_dependents='.urlencode($search_dependents);

	
    if ($optioncss != '') $param.='&optioncss='.$optioncss;
    // Add $param from extra fields
    foreach ($search_array_options as $key => $val)
    {
        $crit=$val;
        $tmpkey=preg_replace('/search_options_/','',$key);
        if ($val != '') $param.='&search_options_'.$tmpkey.'='.urlencode($val);
    } 

    print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $params, $sortfield, $sortorder, '', $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);


	print '<form method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
    if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
    print '<input type="hidden" name="action" value="list">';
	print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
	print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
	
    if ($sall)
    {
        foreach($fieldstosearchall as $key => $val) $fieldstosearchall[$key]=$langs->trans($val);
        print $langs->trans("FilterOnInto", $all) . join(', ',$fieldstosearchall);
    }
    
    $moreforfilter = '';
    $moreforfilter.='<div class="divsearchfield">';
    $moreforfilter.= $langs->trans('MyFilter') . ': <input type="text" name="search_myfield" value="'.dol_escape_htmltag($search_myfield).'">';
    $moreforfilter.= '</div>';
    
	if (! empty($moreforfilter))
	{
		print '<div class="liste_titre liste_titre_bydiv centpercent">';
		print $moreforfilter;
    	$parameters=array();
    	$reshook=$hookmanager->executeHooks('printFieldPreListTitle',$parameters);    // Note that $action and $object may have been modified by hook
	    print $hookmanager->resPrint;
	    print '</div>';
	}

    $varpage=empty($contextpage)?$_SERVER["PHP_SELF"]:$contextpage;
    $selectedfields=$form->multiSelectArrayWithCheckbox('selectedfields', $arrayfields, $varpage);	// This also change content of $arrayfields
	
	print '<table class="liste '.($moreforfilter?"listwithfilterbefore":"").'">';

    // Fields title
    print '<tr class="liste_titre">';
    // 
if (! empty($arrayfields['t.fk_user']['checked'])) print_liste_field_titre($arrayfields['t.fk_user']['label'],$_SERVER['PHP_SELF'],'t.fk_user','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.firstname']['checked'])) print_liste_field_titre($arrayfields['t.firstname']['label'],$_SERVER['PHP_SELF'],'t.firstname','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.lastname']['checked'])) print_liste_field_titre($arrayfields['t.lastname']['label'],$_SERVER['PHP_SELF'],'t.lastname','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.lastnametwo']['checked'])) print_liste_field_titre($arrayfields['t.lastnametwo']['label'],$_SERVER['PHP_SELF'],'t.lastnametwo','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.state_marital']['checked'])) print_liste_field_titre($arrayfields['t.state_marital']['label'],$_SERVER['PHP_SELF'],'t.state_marital','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.docum']['checked'])) print_liste_field_titre($arrayfields['t.docum']['label'],$_SERVER['PHP_SELF'],'t.docum','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.issued_in']['checked'])) print_liste_field_titre($arrayfields['t.issued_in']['label'],$_SERVER['PHP_SELF'],'t.issued_in','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.sex']['checked'])) print_liste_field_titre($arrayfields['t.sex']['label'],$_SERVER['PHP_SELF'],'t.sex','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.phone_emergency']['checked'])) print_liste_field_titre($arrayfields['t.phone_emergency']['label'],$_SERVER['PHP_SELF'],'t.phone_emergency','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.blood_type']['checked'])) print_liste_field_titre($arrayfields['t.blood_type']['label'],$_SERVER['PHP_SELF'],'t.blood_type','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.dependents']['checked'])) print_liste_field_titre($arrayfields['t.dependents']['label'],$_SERVER['PHP_SELF'],'t.dependents','',$params,'',$sortfield,$sortorder);

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
if (! empty($arrayfields['t.fk_user']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user" value="'.$search_fk_user.'" size="10"></td>';
if (! empty($arrayfields['t.firstname']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_firstname" value="'.$search_firstname.'" size="10"></td>';
if (! empty($arrayfields['t.lastname']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_lastname" value="'.$search_lastname.'" size="10"></td>';
if (! empty($arrayfields['t.lastnametwo']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_lastnametwo" value="'.$search_lastnametwo.'" size="10"></td>';
if (! empty($arrayfields['t.state_marital']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_state_marital" value="'.$search_state_marital.'" size="10"></td>';
if (! empty($arrayfields['t.docum']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_docum" value="'.$search_docum.'" size="10"></td>';
if (! empty($arrayfields['t.issued_in']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_issued_in" value="'.$search_issued_in.'" size="10"></td>';
if (! empty($arrayfields['t.sex']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_sex" value="'.$search_sex.'" size="10"></td>';
if (! empty($arrayfields['t.phone_emergency']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_phone_emergency" value="'.$search_phone_emergency.'" size="10"></td>';
if (! empty($arrayfields['t.blood_type']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_blood_type" value="'.$search_blood_type.'" size="10"></td>';
if (! empty($arrayfields['t.dependents']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_dependents" value="'.$search_dependents.'" size="10"></td>';

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
    $searchpitco=$form->showFilterAndCheckAddButtons(0);
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
            print '<td></td>';
            if (! $i) $totalarray['nbfield']++;

            print '</tr>';
        }
        $i++;
    }
    
    $db->free($resql);

	$parameters=array('sql' => $sql);
	$reshook=$hookmanager->executeHooks('printFieldListFooter',$parameters);    // Note that $action and $object may have been modified by hook
	print $hookmanager->resPrint;

	print "</table>\n";
	print "</form>\n";
	
	$db->free($result);
}
else
{
    $error++;
    dol_print_error($db);
}


// End of page
llxFooter();
$db->close();
