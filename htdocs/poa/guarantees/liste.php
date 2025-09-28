<?php
/* Copyright (C) 2014-2015 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *      \file       htdocs/poa/guarantees/liste.php
 *      \ingroup    Guarantees
 *      \brief      Page liste des guarantees
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/poa/guarantees/class/poaguarantees.class.php");
require_once(DOL_DOCUMENT_ROOT."/contrat/class/contrat.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/lib/poa.lib.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';

$langs->load("poa@poa");

if (!$user->rights->poa->guar->leer)
  accessforbidden();

$object = new Poaguarantees($db);
$objcon = new Contrat($db);
$extrafields = new ExtraFields($db);

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

if (! $sortfield) $sortfield="t.ref";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;
if (empty($filter))
  $filter = -1;
$sql = "SELECT";
$sql.= " t.rowid AS id,";
$sql.= " t.fk_contrat,";
$sql.= " t.code_guarantee,";
$sql.= " t.date_ini,";
$sql.= " t.date_fin,";
$sql.= " t.ref,";
$sql.= " t.issuer,";
$sql.= " t.concept,";
$sql.= " t.amount,";
$sql.= " t.fk_user_create,";
$sql.= " t.date_create,";
$sql.= " t.tms,";
$sql.= " t.statut";
$sql.= " FROM ".MAIN_DB_PREFIX."poa_guarantees as t";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_process_contrat AS p ON t.fk_contrat = p.fk_contrat";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_process AS pp ON p.fk_poa_process = pp.rowid";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_prev AS pr ON pp.fk_poa_prev = pr.rowid";

$sql.= " WHERE pr.entity = ".$conf->entity;

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
$sql.= " GROUP BY t.ref,t.code_guarantee, t.issuer, t.concept, t.date_ini, t.date_fin, t.amount ";
$sql.= " ORDER BY $sortfield $sortorder";
$sql.= $db->plimit($limit+1, $offset);

$result = $db->query($sql);
if ($result)
{
    $num = $db->num_rows($result);
    $i = 0;
    $help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
    llxHeader("",$langs->trans("Liste guarantees"),$help_url);
    
    print_barre_liste($langs->trans("Liste guarantees"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);
    
    print '<table class="noborder" width="100%">';
    
    print "<tr class=\"liste_titre\">";
    print_liste_field_titre($langs->trans("Ref"),"liste.php", "p.ref","","","");
    print_liste_field_titre($langs->trans("Type"),"liste.php", "t.code_guarantee","","","");
    print_liste_field_titre($langs->trans("Issuer"),"liste.php", "t.issuer","","","");
    print_liste_field_titre($langs->trans("Concept"),"liste.php", "t.concept","","","");
    print_liste_field_titre($langs->trans("Dateini"),"liste.php", "t.date_ini","","","");
    print_liste_field_titre($langs->trans("Datefin"),"liste.php", "t.date_fin","","","");
    print_liste_field_titre($langs->trans("Contrat"),"", "","","","");
    print_liste_field_titre($langs->trans("Amount"),"liste.php", "t.amount","","","");
    print_liste_field_titre($langs->trans("Status"),"liste.php", "t.statut","","","");
    print "</tr>\n";
    $var = true;
    if ($num) {
    	$var=True;
      	while ($i < min($num,$limit))
		{
	  		$obj = $db->fetch_object($result);
	  		$var=!$var;
	  		//buscamos el contrato
	  		$res = $objcon->fetch($obj->fk_contrat);
	  		print "<tr $bc[$var]>";
		  	print '<td><a href="fiche.php?id='.$obj->id.'" title="'.$obj->ref.'">'.img_picto($langs->trans("Guarantee"),DOL_URL_ROOT.'/poa/img/guarantee.png','',1).' '.$obj->ref.'</a></td>';
	  		print '<td>'.select_code_guarantees($obj->code_guarantee,'code_guarantee','',0,1,'code').'</td>';
	  		print '<td>'.$obj->issuer.'</td>';
	  		print '<td>'.$obj->concept.'</td>';
	  		print '<td>'.dol_print_date($obj->date_ini,'day').'</td>';
	  		print '<td>'.dol_print_date($obj->date_fin,'day').'</td>';
	  		if ($res > 0 && $objcon->id == $obj->fk_contrat)
	  			print '<td>'.$objcon->array_options['options_ref_contrato'].'</td>';
	  		else 
	  			print '<td>&nbsp;</td>';
	  		
	  		print '<td align="right">'.price(price2num($obj->amount,'MT')).'</td>';
	  		print '<td nowrap>'.$object->LibStatut($obj->statut,2).'</td>';
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
