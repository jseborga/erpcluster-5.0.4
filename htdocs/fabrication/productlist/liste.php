<?php
/* Copyright (C) 2001-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *      \file       htdocs/fabrication/productlist/liste.php
 *      \ingroup    fabrication
 *      \brief      Page liste des product list
 */

require("../../main.inc.php");
//require_once(DOL_DOCUMENT_ROOT."/almacen/class/commonobject_.class.php");
require_once(DOL_DOCUMENT_ROOT."/fabrication/productlist/class/productlist.class.php");
//require_once(DOL_DOCUMENT_ROOT."/fabrication/units/class/units.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
if (! empty($conf->categorie->enabled))
	require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
if (! empty($conf->almacen->enabled))
	require_once DOL_DOCUMENT_ROOT.'/almacen/class/productunit.class.php';

$langs->load("stocks");
$langs->load("product");
$langs->load("fabrication@fabrication");

if (!$user->rights->fabrication->leerlistproduct)
	accessforbidden();
$action = GETPOST('action');
$id = GETPOST('id','int');
$search_ref=isset($_GET["search_ref"])?$_GET["search_ref"]:$_POST["search_ref"];
$search_nomf=isset($_GET["search_nomf"])?$_GET["search_nomf"]:$_POST["search_nomf"];
$search_unitf=isset($_GET["search_unitf"])?$_GET["search_unitf"]:$_POST["search_unitf"];
$search_refh=isset($_GET["search_refh"])?$_GET["search_refh"]:$_POST["search_refh"];
$search_nomh=isset($_GET["search_nomh"])?$_GET["search_nomh"]:$_POST["search_nomh"];
$search_unith=isset($_GET["search_unith"])?$_GET["search_unith"]:$_POST["search_unith"];
$search_status=isset($_GET["search_status"])?$_GET["search_status"]:$_POST["search_status"];

if (isset($_POST['nosearch_x']) || isset($_GET['nosearch_x']))
{
	$search_ref='';
	$search_nomf='';
	$search_unitf='';
	$search_refh='';
	$search_unith='';
	$search_nomh='';
	$search_statut = 9;
}


$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];

if (! $sortfield) $sortfield="p.ref";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;

$object = new Productlist($db);
if ($id) $object->fetch($id);
//action

if ($action == 'confirm_delete' && $_REQUEST['confirm'] == 'yes' && $user->rights->fabrication->supprimerlistproduct)
{
	$object->fetch($id);
	if ($object->id == $id)
	{
		$res = $object->delete($user);
		header('Location: '.$_SERVER['PHP_SELF']);
		exit;
	}
}
$sql  = "SELECT pl.rowid, pl.fk_product_father, pl.fk_unit_father, pl.fk_product_son, pl.fk_unit_son, pl.qty_father, pl. qty_son, pl.statut, p.ref, p.label ";
$sql.= " FROM ".MAIN_DB_PREFIX."product_list as pl";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product AS p ON pl.fk_product_father = p.rowid ";
$sql.= " WHERE pl.entity = ".$conf->entity;
if ($sref)
{
	$sql.= " AND p.ref like '%".$sref."%'";
}
if ($sall)
{
	$sql.= " AND (p.ref like '%".$sall."%' OR u.ref like '%".$sall."%' OR pl.qty_son like '%".$sall."%' OR pl.qty_father like '%".$sall."%')";
}
if ($search_ref) $sql.= " AND p.ref LIKE '%".$search_ref."%'";
if ($search_nomf) $sql.= " AND p.label LIKE '%".$search_nomf."%'";

$sql.= " ORDER BY $sortfield $sortorder";
$sql.= $db->plimit($limit+1, $offset);

//echo $sql;
$form = new Form($db);
$result = $db->query($sql);
if ($result)
{
	$num = $db->num_rows($result);
	$i = 0;
	$help_url='EN:Module_Fabrication_En|FR:Module_Fabrication|ES:M&oacute;dulo_Fabrication';
	llxHeader("",$langs->trans("ListMaterial"),$help_url);

	print_barre_liste($langs->trans("ListMaterial"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

	// Lignes des champs de filtre
	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';


	print '<table class="noborder" width="100%">';

	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Ref"),"liste.php", "p.rowid","","","",$sortfield,$sortorder);
	//print_liste_field_titre($langs->trans("Productfather"),"liste.php", "p.ref","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Name"),"liste.php", "p.label","","","",$sortfield,$sortorder);

	print_liste_field_titre($langs->trans("Unit"),"liste.php", "u.ref","","","",$sortfield,$sortorder);
	//print_liste_field_titre($langs->trans("Quantityfather"),"liste.php", "pl.qty_father","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Productson"),"liste.php", "","","","","","");
	print_liste_field_titre($langs->trans("Name"),"liste.php", "","","","","","");
	print_liste_field_titre($langs->trans("Unitson"),"liste.php", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Quantityson"),"liste.php", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Statusd"),"liste.php", "","","","",$sortfield,$sortorder);
	print "</tr>\n";

	print '<tr class="liste_titre">';
	print '<td class="liste_titre">';
	print '<input class="flat" size="10" type="text" name="search_ref" value="'.$search_ref.'">';
	print '</td><td class="liste_titre" align="left">';
	print '<input class="flat" type="text" name="search_nomf" value="'.$search_nomf.'">';
	print '</td><td class="liste_titre" align="left">';
	print '<input class="flat" type="text" size="10" name="search_unitf" value="'.$search_unitf.'">';
	//print '</td><td class="liste_titre" align="center">';
	print '</td><td class="liste_titre" align="center">';
	print '<input class="flat" type="text" size="10" name="search_refh" value="'.$search_refh.'">';
	print '</td><td class="liste_titre" align="center">';
	print '<input class="flat" type="text" size="10" name="search_nomh" value="'.$search_nomh.'">';
	print '</td><td class="liste_titre" align="center">';
	print '<input class="flat" type="text" size="8" name="search_unith" value="'.$search_unith.'">';
	print '</td><td align="right" class="liste_titre">';
	print '</td><td class="liste_titre" align="center">';
	print '<input type="image" class="liste_titre" name="button_search" src="'.DOL_URL_ROOT.'/theme/'.$conf->theme.'/img/search.png"  value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
	print '&nbsp;';
	print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Searchclear")).'" name="nosearch" title="'.dol_escape_htmltag($langs->trans("Searchclear")).'">';
	print '</td></tr>';

	if ($num)
	{
		$objproduct = new Product($db);
		$objproducth = new Product($db);
		$var=True;

		while ($i < min($num,$limit))
		{
			$selref = '';
			$selrefh = '';
			$objp = $db->fetch_object($result);
			$objproduct->fetch($objp->fk_product_father);
			$unit = $langs->trans($objproduct->getLabelOfUnit('short'));
			if ($conf->almacen->enabled && $abc)
			{
				$objproductunit = new Productunit($db);
				$objproductunit->fetch('',$objp->fk_product_father);
				if ($objproductunit->fk_product > 0)
				{
					$unit = $objproductunit->getLabelOfUnit();
				}
			}
			$objproducth->fetch($objp->fk_product_son);
			$unith = $langs->trans($objproducth->getLabelOfUnit('short'));
			if ($conf->almacen->enabled && $abc)
			{
				$objproductunit = new Productunit($db);
				$objproductunit->fetch('',$objp->fk_product_son);
				if ($objproductunit->fk_product > 0)
				{
					$unith = $objproductunit->getLabelOfUnit();
				}
			}
			if ($id == $objp->rowid)
			{
				$selref = $objproduct->ref.' '.$objproduct->label;
				$selrefh = $objproducth->ref.' '.$objproducth->label;
			}
			//verificamos el filtro
			$lView = true;
			if ($search_unitf)
				if (STRTOUPPER($unit) != STRTOUPPER($search_unitf)) $lView = false;
			if ($search_refh)
			{
				if ($lView)
				{
					$pos = strpos(STRTOUPPER($objproducth->ref), STRTOUPPER($search_refh));
					if ($pos === false) $lView = false;
				}
			}
			if ($search_nomh)
			{
				if ($lView)
				{
					$pos = strpos(STRTOUPPER($objproducth->label), STRTOUPPER($search_nomh));
					if ($pos === false) $lView = false;
				}
			}
			if ($search_unith)
				if (STRTOUPPER($unith) != STRTOUPPER($search_unith)) $lView = false;


			if ($lView)
			{
				$var=!$var;
				print "<tr $bc[$var]>";
	      //	      print '<td><a href="fiche.php?id='.$objp->rowid.'">'.img_object($langs->trans("ShowListProduct"),'action').' '.$objp->rowid.'</a></td>';
	      // if ($entrepot->id == $objp->fk_entrepot)
	      // 	print '<td>'.$entrepot->libelle.'</td>';
	      // else
	      // 	print '<td>&nbsp;</td>';

				print '<td>';
				print $objproduct->getNomUrl(1);
				print '</td>';

				print '<td>'.$objproduct->label.'</td>';
				print '<td>'.$unit.'</td>';
			//print '<td>'.$objp->qty_father.'</td>';
	      	      		//buscando el hijo principal

				//$objproduct->fetch($objp->fk_product_son);
				$unit = '';
				if ($conf->almacen->enabled)
				{
					$objproductunit = new Productunit($db);
					$objproductunit->fetch('',$objp->fk_product_son);
					if ($objproductunit->fk_product > 0)
					{
						$unit = $objproductunit->getLabelOfUnit();
					}
				}

				print '<td>';
				print $objproducth->getNomUrl(1);
				print '</td>';

				//if ($objproduct->id == $objp->fk_product_son)
					print '<td>'.$objproducth->label.'</td>';
				//else
				//	print '<td>&nbsp;</td>';
				print '<td>'.$unith.'</td>';
				print '<td>'.$objp->qty_son.'</td>';
				print '<td>';
				if ($user->rights->fabrication->supprimerlistproduct)
				{
					print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$objp->rowid.'&action=delete">'.img_picto($langs->trans('Delete'),'delete').'</a>';
				}
				print '</td>';
				print "</tr>\n";
			}
			$i++;
		}
	}
		  // Confirm delete third party
	if ($action == 'delete')
	{
				//$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("DeleteMaterial"),$langs->trans("ConfirmDeleteMaterial",$selref).' <b>'.$selref.'</b> '.$langs->trans('y material hijo').' <b>'.$selrefh.'</b>',"confirm_delete",'',0,2);
		if ($ret == 'html') print '<br>';
	}

	$db->free($result);

	print "</table>";
	print '</form>';
}
else
{
	dol_print_error($db);
}


$db->close();

llxFooter();
?>
