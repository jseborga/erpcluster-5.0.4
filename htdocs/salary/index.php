<?php
/* Copyright (C) 2001-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
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
 *      \file       htdocs/salary/index.php
 *      \ingroup    Salary
 *      \brief      Page index de salary
 */

require("../main.inc.php");

require_once(DOL_DOCUMENT_ROOT."/salary/class/adherentext.class.php");
require_once(DOL_DOCUMENT_ROOT."/salary/class/psalaryhistoryext.class.php");
require_once(DOL_DOCUMENT_ROOT."/salary/class/pperiodext.class.php");
require_once(DOL_DOCUMENT_ROOT."/salary/class/pproces.class.php");
require_once(DOL_DOCUMENT_ROOT."/salary/class/pcontractext.class.php");

$langs->load("stocks");
$langs->load("almacen@almacen");
$langs->load("fabrication@fabrication");

if (!$user->rights->salary->lire)
	accessforbidden();

llxHeader("",$langs->trans("Managementsalary"),$help_url);


print '<div><p>Modulo de Planilla de Sueldos</p></div>';

print '<div class="fichecenter"><div class="fichethirdleft">';
$aSex = array(-1 =>$langs->trans('Nodefined'),1=>$langs->trans('Varon'),2=>$langs->trans('Mujer'));
$aStat = array(0=>$langs->trans('Draft'),1=>$langs->trans('Validated'));
//listado de miembros registrados
$objAdherent = new Adherentext($db);
$res = $objAdherent->fetchAll('','',0,0,array(),'AND');
if ($res >0)
{
	$lines = $objAdherent->lines;
	foreach ($lines AS $j => $line)
	{
		$aStatus[$line->statut]++;
		$aType[$line->label_adherent_type]++;
		if (is_null($line->sex) || trim($line->sex)=='-')$line->sex = -1;
		$aSexo[$line->sex]++;
	}
	print '<table class="formdoc" width="100%">';
	print '<tr class="liste_titre"><td>'.$langs->trans('Staffregistrationstatus').'</td><td align="right">'.$langs->trans('Qty').'</td></tr>';
	$var = true;
	foreach ($aStatus AS $k => $value)
	{
		$var=!$var;
		print '<tr '.$bc[$var].'><td>'.$aStat[$k].'</td><td align="right">'.$value.'</td></tr>';
		$sumValue += $value;
	}
	//total
	print '<tr class="liste_total"><td>'.$langs->trans('Total').'</td><td align="right">'.$sumValue.'</td></tr>';
	print '</table>';

	print '<table class="formdoc" width="100%">';
	print '<tr class="liste_titre"><td>'.$langs->trans('Personaltyperegistered').'</td><td align="right">'.$langs->trans('Qty').'</td></tr>';
	$var = true;
	$sumValue=0;
	foreach ($aType AS $k => $value)
	{
		$var=!$var;
		print '<tr '.$bc[$var].'><td>'.$k.'</td><td align="right">'.$value.'</td></tr>';
		$sumValue += $value;
	}
	//total
	print '<tr class="liste_total"><td>'.$langs->trans('Total').'</td><td align="right">'.$sumValue.'</td></tr>';
	print '</table>';

	print '<table class="formdoc" width="100%">';
	print '<tr class="liste_titre"><td>'.$langs->trans('Sex').'</td><td align="right">'.$langs->trans('Qty').'</td></tr>';
	$var = true;
	$sumValue=0;
	foreach ($aSexo AS $k => $value)
	{
		$var=!$var;
		print '<tr '.$bc[$var].'><td>'.$aSex[$k].'</td><td align="right">'.$value.'</td></tr>';
		$sumValue += $value;
	}
	//total
	print '<tr class="liste_total"><td>'.$langs->trans('Total').'</td><td align="right">'.$sumValue.'</td></tr>';
	print '</table>';

}
print '</div><div class="fichetwothirdright"><div class="ficheaddleft">';
//lista el valor de la ultima planilla de pago aprobado
//se debe contar con una variable global que identifique el concepto a buscar
if (!empty($conf->global->SALARY_FKCONCEPT_FILTER))
{
	$objPeriod = new Pperiodext($db);
	$objSalaryhistory = new Psalaryhistoryext($db);
	$filter = " AND t.anio = ".date('Y');
	$res = $objPeriod->fetchAll('DESC','t.anio,t.mes',0,1,array('state'=>5),'AND',$filter,true);
	$fk_period = 0;
	if ($res ==1)
	{
		$fk_period = $objPeriod->id;
		$monthyear = $objPeriod->mes.'/'.$objPeriod->anio;
	}
	elseif($res>1)
	{
		foreach ($objPeriod->lines AS $j => $line)
		{
			if (empty($fk_period))
			{
				$fk_period = $line->id;
				$monthyear = $line->mes.'/'.$line->anio;
			}
		}
	}
	$filter = " AND t.fk_concept = ".$conf->global->SALARY_FKCONCEPT_FILTER;
	$filter.= " AND t.fk_period = ".$fk_period;
	$res = $objSalaryhistory->fetchAll('','',0,0,array(),'AND',$filter);
	if ($res >0)
	{
		$lines = $objSalaryhistory->lines;
		foreach ($lines AS $j => $line)
		{
			$aSalary[$line->fk_proces]+= $line->amount;
		}
		$objProcess = new Pproces($db);
		print '<table class="formdoc" width="100%">';
		print '<tr class="liste_titre"><td>'.$langs->trans('Lastapprovedpaymentform').' '.$monthyear.'</td><td align="right">'.$langs->trans('Amount').'</td></tr>';
		$var = true;
		$sumValue=0;
		foreach ($aSalary AS $k => $value)
		{
			$objProcess->fetch($k);
			$var=!$var;
			print '<tr '.$bc[$var].'><td>'.$objProcess->label.'</td><td align="right">'.price(price2num($value,'MT')).'</td></tr>';
			$sumValue += $value;
		}
		//total
		print '<tr class="liste_total"><td>'.$langs->trans('Total').'</td><td align="right">'.price(price2num($sumValue,'MT')).'</td></tr>';
		print '</table>';
	}
}
//lista de contratos con vencimiento
$objContract = new Pcontractext($db);
$aDate = dol_getdate(dol_now());

$date = dol_mktime(0,0,0,$aDate['mon'],$aDate['mday'],$aDate['year']);
$filter = " AND t.date_fin > 0 AND t.date_fin >=".$db->idate($date);
$res = $objContract->fetchAll('DESC','date_fin',0,0,array(),'AND',$filter);
if ($res > 0)
{
	$lines = $objContract->lines;
	foreach($lines AS $j => $line)
	{
		$aMember[$line->fk_proces][$line->fk_user]['ref'] = $line->ref;
		$aMember[$line->fk_proces][$line->fk_user]['date_ini'] = $line->date_ini;
		$aMember[$line->fk_proces][$line->fk_user]['date_fin'] = $line->date_fin;
	}
	$objProcess = new Pproces($db);
	print '<table class="formdoc" width="100%">';
	print '<tr class="liste_titre"><td colspan="4">'.$langs->trans('Nextcontractstoexpire').'</td></tr>';
	print '<tr class="liste_titre"><td>'.$langs->trans('Proces').'</td><td>'.$langs->trans('Name').'</td><td align="center">'.$langs->trans('Dateini').'</td><td align="center">'.$langs->trans('Datefin').'</td></tr>';
	$var = true;
	$sumValue=0;
	foreach ((array) $aMember AS $fk_proces => $aData)
	{
		$objProcess->fetch($fk_proces);
		foreach ($aData AS $fk_member => $data)
		{
			$objAdherent->fetch($fk_member);
			$var=!$var;
			print '<tr '.$bc[$var].'>';
			print '<td>'.$objProcess->label.'</td>';
			print '<td>'.$objAdherent->getNomUrl(1).' '.$objAdherent->lastname.' '.$objAdherent->firstname.'</td>';
			print '<td align="center">'.dol_print_date($data['date_ini'],'day').'</td>';
			print '<td align="center">'.dol_print_date($data['date_fin'],'day').'</td>';
			print '</tr>';

		}
	}
	print '</table>';
}
$res = $objContract->fetchAll('DESC','date_ini',10,1,array('state'=>1),'AND');
if ($res > 0)
{
	$lines = $objContract->lines;
	print '<table class="formdoc" width="100%">';
	print '<tr class="liste_titre"><td>'.$langs->trans('Lasttencontractssigned').'</td><td align="right">'.$langs->trans('Dateini').'</td></tr>';
	$var = true;

	foreach ($lines AS $j => $line)
	{
		$objAdherent->fetch($line->fk_user);
		print '<tr><td>'.$objAdherent->getNomUrl(1).' '.$objAdherent->lastname.' '.$objAdherent->firstname.'</td><td align="right">'.dol_print_date($line->date_ini,'day').'</td></tr>';
	}
	print '</table>';
}

print '</div></div></div>';

$db->close();

llxFooter();
?>
