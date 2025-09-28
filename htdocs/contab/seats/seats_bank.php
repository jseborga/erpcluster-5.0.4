<?php
/* Copyright (C) 2013-2013 Ramiro Queso        <ramiro@ubuntu-bo.com>
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
 *      \file       htdocs/contab/seat/seats_bank.php
 *      \ingroup    Contab asientos banco
 *      \brief      Page liste genera asientos bancos
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/contab/class/contabseat.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/commonobject_.class.php");
require_once(DOL_DOCUMENT_ROOT."/contab/lib/contab.lib.php");
require_once(DOL_DOCUMENT_ROOT."/contab/lib/seats.lib.php");

require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabaccounting.class.php';

require_once DOL_DOCUMENT_ROOT.'/contab/class/contabseat.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabseatdet.class.php';
require_once DOL_DOCUMENT_ROOT.'/ventas/bank/class/bankspending.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabspendingaccount.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/deplacement/class/deplacement.class.php';


$langs->load("contab@contab");

if (!$user->rights->contab->leerseatma)
  accessforbidden();

$aArraySeat = seat_bank();
foreach ((array) $aArraySeat AS $i => $data)
{
  $aArrayNew = array();
  $sequen = 1;
  $sumDebe = 0;
  $sumCred = 0;
  $nCountData = count($data);
  foreach ((array) $data AS $rowid => $objp)
    {
      if ($sequen == 1)
	{
	  echo '<br>grupo '.$i.' rowid '.$rowid.' cuenta '.$objp->fk_account;
	  $objSeat = new Contabseat($db);
	  $objSeat->entity = $conf->entity;
	  $objSeat->date_seat = $objp->datec;
	  $objSeat->lote = '(PROV)';
	  $objSeat->sblote = '(PROV)';
	  $objSeat->doc = '(PROV)';
	  $objSeat->currency = '12222';
	  $objSeat->type_seat = 2;
	  $objSeat->debit_total = 0;
	  $objSeat->credit_total = 0;
	  $objSeat->history = $objp->note;
	  $objSeat->manual = 0;
	  $objSeat->fk_user_creator = $user->id;
	  $objSeat->fk_date_creator = date('Y-m-d H:i:s');
	  $objSeat->state = 1;
	  $idSeat = $objSeat->create($user);
	}  
      //detalle
      echo '<pre>';
      print_r($objp);
      echo '<pre>';
      echo '<hr>';
      $dcd = 1;
      $dcc = 1;
      $objSeatDet = new Contabseatdet($db);
      $objSeatDet->fk_contab_seat = $idSeat;
      If ($objp->fk_type == "SOLD")
	{
	  //saldo inicial
	  //la cuenta se obtiene del banco
	  $objAccount = new Account($db);
	  $objAccount->fetch($objp->fk_account);
	  $objSeatDet->debit_account = $objAccount->account_number;
	  $objSeatDet->debit_detail = $langs->trans('opening balance');
	  $objSeatDet->credit_account = $conf->global->ACCOUNT_CAPITAL;
	  $objSeatDet->credit_detail = $langs->trans('opening balance ajuste');
	  $objSeatDet->amount = $objp->amount;
	  
	  $objSeatDet->dcd = $dcd;
	  $objSeatDet->dcc = $dcc;
	  $objSeatDet->history = $objp->history;
	  $objSeatDet->sequence = $sequen;
	  $objSeatDet->fk_standard_seat = 505;
	  $objSeatDet->type_seat = 3;
	  $objSeatDet->routines = "bank";
	  $objSeatDet->date_rate = date('Y-m-d');
	  $objSeatDet->rate = 0;
	  $objSeatDet->value02 = 0;
	  $objSeatDet->value03 = 0;
	  $objSeatDet->value04 = 0;
	  $objSeatDet->fk_user_creator = $user->id;
	  $objSeatDet->fk_date_creator = date('Y-m-d H:i:s');

	  $sumDebe = $objSeatDet->amount;
	  $sumCred = $objSeatDet->amount;
	}
      If ($objp->fk_type == "LIQ")
	{
	  If ($nCountData == 1)
	    {
	      If ($objp->amount > 0)
		{
		  //debito
		  $objAccount = new Account($db);
		  $objAccount->fetch($objp->fk_account);
		  $objSeatDet->debit_account = $objAccount->account_number;
		  $objSeatDet->debit_detail  = $langs->trans('CustomerInvoicePayment');		  
		  $objSeatDet->amount = $objp->amount;
		  $sumDebe = $objSeatDet->amount;
		  //credito
		  $objSeatDet->credit_account = $conf->global->ACCOUNT_DEBTOR_FOR_SALE;
		  $objSeatDet->credit_detail = $langs->trans('Debtorforsale'.' '.$user->name);
		}
	      else
		{
		  //gasto 
		  $objBankSpending = new Bankspending($db);
		  $objBankSpending->fetch_bank($objp->rowid);
		  print_r($objBankSpending);
		  If ($objBankSpending->fk_bank_id == $objp->rowid)
		    {

		      $objDeplacem = new Deplacement($db);
		      $objDeplacem->fetch($objBankSpending->fk_deplacement);
		      $objSpendingAcc = new Contabspendingaccount($db);
		      $objSpendingAcc->fetch_ref($objDeplacem->type);
		      //denbito
		      $objSeatDet->debit_account = $objSpendingAcc->fk_account;
		      $objSeatDet->debit_detail  = $objDeplacem->note;
		    }
		  else
		    {
		      $objSeatDet->debit_account = $conf->global->ACCOUNT_EXPENSES;
		      $objSeatDet->debit_detail  = $langs->trans("Expenses not defined");
		    }
		  //credito
		  $objAccount = new Account($db);
		  $objAccount->fetch($objp->fk_account);
		  $objSeatDet->credit_account = $objAccount->account_number;
		  $objSeatDet->credit_detail  = $langs->trans('Expenses');		  
		  $objSeatDet->amount = $objp->amount * -1;
		  $sumCred = $objSeatDet->amount;
		}
	      $objSeatDet->dcd = $dcd;
	      $objSeatDet->dcc = $dcc;
	      $objSeatDet->history = $objp->history;
	      $objSeatDet->sequence = $sequen;
	      $objSeatDet->fk_standard_seat = 505;
	      $objSeatDet->type_seat = 3;
	      $objSeatDet->routines = "bank";
	      $objSeatDet->date_rate = date('Y-m-d');
	      $objSeatDet->rate = 0;
	      $objSeatDet->value02 = 0;
	      $objSeatDet->value03 = 0;
	      $objSeatDet->value04 = 0;
	      $objSeatDet->fk_user_creator = $user->id;
	      $objSeatDet->fk_date_creator = date('Y-m-d H:i:s');
	      
	    }
	  else
	    {
	      if ($objp->amount > 0)
		{
		  $objAccount = new Account($db);
		  $objAccount->fetch($objp->fk_account);
		  $objSeatDet->debit_account = $objAccount->account_number;
		  $objSeatDet->debit_detail  = $langs->trans($objp->label);
		  $objSeatDet->amount = $objp->amount;
		  $sumDebe = $objSeatDet->amount;
		}
	      else
		{
		  $objAccount = new Account($db);
		  $objAccount->fetch($objp->fk_account);
		  $objSeatDet->credit_account = $objAccount->account_number;
		  $objSeatDet->credit_detail  = $langs->trans($objp->label);
		  $objSeatDet->amount = $objp->amount * -1;
		  $sumCred = $objSeatDet->amount;
		}
	      $objSeatDet->dcd = $dcd;
	      $objSeatDet->dcc = $dcc;
	      $objSeatDet->history = $objp->label;
	      $objSeatDet->sequence = $sequen;
	      $objSeatDet->fk_standard_seat = 505;
	      $objSeatDet->type_seat = 3;
	      $objSeatDet->routines = "bank";
	      $objSeatDet->date_rate = date('Y-m-d');
	      $objSeatDet->rate = 0;
	      $objSeatDet->value02 = 0;
	      $objSeatDet->value03 = 0;
	      $objSeatDet->value04 = 0;

	      $objSeatDet->fk_user_creator = $user->id;
	      $objSeatDet->fk_date_creator = date('Y-m-d H:i:s');
	    }
	}
      $sequen++;
    }
  $objSeatDet->state = 1;

  echo '<pre>';
  print_r($objSeatDet);
  echo '<pre>';
  //registra en la seat_det
  $idSeatDet = $objSeatDet->create($user);
  if ($idSeatDet <= 0)
    {
      echo $mesg='<div class="error">'.$objSeatDet->error.'</div>';
      exit;
    }
  //actuaizando y validando
  $object = new Contabseat($db);

  $object->fetch($idSeat);
  $ref = substr($object->lote, 1, 4);
  if ($ref == 'PROV')
    {
      list($numlote,$numsblote,$numdoc) = $object->getNextNumRef($object);
    }
  else
    {
      $numlote   = $object->lote;
      $numsblote = $object->sblote;
      $numdoc    = $object->doc;
    }
  $object->lote = $numlote;
  $object->sblote = $sblote;
  $object->doc = $numdoc;
  $object->debit_amount = $sumdebe;
  $object->credit_amount = $sumCred;
  $object->update($user);
}

$db->close();

llxFooter();
?>
