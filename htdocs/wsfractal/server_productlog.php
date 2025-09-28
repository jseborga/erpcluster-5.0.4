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
 *       \file       htdocs/webservices/server_productlog.php
 *       \brief      File that is entry point to call Dolibarr WebServices
 *       \version    $Id: server_productlog.php,v 1.7 2010/12/19 11:49:37 eldy Exp $
 */

// This is to make Dolibarr working with Plesk
set_include_path($_SERVER['DOCUMENT_ROOT'].'/htdocs');

require_once("../master.inc.php");
require_once(NUSOAP_PATH.'/nusoap.php');		// Include SOAP
require_once(DOL_DOCUMENT_ROOT."/core/lib/ws.lib.php");
require_once(DOL_DOCUMENT_ROOT."/wsfractal/class/productlog.class.php");


dol_syslog("Call Productlog webservices interfaces");

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
$server->configureWSDL('WebServicesDolibarrProductlog',$ns);
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
	'productlog',
	'complexType',
	'struct',
	'all',
	'',
	array(

		'lines' => array('name'=>'lines','type'=>'tns:LinesArray2'),
		'fk_product' => array('name'=>'fk_product','type'=>'xsd:string'),
		'description' => array('name'=>'description','type'=>'xsd:string'),
		'status_product' => array('name'=>'status_product','type'=>'xsd:string'),
		'fk_user_create' => array('name'=>'fk_user_create','type'=>'xsd:string'),
		'fk_user_mod' => array('name'=>'fk_user_mod','type'=>'xsd:string'),
		'datec' => array('name'=>'datec','type'=>'xsd:string'),
		'datem' => array('name'=>'datem','type'=>'xsd:string'),
		'tms' => array('name'=>'tms','type'=>'xsd:string'),
		'fk_user' => array('name'=>'fk_user','type'=>'xsd:string'),
		'status' => array('name'=>'status','type'=>'xsd:string')
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
	'getProductlog',
	// Entry values
	array('authentication'=>'tns:authentication','id'=>'xsd:string','ref'=>'xsd:string','ref_ext'=>'xsd:string'),
	// Exit values
	array('result'=>'tns:result','productlog'=>'tns:productlog'),
	$ns,
	$ns.'#getProductlog',
	$styledoc,
	$styleuse,
	'WS to get productlog'
);

// Register WSDL
$server->register(
	'verifProductlog',
	// Entry values
	array('authentication'=>'tns:authentication','status'=>'xsd:string','ref'=>'xsd:string','ref_ext'=>'xsd:string'),
	// Exit values
	array('result'=>'tns:result','productlog'=>'tns:productlog'),
	$ns,
	$ns.'#verifProductlog',
	$styledoc,
	$styleuse,
	'WS to verif productlog'
);

// Register WSDL
$server->register(
	'createProductlog',
	// Entry values
	array('authentication'=>'tns:authentication','productlog'=>'tns:productlog'),
	// Exit values
	array('result'=>'tns:result','id'=>'xsd:string'),
	$ns,
	$ns.'#createProductlog',
	$styledoc,
	$styleuse,
	'WS to create a productlog'
);

$server->wsdl->addComplexType(
	'LinesArray2',
	'complexType',
	'array',
	'sequence',
	'',
	array(
		'line' => array(
			'name' => 'line',
			'type' => 'tns:line',
			'minOccurs' => '0',
			'maxOccurs' => 'unbounded'
		)
	)
);
$line_fields = array(
	'id' => array('name'=>'id','type'=>'xsd:string'),
	'fk_product' => array('name'=>'fk_product','type'=>'xsd:string'),
	'description' => array('name'=>'description','type'=>'xsd:string')
);

// Define other specific objects
$server->wsdl->addComplexType(
	'line',
	'complexType',
	'struct',
	'all',
	'',
	$line_fields
);
/**
 * Get Productlog
 *
 * @param	array		$authentication		Array of authentication information
 * @param	int			$id					Id of object
 * @param	string		$ref				Ref of object
 * @param	string		$ref_ext			Ref external of object
 * @return	mixed
 */
function getProductlog($authentication,$id,$ref='',$ref_ext='')
{
	global $db,$conf,$langs;

	dol_syslog("Function: getProductlog login=".$authentication['login']." id=".$id." ref=".$ref." ref_ext=".$ref_ext);

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
			$productlog=new Productlog($db);
			$result=$productlog->fetch($id,$ref,$ref_ext);
			if ($result > 0)
			{

				$linesresp=array();
				$i=0;
				foreach($productlog->lines as $line)
				{
					var_dump($line); exit;
					$linesresp[]=array(
						'id' => $line->id,
						'fk_product'=>$line->fk_product,
						'description'=>$line->description,
					);
					$i++;
				}

				// Create
				$objectresp = array(
					'result'=>array('result_code'=>'OK', 'result_label'=>''),
					'productlog'=>array(

						'lines' => $linesresp,
						'fk_product' => $productlog->fk_product,
						'description' => $productlog->description,
						'status_product' => $productlog->status_product,
						'fk_user_create' => $productlog->fk_user_create,
						'fk_user_mod' => $productlog->fk_user_mod,
						'datec' => $productlog->datec,
						'datem' => $productlog->datem,
						'tms' => $productlog->tms,
						'status' => $productlog->status
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
 * Get Productlog
 *
 * @param	array		$authentication		Array of authentication information
 * @param	int			$id					Id of object
 * @param	string		$ref				Ref of object
 * @param	string		$ref_ext			Ref external of object
 * @return	mixed
 */
function verifProductlog($authentication,$status,$ref='',$ref_ext='')
{
	global $db,$conf,$langs;

	dol_syslog("Function: getProductlog login=".$authentication['login']." id=".$id." ref=".$ref." ref_ext=".$ref_ext);

	if ($authentication['entity']) $conf->entity=$authentication['entity'];

	// Init and check authentication
	$objectresp=array();
	$errorcode='';$errorlabel='';
	$error=0;
	$fuser=check_authentication($authentication,$error,$errorcode,$errorlabel);
	// Check parameters
	if (! $error && (($status && $ref) || ($status && $ref_ext) || ($ref && $ref_ext)))
	{
		$error++;
		$errorcode='BAD_PARAMETERS'; $errorlabel="Parameter id, ref and ref_ext can't be both provided. You must choose one or other but not both.";
	}

	if (! $error)
	{
		$fuser->getrights();

		if ($fuser->rights->wsfractal->read)
		{
			$productlog=new Productlog($db);
			//$userlog=new Userlog($db);
			$filter = " AND t.status_product>0";
			$result=$productlog->fetchAll('','',0,0,array(),'AND',$filter);
			if ($result > 0)
			{
				$linesresp=array();
				$i=0;
				$datec = '';
				foreach($productlog->lines as $line)
				{
					if ($datec <= $line->datec) $datec = $line->datec;
						//var_dump($line); exit;
					$linesresp[$i]=array(
						'id' => $line->id,
						'fk_product'=>$line->fk_product,
						'description'=>$line->description,
						'datec' => $line->datec,
					);
					$i++;
				}

				// Create
				$objectresp = array(
					'result'=>array('result_code'=>'OK', 'result_label'=>''),
					'productlog'=>array(

						'lines' => $linesresp,
						'fk_product' => 0,
						'description' => 'existe',
						'status_product' => 1,
						'fk_user_create' => $user->id,
						'fk_user_mod' => $user->id,
						'datec' => $datec,
						'datem' => dol_now(),
						'tms' => dol_now(),
						'fk_user' => $fuser->id,
						'status' => 1
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
 * Create Productlog
 *
 * @param	array		$authentication		Array of authentication information
 * @param	Productlog	$productlog		    $productlog
 * @return	array							Array result
 */
function createProductlog($authentication,$productlog)
{
	global $db,$conf,$langs;

	$now=dol_now();

	dol_syslog("Function: createProductlog login=".$authentication['login']);

	if ($authentication['entity']) $conf->entity=$authentication['entity'];

	// Init and check authentication
	$objectresp=array();
	$errorcode='';$errorlabel='';
	$error=0;
	$fuser=check_authentication($authentication,$error,$errorcode,$errorlabel);
	// Check parameters


	if (! $error)
	{
		$newobject=new Productlog($db);

		$newobject->lines=$productlog->lines;
		$newobject->fk_product=$productlog->fk_product;
		$newobject->description=$productlog->description;
		$newobject->status_product=$productlog->status_product;
		$newobject->fk_user_create=$productlog->fk_user_create;
		$newobject->fk_user_mod=$productlog->fk_user_mod;
		$newobject->datec=$productlog->datec;
		$newobject->datem=$productlog->datem;
		$newobject->tms=$productlog->tms;
		$newobject->status=$productlog->status;
		$newobject->id=$productlog->id;
		$newobject->import_key=$productlog->import_key;
		$newobject->array_options=$productlog->array_options;
		$newobject->linkedObjectsIds=$productlog->linkedObjectsIds;
		$newobject->linkedObjects=$productlog->linkedObjects;
		$newobject->context=$productlog->context;
		$newobject->canvas=$productlog->canvas;
		$newobject->project=$productlog->project;
		$newobject->fk_project=$productlog->fk_project;
		$newobject->projet=$productlog->projet;
		$newobject->contact=$productlog->contact;
		$newobject->contact_id=$productlog->contact_id;
		$newobject->thirdparty=$productlog->thirdparty;
		$newobject->user=$productlog->user;
		$newobject->origin=$productlog->origin;
		$newobject->origin_id=$productlog->origin_id;
		$newobject->ref=$productlog->ref;
		$newobject->ref_previous=$productlog->ref_previous;
		$newobject->ref_next=$productlog->ref_next;
		$newobject->ref_ext=$productlog->ref_ext;
		$newobject->statut=$productlog->statut;
		$newobject->country=$productlog->country;
		$newobject->country_id=$productlog->country_id;
		$newobject->country_code=$productlog->country_code;
		$newobject->barcode_type=$productlog->barcode_type;
		$newobject->barcode_type_code=$productlog->barcode_type_code;
		$newobject->barcode_type_label=$productlog->barcode_type_label;
		$newobject->barcode_type_coder=$productlog->barcode_type_coder;
		$newobject->mode_reglement_id=$productlog->mode_reglement_id;
		$newobject->cond_reglement_id=$productlog->cond_reglement_id;
		$newobject->cond_reglement=$productlog->cond_reglement;
		$newobject->fk_delivery_address=$productlog->fk_delivery_address;
		$newobject->shipping_method_id=$productlog->shipping_method_id;
		$newobject->modelpdf=$productlog->modelpdf;
		$newobject->fk_account=$productlog->fk_account;
		$newobject->note_public=$productlog->note_public;
		$newobject->note_private=$productlog->note_private;
		$newobject->note=$productlog->note;
		$newobject->total_ht=$productlog->total_ht;
		$newobject->total_tva=$productlog->total_tva;
		$newobject->total_localtax1=$productlog->total_localtax1;
		$newobject->total_localtax2=$productlog->total_localtax2;
		$newobject->total_ttc=$productlog->total_ttc;
		$newobject->fk_incoterms=$productlog->fk_incoterms;
		$newobject->libelle_incoterms=$productlog->libelle_incoterms;
		$newobject->location_incoterms=$productlog->location_incoterms;
		$newobject->name=$productlog->name;
		$newobject->lastname=$productlog->lastname;
		$newobject->firstname=$productlog->firstname;
		$newobject->civility_id=$productlog->civility_id;


		//...

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
