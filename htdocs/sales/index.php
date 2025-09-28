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
dol_include_once('/sales/class/commandeext.class.php');
dol_include_once('/sales/class/propalext.class.php');
dol_include_once('/sales/class/factureext.class.php');
dol_include_once('/societe/class/societe.class.php');
dol_include_once('/core/class/html.formv.class.php');

dol_include_once('/core/lib/date.lib.php');
//$langs->load("stocks");
$langs->load("sales");

if (!$user->rights->sales->lire)
	accessforbidden();

list($country,$countrycod,$countryname) = explode(':',$conf->global->MAIN_INFO_SOCIETE_COUNTRY) ;
//search last exchange rate
// $objectcop = new Csindexes($db);
// $objectcop->fetch_last($country);

// if ($objectcop->date_ind <> $db->jdate(date('Y-m-d')))
//   {
//     header("Location: ".DOL_URL_ROOT.'/wages/exchangerate/fiche.php?action=create');
//     exit;
//   }

llxHeader("",$langs->trans("Sales"),$help_url);


print_fiche_titre($langs->trans("Sales"));

$form = new Formv($db);

//print '<table border="0" width="100%" class="notopnoleftnoright">';
//print '<tr><td valign="top" width="30%" class="notopnoleft">';
$aDate = dol_getdate(dol_now());
$aYear = array();
$aData = array();
$min = $aDate['year']-5;
$max = $aDate['year']+5;
for ($a=$min; $a <= $max; $a++)
{
	$aYear[$a] = $a;
}
if (! empty($conf->use_javascript_ajax))
{
	print "\n".'<script type="text/javascript">';
	print '$(document).ready(function () {';
	print '$("#year").change(function() {
		document.formind.action.value="create";
		document.formind.submit();
	});';
	print '});';
	print '</script>'."\n";
}

$year = GETPOST('year');
print '<form name="formind" method="POST" action="'.$_SERVER['PHP_SELF'].'">';

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<th colspan="4">'.$langs->trans("Resumen de ventas").'&nbsp;'.$form->selectarray('year',$aYear,(GETPOST('year')?GETPOST('year'):$aDate['year'])).'</th></tr>';

print "</table><br>";
print '</form>';

print '<div class="fichecenter"><div class="fichethirdleft">';


// Orders
$objCommande = new Commandeext($db);
$res = $objCommande->fetchAll('DESC','c.date_commande',0,5,array(),'AND');
if ($res)
{
	$num = count($objCommande->lines);
	$i = 0;

	print '<table class="formdoc" width="100%">';
	print '<tr class="liste_titre"><td>'.'<span class="badge">'.$num.'</span> '.$langs->trans("Lastorders").'</td><td align="center">'.$langs->trans("Date").'</td><td align="right">'.$langs->trans('Status').'</td><td>&nbsp;</td>';
	print "</tr>\n";
	$var=True;
	$lines = $objCommande->lines;
	foreach ($lines AS $j => $line)
	{
		$var=!$var;
		$objCommande->statut = $line->statut;
		$objCommande->id = $line->id;
		$objCommande->ref = $line->ref;
		print "<tr ".$bc[$var].">";
		print '<td>'.$objCommande->getNomUrladd(1).'</td>';
		print '<td align="center">'.dol_print_date($line->date_commande,'day').'</td>';
		print '<td align="right">'.$objCommande->getLibStatut(2).'</td>';
		print '<td align="center"></td>';
		print "</tr>\n";
		$i++;
	}
	print "</table>";
	print "<br>\n";
}

// propal
$objPropal = new Propalext($db);
$res = $objPropal->fetchAll('DESC','p.datep',0,5,array(),'AND');
if ($res)
{
	$num = count($objPropal->lines);
	$i = 0;

	print '<table class="formdoc" width="100%">';
	print '<tr class="liste_titre"><td>'.'<span class="badge">'.$num.'</span> '.$langs->trans("Lastbudget").'</td><td align="center">'.$langs->trans("Date").'</td><td align="right">'.$langs->trans('Status').'</td><td>&nbsp;</td>';
	print "</tr>\n";
	$var=True;
	$lines = $objPropal->lines;
	foreach ($lines AS $j => $line)
	{
		$var=!$var;
		$objPropal->statut = $line->statut;
		$objPropal->id = $line->id;
		$objPropal->ref = $line->ref;
		$objPropal->ref_client = $line->ref_client;
		$objPropal->total_ttc = $line->total_ttc;

		print "<tr ".$bc[$var].">";
		print '<td>'.$objPropal->getNomUrladd(1).'</td>';
		print '<td align="center">'.dol_print_date($line->date,'day').'</td>';
		print '<td align="right">'.$objPropal->getLibStatut(2).'</td>';
		print '<td align="center"></td>';
		print "</tr>\n";
		$i++;
	}
	print "</table>";
	print "<br>\n";
}

// facture
$objFacture = new Factureext($db);
$objSociete = new Societe($db);
 $res = $objFacture->fetchAll('DESC','f.datef',0,0,array(),'AND');
if ($res)
{
	$lines = $objFacture->lines;
	$num = count($lines);
	$i = 0;

	print '<table class="formdoc" width="100%">';
	print '<tr class="liste_titre"><td>'.'<span class="badge">'.$num.'</span> '.$langs->trans("Lastfacture").'</td><td align="center">'.$langs->trans("Client").'</td><td align="center">'.$langs->trans("Date").'</td><td align="right">'.$langs->trans('Status').'</td><td>&nbsp;</td>';
	print "</tr>\n";
	$var=True;
	foreach ($lines AS $j => $line)
	{
		$var=!$var;
		$objFacture->statut = $line->statut;
		$objFacture->id = $line->id;
		$objFacture->ref = $line->ref;
		$objFacture->ref_client = $line->ref_client;
		$objFacture->total_ttc = $line->total_ttc;
		$objSociete->fetch($line->fk_soc);
		print "<tr ".$bc[$var].">";
		print '<td>'.$objFacture->getNomUrladd(1).'</td>';
		print '<td>'.$objSociete->getNomUrl(1).'</td>';
		print '<td align="center">'.dol_print_date($line->date,'day').'</td>';
		print '<td align="right">'.$objFacture->getLibStatut(2).'</td>';
		print '<td align="center"></td>';
		print "</tr>\n";
		$i++;
	}
	print "</table>";
	print "<br>\n";
}
print '</div><div class="fichetwothirdright"><div class="ficheaddleft">';
//lista de facturas
$objFacture = new Factureext($db);
$filter = " AND YEAR(f.datef) = ".($year?$year:date('Y'));
$res = $objFacture->fetchAll('','',0,0,array(),'AND',$filter);
if ($res >0)
{
	$lines = $objFacture->lines;
	foreach ($lines AS $j => $line)
	{
		$aFacturenumaut[$line->num_autoriz]+=$line->total_ttc;
		$aFactureactivity[$line->activity]+=$line->total_ttc;
		$aDate = dol_getdate($line->datef);

		$aFacturemonth[$aDate['mon']]+=$line->total_ttc;
	}
	print '<table class="formdoc" width="100%">';
	print '<tr class="liste_titre"><td>'.$langs->trans("Billingbyactivity").' '.($year?$year:date('Y')).'</td><td align="right">'.$langs->trans("Amount").'</td><td>&nbsp;</td>';
	print "</tr>\n";
	$var=True;
	$sumTotal = 0;
	foreach ($aFactureactivity AS $activity => $value)
	{
		$var=!$var;
		print "<tr ".$bc[$var].">";
		print '<td>'.($activity?$langs->trans($activity):$langs->trans('Others')).'</td>';
		print '<td align="right">'.price(price2num($value,'MT')).'</td>';
		$sumTotal+=$value;
		print "</tr>\n";
	}
	print '<tr class="liste_total">';
	print '<td>'.$langs->trans('Total').'</td>';
	print '<td align="right">'.price(price2num($sumTotal,'MT')).'</td>';
	print "</tr>\n";

	print "</table>";
	print "<br>\n";

	$aMonth = monthArray($langs,0);
	print '<table class="formdoc" width="100%">';
	print '<tr class="liste_titre"><td>'.$langs->trans("Monthlyinvoicing").' '.($year?$year:date('Y')).'</td><td align="right">'.$langs->trans("Amount").'</td><td>&nbsp;</td>';
	print "</tr>\n";
	$var=True;
	$sumTotal = 0;
	foreach ($aFacturemonth AS $month => $value)
	{
		$var=!$var;
		print "<tr ".$bc[$var].">";
		print '<td>'.$aMonth[$month].'</td>';
		print '<td align="right">'.price(price2num($value,'MT')).'</td>';
		$sumTotal+=$value;
		print "</tr>\n";
	}
	print '<tr class="liste_total">';
	print '<td>'.$langs->trans('Total').'</td>';
	print '<td align="right">'.price(price2num($sumTotal,'MT')).'</td>';
	print "</tr>\n";

	print "</table>";
	print "<br>\n";
}


print '</div></div></div>';

$db->close();

llxFooter();
?>
