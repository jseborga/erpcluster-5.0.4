<?php
/* Copyright (C) 7102 no one <email>
 *  Activos
 */



require("../main.inc.php");
dol_include_once('/assets/class/assetsext.class.php');
dol_include_once('/sales/class/propalext.class.php');
dol_include_once('/sales/class/factureext.class.php');
dol_include_once('/societe/class/societe.class.php');
dol_include_once('/core/class/html.formv.class.php');

dol_include_once('/core/lib/date.lib.php');
//$langs->load("stocks");
$langs->load("assets");

if (!$user->rights->assets->read)
	accessforbidden();

list($country,$countrycod,$countryname) = explode(':',$conf->global->MAIN_INFO_SOCIETE_COUNTRY) ;


llxHeader("",$langs->trans("Assets"),$help_url);


print_fiche_titre($langs->trans("Assets"));

$form = new Formv($db);

/*Objetos para el index*/

$objAssets = new Assetsext($db);


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


// Numero de Activos

print '<div class="fichecenter"><div class="fichethirdleft">';

$sql = "SELECT";
$sql.= " t.rowid,";

$sql .= " t.entity,";
$sql .= " t.fk_father,";
$sql .= " t.fk_facture_fourn,";
$sql .= " t.fk_facture,";
$sql .= " t.type_group,";
$sql .= " t.type_patrim,";
$sql .= " t.ref,";
$sql .= " t.ref_ext,";
$sql .= " t.item_asset,";
$sql .= " t.date_adq,";
$sql .= " t.date_day,";
$sql .= " t.date_month,";
$sql .= " t.date_year,";
$sql .= " t.date_active,";
$sql .= " t.date_reval,";
$sql .= " t.useful_life_residual,";
$sql .= " t.quant,";
$sql .= " t.coste,";
$sql .= " t.coste_residual,";
$sql .= " t.coste_reval,";
$sql .= " t.coste_residual_reval,";
$sql .= " t.useful_life_reval,";
$sql .= " t.dep_acum,";
$sql .= " t.date_baja,";
$sql .= " t.amount_sale,";
$sql .= " t.descrip,";
$sql .= " t.number_plaque,";
$sql .= " t.trademark,";
$sql .= " t.model,";
$sql .= " t.anio,";
$sql .= " t.fk_asset_sup,";
$sql .= " t.fk_location,";
$sql .= " t.code_bar,";
$sql .= " t.fk_method_dep,";
$sql .= " t.type_property,";
$sql .= " t.code_bim,";
$sql .= " t.fk_product,";
$sql .= " t.useful_life,";
$sql .= " t.percent,";
$sql .= " t.account_accounting,";
$sql .= " t.fk_unit,";
$sql .= " t.model_pdf,";
$sql .= " t.coste_unit_use,";
$sql .= " t.fk_unit_use,";
$sql .= " t.codcont,";
$sql .= " t.codaux,";
$sql .= " t.orgfin,";
$sql .= " t.cod_rube,";
$sql .= " t.fk_departament,";
$sql .= " t.fk_resp,";
$sql .= " t.departament_name,";
$sql .= " t.resp_name,";
$sql .= " t.fk_user_create,";
$sql .= " t.fk_user_mod,";
$sql .= " t.date_create,";
$sql .= " t.date_mod,";
$sql .= " t.mark,";
$sql .= " t.been,";
$sql .= " t.tms,";
$sql .= " t.fk_asset_mov,";
$sql .= " t.status_reval,";
$sql .= " t.statut, ";
$sql .= " ag.code,";
$sql .= " ag.useful_life as cg_useful_life, ";
$sql .= " ag.label as cg_label ";


$sql.= " FROM ".MAIN_DB_PREFIX."assets as t";
$sql.= " INNER JOIN ".MAIN_DB_PREFIX."c_assets_group as ag ON ag.code = t.type_group";

$sql.= " WHERE 1 = 1 AND t.statut >= 0";
$sql.= " ORDER BY t.type_group ASC";

		//echo $sql;

$nbtotalofrecords = '';

$result = $db->query($sql);
$nbtotalofrecords = $db->num_rows($result);

$aAssets = array();

$j = 0;

while ($j < $nbtotalofrecords)
{
	$obj = $db->fetch_object($result);
	if ($obj)
	{
		$aAssets[$j]['ref']=$obj->ref;
		$aAssets[$j]['ref_ext']=$obj->ref_ext;
		$aAssets[$j]['type_group']=$obj->type_group;
		$aAssets[$j]['label_group']=$obj->cg_label;
		$aAssets[$j]['group_useful_life']=$obj->cg_useful_life;
		$aAssets[$j]['descrip']=$obj->descrip;
		$aAssets[$j]['date_adq']=$db->jdate($obj->date_adq);
		$aAssets[$j]['date_reval']=$db->jdate($obj->date_reval);
		$aAssets[$j]['coste']=$obj->coste;
		$aAssets[$j]['useful_life']=$obj->useful_life;
		$aAssets[$j]['status']=$obj->statut;
	}
	$j++;
}

$var           =true;
$numActivosMes = 0;
$aLineas       = array();
$tiempo_Vida   = 0;
foreach ($aAssets as $pos => $valor) {

	if(!empty($valor['date_reval'])){

		if(!empty($valor['useful_life'])){
			$dateOff = dol_time_plus_duree($valor['date_reval'], ceil($valor['useful_life']*365),'d');
			$tiempo_Vida = ceil($valor['useful_life']);
			$dias = num_between_day(dol_now(),$dateOff);

		}else{
			$dateOff = dol_time_plus_duree($valor['date_reval'], ceil($valor['group_useful_life']*365),'d');
			$dias = num_between_day(dol_now(),$dateOff);
			$tiempo_Vida = ceil($valor['group_useful_life']);
		}

	}elseif (!empty($valor['useful_life'])) {
		$dateOff = dol_time_plus_duree($valor['date_adq'], ceil($valor['useful_life']*365),'d');
		$dias = num_between_day(dol_now(),$dateOff);
		$tiempo_Vida = ceil($valor['useful_life']);
	}elseif (!empty($valor['group_useful_life'])) {
		$dateOff = dol_time_plus_duree($valor['date_adq'], ceil($valor['group_useful_life']*365),'d');
		$dias = num_between_day(dol_now(),$dateOff);
		$tiempo_Vida = ceil($valor['group_useful_life']);
	}



	$fechaVencimineto = dol_getdate($dateOff);

			//if( $fechaVencimineto[year] == $year && $fechaVencimineto[mon] == $months){
	if( $fechaVencimineto[year] == $year && $fechaVencimineto[mon] == $months){
		$numActivosMes++;
	}

	$var     = !$var;
	$dateOff = 0;
	$dias    = 0;
}

print '<table class="formdoc" width="100%">';
print '<tr class="liste_titre"><td colspan = "3">'.$langs->trans("Numero de Activos").'</span></td>';
print '<td align = "right">'.$langs->trans("Total").'</td>';
print '</tr>';
print '<tr><td colspan="3">';
print $langs->trans("Numero de activos")." : ".'</td>';
print '<td align = "right">'.count($aAssets).'</td>';
print '</tr>';
print '</table>';
print '<br>';
// Activos que seran dados de baj en el mes en curso

print '<table class="formdoc" width="100%">';
print '<tr class="liste_titre"><td colspan = "3">'.$langs->trans("Assets").'</td>';
print '<td align = "right">'.$langs->trans("Total").'</td>';
print '</tr>';
print '<tr><td colspan="3">';
print $langs->trans("Activos a dar de baja en el mes de").' '.$langs->trans($aDate[month]).'</td>';
print '<td align = "right">'.$numActivosMes.'</td>';
print '</tr>';
print '</table>';

print '</div><div class="fichetwothirdright"><div class="ficheaddleft">';

//lista de activos vencidos en el mes en curso

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre"><td colspan = "4">'.$langs->trans("20 activos a ser dados de baja").'</td>';
print '</tr>';
print "<tr class=\"liste_titre\">";
print_liste_field_titre($langs->trans("Ref"),"", "","","","");
print_liste_field_titre($langs->trans("Descrip"),"", "",'','','');
print_liste_field_titre($langs->trans("Dateadq"),"", "","","","");
print_liste_field_titre($langs->trans("Dateoff"),"", "",'','','align="center"');
print "</tr>\n";
$var           =true;
$numActivosMes = 0;
$tiempo_Vida   = 0;
$lim = 1;
foreach ($aAssets as $pos => $valor) {

	if(!empty($valor['date_reval'])){

		if(!empty($valor['useful_life'])){
			$dateOff = dol_time_plus_duree($valor['date_reval'], ceil($valor['useful_life']*365),'d');
			$tiempo_Vida = ceil($valor['useful_life']);
			$dias = num_between_day(dol_now(),$dateOff);

		}else{
			$dateOff = dol_time_plus_duree($valor['date_reval'], ceil($valor['group_useful_life']*365),'d');
			$dias = num_between_day(dol_now(),$dateOff);
			$tiempo_Vida = ceil($valor['group_useful_life']);
		}

	}elseif (!empty($valor['useful_life'])) {
		$dateOff = dol_time_plus_duree($valor['date_adq'], ceil($valor['useful_life']*365),'d');
		$dias = num_between_day(dol_now(),$dateOff);
		$tiempo_Vida = ceil($valor['useful_life']);
	}elseif (!empty($valor['group_useful_life'])) {
		$dateOff = dol_time_plus_duree($valor['date_adq'], ceil($valor['group_useful_life']*365),'d');
		$dias = num_between_day(dol_now(),$dateOff);
		$tiempo_Vida = ceil($valor['group_useful_life']);
	}

	$fechaVencimineto = dol_getdate($dateOff);

				//if( $fechaVencimineto[year] == $year && $fechaVencimineto[mon] == $months && $lim <= 20){
	if( $fechaVencimineto[year] == $year && $fechaVencimineto[mon] == $months && $lim <= 20)
	{
		print '<tr '.$bc[$var].'>';
		$objAssets->id = $obj->rowid;
		$objAssets->ref = $valor['ref'];
		print '<td>'.$objAssets->getNomUrl(1).'</td>';
		print '<td>'.$valor['descrip'].'  -   '.ceil($valor['useful_life']*365).'</td>';
		print '<td>'.dol_print_date($valor['date_adq'],'day').'</td>';
		print '<td>'.dol_print_date($dateOff,'day').'</td>';
		print '</tr>';
		$lim++;
	}

	$var     = !$var;
	$dateOff = 0;
	$dias    = 0;



}
print "</table>";


// Numero de los ultimos Activos introducidos

//print '<div class="fichecenter"><div class="fichethirdleft">';

print '<br><br><br>';

print '<table class="noborder" width="100%">';


print '<table class="formdoc" width="100%">';
print '<tr class="liste_titre"><td colspan="3">'.$langs->trans("Ultimos 20 Activos Registrados").'</td>';
print '</tr>';
print "<tr class=\"liste_titre\">";
print_liste_field_titre($langs->trans("Ref"),"", "","","","");
print_liste_field_titre($langs->trans("Descrip"),"", "",'','','');
print_liste_field_titre($langs->trans("Dateadq"),"", "",'','','align="center"');
print "</tr>\n";

$rA = $objAssets->fetchAll('DESC','rowid',19,0,array(),'AND');

if($rA > 0){
	$var = true;
	$obj = $objAssets->lines;
	foreach ($obj as $key => $value) {

		print '<tr '.$bc[$var].'>';
		$objAssets->id = $value->id;
		$objAssets->ref = $value->ref;

		print '<td>'.$objAssets->getNomUrl(1).'</td>';
		print '<td>'.$value->descrip.'</td>';
		print '<td>'.dol_print_date($value->date_adq,'day').'</td>';
		print '</tr>';
		$var     = !$var;
	}
}





print '</table>';


print '</div></div></div>';

$db->close();

llxFooter();
?>
