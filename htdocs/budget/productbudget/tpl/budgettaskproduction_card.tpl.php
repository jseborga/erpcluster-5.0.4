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
 *					Initialy built by build_class_from_table on 2018-04-19 09:09
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

//vemos que se tiene en la items productos
$filter = " AND t.fk_item = ".$id;
$filter.= " AND t.group_structure = 'MQ'";
$resitemprod = $objBudgettaskproduct->fetchAll('ASC','t.label',0,0,array(),'AND',$filter);
$aTitle = array();
if ($resitemprod>0)
{
	$lines = $objBudgettaskproduct->lines;
	//vamos a armar titulos
	foreach ($lines AS $j => $line)
	{
		$aTitle[$line->id] = array('ref'=>$line->ref,'fk_product'=>$line->fk_product,'label'=>$line->label,'formula'=>$line->formula,'units'=>$line->units,'commander'=>$line->commander);
		if ($line->fk_product>0)
		{
			$resprod=$objProduct->fetch($line->fk_product);
			if ($resprod>0)
				$aTitle[$line->id]['objProduct'] = $objProduct;
		}
	}
}

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	if ($cancel)
	{
		if ($action != 'addlink')
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}
		if ($id > 0 || ! empty($ref)) $ret = $objBudgettaskproduction->fetch($id,$ref);
		$action='';
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

		$objBudgettaskproduction->fk_budget_task=GETPOST('fk_budget_task','int');
		$objBudgettaskproduction->fk_variable=GETPOST('fk_variable','int');
		$objBudgettaskproduction->fk_budget_task_product=GETPOST('fk_budget_task_product','int');
		$objBudgettaskproduction->quantity=GETPOST('quantity','alpha');
		$objBudgettaskproduction->active=GETPOST('active','int');
		$objBudgettaskproduction->fk_user_create=GETPOST('fk_user_create','int');
		$objBudgettaskproduction->fk_user_mod=GETPOST('fk_user_mod','int');
		$objBudgettaskproduction->status=GETPOST('status','int');



		if (empty($objBudgettaskproduction->ref))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objBudgettaskproduction->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/list.php',1);
				header("Location: ".$urltogo);
				exit;
			}
			{
				// Creation KO
				if (! empty($objBudgettaskproduction->errors)) setEventMessages(null, $objBudgettaskproduction->errors, 'errors');
				else  setEventMessages($objBudgettaskproduction->error, null, 'errors');
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


		$objBudgettaskproduction->fk_budget_task=GETPOST('fk_budget_task','int');
		$objBudgettaskproduction->fk_variable=GETPOST('fk_variable','int');
		$objBudgettaskproduction->fk_budget_task_product=GETPOST('fk_budget_task_product','int');
		$objBudgettaskproduction->quantity=GETPOST('quantity','alpha');
		$objBudgettaskproduction->active=GETPOST('active','int');
		$objBudgettaskproduction->fk_user_create=GETPOST('fk_user_create','int');
		$objBudgettaskproduction->fk_user_mod=GETPOST('fk_user_mod','int');
		$objBudgettaskproduction->status=GETPOST('status','int');



		if (empty($objBudgettaskproduction->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objBudgettaskproduction->update($user);
			if ($result > 0)
			{
				$action='view';
			}
			else
			{
				// Creation KO
				if (! empty($objBudgettaskproduction->errors)) setEventMessages(null, $objBudgettaskproduction->errors, 'errors');
				else setEventMessages($objBudgettaskproduction->error, null, 'errors');
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
		$result=$objBudgettaskproduction->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/budget/list.php',1));
			exit;
		}
		else
		{
			if (! empty($objBudgettaskproduction->errors)) setEventMessages(null, $objBudgettaskproduction->errors, 'errors');
			else setEventMessages($objBudgettaskproduction->error, null, 'errors');
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
	print '<input type="hidden" name="id" value="'.$id.'">';
	print '<input type="hidden" name="idr" value="'.$idr.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
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
if ($idrd && $action == 'edit')
{
	print load_fiche_titre($langs->trans("MyModule"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$objBudgettaskproduction->id.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_budget_task").'</td><td><input class="flat" type="text" name="fk_budget_task" value="'.$objBudgettaskproduction->fk_budget_task.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_variable").'</td><td><input class="flat" type="text" name="fk_variable" value="'.$objBudgettaskproduction->fk_variable.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_budget_task_product").'</td><td><input class="flat" type="text" name="fk_budget_task_product" value="'.$objBudgettaskproduction->fk_budget_task_product.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldquantity").'</td><td><input class="flat" type="text" name="quantity" value="'.$objBudgettaskproduction->quantity.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td><input class="flat" type="text" name="active" value="'.$objBudgettaskproduction->active.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.$objBudgettaskproduction->fk_user_create.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.$objBudgettaskproduction->fk_user_mod.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.$objBudgettaskproduction->status.'"></td></tr>';

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
	$res = $objBudgettaskproduction->fetch_optionals($objBudgettaskproduction->id, $extralabels);


	print load_fiche_titre($langs->trans("Budgettaskproduction"));

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
			print_liste_field_titre($data['ref'],$_SERVER['PHP_SELF'],'','','','align="right"',$sortfield,$sortorder);

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
					$resip=$objBudgettaskproduction->fetch(0,$id,$j,$k);
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
				$formula = str_replace($variable,$value,$formula);
			}


			//vamos a reemplazar algunos
			if (count($aVarformula[$k])>0)
			{
				eval("\$result= $formula;");
				print price(price2num($result,$nDecimal));
				$aResult[$k] = $result;
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
		$units = ceil($nTimeproductionoftheactivity/$aResult[$k]);
		print $units;
		$aUnits[$k] = $units;
		print '</td>';
	}
	print '</tr>';

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

	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);
	// Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->budget->taskrend->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$objBudgettaskproduction->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}
	}
	print '</div>'."\n";
}

