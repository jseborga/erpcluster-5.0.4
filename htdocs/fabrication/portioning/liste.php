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
 *      \file       htdocs/fabrication/productalternative/liste.php
 *      \ingroup    fabrication / productos alternativos
 *      \brief      Page liste productos alternativos
 */

require("../../main.inc.php");
//require_once(DOL_DOCUMENT_ROOT."/almacen/class/commonobject_.class.php");
require_once(DOL_DOCUMENT_ROOT."/fabrication/class/productportioningadd.class.php");
require_once(DOL_DOCUMENT_ROOT."/fabrication/units/class/units.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
if (! empty($conf->categorie->enabled))
	require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';

$langs->load("stocks");
$langs->load("product");
$langs->load("fabrication@fabrication");

if (!$user->rights->fabrication->port->leer)
	accessforbidden();

$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="t.rowid";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;

$portioning = new Productportioningadd($db);
$filter = array(1=>1);
$filterstatic = '';
$result = $portioning->fetchAll($sortorder, $sortfield, $limit, $offset, $filter, 'AND',$filterstatic,false);

if ($result)
{
	$i = 0;
	$help_url='EN:Module_Fabrication_En|FR:Module_Fabrication|ES:M&oacute;dulo_Fabrication';
	llxHeader("",$langs->trans("ListePortioningProduct"),$help_url);

	print_barre_liste($langs->trans("ListePortioningProduct"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

	print '<table class="noborder" width="100%">';

	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Ref"),"liste.php", "pl.rowid","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Product"),"liste.php", "p.ref","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Name"),"liste.php", "p.label","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Productportion"),"liste.php", "","","","","","");
	print_liste_field_titre($langs->trans("Name"),"liste.php", "","","","","","");

	print_liste_field_titre($langs->trans("Unit"),"liste.php", "u.ref","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Quantity"),"liste.php", "pl.qty","","",'align="right"',$sortfield,$sortorder);
	print "</tr>\n";
	$product = new Product($db);
	$objunit    = new Units($db);
	$var=True;
	$lines = $portioning->lines;
	for ($i=0; $i<$result; $i++)
	{
		$objp = $lines[$i];
		  //buscando el hijo principal
		$product->fetch($objp->fk_product);
		//$objunit->fetch($objp->fk_unit_alt);

		$var=!$var;
		print "<tr $bc[$var]>";
		print '<td><a href="fiche.php?id='.$objp->id.'">'.img_object($langs->trans("ShowProductPortion"),'action').' '.$objp->rowid.'</a></td>';
		  // if ($entrepot->id == $objp->fk_entrepot)
		  // 	print '<td>'.$entrepot->libelle.'</td>';
		  // else
		  // 	print '<td>&nbsp;</td>';

		print '<td><a href="'.DOL_URL_ROOT.'/product/fiche.php?id='.$objp->fk_product.'">'.$product->getNomUrl(1).'</a></td>';

		print '<td>'.$product->label.'</td>';
		$product->fetch($objp->fk_product_portion);
		print '<td><a href="'.DOL_URL_ROOT.'/product/fiche.php?id='.$objp->fk_product_portion.'">'.$product->getNomUrl(1).'</a></td>';

		print '<td>'.$product->label.'</td>';
		print '<td>'.'</td>';
		print '<td align="right">'.$objp->qty.'&nbsp;</td>';
		print "</tr>\n";
		$i++;
	}
	print "</table>";
}



$db->close();

llxFooter();
?>
