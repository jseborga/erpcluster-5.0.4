<?php
/* Copyright (C) 2001-2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2004      Eric Seigne          <eric.seigne@ryxeo.com>
 * Copyright (C) 2005      Simon TOSSER         <simon@kornog-computing.com>
 * Copyright (C) 2005-2009 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2013      Cédric Salvador      <csalvador.gpcsolutions.fr>
 * Copyright (C) 2013-2015 Juanjo Menent	    <jmenent@2byte.es>
 * Copyright (C) 2014-2015 Cédric Gross         <c.gross@kreiz-it.fr>
 * Copyright (C) 2015      Marcos García        <marcosgdf@gmail.com>
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
require_once DOL_DOCUMENT_ROOT.'/product/stock/class/entrepot.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.product.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/product.lib.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/stock/class/productstockentrepot.class.php';
if (! empty($conf->productbatch->enabled)) require_once DOL_DOCUMENT_ROOT.'/product/class/productbatch.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/cpartidaext.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/partidaproduct.class.php';

$langs->load("orgman");
$langs->load("products");
$langs->load("orders");
$langs->load("bills");
$langs->load("stocks");
$langs->load("sendings");
if (! empty($conf->productbatch->enabled)) $langs->load("productbatch");

$backtopage=GETPOST('backtopage');
$action=GETPOST("action");
$cancel=GETPOST('cancel');

$id=GETPOST('id', 'int');
$ref=GETPOST('ref', 'alpha');
$stocklimit = GETPOST('seuil_stock_alerte');
$desiredstock = GETPOST('desiredstock');
$cancel = GETPOST('cancel');
$fieldid = isset($_GET["ref"])?'ref':'rowid';
$d_eatby=dol_mktime(0, 0, 0, $_POST['eatbymonth'], $_POST['eatbyday'], $_POST['eatbyyear']);
$d_sellby=dol_mktime(0, 0, 0, $_POST['sellbymonth'], $_POST['sellbyday'], $_POST['sellbyyear']);
$pdluoid=GETPOST('pdluoid','int');
$batchnumber=GETPOST('batch_number','san_alpha');
if (!empty($batchnumber)) {
	$batchnumber=trim($batchnumber);
}
if (!isset($_SESSION['period_year'])) $_SESSION['period_year'] = date('Y');
$period_year = $_SESSION['period_year'];
// Security check
if ($user->societe_id) $socid=$user->societe_id;
$result=restrictedArea($user,'produit&stock',$id,'product&product','','',$fieldid);


$object = new Product($db);
$extrafields = new ExtraFields($db);
$objPartidaproduct = new Partidaproduct($db);
// fetch optionals attributes and labels
$extralabels=$extrafields->fetch_name_optionals_label($object->table_element);

if ($id > 0 || ! empty($ref))
{
	$result = $object->fetch($id, $ref);

}
$modulepart='product';

// Get object canvas (By default, this is not defined, so standard usage of dolibarr)
$canvas = !empty($object->canvas)?$object->canvas:GETPOST("canvas");
$objcanvas=null;
if (! empty($canvas))
{
	require_once DOL_DOCUMENT_ROOT.'/core/class/canvas.class.php';
	$objcanvas = new Canvas($db,$action);
	$objcanvas->getCanvas('stockproduct','card',$canvas);
}

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('stockproductcard','globalcard'));

/*
 *	Actions
 */

if ($cancel) $action='';

//$parameters=array('id'=>$id, 'ref'=>$ref, 'objcanvas'=>$objcanvas);
//$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);
// Note that $action and $object may have been modified by some hooks
//if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if($action == 'setcode_partida') {
	$code_partida = GETPOST('code_partida','alpha');
	$fk_product = GETPOST('id');
	//buscamos si existe la partida omitiendo la gestion
	$filter = " AND t.code = '".$code_partida."'";
	$objPartida = new Cpartida($db);
	$res = $objPartida->fetchAll('DESC','period_year,code',0,0,array('active'=>1),'AND',$filter);
	if ($res <= 0)
	{
		$error++;
		setEventMessages($langs->trans('No existe la partida'),null,'errors');
	}
	if(empty($code_partida)) {
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Partida")), null, 'errors');
	}
	if(!$error)
	{
		//primero buscamos

		$res = $objPartidaproduct->fetch($id);
		if ($res == 0)
		{
			//creamos
			$objPartidaproduct->code_partida = $code_partida;
			$objPartidaproduct->fk_product = GETPOST('id','int');
			$objPartidaproduct->fk_user_create = $user->id;
			$objPartidaproduct->fk_user_mod = $user->id;
			$objPartidaproduct->datec = dol_now();
			$objPartidaproduct->datem = dol_now();
			$objPartidaproduct->tms = dol_now();
			$objPartidaproduct->active = 1;
			$result = $objPartidaproduct->create($user);
		}
		elseif ($res > 0)
		{
			//actualizamos
			$objPartidaproduct->code_partida = $code_partida;
			$objPartidaproduct->fk_product = GETPOST('id','int');
			$objPartidaproduct->fk_user_mod = $user->id;
			$objPartidaproduct->datem = dol_now();
			$objPartidaproduct->tms = dol_now();
			$objPartidaproduct->active = 1;
			$result = $objPartidaproduct->update($user);
		}
		if ($result <=0)
		{
			$error++;
			setEventMessages($objPartidaproduct->error,$objPartidaproduct->errors,'errors');
		}
	}
	header("Location: ".$_SERVER["PHP_SELF"]."?id=".GETPOST('id'));
	exit;
}

/*
 * View
 */

$form = new Form($db);
$formproduct=new FormProduct($db);


if ($id > 0 || $ref)
{
	$object = new Product($db);
	$result = $object->fetch($id,$ref);

	$object->load_stock();

	$title = $langs->trans('ProductServiceCard');
	$helpurl = '';
	$shortlabel = dol_trunc($object->label,16);
	if (GETPOST("type") == '0' || ($object->type == Product::TYPE_PRODUCT))
	{
		$title = $langs->trans('Product')." ". $shortlabel ." - ".$langs->trans('Stock');
		$helpurl='EN:Module_Products|FR:Module_Produits|ES:M&oacute;dulo_Productos';
	}
	if (GETPOST("type") == '1' || ($object->type == Product::TYPE_SERVICE))
	{
		$title = $langs->trans('Service')." ". $shortlabel ." - ".$langs->trans('Stock');
		$helpurl='EN:Module_Services_En|FR:Module_Services|ES:M&oacute;dulo_Servicios';
	}

	llxHeader('', $title, $helpurl);

	if ($result > 0)
	{
		$head=product_prepare_head($object);
		$titre=$langs->trans("CardProduct".$object->type);
		$picto=($object->type==Product::TYPE_SERVICE?'service':'product');
		dol_fiche_head($head, 'partida', $titre, 0, $picto);

		dol_htmloutput_events();

		$linkback = '<a href="'.DOL_URL_ROOT.'/product/list.php">'.$langs->trans("BackToList").'</a>';

		dol_banner_tab($object, 'ref', $linkback, ($user->societe_id?0:1), 'ref');

		print '<div class="fichecenter">';

		print '<div class="underbanner clearboth"></div>';
		print '<table class="border tableforfield" width="100%">';

		if ($conf->productbatch->enabled)
		{
			print '<tr><td class="titlefield">'.$langs->trans("ManageLotSerial").'</td><td>';
			print $object->getLibStatut(0,2);
			print '</td></tr>';
		}

		// partida
		//buscamos la partida
		$res = $objPartidaproduct->fetch($object->id);
		if ($res >0)
		{
			//buscamos si existe la partida omitiendo la gestion
			$filter = " AND t.code = '".$objPartidaproduct->code_partida."'";
			$objPartida = new Cpartida($db);
			$res = $objPartida->fetchAll('DESC','period_year,code',0,0,array('active'=>1),'AND',$filter);
			$cPartida = '';
			if ($res>0)
			{
				foreach ($objPartida->lines AS $j => $line)
				{
					if (empty($cPartida))
						$cPartida = $line->label;
					else
						continue;
				}
			}
		 // partida
		}
		print '<tr><td>'.$form->editfieldkey("Partida",'code_partida',$objPartidaproduct->code_partida,$object,$user->rights->produit->creer).'</td><td colspan="2">';
		if ($cPartida)
		{
			$link = $objPartidaproduct->code_partida;
			$res = $objPartida->fetch(0,$objPartidaproduct->code_partida,$period_year);
			if ($res==1)
				$link = $objPartida->getNomUrl();
		}
		if ($action == 'editcode_partida')
			$link = $objPartida->code;

		print $form->editfieldval("Partida",'code_partida',$link,$object,$user->rights->produit->creer,'string').' - '.$cPartida;
		print '</td></tr>';



		print "</table>";

		print '</div>';
		print '<div style="clear:both"></div>';

		dol_fiche_end();
	}

}
else
{
	dol_print_error();
}


llxFooter();

$db->close();
