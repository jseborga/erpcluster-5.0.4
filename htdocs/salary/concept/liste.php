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
require_once(DOL_DOCUMENT_ROOT."/salary/class/pconceptext.class.php");
require_once(DOL_DOCUMENT_ROOT."/salary/class/pformulas.class.php");
require_once(DOL_DOCUMENT_ROOT."/salary/lib/salary.lib.php");
if (! empty($conf->contab->enabled))
  require_once DOL_DOCUMENT_ROOT . '/contab/class/contabaccounting.class.php';

$langs->load("salary@salary");

if (!$user->rights->salary->concept->lire)
  accessforbidden();

$object = new Pconceptext($db);
if ($conf->contab->enabled)
  $objAccount = new Contabaccounting($db);

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

$sql  = "SELECT p.rowid AS id, p.ref, p.detail, p.details, p.ref_formula, ";
$sql .= "p.type_cod, p.print, p.fk_codfol, p.income_tax, p.percent, p.contab_account_ref, ";
$sql .= "d.ref AS reffol ";
$sql.= " FROM ".MAIN_DB_PREFIX."p_concept as p ";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_type_fol AS d ON p.fk_codfol = d.rowid ";
$sql.= " WHERE d.entity = ".$conf->entity;
$sql.= " AND p.entity = ".$conf->entity;


if ($sref)
  {
    $sql.= " AND p.ref like '%".$sref."%'";
  }
if ($sall)
  {
    $sql.= " AND (p.ref like '%".$sall."%' OR p.detail like '%".$sall."%' OR d.ref like '%".$sall."%' OR p.details like '%".$sall."%')";
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

    print_barre_liste($langs->trans("Liste concept"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

    print '<table class="noborder" width="100%">';

    print "<tr class=\"liste_titre\">";
    print_liste_field_titre($langs->trans("Ref"),"liste.php", "p.ref","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Detail"),"liste.php", "p.detail","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Typecod"),"liste.php", "p.type_cod","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Print"),"liste.php", "p.print","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Fol"),"liste.php", "d.ref","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Incometax"),"liste.php", "p.income_tax","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Percent"),"liste.php", "p.percent","","","",$sortfield,$sortorder);
    if ($conf->contab->enabled)
      {
	print_liste_field_titre($langs->trans("Account"),"liste.php", "p.contab_account_ref","","","",$sortfield,$sortorder);
      }
    print "</tr>\n";
    if ($num) {
      $var=True;
      while ($i < min($num,$limit))
	{
	  $obj = $db->fetch_object($result);
	  // if ($obj->fk_formula && empty($obj->ref_formula) )
	  // 	{
	  // 	  $objectf = new Pformulas($db);
	  // 	  $objectf->fetch($obj->fk_formula);
	  // 	  if ($objectf->id == $obj->fk_formula)
	  // 	    {
	  // 	      $object->fetch($obj->id);
	  // 	      $object->ref_formula = $objectf->codref;
	  // 	      $object->update($user);
	  // 	    }
	  // 	}
	  $var=!$var;
	  print "<tr $bc[$var]>";
	  print '<td><a href="fiche.php?id='.$obj->id.'">'.img_object($langs->trans("Ref"),'concept').' '.$obj->ref.'</a></td>';

	  print '<td>'.$obj->detail.'</td>';
	  print '<td>'.select_typecod($obj->type_cod,'type_cod','','',1,1).'</td>';
	  print '<td>'.select_yesno($obj->print,'print','','',1,1).'</td>';
	  print '<td>'.$obj->reffol.'</td>';
	  print '<td>'.select_incometax($obj->income_tax,'income_tax','','',1,1).'</td>';
	  print '<td>'.$obj->percent.'</td>';
	  if ($conf->contab->enabled)
	    {
	      $objAccount->fetch('',$obj->contab_account_ref);
	      print '<td>'.$objAccount->cta_name.'</td>';
	    }
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
