<?php
/* Copyright (C) 2007-2008 Jeremie Ollivier    <jeremie.o@laposte.net>
 * Copyright (C) 2011      Laurent Destailleur <eldy@users.sourceforge.net>
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
require_once(DOL_DOCUMENT_ROOT.'/user/class/user.class.php');


require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
dol_include_once('/user/class/user.class.php');
dol_include_once('/almacen/class/entrepotext.class.php');
dol_include_once('/almacen/class/stockmouvementtempext.class.php');
dol_include_once('/almacen/class/stockmouvementdocext.class.php');
dol_include_once('/product/class/product.class.php');
$langs->load('almacen');
$langs->load("main");
header("Content-type: text/html; charset=".$conf->file->character_set_client);

$facid=GETPOST('facid','int');
$nro=GETPOST('ref','alpha');


$object = new Stockmouvementdocext($db);
$objectdet = new Stockmouvementtempext($db);
$objEntrepot = new Entrepotext($db);
$objProduct =new Product($db);

$res = $object->fetch($facid);
if (!$res) exit;
$objEntrepot->fetch($object->fk_entrepot_from);
$origen = $objEntrepot->lieu;
$date = dol_print_date($object->datem,'day');
$filter = " AND t.ref = '".$object->ref."'";
$filter.= " AND t.value > 0";
$aTransfer = array();
$aTransferid = array();
$res = $objectdet->fetchAll('','',0,0,array(),'AND',$filter);
if ($res>0)
{
	$lines = $objectdet->lines;
	foreach ($lines AS $j => $line)
	{
		$objEntrepot->fetch($line->fk_entrepot);
		$aTransfer[$line->ref][$objEntrepot->lieu][$line->fk_product] = $line->value;
	}
}

// Recuperation et affichage de la date et de l'heure
$now = dol_now();

include DOL_DOCUMENT_ROOT.'/almacen/transferencia/tpl/ticket_header_text.tpl.php';

$html.=  '<div class="master">';

foreach ($aTransfer AS $nro => $aData)
{
	foreach ($aData AS $destino => $data)
	{
		$html.=  '<div class="entete">';
		$html.=  '<div class="infos">';
		$html.=  '<p class="text13 center">';
		$html.=  '*** '.$langs->trans('Transferproduct').' ***';
		$html.=  '<br>';
		$html.=  '----------------------------';
		$html.=  '<br>';
		$html.=  $langs->trans('Nro.:').' '.$nro;
		$html.=  '<br>';
		$html.=  $langs->trans('Date').' '.$date;
		$html.=  '<br>';
		$html.=  $langs->trans('Origen').': '.$origen;
		$html.=  '<br>';
		$html.=  $langs->trans('Destino').': <b>'.$destino.'</b>';
		$html.=  '</p>';
		$html.=  '</div>';
		$html.=  '</div>';

		$html.=  '<table class="liste_articles">';
		$html.=  '<tr class="titres">';
		$html.=  '<th width="19px">'.$langs->trans("Code").'</th>';
		$html.=  '<th width="190px">'.$langs->trans("Label").'</th>';
		$html.=  '<th width="50px">'.$langs->trans("Qty").'</th>';
		$html.=  '<th width="50px">'.$langs->trans("Verif").'</th>';
		$html.=  '</tr>';

		$nProduct = 0;
		foreach ($data AS $fk_product => $qty)
		{
			$nProduct++;
			$objProduct->fetch($fk_product);
			$html.=  '<tr class="titrestd">';
			$html.=  '<td>'.$objProduct->ref.'</td>';
			$html.=  '<td align="left">'.$objProduct->label.'</td>';
			$html.=  '<td>'.price($qty).'</td>';
			$html.=  '<td align="right">__________</td>';
			$html.=  '</tr>';
		}
		$html.=  '<tr class="total"><td colspan="3" nowrap="nowrap">'.$langs->trans("Numberitems").'</td><td align="right" nowrap="nowrap" style="font-weight:bold;">'.price($nProduct)."</td></tr>\n";

		$html.=  '</table>';


		$html.=  '<div class="infos">';
		$html.=  '<p class="text13 center">';
		$html.=  '<br>';
		$html.= $langs->trans('Deliveraccording');
		$html.=  '<br>';
		$html.= '___________________________';
		$html.=  '<br>';
		$html.=  '<br>';
		$html.=  '<br>';
		$html.=  '<br>';
		$html.= $langs->trans('Receivedas');
		$html.=  '<br>';
		$html.= '___________________________';
		$html.=  '</p>';
		$html.=  '<p class="foother center">';
		$html.=  '<br>';
		$html.= dol_print_date(dol_now(),'dayhour');
		$html.=  '<br>';
		$html.= $user->login;
		$html.=  '</p>';
		$html.=  '<br>';
		$html.=  '<br>';
		$html.=  '<br>';
		$html.=  '<p class="textwhite">&nbsp;.</p>';
		$html.=  '</div>';

	}
}
include DOL_DOCUMENT_ROOT.'/almacen/transferencia/tpl/ticket_footer_text.tpl.php';

$resulthtml = $html;




print $resulthtml;

?>