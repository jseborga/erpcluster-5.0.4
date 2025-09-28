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
require_once(DOL_DOCUMENT_ROOT."/poa/workflow/class/poaworkflow.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/workflow/class/poaworkflowdet.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/execution/class/poaprev.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/area/class/poaarea.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/area/class/poaareauser.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/lib/poa.lib.php");

$langs->load("other");
$langs->load("poa@poa");

if (!$user->rights->poa->work->seg)
  accessforbidden();

$object = new Poaworkflow($db);
$objarea = new Poaarea($db);
$objareau= new Poaareauser($db);

//verificando a que areas pertenece el usuario
$aArea = $objareau->getuserarea($user->id);
$aUserArea     = array();
$aUserAreaId   = array();
$aAreaUserId   = array();
$aUserAreaSel  = array();
$aUserAreaPerm = array();
foreach ((array) $aArea AS $j => $objArea)
{
  $aUserArea[$objArea->ref] = $objArea->ref;
  $aUserAreaId[$objArea->id] = $objArea->id;
  $aUserAreaPerm[$objArea->ref] = $objArea->privilege;
  $aUserAreaSel[$objArea->rowid] = $objArea->label;
} 

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
if (empty($gestion)) $gestion = date('Y');

if (! $sortfield) $sortfield="a.date_tracking";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;
if (empty($filter))
  $filter = -1;

$sql = " SELECT p.rowid AS id, p.fk_poa_prev, p.contrat, p.date_workflow,";
$sql.= " p.doclink, p.statut, ";
$sql.= " a.code_area_last, a.code_area_next, a.code_procedure, a.date_tracking, a.detail, a.sequen, a.statut AS statutdet, ";
$sql.= " b.nro_preventive, b.date_preventive, b.label, b.amount ";
$sql.= " FROM ".MAIN_DB_PREFIX."poa_workflow AS p ";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_workflow_det AS a ON a.fk_poa_workflow = p.rowid ";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_prev AS b ON p.fk_poa_prev = b.rowid ";

$sql.= " WHERE b.entity = ".$conf->entity;
$sql.= " AND a.statut = 1";
$sql.= " AND b.gestion = ".$gestion;

if (!$user->admin)
  {
    $aArea = $objareau->getuserarea($user->id);
    $aUserArea   = array();
    $aUserAreaId = array();
    $aUserAreaSel = array();
    foreach ((array) $aArea AS $j => $objArea)
      {
	$aUserArea[$objArea->ref] = $objArea->ref;
	$aUserAreaId[$objArea->rowid] = $objArea->rowid;
	$aUserAreaSel[$objArea->rowid] = $objArea->label;
      } 
    $filterarea = implode(',',$aUserArea);
    $sql.= " AND a.code_area_next IN ('".$filterarea."')";
  }

$sql.= " ORDER BY $sortfield $sortorder";
$sql.= $db->plimit($limit+1, $offset);

$result = $db->query($sql);
if ($result)
  {
    $num = $db->num_rows($result);
    $i = 0;
    $help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
    llxHeader("",$langs->trans("Liste workflow"),$help_url);
    
    print_barre_liste($langs->trans("Liste workflow"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);
    if ($num > 0)
      {
	
	print '<table class="noborder" width="100%">';
	    
	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Shipping date"),"liste.php", "a.date_tracking","","","");
	print_liste_field_titre($langs->trans("Number"),"liste.php", "p.gestion","","",'align="center"');
	print_liste_field_titre($langs->trans("Title"),"liste.php", "pr.nro_preventive","","","");
	print_liste_field_titre($langs->trans("Amount"),"liste.php", "p.amount","","","");
	print_liste_field_titre($langs->trans("Ofarea"),"liste.php", "p.label","","",'align="center"');
	print_liste_field_titre($langs->trans("Procedure"),"liste.php", "a.ref","","","");
	print_liste_field_titre($langs->trans("Detail"),"liste.php", "p.date_process","","","");
	print "</tr>\n";
	
	if ($num)
	  {
	    $var=True;
	    while ($i < min($num,$limit))
	      {
		$obj = $db->fetch_object($result);
		$var=!$var;
		print "<tr $bc[$var]>";
		print '<td><a href="fiche.php?id='.$obj->id.($aUserAreaPerm[$obj->code_area_next]!=1?'&action=createtransf':'').'" title="'.$obj->detail.'">'.img_picto($langs->trans("Workflow"),DOL_URL_ROOT.'/poa/img/workf.png','',1).' '.dol_print_date($obj->date_tracking,'day').'</a></td>';
		print '<td align="center">'.$obj->nro_preventive.'</td>';
		print '<td>'.$obj->label.'</td>';
		print '<td align="right">'.price(price2num($obj->amount,'MT')).'</td>';
		print '<td align="center">'.$obj->code_area_last.'</td>';
		print '<td>';
		print select_typeprocedure($obj->code_procedure,'code_procedure','',0,1,'code');
		print '</td>';
		print '<td>'.$obj->detail.'</td>';
		print "</tr>\n";
		$i++;
	      }
	  }
      }
    else
      {
	print '<span>';
	if (!$user->admin)
	  print $langs->trans('Congratulations').', ';
	print $langs->trans('There is no pending records');
	print '</span>';
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
