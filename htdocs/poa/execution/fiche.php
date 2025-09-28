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
 *	\file       htdocs/poa/execution/fiche.php
 *	\ingroup    Preventive
 *	\brief      Page fiche POA preventive
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';


require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poaprev.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poaprevseg.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/process/class/poaprocess.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/pac/class/poapac.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/poa/class/poapoa.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/structure/class/poastructure.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/area/class/poaarea.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/area/class/poaareauser.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidapre.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidapredet.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poaactivityprev.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/process/class/poaprocess.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/activity/class/poaactivity.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/flowmodels/class/cflowmodels.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/workflow/class/poaworkflow.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/workflow/class/poaworkflowdet.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/lib/poa.lib.php';
require_once DOL_DOCUMENT_ROOT.'/poa/lib/doc.lib.php';

require_once DOL_DOCUMENT_ROOT.'/poa/class/poapreemail.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/class/html.formadd.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
//images
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

$langs->load("poa@poa");

$action=GETPOST('action');

$id          = GETPOST("id"); //id preventivo
$idp         = GETPOST("idp");
$fk_poa      = GETPOST("fk_poa"); //id poa
$idpp        = GETPOST("idpp");
$idppp       = GETPOST("idppp"); //id insumo partida_pre_det
$fk_activity = GETPOST('fk_activity');
$sortfield   = GETPOST("sortfield");
$sortorder   = GETPOST("sortorder");

if (empty($_SESSION['gestion']))
  $_SESSION['gestion'] = date('Y');;
$gestion     = $_SESSION['gestion'];

$mesg = '';


$object  	= new Poaprev($db);//poa prev
$object_s       = new Poaprevseg($db); //poa prev seguimiento
$objuser 	= new User($db);
$objpac  	= new Poapac($db);
$objpoa  	= new Poapoa($db);
$objarea 	= new Poaarea($db);
$objareauser 	= new Poaareauser($db);
$objprev     	= new Poapartidapre($db);
$objprevdet  	= new Poapartidapredet($db);
$objstr      	= new Poastructure($db);
$objact      	= new Poaactivity($db);
$objproc        = new Poaprocess($db);
//$objacpr        = new Poaactivityprev($db);
$aExcludeArea = array();
$idFather = 0;
$lPrevseg = false;

//conectando a otra base de datos
// $dbtype='mysql';
// $dbhost='localhost';
// $dbuser='root';
// $dbpass = 'dddddddddd';
// $dbname = 'pruebas';
// $dbport = '3306';
// $dbv=getDoliDBInstance($dbtype,$dbhost,$dbuser,$dbpass,$dbname,$dbport);
// if ($dbv->error)
//   {
//     dol_print_error($dbv,"host=".$dbhost.", port=".$dbport.", user=".$dbuser.", databasename=".$dbname.", ".$dbv->error);
//     exit;
//   }
//  else
//    {
//      $sql = 'select * from user';
//      $res = $dbv->query($sql);
//      if ($res > 0)
//        {
// 	 $num = $db->num_rows($res);
// 	 $i = 0;
// 	 while ($i < $num)
// 	   {
// 	     $obj = $dbv->fetch_object($result);
// 	     $obj->email.' '.$obj->user_id;
// 	     $i++;
// 	   }
//        }
//    }
// //fin conexion base datos extra

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

// Add
if ($action == 'add' && $user->rights->poa->prev->crear)
  {
	$error = 0;
	$date_preventive = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));

	$object->gestion         = GETPOST('gestion');
	$fk_father       = GETPOST('fk_father')+0; //nro_preventivo

	//agrego nuevo seguimiento
	$object_s->initAsSpecimen();
	$object_s->fk_father = 0;
	$object_s->date_create = dol_now();
	$object_s->fk_user_create = $user->id;
	$object_s->tms = dol_now();
	$object_s->statut = 1;

	if (!empty($fk_father))
	  {
	$objnew = new Poaprev($db);
	if ($objnew->fetch('',$fk_father,$object->gestion)>0)//busco
	  if ($objnew->nro_preventive == $fk_father)//verifico
		$object_s->fk_father = $objnew->id;
	  else
		$object_s->fk_father = 0;
	else
	  $object_s->fk_father = 0;
	  }
	else
	  $object_s->fk_father = 0;
	//preventive gestion pasada
	$nro_preventive_ant = GETPOST('nro_preventive_ant');
	$gestion_ant = GETPOST('gestion_ant');
	if (!empty($nro_preventive_ant) && !empty($gestion_ant))
	  {
	$objnew = new Poaprev($db);
	if ($objnew->fetch('',$nro_preventive_ant,$gestion_ant)>0)
	  if ($objnew->nro_preventive == $nro_preventive_ant && $objnew->gestion == $gestion_ant)
		$object_s->fk_prev_ant = $objnew->id;
	  else
		$object_s->fk_prev_ant = 0;
	else
	  $object_s->fk_prev_ant = 0;
	  }
	else
	  $object_s->fk_prev_ant = 0;

	//armamos el preventivo nuevo
	$object->fk_pac          = GETPOST('fk_pac')+0;
	$object->fk_area         = GETPOST('fk_area');
	$object->nro_preventive  = GETPOST('nro_preventive');
	$object->priority        = GETPOST('priority');
	$object->code_requirement= GETPOST('code_requirement');
	$object->date_preventive = $date_preventive;
	$object->fk_user_create  = GETPOST('fk_user_create');
	$object->label           = GETPOST('label');
	$object->pseudonym       = GETPOST('pseudonym');

	if (empty($object->label))
	  {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errorlabelisrequired").'</div>';
	  }
	$object->date_create = dol_now();
	$object->tms = dol_now();
	if ($object->fk_user_create <= 0)
	  $object->fk_user_create = $user->id;
	$object->amount = 0;
	$object->entity = $conf->entity;
	$object->statut = 0;
	$object->active = 1;

	if (empty($object->nro_preventive))
	  {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errornropreventiveisrequired").'</div>';
	  }
	if (empty($error))
	  {
	$db->begin();
	$id = $object->create($user);
	if ($id > 0)
	  {
		//agregamos al preventivo seguimiento
		$object_s->fk_prev = $id;
		$res = $object_s->create($user);
		//actualizando en activity
		if ($objact->fetch(GETPOST('fk_activity'))>0)
		  {
		$objact->fk_prev = $id;
		$objact->nro_activity = $object->nro_preventive;
		// if (empty($object->fk_father))
		//   $objacpr->fk_prev = $id;
		//$objact->statut = 0;
		$idr = $objact->update($user);
		if ($idr>0)
		  {
			$db->commit();
			header("Location: ".$_SERVER['PHP_SELF']."?id=".$id.'&fk_poa='.$fk_poa);
			exit;
		  }
		  }
		else
		  {
		$action = 'create';
		$mesg='<div class="error">'.$objact->error.'</div>';
		  }
	  }
	$db->rollback();
	$action = 'create';
	$mesg='<div class="error">'.$object->error.'</div>';
	  }
	else
	  {
	if ($error)
	  $action="create";   // Force retour sur page creation
	  }
  }

//uppdf
if ($action == 'uppdf')
{
	$linklast = GETPOST('linklast','alpha');
	if ($object->fetch($_POST["id"])>0)
	{
		// Logo/Photo save
		$dir     = $conf->poa->dir_output.'/execution/pdf';
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

			if (doc_format_supported($_FILES['docpdf']['name']) > 0)
			{
				dol_mkdir($dir);
				if (@is_dir($dir))
				{
					$newfile=$dir.'/'.dol_sanitizeFileName($_FILES['docpdf']['name']);
					$newfile=$dir.'/'.dol_sanitizeFileName($id.'.pdf');
					$result = dol_move_uploaded_file($_FILES['docpdf']['tmp_name'], $newfile, 1);
					if (! $result > 0) $errors[] = "ErrorFailedToSaveFile";
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
				$errors[] = "ErrorBadImageFormat";

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
	}
	if ($linklast)
	{
		header('Location: '.$linklast);
		exit;
	}
}

// Addpartida
if ($action == 'addpartida' && $user->rights->poa->prev->crear)
  {
	$error = 0;

	$objprev->fk_poa_prev = $id;
	$objprev->fk_poa       = GETPOST('fk_poa');
	$objprev->fk_structure = GETPOST('fk_structure');
	$objprev->partida = GETPOST('partida');
	$objprev->amount  = GETPOST('amount');
	$objprev->tms     = date('YmdHis');
	$objprev->statut  = 1;
	$objprev->active  = 0;

	if (empty($objprev->fk_structure))
	  {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errorpoaisrequired").'</div>';
	  }
	if (empty($objprev->partida))
	  {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errorpartidaisrequired").'</div>';
	  }
	if (empty($objprev->amount))
	  {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Erroramountisrequired").'</div>';
	  }

	if (empty($error))
	  {
	$idp = $objprev->create($user);
	if ($idp > 0)
	  {
		header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
		exit;
	  }
	$action = '';
	$mesg='<div class="error">'.$objprev->error.'</div>';
	  }
	else
	  {
	if ($error)
	  $action="";   // Force retour sur page creation
	  }
  }

// updatepartida
if ($action == 'updatepartida' && $user->rights->poa->prev->mod)
  {
	$error = 0;
	//buscamos
	if ($objprev->fetch($idp)>0)
	  {
	$objprev->fk_poa_prev = $id;
	$objprev->fk_poa       = GETPOST('fk_poa');
	$objprev->fk_structure = GETPOST('fk_structure');
	$objprev->partida = GETPOST('partida');
	$objprev->amount  = GETPOST('amount');
	$objprev->statut  = 1;
	$objprev->active  = 0;

	if (empty($objprev->fk_structure))
	  {
		$error++;
		$mesg.='<div class="error">'.$langs->trans("Errorpoaisrequired").'</div>';
	  }
	if (empty($objprev->partida))
	  {
		$error++;
		$mesg.='<div class="error">'.$langs->trans("Errorpartidaisrequired").'</div>';
	  }
	if (empty($objprev->amount))
	  {
		$error++;
		$mesg.='<div class="error">'.$langs->trans("Erroramountisrequired").'</div>';
	  }

	if (empty($error))
	  {
		$idp = $objprev->update($user);
		if ($idp > 0)
		  {
		header("Location: ".$_SERVER['PHP_SELF']."?id=".$id.'&dol_hide_leftmenu=1');
		exit;
		  }
		$action = 'editpartida';
		$mesg='<div class="error">'.$objprev->error.'</div>';
	  }
	else
	  {
		if ($error)
		  $action="";   // Force retour sur page creation
	  }
	  }
	else
	  {
	$action = 'editpartida';
	$mesg='<div class="error">'.$objprev->error.'</div>';
	  }
  }


// Addmodify //disminuciones del preventivo
if ($action == 'addmodify' && $user->rights->poa->prev->dis)
  {
	$error = 0;
	//recibimos

	$aPartidaPre = GETPOST('amount');
	foreach ((array) $aPartidaPre AS $idp => $value)
	  {
	$objpartidapre = new Poapartidapre($db);
	//buscamos en partidapre
	if ($objpartidapre->fetch($idp) && $value > 0)
	  {
		//registro nuevo con los valores encontrados
		$objprev->fk_poa       = $objpartidapre->fk_poa;
		$objprev->fk_structure = $objpartidapre->fk_structure;
		$objprev->fk_poa_prev  = $objpartidapre->fk_poa_prev;
		$objprev->partida = $objpartidapre->partida;
		$objprev->amount  = $value * -1;
		$objprev->tms     = date('YmdHis');
		$objprev->statut  = 1;
		$objprev->active  = 1;
		$result = $objprev->create($user);
	  }
	  }
	header("Location: ".$_SERVER['PHP_SELF']."?id=".$id.'&dol_hide_leftmenu=1');
	exit;
  }

// Addmodify //disminuciones del preventivo
if ($action == 'addmodifyr' && $user->rights->poa->prev->dis)
  {
	$error = 0;
	//recibimos
	//$objprev = new Poapartidapre($db);
	$res = $objprev->fetch(GETPOST('idpp'));
	if ($res>0 && $objprev->id == GETPOST('idpp'))
	  {
	$objprev->amount = GETPOST('amount') * -1;
	$objprev->tms = dol_now();
	$result = $objprev->update($user);

	  }
	header("Location: ".$_SERVER['PHP_SELF']."?id=".$id.'&dol_hide_leftmenu=1');
	exit;
  }

// Addpartidaprod
if ($action == 'addpartidaprod' && $user->rights->poa->prev->crear)
  {
	$error = 0;
	$objprevdet->fk_poa_partida_pre = GETPOST('idp');
	$objprevdet->detail       = GETPOST('detail');
	$objprevdet->quant = GETPOST('quant');
	$objprevdet->amount_base = GETPOST('amount_base');
	$objprevdet->fk_product = 0;
	$objprevdet->fk_contrato = 0;
	$objprevdet->amount = 0;
	$objprevdet->tms     = date('YmdHis');
	$objprevdet->statut  = 1;
	if (empty($objprevdet->detail))
	  {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errordetailisrequired").'</div>';
	  }
	if (empty($objprevdet->quant))
	  {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errorquantisrequired").'</div>';
	  }
	if (empty($error))
	  {
	$idpd = $objprevdet->create($user);
	if ($idpd > 0)
	  {
		header("Location: ".$_SERVER['PHP_SELF']."?id=".$id
		   ."&idp=".$_POST['idp'].
		   "&action=eproduct&dol_hide_leftmenu=1");
		exit;
	  }
	$action = 'eproduct';
	$_GET['idp'] = $_POST['idp'];
	$mesg='<div class="error">'.$objprevdet->error.'</div>';
	  }
	else
	  {
	if ($error)
	  {
		$action="eproduct";   // Force retour sur page creation
		$_GET['idp'] = $_POST['idp'];
	  }
	  }
  }

// Updatepartidaprod
if ($action == 'updatepartidaprod' && $user->rights->poa->prev->crear)
  {
	$idppp = GETPOST('idppp');
	if ($objprevdet->fetch($idppp)>0)
	  {
	$error = 0;
	$objprevdet->fk_poa_partida_pre = GETPOST('idp');
	$objprevdet->detail       = GETPOST('detail');
	$objprevdet->quant = GETPOST('quant');
	$objprevdet->amount_base = GETPOST('amount_base');
	$objprevdet->fk_product = 0;
	//$objprevdet->amount = 0;
	$objprevdet->tms     = dol_now();
	$objprevdet->statut  = 1;
	if (empty($objprevdet->detail))
	  {
		$error++;
		$mesg.='<div class="error">'.$langs->trans("Errordetailisrequired").'</div>';
	  }
	if (empty($objprevdet->quant))
	  {
		$error++;
		$mesg.='<div class="error">'.$langs->trans("Errorquantisrequired").'</div>';
	  }
	if (empty($error))
	  {
		$res = $objprevdet->update($user);
		if ($res > 0)
		  {
		header("Location: ".$_SERVER['PHP_SELF']."?id=".$id
			   ."&idp=".$_POST['idp'].
			   "&action=eproduct&dol_hide_leftmenu=1");
		exit;
		  }
		$action = 'eproduct';
		$_GET['idp'] = $_POST['idp'];
		$_GET['idppp'] = $_POST['idppp'];
		$mesg='<div class="error">'.$objprevdet->error.'</div>';
	  }
	else
	  {
		if ($error)
		  {
		$action="eproduct";   // Force retour sur page creation
		$_GET['idp'] = $_POST['idp'];
		$_GET['idppp'] = $_POST['idppp'];
		  }
	  }
	  }
  }

// Adduser
if ($action == 'adduser' && $user->rights->poa->area->crear)
  {
	$error = 0;
	$objuser->fk_area = $_POST["id"];
	$objuser->fk_user = GETPOST('fk_user');
	$objuser->date_create = date('Y-m-d');
	$objuser->tms = date('YmdHis');
	$objuser->active  = 1;
	if (empty($objuser->fk_user))
	  {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Erroruserisrequired").'</div>';
	  }

	if (empty($error))
	  {
	if ($objuser->create($user))
	  {
		header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
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

// validate prev //opcion nueva
if ($action == 'confirm_validate_prev' && $_REQUEST["confirm"] == 'yes'
	&& $user->rights->poa->prev->val)
  {
	$error = 0;
	$object->fetch($_REQUEST["id"]);
	$fk_type_con = $_REQUEST["fk_type_con"];
	$db->begin();

	//obtener la suma
	$total = $objprev->getsum(GETPOST('id'));
	//cambiando a validado
	$object->amount = $total;
	$object->statut = 1;
	//$object->ref = $object->codref;
	//update
	$res1 = $object->update($user);
	if ($res1<=0)
	  {
	$error++;
	$mesg.='<div class="error">'.$object->error.'</div>';
	  }
	//$action = '';
	//creamos el inicio de proceso pendiente de numeracion

	//creamos el workflow
	// $objwork = new Poaworkflow($db);
	// $objwork->date_workflow = $object->date_preventive;
	// $objwork->fk_poa_prev = $object->id;
	// $objwork->contrat = GETPOST('contrat')+0;
	// $objwork->deadlines = GETPOST('deadlines')+0;
	// $objwork->fk_user_create = $user->id;
	// $objwork->tms = dol_now();
	// $objwork->statut = 1;

	// if ($objwork->contrat < 0)
	//   {
	// 	$error++;
	// 	echo '<hr>error 2';
	// 	echo $mesg.='<div class="error">'.$langs->trans("Errorcontratisrequired").'</div>';
	//   }
	// //actualizamos el poa_activity_prev
	// $objact->fetch('',$_REQUEST['id']);
	// if ($objact->fk_prev == $_REQUEST['id'])
	//   {
	// 	echo '<hr>encuentra ';
	// 	$objact->statut = 1;
	// 	if ($objacpr->update($user)<=0)
	// 	  {
	// 	    echo '<hr>error 3';
	// 	    $error++;
	// 	    $mesg.='<div class="error">'.$objacpr->error.'</div>';
	// 	  }
	//   }
	// else
	//   {
	// 	echo '<hr>no es igual ';
	// 	$error++;
	//   }
	if (empty($error))
	  {
	// $idw = $objwork->create($user);
	// if ($idw > 0)
	//   {
	//     //obtenemos el primero hito del cflowmodels
	//     $objflow = new Cflowmodels($db);
	//     $objflow->getlist($fk_type_con);
	//     $objcflow = '';
	//     foreach ((array) $objflow->array AS $k => $objnew)
	//       {
	// 	if (empty($objcflow))
	// 	  $objcflow = $objnew;
	//       }
	//     if ($objcflow->code)
	//       {
	// 	//crear el primer hito
	// 	$objworkd = new Poaworkflowdet($db);
	// 	$objworkd->initAsSpecimen();
	// 	$objworkd->fk_poa_workflow = $idw;
	// 	$objworkd->code_area_last = $object->fk_area;
	// 	$objworkd->code_area_next = $object->fk_area;
	// 	$objworkd->code_procedure = $objcflow->code;
	// 	$objworkd->date_tracking = $object->date_preventive;
	// 	$objworkd->detail = $objcflow->label;
	// 	$objworkd->sequen = 1;
	// 	$objworkd->fk_user_create = $user->id;
	// 	$objworkd->tms = dol_now();
	// 	$objworkd->statut = 1;
	// 	$result = $objworkd->create($user);
	// 	if ($result > 0)
	// 	  {
			$db->commit();
			header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
			exit;
	// 	  }
	// 	else
	// 	  {
	// 	    $db->rollback();
	// 	    $mesg='<div class="error">'.$objworkd->error.'</div>';
	// 	  }
	//       }
	//     else
	//       {
	// 	$db->rollback();
	// 	$mesg='<div class="error">'.$langs->trans('Errornotdefinedflowforthemodality').'</div>';
	//       }
	//   }
	// else
	//   {
	//     $db->rollback();
	//     $mesg='<div class="error">'.$objwork->error.'</div>';
	//   }
	  }
	else
	  $db->rollback();
	$action='';
  }
// Delete prev
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->poa->prev->del)
  {
  $error = 0;
  $object->fetch($_REQUEST["id"]);
  $db->begin();
  //cambiando el estado en partida prev
  $objprev->getlist($_REQUEST["id"]);
  if (count($objprev->array) > 0)
	{
	  foreach ((array) $objprev->array AS $idprev => $objprevdet)
	{
	  $objnew = new Poapartidapre($db);
	  if ($objnew->fetch($idprev))
		{
		  $objnew->statut = 2;
		  $result = $objnew->update($user);
		  if ($result <=0)
		$error++;
		}
	  else
		$error++;
	}
	}
  if (empty($error))
	{
	  $result=$object->delete($user);
	  if ($result > 0)
	{
	  $db->commit();
	  header("Location: ".DOL_URL_ROOT.'/poa/execution/liste.php');
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

// Delete prev modification
if ($action == 'confirm_delete_mod' && $_REQUEST["confirm"] == 'yes' && $user->rights->poa->prev->del)
{
  $error = 0;
  if ($objprev->fetch($_REQUEST["idpp"])>0)
	{
	  $res = $objprev->delete($user);
	  if ($res > 0)
	{
	  header("Location: ".$SERVER['PHP_SELF'].'?id='.$id);
	  exit;
	}
	  else
	{
	  $mesg='<div class="error">'.$objprev->error.'</div>';
	  $action='';
	}
	}
  else
	{
	  $mesg='<div class="error">'.$objprev->error.'</div>';
	  $action = '';
	}
 }

// Delete prev modification
if ($action == 'confirm_delete_product' && $_REQUEST["confirm"] == 'yes' && $user->rights->poa->prev->del)
{
  $error = 0;
  if ($objprevdet->fetch($_REQUEST["idppp"])>0)
	{
	  $idp = $objprevdet->fk_poa_partida_pre;
	  $res = $objprevdet->delete($user);
	  if ($res > 0)
	{
	  header("Location: ".$SERVER['PHP_SELF'].'?id='.$id.'&idp='.$idp.'&action=eproduct&dol_hide_leftmenu=1');
	  exit;
	}
	  else
	{
	  $mesg='<div class="error">'.$objprevdet->error.'</div>';
	  $action='';
	}
	}
  else
	{
	  $mesg='<div class="error">'.$objprevdet->error.'</div>';
	  $action = '';
	}
 }

// Cancel preventive
if ($action == 'confirm_cancel' && $_REQUEST["confirm"] == 'yes' && $user->rights->poa->prev->nul)
{
  $object->fetch($_REQUEST["id"]);
  //verificamos que no tenga proceso iniciado
  $objproc = new Poaprocess($db);
  $objproc->fetch_prev($_REQUEST["id"]);
  $result = 1;
  if ($objproc->fk_poa_prev == $object->id)
	{
	  if ($objproc->statut != -1)
	{
	  $result = 0;
	  $mesg='<div class="error">'.$langs->trans('Has boot process, delete the startup process').'</div>';
	  $action='';
	}
	}
  if ($result>0)
	{
	  $object->statut = -1;
	  $result=$object->update($user);
	  if ($result > 0)
	{
	  header("Location: ".DOL_URL_ROOT.'/poa/poa/liste.php?dol_hide_leftmenu=1');
	  exit;
	}
	  else
	{
	  $mesg='<div class="error">'.$object->error.'</div>';
	  $action='';
	}
	}
 }

// Delete charge
if ($action == 'confirm_delete_partida' && $_REQUEST["confirm"] == 'yes' && $user->rights->poa->prev->delit)
{
  $objprev->fetch($_REQUEST["idp"]);
  $objprev->statut = 2; //anulado
  $result=$objprev->update($user);
  if ($result <=0)
	{
	  $mesg='<div class="error">'.$objprev->error.'</div>';
	  $action='';
	}
 }

// Modification entrepot
if ($action == 'update' && $_POST["cancel"] <> $langs->trans("Cancel"))
  {
	$error = 0;
	if ($object->fetch($_POST["id"])>0)
	  {
	//busco el preventivo seguimiento
	$object_s->fetch('',$_POST["id"]);
	$lPrevseg = false; //no existe
	if ($object_s->fk_prev == $object->id)
	  $lPrevseg = true; //si existe
	//actualizamos la actividad
	//buscamos si ya se seleccion la actividad
	//si no existe guardamos
	$fk_activity = GETPOST('fk_activity');
	if ($objact->fetch(GETPOST('fk_activity'))>0 && $fk_activity > 0)
	  {
		if (empty($objact->fk_prev) || $objact->fk_prev == $id)
		  {
		$objact->fk_prev = $id;
		$ida = $objact->update($user);
		if ($ida <=0)
		  {
			$error++;
			$mesg.='<div class="error">'.$objact->error.'</div>';
			$action = 'edit';
		  }
		  }
		else
		  {
		$error++;
		$mesg.='<div class="error">'.$langs->trans('Errortheactivityalreadyhaspreventive').'</div>';
		$action = 'edit';
		  }
	  }
	$db->begin();
	$date_preventive = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
	$object->gestion = GETPOST('gestion');
	$fk_father       = GETPOST('fk_father')+0;
	//$lPrevseg = false;
	//$lPrevsegnew = false;
	if (!empty($fk_father))
	  {
		$objnew = new Poaprev($db);
		if ($objnew->fetch('',$fk_father,$object->gestion)>0)
		  if ($objnew->nro_preventive == $fk_father)
		{
		  if ($lPrevseg)
			{
			  //actualizo
			  $object_s->fk_father = $objnew->id;
			  $object_s->tms = dol_now();
			}
		  else
			{
			  //agrego nuevo
			  // $lPrevsegnew = true;
			  $object_s->initAsSpecimen();
			  $object_s->fk_prev = $object->id;
			  $object_s->fk_father = $objnew->id;
			  $object_s->date_create = dol_now();
			  $object_s->fk_user_create = $user->id;
			  $object_s->tms = dol_now();
			  $object_s->statut = 1;
			}
		  //$object->fk_father = $objnew->id;
		}
		  else
		$object_s->fk_father = 0;
		else
		  $object_s->fk_father = 0;
	  }
	else
	  {
		$object_s->fk_father = 0;
		$object_s->fk_prev = $object->id;
		$object_s->date_create = dol_now();
		$object_s->tms = dol_now();
		$object_s->fk_user_create = $user->id;
		$object_s->statut = 1;
	  }
	//preventive gestion pasada
	$nro_preventive_ant = GETPOST('nro_preventive_ant');
	$gestion_ant = GETPOST('gestion_ant');
	if (!empty($nro_preventive_ant) && !empty($gestion_ant))
	  {
		if (!$lPrevseg)
		  {
		//se debe crear
		$object_s->initAsSpecimen();
		$object_s->fk_prev = $object->id;
		$object_s->fk_father = 0;
		$object_s->date_create = dol_now();
		$object_s->fk_user_create = $user->id;
		$object_s->tms = dol_now();
		$object_s->statut = 1;
		  }
		$objnew = new Poaprev($db);
		if ($objnew->fetch('',$nro_preventive_ant,$gestion_ant)>0)
		  if ($objnew->nro_preventive == $nro_preventive_ant && $objnew->gestion == $gestion_ant)
		$object_s->fk_prev_ant = $objnew->id;
		  else
		$object_s->fk_prev_ant = 0;
		else
		  $object_s->fk_prev_ant = 0;
	  }
	else
	  $object_s->fk_prev_ant = 0;

	//	exit;
	$object->fk_pac          = GETPOST('fk_pac');
	$object->fk_area         = GETPOST('fk_area');
	$object->nro_preventive  = GETPOST('nro_preventive');
	$object->priority        = GETPOST('priority');
	$object->code_requirement= GETPOST('code_requirement');
	$object->date_preventive = $date_preventive;
	$object->fk_user_create  = GETPOST('fk_user_create');
	$object->label           = GETPOST('label');
	$object->pseudonym       = GETPOST('pseudonym');
	if (empty($error))
	  {
	   $res = $object->update($_POST["id"], $user);
		if ( $res > 0)
		  {
		//actualizamos seguimiento
		if ($lPrevseg)
		  $res = $object_s->update($user);
		else
		  $res = $object_s->create($user);
		// if ($lPrevseg)
		//   {
		//     if ($lPrevsegnew)
		//       $res = $object_s->create($user);
		//     else
		//       $res = $object_s->update($user);
		//   }
		if ($res > 0)
		  {
			$db->commit();
			$action = '';
			$_GET["id"] = $_POST["id"];
			header("Location: ".$_SERVER['PHP_SELF'].'?id='.$id);
			exit;
		  }
		else
		  {
			$db->rollback();
			$action = 'edit';
			$_GET["id"] = $_POST["id"];
			$mesg = '<div class="error">'.$object_s->error.'</div>';
		  }
		  }
		else
		  {
		$db->rollback();
		$action = 'edit';
		$_GET["id"] = $_POST["id"];
		$mesg = '<div class="error">'.$object->error.'</div>';
		  }
	  }
	else
	  $db->rollback();
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
	$tmparray['nro_preventive'] = GETPOST('nro_preventive');
	$tmparray['date_preventive'] = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));

	if (! empty($tmparray['fk_pac']))
	  {
	$object->fk_pac = $tmparray['fk_pac'];
	$objpac->fetch($object->fk_pac);
	$object->gestion = $tmparray['gestion'];
	$object->fk_father = $tmparray['fk_father'];
	$object->nom = $tmparray['nom'];
	$object->nro_preventive = $tmparray['nro_preventive'];
	$object->date_preventive = $tmparray['date_preventive'];
	if ($objpac->id == $object->fk_pac)
	  {
		$object->label  = $objpac->nom;
		$object->amount = $objpac->amount;

	  }
	$action='create';
	  }
  }

if ( ($action == 'createedit_a') )
  {
	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	//$tmparray=getProperty(GETPOST('country_id','int'),'all',$db,$langs,0);
	$tmparray['fk_activity'] = GETPOST('fk_activity');
	$tmparray['fk_pac'] = GETPOST('fk_pac');
	$tmparray['fk_poa'] = GETPOST('fk_poa');
	$tmparray['idp'] = GETPOST('idp');
	$tmparray['gestion'] = GETPOST('gestion');
	$tmparray['fk_father'] = GETPOST('fk_father');
	$tmparray['nom'] = GETPOST('nom');
	$tmparray['nro_preventive'] = GETPOST('nro_preventive');
	$tmparray['date_preventive'] = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));

	if (! empty($tmparray['fk_activity']))
	  {
	$object->fk_pac = $tmparray['fk_activity'];
	$object->fk_pac = $tmparray['fk_pac'];
	$fk_poa = $tmparray['fk_poa'];
	$objpac->fetch($object->fk_pac);
	$object->gestion = $tmparray['gestion'];
	$object->fk_father = $tmparray['fk_father'];
	$object->nom = $tmparray['nom'];
	$object->nro_preventive = $tmparray['nro_preventive'];
	$object->date_preventive = $tmparray['date_preventive'];
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
	$objprev->fk_structure = $tmparray['fk_structure'];
	$objprev->fk_poa = $tmparray['fk_poa'];
	$objprev->partida = $tmparray['partida'];
	$objprev->amount = $tmparray['amount'];
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
$formadd=new Formadd($db);

$aArrcss= array('poa/css/style.css','poa/css/title.css','poa/css/styles.css','poa/css/poamenu.css');
$aArrjs = array('poa/js/config.js','poa/js/ajax.js','poa/js/enviareform.js','poa/js/poa.js','poa/js/scriptajax.js');
$help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
llxHeader("",$langs->trans("Preventive"),$help_url,'','','',$aArrjs,$aArrcss);

if ($action == 'create' && $user->rights->poa->prev->crear)
  {
	$res = $objact->fetch($fk_activity);
	$objpac->fetch_poa($fk_poa);
	$lLoop = count($objpac->array);

	print_fiche_titre($langs->trans("Newpreventive"));
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
	//armamos para cuando se seleccione una actividad verifique si ya tiene creado la ejecucion (preventivo)
	print "\n".'<script type="text/javascript" language="javascript">';
	print '$(document).ready(function () {';
	print '$("#selectfk_activity'.$k.'").change(function() {';
	print ' document.form_fiche.action.value="createedit_a";
				document.form_fiche.submit();
			  });
		  });';
	print '</script>'."\n";

	print '<form name="form_fiche" action="'.$_SERVER['PHP_SELF'].'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="fk_poa" value="'.$fk_poa.'">';
	print '<input type="hidden" name="dol_hide_leftmenu" value="1">';
	dol_htmloutput_mesg($mesg);

	print '<table class="border" width="100%">';

	// pac
	//listamos todos los pac que afecten al poa
	$k = 0;
	if (count($objpac->array) > 0)
	  {
	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans("Select"),"", "","","","");
	print_liste_field_titre($langs->trans("Pac"),"", "","","","");
	print '</tr>';
	//registro vacio
	print '<tr><td align="center">';
	print '<input type="radio" '.(empty($object->fk_pac)?'checked':'').' id="selectfk_pac'.$k.'" name="fk_pac" value="0">';
	print '</td>';
	print '<td colspan="2">';
	print $langs->trans('Noselection');
	print '</td></tr>';
	$k++;
	  }
	$fk_pac = (empty($object->fk_pac)?$objact->fk_pac:$object->fk_pac);
	foreach((array) $objpac->array AS $j => $obj_pac)
	  {
	print '<tr><td align="center">';
	print '<input type="radio" '.($fk_pac == $obj_pac->id?'checked':'').' id="selectfk_pac'.$k.'" name="fk_pac" value="'.$obj_pac->id.'">';
	print '</td>';
	print '<td colspan="2">';
	print $obj_pac->nom;
	print '</td></tr>';
	$k++;
	  }

	// print '<tr><td>'.$langs->trans('PAC').'</td><td colspan="2">';
	// print $objpac->select_pac($object->fk_pac,'fk_pac','',120,1);
	// print '</td></tr>';

	//activity
	print '<tr><td class="fieldrequired">'.$langs->trans('Activity').'</td><td colspan="2">';
	if (!$user->admin)
	  {
	if($res>0)
	  {
		print $objact->label;
		print '<input type="hidden" name="fk_activity" value="'.$fk_activity.'"';
	  }
	else
	  $mesg = '<div class="error">'.$langs->trans("Erroractivityisnull").'</div>';
	  }
	else
	  if ($fk_poa)
	print $objact->select_activity((empty($object->fk_activity)?$fk_activity:$object->fk_activity),'fk_activity','',120,1,0,1,''," AND fk_poa = ".$fk_poa);
	  else
	print $langs->trans('Errorpoaisrequired');
	print '</td></tr>';

	// area
	print '<tr><td class="fieldrequired">'.$langs->trans('Area').'</td><td colspan="2">';
	if (!$user->admin)
	  {
	$objarea->fetch($idFather);
	print $objarea->label;
	print '<input type="hidden" name="fk_area" value="'.$idFather.'"';
	  }
	else
	  print $objarea->select_area((empty($object->fk_area)?(!empty($objact->fk_area)?$objact->fk_area:$idFather):$object->fk_area),'fk_area','',120,1);
	print '</td></tr>';


	// gestion
	print '<tr><td class="fieldrequired">'.$langs->trans('Gestion').'</td><td colspan="2">';
	print '<input id="gestion" type="text" value="'.(empty($object->gestion)?date('Y'):$object->gestion).'" name="gestion" size="6" maxlength="4">';
	print '</td></tr>';

	// father
	print '<tr><td>'.$langs->trans('Preventive top').'</td><td colspan="2">';
	print '<input id="fk_father" type="text" value="'.$object->fk_father.'" name="fk_father" size="6" maxlength="4" placeholder="'.$langs->trans('Preventivemain').'">';
	print '</td></tr>';

	// label
	print '<tr><td class="fieldrequired">'.$langs->trans('Name').'</td><td colspan="2">';
	print '<input id="label500" type="text" value="'.(empty($object->label)?$objact->label:$object->label).'" name="label" size="120" maxlength="255">';
	print '</td></tr>';

	// pseudonym
	print '<tr><td>'.$langs->trans('Pseudonym').'</td><td colspan="2">';
	print '<input id="pseudonym500" type="text" value="'.(empty($object->pseudonym)?$objact->pseudonym:$object->pseudonym).'" name="pseudonym" size="120" maxlength="50">';
	print '</td></tr>';

	//nro
	print '<tr><td class="fieldrequired">'.$langs->trans('Nro').'</td><td colspan="2">';
	print '<input id="nro_preventive" type="text" value="'.(empty($object->nro_preventive)?$objact->nro_activity:$object->nro_preventive).'" name="nro_preventive" size="15" maxlength="12">';
	print '</td></tr>';

	//priority
	print '<tr><td class="fieldrequired">'.$langs->trans('Priority').'</td><td colspan="2">';
	print '<input id="priority" type="text" value="'.(empty($object->priority)?$objact->priority:$object->priority).'" name="priority" size="5" maxlength="2">';
	print '</td></tr>';

	//requirementtype
	print '<tr><td class="fieldrequired">'.$langs->trans('Requirementtype').'</td><td colspan="2">';
	print select_requirementtype((empty($object->code_requirement)?$objact->code_requirement:$object->code_requirement),'code_requirement','',1,0,'code');
	print '</td></tr>';

	//date_preventive
	print '<tr><td class="fieldrequired">'.$langs->trans('Date').'</td><td colspan="2">';
	$form->select_date((empty($object->date_assign)?dol_now():$object->date_assign),'di_','','','',"date",1,1);
	print '</td></tr>';

	//continuacion de preventivo gestiones anteriores
	print '<tr><td>'.$langs->trans('Preventivemainlast').'</td><td colspan="2">';
	print '<input id="nro_preventive_ant" type="text" value="'.$nro_preventive_ant.'" name="nro_preventive_ant" size="8" maxlength="12" placeholder="'.$langs->trans('Preventivemain').'">';
	print '<input id="gestion_ant" type="text" value="'.$gestion_ant.'" name="gestion_ant" size="4" maxlength="4" placeholder="'.$langs->trans('Year').'">';
	print info_admin($langs->trans("Only to retrieve and process the start of monitoring in the workflow"),1);
	print '</td></tr>';

	//respon
	print '<tr><td class="fieldrequired">'.$langs->trans('Responsible').'</td><td colspan="2">';
	$exclude = array();
	if (empty($object->entity)) $object->entity = $conf->entity;
	if ($user->rights->poa->prev->creart)
	  print $formadd->select_use((empty($object->fk_user_create)?$objact->fk_user_create:$object->fk_user_create), 'fk_user_create', '', 1);
	  //      print $form->select_dolusers((empty($object->fk_user_create)?$user->id:$object->fk_user_create),'fk_user_create',1,$exclude,0,'','',$object->entity);
	else
	  {
	if ($objuser->fetch($user->id))
	  print $objuser->lastname.' '.$objuser->firstname;
	print '<input type="hidden" name="fk_user_create" value="'.$user->id.'">';
	  }
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
	 $res = $object_s->fetch('',$id);
	 if ($result < 0)
	   {
		 dol_print_error($db);
	   }
	 //buscamos la actividad
	 $objact->fetch('',$id);
	 /*
	  * Affichage fiche
	  */
	 if ($action <> 'edit' && $action <> 're-edit')
	   {
		 //$head = fabrication_prepare_head($object);

		 dol_fiche_head($head, 'card', $langs->trans("Preventive"), 0, 'mant');

		 /*
		  * Confirmation de la validation
		  */
		 if ($action == 'validate')
		   {
		 $object->fetch(GETPOST('id'));
		  //obtener la suma
		 $total = $objprev->getsum(GETPOST('id'));
		 //cambiando a validado
		 $object->amount = $total;
		 $object->statut = 1;
		 $object->ref = $object->codref;
		  //update
		 $object->update($user);
		 $action = '';
		   }

		 // Confirm delete preventive
		 if ($action == 'delete')
		   {
		 $form = new Form($db);
		 $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("Deleteprev"),$langs->trans("Confirmdeleteprev",$object->ref.' '.$object->detail),"confirm_delete",'',0,2);
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
		 $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&idppp='.$idppp.'&dol_hide_leftmenu=1',$langs->trans("Deleteproduct"),$langs->trans("Confirmdeleteproduct",$object->ref.' '.$object->detail),"confirm_delete_product",'',0,2);
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
		 $objprev->fetch($idp);
		 $form = new Form($db);
		 $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id.'&idp='.$idp,$langs->trans("Deletepreventivepartida"),$langs->trans("Confirmdeletepreventivepartida".': '.$langs->trans('Partida').' '.$objprev->partida.' '.$langs->trans('Amount').' '.price($objprev->amount),''),"confirm_delete_partida",'',0,2);
		  if ($ret == 'html') print '<br>';
		   }

		 print '<table class="border" width="100%">';

		 //	  $linkback = '<a href="'.DOL_URL_ROOT.'/poa/execution/liste.php">'.$langs->trans("BackToList").'</a>';

		 // Ref
		 print '<tr><td width="20%">'.$langs->trans("Ref").'</td>';
		 print '<td class="valeur" colspan="2">';
		 //$object->ref = $object->rowid;
		 print $form->showrefnav($object, 'id', $linkback,1,'rowid','rowid');
		 print '</td></tr>';

		 // pac
		 print '<tr><td>'.$langs->trans('PAC').'</td><td colspan="2">';
		 $objpac->fetch($object->fk_pac);
		 if ($objpac->id == $object->fk_pac)
		   print $objpac->nom;
		 else
		   print '&nbsp;NOT '.$object->fk_pac;
		 print '</td></tr>';

		 // pac
		 print '<tr><td>'.$langs->trans('Activity').'</td><td colspan="2">';
		 if ($objact->fk_prev == $object->id)
		   print $objact->label;
		 print '</td></tr>';

		 // area
		 print '<tr><td>'.$langs->trans('Area').'</td><td colspan="2">';
		 $objarea->fetch($object->fk_area);
		 if ($objarea->id == $object->fk_area)
		   print $objarea->label;
		 else
		   print '&nbsp;';
		 print '</td></tr>';

	  // fk_father
		 print '<tr><td>'.$langs->trans('Preventive top').'</td><td colspan="2">';
		 $objnew = new Poaprev($db);
		 if ($objnew->fetch($object_s->fk_father)>0)
		   if ($objnew->id == $object_s->fk_father)
		 print $objnew->nro_preventive;

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
		 print $object->nro_preventive;
		 print '</td></tr>';

		 //priority
		 print '<tr><td>'.$langs->trans('Priority').'</td><td colspan="2">';
		 print $object->priority;
		 print '</td></tr>';

		 //requirementtype
		 print '<tr><td>'.$langs->trans('Requirementtype').'</td><td colspan="2">';
		 print select_requirementtype($object->code_requirement,'code_requirement','',0,1,'code');
		 print '</td></tr>';

		 //date_preventive
		 print '<tr><td>'.$langs->trans('Date').'</td><td colspan="2">';
		 print dol_print_date($object->date_preventive,"day");
		 print '</td></tr>';

		 //amount
		 print '<tr><td>'.$langs->trans('Amount').'</td><td colspan="2">';
		 print number_format(price2num($object->amount,"MT"),2);
		 print '</td></tr>';

		 //subir imagen
		 print '<tr><td>'.$langs->trans('PDF').'</td><td colspan="2">';
		 $dir = $conf->poa->dir_output."/execution/pdf/".$id.'.pdf';
		 $url = DOL_URL_ROOT.'/documents/poa'."/execution/pdf/".$id.'.pdf';
		 if ($user->rights->poa->prev->mod)
		   if ($action != 'upload')
		 {
		   print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&action=upload'.'">'.img_picto($langs->trans('Uploaddoc'),DOL_URL_ROOT.'/poa/img/subir.png','',1).'</a>';
		   //mostramos el archivo
		   if (file_exists($dir))
			 {

			   print '&nbsp;&nbsp;';
			   print '<a href="'.$url.'" target="_blank">'.img_picto($langs->trans('PDF'),'pdf2').'</a>';
			 }
		 }
		   else
		 {
		   include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/addpdf.tpl.php';
		 }
		 else
		   {
		 //mostramos el archivo
		 if (file_exists($dir))
		   {
			 print '&nbsp;&nbsp;';
			 print '<a href="'.$url.'" target="_blank">'.img_picto($langs->trans('PDF'),'pdf2').'</a>';
		   }
		   }
		 print '</td></tr>';

		 //continuacion de preventivo gestiones anteriores
		 print '<tr><td>'.$langs->trans('Preventivemainlast').'</td><td colspan="2">';
		 if ($object_s->fk_prev_ant)
		   {
		 $objnew = new Poaprev($db);
		 if ($objnew->fetch($object_s->fk_prev_ant)>0)
		   {
			 $nro_preventive_ant = $objnew->nro_preventive;
			 $gestion_ant = $objnew->gestion;
			 $label_ant = $objnew->label;
			 print $nro_preventive_ant.'-'.$gestion_ant.': '.$label_ant;
		   }
		 else
		   {
			 print $langs->trans('No defined').' '.$object_s->fk_prev_ant;
		   }
		   }
		 else
		   print '&nbsp;';
		 print '</td></tr>';

		 //respon
		 print '<tr><td>'.$langs->trans('Responsible').'</td><td colspan="2">';
		 $objuser->fetch($object->fk_user_create);
		 if ($objuser->id == $object->fk_user_create)
		   print $objuser->lastname.' '.$objuser->firstname;
		 else
		   print '&nbsp;';
		 print '</td></tr>';
		 print "</table>";

		 print '<br>';
		 print '<table class="liste_titre" width="100%">';
		 print '<tr class="liste_titre">';
		 print '<td>'.$langs->trans("Meta",$cursorline).'</td>';
		 print '<td>'.$langs->trans("Structure",$cursorline).'</td>';
		 print '<td align="center">'.$langs->trans("Partida").'</td>';
		 print '<td align="center">'.$langs->trans("Amount").'</td>';
		 print '<td align="right">'.$langs->trans("Action").'</td>';
		 print '</tr>';
		 //registro nuevo
		 if ($object->statut == 0  && $action!='editpartida')
		   {
		 include_once DOL_DOCUMENT_ROOT.'/poa/execution/tpl/form.tpl.php';
		   }

		 //definimos array para saldos
		 $aPrev = array();
		 //para habilitar boton de validacion
		 $lValidate = true;
		 $aValidate = array();
		 //listado partidas
		 $sumaPartida = 0;
		 $objprev->getlist($object->id);
		 if (count($objprev->array) > 0)
		   {
		 $var = true;
		 foreach ($objprev->array AS $j => $objpartidapre)
		   {
			 $sumaPartida+=$objpartidapre->amount;

			 //obtenemos la suma de la partida en insumos producto
			 $sumaParcial = $objprevdet->getsum($objpartidapre->id,0);
			 if ($sumaParcial != $objpartidapre->amount ||
			 empty($sumaParcial))
			   $lValidate = false;
			 else
			   $aValidate[$objpartidapre->id] = true;

			 if ($action == 'editpartida' && $objpartidapre->id == $idp)
			   {
			 //buscamos
			 $objprev->fetch($idp);
			 include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/form.tpl.php';
			   }
			 else
			   {
			 $var=!$var;
			 print "<tr $bc[$var]>";
			 //poa
			 print '<td>';
			 $objpoa->fetch($objpartidapre->fk_poa);
			 if ($objpoa->id == $objpartidapre->fk_poa)
			   print $objpoa->label;
			 else
			   print '&nbsp;';
			 print '</td>';
			 //structure
			 print '<td>';
			 $objstr->fetch($objpartidapre->fk_structure);
			 if ($objstr->id == $objpartidapre->fk_structure)
			   print $objstr->sigla;
			 else
			   print '&nbsp;';
			 print '</td>';

			 // partida
			 print '<td>';
			 print $objpartidapre->partida;
			 print '</td>';

			 // amount
			 print '<td align="right">';
			 print price(price2num($objpartidapre->amount,'MT'));
			 print '</td>';
			 //agregamos al array de saldos
			 $aPrev[$objpartidapre->id] += $objpartidapre->amount;
			 print '<td align="right">';
			 // if ($object->statut == 0)
			 // 	{
			 // 	  print '<a href="" alt="'.$langs->trans('Edit').'">'.img_picto($langs->trans('Edit'),'edit.png').'</a>';
			 // 	  print '&nbsp;';
			 // 	}
			 print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idp='.$objpartidapre->id.'&action=eproduct&dol_hide_leftmenu=1" alt="'.$langs->trans('Product').'">'.img_picto($langs->trans('Product'),DOL_URL_ROOT.'/poa/img/product.png','',1).'</a>';
			 if ($object->statut == 0 && $user->rights->poa->prev->delit)
			   {
				 print '&nbsp;&nbsp;';
				 print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idp='.$objpartidapre->id.'&action=delpartida" alt="'.$langs->trans('Delete').'">'.img_picto($langs->trans('Delete'),'delete').'</a>';

			   }
			 if ($user->admin && $user->rights->poa->prev->crear)
			   {
				 print '&nbsp;&nbsp;';
				 print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idp='.$objpartidapre->id.'&action=editpartida&dol_hide_leftmenu=1" alt="'.$langs->trans('Edit').'">'.img_picto($langs->trans('Edit'),'edit').'</a>';

			   }

			 print '</td>';
			 print '</tr>';
			 if ($_GET['idp'] == $objpartidapre->id && ($action == 'eproduct' || $action == 'editproduct'))
			   {
				 //editamos el registro de productos
				 print '<tr class="liste_titre">';
				 print '<td colspan="2">'.$langs->trans("Product",$cursorline).'</td>';
				 print '<td align="right">'.$langs->trans("Quant").'</td>';
				 print '<td align="right">'.$langs->trans("Amount").'</td>';

				 print '<td align="right">'.$langs->trans("Action").'</td>';
				 print '</tr>';
				 if ($user->admin || ($action != 'editproduct' && $object->statut == 0 && !$aValidate[$objpartidapre->id]))
				   {
				 $objprevdetclon = $objprevdet;
				 include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/addproduct.tpl.php';
				   }
				 //listando
				 $sumaProd = 0;
				 $objprevdet->getlist($objpartidapre->id,'N');
				 foreach((array) $objprevdet->array AS $k => $objprevpro)
				   {
				 echo $action.' '.$objprevpro->id.' '.$_GET['idppp'];
				 if ($action == 'editproduct' && $objprevpro->id == $_GET['idppp'])
				   {
					 $objprevdetclon = $objprevpro;
					 include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/addproduct.tpl.php';
				   }
				 else
				   {
					 $var=!$var;
					 print "<tr $bc[$var]>";
					 // producto
					 print '<td colspan="2">';
					 print $objprevpro->detail;
					 print '</td>';

					 // Quant
					 print '<td align="right">';
					 print price2num($objprevpro->quant,'MT');
					 print '</td>';

					 // amount base
					 print '<td align="right">';
					 print price(price2num($objprevpro->amount_base,'MT'));
					 $sumaProd+= $objprevpro->amount_base;
					 print '</td>';

					 print '<td align="right">';
					 if ($user->admin || $object->statut == 0)
					   {
					 if ($user->admin || $user->id == $object->fk_user_create)
					   {
						 print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&idp='.$objpartidapre->id.'&idppp='.$objprevpro->id.'&action=editproduct'.'">'.img_picto($langs->trans('Edit'),'edit.png').'</a>';
						 print '&nbsp;';
					   }
					 print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&idppp='.$objprevpro->id.'&action=deleteins'.'">'.img_picto($langs->trans('Delete'),'delete.png').'</a>';
					   }
					 else
					   print '&nbsp;';
					 print '</td>';
					 print '</tr>';
				   }
				   }
				 //totales
				 print '<tr class="color_total">';
				 print '<td colspan="3"></td>';
				 print '<td align="right">';
				 print price(price2num($sumaProd,'MT'));
				 print '</td>';
				 print '<td></td>';
				 print '</tr>';
			   }
			   }
		   }
		   }
		 print "</table>";
		 if ($object->statut > 0)
		   {
		 //nuevo para modificaciones preventivo
		 print '<br>';
		 print '<table class="liste_titre" width="100%">';
		 print '<tr class="liste_titre">';
		 print '<td>'.$langs->trans("Modify prev",$cursorline).'</td>';
		 print '<td>'.$langs->trans("Structure",$cursorline).'</td>';
		 print '<td align="center">'.$langs->trans("Partida").'</td>';
		 print '<td align="center">'.$langs->trans("Amount").'</td>';
		 print '<td align="right">'.$langs->trans("Action").'</td>';
		 print '</tr>';
		 //listado
		 $objprev->getlist($object->id,'N');
		 if (count($objprev->array) > 0)
		   {
			 $var = true;
			 foreach ($objprev->array AS $j => $objpartidapre)
			   {
			 if ($action == 'editmod' && $objpartidapre->id == $idpp)
			   {
				 //registro a modificar
				 print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
				 print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
				 print '<input type="hidden" name="action" value="addmodifyr">';
				 print '<input type="hidden" name="id" value="'.$object->id.'">';
				 print '<input type="hidden" name="idpp" value="'.$objpartidapre->id.'">';
				 print '<input type="hidden" name="dol_hide_leftmenu" value="1">';

				 $var=!$var;
				 print "<tr $bc[$var]>";
				 //poa
				 print '<td>';
				 $objpoa->fetch($objpartidapre->fk_poa);
				 if ($objpoa->id == $objpartidapre->fk_poa)
				   print $objpoa->label;
				 else
				   print '&nbsp;';
				 print '</td>';
				 //structure
				 print '<td>';
				 $objstr->fetch($objpartidapre->fk_structure);
				 if ($objstr->id == $objpartidapre->fk_structure)
				   print $objstr->sigla;
				 else
				   print '&nbsp;';
				 print '</td>';

				 // partida
				 print '<td>';
				 print $objpartidapre->partida;
				 print '</td>';

				 // amount
				 print '<td align="right">';
				 $amount = $objpartidapre->amount*-1;
				 print '<input type="number" min="0" step="any" name="amount" value="'.$amount.'">';
				 print '</td>';
				 print '<td>';
				 print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'"></center>';
				 print '</td>';
				 print '</tr>';
				 print '</form>';

			   }
			 else
			   {
				 $var=!$var;
				 print "<tr $bc[$var]>";
				 //poa
				 print '<td>';
				 $objpoa->fetch($objpartidapre->fk_poa);
				 if ($objpoa->id == $objpartidapre->fk_poa)
				   print $objpoa->label;
				 else
				   print '&nbsp;';
				 print '</td>';
				 //structure
				 print '<td>';
				 $objstr->fetch($objpartidapre->fk_structure);
				 if ($objstr->id == $objpartidapre->fk_structure)
				   print $objstr->sigla;
				 else
				   print '&nbsp;';
				 print '</td>';

				 // partida
				 print '<td>';
				 print $objpartidapre->partida;
				 print '</td>';

				 // amount
				 print '<td align="right">';
				 print price(price2num($objpartidapre->amount,'MT'));
				 print '</td>';
				 //restamos las disminuciones
				 $aPrev[$objpartidapre->id] += $objpartidapre->amount;
				 print '<td align="right">';
				 if ($user->admin || $object->statut == 1)
				   {
				 print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idpp='.$objpartidapre->id.'&action=editmod" alt="'.$langs->trans('Edit').'">'.img_picto($langs->trans('Edit'),'edit.png').'</a>';
				 print '&nbsp;';
				 print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idpp='.$objpartidapre->id.'&action=deletemod" alt="'.$langs->trans('Delete').'">'.img_picto($langs->trans('Delete'),'delete.png').'</a>';

				   }
				 print '</td>';
				 print '</tr>';
			   }
			   }
		   }
		 print "</table>";
		   }
		 //nuevo registro para modificaciones preventivo
		 if ($action == 'reduc')
		   {
		 print '<br>';
		 print '<h3>';
		 print $langs->trans('Nuevo registro de disminucion preventivo');
		 print '</h3>';

		 //registro nuevo
		 print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
		 print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		 print '<input type="hidden" name="action" value="addmodify">';
		 print '<input type="hidden" name="id" value="'.$object->id.'">';
		 print '<input type="hidden" name="dol_hide_leftmenu" value="1">';

		 print '<table class="liste_titre" width="100%">';
		 print '<tr class="liste_titre">';
		 print '<td>'.$langs->trans("Modify prev",$cursorline).'</td>';
		 print '<td>'.$langs->trans("Structure",$cursorline).'</td>';
		 print '<td align="center">'.$langs->trans("Partida").'</td>';
		 print '<td align="center">'.$langs->trans("Amount").'</td>';
		 print '</tr>';

		 //listado
		 $objprev->getlist($object->id,'S');
		 if (count($objprev->array) > 0)
		   {
			 $var = true;
			 foreach ($objprev->array AS $j => $objpartidapre)
			   {
			 $var=!$var;
			 print "<tr $bc[$var]>";
			 //poa
			 print '<td>';
			 $objpoa->fetch($objpartidapre->fk_poa);
			 if ($objpoa->id == $objpartidapre->fk_poa)
			   print $objpoa->label;
			 else
			   print '&nbsp;';
			 print '</td>';
			 //structure
			 print '<td>';
			 $objstr->fetch($objpartidapre->fk_structure);
			 if ($objstr->id == $objpartidapre->fk_structure)
			   print $objstr->sigla;
			 else
			   print '&nbsp;';
			 print '</td>';

			 // partida
			 print '<td>';
			 print $objpartidapre->partida;
			 print '</td>';

			 // amount
			 print '<td align="right">';
			 if ($action == 'reduc')
			   {
				 print '<input type="number" min="0" max="'.
				   $aPrev[$objpartidapre->id].'" step="any" name="amount['.
				   $objpartidapre->id.']" value="0"';
			   }
			 else
			   print price(price2num($objpartidapre->amount,'MT'));
			 print '</td>';

			 print '</tr>';
			   }
		   }
		 print "</table>";
		 print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';
		 print '</form>';
		   }
		 //fin nuevo para modificaciones preventivo
		 print '</div>';


		 /* ********************************* */
		 /*                                   */
		 /* Barre d'action                    */
		 /*                                   */
		 /* ********************************* */

		 print "<div class=\"tabsAction\">\n";

		 print '<a class="butAction" href="'.DOL_URL_ROOT.'/poa/execution/ficheprev.php?ida='.$objact->id.'&dol_hide_leftmenu=1">'.$langs->trans("Return").'</a>';

		 if ($action == '')
		   {
		 // if ($user->rights->poa->prev->crear)
		 // 	print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=create">'.$langs->trans("Createnew").'</a>';
		 // else
		 // 	print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";

		 //aumentar la verificacion del estadod
		 if ($user->admin ||
			 ($user->rights->poa->prev->mod && $object->fk_user_create == $user->id))
		   print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=edit&id='.$object->id.'&dol_hide_leftmenu=1">'.$langs->trans("Modify").'</a>';
		 else
		   print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

		 if ($user->rights->poa->prev->del && $object->statut == 0)
		   print '<a class="butActionDelete" href="'.$_SERVER['PHP_SELF'].'?action=delete&id='.$object->id.'&dol_hide_leftmenu=1">'.$langs->trans("Delete").'</a>';
		 else
		   print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
		 // if ($user->rights->poa->prev->val && $object->statut == 0 && $lValidate)
		 // 	print "<a class=\"butAction\" href=\"fiche.php?action=validate&id=".$object->id."\">".$langs->trans("Validate")."</a>";
		 // else
		 // 	print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Validate")."</a>";

		 if ($user->rights->poa->prev->nul && $object->statut > 0)
		   print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=anulate&id='.$object->id.'">'.$langs->trans("Cancel").'</a>';
		 else
		   print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Cancel")."</a>";

		 // if ($user->rights->poa->prev->nul && $object->statut > 0)
		 // 	print "<a class=\"butAction\" href=\"fiche.php?action=anulate&id=".$object->id."\">".$langs->trans("Cancel")."</a>";
		 // else
		 // 	print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Cancel")."</a>";
		 //disminucion del preventivo con autorizacion
		 if ($user->rights->poa->prev->dis && $object->statut > 0)
		   print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=reduc&id='.$object->id.'">'.$langs->trans("Add modify").'</a>';
		 else
		   print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Add modify")."</a>";
		   }
		 elseif($action=='eproduct')
		   {
		 // if ($object->statut == 0)
		 // 	{
		 // 	  if ($user->rights->poa->prev->val && $lValidate)
		 // 	    print "<a class=\"butAction\" href=\"fiche.php?action=validate&id=".$object->id."\">".$langs->trans("Validate")."</a>";
		 // 	  else
		 // 	    print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Validate")."</a>";
		 // 	}
		   }

		 print "</div>";
	  //validacion
		 if ($lValidate && $object->statut == 0 && (empty($action) || $action == 'eproduct'))
		   {
		 $_SESSION['preventive']['id'][$object->id] = $object->id;
		 print select_tables_div($selected,$object->id,'05',$sumaPartida);
		   }
	   }
	 /*
	  * Edition fiche
	  */
	 if (($action == 'edit' || $action == 're-edit') && 1)
	   {
		 print_fiche_titre($langs->trans("ApplicationsEdit"), $mesg);

		 print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
		 print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		 print '<input type="hidden" name="action" value="update">';
		 print '<input type="hidden" name="id" value="'.$object->id.'">';
		 print '<input type="hidden" name="dol_hide_leftmenu" value="1">';

		 print '<table class="border" width="100%">';

		 // pac
		 print '<tr><td>'.$langs->trans('PAC').'</td><td colspan="2">';
		 print $objpac->select_pac($object->fk_pac,'fk_pac','',120,1);
		 print '</td></tr>';

		 //activity
		 print '<tr><td class="fieldrequired">'.$langs->trans('Activity').'</td><td colspan="2">';
		 if (!$user->admin)
		   {
		 if($objact->fetch('',$object->id)>0)
		   {
			 print_r($objact);
			 print $objact->label;
			 print '<input type="hidden" name="fk_activity" value="'.$fk_activity.'"';
		   }
		 else
		   $mesg = '<div class="error">'.$langs->trans("Erroractivityisnull").'</div>';
		   }
		 else
		   {
		 $objact->fetch('',$object->id);
		 print $objact->select_activity((empty($objact->id)?$fk_activity:$objact->id),'fk_activity','',120,1,0,1,''," AND c.gestion = ".$gestion);
		   }
		 print '</td></tr>';

		 // area
		 print '<tr><td class="fieldrequired">'.$langs->trans('Area').'</td><td colspan="2">';
		 print $objarea->select_area($object->fk_area,'fk_area','',120,1);
		 print '</td></tr>';

		 // fk_father
		 $father = '';
		 if (!empty($object_s->fk_father))
		   {
		 $objnew = new Poaprev($db);
		 if ($objnew->fetch($object_s->fk_father)>0)
		   if ($objnew->id == $object_s->fk_father)
			 $father = $objnew->nro_preventive;
		   }
		 print '<tr><td>'.$langs->trans('Preventive top').'</td><td colspan="2">';
		 print '<input id="fk_father" type="text" value="'.$father.'" name="fk_father" size="6" maxlength="4" placeholder="'.$langs->trans('Preventivemain').'">';
		 print '</td></tr>';

		 // gestion
		 print '<tr><td class="fieldrequired">'.$langs->trans('Gestion').'</td><td colspan="2">';
		 print '<input id="gestion" type="text" value="'.(empty($object->gestion)?date('Y'):$object->gestion).'" name="gestion" size="6" maxlength="4">';
		 print '</td></tr>';

		 // label
		 print '<tr><td class="fieldrequired">'.$langs->trans('Name').'</td><td colspan="2">';
		 print '<input id="label" type="text" value="'.$object->label.'" name="label" size="120" maxlength="255">';
		 print '</td></tr>';

		 // pseudonym
		 print '<tr><td>'.$langs->trans('Pseudonym').'</td><td colspan="2">';
		 print '<input id="pseudonym" type="text" value="'.$object->pseudonym.'" name="pseudonym" size="120" maxlength="50">';
		 print '</td></tr>';

		 //nro
		 print '<tr><td class="fieldrequired">'.$langs->trans('Nro').'</td><td colspan="2">';
		 print '<input id="nro_preventive" type="text" value="'.$object->nro_preventive.'" name="nro_preventive" size="15" maxlength="12">';
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
		 $form->select_date($object->date_preventive,'di_','','','',"date",1,1);
		 print '</td></tr>';

		 //continuacion de preventivo gestiones anteriores
		 $nro_preventive_ant = '';
		 $gestion_ant = '';
		 if ($object_s->fk_prev_ant)
		   {
		 $objnew = new Poaprev($db);
		 if ($objnew->fetch($object_s->fk_prev_ant)>0)
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
