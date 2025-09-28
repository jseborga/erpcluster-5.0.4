<?php
/* Copyright (C) 2013-2013 Ramiro Queso Cusi        <ramiro@ubuntu-bo.com>
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
   *	\file       htdocs/contab/seats/seat.php
   *	\ingroup    Contab Asientos
   *	\brief      Page fiche contab
   */
  /* codigo fuente para generar asientos contables de los diferentes procesos
   * proceso de pedidos a proveedores
   */
require("../../main.inc.php");

require_once DOL_DOCUMENT_ROOT.'/core/modules/commande/modules_commande.php';
//require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';
require_once(DOL_DOCUMENT_ROOT."/product/stock/class/entrepot.class.php");

require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/stock/class/mouvementstock.class.php");

require_once(DOL_DOCUMENT_ROOT."/commande/class/commande.class.php");

require_once(DOL_DOCUMENT_ROOT."/core/lib/stock.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/product.lib.php");

$langs->load("contab@contab");

$action=GETPOST('action');

$warehouseid    = GETPOST("warehouseid");
$fk_fabrication = GETPOST("fk_fabrication");
$sortfield      = GETPOST("sortfield");
$sortorder      = GETPOST("sortorder");

//seleccionando los pedidos a proveedores
$sql  = "SELECT sa.rowid, sa.ref as ref, sa.fk_entrepot, f.ref as ref_fabrication, sa.date_creation, sa.date_delivery, sa.description, sa.statut ";
$sql.= " FROM ".MAIN_DB_PREFIX."sol_almacen as sa";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."fabrication as f ";
$sql.= " ON sa.fk_fabrication = f.rowid ";
$sql.= " WHERE sa.entity = ".$conf->entity;


if (! $sortfield) $sortfield="p.ref";
if (! $sortorder) $sortorder="DESC";

$mesg = '';

$formproduct=new FormProduct($db);
$formfabrication=new Fabrication($db);
$objCommande = new Commande($db);
$objunits = new Units($db);
$objectUrqEntrepot = new Entrepotrelation($db);

if (!empty($fk_fabrication))
  {
    $formfabrication->fetch($fk_fabrication);
    if (!empty($formfabrication->fk_commande))
      $fk_commande = $formfabrication->fk_commande;
  }
/*
 * Actions
 */

// registro de productos alternativos
if ($action == 'addlines' && $user->rights->almacen->crearlistproductalt)
  {
    $object = new Productalternative($db);
	
    $object->fk_product     = GETPOST("idprod");
    $object->fk_unit        = GETPOST("fk_unit_father");
    $object->entity         = $conf->entity;
    $object->fk_product_alt = GETPOST("idprod1");
    $object->fk_unit_alt    = GETPOST("fk_unit_son");
    $object->qty            = GETPOST("qty_father");
    $object->qty_alt        = GETPOST("qty_son");
    $object->statut         = 1;
    if ($object->fk_product && $object->fk_product_alt) {
      $id = $object->create($user);
      if ($id > 0)
	{
	  header("Location: fiche.php?id=".GETPOST('id'));
	  exit;
	}
      $action = 'create';
      $mesg='<div class="error">'.$object->error.'</div>';
    }
    else {
      $mesg='<div class="error">'.$langs->trans("ErrorProductRequired").'</div>';
      $action="create";   // Force retour sur page creation
    }
}

// Ajout entrepot
if ($action == 'add' && $user->rights->almacen->crearpedido)
  {
    $object = new Solalmacen($db);
	$datedelivery  = dol_mktime(12, 0, 0, GETPOST('remonth'),  GETPOST('reday'),  GETPOST('reyear'));
	
	$object->ref           = $_POST["ref"];
	$object->entity        = $conf->entity;
	$object->fk_entrepot   = $_POST["fk_entrepot"];
	$object->fk_fabrication= $_POST["fk_fabrication"];
	$object->description   = $_POST["description"];
	$object->statut        = 0;
	$object->date_creation = date('Y-m-d');
	$object->date_delivery = $datedelivery;

	if ($object->fk_entrepot) {
		$id = $object->create($user);
		if ($id > 0)
		{
		  header("Location: fiche.php?id=".$id);
		  exit;
		}
		$action = 'create';
		$mesg='<div class="error">'.$object->error.'</div>';
	}
	else {
		$mesg='<div class="error">'.$langs->trans("ErrorRefRequired").'</div>';
		$action="create";   // Force retour sur page creation
	}
}

//registrar producto a fabrication
if ($action == 'transferf' && $user->rights->fabrication->creer)
{
  $object = new Fabrication($db);
  $object->fetch($_GET['id']);
  $commandeid = $object->fk_commande;
  if (!empty($commandeid))
    {
      $commandedet = new Orderline($db);
      $commandedet->fetch($_GET['pid']);
      $objectdet = new Fabricationdet($db);
      $objectdet->fk_fabrication = $_GET["id"];
      $objectdet->fk_product     = $commandedet->fk_product;
      $objectdet->qty            = $commandedet->qty;

      if ($objectdet->fk_product) {
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
	  $action="create";   // Force retour sur page creation
	}
    }
}
//registrar producto a fabrication
if ($action == 'confirmalternative' && $user->rights->almacen->crearpedido)
{
  $object = new Solalmacen($db);
  $object->fetch(GETPOST('id'));
  $rowid = GETPOST('rowid');
  $prowid = GETPOST('pid');
  if (!empty($object->id))
    {
      $objectdet = new Solalmacendet($db);
      $objectdet->fetch(GETPOST('rowid'));
      $qtySol = $objectdet->qty;

      $objProdAlt = new Productalternative($db);
      $objProdAlt->fetch(GETPOST('pid'));

      $newQty = $qtySol / $objProdAlt->qty * $objProdAlt->qty_alt;
      $objectdet->fk_product = $objProdAlt->fk_product_alt;
      $objectdet->qty = $newQty;
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
if ($action == 'transferdel' && $user->rights->almacen->crearpedido)
{
  $object = new Solalmacen($db);
  $object->fetch($_GET['id']);
  if (!empty($object->id))
    {
      $objectdet = new Solalmacendet($db);
      $objectdet->fetch($_GET['aid']);
      
      if ($objectdet->fk_almacen == $_GET['id']) {
	$objectdet->delete($user);
	header("Location: fiche.php?id=".$_GET['id']);
	exit;
      }
    }
}

if ($action == 'addline' && $user->rights->almacen->crearpedido)
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
	    $object = new Solalmacen($db);
	    $object->fetch(GETPOST('id'));
	    $objectdet = new Solalmacendet($db);

	    //agregando el producto
	    $objectdet->fk_almacen = $object->id;
	    $objectdet->qty = GETPOST('qty');
	    $objectdet->fk_product = $idprod;
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

/*
 * Confirmation de la validation
 */
// if ($action == 'validate')
//   {
//     $object = new Solalmacen($db);
//     $object->fetch(GETPOST('id'));
//     //cambiando a validado
//     $object->statut = 1;
//     //update
//     $object->update($user);
//     header("Location: fiche.php?id=".$_GET['id']);
//   }

/*
 * Confirmation de la re validation
 */
if ($action == 'revalidate')
  {
    $object = new Solalmacen($db);
    $object->fetch(GETPOST('id'));
    //cambiando a validado
    $object->statut = 0;
    //update
    $object->update($user);
    header("Location: fiche.php?id=".$_GET['id']);
  }

// Delete warehouse
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->stock->supprimer)
{
	$object = new Solalmacen($db);
	$object->fetch($_REQUEST["id"]);
	$result=$object->delete($user);
	if ($result > 0)
	{
		header("Location: ".DOL_URL_ROOT.'/almacen/liste.php');
		exit;
	}
	else
	{
		$mesg='<div class="error">'.$object->error.'</div>';
		$action='';
	}
}

// Modification entrepot
if ($action == 'update' && $_POST["cancel"] <> $langs->trans("Cancel"))
{
	$object = new Entrepot($db);
	if ($object->fetch($_POST["id"]))
	{
		$object->libelle     = $_POST["libelle"];
		$object->description = $_POST["desc"];
		$object->statut      = $_POST["statut"];
		$object->lieu        = $_POST["lieu"];
		$object->address     = $_POST["address"];
		$object->cp          = $_POST["zipcode"];
		$object->ville       = $_POST["town"];
		$object->pays_id     = $_POST["country_id"];
		$object->zip         = $_POST["zipcode"];
		$object->town        = $_POST["town"];
		$object->country_id  = $_POST["country_id"];

		if ( $object->update($_POST["id"], $user) > 0)
		{
			$action = '';
			$_GET["id"] = $_POST["id"];
			//$mesg = '<div class="ok">Fiche mise a jour</div>';
		}
		else
		{
			$action = 'edit';
			$_GET["id"] = $_POST["id"];
			$mesg = '<div class="error">'.$object->error.'</div>';
		}
	}
	else
	{
		$action = 'edit';
		$_GET["id"] = $_POST["id"];
		$mesg = '<div class="error">'.$object->error.'</div>';
	}
}

// Modification item
if ($action == 'updateitem' && $user->rights->almacen->crearpedido)
{
	$object = new Solalmacendet($db);
	if ($object->fetch($_POST["rowid"]))
	{
		$object->qty     = $_POST["qty"];

		if ( $object->update($_POST["rowid"], $user) > 0)
		{
		  $action = '';
		  $_GET["id"] = $_POST["id"];
		  //$mesg = '<div class="ok">Fiche mise a jour</div>';
		  header("Location: fiche.php?id=".$object->fk_almacen);
		  exit;

		}
		else
		{
			$action = 'edit';
			$_GET["id"] = $_POST["id"];
			$mesg = '<div class="error">'.$object->error.'</div>';
		}
	}
	else
	{
		$action = 'edit';
		$_GET["id"] = $_POST["id"];
		$mesg = '<div class="error">'.$object->error.'</div>';
	}

}
//actualiza el pedido de almacen y crea la salida de almacenes
if ($action == 'addDeliver' && $user->rights->almacen->crearentrega)
{
  $object = new Solalmacen($db);
  $object->fetch(GETPOST("id"));
  $objectDet = new Solalmacendet($db);
  $fk_almacen = GETPOST("id");
  $aRowItem   = GETPOST('qty_livree');

  foreach($aRowItem AS $rowid => $qty)
    {
      if ($objectDet->fetch($rowid))
	{
	  $objectDet->qty_livree = $qty;
	  
	  if ( $objectDet->update($rowid, $user) > 0)
	    {
	      $type = 1;
	      $qty = $qty * -1;
	      $label = $langs->trans("ShipmentAccordingtoOrder")." ".$object->ref;
	      $objMouvement = new MouvementStock($db);
	      $result = $objMouvement->_create($user,$objectDet->fk_product,$object->fk_entrepot,
					       $qty,$type,0,$label);
	      if ($result == -1 || $result == 0)
		{
		  echo 'error de registro';
		  exit;
		}
	      //sigue procesando
	    }
	  else
	    {
	      $action = 'edit';
	      $_GET["id"] = $_POST["id"];
	      $mesg = '<div class="error">'.$object->error.'</div>';
	    }
	}
      else
	{
	  //analizar si no existe el producto
	}
    }
  $object->statut = 2;
  $object->update($user);
  header("Location: fiche.php?id=".$object->id);
  exit;
  
}


if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}



/*
 * View
 */

$productstatic=new Product($db);
$form=new Form($db);
$formcompany=new FormCompany($db);

$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';
llxHeader("",$langs->trans("ApplicationsWarehouseCard"),$help_url);

if ($action == 'create' && $user->rights->almacen->crearpedido)
{
  //verificando la existencia de pedido
  $fk_entrepotsol = 0;
  if ($fk_commande)
    {
      $sql   = "SELECT fk_entrepot FROM ".MAIN_DB_PREFIX."commande_venta";
      $sql  .= " WHERE fk_commande = '".$fk_commande."'";
      $rsql  = $db->query($sql);
      $objcv = $db->fetch_object($rsql);
      $fk_entrepotSol = $objcv->fk_entrepot;
    }
	print_fiche_titre($langs->trans("NewApplicationsEntrepot"));

	print "<form action=\"fiche.php\" method=\"post\">\n";
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

	// Entrepot Almacen
	print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Entrepot').'</td><td colspan="3">';

	print $objectUrqEntrepot->select_padre('','fk_entrepot',1);
	print '</td></tr>';

	// Fabrication
	print '<tr><td width="25%">'.$langs->trans('OrderProduction').'</td><td colspan="3">';
	print $formfabrication->select_fabrication(GETPOST('fk_fabrication'),'fk_fabrication','',!$disabled,!$disabled);
	print '</td></tr>';

	//date creation
	print '<tr><td width="25%">'.$langs->trans("Date").'</td><td colspan="3">';
	print date('d/m/Y');
	print '</td></tr>';

	//date delivery
	// Date de livraison
	print '<tr><td width="25%" class="fieldrequired">'.$langs->trans("DeliveryDate").'</td><td colspan="2">';
	if (empty($datedelivery))
	  {
	    if (! empty($conf->global->DATE_LIVRAISON_WEEK_DELAY)) $datedelivery = time() + ((7*$conf->global->DATE_LIVRAISON_WEEK_DELAY) * 24 * 60 * 60);
	    else $datedelivery=empty($conf->global->MAIN_AUTOFILL_DATE)?-1:0;
	  }
	$form->select_date($datedelivery,'date_delivery','','','',"crea_commande",1,1);
	print '<input id="reday" type="hidden" value="20" name="reday">';
	print '<input id="remonth" type="hidden" value="03" name="remonth">';
	print '<input id="reyear" type="hidden" value="2013" name="reyear">';

	print "</td></tr>";

	//description
	print '<tr><td width="25%" class="field">'.$langs->trans("Description").'</td><td colspan="3">';
	print '<textarea wrap="soft" name="description" rows="3" cols="40"></textarea>';
	print '</td></tr>';

	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

	print '</form>';
}
else
{
	if ($_GET["id"])
	{
		dol_htmloutput_mesg($mesg);

		$object = new Solalmacen($db);
		$objectDet = new Solalmacendet($db);
		$aArrayItem = $objectDet->list_item($_GET["id"]);
		$numLinesItem = count($aArrayItem);
		$commande = new Commande($db);
		$objEntrepot = new Entrepot($db);
		$objFabrication = new Fabrication($db);
		$result = $object->fetch($_GET["id"]);
		if ($result < 0)
		{
			dol_print_error($db);
		}


		/*
		 * Affichage fiche
		 */
		if ($action <> 'edit' && $action <> 're-edit')
		{
		  //$head = fabrication_prepare_head($object);

			dol_fiche_head($head, 'card', $langs->trans("ApplicationEntrepot"), 0, 'stock');


			/*
			 * Confirmation de la validation
			*/
			if ($action == 'validate')
			{
				// on verifie si l'objet est en numerotation provisoire
				$ref = substr($object->ref, 1, 4);
				if ($ref == 'PROV')
				{
					$numref = $object->getNextNumRef($soc);
				}
				else
				{
					$numref = $object->ref;
				}

				//$object = new Solalmacen($db);
				$object->fetch(GETPOST('id'));
				//cambiando a validado
				$object->statut = 1;
				$object->ref = $numref;
				//update
				$object->update($user);
				$action = '';
				//header("Location: fiche.php?id=".$_GET['id']);

			}


			// Confirm delete third party
			if ($action == 'delete')
			{
				$form = new Form($db);
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("DeleteAWarehouse"),$langs->trans("ConfirmDeleteWarehouse",$object->libelle),"confirm_delete",'',0,2);
				if ($ret == 'html') print '<br>';
			}

			print '<table class="border" width="100%">';

			// Ref
			print '<tr><td width="25%">'.$langs->trans("Ref").'</td><td colspan="3">';
			print $object->ref;
			print '</td></tr>';

			//Entrepot
			$objEntrepot->fetch($object->fk_entrepot);
			print '<tr><td>'.$langs->trans("Entrepot").'</td><td colspan="3">';
			print $objEntrepot->libelle." - ".$objEntrepot->lieu;
			print '</td></tr>';

			// Fabrication
			$objFabrication->fetch($object->fk_fabrication);
			print '<tr><td width="25%">'.$langs->trans('OrderProduction').'</td><td colspan="3">';
			print $objFabrication->ref;
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
			print '<tr><td>'.$langs->trans("Status").'</td><td colspan="3">'.$object->getLibStatut(4).'</td></tr>';


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
			  if ($user->rights->almacen->crearpedido && $object->statut == 0)
			    print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
			  else
			    print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

			  if (($object->statut==1 || $object->statut==0 ) && $user->rights->almacen->supprimer)
			    print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
			  else
			    print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
			  // Valid
			  if ($object->statut == 0 && $numLinesItem > 0)
			    //&& $numlines > 0)
			    //&& $user->rights->fabrication->valider)
			    {
			      print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=validate">'.$langs->trans('Validate').'</a>';
			    }
			  // ReValid
			  if ($object->statut == 1)
			    //&& $numlines > 0)
			    //&& $user->rights->fabrication->valider)
			    {
			      print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=revalidate">'.$langs->trans('Return').'</a>';
			    }
			  // Salida Almacen
			  if ($object->statut == 1 && $user->rights->almacen->crearentrega)
			    {
			      print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=deliver">'.$langs->trans('SubmitRequest').'</a>';
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

			/*
			  Proceso de carga a solalmacendet si existe la orden de produccion
			 */
			if ($object->fk_fabrication > 0)
			  {
			    $sql = "SELECT pa.fk_product_fils as prowid, p.ref, p.label as produit, p.fk_product_type as type, p.pmp as ppmp, p.price, p.price_ttc,";
			    $sql.= " pa.qty AS qtyconvert, cd.qty as qty";
			    $sql.= " FROM ".MAIN_DB_PREFIX."fabricationdet AS cd ";
			    $sql.= " INNER JOIN ".MAIN_DB_PREFIX."product_association AS pa ON cd.fk_product = pa.fk_product_pere ";
			    $sql.= " INNER JOIN ".MAIN_DB_PREFIX."product AS p ON p.rowid = pa.fk_product_fils ";
			    
			    $sql.= " WHERE ";
			    $sql.= " cd.fk_fabrication = '".$object->fk_fabrication."'";	// We do not show if stock is 0 (no product in this warehouse)
			    
			    //cd.fk_product = p.rowid";
			    if (!empty($listId)) $sql .= " AND (p.rowid NOT IN ($listId) OR p.rowid IS NULL)";
			    
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
				    //verificando si existe el producto para el peido al almacen
				    $query = "SELECT sa.rowid AS rowid";
				    $query.= " FROM ".MAIN_DB_PREFIX."sol_almacendet AS sa ";
				    $query.= " WHERE sa.fk_almacen = '".$object->id."'";
				    $query.= " AND sa.fk_product = '".$objp->prowid."'";
				    $resultsql = $db->query($query);
				    $objres = $db->fetch_object($resultsql);
				    if (empty($objres->rowid))
				      {
					$objSolalmdet = new Solalmacendet($db);
					$objSolalmdet->fk_almacen = $object->id;
					$objSolalmdet->fk_product = $objp->prowid;
					$objSolalmdet->qty = $objp->qty*$objp->qtyconvert;
					$objSolalmdet->qtylivree = $objp->qty*$objp->qtyconvert;

					$objSolalmdet->create($user);
				      }
				    $i++;
				  }
				$db->free($resql);
			      }
			  }
			/* ************************************************************************** */
			/*                                                                            */
			/* Affichage de la liste des produits                                         */
			/*                                                                            */
			/* ************************************************************************** */
			print '<br>';
			dol_fiche_head($head, 'card', $langs->trans("ListeProductApplication"), 0, 'stock');
			if ($action == 'deliver' && $object->statut == 1)
			  {
			    print "<form action=\"fiche.php\" method=\"post\">\n";
			    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			    print '<input type="hidden" name="action" value="addDeliver">';
			    print '<input type="hidden" name="id" value="'.$_GET['id'].'">'."\n";
			  }

			print '<table class="noborder" width="100%">';
			print "<tr class=\"liste_titre\">";
			print_liste_field_titre($langs->trans("Product"),"", "p.ref","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("Alternative"),"", "p.ref","&amp;id=".$_GET['id'],"","");
			print_liste_field_titre($langs->trans("Label"),"", "p.label","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
			print_liste_field_titre($langs->trans("Units"),"", "cd.qty","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
			if ($object->statut == 0)
			  print_liste_field_titre($langs->trans("Select"),"", "cd.rowid","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
			if ($action == 'deliver' && $object->statut == 1)
			  {
			    print_liste_field_titre($langs->trans("Deliver"),"", "cd.qty_livree","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
			  }
			print "</tr>";

			$totalunit=0;
			$totalvalue=$totalvaluesell=0;

			$sql = "SELECT p.rowid as rowid, p.ref, p.label as produit, p.fk_product_type as type, p.pmp as ppmp, p.price, p.price_ttc,";
			$sql.= " cd.qty as qty, cd.rowid AS arowid";
			$sql.= " FROM ".MAIN_DB_PREFIX."sol_almacendet cd, ".MAIN_DB_PREFIX."product p";
			$sql.= " WHERE cd.fk_product = p.rowid";
			$sql.= " AND cd.fk_almacen = '".$object->id."'";	// We do not show if stock is 0 (no product in this warehouse)

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
			      $arrayId[$objp->rowid] = $objp->rowid;
			      // Multilangs
			      if ($conf->global->MAIN_MULTILANGS) // si l'option est active
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
			      if ($action == 'moditem' && $objp->arowid == $_GET['rowid'])
				{
				  print "<form action=\"fiche.php\" method=\"post\">\n";
				  print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
				  print '<input type="hidden" name="action" value="updateitem">';
				  print '<input type="hidden" name="rowid" value="'.$objp->arowid.'">'."\n";
				  print '<input type="hidden" name="id" value="'.$object->id.'">'."\n";
				  
				  print "<td>";
				  print $objp->ref;
				  print '</td>';
				  print "<td>";
				  print '&nbsp;';
				  print '</td>';

				  print '<td>'.$objp->produit.'</td>';
				  
				  print '<td align="right"><input type="text" name="qty" value="'.$objp->qty.'" size="12"></td>';
				  
				  if ($user->rights->almacen->crearpedido && $object->statut == 0)
				    {
				      print '<td align="right">';
				      print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
				      print "</td>";
				    }
				  print '</form>';
				}
			      else
				{
				  print "<td>";
				  print img_picto($langs->trans("Modify"),'edit.png').' '. '<a href="'.DOL_URL_ROOT.'/almacen/fiche.php?id='.$object->id.'&rowid='.$objp->arowid.'&action=moditem">'.$objp->ref.'</a>';
				  print '</td>';
				  print "<td>";
				  if ($object->statut == 0)
				    {
				      print '<a href="'.DOL_URL_ROOT.'/almacen/fiche.php?id='.$object->id.'&rowid='.$objp->arowid.'&prowid='.$objp->rowid.'&action=alternative">'.img_picto($langs->trans("Alternative"),'alternative.png').'</a>';
				    }
				  else
				    {
				      print '&nbsp;';
}
				  print '</td>';
				  print '<td>'.$objp->produit.'</td>';
				  
				  print '<td align="right">'.$objp->qty.'</td>';
				  $totalunit+=$objp->qty;
				  
				  
				  if ($user->rights->almacen->crearpedido && $object->statut == 0)
				    {
				      print '<td align="right"><a href="'.DOL_URL_ROOT.'/almacen/fiche.php?id='.$object->id.'&amp;aid='.$objp->arowid.'&amp;action=transferdel">';
				      print img_picto($langs->trans("Delete"),'stcomm-1.png').' '.$langs->trans("Delete");
				      print "</a></td>";
				    }
				  if ($user->rights->almacen->crearentrega && $object->statut == 1)
				    {
				      if ($action == 'deliver')
					{
					  print '<td align="right">';
					  print '<input type="text" name="qty_livree['.$objp->arowid.']" value="'.$objp->qty.'" size="12">';
					  print "</td>";
					}
				    }
				}
			      print "</tr>";
			      $i++;
			    }
			  $db->free($resql);
			  
			  /* print '<tr class="liste_total"><td class="liste_total" colspan="2">'.$langs->trans("Total").'</td>'; */
			  /* print '<td class="liste_total" align="right">'.$totalunit.'</td>'; */
			  /* print '<td class="liste_total">&nbsp;</td>'; */
			  
			  /* print '</tr>'; */
			  
			}
			else
			  {
			    dol_print_error($db);
			  }
			print "</table>\n";
			if ($action == 'deliver' && $object->statut == 1)
			  {
			    print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';
			    print '</form>';
			  }

			if ($object->statut == 0)
			  {
			    print '<table id="tablelines" class="noborder" width="100%">';
			    
			    if ($action != 'editline' && $action != 'alternative' && $action != 'createAlternative')
			      {
			    
				$var=true;

				if ($conf->global->MAIN_FEATURES_LEVEL > 1)
				  {
				    // Add free or predefined products/services
				    $object->formAddObjectLine(1,$mysoc,$soc,$hookmanager);
				  }
				else
				  {
				    // Add predefined products/services
				    if (! empty($conf->product->enabled) || ! empty($conf->service->enabled))
				      {
					$var=!$var;
					$object->formAddPredefinedProduct(0,$mysoc,$soc,$hookmanager);
				      }
				  }
				
				$parameters=array();
				$reshook=$hookmanager->executeHooks('formAddObjectLine',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
			      }
			    print '</table>';
			  }


			/* ************************************************************************** */
			/*                                                                            */
			/* Affichage de la liste des produits de l'entrepot                           */
			/*                                                                            */
			/* ************************************************************************** */

			if ($object->fk_fabrication > 0 && $object->statut==0 && $action<>'alternative' && $action<>'createAlternative')
			  {
			    print '<br>';
			    dol_fiche_head($head, 'card', $langs->trans("ListeProductFabrication"), 0, 'stock');
			    if (!empty($arrayId))
			      $listId = implode(',',$arrayId);
			    print '<table class="noborder" width="100%">';
			    print "<tr class=\"liste_titre\">";
			    print_liste_field_titre($langs->trans("Product"),"", "p.ref","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
			    print_liste_field_titre($langs->trans("Label"),"", "p.label","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
			    print_liste_field_titre($langs->trans("Units"),"", "cd.qty","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
			    print "</tr>";
			    
			    $totalunit=0;
			    $totalvalue=$totalvaluesell=0;
			    
			    $sql = "SELECT pa.fk_product_fils as prowid, p.ref, p.label as produit, p.fk_product_type as type, p.pmp as ppmp, p.price, p.price_ttc,";
			    $sql.= " pa.qty AS qtyconvert, cd.qty as qty";
			    $sql.= " FROM ".MAIN_DB_PREFIX."fabricationdet AS cd ";
			    $sql.= " INNER JOIN ".MAIN_DB_PREFIX."product_association AS pa ON cd.fk_product = pa.fk_product_pere ";
			    $sql.= " INNER JOIN ".MAIN_DB_PREFIX."product AS p ON p.rowid = pa.fk_product_fils ";
			    
			    $sql.= " WHERE ";
			    $sql.= " cd.fk_fabrication = '".$object->fk_fabrication."'";	// We do not show if stock is 0 (no product in this warehouse)
			    
			    //cd.fk_product = p.rowid";
			    if (!empty($listId)) $sql .= " AND (p.rowid NOT IN ($listId) OR p.rowid IS NULL)";
			    
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
				    if ($conf->global->MAIN_MULTILANGS) // si l'option est active
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
				    print '<td>'.$objp->produit.'</td>';
				    
				    print '<td align="right">'.$objp->qty*$objp->qtyconvert.'</td>';
				    $totalunit+=$objp->qty;
				    
				    if ($user->rights->fabrication->creer && $object->statut == 0)
				      {
					if (empty($objp->rowid))
					  {
					    /* print '<td align="right"><a href="'.DOL_URL_ROOT.'/fabrication/fiche.php?id='.$object->id.'&amp;pid='.$objp->prowid.'&amp;action=add_product">'; */
					    /* print img_picto($langs->trans("StockMovement"),'uparrow.png').' '.$langs->trans("RegisterProduct"); */
					    /* print "</a></td>"; */
					  }
					else
					  {
					    if ($user->rights->stock->mouvement->creer)
					      {
						/* print '<td align="right"><a href="'.DOL_URL_ROOT.'/fabrication/fiche.php?id='.$object->id.'&amp;pid='.$objp->prowid.'&amp;action=transferf">'; */
						/* print img_picto($langs->trans("StockMovement"),'uparrow.png').' '.$langs->trans("StockFabrication"); */
						/* print "</a></td>"; */
					      }
					  }
					print "</tr>";
					$i++;
				      }
				  }
				$db->free($resql);
				
				/* print '<tr class="liste_total"><td class="liste_total" colspan="2">'.$langs->trans("Total").'</td>'; */
				/* print '<td class="liste_total" align="right">'.$totalunit.'</td>'; */
				/* print '<td class="liste_total">&nbsp;</td>'; */
				/* print '</tr>'; */
				
			      }
			    else
			      {
				dol_print_error($db);
			      }
			    print "</table>\n";
			  }

			/* ************************************************************************** */
			/*                                                                            */
			/* Product Alternative                                                        */
			/*                                                                            */
			/* ************************************************************************** */

			if ($_GET['rowid'] && $_GET['prowid'] && $object->statut==0 && $action = 'alternative')
			  {
			    print '<br>';
			    dol_fiche_head($head, 'card', $langs->trans("ListProductAlternative"), 0, 'stock');
			    if (!empty($arrayId))
			      $listId = implode(',',$arrayId);
			    print '<table class="noborder" width="100%">';
			    print "<tr class=\"liste_titre\">";
			    print_liste_field_titre($langs->trans("Product"),"", "p.ref","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
			    print_liste_field_titre($langs->trans("Label"),"", "p.label","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
			    print_liste_field_titre($langs->trans("Units"),"", "cd.qty","&amp;id=".$_GET['id'],"",'align="right"',$sortfield,$sortorder);
			    print_liste_field_titre($langs->trans("Actions"),"", "cd.qty","&amp;id=".$_GET['id'],"",'align="right"');
			    print "</tr>";
			    
			    $totalunit=0;
			    $totalvalue=$totalvaluesell=0;
			    
			    $sql = "SELECT pa.fk_product_alt as prowid, p.ref, p.label as produit, u.ref as unit, pa.rowid AS arowid ";
			    $sql.= " FROM ".MAIN_DB_PREFIX."product_alternative AS pa ";
			    $sql.= " INNER JOIN ".MAIN_DB_PREFIX."product AS p ON p.rowid = pa.fk_product_alt ";
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
				    if ($conf->global->MAIN_MULTILANGS) // si l'option est active
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
				    print '<td>'.$objp->unit.'</td>';
				    print '<td align="right"><a href="'.DOL_URL_ROOT.'/almacen/fiche.php?id='.$object->id.'&rowid='.$_GET['rowid'].'&amp;pid='.$objp->arowid.'&amp;action=confirmalternative">';
				    print img_picto($langs->trans("Select"),'uparrow.png').' '.$langs->trans("Select");
				    print "</a></td>";
				    print "</tr>";
				    $i++;
				  }
				$db->free($resql);
				
				/* print '<tr class="liste_total"><td class="liste_total" colspan="2">'.$langs->trans("Total").'</td>'; */
				/* print '<td class="liste_total" align="right">'.$totalunit.'</td>'; */
				/* print '<td class="liste_total">&nbsp;</td>'; */
				/* print '</tr>'; */
				
			      }
			    else
			      {
				dol_print_error($db);
			      }
			    print "</table>\n";
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
				    if (! empty($conf->product->enabled) || ! empty($conf->service->enabled))
				      {
					$var=!$var;
					$objectpalt->formAddPredefinedProduct(0,$mysoc,$soc,$hookmanager,$object->id);
				      }
				  }
				
				$parameters=array();
				$reshook=$hookmanager->executeHooks('formAddObjectLine',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
			      }
			    print '</table>';
			    
			  }
			
			
		}


		/*
		 * Edition fiche
		 */
		if (($action == 'edit' || $action == 're-edit') && 1)
		{
		  print_fiche_titre($langs->trans("ApplicationsEdit"), $mesg);
		  
		  print '<form action="fiche.php" method="POST">';
		  print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		  print '<input type="hidden" name="action" value="update">';
		  print '<input type="hidden" name="id" value="'.$object->id.'">';
		  
		  print '<table class="border" width="100%">';
		  
		  // Ref
		  print '<tr><td width="20%" class="fieldrequired">'.$langs->trans("Ref").'</td><td colspan="3"><input name="libelle" size="20" value="'.$object->ref.'"></td></tr>';
		  
		  
		  // Entrepot Almacen
		  print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Entrepot').'</td><td colspan="3">';
		  print $objectUrqEntrepot->select_padre($object->fk_entrepot,'fk_entrepot',1);
		  print '</td></tr>';
		  
		  // Fabrication
		  print '<tr><td width="25%">'.$langs->trans('OrderProduction').'</td><td colspan="3">';
		  print $formfabrication->select_fabrication($object->fk_fabrication,'fk_fabrication','',!$disabled,!$disabled);
		  print '</td></tr>';
		  
		  //date creation
		  print '<tr><td width="25%">'.$langs->trans("Date").'</td><td colspan="3">';
			print $object->date_creation ? dol_print_date($object->date_creation,'daytext') : '&nbsp;';
		  print '</td></tr>';
		  
		  //date delivery
		  // Date de livraison
		  print '<tr><td width="25%" class="fieldrequired">'.$langs->trans("DeliveryDate").'</td><td colspan="2">';
		  if (empty($datedelivery))
		    {
		      if (! empty($conf->global->DATE_LIVRAISON_WEEK_DELAY)) $datedelivery = time() + ((7*$conf->global->DATE_LIVRAISON_WEEK_DELAY) * 24 * 60 * 60);
		      else $datedelivery=empty($conf->global->MAIN_AUTOFILL_DATE)?-1:0;
		    }
		  $form->select_date($object->date_delivery,'date_delivery','','','',"crea_commande",1,1);
		  print '<input id="reday" type="hidden" value="20" name="reday">';
		  print '<input id="remonth" type="hidden" value="03" name="remonth">';
		  print '<input id="reyear" type="hidden" value="2013" name="reyear">';
		  
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
