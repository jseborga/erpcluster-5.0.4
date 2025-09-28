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
require_once NUSOAP_PATH.'/nusoap.php';     // Include SOAP

$WS_DOL_URL = DOL_MAIN_URL_ROOT.'/wsfractal/server_user.php';
//$WS_DOL_URL = 'http://localhost:8080/';   // To test with Soapui mock. If not a page, should end with /
$WS_METHOD0  = 'setUserPassword';
$WS_METHOD1  = 'getUser';
//$WS_METHOD2  = 'getOrdersForThirdParty';
$ns='http://www.dolibarr.org/ns/';

//recibimos valores
$login = GETPOST('login');
$pass = GETPOST('pass');

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

// Call the WebService method and store its result in $result.
$authentication=array(
	'dolibarrkey'=>$conf->global->WEBSERVICES_KEY,
	'sourceapplication'=>'DEMO',
	'login'=>$login,
	'password'=>$pass,
	'entity'=>'1');
$shortuser=array('login'=>$login,'password'=>$pass);
// Test url 1
$lGet = false;
if ($WS_METHOD0)
{
	$parameters = array('authentication'=>$authentication,$shortuser);
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
		$id = $result0['id'];
		$parameters = array('authentication'=>$authentication,$id);
		dol_syslog("Call method ".$WS_METHOD0);
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
		{

		}

	}
}

/*
 * View
 */

//header("Content-type: text/html; charset=utf8");
//print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">'."\n";
if ($WS_METHOD0)
{
	//$aProduct['products'] = $result0['products'];
	if ($lGet)
	{
		$result1a[] = $result1['user'];
		$res['user'] = $result1a;
		echo json_encode($res);
	}
	else
		echo json_encode($result0);
}

?>
