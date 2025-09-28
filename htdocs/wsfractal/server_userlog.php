<?php
/* Copyright (C) 2006-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *       \file       htdocs/webservices/server_userlog.php
 *       \brief      File that is entry point to call Dolibarr WebServices
 *       \version    $Id: server_userlog.php,v 1.7 2010/12/19 11:49:37 eldy Exp $
 */

// This is to make Dolibarr working with Plesk
set_include_path($_SERVER['DOCUMENT_ROOT'].'/htdocs');

require_once("../master.inc.php");
require_once(NUSOAP_PATH.'/nusoap.php');		// Include SOAP
require_once(DOL_DOCUMENT_ROOT."/core/lib/ws.lib.php");
require_once(DOL_DOCUMENT_ROOT."/wsfractal/class/userlog.class.php");


dol_syslog("Call Userlog webservices interfaces");

// Enable and test if module web services is enabled
if (empty($conf->global->MAIN_MODULE_WEBSERVICES))
{
	$langs->load("admin");
	dol_syslog("Call Dolibarr webservices interfaces with module webservices disabled");
	print $langs->trans("WarningModuleNotActive",'WebServices').'.<br><br>';
	print $langs->trans("ToActivateModule");
	exit;
}

// Create the soap Object
$server = new nusoap_server();
$server->soap_defencoding='UTF-8';
$server->decode_utf8=false;
$ns='http://www.dolibarr.org/ns/';
$server->configureWSDL('WebServicesDolibarrUserlog',$ns);
$server->wsdl->schemaTargetNamespace=$ns;


// Define WSDL Authentication object
$server->wsdl->addComplexType(
	'authentication',
	'complexType',
	'struct',
	'all',
	'',
	array(
		'dolibarrkey' => array('name'=>'dolibarrkey','type'=>'xsd:string'),
		'sourceapplication' => array('name'=>'sourceapplication','type'=>'xsd:string'),
		'login' => array('name'=>'login','type'=>'xsd:string'),
		'password' => array('name'=>'password','type'=>'xsd:string'),
		'entity' => array('name'=>'entity','type'=>'xsd:string'),
	)
);

// Define WSDL Return object
$server->wsdl->addComplexType(
	'result',
	'complexType',
	'struct',
	'all',
	'',
	array(
		'result_code' => array('name'=>'result_code','type'=>'xsd:string'),
		'result_label' => array('name'=>'result_label','type'=>'xsd:string'),
	)
);

// Define other specific objects
$server->wsdl->addComplexType(
	'userlog',
	'complexType',
	'struct',
	'all',
	'',
	array(

		'lines' => array('name'=>'lines','type'=>'xsd:string'),
		'fk_user' => array('name'=>'fk_user','type'=>'xsd:string'),
		'description' => array('name'=>'description','type'=>'xsd:string'),
		'status_product' => array('name'=>'status_product','type'=>'xsd:string'),
		'fk_user_create' => array('name'=>'fk_user_create','type'=>'xsd:string'),
		'fk_user_mod' => array('name'=>'fk_user_mod','type'=>'xsd:string'),
		'datec' => array('name'=>'datec','type'=>'xsd:string'),
		'datem' => array('name'=>'datem','type'=>'xsd:string'),
		'tms' => array('name'=>'tms','type'=>'xsd:string'),
		'status' => array('name'=>'status','type'=>'xsd:string')

	//...
	)
);



// 5 styles: RPC/encoded, RPC/literal, Document/encoded (not WS-I compliant), Document/literal, Document/literal wrapped
// Style merely dictates how to translate a WSDL binding to a SOAP message. Nothing more. You can use either style with any programming model.
// http://www.ibm.com/developerworks/webservices/library/ws-whichwsdl/
$styledoc='rpc';       // rpc/document (document is an extend into SOAP 1.0 to support unstructured messages)
$styleuse='encoded';   // encoded/literal/literal wrapped
// Better choice is document/literal wrapped but literal wrapped not supported by nusoap.


// Register WSDL
$server->register(
	'getUserlog',
	// Entry values
	array('authentication'=>'tns:authentication','id'=>'xsd:string','ref'=>'xsd:string','ref_ext'=>'xsd:string'),
	// Exit values
	array('result'=>'tns:result','userlog'=>'tns:userlog'),
	$ns,
	$ns.'#getUserlog',
	$styledoc,
	$styleuse,
	'WS to get userlog'
);

// Register WSDL
$server->register(
	'createUserlog',
	// Entry values
	array('authentication'=>'tns:authentication','userlog'=>'tns:userlog'),
	// Exit values
	array('result'=>'tns:result','id'=>'xsd:string'),
	$ns,
	$ns.'#createUserlog',
	$styledoc,
	$styleuse,
	'WS to create a userlog'
);




/**
 * Get Userlog
 *
 * @param	array		$authentication		Array of authentication information
 * @param	int			$id					Id of object
 * @param	string		$ref				Ref of object
 * @param	string		$ref_ext			Ref external of object
 * @return	mixed
 */
function getUserlog($authentication,$id,$ref='',$ref_ext='')
{
	global $db,$conf,$langs;

	dol_syslog("Function: getUserlog login=".$authentication['login']." id=".$id." ref=".$ref." ref_ext=".$ref_ext);

	if ($authentication['entity']) $conf->entity=$authentication['entity'];

	// Init and check authentication
	$objectresp=array();
	$errorcode='';$errorlabel='';
	$error=0;
	$fuser=check_authentication($authentication,$error,$errorcode,$errorlabel);
	// Check parameters
	if (! $error && (($id && $ref) || ($id && $ref_ext) || ($ref && $ref_ext)))
	{
		$error++;
		$errorcode='BAD_PARAMETERS'; $errorlabel="Parameter id, ref and ref_ext can't be both provided. You must choose one or other but not both.";
	}

	if (! $error)
	{
		$fuser->getrights();

		if ($fuser->rights->wsfractal->read)
		{
			$userlog=new Userlog($db);
			$result=$userlog->fetch($id,$ref,$ref_ext);
			if ($result > 0)
			{
				// Create
				$objectresp = array(
					'result'=>array('result_code'=>'OK', 'result_label'=>''),
					'userlog'=>array(

						'lines' => $userlog->lines,
						'fk_user' => $userlog->fk_user,
						'description' => $userlog->description,
						'status_product' => $userlog->status_product,
						'fk_user_create' => $userlog->fk_user_create,
						'fk_user_mod' => $userlog->fk_user_mod,
						'datec' => $userlog->datec,
						'datem' => $userlog->datem,
						'tms' => $userlog->tms,
						'status' => $userlog->status,


					//...
					)
				);
			}
			else
			{
				$error++;
				$errorcode='NOT_FOUND'; $errorlabel='Object not found for id='.$id.' nor ref='.$ref.' nor ref_ext='.$ref_ext;
			}
		}
		else
		{
			$error++;
			$errorcode='PERMISSION_DENIED'; $errorlabel='User does not have permission for this request';
		}
	}

	if ($error)
	{
		$objectresp = array('result'=>array('result_code' => $errorcode, 'result_label' => $errorlabel));
	}

	return $objectresp;
}


/**
 * Create Userlog
 *
 * @param	array		$authentication		Array of authentication information
 * @param	Userlog	$userlog		    $userlog
 * @return	array							Array result
 */
function createUserlog($authentication,$userlog)
{
	global $db,$conf,$langs;

	$now=dol_now();

	dol_syslog("Function: createUserlog login=".$authentication['login']);

	if ($authentication['entity']) $conf->entity=$authentication['entity'];

	// Init and check authentication
	$objectresp=array();
	$errorcode='';$errorlabel='';
	$error=0;
	$fuser=check_authentication($authentication,$error,$errorcode,$errorlabel);
	// Check parameters


	if (! $error)
	{
		$newobject=new Userlog($db);

		$newobject->fk_user=$userlog['fk_user'];
		$newobject->description=$userlog['description'];
		$newobject->status_product=$userlog['status_product'];
		$newobject->fk_user_create=$userlog['fk_user_create'];
		$newobject->fk_user_mod=$userlog['fk_user_mod'];
		$newobject->datec=$userlog['datec'];
		$newobject->datem=$userlog['datem'];
		$newobject->tms=$userlog['tms'];
		$newobject->status=$userlog['status'];

		$db->begin();

		$result=$newobject->create($fuser);
		if ($result <= 0)
		{
			$error++;
		}

		if (! $error)
		{
			$db->commit();
			$objectresp=array('result'=>array('result_code'=>'OK', 'result_label'=>''),'id'=>$newobject->id,'ref'=>$newobject->ref);
		}
		else
		{
			$db->rollback();
			$error++;
			$errorcode='KO';
			$errorlabel=$newobject->error;
		}
	}

	if ($error)
	{
		$objectresp = array('result'=>array('result_code' => $errorcode, 'result_label' => $errorlabel));
	}

	return $objectresp;
}

// Return the results.
$server->service(file_get_contents("php://input"));
