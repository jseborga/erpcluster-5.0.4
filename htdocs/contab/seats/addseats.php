<?php
/* Copyright (C) 2001-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2003      Jean-Louis Bergamo   <jlb@j1b.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2004      Christophe Combelles <ccomb@free.fr>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2010-2011 Juanjo Menent        <jmenent@@2byte.es>
 * Copyright (C) 2012      Marcos García         <marcosgdf@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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
 *	    \file       htdocs/contab/seats/addseats.php
 *		\ingroup    banque
 *		\brief      List of details of bank transactions for an account
 */

require('../../main.inc.php');
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
require_once(DOL_DOCUMENT_ROOT."/contab/lib/contab.lib.php");

//asientos
require_once(DOL_DOCUMENT_ROOT."/contab/class/contabseat.class.php");
require_once(DOL_DOCUMENT_ROOT."/contab/class/contabseatdet.class.php");
require_once(DOL_DOCUMENT_ROOT."/contab/class/contabseatflag.class.php");

$langs->load("banks");
$langs->load("categories");
$langs->load("bills");
$langs->load("contab@contab");

$id = (GETPOST('id','int') ? GETPOST('id','int') : GETPOST('account','int'));
$ref = GETPOST('ref','alpha');
$action=GETPOST('action','alpha');
$confirm=GETPOST('confirm','alpha');

$group_seat     = GETPOST('group_seat');
$type_seat      = GETPOST('type_seat');
$history = GETPOST('history');

$lProcesaSeat   = false;

// Security check
$fieldvalue = (! empty($id) ? $id : (! empty($ref) ? $ref :''));
$fieldtype = (! empty($ref) ? 'ref' :'rowid');
if ($user->societe_id) $socid=$user->societe_id;
$result=restrictedArea($user,'banque',$fieldvalue,'bank_account','','',$fieldtype);

$date_seat=dol_mktime(0, 0, 0, $date_seatmonth, $date_seatday, $date_seatyear);
$mesg='';

/*
 * Action
 */
$dateop=-1;

if ($action == 'save' ) && !isset($_POST["cancel"]) && $user->rights->contab->crear)
  {
    $error = 0;
    $aArrAsiento = $_SESSION['aArraySeat'];
    $aArrayDoc   = $_SESSION['aArrayTabdoc'];
    $table_nom   = $_SESSION['table_nom'];

    $date_seat = GETPOST('date_seat');
    $input = 1;
    foreach ((array) $aArrAsiento AS $i => $aAsiento)
      {
	$db->begin();
	$input++;
	//encabezado
	$objContabSeat = new Contabseat($db);
	$objContabSeat->entity = $conf->entity;
	$objContabSeat->date_seat = $date_seat;
	$objContabSeat->lote = '00800';
	$objContabSeat->sblote = '001';
	$objContabSeat->doc = str_pad($input, 6, "0", STR_PAD_LEFT);;
	$objContabSeat->currency = 1;
	$objContabSeat->type_seat = 2;
	$objContabSeat->history = GETPOST('history');
	$objContabSeat->manual = 2;
	$objContabSeat->fk_user_creator = $user->id;
	$objContabSeat->state = 1;
	$fk_seat = $objContabSeat->create($user);
	if ($fk_seat < 0)
	  {
	    echo '<pre>';
	    print_r($objContabSeat);
	    echo '</pre>';
	    print '<hr>'.$error++;
	  }
	else
	  {
	    foreach ((array) $aAsiento AS $j => $aData)
	      {
		$table_nom = $aData['table_nom'];
		$objCSDet = new Contabseatdet($db);
		$objCSDet->fk_contab_seat = $fk_seat;
		$objCSDet->debit_account = $aData['deudor'];
		$objCSDet->credit_account = $aData['acreedor'];
		$objCSDet->amount = $aData['amount'];
		$objCSDet->value02 = 0;
		$objCSDet->value03 = 0;
		$objCSDet->value04 = 0;

		$objCSDet->history = $history;
		$objCSDet->sequence = $j+1;
		if (!empty($objCSDet->debit_account) && 
		    empty($objCSDet->credit_account))
		  $objCSDet->type_seat = 1;
		if (empty($objCSDet->debit_account) && 
		    !empty($objCSDet->credit_account))
		  $objCSDet->type_seat = 2;

		$objCSDet->routines = 'seatbank';
		$objCSDet->date_rate = date('Y-m-d');
		$objCSDet->rate = 1;
		$objCSDet->fk_user_creator = $user->id;
		$objCSDet->fk_date_creator = date('Y-m-d');
		$objCSDet->state = 1;
		$return = $objCSDet->create($user);

		if ($return < 0)
		  {
		    echo '<pre>';
		    print_r($objCSDet);
		    echo '</pre>';
		    print '<hr>errrdet '.$error++;
		  }
	      }
	  }
	$aDoc = $aArrayDoc[$i];
	foreach((array) $aDoc AS $rowid => $valor)
	  {
	    $objFlag = new Contabseatflag($db);
	    $objFlag->entity = $conf->entity;
	    $objFlag->fk_seat = $fk_seat;
	    $objFlag->table_nom = MAIN_DB_PREFIX.$table_nom;
	    $objFlag->table_id = $rowid;
	    $objFlag->tms = date(YmdHis);
	    $objFlag->state = 1;
	    $return = $objFlag->create($user);
	    if ($return < 0)
	      {
		echo '<pre>';
		print_r($objFlag);
		echo '</pre>';
	      $error++;
	      }
	  }
	if (empty($error))
	  {
	    $db->commit();
	  }
	else
	  {
	    $db->rollback();
	  }

      }
    $_SESSION['aArraySeat'] = array();
    $_SESSION['aArrayTabdoc'] = array();
    $_SESSION['table_nom'] = '';
  
    header('Location: '$_SESSION['url_seat']);
  }

llxFooter();

$db->close();
?>
