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

//require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/product.lib.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

//require_once DOL_DOCUMENT_ROOT.'/almacen/class/commonobject_.class.php';
require_once DOL_DOCUMENT_ROOT.'/productext/class/productlist.class.php';
require_once DOL_DOCUMENT_ROOT.'/productext/lib/productext.lib.php';
//require_once DOL_DOCUMENT_ROOT.'/fabrication/class/fabrication.class.php';
require_once DOL_DOCUMENT_ROOT.'/productext/class/productaddext.class.php';
//require_once DOL_DOCUMENT_ROOT.'/fabrication/class/productunit.class.php';
if ($conf->almacen->enabled)
	require_once DOL_DOCUMENT_ROOT.'/almacen/class/productunit.class.php';


//require_once DOL_DOCUMENT_ROOT.'/fabrication/units/class/units.class.php';
require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';


$langs->load("productext@productext");
$langs->load("products");

$action  = GETPOST('action');
$id      = GETPOST("id");
$idr     = GETPOST('idr');
$idprod  = GETPOST("idprod");
$idprod1 = GETPOST("idprod1");

$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");
$search    = GETPOST('search');

if (! $sortfield) $sortfield="p.ref";
if (! $sortorder) $sortorder="DESC";

$now = dol_now();
$mesg = '';
$lSearch = False;
//$formproduct=new FormProduct($db);
$object    = new Product($db);
$objecttmp = new Product($db);
$objProductadd = new Productaddext($db);
$objectl   = new Productlist($db);

//$objunits  = new Units($db);
//$objpunit  = new Productunit($db);
//if ($conf->fabrication->enabled)
//	$objunit   = new Units($db);
/*
 * Actions
 */

// Ajout entrepot
//if ($action == 'addunit'  && $user->rights->fabrication->mat->write)
//{
//	$objpunit->fetch('',GETPOST('id'));
//	if (empty($objpunit->fk_product))
//	{
//		$objpunit->fk_product = GETPOST('id');
//		$objpunit->fk_unit = GETPOST('fk_unit');
//		$objpunit->active = 1;
//		$res = $objpunit->create($user);
//	}
//	$_GET['id'] = $_POST['id'];
//	$action = '';
//}

// Ajout entrepot
if ($action == 'updateqty'  && $user->rights->productext->mat->write)
{
	$db->begin();
	$error=0;
	$res = $objProductadd->fetch (0,$id);
	if ($res==1)
	{
		//actualizamos
		$objProductadd->quant_material = GETPOST('qty_father');
		$res = $objProductadd->update($user);
		if ($res <=0)
		{
			$error++;
			setEventMessages($objProductadd->error,$objProductadd->errors,'errors');
		}
	}
	elseif(empty($res))
	{
		//creamos
		$objProductadd->fk_product = GETPOST('id');
		$objProductadd->tva_tx = (GETPOST('tva_tx')?GETPOST('tva_tx'):0);
		$objProductadd->percent_base = (GETPOST('percent_base')?GETPOST('percent_base'):0);
		$objProductadd->sel_ice = 0;
		$objProductadd->sel_iva = 1;
		$objProductadd->fk_unit_ext = 0;
		$objProductadd->fk_product_type = 0;
		$objProductadd->quant_convert = 0;
		$objProductadd->quant_disassembly = 0;
		$objProductadd->quant_material = GETPOST('qty_father');
		$objProductadd->fk_user_create = $user->id;
		$objProductadd->fk_user_mod = $user->id;
		$objProductadd->date_create = $now;
		$objProductadd->date_mod = $now;
		$objProductadd->tms = $now;
		$objProductadd->status = 1;
		$res = $objProductadd->create($user);
		if ($res <=0)
		{
			$error++;
			setEventMessages($objProductadd->error,$objProductadd->errors,'errors');
		}
	}

	//buscamos el producto
	$filter = " AND t.fk_product_father = ".$id;
	$res = $objectl->fetchAll('','',0,0,array(),'AND',$filter);
	if ($res >0)
	{
		$lines = $objectl->lines;
		foreach ($lines AS $j => $line)
		{
			if (!$error)
			{
				$res = $objectl->fetch ($line->id);
				if ($res==1)
				{
					$objectl->qty_father = GETPOST('qty_father');
					$res = $objectl->update($user);
					if ($res <= 0)
					{
						$error++;
						setEventMessages($objectl->error,$objectl->errors,'errors');
					}
				}
			}
		}
	}
	if (!$error)
	{
		$db->commit();
		setEventMessages($langs->trans('Updatesatisfactory'),null,'mesgs');
		$action = '';
	}
	else
	{
		$db->rollback();
		$action = '';
	}


//	if (empty($objpunit->fk_product))
//	{
//		$objpunit->fk_product = GETPOST('id');
//		$objpunit->fk_unit = GETPOST('fk_unit');
//		$objpunit->active = 1;
//		$res = $objpunit->create($user);
//	}
//	$_GET['id'] = $_POST['id'];
//	$action = '';
}


if ($action == 'addlines' && $user->rights->productext->mat->write)
{
	$obj = new Productlist($db);

	$obj->fk_product_father = GETPOST("idprod");
	$obj->fk_unit_father    = GETPOST("fk_unit_father");
	$obj->entity            = $conf->entity;
	$obj->fk_product_son    = GETPOST("idprod1");
	$obj->fk_unit_son       = GETPOST("fk_unit_son");
	$obj->qty_father        = GETPOST("qty_father");
	$obj->qty_son           = GETPOST("qty_son");
	$obj->statut            = 1;
	if ($obj->fk_product_father > 0 && $obj->fk_product_son > 0)
	{
	//revisamos y almacenamos
	//agregamos las unidades de medida para cada producto si no tienen
	//	$objpunit->fetch('',$obj->fk_product_father);
	//	if ($objpunit->id)
	//	{
	//		if ($objpunit->fk_unit != $obj->fk_unit_father)
	//		{
	//			$error++;
	//			$mesg.='<div class="error">'.$langs->trans('Unitsdiferentes').'</div>';
	//		}
	//	}
	//	else
	///	{
	//		//agregamos la unidad padre
	//		$objpunit->fk_product = $obj->fk_product_father;
	//		$objpunit->fk_unit = $obj->fk_unit_father;
	//		$objpunit->active = 1;
	//		$objpunit->create($user);
	//	}
		//hijo
	//	$objpunit->fetch('',$obj->fk_product_son);
	//	if ($objpunit->id)
	//	{
	//		if ($objpunit->fk_unit != $obj->fk_unit_son)
	//		{
	//			$error++;
	//			$mesg.='<div class="error">'.$langs->trans('Unitssondiferentes').'</div>';
	//		}
	//	}
	//	else
	//	{
	//	//agregamos la unidad hijo
	//		$objpunit->fk_product = $obj->fk_product_son;
	//		$objpunit->fk_unit = $obj->fk_unit_son;
	//		$objpunit->active = 1;
	//		$objpunit->create($user);
	//	}
		if (empty($error))
		{
			$id = $obj->create($user);
			if ($id > 0)
			{
		//header("Location: fiche.php?id=".$id);
				header('Location: '.$_SERVER['PHP_SELF'].'?id='.$obj->fk_product_father);
				exit;
			}
		}
		$action = 'create';
		$mesg.='<div class="error">'.$object->error.'</div>';
	}
	else
	{
		$mesg='<div class="error">'.$langs->trans("ErrorRefRequired").'</div>';
		$action="create";
	   // Force retour sur page creation
	}
}

// Ajout entrepot
if ($action == 'addlinesson' && $user->rights->productext->mat->write)
{
	if ($object->fetch(GETPOST('id')))
	{
		$idprod1 = GETPOST('idprod1');
		$objectl = new Productlist($db);
		if (empty(GETPOST('idprod1')))
		{
			if (!empty(GETPOST('search_idprod1')))
			{
				$res = $objecttmp->fetch('',GETPOST('search_idprod1'));
				if ($res >0 && $objecttmp->ref == GETPOST('search_idprod1'))
				{
					$idprod1 = $objecttmp->id;
				}
			}
		}
		// $objectLast = new Productlist($db);
		// $objectLast->fetch(GETPOST("idprod"));
		//buscamos la unidad
		//$objpunit->fetch('',$_POST['id']);

		//agregando
		$objectl->fk_product_father = $_POST["id"];
		$objectl->fk_unit_father    = $objpunit->fk_unit+0;
		$objectl->qty_father        = (GETPOST('qty_father')>0?GETPOST('qty_father'):1);
		$objectl->entity            = $conf->entity;
		$objectl->fk_product_son    = $idprod1;
		$objectl->fk_unit_son       = GETPOST("fk_unit_son")+0;
		//$objectl->fk_unit_son       = GETPOST("fk_unit_son");
		$objectl->qty_son           = GETPOST("qty_son");
		$objectl->statut            = 1;
		if ($objectl->fk_product_father && $objectl->fk_product_son)
		{
			$res = $objectl->create($user);
			if ($res > 0)
			{
				header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id.'&action=createnew');
				exit;
			}
			$action = 'createnew';
			$mesg='<div class="error">'.$object->error.'</div>';
		}
		else
		{
			$error++;
			$mesg='<div class="error">'.$langs->trans("ErrorRefRequired").'</div>';
			$action="createnew";
		}
	}
}
// Delete warehouse
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->productext->mat->del)
{
	$objectl = new Productlist($db);
	$objectl->fetch($_REQUEST["idr"]);
	if ($id == $objectl->fk_product_father)
	{
		$result=$objectl->delete($user);
		if ($result > 0)
		{
			header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
			exit;
		}
		else
		{
			$mesg='<div class="error">'.$object->error.'</div>';
			$action='';
		}
	}
	$mesg.='<div class="error">'.$langs->trans('Errornoidenticalproduct').'</div>';

	$action = '';
}

// Modification entrepot
if ($action == 'update' && $user->rights->productext->mat->write && $_POST["cancel"] <> $langs->trans("Cancel"))
{
	$object = new Productlist($db);
	if ($object->fetch($_POST["id"]))
	{
		$object->fk_product_father = GETPOST("idprod");
		$object->fk_unit_father    = GETPOST("fk_unit_father");
		$object->fk_product_son    = GETPOST("idprod1");
		$object->fk_unit_son       = GETPOST("fk_unit_son")+0;
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


if ($action == 'search')
{
	$lSearch = True;
	$action = 'create';
}

if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}



/*
 * View
 */

if (isset($_GET['id']))
	$object->fetch($_GET["id"]);
if ($object->id != $_GET["id"])
	$action = 'create';

$productstatic = new Product($db);
$form          = new Formv($db);

//$formcompany=new FormCompany($db);

$help_url='EN:Module_Fabrication_En|FR:Module_Fabrication|ES:M&oacute;dulo_Fabrication';
llxHeader("",$langs->trans("ApplicationsWarehouseCard"),$help_url);

$head=product_prepare_head($object);
$tab = 'material';
$titre = $langs->trans("Productsheet");
$picto=($object->type== Product::TYPE_SERVICE?'service':'product');
dol_fiche_head($head, $tab, $titre, 0, $picto);

if ($action == 'create' && $user->rights->productext->mat->write)
{
	print_fiche_titre($langs->trans("Newmaterial"));

	dol_htmloutput_mesg($mesg);
	print '<div>';
	print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="search">';
	print '<input type="text" name="search" value="'.$search.'">';
	print '<input type="submit" value="'.$langs->trans('Search').'">';
	print '</form>';
	print '</div>';

	if ($lSearch)
	{
		$sql = "SELECT ";
		$sql.= " p.rowid, p.ref, p.label, p.description ";
		$sql.= " FROM ".MAIN_DB_PREFIX."product AS p ";
	  // multilang
		if ($conf->global->MAIN_MULTILANGS)
	  // si l'option est active
		{
			$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."product_lang as pl ON pl.fk_product = p.rowid AND pl.lang = '".$langs->getDefaultLang() ."'";
		}

		$sql.= " WHERE p.entity IN (".$conf->entity.")";
		if ($search)
		{
	  // For natural search
			$params = array('p.ref', 'p.label', 'p.description', 'p.note');
	  // multilang
			if ($conf->global->MAIN_MULTILANGS)
	  // si l'option est active
			{
				$params[] = 'pl.description';
				$params[] = 'pl.note';
			}
			if (! empty($conf->barcode->enabled)) {
				$params[] = 'p.barcode';
			}
			$sql .= natural_search($params, $search);
		}
		$sql.= " GROUP BY p.rowid, p.ref, p.label, p.barcode, p.price, p.price_ttc, p.price_base_type,";
		$sql.= " p.fk_product_type, p.tms,";
		$sql.= " p.duration, p.tosell, p.tobuy, p.seuil_stock_alerte";
		$sql .= ', p.desiredstock';
		$resql = $db->query($sql);
		if ($resql)
		{
			$num = $db->num_rows($resql);
			if ($num)
			{
				print '<table class="liste" width="100%">';
		  // Lignes des titres
				print '<tr class="liste_titre">';
				print_liste_field_titre($langs->trans("Ref"), $_SERVER["PHP_SELF"], "p.ref",$param,"","",$sortfield,$sortorder);
				print_liste_field_titre($langs->trans("Label"), $_SERVER["PHP_SELF"], "p.label",$param,"","",$sortfield,$sortorder);

				$i = 0;
				$var = true;
				while ($i < $num)
				{
					$obj = $db->fetch_object($resql);
					$var = !$var;
					print '<tr '.$bc[$var].'>';
		  // ref
					print '<td width="5%">'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$obj->rowid.'">'.$obj->ref.'</a>'.'</td>';
		  // Label
					print '<td>'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$obj->rowid.'">'.$obj->label.'</a>'.'</td>';
					print '</tr>';
					$i++;
				}
				print '</table>';
			}
		}
	}
  // print '<table class="border" width="100%">';
  // print '<tr>';
  // print '<td>';
  // //product father
  // print '<table id="tablelines" class="noborder" width="100%">';
  // if ($action != 'editline')
  //   {

  //     $var=true;

  //     if ($conf->global->MAIN_FEATURES_LEVEL > 1)
  // 	{
  // 	  // Add free or predefined products/services
  // 	  $object->formAddObjectLine(1,$mysoc,$soc,$hookmanager);
  // 	}
  //     else
  // 	{
  // 	  // Add predefined products/services
  // 	  if (! empty($conf->product->enabled) || ! empty($conf->service->enabled))
  // 	    {
  // 	      $var=!$var;
  // 	      $$objProductadd->formAddPredefinedProduct_lm(0,$mysoc,$soc,$hookmanager);
  // 	    }
  // 	}

  //     $parameters=array();
  //     $reshook=$hookmanager->executeHooks('formAddObjectLine',$parameters,$$objProductadd,$action);    // Note that $action and $object may have been modified by hook
  //   }
  // print '</table>';
  // print '</td>';
  // print '</tr>';
  // print '</table>';
  //  print '</form>';
}
else
{
	if ($id>0)
	{
		dol_htmloutput_mesg($mesg);

		$objectl = new Productlist($db);
		$objproductf = new Product($db);
		$object = new Product($db);
		$result = $object->fetch($id);
		$resadd = $objProductadd->fetch(0,$id);
		$qty_father=0;
		if ($resadd==1)
		{
			$qty_father = $objProductadd->quant_material;
		}
	  //$objproducts = new Product($db);
	  // $objunitf    = new Units($db);
	  // $objunits    = new Units($db);
	  //buscamos el producto en llx_product_list
		//$objectl->fetch_product($id);

		//$filter = " AND t.fk_product_father = ".$id;
		//$res = $objectl->fetchAll('','',0,0,array(),'AND',$filter);
		//vamos a verificar la cantidad definidia para el padre
		//$qty_father = 0;
		//if ($res >0)
		//{
		//	foreach ($objectl->lines AS $j => $line)
		//	{
		//		$qty_father = $line->qty_father;
		//	}
		//}

	  //buscamos el producto
	  //buscamos la unidad
		//$objpunit->fetch('',$_GET['id']);
	  // if ($object->fk_product_father == $_GET["id"])
	  // 	{
	  // 	  $objunitf->fetch($object->fk_unit_father);
	  // 	}
		if ($result < 0)
		{
			dol_print_error($db);
		}


	  //noedit
		if ($action <> 'edit' && $action <> 're-edit')
		{
	  //$head = fabrication_prepare_head($object);

	  //dol_fiche_head($head, 'card', $langs->trans("Listmaterial"), 0, 'stock');

	  // Confirm delete third party
			if ($action == 'delete')
			{
				//$form = new Form($db);
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&idr='.$_REQUEST['idr'],$langs->trans("DeleteListProduct"),$langs->trans("ConfirmDeleteListProduct",$object->libelle),"confirm_delete",'',0,2);
				if ($ret == 'html') print '<br>';
			}

			print '<table class="border" width="100%">';
	  // ref
			print '<tr><td width="25%">'.$langs->trans("Ref").'</td><td colspan="3">';
			print $object->ref;
			print '</td></tr>';

	  // label
			print '<tr><td width="25%">'.$langs->trans("Label").'</td><td colspan="3">';
			print $object->label;
			print '</td></tr>';

	  //unit
			print '<tr><td>'.$langs->trans("Unit").'</td><td colspan="3">';
			$lCreate = true;
			print $object->getLabelOfUnit();
			//echo '<hr>'.$object->finished.' | '.$object->id;
			if ($object->finished <=0)
				$lCreate = false;

			//$objpunit->fetch($idr,$object->id);
			//if ($objpunit->fk_product == $object->id)
			//{
			//	print select_unit($objpunit->fk_unit,'fk_unit','',0,1,'rowid','label');
			//}
			print '</td></tr>';
			//qty father
			print '<tr><td>'.$langs->trans("Quantity").'</td><td colspan="3">';
			if ($user->rights->productext->mat->write && $action != 'editqty')
				print $qty_father.' '.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&action=editqty">'.img_picto($langs->trans('Edit'),'edit').'</a>';
			elseif ($id && $user->rights->productext->mat->write && $action == 'editqty')
			{
				print '<form action="'.$_SERVER['PHP_SELF'].'?id='.$id.'" method="post">';
				print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
				print '<input type="hidden" name="action" value="updateqty">';
				print '<input type="hidden" name="id" value="'.$id.'">';
				print '<input type="number" name="qty_father" value="'.$qty_father.'">';
				print '<input class="butAction" type="submit" value="'.$langs->trans('Save').'">';
				print '</form>';
			}
			else
			{
				print $qty_father;
			}
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

			if (empty($action))
			{

				if (($object->statut==1 || $object->statut==0 ) && $user->rights->productext->mat->write)
				{
					if ($lCreate)
						print "<a class=\"butActionDelete\" href=\"fiche.php?action=createnew&id=".$id."\">".$langs->trans("Create")."</a>";
				}
				else
					print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Create")."</a>";

		  // if (($object->statut==1 || $object->statut==0 ) && $user->rights->fabrication->supprimerlistproduct)
		  // 	print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
		  // else
		  // 	print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";


			}
			print "</div>";


			if ($lCreate)
			{
				print_fiche_titre($langs->trans("Newlistproduct"));

				dol_fiche_head();
				/*******************************/
				/*crear nuevo hijo*/
				/*******************************/

				dol_htmloutput_mesg($mesg);

				if ($action == 'createnew')
				{
					print '<table class="border" width="100%">';
					print '<tr>';
					print '<td>';
					//product father
					print '<table id="tablelines" class="noborder" width="100%">';
					$var=true;
					$object->qty_father = $qty_father;
					$objProductadd->qty_father = $qty_father;
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
							$objProductadd->formAddPredefinedProductSon(0,$mysoc,$soc,$hookmanager);
						}
					}

					$parameters=array();
					$reshook=$hookmanager->executeHooks('formAddObjectLine',$parameters,$objproductf,$action);
					// Note that $action and $object may have been modified by hook
					print '</table>';
					print '</td>';
					print '</tr>';
					print '</table>';
				}

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
				print_liste_field_titre($langs->trans("Unit"),"", "u.ref","&amp;id=".$_GET['id'],"",'align="center"',$sortfield,$sortorder);
				print_liste_field_titre($langs->trans("Quantity"),"", "pa.qty_son","&amp;id=".$_GET['id'],"",'align="right"');
				//	  print_liste_field_titre($langs->trans("Status"),"", "pa.statut","&amp;id=".$_GET['id'],"",'align="center"');
				print_liste_field_titre($langs->trans("Action"),"", "","","",'align="right"');
				print "</tr>";

				$totalunit=0;
				$totalvalue=$totalvaluesell=0;

				$sql = "SELECT pa.rowid, pa.fk_product_father,pa.fk_product_son as psrowid, p.ref, p.label as produit, pa.qty_father, pa.qty_son, pa.statut ";
				$sql.= " FROM ".MAIN_DB_PREFIX."product_list AS pa ";
				$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product AS p ON p.rowid = pa.fk_product_son ";

				$sql.= " WHERE ";
				$sql.= " pa.fk_product_father = '".$object->id."'";

				$sql.= $db->order($sortfield,$sortorder);

				dol_syslog('List products alternative sql='.$sql);
				$resql = $db->query($sql);
				if ($resql)
				{
					$objProduct = new Product($db);
					$num = $db->num_rows($resql);
					$i = 0;
					$var=True;
					while ($i < $num)
					{
						$unit = '';
						$objp = $db->fetch_object($resql);

						//vamos a asignar valorea al objeto product
						$productstatic->fetch($objp->psrowid);
						//$objpunit->fetch('',$objp->psrowid);
						//if ($objpunit->fk_product == $objp->psrowid)
						//$unit = $objProduct->getLabelOfUnit();
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
						print '<td align="left">';
						//$productstatic->id=$objp->psrowid;
						//$productstatic->ref=$objp->ref;
						//$productstatic->type=$objp->type;
						//$productstatic->fk_unit=$objp->rsrowid;
						print $productstatic->getNomUrl(1);
						print '</td>';
						print '<td align="left">'.$objp->produit.'</td>';
						print '<td align="center">'.$productstatic->getLabelOfUnit().'</td>';
						print '<td align="right">'.$objp->qty_son.'</td>';
						//		  print '<td align="center">'.$objp->statut.'</td>';
						if ($user->rights->productext->mat->del)
							print '<td align="right"><a href="'.DOL_URL_ROOT.'/productext/productlist/fiche.php?action=delete&id='.$objp->fk_product_father.'&idr='.$objp->rowid.'">'.img_picto($langs->trans("Clearmaterial"),'delete').'</a></td>';
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
				dol_fiche_end();

			}
		}
	}
}


llxFooter();

$db->close();
?>
