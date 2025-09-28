<?php
/* Copyright (C) 7102 no one <email>
 *  Activos
 */


require("../main.inc.php");
dol_include_once('/assets/class/assetsext.class.php');
dol_include_once('/fiscal/class/vdosingext.class.php');
dol_include_once('/core/class/html.formv.class.php');

dol_include_once('/core/lib/date.lib.php');
//$langs->load("stocks");
$langs->load("fiscal");

list($country,$countrycod,$countryname) = explode(':',$conf->global->MAIN_INFO_SOCIETE_COUNTRY) ;

llxHeader("",$langs->trans("Fiscal"),$help_url);


print_fiche_titre($langs->trans("Fiscal"));

$form = new Formv($db);

/*Objetos para el index*/

$objVdosing = new Vdosingext($db);


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

// Activos que seran dados de baj en el mes en curso

$sql = "SELECT";
$sql.= " t.rowid,";

		$sql .= " t.entity,";
		$sql .= " t.fk_subsidiaryid,";
		$sql .= " t.series,";
		$sql .= " t.num_ini,";
		$sql .= " t.num_fin,";
		$sql .= " t.num_ult,";
		$sql .= " t.num_aprob,";
		$sql .= " t.type,";
		$sql .= " t.active,";
		$sql .= " t.date_val,";
		$sql .= " t.num_autoriz,";
		$sql .= " t.cod_control,";
		$sql .= " t.lote,";
		$sql .= " t.chave,";
		$sql .= " t.descrip,";
		$sql .= " t.activity,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.date_create,";
		$sql .= " t.date_mod,";
		$sql .= " t.tms,";
		$sql .= " t.status,";
		$sql .= " s.ref";

        $sql.= " FROM ".MAIN_DB_PREFIX."v_dosing as t";
        $sql.= " INNER JOIN ".MAIN_DB_PREFIX."subsidiary as s ON s.rowid = t.fk_subsidiaryid";

		//$sql.= " WHERE 1 = 1 AND t.statut >= 0";
		$sql.= " WHERE 1 = 1 AND t.status >= 1 AND t.active = 1";
        //$sql.= " ORDER BY t.type_group ASC";


        $nbtotalofrecords = '';
		$year = $fechaActual[year];
		$months = $fechaActual[mon];

	    $result = $db->query($sql);
		$nbtotalofrecords = $db->num_rows($result);


    	print '<table class="noborder" width="100%">';
		print '<tr class="liste_titre"><td colspan = "4">'.$langs->trans("Dosagesthatexpireinthemonth").' : '.$langs->trans($fechaActual[month]).'</td>';
		print '</tr>';
		print "<tr class=\"liste_titre\">";
		print_liste_field_titre($langs->trans("Numautoriz"),"", "",'','','');
		print_liste_field_titre($langs->trans("Serie"),"", "","","","");
		print_liste_field_titre($langs->trans("Subsidiary"),"", "","","","");
		print_liste_field_titre($langs->trans("Dateval"),"", "",'','','align="center"');
		print "</tr>\n";

        $j = 0;


        while ($j < $nbtotalofrecords)
        {
			$var = true;
            $obj = $db->fetch_object($result);
            if ($obj)
            {
				$fechaConsultar = dol_getdate($db->jdate($obj->date_val));
				//echo $fechaConsultar[year] ." - ".$fechaConsultar[mon]."<br>";
				if( $fechaConsultar[year] == $year && $fechaConsultar[mon] == $months){
				//if( $fechaConsultar[year] == $year && $fechaConsultar[mon] == 11){
				print '<tr '.$bc[$var].'>';
				$objVdosing->id = $obj->rowid;
			    $objVdosing->num_autoriz = $obj->num_autoriz;
			   	print '<td>'.$objVdosing->getNomUrl(1)." ".$obj->num_autoriz.'</td>';
				print '<td>'.$obj->series.'</td>';
			   	print '<td>'.$obj->ref.'</td>';
			   	print '<td align="center">'.dol_print_date($db->jdate($obj->date_val),'day').'</td>';
				print '</tr>';
				}
            }
            $j++;
        }
    	print "</table>";

print '</div><div class="fichetwothirdright"><div class="ficheaddleft">';

//lista de activos vencidos en el mes en curso
$sql = "SELECT";
$sql.= " t.rowid,";

		$sql .= " t.entity,";
		$sql .= " t.fk_subsidiaryid,";
		$sql .= " t.series,";
		$sql .= " t.num_ini,";
		$sql .= " t.num_fin,";
		$sql .= " t.num_ult,";
		$sql .= " t.num_aprob,";
		$sql .= " t.type,";
		$sql .= " t.active,";
		$sql .= " t.date_val,";
		$sql .= " t.num_autoriz,";
		$sql .= " t.cod_control,";
		$sql .= " t.lote,";
		$sql .= " t.chave,";
		$sql .= " t.descrip,";
		$sql .= " t.activity,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.date_create,";
		$sql .= " t.date_mod,";
		$sql .= " t.tms,";
		$sql .= " t.status,";
		$sql .= " s.ref";

        $sql.= " FROM ".MAIN_DB_PREFIX."v_dosing as t";
        $sql.= " INNER JOIN ".MAIN_DB_PREFIX."subsidiary as s ON s.rowid = t.fk_subsidiaryid";

		//$sql.= " WHERE 1 = 1 AND t.statut >= 0";
		$sql.= " WHERE 1 = 1 AND t.status = 1 AND t.active = 1";
        //$sql.= " ORDER BY t.type_group ASC";


        $nbtotalofrecords = '';
		$year = $fechaActual[year];
		$months = $fechaActual[mon];

	    $result = $db->query($sql);
		$nbtotalofrecords = $db->num_rows($result);


    	print '<table class="noborder" width="100%">';
		print '<tr class="liste_titre"><td colspan = "5">'.$langs->trans("Activedosages").'</td>';
		print '</tr>';
		print "<tr class=\"liste_titre\">";
		print_liste_field_titre($langs->trans("Numautoriz"),"", "",'','','');
		print_liste_field_titre($langs->trans("Serie"),"", "","","","");
		print_liste_field_titre($langs->trans("Type"),"", "","","","");
		print_liste_field_titre($langs->trans("Subsidiary"),"", "","","","");
		print_liste_field_titre($langs->trans("Dateval"),"", "",'','','align="center"');
		print "</tr>\n";

        $j = 0;


        while ($j < $nbtotalofrecords)
        {
			$var = true;
            $obj = $db->fetch_object($result);
            if ($obj)
            {
				print '<tr '.$bc[$var].'>';
				$objVdosing->id = $obj->rowid;
			    $objVdosing->num_autoriz = $obj->num_autoriz;
			   	print '<td>'.$objVdosing->getNomUrl(1)." ".$obj->num_autoriz.'</td>';
				print '<td>'.$obj->series.'</td>';
				print '<td>'.$obj->type.'</td>';
			   	print '<td>'.$obj->ref.'</td>';
			   	print '<td align="center">'.dol_print_date($db->jdate($obj->date_val),'day').'</td>';
				print '</tr>';

            }
            $j++;
        }
    	print "</table>";

print '</div></div></div>';

$db->close();

llxFooter();
?>
