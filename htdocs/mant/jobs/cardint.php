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

require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobscontact.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsuser.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mtyperepair.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsorderext.class.php';

require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsmaterialusedext.class.php';


if ($conf->orgman->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/pcharge.class.php';
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentuserext.class.php';
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
$langs->load("orgman");
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
$objWorkuser = new Mworkrequestuser($db);
$objWorkcont = new Mworkrequestcontact($db);

$objJobuser = new Mjobsuser($db);
$objJobscontact = new Mjobscontact($db);

$objCharge = new Pcharge($db);
$objDepartament = new Pdepartamentext($db);
$objTyperepair = new Mtyperepair($db);
$objAdherent = new Adherent($db);

if ($conf->assets->enabled)
	$objassets = new Assetsext($db);

if ($id) $object->fetch($id);

/*
 * Actions
 */
	   		// Confirmation departament assign
if ($action == 'confirm_assigndpto' && $_REQUEST['confirm'] == 'yes')
{
 		// on verifie si l'objet est en numerotation provisoire
 		//$object = new Solalmacen($db);
	$object->fetch(GETPOST('id'));
		//cambiando a validado
		//cambiando a programado
	$object->fk_departament_assign = GETPOST('fk_departament_assign','int');
 		//update
	$db->begin();
	$res = $object->update($user);
	if ($res <=0)
	{
		$error++;
		setEventMessages($object->error,$object->errors,'errors');
	}
	if (!$error)
	{
		//vamos a agregar a todos los que estan dentro del departamento
		$objDepartamentuser = new Pdepartamentuserext($db);
		$filterstatic = " AND t.fk_departament = ".$object->fk_departament_assign;
		$res = $objDepartamentuser->fetchAll('','',0,0,array('active'=>1),'AND',$filterstatic,false);
		if ($res > 0)
		{
			$lines = $objDepartamentuser->lines;
			foreach ($lines AS $j => $line)
			{
				if (!$error)
				{
					//nuevo
					$objJobuser->fk_user = $line->fk_user;
					$objJobuser->fk_jobs = $id;
					$objJobuser->status      = 1;
					$objJobuser->fk_user_create = $user->id;
					$objJobuser->fk_user_mod = $user->id;
					$objJobuser->datec = dol_now();
					$objJobuser->datem = dol_now();
					$objJobuser->tms = dol_now();
					$result = $objJobuser->create($user);
					if ($result <=0)
					{
						$error++;
						setEventMessages($objJobuser->error,$objJobuser->errors,'errors');
					}
				}
			}
		}
	}
	if (!$error)
	{
		$db->commit();
		setEventMessages($langs->trans('Succesfullupdate'),null,'mesgs');
		header('Location: '.$_SERVER['PHP_SELF'].'?id='.$object->id);
		exit;
	}
	else
	{
		$db->rollback();
		setEventMessages($langs->trans('It is not possible to assign the department and its dependents'),null,'warnings');
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
			header("Location: liste.php");
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
		$object->fk_soc = 0;
		$object->fk_member = GETPOST('fk_member','int');
		$object->fk_equipment = GETPOST('fk_equipment','int');
		$object->fk_departament = GETPOST('fk_departament','int');
		$object->fk_property = GETPOST('fk_property','int');
		$object->fk_location = GETPOST('fk_location','int');
		$object->email = $user->email;
		$object->internal = GETPOST('internal','int')+0;
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
	//if (empty($object->speciality))
	//{
	//	$error++;
	//	$mesg.='<div class="error">'.$langs->trans('Error, speciality is required').'</div>';
	//}
	if (empty($error))
	{
		$id = $object->create($user);
		if ($id > 0)
		{
			header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
			exit;
		}
		$action = 'create';
		$mesg='<div class="error">'.$object->error.'</div>';
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
			$objUser->fetch($objjus->fk_user);
			if ($objUser->id == $objjus->fk_user)
			{
				if (!empty($emailto)) $emailto.= ',';
				$emailto.= $objUser->email;
			}
		}
		$emailto = '';
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
if ($action == 'upassignreq' && $user->rights->mant->jobs->assignjobs)
{
	$object->fetch(GETPOST('id'));
	$statut = $object->status;

	//recuperamos a todos los tecnicos internos asigandos
	$objUser = new User($db);
	$filterstatic = " AND t.fk_jobs = ".$object->id;

	$resu = $objJobuser->fetchAll('ASC', 'datec', 0,0,array(1=>1),'AND',$filterstatic);
	$aArray = $objJobuser->lines;
	$emailto = '';
	foreach ((array) $aArray AS $i => $objJuser)
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
			setEventMessages($langs->trans("MailSuccessfulySent",$mailfile->getValidAddress($email_from,2),$mailfile->getValidAddress($sendto,2)),null,'mesgs');
			$object->status = 3;
			$res = $object->update($user);
			if ($res>0)
			{
				setEventMessages($langs->trans('Successfulassignmentsubmission'),null,'mesgs');
				header("Location: ".$_SERVER['PHP_SELF']."?id=".$id.'&mesg='.$mesg);
				exit;
			}
			else
			{
				setEventMessages($object->error,$object->errors,'errors');
				$action = 'asignjobs';
			}
		}
		else
		{
			setEventMessages($langs->trans("ResultKo").'<br>'.$mailfile->error.' '.$result,null,'errors');
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

			$objJobsorder = new Mjobsorderext($db);
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

	$objJobsorder = new Mjobsorderext($db);
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
		$objJobsused = new Mjobsmaterialusedext($db);
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
		header("Location: ".DOL_URL_ROOT.'/mant/jobs/liste.php');
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
	$objJobsorder = new Mjobsorderext($db);

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
	$objJobsused = new Mjobsmaterialusedext($db);
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
$aArrcss = array('/mant/css/style-desktop.css');
$conf->dol_hide_leftmenu = 0;
llxHeader("",$langs->trans("Managementmant"),$help_url,'','','',$aArrjs,$aArrcss);



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

		dol_fiche_head($head, 'int', $langs->trans("Jobs"), 0, 'mant');

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
	  		// assigndep
		if ($action == 'assigndpto')
		{
			$objDepartament->fetch(GETPOST('fk_departament_assign'));
			$form = new Form($db);
			$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&fk_departament_assign='.GETPOST('fk_departament_assign','int'),
				$langs->trans("Assignment departament"),
				$langs->trans("Confirm assignment departament").' '.$object->ref.' => '.$objDepartament->label,
				"confirm_assigndpto",'',1,1);
			if ($ret == 'html') print '<br>';
		}

	  		// assignloc
		if ($action == 'asignloc')
		{
			$form = new Form($db);
			$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,
				$langs->trans("Assignment internal"),
				$langs->trans("Confirm assignment internal").' '.$object->ref,
				"confirm_asignloc",'',1,1);
			if ($ret == 'html') print '<br>';
		}

	  		// Confirm delete third party
		if ($action == 'delete')
		{
			$form = new Form($db);
			$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,
				$langs->trans("Deleteworkorder"),
				$langs->trans("Confirmdeleteworkorder".' '.$object->ref.' '.$object->email),
				"confirm_delete",'',0,2);
			if ($ret == 'html') print '<br>';
		}
	  		// Confirm delete third party
		if ($action == 'validate')
		{
			$form = new Form($db);
			$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,
				$langs->trans("Validateworkorder"),
				$langs->trans("ConfirmValidateworkorder".' '.$object->ref),
				"confirm_validate",'',0,1);
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
			print $objwork->ref;
			print '</td></tr>';
		}

	  		// ref numeracion automatica de la OT
		//print '<tr><td width="20%">'.$langs->trans('Jobsordernumber').'</td><td class="valeur" colspan="2">';
		//$linkback = '<a href="'.DOL_URL_ROOT.'/mant/jobs/liste.php">'.$langs->trans("BackToList").'</a>';

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
			print '<tr><td >'.$langs->trans('Equipment').'</td><td colspan="2">';
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
		print '<tr><td width="20%">'.$langs->trans('Solicitante').'</td><td colspan="2">';
		if ($objAdherent->id == $object->fk_member)
			print $objAdherent->lastname.' '.$objAdherent->firstname;
		else
			print '&nbsp;';
		print '</td></tr>';
	  		// departamento
		if ($object->fk_departament>0)
		{
			$resd = $objDepartament->fetch($object->fk_departament);
			print '<tr><td >'.$langs->trans('Requesting department').'</td><td colspan="2">';
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
			print '<input type="hidden" name="action" value="assigndpto">';
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

	  		// Statut
		if ($action != 'editregjobs')
		{
			print '<tr><td>'.$langs->trans("Status").'</td><td colspan="2">'.$object->getLibStatut(6).'</td></tr>';
		}
		print "</table>";
		dol_fiche_end();


		include DOL_DOCUMENT_ROOT.'/mant/jobs/tpl/member_list.tpl.php';


		/* ****************************************** */
		/*                                            */
		/* Barre d'action                             */
		/*                                            */
		/* ****************************************** */

		print "<div class=\"tabsAction\">\n";

		if ($action == '')
		{
		 		// asignar trabajos a tecnicos
			if ($object->status == 2 && $user->rights->mant->tick->asst)
			{
				$lTechnic = false;
				if ($object->fk_soc <=0) $lTechnic = false; else $lTechnic = true;

				if (empty($user->societe_id) && !$lTechnic)
				{
					if ($object->fk_departament_assign <=0)
						print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=asigndep&amp;id='.$object->id.'">'.$langs->trans('Assignment departament').'</a>';
				}

				if ((empty($user->societe_id) && !$lTechnic) || (!empty($user->societe_id) && $lTechnic == true))
					print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=asignjobs&amp;id='.$object->id.'">'.$langs->trans('Technicassign').'</a>';
			}
		 		//programacion de trabajos
			if ($object->status == 3 && $user->rights->mant->tick->prog )
			{
					print '<a class="butAction" href="'.DOL_URL_ROOT.'/mant/jobs/cardprog.php'.'?id='.$object->id.'">'.$langs->trans('Programming').'</a>';
			}

		  		//ejecutar trabajo

		  		//impres ot
			if ($object->status == 4)
			{
					//print '<a class="butAction" href="'.DOL_URL_ROOT.'/mant/jobs/fiche_excel.php'.'?id='.$object->id.'">'.$langs->trans('Excel').'</a>';
			}

		  		// open jobs
			if (($object->status == 4 || $object->status == 8) && $user->rights->mant->jobs->openwork)
			{
					//print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=openwork">'.$langs->trans('Openwork').'</a>';
			}
		}

		print '</div>';

		if ($object->status >= 2)
		{
			if (!$lTechnic)
			{
					//if ($action == 'asignjobs')
				 		//registro de tecnicos internos
						//include DOL_DOCUMENT_ROOT.'/mant/jobs/tpl/adduser.tpl.php';
			}
				//print '</div>';
		}

		if ($action == 'rechasignjobs' && ($object->status == 1 && $user->rights->mant->jobs->rechasig || ($object->status == 2 && $user->rights->mant->jobs->rechasig && $object->fk_soc != -1)))
		{
		  		//justificacion rechazo

			dol_fiche_head($head, 'card', $langs->trans("Rejectworkorder"), 0, 'mant');

			print '<table class="border" width="100%" style="vertical-align:text-top;">';
			print '<tr><td width="50% "">';

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

		  		//fin justificacion rechazo


		}

	  		//asignacion de trabajos a externo o interno
		if (is_null($object->fk_soc) && $abc)
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

				if ($object->fk_soc == -2 && $action == 'asignjobsdet' ||
					($objTypent->id == $objSoc->typent_id &&
						$objTypent->code == 'TE_BCB' && $object->status > 1))
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
			if ($user->societe_id > 0)
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
				include_once DOL_DOCUMENT_ROOT.'/mant/jobs/tpl/programation.tpl.php';
			}
		}


		/* ****************************************** */
		/*                                            */
		/* Barre d'action                             */
		/*                                            */
		/* ****************************************** */

		print "<div class=\"tabsAction\">\n";
	  		//realizar la comunicacion via correo de la programacion
		if ($action == 'asignjobs' && $user->rights->mant->jobs->assignjobs ||
			$action == 'editjobs' && $user->rights->mant->jobs->upjobs )
		{
			if (($user->societe_id > 0 && $numberContact > 0 && !empty($object->date_ini_prog)) ||
				($user->societe_id <= 0 && !empty($object->date_ini_prog)))
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
	}
}


llxFooter();

$db->close();
?>
