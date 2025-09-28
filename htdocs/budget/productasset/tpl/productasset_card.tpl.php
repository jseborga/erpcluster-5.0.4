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
 *   	\file       budget/productasset_card.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-04-16 15:10
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


$search_fk_product=GETPOST('search_fk_product','int');
$search_cost_hour_productive=GETPOST('search_cost_hour_productive','alpha');
$search_cost_hour_improductive=GETPOST('search_cost_hour_improductive','alpha');
$search_cost_acquisition=GETPOST('search_cost_acquisition','alpha');
$search_engine_power=GETPOST('search_engine_power','alpha');
$search_fk_type_engine=GETPOST('search_fk_type_engine','int');
$search_cost_tires=GETPOST('search_cost_tires','alpha');
$search_useful_life_tires=GETPOST('search_useful_life_tires','alpha');
$search_useful_life_year=GETPOST('search_useful_life_year','alpha');
$search_useful_life_hours=GETPOST('search_useful_life_hours','alpha');
$search_percent_residual_value=GETPOST('search_percent_residual_value','alpha');
$search_percent_repair=GETPOST('search_percent_repair','alpha');
$search_percent_interest=GETPOST('search_percent_interest','alpha');
$search_diesel_consumption=GETPOST('search_diesel_consumption','alpha');
$search_diesel_lubricants=GETPOST('search_diesel_lubricants','alpha');
$search_gasoline_consumption=GETPOST('search_gasoline_consumption','alpha');
$search_gasoline_lubricants=GETPOST('search_gasoline_lubricants','alpha');
$search_cost_diesel=GETPOST('search_cost_diesel','alpha');
$search_cost_gasoline=GETPOST('search_cost_gasoline','alpha');
$search_energy_kw=GETPOST('search_energy_kw','alpha');
$search_cost_depreciation=GETPOST('search_cost_depreciation','alpha');
$search_cost_interest=GETPOST('search_cost_interest','alpha');
$search_cost_fuel_consumption=GETPOST('search_cost_fuel_consumption','alpha');
$search_cost_lubricants=GETPOST('search_cost_lubricants','alpha');
$search_cost_tires_replacement=GETPOST('search_cost_tires_replacement','alpha');
$search_cost_repair=GETPOST('search_cost_repair','alpha');
$search_cost_pu_improductive=GETPOST('search_cost_pu_improductive','alpha');
$search_cost_pu_productive=GETPOST('search_cost_pu_productive','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_status=GETPOST('search_status','int');

$lines = fetchAll_type_engine($db,'ASC', 't.label', 0, 0, array(), 'AND'," AND t.active = 1");
		//convertimos en array;
$aTypeengine = array();
if(is_array($lines) && count($lines)>0)
{
	foreach ($lines AS $j => $line)
		$aTypeengine[$line->id] = $line->label;
}


if (empty($action) && empty($id) && empty($ref)) $action='view';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
//$result = restrictedArea($user, 'budget', $id);

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('productasset'));

//vamos a realizar los calculos para el tab default
if ($objProductasset->useful_life_year>0 && $objProductasset->useful_life_hours)
	$objProductasset->cost_depreciation = (($objProductasset->cost_acquisition-$objProductasset->cost_tires)*(100-$objProductasset->percent_residual_value))/(100*$objProductasset->useful_life_year*$objProductasset->useful_life_hours);
else
	$objProductasset->cost_depreciation=0;
//=((H4-K4)*(100-O4))/(100*M4*N4)
if ($objProductasset->useful_life_year>0 && $objProductasset->percent_repair && $objProductasset->useful_life_hours)
	$objProductasset->cost_interest = ((($objProductasset->cost_acquisition-$objProductasset->cost_tires)*($objProductasset->useful_life_year+1))/(2*$objProductasset->useful_life_year))*$objProductasset->percent_interest/($objProductasset->useful_life_hours*100);
//=(((H4-K4)*(M4+1))/(2*M4))*Q4/(N4*100)
$objProductasset->cost_fuel_consumption = $objProductasset->diesel_consumption*$objProductasset->engine_power*$objProductasset->cost_diesel;
//=R4*I4*V4
$objProductasset->cost_lubricants = $objProductasset->diesel_lubricants*$objProductasset->engine_power*$objProductasset->cost_diesel;
//=S4*I4*V4
$objProductasset->cost_tires_replacement = ($objProductasset->cost_tires>0?$objProductasset->cost_tires/$objProductasset->useful_life_tires:0);
//=SI(K4="",0,K4/L4)
if ($objProductasset->useful_life_year>0 && $objProductasset->percent_repair && $objProductasset->useful_life_hours)
	$objProductasset->cost_repair = (($objProductasset->cost_acquisition-$objProductasset->cost_tires)/($objProductasset->useful_life_year*$objProductasset->useful_life_hours)*$objProductasset->percent_repair/100);
//=((H4-K4)/(M4*N4)*P4/100)
//if (empty($objProductasset->cost_pu_productive))
//{
$cost_pu_productive = $objProductasset->cost_pu_productive;
	$objProductasset->cost_pu_productive = $objProductasset->cost_depreciation+$objProductasset->cost_interest+$objProductasset->cost_fuel_consumption+$objProductasset->cost_lubricants+$objProductasset->cost_tires_replacement+$objProductasset->cost_repair;
	$objProductasset->cost_pu_improductive = $objProductasset->cost_depreciation+$objProductasset->cost_interest;
//}
if ($objProductasset->cost_pu_productive != $cost_pu_productive && $objProductasset->cost_pu_productive>0)
{
	//actualizamos
	$resup = $objProductasset->update($user);
	if ($resup <=0)
	{
		$error++;
		setEventMessages($objProductasset->error,$objProductasset->errors,'errors');
	}
}
//=SUMA(AH4:AM4)
/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

// Part to create
if ($action == 'create')
{
	print load_fiche_titre($langs->trans("NewMyModule"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	print '<input type="hidden" name="tab" value="'.$tab.'">';
	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td><input class="flat" type="text" name="fk_product" value="'.GETPOST('fk_product').'"></td></tr>';
	if ($tab == 'default')
	{
		//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_hour_productive").'</td><td><input class="flat" type="text" name="cost_hour_productive" value="'.GETPOST('cost_hour_productive').'"></td></tr>';
		//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_hour_improductive").'</td><td><input class="flat" type="text" name="cost_hour_improductive" value="'.GETPOST('cost_hour_improductive').'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_depreciation").'</td><td><input class="flat" type="number" min="0" step="any" name="cost_depreciation" value="'.GETPOST('cost_depreciation').'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_interest").'</td><td><input class="flat" type="number" min="0" step="any" name="cost_interest" value="'.GETPOST('cost_interest').'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_fuel_consumption").'</td><td><input class="flat" type="number" min="0" step="any" name="cost_fuel_consumption" value="'.GETPOST('cost_fuel_consumption').'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_lubricants").'</td><td><input class="flat" type="number" min="0" step="any" name="cost_lubricants" value="'.GETPOST('cost_lubricants').'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_tires_replacement").'</td><td><input class="flat" type="number" min="0" step="any" name="cost_tires_replacement" value="'.GETPOST('cost_tires_replacement').'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_repair").'</td><td><input class="flat" type="number" min="0" step="any" name="cost_repair" value="'.GETPOST('cost_repair').'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_pu_improductive").'</td><td><input class="flat" type="number" min="0" step="any" name="cost_pu_improductive" value="'.GETPOST('cost_pu_improductive').'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_pu_productive").'</td><td><input class="flat" type="number" min="0" step="any" name="cost_pu_productive" value="'.GETPOST('cost_pu_productive').'"></td></tr>';
	}
	if ($tab == 'technical')
	{
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpercent_interest").'</td><td><input class="flat" type="number" min="0" step="any" name="percent_interest" value="'.GETPOST('percent_interest').'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_acquisition").'</td><td><input class="flat" type="number" min="0" step="any" name="cost_acquisition" value="'.GETPOST('cost_acquisition').'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldengine_power").'</td><td><input class="flat" type="number" min="0" step="any" name="engine_power" value="'.GETPOST('engine_power').'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_type_engine").'</td><td><input class="flat" type="number" min="0" step="any" name="fk_type_engine" value="'.GETPOST('fk_type_engine').'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_tires").'</td><td><input class="flat" type="number" min="0" step="any" name="cost_tires" value="'.GETPOST('cost_tires').'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fielduseful_life_tires").'</td><td><input class="flat" type="number" min="0" step="any" name="useful_life_tires" value="'.GETPOST('useful_life_tires').'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fielduseful_life_year").'</td><td><input class="flat" type="number" min="0" step="any" name="useful_life_year" value="'.GETPOST('useful_life_year').'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fielduseful_life_hours").'</td><td><input class="flat" type="number" min="0" step="any" name="useful_life_hours" value="'.GETPOST('useful_life_hours').'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpercent_residual_value").'</td><td><input class="flat" type="number" min="0" step="any" name="percent_residual_value" value="'.GETPOST('percent_residual_value').'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpercent_repair").'</td><td><input class="flat" type="number" min="0" step="any" name="percent_repair" value="'.GETPOST('percent_repair').'"></td></tr>';
		print '<tr><td>'.$langs->trans("Fieldformula").'</td><td><input class="flat" type="text" name="formula" value="'.GETPOST('formula').'"></td></tr>';
	}
	if ($tab == 'factor')
	{
		print '<tr><td class="fieldrequired">'.$langs->trans("Fielddiesel_consumption").'</td><td><input class="flat" type="number" min="0" step="any" name="diesel_consumption" value="'.GETPOST('diesel_consumption').'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fielddiesel_lubricants").'</td><td><input class="flat" type="number" min="0" step="any" name="diesel_lubricants" value="'.GETPOST('diesel_lubricants').'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldgasoline_consumption").'</td><td><input class="flat" type="number" min="0" step="any" name="gasoline_consumption" value="'.GETPOST('gasoline_consumption').'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldgasoline_lubricants").'</td><td><input class="flat" type="number" min="0" step="any" name="gasoline_lubricants" value="'.GETPOST('gasoline_lubricants').'"></td></tr>';
	}
	if ($tab == 'cost')
	{
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_diesel").'</td><td><input class="flat" type="number" min="0" step="any" name="cost_diesel" value="'.GETPOST('cost_diesel').'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_gasoline").'</td><td><input class="flat" type="number" min="0" step="any" name="cost_gasoline" value="'.GETPOST('cost_gasoline').'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldenergy_kw").'</td><td><input class="flat" type="number" min="0" step="any" name="energy_kw" value="'.GETPOST('energy_kw').'"></td></tr>';

	}
		//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.GETPOST('fk_user_create').'"></td></tr>';
		//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.GETPOST('fk_user_mod').'"></td></tr>';
		//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.GETPOST('status').'"></td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}

// Part to edit record
if ($id && $action == 'edit')
{
	$head=budgetproductasset_prepare_head($object,$user);
	$titre=$langs->trans("CardProduct".$object->type);
	$picto=($object->type== Product::TYPE_SERVICE?'service':'product');
	dol_fiche_head($head, $tab, $titre, 0, $picto);

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	print '<input type="hidden" name="idr" value="'.$objProductasset->id.'">';
	print '<input type="hidden" name="tab" value="'.$tab.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	if ($tab == 'default')
	{
		//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_hour_productive").'</td><td><input class="flat" type="text" name="cost_hour_productive" value="'.(GETPOST('cost_hour_productive')?GETPOST('cost_hour_productive'):$objProductasset->cost_hour_productive).'"></td></tr>';
		//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_hour_improductive").'</td><td><input class="flat" type="text" name="cost_hour_improductive" value="'.(GETPOST('cost_hour_improductive')?GETPOST('cost_hour_improductive'):$objProductasset->cost_hour_improductive).'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_depreciation").'</td><td><input class="flat" type="number" min="0" step="any" name="cost_depreciation" value="'.(GETPOST('cost_depreciation')?GETPOST('cost_depreciation'):$objProductasset->cost_depreciation).'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_interest").'</td><td><input class="flat" type="number" min="0" step="any" name="cost_interest" value="'.(GETPOST('cost_interest')?GETPOST('cost_interest'):$objProductasset->cost_interest).'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_fuel_consumption").'</td><td><input class="flat" type="number" min="0" step="any" name="cost_fuel_consumption" value="'.(GETPOST('cost_fuel_consumption')?GETPOST('cost_fuel_consumption'):$objProductasset->cost_fuel_consumption).'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_lubricants").'</td><td><input class="flat" type="number" min="0" step="any" name="cost_lubricants" value="'.(GETPOST('cost_lubricants')?GETPOST('cost_lubricants'):$objProductasset->cost_lubricants).'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_tires_replacement").'</td><td><input class="flat" type="number" min="0" step="any" name="cost_tires_replacement" value="'.(GETPOST('cost_tires_replacement')?GETPOST('cost_tires_replacement'):$objProductasset->cost_tires_replacement).'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_repair").'</td><td><input class="flat" type="number" min="0" step="any" name="cost_repair" value="'.(GETPOST('cost_repair')?GETPOST('cost_repair'):$objProductasset->cost_repair).'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_pu_improductive").'</td><td><input class="flat" type="number" min="0" step="any" name="cost_pu_improductive" value="'.(GETPOST('cost_pu_improductive')?GETPOST('cost_pu_improductive'):$objProductasset->cost_pu_improductive).'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_pu_productive").'</td><td><input class="flat" type="number" min="0" step="any" name="cost_pu_productive" value="'.(GETPOST('cost_pu_productive')?GETPOST('cost_pu_productive'):$objProductasset->cost_pu_productive).'"></td></tr>';
	}
	if ($tab == 'technical')
	{
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpercent_interest").'</td><td><input class="flat" type="number" min="0" step="any" name="percent_interest" value="'.(GETPOST('percent_interest')?GETPOST('percent_interest'):$objProductasset->percent_interest).'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_acquisition").'</td><td><input class="flat" type="number" min="0" step="any" name="cost_acquisition" value="'.(GETPOST('cost_acquisition')?GETPOST('cost_acquisition'):$objProductasset->cost_acquisition).'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldengine_power").'</td><td><input class="flat" type="number" min="0" step="any" name="engine_power" value="'.(GETPOST('engine_power')?GETPOST('engine_power'):$objProductasset->engine_power).'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_type_engine").'</td><td>';
		print $form->selectarray('fk_type_engine',$aTypeengine,$objProductasset->fk_type_engine,1);
		print '</td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_tires").'</td><td><input class="flat" type="number" min="0" step="any" name="cost_tires" value="'.(GETPOST('cost_tires')?GETPOST('cost_tires'):$objProductasset->cost_tires).'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fielduseful_life_tires").'</td><td><input class="flat" type="number" min="0" step="any" name="useful_life_tires" value="'.(GETPOST('useful_life_tires')?GETPOST('useful_life_tires'):$objProductasset->useful_life_tires).'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fielduseful_life_year").'</td><td><input class="flat" type="number" min="0" step="any" name="useful_life_year" value="'.(GETPOST('useful_life_year')?GETPOST('useful_life_year'):$objProductasset->useful_life_year).'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fielduseful_life_hours").'</td><td><input class="flat" type="number" min="0" step="any" name="useful_life_hours" value="'.(GETPOST('useful_life_hours')?GETPOST('useful_life_hours'):$objProductasset->useful_life_hours).'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpercent_residual_value").'</td><td><input class="flat" type="number" min="0" step="any" name="percent_residual_value" value="'.(GETPOST('percent_residual_value')?GETPOST('percent_residual_value'):$objProductasset->percent_residual_value).'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpercent_repair").'</td><td><input class="flat" type="number" min="0" step="any" name="percent_repair" value="'.(GETPOST('percent_repair')?GETPOST('percent_repair'):$objProductasset->percent_repair).'"></td></tr>';
		print '<tr><td>'.$langs->trans("Fieldformula").'</td><td><input class="flat" type="text" name="formula" value="'.(GETPOST('formula')?GETPOST('formula'):$objProductasset->formula).'"></td></tr>';
	}
	if ($tab == 'factor')
	{
		print '<tr><td class="fieldrequired">'.$langs->trans("Fielddiesel_consumption").'</td><td><input class="flat" type="number" min="0" step="any" name="diesel_consumption" value="'.(GETPOST('diesel_consumption')?GETPOST('diesel_consumption'):$objProductasset->diesel_consumption).'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fielddiesel_lubricants").'</td><td><input class="flat" type="number" min="0" step="any" name="diesel_lubricants" value="'.(GETPOST('diesel_lubricants')?GETPOST('diesel_lubricants'):$objProductasset->diesel_lubricants).'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldgasoline_consumption").'</td><td><input class="flat" type="number" min="0" step="any" name="gasoline_consumption" value="'.(GETPOST('gasoline_consumption')?GETPOST('gasoline_consumption'):$objProductasset->gasoline_consumption).'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldgasoline_lubricants").'</td><td><input class="flat" type="number" min="0" step="any" name="gasoline_lubricants" value="'.(GETPOST('gasoline_lubricants')?GETPOST('gasoline_lubricants'):$objProductasset->gasoline_lubricants).'"></td></tr>';
	}
	if ($tab == 'cost')
	{
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_diesel").'</td><td><input class="flat" type="number" min="0" step="any" name="cost_diesel" value="'.(GETPOST('cost_diesel')?GETPOST('cost_diesel'):$objProductasset->cost_diesel).'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_gasoline").'</td><td><input class="flat" type="number" min="0" step="any" name="cost_gasoline" value="'.(GETPOST('cost_gasoline')?GETPOST('cost_gasoline'):$objProductasset->cost_gasoline).'"></td></tr>';
		print '<tr><td class="fieldrequired">'.$langs->trans("Fieldenergy_kw").'</td><td><input class="flat" type="number" min="0" step="any" name="energy_kw" value="'.(GETPOST('energy_kw')?GETPOST('energy_kw'):$objProductasset->energy_kw).'"></td></tr>';

	}

	print '</table>';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}



// Part to show record
if ($objProductasset->id >= 0 && (empty($action) || ($action != 'edit' && $action != 'create')))
{
	$res = $objProductasset->fetch_optionals($objProductasset->id, $extralabels);

	$head=budgetproductasset_prepare_head($object,$user);
	$titre=$langs->trans("CardProduct".$object->type);
	$picto=($object->type== Product::TYPE_SERVICE?'service':'product');
	dol_fiche_head($head, $tab, $titre, 0, $picto);

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $objProductasset->id, $langs->trans('DeleteMyOjbect'), $langs->trans('ConfirmDeleteMyObject'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>'.$objProductasset->label.'</td></tr>';
	//
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td>'.$objProductasset->fk_product.'</td></tr>';
	if ($tab == 'default')
	{
		//print '<tr><td>'.$langs->trans("Fieldcost_hour_productive").'</td><td class="right">'.price(price2num($objProductasset->cost_hour_productive,$nDecimal)).'</td></tr>';
		//print '<tr><td>'.$langs->trans("Fieldcost_hour_improductive").'</td><td class="right">'.price(price2num($objProductasset->cost_hour_improductive,$nDecimal)).'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldcost_depreciation").'</td><td class="right">'.price(price2num($objProductasset->cost_depreciation,$nDecimal)).'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldcost_interest").'</td><td class="right">'.price(price2num($objProductasset->cost_interest,$nDecimal)).'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldcost_fuel_consumption").'</td><td class="right">'.price(price2num($objProductasset->cost_fuel_consumption,$nDecimal)).'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldcost_lubricants").'</td><td class="right">'.price(price2num($objProductasset->cost_lubricants,$nDecimal)).'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldcost_tires_replacement").'</td><td class="right">'.price(price2num($objProductasset->cost_tires_replacement,$nDecimal)).'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldcost_repair").'</td><td class="right">'.price(price2num($objProductasset->cost_repair,$nDecimal)).'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldcost_pu_improductive").'</td><td class="right">'.price(price2num($objProductasset->cost_pu_improductive,$nDecimal)).'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldcost_pu_productive").'</td><td class="right">'.price(price2num($objProductasset->cost_pu_productive,$nDecimal)).'</td></tr>';
	}
	if ($tab == 'technical')
	{
		print '<tr><td>'.$langs->trans("Fieldpercent_interest").'</td><td class="right">'.price(price2num($objProductasset->percent_interest,$nDecimal)).'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldcost_acquisition").'</td><td class="right">'.$objProductasset->cost_acquisition.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldengine_power").'</td><td class="right">'.$objProductasset->engine_power.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldfk_type_engine").'</td><td class="right">'.$aTypeengine[$objProductasset->fk_type_engine].'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldcost_tires").'</td><td class="right">'.$objProductasset->cost_tires.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fielduseful_life_tires").'</td><td class="right">'.$objProductasset->useful_life_tires.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fielduseful_life_year").'</td><td class="right">'.$objProductasset->useful_life_year.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fielduseful_life_hours").'</td><td class="right">'.$objProductasset->useful_life_hours.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldpercent_residual_value").'</td><td class="right">'.$objProductasset->percent_residual_value.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldpercent_repair").'</td><td class="right">'.$objProductasset->percent_repair.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldformula").'</td><td class="right">'.$objProductasset->formula.'</td></tr>';
	}
	if ($tab == 'factor')
	{
		print '<tr><td>'.$langs->trans("Fielddiesel_consumption").'</td><td class="right">'.$objProductasset->diesel_consumption.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fielddiesel_lubricants").'</td><td class="right">'.$objProductasset->diesel_lubricants.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldgasoline_consumption").'</td><td class="right">'.$objProductasset->gasoline_consumption.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldgasoline_lubricants").'</td><td class="right">'.$objProductasset->gasoline_lubricants.'</td></tr>';
	}
	if ($tab == 'cost')
	{
		print '<tr><td>'.$langs->trans("Fieldcost_diesel").'</td><td class="right">'.$objProductasset->cost_diesel.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldcost_gasoline").'</td><td class="right">'.$objProductasset->cost_gasoline.'</td></tr>';
		print '<tr><td>'.$langs->trans("Fieldenergy_kw").'</td><td class="right">'.$objProductasset->energy_kw.'</td></tr>';
	}
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td>'.$objProductasset->fk_user_create.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td>'.$objProductasset->fk_user_mod.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldstatus").'</td><td class="right">'.$objProductasset->getLibStatut(6).'</td></tr>';
	print '</table>';

	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{

		if (empty($objProductasset->id) && $user->rights->budget->asset->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=create&amp;tab='.$tab.'">'.$langs->trans("New").'</a></div>'."\n";
		}
		if ($objProductasset->id && $user->rights->budget->asset->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&idr='.$objProductasset->id.'&amp;action=edit&amp;tab='.$tab.'">'.$langs->trans("Modify").'</a></div>'."\n";
		}
		if ($user->rights->budget->asset->upload)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.dol_buildpath('/budget/productasset/import.php',1).'?action=create">'.$langs->trans("Upload").'</a></div>'."\n";
		}

		if ($user->rights->budget->asset->del)
		{
			//print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&idr='.$objProductasset->id.'&amp;action=delete&amp;tab='.$tab.'">'.$langs->trans('Delete').'</a></div>'."\n";
		}
	}
	print '</div>'."\n";

	// Example 2 : Adding links to objects
	// Show links to link elements
	//$linktoelem = $form->showLinkToObjectBlock($object, null, array('productasset'));
	//$somethingshown = $form->showLinkedObjectBlock($object, $linktoelem);

}


