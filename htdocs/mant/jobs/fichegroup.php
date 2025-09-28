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
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/mant/request/class/mworkrequest.class.php';

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

$object      = new Mjobs($db);
$objwork     = new Mworkrequest($db);
$objProperty = new Mproperty($db);
$objLocation = new Mlocation($db);
$objSoc      = new Societe($db);
$objEquipment= new Mequipment($db);
$objUser     = new User($db);
$objJobsuser = new Mjobsuser($db);
if ($conf->assets->enabled)
  $objassets = new Assets($db);


/*
 * Actions
 */


// Addgroup
if ($action == 'addgroup' && $user->rights->mant->jobs->crear)
  {
    $error=0;
    $date_create  = dol_now();
    $db->begin();

    foreach ((array) $_SESSION['seletick'] AS $idw => $value)
      {
	$result = $objwork->fetch($idw);

	$code= generarcodigo(4);
	$object->initAsSpecimen();
	$object->address_ip     = $_SERVER['REMOTE_ADDR'];
	$object->ref            = '(PROV)'.$code;
	$object->fk_member      = $objwork->fk_member;
	$object->entity         = $conf->entity;
	$object->fk_charge      = 0;
	$object->fk_departament = 0;
	$object->fk_equipment   = 0;
	$object->fk_work_request= $idw;
	$object->internal       = $objwork->internal;
	$object->fk_property    = $objwork->fk_property;
	$object->fk_location    = $objwork->fk_location;
	$object->speciality     = GETPOST("speciality");
	$object->detail_problem = $objwork->detail_problem;
	$object->date_create    = $date_create;
	$object->fk_user_assign = 0;
	$object->fk_user_equipment_prog = 0;
	$object->fk_equipment_prog = 0;
	$object->fk_property_prog = 0;
	$object->fk_location_prog = 0;
	$object->fk_user_prog = 0;
	$object->group_task = 0;
	$object->task = 0;

	$object->fk_soc         = GETPOST('fk_soc')+0;
	$object->statut         = 0;
	$object->statut_job = 0;
	//buscamos el correo del usuario seleccionado
	if (empty($object->speciality))
	  {
	    $error++;
	    $mesg.='<div class="error">'.$langs->trans('Error, speciality is required').'</div>';
	  }

	if ($object->fk_soc <= 0)
	  {
	    $error++;
	    $mesg.='<div class="error">'.$langs->trans('Error, fk_soc is required').'</div>';
	  }
	if (!empty($objwork->email))
	  $object->email = $objwork->email;
	if ($objwork->fk_member)
	  {
	    $objAdherent = new Adherent($db);
	    $objAdherent->fetch($objwork->fk_member);
	    if ($objAdherent->id == $objwork->fk_member)
	      {
		$object->fk_member = $objpwork->fk_member;
		$object->email = $objAdherent->email;
	      }
	    else
	      {
		$error++;
		$mesg.='<div class="error">'.$langs->trans('Error, user is required').'</div>';
	      }
	  }
	else
	  {
	    $error++;
	    $mesg.='<div class="error">'.$langs->trans('Error, user or email is required').'</div>';
	  }
	if (empty($error)) 
	  {
	    $id = $object->create($user);
	    if ($id > 0)
	      {
		//asignamos directamente el numero de ticket
		$objnew = new Mjobs($db);
		$objnew->fetch($id);
		$ref = substr($objnew->ref, 1, 4);
		if ($ref == 'PROV')
		  {
		    $numref = $object->getNextNumRef($object);
		  }
		else
		  {
		    $error++;
		    $numref = $object->ref;
		    $mesg.= '<div class="error">'.$langs->trans('Error, la orden de trabajo tiene numero').' '.$numref.'</div>';
		  }
		$objnew->ref = $numref;
		$objnew->statut = 2;
		$res = $objnew->update($user);
		if ($res <=0)
		  $error++;
	      }
	    else
	      {
		$error++;
		$mesg.= '<div class="error">'.$object->error.'</div>';
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
	header("Location: ".DOL_URL_ROOT."/mant/request/liste.php");
	exit;
      }
    else
      { 
	$db->rollback();
	$mesg.= '<div class="error">'.$langs->trans('Error, no se pudo crear las ordenes de trabajo, revise').'</div>';
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
    $aseletick = $_SESSION['seletick'];
    
    //verificamos si biene de work request
    if (!empty($aseletick))
      {
	print_fiche_titre($langs->trans("Newworkorders"));
	if (empty($object->ref)) $object->ref = '(PROV)';
	
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
