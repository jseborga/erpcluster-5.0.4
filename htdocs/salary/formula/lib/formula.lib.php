<?php
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

function proc_formula($refFormula,$idUser,$fk_period,$impres=False)
{
	global $db,$conf,$langs,$object,$objecto,$objectU,$objectC,$objectCo,$objectgt,$objectgf,$objectf,$objectpe;
	$_SESSION['param']['actual'] = array('idUser'    => $idUser,
		'fk_period' => $fk_period);
	$_SESSION['param']['nDiasTrab'] = $conf->global->SALARY_NRO_DIAS_LABORAL;

	$sql = "SELECT rowid, fk_operator, type, changefull, andor, sequen, nmonth ";
	$sql.= " FROM ".MAIN_DB_PREFIX."p_formulas_det ";
	$sql.= " WHERE ref_formula = '".$refFormula."' ";
	$sql.= " AND entity = ".$conf->entity;
	$sql.= " AND state != -1 ";
	$sql.= " ORDER BY sequen ";

	$result = $db->query($sql);
	$uid = $idUser;
	if ($result)
	{
		//buscando el periodo
		$per_date_ini = '';
		$per_date_fin = '';
		$res = $objectpe->fetch($fk_period);
		if ($res)
		{
			$per_date_ini = $objectpe->date_ini;
			$per_date_fin = $objectpe->date_fin;
			$anio = $objectpe->anio;
		}
		$num = $db->num_rows($result);
		$i = 0;
		//debe ser desde 1
		if ($num)
		{
			$var=True;
			while ($i < $num)
			{
				$obj = $db->fetch_object($result);
				$nmonth = $obj->nmonth;
				if (empty($nmonth) || is_null($nmonth)) $nmonth = 0;
				//buscar operador
				$objecto->fetch($obj->fk_operator);
				//echo '<br>operator '.$objecto->operator.' | '.$obj->type;

				if ($objecto->operator == "sum()")
				{
					$campo = $obj->changefull;
					if ($obj->type == 'p_concept')
					{
						//echo '<br>sumando ';
						$suma = 0;
						//sumamos los bonos para agregar al basico
						list($nEntity,$cRef) = explode('|',$campo);
						$objectC->fetch_ref($cRef);

						//suma los bonos al basico
						//echo '<hr>concept '.$objectC->id. ' '.$objectC->rowid.' '.$cRef;
						$suma += suma_concept_bonus($idUser,$objectC->id,$per_date_ini,$per_date_fin);
						if ($impres)
						{
							print '<tr><td>'.$langs->trans('Line').' '.$obj->sequen.'</td><td align="center">'.$objecto->operator.'</td><td>'.$suma.'</td></tr>';
						}
						//se recupera el valor del campo changefull en la tabla user
						$cFormula.= " + ".$suma;
					}
				}
				else
				{
					$cFormula .= " ".$objecto->operator." ";
					$campo = $obj->changefull;
					//echo '<hr>objtype '.$obj->type;
					if ($obj->type == 'formula')
					{
						//echo '<br>campo '.$campo.' FROMULA  '.$suma.' res '.$res;
						//se ejecuta la formula
						//verificamos si las variables tiene valor
						if (empty($res)) $res = 0;
						if (empty($suma)) $suma = 0;
						$newFormula = 'formula_'.$campo.'('.$idUser.','.$res.','.$suma.')';
						//eval("\$res = \"$newFormula\";");
						eval('$res = '.'formula_'.$campo.'('.$idUser.','.$res.','.$suma.','.$nmonth.')'.';');
						//echo '<Br>res '.$res;
						$cFormula = $res;
						if ($impres)
						{
							eval("\$res = \"$cFormula\";");
							//eval('$res = '.$cFormula.';');
							//echo '<br>res formula '. $res;
							print '<tr><td>'.$langs->trans('Line').' '.$obj->sequen.'</td><td align="center">'.$objecto->operator.'</td><td>'.$res.'</td></tr>';
						}
					}

					if ($obj->type == 'valor')
					{
						//se recupera el valor del campo changefull en la tabla user
						$cFormula.= $campo;
						if ($impres)
						{
							print '<tr><td>'.$langs->trans('Line').' '.$obj->sequen.'</td><td align="center">'.$objecto->operator.'</td><td>'.$campo.'</td></tr>';
						}

					}
					if ($obj->type == 'p_users')
					{
						//se recupera el valor del campo changefull en la tabla user
						$objectCo->fetch_vigent($uid,1);
						//echo '<hr>campo '.$objectCo->$campo.' '.$campo.' '.$uid;
						$cFormula.= $objectCo->$campo;
						//eval("\$res = \"$cFormula\";");
						eval('$res = '.$cFormula.';');
						if ($impres)
						{
							print '<tr><td>'.$langs->trans('Line').' '.$obj->sequen.'</td><td align="center">'.$objecto->operator.'</td><td>'.$objectCo->$campo.'</td></tr>';
						}
					}
					if ($obj->type == 'p_concept')
					{
						$nmonth = $obj->nmonth;

						list($nEntity,$cRef) = explode('|',$campo);
						$objectC->fetch_ref($cRef);

						$suma = 0;
						//buscando el resultado del concepto para el usuario y el periodo

						$sql = "SELECT amount ";
						$sql.= " FROM ".MAIN_DB_PREFIX."p_salary_present ";
						$sql.= " WHERE fk_concept = ".$objectC->id;
						$sql.= " AND fk_user = ".$idUser;
						$sql.= " AND fk_period = ".$fk_period;

						$resql = $db->query($sql);
						if ($resql)
						{
							$num1 = $db->num_rows($resql);
							$j = 0;
							if ($num1)
							{
								$var=True;
								while ($j < $num1)
								{
									$objsum = $db->fetch_object($resql);
									$suma += $objsum->amount;
									$j++;
								}
							}
							$cFormula.= $suma;
							//echo '<hr>cformula '.$cFormula;
							//aumentado
							eval('$res = '.$cFormula.';');

						}
						if ($impres)
						{
							print '<tr><td>'.$langs->trans('Line').' '.$obj->sequen.'</td><td align="center">'.$objecto->operator.'</td><td>'.$suma.'</td></tr>';
						}

					}
					if ($obj->type == 'p_generic_table')
					{
						//echo '<hr>campo '.$campo;
						list($nEntity,$cTableCod,$cSequen) = explode('|',$campo);
						//echo '<br>'.$nEntity.' '.$cTableCod.' '.$cSequen;
						$objectgt->fetch_table_cod($cTableCod,$cSequen);
						//echo '<br>buscado '.$objectgt->id.' ctablecod '.$cTableCod.' csequen '.$cSequen;
						$suma = 0;
						//echo '<hr>ant form '.$objecto->operator.' '.$cFormula.' '.$res.' ||campo '.$campo;
						list($cFormula,$suma) = calc_generic($objecto->operator,$cFormula,$res,$objectgt->id);
						//echo '<br>cformula '.$cFormula.' suma '.$suma;
						if ($impres)
						{
							print '<tr><td>'.$langs->trans('Line').' '.$obj->sequen.'</td><td align="center">'.$objecto->operator.'</td><td>'.$suma.'</td></tr>';
						}

					}
					//echo '<hr> '.$cFormula;
					//eval("\$res = \"$cFormula\";");
					eval('$res = '.$cFormula.';');
					$cFormula = $res;
				}
				//echo '<BR>RESULTADO RE '.$res.' '.dol_print_date($res);
				//echo '<br>cFormula '.$cFormula;
				$i++;
			}
			//eval("\$res = \"$cFormula\";");
			eval('$res = '.$cFormula.';');
			//echo '<hr>res '.$total = $res;exit;
		}
	}
	return $res;
}

//funcion especifica para bono antiguedad
function formula_S005($idUser=0,$res=0,$suma=0,$nmonth=0)
{
	global $conf;

	//echo '<hr>recibeS005 '.$idUser.' '.$res.' '.$suma;
	//$idUser = $_SESSION['param']['actual']['idUser'];
	$nDiasLab = $conf->global->SALARY_NRO_DIAS_LABORAL;
  	//obtener numero de cuotas a calcular
	$nCuotaBA = $conf->global->SALARY_NRO_BASIC_BONO_ANT;
  	//salario basico
	$nAnual   = $conf->global->SALARY_NRO_DIAS_ANUAL_LABORAL;
	if (empty($nAnual)) $nAnual = 360;
	$nSalaryBasic = $conf->global->SALARY_BASIC_AMOUNT;

  	//veriicamos si esta activo el calculo de bonos antiguedad en el contrato
	$nBonus = formula_S_sinobonoant();
	if ($nBonus == 1)
	{
      //no corresponde
		$nDiasTrab = formula_S_diastrab($suma);
		$nTotalAnio = $nDiasTrab / $nAnual;

		$nTotalSalary = $nSalaryBasic * $nCuotaBA * $suma / 100;
	}
	else
		$nTotalSalary = 0;
	return $nTotalSalary;
}

//funcion especifica para bono antiguedad
function formula_S_bonoant($idUser=0,$res=0,$suma=0,$nmonth=0)
{
	global $conf,$objectCo;

	//echo '<hr>recibebonoant '.$idUser.' '.$res.' '.$suma;
	//$idUser = $_SESSION['param']['actual']['idUser'];
	$nDiasLab = $conf->global->SALARY_NRO_DIAS_LABORAL;
  	//obtener numero de cuotas a calcular
	$nCuotaBA = $conf->global->SALARY_NRO_BASIC_BONO_ANT;
  	//salario basico
	$nAnual   = $conf->global->SALARY_NRO_DIAS_ANUAL_LABORAL;
	if (empty($nAnual)) $nAnual = 360;
	$nSalaryBasic = $conf->global->SALARY_BASIC_AMOUNT;

  	//veriicamos si esta activo el calculo de bonos antiguedad en el contrato
	$nBonus = $objectCo->bonus_old;
	//$nBonus = formula_S_sinobonoant();
	if ($nBonus == 1)
	{
		$nTotalAnio = $suma;
		$nTotalSalary = $nSalaryBasic * $nCuotaBA * $suma / 100;
	}
	else
		$nTotalSalary = 0;
	return $nTotalSalary;
}

//funcion especifica para calculo años trabajados
function formula_S006($idUser=0,$res=0,$suma=0,$nmonth=0)
{
	global $conf;
	$nDiasLabAn = $conf->global->SALARY_NRO_DIAS_ANUAL_LABORAL;
	$nAnio = $suma / $nDiasLabAn;
  //echo '<hr>'.  $nAnio;
  //exit;
  //recorriendo el intervalo para determinar el porcentaje correspondiente.
	return $nAnio;
}

//funcion especifica para bono antiguedad  //VERIFICAR NO SE CALCULA NADA CON LOS VALORES RECIBIDOS
function formula_liqpayment($idUser=0,$type_cod=0,$suma=0)
{
	global $db, $conf;
	$idUser = $_SESSION['param']['actual']['idUser'];
	$fk_period = $_SESSION['param']['actual']['fk_period'];

  //abrimos la tabla salary_present Base ingresos
	$sql = " SELECT c.type_cod, sp.amount ";
	$sql.= " FROM ".MAIN_DB_PREFIX."p_salary_present AS sp";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_concept AS c ON sp.fk_concept = c.rowid ";
	$sql.= " WHERE sp.entity = ".$conf->entity;
	$sql.= " AND sp.fk_user = ".$idUser;
	$sql.= " AND sp.fk_period = ".$fk_period;
	$sql.= " AND c.type_cod IN (1,2) ";
	$resql = $db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ( $i < $num)
			{
				$obj = $db->fetch_object($resql);
				if ($obj->type_cod == 1)
					$sumaIng += $obj->amount;
				if ($obj->type_cod == 2)
					$sumaSal += $obj->amount;
				$i++;
			}
		}
	}
	$liquid = $sumaIng - $sumaSal;

	//echo '<br>RES '.$liquid.' = '.$sumaIng.' - '.$sumaSal;
	if ($type == 0)
		return $liquid;
	if ($type == 1)
		return $sumaIng;
	if ($type == 2)
		return $sumaSal;
}

//funcion especifica para bono antiguedad
function formula_S_sinobonoant()
{
	global $conf;
	//$idUser = $_SESSION['param']['actual']['idUser'];
	$nBonus = formula_S_diaslab($idUser,'bonus_old');
	return $nBonus;
}
//funcion especifica para bono antiguedad
/*
* idUser=Id de usuario / adherent
* res = resultado
*suma = total suma
*/
function formula_S_diastrab($idUser=0,$res=0,$suma=0,$nmonth=0)
{
	global $conf;
	$nTotalDias = formula_S_diaslab($idUser, 'nDiasTrab', $res, $nmonth) + 0;
	if ($nTotalDias > $conf->global->SALARY_NRO_DIAS_LABORAL)
		$nTotalDias = $conf->global->SALARY_NRO_DIAS_LABORAL;
	return $nTotalDias;
}
function formula_S_totaldiastrab($idUser=0,$res=0,$suma=0,$nmonth=0)
{
	global $conf;
	$nTotalDias = formula_S_diaslab($idUser, 'nTotalDias', $res, $nmonth) + 0;
	return $nTotalDias;
}
function formula_S_totalyeartrab($idUser=0,$res=0,$suma=0,$nmonth=0)
{
	global $conf;
	$nTotalDias = floor(formula_S_diaslab($idUser, 'nTotalAnio', $res, $nmonth) + 0);
	return $nTotalDias;
}
//numero de dias trabajados para aguinaldo
function formula_S_totalaguitrab($idUser=0,$res=0,$suma=0,$nmonth=0)
{
	global $conf;
	$nTotalDias = floor(formula_S_diaslab($idUser, 'nTotalDiasAgui', $res, $nmonth) + 0);
	return $nTotalDias;
}
//promedio ultimos tres sueldos para aguinaldo
function formula_S_promediosueldo($idUser=0,$res=0,$suma=0,$nmonth=0)
{
	global $conf;
	$average = floor(formula_S_diaslab($idUser, 'average', $res, $nmonth) + 0);
	return $average;
}

function formula_calc_solidario($idUser=0,$res,$suma=0)
{
	global $langs,$conf,$db,$objectgt;
  //recuperamos el codigo de la tabla generica
	$codeBase = $conf->global->SALARY_CONCEPT_BASE_SOLIDARY_CONTRIBUTION;
	$code = $conf->global->SALARY_CODE_TABLE_GENERIC_SOLIDARY_CONTRIBUTION;
	if (empty($code))
	{
		print '<div class="error">'.$langs->trans('Error, NO esta definido SALARY_CODE_TABLE_GENERIC_SOLIDARY_CONTRIBUTION en el sistema. Vaya a Inicio, Configuracion, Varios.').'</div>';
		exit;
	}
	//$idUser = $_SESSION['param']['actual']['idUser'];
	$fk_period = $_SESSION['param']['actual']['fk_period'];

	$cTable_cod = $code;


	$sql1 = "SELECT u.rowid AS id, u.type_value ";
	$sql1.= " FROM ".MAIN_DB_PREFIX."p_generic_table AS u ";
	$sql1.= " WHERE u.table_cod = '".$cTable_cod."'";
  //$sql1.= " AND u.state = 1 ";
	$sql1.= " AND u.entity = ".$conf->entity;
	$resql1 = $db->query($sql1);
	if ($resql1)
	{
		$num2 = $db->num_rows($resql1);
		$k = 0;
		if ($num2)
		{
			$var=True;
			while ($k < $num2)
			{
				$obj1 = $db->fetch_object($resql1);
				if ($obj1->type_value == 1)
					$campoDe = $obj1->id;
				if ($obj1->type_value == 2)
					$campoA = $obj1->id;
				if ($obj1->type_value == 3)
					$campoR = $obj1->id;
				$k++;
			}
			$cIds = $campoDe.','.$campoA.','.$campoR;
			$aArrGTT = array('de'  => $campoDe,
				'a'   => $campoA,
				'res' => $campoR);
		}
	}
	$total = 0;
  //es el valor en forma directa
	$sql1 = "SELECT u.sequen, u.generic_table_ref, u.field_value, ";
	$sql1.= " t.rowid AS id, t.type_value";
	$sql1.= " FROM ".MAIN_DB_PREFIX."p_generic_field AS u ";
	$sql1.= " INNER JOIN ".MAIN_DB_PREFIX."p_generic_table AS t ";
	$sql1.= " ON u.generic_table_ref = t.ref ";

	$sql1.= " WHERE t.table_cod = '".$cTable_cod."'";
	$sql1.= " AND t.entity = ".$conf->entity;
	$sql1.= " ORDER BY u.sequen, t.type_value ";
  //echo $sql1.= " AND t.state = 1 ";
	$resql1 = $db->query($sql1);
	if ($resql1)
	{
		$num3 = $db->num_rows($resql1);
		$l = 0;
		if ($num3)
		{
			$var=True;
			while ($l < $num3)
			{
				$obj3 = $db->fetch_object($resql1);
				if ($obj3->id == $campoDe)
					$aArrDet[$obj3->sequen]['de'] = $obj3->field_value;
				if ($obj3->id == $campoA)
					$aArrDet[$obj3->sequen]['a'] = $obj3->field_value;
				if ($obj3->id == $campoR)
					$aArrDet[$obj3->sequen]['res'] = $obj3->field_value;
				$l++;
			}
		}
	}
	$ret = 0;
	//analizando
	//validando los limites
	//print_r($aArrDet);
	foreach((array) $aArrDet AS $i => $data)
	{
		if ($suma >= $data['de'])
		{

			$ret += (($suma - $data['de'])>0?($suma - $data['de']):0) * $data['res'] / 100;
			//echo 	 '<hr>'. $ret.' += ('.$suma.' - '.$data['de'].') * '.$data['res'].' / 100';

		}
	}
	$cFormula = $ret;
	return $ret;
}

function calc_generic($operator,$cFormula,$res,$campo)
{
	global $lang,$conf,$db,$objectgt;

	$objectgt->fetch($campo);
	if (empty($objectgt->limits) || $objectgt->limits == 2 || $objectgt->limits == -1)
	{
		$suma = 0;
      //no utiliza limites
      //es el valor en forma directa
		$sql = "SELECT field_value ";
		$sql.= " FROM ".MAIN_DB_PREFIX."p_generic_field ";
		$sql.= " WHERE generic_table_ref = '".$objectgt->ref."'";
		$resql = $db->query($sql);
		if ($resql)
		{
			$num1 = $db->num_rows($resql);
			$j = 0;
			if ($num1)
			{
				$var=True;
				while ($j <=$num1)
				{
					$objsum = $db->fetch_object($resql);
					$suma += $objsum->field_value;
					$j++;
				}
			}
		}
		$cFormula.= $suma;
	}
	else
	{
		$sumaAnt = $res;
      //valores definidos de limites
		$cTable_cod = $objectgt->table_cod;

		$sql1 = "SELECT u.rowid AS id, u.type_value ";
		$sql1.= " FROM ".MAIN_DB_PREFIX."p_generic_table AS u ";
		$sql1.= " WHERE u.table_cod = '".$cTable_cod."'";
      //$sql1.= " AND u.state = 1 ";
		$sql1.= " AND u.entity = ".$conf->entity;
		$resql1 = $db->query($sql1);
		if ($resql1)
		{
			$num2 = $db->num_rows($resql1);
			$k = 0;
			if ($num2)
			{
				$var=True;
				while ($k < $num2)
				{
					$obj1 = $db->fetch_object($resql1);
					if ($obj1->type_value == 1)
						$campoDe = $obj1->id;
					if ($obj1->type_value == 2)
						$campoA = $obj1->id;
					if ($obj1->type_value == 3)
						$campoR = $obj1->id;
					$k++;
				}
				$cIds = $campoDe.','.$campoA.','.$campoR;
				$aArrGTT = array('de'  => $campoDe,
					'a'   => $campoA,
					'res' => $campoR);
			}
		}
		$suma = 0;
      //es el valor en forma directa
		$sql1 = "SELECT t.table_cod, u.sequen, u.generic_table_ref, u.field_value, ";
		$sql1.= " t.rowid AS id, t.type_value";
		$sql1.= " FROM ".MAIN_DB_PREFIX."p_generic_field AS u ";
		$sql1.= " INNER JOIN ".MAIN_DB_PREFIX."p_generic_table AS t ";
		$sql1.= " ON u.generic_table_ref = t.ref ";

		$sql1.= " WHERE t.table_cod = '".$cTable_cod."'";
		$sql1.= " AND t.entity = ".$conf->entity;
		$sql1.= " ORDER BY u.sequen, t.type_value ";
      //echo $sql1.= " AND t.state = 1 ";
		$resql1 = $db->query($sql1);
		if ($resql1)
		{
			$num3 = $db->num_rows($resql1);
			$l = 0;
			if ($num3)
			{
				$var=True;
				while ($l < $num3)
				{
					$obj3 = $db->fetch_object($resql1);

					if ($obj3->id == $campoDe)
						$aArrDet[$obj3->sequen]['de'] = $obj3->field_value+0;
					if ($obj3->id == $campoA)
						$aArrDet[$obj3->sequen]['a'] = $obj3->field_value+0;
					if ($obj3->id == $campoR)
						$aArrDet[$obj3->sequen]['res'] = $obj3->field_value+0;
					$l++;
				}
			}
		}

      //validando los limites
      // echo '<pre>';
      // print_r($aArrDet);
      // echo '</pre>';
		foreach((array) $aArrDet AS $i => $data)
		{
	  //echo '<hr>	  if ('.$sumaAnt.' >= '.$data['de'].' && '.$sumaAnt.' <= '.$data['a'].')'.' res '.$data['res'];
			$data['de']+=0;
			$data['a']+=0;
			$data['res']+=0;
			if ($sumaAnt >= $data['de'] && $sumaAnt <= $data['a'])
			{
				$suma = $data['res'];
	      //echo '<hr>suma '.$suma = $data['res'];
	      //echo '<hr>multi '.$suma = $sumaAnt * $percent;

			}
		}
      //echo       $cFormula .= $operator.' '.$suma;exit;
		$cFormula .= ' '.$suma;

      // $sql = "SELECT field_value";
      // //se recupera el valor del campo changefull en la tabla generic_field
      // $objectgf->fetch($uid);
      // $cFormula.= $objectU->$campo;
	}
	return array($cFormula,$suma);
}

//valida el valor $res si >0 devuelve el mismo, si 0 o negativo devuelve 0
function formula_val_iva($idUser=0,$res=0,$suma=0)
{
  //echo $res;
	if ($res > 0)
		return $res;
	else
		return 0;
}

//valida el valor $res si >0 devuelve el mismo, si 0 o negativo devuelve 0
function formula_val_saldo_iva($idUser=0,$res=0,$suma=0)
{
  //echo '<hr>antes '.$res;
	if ($res > 0)
		return $res;
	else
	{
		return 0;
	}
}

function formula_calc_saldo_iva($idUser=0,$res=0,$suma=0)
{
	global $db,$conf,$langs,$object,$objecto,$objectU,$objectC,$objectCo,$objectgt,$objectgf,$objectf,$objectpe,$objhistory;
	$fk_periodant = 0;
	$ref = $conf->global->SALARY_CONCEPT_BALANCE_RC_IVA;

  //buscamos el concepto
	$objc = new Pconcept($db);
	$res = $objc->fetch('',$ref);
	if ($res > 0)
		$fk_concept = $objc->id;
	else
	{
		print $mesg='<div class="error">'.$langs->trans('Error, Not exist concept.').' '.$langs->trans('Please register').'</div>';
		exit;
	}
	if (empty($ref))
	{
		print $mesg='<div class="error">'.$langs->trans('Error, Not defined constant').' SALARY_CONCEPT_BALANCE_RC_IVA '.$langs->trans('Please log in Menu, Settings, Miscellaneous').'</div>';
		exit;
	}
  //buscando en la tabla salary_history el saldo anterior aprobado
	$iduser    = $_SESSION['param']['actual']['idUser'];
	$fk_period = $_SESSION['param']['actual']['fk_period'];
	$month = $objectpe->mes;
	$year  = $objectpe->anio;
	$fk_proces = $objectpe->fk_proces;
	$fk_type_fol = $objectpe->fk_type_fol;

  //restamos un mes
	if ($month == 1)
	{
		$month = 12;
		$year  = $year -1;
	}
	else
	{
		$month = $month - 1;
	}
	$month = (STRLEN($month) == 1?'0'.$month:$month);
  //buscamos el id del periodo
	$objper = new Pperiod($db);
	$res = $objper->fetch($month,$year);
	if ($res > 0)
	{
		$fk_periodant = $objper->id;
	}
  //buscamos en history
	require_once DOL_DOCUMENT_ROOT.'/salary/class/psalaryhistoryext.class.php';
	$objhistory = new Psalaryhistoryext($db);
	$res = $objhistory->fetch_salary_p($fk_user,$fk_period,$fk_proces,$fk_type_fol,$fk_concept,$state);
	if ($res>0)
		return $objhistory->amount;
	else
		return 0;
}

function suma_concept_bonus($idUser,$id,$per_date_ini, $per_date_fin)
{
	global $db, $conf;
	$sql = "SELECT ub.date_ini, ub.date_fin, ub.amount AS total, ub.type ";
	$sql.= " FROM ".MAIN_DB_PREFIX."p_user_bonus"." AS ub" ;
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_user AS u ";
	$sql.= " ON ub.fk_puser = u.rowid ";
	$sql.= " WHERE u.fk_user = ".$idUser;
	$sql.= " AND ub.fk_concept = ".$id;
  //$sql.= " AND u.state = 1 ";
	$sql.= " AND ub.state = 1 ";

	$resql = $db->query($sql);
	if ($resql)
	{
		$num1 = $db->num_rows($resql);
		$j = 0;
		if ($num1)
		{
			$var=True;
			while ($j < $num1)
			{
				$objsum = $db->fetch_object($resql);
				$objsum->date_ini = $db->jdate($objsum->date_ini);
				$objsum->date_fin = $db->jdate($objsum->date_fin);
	      //validando las fechas
				$lSuma = True;
	      //echo '<hr>'.$objsum->date_fin;
				if (empty($objsum->date_fin))
				{
					if ( $objsum->date_ini < $per_date_fin )
						$lSuma = True;
					else
						$lSuma = False;
				}
				else
				{
					if ($objsum->date_fin > $per_date_ini && $objSum->date_fin <= $per_date_fin)
						$lSuma = True;
					elseif ($objsum->date_fin > $per_date_fin)
						$lSuma = True;
					else
						$lSuma = False;
				}
				if ($lSuma)
				{
					if ($objsum->type == 1)
						$suma += $objsum->total;
					else
					{
						if ($objsum->date_ini > $per_date_ini &&
							$objsum->date_ini <= $per_date_fin)
						{
			  //restamos los dias trabajaods
							$dFin=dol_print_date($per_date_fin,'%Y%m%d');
							$dIni=dol_print_date($objsum->date_ini,'%Y%m%d');
							$nDiasTrab = $dFin-$dIni;
						}
						else
							$nDiasTrab = 30;
		      //validando
						$suma += $objsum->total/$_SESSION['param']['nDiasTrab']*$nDiasTrab;

					}
				}
				$j++;
			}
		}
	}
	return $suma;
}

//numero total de dias mes para calculo
function formula_S_nrodiasmes()
{
	global $conf;
	return $conf->global->SALARY_NRO_DIAS_LABORAL;
}

//funcion para calcular los dias laborales del empleado $id
//$op='nDiasTrab';
//$op='nTotalDias';
//$op='nTotalAnio';
//$op='bonus_old';
function formula_S_diaslab_backup($id=0,$op='nDiasTrab')
{
	global $db,$conf,$langs,$objectsp,$object,$objectpe,$objectC,$user,$fk_period,$fk_proces,$fk_type_fol;
	$langs->load("salary@salary");
	$sql = "SELECT p.rowid, p.lastname AS lastname, p.firstname AS firstname, ";
	$sql.= " pu.lastnametwo AS lastnametwo, ";
	$sql.= " pt.date_ini, pt.date_fin, pt.basic, pt.bonus_old ";
	$sql.= " FROM ".MAIN_DB_PREFIX."adherent AS p ";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."p_user AS pu ON p.rowid = pu.fk_user ";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_contract AS pt ON pt.fk_user = p.rowid ";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_charge AS pc ON pc.rowid = pt.fk_charge ";
	$sql.= " WHERE p.entity = ".$conf->entity;
	$sql.= " AND pt.state = 1 ";
	if ($id) $sql.= " AND p.rowid = ".$id;
	$sql.= " AND pt.fk_proces =".$fk_proces;
	$sql.= " ORDER BY p.lastname, pu.lastnametwo, p.firstname, pt.date_ini ";
  	//echo '<br>lab '.$sql;
	$resql=$db->query($sql);
	$lRet = false;
  //unset($_SESSION['aPlanilla']);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$lRet = true;
				$obj = $db->fetch_object($resql);
				//verificamos datos de si el empleado esta activo
				$lReg = True;
				$obj->date_ini = $db->jdate($obj->date_ini);
				$obj->date_fin = $db->jdate($obj->date_fin);
				if (empty($obj->data_fin) ||
					(!empty($obj->date_fin) &&
						$obj->date_fin > $objectpe->date_fin))
				{
					//echo '<br>adentro '.dol_print_date($obj->date_ini) .' <> '.dol_print_date($objectpe->date_ini);
					if ($obj->date_ini <= $objectpe->date_ini || ($obj->date_ini > $objectpe->date_ini && $obj->date_ini <= $objectpe->date_fin) )
					{
						//echo '<br>calcula';
						//calculamos los dias trabajados
						if ($obj->date_ini <= $objectpe->date_ini)
						{
							//echo '<br>dateini anterior al dateini period ';
							//verificamos que no tenga fecha fin
							if (empty($obj->date_fin))
							{
								//echo '<br>no tiene fecha fin';
								$nDiasTrab = $conf->global->SALARY_NRO_DIAS_LABORAL;
								$nTotalDias = $conf->global->SALARY_NRO_DIAS_LABORAL;
								$nTotalDias = totaldiastrab($obj->date_ini,$objectpe->date_fin);
							}
							else
							{
								//echo '<br>si tiene fecha fin';
								//echo '<br>'.dol_print_date($obj->date_fin,'day').' '.dol_print_date($objectpe->date_fin,'day');
								if ($obj->date_fin < $objectpe->date_fin)
								{

								}
								else if ($obj->date_fin <= $objectpe->date_fin)
								{
									//echo '<br>fecha fin menor al fecha fin del period ';
									$dFin=dol_print_date($obj->date_fin,'%Y%m%d');
									$dIni=dol_print_date($objectpe->date_ini,'%Y%m%d');
									$nDiasTrab = $dFin-$dIni + 1;
									$nDiasTrab = num_between_day($objectpe->date_ini, $obj->date_fin, 1);
									$aDateend = dol_getdate($obj->date_fin);
									if ($aDateend['mday']>30) $nDiasTrab--;

									$nTotalDias = totaldiastrab($obj->date_ini,$obj->date_fin);
								}
								else
								{
									//echo '<br>la fecha final es mayor a la fecha del periodo';
									//se calcula por el total de dias
									$nDiasTrab = $conf->global->SALARY_NRO_DIAS_LABORAL;
									$nTotalDias = $conf->global->SALARY_NRO_DIAS_LABORAL;
									$nTotalDias = totaldiastrab($obj->date_ini,$objectpe->date_fin);
								}
							}
						}
						else if ($obj->date_ini > $objectpe->date_ini && $obj->date_ini <= $objectpe->date_fin)
						{
							if (empty($obj->date_fin))
							{
								$aDate = dol_getdate($objectpe->date_fin);
								if ($aDate['mon'] == 2)
								{
									$dini = date('d',$obj->date_ini);
									$aDateini = dol_getdate($obj->date_ini);
									$dini = $aDateini['mday'];
									$dfin = $_SESSION['param']['nDiasTrab'];
									$nDiasTrab = $dfin-$dini+1;
									$nTotalDias = totaldiastrab($obj->date_ini,$objectpe->date_fin,1);
								}
								else
								{
									//restamos los dias trabajaods
									$dFin=dol_print_date($objectpe->date_fin,'%Y%m%d');
									$dIni=dol_print_date($obj->date_ini,'%Y%m%d');
									$nDiasTrab = $dFin-$dIni+1;
									$nDiasTrab = num_between_day($obj->date_ini, $objectpe->date_fin, 1);
									$aDateend = dol_getdate($objectpe->date_fin);
									if ($aDateend['mday']>30) $nDiasTrab--;

									$nTotalDias = totaldiastrab($obj->date_ini,$objectpe->date_fin);
								}
							}
							else
							{
								// echo '<br>si tiene fecha fin';
								// echo '<br>'.dol_print_date($obj->date_fin,'day').' '.dol_print_date($objectpe->date_fin,'day');
								if ($obj->date_fin <= $objectpe->date_fin)
								{
									//echo '<br>fecha fin menor al fecha fin del period ';
									$dFin=dol_print_date($obj->date_fin,'%Y%m%d');
									$dIni=dol_print_date($obj->date_ini,'%Y%m%d');
									$nDiasTrab = $dFin-$dIni + 1;
									$nDiasTrab = num_between_day($obj->date_ini, $obj->date_fin, 1);
									$aDateend = dol_getdate($obj->date_fin);
									if ($aDateend['mday']>30) $nDiasTrab--;

									$nTotalDias = totaldiastrab($obj->date_ini,$obj->date_fin);
								}
								else
								{
									//echo '<br>la fecha final es mayor a la fecha del periodo';
									$dFin=dol_print_date($objectpe->date_fin,'%Y%m%d');
									$dIni=dol_print_date($obj->date_ini,'%Y%m%d');
									$nDiasTrab = $dFin-$dIni + 1;
									$nDiasTrab = num_between_day($obj->date_ini, $objectpe->date_fin, 1);
									$aDateend = dol_getdate($objectpe->date_fin);
									if ($aDateend['mday']>30) $nDiasTrab--;
									$nTotalDias = totaldiastrab($obj->date_ini,$obj->date_fin);
								}
							}
						}



						$nTotalAnio = $nTotalDias / 360;
		      // $_SESSION['aDiastrab'][$obj->rowid]['id']         = $obj->rowid;
		      // $_SESSION['aDiastrab'][$obj->rowid]['basic']      = $obj->basic;
		      // $_SESSION['aDiastrab'][$obj->rowid]['date_ini']   = $obj->date_ini;
		      // $_SESSION['aDiastrab'][$obj->rowid]['date_fin']   = $obj->date_fin;
		      // $_SESSION['aDiastrab'][$obj->rowid]['date_ini_p'] = $objectpe->date_ini;
		      // $_SESSION['aDiastrab'][$obj->rowid]['date_fin_p'] = $objectpe->date_fin;
		      // $_SESSION['aDiastrab'][$obj->rowid]['nDiasTrab'] += $nDiasTrab;
		      // $_SESSION['aDiastrab'][$obj->rowid]['nTotalDias']+= $nTotalDias;
		      // $_SESSION['aDiastrab'][$obj->rowid]['nTotalAnio']+= $nTotalAnio;
						$aDiastrab[$obj->rowid]['id']         = $obj->rowid;
						$aDiastrab[$obj->rowid]['basic']      = $obj->basic;
						$aDiastrab[$obj->rowid]['date_ini']   = $obj->date_ini;
						$aDiastrab[$obj->rowid]['date_fin']   = $obj->date_fin;
						$aDiastrab[$obj->rowid]['date_ini_p'] = $objectpe->date_ini;
						$aDiastrab[$obj->rowid]['date_fin_p'] = $objectpe->date_fin;
						$aDiastrab[$obj->rowid]['nDiasTrab'] += $nDiasTrab;
						$aDiastrab[$obj->rowid]['nTotalDias']+= $nTotalDias;
						$aDiastrab[$obj->rowid]['nTotalAnio']+= $nTotalAnio;
					}
				}
				$i++;
			}
		}
	}
  // echo '<pre>';
  // print_r($aDiastrab);
  // echo '</pre>';
  // exit;
  //  $nValue = $_SESSION['aDiastrab'][$id][$op];
	$nValue = $aDiastrab[$id][$op];
	return $nValue;
}

//nueva formula de dias traba
//funcion para calcular los dias laborales del empleado $id
//$op='nDiasTrab';
//$op='nTotalDias';
//$op='nTotalAnio';
//$op='bonus_old';
function formula_S_diaslab($id=0,$op='nDiasTrab', $nmonth=0)
{
	global $db,$conf,$langs,$objectsh,$objectsp,$object,$objectpe,$objectC,$user,$fk_period,$fk_proces,$fk_type_fol;


	require_once DOL_DOCUMENT_ROOT.'/salary/class/pperiodext.class.php';
	$objPeriod = new Pperiodext($db);
	//armamos fechas de inicio y fin
	$anio = $objectpe->anio;
	$dateiniges = dol_mktime(0,0,0,1,1,$objectpe->anio);
	$datefinges = dol_mktime(23,59,59,12,31,$objectpe->anio);
	$aDayini = dol_getdate($dateiniges);
	$aDayfin = dol_getdate($datefinges);
	$nAverage = 3;
	$nMonthlast = 11;
	$sql = "SELECT p.rowid, p.lastname AS lastname, p.firstname AS firstname, ";
	$sql.= " pu.lastnametwo AS lastnametwo, ";
	$sql.= " pt.date_ini, pt.date_fin, pt.basic, pt.bonus_old ";
	$sql.= " FROM ".MAIN_DB_PREFIX."adherent AS p ";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."p_user AS pu ON p.rowid = pu.fk_user ";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_contract AS pt ON pt.fk_user = p.rowid ";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_charge AS pc ON pc.rowid = pt.fk_charge ";
	$sql.= " WHERE p.entity = ".$conf->entity;
	$sql.= " AND pt.state = 1 ";
	if ($id) $sql.= " AND p.rowid = ".$id;
	//$sql.= " AND pt.fk_proces =".$fk_proces;
	$sql.= " ORDER BY p.lastname, pu.lastnametwo, p.firstname, pt.date_ini ";
	$resql=$db->query($sql);
	$lRet = false;
	//unset($_SESSION['aPlanilla']);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$lRet = true;
				$obj = $db->fetch_object($resql);
				//verificamos datos de si el empleado esta activo
				$lReg = True;
				$obj->date_ini = $db->jdate($obj->date_ini);
				$obj->date_fin = $db->jdate($obj->date_fin);
				//echo '<hr>'.$obj->date_ini.' '.$obj->date_fin.' '.$objectpe->date_fin;
				if ((empty($obj->data_fin) || is_null($obj->date_fin)) || (!empty($obj->date_fin) && $obj->date_fin > $objectpe->date_fin))
				{
					dol_print_date($obj->date_fin) .' <> '.dol_print_date($objectpe->date_fin);
					if ($obj->date_ini <= $objectpe->date_ini || ($obj->date_ini > $objectpe->date_ini && $obj->date_ini <= $objectpe->date_fin) )
					{
						//calculamos dias trabajados para aguinaldo
						$nDaysum=0;
						if ($obj->date_ini >= $dateiniges)
						{
							//obtenemos las tres ultimas papeletas pagadas

							$aDatetmp = dol_getdate($obj->date_ini);
							if ($aDatetmp['mday'] == 1 && $aDatetmp['mon'] == 10 && $aDatetmp['year'] == $anio)
								$nAverage = 2;
							if (empty($obj->date_fin) || is_null($obj->date_fin))
							{
								$nMonthlast = $aDayfin['mon']-1;

								if ($aDatetmp['mday'] == $aDayini['mday'] && $aDatetmp['mon'] == $aDayini['mon'])
									$nDaysum = 360;
								else
								{
									//ultimo dia del mes de inicio
									$aDatefintmp = dol_getdate(dol_get_last_day($anio,$aDatetmp['mon']));
									$nDaysum = $aDatefintmp['mday']-$aDatetmp['mday'];
									$nMonthini = $aDatetmp['mon'];
									$nDaysum+=(12-$nMonthini)*30;
								}
							}
							else
							{
								if ($obj->date_fin >= $datefinges)
								{
									$nMonthlast = $aDayfin['mon']-1;
									if ($aDatetmp['mday'] == $aDayini['mday'] && $aDatetmp['mon'] == $aDayini['mon']) $nDaysum = 360;
									else
									{
										//ultimo dia del mes de inicio
										$aDatefintmp = dol_getdate(dol_get_last_day($anio,$aDatetmp['mon']));
										$nDaysum = $aDatefintmp['mday']-$aDatetmp['mday'];
										$nMonthini = $aDatetmp['mon'];
										$nDaysum+=(12-$nMonthini)*30;
									}
								}
								else
								{
									//calcula los ultimos dias trabajados en el mes del despido
									$aDatefintmp = dol_getdate($obj->date_fin);
									$nMonthlast = $aDatefintmp['mon']-1;
									$nDaysum = $aDatefintmp['mday'];

									if ($aDatetmp['mday'] == $aDayini['mday'] && $aDatetmp['mon'] == $aDayini['mon'])
									{
										$nDaysum+= ($aDatefintmp['mon']-$aDatetmp['mon']-1)*30;
									}
									else
									{
										//ultimo dia del mes de inicio
										$aDateinitmp = dol_getdate(dol_get_last_day($anio,$aDatetmp['mon']));
										$nDaysum+= $aDateinitmp['mday']-$aDatetmp['mday'];

										$nMonthini = $aDatetmp['mon'];
										$nDaysum+=($aDatefintmp['mon']-$nMonthini-1)*30;
									}
								}
							}
						}
						else
						{
							$nDaysum = 360;

							if (empty($obj->date_fin) || is_null($obj->date_fin))
							{
								$nMonthlast = $aDayfin['mon'];
							}
							else
							{
								if ($obj->date_fin >= $datefinges)
								{
									$nMonthlast = $aDayfin['mon'];
								}
								else
								{
									//calcula los ultimos dias trabajados en el mes del despido
									$aDatefintmp = dol_getdate($obj->date_fin);
									$nMonthlast = $aDatefintmp['mon'];
								}
							}
						}
						//
						//verificamos cuantos sueldos recupera
						$nCopyaverage = 1;
						$nSumsalary = 0;
						//obtenemos las tres ultimas papeletas

						for ($a = $nMonthlast; $a >=1; $a--)
						{
							if ($nCopyaverage <= $nAverage)
							{
								//$filterper = " AND t.fk_user = ".$id;
								$filterper = " AND t.fk_type_fol = ".$conf->global->SALARY_FKTYPEFOL_FILTER;
								$filterper.= " AND t.mes = ".$a;
								$filterper.= " AND t.anio = ".$anio;
								$filterper.= " AND t.fk_proces = ".$fk_proces;

								$filterper.= " AND t.entity = ".$conf->entity;
								$resp = $objPeriod->fetchAll('','',0,0,array(),'AND',$filterper,true);
								$fk_period_filter = 0;
								if ($resp==1) $fk_period_filter = $objPeriod->id;
								$fk_concept_filter = $conf->global->SALARY_FKCONCEPT_FILTER;
								$fk_type_fol_filter = $conf->global->SALARY_FKTYPEFOL_FILTER;
								$filterval = " AND t.fk_user = ".$id;
								$filterval.= " AND t.fk_concept = ".$fk_concept_filter;
								$filterval.= " AND t.fk_period = ".$fk_period_filter;
								$filterval.= " AND t.fk_type_fol = ".$fk_type_fol_filter;

								$ressh = $objectsh->fetchAll('DESC','t.date_reg',0,0,array(),'AND',$filterval,true);
								if ($ressh>0)
								{
									$aSalaryconcept[$a] = $objectsh->amount;
									$nSumsalary+= $objectsh->amount;
									if($id == 36) echo ' sumsalary '.$nSumsalary;
								}
								$nCopyaverage++;
							}
						}
						//con el resultado dividimos la sumatoria entre nAverage
						$average = ($nSumsalary / $nAverage)*1;
						//calculamos los dias trabajados
						if ($obj->date_ini <= $objectpe->date_ini)
						{
							if (empty($obj->date_fin))
							{
								$nDiasTrab = $conf->global->SALARY_NRO_DIAS_LABORAL;
								$nTotalDias = $conf->global->SALARY_NRO_DIAS_LABORAL;
								$nTotalDias = totaldiastrab($obj->date_ini,$objectpe->date_fin);
							}
							else
							{
								if ($obj->date_fin <= $objectpe->date_fin)
								{
									$dFin=dol_print_date($obj->date_fin,'%Y%m%d');
									$dIni=dol_print_date($objectpe->date_ini,'%Y%m%d');
									$nDiasTrab = $dFin-$dIni + 1;
									$nDiasTrab = num_between_day($objectpe->date_ini, $obj->date_fin, 1);
									if ($nDiasTrab <0 || empty($nDiasTrab)) echo '<hr>errid '.$id.' iniper '.dol_print_date($objectpe->date_ini,'day').' fincontr '.dol_print_date($obj->date_fin,'day');
									$aDateend = dol_getdate($obj->date_fin);
									if ($aDateend['mday']>30) $nDiasTrab--;
									$nTotalDias = totaldiastrab($obj->date_ini,$obj->date_fin);
								}
								else
								{
									//se calcula por el total de dias
									$nDiasTrab = $conf->global->SALARY_NRO_DIAS_LABORAL;
									$nTotalDias = $conf->global->SALARY_NRO_DIAS_LABORAL;
									$nTotalDias = totaldiastrab($obj->date_ini,$objectpe->date_fin);
								}
							}
						}
						elseif ($obj->date_ini > $objectpe->date_ini &&
							$obj->date_ini <= $objectpe->date_fin)
						{
							if (empty($obj->date_fin))
							{
								$aDate = dol_getdate($objectpe->date_fin);
								if ($aDate['mon'] == 2)
								{
									$dini = date('d',$obj->date_ini);
									$aDateini = dol_getdate($obj->date_ini);
									$dini = $aDateini['mday'];
									$dfin = $_SESSION['param']['nDiasTrab'];
									$nDiasTrab = $dfin-$dini+1;
									$nTotalDias = totaldiastrab($obj->date_ini,$objectpe->date_fin,1);
								}
								else
								{
				  					//restamos los dias trabajaods
									$dFin=dol_print_date($objectpe->date_fin,'%Y%m%d');
									$dIni=dol_print_date($obj->date_ini,'%Y%m%d');
									$nDiasTrab = $dFin-$dIni+1;
									$nDiasTrab = num_between_day($obj->date_ini, $objectpe->date_fin, 1);
									$aDateend = dol_getdate($objectpe->date_fin);
									if ($aDateend['mday']>30) $nDiasTrab--;
									$nTotalDias = totaldiastrab($obj->date_ini,$objectpe->date_fin);
								}
							}
							else
							{
								if ($obj->date_fin <= $objectpe->date_fin)
								{
									$dFin=dol_print_date($obj->date_fin,'%Y%m%d');
									$dIni=dol_print_date($obj->date_ini,'%Y%m%d');
									$nDiasTrab = $dFin-$dIni + 1;
									$nDiasTrab = num_between_day($obj->date_ini, $obj->date_fin, 1);
									$aDateend = dol_getdate($obj->date_fin);
									if ($aDateend['mday']>30) $nDiasTrab--;
									$nTotalDias = totaldiastrab($obj->date_ini,$obj->date_fin);
								}
								else
								{
				  					//echo '<br>la fecha final es mayor a la fecha del periodo';
									$dFin=dol_print_date($objectpe->date_fin,'%Y%m%d');
									$dIni=dol_print_date($obj->date_ini,'%Y%m%d');
									$nDiasTrab = $dFin-$dIni + 1;
									$nDiasTrab = num_between_day($obj->date_ini, $objectpe->date_fin, 1);
									$aDateend = dol_getdate($objectpe->date_fin);
									if ($aDateend['mday']>30) $nDiasTrab--;

									$nTotalDias = totaldiastrab($obj->date_ini,$obj->date_fin);
								}
							}
						}
						//agregamos el calculo del cas
						require_once DOL_DOCUMENT_ROOT.'/assistance/class/membercas.class.php';
						$objMembercas = new Membercas($db);
						//verificamos si existe registro del cass
						$filtercas = " AND t.fk_member = ".$id." AND t.status = 1";
						$rescas = $objMembercas->fetchAll('','',0,0,array(1=>1),'AND',$filtercas,true);
						$nTotalDiascas = 0;
						if ($rescas==1)
						{
							$nTotalDiascas+=$objMembercas->number_year*360;
							$nTotalDiascas+=$objMembercas->number_month*30;
							$nTotalDiascas+=$objMembercas->number_day;
							$nTotalAnio = ($nTotalDiascas+$nTotalDias)/360;
						}
						else
							$nTotalAnio = $nTotalDias / 360;

						$aDiastrab[$obj->rowid]['id']         = $obj->rowid;
						$aDiastrab[$obj->rowid]['basic']      = $obj->basic;
						$aDiastrab[$obj->rowid]['date_ini']   = $obj->date_ini;
						$aDiastrab[$obj->rowid]['date_fin']   = $obj->date_fin;
						$aDiastrab[$obj->rowid]['date_ini_p'] = $objectpe->date_ini;
						$aDiastrab[$obj->rowid]['date_fin_p'] = $objectpe->date_fin;
						$aDiastrab[$obj->rowid]['nDiasTrab'] += $nDiasTrab;
						$aDiastrab[$obj->rowid]['nTotalDias']+= $nTotalDias;
						$aDiastrab[$obj->rowid]['nTotalAnio']+= $nTotalAnio;
						$aDiastrab[$obj->rowid]['nTotalDiasAgui'] = ($nDaysum>=90?($nDaysum>360?360:$nDaysum):0);
						$aDiastrab[$obj->rowid]['aSalaryconcept'] = $aSalaryconcept;
						$aDiastrab[$obj->rowid]['average'] = $average;
						//if ($id == 36) echo ' prom '. $average;
					}
				}
				$i++;
			}
		}
	}

	//  $nValue = $_SESSION['aDiastrab'][$id][$op];
	return $aDiastrab[$id][$op];
	//return $nValue;
}

?>