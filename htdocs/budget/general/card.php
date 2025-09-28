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
 *   	\file       budget/budgetgeneral_card.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-11-07 10:05
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

dol_include_once('/budget/class/budgetgeneral.class.php');
dol_include_once('/budget/class/budgetconcept.class.php');
dol_include_once('/budget/class/parametercalculation.class.php');

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';

// Load traductions files requiredby by page
$langs->load("budget");
$langs->load("other");
$langs->load("admin");
$langs->load("companies");
// Get parameters
$id			= GETPOST('id','int');
$idr		= GETPOST('idr','int');
//$subaction	= GETPOST('subaction','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');
$fk_budget = $object->id;

$search_fk_budget=GETPOST('search_fk_budget','int');
$search_exchange_rate=GETPOST('search_exchange_rate','alpha');
$search_second_currency=GETPOST('search_second_currency','int');
$search_decimal_quant=GETPOST('search_decimal_quant','int');
$search_decimal_pu=GETPOST('search_decimal_pu','int');
$search_decimal_total=GETPOST('search_decimal_total','int');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_status=GETPOST('search_status','int');



// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
$lAccess = $user->rights->budget->par->leer+$user->rights->budget->par->write+$user->rights->budget->par->mod;
if ($lAccess <=0)
{
	print '<div class="error">'.$langs->trans('Errornotauthorized').'</div>';
	exit;
}

if (empty($subaction) && empty($id) && empty($ref)) $subaction='list';

// Load general if id or ref is provided as parameter
$general=new Budgetgeneral($db);
if ($fk_budget > 0 && $subaction != 'add')
{
	$result=$general->fetch($idr,$fk_budget);
	if ($result < 0) dol_print_error($db);
	elseif ($result == 0) $subaction = 'create';
}
$parameter = new Parametercalculation($db);
$concept = new Budgetconcept($db);
$filtercon = " AND t.fk_budget = ".$id;
$nb = $concept->fetchAll('', '',0,0,array(1=>1),'AND',$filtercon,false);
if ($nb <=0)
{
	//creamos por defecto para el proyecto los valores standard no modificables
	$idBudget = $id;
	include DOL_DOCUMENT_ROOT.'/budget/include/add_concept.inc.php';
}

// Initialize technical general to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('budgetgeneral'));
$extrafields = new ExtraFields($db);

if (GETPOST('cancel') && GETPOST('cancel') == $langs->trans('Cancel'))
{
	$subaction = '';
}

/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

//llxHeader('','MyPageName','');

$form=new Formv($db);


// Put here content of your page

// Example : Adding jquery code

// Part to create
if ($subaction == 'create')
{
	print load_fiche_titre($langs->trans("Parameters"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="gen">';
	print '<input type="hidden" name="subaction" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';

	dol_fiche_head();
	$var=!$var;
	print '<table class="border centpercent">'."\n";
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldexchange_rate").'</td><td><input class="form-control bfh-number" type="number" min="0" step="any" name="exchange_rate" value="'.GETPOST('exchange_rate').'"></td></tr>';
	$var=!$var;
	print '<tr '.$bc[$var].'><td class="fieldrequired">'.$langs->trans("Fieldbase_currency").'</td><td>';
	print $form->selectCurrency_((GETPOST('base_currency')?GETPOST('base_currency'):$conf->currency),"base_currency");
	print '</td></tr>'."\n";
	$var=!$var;
	print '<tr '.$bc[$var].'><td class="fieldrequired">'.$langs->trans("Fieldsecond_currency").'</td><td>';
	print $form->selectCurrency_((GETPOST('second_currency')?GETPOST('second_currency'):''),"second_currency");
	print '</td></tr>'."\n";
	$var=!$var;
	print '<tr '.$bc[$var].'><td class="fieldrequired">'.$langs->trans("Fielddecimal_quant").'</td><td><input class="form-control bfh-number" type="number" name="decimal_quant" value="'.(GETPOST('decimal_quant')?GETPOST('decimal_quant'):2).'" min="1" max="10"></td></tr>';
	$var=!$var;
	print '<tr '.$bc[$var].'><td class="fieldrequired">'.$langs->trans("Fielddecimal_pu").'</td><td><input class="form-control bfh-number" type="number" name="decimal_pu" value="'.(GETPOST('decimal_pu')?GETPOST('decimal_pu'):2).'" min="1" max="10"></td></tr>';
	$var=!$var;
	print '<tr '.$bc[$var].'><td class="fieldrequired">'.$langs->trans("Fielddecimal_total").'</td><td><input class="form-control bfh-number" type="number" name="decimal_total" value="'.(GETPOST('decimal_total')?GETPOST('decimal_total'):2).'" min="1" max="10"></td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="butAction" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="butActionDelete" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}



// Part to edit record
if (($idr) && $subaction == 'edit')
{
	print load_fiche_titre($langs->trans("Parameters"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="gen">';
	print '<input type="hidden" name="subaction" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	print '<input type="hidden" name="idr" value="'.$general->id.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldexchange_rate").'</td><td><input class="form-control flat" type="number" step="any" min="0" name="exchange_rate" value="'.$general->exchange_rate.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbase_currency").'</td><td>';
	print $form->selectCurrency_((GETPOST('base_currency')?GETPOST('base_currency'):$general->base_currency),"base_currency");
	print '</td></tr>'."\n";
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldsecond_currency").'</td><td>';
	print $form->selectCurrency_((GETPOST('second_currency')?GETPOST('second_currency'):$general->second_currency),"second_currency");
	print '</td></tr>'."\n";
	print '<tr><td class="fieldrequired">'.$langs->trans("Fielddecimal_quant").'</td><td><input class="form-control bfh-number" type="number" min="0" max="10" name="decimal_quant" value="'.$general->decimal_quant.'" min="1" max="10"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fielddecimal_pu").'</td><td><input class="form-control bfh-number" type="number" min="0" max="10" name="decimal_pu" value="'.$general->decimal_pu.'" min="1" max="10"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fielddecimal_total").'</td><td><input class="form-control bfh-number" type="number" min="0" max="10" name="decimal_total" value="'.$general->decimal_total.'" min="1" max="10"></td></tr>';
	print '</table>';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="butAction" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="butActionDelete" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}
// Part to show record
if ($general->id && (empty($subaction) || $subaction == 'view' || $subaction == 'delete'))
{
	print load_fiche_titre($langs->trans("Generalparameters"));

	dol_fiche_head();

	if ($subaction == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $general->id, $langs->trans('DeleteMyGeneral'), $langs->trans('ConfirmDeleteMygeneral'), 'confirm_deletegen', '', 0, 1);
		print $formconfirm;
	}

	print '<table class="border centpercent">'."\n";
	print '<tr><td>'.$langs->trans("Companycurrency").'</td><td>';
	print currency_name($general->base_currency,1);
	print ' ('.$conf->currency;
	print ($conf->currency != $langs->getCurrencySymbol($conf->currency) ? ' - '.$langs->getCurrencySymbol($conf->currency) : '');
	print ')';
	print '</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldexchange_rate").'</td><td>'.$general->exchange_rate.'</td></tr>';

	print '<tr><td>'.$langs->trans("Fieldsecond_currency").'</td><td>';
	print currency_name($general->second_currency,1);
	print ' ('.$general->second_currency;
	print ($general->second_currency != $langs->getCurrencySymbol($general->second_currency) ? ' - '.$langs->getCurrencySymbol($general->second_currency) : '');
	print ')';
	print '</td></tr>';
	print '<tr><td>'.$langs->trans("Fielddecimal_quant").'</td><td>'.$general->decimal_quant.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fielddecimal_pu").'</td><td>'.$general->decimal_pu.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fielddecimal_total").'</td><td>'.$general->decimal_total.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td>'.$general->fk_user_create.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td>'.$general->fk_user_mod.'</td></tr>';

	print '</table>';

	dol_fiche_end();

	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$general,$action);    // Note that $action and $general may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->budget->par->mod && $object->fk_statut == 0)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$general->fk_budget.'&idr='.$general->id.'&amp;action=gen&subaction=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->budget->par->del)
		{
			//print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$general->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
		}
	}
	print '</div>'."\n";


	// Example 2 : Adding links to generals
	//$somethingshown=$form->showLinkedgeneralBlock($general);
	//$linktoelem = $form->showLinkTogeneralBlock($general);
	//if ($linktoelem) print '<br>'.$linktoelem;

}

//mostramos los parametros de calculo
// Part to show record
if ($user->rights->budget->par->leer && $subaction != 'edit')
{
	//$text = 'Esto es una prueba';
	$notabs = 2;
	//print $tooltip = $form->textwithtooltip($text, $htmltext, 1, 0, $img, $extracss, $notabs, img_picto('', 'rightarrow'), $noencodehtmltext);
	//$tooltip = $form->textwithtooltip($text,$description,3,'','',$cursorline,0,(!empty($line->fk_parent_line)?img_picto('', 'rightarrow'):''));

	print load_fiche_titre($langs->trans("Calculationparameters").' '.$tooptip);

	if ($subaction == 'delete_concept')
	{
		$concept->fetch(GETPOST('idr'));
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id='.$id.'&action=gen&idr='.GETPOST('idr'), $langs->trans('DeleteConcept'), $langs->trans('ConfirmDeleteConcept').': '.$langs->trans($concept->label), 'confirm_delete_concept', '', 0, 1);
		print $formconfirm;
	}
	$concept = new Budgetconcept($db);
	$filtercon = " AND t.fk_budget = ".$id;
	$nb = $concept->fetchAll('', '',0,0,array(1=>1),'AND',$filtercon,false);
	$lines = $concept->lines;
	$exclude = '';
	$aDelete = array();
	foreach ((array) $lines AS $j => $line)
	{
		//verificamos si los parametros existen
		$res = $parameter->fetch(0,$line->ref);
		if ($res <= 0) $aDelete[$line->id] = $line->id;
		if (!empty($exclude)) $exclude.=',';
		$exclude.="'".$line->ref."'";
	}
	if ($exclude)
		$filterstatic = " AND t.code NOT IN (".$exclude.")";
	$filterstatic.= " AND t.active = 1";
	$filterstatic.= " AND t.status = 1";
	$nbp = $parameter->fetchAll('ASC','t.label',0,0,array(1=>1),'AND',$filterstatic);
	$options = '<option value="0">&nbsp;</option>';
	if ($nbp>0)
	{
		foreach ($parameter->lines AS $j => $line)
			$options.= '<option value="'.$line->code.'">'.$line->label.'</option>';
	}

	if ($subaction == 'editc' || $subaction == 'createc')
	{
		print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
		print '<input type="hidden" name="action" value="gen">';
		if ($subaction == 'editc')
			print '<input type="hidden" name="subaction" value="updateconcept">';
		else
			print '<input type="hidden" name="subaction" value="addconcept">';
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
		print '<input type="hidden" name="id" value="'.$object->id.'">';
	}
	dol_fiche_head();
	print '<table class="border centpercent">'."\n";
	print '<thead>';
	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans('Label'),'','','',$params,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Value'),'','','',$params,'',$sortfield,$sortorder);
	if ($subaction != 'editc')
		print_liste_field_titre($langs->trans('Action'),'','','',$params,'align="right"',$sortfield,$sortorder);
	print '</tr>';
	print '</thead>';
	print '<tbody>';
	if ($subaction == 'createc' && $nbp>0)
	{
		$var = !$var;
		print "<tr $bc[$var]>";
		if ($nbp>0)
			print '<td>'.'<select name="code">'.$options.'</select>';
		else
			print '<td>'.$langs->trans('No esta definido los parametros').'</td>';
		print '<td align="right">'.'<input type="number" min="0" step="any" name="amount" value="'.GETPOST('amount').'">'.'</td>';
		print '<td align="right"></td>';
		print '</tr>';
	}
	if ($nb >0)
	{
		//recorremos y guardamos
		$lVerif = true;
		if (count($aDelete)>0)
		{
			setEventMessages($langs->trans('Precaución, verifique los parametros de calculo, no existen'),null,'errors');
			$lVerif = false;
		}
		foreach ($lines AS $j => $line)
		{

			$var = !$var;
			print "<tr $bc[$var]>";
			if ($subaction == 'editc')
			{
				print '<td>'.$langs->trans($line->label).'</td>';
				print '<td align="right">'.'<input type="number" step="any" min="0" max="100" name="amount['.$line->ref.']" value="'.$line->amount.'">'.'</td>';
				print '<td></td>';
			}
			else
			{
				print '<td>'.$langs->trans($line->label).'</td>';
				print '<td align="right">'.$line->amount.'</td>';
				if ($line->status ==2 && $user->rights->budget->par->del && $subaction != 'editc' && $object->fk_statut == 0 )
				{
					if ($lVerif)
						print '<td align="right">'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$line->id.'&action=gen&subaction=delete_concept">'.img_picto($langs->trans('Delete'),'delete').'</a>'.'</td>';
					else
					{
						if ($aDelete[$line->id])
							print '<td align="right">'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$line->id.'&action=gen&subaction=delete_concept">'.img_picto($langs->trans('Delete'),'delete').'</a>'.'</td>';
					}
				}
				else
					print '<td></td>';
			}
			print '</tr>';
		}
	}
	print '</tbody>';
	print '</table>';

	dol_fiche_end();


	if ($subaction == 'editc' || $subaction == 'createc')
	{
		print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
		print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
		print '</div>';
		print '</form>';
	}

	if (empty($subaction) || $subaction == 'view')
	{
		// Buttons
		print '<div class="tabsAction">'."\n";
		$parameters=array();
		$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$general,$action);
		// Note that $action and $general may have been modified by hook
		if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

		if (empty($reshook))
		{
			if (count($aDelete)==0)
			{
				if ($user->rights->budget->par->write && $nbp>0  && $object->fk_statut == 0)
				{
					print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$general->fk_budget.'&idr='.$general->id.'&amp;action=gen&subaction=createc">'.$langs->trans("Create").'</a></div>'."\n";
				}
				if ($user->rights->budget->par->mod && $object->fk_statut == 0)
				{
					print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$general->fk_budget.'&idr='.$general->id.'&amp;action=gen&subaction=editc">'.$langs->trans("Modify").'</a></div>'."\n";
				}
			}
			//else
		}
		print '</div>'."\n";
	}
}


// End of page
//llxFooter();
//$db->close();
