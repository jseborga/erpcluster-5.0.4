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

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/commonobject_.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/productlist/class/productlist.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/units/class/units.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
if (! empty($conf->categorie->enabled))
	require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';

$langs->load("stocks");
$langs->load("product");
$langs->load("almacen@almacen");

if (!$user->rights->almacen->leerunidad)
  accessforbidden();

$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="p.ref";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;


$sql  = "SELECT pl.rowid, p.rowid AS prowid, p.ref AS ref, p.label AS label, u.ref AS unit, pl.statut ";
$sql.= " FROM ".MAIN_DB_PREFIX."product_list as pl";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product as p ";
$sql.= " ON pl.fk_product_father = p.rowid ";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."units as u ";
$sql.= " ON pl.fk_unit_father = u.rowid ";
$sql.= " WHERE pl.entity = ".$conf->entity;
if ($sref)
{
    $sql.= " AND p.ref like '%".$sref."%'";
}
if ($sall)
{
    $sql.= " AND (p.ref like '%".$sall."%' OR u.ref like '%".$sall."%' OR p.label like '%".$sall."%')";
}
$sql.= " ORDER BY $sortfield $sortorder";
$sql.= $db->plimit($limit+1, $offset);

$result = $db->query($sql);
if ($result)
  {
    $num = $db->num_rows($result);
    $i = 0;
    $help_url='EN:Module_Fabrication_En|FR:Module_Fabrication|ES:M&oacute;dulo_Fabrication';
    llxHeader("",$langs->trans("Warehouse"),$help_url);
    
    print_barre_liste($langs->trans("ListUnitsToApplications"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);
    
    print '<table class="noborder" width="100%">';
    
    print "<tr class=\"liste_titre\">";
    print_liste_field_titre($langs->trans("Ref"),"liste.php", "p.rowid","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Name"),"liste.php", "p.label","","","",$sortfield,$sortorder);
    
    print_liste_field_titre($langs->trans("Unit"),"liste.php", "u.ref","","","",$sortfield,$sortorder);
    print "</tr>\n";
    if ($num) {
      $objproduct = new Product($db);
      $objunit    = new Units($db);
      $var=True;
      $refAnt = '';
      while ($i < min($num,$limit))
	{
	  $objp = $db->fetch_object($result);
	  if ($refAnt != $objp->ref)
	    {
	      $refAnt = $objp->ref;      
	      $var=!$var;
	      print "<tr $bc[$var]>";
	      print '<td><a href="'.DOL_URL_ROOT.'/almacen/productlist/fiche.php?id='.$objp->prowid.'">'.img_object($langs->trans("ShowListProduct"),'action').' '.$objp->label.'</a></td>';
	      
	      print '<td>'.$objp->label.'</td>';
	      print '<td>'.$objp->unit.'</td>';
	      print "</tr>\n";
	    }
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
