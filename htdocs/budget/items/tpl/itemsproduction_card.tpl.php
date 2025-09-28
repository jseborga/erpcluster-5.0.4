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
 *   	\file       budget/itemsproduction_card.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-04-18 10:29
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
$search_fk_variable=GETPOST('search_fk_variable','int');
$search_fk_items_product=GETPOST('search_fk_items_product','int');
$search_quantity=GETPOST('search_quantity','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_status=GETPOST('search_status','int');



if (empty($action) && empty($id) && empty($ref)) $action='view';

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($objItemsproduction->table_element);

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('itemsproduction'));

//armanos un array de las variables disponibles
$filter = " AND t.status = 1";
$filter = " AND t.entity = ".$conf->entity;
$resvar = $objPuvariables->fetchAll('ASC','ref',0,0,array(),'AND',$filter);
$aVariables = array();
if ($resvar>0)
{
	$lines = $objPuvariables->lines;
	//vamos a armar titulos
	foreach ($lines AS $j => $line)
	{
		$aVariables[$line->id] = array('ref'=>$line->ref,'fk_unit'=>$line->fk_unit,'label'=>$line->label);
		if ($line->fk_unit>0)
		{
			$objTmp = new PuvariablesLine($db);
			$objTmp->fk_unit = $line->fk_unit;
			$aVariables[$line->id]['unit'] = $objTmp->getLabelOfUnit('short');
		}
	}
}

//vemos que se tiene en la items productos
$filter = " AND t.fk_item = ".$object->fk_item;
$filter.= " AND t.group_structure = 'MQ'";
$resitemprod = $objItemsproduct->fetchAll('ASC','t.label',0,0,array(),'AND',$filter);
$aTitle = array();
if ($resitemprod>0)
{
	$lines = $objItemsproduct->lines;
	//vamos a armar titulos
	foreach ($lines AS $j => $line)
	{
		$resreg= $objItemsproductregion->fetch(0,$line->id,$fk_region,$fk_sector);
		if ($resreg==1)
		{
			$aTitle[$line->id] = array('ref'=>$line->ref,'fk_product'=>$line->fk_product,'label'=>$line->label,'formula'=>$line->formula,'units'=>$line->units,'commander'=>$line->commander);

			if ($resreg==1)
			{
				$aTitle[$line->id]['units'] = $objItemsproductregion->units;
				$aTitle[$line->id]['commander'] = $objItemsproductregion->commander;
				$aTitle[$line->id]['fk_item_product_region'] = $objItemsproductregion->id;
			}
			if ($line->fk_product>0)
			{
				$resprod=$objProduct->fetch($line->fk_product);
				if ($resprod>0)
					$aTitle[$line->id]['objProduct'] = $objProduct;
			}
		}
	}
}

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

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_item").'</td><td><input class="flat" type="text" name="fk_item" value="'.GETPOST('fk_item').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_variable").'</td><td><input class="flat" type="text" name="fk_variable" value="'.GETPOST('fk_variable').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_items_product").'</td><td><input class="flat" type="text" name="fk_items_product" value="'.GETPOST('fk_items_product').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldquantity").'</td><td><input class="flat" type="text" name="quantity" value="'.GETPOST('quantity').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.GETPOST('fk_user_create').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.GETPOST('fk_user_mod').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.GETPOST('status').'"></td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}



// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	//recuperamos las unidades si existe
	$aUnits= array();
	if (isset($_SESSION['aItemsproduction_unit']))
	{
		$aItemsproduction_unit = unserialize($_SESSION['aItemsproduction_unit']);
		$aUnits = $aItemsproduction_unit[$id];
	}

	print load_fiche_titre($langs->trans("Itemsproduction"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	print '<tr class="liste_titre">';
	//armamos por defecto la cabecera inicial
	print_liste_field_titre($langs->trans('Code'),$_SERVER['PHP_SELF'],'','','align="center"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Description'),$_SERVER['PHP_SELF'],'','','align="center"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Unit'),$_SERVER['PHP_SELF'],'','','align="center"',$sortfield,$sortorder);

	//vamos a armar los equipos segun la lsita de itemsproduct
	if (count($aTitle))
	{
		foreach ($aTitle AS $j => $data)
		{
			print_liste_field_titre($data['label'],$_SERVER['PHP_SELF'],'','','align="center"',$sortfield,$sortorder);
		}
	}
	print '</tr>';


	//armamos el cuerpo
	if (count($aVariables)>0)
	{
		foreach ($aVariables AS $j => $data)
		{
			print '<input type="hidden" name="idVar['.$j.']" value="'.$j.'">';
			$var = !$var;
			print '<tr '.$bc[$var].'>';
			print '<td>'.$data['ref'].'</td>';
			print '<td>'.$data['label'].'</td>';
			print '<td>'.$data['unit'].'</td>';
			//vamos a buscar los valores cargados segun array a title
			if (count($aTitle)>0)
			{
				foreach ($aTitle AS $k => $row)
				{
					//vamos a determinar si corresponde input para cada equipo
					$formulatmp = $row['formula'];
					$pos = strpos($formulatmp,$data['ref']);
					$lView=false;
					if ($pos===false)
					{
					}
					else
					{
						$lView=true;
					}
					print '<td>';
					if($lView)
					{
						//buscamos en itemsproduction
						$resip=$objItemsproduction->fetch(0,$object->fk_item,$j,$k,$fk_region,$fk_sector);
						if ($resip==1)
							print '<input class="maxwidth100onsmartphone quatrevingtpercent" type="number" min="0" step="any" name="variable['.$k.']['.$j.']" value="'.$objItemsproduction->quantity.'">';
						else
							print '<input class="maxwidth100onsmartphone quatrevingtpercent" type="number" min="0" step="any" name="variable['.$k.']['.$j.']" value="'.GETPOST('variable['.$k.']['.$j.']').'">';
					}
					print '</td>';
				}
			}
			print '</tr>';
		}
	}

	//vamos a definir o actualizar las unidades
	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td colspan="3">'.$langs->trans('Units').'</td>';
	if (count($aTitle)>0)
	{
		$lUseunits = false;
		if (is_array($aUnits) && count($aUnits)>0) $lUseunits= true;
		foreach ($aTitle AS $k => $row)
		{

			print '<td>';
			print '<input type="number" min="0" name="aUnits['.$k.']" value="'.($lUseunits?$aUnits[$k]:$row['units']).'" '.($lUseunits?'readonly':'').'>';
			print '</td>';
		}
	}
	print '</tr>';

	//vamos a definir el commandante
	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td colspan="3">'.$langs->trans('Commander').'</td>';
	if (count($aTitle)>0)
	{
		foreach ($aTitle AS $k => $row)
		{
			$checked= '';
			if ($row['commander']) $checked= ' checked';
			print '<td>';
			print '<input type="radio" name="commander" value="'.$k.'"'.$checked.'>';
			print '</td>';
		}
	}
	print '</tr>';
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
	$res = $objItemsproduction->fetch_optionals($objItemsproduction->id, $extralabels);


	print load_fiche_titre($langs->trans("Itemsproduction"));

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	print '<tr class="liste_titre">';
	//armamos por defecto la cabecera inicial
	print_liste_field_titre($langs->trans('Code'),$_SERVER['PHP_SELF'],'','','align="center"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Description'),$_SERVER['PHP_SELF'],'','','align="center"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Unit'),$_SERVER['PHP_SELF'],'','','align="center"',$sortfield,$sortorder);

	//vamos a armar los equipos segun la lsita de itemsproduct
	if (count($aTitle))
	{
		foreach ($aTitle AS $j => $data)
		{
			print_liste_field_titre($data['label'],$_SERVER['PHP_SELF'],'','','','align="right"',$sortfield,$sortorder);

		}
	}
	print '</tr>';


	//armamos el cuerpo
	if (count($aVariables)>0)
	{
		$aVarformula = array();
		foreach ($aVariables AS $j => $data)
		{
			$var = !$var;
			print '<tr '.$bc[$var].'>';
			print '<td>'.$data['ref'].'</td>';
			print '<td>'.$data['label'].'</td>';
			print '<td>'.$data['unit'].'</td>';
			//vamos a buscar los valores cargados segun array a title
			if (count($aTitle)>0)
			{
				foreach ($aTitle AS $k => $row)
				{
					print '<td class="right">';
					//buscamos en itemsproduction
					$resip=$objItemsproduction->fetch(0,$object->fk_item,$j,$k,$fk_region,$fk_sector);
					if ($resip==1)
					{
						print price(price2num($objItemsproduction->quantity,$nDecimal));
						$aVarformula[$k][$data['ref']]= $objItemsproduction->quantity;
					}
					print '</td>';

				}
			}
			print '</tr>';
		}
	}

	//vamos a mostrar unos calculos
	print '<tr class="liste_total">';
	print '<td colspan="3">'.$langs->trans('Hourproduction').'</td>';
	$aOperator= array('*'=>'|*|','+'=>'|+|','-'=>'|-|','/'=>'|/|','('=>'|(|',')'=>'|)|');
	foreach ($aTitle AS $k => $row)
	{
		$selectcolor = '';
		if ($row['commander'])
			$selectcolor='style="background-color:#ffff00;"';
		print '<td class="right" '.$selectcolor.'>';
		if($row['formula'])
		{
			$formula = $row['formula'];
			foreach ((array) $aVarformula[$k] AS $variable => $value)
			{
				if (empty($value))$value= 0;
				$formula = str_replace($variable,$value,$formula);
			}
			//vamos a reemplazar algunos
			if (is_array($aVarformula) && isset($aVarformula[$k]))
			{
				if (count($aVarformula[$k])>0)
				{
					if ($formula)
					{
						eval("\$result= $formula;");
						print price(price2num($result,$nDecimal));
						$aResult[$k] = $result;
					}
				}
			}
		}
		print '</td>';
	}
	print '</tr>';
	$nTimeproductionoftheactivity = 0;
	//vamos a definir quien commanda
	foreach ($aTitle AS $k => $row)
	{
		if ($row['commander']) $nTimeproductionoftheactivity = $aResult[$k];
	}

	//vamos a calcular las unidades
	print '<tr class="liste_total">';
	print '<td colspan="3">'.$langs->trans('Units').'</td>';
	foreach ($aTitle AS $k => $row)
	{
		print '<td class="center">';
		$units= '';
		if ($aResult[$k]>0)
			$units = ceil($nTimeproductionoftheactivity/$aResult[$k]);
		print $units;
		$aUnits[$k] = $units;
		print '</td>';
	}
	print '</tr>';
	//vamos a pasar a una variable de session para actualizar las unidades
	$aItemsproduction_unit[$object->fk_item] = $aUnits;
	$_SESSION['aItemsproduction_unit'] = serialize($aItemsproduction_unit);
	//Time Production of the activity
	print '<tr class="liste_total">';
	print '<td colspan="3">'.$langs->trans('Timeproductionoftheactivity').'</td>';
	print '<td class="center" colspan="'.count($aTitle).'">';
	print price(price2num($nTimeproductionoftheactivity,$nDecimal));
	print '</td>';
	print '</tr>';

	//vamos a rendimiento aplicable
	print '<tr class="liste_total">';
	print '<td colspan="3">'.$langs->trans('Applicableperformance').'</td>';
	foreach ($aTitle AS $k => $row)
	{
		$nApplicableperformance=0;
		if ($nTimeproductionoftheactivity)
		{
			$units = 0;
			if (isset($aUnits[$k]) && $aUnits[$k]>0) $units = $aUnits[$k]+0;
			$nApplicableperformance=1/$nTimeproductionoftheactivity*$units;
		}
		$aApplicable[$k] = $nApplicableperformance;
		print '<td class="center">';
		print $nApplicableperformance;
		print '</td>';
	}
	print '</tr>';

	//vamos a calcular el precio productivo
	print '<tr class="liste_total">';
	print '<td colspan="3">'.$langs->trans('Productiveprice').'</td>';
	foreach ($aTitle AS $k => $row)
	{
		$nPriceproductive=0;
		if ($nTimeproductionoftheactivity)
		{
			$units = 0;
			if (isset($aUnits[$k]) && $aUnits[$k]>0) $units = $aUnits[$k]+0;
			if ($aResult[$k]>0)
				$nPriceproductive=$nTimeproductionoftheactivity/($aResult[$k]*$units)*100;
			else
				$nPriceproductive=0;
			$aPriceproductive[$k] = $nPriceproductive;
		}
		print '<td class="center">';
		print price(price2num($nPriceproductive,$nDecimal));
		print '</td>';
	}
	print '</tr>';

		//vamos a calcular el precio improducto
	print '<tr class="liste_total">';
	print '<td colspan="3">'.$langs->trans('Unproductiveprice').'</td>';
	foreach ($aTitle AS $k => $row)
	{
		$nPriceunproductive=0;
		if ($nTimeproductionoftheactivity)
		{
			$nPriceunproductive=100-$aPriceproductive[$k];
			$aPriceunproductive[$k] = $nPriceunproductive;
		}
		print '<td class="center">';
		print price(price2num($nPriceunproductive,$nDecimal));
		print '</td>';
	}
	print '</tr>';

	print '</table>';

	dol_fiche_end();

	if ($object->status==0)
	{
		//se actualiza en llx_items_region la hour_production
		$resir = $objItemsregion->fetch(0,$object->fk_item,$fk_region,$fk_sector);
		$lAdd=true;
		if ($resir==1) $lAdd=false;
		$objItemsregion->hour_production = $nTimeproductionoftheactivity;
		$objItemsregion->amount = $totalCost+0;
		if (empty($objItemsregion->hour_production)) $objItemsregion->hour_production = 0;
		if (empty($objItemsregion->amount_noprod)) $objItemsregion->amount_noprod = 0;
		if (empty($objItemsregion->amount)) $objItemsregion->amount = 0;
		if ($lAdd)
		{
			$objItemsregion->fk_item = $object->fk_item;
			$objItemsregion->fk_region = $fk_region;
			$objItemsregion->fk_sector = $fk_sector;
			$objItemsregion->fk_user_create = $user->id;
			$objItemsregion->datec = $now;
			$objItemsregion->status = 1;
		}
		$objItemsregion->fk_user_mod = $user->id;
		$objItemsregion->datem = $now;
		$objItemsregion->tms = $now;

		if (!$lAdd) $res = $objItemsregion->update($user);
		else $res = $objItemsregion->create($user);
		if ($res <=0)
		{
			$error++;
			setEventMessages($objItemsregion->error,$objItemsregion->errors,'errors');
		}
		if (!$error)
		{
			//se actualiza los valores en la tabla
			foreach ($aTitle AS $k => $row)
			{
				//actualizamos resultados en items product region
				$res = $objItemsproductregion->fetch(0,$k,$fk_region,$fk_sector);
				if ($res==1)
				{
					$units = 0;
					if (isset($aUnits[$k]) && $aUnits[$k]>0) $units = $aUnits[$k]+0;
					$objItemsproductregion->units = $units;
					$objItemsproductregion->hour_production = $aResult[$k]+0;
					$objItemsproductregion->price_improductive = $aPriceunproductive[$k]+0;
					$objItemsproductregion->price_productive = $aPriceproductive[$k]+0;
					$objItemsproductregion->performance = $aApplicable[$k]+0;
					$objItemsproductregion->fk_user_mod = $user->id;
					$objItemsproductregion->datem = $now;
					$objItemsproductregion->tms = $now;
					$res = $objItemsproductregion->update($user);
					if ($res <=0)
					{
						$error++;
						setEventMessages($objItemsproductregion->error,$objItemsproductregion->errors,'errors');
					}
				}
			}
		}
	}

	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);
	// Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->budget->ite->writerend && $object->status==0)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}
	}
	print '</div>'."\n";
}
