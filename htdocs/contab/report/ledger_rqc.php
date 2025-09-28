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
 *	\file       htdocs/contab/report/ledger.php
 *	\ingroup    Books report
 *	\brief      Page fiche contab ledger
 */

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/report.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/accountancy/class/html.formventilation.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabaccountingext.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabseatext.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabseatdetext.class.php';

require_once(DOL_DOCUMENT_ROOT."/contab/class/accountingaccountext.class.php");
require_once(DOL_DOCUMENT_ROOT."/contab/class/accountingaccountadd.class.php");

require_once DOL_DOCUMENT_ROOT.'/contab/lib/contab.lib.php';

$langs->load("contab");

$action=GETPOST('action');
$id    = GETPOST("id");
//verificamos la gestion activa
if (!isset($_SESSION['period_year']))
	$_SESSION['period_year'] = strftime("%Y",dol_now());
$period_year = $_SESSION['period_year'];

$year_current = strftime("%Y",dol_now());
$pastmonth = strftime("%m",dol_now());
$pastmonthyear = $period_year;
$year_current = strftime("%Y",dol_now());
if ($pastmonthyear < $year_current) $pastmonth = 12;
if ($pastmonth == 0)
{
	$pastmonth = 12;
	$pastmonthyear--;
}
$date_initial = dol_get_first_day($pastmonthyear,1,false);

$date_ini  = dol_mktime(0, 0, 0, GETPOST('di_month'),  GETPOST('di_day'),  GETPOST('di_year'));
$date_fin  = dol_mktime(23, 59, 59, GETPOST('df_month'),  GETPOST('df_day'),  GETPOST('df_year'));
if (empty($date_end) && empty($date_ini)) // We define date_start and date_end
{
	$date_ini=dol_get_first_day($pastmonthyear,$pastmonth,false);
	$date_fin=dol_get_last_day($pastmonthyear,$pastmonth,false);
}

$mesg = '';

$object = new Contabseatext($db);
$objSeatdet = new Contabseatdetext($db);
$objAccounting = new Accountingaccountext($db);
$objAccountingadd = new Accountingaccountadd($db);

$form = new Form($db);

$filteracc = '';
$res = $objAccounting->fetchAll('','',0,0,array(1=>1),'AND',$filteracc);
$options = "";

$id = GETPOST('id');

if ($res >0)
{
	$lines = $objAccounting->lines;
	foreach ($lines AS $j => $line)
	{
		$select = '';
		if ($id == $line->id) $select = ' selected';
		$options.= '<option value="'.$line->id.'" '.$select.'>'.$line->account_number.' '.$line->label.'</options>';
	}
}

/*
 * Actions
 */

/*
* view
*/
$form = new Form($db);
$formventilation = new Formventilation($db);


$help_url='EN:Module_Contab_En|FR:Module_Contab|ES:M&oacute;dulo_Contab';
llxHeader("",$langs->trans("Managementaccounting"),$help_url);

print_barre_liste($langs->trans("Ledger"), $page, "bc.php", "", $sortfield, $sortorder,'',$num);

print "<form action=\"ledger.php\" method=\"post\">\n";
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="edit">';
dol_htmloutput_mesg($mesg);
print '<table class="border" width="100%">';

// date ini
print '<tr><td class="fieldrequired">'.$langs->trans('Dateini').'</td><td colspan="2">';
$form->select_date($date_ini,'di_','','','',"crea_seat",1,1);
print '</td></tr>';

// date fin
print '<tr><td class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="2">';
$form->select_date($date_fin,'df_','','','',"crea_seat",1,1);
print '</td></tr>';

// date fin
print '<tr><td class="fieldrequired">'.$langs->trans('Account').'</td><td colspan="2">';
//print $object->select_account($id,'id','',0,1,2);
print $formventilation->select_account(GETPOST('id'),'id',1);
print '</td></tr>';

print '</table>';

print '<center><br><input type="submit" class="button" value="'.$langs->trans("Generate").'"></center>';

print '</form>';



/*
 * View
 */

if ($id)
{
	dol_htmloutput_mesg($mesg);
	$result = $objAccounting->fetch($id);
	$resadd = $objAccountingadd->fetch(0,$id);
	if ($result < 0)
	{
		dol_print_error($db);
	}


	//$head = fabrication_prepare_head($object);
	print '<br>';
	dol_fiche_head();

	print '<table class="border" width="100%">';

	// ref
	print '<tr><td width="20%">'.$langs->trans('Ref').'</td><td colspan="2">';
	print $objAccounting->account_number;
	print '</td></tr>';
	// name
	print '<tr><td>'.$langs->trans('Name').'</td><td colspan="2">';
	print $objAccounting->label;
	print '</td></tr>';

	//top
	print '<tr><td>'.$langs->trans('Accounttop').'</td><td colspan="2">';
	if ($objAccounting->account_parent)
		print $objAccounting->account_parent;
	else
		print '&nbsp;';
	print '</td></tr>';

	//cta_class
	print '<tr><td>'.$langs->trans('Class').'</td><td colspan="2">';
	print $objAccountingadd->cta_class;
	print '</td></tr>';

	//cta_normal
	print '<tr><td>'.$langs->trans('Normal').'</td><td colspan="2">';
	print $objAccountingadd->cta_normal;
	print '</td></tr>';

	//status
	print '<tr><td>'.$langs->trans('Status').'</td><td colspan="2">';
	print $objAccounting->getLibStatut(6);
	print '</td></tr>';

	print "</table>";
	dol_fiche_end();

	//obtenemos la suma anterior
	$aDate = dol_getdate($date_ini);
	$lCalculoant = true;
	if ($aDate['mon']==1 && $aDate['mday']==1) $lCalculoant = false;

	$aDateant = dol_get_prev_day($aDate['mday'], $aDate['mon'],$aDate['year']);
	$date_ini_ant = dol_mktime(23,59,59,$aDateant['month'],$aDateant['day'],$aDateant['year']);
	$sumDa=0;
	$sumCa=0;
	if ($lCalculoant)
	{
		$res = $objSeatdet->fetch_list_account_group($objAccounting->account_number,$date_initial,$date_ini_ant);
		$sumBalance = 0;
		if ($res>0)
		{
			$aArray = $objSeatdet->aArray;
			$sumDa +=$aArray['debit_amount'];
			$sumCa +=$aArray['credit_amount'];
		}
	}
	//liste movimiento contable

	$res = $objSeatdet->fetch_list_account($objAccounting->account_number,$date_ini,$date_fin);

	if ($res>0)
	{
		$aArray = $objSeatdet->aArray;
		$aArrayDet = $objSeatdet->aArrayDet;
		print '<table class="noborder" width="100%">';

		print '<tr class="liste_titre">';
		print_liste_field_titre($langs->trans("Ref"),"", "","","","");
		print_liste_field_titre($langs->trans("Date"),"", "","","","");
		print_liste_field_titre($langs->trans("Detail"),"", "","","",'align="left"');
		print_liste_field_titre($langs->trans("Debit"),"", "","","",'align="right"');
		print_liste_field_titre($langs->trans("Credit"),"", "","","",'align="right"');
		print_liste_field_titre($langs->trans("Balance"),"", "","","",'align="right"');

		print '</tr>';
		//registramos saldo anterior
		if ($sumDa>0 || $sumCa>0)
		{
			$var=!$var;
			print "<tr $bc[$var]>";
			print '<td width="6%">'.'</td>';
			print '<td width="4%">'.'</td>';
			print '<td>'.$langs->trans('Previousbalance').'</td>';
			if ($objAccountingadd->cta_normal==1) $difBalance = $sumDa-$sumCa;
			else $difBalance = $sumCa-$sumDa;
			print '<td width="5%" align="right">'.'</td>';
			print '<td width="5%" align="right">'.'</td>';
			print '<td width="5%" align="right">'.price(price2num($difBalance,'MT')).'</td>';
			print '</tr>';
			$sumBalance =$difBalance;
		}
		foreach($aArrayDet AS $fk_seat => $aData)
		{
			$object = new Contabseatext($db);
			$object->fetch($fk_seat);
			$var=!$var;
			print "<tr $bc[$var]>";
			print '<td width="6%">'.$object->getNomUrl(1).'</td>';
			print '<td width="4%">'.dol_print_date($object->date_seat,'day').'</td>';
			print '<td>'.$object->history.'</td>';
			print '<td width="5%" align="right">'.price(price2num($aData['debit_account'],'MT')).'</td>';
			$sumD +=$aData['debit_account'];
			$sumC +=$aData['credit_account'];
			if ($objAccountingadd->cta_normal==1) $dif = $aData['debit_account']-$aData['credit_account'];
			else $dif = $aData['credit_account']-$aData['debit_account'];
			$sumBalance+=$dif;
			print '<td width="5%" align="right">'.price(price2num($aData['credit_account'],'MT')).'</td>';
			print '<td width="5%" align="right">'.price(price2num($sumBalance,'MT')).'</td>';

			print '</tr>';

		}
		print '<tr class="liste_total"><td align="left" colspan="3">'.$langs->trans("Total").'</td>';
		print '<td align="right">'.price(price2num($sumD,'MT')).'</td>';
		print '<td align="right">'.price(price2num($sumC,'MT')).'</td>';
		print '</tr>';

		print '<tr class="liste_total"><td align="left" colspan="3">'.$langs->trans("Accountbalances").'</td>';
		if ($objAccountingadd->cta_normal == 1)
		{
			print '<td align="right">'.price(price2num($difBalance+$sumD-$sumC,'MT')).'</td>';
			print '<td align="right">&nbsp;</td>';
		}
		else
		{
			print '<td align="right">&nbsp;</td>';
			print '<td align="right">'.price(price2num($difBalance+$sumC-$sumD,'MT')).'</td>';
		}
		print '</tr>';
		print '</table>';
	}
	else
	{
		print '<table class="noborder" width="100%">';
		$var=!$var;
		print "<tr $bc[$var]>";
		print '<td>'.$langs->trans('Nomovement').'</td>';
		print  '</tr>';
		print '</table>';
	}
}


llxFooter();

$db->close();
?>
