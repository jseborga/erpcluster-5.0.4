<?php
/* Copyright (C) 2001-2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2004      Eric Seigne          <eric.seigne@ryxeo.com>
 * Copyright (C) 2005      Simon TOSSER         <simon@kornog-computing.com>
 * Copyright (C) 2005-2009 Regis Houssin        <regis.houssin@capnetworks.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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
 *	\file       htdocs/product/stock/product.php
 *	\ingroup    product stock
 *	\brief      Page to list detailed stock of a product
 */

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
// require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/product/stock/class/entrepot.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/product.lib.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementtype.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementadd.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementdocext.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/mouvementstockext.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/entrepotbanksoc.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/entrepotuserext.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/ctypemouvement.class.php';

require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartament.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/csources.class.php';

require_once DOL_DOCUMENT_ROOT.'/almacen/lib/almacen.lib.php';
require_once(DOL_DOCUMENT_ROOT."/almacen/core/modules/almacen/modules_almacen.php");

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';


$langs->load("almacen");
$langs->load("products");
$langs->load("orders");
$langs->load("bills");
$langs->load("stocks");
$langs->load("orgman");

if (!$user->rights->almacen->transf->write)
	accessforbidden();

$action=GETPOST("action");
$cancel=GETPOST('cancel');
$confirm=GETPOST('confirm');

// Security check
$id    = GETPOST('id')?GETPOST('id'):GETPOST('ref');
$url = GETPOST('url','alpha');
$idreg = GETPOST('idreg');
$idr   = GETPOST('idr');
$ref   = GETPOST('ref');
$stocklimit = GETPOST('stocklimit');
$fieldid    = isset($_GET["ref"])?'ref':'rowid';

if ($user->societe_id) $socid=$user->societe_id;
//$result=restrictedArea($user,'almacen',$id,'product&product','','',$fieldid);

$typemethod = $conf->global->ALMACEN_METHOD_VALUATION_INVENTORY;
//verificamos el periodo
verif_year();

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
include_once DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php';
$hookmanager=new HookManager($db);
$hookmanager->initHooks(array('ordercard'));

$object = new MouvementStockext($db);
$objectadd = new Stockmouvementadd($db);
$objectdoc = new Stockmouvementdocext($db);
$objproduct = new Product($db);
$entrepot = new Entrepot($db);
$objentrepotuser = new Entrepotuserext($db);
$objecttype = new Ctypemouvement($db);
$departament = new Pdepartament($db);
$objsource = new Csources($db);
$societe = new Societe($db);

$formfile = new Formfile($db);

//filtro por usuario
$filteruser = '0';
$aFilterent = array();
if ($id)
{
	$res = $objectdoc->fetch($id);
}

if ($action == 'builddoc')	// En get ou en post
{
	$objectdoc->fetch($id,trim($ref));
	//$id = $objectdoc->id;
	//$objectdoc->fetch_thirdparty();
	//$objectdoc->fetch_lines();
	if (GETPOST('model'))
	{
		$objectdoc->setDocModel($user, GETPOST('model'));
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
	$result=almacen_pdf_create($db, $objectdoc, $objectdoc->model_pdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);
	if ($result <= 0)
	{
		dol_print_error($db,$result);
		exit;
	}
	else
	{
		header('Location: '.$_SERVER["PHP_SELF"].'?id='.$id);
		exit;
	}
}
	// Remove file in doc form
if ($action == 'remove_file')
{
	if ($objectdoc->id > 0)
	{
		require_once DOL_DOCUMENT_ROOT . '/core/lib/files.lib.php';

		$langs->load("other");
		$upload_dir = $conf->almacen->dir_output;
		//. '/' . dol_sanitizeFileName($objectdoc->ref);

		$file = $upload_dir . '/' . GETPOST('file');
		$ret = dol_delete_file($file, 0, 0, 0, $objectdoc);
		if ($ret)
			setEventMessage($langs->trans("FileWasRemoved", GETPOST('urlfile')));
		else
			setEventMessage($langs->trans("ErrorFailToDeleteFile", GETPOST('urlfile')), 'errors');
		$action = '';
	}
}


if ($cancel)
{
	header("Location: ".DOL_URL_ROOT."/almacen/mouvement.php");
	exit;
}

$itemTransf = array();
$transf = array();
if (! empty($_SESSION['itemTransf'])) $itemTransf=json_decode($_SESSION['itemTransf'],true);
if (! empty($_SESSION['transf'])) $transf=json_decode($_SESSION['transf'],true);
// Set stock limit
if ($action == 'setstocklimit')
{
	$product = new Product($db);
	$result=$product->fetch($id);
	$product->seuil_stock_alerte=$stocklimit;
	$result=$product->update($product->id,$user,1,0,1);
	if ($result < 0)
		setEventMessage($product->error, 'errors');
	$action = '';
}



/*
 * View
 */

$formproduct=new FormProduct($db);
$form = new Formv($db);

// if ($ref) $result = $product->fetch('',$ref);
// if ($id > 0) $result = $product->fetch($id);
//$arrayofcss=array('/ventas/css/style.css');
//$arrayofjs=array('/almacen/javascript/recargar.js');
$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';

$morejs=array("/almacen/javascript/almacen.js");
llxHeader('',$langs->trans("Transfer"),$help_url,'','','',$morejs,'',0,0);

/*
 * habilitamos una sesion para la carga de items de transferencia
 * $_SESSION['itemTransf'] = array()
*/


//edicion
if ((!empty($id) || !empty($ref)) && $action != 'mod')
{
	$resdoc = $objectdoc->fetch($id,$ref);
	$id = $objectdoc->id;
	$objecttype->fetch($objectdoc->fk_type_mov);

	//imprimimos la cabecera
	print_fiche_titre($langs->trans('Mouvement').': '.$objecttype->label);
	dol_fiche_head();

	//imprimimos el cuerpo directamente de stock_mouvement
	print '<table class="noborder" width="100%">';
	print "<tr>";
	print '<td>'.$langs->trans("Ref").'</td>';
	print '<td>'.$objectdoc->ref.'</td>';
	print '</tr>';
	//departament
	if ($objectdoc->fk_departament > 0)
	{
		print "<tr>";
		print '<td>'.$langs->trans("Departament").'</td>';
		$departament->fetch($objectdoc->fk_departament);
		print '<td>'.$departament->getNomUrl(1).'</td>';
		print '</tr>';
	}
	//proveedor
	if ($objectdoc->fk_soc > 0)
	{
		print "<tr>";
		print '<td>'.$langs->trans("Societe").'</td>';
		$societe->fetch($objectdoc->fk_soc);
		print '<td>'.$societe->getNomUrl(1).'</td>';
		print '</tr>';
	}
	//source
	if ($objectdoc->fk_source > 0)
	{
		print "<tr>";
		print '<td>'.$langs->trans("Foundingsource").'</td>';
		$objsource->fetch($objectdoc->fk_source);
		print '<td>'.$objsource->getNomUrl(1).'</td>';
		print '</tr>';
	}
	print "<tr>";
	print '<td>'.$langs->trans("Document").'</td>';
	print '<td>'.$objectdoc->ref_ext.'</td>';
	print '</tr>';

	print "<tr>";
	print '<td>'.$langs->trans("Label").'</td>';
	print '<td>'.$objectdoc->label.'</td>';
	print '</tr>';

	print '</table>';

	dol_fiche_end();

	dol_fiche_head();
	$filterstatic = " AND fk_stock_mouvement_doc = ".$objectdoc->id;
	$res = $object->fetchAll('ASC', 'datem', 0, 0, array(1=>1), 'AND', $filterstatic);

	print '<table class="border" width="100%">';
	print "<tr class=\"liste_titre\">";
	print '<td >'.$langs->trans("Product").'</td>';
	print '<td >'.$langs->trans("Description").'</td>';
	print '<td >'.$langs->trans("Unit").'</td>';
	print '<td >'.$langs->trans("Price").'</td>';
	print '<td >'.$langs->trans("Qty").'</td>';
	print '<td >'.$langs->trans("Total").'</td>';
	print '</tr>';
	if ($res > 0)
	{
		$lines = $object->lines;
		$sumTotal = 0;
		$cuenta = 0;
		foreach ($lines AS $j => $line)
		{
			$var = !$var;
			$objproduct->fetch ($line->fk_product);
			print "<tr $bc[$var]>";
			print '<td >'.$objproduct->getNomUrl(1).'</td>';
			print '<td >'.$objproduct->label.'</td>';
			print '<td >'.$objproduct->getLabelOfUnit().'</td>';
			print '<td align="right">'.price(price2num($line->price,'MU'),5).'</td>';
			print '<td align="right">'.$line->value.'</td>';
			print '<td align="right">'.price(price2num($line->value * $line->price,'MT')).'</td>';
			print '</tr>';
			$sumTotal += $line->value * $line->price;
			$cuenta+= $line->value;
		}
		print '<tr class="liste_total">';
		print '<td colspan="4">'.$langs->trans('Total').'</td>';
		print '<td align="right">'.$cuenta.'</td>';
		print '<td align="right">'.price(price2num($sumTotal,'MT')).'</td>';
		print '</tr>';
	}
	print '</table>';
	dol_fiche_end();

	if ($res > 0)
	{
	        // Define output language
		if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
		{
			$outputlangs = $langs;
			$newlang = '';
			if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
			if ($conf->global->MAIN_MULTILANGS && empty($newlang))	$newlang = $objectdoc->thirdparty->default_lang;
			if (! empty($newlang)) {
				$outputlangs = new Translate("", $conf);
				$outputlangs->setDefaultLang($newlang);
			}

			$model=$objectdoc->model_pdf;
			$result=$objectdoc->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);
			if ($result < 0) dol_print_error($db,$result);
		}
	}

	/* ********************************************* */
	/*                                               */
	/* Barre d'action                                */
	/*                                               */
	/* ********************************************* */

	if (empty($action))
	{
		print "<div class=\"tabsAction\">\n";
		if ($url)
			print '<a class="butAction" href="'.$url.'">'.$langs->trans("Return").'</a>';
		else
			print '<a class="butAction" href="'.DOL_URL_ROOT.'/almacen/mouvement.php">'.$langs->trans("Return").'</a>';
		print '</div>';
	}

		print '<div class="tabsAction">';
			//documents
		print '<table width="100%"><tr><td width="50%" valign="top">';
		print '<a name="builddoc"></a>';
		$filename=dol_sanitizeFileName($objectdoc->ref);
			//cambiando de nombre al reporte
		$filedir   =$conf->almacen->dir_output . '/' . dol_sanitizeFileName($objectdoc->ref);
		$urlsource =$_SERVER['PHP_SELF'].'?id='.$id;
		$genallowed=$user->rights->almacen->creardoc;
		$delallowed=$user->rights->almacen->deldoc;
		$genallowed=0;
		$delallowed=0;
		$objectdoc->modelpdf = $objectdoc->model_pdf;
		print '<br>';
		print $formfile->showdocuments('almacen',$filename,$filedir,$urlsource,$genallowed,$delallowed,$objectdoc->modelpdf,1,0,0,28,0,'','','',$soc->default_lang);
		$somethingshown=$formfile->numoffiles;
		print '</td></tr></table>';

		print "</div>";



}
dol_fiche_end();


llxFooter();

$db->close();
?>
