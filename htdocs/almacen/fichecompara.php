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
$filter = " AND t.datem> ".$db->idate($dateinicial)." AND  t.value > 0";
$filter.= " AND a.balance_peps = 0 ";
echo '<hr>res '.$res = $objMouvement->fetchAll('ASC', 't.datem,t.rowid', 0, 0, array(), 'AND', $filter,false,0);
$aa=0;
$aUpdate = array();
$db->begin();
if ($res>0)
{
	$aNewbalance=array();

	$lines = $objMouvement->lines;
	foreach ($lines AS $j => $line)
	{
		$qtySalida = 0;
		//if ($line->fk_entrepot==11 && $line->fk_product == 195)
		//buscamos las salidas para comprobar
		$filter = " AND a.fk_parent_line =  ".$line->id;
		$res = $objMouvement->fetchAll('ASC', 't.datem,t.rowid', 0, 0, array(), 'AND', $filter,false,0);
		if ($line->id == 12699) echo '<br>res '.$res;
		$objProduct->fetch($line->fk_product);
		if ($res>0)
		{
			$linessal = $objMouvement->lines;
			foreach ($linessal AS $k => $linesal)
			{
				$res1 = $objMouvement->fetch($linesal->fk_stock_mouvement);
				if ($line->id == 12699)
				{
					echo '<hr>viene de '.$linesal->fk_stock_mouvement;
					echo '<br>x1 '.$res1;
				}
			//	echo '<hr>id '.$line->id.' value= '.$line->value.' balance_peps= '.$line->balance_peps.' salida '.$linesal->id.' '.$linesal->value;
				if ($res1>0)
					$qtySalida+=$objMouvement->qty;
				else
				{
					echo '<hr>error';
					exit;
				}
			}
		}
		//echo ' total salida = '.$qtySalida;
		$balance = $line->value+$qtySalida;
		if ($balance >0)
		{
			$aa++;
			echo '<br>veces '.$aa;
			echo '<hr>'.$objProduct->ref.' '.$objProduct->label;
			echo '<br>filter '.$filter;
			echo ' res1= '.$res1;
			echo '<br>'.$line->id.' value= '.$line->value.' qtysal= '.$qtySalida.' balance= '.$balance;
			$ress =$objMouvementadd->fetch (0,$line->id);
			if ($ress>0)
			{
				//actualizamos
				$objMouvementadd->balance_peps = $line->value;
				echo '<br>se actualiza en '.$line->id.' '.$objMouvementadd->id.' balancepeps=|'.$objMouvement->balance_peps.'| '.' por '.$balance;
				$resup = $objMouvementadd->update($user);
				if ($resup<=0)
				{
					$error++;

				}
			}
		}
		//if ($line->id == 12683) exit;
		//echo 'SALIDAS<pre>';
		//print_r($aSales);
		//echo '</pre>';
	}
}
//echo '<hr>FINAL ';
if (!$error) $db->commit();
else $db->rollback();



llxFooter();

$db->close();
?>