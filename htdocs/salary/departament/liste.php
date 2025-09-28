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
require_once(DOL_DOCUMENT_ROOT."/salary/class/pdepartamentext.class.php");

//require_once(DOL_DOCUMENT_ROOT."/salary/lib/salary.lib.php");

$langs->load("salary@salary");

if (!$user->rights->salary->dpto->lire)
	accessforbidden();

$object = new Pdepartamentext($db);
$objectf = new Pdepartamentext($db);

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
$sql  = "SELECT p.rowid AS id, p.ref, p.label, p.fk_father, p.fk_user_resp, p.active, p.status, ";
$sql.= "u.lastname, u.firstname ";
$sql.= " FROM ".MAIN_DB_PREFIX."p_departament as p ";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."user AS u ON p.fk_user_resp = u.rowid ";
$sql.= " WHERE p.entity = ".$conf->entity;

if ($sref)
{
	$sql.= " AND p.ref like '%".$sref."%'";
}
if ($sall)
{
	$sql.= " AND (p.name like '%".$sall."%' OR u.firstname like '%".$sall."%' OR u.email like '%".$sall."%')";
}

$sql.= " ORDER BY $sortfield $sortorder";

$sql.= $db->plimit($limit+1, $offset);

$result = $db->query($sql);
if ($result)
{
	$num = $db->num_rows($result);
	$i = 0;
	$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
	llxHeader("",$langs->trans("Liste departament"),$help_url);

	print_barre_liste($langs->trans("Liste departament"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

	print '<table class="noborder" width="100%">';

	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Ref"),"liste.php", "p.ref","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Label"),"liste.php", "p.ref","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Father"),"liste.php", "u.name","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Name"),"liste.php", "u.firstname","","",'align="right"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Active"),"liste.php", "p.active","","",'align="center"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Statut"),"liste.php", "p.status","","",'align="center"',$sortfield,$sortorder);
	print "</tr>\n";
	if ($num) {
		$var=True;
		while ($i < min($num,$limit))
		{
			$obj = $db->fetch_object($result);

			$object->ref = $obj->ref;
			$object->id = $obj->id;
			$object->status = $obj->status;

	      //$object = new Pdepartament($db);
			$objectf->fetch($obj->fk_father);

			$var=!$var;
			print "<tr $bc[$var]>";
			print '<td>'.$object->getNomUrl(1).'</td>';
			print '<td>'.$obj->label.'</td>';
			if ($objectf->id == $obj->fk_father)
			{
				print '<td>'.$objectf->getNomUrl(1).'</td>';
			}
			else
			{
				print '<td>&nbsp;</td>';
			}
			print '<td align="right">'.$obj->name.' '.$obj->firstname.'</td>';
			print '<td align="center">'.($obj->active?img_picto('','switch_on'):img_picto('','switch_off')).'</td>';
			print '<td align="center">'.$object->getLibStatut(1).'</td>';
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
