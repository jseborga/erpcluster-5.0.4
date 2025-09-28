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
 *      \file       htdocs/salary/concept/liste.php
 *      \ingroup    Salary concept
 *      \brief      Page liste des salary concept
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/salary/lib/salary.lib.php");
require_once(DOL_DOCUMENT_ROOT."/salary/class/pconceptext.class.php");

$langs->load("salary@salary");

if (!$user->rights->salary->pay->lire)
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

//recuperar parametro concepto liquido pagable
$concept = $conf->global->SALARY_CONCEPT_LIQUID_PAYMENT;
$objConcept = new Pconceptext($db);
$objConcept->fetch_ref($concept);
if (empty($concept))
  {
    print $mesg='<div class="error">'.$langs->trans('Error. no esta definido la variable SALARY_CONCEPT_LIQUID_PAYMENT').'</div>';
    exit;
  }
$fk_concept = $objConcept->id;
if (empty($fk_concept))
  $fk_concept = 0;

//$fk_concept = 73;
$sql = "SELECT";
$sql.= " t.rowid,";

$sql.= " t.entity,";
$sql.= " t.fk_salary_present,";
$sql.= " t.fk_proces,";
$sql.= " t.fk_type_fol,";
$sql.= " t.fk_concept,";
$sql.= " t.fk_period,";
$sql.= " t.fk_user,";
$sql.= " t.fk_cc,";
$sql.= " t.type,";
$sql.= " t.cuota,";
$sql.= " t.semana,";
$sql.= " t.amount_inf,";
$sql.= " t.amount,";
$sql.= " t.hours_info,";
$sql.= " t.hours,";
$sql.= " t.date_reg,";
$sql.= " t.date_create,";
$sql.= " t.fk_user_create,";
$sql.= " t.fk_account,";
$sql.= " t.payment_state,";
$sql.= " t.state, ";
$sql.= " p.anio, p.mes, ";
$sql.= " a.firstname, a.lastname ";
$sql.= " FROM ".MAIN_DB_PREFIX."p_salary_history as t";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_period AS p ON t.fk_period = p.rowid ";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."adherent AS a ON t.fk_user = a.rowid ";
$sql.= " WHERE t.entity = ".$conf->entity;
$sql.= " AND t.fk_concept IN (".$fk_concept.")";

if ($sref)
  {
    $sql.= " AND p.anio like '%".$sref."%'";
  }
if ($sall)
  {
    $sql.= " AND (p.anio like '%".$sall."%' OR p.mes like '%".$sall."%' OR a.lastname like '%".$sall."%' OR a.firstname like '%".$sall."%')";
  }
$sql.= " ORDER BY $sortfield $sortorder";
$sql.= $db->plimit($limit+1, $offset);

$result = $db->query($sql);
if ($result)
  {
    $num = $db->num_rows($result);
    $i = 0;
    $help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
    llxHeader("",$langs->trans("Liste concept"),$help_url);

    print_barre_liste($langs->trans("Liste payments"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

    print '<table class="noborder" width="100%">';

    print "<tr class=\"liste_titre\">";
    print_liste_field_titre($langs->trans("Year"),"liste.php", "p.anio","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Month"),"liste.php", "p.mes","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Firstname"),"liste.php", "a.firstname","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Lastname"),"liste.php", "a.lastname","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Amount"),"liste.php", "t.amount","","",'align="right"',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Paymentstate"),"liste.php", "t.payment_state","","",'align="right"',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Action"),"", "","","","");
    print "</tr>\n";
    if ($num) {
      $var=True;
      while ($i < min($num,$limit))
	{
	  $obj = $db->fetch_object($result);
	  $var=!$var;
	  print "<tr $bc[$var]>";
	  print '<td>'.$obj->anio.'</a></td>';
	  print '<td>'.$obj->mes.'</td>';
	  print '<td>'.$obj->firstname.'</td>';
	  print '<td>'.$obj->lastname.'</td>';
	  print '<td align="right">'.price($obj->amount,'MU').'</td>';
	  print '<td align="right">'.libState($obj->payment_state,6).'</td>';

	  print '<td></td>';
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
