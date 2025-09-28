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
 *   	\file       budget/productbudget_card.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-04-23 15:41
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
		if ($id > 0 || ! empty($ref)) $ret = $object->fetch($id,$ref);
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

		$object->fk_product=GETPOST('fk_product','int');
		$object->fk_budget=GETPOST('fk_budget','int');
		$object->ref=GETPOST('ref','alpha');
		$object->label=GETPOST('label','alpha');
		$object->fk_unit=GETPOST('fk_unit','int');
		$object->code_structure=GETPOST('code_structure','alpha');
		$object->group_structure=GETPOST('group_structure','alpha');
		$object->formula=GETPOST('formula','alpha');
		$object->units=GETPOST('units','int');
		$object->commander=GETPOST('commander','int');
		$object->price_productive=GETPOST('price_productive','alpha');
		$object->price_improductive=GETPOST('price_improductive','alpha');
		$object->active=GETPOST('active','int');
		$object->fk_object=GETPOST('fk_object','int');
		$object->quant=GETPOST('quant','alpha');
		$object->percent_prod=GETPOST('percent_prod','alpha');
		$object->amount_noprod=GETPOST('amount_noprod','alpha');
		$object->amount=GETPOST('amount','alpha');
		$object->work_hours=GETPOST('work_hours','alpha');
		$object->fk_user_create=GETPOST('fk_user_create','int');
		$object->fk_user_mod=GETPOST('fk_user_mod','int');
		$object->status=GETPOST('status','int');



		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$object->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/list.php',1);
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


		$object->fk_product=GETPOST('fk_product','int');
		$object->fk_budget=GETPOST('fk_budget','int');
		$object->ref=GETPOST('ref','alpha');
		$object->label=GETPOST('label','alpha');
		$object->fk_unit=GETPOST('fk_unit','int');
		$object->code_structure=GETPOST('code_structure','alpha');
		$object->group_structure=GETPOST('group_structure','alpha');
		$object->formula=GETPOST('formula','alpha');
		$object->units=GETPOST('units','int');
		$object->commander=GETPOST('commander','int');
		$object->price_productive=GETPOST('price_productive','alpha');
		$object->price_improductive=GETPOST('price_improductive','alpha');
		$object->active=GETPOST('active','int');
		$object->fk_object=GETPOST('fk_object','int');
		$object->quant=GETPOST('quant','alpha');
		$object->percent_prod=GETPOST('percent_prod','alpha');
		$object->amount_noprod=GETPOST('amount_noprod','alpha');
		$object->amount=GETPOST('amount','alpha');
		$object->work_hours=GETPOST('work_hours','alpha');
		$object->fk_user_create=GETPOST('fk_user_create','int');
		$object->fk_user_mod=GETPOST('fk_user_mod','int');
		$object->status=GETPOST('status','int');



		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
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
			header("Location: ".dol_buildpath('/budget/list.php',1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else setEventMessages($object->error, null, 'errors');
		}
	}
}

$aStrgroupcat = $aStrbudget[$id]['aStrgroupcat'];
$aCategorie = $aStrbudget[$id]['aStrlabel'];
if (count($aStrgroupcat)>0)
{
	foreach ($aStrgroupcat AS $group_structure => $fk_categorie)
	{
		$aStructure[$fk_categorie] = $aCategorie[$fk_categorie];
	}
}

/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/
$form=new Form($db);


// Put here content of your page


// Part to create
if ($action == 'changefactor')
{
	print load_fiche_titre($langs->trans("NewMyModule"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="processfactor">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$id.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";

	//Categorie
	print '<tr><td class="fieldrequired">'.$langs->trans("Selectstructure").'</td><td>';
	print $form->selectarray('fk_categorie',$aStructure,GETPOST('fk_categorie'),1);
	print '</td></tr>';
	//factor
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfactor").'</td><td><input class="flat" type="number" min="0" step="any" name="factor" value="'.GETPOST('factor').'"></td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Process").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}
if ($action == 'processfactor')
{
	$factor = GETPOST('factor','int');

	//vamos a mostrar un resultado parcial
	$filter = " AND t.fk_budget = ".$id;
	$filter.= " AND t.code_structure = '".GETPOST('fk_categorie')."'";

	$res = $objProductbudget->fetchAll('ASC','t.ref',0,0,array(),'AND',$filter);
	if ($res>0)
	{
		print load_fiche_titre($langs->trans("NewMyModule"));

		print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
		print '<input type="hidden" name="action" value="confirmfactor">';
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
		print '<input type="hidden" name="fk_categorie" value="'.GETPOST('fk_categorie').'">';
		print '<input type="hidden" name="factor" value="'.GETPOST('factor').'">';
		print '<input type="hidden" name="id" value="'.$id.'">';

		dol_fiche_head();
		print '<table class="border centpercent">'."\n";

		//Categorie
			print '<tr><td width="20%">'.$langs->trans("Selectstructure").'</td><td>';
			print $aCategorie[GETPOST('fk_categorie')];
			print '</td></tr>';
			//factor
			print '<tr><td>'.$langs->trans("Fieldfactor").'</td><td>'.price(GETPOST('factor')).'</td></tr>';

		print '</table>'."\n";
		dol_fiche_end();

		dol_fiche_head();
		print '<table class="border centpercent">'."\n";
		print '<tr class="liste_titre">';
		print_liste_field_titre($langs->trans('Ref'),$_SERVER['PHP_SELF'],'','',$params,'align="left"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Label'),$_SERVER['PHP_SELF'],'','',$params,'align="left"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Priceunitsant'),$_SERVER['PHP_SELF'],'','',$params,'align="right"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Pricenew'),$_SERVER['PHP_SELF'],'','',$params,'align="right"',$sortfield,$sortorder);
		print '</tr>';

		$lines = $objProductbudget->lines;
		foreach ($lines AS $j => $line)
		{
			$objProductbudget->id=$line->id;
			$objProductbudget->ref = $line->ref;
			$objProductbudget->label = $line->label;

			$var = !$var;
			print '<tr '.$bc[$var].'>';
			print '<td>'.$objProductbudget->getNomUrl().'</td>';
			print '<td>'.$line->label.'</td>';
			print '<td align="right">'.$line->amount.'</td>';
			$amountnew = $line->amount * $factor;
			print '<td align="right">'.$amountnew.'</td>';
			print '</tr>';
		}
		print '</table>';
		dol_fiche_end();
		print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Confirmchange").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

		print '</form>';
	}

}

