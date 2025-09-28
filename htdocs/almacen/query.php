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
 *      \file       htdocs/almacen/liste.php
 *      \ingroup    almacen
 *      \brief      Page liste des solicitudes a almacenes
 */

require("../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/commonobject_.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacen.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacendet.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/stock/class/entrepot.class.php");
require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/fabrication/class/fabrication.class.php");

$langs->load("stocks");
$langs->load("almacen@almacen");

if (!$user->rights->almacen->leerpedido)
	accessforbidden();

$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="sa.ref";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;
$date  = dol_mktime(12, 0, 0, GETPOST('remonth'),  GETPOST('reday'),  GETPOST('reyear'));
$date  = dol_mktime(12, 0, 0, 7,  3,  2016);


$sql  = "SELECT sa.rowid, sa.ref as ref, sa.fk_entrepot, ";
if ($conf->fabrication->enabled)
	$sql .= " f.ref as ref_fabrication,  ";
$sql.= " sa.date_creation, sa.date_delivery, sa.description, sa.statut";
$sql.= " FROM ".MAIN_DB_PREFIX."sol_almacen as sa";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."sol_almacendet AS sad ON sad.fk_almacen = sa.rowid ";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product AS p ON sad.fk_product = p.rowid ";
$sql.= " WHERE sa.entity = ".$conf->entity;
$sql.= " AND sa.date_create = ".$date;

$sql.= " ORDER BY p.ref ";
$sql.= $db->plimit($limit+1, $offset);
echo $sql;exit;
$result = $db->query($sql);
if ($result)
{
	$num = $db->num_rows($result);
	$i = 0;
	$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';
	llxHeader("",$langs->trans("ListStockToApplications"),$help_url);

	print_barre_liste($langs->trans("ListStockToApplications"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

	print '<table class="noborder" width="100%">';

	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Ref"),"liste.php", "sa.ref","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Entrepot"),"liste.php", "sa.fk_entrepot","","","",$sortfield,$sortorder);
	if ($conf->fabrication->enabled)
		print_liste_field_titre($langs->trans("OrderProduction"),"liste.php", "f.ref","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("dateCreation"),"liste.php", "sa.date_creation","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("dateApplication"),"liste.php", "sa.date_delivery","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Status"),"liste.php", "sa.statut",'','','align="right"',$sortfield,$sortorder);
	print "</tr>\n";
	if ($num)
	{
		$almacen     = new Solalmacen($db);
		$entrepot    = new Entrepot($db);
		$var=True;
		while ($i < min($num,$limit))
		{
			$objp = $db->fetch_object($result);
			$entrepot->fetch($objp->fk_entrepot);

			$var=!$var;
			print "<tr $bc[$var]>";
			print '<td><a href="fiche.php?id='.$objp->rowid.'">'.img_object($langs->trans("ShowWarehouse"),'stock').' '.$objp->ref.'</a></td>';
			if ($entrepot->id == $objp->fk_entrepot)
				print '<td>'.$entrepot->libelle.'</td>';
			else
				print '<td>&nbsp;</td>';
			if ($conf->fabrication->enabled)
				print '<td>'.$objp->ref_fabrication.'</td>';
			print '<td>'.dol_print_date($objp->date_creation,'day').'</td>';
			print '<td>'.dol_print_date($objp->date_delivery,'day').'</td>';
			print '<td align="right">'.$almacen->LibStatut($objp->statut,'',5).'</td>';
			print "</tr>\n";
			$i++;
		}
	}

	$db->free($result);

	print "</table>";

}
else
{
	dol_print_error($db);
}


$db->close();

llxFooter();
?>
