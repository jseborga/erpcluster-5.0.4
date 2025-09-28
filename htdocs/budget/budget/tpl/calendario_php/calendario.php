<?
session_start();
if (isset($_GET['finde'])) {$_SESSION['finde']=$_GET['finde'];}
if (isset($_GET['nulos'])) {$_SESSION['nulos']=$_GET['nulos'];}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?
/*
Licencia Creative Commons: http://creativecommons.org/licenses/by-sa/3.0/es/
Si lo vas a usar, lee la licencia: puedes usarlo para fines comerciales y modificarlo, pero tienes que mencionar la fuente original

A los iconos utilizados (http://tango.freedesktop.org/Tango_Desktop_Project) se les aplica otra licencia: http://creativecommons.org/licenses/by-sa/2.5/
*/
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Calendario en PHP</title>
<style type="text/css">
	
	* {margin:0; padding:0;}
	
	body {background-color:#bbb; font-family:arial,sans-serif;}
	#contenedor {background-color:#fff; margin:10px auto; padding:20px 40px; width:420px;}
	#contenedor h1 {font-size:16px; margin-bottom:32px; text-align:center;}
	
	#calendario {width:420px;}
	#calendario .encabezadoCalendario {display:none;}
	#calendario div.tabla {position:relative; width:420px; padding-bottom:26px;}
	#calendario p {display:none;}
	#calendario ul {list-style:none;}
	#calendario ul span {display:none;}
	#calendario table {border:1px solid #bbb; border-collapse: separate; border-spacing: 0; border-width:1px 0 0 1px; width:420px;}
	#calendario table caption span {display:none;}
	#calendario table thead th {background-color:#eee; border:1px solid #bbb; border-width:0 1px 1px 0 ; color:#0c0;}
	#calendario table tbody td {border:1px solid #bbb; border-width:0 1px 1px 0 ; text-align:center;}
	#calendario table tbody td .enlace {display:block;}
	#calendario table tbody td.diaNulo {color:#ccc;}
	
	#calNavMeses li:first-letter {text-transform:uppercase;}
	#calNavMeses li {position:absolute; top:0; }
	#calNavMeses li a {display:block; height:16px; width:16px;}
	#calNavMeses li.anterior a {background:url(go-previous.png) no-repeat 0 0;}
	#calNavMeses li.siguiente a {background:url(go-next.png) no-repeat 0 0;}
	
	#calNavYears li {bottom:0; position:absolute;}
	
	#calendario ul li.anterior {left:0;}
	#calendario ul li.siguiente {right:0;}
	
	#contenedor #calendario a {color:#0000EE; text-decoration:none;}
	#contenedor #calendario a:hover {color:#F7941D;}
	#contenedor #calendario table tbody td.fechaHoy a {background-color:#ccc; color:#585858;}
	#contenedor #calendario table tbody td.fechaSeleccionada a {background-color:#585858; color:#fff;}
	
	#opciones {border:1px solid #ccc; border-width:1px 0; margin:20px 0; padding:10px 0;}
	#opciones p {font-size:14px;}
	#opciones ul {list-style:none;}
	
	#commons {font-size:11px;margin-top:32px; padding-bottom:16px; text-align:center;}
	
	#contenedor #opciones a, #contenedor #commons a {color:#0000EE; text-decoration:none;}
	#contenedor #opciones a:hover, #contenedor #commons a:hover {color:#F7941D;}
	
</style>
</head>
<body>

<div id="contenedor">
<h1>Calendario PHP</h1>

<?
function calendario ($year,$mes,$finDeSemana=1,$mostrarDiasNulos=1,$nivelH=2) {
	
	if (strlen($year)!=4) {$year=date('Y');}
	if (($mes<1 or $mes>12) or (strlen($mes)<1 or strlen($mes)>2)) {$year=date('n');}
	
	// Listados: días de la semana, letra inicial de los días de la semana, y meses
	$dias = array('Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado','Domingo');
	$diasAbbr = array('L','M','M','J','V','S','D');
	$meses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiempre','Octubre','Noviembre','Diciembre');
	
	// Se sacan valores que se utilizarán más adelante
	$diaInicial = gmmktime(0,0,0,$mes,1,$year);  // Primer día del mes dado
	$diasNulos = (date("N",$diaInicial))-1; // Con 'N' la semana empieza en Lunes. Con 'w', en domingo
		if($diasNulos<0){$diasNulos = 7-abs($diasNulos);}
	$diasEnMes = date("t",$diaInicial); // Número de días del mes dado
	
	// Se abre la capa contenedora y se genera el encabezado del bloque de calendario
	$html .= '<div id="calendario">';
	$html .= '<h'.$nivelH.' class="encabezadoCalendario">Calendario</h'.$nivelH.'>';
	
	// Párrafos con la fecha actual y la fecha seleccionada
	$html .= '<p>Fecha actual: '.date('j').' de '.$meses[(intval(date('n'))-1)].' de '.date('Y').'</p>';
	$html .= '<p>Fecha seleccionada: ';
	if (isset($_GET['dia'])) {$html .= ''.$_GET['dia'].' de ';} // El día solo sale si se ha definido previamente en el parámetro 'dia' de la URL
	$html .= ''.$meses[($mes-1)].' de '.$year.'</p>';
	$html .= '<div class="tabla">';
	
	
	// Enlaces al mes anterior y al siguiente
	$html .= '<p>Navegación por meses:</p>';
	$html .= '<ul id="calNavMeses">';
	$enlaceAnterior1 = gmmktime(0,0,0,($mes-1),1,$year);
	$mesAnterior = date('n',$enlaceAnterior1);
	$yearMesAnterior = date('Y',$enlaceAnterior1);
	$enlaceSiguiente1 = gmmktime(0,0,0,($mes+1),1,$year);
	$mesSiguiente = date('n',$enlaceSiguiente1);
	$yearMesSiguiente = date('Y',$enlaceSiguiente1);
	$html .= '<li class="anterior"><a href="?mes='.$mesAnterior.'&amp;ano='.$yearMesAnterior.'"><span>Mes anterior ('.$meses[($mesAnterior-1)].')</span></a></li>';
	$html .= '<li class="siguiente"><a href="?mes='.$mesSiguiente.'&amp;ano='.$yearMesSiguiente.'"><span>Mes siguiente ('.$meses[($mesSiguiente-1)].')</span></a></li>';
	$html .= '</ul>';
	
	// Enlaces al año anterior y al siguiente
	$html .= '<p>Navegación por años:</p>';
	$html .= '<ul id="calNavYears">';
	$enlaceAnterior2 = gmmktime(0,0,0,$mes,1,($year-1));
	$yearAnterior = date('Y',$enlaceAnterior2);
	$enlaceSiguiente2 = gmmktime(0,0,0,$mes,1,($year+1));
	$yearSiguiente = date('Y',$enlaceSiguiente2);
	$html .= '<li class="anterior"><a href="?mes='.$mes.'&amp;ano='.$yearAnterior.'"><span>Año anterior (</span>'.$yearAnterior.'<span>)</span></a></li>';
	$html .= '<li class="siguiente"><a href="?mes='.$mes.'&amp;ano='.$yearSiguiente.'"><span>Año siguiente (</span>'.$yearSiguiente.'<span>)</span></a></li>';
	$html .= '</ul>';
	
	// Se abre la tabla que contiene el calendario
	$html .= '<table>';
	
	// Título mes-año (elemento CAPTION)
	$mesLista = $mes-1;
	$html .= '<caption>'.$meses[$mesLista].'<span> de</span> '.$year.'</caption>';
	
	// Se definen anchuras en elementos COL
	$cl=0; $anchoCol=100/7; while ($cl<7) {$html .= '<col width="'.$anchoCol.'%" />'; $cl++;}
	
	// Fila de los días de la semana (elemento THEAD)
	$html .= '<thead><tr>';$d=0;
	while ($d<7) {$html .= '<th scope="col" abbr="'.$dias[$d].'">'.$diasAbbr[$d].'</th>';$d++;}
	$html .= '</tr></thead>';
	
	// Se generan los días nulos (días del mes anterior o posterior) iniciales, el TBODY y su primer TR
	$html .= '<tbody>';
	if ($diasNulos>0) {$html .= '<tr>';} // Se abre el TR solo si hay días nulos
	if ($diasNulos>0 and $mostrarDiasNulos==0) {$html .= '<td class="nulo" colspan="'.$diasNulos.'"></td>';} // Se hace un TD en blanco con el ancho según los día nulos que haya
	if ($mostrarDiasNulos==1) { // Generación de los TD con días nulos si está activado que se muestren
		$dni=$diasNulos;$i=0;
		while ($i<$diasNulos) {
			$enSegundosNulo = gmmktime(0,0,0,$mes,(1-$dni),$year);
			$dmNulo = date('j',$enSegundosNulo);
			$idFechaNulo = 'cal-'.date('Y-m-d',$enSegundosNulo);
			$html .= '<td id="'.$idFechaNulo.'" class="diaNulo"><span class="dia"><span class="enlace">'.$dmNulo.'</span></span></td>';
			$dni--;
			$i++;
		}
	}
	
	
	
	// Se generan los TD con los días del mes
	$dm=1;$x=0;$ds=$diasNulos+1;
	while ($dm<=$diasEnMes) {
		if(($x+$diasNulos)%7==0 and $x!=0) {$html .= '</tr>';} // Se evita el cierre del TR si no hay días nulos iniciales
		if(($x+$diasNulos)%7==0) {$html .= '<tr>';$ds=1;}
		$enSegundosCalendario = gmmktime(0,0,0,$mes,$dm,$year); // Fecha del día generado en segundos
		$enSegundosActual = gmmktime(0,0,0,date('n'),date('j'),date('Y')); // Fecha actual en segundos
		$enSegundosSeleccionada = gmmktime(0,0,0,$_GET['mes'],$_GET['dia'],$_GET['ano']); // Fecha seleccionada, en segundos
		$idFecha = 'cal-'.date('Y-m-d',$enSegundosCalendario);
		
		// Se generan los parámetros de la URL para el enlace del día
		$link_dia = date('j',$enSegundosCalendario);
		$link_mes = date('n',$enSegundosCalendario);
		$link_year = date('Y',$enSegundosCalendario);
		
		// Clases y etiquetado general para los días, para día actual y para día seleccionado
		$claseActual='';$tagDia='span';
		if ($enSegundosCalendario==$enSegundosActual) {$claseActual=' fechaHoy';$tagDia='strong';}
		if ($enSegundosCalendario==$enSegundosSeleccionada and isset($_GET['dia'])) {$claseActual=' fechaSeleccionada';$tagDia='em';}
		if ($enSegundosCalendario==$enSegundosActual and $enSegundosCalendario==$enSegundosSeleccionada and isset($_GET['dia'])) {$claseActual=' fechaHoy fechaSeleccionada';$tagDia='strong';}
		
		// Desactivación de los días del fin de semana
		if (($ds<6 and $finDeSemana==0) or $finDeSemana!=0) { // Si el fin de semana está activado, o el día es de lunes a viernes
			$tagEnlace='a';
			$atribEnlace='href="?dia='.$link_dia.'&amp;mes='.$link_mes.'&amp;ano='.$link_year.'"';
		} if ($ds>5 and $finDeSemana==0) { // Si el fin de semana está desactivado y el día es sábado o domingo
			$tagEnlace='span';
			$atribEnlace='';
			$paramFinde='0';
		}
		
		// Con las variables ya definidas, se crea el HTML del TD
		$html .= '<td id="'.$idFecha.'" class="'.calendarioClaseDia($ds).$claseActual.'"><'.$tagDia.' class="dia"><'.$tagEnlace.' class="enlace" '.$atribEnlace.'>'.$dm.'</'.$tagEnlace.'></'.$tagDia.'></td>';
		
		$dm++;$x++;$ds++;
	}
	
	// Se generan los días nulos finales
	$diasNulosFinales = 0;
	while((($diasEnMes+$diasNulos)%7)!=0){$diasEnMes++;$diasNulosFinales++;}
	if ($diasNulosFinales>0 and $mostrarDiasNulos==0) {$html .= '<td class="nulo" colspan="'.$diasNulosFinales.'"></td>';} // Se hace un TD en blanco con el ancho según los día nulos que haya (si no se activa mostrar los días nulos)
	if ($mostrarDiasNulos==1) { // Generación de días nulos (si se activa mostrar los días nulos)
		$dnf=0;
		while ($dnf<$diasNulosFinales) {
			$enSegundosNulo = gmmktime(0,0,0,($mes+1),($dnf+1),$year);
			$dmNulo = date('j',$enSegundosNulo);
			$idFechaNulo = 'cal-'.date('Y-m-d',$enSegundosNulo);
			$html .= '<td id="'.$idFechaNulo.'" class="diaNulo"><span class="dia"><span class="enlace">'.$dmNulo.'</span></span></td>';
			$dnf++;
		}
	}
	
	// Se cierra el último TR y el TBODY
	$html .= '</tr></tbody>';
	
	// Se cierra la tabla
	$html .= '</table>';
	
	// Se cierran la capa de la tabla y la capa contenedora
	$html .= '</div>';
	$html .= '</div>';
	
	// Se devuelve la variable que contiene el HTML del calendario
	return $html;
}

function calendarioClaseDia ($dia) {
	switch ($dia) {
		case 1: $clase = 'lunes semana'; break;
		case 2: $clase = 'martes semana'; break;
		case 3: $clase = 'miercoles semana'; break;
		case 4: $clase = 'jueves semana'; break;
		case 5: $clase = 'viernes semana'; break;
		case 6: $clase = 'sabado finDeSemana'; break;
		case 7: $clase = 'domingo finDeSemana'; break;
	}
	return $clase;
}
?>

<?
$yearCalendario = date('Y');if (isset($_GET['ano'])) {$yearCalendario = $_GET['ano'];} // Si no se define año en el parámetro de URL, el año actual
$mesCalendario = date('n');if (isset($_GET['mes'])) {$mesCalendario = $_GET['mes'];} // Si no se define mes en el parámetro de URL, el mes actual
$finDeSemana = 1;if (isset($_SESSION['finde'])) {$finDeSemana = $_SESSION['finde'];} // Días de fin de semana activados: '1' para activado, '0' para desactivado (se predetermina a 1)
$diasNulos = 1;if (isset($_SESSION['nulos'])) {$diasNulos = $_SESSION['nulos'];} // Los días que son de otros meses pero que coinciden con las semanas de inicio y final del mes actual: '1' para mostrarlos, '0' para dejarlos ocultos (el TD lleva un atributo class="diaNulo" para poder darle otro color y van sin enlace)
$nivelH = 2; // Nivel para el encabezado del bloque de calendario, con valor entre '1' y '6'. Se predetermina en '2'.
echo calendario($yearCalendario,$mesCalendario,$finDeSemana,$diasNulos,$nivelH);
?>

<div id="opciones">
	<ul>
<?

if (isset($_GET['mes']) and isset($_GET['ano'])) {
	$paramFecha = '&amp;mes='.$_GET['mes'].'&amp;ano='.$_GET['ano'].'';
}

$findeActivado=0;$textoFindeActivado='Desactivar';
if (isset($_SESSION['finde'])) {
	$findeActivado=abs($_SESSION['finde']-1);
	$textoFindeActivado='Activar';
	if ($findeActivado==0) {$textoFindeActivado='Desactivar';}
}
echo '<li><a href="?finde='.$findeActivado.$paramFecha.'">'.$textoFindeActivado.' fines de semana</a></li>';

$nulosActivado=0;$textoNulosActivado='Desactivar';
if (isset($_SESSION['nulos'])) {
	$nulosActivado=abs($_SESSION['nulos']-1);
	$textoNulosActivado='Activar';
	if ($nulosActivado==0) {$textoNulosActivado='Desactivar';}
}
echo '<li><a href="?nulos='.$nulosActivado.$paramFecha.'">'.$textoNulosActivado.' días nulos</a></li>';
 ?>
	</ul>
</div>

<p id="commons"><a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/es/"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-sa/3.0/es/88x31.png" /></a><br /><span xmlns:dc="http://purl.org/dc/elements/1.1/" property="dc:title">Calendario PHP</span> por <a xmlns:cc="http://creativecommons.org/ns#" href="http://outbook.blogspot.com" property="cc:attributionName" rel="cc:attributionURL">Antonio Rodr&#237;guez</a> se distribuye bajo licencia <a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/es/">Creative Commons Reconocimiento-Compartir bajo la misma licencia 3.0 Espa&#241;a License</a> (excepto los iconos, que proceden de <a href="http://tango.freedesktop.org">Tango Desktop Project</a>: <a href="http://creativecommons.org/licenses/by-sa/2.5/deed.es">Reconocimiento-Compartir bajo la misma licencia 2.5 Genérica</a>).</p>
</div>

</body>
</html>