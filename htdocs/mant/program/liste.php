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
 *      \file       htdocs/mant/request/liste.php
 *      \ingroup    Mantenimiento
 *      \brief      Page liste des liste work request
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/mant/class/mantprogramming.class.php");
if ($conf->assets->enabled)
	require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsext.class.php';

require_once DOL_DOCUMENT_ROOT.'/mant/lib/mant.lib.php';


$langs->load("mant");
$langs->load("other");

if (!$user->rights->mant->prog->leer)
	accessforbidden();

$id   = GETPOST('id');
$idot = GETPOST('idot');

$action = GETPOST('action');

if (isset($_GET['ssearch']))
{
	$lnk = DOL_URL_ROOT.'/mant/jobs/fiche.php?id=';
	//opcion de busqueda para
	//asignacion de activos
	if ($_GET['ssearch'] == 3)
		$lnk = DOL_URL_ROOT.'/assets/assignment/fiche.php?id=';
	$_SESSION['ssearch'][$idot]['id'] = $_GET['idot'];
	$_SESSION['ssearch'][$idot]['link'] = $lnk.$_GET['idot'].'&amp;action='.($_GET['ssearch']==1?'editjobs':($_GET['ssearch']==2?'editregjobs':''));
}
$filter         = GETPOST('filter');
$search_code    = GETPOST('search_code');
$search_item    = GETPOST('search_item');
$search_group   = GETPOST('search_group');
$search_descrip = GETPOST('search_descrip');
if (isset($_GET['sda_']) || isset($_POST['sda_']))
	$search_dateini = (dol_mktime(12, 0, 0, GETPOST('sda_month'),GETPOST('sda_day'),GETPOST('sda_year')));

if (isset($_POST['nosearch_x']))
{
	$search_code     = '';
	$search_item     = '';
	$search_group = '';
	$search_descrip   = '';
	$search_dateadq    = '';
}

if ($action == 'sub')
	$filter.= ','.$id;

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];

if (! $sortfield) $sortfield="t.rowid";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;
$filter = '';
$lRegisterUser = true;

if (!empty($id))
	$filter.= " AND t.rowid = ".$id;
if (!empty($search_area))
{
	$filter .= " AND (a.ref_ext LIKE '%".$db->escape($search_area)."%'";
	$filter .= " OR a.label LIKE '%".$db->escape($search_area)."%' ) ";
}
//filtro search
if (!empty($search_code))
	$filter .= " AND t.ref_ext LIKE '%".$db->escape($search_code)."%'";
if (!empty($search_item))
	$filter .= " AND t.item LIKE '%".$db->escape($search_item)."%'";
if (!empty($search_group))
	$filter .= " AND g.label LIKE '%".$db->escape($search_group)."%'";
if (!empty($search_descrip))
	$filter .= " AND t.descrip LIKE '%".$db->escape($search_descrip)."%'";
if (!empty($search_dateini))
{
	$filter .= " AND t.date_ini = '".$db->escape(date('Y-m-d',$search_dateini))."'";

	// $filter .= " AND month(t.date_adq) LIKE '%".$db->escape(date('m',$search_dateadq))."%'";
	// $filter .= " AND day(t.date_adq) LIKE '%".$db->escape(date('d',$search_dateadq))."%'";
	// $filter .= " AND year(t.date_adq) LIKE '%".$db->escape(date('Y',$search_dateadq))."%'";
}
$sql = "SELECT";
$sql.= " t.rowid AS id,";

$sql.= " t.entity,";
$sql.= " t.fk_asset,";
$sql.= " t.fk_equipment,";
$sql.= " t.typemant,";
$sql.= " t.frequency,";
$sql.= " t.detail_value,";
$sql.= " t.description,";
$sql.= " t.date_ini,";
$sql.= " t.date_last,";
$sql.= " t.date_next,";
$sql.= " t.date_create,";
$sql.= " t.fk_user_create,";
$sql.= " t.tms,";
$sql.= " t.statut,";
$sql.= " t.active,";
$sql.= " a.ref AS ref_asset,";
$sql.= " a.ref_ext AS ref_ext_asset,";
$sql.= " a.descrip AS asset,";
$sql.= " tm.label AS typemant,";
$sql.= " f.label AS frequency";
$sql.= " , e.ref AS ref_equipment ";
$sql.= " , e.label AS label_equipment ";

$sql.= " FROM ".MAIN_DB_PREFIX."mant_programming as t ";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."assets AS a ON t.fk_asset = a.rowid ";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."m_equipment AS e ON t.fk_equipment = e.rowid ";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."c_typemant AS tm ON t.typemant = tm.code ";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."c_frequency AS f ON t.frequency = f.code ";

$sql.= " WHERE t.entity = ".$conf->entity;
if ($filter)
	$sql.= $filter;
$sql.= " ORDER BY $sortfield $sortorder";
$sql.= $db->plimit($limit+1, $offset);

$result = $db->query($sql);

$form=new Form($db);

if ($result)
{
	$num = $db->num_rows($result);
	$i = 0;
	$help_url='EN:Module_Mant_En|FR:Module_Mant|ES:M&oacute;dulo_Mant';
	//$aArrjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/jquery-1.3.min.js');
	llxHeader("",$langs->trans("Liste programming"),$help_url,'','','',$aArrjs);

	print_barre_liste($langs->trans("Programming maintenance"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

	print '<form name="fo1" method="POST" id="fo1" action="'.$_SERVER["PHP_SELF"].'">'."\n";
	print '<input type="hidden" name="idot" value="'.$idot.'">';

	print '<table class="noborder" width="100%">';

	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Ref"),"liste.php", "t.rowid","","","");
	//print_liste_field_titre($langs->trans("Asset"),"liste.php", "a.descrip","","","");
	print_liste_field_titre($langs->trans("Equipment"),"liste.php", "a.descrip","","","");
	print_liste_field_titre($langs->trans("Typemant"),"liste.php", "tm.label","","","");
	print_liste_field_titre($langs->trans("Frequency"),"liste.php", "f.label","","","");
	print_liste_field_titre($langs->trans("Description"),"liste.php", "t.description","","","");
	print_liste_field_titre($langs->trans("Dateini"),"liste.php", "t.date_ini","","","");
	print_liste_field_titre($langs->trans("Datelast"),"liste.php", "t.date_last","","","");
	print_liste_field_titre($langs->trans("Datenext"),"liste.php", "t.date_next","","","");
	print_liste_field_titre($langs->trans("Active"),"liste.php", "t.active","","","");
	print_liste_field_titre($langs->trans("Statut"),"", "","","","");
	print "</tr>\n";

	//filtro
	print '<tr class="liste_titre">';
	print '<td>&nbsp;</td>';
	print '<td><input type="text" class="flat" name="search_equipment" value="'.$search_ass.'" size="10"></td>';
	print '<td><input type="text" class="flat" name="search_typem" value="'.$search_typem.'" size="7"></td>';
	print '<td><input type="text" class="flat" name="search_freq" value="'.$search_freq.'" size="15"></td>';
	print '<td><input type="text" class="flat" name="search_descrip" value="'.$search_descrip.'" size="15"></td>';
	print '<td>';
	$form->select_date($db->jdate($search_dateini),'sda_','','',(empty($search_dateini)?1:0),"date",1,0,0);
	print '</td>';
	print '<td>&nbsp;</td>';
	print '<td>&nbsp;</td>';
	print '<td><input type="text" class="flat" name="search_act" value="'.$search_act.'" size="5"></td>';

	print '<td align="right" nowrap>';
	print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
	print '&nbsp;';
	print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Searchclear")).'" name="nosearch" title="'.dol_escape_htmltag($langs->trans("Searchclear")).'">';

	print '</td>';
	print '</tr>';
	if ($num)
	{
		$var=True;
		while ($i < min($num,$limit))
		{
			$obj = $db->fetch_object($result);

			$var=!$var;

			print "<tr $bc[$var]>";
			print '<td nowrap>';
			print '<a href="fiche.php?id='.$obj->id.'">'.img_picto($langs->trans("Programming"),DOL_URL_ROOT.'/mant/img/programming','',1).'</a>';
			print '</td>';
			print '<td>'.$obj->ref_equipment.' '.$obj->label_equipment.'</td>';
			print '<td>'.$obj->typemant.'</td>';
			print '<td>'.$obj->frequency.'</td>';
			print '<td>'.$obj->descrip.'</td>';
			print '<td>'.dol_print_date($db->jdate($obj->date_ini),'day').'</td>';
			print '<td>'.dol_print_date($db->jdate($obj->date_last),'day').'</td>';
			print '<td>'.dol_print_date($db->jdate($obj->date_next),'day').'</td>';
			print '<td>';
			print ($obj->active==1?img_picto($langs->trans('Active'),'switch_on'):img_picto($langs->trans('Active'),'switch_on'));
			print '</td>';

			print "</tr>\n";
			$i++;
		}
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
