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
 *   	\file       budget/incidents_card.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-05-11 09:29
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
dol_include_once('/budget/class/incidents.class.php');
dol_include_once('/budget/class/incidentsdetext.class.php');
dol_include_once('/budget/class/incidentsres.class.php');
dol_include_once('/budget/lib/budget.lib.php');
dol_include_once('/orgman/class/cregiongeographic.class.php');
dol_include_once('/user/class/user.class.php');

// Load traductions files requiredby by page
$langs->load("budget");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$idr		= GETPOST('idr','int');
$action		= GETPOST('action','alpha');
$subaction		= GETPOST('subaction','alpha');
$typetwo		= GETPOST('typetwo','alpha');
$cancel     = GETPOST('cancel');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_label=GETPOST('search_label','alpha');
$search_fk_region=GETPOST('search_fk_region','int');
$search_incident=GETPOST('search_incident','alpha');
$search_active=GETPOST('search_active','int');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_status=GETPOST('search_status','int');



if (empty($action) && empty($id) && empty($ref)) $action='view';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
//$result = restrictedArea($user, 'budget', $id);


$object = new Incidents($db);
$extrafields = new ExtraFields($db);
$objCregiongeographic = new Cregiongeographic($db);
$objUser = new User($db);
$objIncident = new Incidents($db);
$objIncidentsdet = new Incidentsdetext($db);
$objIncidentsdettmp = new Incidentsdetext($db);
$objIncidentsres = new Incidentsres($db);
// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('incidents'));

$aGroupdet['costmo']=0;


/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/
$now = dol_now();
$type='costmo';

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	if ($cancel)
	{
		if ($action != 'addlink')
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/incidents/'.$type.'.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}
		if ($idr > 0) $ret = $objIncidentsdet->fetch($idr);
		$action='';
	}
	include DOL_DOCUMENT_ROOT.'/budget/incidents/include/crud.inc.php';

}



//armamos option para cregiongeographic

//armamos las regiones en un array
$filter='';
$res = $objCregiongeographic->fetchAll('ASC','t.label',0,0,array('status'=>1),'AND,$filter');
if ($res>0)
{
	$lines = $objCregiongeographic->lines;
	foreach ($lines AS $j => $line)
		$aRegiongeographic[$line->id] = $line->label.' ('.$line->ref.')';
}
//recuperamos que se tiene registrado para que no se duplique
$filter = " AND t.fk_incident = ".$object->id;
$filter.= " AND t.type = 'costmo'";
$res = $objIncidentsdettmp->fetchAll('','',0,0,array(),'AND',$filter);
$aDef = array();
if ($res>0)
{
	$lines = $objIncidentsdettmp->lines;
	foreach ($lines AS $j => $line)
	{
		$aDef[$line->label] = $line->label;
	}
}
//para esta opcion vamos a buscar los registros en mano de obra (occupational)

//para esta opcion vamos a buscar los registros en mano de obra (politic)
$filter = " AND t.fk_region = ".$object->fk_region;
$filter.= " AND t.code_parameter = 'BENESOC'";
$res = $objIncident->fetchAll('','',0,0,array(),'AND',$filter,true);
$nBenesoc = 0;

$aPolitic= array();
$aPoliticsalarymonth=array();
$aOccupational=array();
$nOccupational=0;
if ($res==1)
{
	$fk_incident= $objIncident->id;
	$nBenesoc=$objIncident->incident;
	//existe y buscamos
	$filter = " AND t.fk_incident = ".$fk_incident;
	$filter.= " AND t.type = 'politic'";
	$res = $objIncidentsdettmp->fetchAll('ASC','t.label',0,0,array(),'AND',$filter);
	if ($res >0)
	{
		$lines = $objIncidentsdettmp->lines;
		foreach ($lines AS $j => $line)
		{
			$aPoliticsalarymonth[$line->label] = $line->value_two;
			if (!$aDef[$line->label]) $aPolitic[$line->label] = $line->label;
		}
	}
	$filter = " AND t.fk_incident = ".$fk_incident;
	$filter.= " AND t.type = 'occupational'";
	$res = $objIncidentsdettmp->fetchAll('ASC','t.label',0,0,array(),'AND',$filter);
	if ($res >0)
	{
		$lines = $objIncidentsdettmp->lines;
		foreach ($lines AS $j => $line)
		{
			$aOccupational[$line->label] = $line->res_two;
			$nOccupational+= $line->res_two;
		}
	}
}
else
{
	$error++;
	setEventMessages($langs->trans('Thecompanypolicyisnotdefinedcorrectly'),null,'errors');
}

/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('',$langs->trans(ucwords($type)),'');

$form=new Form($db);


// Put here content of your page




// Part to show record
if ($object->id > 0)
{
	$res = $object->fetch_optionals($object->id, $extralabels);

	$head = incidents_prepare_head($object,$user);

	dol_fiche_head($head, $type, $langs->trans("Incidents"),0,'incidents');

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id.'&idr='.$idr.'&type='.$type, $langs->trans('Deleteincidents'), $langs->trans('ConfirmDeleteincidents'), 'confirm_delete', '', 0, 2);
		print $formconfirm;
	}

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>'.$object->label.'</td></tr>';
	//
	print '<tr><td>'.$langs->trans("Fieldref").'</td><td>'.$object->ref.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldlabel").'</td><td>'.$object->label.'</td></tr>';
	$res = $objCregiongeographic->fetch($object->fk_region);
	if ($res == 1)
	{
		print '<tr><td>'.$langs->trans("Fieldfk_region").'</td><td>'.$objCregiongeographic->getNomUrl().'</td></tr>';
	}
	//print '<tr><td>'.$langs->trans("Fieldincident").'</td><td>'.price($object->incident).'</td></tr>';
	//print '<tr><td>'.$langs->trans("Fieldactive").'</td><td>'.($object->active?$langs->trans('Yes'):$langs->trans('Not')).'</td></tr>';
	//$objUser->fetch($object->fk_user_create);
	//print '<tr><td>'.$langs->trans("Fieldfk_user_create").'</td><td>'.$objUser->getNomUrl(1).'</td></tr>';
	//$objUser->fetch($object->fk_user_mod);
	//print '<tr><td>'.$langs->trans("Fieldfk_user_mod").'</td><td>'.$objUser->getNomUrl(1).'</td></tr>';
	//print '<tr><td>'.$langs->trans("Fieldstatus").'</td><td>'.$object->getLibStatut(6).'</td></tr>';

	print '</table>';

	dol_fiche_end();

	include DOL_DOCUMENT_ROOT.'/budget/incidents/tpl/incidentsdet_list.tpl.php';
	$partial = $aTotal['value_seven'];

	//vamos a armar los resumenes
	print '<table class="border centpercent">'."\n";
	print '<tr><td>'.$langs->trans("Fieldlabel").'</td>';
	print '<td>'.$langs->trans("Fieldday").'</td>';
	print '</tr>';
	$incident=$partial;

	$var= !$var;
	print '<tr '.$bc[$var].'>';
	print '<td>'.$langs->trans('Fieldincidentby').' '.$langs->trans(ucwords($type)).'</td>';
	print '<td align="right">'.price(price2num($incident,'MT')).'</td>';
	print '</tr>';
	print '</table>';
	if ($object->status==0 && $abc)
	{
		if (!$error)
		{
			//vamos a crear o actualziar el registro resumen
			$filter = " AND t.fk_incident = ".$object->id;
			$filter.= " AND t.type = '".$type."'";
			$res = $objIncidentsres->fetchAll('','',0,0,array(),'AND',$filter,true);
			$lAdd=true;
			if ($res == 1) $lAdd=false;
			$objIncidentsres->fk_incident = $object->id;
			$objIncidentsres->type = $type;
			//1 es para gastos de licitacion y contratacion
			$objIncidentsres->group_det = $aGroupdet[$type];
			$objIncidentsres->incident=$incident;

			$objIncidentsres->fk_user_mod=$user->id;

			$objIncidentsres->datem=$now;
			$objIncidentsres->tms=$now;
			$objIncidentsres->status=1;
			if ($lAdd)
			{
				$objIncidentsres->fk_user_create=$user->id;
				$objIncidentsres->datec=$now;
				$res = $objIncidentsres->create($user);
			}
			else $res = $objIncidentsres->update($user);
			if ($res <=0)
			{
				$error++;
				setEventMessages($object->error,$object->errors,'errors');
			}

		}
	}

}


// End of page
llxFooter();
$db->close();
