<?php
/* Copyright (C) 2013-2013 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *      \file       htdocs/contab/pointentry/liste.php
 *      \ingroup    Contab Point entry
 *      \brief      Page liste des point entry contable
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/contab/class/contabpointentry.class.php");
require_once(DOL_DOCUMENT_ROOT."/contab/lib/contab.lib.php");

$langs->load("contab@contab");

if (!$user->rights->contab->leerpoint)
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

$sql  = "SELECT p.rowid, p.ref, p.description, p.cfglan ";
$sql.= " FROM ".MAIN_DB_PREFIX."contab_point_entry as p";
$sql.= " WHERE p.entity = ".$conf->entity;
if ($sref)
{
    $sql.= " AND p.ref like '%".$sref."%'";
}
if ($sall)
{
    $sql.= " AND (p.ref like '%".$sall."%' OR p.description like '%".$sall."%' OR p.cfglan like '%".$sall."%')";
}
$sql.= " ORDER BY $sortfield $sortorder";
$sql.= $db->plimit($limit+1, $offset);

$result = $db->query($sql);
if ($result)
{
	$num = $db->num_rows($result);
	$i = 0;
	$help_url='EN:Module_Contab_En|FR:Module_Contab|ES:M&oacute;dulo_Contab';
	llxHeader("",$langs->trans("PointEntrylist"),$help_url);

	print_barre_liste($langs->trans("Listpointsentry"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

	print '<table class="noborder" width="100%">';

	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Ref"),"liste.php", "p.ref","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Description"),"liste.php", "p.description","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Cfgonline"),"liste.php", "p.cfglan","","","",$sortfield,$sortorder);
	print "</tr>\n";
	if ($num) {
	  $var=True;
	  while ($i < min($num,$limit))
	    {
	      $objp = $db->fetch_object($result);
	      $var=!$var;
	      print "<tr $bc[$var]>";
	      print '<td><a href="fiche.php?id='.$objp->rowid.'">'.img_object($langs->trans("Showpointentry"),'point').' '.$objp->ref.'</a></td>';
	      print '<td>'.$objp->description.'</td>';
	      print '<td>'.select_cfglan($objp->cfglan,'','','','',1).'</td>';

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
