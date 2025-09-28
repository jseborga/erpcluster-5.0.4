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
 *   	\file       productext/productregionprice_card.php
 *		\ingroup    productext
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-04-12 15:25
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
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
dol_include_once('/productext/class/productregionprice.class.php');

// Load traductions files requiredby by page
$langs->load("productext");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$cancel     = GETPOST('cancel');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_fk_region_geographic=GETPOST('search_fk_region_geographic','int');
$search_fk_soc=GETPOST('search_fk_soc','int');
$search_fk_product=GETPOST('search_fk_product','int');
$search_price=GETPOST('search_price','alpha');
$search_quantity=GETPOST('search_quantity','alpha');
$search_remise_percent=GETPOST('search_remise_percent','alpha');
$search_remise=GETPOST('search_remise','alpha');
$search_tva_tx=GETPOST('search_tva_tx','alpha');
$search_default_vat_code=GETPOST('search_default_vat_code','alpha');
$search_info_bits=GETPOST('search_info_bits','int');
$search_fk_user=GETPOST('search_fk_user','int');
$search_import_key=GETPOST('search_import_key','alpha');



if (empty($action) && empty($id) && empty($ref)) $action='view';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
//$result = restrictedArea($user, 'productext', $id);


$objProductregionprice = new Productregionprice($db);
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($objProductregionprice->table_element);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('productregionprice'));



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$objProductregionprice,$action);    // Note that $action and $objProductregionprice may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	if ($cancel)
	{
		if ($action != 'addlink')
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/productext/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}
		if ($id > 0 || ! empty($ref)) $ret = $objProductregionprice->fetch($id,$ref);
		$action='';
	}

	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/productext/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */

	$objProductregionprice->entity=GETPOST('entity','int');
	$objProductregionprice->fk_region_geographic=GETPOST('fk_region_geographic','int');
	$objProductregionprice->fk_soc=GETPOST('fk_soc','int');
	$objProductregionprice->fk_product=GETPOST('fk_product','int');
	$objProductregionprice->price=GETPOST('price','alpha');
	$objProductregionprice->quantity=GETPOST('quantity','alpha');
	$objProductregionprice->remise_percent=GETPOST('remise_percent','alpha');
	$objProductregionprice->remise=GETPOST('remise','alpha');
	$objProductregionprice->tva_tx=GETPOST('tva_tx','alpha');
	$objProductregionprice->default_vat_code=GETPOST('default_vat_code','alpha');
	$objProductregionprice->info_bits=GETPOST('info_bits','int');
	$objProductregionprice->fk_user=GETPOST('fk_user','int');
	$objProductregionprice->import_key=GETPOST('import_key','alpha');



		if (empty($objProductregionprice->ref))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objProductregionprice->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/productext/list.php',1);
				header("Location: ".$urltogo);
				exit;
			}
			{
				// Creation KO
				if (! empty($objProductregionprice->errors)) setEventMessages(null, $objProductregionprice->errors, 'errors');
				else  setEventMessages($objProductregionprice->error, null, 'errors');
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


	$objProductregionprice->entity=GETPOST('entity','int');
	$objProductregionprice->fk_region_geographic=GETPOST('fk_region_geographic','int');
	$objProductregionprice->fk_soc=GETPOST('fk_soc','int');
	$objProductregionprice->fk_product=GETPOST('fk_product','int');
	$objProductregionprice->price=GETPOST('price','alpha');
	$objProductregionprice->quantity=GETPOST('quantity','alpha');
	$objProductregionprice->remise_percent=GETPOST('remise_percent','alpha');
	$objProductregionprice->remise=GETPOST('remise','alpha');
	$objProductregionprice->tva_tx=GETPOST('tva_tx','alpha');
	$objProductregionprice->default_vat_code=GETPOST('default_vat_code','alpha');
	$objProductregionprice->info_bits=GETPOST('info_bits','int');
	$objProductregionprice->fk_user=GETPOST('fk_user','int');
	$objProductregionprice->import_key=GETPOST('import_key','alpha');



		if (empty($objProductregionprice->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objProductregionprice->update($user);
			if ($result > 0)
			{
				$action='view';
			}
			else
			{
				// Creation KO
				if (! empty($objProductregionprice->errors)) setEventMessages(null, $objProductregionprice->errors, 'errors');
				else setEventMessages($objProductregionprice->error, null, 'errors');
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
		$result=$objProductregionprice->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/productext/list.php',1));
			exit;
		}
		else
		{
			if (! empty($objProductregionprice->errors)) setEventMessages(null, $objProductregionprice->errors, 'errors');
			else setEventMessages($objProductregionprice->error, null, 'errors');
		}
	}
}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('','MyPageName','');

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

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td><input class="flat" type="text" name="entity" value="'.GETPOST('entity').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_region_geographic").'</td><td><input class="flat" type="text" name="fk_region_geographic" value="'.GETPOST('fk_region_geographic').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_soc").'</td><td><input class="flat" type="text" name="fk_soc" value="'.GETPOST('fk_soc').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td><input class="flat" type="text" name="fk_product" value="'.GETPOST('fk_product').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice").'</td><td><input class="flat" type="text" name="price" value="'.GETPOST('price').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldquantity").'</td><td><input class="flat" type="text" name="quantity" value="'.GETPOST('quantity').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldremise_percent").'</td><td><input class="flat" type="text" name="remise_percent" value="'.GETPOST('remise_percent').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldremise").'</td><td><input class="flat" type="text" name="remise" value="'.GETPOST('remise').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtva_tx").'</td><td><input class="flat" type="text" name="tva_tx" value="'.GETPOST('tva_tx').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddefault_vat_code").'</td><td><input class="flat" type="text" name="default_vat_code" value="'.GETPOST('default_vat_code').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldinfo_bits").'</td><td><input class="flat" type="text" name="info_bits" value="'.GETPOST('info_bits').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user").'</td><td><input class="flat" type="text" name="fk_user" value="'.GETPOST('fk_user').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldimport_key").'</td><td><input class="flat" type="text" name="import_key" value="'.GETPOST('import_key').'"></td></tr>';

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
	print '<input type="hidden" name="id" value="'.$objProductregionprice->id.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td><input class="flat" type="text" name="entity" value="'.$objProductregionprice->entity.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_region_geographic").'</td><td><input class="flat" type="text" name="fk_region_geographic" value="'.$objProductregionprice->fk_region_geographic.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_soc").'</td><td><input class="flat" type="text" name="fk_soc" value="'.$objProductregionprice->fk_soc.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td><input class="flat" type="text" name="fk_product" value="'.$objProductregionprice->fk_product.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice").'</td><td><input class="flat" type="text" name="price" value="'.$objProductregionprice->price.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldquantity").'</td><td><input class="flat" type="text" name="quantity" value="'.$objProductregionprice->quantity.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldremise_percent").'</td><td><input class="flat" type="text" name="remise_percent" value="'.$objProductregionprice->remise_percent.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldremise").'</td><td><input class="flat" type="text" name="remise" value="'.$objProductregionprice->remise.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtva_tx").'</td><td><input class="flat" type="text" name="tva_tx" value="'.$objProductregionprice->tva_tx.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddefault_vat_code").'</td><td><input class="flat" type="text" name="default_vat_code" value="'.$objProductregionprice->default_vat_code.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldinfo_bits").'</td><td><input class="flat" type="text" name="info_bits" value="'.$objProductregionprice->info_bits.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user").'</td><td><input class="flat" type="text" name="fk_user" value="'.$objProductregionprice->fk_user.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldimport_key").'</td><td><input class="flat" type="text" name="import_key" value="'.$objProductregionprice->import_key.'"></td></tr>';

	print '</table>';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}



// Part to show record
if ($objProductregionprice->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create')))
{
    $res = $objProductregionprice->fetch_optionals($objProductregionprice->id, $extralabels);


	print load_fiche_titre($langs->trans("MyModule"));

	dol_fiche_head();

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $objProductregionprice->id, $langs->trans('DeleteMyOjbect'), $langs->trans('ConfirmDeleteMyObject'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>'.$objProductregionprice->label.'</td></tr>';
	//
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td>'.$objProductregionprice->entity.'</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_region_geographic").'</td><td>'.$objProductregionprice->fk_region_geographic.'</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_soc").'</td><td>'.$objProductregionprice->fk_soc.'</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td>'.$objProductregionprice->fk_product.'</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice").'</td><td>'.$objProductregionprice->price.'</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldquantity").'</td><td>'.$objProductregionprice->quantity.'</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldremise_percent").'</td><td>'.$objProductregionprice->remise_percent.'</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldremise").'</td><td>'.$objProductregionprice->remise.'</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtva_tx").'</td><td>'.$objProductregionprice->tva_tx.'</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddefault_vat_code").'</td><td>'.$objProductregionprice->default_vat_code.'</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldinfo_bits").'</td><td>'.$objProductregionprice->info_bits.'</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user").'</td><td>'.$objProductregionprice->fk_user.'</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldimport_key").'</td><td>'.$objProductregionprice->import_key.'</td></tr>';

	print '</table>';

	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$objProductregionprice,$action);    // Note that $action and $objProductregionprice may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->productext->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$objProductregionprice->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->productext->delete)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$objProductregionprice->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
		}
	}
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	// Show links to link elements
	//$linktoelem = $form->showLinkToObjectBlock($objProductregionprice, null, array('productregionprice'));
	//$somethingshown = $form->showLinkedObjectBlock($objProductregionprice, $linktoelem);

}


// End of page
llxFooter();
$db->close();
