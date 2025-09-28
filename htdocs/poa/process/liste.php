<?php
/* Copyright (C) 2014-2014 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *      \file       htdocs/poa/process/liste.php
 *      \ingroup    Process
 *      \brief      Page liste des process
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/poa/process/class/poaprocess.class.php");

$langs->load("poa@poa");

if (!$user->rights->poa->proc->leer)
  accessforbidden();

$object = new Poaprocess($db);

$id = GETPOST('id');
$action = GETPOST('action');

$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];
$filter = GETPOST('filter');
if ($action == 'sub')
  $filter.= ','.$id;
$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];

$gestion = $_SESSION['gestion'];

if (! $sortfield) $sortfield="pr.nro_preventive";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;
if (empty($filter))
  $filter = -1;
$sql = "SELECT p.rowid AS id, p.gestion, p.ref, p.fk_poa_prev, p.fk_area, p.label, p.date_process, p.amount, p.term, p.fk_poa_pac, p.ref_pac, p.statut, ";
$sql.= " a.ref AS refarea, a.label AS labelarea, ";
$sql.= " pr.label AS labelpre, pr.nro_preventive, pr.rowid AS idprev, ";
$sql.= " tc.label AS labelcon, ";
$sql.= " ta.label AS labeladj ";

$sql.= " FROM ".MAIN_DB_PREFIX."poa_process AS p ";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_area AS a ON p.fk_area = a.rowid ";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."poa_prev AS pr ON p.fk_poa_prev = pr.rowid ";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_tables AS tc ON p.fk_type_con = tc.rowid ";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_tables AS ta ON p.fk_type_adj = ta.rowid ";

$sql.= " WHERE p.entity = ".$conf->entity;
$sql.= " AND p.gestion = ".$gestion;
if (!$user->admin)
  {
    $sql.= " AND pr.fk_user_create = ".$user->id;
  }
if ($sref)
{
    $sql.= " AND p.ref like '%".$sref."%'";
}
if ($sall)
{
    $sql.= " AND (p.ref like '%".$sall."%' OR p.label like '%".$sall."%' OR p.active like '%".$sall."%')";
}

$sql.= " ORDER BY $sortfield $sortorder";
$sql.= $db->plimit($limit+1, $offset);

$result = $db->query($sql);
if ($result)
  {
    $num = $db->num_rows($result);
    $i = 0;
    $help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
    llxHeader("",$langs->trans("Liste process"),$help_url);
    
    print_barre_liste($langs->trans("Liste process"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);
    
    print '<table class="noborder" width="100%">';
    
    print "<tr class=\"liste_titre\">";
    print_liste_field_titre($langs->trans("Ref"),"liste.php", "p.ref","","","");
    print_liste_field_titre($langs->trans("Year"),"liste.php", "p.gestion","","","");
    print_liste_field_titre($langs->trans("Num. prev"),"liste.php", "pr.nro_preventive","","","");
    print_liste_field_titre($langs->trans("Title"),"liste.php", "p.label","","","");
    print_liste_field_titre($langs->trans("Area"),"liste.php", "a.ref","","","");
    print_liste_field_titre($langs->trans("Date"),"liste.php", "p.date_process","","","");
    print_liste_field_titre($langs->trans("Amount"),"liste.php", "p.amount","","","");
    print_liste_field_titre($langs->trans("Modality"),"liste.php", "tc.label","","","");
    print_liste_field_titre($langs->trans("Typeadjudication"),"liste.php", "ta.label","","","");
    print_liste_field_titre($langs->trans("Status"),"liste.php", "p.statut","","","");
    print_liste_field_titre($langs->trans("Action"),"", "","","","");
    print "</tr>\n";
    
    if ($num) {
      $var=True;
      while ($i < min($num,$limit))
	{
	  $obj = $db->fetch_object($result);
	  $var=!$var;
	  print "<tr $bc[$var]>";
	  print '<td><a href="fiche.php?id='.$obj->id.'" title="'.$obj->label.'">'.img_picto($langs->trans("Process"),DOL_URL_ROOT.'/poa/img/process.png','',1).' '.$obj->ref.'</a></td>';
	  print '<td>'.$obj->gestion.'</td>';

	  print '<td><a href="'.DOL_URL_ROOT.'/poa/execution/fiche.php?id='.$obj->idprev.'" title="'.$obj->labelpre.'">'.img_picto($langs->trans("Preventive"),DOL_URL_ROOT.'/poa/img/prev.png','',1).' '.$obj->nro_preventive.'</a></td>';

	  print '<td style="min-width:300px;">'.(strlen($obj->label)>40?substr($obj->label,0,40).'....':$obj->label).'</td>';
	  print '<td>'.$obj->refarea.'</td>';
	  print '<td>'.dol_print_date($obj->date_process,'day').'</td>';
	  print '<td align="right">'.price(price2num($obj->amount,'MT')).'</td>';
	  print '<td>'.$obj->labelcon.'</td>';
	  print '<td>'.$obj->labeladj.'</td>';

	  print '<td nowrap>'.$object->LibStatut($obj->statut,2).'</td>';
	  print '<td nowrap>';
	  // print '<a href="fiche.php?id='.$obj->id.'">'.img_picto($langs->trans("Edit"),'edit').'</a>';
	  print '&nbsp;';
	  print '<a href="fiche_pas1.php?id='.$obj->id.'">'.img_picto($langs->trans("Contract"),DOL_URL_ROOT.'/poa/img/comp.png','',1).'</a>';
	  print '&nbsp;';
	  print '<a href="fiche_pas2.php?id='.$obj->id.'">'.img_picto($langs->trans("Payments"),DOL_URL_ROOT.'/poa/img/deve.png','',1).'</a>';
	  print '</td>';
	  
	  print "</tr>\n";
	  $i++;
	}
    }
    
    $db->free($result);
    
    print "</table>";
    print "<div class=\"tabsAction\">\n";
    
    if ($action == '')
      {
	if ($user->rights->poa->area->crear)
	  print "<a class=\"butAction\" href=\"fiche.php?action=create\">".$langs->trans("Createnew")."</a>";
	else
	  print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";
      }
    print '</div>';
  }
 else
   {
     dol_print_error($db);
   }


$db->close();

llxFooter();
?>
