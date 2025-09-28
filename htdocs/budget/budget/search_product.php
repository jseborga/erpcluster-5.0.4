<?php
/* Copyright (C) 2007-2008 Jeremie Ollivier <jeremie.o@laposte.net>
 * Copyright (C) 2008-2011 Laurent Destailleur   <eldy@uers.sourceforge.net>
 * Copyright (C) 2011 Juanjo Menent			  	 <jmenent@2byte.es>
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
 *	\file       htdocs/ventas/consultanit.php
 *	\ingroup    cashdesk
 *	\brief      Include to show main page for cashdesk module
 */
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
if(isset($_GET['ref']) && $_GET['ref'] != null)
{

	$idtag = GETPOST('idtag');
	require_once(DOL_DOCUMENT_ROOT.'/product/class/product.class.php');
	require_once(DOL_DOCUMENT_ROOT.'/budget/class/productpricebudget.class.php');
	$product = new Product($db);
	$objprodpb	= new Productpricebudget($db);

	$result = $product->fetch('',trim($_GET['ref']));
	if ($result)
	{
		$lView = true;
		if ($product->ref == $_GET['ref'])
		{
			if ($lView)
			{
				$rowid = $product->id;
				$ref = $product->ref;
				$label = $product->label;
				$desc = $product->description;
				$price = $product->price_ttc;
				$desc = 0;
			}
		}
		else
		{
			$lView = false;
			unset($rowid);
			$ref = GETPOST('ref');
			$label = 'dddd';
			$desc = '';
			$price = 0;
			$desc = 0;
		}
	}
	else
	{
		$lView = false;
		unset($rowid);
		$ref = GETPOST('ref');
		$label = 'xxxxx';
		$desc = '';
		$price = 'zx';
		$desc = 'xz';
	}

	/*
	 * Aquí haces el resto de script, asegúrate de validar bien
	 * la cédula con la función mysql_real_escape_string() de php
	 * para evitar todo tipo de injección posible.
	 */
	print '<script type="text/javascript">';
	print ' window.parent.document.getElementById('."'search_product'".').value = "'. $ref.'"';
	print '</script>';
	print '<script type="text/javascript">';
	print ' window.parent.document.getElementById('."'product'".').value = "'. $rowid.'"';
	print '</script>';
	if (!$lView)
	{
		print '<script type="text/javascript">';
		print ' window.parent.document.getElementById('."'refsearch'".').value = "'. $ref.'"';
		print '</script>';
		print '<script type="text/javascript">';
		print ' window.parent.document.getElementById('."'refcat'".').style.display = "block"';
		print '</script>';		
		print '<script type="text/javascript">';
		print ' window.parent.document.getElementById('."'refcatn'".').style.display ="none"';
		print '</script>';		
		print '<script type="text/javascript">';
		print ' window.parent.document.getElementById('."'refcat'".').focus()';
		print '</script>';		
	}
}
?>
