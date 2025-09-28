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
require_once(DOL_DOCUMENT_ROOT."/salary/lib/salary.lib.php");
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/member.lib.php';

$langs->load("salary");
$langs->load("members");
$langs->load("users");
$langs->load('other');

if (!$user->rights->salary->leersacharge)
	accessforbidden();

$id = GETPOST('rowid');

$object  = new Pcharge($db);
$objAdh  = new Adherent($db); //members

$objAdh->fetch($id);

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

$sql = "SELECT p.rowid AS id, p.fk_user, p.ref AS refcontract, p.basic, p.date_ini, p.date_fin, p.state, p.number_item, ";
$sql.= " d.ref AS refdepartament, ";
$sql.= " c.ref AS refcharge, ";
$sql.= " r.ref AS refproces, ";
$sql.= " cc.ref AS refcc, cc.label AS labelcc ";
$sql.= " FROM ".MAIN_DB_PREFIX."p_contract as p ";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."p_departament as d ON p.fk_departament = d.rowid ";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."p_charge as c ON p.fk_charge = c.rowid ";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."p_proces as r ON p.fk_proces = r.rowid ";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."p_centro_costo as cc ON p.fk_cc = cc.rowid ";

$sql.= " WHERE r.entity = ".$conf->entity;
if ($id)
{
	$sql.= " AND p.fk_user =".$id."";
}

if ($sall)
{
	$sql.= " AND (c.ref like '%".$sall."%' OR r.ref like '%".$sall."%' OR p.basic like '%".$sall."%')";
}
$sql.= " ORDER BY $sortfield $sortorder";
$sql.= $db->plimit($limit+1, $offset);
//echo $sql;
$result = $db->query($sql);
if ($result)
{
	$num = $db->num_rows($result);
	$i = 0;
	$help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
	llxHeader("",$langs->trans("Liste contract member"),$help_url);

	// print_barre_liste($langs->trans("Liste contract"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);
	//$head=salary_prepare_head($objAdh);
	$head = member_prepare_head($objAdh);
	dol_fiche_head($head, 'tabname2', $langs->trans("Member"),0,'user');

	print '<table class="noborder" width="100%">';

	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Ref"),"liste.php", "p.ref","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Item"),"liste.php", "p.number_item","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Costcenter"),"liste.php", "cc.ref","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Departament"),"liste.php", "d.ref","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Charge"),"liste.php", "c.ref","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Proces"),"liste.php", "r.ref","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Dateini"),"liste.php", "p.date_ini","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Datefin"),"liste.php", "p.date_fin","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Basic"),"liste.php", "p.basic","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Status"),"liste.php", "p.state","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Action"),"liste.php", "","","","");
	print "</tr>\n";
	//para mostrar el boton crear
	$lContract = True;
	if ($num) {
		$var=True;
		while ($i < min($num,$limit))
		{
			$obj = $db->fetch_object($result);
			if (empty($obj->date_fin))
				$lContract = False;
			$var=!$var;
			print "<tr $bc[$var]>";
			print '<td><a href="fiche.php?rowid='.$obj->fk_user.'&rid='.$obj->id.'">'.img_object($langs->trans("Ref"),'contract').' '.$obj->refcontract.'</a></td>';
			print '<td>'.$obj->number_item.'</td>';
			print '<td>'.$obj->refcc.' - '.$obj->labelcc.'</td>';
			print '<td>'.$obj->refdepartament.'</td>';
			print '<td>'.$obj->refcharge.'</td>';
			print '<td>'.$obj->refproces.'</td>';
			print '<td>'.dol_print_date($db->jdate($obj->date_ini),'daytext').'</td>';
			print '<td>'.dol_print_date($db->jdate($obj->date_fin),'daytext').'</td>';
			print '<td align="right">'.price($obj->basic).'</td>';
			print '<td align="left">'.LibState($obj->state,5).'</td>';
			print '<td align="center"><a href="fiche.php?rowid='.$obj->fk_user.'&rid='.$obj->id.'">'.img_picto($langs->trans("Edit"),'edit.png').'</a></td>';

			print "</tr>\n";
			$i++;
		}
	}

	$db->free($result);

	print "</table>";
	/* ************************************************************************** */
	/*                                                                            */
	/* Barre d'action                                                             */
	/*                                                                            */
	/* ************************************************************************** */

	print "<div class=\"tabsAction\">\n";

	if ($action == '' && ($num <=0 || $lContract))
	{
		if ($user->rights->salary->contract->creer)
			print '<a class="butAction" href="fiche.php?action=create&rowid='.$id.'">'.$langs->trans("Create").'</a>';
		else
			print '<a class="butActionRefused" href="#">'.$langs->trans("Modify").'</a>';
	}
	print "</div>";
}
else
{
	dol_print_error($db);
}


$db->close();

llxFooter();
?>
