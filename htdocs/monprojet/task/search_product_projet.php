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
include '../../main.inc.php';

//if (! $res) die("Include of main fails");

if(isset($_GET['ref']) && $_GET['ref'] != null)
{
		echo 'buscaprodprojet ';

	//verificamos si descomponemos el ref
	$aRef = explode('|',$_GET['ref']);
	$lBudget = false;
	if (count($aRef)>1)
	{
		$ref = $aRef[1];
		$lBudget = true;
	}
	else
		$ref = $aRef[0];

	$idtag = GETPOST('idtag');

	//buscando el prod en product_projet
	require_once(DOL_DOCUMENT_ROOT.'/monprojet/class/productprojet.class.php');
	$product = new Productprojet($db);
	$filter = array('UPPER(ref)'=>strtoupper($ref));
	$filterstatic = " AND t.fk_projet = ".$_GET['id'];
	$res = $product->fetchAll('','',0,0,$filter,'AND',$filterstatic,true);
	if ($res > 0)
	{
		//existe el producto en budget
		//recuperamos informacion
		$lBudget = true;
		$lView = true;
		$rowid = $product->id;
		$ref = $product->ref;
		$label = $product->label;
		$desc = $product->description;
		$fk_unit = $product->fk_unit;
		$quant = $product->quant;
		$price = $product->amount;
	}
	else
	{
		require_once(DOL_DOCUMENT_ROOT.'/product/class/product.class.php');
		$product = new Product($db);
		$result = $product->fetch('',trim($_GET['ref']));
		$lBudget = false;
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
					$fk_unit = $product->fk_unit;
					if ($lBudget) $price = $product->amount;
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
				$fk_unit = 0;
				$desc = 0;
			}
		}
		else
		{
			$lView = false;
			unset($rowid);
			$ref = GETPOST('ref');
			$label = 'xxxxx';
			$fk_unit = 0;
			$desc = '';
			$price = 'zx';
			$desc = 'xz';
		}
	}
	//
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
		print '<script type="text/javascript">';
		print ' window.parent.document.getElementById('."'price'".').readOnly = false;';
		print '</script>';		
		print '<script type="text/javascript">';
		print ' window.parent.document.getElementById('."'fk_unit'".').readOnly = false;';
		print '</script>';			
	}
	else
	{
		print '<script type="text/javascript">';
		print ' window.parent.document.getElementById('."'price'".').readOnly = '.($lBudget?'true;':'false;');
		print '</script>';		
		print '<script type="text/javascript">';
		print ' window.parent.document.getElementById('."'fk_unit'".').readOnly = '.($lBudget?'true;':'false;');
		print '</script>';		

		print '<script type="text/javascript">';
		print ' window.parent.document.getElementById('."'fk_product_budget'".').value = "'. ($lBudget?$rowid:'').'"';
		print '</script>';
		print '<script type="text/javascript">';
		print ' window.parent.document.getElementById('."'fk_unit'".').value = "'.$fk_unit.'"';
		print '</script>';		
		//print '<script type="text/javascript">';
		//print ' window.parent.document.getElementById('."'quant'".').value = "'.$quant.'"';
		//print '</script>';		
		print '<script type="text/javascript">';
		print ' window.parent.document.getElementById('."'price'".').value = "'.$price.'"';
		print '</script>';		
		print '<script type="text/javascript">';
		print ' window.parent.document.getElementById('."'refcat'".').style.display = "none"';
		print '</script>';
		print '<script type="text/javascript">';
		print ' window.parent.document.getElementById('."'refcatn'".').style.display ="block"';
		print '</script>';		
	}
}
?>
