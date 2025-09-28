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
dol_include_once('/assistance/class/assistanceext.class.php');
dol_include_once('/assistance/class/licencesext.class.php');
dol_include_once('/assistance/class/ctypelicenceext.class.php');
dol_include_once('/assistance/class/adherentext.class.php');

$langs->load("assistance");
$langs->load("stocks");

$cancel = GETPOST('cancel');
$year = GETPOST('year');
$action = GETPOST('action');

if (isset($_POST['year'])) $_SESSION['period_year'] = $_POST['year'];

if (!$user->rights->assistance->read) accessforbidden();

$object = new Assistanceext($db);


llxHeader("",$langs->trans("Assistance"),$help_url);

$form=new Form($db);

print load_fiche_titre($langs->trans("Assistance"),'','title_commercial.png');

print '<div class="fichecenter">';
print '<div class="fichethirdleft">';
/*
 * Resumen de registro de asistencia
 */
$aDate = dol_getdate(dol_now());
$aYear = array();
if ($conf->assistance->enabled && $user->rights->assistance->mem->rev)
{
	$aData = array();
	$min = $aDate['year']-10;
	$max = $aDate['year']+10;
	for ($a=$min; $a <= $max; $a++)
	{
		$aYear[$a] = $a;
	}
	$filter = " AND YEAR(t.date_ass) = ".($year?$year:$aDate['year']);
	$res = $object->fetchAll('ASC','t.statut',0,0,array(1=>1),'AND',$filter);
	if ($res > 0)
	{
		$lines = $object->lines;
		foreach ($lines AS $j => $line)
		{
			$aDate = dol_getdate($line->date_ass);
			$aData[$aDate['mon']][$aDate['mday']][$line->statut]++;
		}
	}
	$num = count($aData);
	$total = 0;
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

	print '<form name="formind" method="POST" action="'.$_SERVER['PHP_SELF'].'">';

	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre">';
	print '<th colspan="4">'.$langs->trans("Estado revisión de asistencia").($num?' <span class="badge">'.$num.' '.$langs->trans('Month').'</span>':'').'&nbsp;'.$form->selectarray('year',$aYear,(GETPOST('year')?GETPOST('year'):$aDate['year'])).'</th></tr>';
	print '<tr class="liste_titre"><th>'.$langs->trans("Month").'</th>';
	print '<th>'.$langs->trans("Day").'</th>';
	print '<th align="right">'.$langs->trans("Status").'</th>';
	print '<th align="right">'.$langs->trans("Registered").'</th>';
	print '</tr>';
	if ($num > 0)
	{
		$i = 0;
		$var=true;
		foreach ($aData AS $mon => $aDataday)
		{

			foreach ($aDataday AS $day => $data)
			{
				$nStatus = 0;
				foreach ($data AS $status => $value)
				{
					$nStatut = $value;
					$search_date = dol_mktime(12,0,0,(strlen($mon)==1?'0'.$mon:$mon), (strlen($day)==1?'0'.$day:$day),(GETPOST('year')?GETPOST('year'):$aDate['year']));

					$var=!$var;
					print '<tr '.$bc[$var].'><td  class="nowrap">';
					print '<a href="'.DOL_URL_ROOT.'/assistance/assistance/list.php?search_date='.$search_date.'&rev=1'.'" title="'.$langs->trans('Seerevisionmarkings').'"">'.$mon.'</a>';
					print '</td>';
					print '<td class="nowrap" align="center">';
					print '<a href="'.DOL_URL_ROOT.'/assistance/assistance/list.php?search_date='.$search_date.'&rev=1'.'" title="'.$langs->trans('Seerevisionmarkings').'"">'.$day.'</a>';
					print '</td>';
					print '<td class="nowrap" align="right">';
					$object->statut = $status;
					print $object->getLibStatut(6);
					print '</td>';
					print '<td class="nowrap" align="right">';
					print $nStatut;
					print '</td>';
					print '</tr>';
					$i++;
					$total += $obj->total;

				}
			}
		}
		if ($total>0)
		{
			$var=!$var;
			print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td><td align="right">'.$total."</td></tr>";
		}
	}
	else
	{
		$var=!$var;
		print '<tr '.$bc[$var].'><td colspan="2" class="opacitymedium">'.$langs->trans("Norecords").'</td></tr>';
	}
	print "</table><br>";
	print '</form>';

}

if ($lView)
{
	print '<br>';
	$sql = " SELECT p.rowid, p.ref, p.label, ";
	$sql.= " ps.reel, ps.fk_entrepot, ";
	$sql.= " e.label AS entrepotlabel ";

	$sql.= " FROM ".MAIN_DB_PREFIX."product as p ";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product_stock AS ps ON p.rowid = ps.fk_product";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."entrepot AS e ON ps.fk_entrepot = e.rowid";
	$sql.= " WHERE ps.reel <= p.seuil_stock_alerte ";
	$sql.= " AND p.entity = ".$conf->entity;
	$sql.= " ORDER BY ps.reel ASC, p.ref ";

	$resql = $db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$now=dol_now();

		$i = 0;
		print '<table class="noborder" width="100%">';
		print '<tr class="liste_titre">';
		print '<td colspan="4">'.$langs->trans("Productos en alerta minima por almacen").($num?' <span class="badge">'.$num.'</span>':'').'</td></tr>';

		while ($i < $num)
		{
			$var = !$var;
			$obj = $db->fetch_object($resql);
			$product->id = $obj->rowid;
			$product->ref = $obj->ref;
			$product->label = $obj->label;

			$entrepot->id = $obj->fk_entrepot;
			$entrepot->label = $obj->entrepotlabel;
			print '<tr '.$bc[$var].'>';
			print '<td>'.$product->getNomUrl(1).'</td>';
			print '<td>'.$obj->label.'</td>';
			print '<td>'.$entrepot->getNomUrl(1).'</td>';
			print '<td>'.$obj->reel.'</td>';
			print '</tr>';

			$i++;
		}
		print '</table>';
	}
}
print '</div>';
print '<div class="fichetwothirdright">';
//numero de licencias solicitadas en lagestion
$filter = " AND YEAR (t.date_ini) = ".($year?$year:date('Y'));
$objLicence = new Licencesext($db);
$objAdherent = new Adherentext($db);
$res = $objLicence->fetchAll('','',0,0,array(),'AND',$filter);
if ($res > 0)
{
	$objTypelicence = new Ctypelicenceext($db);
	foreach ($objLicence->lines AS $j => $line)
	{
		$objTypelicence->fetch(0,$line->type_licence);
		if ($objTypelicence->type == 'L')
			$aLicence[$objTypelicence->label]++;
		else
			$aVacation[$objTypelicence->label]++;

		$aLicencemember[$objTypelicence->label][$line->fk_member]++;
		if ($line->statut>=3 && $objTypelicence->type=='V')
		{
			if ($line->date_fin_ejec > dol_now())
				$aUservacation[$line->fk_member]['date'] = $date_fin_ejec;
		}
	}
	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre">';
	$num = count($aLicence);
	print '<td colspan="2">'.$langs->trans("Licencias solicitadas en la gestión").' '.($year?$year:date('Y')).($num?' <span class="badge">'.$num.'</span>':'').'</td></tr>';

	foreach ((array) $aLicence AS $type_licence => $value)
	{
		$var = !$var;
		print '<tr '.$bc[$var].'>';
		print '<td>'.$type_licence.'</td>';
		print '<td align="right">'.$value.'</td>';
		print '</tr>';

		$i++;
	}
	print '</table>';

	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre">';
	$num = count($aVacation);
	print '<td colspan="2">'.$langs->trans("Vacaciones solicitadas en la gestión").' '.($year?$year:date('Y')).($num?' <span class="badge">'.$num.'</span>':'').'</td></tr>';

	foreach ((array) $aVacation AS $type_licence => $value)
	{
		$var = !$var;
		print '<tr '.$bc[$var].'>';
		print '<td>'.$type_licence.'</td>';
		print '<td align="right">'.$value.'</td>';
		print '</tr>';

		$i++;
	}
	print '</table>';

	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre">';
	$num = count($aUservacation);
	print '<td colspan="2">'.$langs->trans("Personal haciendo uso de vacación").' '.($year?$year:date('Y')).($num?' <span class="badge">'.$num.'</span>':'').'</td></tr>';

	foreach ((array) $aUservacation AS $fk_member => $data)
	{
		$objAdherent->fetch($fk_member);
		$var = !$var;
		print '<tr '.$bc[$var].'>';
		print '<td>'.$objAdherent->getNomUrl(1).' '.$objAdherent->lastname.' '.$objAdherent->firstname.'</td>';
		print '<td align="right">'.dol_print_date($data['date'],'day').'</td>';
		print '</tr>';

		$i++;
	}
	print '</table>';

}
print '</div>';

print '</div>';

$db->close();
llxFooter();
?>
