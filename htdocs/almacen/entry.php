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

require '../../main.inc.php';
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
if (! empty($conf->propal->enabled))
	require DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
if (! empty($conf->projet->enabled)) {
	require DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
	require DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
}
//modulo
require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementtempext.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementtype.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementdocext.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/ctypemouvement.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/contabperiodo.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/lib/almacen.lib.php';

require_once DOL_DOCUMENT_ROOT.'/almacen/class/transf.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/entrepotbanksoc.class.php';

$langs->load("products");
$langs->load("orders");
$langs->load("bills");
$langs->load("stocks");
$langs->load("almacen@almacen");

if (!$user->rights->almacen->creartransfin) accessforbidden();

$action=GETPOST("action");
$cancel=GETPOST('cancel');

// Security check
$id = GETPOST('id')?GETPOST('id'):GETPOST('ref');
$ref = GETPOST('ref');
$idr = GETPOST('idr');
$idreg = GETPOST('idreg');
$stocklimit = GETPOST('stocklimit');
$fieldid = isset($_GET["ref"])?'ref':'rowid';
if ($user->societe_id) $socid=$user->societe_id;
//$result=restrictedArea($user,'produit&stock',$id,'product&product','','',$fieldid);
$inputprice = $conf->global->ALMACEN_MOUVEMENT_INPUT_VALUE_FOR_PRODUCT;
$typemethod = $conf->global->ALMACEN_METHOD_VALUATION_INVENTORY;
//verificamos el periodo
verif_year();

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
include_once DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php';
$hookmanager=new HookManager($db);
$hookmanager->initHooks(array('ordercard'));

$object = new Stockmouvementtempext($db);
$objproduct = new Product($db);
$objentrepot = new Entrepotbanksoc($db);
$objectdoc = new Stockmouvementdocext($db);
$objecttype = new Ctypemouvement($db);
$entrepot = new Entrepot($db);
$period = new Contabperiodo($db);

//recuperamos el id del almacen por defecto
if ($conf->global->ALMACEN_CODE_DEFAULT_STORE)
{
	$entrepot->fetch(0,$conf->global->ALMACEN_CODE_DEFAULT_STORE);
	$idEntrepotdefault = $entrepot->id;
}
//verificamos los periodos activos segun la gestión seleccionada

if ($conf->global->ALMACEN_FILTER_YEAR)
	$filterperiod = " AND t.period_year = ".$_SESSION['period_year'];
$filterperiod.= " AND t.status_al = 1";
$filterperiod.= " AND t.statut = 1";
$res = $period->fetchAll('','',0,0,array(1=>1),'AND',$filterperiod);
$aPeriodopen = array();
if ($res>0)
{
	foreach($period->lines AS $i => $line)
	{
		$aPeriodopen[$line->period_year][$line->period_month] = $line->period_month;
	}
}

/*
 *	Actions
 */

$aItemTransf = array();
$transf = array();
if (! empty($_SESSION['itemTransfe'])) $itemTransf=json_decode($_SESSION['itemTransfe'],true);
if (! empty($_SESSION['transfe'])) $transf=json_decode($_SESSION['transfe'],true);

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
	$idr = $transf['idr'];
	//source
	//$idd = $transf['idd'];
	$idd = 0;
	$label = $transf['label'];
	$datesel = $transf['datesel'];
	$fk_type_mov = $transf['fk_type_mov'];
	$codeinv = '';
	if (! ($idr > 0))
	{
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Warehouse")), null, 'errors');

		$error++;
		$action='transfert';
	}
	if (count($itemTransf)<=0)
	{
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("NumberOfUnit")), null, 'errors');
		$error++;
		$action='transfert';
	}
	if (! $error)
	{
		if ($idr <> $idd)
		{
			//buscamos la numeracion para la transferencia
			$ref = 'PROV';
			if ($ref == 'PROV')
				$numref = $objectdoc->getNextNumRef($soc);
			else
				$numref = $objectdoc->ref;

			$db->begin();
			//creamos el registro principal
			$objectdoc->ref = $numref;
			$objectdoc->entity = $conf->entity;
			$objectdoc->fk_entrepot_from = $idr+0;
			$objectdoc->fk_entrepot_to = $idd+0;
			$objectdoc->fk_departament = $transf['fk_departament']+0;
			$objectdoc->fk_soc = $transf['fk_soc']+0;
			$objectdoc->fk_type_mov = $fk_type_mov+0;
			$objectdoc->ref_ext = $transf['ref_ext'];
			$objectdoc->datem = $datesel;
			$objectdoc->label = $label;
			$objectdoc->date_create = dol_now();
			$objectdoc->date_mod = dol_now();
			$objectdoc->tms = dol_now();
			$objectdoc->model_pdf = 'salidaalm';
			$objectdoc->fk_user_create = $user->id;
			$objectdoc->fk_user_mod = $user->id;
			$objectdoc->statut = 1;
			$res = $objectdoc->create($user);
			if ($res <=0)
			{
				$error++;
				setEventMessages($objectdoc->error,$objectdoc->errors,'errors');
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
						$result=$transf->fetch($id);
						if ($nbpiece <>0)
						{
							$product->load_stock();
							//Load array product->stock_warehouse

							// Define value of products moved
							$pricesrc=0;
							if (isset($product->stock_warehouse[$idr]->pmp))
								$pricesrc=$product->stock_warehouse[$idr]->pmp;
							$priceppp=$pricesrc;

							if ($conf->global->ALMACEN_MOUVEMENT_INPUT_VALUE_FOR_PRODUCT == 1)
							{
								$priceppp = $aData['price']/$aData['qty'];
								$price_peps=$aData['price']/$aData['qty'];
								$price_ueps=$aData['price']/$aData['qty'];
							}
							else
							{
								$priceppp=$aData['price'];
								$price_peps=$aData['price'];
								$price_ueps=$aData['price'];
							}

							$balance_peps = $aData['qty'];
							$balance_ueps = $aData['qty'];


							if ($pricedest<0) $pricedest = $pricedest * (-1);
							//print 'price src='.$pricesrc.', price dest='.$pricedest;exit;

							// Remove stock
							//$result1=$transf->add_transfer($user,$idr,$numref,$nbpiece,1,$label,$pricesrc);
							// Add stock
							$result2=$transf->add_transfer($user,$idr,$numref,$nbpiece,0,$label,$priceppp,$codeinv,$fk_type_mov,$datesel,$balance_peps,$balance_ueps,$price_peps,$price_ueps);
							if ($result2 >= 0)
							{
								//insertamos en la tabla adicional

							//$db->commit();
							}
							else
							{
								$error++;
								setEventMessage($transf->error, 'errors');
							}
						}
					}
					else
					{
						$error++;
						setEventMessages($langs->trans('Error de registro'),null,'errors');
					}
				}
			}
		}
	}
	if (empty($error))
	{
		$db->commit();
		unset($_SESSION['itemTransfe']);
		unset($_SESSION['transfe']);
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

// action delitem
if ($action == 'delitem')
{
	unset($itemTransf[GETPOST('id')]);
	$_SESSION['itemTransfe']=json_encode($itemTransf);
	header("Location: entry.php?action=create");
	exit;
}
// action delitem
if ($action == 'clean')
{
	unset($_SESSION['itemTransfe']);
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
				$itemTransf[$idreg]=array('id'=>$idreg, 'id_product'=>$product->id, 'qty'=>GETPOST("nbpiece"), 'batch'=>$batch,'price'=>GETPOST('price'),'label'=>GETPOST('label'));
				$_SESSION['itemTransfe']=json_encode($itemTransf);
				//$_SESSION["itemTransf"][$product->id] = GETPOST("nbpiece");
				header("Location: entry.php?action=create");
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
		if ($product->fetch((!empty(GETPOST('idprod'))?GETPOST('idprod'):''),(!empty(GETPOST('search_idprod'))?GETPOST('search_id_product'):''))>0)
		{
			//editamos el producto en la session
			if ($product->id>0)
			{
				$itemTransf[$idr]['id_product'] = $product->id;
				$itemTransf[$idr]['qty'] = GETPOST('nbpiece');
				$itemTransf[$idr]['price'] = GETPOST('price');
				$_SESSION['itemTransfe']=json_encode($itemTransf);
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
		header("Location: entry.php?action=create");
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
					if (isset($product->stock_warehouse[GETPOST("id_entrepot_source")]->pmp)) $pricesrc=$product->stock_warehouse[GETPOST("id_entrepot_source")]->pmp;
					$pricedest=$pricesrc;

					// Add stock
					//registro directoa stock_mouvement
					$id_entrepot = GETPOST("id_entrepot_source");
					$label = GETPOST('label');
					$price = $pricedest;
					$movement = 0;
					if ($id_entrepot)
					{
						require_once DOL_DOCUMENT_ROOT .'/product/stock/class/mouvementstock.class.php';

						$op[0] = "+".trim($nbpiece);
						$op[1] = "-".trim($nbpiece);

						$movementstock=new MouvementStock($db);
						$mvid=$movementstock->_create($user,$id,$id_entrepot,$op[$movement],$movement,$price,$label);

						if ($mvid<= 0)
						{
							$error++;
						}
						if (empty($error))
						{
							//creamos registro en stock_mouvement_type
							$objsmt->fk_stock_mouvement = $mvid;
							$objsmt->fk_type_mouvement = GETPOST('fk_type_mouvement');
							$objsmt->tms = dol_now();
							$objsmt->statut = 1;
							$resmt = $objsmt->create($user);
							if ($resmt <= 0) $error++;
						}
					}

					//$result2=$product->correct_stock($user,GETPOST("id_entrepot_source"),$nbpiece,0,GETPOST("label"),$pricedest);

/*					if ($result2 >= 0)
					{
						$db->commit();
						// header("Location: product.php?id=".$product->id);
						// exit;
					}
					else
					{
						setEventMessage($product->error, 'errors');
						$db->rollback();
					}
					*/
				}
			}
			if ($error)
				$db->rollBack();
			else
			{
				$db->commit();
				$_SESSION["itemFe"] = array();
			}
			header("Location: entry.php?action=create");
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
//llxHeader("",$langs->trans("CreateMovementEntry".$product->type),$help_url);
$morejs=array("/almacen/javascript/almacen.js");
llxHeader('',$langs->trans("Transfer"),$help_url,'','','',$morejs,'',0,0);


//$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';
//llxHeader("",$langs->trans("CardProduct".$product->type),$help_url);


// transfer stock
if ($action == 'transfert_stock')
{
	$transf['idr'] = GETPOST('id_entrepot_source');
	$transf['idd'] = GETPOST('id_entrepot_destination');
	$transf['fk_type_mov'] = GETPOST('fk_type_mouvement');
	$transf['label'] = GETPOST('label');
	$transf['fk_departament'] = GETPOST('fk_departament');
	$transf['fk_soc'] = GETPOST('fk_soc');
	$transf['ref_ext'] = GETPOST('ref_ext');
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
	$_SESSION['transfe']=json_encode($transf);
	$form = new Form($db);
	$ret=$form->form_confirm($_SERVER["PHP_SELF"],$langs->trans("InputProduct"),$langs->trans("ConfirmInputProduct",$object->libelle),"confirm_transfert_stock",'',0,2);
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
	$itemTransf=json_decode($_SESSION['itemTransfe'],true);
	//WYSIWYG Editor
	require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';

	print_fiche_titre($langs->trans('CreateMovementEntry'));

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

	//listetitle($sortfield,$sortorder);
	print '<table class="noborder" width="100%">';
	if ($action == 'create')
	{
	//print_liste_field_titre($langs->trans("Product"),"liste.php", "","","","",$sortfield,$sortorder);
	//print_liste_field_titre($langs->trans("Unit"),"liste.php", "","","","",$sortfield,$sortorder);
	//print_liste_field_titre($langs->trans("Quantity"),"liste.php", "","","",'align="right"',$sortfield,$sortorder);
	//print_liste_field_titre($langs->trans("P.U.").' '.$langs->trans('Neto'),"liste.php", "","","",'align="right"',$sortfield,$sortorder);
	//print_liste_field_titre($langs->trans("Movement value").' '.$langs->trans('Neto'),"liste.php", "","","",'align="right"',$sortfield,$sortorder);
	//print_liste_field_titre($langs->trans("Action"),"liste.php", "","","",'align="right"',$sortfield,$sortorder);
		$var= !$var;
		print "<tr $bc[$var]>";
		print '<th>'.$langs->trans('Product').'</th>';
		print '<td nowrap>';
		$i = 1;
		print $form->select_produits_v(GETPOST('idprod'),'idprod','',$conf->product->limit_size,0,-1,2,'',1,'','');
		print '<input type="text" readonly style="border:none;" id="labelproduct" name="labelproduct" size="80%">';
		print 'xx<input type="text" readonly style="border:none;" id="labxxx" name="labxxx" size="80%">';
		print '</td>';
		print '<td>';
		print '<input type="text" readonly style="border:none;" id="unit" name="unit" value="">';
		print '</td>';
		print '</tr>';
		print '<tr>';
		print '<th>'.$langs->trans('Qty').'</th>';
		print '<td align="right"  class="fieldrequired">';
		print '<input type="number" id="nbpiece" min="0" step="any" name="nbpiece" size="10" value="'.GETPOST("nbpiece").'" required>';
		print '</td>';
		print '</tr>';
		print '<tr>';
		$min = '0';
		if ($typemethod>0) $min = '0.00001';

		if($inputprice == 1)
		{
			print '<td>'.$langs->trans('Movement value').' '.$langs->trans('Neto').'</td>';
			print '<td align="right">';
			print '<input type="number" name="price" step="any" min="'.$min.'" value="'.GETPOST("price").'" required>';
			print '</td>';
		}
		else
		{
			print '<td align="right">';
			print $langs->trans("P.U.").' '.$langs->trans('Neto');
			print '</td>';
			print '<td>';
			print '<input type="number" name="price" step="any" min="'.$min.'" value="'.GETPOST("price").'" required>';
			print '</td>';
		}
		print '</tr>';
		print '<tr>';
		//print '<td align="left">';
		//print '<input type="text" name="label" size="40" value="'.GETPOST("label").'">';
		//print '</td>';

		print '<td align="right"  class="fieldrequired">';
		print '<input type="submit" class="button" value="'.$langs->trans('Add').'">';
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

	if ($user->rights->almacen->creartransfin && $action != 'endt')
	{
		print "<div class=\"tabsAction\">\n";
		print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=endt">'.$langs->trans("Createmovement").'</a>';
		print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=clean">'.$langs->trans("Clean").'</a>';
		print '</div>';
	}


	if (count($aItemRow)>0 && $action=="endt")
	{
		//print_titre($langs->trans('Input'));

		print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">'."\n";
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="transfert_stock">';
		print '</br>';

		dol_fiche_head();

		print '<table class="tagtable liste listwithfilterbefore" width="100%">';
		print '<tr>';
		print '<td width="20%" class="fieldrequired">'.$langs->trans("WarehouseEntry").'</td><td width="20%">';
		print $formproduct->selectWarehouses(($_GET["dwid"]?$_GET["dwid"]:(GETPOST('id_entrepot_source')?GETPOST('id_entrepot_source'):$idEntrepotdefault)),'id_entrepot_source','',1);
		print '</td>';
		print '</tr>';
		print '<tr>';
		print '<td width="20%" class="fieldrequired">'.$langs->trans("Type").'</td><td>';
		$filterstatic = " AND t.type = 'E'";
		$res = $objecttype->fetchAll('ASC', 't.label', 0, 0, array(1=>1),'AND',$filterstatic);
		$options = '';
		if ($res>0)
		{
			foreach ($objecttype->lines AS $j => $line)
			{
				$options.= '<option value="'.$line->id.'" '.(GETPOST('fk_type_mouvement') == $line->id?'selected':'').'>'.$line->label.'</option>';
			}
		}
		print '<select name="fk_type_mouvement">'.$options.'</select>';;
		print '</td>';
		print '</tr>';

		print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Date').'</td><td colspan="3">';
		if ($user->rights->almacen->transf->datem)
			$form->select_date(($datesel?$datesel:dol_now()),'di',1,1,'',"crea_commande",1,1);
		else
			print dol_print_date(dol_now());
		print '</td></tr>';
		//unidad ejecutora
		if ($conf->global->ALMACEN_MOUVEMENT_INPUT_DISPLAY_DEPARTAMENT)
		{
			print '<tr>';
			print '<td width="20%">'.$langs->trans('Departament').'/'.$langs->trans("Executing Units").'</td>';
			print '<td colspan="4">';
			print $form->select_departament(GETPOST('fk_departament'),'fk_departament','',0,1);
			print '</td>';
			print '</tr>';
		}
		//proveedor
		if ($conf->global->ALMACEN_MOUVEMENT_INPUT_DISPLAY_SOCIETE)
		{
			print '<tr>';
			print '<td width="20%">'.$langs->trans('Supplier').'</td>';
			print '<td colspan="4">';
			$filtertype = 's.client = 1 OR s.client = 3';
			$filtertype = 's.fournisseur = 1';
			print $form->select_company(GETPOST('fk_soc','int'), 'fk_soc', 's.fournisseur = 1', 'SelectThirdParty');
			print '</td>';
			print '</tr>';
		}
		print '<tr>';
		print '<td width="20%">'.$langs->trans("Document").'</td>';
		print '<td colspan="4">';
		print '<input type="text" name="ref_ext" size="10" value="'.GETPOST("ref_ext").'">';
		print '</td>';
		print '</tr>';

		print '<tr>';
		print '<td width="20%">'.$langs->trans("Label").'</td>';
		print '<td colspan="4">';
		print '<input type="text" name="label" size="40" value="'.GETPOST("label").'">';
		print '</td>';
		print '</tr>';

		print '</table>';
		dol_fiche_end();
		print "<div class=\"tabsAction\">\n";

		print '<center><input type="submit" class="button" value="'.$langs->trans('Save').'">&nbsp;';
		print '<a href="'.$_SERVER['PHP_SELF'].'?action=create" class="button">'.$langs->trans("Cancel").'</a>';
		print '</div>';
		print '</form>';
	}
	//dol_fiche_end();
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

llxFooter();

$db->close();

function listetitle($sortfield,$sortorder)
{
	global $langs,$conf;
	print '<table class="tagtable liste listwithfilterbefore">';
	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Product"),"liste.php", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Unit"),"liste.php", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Quantity"),"liste.php", "","","",'align="right"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("P.U.").' '.$langs->trans('Neto'),"liste.php", "","","",'align="right"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Movement value").' '.$langs->trans('Neto'),"liste.php", "","","",'align="right"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Action"),"liste.php", "","","",'align="right"',$sortfield,$sortorder);
	print '</tr>';
}

function listaentry($aItemRow,$idr=0,$action='',$var=false)
{
	global $conf,$db,$langs,$form,$bc;
	$inputprice = $conf->global->ALMACEN_MOUVEMENT_INPUT_VALUE_FOR_PRODUCT;
	$total = 0;
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
				print $form->select_produits_v($aReg['id_product'],'id_product','',$conf->product->limit_size,0,-1,2,'',1,'','');
				print '</td>';
				print '<td align="right"  class="fieldrequired">';
				print '';
				print '</td>';

				print '<td align="right"  class="fieldrequired">';
				print '<input type="number" step="any" min="0" name="nbpiece" value="'.$aReg['qty'].'" required>';
				print '</td>';

				if($inputprice == 1)
				{
					print '<td></td>';
					print '<td align="right">';
					print '<input type="number" name="price" step="any" min="0" value="'.$aReg["price"].'" required>';
					print '</td>';
				}
				else
				{
					print '<td align="right">';
					print '<input type="number" name="price" step="any" min="0" value="'.$aReg["price"].'" required>';
					print '</td>';
					print '<td></td>';
				}

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
				print '<td width="2%" align="center">';
				print $langs->trans($product->getLabelOfUnit('short'));
				print '</td>';

				print '<td width="5%" align="center">';
				print price(price2num($aReg['qty']));
				print '</td>';
				print '</td>';
				if ($inputprice == 1)
				{
					$pu = $aReg['price'] / $aReg['qty'];
					print '<td  width="5%" align="right">';
					print price(price2num($pu,'MU'));
					print '</td>';
				}
				else
				{
					print '<td  width="5%" align="right">';
					print price(price2num($aReg['price'],'MU'));
					print '</td>';
				}
				if ($inputprice == 1)
				{
					print '<td  width="5%" align="right">';
					print price(price2num($aReg['price'],'MT'));
					print '</td>';
					$total+= $aReg['price'];
				}
				else
				{
					$pricetotal = $aReg['price'] * $aReg['qty'];
					print '<td  width="5%" align="right">';
					print price(price2num($pricetotal,'MT'));
					print '</td>';
					$total+= $pricetotal;
				}
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
					print '<a href="'.DOL_URL_ROOT.'/almacen/transferencia/entry.php?action=delitem&id='.$idreg.'">'.img_picto($langs->trans("Delete"),'delete').'</a>';
				}
				print '</td>';
				print '</tr>';
			}
		}
	}
	//totales
	print '<tr class="liste_total">';
	print '<td colspan="4">'.$langs->trans('Total').'</td>';
	print '<td align="right">'.price(price2num($total,'MT')).'</td>';
	print '</tr>';
}
?>
