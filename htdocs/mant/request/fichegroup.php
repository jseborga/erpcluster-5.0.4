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
 *	\file       htdocs/mant/jobs/fichegroup.php
 *	\ingroup    Ordenes de Trabajo por grupo
*	\brief      Page fiche mantenimiento 
 */

require("../../main.inc.php");
// require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
// require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/request/class/mworkrequest.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/request/class/mworkrequestcontact.class.php';

// require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobs.class.php';
// require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobscontact.class.php';
// require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobsorder.class.php';
// require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobsuser.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/property/class/mproperty.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/property/class/mlocation.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/charge/class/pcharge.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/departament/class/pdepartament.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/equipment/class/mequipment.class.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

if ($conf->assets->enabled)
  {
    require_once DOL_DOCUMENT_ROOT.'/assets/assets/class/assets.class.php';
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

$langs->load("companies");
$langs->load("commercial");
$langs->load("bills");
$langs->load("banks");
$langs->load("users");
$langs->load("other");
$langs->load("mant");

$action=GETPOST('action');

$id  = GETPOST("id");
$ref = GETPOST('ref','alpha');
$idw = GETPOST('idw');
$idu = GETPOST('idu');
$fk_equipment = GETPOST('fk_equipment');

if (! empty($user->societe_id)) $socid=$user->societe_id;
$url = $dolibarr_main_url_root;

// $sortfield = GETPOST("sortfield");
// $sortorder = GETPOST("sortorder");

// if (! $sortfield) $sortfield="p.period_month";
// if (! $sortorder) $sortorder="DESC";

$mesg = '';
$tmparray = array();

//$object      = new Mjobs($db);
$objwork     = new Mworkrequest($db);
$objworkcont = new Mworkrequestcontact($db);
$objProperty = new Mproperty($db);
$objLocation = new Mlocation($db);
$objSoc      = new Societe($db);
$objEquipment= new Mequipment($db);
$objUser     = new User($db);
//$objJobsuser = new Mjobsuser($db);
if ($conf->assets->enabled)
  $objassets = new Assets($db);


/*
 * Actions
 */


// Addgroup
//asignacion de trabajos de ticket por grupo
if ($action == 'addgroup' && $user->rights->mant->tick->ass)
  {
    $error=0;
    $date_create  = dol_now();
    $db->begin();

    foreach ((array) $_SESSION['seletick'] AS $idw => $value)
      {
	$result = $objwork->fetch($idw);
	//debemos actualizar
	$objwork->fk_soc = GETPOST('fk_soc');
	$objwork->speciality     = GETPOST("speciality");
	$objwork->fk_user_assign = $user->id;
	$objwork->date_assign = dol_now();
	$objwork->statut = 2;
	if (empty($objwork->speciality))
	  {
	    $error++;
	    $mesg.='<div class="error">'.$langs->trans('Error, speciality is required').'</div>';
	  }

	if ($objwork->fk_soc <= 0)
	  {
	    $error++;
	    $mesg.='<div class="error">'.$langs->trans('Error, fk_soc is required').'</div>';
	  }	
	if (empty($error)) 
	  {
	    $res = $objwork->update($user);
	    if ($res > 0)
	      {
		//verificamos si es una empresa externa
		$socid = $objwork->fk_soc;
		$objSoc = new Societe($db);
		$lSelectSocid = true;
		$resu = $objSoc->fetch($socid);
		if ($resu > 0)
		  $objTypent = fetch_typent($objSoc->typent_id);
		if ($objTypent->id == $objSoc->typent_id && $objTypent->code == 'TE_BCB')
		  $lSelectSocid = false;
		//envio de correo
		if ($emailto && $object->statut == 2 &&
		    $lSelectSocid)
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
		      }
		    else
		      {
			$error++;
			$mesg='<div class="error">'.$langs->trans("ResultKo").
			  '<br>'.$mailfile->error.' '.$result.'</div>';
			$action = 'create';
		      }
		  }
		//fin envio de correo
	      }
	    else
	      {
		$error++;
		$mesg.= '<div class="error">'.$objwork->error.'</div>';
	      }
	  }
	else
	  {
	    $action="create";   // Force retour sur page creation
	  }
      }//foreach
    if (empty($error))
      {
	$db->commit();
	  $mesg='<div class="ok">'.$langs->trans("SuccessfulyAssign").'</div>';
	if ($conf->global->MANT_SEND_EMAIL)
	  $mesg='<div class="ok">'.$langs->trans("MailSuccessfulySent").'</div>';

	header("Location: ".DOL_URL_ROOT."/mant/request/liste.php?mesg=".$mesg);
	exit;
      }
    else
      { 
	$db->rollback();
	$mesg.= '<div class="error">'.$langs->trans('Error, no se pudo asignar los tickets, revise').'</div>';
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
$objCharge = new Pcharge($db);
$objDepartament = new Pdepartament($db);

$form=new Form($db);

$aArrjs = array();
$help_url='EN:Module_Contab_En|FR:Module_Contab|ES:M&oacute;dulo_Contab';
$aArrcss = array('/mant/css/style-desktop.css');
$conf->dol_hide_leftmenu = 0;
llxHeader("",$langs->trans("Managementmant"),$help_url,'','','',$aArrjs,$aArrcss);



if ($action == 'create' && $user->rights->mant->tick->ass) //asignar
  {
    $aseletick = $_SESSION['seletick'];
    
    //verificamos si biene de work request
    if (!empty($aseletick))
      {
	print_fiche_titre($langs->trans("Assign tickets"));
	//if (empty($object->ref)) $object->ref = '(PROV)';
	
	//obtenemos todos los tickets
	print '<table class="border" width="100%">';
	
	foreach ((array) $aseletick AS $idw => $value)
	  {
	    $result = $objwork->fetch($idw);
	    if ($result > 0 && $objwork->id == $idw)
	      {
		print '<tr>';
		print '<td>'.$objwork->ref.'</td>';
		print '<td>'.$objwork->detail_problem.'</td>';
		print '</tr>';
	      }
	  }
	print '</table>';  
	
	print '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form_index">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="addgroup">';
	
	dol_htmloutput_mesg($mesg);
	
	print '<table class="border" width="100%">';
	
	
	// Especiality
	print '<tr><td class="fieldrequired">'.$langs->trans('Speciality').'</td><td colspan="2">';
	print select_speciality($object->speciality,'speciality','',1);
	print '</td></tr>';
	
	print '<tr><td class="fieldrequired"> '.$langs->trans('Assigningworkto').'</td><td colspan="2">';
	print select_societe($object->fk_soc,'fk_soc','',0,1,0);
	print '</td></tr>';
	
	print '</table>';
	
	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';
	
	print '</form>';
      }
    print '<center><br>'.'<span style="color:#ff0000;">'.$langs->trans('Recomendacion:').'</span>'.' '.$langs->trans('Debe seleccionar correctamente la especialidad y la empresa a asignar el trabajo. Una vez presionado el boton').' '.'<span style="color:#002aff">'.$langs->trans('CREAR').'</span>'.' '.$langs->trans('no se puede volver atras.').'</center>';

  }


llxFooter();

$db->close();
?>
