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
 *   	\file       /assistance_page.php
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
dol_include_once('/assistance/class/assistance.class.php');
dol_include_once('/assistance/class/typemarking.class.php');
dol_include_once('/assistance/class/html.formadd.class.php');
dol_include_once('/contact/class/contact.class.php');
dol_include_once('/adherents/class/adherent.class.php');

// Load traductions files requiredby by page
$langs->load("assistance");
$langs->load("companies");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');
$mc	= GETPOST('mc','alpha');
if (empty($mc))$mc='m';
$sortorder	= GETPOST('sortorder','alpha');
$sortfield	= GETPOST('sortfield','alpha');

$search_name=GETPOST('search_name','alpha');
$search_number=GETPOST('search_number','int');
if (isset($_GET['setdate']) && $_GET['setdate'] == 1)
	$search_date = GETPOST('search_date');
else
{
	//$search_date = dol_mktime($_POST['sehour'],$_POST['semin'],0,$_POST['semonth'],$_POST['seday'],$_POST['seyear'],'user');
	$search_date = dol_mktime(0,0,0,GETPOST('semonth'),GETPOST('seday'),GETPOST('seyear'),'user');
}
if (isset($_GET['search_name']))
	$_SESSION['search_name'] = GETPOST('search_name');
if (isset($_GET['search_number']))
	$_SESSION['search_number'] = GETPOST('search_number');
if ($search_date)
	$_SESSION['search_date'] = $search_date;
if (empty($_SESSION['search_date']))
	$_SESSION['search_date'] = dol_now();

$aMarking= array(1=>$langs->trans('Primaryentry'),
	2=>$langs->trans('Primaryexit'),
	3=>$langs->trans('Secundaryentry'),
	4=>$langs->trans('Secundaryexit'),
	5=>$langs->trans('Thirdentry'),
	6=>$langs->trans('Thirdexit'),
	);

// Purge criteria
if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter")) // Both test are required to be compatible with all browsers
{
	$search_all='';
	$_SESSION['search_name']="";
	$_SESSION['search_number']="";
	$_SESSION['search_date']="";
}

$search_name   = $_SESSION['search_name'];
$search_number = $_SESSION['search_number'];
$search_date   = $_SESSION['search_date'];


$aDate = dol_getdate($search_date);
$aDateAnt = dol_get_prev_day($aDate['mday'],$aDate['mon'],$aDate['year']);
//creamos los rangos de fecha

$search_dateini = dol_mktime(23,59,59,$aDateAnt['month'],$aDateAnt['day'],$aDateAnt['year'],'user');
$search_datefin = dol_mktime(23,59,59,$aDate['mon'],$aDate['mday'],$aDate['year'],'user');

//echo '<hr>'.dol_print_date($search_dateini,'dayhour').' '.dol_print_date($search_datefin,'dayhour');
// Protection if external user
if ($user->societe_id > 0)
{
	accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$typemar = new Typemarking($db);
$object=new Assistance($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}
// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('assistance'));
$extrafields = new ExtraFields($db);



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

// Part to create
if ($action == 'register')
{
	if ($user->fk_contact >0)
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/assistance/assistance.php',1);
		header("Location: ".$urltogo);
		exit;
	}
}
if (empty($reshook))
{
	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/assistance/assistance.php?mc='.$mc,1);
			header("Location: ".$urltogo);
			exit;
		}
		$timemax = $conf->global->ASSISTANCE_TIMEMAX_REGISTER_NEXT;
		$aDatehoy = dol_getdate(dol_now());
		$dHoy = $aDatehoy['mday'];
		$mHoy = $aDatehoy['mon'];
		$yHoy = $aDatehoy['year'];
		$hhHoy = $aDatehoy['hours'];
		$mmHoy = $aDatehoy['minutes'];
		$timetot = $hhHoy * 60 + $mmHoy;
		$error=0;
	//buscamos si existe registro con tiempo -5min
		if (empty($timemax)) $timemax = 5;
	//buscamos el ultimo registro de la persona
		$objectn = new Assistance($db);
		$idcontact = GETPOST('fk_contact','int')+0;
		$idmember  = GETPOST('fk_member','int')+0;
		$cm = $idcontact>0?'c':'m';
		if ($idmember>0)
			$objectn->fetchAll($sortorder='DESC', $sortfield='date_ass', $limit=20, $offset=0, array('fk_member'=>$idmember), '', $filtermode='AND');
		if ($idcontact>0)
			$objectn->fetchAll($sortorder='DESC', $sortfield='date_ass', $limit=20, $offset=0, array('fk_contact'=>$idcontact), $filtermode='AND');
		$lRegister = false;
		if (count($objectn->lines) >0)
		{
			$nRegister = 0;
		//verificamos cuantos registros del dia existen
			foreach ($objectn->lines AS $m => $objr)
			{
				$aDate = dol_getdate($objr->date_ass);
				if ($aDate['mday'] == $dHoy &&
					$aDate['mon'] == $mHoy &&
					$aDate['year'] == $yHoy)
					$nRegister++;
			}
			$nRegister++;
			$obj = $objectn->lines[0];
		//verificamos cuando se registro por ultima vez por member
			if ($cm == 'm')
			{
				if ($idmember && $obj->fk_member == $idmember)
				{
					$aDate = dol_getdate($obj->date_ass);
					if ($aDate['mday'] == $dHoy &&
						$aDate['mon'] == $mHoy &&
						$aDate['year'] == $yHoy)
					{
			//tiene marcado de hoy
			//verificamos la hora y min
						$timetotreg = $aDate['hours'] * 60 + $aDate['minutes'] + $timemax+1;
						if ($timetotreg >= $timetot)
						{
							$lRegister = false;
							$error++;
							setEventMessage($langs->trans("Thereisarecord",$langs->transnoentitiesnoconv("Members")),'errors');
						}
						else
							$lRegister = true;
					}
					else
					{
						$nRegister = 1;
						$lRegister = true;
					}
				}
				else
				{
					$nRegister = 1;
					$lRegister = true;
				}
			}

		}
		else
		{
			$nRegister = 1;
			$lRegister = true;
		}
		/* object_prop_getpost_prop */
		if ($lRegister)
		{

			$object->entity=$conf->entity;
			$object->fk_soc=GETPOST('fk_soc','int')+0;
			$object->fk_contact=GETPOST('fk_contact','int')+0;
			$object->fk_member=GETPOST('fk_member','int')+0;
			$object->date_ass=dol_now();
			$object->marking_number=$nRegister;
			$object->fk_user_create=$user->id;
			$object->fk_user_mod=$user->id;
			$object->date_create = dol_now();
			$object->tms = dol_now();
			$object->statut=1;
			if ($object->fk_member <=0)
			{
				$error++;
				setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Member")),'errors');
			}
			if ($object->fk_member <= 0 && $object->fk_contact <=0)
			{
				$error++;
				setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Contact")),'errors');
			}

			if (! $error)
			{
				$result=$object->create($user);
				if ($result > 0)
				{
			// Creation OK
					$urltogo=$backtopage?$backtopage:dol_buildpath('/assistance/assistance.php?mc='.$mc,1);
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
	}

	// Cancel
	if ($action == 'update' && GETPOST('cancel'))
	{
		$action='create';
	}

	// Action to update record
	if ($action == 'update' && ! GETPOST('cancel'))
	{
		$error=0;
		$date_ass = dol_mktime($_POST['date_hour'],$_POST['date_min'],0,$_POST['date_month'],$_POST['date_day'],$_POST['date_year'],'user');

		$object->fk_soc=GETPOST('fk_soc','int')+0;
		$object->fk_member=GETPOST('fk_member','int')+0;
		$object->fk_contact=GETPOST('fk_contact','int')+0;
		$object->marking_number=GETPOST('marking_number','int');
		$object->date_ass=$date_ass;
		$object->fk_user_mod=$user->id;
		$object->tms = dol_now();
		$object->statut=1;

		if (empty($object->fk_member) && empty($object->fk_contact))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Member")),null,'errors');
		}

		if (! $error)
		{
			if ($object->fk_member>0) $mc= 'm';
			if ($object->fk_contact>0) $mc= 'c';
			$result=$object->update($user);
			if ($result > 0)
			{
				$urltogo=$backtopage?$backtopage:dol_buildpath('/assistance/assistance.php?mc='.$mc,1);
				header("Location: ".$urltogo);
				exit;
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
	if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $object->statut == 1 )
	{
		if ($object->fk_member>0) $mc = 'm';
		if ($object->fk_contact>0) $mc = 'c';

		$result=$object->delete($user);
		if ($result > 0)
		{
		// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/assistance/assistance.php?mc='.$mc,1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null,$object->errors,'errors');
			else setEventMessages($object->error,null,'errors');
		}
	}
	// Cancel
	if ($_REQUEST["confirm"] == 'no')
	{
		if ($object->fk_member>0) $mc = 'm';
		if ($object->fk_contact>0) $mc = 'c';
		$action='list';
		$id=0;
	}
}



/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('','Assistance','');

$form=new Form($db);
$formadd=new Formadd($db);


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

// Part to create
if ($action == 'register')
{
	if (!$user->admin && $user->fk_member >0)
	{
		print_fiche_titre($langs->trans("Registro de asistencia"));

	// Confirm validate request
		if ($action == 'register')
		{
			if (empty($_SESSION['urlant']))
				$_SESSION['urlant'] = $_SERVER['PHP_SELF'];;
			$form = new Form($db);
			$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?fk_member=".$user->fk_member.'&backtopage='.$_SESSION['urlant'],$langs->trans("Assistance"),$langs->trans("Desea registrar su asistencia",$object->ref),"add",'',0,2);
			if ($ret == 'html') print '<br>';
		}
	}
}


// Part to create
if ($action == 'create' || empty($id)  && $action != 'register' &&
	($user->rights->assistance->mem->crear ||
		$user->rights->assistance->con->crear))
{
	print_fiche_titre($langs->trans("New"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="mc" value="'.$mc.'">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	print '<tr><td class="fieldrequired">'.$langs->trans("Name").'</td><td>';
	if ($mc=='m')
	{
		print $formadd->select_member($fk_member,'fk_member','',1,'','','','','autofocus');
	}
	if ($mc=='c')
	{
		print $form->select_contacts(0,$fk_contact,'fk_contact',1);
	}

	print '</td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> </div>';

	print '</form>';
	$action = 'list';
}

// Part to show a list
if ($action == 'list' || $action == 'delete' || (empty($id) && $action != 'register') )
{
	// Put here content of your page
	print load_fiche_titre($langs->trans('Dials'));
	// Confirm delete request
	if ($action == 'delete')
	{
		$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&mc=m',$langs->trans("Delete"),$langs->trans("Confirmdeleteassistance",$object->ref),"confirm_delete",'',0,2);
		if ($ret == 'html') print '<br>';
	}

	$sql = "SELECT";
	$sql.= " t.rowid,";

	$sql .= " t.entity,";
	$sql .= " t.fk_soc,";
	$sql .= " t.fk_member,";
	$sql .= " t.marking_number,";
	$sql .= " t.date_ass,";
	$sql .= " t.images,";
	$sql .= " t.fk_user_create,";
	$sql .= " t.fk_user_mod,";
	$sql .= " t.datec,";
	$sql .= " t.tms,";
	$sql .= " t.statut";


	// Add fields for extrafields
	foreach ($extrafields->attribute_list as $key => $val) $sql.=",ef.".$key.' as options_'.$key;
	// Add fields from hooks
	$parameters=array();
	$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
	$sql.=$hookmanager->resPrint;
	$sql.= " FROM ".MAIN_DB_PREFIX."assistance as t";
	if ($mc == 'm')
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."adherent AS a ON t.fk_member = a.rowid";
	if ($mc == 'c')
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."socpeople AS a ON t.fk_contact = a.rowid";

	$sql.= " WHERE 1 = 1";
	//filtro por fecha
	$sql.= " AND t.date_ass BETWEEN '".$db->idate($search_dateini)."' AND '".$db->idate($search_datefin)."'";
	if ($user->fk_member>0)
	{
		if (!$user->rights->assistance->con->leer)
			$sql.= " AND t.fk_member = ".$user->fk_member;
	}
	if ($search_name)
	{
		$sql.= " AND (a.lastname LIKE '%".$search_name."%'";
		$sql.= " OR a.firstname LIKE '%".$search_name."%'";
		if ($mc == 'm')
			$sql.= " OR a.login LIKE '%".$search_name."%'";
		$sql.= ")";
	}
	if ($search_number>0) $sql.= natural_search("marking_number",$search_number);

	// Add where from hooks
	$parameters=array();
	$reshook=$hookmanager->executeHooks('printFieldListWhere',$parameters);    // Note that $action and $object may have been modified by hook
	$sql.=$hookmanager->resPrint;
	// Count total nb of records
	$nbtotalofrecords = 0;
	if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
	{
		$result = $db->query($sql);
		$nbtotalofrecords = $db->num_rows($result);
	}
	if (empty($sortfield))
		$sortfield = 't.date_ass';
	if (empty($sortorder))
		$sortorder = 'DESC';
	$sql.= $db->order($sortfield, $sortorder);
	$sql.= $db->plimit($conf->liste_limit+1, $offset);
	dol_syslog($script_file, LOG_DEBUG);
	// $sql;
	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);

		$params='';
		$params.= '&amp;search_field1='.urlencode($search_field1);
		$params.= '&amp;search_field2='.urlencode($search_field2);

		print_barre_liste($title, $page, $_SERVER["PHP_SELF"],$params,$sortfield,$sortorder,'',$num,$nbtotalofrecords,'title_companies');


		print '<form method="GET" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
		print '<input type="hidden" name="mc" value="'.$mc.'">';
		if (! empty($moreforfilter))
		{
			print '<div class="liste_titre">';
			print $moreforfilter;
			$parameters=array();
		$reshook=$hookmanager->executeHooks('printFieldPreListTitle',$parameters);    // Note that $action and $object may have been modified by hook
		print $hookmanager->resPrint;
		print '</div>';
	}

	print '<table class="noborder centpercent">'."\n";

		// Fields title
	print '<tr class="liste_titre">';

	print_liste_field_titre($langs->trans('Name'),$_SERVER['PHP_SELF'],'t.fk_member','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Type'),$_SERVER['PHP_SELF'],'t.type_marking','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Date'),$_SERVER['PHP_SELF'],'t.date_ass','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('statut'),$_SERVER['PHP_SELF'],'t.statut','',$param,'align="right"',$sortfield,$sortorder);

	$parameters=array();
		$reshook=$hookmanager->executeHooks('printFieldListTitle',$parameters);    // Note that $action and $object may have been modified by hook
		print $hookmanager->resPrint;
		print '</tr>'."\n";

		// Fields title search
		print '<tr class="liste_titre">';
		print '<td class="liste_titre"><input type="text" class="flat" name="search_name" value="'.$search_name.'" size="10"></td>';
		print '<td class="liste_titre">';
		print $form->selectarray('search_number',$aMarking,$search_number,1);
		print '</td>';
		print '<td class="liste_titre">';
		print $form->select_date((empty($search_date)?dol_now():$search_date),'se',0,0,1,'search_date',1);
		print '</td>';
		print '<td align="right">';
		print '<input type="image" class="liste_titre" name="button_search" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
		print '<input type="image" class="liste_titre" name="button_removefilter" src="'.img_picto($langs->trans("RemoveFilter"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
		print '</td>';

		$parameters=array();
		$reshook=$hookmanager->executeHooks('printFieldListOption',$parameters);    // Note that $action and $object may have been modified by hook
		print $hookmanager->resPrint;
		print '</tr>'."\n";
		if (empty($search_date)) $search_date = dol_now();

		$i = 0;
		while ($i < $num)
		{
			$obj = $db->fetch_object($resql);
			if ($obj)
			{
				$lView = true;
				if ($search_date>0)
				{
					$aDateloc = dol_getdate($db->jdate($obj->date_ass));
					$aDate = dol_getdate($search_date);
					if ($aDateloc['mday'] == $aDate['mday'] &&
						$aDateloc['mon'] == $aDate['mon'] &&
						$aDateloc['year'] == $aDate['year'])
						$lView = true;
					else
						$lView = false;
				}
				if ($lView)
				{
			//buscamos
					if (!empty($obj->fk_contact))
					{
						$mc = 'c';
						$objs = new Contact($db);
						$objs->fetch($obj->fk_contact);
					}
					if (!empty($obj->fk_member))
					{
						$mc = 'm';
						$objs = new Adherent($db);
						$objs->fetch($obj->fk_member);
					}
					$typemar->fetch($obj->type_marking);
			// You can use here results
					print '<tr>';
					print '<td>'.$objs->lastname.' '.$objs->firstname.'</td>';
					print '<td>'.$aMarking[$obj->marking_number].'</td>';
					print '<td>'.dol_print_date($db->jdate($obj->date_ass),'dayhour').'</td>';
					print '<td align="right">';
					if ($user->rights->assistance->mon->mod || $user->rights->assistance->con->mod)
						print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$obj->rowid.'&action=edit">'.img_picto($langs->trans('Edit'),'edit').'</a>';
					print '&nbsp;';
					if ($user->rights->assistance->mon->del || $user->rights->assistance->con->del)
						print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$obj->rowid.'&action=delete&mc='.$mc.'">'.img_picto($langs->trans('Delete'),'delete').'</a>';
					else
						print '&nbsp;';
					print '</td>';


					$parameters=array('obj' => $obj);
			$reshook=$hookmanager->executeHooks('printFieldListValue',$parameters);    // Note that $action and $object may have been modified by hook
			print $hookmanager->resPrint;
			print '</tr>';
		}
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
		if ($user->fk_member>0)
		{

			print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
			print '<input type="hidden" name="action" value="register">';
			print '<input type="hidden" name="mc" value="'.$mc.'">';
			print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
			print '<input type="hidden" name="fk_member" value="'.$user->fk_member.'">';

			print '<div class="inline-block divButAction"><input type="submit" class="butAction" name="add" value="'.$langs->trans("Registerassistance").'"> </div>';

			print '</form>';

		}
		print '</div>'."\n";

	}
	else
	{
		$error++;
		dol_print_error($db);
	}
}

// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';

	dol_fiche_head();

	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';

	print '<table class="border centpercent">'."\n";
	print '<tr><td class="fieldrequired">'.$langs->trans("Name").'</td><td>';
	if ($object->fk_member>0)
	{
		print $formadd->select_member($object->fk_member,'fk_member','',1,'','','','','autofocus');
	}
	elseif ($object->fk_contact>0)
	{
		print $form->select_contacts(0,$object->fk_contact,'fk_contact',1);
	}

	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Date").'</td><td>';
	print $form->select_date($object->date_ass,'date_',1,1,0);
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Markingnumber").'</td><td>';
	print '<input type="number" min="0" max="10" name="marking_number" value="'.$object->marking_number.'">';
	print '</td></tr>';
	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Save").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}



// Part to show record
if ($id && (empty($action) || $action == 'view'))
{
	dol_fiche_head();



	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->assistance->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->assistance->delete)
		{
		if ($conf->use_javascript_ajax && empty($conf->dol_use_jmobile))	// We can't use preloaded confirm form with jmobile
		{
			print '<div class="inline-block divButAction"><span id="action-delete" class="butActionDelete">'.$langs->trans('Delete').'</span></div>'."\n";
		}
		else
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
		}
	}
}
print '</div>'."\n";
	// Example 2 : Adding links to objects
	//$somethingshown=$form->showLinkedObjectBlock($object);
	//$linktoelem = $form->showLinkToObjectBlock($object);
	//if ($linktoelem) print '<br>'.$linktoelem;
}
// End of page
llxFooter();
$db->close();
