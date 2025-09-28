<?php

function generarcodigo($longitud)
{
	$key = '';
	$pattern = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	$max = strlen($pattern)-1;
	for($i=0; $i < $longitud; $i++)
	{
		$key .= $pattern{mt_rand(0,$max)};
	}
	return $key;
}

function get_group_structure()
{
	global $langs;
	$aGroup = array('MA'=>$langs->trans('Materials'),'MO'=>$langs->trans('Workforce'),'MQ'=>$langs->trans('Machineryandequipment'),'OT'=>$langs->trans('Others'));
	return $aGroup;
}

function fetch_unit($id=0,$ref='')
{
	global $langs,$db;
	$sql = " SELECT rowid, code, label, short_label ";
	$sql.= " FROM ".MAIN_DB_PREFIX."c_units";
	if ($id>0)
		$sql.= " WHERE rowid = ".$id;
	else $sql.= " WHERE code = '".$ref."'";
	$sql;
	$resql = $db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		if ($num>0)
		{
			$obj = $db->fetch_object($resql);
			return $obj;
		}
		else
			return $num;
	}
	else
		return -1;
}

//numeracion order ref nuevo
//funcion para asignar el numero de orden de tarea
function get_orderlastnew($id,$fk,$data,array $aRef=array(),array $aNumberref=array(),array $aRefnumber=array())
{
	global $taskadd,$taskstat,$objecttaskadd;
	if (empty($data['fk_task_parent'])) $lGrupo = 0;
	//asigno variable para modulos
	$aModule = array();
	$aLevel = array();
	$nParent = $data['fk_task_parent'];
	$aOrdernumparent[$id] = $data['fk_task_parent'];

	$aOrdertask[$id] = $data['fk_task_parent'];
	$aOrdernumref = array();
	$aOrder_ref = array();
	$aOrdercount = array();
	$nDigit = 7;
	$nOrder = 1000000;
	$cDigit = '1000000';
	//recupero los datos adicionales de la tarea
	//$objecttaskadd->fetch(0,$line->id);
	$nLevel = 0;
		//es grupo
		//buscamos que nivel es si fk_parent != 0
		//$aLevel[$nLevel] = $nParent;
		//$aModule[$nParent] = $nLevel;
	//echo '<hr>id '.$id;
	//echo '<br>nparent '.$nParent;
	if ($nParent > 0)
	{
		//recuperamos que numero tiene el padre
		//echo '<br>nPadre '.
		$nPadre = $aNumberref[$nParent];
		if ($data['group'])
		{
			//echo ';  es grupoint ';
			//es grupo procesamos como grupo
			//echo '; nreg= '.
			$nreg = count($aRefnumber[$data['level']-1][$nParent]);
			if (empty($nreg))
			{
				$aRef[$data['level']][$id] = $nPadre + substr($cDigit,0,$nDigit-$data['level']) * 1;
				//echo  '; id= '.$id;
				//echo '; numberref= '.
				$aNumberref[$id] = $aRef[$data['level']][$id];
			}
			else
			{
				$aRef[$data['level']][$id] = $nPadre + substr($cDigit,0,$nDigit-$data['level']) * $nreg;
				//echo '; id = '.$id;
				//echo '; numbreref mult '.
				$aNumberref[$id] = $aRef[$data['level']][$id];
			}
			$aRefnumber[$data['level']-1][$nParent][$id] = $id;
		}
		else
		{
			//echo ' No es grupo;  level '.$data['level'].'; padre= '.$nPadre;
			//NO es grupo procesamos como tarea simple
			//echo '; nreg= '.
			$nreg = count($aRefnumber[$data['level']-1][$nParent]);
			if (empty($nreg))
			{
				$aRef[$data['level']][$id] = $nPadre + 1;
				//echo  '; id= '.$id;
				//echo '; numberref= '.
				$aNumberref[$id] = $aRef[$data['level']][$id];
			}
			else
			{
				$aRef[$data['level']][$id] = $nPadre +$nreg + 1;
				//echo  '; id= '.$id;
				//echo '; numberref= '.
				$aNumberref[$id] = $aRef[$data['level']][$id];
			}
			$aRefnumber[$data['level']-1][$nParent][$id] = $id;
		}
	}
	else
	{
		$lGrupo = 1;
		//echo '<br>grupo ';
		//busco el maximo numero registrado
		//echo ' nreg= '.
		$nreg = count($aRefnumber[$data['level']]);
		if (empty($nreg))
		{
			$aRef[$data['level']][$id] = $nOrder * ($nreg + 1);
			//echo  '; id= '.$id;
			//echo '; numberref= '.
			$aNumberref[$id] = $aRef[$data['level']][$id];
			$aRefnumber[0][$id][0][$id] = array();
		}
		else
		{
			$aRef[$data['level']][$id] = $nOrder * ($nreg+1);
			//echo  '; id= '.$id;
			//echo '; numberref== '.
			$aNumberref[$id] = $aRef[$data['level']][$id];
			$aRefnumber[0][$id][0][$id] = array();
		}
	}
	return array($aRef,$aNumberref,$aRefnumber);
	//obtenemos cual es el ultimo registro por cada grupo
}

function getformatdate($seldate,$date)
{
	$ampmo = '';
	switch ($seldate)
	{
		case 0:
	  //dd/mm/yyyy
		$aDateo1 = explode(' ',$date);
		$aDateo = explode('/',$aDateo1[0]);
		$aHouro = explode(':',$aDateo1[1]);
		$ampmo = $aDateo1[2];
		if (empty($ampmo))
			$date = dol_mktime($aHouro[0],$aHouro[1],0,$aDateo[1],$aDateo[0],$aDateo[2],'user');
		else
			$date = dol_mktime($aHouro[0]+12,$aHouro[1],0,$aDateo[1],$aDateo[0],$aDateo[2],'user');
		break;
		case 1:
	  //dd-mm-yyyy
		$aDateo1 = explode(' ',$date);
		$aDateo = explode('-',$aDateo1[0]);
		$aHouro = explode(':',$aDateo1[1]);
		$ampmo = $aDateo1[2];
		if (empty($ampmo))
			$date = dol_mktime($aHouro[0],$aHouro[1],0,$aDateo[1],$aDateo[0],$aDateo[2],'user');
		else
			$date = dol_mktime($aHouro[0]+12,$aHouro[1],0,$aDateo[1],$aDateo[0],$aDateo[2],'user');
		break;
		case 2:
	  //mm/dd/yyyy
		$aDateo1 = explode(' ',$date);
		$aDateo = explode('/',$aDateo1[0]);
		$aHouro = explode(':',$aDateo1[1]);
		$ampmo = $aDateo1[2];
		if (empty($ampmo))
			$date = dol_mktime($aHouro[0],$aHouro[1],0,$aDateo[0],$aDateo[1],$aDateo[2],'user');
		else
			$date = dol_mktime($aHouro[0]+12,$aHouro[1],0,$aDateo[0],$aDateo[1],$aDateo[2],'user');
		break;
		case 3:
	  //mm-dd-yyyy
		$aDateo1 = explode(' ',$date);
		$aDateo = explode('-',$aDateo1[0]);
		$aHouro = explode(':',$aDateo1[1]);
		$ampmo = $aDateo1[2];
		if (empty($ampmo))
			$date = dol_mktime($aHouro[0],$aHouro[1],0,$aDateo[0],$aDateo[1],$aDateo[2],'user');
		else
			$date = dol_mktime($aHouro[0]+12,$aHouro[1],0,$aDateo[0],$aDateo[1],$aDateo[2],'user');
		break;
		case 4:
	  //yyyy/mm/dd
		$aDateo1 = explode(' ',$date);
		$aDateo = explode('/',$aDateo1[0]);
		$aHouro = explode(':',$aDateo1[1]);
		$ampmo = $aDateo1[2];
		if (empty($ampmo))
			$date = dol_mktime($aHouro[0],$aHouro[1],0,$aDateo[1],$aDateo[2],$aDateo[0],'user');
		else
			$date = dol_mktime($aHouro[0]+12,$aHouro[1],0,$aDateo[1],$aDateo[2],$aDateo[0],'user');
		break;
		case 5:
	  //yyyy-mm-dd
		$aDateo1 = explode(' ',$date);
		$aDateo = explode('-',$aDateo1[0]);
		$aHouro = explode(':',$aDateo1[1]);
		$ampmo = $aDateo1[2];
		if (empty($ampmo))
			$date = dol_mktime($aHouro[0],$aHouro[1],0,$aDateo[1],$aDateo[2],$aDateo[0],'user');
		else
			$date = dol_mktime($aHouro[0]+12,$aHouro[1],0,$aDateo[1],$aDateo[2],$aDateo[0],'user');
		break;

	}
	return $date;
}

function convertdate($aDatef,$selvalue,$date)
{
	$sel = $aDatef[$selvalue];
	switch ($sel)
	{
		case 0:
		list($day,$mes,$anio) = explode('/',$date);
		break;
		case 0:
		list($day,$mes,$anio) = explode('-',$date);
		break;
		case 0:
		list($mes,$day,$anio) = explode('/',$date);
		break;
		case 0:
		list($mes,$day,$anio) = explode('-',$date);
		break;
		case 0:
		list($anio,$mes,$day) = explode('/',$date);
		break;
		case 0:
		list($anio,$mes,$day) = explode('-',$date);
		break;
	}
	$newdate = dol_mktime(12, 0, 0, $mes, $day, $anio);
	return $newdate;
}


function calendario ($id,$year,$mes,$finDeSemana=1,$mostrarDiasNulos=1,$nivelH=2) {

	if (strlen($year)!=4) {$year=date('Y');}
	if (($mes<1 or $mes>12) or (strlen($mes)<1 or strlen($mes)>2)) {$year=date('n');}

	// Listados: días de la semana, letra inicial de los días de la semana, y meses
	$dias = array('Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado','Domingo');
	$diasAbbr = array('L','M','M','J','V','S','D');
	$meses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiempre','Octubre','Noviembre','Diciembre');

	// Se sacan valores que se utilizarán más adelante
	$diaInicial = dol_get_first_day($year,$mes);
	//echo 'iop 1 '.$diaInicial.' '.dol_print_date($diaInicial,'dayhour');
	//$diaInicial = $aNewday['mday'];
	//$diaInicial = gmmktime(0,0,0,$mes,1,$year);  // Primer día del mes dado
	//
	//echo '<hr>op2 '.$diaInicial.' '.dol_print_date($diaInicial,'dayhour');
	$diasNulos = (date("N",$diaInicial))-1; // Con 'N' la semana empieza en Lunes. Con 'w', en domingo
	if($diasNulos<0){$diasNulos = 7-abs($diasNulos);}
	$diasEnMes = date("t",$diaInicial); // Número de días del mes dado

	// Se abre la capa contenedora y se genera el encabezado del bloque de calendario
	$html .= '<div id="calendarioc">';
	$html .= '<h'.$nivelH.' class="encabezadoCalendario">Calendario</h'.$nivelH.'>';

	// Párrafos con la fecha actual y la fecha seleccionada
	$html .= '<p>Fecha actual: '.date('j').' de '.$meses[(intval(date('n'))-1)].' de '.date('Y').'</p>';
	$html .= '<p>Fecha seleccionada: ';
	if (isset($_GET['dia'])) {$html .= ''.$_GET['dia'].' de ';} // El día solo sale si se ha definido previamente en el parámetro 'dia' de la URL
	$html .= ''.$meses[($mes-1)].' de '.$year.'</p>';
	$html .= '<div class="tablac">';


	// Enlaces al mes anterior y al siguiente
	$html .= '<p>Navegación por meses:</p>';
	$html .= '<ul id="calNavMeses">';
	$aArray = dol_get_prev_month($mes, $year);
	$mesAnterior = $aArray['month'];
	$yearMesAnterior = $aArray['year'];

	//$yearMesAnterior = date('Y',$enlaceAnterior1);
	//$enlaceSiguiente1 = gmmktime(0,0,0,($mes+1),1,$year);
	//echo '<hr>sig '.$mesSiguiente = date('n',$enlaceSiguiente1);
	//$yearMesSiguiente = date('Y',$enlaceSiguiente1);
	$aArray = dol_get_next_month($mes, $year);
	$mesSiguiente = $aArray['month'];
	$yearMesSiguiente = $aArray['year'];
	$html .= '<li class="anterior"><a href="?id='.$id.'&mes='.$mesAnterior.'&amp;ano='.$yearMesAnterior.'"><span>Mes anterior ('.$meses[($mesAnterior-1)].')</span></a></li>';
	$html .= '<li class="siguiente"><a href="?id='.$id.'&mes='.$mesSiguiente.'&amp;ano='.$yearMesSiguiente.'"><span>Mes siguiente ('.$meses[($mesSiguiente-1)].')</span></a></li>';
	$html .= '</ul>';

	// Enlaces al año anterior y al siguiente
	$html .= '<p>Navegación por años:</p>';
	$html .= '<ul id="calNavYears">';
	$aArray = dol_get_prev_month($mes, $year-1);
	$yearAnterior = $aArray['year'];
	$aArray = dol_get_prev_month($mes, $year+1);
	$yearSiguiente = $aArray['year'];
	$html .= '<li class="anterior"><a href="?id='.$id.'&mes='.$mes.'&amp;ano='.$yearAnterior.'"><span>Año anterior (</span>'.$yearAnterior.'<span>)</span></a></li>';
	$html .= '<li class="siguiente"><a href="?id='.$id.'&mes='.$mes.'&amp;ano='.$yearSiguiente.'"><span>Año siguiente (</span>'.$yearSiguiente.'<span>)</span></a></li>';
	$html .= '</ul>';

	// Se abre la tabla que contiene el calendario
	$html .= '<table class="tableline">';

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
	//ultimo dia del mes anterior
	$ultimodiaanterior = dol_get_last_day($yearMesAnterior,$mesAnterior);
	$aDayult = dol_getdate($ultimodiaanterior);
	$diaant = $aDayult['mday']-$diasNulos+1;
	$enSegundosNulo = dol_mktime(0,0,0,$mesAnterior,$diaant,$yearMesAnterior);
	if ($mostrarDiasNulos==1)
	{
		// Generación de los TD con días nulos si está activado que se muestren
		$dni=$diasNulos;$i=0;
		while ($i<$diasNulos)
		{
			$aDay = dol_getdate($enSegundosNulo);
			//$enSegundosNulo = dol_mktime(0,0,0,$aDay['mon'],$aDay['mday'],$aDay['year']);
			$dmNulo = date('j',$enSegundosNulo);
			$idFechaNulo = 'cal-'.date('Y-m-d',$enSegundosNulo);
			$html .= '<td id="'.$idFechaNulo.'" class="diaNulo"><span class="dia"><span class="enlace">'.$dmNulo.'</span></span></td>';
			$dni--;
			$i++;
			$aDay = dol_get_next_day($aDay['mday'], $aDay['mon'], $aDay['year']);
			$enSegundosNulo = dol_mktime(0,0,0,$aDay['month'],$aDay['day'],$aDay['year']);
		}
	}



	// Se generan los TD con los días del mes
	$dm=1;$x=0;$ds=$diasNulos+1;
	$aHoy = dol_getdate(dol_now());
	while ($dm<=$diasEnMes)
	{
		if(($x+$diasNulos)%7==0 and $x!=0) {$html .= '</tr>';} // Se evita el cierre del TR si no hay días nulos iniciales
		if(($x+$diasNulos)%7==0) {$html .= '<tr>';$ds=1;}
		$enSegundosCalendario = gmmktime(0,0,0,$mes,$dm,$year); // Fecha del día generado en segundos
		$enSegundosCalendario = dol_mktime(0,0,0,$mes,$dm,$year);
		$enSegundosActual = gmmktime(0,0,0,date('n'),date('j'),date('Y')); // Fecha actual en segundos
		$enSegundosActual = dol_mktime(0,0,0,$aHoy['mon'],$aHoy['mday'],$aHoy['year']);

		$enSegundosSeleccionada = gmmktime(0,0,0,$_GET['mes'],$_GET['dia'],$_GET['ano']); // Fecha seleccionada, en segundos
		$enSegundosSeleccionada = dol_mktime(0,0,0,$_GET['mes'],$_GET['dia'],$_GET['ano']);

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
		if (($ds<6 and $finDeSemana==0) or $finDeSemana!=0)
		{
			// Si el fin de semana está activado, o el día es de lunes a viernes
			$tagEnlace='a';
			$atribEnlace='href="?id='.$id.'&dia='.$link_dia.'&amp;mes='.$link_mes.'&amp;ano='.$link_year.'"';
		}
		if ($ds>5 and $finDeSemana==0)
		{
			// Si el fin de semana está desactivado y el día es sábado o domingo
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
	if ($mostrarDiasNulos==1)
	{
		// Generación de días nulos (si se activa mostrar los días nulos)
		$dnf=0;
		while ($dnf<$diasNulosFinales) {
			$enSegundosNulo = gmmktime(0,0,0,($mes+1),($dnf+1),$year);
			$enSegundosNulo = dol_mktime(0,0,0,($mes+1),($dnf+1),$year);
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
function limpiatexto($texto)
{
	$textoLimpio = preg_replace('([^A-Za-z])', '', $texto);
	return $textoLimpio;
}

/*numerico a literal DSO*/
function num2texto($numero, $moneda = "Bolivianos", $singular = "Boliviano") {
	/* Obtenida de www.hackingballz.com*/
	/* Si es 0 el número, no tiene caso procesar toda la información */
	if( $numero == 0 || !isset( $numero ) ) {
		return strtoupper( "CERO $moneda 00/100" );
	}
		/* En caso que sea un peso, pues igual que el 0 aparte que no muestre
		  el plural "pesos"
		*/
		  if( $numero == 1 ) {
		  	return strtoupper( "UN $singular 00/100" );
		  }

		//$numeros["unidad"][0][0]="cero";
		  $numeros["unidad"][1][0] = "un";
		  $numeros["unidad"][2][0] = "dos";
		  $numeros["unidad"][3][0] = "tres";
		  $numeros["unidad"][4][0] = "cuatro";
		  $numeros["unidad"][5][0] = "cinco";
		  $numeros["unidad"][6][0] = "seis";
		  $numeros["unidad"][7][0] = "siete";
		  $numeros["unidad"][8][0] = "ocho";
		  $numeros["unidad"][9][0] = "nueve";

		  $numeros["decenas"][1][0] = "diez";
		  $numeros["decenas"][2][0] = "veinte";
		  $numeros["decenas"][3][0] = "treinta";
		  $numeros["decenas"][4][0] = "cuarenta";
		  $numeros["decenas"][5][0] = "cincuenta";
		  $numeros["decenas"][6][0] = "sesenta";
		  $numeros["decenas"][7][0] = "setenta";
		  $numeros["decenas"][8][0] = "ochenta";
		  $numeros["decenas"][9][0] = "noventa";
		  $numeros["decenas"][1][1][0] = "dieci";
		  $numeros["decenas"][1][1][1] = "once";
		  $numeros["decenas"][1][1][2] = "doce";
		  $numeros["decenas"][1][1][3] = "trece";
		  $numeros["decenas"][1][1][4] = "catorce";
		  $numeros["decenas"][1][1][5] = "quince";
		  $numeros["decenas"][2][1] = "veinte y ";
		  $numeros["decenas"][3][1] = "treinta y ";
		  $numeros["decenas"][4][1] = "cuarenta y ";
		  $numeros["decenas"][5][1] = "cincuenta y ";
		  $numeros["decenas"][6][1] = "sesenta y ";
		  $numeros["decenas"][7][1] = "setenta y ";
		  $numeros["decenas"][8][1] = "ochenta y ";
		  $numeros["decenas"][9][1] = "noventa y ";

		  $numeros["centenas"][1][0] = "cien";
		  $numeros["centenas"][2][0] = "doscientos ";
		  $numeros["centenas"][3][0] = "trecientos ";
		  $numeros["centenas"][4][0] = "cuatrocientos ";
		  $numeros["centenas"][5][0] = "quinientos ";
		  $numeros["centenas"][6][0] = "seiscientos ";
		  $numeros["centenas"][7][0] = "setecientos ";
		  $numeros["centenas"][8][0] = "ochocientos ";
		  $numeros["centenas"][9][0] = "novecientos ";
		  $numeros["centenas"][1][1] = "ciento ";

		  $postfijos[1][0] = "";
		  $postfijos[10][0] = "";
		  $postfijos[100][0] = "";
		  $postfijos[1000][0] = " mil ";
		  $postfijos[10000][0] = " mil ";
		  $postfijos[100000][0] = " mil ";
		  $postfijos[1000000][0] = " millon ";
		  $postfijos[10000000][0] = " millon ";
		  $postfijos[100000000][0] = " millon ";
		  $postfijos[1000000][1] = " millones ";
		  $postfijos[10000000][1] = " millones ";
		  $postfijos[100000000][1] = " millones ";

		  $decimal_break = ".";
			//echo "test run on ".$numero."<br>";
		  $entero = strtok( $numero, $decimal_break);
		  $decimal = strtok( $decimal_break );
		  if ( $decimal == "" ) {
		  	$decimal = "00";
		  }
		  if ( strlen( $decimal ) < 2 ) {
		  	$decimal .= "0";
		  }
		  if ( strlen( $decimal ) > 2 ) {
		  	$decimal = substr( $decimal, 0, 2 );
		  }
		  $decimal .= '/100';
		  $entero_breakdown = $entero;

		  $breakdown_key = 1000000000000;
		  $num_string = "";
		  while ( $breakdown_key > 0.5 ) {
		  	$breakdown["entero"][$breakdown_key]["number"] =
		  	floor( $entero_breakdown/$breakdown_key );

		  	if ( $breakdown["entero"][$breakdown_key]["number"] > 0 ) {
		  		$breakdown["entero"][$breakdown_key][100] =
		  		floor( $breakdown["entero"][$breakdown_key]["number"] / 100 );
		  		$breakdown["entero"][$breakdown_key][10] =
		  		floor( ( $breakdown["entero"][$breakdown_key]["number"] % 100 )
		  			/ 10 );
		  		$breakdown["entero"][$breakdown_key][1] =
		  		floor( $breakdown["entero"][$breakdown_key]["number"] % 10 );

		  		$hundreds = $breakdown["entero"][$breakdown_key][100];
				// if not a closed value at hundredths
		  		if ( ( $breakdown["entero"][$breakdown_key][10]
		  			+ $breakdown["entero"][$breakdown_key][1] ) > 0 ) {
		  			$chundreds = 1;
		  	} else {
		  		$chundreds = 0;
		  	}

		  	if ( isset( $numeros["centenas"][$hundreds][$chundreds] ) ) {
		  		$num_string .= $numeros["centenas"][$hundreds][$chundreds];
		  	} else {
		  		if( isset( $numeros["centenas"][$hundreds][0] ) ) {
		  			$num_string .= $numeros["centenas"][$hundreds][0];
		  		}
		  	}

		  	if ( ( $breakdown["entero"][$breakdown_key][1] ) > 0 ) {
		  		$ctens = 1;
		  		$tens = $breakdown["entero"][$breakdown_key][10];
		  		if ( ( $breakdown["entero"][$breakdown_key][10] ) == 1 ) {
		  			if ( ( $breakdown["entero"][$breakdown_key][1] ) < 6 ) {
		  				$cctens = $breakdown["entero"][$breakdown_key][1];
		  				$num_string .=
		  				$numeros["decenas"][$tens][$ctens][$cctens];
		  			} else {
		  				$num_string .= $numeros["decenas"][$tens][$ctens][0];
		  			}
		  		} else {
		  			if( isset( $numeros["decenas"][$tens][$ctens] ) ){
		  				$num_string .= $numeros["decenas"][$tens][$ctens];
		  			}
		  		}
		  	} else {
		  		$ctens = 0;
		  		$tens = $breakdown["entero"][$breakdown_key][10];
		  		if( isset( $numeros["decenas"][$tens][$ctens] ) ) {
		  			$num_string .= $numeros["decenas"][$tens][$ctens];
		  		}
		  	}

		  	if ( !( isset( $cctens ) ) ) {
		  		$ones = $breakdown["entero"][$breakdown_key][1];
		  		if ( isset( $numeros["unidad"][$ones][0] ) ) {
		  			$num_string .= $numeros["unidad"][$ones][0];
		  		}
		  	}

		  	$cpostfijos = -1;
		  	if ( $breakdown["entero"][$breakdown_key]["number"] > 1 ) {
		  		$cpostfijos = 1;
		  	}

		  	if ( isset( $postfijos[$breakdown_key][$cpostfijos] ) ) {
		  		$num_string .= $postfijos[$breakdown_key][$cpostfijos];
		  	} else {
		  		$num_string .= $postfijos[$breakdown_key][0];
		  	}
		  }
		  unset( $cctens );
		  $entero_breakdown %= $breakdown_key;
		  $breakdown_key /= 1000;
		}
		$letras = $num_string . ' ' . $decimal . ($moneda?" $moneda":"");
		$letras = strtoupper($letras);
		return $letras;
	}
	?>