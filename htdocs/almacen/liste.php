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
require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacenext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacendet.class.php");
require_once DOL_DOCUMENT_ROOT.'/almacen/class/entrepotuserext.class.php';
require_once(DOL_DOCUMENT_ROOT."/product/stock/class/entrepot.class.php");
require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");
//require_once(DOL_DOCUMENT_ROOT."/fabrication/class/fabrication.class.php");
require_once DOL_DOCUMENT_ROOT.'/almacen/lib/almacen.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

if ($conf->purchase->enabled)
	require_once(DOL_DOCUMENT_ROOT."/purchase/class/purchaserequestext.class.php");
if ($conf->orgman->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentuserext.class.php';
}
$langs->load("stocks");
$langs->load("almacen");

if (!$user->rights->almacen->pedido->read) accessforbidden();

if ($conf->global->ALMACEN_FILTER_YEAR && !isset($_SESSION['period_year']))
{
	header('Location: '.DOL_URL_ROOT.'/almacen/index.php');
	exit;
}
$typemethod = $conf->global->ALMACEN_METHOD_VALUATION_INVENTORY;
//verificamos el periodo
verif_year();

$year = $_SESSION['period_year'];

$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];

if (isset($_POST['search_ref']))
	$_SESSION['asearch_ref'] = $_POST['search_ref'];
if (isset($_POST['search_ref_prod']))
	$_SESSION['asearch_ref_prod'] = $_POST['search_ref_prod'];
if (isset($_POST['search_entrepotfrom']))
	$_SESSION['asearch_entrepotfrom'] = $_POST['search_entrepotfrom'];
if (isset($_POST['search_entrepotto']))
	$_SESSION['asearch_entrepotto'] = $_POST['search_entrepotto'];
if (isset($_POST['search_statut']) || isset($_GET['search_statut']))
{
	//if (empty($_SESSION['asearch_statut']) && $user->rights->almacen->pedido->ent)
	//	$_SESSION['asearch_statut'] = 6;
	//else
		$_SESSION['asearch_statut'] = GETPOST('search_statut');
}
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
if (isset($_POST['search_prref']))
	$_SESSION['asearch_prref'] = $_POST['search_prref'];
//if (empty($_SESSION['asearch_statut']))
//	$_SESSION['asearch_statut'] = -1;
if (isset($_POST['search_op']))
	$_SESSION['asearch_op'] = $_POST['search_op'];
if (isset($_POST['search_description']))
	$_SESSION['asearch_description'] = $_POST['search_description'];
if (isset($_POST['nosearch_x']) || isset($_GET['nosearch_x']))
{
	$_SESSION["asearch_ref"] = '';
	$_SESSION["asearch_ref_prod"] = '';
	$_SESSION["asearch_op"] = '';
	$_SESSION["asearch_dateci"] = '';
	$_SESSION["asearch_datecf"] = '';
	$_SESSION["asearch_datedi"] = '';
	$_SESSION["asearch_datedf"] = '';
	$_SESSION["asearch_entrepotfrom"] = '';
	$_SESSION["asearch_entrepotto"] = '';
	$_SESSION["asearch_prref"] = '';
	$_SESSION["asearch_op"] = '';
	$_SESSION["asearch_description"] = '';
	$_SESSION["asearch_statut"] = -3;
}
$search_desc = $_SESSION['asearch_desc'];
$search_dateci = $_SESSION['asearch_dateci'];
$search_datecf = $_SESSION['asearch_datecf'];
$search_datedi = $_SESSION['asearch_datedi'];
$search_datedf = $_SESSION['asearch_datedf'];
$search_ref = $_SESSION['asearch_ref'];
$search_ref_prod = $_SESSION['asearch_ref_prod'];
$search_prref = $_SESSION['asearch_prref'];
$search_op = $_SESSION['asearch_op'];
$search_description = $_SESSION['asearch_description'];
$search_statut = $_SESSION['asearch_statut']+0;
$search_entrepotfrom = $_SESSION['asearch_entrepotfrom'];
$search_entrepotto = $_SESSION['asearch_entrepotto'];

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];

if (! $sortfield) $sortfield="sa.date_creation";
if (! $sortorder) $sortorder="DESC";

$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;
$aStatut = array(-2=>$langs->trans('Rejected'),
	-1=>$langs->trans('Annulled'),
	0=>$langs->trans('Draft'),
	6=>$langs->trans('Approved'),
	1=>$langs->trans('Validated'),
	2=>$langs->trans('Delivered'),
	5=>$langs->trans('StatusOrderoutofstock'));

$object = new Solalmacenext($db);
$objuser = new User($db);

$form = new Form($db);

//filtramos por $obj designados segun usuario
$objentrepotuser = new Entrepotuserext($db);
$objDepartament = new Pdepartamentext($db);
$objDepartamentuser = new Pdepartamentuserext($db);

$filteruser = '';
$filterfkuser = '';
$fk_user_resp = '';
$aFilterarea = array();
$aFilterent = array();
$aFilterentsol = array();
$filterusersol = '';
$now = dol_now();
if (!$user->admin) list($aFilterent, $filteruser,$aFilterentsol, $filterusersol,$aAreadirect,$fk_areaasign,$filterarea,$aFilterarea, $fk_user_resp,$aExcluded) = verif_accessalm();

if (isset($_POST['search_statut']) || isset($_GET['search_statut']))
{
}
else
{
	if ($user->rights->almacen->pedido->app)
		$search_statut = 1;
	if ($user->rights->almacen->pedido->ent)
		$search_statut = 6;
}

$sql  = "SELECT sa.rowid, sa.ref as ref, sa.fk_entrepot, sa.fk_entrepot_from, sa.fk_user_create, ";
if ($conf->fabrication->enabled)
	$sql .= " f.ref as ref_fabrication,  ";
$sql.= " sa.date_creation, sa.date_delivery, sa.description, sa.statut, ";
$sql.= " e.label as entrepotfrom, ";
$sql.= " ee.label as entrepotto ";
$sql.= ", u.lastname, u.firstname, u.login ";
if ($conf->global->PURCHASE_INTEGRATED_POA)
	$sql.= " ,pr.rowid AS prrowid, pr.ref AS prref ";
$sql.= " FROM ".MAIN_DB_PREFIX."sol_almacen as sa";
if ($conf->fabrication->enabled)
{
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."fabrication as f ";
	$sql.= " ON sa.fk_fabrication = f.rowid ";
}
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."entrepot as e ON sa.fk_entrepot_from = e.rowid ";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."entrepot as ee ON sa.fk_entrepot = ee.rowid ";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."user as u ON sa.fk_user_create = u.rowid ";
if ($conf->global->PURCHASE_INTEGRATED_POA)
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."purchase_request as pr ON pr.originid = sa.rowid AND pr.origin = 'solalmacen'";
$sql.= " WHERE sa.entity = ".$conf->entity;
if ($sref)
	$sql.= " AND sa.ref like '%".$sref."%'";
if ($sall)
	$sql.= " AND (sa.ref like '%".$sall."%' OR sa.date_delivery like '%".$sall."%' OR sa.description like '%".$sall."%')";
if ($search_ref) $sql.=" AND sa.ref LIKE '%".$search_ref."%'";
if ($search_prref) $sql.=" AND pr.ref LIKE '%".$search_prref."%'";
if ($search_description) $sql.=" AND sa.description LIKE '%".$search_description."%'";
if ($search_entrepotfrom) $sql.=" AND e.label LIKE '%".$search_entrepotfrom."%'";
if ($search_entrepotto) $sql.=" AND ee.label LIKE '%".$search_entrepotto."%'";
if ($search_dateci) $sql.=" AND sa.date_creation BETWEEN '".$db->idate($search_dateci)."' AND '".$db->idate($search_datecf)."'";
if ($search_datedi) $sql.=" AND sa.date_delivery BETWEEN '".$db->idate($search_datedi)."' AND '".$db->idate($search_datedf)."'";

if ($search_statut <>-3)
	$sql.= " AND sa.statut =".$search_statut;
if ($search_ref_prod)
	$sql.=" AND f.ref LIKE '%".$search_ref_prod."%'";
if ($search_op)
{
	$sql.=" AND (u.lastname LIKE '%".$search_op."%'";
	$sql.=" OR u.firstname LIKE '%".$search_op."%'";
	$sql.=" OR u.login LIKE '%".$search_op."%'";
	$sql.=")";
}

//fitro segun entrepot y departament

//almaceneros
$sqlfil.="";
if ($filteruser)
{
	$sqlfil.= " sa.fk_entrepot IN (".$filteruser.") ";
}

if ($filterusersol)
{
	if ($sqlfil) $sqlfil.= " OR ";
	$sqlfil.= " ( sa.fk_entrepot IN (".$filterusersol.")";
	$sqlfil.= " AND sa.fk_user_create IN (".$user->id.") )";
}
if ($filterarea)
{
	$idsFilterarea = $filterarea;
	if ($fk_areaasign)
		$idsFilterarea.= ($idsFilterarea?','.$fk_areaasign:$fk_areaasign);
	if (!$user->admin && $user->rights->almacen->pedido->val)
	{
		$aDepartamentval = $objDepartament->verif_accessresp($user->fk_member);
		if (count($aDepartamentval)>0)
			$idsFilterarea.=($idsFilterarea?','.implode(',',$aDepartamentval):implode(',',$aDepartamentval));
	}
	if (!$user->rights->almacen->pedido->appall)
	{
		if (!$user->rights->almacen->pedido->app)
		{
			if ($sqlfil) $sqlfil.= " OR ";
			$sqlfil.= " (sa.fk_departament IN (".$idsFilterarea.")";
			$sqlfil.= " AND sa.fk_user_create IN (".$user->id.") )";
		}
		else
		{
			if ($sqlfil) $sqlfil.= " OR ";
			$idsFilterarea = implode(',',$aFilterarea);
			$sqlfil.= " (sa.fk_departament IN (".$idsFilterarea.")";
			$sqlfil.=")";
		}
	}
}
if (!$user->admin)
{
	if ($user->rights->almacen->pedido->val)
	{
		$aDepartamentval = $objDepartament->verif_accessresp($user->fk_member);
		if (count($aDepartamentval)>0)
			$idsFilterarea.=($idsFilterarea?','.implode(',',$aDepartamentval):implode(',',$aDepartamentval));
		if ($sqlfil) $sqlfil.= " OR ";
		if (!empty($idsFilterarea))
			$sqlfil.= " sa.fk_departament IN (".$idsFilterarea.")";
		else
			$sqlfil.= " sa.fk_departament IN (0)";
	}
	else
	{
		if (!$user->rights->almacen->pedido->appall)
		{
			if (empty($filteruser) && empty($filterusersol) && empty($filterarea))
			{
				if ($sqlfil) $sqlfil.= " OR ";
				$sqlfil.= " sa.fk_user_create = ".$user->id;
			}
		}
	}
}
if (!empty($sqlfil))
	$sql.= " AND (".$sqlfil.")";


//si tiene permiso de validador
/*
//almaceneros
if ($filteruser)
{
	if ($user->rights->almacen->pedido->ent || $user->rights->almacen->pedido->app)
	{
		//$sql.= " AND (sa.fk_entrepot_from IN (".$filteruser.")";
		$sql.= " AND sa.fk_entrepot IN (".$filteruser.") ";
	}
	else
	{
		if (!empty($filteruser))
		{
			//$sql.= " AND sa.fk_entrepot_from IN (".$filteruser.")";
		}
		else
			$sql.= " AND sa.fk_user_create IN (".$user->id.")";
	}
}
if ($filterusersol)
{
	if ($user->rights->almacen->pedido->ent)
	{
		$sql.= " AND (sa.fk_entrepot_from IN (".$filterusersol.")";
		$sql.= " OR sa.fk_entrepot IN (".$filterusersol.") )";
	}
	else
	{
		if (!empty($filterusersol))
		{
			$sql.= " AND sa.fk_entrepot IN (".$filterusersol.")";
			$sql.= " AND sa.fk_user_create IN (".$user->id.")";
		}
		else
			$sql.= " AND sa.fk_user_create IN (".$user->id.")";
	}
}
if ($filterarea)
{
	if (!$user->rights->almacen->pedido->ent)
	{
		$idsFilterarea = $filterarea;
		if ($fk_areaasign)
			$idsFilterarea.= ($idsFilterarea?','.$fk_areaasign:$fk_areaasign);
		if ($user->admin && $user->rights->almacen->pedido->val)
		{
			$aDepartamentval = $objDepartament->verif_accessresp($user->fk_member);
			if (count($aDepartamentval)>0)
				$idsFilterarea.=($idsFilterarea?','.implode(',',$aDepartamentval):implode(',',$aDepartamentval));
		}
		if (!$user->rights->almacen->pedido->appall)
			$sql.= " AND sa.fk_departament IN (".$idsFilterarea.")";
	}
	//if (empty($filteruser) && empty($filterusersol) && !$user->rights->almacen->pedido->app && !$user->admin && !$user->rights->almacen->pedido->val)
	//	$sql.= " AND sa.fk_user_create IN (".$user->id.")";
}
if (!$user->admin)
{
	if ($user->rights->almacen->pedido->val)
	{
		$aDepartamentval = $objDepartament->verif_accessresp($user->fk_member);
		if (count($aDepartamentval)>0)
			$idsFilterarea.=($idsFilterarea?','.implode(',',$aDepartamentval):implode(',',$aDepartamentval));
		if (!empty($idsFilterarea))
			$sql.= " AND sa.fk_departament IN (".$idsFilterarea.")";
		else
			$sql.= " AND sa.fk_departament IN (0)";
	}
	else
	{
		if (!$user->rights->almacen->pedido->appall)
		{
			if (empty($filteruser) && empty($filterusersol) && empty($filterarea))
				$sql.= " AND sa.fk_user_create = ".$user->id;
		}
	}
}
*/
if ($conf->global->ALMACEN_FILTER_YEAR)
{
	$sql.= " AND(year(sa.date_creation) = ".$_SESSION['period_year']." OR sa.statut IN(1,6))";
}

$sql.= " ORDER BY $sortfield $sortorder";
$sql.= $db->plimit($limit+1, $offset);
//echo $sql;
$result = $db->query($sql);
//echo $sql;
//armamos options para status
$options = '<option value="-3">'.$langs->trans('All').'</option>';
foreach ($aStatut AS $j => $value)
{
	$selected = '';
	if ($search_statut == $j) $selected = ' selected="selected"';
	$options.= '<option value="'.$j.'" '.$selected.'>'.$value.'</option>';
}
$param='';
if (! empty($contextpage) && $contextpage != $_SERVER["PHP_SELF"]) $param.='&contextpage='.$contextpage;
if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;
if ($search_statut != '') $param.= '&amp;search_statut='.urlencode($search_statut);
if ($search_ref != '') $param.= '&amp;search_ref='.urlencode($search_ref);
if ($search_entrepotto != '') $param.= '&amp;search_entrepotto='.urlencode($search_entrepotto);
if ($search_op != '') $param.= '&amp;search_op='.urlencode($search_op);
if ($search_prref != '') $param.= '&amp;search_prref='.urlencode($search_prref);

$params = $param;
if ($result)
{
	if ($conf->global->PURCHASE_INTEGRATED_POA)
		$objpurchaserequest = new Purchaserequestext($db);

	$num = $db->num_rows($result);
	$i = 0;
	$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';
	llxHeader("",$langs->trans("ListStockToApplications"),$help_url);

	print_barre_liste($langs->trans("ListStockToApplications"), $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder,'',$num);

	//armamos el filtro
	print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

	print '<div style="min-width:450px;overflow-x: auto; white-space: nowrap;">';

	print '<table class="noborder" width="100%">';

	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Ref"),"liste.php", "sa.ref","",$params,"",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Entrepotfrom"),"liste.php", "e.label","",$params,"",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Entrepotto"),"liste.php", "ee.label","",$params,"",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Author"),"liste.php", "u.lastname","",$params,"",$sortfield,$sortorder);
	if ($conf->fabrication->enabled)
		print_liste_field_titre($langs->trans("OrderProduction"),"liste.php", "f.ref","",$params,"",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("dateCreation"),"liste.php", "sa.date_creation","",$params,'nowrap width="180px"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("dateApplication"),"liste.php", "sa.date_delivery","",$params,'nowrap width="180px"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Description"),"liste.php", "sa.description","",$params,'nowrap width="180px"',$sortfield,$sortorder);
	if ($conf->global->PURCHASE_INTEGRATED_POA)
		print_liste_field_titre($langs->trans("Purchaserequest"),"liste.php", "pr.ref","",$params,"",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Status"),"liste.php", "sa.statut",'',$params,'align="right"',$sortfield,$sortorder);
	print "</tr>\n";

	print "<tr class=\"liste_titre\">";
	print '<td>'.'<input type="text"name="search_ref" size="8" value="'.$search_ref.'">'.'</td>';
	print '<td>'.'<input type="text"name="search_entrepotfrom" size="8" value="'.$search_entrepotfrom.'">'.'</td>';
	print '<td>'.'<input type="text"name="search_entrepotto" size="8" value="'.$search_entrepotto.'">'.'</td>';
	print '<td>'.'<input type="text"name="search_op" size="8" value="'.$search_op.'">'.'</td>';
	if ($conf->fabrication->enabled)
		print '<td>'.'<input type="text"name="search_ref_prod" size="8" value="'.$search_ref_prod.'">'.'</td>';
	print '<td>';
	$form->select_date($search_dateci,'re',0,0,'1',"crea_commande",1,0);
	print '</td>';
	print '<td>';
	$form->select_date($search_datedi,'de',0,0,'1',"crea_commande",1,0);
	print '</td>';
	print '<td>';
	print '<input type="text"name="search_description" size="5" value="'.$search_description.'">';
	print '</td>';
	if ($conf->global->PURCHASE_INTEGRATED_POA)
		print '<td>'.'<input type="text"name="search_prref" size="5" value="'.$search_prref.'">'.'</td>';
	print '<td nowrap valign="top" align="right">';
	print '<select name="search_statut">'.$options.'</select>';
	//print $form->selectarray('search_statut',$aStatut,$search_statut,1);
	print '&nbsp;';

	print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
	print '&nbsp;';
	print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Searchclear")).'" name="nosearch" title="'.dol_escape_htmltag($langs->trans("Searchclear")).'">';
	print '</td>';
	print "</tr>";

	if ($num)
	{
		//$object     = new Solalmacen($db);
		$entrepot    = new Entrepot($db);
		$var=True;
		while ($i < min($num,$limit))
		{
			$objp = $db->fetch_object($result);
			$object->id = $objp->rowid;
			$object->ref = $objp->ref;
			$object->statut = $objp->statut;

			$objuser->fetch($objp->fk_user_create);
			$var=!$var;
			print "<tr $bc[$var]>";
			print '<td>';
			print $object->getNomUrl(1);
			print '</td>';
			//<a href="fiche.php?id='.$objp->rowid.'">'.img_object($langs->trans("ShowWarehouse"),'stock').' '.$objp->ref.'</a></td>';
			print '<td>'.$objp->entrepotfrom.'</td>';
			print '<td>'.$objp->entrepotto.'</td>';
			print '<td>'.$objuser->getNomUrl(1).'</td>';
			if ($conf->fabrication->enabled)
				print '<td>'.$objp->ref_fabrication.'</td>';
			print '<td>'.dol_print_date($db->jdate($objp->date_creation),'day').'</td>';
			print '<td>'.dol_print_date($db->jdate($objp->date_delivery),'day').'</td>';
			print '<td>'.'<a href="#" title="'.$objp->description.'">'.dol_trunc($objp->description,20).'</a></td>';
			if ($conf->global->PURCHASE_INTEGRATED_POA)
			{
				$objpurchaserequest->id = $objp->prrowid;
				$objpurchaserequest->ref = $objp->prref;
				print '<td>'.$objpurchaserequest->getNomUrl(1).'</td>';
			}
			print '<td align="right">'.$object->getLibStatut(5).'</td>';
			print "</tr>\n";
			$i++;
		}
	}

	$db->free($result);

	print "</table>";
	print '</div>';
	print '</from>';

}
else
{
	dol_print_error($db);
}


$db->close();

llxFooter();
?>
