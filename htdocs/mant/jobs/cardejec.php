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

require_once DOL_DOCUMENT_ROOT.'/mant/class/adherentext.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mworkrequestext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mworkrequestcontact.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mworkrequestuser.class.php';

require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsprogram.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsadvance.class.php';

require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobscontact.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsuser.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mtyperepair.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsorderext.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/contactext.class.php';

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
$langs->load("companies");
$langs->load("commercial");
$langs->load("bills");
$langs->load("banks");
$langs->load("users");
$langs->load("other");

$action=GETPOST('action');

$id  = GETPOST("id");
$idr = GETPOST('idr');
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
$objectprogram = new Mjobsprogram($db);
$objectadvance = new Mjobsadvance($db);
$objwork     = new Mworkrequestext($db);
$objProperty = new Mproperty($db);
$objLocation = new Mlocation($db);
$objSoc      = new Societe($db);
$objEquipment= new Mequipmentext($db);
$objUser     = new User($db);
$objAdherent = new Adherentext($db);
//$objJobsuser = new Mjobsuser($db);
$objcomm     = new Commande($db);
$objWorkuser = new Mworkrequestuser($db);
$objWorkcont = new Mworkrequestcontact($db);

$objJobuser = new Mjobsuser($db);
$objJobscontact = new Mjobscontact($db);
$objContact = new Contactext($db);

$objCharge = new Pcharge($db);
$objDepartament = new Pdepartamentext($db);
$objTyperepair = new Mtyperepair($db);

if ($conf->assets->enabled)
	$objassets = new Assetsext($db);

if ($id) $object->fetch($id);

/*
 * Actions
 */
// upjobs programacion del trabajo a realizar //upjobs
if ($action == 'addadvance' && $user->rights->mant->jobs->regjobs )
{
	$actionant = GETPOST('actionant');

	$statut = $object->status;
	$date_ini = dol_mktime(GETPOST('di_hour'), 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
	$date_fin = dol_mktime(GETPOST('df_hour'), 0, 0, GETPOST('df_month'),GETPOST('df_day'),GETPOST('df_year'));
	$used_time = GETPOST('ut_hour').':'.GETPOST('ut_min');
	$db->begin();
	$aMembers = GETPOST('members');
	$idsMember = '';
	foreach ((array) $aMembers AS $j => $idMember)
	{
		if (!empty($idsMember)) $idsMember.=',';
		$idsMember.= $idMember;
	}
	$filterstatic = " AND t.fk_jobs = ".$object->id;
	//recuperamos el ultimo numero
	$res = $objectadvance->fetchAll('DESC','ref', 0,0,array(1=>1),'AND',$filterstatic,true);
	if ($res == 1)
		$ref = $objectadvance->ref + 1;
	elseif($res>1)
	{
		foreach ($objectadvance->lines AS $j => $line)
		{
			if (empty($ref)) $ref = $line->ref + 1;
			else continue;
		}
	}
	else $ref = 1;

	//revisamos la subida de archivo
	// Logo/Photo save
	$dir     = $conf->mant->multidir_output[$object->entity]."/".$object->id."/images";
	$file_OKini = is_uploaded_file($_FILES['photofin']['tmp_name']);

	if ($file_OKini)
	{
		if (GETPOST('deletephotofin'))
		{
			$fileimg=$dir.'/'.$object->image_adv;
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
					$imgThumbSmall = vignette($newfile, $maxwidthsmall, $maxheightsmall, '_small', $quality);
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
			case 2:
			$errors[] = "ErrorFileSizeTooLarge";
			break;
			case 3:
			$errors[] = "ErrorFilePartiallyUploaded";
			break;
		}
	}
	//creamos un registro de la programación
	$objectadvance->fk_jobs = $object->id;
	$objectadvance->fk_speciality = GETPOST("fk_speciality",'int');
	$objectadvance->fk_jobs_program = GETPOST('fk_jobs_program');
	if (empty($objectadvance->fk_jobs_program)) $objectadvance->fk_jobs_program = 0;
	$objectadvance->ref = $ref;
	$objectadvance->date_ini    = $date_ini;
	$objectadvance->date_fin    = $date_fin;
	$objectadvance->description = GETPOST('description','alpha');
	$objectadvance->used_time = $used_time;
	$objectadvance->image_adv = $newfile;
	$objectadvance->members = $idsMember;
	$objectadvance->fk_user_create = $user->id;
	$objectadvance->fk_user_mod = $user->id;
	$objectadvance->datec = dol_now();
	$objectadvance->datem = dol_now();
	$objectadvance->tms = dol_now();
	$objectadvance->status = 1;
	//validamos
	if ($objectadvance->fk_speciality <=0)
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Speciality")), null, 'errors');
	}
	if (empty(GETPOST('ut_hour')) && empty(GETPOST('ut_min')))
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Usedtime")), null, 'errors');
	}
	if ($objectadvance->date_ini > $objectadvance->date_fin)
	{
		//$error++;
		//setEventMessages($langs->trans('La fecha inicio no puede ser mayor a la fecha final'),null,'errors');
	}
	if ($object->status !=4)
	{
		$error++;
		setEventMessages($langs->trans('La Orden de trabajo no esta en estado para programación'),null,'errors');
	}
	if (!$error)
	{
		$result = $objectadvance->create($user);
		if ($result<=0)
		{
			$error++;
			setEventMessages($objectadvance->error,$objectadvance->errors,'errors');
		}
	}
	//actualizamos la fecha de reporte en object
	if (empty($object->date_ini)) $object->date_ini = $date_ini;
	else
	{
		if ($date_ini <= $object->date_ini) $object->date_ini = $date_ini;
	}
	if (empty($object->date_fin)) $object->date_fin = $date_fin;
	else
	{
		if ($date_fin >= $object->date_fin) $object->date_fin = $date_fin;
	}
	$object->fk_user_mod = $user->id;
	$object->datem = dol_now();
	$object->tms = dol_now();
	$res = $object->update($user);
	if ($res <=0)
	{
		$error++;
		setEventMessages($object->error,$object->errors,'errors');
	}
	if (!$error)
	{
		$db->commit();
		header('Location: '.$_SERVER['PHP_SELF'].'?id='.$object->id);
		exit;
	}
	else
	{
		$db->rollback();
		$action=$actiondes;
	}
}
//updatework
// upjobs programacion del trabajo a realizar //upjobs
if ($action == 'updateadvance' && $user->rights->mant->jobs->modjobs )
{
	$actionant = GETPOST('actionant');
	if ($idr) $objectadvance->fetch($idr);

	$statut = $object->status;
	$date_ini = dol_mktime(GETPOST('di_hour'), 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
	$date_fin = dol_mktime(GETPOST('df_hour'), 0, 0, GETPOST('df_month'),GETPOST('df_day'),GETPOST('df_year'));
	$used_time = GETPOST('ut_hour').':'.GETPOST('ut_min');
	$db->begin();
	$aMembers = GETPOST('members');
	$idsMember = '';
	foreach ((array) $aMembers AS $j => $idMember)
	{
		if (!empty($idsMember)) $idsMember.=',';
		$idsMember.= $idMember;
	}

	//revisamos la subida de archivo
	// Logo/Photo save
	$dir     = $conf->mant->multidir_output[$object->entity]."/".$object->id."/images";
	$file_OKini = is_uploaded_file($_FILES['photofin']['tmp_name']);

	if ($file_OKini)
	{
		if (GETPOST('deletephotofin'))
		{
			$fileimg=$dir.'/'.$object->image_adv;
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
					$imgThumbSmall = vignette($newfile, $maxwidthsmall, $maxheightsmall, '_small', $quality);
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
			case 2:
			$errors[] = "ErrorFileSizeTooLarge";
			break;
			case 3:
			$errors[] = "ErrorFilePartiallyUploaded";
			break;
		}
	}
	//actualziamos el registro de la programación
	$objectadvance->fk_jobs = $object->id;
	$objectadvance->fk_speciality = GETPOST("fk_speciality",'int');
	$objectadvance->fk_jobs_program = GETPOST('fk_jobs_program');
	if (empty($objectadvance->fk_jobs_program)) $objectadvance->fk_jobs_program = 0;

	//$objectadvance->ref = $ref;
	$objectadvance->date_ini    = $date_ini;
	$objectadvance->date_fin    = $date_fin;
	$objectadvance->description = GETPOST('description','alpha');
	$objectadvance->used_time = "'".$used_time."'";
	if ($newfile)
		$objectadvance->image_adv = $newfile;
	$objectadvance->members = $idsMember;
	$objectadvance->fk_user_mod = $user->id;
	$objectadvance->datem = dol_now();
	$objectadvance->tms = dol_now();
	$objectadvance->status = 1;
	//validamos
	if ($objectadvance->fk_speciality <=0)
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Speciality")), null, 'errors');
	}
	if (empty(GETPOST('ut_hour')) && empty(GETPOST('ut_min')))
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Usedtime")), null, 'errors');
	}
	if ($objectadvance->date_ini > $objectadvance->date_fin)
	{
		//$error++;
		//setEventMessages($langs->trans('La fecha inicio no puede ser mayor a la fecha final'),null,'errors');
	}
	if ($object->status !=4)
	{
		$error++;
		setEventMessages($langs->trans('La Orden de trabajo no esta en estado para programación'),null,'errors');
	}
	if (!$error)
	{
		$result = $objectadvance->update($user);
		if ($result<=0)
		{
			$error++;
			setEventMessages($objectadvance->error,$objectadvance->errors,'errors');
		}
	}
	if (!$error)
	{
		$db->commit();
		$action = '';
		//header('Location: '.$_SERVER['PHP_SELF'].'?id='.$object->id);
		//exit;
	}
	else
	{
		$db->rollback();
		$action=$actiondes;
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

//confirm_closework
if ($action == 'confirm_closework' && $_REQUEST['confirm'] == 'yes' && $user->rights->mant->jobs->close)
{
	if (GETPOST('cancel'))
	{
		$urltogo = $_SERVER['PHP_SELF'].'?id='.$id;
		header("Location: ".$urltogo);
		exit;
	}

	$object->fetch(GETPOST('id'));
	$statut = $object->status;
	$object->status      = 5;
	//trabajo concluido
	$object->fk_user_job = $user->id;
	$db->begin();
	//verificamos el inicio y fin de las ordenes de trabajo
	$filterstatic = " AND t.fk_jobs = ".$object->id;
	$res = $objectadvance->fetchAll('ASC','t.date_ini,t.date_fin',0,0, array(1=>1),'AND',$filterstatic);
	$date_ini = '';
	$date_fin = '';
	if ($res > 0)
	{
		$lines = $objectadvance->lines;
		foreach ($lines AS $j => $line)
		{
			if (empty($date_ini)) $date_ini = $line->date_ini;
			$date_fin = $line->date_fin;
		}
		$object->date_ini = $date_ini;
		$object->date_fin = $date_fin;
	}
	else
	{
		$error++;
		setEventMessages($langs->trans('No se tiene registro de ejecución'),null,'errors');
	}
	if (!$error && $object->fk_work_request && $statut == 4 )
	{
		//verificamos cuantas ordenes de trabajo tiene y si estan concluidas
		$res = $object->getlist($object->fk_work_request);
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
		if ($nJobsclose == $nJobs)
		{
			//actualizamos el work_request
			$objwork->fetch($object->fk_work_request);
			$objwork->statut = 6;
			$res = $objwork->update($user);
			if ($res <=0)
			{
				$error++;
				setEventMessages($objwork->error,$objwork->errors,'errors');
			}
		}
	}
	if (!$error)
	{
		$res = $object->update($user);
		if ($res <=0)
		{
			$error++;
			setEventMessages($object->error,$object->errors,'errors');
		}
	}
	if (!$error)
	{
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
	}
	else
	{
		$mesg='<div class="error">'.$langs->trans("Errorlackinformation_").'</div>';
		$action="editregjobs";
		// Force retour sur page creation
	}
	if (!$error)
	{
		$db->commit();
		header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
		exit;
	}
	else
		$db->rollback();

	$action = '';

}


// closework
if ($action == 'closework' && $_REQUEST['confirm'] == 'yes' && $user->rights->mant->jobs->close)
{
	$object->fetch(GETPOST('id'));
	$statut = $object->status;
	$object->status      = 5;
	//trabajo concluido
	$object->fk_user_job = $user->id;
	if ($object->date_fin && $object->description_job && $statut == 4 )
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
	foreach ((array) $aArray AS $i => $objJuser)
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
	$head = jobs_prepare_head($object);

	dol_fiche_head($head, 'exec', $langs->trans("Jobs"), 0, 'mant');

	// Confirm validate programation
	if ($action == 'valprogram')
	{
		$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,
			$langs->trans("Validateprogramming"),
			$langs->trans("ConfirmValidateprogramming".' '.$object->ref),
			"confirm_validateprogram",'',0,1);
		if ($ret == 'html') print '<br>';
	}

	if ($action == 'close')
	{
		$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,
			$langs->trans("Closeworkorder"),
			$langs->trans("ConfirmCloseworkorder").' '.$object->ref,
			"confirm_closework",'',0,2);
		if ($ret == 'html') print '<br>';
	}
	print '<table class="border" width="100%">';

	if (!empty($objwork->ref))
	{
		print '<tr><td width="20%">'.$langs->trans('Ticketnumber').'</td><td colspan="2">';
		print $objwork->ref;
		print '</td></tr>';
	}

	  		// ref numeracion automatica de la OT
	print '<tr><td width="20%">'.$langs->trans('Jobsordernumber').'</td><td class="valeur" colspan="2">';
	$linkback = '<a href="'.DOL_URL_ROOT.'/mant/jobs/liste.php">'.$langs->trans("BackToList").'</a>';

	print $object->ref;
	print '</td></tr>';

	  		//email
	if ($object->email)
	{
		print '<tr><td width="20%">'.$langs->trans('Email').'</td><td colspan="2">';
		print $object->email;
		print '</td></tr>';
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

	if ($object->fk_departament_assign>0)
	{
		print '<tr><td >'.$langs->trans('Departamentassigned').'</td><td colspan="2">';
		$objDepartament->fetch($object->fk_departament_assign);
		print $objDepartament->getNomUrl();
		print '</td></tr>';
	}

	print '<tr><td>'.$langs->trans("Status").'</td><td colspan="2">'.$object->getLibStatut(6).'</td></tr>';

	print "</table>";

	print '</div>';


	/* **************************** */
	/*                                            */
	/* Barre d'action                       */
	/*                                            */
	/* **************************** */

	print "<div class=\"tabsAction\">\n";

	if ($action == '')
	{
		 		//programacion de trabajos
		if ($object->status == 4 && $user->rights->mant->jobs->regjobs )
		{
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=advance&amp;id='.$object->id.'">'.$langs->trans('Reportprogress').'</a>';
		}

		  		//ejecutar trabajo

		  		//impres ot
		if ($object->status == 5)
		{
			print '<a class="butAction" href="'.DOL_URL_ROOT.'/mant/jobs/fiche_excel.php'.'?id='.$object->id.'">'.$langs->trans('Excel').'</a>';
		}

		  		// open jobs
		if (($object->status == 5 || $object->status == 8) && $user->rights->mant->jobs->openwork)
		{
			//print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=openwork">'.$langs->trans('Openwork').'</a>';
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

	if ($action == 'rechasignjobs' &&
		($object->status == 1 && $user->rights->mant->jobs->rechasig ||
			($object->status == 2 && $user->rights->mant->jobs->rechasig &&
				$object->fk_soc != -1))
	)
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

	  		//print view programation
	if ($object->status == 3 || $object->status == 4 || $object->status == 5 || $object->status == 6)
	{
		if ($idr>0 && $action == 'editjobs')
		{
			$objectadvance->fetch($idr);
			$fk_jobs_program = $objectadvance->fk_jobs_program;
		}
			//vamos a armar los trabajos programados para esta orden
		$filterjob = " AND t.fk_jobs = ".$object->id;
		$resprog = $objectprogram->fetchAll('ASC','ref',0,0,array(1=>1),'AND',$filterjob);
		$optionsprog = '<option value="">'.$langs->trans('Selectprogramming').'</option>';
		if ($resprog>0)
		{
			foreach ($objectprogram->lines AS $l => $linep)
			{
				$selected = '';
				if ((GETPOST('fk_jobs_program')?GETPOST('fk_jobs_program'):$fk_jobs_program) == $linep->id)
					{
						$selected = ' selected';
						$_POST['fk_speciality'] = $linep->fk_speciality;
					}
					$speciality = select_speciality($linep->fk_speciality,'fk_speciality','',0,1,'rowid');
					$optionsprog.='<option value="'.$linep->id.'" '.$selected.'>'.$speciality.' - '.$linep->description.'</option>';
				}
			}

			if ($action == 'advance')
			{
					//ejecucion de trabajos
				if ($object->status ==4) include_once DOL_DOCUMENT_ROOT.'/mant/jobs/tpl/work_advance.tpl.php';
			}
			include_once DOL_DOCUMENT_ROOT.'/mant/jobs/tpl/work_advance_list.tpl.php';
		}

		/* **************************** */
		/*                                            */
		/* Barre d'action                       */
		/*                                            */
		/* **************************** */

		print "<div class=\"tabsAction\">\n";

		if ($action == '' && $res > 0)
		{
		 		//cerrar orden de trabajo
			if ($object->status == 4 && $user->rights->mant->jobs->close )
			{
				print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=close&amp;id='.$object->id.'">'.$langs->trans('Closeworkorder').'</a>';
			}
		}


	}


	llxFooter();

	$db->close();
	?>
