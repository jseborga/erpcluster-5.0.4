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
 *   	\file       budget/itemsproduct_card.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-04-18 09:10
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


$search_fk_item=GETPOST('search_fk_item','int');
$search_fk_product=GETPOST('search_fk_product','int');
$search_group_structure=GETPOST('search_group_structure','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_status=GETPOST('search_status','int');

$cDecimal='0.';
for ($a=1; $a<=$nDecimal;$a++)
{
	if ($a==$nDecimal) $cDecimal.='1';
	else $cDecimal.='0';
}


if (empty($action) && empty($id) && empty($ref)) $action='view';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
//$result = restrictedArea($user, 'budget', $id);


$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($objItemsproduct->table_element);

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('itemsproduct'));


/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/


if (! empty($conf->use_javascript_ajax))
{
	print "\n".'<script type="text/javascript">';
	print '$(document).ready(function () { ';
	if (!$object->manual_performance)
	{
		print '	$("#units").change(function() { document.formsup.action.value="'.$action.'"; document.formsup.submit(); }); $("#performance").change(function() { 	document.formsup.action.value="'.$action.'"; document.formsup.submit(); 		}); ';
	}
	print '	$("#fk_product").change(function() { document.formsup.action.value="'.$action.'"; 	document.formsup.submit(); 	}); });';
	print '</script>'."\n";
}


// Part to create
if ($action == 'create')
{
	print load_fiche_titre($langs->trans("NewMyModule"));

	print '<form name="formsup" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';

	dol_fiche_head();

	if($objItemsregion->hour_production>0)
	{
		if (isset($_POST['units']) && GETPOST('units')!=GETPOST('unitstmp'))
		{
			$objItemsproduct->performance = GETPOST('units')/$objItemsregion->hour_production;
			$_POST['performance'] = GETPOST('units')/$objItemsregion->hour_production;
		}
		elseif(isset($_POST['performance']) && GETPOST('performance')!=GETPOST('performancetmp'))
		{
			$objItemsproduct->units = ceil(GETPOST('performance')/$objItemsregion->hour_production);
			$_POST['units'] = ceil(GETPOST('performance')/$objItemsregion->hour_production);
		}
	}

			//vamos a establecer valor a dos variables
	if (!isset($_POST['units']))
	{
		$unitstmp= $obj->units;
		$performancetmp= $obj->performance;
	}
	else
	{
		$unitstmp = GETPOST('units');
		$performancetmp = GETPOST('performance');
	}

	print '<input type="hidden" name="unitstmp" value="'.$unitstmp.'">';
	print '<input type="hidden" name="performancetmp" value="'.$performancetmp.'">';

	print '<table class="border centpercent">'."\n";
	print '<tr><td>'.$langs->trans("Fieldfk_product").'</td><td>';
	$form->select_produits_v(GETPOST('fk_product'),'fk_product','',$conf->product->limit_size,0,-1,2,'',1,array());
	print '</td></tr>';
	if($fk_product>0)
	{
		//vamos a buscar el producto y rellenar los valores
		$resprod = $objProduct->fetch($fk_product);
		if ($resprod==1)
		{
			$_POST['ref'] = $objProduct->ref;
			$_POST['label'] = $objProduct->label;
			$_POST['fk_unit'] = $objProduct->fk_unit;
			$_POST['fk_origin'] = $objProduct->country_id;
			//vamos a buscar la categoria
			$aCat = $objCategorie->containing($fk_product, 'product', $mode='id');
			if (is_array($aCat))
			{
				foreach ($aCat AS $j => $fk_categorie)
				{
					if ($conf->global->ITEMS_DEFAULT_CATEGORY_MA== $fk_categorie) $_POST['group_structure'] = 'MA';
					if ($conf->global->ITEMS_DEFAULT_CATEGORY_MO== $fk_categorie) $_POST['group_structure'] = 'MO';
					if ($conf->global->ITEMS_DEFAULT_CATEGORY_MQ== $fk_categorie) $_POST['group_structure'] = 'MQ';
				}

			}
		}
		//vamos a buscar el costo productivo
		$res = $objProductasset->fetch(0,$fk_product);
		if ($res == 1)
		{
			$_POST['amount_noprod'] = $lCurrency?$objProductasset->cost_pu_improductive/$exchange_rate:$objProductasset->cost_pu_improductive;
			$_POST['amount'] = $lCurrency?$objProductasset->cost_pu_productive/$exchange_rate:$objProductasset->cost_pu_productive;
		}
	}
	print '<tr><td>'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.GETPOST('ref').'" maxlength="30" autofocus></td></tr>'
;	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldgroup_structure").'</td><td>';
	print $form->selectarray('group_structure',$aGroup,GETPOST('group_structure'),1);
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat quatrevingtpercent" maxlength="255" type="text" name="label" value="'.GETPOST('label').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit").'</td><td>';
	print $form->selectUnits(GETPOST('fk_unit'),'fk_unit',1);
	print '</td></tr>';
	$lView = true;
	if ($objItemsproduct->group_structure == 'MQ')
	{
		if (!$object->manual_performance)
			$lView=false;
	}

	if ($lView)
	{

		print '<tr><td>'.$langs->trans("Fieldquantity").'</td><td><input id="units" class="flat" type="number" min="0" step="any" name="units" value="'.(GETPOST('units')?GETPOST('units'):1).'" maxlength="11"></td></tr>';
		//print '<tr><td>'.$langs->trans("Fieldcommander").'</td><td><input id="commander" class="flat" type="number" min="0" step="any" name="commander" value="'.GETPOST('commander').'" maxlength="11"></td></tr>';
		print '<tr><td>'.$langs->trans("Fieldperformance").'</td><td><input id="performance" class="flat" type="number" min="0" step="any" name="performance" value="'.(GETPOST('performance')?GETPOST('performance'):100).'" maxlength="11"></td></tr>';

		if ($object->manual_performance)
			print '<tr><td>'.$langs->trans("Fieldprice_productive").'</td><td><input id="price_productive" class="flat" type="number" min="0" step="'.$cDecimal.'" name="price_productive" value="'.price2num((GETPOST('price_productive')?GETPOST('price_productive'):100),$nDecimal).'" maxlength="11"></td></tr>';

		if ($fk_product > 0)
		{
			print '<tr><td nowrap valign="top">'.$langs->trans("Fieldamount").'</td><td>';
			print '<select name="amount">'.$options.'</select>';
			print '<br><input type="number" min="0" step="any" name="amount_new" value="'.GETPOST('amount_new').'" placeholder="'.$langs->trans('Pricenow').'">';
			print info_admin($langs->trans("Thenewpriceprevailsovertheselected"),1);
			print '</td></tr>';
		}
	}
	//costo no productivo
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_improductive").' '.$lCurrency.'</td><td><input id="amount_noprod" class="flat" type="number" min="0" step="'.$cDecimal.'" name="amount_noprod" value="'.GETPOST('amount_noprod').'" maxlength="11"></td></tr>';
	//costo productivo
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_productive").'</td><td><input id="amount" class="flat" type="number" min="0" step="'.$cDecimal.'" name="amount" value="'.GETPOST('amount').'" maxlength="11"></td></tr>';


	print '<tr><td class="fieldrequired"><label for="selectcountry_id">'.$langs->trans("Fieldfk_origin").'</label></td><td class="maxwidthonsmartphone">';
	print $form->select_country((GETPOST('fk_origin')?GETPOST('fk_origin'):$mysoc->country_id),'fk_origin');
	if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
	print '</td></tr>'."\n";

	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpercent_origin").'</td><td>';
	print '<input type="number" min="0" step="any" max="100" name="percent_origin" value="'.(GETPOST('percent_origin')?GETPOST('percent_origin'):100).'">';
	print '</td></tr>';

	//if (!$conf->productext->enabled)
	if ($objitemsproduct->group_structure == 'MQ')
		print '<tr><td>'.$langs->trans("Fieldformula").'</td><td><input class="flat" type="text" name="formula" value="'.GETPOST('formula').'" maxlength="100"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.GETPOST('fk_user_create').'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.GETPOST('fk_user_mod').'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.GETPOST('status').'"></td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}



// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	print load_fiche_titre($langs->trans("Itemsproduct"));

	print '<form name="formsup" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	print '<input type="hidden" name="idr" value="'.$objItemsproduct->id.'">';

	dol_fiche_head();
	print '<table class="border centpercent">'."\n";

	if($objItemsregion->hour_production>0)
	{
		if (isset($_POST['units']) && GETPOST('units')!=GETPOST('unitstmp'))
		{
			$objItemsproductregion->performance = GETPOST('units')/$objItemsregion->hour_production;
			$_POST['performance'] = GETPOST('units')/$objItemsregion->hour_production;
		}
		elseif(isset($_POST['performance']) && GETPOST('performance')!=GETPOST('performancetmp'))
		{
			$objItemsproductregion->units = ceil(GETPOST('performance')/$objItemsregion->hour_production);
			$_POST['units'] = ceil(GETPOST('performance')/$objItemsregion->hour_production);
		}
	}

			//vamos a establecer valor a dos variables
	if (!isset($_POST['units']))
	{
		$unitstmp= $obj->units;
		$performancetmp= $obj->performance;
	}
	else
	{
		$unitstmp = GETPOST('units');
		$performancetmp = GETPOST('performance');
	}
	print '<input type="hidden" name="unitstmp" value="'.$unitstmp.'">';
	print '<input type="hidden" name="performancetmp" value="'.$performancetmp.'">';
	print '<input type="hidden" name="price_productive" value="'.$objItemsproductregion->price_productive.'">';
	print '<input type="hidden" name="price_improductive" value="'.$objItemsproductregion->price_improductive.'">';
	print '<tr><td>'.$langs->trans("Fieldfk_product").'</td><td>';
	$form->select_produits_v((GETPOST('fk_product')?GETPOST('fk_product'):$objItemsproduct->fk_product),'fk_product','',$conf->product->limit_size,0,-1,2,'',1,array());
	print '</td></tr>';

	print '<tr><td>'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.(GETPOST('ref')?GETPOST('ref'):$objItemsproduct->ref).'" maxlength="30"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldgroup_structure").'</td><td>';
	print $form->selectarray('group_structure',$aGroup,(GETPOST('group_structure')?GETPOST('group_structure'):$objItemsproduct->group_structure),1);
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat quatrevingtpercent" type="text" name="label" value="'.(GETPOST('label')?GETPOST('label'):$objItemsproduct->label).'" maxlength="255"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit").'</td><td>';
	print $form->selectUnits((GETPOST('fk_unit')?GETPOST('fk_unit'):$objItemsproduct->fk_unit),'fk_unit',1);
	print '</td></tr>';
	$lView = true;
	if ($objItemsproduct->group_structure == 'MQ')
	{
		if (!$object->manual_performance)
			$lView=false;
	}

	if ($lView)
	{
		print '<tr><td>'.$langs->trans("Fieldquantity").'</td><td><input id="units" class="flat" type="number" min="0" step="1" name="units" value="'.(GETPOST('units')?GETPOST('units'):$objItemsproductregion->units).'" ></td></tr>';
		if ($objItemsproduct->group_structure =='MQ' && !$object->manual_performance)
			print '<tr><td>'.$langs->trans("Fieldcommander").'</td><td><input id="commander" class="flat" type="number" min="0" step="any" name="commander" value="'.(GETPOST('commander')?GETPOST('commander'):$objItemsproductregion->commander).'" maxlength="11"></td></tr>';

		print '<tr><td>'.$langs->trans("Fieldperformance").'</td><td><input id="performance" class="flat" type="number" min="0" step="'.$cDecimal.'" name="performance" value="'.price2num((GETPOST('performance')?GETPOST('performance'):$objItemsproductregion->performance),$nDecimal).'" maxlength="11"></td></tr>';
		if ($object->manual_performance)
			print '<tr><td>'.$langs->trans("Fieldprice_productive").'</td><td><input id="price_productive" class="flat" type="number" min="0" step="'.$cDecimal.'" name="price_productive" value="'.price2num((GETPOST('price_productive')?GETPOST('price_productive'):$objItemsproductregion->price_productive),$nDecimal).'" maxlength="11"></td></tr>';

		$fk_product=GETPOST('fk_product')?GETPOST('fk_product'):$objItemsproduct->fk_product;
		if ($fk_product>0)
		{
			print '<tr><td nowrap valign="top">'.$langs->trans("Fieldamount").'</td><td>';
			print '<select name="amount">'.$options.'</select>';
			print '<br><input type="number" min="0" step="any" name="amount_new" value="'.(!$lPriceselected?(GETPOST('amount_new')?GETPOST('amount_new'):$objItemsproductregion->amount):GETPOST('amount_new')).'" placeholder="'.$langs->trans('Pricenow').'">';
			print info_admin($langs->trans("Thenewpriceprevailsovertheselected"),1);
			print '</td></tr>';
		}
	}
	//costo no productivo
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_improductive").'</td><td><input id="amount_noprod" class="flat" type="number" min="0" step="'.$cDecimal.'" name="amount_noprod" value="'.price2num((GETPOST('amount_noprod')?GETPOST('amount_noprod'):$objItemsproductregion->amount_noprod),$nDecimal).'" maxlength="11"></td></tr>';
	//costo productivo
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_productive").'</td><td><input id="amount" class="flat" type="number" min="0" step="'.$cDecimal.'" name="amount" value="'.price2num((GETPOST('amount')?GETPOST('amount'):$objItemsproductregion->amount),$nDecimal).'" maxlength="11"></td></tr>';

	print '<tr><td class="fieldrequired"><label for="selectcountry_id">'.$langs->trans("Fieldfk_origin").'</label></td><td class="maxwidthonsmartphone">';
	print $form->select_country((GETPOST('fk_origin')?GETPOST('fk_origin'):($objItemsproductregion->fk_origin?$objItemsproductregion->fk_origin:$mysoc->country_id)),'fk_origin');
	if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
	print '</td></tr>'."\n";

	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpercent_origin").'</td><td>';
	print '<input type="number" min="0" step="any" max="100" name="percent_origin" value="'.(GETPOST('percent_origin')?GETPOST('percent_origin'):$objItemsproductregion->percent_origin).'">';
	print '</td></tr>';

	if ($objItemsproduct->group_structure=='MQ' && !$object->manual_performance)
	{
		$formula = (GETPOST('formula')?GETPOST('formula'):$objItemsproduct->formula);
		print '<tr><td>'.$langs->trans("Fieldformula").'</td><td><input class="flat" type="text" name="formula" value="'.$formula.'" maxlength="100"></td></tr>';
	}
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.$objItemsproduct->fk_user_create.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.$objItemsproduct->fk_user_mod.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.$objItemsproduct->status.'"></td></tr>';

	print '</table>';
	dol_fiche_end();
	if ($formula && !$object->manual_performance)
	{
		//si tiene formula vamos a crear un form para agregar los valores de la formula
		$filtervar = " AND t.status = 1";
		$resvar = $objPuvariables->fetchAll('ASC','t.ref',0,0,array(),'AND',$filtervar);
		if ($resvar>0)
		{
			dol_fiche_head();
			print '<table class="border centpercent">'."\n";
			$lines = $objPuvariables->lines;
			foreach ($lines AS $j => $line)
			{
				$pos = strpos($formula,$line->ref);
				if ($pos===false)
				{
				}
				else
				{
					$value = '';
					//vamos a recuperar el valor en production si existe
					$resp=$objItemsproduction->fetch(0, $objItem->id,$line->id,$objItemsproduct->id,$fk_region,$fk_sector);
					if ($resp==1)
						$value = (GETPOST("var[$line->id]")?GETPOST("var[$line->id]"):$objItemsproduction->quantity);
					$var = !$var;
					print '<tr '.$bc[$var].'>';
					print '<td>'.$line->ref.'</td><td>'.'<input type="number" min="0" step="any" name="var['.$line->id.']['.$objItemsproduct->id.']" value="'.$value.'">'.'</td>';
					print '</tr>';
				}
			}
			print '</table>';
		}

	}


	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}



// Part to show record
if ($objItemsproduct->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create')))
{
	$res = $objItemsproduct->fetch_optionals($objItemsproduct->id, $extralabels);

	print load_fiche_titre($langs->trans("Itemsproduct"));

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>'.$objItemsproduct->label.'</td></tr>';
	//
	print '<tr><td>'.$langs->trans("Fieldref").'</td><td>'.$objItemsproduct->ref.'</td></tr>';
	//print '<tr><td>'.$langs->trans("Fieldfk_item").'</td><td>'.$objItemsproduct->fk_item.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldgroup_structure").'</td><td>'.$aGroup[$objItemsproduct->group_structure].'</td></tr>';
	if ($objItemsproduct->fk_product)
	{
		$objProduct->fetch($objItemsproduct->fk_product);
		print '<tr><td>'.$langs->trans("Fieldfk_product").'</td><td>'.$objProduct->getNomUrl(1).'</td></tr>';
	}
	print '<tr><td>'.$langs->trans("Fieldlabel").'</td><td>'.$objItemsproduct->label.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldfk_origin").'</td><td>';
	if ($objItemsproductregion->fk_origin)
	{
		$tmparray=getCountry($objItemsproductregion->fk_origin,'all');
		$country_code=$tmparray['code'];
		$country=$tmparray['label'];


		$img=picto_from_langcode($country_code);
		$origin =  $img?$img.' ':'';
		print $origin .= getCountry($country_code,1);
	}

	print '</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldpercent_origin").'</td><td>'.price(price2num($objItemsproductregion->percent_origin,'MT')).'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldunits").'</td><td>'.$objItemsproduct->units.'</td></tr>';
	if ($objItemsproduct->group_structure=='MQ')
		print '<tr><td>'.$langs->trans("Fieldformula").'</td><td>'.$objItemsproduct->formula.'</td></tr>';

	//print '<tr><td>'.$langs->trans("Fieldfk_user_create").'</td><td>'.$objItemsproduct->fk_user_create.'</td></tr>';
	//print '<tr><td>'.$langs->trans("Fieldfk_user_mod").'</td><td>'.$objItemsproduct->fk_user_mod.'</td></tr>';
	//print '<tr><td>'.$langs->trans("Fieldstatus").'</td><td>'.$objItemsproduct->getLibStatut(3).'</td></tr>';

	print '</table>';

	dol_fiche_end();

	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->budget->ite->writepro && $object->status == 0)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&idr='.$objItemsproduct->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($object->status == 0 && $user->rights->budget->ite->delpro)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$id.'&idr='.$objItemsproduct->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
		}
	}
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	// Show links to link elements
	//$linktoelem = $form->showLinkToObjectBlock($object, null, array('itemsproduct'));
	//$somethingshown = $form->showLinkedObjectBlock($object, $linktoelem);

}


