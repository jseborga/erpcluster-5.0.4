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
require_once DOL_DOCUMENT_ROOT.'/almacen/productlist/class/productlist.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/units/class/units.class.php';
require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

//require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/product.lib.php");

$langs->load("products");
$langs->load("almacen@almacen");

$action  = GETPOST('action');
$id      = GETPOST("id");
$idprod  = GETPOST("idprod");
$idprod1 = GETPOST("idprod1");

$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");

if (! $sortfield) $sortfield="p.ref";
if (! $sortorder) $sortorder="DESC";

$mesg = '';

//$formproduct=new FormProduct($db);

/*
 * Actions
 */

// Ajout entrepot
if ($action == 'addlines' && $user->rights->almacen->crearunits)
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
	  //header("Location: fiche.php?id=".$id);
	  header("Location: fiche.php?id=".GETPOST("idprod"));

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

// Ajout entrepot
if ($action == 'addlinesson' && $user->rights->fabrication->crearlistproduct)
  {
    $object = new Productlist($db);
    $objectLast = new Productlist($db);
    $objectLast->fetch(GETPOST("id"));
    
    $object->fk_product_father = GETPOST("idprod");
    $object->fk_unit_father    = $objectLast->fk_unit_father;
    $object->qty_father        = $objectLast->qty_father;
    $object->entity            = $conf->entity;
    $object->fk_product_son    = GETPOST("idprod1");
    $object->fk_unit_son       = GETPOST("fk_unit_son");
    $object->qty_son           = GETPOST("qty_son");
    $object->statut            = 1;
    if ($object->fk_product_father && $object->fk_product_son) {
      $id = $object->create($user);
      if ($id > 0)
	{
	  header("Location: fiche.php?id=".$object->fk_product_father);
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
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->fabrication->supprimerlistproduct)
{
	$object = new Productlist($db);
	$object->fetch($_REQUEST["id"]);
	$fk_product_father = $object->fk_product_father;
	$result=$object->delete($user);
	if ($result > 0)
	  {
	    header("Location: ".DOL_URL_ROOT.'/fabrication/productlist/fiche.php?id='.$fk_product_father);
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

$object = new Productlist($db);
$object->fetch_product($_GET["id"]);
if ($object->fk_product_father != $_GET["id"])
  {
    $objectpr = new Product($db);
    $objectpr->fetch($_GET["id"]);
    $object->productNome = $objectpr->label;
    $object->fk_product_father = $_GET["id"];
    $action = 'create';
  }

$productstatic = new Product($db);
$objunits      = new Units($db);
$form          = new Form($db);
//$formcompany=new FormCompany($db);

$help_url='EN:Module_Almacen_En|FR:Module_Almacen|ES:M&oacute;dulo_Almacen';
llxHeader("",$langs->trans("Warehouse"),$help_url);

//$head=fabrication_prepare_head($object);
dol_fiche_head($head, $tab, $langs->trans("Warehouse"),0,($object->public?'projectpub':'project'));

if ($action == 'create' && $user->rights->fabrication->crearlistproduct)
{
  print_fiche_titre($langs->trans("Newproduct"));
  
  dol_htmloutput_mesg($mesg);


	print "<form action=\"fiche.php\" method=\"post\">\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

	// Ref
	print '<tr><td width="25%" class="fieldrequired">'.$langs->trans("Ref").'</td><td colspan="3"><input name="ref" size="12" value=""></td></tr>';

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

      $object = new Productlist($db);
      $objproductf = new Product($db);
      $objproducts = new Product($db);
      $objunitf    = new Units($db);
      $objunits    = new Units($db);
      
      $result = $object->fetch_product($_GET["id"]);
      $objproductf->fetch($_GET["id"]);
      if ($object->fk_product_father == $_GET["id"])
	{
	  $objunitf->fetch($object->fk_unit_father);
	}
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

	  //dol_fiche_head($head, 'card', $langs->trans("Listmaterial"), 0, 'stock');

	  // Confirm delete third party
	  if ($action == 'delete')
	    {
	      $form = new Form($db);
	      $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&idproduct='.$object->fk_product_father,$langs->trans("DeleteListProduct"),$langs->trans("ConfirmDeleteListProduct",$object->libelle),"confirm_delete",'',0,2);
	      if ($ret == 'html') print '<br>';
	    }
	  
	  print '<table class="border" width="100%">';
	  // product father
	  print '<tr><td width="25%">'.$langs->trans("Productfather").'</td><td colspan="3">';
	  print $objproductf->ref .' - '.$objproductf->label;
	  print '</td></tr>';
	  
	  //unit father
	  print '<tr><td>'.$langs->trans("Unitfather").'</td><td colspan="3">';
	  print $objunitf->ref." - ".$objunitf->description;
	  print '</td></tr>';
	  
	  //qty father
	  print '<tr><td>'.$langs->trans("Quantityfather").'</td><td colspan="3">';
	  print $object->qty_father;
	  print '</td></tr>';
	  
			// // product son
			// print '<tr><td width="25%">'.$langs->trans("Productson").'</td><td colspan="3">';
			// print $objproducts->ref;
			// print '</td></tr>';

			// //unit son
			// print '<tr><td>'.$langs->trans("Unitson").'</td><td colspan="3">';
			// print $objunits->ref." - ".$objunits->description;
			// print '</td></tr>';

			// //qty father
			// print '<tr><td>'.$langs->trans("Quantityson").'</td><td colspan="3">';
			// print $object->qty_son;
			// print '</td></tr>';



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
	      
	      if (($object->statut==1 || $object->statut==0 ) && $user->rights->fabrication->crearlistproduct)
		print "<a class=\"butActionDelete\" href=\"fiche.php?action=createnew&id=".$object->fk_product_father."\">".$langs->trans("Create")."</a>";
	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Create")."</a>";
	      
	      // if (($object->statut==1 || $object->statut==0 ) && $user->rights->fabrication->supprimerlistproduct)
	      // 	print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
	      // else
	      // 	print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
	      
	      
	    }
	  print "</div>";

	  print '<div>';
	  /*******************************/
	  /*crear nuevo hijo*/
	  /*******************************/
	  print_fiche_titre($langs->trans("Newlistproduct"));
	  
	  dol_htmloutput_mesg($mesg);
	  
	  print '<table class="border" width="100%">';
	  print '<tr>';
	  print '<td>';
	  //product father
	  print '<table id="tablelines" class="noborder" width="100%">';
	  if ($action == 'createnew')
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
		      $object->formAddPredefinedProductSon(0,$mysoc,$soc,$hookmanager);
		    }
		}
	      
	      $parameters=array();
	      $reshook=$hookmanager->executeHooks('formAddObjectLine',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	    }
	  print '</table>';
	  print '</td>';
	  print '</tr>';
	  print '</table>';  
	  print '</div>';

	  /* ************************************************************************** */
	  /*                                                                            */
	  /* Product hijos                                                              */
	  /*                                                                            */
	  /* ************************************************************************** */
	  
	  print '<br>';
	  //dol_fiche_head($head, 'card', $langs->trans("ListMaterials"), 0, 'stock');
	  
	  print '<table class="noborder" width="100%">';
	  print "<tr class=\"liste_titre\">";
	  print_liste_field_titre($langs->trans("Ref"),"", "p.ref","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
	  print_liste_field_titre($langs->trans("Label"),"", "p.label","&amp;id=".$_GET['id'],"","",$sortfield,$sortorder);
	  print_liste_field_titre($langs->trans("Units"),"", "u.ref","&amp;id=".$_GET['id'],"",'align="center"',$sortfield,$sortorder);
	  print_liste_field_titre($langs->trans("Quantity"),"", "pa.qty_son","&amp;id=".$_GET['id'],"",'align="right"');
	  //	  print_liste_field_titre($langs->trans("Status"),"", "pa.statut","&amp;id=".$_GET['id'],"",'align="center"');
	  print_liste_field_titre($langs->trans("Action"),"", "","","",'align="right"');
	  print "</tr>";
	  
	  $totalunit=0;
	  $totalvalue=$totalvaluesell=0;
	  
	  $sql = "SELECT pa.rowid, pa.fk_product_father,pa.fk_product_son as psrowid, p.ref, p.label as produit, u.ref as unit, pa.qty_father, pa.qty_son, pa.statut ";
	  $sql.= " FROM ".MAIN_DB_PREFIX."product_list AS pa ";
	  $sql.= " INNER JOIN ".MAIN_DB_PREFIX."product AS p ON p.rowid = pa.fk_product_son ";
	  $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."units AS u ON u.rowid = pa.fk_unit_son ";
	  
	  $sql.= " WHERE ";
	  $sql.= " pa.fk_product_father = '".$object->fk_product_father."'";
			    
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
		  print '<td align="left">';
		  $productstatic->id=$objp->rowid;
		  $productstatic->ref=$objp->ref;
		  $productstatic->type=$objp->type;
		  print $productstatic->getNomUrl(1,'stock',16);
		  print '</td>';
		  print '<td align="left">'.$objp->produit.'</td>';
		  print '<td align="center">'.$objp->unit.'</td>';
		  print '<td align="right">'.$objp->qty_son.'</td>';
		  //		  print '<td align="center">'.$objp->statut.'</td>';
		  if ($user->rights->fabrication->supprimerlistproduct)
		    print '<td align="right"><a href="'.DOL_URL_ROOT.'/fabrication/productlist/fiche.php?action=delete&id='.$objp->fk_product_father.'&iid='.$objp->rowid.'">'.img_picto($langs->trans("DeleteListProduct"),'delete').'</a></td>';
		  else
		    print '<td>&nbsp;</td>';
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
	  
	  
	  
	}
      
    }
}


llxFooter();

$db->close();
?>
