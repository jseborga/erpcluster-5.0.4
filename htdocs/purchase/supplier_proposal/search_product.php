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

require_once("../../main.inc.php");

if(isset($_GET['ref']) && $_GET['ref'] != null || isset($_GET['idprod']) && $_GET['idprod'] >0)
{
	$search = trim($_GET['ref']);
	$idprod = $_GET['idprod'];
	require_once(DOL_DOCUMENT_ROOT.'/product/class/product.class.php');
	$product = new Product($db);
	$res = $result = $product->fetch($idprod,trim($_GET['ref']));
	$fk_unit = 0;
	if ($result)
	{
		$lView = true;
		if ($idprod == $product->id)
		{
			$rowid = $product->id;
			$ref = $product->ref;
			$label = $product->label;
			$desc = $product->description;
			$price = $product->price_ttc;
			$fk_unit = $product->fk_unit;
			$desc = 0;
		}
		elseif ($product->ref == $_GET['ref'])
		{
			$rowid = $product->id;
			$ref = $product->ref;
			$label = $product->label;
			$desc = $product->description;
			$price = $product->price_ttc;
			$fk_unit = $product->fk_unit;
			$desc = 0;
		}
		else
		{
			unset($rowid);
			$ref = '';
			$label = 'dddd';
			$desc = '';
			$price = 0;
			$desc = 0;
			$fk_unit= 0;
		}
	}
	else
	{
		unset($rowid);
		$ref = '';
		$label = 'xxxxx';
		$desc = '';
		$price = 'zx';
		$desc = 'xz';
		$fk_unit = 0;
	}
	/*
	 * Aquí haces el resto de script, asegúrate de validar bien
	 * la cédula con la función mysql_real_escape_string() de php
	 * para evitar todo tipo de injección posible.
	 */
	print '<script type="text/javascript">';
	print ' window.parent.document.getElementById('."'fk_unit'".').value = "'. $fk_unit.'"';
	print '</script>';
	print '<script type="text/javascript">';
	print ' window.parent.document.getElementById('."'idprodfournprice'".').value = "'. $rowid.'"';
	print '</script>';
}
?>
