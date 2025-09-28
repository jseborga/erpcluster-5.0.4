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
 *	\file       htdocs/mant/request/ficheemail.php
 *	\ingroup    Ordenes de Trabajo
 *	\brief      Page fiche mantenimiento 
 */
define("NOLOGIN",1);
define("NOCSRFCHECK",1);

$entity=(! empty($_GET['entity']) ? (int) $_GET['entity'] : (! empty($_POST['entity']) ? (int) $_POST['entity'] : 1));
if (is_int($entity)) define("DOLENTITY", $entity);

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formv.class.php';
// require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

require_once DOL_DOCUMENT_ROOT.'/mant/request/class/mworkrequest.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobs.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobscontact.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/mproperty.class.php';
require_once DOL_DOCUMENT_ROOT.'/orgman/class/mlocation.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/mant/lib/mant.lib.php';
require_once DOL_DOCUMENT_ROOT.'/mant/lib/adherent.lib.php';

require_once DOL_DOCUMENT_ROOT.'/mant/charge/class/pcharge.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/departament/class/pdepartament.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/class/html.formadd.class.php';

//require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

//para envio email
require_once DOL_DOCUMENT_ROOT.'/core/lib/emailing.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';
//require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/comm/mailing/class/mailing.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';

$langs->load("main");
$langs->load("members");
$langs->load("companies");
$langs->load("install");
$langs->load("other");
$langs->load("mant@mant");


$action=GETPOST('action');

$id        = GETPOST("id");
$code      = GETPOST('code');
$ref       = GETPOST("ref");
$action    = GETPOST("action");
//verifica email
$mailDefault = param_email();
//direccion
$url = $dolibarr_main_url_root;

$mesg = '';

$object = new Mworkrequest($db);
$objadd = new Adherent($db);
$objCharge = new Pcharge($db);
$objDepartament = new Pdepartament($db);
$objUser = new User($db);

/*
 * Actions
 */
// Add
if ($action == 'addemail')
{
	$error = 0;
	$mesg='';
	$fk_member = GETPOST('fk_member');
	//buscamos adherents
	$res = $objadd->fetch($fk_member);
	if ($res && $objadd->id == $fk_member)
	{
		$email_a = $objadd->email;

	//OTRA OPCION PARA VALIDAR POR CORREO
	//validamos correo
	// $email_a = GETPOST('email').(!empty($conf->global->MANT_USE_EXTENSION_MAIL_COMPANY)?'@'.$conf->global->MANT_EXTENSION_MAIL_DEFAULT:'');
	// if (!filter_var($email_a, FILTER_VALIDATE_EMAIL))
	//   {
	// 	$error++;
	// 	$mesg='<div class="error">'.$langs->trans("ResultKo").'<br>'.$langs->trans('Error, no esta correcto el formato del correo.').'</div>';
	//   }
		$date_create  = dol_now();
		$code = generarcodigo(10);
		$object->address_ip     = $_SERVER['REMOTE_ADDR'];
		$object->ref            = '(PROV)'.$code;
		$object->entity         = $entity;
		$object->email          = $email_a;
		$object->tokenreg       = $code;
		$object->fk_member      = $fk_member;
		$object->internal       = GETPOST('internal')+0;
		$object->date_create    = $date_create;
		$object->statut         = 0;
		$object->tms            = dol_now();
	// if (!empty($mailDefault))
	//   {
	//     //verificamos uso de extension mail
	//     $aMail = explode('@',$object->email);
	//     if ($aMail[1] != $mailDefault)
	//       {
	// 	$error++;
	// 	$mesg.='<div class="error">'.$langs->trans("ResultKo").'<br>'.$langs->trans('Error, no esta permitido el uso de ese correo.').'</div>';
	//       }
	//   }
	//buscando el email en adherent
	//mant/lib/adherent.lib.php
	// $idAdherent = adherent_fetch('',$object->email);
	// if ($idAdherent <=0)
	//   {
	//     $error++;
	//     $mesg.='<div class="error">'.$langs->trans("ResultKo").'<br>'.$langs->trans('No existe registro del correo, favor contactarse con el Administrador.').'</div>';	
	//   }
	// if (!empty($object->email) && empty($error))
	//   {
	//     $emailto = $object->email;
	//     //buscando el email en adherent
	//     //mant/lib/adherent.lib.php
	//     $idAdherent = adherent_fetch('',$object->email);
	//extrafields
	//mant/lib/adherent.lib.php
		$aArray = adherent_fetch_ext($fk_member);
		$object->fk_charge = $aArray['fk_charge'];
		$object->fk_departament = $aArray['fk_departament'];
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
	//cambiando a validado
	//$object->statut = 1;
		$id = $object->create($user);
		if ($id > 0)
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
			$sendto   = $emailto;
			$email_from = $conf->global->MAIN_MAIL_EMAIL_FROM;
			$tmpbody = htmlsendemail($id,$code,$url);
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
				$mesg='<div class="ok">'.$langs->trans("MailSuccessfulySent",$mailfile->getValidAddress($object->email_from,2),$mailfile->getValidAddress($object->sendto,2)).'</div>';
				header("Location: ficheemail.php?id=".$id.'&action=edit&code='.$code);
				exit;
			}
			else
			{
				$mesg='<div class="error">'.$langs->trans("ResultKo").'<br>'.$mailfile->error.' '.$result.'</div>';
				$action = 'create';
			}
		}
		else
		{
			$action = 'create';
			$mesg='<div class="error">'.$object->error.'</div>';
		}
	}
	else
	{
		if (empty($error))
			$mesg = '<div class="error">'.$langs->trans("Error, registre su email y el numero interno, gracias.").'</div>';
		$action = 'create';
	}
}

// Add
if ($action == 'updateemail')
{
	$object->fetch($id);
	$object->address_ip     = $_SERVER['REMOTE_ADDR'];
	$object->fk_member      = GETPOST("fk_member");
	$object->entity         = $entity;
	$object->fk_charge      = GETPOST("fk_charge");
	$object->fk_departament = GETPOST("fk_departament");
	$object->speciality  = GETPOST("speciality");
	if ($object->fk_charge && $object->speciality)
	{
		$id = $object->update($user);
		if ($id > 0)
		{
			header("Location: ficheemail.php?id=".$id);
			exit;
		}
		$action = 'edit';
		$mesg='<div class="error">'.$object->error.'</div>';
	}
	else
	{
		$mesg='<div class="error">'.$langs->trans("Errorchargerequired").'</div>';
	$action="create";   // Force retour sur page creation
}
}

// updateconfirm
if ($action == 'updateconfirm')
{
	$lReg = false;
	$object->fetch($id);
	if ($object->statut_job >=1)
		$lReg = true;
	$object->address_ip     = $_SERVER['REMOTE_ADDR'];
	$object->description_confirm = GETPOST('description_confirm');
	$object->statut_job          = GETPOST('confirm');

	if ($object->description_confirm && $object->statut_job && !$lReg)
	{
		$result = $object->update($user);
		$emailfrom = $object->email;
		if ($object->email)
		{
			$textmsg = '';

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
			$object->fk_user_assign;
			if ($objUser->fetch($object->fk_user_assign))
				$emailto = $objUser->email;

			
			$addr_bcc = '';
			if ($object->statut_job == 1)
				$tmpsujet = $langs->trans('Compliance work order').' '.$object->ref;
			if ($object->statut_job == 2)
				$tmpsujet = $langs->trans('Nonconformity of the work order').' '.$object->ref;
			
			$sendto   = $emailto;
			$email_from = $conf->global->MAIN_MAIL_EMAIL_FROM;
			$textmsg.='<p>'.$object->description_confirm.'</p>';
			$tmpbody = htmlsendemailconfirm($id,$textmsg,$url);
			$msgishtml = 1;
			$email_errorsto = $conf->global->MAIN_MAIL_ERRORS_TO;
			$arr_css = array('bgcolor' => '#FFFFCC');
			$mailfile = new CMailFile($tmpsujet,$sendto,$email_from,$tmpbody, $arr_file,$arr_mime,$arr_name,$addr_cc, $addr_bcc, 0, $msgishtml,$email_errorsto,$arr_css);
			$result=$mailfile->sendfile();
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
			$error++;
			$mesg='<div class="error">'.$object->error.'</div>';
		}
		if (empty($error))
		{
			$db->commit();
			header("Location: ficheemail.php?id=".$id."&code=".$code);
			exit;
		}
		else
			$db->rollback();

		$action = 'confirm';
		$mesg='<div class="error">'.$object->error.'</div>';
	}
	else
	{
		$mesg='<div class="error">'.$langs->trans("Errordescriptionrequired").'</div>';
	$action="confirm";   // Force retour sur page creation
}
}

// Modification entrepot
if ($action == 'update' && $_POST["cancel"] <> $langs->trans("Cancel"))
{
	$error = 0;
	if ($object->fetch($_POST["id"]))
	{
		$object->fk_property = GETPOST('fk_property');
		$object->fk_location = GETPOST('fk_location');
		$object->internal   =  GETPOST('internal');
		$object->speciality = GETPOST('speciality');
		$object->detail_problem = GETPOST('detail_problem','alpha');
		$object->statut = 1;
		if (empty($object->fk_property))
		{
			$error++;
			$mesg = '<div class="error">'.$langs->trans('Error, property is required').'</div>';
		}
		if (empty($object->fk_location))
		{
			$error++;
			$mesg = '<div class="error">'.$langs->trans('Error, location is required').'</div>';
		}
		if (empty($object->detail_problem))
		{
			$error++;
			$mesg = '<div class="error">'.$langs->trans('Error, detail problem is required').'</div>';
		}
		if (empty($error))
		{
			if ( $object->update($_POST["id"], $user) > 0)
			{
				$action = '';
				$_GET["id"] = $_POST["id"];
				$mesg = '<div class="ok">'.$langs->trans('Actualizacion satistactoria').'</div>';
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
		}
	}
	else
	{
		$action = 'edit';
		$_GET["id"] = $_POST["id"];
		$mesg = '<div class="error">'.$object->error.'</div>';
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

$objProperty = new Mproperty($db);
$objLocation = new Mlocation($db);

$form=new Formv($db);
//$formadd=new Formadd($db);

// $aArrjs = array();
// $help_url='EN:Module_Contab_En|FR:Module_Contab|ES:M&oacute;dulo_Contab';
// $aArrcss = array('/mant/css/style-desktop.css');
// $conf->dol_hide_leftmenu = 0;
// llxHeader("",$langs->trans("Managementmant"),$help_url,'','','',$aArrjs,$aArrcss);

llxHeaderVierge($langs->trans("Newticket"));


if (empty($id) || $action == 'create')
{
	print_titre($langs->trans("Newticket"));
	print '<br>';
	if (empty($object->ref)) $object->ref = '(PROV)';
	print '<form action="ficheemail.php" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="addemail">';
	
	dol_htmloutput_errors($mesg);

	print '<table class="border" width="100%">';
	
	// ref numeracion automatica de la OT
	print '<tr><td class="fieldrequired" width="20%">'.$langs->trans('User').'</td>';
	
	print '<td colspan="2">';
	//print $formadd->select_member('', 'fk_member', '', 1);
	print $form->select_member('','fk_member', " d.statut = 1 ",1,0,0,array(),0);
	print '</td>';
	print '</tr>';
	// print '<input id="email" type="text" value="'.$object->email.'" name="email" size="30">'.(!empty($conf->global->MANT_USE_EXTENSION_MAIL_COMPANY)?'@'.$conf->global->MANT_EXTENSION_MAIL_DEFAULT:'');
	//    print '</td></tr>';
	
	print '</table>';
	
	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Send").'"></center>';
	
	print '</form>';
}
else
{
	if ($id || $ref)
	{
		dol_htmloutput_mesg($mesg);      
		$result = $object->fetch($id,$ref,$entity);
		if ($object->tokenreg != $code)
		{
			$mesg = '<div class="error">'.$langs->trans('no esta autorizado para acceder.').'</div>';
			dol_htmloutput_mesg($mesg);      
			exit;
		}
	 if ($object->statut > 0 && $action != 'confirm') //cerrarmos la edicion ya que se encuentra concluido
	 $action = '';

	 $objAdherent = new Adherent($db);
	 if ($result < 0)
	 {
	 	dol_print_error($db);
	 }
	 if ( ($action == 'updateedit') )
	 {
	 	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
		 //$tmparray=getProperty(GETPOST('country_id','int'),'all',$db,$langs,0);
	 	$tmparray['id'] = GETPOST('fk_property');
	 	$tmparray['internal'] = GETPOST('internal');

	 	if (! empty($tmparray['id']))
	 	{
		 //$object->fetch(GETPOST('id'));
	 		$object->fk_property = $tmparray['id'];
	 		$object->internal = $tmparray['internal'];
		 //$_GET['id']=$object->id;
	 		$action='edit';
	 	}
	 }
	 
	 /*
	  * Affichage fiche
	  */
	 if ($action <> 'edit' && $action <> 're-edit')
	 {
		 //$head = fabrication_prepare_head($object);
	 	if ($object->statut <= 1)
	 	{
	 		print '<span>';
	 		print $langs->trans('Se ha registrado su requerimiento con los siguientes datos: ');
	 		print '</span>';
	 	}
	 	dol_fiche_head($head, 'card', $langs->trans("Ticket"), 0, 'mant');
	 	print '<br>';

	 	print '<table class="border" width="100%">';

	 	
		 // ref numeracion automatica de la OT
	 	print '<tr><td width="20%">'.$langs->trans('Jobsordernumber').'</td><td colspan="2">';
	 	print $object->ref;
	 	print '</td></tr>';
	 	
		 //email
	 	print '<tr><td width="20%">'.$langs->trans('Email').'</td><td colspan="2">';
	 	print $object->email;
	 	print '</td></tr>';

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
	 	
		 //descripcion
	 	print '<tr><td >'.$langs->trans('Detailtheproblem').'</td><td colspan="2">';
	 	print $object->detail_problem;
	 	print '</td></tr>';

		 //descripcion confirm
	 	if ($object->statut_job)
	 		print '<tr><td >'.$langs->trans('Confirmation or reject').'</td><td colspan="2">';
	 	print $object->description_confirm;
	 	print '</td></tr>';
	 	
		 // Statut
	 	print '<tr><td>'.$langs->trans("Status").'</td><td colspan="2">'.$object->getLibStatut(6).'</td></tr>';
	 	
	 	print "</table>";
	 	
	 	print '</div>';
	 	if ($object->statut <= 1)
	 	{
	 		print '<span>';
	 		print '<p>'.$langs->trans('Un funcionario de la Gerencia de Administración se comunicará con usted próximamente');
	 		print '</p>';
	 		print '<p>'.$langs->trans('Por favor registre el número de Orden de Trabajo para hacer seguimiento a su requerimiento');
	 		print '</p>';
	 		print '<p>'.$langs->trans('Le agradeceremos reportar la conformidad correspondiente cuando su requerimiento haya sido atendido. Para este efecto le enviaremos un correo electrónico una vez se haya concluido el trabajo');
	 		print '</p>';

	 		print '</span>';
	 	}

	 	if ($object->statut >= 3)
	 	{
	 		dol_fiche_head($head, 'card', $langs->trans("Programming of work"), 0, 'mant');
	 		
	 		print '<table class="border" width="100%">';
	 		
		 // Especiality
	 		print '<tr><td width="20%">'.$langs->trans('Speciality').'</td><td colspan="2">';
	 		print select_speciality($object->speciality_prog,'speciality_prog','',1,1);
	 		print '</td></tr>';
		 //descripcion
	 		print '<tr><td >'.$langs->trans('Description').'</td><td colspan="2">';
	 		print $object->description_prog;
	 		print '</td></tr>';
	 		
		 // dateini
	 		print '<tr><td>'.$langs->trans('Dateini').'</td><td colspan="2">';
	 		print dol_print_date($object->date_ini_prog,'daytext');
	 		
	 		print '</td></tr>';

		 // datefin
	 		print '<tr><td>'.$langs->trans('Datefin').'</td><td colspan="2">';
	 		print dol_print_date($object->date_fin_prog,'daytext');
	 		print '</td></tr>';

		 // imagen ini
	 		print '<tr class="hideonsmartphone">';
	 		print '<td>'.$langs->trans("Photobeforestartingwork").'</td>';
	 		print '<td colspan="2">';
	 		if ($object->image_ini) print $object->showphoto('ini',$object,100);
	 		print '</td>';
	 		print '</tr>';
	 		

	 		if ($object->statut >= 4)
	 		{
	 			
			 //descripcion job
	 			print '<tr><td >'.$langs->trans('Descriptionofworkperformed').'</td><td colspan="2">';
	 			print $object->description_job;
	 			print '</td></tr>';
	 			
			 // typemant
	 			print '<tr><td width="20%">'.$langs->trans('Typeofwork').'</td><td colspan="2">';
	 			select_typemant($object->typemant,'typemant','',1,1);
	 			print '</td></tr>';
	 			
	 			
			 // imagen fin
	 			print '<tr class="hideonsmartphone">';
	 			print '<td>'.$langs->trans("Phototocompletethework").'</td>';
	 			print '<td colspan="2">';
	 			if ($object->image_fin) print $object->showphoto('fin',$object,100);
	 			print '</td>';
	 			print '</tr>';

			 // description_confirm
	 			if ($object->statut_job >= 1)
	 			{
	 				print '<tr><td width="20%">'.$langs->trans('Confirmationorrejection').'</td><td colspan="2">';
	 				print $object->description_confirm;
	 				print '</td></tr>';
	 			}
	 		}
	 		print "</table>";
	 		print '</div>';
	 	}
	 }

	 /*
	  * Edition fiche
	  */
	 if (($action == 'edit' || $action == 're-edit'))
	 {
	 	print_fiche_titre($langs->trans("Updateticket"), $mesg);
	 	
	 	print "\n".'<script type="text/javascript" language="javascript">';
	 	print '$(document).ready(function () {
	 		$("#selectfk_property").change(function() {
	 			document.form_index.action.value="updateedit";
	 			document.form_index.submit();
	 		});
	 	});';
	 	print '</script>'."\n";
	 	
	 	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'" name="form_index">';
	 	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	 	print '<input type="hidden" name="code" value="'.$code.'">';
	 	print '<input type="hidden" name="action" value="update">';
	 	print '<input type="hidden" name="id" value="'.$object->id.'">';
	 	
	 	print '<table class="border" width="100%">';
	 	
		 // ref numeracion automatica de la OT
	 	print '<tr><td width="20%">'.$langs->trans('Jobsordernumber').'</td><td colspan="2">';
	 	print $object->ref;
	 	print '</td></tr>';
	 	
		 // email
	 	print '<tr><td width="20%">'.$langs->trans('Email').'</td><td colspan="2">';
	 	print $object->email;
	 	print '</td></tr>';
	 	
		 // internal
	 	print '<tr><td  class="fieldrequired" width="20%">'.$langs->trans('Internal').'</td><td colspan="2">';
	 	print '<input id="internal" type="text" value="'.$object->internal.'" name="internal" size="5">';
	 	print '</td></tr>';
	 	
		 // property
	 	print '<tr><td class="fieldrequired">'.$langs->trans('Property').'</td><td colspan="2">';
	 	$fk_property = ($object->fk_property<=0?(!empty($conf->global->MANT_DEFAULT_PROPERTY)?$conf->global->MANT_DEFAULT_PROPERTY:2):$object->fk_property);
	 	$filter = " AND t.entity = ".$conf->entity;
	 	$res = $objProperty->fetchAll('ASC','label',0,0,array('status'=>1),'AND',$filter);
	 	$options = '';
	 	$lines =$objProperty->lines;
	 	foreach ((array) $lines AS $j => $line)
	 	{
	 		$selected = '';
	 		if (GETPOST('fk_property') == $line->id) $selected = ' selected';
	 		$options.= '<option value="" '.$selected.'>'.$line->label.' ('.$line->ref.')'.'</option>';
	 	}
	 	print '<select name="fk_property">'.$options.'</select>';
		 //print $objProperty->select_property($fk_property,'fk_property','',40,1);
	 	print '</td></tr>';
	 	
		 // location
	 	print '<tr><td class="fieldrequired">'.$langs->trans('Location').'</td><td colspan="2">';
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

	 	print '</td></tr>';	  
	 	
		 //descripcion
	 	print '<tr><td class="fieldrequired">'.$langs->trans('Detailtheproblem').'</td><td colspan="2">';
	 	print '<textarea name="detail_problem" cols="80" rows="5">'.$object->detail_problem.'</textarea>';
	 	print '</td></tr>';
	 	
	 	print '</table>';

	 	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Send").'"></center>';
	 	
	 	print '</form>';
	 	
	 }

	 /*
	  * Edition fiche
	  */
	 $lReg = false;
	 if ($object->statut_job >= 1)
	 	$lReg = true;
	 if (($action == 'confirm') && !$lReg)
	 {
	 	print_fiche_titre($langs->trans("Compliance work order"), $mesg);
	 	
	 	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'" name="form_index">';
	 	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	 	print '<input type="hidden" name="code" value="'.$code.'">';
	 	print '<input type="hidden" name="action" value="updateconfirm">';
	 	print '<input type="hidden" name="id" value="'.$object->id.'">';
	 	
	 	print '<table class="border" width="100%">';
	 	
		 // email
	 	print '<tr><td width="20%">'.$langs->trans('Email').'</td><td colspan="2">';
	 	print $object->email;
	 	print '</td></tr>';
	 	
		 // description_confirm
	 	print '<tr><td>';
	 	print $langs->trans('Confirmation or reject');
	 	print '</td>';
	 	
	 	print '<td colspan="2">';
	 	print '<textarea name="description_confirm" cols="80" rows="5">'.$object->description_confirm.'</textarea>';
	 	print '</td>';
	 	print '</tr>';

	 	print '<tr><td  class="fieldrequired" width="20%">'.$langs->trans('Isinconformity').'</td><td colspan="2">';
	 	print '<input id="confirm" type="radio" value="1" name="confirm">'.$langs->trans('Yes').'&nbsp;<input id="confirm" type="radio" value="2" name="confirm" chequed="chequed">'.$langs->trans('Not');
	 	print '</td></tr>';
	 	
	 	print '</table>';
	 	
	 	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Send").'"></center>';
	 	
	 	print '</form>';
	 	
	 }
	 
	}
}

print
llxFooterVierge();

$db->close();

function htmlemail($id)
{
	global $object,$langs,$objCharge,$objDepartament;
	$html = '<!DOCTYPE HTML>';
	$html.= '<html>';
	$html.= '<head>';
	$html.= '<link rel="stylesheet" media="screen" href="http://192.168.43.98/bcb/mant/css/style-email.css">';
	$html.= '</head>';
	$html.= '<body>';
	$html.= '<form class="contact_form" action="http://192.168.43.98/bcb/mant/jobs/fichereg.php" method="POST">';
	$html.= '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	$html.= '<input type="hidden" name="action" value="updateemail">';
	$html.= '<input type="hidden" name="rowid" value="'.$id.'">';

	
	dol_htmloutput_mesg($mesg);

	$html.= '<table class="border" width="100%">';
	$html.= '<ul>';
  // ref numeracion automatica de la OT
	$html.= '<li>';
	$html.= '<label>'.$langs->trans('Jobsordernumber').'</label>';
	$html.= $object->ref;
  //  $html.= '</td></tr>';
	$html.= '</li>';
	$html.= '<li>';
  // solicitante
	$html.= '<label for="fk_member">'.$langs->trans('Solicitante').'</label>';
	$html.= select_adherent($user->fk_member,'fk_member','',0,1);
  //  $html.= '</td></tr>';
	$html.= '</li>';
	$html.= '<li>';

  // charge
	$html.= '<label for="fk_charge">'.$langs->trans('Charge').'</label>';
	$html.= $objCharge->select_charge($object->fk_charge,'fk_charge','',0,1);
  //$html.= '</td></tr>';
	$html.= '</li>';
	$html.= '<li>';

  // departament
	$html.= '<label for="fk_departament">'.$langs->trans('Departament').'</label>';
	$html.= $objDepartament->select_departament($object->fk_departament,'fk_departament','',0,1);
  //  $html.= '</td></tr>';
	$html.= '</li>';
	$html.= '<li>';

  // Especiality
	$html.= '<label>'.$langs->trans('Speciality').'</label>';
	$html.= select_speciality($object->speciality,'speciality','',1);
  //  $html.= '</td></tr>';
	$html.= '</li>';
	$html.= '<li>';

  //  $html.= '</table>';

	$html.= '<button class="submit" type="submit">'.$langs->trans("Register").'</button>';
	$html.= '</li>';
	$html.= '</ul>';

	$html.= '</form>';
	$html.= '</body>';
	$html.= '</html>';
	return $html;
}


/**
 * Show header for new member
 *
 * @param 	string		$title				Title
 * @param 	string		$head				Head array
 * @param 	int    		$disablejs			More content into html header
 * @param 	int    		$disablehead		More content into html header
 * @param 	array  		$arrayofjs			Array of complementary js files
 * @param 	array  		$arrayofcss			Array of complementary css files
 * @return	void
 */
function llxHeaderVierge($title, $head="", $disablejs=0, $disablehead=0, $arrayofjs='', $arrayofcss='')
{
	global $user, $conf, $langs, $mysoc;
	top_htmlhead($head, $title, $disablejs, $disablehead, $arrayofjs, $arrayofcss); // Show html headers
	print '<body id="mainbody" class="publicnewmemberform" style="margin-top: 10px;">';

	// Print logo
	$urllogo=DOL_URL_ROOT.'/theme/login_logo.png';

	if (! empty($mysoc->logo_small) && is_readable($conf->mycompany->dir_output.'/logos/thumbs/'.$mysoc->logo_small))
	{
		$urllogo=DOL_URL_ROOT.'/viewimage.php?cache=1&amp;modulepart=companylogo&amp;file='.urlencode('thumbs/'.$mysoc->logo_small);
	}
	elseif (! empty($mysoc->logo) && is_readable($conf->mycompany->dir_output.'/logos/'.$mysoc->logo))
	{
		$urllogo=DOL_URL_ROOT.'/viewimage.php?cache=1&amp;modulepart=companylogo&amp;file='.urlencode($mysoc->logo);
		$width=128;
	}
	elseif (is_readable(DOL_DOCUMENT_ROOT.'/theme/dolibarr_logo.png'))
	{
		$urllogo=DOL_URL_ROOT.'/theme/dolibarr_logo.png';
	}
	// print '<center>';
	// print '<img alt="Logo" id="logosubscribe" title="" src="'.$urllogo.'" />';
	// print '</center><br>';

	print '<div style="margin-left: 50px; margin-right: 50px;">';
}

/**
 * Show footer for new member
 *
 * @return	void
 */
function llxFooterVierge()
{
	print '</div>';

	printCommonFooter('public');

	print "</body>\n";
	print "</html>\n";
}

?>
