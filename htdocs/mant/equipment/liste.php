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
 *      \file       htdocs/mant/equipment/liste.php
 *      \ingroup    Equipment
 *      \brief      Page liste des chart of equipment
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/mant/equipment/class/mequipment.class.php");
//require_once(DOL_DOCUMENT_ROOT."/mant/lib/mant.lib.php");

$langs->load("mant@mant");

if (!$user->rights->mant->teacher->leer)
  accessforbidden();

$object = new Mequipment($db);

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
$sql  = "SELECT p.rowid AS id, p.ref, p.ref_ext, p.nom AS name, p.trademark, p.model, p.anio, p.fk_location, p.statut, ";
$sql.= " l.detail AS location, ";
$sql.= " m.ref AS property ";
$sql.= " FROM ".MAIN_DB_PREFIX."m_equipment as p ";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."m_location AS l ON p.fk_location = l.rowid ";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."m_property AS m ON l.fk_property = m.rowid ";
$sql.= " WHERE p.entity = ".$conf->entity;

if ($sref)
{
    $sql.= " AND p.ref like '%".$sref."%'";
}
if ($sall)
{
    $sql.= " AND (p.nom like '%".$sall."%' OR l.detail like '%".$sall."%' OR m.reg like '%".$sall."%')";
}
$sql.= " ORDER BY $sortfield $sortorder";
$sql.= $db->plimit($limit+1, $offset);

$result = $db->query($sql);
if ($result)
  {
    $num = $db->num_rows($result);
    $i = 0;
    $help_url='EN:Module_Mant_En|FR:Module_Mant|ES:M&oacute;dulo_Mant';
    llxHeader("",$langs->trans("Liste departament"),$help_url);

    print_barre_liste($langs->trans("Liste equipment"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);
    
    print '<table class="noborder" width="100%">';
    
    print "<tr class=\"liste_titre\">";
    print_liste_field_titre($langs->trans("Ref"),"liste.php", "p.ref","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Refext"),"liste.php", "p.ref_ext","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Name"),"liste.php", "p.nom","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Trademark"),"liste.php", "p.trademark","","",'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Model"),"liste.php", "p.model","","",'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Year"),"liste.php", "p.anio","","",'align="right"',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Location"),"liste.php", "l.detail","","",'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Property"),"liste.php", "m.ref","","",'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Status"),"liste.php", "p.statut","","",'align="right"',$sortfield,$sortorder);
    print "</tr>\n";
    if ($num) {
	  $var=True;
	  while ($i < min($num,$limit))
	    {
	      $obj = $db->fetch_object($result);

	      $var=!$var;
	      print "<tr $bc[$var]>";
	      print '<td><a href="fiche.php?id='.$obj->id.'">'.img_object($langs->trans("Ref"),'equipment').' '.$obj->ref.'</a></td>';
	      print '<td>'.$obj->ref_ext.'</td>';
	      print '<td>'.$obj->name.'</td>';
	      print '<td>'.$obj->trademark.'</td>';
	      print '<td>'.$obj->model.'</td>';
	      print '<td>'.$obj->anio.'</td>';
	      print '<td>'.$obj->location.'</td>';
	      print '<td>'.$obj->property.'</td>';

	      print '<td align="right">'.$object->libStatut($obj->statut,0).'</td>';
	      print "</tr>\n";
	      $i++;
	    }
	}

	$db->free($result);

	print "</table>";
	print "<div class=\"tabsAction\">\n";
	  
	if ($action == '')
	  {
	    if ($user->rights->mant->teacher->crear)
	      print "<a class=\"butAction\" href=\"fiche.php?action=create\">".$langs->trans("Createnew")."</a>";
	    else
	      print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";
	  }
	print '</div>';

}
else
{
  dol_print_error($db);
}


$db->close();

llxFooter();
?>
