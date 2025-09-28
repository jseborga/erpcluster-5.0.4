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
 *   	\file       productext/productbonus_card.php
 *		\ingroup    productext
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-03-20 17:42
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



$search_fk_product=GETPOST('search_fk_product','int');
$search_ref=GETPOST('search_ref','alpha');
$search_label=GETPOST('search_label','alpha');
$search_fk_bonus_type=GETPOST('search_fk_bonus_type','int');
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
//$result = restrictedArea($user, 'productext', $id);


$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($objProductbonus->table_element);

// Load object
//include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('productbonus'));

$optionsbonustype='';
$filter = " AND t.active = 1 AND t.status = 1 ";
$restype =$objCbonustype->fetchAll('ASC','t.label',0,0,array(),'AND',$filter);
if ($restype>0)
{
	$lines = $objCbonustype->lines;
	foreach ($lines AS $j => $line)
	{
		$aBonustype[$line->id] = $line->label.' ('.$line->ref.')';
		$selected = '';
		if ((GETPOST('fk_bonus_type')?GETPOST('fk_bonus_type'):$objProductbonus->fk_bonus_type) == $line->id) $selected = ' selected';
		$optionsbonustype.= '<option value="'.$line->id.'" '.$selected.'>'.$line->label.' '.$line->ref.'</option>';
	}
}
/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/


$form=new Form($db);


// Put here content of your page

// Example : Adding jquery code
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	function init_myfunc()
	{
		jQuery("#myid").removeAttr(\'disabled\');
		jQuery("#myid").attr(\'disabled\',\'disabled\');
	}
	init_myfunc();
	jQuery("#mybutton").click(function() {
		init_myfunc();
	});
});
</script>';


// Part to create
if ($action == 'create')
{
	print load_fiche_titre($langs->trans("NewMyModule"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$id.'">';
	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td><input class="flat" type="text" name="fk_product" value="'.GETPOST('fk_product').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.GETPOST('ref').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.GETPOST('label').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_bonus_type").'</td><td>';
	print $form->selectarray('fk_bonus_type',$aBonustype,GETPOST('fk_bonus_type'),1);
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Valuetocompare").'</td><td>';
	print $form->selectarray('type_value',$aTypevalue,(GETPOST('type_value')?GETPOST('type_value'):'Q'),1);
	print '</td></tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td>';
	print $form->selectyesno('active',GETPOST('active'),1);
	print '</td></tr>';
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
	print load_fiche_titre($langs->trans("MyModule"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="idr" value="'.$objProductbonus->id.'">';
	print '<input type="hidden" name="id" value="'.$id.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td><input class="flat" type="text" name="fk_product" value="'.GETPOST('fk_product').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.(GETPOST('ref')?GETPOST('ref'):$objProductbonus->ref).'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.(GETPOST('label')?GETPOST('label'):$objProductbonus->label).'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_bonus_type").'</td><td>';
	print $form->selectarray('fk_bonus_type',$aBonustype,(GETPOST('fk_bonus_type')?GETPOST('fk_bonus_type'):$objProductbonus->fk_bonus_type));
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_value").'</td><td>';
	print $form->selectarray('type_value',$aTypevalue,(GETPOST('type_value')?GETPOST('type_value'):$objProductbonus->type_value),1);
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td>';
	print $form->selectyesno('active',(GETPOST('active')?GETPOST('active'):$objProductbonus->active),1);
	print '</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.GETPOST('fk_user_create').'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.GETPOST('fk_user_mod').'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.GETPOST('status').'"></td></tr>';

	print '</table>'."\n";

	print '</table>';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}



// Part to show record
if ($objProductbonus->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create')))
{
	$res = $objProductbonus->fetch_optionals($objProductbonus->id, $extralabels);
	$restype = $objCbonustype->fetch($objProductbonus->fk_bonus_type);

	$head = bonus_prepare_head($objProductbonus);
	$titre=$langs->trans('Productbonus');
	dol_fiche_head($head, 'card', $titre, 0, 'bonus');


	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id='.$id.'&idr=' . $objProductbonus->id, $langs->trans('DeleteProductbonus'), $langs->trans('ConfirmDeleteProductbonus'), 'confirm_delete', '', 1, 1);
		print $formconfirm;
	}
	if ($action == 'validate') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id='.$id.'&idr=' . $objProductbonus->id, $langs->trans('ValidateProductbonus'), $langs->trans('ConfirmValidateProductbonus'), 'confirm_validate', '', 1, 2);
		print $formconfirm;
	}
	if ($action == 'novalidate') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id='.$id.'&idr=' . $objProductbonus->id, $langs->trans('NovalidateProductbonus'), $langs->trans('ConfirmNovalidateProductbonus'), 'confirm_novalidate', '', 1, 2);
		print $formconfirm;
	}

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>'.$objProductbonus->label.'</td></tr>';
	//
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td>'.$objProductbonus->fk_product.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldref").'</td><td>'.$objProductbonus->ref.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldlabel").'</td><td>'.$objProductbonus->label.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldfk_bonus_type").'</td><td>'.$objCbonustype->getNomUrl().'</td></tr>';
	print '<tr><td>'.$langs->trans("Valuetocompare").'</td><td>'.($objProductbonus->type_value=='Q'?$langs->trans('Quantity'):$langs->trans('Value')).'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldactive").'</td><td>'.($objProductbonus->active?$langs->trans('Yes'):$langs->trans('Not')).'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td>'.$objProductbonus->fk_user_create.'</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td>'.$objProductbonus->fk_user_mod.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldstatus").'</td><td>'.$objProductbonus->getLibStatut(6).'</td></tr>';

	print '</table>';

	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->productext->bonus->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$id.'&idr='.$objProductbonus->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}
		if ($user->rights->productext->bonus->val)
		{
			if ($objProductbonus->status==0)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$id.'&idr='.$objProductbonus->id.'&amp;action=validate">'.$langs->trans('Validate').'</a></div>'."\n";
		}
		else
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$id.'&idr='.$objProductbonus->id.'&amp;action=novalidate">'.$langs->trans('Novalidate').'</a></div>'."\n";
		}
		}
		if ($user->rights->productext->bonus->del)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$id.'&idr='.$objProductbonus->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
		}
	}
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	// Show links to link elements
	//$linktoelem = $form->showLinkToObjectBlock($object, null, array('productbonus'));
	//$somethingshown = $form->showLinkedObjectBlock($object, $linktoelem);

}


