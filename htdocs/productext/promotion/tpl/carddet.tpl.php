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


// Part to show record
if ($objPromotion->id > 0)
{
	$res = $objPromotion->fetch_optionals($objPromotion->id, $extralabelsprom);

	print load_fiche_titre($langs->trans("Promotions"));

	$head = promotion_prepare_head($objPromotion);
	$titre=$langs->trans('Promotions');
	dol_fiche_head($head, 'carddet', $titre, 0, 'bill');


	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id='.$id.'&idr=' . $idr.'&idrd='.$objPromotiondet->id, $langs->trans('Deletepromotion'), $langs->trans('ConfirmDeletepromotion'), 'confirm_delete', '', 0, 2);
		print $formconfirm;


	}
	if ($action == 'validate') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id='.$id.'&idr=' . $objPromotion->id, $langs->trans('ValidatePromotion'), $langs->trans('ConfirmValidatepromotion'), 'confirm_validate', '', 0, 2);
		print $formconfirm;
	}

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>'.$objPromotion->label.'</td></tr>';
	//
	//print '<tr><td>'.$langs->trans("Fieldentity").'</td><td>'.$objPromotion->entity.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldref").'</td><td>'.$objPromotion->ref.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldlabel").'</td><td>'.$objPromotion->label.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fielddetail").'</td><td>'.$objPromotion->detail.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fortheamount").'</td><td>'.$objPromotion->qty.'</td></tr>';
	$objCtypepromotion->fetch($objPromotion->fk_type_promotion);
	print '<tr><td>'.$langs->trans("Fieldfk_type_promotion").'</td><td>'.$objCtypepromotion->getNomUrl().'</td></tr>';
	//print '<tr><td>'.$langs->trans("Fieldactive").'</td><td>'.($objPromotion->active?$langs->trans('Yes'):$langs->trans('Not')).'</td></tr>';
	//$objUser->fetch($objPromotion->fk_user_create);
	//print '<tr><td>'.$langs->trans("Fieldfk_user_create").'</td><td>'.$objUser->getNomUrl(1).'</td></tr>';
	//$objUser->fetch($objPromotion->fk_user_mod);
	//print '<tr><td>'.$langs->trans("Fieldfk_user_mod").'</td><td>'.$objUser->getNomUrl(1).'</td></tr>';
	//print '<tr><td>'.$langs->trans("Fieldstatus").'</td><td>'.$objPromotion->getLibStatut(6).'</td></tr>';

	print '</table>';

	dol_fiche_end();

	include_once DOL_DOCUMENT_ROOT.'/productext/promotion/tpl/listdet.tpl.php';




	// Example 2 : Adding links to objects
	// Show links to link elements
	//$linktoelem = $form->showLinkToObjectBlock($object, null, array('promotions'));
	//$somethingshown = $form->showLinkedObjectBlock($object, $linktoelem);

}

