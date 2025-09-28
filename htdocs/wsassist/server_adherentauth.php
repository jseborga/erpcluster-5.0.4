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
 *       \file       htdocs/webservices/server_adherentauth.php
 *       \brief      File that is entry point to call Dolibarr WebServices
 *       \version    $Id: server_adherentauth.php,v 1.7 2010/12/19 11:49:37 eldy Exp $
 */

// This is to make Dolibarr working with Plesk
set_include_path($_SERVER['DOCUMENT_ROOT'].'/htdocs');

require_once("../master.inc.php");
require_once(NUSOAP_PATH.'/nusoap.php');		// Include SOAP
require_once(DOL_DOCUMENT_ROOT."/core/lib/ws.lib.php");
require_once(DOL_DOCUMENT_ROOT."/wsassist/class/adherentauth.class.php");


dol_syslog("Call adherentauth webservices interfaces");

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
$server->configureWSDL('WebServicesDolibarradherentauth',$ns);
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
	'adherentauth',
	'complexType',
	'struct',
	'all',
	'',
	array(

		'lines' => array('name'=>'lines','type'=>'xsd:string'),
		'fk_adherent' => array('name'=>'fk_adherent','type'=>'xsd:string'),
		'fk_user' => array('name'=>'fk_user','type'=>'xsd:string'),
		'fk_property' => array('name'=>'fk_property','type'=>'xsd:string'),
		'code_mobile' => array('name'=>'code_mobile','type'=>'xsd:string'),
		'lastname' => array('name'=>'lastname','type'=>'xsd:string'),
		'firstname' => array('name'=>'firstname','type'=>'xsd:string'),
		'property' => array('name'=>'property','type'=>'xsd:string'),
		'entity' => array('name'=>'entity','type'=>'xsd:string'),
		'fk_user_create' => array('name'=>'fk_user_create','type'=>'xsd:string'),
		'fk_user_mod' => array('name'=>'fk_user_mod','type'=>'xsd:string'),
		'datec' => array('name'=>'datec','type'=>'xsd:string'),
		'datem' => array('name'=>'datem','type'=>'xsd:string'),
		'tms' => array('name'=>'tms','type'=>'xsd:string'),
		'status' => array('name'=>'status','type'=>'xsd:string'),
		'id' => array('name'=>'id','type'=>'xsd:string')
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
	'getadherentauth',
	// Entry values
	array('authentication'=>'tns:authentication','id'=>'xsd:string','ref'=>'xsd:string','ref_ext'=>'xsd:string'),
	// Exit values
	array('result'=>'tns:result','adherentauth'=>'tns:adherentauth'),
	$ns,
	$ns.'#getadherentauth',
	$styledoc,
	$styleuse,
	'WS to get adherentauth'
);

// Register WSDL
$server->register(
	'createadherentauth',
	// Entry values
	array('authentication'=>'tns:authentication','adherentauth'=>'tns:adherentauth'),
	// Exit values
	array('result'=>'tns:result','id'=>'xsd:string'),
	$ns,
	$ns.'#createadherentauth',
	$styledoc,
	$styleuse,
	'WS to create a adherentauth'
);




/**
 * Get adherentauth
 *
 * @param	array		$authentication		Array of authentication information
 * @param	int			$id					Id of object
 * @param	string		$ref				Ref of object
 * @param	string		$ref_ext			Ref external of object
 * @return	mixed
 */
function getadherentauth($authentication,$id,$ref='',$ref_ext='')
{
	global $db,$conf,$langs;

	dol_syslog("Function: getadherentauth login=".$authentication['login']." id=".$id." ref=".$ref." ref_ext=".$ref_ext);

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
		if ($fuser->rights->wsassist->read)
		{
			$adherentauth=new adherentauth($db);
			$result=$adherentauth->fetch($id,$fuser->fk_member,$ref_ext);
			if ($result > 0)
			{
				//recuperamos de la tabla m_property
				$property = '';
				if ($adherentauth->fk_property>0)
				{
					$sql = " SELECT rowid, ref, label";
					$sql.= " FROM ".MAIN_DB_PREFIX."m_property ";
					$sql.= " WHERE rowid = ".$adherentauth->fk_property;
					$resql = $db->query($sql);
					if ($resql)
					{
						$obj = $db->fetch_object($resql);
						$property = $obj->label;
					}
				}
				// Create
				$objectresp = array(
					'result'=>array('result_code'=>'OK', 'result_label'=>''),
					'adherentauth'=>array(

						'lines' => $adherentauth->lines,
						'fk_adherent' => $adherentauth->fk_adherent,
						'fk_user' => $fuser->id,
						'fk_property' => $adherentauth->fk_property,
						'code_mobile' => $adherentauth->code_mobile,
						'lastname' => $fuser->lastname,
						'firstname' => $fuser->firstname,
						'property' => $property,
						'entity' => $fuser->entity,
						'fk_user_create' => $adherentauth->fk_user_create,
						'fk_user_mod' => $adherentauth->fk_user_mod,
						'datec' => $adherentauth->datec,
						'datem' => $adherentauth->datem,
						'tms' => $adherentauth->tms,
						'status' => $adherentauth->status,
						'id' => $adherentauth->id,
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
 * Create adherentauth
 *
 * @param	array		$authentication		Array of authentication information
 * @param	adherentauth	$adherentauth		    $adherentauth
 * @return	array							Array result
 */
function createadherentauth($authentication,$adherentauth)
{
	global $db,$conf,$langs;

	$now=dol_now();

	dol_syslog("Function: createadherentauth login=".$authentication['login']);

	if ($authentication['entity']) $conf->entity=$authentication['entity'];

	// Init and check authentication
	$objectresp=array();
	$errorcode='';$errorlabel='';
	$error=0;
	$fuser=check_authentication($authentication,$error,$errorcode,$errorlabel);
	// Check parameters


	if (! $error)
	{
		$newobject=new adherentauth($db);

		$newobject->lines=$adherentauth->lines;
		$newobject->fk_adherent=$adherentauth->fk_adherent;
		$newobject->fk_property=$adherentauth->fk_property;
		$newobject->code_mobile=$adherentauth->code_mobile;
		$newobject->fk_user_create=$adherentauth->fk_user_create;
		$newobject->fk_user_mod=$adherentauth->fk_user_mod;
		$newobject->datec=$adherentauth->datec;
		$newobject->datem=$adherentauth->datem;
		$newobject->tms=$adherentauth->tms;
		$newobject->status=$adherentauth->status;
		$newobject->id=$adherentauth->id;
		$newobject->import_key=$adherentauth->import_key;
		$newobject->array_options=$adherentauth->array_options;
		$newobject->linkedObjectsIds=$adherentauth->linkedObjectsIds;
		$newobject->linkedObjects=$adherentauth->linkedObjects;
		$newobject->context=$adherentauth->context;
		$newobject->canvas=$adherentauth->canvas;
		$newobject->project=$adherentauth->project;
		$newobject->fk_project=$adherentauth->fk_project;
		$newobject->projet=$adherentauth->projet;
		$newobject->contact=$adherentauth->contact;
		$newobject->contact_id=$adherentauth->contact_id;
		$newobject->thirdparty=$adherentauth->thirdparty;
		$newobject->user=$adherentauth->user;
		$newobject->origin=$adherentauth->origin;
		$newobject->origin_id=$adherentauth->origin_id;
		$newobject->ref=$adherentauth->ref;
		$newobject->ref_previous=$adherentauth->ref_previous;
		$newobject->ref_next=$adherentauth->ref_next;
		$newobject->ref_ext=$adherentauth->ref_ext;
		$newobject->statut=$adherentauth->statut;
		$newobject->country=$adherentauth->country;
		$newobject->country_id=$adherentauth->country_id;
		$newobject->country_code=$adherentauth->country_code;
		$newobject->barcode_type=$adherentauth->barcode_type;
		$newobject->barcode_type_code=$adherentauth->barcode_type_code;
		$newobject->barcode_type_label=$adherentauth->barcode_type_label;
		$newobject->barcode_type_coder=$adherentauth->barcode_type_coder;
		$newobject->mode_reglement_id=$adherentauth->mode_reglement_id;
		$newobject->cond_reglement_id=$adherentauth->cond_reglement_id;
		$newobject->cond_reglement=$adherentauth->cond_reglement;
		$newobject->fk_delivery_address=$adherentauth->fk_delivery_address;
		$newobject->shipping_method_id=$adherentauth->shipping_method_id;
		$newobject->modelpdf=$adherentauth->modelpdf;
		$newobject->fk_account=$adherentauth->fk_account;
		$newobject->note_public=$adherentauth->note_public;
		$newobject->note_private=$adherentauth->note_private;
		$newobject->note=$adherentauth->note;
		$newobject->total_ht=$adherentauth->total_ht;
		$newobject->total_tva=$adherentauth->total_tva;
		$newobject->total_localtax1=$adherentauth->total_localtax1;
		$newobject->total_localtax2=$adherentauth->total_localtax2;
		$newobject->total_ttc=$adherentauth->total_ttc;
		$newobject->fk_incoterms=$adherentauth->fk_incoterms;
		$newobject->libelle_incoterms=$adherentauth->libelle_incoterms;
		$newobject->location_incoterms=$adherentauth->location_incoterms;
		$newobject->name=$adherentauth->name;
		$newobject->lastname=$adherentauth->lastname;
		$newobject->firstname=$adherentauth->firstname;
		$newobject->civility_id=$adherentauth->civility_id;


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
