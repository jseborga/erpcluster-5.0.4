<?php
/* Copyright (C) 2006-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *       \file       htdocs/webservices/demo_wsclient_productorservice.php
 *       \brief      Demo page to make a client call to Dolibarr WebServices "server_product"
 */

// This is to make Dolibarr working with Plesk
set_include_path($_SERVER['DOCUMENT_ROOT'].'/htdocs');

require_once '../master.inc.php';
require_once DOL_DOCUMENT_ROOT.'/wsfractal/class/userlog.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once NUSOAP_PATH.'/nusoap.php';		// Include SOAP

$WS_DOL_URL = DOL_MAIN_URL_ROOT.'/wsfractal/server_productlog.php';
//$WS_DOL_URL = 'http://localhost:8080/';	// To test with Soapui mock. If not a page, should end with /

$nameservice = GETPOST('nameservice');
$login = GETPOST('login');
$pass = GETPOST('pass');
if ($nameservice == 'verifProductlog') $WS_METHOD2 = 'verifProductlog';
if ($nameservice == 'getProductlog') $WS_METHOD3 = 'getProductlog';

$ns='http://www.dolibarr.org/ns/';

// Set the WebService URL
dol_syslog("Create nusoap_client for URL=".$WS_DOL_URL);
$soapclient1 = new nusoap_client($WS_DOL_URL);
if ($soapclient1)
{
	$soapclient1->soap_defencoding='UTF-8';
	$soapclient1->decodeUTF8(false);
}
$soapclient2 = new nusoap_client($WS_DOL_URL);
if ($soapclient2)
{
	$soapclient2->soap_defencoding='UTF-8';
	$soapclient2->decodeUTF8(false);
}
$soapclient3 = new nusoap_client($WS_DOL_URL);
if ($soapclient3)
{
	$soapclient3->soap_defencoding='UTF-8';
	$soapclient3->decodeUTF8(false);
}


// Call the WebService method and store its result in $result.
$authentication=array(
	'dolibarrkey'=>$conf->global->WEBSERVICES_KEY,
	'sourceapplication'=>'DEMO',
	'login'=>$login,
	'password'=>$pass,
	'entity'=>'');


// Test url 1
if ($WS_METHOD1)
{
	$parameters = array('authentication'=>$authentication,'id'=>1,'ref'=>'');
	dol_syslog("Call method ".$WS_METHOD1);
	$result1 = $soapclient1->call($WS_METHOD1,$parameters,$ns,'');
	if (! $result1)
	{
		print $soapclient1->error_str;
		print "<br>\n\n";
		print $soapclient1->request;
		print "<br>\n\n";
		print $soapclient1->response;
		exit;
	}
}

// Test url 2
if ($WS_METHOD2)
{
	$parameters = array('authentication'=>$authentication,'status'=>1,'ref'=>'','ref_ext'=>'');
	dol_syslog("Call method ".$WS_METHOD2);
	$result2 = $soapclient2->call($WS_METHOD2,$parameters,$ns,'');
	if (! $result2)
	{
		print $soapclient2->error_str;
		print "<br>\n\n";
		print $soapclient2->request;
		print "<br>\n\n";
		print $soapclient2->response;
		exit;
	}
}

// Test url 3
if ($WS_METHOD3)
{
	$parameters = array('authentication'=>$authentication,'filterproduct'=>array('type'=>-1));
	dol_syslog("Call method ".$WS_METHOD3);
	$result3 = $soapclient3->call($WS_METHOD3,$parameters,$ns,'');
	if (! $result3)
	{
		print $soapclient3->error_str;
		print "<br>\n\n";
		print $soapclient3->request;
		print "<br>\n\n";
		print $soapclient3->response;
		exit;
	}
}


/*
 * View
 */

header("Content-type: text/html; charset=utf8");
//print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">'."\n";

if ($WS_METHOD1)
{
	echo '<html>'."\n";
	echo '<head>';
	echo '<title>WebService Test: '.$WS_METHOD1.'</title>';
	echo '</head>'."\n";

	echo '<body>'."\n";
	echo 'NUSOAP_PATH='.NUSOAP_PATH.'<br>';

	echo "<h2>Request:</h2>";
	echo '<h4>Function</h4>';
	echo $WS_METHOD1;
	echo '<h4>SOAP Message</h4>';
	echo '<pre>' . htmlspecialchars($soapclient1->request, ENT_QUOTES) . '</pre>';
//echo '<hr>';
	echo "<h2>Response:</h2>";
	echo '<h4>Result</h4>';
	echo '<pre>';
	print_r($result1);
	echo '</pre>';
	echo '<h4>SOAP Message</h4>';
	echo '<pre>' . htmlspecialchars($soapclient1->response, ENT_QUOTES) . '</pre>';

	print '<hr>';
}
if ($WS_METHOD2)
{
	if ($result2['result']['result_code'] == 'OK')
	{
		$fk_user = $result2['productlog']['fk_user'];
		$datec = $result2['productlog']['datec'];
		//verificamos si el usuario tiene actualizado en su equipo
		$objUserlog = new Userlog($db);
		$product = new Product($db);
		//verificamos la ultima vez actualizada
		$filter = " AND t.fk_user = ".$fk_user;
		$filter.= " AND t.datec <= ".$db->idate($datec);
		$res = $objUserlog->fetchAll('DESC','datec',0,0,array(),'AND',$filter);
		$datecult = '';
		$lDateverif = false;
		if ($res>0)
		{
			$lDateverif = true;
			foreach ($objUserlog->lines AS $j => $line)
			{
				if (empty($datecult)) $datecult = $line->datec;
			}
		}
		//$filter = " AND t.fk_user = ".$fk_user;
		//$filter.= " AND t.datec > ".$db->idate($datec);
		//echo $res = $objUserlog->fetchAll('','',0,0,array(),'AND',$filter);

		if (empty($res))
		{
			//vamos a recorrer todos los productos para enviar
			$aProduct = array();

			$lines = $result2['productlog']['lines'];

			if (count($lines)>0)
			{
				foreach ($lines AS $j => $line)
				{
					//echo '<hr>'.$line->datec.' === '.$datecult;
					$lView = true;
					if ($lDateverif)
					{
						if ($line->datec > $datecult )
							$lView = true;
						else
							$lView = false;
					}
					if ($lView)
					{
						$product->fetch ($line['fk_product']);
						$aProduct[$j]= array(
							'id' => $product->id,
							'ref' => $product->ref,
							'ref_ext' => $product->ref_ext,
							'label' => $product->label,
							'description' => $product->description,
							'date_creation' => dol_print_date($product->date_creation,'dayhourrfc'),
							'date_modification' => dol_print_date($product->date_modification,'dayhourrfc'),
							'note' => $product->note,
							'status_tosell' => $product->status,
							'status_tobuy' => $product->status_buy,
							'type' => $product->type,
							'barcode' => $product->barcode,
							'barcode_type' => $product->barcode_type,
							'country_id' => $product->country_id>0?$product->country_id:'',
							'country_code' => $product->country_code,
							'custom_code' => $product->customcode,

							'price_net' => $product->price,
							'price' => $product->price_ttc,
							'price_min_net' => $product->price_min,
							'price_min' => $product->price_min_ttc,
							'price_base_type' => $product->price_base_type,
							'vat_rate' => $product->tva_tx,
						//! French VAT NPR
							'vat_npr' => $product->tva_npr,
						//! Spanish local taxes
							'localtax1_tx' => $product->localtax1_tx,
							'localtax2_tx' => $product->localtax2_tx,

							'stock_real' => $product->stock_reel,
							'stock_virtual' => $product->stock_theorique,
							'stock_alert' => $product->seuil_stock_alerte,
							'pmp' => $product->pmp,
							'import_key' => $product->import_key,
							'dir' => $pdir,
							'images' => $product->liste_photos($dir,$nbmax=10)
						);
					}
				}
			}
			unset($result2);
			$result2['productlog'] = $aProduct;
		}
		else
		{
			unset($aProduct);
			$lines = $result2['productlog']['lines'];
			foreach ($lines AS $j => $line)
			{
				//echo '<hr>'.$line['datec'].' === '.$datecult;
				$lView = true;
				if ($lDateverif)
				{
					if ($line['datec'] > $datecult )
						$lView = true;
					else
						$lView = false;
				}
				if ($lView)
				{
					//echo '<br>fk_prod '.$line['fk_product'];
					$product->fetch ($line['fk_product']);
					$aProduct[]= array(
						'id' => $product->id,
						'ref' => $product->ref,
						'ref_ext' => $product->ref_ext,
						'label' => $product->label,
						'description' => $product->description,
						'date_creation' => dol_print_date($product->date_creation,'dayhourrfc'),
						'date_modification' => dol_print_date($product->date_modification,'dayhourrfc'),
						'note' => $product->note,
						'status_tosell' => $product->status,
						'status_tobuy' => $product->status_buy,
						'type' => $product->type,
						'barcode' => $product->barcode,
						'barcode_type' => $product->barcode_type,
						'country_id' => $product->country_id>0?$product->country_id:'',
						'country_code' => $product->country_code,
						'custom_code' => $product->customcode,

						'price_net' => $product->price,
						'price' => $product->price_ttc,
						'price_min_net' => $product->price_min,
						'price_min' => $product->price_min_ttc,
						'price_base_type' => $product->price_base_type,
						'vat_rate' => $product->tva_tx,
						//! French VAT NPR
						'vat_npr' => $product->tva_npr,
						//! Spanish local taxes
						'localtax1_tx' => $product->localtax1_tx,
						'localtax2_tx' => $product->localtax2_tx,

						'stock_real' => $product->stock_reel,
						'stock_virtual' => $product->stock_theorique,
						'stock_alert' => $product->seuil_stock_alerte,
						'pmp' => $product->pmp,
						'import_key' => $product->import_key,
						'dir' => $pdir,
						'images' => $product->liste_photos($dir,$nbmax=10)
					);
				}
			}

			unset($resultnew);
			$resultnew['productlog'] = $aProduct;
			$result2['productlog'] = $resultnew['productlog'];
		}
	}
	$aResult['productadd'] = $result2['productlog'];

	echo json_encode($aResult);
}

if ($WS_METHOD3)
{

//echo "<h2>Request:</h2>";
//echo '<h4>Function</h4>';
//echo $WS_METHOD3;
//echo '<h4>SOAP Message</h4>';
//echo '<pre>' . htmlspecialchars($soapclient3->request, ENT_QUOTES) . '</pre>';
//echo '<hr>';
//echo "<h2>Response:</h2>";
//echo '<h4>Result</h4>';
//echo '<pre>';
//print_r($result3);
	$aProduct['products'] = $result3['products'];

	echo json_encode($aProduct);
//echo '</pre>';
//echo '<h4>SOAP Message</h4>';
//echo '<pre>' . htmlspecialchars($soapclient3->response, ENT_QUOTES) . '</pre>';

}

//echo '</body>'."\n";
//echo '</html>'."\n";
?>
