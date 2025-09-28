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
print_r($_GET);
if(isset($_GET['ref']) && $_GET['ref'] != null)
{
	$idtag = GETPOST('idtag');
	require_once(DOL_DOCUMENT_ROOT.'/budget/class/items.class.php');
	$product = new Items($db);
	echo $result = $product->fetch('',trim($_GET['ref']));
	if ($result>0)
	{
		$lView = true;
		if ($product->ref == $_GET['ref'])
		{
			if ($lView)
			{
				$rowid = $product->id;
				$ref = $product->ref;
				$fk_unit = $product->fk_unit;
				$label = $product->detail;
				$desc = $product->description;
				$price = $product->amount;
				$quant = $product->quant;
				$desc = 0;
			}
		}
		else
		{
			$lView = false;
			unset($rowid);
			$ref = $_GET['ref'];
			$label = 'dddd';
			$fk_unit = '';
			$desc = '';
			$price = 0;
			$desc = 0;
			$quant = 0;
			$refsearch = $_GET['ref'];
		}
	}
	else
	{
		$lView = false;
		unset($rowid);
		$ref = $_GET['ref'];
		$label = 'xxxxx';
		$fk_unit = '';
		$desc = '';
		$quant = 0;
		$price = 'zx';
		$desc = 'xz';
		$refsearch = $_GET['ref'];
	}

	/*
	 * Aquí haces el resto de script, asegúrate de validar bien
	 * la cédula con la función mysql_real_escape_string() de php
	 * para evitar todo tipo de injección posible.
	 */
	print '<script type="text/javascript">';
	print ' window.parent.document.getElementById('."'search_itemid'".').value = "'. $ref.'"';
	print '</script>';
	print '<script type="text/javascript">';
	print ' window.parent.document.getElementById('."'itemid'".').value = "'. $rowid.'"';
	print '</script>';
	//print '<script type="text/javascript">';
	//print ' window.parent.document.getElementById('."'quant'".').value = "'. price($quant).'"';
	//print '</script>';
	print '<script type="text/javascript">';
	print ' window.parent.document.getElementById('."'amount'".').value = "'. price($price).'"';
	print '</script>';
	print '<script type="text/javascript">';
	print ' window.parent.document.getElementById('."'fk_unit'".').value = "'. $fk_unit.'"';
	print '</script>';
	if (!$lView)
	{
		print '<script type="text/javascript">';
		print ' window.parent.document.getElementById('."'refsearch'".').value = "'. $refsearch.'"';
		print '</script>';
	}
}
?>
