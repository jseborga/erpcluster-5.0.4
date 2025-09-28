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
 *   	\file       monprojet/projetproduct_card.php
 *		\ingroup    monprojet
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-09-16 12:52
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
dol_include_once('/monprojet/class/projetproduct.class.php');

// Load traductions files requiredby by page
$langs->load("monprojet");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_fk_projet=GETPOST('search_fk_projet','int');
$search_fk_product=GETPOST('search_fk_product','int');
$search_ref=GETPOST('search_ref','alpha');
$search_ref_ext=GETPOST('search_ref_ext','alpha');
$search_fk_parent=GETPOST('search_fk_parent','int');
$search_fk_categorie=GETPOST('search_fk_categorie','int');
$search_label=GETPOST('search_label','alpha');
$search_description=GETPOST('search_description','alpha');
$search_fk_country=GETPOST('search_fk_country','int');
$search_price=GETPOST('search_price','alpha');
$search_price_ttc=GETPOST('search_price_ttc','alpha');
$search_price_min=GETPOST('search_price_min','alpha');
$search_price_min_ttc=GETPOST('search_price_min_ttc','alpha');
$search_price_base_type=GETPOST('search_price_base_type','alpha');
$search_tva_tx=GETPOST('search_tva_tx','alpha');
$search_recuperableonly=GETPOST('search_recuperableonly','int');
$search_localtax1_tx=GETPOST('search_localtax1_tx','alpha');
$search_localtax1_type=GETPOST('search_localtax1_type','alpha');
$search_localtax2_tx=GETPOST('search_localtax2_tx','alpha');
$search_localtax2_type=GETPOST('search_localtax2_type','alpha');
$search_fk_user_author=GETPOST('search_fk_user_author','int');
$search_fk_user_modif=GETPOST('search_fk_user_modif','int');
$search_fk_product_type=GETPOST('search_fk_product_type','int');
$search_pmp=GETPOST('search_pmp','alpha');
$search_finished=GETPOST('search_finished','int');
$search_fk_unit=GETPOST('search_fk_unit','int');
$search_cost_price=GETPOST('search_cost_price','alpha');
$search_status=GETPOST('search_status','int');



// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Projetproduct($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('projetproduct'));
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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */
		
	$object->fk_projet=GETPOST('fk_projet','int');
	$object->fk_product=GETPOST('fk_product','int');
	$object->ref=GETPOST('ref','alpha');
	$object->ref_ext=GETPOST('ref_ext','alpha');
	$object->fk_parent=GETPOST('fk_parent','int');
	$object->fk_categorie=GETPOST('fk_categorie','int');
	$object->label=GETPOST('label','alpha');
	$object->description=GETPOST('description','alpha');
	$object->fk_country=GETPOST('fk_country','int');
	$object->price=GETPOST('price','alpha');
	$object->price_ttc=GETPOST('price_ttc','alpha');
	$object->price_min=GETPOST('price_min','alpha');
	$object->price_min_ttc=GETPOST('price_min_ttc','alpha');
	$object->price_base_type=GETPOST('price_base_type','alpha');
	$object->tva_tx=GETPOST('tva_tx','alpha');
	$object->recuperableonly=GETPOST('recuperableonly','int');
	$object->localtax1_tx=GETPOST('localtax1_tx','alpha');
	$object->localtax1_type=GETPOST('localtax1_type','alpha');
	$object->localtax2_tx=GETPOST('localtax2_tx','alpha');
	$object->localtax2_type=GETPOST('localtax2_type','alpha');
	$object->fk_user_author=GETPOST('fk_user_author','int');
	$object->fk_user_modif=GETPOST('fk_user_modif','int');
	$object->fk_product_type=GETPOST('fk_product_type','int');
	$object->pmp=GETPOST('pmp','alpha');
	$object->finished=GETPOST('finished','int');
	$object->fk_unit=GETPOST('fk_unit','int');
	$object->cost_price=GETPOST('cost_price','alpha');
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
				$urltogo=$backtopage?$backtopage:dol_buildpath('/monprojet/list.php',1);
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

		
	$object->fk_projet=GETPOST('fk_projet','int');
	$object->fk_product=GETPOST('fk_product','int');
	$object->ref=GETPOST('ref','alpha');
	$object->ref_ext=GETPOST('ref_ext','alpha');
	$object->fk_parent=GETPOST('fk_parent','int');
	$object->fk_categorie=GETPOST('fk_categorie','int');
	$object->label=GETPOST('label','alpha');
	$object->description=GETPOST('description','alpha');
	$object->fk_country=GETPOST('fk_country','int');
	$object->price=GETPOST('price','alpha');
	$object->price_ttc=GETPOST('price_ttc','alpha');
	$object->price_min=GETPOST('price_min','alpha');
	$object->price_min_ttc=GETPOST('price_min_ttc','alpha');
	$object->price_base_type=GETPOST('price_base_type','alpha');
	$object->tva_tx=GETPOST('tva_tx','alpha');
	$object->recuperableonly=GETPOST('recuperableonly','int');
	$object->localtax1_tx=GETPOST('localtax1_tx','alpha');
	$object->localtax1_type=GETPOST('localtax1_type','alpha');
	$object->localtax2_tx=GETPOST('localtax2_tx','alpha');
	$object->localtax2_type=GETPOST('localtax2_type','alpha');
	$object->fk_user_author=GETPOST('fk_user_author','int');
	$object->fk_user_modif=GETPOST('fk_user_modif','int');
	$object->fk_product_type=GETPOST('fk_product_type','int');
	$object->pmp=GETPOST('pmp','alpha');
	$object->finished=GETPOST('finished','int');
	$object->fk_unit=GETPOST('fk_unit','int');
	$object->cost_price=GETPOST('cost_price','alpha');
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
			header("Location: ".dol_buildpath('/monprojet/list.php',1));
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_projet").'</td><td><input class="flat" type="text" name="fk_projet" value="'.GETPOST('fk_projet').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td><input class="flat" type="text" name="fk_product" value="'.GETPOST('fk_product').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.GETPOST('ref').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref_ext").'</td><td><input class="flat" type="text" name="ref_ext" value="'.GETPOST('ref_ext').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_parent").'</td><td><input class="flat" type="text" name="fk_parent" value="'.GETPOST('fk_parent').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_categorie").'</td><td><input class="flat" type="text" name="fk_categorie" value="'.GETPOST('fk_categorie').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.GETPOST('label').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddescription").'</td><td><input class="flat" type="text" name="description" value="'.GETPOST('description').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_country").'</td><td><input class="flat" type="text" name="fk_country" value="'.GETPOST('fk_country').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice").'</td><td><input class="flat" type="text" name="price" value="'.GETPOST('price').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice_ttc").'</td><td><input class="flat" type="text" name="price_ttc" value="'.GETPOST('price_ttc').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice_min").'</td><td><input class="flat" type="text" name="price_min" value="'.GETPOST('price_min').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice_min_ttc").'</td><td><input class="flat" type="text" name="price_min_ttc" value="'.GETPOST('price_min_ttc').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice_base_type").'</td><td><input class="flat" type="text" name="price_base_type" value="'.GETPOST('price_base_type').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtva_tx").'</td><td><input class="flat" type="text" name="tva_tx" value="'.GETPOST('tva_tx').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrecuperableonly").'</td><td><input class="flat" type="text" name="recuperableonly" value="'.GETPOST('recuperableonly').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax1_tx").'</td><td><input class="flat" type="text" name="localtax1_tx" value="'.GETPOST('localtax1_tx').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax1_type").'</td><td><input class="flat" type="text" name="localtax1_type" value="'.GETPOST('localtax1_type').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax2_tx").'</td><td><input class="flat" type="text" name="localtax2_tx" value="'.GETPOST('localtax2_tx').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax2_type").'</td><td><input class="flat" type="text" name="localtax2_type" value="'.GETPOST('localtax2_type').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_author").'</td><td><input class="flat" type="text" name="fk_user_author" value="'.GETPOST('fk_user_author').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_modif").'</td><td><input class="flat" type="text" name="fk_user_modif" value="'.GETPOST('fk_user_modif').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product_type").'</td><td><input class="flat" type="text" name="fk_product_type" value="'.GETPOST('fk_product_type').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpmp").'</td><td><input class="flat" type="text" name="pmp" value="'.GETPOST('pmp').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfinished").'</td><td><input class="flat" type="text" name="finished" value="'.GETPOST('finished').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit").'</td><td><input class="flat" type="text" name="fk_unit" value="'.GETPOST('fk_unit').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_price").'</td><td><input class="flat" type="text" name="cost_price" value="'.GETPOST('cost_price').'"></td></tr>';
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_projet").'</td><td><input class="flat" type="text" name="fk_projet" value="'.$object->fk_projet.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td><input class="flat" type="text" name="fk_product" value="'.$object->fk_product.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.$object->ref.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref_ext").'</td><td><input class="flat" type="text" name="ref_ext" value="'.$object->ref_ext.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_parent").'</td><td><input class="flat" type="text" name="fk_parent" value="'.$object->fk_parent.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_categorie").'</td><td><input class="flat" type="text" name="fk_categorie" value="'.$object->fk_categorie.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.$object->label.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddescription").'</td><td><input class="flat" type="text" name="description" value="'.$object->description.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_country").'</td><td><input class="flat" type="text" name="fk_country" value="'.$object->fk_country.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice").'</td><td><input class="flat" type="text" name="price" value="'.$object->price.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice_ttc").'</td><td><input class="flat" type="text" name="price_ttc" value="'.$object->price_ttc.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice_min").'</td><td><input class="flat" type="text" name="price_min" value="'.$object->price_min.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice_min_ttc").'</td><td><input class="flat" type="text" name="price_min_ttc" value="'.$object->price_min_ttc.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice_base_type").'</td><td><input class="flat" type="text" name="price_base_type" value="'.$object->price_base_type.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtva_tx").'</td><td><input class="flat" type="text" name="tva_tx" value="'.$object->tva_tx.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrecuperableonly").'</td><td><input class="flat" type="text" name="recuperableonly" value="'.$object->recuperableonly.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax1_tx").'</td><td><input class="flat" type="text" name="localtax1_tx" value="'.$object->localtax1_tx.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax1_type").'</td><td><input class="flat" type="text" name="localtax1_type" value="'.$object->localtax1_type.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax2_tx").'</td><td><input class="flat" type="text" name="localtax2_tx" value="'.$object->localtax2_tx.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax2_type").'</td><td><input class="flat" type="text" name="localtax2_type" value="'.$object->localtax2_type.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_author").'</td><td><input class="flat" type="text" name="fk_user_author" value="'.$object->fk_user_author.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_modif").'</td><td><input class="flat" type="text" name="fk_user_modif" value="'.$object->fk_user_modif.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product_type").'</td><td><input class="flat" type="text" name="fk_product_type" value="'.$object->fk_product_type.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpmp").'</td><td><input class="flat" type="text" name="pmp" value="'.$object->pmp.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfinished").'</td><td><input class="flat" type="text" name="finished" value="'.$object->finished.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit").'</td><td><input class="flat" type="text" name="fk_unit" value="'.$object->fk_unit.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_price").'</td><td><input class="flat" type="text" name="cost_price" value="'.$object->cost_price.'"></td></tr>';
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_projet").'</td><td>$object->fk_projet</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td>$object->fk_product</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td>$object->ref</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref_ext").'</td><td>$object->ref_ext</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_parent").'</td><td>$object->fk_parent</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_categorie").'</td><td>$object->fk_categorie</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td>$object->label</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddescription").'</td><td>$object->description</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_country").'</td><td>$object->fk_country</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice").'</td><td>$object->price</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice_ttc").'</td><td>$object->price_ttc</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice_min").'</td><td>$object->price_min</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice_min_ttc").'</td><td>$object->price_min_ttc</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice_base_type").'</td><td>$object->price_base_type</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtva_tx").'</td><td>$object->tva_tx</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrecuperableonly").'</td><td>$object->recuperableonly</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax1_tx").'</td><td>$object->localtax1_tx</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax1_type").'</td><td>$object->localtax1_type</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax2_tx").'</td><td>$object->localtax2_tx</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax2_type").'</td><td>$object->localtax2_type</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_author").'</td><td>$object->fk_user_author</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_modif").'</td><td>$object->fk_user_modif</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product_type").'</td><td>$object->fk_product_type</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpmp").'</td><td>$object->pmp</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfinished").'</td><td>$object->finished</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit").'</td><td>$object->fk_unit</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcost_price").'</td><td>$object->cost_price</td></tr>';
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
		if ($user->rights->monprojet->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->monprojet->delete)
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
