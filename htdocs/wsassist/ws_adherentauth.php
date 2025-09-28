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
require_once NUSOAP_PATH.'/nusoap.php';		// Include SOAP

$WS_DOL_URL = DOL_MAIN_URL_ROOT.'/wsassist/server_adherentauth.php';
//$WS_DOL_URL = 'http://localhost:8080/';	// To test with Soapui mock. If not a page, should end with /

//recibiendo valores
$nameservice = GETPOST('nameservice');
$login = GETPOST('login');
$pass = GETPOST('pass');
$res = GETPOST('res');
$fk_user = GETPOST('fk_user');
if ($res==1) $nameservice = 'createadherentauth';
if ($nameservice == 'createadherentauth') $WS_METHOD0 = 'createadherentauth';
else $WS_METHOD1 = $nameservice;
if ($res)
{
		$data = array(
		"id" => 1,
		"ref"=>$login,
		"fk_user" => $fk_user,
		"description" => $res,
		"status_product" => 1,
		"fk_user_create" => $fk_user,
		"fk_user_mod" => $fk_user,
		"datec" => dol_now(),
		"datem" => dol_now(),
		"tms" => dol_now(),
		"status" => 1
	);
}

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
	'sourceapplication'=>'wsassist',
	'login'=>$login,
	'password'=>$pass,
	'entity'=>'');


// Test url 1
if ($WS_METHOD0)
{
	$parameters = array('authentication'=>$authentication,$data);
	dol_syslog("Call method ".$WS_METHOD0);
	$result0 = $soapclient1->call($WS_METHOD0,$parameters,$ns,'');
	if (! $result0)
	{
		print $soapclient1->error_str;
		print "<br>\n\n";
		print $soapclient1->request;
		print "<br>\n\n";
		print $soapclient1->response;
		exit;
	}
}
if ($WS_METHOD1)
{
	$parameters = array('authentication'=>$authentication,$id,$login);
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

/*
 * View
 */

header("Content-type: text/html; charset=utf8");

if ($WS_METHOD0)
{
	$aResult['result'] = $result0['result']['result_code'];
	echo json_encode($aResult);
}
if ($WS_METHOD1)
{
	unset($aResult);
	if ($result1['result']['result_code']=='OK')
	{
		$aResult['result'][] = $result1['adherentauth'];
	}
	else
		$aResult['result'][] = $result1['result']['result_code'];
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
