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
require_once DOL_DOCUMENT_ROOT.'/poa/process/class/poaprocess.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/pac/class/poapac.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/poa/class/poapoa.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/structure/class/poastructure.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/area/class/poaarea.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/area/class/poaareauser.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidapre.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidapredet.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/lib/poa.lib.php';

require_once DOL_DOCUMENT_ROOT.'/poa/class/poapreemail.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

$langs->load("poa@poa");

$action=GETPOST('action');

$id        = GETPOST("id");
$idp       = GETPOST("idp");
$idpp      = GETPOST("idpp");
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");

$mesg = '';


$object  = new Poaprev($db);
$objuser = new User($db);
$objpac  = new Poapac($db);
$objpoa  = new Poapoa($db);
$objarea = new Poaarea($db);
$objareauser = new Poaareauser($db);
$objprev = new Poapartidapre($db);
$objprevdet = new Poapartidapredet($db);
$objstr  = new Poastructure($db);
$aExcludeArea = array();
$idFather = 0;

//conectando a otra base de datos
// $dbtype='mysql';
// $dbhost='localhost';
// $dbuser='root';
// $dbpass = 'dsoGmlp123';
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

    $object->fk_pac          = GETPOST('fk_pac');
    $object->fk_area         = GETPOST('fk_area');
    $object->nro_preventive  = GETPOST('nro_preventive');
    $object->priority        = GETPOST('priority');
    $object->code_requirement= GETPOST('code_requirement');
    $object->date_preventive = $date_preventive;
    $object->fk_user_create  = GETPOST('fk_user_create');
    $object->label           = GETPOST('label');
    $object->pseudonym       = GETPOST('pseudonym');

    //puscamos el pac
    // if ($object->fk_pac > 0)
    //   {
    // 	$objpac->fetch($object->fk_pac);
    // 	if ($objpac->id == $object->fk_pac)
    // 	  {
    // 	    $object->label = $objpac->label;
    // 	  }
    // 	else
    // 	  {
    // 	    $error++;
    // 	    $mesg.='<div class="error">'.$langs->trans("Errorpacisrequired").'</div>';	
    // 	  }
    //   }
    // else
    //   {
    // 	$object->label = GETPOST('label');
	if (empty($object->label))
	  {
	    $error++;
	    $mesg.='<div class="error">'.$langs->trans("Errorlabelisrequired").'</div>';	
	  }
      // }
    $object->date_create = dol_now();
    $object->tms = date('YmdHis');
    if ($object->fk_user_create <= 0)
      $object->fk_user_create = $user->id;
    $object->amount = 0;
    $object->entity = $conf->entity;
    $object->statut     = 0;
    $object->active = 1;

    if (empty($object->nro_preventive))
      {
	$error++;
	$mesg.='<div class="error">'.$langs->trans("Errornropreventiveisrequired").'</div>';
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
	if ($error)
	  $action="create";   // Force retour sur page creation
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
	    header("Location: fiche.php?id=".$id);
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
		header("Location: fiche.php?id=".$id);
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
    header("Location: fiche.php?id=".$id);
    exit;
  }

// Addpartidaprod
if ($action == 'addpartidaprod' && $user->rights->poa->prev->crear)
  {
    $error = 0;

    $objprevdet->fk_poa_partida_pre = GETPOST('idp');
    $objprevdet->detail       = GETPOST('detail');
    $objprevdet->quant = GETPOST('quant');
    $objprevdet->fk_product = 0;
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
	    header("Location: fiche.php?id=".$id
		   ."&idp=".$_POST['idp'].
		   "&action=editproduct");
	    exit;
	  }
	$action = 'editproduct';
	$_GET['idp'] = $_POST['idp'];
	$mesg='<div class="error">'.$objprevdet->error.'</div>';
      }
    else
      {
	if ($error)
	  {
	    $action="editproduct";   // Force retour sur page creation
	    $_GET['idp'] = $_POST['idp'];
	  }
      }
  }


// Add
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
	    header("Location: fiche.php?id=".$id);
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

// Cancel preventive
if ($action == 'confirm_cancel' && $_REQUEST["confirm"] == 'yes' && $user->rights->poa->prev->nul)
{
  $object->fetch($_REQUEST["id"]);
  //verificamos que no tenga proceso iniciado
  $objproc = new Poaprocess($db);
  $objproc->fetch_prev($_REQUEST["id"]);
  if ($objproc->fk_poa_prev == $object->id && $objproc->statut != -1)
    {
      $mesg='<div class="error">'.$langs->trans('Has boot process, delete the startup process').'</div>';
      $action='';
    }
  else
    {
      $object->statut = -1;
      $result=$object->update($user);
      if ($result > 0)
	{
	  header("Location: ".DOL_URL_ROOT.'/poa/execution/liste.php');
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

    if ($object->fetch($_POST["id"]))
      {
	$date_preventive = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
	
	$object->gestion         = GETPOST('gestion');
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

	$object->fk_pac          = GETPOST('fk_pac');
	$object->fk_area         = GETPOST('fk_area');
	$object->nro_preventive  = GETPOST('nro_preventive');
	$object->priority        = GETPOST('priority');
	$object->code_requirement= GETPOST('code_requirement');
	$object->date_preventive = $date_preventive;
	$object->fk_user_create  = GETPOST('fk_user_create');
	$object->label           = GETPOST('label');
	$object->pseudonym       = GETPOST('pseudonym');
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
    $tmparray['fk_pac'] = GETPOST('fk_pac');
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

$help_url='EN:Module_Poa_En|FR:Module_Poa|ES:M&oacute;dulo_Poa';
llxHeader("",$langs->trans("Preventive"),$help_url);

if ($action == 'create' && $user->rights->poa->prev->crear)
  {
    print_fiche_titre($langs->trans("Newpreventive"));
  
  print "\n".'<script type="text/javascript" language="javascript">';
  print '$(document).ready(function () {
              $("#selectfk_pac").change(function() {
                document.form_fiche.action.value="createedit";
                document.form_fiche.submit();
              });
          });';
  print '</script>'."\n";

    print '<form name="form_fiche" action="fiche.php" method="post">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="add">';
    
    dol_htmloutput_mesg($mesg);

    print '<table class="border" width="100%">';

    // pac

    print '<tr><td>'.$langs->trans('PAC').'</td><td colspan="2">';
    print $objpac->select_pac($object->fk_pac,'fk_pac','',120,1);
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
      print $objarea->select_area((empty($object->fk_area)?$idFather:$object->fk_area),'fk_area','',120,1);
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
      print $form->select_dolusers((empty($object->fk_user_create)?$user->id:$object->fk_user_create),'fk_user_create',1,$exclude,0,'','',$object->entity);
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

	  $linkback = '<a href="'.DOL_URL_ROOT.'/poa/execution/liste.php">'.$langs->trans("BackToList").'</a>';
	  
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
	  if ($objnew->fetch($object->fk_father)>0)
	    if ($objnew->id == $object->fk_father)
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
	  
	  //continuacion de preventivo gestiones anteriores
	  print '<tr><td>'.$langs->trans('Preventivemainlast').'</td><td colspan="2">';
	  if ($object->fk_prev_ant)
	    {
	      $objnew = new Poaprev($db);
	      if ($objnew->fetch($object->fk_prev_ant)>0)
		{
		  $nro_preventive_ant = $objnew->nro_preventive;
		  $gestion_ant = $objnew->gestion;
		  $label_ant = $objnew->label;
		  print $nro_preventive_ant.'-'.$gestion_ant.': '.$label_ant;
		}
	      else
		{
		  print $langs->trans('No defined').' '.$object->fk_prev_ant;
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

	  if ($object->statut == 0)
	    {
	      include_once DOL_DOCUMENT_ROOT.'/poa/execution/tpl/form.tpl.php';
	  //     print "\n".'<script type="text/javascript" language="javascript">';
	  //     print '$(document).ready(function () {
          //     $("#selectfk_poa").change(function() {
          //       document.form_meta.action.value="createeditpar";
          //       document.form_meta.submit();
          //     });
          // });';
	  //     print '</script>'."\n";

	  //     print '<form name="form_meta" action="fiche.php" method="POST">';
	  //     print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	  //     print '<input type="hidden" name="action" value="addpartida">';
	  //     print '<input type="hidden" name="id" value="'.$object->id.'">';

	  //     print '<tr>';
	  //     // poa
	  //     print '<td>';
	  //     print $objpoa->select_poa((empty($objprev->fk_poa)?$objpac->fk_poa:$objprev->fk_poa),'fk_poa','',75,1,$objprev->fk_structure);
	  //     print '</td>';
	  //     //buscamos el poa
	  //     $objpoasearch = new Poapoa($db);
	  //     $objpoasearch->fetch((empty($objprev->fk_poa)?$objpac->fk_poa:$objprev->fk_poa));
	  //     // structure
	  //     print '<td width="120">';
	  //     print $objstr->select_structure($objpoasearch->fk_structure,'fk_structure','',3,1,3);
	  //     print '</td>';
	  //     $gestion = date('Y');
	  //     // partida
	  //     $objpoasearch->get_partida($objpoasearch->fk_structure,$object->gestion);
	  //     print '<td>';
	  //     print $form->selectarray('partida',$objpoasearch->array,$objpoasearch->partida);
	  //     // print '<input id="partida" type="text" value="'.$objprev->partida.'" name="partida" size="12" maxlength="10">';
	  //     print '</td>';
	      
	  //     // amount
	  //     print '<td>';
	  //     print '<input id="amount" type="number" step="0.01" value="'.$objprev->amount.'" name="amount" size="12" maxlength="12">';
	  //     print '</td>';
	      
	  //     print '<td align="right">';
	  //     print '<input type="image" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/poa/img/save.png" width="14" height="14">';
	  //     print '</td>';
	  //     print '</tr>';
	      
	  //     print '</form>';
	    }

	  //definimos array para saldos
	  $aPrev = array();
	  //listado

	  $objprev->getlist($object->id);
	  if (count($objprev->array) > 0)
	    {
	      $var = true;
	      foreach ($objprev->array AS $j => $objpartidapre)
		{
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
		      if ($object->statut == 0)
			{
			  print '<a href="" alt="'.$langs->trans('Edit').'">'.img_picto($langs->trans('Edit'),'edit.png').'</a>';
			  print '&nbsp;';
			}
		      print '<a href="fiche.php?id='.$id.'&idp='.$objpartidapre->id.'&action=editproduct" alt="'.$langs->trans('Product').'">'.img_picto($langs->trans('Product'),DOL_URL_ROOT.'/poa/img/product.png','',1).'</a>';
		      if ($object->statut == 0 && $user->rights->poa->prev->delit)
			{
			  print '&nbsp;';
			  print '<a href="fiche.php?id='.$id.'&idp='.$objpartidapre->id.'&action=delpartida" alt="'.$langs->trans('Delete').'">'.img_picto($langs->trans('Delete'),'delete').'</a>';
			  
			}
		      if ($user->admin && $user->rights->poa->prev->crear)
			{
			  print '&nbsp;';
			  print '<a href="fiche.php?id='.$id.'&idp='.$objpartidapre->id.'&action=editpartida" alt="'.$langs->trans('Edit').'">'.img_picto($langs->trans('Edit'),'edit').'</a>';
			  
			}
		      
		      print '</td>';
		      print '</tr>';
		      if ($action == 'editproduct' && $_GET['idp'] == $objpartidapre->id)
			{
			  //editamos el registro de productos
			  print '<tr class="liste_titre">';
			  print '<td colspan="2">'.$langs->trans("Product",$cursorline).'</td>';
			  print '<td colspan="2" align="right">'.$langs->trans("Quant").'</td>';
			  print '<td align="right">'.$langs->trans("Action").'</td>';
			  print '</tr>';
			  if ($object->statut == 0)
			    {
			      //registro nuevo	  
			      print '<form action="fiche.php" method="POST">';
			      print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			      print '<input type="hidden" name="action" value="addpartidaprod">';
			      print '<input type="hidden" name="id" value="'.$object->id.'">';
			      print '<input type="hidden" name="idp" value="'.$objpartidapre->id.'">';
			      
			      print '<tr>';
			      
			      // producto
			      print '<td colspan="2">';
			      print '<input id="detail" type="text" value="'.$objprevdet->detail.'" name="detail" size="70" maxlength="255">';
			      print '</td>';
			      
			      // Quant
			      print '<td colspan="2"  align="right">';
			      print '<input id="quant" type="number" value="'.$objprevdet->quant.'" name="quant" size="12" maxlength="12">';
			      print '</td>';
			      
			      print '<td align="right">';
			      print '<input type="image" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/poa/img/save.png" width="14" height="14">';
			      print '</td>';
			      print '</tr>';
			      
			      print '</form>';
			    }
			  //listando
			  $objprevdet->getlist($objpartidapre->id,'N');
			  foreach((array) $objprevdet->array AS $k => $objprevpro)
			    {
			      $var=!$var;
			      print "<tr $bc[$var]>";
			      // producto
			      print '<td colspan="2">';
			      print $objprevpro->detail;
			      print '</td>';
			      
			      // Quant
			      print '<td colspan="2"  align="right">';
			      print price2num($objprevpro->quant,'MT');
			      print '</td>';
			      
			      print '<td align="right">';
			      if ($object->statut == 0)
				print '<a href="">'.img_picto($langs->trans('Delete'),'delete.png').'</a>';
			      else
				print '&nbsp;';
			      print '</td>';
			      print '</tr>';
			      
			    }
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
		      if ($object->statut == 1)
			{
			  print '<a href="" alt="'.$langs->trans('Edit').'">'.img_picto($langs->trans('Edit'),'edit.png').'</a>';
			  print '&nbsp;';
			  print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idpp='.$objpartidapre->id.'&action=deletemod" alt="'.$langs->trans('Delete').'">'.img_picto($langs->trans('Delete'),'delete.png').'</a>';
			  
			}
		      
		      print '</td>';
		      print '</tr>';
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
	      print '<form action="fiche.php" method="POST">';
	      print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	      print '<input type="hidden" name="action" value="addmodify">';
	      print '<input type="hidden" name="id" value="'.$object->id.'">';

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
			  print '<input type="number" min="1" max="'.
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
	  
	  if ($action == '')
	    {
	      if ($user->rights->poa->prev->crear)
		print "<a class=\"butAction\" href=\"fiche.php?action=create\">".$langs->trans("Createnew")."</a>";
	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Createnew")."</a>";

	      //aumentar la verificacion del estadod 
	      if ($user->admin ||
		  ($user->rights->poa->prev->mod && $object->fk_user_create == $user->id))
		print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$object->id."\">".$langs->trans("Modify")."</a>";
	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";
	      
	      if ($user->rights->poa->prev->del && $object->statut == 0)
		print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$object->id."\">".$langs->trans("Delete")."</a>";
	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
	      if ($user->rights->poa->prev->val && $object->statut == 0)
		print "<a class=\"butAction\" href=\"fiche.php?action=validate&id=".$object->id."\">".$langs->trans("Validate")."</a>";
	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Validate")."</a>";

	      if ($user->rights->poa->prev->nul && $object->statut > 0)
		print "<a class=\"butAction\" href=\"fiche.php?action=anulate&id=".$object->id."\">".$langs->trans("Cancel")."</a>";
	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Cancel")."</a>";

	      // if ($user->rights->poa->prev->nul && $object->statut > 0)
	      // 	print "<a class=\"butAction\" href=\"fiche.php?action=anulate&id=".$object->id."\">".$langs->trans("Cancel")."</a>";
	      // else
	      // 	print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Cancel")."</a>";
	      //disminucion del preventivo con autorizacion
	      if ($user->rights->poa->prev->dis && $object->statut > 0)
		print "<a class=\"butAction\" href=\"fiche.php?action=reduc&id=".$object->id."\">".$langs->trans("Add modify")."</a>";
	      else
		print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Add modify")."</a>";

	      
	    }	  
	  elseif($action=='editproduct')
	    {
	      if ($object->statut == 0)
		{
		  if ($user->rights->poa->prev->val)
		    print "<a class=\"butAction\" href=\"fiche.php?action=validate&id=".$object->id."\">".$langs->trans("Validate")."</a>";
		  else
		    print "<a class=\"butActionValidate\" href=\"#\">".$langs->trans("Validate")."</a>";
		}
	    }
	  print "</div>";		
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

	  // pac
	  print '<tr><td>'.$langs->trans('PAC').'</td><td colspan="2">';
	  print $objpac->select_pac($object->fk_pac,'fk_pac','',120,1);
	  print '</td></tr>';

	  // area
	  print '<tr><td class="fieldrequired">'.$langs->trans('Area').'</td><td colspan="2">';
	  print $objarea->select_area($object->fk_area,'fk_area','',120,1);
	  print '</td></tr>';

	  // fk_father
	  $father = '';
	  if (!empty($object->fk_father))
	    {
	      $objnew = new Poaprev($db);
	      if ($objnew->fetch($object->fk_father)>0)
		if ($objnew->id == $object->fk_father)
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
