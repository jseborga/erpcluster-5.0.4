<?php

$yearCalendario = date('Y');if (isset($_GET['ano'])) {$yearCalendario = $_GET['ano'];} // Si no se define año en el parámetro de URL, el año actual
$mesCalendario = date('n');if (isset($_GET['mes'])) {$mesCalendario = $_GET['mes'];} // Si no se define mes en el parámetro de URL, el mes actual
$finDeSemana = 1;if (isset($_SESSION['finde'])) {$finDeSemana = $_SESSION['finde'];} // Días de fin de semana activados: '1' para activado, '0' para desactivado (se predetermina a 1)
$diasNulos = 1;if (isset($_SESSION['nulos'])) {$diasNulos = $_SESSION['nulos'];} // Los días que son de otros meses pero que coinciden con las semanas de inicio y final del mes actual: '1' para mostrarlos, '0' para dejarlos ocultos (el TD lleva un atributo class="diaNulo" para poder darle otro color y van sin enlace)
$nivelH = 2; // Nivel para el encabezado del bloque de calendario, con valor entre '1' y '6'. Se predetermina en '2'.
print '<div id="contenedor">';
print '<div class="col-md-12">';
print '<div class="col-md-5">';
print calendario($id,$yearCalendario,$mesCalendario,$finDeSemana,$diasNulos,$nivelH);

/*
print '<div id="opciones">';
print '	<ul>';

if (isset($_GET['mes']) and isset($_GET['ano'])) {
	$paramFecha = '&amp;mes='.$_GET['mes'].'&amp;ano='.$_GET['ano'].'';
}

$findeActivado=0;$textoFindeActivado='Desactivar';
if (isset($_SESSION['finde'])) {
	$findeActivado=abs($_SESSION['finde']-1);
	$textoFindeActivado='Activar';
	if ($findeActivado==0) {$textoFindeActivado='Desactivar';}
}
print '<li><a href="?finde='.$findeActivado.$paramFecha.'">'.$textoFindeActivado.' fines de semana</a></li>';

$nulosActivado=0;$textoNulosActivado='Desactivar';
if (isset($_SESSION['nulos'])) {
	$nulosActivado=abs($_SESSION['nulos']-1);
	$textoNulosActivado='Activar';
	if ($nulosActivado==0) {$textoNulosActivado='Desactivar';}
}
print '<li><a href="?nulos='.$nulosActivado.$paramFecha.'">'.$textoNulosActivado.' días nulos</a></li>';
 
print '	</ul>';
print '</div>';
*/
print '</div>';
print '<div class="col-md-6">';
//verificamos el contenido de cada fecha en calendar_special
$objcalspecial = new Calendarspecial($db);
$filtercal = " AND t.object = '".$object->element."'";
$filtercal.= " AND t.fk_object = ".$object->id;
$mes 	= GETPOST('mes');
$dia 	= GETPOST('dia');
$year 	= GETPOST('ano');
$dayselect = dol_mktime(0,0,0,$mes,$dia,$year);

$string = $year.'-'.(strlen($mes)==1?'0'.$mes:$mes).'-'.(strlen($dia)==1?'0'.$dia:$dia);
$date = dol_stringtotime($string);
$filtercal.= " AND month(t.dateo) =".$mes;
$filtercal.= " AND year(t.dateo) =".$year;
$filtercal.= " AND day(t.dateo) =".$dia;
//$filtercal.= " AND t.dateo =".$db->idate($date);
$res = $objcalspecial->fetchAll('','',0,0,array(1=>1),'AND',$filtercal,true);
if ($res > 0)
{
	$fk_calendar = $objcalspecial->fk_calendar;
	$type_date = $objcalspecial->type_date;
}

	//armamos uno vacio
	include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/addcalendar.tpl.php';
	$contenido = $html;

//$contenido = $res;
print '<div id="calendartpl">'.$contenido.'</div>';
print '</div>';

print '</div>';
print '</div>';

?>