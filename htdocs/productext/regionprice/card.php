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
require_once DOL_DOCUMENT_ROOT.'/product/dynamic_price/class/price_expression.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/dynamic_price/class/price_parser.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/productext/class/productregionprice.class.php';
if ($conf->orgman->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/cregiongeographic.class.php';
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/cdepartementsregion.class.php';
}
if ($conf->purchase->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/purchase/class/supplier_proposalext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/purchase/class/supplierproposaladdext.class.php';
}

$langs->load("products");
$langs->load("productext@productext");
$langs->load("suppliers");
$langs->load("bills");
$langs->load("margins");

$id = GETPOST('id', 'int');
$idr= GETPOST('idr', 'int');
$ref = GETPOST('ref', 'alpha');
$rowid=GETPOST('rowid','int');
$action=GETPOST('action', 'alpha');
$cancel=GETPOST('cancel', 'alpha');
$socid=GETPOST('socid', 'int');
$cost_price=GETPOST('cost_price', 'alpha');
$backtopage=GETPOST('backtopage','alpha');
$error=0;

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
$result=restrictedArea($user,'produit|service&fournisseur',$fieldvalue,'product&product','','',$fieldtype);

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('pricesuppliercard','globalcard'));

$object = new ProductFournisseur($db);
if ($id > 0 || $ref)
{
	$object->fetch($id,$ref);
}

$sortfield = GETPOST("sortfield",'alpha');
$sortorder = GETPOST("sortorder",'alpha');

if (! $sortfield) $sortfield="s.nom";
if (! $sortorder) $sortorder="ASC";

$objUser = new User($db);
$objSociete = new Societe($db);
$objCregiongeographic = new Cregiongeographic($db);
$objProductregionprice = new Productregionprice($db);
if ($conf->orgman->enabled)
{
	$objCregiongeographic = new Cregiongeographic($db);
	$objCdepartementsregion = new Cdepartementsregion($db);
}
if ($conf->purchase->enabled)
{
	$objSupplierproposal = new Supplierproposalext($db);
	$objSupplierproposalline = new SupplierProposalLineext($db);
	$objSupplierproposaladd = new Supplierproposaladdext($db);

}

/*
 * Actions
 */

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/
$now = dol_now();
$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$objProductregionprice,$action);    // Note that $action and $objProductregionprice may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	if ($cancel)
	{
		if ($action != 'addlink')
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/productext/regionprice/card.php?id='.$id,1);
			header("Location: ".$urltogo);
			exit;
		}
		if ($idr > 0) $ret = $objProductregionprice->fetch($idr);
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

		$objProductregionprice->entity=$conf->entity;
		$objProductregionprice->fk_region_geographic=GETPOST('fk_region_geographic','int');
		$objProductregionprice->fk_soc=GETPOST('fk_soc','int');
		$objProductregionprice->fk_supplier_proposal_det=0;
		$objProductregionprice->fk_product=GETPOST('fk_product','int');
		$objProductregionprice->price=GETPOST('price','alpha');
		$objProductregionprice->quantity=GETPOST('quantity','alpha');
		if (empty($objProductregionprice->quantity)) $objProductregionprice->quantity=1;
		$objProductregionprice->remise_percent=GETPOST('remise_percent','alpha');
		if (empty($objProductregionprice->remise_percent)) $objProductregionprice->remise_percent=0;
		$objProductregionprice->remise=GETPOST('remise','alpha');
		if (empty($objProductregionprice->remise)) $objProductregionprice->remise=0;
		$objProductregionprice->tva_tx=GETPOST('tva_tx','alpha');
		if (empty($objProductregionprice->tva_tx)) $objProductregionprice->tva_tx=0;
		$objProductregionprice->default_vat_code=GETPOST('default_vat_code','alpha');
		$objProductregionprice->info_bits=GETPOST('info_bits','int');
		if (empty($objProductregionprice->info_bits))$objProductregionprice->info_bits=0;
		$objProductregionprice->fk_user=$user->id;
		$objProductregionprice->import_key=0;
		$objProductregionprice->date_create = $now;
		$objProductregionprice->datec = $now;
		$objProductregionprice->datem = $now;
		$objProductregionprice->tms = $now;
		$objProductregionprice->status = 1;

		if ($objProductregionprice->fk_region_geographic<=0)
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldfk_region_geographic")), null, 'errors');
		}
		if ($objProductregionprice->fk_soc<=0)
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldfk_soc")), null, 'errors');
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
	if ($action == 'updateprice')
	{
		$error=0;
		if ($idr>0) $res = $objProductregionprice->fetch($idr);

		$objProductregionprice->entity=$conf->entity;
		$objProductregionprice->fk_region_geographic=GETPOST('fk_region_geographic','int');
		$objProductregionprice->fk_soc=GETPOST('fk_soc','int');
		$objProductregionprice->fk_product=$id;
		$objProductregionprice->fk_supplier_proposal_det=0;
		$objProductregionprice->price=GETPOST('price','alpha');
		$objProductregionprice->quantity=GETPOST('qty','int');
		$objProductregionprice->quantity=GETPOST('quantity','alpha');
		if (empty($objProductregionprice->quantity)) $objProductregionprice->quantity=1;
		$objProductregionprice->remise_percent=GETPOST('remise_percent','alpha');
		if (empty($objProductregionprice->remise_percent)) $objProductregionprice->remise_percent=0;
		$objProductregionprice->remise=GETPOST('remise','alpha');
		if (empty($objProductregionprice->remise)) $objProductregionprice->remise=0;
		$objProductregionprice->tva_tx=GETPOST('tva_tx','alpha');
		if (empty($objProductregionprice->tva_tx)) $objProductregionprice->tva_tx=0;
		$objProductregionprice->info_bits=GETPOST('info_bits','int');
		if (empty($objProductregionprice->info_bits))$objProductregionprice->info_bits=0;
		$objProductregionprice->fk_user=$user->id;
		$objProductregionprice->fk_user_create=$user->id;
		$objProductregionprice->fk_user_mod=$user->id;
		$objProductregionprice->datem=$now;
		$objProductregionprice->date_create=$now;
		$objProductregionprice->datec=$now;
		$objProductregionprice->import_key=GETPOST('import_key','alpha');

		if ($objProductregionprice->fk_region_geographic<=0)
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldfk_region_geographic")), null, 'errors');
		}
		if ($objProductregionprice->fk_soc<=0)
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldfk_soc")), null, 'errors');
		}
		if ($objProductregionprice->price<=0)
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldprice")), null, 'errors');
		}

		if (! $error)
		{
			if ($objProductregionprice->id >0)
				$result=$objProductregionprice->update($user);
			else
				$result=$objProductregionprice->create($user);
			if ($result > 0)
			{
				$action='';
			}
			else
			{
				// Creation KO
				if (! empty($objProductregionprice->errors)) setEventMessages(null, $objProductregionprice->errors, 'errors');
				else setEventMessages($objProductregionprice->error, null, 'errors');
				$action='add_price';
			}
		}
		else
		{
			$action='add_price';
		}
	}

	if ($id>0)
	{
		//buscamos por el producto = id
		$filter = " AND t.fk_product = ".$id;
		$error=0;
		$res = $objSupplierproposalline->fetchAll('','',0,0,array(),'AND',$filter);
		if ($res > 0)
		{
			$db->begin();
			$lines = $objSupplierproposalline->lines;
			foreach ($lines AS $j => $line)
			{
				if (!$error)
				{
					//vamos a buscar el padre de supplierproposaldet

					$resadd = $objSupplierproposaladd->fetch(0,$line->fk_supplier_proposal);
					if ($resadd==1)
					{
						$ressup = $objSupplierproposal->fetch($line->fk_supplier_proposal);
						//ya tenemos la provincia el societe y el producto
						//vamos a determinar que region geografica esta la provincia

						if ($objSupplierproposaladd->fk_province>0)
						{
							$resdep = $objCdepartementsregion->fetch(0,$objSupplierproposaladd->fk_province);
							if ($resdep==1)
							{
								//existe y ya sabemos a que region geografica pertenece
								//vamos a buscar dentro de producto_region_price
								$filterprod = " AND t.fk_product = ".$id;
								$filterprod.= " AND t.fk_region_geographic = ".$objCdepartementsregion->fk_region_geographic;
								$filterprod.= " AND t.entity = ".$conf->entity;
								$filterprod.= " AND t.fk_supplier_proposal_det = ".$line->id;
								$resprod=$objProductregionprice->fetchAll('','',0,0,array(),'AND',$filterprod);
								if (empty($resprod))
								{
									//agregamos el nuevo registro
									$objProductregionprice->initAsSpecimen();
									$objProductregionprice->entity = $conf->entity;
									$objProductregionprice->fk_region_geographic = $objCdepartementsregion->fk_region_geographic;
									$objProductregionprice->fk_soc = $objSupplierproposal->socid;
									$objProductregionprice->fk_supplier_proposal_det = $line->id;
									$objProductregionprice->date_create = $objSupplierproposal->date_creation;
									$objProductregionprice->datec = $now;
									$objProductregionprice->tms = $now;
									$objProductregionprice->fk_product = $id;
									$objProductregionprice->price = $line->price;
									$objProductregionprice->quantity = 1;
									$objProductregionprice->remise_percent = $line->remise_percent;
									$objProductregionprice->remise = $line->remise;
									$objProductregionprice->tva_tx = $line->tva_tx;
									$objProductregionprice->info_bits = 0;
									$objProductregionprice->fk_user = $user->id;
									$objProductregionprice->fk_user_create = $user->id;
									$objProductregionprice->fk_user_mod = $user->id;
									$objProductregionprice->datem = $now;
									$objProductregionprice->import_key = 2;

									$result = $objProductregionprice->create($user);
									if ($result <=0)
									{
										$error++;
										setEventMessages($objProductregionprice->error,$objProductregionprice->errors,'errors');
									}
								}
							}
						}
					}
				}
			}
			if (!$error) $db->commit();
			else $db->rollback();
		}
		elseif($res < 0)
		{
			$error++;
			setEventMessages($objSupplierproposalline->error,$objSupplierproposalline->errors,'errors');
		}
	}
}



//armamos los arrays para cada maestro
$aCregiong = array();
$aCaltitude = array();
$filter = " AND t.status = 1";
$res = $objCregiongeographic->fetchAll('ASC','t.label',0,0,array(),'AND',$filter);
if ($res>0)
{
	$lines = $objCregiongeographic->lines;
	foreach ($lines AS $j => $line)
		$aCregiong[$line->id] = $line->label.' ('.$line->ref.')';
}
/*
 * view
 */

$title = $langs->trans('ProductServiceCard');
$helpurl = '';
$shortlabel = dol_trunc($object->label,16);
if (GETPOST("type") == '0' || ($object->type == Product::TYPE_PRODUCT))
	{
		$title = $langs->trans('Product')." ". $shortlabel ." - ".$langs->trans('BuyingPrices');
		$helpurl='EN:Module_Products|FR:Module_Produits|ES:M&oacute;dulo_Productos';
	}
	if (GETPOST("type") == '1' || ($object->type == Product::TYPE_SERVICE))
		{
			$title = $langs->trans('Service')." ". $shortlabel ." - ".$langs->trans('BuyingPrices');
			$helpurl='EN:Module_Services_En|FR:Module_Services|ES:M&oacute;dulo_Servicios';
		}

		llxHeader('', $title, $helpurl);

		$form = new Form($db);

		if ($id > 0 || $ref)
		{
			if ($result)
			{
				if ($action == 'ask_remove_pf') {
					$form = new Form($db);
					$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $id . '&rowid=' . $rowid, $langs->trans('DeleteProductBuyPrice'), $langs->trans('ConfirmDeleteProductBuyPrice'), 'confirm_remove_pf', '', 0, 1);
					echo $formconfirm;
				}

				if ($action <> 'edit' && $action <> 're-edit')
				{
					$head=product_prepare_head($object);
					$titre=$langs->trans("CardProduct".$object->type);
					$picto=($object->type== Product::TYPE_SERVICE?'service':'product');
					dol_fiche_head($head, 'regionprice', $titre, 0, $picto);

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


					// Form to add or update a price
					if (($action == 'add_price' || $action == 'updateprice' ) && $user->rights->productext->regp->write)
					{
						if ($rowid)
						{
							$object->fetch_product_fournisseur_price($rowid, 1);
					//Ignore the math expression when getting the price
							print load_fiche_titre($langs->trans("ChangeSupplierPrice"));
						}
						else
						{
							print load_fiche_titre($langs->trans("AddPrice"));
						}

						print '<form action="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'" method="POST">';
						print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
						print '<input type="hidden" name="action" value="updateprice">';

						dol_fiche_head();

						print '<table class="border" width="100%">';

				// Regiongeographic
						print '<tr><td class="titlefield fieldrequired">'.$langs->trans("Cregiongeographic").'</td><td>';
						print $form->selectarray('fk_region_geographic',$aCregiong,(GETPOST('fk_region_geographic')?GETPOST('fk_region_geographic'):$objProductregionprice->fk_region_geographic),1);
						print '</td></tr>';
					// societe
						print '<tr><td class="titlefield fieldrequired">'.$langs->trans("Supplier").'</td><td>';
						print $form->select_company(GETPOST('fk_soc'),'fk_soc','s.fournisseur=1',1);
						print '</td></tr>';

				// Qty min
						print '<tr>';
						print '<td class="fieldrequired">'.$langs->trans("Qty").'</td>';
						print '<td>';
						$quantity = GETPOST('qty') ? GETPOST('qty') : $objProductregionprice->quantity;
						print '<input class="flat" name="qty" size="5" value="'.$quantity.'">';
						print '</td></tr>';

						print '<tr><td class="fieldrequired">'.$langs->trans("Price").'</td>';
						print '<td>';
						print '<input type="text" class="flat" size="5" name="price" value="'.(GETPOST("price")?GETPOST("tva_tx"):$objProductregionprice->price).'">';
						print '</td></tr>';


						if (is_object($hookmanager))
						{
							$parameters=array('id_fourn'=>$id_fourn,'prod_id'=>$object->id);
							$reshook=$hookmanager->executeHooks('formObjectOptions',$parameters,$object,$action);
						}

						print '</table>';

						dol_fiche_end();

						print '<div class="center">';
						print '<input class="button" type="submit" value="'.$langs->trans("Save").'">';
						print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
						print '<input class="button" type="submit" name="cancel" value="'.$langs->trans("Cancel").'">';
						print '</div>';

						print '</form>';
					}

			// Actions buttons

					print "\n<div class=\"tabsAction\">\n";

					if ($action != 'add_price' && $action != 'updateprice')
					{
						$parameters=array();
				$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
				if (empty($reshook))
				{
					if ($user->rights->productext->regp->write)
					{
						print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&amp;action=add_price">';
						print $langs->trans("AddPrice").'</a>';
						print '&nbsp;<a class="butAction" href="'.'ficheimport.php?id='.$object->id.'&amp;action=create">';
						print $langs->trans("Importprice").'</a>';
					}
				}
			}

			print "\n</div>\n";
			print '<br>';


			if ($user->rights->productext->regp->read)
			{
				include DOL_DOCUMENT_ROOT.'/productext/regionprice/tpl/productregionprice_list.tpl.php';
			}
		}
	}
}
else
{
	print $langs->trans("ErrorUnknown");
}


// End of page
llxFooter();
$db->close();
