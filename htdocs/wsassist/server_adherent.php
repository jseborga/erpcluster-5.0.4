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
 *       \file       htdocs/webservices/server_adherent.php
 *       \brief      File that is entry point to call Dolibarr WebServices
 *       \version    $Id: server_adherent.php,v 1.7 2010/12/19 11:49:37 eldy Exp $
 */

// This is to make Dolibarr working with Plesk
set_include_path($_SERVER['DOCUMENT_ROOT'].'/htdocs');

require_once("../master.inc.php");
require_once(NUSOAP_PATH.'/nusoap.php');		// Include SOAP
require_once(DOL_DOCUMENT_ROOT."/core/lib/ws.lib.php");
require_once(DOL_DOCUMENT_ROOT."/assistance/class/adherentext.class.php");


dol_syslog("Call adherent webservices interfaces");

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
$server->configureWSDL('WebServicesDolibarradherent',$ns);
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
		'lines' => array('name'=>'lines','type'=>'tns:LinesArray2'),
	)
);

// Define other specific objects
$server->wsdl->addComplexType(
	'adherent',
	'complexType',
	'struct',
	'all',
	'',
	array(

		'lines' => array('name'=>'lines','type'=>'xsd:string'),
		'entity' => array('name'=>'entity','type'=>'xsd:string'),
		'ref_ext' => array('name'=>'ref_ext','type'=>'xsd:string'),
		'civility' => array('name'=>'civility','type'=>'xsd:string'),
		'lastname' => array('name'=>'lastname','type'=>'xsd:string'),
		'lastnametwo' => array('name'=>'lastnametwo','type'=>'xsd:string'),
		'firstname' => array('name'=>'firstname','type'=>'xsd:string'),
		'docum' => array('name'=>'docum','type'=>'xsd:string'),
		'login' => array('name'=>'login','type'=>'xsd:string'),
		'pass' => array('name'=>'pass','type'=>'xsd:string'),
		'pass_crypted' => array('name'=>'pass_crypted','type'=>'xsd:string'),
		'fk_adherent_type' => array('name'=>'fk_adherent_type','type'=>'xsd:string'),
		'morphy' => array('name'=>'morphy','type'=>'xsd:string'),
		'societe' => array('name'=>'societe','type'=>'xsd:string'),
		'fk_soc' => array('name'=>'fk_soc','type'=>'xsd:string'),
		'address' => array('name'=>'address','type'=>'xsd:string'),
		'zip' => array('name'=>'zip','type'=>'xsd:string'),
		'town' => array('name'=>'town','type'=>'xsd:string'),
		'state_id' => array('name'=>'state_id','type'=>'xsd:string'),
		'country' => array('name'=>'country','type'=>'xsd:string'),
		'email' => array('name'=>'email','type'=>'xsd:string'),
		'skype' => array('name'=>'skype','type'=>'xsd:string'),
		'phone' => array('name'=>'phone','type'=>'xsd:string'),
		'phone_perso' => array('name'=>'phone_perso','type'=>'xsd:string'),
		'phone_mobile' => array('name'=>'phone_mobile','type'=>'xsd:string'),
		'birth' => array('name'=>'birth','type'=>'xsd:string'),
		'photo' => array('name'=>'photo','type'=>'xsd:string'),
		'statut' => array('name'=>'statut','type'=>'xsd:string'),
		'public' => array('name'=>'public','type'=>'xsd:string'),
		'datefin' => array('name'=>'datefin','type'=>'xsd:string'),
		'note_private' => array('name'=>'note_private','type'=>'xsd:string'),
		'note_public' => array('name'=>'note_public','type'=>'xsd:string'),
		'model_pdf' => array('name'=>'model_pdf','type'=>'xsd:string'),
		'datevalid' => array('name'=>'datevalid','type'=>'xsd:string'),
		'datec' => array('name'=>'datec','type'=>'xsd:string'),
		'tms' => array('name'=>'tms','type'=>'xsd:string'),
		'fk_user_author' => array('name'=>'fk_user_author','type'=>'xsd:string'),
		'fk_user_mod' => array('name'=>'fk_user_mod','type'=>'xsd:string'),
		'fk_user_valid' => array('name'=>'fk_user_valid','type'=>'xsd:string'),
		'canvas' => array('name'=>'canvas','type'=>'xsd:string'),
		'import_key' => array('name'=>'import_key','type'=>'xsd:string'),
		'id' => array('name'=>'id','type'=>'xsd:string'),

	)
);

// Define other specific objects
$server->wsdl->addComplexType(
	'line',
	'complexType',
	'struct',
	'all',
	'',
	array(

		'entity' => array('name'=>'entity','type'=>'xsd:string'),
		'ref_ext' => array('name'=>'ref_ext','type'=>'xsd:string'),
		'civility' => array('name'=>'civility','type'=>'xsd:string'),
		'lastname' => array('name'=>'lastname','type'=>'xsd:string'),
		'lastnametwo' => array('name'=>'lastnametwo','type'=>'xsd:string'),
		'firstname' => array('name'=>'firstname','type'=>'xsd:string'),
		'docum' => array('name'=>'docum','type'=>'xsd:string'),
		'login' => array('name'=>'login','type'=>'xsd:string'),
		'pass' => array('name'=>'pass','type'=>'xsd:string'),
		'pass_crypted' => array('name'=>'pass_crypted','type'=>'xsd:string'),
		'fk_adherent_type' => array('name'=>'fk_adherent_type','type'=>'xsd:string'),
		'morphy' => array('name'=>'morphy','type'=>'xsd:string'),
		'societe' => array('name'=>'societe','type'=>'xsd:string'),
		'fk_soc' => array('name'=>'fk_soc','type'=>'xsd:string'),
		'address' => array('name'=>'address','type'=>'xsd:string'),
		'zip' => array('name'=>'zip','type'=>'xsd:string'),
		'town' => array('name'=>'town','type'=>'xsd:string'),
		'state_id' => array('name'=>'state_id','type'=>'xsd:string'),
		'country' => array('name'=>'country','type'=>'xsd:string'),
		'email' => array('name'=>'email','type'=>'xsd:string'),
		'skype' => array('name'=>'skype','type'=>'xsd:string'),
		'phone' => array('name'=>'phone','type'=>'xsd:string'),
		'phone_perso' => array('name'=>'phone_perso','type'=>'xsd:string'),
		'phone_mobile' => array('name'=>'phone_mobile','type'=>'xsd:string'),
		'birth' => array('name'=>'birth','type'=>'xsd:string'),
		'photo' => array('name'=>'photo','type'=>'xsd:string'),
		'statut' => array('name'=>'statut','type'=>'xsd:string'),
		'public' => array('name'=>'public','type'=>'xsd:string'),
		'datefin' => array('name'=>'datefin','type'=>'xsd:string'),
		'note_private' => array('name'=>'note_private','type'=>'xsd:string'),
		'note_public' => array('name'=>'note_public','type'=>'xsd:string'),
		'model_pdf' => array('name'=>'model_pdf','type'=>'xsd:string'),
		'datevalid' => array('name'=>'datevalid','type'=>'xsd:string'),
		'datec' => array('name'=>'datec','type'=>'xsd:string'),
		'tms' => array('name'=>'tms','type'=>'xsd:string'),
		'fk_user_author' => array('name'=>'fk_user_author','type'=>'xsd:string'),
		'fk_user_mod' => array('name'=>'fk_user_mod','type'=>'xsd:string'),
		'fk_user_valid' => array('name'=>'fk_user_valid','type'=>'xsd:string'),
		'canvas' => array('name'=>'canvas','type'=>'xsd:string'),
		'import_key' => array('name'=>'import_key','type'=>'xsd:string'),
		'id' => array('name'=>'id','type'=>'xsd:string')

	)
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
	),
	null,
	'tns:line'
);


// 5 styles: RPC/encoded, RPC/literal, Document/encoded (not WS-I compliant), Document/literal, Document/literal wrapped
// Style merely dictates how to translate a WSDL binding to a SOAP message. Nothing more. You can use either style with any programming model.
// http://www.ibm.com/developerworks/webservices/library/ws-whichwsdl/
$styledoc='rpc';       // rpc/document (document is an extend into SOAP 1.0 to support unstructured messages)
$styleuse='encoded';   // encoded/literal/literal wrapped
// Better choice is document/literal wrapped but literal wrapped not supported by nusoap.


// Register WSDL
$server->register(
	'getadherent',
	// Entry values
	array('authentication'=>'tns:authentication','id'=>'xsd:string','ref'=>'xsd:string','ref_ext'=>'xsd:string'),
	// Exit values
	array('result'=>'tns:result','adherent'=>'tns:adherent'),
	$ns,
	$ns.'#getadherent',
	$styledoc,
	$styleuse,
	'WS to get adherent'
);

// Register WSDL
$server->register(
	'createadherent',
	// Entry values
	array('authentication'=>'tns:authentication','adherent'=>'tns:adherent'),
	// Exit values
	array('result'=>'tns:result','id'=>'xsd:string'),
	$ns,
	$ns.'#createadherent',
	$styledoc,
	$styleuse,
	'WS to create a adherent'
);




/**
 * Get adherent
 *
 * @param	array		$authentication		Array of authentication information
 * @param	int			$id					Id of object
 * @param	string		$ref				Ref of object
 * @param	string		$ref_ext			Ref external of object
 * @return	mixed
 */
function getadherent($authentication,$id,$ref='',$ref_ext='')
{
	global $db,$conf,$langs;

	dol_syslog("Function: getadherent login=".$authentication['login']." id=".$id." ref=".$ref." ref_ext=".$ref_ext);

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
			$objAdherent=new adherentext($db);
			$filter = " AND t.entity = ".$id;
			$result=$objAdherent->fetchAll('','',0,0,array(),'AND',$filter);
			if ($result > 0)
			{
				$lines = $objAdherent->lines;
				foreach ($lines AS $j => $line)
				{
					$linesresp[] = array(
						"id" => $line->id,
						"entity" => $line->entity,
						"ref_ext" => $line->ref_ext,
						"civility" => $line->civility,
						"lastname" => $line->lastname,
						"lastnametwo" => $line->plastnametwo,
						"firstname" => $line->firstname,
						"docum" => $line->docum,
						"login" => $line->login,
						"pass" => $line->pass,
						"pass_crypted" => $line->pass_crypted,
						"fk_adherent_type" => $line->fk_adherent_type,
						"morphy" => $line->morphy,
						"societe" => $line->societe,
						"fk_soc" => $line->fk_soc,
						"address" => $line->address,
						"zip" => $line->zip,
						"town" => $line->town,
						"state_id" => $line->state_id,
						"country" => $line->country,
						"email" => $line->email,
						"skype" => $line->skype,
						"phone" => $line->phone,
						"phone_perso" => $line->phone_perso,
						"phone_mobile" => $line->phone_mobile,
						"birth" => $db->jdate($line->birth),
						"photo" => $line->photo,
						"statut" => $line->statut,
						"public" => $line->public,
						"datefin" => $db->jdate($line->datefin),
						"note_private" => $line->note_private,
						"note_public" => $line->note_public,
						"model_pdf" => $line->model_pdf,
						"datevalid" => $db->jdate($line->datevalid),
						"datec" => $db->jdate($line->datec),
						"tms" => $db->jdate($line->tms),
						"fk_user_author" => $line->fk_user_author,
						"fk_user_mod" => $line->fk_user_mod,
						"fk_user_valid" => $line->fk_user_valid,
						"canvas" => $line->canvas,
						"import_key" => $line->import_key
					);
				}

				$objectresp = array('result'=>array('result_code'=>'OK', 'result_label'=>'','lines'=>$linesresp));
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
 * Create adherent
 *
 * @param	array		$authentication		Array of authentication information
 * @param	adherent	$adherent		    $adherent
 * @return	array							Array result
 */
function createadherent($authentication,$adherent)
{
	global $db,$conf,$langs;

	$now=dol_now();

	dol_syslog("Function: createadherent login=".$authentication['login']);

	if ($authentication['entity']) $conf->entity=$authentication['entity'];

	// Init and check authentication
	$objectresp=array();
	$errorcode='';$errorlabel='';
	$error=0;
	$fuser=check_authentication($authentication,$error,$errorcode,$errorlabel);
	// Check parameters


	if (! $error)
	{
		$newobject=new adherent($db);

		$newobject->lines=$adherent->lines;
		$newobject->entity=$adherent->entity;
		$newobject->ref_ext=$adherent->ref_ext;
		$newobject->civility=$adherent->civility;
		$newobject->lastname=$adherent->lastname;
		$newobject->firstname=$adherent->firstname;
		$newobject->login=$adherent->login;
		$newobject->pass=$adherent->pass;
		$newobject->pass_crypted=$adherent->pass_crypted;
		$newobject->fk_adherent_type=$adherent->fk_adherent_type;
		$newobject->morphy=$adherent->morphy;
		$newobject->societe=$adherent->societe;
		$newobject->fk_soc=$adherent->fk_soc;
		$newobject->address=$adherent->address;
		$newobject->zip=$adherent->zip;
		$newobject->town=$adherent->town;
		$newobject->state_id=$adherent->state_id;
		$newobject->country=$adherent->country;
		$newobject->email=$adherent->email;
		$newobject->skype=$adherent->skype;
		$newobject->phone=$adherent->phone;
		$newobject->phone_perso=$adherent->phone_perso;
		$newobject->phone_mobile=$adherent->phone_mobile;
		$newobject->birth=$adherent->birth;
		$newobject->photo=$adherent->photo;
		$newobject->statut=$adherent->statut;
		$newobject->public=$adherent->public;
		$newobject->datefin=$adherent->datefin;
		$newobject->note_private=$adherent->note_private;
		$newobject->note_public=$adherent->note_public;
		$newobject->model_pdf=$adherent->model_pdf;
		$newobject->datevalid=$adherent->datevalid;
		$newobject->datec=$adherent->datec;
		$newobject->tms=$adherent->tms;
		$newobject->fk_user_author=$adherent->fk_user_author;
		$newobject->fk_user_mod=$adherent->fk_user_mod;
		$newobject->fk_user_valid=$adherent->fk_user_valid;
		$newobject->canvas=$adherent->canvas;
		$newobject->import_key=$adherent->import_key;
		$newobject->id=$adherent->id;
		$newobject->array_options=$adherent->array_options;
		$newobject->linkedObjectsIds=$adherent->linkedObjectsIds;
		$newobject->linkedObjects=$adherent->linkedObjects;
		$newobject->context=$adherent->context;
		$newobject->project=$adherent->project;
		$newobject->fk_project=$adherent->fk_project;
		$newobject->projet=$adherent->projet;
		$newobject->contact=$adherent->contact;
		$newobject->contact_id=$adherent->contact_id;
		$newobject->thirdparty=$adherent->thirdparty;
		$newobject->user=$adherent->user;
		$newobject->origin=$adherent->origin;
		$newobject->origin_id=$adherent->origin_id;
		$newobject->ref=$adherent->ref;
		$newobject->ref_previous=$adherent->ref_previous;
		$newobject->ref_next=$adherent->ref_next;
		$newobject->country_id=$adherent->country_id;
		$newobject->country_code=$adherent->country_code;
		$newobject->barcode_type=$adherent->barcode_type;
		$newobject->barcode_type_code=$adherent->barcode_type_code;
		$newobject->barcode_type_label=$adherent->barcode_type_label;
		$newobject->barcode_type_coder=$adherent->barcode_type_coder;
		$newobject->mode_reglement_id=$adherent->mode_reglement_id;
		$newobject->cond_reglement_id=$adherent->cond_reglement_id;
		$newobject->cond_reglement=$adherent->cond_reglement;
		$newobject->fk_delivery_address=$adherent->fk_delivery_address;
		$newobject->shipping_method_id=$adherent->shipping_method_id;
		$newobject->modelpdf=$adherent->modelpdf;
		$newobject->fk_account=$adherent->fk_account;
		$newobject->note=$adherent->note;
		$newobject->total_ht=$adherent->total_ht;
		$newobject->total_tva=$adherent->total_tva;
		$newobject->total_localtax1=$adherent->total_localtax1;
		$newobject->total_localtax2=$adherent->total_localtax2;
		$newobject->total_ttc=$adherent->total_ttc;
		$newobject->fk_incoterms=$adherent->fk_incoterms;
		$newobject->libelle_incoterms=$adherent->libelle_incoterms;
		$newobject->location_incoterms=$adherent->location_incoterms;
		$newobject->name=$adherent->name;
		$newobject->civility_id=$adherent->civility_id;


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
