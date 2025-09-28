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

$langs->load("products");
$langs->load("orders");
$langs->load("bills");
$langs->load("stocks");

if (!$user->rights->almacen->creartransfout)
  accessforbidden();

$action=GETPOST("action");
$cancel=GETPOST('cancel');

// Security check
$id = GETPOST('id')?GETPOST('id'):GETPOST('ref');
$ref = GETPOST('ref');
$stocklimit = GETPOST('stocklimit');
$cancel = GETPOST('cancel');
$fieldid = isset($_GET["ref"])?'ref':'rowid';
if ($user->societe_id) $socid=$user->societe_id;
//$result=restrictedArea($user,'produit&stock',$id,'product&product','','',$fieldid);

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
include_once DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php';
$hookmanager=new HookManager($db);
$hookmanager->initHooks(array('ordercard'));

/*
 *	Actions
 */

if ($cancel) $action='';

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
// action delitem
if ($action == 'delitem')
{
  unset($_SESSION['itemFo'][GETPOST("id")]);
  header("Location: out.php?action=create");
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
	$_SESSION["itemFo"][GETPOST("id_product")] = GETPOST("nbpiece");
	header("Location: out.php?action=create");
	exit;
      }
 }
// Transfer stock from a warehouse to another warehouse
if ($action == "transfert_stock" && ! $cancel)
{
	if (! (GETPOST("id_entrepot_source") > 0))
	{
	  setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Warehouse")), 'errors');
	  $error++;
	  $action='transfert';
	}
	
	if (empty($_SESSION["itemFo"]))
	  {
	    setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("NumberOfUnit")), 'errors');
	    $error++;
	    $action='create';
	  }

	if (! $error)
	  {
	    if (GETPOST("id_entrepot_source"))
	      {
		foreach ((array) $_SESSION["itemFo"] AS $id => $nbpiece)
		  {
		    if (is_numeric($nbpiece) && $id)
		      {
			$product = new Product($db);
			$result=$product->fetch($id);
			$db->begin();
			
			$product->load_stock();	// Load array product->stock_warehouse
			
			// Define value of products moved
			$pricesrc=0;
			if (isset($product->stock_warehouse[GETPOST("id_entrepot_source")]->pmp)) $pricesrc=$product->stock_warehouse[GETPOST("id_entrepot_source")]->pmp;
			$pricedest=$pricesrc;
			
			//print 'price src='.$pricesrc.', price dest='.$pricedest;exit;
			
			// Remove stock
			$result1=$product->correct_stock(
							 $user,
							 GETPOST("id_entrepot_source"),
							 $nbpiece,
							 1,
							 GETPOST("label"),
							 $pricesrc
							 );
			
			// // Add stock
			// $result2=$product->correct_stock(
			// 				 $user,
			// 				 GETPOST("id_entrepot_destination"),
			// 				 $nbpiece,
			// 				 0,
			// 				 GETPOST("label"),
			// 				 $pricedest
			// 				 );
			
			if ($result1 >= 0)
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
		      }
		  }
		$_SESSION["itemFo"] = array();
		header("Location: out.php?action=create");
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

$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';
llxHeader("",$langs->trans("CardProduct".$product->type),$help_url);

/*
 * habilitamos una sesion para la carga de items de transferencia
 * $_SESSION['itemFo'][$rowid] = 0
*/
/*
 * create transfert
 */
if ($action == "create")
  {
    //WYSIWYG Editor
    require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
    
    print_fiche_titre($langs->trans('CreateMovementOut'));

    $form = new Form($db);
    print_titre($langs->trans("InternalMovement"));
    print '</br>';
    print '<table class="border" width="70%">';
    //encabezado de registro nuevo
    print '<tr>';
    print '<td width="20%" class="fieldrequired">'.$langs->trans("Product").'</td>';
    print '<td width="60%" class="fieldrequired">'.$langs->trans("Description").'</td>';
    print '<td width="15%" class="fieldrequired">'.$langs->trans("NumberOfUnit").'</td>';
    print '<td width="5%" class="fieldrequired">'.$langs->trans("Action").'</td>';

    print '</tr>';
    print '<form action="'.$_SERVER["PHP_SELF"].'?id='.$product->id.'" method="post">'."\n";
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="transfert_session">';

    print '<tr>';
    print '<td width="20%">';
    $i = 1;
    print $form->select_produits('','id_product','',$conf->product->limit_size);
    print '</td>';
    print '<td width="60%">';
    print $object->descripcion;
    print '</td>';
    print '<td width="15%" class="fieldrequired">';
    print '<input name="nbpiece" size="10" value="'.GETPOST("nbpiece").'">';
    print '</td>';
    print '<td width="5%" class="fieldrequired">';
    print '<center><input type="submit" class="button" value="'.$langs->trans('Add').'"></center>';
    print '</td>';
    print '</tr>';
    print '</form>';
    $aItemRow = $_SESSION["itemFo"];

    foreach ((array) $aItemRow AS $id => $qty)
      {
	if (!empty($id))
	  {
	    $product = new Product($db);
	    $product->fetch($id);
	    print '<tr>';
	    print '<td width="20%">';
	    print $product->ref;
	    print '</td>';
	    print '<td width="60%">';
	    print $product->label;
	    print '</td>';
	    print '<td width="20%" align="right">';
	    print $qty;
	    print '</td>';
	    print '<td>';
	    print '<center>';
	    //	print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';
	    print '<a href="'.DOL_URL_ROOT.'/almacen/transferencia/out.php?action=delitem&id='.$id.'">'.$langs->trans("Delete").'</a>';
	    
	    print '</td>';
	    print '</tr>';
	  }
      }    
    print '</table>';
    
    print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">'."\n";
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="transfert_stock">';
    print '</br>';

    print '<table class="border" width="70%">';
    print '<tr>';
    print '<td width="20%" class="fieldrequired">'.$langs->trans("WarehouseOut").'</td><td width="20%">';
    print $formproduct->selectWarehouses(($_GET["dwid"]?$_GET["dwid"]:GETPOST('id_entrepot_source')),'id_entrepot_source','',1);
    print '</td>';
    print '</tr>';

    print '<tr>';
    print '<td width="20%">'.$langs->trans("Label").'</td>';
    print '<td colspan="4">';
    print '<input type="text" name="label" size="40" value="'.GETPOST("label").'">';
    print '</td>';
    print '</tr>';

    print '</table>';
    print '<center><input type="submit" class="button" value="'.$langs->trans('Save').'">&nbsp;';
    print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';
    
    print '</form>';
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
?>
