<?php
/* Copyright (C) 2009-2010	Erick Bullier	<eb.dev@ebiconsulting.fr>
 * Copyright (C) 2010-2011	Regis Houssin	<regis@dolibarr.fr>
* Copyright (C) 2012       Florian Henry   <florian.henry@open-concept.pro>
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
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
*/

/**
 * 	\file		/agefodd/index.php
 * 	\brief		Tableau de bord du module de formation pro. (Agefodd).
* 	\Version	$Id$
*/

/*error_reporting(E_ALL);
 ini_set('display_errors', true);
 ini_set('html_errors', false);*/

$res=@include("../main.inc.php");					// For root directory
if (! $res) $res=@include("../../main.inc.php");	// For "custom" directory
if (! $res) die("Include of main fails");

require_once(DOL_DOCUMENT_ROOT.'/sales/class/bankstatusext.class.php');
require_once(DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php');
require_once(DOL_DOCUMENT_ROOT.'/user/class/user.class.php');

require_once(DOL_DOCUMENT_ROOT.'/sales/class/factureext.class.php');

dol_include_once('/sales/lib/account.lib.php');

// Security check
if (!$user->rights->sales->use) accessforbidden();

$langs->load('contable@contable');
$langs->load('sales@sales');

llxHeader('',$langs->trans('Sales'));

print_barre_liste($langs->trans("Openboxes"), $page, "index.php","&socid=$socid",$sortfield,$sortorder,'',$num);

$objuser = new User($db);
$objaccount = new Account($db);
$objbankstat = new Bankstatusext($db);
$objbankstat->getlist($user);

print '<table width="auto">';

//Openbox
print '<tr><td width=auto>';
print '<table class="noborder" width="500px">';
// print '<tr class="liste_titre"><td colspan=4>'.$langs->trans("Openboxes").'</td></tr>';
print '<tr class="liste_titre">';
print '<td align="center">'.$langs->trans('Sucursal').'</td>';
print '<td align="center">'.$langs->trans('Date').'</td>';
if ($user->rights->ventas->res->leer)
	print '<td align="center">'.$langs->trans('Amount').'</td>';
else
	print '<td align="center">&nbsp;</td>';

print '<td align="center">'.$langs->trans('User').'</td>';
print '</tr>';
foreach ((array) $objbankstat->array AS $i => $objb)
{
	$objaccount->fetch($objb->fk_bank);
	$objuser->fetch($objb->fk_user);
	print '<tr>';
	print '<td>'.$objaccount->label.'</td>';
	print '<td>'.dol_print_date($objb->date_register,'dayhour').'</td>';
	if ($user->rights->ventas->res->leer)
	{
		$saldoBankUser = saldoAccount($objb->fk_bank,$objb->fk_user);
		$saldoBankUser += 0;

		print '<td align="right">'.price($saldoBankUser).'</td>';  
	}
	else
		print '<td>&nbsp;</td>';
	print '<td>'.$objuser->lastname.' '.$objuser->firstname.'</td>';

	print '</tr>';
}
print '</table>';
print '</td></tr></table>';

//realizando una actualizacion
$sql = " SELECT";
$sql.= " t.rowid, ";
$sql.= " t.facnumber, ";
$sql.= " t.datec,";
$sql.= " t.tva, ";
$sql.= " t.total, ";
$sql.= " t.total_ttc, ";
$sql.= " t.fk_statut ";

$sql.= " FROM ".MAIN_DB_PREFIX."facture AS t ";
$sql.= " WHERE t.entity = ".$conf->entity;
//$sql.= " AND t.type = 3 ";
$sql.= " AND t.fk_statut != 3";
$sql.= " AND t.datef >= '2016/07/01'";
//$sql.= " AND t.rowid = 8185";
$resql = $db->query($sql);
if ($resql && $abc)
{
	$num = $db->num_rows($resql);
	$i = 0;
	print '<table border="1">';
	print '<tr>';
	print '<td>';
	print 'Seq';
	print '</td>';
	print '<td>';
	print 'rowid_fac';
	print '</td>';
	print '<td>';
	print 'statutfac';
	print '</td>';
	print '<td>';
	print 'facnumber';
	print '</td>';
	print '<td>';
	print 'Date';
	print '</td>';
	print '<td align="right">';
	print 'total_ttc';
	print '</td>';
	print '<td align="center">';
	print 'Nfiscal';
	print '</td>';
	print '<td align="right">';
	print 'Amountv';
	print '</td>';
	print '<td align="right">';
	print 'amount_pfac';
	print '</td>';
	print '<td align="right">';
	print 'amount_paiement';
	print '</td>';
	print '<td align="right">';
	print 'amount_bank';
	print '</td>';
	print '<td align="right">';
	print 'diferencia';
	print '</td>';
	print '</tr>';
$seq = 0;
	while ($i < $num)
	{
		$amountpayf = 0;
		$amountpay = 0;
		$amountbank = 0;
		$obj = $db->fetch_object($resql);
		//buscamos la relacion con vfiscal
		$sql = " SELECT";
		$sql.= " t.rowid, ";
		$sql.= " t.nfiscal,";
		$sql.= " t.baseimp1 ";
		$sql.= " FROM ".MAIN_DB_PREFIX."v_fiscal AS t ";
		$sql.= " WHERE t.fk_facture = ".$obj->rowid;
		$resqlv = $db->query($sql);
		$amountv = 0;
		$nFiscal = '';
		if ($resqlv)
		{
			$objv = $db->fetch_object($resqlv);
			$amountv = $objv->baseimp1;
			$nFiscal = $objv->nfiscal;
		}
		//buscamos la relacion de factura pago
		$sql = " SELECT";
		$sql.= " t.rowid, ";
		$sql.= " t.fk_paiement, ";
		$sql.= " t.amount ";
		$sql.= " FROM ".MAIN_DB_PREFIX."paiement_facture AS t ";
		$sql.= " WHERE t.fk_facture = ".$obj->rowid;
		$resql1 = $db->query($sql);
		$idpay = '';
		if ($resql1)
		{
			$j = 0;
			$numpf = $db->num_rows($resql1);
			while ($j < $numpf)
			{
				$obj1 = $db->fetch_object($resql1);
				if (!empty($idpay)) $idpay.= ',';
				$idpay.= $obj1->fk_paiement;
				$amountpayf+= $obj1->amount;
				$j++;
			}
			//$idpay = $obj1->fk_paiement;
			//buscamos la relacion de factura pago
			$sql = " SELECT";
			$sql.= " t.rowid, ";
			$sql.= " t.fk_bank, ";
			$sql.= " t.amount ";
			$sql.= " FROM ".MAIN_DB_PREFIX."paiement AS t ";
			$sql.= " WHERE t.rowid IN(".$idpay.")";
			$resqlp = $db->query($sql);
			$idbank = '';
			if ($resqlp)
			{
				$k = 0;
				$nump = $db->num_rows($resqlp);
				while ($k < $nump)
				{
					$objp = $db->fetch_object($resqlp);
					if (!empty($idbank)) $idbank.= ',';
					$idbank.= $objp->fk_bank;
					$amountpay+= $objp->amount;
					$k++;
				}
				//$idbank = $objp->fk_bank;
			//buscamos la relacion de paiement y bank
				$sql = " SELECT";
				$sql.= " t.rowid, ";
				$sql.= " t.fk_account, ";
				$sql.= " t.amount ";
				$sql.= " FROM ".MAIN_DB_PREFIX."bank AS t ";
				$sql.= " WHERE t.rowid IN (".$idbank.")";
				$resqlb = $db->query($sql);
				if ($resqlb)
				{
					$l = 0;
					$numb = $db->num_rows($resqlb);
					while ($l < $numb)
					{
						$objb = $db->fetch_object($resqlb);
						$amountbank+= $objb->amount;
						$l++;
					}
				//$idbank = $objp->fk_bank;
				}
			}
		}
		$lPrint = false;
		$dif = 	price2num($obj->total_ttc - $objb->amount,'MT');
		//if ($dif < 0)
			//$lPrint = true;
		if (empty($amountv) || $amountv != $obj->total_ttc)
			$lPrint = true;

		if ($lPrint)
		{
			$seq++;
			print '<tr>';
			print '<td>';
			print $seq;
			print '</td>';
			print '<td>';
			print $obj->rowid;
			print '</td>';
			print '<td>';
			print $obj->fk_statut;
			print '</td>';
			print '<td>';
			print $obj->facnumber;
			print '</td>';
			print '<td>';
			print dol_print_date($db->jdate($obj->datec),'dayhour');
			print '</td>';
			print '<td align="right">';
			print price($obj->total_ttc);
			print '</td>';
			print '<td align="center">';
			print $nFiscal;
			print '</td>';
			print '<td align="right">';
			print price($amountv);
			print '</td>';
			print '<td align="right">';
			print price($amountpayf);
			print '</td>';
			print '<td align="right">';
			print price($amountpay);
			print '</td>';
			print '<td align="right">';
			print price($amountbank);
			print '</td>';
			print '<td align="right">';
			print price($obj->total_ttc - $amountbank);
			print '</td>';
			print '</tr>';
		}
		$i++;
	}
	print '</table>';
}

if ($resql && $abc)
{
	$num = $db->num_rows($resql);
	$i = 0;
	print '<table border="1">';
	print '<tr>';
	print '<td>';
	print 'Seq';
	print '</td>';
	print '<td>';
	print 'rowid_fac';
	print '</td>';
	print '<td>';
	print 'statutfac';
	print '</td>';
	print '<td>';
	print 'facnumber';
	print '</td>';
	print '<td>';
	print 'Date';
	print '</td>';
	print '<td align="right">';
	print 'total_ttc';
	print '</td>';
	print '<td align="right">';
	print 'total';
	print '</td>';
	print '<td align="right">';
	print 'tva';
	print '</td>';
	print '<td align="center">';
	print 'idVfiscal';
	print '</td>';
	print '<td align="center">';
	print 'Nfiscal';
	print '</td>';
	print '<td align="center">';
	print 'Numaut';
	print '</td>';
	print '<td align="right">';
	print 'Amountv';
	print '</td>';
	print '<td align="right">';
	print 'valimp1';
	print '</td>';
	print '<td align="right">';
	print 'newbaseimp1';
	print '</td>';
	print '<td align="right">';
	print 'newvalimp1';
	print '</td>';
	print '</tr>';
	$seq = 0;
	while ($i < $num)
	{
		$amountpayf = 0;
		$amountpay = 0;
		$amountbank = 0;
		$obj = $db->fetch_object($resql);
		//buscamos la relacion con vfiscal
		$sql = " SELECT";
		$sql.= " t.rowid, ";
		$sql.= " t.nfiscal,";
		$sql.= " t.baseimp1, ";
		$sql.= " t.valimp1, ";
		$sql.= " t.num_autoriz ";
		$sql.= " FROM ".MAIN_DB_PREFIX."v_fiscal AS t ";
		$sql.= " WHERE t.fk_facture = ".$obj->rowid;
		$resqlv = $db->query($sql);
		$amountv = 0;
		$nFiscal = '';
		$numaut = '';
		$valimp1 = '';
		$idVfiscal = '';
		if ($resqlv)
		{
			$objv = $db->fetch_object($resqlv);
			$idVfiscal = $objv->rowid;
			$amountv = $objv->baseimp1;
			$valimp1 = $objv->valimp1;
			$numaut = $objv->num_autoriz;
			$nFiscal = $objv->nfiscal;
		}
		$lPrint = false;
		if (empty($amountv) || $amountv != $obj->total_ttc)
			$lPrint = true;
		if (empty($idVfiscal)) $lPrint = false;
		if ($lPrint)
		{
			$seq++;
			print '<tr>';
			print '<td>';
			print $seq;
			print '</td>';
			print '<td>';
			print $obj->rowid;
			print '</td>';
			print '<td>';
			print $obj->fk_statut;
			print '</td>';
			print '<td>';
			print $obj->facnumber;
			print '</td>';
			print '<td>';
			print dol_print_date($db->jdate($obj->datec),'dayhour');
			print '</td>';
			print '<td align="right">';
			print price($obj->total_ttc);
			print '</td>';
			print '<td align="right">';
			print price($obj->total);
			print '</td>';
			print '<td align="right">';
			print price($obj->tva);
			print '</td>';
			print '<td align="center">';
			print $idVfiscal;
			print '</td>';
			print '<td align="center">';
			print $nFiscal;
			print '</td>';
			print '<td align="center">';
			print $numaut;
			print '</td>';
			print '<td align="right">';
			print price($amountv);
			print '</td>';
			print '<td align="right">';
			print price($valimp1);
			print '</td>';
			$newbaseimp1 = $obj->total_ttc;
			$newvalimp1 = $obj->tva;
			print '<td align="right">';
			print price($newbaseimp1);
			print '</td>';
			print '<td align="right">';
			print price($newvalimp1);
			print '</td>';
			print '</tr>';
		}
		$i++;
	}
	print '</table>';
}


$db->close();

llxFooter('$Date: 2010-03-28 19:06:42 +0200 (dim. 28 mars 2010) $ - $Revision: 51 $');

?>