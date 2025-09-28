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
 *	    \file       htdocs/compta/bank/account.php
 *		\ingroup    banque
 *		\brief      List of details of bank transactions for an account
 */


// $id = (GETPOST('id','int') ? GETPOST('id','int') : GETPOST('account','int'));
// $ref = GETPOST('ref','alpha');
// $action=GETPOST('action','alpha');
// $confirm=GETPOST('confirm','alpha');

// $date_startmonth= GETPOST('date_startmonth');
// $date_startday  = GETPOST('date_startday');
// $date_startyear = GETPOST('date_startyear');
// $date_endmonth  = GETPOST('date_endmonth');
// $date_endday    = GETPOST('date_endday');
// $date_endyear   = GETPOST('date_endyear');
// $group_seat     = GETPOST('group_seat');
// $type_seat      = GETPOST('type_seat');
// $date_seatmonth = GETPOST('date_seatmonth');
// $date_seatday   = GETPOST('date_seatday');
// $date_seatyear  = GETPOST('date_seatyear');
// $history = GETPOST('history');

// $lProcesaSeat   = false;

// // Security check
// $fieldvalue = (! empty($id) ? $id : (! empty($ref) ? $ref :''));
// $fieldtype = (! empty($ref) ? 'ref' :'rowid');
// if ($user->societe_id) $socid=$user->societe_id;
// $result=restrictedArea($user,'banque',$fieldvalue,'bank_account','','',$fieldtype);

// $paiementtype=GETPOST('paiementtype','alpha',3);
// $req_nb=GETPOST("req_nb",'',3);
// $thirdparty=GETPOST("thirdparty",'',3);
// $req_desc=GETPOST("req_desc",'',3);
// $req_debit=GETPOST("req_debit",'',3);
// $req_credit=GETPOST("req_credit",'',3);

// $vline=GETPOST("vline");
// $page=GETPOST('page','int');
// $negpage=GETPOST('negpage','int');
// if ($negpage)
// {
//     $page=$_GET["nbpage"] - $negpage;
//     if ($page > $_GET["nbpage"]) $page = $_GET["nbpage"];
// }

// $date_seat=dol_mktime(0, 0, 0, $date_seatmonth, $date_seatday, $date_seatyear);
// $date_start=dol_mktime(0, 0, 0, $date_startmonth, $date_startday, $date_startyear);
// $date_end=dol_mktime(23, 59, 59, $date_endmonth, $date_endday, $date_endyear);

// if (empty($date_start) || empty($date_end)) // We define date_start and date_end
//   {
//     $date_start=dol_get_first_day($pastmonthyear,$pastmonth,false); $date_end=dol_get_last_day($pastmonthyear,$pastmonth,false);
//   }

// $mesg='';

// $object = new Account($db);
// $form = new Form($db);

// $societestatic=new Societe($db);
// $chargestatic=new ChargeSociales($db);
// $memberstatic=new Adherent($db);
// $paymentstatic=new Paiement($db);
// $paymentsupplierstatic=new PaiementFourn($db);
// $paymentvatstatic=new TVA($db);
// $bankstatic=new Account($db);
// $banklinestatic=new AccountLine($db);
// $facturestatic=new Facture($db);
// $facturefournstatic=new FactureFournisseur($db);
// $contabaccounting=new Contabaccounting($db);
// $deplacementstatic=new Deplacement($db);

/*
 * Action
 */
$dateop=-1;

// if ($action == 'save' ) && !isset($_POST["cancel"]) && $user->rights->contab->crear)
//   {
$error = 0;
$now = dol_now();
$aArrAsiento = $_SESSION['aArraySeat'];
$aArrayDoc   = $_SESSION['aArrayTabdoc'];
$table_nom   = $_SESSION['table_nom'];
$date_seat  = GETPOST('date_seat');
$type_seat  = GETPOST('type_seat');
$codtr = GETPOST('codtr');
$seat_month = GETPOST('seat_month');
if (strlen($seat_month) == 1)
	$seat_month = str_pad($seat_month, 2, "0", STR_PAD_LEFT);

$seat_year  = GETPOST('seat_year');
$lote       = GETPOST('lote');
$sblote     = GETPOST('sblote');
if($type_seat == 1)
	$type_numeric  = $conf->global->CONTAB_TSE_INGRESO;
elseif($type_seat == 2)
	$type_numeric  = $conf->global->CONTAB_TSE_EGRESO;
elseif($type_seat == 3)
	$type_numeric  = $conf->global->CONTAB_TSE_TRASPASO;

$input = 1;
$db->begin();
foreach ((array) $aArrAsiento AS $i => $aAsiento)
{
  	//buscando el sequential
	$objContabSeat = new Contabseatext($db);
	$objContabSeat->get_next_typenumeric($type_seat,$seat_month,$seat_year);
	$sequential = str_pad($objContabSeat->sequential, 10, "0", STR_PAD_LEFT);
	$objContabSeat->get_next_lote($lote,$sblote,$seat_year);
	$doc = str_pad($objContabSeat->doc, 6, "0", STR_PAD_LEFT);
	$input++;
	//encabezado
	$objContabSeat = new Contabseatext($db);
	$objContabSeat->entity = $conf->entity;
	$objContabSeat->date_seat = $date_seat;
	$objContabSeat->codtr = $codtr;
	$objContabSeat->lote = $lote;
	$objContabSeat->sblote = $sblote;
	$objContabSeat->doc = $doc;
	$objContabSeat->ref = $lote.$sblote.$doc;
	$objContabSeat->seat_month = $seat_month;
	$objContabSeat->seat_year = $seat_year;
	$objContabSeat->seaty_year = $seat_year;
	$objContabSeat->type_numeric = $type_numeric;
	$objContabSeat->sequential = $sequential;
	$objContabSeat->type_numeric = rand(1,99);
	$objContabSeat->currency = 2;
	//moneda 1 sus, 2 bs
	$objContabSeat->type_seat = $type_seat;
	//2 ingresos, 1 egresos, 3 traspaso
	$objContabSeat->history = GETPOST('history');
	$objContabSeat->manual = 2;
	$objContabSeat->fk_user_create = $user->id;
	$objContabSeat->fk_user_mod = $user->id;
	$objContabSeat->datec = $now;
	$objContabSeat->datem = $now;
	$objContabSeat->tms = $now;
	$objContabSeat->status = 1;
	$fk_seat = $objContabSeat->create($user);
	if ($fk_seat < 0)
	{
		$error++;
		setEventMessages($objContabSeat->error,$objContabSeat->errors,'errors');
	}
	else
	{
		$sumD = 0;
		$sumC = 0;
		foreach ((array) $aAsiento AS $j => $aData)
		{
	  		//$table_nom = $aData['table_nom'];
			$objCSDet = new Contabseatdetext($db);
			$objCSDet->fk_contab_seat = $fk_seat;
			$objCSDet->debit_account = $aData['deudor'];
			$objCSDet->credit_account = $aData['acreedor'];
			$objCSDet->amount = $aData['amount'];
			$objCSDet->fk_standard_seat = 0;
			$objCSDet->value02 = 0;
			$objCSDet->value03 = 0;
			$objCSDet->value04 = 0;

			$objCSDet->history = $history;
			if ($aData['groupseat'] == 1)
				$objCSDet->history = $aData['label'];
			$objCSDet->sequence = $j+1;
			if (!empty($objCSDet->debit_account) &&
				empty($objCSDet->credit_account))
			{
				$objCSDet->type_seat = 1;
				$sumD += $aData['amount'];
			}
			if (empty($objCSDet->debit_account) &&
				!empty($objCSDet->credit_account))
			{
				$objCSDet->type_seat = 2;
				$sumC += $aData['amount'];
			}
			$objCSDet->routines = 'seatbank';
			$objCSDet->date_rate = $now;
			$objCSDet->rate = 1;
			$objCSDet->fk_user_create = $user->id;
			$objCSDet->fk_user_mod = $user->id;
			$objCSDet->datec = $now;
			$objCSDet->datem = $now;
			$objCSDet->tms = $now;
			$objCSDet->status = 1;
			$return = $objCSDet->create($user);

			if ($return < 0)
			{
				setEventMessages($objCSDet->error,$objCSDet->errors,'errors');
				echo '<pre>';
				print_r($objCSDet);
				echo '</pre>';
				print '<hr>errrdet '.$error++;
			}
		}
	  //actualizando la sumatoria del asiento
		$objContabSeat->fetch($fk_seat);
		if ($objContabSeat->id == $fk_seat)
		{
			$objContabSeat->debit_total = price2num($sumD);
			$objContabSeat->credit_total = price2num($sumC);
			$resup = $objContabSeat->update($user);
			if ($resup<=0)
				setEventMessages($objContabSeat->error,$objContabSeat->errors,'errors');
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
			setEventMessages($objFlag->error,$objFlag->errors,'errors');
			echo '<pre>';
			print_r($objFlag);
			echo '</pre>';
			$error++;
		}
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

$_SESSION['aArraySeat'] = array();
$_SESSION['aArrayTabdoc'] = array();
$_SESSION['table_nom'] = '';

?>
