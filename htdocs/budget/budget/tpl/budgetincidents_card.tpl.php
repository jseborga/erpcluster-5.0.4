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
 *   	\file       budget/budgetincidents_card.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-05-23 09:41
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


$search_entity=GETPOST('search_entity','int');
$search_fk_budget=GETPOST('search_fk_budget','int');
$search_ref=GETPOST('search_ref','alpha');
$search_label=GETPOST('search_label','alpha');
$search_code_parameter=GETPOST('search_code_parameter','alpha');
$search_fk_region=GETPOST('search_fk_region','int');
$search_day_year=GETPOST('search_day_year','int');
$search_day_efective=GETPOST('search_day_efective','int');
$search_day_journal=GETPOST('search_day_journal','int');
$search_day_num=GETPOST('search_day_num','int');
$search_salary_min=GETPOST('search_salary_min','alpha');
$search_njobs=GETPOST('search_njobs','int');
$search_cost_direct=GETPOST('search_cost_direct','alpha');
$search_time_duration=GETPOST('search_time_duration','int');
$search_exchange_rate=GETPOST('search_exchange_rate','alpha');
$search_ponderation=GETPOST('search_ponderation','alpha');
$search_commission=GETPOST('search_commission','alpha');
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

$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($objBudgetincidents->table_element);

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('budgetincidents'));


$aGroupdet['copiesplane']=1;
$aGroupdet['propossal']=1;
$aGroupdet['legaldoc']=1;
$aGroupdet['guarantees']=1;
$aGroupdet['operation']=2;
$aGroupdet['administrative']=2;
$aGroupdet['mobilization']=3;
$aGroupdet['traffic']=4;
$aGroupdet['risk']=4;
$aGroupdet['faenas']=5;


$aArray=array(1=>'Porcentaje',2=>'Num de salarios',3=>'Salario mínimo',4=>'Porcentaje de obreros beneficiados');
$aArrayadd=array(5=>'Monto anual Bs./Obrero');

//para esta opcion vamos a buscar los registros en mano de obra (politic)
$objBudgetincidentstmp = new Budgetincidentsext($db);
$objBudgetincidentsdettmp = new Budgetincidentsdet($db);
$filter = " AND t.fk_budget = ".$object->id;
$filter.= " AND t.fk_region = ".$object->fk_region;
$filter.= " AND t.code_parameter = 'BENESOC'";
$res = $objBudgetincidentstmp->fetchAll('','',0,0,array(),'AND',$filter,true);
$nBenesoc = 0;

$aPolitic= array();
$aPoliticsalarymonth=array();
$aOccupational=array();
$nOccupational=0;
$nBenesoc=0;
if ($res==1)
{
	$fk_incident= $objBudgetincidentstmp->id;
	$nBenesoc=$objBudgetincidentstmp->incident;
	//existe y buscamos
	$filter = " AND t.fk_budget_incident = ".$fk_incident;
	$filter.= " AND t.type = 'politic'";
	$res = $objBudgetincidentsdettmp->fetchAll('ASC','t.label',0,0,array(),'AND',$filter);
	if ($res >0)
	{
		$lines = $objBudgetincidentsdettmp->lines;
		foreach ($lines AS $j => $line)
		{
			$aPoliticsalarymonth[$line->label] = $line->value_two;
			if (!$aDef[$line->label]) $aPolitic[$line->label] = $line->label;
		}
	}
	$filter = " AND t.fk_budget_incident = ".$fk_incident;
	$filter.= " AND t.type = 'occupational'";
	$res = $objBudgetincidentsdettmp->fetchAll('ASC','t.label',0,0,array(),'AND',$filter);
	if ($res >0)
	{
		$lines = $objBudgetincidentsdettmp->lines;
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


// Put here content of your page

if ($action == 'select')
{
	//vamos a seleccionar que incidentas se tiene por defecto y para la region
	$filter = " AND t.fk_region = ".$object->fk_region;
	$res = $objIncidents->fetchAll('ASC','t.ref',0,0,array(),'AND',$filter);
	if ($res >0)
	{
		$options = "";
		$lines = $objIncidents->lines;
		foreach ($lines AS $j => $line)
		{
			$options.= '<option value="'.$line->id.'">'.$line->label.' ('.$line->ref.')'.'</option>';
		}
		print load_fiche_titre($langs->trans("NewMyModule"));

		print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
		print '<input type="hidden" name="action" value="import">';
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
		print '<input type="hidden" name="id" value="'.$object->id.'">';

		dol_fiche_head();

		print '<table class="border centpercent">'."\n";
		// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
		//
		print '<tr><td class="fieldrequired">'.$langs->trans("Selectincident").'</td><td>';
		print '<select name="fk_incident">'.$options.'</select>';
		print '</td></tr>';
		print '</table>';

		dol_fiche_end();

		print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Import").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

		print '</form>';

	}
}
// Part to create
if (empty($idr) && $action == 'create')
{
	print load_fiche_titre($langs->trans("NewMyModule"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.GETPOST('ref').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.GETPOST('label').'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldcode_parameter").'</td><td>';
	print $form->selectarray('code_parameter',$aParameter,GETPOST('code_parameter'));
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_region").'</td><td>';
	print $aRegiongeographic[$object->fk_region];
	print '<input class="flat" type="hidden" name="fk_region" value="'.$object->fk_region.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldday_year").'</td><td><input class="flat" type="number" min="0" max="365" name="day_year" value="'.GETPOST('day_year').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldday_efective_month").'</td><td><input class="flat" type="number" min="1" max="30" name="day_efective_month" value="'.GETPOST('day_efective_month').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldday_journal").'</td><td><input class="flat" type="number" min="0" max="30" name="day_journal" value="'.$objBudgetincidents->day_journal.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldday_num").'</td><td><input class="flat" type="number" name="day_num" min="1" max="30" value="'.GETPOST('day_num').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldsalary_min").'</td><td><input class="flat" type="number" min="0" step="any" name="salary_min" value="'.GETPOST('salary_min').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnjobs").'</td><td><input class="flat" type="number" min="0" name="njobs" value="'.GETPOST('njobs').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_direct").'</td><td><input class="flat" type="number" min="0" step="any" name="cost_direct" value="'.GETPOST('cost_direct').'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldtime_duration").'</td><td><input class="flat" type="number" min="0"  name="time_duration" value="'.GETPOST('time_duration').'"> Años</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldexchange_rate").'</td><td><input class="flat" type="number" min="0" step="any" name="exchange_rate" value="'.GETPOST('exchange_rate').'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldtva_tx").'</td><td><input class="flat" type="number" min="0" step="any" max="100" name="tva_tx" value="'.GETPOST('tva_tx').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtime_duration").'</td><td><input class="flat" type="number" min="0" name="time_duration" value="'.GETPOST('time_duration').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldexchange_rate").'</td><td><input class="flat" type="number" min="0" step="any" name="exchange_rate" value="'.GETPOST('exchange_rate').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldponderation").'</td><td><input class="flat" type="number" min="0" step="any" name="ponderation" value="'.GETPOST('ponderation').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcommission").'</td><td><input class="flat" type="number" min="0" step="any" max="100" name="commission" value="'.GETPOST('commission').'"></td></tr>';



	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}



// Part to edit record
if (($idr) && $action == 'edit')
{
	print load_fiche_titre($langs->trans("MyModule"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	print '<input type="hidden" name="idr" value="'.$objBudgetincidents->id.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.$objBudgetincidents->ref.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.$objBudgetincidents->label.'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldcode_parameter").'</td><td>'.$objBudgetincidents->code_parameter.'<input class="flat" type="hidden" name="code_parameter" value="'.$objBudgetincidents->code_parameter.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_region").'</td><td>';
	print $aRegiongeographic[$objBudgetincidents->fk_region];
	print '<input class="flat" type="hidden" name="fk_region" value="'.$objBudgetincidents->fk_region.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldday_year").'</td><td><input class="flat" type="number" min="0" max="365" name="day_year" value="'.$objBudgetincidents->day_year.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldday_efective_month").'</td><td><input class="flat" type="number" min="1" max="30" name="day_efective_month" value="'.$objBudgetincidents->day_efective_month.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldday_journal").'</td><td><input class="flat" type="number" min="0" max="365" name="day_journal" value="'.$objBudgetincidents->day_journal.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldday_num").'</td><td><input class="flat" type="number" name="day_num" min="1" max="30" value="'.$objBudgetincidents->day_num.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldsalary_min").'</td><td><input class="flat" type="number" min="0" step="any" name="salary_min" value="'.$objBudgetincidents->salary_min.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnjobs").'</td><td><input class="flat" type="number" min="0" name="njobs" value="'.$objBudgetincidents->njobs.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_direct").'</td><td><input class="flat" type="number" min="0" step="any" name="cost_direct" value="'.$objBudgetincidents->cost_direct.'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldtime_duration").'</td><td><input class="flat" type="number" min="0"  name="time_duration" value="'.(GETPOST('time_duration')?GETPOST('time_duration'):$objBudgetincidents->time_duration).'"> Años</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldexchange_rate").'</td><td><input class="flat" type="number" min="0" step="any" name="exchange_rate" value="'.(GETPOST('exchange_rate')?GETPOST('exchange_rate'):$objBudgetincidents->exchange_rate).'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldtva_tx").'</td><td><input class="flat" type="number" min="0" step="any" max="100" name="tva_tx" value="'.(GETPOST('tva_tx')?GETPOST('tva_tx'):$objBudgetincidents->tva_tx).'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtime_duration").'</td><td><input class="flat" type="number" min="0" name="time_duration" value="'.$objBudgetincidents->time_duration.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldexchange_rate").'</td><td><input class="flat" type="number" min="0" step="any" name="exchange_rate" value="'.$objBudgetincidents->exchange_rate.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldponderation").'</td><td><input class="flat" type="number" min="0" step="any" name="ponderation" value="'.$objBudgetincidents->ponderation.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcommission").'</td><td><input class="flat" type="number" min="0" step="any" max="100" name="commission" value="'.$objBudgetincidents->commission.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td><input class="flat" type="text" name="entity" value="'.$objBudgetincidents->entity.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_budget").'</td><td><input class="flat" type="text" name="fk_budget" value="'.$objBudgetincidents->fk_budget.'"></td></tr>';

	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldincident").'</td><td><input class="flat" type="text" name="incident" value="'.$objBudgetincidents->incident.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td><input class="flat" type="text" name="active" value="'.$objBudgetincidents->active.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.$objBudgetincidents->fk_user_create.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.$objBudgetincidents->fk_user_mod.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.$objBudgetincidents->status.'"></td></tr>';

	print '</table>';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}


// Part to show record
if ($objBudgetincidents->id > 0 && $action!='edit' && $action !='create')
{
	$res = $object->fetch_optionals($object->id, $extralabels);
	$head = budgetincidents_prepare_head($objBudgetincidents,$user);
	if (empty($type)) $type='card';
	dol_fiche_head($head, $type, $langs->trans("Incidents"),0,'incidents');

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $objBudgetincidents->id, $langs->trans('DeleteMyOjbect'), $langs->trans('ConfirmDeleteMyObject'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>'.$objBudgetincidents->label.'</td></tr>';
	//
	print '<tr><td>'.$langs->trans("Fieldref").'</td><td>'.$objBudgetincidents->ref.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldlabel").'</td><td>'.$objBudgetincidents->label.'</td></tr>';
	if ($type == 'card')
	{
		print '<tr><td>'.$langs->trans("Fieldcode_parameter").'</td><td>'.$objBudgetincidents->code_parameter.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldfk_region").'</td><td>'.$objBudgetincidents->fk_region.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldday_year").'</td><td>'.$objBudgetincidents->day_year.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldday_efective_month").'</td><td>'.$objBudgetincidents->day_efective_month.'</td></tr>';
		//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldday_journal").'</td><td>'.$objBudgetincidents->day_journal.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldday_num").'</td><td>'.$objBudgetincidents->day_num.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldsalary_min").'</td><td>'.$objBudgetincidents->salary_min.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldnjobs").'</td><td>'.$objBudgetincidents->njobs.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldcost_direct").'</td><td>'.$objBudgetincidents->cost_direct.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldtime_duration").'</td><td>'.$objBudgetincidents->time_duration.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldexchange_rate").'</td><td>'.$objBudgetincidents->exchange_rate.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldtva_tx").'</td><td>'.$objBudgetincidents->tva_tx.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldponderation").'</td><td>'.$objBudgetincidents->ponderation.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldcommission").'</td><td>'.$objBudgetincidents->commission.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldincident").'</td><td>'.$objBudgetincidents->incident.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldactive").'</td><td>'.($objBudgetincidents->active?$langs->trans('Yes'):$langs->trans('Not')).'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldstatus").'</td><td>'.$objBudgetincidents->getLibStatut(6).'</td></tr>';
	}
	print '</table>';

	dol_fiche_end();

	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);
	 // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->budget->bud->val)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&idr='.$objBudgetincidents->id.'&amp;action=validate">'.$langs->trans("Validate").'</a></div>'."\n";
		}
		if ($user->rights->budget->bud->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&idr='.$objBudgetincidents->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->budget->bud->del)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&idr='.$objBudgetincidents->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
		}
	}
	print '</div>'."\n";


	if ($type!='card')
	{
		include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/incidentsdet_list.tpl.php';
		if ($type=='subsidies')
		{
			$partial = $aTotal['value_four'];
			unset($aTotal);
			$typetwo='politic';
			include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/incidentsdet_list.tpl.php';
		}
		if ($type == 'occupational')
		{
			$partial = $aTotal['value_two'];
			unset($aTotal);
			$typetwo='security';
			include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/incidentsdet_list.tpl.php';
		}
	}
	else
		include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/incidentsres_list.tpl.php';

	if ($objBudgetincidents->code_parameter=='BENESOC')
	{
		if ($type=='inactivity')
		{
			print '<table class="border centpercent">'."\n";
			print '<tr><td>'.$langs->trans("Fieldlabel").'</td>';
			print '<td>'.$langs->trans("Fieldday").'</td>';
			print '</tr>';
			$var= !$var;
			print '<tr '.$bc[$var].'>';
			print '<td>'.$langs->trans('Fieldday_year').'</td>';
			print '<td align="right">'.$objBudgetincidents->day_year.'</td>';
			print '</tr>';
			$var= !$var;
			print '<tr '.$bc[$var].'>';
			print '<td>'.$langs->trans('Fieldday_efective').'</td>';
			$day_efective = $objBudgetincidents->day_year - $aTotal['value_one'];
			print '<td align="right">'.$day_efective.'</td>';
			print '</tr>';
			$var= !$var;
			print '<tr '.$bc[$var].'>';
			print '<td>'.$langs->trans('Fieldday_journal').'</td>';
			$day_journal = $day_efective + $aTotal['value_two'];
			print '<td align="right">'.$day_journal.'</td>';
			print '</tr>';

			$var= !$var;
			print '<tr '.$bc[$var].'>';
			print '<td>'.$langs->trans('Fieldincident').'</td>';
			$incident = 0;
			if ($day_efective > 0)
				$incident = $day_journal / $day_efective;
			if ($incident>0) $incident = ($incident-1)*100;

			print '<td align="right">'.price(price2num($incident,'MT')).' %</td>';
			print '</tr>';
			print '</table>';
		}
		if ($type == 'benefits')
		{
	//vamos a armar los resumenes
			print '<table class="border centpercent">'."\n";
			print '<tr><td>'.$langs->trans("Fieldlabel").'</td>';
			print '<td>'.$langs->trans("Fieldday").'</td>';
			print '</tr>';
			$var= !$var;
			print '<tr '.$bc[$var].'>';
			print '<td>'.$langs->trans('Fieldday_year').'</td>';
			print '<td align="right">'.$objBudgetincidents->day_year.'</td>';
			print '</tr>';
			$var= !$var;
			print '<tr '.$bc[$var].'>';
			print '<td>'.$langs->trans('Fieldday_efective').'</td>';
			print '<td align="right">'.$objBudgetincidents->day_efective.'</td>';
			print '</tr>';

			$var= !$var;
			print '<tr '.$bc[$var].'>';
			print '<td>'.$langs->trans('Fieldincidentby').' '.$langs->trans($type).'</td>';
			$incident = 0;
			if ($objBudgetincidents->day_efective > 0)
				$incident = $aTotal['value_two'] / $objBudgetincidents->day_efective * 100;

			print '<td align="right">'.price(price2num($incident,'MT')).' %</td>';
			print '</tr>';
			print '</table>';
		}
		if ($type == 'subsidies')
		{
				//vamos a armar los resumenes
			print '<table class="border centpercent">'."\n";
			print '<tr><td>'.$langs->trans("Fieldlabel").'</td>';
			print '<td>'.$langs->trans("Fieldday").'</td>';
			print '</tr>';
			$var= !$var;
			print '<tr '.$bc[$var].'>';
			print '<td>'.$langs->trans('Partial').'</td>';
			print '<td align="right">'.$partial.'</td>';
			print '</tr>';

			$totaltwo=$aTotal['value_four']*12;
			$var= !$var;
			print '<tr '.$bc[$var].'>';
			print '<td>'.$langs->trans('Fieldtotalyear').'</td>';
			print '<td align="right">'.$totaltwo.'</td>';
			print '</tr>';

			$incident=0;
			if ($totaltwo>0)
				$incident=$partial /$totaltwo*100;

			$var= !$var;
			print '<tr '.$bc[$var].'>';
			print '<td>'.$langs->trans('Fieldincidentby').' '.$langs->trans($type).' '.$aTotal['value_two'].'</td>';

			print '<td align="right">'.price(price2num($incident,'MT')).' %</td>';
			print '</tr>';
			print '</table>';
		}
		if ($type=='contribution')
		{
				//vamos a armar los resumenes
			print '<table class="border centpercent">'."\n";
			print '<tr><td>'.$langs->trans("Fieldlabel").'</td>';
			print '<td>'.$langs->trans("Fieldvalue").'</td>';
			print '</tr>';

			$incident=$aTotal['value_one'];

			$var= !$var;
			print '<tr '.$bc[$var].'>';
			print '<td>'.$langs->trans('Fieldincidentby').' '.$langs->trans($type).'</td>';

			print '<td align="right">'.price(price2num($incident,'MT')).' %</td>';
			print '</tr>';
			print '</table>';

		}
		if ($type=='antiquity')
		{
				//vamos a armar los resumenes
			print '<table class="border centpercent">'."\n";
			print '<tr><td>'.$langs->trans("Fieldlabel").'</td>';
			print '<td>'.$langs->trans("Fieldvalue").'</td>';
			print '</tr>';

	//vmos a realizar el calculo
	//D106/100*D107*D108*D109/100*12
			$amountanual= $aTotalant[1]/100*$aTotalant[2]*$aTotalant[3]*$aTotalant[4]*12;
			if ($objBudgetincidents->ponderation>0)
				$incident=$amountanual / $objBudgetincidents->ponderation;

			$var= !$var;
			print '<tr '.$bc[$var].'>';
			print '<td>'.$langs->trans('Fieldincidentby').' '.$langs->trans(ucwords($type)).'</td>';

			print '<td align="right">'.price(price2num($incident,'MT')).' %</td>';
			print '</tr>';
			print '</table>';

		}
		if ($type == 'occupational')
		{
			$partialtwo = $aTotal['value_four'];
				//vamos a armar los resumenes
			$ponderationmonth=0;
			if ($objBudgetincidents->ponderation>0)
				$ponderationmonth = $objBudgetincidents->ponderation/12;
			print '<table class="border centpercent">'."\n";
			print '<tr><td>'.$langs->trans("Fieldlabel").'</td>';
			print '<td>'.$langs->trans("Fieldday").'</td>';
			print '</tr>';
			$var= !$var;
			print '<tr '.$bc[$var].'>';
			print '<td>'.$langs->trans('Occupational').'</td>';
			print '<td align="right">'.price(price2num($partial,'MT')).'</td>';
			print '</tr>';
			$var= !$var;
			print '<tr '.$bc[$var].'>';
			print '<td>'.$langs->trans('Security').'</td>';
			print '<td align="right">'.price(price2num($partialtwo,'MT')).'</td>';
			print '</tr>';
			$partial+=$partialtwo;

			$var= !$var;
			print '<tr '.$bc[$var].'>';
			print '<td>'.$langs->trans('Salaryponderation').'</td>';
			print '<td align="right">'.price(price2num($ponderationmonth,'MT')).'</td>';
			print '</tr>';
			$incident=0;
			if ($ponderationmonth>0)
				$incident=$partial/$ponderationmonth*100;
			$var= !$var;
			print '<tr '.$bc[$var].'>';
			print '<td>'.$langs->trans('Fieldincidentby').' '.$langs->trans($type).'</td>';

			print '<td align="right">'.price(price2num($incident,'MT')).' %</td>';
			print '</tr>';
			print '</table>';
		}

	}
	if ($objBudgetincidents->code_parameter=='COSTMO')
	{
		if ($type=='costmo')
		{
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
		}
	}
	if ($objBudgetincidents->code_parameter=='HERMEN')
	{
		$partial = $aTotal['value_four'];

	//vamos a recuperar valores
		$nWeightsalarymonth=0;
	//vamos a recuperar de incidents para la region seleccionada
		$filter = " AND t.code_parameter= 'BENESOC'";
		$filter.= " AND t.fk_region = ".$object->fk_region;
		$filter.= " AND t.fk_budget = ".$object->id;
		$res = $objBudgetincidentstmp->fetchAll('','',0,0,array(),'AND',$filter,true);
		$search_fk = 0;
		if ($res==1) $search_fk = $objBudgetincidentstmp->id;
		$filter = " AND t.type='politic'";
		$filter.= " AND t.fk_budget_incident = ".$search_fk;
		$res = $objBudgetincidentsdettmp->fetchAll('','',0,0,array(),'AND',$filter);
		$suma=0;
		if ($res > 0)
		{
			$lines = $objBudgetincidentsdettmp->lines;
			foreach ($lines AS $j => $line)
				$suma+= $line->res_four;
		}
		$filter = " AND t.fk_budget_incident = ".$search_fk;
		$res = $objBudgetincidentsres->fetchAll('','',0,0,array(),'AND',$filter);
		$incident=0;
		if ($res > 0)
		{
			$lines = $objBudgetincidentsres->lines;
			foreach ($lines AS $j => $line)
				$incident+= $line->incident;
		}

	//recuperamos el promedio de la incidencia
	//vamos a armar los resumenes
		print '<table class="border centpercent">'."\n";
		print '<tr><td align="center">'.$langs->trans("Weightedsalarymonth").'</td>';
		print '<td align="center">'.$langs->trans("Incidents").'</td>';
		print '<td align="center">'.$langs->trans("Workforce").'</td>';
		print '<td align="center">'.$langs->trans("Annualamount").'</td>';
		print '</tr>';
		$var= !$var;
		print '<tr '.$bc[$var].'>';
		print '<td align="center">'.$suma.'</td>';
		print '<td align="center">'.$incident.'</td>';
		print '<td align="center">'.$objBudgetincidents->njobs.'</td>';
		$nAnnualamount= ($suma * ($incident/100) * 12 * $objBudgetincidents->njobs);
		print '<td align="center">'.price($nAnnualamount).'</td>';
		print '</tr>';
		$incident=0;
		if ($nAnnualamount>0)
			$incident= price2num($partial/$nAnnualamount * 100,'MT');
		$var= !$var;
		print '<tr '.$bc[$var].'>';
		print '<td colspan="2"  align="center">'.$langs->trans('Incidencia por herramientas menores').'</td>';
		print '<td  align="center">'.$incident.'</td>';
		print '</tr>';
		print '</table>';
	}
	if ($objBudgetincidents->code_parameter== 'GASGEN')
	{
		if ($type=='copiesplane') $partial = $aTotal['value_three'];
		if ($type == 'propossal') $partial = $aTotal['value_five'];
		if ($type == 'legaldoc') $partial = $aTotal['value_five'];
		if($type=='guarantees') $partial = $aTotal['value_five'];
		if ($type=='operation') $partial = $aTotal['value_five'];
		if($type=='administrative') $partial = $aTotal['value_five'];
		if($type=='mobilization') $partial = $aTotal['value_four'];
		if($type=='traffic') $partial = $aTotal['value_four'];
		if ($type=='risk') $partial = $aTotal['value_four'];
		if ($type=='faenas') $partial = $aTotal['value_four'];

		//vamos a armar los resumenes
		print '<table class="border centpercent">'."\n";
		print '<tr><td>'.$langs->trans("Fieldlabel").'</td>';
		print '<td>'.$langs->trans("Fieldday").'</td>';
		print '</tr>';
		$incident=$partial;
		$var= !$var;
		print '<tr '.$bc[$var].'>';
		print '<td>'.$langs->trans('Fieldincidentby').' '.$langs->trans(ucwords($type)).'</td>';
		print '<td align="right">'.price(price2num($incident,'MT')).' %</td>';
		print '</tr>';

	}
	if ($type!='card' && $objBudgetincidents->status==0)
	{
		if (!$error)
		{
			//vamos a crear o actualziar el registro resumen
			$filter = " AND t.fk_budget_incident = ".$idr;
			$filter.= " AND t.type = '".$type."'";
			$res = $objBudgetincidentsres->fetchAll('','',0,0,array(),'AND',$filter,true);
			$lAdd=true;
			if ($res == 1) $lAdd=false;
			$objBudgetincidentsres->fk_budget_incident = $objBudgetincidents->id;
			$objBudgetincidentsres->type = $type;
			//1 es para gastos de licitacion y contratacion
			$objBudgetincidentsres->group_det = $aGroupdet[$type]+0;
			$objBudgetincidentsres->incident=$incident;

			$objBudgetincidentsres->fk_user_mod=$user->id;

			$objBudgetincidentsres->datem=$now;
			$objBudgetincidentsres->tms=$now;
			$objBudgetincidentsres->status=1;
			if ($lAdd)
			{
				$objBudgetincidentsres->fk_user_create=$user->id;
				$objBudgetincidentsres->datec=$now;
				$res = $objBudgetincidentsres->create($user);
			}
			else $res = $objBudgetincidentsres->update($user);
			if ($res <=0)
			{
				$error++;
				setEventMessages($objBudgetincidentsres->error,$objBudgetincidentsres->errors,'errors');
			}
			if ($objBudgetincidents->code_parameter == 'BENESOC' || $objBudgetincidents->code_parameter == 'GASGEN')
			{
				$sumIncident=0;
				$filter = " AND t.fk_budget_incident = ".$objBudgetincidents->id;
				$res = $objBudgetincidentsres->fetchAll('','',0,0,array(),'AND',$filter);
				if ($res>0)
				{
					$linesres = $objBudgetincidentsres->lines;
					foreach ($linesres AS $j => $lineres)
						$sumIncident+= $lineres->incident;
				}
				//vamos a actualizar los valores
				if ($type=='subsidies') $objBudgetincidents->ponderation=$totaltwo+0;
				if ($type=='inactivity')
				{
					$objBudgetincidents->day_efective=$day_efective;
					$objBudgetincidents->day_journal = $day_journal;
				}
				$objBudgetincidents->incident = $sumIncident;
				$res = $objBudgetincidents->update($user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($object->error,$object->errors,'errors');
				}

			}
			if ($objBudgetincidents->code_parameter=='HERMEN')
			{
				//vamos a actualizar los valores
				$objBudgetincidents->incident=$incident+0;
				$res = $objBudgetincidents->update($user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($objBudgetincidents->error,$objBudgetincidents->errors,'errors');
				}
			}
		}
	}



}
