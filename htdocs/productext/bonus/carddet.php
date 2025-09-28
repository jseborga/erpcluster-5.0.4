<?php
/* Copyright (C) 2001-2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2014 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005      Eric Seigne          <eric.seigne@ryxeo.com>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2006      Andre Cianfarani     <acianfa@free.fr>
 * Copyright (C) 2006      Auguria SARL         <info@auguria.org>
 * Copyright (C) 2010-2014 Juanjo Menent        <jmenent@2byte.es>
 * Copyright (C) 2013      Marcos García        <marcosgdf@gmail.com>
 * Copyright (C) 2013      Cédric Salvador      <csalvador@gpcsolutions.fr>
 * Copyright (C) 2011-2014 Alexandre Spangaro   <alexandre.spangaro@gmail.com>
 * Copyright (C) 2014      Cédric Gross         <c.gross@kreiz-it.fr>
 * Copyright (C) 2014	   Ferran Marcet		<fmarcet@2byte.es>
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
 *  \file       htdocs/product/fiche.php
 *  \ingroup    product
 *  \brief      Page to show product
 */

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/canvas.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/genericobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/product.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
if (! empty($conf->propal->enabled))   require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
if (! empty($conf->facture->enabled))  require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
if (! empty($conf->commande->enabled)) require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';
require_once DOL_DOCUMENT_ROOT.'/productext/class/productadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/productext/class/productbonusext.class.php';
require_once DOL_DOCUMENT_ROOT.'/productext/class/cbonustypeext.class.php';
require_once DOL_DOCUMENT_ROOT.'/productext/class/bonusext.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/productext/lib/productext.lib.php';


$langs->load("promotion");
$langs->load("fiscal");
$langs->load("products");
$langs->load("other");
if (! empty($conf->stock->enabled)) $langs->load("stocks");
if (! empty($conf->facture->enabled)) $langs->load("bills");
if ($conf->productbatch->enabled) $langs->load("productbatch");

$mesg=''; $error=0; $errors=array(); $_error=0;

$id=GETPOST('id', 'int');
$ref=GETPOST('ref', 'alpha');
$idr = GETPOST('idr','int');
$idrd = GETPOST('idrd','int');
$type=GETPOST('type','int');
$fk_type=GETPOST('fk_type','int');
$action=(GETPOST('action','alpha') ? GETPOST('action','alpha') : 'view');
$confirm=GETPOST('confirm','alpha');
$socid=GETPOST('socid','int');
if (! empty($user->societe_id)) $socid=$user->societe_id;

$object = new Product($db);
$extrafields = new ExtraFields($db);
$objectadd = new Productadd($db);
$objProductbonus = new Productbonusext($db);
$objCbonustype = new Cbonustypeext($db);
$objBonus = new Bonusext($db);
$objUser = new User($db);

// fetch optionals attributes and labels
$extralabels=$extrafields->fetch_name_optionals_label($object->table_element);
$extralabelsprom=$extrafields->fetch_name_optionals_label($objPromotion->table_element);

if ($id > 0 || ! empty($ref))
{
	$object = new Product($db);
	$object->fetch($id, $ref);
	$objectadd->fetch('',$object->id);
}
if ($idr > 0)
{
	$res = $objProductbonus->fetch($idr);
	if ($res==1)
	{
		$restype = $objCbonustype->fetch($objProductbonus->fk_bonus_type);
	}
}
if ($idrd > 0)
{
	$objBonus->fetch($idrd);
}

// Get object canvas (By default, this is not defined, so standard usage of dolibarr)
$canvas = !empty($object->canvas)?$object->canvas:GETPOST("canvas");
$objcanvas='';
if (! empty($canvas))
{
	require_once DOL_DOCUMENT_ROOT.'/core/class/canvas.class.php';
	$objcanvas = new Canvas($db,$action);
	$objcanvas->getCanvas('product','card',$canvas);
}

// Security check
$fieldvalue = (! empty($id) ? $id : (! empty($ref) ? $ref : ''));
$fieldtype = (! empty($ref) ? 'ref' : 'rowid');
$result=restrictedArea($user,'produit|service',$fieldvalue,'product&product','','',$fieldtype,$objcanvas);

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('productcard'));

$filter = " AND active = 1";
//$res = $objCtypepromotion->fetchAll('ASC','t.label',0,0,array(),'AND',$filter);
$optionstype = '<option value="">'.$langs->trans('Select').'</option>';
if ($res >0)
{
	//foreach ($objCtypepromotion->lines AS $j => $line)
	//{
	//	$selected = '';
	//	$fk_type = GETPOST('type')?GETPOST('type'):$objPromotion->fk_type_promotion;
	//	if ($fk_type == $line->id) $selected = ' selected';
	//	$optionstype.= '<option value="'.$line->id.'" '.$selected.'>'.$line->ref.' '.$line->label.'</option>';
	//}
}

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/
$now = dol_now();

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$objBonus,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	if ($cancel)
	{
		if ($action != 'addlink')
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/productext/bonus/carddet.php?id?'.$id.'&idr='.$idr,1);
			header("Location: ".$urltogo);
			exit;
		}
		if ($idrd > 0 || ! empty($ref)) $ret = $objBonus->fetch($idrd,$ref);
		$action='';
	}

	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/productext/bonus/carddet.php?id='.$id.'&idr='.$idr,1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */

		$objBonus->fk_product_bonus=$idr;
		$objBonus->qty_ini=GETPOST('qty_ini','alpha');
		if (empty($objBonus->qty_ini)) $objBonus->qty_ini = 0;
		$objBonus->qty_fin=GETPOST('qty_fin','alpha');
		if (empty($objBonus->qty_fin)) $objBonus->qty_fin = 0;
		$objBonus->active=GETPOST('active','int');
		$objBonus->fk_user_create=$user->id;
		$objBonus->fk_user_mod=$user->id;
		$objBonus->datec = $now;
		$objBonus->datem = $now;
		$objBonus->tms = $now;
		$objBonus->status=1;

		if ($objBonus->fk_product_bonus<=0)
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldfk_product_bonus")), null, 'errors');
		}
		if ($objBonus->qty_ini<=0)
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldqty_ini")), null, 'errors');
		}
		if ($objCbonustype->type == 'L')
		{
			if ($objBonus->qty_fin<=0)
			{
				$error++;
				setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldqty_fin")), null, 'errors');
			}
			if ($objBonus->qty_ini >$objBonus->qty_fin)
			{
				$error++;
				if ($objProductbonus->type_value == 'Q')
					setEventMessages($langs->trans("Thefinalamountcannotbelessthantheinitialamount"), null, 'errors');
				else
					setEventMessages($langs->trans("Thefinalvaluecannotbelessthantheinitialvalue"), null, 'errors');
			}
		}
		if (! $error)
		{
			$result=$objBonus->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/productext/bonus/carddet.php?id='.$id.'&idr='.$idr,1);
				header("Location: ".$urltogo);
				exit;
			}
			{
				// Creation KO
				if (! empty($objBonus->errors)) setEventMessages(null, $objBonus->errors, 'errors');
				else  setEventMessages($objBonus->error, null, 'errors');
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


		$objBonus->fk_product_bonus=$idr;
		$objBonus->qty_ini=GETPOST('qty_ini','alpha');
		if (empty($objBonus->qty_ini)) $objBonus->qty_ini = 0;
		$objBonus->qty_fin=GETPOST('qty_fin','alpha');
		if (empty($objBonus->qty_fin)) $objBonus->qty_fin = 0;
		$objBonus->active=GETPOST('active','int');
		$objBonus->fk_user_mod=$user->id;
		$objBonus->datem = $now;
		$objBonus->tms = $now;
		$objBonus->status=1;

		if ($objBonus->fk_product_bonus<=0)
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldfk_product_bonus")), null, 'errors');
		}
		if ($objBonus->qty_ini<=0)
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldqty_ini")), null, 'errors');
		}
		if ($objCbonustype->type == 'L')
		{
			if ($objBonus->qty_fin<=0)
			{
				$error++;
				setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldqty_fin")), null, 'errors');
			}
			if ($objBonus->qty_ini >$objBonus->qty_fin)
			{
				$error++;
				if ($objProductbonus->type_value == 'Q')
					setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Thefinalamountcannotbelessthantheinitialamount")), null, 'errors');
				else
					setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Thefinalvaluecannotbelessthantheinitialvalue")), null, 'errors');
			}
		}

		if (! $error)
		{
			$result=$objBonus->update($user);
			if ($result > 0)
			{
				$action='view';
			}
			else
			{
				// Creation KO
				if (! empty($objBonus->errors)) setEventMessages(null, $objBonus->errors, 'errors');
				else setEventMessages($objBonus->error, null, 'errors');
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
		$result=$objBonus->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/productext/list.php',1));
			exit;
		}
		else
		{
			if (! empty($objBonus->errors)) setEventMessages(null, $objBonus->errors, 'errors');
			else setEventMessages($objBonus->error, null, 'errors');
		}
	}
}




$aTypevalue = array('V'=>$langs->trans('Value'),'Q'=>$langs->trans('Quantity'));


/*
 * View
 */

$helpurl='';
if (GETPOST("type") == '0' || ($object->type == '0')) $helpurl='EN:Module_Products|FR:Module_Produits|ES:M&oacute;dulo_Productos';
	if (GETPOST("type") == '1' || ($object->type == '1')) $helpurl='EN:Module_Services_En|FR:Module_Services|ES:M&oacute;dulo_Servicios';

		if (isset($_GET['type'])) $title = $langs->trans('CardProduct'.GETPOST('type'));
		else $title = $langs->trans('ProductServiceCard');

		llxHeader('', $title, $helpurl);

		$form = new Form($db);
		$formproduct = new FormProduct($db);


		if (is_object($objcanvas) && $objcanvas->displayCanvasExists($action))
		{
	// -----------------------------------------
	// When used with CANVAS
	// -----------------------------------------
			if (empty($object->error) && $id)
			{
				$object = new Product($db);
				$result=$object->fetch($id);
				if ($result <= 0) dol_print_error('',$object->error);
			}
	$objcanvas->assign_values($action, $object->id, $object->ref);	// Set value for templates
	$objcanvas->display_canvas($action);							// Show template
}
else
{
	/*
	 * Product card
	 */

	if ($object->id > 0)
	{
		$head=product_prepare_head($object, $user);
		$titre=$langs->trans("CardProduct".$object->type);
		$picto=($object->type==1?'service':'product');
		dol_fiche_head($head, 'bonus', $titre, 0, $picto);

		$showphoto=$object->is_photo_available($conf->product->multidir_output[$object->entity]);
		$showbarcode=empty($conf->barcode->enabled)?0:1;
		if (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && empty($user->rights->barcode->lire_advance)) $showbarcode=0;

		// Fiche en mode edition
		if ($action == 'edit' && ($user->rights->produit->creer || $user->rights->service->creer))
		{
			$type = $langs->trans('Product');
			if ($object->isservice()) $type = $langs->trans('Service');
			print_fiche_titre($langs->trans('Modify').' '.$type.' : '.(is_object($object->oldcopy)?$object->oldcopy->ref:$object->ref), "");

			// Main official, simple, and not duplicated code
			print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">'."\n";
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="update">';
			print '<input type="hidden" name="id" value="'.$object->id.'">';
			print '<input type="hidden" name="canvas" value="'.$object->canvas.'">';
			print '<table class="border allwidth">';
		}

			// En mode visu
		print '<table class="border" width="100%"><tr>';

			// Ref
		print '<td width="15%">'.$langs->trans("Ref").'</td><td colspan="'.(2+(($showphoto||$showbarcode)?1:0)).'">';
		print $form->showrefnav($object,'ref','',1,'ref');
		print '</td>';

		print '</tr>';

			// Label
		print '<tr><td>'.$langs->trans("Label").'</td><td colspan="2">'.$object->libelle.'</td>';

		$nblignes=7;
		if (! empty($conf->produit->enabled) && ! empty($conf->service->enabled)) $nblignes++;
		if ($showbarcode) $nblignes+=2;
		if ($object->type!=1) $nblignes++;
		if (empty($conf->global->PRODUCT_DISABLE_CUSTOM_INFO)) $nblignes+=2;
		if ($object->isservice()) $nblignes++;
		else $nblignes+=4;

			// Photo
		if ($showphoto || $showbarcode)
		{
			print '<td valign="middle" align="center" width="25%" rowspan="'.$nblignes.'">';
			if ($showphoto)   print $object->show_photos($conf->product->multidir_output[$object->entity],1,1,0,0,0,80);
			if ($showphoto && $showbarcode) print '<br><br>';
			if ($showbarcode) print $form->showbarcode($object);
			print '</td>';
		}

		print '</tr>';

			// Type
		if (! empty($conf->produit->enabled) && ! empty($conf->service->enabled))
		{
				// TODO change for compatibility with edit in place
			$typeformat='select;0:'.$langs->trans("Product").',1:'.$langs->trans("Service");
			print '<tr><td>'.$form->editfieldkey("Type",'fk_product_type',$object->type,$object,$user->rights->produit->creer||$user->rights->service->creer,$typeformat).'</td><td colspan="2">';
			print $form->editfieldval("Type",'fk_product_type',$object->type,$object,$user->rights->produit->creer||$user->rights->service->creer,$typeformat);
			print '</td></tr>';
		}




			// Status (to sell)
		print '<tr><td>'.$langs->trans("Status").' ('.$langs->trans("Sell").')</td><td colspan="2">';
		print $object->getLibStatut(2,0);
		print '</td></tr>';

			// Status (to buy)
		print '<tr><td>'.$langs->trans("Status").' ('.$langs->trans("Buy").')</td><td colspan="2">';
		print $object->getLibStatut(2,1);
		print '</td></tr>';

			// Description
		print '<tr><td valign="top">'.$langs->trans("Description").'</td><td colspan="2">'.(dol_textishtml($object->description)?$object->description:dol_nl2br($object->description,1,true)).'</td></tr>';
		print '</table>';
		dol_fiche_end();

	}
	else if ($action != 'create')
	{
		header("Location: index.php");
		exit;
	}
}


// Define confirmation messages
/*
$formquestionclone=array(
	'text' => $langs->trans("ConfirmClone"),
	array('type' => 'text', 'name' => 'clone_ref','label' => $langs->trans("NewRefForClone"), 'value' => $langs->trans("CopyOf").' '.$object->ref, 'size'=>24),
	array('type' => 'checkbox', 'name' => 'clone_content','label' => $langs->trans("CloneContentProduct"), 'value' => 1),
	array('type' => 'checkbox', 'name' => 'clone_prices', 'label' => $langs->trans("ClonePricesProduct").' ('.$langs->trans("FeatureNotYetAvailable").')', 'value' => 0, 'disabled' => true),
	array('type' => 'checkbox', 'name' => 'clone_composition', 'label' => $langs->trans('CloneCompositionProduct'), 'value' => 1)
);

// Confirm delete product
if (($action == 'delete' && (empty($conf->use_javascript_ajax) || ! empty($conf->dol_use_jmobile)))
// Output when action = clone if jmobile or no js
	|| (! empty($conf->use_javascript_ajax) && empty($conf->dol_use_jmobile)))
	// Always output when not jmobile nor js
{
	print $form->formconfirm("fiche.php?id=".$object->id,$langs->trans("DeleteProduct"),$langs->trans("ConfirmDeleteProduct"),"confirm_delete",'',0,"action-delete");
}

// Clone confirmation
if (($action == 'clone' && (empty($conf->use_javascript_ajax) || ! empty($conf->dol_use_jmobile)))
// Output when action = clone if jmobile or no js
	|| (! empty($conf->use_javascript_ajax) && empty($conf->dol_use_jmobile)))
	// Always output when not jmobile nor js
{
	print $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id,$langs->trans('CloneProduct'),$langs->trans('ConfirmCloneProduct',$object->ref),'confirm_clone',$formquestionclone,'yes','action-clone',250,600);
}
*/


/* ************************************************************************** */
/*                                                                            */
/* Barre d'action                                                             */
/*                                                                            */
/* ************************************************************************** */

print "\n".'<div class="tabsAction">'."\n";

$parameters=array();
$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
if (empty($reshook))
{
	if ($action == '' || $action == 'view')
	{
		if ($user->rights->productext->prod->crear)
		{
			//print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=edit&amp;id='.$object->id.'">'.$langs->trans("Modify").'</a></div>';
		}
	}
}

print "\n</div><br>\n";

	$head = bonus_prepare_head($objProductbonus);
	$titre=$langs->trans('Productbonus');
	dol_fiche_head($head, 'carddet', $titre, 0, 'bonus');
		//incluimos la lista de promociones para el producto
//if (!empty($idr)  && (empty($action) || $action == 'view' || $action == 'list'))
//{
require_once DOL_DOCUMENT_ROOT.'/productext/bonus/tpl/bonus_list.tpl.php';
//}
//else
//{
//	require_once DOL_DOCUMENT_ROOT.'/productext/bonus/tpl/bonus_card.tpl.php';
//}
dol_fiche_end();



llxFooter();
$db->close();
