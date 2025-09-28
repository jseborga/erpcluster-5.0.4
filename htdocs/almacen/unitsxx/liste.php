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
 *      \file       htdocs/units/liste.php
 *      \ingroup    Unidades
 *      \brief      Page liste des solicitudes a Unidades
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/units/class/units.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");

$langs->load("units@units");
$langs->load("almacen@almacen");

if (!$user->rights->almacen->leerunidad)
  accessforbidden();

$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="u.ref";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;

$sql  = "SELECT u.rowid, u.ref as ref, u.description ";
$sql.= " FROM ".MAIN_DB_PREFIX."units as u";
$sql.= " WHERE u.rowid <> ''";
if ($sref)
  $sql.= " AND u.ref like '%".$sref."%'";

if ($sall)
  $sql.= " AND (u.ref like '%".$sall."%' OR u.description like '%".$sall."%')";

$sql.= " ORDER BY $sortfield $sortorder";
$sql.= $db->plimit($limit+1, $offset);

$result = $db->query($sql);
if ($result)
  {
    $num = $db->num_rows($result);
    $i = 0;
    $help_url='EN:Module_Units_En|FR:Module_Units|ES:M&oacute;dulo_Units';
    llxHeader("",$langs->trans("ListUnitsToApplications"),$help_url);
    
    print_barre_liste($langs->trans("ListUnitsToApplications"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);
    
    print '<table class="noborder" width="100%">';
    
    print "<tr class=\"liste_titre\">";
    print_liste_field_titre($langs->trans("Ref"),"liste.php", "u.ref","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Description"),"liste.php", "u.description","","","",$sortfield,$sortorder);
    print "</tr>\n";
    if ($num) {
      $var=True;
      while ($i < min($num,$limit))
	{
	  $objp = $db->fetch_object($result);
	  $var=!$var;
	  print "<tr $bc[$var]>";
	  print '<td><a href="fiche.php?id='.$objp->rowid.'">'.img_object($langs->trans("ShowUnits"),'stock').' '.$objp->ref.'</a></td>';
	  print '<td>'.$objp->description.'</td>';
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
