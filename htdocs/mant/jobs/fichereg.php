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
define("NOLOGIN",1);
define("NOCSRFCHECK",1);

$entity=(! empty($_GET['entity']) ? (int) $_GET['entity'] : (! empty($_POST['entity']) ? (int) $_POST['entity'] : 1));
if (is_int($entity)) define("DOLENTITY", $entity);

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


$langs->load("mant@mant");

$action=GETPOST('action');

$id        = GETPOST("rowid");


$mesg = '';

$object = new Mjobs($db);
$objCharge = new Pcharge($db);
$objDepartament = new Pdepartament($db);
/*
 * Actions
 */
echo 'regggg';
// Add
if ($action == 'updateemail')
  {
    $object->fetch($id);
    $token = GETPOST('token');
    if ($object->statut == 0)
      {
	$object->address_ip     = $_SERVER['REMOTE_ADDR'];
	$object->fk_member      = GETPOST("fk_member");
	//$object->entity         = $entity;
	$object->fk_charge      = GETPOST("fk_charge");
	$object->fk_departament = GETPOST("fk_departament");
	$object->speciality     = GETPOST("speciality");
	$object->statut         = 1;
	if ($object->fk_charge && $object->speciality)
	  {
	    $id = $object->update($user);
	    if ($id > 0)
	      {
		print '<div>registro concluido, espere la notificacion hasta que se designe al tecnico. gracias.</div>';
		exit;
	      }
	    else
	      {
		print '<div>Error, falla en el registro, favor ejecutar nuevamente. gracias.</div>';
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
    else
      {
	print '<div>El registro ya fue realizado. gracias.</div>';
      }
  }

llxFooter();

$db->close();


?>
