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

$langs->load("fiscal");
$langs->load("products");
$langs->load("other");
if (! empty($conf->stock->enabled)) $langs->load("stocks");
if (! empty($conf->facture->enabled)) $langs->load("bills");
if ($conf->productbatch->enabled) $langs->load("productbatch");

$mesg=''; $error=0; $errors=array(); $_error=0;

$id=GETPOST('id', 'int');
$ref=GETPOST('ref', 'alpha');
$type=GETPOST('type','int');
$action=(GETPOST('action','alpha') ? GETPOST('action','alpha') : 'view');
$confirm=GETPOST('confirm','alpha');
$socid=GETPOST('socid','int');
if (! empty($user->societe_id)) $socid=$user->societe_id;

$object = new Product($db);
$extrafields = new ExtraFields($db);
$objectadd = new Productadd($db);

// fetch optionals attributes and labels
$extralabels=$extrafields->fetch_name_optionals_label($object->table_element);

if ($id > 0 || ! empty($ref))
{
	$object = new Product($db);
	$object->fetch($id, $ref);
    $objectadd->fetch('',$object->id);
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



/*
 * Actions
 */

$createbarcode=empty($conf->barcode->enabled)?0:1;
if (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && empty($user->rights->barcode->creer_advance)) $createbarcode=0;

$parameters=array('id'=>$id, 'ref'=>$ref, 'objcanvas'=>$objcanvas);
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
$error=$hookmanager->error; $errors=$hookmanager->errors;


    // Update a product or service
if ($action == 'update' && ($user->rights->fiscal->prod->crear || $user->rights->fiscal->prod->mod))
{
    if ($object->id > 0)
    {
        $objectadd->percent_base = GETPOST('percent_base');
        $objectadd->fk_product_type = $object->type;
        $objectadd->tva_tx  = GETPOST('tva_tx');
        $objectadd->sel_ice = GETPOST('sel_ice');
        $objectadd->sel_iva = GETPOST('sel_iva');
        if ($objectadd->sel_iva <=0) $objectadd->tva_tx = 0;
        if ($objectadd->id > 0)
        {
            //modificamos
            $objectadd->fk_user_mod = $user->id;
            $objectadd->date_mod = dol_now();
            $objectadd->tms = dol_now();
            $res = $objectadd->update($user);
        }
        else
        {
            //creamos
            $objectadd->fk_unit_ext = 0;
            $objectadd->fk_product = $id;
            $objectadd->quant_convert=1;
            $objectadd->quant_material=0;
            $objectadd->quant_disassembly=0;
            $objectadd->price_std=0;
            $objectadd->fk_user_create = $user->id;
            $objectadd->fk_user_mod = $user->id;
            $objectadd->date_create = dol_now();
            $objectadd->date_mod = dol_now();
            $objectadd->tms = dol_now();
            $objectadd->status = 1;
            $res = $objectadd->create($user);
        }
        if ($res > 0)
        {
            $action = '';
        }
        else
        {
            if (count($object->errors)) setEventMessage($object->errors, 'errors');
            else setEventMessage($langs->trans($object->error), 'errors');
            $action = 'edit';
        }
    }
    else
    {
        if (count($object->errors)) setEventMessage($object->errors, 'errors');
        else setEventMessage($langs->trans("ErrorProductBadRefOrLabel"), 'errors');
        $action = 'edit';
    }
}


if (GETPOST("cancel") == $langs->trans("Cancel"))
{
    $action = '';
    header("Location: ".$_SERVER["PHP_SELF"]."?id=".$object->id);
    exit;
}


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
    // -----------------------------------------
    // When used in standard mode
    // -----------------------------------------
    if ($action == 'create' && ($user->rights->produit->creer || $user->rights->service->creer))
    {
        //WYSIWYG Editor
        require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';

		// Load object modCodeProduct
        $module=(! empty($conf->global->PRODUCT_CODEPRODUCT_ADDON)?$conf->global->PRODUCT_CODEPRODUCT_ADDON:'mod_codeproduct_leopard');
        if (substr($module, 0, 16) == 'mod_codeproduct_' && substr($module, -3) == 'php')
        {
            $module = substr($module, 0, dol_strlen($module)-4);
        }
        $result=dol_include_once('/core/modules/product/'.$module.'.php');
        if ($result > 0)
        {
        	$modCodeProduct = new $module();
        }

		// Load object modBarCodeProduct
        if (! empty($conf->barcode->enabled) && ! empty($conf->global->BARCODE_PRODUCT_ADDON_NUM))
        {
           $module=strtolower($conf->global->BARCODE_PRODUCT_ADDON_NUM);
           $result=dol_include_once('/core/modules/barcode/'.$module.'.php');
           if ($result > 0)
           {
            $modBarCodeProduct =new $module();
        }
    }

    print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="add">';
    print '<input type="hidden" name="type" value="'.$type.'">'."\n";
    if (! empty($modCodeProduct->code_auto))
       print '<input type="hidden" name="code_auto" value="1">';
   if (! empty($modBarCodeProduct->code_auto))
       print '<input type="hidden" name="barcode_auto" value="1">';

   if ($type==1) $title=$langs->trans("NewService");
   else $title=$langs->trans("NewProduct");
   print_fiche_titre($title);

   print '<table class="border" width="100%">';
   print '<tr>';
   $tmpcode='';
   if (! empty($modCodeProduct->code_auto)) $tmpcode=$modCodeProduct->getNextValue($object,$type);
   print '<td class="fieldrequired" width="20%">'.$langs->trans("Ref").'</td><td colspan="3"><input name="ref" size="20" maxlength="128" value="'.dol_escape_htmltag(GETPOST('ref')?GETPOST('ref'):$tmpcode).'">';
   if ($_error)
   {
    print $langs->trans("RefAlreadyExists");
}
print '</td></tr>';

        // Label
print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td colspan="3"><input name="libelle" size="40" maxlength="255" value="'.dol_escape_htmltag(GETPOST('libelle')).'"></td></tr>';

        // On sell
print '<tr><td class="fieldrequired">'.$langs->trans("Status").' ('.$langs->trans("Sell").')</td><td colspan="3">';
$statutarray=array('1' => $langs->trans("OnSell"), '0' => $langs->trans("NotOnSell"));
print $form->selectarray('statut',$statutarray,GETPOST('statut'));
print '</td></tr>';

        // To buy
print '<tr><td class="fieldrequired">'.$langs->trans("Status").' ('.$langs->trans("Buy").')</td><td colspan="3">';
$statutarray=array('1' => $langs->trans("ProductStatusOnBuy"), '0' => $langs->trans("ProductStatusNotOnBuy"));
print $form->selectarray('statut_buy',$statutarray,GETPOST('statut_buy'));
print '</td></tr>';

	    // Batch number management
if ($conf->productbatch->enabled)
{
   print '<tr><td class="fieldrequired">'.$langs->trans("Status").' ('.$langs->trans("Batch").')</td><td colspan="3">';
   $statutarray=array('0' => $langs->trans("ProductStatusNotOnBatch"), '1' => $langs->trans("ProductStatusOnBatch"));
   print $form->selectarray('status_batch',$statutarray,GETPOST('status_batch'));
   print '</td></tr>';
}

$showbarcode=empty($conf->barcode->enabled)?0:1;
if (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && empty($user->rights->barcode->lire_advance)) $showbarcode=0;

if ($showbarcode)
{
  print '<tr><td>'.$langs->trans('BarcodeType').'</td><td>';
  if (isset($_POST['fk_barcode_type']))
  {
   $fk_barcode_type=GETPOST('fk_barcode_type');
}
else
{
  if (empty($fk_barcode_type) && ! empty($conf->global->PRODUIT_DEFAULT_BARCODE_TYPE)) $fk_barcode_type = $conf->global->PRODUIT_DEFAULT_BARCODE_TYPE;
}
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formbarcode.class.php';
$formbarcode = new FormBarCode($db);
print $formbarcode->select_barcode_type($fk_barcode_type, 'fk_barcode_type', 1);
print '</td><td>'.$langs->trans("BarcodeValue").'</td><td>';
$tmpcode=isset($_POST['barcode'])?GETPOST('barcode'):$object->barcode;
if (empty($tmpcode) && ! empty($modBarCodeProduct->code_auto)) $tmpcode=$modBarCodeProduct->getNextValue($object,$type);
print '<input size="40" type="text" name="barcode" value="'.dol_escape_htmltag($tmpcode).'">';
print '</td></tr>';
}

        // Description (used in invoice, propal...)
print '<tr><td valign="top">'.$langs->trans("Description").'</td><td colspan="3">';

$doleditor = new DolEditor('desc', GETPOST('desc'), '', 160, 'dolibarr_notes', '', false, true, $conf->global->FCKEDITOR_ENABLE_PRODUCTDESC, 4, 80);
$doleditor->Create();

print "</td></tr>";

        // Public URL
print '<tr><td valign="top">'.$langs->trans("PublicUrl").'</td><td colspan="3">';
print '<input type="text" name="url" size="90" value="'.GETPOST('url').'">';
print '</td></tr>';

        // Stock min level
if ($type != 1 && ! empty($conf->stock->enabled))
{
    print '<tr><td>'.$langs->trans("StockLimit").'</td><td>';
    print '<input name="seuil_stock_alerte" size="4" value="'.GETPOST('seuil_stock_alerte').'">';
    print '</td>';
            // Stock desired level
    print '<td>'.$langs->trans("DesiredStock").'</td><td>';
    print '<input name="desiredstock" size="4" value="'.GETPOST('desiredstock').'">';
    print '</td></tr>';
}
else
{
    print '<input name="seuil_stock_alerte" type="hidden" value="0">';
    print '<input name="desiredstock" type="hidden" value="0">';
}

        // Nature
if ($type != 1)
{
    print '<tr><td>'.$langs->trans("Nature").'</td><td colspan="3">';
    $statutarray=array('1' => $langs->trans("Finished"), '0' => $langs->trans("RowMaterial"));
    print $form->selectarray('finished',$statutarray,GETPOST('finished'),1);
    print '</td></tr>';
}

        // Duration
if ($type == 1)
{
    print '<tr><td>'.$langs->trans("Duration").'</td><td colspan="3"><input name="duration_value" size="6" maxlength="5" value="'.GETPOST('duration_value').'"> &nbsp;';
    print '<input name="duration_unit" type="radio" value="h">'.$langs->trans("Hour").'&nbsp;';
    print '<input name="duration_unit" type="radio" value="d">'.$langs->trans("Day").'&nbsp;';
    print '<input name="duration_unit" type="radio" value="w">'.$langs->trans("Week").'&nbsp;';
    print '<input name="duration_unit" type="radio" value="m">'.$langs->trans("Month").'&nbsp;';
    print '<input name="duration_unit" type="radio" value="y">'.$langs->trans("Year").'&nbsp;';
    print '</td></tr>';
}

        if ($type != 1)	// Le poids et le volume ne concerne que les produits et pas les services
        {
            // Weight
            print '<tr><td>'.$langs->trans("Weight").'</td><td colspan="3">';
            print '<input name="weight" size="4" value="'.GETPOST('weight').'">';
            print $formproduct->select_measuring_units("weight_units","weight");
            print '</td></tr>';
            // Length
            print '<tr><td>'.$langs->trans("Length").'</td><td colspan="3">';
            print '<input name="size" size="4" value="'.GETPOST('size').'">';
            print $formproduct->select_measuring_units("size_units","size");
            print '</td></tr>';
            // Surface
            print '<tr><td>'.$langs->trans("Surface").'</td><td colspan="3">';
            print '<input name="surface" size="4" value="'.GETPOST('surface').'">';
            print $formproduct->select_measuring_units("surface_units","surface");
            print '</td></tr>';
            // Volume
            print '<tr><td>'.$langs->trans("Volume").'</td><td colspan="3">';
            print '<input name="volume" size="4" value="'.GETPOST('volume').'">';
            print $formproduct->select_measuring_units("volume_units","volume");
            print '</td></tr>';
        }

        // Custom code
        if (empty($conf->global->PRODUCT_DISABLE_CUSTOM_INFO))
        {
         print '<tr><td>'.$langs->trans("CustomCode").'</td><td><input name="customcode" size="10" value="'.GETPOST('customcode').'"></td>';
	        // Origin country
         print '<td>'.$langs->trans("CountryOrigin").'</td><td>';
         print $form->select_country(GETPOST('country_id','int'),'country_id');
         if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
         print '</td></tr>';
     }

        // Other attributes
     $parameters=array('colspan' => 3);
        $reshook=$hookmanager->executeHooks('formObjectOptions',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
        if (empty($reshook) && ! empty($extrafields->attribute_label))
        {
        	print $object->showOptionals($extrafields,'edit',$parameters);
        }

        // Note (private, no output on invoices, propales...)
        print '<tr><td valign="top">'.$langs->trans("NoteNotVisibleOnBill").'</td><td colspan="3">';

        // We use dolibarr_details as type of DolEditor here, because we must not accept images as description is included into PDF and not accepted by TCPDF.
        $doleditor = new DolEditor('note', GETPOST('note'), '', 140, 'dolibarr_details', '', false, true, $conf->global->FCKEDITOR_ENABLE_PRODUCTDESC, 8, 70);
        $doleditor->Create();

        print "</td></tr>";
        print '</table>';

        print '<br>';

        if (! empty($conf->global->PRODUIT_MULTIPRICES))
        {
            // We do no show price array on create when multiprices enabled.
            // We must set them on prices tab.
        }
        else
        {
            print '<table class="border" width="100%">';

            // PRIX
            print '<tr><td>'.$langs->trans("SellingPrice").'</td>';
            print '<td><input name="price" size="10" value="'.$object->price.'">';
            print $form->select_PriceBaseType($object->price_base_type, "price_base_type");
            print '</td></tr>';

            // MIN PRICE
            print '<tr><td>'.$langs->trans("MinPrice").'</td>';
            print '<td><input name="price_min" size="10" value="'.$object->price_min.'">';
            print '</td></tr>';

            // VAT
            print '<tr><td width="20%">'.$langs->trans("VATRate").'</td><td>';
            print $form->load_tva("tva_tx",-1,$mysoc,'');
            print '</td></tr>';

            print '</table>';

            print '<br>';
        }

        /*if (empty($conf->accounting->enabled) && empty($conf->comptabilite->enabled) && empty($conf->accountingexpert->enabled))
        {
            // Don't show accounting field when accounting id disabled.
        }
        else
        {*/
            print '<table class="border" width="100%">';

            // Accountancy_code_sell
            print '<tr><td>'.$langs->trans("ProductAccountancySellCode").'</td>';
            print '<td><input name="accountancy_code_sell" size="16" value="'.$object->accountancy_code_sell.'">';
            print '</td></tr>';

            // Accountancy_code_buy
            print '<tr><td width="20%">'.$langs->trans("ProductAccountancyBuyCode").'</td>';
            print '<td><input name="accountancy_code_buy" size="16" value="'.$object->accountancy_code_buy.'">';
            print '</td></tr>';

            print '</table>';

            print '<br>';
        //}

            print '<center><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

            print '</form>';
        }

    /*
     * Product card
     */

    if ($object->id > 0)
    {
        $head=product_prepare_head($object, $user);
        $titre=$langs->trans("CardProduct".$object->type);
        $picto=($object->type==1?'service':'product');
        dol_fiche_head($head, 'fiscal', $titre, 0, $picto);

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

        if ($showbarcode)
        {
                // Barcode type
            print '<tr><td class="nowrap">';
            print '<table width="100%" class="nobordernopadding"><tr><td class="nowrap">';
            print $langs->trans("BarcodeType");
            print '<td>';
            if (($action != 'editbarcodetype') && $user->rights->barcode->creer) print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=editbarcodetype&amp;id='.$object->id.'">'.img_edit($langs->trans('Edit'),1).'</a></td>';
            print '</tr></table>';
            print '</td><td colspan="2">';
            if ($action == 'editbarcodetype')
            {
                require_once DOL_DOCUMENT_ROOT.'/core/class/html.formbarcode.class.php';
                $formbarcode = new FormBarCode($db);
                $formbarcode->form_barcode_type($_SERVER['PHP_SELF'].'?id='.$object->id,$object->barcode_type,'fk_barcode_type');
            }
            else
            {
                $object->fetch_barcode();
                print $object->barcode_type_label?$object->barcode_type_label:($object->barcode?'<div class="warning">'.$langs->trans("SetDefaultBarcodeType").'<div>':'');
            }
            print '</td></tr>'."\n";

                // Barcode value
            print '<tr><td class="nowrap">';
            print '<table width="100%" class="nobordernopadding"><tr><td class="nowrap">';
            print $langs->trans("BarcodeValue");
            print '<td>';
            if (($action != 'editbarcode') && $user->rights->barcode->creer) print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=editbarcode&amp;id='.$object->id.'">'.img_edit($langs->trans('Edit'),1).'</a></td>';
            print '</tr></table>';
            print '</td><td colspan="2">';
            if ($action == 'editbarcode')
            {
                print '<form method="post" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'">';
                print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
                print '<input type="hidden" name="action" value="setbarcode">';
                print '<input type="hidden" name="barcode_type_code" value="'.$object->barcode_type_code.'">';
                print '<input size="40" type="text" name="barcode" value="'.$object->barcode.'">';
                print '&nbsp;<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
            }
            else
            {
                print $object->barcode;
            }
            print '</td></tr>'."\n";
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


        // Fiche en mode edition
        if ($action == 'edit' && ($user->rights->produit->creer || $user->rights->service->creer))
        {
            // percent_base
            if (isset($objectadd->percent_base))
                $percent_base = $objectadd->percent_base;
            else
                $percent_base = 100;
            print '<tr><td>'.$langs->trans("Calculationbase").'</td><td colspan="2">';
            print '<input type="number" name="percent_base" step="any" value="'.$percent_base.'">'.' % '.$langs->trans('Default').' 100%';
            print '</td></tr>';
            // sel_ice
            print '<tr><td>'.$langs->trans("ICE").'</td><td colspan="2">';
            print $form->selectyesno('sel_ice',$sel_ice,1);
            print '</td></tr>';
            // sel_iva
            print '<tr><td>'.$langs->trans("IVA").'</td><td colspan="2">';
            if (isset($objectadd->sel_iva))
            {
                $sel_iva = $objectadd->sel_iva;
                $tva_tx = $objectadd->tva_tx;
            }
            else
            {
                $sel_iva = 1;
                $tva_tx = 13;
            }
            print $form->selectyesno('sel_iva',$sel_iva,1);
            print '</td></tr>';
            // tva_tx
            print '<tr><td>'.$langs->trans("Tauxiva").'</td><td colspan="2">';
            print '<input type="number" name="tva_tx" step="any"  value="'.$tva_tx.'">'.' %';
            print '</td></tr>';

            print '</table>';

            print '<br>';

            print '<center><input type="submit" class="button" value="'.$langs->trans("Save").'"> &nbsp; &nbsp; ';
            print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';

            print '</form>';
        }
        // Fiche en mode visu
        else
        {

            //registros adicionales
            // percent_base
            print '<tr><td>'.$langs->trans("Calculationbase").'</td><td colspan="2">';
            print $objectadd->percent_base.' %';
            print '</td></tr>';
            // sel_ice
            print '<tr><td>'.$langs->trans("ICE").'</td><td colspan="2">';
            print ($objectadd->sel_ice?$langs->trans('Yes'):$langs->trans('No'));
            print '</td></tr>';
            // sel_iva
            print '<tr><td>'.$langs->trans("IVA").'</td><td colspan="2">';
            print ($objectadd->sel_iva?$langs->trans('Yes'):$langs->trans('No'));
            print '</td></tr>';
            // tva_tx
            print '<tr><td>'.$langs->trans("Tauxiva").'</td><td colspan="2">';
            print price($objectadd->tva_tx).' %';
            print '</td></tr>';

            print "</table>\n";

        }
        dol_fiche_end();
    }
    else if ($action != 'create')
    {
        header("Location: index.php");
        exit;
    }
}


// Define confirmation messages
$formquestionclone=array(
	'text' => $langs->trans("ConfirmClone"),
    array('type' => 'text', 'name' => 'clone_ref','label' => $langs->trans("NewRefForClone"), 'value' => $langs->trans("CopyOf").' '.$object->ref, 'size'=>24),
    array('type' => 'checkbox', 'name' => 'clone_content','label' => $langs->trans("CloneContentProduct"), 'value' => 1),
    array('type' => 'checkbox', 'name' => 'clone_prices', 'label' => $langs->trans("ClonePricesProduct").' ('.$langs->trans("FeatureNotYetAvailable").')', 'value' => 0, 'disabled' => true),
    array('type' => 'checkbox', 'name' => 'clone_composition', 'label' => $langs->trans('CloneCompositionProduct'), 'value' => 1)
    );

// Confirm delete product
if (($action == 'delete' && (empty($conf->use_javascript_ajax) || ! empty($conf->dol_use_jmobile)))	// Output when action = clone if jmobile or no js
	|| (! empty($conf->use_javascript_ajax) && empty($conf->dol_use_jmobile)))							// Always output when not jmobile nor js
{
    print $form->formconfirm("fiche.php?id=".$object->id,$langs->trans("DeleteProduct"),$langs->trans("ConfirmDeleteProduct"),"confirm_delete",'',0,"action-delete");
}

// Clone confirmation
if (($action == 'clone' && (empty($conf->use_javascript_ajax) || ! empty($conf->dol_use_jmobile)))		// Output when action = clone if jmobile or no js
	|| (! empty($conf->use_javascript_ajax) && empty($conf->dol_use_jmobile)))							// Always output when not jmobile nor js
{
    print $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id,$langs->trans('CloneProduct'),$langs->trans('ConfirmCloneProduct',$object->ref),'confirm_clone',$formquestionclone,'yes','action-clone',250,600);
}



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
     if ($user->rights->fiscal->prod->crear)
     {
         print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=edit&amp;id='.$object->id.'">'.$langs->trans("Modify").'</a></div>';
     }
 }
}

print "\n</div><br>\n";





llxFooter();
$db->close();
