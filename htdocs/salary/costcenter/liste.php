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
 *      \file       htdocs/salary/centrocosto/liste.php
 *      \ingroup    Salary centro costo
 *      \brief      Page liste Salary centro costo
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/salary/class/pcentrocosto.class.php");
require_once(DOL_DOCUMENT_ROOT."/salary/lib/salary.lib.php");

$langs->load("salary@salary");

if (!$user->rights->salary->cc->lire)
  accessforbidden();

$object = new Pcentrocosto($db);

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

$sql  = "SELECT p.rowid AS id, p.ref, p.label, p.fk_cc_sup, p.stipulation, p.locked, p.state ";
$sql.= " FROM ".MAIN_DB_PREFIX."p_centro_costo AS p ";
$sql.= " WHERE p.entity = ".$conf->entity;
if ($sref)
{
    $sql.= " AND p.ref like '%".$sref."%'";
}
if ($sall)
{
    $sql.= " AND (p.ref like '%".$sall."%' OR p.label like '%".$sall."% ')";
}
$sql.= " ORDER BY $sortfield $sortorder";
$sql.= $db->plimit($limit+1, $offset);

$result = $db->query($sql);
if ($result)
  {
    $num = $db->num_rows($result);
    $i = 0;
    $help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
    llxHeader("",$langs->trans("Liste cost center"),$help_url);
    
    print_barre_liste($langs->trans("Liste cost center"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);
    
    print '<table class="noborder" width="100%">';
    
    print "<tr class=\"liste_titre\">";
    print_liste_field_titre($langs->trans("Ref"),"liste.php", "p.ref","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Label"),"liste.php", "p.label","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Sup"),"liste.php", "p.fk_cc_sup","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Condition"),"liste.php", "p.stipulation","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Locked"),"liste.php", "p.locked","","",'align="center"',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Status"),"liste.php", "p.state","","",'align="right"',$sortfield,$sortorder);

    print "</tr>\n";
    if ($num) {
      $var=True;
      while ($i < min($num,$limit))
	{
	  $obj = $db->fetch_object($result);
	  
	  $var=!$var;
	  print "<tr $bc[$var]>";
	  print '<td><a href="fiche.php?id='.$obj->id.'">'.img_object($langs->trans("Ref"),'centrocosto').' '.$obj->ref.'</a></td>';
	  print '<td align="left">'.$obj->label.'</td>';
	  $object->fetch($obj->fk_cc_sup);
	  if ($object->id == $obj->fk_cc_sup)
	    print '<td align="left">'.$object->label.'</td>';
	  else
	    print '<td align="left">&nbsp;</td>';
	  print '<td align="center">'.select_cta_normal($obj->stipulation,'stipulation','','',1,1).'</td>';
	  print '<td align="center">'.select_yesno($obj->locked,'locked','','',1,1).'</td>';
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
