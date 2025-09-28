<?php
/* Copyright (C) 2002-2005	Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2004-2013	Laurent Destailleur 	<eldy@users.sourceforge.net>
 * Copyright (C) 2004		Christophe Combelles	<ccomb@free.fr>
 * Copyright (C) 2005		Marc Barilley			<marc@ocebo.fr>
 * Copyright (C) 2005-2013	Regis Houssin			<regis.houssin@capnetworks.com>
 * Copyright (C) 2010-2014	Juanjo Menent			<jmenent@2byte.es>
 * Copyright (C) 2013		Philippe Grand			<philippe.grand@atoo-net.com>
 * Copyright (C) 2013       Florian Henry		  	<florian.henry@open-concept.pro>
 * Copyright (C) 2014       Marcos García           <marcosgdf@gmail.com>
 * Copyright (C) 2016       Ramiro Queso            <ramiro@gmail.com>
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
 *	\file       htdocs/fourn/facture/card.php
 *	\ingroup    facture, fournisseur
 *	\brief      Page for supplier invoice card (view, edit, validate)
 */

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/modules/supplier_invoice/modules_facturefournisseur.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/paiementfourn.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/fourn.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
if (!empty($conf->produit->enabled))
	require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
if (!empty($conf->projet->enabled)) {
	require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';
}
if (!empty($conf->purchase->enabled)) {
	require_once DOL_DOCUMENT_ROOT.'/purchase/lib/purchase.lib.php';
}
if (!empty($conf->fiscal->enabled)) {
	require_once DOL_DOCUMENT_ROOT.'/fiscal/class/cfiscal.class.php';
}


$langs->load('bills');
$langs->load('compta');
$langs->load('suppliers');
$langs->load('companies');
$langs->load('products');
$langs->load('banks');

$id			= (GETPOST('facid','int') ? GETPOST('facid','int') : GETPOST('id','int'));
$action		= GETPOST("action");
$confirm	= GETPOST("confirm");
$ref		= GETPOST('ref','alpha');

//PDF
$hidedetails = (GETPOST('hidedetails','int') ? GETPOST('hidedetails','int') : (! empty($conf->global->MAIN_GENERATE_DOCUMENTS_HIDE_DETAILS) ? 1 : 0));
$hidedesc 	 = (GETPOST('hidedesc','int') ? GETPOST('hidedesc','int') : (! empty($conf->global->MAIN_GENERATE_DOCUMENTS_HIDE_DESC) ?  1 : 0));
$hideref 	 = (GETPOST('hideref','int') ? GETPOST('hideref','int') : (! empty($conf->global->MAIN_GENERATE_DOCUMENTS_HIDE_REF) ? 1 : 0));

// Security check
$socid='';
if (! empty($user->societe_id)) $socid=$user->societe_id;
$result = restrictedArea($user, 'fournisseur', $id, 'facture_fourn', 'facture');

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('invoicesuppliercard','globalcard'));

$object=new FactureFournisseur($db);
$extrafields = new ExtraFields($db);
$objcfiscal = new Cfiscal($db);

// fetch optionals attributes and labels
$extralabels=$extrafields->fetch_name_optionals_label($object->table_element);

// Load object
if ($id > 0 || ! empty($ref))
{
	$ret=$object->fetch($id, $ref);
}

$permissionnote=$user->rights->fournisseur->facture->creer;	// Used by the include of actions_setnotes.inc.php


/*
 * Actions
 */

$parameters=array('socid'=>$socid);
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

include DOL_DOCUMENT_ROOT.'/core/actions_setnotes.inc.php';	// Must be include, not includ_once

// Action clone object
if ($action == 'addcfiscal')
{
	$objcfiscal->fk_facture_fourn = $id;
	$objcfiscal->nfiscal = GETPOST('nfiscal');
	$objcfiscal->nit = GETPOST('nit');
	$objcfiscal->razsoc = GETPOST('razsoc');
	$objcfiscal->date_exp = $date_exp;
	$objcfiscal->num_autoriz = GETPOST('num_autoriz');
	$objcfiscal->cod_control = GETPOST('cod_control');
	$objcfiscal->baseimp1 = GETPOST('baseimp1');
	$objcfiscal->aliqimp1 = GETPOST('aliqimp1');
	$objcfiscal->valimp1 = GETPOST('valimp1');
	$objcfiscal->basenoimp1 = GETPOST('basenoimp1');
	$objcfiscal->valret1 = GETPOST('valret1');
	$objcfiscal->date_create = dol_now();
	$objcfiscal->fk_user_create = $user->id;
	$objcfiscal->status = 1;

	$result=$objcfiscal->create($id);
	if ($result > 0)
	{
		header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
		exit;
	}
	else
	{
		$langs->load("errors");
		setEventMessage($langs->trans($object->error), 'errors');
		$action='';
	}
//    }
}
elseif ($action == 'confirm_valid' && $confirm == 'yes' && $user->rights->fournisseur->facture->valider)
{
	$idwarehouse=GETPOST('idwarehouse');

	$object->fetch($id);
	$object->fetch_thirdparty();

	$qualified_for_stock_change=0;
	if (empty($conf->global->STOCK_SUPPORTS_SERVICES))
	{
		$qualified_for_stock_change=$object->hasProductsOrServices(2);
	}
	else
	{
		$qualified_for_stock_change=$object->hasProductsOrServices(1);
	}

    // Check parameters
	if (! empty($conf->stock->enabled) && ! empty($conf->global->STOCK_CALCULATE_ON_SUPPLIER_BILL) && $qualified_for_stock_change)
	{
		$langs->load("stocks");
		if (! $idwarehouse || $idwarehouse == -1)
		{
			$error++;
			setEventMessage($langs->trans('ErrorFieldRequired',$langs->transnoentitiesnoconv("Warehouse")), 'errors');
			$action='';
		}
	}

	if (! $error)
	{
		$result = $object->validate($user,'',$idwarehouse);
		if ($result < 0)
		{
			setEventMessages($object->error,$object->errors,'errors');
		}
	}
}

elseif ($action == 'confirm_delete' && $confirm == 'yes' && $user->rights->fournisseur->facture->supprimer)
{
	$object->fetch($id);
	$object->fetch_thirdparty();
	$result=$object->delete($id);
	if ($result > 0)
	{
		header('Location: list.php');
		exit;
	}
	else
	{
		setEventMessage($object->error, 'errors');
	}
}

elseif ($action == 'confirm_delete_line' && $confirm == 'yes' && $user->rights->fournisseur->facture->creer)
{
	$object->fetch($id);
	$ret = $object->deleteline(GETPOST('lineid'));
	if ($ret > 0)
	{
		header('Location: '.$_SERVER["PHP_SELF"].'?id='.$id);
		exit;
	}
	else
	{
		setEventMessage($object->error, 'errors');
		/* Fix bug 1485 : Reset action to avoid asking again confirmation on failure */
		$action='';
	}
}

elseif ($action == 'confirm_paid' && $confirm == 'yes' && $user->rights->fournisseur->facture->creer)
{
	$object->fetch($id);
	$result=$object->set_paid($user);
	if ($result<0)
	{
		setEventMessage($object->error,'errors');
	}
}

// Set supplier ref
if ($action == 'setref_supplier' && $user->rights->fournisseur->commande->creer)
{
	$result=$object->setValueFrom('ref_supplier',GETPOST('ref_supplier','alpha'));
	if ($result < 0) dol_print_error($db, $object->error);
}

// conditions de reglement
if ($action == 'setconditions' && $user->rights->fournisseur->commande->creer)
{
	$result=$object->setPaymentTerms(GETPOST('cond_reglement_id','int'));
}

// mode de reglement
else if ($action == 'setmode' && $user->rights->fournisseur->commande->creer)
{
	$result = $object->setPaymentMethods(GETPOST('mode_reglement_id','int'));
}

// bank account
else if ($action == 'setbankaccount' && $user->rights->fournisseur->facture->creer) {
	$result=$object->setBankAccount(GETPOST('fk_account', 'int'));
}

// Set label
elseif ($action == 'setlabel' && $user->rights->fournisseur->facture->creer)
{
	$object->fetch($id);
	$object->label=$_POST['label'];
	$result=$object->update($user);
	if ($result < 0) dol_print_error($db);
}
elseif ($action == 'setdatef' && $user->rights->fournisseur->facture->creer)
{
	$object->fetch($id);
	$object->date=dol_mktime(12,0,0,$_POST['datefmonth'],$_POST['datefday'],$_POST['datefyear']);
	if ($object->date_echeance && $object->date_echeance < $object->date) $object->date_echeance=$object->date;
	$result=$object->update($user);
	if ($result < 0) dol_print_error($db,$object->error);
}
elseif ($action == 'setdate_lim_reglement' && $user->rights->fournisseur->facture->creer)
{
	$object->fetch($id);
	$object->date_echeance=dol_mktime(12,0,0,$_POST['date_lim_reglementmonth'],$_POST['date_lim_reglementday'],$_POST['date_lim_reglementyear']);
	if (! empty($object->date_echeance) && $object->date_echeance < $object->date)
	{
		$object->date_echeance=$object->date;
		setEventMessage($langs->trans("DatePaymentTermCantBeLowerThanObjectDate"),'warnings');
	}
	$result=$object->update($user);
	if ($result < 0) dol_print_error($db,$object->error);
}

// Delete payment
elseif ($action == 'deletepaiement' && $user->rights->fournisseur->facture->creer)
{
	$object->fetch($id);
	if ($object->statut == 1 && $object->paye == 0)
	{
		$paiementfourn = new PaiementFourn($db);
		$result=$paiementfourn->fetch(GETPOST('paiement_id'));
        if ($result > 0) $result=$paiementfourn->delete(); // If fetch ok and found
        if ($result < 0) {
        	setEventMessage($paiementfourn->error, 'errors');
        }
    }
}

// Create
elseif ($action == 'add' && $user->rights->fournisseur->facture->creer)
{
	$error=0;

	$datefacture=dol_mktime(12,0,0,$_POST['remonth'],$_POST['reday'],$_POST['reyear']);
	$datedue=dol_mktime(12,0,0,$_POST['echmonth'],$_POST['echday'],$_POST['echyear']);

	if (GETPOST('socid','int')<1)
	{
		setEventMessage($langs->trans('ErrorFieldRequired',$langs->transnoentities('Supplier')), 'errors');
		$action='create';
		$error++;
	}

	if ($datefacture == '')
	{
		setEventMessage($langs->trans('ErrorFieldRequired',$langs->transnoentities('DateInvoice')), 'errors');
		$action='create';
		$_GET['socid']=$_POST['socid'];
		$error++;
	}
	if (! GETPOST('ref_supplier'))
	{
		setEventMessage($langs->trans('ErrorFieldRequired',$langs->transnoentities('RefSupplier')), 'errors');
		$action='create';
		$_GET['socid']=$_POST['socid'];
		$error++;
	}

    // Fill array 'array_options' with data from add form

	if (! $error)
	{
		$db->begin();

		$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);
		$ret = $extrafields->setOptionalsFromPost($extralabels, $object);
		if ($ret < 0) $error++;

		$tmpproject = GETPOST('projectid', 'int');

        // Creation facture
		$object->ref           = $_POST['ref'];
		$object->ref_supplier  = $_POST['ref_supplier'];
		$object->socid         = $_POST['socid'];
		$object->libelle       = $_POST['libelle'];
		$object->date          = $datefacture;
		$object->date_echeance = $datedue;
		$object->note_public   = GETPOST('note_public');
		$object->note_private  = GETPOST('note_private');
		$object->cond_reglement_id = GETPOST('cond_reglement_id');
		$object->mode_reglement_id = GETPOST('mode_reglement_id');
		$object->fk_account        = GETPOST('fk_account', 'int');
		$object->fk_project    = ($tmpproject > 0) ? $tmpproject : null;

		// Auto calculation of date due if not filled by user
		if(empty($object->date_echeance)) $object->date_echeance = $object->calculate_date_lim_reglement();

        // If creation from another object of another module
		if ($_POST['origin'] && $_POST['originid'])
		{
            // Parse element/subelement (ex: project_task)
			$element = $subelement = $_POST['origin'];
            /*if (preg_match('/^([^_]+)_([^_]+)/i',$_POST['origin'],$regs))
             {
            $element = $regs[1];
            $subelement = $regs[2];
        }*/

            // For compatibility
        if ($element == 'order')    {
        	$element = $subelement = 'commande';
        }
        if ($element == 'propal')   {
        	$element = 'comm/propal'; $subelement = 'propal';
        }
        if ($element == 'contract') {
        	$element = $subelement = 'contrat';
        }
        if ($element == 'order_supplier') {
        	$element = 'fourn'; $subelement = 'fournisseur.commande';
        }
        if ($element == 'project')
        {
        	$element = 'projet';
        }
        $object->origin    = $_POST['origin'];
        $object->origin_id = $_POST['originid'];

        $id = $object->create($user);

            // Add lines
        if ($id > 0)
        {
        	require_once DOL_DOCUMENT_ROOT.'/'.$element.'/class/'.$subelement.'.class.php';
        	$classname = ucfirst($subelement);
        	if ($classname == 'Fournisseur.commande') $classname='CommandeFournisseur';
        	$srcobject = new $classname($db);

        	$result=$srcobject->fetch($_POST['originid']);
        	if ($result > 0)
        	{
        		$lines = $srcobject->lines;
        		if (empty($lines) && method_exists($srcobject,'fetch_lines'))
        		{
        			$srcobject->fetch_lines();
        			$lines = $srcobject->lines;
        		}

        		$num=count($lines);
        		for ($i = 0; $i < $num; $i++)
        		{
        			$desc=($lines[$i]->desc?$lines[$i]->desc:$lines[$i]->libelle);
        			$product_type=($lines[$i]->product_type?$lines[$i]->product_type:0);

                        // Dates
                        // TODO mutualiser
        			$date_start=$lines[$i]->date_debut_prevue;
        			if ($lines[$i]->date_debut_reel) $date_start=$lines[$i]->date_debut_reel;
        			if ($lines[$i]->date_start) $date_start=$lines[$i]->date_start;
        			$date_end=$lines[$i]->date_fin_prevue;
        			if ($lines[$i]->date_fin_reel) $date_end=$lines[$i]->date_fin_reel;
        			if ($lines[$i]->date_end) $date_end=$lines[$i]->date_end;

                        // FIXME Missing $lines[$i]->ref_supplier and $lines[$i]->label into addline and updateline methods. They are filled when coming from order for example.
        			$result = $object->addline(
        				$desc,
        				$lines[$i]->subprice,
        				$lines[$i]->tva_tx,
        				$lines[$i]->localtax1_tx,
        				$lines[$i]->localtax2_tx,
        				$lines[$i]->qty,
        				$lines[$i]->fk_product,
        				$lines[$i]->remise_percent,
        				$date_start,
        				$date_end,
        				0,
        				$lines[$i]->info_bits,
        				'HT',
        				$product_type
        				);

        			if ($result < 0)
        			{
        				$error++;
        				break;
        			}
        		}
        	}
        	else
        	{
        		$error++;
        	}
        }
        else
        {
        	$error++;
        }
    }
        // If some invoice's lines already known
    else
    {
    	$id = $object->create($user);
    	if ($id < 0)
    	{
    		$error++;
    	}

    	if (! $error)
    	{
    		for ($i = 1 ; $i < 9 ; $i++)
    		{
    			$label = $_POST['label'.$i];
    			$amountht  = price2num($_POST['amount'.$i]);
    			$amountttc = price2num($_POST['amountttc'.$i]);
    			$tauxtva   = price2num($_POST['tauxtva'.$i]);
    			$qty = $_POST['qty'.$i];
    			$fk_product = $_POST['fk_product'.$i];
    			if ($label)
    			{
    				if ($amountht)
    				{
    					$price_base='HT'; $amount=$amountht;
    				}
    				else
    				{
    					$price_base='TTC'; $amount=$amountttc;
    				}
    				$atleastoneline=1;

    				$product=new Product($db);
    				$product->fetch($_POST['idprod'.$i]);

    				$ret=$object->addline($label, $amount, $tauxtva, $product->localtax1_tx, $product->localtax2_tx, $qty, $fk_product, $remise_percent, '', '', '', 0, $price_base);
    				if ($ret < 0) $error++;
    			}
    		}
    	}
    }

    if ($error)
    {
    	$langs->load("errors");
    	$db->rollback();
    	setEventMessage($langs->trans($object->error), 'errors');
    	$action='create';
    	$_GET['socid']=$_POST['socid'];
    }
    else
    {
    	$db->commit();

    	if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE)) {
    		$outputlangs = $langs;
    		$result = $object->generateDocument($object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
    		if ($result	<= 0)
    		{
    			dol_print_error($db,$result);
    			exit;
    		}
    	}

    	header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
    	exit;
    }
}
}

// Edit line
elseif ($action == 'update_line' && $user->rights->fournisseur->facture->creer)
{
	// TODO Missing transaction
    if (GETPOST('etat') == '1' && ! GETPOST('cancel')) // si on valide la modification
    {
    	$object->fetch($id);
    	$object->fetch_thirdparty();

    	if ($_POST['puht'])
    	{
    		$pu=$_POST['puht'];
    		$price_base_type='HT';
    	}
    	if ($_POST['puttc'])
    	{
    		$pu=$_POST['puttc'];
    		$price_base_type='TTC';
    	}

    	if (GETPOST('idprod'))
    	{
    		$prod = new Product($db);
    		$prod->fetch($_POST['idprod']);
    		$label = $prod->description;
    		if (trim($_POST['desc']) != trim($label)) $label=$_POST['desc'];

    		$type = $prod->type;
    	}
    	else
    	{

    		$label = $_POST['desc'];
    		$type = $_POST["type"]?$_POST["type"]:0;

    	}

    	$localtax1_tx= get_localtax($_POST['tauxtva'], 1, $mysoc,$object->thirdparty);
    	$localtax2_tx= get_localtax($_POST['tauxtva'], 2, $mysoc,$object->thirdparty);
    	$remise_percent=GETPOST('remise_percent');

    	$result=$object->updateline(GETPOST('lineid'), $label, $pu, GETPOST('tauxtva'), $localtax1_tx, $localtax2_tx, GETPOST('qty'), GETPOST('idprod'), $price_base_type, 0, $type, $remise_percent);
    	if ($result >= 0)
    	{
    		unset($_POST['label']);
    	}
    	else
    	{
    		setEventMessage($object->error,'errors');
    	}
    }
}

elseif ($action == 'addline' && $user->rights->fournisseur->facture->creer)
{
	$db->begin();

	$ret=$object->fetch($id);
	if ($ret < 0)
	{
		dol_print_error($db,$object->error);
		exit;
	}
	$ret=$object->fetch_thirdparty();

	$langs->load('errors');
	$error=0;

	// Set if we used free entry or predefined product
	$predef='';
	$product_desc=(GETPOST('dp_desc')?GETPOST('dp_desc'):'');
	if (GETPOST('prod_entry_mode') == 'free')
	{
		$idprod=0;
		$price_ht = GETPOST('price_ht');
		$tva_tx = (GETPOST('tva_tx') ? GETPOST('tva_tx') : 0);
	}
	else
	{
		$idprod=GETPOST('idprod', 'int');
		$price_ht = '';
		$tva_tx = '';
	}

	$qty = GETPOST('qty'.$predef);
	$remise_percent=GETPOST('remise_percent'.$predef);

	if (GETPOST('prod_entry_mode')=='free' && GETPOST('price_ht') < 0 && $qty < 0)
	{
		setEventMessage($langs->trans('ErrorBothFieldCantBeNegative', $langs->transnoentitiesnoconv('UnitPrice'), $langs->transnoentitiesnoconv('Qty')), 'errors');
		$error++;
	}
	if (GETPOST('prod_entry_mode')=='free'  && ! GETPOST('idprodfournprice') && GETPOST('type') < 0)
	{
		setEventMessage($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Type')), 'errors');
		$error++;
	}
    if (GETPOST('prod_entry_mode')=='free' && GETPOST('price_ht')==='' && GETPOST('price_ttc')==='') // Unit price can be 0 but not ''
    {
    	setEventMessage($langs->trans($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('UnitPrice'))), 'errors');
    	$error++;
    }
    if (GETPOST('prod_entry_mode')=='free' && ! GETPOST('dp_desc'))
    {
    	setEventMessage($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Description')), 'errors');
    	$error++;
    }
    if (! GETPOST('qty'))
    {
    	setEventMessage($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Qty')), 'errors');
    	$error++;
    }

    if (GETPOST('prod_entry_mode') != 'free')	// With combolist mode idprodfournprice is > 0 or -1. With autocomplete, idprodfournprice is > 0 or ''
    {
    	$idprod=0;
    	$productsupplier=new ProductFournisseur($db);

        if (GETPOST('idprodfournprice') == -1 || GETPOST('idprodfournprice') == '') $idprod=-2;	// Same behaviour than with combolist. When not select idprodfournprice is now -2 (to avoid conflict with next action that may return -1)

        if (GETPOST('idprodfournprice') > 0)
        {
    		$idprod=$productsupplier->get_buyprice(GETPOST('idprodfournprice'), $qty);    // Just to see if a price exists for the quantity. Not used to found vat.
    	}

    	if ($idprod > 0)
    	{
    		$result=$productsupplier->fetch($idprod);

    		$label = $productsupplier->libelle;

    		$desc = $productsupplier->description;
    		if (trim($product_desc) != trim($desc)) $desc = dol_concatdesc($desc, $product_desc);

    		$tvatx=get_default_tva($object->thirdparty, $mysoc, $productsupplier->id, $_POST['idprodfournprice']);
    		$npr = get_default_npr($object->thirdparty, $mysoc, $productsupplier->id, $_POST['idprodfournprice']);

    		$localtax1_tx= get_localtax($tvatx, 1, $mysoc,$object->thirdparty);
    		$localtax2_tx= get_localtax($tvatx, 2, $mysoc,$object->thirdparty);

    		$type = $productsupplier->type;

            // TODO Save the product supplier ref into database into field ref_supplier (must rename field ref into ref_supplier first)
    		$result=$object->addline($desc, $productsupplier->fourn_pu, $tvatx, $localtax1_tx, $localtax2_tx, $qty, $idprod, $remise_percent, '', '', 0, $npr);
    	}
    	if ($idprod == -2 || $idprod == 0)
    	{
            // Product not selected
    		$error++;
    		$langs->load("errors");
    		setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("ProductOrService")), 'errors');
    	}
    	if ($idprod == -1)
    	{
            // Quantity too low
    		$error++;
    		$langs->load("errors");
    		setEventMessage($langs->trans("ErrorQtyTooLowForThisSupplier"), 'errors');
    	}
    }
    else if( GETPOST('price_ht')!=='' || GETPOST('price_ttc')!=='' )
    {
    	$pu_ht = price2num($price_ht, 'MU');
    	$pu_ttc = price2num(GETPOST('price_ttc'), 'MU');
    	$tva_npr = (preg_match('/\*/', $tva_tx) ? 1 : 0);
    	$tva_tx = str_replace('*', '', $tva_tx);
    	$label = (GETPOST('product_label') ? GETPOST('product_label') : '');
    	$desc = $product_desc;
    	$type = GETPOST('type');

    	$tva_tx = price2num($tva_tx);	// When vat is text input field

    	// Local Taxes
    	$localtax1_tx= get_localtax($tva_tx, 1,$mysoc,$object->thirdparty);
    	$localtax2_tx= get_localtax($tva_tx, 2,$mysoc,$object->thirdparty);

    	if (!empty($_POST['price_ht']))
    	{
    		$ht = price2num($_POST['price_ht']);
    		$price_base_type = 'HT';

            //print $product_desc, $pu, $txtva, $qty, $fk_product=0, $remise_percent=0, $date_start='', $date_end='', $ventil=0, $info_bits='', $price_base_type='HT', $type=0
    		$result=$object->addline($product_desc, $ht, $tva_tx, $localtax1_tx, $localtax2_tx, $qty, 0, $remise_percent, $datestart, $dateend, 0, $npr, $price_base_type, $type);
    	}
    	else
    	{
    		$ttc = price2num($_POST['price_ttc']);
    		$ht = $ttc / (1 + ($tva_tx / 100));
    		$price_base_type = 'HT';
            //print $product_desc, $pu, $txtva, $qty, $fk_product=0, $remise_percent=0, $date_start='', $date_end='', $ventil=0, $info_bits='', $price_base_type='HT', $type=0
    		$result=$object->addline($product_desc, $ht, $tva_tx,$localtax1_tx, $localtax2_tx, $qty, 0, $remise_percent, $datestart, $dateend, 0, $npr, $price_base_type, $type);
    	}
    }

    //print "xx".$tva_tx; exit;
    if (! $error && $result > 0)
    {
    	$db->commit();

        // Define output language
    	if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
    	{
    		$outputlangs = $langs;
    		$newlang = '';
    		if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
    		if ($conf->global->MAIN_MULTILANGS && empty($newlang))	$newlang = $object->thirdparty->default_lang;
    		if (! empty($newlang)) {
    			$outputlangs = new Translate("", $conf);
    			$outputlangs->setDefaultLang($newlang);
    		}
    		$model=$object->modelpdf;
    		$ret = $object->fetch($id); // Reload to get new records

    		$result=$object->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);
    		if ($result < 0) dol_print_error($db,$result);
    	}

    	unset($_POST ['prod_entry_mode']);

    	unset($_POST['qty']);
    	unset($_POST['type']);
    	unset($_POST['remise_percent']);
    	unset($_POST['pu']);
    	unset($_POST['price_ht']);
    	unset($_POST['price_ttc']);
    	unset($_POST['tva_tx']);
    	unset($_POST['label']);
    	unset($localtax1_tx);
    	unset($localtax2_tx);
    	unset($_POST['np_marginRate']);
    	unset($_POST['np_markRate']);
    	unset($_POST['dp_desc']);
    	unset($_POST['idprodfournprice']);

    	unset($_POST['date_starthour']);
    	unset($_POST['date_startmin']);
    	unset($_POST['date_startsec']);
    	unset($_POST['date_startday']);
    	unset($_POST['date_startmonth']);
    	unset($_POST['date_startyear']);
    	unset($_POST['date_endhour']);
    	unset($_POST['date_endmin']);
    	unset($_POST['date_endsec']);
    	unset($_POST['date_endday']);
    	unset($_POST['date_endmonth']);
    	unset($_POST['date_endyear']);
    }
    else
    {
    	$db->rollback();
    	setEventMessage($object->error, 'errors');
    }

    $action = '';
}

elseif ($action == 'classin')
{
	$object->fetch($id);
	$result=$object->setProject($_POST['projectid']);
}


// Set invoice to draft status
elseif ($action == 'edit' && $user->rights->fournisseur->facture->creer)
{
	$object->fetch($id);

	$totalpaye = $object->getSommePaiement();
	$resteapayer = $object->total_ttc - $totalpaye;

    // On verifie si les lignes de factures ont ete exportees en compta et/ou ventilees
    //$ventilExportCompta = $object->getVentilExportCompta();

    // On verifie si aucun paiement n'a ete effectue
	if ($resteapayer == $object->total_ttc	&& $object->paye == 0 && $ventilExportCompta == 0)
	{
		$object->set_draft($user);

        // Define output language
		if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
		{
			$outputlangs = $langs;
			$newlang = '';
			if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
			if ($conf->global->MAIN_MULTILANGS && empty($newlang))	$newlang = $object->thirdparty->default_lang;
			if (! empty($newlang)) {
				$outputlangs = new Translate("", $conf);
				$outputlangs->setDefaultLang($newlang);
			}
			$model=$object->modelpdf;
    		$ret = $object->fetch($id); // Reload to get new records

    		$result=$object->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);
    		if ($result < 0) dol_print_error($db,$result);
    	}

    	$action='';
    }
}

// Set invoice to validated/unpaid status
elseif ($action == 'reopen' && $user->rights->fournisseur->facture->creer)
{
	$result = $object->fetch($id);
	if ($object->statut == 2
		|| ($object->statut == 3 && $object->close_code != 'replaced'))
	{
		$result = $object->set_unpaid($user);
		if ($result > 0)
		{
			header('Location: '.$_SERVER["PHP_SELF"].'?id='.$id);
			exit;
		}
		else
		{
			setEventMessage($object->error, 'errors');
		}
	}
}

// Link invoice to order
if (GETPOST('linkedOrder')) {
	$object->fetch($id);
	$object->fetch_thirdparty();
	$result = $object->add_object_linked('order_supplier', GETPOST('linkedOrder'));
}

// Add file in email form
if (GETPOST('addfile'))
{
	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

    // Set tmp user directory TODO Use a dedicated directory for temp mails files
	$vardir=$conf->user->dir_output."/".$user->id;
	$upload_dir_tmp = $vardir.'/temp';

	dol_add_file_process($upload_dir_tmp,0,0);
	$action='presend';
}

// Remove file in email form
if (! empty($_POST['removedfile']))
{
	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

    // Set tmp user directory
	$vardir=$conf->user->dir_output."/".$user->id;
	$upload_dir_tmp = $vardir.'/temp';

	// TODO Delete only files that was uploaded from email form
	dol_remove_file_process($_POST['removedfile'],0);
	$action='presend';
}

// Send mail
if ($action == 'send' && ! $_POST['addfile'] && ! $_POST['removedfile'] && ! $_POST['cancel'])
{
	$langs->load('mails');

	$object->fetch($id);
	$result=$object->fetch_thirdparty();
	if ($result > 0)
	{
//        $ref = dol_sanitizeFileName($object->ref);
//        $file = $conf->fournisseur->facture->dir_output.'/'.get_exdir($object->id,2).$ref.'/'.$ref.'.pdf';

//        if (is_readable($file))
//        {
		if ($_POST['sendto'])
		{
                // Le destinataire a ete fourni via le champ libre
			$sendto = $_POST['sendto'];
			$sendtoid = 0;
		}
		elseif ($_POST['receiver'] != '-1')
		{
                // Recipient was provided from combo list
                if ($_POST['receiver'] == 'thirdparty') // Id of third party
                {
                	$sendto = $object->client->email;
                	$sendtoid = 0;
                }
                else	// Id du contact
                {
                	$sendto = $object->client->contact_get_property($_POST['receiver'],'email');
                	$sendtoid = $_POST['receiver'];
                }
            }

            if (dol_strlen($sendto))
            {
            	$langs->load("commercial");

            	$from = $_POST['fromname'] . ' <' . $_POST['frommail'] .'>';
            	$replyto = $_POST['replytoname']. ' <' . $_POST['replytomail'].'>';
            	$message = $_POST['message'];
            	$sendtocc = $_POST['sendtocc'];
            	$deliveryreceipt = $_POST['deliveryreceipt'];

            	if ($action == 'send')
            	{
            		if (dol_strlen($_POST['subject'])) $subject=$_POST['subject'];
            		else $subject = $langs->transnoentities('CustomerOrder').' '.$object->ref;
            		$actiontypecode='AC_SUP_INV';
            		$actionmsg = $langs->transnoentities('MailSentBy').' '.$from.' '.$langs->transnoentities('To').' '.$sendto;
            		if ($message)
            		{
            			if ($sendtocc) $actionmsg = dol_concatdesc($actionmsg, $langs->transnoentities('Bcc') . ": " . $sendtocc);
            			$actionmsg = dol_concatdesc($actionmsg, $langs->transnoentities('MailTopic') . ": " . $subject);
            			$actionmsg = dol_concatdesc($actionmsg, $langs->transnoentities('TextUsedInTheMessageBody') . ":");
            			$actionmsg = dol_concatdesc($actionmsg, $message);
            		}
            		$actionmsg2=$langs->transnoentities('Action'.$actiontypecode);
            	}

                // Create form object
            	include_once DOL_DOCUMENT_ROOT.'/core/class/html.formmail.class.php';
            	$formmail = new FormMail($db);

            	$attachedfiles=$formmail->get_attached_files();
            	$filepath = $attachedfiles['paths'];
            	$filename = $attachedfiles['names'];
            	$mimetype = $attachedfiles['mimes'];

                // Send mail
            	require_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';
            	$mailfile = new CMailFile($subject,$sendto,$from,$message,$filepath,$mimetype,$filename,$sendtocc,'',$deliveryreceipt,-1);
            	if ($mailfile->error)
            	{
            		setEventMessage($mailfile->error,'errors');
            	}
            	else
            	{
            		$result=$mailfile->sendfile();
            		if ($result)
            		{
                        $mesg=$langs->trans('MailSuccessfulySent',$mailfile->getValidAddress($from,2),$mailfile->getValidAddress($sendto,2));		// Must not contain "
                        setEventMessage($mesg);

                        $error=0;

                        // Initialisation donnees
                        $object->sendtoid		= $sendtoid;
                        $object->actiontypecode	= $actiontypecode;
                        $object->actionmsg		= $actionmsg;
                        $object->actionmsg2		= $actionmsg2;
                        $object->fk_element		= $object->id;
                        $object->elementtype	= $object->element;

                        // Appel des triggers
                        include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
                        $interface=new Interfaces($db);
                        $result=$interface->run_triggers('BILL_SUPPLIER_SENTBYMAIL',$object,$user,$langs,$conf);
                        if ($result < 0) {
                        	$error++; $object->errors=$interface->errors;
                        }
                        // Fin appel triggers

                        if ($error)
                        {
                        	dol_print_error($db);
                        }
                        else
                        {
                            // Redirect here
                            // This avoid sending mail twice if going out and then back to page
                        	header('Location: '.$_SERVER["PHP_SELF"].'?id='.$object->id);
                        	exit;
                        }
                    }
                    else
                    {
                    	$langs->load("other");
                    	if ($mailfile->error)
                    	{
                    		$mesg.=$langs->trans('ErrorFailedToSendMail',$from,$sendto);
                    		$mesg.='<br>'.$mailfile->error;
                    	}
                    	else
                    	{
                    		$mesg.='No mail sent. Feature is disabled by option MAIN_DISABLE_ALL_MAILS';
                    	}
                    	setEventMessage($mesg, 'errors');
                    }
                }
            }

            else
            {
            	$langs->load("other");
            	setEventMessage($langs->trans('ErrorMailRecipientIsEmpty'), 'errors');
            	dol_syslog('Recipient email is empty');
            }
/*        }
        else
        {
            $langs->load("errors");
            $mesg='<div class="error">'.$langs->trans('ErrorCantReadFile',$file).'</div>';
            dol_syslog('Failed to read file: '.$file);
        }*/
    }
    else
    {
    	$langs->load("other");
    	setEventMessage($langs->trans('ErrorFailedToReadEntity',$langs->trans("Invoice")), 'errors');
    	dol_syslog('Unable to read data from the invoice. The invoice file has perhaps not been generated.');
    }

    //$action = 'presend';
}

// Build document
elseif ($action == 'builddoc')
{
	// Save modele used
	$object->fetch($id);
	$object->fetch_thirdparty();

	// Save last template used to generate document
	if (GETPOST('model')) $object->setDocModel($user, GETPOST('model','alpha'));

	$outputlangs = $langs;
	$newlang=GETPOST('lang_id','alpha');
	if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$object->client->default_lang;
	if (! empty($newlang))
	{
		$outputlangs = new Translate("",$conf);
		$outputlangs->setDefaultLang($newlang);
	}
	$result = $object->generateDocument($object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
	if ($result	<= 0)
	{
		dol_print_error($db,$result);
		exit;
	}
}
// Make calculation according to calculationrule
elseif ($action == 'calculate')
{
	$calculationrule=GETPOST('calculationrule');

	$object->fetch($id);
	$object->fetch_thirdparty();
	$result=$object->update_price(0, (($calculationrule=='totalofround')?'0':'1'), 0, $object->thirdparty);
	if ($result	<= 0)
	{
		dol_print_error($db,$result);
		exit;
	}
}
// Delete file in doc form
elseif ($action == 'remove_file')
{
	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

	if ($object->fetch($id))
	{
		$object->fetch_thirdparty();
		$upload_dir =	$conf->fournisseur->facture->dir_output . "/";
		$file =	$upload_dir	. '/' .	GETPOST('file');
		$ret=dol_delete_file($file,0,0,0,$object);
		if ($ret) setEventMessage($langs->trans("FileWasRemoved", GETPOST('urlfile')));
		else setEventMessage($langs->trans("ErrorFailToDeleteFile", GETPOST('urlfile')), 'errors');
	}
}

elseif ($action == 'update_extras')
{
	// Fill array 'array_options' with data from add form
	$extralabels=$extrafields->fetch_name_optionals_label($object->table_element);
	$ret = $extrafields->setOptionalsFromPost($extralabels,$object,GETPOST('attribute'));
	if ($ret < 0) $error++;

	if (!$error)
	{
		// Actions on extra fields (by external module or standard code)
		// FIXME le hook fait double emploi avec le trigger !!
		$hookmanager->initHooks(array('supplierinvoicedao'));
		$parameters=array('id'=>$object->id);

		$reshook=$hookmanager->executeHooks('insertExtraFields',$parameters,$object,$action); // Note that $action and $object may have been modified by some hooks

		if (empty($reshook))
		{
			if (empty($conf->global->MAIN_EXTRAFIELDS_DISABLED)) // For avoid conflicts if trigger used
			{

				$result=$object->insertExtraFields();

				if ($result < 0)
				{
					$error++;
				}

			}
		}
		else if ($reshook < 0) $error++;
	}
	else
	{
		$action = 'edit_extras';
	}
}

if (! empty($conf->global->MAIN_DISABLE_CONTACTS_TAB) && $user->rights->fournisseur->facture->creer)
{
	if ($action == 'addcontact')
	{
		$result = $object->fetch($id);

		if ($result > 0 && $id > 0)
		{
			$contactid = (GETPOST('userid') ? GETPOST('userid') : GETPOST('contactid'));
			$result = $object->add_contact($contactid, $_POST["type"], $_POST["source"]);
		}

		if ($result >= 0)
		{
			header("Location: ".$_SERVER['PHP_SELF']."?id=".$object->id);
			exit;
		}
		else
		{
			if ($object->error == 'DB_ERROR_RECORD_ALREADY_EXISTS')
			{
				$langs->load("errors");
				setEventMessage($langs->trans("ErrorThisContactIsAlreadyDefinedAsThisType"), 'errors');
			}
			else
			{
				setEventMessage($object->error, 'errors');
			}
		}
	}

	// bascule du statut d'un contact
	else if ($action == 'swapstatut')
	{
		if ($object->fetch($id))
		{
			$result=$object->swapContactStatus(GETPOST('ligne'));
		}
		else
		{
			dol_print_error($db);
		}
	}

	// Efface un contact
	else if ($action == 'deletecontact')
	{
		$object->fetch($id);
		$result = $object->delete_contact($_GET["lineid"]);

		if ($result >= 0)
		{
			header("Location: ".$_SERVER['PHP_SELF']."?id=".$object->id);
			exit;
		}
		else {
			dol_print_error($db);
		}
	}
}


/*
 *	View
 */

$form = new Form($db);
$formfile = new FormFile($db);
$bankaccountstatic=new Account($db);

llxHeader('','','');


if ($id > 0 || ! empty($ref))
{
	/* *************************************************************************** */
	/*                                                                             */
	/* Fiche en mode visu ou edition                                               */
	/*                                                                             */
	/* *************************************************************************** */

	$now=dol_now();

	$productstatic = new Product($db);

	$object->fetch($id,$ref);
	$result=$object->fetch_thirdparty();
	if ($result < 0) dol_print_error($db);

	$societe = new Fournisseur($db);
	$result=$societe->fetch($object->socid);
	if ($result < 0) dol_print_error($db);

        // fetch optionals attributes and labels
	$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

        /*
         *	View card
         */
        $head = purchase_invoice_prepare_head($object);
        $titre=$langs->trans('SupplierInvoice');

        dol_fiche_head($head, 'Fiscal', $titre, 0, 'bill');

        // Confirmation de la suppression d'une ligne produit
        if ($action == 'confirm_delete_line')
        {
        	$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id.'&lineid='.$_GET["lineid"], $langs->trans('DeleteProductLine'), $langs->trans('ConfirmDeleteProductLine'), 'confirm_delete_line', '', 1, 1);
        }

        // Clone confirmation
        if ($action == 'clone')
        {
            // Create an array for form
        	$formquestion=array(
            //'text' => $langs->trans("ConfirmClone"),
            //array('type' => 'checkbox', 'name' => 'clone_content',   'label' => $langs->trans("CloneMainAttributes"),   'value' => 1)
        		);
            // Paiement incomplet. On demande si motif = escompte ou autre
        	$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id,$langs->trans('CloneInvoice'),$langs->trans('ConfirmCloneInvoice',$object->ref),'confirm_clone',$formquestion,'yes', 1);
        }

        // Confirmation de la validation
        if ($action == 'valid')
        {
			 // on verifie si l'objet est en numerotation provisoire
        	$objectref = substr($object->ref, 1, 4);
        	if ($objectref == 'PROV')
        	{
        		$savdate=$object->date;
        		if (! empty($conf->global->FAC_FORCE_DATE_VALIDATION))
        		{
        			$object->date=dol_now();
                    //TODO: Possibly will have to control payment information into suppliers
                    //$object->date_lim_reglement=$object->calculate_date_lim_reglement();
        		}
        		$numref = $object->getNextNumRef($societe);
        	}
        	else
        	{
        		$numref = $object->ref;
        	}

        	$text=$langs->trans('ConfirmValidateBill',$numref);
            /*if (! empty($conf->notification->enabled))
            {
            	require_once DOL_DOCUMENT_ROOT .'/core/class/notify.class.php';
            	$notify=new Notify($db);
            	$text.='<br>';
            	$text.=$notify->confirmMessage('BILL_SUPPLIER_VALIDATE',$object->socid);
            }*/
            $formquestion=array();

            $qualified_for_stock_change=0;
            if (empty($conf->global->STOCK_SUPPORTS_SERVICES))
            {
            	$qualified_for_stock_change=$object->hasProductsOrServices(2);
            }
            else
            {
            	$qualified_for_stock_change=$object->hasProductsOrServices(1);
            }

            if (! empty($conf->stock->enabled) && ! empty($conf->global->STOCK_CALCULATE_ON_SUPPLIER_BILL) && $qualified_for_stock_change)
            {
            	$langs->load("stocks");
            	require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
            	$formproduct=new FormProduct($db);
            	$formquestion=array(
                //'text' => $langs->trans("ConfirmClone"),
                //array('type' => 'checkbox', 'name' => 'clone_content',   'label' => $langs->trans("CloneMainAttributes"),   'value' => 1),
                //array('type' => 'checkbox', 'name' => 'update_prices',   'label' => $langs->trans("PuttingPricesUpToDate"),   'value' => 1),
            		array('type' => 'other', 'name' => 'idwarehouse',   'label' => $langs->trans("SelectWarehouseForStockIncrease"),   'value' => $formproduct->selectWarehouses(GETPOST('idwarehouse'),'idwarehouse','',1)));
            }

            $formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('ValidateBill'), $text, 'confirm_valid', $formquestion, 1, 1, 240);

        }

        // Confirmation set paid
        if ($action == 'paid')
        {
        	$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('ClassifyPaid'), $langs->trans('ConfirmClassifyPaidBill', $object->ref), 'confirm_paid', '', 0, 1);

        }

        // Confirmation de la suppression de la facture fournisseur
        if ($action == 'delete')
        {
        	$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('DeleteBill'), $langs->trans('ConfirmDeleteBill'), 'confirm_delete', '', 0, 1);

        }

        if (!$formconfirm) {
        	$parameters=array('lineid'=>$lineid);
			$formconfirm=$hookmanager->executeHooks('formConfirm',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
		}

		// Print form confirm
		print $formconfirm;


        /**
         * 	Invoice
         */
        print '<table class="border" width="100%">';

        $linkback = '<a href="'.DOL_URL_ROOT.'/fourn/facture/list.php'.(! empty($socid)?'?socid='.$socid:'').'">'.$langs->trans("BackToList").'</a>';

        // Ref
        print '<tr><td class="nowrap" width="20%">'.$langs->trans("Ref").'</td><td colspan="4">';
        print $form->showrefnav($object, 'ref', $linkback, 1, 'ref', 'ref');
        print '</td>';
        print "</tr>\n";

        // Ref supplier
        print '<tr><td>'.$form->editfieldkey("RefSupplier",'ref_supplier',$object->ref_supplier,$object,($object->statut<2 && $user->rights->fournisseur->facture->creer)).'</td><td colspan="4">';
        print $form->editfieldval("RefSupplier",'ref_supplier',$object->ref_supplier,$object,($object->statut<2 && $user->rights->fournisseur->facture->creer));
        print '</td></tr>';

        // Third party
        print '<tr><td>'.$langs->trans('Supplier').'</td><td colspan="4">'.$societe->getNomUrl(1,'supplier');
        print ' &nbsp; (<a href="'.DOL_URL_ROOT.'/fourn/facture/list.php?socid='.$object->socid.'">'.$langs->trans('OtherBills').'</a>)</td>';
        print '</tr>';

        // Type
        print '<tr><td>'.$langs->trans('Type').'</td><td colspan="4">';
        print $object->getLibType();
        if ($object->type == FactureFournisseur::TYPE_REPLACEMENT)
        {
        	$facreplaced=new FactureFournisseur($db);
        	$facreplaced->fetch($object->fk_facture_source);
        	print ' ('.$langs->transnoentities("ReplaceInvoice",$facreplaced->getNomUrl(1)).')';
        }
        if ($object->type == FactureFournisseur::TYPE_CREDIT_NOTE)
        {
        	$facusing=new FactureFournisseur($db);
        	$facusing->fetch($object->fk_facture_source);
        	print ' ('.$langs->transnoentities("CorrectInvoice",$facusing->getNomUrl(1)).')';
        }

        $facidavoir=$object->getListIdAvoirFromInvoice();
        if (count($facidavoir) > 0)
        {
        	print ' ('.$langs->transnoentities("InvoiceHasAvoir");
        	$i=0;
        	foreach($facidavoir as $id)
        	{
        		if ($i==0) print ' ';
        		else print ',';
        		$facavoir=new FactureFournisseur($db);
        		$facavoir->fetch($id);
        		print $facavoir->getNomUrl(1);
        	}
        	print ')';
        }
        if (isset($facidnext) && $facidnext > 0)
        {
        	$facthatreplace=new FactureFournisseur($db);
        	$facthatreplace->fetch($facidnext);
        	print ' ('.$langs->transnoentities("ReplacedByInvoice",$facthatreplace->getNomUrl(1)).')';
        }
        print '</td></tr>';

        // Label
        print '<tr><td>'.$form->editfieldkey("Label",'label',$object->label,$object,($object->statut<2 && $user->rights->fournisseur->facture->creer)).'</td>';
        print '<td colspan="3">'.$form->editfieldval("Label",'label',$object->label,$object,($object->statut<2 && $user->rights->fournisseur->facture->creer)).'</td>';

        /*
         * List of payments
         */
        $nbrows=9; $nbcols=2;
        if (! empty($conf->projet->enabled)) $nbrows++;
        if (! empty($conf->banque->enabled)) { $nbrows++; $nbcols++; }

        // Local taxes
        if ($societe->localtax1_assuj=="1") $nbrows++;
        if ($societe->localtax2_assuj=="1") $nbrows++;

        print '<td rowspan="'.$nbrows.'" valign="top">';

        print '</td>';

        print '</tr>';

        // Date
        print '<tr><td>'.$form->editfieldkey("Date",'datef',$object->datep,$object,($object->statut<2 && $user->rights->fournisseur->facture->creer && $object->getSommePaiement() <= 0),'datepicker').'</td><td colspan="3">';
        print $form->editfieldval("Date",'datef',$object->datep,$object,($object->statut<2 && $user->rights->fournisseur->facture->creer && $object->getSommePaiement() <= 0),'datepicker');
        print '</td>';


        // Status
        $alreadypaid=$object->getSommePaiement();
        print '<tr><td>'.$langs->trans('Status').'</td><td colspan="3">'.$object->getLibStatut(4,$alreadypaid).'</td></tr>';


        // Project
        if (! empty($conf->projet->enabled))
        {
        	$langs->load('projects');
        	print '<tr>';
        	print '<td>';

        	print '<table class="nobordernopadding" width="100%"><tr><td>';
        	print $langs->trans('Project');
        	print '</td>';
        	if ($action != 'classify')
        	{
        		print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=classify&amp;id='.$object->id.'">';
        		print img_edit($langs->trans('SetProject'),1);
        		print '</a></td>';
        	}
        	print '</tr></table>';

        	print '</td><td colspan="3">';
        	if ($action == 'classify')
        	{
        		$form->form_project($_SERVER['PHP_SELF'].'?id='.$object->id, empty($conf->global->PROJECT_CAN_ALWAYS_LINK_TO_ALL_SUPPLIERS)?$object->socid:'-1', $object->fk_project, 'projectid');
        	}
        	else
        	{
        		$form->form_project($_SERVER['PHP_SELF'].'?id='.$object->id, $object->socid, $object->fk_project, 'none');
        	}
        	print '</td>';
        	print '</tr>';
        }

        // Other attributes
        $cols = 4;
        include DOL_DOCUMENT_ROOT . '/core/tpl/extrafields_view.tpl.php';

        print '</table>';

        if (! empty($conf->global->MAIN_DISABLE_CONTACTS_TAB))
        {
        	print '<br>';
        	$blocname = 'contacts';
        	$title = $langs->trans('ContactsAddresses');
        	include DOL_DOCUMENT_ROOT.'/core/tpl/bloc_showhide.tpl.php';
        }

        if (! empty($conf->global->MAIN_DISABLE_NOTES_TAB))
        {
        	$colwidth=20;
        	$blocname = 'notes';
        	$title = $langs->trans('Notes');
        	include DOL_DOCUMENT_ROOT.'/core/tpl/bloc_showhide.tpl.php';
        }


        /*
         * Lines
         */

		/*
        print '	<form name="addfiscal" id="addfiscal" action="'.$_SERVER["PHP_SELF"].'?etat=1&id='.$object->id.(($action != 'edit_line')?'#add':'#line_'.GETPOST('lineid')).'" method="POST">
        <input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">
        <input type="hidden" name="action" value="'.(($action != 'edit_line')?'addline':'update_line').'">
        <input type="hidden" name="mode" value="">
        <input type="hidden" name="id" value="'.$object->id.'">
        <input type="hidden" name="facid" value="'.$object->id.'">
        <input type="hidden" name="socid" value="'.$societe->id.'">

        ';
		*/

        print '<br>';
        print '<table id="tablelines" class="noborder noshadow" width="100%">';
        $var=1;
        $num=count($object->lines);
        for ($i = 0; $i < $num; $i++)
        {
        	if ($i == 0)
        	{
        		print '<tr class="liste_titre"><td>'.$langs->trans('Label').'</td>';
        		print '<td align="right">'.$langs->trans('VAT').'</td>';
        		print '<td align="right">'.$langs->trans('PriceUHT').'</td>';
        		print '<td align="right">'.$langs->trans('PriceUTTC').'</td>';
        		print '<td align="right">'.$langs->trans('Qty').'</td>';
        		print '<td align="right">'.$langs->trans('ReductionShort').'</td>';
        		print '<td align="right">'.$langs->trans('TotalHTShort').'</td>';
        		print '<td align="right">'.$langs->trans('TotalTTCShort').'</td>';
        		print '<td>&nbsp;</td>';
        		print '<td>&nbsp;</td>';
        		print '</tr>';
        	}

            // Show product and description
        	$type=(! empty($object->lines[$i]->product_type)?$object->lines[$i]->product_type:(! empty($object->lines[$i]->fk_product_type)?$object->lines[$i]->fk_product_type:0));
            // Try to enhance type detection using date_start and date_end for free lines where type was not saved.
        	$date_start='';
        	$date_end='';
        	if (! empty($object->lines[$i]->date_start))
        	{
        		$date_start=$object->lines[$i]->date_start;
        		$type=1;
        	}
        	if (! empty($object->lines[$i]->date_end))
        	{
        		$date_end=$object->lines[$i]->date_end;
        		$type=1;
        	}

        	$var=!$var;

            // Edit line
        	if ($object->statut == 0 && $action == 'edit_line' && $_GET['etat'] == '0' && $_GET['lineid'] == $object->lines[$i]->rowid)
        	{
        		print '<tr '.$bc[$var].'>';

                // Show product and description
        		print '<td>';

        		print '<input type="hidden" name="lineid" value="'.$object->lines[$i]->rowid.'">';

        		if ((! empty($conf->product->enabled) || ! empty($conf->service->enabled)) && $object->lines[$i]->fk_product > 0)
        		{
        			print '<input type="hidden" name="idprod" value="'.$object->lines[$i]->fk_product.'">';
        			$product_static=new ProductFournisseur($db);
        			$product_static->fetch($object->lines[$i]->fk_product);
        			$text=$product_static->getNomUrl(1);
        			$text.= ' - '.$product_static->libelle;
        			print $text;
        			print '<br>';
        		}
        		else
        		{
                    $forceall=1;	// For suppliers, we always show all types
                    print $form->select_type_of_lines($object->lines[$i]->product_type,'type',1,0,$forceall);
                    if ($forceall || (! empty($conf->product->enabled) && ! empty($conf->service->enabled))
                    	|| (empty($conf->product->enabled) && empty($conf->service->enabled))) print '<br>';
                }

            if (is_object($hookmanager))
            {
            	$parameters=array('fk_parent_line'=>$line->fk_parent_line, 'line'=>$object->lines[$i],'var'=>$var,'num'=>$num,'i'=>$i);
            	$reshook=$hookmanager->executeHooks('formEditProductOptions',$parameters,$object,$action);
            }

            $nbrows=ROWS_2;
            if (! empty($conf->global->MAIN_INPUT_DESC_HEIGHT)) $nbrows=$conf->global->MAIN_INPUT_DESC_HEIGHT;
            $doleditor=new DolEditor('desc',$object->lines[$i]->description,'',128,'dolibarr_details','',false,true,$conf->global->FCKEDITOR_ENABLE_DETAILS,$nbrows,70);
            $doleditor->Create();
            print '</td>';

                // VAT
            print '<td align="right">';
            print $form->load_tva('tauxtva',$object->lines[$i]->tva_tx,$societe,$mysoc);
            print '</td>';

                // Unit price
            print '<td align="right" class="nowrap"><input size="4" name="puht" type="text" value="'.price($object->lines[$i]->pu_ht).'"></td>';

            print '<td align="right" class="nowrap"><input size="4" name="puttc" type="text" value=""></td>';

            print '<td align="right"><input size="1" name="qty" type="text" value="'.$object->lines[$i]->qty.'"></td>';

            print '<td align="right" class="nowrap"><input size="1" name="remise_percent" type="text" value="'.$object->lines[$i]->remise_percent.'"><span class="hideonsmartphone">%</span></td>';

            print '<td align="right" class="nowrap">&nbsp;</td>';

            print '<td align="right" class="nowrap">&nbsp;</td>';

            print '<td align="center" colspan="2"><input type="submit" class="button" name="save" value="'.$langs->trans('Save').'">';
            print '<br><input type="submit" class="button" name="cancel" value="'.$langs->trans('Cancel').'"></td>';

            print '</tr>';
        }
            else // Affichage simple de la ligne
            {
            	print '<tr id="row-'.$object->lines[$i]->rowid.'" '.$bc[$var].'>';

                // Show product and description
            	print '<td>';
            	if ($object->lines[$i]->fk_product)
            	{
                    print '<a name="'.$object->lines[$i]->rowid.'"></a>'; // ancre pour retourner sur la ligne

                    $product_static=new ProductFournisseur($db);
                    $product_static->fetch($object->lines[$i]->fk_product);
                    $text=$product_static->getNomUrl(1);
                    $text.= ' - '.$product_static->libelle;
                    $description=($conf->global->PRODUIT_DESC_IN_FORM?'':dol_htmlentitiesbr($object->lines[$i]->description));
                    print $form->textwithtooltip($text,$description,3,'','',$i);

                    // Show range
                    print_date_range($date_start,$date_end);

                    // Add description in form
                    if (! empty($conf->global->PRODUIT_DESC_IN_FORM)) print ($object->lines[$i]->description && $object->lines[$i]->description!=$product_static->libelle)?'<br>'.dol_htmlentitiesbr($object->lines[$i]->description):'';
                }

                // Description - Editor wysiwyg
                if (! $object->lines[$i]->fk_product)
                {
                	if ($type==1) $text = img_object($langs->trans('Service'),'service');
                	else $text = img_object($langs->trans('Product'),'product');
                	print $text.' '.nl2br($object->lines[$i]->description);

                    // Show range
                	print_date_range($date_start,$date_end);
                }

                if (is_object($hookmanager))
                {
                	$parameters=array('fk_parent_line'=>$line->fk_parent_line, 'line'=>$object->lines[$i],'var'=>$var,'num'=>$num,'i'=>$i);
                	$reshook=$hookmanager->executeHooks('formViewProductSupplierOptions',$parameters,$object,$action);
                }
                print '</td>';

                // VAT
                print '<td align="right">'.vatrate($object->lines[$i]->tva_tx, true, $object->lines[$i]->info_bits).'</td>';

                // Unit price
                print '<td align="right" class="nowrap">'.price($object->lines[$i]->pu_ht,'MU').'</td>';

                print '<td align="right" class="nowrap">'.($object->lines[$i]->pu_ttc?price($object->lines[$i]->pu_ttc,'MU'):'&nbsp;').'</td>';

                print '<td align="right">'.$object->lines[$i]->qty.'</td>';

                print '<td align="right">'.(($object->lines[$i]->remise_percent > 0)?$object->lines[$i]->remise_percent.'%':'').'</td>';

                print '<td align="right" class="nowrap">'.price($object->lines[$i]->total_ht).'</td>';

                print '<td align="right" class="nowrap">'.price($object->lines[$i]->total_ttc).'</td>';

                if (is_object($hookmanager))
                {
                	$parameters=array('line'=>$object->lines[$i],'num'=>$num,'i'=>$i);
                	$reshook=$hookmanager->executeHooks('printObjectLine',$parameters,$object,$action);
                }

                print '<td align="center" width="16">';
                if ($object->statut == 0) print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit_line&amp;etat=0&amp;lineid='.$object->lines[$i]->rowid.'">'.img_edit().'</a>';
                else print '&nbsp;';
                print '</td>';

                print '<td align="center" width="16">';
                if ($object->statut == 0)
                {
                	print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=confirm_delete_line&amp;lineid='.$object->lines[$i]->rowid.'">'.img_delete().'</a>';
                }
                else print '&nbsp;';
                print '</td>';

                print '</tr>';
            }

        }

        //registro de la factura fiscal


        print '</table>';

        //print '</form>';

        dol_fiche_end();

        //registro de la factura fiscal
        if ($conf->fiscal->enabled)
        {
        	dol_fiche_head();

        //cfiscal

		//buscamos el vdosing
        	if ($action == 'editv' || $action == 'create')
        	{
        		print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
        		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
        		if ($action == 'editv')
	        		print '<input type="hidden" name="action" value="updatecfiscal">';
	        	else
	        		print '<input type="hidden" name="action" value="addcfiscal">';

        		print '<input type="hidden" name="facid" value="'.$object->id.'">'."\n";
        		print '<input type="hidden" name="idr" value="'.$objfiscal->id.'">'."\n";
        	}
        	print '<table class="noborder" width="100%">';

        	print "<tr class=\"liste_titre\">";
        	print_liste_field_titre($langs->trans("Factura"),"", "","","","");
        	print_liste_field_titre($langs->trans("Nit"),"", "","","","");
        	print_liste_field_titre($langs->trans("Name"),"", "","","","");
        	print_liste_field_titre($langs->trans("Date"),"", "","","","");
        	print_liste_field_titre($langs->trans("Autorizationnumber"),"", "",'','','align="center"');
        	print_liste_field_titre($langs->trans("Controlcode"),"", "",'','','align="center"');
        	print_liste_field_titre($langs->trans("Baseimp"),"", "",'','','align="right"');
        	print_liste_field_titre($langs->trans("Nosujetoaimp"),"", "",'','','align="right"');
        	print_liste_field_titre($langs->trans("Descuento"),"", "",'','','align="right"');
        	print_liste_field_titre($langs->trans("IVA"),"", "",'','','align="right"');
        	print_liste_field_titre($langs->trans("Balance"),"", "",'','','align="right"');
        	print_liste_field_titre($langs->trans("Action"),"", "",'','','align="right"');
        	print "</tr>\n";
        	$var=true;
        	if ($objfiscal->status == 2)
        		print '<tr style="color:#ff0000;">';
        	else
        		print "<tr $bc[$var]>";
        	if ($action == 'editv' || $action=='create')
        	{
        			print '<td>'.'<input type="text" name="nfiscal" value="'.$objfiscal->nfiscal.'" size="5"></td>';
        			print '<td>'.'<input type="text" name="nit" value="'.$objfiscal->nit.'" size="8"></td>';
        			print '<td>'.'<input type="text" name="razsoc" value="'.$objfiscal->razsoc.'" size="13"></td>';
        			print '<td>';
        			$form->select_date($objfiscal->date_exp,'re','','','',"crea_commande",1,1);
        			print $form->load_tva('tauxtva',$object->lines[$i]->tva_tx,$societe,$mysoc);
        			print '</td>';
        			print '<td align="center">'.'<input type="text" name="num_autoriz" value="'.$objfiscal->num_autoriz.'" size="13"></td>';
        			print '<td align="center">'.'<input type="text"  name="codecont" value="'.$objfiscal->cod_control.'" size="7"></td>';
        			print '<td align="right">'.'<input type="number" step="any" name="baseimp1" value="'.$objfiscal->baseimp1.'" size="7"></td>';
        			print '<td align="right">'.'<input type="number" step="any" name="baseimp1" value="'.$objfiscal->basenoimp1.'" size="7"></td>';
        			print '<td align="right">'.'<input type="number" step="any" name="baseimp1" value="'.$objfiscal->valret1.'" size="7"></td>';
        			print '<td align="right">'.'<input type="number" step="any" name="valimp1" value="'.$objfiscal->valimp1.'" size="7"></td>';
        			print '<td align="right">'.'</td>';
        			print '<td align="right">'.'<input type="submit" name="save" value="save">'.'</td>';
        	}
        	else
        	{
        		print '<td>'.img_picto($langs->trans("Invoice"),DOL_URL_ROOT.'/ventas/img/invoice.png','',1).' '.$objfiscal->nfiscal.'</a></td>';
        		print '<td>'.$objfiscal->nit.'</td>';
        		print '<td>'.$objfiscal->razsoc.'</td>';
        		print '<td>'.dol_print_date($objfiscal->date_exp,'day').'</td>';
        		print '<td align="center">'.$objfiscal->num_autoriz.'</td>';
        		print '<td align="center">'.$objfiscal->cod_control.'</td>';
        		print '<td align="right">'.price($objfiscal->baseimp1).'</td>';
        		print '<td align="right">'.price($objfiscal->basenoimp1).'</td>';
        		print '<td align="right">'.price($objfiscal->valret1).'</td>';
        		print '<td align="right">'.price($objfiscal->valimp1).'</td>';
        		print '<td align="right">'.price($objfiscal->baseimp1 - $objfiscal->valimp1).'</td>';
        		if ($user->rights->ventas->fact->edit && $lDosing)
        		{
        			print '<td align="right">'.'<a href="'.$_SERVER['PHP_SELF'].'?facid='.$object->id.'&action=editv">'.img_picto($langs->trans('Edit'),'edit').'</a></td>';		
        		}
        	}
        	print "</tr>\n";

					 //$db->free($result);

        	print "</table>";
        	if ($action == 'editv' && $lDosing)
        	{
        		print '</form>';
        	}

        	dol_fiche_end();
        }


        if ($action != 'presend')
        {
            /*
             * Boutons actions
             */

            print '<div class="tabsAction">';


			// Reopen a standard paid invoice
			if ($object->fk_statut >= 1)
			{


				print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?facid=' . $object->id . '&amp;action=create">' . $langs->trans('Facture') . '</a></div>';
			}

        }
        /*
         * Show mail form
        */
        if ($action == 'presend')
        {
        	$ref = dol_sanitizeFileName($object->ref);
        	include_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
        	$fileparams = dol_most_recent_file($conf->fournisseur->facture->dir_output.'/'.get_exdir($object->id,2).$ref, preg_quote($ref, '/').'([^\-])+');
        	$file=$fileparams['fullname'];

            // Define output language
        	$outputlangs = $langs;
        	$newlang = '';
        	if ($conf->global->MAIN_MULTILANGS && empty($newlang) && ! empty($_REQUEST['lang_id']))
        		$newlang = $_REQUEST['lang_id'];
        	if ($conf->global->MAIN_MULTILANGS && empty($newlang))
        		$newlang = $object->client->default_lang;

        	if (!empty($newlang))
        	{
        		$outputlangs = new Translate('', $conf);
        		$outputlangs->setDefaultLang($newlang);
        		$outputlangs->load('bills');
        	}

            // Build document if it not exists
        	if (! $file || ! is_readable($file))
        	{
        		$result = $object->generateDocument(GETPOST('model')?GETPOST('model'):$object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
        		if ($result <= 0)
        		{
        			dol_print_error($db,$result);
        			exit;
        		}
        		$fileparams = dol_most_recent_file($conf->fournisseur->facture->dir_output.'/'.get_exdir($object->id,2).$ref, preg_quote($ref, '/').'([^\-])+');
        		$file=$fileparams['fullname'];
        	}

        	print '<br>';
        	print_titre($langs->trans('SendBillByMail'));

            // Cree l'objet formulaire mail
        	include_once DOL_DOCUMENT_ROOT.'/core/class/html.formmail.class.php';
        	$formmail = new FormMail($db);
        	$formmail->param['langsmodels']=(empty($newlang)?$langs->defaultlang:$newlang);
        	$formmail->fromtype = 'user';
        	$formmail->fromid   = $user->id;
        	$formmail->fromname = $user->getFullName($langs);
        	$formmail->frommail = $user->email;
        	$formmail->withfrom=1;
        	$liste=array();
        	foreach ($object->thirdparty->thirdparty_and_contact_email_array(1) as $key=>$value)	$liste[$key]=$value;
        	$formmail->withto=GETPOST("sendto")?GETPOST("sendto"):$liste;
        	$formmail->withtocc=$liste;
        	$formmail->withtoccc=$conf->global->MAIN_EMAIL_USECCC;
        	$formmail->withtopic=$outputlangs->trans('SendBillRef','__FACREF__');
        	$formmail->withfile=2;
        	$formmail->withbody=1;
        	$formmail->withdeliveryreceipt=1;
        	$formmail->withcancel=1;
            // Tableau des substitutions
        	$formmail->substit['__FACREF__']=$object->ref;
        	$formmail->substit['__SIGNATURE__']=$user->signature;
        	$formmail->substit['__PERSONALIZED__']='';
        	$formmail->substit['__CONTACTCIVNAME__']='';

            //Find the good contact adress
        	$custcontact='';
        	$contactarr=array();
        	$contactarr=$object->liste_contact(-1,'external');

        	if (is_array($contactarr) && count($contactarr)>0) {
        		foreach($contactarr as $contact) {
        			if ($contact['libelle']==$langs->trans('TypeContact_invoice_supplier_external_BILLING')) {
        				require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
        				$contactstatic=new Contact($db);
        				$contactstatic->fetch($contact['id']);
        				$custcontact=$contactstatic->getFullName($langs,1);
        			}
        		}

        		if (!empty($custcontact)) {
        			$formmail->substit['__CONTACTCIVNAME__']=$custcontact;
        		}
        	}

            // Tableau des parametres complementaires
        	$formmail->param['action']='send';
        	$formmail->param['models']='invoice_supplier_send';
        	$formmail->param['facid']=$object->id;
        	$formmail->param['returnurl']=$_SERVER["PHP_SELF"].'?id='.$object->id;

            // Init list of files
        	if (GETPOST("mode")=='init')
        	{
        		$formmail->clear_attached_files();
        		$formmail->add_attached_files($file,basename($file),dol_mimetype($file));
        	}

            // Show form
        	print $formmail->get_form();

        	print '<br>';
        }
    }



// End of page
    llxFooter();
    $db->close();
