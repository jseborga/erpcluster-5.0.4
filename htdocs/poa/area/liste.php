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
 *      \file       htdocs/mant/charge/liste.php
 *      \ingroup    Mantenimeinto cargos
 *      \brief      Page liste des charges
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/poa/area/class/poaarea.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/lib/poa.lib.php");

$langs->load("poa@poa");

if (!$user->rights->poa->area->leer)
	accessforbidden();

$object = new Poaarea($db);

$id = GETPOST('id');
$action = GETPOST('action');

$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];
$filterf = GETPOST('filterf');
$filter = GETPOST('filter');
$filtro = GETPOST('filtro');

if (empty($_GET['top']))
	$_SESSION['arrayPoa'] = array();
if ($_GET['top'] == 1)
	$_SESSION['filterrowid'] = $_GET['id'];
if ($_GET['top'] >1)
	$_SESSION['filterrowid'] = $_SESSION['filterrowid'].','.$_GET['id'];

// if ($action == 'sub')
//   $filter.= ','.$id;

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="p.ref";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;
// if (empty($filter))
//   $filter = -1;
//filtros
if ($_GET['top'] >= 1)
	$filter = " AND fk_father IN (".$_SESSION['filterrowid'].")" . " OR rowid = ".$id;


if (empty($filter))
	$filter = " AND fk_father = -1";

$sql  = "SELECT p.rowid AS id, p.ref, p.label, p.active, p.fk_father, p.code_actor, p.pos ";
$sql.= " FROM ".MAIN_DB_PREFIX."poa_area as p ";
$sql.= " WHERE p.entity = ".$conf->entity;
$sql.= $filter;

//$sql.= " AND fk_father IN (".$filter.")";
if ($sref)
	$sql.= " AND p.ref like '%".$sref."%'";

if ($sall)
	$sql.= " AND (p.ref like '%".$sall."%' OR p.label like '%".$sall."%' OR p.active like '%".$sall."%')";

//$sql.= " ORDER BY $sortfield $sortorder";
$sql.= " ORDER BY pos";
$sql.= $db->plimit($limit+1, $offset);

$result = $db->query($sql);
if ($result)
{
	$num = $db->num_rows($result);
	$i = 0;
	// $help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
	// llxHeader("",$langs->trans("Liste areas"),$help_url);

	$aArrcss= array('poa/css/style.css','poa/css/title.css','poa/css/styles.css','poa/css/poamenu.css');
	$aArrjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/jquery-1.3.min.js','poa/js/poa.js','poa/js/scriptajax.js');
	$help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
	llxHeader("",$langs->trans("Liste area"),$help_url,'','','',$aArrjs,$aArrcss);

	print_barre_liste($langs->trans("Liste area"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);
	
	print '<table class="noborder" width="100%">';
	
	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Ref"),"liste.php", "p.ref","","","");
	print_liste_field_titre($langs->trans("Label"),"liste.php", "p.label","","","");
	print_liste_field_titre($langs->trans("Upperlevel"),"liste.php", "p.fk_father","","","");
	print_liste_field_titre($langs->trans("Actor"),"liste.php", "p.code_actor","","","");
	print_liste_field_titre($langs->trans("Status"),"liste.php", "p.active","","","");
	print_liste_field_titre($langs->trans("Action"),"", "","","","");
	print "</tr>\n";
	$espacio0 = '';
	$espacio1 = '&nbsp;&nbsp;&nbsp;&nbsp;';
	$espacio2 = $espacio1.$espacio1;
	$espacio3 = $espacio2.$espacio1;
	
	if ($num)
	{
		$var=True;
		while ($i < min($num,$limit))
		{
			$obj = $db->fetch_object($result);
			if ($obj->pos == 0)
				$espacio = $espacio0;
			if ($obj->pos == 1)
				$espacio = $espacio1;
			if ($obj->pos == 2)
				$espacio = $espacio2;
			if ($obj->pos == 3)
				$espacio = $espacio3;
			
			$var=!$var;
			print "<tr $bc[$var]>";
			$filtro = $obj->id;
			$father = $obj->fk_father;
			
			if ($idFather>0)
				$espacio = '&nbsp;&nbsp;&nbsp;&nbsp;';
			print '<td><a href="liste.php?id='.$obj->id.'&action=sub&top=1&filtro='.$filtro.'&father='.$father.'&dol_hide_leftmenu=1">'.$espacio.img_picto($langs->trans("Ref"),'rightarrow').' '.$obj->ref.'</a></td>';
			
			print '<td>'.$obj->label.'</td>';
			$object->fetch($obj->fk_father);
			if ($object->id == $obj->fk_father)
				print '<td>'.$object->label.'</td>';
			else
				print '<td>&nbsp;</td>';
			print '<td>';
			print select_actors($obj->code_actor,'code_actor','',0,1);
			print '</td>';
			print '<td>'.$obj->active.'</td>';
			print '<td><a href="fiche.php?id='.$obj->id.'&dol_hide_leftmenu=1">'.img_picto($langs->trans("Edit"),'edit').'</a></td>';
			
			print "</tr>\n";
			
			
			$i++;
		}
	}
	
	$db->free($result);
	
	print "</table>";
	print "<div class=\"tabsAction\">\n";
	print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/poa/liste.php?dol_hide_leftmenu=1">'.$langs->trans("Return").'</a>';
	
	if ($action == '')
	{
		if ($user->rights->poa->area->crear)
			print "<a class=\"butAction\" href=\"fiche.php?action=create&dol_hide_leftmenu=1\">".$langs->trans("Createnew")."</a>";
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
