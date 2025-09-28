<?php
/* Copyright (C) 2001-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2015-2015 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *      \file       htdocs/fabrication/report.php
 *      \ingroup    Fabrication
 *      \brief      Page report fabrication date ini date fin,
 */

require("../../main.inc.php");

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';

require_once(DOL_DOCUMENT_ROOT."/fabrication/class/fabricationext.class.php");
require_once(DOL_DOCUMENT_ROOT."/fabrication/class/fabricationdet.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/stock/class/entrepot.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/stock/class/mouvementstock.class.php");
require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/fabrication/class/fabrication.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/local/class/entrepotrelation.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/core/modules/almacen/modules_almacen.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacenext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacendetext.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/productunit.class.php");
require_once(DOL_DOCUMENT_ROOT."/commande/class/commande.class.php");
require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/lib/almacen.lib.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/stock.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/product.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
if ($conf->ventas->enabled)
	require_once DOL_DOCUMENT_ROOT.'/ventas/class/commandesale.class.php';
$langs->load("stocks");
$langs->load("almacen@almacen");

if (!$user->rights->fabrication->report)
	accessforbidden();

$id = GETPOST('id','int');
$yesno = GETPOST('yesno');
$zeroyesno = GETPOST('zeroyesno');
$action = GETPOST('action');
if (empty($yesno))
	$yesno = $_SESSION['selyesno'];
if (empty($yesno))
	$yesno = 2;
if (empty($zeroyesno))
	$zeroyesno = $_SESSION['selzeroyesno'];
if (empty($zeroyesno))
	$zeroyesno = 2;
if (empty($id))
	$id = $_SESSION['idEntrepot'];
$dateini = dol_now();
$datefin = dol_now();
if ($action == 'edit')
{
	$dateini  = dol_mktime(12, 0, 0, GETPOST('dimonth'),  GETPOST('diday'),  GETPOST('diyear'));
	$datefin  = dol_mktime(23, 59, 59, GETPOST('dfmonth'),  GETPOST('dfday'),  GETPOST('dfyear'));

}
$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
if (! $sortfield) $sortfield="f.ref";
if (! $sortorder) $sortorder="ASC";
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;

$object  = new Fabricationext($db);
$objectd = new Fabricationdet($db);
$solalmacen = new Solalmacenext($db);
$product = new Product($db);
$productunit = new Productunit($db);
//$object = new Entrepotrelation($db);
$entrepot = new Entrepot($db);
$commande = new Commande($db);
$form = new Form($db);
$formfile = new Formfile($db);
$movement=new MouvementStock($db);
$societe = new Societe($db);
$objuser = new User($db);
if ($conf->ventas->enabled)
	$commandesale = new Commandesale($db);

if ($id>0)
	$object->fetch($id);
$hookmanager->initHooks(array('almacen'));
//print_r($hookmanager);
/*
Actions
*/
if ($action == 'builddoc')	// En get ou en post
{
	// $object->fetch($id);
	// if (empty($object->id))
	//   $object->id = $id;
	// $object->fetch_thirdparty();
	// $object->fetch_lines();
	// if (GETPOST('model'))
	//   {
	//     $object->setDocModel($user, GETPOST('model'));
	//   }

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
	$result=almacen_pdf_create($db, $object, $object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref, $hookmanager);
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


$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';
llxHeader("",$langs->trans("Manufacturingreport"),$help_url);

print_fiche_titre($langs->trans("Manufacturingreport"));

print '<div>';
print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="edit">';

print '<table class="border" width="100%">';

print '<tr><td width="25%" class="fieldrequired">'.$langs->trans('Productionorder').'</td><td colspan="3">';
print $object->select_fabrication($id,'id','',0, 1,'');
print '</td></tr>';

print '</table>';
print '<center><input type="submit" class="button" value="'.$langs->trans('Process').'"></center>';
print '</form>';
print '</div>';
print '<br>';

if ($action == 'edit')
{
	$_SESSION['fk_fabrication'] = $id;
	$_SESSION['selyesno'] = $yesno;
	$_SESSION['selzeroyesno'] = $zeroyesno;
	$aRowid = array();

	//movimiento de salidas y entradas
	// if ($yesno == 1)
	//   $object->fetch_lines();

	$object->fetch($id);
	$object->fetch_lines();

	//obtenemos todos los pedidos a almacen
	$commande->fetch($object->fk_commande);
	if ($conf->ventas->enabled)
		$commandesale->fetch('',$object->fk_commande);
	$societe->fetch($commande->socid);
	$objuser->fetch($commande->user_author_id);
	$aAlmacen = $solalmacen->fetch_fabrication($id);

	if (count($aAlmacen)>0)
	{
		print_fiche_titre($langs->trans('Fabrication').' '.$object->ref);
		if ($object->fk_commande>0)
		{
			print '<table class="noborder" width="100%">';
			print "<tr>";
			print '<td>'.$langs->trans('Order').'</td>';
			print '<td><b>'.$commande->ref.'</b></td>';
			print '<td>'.$langs->trans('Client').': <b>'.$societe->nom.'</b></td>';
			print '<td>'.$langs->trans('User').': <b>'.$objuser->lastname.' '.$objuser->firstname.'</b></td>';
			print '</tr>';
			print "<tr>";
			print '<td>'.$langs->trans('Creationdate').'</td>';
			print '<td>'.'<b>'.dol_print_date($commande->date,'day').'</b></td>';
			print '<td>'.$langs->trans('Deliverdate').': <b>'.dol_print_date($commande->date_livraison,'day').'</b></td>';
		//almacen destino
			$entrepot->fetch($commandesale->fk_entrepot_end);
			print '<td>'.$langs->trans('Warehousedelivery').': <b>'.$entrepot->lieu.'</b></td>';

			print '</tr>';
			print '</table>';
		}
		//que se tiene que producir
		print '<table class="noborder" width="100%">';
		print "<tr class=\"liste_titre\">";
		print_liste_field_titre($langs->trans("Product"),"reportop.php", "","","",'align="left"');
		print_liste_field_titre($langs->trans("Unit"),"reportop.php", "","","",'align="left"');
		print_liste_field_titre($langs->trans("Qty"),"reportop.php", "","","",'align="right"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Merma"),"reportop.php", "","","",'align="right"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Producido"),"reportop.php", "","","",'align="right"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Price"),"reportop.php", "","","",'align="right"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Total"),"reportop.php", "","","",'align="right"',$sortfield,$sortorder);
		print '</tr>';
		//recorremos
		$suma = 0;
		$j = 0;
		foreach ((array) $object->lines AS $i => $line)
		{
			$unit = '';
			$product->fetch($line->fk_product);
			$productunit->fetch('',$line->fk_product);
			if ($productunit->fk_product == $line->fk_product)
				$unit = $productunit->getLabelOfUnit();
			//imprimimos
			print '<tr>';
			print '<td>'.$product->getNomUrl(1).' '.$line->libelle.'</td>';
			print '<td>'.$unit.'</td>';
			print '<td align="right">'.price2num($line->qty).'</td>';
			print '<td align="right">'.price2num($line->qty_decrease).'</td>';
			print '<td align="right">'.price2num($line->qty_first).'</td>';
			print '<td align="right">'.price(price2num($line->price,'MU')).'</td>';
			print '<td align="right">'.price(price2num($line->qty_first*$line->price,'MU')).'</td>';
			print '</tr>';
			$suma+=price2num($line->qty_first*$line->price,'MU');
			$j++;
		}
		print '<tr class="liste_total">';
		print '<td colspan="2">'.$langs->trans('Total').'</td>';
		print '<td colspan="4">'.$j.' '.$langs->trans('Items').'</td>';
		print '<td align="right">'.price(price2num($suma,'MT')).'</td>';
		print '</tr>';

		print "</table>";


		$suma = 0;
		$j = 0;
		print '<table class="noborder" width="100%">';
		print "<tr class=\"liste_titre\">";
		print_liste_field_titre($langs->trans("Ref"),"reportop.php", "","","","",$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Date"),"reportop.php", "","","",'align="left"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Supplies"),"reportop.php", "","","",'align="left"');
		print_liste_field_titre($langs->trans("Unit"),"reportop.php", "","","",'align="left"');
		print_liste_field_titre($langs->trans("Qty"),"reportop.php", "","","",'align="right"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Price"),"reportop.php", "","","",'align="right"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Total"),"reportop.php", "","","",'align="right"',$sortfield,$sortorder);
		print '</tr>';
		//recorremos
		$suma = 0;
		$j = 0;
		foreach ((array) $aAlmacen AS $fk_solalmacen => $aData)
		{
			$solalmacen->fetch($fk_solalmacen);
			$solalmacen->fetch_lines();
			foreach((array) $solalmacen->lines AS $line)
			{
				$unit = '';
				$product->fetch($line->fk_product);
				$productunit->fetch('',$line->fk_product);
				if ($productunit->fk_product == $line->fk_product)
					$unit = $productunit->getLabelOfUnit();
				//imprimimos
				print '<tr>';
				print '<td>'.$solalmacen->ref.'</td>';
				print '<td>'.dol_print_date($solalmacen->date_creation,'day').'</td>';
				print '<td>'.$product->getNomUrl(1).' '.$line->libelle.'</td>';
				print '<td>'.$unit.'</td>';
				print '<td align="right">'.price2num($line->qty).'</td>';
				print '<td align="right">'.price(price2num($line->price,'MU')).'</td>';
				print '<td align="right">'.price(price2num($line->qty*$line->price,'MU')).'</td>';
				print '</tr>';
				$suma+=price2num($line->qty*$line->price,'MU');
				$j++;
			}
		}
		print '<tr class="liste_total">';
		print '<td colspan="2">'.$langs->trans('Total').'</td>';
		print '<td colspan="4">'.$j.' '.$langs->trans('Items').'</td>';
		print '<td align="right">'.price(price2num($suma,'MT')).'</td>';
		print '</tr>';

		print "</table>";

	// print '<div class="tabsAction">';
	// //documents
	//     print '<table width="100%"><tr><td width="50%" valign="top">';
	//     print '<a name="builddoc"></a>'; // ancre
	//     $objecten->fetch($id);
	//     /*
	//      * Documents generes
	//      */
	//     $filename=dol_sanitizeFileName($objecten->libelle);
	//     //cambiando de nombre al reporte
	//     $filedir=$conf->almacen->dir_output . '/' . dol_sanitizeFileName($objecten->libelle);
	//     $urlsource=$_SERVER['PHP_SELF'].'?id='.$id;
	//     $genallowed=$user->rights->almacen->crearpedido;
	//     $delallowed=$user->rights->almacen->delpedido;
	//     print '<br>';
	//     print $formfile->showdocuments('almacen',$filename,$filedir,$urlsource,$genallowed,$delallowed,$object->modelpdf,1,0,0,28,0,'','','',$soc->default_lang);
	//     $somethingshown=$formfile->numoffiles;
	//     print '</td></tr></table>';

	// print "</div>";

	}
	// else
	//   {
	// 	dol_print_error($db);
	//   }
}

$db->close();

llxFooter();
?>
