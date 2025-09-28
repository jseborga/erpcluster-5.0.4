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
 *   	\file       promotion/promotions_card.php
 *		\ingroup    promotion
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-02-26 12:59
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
$search_ref=GETPOST('search_ref','alpha');
$search_label=GETPOST('search_label','alpha');
$search_detail=GETPOST('search_detail','alpha');
$search_fk_product=GETPOST('search_fk_product','int');
$search_fk_type_promotion=GETPOST('search_fk_type_promotion','int');
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
//$result = restrictedArea($user, 'promotion', $id);



// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('promotions'));




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

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
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td><input class="flat" type="text" name="entity" value="'.GETPOST('entity').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" maxlength="30" value="'.GETPOST('ref').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" maxlength="150" name="label" value="'.GETPOST('label').'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fielddetail").'</td><td><input class="flat" type="text" name="detail" value="'.GETPOST('detail').'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldqty").'</td><td><input class="flat" type="number" name="qty" min="0" step="any" value="'.GETPOST('qty').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_type_promotion").'</td><td>';
	print '<select name="fk_type_promotion">'.$optionstype.'</select>';
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
if (($idr) && $action == 'edit')
{
	print load_fiche_titre($langs->trans("MyModule"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	print '<input type="hidden" name="idr" value="'.$objPromotion->id.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td><input class="flat" type="text" name="entity" value="'.$objPromotion->entity.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" maxlength="30" name="ref" value="'.(GETPOST('ref')?GETPOST('ref'):$objPromotion->ref).'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" maxlength="150" name="label" value="'.(GETPOST('label')?GETPOST('label'):$objPromotion->label).'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fielddetail").'</td><td><input class="flat" type="text" name="detail" value="'.(GETPOST('detail')?GETPOST('detail'):$objPromotion->detail).'"></td></tr>';
	print '<tr><td>'.$langs->trans("Fieldqty").'</td><td><input class="flat" type="number" name="qty" min="0" step="any" value="'.(GETPOST('qty')?GETPOST('qty'):$objPromotion->qty).'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_type_promotion").'</td><td>';
	print '<select name="fk_type_promotion">'.$optionstype.'</select>';
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldactive").'</td><td>';
	print $form->selectyesno('active',(GETPOST('active')?GETPOST('active'):$objPromotion->active),1);
	print '</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.$objPromotion->fk_user_create.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.$objPromotion->fk_user_mod.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.$objPromotion->status.'"></td></tr>';

	print '</table>';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}



// Part to show record
if ($objPromotion->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create')))
{
	$res = $objPromotion->fetch_optionals($objPromotion->id, $extralabelsprom);

	print load_fiche_titre($langs->trans("Promotions"));

	$head = promotion_prepare_head($objPromotion);
	$titre=$langs->trans('Promotions');
	dol_fiche_head($head, 'card', $titre, 0, 'bill');


	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id='.$id.'&idr=' . $objPromotion->id, $langs->trans('Deletepromotion'), $langs->trans('ConfirmDeletepromotion'), 'confirm_delete', '', 1, 1);
		print $formconfirm;
	}
	if ($action == 'validate') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id='.$id.'&idr=' . $objPromotion->id, $langs->trans('Validatepromotion'), $langs->trans('ConfirmValidatepromotion'), 'confirm_validate', '', 1, 1);
		print $formconfirm;
	}
	if ($action == 'novalidate') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id='.$id.'&idr=' . $objPromotion->id, $langs->trans('Novalidatepromotion'), $langs->trans('ConfirmNovalidatepromotion'), 'confirm_novalidate', '', 1, 2);
		print $formconfirm;
	}

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>'.$objPromotion->label.'</td></tr>';
	//
	//print '<tr><td>'.$langs->trans("Fieldentity").'</td><td>'.$objPromotion->entity.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldref").'</td><td>'.$objPromotion->ref.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldlabel").'</td><td>'.$objPromotion->label.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fielddetail").'</td><td>'.$objPromotion->detail.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldqty").'</td><td>'.price($objPromotion->qty).'</td></tr>';
	$objCtypepromotion->fetch($objPromotion->fk_type_promotion);
	print '<tr><td>'.$langs->trans("Fieldfk_type_promotion").'</td><td>'.$objCtypepromotion->getNomUrl().'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldactive").'</td><td>'.($objPromotion->active?$langs->trans('Yes'):$langs->trans('Not')).'</td></tr>';
	$objUser->fetch($objPromotion->fk_user_create);
	print '<tr><td>'.$langs->trans("Fieldfk_user_create").'</td><td>'.$objUser->getNomUrl(1).'</td></tr>';
	$objUser->fetch($objPromotion->fk_user_mod);
	print '<tr><td>'.$langs->trans("Fieldfk_user_mod").'</td><td>'.$objUser->getNomUrl(1).'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldstatus").'</td><td>'.$objPromotion->getLibStatut(6).'</td></tr>';

	print '</table>';

	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if (empty($objPromotion->status))
		{
			if ($user->rights->productext->prom->val)
			{
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$id.'&idr='.$objPromotion->id.'&amp;action=validate">'.$langs->trans('Validate').'</a></div>'."\n";
			}
			if ($user->rights->productext->prom->write)
			{
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$id.'&idr='.$objPromotion->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
			}

			if ($user->rights->productext->prom->del)
			{
				print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$id.'&idr='.$objPromotion->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
			}

		}
		if ($objPromotion->status == 1)
		{
			if ($user->rights->productext->prom->val)
			{
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$id.'&idr='.$objPromotion->id.'&amp;action=novalidate">'.$langs->trans('Novalidate').'</a></div>'."\n";
			}

		}
	}
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	// Show links to link elements
	//$linktoelem = $form->showLinkToObjectBlock($object, null, array('promotions'));
	//$somethingshown = $form->showLinkedObjectBlock($object, $linktoelem);

}

