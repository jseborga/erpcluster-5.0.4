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
	require_once(DOL_DOCUMENT_ROOT."/fabrication/class/fabrication.class.php");
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
$objMouvement 	= new MouvementStockext($db);

$entrepot = new Entrepotext($db);
$objuser = new User($db);
$objform = new Solalmacenform($db);
$objpunit = new Productunit($db);
$formfile = new FormFile($db);
$product = new Product($db);
if ($conf->fabrication->enabled)
	$formfabrication=new Fabrication($db);
if ($conf->orgman->enabled)
{
	$objDepartament=new Pdepartamentext($db);
	$objDepartamentuser=new Pdepartamentuserext($db);
}

//vamos a generar y verificar movimientos de almacen
$error=0;
$dateinicial = dol_mktime(0,0,0,1,1,2018);
$filter = " AND t.datem < ".$db->idate($dateinicial)." AND  t.value > 0";
$res = $objMouvement->fetchAll('ASC', 't.datem,t.rowid', 0, 0, array(), 'AND', $filter,false,0);
$objTmp = new Stockmouvementadd($db);
if ($res >0)
{
	$db->begin();
	$lines = $objMouvement->lines;
	foreach ($lines AS $m => $linem)
	{
		if (!$error)
		{
		$restmp = $objTmp->fetch(0,$linem->id);
		if ($restmp>0)
		{
			//echo '<hr>value '.$linem->value.' new '.$objTmp->balance_peps;
			$objTmp->balance_peps = 0;
			$resup = $objTmp->update($user);
			if ($resup <=0)
			{
				$error++;
				echo '<hr>resup '.$resup;
				echo ' linemid '.$linem->id;
				exit;
			}
		}
		}
	}
	//echo '<hr>ERRORINI '.$error;
	if (!$error) $db->commit();
	else $db->rollback();

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

//vamos a generar y verificar movimientos de almacen
$dateinicial = dol_mktime(0,0,0,1,1,2018);
$filter = " AND t.datem> ".$db->idate($dateinicial)." AND  t.value < 0";
$res = $objMouvement->fetchAll('ASC', 't.datem,t.rowid', 0, 0, array(), 'AND', $filter,false,0);
$aUpdate = array();
if ($res>0)
{
	$aNewbalance=array();

	$lines = $objMouvement->lines;
	foreach ($lines AS $j => $line)
	{
		$qtySalida = 0;
		//if ($line->fk_entrepot==11 && $line->fk_product == 195)
		//{
		//	echo '<hr>id '.$line->id.' e '.$line->fk_entrepot.' p '.$line->fk_product.' q '.$line->value;
		//}
		$fk_entrepot = $line->fk_entrepot;
		$datem = $line->datem;
		$qty = $line->value*-1;
		//obtenemos los ingresos de la gestion
		$dateinicial = dol_mktime(0,0,0,1,1,2018);
		$filter = " AND t.datem> ".$db->idate($dateinicial)." AND  t.value > 0";
		$filter.= " AND t.fk_product = ".$line->fk_product." AND t.fk_entrepot = ".$fk_entrepot;
		$res = $objMouvement->fetchAll('ASC', 't.datem,t.rowid', 0, 0, array(), 'AND', $filter,false,0);
		if ($res>0)
		{
			$linesing = $objMouvement->lines;
			foreach ($linesing AS $k => $dataing)
			{
				if (!isset($aNewbalance[$fk_entrepot][$line->fk_product][$dataing->id]))
				{
					$aNewbalance[$fk_entrepot][$line->fk_product][$dataing->id] = $dataing->value;

					$aNewbalancevalue[$fk_entrepot][$line->fk_product][$dataing->id] = $dataing->value_peps;
				}
			}
		}
		/*
		if ($line->fk_entrepot==11 && $line->fk_product == 195)
		{
			echo '<HR>FILTRADO<pre>';
			print_r($aNewbalance[$line->fk_entrepot][$line->fk_product]);
			print_r($aNewbalancevalue[$line->fk_entrepot][$line->fk_product]);
			echo '</pre>';

						//valuacion por el metodo peps
		}
		*/
		$objMouvement = new MouvementStockext($db);
		//echo '<hr>typemet '.$typemethod;
		$res = $objMouvement->method_valuation($fk_entrepot,$datem,$line->fk_product,($typemethod>0?0:''));
		$aIng = $objMouvement->aIng;
		$aSal = $objMouvement->aSal;
		//echo 'INGRESOS<pre>';
		$aIng = $aNewbalance[$line->fk_entrepot][$line->fk_product];
		//if ($line->fk_entrepot==11 && $line->fk_product == 195)
		//	print_r($aIng);
						//vamos a verificar los saldos de cada ingreso
		$aIngtmp = $aIng;
		$aBalancetmp= array();
		//foreach ($aIngtmp AS $jtmp => $datatmp)
		//{
		//	$aBalancetmp[$jtmp]=$datatmp->value;
		//	foreach ($aSal AS $ktmp => $datasal)
		//	{
		//		if ($datasal->fk_parent_line == $jtmp)
		//			$aBalancetmp[$jtmp]+=$datasal->value;
		//	}
		//}
		//echo '<br>balance';
		//print_r($aBalancetmp);
						//actualizamos valor para realizar la salida
		//foreach ((array) $aIngtmp AS $jtmp => $data)
		//{
		//	//$aIng[$jtmp]->balance_peps= $aBalancetmp[$jtmp];
		//}
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
			unset($aSales);
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
						//if ($line->fk_entrepot==11 && $line->fk_product == 195)
						//	ECHO '<HR>VERIFICALINEA '.$j.' |poeps '.$lineing->balance_peps.' |new '.$aNewbalance[$line->fk_entrepot][$line->fk_product][$j];
						if ($line->value > $aNewbalance[$line->fk_entrepot][$line->fk_product][$j])
						{
							$qtySalilda+=$line->value - $aNewbalance[$line->fk_entrepot][$line->fk_product][$j];
							$qtysal+= $qtySalida*-1;
							$qtySalida=0;
						}
						else
							$qtySalida=0;
						$balance_peps = $aNewbalance[$line->fk_entrepot][$line->fk_product][$j];

						if ($balance_peps > 0)
						{
							$value_peps = $aNewbalancevalue[$line->fk_entrepot][$line->fk_product][$j];
							//$value_peps = ($lineing->value_peps_adq?$lineing->value_peps_adq:$lineing->value_peps);
							if ($balance_peps >= $qtysal)
							{
								$aSales[$j]['value'] = $value_peps;
								//if ($line->fk_product == 195 && $line->fk_entrepot == 11)
								//	echo '<hr>cantidadquesaleA '.$qtysal;
								$aSales[$j]['qty'] = $qtysal;
												//////////////////////////////////////////////////////////////////
								$aSales[$j]['id'] = $j;
								$qtyent += $qtysal;
								$lEntrega =false;
								//actualizamos el saldo en stock_mouvement
								//$resmadd = $objMouvementadd->fetch(0,$j);
								$aNewbalance[$line->fk_entrepot][$line->fk_product][$j]-=$qtysal;
								//if ($line->fk_entrepot==11 && $line->fk_product == 195)
								//	echo '<br>seresta '.$qtysal.' nuevosaldo '.$aNewbalance[$line->fk_entrepot][$line->fk_product][$j];
								//$objMouvementadd->balance_peps -= $qtysal;
								//$resmadd = $objMouvementadd->update($user);
								//if ($resmadd<=0)
								//{
								//	$error=102;
								//	setEventMessages($objMouvementadd->error,$objMouvementadd->errors,'errors');
								//}
							}
							else
							{
								$aSales[$j]['value'] = $value_peps;
								$aSales[$j]['qty'] = $aNewbalance[$line->fk_entrepot][$line->fk_product][$j];
								$aSales[$j]['id'] = $j;
								$qtysal-=$aNewbalance[$line->fk_entrepot][$line->fk_product][$j];
								$qtyent+=$aNewbalance[$line->fk_entrepot][$line->fk_product][$j];
								//if ($line->fk_product == 46 && $line->fk_entrepot == 4)
								//if ($line->fk_entrepot==11 && $line->fk_product == 195)
								//echo '<hr>cantidadquesaleB '.$aNewbalance[$line->fk_entrepot][$line->fk_product][$j];exit;
												//actualizamos el saldo en stock_mouvement
								//$resmadd = $objMouvementadd->fetch(0,$lineing->id);
								$aNewbalance[$line->fk_entrepot][$line->fk_product][$j] -= $aNewbalance[$line->fk_entrepot][$line->fk_product][$j];
								//if ($line->fk_product == 46 && $line->fk_entrepot == 4)
								//	echo '<br>seresta TODO '.' nuevosaldo '.$aNewbalance[$line->fk_entrepot][$line->fk_product][$j];

								//$objMouvementadd->balance_peps -= $lineing->balance_peps;
								//$resmadd = $objMouvementadd->update($user);
								//if ($resmadd<=0)
								//{
								//	$error=103;
								//	setEventMessages($objMouvementadd->error,$objMouvementadd->errors,'errors');
								//}
							}
						}
					}
					/*
					if ($line->fk_entrepot==11 && $line->fk_product == 195)
					{
						echo '<pre>entrega';
						print_r($aSales[$j]);
						echo '<br>nuevovalance ';
						print_r($aNewbalance[$line->fk_entrepot][$line->fk_product]);
						echo '</pre>';
					}
					*/
					//para modificar
					foreach ((array) $aSales AS $i => $datanew)
					{
						$aNewupdate[$line->id]['fk_parent_line'] = $i;
						$aNewupdate[$line->id]['qty'] = $datanew['qty'];
						//$aNewupdate[$line->id]['value_peps'] = $datanew['value'];
						$aNewupdate[$line->id]['value_peps'] = $aNewbalancevalue[$line->fk_entrepot][$line->fk_product][$i];
					}
					//se modifica la linea
					/*
					if ($line->fk_entrepot==11 && $line->fk_product == 195)
					{
						echo '<br>RESULTADO PARA '.$line->id;
						print_r($aNewupdate[$line->id]);
					}
					*/
				}
				continue;
			}

			//echo '<hr>COMPARACANTIDADES '.$qty.' '.$qtyent;
			if ($qty != $qtyent)
			{
				echo ' lineid '.$line->id;
				$error=104;
				if ($qty > $qtyent)
				{
					echo '<br>'.$langs->trans('NO existe saldo en almacen para cubrir la entrega de- ').' '.$objProduct->ref.' '.$objProduct->label;
				}
				else
				{
					echo '<br>'.$langs->trans('Se esta entregando en demasia').' '.$objProduct->ref.' '.$objProduct->label;
				}
				exit;
			}
		}
		//echo 'SALIDAS<pre>';
		//print_r($aSales);
		//echo '</pre>';
	}
}
//echo '<hr>FINAL ';


//actualizamos los saldos de gestiones anteriores
/*
echo '<pre>';
print_r($aNewbalance);
echo '<hr>';
print_r($aNewupdate);
echo '<pre>';
*/
$db->begin();
echo '<hr>antesde '.$error;
foreach ($aNewbalance AS $fk_entrepot => $aDatap)
{

	foreach ($aDatap AS $fk_product => $aData)
	{
		foreach ($aData AS $id => $value)
		{
			if (!$error)
			{
				//buscamos y mostramos
				$objMouvement->fetch($id);
				$ress = $objMouvementadd->fetch(0,$id);
				//echo '<hr>ingresos id '.$id .' existeadd '.$ress.' saldoactual '.$objMouvementadd->balance_peps.' saldonuevo '.$value.' '.($value<0?' difnega ':'');
				$objMouvementadd->balance_peps = $value;
				$resup = $objMouvementadd->update($user);
				if($resup<=0)
				{
					$error++;
					echo '<hr>resup '.$resup;
					echo ' ID '.$id;
					exit;
				}
			}
		}
	}
}
//echo '<HR>ACTUALIZANDO SALIDAS '.$error;
foreach ($aNewupdate AS $fk => $aData)
{
	if (!$error)
	{
			//buscamos y mostramos
		$objMouvement = new MouvementStockext($db);
		$resx = $objMouvement->fetch($fk);
		$objMouvementadd->fetch(0,$fk);
		if ($resx)
		{
		}

		//if ($resx && $objMouvement->warehouse_id==11 && $objMouvement->product_id == 195)
		//{
		//	echo '<hr>'.$fk;
		//	echo '<br>qantidadsalida '.$objMouvement->qty;
		//	echo ' | addreg '.$objMouvementadd->qty;
		//}
		$dif = $objMouvementadd->value_peps - $aData['value_peps'];
		//echo '<hr>remplazamos salidas  '.$fk .' salida '.$objMouvement->value.' '.$objMouvementadd->fk_parent_line.' VALORANT '.$objMouvementadd->value_peps.' nuevoparent '.$aData['fk_parent_line'].' newvalue '.$aData['value_peps'].' DIFERENCIA '.$dif;
		$objMouvementadd->fk_parent_line = $aData['fk_parent_line'];
		$objMouvementadd->balance_ueps = $aData['qty']*-1;
		$objMouvementadd->value_peps = $aData['value_peps'];
		//if ($resx && $objMouvement->warehouse_id==4 && $objMouvement->product_id == 46)
		//{
		//	echo ' nuevo '.$objMouvementadd->balance_ueps;
		//}
		$resup = $objMouvementadd->update($user);
		if($resup<=0)
		{
			$error++;
			echo '<hr>resup '.$resup;
			echo ' FK '.$fk;
			exit;
		}
	}
}
echo '<hr>final '.$error;
if (!$error) $db->commit();
else $db->rollback();
exit;



llxFooter();

$db->close();
?>