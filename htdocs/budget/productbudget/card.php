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
 *   	\file       budget/productbudget_card.php
 *		\ingroup    budget
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-10-26 15:47
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
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
dol_include_once('/budget/class/productbudgetext.class.php');
dol_include_once('/budget/class/budgetext.class.php');
dol_include_once('/budget/class/budgettaskresourceext.class.php');

dol_include_once('/user/class/user.class.php');
dol_include_once('/product/class/product.class.php');
dol_include_once('/budget/class/budgetgeneral.class.php');
dol_include_once('/budget/class/pustructureext.class.php');
dol_include_once('/categories/class/categorie.class.php');
dol_include_once('/budget/lib/budget.lib.php');

// Load traductions files requiredby by page
$langs->load("budget");
$langs->load("products");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$idr 		= GETPOST('idr','int');
$action		= GETPOST('action','alpha');
$confirm	= GETPOST('confirm','alpha');
$cancel		= GETPOST('cancel','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_fk_product=GETPOST('search_fk_product','int');
$search_fk_budget=GETPOST('search_fk_budget','int');
$search_ref=GETPOST('search_ref','alpha');
$search_label=GETPOST('search_label','alpha');
$search_fk_unit=GETPOST('search_fk_unit','int');
$search_code_structure=GETPOST('search_code_structure','alpha');
$search_quant=GETPOST('search_quant','alpha');
$search_amount=GETPOST('search_amount','alpha');
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
$object=new Productbudgetext($db);
$general = new Budgetgeneral($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
	$resultg = $general->fetch(0,$object->fk_budget);
	if ($resultg < 0) dol_print_error($db);
}
$product = new Product($db);
$budget = new Budgetext($db);
$objuser = new User($db);
$pustr = new Pustructureext($db);
$categorie = new Categorie($db);
$objBudgettaskresource = new Budgettaskresourceext($db);

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('productbudget'));
$extrafields = new ExtraFields($db);

$res = get_structure_budget($object->fk_budget);
$aStrbudget = unserialize($_SESSION['aStrbudget']);
$aCat = $aStrbudget[$object->fk_budget]['aStrlabel'];

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$pobject,$action);    // Note that $action and $pobject may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/productbudget.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */

		$object->fk_product=GETPOST('fk_product','int');
		$object->fk_budget=GETPOST('fk_budget','int');
		$object->ref=GETPOST('ref','alpha');
		$object->label=GETPOST('label','alpha');
		$object->fk_unit=GETPOST('fk_unit','int');
		$object->code_structure=GETPOST('code_structure','alpha');
		$object->quant=GETPOST('quant','alpha');
		$object->amount=GETPOST('amount','alpha');
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
				$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/list.php',1);
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
	if ($action == 'verupdate' && GETPOST('cancel')) $action='view';

	// Action to update record
	if ($action == 'confirm_update' && ! GETPOST('cancel') && $user->rights->budget->bpri->mod)
	{
		$error=0;
		$_POST = unserialize($_SESSION['aPost']);

		$object->fk_product=GETPOST('product','int');
		if (empty($object->fk_product)) $object->fk_product = 0;
		//$object->fk_budget=GETPOST('fk_budget','int');
		$object->ref=GETPOST('ref','alpha');
		$object->label=GETPOST('label','alpha');
		$object->fk_unit=GETPOST('fk_unit','int');
		$object->code_structure=GETPOST('code_structure','alpha');
		//$object->quant=GETPOST('quant','alpha');
		$object->fk_origin=GETPOST('fk_origin','int');
		$object->percent_origin=GETPOST('percent_origin','int');
		$object->percent_prod=GETPOST('percent_prod','alpha');
		$object->amount_noprod=GETPOST('amount_noprod','alpha');
		$object->amount=GETPOST('amount','alpha');
		$object->fk_user_mod=$user->id;
		$object->date_mod = dol_now();
		$object->tms = dol_now();

		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}
		if (empty($object->label))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Label")), null, 'errors');
		}
		if (empty($object->code_structure))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Codestructure")), null, 'errors');
		}
		if (empty($object->amount))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Amount")), null, 'errors');
		}

		if (! $error)
		{
			$db->begin();
			$result=$object->update($user);
			if ($result > 0)
			{
				//vamos a actualziar los datos detail y fk_unit
				$filter = " AND t.fk_product_budget = ".$id;
				$res = $objBudgettaskresource->fetchAll('','',0,0,array(),'AND',$filter);
				if ($res >0)
				{
					$lines = $objBudgettaskresource->lines;
					foreach ($lines AS $j => $line)
					{
						$res =$objBudgettaskresource->fetch($line->id);
						if ($res == 1)
						{
							$objBudgettaskresource->detail = $object->label;
							$objBudgettaskresource->fk_unit = $object->fk_unit;
							$objBudgettaskresource->fk_user_mod = $user->id;
							$objBudgettaskresource->datem = $now;
							$objBudgettaskresource->tms = $now;
							$res = $objBudgettaskresource->update($user);
							if ($res <=0)
							{
								$error++;
								setEventmessages($objBudgettaskresource->error,$objBudgettaskresource->errors,'errors');
							}
						}
					}
				}
				if (!$error)
				{
				//actualizamos los prcios en el proyecto
					require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettaskresourceext.class.php';
					$objbtr = new Budgettaskresourceext($db);
					$res = $objbtr->update_unit_price($user, $object->id, $object->amount,$object->amount_noprod);
					if ($res <=0)
					{
						$error++;
						setEventMessages($objbtr->error,$objbtr->errors,'errors');
					}
					if (!$error)
					{
						$objBudget = new Budgetext($db);
						$action='view';
					//se tiene que actualizar todo el presupuesto
						$res = $objBudget->fetch($object->fk_budget);
						if ($res==1 && $objBudget->id == $object->fk_budget)
						{
							$res = $objBudget->update_pu_all($user,$aStrbudget,'general');
							if ($res<=0)
							{
								$error++;
								setEventMessages($langs->trans('Errorinbudgetpriceupdate'),null,'errors');
							}
						}
					}
				}
			}
			else
			{
				// Creation KO
				if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else setEventMessages($object->error, null, 'errors');
				$action='edit';
			}
			if (!$error)
			{
				$db->commit();
				//setEventMessages($langs->trans('Updatesatisfactory'),null,'mesgs');
				header('Location: '.$_SERVER['PHP_SELF'].'?id='.$object->id);
				exit;
			}
			else
			{
				$db->rollback();
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
		$fk_budget = $object->fk_budget;
		$result=$object->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/budget/budget/card.php?id='.$fk_budget,1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else setEventMessages($object->error, null, 'errors');
		}
		$action = '';
	}
}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/
$title = $langs->trans('Priceunits');
$morejs = array('/budget/js/priceunit.js',);
if ($action == 'edit')
	$morecss = array('/budget/css/style.css','/budget/css/bootstrap.min.css',);
llxHeader('',$title,'','','','',$morejs,$morecss,0,0);


$form=new Formv($db);


// Put here content of your page

// Part to create
if ($action == 'create' && $abc)
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
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td>';
	//$form->select_produits_v($newdata->fk_product,'product','',$conf->product->limit_size,0,-1,2,'',1,$ajaxOptions,'','','resource');
	$form->select_produits_v($newdata->fk_product,'product','',$conf->product->limit_size,0,-1,2,'',1,$ajaxOptions);
	//$form->select_produits_v($idprod,'idprod',$filtertype,$conf->product->limit_size,$buyer->price_level,-1,2,'',1);
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_budget").'</td><td><input class="flat" type="text" name="fk_budget" value="'.GETPOST('fk_budget').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.GETPOST('ref').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.GETPOST('label').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit").'</td><td><input class="flat" type="text" name="fk_unit" value="'.GETPOST('fk_unit').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode_structure").'</td><td><input class="flat" type="text" name="code_structure" value="'.GETPOST('code_structure').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldquant").'</td><td><input class="flat" type="text" name="quant" value="'.GETPOST('quant').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td><input class="flat" type="text" name="amount" value="'.GETPOST('amount').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.GETPOST('fk_user_create').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.GETPOST('fk_user_mod').'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.GETPOST('status').'"></td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}


// Part to edit record
if (($id || $ref) && $action == 'edit' && $user->rights->budget->bpri->mod)
{
	//$aCat = array ('MAT'=>$langs->trans('Material'),'MDO'=>$langs->trans('Mano de Obra'),'EMH'=>$langs->trans('Equipo y Maquinaria'));

	print load_fiche_titre($langs->trans("Budgetprices"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="verupdate">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	//print '<input type="hidden" name="idr" value="'.$object->id.'">';

	dol_fiche_head();

	print '<div class="alert alert-warning" alert-dismissable>';
	print '<button type="button" class="close" data-dismiss="alert">&times;</button>';
	print '<strong>¡'.$langs->trans('Warning').'!</strong>';
	print ' '.$langs->trans('La modificación del registro afectara a todo el presupuesto, actualizandose los precios unitarios de todos los items');
	print '</div>';
	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_product").'</td><td>';
	$form->select_produits_v($object->fk_product,'product','',$conf->product->limit_size,0,1,2,'',1,array(),0,1,0);
	//$form->select_produits($object->fk_product, 'productid', '', $conf->product->limit_size, 0, $status=1, $finished=2,'', 1, array(), $socid=0, $showempty='1', $forcecombo=0, $morecss='', $hidepriceinlabel=0, $warehouseStatus='');
	print '</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_budget").'</td><td><input class="flat" type="text" name="fk_budget" value="'.$object->fk_budget.'"></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldref").'</td><td><input class="flat" type="text" name="ref" value="'.$object->ref.'" required></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldlabel").'</td><td><input class="flat" type="text" name="label" value="'.$object->label.'" required></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_unit").'</td><td>';
	print $form->selectUnits($object->fk_unit,'fk_unit',0);
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcode_structure").'</td><td>';
	print $form->selectarray('code_structure',$aCat,(GETPOST('code_categorie')?GETPOST('code_categorie'):$object->code_structure),1);
	print '</td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldquant").'</td><td><input class="flat" type="text" name="quant" value="'.$object->quant.'"></td></tr>';
	print '<tr><td class="fieldrequired"><label for="selectcountry_id">'.$langs->trans("Fieldfk_origin").'</label></td><td class="maxwidthonsmartphone">';
	print $form->select_country((GETPOST('fk_origin')?GETPOST('fk_origin'):($object->fk_origin?$object->fk_origin:$mysoc->country_id)),'fk_origin');
	if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
	print '</td></tr>'."\n";
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpercent_origin").'</td><td><input class="flat" type="number" step="any" min="0" max="100" name="percent_origin" value="'.price2num($object->percent_origin?$object->percent_origin:100,'MT').'"> %</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpercentprod").'</td><td><input class="flat" type="number" step="any" min="0" name="percent_prod" value="'.($object->percent_prod?$object->percent_prod:100).'" required> %</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamountnoprod").'</td><td><input class="flat" type="number" step="any" min="0" name="amount_noprod" value="'.$object->amount_noprod.'" required></td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Fieldamount").'</td><td><input class="flat" type="number" step="any" min="0" name="amount" value="'.$object->amount.'" required></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_create").'</td><td><input class="flat" type="text" name="fk_user_create" value="'.$object->fk_user_create.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user_mod").'</td><td><input class="flat" type="text" name="fk_user_mod" value="'.$object->fk_user_mod.'"></td></tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.$object->status.'"></td></tr>';

	print '</table>';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}

// Part to show record
if ($id && (empty($action) || $action == 'view' || $action == 'delete' || $action == 'verupdate'))
{
	//$head = productbudget_prepare_head($object,$user);
	dol_fiche_head($head, 'card', $langs->trans("Budgetprices"),0,'item');

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('DeleteMyOjbect'), $langs->trans('ConfirmDeleteMyObject'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}
	if ($action == 'verupdate') {
		$_SESSION['aPost'] = serialize($_POST);
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('Updateregistration'), $langs->trans('ConfirmRecordUpdate').' '.$object->ref.' '.$object->label, 'confirm_update', '', 0, 1);
		print $formconfirm;
	}

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
	if ($object->fk_product) $product->fetch($object->fk_product);
	print '<tr><td>'.$langs->trans("Fieldfk_product").'</td><td>'.($object->fk_product?$product->getNomUrl(1):$langs->trans('Undefined')).'</td></tr>';
	$budget->fetch($object->fk_budget);
	print '<tr><td>'.$langs->trans("Fieldfk_budget").'</td><td>'.$budget->getNomUrl(1).'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldref").'</td><td>'.$object->ref.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldlabel").'</td><td>'.$object->label.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldfk_unit").'</td><td>'.$object->getLabelOfUnit().'</td></tr>';
	$rescat = $categorie->fetch($object->code_structure);
	print '<tr><td>'.$langs->trans("Fieldcode_structure").'</td><td>'.$categorie->getNomUrl(1).'</td></tr>';
	//print '<tr><td>'.$langs->trans("Fieldquant").'</td><td>'.$object->quant.'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldfk_origin").'</td><td>';
	if ($object->fk_origin)
	{
		$tmparray=getCountry($object->fk_origin,'all');
		$country_code=$tmparray['code'];
		$country=$tmparray['label'];
		$img=picto_from_langcode($country_code);
		$origin =  $img?$img.' ':'';
		print $origin .= getCountry($country_code,1);
	}
	print '</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldpercent_origin").'</td><td>'.price2num($object->percent_origin,'MT').' %</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldpercentprod").'</td><td>'.price2num($object->percent_prod,'MU').' %</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldamountnoprod").'</td><td>'.price2num($object->amount_noprod,$general->decimal_pu).'</td></tr>';

	print '<tr><td>'.$langs->trans("Fieldamount").'</td><td>'.price2num($object->amount,$general->decimal_pu).'</td></tr>';
	$objuser->fetch($object->fk_user_create);
	print '<tr><td>'.$langs->trans("Fieldfk_user_create").'</td><td>'.$objuser->getNomUrl(1).'</td></tr>';
	$objuser->fetch($object->fk_user_mod);
	print '<tr><td>'.$langs->trans("Fieldfk_user_mod").'</td><td>'.$objuser->getNomUrl(1).'</td></tr>';
	print '<tr><td>'.$langs->trans("Fieldstatus").'</td><td>'.$object->getLibStatut(2).'</td></tr>';

	print '</table>';

	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$pobject,$action);    // Note that $action and $pobject may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		print '<div class="inline-block divButAction"><a class="butAction" href="'.DOL_URL_ROOT.'/budget/budget/resources.php?id='.$object->fk_budget.'">'.$langs->trans("Return").'</a></div>'."\n";

		if ($user->rights->budget->bpri->mod)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->budget->bpri->del)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
		}
	}
	print '</div>'."\n";
}


// End of page
llxFooter();
$db->close();
