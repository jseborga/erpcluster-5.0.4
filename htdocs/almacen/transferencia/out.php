<?php
/* Copyright (C) 2001-2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2004      Eric Seigne          <eric.seigne@ryxeo.com>
 * Copyright (C) 2005      Simon TOSSER         <simon@kornog-computing.com>
 * Copyright (C) 2005-2009 Regis Houssin        <regis.houssin@capnetworks.com>
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
 *	\file       htdocs/product/stock/product.php
 *	\ingroup    product stock
 *	\brief      Page to list detailed stock of a product
 */
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
//require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';

require_once DOL_DOCUMENT_ROOT.'/product/stock/class/entrepot.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/product.lib.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
if (! empty($conf->propal->enabled))
	require DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
if (! empty($conf->projet->enabled)) {
	require DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
	require DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
}
//modulo
require_once(DOL_DOCUMENT_ROOT."/almacen/class/mouvementstockext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/stockmouvementadd.class.php");
require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementtemp.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementtype.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementdocext.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/ctypemouvement.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/lib/almacen.lib.php';

require_once DOL_DOCUMENT_ROOT.'/almacen/class/transf.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/entrepotbanksoc.class.php';

$langs->load("products");
$langs->load("orders");
$langs->load("bills");
$langs->load("stocks");
$langs->load("almacen@almacen");

if (!$user->rights->almacen->transfout->write) accessforbidden();

$action=GETPOST("action");
$cancel=GETPOST('cancel');
// Security check
$id = GETPOST('id')?GETPOST('id'):GETPOST('ref');
$idr = GETPOST('idr');
$idreg = GETPOST('idreg');
$ref = GETPOST('ref');
$stocklimit = GETPOST('stocklimit');

$fieldid = isset($_GET["ref"])?'ref':'rowid';
if ($user->societe_id) $socid=$user->societe_id;
//$result=restrictedArea($user,'produit&stock',$id,'product&product','','',$fieldid);

$typemethod = $conf->global->ALMACEN_METHOD_VALUATION_INVENTORY;
//verificamos el periodo
verif_year($action=='create'?true:false);

$period_year = $_SESSION['period_year'];
$lAddnew = $_SESSION['lAlmacennew'];

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
include_once DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php';
$hookmanager=new HookManager($db);
$hookmanager->initHooks(array('ordercard'));

$object = new Stockmouvementtemp($db);
$objproduct = new Product($db);
$objentrepot = new Entrepotbanksoc($db);
$objectdoc = new Stockmouvementdocext($db);
$objecttype = new Ctypemouvement($db);

//verifica permisos de almacenes
$aFilterent = array();
$aFilterentsol = array();
$filterusersol = '';
$now = dol_now();
if (!$user->admin) list($aFilterent, $filteruser,$aFilterentsol, $filterusersol,$aAreadirect,$fk_areaasign,$filterarea,$aFilterarea, $fk_user_resp,$aExcluded) = verif_accessalm();
/*
 *	Actions
 */

$aItemTransf = array();
$transf = array();
if (! empty($_SESSION['itemTransfo'])) $itemTransf=json_decode($_SESSION['itemTransfo'],true);
if (! empty($_SESSION['transfo'])) $transf=json_decode($_SESSION['transfo'],true);

if ($cancel) $action='';
if ($cancel)
{
	header("Location: ".DOL_URL_ROOT."/almacen/transferencia/liste.php");
	exit;
}
// Set stock limit
if ($action == 'setstocklimit')
{
	$product = new Product($db);
	$result=$product->fetch($id);
	$product->seuil_stock_alerte=$stocklimit;
	$result=$product->update($product->id,$user,1,0,1);
	if ($result < 0)
		setEventMessage($product->error, 'errors');
	$action = '';
}
// Transfer stock from a warehouse to another warehouse
if ($action == "confirm_transfert_stock" && $_REQUEST['confirm'] == 'yes')
{
	$idr = $transf['idr']+0;
	//source
	$idd = $transf['idd']+0;
	//destination
	$idr = 0;
	$label = $transf['label'];
	$datesel = $transf['datesel'];
	$fk_type_mov = $transf['fk_type_mouvement'];
	$codeinv = '';
	if ($idd <= 0)
	{
		setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Warehouse")), 'errors');
		$error++;
		$action='transfert';
	}
	if (count($itemTransf)<=0)
	{
		setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("NumberOfUnit")), 'errors');
		$error++;
		$action='create';
	}
	if (! $error)
	{
		$db->begin();
		if ($idr <> $idd)
		{
			//buscamos la numeracion para la transferencia
			$ref = 'PROV';
			if ($ref == 'PROV')
				$numref = $objectdoc->getNextNumRef($soc);
			else
				$numref = $objectdoc->ref;

			//creamos el registro principal
			$objectdoc->ref = $numref;
			$objectdoc->entity = $conf->entity;
			$objectdoc->fk_entrepot_from = $idr+0;
			$objectdoc->fk_entrepot_to = $idd+0;
			$objectdoc->fk_type_mov = $fk_type_mov+0;
			$objectdoc->datem = $datesel;
			$objectdoc->label = $label;
			$objectdoc->date_create = dol_now();
			$objectdoc->date_mod = dol_now();
			$objectdoc->tms = dol_now();
			$objectdoc->model_pdf = 'outputalm';
			$objectdoc->fk_user_create = $user->id;
			$objectdoc->fk_user_mod = $user->id;
			$objectdoc->statut = 1;
			$res = $objectdoc->create($user);
			if ($res <=0)
			{
				setEventMessages($objectdoc->error,$objectdoc->errors,'errors');
				$error++;
			}
			if (!$error)
			{
				foreach ((array) $itemTransf AS $idreg => $aData)
				{
					$id = $aData['id_product'];
					$nbpiece = $aData['qty'];
					if (is_numeric($nbpiece) && $id)
					{
						$product = new Product($db);
						$transf = new Transf($db);
						$transf->id = $id;
						$result=$product->fetch($id);
						if ($nbpiece <>0)
						{
							$product->load_stock();
							//Load array product->stock_warehouse

							// Define value of products moved
							$pricesrc=0;
							if (isset($product->stock_warehouse[$idr]->pmp))
								$pricesrc=$product->stock_warehouse[$idr]->pmp;
							$pricedest=$pricesrc;

							//print 'price src='.$pricesrc.', price dest='.$pricedest;exit;
							// Remove stock
							$result1=$transf->add_transfer($user,$idd,$numref,$nbpiece,1,$label,$pricesrc,$codeinv,$fk_type_mov,$datesel,0,0,0,0);
							// Add stock
							//$result2=$transf->add_transfer($user,$idr,$numref,$nbpiece,0,$label,$pricedest);
							if ($result1 < 0)
							{
								$error++;
								setEventMessages($transf->error,$transf->errors,'errors');
							}
						}
					}
				}
			}
		}

		if (empty($error))
		{
			$db->commit();
			unset($_SESSION['itemTransfo']);
			unset($_SESSION['transfo']);
			header("Location: liste.php");
			exit;
		}
		else
		{
			$db->rollback();
			header("Location: ".$_SERVER['PHP_SELF']."?action=create");
			exit;
		}
	}
}

// action delitem
if ($action == 'delitem')
{
	//unset($_SESSION['itemTransf'][GETPOST("id")]);
	if (! empty($itemTransf[$idreg])) unset($itemTransf[$idreg]);
	if (count($itemTransf) > 0) $_SESSION['itemTransfo']=json_encode($itemTransf);
	else unset($_SESSION['itemTransfo']);
	header("Location: ".$_SERVER['PHP_SELF']."?action=create");
	exit;
}
// action delitem
if ($action == 'clean')
{
	unset($_SESSION['itemTransfo']);
	header("Location: ".$_SERVER['PHP_SELF']."?action=create");
	exit;
}

//transfer session
if ($action == "transfert_session" && ! $cancel)
{
	if (! GETPOST("nbpiece"))
	{
		setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("NumberOfUnit")), 'errors');
		$error++;
		$action='create';
	}
	else
	{
		//buscamos el producto
		$product = new Product($db);
		$idprod = GETPOST('idprod');
		$search_idprod = GETPOST('search_idprod');
		if (!empty($idprod) || !empty($search_idprod))
		{

			if ($product->fetch((!empty(GETPOST('idprod'))?GETPOST('idprod'):''),(!empty(GETPOST('search_idprod'))?GETPOST('search_idprod'):''))>0)
			{
				if (count(array_keys($itemTransf)) > 0) $idreg=max(array_keys($itemTransf)) + 1;
				else $idreg=1;
				if ($product->id>0)
				{
				//verificamos que sea un producto unico
					foreach ((array) $itemTransf AS $j => $data)
					{
						if ($product->id == $data['id_product'])
							$idreg = $j;
					}
					$itemTransf[$idreg]=array('id'=>$idreg, 'id_product'=>$product->id, 'qty'=>GETPOST("nbpiece"), 'batch'=>$batch);
					$_SESSION['itemTransfo']=json_encode($itemTransf);
					header("Location: ".$_SERVER['PHP_SELF']."?action=create");
					exit;
				}
				else
				{
					setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Product")), 'errors');
					$action = 'create';
				}
			}
			else
			{
				setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Product")), 'errors');
				$action = 'create';
			}
		}
		else
		{
			setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Product")), 'errors');
			$action = 'create';
		}
	}
}

//transfer session
if ($action == "transfert_session_up" && ! $cancel)
{
	if (! GETPOST("nbpiece"))
	{
		setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("NumberOfUnit")), 'errors');
		$error++;
		$action='edit';
	}
	else
	{
		//buscamos el producto
		$product = new Product($db);
		if ($product->fetch((!empty(GETPOST('idprod'))?GETPOST('idprod'):''),(!empty(GETPOST('search_idprod'))?GETPOST('search_idprod'):''))>0)
		{
			//editamos el producto en la session
			if ($product->id>0)
			{
				$itemTransf[$idr]['id_product'] = $product->id;
				$itemTransf[$idr]['qty'] = GETPOST('nbpiece');
				$_SESSION['itemTransfo']=json_encode($itemTransf);
				//$_SESSION["itemTransf"][$product->id] = GETPOST("nbpiece");
				header("Location: ".$_SERVER['PHP_SELF']."?action=create");
				exit;
			}
			else
			{
				setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Product")), 'errors');
				$action = 'create';
			}
		}
		else
		{
			setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Product")), 'errors');
			$action = 'create';
		}
	}
}

if ($action == "transfert_session" && ! $cancel && $abc)
{
	$product = new Product($db);
	$idProduct = '';
	$search_id_product = GETPOST('search_id_product');
	//buscamos el producto
	$res = $product->fetch(0,$search_id_product);
	if ($product->ref == $search_id_product) $idProduct = $product->id;
	$nbpiece = GETPOST('nbpiece');
	if (!$nbpiece)
	{
		setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("NumberOfUnit")), 'errors');
		$error++;
		$action='create';
	}
	else
	{
		$_SESSION["itemFe"][$idProduct] = GETPOST("nbpiece");
		header("Location: ".$_SERVER['PHP_SELF']."?action=create");
		exit;
	}
}
// Transfer stock from a warehouse to another warehouse
if ($action == "entry_stock" && ! $cancel)
{
	if (! (GETPOST("id_entrepot_source") > 0))
	{
		setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Warehouse")), 'errors');
		$error++;
		$action='transfert';
	}
	if (! (GETPOST("fk_type_mouvement") > 0))
	{
		setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Typemouvement")), 'errors');
		$error++;
		$action='transfert';
	}

	if (empty($_SESSION["itemFe"]))
	{
		setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("NumberOfUnit")), 'errors');
		$error++;
		$action='create';
	}

	if (! $error)
	{
		if (GETPOST("id_entrepot_source"))
		{
			$objsmt = new Stockmouvementtype($db);
			$db->begin();
			$aId = array();
			foreach ((array) $_SESSION["itemFe"] AS $id => $nbpiece)
			{
				if (is_numeric($nbpiece) && $id)
				{
					$product = new Product($db);
					$result=$product->fetch($id);
					$product->load_stock();	// Load array product->stock_warehouse

					// Define value of products moved
					$pricesrc=0;
					if (isset($product->stock_warehouse[GETPOST("id_entrepot_source")]->pmp))
						$pricesrc=$product->stock_warehouse[GETPOST("id_entrepot_source")]->pmp;
					else
						$pricesrc=$product->pmp;
					$pricedest=$pricesrc;
					$qty = $nbpiece;

					$aSales = array();
					//valuacion por el metodo peps
					$objMouvement = new MouvementStockext($db);
					$date = dol_now();
					$resmov = $objMouvement->get_value_product(GETPOST("id_entrepot_source"),$date,$id,$qty,$typemethod,$pricesrc,$product);
					if ($resmov <= 0) $error++;

					$aSales = $objMouvement->aSales;
					/*
					$res = $objMouvement->method_valuation(GETPOST("id_entrepot_source"),dol_now(),$id);
					$aIng = $objMouvement->aIng;
					//recorremos los ingresos para realizar la salida correspondiente
					$qtysal = $qty;
					$qtyent = 0;
					if (count($aIng)==0 && empty($typemethod))
					{
						if (!$conf->global->STOCK_ALLOW_NEGATIVE_TRANSFER)
						{
							if($objProduct->stock_warehouse[$object->fk_entrepot]->real < $qty)
							{
								$error++;
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
						foreach ((array) $aIng AS $j => $lineing)
						{
							if ($lineing->balance_peps > 0)
							{
								if ($lineing->balance_peps >= $qtysal)
								{
									$aSales[$lineing->id]['value'] = $lineing->value_peps;
									$aSales[$lineing->id]['qty'] = $qtysal;
									$qtyent += $qtysal;
								//actualizamos el saldo en stock_mouvement
									$resmadd = $objMouvementadd->fetch(0,$lineing->id);
									$objMouvementadd->balance_peps -= $qtysal;
									$resmadd = $objMouvementadd->update($user);
									if ($resmadd<=0)
									{
										$error++;
										setEventMessages($objMouvementadd->error,$objMouvementadd->errors,'errors');
									}
								}
								else
								{
									$aSales[$lineing->id]['value'] = $lineing->value_peps;
									$aSales[$lineing->id]['qty'] = $lineing->balance_peps;
									$qtysal-=$lineing->balance_peps;
									$qtyent+=$lineing->balance_peps;
								//actualizamos el saldo en stock_mouvement
									$resmadd = $objMouvementadd->fetch(0,$lineing->id);
									$objMouvementadd->balance_peps -= $lineing->balance_peps;
									$resmadd = $objMouvementadd->update($user);
									if ($resmadd<=0)
									{
										$error++;
										setEventMessages($objMouvementadd->error,$objMouvementadd->errors,'errors');
									}
								}
							}
							else
							{
								$error++;
								setEventMessages($langs->trans('No existe saldo suficiente para entregar').' '.$objProduct->ref.' '.$objProduct->label,null,'errors');
							}
						}
						if ($qty != $qtyent)
						{
							$error++;
							if ($qty > $qtyent)
							{
								setEventMessages($langs->trans('NO existe saldo en almacen para cubrir la entrega de').' '.$objProduct->ref.' '.$objProduct->label,null,'errors');
							}
							else
							{
								setEventMessages($langs->trans('Se esta entregando en demasia').' '.$objProduct->ref.' '.$objProduct->label,null,'errors');
							}
						}
					}
					*/
					if (!$error)
					{
						$id_entrepot = GETPOST("id_entrepot_source");
						$movement = 1;
					//movimiento de salida
					//salida de producto
						foreach ($aSales AS $fk_stock => $row)
						{
							$type = 1;
							$qtyt = $qty * -1;
							$qtyt = $row['qty'] * -1;

							$op[0] = "+".trim($qty);
							$op[1] = "-".trim($qty);

							$label = GETPOST('label');
							$objMouvement = new MouvementStockext($db);

							$objMouvement->origin->element = ($objectdet->element?$objectdet->element:'solalmacendet');
							$objMouvement->origin->id = $rowid;
							$result = $objMouvement->_create($user,$id,$id_entrepot,$op[$movement],$movement,$pricedest,$label);
							exit;
							if ($result == -1 || $result == 0)
							{
								$error++;
								setEventMessages($objMouvement->error,$objMouvement->errors,'errors');
							}
							else
							{
							//agregamos en la tabla adicional stock_mouvement_add
								$now = dol_now();
								$objMouvementadd->fk_stock_mouvement = $result;
								$objMouvementadd->fk_facture = 0;
								$objMouvementadd->fk_user_create = $user->id;
								$objMouvementadd->fk_user_mod = $user->id;
								$objMouvementadd->fk_parent_line = $fk_stock;
								$objMouvementadd->date_create = $now;
								$objMouvementadd->date_mod = $now;
								$objMouvementadd->tms = $now;
								$objMouvementadd->balance_peps = 0;
								$objMouvementadd->balance_ueps = 0;
								$objMouvementadd->value_peps = $row['value'];
								$objMouvementadd->value_ueps = $price_ueps+0;
								$objMouvementadd->status = 1;
								$resadd = $objMouvementadd->create($user);
								if ($resadd <=0)
								{
									setEventMessages($objMouvementadd->error,$objMouvementadd->errors,'errors');
									$error++;
								}

							//creamos registro en stock_mouvement_type
								$objsmt->fk_stock_mouvement = $result;
								$objsmt->fk_type_mouvement = GETPOST('fk_type_mouvement');;
								$objsmt->tms = dol_now();
								$objsmt->statut = 1;
								$resmt = $objsmt->create($user);
								if ($resmt <= 0)
								{
									$error++;
									setEventMessages($objsmt->error,$objsmt->errors,'errors');
								}
							}
						}
					}
				}
			}
			if ($error)
				$db->rollBack();
			else
			{
				$db->commit();
				$_SESSION["itemFe"] = array();
			}
			header("Location: ".$_SERVER['PHP_SELF']."?action=create");
			exit;
		}
	}
}


/*
 * View
 */

$formproduct=new FormProduct($db);

// if ($ref) $result = $product->fetch('',$ref);
// if ($id > 0) $result = $product->fetch($id);
//$arrayofcss=array('/ventas/css/style.css');
$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';
//llxHeader("",$langs->trans("CreateMovementOut").$product->type,$help_url,'',0,0,'',$arrayofcss);
$morejs=array("/almacen/javascript/almacen.js");
llxHeader('',$langs->trans("CreateMovementOut"),$help_url,'','','',$morejs,'',0,0);

//$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';
//llxHeader("",$langs->trans("CardProduct".$product->type),$help_url);


// transfer stock
if ($action == 'transfert_stock')
{
	$transf['idr'] = GETPOST('id_entrepot_source');
	$transf['idd'] = GETPOST('id_entrepot_destination');
	$transf['fk_type_mouvement'] = GETPOST('fk_type_mouvement');
	$transf['label'] = GETPOST('label');
	if ($user->rights->almacen->transf->datem)
	{
		$dimonth = strlen(GETPOST('dimonth'))==1?'0'.GETPOST('dimonth'):GETPOST('dimonth');
		$diday = strlen(GETPOST('diday'))==1?'0'.GETPOST('diday'):GETPOST('diday');
		$diyear = GETPOST('diyear');
		$dateinisel  = dol_mktime(12, 0, 0, GETPOST('dimonth'), GETPOST('diday'), GETPOST('diyear'));
	}
	else
		$dateinisel = dol_now();
	$transf['datesel'] = $dateinisel;
	$_SESSION['transfo']=json_encode($transf);
	$form = new Form($db);
	$ret=$form->form_confirm($_SERVER["PHP_SELF"],$langs->trans("OutProduct"),$langs->trans("ConfirmOutProduct",$object->libelle),"confirm_transfert_stock",'',0,2);
	if ($ret == 'html') print '<br>';
}
/*
 * habilitamos una sesion para la carga de items de transferencia
 * $_SESSION['itemFe'][$rowid] = 0
*/
/*
 * create transfert
 */
if ($action == "create" || $action=='endt' || $action == 'edit')
{
	$itemTransf=json_decode($_SESSION['itemTransfo'],true);
	//WYSIWYG Editor
	require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';

	print_fiche_titre($langs->trans('CreateMovementOut').' '.$period_year);

	$form = new Formv($db);
	dol_fiche_head();

	if ($action == 'create' || $action == 'edit')
	{
		//	print_titre($langs->trans("InternalMovement"));
		print '<form action="'.$_SERVER["PHP_SELF"].'?id='.$product->id.'" method="post">'."\n";
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		if ($action == 'create')
			print '<input type="hidden" name="action" value="transfert_session">';
		if ($action == 'edit')
		{
			print '<input type="hidden" name="action" value="transfert_session_up">';
			print '<input type="hidden" name="idr" value="'.$idr.'">';
		}
	}

	if (!$lAddnew)
	{
		setEventMessages($langs->trans('It is not allowed to carry out movements in this management'),null,'warnings');
	}

	listetitle($sortfield,$sortorder);

	if ($action == 'create' && $lAddnew)
	{
		print "<tr $bc[$var]>";
		print '<td width="80%">';
		$i = 1;
		print $form->select_produits_v('','idprod','',$conf->product->limit_size,0,-1,2,'',1,'','');
		print '<input type="text" style="border:none;" id="labelproduct" name="labelproduct" value="" readonly>';
		print '</td>';

		print '<td>';
		print '<input type="text" style="border:none;" id="unit" name="unit" value="" readonly>';
		print '</td>';

		print '<td width="15%" align="right"  class="fieldrequired">';
		print '<input type="number" id="nbpiece" step="any" name="nbpiece" size="10" value="'.GETPOST("nbpiece").'">';
		print '</td>';
		print '<td width="5%" align="right"  class="fieldrequired">';
		print '<center><input type="submit" class="button" value="'.$langs->trans('Add').'"></center>';
		print '</td>';
		print '</tr>';
	}

	$aItemRow = $itemTransf;

	listaentry($aItemRow,$idr,$action,$var);

	print '</table>';
	dol_fiche_end();

	if ($action == 'create')
	{
		print '</form>';
	}

	if ($user->rights->almacen->transfout->write && $action != 'endt')
	{
		print "<div class=\"tabsAction\">\n";
		if (count($aItemRow)>0)
		{
			print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=endt">'.$langs->trans("Createmovement").'</a>';
			print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=clean">'.$langs->trans("Clean").'</a>';
		}
		print '</div>';
	}

	if (count($aItemRow)>0 && $action=="endt")
	{
		dol_fiche_head();
		print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">'."\n";
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="transfert_stock">';
		print '</br>';

		print '<table class="noborder" width="100%">';
		print '<tr>';
		print '<td width="20%" class="fieldrequired">'.$langs->trans("WarehouseOut").'</td><td width="20%">';
		print $formproduct->selectWarehouses(($_GET["dwid"]?$_GET["dwid"]:(GETPOST('id_entrepot_destination')?GETPOST('id_entrepot_destination'):$idEntrepotdefault)),'id_entrepot_destination','',1,0,0,'',0,0,array(),'',$aExcluded);
		print '</td>';
		print '</tr>';
		print '<tr>';
		print '<td width="20%" class="fieldrequired">'.$langs->trans("Type").'</td><td>';
		$filterstatic = " AND t.type = 'O'";
		$res = $objecttype->fetchAll('ASC', 't.label', 0, 0, array(1=>1),'AND',$filterstatic);
		$options = '';
		if ($res>0)
		{
			foreach ($objecttype->lines AS $j => $line)
			{
				$options.= '<option value="'.$line->id.'" '.(GETPOST('fk_type_mouvement') == $line->id?'selected':'').'>'.$line->label.'</option>';
			}
		}
		print '<select name="fk_type_mouvement">'.$options.'</select>';

		//print select_type_mouvement($selected,'fk_type_mouvement','',0,1);
		print '</td>';
		print '</tr>';

		print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Date').'</td><td colspan="3">';
		if ($user->rights->almacen->transf->datem)
			$form->select_date(($datesel?$datesel:dol_now()),'di',1,1,'',"crea_commande",1,1);
		else
			print dol_print_date(dol_now());
		print '</td></tr>';

		print '<tr>';
		print '<td width="20%">'.$langs->trans("Label").'</td>';
		print '<td colspan="4">';
		print '<input type="text" name="label" size="40" value="'.GETPOST("label").'">';
		print '</td>';
		print '</tr>';

		print '</table>';
		print '<center><input type="submit" class="button" value="'.$langs->trans('Save').'">&nbsp;';
		print '<a href="'.$_SERVER['PHP_SELF'].'?action=create" class="button">'.$langs->trans("Cancel").'</a>';
		print '</form>';
		dol_fiche_end();
	}
}



/* ************************************************************************** */
/*                                                                            */
/* Barre d'action                                                             */
/*                                                                            */
/* ************************************************************************** */


if (empty($action) && $product->id)
{
	print "<div class=\"tabsAction\">\n";

	if ($user->rights->stock->creer)
	{
		print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$product->id.'&amp;action=correction">'.$langs->trans("StockCorrection").'</a>';
	}

	if ($user->rights->stock->mouvement->creer)
	{
		print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$product->id.'&amp;action=transfert">'.$langs->trans("StockMovement").'</a>';
	}

	print '</div>';
}




/*
 * Contenu des stocks
 */
// print '<br><table class="noborder" width="100%">';
// print '<tr class="liste_titre"><td width="40%">'.$langs->trans("Warehouse").'</td>';
// print '<td align="right">'.$langs->trans("NumberOfUnit").'</td>';
// print '<td align="right">'.$langs->trans("AverageUnitPricePMPShort").'</td>';
// print '<td align="right">'.$langs->trans("EstimatedStockValueShort").'</td>';
// print '<td align="right">'.$langs->trans("SellPriceMin").'</td>';
// print '<td align="right">'.$langs->trans("EstimatedStockValueSellShort").'</td>';
// print '</tr>';

// $sql = "SELECT e.rowid, e.label, ps.reel, ps.pmp";
// $sql.= " FROM ".MAIN_DB_PREFIX."entrepot as e,";
// $sql.= " ".MAIN_DB_PREFIX."product_stock as ps";
// $sql.= " WHERE ps.reel != 0";
// $sql.= " AND ps.fk_entrepot = e.rowid";
// $sql.= " AND e.entity = ".$conf->entity;
// $sql.= " AND ps.fk_product = ".$product->id;
// $sql.= " ORDER BY e.label";

// $entrepotstatic=new Entrepot($db);
// $total=0;
// $totalvalue=$totalvaluesell=0;

// $resql=$db->query($sql);
// if ($resql)
// {
// 	$num = $db->num_rows($resql);
// 	$total=$totalwithpmp;
// 	$i=0; $var=false;
// 	while ($i < $num)
// 	{
// 		$obj = $db->fetch_object($resql);
// 		$entrepotstatic->id=$obj->rowid;
// 		$entrepotstatic->libelle=$obj->label;
// 		print '<tr '.$bc[$var].'>';
// 		print '<td>'.$entrepotstatic->getNomUrl(1).'</td>';
// 		print '<td align="right">'.$obj->reel.($obj->reel<0?' '.img_warning():'').'</td>';
// 		// PMP
// 		print '<td align="right">'.(price2num($obj->pmp)?price2num($obj->pmp,'MU'):'').'</td>'; // Ditto : Show PMP from movement or from product
// 		print '<td align="right">'.(price2num($obj->pmp)?price(price2num($obj->pmp*$obj->reel,'MT')):'').'</td>'; // Ditto : Show PMP from movement or from product
//         // Sell price
// 		print '<td align="right">';
//         if (empty($conf->global->PRODUIT_MUTLI_PRICES)) print price(price2num($product->price,'MU'));
//         else print $langs->trans("Variable");
//         print '</td>'; // Ditto : Show PMP from movement or from product
//         print '<td align="right">';
//         if (empty($conf->global->PRODUIT_MUTLI_PRICES)) print price(price2num($product->price*$obj->reel,'MT')).'</td>'; // Ditto : Show PMP from movement or from product
//         else print $langs->trans("Variable");
// 		print '</tr>'; ;
// 		$total += $obj->reel;
// 		if (price2num($obj->pmp)) $totalwithpmp += $obj->reel;
// 		$totalvalue = $totalvalue + price2num($obj->pmp*$obj->reel,'MU'); // Ditto : Show PMP from movement or from product
//         $totalvaluesell = $totalvaluesell + price2num($product->price*$obj->reel,'MU'); // Ditto : Show PMP from movement or from product
// 		$i++;
// 		$var=!$var;
// 	}
// }
// else dol_print_error($db);
// print '<tr class="liste_total"><td align="right" class="liste_total">'.$langs->trans("Total").':</td>';
// print '<td class="liste_total" align="right">'.$total.'</td>';
// print '<td class="liste_total" align="right">';
// print ($totalwithpmp?price($totalvalue/$totalwithpmp):'&nbsp;');
// print '</td>';
// print '<td class="liste_total" align="right">';
// print price(price2num($totalvalue,'MT'));
// print '</td>';
// print '<td class="liste_total" align="right">';
// if (empty($conf->global->PRODUIT_MUTLI_PRICES)) print ($total?price($totalvaluesell/$total):'&nbsp;');
// else print $langs->trans("Variable");
// print '</td>';
// print '<td class="liste_total" align="right">';
// if (empty($conf->global->PRODUIT_MUTLI_PRICES)) print price(price2num($totalvaluesell,'MT'));
// else print $langs->trans("Variable");
// print '</td>';
// print "</tr>";
// print "</table>";


llxFooter();

$db->close();

function listetitle($sortfield,$sortorder)
{
	global $langs;
	print '<table class="noborder" width="100%">';
	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Product"),"liste.php", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Unit"),"liste.php", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Quantity"),"liste.php", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Action"),"liste.php", "","","","",$sortfield,$sortorder);
	print '</tr>';
}

function listaentry($aItemRow,$idr=0,$action='',$var=false)
{
	global $conf,$db,$langs,$form,$bc;
	foreach ((array) $aItemRow AS $idreg => $aReg)
	{
		if (!empty($idreg))
		{
			$var=!$var;
			$product = new Product($db);
			$product->fetch($aReg['id_product']);
			if ($idr == $idreg && $action == 'edit')
			{
				print "<tr $bc[$var]>";
				print '<td>';
				$i = 1;
				print $form->select_produits_v($aReg['id_product'],'idprod','',$conf->product->limit_size,0,-1,2,'',1,'','');
				print '</td>';
				print '<td>';
				print '';
				print '</td>';
				print '<td align="right"  class="fieldrequired">';
				print '<input name="nbpiece" size="10" value="'.$aReg['qty'].'">';
				print '</td>';
		//print '<td align="right">';
		//print '<input type="number" name="price" size="10" value="'.GETPOST("price").'">';
		//print '</td>';
		//print '<td align="left">';
		//print '<input type="text" name="label" size="40" value="'.GETPOST("label").'">';
		//print '</td>';
				print '<td align="right"  class="fieldrequired">';
				print '<center><input type="submit" class="button" value="'.$langs->trans('Save').'"></center>';
				print '</td>';
				print '</tr>';
			}
			else
			{
				print "<tr $bc[$var]>";
				print '<td width="48%">';
				print $product->ref.': '.$product->label;
				print '</td>';
				print '<td width="2%">';
				print $langs->trans($product->getLabelOfUnit());
				print '</td>';
				print '<td width="5%" align="center">';
				print $aReg['qty'];
				print '</td>';
				print '</td>';
				//print '<td  width="5%" align="right">';
				//print price($aReg['price']);
				//print '</td>';
				//print '</td>';
				//print '<td  width="35%" align="left">';
				//print $aReg['label'];
				//print '</td>';
				print '<td width="5%" align="right">';
				if ($action != 'endt')
				{
					print '<center>';
			//	print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';
					print '<a href="'.$_SERVER['PHP_SELF'].'?action=edit&idr='.$idreg.'">'.img_picto($langs->trans("Edit"),'edit').'</a>';
					print '&nbsp;';
					print '<a href="'.DOL_URL_ROOT.'/almacen/transferencia/out.php?action=delitem&idreg='.$idreg.'">'.img_picto($langs->trans("Delete"),'delete').'</a>';
				}
				print '</td>';
				print '</tr>';
			}
		}
	}
}

?>
