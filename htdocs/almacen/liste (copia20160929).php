<?php
/* Copyright (C) 2001-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
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
 *      \file       htdocs/almacen/liste.php
 *      \ingroup    almacen
 *      \brief      Page liste des solicitudes a almacenes
 */

require("../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/commonobject_.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacen.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacendet.class.php");
require_once DOL_DOCUMENT_ROOT.'/almacen/class/entrepotuseradd.class.php';
require_once(DOL_DOCUMENT_ROOT."/product/stock/class/entrepot.class.php");
require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/fabrication/class/fabrication.class.php");

$langs->load("stocks");
$langs->load("almacen@almacen");

if (!$user->rights->almacen->leerpedido)
	accessforbidden();

$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];

if (isset($_POST['search_ref']))
	$_SESSION['asearch_ref'] = $_POST['search_ref'];
if (isset($_POST['search_entrepotfrom']))
	$_SESSION['asearch_entrepotfrom'] = $_POST['search_entrepotfrom'];
if (isset($_POST['search_entrepotto']))
	$_SESSION['asearch_entrepotto'] = $_POST['search_entrepotto'];
if (isset($_POST['search_statut']) || isset($_GET['search_statut']))
	$_SESSION['asearch_statut'] = GETPOST('search_statut');
if (isset($_POST['reyear']))
{
	$search_datec  = dol_mktime(0, 0, 0, GETPOST('remonth'),  GETPOST('reday'),  GETPOST('reyear'));
	$_SESSION['asearch_datec'] = $search_datec;
	$search_datecfin  = dol_mktime(23, 59, 59, GETPOST('remonth'),  GETPOST('reday'),  GETPOST('reyear'));
	$_SESSION['asearch_dateci'] = $search_datec;
	$_SESSION['asearch_datecf'] = $search_datecfin;
}
if (isset($_POST['deyear']))
{
	$search_dated  = dol_mktime(0, 0, 0, GETPOST('demonth'),  GETPOST('deday'),  GETPOST('deyear'));
	$_SESSION['asearch_dated'] = $search_dated;
	$search_datedfin  = dol_mktime(23, 59, 59, GETPOST('demonth'),  GETPOST('deday'),  GETPOST('deyear'));
	$_SESSION['asearch_datedi'] = $search_dated;
	$_SESSION['asearch_datedf'] = $search_datedfin;
}
if (empty($_SESSION['asearch_statut']))
	$_SESSION['asearch_statut'] = -1;
if (isset($_POST['search_op']))
	$_SESSION['asearch_op'] = $_POST['search_op'];
if (isset($_POST['nosearch_x']) || isset($_GET['nosearch_x']))
{
	$_SESSION["asearch_ref"] = '';
	$_SESSION["asearch_op"] = '';
	$_SESSION["asearch_dateci"] = '';
	$_SESSION["asearch_datecf"] = '';
	$_SESSION["asearch_datedi"] = '';
	$_SESSION["asearch_datedf"] = '';
	$_SESSION["asearch_entrepotfrom"] = '';
	$_SESSION["asearch_entrepotto"] = '';
	$_SESSION["asearch_op"] = '';
	$_SESSION["asearch_statut"] = -1;
}
$search_desc = $_SESSION['asearch_desc'];
$search_dateci = $_SESSION['asearch_dateci'];
$search_datecf = $_SESSION['asearch_datecf'];
$search_datedi = $_SESSION['asearch_datedi'];
$search_datedf = $_SESSION['asearch_datedf'];
$search_ref = $_SESSION['asearch_ref'];
$search_op = $_SESSION['asearch_op'];
$search_statut = $_SESSION['asearch_statut'];
$search_entrepotfrom = $_SESSION['asearch_entrepotfrom'];
$search_entrepotto = $_SESSION['asearch_entrepotto'];

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];

if (! $sortfield) $sortfield="sa.ref";
if (! $sortorder) $sortorder="DESC";

$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;
$aStatut = array(0=>$langs->trans('Draft'),
	1=>$langs->trans('Validated'),
	2=>$langs->trans('Delivered'));

$form = new Form($db);

//filtramos por almacenes designados segun usuario
$objentrepotuser = new Entrepotuseradd($db);
$aFilterent = array();
$filteruser = '';
if (!$user->admin)
{
	$filter = array(1=>1);
	$filterstatic = " AND t.fk_user = ".$user->id;
	$filterstatic.= " AND t.active = 1";
	$res = $objentrepotuser->fetchAll('','',0,0,$filter,'AND',$filterstatic,false);
	//$res = $objentrepot->getlistuser($user->id);
	if ($res > 0)
	{
		$num = count($objentrepotuser->lines);
		$i = 0;
		$line = $objentrepotuser->lines;
		for ($i=0; $i < $num; $i++)
		{
			if (!empty($filteruser))$filteruser.= ',';
			$filteruser.= $line[$i]->fk_entrepot;
			$aFilterent[$line[$i]->fk_entrepot] = $line[$i]->fk_entrepot;
		}
	}
}
$sql  = "SELECT sa.rowid, sa.ref as ref, sa.fk_entrepot, sa.fk_entrepot_from, sa.fk_user_create, ";
if ($conf->fabrication->enabled)
	$sql .= " f.ref as ref_fabrication,  ";
$sql.= " sa.date_creation, sa.date_delivery, sa.description, sa.statut, ";
$sql.= " e.label as entrepotfrom, ";
$sql.= " ee.label as entrepotto ";

$sql.= " FROM ".MAIN_DB_PREFIX."sol_almacen as sa";
if ($conf->fabrication->enabled)
{
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."fabrication as f ";
	$sql.= " ON sa.fk_fabrication = f.rowid ";
}
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."entrepot as e ON sa.fk_entrepot_from = e.rowid ";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."entrepot as ee ON sa.fk_entrepot = ee.rowid ";
$sql.= " WHERE sa.entity = ".$conf->entity;
if ($sref)
	$sql.= " AND sa.ref like '%".$sref."%'";
if ($sall)
	$sql.= " AND (sa.ref like '%".$sall."%' OR sa.date_delivery like '%".$sall."%' OR sa.description like '%".$sall."%')";
if ($search_ref) $sql.=" AND sa.ref LIKE '%".$search_ref."%'";
if ($search_entrepotfrom) $sql.=" AND e.label LIKE '%".$search_entrepotfrom."%'";
if ($search_entrepotto) $sql.=" AND ee.label LIKE '%".$search_entrepotto."%'";
if ($search_dateci) $sql.=" AND sa.date_creation BETWEEN '".$db->idate($search_dateci)."' AND '".$db->idate($search_datecf)."'";
if ($search_datedi) $sql.=" AND sa.date_delivery BETWEEN '".$db->idate($search_datedi)."' AND '".$db->idate($search_datedf)."'";
if ($search_statut <>-1)
	$sql.= " AND sa.statut =".$search_statut;
if ($filteruser)
{
	if ($user->rights->almacen->entregaped)
	{
		$sql.= " AND (sa.fk_entrepot_from IN (".$filteruser.")";
		$sql.= " OR sa.fk_entrepot IN (".$filteruser.") )";
	}
	else
	{
		$sql.= " AND sa.fk_entrepot_from IN (".$filteruser.")";
	}
}

$sql.= " ORDER BY $sortfield $sortorder";
$sql.= $db->plimit($limit+1, $offset);
//echo $sql;
$result = $db->query($sql);


if ($result)
{
	$num = $db->num_rows($result);
	$i = 0;
	$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';
	llxHeader("",$langs->trans("ListStockToApplications"),$help_url);

	print_barre_liste($langs->trans("ListStockToApplications"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

	//armamos el filtro
	print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

	print '<table class="noborder" width="100%">';

	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Ref"),"liste.php", "sa.ref","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Entrepotfrom"),"liste.php", "e.label","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Entrepotto"),"liste.php", "ee.label","","","",$sortfield,$sortorder);
	if ($conf->fabrication->enabled)
		print_liste_field_titre($langs->trans("OrderProduction"),"liste.php", "f.ref","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("dateCreation"),"liste.php", "sa.date_creation","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("dateApplication"),"liste.php", "sa.date_delivery","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Status"),"liste.php", "sa.statut",'','','align="right"',$sortfield,$sortorder);
	print "</tr>\n";

	print "<tr class=\"liste_titre\">";
	print '<td>'.'<input type="text"name="search_ref" size="12" value="'.$search_ref.'">'.'</td>';	
	print '<td>'.'<input type="text"name="search_entrepotfrom" size="12" value="'.$search_entrepotfrom.'">'.'</td>';	
	print '<td>'.'<input type="text"name="search_entrepotto" size="12" value="'.$search_entrepotto.'">'.'</td>';	
	print '<td>'.'<input type="text"name="search_op" size="12" value="'.$search_op.'">'.'</td>';	
	
	print '<td>';
	$form->select_date($search_dateci,'re',0,0,'1',"crea_commande",1,0);
	print '</td>';
	print '<td>';
	$form->select_date($search_datedi,'de',0,0,'1',"crea_commande",1,0);
	print '</td>';
	print '<td nowrap valign="top" align="right">';
	print $form->selectarray('search_statut',$aStatut,$search_statut,1);
	print '&nbsp;';

	print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
	print '&nbsp;';
	print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Searchclear")).'" name="nosearch" title="'.dol_escape_htmltag($langs->trans("Searchclear")).'">';
	print '</td>';
	print "</tr>\n";

	if ($num)
	{
		$almacen     = new Solalmacen($db);
		$entrepot    = new Entrepot($db);
		$var=True;
		while ($i < min($num,$limit))
		{
			$objp = $db->fetch_object($result);
			$var=!$var;
			print "<tr $bc[$var]>";
			print '<td><a href="fiche.php?id='.$objp->rowid.'">'.img_object($langs->trans("ShowWarehouse"),'stock').' '.$objp->ref.'</a></td>';
			print '<td>'.$objp->entrepotfrom.'</td>';
			print '<td>'.$objp->entrepotto.'</td>';
			if ($conf->fabrication->enabled)
				print '<td>'.$objp->ref_fabrication.'</td>';
			print '<td>'.dol_print_date($db->jdate($objp->date_creation),'day').'</td>';
			print '<td>'.dol_print_date($db->jdate($objp->date_delivery),'day').'</td>';
			print '<td align="right">'.$almacen->LibStatut($objp->statut,'',5).'</td>';
			print "</tr>\n";
			$i++;
		}
	}

	$db->free($result);

	print "</table>";

	print '</from>';

}
else
{
	dol_print_error($db);
}


$db->close();

llxFooter();
?>
