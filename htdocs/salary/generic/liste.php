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
 *      \file       htdocs/salary/generictable/liste.php
 *      \ingroup    Table generics
 *      \brief      Page liste des salary generic table
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/salary/class/pgenerictableext.class.php");
require_once(DOL_DOCUMENT_ROOT."/salary/class/pgenericfieldext.class.php");
require_once(DOL_DOCUMENT_ROOT."/salary/lib/salary.lib.php");

$langs->load("salary@salary");

if (!$user->rights->salary->generic->lire)
  accessforbidden();

$object  = new Pgenerictableext($db);
$objectt = new Pgenericfieldext($db);

$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="p.table_cod";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;

$sql  = "SELECT p.rowid AS id, p.table_cod, p.table_name, p.sequen, p.field_name, p.state ";
$sql.= " FROM ".MAIN_DB_PREFIX."p_generic_table as p ";
$sql.= " WHERE p.entity = ".$conf->entity;

if ($sref)
{
    $sql.= " AND p.table_cod like '%".$sref."%'";
}
if ($sall)
{
    $sql.= " AND (p.table_cod like '%".$sall."%' OR p.table_name like '%".$sall."%' OR p.field_name like '%".$sall."%' OR p.sequen like '%".$sall."%')";
}
$sql.= " ORDER BY $sortfield $sortorder";
$sql.= $db->plimit($limit+1, $offset);

$result = $db->query($sql);
if ($result)
{
	$num = $db->num_rows($result);
	$i = 0;
	$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
	llxHeader("",$langs->trans("Liste generic table"),$help_url);

	print_barre_liste($langs->trans("Liste generic table"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

	print '<table class="noborder" width="100%">';

	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Tablecod"),"liste.php", "p.table_cod","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Tablename"),"liste.php", "p.table_name","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Fieldname"),"liste.php", "p.field_name","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Sequen"),"liste.php", "p.sequen","",'','align="center"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Status"),"liste.php", "p.state","","",'align="right"',$sortfield,$sortorder);

	print "</tr>\n";
	if ($num) {
	  $var=True;
	  while ($i < min($num,$limit))
	    {
	      $obj = $db->fetch_object($result);
	      //buscamos y reemplazamos
// 	      $object->fetch($obj->id);
// 	      if ($object->id == $obj->id)
// 		{
// 		  $sqlr = "UPDATE ".MAIN_DB_PREFIX."p_generic_field SET generic_table_ref = '".$object->ref."'";
// echo '<hr>'.		  $sqlr.= " WHERE fk_generic_table = ".$object->id;
// 		  $db->query($sqlr);
// 		  //actualizamos
// 		  $object->update($user);
// 		}
	      $var=!$var;
	      print "<tr $bc[$var]>";
	      print '<td><a href="fiche.php?id='.$obj->id.'">'.img_object($langs->trans("Tablecod"),'generic').' '.$obj->table_cod.'</a></td>';

	      print '<td>'.$obj->table_name.'</td>';
	      print '<td>'.$obj->field_name.'</td>';
	      print '<td align="center">'.$obj->sequen.'</td>';
	      print '<td align="right">'.libState($obj->state,1).'</td>';
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
