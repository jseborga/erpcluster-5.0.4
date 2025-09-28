<?php

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
	$letras = $num_string . ' ' . $decimal . " $moneda";
	$letras = strtoupper( $letras );
	return $letras;
}
?>