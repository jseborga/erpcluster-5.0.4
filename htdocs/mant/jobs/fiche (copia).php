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

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobs.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobscontact.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobsorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/jobs/class/mjobsuser.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/property/class/mproperty.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/property/class/mlocation.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/charge/class/pcharge.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/departament/class/pdepartament.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/equipment/class/mequipment.class.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/mant/lib/mant.lib.php';
require_once DOL_DOCUMENT_ROOT.'/mant/lib/adherent.lib.php';
require_once DOL_DOCUMENT_ROOT.'/mant/lib/societe.lib.php';
require_once DOL_DOCUMENT_ROOT.'/mant/lib/user.lib.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';


//require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$langs->load("companies");
$langs->load("commercial");
$langs->load("bills");
$langs->load("banks");
$langs->load("users");
$langs->load("other");
$langs->load("mant");

$action=GETPOST('action');

$id        = GETPOST("id");

if (! empty($user->societe_id)) $socid=$user->societe_id;

// $sortfield = GETPOST("sortfield");
// $sortorder = GETPOST("sortorder");

// if (! $sortfield) $sortfield="p.period_month";
// if (! $sortorder) $sortorder="DESC";

$mesg = '';

$object      = new Mjobs($db);
$objProperty = new Mproperty($db);
$objLocation = new Mlocation($db);
$objSoc      = new Societe($db);
$objEquipment= new Mequipment($db);

/*
 * Actions
 */

// Add
if ($action == 'add' && $user->rights->mant->jobs->crear)
  {
    $error=0;
    $date_create  = dol_mktime(12, 0, 0, date('m'),  date('d'),  date('Y'));

    $object->address_ip     = $_SERVER['REMOTE_ADDR'];
    $object->ref            = '(PROV)';
    $object->fk_member      = GETPOST("fk_member");
    $object->entity         = $conf->entity;
    $object->fk_charge      = GETPOST("fk_charge");
    $object->fk_departament = GETPOST("fk_departament");
    $object->internal       = GETPOST("internal");
    $object->fk_property    = GETPOST("fk_property");
    $object->fk_location    = GETPOST("fk_location");
    $object->speciality     = GETPOST("speciality");
    $object->date_create    = $date_create;
    $object->statut         = 0;
    //buscamos el correo del usuario seleccionado
    $objAdherent = new Adherent($db);
    $objAdherent->fetch($object->fk_member);
    if ($objAdherent->id == $object->fk_member)
      $object->email = $objAdherent->email;
    else
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans('Error, user is required').'</div>';
      }
    if (empty($object->fk_property))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans('Error, property is required').'</div>';
      }
    if (empty($object->fk_location))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans('Error, location is required').'</div>';
      }
    if (empty($object->speciality))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans('Error, speciality is required').'</div>';
      }

    if (empty($error)) 
      {
	$id = $object->create($user);
	if ($id > 0)
	  {
	    header("Location: fiche.php?id=".$id);
	    exit;
	  }
	$action = 'create';
	$mesg='<div class="error">'.$object->error.'</div>';
      }
    else
      {
	$action="create";   // Force retour sur page creation
      }
  }



// updateemail
if ($action == 'updatesocid' && $user->rights->mant->jobs->assignjobs)
  {
    $object->fetch($id);
    $object->fk_soc = GETPOST("fk_soc");
    if ($object->fk_soc)
      {
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
		header("Location: fiche.php?id=".$id.'&action=asignjobsdet');
		exit;
	      }
	    else
	      {
		header("Location: fiche.php?id=".$id);
		exit;
	      }
	  }
	$action = 'asignjobs';
	$mesg='<div class="error">'.$object->error.'</div>';
      }
    else
      {
	$mesg='<div class="error">'.$langs->trans("Errorsocrequired").'</div>';
	$action="asignjobs";   // Force retour sur page creation
      }
  }

// updateemail
if ($action == 'updateemail')
  {
    $object->fetch($id);
    $object->address_ip     = $_SERVER['REMOTE_ADDR'];
    $object->fk_member      = GETPOST("fk_member");
    $object->fk_charge      = GETPOST("fk_charge");
    $object->fk_departament = GETPOST("fk_departament");
    $object->internal       = GETPOST("internal");
    $object->fk_property    = GETPOST("fk_property");
    $object->fk_location = GETPOST("fk_location");
    $object->speciality  = GETPOST("speciality");
    if ($object->internal && $object->speciality)
      {
	$id = $object->update($user);
	if ($id > 0)
	  {
	    header("Location: fiche.php?id=".$id);
	    exit;
	  }
	$action = 'create';
	$mesg='<div class="error">'.$object->error.'</div>';
      }
    else
      {
	$mesg='<div class="error">'.$langs->trans("Errorinternalrequired").'</div>';
	$action="create";   // Force retour sur page creation
      }
  }


// upjobs
if ($action == 'upjobs' && $user->rights->mant->jobs->upjobs)
  {
    $actionant = GETPOST('actionant');
    $id = GETPOST('id');
    $object->fetch(GETPOST('id'));
    $statut = $object->statut;
    $date_ini = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));    
    $object->speciality_job = GETPOST("speciality_job");
    $object->date_ini    = $date_ini;
    $object->description = GETPOST('description');
    $object->fk_equipment = GETPOST('fk_equipment','int',2);
    //$object->statut      = 2;
    if ($object->date_ini && $object->speciality_job && $statut == 1) 
      {
	$result = $object->update($user);
	if ($result > 0)
	  {
	    if (!empty($actionant))
	      {
		header("Location: fiche.php?id=".$id.'&action='.$actionant);
		exit;
	      }
	    else
	      {
		header("Location: fiche.php?id=".$id.'&action=editjobs');
		exit;
	      }
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

// upworks
if ($action == 'upwork' && $user->rights->mant->jobs->regjobs)
  {
    $id = GETPOST('id');
    $object->fetch(GETPOST('id'));
    $statut   = $object->statut;
    $date_fin = dol_mktime(12, 0, 0, GETPOST('df_month'),GETPOST('df_day'),GETPOST('df_year'));    
    $object->date_fin        = $date_fin;
    $object->description_job = GETPOST('description_job');
    $object->typemant        = GETPOST('typemant');
    $object->fk_equipment    = GETPOST('fk_equipment');

    if (GETPOST('deletephotoini')) $object->image_ini = '';
    else if (! empty($_FILES['photoini']['name'])) $object->image_ini = dol_sanitizeFileName($_FILES['photoini']['name']);

    if (GETPOST('deletephotofin')) $object->image_fin = '';
    else if (! empty($_FILES['photofin']['name'])) $object->image_fin = dol_sanitizeFileName($_FILES['photofin']['name']);

    // Logo/Photo save
    $dir     = $conf->mant->multidir_output[$object->entity]."/".$object->id."/images";
    $file_OKini = is_uploaded_file($_FILES['photoini']['tmp_name']);
    $file_OKfin = is_uploaded_file($_FILES['photofin']['tmp_name']);

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
	  case 1: //uploaded file exceeds the upload_max_filesize directive in php.ini
	  case 2: //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
	    $errors[] = "ErrorFileSizeTooLarge";
	    break;
	  case 3: //uploaded file was only partially uploaded
	    $errors[] = "ErrorFilePartiallyUploaded";
	    break;
	  }
      }
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
	  case 1: //uploaded file exceeds the upload_max_filesize directive in php.ini
	  case 2: //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
	    $errors[] = "ErrorFileSizeTooLarge";
	    break;
	  case 3: //uploaded file was only partially uploaded
	    $errors[] = "ErrorFilePartiallyUploaded";
	    break;
	  }
      }

                // Gestion du logo de la société

    //$object->statut      = 2;
    if ($object->date_fin && $object->description_job && $object->typemant && $statut == 2 && count($errors) <= 0) 
      {
	$result = $object->update($user);
	if ($result > 0)
	  {
	    header("Location: fiche.php?id=".$id.'&action=editregjobs');
	    exit;
	  }
	$action = 'editregjobs';
	$mesg='<div class="error">'.$object->error.'</div>';
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
	$action="editregjobs";   // Force retour sur page creation
      }
  }

// Add
if ($action == 'closework' && $user->rights->mant->jobs->regjobs)
  {
    $id = GETPOST('id');
    $object->fetch(GETPOST('id'));
    $statut = $object->statut;
    $object->statut      = 3;
    if ($object->date_fin && $object->description_job && $statut == 2 &&
	!empty($object->image_ini) && !empty($object->image_fin)) 
      {
	$result = $object->update($user);
	if ($result > 0)
	  {
	    header("Location: fiche.php?id=".$id);
	    exit;
	  }
	$action = 'editregjobs';
	$mesg='<div class="error">'.$object->error.'</div>';
      }
    else 
      {
	$mesg='<div class="error">'.$langs->trans("Errorlackinformation").'</div>';
	$action="editregjobs";   // Force retour sur page creation
      }
  }

// startjobs
if ($action == 'startjobs' && ($user->rights->mant->jobs->upjobs || $user->rights->mant->jobs->assignjobs))
  {
    $id = GETPOST('id');
    $object->fetch(GETPOST('id'));
    $statut = $object->statut;
    $object->statut      = 2;
    if ($object->date_ini && $object->speciality_job && $statut == 1) 
      {
	$result = $object->update($user);
	if ($result > 0)
	  {
	    header("Location: fiche.php?id=".$id);
	    exit;
	  }
	$action = 'editjobs';
	$mesg='<div class="error">'.$object->error.'</div>';
      }
    else 
      {
	$mesg='<div class="error">'.$langs->trans("Errorlackinformation").'</div>';
	$action="editjobs";   // Force retour sur page creation
      }
  }

// Addcontact
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

// Addcontact
if ($action == 'updateuserdet' && $user->rights->mant->jobs->assignjobs)
  {
    $id = GETPOST('id');
    $object->fetch(GETPOST('id'));
    $statut = $object->statut;

    $objJobsuser = new Mjobsuser($db);
    $objJobsuser->fk_user = GETPOST("fk_user");
    $objJobsuser->statut    = 1;
    $objJobsuser->fk_jobs = $id;
    $objJobsuser->detail    = GETPOST('detail');
    $objJobsuser->tms = date('YmdHis');
    if ($objJobsuser->fk_user && $statut == 1) 
      {
	$result = $objJobsuser->create($user);
	if ($result > 0)
	  {
	    header("Location: fiche.php?id=".$id.'&action=asignjobsdet');
	    exit;
	  }
	$action = 'asignjobsdet';
	$mesg='<div class="error">'.$object->error.'</div>';
      }
    else 
      {
	$mesg='<div class="error">'.$langs->trans("Erroruserrequired").'</div>';
	$action="asignjobsdet";   // Force retour sur page creation
      }
  }

// Addorder
if ($action == 'addorder' && $user->rights->mant->jobs->regjobs)
  {
    $date_order = dol_mktime(12, 0, 0, GETPOST('do_month'),GETPOST('do_day'),GETPOST('do_year'));    

    $id = GETPOST('id');
    $object->fetch(GETPOST('id'));
    $statut = $object->statut;

    $objJobsorder = new Mjobsorder($db);
    $objJobsorder->fk_jobs = $id;
    $objJobsorder->order_number = GETPOST('order_number');
    $objJobsorder->date_order = $date_order;
    $objJobsorder->description = GETPOST('description');

    $description = GETPOST('description');	
    $order_number = GETPOST('order_number');

    $objJobsorder->statut      = 1;
    $objJobsorder->tms = date('YmdHis');
    if ($objJobsorder->order_number && $statut == 2) 
      {
	$result = $objJobsorder->create($user);
	if ($result > 0)
	  {
	    header("Location: fiche.php?id=".$id.'&action=editregjobs');
	    exit;
	  }
	$action = 'editregjobs';
	$mesg='<div class="error">'.$object->error.'</div>';
      }
    else 
      {
	$mesg='<div class="error">'.$langs->trans("Errororderrequired").'</div>';
	$action="editregjobs";   // Force retour sur page creation
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

  print "\n".'<script type="text/javascript" language="javascript">';
  print '$(document).ready(function () {
              $("#selectfk_property").change(function() {
                document.form_index.action.value="createedit";
                document.form_index.submit();
              });
          });';
  print '</script>'."\n";
  
  print '<form action="fiche.php" method="post" name="form_index">';
  print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
  print '<input type="hidden" name="action" value="add">';
  print '<input type="hidden" name="rowid" value="15">';
  
  dol_htmloutput_mesg($mesg);

  print '<table class="border" width="100%">';

  // ref numeracion automatica de la OT
  print '<tr><td class="fieldrequired" width="20%">'.$langs->trans('Jobsordernumber').'</td><td colspan="2">';
  print '<input id="ref" type="text" value="'.$object->ref.'" name="ref" size="13" maxlength="15" disabled="disabled">';
  print '</td></tr>';

  // solicitante
  print '<tr><td class="fieldrequired">'.$langs->trans('Solicitante').'</td><td colspan="2">';
  print select_adherent((empty($object->fk_member)?$user->fk_member:$object->fk_member),'fk_member','',0,1);
  print '</td></tr>';

  // ref numeracion automatica de la OT
  print '<tr><td  class="fieldrequired" width="20%">'.$langs->trans('Internal').'</td><td colspan="2">';
  print '<input id="internal" type="text" value="'.$object->internal.'" name="internal" size="5">';
  print '</td></tr>';

  // charge
  // print '<tr><td class="fieldrequired">'.$langs->trans('Charge').'</td><td colspan="2">';
  // print $objCharge->select_charge($object->fk_charge,'fk_charge','',0,1);
  // print '</td></tr>';

  // departament
  // print '<tr><td class="fieldrequired">'.$langs->trans('Departament').'</td><td colspan="2">';
  // print $objDepartament->select_departament($object->fk_departament,'fk_departament','',0,1);
  // print '</td></tr>';

  // property
  print '<tr><td class="fieldrequired">'.$langs->trans('Property').'</td><td colspan="2">';
  print $objProperty->select_property($object->fk_property,'fk_property','',40,1);
  print '</td></tr>';
  
  // location
  print '<tr><td class="fieldrequired">'.$langs->trans('Location').'</td><td colspan="2">';
  print $objLocation->select_location($object->fk_location,'fk_location','',40,1,$object->fk_property);
  print '</td></tr>';	  

  // Especiality
  print '<tr><td class="fieldrequired">'.$langs->trans('Speciality').'</td><td colspan="2">';
  print select_speciality($object->speciality,'speciality','',1);
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
	  if ($action == 'openwork')
	    {
	      $object->fetch(GETPOST('id'));
	      //cambiando a validado
	      $object->statut = 2;
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
	  
	  //email
	  print '<tr><td width="20%">'.$langs->trans('Email').'</td><td colspan="2">';
	  print $object->email;
	  print '</td></tr>';

	  // solicitante
	  $objAdherent->fetch($object->fk_member);
	  print '<tr><td >'.$langs->trans('Solicitante').'</td><td colspan="2">';
	  if ($objAdherent->id == $object->fk_member)
	    print $objAdherent->lastname.' '.$objAdherent->firstname;
	  else
	    print '&nbsp;';
	  print '</td></tr>';

	  //internal
	  print '<tr><td width="20%">'.$langs->trans('Internal').'</td><td colspan="2">';
	  print $object->internal;
	  print '</td></tr>';
	  
	  // charge
	  // $objCharge->fetch($object->fk_charge);
	  // print '<tr><td >'.$langs->trans('Charge').'</td><td colspan="2">';
	  // print $objCharge->ref;
	  // print '</td></tr>';
	  
	  // departament
	  // $objDepartament->fetch($object->fk_departament);
	  // print '<tr><td >'.$langs->trans('Departament').'</td><td colspan="2">';
	  // print $objDepartament->ref;
	  // print '</td></tr>';
	  
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

	  // Especiality
	  print '<tr><td >'.$langs->trans('Speciality').'</td><td colspan="2">';
	  select_speciality($object->speciality,'speciality','',1,1);
	  print '</td></tr>';

	  // fk_soc
	  print '<tr><td >'.$langs->trans('Assigned').'</td><td colspan="2">';

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
	      print $objSoc->name;
	    }
	  else
	    {
	      if ($object->fk_soc == -2)
		print $langs->trans('Internalassignment');
	      else
		print '&nbsp;';
	    }
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
	      // assign jobs
	      if ($object->statut == 1 && $user->rights->mant->jobs->assignjobs)
		{
		  print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=asignjobs">'.$langs->trans('Assignwork').'</a>';
		}

	      // init work
	      if ($object->statut == 1 && $user->rights->mant->jobs->upjobs && $object->fk_soc != -1)
		{
		  print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editjobs">'.$langs->trans('Startwork').'</a>';
		}
	      // open jobs
	      if ($object->statut == 3 && $user->rights->mant->jobs->openwork)
		{
		  print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=openwork">'.$langs->trans('Openwork').'</a>';
		}

	    }	  

	  print '</div>';

	  //asignacion de trabajos a externo o interno
	  if ($object->statut == 1 && ($action == 'asignjobs' || $action == 'asignjobsdet'))
	    {	      
	      $lSelectSocid = false;

	      if (empty($object->fk_soc))
		$lSelectSocid = true;

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

	      print '<form action="fiche.php" method="POST" name="form_index">';
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
	      print select_societe($object->fk_soc,'fk_soc','',0,1,1);
	      print '</td>';
	      print '</tr>';
	      print '</table>';
	      if ($objSoc->fetch($object->fk_soc))
		{
		  $objTypent = fetch_typent($objSoc->typent_id);
		}
	      
	      if (($object->fk_soc == -2 && $action == 'asignjobsdet') || ($objTypent->id == $objSoc->typent_id && $objTypent->code == 'TE_BCB' ))
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
		      print '<td></td>';
		      print '</tr>';
		    }
		  print "</table>";
		}
	      print '<center><br><input type="submit" class="button" value="'.$langs->trans("Saveuser").'"></center>';
	      print '</form>';

	      print '</td><td width="50%" valign="top">';
	      if ($object->fk_soc == -2 || 
		  ($objTypent->id == $objSoc->typent_id && 
		   $objTypent->code == 'TE_BCB' ))
		{
		  print '<form action="fiche.php" method="POST">';
		  print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		  print '<input type="hidden" name="action" value="upjobs">';
		  print '<input type="hidden" name="actionant" value="asignjobsdet">';

		  print '<input type="hidden" name="id" value="'.$object->id.'">';
		  
		  print '<table class="border" width="99%">';
		  
		  // Especiality
		  print '<tr><td >'.$langs->trans('Speciality').'</td><td colspan="2">';
		  print select_speciality((empty($object->speciality_job)?$object->speciality:$object->speciality_job),'speciality_job','',1);
		  print '</td></tr>';
		  //descripcion
		  print '<tr><td >'.$langs->trans('Description').'</td><td colspan="2">';
		  print '<textarea name="description" cols="40" rows="5">'.$object->description.'</textarea>';
		  print '</td></tr>';
		  
		  // dateini
		  print '<tr><td class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="2">';
		  $form->select_date($object->date_ini,'di_','','','',"crearperiod",1,1);
		  print '</td></tr>';
		  
		  // // datefin
		  // print '<tr><td>'.$langs->trans('Datefin').'</td><td colspan="2">';
		  // $form->select_date((empty($object->date_fin)?'':$object->date_fin),'df_','','',1,"fiche_index",1,1);
		  // print '</td></tr>';
		  
		  
		  print "</table>";

		  print '<center><br><input type="submit" class="button" value="'.$langs->trans("Saveworktobeperformed").'"></center>';

		  print '</form>';
		}
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


	  //registro de atencion
	  if ($object->statut == 1 && $action == 'editjobs')
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

	      print '<table class="border" width="100%" style="vertical-align:text-top;">';
	      print '<tr><td width="50% "">';
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
	      print '<form action="fiche.php" method="POST" name="form_index">';
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

	      print '<center><br><input type="submit" class="button" value="'.$langs->trans("Savecontact").'"></center>';

	      print '</form>';
	      print '</td><td width="50%" valign="top">';
	      print '<form action="fiche.php" method="POST">';
	      print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	      print '<input type="hidden" name="action" value="upjobs">';
	      print '<input type="hidden" name="id" value="'.$object->id.'">';
	      
	      print '<table class="border" width="99%">';
	      
	      // Especiality
	      print '<tr><td >'.$langs->trans('Speciality').'</td><td colspan="2">';
	      print select_speciality((empty($object->speciality_job)?$object->speciality:$object->speciality_job),'speciality_job','',1);
	      print '</td></tr>';

	      // equipment
	      print '<tr><td >'.$langs->trans('Equipment').'</td><td colspan="2">';
	      print $objEquipment->select_equipment($object->fk_equipment,'fk_equipment','',40);
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
	      // print '<tr><td>'.$langs->trans('Datefin').'</td><td colspan="2">';
	      // $form->select_date((empty($object->date_fin)?'':$object->date_fin),'df_','','',1,"fiche_index",1,1);
	      // print '</td></tr>';
	      
	      
	      print "</table>";

	      print '<center><br><input type="submit" class="button" value="'.$langs->trans("Saveworktobeperformed").'"></center>';

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

	  if ($object->statut == 2 || $object->statut == 3)
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
	      
	      if ($action != 'editregjobs')
		{
		  // typemant
		  print '<tr><td width="20%">'.$langs->trans('Typeofwork').'</td><td colspan="2">';
		  select_typemant($object->typemant,'typemant','',1,1);
		  print '</td></tr>';

		  // imagen ini

		  print '<tr class="hideonsmartphone">';
		  print '<td>'.$langs->trans("Photobeforestartingwork").'</td>';
		  print '<td colspan="2">';
		  if ($object->image_ini) print $object->showphoto('ini',$object,100);
		  print '</td>';
		  print '</tr>';
		  
		  // imagen fin
		  print '<tr class="hideonsmartphone">';
		  print '<td>'.$langs->trans("Phototocompletethework").'</td>';
		  print '<td colspan="2">';
		  if ($object->image_fin) print $object->showphoto('fin',$object,100);
		  print '</td>';
		  print '</tr>';
		}
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
	      if ($action == '')
		{
		  // assign work
		  if ($object->statut == 2 && $user->rights->mant->jobs->regjobs)
		    {
		      //buscamos los tecnicos asignados
		      //autorizacion solo para el tecnico asignado o el administrador externo
		      $lRunjobs = false;
		      if ($object->typent == 'TE_BCB')
			{
			  
			  if (!empty($user->array_options['options_fk_tercero']) && 
			      $object->typent_id == $user->array_options['options_fk_tercero'])
			    $lRunjobs = true;
			  
			}
		      else
			{
			  $objJobscon = new Mjobscontact($db);
			  $aContact = $objJobscon->list_contact($id);
			  foreach((array) $aContact AS $j => $objContact)
			    {
			      if ($objContact->fk_contact == $user->contact_id)
				$lRunjobs = true;
			    }
			}
		      if ($lRunjobs)
			print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editregjobs">'.$langs->trans('Runjobs').'</a>';
		    }
		  
		}

	      print '</div>';	      
	      
	    }

	  if ($object->statut == 2 && $action == 'editregjobs')
	    {
      	      
	      dol_fiche_head($head, 'card', $langs->trans("Workcarriedout"), 0, 'mant');
	      
	      print '<table class="border" width="100%" style="vertical-align:text-top;">';
	      print '<tr><td width="50% "">';
	      print '<form action="fiche.php" method="POST" name="form_index">';
	      print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	      print '<input type="hidden" name="action" value="addorder">';
	      print '<input type="hidden" name="id" value="'.$object->id.'">';

	      print '<table class="border" width="100%">';
	      print '<tr class="liste_titre">';
	      print_liste_field_titre($langs->trans("Ordernumber"),"", "","","","");
	      print_liste_field_titre($langs->trans("Date"),"", "","","","");
	      print_liste_field_titre($langs->trans("Description"),"", "",'','','');
	      print "</tr>\n";
	      //registro nuevo
	      print '<tr>';
	      print '<td>';
	      print '<input id="order_number" type="text" value="'.$order_number.'" name="order_number" size="17" maxlength="15">';
	      print '</td>';
	      print '<td>';
	      $form->select_date($date_order,'do_','','','',"regjobs",1,1);
	      print '</td>';
	      print '<td>';
	      print '<textarea name="description" cols="40" rows="5">'.$description.'</textarea>';
	      print '</td>';
	      print '</tr>';
	      $objJobsOrder = new Mjobsorder($db);
	      $aOrder = $objJobsOrder->list_order($object->id);
	      foreach ((array) $aOrder AS $j => $objOrder)
		{
		  print '<tr>';
		  print '<td>'.$objOrder->order_number.'</td>';
		  print '<td>'.$objOrder->date_order.'</td>';
		  print '<td>'.$objOrder->description.'</td>';
		  print '</tr>';
		}
	      print "</table>";

	      print '<center><br><input type="submit" class="button" value="'.$langs->trans("Saveorder").'"></center>';

	      print '</form>';
	      print '</td>';
	      print '<td width="50%" valign="top">';
	      print '<form  enctype="multipart/form-data" action="fiche.php" method="POST">';
	      print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	      print '<input type="hidden" name="action" value="upwork">';
	      print '<input type="hidden" name="id" value="'.$object->id.'">';
	      
	      print '<table class="border" width="99%">';
	      
	      // typemant
	      print '<tr><td class="fieldrequired">'.$langs->trans('Typeofwork').'</td><td colspan="2">';
	      print select_typemant($object->typemant,'typemant','',1);
	      print '</td></tr>';

	      // equipment
	      print '<tr><td >'.$langs->trans('Equipment').'</td><td colspan="2">';
	      print $objEquipment->select_equipment($object->fk_equipment,'fk_equipment','',40);
	      print '</td></tr>';

	      //descripcion job
	      print '<tr><td class="fieldrequired">'.$langs->trans('Description').'</td><td colspan="2">';
	      print '<textarea name="description_job" cols="40" rows="5">'.$object->description_job.'</textarea>';
	      print '</td></tr>';

	      // dateini
	      print '<tr><td>'.$langs->trans('Dateini').'</td><td colspan="2">';
	      print dol_print_date($object->date_ini);
	      print '</td></tr>';

	      // imagen ini
	      print '<tr class="hideonsmartphone">';
	      print '<td>'.$langs->trans("Photobeforestartingwork").'</td>';
	      print '<td colspan="2">';
	      if ($object->image_ini) print $object->showphoto('ini',$object,50);
	      $caneditfield=1;
	      if ($caneditfield)
		{
		  if ($object->image_ini) print "<br>\n";
		  print '<table class="nobordernopadding">';
		  if ($object->image_ini) print '<tr><td><input type="checkbox" class="flat" name="deletephotoini" id="photodeleteini"> '.$langs->trans("Delete").'<br><br></td></tr>';
		  //print '<tr><td>'.$langs->trans("PhotoFile").'</td></tr>';
		  print '<tr><td><input type="file" class="flat" name="photoini" id="photoiniinput"></td></tr>';
		  print '</table>';
		}
	      print '</td>';
	      print '</tr>';

	      // imagen fin
	      print '<tr class="hideonsmartphone">';
	      print '<td>'.$langs->trans("Phototocompletethework").'</td>';
	      print '<td colspan="2">';
	      if ($object->image_fin) print $object->showphoto('fin',$object,50);
	      $caneditfield=1;
	      if ($caneditfield)
		{
		  if ($object->image_fin) print "<br>\n";
		  print '<table class="nobordernopadding">';
		  if ($object->image_fin) print '<tr><td><input type="checkbox" class="flat" name="deletephotofin" id="photodeletefin"> '.$langs->trans("Delete").'<br><br></td></tr>';
		  //print '<tr><td>'.$langs->trans("PhotoFile").'</td></tr>';
		  print '<tr><td><input type="file" class="flat" name="photofin" id="photofininput"></td></tr>';
		  print '</table>';
		}
	      print '</td>';
	      print '</tr>';
	      
	      // datefin
	      print '<tr><td class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="2">';
	      $form->select_date((empty($object->date_fin)?'':$object->date_fin),'df_','','',1,"fiche_index",1,1);
	      print '</td></tr>';
	      
	      
	      print "</table>";

	      print '<center><br><input type="submit" class="button" value="'.$langs->trans("Savework").'"></center>';

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
	  /* ****************************************** */
	  /*                                            */
	  /* Barre d'action                             */
	  /*                                            */
	  /* ****************************************** */
	  
	  print '</div>';
	  print "<div class=\"tabsAction\">\n";
	  
	  if ($action == 'editjobs')
	    {
	      if ($numberContact > 0 && !empty($object->date_ini))
		{
		  if ($user->rights->mant->jobs->upjobs && $object->statut == 1)
		    {
		      print '<form action="fiche.php" method="POST">';
		      print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		      print '<input type="hidden" name="action" value="startjobs">';
		      print '<input type="hidden" name="id" value="'.$object->id.'">';
		      
		      print '<center><br><input type="submit" class="button" value="'.$langs->trans("Beginjob").'"></center>';
		      print '</form>';
		    }
		}
	    }
	  print '</div>';

	  print "<div class=\"tabsAction\">\n";
	  
	  if ($action == 'editregjobs')
	    {
	      if (!empty($object->image_ini) && !empty($object->image_fin) 
		  && !empty($object->date_fin) && !empty($object->typemant))
		{
		  if ($user->rights->mant->jobs->regjobs && $object->statut == 2)
		    {
		      print '<form action="fiche.php" method="POST">';
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
	      if ($numberuser > 0 && !empty($object->date_ini))
		{
		  if ($user->rights->mant->jobs->assignjobs && $object->statut == 1)
		    {
		      print '<form action="fiche.php" method="POST">';
		      print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		      print '<input type="hidden" name="action" value="startjobs">';
		      print '<input type="hidden" name="id" value="'.$object->id.'">';
		      
		      print '<center><br><input type="submit" class="button" value="'.$langs->trans("Beginjob").'"></center>';
		      print '</form>';
		    }
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
	  print '<input type="hidden" name="action" value="updateemail">';
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
	  
	  // ref numeracion automatica de la OT
	  print '<tr><td  class="fieldrequired" width="20%">'.$langs->trans('Internal').'</td><td colspan="2">';
	  print '<input id="internal" type="text" value="'.$object->internal.'" name="internal" size="5">';
	  print '</td></tr>';

	  // charge
	  print '<tr><td class="fieldrequired">'.$langs->trans('Charge').'</td><td colspan="2">';
	  print $objCharge->select_charge($object->fk_charge,'fk_charge','',0,1);
	  print '</td></tr>';
	  
	  // departament
	  print '<tr><td class="fieldrequired">'.$langs->trans('Departament').'</td><td colspan="2">';
	  print $objDepartament->select_departament($object->fk_departament,'fk_departament','',0,1);
	  print '</td></tr>';
	  
	  // property
	  print '<tr><td class="fieldrequired">'.$langs->trans('Property').'</td><td colspan="2">';
	  print $objProperty->select_property($object->fk_property,'fk_property','',40,1);
	  print '</td></tr>';

	  // location
	  print '<tr><td class="fieldrequired">'.$langs->trans('Location').'</td><td colspan="2">';
	  print $objLocation->select_location($object->fk_location,'fk_location','',40,1,$object->fk_property);
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
?>
