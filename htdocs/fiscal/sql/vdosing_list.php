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
 *   	\file       fiscal/vdosing_list.php
 *		\ingroup    fiscal
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-12-27 17:44
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
dol_include_once('/fiscal/class/vdosing.class.php');

// Load traductions files requiredby by page
$langs->load("fiscal");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_fk_subsidiaryid=GETPOST('search_fk_subsidiaryid','int');
$search_series=GETPOST('search_series','alpha');
$search_num_ini=GETPOST('search_num_ini','int');
$search_num_fin=GETPOST('search_num_fin','int');
$search_num_ult=GETPOST('search_num_ult','int');
$search_num_aprob=GETPOST('search_num_aprob','alpha');
$search_type=GETPOST('search_type','int');
$search_active=GETPOST('search_active','int');
$search_num_autoriz=GETPOST('search_num_autoriz','alpha');
$search_cod_control=GETPOST('search_cod_control','alpha');
$search_lote=GETPOST('search_lote','int');
$search_chave=GETPOST('search_chave','alpha');
$search_descrip=GETPOST('search_descrip','alpha');
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
$hookmanager->initHooks(array('vdosinglist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('fiscal');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Load object if id or ref is provided as parameter
$object=new Vdosing($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Definition of fields for list
$arrayfields=array(
    
't.entity'=>array('label'=>$langs->trans("Fieldentity"), 'checked'=>1),
't.fk_subsidiaryid'=>array('label'=>$langs->trans("Fieldfk_subsidiaryid"), 'checked'=>1),
't.series'=>array('label'=>$langs->trans("Fieldseries"), 'checked'=>1),
't.num_ini'=>array('label'=>$langs->trans("Fieldnum_ini"), 'checked'=>1),
't.num_fin'=>array('label'=>$langs->trans("Fieldnum_fin"), 'checked'=>1),
't.num_ult'=>array('label'=>$langs->trans("Fieldnum_ult"), 'checked'=>1),
't.num_aprob'=>array('label'=>$langs->trans("Fieldnum_aprob"), 'checked'=>1),
't.type'=>array('label'=>$langs->trans("Fieldtype"), 'checked'=>1),
't.active'=>array('label'=>$langs->trans("Fieldactive"), 'checked'=>1),
't.num_autoriz'=>array('label'=>$langs->trans("Fieldnum_autoriz"), 'checked'=>1),
't.cod_control'=>array('label'=>$langs->trans("Fieldcod_control"), 'checked'=>1),
't.lote'=>array('label'=>$langs->trans("Fieldlote"), 'checked'=>1),
't.chave'=>array('label'=>$langs->trans("Fieldchave"), 'checked'=>1),
't.descrip'=>array('label'=>$langs->trans("Fielddescrip"), 'checked'=>1),
't.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'checked'=>1),
't.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'checked'=>1),
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
if (! GETPOST('confirmmassaction')) { $massaction=''; }

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter.x") ||GETPOST("button_removefilter")) // All test are required to be compatible with all browsers
{
	
$search_entity='';
$search_fk_subsidiaryid='';
$search_series='';
$search_num_ini='';
$search_num_fin='';
$search_num_ult='';
$search_num_aprob='';
$search_type='';
$search_active='';
$search_num_autoriz='';
$search_cod_control='';
$search_lote='';
$search_chave='';
$search_descrip='';
$search_fk_user_create='';
$search_fk_user_mod='';
$search_status='';

	
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

		$sql .= " t.entity,";
		$sql .= " t.fk_subsidiaryid,";
		$sql .= " t.series,";
		$sql .= " t.num_ini,";
		$sql .= " t.num_fin,";
		$sql .= " t.num_ult,";
		$sql .= " t.num_aprob,";
		$sql .= " t.type,";
		$sql .= " t.active,";
		$sql .= " t.date_val,";
		$sql .= " t.num_autoriz,";
		$sql .= " t.cod_control,";
		$sql .= " t.lote,";
		$sql .= " t.chave,";
		$sql .= " t.descrip,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.date_create,";
		$sql .= " t.date_mod,";
		$sql .= " t.tms,";
		$sql .= " t.status";


// Add fields for extrafields
foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."v_dosing as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."v_dosing_extrafields as ef on (u.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_entity) $sql.= natural_search("entity",$search_entity);
if ($search_fk_subsidiaryid) $sql.= natural_search("fk_subsidiaryid",$search_fk_subsidiaryid);
if ($search_series) $sql.= natural_search("series",$search_series);
if ($search_num_ini) $sql.= natural_search("num_ini",$search_num_ini);
if ($search_num_fin) $sql.= natural_search("num_fin",$search_num_fin);
if ($search_num_ult) $sql.= natural_search("num_ult",$search_num_ult);
if ($search_num_aprob) $sql.= natural_search("num_aprob",$search_num_aprob);
if ($search_type) $sql.= natural_search("type",$search_type);
if ($search_active) $sql.= natural_search("active",$search_active);
if ($search_num_autoriz) $sql.= natural_search("num_autoriz",$search_num_autoriz);
if ($search_cod_control) $sql.= natural_search("cod_control",$search_cod_control);
if ($search_lote) $sql.= natural_search("lote",$search_lote);
if ($search_chave) $sql.= natural_search("chave",$search_chave);
if ($search_descrip) $sql.= natural_search("descrip",$search_descrip);
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
    
if ($search_entity != '') $params.= '&amp;search_entity='.urlencode($search_entity);
if ($search_fk_subsidiaryid != '') $params.= '&amp;search_fk_subsidiaryid='.urlencode($search_fk_subsidiaryid);
if ($search_series != '') $params.= '&amp;search_series='.urlencode($search_series);
if ($search_num_ini != '') $params.= '&amp;search_num_ini='.urlencode($search_num_ini);
if ($search_num_fin != '') $params.= '&amp;search_num_fin='.urlencode($search_num_fin);
if ($search_num_ult != '') $params.= '&amp;search_num_ult='.urlencode($search_num_ult);
if ($search_num_aprob != '') $params.= '&amp;search_num_aprob='.urlencode($search_num_aprob);
if ($search_type != '') $params.= '&amp;search_type='.urlencode($search_type);
if ($search_active != '') $params.= '&amp;search_active='.urlencode($search_active);
if ($search_num_autoriz != '') $params.= '&amp;search_num_autoriz='.urlencode($search_num_autoriz);
if ($search_cod_control != '') $params.= '&amp;search_cod_control='.urlencode($search_cod_control);
if ($search_lote != '') $params.= '&amp;search_lote='.urlencode($search_lote);
if ($search_chave != '') $params.= '&amp;search_chave='.urlencode($search_chave);
if ($search_descrip != '') $params.= '&amp;search_descrip='.urlencode($search_descrip);
if ($search_fk_user_create != '') $params.= '&amp;search_fk_user_create='.urlencode($search_fk_user_create);
if ($search_fk_user_mod != '') $params.= '&amp;search_fk_user_mod='.urlencode($search_fk_user_mod);
if ($search_status != '') $params.= '&amp;search_status='.urlencode($search_status);

	
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
if (! empty($arrayfields['t.entity']['checked'])) print_liste_field_titre($arrayfields['t.entity']['label'],$_SERVER['PHP_SELF'],'t.entity','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_subsidiaryid']['checked'])) print_liste_field_titre($arrayfields['t.fk_subsidiaryid']['label'],$_SERVER['PHP_SELF'],'t.fk_subsidiaryid','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.series']['checked'])) print_liste_field_titre($arrayfields['t.series']['label'],$_SERVER['PHP_SELF'],'t.series','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.num_ini']['checked'])) print_liste_field_titre($arrayfields['t.num_ini']['label'],$_SERVER['PHP_SELF'],'t.num_ini','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.num_fin']['checked'])) print_liste_field_titre($arrayfields['t.num_fin']['label'],$_SERVER['PHP_SELF'],'t.num_fin','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.num_ult']['checked'])) print_liste_field_titre($arrayfields['t.num_ult']['label'],$_SERVER['PHP_SELF'],'t.num_ult','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.num_aprob']['checked'])) print_liste_field_titre($arrayfields['t.num_aprob']['label'],$_SERVER['PHP_SELF'],'t.num_aprob','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.type']['checked'])) print_liste_field_titre($arrayfields['t.type']['label'],$_SERVER['PHP_SELF'],'t.type','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.active']['checked'])) print_liste_field_titre($arrayfields['t.active']['label'],$_SERVER['PHP_SELF'],'t.active','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.num_autoriz']['checked'])) print_liste_field_titre($arrayfields['t.num_autoriz']['label'],$_SERVER['PHP_SELF'],'t.num_autoriz','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.cod_control']['checked'])) print_liste_field_titre($arrayfields['t.cod_control']['label'],$_SERVER['PHP_SELF'],'t.cod_control','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.lote']['checked'])) print_liste_field_titre($arrayfields['t.lote']['label'],$_SERVER['PHP_SELF'],'t.lote','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.chave']['checked'])) print_liste_field_titre($arrayfields['t.chave']['label'],$_SERVER['PHP_SELF'],'t.chave','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.descrip']['checked'])) print_liste_field_titre($arrayfields['t.descrip']['label'],$_SERVER['PHP_SELF'],'t.descrip','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_create']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_create']['label'],$_SERVER['PHP_SELF'],'t.fk_user_create','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_mod']['label'],$_SERVER['PHP_SELF'],'t.fk_user_mod','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.status']['checked'])) print_liste_field_titre($arrayfields['t.status']['label'],$_SERVER['PHP_SELF'],'t.status','',$params,'',$sortfield,$sortorder);

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
if (! empty($arrayfields['t.entity']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_entity" value="'.$search_entity.'" size="10"></td>';
if (! empty($arrayfields['t.fk_subsidiaryid']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_subsidiaryid" value="'.$search_fk_subsidiaryid.'" size="10"></td>';
if (! empty($arrayfields['t.series']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_series" value="'.$search_series.'" size="10"></td>';
if (! empty($arrayfields['t.num_ini']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_num_ini" value="'.$search_num_ini.'" size="10"></td>';
if (! empty($arrayfields['t.num_fin']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_num_fin" value="'.$search_num_fin.'" size="10"></td>';
if (! empty($arrayfields['t.num_ult']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_num_ult" value="'.$search_num_ult.'" size="10"></td>';
if (! empty($arrayfields['t.num_aprob']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_num_aprob" value="'.$search_num_aprob.'" size="10"></td>';
if (! empty($arrayfields['t.type']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_type" value="'.$search_type.'" size="10"></td>';
if (! empty($arrayfields['t.active']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_active" value="'.$search_active.'" size="10"></td>';
if (! empty($arrayfields['t.num_autoriz']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_num_autoriz" value="'.$search_num_autoriz.'" size="10"></td>';
if (! empty($arrayfields['t.cod_control']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_cod_control" value="'.$search_cod_control.'" size="10"></td>';
if (! empty($arrayfields['t.lote']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_lote" value="'.$search_lote.'" size="10"></td>';
if (! empty($arrayfields['t.chave']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_chave" value="'.$search_chave.'" size="10"></td>';
if (! empty($arrayfields['t.descrip']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_descrip" value="'.$search_descrip.'" size="10"></td>';
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
