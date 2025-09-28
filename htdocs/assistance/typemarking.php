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
 *   	\file       /typemarking_page.php
 *		\ingroup
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2015-10-12 08:48
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
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
dol_include_once('/assistance/class/typemarking.class.php');
dol_include_once('/assistance/class/assistancedef.class.php');
dol_include_once('/assistance/class/adherentext.class.php');
dol_include_once('/core/lib/date.lib.php');

// Load traductions files requiredby by page
$langs->load("assistance");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');
$mark	= GETPOST('mark','int');
$sortorder	= GETPOST('sortorder','alpha');
$sortfield	= GETPOST('sortfield','alpha');
$fixed_date = dol_mktime(12,0,0,GETPOST('fd_month'),GETPOST('fd_day'),GETPOST('fd_year'),'user');


$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_mark=GETPOST('search_mark','alpha');

$search_sex=GETPOST('search_sex','alpha');

$search_detail=GETPOST('search_detail','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_date_create=GETPOST('search_date_create','int');
$search_tms=GETPOST('search_tms','int');
$search_statut=GETPOST('search_statut','int');



// Protection if external user
if ($user->societe_id > 0)
{
	accessforbidden();
}

$aMark = array(1=>'primary',
	2=>'secundary',
	3=>'third',
	4=>'fourth',
	5=>'fifth',
	6=>'sixth');
$aMarklang = array(1=>'Primary',
	2=>'Secundary',
	3=>'Third',
	4=>'Fourth',
	5=>'Fifth',
	6=>'Sixth');
$aMarkvar = array(1=>array('1i','1e'),
	2=>array('2i','2e'),
	3=>array('3i','3e'),
	4=>array('4i','4e'),
	5=>array('5i','5e'),
	6=>array('6i','6e'),);
$aMarktype = array('entry','exit');
if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Typemarking($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}
$objAssistancedef = new Assistancedef($db);
$objAdherent = new Adherentext($db);
// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('typemarking'));
$extrafields = new ExtraFields($db);
$aSelectday = array(1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,0=>0);
$aDay = array(0=>$langs->trans('Sunday'),1=>$langs->trans('Monday'),2=>$langs->trans('Tuesday'),3=>$langs->trans('Wednesday'),4=>$langs->trans('Thursday'),5=>$langs->trans('Friday'),6=>$langs->trans('Saturday'));

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{

    // Purge search criteria
	if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter.x") ||GETPOST("button_removefilter"))
    // All tests are required to be compatible with all browsers
	{

		$search_ref='';
		$search_mark='';
		$search_detail='';
	}

	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/assistance/typemarking.php',1);
			header("Location: ".$urltogo);
			exit;
		}
		$fixed_date = '';
		if (isset($_POST['fd_month']))
			$fixed_date = dol_mktime(12,0,0,GETPOST('fd_month'),GETPOST('fd_day'),GETPOST('fd_year'),'user');
		if (empty($fixed_date))
		{
			if (GETPOST('day_def'))
			{
				$aDaydef = GETPOST('day_def');
				$cDaydef = '';
				foreach ($aDaydef AS $k => $value)
				{
					if (!empty($cDaydef) || $cDaydef==='0') $cDaydef.= ',';
					$cDaydef.= $value;
				}
			}
		}
		else
		{
			$aDays = dol_getdate($fixed_date);
			$cDaydef = $aDays['wday'];
		}


		$error=0;

		/* object_prop_getpost_prop */
		$object->mark=GETPOST('mark','int');
		$object->entity=$conf->entity;
		$object->ref=GETPOST('ref','alpha');
		$object->detail=GETPOST('detail','alpha');
		$object->fixed_date=$fixed_date;

		$object->sex=GETPOST('sex','alpha');
		$object->day_def=$cDaydef;

		$object->additional_time=GETPOST('additional_time','int')+0;
		$day = date('d');
		$month = date('m');
		$year = date('Y');
		if ($object->fixed_date > 0)
		{
			$aDay = dol_getdate($object->fixed_date);
			$day = $aDay['mday'];
			$month = $aDay['mon'];
			$year = $aDay['year'];
		}

		for ($x=1;$x<=$object->mark;$x++)
		{
			$y=0;
			for ($y = 0; $y <=1; $y++)
			{
				$variable = $aMark[$x].'_'.$aMarktype[$y];
				$object->$variable = dol_mktime($_POST[$aMarkvar[$x][$y].'hour'],$_POST[$aMarkvar[$x][$y].'min'],0,$month,$day,$year,'user');
			}
		}
		$object->fk_user_create=$user->id;
		$object->fk_user_mod=$user->id;
		$object->date_create=dol_now();
		$object->tms=dol_now();
		$object->statut=1;

		if (empty($object->ref))
		{
			$error++;
			setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")),'errors');
		}

		if (! $error)
		{
			$result=$object->create($user);
			if ($result > 0)
			{
		// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/assistance/typemarking.php',1);
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

	// Cancel
	if ($action == 'update' && GETPOST('cancel')) $action='view';

	// Action to update record
	if ($action == 'update' && ! GETPOST('cancel'))
	{
		$error=0;
		$fixed_date = '';
		if (isset($_POST['fd_month']))
			$fixed_date = dol_mktime(12,0,0,GETPOST('fd_month'),GETPOST('fd_day'),GETPOST('fd_year'),'user');
		if (empty($fixed_date))
		{
			if (GETPOST('day_def'))
			{
				$aDaydef = GETPOST('day_def');
				$cDaydef = '';
				foreach ($aDaydef AS $k => $value)
				{
					if (!empty($cDaydef) || $cDaydef==='0') $cDaydef.= ',';
					$cDaydef.= $value;
				}
			}
		}
		else
		{
			$aDays = dol_getdate($fixed_date);
			$cDaydef = $aDays['wday'];
		}
		$object->mark=GETPOST('mark','int');
		$object->ref=GETPOST('ref','alpha');
		$object->detail=GETPOST('detail','alpha');
		$object->fixed_date=$fixed_date;

		$object->sex=GETPOST('sex','alpha');
		$object->day_def=$cDaydef;

		$object->additional_time=GETPOST('additional_time','int')+0;
		$day = date('d');
		$month = date('m');
		$year = date('Y');
		if ($object->fixed_date > 0)
		{
			$aDay = dol_getdate($object->fixed_date);
			$day = $aDay['mday'];
			$month = $aDay['mon'];
			$year = $aDay['year'];
		}
		for ($x=1;$x<=$object->mark;$x++)
		{
			$y=0;
			for ($y = 0; $y <=1; $y++)
			{
				$variable = $aMark[$x].'_'.$aMarktype[$y];
				$object->$variable = dol_mktime($_POST[$aMarkvar[$x][$y].'hour'],$_POST[$aMarkvar[$x][$y].'min'],0,$month,$day,$year,'user');
			}
		}
		$object->fk_user_mod=$user->id;
		$object->tms=dol_now();
		//$object->statut=1;


		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")),null,'errors');
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
			header("Location: ".dol_buildpath('/assistance/typemarking.php',1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null,$object->errors,'errors');
			else setEventMessages($object->error,null,'errors');
		}
	}
	// Action to novalidate
	if ($action == 'novalidate')
	{
		$object->statut = 0;
		$result=$object->update($user);
		if ($result > 0)
		{
			// novalidate OK
			setEventMessages($langs->trans('Successfullyupdate'),null,'mesgs');
			header("Location: ".dol_buildpath('/assistance/typemarking.php?id='.$id,1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null,$object->errors,'errors');
			else setEventMessages($object->error,null,'errors');
			$action = 'view';
		}
	}
	// Action to novalidate
	if ($action == 'validate')
	{
		$object->statut = 1;
		$result=$object->update($user);
		if ($result > 0)
		{
		// validate OK
			setEventMessages($langs->trans('Successfullyupdate'),null,'mesgs');
			header("Location: ".dol_buildpath('/assistance/typemarking.php?id='.$id,1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null,$object->errors,'errors');
			else setEventMessages($object->error,null,'errors');
			$action = 'view';
		}
	}
}

//LUCHO
if ($action == 'createrefr')
{
	$tmparray['detail'] = GETPOST('detail','alpha');
	$tmparray['ref'] = GETPOST('ref','alpha');
	$tmparray['mark'] = GETPOST('mark','int');
	$tmparray['fixed_date'] = dol_mktime(0,0,0,GETPOST('fd_month'),GETPOST('fd_day'),GETPOST('fd_year'),'user');
	$tmparray['additional_time'] = GETPOST('additional_time');
	$tmparray['primary_entry'] = dol_mktime($_POST['1ihour'],$_POST['1imin'],0,date('m')/*month*/,date('d')/*day*/,date('Y')/*year*/,'user');
	$tmparray['primary_exit'] = dol_mktime($_POST['1ehour'],$_POST['1emin'],0,date('m')/*month*/,date('d')/*day*/,date('Y')/*year*/,'user');
	$tmparray['secundary_entry'] = dol_mktime($_POST['2ihour'],$_POST['2imin'],0,date('m')/*month*/,date('d')/*day*/,date('Y')/*year*/,'user');
	$tmparray['secundary_exit'] = dol_mktime($_POST['2ehour'],$_POST['2emin'],0,date('m')/*month*/,date('d')/*day*/,date('Y')/*year*/,'user');
	$tmparray['third_entry'] = dol_mktime($_POST['3ihour'],$_POST['3imin'],0,date('m')/*month*/,date('d')/*day*/,date('Y')/*year*/,'user');
	$tmparray['third_exit'] = dol_mktime($_POST['3ehour'],$_POST['3emin'],0,date('m')/*month*/,date('d')/*day*/,date('Y')/*year*/,'user');
	$tmparray['fourth_entry'] = dol_mktime($_POST['4ihour'],$_POST['4imin'],0,date('m')/*month*/,date('d')/*day*/,date('Y')/*year*/,'user');
	$tmparray['fourth_exit'] = dol_mktime($_POST['4ehour'],$_POST['4emin'],0,date('m')/*month*/,date('d')/*day*/,date('Y')/*year*/,'user');
	$tmparray['fifth_entry'] = dol_mktime($_POST['5ihour'],$_POST['5imin'],0,date('m')/*month*/,date('d')/*day*/,date('Y')/*year*/,'user');
	$tmparray['fifth_exit'] = dol_mktime($_POST['5ehour'],$_POST['5emin'],0,date('m')/*month*/,date('d')/*day*/,date('Y')/*year*/,'user');
	$tmparray['sixth_entry'] = dol_mktime($_POST['6ihour'],$_POST['6imin'],0,date('m')/*month*/,date('d')/*day*/,date('Y')/*year*/,'user');
	$tmparray['sixth_exit'] = dol_mktime($_POST['6ehour'],$_POST['6emin'],0,date('m')/*month*/,date('d')/*day*/,date('Y')/*year*/,'user');

	if ($tmparray['mark']>0)
	{
		$detail = $tmparray['detail'];
		$additional_time = $tmparray['additional_time'];
		$ref = $tmparray['ref'];
		$object->fixed_date = $tmparray['fixed_date'];
		$mark = $tmparray['mark'];
	}
	$action='create';
}

if ($action == 'createrefe')
{
	$tmparray['detail'] = GETPOST('detail','alpha');
	$tmparray['ref'] = GETPOST('ref','alpha');
	$tmparray['mark'] = GETPOST('mark','int');
	$tmparray['additional_time'] = GETPOST('additional_time');
	$tmparray['fixed_date'] = dol_mktime(0,0,0,GETPOST('fd_month'),GETPOST('fd_day'),GETPOST('fd_year'),'user');
	$tmparray['primary_entry'] = dol_mktime($_POST['1ihour'],$_POST['1imin'],0,date('m')/*month*/,date('d')/*day*/,date('Y')/*year*/,'user');
	$tmparray['primary_exit'] = dol_mktime($_POST['1ehour'],$_POST['1emin'],0,date('m')/*month*/,date('d')/*day*/,date('Y')/*year*/,'user');
	$tmparray['secundary_entry'] = dol_mktime($_POST['2ihour'],$_POST['2imin'],0,date('m')/*month*/,date('d')/*day*/,date('Y')/*year*/,'user');
	$tmparray['secundary_exit'] = dol_mktime($_POST['2ehour'],$_POST['2emin'],0,date('m')/*month*/,date('d')/*day*/,date('Y')/*year*/,'user');
	$tmparray['third_entry'] = dol_mktime($_POST['3ihour'],$_POST['3imin'],0,date('m')/*month*/,date('d')/*day*/,date('Y')/*year*/,'user');
	$tmparray['third_exit'] = dol_mktime($_POST['3ehour'],$_POST['3emin'],0,date('m')/*month*/,date('d')/*day*/,date('Y')/*year*/,'user');
	$tmparray['fourth_entry'] = dol_mktime($_POST['4ihour'],$_POST['4imin'],0,date('m')/*month*/,date('d')/*day*/,date('Y')/*year*/,'user');
	$tmparray['fourth_exit'] = dol_mktime($_POST['4ehour'],$_POST['4emin'],0,date('m')/*month*/,date('d')/*day*/,date('Y')/*year*/,'user');
	$tmparray['fifth_entry'] = dol_mktime($_POST['5ihour'],$_POST['5imin'],0,date('m')/*month*/,date('d')/*day*/,date('Y')/*year*/,'user');
	$tmparray['fifth_exit'] = dol_mktime($_POST['5ehour'],$_POST['5emin'],0,date('m')/*month*/,date('d')/*day*/,date('Y')/*year*/,'user');
	$tmparray['sixth_entry'] = dol_mktime($_POST['6ihour'],$_POST['6imin'],0,date('m')/*month*/,date('d')/*day*/,date('Y')/*year*/,'user');
	$tmparray['sixth_exit'] = dol_mktime($_POST['6ehour'],$_POST['6emin'],0,date('m')/*month*/,date('d')/*day*/,date('Y')/*year*/,'user');

	if ($tmparray['mark']>0)
	{
		$object->detail = $tmparray['detail'];
		$object->additional_time = $tmparray['additional_time'];
		$object->ref = $tmparray['ref'];
		$object->mark = $tmparray['mark'];
		$object->fixed_date = $tmparray['fixed_date'];
		$mark = $tmparray['mark'];
	}
	$action='edit';
}


/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('',$langs->trans('Typemarking'),'');

$form=new Form($db);


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


// Part to show a list
if ($action == 'list' || (empty($id) && $action != 'create'))
{
	// Put here content of your page
	print load_fiche_titre($langs->trans('Dialssettings'));

	$sql = "SELECT";
	$sql.= " t.rowid,";

	$sql .= " t.entity,";
	$sql .= " t.ref,";
	$sql .= " t.detail,";
	$sql .= " t.mark,";
	$sql .= " t.fixed_date,";
	$sql .= " t.sex,";
	$sql .= " t.day_def,";
	$sql .= " t.primary_entry,";
	$sql .= " t.primary_exit,";
	$sql .= " t.secundary_entry,";
	$sql .= " t.secundary_exit,";
	$sql .= " t.third_entry,";
	$sql .= " t.third_exit,";
	$sql .= " t.fourth_entry,";
	$sql .= " t.fourth_exit,";
	$sql .= " t.fifth_entry,";
	$sql .= " t.fifth_exit,";
	$sql .= " t.sixth_entry,";
	$sql .= " t.sixth_exit,";
	$sql .= " t.fk_user_create,";
	$sql .= " t.fk_user_mod,";
	$sql .= " t.date_create,";
	$sql .= " t.tms,";
	$sql .= " t.statut";


	// Add fields for extrafields
	foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
	// Add fields from hooks
	$parameters=array();
	$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
	$sql.=$hookmanager->resPrint;
	$sql.= " FROM ".MAIN_DB_PREFIX."type_marking as t";
	$sql.= " WHERE 1 = 1";
	$sql.= " AND t.entity = ".$conf->entity;
	if ($search_ref) $sql.= natural_search("ref",$search_ref);
	if ($search_mark) $sql.= natural_search("mark",$search_mark);
	if ($search_detail) $sql.= natural_search("detail",$search_detail);

	if ($search_sex) $sql.=" AND t.sex = '".$search_sex."'";//$sql.= natural_search("sex",$search_sex);


	if ($search_statut) $sql.= natural_search("statut",$search_statut);


	//echo $sql;
	// Add where from hooks
	$parameters=array();
	$reshook=$hookmanager->executeHooks('printFieldListWhere',$parameters);    // Note that $action and $object may have been modified by hook
	$sql.=$hookmanager->resPrint;

	// Count total nb of records
	$nbtotalofrecords = 0;
	// if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
	// {
	// 	$result = $db->query($sql);
	// 	$nbtotalofrecords = $db->num_rows($result);
	// }
	if (empty($sortfield))
		$sortfield = 't.ref';
	if (empty($sortorder))
		$sortorder = 'ASC';

	$sql.= $db->order($sortfield, $sortorder);
	$sql.= $db->plimit($conf->liste_limit+1, $offset);

	dol_syslog($script_file, LOG_DEBUG);
	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);

		$params='';
		$params.= '&amp;search_field1='.urlencode($search_field1);
		$params.= '&amp;search_field2='.urlencode($search_field2);

		print_barre_liste($title, $page, $_SERVER["PHP_SELF"],$params,$sortfield,$sortorder,'',$num,$nbtotalofrecords,'title_companies');


		print '<form method="GET" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';

		if (! empty($moreforfilter))
		{
			print '<div class="liste_titre">';
			print $moreforfilter;
			$parameters=array();
			$reshook=$hookmanager->executeHooks('printFieldPreListTitle',$parameters);    // Note that $action and $object may have been modified by hook
			print $hookmanager->resPrint;
			print '</div>';
		}

		print '<table class="noborder">'."\n";

		// Fields title
		print '<tr class="liste_titre">';

		print_liste_field_titre($langs->trans('Ref'),$_SERVER['PHP_SELF'],'t.ref','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Mark'),$_SERVER['PHP_SELF'],'t.mark','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Detail'),$_SERVER['PHP_SELF'],'t.detail','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Fieldfixed_date'),$_SERVER['PHP_SELF'],'t.fixed_date','',$param,'',$sortfield,$sortorder);

		print_liste_field_titre($langs->trans('Sex'),$_SERVER['PHP_SELF'],'t.sex','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Days'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Primaryentry'),$_SERVER['PHP_SELF'],'t.primary_entry','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Primaryexit'),$_SERVER['PHP_SELF'],'t.primary_exit','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Secundaryentry'),$_SERVER['PHP_SELF'],'t.secundary_entry','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Secundaryexit'),$_SERVER['PHP_SELF'],'t.secundary_exit','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Thirdentry'),$_SERVER['PHP_SELF'],'t.third_entry','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Thirdexit'),$_SERVER['PHP_SELF'],'t.third_exit','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Fourthentry'),$_SERVER['PHP_SELF'],'t.fourth_entry','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Fourthexit'),$_SERVER['PHP_SELF'],'t.fourth_exit','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Fifthentry'),$_SERVER['PHP_SELF'],'t.fifth_entry','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Fifthexit'),$_SERVER['PHP_SELF'],'t.fifth_exit','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Sixthentry'),$_SERVER['PHP_SELF'],'t.sixth_entry','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Sixthexit'),$_SERVER['PHP_SELF'],'t.sixth_exit','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Additionaltime'),$_SERVER['PHP_SELF'],'t.additional_time','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Quantmember'),$_SERVER['PHP_SELF'],'','',$param,'align="center"',$sortfield,$sortorder);

		print_liste_field_titre($langs->trans('Statut'),$_SERVER['PHP_SELF'],'t.statut','',$param,'',$sortfield,$sortorder);

		$parameters=array();
		$reshook=$hookmanager->executeHooks('printFieldListTitle',$parameters);    // Note that $action and $object may have been modified by hook
		print $hookmanager->resPrint;


		$aSexo = array(-1=>$langs->trans("All"),1=>$langs->trans("Men"),2=>$langs->trans("Women"));
		foreach ($aSexo as $key => $value) {
			if($search_sex == $key){
				$opcSex .= '<option value="'.$key.'" selected>'.$value."</option>";
			}else{
				$opcSex .= '<option value="'.$key.'">'.$value."</option>";
			}
		}



		print '</tr>'."\n";

		// Fields title search
		print '<tr class="liste_titre">';
		print '<td class="liste_titre"><input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="10"></td>';
		print '<td class="liste_titre"><input type="text" class="flat" name="search_mark" value="'.$search_mark.'" size="10"></td>';
		print '<td class="liste_titre"><input type="text" class="flat" name="search_detail" value="'.$search_detail.'" size="10"></td>';

		print '<td class="liste_titre"></td>';
		print '<td class="liste_titre"><select name="search_sex">'.$opcSex.'</select></td>';

		print '<td class="liste_titre" colspan="14"></td>';
		print '<td class="liste_titre"></td>';
		//print '<td class="liste_titre"></td>';
		$parameters=array();
		$reshook=$hookmanager->executeHooks('printFieldListOption',$parameters);
		// Note that $action and $object may have been modified by hook
		print $hookmanager->resPrint;
// Action column
		print '<td class="liste_titre" align="right">';
		$searchpitco=$form->showFilterAndCheckAddButtons($massactionbutton?1:0, 'checkforselect', 1);
		print $searchpitco;
		print '</td>';
		print '</tr>'."\n";

		$i = 0;
		while ($i < $num)
		{
			$obj = $db->fetch_object($resql);
			if ($obj)
			{
				//calculamos que cantidad e miembros eestan asignados
				$html = '';
				$filter = " AND t.type_marking = '".$obj->ref."'";
				$nReg = $objAssistancedef->fetchAll('','',0,0,array(1=>1),'AND',$filter);
				if ($nReg>0)
				{
					//armamos los nombres de las personas
					$lines = $objAssistancedef->lines;
					$htmltooltip='';
					foreach ($lines AS $j => $line)
					{
						$resadh = $objAdherent->fetch($line->fk_reg);
						if ($resadh==1)
						{
							$htmltooltip.=''.$langs->trans("Name").': <b>'.$objAdherent->lastname.' '.$objAdherent->firstname.'</b><br>';
						}
					}
					$html = $form->textwithpicto('',$htmltooltip,1,0);
				}
				// You can use here results
				print '<tr>';

				print '<td>'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$obj->rowid.'">'.$obj->ref.'</a>'.'</td>';
				print '<td>'.$obj->mark.'</td>';
				print '<td>'.$obj->detail.'</td>';
				if ($obj->fixed_date>0)
					print '<td>'.dol_print_date($db->jdate($obj->fixed_date),'day').'</td>';
				else
					print '<td></td>';

				print '<td>'.$aSexo[$obj->sex].'</td>';
				$aDays = explode(',',$obj->day_def);
				$cDay = '';
				foreach ((array) $aDays AS $k => $value)
				{
					if (!empty($cDay)) $cDay.=', ';
					$cDay.= $aDay[$value];
				}
				print '<td>'.$cDay.'</td>';

				print '<td>'.dol_print_date($db->jdate($obj->primary_entry),'hour').'</td>';
				print '<td>'.dol_print_date($db->jdate($obj->primary_exit),'hour').'</td>';
				print '<td>'.dol_print_date($db->jdate($obj->secundary_entry),'hour').'</td>';
				print '<td>'.dol_print_date($db->jdate($obj->secundary_exit),'hour').'</td>';
				print '<td>'.dol_print_date($db->jdate($obj->third_entry),'hour').'</td>';
				print '<td>'.dol_print_date($db->jdate($obj->third_exit),'hour').'</td>';
				print '<td>'.dol_print_date($db->jdate($obj->fourth_entry),'hour').'</td>';
				print '<td>'.dol_print_date($db->jdate($obj->fourth_exit),'hour').'</td>';
				print '<td>'.dol_print_date($db->jdate($obj->fifth_entry),'hour').'</td>';
				print '<td>'.dol_print_date($db->jdate($obj->fifth_exit),'hour').'</td>';
				print '<td>'.dol_print_date($db->jdate($obj->sixth_entry),'hour').'</td>';
				print '<td>'.dol_print_date($db->jdate($obj->sixth_exit),'hour').'</td>';
				print '<td>'.$obj->additional_time.'</td>';
				print '<td align="center">'.$nReg.' '.$html.'</td>';
				print '<td align="center">'.$object->libStatut($obj->statut,3).'</td>';


				$parameters=array('obj' => $obj);
				$reshook=$hookmanager->executeHooks('printFieldListValue',$parameters);
				// Note that $action and $object may have been modified by hook
				print $hookmanager->resPrint;
				print '</tr>';
			}
			$i++;
		}

		$db->free($resql);

		$parameters=array('sql' => $sql);
		$reshook=$hookmanager->executeHooks('printFieldListFooter',$parameters);
		 // Note that $action and $object may have been modified by hook
		print $hookmanager->resPrint;

		print "</table>\n";
		print "</form>\n";

	// Buttons
		print '<div class="tabsAction">'."\n";
		$parameters=array();
		$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);
	 // Note that $action and $object may have been modified by hook
		if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

		if (empty($reshook))
		{
			if ($user->rights->assistance->def->crear)
			{
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=create">'.$langs->trans("New").'</a></div>'."\n";
			}
		}
		print '</div>'."\n";

	}
	else
	{
		$error++;
		dol_print_error($db);
	}
}



// Part to create
if ($action == 'create')
{
	print_fiche_titre($langs->trans("New"));
	print "\n".'<script type="text/javascript" language="javascript">';
	print '$(document).ready(function () {
		$("#mark").change(function() {
			document.form.action.value="createrefr";
			document.form.submit();
		});
		$("#fd_day").change(function() {
			document.form.action.value="createrefr";
			document.form.submit();
		});
	});';
	print '</script>'."\n";

	$aSexo = array(-1=>$langs->trans("All"),1=>$langs->trans("Men"),2=>$langs->trans("Women"));

	foreach ($aSexo as $key => $value) {
		if(GETPOST('sex') == $key){
			$opcSex .= '<option value="'.$key.'" selected>'.$value."</option>";
		}else{
			$opcSex .= '<option value="'.$key.'">'.$value."</option>";
		}
	}

	print '<form method="POST" id="form" name="form" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';


	dol_fiche_head();
	if ($mark >12)$mark=12;

	print '<table class="border centpercent">'."\n";
	print '<tr><td width="15%" class="fieldrequired">'.$langs->trans("Ref").'</td><td>';
	print '<input class="flat" type="text" size="10" name="ref" value="'.$ref.'">';
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>';
	print '<input class="flat" type="text" size="35" name="detail" value="'.$detail.'">';
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfixed_date").'</td><td>';
	print $form->select_date($fixed_date,'fd_',0,0,1);
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Sex").'</td><td>';
	print '<select name="sex">'.$opcSex.'</select>';
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Days").'</td><td>';
	if (GETPOST('day_def'))
	{
		$aDaydef = GETPOST('day_def');
		unset($aSelectday);
		foreach ($aDaydef AS $k => $value)
			$aSelectday[$value] = $value;
	}
	if (empty($fixed_date)) print $form->multiselectarray('day_def',$aDay,$aSelectday);
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Numbermark").'</td><td>';
	print '<input class="flat" id="mark" type="number" min="2" step="2" max="12" name="mark" value="'.$mark.'">';
	print '</td></tr>';

	if (empty($mark)) $mark=0;
	$mark = $mark/2;
	for($x = 1; $x <= $mark; $x++)
	{
		$y=0;
		for ($y = 0; $y <=1; $y++)
		{
			$labeltext = $aMarklang[$x].$aMarktype[$y];
			$variable = $aMark[$x].'_'.$aMarktype[$y];
			$variable = $tmparray[$aMark[$x].'_'.$aMarktype[$y]];
			print '<tr><td class="fieldrequired">'.$langs->trans($labeltext).'</td><td>';
			print $form->select_date($variable,$aMarkvar[$x][$y],1,1,1,'',0);
			print '</td></tr>';
		}
	}


	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}



// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	print_fiche_titre($langs->trans("Edit"));

	print "\n".'<script type="text/javascript" language="javascript">';
	print '$(document).ready(function () {
		$("#mark").change(function() {
			document.form.action.value="createrefe";
			document.form.submit();
		});
		$("#fd_").change(function() {
			document.form.action.value="createrefe";
			document.form.submit();
		});

	});';

	$aSexo = array(-1=>$langs->trans("All"),1=>$langs->trans("Men"),2=>$langs->trans("Women"));

	foreach ($aSexo as $key => $value) {
		if($object->sex == $key){
			$opcSex .= '<option value="'.$key.'" selected >'.$value."</option>";
		}else{
			$opcSex .= '<option value="'.$key.'">'.$value."</option>";
		}

	}

	print '</script>'."\n";
	print '<form method="POST" id="form" name="form" action="'.$_SERVER["PHP_SELF"].'">';

	dol_fiche_head();

	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';

	print '<table class="border centpercent">'."\n";
	print '<tr><td class="fieldrequired">'.$langs->trans("Ref").'</td><td>';
	print '<input class="flat" type="text" size="10" name="ref" value="'.$object->ref.'">';
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>';
	print '<input class="flat" type="text" size="35" name="detail" value="'.$object->detail.'">';
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfixed_date").'</td><td>';
	print $form->select_date($object->fixed_date,'fd_',0,0,1);
	$fixeddate = $object->fixed_date;
	if (GETPOST('fd_day')>0) $fixeddate = $fixed_date;
	print '</td></tr>';


	print '<tr><td class="fieldrequired">'.$langs->trans("Sex").'</td><td>';
	print '<select name="sex">'.$opcSex.'</select>';
	print '</td></tr>';
	$aSelectday = explode(',',$object->day_def);
	if (GETPOST('day_def'))
	{
		$aDaydef = GETPOST('day_def');
		unset($aSelectday);
		foreach ($aDaydef AS $k => $value)
			$aSelectday[$value] = $value;
	}
	print '<tr><td class="fieldrequired">'.$langs->trans("Days").'</td><td>';
	if ($fixeddate<=0)
		print $form->multiselectarray('day_def',$aDay,$aSelectday);
	else
	{
		$aDays = dol_getdate($fixeddate);
		print $aDay[$aDays['wday']];
	}
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Numbermark").'</td><td>';
	print '<input class="flat" id="mark" type="number" min="0" step="2" max="12" name="mark" value="'.$object->mark.'">';
	print '</td></tr>';
	$mark = (!empty($mark)?$mark/2:$object->mark/2);
	for($x = 1; $x <= $mark; $x++)
	{
		$y=0;
		for ($y = 0; $y <=1; $y++)
		{
			$labeltext = $aMarklang[$x].$aMarktype[$y];
			if (!empty($tmparray[$aMark[$x]]))
				$variable = $tmparray[$aMark[$x].'_'.$aMarktype[$y]];
			else
			{
				$var=$aMark[$x].'_'.$aMarktype[$y];
				$variable = $object->$var;
			}
			print '<tr><td class="fieldrequired">'.$langs->trans($labeltext).'</td><td>';
			print $form->select_date($variable,$aMarkvar[$x][$y],1,1,1,'',0);
			print '</td></tr>';
		}
	}

	print '<tr><td class="fieldrequired">'.$langs->trans("Aditionaltime").'</td><td>';
	print '<input class="flat" type="number" min="0" step="1" max="60" name="additional_time" value="'.$object->additional_time.'">';
	print '</td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Save").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}



// Part to show record
if ($id && (empty($action) || $action == 'view' || $action == 'delete'))
{
	print load_fiche_titre($langs->trans('Dialssettings'));

	dol_fiche_head();
	$aSexo = array(-1=>$langs->trans("All"),1=>$langs->trans("Men"),2=>$langs->trans("Women"));

	// Confirm delete request
	if ($action == 'delete')
	{
		$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Delete"),$langs->trans("Confirmdeletetypemarking",$object->ref),"confirm_delete",'',0,2);
		if ($ret == 'html') print '<br>';
	}
	print '<table class="border centpercent">'."\n";
	print '<tr><td width="15%">'.$langs->trans("Ref").'</td><td>';
	print $object->ref;
	print '</td></tr>';
	print '<tr><td>'.$langs->trans("Mark").'</td><td>';
	print $object->mark;
	print '</td></tr>';
	print '<tr><td>'.$langs->trans("Label").'</td><td>';
	print $object->detail;
	print '</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldfixed_date").'</td><td>';
	print dol_print_date($object->fixed_date,'day');
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("Sex").'</td><td>';
	print $aSexo[$object->sex];
	print '</td></tr>';
	$aDays = explode(',',$object->day_def);
	$cDay = '';
	foreach ((array) $aDays AS $k => $value)
	{
		if (!empty($cDay)) $cDay.=', ';
		$cDay.= $aDay[$value];
	}
	print '<tr><td>'.$langs->trans("Day").'</td><td>';
	if ($object->fixed_date>0)
	{
		$aDays = dol_getdate($object->fixed_date);
		print $aDay[$aDays['wday']];
	}
	else
		print $cDay;
	print '</td></tr>';
	$mark = $object->mark/2;
	for($x = 1; $x <= $mark; $x++)
	{
		$y=0;
		for ($y = 0; $y <=1; $y++)
		{
			$labeltext = $aMarklang[$x].$aMarktype[$y];
			$variable = $aMark[$x].'_'.$aMarktype[$y];
			$variable = $aMark[$x].'_'.$aMarktype[$y];
			print '<tr><td>'.$langs->trans($labeltext).'</td><td>';
			print dol_print_date($object->$variable,'hour');
			print '</td></tr>';
		}
	}

	print '<tr><td>'.$langs->trans("Aditionaltime").'</td><td>';
	print $object->additional_time.' '.$langs->trans('Minutes');
	print '</td></tr>';
	print '<tr><td>'.$langs->trans("Statut").'</td><td>';
	print $object->getLibStatut();
	print '</td></tr>';

	print '</table>'."\n";


	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->assistance->def->mod && $object->statut == 0)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->assistance->def->del && $object->statut == 0)
		{
			if ($conf->use_javascript_ajax && !empty($conf->dol_use_jmobile))
		// We can't use preloaded confirm form with jmobile
			{
				print '<div class="inline-block divButAction"><span id="action-delete" class="butActionDelete">'.$langs->trans('Delete').'</span></div>'."\n";
			}
			else
			{
				print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
			}
		}
		if ($user->rights->assistance->def->val)
		{
			if ($object->statut == 1)
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=novalidate">'.$langs->trans('Novalidate').'</a></div>'."\n";
			else
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=validate">'.$langs->trans('Validate').'</a></div>'."\n";
		}
	}
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	//$somethingshown=$form->showLinkedObjectBlock($object);
	//$linktoelem = $form->showLinkToObjectBlock($object);
	//if ($linktoelem) print '<br>'.$linktoelem;
	include DOL_DOCUMENT_ROOT.'/assistance/tpl/membertypemarking_list.tpl.php';

}


// End of page
llxFooter();
$db->close();
