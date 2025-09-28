<?php
/* Copyright (C) 2001-2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2013 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2004      Eric Seigne          <eric.seigne@ryxeo.com>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2010-2012 Juanjo Menent        <jmenent@2byte.es>
 * Copyright (C) 2012      Christophe Battarel  <christophe.battarel@altairis.fr>
 * Copyright (C) 2014      Ion Agorria          <ion@agorria.com>
 * Copyright (C) 2015      Alexandre Spangaro   <aspangaro.dolibarr@gmail.com>
 * Copyright (C) 2016      Ferran Marcet		<fmarcet@2byte.es>
 * Copyright (C) 2018      Ramiro Queso		<ramiroques@gmail.com>
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
 *  \file       htdocs/product/fournisseurs.php
 *  \ingroup    product
 *  \brief      Page of tab suppliers for products
 */

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/product.lib.php';
require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.product.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/budget/class/productasset.class.php';
require_once DOL_DOCUMENT_ROOT.'/budget/lib/budget.lib.php';

$langs->load("products");
$langs->load("productext@productext");
$langs->load("suppliers");
$langs->load("bills");
$langs->load("margins");

$id = GETPOST('id', 'int');
$idr= GETPOST('idr', 'int');
$ref = GETPOST('ref', 'alpha');
$rowid=GETPOST('rowid','int');
$tab = GETPOST('tab','alpha');
$action=GETPOST('action', 'alpha');
$cancel=GETPOST('cancel', 'alpha');
$socid=GETPOST('socid', 'int');
$cost_price=GETPOST('cost_price', 'alpha');
$backtopage=GETPOST('backtopage','alpha');
$error=0;
if (empty($tab))
{
	$tab = 'default';
}
// If socid provided by ajax company selector
if (! empty($_REQUEST['search_fourn_id']))
{
	$_GET['id_fourn'] = $_GET['search_fourn_id'];
	$_POST['id_fourn'] = $_POST['search_fourn_id'];
	$_REQUEST['id_fourn'] = $_REQUEST['search_fourn_id'];
}

// Security check
$fieldvalue = (! empty($id) ? $id : (! empty($ref) ? $ref : ''));
$fieldtype = (! empty($ref) ? 'ref' : 'rowid');
if ($user->societe_id) $socid=$user->societe_id;

$result=restrictedArea($user,'produit|service&fournisseur|budget',$fieldvalue,'product&product|budget','','',$fieldtype);

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('pricesuppliercard','globalcard'));

$object = new Product($db);
if ($id > 0 || $ref)
{
	$res = $object->fetch($id,$ref);
}

$sortfield = GETPOST("sortfield",'alpha');
$sortorder = GETPOST("sortorder",'alpha');

if (! $sortfield) $sortfield="s.nom";
if (! $sortorder) $sortorder="ASC";

$objUser = new User($db);
$objProductasset = new Productasset($db);

if ($id>0)
{
	$result = $objProductasset->fetch(0,$id);
}

$nDecimal = ($conf->global->BUDGET_DEFAULT_NUMBER_DECIMAL?$conf->global->BUDGET_DEFAULT_NUMBER_DECIMAL:3);

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/
$now = dol_now();
$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	if ($cancel)
	{
		if ($action != 'addlink')
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/productasset/card.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}
		if ($idr > 0) $ret = $objProductasset->fetch($idr);
		$action='';
	}

	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/productasset/card.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */
		$objProductasset->fk_product=$id;
		$objProductasset->cost_hour_productive=GETPOST('cost_hour_productive','int');
		if (empty($objProductasset->cost_hour_productive))$objProductasset->cost_hour_productive=0;
		$objProductasset->cost_hour_improductive=GETPOST('cost_hour_improductive','int');
		if (empty($objProductasset->cost_hour_improductive))$objProductasset->cost_hour_improductive=0;
		$objProductasset->cost_depreciation=GETPOST('cost_depreciation','int');
		if (empty($objProductasset->cost_depreciation))$objProductasset->cost_depreciation=0;
		$objProductasset->cost_interest=GETPOST('cost_interest','int');
		if (empty($objProductasset->cost_interest))$objProductasset->cost_interest=0;
		$objProductasset->cost_fuel_consumption=GETPOST('cost_fuel_consumption','int');
		if (empty($objProductasset->cost_fuel_consumption))$objProductasset->cost_fuel_consumption=0;
		$objProductasset->cost_lubricants=GETPOST('cost_lubricants','alpha');
		if (empty($objProductasset->cost_lubricants))$objProductasset->cost_lubricants=0;
		$objProductasset->cost_tires_replacement=GETPOST('cost_tires_replacement','int');
		if (empty($objProductasset->cost_tires_replacement))$objProductasset->cost_tires_replacement=0;
		$objProductasset->cost_repair=GETPOST('cost_repair','alpha');
		if (empty($objProductasset->cost_repair))$objProductasset->cost_repair=0;
		$objProductasset->cost_pu_improductive=GETPOST('cost_pu_improductive','int');
		if (empty($objProductasset->cost_pu_improductive))$objProductasset->cost_pu_improductive=0;
		$objProductasset->cost_pu_productive=GETPOST('cost_pu_productive','int');
		if (empty($objProductasset->cost_pu_productive))$objProductasset->cost_pu_productive=0;

		$objProductasset->percent_interest=GETPOST('percent_interest','int');
		if (empty($objProductasset->percent_interest))$objProductasset->percent_interest=0;
		$objProductasset->cost_acquisition=GETPOST('cost_acquisition','int');
		if (empty($objProductasset->cost_acquisition))$objProductasset->cost_acquisition=0;
		$objProductasset->engine_power=GETPOST('engine_power','int');
		if (empty($objProductasset->engine_power))$objProductasset->engine_power=0;
		$objProductasset->fk_type_engine=GETPOST('fk_type_engine','int');
		if (empty($objProductasset->fk_type_engine))$objProductasset->fk_type_engine=0;
		$objProductasset->cost_tires=GETPOST('cost_tires','int');
		if (empty($objProductasset->cost_tires))$objProductasset->cost_tires=0;
		$objProductasset->useful_life_tires=GETPOST('useful_life_tires','int');
		if (empty($objProductasset->useful_life_tires))$objProductasset->useful_life_tires=0;
		$objProductasset->useful_life_year=GETPOST('useful_life_year','int');
		if (empty($objProductasset->useful_life_year))$objProductasset->useful_life_year=0;
		$objProductasset->useful_life_hours=GETPOST('useful_life_hours','int');
		if (empty($objProductasset->useful_life_hours))$objProductasset->useful_life_hours=0;
		$objProductasset->percent_residual_value=GETPOST('percent_residual_value','int');
		if (empty($objProductasset->percent_residual_value))$objProductasset->percent_residual_value=0;
		$objProductasset->percent_repair=GETPOST('percent_repair','int');
		if (empty($objProductasset->percent_repair))$objProductasset->percent_repair=0;
		$objProductasset->formula=GETPOST('formula','alpha');

		$objProductasset->diesel_consumption=GETPOST('diesel_consumption','int');
		if (empty($objProductasset->diesel_consumption))$objProductasset->diesel_consumption=0;
		$objProductasset->diesel_lubricants=GETPOST('diesel_lubricants','int');
		if (empty($objProductasset->diesel_lubricants))$objProductasset->diesel_lubricants=0;
		$objProductasset->gasoline_consumption=GETPOST('gasoline_consumption','int');
		if (empty($objProductasset->gasoline_consumption))$objProductasset->gasoline_consumption=0;
		$objProductasset->gasoline_lubricants=GETPOST('gasoline_lubricants','int');
		if (empty($objProductasset->gasoline_lubricants))$objProductasset->gasoline_lubricants=0;

		$objProductasset->cost_diesel=GETPOST('cost_diesel','int');
		if (empty($objProductasset->cost_diesel))$objProductasset->cost_diesel=0;
		$objProductasset->cost_gasoline=GETPOST('cost_gasoline','int');
		if (empty($objProductasset->cost_gasoline))$objProductasset->cost_gasoline=0;
		$objProductasset->energy_kw=GETPOST('energy_kw','int');
		if (empty($objProductasset->energy_kw))$objProductasset->energy_kw=0;

		$objProductasset->fk_user_create=$user->id;
		$objProductasset->fk_user_mod=$user->id;
		$objProductasset->datec=$now;
		$objProductasset->datem=$now;
		$objProductasset->tms=$now;
		$objProductasset->status=0;

		if (empty($objProductasset->fk_product))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Product")), null, 'errors');
		}

		if (! $error)
		{
			$result=$objProductasset->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/productasset/card.php?id='.$object->id.'&tab='.$tab,1);
				header("Location: ".$urltogo);
				exit;
			}
			{
				// Creation KO
				if (! empty($objProductasset->errors)) setEventMessages(null, $objProductasset->errors, 'errors');
				else  setEventMessages($objProductasset->error, null, 'errors');
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
		if ($result==1)
		{
			$error=0;
			if ($tab == 'default')
			{
				$objProductasset->fk_product=$id;

				$objProductasset->cost_hour_productive=GETPOST('cost_hour_productive','int');
				if (empty($objProductasset->cost_hour_productive))$objProductasset->cost_hour_productive=0;
				$objProductasset->cost_hour_improductive=GETPOST('cost_hour_improductive','int');
				if (empty($objProductasset->cost_hour_improductive))$objProductasset->cost_hour_improductive=0;
				$objProductasset->cost_depreciation=GETPOST('cost_depreciation','int');
				if (empty($objProductasset->cost_depreciation))$objProductasset->cost_depreciation=0;
				$objProductasset->cost_interest=GETPOST('cost_interest','int');
				if (empty($objProductasset->cost_interest))$objProductasset->cost_interest=0;
				$objProductasset->cost_fuel_consumption=GETPOST('cost_fuel_consumption','int');
				if (empty($objProductasset->cost_fuel_consumption))$objProductasset->cost_fuel_consumption=0;
				$objProductasset->cost_lubricants=GETPOST('cost_lubricants','alpha');
				if (empty($objProductasset->cost_lubricants))$objProductasset->cost_lubricants=0;
				$objProductasset->cost_tires_replacement=GETPOST('cost_tires_replacement','int');
				if (empty($objProductasset->cost_tires_replacement))$objProductasset->cost_tires_replacement=0;
				$objProductasset->cost_repair=GETPOST('cost_repair','alpha');
				if (empty($objProductasset->cost_repair))$objProductasset->cost_repair=0;
				$objProductasset->cost_pu_improductive=GETPOST('cost_pu_improductive','int');
				if (empty($objProductasset->cost_pu_improductive))$objProductasset->cost_pu_improductive=0;
				$objProductasset->cost_pu_productive=GETPOST('cost_pu_productive','int');
				if (empty($objProductasset->cost_pu_productive))$objProductasset->cost_pu_productive=0;


			}
			if ($tab == 'technical')
			{
				$objProductasset->percent_interest=GETPOST('percent_interest','int');
				if (empty($objProductasset->percent_interest))$objProductasset->percent_interest=0;
				$objProductasset->cost_acquisition=GETPOST('cost_acquisition','int');
				if (empty($objProductasset->cost_acquisition))$objProductasset->cost_acquisition=0;
				$objProductasset->engine_power=GETPOST('engine_power','int');
				if (empty($objProductasset->engine_power))$objProductasset->engine_power=0;
				$objProductasset->fk_type_engine=GETPOST('fk_type_engine','int');
				if (empty($objProductasset->fk_type_engine))$objProductasset->fk_type_engine=0;
				$objProductasset->cost_tires=GETPOST('cost_tires','int');
				if (empty($objProductasset->cost_tires))$objProductasset->cost_tires=0;
				$objProductasset->useful_life_tires=GETPOST('useful_life_tires','int');
				if (empty($objProductasset->useful_life_tires))$objProductasset->useful_life_tires=0;
				$objProductasset->useful_life_year=GETPOST('useful_life_year','int');
				if (empty($objProductasset->useful_life_year))$objProductasset->useful_life_year=0;
				$objProductasset->useful_life_hours=GETPOST('useful_life_hours','int');
				if (empty($objProductasset->useful_life_hours))$objProductasset->useful_life_hours=0;
				$objProductasset->percent_residual_value=GETPOST('percent_residual_value','int');
				if (empty($objProductasset->percent_residual_value))$objProductasset->percent_residual_value=0;
				$objProductasset->percent_repair=GETPOST('percent_repair','int');
				if (empty($objProductasset->percent_repair))$objProductasset->percent_repair=0;
				$objProductasset->formula=GETPOST('formula','alpha');
			}
			if ($tab == 'factor')
			{
				$objProductasset->diesel_consumption=GETPOST('diesel_consumption','int');
				if (empty($objProductasset->diesel_consumption))$objProductasset->diesel_consumption=0;
				$objProductasset->diesel_lubricants=GETPOST('diesel_lubricants','int');
				if (empty($objProductasset->diesel_lubricants))$objProductasset->diesel_lubricants=0;
				$objProductasset->gasoline_consumption=GETPOST('gasoline_consumption','int');
				if (empty($objProductasset->gasoline_consumption))$objProductasset->gasoline_consumption=0;
				$objProductasset->gasoline_lubricants=GETPOST('gasoline_lubricants','int');
				if (empty($objProductasset->gasoline_lubricants))$objProductasset->gasoline_lubricants=0;
			}
			if ($tab == 'cost')
			{
				$objProductasset->cost_diesel=GETPOST('cost_diesel','int');
				if (empty($objProductasset->cost_diesel))$objProductasset->cost_diesel=0;
				$objProductasset->cost_gasoline=GETPOST('cost_gasoline','int');
				if (empty($objProductasset->cost_gasoline))$objProductasset->cost_gasoline=0;
				$objProductasset->energy_kw=GETPOST('energy_kw','int');
				if (empty($objProductasset->energy_kw))$objProductasset->energy_kw=0;
			}
			$objProductasset->fk_user_mod=$user->id;
			$objProductasset->datem=$now;
			$objProductasset->tms=$now;

			if (! $error)
			{
				$result=$objProductasset->update($user);
				if ($result > 0)
				{
					$action='view';
				}
				else
				{
				// Creation KO
					if (! empty($objProductasset->errors)) setEventMessages(null, $objProductasset->errors, 'errors');
					else setEventMessages($objProductasset->error, null, 'errors');
					$action='edit';
				}
			}
			else
			{
				$action='edit';
			}
		}
		else
		{
			$error++;
			setEventMessages($objProductasset->error,$objProductasset->errors,'errors');
		}
	}

	// Action to delete
	if ($action == 'confirm_delete')
	{
		$result=$objProductasset->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/budget/list.php',1));
			exit;
		}
		else
		{
			if (! empty($objProductasset->errors)) setEventMessages(null, $objProductasset->errors, 'errors');
			else setEventMessages($objProductasset->error, null, 'errors');
		}
	}
}





//armamos los arrays para cada maestro
$aCregiong = array();
$aCaltitude = array();
/*
 * view
 */


$title = $langs->trans('ProductServiceCard');
$helpurl = '';
$shortlabel = dol_trunc($object->label,16);
if (
	GETPOST("type") == '0' || ($object->type == Product::TYPE_PRODUCT))
{
	$title = $langs->trans('Product')." ". $shortlabel ." - ".$langs->trans('BuyingPrices');
	$helpurl='EN:Module_Products|FR:Module_Produits|ES:M&oacute;dulo_Productos';
}
if (
	GETPOST("type") == '1' || ($object->type == Product::TYPE_SERVICE))
{
	$title = $langs->trans('Service')." ". $shortlabel ." - ".$langs->trans('BuyingPrices');
	$helpurl='EN:Module_Services_En|FR:Module_Services|ES:M&oacute;dulo_Servicios';
}

llxHeader('', $title, $helpurl);

$form = new Form($db);

if ($id > 0 || $ref)
{
	if ($res)
	{
		$head=product_prepare_head($object);
		$titre=$langs->trans("CardProduct".$object->type);
		$picto=($object->type== Product::TYPE_SERVICE?'service':'product');
		dol_fiche_head($head, 'fixedasset', $titre, 0, $picto);

		$linkback = '<a href="'.DOL_URL_ROOT.'/product/list.php">'.$langs->trans("BackToList").'</a>';

		$shownav = 1;
		if ($user->societe_id && ! in_array('product', explode(',',$conf->global->MAIN_MODULES_FOR_EXTERNAL))) $shownav=0;

		dol_banner_tab($object, 'ref', $linkback, $shownav, 'ref');

		print '<div class="fichecenter">';

		print '<div class="underbanner clearboth"></div>';
		print '<table class="border tableforfield" width="100%">';

			// Minimum Price

					//print '<tr><td class="titlefield">'.$langs->trans("BuyingPriceMin").'</td>';
					//print '<td colspan="2">';
					//$product_fourn = new ProductFournisseur($db);
					//if ($product_fourn->find_min_price_product_fournisseur($object->id) > 0)
					//{
					//	if ($product_fourn->product_fourn_price_id > 0) print $product_fourn->display_price_product_fournisseur();
					//	else print $langs->trans("NotDefined");
					//}
					//print '</td></tr>';

			// Cost price. Can be used for margin module for option "calculate margin on explicit cost price
			// Accountancy sell code
		print '<tr><td>';
		$textdesc =$langs->trans("CostPriceDescription");
		$textdesc.="<br>".$langs->trans("CostPriceUsage");
		$text=$form->textwithpicto($langs->trans("CostPrice"), $textdesc, 1, 'help', '');
					//print $form->editfieldkey($text,'cost_price',$object->cost_price,$object,$user->rights->produit->creer||$user->rights->service->creer,'amount:6');
		print $langs->trans('Unit');
		print '</td><td colspan="2">';
					//print $form->editfieldval($text,'cost_price',$object->cost_price,$object,$user->rights->produit->creer||$user->rights->service->creer,'amount:6');
		print $object->getLabelOfUnit();
		print '</td></tr>';

		print '</table>';

		print '</div>';

		print '<div style="clear:both"></div>';

		dol_fiche_end();
	}

	if ($user->rights->budget->asset->read)
	{
		include DOL_DOCUMENT_ROOT.'/budget/productasset/tpl/productasset_card.tpl.php';
	}
}

else
{
	print $langs->trans("ErrorUnknown");
}


// End of page
llxFooter();
$db->close();
