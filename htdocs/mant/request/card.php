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
// require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mworkrequestext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mworkrequestcontact.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mworkrequestuser.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobscontact.class.php';
// require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobsorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsuserext.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/mproperty.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/mlocation.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/pchargeext.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mequipmentext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mtyperepair.class.php';
if ($conf->assets->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/assets/class/form_assets.class.php';
}

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/mant/lib/mant.lib.php';
require_once DOL_DOCUMENT_ROOT.'/mant/lib/adherent.lib.php';
require_once DOL_DOCUMENT_ROOT.'/mant/lib/societe.lib.php';
require_once DOL_DOCUMENT_ROOT.'/mant/lib/user.lib.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/html.formadd.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';


//require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';

///require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
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

$id        = GETPOST("id");
$idu       = GETPOST("idu");
$ref       = GETPOST('ref','alpha');
$confirm = GETPOST('confirm');
$cancel = GETPOST('cancel');
$fk_equipment = GETPOST('fk_equipment');
if (! empty($user->societe_id)) $socid=$user->societe_id;
$url = $dolibarr_main_url_root;

// $sortfield = GETPOST("sortfield");
// $sortorder = GETPOST("sortorder");

// if (! $sortfield) $sortfield="p.period_month";
// if (! $sortorder) $sortorder="DESC";

$mesg = '';
if (isset($_GET['mesg']))
	$mesg = $_GET['mesg'];
$object      = new Mworkrequestext($db);
$objReqcont  = new Mworkrequestcontact($db);
$objRequser  = new Mworkrequestuser($db);
$objjobs     = new Mjobsext($db);
$objProperty = new Mproperty($db);
$objLocation = new Mlocation($db);
$objSoc      = new Societe($db);
$objEquipment= new Mequipmentext($db);
$objUser     = new User($db);
$objTyperepair = new Mtyperepair($db);

$objJobsuser = new Mjobsuserext($db);
$objAdherent = new Adherent($db);
//$objJobscontact = new Mjobscontact($db);
$objContact = new Contact($db);
if ($conf->assets->enabled)
	$objassets = new Assetsext($db);

$lIntegratedasset = false;
if ($conf->global->MANT_EQUIPMENT_INTEGRATED_WITH_ASSET)
	$lIntegratedasset = true;

/*
 * Actions
 */

if ($action == 'confirm_validate' && $_REQUEST["confirm"] == 'yes' && $user->rights->mant->tick->crear)
{
	$res = $object->fetch($id);
	$error = 0;
	$mesg='';
	//$fk_member = GETPOST('fk_member');
	//buscamos adherents

	if ($res && $object->id == $id)
	{
		$res1 = $objAdherent->fetch($object->fk_member);
		$email_a = $objAdherent->email;
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

		$object->ref = $numref;
		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->trans('No esta definido la numeración, revise'),null,'errors');
		}
		//cambiando a validado
		$object->status = 4;

		if (!$error)
		{
			$db->begin();
			$resid = $object->update($user);
			if ($resid > 0)
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

				$tmpsujet = $langs->trans('Generation of ticket');
				if ($objUser->fetch($object->fk_user_assign))
					$emailto = $objUser->email;
				$sendto   = $emailto;
				$email_from = $email_a;
				$tmpbody = htmlsendemail($id,$code,$url);
				$msgishtml = 1;
				$email_errorsto = $conf->global->MAIN_MAIL_ERRORS_TO;
				$arr_css = array('bgcolor' => '#FFFFCC');
				$mailfile = new CMailFile($tmpsujet,$sendto,$email_from,$tmpbody, $arr_file,$arr_mime,$arr_name,'', '', 0, $msgishtml,$email_errorsto,$arr_css);
				if ($conf->global->MANT_SEND_EMAIL)
					$result=$mailfile->sendfile();
				else
					$result = 1;
				if ($result>0)
				{
					$db->commit();
					$mesg='<div class="ok">'.$langs->trans("MailSuccessfulySent",$mailfile->getValidAddress($object->email_from,2),$mailfile->getValidAddress($object->sendto,2)).'</div>';
					header("Location: ".$_SERVER['PHP_SELF']."?id=".$id.'&code='.$code.'&mesg='.$mesg);
					exit;
				}
				else
				{
					$db->rollback();
					$mesg='<div class="error">'.$langs->trans("ResultKo").'<br>'.$mailfile->error.' '.$result.'</div>';
					$action = '';
				}
			}
			else
			{
				$db->rollback();
				$action = '';
				$mesg='<div class="error">'.$object->error.'</div>';
			}
		}
	}
	else
	{
		if (empty($error))
			$mesg = '<div class="error">'.$langs->trans("Error, registre su email y el numero interno, gracias.").'</div>';
		$action = '';
	}
}

// refusesend
if ($action == 'refusesend' && $user->rights->mant->jobs->rech)
{
	$object->fetch($id);

	$object->description_job = GETPOST('description_job','alpha');
	$object->status = 9;
	$object->tms = date('YmdHis');

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
			$result=$mailfile->sendfile();
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

// Add
if ($action == 'add' && $user->rights->mant->tick->crear)
{
	$error=0;
	$date_create  = dol_mktime(12, 0, 0, date('m'),  date('d'),  date('Y'));
	$code = generarcodigo(9);
	$object->address_ip     = $_SERVER['REMOTE_ADDR'];
	$object->ref            = '(PROV)'.$code;
	$object->entity         = $conf->entity;
	$object->fk_member      = GETPOST('fk_member');
	if ($object->fk_member > 0)
	{
	  //buscamos el email
		if($objAdherent->fetch($object->fk_member)>0)
		{
			if ($objAdherent->id == $object->fk_member)
				$object->email = $objAdherent->email;
		}
	}
	//$object->email          = GETPOST('email');
	$object->fk_property    = GETPOST('fk_property')+0;
	$object->fk_location    = GETPOST('fk_location')+0;
	$object->fk_equipment   = GETPOST('fk_equipment');
	$object->fk_type_repair   = GETPOST('fk_type_repair');
	$object->detail_problem = GETPOST('detail_problem');
	$object->tokenreg       = $code;
	$object->internal       = GETPOST('internal')+0;
	$object->date_create    = $date_create;
	$object->status         = 0;
	$object->fk_user_create = $user->id;
	$object->fk_user_mod 	= $user->id;
	$object->datec 			= dol_now();
	$object->datem 			= dol_now();
	$object->tms 			= dol_now();

	//verificando errores
	if ($object->fk_member<=0)
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Applicant")), null, 'errors');
	}
	if ($object->fk_equipment<=0)
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Equipment")), null, 'errors');
	}
	if ($object->fk_type_repair<=0)
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Typerepair")), null, 'errors');
	}

	if (!empty($mailDefault))
	{
		//verificamos uso de extension mail
		$aMail = explode('@',$object->email);
		if ($aMail[1] != $mailDefault)
		{
			$error++;
			$mesg='<div class="error">'.$langs->trans("ResultKo").'<br>'.$langs->trans('Error, no esta permitido el uso de ese correo.').'</div>';
		}
	}

	if (!empty($object->email) && empty($error))
	{
		$emailto = $object->email;
		//buscando el email en adherent
		//mant/lib/adherent.lib.php
		$idAdherent = adherent_fetch('',$object->email);
		if ($idAdherent>0)
		{
		//existe y recuperamos
			$objAdherent = new Adherent($db);
			$objAdherent->fetch($idAdherent);
			$object->fk_member = $idAdherent;
		//extrafields
		//mant/lib/adherent.lib.php
			$aArray = adherent_fetch_ext($idAdherent);
			$object->fk_charge = $aArray['fk_charge'];
			$object->fk_departament = $aArray['fk_departament'];
		}
	}

	if (empty($error))
	{
		$id = $object->create($user);
		if ($id > 0)
		{
			setEventMessages($langs->trans('Createdsuccessfully'),null,'mesgs');
			header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
			exit;
		}
		$action = 'create';
		setEventMessages($object->error,$object->errors,'errors');
	}
	else
	{
		$action="create";
	// Force retour sur page creation
	}
}

// update
if ($action == 'update' && $_POST["cancel"] <> $langs->trans("Cancel") && $user->rights->mant->tick->crear)
{
	if ($object->fetch($id) > 0)
	{
		$error=0;
		$object->address_ip     = $_SERVER['REMOTE_ADDR'];
		$object->fk_member      = GETPOST('fk_member');
		if ($object->fk_member > 0)
		{
			//buscamos el email
			if($objAdherent->fetch($object->fk_member)>0)
			{
				if ($objAdherent->id == $object->fk_member)
					$object->email = $objAdherent->email;
			}
		}
		$object->fk_property    = GETPOST('fk_property')+0;
		$object->fk_location    = GETPOST('fk_location')+0;
		$object->fk_type_repair   = GETPOST('fk_type_repair');
		$object->detail_problem = GETPOST('detail_problem');
		$object->internal       = GETPOST('internal')+0;
		$object->status         = 0;
		$object->tms            = dol_now();
	//verificando errores
		if ($object->fk_member<=0)
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Applicant")), null, 'errors');
		}
		if ($object->fk_equipment<=0)
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Equipment")), null, 'errors');
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
				setEventMessages($langs->trans('Successfullyupdate'),null,'mesgs');
				header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
				exit;
			}
			$action = 'edit';
			$mesg='<div class="error">'.$object->error.'</div>';
		}
		else
		{
			$action="edit";
		// Force retour sur page creation
		}
	}
}

// update
if ($action == 'updateassign' && $_POST["cancel"] <> $langs->trans("Cancel") && $user->rights->mant->tick->ass )
{
	if ($object->fetch($id) > 0)
	{
		$error=0;
		$object->fk_soc     = GETPOST('fk_soc');
		//$object->speciality = GETPOST('speciality');
		$object->status         = 2;
	//estado asignado a empresa
		$object->tms            = dol_now();
		if (empty($object->fk_soc))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans('Error, company is required').'</div>';
		}
		//if (empty($object->speciality))
		//{
		//	$error++;
		//	$mesg.='<div class="error">'.$langs->trans('Error, speciality is required').'</div>';
		//}

		if (empty($error))
		{
			$db->begin();
			$res = $object->update($user);
			if ($res > 0)
			{
				$db->commit();
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

//upjobs
// upjobs programacion del trabajo a realizar //upjobs
if ($action == 'upjobs' && $user->rights->mant->tick->prog)
{
	$actiondes = 'asignjobs';
	$actionant = GETPOST('actionant');
	$object->fetch(GETPOST('id'));

	$statut = $object->status;
	$date_ini_prog = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
	$date_fin_prog = dol_mktime(12, 0, 0, GETPOST('fi_month'),GETPOST('fi_day'),GETPOST('fi_year'));
	$object->speciality_prog  = GETPOST("speciality_prog");
	$object->date_ini_prog    = $date_ini_prog;
	$object->date_fin_prog    = $date_fin_prog;
	$object->description_prog = GETPOST('description_prog');
	$object->fk_equipment_prog = GETPOST('fk_equipment_prog','int',2);
	if (GETPOST('deletephotoini')) $object->image_ini = '';
	elseif (! empty($_FILES['photoini']['name'])) $object->image_ini = dol_sanitizeFileName($_FILES['photoini']['name']);

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

	if ($object->date_ini_prog && $object->date_fin_prog && $object->speciality_prog && $statut == 3)
	{
		$result = $object->update($user);
		if ($result > 0)
		{
			if (!empty($actionant))
			{
				header("Location: ".$_SERVER['PHP_SELF']."?id=".$id.'&action='.$actionant);
				exit;
			}
			else
			{
				//editjobs
				header("Location: ".$_SERVER['PHP_SELF']."?id=".$id.'&action='.$actiondes);
				exit;
			}
		}
		$action = $actiondes;
		setEventMessages($object->error,$object->errors,'errors');
	}
	else
	{
		setEventMessages($langs->trans("Errorlackinformation."),null,'errors');
		$action=$actiondes;
	// Force retour sur page creation
	}
}

//assign fin work
// upassignjobs
  //echo 'action '.$action.' '.$user->rights->mant->tick->asst;exit;
if ($action == 'confirm_upassignreq' && $confirm == 'yes' && $user->rights->mant->tick->asst)
{
	$object->fetch(GETPOST('id'));
	$statut = $object->status;

	//recuperamos a todos los tecnicos internos asigandos
	$objUser = new User($db);
	//$aArray = $objRequser->list_requestuser($id);
	$filter = " AND t.fk_work_request = ".$id;
	$res = $objRequser->fetchAll('','',0,0,array(1=>1),'AND',$filter);
	if ($res > 0) $lines = $objRequser->lines;
	$emailto = '';
	//print_r($aArray);
	foreach ((array) $lines AS $i => $objJuser)
	{
		$objUser->fetch($objJuser->fk_user);
		if ($objUser->id == $objJuser->fk_user && !empty($objUser->email))
		{
			if (!empty($emailto)) $emailto.= ',';
			$emailto = $objUser->email;
		}
	}
	if (empty($emailto))
		$emailto = $user->email;
	//echo $emailto.' '.$object->status;exit;
	if ($emailto && $object->status == 2)
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
		if ($conf->global->MANT_SEND_EMAIL)
			$result=$mailfile->sendfile();
		else
			$result = 1;
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
		setEventMessages($langs->trans('Technicianswithoutmail'),null,'warnings');
		setEventMessages($langs->trans('Satisfactoryupdate'),null,'mesgs');
		$object->status = 3;
		$res = $object->update($user);
		if ($res>0)
		{
			header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
			exit;
		}
		else
		{
			setEventMessages($object->error,$obejct->errors,'errors');
			$action = 'asignjobs';
		}
	// header("Location: fiche.php?id=".$id.'&action=asignjobs&mesg='.$mesg);
	// exit;
	}
}

// startjobs pasa a la etapa de programacion 3
if ($action == 'startjobs' && $user->rights->mant->tick->prog)
{
	$id = GETPOST('id');
	$object->fetch(GETPOST('id'));
	$statut = $object->status;
	$object->status       = 4; //programado
	$object->fk_user_prog = $user->id;
	if ($object->date_ini_prog && $object->speciality_prog && $statut == 3)
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
		//buscamos al usuario asignador //fiscal
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
			header("Location: ".$_SERVER['PHP_SELF']."?id=".$id.'&mesg='.$mesg);
			exit;
		}
		else
			$db->rollback();
		$action = 'program';
		$mesg='<div class="error">'.$object->error.'</div>';
	}
	else
	{
		$mesg='<div class="error">'.$langs->trans("Errorlackinformation,,").'</div>';
		$action="program";
		// Force retour sur page creation
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
	header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
}

// Delete request
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->mant->tick->del)
{
	$object->fetch($_REQUEST["id"]);
	$result=$object->delete($user);
	if ($result > 0)
	{
		header("Location: ".DOL_URL_ROOT.'/mant/request/liste.php');
		exit;
	}
	else
	{
		$mesg='<div class="error">'.$object->error.'</div>';
		$action='';
	}
}

// Addcontact tecnicos externos
if ($action == 'addcontact' && $user->rights->mant->tick->asst)
{
	$object->fetch($id);
	print_r($_POST);
	if ($_REQUEST['close'] == $langs->trans('Close'))
	{
	//pasa a estado 3
		$object->status = 3;
		$res = $object->update($user);
		if ($res >0)
		{
			header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
			exit;
		}
		else
		{
			$action = 'asignjobs';
			$mesg='<div class="error">'.$object->error.'</div>';
		}
	}
	else
	{
		$statut = $object->status;
		$objReqcont = new Mworkrequestcontact($db);
		$objReqcont->fk_contact = GETPOST("fk_contact",'int');
		$objReqcont->fk_work_request = $id;
		$objReqcont->status      = 1;
		$objReqcont->fk_user_create = $user->id;
		$objReqcont->fk_user_mod = $user->id;
		$objReqcont->datec = dol_now();
		$objReqcont->datem = dol_now();
		$objReqcont->tms = dol_now();
		if ($objReqcont->fk_contact && $statut == 2 || $objReqcont->fk_contact && $statut == 3 && empty($object->date_ini))
		{
			$result = $objReqcont->create($user);
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
}

// Adduser tecnicos internos
if ($action == 'adduser' && $user->rights->mant->tick->asst)
{
	$object->fetch($id);
	$statut = $object->status;
	//nuevo
	$objRequser->fk_user = GETPOST("fk_user",'int');
	$objRequser->fk_work_request = $id;
	$objRequser->status      = 1;
	$objRequser->fk_user_create = $user->id;
	$objRequser->fk_user_mod = $user->id;
	$objRequser->datec = dol_now();
	$objRequser->datem = dol_now();
	$objRequser->tms = dol_now();
	if ($objRequser->fk_user > 0 && $statut == 2)
	{
		$result = $objRequser->create($user);
		if ($result > 0)
		{
			header("Location: ".$_SERVER['PHP_SELF']."?id=".$id.'&action=asignjobs');
			exit;
		}
		$action = 'asignjobs';
		$mesg='<div class="error">'.$objRequser->error.'</div>';
	}
	else
	{
		$mesg='<div class="error">'.$langs->trans("Erroruserisrequired").'</div>';
		$action="asignjobs";
		// Force retour sur page creation
	}
}

// Delete user request
if ($action == 'confirm_delete_user')
{
	if ($_REQUEST["confirm"] == 'yes' && $user->rights->mant->tick->asst)
	{
		$objRequser->fetch($_REQUEST["idu"]);
		$result=$objRequser->delete($user);
		if ($result > 0)
		{
			header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id.'&action=asignjobs');
			exit;
		}
		else
		{
			$mesg='<div class="error">'.$obRequser->error.'</div>';
			$action='asignjobs';
		}
	}
	else
	{
		$action = 'asignjobs';
	}
}

// Delete request
if ($action == 'deletec' && $user->rights->mant->tick->asst)
{
	$object->fetch($id);
	$objReqcont->fetch($_REQUEST["idr"]);
	if ($objReqcont->id == $_REQUEST['idr'] &&
		$objReqcont->fk_work_request == $_REQUEST['id'] && $object->status == 2)
	{
		$result=$objReqcont->delete($user);
		if ($result > 0)
		{
			header("Location: ".$_SERVER['PHP_SELF'].'?id='.$_REQUEST['id'].'&action=asignjobs');
			exit;
		}
		else
		{
			$mesg='<div class="error">'.$objReqcont->error.'</div>';
			$action='asignjobs';
		}
	}
	$action='asignjobs';
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
	$tmparray['id'] = GETPOST('id');

	if (! empty($tmparray['fk_property']))
	{
		$object->fk_property = $tmparray['fk_property'];
		$object->ref = $tmparray['ref'];
		$object->fk_member = $tmparray['fk_member'];
		$object->internal = $tmparray['internal'];
		$object->speciality = $tmparray['speciality'];
		if ($tmparray['id'])
			$action='edit';
		else
			$action='create';
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

// $objCharge = new Pcharge($db);
// $objDepartament = new Pdepartament($db);

$form=new Formv($db);

$aArrjs = array();
$help_url='EN:Module_Mant_En|FR:Module_Mant|ES:M&oacute;dulo_Mant';
$aArrcss = array('/mant/css/style-desktop.css');
$conf->dol_hide_leftmenu = 0;
llxHeader("",$langs->trans("Managementmant"),$help_url,'','','',$aArrjs,$aArrcss);



if ($action == 'create' && $user->rights->mant->tick->crear)
{
	print_fiche_titre($langs->trans("Newworkrequest"));
	if (empty($object->ref)) $object->ref = '(PROV)';

	print "\n".'<script type="text/javascript" language="javascript">';
	print '$(document).ready(function () {
		$("#fk_property").change(function() {
			document.form_index.action.value="create";
			document.form_index.submit();
		});
		$("#fk_equipment").change(function() {
			document.form_index.action.value="create";
			document.form_index.submit();
		});
		$("#fk_asset").change(function() {
			document.form.action.value="create";
			document.form.submit();
		});

	});';
	print '</script>'."\n";

	print '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form_index">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="rowid" value="15">';

	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

	// ref numeracion automatica de la OT
	print '<tr><td class="fieldrequired" width="20%">'.$langs->trans('Jobsticketnumber').'</td><td colspan="2">';
	print '<input id="ref" type="text" value="'.GETPOST('ref').'" name="ref" size="13" maxlength="15" disabled="disabled">';
	print '</td></tr>';

	// solicitante
	print '<tr><td class="fieldrequired">'.$langs->trans('Applicant').'</td><td colspan="2">';
	if ($user->rights->mant->tick->selus)
		print select_adherent((GETPOST('fk_member')?GETPOST('fk_member'):$user->fk_member),'fk_member','',0,1);
	else
	{
		print $user->lastname.' '.$user->firstname;
		print '<input type="hidden" name="fk_member" value="'.$user->fk_member.'">';
	}
	print '</td></tr>';
	//equipment
	//print '<tr><td  class="fieldrequired">'.$langs->trans('Equipment').'</td><td colspan="2">';
	print '<tr><td class="fieldrequired">'.$langs->trans("Equipment").'</td><td>';
	print $form->select_equipment($fk_equipment, 'fk_equipment', '', 20, 0, 0, 2, '', 1, array(),0,'','',0);
	print '</td></tr>';


	//$res = $objEquipment->fetchAll('ASC', 'label',0,0,array('entity'=>$conf->entity,'status'=>1),'AND','',false);

	//$options = '<option>'.$langs->trans('Select').'</option>';
	if($res>0)
	{
	//	foreach($objEquipment->lines AS $j => $line)
	//	{
	//		$selected = '';
	//		if(GETPOST('fk_equipment') == $line->id) $selected = ' selected';
	//		$options.= '<option value="'.$line->id.'" '.$selected.'>'.$line->label.'</option>';
	//	}
	}
	//print '<select id="fk_equipment" name="fk_equipment">'.$options.'</select>';
	print '</td></tr>';

	// internal
	print '<tr><td>'.$langs->trans('Internal').'</td><td colspan="2">';
	print '<input id="internal" type="text" value="'.GETPOST('internal').'" name="internal" size="5">';
	print '</td></tr>';

	$fk_property = GETPOST('fk_property');
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
	print '<tr><td>'.$langs->trans('Property').'</td><td colspan="2">';
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
	print '</td></tr>';

	// location
	print '<tr><td>'.$langs->trans('Location').'</td><td colspan="2">';
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
	print '</td></tr>';

	//type repair
	print '<tr><td class="fieldrequired">'.$langs->trans('Typerepair').'</td><td colspan="2">';
	print $form->select_type_repair(GETPOST('fk_type_repair'),'fk_type_repair','',0,1,'');
	print '</td></tr>';

	//descripcion
	print '<tr><td class="fieldrequired">'.$langs->trans('Detailtheproblem').'</td><td colspan="2">';
	print '<textarea name="detail_problem" cols="80" rows="5">'.GETPOST('detail_problem').'</textarea>';
	print '</td></tr>';

	print '</table>';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

	print '</form>';
}
else
{
	if ($id)
	{
		dol_htmloutput_mesg($mesg);
		$result = $object->fetch($id);
		$lImage = true;
		//buscamos si la ubicacion tiene seguridad activa
		if ($objLocation->fetch($object->fk_location) > 0)
		{
			if ($objLocation->id == $object->fk_location)
			{
				if ($objLocation->safety) $lImage = false;
			}
		}
		if (isset($tmparray['fk_property']))
			$object->fk_property = $tmparray['fk_property'];
		$objadh = new Adherent($db);
		if ($result < 0)
			dol_print_error($db);


		/*
		* Affichage fiche
		*/
		if ($action <> 'edit' && $action <> 're-edit')
		{
			//$head = fabrication_prepare_head($object);


			print_fiche_titre($langs->trans('Ticket'));
			dol_fiche_head();
			// Confirmation de la validation
			if ($action == 'validate')
			{
				// on verifie si l'objet est en numerotation provisoire
				$ref = substr($object->ref, 1, 4);
				if ($ref == 'PROV')
				{
					$numref = $object->getNextNumRef($soc);
				}
				else
				{
					$numref = $object->ref;
				}

				//$object = new Solalmacen($db);
				$object->fetch(GETPOST('id'));
				//cambiando a validado
				$object->ref = $numref;

				//cambiando a validado
				if ($object->status == 0)
				{
					$object->status = 4;
					//update
					$object->update($user);
				}
				$action = '';
			}
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
				//$tmparray=getProperty(GETPOST('country_id','int'),'all',$db,$langs,0);
				$tmparray['id'] = GETPOST('fk_soc');
				if (! empty($tmparray['id']))
				{
					//$object->fetch(GETPOST('id'));
					$object->fk_soc = $tmparray['id'];
					//$_GET['id']=$object->id;
					$action='asignjobs';
				}
			}

			//Close period
			if ($action == 'close')
			{
				$object->fetch(GETPOST('id'));
				//cambiando a validado
				$object->status = 2;
				//update
				$object->update($user);
				$action = '';
				//header("Location: fiche.php?id=".$_GET['id']);
			}

		// Open period
			if ($action == 'openwork')
			{
				$object->fetch(GETPOST('id'));
				 //cambiando a validado
				$object->status = 2;
				 //update
				$object->update($user);
				$action = '';
				 //header("Location: fiche.php?id=".$_GET['id']);
			}

			// Confirm send adherent

			if ($action == 'upassignreq')
			{
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Sendassignmentoftechnicians"),$langs->trans("ConfirmSendassignmentoftechnicians").' '.$object->ref.' '.$object->email,"confirm_upassignreq",'',0,2);
				if ($ret == 'html') print '<br>';
			}

			// Confirm send ticket
			if ($action == 'sendticket')
			{
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Sendjobticket"),$langs->trans("Confirmsendjobticket").' '.$object->ref.' '.$object->email,"confirm_validate",'',0,2);
				if ($ret == 'html') print '<br>';
			}

		 // Confirm delete third party
			if ($action == 'delete')
			{
				$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deleteworkorder"),$langs->trans("Confirmdeleteworkorder".' '.$object->ref.' '.$object->email),"confirm_delete",'',0,2);
				if ($ret == 'html') print '<br>';
			}

		 // Confirm delete user
			if ($action == 'deladh')
			{
				if ($objRequser->fetch($idu) > 0)
				{
					$nameadh = '';
					if ($objAdherent->fetch($objRequser->fk_user) > 0)
						$nameadh = $objAdherent->lastname.' '.$objAdherent->firstname;

					$form = new Form($db);
					$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&idu='.$idu,$langs->trans("Deleteteuser"),$langs->trans("Confirmdeleteuser".' '.$nameadh),"confirm_delete_user",'',0,2);
					if ($ret == 'html') print '<br>';
				}
			}

			print '<table class="border" width="100%">';

		 // ref numeracion automatica de la OT
			print '<tr><td width="20%">'.$langs->trans('Jobsticketnumber').'</td><td colspan="2">';
			print $object->ref;
			print '</td></tr>';

		 // solicitante
			print '<tr><td>'.$langs->trans('Solicitante').'</td><td colspan="2">';
			if ($objadh->fetch($object->fk_member) > 0)
				print $objadh->lastname.' '.$objadh->firstname;
			else
				print $langs->trans('not defined');
			print '</td></tr>';

		 // equipment
			print '<tr><td>'.$langs->trans('Equipment').'</td><td colspan="2">';
			if ($objEquipment->fetch($object->fk_equipment) > 0)
				print $objEquipment->ref;
			else
				print $langs->trans('not defined');
			print '</td></tr>';

			if ($object->internal)
			{
		 // internal
				print '<tr><td width="20%">'.$langs->trans('Internal').'</td><td colspan="2">';
				print $object->internal;
				print '</td></tr>';
			}
			if ($object->fk_property>0)
			{
		 	// property
				print '<tr><td>'.$langs->trans('Property').'</td><td colspan="2">';
				if ($objProperty->fetch($object->fk_property) > 0)
					print $objProperty->ref;
				else
					print $langs->trans('not defined');
				print '</td></tr>';
			}
			if ($object->fk_location>0)
			{
		 	// location
				print '<tr><td>'.$langs->trans('Location').'</td><td colspan="2">';
				if ($objLocation->fetch($object->fk_location) > 0)
					print $objLocation->detail;
				else
					print $langs->trans('not defined');
				print '</td></tr>';
			}
			//type_repair
			print '<tr><td>'.$langs->trans('Typerepair').'</td><td colspan="2">';
			if ($objTyperepair->fetch($object->fk_type_repair) > 0)
				print $objTyperepair->label;
			else
				print $langs->trans('not defined');
			print '</td></tr>';

		 //descripcion
			print '<tr><td>'.$langs->trans('Detailtheproblem').'</td><td colspan="2">';
			print $object->detail_problem;
			print '</td></tr>';

			/*
		 //empresa asignada
			if ($action == 'asignsoc' && $user->rights->mant->tick->ass)
			{
				print '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form_index">';
				print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
				print '<input type="hidden" name="action" value="updateassign">';
				print '<input type="hidden" name="id" value="'.$id.'">';
			}
			print '<tr><td '.($action=='asignsoc'?'class="fieldrequired"':'').'>'.$langs->trans('Assignedto').'</td><td colspan="2">';
			if ($action == 'asignsoc' && $user->rights->mant->tick->ass)
			{
				print $form->select_company($object->fk_soc,'fk_soc','',1);
				print ' '.$langs->trans('Emptytoassigninternaltechnical');
				print '<input type="submit" class="button" value="'.$langs->trans('Save').'">';
			}
			else
			{
				$objSoc->fetch($object->fk_soc);
				if ($objSoc->id == $object->fk_soc)
					print $objSoc->nom;
				else
				{
					if ($object->fk_soc <0)
						print $langs->trans('Samecompany');
					else
						print '&nbsp;';
				}
			}
			print '</td></tr>';

			 //speciality
			 //print '<tr><td '.($action=='asignsoc'?'class="fieldrequired"':'').'>'.$langs->trans('Speciality').'</td><td colspan="2">';
			 //if ($action == 'asignsoc' && $user->rights->mant->tick->ass)
			 //{
			//print select_speciality($object->speciality,'speciality','',1);

			 //}
			 //else
			 //{
			 //	print select_speciality($object->speciality,'speciality','',0,1);
			 //}
			 //print '</td></tr>';
			if ($action == 'asignsoc' && $user->rights->mant->tick->ass)
				print '</form>';
			*/
			 // Statut
				print '<tr><td>'.$langs->trans("Status").'</td><td colspan="2">'.$object->getLibStatut(6).'</td></tr>';

				print "</table>";

				print '</div>';
		 //agregamos tecnicos
				$lTechnic = false;
		 //tecnicos internos : true= tecnicos externos
				if ($object->fk_soc <= 0) $lTechnic = false;
				if ($object->fk_soc > 0) $lTechnic = true;

		 //para versiones anteriores
				$socid = $object->fk_soc;
				$objSoc = new Societe($db);
				$lSelectSocid = true;
				$res = $objSoc->fetch($object->fk_soc);
				if ($res > 0)
					$objTypent = fetch_typent($objSoc->typent_id);
				if ($objTypent->id == $objSoc->typent_id && $objTypent->code == 'TE_BCB')
		 //asignjobsdet
					$lTechnic = false;
				if ($object->status == 2 && ($action == 'asignjobs' || $action == 'upassignreq'))
				{
					if (!$lTechnic)
					{
			 //registro de tecnicos internos
						include DOL_DOCUMENT_ROOT.'/mant/request/tpl/adduser.tpl.php';
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
		 //PROGRAMACION DE TRABAJOs
				if ($object->status ==3 && $action == 'program')
					include_once DOL_DOCUMENT_ROOT.'/mant/request/tpl/programation.tpl.php';

			//if ($object->status >=4)
			//	include_once DOL_DOCUMENT_ROOT.'/mant/request/tpl/programation_view.tpl.php';

				/* ****************************************** */
				/*                                            */
				/* Barre d'action                             */
				/*                                            */
				/* ****************************************** */

				print "<div class=\"tabsAction\">\n";

		 //$objjobs->getlist($id);
			//obtenemos la lista de ordenes de trabajo para el ticket
				if ($object->status>=4)
				{
					$filterticket = " AND t.fk_work_request = ".$object->id;
					$objjobs->fetchAll('','',0,0,array(1=>1),'AND',$filterticket);
				}
				if ($action == '')
				{
					if ($user->rights->mant->tick->crear && $object->status == 0)
						print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=edit&id='.$object->id.'">'.$langs->trans("Modify").'</a>';
					else
						print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

					if (($object->status==0 ) && $user->rights->mant->tick->del)
						print '<a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?action=delete&id='.$object->id.'">'.$langs->trans("Delete").'</a>';
					else
						print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
		 //enviar ticket de trabajo
					if ($object->status == 0 && $user->rights->mant->tick->crear)
					{
						print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=sendticket">'.$langs->trans('Send').'</a>';
					}

		 // Valid
					if ($object->status == 0 && $user->rights->mant->tick->val)
					{
						print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=validate">'.$langs->trans('Validate').'</a>';
					}
		 // Notvalidate
					if ($object->status == 1 && $user->rights->mant->tick->val && count($objjobs->lines) <= 0)
					{
						print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=revalidate">'.$langs->trans('Notvalidate').'</a>';
					}

		 // rechazar
					if ($object->status == 2 && $user->rights->mant->tick->rech)
					{
					//if (count($objjobs->array) <=0)
					//	print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=refuse">'.$langs->trans('Refuse').'</a>';
					}

		 // asignar ticket a empresa
					if (empty($action) && $object->status == 1 && $user->rights->mant->tick->ass)
					{
						print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=asignsoc&amp;id='.$object->id.'">'.$langs->trans('Societeassign').'</a>';
					}

		 // asignar trabajos a tecnicos
					if (empty($action) && $object->status == 2 && $user->rights->mant->tick->asst)
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
						if ((empty($user->societe_id) && !$lTechnic) ||
							(!empty($user->societe_id) && $lTechnic == true))
							print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=asignjobs&amp;id='.$object->id.'">'.$langs->trans('Technicassign').'</a>';
					}
		 //programacion de trabajos
					if ($object->status == 3 && $user->rights->mant->tick->prog )
					{
						print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=program&amp;id='.$object->id.'">'.$langs->trans('Programming').'</a>';
					}
		 // crear orden de trabajo
					if ($object->status == 4 && $user->rights->mant->jobs->crear)
					{
						print '<a class="butAction" href="'.DOL_URL_ROOT.'/mant/jobs/card.php?action=create&amp;idw='.$object->id.'">'.$langs->trans('Createjobs').'</a>';
					}
				}

		 //programacion
				if (empty($action) || $action == 'program')
				{
					$lContinue = true;
					if ($lImage && empty($object->image_ini))
						$lContinue = false;
					if ( $object->status == 3 && $lContinue)
					{
						print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
						print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
						print '<input type="hidden" name="action" value="startjobs">';
						print '<input type="hidden" name="id" value="'.$object->id.'">';

						print '<center><br><input type="submit" class="button" value="'.$langs->trans("Sendprogramming").'"></center>';
						print '</form>';
					}
				}
				print '</div>';

			//listamos las ordenes de trabajo generados para el ticket
				if (count($objjobs->lines) > 0)
				{

				//print_barre_liste($langs->trans("Listjobsorder"));

					print '<table class="noborder" width="100%">';
					print "<tr class=\"liste_titre\">";
					print_liste_field_titre($langs->trans("Ref"),"", "","","","");
					print_liste_field_titre($langs->trans("Date"),"", "","","","");
				//print_liste_field_titre($langs->trans("Assigned"),"", "","","","");
				//print_liste_field_titre($langs->trans("Responsible"),"", "","","","");
					print_liste_field_titre($langs->trans("Status"),"", "",'','','align="right"');
					print "</tr>\n";

					foreach ((array) $objjobs->lines AS $j => $objp)
					{
						$objjobs->status = $objp->status;
						$objAdherent->fetch($objp->fk_member);
						$var=!$var;
						print "<tr $bc[$var]>";
						print '<td><a href="'.DOL_URL_ROOT.'/mant/jobs/card.php?id='.$objp->id.'">'. $objp->ref.'</a></td>';

						print '<td><a href="'.DOL_URL_ROOT.'/mant/jobs/card.php?id='.$objp->id.'">'.img_object($langs->trans("Showjobsorder"),'calendar').' '. dol_print_date($objp->date_create,'day').'</a></td>';
					/*
					if ($objp->fk_soc > 0)
					{
				 		//buscamos a la compania
						$lUser = false;

						if ($objSoc->fetch($objp->fk_soc) > 0 && $objp->fk_soc > 0)
						{
							$objTypent = fetch_typent($objSoc->typent_id);
							if ($objp->fk_soc == -2 || ($objTypent->id == $objSoc->typent_id && $objTypent->code == 'TE_BCB' ))
								//asignacion interna
								$lUser = true;
							print '<td>'.$objSoc->name.'</td>';
						}
						else
							print '<td>&nbsp;</td>';
						if (!$lUser)
						{
								 //buscamos si esta asignado el contacto responsable
							$aArray = $objJobscontact->list_contact($objp->id);
							if (count($aArray) > 0)
							{
								$htmlc = '';
								print '<td>';
								foreach((array) $aArray AS $j => $objc)
								{
									if (!empty($htmlc))$htmlc.='</br>';
									if ($objContact->fetch($objc->fk_contact))
									{
										$htmlc.=$objContact->lastname.' '.$objContact->firstname;
									}
								}
								print $htmlc;
								print '</td>';
							}
							else
								print '<td>&nbsp;</td>';
						}
						else
						{
								 //buscamos si esta asignado el usuario responsable
								//modificado list_jobsuser
							$aArray = $objRequser->list_requestuser($objp->id);
							if (count($aArray) > 0)
							{
								$htmlc = '';
								print '<td>';
								foreach((array) $aArray As $j => $obju)
								{
									if (!empty($htmlc))$htmlc.='</br>';
									if ($objUser->fetch($obju->fk_user))
										$htmlc.=$objUser->lastname.' '.$objUser->firstname.'</td>';
								}
								print $htmlc;
								print '</td>';
							}

						}
					}
					else
					{
						print '<td>'.$langs->trans('Internalassignment').'</td>';
							//buscamos si esta asignado el usuario responsable
							//echo $objp->rowid;
							//modificado list_jobsuser
						$aArray = $objRequser->list_requestuser($objp->rowid);
						if (count($aArray) > 0)
						{
							$htmlc = '';
							print '<td>';
							foreach((array) $aArray As $j => $obju)
							{
								if (!empty($htmlc))$htmlc.='</br>';
								if ($objUser->fetch($obju->fk_user))
									$htmlc.=$objUser->lastname.' '.$objUser->firstname;
							}
							print $htmlc;
							print '</td>';
						}

					}
					*/
					print '<td align="right">'.$objjobs->GetLibStatut(6).'</td>';
					print '</tr>';

				}
			}
		}


	//Edition fiche
		if (($action == 'edit' || $action == 're-edit') && 1)
		{
			print_fiche_titre($langs->trans("Editjobticket"), $mesg);

			print "\n".'<script type="text/javascript" language="javascript">';
			print '$(document).ready(function () {
				$("#selectfk_property").change(function() {
					document.form_index.action.value="createedit";
					document.form_index.submit();
				});
			});';
			print '</script>'."\n";

			print '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form_index">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="update">';
			print '<input type="hidden" name="id" value="'.$id.'">';

			print '<table class="border" width="100%">';

		 // ref numeracion automatica de la OT
			print '<tr><td class="fieldrequired" width="20%">'.$langs->trans('Jobsticketnumber').'</td><td colspan="2">';
			print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="13" maxlength="15" disabled="disabled">';
			print '</td></tr>';

		 // solicitante
			print '<tr><td class="fieldrequired">'.$langs->trans('Solicitante').'</td><td colspan="2">';
			print select_adherent((empty($object->fk_member)?$user->fk_member:$object->fk_member),'fk_member','',0,1);
			print '</td></tr>';

			//equipment
			print '<tr><td  class="fieldrequired">'.$langs->trans('Equipment').'</td><td colspan="2">';
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
				print $form->select_equipment(($fk_equipment?$fk_equipment:$object->fk_equipment), 'fk_equipment', '', 20, 0, 0, 2, '', 1, array(),0,'','',0);
				print '</td></tr>';

		 // internal
				print '<tr><td  class="fieldrequired" width="20%">'.$langs->trans('Internal').'</td><td colspan="2">';
				print '<input id="internal" type="text" value="'.$object->internal.'" name="internal" size="5">';
				print '</td></tr>';

		// property
				$res = $objLocation->fetch($object->fk_location);
				if ($res>0) $fk_property = $objLocation->fk_property;
				print '<tr><td class="fieldrequired">'.$langs->trans('Property').'</td><td colspan="2">';
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
				print '</td></tr>';

		// location
				$fk_location = (GETPOST('fk_location')?GETPOST('fk_location'):$object->fk_location);
				print '<tr><td class="fieldrequired">'.$langs->trans('Location').'</td><td colspan="2">';
				$filter = " AND t.fk_property = ".$fk_property;
				$res = $objLocation->fetchAll('ASC','detail',0,0,array('status'=>1),'AND',$filter);
				$options = '';
				$lines =$objLocation->lines;
				foreach ((array) $lines AS $j => $line)
				{
					$selected = '';
					if ($fk_location == $line->id) $selected = ' selected';
					$options.= '<option value="'.$line->id.'" '.$selected.'>'.$line->detail.'</option>';
				}
				print '<select id="fk_location" name="fk_location">'.$options.'</select>';
	//print $objLocation->select_location(GETPOST('fk_location'),'fk_location','',40,1,GETPOST('fk_property'));
				print '</td></tr>';

			//type repair
				print '<tr><td class="fieldrequired">'.$langs->trans('Typerepair').'</td><td colspan="2">';
				print $form->select_type_repair((GETPOST('fk_type_repair')?GETPOST('fk_type_repair'):$object->fk_type_repair),'fk_type_repair','',0,1,'');
				print '</td></tr>';

		 //descripcion
				print '<tr><td class="fieldrequired">'.$langs->trans('Detailtheproblem').'</td><td colspan="2">';
				print '<textarea name="detail_problem" cols="80" rows="5">'.$object->detail_problem.'</textarea>';
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
