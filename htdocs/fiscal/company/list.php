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
 *   	\file       fiscal/entity_list.php
 *		\ingroup    fiscal
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-11-17 15:53
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
dol_include_once('/fiscal/class/entity.class.php');
dol_include_once('/fiscal/class/entityadd.class.php');
dol_include_once('/user/class/user.class.php');

// Load traductions files requiredby by page
$langs->load("fiscal");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_label=GETPOST('search_label','alpha');
$search_description=GETPOST('search_description','alpha');
$search_fk_user_creat=GETPOST('search_fk_user_creat','int');
$search_options=GETPOST('search_options','alpha');
$search_visible=GETPOST('search_visible','int');
$search_active=GETPOST('search_active','int');


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
$hookmanager->initHooks(array('entitylist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('fiscal');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Load object if id or ref is provided as parameter
$object=new Entity($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}
$objectadd = new Entityadd($db);

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	//verificamos la existencia o no de la empresa principal
	$objecttmp = new Entity($db);
	$res = $objecttmp->fetch(1);
	if (empty($res))
	{
		//creamos un registro de la empresa principal
		$objecttmp->id = 1;
		$objecttmp->label=$conf->global->MAIN_INFO_SOCIETE_NOM;
		$objecttmp->description=$conf->global->MAIN_INFO_SOCIETE_NOTE;
		$objecttmp->fk_user_creat=$user->id;
		//$objecttmp->options=GETPOST('options','alpha');
		$objecttmp->visible=1;
		$objecttmp->active=1;

		$objectadd->socialreason=$conf->global->MAIN_INFO_SOCIETE_NOM;
		$objectadd->nit=$conf->global->MAIN_INFO_TVAINTRA;
		$objectadd->activity=$conf->global->MAIN_INFO_SOCIETE_OBJECT;
		$objectadd->address=$conf->global->MAIN_INFO_SOCIETE_ADDRESS;
		//$objectadd->city=GETPOST('city','alpha');
		$objectadd->phone=$conf->global->MAIN_INFO_SOCIETE_TEL;
		//$objectadd->message=GETPOST('message','alpha');
		$objectadd->status=1;


		if (empty($objecttmp->label))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Label")), null, 'errors');
		}

		if (! $error)
		{
			$db->begin();
			$result=$objecttmp->create($user);
			if ($result > 0)
			{
				//creamos en add
				$objectadd->fk_entity = $result;
				$res = $objectadd->create($user);
				if ($res > 0)
				{
					$db->commit();
					setEventMessages($langs->trans('Saverecord'),null,'mesgs');
					// Creation OK
					$urltogo=$backtopage?$backtopage:dol_buildpath('/fiscal/company/list.php',1);
					header("Location: ".$urltogo);
					exit;
				}
				else
					setEventMessages($objectadd->error,$objectadd->errors,'errors');
			}
			else
			{
				// Creation KO
				if (! empty($objecttmp->errors)) setEventMessages(null, $objecttmp->errors, 'errors');
				else  setEventMessages($objecttmp->error, null, 'errors');
			}
			$db->rollback();
		}
	}
}

// Definition of fields for list
$arrayfields=array(

	't.label'=>array('label'=>$langs->trans("Fieldlabel"), 'checked'=>1),
	't.description'=>array('label'=>$langs->trans("Fielddescription"), 'checked'=>1),
//'t.options'=>array('label'=>$langs->trans("Fieldoptions"), 'checked'=>1),
	'a.socialreason'=>array('label'=>$langs->trans("Fieldsocialreason"), 'checked'=>1),
	'a.nit'=>array('label'=>$langs->trans("Fieldnit"), 'checked'=>1),
	'a.activity'=>array('label'=>$langs->trans("Fieldactivity"), 'checked'=>1),
	'a.address'=>array('label'=>$langs->trans("Fieldaddress"), 'checked'=>1),
	'a.city'=>array('label'=>$langs->trans("Fieldcity"), 'checked'=>1),
	'a.phone'=>array('label'=>$langs->trans("Fieldphone"), 'checked'=>1),
	'a.message'=>array('label'=>$langs->trans("Fieldmessage"), 'checked'=>1),
	't.fk_user_creat'=>array('label'=>$langs->trans("Fieldfk_user_creat"), 'checked'=>1),
	't.visible'=>array('label'=>$langs->trans("Fieldvisible"), 'checked'=>1),
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
if (! GETPOST('confirmmassaction')) { $massaction=''; }

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter.x") ||GETPOST("button_removefilter")) // All test are required to be compatible with all browsers
{

	$search_label='';
	$search_description='';
	$search_fk_user_creat='';
	$search_options='';
	$search_visible='';
	$search_active='';


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
			header("Location: ".dol_buildpath('/fiscal/list.php',1));
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
$title = $langs->trans('Companies');
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

$sql .= " t.tms,";
$sql .= " t.label,";
$sql .= " t.description,";
$sql .= " t.datec,";
$sql .= " t.fk_user_creat,";
$sql .= " t.options,";
$sql .= " t.visible,";
$sql .= " t.active,";

$sql .= " a.socialreason,";
$sql .= " a.nit,";
$sql .= " a.activity,";
$sql .= " a.address,";
$sql .= " a.city,";
$sql .= " a.phone,";
$sql .= " a.message";

// Add fields for extrafields
foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."entity as t";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."entity_add as a ON a.fk_entity = t.rowid";

if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."entity_extrafields as ef on (u.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_label) $sql.= natural_search("label",$search_label);
if ($search_description) $sql.= natural_search("description",$search_description);
if ($search_fk_user_creat) $sql.= natural_search("fk_user_creat",$search_fk_user_creat);
if ($search_options) $sql.= natural_search("options",$search_options);
if ($search_visible) $sql.= natural_search("visible",$search_visible);
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

	if ($search_label != '') $params.= '&amp;search_label='.urlencode($search_label);
	if ($search_description != '') $params.= '&amp;search_description='.urlencode($search_description);
	if ($search_fk_user_creat != '') $params.= '&amp;search_fk_user_creat='.urlencode($search_fk_user_creat);
	if ($search_options != '') $params.= '&amp;search_options='.urlencode($search_options);
	if ($search_visible != '') $params.= '&amp;search_visible='.urlencode($search_visible);
	if ($search_active != '') $params.= '&amp;search_active='.urlencode($search_active);


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
    if (! empty($arrayfields['t.label']['checked'])) print_liste_field_titre($arrayfields['t.label']['label'],$_SERVER['PHP_SELF'],'t.label','',$params,'',$sortfield,$sortorder);
    if (! empty($arrayfields['t.description']['checked'])) print_liste_field_titre($arrayfields['t.description']['label'],$_SERVER['PHP_SELF'],'t.description','',$params,'',$sortfield,$sortorder);
    if (! empty($arrayfields['a.socialreason']['checked'])) print_liste_field_titre($arrayfields['a.socialreason']['label'],$_SERVER['PHP_SELF'],'a.socialreason','',$params,'',$sortfield,$sortorder);
    if (! empty($arrayfields['a.nit']['checked'])) print_liste_field_titre($arrayfields['a.nit']['label'],$_SERVER['PHP_SELF'],'a.nit','',$params,'',$sortfield,$sortorder);
    if (! empty($arrayfields['a.activity']['checked'])) print_liste_field_titre($arrayfields['a.activity']['label'],$_SERVER['PHP_SELF'],'a.activity','',$params,'',$sortfield,$sortorder);
    if (! empty($arrayfields['a.address']['checked'])) print_liste_field_titre($arrayfields['a.address']['label'],$_SERVER['PHP_SELF'],'a.address','',$params,'',$sortfield,$sortorder);
    if (! empty($arrayfields['a.city']['checked'])) print_liste_field_titre($arrayfields['a.city']['label'],$_SERVER['PHP_SELF'],'a.city','',$params,'',$sortfield,$sortorder);
    if (! empty($arrayfields['a.phone']['checked'])) print_liste_field_titre($arrayfields['a.phone']['label'],$_SERVER['PHP_SELF'],'a.phone','',$params,'',$sortfield,$sortorder);
    if (! empty($arrayfields['a.message']['checked'])) print_liste_field_titre($arrayfields['a.message']['label'],$_SERVER['PHP_SELF'],'a.message','',$params,'',$sortfield,$sortorder);

    if (! empty($arrayfields['t.fk_user_creat']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_creat']['label'],$_SERVER['PHP_SELF'],'t.fk_user_creat','',$params,'',$sortfield,$sortorder);
    if (! empty($arrayfields['t.options']['checked'])) print_liste_field_titre($arrayfields['t.options']['label'],$_SERVER['PHP_SELF'],'t.options','',$params,'',$sortfield,$sortorder);
    if (! empty($arrayfields['t.visible']['checked'])) print_liste_field_titre($arrayfields['t.visible']['label'],$_SERVER['PHP_SELF'],'t.visible','',$params,'',$sortfield,$sortorder);
    if (! empty($arrayfields['t.active']['checked'])) print_liste_field_titre($arrayfields['t.active']['label'],$_SERVER['PHP_SELF'],'t.active','',$params,'',$sortfield,$sortorder);

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
    if (! empty($arrayfields['t.label']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_label" value="'.$search_label.'" size="6"></td>';
    if (! empty($arrayfields['t.description']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_description" value="'.$search_description.'" size="6"></td>';
    if (! empty($arrayfields['a.socialreason']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_socialreason" value="'.$search_socialreason.'" size="6"></td>';
    if (! empty($arrayfields['a.nit']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_nit" value="'.$search_nit.'" size="6"></td>';
    if (! empty($arrayfields['a.activity']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_activity" value="'.$search_activity.'" size="6"></td>';
    if (! empty($arrayfields['a.address']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_address" value="'.$search_address.'" size="6"></td>';
    if (! empty($arrayfields['a.city']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_city" value="'.$search_city.'" size="6"></td>';
    if (! empty($arrayfields['a.phone']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_phone" value="'.$search_phone.'" size="5"></td>';
    if (! empty($arrayfields['a.message']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_message" value="'.$search_message.'" size="6"></td>';
    if (! empty($arrayfields['t.fk_user_creat']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_creat" value="'.$search_fk_user_creat.'" size="5"></td>';
    if (! empty($arrayfields['t.options']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_options" value="'.$search_options.'" size="5"></td>';
    if (! empty($arrayfields['t.visible']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_visible" value="'.$search_visible.'" size="4"></td>';
    if (! empty($arrayfields['t.active']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_active" value="'.$search_active.'" size="4"></td>';

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
    $objuser = new User($db);
    while ($i < min($num, $limit))
    {
    	$obj = $db->fetch_object($resql);
    	if ($obj)
    	{
    		$var = !$var;
    		$object->fetch($obj->rowid);
            // Show here line of result
    		print '<tr '.$bc[$var].'>';
            // LIST_OF_TD_FIELDS_LIST

    		if (! empty($arrayfields['t.label']['checked']))
    		{
    			print '<td>'.$object->getNomUrl(1).'</td>';
    			if (! $i) $totalarray['nbfield']++;
    		}
    		if (! empty($arrayfields['t.description']['checked']))
    		{
    			print '<td>'.$obj->description.'</td>';
    			if (! $i) $totalarray['nbfield']++;
    		}
    		if (! empty($arrayfields['a.socialreason']['checked']))
    		{
    			print '<td>'.$obj->socialreason.'</td>';
    			if (! $i) $totalarray['nbfield']++;
    		}
    		if (! empty($arrayfields['a.nit']['checked']))
    		{
    			print '<td>'.$obj->nit.'</td>';
    			if (! $i) $totalarray['nbfield']++;
    		}
    		if (! empty($arrayfields['a.activity']['checked']))
    		{
    			print '<td>'.$obj->activity.'</td>';
    			if (! $i) $totalarray['nbfield']++;
    		}
    		if (! empty($arrayfields['a.address']['checked']))
    		{
    			print '<td>'.$obj->address.'</td>';
    			if (! $i) $totalarray['nbfield']++;
    		}
    		if (! empty($arrayfields['a.city']['checked']))
    		{
    			print '<td>'.$obj->city.'</td>';
    			if (! $i) $totalarray['nbfield']++;
    		}
    		if (! empty($arrayfields['a.phone']['checked']))
    		{
    			print '<td>'.$obj->phone.'</td>';
    			if (! $i) $totalarray['nbfield']++;
    		}
    		if (! empty($arrayfields['a.message']['checked']))
    		{
    			print '<td>'.$obj->message.'</td>';
    			if (! $i) $totalarray['nbfield']++;
    		}
    		if (! empty($arrayfields['t.fk_user_creat']['checked']))
    		{
    			$objuser->fetch($obj->fk_user_creat);
    			print '<td>'.$objuser->getNomUrl(1).'</td>';
    			if (! $i) $totalarray['nbfield']++;
    		}
    		if (! empty($arrayfields['t.visible']['checked']))
    		{
    			print '<td>'.($obj->visible?img_picto('','switch_on'):img_picto('','switch_off')).'</td>';
    			if (! $i) $totalarray['nbfield']++;
    		}
    		if (! empty($arrayfields['t.active']['checked']))
    		{
    			print '<td>'.($obj->active?img_picto('','switch_on'):img_picto('','switch_off')).'</td>';
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
