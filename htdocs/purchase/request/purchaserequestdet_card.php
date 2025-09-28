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
 *   	\file       purchase/purchaserequestdet_card.php
 *		\ingroup    purchase
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-02-02 15:00
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
dol_include_once('/purchase/class/purchaserequestdet.class.php');

// Load traductions files requiredby by page
$langs->load("purchase");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_fk_purchase_request=GETPOST('search_fk_purchase_request','int');
$search_fk_parent_line=GETPOST('search_fk_parent_line','int');
$search_fk_product=GETPOST('search_fk_product','int');
$search_label=GETPOST('search_label','alpha');
$search_description=GETPOST('search_description','alpha');
$search_fk_remise_except=GETPOST('search_fk_remise_except','int');
$search_tva_tx=GETPOST('search_tva_tx','alpha');
$search_localtax1_tx=GETPOST('search_localtax1_tx','alpha');
$search_localtax1_type=GETPOST('search_localtax1_type','alpha');
$search_localtax2_tx=GETPOST('search_localtax2_tx','alpha');
$search_localtax2_type=GETPOST('search_localtax2_type','alpha');
$search_qty=GETPOST('search_qty','alpha');
$search_fk_unit=GETPOST('search_fk_unit','int');
$search_remise_percent=GETPOST('search_remise_percent','alpha');
$search_remise=GETPOST('search_remise','alpha');
$search_price=GETPOST('search_price','alpha');
$search_subprice=GETPOST('search_subprice','alpha');
$search_total_ht=GETPOST('search_total_ht','alpha');
$search_total_tva=GETPOST('search_total_tva','alpha');
$search_total_localtax1=GETPOST('search_total_localtax1','alpha');
$search_total_localtax2=GETPOST('search_total_localtax2','alpha');
$search_total_ttc=GETPOST('search_total_ttc','alpha');
$search_product_type=GETPOST('search_product_type','int');
$search_info_bits=GETPOST('search_info_bits','int');
$search_buy_price_ht=GETPOST('search_buy_price_ht','alpha');
$search_fk_product_fournisseur_price=GETPOST('search_fk_product_fournisseur_price','int');
$search_special_code=GETPOST('search_special_code','int');
$search_rang=GETPOST('search_rang','int');
$search_ref_fourn=GETPOST('search_ref_fourn','alpha');
$search_fk_multicurrency=GETPOST('search_fk_multicurrency','int');
$search_multicurrency_code=GETPOST('search_multicurrency_code','alpha');
$search_multicurrency_subprice=GETPOST('search_multicurrency_subprice','alpha');
$search_multicurrency_total_ht=GETPOST('search_multicurrency_total_ht','alpha');
$search_multicurrency_total_tva=GETPOST('search_multicurrency_total_tva','alpha');
$search_multicurrency_total_ttc=GETPOST('search_multicurrency_total_ttc','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_status=GETPOST('search_status','int');



// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Purchaserequestdet($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('purchaserequestdet'));
$extrafields = new ExtraFields($db);



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
	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/purchase/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */
		
	$object->fk_purchase_request=GETPOST('fk_purchase_request','int');
	$object->fk_parent_line=GETPOST('fk_parent_line','int');
	$object->fk_product=GETPOST('fk_product','int');
	$object->label=GETPOST('label','alpha');
	$object->description=GETPOST('description','alpha');
	$object->fk_remise_except=GETPOST('fk_remise_except','int');
	$object->tva_tx=GETPOST('tva_tx','alpha');
	$object->localtax1_tx=GETPOST('localtax1_tx','alpha');
	$object->localtax1_type=GETPOST('localtax1_type','alpha');
	$object->localtax2_tx=GETPOST('localtax2_tx','alpha');
	$object->localtax2_type=GETPOST('localtax2_type','alpha');
	$object->qty=GETPOST('qty','alpha');
	$object->fk_unit=GETPOST('fk_unit','int');
	$object->remise_percent=GETPOST('remise_percent','alpha');
	$object->remise=GETPOST('remise','alpha');
	$object->price=GETPOST('price','alpha');
	$object->subprice=GETPOST('subprice','alpha');
	$object->total_ht=GETPOST('total_ht','alpha');
	$object->total_tva=GETPOST('total_tva','alpha');
	$object->total_localtax1=GETPOST('total_localtax1','alpha');
	$object->total_localtax2=GETPOST('total_localtax2','alpha');
	$object->total_ttc=GETPOST('total_ttc','alpha');
	$object->product_type=GETPOST('product_type','int');
	$object->info_bits=GETPOST('info_bits','int');
	$object->buy_price_ht=GETPOST('buy_price_ht','alpha');
	$object->fk_product_fournisseur_price=GETPOST('fk_product_fournisseur_price','int');
	$object->special_code=GETPOST('special_code','int');
	$object->rang=GETPOST('rang','int');
	$object->ref_fourn=GETPOST('ref_fourn','alpha');
	$object->fk_multicurrency=GETPOST('fk_multicurrency','int');
	$object->multicurrency_code=GETPOST('multicurrency_code','alpha');
	$object->multicurrency_subprice=GETPOST('multicurrency_subprice','alpha');
	$object->multicurrency_total_ht=GETPOST('multicurrency_total_ht','alpha');
	$object->multicurrency_total_tva=GETPOST('multicurrency_total_tva','alpha');
	$object->multicurrency_total_ttc=GETPOST('multicurrency_total_ttc','alpha');
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
				$urltogo=$backtopage?$backtopage:dol_buildpath('/purchase/list.php',1);
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

	// Cancel
	if ($action == 'update' && GETPOST('cancel')) $action='view';

	// Action to update record
	if ($action == 'update' && ! GETPOST('cancel'))
	{
		$error=0;

		
	$object->fk_purchase_request=GETPOST('fk_purchase_request','int');
	$object->fk_parent_line=GETPOST('fk_parent_line','int');
	$object->fk_product=GETPOST('fk_product','int');
	$object->label=GETPOST('label','alpha');
	$object->description=GETPOST('description','alpha');
	$object->fk_remise_except=GETPOST('fk_remise_except','int');
	$object->tva_tx=GETPOST('tva_tx','alpha');
	$object->localtax1_tx=GETPOST('localtax1_tx','alpha');
	$object->localtax1_type=GETPOST('localtax1_type','alpha');
	$object->localtax2_tx=GETPOST('localtax2_tx','alpha');
	$object->localtax2_type=GETPOST('localtax2_type','alpha');
	$object->qty=GETPOST('qty','alpha');
	$object->fk_unit=GETPOST('fk_unit','int');
	$object->remise_percent=GETPOST('remise_percent','alpha');
	$object->remise=GETPOST('remise','alpha');
	$object->price=GETPOST('price','alpha');
	$object->subprice=GETPOST('subprice','alpha');
	$object->total_ht=GETPOST('total_ht','alpha');
	$object->total_tva=GETPOST('total_tva','alpha');
	$object->total_localtax1=GETPOST('total_localtax1','alpha');
	$object->total_localtax2=GETPOST('total_localtax2','alpha');
	$object->total_ttc=GETPOST('total_ttc','alpha');
	$object->product_type=GETPOST('product_type','int');
	$object->info_bits=GETPOST('info_bits','int');
	$object->buy_price_ht=GETPOST('buy_price_ht','alpha');
	$object->fk_product_fournisseur_price=GETPOST('fk_product_fournisseur_price','int');
	$object->special_code=GETPOST('special_code','int');
	$object->rang=GETPOST('rang','int');
	$object->ref_fourn=GETPOST('ref_fourn','alpha');
	$object->fk_multicurrency=GETPOST('fk_multicurrency','int');
	$object->multicurrency_code=GETPOST('multicurrency_code','alpha');
	$object->multicurrency_subprice=GETPOST('multicurrency_subprice','alpha');
	$object->multicurrency_total_ht=GETPOST('multicurrency_total_ht','alpha');
	$object->multicurrency_total_tva=GETPOST('multicurrency_total_tva','alpha');
	$object->multicurrency_total_ttc=GETPOST('multicurrency_total_ttc','alpha');
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
			header("Location: ".dol_buildpath('/purchase/list.php',1));
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_purchase_request").'</td><td><input class="flat" type="text" name="fk_purchase_request" value="'.GETPOST('fk_purchase_request').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_parent_line").'</td><td><input class="flat" type="text" name="fk_parent_line" value="'.GETPOST('fk_parent_line').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td><input class="flat" type="text" name="fk_product" value="'.GETPOST('fk_product').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.GETPOST('label').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddescription").'</td><td><input class="flat" type="text" name="description" value="'.GETPOST('description').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_remise_except").'</td><td><input class="flat" type="text" name="fk_remise_except" value="'.GETPOST('fk_remise_except').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtva_tx").'</td><td><input class="flat" type="text" name="tva_tx" value="'.GETPOST('tva_tx').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax1_tx").'</td><td><input class="flat" type="text" name="localtax1_tx" value="'.GETPOST('localtax1_tx').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax1_type").'</td><td><input class="flat" type="text" name="localtax1_type" value="'.GETPOST('localtax1_type').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax2_tx").'</td><td><input class="flat" type="text" name="localtax2_tx" value="'.GETPOST('localtax2_tx').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax2_type").'</td><td><input class="flat" type="text" name="localtax2_type" value="'.GETPOST('localtax2_type').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldqty").'</td><td><input class="flat" type="text" name="qty" value="'.GETPOST('qty').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit").'</td><td><input class="flat" type="text" name="fk_unit" value="'.GETPOST('fk_unit').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldremise_percent").'</td><td><input class="flat" type="text" name="remise_percent" value="'.GETPOST('remise_percent').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldremise").'</td><td><input class="flat" type="text" name="remise" value="'.GETPOST('remise').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice").'</td><td><input class="flat" type="text" name="price" value="'.GETPOST('price').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldsubprice").'</td><td><input class="flat" type="text" name="subprice" value="'.GETPOST('subprice').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtotal_ht").'</td><td><input class="flat" type="text" name="total_ht" value="'.GETPOST('total_ht').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtotal_tva").'</td><td><input class="flat" type="text" name="total_tva" value="'.GETPOST('total_tva').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtotal_localtax1").'</td><td><input class="flat" type="text" name="total_localtax1" value="'.GETPOST('total_localtax1').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtotal_localtax2").'</td><td><input class="flat" type="text" name="total_localtax2" value="'.GETPOST('total_localtax2').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtotal_ttc").'</td><td><input class="flat" type="text" name="total_ttc" value="'.GETPOST('total_ttc').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldproduct_type").'</td><td><input class="flat" type="text" name="product_type" value="'.GETPOST('product_type').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldinfo_bits").'</td><td><input class="flat" type="text" name="info_bits" value="'.GETPOST('info_bits').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbuy_price_ht").'</td><td><input class="flat" type="text" name="buy_price_ht" value="'.GETPOST('buy_price_ht').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product_fournisseur_price").'</td><td><input class="flat" type="text" name="fk_product_fournisseur_price" value="'.GETPOST('fk_product_fournisseur_price').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldspecial_code").'</td><td><input class="flat" type="text" name="special_code" value="'.GETPOST('special_code').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrang").'</td><td><input class="flat" type="text" name="rang" value="'.GETPOST('rang').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref_fourn").'</td><td><input class="flat" type="text" name="ref_fourn" value="'.GETPOST('ref_fourn').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_multicurrency").'</td><td><input class="flat" type="text" name="fk_multicurrency" value="'.GETPOST('fk_multicurrency').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmulticurrency_code").'</td><td><input class="flat" type="text" name="multicurrency_code" value="'.GETPOST('multicurrency_code').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmulticurrency_subprice").'</td><td><input class="flat" type="text" name="multicurrency_subprice" value="'.GETPOST('multicurrency_subprice').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmulticurrency_total_ht").'</td><td><input class="flat" type="text" name="multicurrency_total_ht" value="'.GETPOST('multicurrency_total_ht').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmulticurrency_total_tva").'</td><td><input class="flat" type="text" name="multicurrency_total_tva" value="'.GETPOST('multicurrency_total_tva').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmulticurrency_total_ttc").'</td><td><input class="flat" type="text" name="multicurrency_total_ttc" value="'.GETPOST('multicurrency_total_ttc').'"></td></tr>';
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
	print load_fiche_titre($langs->trans("MyModule"));
    
	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	
	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_purchase_request").'</td><td><input class="flat" type="text" name="fk_purchase_request" value="'.$object->fk_purchase_request.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_parent_line").'</td><td><input class="flat" type="text" name="fk_parent_line" value="'.$object->fk_parent_line.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td><input class="flat" type="text" name="fk_product" value="'.$object->fk_product.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.$object->label.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddescription").'</td><td><input class="flat" type="text" name="description" value="'.$object->description.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_remise_except").'</td><td><input class="flat" type="text" name="fk_remise_except" value="'.$object->fk_remise_except.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtva_tx").'</td><td><input class="flat" type="text" name="tva_tx" value="'.$object->tva_tx.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax1_tx").'</td><td><input class="flat" type="text" name="localtax1_tx" value="'.$object->localtax1_tx.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax1_type").'</td><td><input class="flat" type="text" name="localtax1_type" value="'.$object->localtax1_type.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax2_tx").'</td><td><input class="flat" type="text" name="localtax2_tx" value="'.$object->localtax2_tx.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax2_type").'</td><td><input class="flat" type="text" name="localtax2_type" value="'.$object->localtax2_type.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldqty").'</td><td><input class="flat" type="text" name="qty" value="'.$object->qty.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit").'</td><td><input class="flat" type="text" name="fk_unit" value="'.$object->fk_unit.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldremise_percent").'</td><td><input class="flat" type="text" name="remise_percent" value="'.$object->remise_percent.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldremise").'</td><td><input class="flat" type="text" name="remise" value="'.$object->remise.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice").'</td><td><input class="flat" type="text" name="price" value="'.$object->price.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldsubprice").'</td><td><input class="flat" type="text" name="subprice" value="'.$object->subprice.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtotal_ht").'</td><td><input class="flat" type="text" name="total_ht" value="'.$object->total_ht.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtotal_tva").'</td><td><input class="flat" type="text" name="total_tva" value="'.$object->total_tva.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtotal_localtax1").'</td><td><input class="flat" type="text" name="total_localtax1" value="'.$object->total_localtax1.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtotal_localtax2").'</td><td><input class="flat" type="text" name="total_localtax2" value="'.$object->total_localtax2.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtotal_ttc").'</td><td><input class="flat" type="text" name="total_ttc" value="'.$object->total_ttc.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldproduct_type").'</td><td><input class="flat" type="text" name="product_type" value="'.$object->product_type.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldinfo_bits").'</td><td><input class="flat" type="text" name="info_bits" value="'.$object->info_bits.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbuy_price_ht").'</td><td><input class="flat" type="text" name="buy_price_ht" value="'.$object->buy_price_ht.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product_fournisseur_price").'</td><td><input class="flat" type="text" name="fk_product_fournisseur_price" value="'.$object->fk_product_fournisseur_price.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldspecial_code").'</td><td><input class="flat" type="text" name="special_code" value="'.$object->special_code.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrang").'</td><td><input class="flat" type="text" name="rang" value="'.$object->rang.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref_fourn").'</td><td><input class="flat" type="text" name="ref_fourn" value="'.$object->ref_fourn.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_multicurrency").'</td><td><input class="flat" type="text" name="fk_multicurrency" value="'.$object->fk_multicurrency.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmulticurrency_code").'</td><td><input class="flat" type="text" name="multicurrency_code" value="'.$object->multicurrency_code.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmulticurrency_subprice").'</td><td><input class="flat" type="text" name="multicurrency_subprice" value="'.$object->multicurrency_subprice.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmulticurrency_total_ht").'</td><td><input class="flat" type="text" name="multicurrency_total_ht" value="'.$object->multicurrency_total_ht.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmulticurrency_total_tva").'</td><td><input class="flat" type="text" name="multicurrency_total_tva" value="'.$object->multicurrency_total_tva.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmulticurrency_total_ttc").'</td><td><input class="flat" type="text" name="multicurrency_total_ttc" value="'.$object->multicurrency_total_ttc.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.$object->fk_user_create.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.$object->fk_user_mod.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.$object->status.'"></td></tr>';

	print '</table>';
	
	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}



// Part to show record
if ($id && (empty($action) || $action == 'view' || $action == 'delete'))
{
	print load_fiche_titre($langs->trans("MyModule"));
    
	dol_fiche_head();

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('DeleteMyOjbect'), $langs->trans('ConfirmDeleteMyObject'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}
	
	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_purchase_request").'</td><td>$object->fk_purchase_request</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_parent_line").'</td><td>$object->fk_parent_line</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td>$object->fk_product</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td>$object->label</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddescription").'</td><td>$object->description</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_remise_except").'</td><td>$object->fk_remise_except</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtva_tx").'</td><td>$object->tva_tx</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax1_tx").'</td><td>$object->localtax1_tx</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax1_type").'</td><td>$object->localtax1_type</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax2_tx").'</td><td>$object->localtax2_tx</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax2_type").'</td><td>$object->localtax2_type</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldqty").'</td><td>$object->qty</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit").'</td><td>$object->fk_unit</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldremise_percent").'</td><td>$object->remise_percent</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldremise").'</td><td>$object->remise</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice").'</td><td>$object->price</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldsubprice").'</td><td>$object->subprice</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtotal_ht").'</td><td>$object->total_ht</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtotal_tva").'</td><td>$object->total_tva</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtotal_localtax1").'</td><td>$object->total_localtax1</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtotal_localtax2").'</td><td>$object->total_localtax2</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtotal_ttc").'</td><td>$object->total_ttc</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldproduct_type").'</td><td>$object->product_type</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldinfo_bits").'</td><td>$object->info_bits</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbuy_price_ht").'</td><td>$object->buy_price_ht</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product_fournisseur_price").'</td><td>$object->fk_product_fournisseur_price</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldspecial_code").'</td><td>$object->special_code</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrang").'</td><td>$object->rang</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref_fourn").'</td><td>$object->ref_fourn</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_multicurrency").'</td><td>$object->fk_multicurrency</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmulticurrency_code").'</td><td>$object->multicurrency_code</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmulticurrency_subprice").'</td><td>$object->multicurrency_subprice</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmulticurrency_total_ht").'</td><td>$object->multicurrency_total_ht</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmulticurrency_total_tva").'</td><td>$object->multicurrency_total_tva</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmulticurrency_total_ttc").'</td><td>$object->multicurrency_total_ttc</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td>$object->fk_user_create</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td>$object->fk_user_mod</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td>$object->status</td></tr>';

	print '</table>';
	
	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->purchase->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->purchase->delete)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
		}
	}
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	//$somethingshown=$form->showLinkedObjectBlock($object);
	//$linktoelem = $form->showLinkToObjectBlock($object);
	//if ($linktoelem) print '<br>'.$linktoelem;

}


// End of page
llxFooter();
$db->close();
