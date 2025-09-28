<?php
/* Copyright (C) 2017-2017 Ramiro Queso<ramiroques@gmail.com>
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
 *       \file       htdocs/orgman/index.php
 *       \ingroup    Orgman
 *       \brief      Page of orgman
 */

require("../main.inc.php");

dol_include_once('/orgman/class/pdepartament.class.php');
dol_include_once('/orgman/class/mproperty.class.php');
dol_include_once('/orgman/class/pcharge.class.php');
dol_include_once('/orgman/class/cpartida.class.php');
dol_include_once('/orgman/class/partidaproduct.class.php');
dol_include_once('/product/class/product.class.php');
dol_include_once('/core/class/html.formv.class.php');

dol_include_once('/core/lib/date.lib.php');
//$langs->load("stocks");
$langs->load("assets");

if (!$user->rights->orgman->lire) accessforbidden();

//$object = new Contabperiodo($db);
//$objalm = new Solalmacen($db);
//$product = new Product($db);

$objPdepartament 	 = new Pdepartament($db);
$objMproperty    	 = new Mproperty($db);
$objPchargue     	 = new Pcharge($db);
$objCpartida     	 = new Cpartida($db);
$objPartidaproduct   = new Partidaproduct($db);
$objProductos    	 = new Product($db);

llxHeader("",$langs->trans("Orgman"),$help_url);

list($country,$countrycod,$countryname) = explode(':',$conf->global->MAIN_INFO_SOCIETE_COUNTRY) ;
print_fiche_titre($langs->trans("Assets"));

$form = new Formv($db);

/*Objetos para el index*/

//print '<table border="0" width="100%" class="notopnoleftnoright">';
//print '<tr><td valign="top" width="30%" class="notopnoleft">';
$aDate = dol_getdate(dol_now());

//$fechaActual = getdate();
$fechaActual = dol_getdate(dol_now());

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

$year = $fechaActual[year];
$months = $fechaActual[mon];


// Ultimos 5 Departamentos creados

print '<div class="fichecenter"><div class="fichethirdleft">';
print '<table class="formdoc" width="100%">';
print '<tr class="liste_titre"><td colspan = "2">'.$langs->trans("Lastdepartments").'</span></td>';
print '</tr>';

print "<tr class=\"liste_titre\">";
print_liste_field_titre($langs->trans("Ref"),"", "","","","");
print_liste_field_titre($langs->trans("Label"),"", "",'','','');
print "</tr>\n";

$rP = $objPdepartament->fetchAll('DESC','rowid',4,0,array(),'AND');

if($rP > 0){
	$var = true;
	$obj = $objPdepartament->lines;
	foreach ($obj as $key => $value) {

		print '<tr '.$bc[$var].'>';
		$objPdepartament->id = $value->id;
	    $objPdepartament->ref = $value->ref;
	   	print '<td>'.$objPdepartament->getNomUrl(1).'</td>';
		print '<td>'.$value->label.'</td>';
	    print '</tr>';
	   $var     = !$var;
	}
}
print '</table>';

print '<br><br><br>';


// Ultimas 5 propiedades registradas

//print '<div class="fichecenter"><div class="fichethirdleft">';
print '<table class="formdoc" width="100%">';
print '<tr class="liste_titre"><td colspan = "2">'.$langs->trans("Lastproperties").'</span></td>';
print '</tr>';

print "<tr class=\"liste_titre\">";
print_liste_field_titre($langs->trans("Ref"),"", "","","","");
print_liste_field_titre($langs->trans("Label"),"", "",'','','');
print "</tr>\n";

$rM = $objMproperty->fetchAll('DESC','rowid',4,0,array(),'AND');

if($rM > 0){
	$var = true;
	$obj = $objMproperty->lines;
	foreach ($obj as $key => $value) {

		print '<tr '.$bc[$var].'>';
		$objMproperty->id = $value->id;
	    $objMproperty->ref = $value->ref;
	   	print '<td>'.$objMproperty->getNomUrl(1).'</td>';
		print '<td>'.$value->label.'</td>';
	    print '</tr>';
	   $var     = !$var;
	}
}
print '</table>';

/****************************************************************/
print '</div><div class="fichetwothirdright"><div class="ficheaddleft">';
/****************************************************************/


// Ultimos 5 cargos creados

print '<table class="formdoc" width="100%">';
print '<tr class="liste_titre"><td colspan = "2">'.$langs->trans("Lastcharge").'</span></td>';
print '</tr>';

print "<tr class=\"liste_titre\">";
print_liste_field_titre($langs->trans("Ref"),"", "","","","");
print_liste_field_titre($langs->trans("Label"),"", "",'','','');
print "</tr>\n";

$rM = $objPchargue->fetchAll('DESC','rowid',4,0,array(),'AND');

if($rM > 0){
	$var = true;
	$obj = $objPchargue->lines;
	foreach ($obj as $key => $value) {

		print '<tr '.$bc[$var].'>';
		$objPchargue->id = $value->id;
	    $objPchargue->ref = $value->ref;
	   	print '<td>'.$objPchargue->getNomUrl(1).'</td>';
		print '<td>'.$value->label.'</td>';
	    print '</tr>';
	   $var     = !$var;
	}
}
print '</table>';

//print '<div class="fichecenter"><div class="fichethirdleft">';

print '<br><br><br>';

// Ultimos 5 partidas

print '<table class="formdoc" width="100%">';
print '<tr class="liste_titre"><td colspan = "2">'.$langs->trans("Unrelatedproducts").'</span></td>';
print '</tr>';

print "<tr class=\"liste_titre\">";
print_liste_field_titre($langs->trans("Ref"),"", "","","","");
print_liste_field_titre($langs->trans("Label"),"", "",'','','');
print "</tr>\n";

$sql .= " SELECT ";
$sql .= " t.rowid, t.ref, t.label,t.fk_product_type ";
$sql.= " FROM ".MAIN_DB_PREFIX."product as t ";

$sql.= " WHERE t.fk_product_type = 0 ";

//echo $sql;

$nbtotalofrecords = '';

$result = $db->query($sql);
$nbtotalofrecords = $db->num_rows($result);
$j = 0;
$limit = 1;

while ($j < $nbtotalofrecords)
{	$var = true;
	$obj = $db->fetch_object($result);

	if ($obj)
	{
		$rX = $objPartidaproduct->fetch($obj->rowid);
		if($rX == 0 && $limit <= 15){
			print '<tr '.$bc[$var].'>';
			$objProductos->id = $obj->rowid;
			$objProductos->ref = $obj->ref;
			print '<td>'.$objProductos->getNomUrl(1).'</td>';
			print '<td>'.$obj->label.'</td>';
			print '</tr>';
		   $var     = !$var;
		   $limit++;
		}

	}
	$j++;
}
print '</table>';



print '</div></div></div>';

$db->close();

llxFooter();
?>
