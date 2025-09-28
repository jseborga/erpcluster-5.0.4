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
 *      \file       htdocs/salary/salarycharge/liste.php
 *      \ingroup    Salary
 *      \brief      Page liste des salary for charge
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/salary/class/pcharge.class.php");
require_once(DOL_DOCUMENT_ROOT."/salary/class/psalarycharge.class.php");

$langs->load("salary@salary");

if (!$user->rights->salary->leersacharge)
  accessforbidden();

$object = new Pcharge($db);

$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="p.detail";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;

$sql  = "SELECT p.rowid AS id, c.ref AS refcharge, p.detail, p.nivel, d.ref AS refdpto, p.salary_practiced, p.salary_market, p.salary_calc ";
$sql.= " FROM ".MAIN_DB_PREFIX."p_salary_charge as p ";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_charge AS c ON p.fk_charge = c.rowid ";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_departament AS d ON c.fk_dpto = d.rowid ";
$sql.= " WHERE d.entity = ".$conf->entity;

if ($sref)
{
    $sql.= " AND p.detail like '%".$sref."%'";
}
if ($sall)
{
    $sql.= " AND (p.detail like '%".$sall."%' OR p.nivel like '%".$sall."%' OR d.ref like '%".$sall."%' OR c.ref like '%".$sall."%')";
}
$sql.= " ORDER BY $sortfield $sortorder";
$sql.= $db->plimit($limit+1, $offset);

$result = $db->query($sql);
if ($result)
{
	$num = $db->num_rows($result);
	$i = 0;
	$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
	llxHeader("",$langs->trans("Liste salary for charge"),$help_url);

	print_barre_liste($langs->trans("Liste salary charges"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

	print '<table class="noborder" width="100%">';

	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Details"),"liste.php", "p.detail","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Departament"),"liste.php", "d.ref","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Charge"),"liste.php", "c.ref","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Nivel"),"liste.php", "p.nivel","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Salarypracticed"),"liste.php", "p.salary_practiced","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Salarymarket"),"liste.php", "p.salary_market","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Salarycalc"),"liste.php", "p.salarycalc","","","",$sortfield,$sortorder);
	print "</tr>\n";
	if ($num) {
	  $var=True;
	  while ($i < min($num,$limit))
	    {
	      $obj = $db->fetch_object($result);
	      $var=!$var;
	      print "<tr $bc[$var]>";
	      print '<td><a href="fiche.php?id='.$obj->id.'">'.img_object($langs->trans("Ref"),'detail').' '.$obj->detail.'</a></td>';
	      
	      print '<td>'.$obj->refdpto.'</td>';
	      print '<td>'.$obj->refcharge.'</td>';
	      print '<td>'.$obj->nivel.'</td>';
	      print '<td align="right">'.price($obj->salary_practiced).'</td>';
	      print '<td align="right">'.price($obj->salary_market).'</td>';
	      print '<td align="right">'.price($obj->salary_calc).'</td>';

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
