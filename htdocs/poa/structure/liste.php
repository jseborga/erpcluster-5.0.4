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
 *      \file       htdocs/poa/poa/liste.php
 *      \ingroup    Plan Operativo Anual
 *      \brief      Page liste des poa
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/poa/class/poastructureext.class.php");

$langs->load("poa@poa");

if (!$user->rights->poa->poa->leer)
	accessforbidden();

$object = new Poastructureext($db);

$id = GETPOST('id');
$action = GETPOST('action');

$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];
$filter = GETPOST('filter');
$filterf = GETPOST('filterf');
$filtro  = GETPOST('filtro');

if ($action == 'sub')
{
    // if ($filter == -1)
    //   {
    // 	$filterf = $id;
    // 	$filter = '';
    //   }
    // if (!empty($filter)) $filter .= ',';
    // $filter.= $id;
}
$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="p.ref";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;
if (empty($_GET['top']))
	$_SESSION['arrayPoa'] = array();
if ($_GET['top'] == 1)
	$_SESSION['filterrowid'] = $_GET['id'];
// if (isset($_GET['id']))
//   {
//     $filterrowid = $_SESSION['filterrowid'];
//     if (!empty($filterrowid)) $filterrowid .= ',';
//     $filterrowid .= $_GET['id'];
//     $_SESSION['filterrowid'] = $filterrowid;
//   }
if (empty($_SESSION['period_year']))
	$_SESSION['period_year'] = date('Y');
$period_year = $_SESSION['period_year'];
//filtros
if ($_GET['top'] >= 1)
	$filter = " AND rowid IN (".$_SESSION['filterrowid'].")";
if (empty($filter))
	$filter = " AND fk_father = -1";

$sql  = "SELECT p.rowid AS id, p.ref, p.label, p.status, p.fk_father, p.sigla, p.pos, ";
$sql.= " p.version ";
$sql.= " FROM ".MAIN_DB_PREFIX."poa_structure as p ";
$sql.= " WHERE p.entity = ".$conf->entity;
$sql.= " AND p.period_year = ".$period_year;
if ($_SESSION['sel_area'])
	$sql.= " AND p.fk_area = ".$_SESSION['sel_area'];

$sql.= $filter;

// if (!empty($filtro))
//   {
//    $sql.=" AND fk_father IN (-1) ";
//  }
// if (!empty($filterf))
//   $sql.= " AND rowid IN(".$filterf .") OR fk_father IN (".$filter.") ";
// else
//   $sql.= " AND fk_father IN (".$filter.")";
$id = GETPOST('id');

if ($sref)
{
	$sql.= " AND p.ref like '%".$sref."%'";
}
if ($sall)
{
	$sql.= " AND (p.ref like '%".$sall."%' OR p.label like '%".$sall."%' OR p.active like '%".$sall."%')";
}
//$sql.= " ORDER BY $sortfield $sortorder";
$sql.= " ORDER BY p.sigla, p.pos";
$sql.= $db->plimit($limit+1, $offset);
$result = $db->query($sql);
if ($result)
{
  // if (isset($_GET['father']))
  //   {
	if ($_GET['top'] == 1)
	{
		$array = array();
		$_SESSION['arrayPoa'] = array();
		$array[1] = $_GET['father'];
		$array[2] = $_GET['id'];

	}
	elseif($_GET['top'] == 2)
	{
		$array = $_SESSION['arrayPoa'];
		$array[3] = $_GET['id'];
	}
	elseif($_GET['top'] == 3)
	{
		$array = $_SESSION['arrayPoa'];
		$array[4] = $_GET['id'];
	}
      //      $array[$id] = 0;

	$_SESSION['arrayPoa'] = $array;
    // }
	$num = $db->num_rows($result);
	$i = 0;
  // $help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
  // llxHeader("",$langs->trans("Liste structure"),$help_url);

	$aArrcss= array('poa/css/style.css','poa/css/title.css','poa/css/styles.css','poa/css/poamenu.css');
	$aArrjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/jquery-1.3.min.js','poa/js/poa.js','poa/js/scriptajax.js');
	$help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
	llxHeader("",$langs->trans("Liste structure"),$help_url,'','','',$aArrjs,$aArrcss);

	print_barre_liste($langs->trans("Liste structure"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

	print '<table class="noborder" width="100%">';

	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Ref"),"liste.php", "p.ref","","",'width="10%"');
	print_liste_field_titre($langs->trans("Label"),"liste.php", "p.label","","","");
	print_liste_field_titre($langs->trans("Upperlevel"),"liste.php", "p.fk_father","","","");
	print_liste_field_titre($langs->trans("Status"),"liste.php", "p.active","","","");
	print_liste_field_titre($langs->trans("Action"),"", "","","","");
	print "</tr>\n";
	$espacio0 = '';
	$espacio1 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	$espacio2 = $espacio1.$espacio1;
	$espacio3 = $espacio2.$espacio1;

	if ($num) {
		$var=True;
		if (!empty($_GET['top']))
		{
			print "<tr $bc[$var]>";
			print '<td><a href="liste.php">'.img_picto($langs->trans("Ref"),'rightarrow').' '.$langs->trans('All').'</a></td>';

			print '<td>&nbsp;</td>';
			print '<td>&nbsp;</td>';
			print '<td>&nbsp;</td>';
			print '<td>&nbsp;</td>';
			print "</tr>\n";
		}
		while ($i < min($num,$limit))
		{	      
			$obj = $db->fetch_object($result);
			$espacio = $espacio1;

			$var=!$var;
			print "<tr $bc[$var]>";
			$filtro = $obj->id;
			$father = $obj->fk_father;
			print '<td><a href="liste.php?id='.$obj->id.'&action=sub&top=1&filtro='.$filtro.'&father='.$father.'&dol_hide_leftmenu=1">'.$espacio1.img_picto($langs->trans("Ref"),'rightarrow').' '.$obj->ref.'</a></td>';

			print '<td>'.$obj->label.' '.$obj->sigla.'</td>';
			$object->fetch($obj->fk_father);
			if ($object->id == $obj->fk_father)
				print '<td>'.$object->label.'</td>';
			else
				print '<td>&nbsp;</td>';

			print '<td nowrap>'.$object->LibStatut($obj->status).'</td>';
			print '<td><a href="fiche.php?id='.$obj->id.'&dol_hide_leftmenu=1">'.img_picto($langs->trans("Edit"),'edit').'</a></td>';

			print "</tr>\n";

			if ($_GET['top'] >= 1)
			{
				$idProg = $_SESSION['arrayPoa'][2];
				if ($idProg == $obj->id && $obj->fk_father == -1 && $obj->pos == 1)
				{
		//buscamos el proyecto
					$objarray = new Poastructureext($db);
					$objArray = $objarray->getlist($obj->id);
					if (count($objArray) > 0)
					{
						foreach ((array) $objArray AS $j => $objHijo)
						{
							$var=!$var;
							print "<tr $bc[$var]>";
							$filtro = $objHijo->id;

							print '<td><a href="liste.php?id='.$objHijo->id.'&action=sub&top=2&filtro='.$filtro.'&father='.$objHijo->fk_father.'&dol_hide_leftmenu=1">'.$espacio2.img_picto($langs->trans("Ref"),'rightarrow').' '.$objHijo->ref.'</a></td>';

							print '<td>'.$objHijo->label.' '.$objHijo->sigla.'</td>';
							$object->fetch($objHijo->fk_father);
							if ($object->id == $objHijo->fk_father)
							{

								print '<td>'.$object->label.'</td>';
							}
							else
								print '<td>&nbsp;</td>';

			    // //actualizamos sigla
			    // $newsigla = $obj->ref.$objHijo->ref;
			    // $objnewHijo = new Poastructure($db);
			    // $objnewHijo->fetch($objHijo->id);
			    // $objnewHijo->sigla = $newsigla;
			    // $objnewHijo->update($user);
			    // $objHijo->sigla = $newsigla;

							print '<td nowrap>'.$object->LibStatut($objHijo->status).'</td>';
							print '<td><a href="fiche.php?id='.$objHijo->id.'&dol_hide_leftmenu=1">'.img_picto($langs->trans("Edit"),'edit').'</a></td>';
							print "</tr>\n";

							$idProy = $_SESSION['arrayPoa'][3];
							if ($idProy == $objHijo->id && $objHijo->fk_father == $obj->id && $objHijo->pos == 2)
							{
								$objarray2 = new Poastructureext($db);
								$objArray2 = $objarray2->getlist($objHijo->id);

								if (count($objArray2) > 0)
								{
									foreach ((array )$objArray2 AS $k => $objHijo2)
									{

										print "<tr $bc[$var]>";
										$filtro = $objHijo2->id;

										print '<td><a href="liste.php?id='.$objHijo2->id.'&action=sub&top=3&filtro='.$filtro.'&father='.$objHijo2->fk_father.'&dol_hide_leftmenu=1">'.$espacio3.img_picto($langs->trans("Ref"),'rightarrow').' '.$objHijo2->ref.'</a></td>';

										print '<td>'.$objHijo2->label.' rrrr '.$objHijo2->sigla.'</td>';
										$object->fetch($objHijo2->fk_father);
										if ($object->id == $objHijo2->fk_father)
											print '<td>'.$object->label.'</td>';
										else
											print '<td>&nbsp;</td>';

				    //actualizamos sigla
				    // $newsigla = $objHijo->sigla.$objHijo2->ref;
				    // $objnewHijo = new Poastructure($db);
				    // $objnewHijo->fetch($objHijo2->id);
				    // $objnewHijo->sigla = $newsigla;
				    // $objnewHijo->update($user);

										print '<td nowrap>'.$object->LibStatut($objHijo2->status).'</td>';
										print '<td><a href="fiche.php?id='.$objHijo2->id.'">'.img_picto($langs->trans("Edit"),'edit').'</a></td>';

										print "</tr>\n";

									}
								}
							}
						}
					}
				}
			}
			$i++;
		}
	}

	$db->free($result);

	print "</table>";
	print "<div class=\"tabsAction\">\n";

	if ($action == '' || $action == 'menu2' || $action== 'sub')
	{
		print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/poa/liste.php'.'?dol_hide_leftmenu=1">'.$langs->trans("Return").'</a>';
		if ($user->rights->poa->str->crear)
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
