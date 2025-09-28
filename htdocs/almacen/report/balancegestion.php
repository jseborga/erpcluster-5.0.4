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
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");

require_once(DOL_DOCUMENT_ROOT."/product/stock/class/entrepot.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");

require_once(DOL_DOCUMENT_ROOT."/almacen/class/commonobject_.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacen.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacendet.class.php");
require_once DOL_DOCUMENT_ROOT.'/almacen/class/entrepotuserext.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/mouvementstockext.class.php';
require_once(DOL_DOCUMENT_ROOT."/almacen/class/inventario.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/contabperiodo.class.php");

require_once(DOL_DOCUMENT_ROOT."/almacen/local/class/entrepotrelationext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/lib/almacen.lib.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/core/modules/almacen/modules_almacen.php");

$langs->load("stocks");
$langs->load("almacen@almacen");
//$langs->load("fabrication@fabrication");

if (!$user->rights->almacen->inv->read) accessforbidden();

$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];
$snom=isset($_GET["snom"])?$_GET["snom"]:$_POST["snom"];
$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];
$action 	= GETPOST('action','alpha');
$sortfield  = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder  = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
$yesnoprice = GETPOST('yesnoprice');
if (! $sortfield) $sortfield="sm.datem";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;



$typemethod = $conf->global->ALMACEN_METHOD_VALUATION_INVENTORY;

if (isset($_POST['fk_entrepot']) || isset($_GET['fk_entrepot']))
	$_SESSION['mingfk_entrepot'] = ($_POST['fk_entrepot']?$_POST['fk_entrepot']:$_GET['fk_entrepot']);
$fk_entrepot = $_SESSION['mingfk_entrepot'];

$now = dol_getdate(dol_now());
$dateinimin = dol_get_first_day($now['year'],1);
if (empty($dateinisel)) $dateinisel = dol_get_first_day($now['year'],1);

//filtramos por almacenes designados segun usuario
$objecten = new Entrepot($db);
$objectUrqEntrepot = new Entrepotrelationext($db);
$objuser = new User($db);
$objentrepotuser = new Entrepotuserext($db);
$movement = new MouvementStockext($db);
$objinv = new Inventario($db);
$periodo = new Contabperiodo($db);

//verificamos el periodo
verif_year();

$aFilterent = array();
$product = new Product($db);

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

//actions
if ($action == 'builddoc')	// En get ou en post
{
	$res = $objectUrqEntrepot->fetch($fk_entrepot);
	if (empty($res))
	{
		$objectUrqEntrepot->rowid = $fk_entrepot;
		$objectUrqEntrepot->fk_entrepot_father = -1;
		$objectUrqEntrepot->tipo = 'almacen';
		$objectUrqEntrepot->model_pdf = GETPOST('model');
		$res = $objectUrqEntrepot->create($user);
	}

	$objectUrqEntrepot->fetch_thirdparty();
	//$objecten->fetch_lines();
	if (GETPOST('model'))
	{
		$objectUrqEntrepot->setDocModel($user, GETPOST('model'));
	}
	// Define output language
	$outputlangs = $langs;
	$newlang='';
	if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang=GETPOST('lang_id');
	if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$object->client->default_lang;
	if (! empty($newlang))
	{
		$outputlangs = new Translate("",$conf);
		$outputlangs->setDefaultLang($newlang);
	}
	if (empty($objectUrqEntrepot->model_pdf))
	{
		$objectUrqEntrepot->modelpdf = GETPOST('model');
		$result=almacen_pdf_create($db, $objectUrqEntrepot, $objectUrqEntrepot->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);
	}
	if ($result <= 0)
	{
		dol_print_error($db,$result);
		exit;
	}
	else
	{
		header('Location: '.$_SERVER["PHP_SELF"].'?id='.$id.'&action=edit');
		exit;
	}
}
	// Remove file in doc form
if ($action == 'remove_file')
{
	if ($id > 0)
	{
		require_once DOL_DOCUMENT_ROOT . '/core/lib/files.lib.php';

		$langs->load("other");
		$upload_dir = $conf->almacen->dir_output;
		//. '/' . dol_sanitizeFileName($objectdoc->ref);

		$file = $upload_dir . '/' . GETPOST('file');
		$ret = dol_delete_file($file, 0, 0, 0, $product);
		if ($ret)
			setEventMessage($langs->trans("FileWasRemoved", GETPOST('urlfile')));
		else
			setEventMessage($langs->trans("ErrorFailToDeleteFile", GETPOST('urlfile')), 'errors');
		$action = '';
	}
}



$formfile = new Formfile($db);
$form = new Formv($db);
if ($fk_entrepot)
{
	$res = $objecten->fetch($fk_entrepot);
}
$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';


$aArrjs = array('almacen/javascript/recargar.js');
$aArrcss = array('almacen/css/style.css');
$help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';

llxHeader("",$langs->trans("Currentbalances"),$help_url,'','','',$aArrjs,$aArrcss);

print_barre_liste($langs->trans("Currentbalances"), $page, "liste.php", "", $sortfield, $sortorder,'',$num);

print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="edit">';
print '<input type="hidden" name="yesnoprice" value="'.$yesnoprice.'">';
print '<table class="border" width="100%">';

// Entrepot Almacen
print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Entrepot').'</td><td colspan="3">';
print $objectUrqEntrepot->select_padre($fk_entrepot,'fk_entrepot',1,'',$filteruser);
print '</td></tr>';

print '</table>';
print '<center><input type="submit" class="button" value="'.$langs->trans('Process').'"></center>';
print '</form>';


if (($action == 'edit' || $action=='edits'))
{
	print '<br>';
	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre">';
	print '<td>'.$langs->trans("Warehouse").'</td>';
	print '<td align="right">'.$langs->trans("Product").'</td>';
	print '<td align="right">'.$langs->trans("Label").'</td>';
	print '<td align="right">'.$langs->trans("Date").'</td>';
	print '<td align="right">'.$langs->trans("Balance").'</td>';
	print '</tr>';

	$lView = true;
	if ($lView)
	{
		$sql = " SELECT p.rowid, p.ref, p.label,  p.seuil_stock_alerte, ";
		$sql.= " ps.reel, ps.fk_entrepot, ps.tms, ";
		$sql.= " e.label AS entrepotlabel ";

		$sql.= " FROM ".MAIN_DB_PREFIX."product as p ";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product_stock AS ps ON p.rowid = ps.fk_product";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."entrepot AS e ON ps.fk_entrepot = e.rowid";
		$sql.= " WHERE ps.tms < '".$db->idate($dateinisel)."'";
		$sql.= " AND p.entity = ".$conf->entity;
		if ($fk_entrepot > 0) $sql.= " AND ps.fk_entrepot = ".$fk_entrepot;
		$sql.= " ORDER BY p.ref ASC ";

		$entrepotstatic=new Entrepot($db);
		$total=0;
		$totalvalue=$totalvaluesell=0;
		$aSaldores = array();
		$resql=$db->query($sql);
		if ($resql)
		{
			$num = $db->num_rows($resql);
			$total=$totalwithpmp;
			$i=0;
			$var=false;
			while ($i < $num)
			{
				$var=!$var;
				$obj = $db->fetch_object($resql);

				$product->id = $obj->rowid;
				$product->ref = $obj->ref;
				$product->label = $obj->label;

				$entrepotstatic->id=$obj->fk_entrepot;
				$entrepotstatic->libelle=$obj->entrepotlabel;

				print '<tr '.$bc[$var].'>';
				print '<td>'.$entrepotstatic->getNomUrl(1).'</td>';
				print '<td>'.$product->getNomUrl(1).'</td>';
				print '<td>'.$product->label.'</td>';
				print '<td align="right">'.dol_print_date($obj->tms,'dayhour').'</td>';
				print '<td align="right">'.$obj->reel.'</td>';

				print '</tr>'; ;
				$i++;
			}
		}
		else
			dol_print_error($db);

		print "</table>";
		//armamos un array para el reporte
		$aKardex['fk_entrepot'] = $fk_entrepot;
		$aKardex['entrepot'] = $objecten->lieu;
		$aKardex['fk_product'] = $id;
		$aKardex['productref'] = $product->ref;
		$aKardex['productlabel'] = $product->label;
		$aKardex['yesnoprice'] = $yesnoprice;
		$aKardex['unit'] = $unit;
		$aKardex['dateini'] = $_SESSION['kardex']['dateinisel'];
		$aKardex['datefin'] = $_SESSION['kardex']['datefinsel'];

		$aKardex['lines'] = array();


		// Define output language
		if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE) && $abc)
		{
			$outputlangs = $langs;
			$newlang = '';
			if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
			if ($conf->global->MAIN_MULTILANGS && empty($newlang))	$newlang = $object->thirdparty->default_lang;
			if (! empty($newlang)) {
				$outputlangs = new Translate("", $conf);
				$outputlangs->setDefaultLang($newlang);
			}

			$model='kardex';
			if ($user->rights->almacen->inv->lirev && $yesnoprice)
				$model='kardexval';
			$objinv->id = $fk_entrepot;
			$objinv->fk_product = $id;
			$result=$objinv->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);
			if ($result < 0) dol_print_error($db,$result);
		}
	}
}
print '<div class="tabsAction">';
		//documents
print '<table width="100%"><tr><td width="50%" valign="top">';
print '<a name="builddoc"></a>';
		// ancre
$objecten->fetch($fk_entrepot);
$diradd = '/balancemin';
if ($yesnoprice && $user->rights->almacen->kard->lirev) $diradd = '/balancemin';
$filename=dol_sanitizeFileName($objecten->libelle).$diradd;
		//cambiando de nombre al reporte
$filedir=$conf->almacen->dir_output . '/' . dol_sanitizeFileName($objecten->libelle).$diradd;
$urlsource=$_SERVER['PHP_SELF'].'?id='.$id.'&yesnoprice='.$yesnoprice;
$genallowed=$user->rights->almacen->creardoc;
if (empty($_SESSION['newKardex']))
	$genallowed = false;
$genallowed = false;
$delallowed=$user->rights->almacen->deldoc;
$objecten->modelpdf = 'kardex';
print '<br>';
print $formfile->showdocuments('almacen',$filename,$filedir,$urlsource,$genallowed,$delallowed,$objecten->modelpdf,1,0,0,28,0,'','','',$soc->default_lang);
$somethingshown=$formfile->numoffiles;
print '</td></tr></table>';
print "</div>";

$db->close();

llxFooter();
?>
