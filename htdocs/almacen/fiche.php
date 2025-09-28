<?php
/* Copyright (C) 2003-2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005      Simon Tosser         <simon@kornog-computing.com>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
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
 *	\file       htdocs/almacen/fiche.php
 *	\ingroup    Almacen
 *	\brief      Page fiche fabrication
 */

require("../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
//require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
//require_once DOL_DOCUMENT_ROOT.'/core/modules/commande/modules_commande.php';
//require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
//require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
//require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/product/stock/class/entrepot.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
require_once(DOL_DOCUMENT_ROOT."/almacen/class/entrepotext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/core/modules/almacen/modules_almacen.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/local/class/entrepotrelationext.class.php");
//unico archivo extension del html.form
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formv.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.getutil.class.php");
if ($conf->fabrication->enabled)
{
	require_once(DOL_DOCUMENT_ROOT."/fabrication/productalternative/class/productalternative.class.php");
	require_once(DOL_DOCUMENT_ROOT."/fabrication/class/fabricationext.class.php");
	require_once(DOL_DOCUMENT_ROOT."/fabrication/class/fabricationdet.class.php");
	require_once(DOL_DOCUMENT_ROOT."/fabrication/productlist/class/productlist.class.php");
	require_once(DOL_DOCUMENT_ROOT."/fabrication/class/commandeventa.class.php");
	require_once(DOL_DOCUMENT_ROOT."/fabrication/units/class/units.class.php");
	//require_once(DOL_DOCUMENT_ROOT."/fabrication/class/productunit.class.php");
}

require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once(DOL_DOCUMENT_ROOT."/almacen/class/mouvementstockext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/stockmouvementadd.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/stockmouvementdocext.class.php");
//require_once(DOL_DOCUMENT_ROOT."/commande/class/commande.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacenext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacendetext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacendetfabricationext.class.php");
require_once DOL_DOCUMENT_ROOT.'/almacen/class/solalmacenlog.class.php';
require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacenform.class.php");
require_once DOL_DOCUMENT_ROOT.'/almacen/class/entrepotuserext.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/productunit.class.php';
require_once(DOL_DOCUMENT_ROOT."/almacen/lib/almacen.lib.php");

require_once(DOL_DOCUMENT_ROOT."/core/lib/stock.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/product.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");

require_once(DOL_DOCUMENT_ROOT."/core/class/html.formcompany.class.php");
if ($conf->projet->enabled && $conf->monprojet->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projectext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/monprojet/class/html.formprojetext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/monprojet/lib/verifcontact.lib.php';
}
if ($conf->orgman->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/cpartida.class.php';
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/partidaproduct.class.php';
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentuserext.class.php';
}
if ($conf->poa->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/poa/class/poastructureext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/poa/class/poapoaext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/poa/class/poapartidapreext.class.php';
}
if ($conf->purchase->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/purchase/class/purchaserequestext.class.php';
}
$langs->load("almacen");
$langs->load("products");
$langs->load("stocks");
$langs->load("companies");
$langs->load("other");

if ($conf->fabrication->enabled)
	$langs->load("fabrication@fabrication");

$action=GETPOST('action');

$id = GETPOST('id');
$warehouseid    = GETPOST("warehouseid");
$fk_fabrication = GETPOST("fk_fabrication");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");
$entrepotall = GETPOST('entrepotall');
if (! $sortfield) $sortfield="p.ref";
if (! $sortorder) $sortorder="DESC";

$mesg = '';
$typemethod = $conf->global->ALMACEN_METHOD_VALUATION_INVENTORY;

//verificamos el periodo
verif_year($action=='create'?true:false);

$period_year = $_SESSION['period_year'];
$lAddnew = $_SESSION['lAlmacennew'];

$now = dol_now();
$aDate = dol_getdate($now);

$formproduct=new FormProduct($db);
//$objCommande = new Commande($db);
if ($conf->fabrication->enabled)
	$objUnits = new Units($db);
  //llx_units
$object            = new Solalmacenext($db);
$objectdet         = new Solalmacendetext($db);
$objectUrqEntrepot = new Entrepotrelationext($db);
$objentrepotuser   = new Entrepotuserext($db);
$objectDetFab      = new Solalmacendetfabricationext($db);
$objMouvementadd   = new Stockmouvementadd($db);
$objectdoc         = new Stockmouvementdocext($db);

$entrepot = new Entrepotext($db);
$objuser = new User($db);
$objform = new Solalmacenform($db);
$objpunit = new Productunit($db);
$formfile = new FormFile($db);
$product = new Product($db);
if ($conf->fabrication->enabled)
	$formfabrication=new Fabricationext($db);
if ($conf->orgman->enabled)
{
	$objDepartament=new Pdepartamentext($db);
	$objDepartamentuser=new Pdepartamentuserext($db);
}

//verificamos saldos de productos
$objProduct = new Product($db);
$objSollog = new Solalmacenlog($db);
$extrafields = new ExtraFields($db);
// fetch optionals attributes and labels
$extralabels=$extrafields->fetch_name_optionals_label($object->table_element);
$extralabelsuser=$extrafields->fetch_name_optionals_label($objuser->table_element);
if (!empty($id))
	$object->fetch($id);
if (!empty($fk_fabrication) && $conf->fabrication->enabled)
{
	$formfabrication->fetch($fk_fabrication);
	if (!empty($formfabrication->fk_commande))
		$fk_commande = $formfabrication->fk_commande;
}
$aFilterent = array();
$aFilterentsol = array();
$filterusersol = '';
$now = dol_now();

if (!$user->admin) list($aFilterent, $filteruser,$aFilterentsol, $filterusersol,$aAreadirect,$fk_areaasign,$filterarea,$aFilterarea, $fk_user_resp,$aExcluded) = verif_accessalm();


$aLog = array(-2=>$langs->trans('StatusOrderRejected'),-1=>$langs->trans('StatusOrderCanceledShort'),0=>$langs->trans('StatusOrderDraftShort'),1=>$langs->trans('StatusOrderValidated'),6=>$langs->trans('StatusOrderApproved'),2=>$langs->trans('StatusOrderSent'),3=>$langs->trans('StatusOrderToBillShort'),4=>$langs->trans('StatusOrderProcessed'),5=>$langs->trans('StatusOrderoutofstock'));
/*
 * Actions
 */
// Confirmation de la validation
if ($action == 'confirm_validate' && $_REQUEST["confirm"] == 'yes' && $user->rights->almacen->pedido->val)
{
	//$object = new Solalmacenext($db);
	$id = GETPOST('id');
	$object->fetch($id);
	$ref = substr($object->ref, 1, 4);
	if ($ref == 'PROV')
	{
		$numref = $object->getNextNumRef($soc);
	}
	else
	{
		$numref = $object->ref;
	}
	//cambiando a validado
	$object->statut = 1;
	//$aDate = dol_getdate($object->date_create);
	//if (empty($aDate['month'])) $object->date_create = dol_now();
	$object->ref = $numref;
	$object->model_pdf = 'pedido';
	$db->begin();
	//update
	$res = $object->update($user);
	if ($res<=0)
	{
		$error++;
		setEventMessages($object->error,$object->errors,'errors');
	}
	if (!$error)
	{
		$objSollog->fk_solalmacen = $id;
		$objSollog->status = $object->statut;
		$objSollog->description = $aLog[$object->statut];
		$objSollog->fk_user_create = $user->id;
		$objSollog->fk_user_mod = $user->id;
		$objSollog->datec = dol_now();
		$objSollog->datem = dol_now();
		$objSollog->tms = dol_now();
		$res = $objSollog->create($user);
		if ($res <=0)
		{
			$error++;
			setEventMessages($objSollog->error,$objSollog->errors,'errors');
		}
	}
	if (!$error)
	{
		$db->commit();
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
			$ret = $object->fetch($id);
			$model=$object->model_pdf;
				// Reload to get new records
			$object->fetch_lines();
			$result=$object->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);
			if ($result < 0) dol_print_error($db,$result);
		}
		$result=almacen_pdf_create($db, $object, $object->model_pdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);

		header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
		exit;
	}
	else
	{
		$db->rollback();
		$action = '';
	}
}

// Confirmation approved
if ($action == 'confirm_approved' && $_REQUEST["confirm"] == 'yes' && $user->rights->almacen->pedido->app)
{
	//$object = new Solalmacenext($db);
	$id = GETPOST('id');
	$object->fetch($id);
	$ref = substr($object->ref, 1, 4);
	if ($ref == 'PROV')
	{
		$numref = $object->getNextNumRef($soc);
	}
	else
	{
		$numref = $object->ref;
	}
	if ($object->statut !=2)
	{
		//cambiando a validado
		$object->statut = 6;
		$object->fk_user_app = $user->id;
		$object->datea = $now;
		$object->datem = $now;
		//$aDate = dol_getdate($object->date_create);
		//if (empty($aDate['month'])) $object->date_create = dol_now();
		//$object->ref = $numref;
		$object->model_pdf = 'pedido';
		$db->begin();
		//update
		$res = $object->update($user);
		if ($res<=0)
		{
			$error++;
			setEventMessages($object->error,$object->errors,'errors');
		}
		if (!$error)
		{
			$objSollog->fk_solalmacen = $id;
			$objSollog->status = $object->statut;
			$objSollog->description = $aLog[$object->statut];
			$objSollog->fk_user_create = $user->id;
			$objSollog->fk_user_mod = $user->id;
			$objSollog->datec = dol_now();
			$objSollog->datem = dol_now();
			$objSollog->tms = dol_now();
			$res = $objSollog->create($user);
			if ($res <=0)
			{
				$error++;
				setEventMessages($objSollog->error,$objSollog->errors,'errors');
			}
		}
		if (!$error)
		{
			$db->commit();
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
				$ret = $object->fetch($id);
				$model=$object->model_pdf;
			// Reload to get new records
				$object->fetch_lines();
				$result=$object->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);
				if ($result < 0) dol_print_error($db,$result);
			}
			$result=almacen_pdf_create($db, $object, $object->model_pdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);
			header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
			exit;
		}
		else
		{
			$db->rollback();
			$action = '';
		}
	}
	else
	{
		$error++;
		setEventMessages('Esta realizando una accion no permitida',null,'errors');
	}
}

// Confirmation change status deliverable
// revisar
if ($action == 'confirm_changestatusval' && $_REQUEST["confirm"] == 'yes' && $user->rights->almacen->pedido->ent)
{
	//$object = new Solalmacenext($db);
	$id = GETPOST('id');
	$object->fetch($id);
	if ($object->statut == 5)
	{
		$object->statut = 6;
		$db->begin();
		$res = $object->update($user);
		if ($res <=0)
		{
			$error++;
			setEventMessages($object->error,$object->errors,'errors');
		}
		if (!$error)
		{
			$objSollog->fk_solalmacen = $id;
			$objSollog->status = $object->statut;
			$objSollog->description = $aLog[$object->statut];
			$objSollog->fk_user_create = $user->id;
			$objSollog->fk_user_mod = $user->id;
			$objSollog->datec = dol_now();
			$objSollog->datem = dol_now();
			$objSollog->tms = dol_now();
			$res = $objSollog->create($user);
			if ($res <=0)
			{
				$error++;
				setEventMessages($objSollog->error,$objSollog->errors,'errors');
			}
		}

		if (!$error)
		{
			$db->commit();
			// Define output language
			if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE) && $abc)
			{
				$outputlangs = $langs;
				$newlang = '';
				if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
				if ($conf->global->MAIN_MULTILANGS && empty($newlang))	$newlang = $object->thirdparty->default_lang;
				if (! empty($newlang)) {
					$outputlangs = new Translate("", $conf);
					$outputlangs->setDefaultLang($newlang);
				}
				$ret = $object->fetch($id);
				$model=$object->model_pdf;
				// Reload to get new records
				$object->fetch_lines();
				$result=$object->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);
				if ($result < 0) dol_print_error($db,$result);
			}
			//$result=almacen_pdf_create($db, $object, $object->model_pdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);

			setEventMessages($langs->trans('Order marked as deliverable'),null,'mesgs');
			header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
			exit;
		}
		else
		{
			$db->rollback();
			$action = '';
		}
	}
	else
	{
		setEventMessages($langs->trans('No se puede cambiar de estado'),null,'warnings');
	}
	$action = '';
}
// Confirmation de la validation
if ($action == 'confirm_noexist' && $_REQUEST["confirm"] == 'yes' && $user->rights->almacen->pedido->ent)
{
	$id = GETPOST('id');
	$object->fetch($id);
	if ($object->statut == 6)
	{
		$object->statut = 5;
		$db->begin();
		$res = $object->update($user);
		if ($res <=0)
		{
			$error++;
			setEventMessages($object->error,$object->errors,'errors');
		}
		if (!$error)
		{
			$objSollog->fk_solalmacen = $id;
			$objSollog->status = $object->statut;
			$objSollog->description = $aLog[$object->statut];
			$objSollog->fk_user_create = $user->id;
			$objSollog->fk_user_mod = $user->id;
			$objSollog->datec = dol_now();
			$objSollog->datem = dol_now();
			$objSollog->tms = dol_now();
			$res = $objSollog->create($user);
			if ($res <=0)
			{
				$error++;
				setEventMessages($objSollog->error,$objSollog->errors,'errors');
			}
		}

		if (!$error)
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
				$ret = $object->fetch($id);
				$model=$object->model_pdf;
				// Reload to get new records
				$object->fetch_lines();
				$result=$object->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);
				if ($result < 0) dol_print_error($db,$result);
			}
			$result=almacen_pdf_create($db, $object, $object->model_pdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);

			setEventMessages($langs->trans('Order marked without existence'),null,'mesgs');
			header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
			exit;
		}
		else
		{
			$db->rollback();
			$action = '';
		}
	}
	else
	{
		setEventMessages($langs->trans('No se puede cambiar de estado'),null,'warnings');
	}
	$action = '';
}

if ($action == 'builddoc')
// En get ou en post
{
	$object = new Solalmacenext($db);
	$id = GETPOST('id');
	$object->fetch($id);
	$object->fetch_thirdparty();
	$object->fetch_lines();

	if (GETPOST('model'))
	{
		$object->setDocModel($user, GETPOST('model'));
	}
	if (GETPOST('model') == 'pedido')
		$object->model_pdf = 'pedido';
	if (GETPOST('model') == 'entrega')
		$object->model_pdf = 'entrega';
	// Define output language
	$outputlangs = $langs;
	$newlang='';
	if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang=GETPOST('lang_id');
	if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$object->client->default_lang;
	if (! empty($newlang))
	{
		$outputlangs = new Translate("",$conf);
		$outputlangs->setDefaultLang($newlang);
	}
	$result=almacen_pdf_create($db, $object, $object->model_pdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);
	if ($result <= 0)
	{
		dol_print_error($db,$result);
		exit;
	}
	else
	{
		header('Location: '.$_SERVER["PHP_SELF"].'?id='.$object->id.(empty($conf->global->MAIN_JUMP_TAG)?'':'#builddoc'));
		exit;
	}
}

// registro de productos alternativos
if ($action == 'addlines' && $user->rights->almacen->crearlistproductalt && $conf->fabrication->enabled)
{
	$objpalt = new Productalternative($db);

	$objpalt->fk_product     = GETPOST("idprod");
	$objpalt->fk_unit        = GETPOST("fk_unit_father");
	$objpalt->entity         = $conf->entity;
	$objpalt->fk_product_alt = GETPOST("idprod1");
	$objpalt->fk_unit_alt    = GETPOST("fk_unit_son");
	$objpalt->qty            = GETPOST("qty_father");
	$objpalt->qty_alt        = GETPOST("qty_son");
	$objpalt->statut         = 1;
	if ($objpalt->fk_product && $objpalt->fk_product_alt) {
		$id = $objpalt->create($user);
		if ($id > 0)
		{
			header("Location: fiche.php?id=".GETPOST('id'));
			exit;
		}
		$action = 'create';
		$mesg='<div class="error">'.$objpalt->error.'</div>';
	}
	else {
		$mesg='<div class="error">'.$langs->trans("ErrorProductRequired").'</div>';
	  $action="create";   // Force retour sur page creation
	}
}

// Ajout entrepot
if ($action == 'add' && $user->rights->almacen->pedido->write)
{
	//$object = new Solalmacenext($db);

	if ($conf->global->ALMACEN_REGISTER_DATEDELIVERY)
		$datedelivery  = dol_mktime(23, 59, 0, GETPOST('remonth'),  GETPOST('reday'),  GETPOST('reyear'));
	else
		$datedelivery = dol_now();

	$ref = '(PROV)'.generarcodigoalm(4);
	$object->ref           	= $ref;
	$object->entity        	= $conf->entity;
	$object->fk_entrepot_from = GETPOST('id_entrepot_source')+0;
	$object->fk_entrepot   	= GETPOST('fk_entrepot');
	$object->fk_fabrication = GETPOST('fk_fabrication')+0;
	$object->fk_departament = GETPOST('fk_departament')+0;
	$object->fk_projet 		= GETPOST('fk_projet')+0;
	if ($user->admin) $object->fk_user    = GETPOST('fk_user') + 0;
	else $object->fk_user    = $user->id;
	$object->description   	= GETPOST('description');
	$object->statut        	= 0;
	$object->date_creation 	= $now;
	$object->date_delivery 	= $datedelivery;
	$object->fk_user_create = $user->id;
	$object->fk_user_mod 	= $user->id;
	$object->fk_user_app = 0;
	$object->fk_user_ent = 0;
	$object->datem = $now;
	$object->tms = dol_now();
	if ($object->fk_entrepot <=0)
	{
		$error++;
		setEventMessages($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Deliveryby')),null, 'errors');
	}
	if ($object->date_delivery < $object->date_creation)
	{
		$error++;
		setEventMessages($langs->trans('La fecha de entrega no puede ser menor a la fecha de creacion'),null,'errors');
	}
	$db->begin();
	if (empty($error))
	{
		$id = $object->create($user);
		if ($id <=0)
		{
			setEventMessages($object->error,$object->errors,'errors');
			$error++;
		}
		else
		{
			$objSollog->fk_solalmacen = $id;
			$objSollog->status = $object->statut;
			$objSollog->description = $aLog[$object->statut];
			$objSollog->fk_user_create = $user->id;
			$objSollog->fk_user_mod = $user->id;
			$objSollog->datec = dol_now();
			$objSollog->datem = dol_now();
			$objSollog->tms = dol_now();
			$res = $objSollog->create($user);
			if ($res <=0)
			{
				$error++;
				setEventMessages($objSollog->error,$objSollog->errors,'errors');
			}
		}
	}

	if (!$error)
	{
		$db->commit();
		setEventMessages($langs->trans('Record created successfully'),null,'mesgs');
		header("Location: fiche.php?id=".$id);
		exit;
	}
	else
	{
		$db->rollback();
		$action="create";
	}
}

//registrar producto a fabrication
if ($action == 'transferf' && $user->rights->almacen->pedido->write)
{
	$object = new Solalmacenext($db);
	$object->fetch($_GET['id']);
	$prowid = GETPOST("pid");
	$qty    = GETPOST("qty");
	$idr = GETPOST('idr','int');
	if (!empty($prowid))
	{
	  //buscamos el precio unitario del producto
		$objProduct->fetch($prowid);
		$objProduct->load_stock();
		$pmp = 0;
		if ($objProduct->stock_warehouse[$object->fk_entrepot]->pmp)
			$pmp = $objProduct->stock_warehouse[$object->fk_entrepot]->pmp;
		$objectdet = new Solalmacendetext($db);
		$objectdet->fk_almacen = $_GET["id"];
		$objectdet->fk_fabricationdet = GETPOST('idr','int');
		$objectdet->fk_product = $prowid;
		$objectdet->qty        = GETPOST("qty",'int');
		$objectdet->price      = $pmp;
		$objectdet->fk_projet  = GETPOST('fk_projet')+0;
		$objectdet->fk_user_create = $user->id;
		$objectdet->fk_user_mod = $user->id;
		$objectdet->date_create = dol_now();
		$objectdet->date_mod = dol_now();
		$objectdet->tms = dol_now();
		$objectdet->status = 1;

		if ($objectdet->fk_product) {
			$id = $objectdet->create($user);
			if ($id > 0)
			{
				header("Location: fiche.php?id=".$_GET['id']);
				exit;
			}
	//$action = 'create';
			$mesg='<div class="error">'.$objectdet->error.'</div>';
		}
		else
		{
			$mesg='<div class="error">'.$langs->trans("ErrorRefRequired").'</div>';
	  //$action="create";   // Force retour sur page creation
		}
	}
}
//registrar producto a fabrication
if ($action == 'confirmalternative' && $user->rights->almacen->pedido->write && $conf->fabrication->enabled)
{
	$object = new Solalmacenext($db);
	$object->fetch(GETPOST('id'));
	$rowid = GETPOST('rowid');
	$prowid = GETPOST('pid');
	if (!empty($object->id))
	{
		$objectdet = new Solalmacendetext($db);
		$objectdet->fetch(GETPOST('rowid'));
		$qtySol = $objectdet->qty;

		$objProdAlt = new Productalternative($db);
		$objProdAlt->fetch(GETPOST('pid'));

		$newQty = $qtySol / $objProdAlt->qty * $objProdAlt->qty_alt;
		$objectdet->fk_product = $objProdAlt->fk_product_alt;
		$objectdet->qty = $newQty;
		$objectdet->fk_user_mod = $user->id;
		$objectdet->date_mod = dol_now();
		$objectdet->tms = dol_now();

		$objectdet->update($user);
		header("Location: fiche.php?id=".$_GET['id']);
		exit;

	}
}

//registrar como producto
if ($action == 'add_product' && $user->rights->fabrication->creer)
{
	$commandedet = new Orderline($db);
	$commandedet->fetch($_GET['pid']);
	$product = new Product($db);
	$product->ref = $commandedet->desc;
	$product->libelle = $commandedet->desc;
	$product->entity = $conf->entity;
	$product->label = $commandedet->desc;
	$product->description = $commandedet->desc;
	$product->type = 0;
	$product->tosell = 1;
	$product->tosell = 0;

	$id = $product->create($user);
	if ($id > 0)
	{
	  //actualizar el registro del producto nuevo
		$commandedet->fk_product = $id;
		$sql = "UPDATE ".MAIN_DB_PREFIX."commandedet SET";
		$sql.= " fk_product='".$id."'";
		$sql.= " WHERE rowid = ".$commandedet->rowid;
		$resql=$db->query($sql);
		header("Location: fiche.php?id=".$_GET['id']);
		exit;
	}
	$action = '';
	$mesg='<div class="error">'.$objectdet->error.'</div>';
}
//registrar producto a fabrication
if ($action == 'transferdel' && $user->rights->almacen->pedido->write)
{
	$object = new Solalmacenext($db);
	$object->fetch($_GET['id']);
	if (!empty($object->id))
	{
		$objectdet = new Solalmacendetext($db);
		$objectdet->fetch($_GET['aid']);

		if ($objectdet->fk_almacen == $_GET['id'])
		{
			$objectdet->delete($user);
			//borramos su registro en solalmacendetfabrication
			$res = $objectDetFab->delete_almacendet($_GET['aid']);
			header("Location: fiche.php?id=".$_GET['id']);
			exit;
		}
	}
}

if ($action == 'addline' && $user->rights->almacen->pedido->write)
{
	$langs->load('errors');
	$error = 0;
	$idprod=GETPOST('idprod', 'int');
	$search_idprod=GETPOST('search_idprod', 'alpha');
	$desc = GETPOST('np_desc');
		//buscamos el producto
	$product = new Product($db);
	if ($idprod>0 || !empty($search_idprod))
	{
		if ($product->fetch((!empty(GETPOST('idprod'))?GETPOST('idprod'):''),(!empty(GETPOST('search_idprod'))?GETPOST('search_idprod'):''))>0)
			{
				$idprod = $product->id;
				$fk_unit = $product->fk_unit;
			}
		}
		if (empty($idprod))
		{
			$error++;
			setEventMessages($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Product')),null, 'errors');
		}
		if (empty($fk_unit))
		{
			$fk_unit = GETPOST('fk_unit');
			if (empty($fk_unit))
			{
				$error++;
				setEventMessages($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Unit')),null, 'errors');
			}
		}
		if ($user->rights->almacen->leersell && $user->rights->almacen->leernosell)
		{

		}
		elseif(!$user->rights->almacen->leersell && $user->rights->almacen->leernosell)
		{
			if (!$product->status_buy)
			{
				$error++;
				setEventMessage($langs->trans('Error No esta permitido para usar el producto de compra').$product->status_buy, 'errors');
			}
		}
		elseif($user->rights->almacen->leersell && !$user->rights->almacen->leernosell)
		{
			if (!$product->status)
			{
				$error++;
				setEventMessage($langs->trans('Error No esta permitido para usar el producto de venta').$product->status, 'errors');
			}
		}

	//if (!$user->rights->almacen->leersell && $product->status)
	//{
	//	$error++;
	//	setEventMessage($langs->trans('Error No esta permitido para usar el producto de venta').$product->status, 'errors');
	//}
	//if (!$user->rights->almacen->leernosell && $product->status_buy)
	//{
	//	$error++;
	//	setEventMessage($langs->trans('Error No esta permitido para usar el producto de compra').$product->status_buy, 'errors');
	//}

		$fk_fabricationdet = GETPOST('fk_fabricationdet','int');
		if ((empty($idprod)) && (GETPOST('qty') < 0))
		{
			setEventMessage($langs->trans('ErrorBothFieldCantBeNegative', $langs->transnoentitiesnoconv('UnitPriceHT'), $langs->transnoentitiesnoconv('Qty')), 'errors');
			$error++;
		}
		if (! GETPOST('qty') && GETPOST('qty') == '')
		{
			setEventMessage($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Qty')), 'errors');
			$error++;
		}

		if (! $error && (GETPOST('qty') >= 0) && (! empty($product_desc) || ! empty($idprod)))
		{
		// Clean parameters
			$predef=((! empty($idprod) && $conf->global->MAIN_FEATURES_LEVEL < 2) ? '_predef' : '');
			$date_start=dol_mktime(0, 0, 0, GETPOST('date_start'.$predef.'month'), GETPOST('date_start'.$predef.'day'), GETPOST('date_start'.$predef.'year'));
			$date_end=dol_mktime(0, 0, 0, GETPOST('date_end'.$predef.'month'), GETPOST('date_end'.$predef.'day'), GETPOST('date_end'.$predef.'year'));
		//	$price_base_type = (GETPOST('price_base_type', 'alpha')?GETPOST('price_base_type', 'alpha'):'HT');

		// Ecrase $pu par celui du produit
		// Ecrase $desc par celui du produit
		// Ecrase $txtva par celui du produit
		// Ecrase $base_price_type par celui du produit
			if (! empty($idprod))
			{
				$prod = new Product($db);
				$prod->fetch($idprod);

				$type = $prod->type;
			}
			$info_bits=0;
			if (!$error)
			{
				if (! empty($idprod))
				{
				// Insert line
					$object = new Solalmacenext($db);
					$object->fetch(GETPOST('id'));
					$objectdet = new Solalmacendetext($db);

					$_SESSION['fk_fabricationdet'][$object->fk_fabrication] = $fk_fabricationdet;

				//buscamos el precio unitario del producto
					$objProduct->fetch($idprod);
					$fk_unit = $objProduct->fk_unit;
					if (empty($fk_unit)) $fk_unit = GETPOST('fk_unit');
					$objProduct->load_stock();
					$pmp = 0;
					if ($objProduct->stock_warehouse[$object->fk_entrepot]->pmp)
						$pmp = $objProduct->stock_warehouse[$object->fk_entrepot]->pmp;
					else
						$pmp = $objProduct->pmp;
				//agregando el producto
					$objectdet->fk_almacen 	= $object->id;
					$objectdet->qty        	= GETPOST('qty');
					$objectdet->description	= GETPOST('np_desc');
					$objectdet->fk_product 	= $idprod;
					$objectdet->fk_unit 	= $fk_unit;
					$objectdet->fk_projet 	= GETPOST('fk_projet')+0;
					$objectdet->fk_projet_task 	= GETPOST('fk_task')+0;
					$objectdet->fk_jobs 	= GETPOST('fk_jobs')+0;
					$objectdet->fk_jobsdet 	= GETPOST('fk_jobsdet')+0;
					$objectdet->fk_structure = GETPOST('fk_structure')+0;
					$objectdet->price      	= $pmp;
					$objectdet->fk_user_create = $user->id;
					$objectdet->fk_user_mod = $user->id;
					$objectdet->date_create = dol_now();
					$objectdet->date_mod = dol_now();
					$objectdet->tms = dol_now();
					$objectdet->status = 1;
					$result = $objectdet->create($user);


					$objectDetFab->fk_almacendet = $result;
					$objectDetFab->fk_fabricationdet = $fk_fabricationdet+0;
					$objectDetFab->qty=GETPOST('qty');
					$objectDetFab->qtylivree = GETPOST('qty');
					$objectDetFab->price = $pmp;
					$res = $objectDetFab->create($user);

					if ($res <=0)
					{
						$error++;
						setEventMessages($objectDetFab->error,$objectDetFab->errors,'errors');
					}

					if ($result > 0)
					{
						unset($_POST['qty']);
						unset($_POST['idprod']);

				// old method
						unset($_POST['np_desc']);
						unset($_POST['dp_desc']);
						unset($_POST['fk_projet']);
						unset($_POST['fk_task']);
						header("Location: ".$_SERVER['PHP_SELF']."?id=".$object->id);
						exit;
					}
					else
					{
						$error++;
						setEventMessages($objectdet->error,$objectdet->errors,'errors');
					}
				}
			}
		}
	}

//re-validation
	if ($action == 'revalidate')
	{
	//$object = new Solalmacenext($db);
		$object->fetch($id);
	//cambiando a borrador
		$object->statut = 0;
	//update
		$db->begin();
		$res = $object->update($user);
		if ($res <= 0)
		{
			$error++;
			setEventMessages($object->error,$object->errors,'errors');
		}
		if (!$error)
		{
			$objSollog->fk_solalmacen = $id;
			$objSollog->status = $object->statut;
			$objSollog->description = $aLog[$object->statut];
			$objSollog->fk_user_create = $user->id;
			$objSollog->fk_user_mod = $user->id;
			$objSollog->datec = dol_now();
			$objSollog->datem = dol_now();
			$objSollog->tms = dol_now();
			$res = $objSollog->create($user);
			if ($res <=0)
			{
				$error++;
				setEventMessages($objSollog->error,$objSollog->errors,'errors');
			}
		}
		if (!$error)
		{
			$db->commit();
			header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
			exit;
		}
		else
		{
			$db->rollback();
			$action = '';
		}
	}

// Delete solalmacen
	if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->almacen->pedido->del)
	{
	//$object = new Solalmacenext($db);
		$res = $object->fetch($id);
		if ($res > 0)
		{
			$result=$object->delete($user);
		//eliminamos los dependientes
			$res = $objectdet->fetchAll('','',0,0,array(1=>1),'AND',' AND t.fk_almacen = '.$id);
			if ($res > 0)
			{
				$lines = $objectdet->lines;
				foreach ($lines AS $j => $line)
				{
					$resd = $objectdet->fetch($line->id);
					if ($resd>0)
					{
						$objectdet->delete($user);
					}
				}
			}

			if ($result > 0)
			{
				header("Location: ".DOL_URL_ROOT.'/almacen/liste.php');
				exit;
			}
			else
			{
				$mesg='<div class="error">'.$object->error.'</div>';
			}
		}
		$action = '';
	}
// anular
	if ($action == 'confirm_anulate' && $_REQUEST["confirm"] == 'yes' && $user->rights->almacen->pedido->nul)
	{
	//$object = new Solalmacenext($db);
		$res = $object->fetch($id);
		if ($res > 0)
		{
			$object->statut = -1;
			$result = $object->update($user);
		//$result=$object->delete($user);
			if ($result > 0)
			{
				header("Location: ".DOL_URL_ROOT.'/almacen/liste.php');
				exit;
			}
			else
			{
				$mesg='<div class="error">'.$object->error.'</div>';
			}
		}
		$action = '';
	}


// reject solalmacen
	if ($action == 'confirm_reject' && $_REQUEST["confirm"] == 'yes' && $user->rights->almacen->pedido->rech)
	{
	//$object = new Solalmacenext($db);
		$res = $object->fetch($id);
		if ($res > 0)
		{
			$object->statut = -2;
			$result = $object->update($user);
		//$result=$object->delete($user);
			if ($result > 0)
			{
				header("Location: ".DOL_URL_ROOT.'/almacen/liste.php');
				exit;
			}
			else
			{
				$mesg='<div class="error">'.$object->error.'</div>';
			}
		}
		$action = '';
	}

// Modification entrepot
	if ($action == 'update' && $_POST["cancel"] <> $langs->trans("Cancel"))
	{
		$object = new Solalmacenext($db);
		if ($object->fetch(GETPOST("id")))
		{
			$datedelivery  = dol_mktime(23, 59, 0, GETPOST('remonth'),  GETPOST('reday'),  GETPOST('reyear'));

			$object->fk_entrepot_from = $_POST["id_entrepot_source"]+0;
			$object->fk_entrepot   	= $_POST["fk_entrepot"];
			$object->fk_fabrication = $_POST["fk_fabrication"]+0;
			$object->fk_departament = $_POST["fk_departament"]+0;
			$object->description   	= $_POST["description"];
			$object->fk_projet   	= GETPOST('fk_projet');
			$object->fk_projet_task = GETPOST('fk_task');
			if (empty($object->fk_projet)) $object->fk_projet = 0;
			if (empty($object->fk_projet_task)) $object->fk_projet_task = 0;
			if ($user->admin)
				$object->fk_user    = GETPOST('fk_user') + 0;
			else
				$object->fk_user    = $user->id;
			$object->date_creation 	= dol_now();
			$object->date_delivery 	= $datedelivery;

			if ($object->update($user) > 0)
			{
				$action = '';
				$_GET["id"] = $_POST["id"];
			}
			else
			{
				$action = 'edit';
				$_GET["id"] = $_POST["id"];
				setEventMessages($object->error,$object->errors,'errors');
			}
		}
		else
		{
			$action = 'edit';
			$_GET["id"] = $_POST["id"];
			setEventMessages($object->error,$object->errors,'errors');
		}
	}
// Modification item
	if ($action == 'updateitem' && $user->rights->almacen->pedido->write)
	{
		$objectdet = new Solalmacendetext($db);
		if ($objectdet->fetch($_POST["rowid"]))
		{
			$objectdet->qty     		= GETPOST('qty','int');
			$objectdet->description 	= GETPOST('np_desc','alpha');
			$objectdet->fk_projet 		= GETPOST('fk_projet','int')+0;
			$objectdet->fk_projet_task = GETPOST('fk_task','int')+0;
			$objectdet->fk_jobs = GETPOST('fk_jobs','int')+0;
			$objectdet->fk_jobsdet = GETPOST('fk_jobsdet','int')+0;
			$objectdet->fk_structure = GETPOST('fk_structure','int')+0;
			$objectdet->fk_user_mod = $user->id;
			$objectdet->date_mod = dol_now();
			$objectdet->tms = dol_now();

			if ($objectdet->update($user) > 0)
			{
				$action = '';
				$_GET["id"] = $_POST["id"];
				setEventMessages($langs->trans('Successfullupdate'),null,'mesgs');
				header("Location: fiche.php?id=".$objectdet->fk_almacen);
				exit;
			}
			else
			{
				$action = 'moditem';
				$_GET["id"] = $_POST["id"];
				setEventMessages($objectdet->error,$objectdet->errors,'errors');
			}
		}
		else
		{
			$action = 'moditem';
			$_GET["id"] = $_POST["id"];
			setEventMessages($objectdet->error,$objectdet->errors,'errors');
		}
	}

	//actualiza el pedido de almacen y crea la salida de almacenes
	if ($action == 'confirm_addDeliver' && $user->rights->almacen->pedido->ent)
	{
		$mesg = '';
		$object = new Solalmacenext($db);
		$object->fetch(GETPOST("id"));
		$aPost = unserialize($_SESSION['aPost']);
		$_POST = $aPost[$id];
		if ($object->statut == 6)
		{
			$objectdet = new Solalmacendetext($db);
			$fk_almacen = GETPOST("id");
			$aRowItem   = GETPOST('qty_livree');
			$aEntrepot = GETPOST('entrepot');
			$error = 0;
			$db->begin();
			//inicializamos la actualizacion

			foreach($aRowItem AS $rowid => $qty)
			{
				if ($qty>0)
				{
					$qty = price2num($qty,'MS');
					if ($objectdet->fetch($rowid))
					{
						//buscamos el precio unitario del producto
						$objProduct->fetch($objectdet->fk_product);
						$objProduct->load_stock();
						$pmp = 0;
						$fk_entrepot = $aEntrepot[$rowid];
						if ($fk_entrepot <=0)
						{
							$error++;
							setEventMessages($langs->trans('El producto').' '.$objProduct->ref.' '.$objProduct->label.' '.$langs->trans('No esta asignado un almacen'),null,'errors');
						}
						if ($objProduct->stock_warehouse[$fk_entrepot]->pmp)
							$pmp = $objProduct->stock_warehouse[$fk_entrepot]->pmp;
						else
							$pmp = $objProduct->pmp;
						$aSales = array();

						//valuacion por el metodo peps
						$objMouvement = new MouvementStockext($db);
						//echo '<hr>typemet '.$typemethod;
						$res = $objMouvement->method_valuation($fk_entrepot,dol_now(),$objectdet->fk_product,($typemethod>0?1:''));
						$aIng = $objMouvement->aIng;
						$aSal = $objMouvement->aSal;
						//vamos a verificar los saldos de cada ingreso
						$aIngtmp = $aIng;
						$aBalancetmp= array();
						foreach ($aIngtmp AS $jtmp => $datatmp)
						{
							$aBalancetmp[$jtmp]=$datatmp->value;
							foreach ($aSal AS $ktmp => $datasal)
							{
								if ($datasal->fk_parent_line == $jtmp)
									$aBalancetmp[$jtmp]+=$datasal->value;
							}
						}
						//echo '<hr>balance';
						//print_r($aBalancetmp);
						//actualizamos valor para realizar la salida
						foreach ($aIngtmp AS $jtmp => $data)
						{
							$aIng[$jtmp]->balance_peps= $aBalancetmp[$jtmp];
						}
						//echo 'NUEVO<pre>';
						//print_r($aIng);
						//echo '</pre>';
						//recorremos los ingresos para realizar la salida correspondiente
						$qtysal = $qty;
						$qtyent = 0;
						if (count($aIng)==0 && empty($typemethod))
						{
							//ECHO 'ING0';
							if (!$conf->global->STOCK_ALLOW_NEGATIVE_TRANSFER)
							{
								if($objProduct->stock_warehouse[$fk_entrepot]->real < $qty)
								{
									$error=100;
									setEventMessages($langs->trans('No existe saldo suficiente'),null,'errors');
								}
								else
								{
									$aSales[0]['value'] = $pmp;
									$aSales[0]['qty'] = $qty;
								}
							}
							else
							{
								$aSales[0]['value'] = $pmp;
								$aSales[0]['qty'] = $qty;
							}
						}
						else
						{
							//ECHO 'CONING parasalida '.$qtysal;
							$lEntrega = true;
							foreach ((array) $aIng AS $j => $lineing)
							{
								if ($lEntrega)
								{
									if (empty($conf->global->ALMACEN_METHOD_VALUATION_INVENTORY))
									{
										if (!$conf->global->STOCK_ALLOW_NEGATIVE_TRANSFER)
										{

											if($objProduct->stock_warehouse[$fk_entrepot]->real < $qty)
											{
												$error=101;
												setEventMessages($langs->trans('No existe saldo suficiente'),null,'errors');
											}
											else
											{
												$aSales[0]['value'] = $pmp;
												$aSales[0]['qty'] = $qty;
												$qtyent = $qty;
											}
										}
										else
										{
											$aSales[0]['value'] = $pmp;
											$aSales[0]['qty'] = $qty;
											$qtyent = $qty;
										}
									}
									elseif ($conf->global->ALMACEN_METHOD_VALUATION_INVENTORY==1)
									{
										//ECHO '<HR>VERIFICALINEA '.$j.' '.$lineing->balance_peps.' para una salida de '.$qtysal;
										if ($lineing->balance_peps > 0)
										{
											$value_peps = ($lineing->value_peps_adq?$lineing->value_peps_adq:$lineing->value_peps);
											if ($lineing->balance_peps >= $qtysal)
											{
												$aSales[$lineing->id]['value'] = $value_peps;
												$aSales[$lineing->id]['qty'] = $qtysal;
												//////////////////////////////////////////////////////////////////
												$aSales[$lineing->id]['id'] = $lineing->id;
												$qtyent += $qtysal;
												$lEntrega =false;
												//actualizamos el saldo en stock_mouvement
												$resmadd = $objMouvementadd->fetch(0,$lineing->id);
												$objMouvementadd->balance_peps -= $qtysal;
												$resmadd = $objMouvementadd->update($user);
												if ($resmadd<=0)
												{
													$error=102;
													setEventMessages($objMouvementadd->error,$objMouvementadd->errors,'errors');
												}
											}
											else
											{
												$aSales[$lineing->id]['value'] = $value_peps;
												$aSales[$lineing->id]['qty'] = $lineing->balance_peps;
												$aSales[$lineing->id]['id'] = $lineing->id;
												$qtysal-=$lineing->balance_peps;
												$qtyent+=$lineing->balance_peps;
												//actualizamos el saldo en stock_mouvement
												$resmadd = $objMouvementadd->fetch(0,$lineing->id);
												$objMouvementadd->balance_peps -= $lineing->balance_peps;
												$resmadd = $objMouvementadd->update($user);
												if ($resmadd<=0)
												{
													$error=103;
													setEventMessages($objMouvementadd->error,$objMouvementadd->errors,'errors');
												}
											}
										}
									}
								}
								continue;
							}
							//echo '<br>qty '.number_format($qty,15).' qtyent '.number_format($qtyent,15);
							//echo '<br>nuevo '.$qty .'!='. price2num($qtyent,'MS');
							if ($qty != price2num($qtyent,'MS'))
							{

								$error=104;
								if ($qty > $qtyent)
								{
									setEventMessages($langs->trans('NO existe saldo en almacen para cubrir la entrega de- ').' '.$objProduct->ref.' '.$objProduct->label,null,'errors');
								}
								else
								{
									setEventMessages($langs->trans('Se esta entregando en demasia').' '.$objProduct->ref.' '.$objProduct->label,null,'errors');
								}
							}
						}
						//echo 'SALIDAS<pre>';
						//print_r($aSales);
						//echo '</pre>';
						//exit;
						if (!$error)
						{
							$objectdet->qty_livree = $qty;
							$objectdet->fk_user_mod = $user->id;
							$objectdet->price = $pmp;
							$objectdet->fk_entrepot = $fk_entrepot+0;
							$objectdet->date_mod = dol_now();
							$objectdet->tms = dol_now();

							$qtyt = $qty;
							if ($qty <>0)
							{
								if ( $objectdet->update($user) > 0)
								{
									//creamos el movimiento de stock con numeración
									//salida de producto
									foreach ($aSales AS $fk_stock => $row)
									{
										$type = 1;
										$qtyt = $qty * -1;
										$qtyt = $row['qty'] * -1;
										$label = $langs->trans("ShipmentAccordingtoOrder")." ".$object->ref;
										$objMouvement = new MouvementStockext($db);
										$objMouvement->origin->element = ($objectdet->element?$objectdet->element:'solalmacendet');
										$objMouvement->origin->id = $rowid;
										$result = $objMouvement->_createadd($user,$objectdet->fk_product,$fk_entrepot,$qtyt,$type,$pmp,$label);
										if ($result < 0 || $result == -1 || $result == 0)
										{
											$error=201;
											setEventMessages($objMouvement->error,$objMouvement->errors,'errors');
										}
										else
										{
											//agregamos en la tabla adicional stock_mouvement_add si no existe o actualizamos
											$resadd = $objMouvementadd->fetch(0,$result);
											if ($resadd == 0)
											{
												$now = dol_now();
												$aDate = dol_getdate($now);
												$price_peps = $row['value'];

												$objMouvementadd->fk_stock_mouvement = $result;
												$objMouvementadd->period_year = $_SESSION['period_year']+0;
												$objMouvementadd->month_year = $_SESSION['period_month']+0;

												$objMouvementadd->fk_stock_mouvement_doc = 0;
												$objMouvementadd->fk_facture = 0;
												$objMouvementadd->fk_user_create = $user->id;
												$objMouvementadd->fk_user_mod = $user->id;
												//$objMouvementadd->fk_parent_line = $fk_stock;
												$objMouvementadd->date_create = $now;
												$objMouvementadd->date_mod = $now;
												$objMouvementadd->tms = $now;
												$objMouvementadd->fk_parent_line = $row['id']+0;
												$objMouvementadd->qty = 0;
												$objMouvementadd->balance_peps = 0;
												$objMouvementadd->balance_ueps = 0;
												$objMouvementadd->value_peps = $row['value'];
												$objMouvementadd->value_ueps = $price_ueps+0;
												$objMouvementadd->status = 1;
												$resadd = $objMouvementadd->create($user);
												if ($resadd <=0)
												{
													setEventMessages($objMouvementadd->error,$objMouvementadd->errors,'errors');
													$error=202;
												}
											}
											elseif ($resadd == 1)
											{
												$now = dol_now();
												//$objMouvementadd->fk_stock_mouvement = $result;
												$objMouvementadd->fk_stock_mouvement_doc = 0;
												$objMouvementadd->fk_facture = 0;
												$objMouvementadd->fk_user_mod = $user->id;
												//$objMouvementadd->fk_parent_line = $fk_stock;
												$objMouvementadd->fk_parent_line = $row['id']+0;
												$objMouvementadd->date_mod = $now;
												$objMouvementadd->tms = $now;
												$objMouvementadd->balance_peps = 0;
												$objMouvementadd->balance_ueps = 0;
												$objMouvementadd->qty = 0;
												$objMouvementadd->value_peps = $row['value'];
												$objMouvementadd->value_ueps = $price_ueps+0;
												$objMouvementadd->status = 1;
												$resadd = $objMouvementadd->update($user);
												if ($resadd <=0)
												{
													setEventMessages($objMouvementadd->error,$objMouvementadd->errors,'errors');
													$error=203;
												}

											}
											if (!$error)
											{
												//registramos en mantenimiento si esta habilitado
												if ($conf->mant->enabled)
												{
													require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsresource.class.php';
													$objJobsresource = new Mjobsresource($db);

													if ($objectdet->fk_jobs>0)
													{
														$ref = '';
														$filterres = " AND t.fk_jobs = ".$objectdet->fk_jobs;
														//recuperamos el ultimo numero
														$res = $objJobsresource->fetchAll('DESC','ref', 0,0,array(1=>1),'AND',$filterres,true);
														if ($res == 1)
															$ref = $objJobsresource->ref + 1;
														elseif($res>1)
														{
															foreach ($objJobsresource->lines AS $j => $line)
															{
																if (empty($ref))
																	$ref = $line->ref + 1;
																else
																	continue;
															}
														}
														else
															$ref = 1;
														$objJobsresource->fk_jobs = $objectdet->fk_jobs;
														$objJobsresource->fk_jobs_program = $objectdet->fk_jobsdet+0;
														$objJobsresource->ref = $ref;
														$objJobsresource->fk_sol_almacen = $objectdet->fk_almacen;
														$objJobsresource->fk_sol_almacendet = $objectdet->rowid;
														$objJobsresource->fk_product = $objectdet->fk_product;
														$objJobsresource->dater = $objectdet->tms;
														//valor fijo para costo materia prima
														$objJobsresource->type_cost = 'MPD';
														$objJobsresource->description = $objectdet->description;
														$objJobsresource->quant = $objectdet->qty_livree;
														$objJobsresource->fk_unit = $objectdet->fk_unit;
														$objJobsresource->price = $objectdet->price;
														$objJobsresource->fk_user_create = $user->id;
														$objJobsresource->fk_user_mod = $user->id;
														$objJobsresource->datec = dol_now();
														$objJobsresource->datem = dol_now();
														$objJobsresource->tms = dol_now();
														$objJobsresource->status = 1;
														$resjr = $objJobsresource->create($user);
														if ($resjr <=0)
														{
															$error=204;
															setEventMessages($objJobsresource->error,$objJobsresource->errors,'errors');
														}
													}
												}
											}
										}
									}
									//sigue procesando
									if ($object->fk_entrepot_from>0)
									{
										//entrada de producto
										foreach ($aSales AS $fk_stock => $row)
										{
											$type = 0;
											$qtyt = $qty;
											$qtyt = $row['qty'];
											$label = $langs->trans("ShipmentAccordingfromOrder")." ".$object->ref;
											$objMouvement = new MouvementStockext($db);
											$objMouvement->origin->element = ($objectdet->element?$objectdet->element:'solalmacendet');
											$objMouvement->origin->id = $rowid;

											$result = $objMouvement->_create($user,$objectdet->fk_product,
												$object->fk_entrepot_from,
												$qtyt,$type,$pmp,$label);
											if ($result == -1 || $result == 0)
											{
												$error=210;
												$mesg.= '<div class="error">'.$objMouvement->error.'</div>';
											}
											else
											{
												//agregamos en la tabla adicional stock_mouvement_add
												$now = dol_now();
												$aDate = dol_getdate($now);
									//agregamos en la tabla adicional stock_mouvement_add si no existe o actualizamos
												$resadd = $objMouvementadd->fetch(0,$result);
												if ($resadd == 0)
												{

													$objMouvementadd->fk_stock_mouvement = $result;
													$objMouvementadd->period_year = $_SESSION['period_year']+0;
													$objMouvementadd->month_year = $_SESSION['period_month']+0;

													$objMouvementadd->fk_stock_mouvement_doc = $resdoc+0;
													$objMouvementadd->fk_facture = 0;
													$objMouvementadd->fk_user_create = $user->id;
													$objMouvementadd->fk_user_mod = $user->id;
									//$objMouvementadd->fk_parent_line = $fk_stock;
													$objMouvementadd->fk_parent_line = $row['id'];
													$objMouvementadd->date_create = $now;
													$objMouvementadd->date_mod = $now;
													$objMouvementadd->tms = $now;
													$objMouvementadd->qty = 0;
													$objMouvementadd->balance_peps = 0;
													$objMouvementadd->balance_ueps = 0;
													$objMouvementadd->value_peps = $row['value'];
													$objMouvementadd->value_ueps = $price_ueps+0;
													$objMouvementadd->status = 1;
													$resadd = $objMouvementadd->create($user);
													if ($resadd <=0)
													{
														setEventMessages($objMouvementadd->error,$objMouvementadd->errors,'errors');
														$error=211;
													}
												}
												elseif($resadd == 1)
												{
													$objMouvementadd->period_year = $_SESSION['period_year']+0;
													$objMouvementadd->month_year = $_SESSION['period_month']+0;

													$objMouvementadd->fk_stock_mouvement_doc = $resdoc+0;
													$objMouvementadd->fk_facture = 0;
													$objMouvementadd->fk_user_create = $user->id;
													$objMouvementadd->fk_user_mod = $user->id;
									//$objMouvementadd->fk_parent_line = $fk_stock;
													$objMouvementadd->fk_parent_line = $row['id'];
													$objMouvementadd->date_create = $now;
													$objMouvementadd->date_mod = $now;
													$objMouvementadd->tms = $now;
													$objMouvementadd->qty = 0;
													$objMouvementadd->balance_peps = 0;
													$objMouvementadd->balance_ueps = 0;
													$objMouvementadd->value_peps = $row['value'];
													$objMouvementadd->value_ueps = $price_ueps+0;
													$objMouvementadd->status = 1;
													$resadd = $objMouvementadd->update($user);
													if ($resadd <=0)
													{
														setEventMessages($objMouvementadd->error,$objMouvementadd->errors,'errors');
														$error=212;
													}

												}
											}
										}
									}
					//sigue procesando
								}
								else
								{
									$error=99;
									$action = 'deliverr';
									$_GET["id"] = $_POST["id"];
									setEventMessages($objectdet->error,$objectdet->errors,'errors');
								}
							}
						}
					}
					else
					{
				//analizar si no existe el producto
					}
				}
			}
			if (!$error)
			{
				$object->statut = 2;
				$object->fk_user_ent = $user->id;
				$object->datee = $now;
				$res = $object->update($user);
				if ($res <= 0)
				{
					$error++;
					setEventMessages($object->error,$object->errors,'errors');
				}
			}
			if (!$error)
			{
				$objSollog->fk_solalmacen = $id;
				$objSollog->status = $object->statut;
				$objSollog->description = $aLog[$object->statut];
				$objSollog->fk_user_create = $user->id;
				$objSollog->fk_user_mod = $user->id;
				$objSollog->datec = $now;
				$objSollog->datem = $now;
				$objSollog->tms = $now;
				$res = $objSollog->create($user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($objSollog->error,$objSollog->errors,'errors');
				}
			}
			//echo '<hr>sedetiene';exit;
			if (empty($error))
			{
				$db->commit();
				setEventMessages($langs->trans('Entrega satisfactoria'),null,'mesgs');
			//generamos el documento
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
					$ret = $object->fetch($id);
					$model=$object->model_pdf;
					// Reload to get new records
					$object->fetch_lines();
					$result=$object->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);
					if ($result < 0) dol_print_error($db,$result);
				}

				$result=almacen_pdf_create($db, $object, $object->model_pdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);

				header("Location: ".$_SERVER['PHP_SELF']."?id=".$object->id);
				exit;
			}
			else
			{
				$action = 'deliver';
				setEventMessages($object->error,$object->errors,'errors');
				$db->rollback();
			}
		}
	}

	if ($_POST["cancel"] == $langs->trans("Cancel"))
	{
		$action = '';
		$id = GETPOST("id");
	}

//vamos a armar si corresponde la lista de almacen quien solicita
	$aEntrepotfrom = array();
	if (count($aFilterent)>0)
	{
		foreach ($aFilterent AS $j)
		{
			$res = $entrepot->fetch($j);
			if ($res)
				$aEntrepotfrom[$entrepot->id] = $entrepot->lieu;
		}
	}
/*
 * View
 */

$productstatic=new Product($db);
$form=new Formv($db);
$getUtil = new getUtil($db);
$formcompany=new FormCompany($db);
if ($conf->projet->enabled && $conf->monprojet->enabled)
	$formproject = new FormProjetsext($db);

$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';
//llxHeader("",$langs->trans("ApplicationsWarehouseCard"),$help_url);
$morejs=array("/almacen/javascript/almacen.js");
llxHeader('',$langs->trans("ApplicationsWarehouseCard"),$help_url,'','','',$morejs,'',0,0);

if (!$lAddnew)
{
	setEventMessages($langs->trans('It is not allowed to carry out movements in this management'),null,'warnings');
}

if (! empty($conf->use_javascript_ajax))
{
	print "\n".'<script type="text/javascript">';
	print '$(document).ready(function () {
		$("#fk_entrepot").change(function() {
			document.formalm.action.value="'.$action.'";
			document.formalm.submit();
		});
	});';
	print '</script>'."\n";
}
if ($action == 'create' && $user->rights->almacen->pedido->write && $lAddnew)
{
	//verificando la existencia de pedido
	$fk_entrepotsol = 0;
	$lViewUnic = false;
	$text = GETPOST('description');
	if ($fk_commande)
	{
		$sql   = "SELECT fk_entrepot FROM ".MAIN_DB_PREFIX."commande_venta";
		$sql  .= " WHERE fk_commande = '".$fk_commande."'";
		$rsql  = $db->query($sql);
		$objcv = $db->fetch_object($rsql);
		$fk_entrepotSol = $objcv->fk_entrepot;
	}

	if (GETPOST('origin') && GETPOST('originid'))
	{
		if (GETPOST('origin') == 'projet')
		{
			//require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projectext.class.php';
			//$projet = new Projetext($db);

			$element = 'monprojet';
			$subelement = 'projectext';
			$othersubelement = 'projetadd';
			dol_include_once('/'.$element.'/class/'.$subelement.'.class.php');
			dol_include_once('/'.$element.'/class/'.$othersubelement.'.class.php');
			$classname = ucfirst($subelement);
			$otherclassname = ucfirst($othersubelement);
			$objectsrc = new $classname($db);
			$objectothersrc = new $otherclassname($db);

			$extralabels_projet=$extrafields->fetch_name_optionals_label($objectsrc->table_element);

			$objectsrc->fetch(GETPOST('originid'));
			$objectothersrc->fetch(0,GETPOST('originid'));
			$fk_entrepot = $objectothersrc->fk_entrepot;
			$res=$objectsrc->fetch_optionals($projet->id,$extralabels_projet);
			$fk_projet = $objectsrc->id;
			if (empty($text)) $text = $langs->trans('Solicitud de material para proyecto').' '.$objectsrc->title;
			//verificamos si tiene almacen de obra
			if ($objectsrc->array_options['options_use_resource']>=1)
			{
				//buscamos la lista de almacenes
				$filterstatic = " AND t.fk_projet = ".$fk_projet;
				$res = $objectUrqEntrepot->fetchAll('', '',0,0,array(1=>1),'AND',$filterstatic);
				if ($res>0)
				{
					$lines = $objectUrqEntrepot->lines;
					if (count($lines) == 1) $lViewunic = true;
					foreach ($lines AS $j => $line)
					{
						if (!empty($idIncluded)) $idIncluded.=',';
						$idIncluded.= $line->id;
					}
				}
			}
		}
	}

	print_fiche_titre($langs->trans("NewApplicationsEntrepot"));

	print '<form name="formalm" action="'.$_SERVER["PHP_SELF"].'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="date_creation" value="'.date('Y-m-d').'">';
	print '<input type="hidden" name="type" value="'.$type.'">'."\n";
	print '<input type="hidden" name="fk_entrepotsol" value="'.$fk_entrepotsol.'">'."\n";

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

	// Ref
	print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">'.$langs->trans("Draft").'</td></tr>';
	// print '<tr><td width="25%" class="fieldrequired">'.$langs->trans("Ref").'</td><td colspan="3"><input name="ref" size="12" value=""></td></tr>';
	if (empty(GETPOST('origin')) && empty(GETPOST('originid')))
	{
		if ($user->admin)
		{
			/*
			print '<tr>';
			print '<td width="20%" >'.$langs->trans("Entrepotfrom").'</td><td width="20%">';
			print $formproduct->selectWarehouses(($_GET["dwid"]?$_GET["dwid"]:GETPOST('id_entrepot_source')),'id_entrepot_source','',1);
			print '</td>';
			print '</tr>';
			/*/
		}
		else
		{
			if (count($aEntrepotfrom)>0)
			{
				print '<tr>';
				print '<td width="20%" >'.$langs->trans("Entrepotfrom").'</td><td width="20%">';
				print $form->selectarray('id_entrepot_source',$aEntrepotfrom,GETPOST('id_entrepot_source'),1);

				print '</td>';
				print '</tr>';
			}

			/*
			$lSelentrepotfrom = true;
			if (count($aFilterent)==1)
			{
				foreach ($aFilterent AS $fk_entrepot)
				{
					$entrepot->fetch($fk_entrepot);
					if ($entrepot->libelle == $conf->global->ALMACEN_CODE_DEFAULT_STORE)
						$lSelentrepotfrom = false;
				}

			}
			//armamos una lista de entrepot
			if (count($aFilterent)>0 && $lSelentrepotfrom)
			{
				print '<tr>';
				print '<td width="20%" >'.$langs->trans("Entrepotfrom").'</td><td width="20%">';
				$checked = '';
				$idExcluded = '';
				if (count($aFilterent) == 1)
				{
					$checked = 'checked="checked"';
					$idExcluded = $filteruser;
				}
				foreach ($aFilterent AS $fk_entrepot)
				{
					$entrepot->fetch($fk_entrepot);
					print '<p>'.$entrepot->lieu.' <input type="radio" '.$checked.' name="id_entrepot_source" value="'.$fk_entrepot.'">'.'</p>';
				}
				print '</td>';
				print '</tr>';
			}
			*/
		}
		//modificamos para que no pida almacen origen y destino
		//print ' <input type="hidden" name="id_entrepot_source" value="0">';
	}
	else
	{
		print '<input type="hidden" name="id_entrepot_source" value="0">';
	}

	// Solicitado a Almacen

	print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Deliveryby').'</td><td colspan="3">';

	$filterstatic = " AND fk_entrepot_father <=0";
	$res = $objectUrqEntrepot->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic);
	$ids;
	if ($res>0)
	{
		foreach ($objectUrqEntrepot->lines AS $j => $line)
		{
			$lAdd = true;
			if (!$user->admin)
			{
				if (empty($aFilterentsol[$line->id]))
					$lAdd = false;
			}
			if ($lAdd)
			{
				if ($ids) $ids.= ',';
				$ids.= $line->id;
			}
		}
	}
	$filterstatic = " AND t.rowid IN (".$ids.") AND t.statut = 1 ";
	if (!$user->admin && $filterusersol)
		$filterstatic.= " AND t.rowid IN (".$filterusersol.") AND t.statut = 1 ";

	$resent = $entrepot->fetchAll('ASC', 'label', 0,0, array(1=>1), 'AND', $filterstatic, false);
	if ($resent>0)
	{
		foreach ($entrepot->lines AS $j => $line)
		{
			$selected = '';
			if (GETPOST('fk_entrepot') && $line->id == GETPOST('fk_entrepot')) $selected = ' selected';
			$optionsent.= '<option value="'.$line->id.'" '.$selected.'>'.$line->lieu.'('.$line->label.')'.'</option>';
		}
	}
	print '<select id="fk_entrepot" name="fk_entrepot">'.$optionsent.'</select>';
	if (isset($_POST['fk_entrepot']))
	{
		//vamos a comparar el almacen solicitante y almacen de donde se pide
		if (GETPOST('fk_entrepot') == GETPOST('id_entrepot_source'))
		{
			$error++;
			setEventMessages($langs->trans('Thewarehousesmustbedifferent'),null,'errors');
		}
	}

	//if (!$user->admin)
	//	$idIncluded = $filterusersol;
	//print $objectUrqEntrepot->select_padre(($fk_entrepot?$fk_entrepot:$conf->global->ALMACEN_CODE_DEFAULT_STORE),'fk_entrepot','',$idExcluded,$idIncluded,($lViewunic?0:1));

	//print '<input type="hidden" name="fk_entrepot" value="0">';
	//}
	//else
	//{
	//	$code_entrepot = $conf->global->ALMACEN_CODE_DEFAULT_STORE;
	//	$entrepot->fetch($fk_entrepot,$code_entrepot);
	//	if ($entrepot->id>0)
	//	{

	//		print $entrepot->getNomUrl(1);
	//		print '<input type="hidden" name="fk_entrepot" value="'.$entrepot->id.'">';
	//	}
	//	else
	//		print 'no definido';
	//}
	print '</td></tr>';

	// Fabrication
	if ($conf->fabrication->enabled)
	{
		print '<tr><td width="25%">'.$langs->trans('OrderProduction').'</td><td colspan="3">';
		if ($fk_fabrication)
		{
			print '<input type="hidden" name="fk_fabrication" value="'.$fk_fabrication.'">';
			$formfabrication->fetch($fk_fabrication);
			print $formfabrication->ref;
		}
		else
		{
			print $formfabrication->select_fabrication($fk_fabrication,'fk_fabrication','',!$disabled,!$disabled,1);
		}
		print '</td></tr>';
	}
	//solicitante
	$exclude = array();
	print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Applicant').'</td><td colspan="3">';
	if ($user->admin)
		print $form->select_dolusers((empty($object->fk_user)?$user->id:$object->fk_user),'fk_user',1,$exclude,0,'','',$object->entity);
	else
	{
		print $user->login;
	}
	print '</td></tr>';

	//area solicitante
	print '<tr><td width="25%">'.$langs->trans('Departament').'</td><td colspan="3">';
	if ($user->admin)
		print $form->select_departament(GETPOST('fk_departament'),'fk_departament','',0,1);
	else
	{

		if ($conf->orgman->enabled)
		{
			$filter = " AND t.rowid IN (".$filterarea.")";
			$resdep = $objDepartament->fetchAll('ASC','label',0,0,array('entity'=>$conf->entity,'active'=>1,'status'=>1), 'AND',$filter);
			$options = '';
			if ($resdep>0)
			{
				foreach($objDepartament->lines AS $j => $line)
				{
					$options.= '<option value="'.$line->id.'">'.$line->label.'</option>';
				}
			}
			print '<select name="fk_departament">'.$options.'</select>';
		}
		else
		{
			if (!empty($user->array_options['options_fk_departament']))
			{

				$getUtil->fetch_departament($user->array_options['options_fk_departament'],'');
				print $getUtil->label;
				print '<input type="hidden" name="fk_departament" value="'.$user->array_options['options_fk_departament'].'">';
			}
			else
				print $langs->trans('NotDefined');
		}
	}
	print '</td></tr>';

	if ($conf->projet->enabled && $conf->monprojet->enabled)
	{
		print '<tr><td>'.$langs->trans("Project").'</td><td>';
		if (!$fk_projet)
		{
			$filterkey = '';
			$numprojet = $formproject->select_projects(($user->societe_id>0?$soc->id:-1), $object->fk_projet, 'fk_projet', 0,0,1,0,0,0,0,$filterkey);
		}
		else
		{
			print $objectsrc->getNomUrladd(1);
			print '<input type="hidden" name="fk_projet" value="'.$fk_projet.'">';
		}
		print '</td></tr>';
	}

	//date creation
	print '<tr><td width="25%">'.$langs->trans("Date").'</td><td colspan="3">';
	print dol_print_date(dol_now(),'dayhour');
	print '</td></tr>';

	//date delivery
	// Date de livraison
	if ($conf->global->ALMACEN_REGISTER_DATEDELIVERY)
	{
		print '<tr><td width="25%" class="fieldrequired">'.$langs->trans("Requirementsdate").'</td><td colspan="2">';
		if (empty($datedelivery))
		{
			if (! empty($conf->global->DATE_LIVRAISON_WEEK_DELAY)) $datedelivery = time() + ((7*$conf->global->DATE_LIVRAISON_WEEK_DELAY) * 24 * 60 * 60);
			else $datedelivery=empty($conf->global->MAIN_AUTOFILL_DATE)?-1:0;
			}
			$form->select_date($datedelivery,'re','','','',"crea_commande",1,1);
			print "</td></tr>";
		}
		//description
		print '<tr><td width="25%" class="field">'.$langs->trans("Description").'</td><td colspan="3">';
		print '<textarea wrap="soft" name="description" rows="3" cols="40">'.$text.'</textarea>';
		print '</td></tr>';

		print '</table>';
		if (!$error)
			print '<center><br><input type="submit" class="button" value="'.$langs->trans("New").'"></center>';

		print '</form>';
	}
	else
	{
		if ($id)
		{
			$_SESSION['cargaFabrication'][$_GET['id']]++;
			dol_htmloutput_mesg($mesg);
			$lAdditem = true;
			$object = new Solalmacenext($db);
			$result = $object->fetch($id);
			$object->fetch_lines();
			if ($result < 0)
			{
				dol_print_error($db);
			}
		//verificamos si tiene orden de producttion
			if ($object->fk_fabrication > 0)
			{
			//buscamos si existe pedidos a almacen con orden de produccion
				$res = $object->getlist_op($object->fk_fabrication);
			//echo ' res '.$res .' >0 && '.count($object->linealm).' > 0';
				if ($res > 0 && count($object->linealm) > 0)
					$lAdditem = false;
			}
		//verificamos que pertenezca al usuario
			if (!$user->admin && $user->id != $object->fk_user_create)
			{
				$error=0;
				if (!$aFilterarea[$object->fk_departament])
				{
					$error++;
				}
				if ($user->rights->almacen->pedido->val)
				{
					$aDepartamentval = $objDepartament->verif_accessresp($user->fk_member);
					if (!empty($aAreadirect))
					{
						foreach ($aAreadirect AS $j)
							$aDepartamentval[$j] = $j;
						$error=0;
						if (!$aDepartamentval[$object->fk_departament]) $error+=301;
					}
				}
				if($user->rights->almacen->pedido->ent)
				{
					$error=0;
				//echo $object->fk_entrepot;
					if (!$aFilterent[$object->fk_entrepot] ) $error=401;
				}
				if ($user->rights->almacen->pedido->app)
				{
					$error=0;
					if (!$aFilterarea[$object->fk_departament])
					{
						$error=501;
					}
				}
				if ($user->rights->almacen->pedido->appall)
					$error = 0;
				if ($error)
				{
					print $mesg = '<div class="error">'.$langs->trans('No esta permitido para ver'). $error.'</div>';
					exit;
				}
			}
			$objectdet = new Solalmacendetext($db);
			$objectDetFab = new Solalmacendetfabricationext($db);
		//incluimos si corresponde
			$aArrayItem = $objectdet->list_item($_GET["id"]);
			if ($lAdditem && empty($aArrayItem))
			{
				include DOL_DOCUMENT_ROOT.'/almacen/includes/addlines.php';
				$aArrayItem = $objectdet->list_item($_GET["id"]);
			}

			if (count($aArrayItem) <=0 || $_SESSION['cargaFabrication'][$_GET['id']] <=1)
			{
			//include DOL_DOCUMENT_ROOT.'/almacen/includes/addlines.php';
			//$aArrayItem = $objectDet->list_item($_GET["id"]);
			}
			$numLinesItem = count($aArrayItem);

			//$commande = new Commande($db);
			$aItemf = array();
			$objEntrepot = new Entrepot($db);
			if ($conf->fabrication->enabled)
			{
				$objFabrication = new Fabricationext($db);
				if ($object->fk_fabrication>0)
				{
					$objFabrication->fetch($object->fk_fabrication);
					$objFabrication->fetch_lines();
					if ($objFabrication->lines)
					{
						foreach ($objFabrication->lines AS $j => $line)
						{
							$aItemf[$line->rowid] = $line->libelle;
						}
					}
				}
			}

			// Affichage fiche
			if ($action <> 'edit' && $action <> 're-edit')
			{

				$head = solalmacen_prepare_head($object);
				dol_fiche_head($head, 'card', $langs->trans("Warehouse orders"), 0, DOL_URL_ROOT.'/almacen/img/order.png',1);

			// Confirm change status deliver
				if ($action == 'changestatusval')
				{
					$_SESSION['aPost'] = $_POST;
				//$form = new Form($db);
					$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Changestatustodeliverable"),$langs->trans("ConfirmChangestatustodeliverable",$object->libelle).' '.$object->ref,"confirm_changestatusval",'',1,2);
					if ($ret == 'html') print '<br>';
				}

			// Confirm addDeliver
				if ($action == 'addDeliver')
				{
					$aPost[$id] = $_POST;
					$_SESSION['aPost'] = serialize($aPost);
				//$form = new Form($db);
					$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Confirmdelivery"),$langs->trans("ConfirmDeliveryWarehouseorder",$object->libelle).' '.$object->ref,"confirm_addDeliver",'','yes',2);
					if ($ret == 'html') print '<br>';
				}

			// Confirm noexist
				if ($action == 'noexist')
				{
				//$form = new Form($db);
					$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Mark out of stock"),$langs->trans("Confirm to mark the order as non-existent",$object->libelle).' '.$object->ref,"confirm_noexist",'','yes',2);
					if ($ret == 'html') print '<br>';
				}

			//confirm validate
				if ($action == 'validate' && $user->rights->almacen->pedido->val)
				{
					$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Confirmvalidate"),$langs->trans("ConfirmValidateWarehouseorder",$object->libelle).' '.$object->ref,"confirm_validate",'',1,2);
					if ($ret == 'html') print '<br>';
				}
			//confirm approved
				if ($action == 'approved')
				{
					$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("ConfirmApprove"),$langs->trans("ConfirmApproveWarehouseorder",$object->libelle).' '.$object->ref,"confirm_approved",'',1,2);
					if ($ret == 'html') print '<br>';
				}
			// Confirm delete third party
				if ($action == 'delete')
				{
				//$form = new Form($db);
					$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deleteorder"),$langs->trans("ConfirmDeleteorder",$object->libelle),"confirm_delete",'',0,2);
					if ($ret == 'html') print '<br>';
				}
				if ($action == 'anulate')
				{
				//$form = new Form($db);
					$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Cancelorder"),$langs->trans("ConfirmCancelorder",$object->libelle),"confirm_anulate",'',0,2);
					if ($ret == 'html') print '<br>';
				}
			// Confirm reject third party
				if ($action == 'reject')
				{
				//$form = new Form($db);
					$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Rejectorder"),$langs->trans("ConfirmRejectorder",$object->libelle),"confirm_reject",'',0,2);
					if ($ret == 'html') print '<br>';
				}

				print '<table class="border" width="100%">';

		  // Ref
				print '<tr><td width="25%">'.$langs->trans("Ref").'</td><td colspan="3">';
				print $object->ref;
				print '</td></tr>';

			//Entrepot source
			/*
			$objEntrepot->fetch($object->fk_entrepot_from);
			if ($object->fk_entrepot_from>0)
			{
				print '<tr><td>'.$langs->trans("Entrepotfrom").'</td><td colspan="3">';
				print $objEntrepot->libelle." - ".$objEntrepot->lieu;
				print '</td></tr>';
			}
			//else
			//{
			//	print $langs->trans('Sin almacen destino');
			//}

			//Entrepot
			$objEntrepot->fetch($object->fk_entrepot);
			print '<tr><td>'.$langs->trans("Deliver by").'</td><td colspan="3">';
			print $objEntrepot->getNomUrl(1);
			print '</td></tr>';
			*/
			// Fabrication
			if ($conf->fabrication->enabled)
			{
				$objFabrication->fetch($object->fk_fabrication);
				print '<tr><td width="25%">'.$langs->trans('OrderProduction').'</td><td colspan="3">';
				print $objFabrication->ref;
				print '</td></tr>';
			}
		  //user
			$objuser->fetch($object->fk_user);
			print '<tr><td width="25%">'.$langs->trans('Applicant').'</td><td colspan="3">';
			if ($objuser->id == $object->fk_user)
				print $objuser->getNomUrl(1);
			else
				print '&nbsp;';
			print '</td></tr>';

			if ($conf->projet->enabled && $conf->monprojet->enabled)
			{
				$projet = new Projectext($db);
				$projet->fetch($object->fk_projet);
				print '<tr><td>'.$langs->trans("Project").'</td><td colspan="3">';
				if ($projet->id == $object->fk_projet)
					print $projet->getNomUrladd(1);
				print '</td></tr>';

			}
			//area solicitante
			if ($object->fk_departament>0)
			{
				print '<tr><td width="25%">'.$langs->trans('Departament').'</td><td colspan="3">';

				$getUtil->fetch_departament($object->fk_departament,'');
				print $getUtil->label;
				print '</td></tr>';
			}
			//fecha creacion
			print '<tr><td>'.$langs->trans("Date").'</td><td colspan="3">';
			print $object->date_creation ? dol_print_date($object->date_creation,'daytext') : '&nbsp;';

			print '</td></tr>';

			//fecha delivery
			if ($conf->global->ALMACEN_REGISTER_DATEDELIVERY)
			{
				print '<tr><td>'.$langs->trans("Requirementsdate").'</td><td colspan="3">';
				print $object->date_delivery ? dol_print_date($object->date_delivery,'daytext') : '&nbsp;';
				print '</td></tr>';
			}
			// Description
			print '<tr><td valign="top">'.$langs->trans("Description").'</td><td colspan="3">'.nl2br($object->description).'</td></tr>';


			// Statut
			// 4 por defecto
			// para emavias 6
			print '<tr><td>'.$langs->trans("Status").'</td><td colspan="3">'.$object->getLibStatut(4).'</td></tr>';


			print "</table>";

			dol_fiche_end();


			///////////////////////////////////
			//
			///////////////////////////////////

			//registro nueva linea
			if (($user->admin && $object->statut == 0 )|| ($object->statut == 0 && $user->rights->almacen->pedido->write && $user->id == $object->fk_user_create) || ($object->statut == 0 && $object->fk_projet>0))
			{
				if ($action != 'moditem' && $action != 'editline' && $action != 'alternative' && $action != 'createAlternative')
				{
					$var=true;
					dol_fiche_head();
					print '<table id="tablelines" class="noborder" width="100%">';

					// Add predefined products/services
					if ($user->rights->almacen->leersell || $user->rights->almacen->leernosell)
					{
						$var=!$var;
						if ($object->fk_fabrication>0)
							$objform->formAddPredefinedProduct_fsd(0,$mysoc,$soc,$hookmanager,$object->id);
						else
							$objform->formAddPredefinedProduct_sd(0,$mysoc,$soc,$hookmanager,$object->id);
					}


					$parameters=array();
					$reshook=$hookmanager->executeHooks('formAddObjectLine',$parameters,$object,$action);
					// Note that $action and $object may have been modified by hook
					print '</table>';
					dol_fiche_end();
				}
			}

			//PRODUCTOS SOLICITADOS
			$aWarehouseproduct = array();
			if ($action == 'deliver' && $object->statut == 6)
			{
				if (! empty($conf->use_javascript_ajax))
				{
					print "\n".'<script type="text/javascript">';
					print '$(document).ready(function () {
						$("#entrepotall").change(function() {
							document.formdis.action.value="deliver";
							document.formdis.submit();
						});
					});';
					print '</script>'."\n";
				}

				print '<form name="formdis" action="'.$_SERVER['PHP_SELF'].'" method="post">';
				print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
				print '<input type="hidden" name="action" value="addDeliver">';
				print '<input type="hidden" name="id" value="'.$id.'">'."\n";
			}
			$head = '';

			print '<div style="overflow-x: auto; white-space: nowrap;">';
			dol_fiche_head($head, 'card', $langs->trans("ListeProductApplication"), 0, 'stock');

			if ($action == 'deliver' && $object->statut == 6)
			{
				$listWarehouses=$entrepot->list_array(1);

				print '<table class="noborder" width="100%">';
				print '<tr>';
				print '<td align="right">'.$langs->trans('Selectentrepot');
				if (count($listWarehouses)>1)
				{
					print $formproduct->selectWarehouses((GETPOST("entrepotall")?GETPOST("entrepotall"):$object->fk_entrepot), "entrepotall".$suffix,'',1,0,$objp->fk_product);
				}
				elseif  (count($listWarehouses)==1)
				{
					print $formproduct->selectWarehouses((GETPOST("entrepotall")?GETPOST("entrepotall"):$object->fk_entrepot), "entrepotall".$suffix,'',1,0,$objp->fk_product);
				}
				else
				{
					print $langs->trans("NoWarehouseDefined");
				}
				print "</td>\n";
				print '</tr>';
				print '</table>';
			}

			print '<table class="noborder" width="100%">';
			print "<tr class=\"liste_titre\">";
			if ($object->fk_fabrication>0)
				print_liste_field_titre($langs->trans("Item"),"", "","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);

			print_liste_field_titre($langs->trans("Product"),"", "","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
			if ($conf->fabrication->enabled && $object->statut == 0)
				print_liste_field_titre($langs->trans("Alternative"),"", "","&amp;id=".$_GET['id'],"","");
			print_liste_field_titre($langs->trans("Label"),"", "","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);

			print_liste_field_titre($langs->trans("Unit"),"", "","","","");
			print_liste_field_titre($langs->trans("Description"),"", "","","","");
			if ($conf->monprojet->enabled)
			{
				print_liste_field_titre($langs->trans("Project"),"", "","","","");
				print_liste_field_titre($langs->trans("Task"),"", "","","","");
			}
			print_liste_field_titre($langs->trans("Appliedfor"),"", "","&amp;id=".$_GET['id'],"",'align="center"',$sortfield,$sortorder);
			if ($object->statut == 0)
				print_liste_field_titre($langs->trans("Select"),"", "","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
			if ($action == 'deliver' && $object->statut == 6)
			{
				print_liste_field_titre($langs->trans("Deliver"),"", "","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans("Entrepot"),"", "","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
			}
			if ($object->statut ==2)
				print_liste_field_titre($langs->trans("Delivered"),"", "","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
			if ($user->rights->almacen->pedido->ent)
			{
				print_liste_field_titre($langs->trans("Balance"),"", "","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
			}

			//REVISAR LA MUESTRA DE LA CANTIDAD ENTREGADA
			print "</tr>";

			//imprimimos para registro de items
			//

			//fin registro de items


			$totalunit=0;
			$totalvalue=$totalvaluesell=0;
			$sql = "SELECT p.rowid AS rowid, p.ref, p.label AS produit, p.fk_product_type AS type, p.pmp AS ppmp, p.price, p.price_ttc, p.fk_unit, ";
			$sql.= " cd.fk_product, cd.fk_fabricationdet, cd.qty AS qty, cd.price AS solprice, cd.qty_livree AS solqtylivree, cd.rowid AS arowid, cd.description, cd.fk_projet, cd.fk_projet_task, cd.fk_jobs, cd.fk_jobsdet, cd.fk_structure, cd.fk_unit AS fk_unitdet ";
			if ($conf->orgman->enabled)
				$sql.= " , pp.code_partida ";
			$sql.= " FROM ".MAIN_DB_PREFIX."sol_almacendet AS cd ";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product AS p ON cd.fk_product = p.rowid ";
			if ($conf->orgman->enabled)
				$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."partida_product AS pp ON cd.fk_product = pp.fk_product ";
			$sql.= " WHERE cd.fk_almacen = '".$object->id."'";
			// We do not show if stock is 0 (no product in this warehouse)
			$sql.= " ORDER BY p.label";
			//$sql.= $db->order($sortfield,$sortorder);

			dol_syslog('List products sql='.$sql);
			$resql = $db->query($sql);
			$lBalance = true;
			if ($resql)
			{
				$num = $db->num_rows($resql);
				$i = 0;
				$var=True;
				$aStructure = array();
				while ($i < $num)
				{
					$objp = $db->fetch_object($resql);
					//resumen para poa
					if ($conf->poa->enabled)
					{
						$costproduct = $objp->solprice * $objp->qty;
						$aStructure[$objp->fk_structure][$objp->code_partida]+= $costproduct;
					}
					$objProduct->fetch($objp->fk_product);
					$objProduct->load_stock();
					$aBalance = $objProduct->stock_warehouse[$object->fk_entrepot];
					$real = $aBalance->real;
					if (empty($object->fk_entrepot))
						$real = $langs->trans('Balance');
					//armamos mensaje con los saldos encontrados
					$label = '';
					$resreal = '';
					$label = '<u>' . $langs->trans('Balance') . '</u>';
					$realutil = 0;
					foreach ($objProduct->stock_warehouse AS $fk_ent => $datastock)
					{
						$resent = $entrepot->fetch($fk_ent);
						if ($resent==1)
						{
							$label.= '<br>';
							$label.= '<b>' . $entrepot->label . ':</b> ' . $datastock->real;
							$realutil+= $datastock->real;
							if (!$conf->global->STOCK_ALLOW_NEGATIVE_TRANSFER)
							{
								if ($datastock->real> 0)
									$aWarehouseproduct[$fk_ent] = $fk_ent;
							}
							else
								$aWarehouseproduct[$fk_ent] = $fk_ent;
						}
					}
					$url = '#';

					$linkclose='';
					if (empty($notooltip))
					{
						if (! empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER))
						{
							$label=$langs->trans("ShowProject");
							$linkclose.=' alt="'.dol_escape_htmltag($label, 1).'"';
						}
						$linkclose.=' title="'.dol_escape_htmltag($label, 1).'"';
						$linkclose.=' class="classfortooltip'.($morecss?' '.$morecss:'').'"';
					}
					else
						$linkclose = ($morecss?' class="'.$morecss.'"':'');

					$linkstart = '<a href="'.$url.'"';
					$linkstart.=$linkclose.'>';
					$linkend='</a>';

					if ($withpicto)
					{
						$resreal.=($linkstart.img_object(($notooltip?'':$label), 'label', ($notooltip?'':'class="classfortooltip"')).$linkend);
						if ($withpicto != 2) $result.=' ';
					}
					$resreal.= $linkstart . $realutil . $linkend;

					//if ($real > 0) $lBalance = false;
					//se modifica : si no existe saldo en ningun almacen se pone falso
					if ($realutil > 0) $lBalance = false;

					$arrayId[$objp->rowid] = $objp->rowid;
					$aProductsollist[$objp->fk_fabricationdet][$objp->rowid]=$objp->rowid;
					// Multilangs
					if ($conf->global->MAIN_MULTILANGS)
					{
						$sql = "SELECT label";
						$sql.= " FROM ".MAIN_DB_PREFIX."product_lang";
						$sql.= " WHERE fk_product=".$objp->rowid;
						$sql.= " AND lang='". $langs->getDefaultLang() ."'";
						$sql.= " LIMIT 1";

						$result = $db->query($sql);
						if ($result)
						{
							$objtp = $db->fetch_object($result);
							if ($objtp->label != '') $objp->produit = $objtp->label;
						}
					}

					$var=!$var;
					print "<tr ".$bc[$var].">";
					if ($action == 'moditem' && $objp->arowid == GETPOST('rowid'))
					{
						if (! empty($conf->use_javascript_ajax))
						{
							print "\n".'<script type="text/javascript">';
							print '$(document).ready(function () {
								$("#fk_projet").change(function() {
									document.formedit.action.value="moditem";
									document.formedit.submit();
								});
								$("#fk_jobs").change(function() {
									document.formedit.action.value="moditem";
									document.formedit.submit();
								});
								$("#idprod").change(function() {
									document.formedit.action.value="moditem";
									document.formedit.submit();
								});
								$("#fk_structure").change(function() {
									document.formedit.action.value="moditem";
									document.formedit.submit();
								});
							});';
							print '</script>'."\n";
						}


						print '<form name="formedit" action="'.$_SERVER['PHP_SELF'].'" method="post">';
						print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
						print '<input type="hidden" name="action" value="updateitem">';
						print '<input type="hidden" name="rowid" value="'.$objp->arowid.'">'."\n";
						print '<input type="hidden" name="id" value="'.$object->id.'">'."\n";

						if ($object->fk_fabrication>0)
						{
							print "<td>";
							print $aItemf[$objp->fk_fabricationdet];
							print '</td>';
						}
						print "<td>";
						print $objp->ref;
						print '</td>';
						print "<td>";
						print '&nbsp;'.$objp->produit;
						print '</td>';
						$unit = $objProduct->getLabelOfUnit();
						if (empty($objProduct->fk_unit))
						{
							//vemos que se guardo en el pedido
							if ($objp->fk_unit > 0)
							{
								$objectdettmp = new SolalmacendetLine($db);
								$objectdettmp->fk_unit = $objp->fk_unit;
								$unit = $objectdettmp->getLabelOfUnit();
							}
						}
						print '<td align="center">'.$langs->trans($unit).'</td>';
						print '<td><input type="text" name="np_desc" value="'.$objp->description.'" size="20"></td>';
						print '<td align="right"><input type="text" name="qty" value="'.$objp->qty.'" size="12"></td>';
						$rowspan= 1;
							//if ($conf->fabrication->enabled) $rowspan++;
						if ($conf->monprojet->enabled) $rowspan++;
						if ($conf->mant->enabled) $rowspan++;
						if ($conf->poa->enabled) $rowspan++;
						if ($user->rights->almacen->pedido->write && $object->statut == 0)
						{
							print '<td align="right" rowspan="'.$rowspan.'">';
							print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
							print "</td>";
						}
						print '</tr>';
						if ($conf->monprojet->enabled)
						{
							print '<tr>';
							dol_include_once('/monprojet/class/html.formprojetext.class.php');
							$formproject = new FormProjetsext($db);
							//projet
							print '<td>'.$langs->trans('Project').'</td>';
							print '<td>';
							$filterkey = '';
							$numprojet = $formproject->select_projects_v(($user->societe_id>0?$soc->id:-1), $objp->fk_projet, 'fk_projet', 0,0,1,0,0,0,0,$filterkey);
							print '</td>';
							print '<td>'.$langs->trans('Task').'</td>';
							print '<td>';
							$numtask = $formproject->selectTasks_v(($user->societe_id>0?$soc->id:-1), $objp->fk_task, 'fk_task', 24, 0, 1, 0, 0, 0,$objp->fk_projet,1,0);
							print '</td>';
							print '</tr>';
						}
						if ($conf->mant->enabled)
						{
							require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsext.class.php';
							require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsprogram.class.php';
							$langs->load('mant');
							$objJobs = new Mjobsext($db);
							$objJobsprog = new Mjobsprogram($db);
							$fk_jobs = GETPOST('fk_jobs')?GETPOST('fk_jobs'):$objp->fk_jobs;
							$filterjob = " AND status IN (2,3,4)";
							$resjob = $objJobs->fetchAll('ASC','ref',0,0,array('entity'=>$conf->entity),'AND',$fiterjob);
							$options = '<option value="0">'.$langs->trans('Select').'</option>';
							if ($resjob > 0)
							{
								$linesj = $objJobs->lines;
								foreach ($linesj AS $j => $linej)
								{
									$selected = '';
									if ($fk_jobs == $linej->id) $selected = ' selected';
									$options.= '<option value="'.$linej->id.'" '.$selected.'>'.$linej->ref.' '.$linej->label.'</option>';
								}
							}
							print '<tr class="nodrag nodrop">';
							print '<td>'.$langs->trans('Workorder').'</td>';
							print '<td>';
							print '<select id="fk_jobs" name="fk_jobs">'.$options.'</select>';
							print '</td>';
							$filterjobp = " AND t.fk_jobs =".$fk_jobs;
							$resjobp = $objJobsprog->fetchAll('ASC','ref',0,0,array(1=>1),'AND',$filterjobp);
							$options = '<option value="0">'.$langs->trans('Selectprogram').'</option>';
							if ($resjobp > 0)
							{
								$linesj = $objJobsprog->lines;
								foreach ($linesj AS $j => $linej)
								{
									$selected = '';
									if ($objp->fk_jobsdet == $linej->id) $selected = ' selected';
									$options.= '<option value="'.$linej->id.'" '.$selected.'>'.$linej->ref.' '.$linej->description.'</option>';
								}
							}
							print '<td>'.$langs->trans('Taskprogramming').'</td>';
							print '<td colspan="2">';
							print '<select name="fk_jobsdet">'.$options.'</select>';
							print '</td>';
							print '</tr>';
						}
							//para presupuesto poa
						if ($conf->poa->enabled && $conf->global->ALMACEN_INTEGER_WITH_POA)
						{
							$objStr 		= new Poastructure($db);
							$objPoa 	= new Poapoaext($db);
							$objPP   	= new Partidaproduct($db);
							$objPre 	= new Poapartidapre($db);
							$fk_structure 	= (GETPOST('fk_structure')?GETPOST('fk_structure'):$objp->fk_structure);

							$res = $objPP->fetch($objp->fk_product);
							$optionspoa = '<option value="-1">'.$langs->trans('Selectpoa').'</option>';
							if ($res > 0)
							{
								$aDate = dol_getdate($object->date_creation);
								$filterpartida = " AND t.gestion = ".$aDate['year'];
								$filterpartida.= " AND t.partida = '".$objPP->code_partida."'";

								if ($object->fk_departament>0) $filterpartida.= " AND t.fk_area = ".$object->fk_departament;
								$respoa = $objPoa->fetchAll('ASC','ref',0,0,array('entity'=>$conf->entity),'AND',$filterpartida);
								if ($respoa>0)
								{
									foreach ($objPoa->lines AS $j => $linepoa)
									{
										$selected = '';
										if ($fk_structure == $linepoa->fk_structure) $selected = ' selected';
										$objStr->fetch($linepoa->fk_structure);
										$optionspoa.= '<option value="'.$linepoa->fk_structure.'" '.$selected.'>'.$objStr->sigla.' - '.$objStr->label.'</option>';
									}
								}
							}
							print '<tr class="liste_titre nodrag nodrop">';
							print '<td>'.$langs->trans('Categoria Programática').'</td>';
							print '<td>'.$langs->trans('Partida').'</td>';
							print '<td>'.$langs->trans('Aprobado').'</td>';
							print '<td>'.$langs->trans('Preventivo').'</td>';
							print '<td>'.$langs->trans('Saldo').'</td>';
							print '</tr>';
							print '<tr>';
							print '<td>';
							print '<select id="fk_structure" name="fk_structure">'.$optionspoa.'</select>';
							print '</td>';
							print '<td>';
							print $objPP->code_partida;
							print '</td>';
							print '<td>';
							//mostramos el presupuesto
							$amount = 0;
							$amountpre = 0;
							if ($fk_structure>0)
							{
								$aDate = dol_getdate($object->date_creation);
								$filterpartida = " AND t.gestion = ".$aDate['year'];
								$filterpartida.= " AND t.partida = '".$objPP->code_partida."'";
								$filterpartida.= " AND t.fk_structure = ".$fk_structure;
								if ($object->fk_departament>0) $filterpartida.= " AND t.fk_area = ".$object->fk_departament;
								$respoa = $objPoa->fetchAll('ASC','ref',0,0,array('entity'=>$conf->entity),'AND',$filterpartida,true);
								if ($respoa==1)
								{
									$amount = $objPoa->amount;
									//buscamos cuantos preventivos ya fueron emitidos
									$filterpartida = " AND t.fk_poa = ".$objPoa->id;
									$filterpartida.= " AND t.partida = '".$objPP->code_partida."'";
									$filterpartida.= " AND t.fk_structure = ".$fk_structure;
									//if ($object->fk_departament>0) $filterpartida.= " AND t.fk_area = ".$object->fk_departament;
									$respre = $objPre->fetchAll('ASC','ref',0,0,array('statut'=>1),'AND',$filterpartida);
									if ($respre>0)
									{
										foreach ($objPre->lines AS $j => $linepre)
										{
											$amountpre+= $linepre->amount;
										}
									}
								}
							}
							$balance = $amount - $amountpre;
							print price($amount);
							print '</td>';
							print '<td>';
							print price($amountpre);
							print '</td>';
							print '<td>';
							print price($balance);
							print '</td>';
							print '</tr>';
						}
						print '</form>';
					}
					else
					{
						if ($object->fk_fabrication>0)
						{
							print "<td>";
							print $aItemf[$objp->fk_fabricationdet];
							print '</td>';
						}
						print "<td>";
						if ($object->statut == 0)
						{
							print $objProduct->getNomUrl(1);
						}
						else
						{
							print $objp->ref;
						}
						print '</td>';
						if ($conf->fabrication->enabled && $object->statut == 0)
						{
							print "<td>";
							if ($object->statut == 0 && $aFilterent[$object->fk_entrepot_from])
							{
								print '<a href="'.DOL_URL_ROOT.'/almacen/fiche.php?id='.$object->id.'&rowid='.$objp->arowid.'&prowid='.$objp->rowid.'&action=alternative">'.img_picto($langs->trans("Alternative"),DOL_URL_ROOT.'/almacen/img/alternative.png','',1).'</a>';
							}
							else
							{
								print '&nbsp;';
							}
							print '</td>';
						}
						print '<td>'.$objp->produit.'</td>';
						//units
						$unit = $objProduct->getLabelOfUnit();
						if (empty($objProduct->fk_unit))
						{
							//vemos que se guardo en el pedido
							if ($objp->fk_unitdet > 0)
							{
								$objectdettmp = new SolalmacendetLine($db);
								$objectdettmp->fk_unit = $objp->fk_unitdet;
								$unit = $objectdettmp->getLabelOfUnit();
							}
						}
						print '<td>'.$langs->trans($unit).'</td>';
						print '<td>'.$objp->description.'</td>';
						if ($conf->monprojet->enabled)
						{
							dol_include_once('/projet/class/project.class.php');
							dol_include_once('/projet/class/task.class.php');
							$project = new Project($db);
							$task = new Task($db);
							if ($objp->fk_projet) $project->fetch($objp->fk_projet);
							if ($objp->fk_projet_task) $task->fetch($objp->fk_projet_task);

							//projet
							print '<td>';
							if ($project->id)
								print $project->getNomUrl(1);
							print '</td>';
							print '<td>';
							if ($task->id)
								print $task->getNomUrl(1);
							print '</td>';
						}

						print '<td align="center">'.price($objp->qty).'</td>';
						$totalunit+=$objp->qty;

						if ($user->rights->almacen->pedido->ent && $object->statut == 6)
						{
							if ($action == 'deliver')
							{
								print '<td align="right">';
								print '<input type="number" min="0" step="any" name="qty_livree['.$objp->arowid.']" value="'.price2num($objp->qty,'MS').'">';
								print "</td>";
								print '<td align="right">';
								$filterstatic = '';
								if (!$conf->global->STOCK_ALLOW_NEGATIVE_TRANSFER)
								{
									$idsEntrepot = implode(',',$aWarehouseproduct);
									$filterstatic = " AND t.rowid IN (".$idsEntrepot.")";
								}
								$resent = $entrepot->fetchAll('ASC', 'label', 0,0, array(1=>1), 'AND', $filterstatic, false);
								if ($resent>0)
								{
									$optionsent = '';
									foreach ($entrepot->lines AS $j => $line)
									{
										$lAdd = true;
										if (!$user->admin)
										{
											if (!$aFilterent[$line->id])
												$lAdd = false;
										}
										if ($lAdd)
										{
											$selected = '';
											if ($entrepotall && $line->id == $entrepotall) $selected = ' selected';
											$optionsent.= '<option value="'.$line->id.'" '.$selected.'>'.$line->label.'</option>';
										}
									}
								}
								print '<select name="entrepot['.$objp->arowid.']">'.$optionsent.'</select>';
								print "</td>\n";
							}
						}
						if ($object->statut == 2)
						{
							print '<td align="right">';
							print price(price2num($objp->solqtylivree,'MT'));
							print "</td>";
						}
						//if (($user->admin && $object->statut == 0)|| ($user->rights->almacen->crearpedido && $object->statut == 0 && $aFilterent[$object->fk_entrepot_from]))
						//if (($user->admin && $object->statut == 0)|| ($user->rights->almacen->pedido->write && $object->statut == 0 && $user->id == $object->fk_user_create))
						if ($user->rights->almacen->pedido->write && $object->statut == 0 && ($user->id == $object->fk_user_create || $user->admin))
						{
							print '<td align="right">';
							print '<a href="'.DOL_URL_ROOT.'/almacen/fiche.php?id='.$object->id.'&rowid='.$objp->arowid.'&action=moditem">'.img_picto($langs->trans("Modify"),'edit').'</a>';
							print '&nbsp;';
							print '<a href="'.DOL_URL_ROOT.'/almacen/fiche.php?id='.$object->id.'&amp;aid='.$objp->arowid.'&amp;action=transferdel">';
							print img_picto($langs->trans("Delete"),'delete');
							print "</a></td>";
						}
						if ($user->rights->almacen->pedido->ent)
						{
							//print '<td align="right">'.price($real).'</td>';
							print '<td align="right">'.$resreal.'</td>';
						}

						//verificamos la existencia de saldo
						if ($objp->qty > $real)
							$aResidue[$objp->fk_product] = $objp->qty-$real;
					}
					print "</tr>";
					$i++;
				}
				$db->free($resql);

			}
			else
			{
				dol_print_error($db);
			}
			print "</table>\n";

			dol_fiche_end();
			print '</div>';
			if ($action == 'deliver' && $object->statut == 6)
			{
				print '<center><br><input type="submit" class="button" value="'.$langs->trans("Entregar").'"></center>';
				print '</form>';
			}
			//procesamos un resumen del presupuesto generado
			if ($conf->poa->enabled && $conf->global->ALMACEN_INTEGER_WITH_POA)
			{
				$langs->load('Poa');
				$objStructure = new Poastructureext($db);
				$objPoa = new Poapoaext($db);
				$objPartida = new Cpartida($db);
				$objPre = new Poapartidapre($db);
				$var = true;
				$sumValue = 0;
				$aResiduepoa = array();
				$sumAmount = 0;
				$sumAmountpre = 0;
				$sumBalance = 0;
				$sumBalancepost = 0;
				dol_fiche_head('', 'card', $langs->trans("Liste structure"), 0, 'stock');
				print '<table class="noborder" width="100%">';
				print "<tr class=\"liste_titre\">";
				print_liste_field_titre($langs->trans("Catprog"),"", "","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
				print_liste_field_titre($langs->trans("Partida"),"", "","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
				print_liste_field_titre($langs->trans("Aprobado"),"", "","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans("Preventivo"),"", "","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans("Balance"),"", "","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans("Amount"),"", "","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans("Balancepost"),"", "","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
				print '</tr>';
				foreach ((array) $aStructure AS $fk_structure => $data)
				{
					$objStructure->fetch($fk_structure);
					foreach ($data AS $code_partida => $value)
					{
						$aDate = dol_getdate($object->date_creation);
						$filterpartida = " AND t.gestion = ".$aDate['year'];
						$filterpartida.= " AND t.partida = '".$code_partida."'";
						$filterpartida.= " AND t.fk_structure = ".$fk_structure;
						if ($object->fk_departament>0) $filterpartida.= " AND t.fk_area = ".$object->fk_departament;
						$respoa = $objPoa->fetchAll('ASC','ref',0,0,array('entity'=>$conf->entity),'AND',$filterpartida,true);
						if ($respoa==1)
						{
							$amount = $objPoa->amount;

							$filterpartida = " AND t.fk_poa = ".$objPoa->id;
							$filterpartida.= " AND t.partida = '".$code_partida."'";
							$filterpartida.= " AND t.fk_structure = ".$fk_structure;
							$respre = $objPre->fetchAll('ASC','ref',0,0,array('statut'=>1),'AND',$filterpartida);
							if ($respre>0)
							{
								foreach ($objPre->lines AS $j => $linepre)
								{
									$amountpre+= $linepre->amount;
								}
							}
						}



						$var = !$var;
						$objPartida->fetch(0,$code_partida,$aDate['year']);
						print "<tr $bc[$var]>";
						print '<td>'.$objStructure->sigla.' '.$objStructure->label.'</td>';
						print '<td>'.$code_partida.' '.$objPartida->label.'</td>';
						print '<td align="right">'.price($amount).'</td>';
						print '<td align="right">'.price($amountpre).'</td>';
						$balance = price2num($amount-$amountpre,'MT');
						print '<td align="right">'.price($balance).'</td>';
						print '<td align="right">'.price($value).'</td>';
						$balancepost = price2num($balance - $value,'MT');
						print '<td align="right">'.price($balancepost).'</td>';
						$sumAmount+= $amount;
						$sumAmountpre+= $amountpre;
						$sumBalance+= $balance;
						$sumValue+= $value;
						$sumBalancepost+= $balancepost;
						print '</tr>';
						if ($balancepost <= 0)
							$aResiduepoa[$fk_structure][$code_partida] = $balancepost;
					}
				}
					//imprimimos totales
				print '<tr class="liste_total">';
				print '<td colspan="2">'.$langs->trans('Total').'</td>';
				print '<td align="right">'.price($sumAmount).'</td>';
				print '<td align="right">'.price($sumAmountpre).'</td>';
				print '<td align="right">'.price($sumBalance).'</td>';
				print '<td align="right">'.price($sumValue).'</td>';
				print '<td align="right">'.price($sumBalancepost).'</td>';
				print '</tr>';

				print '</table>';
				dol_fiche_end();
			}

			//barre de action
			print "<div class=\"tabsAction\">\n";

			if ($action == '')
			{
				//if ($user->rights->almacen->pedido->write && $object->statut == 0 && ($user->id == $object->fk_user_create  && $aFilterarea[$object->fk_departament] || $aFilterent[$object->fk_entrepot_from] || $user->admin))
				if ($user->rights->almacen->pedido->write && $object->statut == 0 && ($user->id == $object->fk_user_create || $user->admin))

					print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

				//anular
				//if (($object->statut==1 || $object->statut==0 ) && $user->rights->almacen->pedido->del)
				//	print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Cancel")."</a>";
				//else
				//	print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Cancel")."</a>";

				//delete
				$lDeleteval = false;
				if ($user->rights->almacen->pedido->val)
				{
					$aDepartamentval = $objDepartament->verif_accessresp($user->fk_member);
					if (!empty($aAreadirect))
					{
						foreach ($aAreadirect AS $j)
							$aDepartamentval[$j] = $j;
					}
					if ($aDepartamentval[$object->fk_departament]) $lDeleteval = true;
				}

				if (($object->statut==0 && $lDeleteval && $user->rights->almacen->pedido->del) || $user->admin)
					print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";

				//rechazar
				//if (($object->statut==1 || $object->statut==0 ) && $user->rights->almacen->pedido->rech && (($aFilterarea[$object->fk_departament] && $object->fk_departament != $fk_areaasign) || $user->admin))
				if (($object->statut==1 || $object->statut==0 ) && $user->rights->almacen->pedido->rech && (($aFilterarea[$object->fk_departament] ) || $user->admin))
					print "<a class=\"butActionDelete\" href=\"fiche.php?action=reject&id=".$object->id."\">".$langs->trans("Reject")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Reject")."</a>";
				// Valid
				// Salida Almacen
				//echo  ' saldoneg '.$conf->global->STOCK_ALLOW_NEGATIVE_TRANSFER;
				if ($conf->global->STOCK_ALLOW_NEGATIVE_TRANSFER)
				{
					if ($object->statut == 6 && ($user->rights->almacen->pedido->ent && $aFilterent[$object->fk_entrepot] || $user->admin))
					{
						print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=deliver">'.$langs->trans('SubmitRequest').'</a>';
					}
				}
				else
				{
					if (!$lBalance)
					{
							//cuando tiene saldo y es posible entregar
						if ($object->statut == 6 && ($user->rights->almacen->pedido->ent && $aFilterent[$object->fk_entrepot] || $user->admin))
						{
							print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=deliver">'.$langs->trans('SubmitRequest').'</a>';
						}
					}
					else
					{
							//cuando no tiene saldo todos los productos
							//marcar como material no existente
						if ($object->statut == 6 && ($user->rights->almacen->pedido->ent && $aFilterent[$object->fk_entrepot] || $user->admin))
						{
							if ($conf->global->ALMACEN_ACTIVE_BUTTON_WITHOUT_EXISTENCE)
								print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=noexist">'.$langs->trans('Without existence').'</a>';
						}
					}
				}
				//ALMACEN_ACTIVE_BUTTON_WITHOUT_EXISTENCE
				if ($object->statut == 0 && $numLinesItem > 0 && $user->rights->almacen->pedido->val)
				{
					$lVal = true;
					if ($conf->global->ALMACEN_INTEGER_WITH_POA)
					{
						if ($conf->poa->enabled)
						{
							//vamos a verificar la existencia de saldo
							if (count($aResidue)>0) $lVal = false;
							else setEventMessages($langs->trans('Existe saldo en almacenes, es posible validar'),null,'mesgs');
						}
						if (!$lVal)
						{
							if (count($aResiduepoa)>0)
							{
								$lVal = false;
								setEventMessages($langs->trans('No existe presupuesto suficiente para validar'),null,'warnings');
							}
							else $lVal = true;
						}
					}
					if ($lVal && $user->rights->almacen->pedido->val)
					{
						print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=validate">'.$langs->trans('Validate').'</a>';
					}
				}
				// ReValid
				//if ($object->statut == 1 && (($aFilterarea[$object->fk_departament] && $object->fk_departament != $fk_areaasign) || $aFilterent[$object->fk_entrepot] || $user->admin))
				if ($object->statut == 1 && ($user->rights->almacen->pedido->appall || ($user->rights->almacen->pedido->app  && $aFilterarea[$object->fk_departament]) || $aFilterent[$object->fk_entrepot] || $user->admin))
				{
					print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=revalidate">'.$langs->trans('Notvalidate').'</a>';
				}
				// Approved
				//if ($object->statut == 1 && ($user->rights->almacen->pedido->appall || ($user->rights->almacen->pedido->app  && (($aFilterarea[$object->fk_departament] && $fk_areaasign != $object->fk_departament) || $aFilterent[$object->fk_entrepot] || $user->admin))))
				//if ($object->statut == 1 && ($user->rights->almacen->pedido->appall || ($user->rights->almacen->pedido->app  && $aFilterarea[$object->fk_departament]) || $aFilterent[$object->fk_entrepot] || $user->admin))
				if ($object->statut == 1 && ($user->rights->almacen->pedido->appall || ($user->rights->almacen->pedido->app  && $aFilterarea[$object->fk_departament]) || $user->admin))
				{
					print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=approved">'.$langs->trans('Approve').'</a>';
				}
				// request order
				if ($object->statut == 5 && (($aFilterent[$object->fk_entrepot_from] || $user->admin) || $object->fk_user_create == $user->id))
				{
					if ($user->rights->purchase->req->creer && $conf->purchase->enabled)
					{
						//verificamos que no exista solicitud de compra ya iniciada
						$objPurchaserequest = new Purchaserequestext($db);
						$filtersol = " AND t.origin = 'solalmacen'";
						$filtersol.= " AND t.originid = ".$object->id;
						$ressol =$objPurchaserequest->fetchAll('','',0,0,array(1=>1),'AND',$filtersol,true);
						if (empty($ressol))
						{
							$langs->load('purchase');
							print '<a class="butAction" href="'.DOL_URL_ROOT.'/purchase/request/card.php?action=create&originid='.$object->id.'&amp;origin=solalmacen">'.$langs->trans('Createpurchaserequest').'</a>';
						}
						else
						{
							//recuperamos saldos
							print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=changestatusval">'.$langs->trans('Changestatustodeliverable').'</a>';
						}
					}
				}
			}

			if ($action == 'alternative' && $user->rights->almacen->crearlistproductalt)
			{
				print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'">'.$langs->trans('Return').'</a>';
				print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=createAlternative">'.$langs->trans('CreateAlternative').'</a>';
			}
			if ($action == 'createAlternative' && $user->rights->almacen->crearlistproductalt)
			{
				print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'">'.$langs->trans('Return').'</a>';
			}
			print "</div>";

			//DOCUMENTS
			print "<div class=\"tabsAction\">\n";
			//documents
			if ($object->statut>=1 && $action!='deliver')
			{
				print '<table width="100%"><tr><td width="50%" valign="top">';
				print '<a name="builddoc"></a>';
				// ancre
				// Documents generes
				$filename=dol_sanitizeFileName($object->ref);
				$filedir=$conf->almacen->dir_output . '/' . dol_sanitizeFileName($object->ref);
				$urlsource=$_SERVER['PHP_SELF'].'?id='.$object->id;
				$genallowed=$user->rights->almacen->creardoc;
				$delallowed=$user->rights->almacen->deldoc;
				//$genallowed = false;
				$delallowed = false;
				$object->modelpdf = 'pedido';
				print '<br>';
				print $formfile->showdocuments('almacen',$filename,$filedir,$urlsource,$genallowed,$delallowed,$object->modelpdf,1,0,0,28,0,'','','',$soc->default_lang);
				$somethingshown=$formfile->numoffiles;
				print '</td></tr></table>';
			}
			print "</div>";




			//liste produits fabrication
			if ($conf->fabrication->enabled)
			{
				if ($object->fk_fabrication > 0 && $object->statut==0 && $action<>'alternative' && $action<>'createAlternative')
				{
					print '<br>';
					dol_fiche_head('', 'card', $langs->trans("ListeProductFabrication"), 0, 'stock');
					if (!empty($arrayId)) $listId = implode(',',$arrayId);
					$listFilterid='';
					$aIdsFilterfd = array();
					if (!empty($aProductsollist))
					{
						foreach ($aProductsollist AS $idpdet => $aIdprod)
						{
							$listFilterid.= " AND (cd.rowid != ".$idpdet;
							$listFilteriddet='';
							if (count($aIdprod)>0)
							{
								foreach ($aIdprod AS $idprod)
								{
									$newval = $idpdet.$idprod;
									if ($listFilteriddet) $listFilteriddet.= ',';
									$listFilteriddet.= $idprod;
									$aIdsFilterfd[$newval]=$newval;
								}
								$listFilterid.= " AND p.rowid NOT IN (".$listFilteriddet.") )";
							}
						}
					}

					print '<table class="noborder" width="100%">';
					print "<tr class=\"liste_titre\">";
					print_liste_field_titre($langs->trans("Product"),"", "p.ref","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
					print_liste_field_titre($langs->trans("Label"),"", "p.label","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
					print_liste_field_titre($langs->trans("Units"),"", "cd.qty","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
					print_liste_field_titre($langs->trans("Actions"),"", "","&amp;id=".$_GET['id'],"",'align="right"');
					print "</tr>";

					$totalunit=0;
					$totalvalue=$totalvaluesell=0;

					$sql = "SELECT concat(cd.rowid,pa.fk_product_son) AS fk, cd.rowid AS fdrowid, pa.fk_product_son as prowid, p.ref, p.label as produit, p.fk_product_type as type, p.pmp as ppmp, p.price, p.price_ttc,";
					$sql.= " pa.qty_son AS qtyconvert, cd.qty as qty";
					$sql.= " FROM ".MAIN_DB_PREFIX."fabricationdet AS cd ";
					$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product_list AS pa ON cd.fk_product = pa.fk_product_father ";
					$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product AS p ON p.rowid = pa.fk_product_son ";

					$sql.= " WHERE ";
					$sql.= " cd.fk_fabrication = '".$object->fk_fabrication."'";
					// We do not show if stock is 0 (no product in this warehouse)

					//cd.fk_product = p.rowid";
					//if (!empty($listId)) $sql .= " AND (p.rowid NOT IN ($listId) OR p.rowid IS NULL)";
					//if (!empty($listFilterid)) $sql .= $listFilterid;
					//echo '<hr>asdfasdf '.implode(',',$aIdsFilterfd);
					if (!empty($aIdsFilterfd)) $sql .= " AND concat(cd.rowid,pa.fk_product_son) NOT IN (".implode(',',$aIdsFilterfd).")";
					dol_syslog('List products sql='.$sql);
					//echo '<hr>'.$sql;
					$resql = $db->query($sql);
					if ($resql)
					{
						$num = $db->num_rows($resql);
						$i = 0;
						$var=True;
						while ($i < $num)
						{
							$objp = $db->fetch_object($resql);

							// Multilangs
							if ($conf->global->MAIN_MULTILANGS)
							// si l'option est active
							{
								$sql = "SELECT label";
								$sql.= " FROM ".MAIN_DB_PREFIX."product_lang";
								$sql.= " WHERE fk_product=".$objp->rowid;
								$sql.= " AND lang='". $langs->getDefaultLang() ."'";
								$sql.= " LIMIT 1";

								$result = $db->query($sql);
								if ($result)
								{
									$objtp = $db->fetch_object($result);
									if ($objtp->label != '') $objp->produit = $objtp->label;
								}
							}

							$var=!$var;
							//print '<td>'.dol_print_date($objp->datem).'</td>';
							print "<tr ".$bc[$var].">";
							print "<td>";
							$productstatic->id=$objp->prowid;
							$productstatic->ref=$objp->ref;
							$productstatic->type=$objp->type;
							print $productstatic->getNomUrl(1,'stock',16);
							print '</td>';
							print '<td>'.$objp->produit.'</td>';
							$qtyNew = $objp->qty*$objp->qtyconvert;
							print '<td align="right">'.$qtyNew.'</td>';
							$totalunit+=$objp->qty;

							if ($user->rights->almacen->pedido->write && $object->statut == 0)
							{

								if (empty($objp->rowid))
								{
									print '<td align="right"><a href="'.DOL_URL_ROOT.'/almacen/fiche.php?id='.$object->id.'&amp;pid='.$objp->prowid.'&qty='.$qtyNew.'&idr='.$objp->fdrowid.'&amp;action=transferf">';
									print img_picto($langs->trans("StockMovement"),'uparrow.png').' '.$langs->trans("Select");
									print "</a></td>";
								}
								else
								{
									if ($user->rights->stock->mouvement->creer)
									{
										print '<td align="right"><a href="'.DOL_URL_ROOT.'/almacen/fiche.php?id='.$object->id.'&amp;pid='.$objp->prowid.'&idr='.$objp->fdrowid.'&amp;action=transferf">';
										print img_picto($langs->trans("StockMovement"),'uparrow.png').' '.$langs->trans("StockAlmacen");
										print "</a></td>";
									}
								}
								print "</tr>";
							}
							$i++;
						}
						$db->free($resql);


					}
					else
					{
						dol_print_error($db);
					}
					print "</table>\n";
					dol_fiche_end();
				}
			}
				//product alternative

			if ($_GET['rowid'] && $_GET['prowid'] && $object->statut==0 && $action = 'alternative')
			{
				print '<br>';
				dol_fiche_head('', 'card', $langs->trans("ListProductAlternative"), 0, 'stock');
				if (!empty($arrayId)) $listId = implode(',',$arrayId);
				print '<table class="noborder" width="100%">';
				print "<tr class=\"liste_titre\">";
				print_liste_field_titre($langs->trans("Product"),"", "p.ref","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
				print_liste_field_titre($langs->trans("Label"),"", "p.label","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
				if ($conf->fabrication->enabled)
					print_liste_field_titre($langs->trans("Units"),"", "cd.qty","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans("Actions"),"", "cd.qty","&amp;id=".$_GET['id'],"",'align="right"');
				print "</tr>";

				$totalunit=0;
				$totalvalue=$totalvaluesell=0;

				$sql = "SELECT pa.fk_product_alt as prowid, p.ref, p.label as produit, ";
				if ($conf->fabrication->enabled)
					$sql.= "u.ref as unit, ";
				$sql.= "pa.rowid AS arowid ";
				$sql.= " FROM ".MAIN_DB_PREFIX."product_alternative AS pa ";
				$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product AS p ON p.rowid = pa.fk_product_alt ";
				if ($conf->fabrication->enabled)
					$sql.= " INNER JOIN ".MAIN_DB_PREFIX."units AS u ON u.rowid = pa.fk_unit_alt ";

				$sql.= " WHERE ";
				$sql.= " pa.fk_product = '".$_GET['prowid']."'";

				$sql.= $db->order($sortfield,$sortorder);

				dol_syslog('List products alternative sql='.$sql);
				$resql = $db->query($sql);
				if ($resql)
				{
					$num = $db->num_rows($resql);
					$i = 0;
					$var=True;
					while ($i < $num)
					{
						$objp = $db->fetch_object($resql);

						// Multilangs
						if ($conf->global->MAIN_MULTILANGS)
						// si l'option est active
						{
							$sql = "SELECT label";
							$sql.= " FROM ".MAIN_DB_PREFIX."product_lang";
							$sql.= " WHERE fk_product=".$objp->prowid;
							$sql.= " AND lang='". $langs->getDefaultLang() ."'";
							$sql.= " LIMIT 1";

							$result = $db->query($sql);
							if ($result)
							{
								$objtp = $db->fetch_object($result);
								if ($objtp->label != '') $objp->produit = $objtp->label;
							}
						}

						$var=!$var;
						//print '<td>'.dol_print_date($objp->datem).'</td>';
						print "<tr ".$bc[$var].">";
						print "<td>";
						$productstatic->id=$objp->rowid;
						$productstatic->ref=$objp->ref;
						$productstatic->type=$objp->type;
						print $productstatic->getNomUrl(1,'stock',16);
						print '</td>';
						print '<td>'.$objp->produit.'</td>';
						if ($conf->fabrication->enabled)
							print '<td>'.$objp->unit.'</td>';
						print '<td align="right"><a href="'.DOL_URL_ROOT.'/almacen/fiche.php?id='.$object->id.'&rowid='.$_GET['rowid'].'&amp;pid='.$objp->arowid.'&amp;action=confirmalternative">';
						print img_picto($langs->trans("Select"),'uparrow.png').' '.$langs->trans("Select");
						print "</a></td>";
						print "</tr>";
						$i++;
					}
					$db->free($resql);

				//print '<tr class="liste_total"><td class="liste_total" colspan="2">'.$langs->trans("Total").'</td>';
				// print '<td class="liste_total" align="right">'.$totalunit.'</td>';
				// print '<td class="liste_total">&nbsp;</td>';
				// print '</tr>';

				}
				else
				{
					dol_print_error($db);
				}
				print "</table>\n";
				dol_fiche_end();
			}
			if ($action == 'createAlternative' && $user->rights->almacen->crearlistproductalt)
			{
				$objectpalt  = new Productalternative($db);

				//product father
				print '<table id="tablelines" class="noborder" width="100%">';
				if ($action != 'editline')
				{

					$var=true;

					if ($conf->global->MAIN_FEATURES_LEVEL > 1)
					{
						// Add free or predefined products/services
						$objectpalt->formAddObjectLine(1,$mysoc,$soc,$hookmanager);
					}
					else
					{
						// Add predefined products/services
						//if (! empty($conf->product->enabled) || ! empty($conf->service->enabled))
						//{
						$var=!$var;
						$objectpalt->formAddPredefinedProduct_sd(0,$mysoc,$soc,$hookmanager,$object->id);
						//}
					}

					$parameters=array();

					$reshook=$hookmanager->executeHooks('formAddObjectLine',$parameters,$object,$action);
				  // Note that $action and $object may have been modified by hook
				}
				print '</table>';
				dol_fiche_end();
			}

		}


			// Edition fiche
		if (($action == 'edit' || $action == 're-edit') && 1)
		{
			print_fiche_titre($langs->trans("ApplicationsEdit"), $mesg);

			print '<form name="formalm" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="update">';
			print '<input type="hidden" name="id" value="'.$object->id.'">';

			print '<table class="border" width="100%">';

				// Ref
			print '<tr><td width="20%" class="fieldrequired">'.$langs->trans("Ref").'</td><td colspan="3"><input name="libelle" size="20" value="'.$object->ref.'" readonly></td></tr>';


			if ($user->admin)
			{
					//SOLO PARA EVIAS DEBE ESTAR BLOQUEADO

				print '<tr>';
				print '<td width="20%" >'.$langs->trans("Entrepotfrom").'</td><td width="20%">';
				print $formproduct->selectWarehouses($object->fk_entrepot_from,'id_entrepot_source','',1);
				print '</td>';
				print '</tr>';

			}
			else
			{

				$lSelentrepotfrom = true;
				if (count($aFilterent)==1)
				{
					foreach ($aFilterent AS $fk_entrepot)
					{
						$entrepot->fetch($fk_entrepot);
						if ($entrepot->libelle == $conf->global->ALMACEN_CODE_DEFAULT_STORE)
							$lSelentrepotfrom = false;
					}
				}
					//armamos una lista de entrepot
				if (count($aFilterent)>0 && $lSelentrepotfrom)
				{
						//SOLO PARA EVIAS DEBE ESTAR BLOQUEADO

					print '<tr>';
					print '<td width="20%" >'.$langs->trans("Entrepotfrom").'</td><td width="20%">';
					print $form->selectarray('id_entrepot_source',$aEntrepotfrom,(GETPOST('id_entrepot_source')?GETPOST('id_entrepot_source'):$object->fk_entrepot_from),1);

						//$checked = '';
						//$idExcluded = '';
						//if (count($aFilterent) == 1)
						//{
						//	$checked = 'checked="checked"';
						//	$idExcluded = $filteruser;
						//}
						//foreach ($aFilterent AS $fk_entrepot)
						//{
						//	$entrepot->fetch($fk_entrepot);
							//print '<p>'.$entrepot->lieu.' <input type="radio" '.$checked.' name="id_entrepot_source" value="'.$fk_entrepot.'">'.'</p>';
						//}
					print '</td>';
					print '</tr>';

				}
			}

				// Entrepot Almacen
			print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('A entregar por').'</td><td colspan="3">';

			$filterstatic = " AND fk_entrepot_father <=0";
			$res = $objectUrqEntrepot->fetchAll('','',0,0,array(),'AND',$filterstatic);
			$ids;
			if ($res>0)
			{
				foreach ($objectUrqEntrepot->lines AS $j => $line)
				{
					$lAdd = true;
					if (!$user->admin)
					{
						if (empty($aFilterentsol[$line->id]))
							$lAdd = false;
					}
					if ($lAdd)
					{
						if ($ids) $ids.= ',';
						$ids.= $line->id;
					}
				}
			}
			$filterstatic = " AND t.rowid IN (".$ids.") AND t.statut = 1 ";
			if (!$user->admin && $filterusersol)
				$filterstatic.= " AND t.rowid IN (".$filterusersol.") AND t.statut = 1 ";
			$resent = $entrepot->fetchAll('ASC', 'label', 0,0, array(1=>1), 'AND', $filterstatic, false);
			$fk_entrepot = $object->fk_entrepot;
			if (GETPOST('fk_entrepot'))
				$fk_entrepot = GETPOST('fk_entrepot');
			if ($resent>0)
			{
				foreach ($entrepot->lines AS $j => $line)
				{
					$selected = '';
					if (fk_entrepot && $line->id == $fk_entrepot) $selected = ' selected';
					$optionsent.= '<option value="'.$line->id.'" '.$selected.'>'.$line->lieu.'('.$line->label.')'.'</option>';
				}
			}
			print '<select name="fk_entrepot">'.$optionsent.'</select>';

			//print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('A entregar por').'</td><td colspan="3">';
			//print $objectUrqEntrepot->select_padre($object->fk_entrepot,'fk_entrepot',1,$idExcluded);
			print '</td></tr>';

			// Entrepot Almacen
			//print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Entrepot').'</td><td colspan="3">';
			//print $objectUrqEntrepot->select_padre($object->fk_entrepot,'fk_entrepot',1);
			//print '</td></tr>';
			if ($conf->projet->enabled && $conf->monprojet->enabled)
			{
				print '<tr><td>'.$langs->trans("Project").'</td><td>';
				if (!$fk_projet)
				{
					$filterkey = '';
					$numprojet = $formproject->select_projects_v(($user->societe_id>0?$soc->id:-1), $object->fk_projet, 'fk_projet', 0,0,1,0,0,0,0,$filterkey);
				}
				else
				{
					print $objectsrc->getNomUrladd(1);
					print '<input type="hidden" name="fk_projet" value="'.$fk_projet.'">';
				}
				print '</td></tr>';
			}

			// Fabrication
			if ($conf->fabrication->enabled)
			{
				print '<tr><td width="25%">'.$langs->trans('OrderProduction').'</td><td colspan="3">';
				print $formfabrication->select_fabrication($object->fk_fabrication,'fk_fabrication','',!$disabled,!$disabled);
				print '</td></tr>';
			}
			//solicitante
			$exclude = array();
			print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Applicant').'</td><td colspan="3">';
			if ($user->admin)
				print $form->select_dolusers((empty($object->fk_user)?$user->id:$object->fk_user),'fk_user',1,$exclude,0,'','',$object->entity);
			else
			{
				print $user->login;
			}
			print '</td></tr>';


	//area solicitante
			print '<tr><td width="25%">'.$langs->trans('Departament').'</td><td colspan="3">';
			if ($user->admin)
				print $form->select_departament($object->fk_departament,'fk_departament','',0,1);
			else
			{

				if ($conf->orgman->enabled)
				{
					$filter = " AND t.rowid IN (".$filterarea.")";
					$resdep = $objDepartament->fetchAll('ASC','label',0,0,array('entity'=>$conf->entity,'active'=>1,'status'=>1), 'AND',$filter);
					$options = '';
					if ($resdep>0)
					{
						foreach($objDepartament->lines AS $j => $line)
						{
							$options.= '<option value="'.$line->id.'">'.$line->label.'</option>';
						}
					}
					print '<select name="fk_departament">'.$options.'</select>';
				}
				else
				{
					if (!empty($user->array_options['options_fk_departament']))
					{

						$getUtil->fetch_departament($user->array_options['options_fk_departament'],'');
						print $getUtil->label;
						print '<input type="hidden" name="fk_departament" value="'.$user->array_options['options_fk_departament'].'">';
					}
					else
						print $langs->trans('NotDefined');
				}
			}
			print '</td></tr>';

			 //date creation
			print '<tr><td width="25%">'.$langs->trans("Date").'</td><td colspan="3">';
			print $object->date_creation ? dol_print_date($object->date_creation,'daytext') : '&nbsp;';
			print '</td></tr>';

			 //date delivery
			 // Date de livraison
			if ($conf->global->ALMACEN_REGISTER_DATEDELIVERY)
			{
				print '<tr><td width="25%" class="fieldrequired">'.$langs->trans("DeliveryDate").'</td><td colspan="2">';
				if (empty($datedelivery))
				{
					if (! empty($conf->global->DATE_LIVRAISON_WEEK_DELAY)) $datedelivery = time() + ((7*$conf->global->DATE_LIVRAISON_WEEK_DELAY) * 24 * 60 * 60);
					else $datedelivery=empty($conf->global->MAIN_AUTOFILL_DATE)?-1:0;
					}
					$form->select_date($object->date_delivery,'re','','','',"crea_commande",1,1);
			  // print '<input id="reday" type="hidden" value="20" name="reday">';
			  // print '<input id="remonth" type="hidden" value="03" name="remonth">';
			  // print '<input id="reyear" type="hidden" value="2013" name="reyear">';
					print "</td></tr>";
				}
			  //description
				print '<tr><td width="25%" class="field">'.$langs->trans("Description").'</td><td colspan="3">';
				print '<textarea wrap="soft" name="description" rows="3" cols="40">'.$object->description.'</textarea>';
				print '</td></tr>';


				print '</table>';

				print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
				print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';

				print '</form>';

			}
		}
	}


	llxFooter();

	$db->close();
	?>