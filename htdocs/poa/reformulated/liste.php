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
require_once(DOL_DOCUMENT_ROOT."/poa/reformulated/class/poareformulated.class.php");
require_once(DOL_DOCUMENT_ROOT."/poa/lib/poa.lib.php");

$langs->load("poa@poa");

if (!$user->rights->poa->refo->leer)
  accessforbidden();

$object = new Poareformulated($db);

$id = GETPOST('id');
$action = GETPOST('action');
if (empty($_SESSION['gestion']))
  $_SESSION['gestion'] = date('Y');
$gestion = $_SESSION['gestion'];

//filtro de acuerdo al area de trabajo
$_SESSION['idsArea'] = filter_area_user($user->id);
$idsArea = $_SESSION['idsArea'];

$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];
$filter = GETPOST('filter');
if ($action == 'sub')
  $filter.= ','.$id;
$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="p.ref";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;
if (empty($filter))
  $filter = -1;

$sql = "SELECT p.rowid AS id, p.gestion, p.ref, p.date_reform, p.statut, p.active ";
$sql.= " FROM ".MAIN_DB_PREFIX."poa_reformulated AS p ";

$sql.= " WHERE p.entity = ".$conf->entity;
$sql.= " AND p.gestion = ".$gestion;

// if ($idsArea)
//   $sql.= " AND p.fk_area IN ($idsArea)";
if ($_SESSION['sel_area'])
  $sql.= " AND p.fk_area = ".$_SESSION['sel_area'];

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

    $aArrcss= array('poa/css/style.css','poa/css/title.css','poa/css/styles.css','poa/css/poamenu.css');
    $aArrjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/poa.js','poa/js/scriptajax.js');
    $help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
    llxHeader("",$langs->trans("Liste reformulated"),$help_url,'','','',$aArrjs,$aArrcss); 

    print_barre_liste($langs->trans("Liste reformulated"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);
    
    print '<table class="noborder" width="100%">';
    
    print "<tr class=\"liste_titre\">";
    print_liste_field_titre($langs->trans("Ref"),"liste.php", "p.ref","","","");
    print_liste_field_titre($langs->trans("Gestion"),"liste.php", "p.gestion","","","");
    print_liste_field_titre($langs->trans("Date"),"liste.php", "p.date_reform","","","");
    print_liste_field_titre($langs->trans("Status"),"liste.php", "p.active","","","");
    print_liste_field_titre($langs->trans("Action"),"", "","","","");
    print "</tr>\n";
    
    if ($num) {
      $var=True;
      while ($i < min($num,$limit))
	{
	  $obj = $db->fetch_object($result);
	  $var=!$var;
	  print "<tr $bc[$var]>";
	  print '<td><a href="fiche.php?id='.$obj->id.'&dol_hide_leftmenu=1" title="'.$obj->ref.'">'.img_picto($langs->trans("Reformulated"),DOL_URL_ROOT.'/poa/img/reform.png','',1).' '.$obj->ref.'</a></td>';
	  print '<td>'.$obj->gestion.'</td>';
	  print '<td>'.dol_print_date($obj->date_reform,'day').'</td>';
	  print '<td>'.$object->LibStatut($obj->statut,2).'</td>';
	  print "</tr>\n";
	  $i++;
	}
    }
    
    $db->free($result);
    
    print "</table>";
    print "<div class=\"tabsAction\">\n";
    
    if ($action == '')
      {
	if ($user->rights->poa->refo->crear)
	  print '<a class="butAction" href="fiche.php?action=create&dol_hide_leftmenu=1">'.$langs->trans("Createnew").'</a>';
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
