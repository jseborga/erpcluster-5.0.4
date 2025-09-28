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
 *      \file       htdocs/salary/period/liste.php
 *      \ingroup    Salary period
 *      \brief      Page liste Salary period
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/salary/class/pperiodext.class.php");
require_once(DOL_DOCUMENT_ROOT."/salary/class/pproces.class.php");
require_once(DOL_DOCUMENT_ROOT."/salary/class/ptypefolext.class.php");
require_once(DOL_DOCUMENT_ROOT."/salary/lib/salary.lib.php");

//require_once(DOL_DOCUMENT_ROOT."/salary/lib/salary.lib.php");

$langs->load("salary@salary");

if (!$user->rights->salary->period->lire)
  accessforbidden();

$object = new Pproces($db);

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

$sql  = "SELECT p.rowid AS id, p.ref, p.mes, p.anio, p.date_ini, p.date_fin, p.date_pay, p.date_court, p.date_close, p.state, ";
$sql.= " pr.ref AS prref, pr.label AS prlabel, ";
$sql.= " tf.ref AS tfref, tf.detail AS tfdetail ";
$sql.= " FROM ".MAIN_DB_PREFIX."p_period AS p ";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_proces AS pr ON p.fk_proces = pr.rowid ";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_type_fol AS tf ON p.fk_type_fol = tf.rowid ";

$sql.= " WHERE p.entity = ".$conf->entity;

if ($sref)
{
    $sql.= " AND p.ref like '%".$sref."%'";
}
if ($sall)
{
    $sql.= " AND (p.ref like '%".$sall."%' OR p.mes like '%".$sall."% ')";
}
$sql.= " ORDER BY $sortfield $sortorder";
$sql.= $db->plimit($limit+1, $offset);

$result = $db->query($sql);
if ($result)
  {
    $num = $db->num_rows($result);
    $i = 0;
    $help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
    llxHeader("",$langs->trans("Liste period"),$help_url);

    print_barre_liste($langs->trans("Liste period"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

    print '<table class="noborder" width="100%">';

    print "<tr class=\"liste_titre\">";
    print_liste_field_titre($langs->trans("Ref"),"liste.php", "p.ref","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Proces"),"liste.php", "pr.ref","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Typefol"),"liste.php", "tf.ref","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Month"),"liste.php", "p.mes","","",'align="right"',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Year"),"liste.php", "p.anio","","",'align="right"',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Dateini"),"liste.php", "p.date_ini","","",'align="right"',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Datefin"),"liste.php", "p.date_fin","","",'align="right"',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Datepay"),"liste.php", "p.date_pay","","",'align="right"',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Datecourt"),"liste.php", "p.date_court","","",'align="right"',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Dateclose"),"liste.php", "p.date_close","","",'align="right"',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Status"),"liste.php", "p.state","","",'align="right"',$sortfield,$sortorder);


    print "</tr>\n";
    if ($num) {
      $var=True;
      while ($i < min($num,$limit))
	{
	  $obj = $db->fetch_object($result);

	  $var=!$var;
	  print "<tr $bc[$var]>";
	  print '<td><a href="fiche.php?id='.$obj->id.'">'.img_object($langs->trans("Ref"),'calendar').' '.$obj->ref.'</a></td>';
	  print '<td align="right">'.$obj->prref.'</td>';
	  print '<td align="right">'.$obj->tfref.'</td>';
	  print '<td align="right">'.$obj->mes.'</td>';
	  print '<td align="right">'.$obj->anio.'</td>';
	  print '<td align="right">'.$obj->date_ini.'</td>';
	  print '<td align="right">'.$obj->date_fin.'</td>';
	  print '<td align="right">'.$obj->date_pay.'</td>';
	  print '<td align="right">'.$obj->date_court.'</td>';
	  print '<td align="right">'.$obj->date_close.'</td>';
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
