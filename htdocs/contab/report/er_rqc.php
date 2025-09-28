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
 *      \file       htdocs/contab/report/er.php
 *      \ingroup    Contab balance de comprobacion
 *      \brief      Page liste des balance comprobacion
*/

require("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/lib/report.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

require_once(DOL_DOCUMENT_ROOT."/contab/class/contabaccountingext.class.php");
require_once(DOL_DOCUMENT_ROOT."/contab/class/contabseatdetext.class.php");
require_once(DOL_DOCUMENT_ROOT."/contab/class/contabseatext.class.php");
require_once(DOL_DOCUMENT_ROOT."/contab/class/contabvisionext.class.php");

require_once(DOL_DOCUMENT_ROOT."/contab/class/accountingaccountext.class.php");

require_once(DOL_DOCUMENT_ROOT."/contab/lib/contab.lib.php");

$langs->load("contab@contab");

// if (!$user->rights->contab->report->leer)
//   accessforbidden();
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

$year = GETPOST('date_endyear');
$printledger  = GETPOST('printledger');
$printaccount = GETPOST('printaccount');
if (empty($year)) $year = $year_current;
$date_ini  = dol_mktime(0, 0, 1, $conf->global->SOCIETE_FISCAL_MONTH_START,  1,  $year);
$date_end  = dol_mktime(23, 59, 59, GETPOST('date_endmonth'),  GETPOST('date_endday'),  GETPOST('date_endyear'));
if (empty($date_end))
// We define date_start and date_end
{
	$date_end=dol_get_last_day($pastmonthyear,$pastmonth,false);
}
$id     = GETPOST('id','int');
$action = GETPOST('action','alpha');

$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page;

$object = new Contabvisionext($db);
$objAccounting = new Accountingaccountext($db);
$form = new Form($db);

if ($action == 'generate')
{
	$result = $object->fetch($id);
	if ($result > 0)
		$ref = $object->ref;
}
if (empty($id))
{
	if (GETPOST('rep')=='bg')
	{
		$res = $object->fetchAll('','',0,0,array(1=>1),'AND'," AND t.ref = '".$conf->global->CONTAB_CODE_VISION_BG."' AND t.sequence = 1",true);
		$id = $object->id;
	}
	elseif (GETPOST('rep')=='er')
	{
		$res = $object->fetchAll('','',0,0,array(1=>1),'AND'," AND t.ref = '".$conf->global->CONTAB_CODE_VISION_ER."' AND t.sequence = 1",true);
		$id = $object->id;
	}
}

$help_url='EN:Module_Contab_En|FR:Module_Contab|ES:M&oacute;dulo_Contab';
llxHeader("",$langs->trans("Managementaccounting"),$help_url);

print_barre_liste($langs->trans("Income statement"), $page, "bc.php", "", $sortfield, $sortorder,'',$num);

print "<form action=\"er.php\" method=\"post\">\n";
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="generate">';
dol_htmloutput_mesg($mesg);
print '<table class="border" width="100%">';

// date seat
print '<tr><td width="20%" class="fieldrequired">'.$langs->trans('Datefin').'</td><td colspan="2">';
$form->select_date($date_end,'date_end','','','',"crea_er",1,1);
print '</td></tr>';

// vision
print '<tr><td class="fieldrequired">'.$langs->trans('Fieldvision').'</td><td colspan="2">';
print $object->select_vision($id,'id','',0,0);
print '</td></tr>';

// imprimir cuentas vision
print '<tr><td class="fieldrequired">'.$langs->trans('Printaccountsvision').'</td><td colspan="2">';
print select_yesno($printaccount,'printaccount','',0,1);
print '</td></tr>';

// imprimir detalle cuentas
print '<tr><td class="fieldrequired">'.$langs->trans('Printledgeraccounts').'</td><td colspan="2">';
print select_yesno($printledger,'printledger','',0,1);
print '</td></tr>';


print '</table>';
print '<center><br><input type="submit" class="button" value="'.$langs->trans("Generate").'"></center>';

print '</form>';

if ($action == 'generate')
{
	$column = 0;
	//proceso de consulta

	$filter = " AND t.entity = ".$conf->entity;
	$filter.= " AND t.ref = '".$ref."' ";
	$filter.= " AND t.rowid != ".$id;
	$res = $object->fetchAll('ASC','t.ref,t.sequence,t.line',0,0,array(1=>1),'AND',$filter);
	if ($res)
	{
		//preparamos los arrays
		$aArraycta = array();
		$aArrayOrd = array();
		$num = $res;
		$lines = $object->lines;
		$i = 0;

		$objectseatdet = new Contabseatdetext($db);
		$var=True;
		foreach ($lines AS $j => $objp)
		{
			//echo '<hr>id '.$objp->id.' class '.$objp->cta_class.' ini '.$objp->fk_accountini.' fin '.$objp->fk_accountfin;
				//$objp = $db->fetch_object($result);
			if ($column <= $objp->cta_column) $column = $objp->cta_column;
			$aArrayOrd[$objp->sequence] = $objp->account;
			$aArrayCta[$objp->account]['label']       = $objp->detail_managment;
			$aArrayCta[$objp->account]['class']       = $objp->cta_class;
			$aArrayCta[$objp->account]['normal']      = $objp->cta_normal;
			$aArrayCta[$objp->account]['balance']     = $objp->cta_balances;
			if ($objp->line == '001')
				$aArrayCta[$objp->account]['column']      = $objp->cta_column;
			$aArrayCta[$objp->account]['operation']   = $objp->cta_operation;
			$aArrayCta[$objp->account]['identifier']  = $objp->cta_identifier;
			$aArrayCta[$objp->account]['account_sup'] = $objp->account_sup;
			if ($objp->cta_class == 2)
			{
					//buscar valores de las cuentas que afecta
				$accountini = '';
				$accountfin = '';
				$fk_accountini = $objp->fk_accountini;
				$fk_accountfin = $objp->fk_accountfin;
				if ($fk_accountini>0)
				{
					$resc = $objAccounting->fetch($fk_accountini);
					if ($resc > 0) $accountini = $objAccounting->account_number;
				}
				if ($fk_accountfin>0)
				{
					$resc = $objAccounting->fetch($fk_accountfin);
					if ($resc > 0) $accountfin = $objAccounting->account_number;
				}
				//echo '<hr>cuentas '.$accountini.' '.$accountfin;
					//buscamos el array del rango de cuentas
				$resacc = $objAccounting->list_account($accountini,$accountfin);
				if ($resacc>0)
				{
					$aListAccount = $objAccounting->aArray;
					//recorremos las cuentas para sumar
					foreach ((array) $aListAccount AS $account => $cta_normal)
					{
						//list($aArr,$aArrDet)
						$ressd = $objectseatdet->fetch_list_account($account,$date_ini,$date_end);
						if ($ressd>0)
						{
							$aArr = $objectseatdet->aArray;
							$aArrDet = $objectseatdet->aArrayDet;
							//echo '<hr>';
							//print_r($aArr);
							//print_r($aArrDet);
							$aArrayCta[$objp->account]['debit_amount']  += $aArr['debit_amount'];
							$aArrayCta[$objp->account]['credit_amount'] += $aArr['credit_amount'];
							$aArrayCta[$objp->account]['accountsheet'][$account]['debit_amount'] += $aArr['debit_amount'];
							$aArrayCta[$objp->account]['accountsheet'][$account]['credit_amount'] += $aArr['credit_amount'];
							//account sup
							$aArrayCta[$objp->account_sup+0]['debit_amount'] += $aArr['debit_amount'];
							$aArrayCta[$objp->account_sup+0]['credit_amount'] += $aArr['credit_amount'];
							if ($cta_normal == 1)
							{
								//deudor
								$aArrayCta[$objp->account]['valor']+=price2num($aArr['debit_amount']-$aArr['credit_amount'],'MT');
								$aArrayCta[$objp->account]['accountcont'][$account]+=price2num($aArr['debit_amount']-$aArr['credit_amount'],'MT');
								//sumando para la cuenta superior
								$aArrayCta[$objp->account_sup+0]['valor']+=$aArr['debit_amount']-$aArr['credit_amount'];
							}
							else
							{
								$aArrayCta[$objp->account]['valor']+=price2num($aArr['credit_amount']-$aArr['debit_amount'],'MT');
								$aArrayCta[$objp->account]['accountcont'][$account]+=price2num($aArr['credit_amount']-$aArr['debit_amount'],'MT');
								//sumando para la cuenta superior
								$aArrayCta[$objp->account_sup+0]['valor']+=$aArr['credit_amount']-$aArr['debit_amount'];
							}
						}
					}
				}
			}
			else
			{
				$aArrayCta[$objp->account_sup+0]['debit_amount']+=$aArrayCta[$objp->account]['debit_amount'];
				$aArrayCta[$objp->account_sup+0]['credit_amount']+=$aArrayCta[$objp->account]['credit_amount'];

			}
			$i++;

		}
			//echo '<hr><pre>';
			//print_r($aArrayCta);
			//print_r($aArrayOrd);
			//echo '</pre>';

		ksort($aArrayOrd);
		//ksort($aArrayCta);
		//imprimiendo el resultado

		print '<table class="noborder" width="100%">';

		print "<tr class=\"liste_titre\">";
		if ($printaccount == 1)
			print_liste_field_titre($langs->trans("Account"),"", "","","","");
		print_liste_field_titre($langs->trans("Name"),"", "","","","");
		for ($i = $column; $i >=0 ; $i--)
		{
			print_liste_field_titre($i,"", "","","",'align="right"');
		}
		print "</tr>\n";
		//echo '<pre>';
		//print_r($aArrayOrd);
		//echo '</pre>';
		foreach((array) $aArrayOrd AS $ord => $account)
		{
			$lSumaTotal = False;
			$lSinValor  = False;
			$aData = $aArrayCta[$account];
			$var=!$var;

			if ($aData['class'] == 1)
			{
				$aIdentifier = explode('|',$aData['identifier']);
				foreach((array) $aIdentifier AS $i => $value)
				{
					if ($value == 1)
						print '<tr '.$bc[$var].' style="font-weight:bold;">';
					if ($value == 2)
						$lSumaTotal = True;
					if ($value == 3)
						$lSinValor = True;
				}
			}
			else
			{
				print "<tr $bc[$var]>";
			}
			//buscamos la cuenta
			if ($printaccount == 1)
				print '<td>'.$account.'</td>';
			print '<td>'.$aData['label'].'</td>';
			if ($aData['normal'] == 1)
			{
				$sumaFila = $aData['debit_amount']-$aData['credit_amount'];
			}
			else
			{
				$sumaFila = $aData['credit_amount']-$aData['debit_amount'];
			}
			if ($aData['class'] == 1)
			{
				if ($lSumaTotal)
				{
					for ($j = $column; $j>=0; $j--)
					{
						if ($j == $aData['column'])
							print '<td align="right">'.price(price2num($sumaFila,'MT')).'</td>';
						else
							print '<td>&nbsp;</td>';
					}
				}
				else
					for ($j = $column; $j>=0; $j--)
					{
						print '<td>&nbsp;</td>';
					}
				}
				else
				{
					for ($j = $column; $j>=0; $j--)
					{
						if ($j == $aData['column'])
							print '<td align="right">'.price(price2num($sumaFila.'MT')).'</td>';
						else
							print '<td>&nbsp;</td>';
					}
				}

				print "</tr>\n";
				if ($printledger == 1)
				{
					//impresion de cuentas contables
					foreach ((array) $aData['accountsheet'] AS $accountsheet => $aRow)
					{
						if ($aData['normal'] == 1)
						{
							$sumaFila = $aRow['debit_amount']-$aRow['credit_amount'];
						}
						else
						{
							$sumaFila = $aRow['credit_amount']-$aRow['debit_amount'];
						}
						if ($sumaFila <> 0)
						{
							$resaccount = $objAccounting->fetch('',$accountsheet,1);
							$accountname = '';
							if ($resaccount>0)
								$accountname = $objAccounting->label;
							print "<tr $bc[$var]>";
							if ($printaccount == 1)
							{
								print '<td>'.$accountsheet.'</td>';
								print '<td>'.$accountname.'</td>';
							}
							else
							{
								print '<td>'.$accountsheet.' '.$accountname.'</td>';
							}
							print '<td align="right">'.price(price2num($sumaFila,'MT')).'</td>';
							print '</tr>';
						}
					}
				}
			}
			// echo '<pre>';
			// print_r($aArrayCta);
			// echo '</pre>';

			//$db->free($result);

			print "</table>";

		}
		else
		{
			dol_print_error($db);
		}
	}

	$db->close();

	llxFooter();

	?>
