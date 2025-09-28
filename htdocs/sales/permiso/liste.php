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
 *      \file       htdocs/cajachica/permisosip/liste.php
 *      \ingroup    cajachica
 *      \brief      Page liste des permisos terceros almacenes
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/sales/permiso/class/entrepotbanksoc.class.php");
require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/stock/class/entrepot.class.php");
require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");

$langs->load("stocks");
$langs->load("sales");
if (!$user->rights->sales->leerPermiso)
  accessforbidden();

$objuser= new User($db);

$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="c.numero_ip";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;

$modPermission = $conf->global->VENTA_PERMISSIONS_PDV_MOD;


$sql  = "SELECT c.rowid, c.numero_ip as ref, c.series, ";
$sql.= " s.nom, e.lieu, c.status, b.label, ";
$sql.= " u.lastname, u.firstname, ";
$sql.= " su.label AS subsidiary ";
$sql.= " FROM ".MAIN_DB_PREFIX."entrepot_bank_soc as c ";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe as s ON c.fk_socid = s.rowid ";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."entrepot as e ON c.fk_entrepotid = e.rowid ";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."bank_account as b ON c.fk_cajaid = b.rowid ";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."subsidiary as su ON c.fk_subsidiaryid = su.rowid ";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."user as u ON c.fk_user = u.rowid ";

// $sql.= " ON c.fk_socid = s.rowid ";
// $sql.= " AND c.fk_entrepotid = e.rowid ";
// $sql.= " AND c.fk_cajaid = b.rowid ";
$sql.= " WHERE c.entity = ".$conf->entity;
if (empty($modPermission))
{
	$sql.= " AND (c.fk_user IS NULL OR c.fk_user = 0) ";
}
else
{
	$sql.= " AND (c.numero_ip IS NULL OR c.numero_ip = '') ";
}
if ($sref)
{
    $sql.= " AND c.numero_ip like '%".$sref."%'";
}
if ($sall)
{
    $sql.= " AND (c.numero_ip like '%".$sall."%' OR s.nom like '%".$sall."%' OR e.lieu like '%".$sall."%')";
}
$sql.= " ORDER BY $sortfield $sortorder";
$sql.= $db->plimit($limit+1, $offset);
$result = $db->query($sql);
if ($result)
{
	$num = $db->num_rows($result);

	$i = 0;

	$help_url='EN:Module_CajaChica_En|FR:Module_CajaChica|ES:M&oacute;dulo_CajaChica';
	llxHeader("",$langs->trans("ListOfPermissions"),$help_url);

	print_barre_liste($langs->trans("ListOfPermissions"), $page, "listeip.php", "", $sortfield, $sortorder,'',$num);

	print '<table class="noborder" width="100%">';

	print "<tr class=\"liste_titre\">";
	if (empty($modPermission))
		print_liste_field_titre($langs->trans("Ref"),"liste.php", "c.numero_ip","","","",$sortfield,$sortorder);
	else
		print_liste_field_titre($langs->trans("User"),"liste.php", "u.lastname","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Societe"),"liste.php", "s.nom","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("EntrepotPDV"),"liste.php", "e.lieu","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Account"),"liste.php", "b.label","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Subsidiary"),"liste.php", "su.label","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Serie"),"liste.php", "c.series","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Status"),"liste.php", "c.status",'','','align="right"',$sortfield,$sortorder);
	print "</tr>\n";
	if ($num) {
	  //$cajaChica=new CajaChicaentrepot($db);
		$var=True;
		while ($i < min($num,$limit))
		{
			$objp = $db->fetch_object($result);
			$var=!$var;
			print "<tr $bc[$var]>";
			if (empty($modPermission))
				print '<td><a href="fiche.php?id='.$objp->rowid.'">'.img_object($langs->trans("Viewpermission"),'stock').' '.$objp->ref.'</a></td>';
			else
				print '<td><a href="fiche.php?id='.$objp->rowid.'">'.img_object($langs->trans("Viewpermission"),'stock').' '.$objp->lastname.' '.$objp->firstname.'</a></td>';
			//print '<td>'.$objp->lastname.' '.$objp->firstname.'</td>';
			print '<td>'.$objp->nom.'</td>';
			print '<td>'.$objp->lieu.'</td>';
			print '<td>'.$objp->label.'</td>';
			print '<td>'.$objp->subsidiary.'</td>';
			print '<td>'.$objp->series.'</td>';
			print '<td align="right">'.$objp->status.'</td>';
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
