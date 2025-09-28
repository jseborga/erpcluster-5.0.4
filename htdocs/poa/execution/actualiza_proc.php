<?php
/* Copyright (C) 20143-2014 Ramiro Queso        <ramiro@ubuntu-bo.com>
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

// require_once DOL_DOCUMENT_ROOT.'/poa/reformulated/class/poareformulateddet.class.php';
// require_once DOL_DOCUMENT_ROOT.'/poa/reformulated/class/poareformulated.class.php';

require_once DOL_DOCUMENT_ROOT.'/poa/process/class/poaprocess.class.php';


$langs->load("poa@poa");
$action = GETPOST('action');
$id     = GETPOST('id');

$mesg = '';
$objectx = new Poaprocess($db);
if ($objectx->fetch($id)>0)
  {
    if ($action == 'updateproc')
      {
	$objectx->cuce = $_GET['di_'.$id];
	$objectx->tms = dol_now();
	$res = $objectx->update($user);
      }
    if ($action == 'updatecode')
      {
	$objectx->code_process = $_GET['df_'.$id];
	$objectx->tms = dol_now();
	$res = $objectx->update($user);
      }
  }
?>
