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
 *	\file       htdocs/fabrication/fiche.php
 *	\ingroup    fabrication
 *	\brief      Page fiche fabrication
 */

require("../main.inc.php");

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formv.class.php");

require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
require_once(DOL_DOCUMENT_ROOT."/product/stock/class/entrepot.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
if ($conf->almacen->enabled)
{
	//require_once(DOL_DOCUMENT_ROOT."/almacen/class/commonobj.class.php");
	//require_once(DOL_DOCUMENT_ROOT."/almacen/class/commonobject_.class.php");
	require_once(DOL_DOCUMENT_ROOT."/almacen/local/class/entrepotrelationext.class.php");
	require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacenext.class.php");
	require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacendetext.class.php");
	require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacendetfabricationext.class.php");
}
require_once(DOL_DOCUMENT_ROOT."/product/stock/class/mouvementstock.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/fabrication/class/commandeext.class.php");

require_once(DOL_DOCUMENT_ROOT."/fabrication/class/fabricationext.class.php");
require_once DOL_DOCUMENT_ROOT.'/fabrication/core/modules/fabrication/modules_fabrication.php';
require_once(DOL_DOCUMENT_ROOT."/fabrication/productlist/class/productlist.class.php");
require_once(DOL_DOCUMENT_ROOT."/fabrication/class/fabricationdet.class.php");
require_once(DOL_DOCUMENT_ROOT."/fabrication/class/fabricationcommon.class.php");
require_once(DOL_DOCUMENT_ROOT."/fabrication/units/class/units.class.php");
require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

require_once(DOL_DOCUMENT_ROOT."/core/lib/stock.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/product.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formcompany.class.php");

$langs->load("products");
$langs->load("stocks");
$langs->load("companies");
$langs->load("fabrication@fabrication");

$action=GETPOST('action');
$id =GETPOST('id','int');

$warehouseid = GETPOST("warehouseid");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");
$commandeid = GETPOST("commandeid");
if (! $sortfield) $sortfield="p.ref";
if (! $sortorder) $sortorder="DESC";

$mesg = '';

$object = new Fabricationext($db);
$formproduct=new FormProduct($db);
$objFabrication=new Fabricationext($db);
$formfile = new FormFile($db);
$fabricationcomm = new Fabricationcommon($db);
$objCommande = new Commandeext($db);


if ($id>0)
{
	$object->fetch($id);
}
/*
 * Actions
 */

if ($action == 'builddoc')
// En get ou en post
{
	$id = GETPOST('id');
	$object->fetch($id);
	$object->fetch_thirdparty();
	$model = GETPOST('model');
	if (GETPOST('model'))
	{
		$object->setDocModel($user, GETPOST('model'));
	}
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
	if ($model == 'terminado' && $object->statut != 2)
	{
		$mesg='<div class="error">'.$langs->trans("ErrorOrderunfinishedproduction").'</div>';
		$action="";
	   // Force retour sur page creation
	}
	else
	{
		$result=fabrication_pdf_create($db, $object, $object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);

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
}
// Ajout entrepot
if ($action == 'add' && $user->rights->fabrication->crearop)
{

	$object->ref           = $_POST["ref"];
	$object->ref           = '(PROV)';
	$object->entity        = $conf->entity;
	$object->fk_commande   = $_POST["fk_commande"];
	if (empty($object->fk_commande)) $object->fk_commande = 0;
	$object->description   = $_POST["description"];
	$object->statut        = 0;
	$object->date_creation = dol_now();
	$object->date_delivery = dol_mktime(12, 0, 0, GETPOST('date_deliverymonth'),GETPOST('date_deliveryday'),GETPOST('date_deliveryyear'));
	if (empty($object->ref))
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Ref")), null, 'errors');
	}
	if (empty($object->date_delivery))
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Datedelivery")), null, 'errors');
	}
	if (!$error)
	{
		$id = $object->create($user);
		if ($id > 0)
		{
			$object->fetch($id);
			$object->ref = $object->ref .' '.$id;
			$object->update($user);
			header("Location: fiche.php?id=".$id);
			exit;
		}
		else
		{
			setEventMessages($object->error,$object->errors,'errors');
		}
		$action = 'create';
	}
	else
	{
		//$mesg='<div class="error">'.$langs->trans("ErrorRefRequired").'</div>';
		$action="create";
	   // Force retour sur page creation
	}

}

// Ajout entrepot
if ($action == 'update' && $user->rights->fabrication->crearop)
{
	$object->fetch(GETPOST("id"));

	$object->ref           = $_POST["ref"];
	$object->fk_commande   = $_POST["fk_commande"];
	$object->description   = $_POST["description"];
	$object->date_creation = dol_now();
	$object->date_delivery = dol_mktime(12, 0, 0, GETPOST('date_deliverymonth'),GETPOST('date_deliveryday'),GETPOST('date_deliveryyear'));

	if ($object->ref) {
		$object->update($user);
		header("Location: fiche.php?id=".GETPOST("id"));
		exit;
	}
	else {
		$mesg='<div class="error">'.$langs->trans("ErrorRefRequired").'</div>';
		$action="edit";   // Force retour sur page creation
	}
}

//confirme delete
// Ajout entrepot
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->fabrication->deleteop)
{
	$object->fetch(GETPOST("id"));
	if ($object->id == GETPOST("id"))
	{
		$res = $object->delete($user);
		if ($res > 0)
		{
			header('Location: '.DOL_URL_ROOT.'/fabrication/liste_pedido.php');
			exit;
		}
	}
}

if ($action == 'addline' && $user->rights->fabrication->crearop)
{
	$langs->load('errors');
	$error = false;
	$idprod=GETPOST('idprod', 'int');

	if ((empty($idprod)) && (GETPOST('qty') < 0))
	{
		setEventMessage($langs->trans('ErrorBothFieldCantBeNegative', $langs->transnoentitiesnoconv('UnitPriceHT'), $langs->transnoentitiesnoconv('Qty')), 'errors');
		$error = true;
	}
	if (! GETPOST('qty') && GETPOST('qty') == '')
	{
		setEventMessage($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Qty')), 'errors');
		$error = true;
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

		if (! empty($idprod))
		{
		// Insert line
			$object->fetch(GETPOST('id'));
			$objectdet = new Fabricationdet($db);

		//agregando el producto
			$objectdet->fk_fabrication = $object->id;
			$objectdet->qty = GETPOST('qty');
			$objectdet->fk_product = $idprod;
			$objectdet->price_total = 0;
			$result = $objectdet->create($user);

			if ($result > 0)
			{
				unset($_POST['qty']);
				unset($_POST['idprod']);

		// old method
				unset($_POST['np_desc']);
				unset($_POST['dp_desc']);
				header("Location: fiche.php?id=".$object->id);
				exit;
			}
			else
			{
				setEventMessage($objectdet->error, 'errors');
			}
		}
	}
}

//registrar producto a fabrication
if ($action == 'transferf' && $user->rights->fabrication->crearop)
{
	$object->fetch($_GET['id']);
	$commandeid = $object->fk_commande;
	if (!empty($commandeid))
	{
		$commandedet = new Orderline($db);
		$commandedet->fetch($_GET['pid']);
		$objectdet = new Fabricationdet($db);
		$objectdet->fk_fabrication = $_GET["id"];
		$objectdet->fk_product     = $commandedet->fk_product;
		$objectdet->fk_commandedet = $commandedet->rowid;
		$objectdet->qty            = $commandedet->qty;
		$objectdet->price_total = 0;
		if ($objectdet->fk_product)
		{
			$id = $objectdet->create($user);
			if ($id > 0)
			{
				header("Location: fiche.php?id=".$_GET['id']);
				exit;
			}
			$action = 'create';
			$mesg='<div class="error">'.$objectdet->error.'</div>';
		}
		else {
			$mesg='<div class="error">'.$langs->trans("ErrorRefRequired").'</div>';
			$action="";
	  // Force retour sur page creation
		}
	}
}

//registrar como producto

//CORREGIR RQC
if ($action == 'add_product' && $user->rights->fabrication->crearop)
{
	$commandedet = new Orderline($db);
	$commandedet->fetch($_GET['pid']);
	$object = new Product($db);
	$object->ref = $commandedet->desc;
	$object->libelle = $commandedet->desc;
	$object->entity = $conf->entity;
	$object->label = $commandedet->desc;
	$object->description = $commandedet->desc;
	$object->type = 0;
	$object->tosell = 1;
	$object->tosell = 0;

	$id = $object->create($user);
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
if ($action == 'transferdel' && $user->rights->fabrication->crearop)
{
	$object->fetch($_GET['id']);
	$fabricationid = $object->id;
	if (!empty($fabricationid))
	{
		$fabricationdet = new Fabricationdet($db);
		$fabricationdet->fetch($_GET['fid']);

		if ($fabricationdet->fk_product) {
			$fabricationdet->delete($user);
			header("Location: fiche.php?id=".$_GET['id']);
			exit;
		}
	}
}

/*
 * Confirmation de la validation
 */

// if ($action == 'validate' && $user->rights->fabrication->crearop)
//   {
//     $object = new Fabrication($db);
//     $object->fetch($_GET['id']);
//     //cambiando a validado
//     $object->statut = 1;
//     //update
//     $object->update($user);
//     header("Location: fiche.php?id=".$_GET['id']);
//   }

/*
 * Confirmation de la re validation
 */
if ($action == 'revalidate' && $user->rights->fabrication->crearop)
{
	$object->fetch($_GET['id']);
	//cambiando a validado
	$object->statut = 0;
	//update
	$object->update($user);
	header("Location: fiche.php?id=".$_GET['id']);
}

if ($action == 'confirm_validate' && $_REQUEST["confirm"] == 'yes' && $user->rights->fabrication->crearop)
{
	$object->fetch(GETPOST('id'));
	if ($object->id == GETPOST('id'))
	{
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
		if (empty($object->date_init) || is_null($object->date_init))
			$object->date_init = dol_now();
		$object->ref = $numref;
	//update
		$res = $object->update($user);
		if ($res > 0)
		{
			header("Location: ".$_SERVER['PHP_SELF']."?id=".$_GET['id']);
			exit;
		}
		else
			$mesg='<div class="error">'.$object->error.'</div>';
	}
	else
	{
		$mesg='<div class="error">'.$object->error.'</div>';
	}

	/*
	$idwarehouse=GETPOST('idwarehouse');

	// Check parameters
	if (! empty($conf->global->STOCK_CALCULATE_ON_VALIDATE_ORDER) && $object->hasProductsOrServices(1))
	{
		if (! $idwarehouse || $idwarehouse == -1)
		{
			$error++;
			$mesgs[]='<div class="error">'.$langs->trans('ErrorFieldRequired',$langs->transnoentitiesnoconv("Warehouse")).'</div>';
			$action='';
		}
	}

	if (! $error)
	{
		$result=$object->valid($user,$idwarehouse);
		if ($result	>= 0)
		{
			// Define output language
			$outputlangs = $langs;
			$newlang='';
			if ($conf->global->MAIN_MULTILANGS && empty($newlang) && ! empty($_REQUEST['lang_id'])) $newlang=$_REQUEST['lang_id'];
			if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$object->client->default_lang;
			if (! empty($newlang))
			{
				$outputlangs = new Translate("",$conf);
				$outputlangs->setDefaultLang($newlang);
			}
			if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE)) commande_pdf_create($db, $object, $object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);
		}
	}
	*/
}

//actualiza el pedido de almacen y crea la salida de almacenes
if ($action == 'confirm_updateProduction'  && $_REQUEST["confirm"] == 'yes' && $user->rights->fabrication->closeproduction)
{
	$_POST = $_SESSION['aPost'];
	$object->fetch(GETPOST("id"));
	$objectDet = new Fabricationdet($db);
	$objDetFab = new Solalmacendetfabricationext($db);
	$fk_production = GETPOST("id");
	$fk_entrepot = GETPOST("fk_entrepot");
	//merma
	$aItemDecrease = GETPOST('qty_decrease');
	//producido
	$aItemFirst    = GETPOST('qty_first');
	//no hay segunda
	$aItemSecond   = GETPOST('qty_second');
	if ( $fk_entrepot > 0)
	{
		$objSolAlmacen = new Solalmacenext($db);
		//listamos todos los pedidos de la orden de produccion
		$aSol = $objSolAlmacen->fetch_fabrication($object->id);
		$aAlmacenDet = array();
	 	//materiales padre con cantidad y precios
		foreach((array) $aSol AS $idSolAlm => $aDataAlm)
		{
			//listamos los productos pedidos para obtener el pmp
			$objSolAlmacenDet = new Solalmacendetext($db);
			$aSolDet = $objSolAlmacenDet->list_item($idSolAlm);
			//recorremos el detalle
			foreach((array) $aSolDet AS $i1 => $aDataAlmDet)
			{
				//recorremos la tabla solalmacendetfabrication
				$filter = array(1=>1);
				$filterstatic = " AND t.fk_almacendet =".$aDataAlmDet['id'];
				$resfab = $objDetFab->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic);
				foreach ((array) $objDetFab->lines AS $i2 => $linefab)
				{
					$aAlmacenDet[$linefab->fk_fabricationdet][$aDataAlmDet['fk_product']]['qty'] += $linefab->qty;
					$aAlmacenDet[$linefab->fk_fabricationdet][$aDataAlmDet['fk_product']]['price'] = $linefab->price;
				}
				//recuperamos la lista de productos padres e hijos
				//$objProductList = new Productlist($db);
				//$aProdList = $objProductList->fetch_list(0,$aDataAlmDet['fk_product']);
				//foreach((array) $aProdList AS $i2 => $aDataList)
				//{
				//	$aAlmacenDet[$aDataList['fk_product_father']][$aDataAlmDet['fk_product']]['qty'] += $aDataAlmDet['qty'];
				//	$aAlmacenDet[$aDataList['fk_product_father']][$aDataAlmDet['fk_product']]['price'] = $aDataAlmDet['price'];
				//}
			}
		}
		$db->begin();
		//inicializamos el almacenado
		$error= 0;
		foreach($aItemFirst AS $rowid => $qty_first)
		{
			if ($objectDet->fetch($rowid))
			{
				$price_total = 0;
				//obtenemos los hijos para sumar el costo total
				$aData = $aAlmacenDet[$rowid];
				foreach ((array) $aData AS $i3 => $aRow)
				{
						$price_total += $aRow['price'] * $aRow['qty'];
				}
				$objectDet->qty_first    = $qty_first+0;
				$objectDet->qty_decrease = $aItemDecrease[$rowid]+0;
				$objectDet->qty_second   = $aItemDecrease[$rowid]+0;
				$objectDet->date_end     = dol_now();
				$price_unit  = $price_total / ($objectDet->qty_first + $objectDet->qty_decrease +
					$objectDet->qty_second);
				$objectDet->price  = $price_unit;
				$objectDet->price_total = $price_unit * $objectDet->qty_first;

				if ( !empty($fk_entrepot))
				{
					if ( $objectDet->update($user) > 0 )
					{
						$type = 3;
						//entrada; 0=manual; 2:salida
						$qty = $qty_first;
						$label = $langs->trans("ShipmentAccordingtoProduction")." ".$object->ref;
						$objMouvement = new MouvementStock($db);
						$result = $objMouvement->_create($user,$objectDet->fk_product,$fk_entrepot,
							$qty,$type,$price_unit,$label);
						if ($result == -1 || $result == 0)
						{
							echo 'error de registro';
							exit;
						}
						//sigue procesando
					}
					else
					{
						$error++;
						$action = 'closeproduction';
						$_GET["id"] = $_POST["id"];
						$mesg = '<div class="error">'.$objectdet->error.'</div>';
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
				//analizar si no existe el producto
			}
		}
		//cerramos definitivamente
		$object->statut = 2;
		$object->date_finish = dol_now();
		$res = $object->update($user);
		if ($res <=0) $error++;

		if (empty($error))
		{
			$db->commit();
			header("Location: fiche.php?id=".$object->id);
			exit;
		}
		else
			$db->rollback();
	}
	else
	{
		$action = 'closeproduction';
		$_GET["id"] = $_POST["id"];
		$mesg = '<div class="error">'.$langs->trans('Error, seleccione el almacen destino').'</div>';
	}
}
if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}

//armamos los pedidos
$filter = "";
$res = $objCommande->fetchAll('','',0,0,array(),'AND',$filter);
$optionsCommande = '<option value="" >'.'</option>';;
if ($res >0)
{
	$fk_commande = GETPOST('fk_commande')?GETPOST('fk_commande'):($commandeid?$commandeid:$object->fk_commande);
	$lines = $objCommande->lines;
	foreach ($lines AS $j => $line)
	{
		$selected = '';
		if ($fk_commande == $line->id) $selected = ' selected';
		$optionsCommande.= '<option value="'.$line->id.'" '.$selected.'>'.$line->ref.'</option>';
	}
}
/*
 * View
 */

$productstatic = new Product($db);
$form          = new Formv($db);
$formcompany   = new FormCompany($db);
$objUnit       = new Units($db);

$arrayofjs=array('/fabrication/javascript/fabrication.js');
$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';
llxHeader("",$langs->trans("WarehouseCard"),$help_url,"","","",$arrayofjs);

// $arrayofcss=array();
// top_htmlhead($head,$langs->trans("Almacen"),0,0,$arrayofjs,$arrayofcss);

if ($action == 'create')
{
	$objCommande = new Commande($db);
	if (!empty($commandeid))
	{
		$objCommande->fetch($commandeid);
		$datedelivery = $objCommande->date_livraison;
	}
	print_fiche_titre($langs->trans("NewOrderProduction"));

	print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="type" value="'.$type.'">'."\n";

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

	// Ref
	print '<tr><td class="fieldrequired">'.$langs->trans('Ref').'</td><td colspan="2">'.$langs->trans("Draft").'</td></tr>';

	//print '<tr><td width="25%" class="fieldrequired">'.$langs->trans("Ref").'</td><td colspan="3"><input name="ref" size="12" value=""></td></tr>';

	//Pedido
	print '<tr><td >'.$langs->trans("Pedido").'</td><td colspan="3">';
	//print $objFabrication->select_commande(GETPOST('commandeid'),'fk_commande','',!$disabled,!$disabled,$disabled);
	print '<select name="fk_commande">'.$optionsCommande.'</select>';
	print '</td></tr>';

	//date creation
	print '<tr><td >'.$langs->trans("Date").'</td><td colspan="3">';
	print date('d/m/Y');
	print '</td></tr>';

	//date delivery
	// Date de livraison
	print '<tr><td class="fieldrequired">'.$langs->trans("DeliveryDate").'</td><td colspan="2">';
	if (empty($datedelivery))
	{
		if (! empty($conf->global->DATE_LIVRAISON_WEEK_DELAY)) $datedelivery = time() + ((7*$conf->global->DATE_LIVRAISON_WEEK_DELAY) * 24 * 60 * 60);
		else $datedelivery=empty($conf->global->MAIN_AUTOFILL_DATE)?-1:0;
	}
	$form->select_date($datedelivery,'date_delivery','','','',"crea_commande",1,1);
	print "</td></tr>";

	//description
	print '<tr><td width="25%" class="field">'.$langs->trans("Description").'</td><td colspan="3">';
	print '<textarea wrap="soft" rows="3" cols="40"></textarea>';
	print '</td></tr>';

	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

	print '</form>';
}
else
{
	//edit
	if ($id)
	{
		dol_htmloutput_mesg($mesg);

		$objFabDet = new Fabricationdet($db);
		$commande = new Commande($db);

		if ($result < 0)
		{
			dol_print_error($db);
		}
		//verificamos si existe productos enproduccion
		$resultDet = $objFabDet->fetch_search($_GET['id']);
		//buscando si existe pedido en tabla sol_almacen
		$sql = "SELECT rowid AS id, statut FROM ".MAIN_DB_PREFIX."sol_almacen ";
		$sql.= " WHERE fk_fabrication = ".$object->id;
		$resql = $db->query($sql);
		$objalm = $db->fetch_object($resql);

		if ($action <> 'edit' && $action <> 're-edit')
		{
		//$head = fabrication_prepare_head($object);

			dol_fiche_head($head, 'card', $langs->trans("Fabrication"), 0, DOL_URL_ROOT.'/fabrication/img/production',1);


		// Confirmation de la validation
			if ($action == 'validate')
			{
				$ref = substr($object->ref, 1, 4);
				if ($ref == 'PROV')
				{
					$numref = $object->getNextNumRef($soc);
				}
				else
				{
					$numref = $object->ref;
				}
				if (!empty($numref))
				{
					//$form = new Form($db);
					$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Confirmvalidate"),$langs->trans("ConfirmValidateOrderFabrication",$object->libelle).' '.$numref,"confirm_validate",'',1,2);
					if ($ret == 'html') print '<br>';
				}
			}

			// Confirm update produccion
			if ($action == 'updateProduction')
			{
				//$form = new Form($db);
				$_SESSION['aPost'] = $_POST;
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Closeproductionprocess"),$langs->trans("Confirmcloseproductionprocess",$object->libelle),"confirm_updateProduction",'',0,2);
				if ($ret == 'html') print '<br>';
			}

		// Confirm delete third party
			if ($action == 'delete')
			{
				//$form = new Form($db);
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("DeleteOrderFabrication"),$langs->trans("ConfirmDeleteOrderFabrication",$object->libelle),"confirm_delete",'',0,2);
				if ($ret == 'html') print '<br>';
			}

			print '<table class="border" width="100%">';

			// Ref
			print '<tr><td width="25%">'.$langs->trans("Ref").'</td><td colspan="3">';
			print $object->ref;
			//print $form->showrefnav($object,'id','',1,'rowid','libelle');
			print '</td>';
			//pedido
			print '<tr><td>'.$langs->trans("Pedido").'</td><td colspan="3">';
			$commande->fetch($object->fk_commande);
			print $commande->ref;
			print '</td></tr>';
			//fecha creacion
			print '<tr><td>'.$langs->trans("Date").'</td><td colspan="3">';
			print $object->date_creation ? dol_print_date($object->date_creation,'daytext') : '&nbsp;';
			print '</td></tr>';

			//fecha delivery
			print '<tr><td>'.$langs->trans("DateDelivery").'</td><td colspan="3">';
			print $object->date_delivery ? dol_print_date($object->date_delivery,'daytext') : '&nbsp;';
			print '</td></tr>';

			// Description
			print '<tr><td valign="top">'.$langs->trans("Description").'</td><td colspan="3">'.nl2br($object->description).'</td></tr>';


			// Statut
			print '<tr><td>'.$langs->trans("Status").'</td><td colspan="3">'.$object->getLibStatut(3).'</td></tr>';


			print "</table>";

			print '</div>';


			/* ************************************************************************** */
			/*                                                                            */
			/* Barre d'action                                                             */
			/*                                                                            */
			/* ************************************************************************** */

			print "<div class=\"tabsAction\">\n";

			if ($action == '')
			{
				if ($user->rights->fabrication->crearop && $object->statut == 0)
					print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

				if ($user->rights->fabrication->deleteop && empty($objalm->id))
					print '<a class="butActionDelete" href="'.$_SERVER['PHP_SELF'].'?action=delete&id='.$object->id.'">'.$langs->trans("Delete").'</a>';
				else
					print '<a class="butActionRefused" href="#">'.$langs->trans("Delete").'</a>';
			  // Valid
				if ($object->statut == 0 && $resultDet)
				//&& $numlines > 0)
				//&& $user->rights->fabrication->valider)
				{
					print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=validate">'.$langs->trans('Validate').'</a>';
				}
			  // ReValid
				if ($object->statut == 1 && empty($objalm->id))
				//&& $numlines > 0)
				//&& $user->rights->fabrication->valider)
				{
					print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=revalidate">'.$langs->trans('Return').'</a>';
				}
			  // Crear Pedido Almacen
				if ($conf->almacen->enabled)
				{
					if ($object->statut == 1)
				//&& $numlines > 0)
				//&& $user->rights->fabrication->valider)
					{
						if (empty($objalm))
						{
							print '<a class="butAction" href="'.DOL_URL_ROOT.'/almacen/fiche.php?fk_fabrication='.$object->id.'&amp;action=create">'.$langs->trans('CreateOrderAlm').'</a>';
						}
						else
						{
							print '<a class="butAction" href="'.DOL_URL_ROOT.'/almacen/fiche.php?id='.$objalm->id.'">'.$langs->trans('OpenOrder').'</a>';
						}
					}
				}
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("CreateOrderAlm")."</a>";

			  //Finalizar orden de produccion
				if ($object->statut == 1 && $objalm->statut == 2 && $user->rights->fabrication->crearop)
				{
				  //			      print '<a class="butAction" href="'.DOL_URL_ROOT.'/fabrication/fiche.php?fk_fabrication='.$object->id.'&amp;action=create">'.$langs->trans('CreateOrder').'</a>';
					print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=closeproduction">'.$langs->trans('CloseProduction').'</a>';

				}


			}

			print "</div>";

			/* ************************************************************************** */
			/*                                                                            */
			/* Affichage de la liste des produits fabrication                           */
			/*                                                                            */
			/* ************************************************************************** */
			print '<br>';
			dol_fiche_head($head, 'card', $langs->trans("ListeProductFabrication"), 0, 'stock');

			if ($action == "closeproduction" && $object->statut == 1)
			{
				$objectEntrepot = new Entrepotrelationext($db);
				print '<form id="frmFabrication" name="frmFabrication" action="fiche.php" method="POST">';
				print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
				print '<input type="hidden" name="action" value="updateProduction">';
				print '<input type="hidden" name="id" value="'.$object->id.'">';
				// Entrepot Almacen
				print '<table class="noborder" width="100%">';
				print '<tr><td width="10%" class="fieldrequired">'.$langs->trans('DestinationWarehouse').'</td><td colspan="3">';
				print $objectEntrepot->select_padre('','fk_entrepot',1);
				print '</td></tr>';
				print '</table>';
			}
			print '<table class="noborder" width="100%">';
			print "<tr class=\"liste_titre\">";
			print_liste_field_titre($langs->trans("Product"),"", "p.ref","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("Label"),"", "p.label","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("Unit"),"", "","","","");

			print_liste_field_titre($langs->trans("Planif"),"", "cd.qty","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("Decrease"),"", "cd.qty","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("Quantityprod"),"", "cd.qty_decrease","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
		// print_liste_field_titre($langs->trans("Second"),"", "cd.qty_first","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("DateEnd"),"", "cd.qty_second","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);

			print_liste_field_titre($langs->trans("Select"),"", "cd.rowid","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
			if ($user->rights->stock->mouvement->creer) print '<td>&nbsp;</td>';
			if ($user->rights->stock->creer)            print '<td>&nbsp;</td>';
			print "</tr>";

			$totalunit=0;
			$totalvalue=$totalvaluesell=0;

			$sql = "SELECT p.rowid as rowid, cd.fk_product, p.ref, p.label as produit, p.fk_product_type as type, p.pmp as ppmp, p.price, p.price_ttc, p.fk_unit, ";
			$sql.= " fd.qty as qty, fd.rowid AS frowid, fd.fk_commandedet, qty_decrease, qty_first, qty_second, fd.date_end, ";
			$sql.= " cd.description ";
			$sql.= " FROM ".MAIN_DB_PREFIX."fabricationdet fd ";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product p ON fd.fk_product = p.rowid ";
			$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."commandedet cd ON fd.fk_commandedet = cd.rowid ";
			$sql.= " WHERE fd.fk_fabrication = '".$object->id."'";
			// We do not show if stock is 0 (no product in this warehouse)

			$sql.= $db->order($sortfield,$sortorder);

			dol_syslog('List products sql='.$sql);
			$resql = $db->query($sql);
			if ($resql)
			{
				$num = $db->num_rows($resql);
				$i = 0;
				$var=True;
				while ($i < $num)
				{

					$objp = $db->fetch_object($resql);
					$arrayId[$objp->fk_commandedet] = $objp->fk_commandedet;
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
					$productstatic->fetch($objp->rowid);
					//$productstatic->id=$objp->rowid;
					//$productstatic->ref=$objp->ref;
					//$productstatic->type=$objp->type;
					print $productstatic->getNomUrl(1,'stock',16);
					print '</td>';
					print '<td>'.$objp->produit.(!empty($objp->description)?' '.$objp->description:'').'</td>';
					print '<td>'.$productstatic->getLabelOfUnit($objp->fk_unit).'</td>';
				//unidad
				//echo $objp->fk_product;
					//$objProdList = new Productlist($db);
					//$objProdList->fetch_product($objp->fk_product);
					//if ($objProdList->fk_product_father == $objp->fk_product)
					//{
					//	$objUnit->fetch($objProdList->fk_unit_father);
					//	print '<td>'.$objUnit->description.'</td>';
					//}
					//else
					//	print '<td>&nbsp;</td>';

					print '<td align="right">'.$objp->qty.'</td>';
					if($action=="closeproduction" && $object->statut == 1)
					{
						print '<td align="right">';
						print '<input id="qty'.$objp->frowid.'" type="hidden" name="qty['.$objp->frowid.']" value="'.$objp->qty.'">';
						print '<input id="rowidd" type="hidden" name="rowidd['.$objp->frowid.']" value="'.$objp->frowid.'">';

						//print '<input type="text" id="qty_decrease'.$objp->frowid.'" name="qty_decrease['.$objp->frowid.']" value="'.$objp->qty_decrease.'" size="8" onfocus="javascript:this.select();" onkeyup="javascript: verifFabrication('.$objp->frowid.');">';
						print '<input type="text" id="qty_decrease'.$objp->frowid.'" name="qty_decrease['.$objp->frowid.']" value="'.$objp->qty_decrease.'" size="8" >';
						print "</td>";
						print '<td align="right">';
						//print '<input type="text" id="qty_first'.$objp->frowid.'" name="qty_first['.$objp->frowid.']" value="'.$objp->qty.'" size="8" onfocus="javascript:this.select();" onkeyup="javascript: verifFabrication('.$objp->frowid.');">';
						print '<input type="text" id="qty_first'.$objp->frowid.'" name="qty_first['.$objp->frowid.']" value="'.$objp->qty.'" size="8" >';
						print "</td>";
						/* print '<td align="right">'; */
						/* print '<input type="text" name="qty_second['.$objp->frowid.']" value="'.$objp->qty_second.'" size="8">'; */
						/* print "</td>"; */
						print '<td align="right">';
						/* print '<input type="text" name="date_end['.$objp->frowid.']" value="'.$objp->date_end.'" size="8">'; */
						print "</td>";

					}
					else
					{
						print '<td align="right">'.$objp->qty_decrease.'</td>';
						print '<td align="right">'.$objp->qty_first.'</td>';
					//    print '<td align="right">'.$objp->qty_second.'</td>';
						print '<td align="right">'.dol_print_date($objp->date_end,'daytext').'</td>';
					}
					$totalunit+=$objp->qty;


					if ($user->rights->fabrication->crearop && $object->statut == 0)
					{
						print '<td align="right"><a href="'.DOL_URL_ROOT.'/fabrication/fiche.php?id='.$object->id.'&amp;fid='.$objp->frowid.'&amp;action=transferdel">';
						print img_picto($langs->trans("Delete"),'stcomm-1.png').' '.$langs->trans("Delete");
						print "</a></td>";
					}
					else
					{
						print '<td></td>';
					}
					print "</tr>";
					$i++;
				}
				$db->free($resql);

				//				print '<tr class="liste_total"><td class="liste_total" colspan="2">'.$langs->trans("Total").'</td>';
				 //print '<td class="liste_total" align="right">'.$totalunit.'</td>';
				 //print '<td class="liste_total">&nbsp;</td>';

				 //print '</tr>';

			}
			else
			{
				dol_print_error($db);
			}
			print "</table>\n";
			if ($action=="closeproduction" && $object->statut == 1)
			{
				print '<center><br><input type="submit" class="button" value="'.$langs->trans("Close").'"></center>';
				print '</form>';
			}

			/* ************************************************************************** */
			/*                                                                            */
			/* Affichage de la liste des produits de l'entrepot                           */
			/*                                                                            */
			/* ************************************************************************** */

			//documents
			if ($object->statut>=1 && $action != "closeproduction")
			{

				print '<table width="100%"><tr><td width="50%" valign="top">';
				print '<a name="builddoc"></a>'; // ancre

				/*
				 * Documents generes
				 */
				$filename=dol_sanitizeFileName($object->ref);
				$filedir=$conf->fabrication->dir_output . '/' . dol_sanitizeFileName($object->ref);
				$urlsource=$_SERVER['PHP_SELF'].'?id='.$object->id;
				$genallowed=$user->rights->fabrication->crearop;
				$delallowed=$user->rights->fabrication->deleteop;
				print '<br>';
				print $formfile->showdocuments('fabrication',$filename,$filedir,$urlsource,$genallowed,$delallowed,$object->modelpdf,1,0,0,28,0,'','','',$soc->default_lang,$hookmanager);
				$somethingshown=$formfile->numoffiles;
				print '</td></tr></table>';

			}
			print "</div>";
			if ($object->fk_commande && empty($objalm->id))
			{
				print '<br>';
				dol_fiche_head($head, 'card', $langs->trans("ListeProductPedido"), 0, 'stock');
				if (!empty($arrayId))
					$listId = implode(',',$arrayId);
				print '<table class="noborder" width="100%">';
				print "<tr class=\"liste_titre\">";
				print_liste_field_titre($langs->trans("Product"),"", "p.ref","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
				print_liste_field_titre($langs->trans("Label"),"", "p.label","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
				print_liste_field_titre($langs->trans("Units"),"", "cd.qty","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans("Select"),"", "cd.rowid","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
				if ($user->rights->stock->mouvement->creer) print '<td>&nbsp;</td>';
				if ($user->rights->stock->creer)            print '<td>&nbsp;</td>';
				print "</tr>";

				$totalunit=0;
				$totalvalue=$totalvaluesell=0;

				$sql = "SELECT p.rowid as rowid, p.ref, p.label as produit, p.fk_product_type as type, p.pmp as ppmp, p.price, p.price_ttc,";
				$sql.= " cd.description AS description, cd.rowid AS prowid, cd.qty as qty";
				$sql.= " FROM ".MAIN_DB_PREFIX."commandedet AS cd ";
				$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product AS p ON cd.fk_product = p.rowid ";
				$sql.= " WHERE ";
				$sql.= " cd.fk_commande = '".$object->fk_commande."'";	// We do not show if stock is 0 (no product in this warehouse)

				//cd.fk_product = p.rowid";
				if (!empty($listId)) $sql .= " AND (cd.rowid NOT IN ($listId) OR p.rowid IS NULL)";

				$sql.= $db->order($sortfield,$sortorder);
				dol_syslog('List products sql='.$sql);
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
						$productstatic->id=$objp->rowid;
						$productstatic->ref=$objp->ref;
						$productstatic->type=$objp->type;
						print $productstatic->getNomUrl(1,'stock',16);
						print '</td>';
						print '<td>'.$objp->description.'</td>';

						print '<td align="right">'.$objp->qty.'</td>';
						$totalunit+=$objp->qty;

						if ($user->rights->fabrication->crearop && $object->statut == 0)
						{
							if (empty($objp->rowid))
							{
								print '<td align="right"><a href="'.DOL_URL_ROOT.'/fabrication/fiche.php?id='.$object->id.'&amp;pid='.$objp->prowid.'&amp;action=add_product">';
								print img_picto($langs->trans("StockMovement"),'uparrow.png').' '.$langs->trans("RegisterProduct");
								print "</a></td>";
							}
							else
							{
							//if ($user->rights->stock->mouvement->creer)
							//{
								print '<td align="right"><a href="'.DOL_URL_ROOT.'/fabrication/fiche.php?id='.$object->id.'&amp;pid='.$objp->prowid.'&amp;action=transferf">';
								print img_picto($langs->trans("StockMovement"),'uparrow.png').' '.$langs->trans("StockFabrication");
								print "</a></td>";
							//}
							}
							print "</tr>";
							$i++;
						}
					}
					$db->free($resql);

					print '<tr class="liste_total"><td class="liste_total" colspan="2">'.$langs->trans("Total").'</td>';
					print '<td class="liste_total" align="right">'.$totalunit.'</td>';
					print '<td class="liste_total">&nbsp;</td>';
					print '</tr>';

				}
				else
				{
					dol_print_error($db);
				}
				print "</table>\n";
			}
			else
			{
				if ($object->statut == 0)
				{
					print '<table id="tablelines" class="noborder" width="100%">';

					if ($action != 'editline')
					{

						$var=true;

						//if ($conf->global->MAIN_FEATURES_LEVEL > 1)
						//{
							// Add free or predefined products/services
						//	$object->formAddObjectLine(1,$mysoc,$soc,$hookmanager);
						//}
						//else
						//{
					// Add predefined products/services
							if (! empty($conf->product->enabled) || ! empty($conf->service->enabled))
							{
								$var=!$var;
								$fabricationcomm->formAddPredefinedProduct_sd(0,$mysoc,$soc,$hookmanager,$object->id);
							}
						//}

						$parameters=array();
					$reshook=$hookmanager->executeHooks('formAddObjectLine',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
				}
			}
		}

	}
	/*
	 * Edition fiche
	 */
	if (($action == 'edit' || $action == 're-edit') && 1)
	{
		print_fiche_titre($langs->trans("Orderproductionedit"), $mesg);

		print '<form action="fiche.php" method="POST">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="update">';
		print '<input type="hidden" name="id" value="'.$object->id.'">';

		print '<table class="border" width="100%">';

		// Ref
		print '<tr><td width="20%" class="fieldrequired">'.$langs->trans("Ref").'</td><td colspan="3"><input name="ref" size="20" value="'.$object->ref.'"></td></tr>';
		//pedido
		print '<tr><td width="20%">'.$langs->trans("Pedido").'</td><td colspan="3">';
		//print $objFabrication->select_commande($object->fk_commande,'fk_commande','',!$disabled,!$disabled,$disabled);
		print '<select name="fk_commande">'.$optionsCommande.'</select>';
		print '</td></tr>';
		//date creation
		print '<tr><td >'.$langs->trans("Date").'</td><td colspan="3">';
		print $object->date_creation;
		print '</td></tr>';

		//date delivery
		print "<tr><td>".$langs->trans("DeliveryDate").'</td><td colspan="2">';
		$datedelivery = $object->date_delivery;
		if (empty($datedelivery))
		{
			if (! empty($conf->global->DATE_LIVRAISON_WEEK_DELAY)) $datedelivery = time() + ((7*$conf->global->DATE_LIVRAISON_WEEK_DELAY) * 24 * 60 * 60);
			else $datedelivery=empty($conf->global->MAIN_AUTOFILL_DATE)?-1:0;
		}
		$form->select_date($datedelivery,'date_delivery','','','',"crea_commande",1,1);
		print "</td></tr>";

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
