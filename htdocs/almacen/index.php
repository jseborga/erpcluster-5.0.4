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
dol_include_once('/almacen/class/contabperiodo.class.php');
dol_include_once('/almacen/class/solalmacenext.class.php');
dol_include_once('/product/class/product.class.php');
dol_include_once('/product/stock/class/entrepot.class.php');

$langs->load("stocks");
$langs->load("almacen@almacen");

$cancel = GETPOST('cancel');
$year = GETPOST('year');
$action = GETPOST('action');

if (isset($_POST['year'])) $_SESSION['period_year'] = $_POST['year'];

if (!$user->rights->almacen->lirealm) accessforbidden();

$object = new Contabperiodo($db);
$objalm = new Solalmacenext($db);
$product = new Product($db);
$entrepot = new Entrepot($db);

list($country,$countrycod,$countryname) = explode(':',$conf->global->MAIN_INFO_SOCIETE_COUNTRY) ;
//search last exchange rate
// $objectcop = new Csindexes($db);
// $objectcop->fetch_last($country);

// if ($objectcop->date_ind <> $db->jdate(date('Y-m-d')))
//   {
//     header("Location: ".DOL_URL_ROOT.'/wages/exchangerate/fiche.php?action=create');
//     exit;
//   }

llxHeader("",$langs->trans("Almacenes"),$help_url);

if ($conf->global->ALMACEN_FILTER_YEAR)
{
	if ($user->rights->almacen->gest->write)
	{
	if (!isset($_SESSION['period_year']) || $action == 'modify')
	{
		$options = '';
		$filterstatic = '';
		$object->fetchAll('DESC', 'period_year', 0, 0, array('statut'=>1), 'AND');
		$aYear = array();
		foreach ((array) $object->lines AS $j => $line)
			$aYear[$line->period_year] = $line->period_year;
		foreach ((array) $aYear AS $year)
		{
			$selected = '';
			if ($_SESSION['period_year'] == $year)
				$selected = 'selected';
			$options.= '<option value="'.$year.'" '.$selected.'>'.$year.'</option>';
		}
		print load_fiche_titre($langs->trans("Selectthegestion"));

		print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
		print '<input type="hidden" name="action" value="add">';
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

		dol_fiche_head();

		print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	//
		print '<tr><td class="fieldrequired">'.$langs->trans("Year").'</td><td>';
		print '<select name="year">'.$options.'</select>';
		print '</td></tr>';

		print '</table>';

		dol_fiche_end();

		print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
		print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
		print '</div>';

		print '</form>';

	}
	else
	{
		$period_year = $_SESSION['period_year'];

		print '<div><h2>'.$langs->trans('Selectedyear').': '.$period_year.'</h2></div>';
		print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=modify">'.$langs->trans("Changeyear").'</a></div>'."\n";
	}
	}
	else
	{
		if (!isset($_SESSION['period_year']))
			$_SESSION['period_year'] = date('Y');
		$period_year = $_SESSION['period_year'];
		print '<div><h2>'.$langs->trans('Selectedyear').': '.$period_year.'</h2></div>';

	}
}
else
	$_SESSION['period_year'] = date('Y');

print load_fiche_titre($langs->trans("Area Almacen"),'','title_commercial.png');

print '<div class="fichecenter">';
print '<div class="fichethirdleft">';
$lView = true;
if ($conf->global->ALMACEN_FILTER_YEAR && empty($_SESSION['period_year'])) $lView = false;
/*
 * Draft supplier proposals
 */
if (! empty($conf->almacen->enabled))
{
	$langs->load("almacen");
	if ($lView)
	{
		$sql = "SELECT p.statut, count(p.rowid) AS total";
		$sql.= " FROM ".MAIN_DB_PREFIX."sol_almacen as p";
		$sql.= " WHERE p.entity IN (".getEntity('sol_almacen', 1).")";
		if ($cong->global->ALMACEN_FILTER_YEAR)
			$sql.= " AND year(p.date_creation) = ".$_SESSION['period_year'];
		$sql.= " GROUP BY p.statut";

		$resql=$db->query($sql);
		if ($resql)
		{
			$total = 0;
			$num = $db->num_rows($resql);

			print '<table class="noborder" width="100%">';
			print '<tr class="liste_titre">';
			print '<td colspan="2">'.$langs->trans("Estado de pedidos a Almacen").($num?' <span class="badge">'.$num.'</span>':'').'</td></tr>';

			if ($num > 0)
			{
				$i = 0;
				$var=true;
				while ($i < $num)
				{
					$obj = $db->fetch_object($resql);
					$objalm->statut = $obj->statut;

					$var=!$var;
					print '<tr '.$bc[$var].'><td  class="nowrap">';
					print '<a href="'.DOL_URL_ROOT.'/almacen/liste.php?search_statut='.$obj->statut.'">'.$objalm->getLibStatut(1).'</a>';
					print '</td>';
					print '<td class="nowrap" align="right">';
					print $obj->total;
					print '</td>';
					print '</tr>';
					$i++;
					$total += $obj->total;
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
				print '<tr '.$bc[$var].'><td colspan="2" class="opacitymedium">'.$langs->trans("NoProposal").'</td></tr>';
			}
			print "</table><br>";

			$db->free($resql);
		}
		else
		{
			dol_print_error($db);
		}
	}
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

$max = 5;
if (! empty($conf->almacen->enabled))
{
	$langs->load("almacen");
	if ($lView)
	{
		$sql = "SELECT p.rowid, p.ref, p.label, d.qty, s.date_creation, s.ref AS solref, s.rowid AS solrowid ";
		$sql.= " FROM ".MAIN_DB_PREFIX."sol_almacen as s";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."sol_almacendet as d ON d.fk_almacen = s.rowid ";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product as p ON d.fk_product = p.rowid ";
		$sql.= " WHERE p.entity IN (".getEntity('sol_almacen', 1).")";
		if ($cong->global->ALMACEN_FILTER_YEAR)
			$sql.= " AND year(p.date_creation) = ".$_SESSION['period_year'];
		$sql.= " ORDER BY s.date_creation DESC";
		$sql .= $db->plimit($max, 0);

		$resql=$db->query($sql);
		if ($resql)
		{
			$num = $db->num_rows($resql);

			print '<table class="noborder" width="100%">';
			print '<tr class="liste_titre">';
			print '<td colspan="4">'.$langs->trans("Ultimos 5 productos solicitados").($num?' <span class="badge">'.$num.'</span>':'').'</td></tr>';

			if ($num > 0)
			{
				$i = 0;
				$var=true;
				while ($i < $num)
				{
					$obj = $db->fetch_object($resql);
					$objalm->id = $obj->solrowid;
					$objalm->ref = $obj->solref;

					$product->id = $obj->rowid;
					$product->ref = $obj->ref;

					$var=!$var;
					print '<tr '.$bc[$var].'>';
					print '<td  class="nowrap">';
					print $objalm->getNomUrl(1);
					print '</td>';
					print '<td  class="nowrap">';
					print dol_print_date($obj->date_creation,'dayhour');
					print '</td>';
					print '<td  class="nowrap">';
					print $product->getNomUrl(1);
					print '</td>';
					print '<td class="nowrap" align="left">';
					print dol_trunc($obj->label,40);
					print '</td>';
					print '</tr>';
					$i++;
				}
			}
			else
			{
				$var=!$var;
				print '<tr '.$bc[$var].'><td colspan="2" class="opacitymedium">'.$langs->trans("NoProducts").'</td></tr>';
			}
			print "</table><br>";

			$db->free($resql);
		}
		else
		{
			dol_print_error($db);
		}
	}
}

print '</div>';

print '</div>';

$db->close();
llxFooter();
?>
