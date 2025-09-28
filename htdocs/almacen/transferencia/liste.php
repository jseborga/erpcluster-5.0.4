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

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/commonobject_.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/stockmouvementtemp.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/stockmouvementdocext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/entrepotbanksoc.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/entrepotuserext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/ctypemouvement.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
if (! empty($conf->categorie->enabled))
	require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';

$langs->load("stocks");
$langs->load("product");
$langs->load("almacen@almacen");

if (!$user->rights->almacen->leertransf)
	accessforbidden();

if ($conf->global->ALMACEN_FILTER_YEAR && !isset($_SESSION['period_year']))
{
	header('Location: '.DOL_URL_ROOT.'/almacen/index.php');
	exit;
}


$object = new Stockmouvementtemp($db);
$objectdoc = new Stockmouvementdocext($db);

$formproduct=new FormProduct($db);
$form = new Form($db);
$objentrepot = new Entrepotbanksoc($db);
$objType = new Ctypemouvement($db);

$aType   = array(1=>$langs->trans('De'),
	2=>$langs->trans('A'));
$aStatut = array(0=>$langs->trans('Draft'),
	1=>$langs->trans('Pending'),
	2=>$langs->trans('Accepted'));
//para emavias
$aStatut = array(9=>$langs->trans('Returned'),1=>$langs->trans('RequestOpen'), 2=>$langs->trans('Closed'));

if (isset($_POST['search_reft']))
	$_SESSION['tsearch_reft'] = $_POST['search_reft'];
if (isset($_POST['search_type']))
	$_SESSION['tsearch_type'] = $_POST['search_type'];
if (isset($_POST['search_entrepot']))
	$_SESSION['tsearch_entrepot'] = $_POST['search_entrepot'];
if (isset($_POST['search_statut']) || isset($_GET['search_statut']))
	$_SESSION['tsearch_statut'] = GETPOST('search_statut');
if (isset($_POST['search_cod']))
	$_SESSION['tsearch_cod'] = $_POST['search_cod'];
if (isset($_POST['search_typem']))
	$_SESSION['tsearch_typem'] = $_POST['search_typem'];
if (isset($_POST['search_prod']))
	$_SESSION['tsearch_prod'] = $_POST['search_prod'];
if (isset($_POST['search_desc']))
	$_SESSION['tsearch_desc'] = $_POST['search_desc'];
if (isset($_POST['reyear']))
{
	$search_dateini  = dol_mktime(0, 0, 1, GETPOST('remonth'),  GETPOST('reday'),  GETPOST('reyear'));
	$_SESSION['tsearch_date'] = $search_date;
	$search_datefin  = dol_mktime(23, 59, 59, GETPOST('remonth'),  GETPOST('reday'),  GETPOST('reyear'));
	$_SESSION['tsearch_datei'] = $search_dateini;
	$_SESSION['tsearch_datef'] = $search_datefin;
}
if (isset($_POST['nosearch_x']) || isset($_GET['nosearch_x']))
{
	$_SESSION["tsearch_desc"] = '';
	$_SESSION["tsearch_datei"] = '';
	$_SESSION["tsearch_datef"] = '';
	$_SESSION["tsearch_cod"] = '';
	$_SESSION["tsearch_type"] = '';
	$_SESSION["tsearch_prod"] = '';
	$_SESSION["tsearch_reft"] = '';
	$_SESSION["tsearch_typem"] = '';
	$_SESSION["tsearch_entrepot"] = '';
	$_SESSION["tsearch_statut"] = -1;
}
$search_desc = $_SESSION['tsearch_desc'];
$search_dateini = $_SESSION['tsearch_datei'];
$search_datefin = $_SESSION['tsearch_datef'];
$search_reft = $_SESSION['tsearch_reft'];
$search_typem = $_SESSION['tsearch_typem'];
$search_type = $_SESSION['tsearch_type'];
$search_cod  = $_SESSION['tsearch_cod'];
$search_prod = $_SESSION['tsearch_prod'];
if (empty($search_type))
{
	$_SESSION['tsearch_type'] = 0;
	$search_type = $_SESSION['tsearch_type'];
}
$search_entrepot = $_SESSION['tsearch_entrepot'];
$search_statut = $_SESSION['tsearch_statut'];
//if (empty($search_statut))
//{
//	$_SESSION['tsearch_statut'] = 1;
//	$search_statut = $_SESSION['tsearch_statut'];
//}
$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="sm.ref DESC, e.lieu ";
if (! $sortorder) $sortorder="DESC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;

//listamos las transferencias

$objentrepotuser = new Entrepotuserext($db);
//filtro por usuario
$filteruser = '0';
$aFilterent = array();

if (!$user->admin)
{
	$filterfkuser = $user->id;
	$filter = array(1=>1);
	$filterstatic = " AND t.fk_user = ".$user->id;
	$filterstatic.= " AND t.active = 1";
	$res = $objentrepotuser->fetchAll('','',0,0,$filter,'AND',$filterstatic,false);
	//$res = $objentrepot->getlistuser($user->id);
	if ($res > 0)
	{
		$num = count($objentrepotuser->lines);
		$i = 0;
		$lines = $objentrepotuser->lines;
		foreach ($lines AS $i => $line)
		{
			if ($line->type == 1)
			{
				if (!empty($filteruser))$filteruser.= ',';
				$filteruser.= $line->fk_entrepot;
				$aFilterent[$line->fk_entrepot] = $line->fk_entrepot;
			}
			if ($line->type == 2)
			{
				if (!empty($filterusersol))$filterusersol.= ',';
				$filterusersol.= $line->fk_entrepot;
				$aFilterentsol[$line->fk_entrepot] = $line->fk_entrepot;
			}

		}
	}
}



$sql  = "SELECT sm.rowid, sm.ref AS reft, sm.statut, sm.fk_entrepot, ";
$sql.= " sm.value AS value, sm.quant, sm.price, sm.type_mouvement, sm.datem, sm.statut AS smstatut,";
$sql.= " d.ref AS refdoc, d.statut AS statutdoc, ";
$sql.= " p.rowid AS prowid, p.ref AS ref, p.label AS label, p.description, ";
$sql.= " sm.label AS labelmouvement, ";
$sql.= " t.label AS labeltype, ";
$sql.= " e.lieu ";
$sql.= " FROM ".MAIN_DB_PREFIX."stock_mouvement_temp as sm";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_type_mouvement as t ON sm.fk_type_mov = t.rowid";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."stock_mouvement_doc as d ON sm.ref = d.ref AND sm.entity = d.entity";

$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product as p ";
$sql.= " ON sm.fk_product = p.rowid ";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."entrepot as e ";
$sql.= " ON sm.fk_entrepot = e.rowid ";
$sql.= " WHERE e.entity = ".$conf->entity;
if ($sref)
	$sql.= " AND p.ref like '%".$sref."%'";
if ($sall)
	$sql.= " AND (p.ref like '%".$sall."%' OR sm.datem like '%".$sall."%' OR sm.value like '%".$sall."%' OR sm.type_mouvement like '%".$sall."%')";

if (is_numeric($search_type) && ($search_type > 0))
{
	if ($search_type == 2)
		$sql.=" AND sm.type_mouvement = 0";
	else
		$sql.=" AND sm.type_mouvement = ".$search_type;
}
if ($search_entrepot>0) $sql.=" AND sm.fk_entrepot = ".$search_entrepot;
if ($search_reft) $sql.=" AND sm.ref LIKE '%".$search_reft."%'";
if ($search_dateini) $sql.=" AND sm.datem BETWEEN '".$db->idate($search_dateini)."' AND '".$db->idate($search_datefin)."'";
if ($search_cod) $sql.=" AND p.ref LIKE '%".$search_cod."%'";
if ($search_typem) $sql.=" AND t.label LIKE '%".$search_typem."%'";
if ($search_desc) $sql.=" AND sm.label LIKE '%".$search_desc."%'";
if ($search_prod)
{
	$sql.=" AND (p.label LIKE '%".$search_prod."%'";
	$sql.= " OR p.description LIKE '%".$search_prod."%')";
}
if ($search_statut>0)
{
	if ($search_statut == 9)
		$sql.=" AND d.statut = 0";
	else
		$sql.=" AND sm.statut = ".$search_statut;
}
elseif (empty($search_statut)) $sql.=" AND sm.statut = 0";

if (!$user->admin)
	$sql.= " AND sm.fk_entrepot IN (".$filteruser.")";

if ($conf->global->ALMACEN_FILTER_YEAR)
	$sql.= " AND (year(sm.datem) = ".$_SESSION['period_year']." OR sm.statut IN (1) )";

$sql.= " ORDER BY $sortfield $sortorder";
$sql.= $db->plimit($limit+1, $offset);
//echo $sql;
$result = $db->query($sql);
//echo $sql;
if ($result)
{
	$num = $db->num_rows($result);
	$i = 0;
	$help_url='EN:Module_Almacen_En|FR:Module_Almacen|ES:M&oacute;dulo_Almacen';
	llxHeader("",$langs->trans("ListStockToMouvements"),$help_url);

	//armamos el filtro
	print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

	print '<div style="overflow-x: auto; white-space: nowrap;">';

	print_barre_liste($langs->trans("ListStockToMouvement"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

	print '<table class="noborder" width="100%">';

	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Ref"),"liste.php", "sm.ref","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Date"),"liste.php", "sm.datem","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("De/A"),"liste.php", "sm.type_mouvement","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Entrepot"),"liste.php", "e.lieu","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Code"),"liste.php", "p.ref","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Name"),"liste.php", "p.label","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Type"),"liste.php", "t.label","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Description"),"liste.php", "sm.label","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Quantity"),"liste.php", "sm.value","","",'align="right"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Received"),"liste.php", "sm.quant","","",'align="right"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Statut"),"liste.php", "sm.statut","","","","","");
	print "</tr>\n";

	print "<tr class=\"liste_titre\">";
	print '<td>'.'<input type="text"name="search_reft" size="12" value="'.$search_reft.'">'.'</td>';
	print '<td>';
	$form->select_date($search_dateini,'re',0,0,'1',"crea_commande",1,0);
	print '</td>';
	print '<td align="right">';
	$search_type += 0;
	print $form->selectarray('search_type',$aType,$search_type,1);
	print '<td>';
	print $formproduct->selectWarehouses($search_entrepot,'search_entrepot','',1);
	print '</td>';
	print '<td>'.'<input type="text"name="search_cod" size="8" value="'.$search_cod.'">'.'</td>';
	print '<td>'.'<input type="text"name="search_prod" size="8" value="'.$search_prod.'">'.'</td>';
	print '<td>'.'<input type="text"name="search_typem" size="10" value="'.$search_typem.'">'.'</td>';
	print '<td>'.'<input type="text"name="search_desc" size="10" value="'.$search_desc.'">'.'</td>';

	print '<td colspan="3" nowrap valign="top" align="right">';
	print $form->selectarray('search_statut',$aStatut,$search_statut,1);
	print '&nbsp;';

	print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
	print '&nbsp;';
	print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Searchclear")).'" name="nosearch" title="'.dol_escape_htmltag($langs->trans("Searchclear")).'">';
	print '</td>';
	print "</tr>\n";

	if ($num)
	{
		// if ($conf->fabrication->enabled)
		//   $objproductlist = new Productlist($db);
		// if ($conf->fabrication->enabled)
		//   $objunit    = new Units($db);
		$var=True;
		while ($i < min($num,$limit))
		{
			$objp = $db->fetch_object($result);
			// if ($conf->fabrication->enabled)
			//   $objproductlist->fetch_product($objp->prowid);
			// if ($conf->fabrication->enabled)
			//   $objunit->fetch($objproduclist->fk_unit_father);
			//actualizamos
			$res = $objectdoc->fetch('',$objp->reft);
			$typemov = '';
			if ($res>0)
			{
				$objType->fetch($objectdoc->fk_type_mov);
				$typemov = $objType->type;
			}
			if ($res > 0 && $abc)
			{
				if ($objectdoc->ref == $objp->reft)
				{
					//no se hace nada
					if ($objp->type_mouvement == 1)
						$objectdoc->fk_entrepot_from = $objp->fk_entrepot;
					if ($objp->type_mouvement == 0)
						$objectdoc->fk_entrepot_to = $objp->fk_entrepot;
					$resdoc = $objectdoc->update($user);
					if ($resdoc<= 0) $mesg= ' error mod ';
				}
				else
				{
					//creamos
					$objectdoc->entity = $conf->entity;
					$objectdoc->ref = $objp->reft;
					$objectdoc->fk_entrepot_from= 0;
					$objectdoc->fk_entrepot_to = 0;
					$objectdoc->model_pdf = 'salidaalm';
					if ($objp->type_mouvement == 1)
						$objectdoc->fk_entrepot_from = $objp->fk_entrepot;
					if ($objp->type_mouvement == 0)
						$objectdoc->fk_entrepot_to = $objp->fk_entrepot;
					$objectdoc->datem =$objp->datem;
					$objectdoc->label = $objp->label;
					$objectdoc->date_create = $objp->datem;
					$objectdoc->date_mod = $objp->datem;
					$objectdoc->tms = $objp->tms;
					$objectdoc->fk_user_create = $user->id;
					$objectdoc->fk_user_mod = $user->id;
					$objectdoc->statut = $objp->statut;
					$resdoc = $objectdoc->create($user);
					if ($resdoc<=0)
					{
						$mesg = 'error ';
					}
				}
			}
			$var=!$var;
			print "<tr $bc[$var]>";
			//if ($objp->type_mouvement == 0 && $objp->statut == 1)
			//	print '<td>'.'<a href="'.DOL_URL_ROOT.'/almacen/transferencia/fiche.php?action=edit&ref='.$objp->reft.'">'.$objp->reft.'</a></td>';
			//else
//			print '<td>'.'<a href="'.DOL_URL_ROOT.'/almacen/transferencia/fiche.php?ref='.$objp->reft.'">'.$objp->reft.'</a></td>';
			if ($typemov == 'T' && $objectdoc->statut==0)
				print '<td>'.'<a href="'.DOL_URL_ROOT.'/almacen/transferencia/card.php?id='.$objectdoc->id.'">'.($res?$objectdoc->ref:$objp->reft).'</a></td>';
			else
				print '<td>'.'<a href="'.DOL_URL_ROOT.'/almacen/transferencia/fiche.php?id='.$objectdoc->id.'">'.($res?$objectdoc->ref:$objp->reft).'</a></td>';
//				print '<td>'.$objp->reft.'</td>';
			print '<td align="center">'.dol_print_date($objp->datem,'day').'</td>';
			print '<td align="center">';
			if ($objp->type_mouvement==0)
				print $langs->trans('A');
			else
				print $langs->trans('De');
			print '</td>';
			print '<td>'.$objp->lieu.'</td>';

		// if ($entrepot->id == $objp->fk_entrepot)
		// 	print '<td>'.$entrepot->libelle.'</td>';
		// else
		// 	print '<td>&nbsp;</td>';

			print '<td>'.$objp->ref.'</td>';

			print '<td>'.$objp->label.'</td>';
			print '<td>'.$objp->labeltype.'</td>';
			print '<td>'.$objp->labelmouvement.'</td>';
			$tdstyle = '';
			if ($objp->value != $objp->quant && $objp->statut > 1)
				$tdstyle = 'style="background-color:#ffd3d3;"';
			print '<td align="right" '.$tdstyle.'>'.$objp->value.'</td>';
			print '<td align="right" '.$tdstyle.'>'.$objp->quant.'</td>';
		//	    print '<td align="right">'.$objp->price.'</td>';
		// if ($conf->fabrication->enabled)
		//   print '<td>'.$objunit->ref.'</td>';
			print '<td>';
			if ($objectdoc->statut != $objp->smstatut)
				$objectdoc->statut = $objp->smstatut;

			//print $object->LibStatut($objp->statut,'',$mode);
			print $objectdoc->getLibStatut(5);
			print '</td>';
			print "</tr>\n";
			$i++;
		}
	}

	$db->free($result);

	print "</table>";
	print '</div>';
	print '</form>';
}
else
{
	dol_print_error($db);
}


$db->close();

llxFooter();
?>
