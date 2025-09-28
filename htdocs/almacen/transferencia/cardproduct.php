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
 *   	\file       almacen/stockmouvementdoc_card.php
 *		\ingroup    almacen
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-02-02 15:29
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
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php');
dol_include_once('/almacen/class/stockmouvementdocext.class.php');
dol_include_once('/almacen/class/stockmouvementtempext.class.php');
dol_include_once('/almacen/class/entrepotext.class.php');
dol_include_once('/almacen/class/ctypemouvement.class.php');
dol_include_once('/orgman/class/pdepartamentext.class.php');
dol_include_once('/societe/class/societe.class.php');
dol_include_once('/user/class/user.class.php');
dol_include_once('/product/class/product.class.php');
include_once(DOL_DOCUMENT_ROOT.'/almacen/lib/almacen.lib.php');
// Load traductions files requiredby by page
$langs->load("almacen");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$idr = GETPOST('idr','int');
$action		= GETPOST('action','alpha');
$cancel     = GETPOST('cancel');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_entity=GETPOST('search_entity','int');
$search_ref=GETPOST('search_ref','alpha');
$search_ref_ext=GETPOST('search_ref_ext','alpha');
$search_fk_entrepot_from=GETPOST('search_fk_entrepot_from','int');
$search_fk_entrepot_to=GETPOST('search_fk_entrepot_to','int');
$search_fk_departament=GETPOST('search_fk_departament','int');
$search_fk_soc=GETPOST('search_fk_soc','int');
$search_fk_type_mov=GETPOST('search_fk_type_mov','int');
$search_fk_source=GETPOST('search_fk_source','int');
$search_model_pdf=GETPOST('search_model_pdf','alpha');
$search_label=GETPOST('search_label','alpha');
$search_fk_user_create=GETPOST('search_fk_user_create','int');
$search_fk_user_mod=GETPOST('search_fk_user_mod','int');
$search_statut=GETPOST('search_statut','int');



if (empty($action) && empty($id) && empty($ref)) $action='view';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}
//$result = restrictedArea($user, 'almacen', $id);


$object = new Stockmouvementdocext($db);
$extrafields = new ExtraFields($db);

$objSociete = new Societe($db);
$objEntrepot = new Entrepotext($db);
$objDepartamemt = new Pdepartamentext($db);
$objUser = new User($db);
$objTypemov = new Ctypemouvement($db);
$objStockdoc = new Stockmouvementdocext($db);
$objStocktemp = new Stockmouvementtempext($db);
$objProduct = new Product($db);
// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('stockmouvementdoc'));



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
$now = dol_now();

if (empty($reshook))
{
	if ($cancel)
	{
		if ($action != 'addlink')
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/almacen/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}
		if ($id > 0 || ! empty($ref)) $ret = $object->fetch($id,$ref);
		$action='';
	}

	if ($action == 'updateline' && $user->rights->almacen->transf->write)
	{
		$objTypemov->fetch($object->fk_type_mov);
		$typemov = $objTypemov->type;
	//modificamos la linea
		$id = GETPOST('id');
		$idr = GETPOST('idr');
		$idto = GETPOST('idto');

		$res = $objStocktemp->fetch($idr);
		$product = new Product($db);
		$db->begin();
		$res =$product->fetch((!empty(GETPOST('idprod'))?GETPOST('idprod'):''),(!empty(GETPOST('search_idprod'))?GETPOST('search_idprod'):''));
		if ($res)
		{
			$objStocktemp->fk_product = $product->id;
			$objStocktemp->value = GETPOST('nbpiece');
			$objStocktemp->balance_peps = GETPOST('nbpiece');
			$objStocktemp->balance_ueps = GETPOST('nbpiece');
			if ($typemov=='E')
			{
				if ($conf->global->ALMACEN_MOUVEMENT_INPUT_VALUE_FOR_PRODUCT)
				{
					$objStocktemp->price = GETPOST('price')/GETPOST('nbpiece');
					$objStocktemp->price_peps = GETPOST('price')/GETPOST('nbpiece');
					$objStocktemp->price_ueps = GETPOST('price')/GETPOST('nbpiece');
				}
				else
				{
					$objStocktemp->price = GETPOST('price','int');
					$objStocktemp->price_peps = GETPOST('price','int');
					$objStocktemp->price_ueps = GETPOST('price','int');
				}
			}
			$res = $objStocktemp->updateline($user);
			if ($res <=0)
			{
				$error++;
				setEventMessages($objStocktemp->error,$objStocktemp->errors,'errors');
			}
			if ($idto>0)
			{
				$objStocktemp->fetch($idto);
				$objStocktemp->fk_product = $product->id;
				$objStocktemp->value = GETPOST('nbpiece')*-1;
				$objStocktemp->balance_peps = GETPOST('nbpiece')*-1;
				$objStocktemp->balance_ueps = GETPOST('nbpiece')*-1;
				if ($typemov=='E')
				{
					if ($conf->global->ALMACEN_MOUVEMENT_INPUT_VALUE_FOR_PRODUCT)
					{
						$objStocktemp->price = GETPOST('price')/GETPOST('nbpiece');
						$objStocktemp->price_peps = GETPOST('price')/GETPOST('nbpiece');
						$objStocktemp->price_ueps = GETPOST('price')/GETPOST('nbpiece');
					}
					else
					{
						$objStocktemp->price = GETPOST('price','int');
						$objStocktemp->price_peps = GETPOST('price','int');
						$objStocktemp->price_ueps = GETPOST('price','int');
					}
				}
				$res = $objStocktemp->updateline($user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($objStocktemp->error,$objStocktemp->errors,'errors');
				}

			}
		}
		else
		{
			$error++;
			setEventMessages($product->error,$product->errors,'errors');
		}
		if (!$error)
		{
			$db->commit();
			setEventMessages($langs->trans('Sucessfullupdate'),null,'mesgs');
			$action='';
		}
		else
			$db->rollback();
		$action = '';
	}

	// Action to delete
	if ($action == 'draft' && $object->statut == 1 && $user->rights->almacen->transf->write)
	{
		$object->statut = 0;
		$object->fk_user_mod = $user->id;
		$object->datem = $now;
		$result=$object->update($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDraft", null, 'mesgs');
			header("Location: ".dol_buildpath('/almacen/transferencia/cardproduct.php?id='.$id,1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else setEventMessages($object->error, null, 'errors');
		}
	}

	// Action to deleteline
	if ($action == 'deleteline')
	{
		$res = $objStocktemp->fetch($idr);
		if ($res==1)
		{
			$res = $objStocktemp->delete($user);
			if ($res<=0)
			{
				$error++;
				setEventMessages($objStocktemp->error,$objStocktemp->errors,'errors');
			}
		}
		if (!$error)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/almacen/transferencia/cardproduct.php?id='.$id,1));
			exit;
		}
		$action = '';
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
	// Action to delete
	if ($action == 'confirm_validate')
	{
		$object->statut = 1;
		$result=$object->update($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("Satisfactoryvalidation", null, 'mesgs');
			header("Location: ".dol_buildpath('/almacen/transferencia/fiche.php?id='.$id,1));
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

llxHeader('','Transferproduct','');

$form=new Formv($db);


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
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref_ext").'</td><td><input class="flat" type="text" name="ref_ext" value="'.GETPOST('ref_ext').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_entrepot_from").'</td><td><input class="flat" type="text" name="fk_entrepot_from" value="'.GETPOST('fk_entrepot_from').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_entrepot_to").'</td><td><input class="flat" type="text" name="fk_entrepot_to" value="'.GETPOST('fk_entrepot_to').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_departament").'</td><td><input class="flat" type="text" name="fk_departament" value="'.GETPOST('fk_departament').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_soc").'</td><td><input class="flat" type="text" name="fk_soc" value="'.GETPOST('fk_soc').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_type_mov").'</td><td><input class="flat" type="text" name="fk_type_mov" value="'.GETPOST('fk_type_mov').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_source").'</td><td><input class="flat" type="text" name="fk_source" value="'.GETPOST('fk_source').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodel_pdf").'</td><td><input class="flat" type="text" name="model_pdf" value="'.GETPOST('model_pdf').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.GETPOST('label').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.GETPOST('fk_user_create').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.GETPOST('fk_user_mod').'"></td></tr>';
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
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref_ext").'</td><td><input class="flat" type="text" name="ref_ext" value="'.$object->ref_ext.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_entrepot_from").'</td><td><input class="flat" type="text" name="fk_entrepot_from" value="'.$object->fk_entrepot_from.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_entrepot_to").'</td><td><input class="flat" type="text" name="fk_entrepot_to" value="'.$object->fk_entrepot_to.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_departament").'</td><td><input class="flat" type="text" name="fk_departament" value="'.$object->fk_departament.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_soc").'</td><td><input class="flat" type="text" name="fk_soc" value="'.$object->fk_soc.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_type_mov").'</td><td><input class="flat" type="text" name="fk_type_mov" value="'.$object->fk_type_mov.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_source").'</td><td><input class="flat" type="text" name="fk_source" value="'.$object->fk_source.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodel_pdf").'</td><td><input class="flat" type="text" name="model_pdf" value="'.$object->model_pdf.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.$object->label.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.$object->fk_user_create.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.$object->fk_user_mod.'"></td></tr>';
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
	$objTypemov->fetch ($object->fk_type_mov);
	print load_fiche_titre($langs->trans("Transferproduct"));

	$head = transfer_prepare_head($object);
	if ($objTypemov->type == 'E') $title=$langs->trans("Entry");
	if ($objTypemov->type == 'O') $title=$langs->trans("Output");
	if ($objTypemov->type == 'T') $title=$langs->trans("Transferproduct");
	dol_fiche_head($head, 'cardproduct', $title, 0, 'transfer');

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('DeleteMyOjbect'), $langs->trans('ConfirmDeleteMyObject'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}

	if ($action == 'validate') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('Validatetransfproduct'), $langs->trans('ConfirmValidatetransfproduct'), 'confirm_validate', '', 0, 1);
		print $formconfirm;
	}

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>'.$object->label.'</td></tr>';
	//
	print '<tr><td width="18%">'.$langs->trans("Fieldref").'</td><td>'.$object->ref.'</td></tr>';
	if (!empty($object->ref_ext))
		print '<tr><td>'.$langs->trans("Fieldref_ext").'</td><td>'.$object->ref_ext.'</td></tr>';
	if (!empty($object->fk_entrepot_from))
	{
		$objEntrepot->fetch($object->fk_entrepot_from);
		print '<tr><td>'.$langs->trans("Fieldfk_entrepot_from").'</td><td>'.$objEntrepot->getNomUrl(1).'</td></tr>';
	}
	if (!empty($object->fk_entrepot_from))
	{
		$objEntrepot->fetch($object->fk_entrepot_to);
		print '<tr><td>'.$langs->trans("Fieldfk_entrepot_to").'</td><td>'.$objEntrepot->getNomUrl(1).'</td></tr>';
	}
	if (!empty($object->fk_departament))
	{
		$objDepartamemt->fetch($object->fk_departament);

		print '<tr><td>'.$langs->trans("Fieldfk_departament").'</td><td>'.$objDepartamemt->getNomUrl(1).'</td></tr>';
	}
	if (!empty($object->fk_soc))
	{
		$objSociete->fetch($object->fk_soc);
		print '<tr><td>'.$langs->trans("Fieldfk_soc").'</td><td>'.$objSociete->getNomUrl(1).'</td></tr>';
	}
	if ($object->fk_type_mov)
	{
		$objTypemov->fetch($object->fk_type_mov);
		print '<tr><td>'.$langs->trans("Fieldfk_type_mov").'</td><td>'.$objTypemov->getNomUrl().'</td></tr>';
	}
	if ($object->fk_source)
		print '<tr><td>'.$langs->trans("Fieldfk_source").'</td><td>'.$object->fk_source.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldmodel_pdf").'</td><td>'.$object->model_pdf.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldlabel").'</td><td>'.$object->label.'</td></tr>';
	$objUser->fetch($object->fk_user_create);
	print '<tr><td>'.$langs->trans("Fieldfk_user_create").'</td><td>'.$objUser->getNomUrl(1).'</td></tr>';
	$objUser->fetch($object->fk_user_mod);
	print '<tr><td>'.$langs->trans("Fieldfk_user_mod").'</td><td>'.$objUser->getNomUrl(1).'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldstatut").'</td><td>'.$object->getLibStatut(1).'</td></tr>';

	print '</table>';

	dol_fiche_end();


	include DOL_DOCUMENT_ROOT.'/almacen/transferencia/tpl/list_transfertemp.tpl.php';

}


// End of page
llxFooter();
$db->close();
