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
 *       \file       htdocs/webservices/demo_wsclient_order.php
 *       \brief      Demo page to make a client call to Dolibarr WebServices "server_order"
 */

// This is to make Dolibarr working with Plesk
set_include_path($_SERVER['DOCUMENT_ROOT'].'/htdocs');

require_once '../master.inc.php';
require_once NUSOAP_PATH.'/nusoap.php';
		// Include SOAP

$method = GETPOST('method');
$fk = GETPOST('fk');
$WS_DOL_URL = DOL_MAIN_URL_ROOT.'/wsfractal/server_order.php';
//$WS_DOL_URL = 'http://localhost:8080/';	// To test with Soapui mock. If not a page, should end with /
if (empty($method))
	$WS_METHOD0  = 'createOrder';
else
{
	$WS_METHOD1  = $method;
}
//$WS_METHOD2  = 'getOrdersForThirdParty';
$ns='http://www.dolibarr.org/ns/';


//recibiendo valores
$carray = html_entity_decode(GETPOST('array'));

$fk_user = GETPOST('fk_user');
$fk_soc = GETPOST('fk_soc');
$entity = GETPOST('entity');
$imei = GETPOST('imei');
$login = GETPOST('login');
$pass = GETPOST('pass');
$send = GETPOST('send');

//procesamos la información del array
$a = 1;
$aArray = explode('|',$carray);

$vat = 13;
if (count($aArray)>0)
{
	$sum_total_net=0;
	$sum_total_vat=0;
	$sum_total =0;
	foreach ($aArray AS $j => $data)
	{
		if (!empty($data))
		{
			$aTmp = explode(':',$data);
			$line = array(
				"id" => $a,
				"type" => 0,
				"desc" => $langs->trans('Pedido por webservice'),
				"vat_rate" => $vat,
				"qty" => $aTmp[3],
				"unitprice" => $aTmp[2],
				"total_net" => $aTmp[3]*$aTmp[2]-($aTmp[3]*$aTmp[2]*$vat/100),
				"total_vat" => $aTmp[3]*$aTmp[2]*$vat/100,
				"total" => $aTmp[3]*$aTmp[2],
				"date_start" => "",
				"date_end" => "",
				"payment_mode_id" => "efectivo",
				"product_id" => $aTmp[0],
				"product_ref" => $aTmp[1],
				"product_label" => "",
				"product_desc" => ""
			);
			$sum_total_net+= $aTmp[3]*$aTmp[2]-($aTmp[3]*$aTmp[2]*$vat/100);
			$sum_total_vat+=$aTmp[3]*$aTmp[2]*$vat/100;
			$sum_total+=$aTmp[3]*$aTmp[2];
			$lines[] = $line;
		}
	}
	$invoice = array(
		"id" => $a,
		"ref" => $a,
		"ref_ext" => $a,
		"thirdparty_id" => $fk_soc,
		"fk_user_author" => $fk_user,
		"fk_user_valid" => $fk_user,
		"date" => date("Y-m-d"),
		"date_due" => date("Y-m-d"),
		"date_creation" => date("Y-m-d h:i:sa"),
		"date_validation" => date("Y-m-d h:i:sa"),
		"date_modification" => "",
		"type" => 0,
		"total_net" => $sum_total_net,
		"total_vat" => $sum_total_vat,
		"total" => $sum_total,
		"note_private" => "Registado desde ".$imei,
		"note_public" => $send,
		"status" => 2,
		"close_code" => "",
		"close_note" => "",
		"project_id" => "",
		"lines" => $lines
	);
}
// Set the WebService URL
dol_syslog("Create nusoap_client for URL=".$WS_DOL_URL);
$soapclient0 = new nusoap_client($WS_DOL_URL);
if ($soapclient0)
{
	$soapclient0->soap_defencoding='UTF-8';
	$soapclient0->decodeUTF8(false);
}
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

// Call the WebService method and store its result in $result.
$authentication=array(
	'dolibarrkey'=>$conf->global->WEBSERVICES_KEY,
	'sourceapplication'=>'DEMO',
	'login'=>$login,
	'password'=>$pass,
	'entity'=>($entity?$entity:1));


/*
//recuperamos los array de datos
$line = array(
	"id" => "59",
	"type" => 0,
	"desc" => "SEKO",
	"vat_rate" => 16.000,
	"qty" => 03,
	"unitprice" => 10500.00000000,
	"total_net" => 10500.0000000,
	"total_vat" => 1680.00000000,
	"total" => 12180.0000000,
	"date_start" => "",
	"date_end" => "",
	"payment_mode_id" => "efectivo",
	"product_id" => 1,
	"product_ref" => "",
	"product_label" => "",
	"product_desc" => ""
);
$lines[] = $line;
$invoice = array(
	"id" => "59",
	"ref" => "0007",
	"ref_ext" => "test",
	"thirdparty_id" => 6,
	"fk_user_author" => "1",
	"fk_user_valid" => "1",
	"date" => date("Y-m-d"),
	"date_due" => date("Y-m-d"),
	"date_creation" => date("Y-m-d h:i:sa"),
	"date_validation" => date("Y-m-d h:i:sa"),
	"date_modification" => "",
	"type" => 0,
	"total_net" => 10500.00000000,
	"total_vat" => 1680.00000000,
	"total" => 12180.0000000,
	"note_private" => "",
	"note_public" => "",
	"status" => 2,
	"close_code" => "",
	"close_note" => "",
	"project_id" => "",
	"lines" => $lines
);
*/

// Test url 1
$lGet = false;
if ($WS_METHOD0)
{
	$parameters = array('authentication'=>$authentication,$invoice);
	dol_syslog("Call method ".$WS_METHOD0);
	$result0 = $soapclient0->call($WS_METHOD0,$parameters,$ns,'');
	if (! $result0)
	{
		print $soapclient0->error_str;
		print "<br>\n\n";
		print $soapclient0->request;
		print "<br>\n\n";
		print $soapclient0->response;
		exit;
	}
	else
	{
		$lGet=true;
	}
}


// Test url 1
if ($WS_METHOD1)
{
	$parameters = array('authentication'=>$authentication,'id'=>$fk,'ref'=>'');
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
	else
		$lGet=true;
}

// Test url 2
if ($WS_METHOD2)
{
	$parameters = array('authentication'=>$authentication,'idthirdparty'=>'4');
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


/*
 * View
 */
if ($WS_METHOD0)
{
	//$aProduct['products'] = $result0['products'];
	if ($lGet)
	{
		$id = $result0['id'];
		$ref = $result0['ref'];
		$result = array('id'=>$id,'ref'=>$ref,'error'=>$result0['result']['result_code']);
		$resultr[] = $result;
		$res['res'] = $resultr;
		echo json_encode($res);
	}
	else
		echo json_encode($result0);
}

if ($WS_METHOD1)
{
	//$aProduct['products'] = $result0['products'];
	if ($lGet)
	{
		$aOrder = $result1['order'];
		$id = $aOrder['id'];
		$status = $aOrder['status'];

		$result = array('id'=>$id,'ref'=>$ref,'status'=>$status,'error'=>$result1['result']['result_code']);
		$resultr[] = $result;
		$res['res'] = $resultr;


		echo json_encode($res);
	}
	else
		echo json_encode($result0);
}

/*
header("Content-type: text/html; charset=utf8");
print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">'."\n";
echo '<html>'."\n";
echo '<head>';
echo '<title>WebService Test: '.$WS_METHOD1.'</title>';
echo '</head>'."\n";

echo '<body>'."\n";
echo 'NUSOAP_PATH='.NUSOAP_PATH.'<br>';

echo "<h2>Request:</h2>";
echo '<h4>Function</h4>';
echo $WS_METHOD0;
echo '<h4>SOAP Message</h4>';
echo '<pre>' . htmlspecialchars($soapclient0->request, ENT_QUOTES) . '</pre>';
echo '<hr>';
echo "<h2>Response:</h2>";
echo '<h4>Result</h4>';
echo '<pre>';
print_r($result0);
echo '</pre>';
echo '<h4>SOAP Message</h4>';
echo '<pre>' . htmlspecialchars($soapclient0->response, ENT_QUOTES) . '</pre>';

print '<hr>';

echo "<h2>Request:</h2>";
echo '<h4>Function</h4>';
echo $WS_METHOD1;
echo '<h4>SOAP Message</h4>';
echo '<pre>' . htmlspecialchars($soapclient1->request, ENT_QUOTES) . '</pre>';
echo '<hr>';
echo "<h2>Response:</h2>";
echo '<h4>Result</h4>';
echo '<pre>';
print_r($result1);
echo '</pre>';
echo '<h4>SOAP Message</h4>';
echo '<pre>' . htmlspecialchars($soapclient1->response, ENT_QUOTES) . '</pre>';

print '<hr>';

echo "<h2>Request:</h2>";
echo '<h4>Function</h4>';
echo $WS_METHOD2;
echo '<h4>SOAP Message</h4>';
echo '<pre>' . htmlspecialchars($soapclient2->request, ENT_QUOTES) . '</pre>';
echo '<hr>';
echo "<h2>Response:</h2>";
echo '<h4>Result</h4>';
echo '<pre>';
print_r($result2);
echo '</pre>';
echo '<h4>SOAP Message</h4>';
echo '<pre>' . htmlspecialchars($soapclient2->response, ENT_QUOTES) . '</pre>';

echo '</body>'."\n";
echo '</html>'."\n";
*/
?>
