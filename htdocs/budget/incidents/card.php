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
dol_include_once('/budget/class/incidentsext.class.php');
dol_include_once('/budget/class/incidentsres.class.php');
dol_include_once('/budget/class/parametercalculation.class.php');
dol_include_once('/orgman/class/cregiongeographic.class.php');
dol_include_once('/user/class/user.class.php');
dol_include_once('/budget/lib/budget.lib.php');

// Load traductions files requiredby by page
$langs->load("budget");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
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
if (!$user->rights->budget->par->read)
{
	accessforbidden();
}
//$result = restrictedArea($user, 'budget', $id);


$object = new Incidentsext($db);
$extrafields = new ExtraFields($db);
$objCregiongeographic = new Cregiongeographic($db);
$objUser = new User($db);
$objParametercalculation = new Parametercalculation($db);
$objIncidentsres= new Incidentsres($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('incidents'));



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/
$now = dol_now();
$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	if ($cancel)
	{
		if ($action != 'addlink')
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/incidents/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}
		if ($id > 0 || ! empty($ref)) $ret = $object->fetch($id,$ref);
		$action='';
	}

	// Action to clone
	if ($action == 'confirm_clone')
	{
		$fk_region = GETPOST('fk_region','int');
		$db->begin();
		$res = $object->clone_item($user,$id,$fk_region);
		if ($res>0)
		{
			$db->commit();
			setEventMessages($langs->trans('Clonesucessfull'),null,'mesgs');
			header('location: '.$_SERVER['PHP_SELF'].'?id='.$res);
			exit;
		}
		else
			$db->rollback();
		$action = '';
	}

	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */

		$object->entity=$conf->entity;
		$object->ref=dol_string_nospecial(trim(dol_strtoupper(GETPOST('ref','alpha'))));
		$object->label=GETPOST('label','alpha');
		$object->fk_region=GETPOST('fk_region','int');
		$object->code_parameter=GETPOST('code_parameter','alpha');
		$object->day_year=GETPOST('day_year','int');
		$object->day_efective=GETPOST('day_efective','int');
		if (empty($object->day_efective)) $object->day_efective=0;
		$object->day_journal=GETPOST('day_journal','int');
		if (empty($object->day_journal)) $object->day_journal=0;
		$object->day_num=GETPOST('day_num','int');
		if (empty($object->day_num)) $object->day_num=0;
		$object->salary_min=GETPOST('salary_min','int');
		if (empty($object->salary_min)) $object->salary_min=0;
		$object->njobs=GETPOST('njobs','int');
		if (empty($object->njobs)) $object->njobs=0;
		$object->cost_direct=GETPOST('cost_direct','int');
		if (empty($object->cost_direct)) $object->cost_direct=0;
		$object->time_duration=GETPOST('time_duration','int');
		if (empty($object->time_duration)) $object->time_duration=0;
		$object->exchange_rate=GETPOST('exchange_rate','int');
		if (empty($object->exchange_rate)) $object->exchange_rate=0;
		$object->tva_tx=GETPOST('tva_tx','int');
		if (empty($object->tva_tx)) $object->tva_tx=0;
		$object->day_efective_month=GETPOST('day_efective_month','int');
		if (empty($object->day_efective_month)) $object->day_efective_month=0;
		$object->commission=GETPOST('commission','alpha');
		if (empty($object->commission)) $object->commission=0;
		$object->incident=GETPOST('incident','alpha');
		if (empty($object->incident)) $object->incident=0;
		$object->active=GETPOST('active','int');
		if (empty($object->active)) $object->active=0;
		$object->ponderation=0;
		$object->fk_user_create=$user->id;
		$object->fk_user_mod=$user->id;
		$object->datec = $now;
		$object->datem = $now;
		$object->tms = $now;
		$object->status=0;

		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}
		if ($object->fk_region <=0)
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldfk_region")), null, 'errors');
		}

		if (! $error)
		{
			$result=$object->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/incidents/card.php?id='.$result,1);
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

	// Action to update record
	if ($action == 'update')
	{
		$error=0;


		$object->entity=$conf->entity;
		$object->ref=dol_string_nospecial(trim(dol_strtoupper(GETPOST('ref','alpha'))));
		$object->label=GETPOST('label','alpha');
		$object->fk_region=GETPOST('fk_region','int');
		$object->day_year=GETPOST('day_year','int');
		$object->day_efective=GETPOST('day_efective','int');
		$object->code_parameter=GETPOST('code_parameter','alpha');
		if (empty($object->day_efective)) $object->day_efective=0;
		$object->day_journal=GETPOST('day_journal','int');
		if (empty($object->day_journal)) $object->day_journal=0;
		$object->day_num=GETPOST('day_num','int');
		if (empty($object->day_num)) $object->day_num=0;
		$object->salary_min=GETPOST('salary_min','int');
		if (empty($object->salary_min)) $object->salary_min=0;
		$object->njobs=GETPOST('njobs','int');
		if (empty($object->njobs)) $object->njobs=0;
		$object->cost_direct=GETPOST('cost_direct','int');
		if (empty($object->cost_direct)) $object->cost_direct=0;
		$object->time_duration=GETPOST('time_duration','int');
		if (empty($object->time_duration)) $object->time_duration=0;
		$object->exchange_rate=GETPOST('exchange_rate','int');
		if (empty($object->exchange_rate)) $object->exchange_rate=0;
		$object->tva_tx=GETPOST('tva_tx','int');
		if (empty($object->tva_tx)) $object->tva_tx=0;
		$object->day_efective_month=GETPOST('day_efective_month','int');
		if (empty($object->day_efective_month)) $object->day_efective_month=0;
		$object->commission=GETPOST('commission','alpha');
		if (empty($object->commission)) $object->commission=0;
		$object->incident=GETPOST('incident','alpha');
		if (empty($object->incident)) $object->incident=0;
		$object->active=GETPOST('active','int');
		if (empty($object->active)) $object->active=0;
		$object->fk_user_mod=$user->id;
		$object->datem = $now;
		$object->tms = $now;
		$object->status=0;


		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}
		if ($object->fk_region <=0)
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldfk_region")), null, 'errors');
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
			header("Location: ".dol_buildpath('/budget/incidents/list.php',1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else setEventMessages($object->error, null, 'errors');
		}
	}
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
//armamos los parametros de calculo
$filter='';
$res = $objParametercalculation->fetchAll('ASC','t.label',0,0,array('status'=>1),'AND,$filter');
if ($res>0)
{
	$lines = $objParametercalculation->lines;
	foreach ($lines AS $j => $line)
		$aParameter[$line->code] = $line->label;
}
$aParameter=array('BENESOC'=>$langs->trans('Socialbenefits'),'HERMEN'=>$langs->trans('Minortools'),'GASGEN'=>$langs->trans('Generalexpenses'),'COSTMO'=>$langs->trans('Laborcostdirecthours'));
/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('',$langs->trans('Incidents'),'');

$form=new Form($db);


// Put here content of your page


// Part to create
if ($action == 'create')
{
	print load_fiche_titre($langs->trans("NewMyModule"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.GETPOST('ref').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.GETPOST('label').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldparameter").'</td><td>';
	print $form->selectarray('code_parameter',$aParameter,GETPOST('code_parameter'),1);
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_region").'</td><td>';
	print $form->selectarray('fk_region',$aRegiongeographic,GETPOST('fk_region'),1);
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldday_year").'</td><td><input class="flat" type="number" min="1" max="366" name="day_year" value="'.GETPOST('day_year').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldday_efective_month").'</td><td><input class="flat" type="number" min="1" max="31" name="day_efective_month" value="'.GETPOST('day_efective_month').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldday_num").'</td><td><input class="flat" type="number" min="1" max="31" name="day_num" value="'.GETPOST('day_num').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldsalary_min").'</td><td><input class="flat" type="number" min="0"  step="any" name="salary_min" value="'.GETPOST('salary_min').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnjobs").'</td><td><input class="flat" type="number" min="0"  name="njobs" value="'.GETPOST('njobs').'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldcost_direct").'</td><td><input class="flat" type="number" min="0" step="any" name="cost_direct" value="'.GETPOST('cost_direct').'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldtime_duration").'</td><td><input class="flat" type="number" min="0"  name="time_duration" value="'.GETPOST('time_duration').'"> Años</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldexchange_rate").'</td><td><input class="flat" type="number" min="0" step="any" name="exchange_rate" value="'.GETPOST('exchange_rate').'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldtva_tx").'</td><td><input class="flat" type="number" min="0" step="any" name="tva_tx" value="'.GETPOST('tva_tx').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcommission").'</td><td><input class="flat len80" type="number" min="0" step="any" name="commission" value="'.GETPOST('commission').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldincident").'</td><td><input class="flat len80" type="number" min="0" step="any" name="incident" value="'.GETPOST('incident').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td>';
	print $form->selectyesno('active',GETPOST('active'),1);
	print '</td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}



// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	print load_fiche_titre($langs->trans("MyModule"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.$object->ref.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.$object->label.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldparameter").'</td><td>';
	print $form->selectarray('code_parameter',$aParameter,(GETPOST('code_parameter')?GETPOST('code_parameter'):$object->code_parameter),1);
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_region").'</td><td>';
	print $form->selectarray('fk_region',$aRegiongeographic,(GETPOST('fk_region')?GETPOST('fk_region'):$object->fk_region),1);
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldday_year").'</td><td><input class="flat" type="number" min="1" max="366" name="day_year" value="'.$object->day_year.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldday_efective_month").'</td><td><input class="flat" type="number" min="1" max="31" name="day_efective_month" value="'.GETPOST('day_efective_month').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldday_num").'</td><td><input class="flat" type="number" min="1" max="31" name="day_num" value="'.(GETPOST('day_num')?GETPOST('day_num'):$object->day_num).'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldsalary_min").'</td><td><input class="flat" type="number" min="0"  step="any" name="salary_min" value="'.(GETPOST('salary_min')?GETPOST('salary_min'):$object->salary_min).'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnjobs").'</td><td><input class="flat" type="number" min="0"  name="njobs" value="'.(GETPOST('njobs')?GETPOST('njobs'):$object->njobs).'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldcost_direct").'</td><td><input class="flat" type="number" min="0" step="any" name="cost_direct" value="'.(GETPOST('cost_direct')?GETPOST('cost_direct'):$object->cost_direct).'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldtime_duration").'</td><td><input class="flat" type="number" min="0"  name="time_duration" value="'.(GETPOST('time_duration')?GETPOST('time_duration'):$object->time_duration).'"> Años</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldexchange_rate").'</td><td><input class="flat" type="number" min="0" step="any" name="exchange_rate" value="'.(GETPOST('exchange_rate')?GETPOST('exchange_rate'):$object->exchange_rate).'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldtva_tx").'</td><td><input class="flat" type="number" min="0" step="any" name="tva_tx" value="'.(GETPOST('tva_tx')?GETPOST('tva_tx'):$object->tva_tx).'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcommission").'</td><td><input class="flat len80" type="number" min="0" step="any" name="commission" value="'.(GETPOST('commission')?GETPOST('commission'):$object->commission).'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldincident").'</td><td><input class="flat" type="text" name="incident" value="'.$object->incident.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td>';
	print $form->selectyesno('active',(GETPOST('active')?GETPOST('active'):$object->active),1);
	print '</td></tr>';

	print '</table>';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}



// Part to show record
if ($object->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create')))
{
	$res = $object->fetch_optionals($object->id, $extralabels);

	$head = incidents_prepare_head($object,$user);
	dol_fiche_head($head, 'card', $langs->trans("Incidents"),0,'incidents');


	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('DeleteMyOjbect'), $langs->trans('ConfirmDeleteMyObject'), 'confirm_delete', '', 1, 1);
		print $formconfirm;
	}

	if ($action == 'clone') {
		$formquestion = array(array('type'=>'select','label'=>$langs->trans('Fieldregion'),'name'=>'fk_region','values'=>$aRegiongeographic));

		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('Cloneincidents'), $langs->trans('ConfirmCloneincidents'), 'confirm_clone', $formquestion, 1, 2);
		print $formconfirm;
	}


	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>'.$object->label.'</td></tr>';
	//
	print '<tr><td>'.$langs->trans("Fieldref").'</td><td>'.$object->ref.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldlabel").'</td><td>'.$object->label.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldparameter").'</td><td>'.$aParameter[$object->code_parameter].'</td></tr>';
	$res = $objCregiongeographic->fetch($object->fk_region);
	if ($res == 1)
	{
		print '<tr><td>'.$langs->trans("Fieldfk_region").'</td><td>'.$objCregiongeographic->getNomUrl().'</td></tr>';
	}
	print '<tr><td>'.$langs->trans("Fieldday_year").'</td><td>'.$object->day_year.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldday_efective_month").'</td><td>'.$object->day_efective_month.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldday_journal").'</td><td>'.$object->day_journal.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldday_num").'</td><td>'.$object->day_num.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldsalary_min").'</td><td>'.price(price2num($object->salary_min,'MT')).'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldnjobs").'</td><td>'.$object->njobs.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldcost_direct").'</td><td>'.$object->cost_direct.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldtime_duration").'</td><td>'.$object->time_duration.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldexchange_rate").'</td><td>'.$object->exchange_rate.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldtva_tx").'</td><td>'.price2num($object->tva_tx,'MT').'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldcommission").'</td><td>'.$object->commission.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldincident").'</td><td>'.price($object->incident).'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldactive").'</td><td>'.($object->active?$langs->trans('Yes'):$langs->trans('Not')).'</td></tr>';
	$objUser->fetch($object->fk_user_create);
	print '<tr><td>'.$langs->trans("Fieldfk_user_create").'</td><td>'.$objUser->getNomUrl(1).'</td></tr>';
	$objUser->fetch($object->fk_user_mod);
	print '<tr><td>'.$langs->trans("Fieldfk_user_mod").'</td><td>'.$objUser->getNomUrl(1).'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldstatus").'</td><td>'.$object->getLibStatut(6).'</td></tr>';

	print '</table>';

	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->budget->par->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=clone">'.$langs->trans("Clone").'</a></div>'."\n";
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->budget->par->del)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
		}
	}
	print '</div>'."\n";

	include DOL_DOCUMENT_ROOT.'/budget/incidents/tpl/incidentsres_list.tpl.php';

}


// End of page
llxFooter();
$db->close();
