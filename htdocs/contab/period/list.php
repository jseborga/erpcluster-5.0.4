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
 *   	\file       almacen/contabperiodo_list.php
 *		\ingroup    almacen
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-03-17 18:00
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

require_once DOL_DOCUMENT_ROOT.'/contab/lib/contab.lib.php';
dol_include_once('/contab/class/contabperiodoext.class.php');

// Load traductions files requiredby by page
$langs->load("contab");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_period_month=GETPOST('search_period_month','alpha');
$search_period_year=GETPOST('search_period_year','int');
$search_statut=GETPOST('search_statut','int');
$search_status_af=GETPOST('search_status_af','int');
$search_status_al=GETPOST('search_status_al','int');


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

$typemethod = $conf->global->ALMACEN_METHOD_VALUATION_INVENTORY;
//verificamos el periodo
verif_year();

// Protection if external user
$socid=0;
if ($user->societe_id > 0)
{
	$socid = $user->societe_id;
	//accessforbidden();
}

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('contabperiodolist'));
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('contab');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Load object if id or ref is provided as parameter
$object=new Contabperiodoext($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Definition of fields for list
$arrayfields=array(

	't.period_month'=>array('label'=>$langs->trans("Fieldperiod_month"), 'checked'=>1),
	't.period_year'=>array('label'=>$langs->trans("Fieldperiod_year"), 'checked'=>1),
	't.date_ini'=>array('label'=>$langs->trans("Fielddate_ini"), 'checked'=>1),
	't.date_fin'=>array('label'=>$langs->trans("Fielddate_fin"), 'checked'=>1),
	't.statut'=>array('label'=>$langs->trans("Fieldstatut"), 'checked'=>1),
	't.status_af'=>array('label'=>$langs->trans("Fieldstatus_af"), 'checked'=>1),
	't.status_al'=>array('label'=>$langs->trans("Fieldstatus_al"), 'checked'=>1),


    //'t.entity'=>array('label'=>$langs->trans("Entity"), 'checked'=>1, 'enabled'=>(! empty($conf->multicompany->enabled) && empty($conf->multicompany->transverse_mode))),
	//'t.datec'=>array('label'=>$langs->trans("DateCreationShort"), 'checked'=>0, 'position'=>500),
	//'t.tms'=>array('label'=>$langs->trans("DateModificationShort"), 'checked'=>0, 'position'=>500),
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
//meses
$aMonth = monthArray($langs,0);
$aStatus = array(0=>$langs->trans('Draft'),1=>$langs->trans('Activated'));
$aStat = array(0=>$langs->trans('StatusOrderClosed'),1=>$langs->trans('StatusOrderOpen'));



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
	$search_period_month='';
	$search_period_year='';
	$search_date_ini='';
	$search_date_fin='';
	$search_statut=-1;
	$search_status_af=-1;
	$search_status_al=-1;


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
	//cambia de estado almacen
	if ($action=='actal' && $user->rights->contab->valperiod)
	{
		$object->status_al = ($object->status_al?0:1);
		$res = $object->update($user);
		if ($res <=0)
		{
			setEventMessages($object->error,$object->errors,'errors');
			$action = '';
		}
		else
		{
			setEventMessages($langs->trans('Saverecord'),null,'mesgs');
			$action = '';
		}
	}
	//cambia de estado almacen
	if ($action=='actaf' && $user->rights->contab->valperiod)
	{
		$object->status_af = ($object->status_af?0:1);
		$res = $object->update($user);
		if ($res <=0)
		{
			setEventMessages($object->error,$object->errors,'errors');
			$action = '';
		}
		else
		{
			setEventMessages($langs->trans('Saverecord'),null,'mesgs');
			$action = '';
		}
	}
	//cambia de estado periodo
	if ($action=='actper' && $user->rights->contab->valperiod)
	{
		$object->statut = ($object->statut?0:1);
		$res = $object->update($user);
		if ($res <=0)
		{
			setEventMessages($object->error,$object->errors,'errors');
			$action = '';
		}
		else
		{
			setEventMessages($langs->trans('Saverecord'),null,'mesgs');
			$action = '';
		}
	}
	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/contab/period/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;
		//buscamos si existe o no
		$filter = " AND t.period_month = ".GETPOST('period_month','int');
		$filter.= " AND t.period_year = ".GETPOST('period_year','int');
		$filter.= " AND t.entity = ".$conf->entity;
		$res = $object->fetchAll('','',0,0,array(1=>1),'AND',$filter,true);
		/* object_prop_getpost_prop */
		if (empty($res))
		{
		$object->entity=$conf->entity;
		$object->period_month=GETPOST('period_month','int');
		$object->period_year=GETPOST('period_year','int');
		$object->date_ini = dol_get_first_day($object->period_year,$object->period_month);
		$object->date_fin = dol_get_last_day($object->period_year,$object->period_month);
		$object->statut=1;
		$object->status_af=1;
		$object->status_al=1;
		$object->status_co=1;

		if (empty($object->period_month))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldperiod_month")), null, 'errors');
		}
		if (empty($object->period_year))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldperiod_year")), null, 'errors');
		}

		if (! $error)
		{
			$result=$object->create($user);
			if ($result > 0)
			{
				// Creation OK
				setEventMessages($langs->trans('Registrycreatedsuccessfully'),null,'mesgs');
				$urltogo=$backtopage?$backtopage:dol_buildpath('/contab/period/list.php',1);
				header("Location: ".$urltogo);
				exit;
			}
			{
				// Creation KO
				if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else  setEventMessages($object->error, null, 'errors');
				$action='create';
			}
		}
		else
		{
			$action='create';
		}
		}
		else
		{
			setEventMessages($langs->trans('Thereisregistration'),null,'errors');
			$action = 'create';
		}
	}

	// Cancel
	if ($action == 'update' && GETPOST('cancel')) $action='view';

	// Action to update record
	if ($action == 'update' && ! GETPOST('cancel'))
	{
		$error=0;

		$object->period_month=GETPOST('period_month','int');
		$object->period_year=GETPOST('period_year','int');

		if (empty($object->period_month))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldperiod_month")), null, 'errors');
		}
		if (empty($object->period_year))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldperiod_year")), null, 'errors');
		}

		if (! $error)
		{
			$result=$object->update($user);
			if ($result > 0)
			{
				$action='view';
			}
			else
			{
				// Creation KO
				if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else setEventMessages($object->error, null, 'errors');
				$action='edit';
			}
		}
		else
		{
			$action='edit';
		}
	}

	// Action to delete
	if ($action == 'confirm_delete')
	{
		$result=$object->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/contab/period/list.php',1));
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
$title = $langs->trans('Contabperiod');
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
$sql .= " t.period_month,";
$sql .= " t.period_year,";
$sql .= " t.date_ini,";
$sql .= " t.date_fin,";
$sql .= " t.statut,";
$sql .= " t.status_af,";
$sql .= " t.status_al";


// Add fields for extrafields
foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."contab_periodo as t";
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label)) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."contab_periodo_extrafields as ef on (u.rowid = ef.fk_object)";
//$sql.= " WHERE 1 = 1";
$sql.= " WHERE t.entity IN (".getEntity('contab_periodo',1).")";

$sql.= " AND t.period_year = ".$_SESSION['period_year'];

if ($search_entity) $sql.= natural_search("entity",$search_entity);
if ($search_period_month>=0) $sql.= natural_search("period_month",$search_period_month);
if ($search_period_year) $sql.= natural_search("period_year",$search_period_year);
if ($search_statut>=0) $sql.= natural_search("statut",$search_statut);
if ($search_status_af>=0) $sql.= natural_search("status_af",$search_status_af);
if ($search_status_al>=0) $sql.= natural_search("status_al",$search_status_al);


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
	if ($search_period_month >= 0) $params.= '&amp;search_period_month='.urlencode($search_period_month);
	if ($search_period_year != '') $params.= '&amp;search_period_year='.urlencode($search_period_year);
	if ($search_statut >= 0) $params.= '&amp;search_statut='.urlencode($search_statut);
	if ($search_status_af >= 0) $params.= '&amp;search_status_af='.urlencode($search_status_af);
	if ($search_status_al >=0) $params.= '&amp;search_status_al='.urlencode($search_status_al);


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
	if ($action == 'create')
		print '<input type="hidden" name="action" value="add">';
	elseif($action == 'edit')
	{
		print '<input type="hidden" name="action" value="update">';
		print '<input type="hidden" name="id" value="'.$object->id.'">';
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
    if (! empty($arrayfields['t.period_month']['checked'])) print_liste_field_titre($arrayfields['t.period_month']['label'],$_SERVER['PHP_SELF'],'t.period_month','',$params,'',$sortfield,$sortorder);
    if (! empty($arrayfields['t.period_year']['checked'])) print_liste_field_titre($arrayfields['t.period_year']['label'],$_SERVER['PHP_SELF'],'t.period_year','',$params,'',$sortfield,$sortorder);
    if (! empty($arrayfields['t.date_ini']['checked'])) print_liste_field_titre($arrayfields['t.date_ini']['label'],$_SERVER['PHP_SELF'],'t.date_ini','',$params,'',$sortfield,$sortorder);
    if (! empty($arrayfields['t.date_fin']['checked'])) print_liste_field_titre($arrayfields['t.date_fin']['label'],$_SERVER['PHP_SELF'],'t.date_fin','',$params,'',$sortfield,$sortorder);

    if (! empty($arrayfields['t.statut']['checked'])) print_liste_field_titre($arrayfields['t.statut']['label'],$_SERVER['PHP_SELF'],'t.statut','',$params,'',$sortfield,$sortorder);
    if (! empty($arrayfields['t.status_af']['checked'])) print_liste_field_titre($arrayfields['t.status_af']['label'],$_SERVER['PHP_SELF'],'t.status_af','',$params,'',$sortfield,$sortorder);
    if (! empty($arrayfields['t.status_al']['checked'])) print_liste_field_titre($arrayfields['t.status_al']['label'],$_SERVER['PHP_SELF'],'t.status_al','',$params,'',$sortfield,$sortorder);

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

    if ($action != 'create' && $action != 'edit')
    {
    // Fields title search
    	print '<tr class="liste_titre">';
	//
    	if (! empty($arrayfields['t.entity']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_entity" value="'.$search_entity.'" size="10"></td>';
    	if (! empty($arrayfields['t.period_month']['checked']))
    	{
    		print '<td class="liste_titre">';
    		print $form->selectarray('search_period_month',$aMonth,$search_period_month,1);
    		print '</td>';
    	}
    	if (! empty($arrayfields['t.period_year']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_period_year" value="'.$search_period_year.'" size="10"></td>';
    	if (! empty($arrayfields['t.period_year']['checked'])) print '<td class="liste_titre"></td>';
    	if (! empty($arrayfields['t.period_year']['checked'])) print '<td class="liste_titre"></td>';
    	if (! empty($arrayfields['t.statut']['checked']))
    	{
    		 print '<td class="liste_titre">';
    		 print $form->selectarray('search_statut',$aStatus,$search_statut,1);
    		 print '</td>';
    	}
    	if (! empty($arrayfields['t.status_af']['checked']))
    	{
    		 print '<td class="liste_titre">';
    		 print $form->selectarray('search_status_af',$aStat,$search_status_af,1);
    		 print '</td>';
    	}
    	if (! empty($arrayfields['t.status_al']['checked']))
    	{
    		 print '<td class="liste_titre">';
    		 print $form->selectarray('search_status_al',$aStat,$search_status_al,1);
    		 print '</td>';
    	}

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

    if ($action == 'delete') {
    	$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('DeletePeriod'), $langs->trans('ConfirmDeletePeriod'), 'confirm_delete', '', 0, 1);
    	print $formconfirm;
    }

    if ($action == 'create')
    {
            // Show here line of result
    	print '<tr '.$bc[$var].'>';
            // LIST_OF_TD_FIELDS_LIST

    	if (! empty($arrayfields['t.period_month']['checked']))
    	{
    		print '<td>'.'<input type="number" name="period_month" min="1" max="12" value="'.GETPOST('period_month').'" required>'.'</td>';
    		if (! $i) $totalarray['nbfield']++;
    	}
    	if (! empty($arrayfields['t.period_year']['checked']))
    	{
    		$yearmin = date('Y')-1;
    		$yearmax = date('Y')+1;
    		$yearmin = $_SESSION['period_year'];
    		$yearmax = $_SESSION['period_year'];

    		print '<td>'.'<input type="number" name="period_year" min="'.$yearmin.'" max="'.$yearmax.'" value="'.(GETPOST('period_year')?GETPOST('period_year'):$_SESSION['period_year']).'" required>'.'</td>';
    		if (! $i) $totalarray['nbfield']++;
    	}
    	if (! empty($arrayfields['t.date_ini']['checked']))
    	{
    		print '<td>'.'</td>';
    		if (! $i) $totalarray['nbfield']++;
    	}
    	if (! empty($arrayfields['t.date_fin']['checked']))
    	{
    		print '<td>'.'</td>';
    		if (! $i) $totalarray['nbfield']++;
    	}
    	if (! empty($arrayfields['t.statut']['checked']))
    	{
    		print '<td>'.'</td>';
    		if (! $i) $totalarray['nbfield']++;
    	}
    	if (! empty($arrayfields['t.status_af']['checked']))
    	{
    		print '<td>'.'</td>';
    		if (! $i) $totalarray['nbfield']++;
    	}
    	if (! empty($arrayfields['t.status_al']['checked']))
    	{
    		print '<td>'.'</td>';
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
    	print '<input type="submit" name="submit" value="'.$langs->trans('Save').'">';
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
    		$object->statut = $obj->statut;
    		$object->status_al = $obj->status_al;
    		$object->status_af = $obj->status_af;

            // Show here line of result
    		print '<tr '.$bc[$var].'>';
            // LIST_OF_TD_FIELDS_LIST

    		if (! empty($arrayfields['t.period_month']['checked']))
    		{
    			print '<td>'.$aMonth[$obj->period_month].'</td>';
    			if (! $i) $totalarray['nbfield']++;
    		}
    		if (! empty($arrayfields['t.period_year']['checked']))
    		{
    			print '<td>'.$obj->period_year.'</td>';
    			if (! $i) $totalarray['nbfield']++;
    		}
    		if (! empty($arrayfields['t.date_ini']['checked']))
    		{
    			print '<td>'.dol_print_date($db->jdate($obj->date_ini),'day').'</td>';
    			if (! $i) $totalarray['nbfield']++;
    		}
    		if (! empty($arrayfields['t.date_fin']['checked']))
    		{
    			print '<td>'.dol_print_date($db->jdate($obj->date_fin),'day').'</td>';
    			if (! $i) $totalarray['nbfield']++;
    		}
    		if (! empty($arrayfields['t.statut']['checked']))
    		{
   				$switch = 'switch_off';
    			if ($obj->statut == 1)
    				$switch = 'switch_on';
    			if ($user->rights->contab->valperiod)
    			{
					print '<td>'.'<a href="'.$_SERVER['PHHP_SELF'].'?id='.$obj->rowid.'&action=actper">'.img_picto('',$switch).' '.$object->getLibStatut(5).'</a>'.'</td>';
    			}
    			else
	    			print '<td>'.$object->getLibStatut(5).'</td>';
    			if (! $i) $totalarray['nbfield']++;
    		}
    		if (! empty($arrayfields['t.status_af']['checked']))
    		{
   				$switchaf = 'switch_off';
    			if ($obj->status_af == 1)
    				$switchaf = 'switch_on';
    			if ($user->rights->contab->valperiod)
    			{
					print '<td>'.'<a href="'.$_SERVER['PHHP_SELF'].'?id='.$obj->rowid.'&action=actaf">'.img_picto('',$switchaf).' '.$object->getLibStatut_af(5).'</a>'.'</td>';
    			}
    			else
	    			print '<td>'.$object->getLibStatut_af(5).'</td>';
    			if (! $i) $totalarray['nbfield']++;
    		}
    		if (! empty($arrayfields['t.status_al']['checked']))
    		{
   				$switchal = 'switch_off';
    			if ($obj->status_al == 1)
    				$switchal = 'switch_on';
    			if ($user->rights->contab->valperiod)
    			{
					print '<td>'.'<a href="'.$_SERVER['PHHP_SELF'].'?id='.$obj->rowid.'&action=actal">'.img_picto('',$switchal).' '.$object->getLibStatut_al(5).'</a>'.'</td>';
    			}
    			else
	    			print '<td>'.$object->getLibStatut_al(5).'</td>';
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

	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->contab->crearperiod)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=create">'.$langs->trans("New").'</a></div>'."\n";
		}
	}
	print '</div>'."\n";

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
