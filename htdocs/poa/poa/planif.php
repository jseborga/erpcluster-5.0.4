<?php
/* Copyright (C) 2015-2015 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *	\file       htdocs/poa/poa/planif.php
 *	\ingroup    poa
 *	\brief      Page fiche poa actualizacion de planificacion meta
 */

require("../../main.inc.php");

require_once DOL_DOCUMENT_ROOT.'/poa/structure/class/poastructurepl.class.php';

$langs->load("poa@poa");

$action  = GETPOST('action'); //action
$gestion = GETPOST('gestion');
$month   = GETPOST('month');
$value   = GETPOST('val'); //value
$id      = GETPOST('id'); //idactivity
$fkid    = GETPOST('fkid');
$object  = new Poastructurepl($db);
echo $action.' id '.$id;

/*
 * Actions
 */
//refo
if ($action =='add')
  {
    //buscamos la existencia del registro para el usuario activo
    $res = $object->fetch($id);
    if ($res>0)
      if ($object->id == $id)
	$action = 'update';
      else
	$action = 'create';
    else
      $action = 'create';
    if ($action == 'update' && $id && $user->rights->poa->poa->mod)
      {
	$object->quant = $value;
	$object->tms = dol_now();
	if (empty($object->quant))
	  $error++;
	if (empty($error))
	  $result = $object->update($user);
      }
    if ($action == 'create' && $user->rights->poa->poa->crear)
      {
	$object->fk_structure = $fkid;
	$object->kind = 'PLAN';
	$object->gestion = $gestion;
	$object->tmonth = $month;
	$object->quant = $value;
	$object->fk_user_create = $user->id;
	$object->date_create = dol_now();
	$object->tms = dol_now();
	$object->statut = 1;
	$object->active = 1;
	if (empty($object->quant))
	  $error++;
	if (empty($error))
	  $result = $object->create($user);
	if ($result<=0)
	  {
	    print $mesg = '<div class="error">'.$object->error.'</div>';
	  }
      }
    
  }
?>
