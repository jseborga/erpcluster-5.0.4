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
 *      \file       htdocs/contab/period/liste.php
 *      \ingroup    Contab period
 *      \brief      Page liste des period contable
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/contab/class/contabseatext.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
require_once(DOL_DOCUMENT_ROOT."/contab/lib/contab.lib.php");
require_once(DOL_DOCUMENT_ROOT."/contab/lib/seats.lib.php");

$langs->load("stocks");
$langs->load("contab@contab");

if (!$user->rights->contab->leerseatma)
	accessforbidden();

$aArraySeat = seat_bank();
$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="p.lote";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;

$object = new Contabseat($db);
$sql  = "SELECT p.rowid, p.date_seat, p.lote, p.sblote, p.doc, p.status, p.currency, p.type_seat, p.history, p.debit_total, p.credit_total ";
$sql.= " FROM ".MAIN_DB_PREFIX."contab_seat as p";
$sql.= " WHERE p.entity = ".$conf->entity;
if ($sref)
{
	$sql.= " AND p.lote like '%".$sref."%'";
}
if ($sall)
{
	$sql.= " AND (p.lote like '%".$sall."%' OR p.sblote like '%".$sall."%' OR p.doc like '%".$sall."%')";
}
$sql.= " ORDER BY $sortfield $sortorder";
$sql.= $db->plimit($limit+1, $offset);

$result = $db->query($sql);
if ($result)
{
	$num = $db->num_rows($result);
	$i = 0;
	$help_url='EN:Module_Contab_En|FR:Module_Contab|ES:M&oacute;dulo_Contab';
	llxHeader("",$langs->trans("Accountingperiodlist"),$help_url);

	print_barre_liste($langs->trans("Seats"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

	print '<table class="noborder" width="100%">';

	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Lote"),"liste.php", "p.lote","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Sblote"),"liste.php", "p.sblote","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Doc"),"liste.php", "p.doc","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Date"),"liste.php", "p.date_seat","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Currency"),"liste.php", "p.currency","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Typeseat"),"liste.php", "p.type_seat","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("History"),"liste.php", "p.history","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Debit"),"liste.php", "p.debit_total","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Credit"),"liste.php", "p.credit_total","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Status"),"liste.php", "p.statut",'','','align="right"',$sortfield,$sortorder);
	print "</tr>\n";
	if ($num) {
		$var=True;
		while ($i < min($num,$limit))
		{
			$objp = $db->fetch_object($result);
			$var=!$var;
			print "<tr $bc[$var]>";
			print '<td><a href="fiche.php?id='.$objp->rowid.'">'.img_object($langs->trans("Showseat"),'seat','').' '.$objp->lote.'</a></td>';
			print '<td>'.$objp->sblote.'</td>';
			print '<td>'.$objp->doc.'</td>';
			print '<td>'.dol_print_date($db->jdate($objp->date_seat),'daytext').'</td>';

			print '<td>'.select_currency($objp->currency,'currency','','',1,1).'</td>';
			print '<td>'.select_type_seat($objp->type_seat,'type_seat','','',1,1).'</td>';
			print '<td>'.$objp->history.'</td>';
			print '<td align="right">'.price($objp->debit_total).'</td>';
			print '<td align="right">'.price($objp->credit_total).'</td>';
			print '<td align="right">'.$object->LibStatut($objp->state,'',4).'</td>';
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
