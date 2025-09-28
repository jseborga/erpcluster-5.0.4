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
 *   	\file       purchase/facturefournadd_card.php
 *		\ingroup    purchase
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2017-04-25 21:23
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
dol_include_once('/purchase/class/facturefournadd.class.php');

// Load traductions files requiredby by page
$langs->load("purchase");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$cancel     = GETPOST('cancel');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_fk_facture_fourn=GETPOST('search_fk_facture_fourn','int');
$search_object=GETPOST('search_object','alpha');
$search_fk_object=GETPOST('search_fk_object','int');
$search_fk_projet_task=GETPOST('search_fk_projet_task','int');
$search_code_facture=GETPOST('search_code_facture','alpha');
$search_code_type_purchase=GETPOST('search_code_type_purchase','alpha');
$search_nit_company=GETPOST('search_nit_company','alpha');
$search_nfiscal=GETPOST('search_nfiscal','int');
$search_ndui=GETPOST('search_ndui','alpha');
$search_num_autoriz=GETPOST('search_num_autoriz','alpha');
$search_nit=GETPOST('search_nit','alpha');
$search_razsoc=GETPOST('search_razsoc','alpha');
$search_cod_control=GETPOST('search_cod_control','alpha');
$search_codqr=GETPOST('search_codqr','alpha');
$search_amountfiscal=GETPOST('search_amountfiscal','alpha');
$search_amountnofiscal=GETPOST('search_amountnofiscal','alpha');
$search_amount_ice=GETPOST('search_amount_ice','alpha');
$search_discount=GETPOST('search_discount','alpha');
$search_localtax3=GETPOST('search_localtax3','alpha');
$search_localtax4=GETPOST('search_localtax4','alpha');
$search_localtax5=GETPOST('search_localtax5','alpha');
$search_localtax6=GETPOST('search_localtax6','alpha');
$search_localtax7=GETPOST('search_localtax7','alpha');
$search_localtax8=GETPOST('search_localtax8','alpha');
$search_localtax9=GETPOST('search_localtax9','alpha');



if (empty($action) && empty($id) && empty($ref)) $action='view';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
//$result = restrictedArea($user, 'purchase', $id);


$object = new Facturefournadd($db);
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('facturefournadd'));



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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/purchase/list.php',1);
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
			$urltogo=$backtopage?$backtopage:dol_buildpath('/purchase/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */
		
	$object->fk_facture_fourn=GETPOST('fk_facture_fourn','int');
	$object->object=GETPOST('object','alpha');
	$object->fk_object=GETPOST('fk_object','int');
	$object->fk_projet_task=GETPOST('fk_projet_task','int');
	$object->code_facture=GETPOST('code_facture','alpha');
	$object->code_type_purchase=GETPOST('code_type_purchase','alpha');
	$object->nit_company=GETPOST('nit_company','alpha');
	$object->nfiscal=GETPOST('nfiscal','int');
	$object->ndui=GETPOST('ndui','alpha');
	$object->num_autoriz=GETPOST('num_autoriz','alpha');
	$object->nit=GETPOST('nit','alpha');
	$object->razsoc=GETPOST('razsoc','alpha');
	$object->cod_control=GETPOST('cod_control','alpha');
	$object->codqr=GETPOST('codqr','alpha');
	$object->amountfiscal=GETPOST('amountfiscal','alpha');
	$object->amountnofiscal=GETPOST('amountnofiscal','alpha');
	$object->amount_ice=GETPOST('amount_ice','alpha');
	$object->discount=GETPOST('discount','alpha');
	$object->localtax3=GETPOST('localtax3','alpha');
	$object->localtax4=GETPOST('localtax4','alpha');
	$object->localtax5=GETPOST('localtax5','alpha');
	$object->localtax6=GETPOST('localtax6','alpha');
	$object->localtax7=GETPOST('localtax7','alpha');
	$object->localtax8=GETPOST('localtax8','alpha');
	$object->localtax9=GETPOST('localtax9','alpha');

		

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

	// Action to update record
	if ($action == 'update')
	{
		$error=0;

		
	$object->fk_facture_fourn=GETPOST('fk_facture_fourn','int');
	$object->object=GETPOST('object','alpha');
	$object->fk_object=GETPOST('fk_object','int');
	$object->fk_projet_task=GETPOST('fk_projet_task','int');
	$object->code_facture=GETPOST('code_facture','alpha');
	$object->code_type_purchase=GETPOST('code_type_purchase','alpha');
	$object->nit_company=GETPOST('nit_company','alpha');
	$object->nfiscal=GETPOST('nfiscal','int');
	$object->ndui=GETPOST('ndui','alpha');
	$object->num_autoriz=GETPOST('num_autoriz','alpha');
	$object->nit=GETPOST('nit','alpha');
	$object->razsoc=GETPOST('razsoc','alpha');
	$object->cod_control=GETPOST('cod_control','alpha');
	$object->codqr=GETPOST('codqr','alpha');
	$object->amountfiscal=GETPOST('amountfiscal','alpha');
	$object->amountnofiscal=GETPOST('amountnofiscal','alpha');
	$object->amount_ice=GETPOST('amount_ice','alpha');
	$object->discount=GETPOST('discount','alpha');
	$object->localtax3=GETPOST('localtax3','alpha');
	$object->localtax4=GETPOST('localtax4','alpha');
	$object->localtax5=GETPOST('localtax5','alpha');
	$object->localtax6=GETPOST('localtax6','alpha');
	$object->localtax7=GETPOST('localtax7','alpha');
	$object->localtax8=GETPOST('localtax8','alpha');
	$object->localtax9=GETPOST('localtax9','alpha');

		

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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_facture_fourn").'</td><td><input class="flat" type="text" name="fk_facture_fourn" value="'.GETPOST('fk_facture_fourn').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldobject").'</td><td><input class="flat" type="text" name="object" value="'.GETPOST('object').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_object").'</td><td><input class="flat" type="text" name="fk_object" value="'.GETPOST('fk_object').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_projet_task").'</td><td><input class="flat" type="text" name="fk_projet_task" value="'.GETPOST('fk_projet_task').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode_facture").'</td><td><input class="flat" type="text" name="code_facture" value="'.GETPOST('code_facture').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode_type_purchase").'</td><td><input class="flat" type="text" name="code_type_purchase" value="'.GETPOST('code_type_purchase').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnit_company").'</td><td><input class="flat" type="text" name="nit_company" value="'.GETPOST('nit_company').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnfiscal").'</td><td><input class="flat" type="text" name="nfiscal" value="'.GETPOST('nfiscal').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldndui").'</td><td><input class="flat" type="text" name="ndui" value="'.GETPOST('ndui').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnum_autoriz").'</td><td><input class="flat" type="text" name="num_autoriz" value="'.GETPOST('num_autoriz').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnit").'</td><td><input class="flat" type="text" name="nit" value="'.GETPOST('nit').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrazsoc").'</td><td><input class="flat" type="text" name="razsoc" value="'.GETPOST('razsoc').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcod_control").'</td><td><input class="flat" type="text" name="cod_control" value="'.GETPOST('cod_control').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcodqr").'</td><td><input class="flat" type="text" name="codqr" value="'.GETPOST('codqr').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamountfiscal").'</td><td><input class="flat" type="text" name="amountfiscal" value="'.GETPOST('amountfiscal').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamountnofiscal").'</td><td><input class="flat" type="text" name="amountnofiscal" value="'.GETPOST('amountnofiscal').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_ice").'</td><td><input class="flat" type="text" name="amount_ice" value="'.GETPOST('amount_ice').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddiscount").'</td><td><input class="flat" type="text" name="discount" value="'.GETPOST('discount').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax3").'</td><td><input class="flat" type="text" name="localtax3" value="'.GETPOST('localtax3').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax4").'</td><td><input class="flat" type="text" name="localtax4" value="'.GETPOST('localtax4').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax5").'</td><td><input class="flat" type="text" name="localtax5" value="'.GETPOST('localtax5').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax6").'</td><td><input class="flat" type="text" name="localtax6" value="'.GETPOST('localtax6').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax7").'</td><td><input class="flat" type="text" name="localtax7" value="'.GETPOST('localtax7').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax8").'</td><td><input class="flat" type="text" name="localtax8" value="'.GETPOST('localtax8').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax9").'</td><td><input class="flat" type="text" name="localtax9" value="'.GETPOST('localtax9').'"></td></tr>';

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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_facture_fourn").'</td><td><input class="flat" type="text" name="fk_facture_fourn" value="'.$object->fk_facture_fourn.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldobject").'</td><td><input class="flat" type="text" name="object" value="'.$object->object.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_object").'</td><td><input class="flat" type="text" name="fk_object" value="'.$object->fk_object.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_projet_task").'</td><td><input class="flat" type="text" name="fk_projet_task" value="'.$object->fk_projet_task.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode_facture").'</td><td><input class="flat" type="text" name="code_facture" value="'.$object->code_facture.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode_type_purchase").'</td><td><input class="flat" type="text" name="code_type_purchase" value="'.$object->code_type_purchase.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnit_company").'</td><td><input class="flat" type="text" name="nit_company" value="'.$object->nit_company.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnfiscal").'</td><td><input class="flat" type="text" name="nfiscal" value="'.$object->nfiscal.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldndui").'</td><td><input class="flat" type="text" name="ndui" value="'.$object->ndui.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnum_autoriz").'</td><td><input class="flat" type="text" name="num_autoriz" value="'.$object->num_autoriz.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnit").'</td><td><input class="flat" type="text" name="nit" value="'.$object->nit.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrazsoc").'</td><td><input class="flat" type="text" name="razsoc" value="'.$object->razsoc.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcod_control").'</td><td><input class="flat" type="text" name="cod_control" value="'.$object->cod_control.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcodqr").'</td><td><input class="flat" type="text" name="codqr" value="'.$object->codqr.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamountfiscal").'</td><td><input class="flat" type="text" name="amountfiscal" value="'.$object->amountfiscal.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamountnofiscal").'</td><td><input class="flat" type="text" name="amountnofiscal" value="'.$object->amountnofiscal.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_ice").'</td><td><input class="flat" type="text" name="amount_ice" value="'.$object->amount_ice.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddiscount").'</td><td><input class="flat" type="text" name="discount" value="'.$object->discount.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax3").'</td><td><input class="flat" type="text" name="localtax3" value="'.$object->localtax3.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax4").'</td><td><input class="flat" type="text" name="localtax4" value="'.$object->localtax4.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax5").'</td><td><input class="flat" type="text" name="localtax5" value="'.$object->localtax5.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax6").'</td><td><input class="flat" type="text" name="localtax6" value="'.$object->localtax6.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax7").'</td><td><input class="flat" type="text" name="localtax7" value="'.$object->localtax7.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax8").'</td><td><input class="flat" type="text" name="localtax8" value="'.$object->localtax8.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax9").'</td><td><input class="flat" type="text" name="localtax9" value="'.$object->localtax9.'"></td></tr>';

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
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_facture_fourn").'</td><td>$object->fk_facture_fourn</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldobject").'</td><td>$object->object</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_object").'</td><td>$object->fk_object</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_projet_task").'</td><td>$object->fk_projet_task</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode_facture").'</td><td>$object->code_facture</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode_type_purchase").'</td><td>$object->code_type_purchase</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnit_company").'</td><td>$object->nit_company</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnfiscal").'</td><td>$object->nfiscal</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldndui").'</td><td>$object->ndui</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnum_autoriz").'</td><td>$object->num_autoriz</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnit").'</td><td>$object->nit</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldrazsoc").'</td><td>$object->razsoc</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcod_control").'</td><td>$object->cod_control</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcodqr").'</td><td>$object->codqr</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamountfiscal").'</td><td>$object->amountfiscal</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamountnofiscal").'</td><td>$object->amountnofiscal</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount_ice").'</td><td>$object->amount_ice</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fielddiscount").'</td><td>$object->discount</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax3").'</td><td>$object->localtax3</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax4").'</td><td>$object->localtax4</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax5").'</td><td>$object->localtax5</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax6").'</td><td>$object->localtax6</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax7").'</td><td>$object->localtax7</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax8").'</td><td>$object->localtax8</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlocaltax9").'</td><td>$object->localtax9</td></tr>';

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
	// Show links to link elements
	//$linktoelem = $form->showLinkToObjectBlock($object, null, array('facturefournadd'));
	//$somethingshown = $form->showLinkedObjectBlock($object, $linktoelem);

}


// End of page
llxFooter();
$db->close();
