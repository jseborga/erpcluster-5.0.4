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
 *   	\file       budget/budgettaskproduction_card.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-04-23 11:48
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


$search_fk_budget_task=GETPOST('search_fk_budget_task','int');
$search_fk_variable=GETPOST('search_fk_variable','int');
$search_fk_budget_task_product=GETPOST('search_fk_budget_task_product','int');
$search_quantity=GETPOST('search_quantity','alpha');
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


// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($objBudgettaskproduction->table_element);


// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('budgettaskproduction'));



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
			$aVariables[$line->id]['unit'] = $objTmp->getLabelOfUnit();
		}
	}
}

$code_structure = $aStrbudget[$object->fk_budget]['aStrgroupcat']['MQ'];
//vemos que se tiene en la items productos
$filter = " AND t.fk_budget_task = ".$object->id;
$filter.= " AND t.code_structure = '".$code_structure."'";
$resitemprod = $objBudgettaskresource->fetchAll('ASC','t.detail',0,0,array(),'AND',$filter);
$aTitle = array();
if ($resitemprod>0)
{
	$lines = $objBudgettaskresource->lines;
	//vamos a armar titulos
	foreach ($lines AS $j => $line)
	{
		$aTitle[$line->fk_product_budget] = array('ref'=>$line->ref,'fk_product'=>$line->fk_product,'label'=>$line->detail,'formula'=>$line->formula,'units'=>$line->units,'commander'=>$line->commander);
		if ($line->fk_product>0)
		{
			$resprod=$objProduct->fetch($line->fk_product);
			if ($resprod>0)
				$aTitle[$line->fk_product_budget]['objProduct'] = $objProduct;
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
	//
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_budget_task").'</td><td><input class="flat" type="text" name="fk_budget_task" value="'.GETPOST('fk_budget_task').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_variable").'</td><td><input class="flat" type="text" name="fk_variable" value="'.GETPOST('fk_variable').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_budget_task_product").'</td><td><input class="flat" type="text" name="fk_budget_task_product" value="'.GETPOST('fk_budget_task_product').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldquantity").'</td><td><input class="flat" type="text" name="quantity" value="'.GETPOST('quantity').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td><input class="flat" type="text" name="active" value="'.GETPOST('active').'"></td></tr>';
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

	print load_fiche_titre($langs->trans("Budgettaskproduction"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$id.'">';

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
						//buscamos en budgettaskproduction
						$resip=$objBudgettaskproduction->fetch(0,$id,$j,$k);
						if ($resip==1)
							print '<input class="maxwidth100onsmartphone quatrevingtpercent" type="number" min="0" step="any" name="variable['.$k.']['.$j.']" value="'.$objBudgettaskproduction->quantity.'">';
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
		if (count($aUnits)>0) $lUseunits= true;
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
if ($object->id > 0 && ($action != 'edit' && $action != 'create'))
{
	//$res = $objBudgettaskproduction->fetch_optionals($objBudgettaskproduction->id, $extralabels);

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
					//echo '<hr>'.$j.' '.$k;
					$resip=$objBudgettaskproduction->fetch(0,$object->id,$j,$k);
					if ($resip==1)
					{
						print price(price2num($objBudgettaskproduction->quantity,$nDecimal));
						$aVarformula[$k][$data['ref']]= $objBudgettaskproduction->quantity;
					}
					print '</td>';

				}
			}
			print '</tr>';
		}
	}
	//mostramos quien es el commandante
	/*
	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td colspan="3">'.$langs->trans('Commander').'</td>';
	if (count($aTitle)>0)
	{
		foreach ($aTitle AS $k => $row)
		{
			$checked= '';
			print '<td align="center">';
			print ($row['commander']?$langs->trans('Yes'):'');
			print '</td>';
		}
	}
	print '</tr>';
	*/
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
	$aItemsproduction_unit[$id] = $aUnits;
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
			$nApplicableperformance=1/$nTimeproductionoftheactivity*$aUnits[$k];
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
			$nPriceproductive=$nTimeproductionoftheactivity/($aResult[$k]*$aUnits[$k])*100;
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

	if ($object->fk_statut==0)
	{
		//actualizamos valores en el item
		$res = $objBudgettaskadd->fetch(0,$id);
		if ($res==1)
		{
			$objBudgettaskadd->hour_production=$nTimeproductionoftheactivity;
			if (empty($objBudgettaskadd->hour_production))$objBudgettaskadd->hour_production=0;
			$objBudgettaskadd->fk_user_mod=$user->id;
			$objBudgettaskadd->datem=$now;
			$res = $objBudgettaskadd->update($user);
			if ($res<=0)
			{
				$error++;
				setEventMessages($objBudgettaskadd->error,$objBudgettaskadd->errors,'errors');
			}
		}
		if (!$error)
		{
			//se actualiza los valores en la tabla
			foreach ($aTitle AS $k => $row)
			{
				//actualizamos resultados
				$res = $objBudgettaskresource->fetch($k);
				if ($res==1)
				{
					$objBudgettaskresource->units = $aUnits[$k];
					$objBudgettaskresource->price_improductive = $aPriceunproductive[$k]+0;
					$objBudgettaskresource->price_productive = $aPriceproductive[$k]+0;
					$objBudgettaskresource->performance = $aApplicable[$k]+0;
					$objBudgettaskresource->fk_user_mod = $user->id;
					$objBudgettaskresource->datem = $now;
					$objBudgettaskresource->tms = $now;
					$res = $objBudgettaskresource->update($user);
					if ($res <=0)
					{
						$error++;
						setEventMessages($objBudgettaskresource->error,$objBudgettaskresource->errors,'errors');
					}
				}
			}
		}
	}

	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$objBudgettaskresource,$action);
	// Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($objBudget->fk_statut == 0)
		{
			if ($user->rights->budget->budr->writerend && $object->fk_statut==0)
			{
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
			}
		}
	}
	print '</div>'."\n";

}
