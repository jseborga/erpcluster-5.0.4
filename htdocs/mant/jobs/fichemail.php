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
 *	\file       htdocs/mant/jobs/fiche.php
 *	\ingroup    Ordenes de Trabajo
 *	\brief      Page fiche mantenimiento 
 */
// define("NOLOGIN",1);
// define("NOCSRFCHECK",1);

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobs.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobscontact.class.php';

require_once DOL_DOCUMENT_ROOT.'/mant/lib/mant.lib.php';
require_once DOL_DOCUMENT_ROOT.'/mant/lib/adherent.lib.php';

require_once DOL_DOCUMENT_ROOT.'/mant/charge/class/pcharge.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/departament/class/pdepartament.class.php';

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

$langs->load("mant@mant");

$action=GETPOST('action');

$id        = GETPOST("rowid");


// $sortfield = GETPOST("sortfield");
// $sortorder = GETPOST("sortorder");

// if (! $sortfield) $sortfield="p.period_month";
// if (! $sortorder) $sortorder="DESC";

$mesg = '';

$object = new Mjobs($db);
$objCharge = new Pcharge($db);
$objDepartament = new Pdepartament($db);
/*
 * Actions
 */

// Add
if ($action == 'addemail')
  {
    $date_create  = dol_mktime(12, 0, 0, date('m'),  date('d'),  date('Y'));

    $object->address_ip     = $_SERVER['REMOTE_ADDR'];
    $object->ref            = '(PROV)';
    $object->entity         = 1;
    $object->email          = GETPOST('email');
    $object->date_create    = $date_create;
    $object->statut         = 0;
    $emailto = $object->email;

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
    
    $object->ref = $numref;
    
    //cambiando a validado
    $object->statut = 1;
    if ($object->email) {
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
	  //	  $arr_mime[] = 'text/html';
	  $arr_mime[] = 'aplication/rtf';

	  $tmpsujet = $langs->trans('Send email request');
	  $sendto   = $emailto;
	  $email_from = $conf->global->MAIN_MAIL_EMAIL_FROM;
	  $tmpbody = htmlemail($id);
	  $msgishtml = 1;
	  $email_errorsto = $conf->global->MAIN_MAIL_ERRORS_TO;
	  $arr_css = array('bgcolor' => '#ffffaa');
	  $mailfile = new CMailFile($tmpsujet,$sendto,$email_from,$tmpbody, $arr_file,$arr_mime,$arr_name,'', '', 0, $msgishtml,$email_errorsto,$arr_css);
	  //$result=$mailfile->sendfile();
	  //quitar
	  $result = 1;
	  if ($result)
	    {
	      print $mesg='<div class="ok">'.$langs->trans("MailSuccessfulySent",$mailfile->getValidAddress($object->email_from,2),$mailfile->getValidAddress($object->sendto,2)).'</div>';
	    }
	  else
	    {
	      print $mesg='<div class="error">'.$langs->trans("ResultKo").'<br>'.$mailfile->error.' '.$result.'</div>';
	    }

	  header("Location: fichemail.php?id=".$id);
	  exit;
	}
      $action = 'create';
      $mesg='<div class="error">'.$object->error.'</div>';
    }
    else {
      $mesg='<div class="error">'.$langs->trans("Errorchargerequired").'</div>';
      $action="create";   // Force retour sur page creation
    }
  }

// Add
if ($action == 'updateemail')
  {
    $object->fetch($id);
    $object->address_ip     = $_SERVER['REMOTE_ADDR'];
    $object->fk_member      = GETPOST("fk_member");
    $object->entity         = 1;
    $object->fk_charge      = GETPOST("fk_charge");
    $object->fk_departament = GETPOST("fk_departament");
    $object->speciality  = GETPOST("speciality");
    if ($object->fk_charge && $object->speciality)
      {
	$id = $object->update($user);
	if ($id > 0)
	  {
	    header("Location: fichemail.php?id=".$id);
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

// Add
if ($action == 'add' && $user->rights->mant->jobs->crear)
  {
    $date_create  = dol_mktime(12, 0, 0, date('m'),  date('d'),  date('Y'));

    // $date_ini  = dol_mktime(12, 0, 0, GETPOST('date_inimonth'),  GETPOST('date_iniday'),  GETPOST('date_iniyear'));
    // $date_fin  = dol_mktime(12, 0, 0, GETPOST('date_finmonth'),  GETPOST('date_finday'),  GETPOST('date_finyear'));
    $object->address_ip     = $_SERVER['REMOTE_ADDR'];
    $object->ref            = GETPOST("ref");
    $object->fk_member      = GETPOST("fk_member");
    $object->entity         = $conf->entity;
    $object->fk_charge      = GETPOST("fk_charge");
    $object->fk_departament = GETPOST("fk_departament");
    $object->speciality  = GETPOST("speciality");
    $object->date_create    = $date_create;
    $object->statut         = 0;
    if ($object->fk_charge && $object->speciality) {
      $id = $object->create($user);
      if ($id > 0)
	{
	  header("Location: fiche.php?id=".$id);
	  exit;
	}
      $action = 'create';
      $mesg='<div class="error">'.$object->error.'</div>';
    }
    else {
      $mesg='<div class="error">'.$langs->trans("Errorchargerequired").'</div>';
      $action="create";   // Force retour sur page creation
    }
  }

// Add
if ($action == 'upjobs' && $user->rights->mant->jobs->upjobs)
  {
    $id = GETPOST('id');
    $object->fetch(GETPOST('id'));
    $statut = $object->statut;
    $date_ini = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));    
    $date_fin = dol_mktime(12, 0, 0, GETPOST('df_month'),GETPOST('df_day'),GETPOST('df_year'));    
    $object->speciality_job = GETPOST("speciality_job");
    $object->date_ini    = $date_ini;
    $object->date_fin    = $date_fin;
    $object->description = GETPOST('description');
    //$object->statut      = 2;
    if ($object->date_ini && $object->speciality_job && $statut == 1) 
      {
	$result = $object->update($user);
	if ($result > 0)
	  {
	    header("Location: fiche.php?id=".$id.'&action=editjobs');
	    exit;
	  }
	$action = 'editjobs';
	$mesg='<div class="error">'.$object->error.'</div>';
      }
    else 
      {
	$mesg='<div class="error">'.$langs->trans("Errordateinirequired").'</div>';
	$action="editjobs";   // Force retour sur page creation
      }
  }

// Add
if ($action == 'addcontact' && $user->rights->mant->jobs->upjobs)
  {
    $id = GETPOST('id');
    $object->fetch(GETPOST('id'));
    $statut = $object->statut;

    $objJobsContact = new Mjobscontact($db);
    $objJobsContact->fk_contact = GETPOST("fk_contact");
    $objJobsContact->fk_charge    = 1;
    $objJobsContact->fk_jobs = $id;
    $objJobsContact->detail    = GETPOST('detail');
    $objJobsContact->statut      = 1;
    $objJobsContact->tms = date('YmdHis');
    if ($objJobsContact->fk_contact && $statut == 1) 
      {
	$result = $objJobsContact->create($user);
	if ($result > 0)
	  {
	    header("Location: fiche.php?id=".$id.'&action=editjobs');
	    exit;
	  }
	$action = 'editjobs';
	$mesg='<div class="error">'.$object->error.'</div>';
      }
    else 
      {
	$mesg='<div class="error">'.$langs->trans("Errorcontactrequired").'</div>';
	$action="editjobs";   // Force retour sur page creation
      }
  }


/*
 * Confirmation de la re validation
 */
if ($action == 'revalidate')
  {
    $object->fetch(GETPOST('id'));
    //cambiando a validado
    $object->statut = 0;
    //update
    $object->update($user);
    header("Location: fiche.php?id=".$_GET['id']);
  }

// Delete period
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->contab->delperiod)
{
  $object = new Contabperiodo($db);
  $object->fetch($_REQUEST["id"]);
  $result=$object->delete($user);
  if ($result > 0)
    {
      header("Location: ".DOL_URL_ROOT.'/contab/period/liste.php');
      exit;
    }
  else
    {
      $mesg='<div class="error">'.$object->error.'</div>';
      $action='';
    }
 }

// Modification entrepot
if ($action == 'update' && $_POST["cancel"] <> $langs->trans("Cancel"))
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



if ($action == 'create' && $user->rights->mant->jobs->crear)
{
  print_fiche_titre($langs->trans("Neworderjobs"));
  if (empty($object->ref)) $object->ref = '(PROV)';
  print "<form action=\"fichemail.php\" method=\"post\">\n";
  print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
  print '<input type="hidden" name="action" value="addemail">';
  
  dol_htmloutput_mesg($mesg);

  print '<table class="border" width="100%">';

  // ref numeracion automatica de la OT
  print '<tr><td class="fieldrequired" width="20%">'.$langs->trans('Email').'</td><td colspan="2">';
  print '<input id="email" type="text" value="'.$object->email.'" name="email" size="30">';
  print '</td></tr>';


  print '</table>';

  print '<center><br><input type="submit" class="button" value="'.$langs->trans("Send").'"></center>';

  print '</form>';
}
else
{
  if ($_GET["id"])
    {
      dol_htmloutput_mesg($mesg);      
      $result = $object->fetch($_GET["id"]);
      $objAdherent = new Adherent($db);
      if ($result < 0)
	{
	  dol_print_error($db);
	}
      
      
      /*
       * Affichage fiche
       */
      if ($action <> 'edit' && $action <> 're-edit')
	{
	  //$head = fabrication_prepare_head($object);
	  
	  dol_fiche_head($head, 'card', $langs->trans("Jobs"), 0, 'mant');
	  
	  /*
	   * Confirmation de la validation
	   */
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
	      $object->statut = 1;
	      //update
	      $object->update($user);
	      $action = '';
	    }

	  /*
	   * Close period
	   */
	  if ($action == 'close')
	    {
	      $object->fetch(GETPOST('id'));
	      //cambiando a validado
	      $object->statut = 2;
	      //update
	      $object->update($user);
	      $action = '';
	      //header("Location: fiche.php?id=".$_GET['id']);
	      
	    }

	  /*
	   * Open period
	   */
	  if ($action == 'open')
	    {
	      $object->fetch(GETPOST('id'));
	      //cambiando a validado
	      $object->statut = 1;
	      //update
	      $object->update($user);
	      $action = '';
	      //header("Location: fiche.php?id=".$_GET['id']);
	      
	    }
	  
	  // Confirm delete third party
	  if ($action == 'delete')
	    {
	      $form = new Form($db);
	      $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deleteperiodaccounting"),$langs->trans("Confirmdeleteperiodaccounting",$object->period_month.' '.$object->period_year),"confirm_delete",'',0,2);
	      if ($ret == 'html') print '<br>';
	    }
	  
	  print '<table class="border" width="100%">';
	  
	  // ref numeracion automatica de la OT
	  print '<tr><td width="20%">'.$langs->trans('Jobsordernumber').'</td><td colspan="2">';
	  print $object->ref;
	  print '</td></tr>';
	  
	  // solicitante
	  $objAdherent->fetch($object->fk_member);
	  print '<tr><td >'.$langs->trans('Solicitante').'</td><td colspan="2">';
	  print $objAdherent->lastname.' '.$objAdherent->firstname;
	  print '</td></tr>';
	  
	  // charge
	  $objCharge->fetch($object->fk_charge);
	  print '<tr><td >'.$langs->trans('Charge').'</td><td colspan="2">';
	  print $objCharge->ref;
	  print '</td></tr>';
	  
	  // departament
	  $objDepartament->fetch($object->fk_departament);
	  print '<tr><td >'.$langs->trans('Departament').'</td><td colspan="2">';
	  print $objDepartament->ref;
	  print '</td></tr>';
	  
	  // Especiality
	  print '<tr><td >'.$langs->trans('Speciality').'</td><td colspan="2">';
	  select_speciality($object->speciality,'speciality','',1,1);
	  print '</td></tr>';
	  
	  // Statut
	  print '<tr><td>'.$langs->trans("Status").'</td><td colspan="2">'.$object->getLibStatut(6).'</td></tr>';
	  	  
	  print "</table>";
	  
	  print '</div>';
	  
	  
	  /* ****************************************** */
	  /*                                            */
	  /* Barre d'action                             */
	  /*                                            */
	  /* ****************************************** */
	  
	  print "<div class=\"tabsAction\">\n";
	  
	  if ($action == '')
	    {
	      if ($user->rights->mant->jobs->crear && $object->statut == 0)
		print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";
	      
	      if (($object->statut==0 ) && $user->rights->mant->jobs->del)
		print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
	      // Valid
	      if ($object->statut == 0 && $user->rights->mant->jobs->val)
		{
		  print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=validate">'.$langs->trans('Validate').'</a>';
		}
	      // ReValid
	      if ($object->statut == 1 && $user->rights->mant->jobs->val)
		{
		  print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=revalidate">'.$langs->trans('Return').'</a>';
		}	      
	      // editjobs
	      if ($object->statut == 1 && $user->rights->mant->jobs->upjobs)
		{
		  print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editjobs">'.$langs->trans('Beginjob').'</a>';
		}

	    }	  

	  print '</div>';

	  //registro de atencion
	  if ($object->statut == 1 && $action == 'editjobs')
	    {
	      //print '<div class="izq">';
	      
	      //print '<section id="section-izq">';
	      
	      $objSoc = new Societe($db);
	      $objSoc->fetch($socid);
	      $aContact = $objSoc->contact_array();
	      $objJobsContact = new Mjobscontact($db);
	      dol_fiche_head($head, 'card', $langs->trans("Beginjob"), 0, 'mant');

	      print '<table class="border" width="100%">';
	      print '<tr><td width="50%">';
	      print '<form action="fiche.php" method="POST">';
	      print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	      print '<input type="hidden" name="action" value="addcontact">';
	      print '<input type="hidden" name="id" value="'.$object->id.'">';
	      

	      print '<table class="border" width="100%">';
	      print '<tr class="liste_titre">';
	      print_liste_field_titre($langs->trans("Name"),"", "","","","");
	      print_liste_field_titre($langs->trans("Charge"),"", "","","","");
	      print_liste_field_titre($langs->trans("Action"),"", "",'','','align="right"');
	      print "</tr>\n";
	      //registro nuevo
	      print '<tr>';
	      print '<td colspan="3">';
	      print $form->selectarray('fk_contact',$aContact,GETPOST('fk_contact'),1);
	      print '</td>';

	      print '</tr>';

	      $aArray = $objJobsContact->list_contact($object->id);

	      $numberContact = 0;
	      foreach ((array) $aArray AS $i => $data)
		{
		  $numberContact++;
		  print '<tr>';
		  print '<td>'.$aContact[$data->fk_contact].'</td>';
		  print '<td></td>';
		  print '<td></td>';
		  print '</tr>';
		}
	      print "</table>";

	      print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'"></center>';

	      print '</form>';
	      // print '</div>';
	      
	      // //print '</section>';
	      // //print '</div>';
	      // //fin izq

	      // //ini der
	      // //print '<div class="izq">';
	      // //print '<section id="section-der">';
	      // dol_fiche_head($head, 'card', $langs->trans("Contact"), 0, 'mant');
	      print '</td><td width="50%" valign="top">';
	      print '<form action="fiche.php" method="POST">';
	      print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	      print '<input type="hidden" name="action" value="upjobs">';
	      print '<input type="hidden" name="id" value="'.$object->id.'">';
	      
	      print '<table class="border" width="99%">';
	      
	      // Especiality
	      print '<tr><td >'.$langs->trans('Speciality').'</td><td colspan="2">';
	      select_speciality($object->speciality,'speciality_job','',1);
	      print '</td></tr>';
	      //descripcion
	      print '<tr><td >'.$langs->trans('Description').'</td><td colspan="2">';
	      print '<textarea name="description" cols="40" rows="5">'.$object->description.'</textarea>';
	      print '</td></tr>';

	      // dateini
	      print '<tr><td class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="2">';
	      $form->select_date($object->date_ini,'di_','','','',"crearperiod",1,1);
	      print '</td></tr>';
	      
	      // datefin
	      print '<tr><td>'.$langs->trans('Datefin').'</td><td colspan="2">';
	      $form->select_date((empty($object->date_fin)?'':$object->date_fin),'df_','','','',"crearperiod",1,1);
	      print '</td></tr>';
	      
	      
	      print "</table>";

	      print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'"></center>';

	      print '</form>';
	      print '</div>';
	      print '</tr>';
	      print '</table>';
	      //print '</section>';
	      //print '</div>';

	      /* ****************************************** */
	      /*                                            */
	      /* Barre d'action                             */
	      /*                                            */
	      /* ****************************************** */
	      
	      // print '<div class="tabsAction">';
	      
	      // if ($action == 'upjobs')
	      // 	{
	      // 	  if ($user->rights->mant->jobs->upjobs && $object->statut == 1)
	      // 	    print "<a class=\"butAction\" href=\"fiche.php?id=".$object->id."\">".$langs->trans("Return")."</a>";
	      // 	}
	      // print '</div>';
	    }

	  if ($object->statut == 2)
	    {
	      dol_fiche_head($head, 'card', $langs->trans("Jobstomake"), 0, 'mant');

	      print '<table class="border" width="100%">';
	      
	      // Especiality
	      print '<tr><td width="20%">'.$langs->trans('Speciality').'</td><td colspan="2">';
	      select_speciality($object->speciality_job,'speciality_job','',1,1);
	      print '</td></tr>';
	      //descripcion
	      print '<tr><td >'.$langs->trans('Description').'</td><td colspan="2">';
	      print $object->description;
	      print '</td></tr>';

	      // dateini
	      print '<tr><td>'.$langs->trans('Dateini').'</td><td colspan="2">';
	      print dol_print_date($object->date_ini,'daytext');

	      print '</td></tr>';
	      
	      // datefin
	      print '<tr><td>'.$langs->trans('Datefin').'</td><td colspan="2">';
	      print dol_print_date($object->date_fin,'daytext');
	      print '</td></tr>';
	      
	      
	      print "</table>";

	      print '</div>';

	      /* ****************************************** */
	      /*                                            */
	      /* Barre d'action                             */
	      /*                                            */
	      /* ****************************************** */
	      
	      print "<div class=\"tabsAction\">\n";
	      
	      if ($action == 'upjobs')
		{
		  if ($user->rights->mant->jobs->upjobs && $object->statut == 1)
		    print "<a class=\"butAction\" href=\"fiche.php?id=".$object->id."\">".$langs->trans("Return")."</a>";
		}
	      print '</div>';
	      
	      
	    }

	  /* ****************************************** */
	  /*                                            */
	  /* Barre d'action                             */
	  /*                                            */
	  /* ****************************************** */
	  
	  print '</div>';
	  print "<div class=\"tabsAction\">\n";
	  
	  if ($action == 'editjobs')
	    {
	      if ($numberContact > 0)
		{
		  if ($user->rights->mant->jobs->upjobs && $object->statut == 1)
		    print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Beginjob")."</a>";
		  else
		    print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Beginjob")."</a>";
		}
	    }
	  print '</div>';
	}
      
      
      /*
       * Edition fiche
       */
      if (($action == 'edit' || $action == 're-edit') && 1)
	{
	  print_fiche_titre($langs->trans("ApplicationsEdit"), $mesg);
	  
	  print '<form action="fiche.php" method="POST">';
	  print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	  print '<input type="hidden" name="action" value="update">';
	  print '<input type="hidden" name="id" value="'.$object->id.'">';
	  
	  print '<table class="border" width="100%">';
	  
	  // ref numeracion automatica de la OT
	  print '<tr><td class="fieldrequired" width="20%">'.$langs->trans('Jobsordernumber').'</td><td colspan="2">';
	  print '<input id="ref" type="text" value="'.$object->ref.'" name="period_year" size="13" maxlength="15">';
	  print '</td></tr>';
	  
	  // solicitante
	  print '<tr><td class="fieldrequired">'.$langs->trans('Solicitante').'</td><td colspan="2">';
	  print select_adherent($object->fk_member,'fk_member','',0,1);
	  print '</td></tr>';
	  
	  // charge
	  print '<tr><td class="fieldrequired">'.$langs->trans('Charge').'</td><td colspan="2">';
	  print $objCharge->select_charge($object->fk_charge,'fk_charge','',0,1);
	  print '</td></tr>';
	  
	  // departament
	  print '<tr><td class="fieldrequired">'.$langs->trans('Departament').'</td><td colspan="2">';
	  print $objDepartament->select_departament($object->fk_departament,'fk_departament','',0,1);
	  print '</td></tr>';
	  
	  // Especiality
	  print '<tr><td class="fieldrequired">'.$langs->trans('Speciality').'</td><td colspan="2">';
	  select_speciality($object->speciality,'speciality','',1);
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

function htmlemail($id)
{
  global $object,$langs,$objCharge,$objDepartament;
  $html = '<form action="http://localhost/bcb/mant/jobs/fichereg.php" method="POST">';
  $html.= '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
  $html.= '<input type="hidden" name="action" value="updateemail">';
  $html.= '<input type="hidden" name="rowid" value="'.$id.'">';

  
  dol_htmloutput_mesg($mesg);

  $html.= '<table class="border" width="100%">';

  // ref numeracion automatica de la OT
  $html.= '<tr><td>'.$langs->trans('Jobsordernumber').'</td><td colspan="2">';
  $html.= '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="13" maxlength="15">';
  $html.= '</td></tr>';

  // solicitante
  $html.= '<tr><td>'.$langs->trans('Solicitante').'</td><td colspan="2">';
  $html.= select_adherent($user->fk_member,'fk_member','',0,1);
  $html.= '</td></tr>';

  // charge
  $html.= '<tr><td>'.$langs->trans('Charge').'</td><td colspan="2">';
  $html.= $objCharge->select_charge($object->fk_charge,'fk_charge','',0,1);
  $html.= '</td></tr>';

  // departament
  $html.= '<tr><td>'.$langs->trans('Departament').'</td><td colspan="2">';
  $html.= $objDepartament->select_departament($object->fk_departament,'fk_departament','',0,1);
  $html.= '</td></tr>';

  // Especiality
  $html.= '<tr><td>'.$langs->trans('Speciality').'</td><td colspan="2">';
  $html.= select_speciality($object->speciality,'speciality','',1);
  $html.= '</td></tr>';

  $html.= '</table>';

  $html.= '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';

   $html.= '</form>';
  return $html;
}

?>
