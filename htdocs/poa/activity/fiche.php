<?php
/* Copyright (C) 2014-2015 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *	\file       htdocs/poa/activity/fiche.php
 *	\ingroup    Activities
 *	\brief      Page fiche POA activitie
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';


require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/activity/class/poaactivity.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/activity/class/poaactivitydet.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/activity/class/poaactivityworkflow.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/activity/class/poaactivitychecklist.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/activity/class/poaactivitydoc.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/process/class/poaprocess.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/pac/class/poapac.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/poa/class/poapoa.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poaprev.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/structure/class/poastructure.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/area/class/poaarea.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/area/class/poaareauser.class.php';
// require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidapre.class.php';
// require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidapredet.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/flowmodels/class/cflowmodels.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/workflow/class/poaworkflow.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/workflow/class/poaworkflowdet.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/document/class/poadocuments.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/lib/poa.lib.php';
require_once DOL_DOCUMENT_ROOT.'/poa/lib/doc.lib.php';

require_once DOL_DOCUMENT_ROOT.'/poa/class/poapreemail.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$langs->load("poa@poa");

$action=GETPOST('action');

$id        = GETPOST("id"); //id actividad
$idr       = GETPOST("idr"); //id actividad det
$idp       = GETPOST("idp");
$fk_poa    = GETPOST("fk_poa"); //id poa
$idpp      = GETPOST("idpp");
$idppp     = GETPOST("idppp"); //id insumo partida_pre_det
$tabs      = GETPOST('tabs');
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");
$gestion = $_SESSION['gestion'];
$mesg = '';
if (empty($_SESSION['tabs']))
  $_SESSION['tabs']='crono';
if (isset($_GET['tabs']) || isset($_POST['tabs']))
  $_SESSION['tabs']=GETPOST('tabs');

$tabs = $_SESSION['tabs'];

$object  = new Poaactivity($db);
$objectd = new Poaactivitydet($db);
$objectw = new Poaactivityworkflow($db);
$objectc = new Poaactivitychecklist($db);
$objdoca = new Poaactivitydoc($db);
$objuser = new User($db);
$objpac  = new Poapac($db);
$objpoa  = new Poapoa($db);
$objpre  = new Poaprev($db);
$objproc = new Poaprocess($db);
$objarea = new Poaarea($db);
$objareauser = new Poaareauser($db);
// $objpre = new Poapartidapre($db);
// $objpredet = new Poapartidapredet($db);
$objstr  = new Poastructure($db);
$objdoc  = new Poadocuments($db);
$aExcludeArea = array();
$idFather = 0;

//areas a las que pertenece el usuario
if (!$user->admin)
  {
    $aArea = $objareauser->getuserarea($user->id);
    foreach((array) $aArea AS $idArea => $objAr)
      {
	//$idFather = $objarea->getfatherarea($idArea);
	$idFather = $idArea;
      }

  }
/*
 * Actions
 */
//uppdf
if ($action == 'updoc')
{
    if ($object->fetch($_POST["id"])>0)
    {
	$db->begin();
	// Logo/Photo save
	$dir     = $conf->poa->dir_output.'/activity/doc';
	$file_OKfin = is_uploaded_file($_FILES['docpdf']['tmp_name']);
	if ($file_OKfin)
    {
        if (GETPOST('deletedocfin'))
        {
            $fileimg=$dir.'/'.$object->image_fin;
            $dirthumbs=$dir.'/thumbs';
            dol_delete_file($fileimg);
            dol_delete_dir_recursive($dirthumbs);
        }

	    if (doc_format_supported_activity($_FILES['docpdf']['name']) > 0)
        {
            dol_mkdir($dir);
            if (@is_dir($dir))
            {
                //agregamos el archivo
                $objdoca->fk_activity = $object->id;
                $objdoca->name_doc = dol_sanitizeFileName($_FILES['docpdf']['name']);
                $objdoca->detail = GETPOST('detail','alpha');
                $objdoca->fk_user_create = $user->id;
                $objdoca->date_create = dol_now();
                $objdoca->tms = dol_now();
                $objdoca->statut = 1;
                $iddoc = $objdoca->create($user);
                if (! $iddoc >0) $error++;
                $aFile = explode('.',dol_sanitizeFileName($_FILES['docpdf']['name']));
                $file = '';
                foreach ((array) $aFile AS $j => $val)
                {
                    if (empty($file)) $file = $iddoc;
                    else $file.= '.'.$val;
                }
                //actualizamos
                $objdocatmp = new Poaactivitydoc($db);
                $objdocatmp->fetch($iddoc);
                if ($objdocatmp->id == $iddoc)
                {
                    $objdocatmp->name_doc = $file;
                    $res = $objdocatmp->update($user);
                    if (!$res>0) $error++;
                }
                $newfile = $dir.'/'.$file;

                $result = dol_move_uploaded_file($_FILES['docpdf']['tmp_name'], $newfile, 1);
                if (! $result > 0)
                {
                    $error++;
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
            $error++;
            $errors[] = "ErrorBadImageFormat";
        }
    }
	else
    {
	    switch($_FILES['docpdf']['error'])
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
	if (empty($error)) $db->commit();
	else $db->rollback();
	$action = '';
    }
}

// Add
if ($action == 'add' && $user->rights->poa->act->crear)
  {
    $error = 0;
    //obtenemos el ultimo numero
    $nro_activity = $object->fetch_next_nro($gestion);

    $object->initAsSpecimen();
    $date_activity = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));

    $object->gestion         = GETPOST('gestion');
    $object->fk_prev         = GETPOST('fk_prev')+0;
    $object->fk_father       = GETPOST('fk_father')+0;
    if (!empty($object->fk_father))
      {
	$objnew = new Poaprev($db);
	if ($objnew->fetch('',$object->fk_father)>0)
	  if ($objnew->nro_preventive == $object->fk_father)
	    $object->fk_father = $objnew->id;
	  else
	    $object->fk_father = 0;
	else
	  $object->fk_father = 0;
      }
    else
      $object->fk_father = 0;
    //preventive gestion pasada
    $nro_preventive_ant = GETPOST('nro_preventive_ant');
    $gestion_ant = GETPOST('gestion_ant');
    if (!empty($nro_preventive_ant) && !empty($gestion_ant))
      {
	$objnew = new Poaprev($db);
	if ($objnew->fetch('',$nro_preventive_ant,$gestion_ant)>0)
	  if ($objnew->nro_preventive == $nro_preventive_ant && $objnew->gestion == $gestion_ant)
	    $object->fk_prev_ant = $objnew->id;
	  else
	    $object->fk_prev_ant = 0;
	else
	  $object->fk_prev_ant = 0;
      }
    else
      $object->fk_prev_ant = 0;

    $object->fk_poa          = GETPOST('fk_poa');
    $object->fk_pac          = GETPOST('fk_pac')+0;
    $object->fk_area         = GETPOST('fk_area');
    if ($user->admin)
      $object->nro_activity  = GETPOST('nro_activity');
    else
      $object->nro_activity  = $nro_activity;
    $object->priority        = GETPOST('priority');
    $object->code_requirement= GETPOST('code_requirement');
    $object->date_activity   = $date_activity;
    $object->fk_user_create  = GETPOST('fk_user_create');
    $object->label           = GETPOST('label');
    $object->partida         = GETPOST('partida');
    $object->amount          = GETPOST('amount');
    $object->pseudonym       = GETPOST('pseudonym');

    if (empty($object->label))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errorlabelisrequired").'</div>';
      }
    $object->date_create = dol_now();
    $object->tms = date('YmdHis');
    if ($object->fk_user_create <= 0)
      $object->fk_user_create = $user->id;
    $object->entity = $conf->entity;
    $object->statut     = 0;
    $object->active = 1;

    if (empty($object->nro_activity))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errornroactivityisrequired").'</div>';
      }
    if (empty($error))
      {
	$id = $object->create($user);
	if ($id > 0)
	  {
	    header("Location: ".$_SERVER['PHP_SELF']."?id=".$id.'&fk_poa='.$fk_poa.'&dol_hide_leftmenu=1');
	    exit;
	  }
	$action = 'create';
	$mesg='<div class="error">'.$object->error.'</div>';
      }
    else
      {
	if ($error)
	  $action="create";   // Force retour sur page creation
      }
  }

// Addpro
if ($action == 'addpro' && $user->rights->poa->act->adds)
  {
    $error = 0;
    if ($object->fetch($id)>0)
      {
	$date_procedure = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));

	$objectd->fk_activity = $id;
	$objectd->code_procedure = GETPOST('code_procedure');
	$objectd->date_procedure = $date_procedure;
	$objectd->date_create = dol_now();
	$objectd->fk_user_create  = $user->id;
	$objectd->tms     = date('YmdHis');
	$objectd->statut  = 1;

	if (empty($objectd->code_procedure))
	  {
	    $error++;
	    $mesg.='<div class="error">'.$langs->trans("Errorcodeprocedureisrequired").'</div>';
	  }
	if (empty($error))
	  {
	    $idp = $objectd->create($user);
	    if ($idp > 0)
	      {
		header("Location: ".$_SERVER['PHP_SELF']."?id=".$id.'&dol_hide_leftmenu=1');
		exit;
	      }
	    $action = '';
	    $mesg='<div class="error">'.$objectd->error.'</div>';
	  }
	else
	  $action="";   // Force retour sur page creation
      }
  }


// updatepro
if ($action == 'updatepro' && $user->rights->poa->act->mods)
  {
    $error = 0;
    if ($object->fetch($id)>0)
      {
	if ($objectd->fetch($idr)>0)
	  {
	    $date_procedure = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));

	    $objectd->code_procedure = GETPOST('code_procedure');
	    $objectd->date_procedure = $date_procedure;
	    $objectd->tms     = date('YmdHis');
	    $objectd->statut  = 1;

	    if (empty($objectd->code_procedure))
	      {
		$error++;
		$mesg.='<div class="error">'.$langs->trans("Errorcodeprocedureisrequired").'</div>';
	      }
	    if (empty($error))
	      {
		$res = $objectd->update($user);
		if ($res > 0)
		  {
		    header("Location: ".$_SERVER['PHP_SELF']."?id=".$id.'&dol_hide_leftmenu=1');
		    exit;
		  }
		$action = 'editpro';
		$mesg='<div class="error">'.$objectd->error.'</div>';
	      }
	  }
      }
    $action='editpro';
  }

// Addmon
if ($action == 'addmon' && $user->rights->poa->act->addm)
  {
    $error = 0;
    if ($object->fetch($id)>0)
      {
	$date_tracking = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
	if (!$user->admin)
	  $date_tracking = dol_now();
	$objectw->fk_activity = $id;
	$objectw->followup = GETPOST('followup');
	$objectw->followto = GETPOST('followto');
	$objectw->date_tracking = $date_tracking;
	$objectw->date_create = dol_now();
	$objectw->fk_user_create  = $user->id;
	$objectw->tms     = date('YmdHis');
	$objectw->statut  = 1;

	if (empty($objectw->followup))
	  {
	    $error++;
	    $mesg.='<div class="error">'.$langs->trans("Errorfollowupisrequired").'</div>';
	  }
	if (empty($error))
	  {
	    $res = $objectw->create($user);
	    if ($res > 0)
	      {
		header("Location: ".$_SERVER['PHP_SELF']."?id=".$id.'&dol_hide_leftmenu=1');
		exit;
	      }
	    $action = '';
	    $mesg='<div class="error">'.$objectw->error.'</div>';
	  }
	else
	  $action="";   // Force retour sur page creation
      }
  }

// updatemon
if ($action == 'updatemon' && $user->rights->poa->act->modm)
  {
    $error = 0;
    if ($object->fetch($id)>0)
      {
	if ($objectw->fetch($idr)>0)
	  {
	    $date_tracking = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
	    $objectw->followup = GETPOST('followup');
	    $objectw->followto = GETPOST('followto');
	    if ($user->admin)
	      $objectw->date_tracking = $date_tracking;
	    $objectw->tms     = date('YmdHis');
	    $objectw->statut  = 1;
	    if (empty($objectw->followup))
	      {
		$error++;
		$mesg.='<div class="error">'.$langs->trans("Errorfollowupisrequired").'</div>';
	      }
	    if (empty($error))
	      {
		$res = $objectw->update($user);
		if ($res > 0)
		  {
		    header("Location: ".$_SERVER['PHP_SELF']."?id=".$id.'&dol_hide_leftmenu=1');
		    exit;
		  }
		$action = '';
		$mesg='<div class="error">'.$objectw->error.'</div>';
	      }
	    else
	      $action="";   // Force retour sur page creation
	  }
      }
  }

// Adddoc
if ($action == 'adddoc' && $user->rights->poa->doc->crear && $_POST["cancel"] <> $langs->trans("Cancel"))
  {
    $error = 0;
    $objdoc->entity = $conf->entity;
    $objdoc->fk_type_con = GETPOST('fk_type_con');
    $objdoc->code = GETPOST('code');
    $objdoc->fk_user_create = $user->id;
    $objdoc->date_create = dol_now();
    $objdoc->tms = dol_now();
    $objdoc->active = 1;
    if (empty($objdoc->code))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errordocumentisrequired").'</div>';
      }
    if (empty($error))
      {
	$res = $objdoc->create($user);
	if ($res > 0)
	  {
	    $mesg.='<div class="ok">'.$langs->trans("Savedsuccesfully").'</div>';
	    $action = '';
	  }
	else
	  {
	    $mesg.='<div class="error">'.$objdoc->error.'</div>';
	    $action = 'createdoc';
	  }
      }
    else
      $action='createdoc';
  }

//addcheck
if ($action == 'addcheck' && $user->rights->poa->act->addc)
  {
    $error = 0;
    if ($object->fetch($id)>0)
      {
	$aChecklist = GETPOST('checklist');

	foreach ((array) $aChecklist AS $code => $value)
	  {
	    //buscamos el registro
	    if ($objectc->fetch_code($object->id,$code)>0)
	      {
		if ($objectc->fk_activity == $object->id &&
		    $objectc->code == $code)
		  {
		    //actualizamos
		    $objectc->checklist = $value;
		    $objectc->tms = dol_now();
		    $res = $objectc->update($user);
		    if ($res <= 0)
		      {
			$error++;
			$mesg.='<div class="error">'.$objectc->error.'</div>';
		      }
		  }
		else
		  {
		    //creamos el registro
		    $objectc->fk_activity = $object->id;
		    $objectc->code = $code;
		    $objectc->checklist = $value;
		    $objectc->fk_user_create = $user->id;
		    $objectc->date_create = dol_now();
		    $objectc->tms = dol_now();
		    $objectc->statut = 1;
		    $objectc->create($user);
		    if ($res <= 0)
		      {
			$error++;
			$mesg.='<div class="error">'.$objectc->error.'</div>';
		      }
		  }
	      }
	  }
      }
    else
      {
	$error++;
	$mesg.='<div class="error">'.$object->error.'</div>';
      }
    if (empty($error))
      $mesg.='<div class="ok">'.$langs->trans("Savedsuccesfully").'</div>';

    $action = '';
  }

// Delete prev
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->poa->act->del)
  {
  $error = 0;
  $object->fetch($_REQUEST["id"]);
  $db->begin();
  if (empty($error))
    {
      $result=$object->delete($user);
      if ($result > 0)
	{
	  $db->commit();
	  header("Location: ".DOL_URL_ROOT.'/poa/poa/liste.php?dol_hide_leftmenu=1&dol_hide_topmenu=1');
	  exit;
	}
      else
	{
	  $db->rollback();
	  $mesg='<div class="error">'.$object->error.'</div>';
	  $action='';
	}
    }
  else
    {
      $db->rollback();
      $mesg='<div class="error">'.$langs->trans('Error delete').'</div>';
      $action = '';
    }
 }

// Delete monitoreo
if ($action == 'confirm_delete_mon' && $_REQUEST["confirm"] == 'yes' && $user->rights->poa->act->delm)
  {
  $error = 0;
  $objectw->fetch($_REQUEST["idr"]);
  $db->begin();
  if (empty($error))
    {
      $result=$objectw->delete($user);
      if ($result > 0)
	{
	  $db->commit();
	  header("Location: ".DOL_URL_ROOT.'/poa/poa/liste.php?dol_hide_leftmenu=1');
	  exit;
	}
      else
	{
	  $db->rollback();
	  $mesg='<div class="error">'.$objectw->error.'</div>';
	  $action='';
	}
    }
  else
    {
      $db->rollback();
      $mesg='<div class="error">'.$langs->trans('Error delete').'</div>';
      $action = '';
    }
 }



// Modification entrepot
if ($action == 'update' && $_POST["cancel"] <> $langs->trans("Cancel"))
  {
    $error = 0;

    if ($object->fetch($_POST["id"]))
      {
	$date_preventive = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));

	$object->gestion         = GETPOST('gestion');
	$object->fk_poa          = GETPOST('fk_poa');
	//buscamos el poa
	$objpoa->fetch($object->fk_poa);
	if ($objpoa->id == $object->fk_poa)
	  {
	    $object->partida = $objpoa->partida;
	  }
	$object->fk_pac          = GETPOST('fk_pac');
	//buscamos el pac
	if ($object->fk_pac > 0)
	  {
	    $objpac->fetch($object->fk_pac);
	    if ($objpac->id == $object->fk_pac)
	      {
		if ($objpac->fk_poa != $object->fk_poa)
		  {
		    $error++;
		    $mesg = '<div class="error">'.$langs->trans('Error, el pac no pertenece al poa').'</div>';
		  }
	      }
	  }
	// $object->fk_father       = GETPOST('fk_father')+0;
	// if (!empty($object->fk_father))
	//   {
	//     $objnew = new Poaprev($db);
	//     if ($objnew->fetch('',$object->fk_father)>0)
	//       if ($objnew->nro_preventive == $object->fk_father)
	// 	$object->fk_father = $objnew->id;
	//       else
	// 	$object->fk_father = 0;
	//     else
	//       $object->fk_father = 0;
	//   }
	// else
	//   $object->fk_father = 0;

	//preventive gestion pasada

	$nro_preventive_ant = GETPOST('nro_preventive_ant');
	$gestion_ant = GETPOST('gestion_ant');
	if (!empty($nro_preventive_ant) && !empty($gestion_ant))
	  {
	    $objnew = new Poaprev($db);
	    if ($objnew->fetch('',$nro_preventive_ant,$gestion_ant)>0)
	      if ($objnew->nro_preventive == $nro_preventive_ant && $objnew->gestion == $gestion_ant)
		$object->fk_prev_ant = $objnew->id;
	      else
		$object->fk_prev_ant = 0;
	    else
	      $object->fk_prev_ant = 0;
	  }
	else
	  $object->fk_prev_ant = 0;

	$object->fk_prev         = GETPOST('fk_prev');
	$object->fk_area         = GETPOST('fk_area');
	$object->nro_activity    = GETPOST('nro_activity');
	$object->priority        = GETPOST('priority');
	$object->code_requirement= GETPOST('code_requirement');
	$object->date_activity   = $date_preventive;
	$object->fk_user_create  = GETPOST('fk_user_create');
	$object->label           = GETPOST('label');
	$object->partida         = GETPOST('partida');
	$object->amount          = GETPOST('amount');
	$object->pseudonym       = GETPOST('pseudonym');
	if (empty($error))
	  {
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
    $tmparray['fk_pac'] = GETPOST('fk_pac');
    $tmparray['idp'] = GETPOST('idp');
    $tmparray['gestion'] = GETPOST('gestion');
    $tmparray['fk_father'] = GETPOST('fk_father');
    $tmparray['nom'] = GETPOST('nom');
    $tmparray['nro_activity'] = GETPOST('nro_activity');
    $tmparray['date_activity'] = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));

    if (! empty($tmparray['fk_pac']))
      {
	$object->fk_pac = $tmparray['fk_pac'];
	$objpac->fetch($object->fk_pac);
	$object->gestion = $tmparray['gestion'];
	$object->fk_father = $tmparray['fk_father'];
	$object->nom = $tmparray['nom'];
	$object->nro_activity = $tmparray['nro_activity'];
	$object->date_activity = $tmparray['date_activity'];
	if ($objpac->id == $object->fk_pac)
	  {
	    $object->label  = $objpac->nom;
	    $object->amount = $objpac->amount;

	  }
	$action='create';
      }
  }

if ( ($action == 'createeditpar') )
  {
    require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
    //$tmparray=getProperty(GETPOST('country_id','int'),'all',$db,$langs,0);
    $tmparray['fk_structure'] = GETPOST('fk_structure');
    $tmparray['fk_poa'] = GETPOST('fk_poa');
    $tmparray['partida'] = GETPOST('partida');
    $tmparray['amount'] = GETPOST('amount');
    if (! empty($tmparray['fk_poa']))
      {
	$objpre->fk_structure = $tmparray['fk_structure'];
	$objpre->fk_poa = $tmparray['fk_poa'];
	$objpre->partida = $tmparray['partida'];
	$objpre->amount = $tmparray['amount'];
	$action='';
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

$form=new Form($db);

// $aArrcss= array('poa/css/style.css','poa/css/title.css','poa/css/styles.css','poa/css/poamenu.css');
// $aArrjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/poa.js','poa/js/scriptajax.js');
// $help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
// llxHeader("",$langs->trans("Activity"),$help_url,'','','',$aArrjs,$aArrcss);

header("Content-type: text/html; charset=".$conf->file->character_set_client);

$aArrayofcss= array('poa/css/style.css','poa/css/styles.css','poa/css/poamenu.css','poa/css/bootstrap-responsive.min.css','poa/css/style-responsive.css');
$aArrayofjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/poa.js','poa/js/scriptajax.js');

top_htmlhead($head,$langs->trans("POA"),0,0,$aArrayofjs,$aArrayofcss);

//filtro
$idTag1 = 1;
$idTag2 = 2;

//impresion de submenu segun seleccion
include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/menup.tpl.php';

//cuerpo
print '<br><br><br>';



if ($action == 'create' && $user->rights->poa->act->crear)
{
    $objpoa->fetch($fk_poa);
    $objpac->fetch_poa($fk_poa);
    $lLoop = count($objpac->array);
    print_fiche_titre($langs->trans("Newactivity"));
    //armamos el script para que se ejecute por todas las lineas del pac
    if ($lLoop > 0)
      for ($k = 1; $k <= $lLoop; $k++)
	{
	  print "\n".'<script type="text/javascript" language="javascript">';
	  print '$(document).ready(function () {';
	  print '$("#selectfk_pac'.$k.'").change(function() {';
	  print ' document.form_fiche.action.value="createedit";
                document.form_fiche.submit();
              });
          });';
	  print '</script>'."\n";
	}
    print '<form name="form_fiche" class="form-horizontal" role="form" action="'.$_SERVER['PHP_SELF'].'" method="post">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="add">';
    print '<input type="hidden" name="fk_poa" value="'.$fk_poa.'">';
    print '<input type="hidden" name="dol_hide_leftmenu" value="1">';

    dol_htmloutput_mesg($mesg);

    //print '<table class="border" width="100%">';

    // pac
    //listamos todos los pac que afecten al poa
    $k = 0;
    if (count($objpac->array) > 0)
      {
	print '<div class="container">';
	print '<table class="table">';
	print '<thead>';
	print '<tr>';
	print_liste_field_titre($langs->trans("Select"),"", "","","","");
	print_liste_field_titre($langs->trans("Pac"),"", "","","","");
	print '</tr>';
	print '</thead>';
	print '<tbody>';
	//registro vacio
	print '<tr><td align="center">';
	print '<input type="radio" '.(empty($object->fk_pac)?'checked':'').' id="selectfk_pac'.$k.'" name="fk_pac" value="0">';
	print '</td>';
	print '<td colspan="2">';
	print $langs->trans('Noselection');
	print '</td></tr>';
	$k++;
	foreach((array) $objpac->array AS $j => $obj_pac)
	  {
	    print '<tr><td align="center">';
	    print '<input type="radio" '.($object->fk_pac == $obj_pac->id?'checked':'').' id="selectfk_pac'.$k.'" name="fk_pac" value="'.$obj_pac->id.'">';
	    print '</td>';
	    print '<td colspan="2">';
	    print $obj_pac->nom;
	    print '</td></tr>';
	    $k++;
	  }
	print '</table>';
      }

    // print '<tr><td>'.$langs->trans('PAC').'</td><td colspan="2">';
    // print $objpac->select_pac($object->fk_pac,'fk_pac','',120,1);
    // print '</td></tr>';

    //fk_prev
    if ($user->admin || $user->rights->poa->act->crear)
      {
	print '<div class="form-group">';
	print '<label class="control-label col-sm-2">'.$langs->trans('Preventive').'</label>';
	print '<div class="col-sm-10">';
	print $objpre->select_poa_prev($object->fk_prev,'fk_prev','',100,1,$gestion,$idFather,'T');
	print '</div>';
	print '</div>';
      }
    // area
    print '<div class="form-group">';
    print '<label class="control-label col-sm-2">'.$langs->trans('Area').'</label>';
    if (!$user->admin)
      {
	$objarea->fetch($idFather);
	print '<div class="col-sm-10">';
	print $objarea->label;
	print '<input type="hidden" name="fk_area" value="'.$idFather.'"';
	print '</div>';
      }
    else
      {
	print '<div class="col-sm-10">';
	print $objarea->select_area((empty($object->fk_area)?$idFather:$object->fk_area),'fk_area','',120,1);
	print '</div>';
      }
    print '</div>';


    // gestion
    print '<div class="form-group">';
    print '<label class="control-label col-sm-2">'.$langs->trans('Gestion').'</label>';
    print '<div class="col-sm-10">';
    print '<input id="gestion" type="text" value="'.(empty($object->gestion)?date('Y'):$object->gestion).'" name="gestion" size="6" maxlength="4">';
    print '</div>';
    print '</div>';
    // label
    print '<div class="form-group">';
    print '<label class="control-label col-sm-2">'.$langs->trans('Name').'</label>';
    print '<div class="col-sm-10">';
    print '<input id="label500" type="text" value="'.(empty($object->label)?$objpoa->label:$object->label).'" name="label" size="120" maxlength="255">';
    print '</div>';
    print '</div>';

    // pseudonym
    print '<div class="form-group">';
    print '<label class="control-label col-sm-2">'.$langs->trans('Pseudonym').'</label>';
    print '<div class="col-sm-10">';
    print '<input id="pseudonym500" type="text" value="'.(empty($object->pseudonym)?$objpoa->pseudonym:$object->pseudonym).'" name="pseudonym" size="120" maxlength="50">';
    print '</div>';
    print '</div>';

    //nro
    print '<div class="form-group">';
    print '<label class="control-label col-sm-2">'.$langs->trans('Nro').'</label>';
    print '<div class="col-sm-10">';
    if ($user->admin)
      print '<input id="nro_activity" type="text" value="'.(empty($object->nro_activity)?$object->fetch_next_nro($gestion):$object->nro_activity).'" name="nro_activity" size="15" maxlength="12">';
    else
      {
	print $langs->trans('Automatic');
      }
    print '</div>';
    print '</div>';

    //priority
    print '<div class="form-group">';
    print '<label class="control-label col-sm-2">'.$langs->trans('Priority').'</label>';
    print '<div class="col-sm-10">';
    print '<input id="priority" type="text" value="'.$object->priority.'" name="priority" size="5" maxlength="2">';
    print '</div>';
    print '</div>';

    //requirementtype
    print '<div class="form-group">';
    print '<label class="control-label col-sm-2">'.$langs->trans('Requirementtype').'</label>';
    print '<div class="col-sm-10">';
    print select_requirementtype($object->code_requirement,'code_requirement','',1,0,'code');
    print '</div>';
    print '</div>';
    //date_preventive
    print '<div class="form-group">';
    print '<label class="control-label col-sm-2">'.$langs->trans('Date').'</label>';
    print '<div class="col-sm-10">';
    $form->select_date((empty($object->date_assign)?dol_now():$object->date_assign),'di_','','','',"date",1,1);
    print '</div>';
    print '</div>';
    //continuacion de preventivo gestiones anteriores
    print '<div class="form-group">';
    print '<label class="control-label col-sm-2">'.$langs->trans('Preventivemainlast').'</label>';
    print '<div class="col-sm-10">';
    print '<input id="nro_preventive_ant" type="text" value="'.$nro_preventive_ant.'" name="nro_preventive_ant" size="8" maxlength="12" placeholder="'.$langs->trans('Preventivemain').'">';
    print '<input id="gestion_ant" type="text" value="'.$gestion_ant.'" name="gestion_ant" size="4" maxlength="4" placeholder="'.$langs->trans('Year').'">';
    print info_admin($langs->trans("Only to retrieve and process the start of monitoring in the workflow"),1);
    print '</div>';
    print '</div>';
    //partida
    print '<div class="form-group">';
    print '<label class="control-label col-sm-2">'.$langs->trans('Partida').'</label>';
    print '<div class="col-sm-10">';
    print '<input id="partida" type="text" value="'.(empty($object->partida)?$objpoa->partida:$object->partida).'" name="partida" size="10" maxlength="12">';
    print '</div>';
    print '</div>';
    //monto
    print '<div class="form-group">';
    print '<label class="control-label col-sm-2">'.$langs->trans('Amount').'</label>';
    print '<div class="col-sm-10">';
    print '<input id="amount" type="number" step="any" min="0" value="'.(empty($object->amount)?$objpoa->amount:$object->amount).'" name="amount" size="10" maxlength="12">';
    print '</div>';
    print '</div>';
    //respon
    print '<div class="form-group">';
    print '<label class="control-label col-sm-2">'.$langs->trans('Responsible').'</label>';
    print '<div class="col-sm-10">';
    $exclude = array();
    if (empty($object->entity)) $object->entity = $conf->entity;
    if ($user->rights->poa->prev->creart)
      print $form->select_dolusers((empty($object->fk_user_create)?$user->id:$object->fk_user_create),'fk_user_create',1,$exclude,0,'','',$object->entity);
    else
      {
	if ($objuser->fetch($user->id))
	  print $objuser->lastname.' '.$objuser->firstname;
	print '<input type="hidden" name="fk_user_create" value="'.$user->id.'">';
      }
    print '</div>';
    print '</div>';

    //print '</table>';

    print '<center><br><button type="button" class="btn btn-primary">'.$langs->trans("Create").'</button></center>';

    print '</form>';
    print "<div class=\"tabsAction\">\n";

    print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/poa/liste.php?dol_hide_leftmenu=1">'.$langs->trans("Return").'</a>';
    print '</div>';
}
else
{
    if ($id)
    {
        dol_htmloutput_mesg($mesg);
        $result = $object->fetch($id);
        if ($result < 0) dol_print_error($db);
        //obtenemos el proceso del preventivo
        $aProcess = getlist_process($object->fk_prev);
        $fk_prev_pri = $object->fk_prev;
        $fk_proc_pri = 0;
        //verificamos si tiene un preventivo principal
        if (count($aProcess['pri'])>0)
            foreach ((array) $aProcess['pri'] AS $fkProcess => $fk_prev_)
            {
                $fk_proc_pri = $fkProcess;
                $fk_prev_pri = $fk_prev_;
            }
        //recuperamos el proceso
        $lProcess = false;
        $fk_type_con = 0;
        if ($fk_proc_pri)
        {
            $lProcess = true;
            $objproc->fetch($fk_proc_pri);
            $fk_type_con = $objproc->fk_type_con;
        }
        /*
        * Affichage fiche
        */
        if ($action <> 'edit' && $action <> 're-edit')
        {
            //$head = fabrication_prepare_head($object);
            dol_fiche_head($head, 'card', $langs->trans("Activity"), 0, 'mant');

    	     /*
	         * Confirmation de la validation
	          */
            if ($action == 'validate')
            {
                if ($object->fetch(GETPOST('id'))>0)
                {
                    //cambiando a validado
                    $object->statut = 1;
                    //update
                    $object->update($user);
                    $action = '';
                }
            }
	       /*
	       * Confirmation de la no  validation
	       */
            if ($action == 'novalidate')
            {
                if ($object->fetch(GETPOST('id'))>0)
                {
                    //cambiando a validado
                    $object->statut = 0;
                    //update
                    $object->update($user);
                    $action = '';
                }
            }
    	     /*
	         * Confirmation de la no  validation
	          */
            if ($action == 'close')
            {
                if ($object->fetch(GETPOST('id'))>0)
                {
                    //cambiando a validado
                    $object->statut = 9;
                    //update
                    $object->update($user);
                    $action = '';
                }
            }
    	     /*
	         * Confirmation de la no  validation
	          */
            if ($action == 'noclose')
            {
                if ($object->fetch(GETPOST('id'))>0)
                {
                    //cambiando a validado
                    $object->statut = 1;
                    //update
                    $object->update($user);
                    $action = '';
                }
            }

    	     // Confirm delete activity
            if ($action == 'delete')
            {
                $form = new Form($db);
                $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deleteactivity"),$langs->trans("Confirmdeleteactivity",$object->ref.' '.$object->detail),"confirm_delete",'',0,2);
                if ($ret == 'html') print '<br>';
            }

            // Confirm delete preventive
            if ($action == 'deletemod')
            {
                $form = new Form($db);
                $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&idpp='.$idpp,$langs->trans("Deletemodifprev"),$langs->trans("Confirmdeletemodifprev",$object->ref.' '.$object->detail),"confirm_delete_mod",'',0,2);
                if ($ret == 'html') print '<br>';
            }

            // Confirm delete partida producto
            if ($action == 'deleteins')
            {
                $form = new Form($db);
                $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&idppp='.$idppp,$langs->trans("Deleteproduct"),$langs->trans("Confirmdeleteproduct",$object->ref.' '.$object->detail),"confirm_delete_product",'',0,2);
                if ($ret == 'html') print '<br>';
            }

            // Confirm delete partida producto
            if ($action == 'validateprev')
            {
                //buscamos la seleccion de la modalidad
                $aTable = fetch_tables($_GET['fk_type_con']);
                $form = new Form($db);
                $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&fk_type_con='.$_GET['fk_type_con'],$langs->trans("Validatepreventive"),$langs->trans("Confirmvalidatepreventive").' : '.$langs->trans('Modality').': '.$aTable['label'].' '.$langs->trans('Of').' '.price($aTable['range_ini']).' '.$langs->trans('To').' '.price($aTable['range_fin']),"confirm_validate_prev",'',0,2);
                if ($ret == 'html') print '<br>';
            }

            // Confirm cancel preventive
            if ($action == 'anulate')
            {
                $form = new Form($db);
                $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Cancelpreventive"),$langs->trans("Confirmcancelpreventive",$object->ref.' '.$object->detail),"confirm_cancel",'',0,2);
                if ($ret == 'html') print '<br>';
            }

            // Confirm delete preventive partida
            if ($action == 'delpartida')
            {
                //buscamos la partida y monto
                $objpre->fetch($idp);
                $form = new Form($db);
                $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&idp='.$idp,$langs->trans("Deletepreventivepartida"),$langs->trans("Confirmdeletepreventivepartida".': '.$langs->trans('Partida').' '.$objpre->partida.' '.$langs->trans('Amount').' '.price($objpre->amount),''),"confirm_delete_partida",'',0,2);
                if ($ret == 'html') print '<br>';
            }


            // Confirm delete moni workflow
            if ($action == 'deletemon')
            {
                //buscamos la partida y monto
                $objectw->fetch($idr);
                $form = new Form($db);
                $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&idr='.$idr,$langs->trans("Deletemonitoreo"),$langs->trans("Confirmdeletemonitoreo".': '.$langs->trans('Followup').': '.$objectw->followup.' : '.$langs->trans('Followto').' '.$objectw->followto,''),"confirm_delete_mon",'',0,2);
                if ($ret == 'html') print '<br>';
            }


            print '<table class="border" width="100%">';

            $linkback = '<a href="'.DOL_URL_ROOT.'/poa/poa/liste.php">'.$langs->trans("BackToList").'</a>';

            // pac
            print '<tr><td>'.$langs->trans('PAC').'</td><td colspan="2">';
            $objpac->fetch($object->fk_pac);
            if ($objpac->id == $object->fk_pac) print $objpac->nom;
            else print '&nbsp;NOT '.$object->fk_pac;
            print '</td></tr>';

            //fk_prev
            print '<tr><td>'.$langs->trans('Preventive').'</td><td colspan="2">';
            if ($objpre->fetch($object->fk_prev)>0) print $objpre->nro_preventive.' '.$objpre->label;
            else print '&nbsp;';
            print '</td></tr>';

            // area
            print '<tr><td>'.$langs->trans('Area').'</td><td colspan="2">';
            $objarea->fetch($object->fk_area);
            if ($objarea->id == $object->fk_area) print $objarea->label;
            else print '&nbsp;';
            print '</td></tr>';

            // gestion
            print '<tr><td>'.$langs->trans('Gestion').'</td><td colspan="2">';
            print $object->gestion;
            print '</td></tr>';

            // label
            print '<tr><td>'.$langs->trans('Name').'</td><td colspan="2">';
            print $object->label;
            print '</td></tr>';

            // pseudonym
            print '<tr><td>'.$langs->trans('Pseudonym').'</td><td colspan="2">';
            print $object->pseudonym;
            print '</td></tr>';

            //nro
            print '<tr><td>'.$langs->trans('Nro').'</td><td colspan="2">';
            print $object->nro_activity;
            print '</td></tr>';

            //priority
            print '<tr><td>'.$langs->trans('Priority').'</td><td colspan="2">';
            print $object->priority;
            print '</td></tr>';

            //requirementtype
            print '<tr><td>'.$langs->trans('Requirementtype').'</td><td colspan="2">';
            print select_requirementtype($object->code_requirement,'code_requirement','',0,1,'code');
            print '</td></tr>';

            //date_activity
            print '<tr><td>'.$langs->trans('Date').'</td><td colspan="2">';
            dol_print_date($object->date_activity,"day");
            print '</td></tr>';

            //partida
            print '<tr><td>'.$langs->trans('Partida').'</td><td colspan="2">';
            print $object->partida;
            print '</td></tr>';

            //amount
            print '<tr><td>'.$langs->trans('Amount').'</td><td colspan="2">';
            print price(price2num($object->amount,"MT"));
            print '</td></tr>';

            //respon
            print '<tr><td>'.$langs->trans('Responsible').'</td><td colspan="2">';
            $objuser->fetch($object->fk_user_create);
            if ($objuser->id == $object->fk_user_create) print $objuser->lastname.' '.$objuser->firstname;
            else print '&nbsp;';
            print '</td></tr>';
            print "</table>";
            print '</div>';

            /* ********************************* */
            /*                                   */
            /* Barre d'action                    */
            /*                                   */
            /* ********************************* */

            print "<div class=\"tabsAction\">\n";
            print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/poa/liste.php?dol_hide_leftmenu=1">'.$langs->trans("Return").'</a>';

            if ($action == '')
            {
                //aumentar la verificacion del estadod
                if (($user->rights->poa->act->mod && $object->statut == 0) && ($object->fk_user_create == $user->id || $user->admin))
                    print "<a class=\"butAction\" href=\"fiche.php?action=edit&dol_hide_leftmenu=1&id=".$object->id."\">".$langs->trans("Modify")."</a>";
                else
                    print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";
                if ($user->rights->poa->act->del && $object->statut == 0)
                    print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&dol_hide_leftmenu=1&id=".$object->id."\">".$langs->trans("Delete")."</a>";
                else
                    print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
                if ($user->rights->poa->act->val && $object->statut == 0)
                    print "<a class=\"butAction\" href=\"fiche.php?action=validate&dol_hide_leftmenu=1&id=".$object->id."\">".$langs->trans("Validate")."</a>";
                else
                    print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Validate")."</a>";
                if ($user->rights->poa->act->val && $object->statut == 1)
                    print "<a class=\"butAction\" href=\"fiche.php?action=novalidate&dol_hide_leftmenu=1&id=".$object->id."\">".$langs->trans("Notvalidate")."</a>";
                else
                    print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Notvalidate")."</a>";

                if ($user->rights->poa->act->nul && $object->statut > 0)
                    print "<a class=\"butAction\" href=\"fiche.php?action=anulate&dol_hide_leftmenu=1&id=".$object->id."\">".$langs->trans("Cancel")."</a>";
                else
                    print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Cancel")."</a>";
                if ($user->rights->poa->prev->crear && $object->statut == 1 && $object->fk_prev <=0)
                    print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/execution/fiche.php?action=create&dol_hide_leftmenu=1&fk_poa='.$object->fk_poa.'&fk_activity='.$object->id.'">'.$langs->trans("Createprev").'</a>';

                if ($user->rights->poa->act->end && ($object->statut > 0 && $object->statut <9))
                    print "<a class=\"butAction\" href=\"fiche.php?action=close&dol_hide_leftmenu=1&id=".$object->id."\">".$langs->trans("Finishactivity")."</a>";
                else
                    print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Finishactivity")."</a>";

                if ($user->rights->poa->act->end && $object->statut == 9)
                    print "<a class=\"butAction\" href=\"fiche.php?action=noclose&dol_hide_leftmenu=1&id=".$object->id."\">".$langs->trans("Reopenactivity")."</a>";
                else
                    print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Reopenactivity")."</a>";
            }
            print "</div>";

            //mostramos
            print '<div class="fiche">';
            //pestanas
            print '<div class="tabs">';
            print '<div class="inline-block tabsElem">';
            print '<a id="card" class="'.($tabs=='crono'?'tabactive':'').' tab inline-block" href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&tabs=crono&dol_hide_leftmenu=1">'.$langs->trans('Cronograma').'</a>';
            print '</div>';
            print '<div class="'.($tabs=='moni'?'tabactive':'').' inline-block tabsElem">';
            print '<a id="card" class="tab inline-block" href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&tabs=moni&dol_hide_leftmenu=1">'.$langs->trans('Monitoring').'</a>';
            print '</div>';
            if ($fk_type_con)
            {
                print '<div class="'.($tabs=='check'?'tabactive':'').' inline-block tabsElem">';
                print '<a id="card" class="tab inline-block" href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&tabs=check&dol_hide_leftmenu=1">'.$langs->trans('Checklist').'</a>';
                print '</div>';
            }
            print '</div>';
            print '<div class="tabBar">';
            if ($tabs=='crono')
                include DOL_DOCUMENT_ROOT.'/poa/activity/tpl/crono.tpl.php';
            if ($tabs=='moni')
                include DOL_DOCUMENT_ROOT.'/poa/activity/tpl/moni.tpl.php';
            if ($tabs=='check' && $fk_type_con)
                include DOL_DOCUMENT_ROOT.'/poa/activity/tpl/check.tpl.php';
            print '</div>'; //tabbar
            print '</div>';

            //para subir archivos
            print_fiche_titre($langs->trans("Documents"));

            dol_fiche_head();
            // echo $user->rights->poa->act->leer.' && '.$object->statut.' == 1 && ('.$user->admin.' || '.$user->id.' == '.$object->fk_user_create;
            if ($user->rights->poa->act->leer && $object->statut == 1 && ($user->admin || $user->id == $object->fk_user_create))
                include DOL_DOCUMENT_ROOT.'/poa/activity/tpl/adddoc.tpl.php';

            //listamos los archivos subidos
            include DOL_DOCUMENT_ROOT.'/poa/activity/tpl/doclist.tpl.php';

            dol_fiche_end();
        }
        /*
        * Edition fiche
        */
        if (($action == 'edit' || $action == 're-edit') && 1)
        {
            print_fiche_titre($langs->trans("Activityedit"), $mesg);
            print '<form action="fiche.php" method="POST">';
            print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
            print '<input type="hidden" name="action" value="update">';
            print '<input type="hidden" name="id" value="'.$object->id.'">';
            print '<input type="hidden" name="dol_hide_leftmenu" value="1">';
            print '<table class="border" width="100%">';
            // pac
            print '<tr><td>'.$langs->trans('PAC').'</td><td colspan="2">';
            print $objpac->select_pac($object->fk_pac,'fk_pac','',120,1);
            print '</td></tr>';
            // poa
            print '<tr><td>'.$langs->trans('POA').'</td><td colspan="2">';
            print $objpoa->select_poa($object->fk_poa,'fk_poa','',120,1);
            print '</td></tr>';
            //fk_prev
            if ($user->admin || $user->rights->poa->act->crear)
            {
                print '<tr><td class="fieldrequired">'.$langs->trans('Preventive').'</td><td colspan="2">';
                print $objpre->select_poa_prev($object->fk_prev,'fk_prev','',100,1,$gestion,$fk_area,'T');
                print '</td></tr>';
            }

            // area
            print '<tr><td class="fieldrequired">'.$langs->trans('Area').'</td><td colspan="2">';
            print $objarea->select_area($object->fk_area,'fk_area','',120,1);
            print '</td></tr>';
            // gestion
            print '<tr><td class="fieldrequired">'.$langs->trans('Gestion').'</td><td colspan="2">';
            print '<input id="gestion" type="text" value="'.(empty($object->gestion)?date('Y'):$object->gestion).'" name="gestion" size="6" maxlength="4">';
            print '</td></tr>';
            // label
            print '<tr><td class="fieldrequired">'.$langs->trans('Name').'</td><td colspan="2">';
            print '<input id="label500" type="text" value="'.$object->label.'" name="label" maxlength="255">';
            print '</td></tr>';
            // pseudonym
            print '<tr><td>'.$langs->trans('Pseudonym').'</td><td colspan="2">';
            print '<input id="pseudonym500" type="text" value="'.$object->pseudonym.'" name="pseudonym" maxlength="50">';
            print '</td></tr>';
            //nro
            print '<tr><td class="fieldrequired">'.$langs->trans('Nro').'</td><td colspan="2">';
            if ($user->admin)
                print '<input id="nro_preventive" type="text" value="'.$object->nro_activity.'" name="nro_activity" size="15" maxlength="12">';
            else
            {
                print $object->nro_activity;
                print '<input id="nro_preventive" type="hidden" value="'.$object->nro_activity.'" name="nro_activity">';
            }
            print '</td></tr>';

            //priority
            print '<tr><td class="fieldrequired">'.$langs->trans('Priority').'</td><td colspan="2">';
            print '<input id="priority" type="text" value="'.$object->priority.'" name="priority" size="5" maxlength="2">';
            print '</td></tr>';

            //requirementtype
            print '<tr><td class="fieldrequired">'.$langs->trans('Requirementtype').'</td><td colspan="2">';
            print select_requirementtype($object->code_requirement,'code_requirement','',1,0,'code');
            print '</td></tr>';

            //date_preventive
            print '<tr><td class="fieldrequired">'.$langs->trans('Date').'</td><td colspan="2">';
            $form->select_date($object->date_activity,'di_','','','',"date",1,1);
            print '</td></tr>';

            //continuacion de preventivo gestiones anteriores
            $nro_preventive_ant = '';
            $gestion_ant = '';
            if ($object->fk_prev_ant)
            {
                $objnew = new Poaprev($db);
                if ($objnew->fetch($object->fk_prev_ant)>0)
                {
                    $nro_preventive_ant = $objnew->nro_preventive;
                    $gestion_ant = $objnew->gestion;
                }
            }
            print '<tr><td>'.$langs->trans('Preventivemainlast').'</td><td colspan="2">';
            print '<input id="nro_preventive_ant" type="text" value="'.$nro_preventive_ant.'" name="nro_preventive_ant" size="8" maxlength="12" placeholder="'.$langs->trans('Preventivemain').'">';
            print '<input id="gestion_ant" type="text" value="'.$gestion_ant.'" name="gestion_ant" size="4" maxlength="4" placeholder="'.$langs->trans('Year').'">';
            print info_admin($langs->trans("Only to retrieve and process the start of monitoring in the workflow"),1);
            print '</td></tr>';

            //partida
            print '<tr><td class="fieldrequired">'.$langs->trans('Partida').'</td><td colspan="2">';
            print '<input id="partida" type="text" value="'.(empty($object->partida)?$objpoa->partida:$object->partida).'" name="partida" size="10" maxlength="12">';
            print '</td></tr>';

            //monto
            print '<tr><td class="fieldrequired">'.$langs->trans('Amount').'</td><td colspan="2">';
            print '<input id="amount" type="number" step="any" min="0" value="'.(empty($object->amount)?$objpoa->amount:$object->amount).'" name="amount" size="10" maxlength="12">';
            print '</td></tr>';
            //respon
            print '<tr><td class="fieldrequired">'.$langs->trans('Responsible').'</td><td colspan="2">';
            $exclude = array();
            if (empty($object->entity)) $object->entity = $conf->entity;
            print $form->select_dolusers((empty($object->fk_user_create)?$user->id:$object->fk_user_create),'fk_user_create',1,$exclude,0,'','',$object->entity);

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
