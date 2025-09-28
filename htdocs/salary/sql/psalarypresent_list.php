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
 *   	\file       salary/psalarypresent_list.php
 *		\ingroup    salary
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-01-09 12:17
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
dol_include_once('/salary/class/psalarypresent.class.php');

// Load traductions files requiredby by page
$langs->load("salary");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_fk_proces=GETPOST('search_fk_proces','int');
$search_fk_type_fol=GETPOST('search_fk_type_fol','int');
$search_fk_concept=GETPOST('search_fk_concept','int');
$search_fk_period=GETPOST('search_fk_period','int');
$search_fk_user=GETPOST('search_fk_user','int');
$search_fk_cc=GETPOST('search_fk_cc','int');
$search_sequen=GETPOST('search_sequen','int');
$search_type=GETPOST('search_type','int');
$search_cuota=GETPOST('search_cuota','int');
$search_semana=GETPOST('search_semana','int');
$search_amount_inf=GETPOST('search_amount_inf','alpha');
$search_amount=GETPOST('search_amount','alpha');
$search_hours_info=GETPOST('search_hours_info','int');
$search_hours=GETPOST('search_hours','int');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_fk_account=GETPOST('search_fk_account','int');
$search_payment_state=GETPOST('search_payment_state','int');
$search_state=GETPOST('search_state','int');


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
$hookmanager->initHooks(array('psalarypresentlist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('salary');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Load object if id or ref is provided as parameter
$object=new Psalarypresent($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Definition of fields for list
$arrayfields=array(
    
't.entity'=>array('label'=>$langs->trans("Fieldentity"), 'checked'=>1),
't.fk_proces'=>array('label'=>$langs->trans("Fieldfk_proces"), 'checked'=>1),
't.fk_type_fol'=>array('label'=>$langs->trans("Fieldfk_type_fol"), 'checked'=>1),
't.fk_concept'=>array('label'=>$langs->trans("Fieldfk_concept"), 'checked'=>1),
't.fk_period'=>array('label'=>$langs->trans("Fieldfk_period"), 'checked'=>1),
't.fk_user'=>array('label'=>$langs->trans("Fieldfk_user"), 'checked'=>1),
't.fk_cc'=>array('label'=>$langs->trans("Fieldfk_cc"), 'checked'=>1),
't.sequen'=>array('label'=>$langs->trans("Fieldsequen"), 'checked'=>1),
't.type'=>array('label'=>$langs->trans("Fieldtype"), 'checked'=>1),
't.cuota'=>array('label'=>$langs->trans("Fieldcuota"), 'checked'=>1),
't.semana'=>array('label'=>$langs->trans("Fieldsemana"), 'checked'=>1),
't.amount_inf'=>array('label'=>$langs->trans("Fieldamount_inf"), 'checked'=>1),
't.amount'=>array('label'=>$langs->trans("Fieldamount"), 'checked'=>1),
't.hours_info'=>array('label'=>$langs->trans("Fieldhours_info"), 'checked'=>1),
't.hours'=>array('label'=>$langs->trans("Fieldhours"), 'checked'=>1),
't.fk_user_create'=>array('label'=>$langs->trans("Fieldfk_user_create"), 'checked'=>1),
't.fk_user_mod'=>array('label'=>$langs->trans("Fieldfk_user_mod"), 'checked'=>1),
't.fk_account'=>array('label'=>$langs->trans("Fieldfk_account"), 'checked'=>1),
't.payment_state'=>array('label'=>$langs->trans("Fieldpayment_state"), 'checked'=>1),
't.state'=>array('label'=>$langs->trans("Fieldstate"), 'checked'=>1),

    
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
$search_fk_proces='';
$search_fk_type_fol='';
$search_fk_concept='';
$search_fk_period='';
$search_fk_user='';
$search_fk_cc='';
$search_sequen='';
$search_type='';
$search_cuota='';
$search_semana='';
$search_amount_inf='';
$search_amount='';
$search_hours_info='';
$search_hours='';
$search_fk_user_create='';
$search_fk_user_mod='';
$search_fk_account='';
$search_payment_state='';
$search_state='';

	
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

		$sql .= " t.entity,";
		$sql .= " t.fk_proces,";
		$sql .= " t.fk_type_fol,";
		$sql .= " t.fk_concept,";
		$sql .= " t.fk_period,";
		$sql .= " t.fk_user,";
		$sql .= " t.fk_cc,";
		$sql .= " t.sequen,";
		$sql .= " t.type,";
		$sql .= " t.cuota,";
		$sql .= " t.semana,";
		$sql .= " t.amount_inf,";
		$sql .= " t.amount,";
		$sql .= " t.hours_info,";
		$sql .= " t.hours,";
		$sql .= " t.date_reg,";
		$sql .= " t.date_create,";
		$sql .= " t.date_mod,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.fk_account,";
		$sql .= " t.payment_state,";
		$sql .= " t.tms,";
		$sql .= " t.state";


// Add fields for extrafields
foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."p_salary_present as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."p_salary_present_extrafields as ef on (u.rowid = ef.fk_object)";
$sql.= " WHERE 1 = 1";
//$sql.= " WHERE u.entity IN (".getEntity('mytable',1).")";

if ($search_entity) $sql.= natural_search("entity",$search_entity);
if ($search_fk_proces) $sql.= natural_search("fk_proces",$search_fk_proces);
if ($search_fk_type_fol) $sql.= natural_search("fk_type_fol",$search_fk_type_fol);
if ($search_fk_concept) $sql.= natural_search("fk_concept",$search_fk_concept);
if ($search_fk_period) $sql.= natural_search("fk_period",$search_fk_period);
if ($search_fk_user) $sql.= natural_search("fk_user",$search_fk_user);
if ($search_fk_cc) $sql.= natural_search("fk_cc",$search_fk_cc);
if ($search_sequen) $sql.= natural_search("sequen",$search_sequen);
if ($search_type) $sql.= natural_search("type",$search_type);
if ($search_cuota) $sql.= natural_search("cuota",$search_cuota);
if ($search_semana) $sql.= natural_search("semana",$search_semana);
if ($search_amount_inf) $sql.= natural_search("amount_inf",$search_amount_inf);
if ($search_amount) $sql.= natural_search("amount",$search_amount);
if ($search_hours_info) $sql.= natural_search("hours_info",$search_hours_info);
if ($search_hours) $sql.= natural_search("hours",$search_hours);
if ($search_fk_user_create) $sql.= natural_search("fk_user_create",$search_fk_user_create);
if ($search_fk_user_mod) $sql.= natural_search("fk_user_mod",$search_fk_user_mod);
if ($search_fk_account) $sql.= natural_search("fk_account",$search_fk_account);
if ($search_payment_state) $sql.= natural_search("payment_state",$search_payment_state);
if ($search_state) $sql.= natural_search("state",$search_state);


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
if ($search_fk_proces != '') $params.= '&amp;search_fk_proces='.urlencode($search_fk_proces);
if ($search_fk_type_fol != '') $params.= '&amp;search_fk_type_fol='.urlencode($search_fk_type_fol);
if ($search_fk_concept != '') $params.= '&amp;search_fk_concept='.urlencode($search_fk_concept);
if ($search_fk_period != '') $params.= '&amp;search_fk_period='.urlencode($search_fk_period);
if ($search_fk_user != '') $params.= '&amp;search_fk_user='.urlencode($search_fk_user);
if ($search_fk_cc != '') $params.= '&amp;search_fk_cc='.urlencode($search_fk_cc);
if ($search_sequen != '') $params.= '&amp;search_sequen='.urlencode($search_sequen);
if ($search_type != '') $params.= '&amp;search_type='.urlencode($search_type);
if ($search_cuota != '') $params.= '&amp;search_cuota='.urlencode($search_cuota);
if ($search_semana != '') $params.= '&amp;search_semana='.urlencode($search_semana);
if ($search_amount_inf != '') $params.= '&amp;search_amount_inf='.urlencode($search_amount_inf);
if ($search_amount != '') $params.= '&amp;search_amount='.urlencode($search_amount);
if ($search_hours_info != '') $params.= '&amp;search_hours_info='.urlencode($search_hours_info);
if ($search_hours != '') $params.= '&amp;search_hours='.urlencode($search_hours);
if ($search_fk_user_create != '') $params.= '&amp;search_fk_user_create='.urlencode($search_fk_user_create);
if ($search_fk_user_mod != '') $params.= '&amp;search_fk_user_mod='.urlencode($search_fk_user_mod);
if ($search_fk_account != '') $params.= '&amp;search_fk_account='.urlencode($search_fk_account);
if ($search_payment_state != '') $params.= '&amp;search_payment_state='.urlencode($search_payment_state);
if ($search_state != '') $params.= '&amp;search_state='.urlencode($search_state);

	
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
if (! empty($arrayfields['t.fk_proces']['checked'])) print_liste_field_titre($arrayfields['t.fk_proces']['label'],$_SERVER['PHP_SELF'],'t.fk_proces','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_type_fol']['checked'])) print_liste_field_titre($arrayfields['t.fk_type_fol']['label'],$_SERVER['PHP_SELF'],'t.fk_type_fol','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_concept']['checked'])) print_liste_field_titre($arrayfields['t.fk_concept']['label'],$_SERVER['PHP_SELF'],'t.fk_concept','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_period']['checked'])) print_liste_field_titre($arrayfields['t.fk_period']['label'],$_SERVER['PHP_SELF'],'t.fk_period','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user']['checked'])) print_liste_field_titre($arrayfields['t.fk_user']['label'],$_SERVER['PHP_SELF'],'t.fk_user','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_cc']['checked'])) print_liste_field_titre($arrayfields['t.fk_cc']['label'],$_SERVER['PHP_SELF'],'t.fk_cc','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.sequen']['checked'])) print_liste_field_titre($arrayfields['t.sequen']['label'],$_SERVER['PHP_SELF'],'t.sequen','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.type']['checked'])) print_liste_field_titre($arrayfields['t.type']['label'],$_SERVER['PHP_SELF'],'t.type','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.cuota']['checked'])) print_liste_field_titre($arrayfields['t.cuota']['label'],$_SERVER['PHP_SELF'],'t.cuota','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.semana']['checked'])) print_liste_field_titre($arrayfields['t.semana']['label'],$_SERVER['PHP_SELF'],'t.semana','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount_inf']['checked'])) print_liste_field_titre($arrayfields['t.amount_inf']['label'],$_SERVER['PHP_SELF'],'t.amount_inf','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.amount']['checked'])) print_liste_field_titre($arrayfields['t.amount']['label'],$_SERVER['PHP_SELF'],'t.amount','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.hours_info']['checked'])) print_liste_field_titre($arrayfields['t.hours_info']['label'],$_SERVER['PHP_SELF'],'t.hours_info','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.hours']['checked'])) print_liste_field_titre($arrayfields['t.hours']['label'],$_SERVER['PHP_SELF'],'t.hours','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_create']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_create']['label'],$_SERVER['PHP_SELF'],'t.fk_user_create','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print_liste_field_titre($arrayfields['t.fk_user_mod']['label'],$_SERVER['PHP_SELF'],'t.fk_user_mod','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.fk_account']['checked'])) print_liste_field_titre($arrayfields['t.fk_account']['label'],$_SERVER['PHP_SELF'],'t.fk_account','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.payment_state']['checked'])) print_liste_field_titre($arrayfields['t.payment_state']['label'],$_SERVER['PHP_SELF'],'t.payment_state','',$params,'',$sortfield,$sortorder);
if (! empty($arrayfields['t.state']['checked'])) print_liste_field_titre($arrayfields['t.state']['label'],$_SERVER['PHP_SELF'],'t.state','',$params,'',$sortfield,$sortorder);

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
if (! empty($arrayfields['t.fk_proces']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_proces" value="'.$search_fk_proces.'" size="10"></td>';
if (! empty($arrayfields['t.fk_type_fol']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_type_fol" value="'.$search_fk_type_fol.'" size="10"></td>';
if (! empty($arrayfields['t.fk_concept']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_concept" value="'.$search_fk_concept.'" size="10"></td>';
if (! empty($arrayfields['t.fk_period']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_period" value="'.$search_fk_period.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user" value="'.$search_fk_user.'" size="10"></td>';
if (! empty($arrayfields['t.fk_cc']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_cc" value="'.$search_fk_cc.'" size="10"></td>';
if (! empty($arrayfields['t.sequen']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_sequen" value="'.$search_sequen.'" size="10"></td>';
if (! empty($arrayfields['t.type']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_type" value="'.$search_type.'" size="10"></td>';
if (! empty($arrayfields['t.cuota']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_cuota" value="'.$search_cuota.'" size="10"></td>';
if (! empty($arrayfields['t.semana']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_semana" value="'.$search_semana.'" size="10"></td>';
if (! empty($arrayfields['t.amount_inf']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount_inf" value="'.$search_amount_inf.'" size="10"></td>';
if (! empty($arrayfields['t.amount']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_amount" value="'.$search_amount.'" size="10"></td>';
if (! empty($arrayfields['t.hours_info']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_hours_info" value="'.$search_hours_info.'" size="10"></td>';
if (! empty($arrayfields['t.hours']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_hours" value="'.$search_hours.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_create']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_create" value="'.$search_fk_user_create.'" size="10"></td>';
if (! empty($arrayfields['t.fk_user_mod']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_user_mod" value="'.$search_fk_user_mod.'" size="10"></td>';
if (! empty($arrayfields['t.fk_account']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_account" value="'.$search_fk_account.'" size="10"></td>';
if (! empty($arrayfields['t.payment_state']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_payment_state" value="'.$search_payment_state.'" size="10"></td>';
if (! empty($arrayfields['t.state']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_state" value="'.$search_state.'" size="10"></td>';

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
