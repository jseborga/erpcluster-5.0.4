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

require_once DOL_DOCUMENT_ROOT.'/poa/process/class/poaprocesscontrat.class.php';
require_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/poa/lib/poa.lib.php';


$langs->load("poa@poa");
$action = GETPOST('action');
$id     = GETPOST('id');
$idc    = GETPOST('idc');

$mesg = '';
$objectx = new Poaprocesscontrat($db);
$objcon = new Contrat($db);
$extrafields = new ExtraFields($db);
$extralabels=$extrafields->fetch_name_optionals_label($objcon->table_element);

$objectx->fetch($idc);
if ($objectx->fetch($idc)>0)
  {
    if ($action == 'updateop')
      {
	$aDate = (stristr($_GET['di_'.$idc],'/')===FALSE?explode('-',$_GET['di_'.$idc]):explode('/',$_GET['di_'.$idc]));
	if (stristr(STRTOUPPER($_SERVER['HTTP_USER_AGENT']),'FIREF')===FALSE)
	  $date_order_proceed = dol_mktime(12, 0, 0, $aDate[1],$aDate[2],$aDate[0]);
	else
	  $date_order_proceed = dol_mktime(12, 0, 0, $aDate[1],$aDate[0],$aDate[2]);

	
	$objectx->date_order_proceed = $date_order_proceed;
	$objectx->tms = dol_now();
	$res = $objectx->update($user);
	//actualizando en contrat
	$comment = $langs->trans('Orden de Proceder o Firma de contrato');
	if ($objcon->fetch($objectx->fk_contrat)>0)
	  {
	    $cod_plazo = $objcon->array_options['options_cod_plazo'];
	    $plazo = $objcon->array_options['options_plazo'];
	    $datefin = date_end($date_order_proceed,$cod_plazo,$plazo);
	    $objcon->fetch_lines();
	    foreach ((array) $objcon->lines AS $i => $objl)
	      {
		$objcon->active_line($user,$objl->id,$date_order_proceed,$datefin,$comment);
	      }
	  }
      }
    if ($action == 'updatedp')
      {
	$aDate = (stristr($_GET['dp_'.$idc],'/')===FALSE?explode('-',$_GET['dp_'.$idc]):explode('/',$_GET['dp_'.$idc]));
	
	if (stristr(STRTOUPPER($_SERVER['HTTP_USER_AGENT']),'FIREF')===FALSE)
	  $date_provisional = dol_mktime(12, 0, 0, $aDate[1],$aDate[2],$aDate[0]);
	else
	  $date_provisional = dol_mktime(12, 0, 0, $aDate[1],$aDate[0],$aDate[2]);

	$objectx->date_provisional = $date_provisional;
	$objectx->tms = dol_now();
	$res = $objectx->update($user);
	//actualizando en contrat recpecion provisional
	$comment = $langs->trans('Recepcion provisional');
	if ($objcon->fetch($objectx->fk_contrat)>0)
	  {
	    $objcon->fetch_lines();
	    foreach ((array) $objcon->lines AS $i => $objl)
	      {
		$objcon->close_line($user,$objl->id,$date_provisional,$comment);
	      }
	  }	
      }
    if ($action == 'updatedf')
      {
	$aDate = (stristr($_GET['df_'.$idc],'/')===FALSE?explode('-',$_GET['df_'.$idc]):explode('/',$_GET['df_'.$idc]));
	
	if (stristr(STRTOUPPER($_SERVER['HTTP_USER_AGENT']),'FIREF')===FALSE)
	  $date_final = dol_mktime(12, 0, 0, $aDate[1],$aDate[2],$aDate[0]);
	else
	  $date_final = dol_mktime(12, 0, 0, $aDate[1],$aDate[0],$aDate[2]);

	$objectx->date_final = $date_final;
	$objectx->tms = dol_now();
	$res = $objectx->update($user);
	//actualizando en contrat recpecion definitiva
	$comment = $langs->trans('Recepcion definitiva');
	if ($objcon->fetch($objectx->fk_contrat)>0)
	  {
	    $objcon->fetch_lines();
	    foreach ((array) $objcon->lines AS $i => $objl)
	      {
		$objcon->close_line($user,$objl->id,$date_final,$comment);
	      }
	  }
      }
    if ($action == 'updatedn')
      {
	$aDate = (stristr($_GET['dn_'.$idc],'/')===FALSE?explode('-',$_GET['dn_'.$idc]):explode('/',$_GET['dn_'.$idc]));
	
	if (stristr(STRTOUPPER($_SERVER['HTTP_USER_AGENT']),'FIREF')===FALSE)
	  $date_nonconformity = dol_mktime(12, 0, 0, $aDate[1],$aDate[2],$aDate[0]);
	else
	  $date_nonconformity = dol_mktime(12, 0, 0, $aDate[1],$aDate[0],$aDate[2]);
	if ($objectx->date_final > 0 || $objectx->date_provisional > 0)
	  {
	    print $mesg.='<div class="error">'.$langs->trans("Errortheprovisionalandfinaldatemustbeblank").'</div>';
	  }
	else
	  {
	    $objectx->date_nonconformity = $date_nonconformity;
	    $objectx->tms = dol_now();
	    $res = $objectx->update($user);
	  }
      }
    if ($action == 'updatenc')
      {
	$objectx->motif = $_GET['motif_'.$idc];
	$objectx->tms = dol_now();
	$res = $objectx->update($user);
      }
  }
?>
