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

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/almacen/class/commonobject_.class.php';
require_once DOL_DOCUMENT_ROOT.'/fabrication/productlist/class/productlist.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/units/class/units.class.php';
//require_once DOL_DOCUMENT_ROOT.'/core/modules/commande/modules_commande.php';
//require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';
require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
// require_once(DOL_DOCUMENT_ROOT."/product/stock/class/entrepot.class.php");
// require_once(DOL_DOCUMENT_ROOT."/almacen/local/class/entrepotrelation.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
// require_once(DOL_DOCUMENT_ROOT."/product/stock/class/mouvementstock.class.php");
// require_once(DOL_DOCUMENT_ROOT."/commande/class/commande.class.php");
// require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacen.class.php");
// require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacendet.class.php");

// require_once(DOL_DOCUMENT_ROOT."/fabrication/class/fabrication.class.php");
// require_once(DOL_DOCUMENT_ROOT."/fabrication/class/fabricationdet.class.php");
// require_once(DOL_DOCUMENT_ROOT."/fabrication/class/commandeventa.class.php");
// require_once(DOL_DOCUMENT_ROOT."/core/lib/stock.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/product.lib.php");
// require_once(DOL_DOCUMENT_ROOT."/core/class/html.formcompany.class.php");

$langs->load("products");
$langs->load("almacen@almacen");
$langs->load("fabrication@fabrication");

$action  = GETPOST('action');
$id      = GETPOST("id");
$idprod  = GETPOST("idprod");
$idprod1 = GETPOST("idprod1");

$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");

if (! $sortfield) $sortfield="p.ref";
if (! $sortorder) $sortorder="DESC";

$mesg = '';

$formproduct=new FormProduct($db);

/*
 * Actions
 */

// Ajout entrepot
if ($action == 'addlines' && $user->rights->fabrication->crearlistproduct)
  {
    $object = new Productlist($db);
	
    $object->fk_product_father = GETPOST("idprod");
    $object->fk_unit_father    = GETPOST("fk_unit_father");
    $object->entity            = $conf->entity;
    $object->fk_product_son    = GETPOST("idprod1");
    $object->fk_unit_son       = GETPOST("fk_unit_son");
    $object->qty_father        = GETPOST("qty_father");
    $object->qty_son           = GETPOST("qty_son");
    $object->statut            = 1;
    if ($object->fk_product_father && $object->fk_product_son) {
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

// Delete warehouse
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->stock->supprimer)
{
	$object = new Productlist($db);
	$object->fetch($_REQUEST["id"]);
	$result=$object->delete($user);
	if ($result > 0)
	  {
	    header("Location: ".DOL_URL_ROOT.'/fabrication/productlist/liste.php');
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
    $object = new Productlist($db);
    if ($object->fetch($_POST["id"]))
      {
	$object->fk_product_father = GETPOST("idprod");
	$object->fk_unit_father    = GETPOST("fk_unit_father");
	$object->fk_product_son    = GETPOST("idprod1");
	$object->fk_unit_son       = GETPOST("fk_unit_son");
	$object->qty_father        = GETPOST("qty_father");
	$object->qty_son           = GETPOST("qty_son");
	
	
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



if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}



/*
 * View
 */

$object        = new Productlist($db);
$productstatic = new Product($db);
$objunits      = new Units($db);
$form          = new Form($db);
//$formcompany=new FormCompany($db);

$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';
llxHeader("",$langs->trans("ApplicationsWarehouseCard"),$help_url);

if ($action == 'create' && $user->rights->fabrication->crearlistproduct)
{
  print_fiche_titre($langs->trans("Newlistproduct"));

  // print "<form action=\"fiche.php\" method=\"post\">\n";
  // print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
  // print '<input type="hidden" name="action" value="add">';
  
  dol_htmloutput_mesg($mesg);
  
  print '<table class="border" width="100%">';
  print '<tr>';
  print '<td>';
  //product father
  print '<table id="tablelines" class="noborder" width="100%">';
  if ($action != 'editline')
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
  print '</td>';
  print '</tr>';
  print '</table>';  
  //  print '</form>';
}
else
{
	if ($_GET["id"])
	{
		dol_htmloutput_mesg($mesg);

		$object = new Productlist($db);
		$objproductf = new Product($db);
		$objproducts = new Product($db);
		$objunitf    = new Units($db);
		$objunits    = new Units($db);

		$result = $object->fetch($_GET["id"]);
		$objproductf->fetch($object->fk_product_father);
		$objproducts->fetch($object->fk_product_son);
		$objunitf->fetch($object->fk_unit_father);
		$objunits->fetch($object->fk_unit_son);

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

			dol_fiche_head($head, 'card', $langs->trans("Listmaterial"), 0, 'stock');



			// Confirm delete third party
			if ($action == 'delete')
			{
				$form = new Form($db);
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("DeleteListProduct"),$langs->trans("ConfirmDeleteListProduct",$object->libelle),"confirm_delete",'',0,2);
				if ($ret == 'html') print '<br>';
			}

			print '<table class="border" width="100%">';

			// product father
			print '<tr><td width="25%">'.$langs->trans("Productfather").'</td><td colspan="3">';
			print $objproductf->ref;
			print '</td></tr>';

			//unit father
			print '<tr><td>'.$langs->trans("Unitfather").'</td><td colspan="3">';
			print $objunitf->ref." - ".$objunitf->description;
			print '</td></tr>';

			//qty father
			print '<tr><td>'.$langs->trans("Quantityfather").'</td><td colspan="3">';
			print $object->qty_father;
			print '</td></tr>';

			// product son
			print '<tr><td width="25%">'.$langs->trans("Productson").'</td><td colspan="3">';
			print $objproducts->ref;
			print '</td></tr>';

			//unit son
			print '<tr><td>'.$langs->trans("Unitson").'</td><td colspan="3">';
			print $objunits->ref." - ".$objunits->description;
			print '</td></tr>';

			//qty father
			print '<tr><td>'.$langs->trans("Quantityson").'</td><td colspan="3">';
			print $object->qty_son;
			print '</td></tr>';



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

			  if (($object->statut==1 || $object->statut==0 ) && $user->rights->fabrication->supprimerlistproduct)
			    print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
			  else
			    print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";

			}

			print "</div>";
		}

	}
}


llxFooter();

$db->close();
?>
