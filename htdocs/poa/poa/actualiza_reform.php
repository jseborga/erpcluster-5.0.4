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

require_once DOL_DOCUMENT_ROOT.'/poa/reformulated/class/poareformulateddet.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/reformulated/class/poareformulated.class.php';

$langs->load("poa@poa");

$action=GETPOST('action');

$id           = GETPOST('id');
$fk_structure = GETPOST('fk_structure');
$fk_poa_poa   = GETPOST('fk_poa_poa');
$partida      = GETPOST('partida');
$amount       = GETPOST('amount');
$gestion      = GETPOST('gestion');
$reform       = GETPOST('reform');

$mesg = '';
$object  = new Poareformulateddet($db);
$obj     = new Poareformulated($db);

$obj->fetch_version($gestion);
$fk_poa_reformulated = 0;

if ($obj->gestion == $gestion)
  {
    $fk_poa_reformulated = $obj->id;
  }
 else
   {
     $objap = new Poareformulated($db);
     $objap->fetch_version($gestion,1);
     if ($objap->gestion == $gestion)
       $version = $objap->version + 1;
     else
       $version = 1;
     
     //creamos
     $obj->entity = $conf->entity;
     $obj->gestion = $gestion;
     $obj->ref = $ref; //revisar
     $obj->date_reform = date('Y-m-d');
     $obj->version = $version;
     $obj->fk_user_create = $user->id;
     $obj->date_crate = date('Y-m-d');
     $obj->tms = date('YmdHis');
     $obj->statut = 0;
     $obj->active = 1;
     $fk_poa_reformulated = $obj->create($user);
     
}
/*
 * Actions
 */
//refo
if ($action == 'create' && $user->rights->poa->refo->crear)
  {
    //buscando
    $object->fetch($id);
    if ($object->id == $id)
      {
	//actualizamos
	$object->reform = $reform;
	$object->amount = $amount;
	$object->update($user);
      }
    elseif(empty($id))
      {
	//creamos
	//creamos nuevo
	$object->fk_poa_reformulated = $fk_poa_reformulated;
	$object->fk_structure = $fk_structure;
	$object->fk_poa_poa = $fk_poa_poa;
	$object->partida = $partida;
	$object->amount = $amount;
	$object->reform = $reform;
	$object->date_create = date('Y-m-d');
	$object->fk_user_create = $user->id;
	$object->tms = date('YmdHis');
	$object->statut = 0;
	$object->create($user);	
      }
  }
//$db->close();
?>
