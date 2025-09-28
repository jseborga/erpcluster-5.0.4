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
require_once(DOL_DOCUMENT_ROOT."/salary/class/psalaryaprob.class.php");
require_once(DOL_DOCUMENT_ROOT."/salary/class/pcharge.class.php");
require_once(DOL_DOCUMENT_ROOT."/adherents/class/adherent.class.php");
require_once DOL_DOCUMENT_ROOT.'/salary/lib/adherent.lib.php';
require_once DOL_DOCUMENT_ROOT.'/salary/lib/salary.lib.php';

//require_once(DOL_DOCUMENT_ROOT."/salary/lib/salary.lib.php");

$langs->load("salary@salary");

if (!$user->rights->salary->salapr->lire)
  accessforbidden();

$object   = new Psalaryaprob($db);
$objectch = new Pcharge($db);
$objectad = new Adherent($db);

$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="p.sequen";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;

$sql  = "SELECT p.rowid AS id, p.type, p.fk_value, p.fk_aprobsup, p.state ";

$sql.= " FROM ".MAIN_DB_PREFIX."p_salary_aprob AS p ";      
$sql.= " WHERE p.entity = ".$conf->entity;

if ($sref)
{
    $sql.= " AND p.type like '%".$sref."%'";
}
if ($sall)
{
    $sql.= " AND (p.type like '%".$sall."%' OR p.sequen like '%".$sall."% ')";
}
$sql.= " ORDER BY fk_value, fk_aprobsup ";
$sql.= $db->plimit($limit+1, $offset);

$result = $db->query($sql);
if ($result)
  {
    $num = $db->num_rows($result);
    $i = 0;
    $help_url='EN:Module_Salary_En|FR:Module_Salary|ES:M&oacute;dulo_Salary';
    llxHeader("",$langs->trans("Liste approver"),$help_url);
    
    print_barre_liste($langs->trans("Liste approver"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);
    
    print '<table class="noborder" width="100%">';
    
    print "<tr class=\"liste_titre\">";
    print_liste_field_titre($langs->trans("Type"),"liste.php", "p.type","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Detail"),"liste.php", "p.fk_value","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Superior"),"liste.php", "p.sequen","","","",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Status"),"liste.php", "p.state","","",'align="right"',$sortfield,$sortorder);

    print "</tr>\n";
    if ($num) {
      $var=True;
      while ($i < min($num,$limit))
	{
	  $obj = $db->fetch_object($result);
	  
	  $var=!$var;
	  print "<tr $bc[$var]>";
	  print '<td><a href="fiche.php?id='.$obj->id.'">'.img_object($langs->trans("Type"),'generic').' '.select_typeapprov($obj->type,'type','','',1,1).'</a></td>';
	  if ($obj->type == 1)
	    {
	      //Personas
	      $objectad->fetch($obj->fk_value);
	      If ($objectad->id == $obj->fk_value)
		{
		  print '<td align="left">'.$objectad->lastname.' '.$objectad->firstname.'</td>';		  
		}
	      else
		print '<td align="right">&nbps;</td>';
	    }
	  if ($obj->type == 2)
	    {
	      //Cargo
	      $objectch->fetch($obj->fk_value);
	      If ($objectch->id == $obj->fk_value)
		{
		  print '<td align="left">'.$objectch->codref.'</td>';		  
		}
	      else
		print '<td align="right">&nbps;</td>';
	    }
	  //buscando al superior
	  $object->fetch($obj->fk_aprobsup);
	  if ($object->id == $obj->fk_aprobsup)
	    {
	      if ($object->type == 1)
		{
		  //Personas
		  $objectad->fetch($object->fk_value);
		  If ($objectad->id == $object->fk_value)
		    {
		      print '<td align="left">'.$objectad->lastname.' '.$objectad->firstname.'</td>';
		    }
		  else
		    print '<td align="right">&nbps;</td>';
		}
	      if ($object->type == 2)
		{
		  //Cargo
		  $objectch->fetch($object->fk_value);
		  If ($objectch->id == $object->fk_value)
		    {
		      print '<td align="left">'.$objectch->codref.'</td>';		  
		    }
		  else
		    print '<td align="right">&nbps;</td>';
		}
	    }
	  else
	    print '<td>&nbsp;</td>';
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
