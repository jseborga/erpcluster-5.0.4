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
 *      \file       htdocs/contab/accounts/liste.php
 *      \ingroup    Contab chart of account
 *      \brief      Page liste des chart of account
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/contab/class/contabaccountingext.class.php");
require_once(DOL_DOCUMENT_ROOT."/contab/lib/contab.lib.php");

//$langs->load("stocks");
$langs->load("contab@contab");

if (!$user->rights->contab->account->read)
  accessforbidden();

$object = new Contabaccountingext($db);

$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="ca.ref";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;

$sql  = "SELECT ca.rowid, ca.ref, ca.cta_class, ca.cta_normal, ca.cta_top, ca.cta_name, ca.statut ";
$sql.= " FROM ".MAIN_DB_PREFIX."contab_accounting as ca";
$sql.= " WHERE ca.entity = ".$conf->entity;
if ($sref)
{
    $sql.= " AND ca.ref like '%".$sref."%'";
}
if ($sall)
{
    $sql.= " AND (ca.cta_name like '%".$sall."%' OR ca.cta_normal like '%".$sall."%' OR ca.cta_class like '%".$sall."%')";
}
$sql.= " ORDER BY $sortfield $sortorder";
$sql.= $db->plimit($limit+1, $offset);

$result = $db->query($sql);
if ($result)
{
	$num = $db->num_rows($result);
	$i = 0;
	$help_url='EN:Module_Contab_En|FR:Module_Contab|ES:M&oacute;dulo_Contab';
	llxHeader("",$langs->trans("Chartofaccountlist"),$help_url);

	print_barre_liste($langs->trans("Listaccounting"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

	print '<table class="noborder" width="100%">';

	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Ref"),"liste.php", "ca.ref","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Name"),"liste.php", "ca.cta_name","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Accountingtop"),"liste.php", "ca.cta_top","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Class"),"liste.php", "ca.cta_class","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Normal"),"liste.php", "ca.cta_normal","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Status"),"liste.php", "p.statut",'','','align="right"',$sortfield,$sortorder);
	print "</tr>\n";
	if ($num) {
	  $var=True;
	  while ($i < min($num,$limit))
	    {
	      $obj = $db->fetch_object($result);
	      $var=!$var;
	      print "<tr $bc[$var]>";
	      print '<td><a href="fiche.php?id='.$obj->rowid.'">'.img_object($langs->trans("Ref"),'account').' '.$obj->ref.'</a></td>';
	      print '<td>'.$obj->cta_name.'</td>';
	      $object->fetch($obj->cta_top);
	      if ($object->id == $obj->cta_top)
		print '<td>'.trim($object->ref).' '.$object->cta_name.'</td>';
	      else
		print '<td>&nbsp;</td>';
	      print '<td>'.select_cta_clase($obj->cta_class,'','','','',1).'</td>';
	      print '<td>'.select_cta_normal($obj->cta_normal,'','','','',1).'</td>';

	      print '<td align="right">'.$object->LibStatut($obj->statut,1).'</td>';
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
