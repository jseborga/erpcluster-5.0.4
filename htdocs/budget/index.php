<?php
/* Copyright (C) 7102 no one <email>
 *  Activos
 */


require("../main.inc.php");
require DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
dol_include_once('/core/class/html.formv.class.php');
dol_include_once('/budget/class/itemsext.class.php');
dol_include_once('/budget/class/budgetext.class.php');
dol_include_once('/budget/class/putypestructureext.class.php');
if ($conf->orgman->enabled)
{
	dol_include_once('/orgman/class/cregiongeographic.class.php');
	dol_include_once('/orgman/class/cclasfin.class.php');
}

dol_include_once('/core/lib/date.lib.php');
//$langs->load("stocks");
$langs->load("budget@budget");

list($country,$countrycod,$countryname) = explode(':',$conf->global->MAIN_INFO_SOCIETE_COUNTRY) ;

/*Objetos para el index*/

$objItems = new Itemsext($db);
$objProduct = new Product($db);
$objBudget = new Budgetext($db);
$objPutypestructure = new Putypestructure($db);
if ($conf->orgman->enabled)
{
	$objCregiongeographic = new Cregiongeographic($db);
	$objCclasfin = new Cclasfin($db);
}

if (isset($_POST['period_year']) || isset($_GET['period_year']))
	$_SESSION['period_year'] = GETPOST('period_year');
else
	$_SESSION['period_year'] = date('Y');

$period_year = $_SESSION['period_year'];

llxHeader("",$langs->trans("Budget"),$help_url);


print_fiche_titre($langs->trans("Budget"));

$form = new Formv($db);


$now = dol_now();
$aDate = dol_getdate($now);

//$fechaActual = getdate();
$fechaActual = dol_getdate($now);
$year = $fechaActual['year'];
$months = $fechaActual['mon'];


if (! empty($conf->use_javascript_ajax))
{
	print "\n".'<script type="text/javascript">';
	print '$(document).ready(function () {';
	print '$("#period_year").change(function() {
		document.formind.action.value="create";
		document.formind.submit();
	});';
	print '});';
	print '</script>'."\n";
}



print '<form name="formind" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="action" value="add">';
print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

dol_fiche_head();

print '<table class="border centpercent">'."\n";
	//
print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Select').' '.$langs->trans("Year").'</td><td>';
print '<input id="period_year" type="number" min="0" max="'.date('Y').'" name="period_year" value="'.$period_year.'">';
print '</td></tr>';

print '</table>';

dol_fiche_end();

print '</form>';

// Numero de Activos

print '<div class="fichecenter"><div class="fichethirdleft">';

// Numero de items segun el tipo
$aItemstatus=array();
$aItemlast=array();
$filter = "";
$res = $objItems->fetchAll('DESC','t.rowid',0,0,array(),'AND',$filter);
$num = 5;
$i = 0;
if ($res>0)
{
	$lines = $objItems->lines;
	foreach ($lines AS $j => $line)
	{
		$aItemstatus[$line->status]++;
		if ($i <$num)
		{
			$aItemlast[$line->id] = $line;
			$i++;
		}
	}
}
$aStatus=array(-1=>$langs->trans('Disabled'),0=>$langs->trans('Draft'),1=>$langs->trans('Enabled'),2=>$langs->trans('Approved'));


print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td colspan = "2">'.$langs->trans("Itemstatus").'</td>';
print '</tr>';
print "<tr class=\"liste_titre\">";
print_liste_field_titre($langs->trans("Status"),"", "",'','','');
print_liste_field_titre($langs->trans("Quant"),"", "","","","");
print "</tr>\n";

$j = 0;
foreach ($aItemstatus AS $status => $val)
{
	print '<tr '.$bc[$var].'>';
	print '<td>'.$aStatus[$status].'</td>';
	print '<td>'.$val.'</td>';
	print '</tr>';
}
print "</table>";
print '</br>';
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td colspan = "4">'.$num.' '.$langs->trans("Lastitems").'</td>';
print '</tr>';
print "<tr class=\"liste_titre\">";
print_liste_field_titre($langs->trans("Ref"),"", "",'','','');
print_liste_field_titre($langs->trans("Label"),"", "","","","");
print_liste_field_titre($langs->trans("Unit"),"", "","","","");
print_liste_field_titre($langs->trans("Date"),"", "","","","");
print "</tr>\n";

$j = 0;
foreach ($aItemlast AS $j => $obj)
{
	print '<tr '.$bc[$var].'>';
	$objItems->id = $obj->id;
	$objItems->ref = $obj->ref;
	$objItems->label = $obj->detail;
	$objProduct->fk_unit = $obj->fk_unit;
	print '<td>'.$objItems->getNomUrl().'</td>';
	print '<td>'.$obj->detail.'</td>';
	print '<td>'.$objProduct->getLabelOfUnit('short').'</td>';
	print '<td>'.dol_print_date($obj->datec,'day').'</td>';
	print '</tr>';
}
print "</table>";
$filter = '';
$res = $objPutypestructure->fetchAll('ASC','t.label',0,0,array(),'AND',$filter);
$aTypestructure = array();
if ($res>0)
{
	$lines = $objPutypestructure->lines;
	foreach ($lines AS $j => $line)
	{
		$aTypestructure[$line->code] = $line;
	}
}

print '</div><div class="fichetwothirdright"><div class="ficheaddleft">';

//lista de ultimos presupuestos creados
$filter = " AND YEAR(t.datec) = ".$period_year;
$res = $objBudget->fetchAll('DESC','t.rowid',0,0,array(),'AND',$filter);
$aBudgettype = array();
$aBudgetlast = array();
$aBudgetregion = array();
$aBudgetsector = array();
$i=0;
if ($res>0)
{
	$lines = $objBudget->lines;
	foreach ($lines AS $j => $line)
	{
		$aBudgettype[$line->type_structure]++;
		if ($i < $num)
		{
			$i++;
			$aBudgetlast[$line->id] = $line;
		}

		$aBudgetregion[$line->fk_region]++;
		$aBudgetsector[$line->fk_sector]++;
	}
}
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre"><td colspan = "7">'.$num.' '.$langs->trans("Budgetlast").'</td>';
print '</tr>';
print "<tr class=\"liste_titre\">";
print_liste_field_titre($langs->trans("Ref"),"", "",'','','');
print_liste_field_titre($langs->trans("Label"),"", "","","","");
print_liste_field_titre($langs->trans("Type"),"", "","","","");
print_liste_field_titre($langs->trans("Version"),"", "","","","");
print_liste_field_titre($langs->trans("Region"),"", "","","","");
print_liste_field_titre($langs->trans("Sector"),"", "","","","");
print_liste_field_titre($langs->trans("Budgetamount"),"", "",'','','align="right"');
print "</tr>\n";

$j = 0;

foreach((array) $aBudgetlast AS $j => $obj)
{
	$var=!$var;
	print '<tr '.$bc[$var].'>';
	$objBudget->id = $obj->id;
	$objBudget->ref = $obj->ref;
	$objBudget->label = $obj->title;
	print '<td>'.$objBudget->getNomUrl(1).'</td>';
	print '<td>'.$obj->title.'</td>';
	print '<td>'.$aTypestructure[$obj->type_structure]->code.'</td>';
	print '<td>'.$obj->version.'</td>';
	if ($obj->fk_region>0)
	{
		$objCregiongeographic->fetch($obj->fk_region);
		print '<td>'.$objCregiongeographic->getNomUrl().'</td>';
	}
	else
		print '<td>'.'</td>';
	if ($obj->fk_sector>0)
	{
		$objCclasfin->fetch($obj->fk_sector);
		print '<td>'.$objCclasfin->getNomUrl().'</td>';
	}
	else
		print '<td>'.'</td>';


	print '<td align="right">'.price(price2num($obj->budget_amount,'MT')).'</td>';
	print '</tr>';

}

print "</table>";

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre"><td colspan = "3">'.$langs->trans("Budgettype").'</td>';
print '</tr>';
print "<tr class=\"liste_titre\">";
print_liste_field_titre($langs->trans("Ref"),"", "",'','','');
print_liste_field_titre($langs->trans("Label"),"", "",'','','');

print_liste_field_titre($langs->trans("Quant"),"", "",'','','align="right"');
print "</tr>\n";

$j = 0;

foreach((array) $aBudgettype AS $j => $val)
{
	$var=!$var;
	$objPutypestructure->fetch(0,$j);
	print '<tr '.$bc[$var].'>';
	print '<td>'.$objPutypestructure->getNomUrl().'</td>';
	print '<td>'.$aTypestructure[$j]->label.'</td>';
	print '<td align="right">'.$val.'</td>';
	print '</tr>';

}

print "</table>";

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre"><td colspan = "2">'.$langs->trans("Budgetbyregion").'</td>';
print '</tr>';
print "<tr class=\"liste_titre\">";
print_liste_field_titre($langs->trans("Region"),"", "",'','','');
print_liste_field_titre($langs->trans("Quant"),"", "",'','','align="right"');
print "</tr>\n";

$j = 0;

foreach((array) $aBudgetregion AS $j => $val)
{
	$var=!$var;
	$objCregiongeographic->fetch($j);
	print '<tr '.$bc[$var].'>';
	print '<td>'.$objCregiongeographic->getNomUrl().'</td>';
	print '<td align="right">'.$val.'</td>';
	print '</tr>';
}

print "</table>";

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre"><td colspan = "2">'.$langs->trans("Budgetbysector").'</td>';
print '</tr>';
print "<tr class=\"liste_titre\">";
print_liste_field_titre($langs->trans("Sector"),"", "",'','','');
print_liste_field_titre($langs->trans("Quant"),"", "",'','','align="right"');
print "</tr>\n";

$j = 0;

foreach((array) $aBudgetsector AS $j => $val)
{
	$var=!$var;
	$objCclasfin->fetch($j);
	print '<tr '.$bc[$var].'>';
	print '<td>'.$objCclasfin->getNomUrl().'</td>';
	print '<td align="right">'.$val.'</td>';
	print '</tr>';
}

print "</table>";


print '</div></div></div>';

$db->close();

llxFooter();
?>
