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
 *      \file       htdocs/salary/forms/liste.php
 *      \ingroup    Salary
 *      \brief      Page liste des salary elaborados
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/salary/class/pcharge.class.php");
require_once(DOL_DOCUMENT_ROOT."/salary/class/psalarycharge.class.php");
require_once(DOL_DOCUMENT_ROOT."/salary/lib/salary.lib.php");
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/salary/class/psalarypresentext.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/member.lib.php';

$langs->load("salary@salary");

if (!$user->rights->salary->leersacharge)
  accessforbidden();

$id = GETPOST('rowid');

$object  = new Psalarypresentext($db);
$objAdh  = new Adherent($db); //members

$objAdh->fetch($id);

$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="p.mes";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;

//select de los pagos realizados al empleado

$sql = "SELECT sp.fk_period, p.ref, p.mes, p.anio ";
$sql.= " FROM ".MAIN_DB_PREFIX."p_salary_present as sp ";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_period as p ON sp.fk_period = p.rowid ";

$sql.= " WHERE sp.entity = ".$conf->entity;
$sql.= " AND sp.fk_user = ".$id;
$sql.= " GROUP BY sp.fk_period, p.ref, p.mes, p.anio";
$sql.= " ORDER BY $sortfield $sortorder";
$sql.= $db->plimit($limit+1, $offset);
echo $sql;
$result = $db->query($sql);
if ($result)
  {
    $num = $db->num_rows($result);
    $i = 0;
    $help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
    llxHeader("",$langs->trans("Liste contract member"),$help_url);
    
    // print_barre_liste($langs->trans("Liste salary"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);
    $head=member_prepare_head($objAdh);
    dol_fiche_head($head, 'tabname3', $langs->trans("Member"),0,'user');
    
    print '<table class="noborder" width="100%">';
    
    print "<tr class=\"liste_titre\">";
    print_liste_field_titre($langs->trans("Action"),"liste.php", "","","","");
    print_liste_field_titre($langs->trans("Month"),"liste.php", "p.ref","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Year"),"liste.php", "cc.ref","","","",$sortfield,$sortorder);
    print "</tr>\n";
    if ($num) {
      $var=True;
      while ($i < min($num,$limit))
	{
	  $obj = $db->fetch_object($result);
	  $var=!$var;
	  print "<tr $bc[$var]>";
	  // print '<td><a href="fiche.php?ref='.$obj->ref.'&rowid='.$id.'">'.img_picto($langs->trans("File"),'file').' '.$obj->ref.'</a></td>';
	  print '<td>'.img_picto($langs->trans("File"),'file').' '.$obj->ref.'</td>';	      
	  print '<td>'.$obj->mes.'</td>';
	  print '<td>'.$obj->anio.'</td>';
	  
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
