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
 *   	\file       almacen/stockmouvementtemp_card.php
 *		\ingroup    almacen
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-03-13 16:16
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
dol_include_once('/almacen/class/stockmouvementtemp.class.php');

// Load traductions files requiredby by page
$langs->load("almacen");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_fk_product=GETPOST('search_fk_product','int');
$search_fk_entrepot=GETPOST('search_fk_entrepot','int');
$search_fk_type_mov=GETPOST('search_fk_type_mov','int');
$search_value=GETPOST('search_value','alpha');
$search_quant=GETPOST('search_quant','alpha');
$search_price=GETPOST('search_price','alpha');
$search_balance_peps=GETPOST('search_balance_peps','alpha');
$search_balance_ueps=GETPOST('search_balance_ueps','alpha');
$search_price_peps=GETPOST('search_price_peps','alpha');
$search_price_ueps=GETPOST('search_price_ueps','alpha');
$search_type_mouvement=GETPOST('search_type_mouvement','int');
$search_fk_user_author=GETPOST('search_fk_user_author','int');
$search_label=GETPOST('search_label','alpha');
$search_fk_origin=GETPOST('search_fk_origin','int');
$search_origintype=GETPOST('search_origintype','alpha');
$search_inventorycode=GETPOST('search_inventorycode','alpha');
$search_batch=GETPOST('search_batch','alpha');
$search_statut=GETPOST('search_statut','int');



// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Stockmouvementtemp($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('stockmouvementtemp'));
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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/almacen/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */
		
	$object->entity=GETPOST('entity','int');
	$object->ref=GETPOST('ref','alpha');
	$object->fk_product=GETPOST('fk_product','int');
	$object->fk_entrepot=GETPOST('fk_entrepot','int');
	$object->fk_type_mov=GETPOST('fk_type_mov','int');
	$object->value=GETPOST('value','alpha');
	$object->quant=GETPOST('quant','alpha');
	$object->price=GETPOST('price','alpha');
	$object->balance_peps=GETPOST('balance_peps','alpha');
	$object->balance_ueps=GETPOST('balance_ueps','alpha');
	$object->price_peps=GETPOST('price_peps','alpha');
	$object->price_ueps=GETPOST('price_ueps','alpha');
	$object->type_mouvement=GETPOST('type_mouvement','int');
	$object->fk_user_author=GETPOST('fk_user_author','int');
	$object->label=GETPOST('label','alpha');
	$object->fk_origin=GETPOST('fk_origin','int');
	$object->origintype=GETPOST('origintype','alpha');
	$object->inventorycode=GETPOST('inventorycode','alpha');
	$object->batch=GETPOST('batch','alpha');
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
				$urltogo=$backtopage?$backtopage:dol_buildpath('/almacen/list.php',1);
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

		
	$object->entity=GETPOST('entity','int');
	$object->ref=GETPOST('ref','alpha');
	$object->fk_product=GETPOST('fk_product','int');
	$object->fk_entrepot=GETPOST('fk_entrepot','int');
	$object->fk_type_mov=GETPOST('fk_type_mov','int');
	$object->value=GETPOST('value','alpha');
	$object->quant=GETPOST('quant','alpha');
	$object->price=GETPOST('price','alpha');
	$object->balance_peps=GETPOST('balance_peps','alpha');
	$object->balance_ueps=GETPOST('balance_ueps','alpha');
	$object->price_peps=GETPOST('price_peps','alpha');
	$object->price_ueps=GETPOST('price_ueps','alpha');
	$object->type_mouvement=GETPOST('type_mouvement','int');
	$object->fk_user_author=GETPOST('fk_user_author','int');
	$object->label=GETPOST('label','alpha');
	$object->fk_origin=GETPOST('fk_origin','int');
	$object->origintype=GETPOST('origintype','alpha');
	$object->inventorycode=GETPOST('inventorycode','alpha');
	$object->batch=GETPOST('batch','alpha');
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
			header("Location: ".dol_buildpath('/almacen/list.php',1));
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.GETPOST('ref').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td><input class="flat" type="text" name="fk_product" value="'.GETPOST('fk_product').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_entrepot").'</td><td><input class="flat" type="text" name="fk_entrepot" value="'.GETPOST('fk_entrepot').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_type_mov").'</td><td><input class="flat" type="text" name="fk_type_mov" value="'.GETPOST('fk_type_mov').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalue").'</td><td><input class="flat" type="text" name="value" value="'.GETPOST('value').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldquant").'</td><td><input class="flat" type="text" name="quant" value="'.GETPOST('quant').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice").'</td><td><input class="flat" type="text" name="price" value="'.GETPOST('price').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbalance_peps").'</td><td><input class="flat" type="text" name="balance_peps" value="'.GETPOST('balance_peps').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbalance_ueps").'</td><td><input class="flat" type="text" name="balance_ueps" value="'.GETPOST('balance_ueps').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice_peps").'</td><td><input class="flat" type="text" name="price_peps" value="'.GETPOST('price_peps').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice_ueps").'</td><td><input class="flat" type="text" name="price_ueps" value="'.GETPOST('price_ueps').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_mouvement").'</td><td><input class="flat" type="text" name="type_mouvement" value="'.GETPOST('type_mouvement').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_author").'</td><td><input class="flat" type="text" name="fk_user_author" value="'.GETPOST('fk_user_author').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.GETPOST('label').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_origin").'</td><td><input class="flat" type="text" name="fk_origin" value="'.GETPOST('fk_origin').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldorigintype").'</td><td><input class="flat" type="text" name="origintype" value="'.GETPOST('origintype').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldinventorycode").'</td><td><input class="flat" type="text" name="inventorycode" value="'.GETPOST('inventorycode').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbatch").'</td><td><input class="flat" type="text" name="batch" value="'.GETPOST('batch').'"></td></tr>';
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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.$object->ref.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td><input class="flat" type="text" name="fk_product" value="'.$object->fk_product.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_entrepot").'</td><td><input class="flat" type="text" name="fk_entrepot" value="'.$object->fk_entrepot.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_type_mov").'</td><td><input class="flat" type="text" name="fk_type_mov" value="'.$object->fk_type_mov.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalue").'</td><td><input class="flat" type="text" name="value" value="'.$object->value.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldquant").'</td><td><input class="flat" type="text" name="quant" value="'.$object->quant.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice").'</td><td><input class="flat" type="text" name="price" value="'.$object->price.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbalance_peps").'</td><td><input class="flat" type="text" name="balance_peps" value="'.$object->balance_peps.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbalance_ueps").'</td><td><input class="flat" type="text" name="balance_ueps" value="'.$object->balance_ueps.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice_peps").'</td><td><input class="flat" type="text" name="price_peps" value="'.$object->price_peps.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice_ueps").'</td><td><input class="flat" type="text" name="price_ueps" value="'.$object->price_ueps.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_mouvement").'</td><td><input class="flat" type="text" name="type_mouvement" value="'.$object->type_mouvement.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_author").'</td><td><input class="flat" type="text" name="fk_user_author" value="'.$object->fk_user_author.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.$object->label.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_origin").'</td><td><input class="flat" type="text" name="fk_origin" value="'.$object->fk_origin.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldorigintype").'</td><td><input class="flat" type="text" name="origintype" value="'.$object->origintype.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldinventorycode").'</td><td><input class="flat" type="text" name="inventorycode" value="'.$object->inventorycode.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbatch").'</td><td><input class="flat" type="text" name="batch" value="'.$object->batch.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatut").'</td><td><input class="flat" type="text" name="statut" value="'.$object->statut.'"></td></tr>';

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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td>$object->entity</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td>$object->ref</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td>$object->fk_product</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_entrepot").'</td><td>$object->fk_entrepot</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_type_mov").'</td><td>$object->fk_type_mov</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvalue").'</td><td>$object->value</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldquant").'</td><td>$object->quant</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice").'</td><td>$object->price</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbalance_peps").'</td><td>$object->balance_peps</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbalance_ueps").'</td><td>$object->balance_ueps</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice_peps").'</td><td>$object->price_peps</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldprice_ueps").'</td><td>$object->price_ueps</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_mouvement").'</td><td>$object->type_mouvement</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_author").'</td><td>$object->fk_user_author</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td>$object->label</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_origin").'</td><td>$object->fk_origin</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldorigintype").'</td><td>$object->origintype</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldinventorycode").'</td><td>$object->inventorycode</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldbatch").'</td><td>$object->batch</td></tr>';
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
		if ($user->rights->almacen->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->almacen->delete)
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
