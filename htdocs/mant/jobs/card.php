<?php
/* Copyright (C) 2014-2014 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *	\file       htdocs/mant/jobs/card.php
 *	\ingroup    Ordenes de Trabajo
*	\brief      Page fiche mantenimiento
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mworkrequestext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mworkrequestcontact.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mworkrequestuser.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/contactext.class.php';

require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobscontactext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsuserext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mtyperepair.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsorderext.class.php';

require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsmaterialusedext.class.php';


if ($conf->orgman->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/pcharge.class.php';
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/mproperty.class.php';
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/mlocation.class.php';
}
require_once DOL_DOCUMENT_ROOT.'/mant/class/mequipmentext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/html.formadd.class.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

if ($conf->assets->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/assets/class/form_assets.class.php';
}
require_once DOL_DOCUMENT_ROOT.'/mant/lib/mant.lib.php';
require_once DOL_DOCUMENT_ROOT.'/mant/lib/adherent.lib.php';
require_once DOL_DOCUMENT_ROOT.'/mant/lib/societe.lib.php';
require_once DOL_DOCUMENT_ROOT.'/mant/lib/user.lib.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';


//require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/emailing.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';
require_once DOL_DOCUMENT_ROOT.'/comm/mailing/class/mailing.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';

$langs->load("mant");
$langs->load("companies");
$langs->load("commercial");
$langs->load("bills");
$langs->load("banks");
$langs->load("users");
$langs->load("other");

$action=GETPOST('action');

$id  = GETPOST("id"); //jobs
$ref = GETPOST('ref','alpha');
$idw = GETPOST('idw');
$idu = GETPOST('idu');
$fk_equipment = GETPOST('fk_equipment','int');
$order_number = GETPOST('order_number');
$date_order = GETPOST('date_order');
$fk_equipment = GETPOST('fk_equipment');

if (! empty($user->societe_id)) $socid=$user->societe_id;
$url = $dolibarr_main_url_root;

// $sortfield = GETPOST("sortfield");
// $sortorder = GETPOST("sortorder");

// if (! $sortfield) $sortfield="p.period_month";
// if (! $sortorder) $sortorder="DESC";

$mesg = '';
$tmparray = array();

$object      = new Mjobsext($db);
$objwork     = new Mworkrequestext($db);
$objProperty = new Mproperty($db);
$objLocation = new Mlocation($db);
$objSoc      = new Societe($db);
$objEquipment= new Mequipmentext($db);
$objUser     = new User($db);
//$objJobsuser = new Mjobsuser($db);
$objcomm     = new Commande($db);
$objContact = new Contactext($db);
$objWorkuser = new Mworkrequestuser($db);
$objWorkcont = new Mworkrequestcontact($db);

$objJobuser = new Mjobsuserext($db);
$objJobscontact = new Mjobscontactext($db);
$objAdherent = new Adherent($db);

$objCharge = new Pcharge($db);
$objDepartament = new Pdepartamentext($db);
$objTyperepair = new Mtyperepair($db);

if ($conf->assets->enabled)
	$objassets = new Assetsext($db);

if ($id) $object->fetch($id);

/*
 * Actions
 */
			// Confirmation departament assign
if ($action == 'confirm_asigndep' && $_REQUEST['confirm'] == 'yes')
{
		// on verifie si l'objet est en numerotation provisoire
		//$object = new Solalmacen($db);
	$object->fetch(GETPOST('id'));
		//cambiando a validado
		//cambiando a programado
	$object->fk_soc = -1;
	if ($object->status == 1) $object->status = 2;

		//update
	$res = $object->update($user);
	if ($res<=0)
	{
		setEventMessages($object->error,$object->errors,'errors');
	}
	else
	{
		setEventMessages($langs->trans('Succesfullupdate'),null,'mesgs');
		header('Location: '.$_SERVER['PHP_SELF'].'?id='.$object->id);
		exit;
	}
	$action = '';
}
			// Confirmation de la validation
if ($action == 'confirm_asignloc' && $_REQUEST['confirm'] == 'yes')
{
				// on verifie si l'objet est en numerotation provisoire
				//$object = new Solalmacen($db);
	$object->fetch(GETPOST('id'));
		//cambiando a validado
		//cambiando a programado
	$object->fk_soc = -1;
	$object->date_assign = dol_now();
	if ($object->status == 1) $object->status = 2;

		//update
	$res = $object->update($user);
	if ($res<=0)
	{
		setEventMessages($object->error,$object->errors,'errors');
	}
	else
	{
		setEventMessages($langs->trans('Succesfullupdate'),null,'mesgs');
		header('Location: '.$_SERVER['PHP_SELF'].'?id='.$object->id);
		exit;
	}
	$action = '';
}


			// Confirmation de la validation
if ($action == 'confirm_validate' && $_REQUEST['confirm'] == 'yes')
{
				// on verifie si l'objet est en numerotation provisoire
	$ref = substr($object->ref, 1, 4);
	if ($ref == 'PROV')
	{
		$numref = $object->getNextNumRef($object);
	}
	else
	{
		$numref = $object->ref;
	}

				//$object = new Solalmacen($db);
	$object->fetch(GETPOST('id'));
				//cambiando a validado
	$object->ref = $numref;
				//cambiando a programado
	if (empty($object->status)) $object->status = 1;
	elseif ($object->status == 1) $object->status = 2;
	elseif ($object->status == 2) $object->status = 3;

				//update
	$res = $object->update($user);
	if ($res<=0)
	{
		setEventMessages($object->error,$object->errors,'errors');
	}
	else
	{
		setEventMessages($langs->trans('Succesfullupdate'),null,'mesgs');
		header('Location: '.$_SERVER['PHP_SELF'].'?id='.$object->id);
		exit;
	}
	$action = '';
}


// refusesend
if ($action == 'refusesend' && $user->rights->mant->jobs->rech)
{
	$object->fetch($id);

	$object->description_job = GETPOST('description_job','alpha');
	$object->status = 9;
	$object->tms = dol_now();

	if (!empty($object->description_job))
	{
		$db->begin();
		$object->update($user);

		$emailto = $object->email;
		if ($object->email)
		{
			//sendmail
			// Define output language
			$outputlangs = $langs;
			$newlang='';
			if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang=GETPOST('lang_id');
			if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$object->client->default_lang;
			if (! empty($newlang))
			{
				$outputlangs = new Translate("",$conf);
				$outputlangs->setDefaultLang($newlang);
			}

			$arr_file = array();
			$arr_mime = array();
			$arr_name = array();
			$arr_mime[] = 'text/html';
		  //$arr_mime[] = 'aplication/rtf';

			$tmpsujet = $langs->trans('Send email request');
			$sendto   = $emailto;
			$email_from = $conf->global->MAIN_MAIL_EMAIL_FROM;
			$tmpbody = htmlsendemailrech($id,$object->description_job,$url);
			$msgishtml = 1;
			$email_errorsto = $conf->global->MAIN_MAIL_ERRORS_TO;
			$arr_css = array('bgcolor' => '#FFFFCC');
			$mailfile = new CMailFile($tmpsujet,$sendto,$email_from,$tmpbody, $arr_file,$arr_mime,$arr_name,'', '', 0, $msgishtml,$email_errorsto,$arr_css);
			if ($conf->global->MANT_SEND_EMAIL)
				$result=$mailfile->sendfile();
			else
				$result = 1;
			if ($result)
			{
				$mesg='<div class="ok">'.
				$langs->trans("MailSuccessfulySent",
					$mailfile->getValidAddress($object->email_from,2),
					$mailfile->getValidAddress($object->sendto,2)).
				'</div>';
			}
			else
			{
				$error++;
				$mesg='<div class="error">'.$langs->trans("ResultKo").
				'<br>'.$mailfile->error.' '.$result.'</div>';
				$action = 'create';
			}
		}
		else
		{
			$action = '';
			$mesg='<div class="error">'.$object->error.'</div>';
		}
		if (empty($error))
		{
			$db->commit();
			header("Location: list.php");
			exit;
		}
		else
			$db->rollback();
	}
	else
	{
		if (empty($error))
			$mesg = '<div class="error">'.$langs->trans("Error, registre su email y el numero interno, gracias.").'</div>';
		$action = '';
	}
}

// update
if ($action == 'updateassign' && $_POST["cancel"] <> $langs->trans("Cancel") && $user->rights->mant->tick->ass )
{
	if ($object->fetch($id) > 0)
	{
		$error=0;
		$object->fk_soc = GETPOST('fk_soc');
		$object->status = 2;
		//estado asignado a empresa
		$object->datem 	= dol_now();
		$object->tms 	= dol_now();
		if (empty($object->fk_soc))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans('Error, company is required').'</div>';
		}
		if (empty($error))
		{
			$db->begin();
			$res = $object->update($user);
			if ($res > 0)
			{
				$db->commit();
				setEventMessages($langs->trans('Succesfullupdate'),null,'mesgs');
				header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
				exit;
			}
			$db->rollback();
			$action = 'asignsoc';
			$mesg='<div class="error">'.$object->error.'</div>';
		}
		else
		{
			$action="asignsoc";
		// Force retour sur page creation
		}
	}
}


// Add
if ($action == 'add' && $user->rights->mant->jobs->crear)
{
	$error=0;
	$reswork = $objwork->fetch($idw);
	$code= generarcodigo(4);
	$object->address_ip     = $_SERVER['REMOTE_ADDR'];
	$object->ref            = '(PROV)'.$code;
	$object->group_task     = GETPOST('group_task');
	$object->task           = 1;
	$object->status         = 0;
	$object->date_create = dol_now();

	if ($reswork>0)
	{
		$object->entity = $objwork->entity;

		$object->fk_work_request = $objwork->id;
		$object->fk_soc = $objwork->fk_soc;
		$object->fk_member = $objwork->fk_member;
		$object->fk_equipment = $objwork->fk_equipment;
		$object->fk_departament = $objwork->fk_departament;
		$object->fk_property = $objwork->fk_property;
		$object->fk_location = $objwork->fk_location;
		$object->email = $objwork->email;
		$object->internal = $objwork->internal;
		$object->speciality = $objwork->speciality;
		$object->detail_problem = $objwork->detail_problem;
		$object->fk_user_assign = $objwork->fk_user_assign;
		$object->date_assign = $objwork->date_assign;
		$object->image_ini = $objwork->image_ini;
		$object->speciality_assign = $objwork->speciality_assign;
		$object->description_assign = $objwork->description_assign;
		$object->description_prog = $objwork->description_prog;
		$object->date_ini_prog = $objwork->date_ini_prog;
		$object->date_fin_prog = $objwork->date_fin_prog;
		$object->speciality_prog = $objwork->speciality_prog;
		$object->fk_equipment_prog = $objwork->fk_equipment_prog;
		$object->fk_property_prog = $objwork->fk_property_prog;
		$object->fk_location_prog = $objwork->fk_location_prog;
		$object->fk_type_repair = GETPOST('fk_type_repair','int');
		$object->typemant_prog = $objwork->typemant_prog;
		$object->fk_user_prog = $objwork->fk_user_prog;
		$object->fk_user_create = $user->id;
		$object->fk_user_mod = $user->id;
		$object->datec = dol_now();
		$object->datem = dol_now();
		$object->tms = dol_now();
	}
	else
	{
		$object->entity = $conf->entity;
		$object->fk_work_request = 0;
		//$object->fk_soc = 0;
		$object->fk_member = GETPOST('fk_member','int');
		$object->fk_equipment = GETPOST('fk_equipment','int')+0;
		$object->fk_departament = GETPOST('fk_departament','int');
		$object->fk_property = GETPOST('fk_property','int')+0;
		$object->fk_location = GETPOST('fk_location','int')+0;

		$object->email = $user->email;
		$object->internal = GETPOST('internal','int')+0;
		$object->fk_type_repair = GETPOST('fk_type_repair','int');
		//$object->speciality = $objwork->speciality;
		$object->detail_problem = GETPOST('detail_problem','alpha');
		$object->fk_user_assign = 0;
		//$object->date_assign = $objwork->date_assign;
		//$object->image_ini = $objwork->image_ini;
		//$object->speciality_assign = $objwork->speciality_assign;
		//$object->description_assign = $objwork->description_assign;
		//$object->description_prog = $objwork->description_prog;
		//$object->date_ini_prog = $objwork->date_ini_prog;
		//$object->date_fin_prog = $objwork->date_fin_prog;
		//$object->speciality_prog = $objwork->speciality_prog;
		//$object->fk_equipment_prog = $objwork->fk_equipment_prog;
		//$object->fk_property_prog = $objwork->fk_property_prog;
		//$object->fk_location_prog = $objwork->fk_location_prog;
		//$object->typemant_prog = $objwork->typemant_prog;
		//$object->fk_user_prog = $objwork->fk_user_prog;
		$object->fk_user_create = $user->id;
		$object->fk_user_mod = $user->id;
		$object->datec = dol_now();
		$object->datem = dol_now();
		$object->tms = dol_now();
	}
	if ($object->fk_equipment <=0)
	{
		if ($object->fk_property<=0)
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Property")), null, 'errors');
		}
		if ($object->fk_location<=0)
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Location")), null, 'errors');
		}
	}
	if ($object->fk_type_repair<=0)
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Typerepair")), null, 'errors');
	}
	if ($object->fk_member<=0)
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Member")), null, 'errors');
	}

	//if (empty($object->speciality))
	//{
	//	$error++;
	//	$mesg.='<div class="error">'.$langs->trans('Error, speciality is required').'</div>';
	//}
	if (!$error)
	{
		$id = $object->create($user);
		if ($id > 0)
		{
			header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
			exit;
		}
		else
		{
			setEventMessages($object->error,$object->errors,'errors');
		}
		$action = 'create';
	}
	else
	{
		$action="create";
	// Force retour sur page creation
	}
}



// updatesocid
if ($action == 'updatesocid' && $user->rights->mant->jobs->assignjobs)
{
	$object->fetch($id);
	$object->fk_soc = GETPOST("fk_soc");
	if ($object->fk_soc)
	{
		$object->status = 2;
		$object->fk_user_assign = $user->id;
		$result = $object->update($user);
		if ($result > 0)
		{
			if ($objSoc->fetch($object->fk_soc))
			{
				$objTypent = fetch_typent($objSoc->typent_id);
			}
			if ($object->fk_soc == -2 ||
				($objTypent->id == $objSoc->typent_id &&
		 $objTypent->code == 'TE_BCB' )) //asignacion interna
			{
				header("Location: card.php?id=".$id.'&action=asignjobsdet');
				exit;
			}
			else
			{
				header("Location: card.php?id=".$id);
				exit;
			}
		}
		$action = 'asignjobs';
		$mesg='<div class="error">'.$object->error.'</div>';
	}
	else
	{
		$mesg='<div class="error">'.$langs->trans("Errorsocrequired").'</div>';
		$action="asignjobs";
		// Force retour sur page creation
	}
}

// updaterech
if ($action == 'updaterech' && $_POST["cancel"] <> $langs->trans("Cancel") && $user->rights->mant->jobs->rechasig)
{
	$object->fetch($id);
	if ($object->id == $id)
	{
		$object->status = 8;
		$object->description_prog = GETPOST('description_prog');
		if (empty($object->description_prog))
		{
			$error++;
			$mesg='<div class="error">'.$langs->trans('Errorrequiredisreasonforrejection').'</div>';
		}
		if (!$error)
		{
			$result = $object->update($user);
			if ($result > 0)
			{
				header("Location: card.php?id=".$id);
				exit;
			}
			$action = 'rechasignjobs';
			$mesg='<div class="error">'.$object->error.'</div>';
		}
		else
			$action = 'rechasignjobs';
	}
	else
	{
		$mesg='<div class="error">'.$object->error.'</div>';
		$action="rechasignjobs";
		// Force retour sur page rejectworkorder
	}
}

// updatejobs
if ($action == 'updatejobs')
{
	if($object->fetch($id)>0)
	{
		if ($object->fk_work_request>0)
			$result = $objwork->fetch($object->fk_work_request);

		$object->address_ip     = $_SERVER['REMOTE_ADDR'];
		if ($user->admin)
			$object->ref = GETPOST('ref');
		$object->fk_member      = GETPOST("fk_member")+0;
		//$object->fk_soc         = GETPOST("fk_soc")+0;
		$object->internal       = GETPOST("internal",'int')+0;
		$object->fk_equipment   = GETPOST("fk_equipment",'int')+0;
		$object->fk_property    = GETPOST("fk_property",'int')+0;
		$object->fk_location 	= GETPOST("fk_location",'int')+0;
		$object->fk_type_repair = GETPOST("fk_type_repair",'int');
		$object->speciality  = GETPOST("speciality");
		$object->detail_problem = GETPOST('detail_problem');
		$object->group_task = (GETPOST('group_task') == 'yes'?1:0);

		if ($result>0)
		{
			$object->fk_soc = $objwork->fk_soc;
			$object->email = $objwork->email;
			$object->fk_user_assign = $objwork->fk_user_assign;
			$object->date_assign = $objwork->date_assign;
			$object->speciality_assign = $objwork->speciality_assign;
			$object->description_assign = $objwork->description_assign;
			$object->description_prog = $objwork->description_prog;
			$object->date_ini_prog = $objwork->date_ini_prog;
			$object->date_fin_prog = $objwork->date_fin_prog;
			$object->image_ini = $objwork->image_ini;
			$object->speciality_prog = $objwork->speciality_prog;
			$object->fk_equipment_prog = $objwork->fk_equipment_prog;
			$object->fk_property_prog = $objwork->fk_property_prog;
			$object->fk_location_prog = $objwork->fk_location_prog;
			$object->typemant_prog = $objwork->typemant_prog;
			$object->fk_user_prog = $objwork->fk_user_prog;
		}
		if ($object->fk_equipment <=0)
		{
			if ($object->fk_property<=0)
			{
				$error++;
				setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Property")), null, 'errors');
			}
			if ($object->fk_location<=0)
			{
				$error++;
				setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Location")), null, 'errors');
			}
		}
		if ($object->fk_type_repair<=0)
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Typerepair")), null, 'errors');
		}

		if (!$error)
		{
			$res = $object->update($user);
			if ($res > 0)
			{
				header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
				exit;
			}
			$action = 'edit';
			setEventMessages($object->error,$object->errors,'errors');
		}
		else
			$action="edit";
	}
	else
	{
		setEventMessages($object->error,$object->errors,'errors');
		$action="edit";
	}
}


//assign fin work
// upassignjobs
if ($action == 'upassignjobs' && $user->rights->mant->jobs->assignjobs)
{
	$object->fetch(GETPOST('id'));
	$statut = $object->status;

	$date_assign = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));

	$object->speciality_assign = GETPOST("speciality_assign");
	$object->date_assign    = $date_assign;
	$object->description_assign = GETPOST('description_assign');

	if ($object->date_assign && $object->description_assign &&
		$object->speciality_assign && $statut == 2)
	{
		$result = $object->update($user);
		if ($result > 0)
		{
		// if (!empty($actionant))
		//   {
		// 	header("Location: card.php?id=".$id.'&action='.$actionant);
		// 	exit;
		//   }
		// else
		//   {
			header("Location: ".$_SERVER['PHP_SELF']."?id=".$id.'&action=asignjobsdet');
			exit;
		// $action = '';
		// $mesg='<div class="ok">'.$langs->trans('Se envio correo de comunicacion al usuario seleccionado').'</div>';
		// }
		}
		else
		{
			$action = 'asignjobsdet';
			$mesg='<div class="error">'.$object->error.'</div>';
		}
	}
	else
	{
		$mesg='<div class="error">'.$langs->trans("Errorlackinformation").'</div>';
		$action="asignjobsdet";
	// Force retour sur page creation
	}
}


// upjobs programacion del trabajo a realizar //upjobs
if ($action == 'upjobs' && $user->rights->mant->jobs->assignjobs ||
	$action == 'upjobs' && $user->rights->mant->jobs->upjobs)
{
	$actiondes = 'asignjobs';
	if ($action == 'upjobs' && $user->rights->mant->jobs->upjobs)
		$actiondes = 'editjobs';
	$actionant = GETPOST('actionant');
	$object->fetch(GETPOST('id'));

	$statut = $object->status;
	$date_ini_prog = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
	$date_fin_prog = dol_mktime(12, 0, 0, GETPOST('fi_month'),GETPOST('fi_day'),GETPOST('fi_year'));
	$object->speciality_prog = GETPOST("speciality_prog");
	$object->date_ini_prog    = $date_ini_prog;
	$object->date_fin_prog    = $date_fin_prog;
	$object->description_prog = GETPOST('description_prog');
	$object->fk_equipment_prog = GETPOST('fk_equipment_prog','int',2);
	if (GETPOST('deletephotoini')) $object->image_ini = '';
	else if (! empty($_FILES['photoini']['name'])) $object->image_ini = dol_sanitizeFileName($_FILES['photoini']['name']);

	// Logo/Photo save
	$dir     = $conf->mant->multidir_output[$object->entity]."/".$object->id."/images";
	$file_OKini = is_uploaded_file($_FILES['photoini']['tmp_name']);

	if ($file_OKini)
	{
		if (GETPOST('deletephotoini'))
		{
			$fileimg=$dir.'/'.$object->image_ini;
			$dirthumbs=$dir.'/thumbs';
			dol_delete_file($fileimg);
			dol_delete_dir_recursive($dirthumbs);
		}

		if (image_format_supported($_FILES['photoini']['name']) > 0)
		{
			dol_mkdir($dir);

			if (@is_dir($dir))
			{
				$newfile=$dir.'/'.dol_sanitizeFileName($_FILES['photoini']['name']);
				$result = dol_move_uploaded_file($_FILES['photoini']['tmp_name'], $newfile, 1);
				if (! $result > 0)
				{
					$errors[] = "ErrorFailedToSaveFile";
				}
				else
				{
			// Create small thumbs for company (Ratio is near 16/9)
			// Used on logon for example
					$imgThumbSmall = vignette($newfile, $maxwidthsmall, $maxheightsmall, '_small', $quality);

			// Create mini thumbs for company (Ratio is near 16/9)
			// Used on menu or for setup page for example
					$imgThumbMini = vignette($newfile, $maxwidthmini, $maxheightmini, '_mini', $quality);
				}
			}
		}
		else
		{
			$errors[] = "ErrorBadImageFormat";
		}
	}
	else
	{
		switch($_FILES['photoini']['error'])
		{
			case 1:
	  //uploaded file exceeds the upload_max_filesize directive in php.ini
			case 2:
	  //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
			$errors[] = "ErrorFileSizeTooLarge";
			break;
			case 3:
	  //uploaded file was only partially uploaded
			$errors[] = "ErrorFilePartiallyUploaded";
			break;
		}
	}

	if ($object->date_ini_prog && $object->date_fin_prog &&
		$object->speciality_prog && $statut == 2)
	{
		$result = $object->update($user);
		if ($result > 0)
		{
			if (!empty($actionant))
			{
				header("Location: card.php?id=".$id.'&action='.$actionant);
				exit;
			}
			else
			{
		//editjobs
				header("Location: card.php?id=".$id.'&action='.$actiondes);
				exit;
			}
		}
		$action = $actiondes;
		$mesg='<div class="error">'.$object->error.'</div>';
	}
	else
	{
		$mesg='<div class="error">'.$langs->trans("Errorlackinformation.").'</div>';
		$action=$actiondes;
	// Force retour sur page creation
	}
}


// upworks
if ($action == 'upwork' && $user->rights->mant->jobs->regjobs)
{
	$error = 0;
	//$present_date = dol_now();
	//$present_date = dol_mktime(12, 0, 0, date('m'),date('d'),date('Y'));
	$adate = dol_getdate(dol_now());
	$present_date  = dol_mktime($adate['hours'], 0, 0, $adate['mon'], $adate['mday'],  $adate['year']);

	$object->fetch(GETPOST('id'));
	$statut   = $object->status;
	$date_ini = dol_mktime(GETPOST('di_hour'), 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));

	$date_fin = dol_mktime(GETPOST('df_hour'), 0, 0, GETPOST('df_month'),GETPOST('df_day'),GETPOST('df_year'));
	$datefin = dol_mktime(GETPOST('df_hour'), 0, 0, GETPOST('df_month'),GETPOST('df_day'),GETPOST('df_year'));
	$object->date_ini        = $date_ini;
	$object->date_fin        = $date_fin;
	$object->description_job = GETPOST('description_job');
	$object->typemant        = GETPOST('typemant');
	$object->speciality_job  = GETPOST('speciality_job');
	$object->fk_equipment    = GETPOST('fk_equipment');
	$object->task            = GETPOST('task');

	if (GETPOST('deletephotofin')) $object->image_fin = '';
	else if (! empty($_FILES['photofin']['name'])) $object->image_fin = dol_sanitizeFileName($_FILES['photofin']['name']);

	// Logo/Photo save
	$dir     = $conf->mant->multidir_output[$object->entity]."/".$object->id."/images";
	$file_OKfin = is_uploaded_file($_FILES['photofin']['tmp_name']);

	if ($file_OKfin)
	{
		if (GETPOST('deletephotofin'))
		{
			$fileimg=$dir.'/'.$object->image_fin;
			$dirthumbs=$dir.'/thumbs';
			dol_delete_file($fileimg);
			dol_delete_dir_recursive($dirthumbs);
		}

		if (image_format_supported($_FILES['photofin']['name']) > 0)
		{
			dol_mkdir($dir);

			if (@is_dir($dir))
			{
				$newfile=$dir.'/'.dol_sanitizeFileName($_FILES['photofin']['name']);
				$result = dol_move_uploaded_file($_FILES['photofin']['tmp_name'], $newfile, 1);

				if (! $result > 0)
				{
					$errors[] = "ErrorFailedToSaveFile";
				}
				else
				{
			// Create small thumbs for company (Ratio is near 16/9)
			// Used on logon for example
					$imgThumbSmall = vignette($newfile, $maxwidthsmall, $maxheightsmall, '_small', $quality);

			// Create mini thumbs for company (Ratio is near 16/9)
			// Used on menu or for setup page for example
					$imgThumbMini = vignette($newfile, $maxwidthmini, $maxheightmini, '_mini', $quality);
				}
			}
		}
		else
		{
			$errors[] = "ErrorBadImageFormat";
		}
	}
	else
	{
		switch($_FILES['photofin']['error'])
		{
			case 1:
	  //uploaded file exceeds the upload_max_filesize directive in php.ini
			case 2:
	  //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
			$errors[] = "ErrorFileSizeTooLarge";
			break;
			case 3:
	  //uploaded file was only partially uploaded
			$errors[] = "ErrorFilePartiallyUploaded";
			break;
		}
	}

				// Gestion du logo de la société

	//$object->status      = 2;
	if ($object->date_ini && $object->description_job && $object->typemant && $statut == 3 && count($errors) <= 0)
	{
		if ($object->date_fin < $object->date_ini)
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("Error, the end date may not be earlier than the start date").' '.dol_print_date($object->date_fin,'day').' '.dol_print_date($object->date_ini,'day').'</div>';
		}
		if ($datefin > $present_date)
		{
//echo 'errororrororor '.$object->date_fin.' '.$present_date.' '.dol_print_date($object->date_fin).' '.dol_print_date($present_date);
			$error++;
			$mesg.='<div class="error">'.$langs->trans("Error, the end date may not be later than the current date").' '.dol_print_date($object->date_fin,'day').' '.dol_print_date($present_date,'day').'</div>';
		}
		if (empty($error))
		{
			$result = $object->update($user);
			if ($result > 0)
			{
				header("Location: card.php?id=".$id.'&action=editregjobs');
				exit;
			}
			$action = 'editregjobs';
			$mesg='<div class="error">'.$object->error.'</div>';
		}
		else
			$action = 'editregjobs';
	}
	else
	{
		$mesg = '';
		if (empty($object->typemant))
			$mesg='<div class="error">'.$langs->trans("Errortypemantrequired").'</div>';
		if (empty($object->date_ini))
			$mesg='<div class="error">'.$langs->trans("Errordateinirequired").'</div>';
		if (empty($object->description_job))
			$mesg='<div class="error">'.$langs->trans("Errordescriptionjobrequired").'</div>';

		if (count($errors) > 0)
		{
			foreach ((array) $errors AS $i => $value)
			{
				$mesg.='<div class="error">'.$langs->trans($value).'</div>';
			}
		}
		$action="editregjobs";
	 // Force retour sur page creation
	}
}

// Add
if ($action == 'closework' && $user->rights->mant->jobs->regjobs)
{
	$id = GETPOST('id');
	$object->fetch(GETPOST('id'));
	$statut = $object->status;
	$object->status      = 4;  //trabajo concluido
	$object->fk_user_job = $user->id;
	if ($object->date_fin && $object->description_job && $statut == 3 )
	{
	//verificamos cuantas ordenes de trabajo tiene y si estan concluidas
		$object->getlist($object->fk_work_request);
		$nJobs = 1;
		$nJobsclose = 0;
		if (count($object->array)>0)
		{
			$nJobs = count($object->array);
			foreach ((array) $object->array AS $j => $objjobsnew)
			{
				if ($objjobsnew->id != $id && $objjobsnew->statut>=4)
					$nJobsclose++;
			}
		}
		$nJobsclose++;
		$db->begin();
		if ($nJobsclose == $nJobs)
		{
		//actualizamos el work_request
			$objwork->fetch($object->fk_work_request);
			$objwork->statut = 6;
			$objwork->update($user);
		}
		$result = $object->update($user);
		$emailto = $object->email;
		if ($object->email)
		{
		//REVISAR ENVIO DE CORREO
		//especiality
			$textmsg = '<p>'.$langs->trans('Speciality').': '.
			select_speciality($object->speciality,'speciality','',1,1).'</p>';
		//equipment
			if ($objEquipment->fetch($object->fk_equipment))
				$textmsg.= '<p>'.$langs->trans('Equipment').': '.
			$objEquipment->nom.'</p>';
		//fecha inicio prog
			$textmsg.= '<p>'.$langs->trans('Dateini').': '.
			dol_print_date($object->date_ini,'daytext').'</p>';
		//fecha final prog
			$textmsg.= '<p>'.$langs->trans('Datefin').': '.
			dol_print_date($object->date_fin,'daytext').'</p>';

		//sendmail
		// Define output language
			$outputlangs = $langs;
			$newlang='';
			if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang=GETPOST('lang_id');
			if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$object->client->default_lang;
			if (! empty($newlang))
			{
				$outputlangs = new Translate("",$conf);
				$outputlangs->setDefaultLang($newlang);
			}

			$arr_file = array();
			$arr_mime = array();
			$arr_name = array();
			$arr_mime[] = 'text/html';
		//$arr_mime[] = 'aplication/rtf';
			$addr_cc = '';
		//buscamos al usuario asignador
			if ($objUser->fetch($object->fk_user_assign))
				$addr_cc = $objUser->email;

			$addr_bcc = '';
			$tmpsujet = $langs->trans('Work order completion').' '.$object->ref;
			$sendto   = $emailto;
			$email_from = $conf->global->MAIN_MAIL_EMAIL_FROM;
			$textmsg.='<p>'.$object->description_job.'</p>';
			$tmpbody = htmlsendemailjob($id,$textmsg,$url);
			$msgishtml = 1;
			$email_errorsto = $conf->global->MAIN_MAIL_ERRORS_TO;
			$arr_css = array('bgcolor' => '#FFFFCC');
			$mailfile = new CMailFile($tmpsujet,$sendto,$email_from,$tmpbody, $arr_file,$arr_mime,$arr_name,$addr_cc, $addr_bcc, 0, $msgishtml,$email_errorsto,$arr_css);
			if ($conf->global->MANT_SEND_EMAIL)
				$result=$mailfile->sendfile();
			else
				$result = 1;
			if ($result)
			{
				$mesg='<div class="ok">'.
				$langs->trans("MailSuccessfulySent",
					$mailfile->getValidAddress($email_from,2),
					$mailfile->getValidAddress($sendto,2)).
				'</div>';
			}
			else
			{
				$error++;
				$mesg='<div class="error">'.$langs->trans("ResultKo").
				'<br>'.$mailfile->error.' '.$result.'</div>';
			}
		}
		else
		{
			$mesg='<div class="error">'.$object->error.'</div>';
		}
		if (empty($error))
		{
			$db->commit();
			header("Location: card.php?id=".$id);
			exit;
		}
		else
			$db->rollback();

		$action = 'editregjobs';
		$mesg='<div class="error">'.$object->error.'</div>';
	}
	else
	{
		$mesg='<div class="error">'.$langs->trans("Errorlackinformation_").'</div>';
		$action="editregjobs";
	// Force retour sur page creation
	}
}
// startjobs programacion
if ($action == 'startjobs' &&
	($user->rights->mant->jobs->upjobs || $user->rights->mant->jobs->assignjobs))
{
	$id = GETPOST('id');
	$object->fetch(GETPOST('id'));
	$statut = $object->status;
	$object->status       = 3; //programado
	$object->fk_user_prog = $user->id;
	if ($object->date_ini_prog && $object->speciality_prog && $statut == 2)
	{

		$db->begin();
		$result = $object->update($user);
		$emailto = $object->email;
		if ($object->email)
		{
		//especiality
			$textmsg = '<p>'.$langs->trans('Speciality').': '.
			select_speciality($object->speciality_prog,'speciality','',1,1).'</p>';
		//equipment
			if ($objEquipment->fetch($object->fk_equipment_prog))
				$textmsg.= '<p>'.$langs->trans('Equipment').': '.
			$objEquipment->nom.'</p>';
		//fecha inicio prog
			$textmsg.= '<p>'.$langs->trans('Dateini').': '.
			dol_print_date($object->date_ini_prog,'daytext').'</p>';
		//fecha final prog
			$textmsg.= '<p>'.$langs->trans('Datefin').': '.
			dol_print_date($object->date_fin_prog,'daytext').'</p>';
		//sendmail
		// Define output language
			$outputlangs = $langs;
			$newlang='';
			if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang=GETPOST('lang_id');
			if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$object->client->default_lang;
			if (! empty($newlang))
			{
				$outputlangs = new Translate("",$conf);
				$outputlangs->setDefaultLang($newlang);
			}

			$arr_file = array();
			$arr_mime = array();
			$arr_name = array();
			$arr_mime[] = 'text/html';
		//$arr_mime[] = 'aplication/rtf';
			$addr_cc = '';
		//buscamos al usuario asignador
			if ($objUser->fetch($object->fk_user_assign))
				$addr_cc = $objUser->email;

			$addr_bcc = '';
			$tmpsujet = $langs->trans('Programming work order').' '.$object->ref;
			$sendto   = $emailto;
			$email_from = $conf->global->MAIN_MAIL_EMAIL_FROM;
			$textmsg.='<p>'.$object->description_prog.'</p>';
			$tmpbody = htmlsendemailprog($id,$textmsg,$url);
			$msgishtml = 1;
			$email_errorsto = $conf->global->MAIN_MAIL_ERRORS_TO;
			$arr_css = array('bgcolor' => '#FFFFCC');
			$mailfile = new CMailFile($tmpsujet,$sendto,$email_from,$tmpbody, $arr_file,$arr_mime,$arr_name,$addr_cc, $addr_bcc, 0, $msgishtml,$email_errorsto,$arr_css);
			if ($conf->global->MANT_SEND_EMAIL)
				$result=$mailfile->sendfile();
			else
				$result = 1;
			if ($result)
			{
				$mesg='<div class="ok">'.
				$langs->trans("MailSuccessfulySent",
					$mailfile->getValidAddress($email_from,2),
					$mailfile->getValidAddress($sendto,2)).
				'</div>';
			}
			else
			{
				$error++;
				$mesg='<div class="error">'.$langs->trans("ResultKo").
				'<br>'.$mailfile->error.' '.$result.'</div>';
			}
		}
		else
		{
			$mesg='<div class="error">'.$object->error.'</div>';
		}
		if (empty($error))
		{
			$db->commit();
			header("Location: card.php?id=".$id);
			exit;
		}
		else
			$db->rollback();
		$action = 'editjobs';
		$mesg='<div class="error">'.$object->error.'</div>';
	}
	else
	{
		$mesg='<div class="error">'.$langs->trans("Errorlackinformation,,").'</div>';
		$action="editjobs";
	// Force retour sur page creation
	}
}

// startjobs communication assign
if ($action == 'startassignjobs' && ($user->rights->mant->jobs->upjobs || $user->rights->mant->jobs->assignjobs))
{
	$id = GETPOST('id');
	$object->fetch(GETPOST('id'));
	$statut = $object->status;
	$object->status       = 2; //programado
	$object->fk_user_prog = $user->id;
	if ($object->date_assign && $object->speciality_assign && $statut == 2)
	{
		$db->begin();
		$result = $object->update($user);
		$emailto = $object->email;
		//enviar correos a los asignados
		$aArray = $objJobsuser->list_jobsuser($id);
		foreach((array) $aArray AS $j => $objjus)
		{
			$objAdherent->fetch($objjus->fk_user);
			if ($objAdherent->id == $objjus->fk_user)
			{
				if (!empty($emailto)) $emailto.= ',';
				$emailto.= $objAdherent->email;
			}
		}
		//$emailto = '';
		if ($emailto)
		{
			//especiality
			$textmsg = '<p>'.$langs->trans('Speciality').': '.
			select_speciality($object->speciality_assign,'speciality','',1,1).'</p>';
			//fecha inicio prog
			$textmsg.= '<p>'.$langs->trans('Dateassigned').': '.
			dol_print_date($object->date_assign,'daytext').'</p>';

			//sendmail
			// Define output language
			$outputlangs = $langs;
			$newlang='';
			if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang=GETPOST('lang_id');
			if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$object->client->default_lang;
			if (! empty($newlang))
			{
				$outputlangs = new Translate("",$conf);
				$outputlangs->setDefaultLang($newlang);
			}

			$arr_file = array();
			$arr_mime = array();
			$arr_name = array();
			$arr_mime[] = 'text/html';
		//$arr_mime[] = 'aplication/rtf';
			$addr_cc = '';
		//buscamos al usuario asignador
			if ($objUser->fetch($object->fk_user_assign))
				$email_from = $objUser->email;

			$addr_bcc = '';
			$tmpsujet = $langs->trans('Assignment work order').' '.$object->ref;
			$sendto   = $emailto;
		//$email_from = $conf->global->MAIN_MAIL_EMAIL_FROM;
			$textmsg.='<p>'.$object->description_assign.'</p>';
			$tmpbody = htmlsendemailassign($id,$textmsg,$url);
			$msgishtml = 1;
			$email_errorsto = $conf->global->MAIN_MAIL_ERRORS_TO;
			$arr_css = array('bgcolor' => '#FFFFCC');
			$mailfile = new CMailFile($tmpsujet,$sendto,$email_from,$tmpbody, $arr_file,$arr_mime,$arr_name,$addr_cc, $addr_bcc, 0, $msgishtml,$email_errorsto,$arr_css);
			if ($conf->global->MANT_SEND_EMAIL)
				$result=$mailfile->sendfile();
			else
				$result = 1;
			if ($result)
			{
				$mesg='<div class="ok">'.
				$langs->trans("MailSuccessfulySent",
					$mailfile->getValidAddress($email_from,2),
					$mailfile->getValidAddress($sendto,2)).
				'</div>';
			}
			else
			{
				$error++;
				$mesg='<div class="error">'.$langs->trans("ResultKo").
				'<br>'.$mailfile->error.' '.$result.'</div>';
			}
		}
	// else
	//   {
	//     $mesg='<div class="error">'.$object->error.'</div>';
	//   }
		if (empty($error))
		{
			$db->commit();
			header("Location: card.php?id=".$id);
			exit;
		}
		else
			$db->rollback();
		$action = 'editjobs';
		$mesg='<div class="error">'.$object->error.'</div>';
	}
	else
	{
		$mesg='<div class="error">'.$langs->trans("Errorlackinformation-").'</div>';
		$action="editjobs";
	// Force retour sur page creation
	}
}

// Adduser tecnicos internos
if ($action == 'adduser' && $user->rights->mant->jobs->assignjobs)
{
	$object->fetch($id);
	$statut = $object->status;
	//nuevo
	$objJobuser->fk_user = GETPOST("fk_user",'int');
	$objJobuser->fk_jobs = $id;
	$objJobuser->status      = 1;
	$objJobuser->fk_user_create = $user->id;
	$objJobuser->fk_user_mod = $user->id;
	$objJobuser->datec = dol_now();
	$objJobuser->datem = dol_now();
	$objJobuser->tms = dol_now();
	if ($objJobuser->fk_user > 0 && $statut == 2)
	{
		$result = $objJobuser->create($user);
		if ($result > 0)
		{
			header("Location: ".$_SERVER['PHP_SELF']."?id=".$id.'&action=asignjobs');
			exit;
		}
		$action = 'asignjobs';
		$mesg='<div class="error">'.$objJobuser->error.'</div>';
	}
	else
	{
		$mesg='<div class="error">'.$langs->trans("Erroruserisrequired").'</div>';
		$action="asignjobs";
		// Force retour sur page creation
	}
}

//assign fin work
// upassignjobs
  //echo 'action '.$action.' '.$user->rights->mant->tick->asst;exit;
if ($action == 'upassignreq' && $user->rights->mant->tick->asst)
{
	$object->fetch(GETPOST('id'));
	$statut = $object->status;

	//recuperamos a todos los tecnicos internos asigandos
	$objUser = new User($db);
	$filterstatic = " AND t.fk_jobs = ".$object->id;

	$objJobuser->fetchAll('ASC', 'datec', 0,0,array(1=>1),'AND',$filterstatic='');
	$aArray = $objJobsuser->lines;
	$emailto = '';
	//print_r($aArray);
	foreach ((array) $objJobuser->lines AS $i => $objJuser)
	{
		$objAdherent->fetch($objJuser->fk_user);
		if ($objAdherent->id == $objJuser->fk_user && !empty($objAdherent->email))
		{
			if (!empty($emailto)) $emailto.= ',';
			$emailto.= $objAdherent->email;
		}
	}

	if (empty($emailto))
		$emailto = $user->email;
	//echo $emailto.' '.$object->status;exit;
	if ($object->status == 2)
	{
		//sendmail
		// Define output language
		$outputlangs = $langs;
		$newlang='';
		if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang=GETPOST('lang_id');
		if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$object->client->default_lang;
		if (! empty($newlang))
		{
			$outputlangs = new Translate("",$conf);
			$outputlangs->setDefaultLang($newlang);
		}

		$arr_file = array();
		$arr_mime = array();
		$arr_name = array();
		$arr_mime[] = 'text/html';
		//$arr_mime[] = 'aplication/rtf';

		$tmpsujet = $langs->trans('Send email request');
		$sendto   = $emailto;
		$email_from = $conf->global->MAIN_MAIL_EMAIL_FROM;
		$objUser->fetch($object->fk_user_assign);
		if ($objUser->id == $object->fk_user_assign && !empty($objUser->email))
			$email_from = $objUser->email;
		//mensaje de asignacion trabajo a tecnico interno
		$tmpbody = htmlsendemailassignti($id,$url);
		$msgishtml = 1;
		$email_errorsto = $conf->global->MAIN_MAIL_ERRORS_TO;
		$arr_css = array('bgcolor' => '#FFFFCC');
		$mailfile = new CMailFile($tmpsujet,$sendto,$email_from,$tmpbody, $arr_file,$arr_mime,$arr_name,'', '', 0, $msgishtml,$email_errorsto,$arr_css);

		if (!empty($emailto))
		{
			if ($conf->global->MANT_SEND_EMAIL)
				$result=$mailfile->sendfile();
			else
				$result = 1;
		}
		else
		{
			setEventMessages($langs->trans('No esta definido las cuentas de correo'),null,'warnings');
			$result = 1;
		}
		if ($result)
		{
			$mesg='<div class="ok">'.
			$langs->trans("MailSuccessfulySent",
				$mailfile->getValidAddress($email_from,2),
				$mailfile->getValidAddress($sendto,2)).'</div>';
			$object->status = 3;
			$res = $object->update($user);
			if ($res>0)
			{
				header("Location: ".$_SERVER['PHP_SELF']."?id=".$id.'&mesg='.$mesg);
				exit;
			}
			else
			{
				$mesg='<div class="error">'.$langs->trans("ResultKo").'<br>'.$object->error.'</div>';
				$action = 'asignjobs';
			}
		}
		else
		{
			$mesg='<div class="error">'.$langs->trans("ResultKo").'<br>'.$mailfile->error.' '.$result.'</div>';
			$action = 'asignjobs';
		}
	}
	else
	{
		$action = '';
	}
	// header("Location: card.php?id=".$id.'&action=asignjobs&mesg='.$mesg);
	// exit;
}

// Addcontact
if ($action == 'addcontact' && $user->rights->mant->jobs->assignjobs)
{
	$object->fetch($id);
	$statut = $object->status;
	$objJobsContact = new Mjobscontact($db);
	$objJobsContact->fk_contact = GETPOST("fk_contact",'int');
	$objJobsContact->fk_charge    = 1; //revisar
	$objJobsContact->fk_jobs = $id;
	$objJobsContact->detail    = GETPOST('detail');
	$objJobsContact->status      = 1;
	$objJobsContact->fk_user_create = $user->id;
	$objJobsContact->fk_user_mod = $user->id;
	$objJobsContact->datec = dol_now();
	$objJobsContact->datem = dol_now();
	$objJobsContact->tms = dol_now();
	if ($objJobsContact->fk_contact && $statut == 2 || $objJobsContact->fk_contact && $statut == 3 && empty($object->date_ini))
	{
		$result = $objJobsContact->create($user);
		if ($result > 0)
		{
			header("Location: ".$_SERVER['PHP_SELF']."?id=".$id.'&action=asignjobs');
			exit;
		}
		$action = 'asignjobs';
		$mesg='<div class="error">'.$object->error.'</div>';
	}
	else
	{
		$mesg='<div class="error">'.$langs->trans("Errorcontactrequired").'</div>';
		$action="asignjobs";
	// Force retour sur page creation
	}
}

// updateuserdet
if ($action == 'updateuserdet' && $user->rights->mant->jobs->assignjobs)
{
	$id = GETPOST('id');
	$object->fetch(GETPOST('id'));
	$statut = $object->status;

	$objJobsuser = new Mjobsuser($db);
	$objJobsuser->fk_user = GETPOST("fk_user");
	$objJobsuser->statut    = 1;
	$objJobsuser->fk_jobs = $id;
	$objJobsuser->detail    = GETPOST('detail');
	$objJobsuser->tms = date('YmdHis');
	if ($objJobsuser->fk_user && $statut == 2)
	{
		$result = $objJobsuser->create($user);
		if ($result > 0)
		{
			header("Location: card.php?id=".$id.'&action=asignjobsdet');
			exit;
		}
		$action = 'asignjobsdet';
		$mesg='<div class="error">'.$object->error.'</div>';
	}
	else
	{
		$mesg='<div class="error">'.$langs->trans("Erroruserrequired").'</div>';
		$action="asignjobsdet";
	// Force retour sur page creation
	}
}

// Addorder
if ($action == 'addorder' && $user->rights->mant->jobs->regjobs)
{
	$error=0;
	//buscamos el pedido
	$objcomm->fetch('',GETPOST('order_number','int'));
	if ($objcomm->ref == $order_number)
		$date_order = $objcomm->date_commande;
	else
		$date_order = dol_mktime(12, 0, 0, GETPOST('do_month'),GETPOST('do_day'),GETPOST('do_year'));
	$aQuant = GETPOST('quant');
	$aDescription = GETPOST('description','alpha');
	$aUnit = GETPOST('unit');
	//recorremos los items
	$db->begin();
	foreach ((array) $aQuant AS $fk_product => $quant)
	{
		if ($quant > 0)
		{
			$object->fetch(GETPOST('id','int',2));
			$statut = $object->status;

			$objJobsorder = new Mjobsorder($db);
			$objJobsorder->fk_jobs = $id;
			$objJobsorder->order_number = GETPOST('order_number');
			$objJobsorder->date_order = $date_order;
			$objJobsorder->description = $aDescription[$fk_product];
			$objJobsorder->fk_product = $fk_product;
			$objJobsorder->quant = $aQuant[$fk_product];
			$objJobsorder->unit = $aUnit[$fk_product];

			$description[$fk_product] = $aDescription[$fk_product];
			$order_number = GETPOST('order_number');
			$fk_product = GETPOST('fk_product');
			$quant[$fk_product] = $aQuant[$fk_product];
			$unit[$fk_product] = $aUnit[$fk_product];

			$objJobsorder->statut      = 1;
			$objJobsorder->tms = date('YmdHis');
			if ($objJobsorder->order_number && $statut == 3)
			{
				$result = $objJobsorder->create($user);
				if ($result <=0)
				{
					$error++;
				}
				$action = 'editregjobs';
				$mesg='<div class="error">'.$objJobsorder->error.'</div>';
			}
		}
	}
	if (!$error)
	{
		$db->commit();
		header("Location: card.php?id=".$id.'&action=editregjobs&order_number='.GETPOST('order_number').'&date_order='.$date_order);
		exit;
	}
	else
	{
		$db->rollback();
		$mesg='<div class="error">'.$langs->trans("Errororderrequired").'</div>';
		$action="editregjobs";
	// Force retour sur page creation
	}
}

// Addordern
if ($action == 'addordern' && $user->rights->mant->jobs->regjobs)
{
	//buscamos el pedido
	$date_order = dol_mktime(12, 0, 0, GETPOST('do_month'),GETPOST('do_day'),GETPOST('do_year'));

	$object->fetch(GETPOST('id','int',2));
	$statut = $object->status;

	$objJobsorder = new Mjobsorder($db);
	$objJobsorder->fk_jobs = $id;
	$objJobsorder->order_number = GETPOST('order_number');
	$objJobsorder->date_order = $date_order;
	$objJobsorder->description = GETPOST('description');
	$objJobsorder->fk_product = GETPOST('fk_product');
	$objJobsorder->quant = GETPOST('quant');
	$objJobsorder->unit = GETPOST('unit');

	$description  = GETPOST('description');
	$order_number = GETPOST('order_number');
	$fk_product   = GETPOST('fk_product');
	$quant        = GETPOST('quant');
	$unit         = GETPOST('unit');

	$objJobsorder->statut      = 1;
	$objJobsorder->tms = date('YmdHis');
	if ($objJobsorder->order_number && $statut == 3)
	{
		$result = $objJobsorder->create($user);
		if ($result > 0)
		{
			header("Location: card.php?id=".$id.'&action=editregjobs&order_number='.GETPOST('order_number').'&date_order='.$date_order);
			exit;
		}
		$action = 'editregjobs';
		$mesg='<div class="error">'.$objJobsorder->error.'</div>';
	}
	else
	{
		$mesg='<div class="error">'.$langs->trans("Errororderrequired").'</div>';
		$action="editregjobs";
	// Force retour sur page creation
	}
}

// Addused
if ($action == 'addused' && $user->rights->mant->jobs->regjobs)
{
	$error= '';
	$used_datereturn = dol_mktime(12, 0, 0, GETPOST('dm_month'),GETPOST('dm_day'),GETPOST('dm_year'));
	if ($object->fetch(GETPOST('id','int',2))>0)
	{
		$statut = $object->status;
		require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobsmaterialused.class.php';
		$objJobsused = new Mjobsmaterialused($db);
		$objJobsused->fk_jobs     = $id;
		$objJobsused->ref         = GETPOST('used_ref');
		$objJobsused->date_return = $used_datereturn;
		$objJobsused->description = GETPOST('used_description');
		$objJobsused->unit  = GETPOST('used_unit');
		$objJobsused->quant = GETPOST('used_quant');

		$objJobsused->statut      = 1;
		$objJobsused->tms = date('YmdHis');

		$used_ref = $objJobsused->ref;
		$used_description = $objJobsused->description;
		$used_unit = $objJobsused->unit;
		$used_quant = $objJobsused->quant;
		if (empty($objJobsused->quant))
			$error++;

		if (empty($error) && $objJobsused->ref && $statut == 3)
		{
			$result = $objJobsused->create($user);
			if ($result > 0)
			{
				header("Location: card.php?id=".$id.'&action=editregjobs');
				exit;
			}
			$action = 'editregjobs';
			$mesg='<div class="error">'.$objJobsused->error.'</div>';
		}
		else
		{
			$mesg='<div class="error">'.$langs->trans("Errordocumentisrequired").'</div>';
			if ($error)
				$mesg='<div class="error">'.$langs->trans("Errorquantisrequired").'</div>';

			$action="editregjobs";
		// Force retour sur page creation
		}
	}
}

/*
 * Confirmation de la re validation
 */
if ($action == 'revalidate')
{
	$object->fetch(GETPOST('id'));
	//cambiando a validado
	$object->status = 0;
	//update
	$object->update($user);
	header("Location: card.php?id=".$_GET['id']);
}

// Delete jobs
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->mant->jobs->del)
{
	$object->fetch($_REQUEST["id"]);
	$result=$object->delete($user);
	if ($result > 0)
	{
		header("Location: ".DOL_URL_ROOT.'/mant/jobs/list.php');
		exit;
	}
	else
	{
		$mesg='<div class="error">'.$object->error.'</div>';
		$action='';
	}
}

// Delete user jobs
if ($action == 'confirm_delete_user')
{
	if ($_REQUEST["confirm"] == 'yes' && $user->rights->mant->jobs->crear)
	{
		$objJobsuser->fetch($_REQUEST["idu"]);
		$result=$objJobsuser->delete($user);
		if ($result > 0)
		{
			header("Location: ".$_SERVER['PHP_SELF'].'/mant/jobs/card.php?id='.$id.'&amp;action=assignjobsdet');
			exit;
		}
		else
		{
			$mesg='<div class="error">'.$object->error.'</div>';
			$action='assignjobsdet';
		}
	}
	else
	{
		$action = 'assignjobsdet';
	}
}

// Delete item orders
if ($action == 'confirm_delete_order' && $_REQUEST["confirm"] == 'yes' && $user->rights->mant->jobs->regjobs)
{
	$objJobsorder = new Mjobsorder($db);

	$result = $objJobsorder->fetch(GETPOST('idr'));
	if ($result)
	{
		$result=$objJobsorder->delete($user);
		if ($result > 0)
		{
			header("Location: ".DOL_URL_ROOT.'/mant/jobs/card.php?id='.$id.'&action=editregjobs');
			exit;
		}
		else
		{
			$mesg='<div class="error">'.$objJobsorder->error.'</div>';
			$action='editregjobs';
		}
	}
}

// Delete item used
if ($action == 'confirm_delete_used' && $_REQUEST["confirm"] == 'yes' && $user->rights->mant->jobs->regjobs)
{
	$objJobsused = new Mjobsmaterialused($db);
	$result = $objJobsused->fetch(GETPOST('idr'));
	if ($result)
	{
		$result=$objJobsused->delete($user);
		if ($result > 0)
		{
			header("Location: ".DOL_URL_ROOT.'/mant/jobs/card.php?id='.$id.'&action=editregjobs');
			exit;
		}
		else
		{
			$mesg='<div class="error">'.$objJobsused->error.'</div>';
			$action='editregjobs';
		}
	}
}

// Modification entrepot
if ($action == 'updatexxx' && $_POST["cancel"] <> $langs->trans("Cancel"))
{
	$date_ini  = dol_mktime(12, 0, 0, GETPOST('date_inimonth'),  GETPOST('date_iniday'),  GETPOST('date_iniyear'));
	$date_fin  = dol_mktime(12, 0, 0, GETPOST('date_finmonth'),  GETPOST('date_finday'),  GETPOST('date_finyear'));

	$object = new Contabperiodo($db);
	if ($object->fetch($_POST["id"]))
	{
		$object->period_month = $_POST["period_month"];
		$object->period_year  = $_POST["period_year"];
		$object->date_ini     = $date_ini;
		$object->date_fin     = $date_fin;
		if ( $object->update($_POST["id"], $user) > 0)
		{
			$action = '';
			$_GET["id"] = $_POST["id"];
		//$mesg = '<div class="ok">Fiche mise a jour</div>';
		}
		else
		{
			$action = 'edit';
			$_GET["id"] = $_POST["id"];
			$mesg = '<div class="error">'.$object->error.'</div>';
		}
	}
	else
	{
		$action = 'edit';
		$_GET["id"] = $_POST["id"];
		$mesg = '<div class="error">'.$object->error.'</div>';
	}
}

if ( ($action == 'createedit') )
{
	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	//$tmparray=getProperty(GETPOST('country_id','int'),'all',$db,$langs,0);
	$tmparray['fk_property'] = GETPOST('fk_property');
	$tmparray['ref'] = GETPOST('ref');
	$tmparray['fk_member'] = GETPOST('fk_member');
	$tmparray['internal'] = GETPOST('internal');
	$tmparray['speciality'] = GETPOST('speciality');

	if (! empty($tmparray['fk_property']))
	{
		$object->fk_property = $tmparray['fk_property'];
		$object->ref = $tmparray['ref'];
		$object->fk_member = $tmparray['fk_member'];
		$object->internal = $tmparray['internal'];
		$object->speciality = $tmparray['speciality'];
		$action='create';
	}
}

if ( ($action == 'createo') )
{
	$tmparray['order_number'] = GETPOST('order_number');

	if (! empty($tmparray['order_number']))
	{
	//buscamos el pedido
		$objcomm->fetch('',$tmparray['order_number']);
		$action='editregjobs';
	}
}

if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $_POST["id"];
}



/*
 * View
 */


$form=new Formv($db);

$aArrjs = array();
$help_url='EN:Module_Contab_En|FR:Module_Contab|ES:M&oacute;dulo_Contab';
//$aArrcss = array('/mant/css/style-desktop.css');
$conf->dol_hide_leftmenu = 0;
llxHeader("",$langs->trans("Managementmant"),$help_url,'','','',$aArrjs,$aArrcss);



if ($action == 'create' && $user->rights->mant->jobs->crear)
{
	$stype = GETPOST('stype');
	if (empty($stype)) $stype = 0;
	//verificamos si biene de work request
	if (!empty($idw))
	{
		$result = $objwork->fetch($idw);
		if ($result > 0 && empty($tmparray))
		{
			$object->internal    = $objwork->internal;
			$object->fk_equipment    = $objwork->fk_equipment;
			$fk_equipment    = $objwork->fk_equipment;
			if ($object->fk_equipment>0) $stype= 1;
			$object->fk_member   = $objwork->fk_member;
			$object->fk_property = $objwork->fk_property;
			$object->fk_location = $objwork->fk_location;
			$object->speciality  = $objwork->speciality_prog;
			$object->detail_problem = $objwork->detail_problem;
			$object->email       = $objwork->email;
			$object->fk_soc      = $objwork->fk_soc;
		}
	}
	print_fiche_titre($langs->trans("Neworderjobs"));
	if (empty($object->ref)) $object->ref = '(PROV)';

	print "\n".'<script type="text/javascript" language="javascript">';
	print '$(document).ready(function () {
		is_stype='.$stype.';
		if (is_stype) {
			$(".equipmentline").show();
			$(".propertyline").hide();
			$(".locationline").hide();
		} else {
			$(".propertyline").show();
			$(".locationline").show();
			$(".equipmentline").hide();
		}

		$("#fk_property").change(function() {
			document.form_index.action.value="create";
			document.form_index.submit();
		});
		$("#fk_equipment").change(function() {
			document.form_index.action.value="create";
			document.form_index.submit();
		});
		$("#stype").change(function() {
			document.form_index.action.value="create";
			document.form_index.submit();
		});
	});';
	print '</script>'."\n";

	print '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form_index">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="idw" value="'.$idw.'">';
	print '<input type="hidden" name="fk_soc" value="'.$object->fk_soc.'">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

	// ref numeracion automatica del ticket
	if (!empty($objwork->ref))
	{
		print '<tr><td width="20%">'.$langs->trans('Ticketnumber').'</td><td colspan="2">';
		print $objwork->ref;
		print '</td></tr>';
	}
	// ref numeracion automatica de la OT
	print '<tr><td class="fieldrequired" width="20%">'.$langs->trans('Jobsordernumber').'</td><td colspan="2">';
	print '<input id="ref" type="text" value="'.GETPOST('ref').'" name="ref" size="13" maxlength="15" disabled="disabled">';
	print '</td></tr>';

	//selector de equipo o inmueble
	$aSelect = array(0=>$langs->trans('Property'),1=>$langs->trans('Equipment'));
	print '<tr><td  class="fieldrequired" width="20%">'.$langs->trans('Mantenimiento').'</td><td colspan="2">';
	print $form->selectarray('stype',$aSelect,$stype);
	print '</td></tr>';
	//equipment
	print '<tr class="equipmentline"><td  class="fieldrequired" width="20%">'.$langs->trans('Equipment').'</td><td colspan="2">';
	/*
	$res = $objEquipment->fetchAll('ASC', 'label',0,0,array('entity'=>$conf->entity,'status'=>1),'AND','',false);
	$options = '<option>'.$langs->trans('Select').'</option>';
	if($res>0)
	{
		foreach($objEquipment->lines AS $j => $line)
		{
			$selected = '';
			if((GETPOST('fk_equipment')?GETPOST('fk_equipment'):$object->fk_equipment) == $line->id) $selected = ' selected';
			$options.= '<option value="'.$line->id.'" '.$selected.'>'.$line->label.'</option>';
		}
	}
	print '<select id="fk_equipment" name="fk_equipment">'.$options.'</select>';
	*/
	print $form->select_equipment($fk_equipment, 'fk_equipment', '', 20, 0, 0, 2, '', 1, array(),0,'','',0);
	print '</td></tr>';

	if ($fk_equipment>0)
	{
		$objEquipment->fetch($fk_equipment);
		$fk_location = $objEquipment->fk_location;
		$_GET['fk_location'] = $fk_location;
		if ($fk_location>0)
		{
			$objLocation->fetch($fk_location);
			$fk_property = $objLocation->fk_property;
			$_GET['fk_property'] = $fk_property;
		}
	}
	// property
	$fk_property = GETPOST('fk_property');
	print '<tr class="propertyline"><td class="fieldrequired">'.$langs->trans('Property').'</td><td colspan="2">';
	if (!empty($idw))
	{
		if ($object->fk_property)
		{
			if ($objProperty->fetch($object->fk_property) > 0)
				print $objProperty->ref;
		}
		print '<input type="hidden" name="fk_property" value="'.$object->fk_property.'">';
	}
	else
	{
		$filter = " AND t.entity = ".$conf->entity;
		$res = $objProperty->fetchAll('ASC','label',0,0,array('status'=>1),'AND',$filter);
		$options = '<option value="-1">'.$langs->trans('Selectproperty').'</option>';
		$lines =$objProperty->lines;
		foreach ((array) $lines AS $j => $line)
		{
			$selected = '';
			if ($fk_property == $line->id) $selected = ' selected';
			$options.= '<option value="'.$line->id.'" '.$selected.'>'.$line->label.' ('.$line->ref.')'.'</option>';
		}
		print '<select id="fk_property" name="fk_property">'.$options.'</select>';
		 //print $objProperty->select_property($fk_property,'fk_property','',40,1);
	}

	print '</td></tr>';

	// location
	print '<tr class="locationline"><td class="fieldrequired">'.$langs->trans('Location').'</td><td colspan="2">';
	if (!empty($idw))
	{
		if ($object->fk_location)
		{
			if ($objLocation->fetch($object->fk_location) > 0)
				print $objLocation->detail;
		}
		print '<input type="hidden" name="fk_location" value="'.$object->fk_location.'">';
	}
	else
	{
		$filter = " AND t.fk_property = ".$fk_property;
		$res = $objLocation->fetchAll('ASC','detail',0,0,array('status'=>1),'AND',$filter);
		$options = '';
		$lines =$objLocation->lines;
		foreach ((array) $lines AS $j => $line)
		{
			$selected = '';
			if (GETPOST('fk_location') == $line->id) $selected = ' selected';
			$options.= '<option value="'.$line->id.'" '.$selected.'>'.$line->detail.'</option>';
		}
		print '<select id="fk_location" name="fk_location">'.$options.'</select>';

	}
	print '</td></tr>';
	// solicitante
	print '<tr><td class="fieldrequired">'.$langs->trans('Solicitante').'</td><td colspan="2">';
	if (!empty($idw))
	{
		if ($object->fk_member)
		{
			$objadh = new Adherent($db);
			if ($objadh->fetch($object->fk_member) > 0)
				print $objadh->lastname.' '.$objadh->firstname;
		}
		print '<input type="hidden" name="fk_member" value="'.$object->fk_member.'">';
	}
	else
		print select_adherent((GETPOST('fk_member','int')?GETPOST('fk_member','int'):$user->fk_member),'fk_member','',0,1);
	print '</td></tr>';

	// departamento

	if (!empty($idw))
	{
		if ($object->fk_departament)
		{

			if ($objDepartament->fetch($object->fk_departament))
			{
				print '<tr><td class="fieldrequired">'.$langs->trans('Departament').'</td><td colspan="2">';
				print $objDepartament->label;
				print '</td></tr>';
			}
		}
		print '<input type="hidden" name="fk_member" value="'.$object->fk_member.'">';
	}
	else
	{
		print '<tr><td class="fieldrequired">'.$langs->trans('Departament').'</td><td colspan="2">';
		print $form->select_departament(GETPOST('fk_departament'),'fk_departament','',0,1,'',0);
		print '</td></tr>';
	}

	// ref internal
	print '<tr><td>'.$langs->trans('Internal').'</td><td colspan="2">';
	if (!empty($idw))
	{
		if ($object->internal)
		{
			print $object->internal;
		}
		print '<input type="hidden" name="internal" value="'.$object->internal.'">';
	}
	else
		print '<input id="internal" type="text" value="'.GETPOST('internal','alpha').'" name="internal" size="5">';
	print '</td></tr>';



	//type repair
	print '<tr><td class="fieldrequired">'.$langs->trans('Typerepair').'</td><td colspan="2">';
	print $form->select_type_repair((GETPOST('fk_type_repair')?GETPOST('fk_type_repair'):$objwork->fk_type_repair),'fk_type_repair','',0,1,'');
	print '</td></tr>';

	//descripcion
	print '<tr><td class="fieldrequired">'.$langs->trans('Detailtheproblem').'</td><td colspan="2">';
	if (!empty($objwork->detail_problem))
	{
		print $objwork->detail_problem;
		print '<input type="hidden" name="detail_problem" value="'.$object->detail_problem.'">';
	}
	else
	{
		print '<textarea name="detail_problem" cols="80" rows="5">'.GETPOST('detail_problem').'</textarea>';
	}
	print '</td></tr>';


	// Especiality
	//print '<tr><td class="fieldrequired">'.$langs->trans('Speciality').'</td><td colspan="2">';
	//print select_speciality(GETPOST('speciality'),'speciality','',1);
	//print '</td></tr>';

	// Group task
	print '<tr><td>'.$langs->trans('Grouptask').'</td><td colspan="2">';
	print $form->selectyesno('group_task',GETPOST('group_task'),1,false);
	print '</td></tr>';

	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

	print '</form>';
}
else
{
	if ($id||$ref)
	{
		dol_htmloutput_mesg($mesg);
		$result = $object->fetch($id,$ref);

		$lImage = true;
		//buscamos si la ubicacion tiene seguridad activa
		if ($objLocation->fetch($object->fk_location)>0)
		{
			if ($objLocation->id == $object->fk_location)
			{
				if ($objLocation->safety)
					$lImage = false;
			}
		}
		$objAdherent = new Adherent($db);
		if ($result < 0)
		{
			dol_print_error($db);
		}

		//validamos la edicion si el estado esta en 1

		if (!$user->admin && $object->status > 0 && $action == 'edit')
			$action = '';

		//verificamos si tiene ticket
		if ($object->fk_work_request > 0)
		{
			$objwork->fetch($object->fk_work_request);
		}
		// Affichage fiche
		if ($action <> 'edit' && $action <> 're-edit')
		{
			$head = jobs_prepare_head($object);

			dol_fiche_head($head, 'card', $langs->trans("Jobs"), 0, 'mant');

			if ( ($action == 'updateedit') )
			{
				require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
				//$tmparray=getProperty(GETPOST('country_id','int'),'all',$db,$langs,0);
				$tmparray['id'] = GETPOST('socidnew');
				if (! empty($tmparray['id']))
				{
					//$object->fetch(GETPOST('id'));
					$socidnew = $tmparray['id'];
					//$_GET['id']=$object->id;
					$action='editjobs';
				}
			}
			if ( ($action == 'updatesocedit') )
			{
				require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
				$tmparray['id'] = GETPOST('fk_soc');
				if (! empty($tmparray['id']))
				{
					//$object->fetch(GETPOST('id'));
					$object->fk_soc = $tmparray['id'];
					$object->status = 1;
					$action='asignjobs';
				}
			}

			// Close period
			if ($action == 'close')
			{
				$object->fetch(GETPOST('id'));
				//cambiando a validado
				$object->status = 2;
				//update
				$object->update($user);
				$action = '';
				//header("Location: card.php?id=".$_GET['id']);

			}

			// Open period
			if ($action == 'openwork')
			{
				$object->fetch(GETPOST('id'));
				//cambiando a validado
				$object->status = 3;
				//update
				$object->update($user);
				$action = '';
				//header("Location: card.php?id=".$_GET['id']);

			}

			// assignloc
			if ($action == 'asignloc')
			{
				$form = new Form($db);
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,
					$langs->trans("Assignment internal"),
					$langs->trans("Confirm assignment internal").' '.$object->ref,
					"confirm_asignloc",'',1,2);
				if ($ret == 'html') print '<br>';
			}

			// Confirm delete third party
			if ($action == 'delete')
			{
				$form = new Form($db);
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,
					$langs->trans("Deleteworkorder"),
					$langs->trans("Confirmdeleteworkorder").' '.$object->ref.' '.$object->email,
					"confirm_delete",'',0,2);
				if ($ret == 'html') print '<br>';
			}
			// Confirm delete third party
			if ($action == 'validate')
			{
				$form = new Form($db);
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,
					$langs->trans("Validateworkorder"),
					$langs->trans("ConfirmValidateworkorder").' '.$object->ref,
					"confirm_validate",'',0,2);
				if ($ret == 'html') print '<br>';
			}

			// Confirm delete order
			if ($action == 'delorder')
			{
				$form = new Form($db);
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&idr='.GETPOST('idr'),$langs->trans("Deleteitemorder"),$langs->trans("Confirmdeleteitemorder".' '.$object->ref.' '.$object->email),"confirm_delete_order",'',0,2);
				if ($ret == 'html') print '<br>';
			}

			// Confirm delete material used
			if ($action == 'delused')
			{
				$form = new Form($db);
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&idr='.GETPOST('idr'),$langs->trans("Deletematerialused"),$langs->trans("Confirmdeleteworkorder".' '.$object->ref.' '.$object->email),"confirm_delete_used",'',0,2);
				if ($ret == 'html') print '<br>';
			}

			// Confirm delete third party
			if ($action == 'deladh')
			{
				if ($objJobsuser->fetch($idu) > 0)
				{
					$nameadh = '';
					if ($objUser->fetch($objJobsuser->fk_user) > 0)
						$nameadh = $objUser->lastname.' '.$objUser->firstname;

					$form = new Form($db);
					$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&idu='.$idu,$langs->trans("Deleteteuser"),$langs->trans("Confirmdeleteuser".' '.$nameadh),"confirm_delete_user",'',0,2);
					if ($ret == 'html') print '<br>';
				}
			}

			$linkback = '<a href="'.DOL_URL_ROOT.'/mant/jobs/list.php">'.$langs->trans("BackToList").'</a>';
			dol_banner_tab($object, 'ref', $linkback, ($user->societe_id?0:1), 'ref');


			print '<table class="border" width="100%">';

			if (!empty($objwork->ref))
			{
				print '<tr><td width="20%">'.$langs->trans('Ticketnumber').'</td><td colspan="2">';
				print $objwork->getNomUrl();
				print '</td></tr>';
			}

			// ref numeracion automatica de la OT
			//print '<tr><td width="20%">'.$langs->trans('Jobsordernumber').'</td><td class="valeur" colspan="2">';
			//$linkback = '<a href="'.DOL_URL_ROOT.'/mant/jobs/list.php">'.$langs->trans("BackToList").'</a>';

			//print $object->ref;
			//print '</td></tr>';



			// print $object->ref;
			// print '</td></tr>';

			//email
			if ($action != 'editregjobs')
			{
				if ($object->email)
				{
					print '<tr><td width="20%">'.$langs->trans('Email').'</td><td colspan="2">';
					print $object->email;
					print '</td></tr>';
				}
			}

			if ($object->fk_equipment>0)
			{
				print '<tr><td width="20%" >'.$langs->trans('Equipment').'</td><td colspan="2">';
				$reseq = $objEquipment->fetch($object->fk_equipment);
				if ($reseq>0)
					print $objEquipment->ref.' '.$objEquipment->label;
				else
					print '&nbsp;';
				print '</td></tr>';
			}
			else
			{
				// property
				$objProperty->fetch($object->fk_property);
				print '<tr><td >'.$langs->trans('Property').'</td><td colspan="2">';
				if ($objProperty->id == $object->fk_property)
					print $objProperty->ref;
				else
					print '&nbsp;';
				print '</td></tr>';

				// location
				$objLocation->fetch($object->fk_location);
				print '<tr><td >'.$langs->trans('Location').'</td><td colspan="2">';
				if ($objLocation->id == $object->fk_location)
					print $objLocation->detail;
				else
					print '&nbsp;';
				print '</td></tr>';

			}
			// solicitante
			$objAdherent->fetch($object->fk_member);
			print '<tr><td >'.$langs->trans('Solicitante').'</td><td colspan="2">';
			if ($objAdherent->id == $object->fk_member)
				print $objAdherent->lastname.' '.$objAdherent->firstname;
			else
				print '&nbsp;';
			print '</td></tr>';
			// departamento
			if ($object->fk_departament>0)
			{
				$resd = $objDepartament->fetch($object->fk_departament);
				print '<tr><td >'.$langs->trans('Departament').'</td><td colspan="2">';
				if ($resd>0)
					print $objDepartament->getNomUrl();
				else
					print '&nbsp;';
				print '</td></tr>';
			}
			//internal
			if ($action != 'editregjobs')
			{
				if (!empty($object->internal))
				{
					print '<tr><td width="20%">'.$langs->trans('Internal').'</td><td colspan="2">';
					print $object->internal;
					print '</td></tr>';
				}
			}


			// Especiality
			if ($action != 'editregjobs' && $abc)
			{
				print '<tr><td >'.$langs->trans('Speciality').'</td><td colspan="2">';
				print select_speciality($object->speciality,'speciality','',0,1);
				print '</td></tr>';
				 // Group task
				print '<tr><td>'.$langs->trans('Grouptask').'</td><td colspan="2">';
				print ($object->group_task==1?$langs->trans('Yes'):$langs->trans('No'));
				print '</td></tr>';
			}

			//typerepaiir
			print '<tr><td >'.$langs->trans('Typerepair').'</td><td colspan="2">';
			$restr=$objTyperepair->fetch($object->fk_type_repair);
			if ($restr>0)
				print $objTyperepair->ref.' '.$objTyperepair->label;
			print '</td></tr>';

			//descripcion
			print '<tr><td>'.$langs->trans('Detailtheproblem').'</td><td colspan="2">';
			print $object->detail_problem;
			print '</td></tr>';


			//departamento asignado
			if ($action == 'asigndep' && $user->rights->mant->tick->asst)
			{
				print '<tr><td '.($action=='asigndet'?'class="fieldrequired"':'').'>'.$langs->trans('Departamentassigned').'</td><td colspan="2">';
				print '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form_index">';
				print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
				print '<input type="hidden" name="action" value="updateassigndpto">';
				print '<input type="hidden" name="id" value="'.$id.'">';

				print $form->select_departament($object->fk_departament_assign,'fk_departament_assign','',0,1,'',0);
				print '<input type="submit" class="button" value="'.$langs->trans('Save').'">';
				print '</form>';
				print '</td></tr>';
			}
			else
			{
				if ($object->fk_departament_assign>0)
				{
					print '<tr><td >'.$langs->trans('Departamentassigned').'</td><td colspan="2">';
					$objDepartament->fetch($object->fk_departament_assign);
					print $objDepartament->getNomUrl();
					print '</td></tr>';
				}
			}

			if ($object->status > 0)
			{
				print '<tr><td '.($action=='asignsoc'?'class="fieldrequired"':'').'>'.$langs->trans('Assignedto').'</td><td colspan="2">';

				//empresa asignada
				if ($action == 'asignsoc' && $user->rights->mant->tick->ass)
				{
					print '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form_index">';
					print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
					print '<input type="hidden" name="action" value="updateassign">';
					print '<input type="hidden" name="id" value="'.$id.'">';

					print $form->select_company($object->fk_soc,'fk_soc','',1);
					print ' '.$langs->trans('Emptytoassigninternaltechnical');
					print '<input type="submit" class="button" value="'.$langs->trans('Save').'">';
					print '</form>';
				}
				else
				{
					if ($object->fk_soc>0)
					{
					// fk_soc
						$objSoc->fetch($object->fk_soc);
						if ($objSoc->id == $object->fk_soc)
						{
						//validamos el typent
							$objTypent = fetch_typent($objSoc->typent_id);

							if ($objTypent->id == $objSoc->typent_id)
							{
								$object->typent = $objTypent->code;
								$object->typent_id = $objTypent->id;
							}
							else
							{
								$object->typent = '';
								$object->typent_id = '';
							}
							$objSoc->fetch($object->fk_soc);
							if ($objSoc->id == $object->fk_soc) print $objSoc->nom;
							else
							{
								if ($object->fk_soc <0)
									print $langs->trans('Samecompany');
								else
									print '&nbsp;';
							}
						}
					}
					elseif ($object->fk_soc <0)
						print $langs->trans('Internalassignment');
				}
				print '</td></tr>';


				/*
				if ($action != 'editregjobs')
				{
					print '<tr><td >'.$langs->trans('Assigned').'</td><td colspan="2">';
					if ($object->fk_soc == -1)
						print $langs->trans('Internalassignment');
					else
						print $objSoc->name;
					print '</td></tr>';
				}
				*/
			}

			// Statut
			if ($action != 'editregjobs')
			{
				print '<tr><td>'.$langs->trans("Status").'</td><td colspan="2">'.$object->getLibStatut(6).'</td></tr>';
			}
			print "</table>";

			if ($object->status == 2 && $action == 'asignjobs')
			{
				if (!$lTechnic)
				{
					//registro de tecnicos internos
					include DOL_DOCUMENT_ROOT.'/mant/jobs/tpl/adduser.tpl.php';
				}
				else
				{
					$aContact = $objSoc->contact_array();
					$objJobsContact = new Mworkrequestcontact($db);
					dol_fiche_head($head, 'card', $langs->trans("Assignwork"), 0, 'mant');
					include DOL_DOCUMENT_ROOT.'/mant/request/tpl/addcontact.tpl.php';
				}
				print '</div>';
			}

			print '</div>';


			/* ****************************************** */
			/*                                            */
			/* Barre d'action                             */
			/*                                            */
			/* ****************************************** */

			print "<div class=\"tabsAction\">\n";

			if ($action == '')
			{
				if ($user->rights->mant->jobs->crear && $object->status == 0)
					print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=edit&id='.$object->id.'">'.$langs->trans("Modify").'</a></div>';
				else
					print '<div class="inline-block divButAction"><a class="butActionRefused" href="#">'.$langs->trans("Modify").'</a></div>';

				if ($object->status==0 && $user->rights->mant->jobs->del)
					print '<div class="inline-block divButAction">'."<a class=\"butActionDelete\" href=\"card.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>".'</div>';
				else
					print '<div class="inline-block divButAction">'."<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>".'</div>';
				// Valid
				if ($object->status == 0 && $user->rights->mant->jobs->val)
				{
					print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=validate">'.$langs->trans('Validate').'</a>';
				}
				// ReValid
				if ($object->status == 1 && $user->rights->mant->jobs->val)
				{
					if ($user->societe_id > 0)
					{
						if ($object->fk_user_create == $user->id)
							print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=revalidate">'.$langs->trans('Do not validate').'</a>';
					}
					elseif ($user->admin)
						print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=revalidate">'.$langs->trans('Do not validate').'</a>';
				}

					//rechazar trabajos asignados //solo empresa asignada y/o tecnico asignado
				if ($object->status == 1 && $user->rights->mant->jobs->rechasig || ($object->status == 2 && $user->rights->mant->jobs->rechasig && $object->fk_soc != -1))
				{
					print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=rechasignjobs">'.$langs->trans('Rejectworkorder').'</a>';
				}
				// asignar ticket a empresa
				if ($user->rights->mant->tick->ass && ($object->status == 1 && ($object->fk_soc >=0 || is_null($object->fk_soc))))
				{
					print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=asignsoc&amp;id='.$object->id.'">'.$langs->trans('Assignment external').'</a>';
				}
				if ($user->rights->mant->tick->ass && ($object->status == 1 && ($object->fk_soc <0 || is_null($object->fk_soc))))
				{
					print '&nbsp;<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=asignloc&amp;id='.$object->id.'">'.$langs->trans('Assignment internal').'</a>';
				}

				// asignar trabajos a tecnicos
				if ($object->status == 2 && $user->rights->mant->tick->asst)
				{
					$lTechnic = false;
					if ($object->fk_soc <=0) $lTechnic = false; else $lTechnic = true;
					//para versiones anteriores
					if ($object->fk_soc > 0)
					{
						$socid = $object->fk_soc;
						$objSoc = new Societe($db);
						$lSelectSocid = true;
						$res = $objSoc->fetch($object->fk_soc);
						if ($res > 0)
							$objTypent = fetch_typent($objSoc->typent_id);
						if ($objTypent->id == $objSoc->typent_id && $objTypent->code == 'TE_BCB')
						//asignjobsdet
							$lTechnic = false;
					}
					//if (empty($user->societe_id) && !$lTechnic)
					//	print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=asigndep&amp;id='.$object->id.'">'.$langs->trans('Assignment departament').'</a>';


					//if ((empty($user->societe_id) && !$lTechnic) || (!empty($user->societe_id) && $lTechnic == true))
					//	print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=asignjobs&amp;id='.$object->id.'">'.$langs->trans('Technicassign').'</a>';
				}
				//programacion de trabajos
				if ($object->status == 3 && $user->rights->mant->tick->prog )
				{
					print '<a class="butAction" href="'.DOL_URL_ROOT.'/mant/jobs/cardprog.php'.'?id='.$object->id.'">'.$langs->trans('Programming').'</a>';
				}

				//ejecutar trabajo

				//impres ot
				if ($object->status == 5)
				{
					print '<a class="butAction" href="'.DOL_URL_ROOT.'/mant/jobs/fiche_excel.php'.'?id='.$object->id.'">'.$langs->trans('Spreadsheet').'</a>';
				}

				// open jobs
				if (($object->status == 5 || $object->status == 8) && $user->rights->mant->jobs->openwork)
				{
					print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=openwork">'.$langs->trans('Openwork').'</a>';
				}

			}

			print '</div>';


			if ($object->status == 9)
			{
				dol_fiche_head($head, 'card', $langs->trans("Refused"), 0, 'mant');
				print '<table class="border" width="100%">';
				print '<tr>';

				print '<td>';
				print $langs->trans('Cause for the return');
				print '</td>';

				print '<td>';
				print $object->description_job;
				print '</td>';
				print '</tr>';
				print '</table>';

				print '</div>';
			}

			  // //refuse
			// if ($object->status == 1 && $action == 'refuse')
			  //   {
			//     dol_fiche_head($head, 'card', $langs->trans("Refusework"), 0, 'mant');

			//     print '<form action="card.php" method="POST" name="form_index">';
			//     print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			//     print '<input type="hidden" name="action" value="refusesend">';

			//     print '<input type="hidden" name="id" value="'.$object->id.'">';

			//     print '<table class="border" width="100%">';
			//     print '<tr class="liste_titre">';

			//     print '<td>';
			//     print $langs->trans('Cause for the return');
			//     print '</td>';

			//     print '<td>';
			//     print '<textarea name="description_job" cols="80" rows="5">'.$object->description_job.'</textarea>';
			//     print '</td>';
			//     print '</tr>';
			//     print '</table>';

			//     print '<center><br><input type="submit" class="button" value="'.$langs->trans("Send").'"></center>';

			//     print '</form>';
			//   }

			if ($action == 'rechasignjobs' && ($object->status == 1 && $user->rights->mant->jobs->rechasig || ($object->status == 2 && $user->rights->mant->jobs->rechasig && $object->fk_soc != -1)))
			{
				//justificacion rechazo

				print_fiche_titre($langs->trans('Rejectworkorder'));
				dol_fiche_head();

				print '<form action="card.php" method="POST" name="form_index">';
				print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

				print '<input type="hidden" name="id" value="'.$object->id.'">';
				print '<input type="hidden" name="action" value="updaterech">';

				print '<table class="border" width="100%">';
				print '<tr>';

				print '<td width="20%">';
				print $langs->trans('Reasonforrejection');
				print '</td>';

				print '<td>';
				print '<textarea name="description_prog" cols="40" rows="5">'.$object->description_prog.'</textarea>';
				print '</td>';
				print '</tr>';
				print '</table>';

				print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'">';
				print '&nbsp;';
				print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';

				print '</form>';
				dol_fiche_end();
				//fin justificacion rechazo


			}

			//asignacion de trabajos a externo o interno
			if (is_null($object->fk_soc) && $ABC)
			{
				if (($object->status == 2 && $action == 'asignjobs' && $user->societe_id <= 0) || ($object->status == 1 && $action == 'asignjobs') || ($object->status == 2 && $action == 'asignjobsdet'))
				{
				//solo para asignacion interno //asigna a externos o internos
					$lSelectSocid = false;

					if (empty($object->fk_soc)) $lSelectSocid = true;

					dol_fiche_head($head, 'card', $langs->trans("Assignwork"), 0, 'mant');

					print '<table class="border" width="100%" style="vertical-align:text-top;">';
					print '<tr><td width="50% "">';

					print "\n".'<script type="text/javascript" language="javascript">';
					print '$(document).ready(function () {
						$("#selectfk_soc").change(function() {
							document.form_index.action.value="updatesocedit";
							document.form_index.submit();
						});
					});';
					print '</script>'."\n";

					print '<form action="card.php" method="POST" name="form_index">';
					print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
					if ($action == 'asignjobs')
						print '<input type="hidden" name="action" value="updatesocid">';
					if ($action == 'asignjobsdet')
						print '<input type="hidden" name="action" value="updateuserdet">';

					print '<input type="hidden" name="id" value="'.$object->id.'">';

					print '<table class="border" width="100%">';
					print '<tr class="liste_titre">';

					print '<td>';
					print $langs->trans('Assignto');
					print '</td>';

					print '<td>';
					print select_societe($object->fk_soc,'fk_soc','',0,1,0);
					print '</td>';
					print '</tr>';
					print '</table>';

					if ($objSoc->fetch($object->fk_soc))
					{
						$objTypent = fetch_typent($objSoc->typent_id);
					}
					if ($object->status == 2 && $action == 'asignjobs')
						$action = 'asignjobsdet';
					//echo $action.' '.$objTypent->id.' '.$objSoc->typent_id.' '.$objTypent->code.' '.$object->status;
					// if (($object->fk_soc == -2 && $action == 'asignjobsdet') || ($objTypent->id == $objSoc->typent_id && $objTypent->code == 'TE_BCB' && $action == 'asignjobsdet')) //asignjobsdet
					if (($objTypent->id == $objSoc->typent_id && $objTypent->code == 'TE_BCB' && $action == 'asignjobsdet'))
					//asignjobsdet
					{
						print '<table class="border" width="100%">';
						print '<tr class="liste_titre">';
						print_liste_field_titre($langs->trans("Name"),"", "","","","");
						print_liste_field_titre($langs->trans("Charge"),"", "","","","");
						print_liste_field_titre($langs->trans("Action"),"", "",'','','align="right"');
						print "</tr>\n";
						//registro nuevo
						print '<tr>';
						print '<td colspan="3">';
						//recuperamos solo los que son miembros
						$aArrayMember = list_user_member('fk_member > 0');
						print $form->select_users($iduser,'fk_user',1,'',0,$aArrayMember);
						print '</td>';

						print '</tr>';

						$objJobsuser = new Mjobsuser($db);
						$objUser = new User($db);
						$aArray = $objJobsuser->list_jobsuser($id);
						$numberuser = 0;
						foreach ((array) $aArray AS $i => $objJuser)
						{

							$objUser->fetch($objJuser->fk_user);
							$numberuser++;
							print '<tr>';
							print '<td>';
							if ($objUser->id == $objJuser->fk_user)
								print $objUser->lastname.' '.$objUser->firstname.'</td>';
							else
								print '&nbsp;';
							print '</td>';
							print '<td>'.$objJuser->detail.'</td>';
							print '<td align="right">'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&amp;idu='.$objJuser->id.'&amp;action=deladh'.'">'.img_picto($langs->trans('Delete'),'delete').'</a>'.'</td>';
							print '</tr>';
						}
						print "</table>";
					}
					if ($object->status == 1)
						print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'"></center>';
					else
						print '<center><br><input type="submit" class="button" value="'.$langs->trans("Saveuser").'"></center>';
					print '</form>';

					print '</td><td width="50%" valign="top">';

					if ($object->fk_soc == -2 && $action == 'asignjobsdet' || ($objTypent->id == $objSoc->typent_id && $objTypent->code == 'TE_BCB' && $object->status > 1))
					{
						print '<form action="card.php" method="POST">';
						print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
						print '<input type="hidden" name="action" value="upassignjobs">';
						print '<input type="hidden" name="actionant" value="asignjobsdet">';

						print '<input type="hidden" name="id" value="'.$object->id.'">';

						print '<table class="border" width="99%">';

						// Especiality
						print '<tr><td >'.$langs->trans('Speciality').'</td><td colspan="2">';
						print select_speciality((empty($object->speciality_assign)?$object->speciality:$object->speciality_assign),'speciality_assign','',1);
						print '</td></tr>';
						//descripcion
						print '<tr><td >'.$langs->trans('Assignedwork').'</td><td colspan="2">';
						print '<textarea name="description_assign" cols="40" rows="5">'.$object->description_assign.'</textarea>';
						print '</td></tr>';

						// datecomunic
						print '<tr><td class="fieldrequired">'.$langs->trans('Dateassigned').'</td><td colspan="2">';
						$form->select_date($object->date_assign,'di_','','','',"dateassign",1,1);
						print '</td></tr>';



						print "</table>";

						print '<center><br><input type="submit" class="button" value="'.$langs->trans("Saveworktobeperformed").'"></center>';

						print '</form>';
					}
					print '</div>';
					print '</tr>';
					print '</table>';
					//print '</section>';
					print '</div>';

				}
			}


			//registro de programacion para externos
			if ($object->status == 2 && $action == 'editjobs' || ($object->status == 2 && $action == 'asignjobs' && $user->societe_id > 0) || ($object->status == 3 && $action == 'asignjobs' && $user->societe_id > 0) && empty($object->date_ini) )
			{
				if ($user->societe_id > 0 && $ABC)
				{
					$objSoc = new Societe($db);
					$lSelectSocid = false;

					if (empty($socid))
						$lSelectSocid = true;
					$socidnew = GETPOST('socidnew');
					if (!empty($socidnew))
					{
						$socid = $socidnew;
						$_SESSION['socidnew'] = $socidnew;
					}
					if ($socid)
						$objSoc->fetch($socid);
					elseif($_SESSION['socidnew'])
						$objSoc->fetch($_SESSION['socidnew']);

					$aContact = $objSoc->contact_array();
					$objJobsContact = new Mjobscontact($db);
					dol_fiche_head($head, 'card', $langs->trans("Assignwork"), 0, 'mant');

					// print '<table class="border" width="100%" style="vertical-align:text-top;">';
					// print '<tr><td>';
					if ($lSelectSocid)
					{
						print "\n".'<script type="text/javascript" language="javascript">';
						print '$(document).ready(function () {
							$("#selectsocidnew").change(function() {
								document.form_index.action.value="updateedit";
								document.form_index.submit();
							});
						});';
						print '</script>'."\n";
					}
					print '<form action="card.php" method="POST" name="form_index">';
					print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
					print '<input type="hidden" name="action" value="addcontact">';
					print '<input type="hidden" name="id" value="'.$object->id.'">';
					if ($lSelectSocid)
					{
						print '<table class="border" width="100%">';
						print '<tr class="liste_titre">';
						print '<td>';
						print select_societe((empty($_SESSION['socidnew'])?$socidnew:$_SESSION['socidnew']),'socidnew','',0,1);
						print '</td>';
						print '</tr>';
						print '</table>';
					}

					print '<table class="border" width="100%">';
					print '<tr class="liste_titre">';
					print_liste_field_titre($langs->trans("Name"),"", "","","","");
					print_liste_field_titre($langs->trans("Charge"),"", "","","","");
					print_liste_field_titre($langs->trans("Action"),"", "",'','','align="right"');
					print "</tr>\n";
					//registro nuevo
					print '<tr>';
					print '<td colspan="2">';
					print $form->selectarray('fk_contact',$aContact,GETPOST('fk_contact'),1);
					print '</td>';
					print '<td align="right">';
					print '<input type="image" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/mant/img/save.png" width="14" height="14">';
					print '</td>';
					print '</tr>';

					$aArray = $objJobsContact->list_contact($object->id);

					$numberContact = 0;
					$var = true;
					foreach ((array) $aArray AS $i => $data)
					{
						$var=!$var;
						$numberContact++;
						print "<tr $bc[$var]>";
						print '<td>'.$aContact[$data->fk_contact].'</td>';
						print '<td></td>';
						print '<td></td>';
						print '</tr>';
					}
					print "</table>";

				// print '<center><br><input type="submit" class="button" value="'.$langs->trans("Savecontact").'"></center>';

					print '</form>';
				// print '</td>';
				// print '</tr>';
				// print '<tr><td>&nbsp;</td></tr>';

				// print '<tr><td>';
					print '</div>';
				}

				//PROGRAMACION DE TRABAJOs
				if ($object->status == 2 && $action == 'editjobs' || ($object->status == 2 && $action == 'asignjobs' && $user->societe_id > 0) )
				{
					//include_once DOL_DOCUMENT_ROOT.'/mant/jobs/tpl/programation.tpl.php';
				}
			}


			/* ****************************************** */
			/*                                            */
			/* Barre d'action                             */
			/*                                            */
			/* ****************************************** */

			print "<div class=\"tabsAction\">\n";
			//realizar la comunicacion via correo de la programacion
			if ($action == 'asignjobs' && $user->rights->mant->jobs->assignjobs || $action == 'editjobs' && $user->rights->mant->jobs->upjobs )
			{
				if (($user->societe_id > 0 && $numberContact > 0 && !empty($object->date_ini_prog)) || ($user->societe_id <= 0 && !empty($object->date_ini_prog)))
				{
					$lContinue = true;
					if ($lImage && empty($object->image_ini))
						$lContinue = false;
					if ( $object->status == 2 && $lContinue)
					{
						print '<form action="card.php" method="POST">';
						print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
						print '<input type="hidden" name="action" value="startjobs">';
						print '<input type="hidden" name="id" value="'.$object->id.'">';

						print '<center><br><input type="submit" class="button" value="'.$langs->trans("Sendprogramming").'"></center>';
						print '</form>';
					}
				}
			}
			print '</div>';

			//print view programation
			if ($object->status == 3 || $object->status == 4 || $object->status == 5 || $object->status == 6)
			{
				if ($action != 'editregjobs' && $ABC)
				{
					//PROGRAMACION DE TRABAJOs
					if ($object->fk_soc <=0 && $object->status ==3 && $action == 'program')
						include_once DOL_DOCUMENT_ROOT.'/mant/jobs/tpl/programation.tpl.php';
					else
						include_once DOL_DOCUMENT_ROOT.'/mant/jobs/tpl/programation_view.tpl.php';
				}
			//	if ($object->status == 4 || $object->status == 5)
			//		include_once DOL_DOCUMENT_ROOT.'/mant/jobs/tpl/material.tpl.php';
			}

			if ($object->status == 4 && $action == 'editregjobs')
			{
				//orders
				$objJobsOrder = new Mjobsorder($db);
				//$objcomm = new Commande($db);
				$aOrder = $objJobsOrder->list_order($object->id);
				$aProduct = array();

				foreach((array) $aOrder AS $k => $objtemp)
				{
					$aProduct[$objtemp->order_number][$objtemp->fk_product] = $objtemp->fk_product;
				}

				$lVieworder = false;
				if ($order_number)
				{
				//if (count($aProduct) < count($objcomm->lines))
					$lVieworder = true;
				}
				if ($lVieworder)
					include_once DOL_DOCUMENT_ROOT.'/mant/jobs/tpl/addorders.tpl.php';
				else
					include_once DOL_DOCUMENT_ROOT.'/mant/jobs/tpl/orders.tpl.php';
				//material used
				include_once DOL_DOCUMENT_ROOT.'/mant/jobs/tpl/used.tpl.php';

				//work performed
				include_once DOL_DOCUMENT_ROOT.'/mant/jobs/tpl/work_performed.tpl.php';

			}
			/* ****************************************** */
			/*                                            */
			/* Barre d'action                             */
			/*                                            */
			/* ****************************************** */

			print "<div class=\"tabsAction\">\n";
			if ($action == 'editjobs' && $object->status == 4)
			{
				if ($action == 'editjobs')
				{
					if ($numberContact > 0 && !empty($object->date_ini_prog))
					{
						if ($user->rights->mant->jobs->upjobs && $object->status == 2)
						{
							print '<form action="card.php" method="POST">';
							print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
							print '<input type="hidden" name="action" value="startjobs">';
							print '<input type="hidden" name="id" value="'.$object->id.'">';

							print '<center><br><input type="submit" class="button" value="'.$langs->trans("Beginjob").'"></center>';
							print '</form>';
						}
					}
				}

			}
			print '</div>';

			print "<div class=\"tabsAction\">\n";

			if ($action == 'editregjobs')
			{
				//close work
				$lContinue = true;
				if ($lImage && empty($object->image_fin))
					$lContinue = false;

				if ($lContinue && !empty($object->date_fin) && !empty($object->typemant))
				{
					if ($user->rights->mant->jobs->regjobs && $object->status == 4)
					{
						print '<form action="card.php" method="POST">';
						print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
						print '<input type="hidden" name="action" value="closework">';
						print '<input type="hidden" name="id" value="'.$object->id.'">';

						print '<center><br><input type="submit" class="button" value="'.$langs->trans("Closeworking").'"></center>';
						print '</form>';
					}
				}
			}
			print '</div>';

			print "<div class=\"tabsAction\">\n";

			if ($action == 'asignjobsdet')
			{
				if ($numberuser > 0 && !empty($object->date_assign))
				{
					if ($user->rights->mant->jobs->assignjobs && $object->status == 2)
					{
						print '<form action="card.php" method="POST">';
						print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
						print '<input type="hidden" name="action" value="startassignjobs">';
						print '<input type="hidden" name="id" value="'.$object->id.'">';

						print '<center><br><input type="submit" class="button" value="'.$langs->trans("Sendcommunication").'"></center>';
						print '</form>';
					}
				}
			}
			print '</div>';

		}


		//
		if (($action == 'edit' || $action == 're-edit') && 1)
		{
			if (isset($_POST['stype']))
				$stype = GETPOST('stype')+0;
			if (!isset($stype))
			{
				if ($object->fk_equipment>0) $stype=1;
				else $stype = 0;
			}

			print_fiche_titre($langs->trans("ApplicationsEdit"), $mesg);

			print "\n".'<script type="text/javascript" language="javascript">';
			print '$(document).ready(function () {
				is_stype='.$stype.';
				if (is_stype) {
					$(".equipmentline").show();
					$(".propertyline").hide();
					$(".locationline").hide();
				} else {
					$(".propertyline").show();
					$(".locationline").show();
					$(".equipmentline").hide();
				}

				$("#fk_property").change(function() {
					document.form_mod.action.value="edit";
					document.form_mod.submit();
				});
				$("#fk_equipment").change(function() {
					document.form_mod.action.value="edit";
					document.form_mod.submit();
				});
				$("#stype").change(function() {
					document.form_mod.action.value="edit";
					document.form_mod.submit();
				});
			});';
			print '</script>'."\n";



			print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST" name="form_mod">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="updatejobs">';
			print '<input type="hidden" name="id" value="'.$object->id.'">';
			print '<input type="hidden" name="fk_soc" value="'.$objwork->fk_soc.'">';

			print '<table class="border" width="100%">';

			// ref numeracion automatica de la OT
			print '<tr><td class="fieldrequired" width="20%">'.$langs->trans('Jobsordernumber').'</td><td colspan="2">';
			print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="13" maxlength="15">';
			print '</td></tr>';

			//selector de equipo o inmueble
			$aSelect = array(0=>$langs->trans('Property'),1=>$langs->trans('Equipment'));
			print '<tr><td  class="fieldrequired" width="20%">'.$langs->trans('Mantenimiento').'</td><td colspan="2">';
			print $form->selectarray('stype',$aSelect,$stype);
			print '</td></tr>';
			//equipment
			print '<tr class="equipmentline"><td  class="fieldrequired" width="20%">'.$langs->trans('Equipment').'</td><td colspan="2">';
			/*
			$res = $objEquipment->fetchAll('ASC', 'label',0,0,array('entity'=>$conf->entity,'status'=>1),'AND','',false);
			$options = '<option>'.$langs->trans('Select').'</option>';
			if($res>0)
			{
				foreach($objEquipment->lines AS $j => $line)
				{
					$selected = '';
					if(GETPOST('fk_equipment') == $line->id) $selected = ' selected';
					$options.= '<option value="'.$line->id.'" '.$selected.'>'.$line->label.'</option>';
				}
			}
			print '<select id="fk_equipment" name="fk_equipment">'.$options.'</select>';
			*/
			print $form->select_equipment(($fk_equipment?$fk_equipment:$object->fk_equipment), 'fk_equipment', '', 20, 0, 0, 2, '', 1, array(),0,'','',0);

			print '</td></tr>';

			if ($fk_equipment>0)
			{
				$objEquipment->fetch($fk_equipment);
				$fk_location = $objEquipment->fk_location;
				$_GET['fk_location'] = $fk_location;
				if ($fk_location>0)
				{
					$objLocation->fetch($fk_location);
					$fk_property = $objLocation->fk_property;
					$_GET['fk_property'] = $fk_property;
				}
			}
			// property
			$fk_property = GETPOST('fk_property')?GETPOST('fk_property'):$fk_property;

			print '<tr class="propertyline"><td class="fieldrequired">'.$langs->trans('Property').'</td><td colspan="2">';
			if (!empty($idw))
			{
				if ($object->fk_property)
				{
					if ($objProperty->fetch($object->fk_property) > 0)
						print $objProperty->ref;
				}
				print '<input type="hidden" name="fk_property" value="'.$object->fk_property.'">';
			}
			else
			{
				$filter = " AND t.entity = ".$conf->entity;
				$res = $objProperty->fetchAll('ASC','label',0,0,array('status'=>1),'AND',$filter);
				$options = '<option value="-1">'.$langs->trans('Selectproperty').'</option>';
				$lines =$objProperty->lines;
				foreach ((array) $lines AS $j => $line)
				{
					$selected = '';
					if ($fk_property == $line->id) $selected = ' selected';
					$options.= '<option value="'.$line->id.'" '.$selected.'>'.$line->label.' ('.$line->ref.')'.'</option>';
				}
				print '<select id="fk_property" name="fk_property">'.$options.'</select>';
				//print $objProperty->select_property(GETPOST('fk_property'),'fk_property','',40,1);
			}
			print '</td></tr>';

			// location
			print '<tr class="locationline"><td class="fieldrequired">'.$langs->trans('Location').'</td><td colspan="2">';
			if (!empty($idw))
			{
				if ($object->fk_location)
				{
					if ($objLocation->fetch($object->fk_location) > 0)
						print $objLocation->detail;
				}
				print '<input type="hidden" name="fk_location" value="'.$object->fk_location.'">';
			}
			else
			{
				$filter = " AND t.fk_property = ".$fk_property;
				$res = $objLocation->fetchAll('ASC','detail',0,0,array('status'=>1),'AND',$filter);
				$options = '';
				$lines =$objLocation->lines;
				foreach ((array) $lines AS $j => $line)
				{
					$selected = '';
					if (GETPOST('fk_location') == $line->id) $selected = ' selected';
					$options.= '<option value="'.$line->id.'" '.$selected.'>'.$line->detail.'</option>';
				}
				print '<select id="fk_location" name="fk_location">'.$options.'</select>';
				//print $objLocation->select_location(GETPOST('fk_location'),'fk_location','',40,1,GETPOST('fk_property'));
			}
			print '</td></tr>';


			// solicitante
			print '<tr><td class="fieldrequired">'.$langs->trans('Solicitante').'</td><td colspan="2">';
			print select_adherent($object->fk_member,'fk_member','',0,1);
			print '</td></tr>';

			// ref numeracion automatica de la OT
			print '<tr><td>'.$langs->trans('Internal').'</td><td colspan="2">';
			print '<input id="internal" type="text" value="'.$object->internal.'" name="internal" size="5">';
			print '</td></tr>';


			//type repair
			print '<tr><td class="fieldrequired">'.$langs->trans('Typerepair').'</td><td colspan="2">';
			print $form->select_type_repair($object->fk_type_repair,'fk_type_repair','',0,1,'');
			print '</td></tr>';

			print '<tr><td class="fieldrequired">'.$langs->trans('Detailtheproblem').'</td><td colspan="2">';
			print '<input type="text" name="detail_problem" value="'.$object->detail_problem.'" size="70">';
			print '</td></tr>';


			 // Especiality
			//print '<tr><td class="fieldrequired">'.$langs->trans('Speciality').'</td><td colspan="2">';
			//print select_speciality($object->speciality,'speciality','',1);
			//print '</td></tr>';

			// Group task
			print '<tr><td>'.$langs->trans('Grouptask').'</td><td colspan="2">';
			print $form->selectyesno('group_task',($object->group_task==1?'yes':'no'),0,false);
			print '</td></tr>';


			print '</table>';

			print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
			print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';

			print '</form>';

		}
	}
}

llxFooter();

$db->close();
?>
