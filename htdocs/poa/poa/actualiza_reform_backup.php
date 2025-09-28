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

$gestion  = GETPOST("gestion");
$id  = GETPOST("id");
$aReform  = GETPOST("reform");
$aReformtext = GETPOST("reformtext");
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
     //creamos
     $obj->entity = $conf->entity;
     $obj->gestion = $gestion;
     $obj->ref = $ref;
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
if (count($aReform) > 0 && $user->rights->poa->refo->crear)
  {
    print_r($aStruct);
    foreach((array) $aReform AS $id => $aStruct)
      {
	foreach((array) $aStruct As $fk_structure => $aPoa)
	  {
	    foreach((array) $aPoa AS $fk_poa_poa => $aPartida)
	      {
		foreach((array) $aPartida AS $partida => $value)
		  {
		    if ($id > 0)
		      {
			//buscamos y reemplazamos
			$object->fetch($id);
			$object->amount = $value;
			$object->update($user);
		      }
		    else
		      {
			if ($value <>0)
			  {
			    //creamos nuevo
			    $object->fk_poa_reformulated = $fk_poa_reformulated;
			    $object->fk_structure = $fk_structure;
			    $object->fk_poa_poa = $fk_poa_poa;
			    $object->partida = $partida;
			    $object->amount = $value;
			    $object->reform->$aReformtext[0][$fk_structure][$fk_poa_poa][$partida];
			    $object->date_create = date('Y-m-d');
			    $object->fk_user_create = $user->id;
			    $object->tms = date('YmdHis');
			    $object->statut = 0;
			    $object->create($user);
			  }
		      }
		  }
	      }
	  }
      }
  }
//$db->close();
?>
