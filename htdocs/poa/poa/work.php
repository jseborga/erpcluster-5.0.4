<?php
/* Copyright (C) 20145-2015 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *	\file       htdocs/poa/poa/actualiza_reform.php
 *	\ingroup    poa
 *	\brief      Page fiche poa actualizacion reform
 */

require("../../main.inc.php");

require_once DOL_DOCUMENT_ROOT.'/poa/activity/class/poaactivitywork.class.php';

$langs->load("poa@poa");

$action = GETPOST('action'); //action
$n      = GETPOST('n'); //nro work
$ctx    = GETPOST('ctx'); //value
$id     = GETPOST('id'); //idactivity
$campo = 't'.$n;
$object  = new Poaactivitywork($db);

/*
 * Actions
 */
//refo
if ($action =='add')
  {
    //buscamos la existencia del registro para el usuario activo
    if ($object->fetch_users($id,$user->id)>0)
      $action = 'update';
    if ($object->fetch_users($id,$user->id)==0)
      $action = 'create';
    if ($action == 'create' && $id && $user->rights->poa->work->crear)
      {
	//creando
	$object->fk_activity = $id;
	$object->fk_user = $user->id;
	$object->$campo = $ctx;
	$object->date_create = dol_now();
	$object->tms = dol_now();
	$object->fk_user_create = $user->id;
	$object->statut = 1;
	if (empty($object->$campo))
	  $error++;
	if (empty($error))
	  $result = $object->create($user);
      }
    if ($action == 'update' && $id && $user->rights->poa->work->crear)
      {
	$object->$campo = $ctx;
	$object->tms = dol_now();
	if (empty($object->$campo))
	  $error++;
	if (empty($error))
	  $result = $object->update($user);
      }

  }
?>
