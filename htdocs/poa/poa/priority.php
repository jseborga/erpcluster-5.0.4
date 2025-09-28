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
 *	\file       htdocs/poa/poa/actualiza_reform.php
 *	\ingroup    poa
 *	\brief      Page fiche poa actualizacion reform
 */

require("../../main.inc.php");

require_once DOL_DOCUMENT_ROOT.'/poa/activity/class/poaactivity.class.php';

$langs->load("poa@poa");

$action = GETPOST('action'); //action
$ctx    = GETPOST('ctx'); //value
$id     = GETPOST('id'); //idactivity
$object  = new Poaactivity($db);

/*
 * Actions
 */
//refo
if ($action =='add')
  {
    //buscamos la existencia del registro para el usuario activo
    if ($object->fetch($id)>0)      
      $action = 'update';
    if ($action == 'update' && $id && $user->rights->poa->act->mod)
      {
	$object->priority = $ctx;
	$object->tms = dol_now();
	if (empty($object->priority))
	  $error++;
	if (empty($error))
	  $result = $object->update($user);
      }
  }
?>
