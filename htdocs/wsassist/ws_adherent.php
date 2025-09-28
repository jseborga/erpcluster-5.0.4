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

$nameservice = GETPOST('nameservice');
$fk = GETPOST('fk');
$WS_DOL_URL = DOL_MAIN_URL_ROOT.'/wsassist/server_adherent.php';
//$WS_DOL_URL = 'http://localhost:8080/';	// To test with Soapui mock. If not a page, should end with /

$WS_METHOD0  = 'getadherent';

//$WS_METHOD2  = 'getOrdersForThirdParty';
$ns='http://www.dolibarr.org/ns/';

//recibiendo valores

$fk_user = GETPOST('fk_user');
$fk_soc = GETPOST('fk_soc');
$entity = GETPOST('entity');
$imei = GETPOST('imei');
$login = GETPOST('login');
$pass = GETPOST('pass');
$code_mobile=GETPOST('code_mobile');
$send = GETPOST('send');

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


// Test url 1
$lGet = false;
if ($WS_METHOD0)
{
	$parameters = array('authentication'=>$authentication,'id'=>$entity,'ref'=>'');
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
		$nreg = $result0['result']['nreg'];
		$result = array('adherent'=>$result0['result']['lines']);

		echo json_encode($result);
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
