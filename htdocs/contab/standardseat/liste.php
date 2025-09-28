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
 *      \file       htdocs/contab/standardseat/liste.php
 *      \ingroup    Contab Standard Seat
 *      \brief      Page liste des Standard Seat contable
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/contab/class/contabstandardseat.class.php");
require_once(DOL_DOCUMENT_ROOT."/contab/lib/contab.lib.php");

$langs->load("contab@contab");

if (!$user->rights->contab->leerpoint)
  accessforbidden();

$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="pe.ref";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;

$sql  = "SELECT ss.rowid AS rowid, pe.ref, pe.description AS pedescription, ss.sequence, ss.status, ss.description AS ssdescription, ss.type_seat, ss.type_balance, ss.debit_account, ss.credit_account, ss.currency, ss.currency_value1, ss.currency_value2, ss.history, ss.history_group, ss.origin ";
$sql.= " FROM ".MAIN_DB_PREFIX."contab_standard_seat as ss";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."contab_point_entry as pe";
$sql.= " ON ss.fk_point_entry = pe.rowid AND ss.entity = pe.entity ";
$sql.= " WHERE ss.entity = ".$conf->entity;

if ($sref)
{
    $sql.= " AND pe.ref like '%".$sref."%'";
}
if ($sall)
{
    $sql.= " AND (pe.ref like '%".$sall
      ."%' OR pe.description like '%".$sall
      ."%' OR ss.type_seat like '%".$sall
      ."%' OR ss.type_balance like '%".$sall
      ."%' OR ss.description like '%".$sall."%')";
}
$sql.= " ORDER BY $sortfield $sortorder";
$sql.= $db->plimit($limit+1, $offset);

$result = $db->query($sql);
if ($result)
{
	$num = $db->num_rows($result);
	$i = 0;
	$help_url='EN:Module_Contab_En|FR:Module_Contab|ES:M&oacute;dulo_Contab';
	llxHeader("",$langs->trans("StandardSeatlist"),$help_url);

	print_barre_liste($langs->trans("ListStandardSeat"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

	print '<table class="noborder" width="100%">';

	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Ref"),"liste.php", "pe.ref","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Sequence"),"liste.php", "ss.sequence","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Status"),"liste.php", "ss.status","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Description"),"liste.php", "ss.description","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Typeseat"),"liste.php", "ss.type_seat","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Typebalance"),"liste.php", "ss.type_balance","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Debitaccount"),"liste.php", "ss.debit_account","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Creditaccount"),"liste.php", "ss.credit_account","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Currency"),"liste.php", "ss.currency","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Currencyvalue1"),"liste.php", "ss.currency_value1","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Currencyvalue2"),"liste.php", "ss.currency_value2","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("History"),"liste.php", "ss.history","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Historygroup"),"liste.php", "ss.history_group","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Origin"),"liste.php", "ss.origin","","","",$sortfield,$sortorder);

	print "</tr>\n";
	if ($num) {
	  $var=True;
	  while ($i < min($num,$limit))
	    {
	      $objp = $db->fetch_object($result);
	      $var=!$var;
	      print "<tr $bc[$var]>";
	      print '<td><a href="fiche.php?id='.$objp->rowid.'">'.img_object($langs->trans("Showstandardseat"),'account').' '.$objp->ref.'</a></td>';
	      print '<td>'.$objp->sequence.'</td>';
	      print '<td>'.$objp->status.'</td>';
	      print '<td>'.$objp->ssdescription.'</td>';
	      print '<td>'.$objp->type_seat.'</td>';
	      print '<td>'.$objp->type_balance.'</td>';
	      print '<td>'.$objp->debit_account.'</td>';
	      print '<td>'.$objp->credit_account.'</td>';
	      print '<td>'.$objp->currency.'</td>';
	      print '<td>'.$objp->currency_value1.'</td>';
	      print '<td>'.$objp->currency_value2.'</td>';
	      print '<td>'.$objp->history.'</td>';
	      print '<td>'.$objp->history_group.'</td>';
	      print '<td>'.$objp->origin.'</td>';
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
