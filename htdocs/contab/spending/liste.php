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
 *      \file       htdocs/contab/spending/liste.php
 *      \ingroup    Contab spending account
 *      \brief      Page liste des spending account contable
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/contab/class/contabspendingaccount.class.php");
require_once(DOL_DOCUMENT_ROOT."/contab/lib/contab.lib.php");

$langs->load("stocks");
$langs->load("contab@contab");

if (!$user->rights->contab->leerperiod)
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

$object = new Contabspendingaccount($db);
$sql  = "SELECT p.rowid, p.ref, p.fk_account, p.state, ";
$sql.= " c.account_number AS refaccount, c.label AS cta_name, ";
$sql.= " t.label AS libelle ";
$sql.= " FROM ".MAIN_DB_PREFIX."contab_spending_account as p";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."accounting_account as c ON p.fk_account = c.rowid";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."c_type_fees AS t ON p.ref = t.code";
$sql.= " WHERE p.entity = ".$conf->entity;
if ($sref)
{
    $sql.= " AND p.ref_month like '%".$sref."%'";
}
if ($sall)
{
    $sql.= " AND (p.ref like '%".$sall."%' OR c.label like '%".$sall."%' OR t.libelle like '%".$sall."%')";
}
$sql.= " ORDER BY $sortfield $sortorder";
$sql.= $db->plimit($limit+1, $offset);

$result = $db->query($sql);
if ($result)
{
	$num = $db->num_rows($result);
	$i = 0;
	$help_url='EN:Module_Contab_En|FR:Module_Contab|ES:M&oacute;dulo_Contab';
	llxHeader("",$langs->trans("Managementaccounting"),$help_url);

	print_barre_liste($langs->trans("Listaccountspending"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

	print '<table class="noborder" width="100%">';
	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Ref"),"liste.php", "p.ref","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Account"),"liste.php", "c.cta_name","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Status"),"liste.php", "p.state",'','','align="right"',$sortfield,$sortorder);
	print "</tr>\n";
	if ($num) {
	  $var=True;
	  while ($i < min($num,$limit))
	    {
	      $objp = $db->fetch_object($result);
	      $var=!$var;
	      print "<tr $bc[$var]>";
	      print '<td><a href="fiche.php?id='.$objp->rowid.'">'.img_object($langs->trans("Showaccount"),'account').' '.$langs->trans($objp->libelle).'</a></td>';
	      print '<td>'.$objp->refaccount.' '.$objp->cta_name.'</td>';
	      print '<td align="right">'.LibState($objp->state,4).'</td>';
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
