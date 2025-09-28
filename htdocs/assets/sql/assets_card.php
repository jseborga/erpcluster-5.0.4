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
 *   	\file       assets/assets_card.php
 *		\ingroup    assets
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-05-25 16:35
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
dol_include_once('/assets/class/assets.class.php');

// Load traductions files requiredby by page
$langs->load("assets");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$cancel     = GETPOST('cancel');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_fk_father=GETPOST('search_fk_father','int');
$search_fk_facture=GETPOST('search_fk_facture','int');
$search_type_group=GETPOST('search_type_group','alpha');
$search_type_patrim=GETPOST('search_type_patrim','alpha');
$search_ref=GETPOST('search_ref','alpha');
$search_item_asset=GETPOST('search_item_asset','int');
$search_useful_life_residual=GETPOST('search_useful_life_residual','int');
$search_quant=GETPOST('search_quant','alpha');
$search_coste=GETPOST('search_coste','alpha');
$search_coste_residual=GETPOST('search_coste_residual','alpha');
$search_coste_reval=GETPOST('search_coste_reval','alpha');
$search_coste_residual_reval=GETPOST('search_coste_residual_reval','alpha');
$search_amount_sale=GETPOST('search_amount_sale','alpha');
$search_descrip=GETPOST('search_descrip','alpha');
$search_number_plaque=GETPOST('search_number_plaque','alpha');
$search_trademark=GETPOST('search_trademark','alpha');
$search_model=GETPOST('search_model','alpha');
$search_anio=GETPOST('search_anio','int');
$search_fk_asset_sup=GETPOST('search_fk_asset_sup','int');
$search_fk_location=GETPOST('search_fk_location','int');
$search_code_bar=GETPOST('search_code_bar','alpha');
$search_fk_method_dep=GETPOST('search_fk_method_dep','int');
$search_type_property=GETPOST('search_type_property','alpha');
$search_code_bim=GETPOST('search_code_bim','alpha');
$search_fk_product=GETPOST('search_fk_product','int');
$search_useful_life=GETPOST('search_useful_life','alpha');
$search_percent=GETPOST('search_percent','alpha');
$search_account_accounting=GETPOST('search_account_accounting','alpha');
$search_fk_unit=GETPOST('search_fk_unit','int');
$search_model_pdf=GETPOST('search_model_pdf','alpha');
$search_coste_unit_use=GETPOST('search_coste_unit_use','alpha');
$search_fk_unit_use=GETPOST('search_fk_unit_use','int');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_mark=GETPOST('search_mark','alpha');
$search_been=GETPOST('search_been','alpha');
$search_fk_asset_mov=GETPOST('search_fk_asset_mov','int');
$search_status_reval=GETPOST('search_status_reval','int');
$search_statut=GETPOST('search_statut','int');



if (empty($action) && empty($id) && empty($ref)) $action='view';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
//$result = restrictedArea($user, 'assets', $id);


$object = new Assets($db);
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('assets'));



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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/assets/list.php',1);
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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/assets/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */
		
	$object->entity=GETPOST('entity','int');
	$object->fk_father=GETPOST('fk_father','int');
	$object->fk_facture=GETPOST('fk_facture','int');
	$object->type_group=GETPOST('type_group','alpha');
	$object->type_patrim=GETPOST('type_patrim','alpha');
	$object->ref=GETPOST('ref','alpha');
	$object->item_asset=GETPOST('item_asset','int');
	$object->useful_life_residual=GETPOST('useful_life_residual','int');
	$object->quant=GETPOST('quant','alpha');
	$object->coste=GETPOST('coste','alpha');
	$object->coste_residual=GETPOST('coste_residual','alpha');
	$object->coste_reval=GETPOST('coste_reval','alpha');
	$object->coste_residual_reval=GETPOST('coste_residual_reval','alpha');
	$object->amount_sale=GETPOST('amount_sale','alpha');
	$object->descrip=GETPOST('descrip','alpha');
	$object->number_plaque=GETPOST('number_plaque','alpha');
	$object->trademark=GETPOST('trademark','alpha');
	$object->model=GETPOST('model','alpha');
	$object->anio=GETPOST('anio','int');
	$object->fk_asset_sup=GETPOST('fk_asset_sup','int');
	$object->fk_location=GETPOST('fk_location','int');
	$object->code_bar=GETPOST('code_bar','alpha');
	$object->fk_method_dep=GETPOST('fk_method_dep','int');
	$object->type_property=GETPOST('type_property','alpha');
	$object->code_bim=GETPOST('code_bim','alpha');
	$object->fk_product=GETPOST('fk_product','int');
	$object->useful_life=GETPOST('useful_life','alpha');
	$object->percent=GETPOST('percent','alpha');
	$object->account_accounting=GETPOST('account_accounting','alpha');
	$object->fk_unit=GETPOST('fk_unit','int');
	$object->model_pdf=GETPOST('model_pdf','alpha');
	$object->coste_unit_use=GETPOST('coste_unit_use','alpha');
	$object->fk_unit_use=GETPOST('fk_unit_use','int');
	$object->fk_user_create=GETPOST('fk_user_create','int');
	$object->fk_user_mod=GETPOST('fk_user_mod','int');
	$object->mark=GETPOST('mark','alpha');
	$object->been=GETPOST('been','alpha');
	$object->fk_asset_mov=GETPOST('fk_asset_mov','int');
	$object->status_reval=GETPOST('status_reval','int');
	$object->statut=GETPOST('statut','int');

		

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
				$urltogo=$backtopage?$backtopage:dol_buildpath('/assets/list.php',1);
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

		
	$object->entity=GETPOST('entity','int');
	$object->fk_father=GETPOST('fk_father','int');
	$object->fk_facture=GETPOST('fk_facture','int');
	$object->type_group=GETPOST('type_group','alpha');
	$object->type_patrim=GETPOST('type_patrim','alpha');
	$object->ref=GETPOST('ref','alpha');
	$object->item_asset=GETPOST('item_asset','int');
	$object->useful_life_residual=GETPOST('useful_life_residual','int');
	$object->quant=GETPOST('quant','alpha');
	$object->coste=GETPOST('coste','alpha');
	$object->coste_residual=GETPOST('coste_residual','alpha');
	$object->coste_reval=GETPOST('coste_reval','alpha');
	$object->coste_residual_reval=GETPOST('coste_residual_reval','alpha');
	$object->amount_sale=GETPOST('amount_sale','alpha');
	$object->descrip=GETPOST('descrip','alpha');
	$object->number_plaque=GETPOST('number_plaque','alpha');
	$object->trademark=GETPOST('trademark','alpha');
	$object->model=GETPOST('model','alpha');
	$object->anio=GETPOST('anio','int');
	$object->fk_asset_sup=GETPOST('fk_asset_sup','int');
	$object->fk_location=GETPOST('fk_location','int');
	$object->code_bar=GETPOST('code_bar','alpha');
	$object->fk_method_dep=GETPOST('fk_method_dep','int');
	$object->type_property=GETPOST('type_property','alpha');
	$object->code_bim=GETPOST('code_bim','alpha');
	$object->fk_product=GETPOST('fk_product','int');
	$object->useful_life=GETPOST('useful_life','alpha');
	$object->percent=GETPOST('percent','alpha');
	$object->account_accounting=GETPOST('account_accounting','alpha');
	$object->fk_unit=GETPOST('fk_unit','int');
	$object->model_pdf=GETPOST('model_pdf','alpha');
	$object->coste_unit_use=GETPOST('coste_unit_use','alpha');
	$object->fk_unit_use=GETPOST('fk_unit_use','int');
	$object->fk_user_create=GETPOST('fk_user_create','int');
	$object->fk_user_mod=GETPOST('fk_user_mod','int');
	$object->mark=GETPOST('mark','alpha');
	$object->been=GETPOST('been','alpha');
	$object->fk_asset_mov=GETPOST('fk_asset_mov','int');
	$object->status_reval=GETPOST('status_reval','int');
	$object->statut=GETPOST('statut','int');

		

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
			header("Location: ".dol_buildpath('/assets/list.php',1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else setEventMessages($object->error, null, 'errors');
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_father").'</td><td><input class="flat" type="text" name="fk_father" value="'.GETPOST('fk_father').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_facture").'</td><td><input class="flat" type="text" name="fk_facture" value="'.GETPOST('fk_facture').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_group").'</td><td><input class="flat" type="text" name="type_group" value="'.GETPOST('type_group').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_patrim").'</td><td><input class="flat" type="text" name="type_patrim" value="'.GETPOST('type_patrim').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.GETPOST('ref').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielditem_asset").'</td><td><input class="flat" type="text" name="item_asset" value="'.GETPOST('item_asset').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielduseful_life_residual").'</td><td><input class="flat" type="text" name="useful_life_residual" value="'.GETPOST('useful_life_residual').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldquant").'</td><td><input class="flat" type="text" name="quant" value="'.GETPOST('quant').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcoste").'</td><td><input class="flat" type="text" name="coste" value="'.GETPOST('coste').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcoste_residual").'</td><td><input class="flat" type="text" name="coste_residual" value="'.GETPOST('coste_residual').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcoste_reval").'</td><td><input class="flat" type="text" name="coste_reval" value="'.GETPOST('coste_reval').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcoste_residual_reval").'</td><td><input class="flat" type="text" name="coste_residual_reval" value="'.GETPOST('coste_residual_reval').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_sale").'</td><td><input class="flat" type="text" name="amount_sale" value="'.GETPOST('amount_sale').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddescrip").'</td><td><input class="flat" type="text" name="descrip" value="'.GETPOST('descrip').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnumber_plaque").'</td><td><input class="flat" type="text" name="number_plaque" value="'.GETPOST('number_plaque').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtrademark").'</td><td><input class="flat" type="text" name="trademark" value="'.GETPOST('trademark').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodel").'</td><td><input class="flat" type="text" name="model" value="'.GETPOST('model').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldanio").'</td><td><input class="flat" type="text" name="anio" value="'.GETPOST('anio').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_asset_sup").'</td><td><input class="flat" type="text" name="fk_asset_sup" value="'.GETPOST('fk_asset_sup').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_location").'</td><td><input class="flat" type="text" name="fk_location" value="'.GETPOST('fk_location').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode_bar").'</td><td><input class="flat" type="text" name="code_bar" value="'.GETPOST('code_bar').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_method_dep").'</td><td><input class="flat" type="text" name="fk_method_dep" value="'.GETPOST('fk_method_dep').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_property").'</td><td><input class="flat" type="text" name="type_property" value="'.GETPOST('type_property').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode_bim").'</td><td><input class="flat" type="text" name="code_bim" value="'.GETPOST('code_bim').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td><input class="flat" type="text" name="fk_product" value="'.GETPOST('fk_product').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielduseful_life").'</td><td><input class="flat" type="text" name="useful_life" value="'.GETPOST('useful_life').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpercent").'</td><td><input class="flat" type="text" name="percent" value="'.GETPOST('percent').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldaccount_accounting").'</td><td><input class="flat" type="text" name="account_accounting" value="'.GETPOST('account_accounting').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit").'</td><td><input class="flat" type="text" name="fk_unit" value="'.GETPOST('fk_unit').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodel_pdf").'</td><td><input class="flat" type="text" name="model_pdf" value="'.GETPOST('model_pdf').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcoste_unit_use").'</td><td><input class="flat" type="text" name="coste_unit_use" value="'.GETPOST('coste_unit_use').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit_use").'</td><td><input class="flat" type="text" name="fk_unit_use" value="'.GETPOST('fk_unit_use').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.GETPOST('fk_user_create').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.GETPOST('fk_user_mod').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmark").'</td><td><input class="flat" type="text" name="mark" value="'.GETPOST('mark').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbeen").'</td><td><input class="flat" type="text" name="been" value="'.GETPOST('been').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_asset_mov").'</td><td><input class="flat" type="text" name="fk_asset_mov" value="'.GETPOST('fk_asset_mov').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus_reval").'</td><td><input class="flat" type="text" name="status_reval" value="'.GETPOST('status_reval').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatut").'</td><td><input class="flat" type="text" name="statut" value="'.GETPOST('statut').'"></td></tr>';

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
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	
	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td><input class="flat" type="text" name="entity" value="'.$object->entity.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_father").'</td><td><input class="flat" type="text" name="fk_father" value="'.$object->fk_father.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_facture").'</td><td><input class="flat" type="text" name="fk_facture" value="'.$object->fk_facture.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_group").'</td><td><input class="flat" type="text" name="type_group" value="'.$object->type_group.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_patrim").'</td><td><input class="flat" type="text" name="type_patrim" value="'.$object->type_patrim.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.$object->ref.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielditem_asset").'</td><td><input class="flat" type="text" name="item_asset" value="'.$object->item_asset.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielduseful_life_residual").'</td><td><input class="flat" type="text" name="useful_life_residual" value="'.$object->useful_life_residual.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldquant").'</td><td><input class="flat" type="text" name="quant" value="'.$object->quant.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcoste").'</td><td><input class="flat" type="text" name="coste" value="'.$object->coste.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcoste_residual").'</td><td><input class="flat" type="text" name="coste_residual" value="'.$object->coste_residual.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcoste_reval").'</td><td><input class="flat" type="text" name="coste_reval" value="'.$object->coste_reval.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcoste_residual_reval").'</td><td><input class="flat" type="text" name="coste_residual_reval" value="'.$object->coste_residual_reval.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_sale").'</td><td><input class="flat" type="text" name="amount_sale" value="'.$object->amount_sale.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddescrip").'</td><td><input class="flat" type="text" name="descrip" value="'.$object->descrip.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnumber_plaque").'</td><td><input class="flat" type="text" name="number_plaque" value="'.$object->number_plaque.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtrademark").'</td><td><input class="flat" type="text" name="trademark" value="'.$object->trademark.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodel").'</td><td><input class="flat" type="text" name="model" value="'.$object->model.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldanio").'</td><td><input class="flat" type="text" name="anio" value="'.$object->anio.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_asset_sup").'</td><td><input class="flat" type="text" name="fk_asset_sup" value="'.$object->fk_asset_sup.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_location").'</td><td><input class="flat" type="text" name="fk_location" value="'.$object->fk_location.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode_bar").'</td><td><input class="flat" type="text" name="code_bar" value="'.$object->code_bar.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_method_dep").'</td><td><input class="flat" type="text" name="fk_method_dep" value="'.$object->fk_method_dep.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_property").'</td><td><input class="flat" type="text" name="type_property" value="'.$object->type_property.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode_bim").'</td><td><input class="flat" type="text" name="code_bim" value="'.$object->code_bim.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td><input class="flat" type="text" name="fk_product" value="'.$object->fk_product.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielduseful_life").'</td><td><input class="flat" type="text" name="useful_life" value="'.$object->useful_life.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpercent").'</td><td><input class="flat" type="text" name="percent" value="'.$object->percent.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldaccount_accounting").'</td><td><input class="flat" type="text" name="account_accounting" value="'.$object->account_accounting.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit").'</td><td><input class="flat" type="text" name="fk_unit" value="'.$object->fk_unit.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodel_pdf").'</td><td><input class="flat" type="text" name="model_pdf" value="'.$object->model_pdf.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcoste_unit_use").'</td><td><input class="flat" type="text" name="coste_unit_use" value="'.$object->coste_unit_use.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit_use").'</td><td><input class="flat" type="text" name="fk_unit_use" value="'.$object->fk_unit_use.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.$object->fk_user_create.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.$object->fk_user_mod.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmark").'</td><td><input class="flat" type="text" name="mark" value="'.$object->mark.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbeen").'</td><td><input class="flat" type="text" name="been" value="'.$object->been.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_asset_mov").'</td><td><input class="flat" type="text" name="fk_asset_mov" value="'.$object->fk_asset_mov.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus_reval").'</td><td><input class="flat" type="text" name="status_reval" value="'.$object->status_reval.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatut").'</td><td><input class="flat" type="text" name="statut" value="'.$object->statut.'"></td></tr>';

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
    $res = $object->fetch_optionals($object->id, $extralabels);

	$head = commande_prepare_head($object);
	dol_fiche_head($head, 'order', $langs->trans("CustomerOrder"), 0, 'order');
		
	print load_fiche_titre($langs->trans("MyModule"));
    
	dol_fiche_head();

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('DeleteMyOjbect'), $langs->trans('ConfirmDeleteMyObject'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}
	
	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>'.$object->label.'</td></tr>';
	// 
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td>$object->entity</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_father").'</td><td>$object->fk_father</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_facture").'</td><td>$object->fk_facture</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_group").'</td><td>$object->type_group</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_patrim").'</td><td>$object->type_patrim</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td>$object->ref</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielditem_asset").'</td><td>$object->item_asset</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielduseful_life_residual").'</td><td>$object->useful_life_residual</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldquant").'</td><td>$object->quant</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcoste").'</td><td>$object->coste</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcoste_residual").'</td><td>$object->coste_residual</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcoste_reval").'</td><td>$object->coste_reval</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcoste_residual_reval").'</td><td>$object->coste_residual_reval</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_sale").'</td><td>$object->amount_sale</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddescrip").'</td><td>$object->descrip</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnumber_plaque").'</td><td>$object->number_plaque</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtrademark").'</td><td>$object->trademark</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodel").'</td><td>$object->model</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldanio").'</td><td>$object->anio</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_asset_sup").'</td><td>$object->fk_asset_sup</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_location").'</td><td>$object->fk_location</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode_bar").'</td><td>$object->code_bar</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_method_dep").'</td><td>$object->fk_method_dep</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_property").'</td><td>$object->type_property</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode_bim").'</td><td>$object->code_bim</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td>$object->fk_product</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielduseful_life").'</td><td>$object->useful_life</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpercent").'</td><td>$object->percent</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldaccount_accounting").'</td><td>$object->account_accounting</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit").'</td><td>$object->fk_unit</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodel_pdf").'</td><td>$object->model_pdf</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcoste_unit_use").'</td><td>$object->coste_unit_use</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit_use").'</td><td>$object->fk_unit_use</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td>$object->fk_user_create</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td>$object->fk_user_mod</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmark").'</td><td>$object->mark</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbeen").'</td><td>$object->been</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_asset_mov").'</td><td>$object->fk_asset_mov</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus_reval").'</td><td>$object->status_reval</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatut").'</td><td>$object->statut</td></tr>';

	print '</table>';
	
	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->assets->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->assets->delete)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
		}
	}
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	// Show links to link elements
	//$linktoelem = $form->showLinkToObjectBlock($object, null, array('assets'));
	//$somethingshown = $form->showLinkedObjectBlock($object, $linktoelem);

}


// End of page
llxFooter();
$db->close();
