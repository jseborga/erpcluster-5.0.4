<?php
//procedimiento de calculo unitario
function procedure_calc($id,$idr,$rep=false)
{
	global $db,$conf,$langs;
	global $objectdet,$objectdetadd,$objectbtr;
	global $object,$objstr,$objstrdet;
	require_once DOL_DOCUMENT_ROOT.'/budget/class/puformulasdetext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/productbudgetext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/budgetgeneral.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/budgetconcept.class.php';
	require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
	
	$objformdet = new Puformulasdetext($db);
	$objop		= new Puoperatorext($db);
	$productbtr = new Productbudgetext($db);
	$general    = new Budgetgeneral($db);
	$concept 	= new Budgetconcept($db);
	$categorie  = new Categorie($db);
	$html = '';
	$aSt = array();
	$_SESSION['sumaitem'][$idr]=0;
	//recuperamos el item
	$objectdet->fetch($idr);
	$objectdetadd->fetch(0,$idr);
	$taskcomple = $objectdetadd->complementary;
	//recuperamos parametros generales
	$general->fetch(0,$id);
	$filterstatic = " AND t.fk_budget_task = ".$idr;
	$objectbtr->fetchAll('ASC', 't.code_structure',0,0,array(1=>1),'AND',$filterstatic);

	//recuperamos la estrcutura
	$_SESSION['cItem'][$idr] = array();
	$filterstatic = " AND t.type_structure = '".$object->type_structure."'";
	$objstr->fetchAll('ASC', 'ordby', 0, 0, array(1=>1), 'AND',$filterstatic);

	$html.= '<tr>';
	$html.= '<td>'.$langs->trans('Description').'</td>';
	$html.= '<td>'.$langs->trans('Unit').'</td>';
	$html.= '<td>'.$langs->trans('Quant').'</td>';
	$html.= '<td>'.$langs->trans('% Prod.').'</td>';
	$html.= '<td>'.$langs->trans('Amountnoprod').'</td>';
	$html.= '<td>'.$langs->trans('P.U.').'</td>';
	$html.= '<td>'.$langs->trans('Total').'</td>';
	$html.= '</tr>';
	$order = 1;
	foreach ((array) $objstr->lines AS $h => $lineh)
	{
		$strcomple = $lineh->complementary;
		$lPrint = true;
		if ($taskcomple && !$strcomple) $lPrint = false;
		if ($lPrint)
		{
			$html.= '<tr>';
			$html.= '<td colspan="5">'.$order.'. '.$lineh->detail.'</td>';
			$html.= '</tr>';
			//recuperamos el struct_det
			//$filterstatic = " AND t.fk_pu_structure = ".$lineh->id;
			$filterstatic = " AND t.entity = ".$lineh->entity;
			$filterstatic.= " AND t.ref_structure = '".$lineh->ref."'";
			$filterstatic.= " AND t.type_structure = '".$lineh->type_structure."'";
			$res1 = $objstrdet->fetchAll('ASC', 'sequen', 0, 0, array(1=>1), 'AND',$filterstatic);
			foreach ((array) $objstrdet->lines AS $i => $linei)
			{
				//procesamos el calculo de la formula
				$filterstatic = " AND t.ref_formula = '".$linei->formula."'";
				$filterstatic.= " AND t.status = 1";
				$resfd = $objformdet->fetchAll('ASC', 'sequen', 0,0,array(1=>1),'AND', $filterstatic);
				$resop = 0;
				foreach((array) $objformdet->lines AS $j => $linej)
				{
					//buscamos el operator
					$type = $linej->type;

					$objop->fetch($linej->fk_operator);
					if ($linej->type != 'valor')
					{
						$aChange = explode('|',$linej->changefull);
					}
					else
						$value = $linej->changefull;
					//segun el operador
					if ($objop->operator == 'sum()')
					{
						//$resop = 0;
						//utilizamos $aChange
						if ($type == 'pu_structure')
						{
							$fk = $aChange[0];
							$code = $aChange[1];
							//procedemos a la suma de la tabla
							foreach((array) $objectbtr->lines AS $k => $linek)
							{
								if ($linek->code_structure == $code)
								{
									$html.= '<tr>';
									$html.= '<td>'.$linek->detail.'</td>';
									$productbtr->fetch($linek->fk_product_budget);
									if ($productbtr->id == $linek->fk_product_budget)
										$html.= '<td>'.$productbtr->getLabelOfUnit('short').'</td>';
									else
										$html.= '<td>nn</td>';
									$nprod = price2num(($linek->quant*$linek->percent_prod*$linek->amount/100),$general->decimal_total);
									$nnprod = price2num(($linek->quant*(100-$linek->percent_prod)*$linek->amount_noprod/100),$general->decimal_total);
									$ntotal = $nprod + $nnprod;
									$html.= '<td align="right">'.number_format($linek->quant,$general->decimal_quant).'</td>';
									$html.= '<td align="right">'.price($linek->percent_prod).' %</td>';
									$html.= '<td align="right">'.number_format($linek->amount_noprod,$general->decimal_pu).'</td>';
									$html.= '<td align="right">'.number_format($linek->amount,$general->decimal_pu).'</td>';
									$html.= '<td align="right">'.number_format($ntotal,$general->decimal_total).'</td>';
									$html.= '</tr>';
									//$aSt[$lineh->ref][$linei->formula]+=$ntotal;
									//$linek->code_structure.' '.$code.' '.$linek->quant;
									$_SESSION['cItem'][$idr][$linei->formula]+= $ntotal;
									$resop += $ntotal;
									$linei->formula.' = '.$_SESSION['cItem'][$idr][$linei->formula];
								}
							}
						}
						if ($type == 'pu_formulas')
						{
							$fk = $aChange[0];
							$code = $aChange[1];
							$_SESSION['cItem'][$idr][$linei->formula]+= $_SESSION['cItem'][$idr][$code];
							$linei->formula.' = '.$_SESSION['cItem'][$idr][$linei->formula];
							$resop+= $_SESSION['cItem'][$idr][$code];
						}
						//return $_SESSION['cItem'][$idr][$linei->formula];
					}
					else
					{
						if ($objop->operator == '+')
						{
							if ($type == 'pu_formulas')
							{
								$fk = $aChange[0];
								$code = $aChange[1]; 
								$result = $_SESSION['cItem'][$idr][$code];
								$resop += $result;
							}
							if ($type == 'parameter_calculation')
							{
								$fk = $aChange[0];
								$code = $aChange[1];
								//buscamos en la tabla budget_concept 
								$concept->fetch(0,$code,$id);
								if ($concept->ref == $code && $concept->fk_budget == $id)
									$resop += $concept->amount;
								else
									setEventMessages($concept->error,$concept->errors,'errors');
							}
							if ($type == 'valor') $resop += $value;
						}
						if ($objop->operator == '-')
						{
							if ($type == 'pu_formulas')
							{
								$fk = $aChange[0];
								$code = $aChange[1]; 
								$result = $_SESSION['cItem'][$idr][$code];
								$resop -= $result;
							}
							if ($type == 'parameter_calculation')
							{
								$fk = $aChange[0];
								$code = $aChange[1];
								//buscamos en la tabla budget_concept 
								$concept->fetch(0,$code,$id);
								if ($concept->ref == $code && $concept->fk_budget == $id)
									$resop -= $concept->amount;
								else
									setEventMessages($concept->error,$concept->errors,'errors');
							}
							if ($type == 'valor') $resop -= $value;
						}
						if ($objop->operator == '*')
						{
							if ($type == 'pu_formulas')
							{
								$fk = $aChange[0];
								$code = $aChange[1]; 
								$result = $_SESSION['cItem'][$idr][$code];
								$resop = price2num($resop * $result,$general->decimal_total);
							}
							if ($type == 'parameter_calculation')
							{
								$fk = $aChange[0];
								$code = $aChange[1];
								//buscamos en la tabla budget_concept 
								$concept->fetch(0,$code,$id);
								if ($concept->ref == $code && $concept->fk_budget == $id)
									$resop = price2num($resop * $concept->amount,$general->decimal_total);
								else
									setEventMessages($concept->error,$concept->errors,'errors');
							}
							if ($type == 'valor') $resop = price2num($resop * $value,$general->decimal_total);
						}
						if ($objop->operator == '/')
						{
							if ($type == 'pu_formulas')
							{
								$fk = $aChange[0];
								$code = $aChange[1]; 
								$result = $_SESSION['cItem'][$idr][$code];
								$resop = price2num($resop / $result,$general->decimal_total);
							}
							if ($type == 'parameter_calculation')
							{
								$fk = $aChange[0];
								$code = $aChange[1];
								//buscamos en la tabla budget_concept 
								$concept->fetch(0,$code,$id);
								if ($concept->ref == $code && $concept->fk_budget == $id)
									$resop = price2num($resop / $concept->amount,$general->decimal_total);
								else
									setEventMessages($concept->error,$concept->errors,'errors');
							}
							if ($type == 'valor') $resop = price2num($resop / $value,$general->decimal_total);
						}
					}
				}
				$_SESSION['cItem'][$idr][$linei->formula] = $resop;
				$aSt[$lineh->ref][$linei->formula]+=price2num($resop,$general->decimal_total);
				if ($linei->status_print)
				{
					$html.= '<tr class="total">';
					$html.= '<td>'.$linei->detail.'</td>';
					$html.= '<td colspan="4">&nbsp;</td>';
					$html.= '<td></td>';
					$html.= '<td align="right">'.number_format(price2num($resop,$decimal_total),$general->decimal_total).'</td>';
					$html.= '</tr>';
				}
			}
			
			//imprimimos el total
			//sumamos todo lo que hay en aSt
			$sumatot = 0;
			foreach((array) $aSt[$lineh->ref] AS $l => $sumapar)
			{
				$sumatot+= $sumapar;
			}
			$html.= '<tr class="total">';
			$html.= '<td colspan="5">&nbsp;</td>';
			$html.= '<td>'.$langs->trans('Total').' '.$lineh->detail.'</td>';
			$html.= '<td align="right">'.number_format($sumatot,$general->decimal_total).'</td>';
			$html.= '</tr>';
			$order++;
		}
	}
	//almacenamos en el item
	$aData = $_SESSION['cItem'][$idr];
	$suma = 0;
	//echo '<pre>';
	//print_r($_SESSION['cItem'][$idr]);
	//echo '</pre>';
	//exit;
	foreach ((array) $aData as $i => $value)
		$suma += $value;
	$_SESSION['sumaitem'][$idr]=price2num($suma,$general->decimal_total);
	$html.= '<tr class="total">';
	$html.= '<td colspan="5">&nbsp;</td>';
	$html.= '<td>'.$langs->trans('Total Precio Unitario').'</td>';
	$html.= '<td align="right">'.number_format($suma,$general->decimal_total).'</td>';
	$html.= '</tr>';
	if ($rep)
		return array($html,price2num($suma,$general->decimal_total));
	else
		return price2num($suma,$general->decimal_total);
}

//retorna la sumatoria
function procedure_calcul__o($id,$idr,$ret=false)
{
	global $db,$conf,$langs;
	global $object,$objstr,$objstrdet;
	require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettaskext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettaskaddext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettaskresource.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/pustructureext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/pustructuredetext.class.php';

	require_once DOL_DOCUMENT_ROOT.'/budget/class/puformulasdetext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/productbudgetext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/budget/class/budgetgeneral.class.php';
	require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
	
	$objformdet = new Puformulasdetext($db);
	$objop		= new Puoperatorext($db);
	$productbtr = new Productbudgetext($db);
	$general_   = new Budgetgeneral($db);
	$categorie  = new Categorie($db);

	$objectdet_ = new Budgettaskext($db);
	$objectdetadd_ = new Budgettaskaddext($db);
	$objectbtr_ = new Budgettaskresource($db);
	$objstr_	= new Pustructureext($db);
	$objstrdet_	= new Pustructuredetext($db);
	$html = '';
	$aSt = array();
	$_SESSION['sumaitem'][$idr]=0;
	//recuperamos el item
	$objectdet_->fetch($idr);
	$objectdetadd_->fetch(0,$idr);
	$taskcomple = $objectdetadd->complementary;
	//recuperamos parametros generales
	$general_->fetch(0,$id);
	$filterstatic = " AND t.fk_budget_task = ".$idr;
	$objectbtr_->fetchAll('ASC', 't.code_structure',0,0,array(1=>1),'AND',$filterstatic);

	//recuperamos la estrcutura
	$_SESSION['cItem'][$idr] = array();
	$filterstatic = " AND t.type_structure = '".$object->type_structure."'";
	$objstr_->fetchAll('ASC', 'ordby', 0, 0, array(1=>1), 'AND',$filterstatic);

	/*
	$html.= '<tr>';
	$html.= '<td>'.$langs->trans('Description').'</td>';
	$html.= '<td>'.$langs->trans('Unit').'</td>';
	$html.= '<td>'.$langs->trans('Quant').'</td>';
	$html.= '<td>'.$langs->trans('% Prod.').'</td>';
	$html.= '<td>'.$langs->trans('Amountnoprod').'</td>';
	$html.= '<td>'.$langs->trans('P.U.').'</td>';
	$html.= '<td>'.$langs->trans('Total').'</td>';
	$html.= '</tr>';
	*/
	$order = 1;
	foreach ((array) $objstr_->lines AS $h => $lineh)
	{
		$strcomple = $lineh->complementary;
		$lPrint = true;
		if ($taskcomple && !$strcomple) $lPrint = false;
		if ($lPrint)
		{

			//$html.= '<tr>';
			//$html.= '<td colspan="5">'.$order.'. '.$lineh->detail.'</td>';
			//$html.= '</tr>';
			//recuperamos el struct_det
			//$filterstatic = " AND t.fk_pu_structure = ".$lineh->id;
			$filterstatic = " AND t.entity = ".$lineh->entity;
			$filterstatic.= " AND t.ref_structure = '".$lineh->ref."'";
			$filterstatic.= " AND t.type_structure = '".$lineh->type_structure."'";
			$res1 = $objstrdet_->fetchAll('ASC', 'sequen', 0, 0, array(1=>1), 'AND',$filterstatic);
			foreach ((array) $objstrdet_->lines AS $i => $linei)
			{
				//procesamos el calculo de la formula
				$filterstatic = " AND t.ref_formula = '".$linei->formula."'";
				$filterstatic.= " AND t.status = 1";
				$resfd = $objformdet->fetchAll('ASC', 'sequen', 0,0,array(1=>1),'AND', $filterstatic);
				$resop = 0;
				foreach((array) $objformdet->lines AS $j => $linej)
				{
					//buscamos el operator
					$type = $linej->type;

					$objop->fetch($linej->fk_operator);
					if ($linej->type != 'valor')
					{
						$aChange = explode('|',$linej->changefull);
					}
					else
						$value = $linej->changefull;
					//segun el operador
					if ($objop->operator == 'sum()')
					{
						//$resop = 0;
						//utilizamos $aChange
						if ($type == 'pu_structure')
						{
							$fk = $aChange[0];
							$code = $aChange[1];
							//procedemos a la suma de la tabla
							foreach((array) $objectbtr_->lines AS $k => $linek)
							{
								$linek->code_structure.' == '.$code;
								if ($linek->code_structure == $code)
								{
									//$html.= '<tr>';
									//$html.= '<td>'.$linek->detail.'</td>';
									$productbtr->fetch($linek->fk_product_budget);
									//if ($productbtr->id == $linek->fk_product_budget)
									//	$html.= '<td>'.$productbtr->getLabelOfUnit('short').'</td>';
									//else
									//	$html.= '<td>nn</td>';
									$nprod = price2num(($linek->quant*$linek->percent_prod*$linek->amount/100),$general_->decimal_total);
									$nnprod = price2num(($linek->quant*(100-$linek->percent_prod)*$linek->amount_noprod/100),$general_->decimal_total);
									$ntotal = $nprod + $nnprod;
									//$html.= '<td align="right">'.number_format($linek->quant,$general_->decimal_quant).'</td>';
									//$html.= '<td align="right">'.price($linek->percent_prod).' %</td>';
									//$html.= '<td align="right">'.number_format($linek->amount_noprod,$general_->decimal_pu).'</td>';
									//$html.= '<td align="right">'.number_format($linek->amount,$general_->decimal_pu).'</td>';
									//$html.= '<td align="right">'.number_format($ntotal,$general_->decimal_total).'</td>';
									//$html.= '</tr>';
									//$aSt[$lineh->ref][$linei->formula]+=$ntotal;
									//$linek->code_structure.' '.$code.' '.$linek->quant;
									$_SESSION['cItem'][$idr][$linei->formula]+= $ntotal;
									$resop += $ntotal;
									$linei->formula.' = '.$_SESSION['cItem'][$idr][$linei->formula];
								}
							}
						}
						if ($type == 'pu_formulas')
						{
							$fk = $aChange[0];
							$code = $aChange[1];
							$_SESSION['cItem'][$idr][$linei->formula]+= $_SESSION['cItem'][$idr][$code];
							$linei->formula.' = '.$_SESSION['cItem'][$idr][$linei->formula];
							$resop+= $_SESSION['cItem'][$idr][$code];
						}
						//return $_SESSION['cItem'][$idr][$linei->formula];
					}
					else
					{
						if ($objop->operator == '+')
						{
							if ($type == 'pu_formulas')
							{
								$fk = $aChange[0];
								$code = $aChange[1]; 
								$result = $_SESSION['cItem'][$idr][$code];
								$resop += $result;
							}
							if ($type == 'valor') $resop += $value;
						}
						if ($objop->operator == '-')
						{
							if ($type == 'pu_formulas')
							{
								$fk = $aChange[0];
								$code = $aChange[1]; 
								$result = $_SESSION['cItem'][$idr][$code];
								$resop -= $result;
							}
							if ($type == 'valor') $resop -= $value;
						}
						if ($objop->operator == '*')
						{
							if ($type == 'pu_formulas')
							{
								$fk = $aChange[0];
								$code = $aChange[1]; 
								$result = $_SESSION['cItem'][$idr][$code];
								$resop = price2num($resop * $result,$general_->decimal_total);
							}
							if ($type == 'valor') $resop = price2num($resop * $value,$general_->decimal_total);
						}
						if ($objop->operator == '/')
						{
							if ($type == 'pu_formulas')
							{
								$fk = $aChange[0];
								$code = $aChange[1]; 
								$result = $_SESSION['cItem'][$idr][$code];
								$resop = price2num($resop / $result,$general_->decimal_total);
							}
							if ($type == 'valor') $resop = price2num($resop / $value,$general_->decimal_total);
						}
					}
				}
				$_SESSION['cItem'][$idr][$linei->formula] = $resop;
				$aSt[$lineh->ref][$linei->formula]+=price2num($resop,$general_->decimal_total);
				if ($linei->status_print)
				{
					//$html.= '<tr class="total">';
					//$html.= '<td>'.$linei->detail.'</td>';
					//$html.= '<td colspan="4">&nbsp;</td>';
					//$html.= '<td></td>';
					//$html.= '<td align="right">'.number_format(price2num($resop,$decimal_total),$general_->decimal_total).'</td>';
					//$html.= '</tr>';
				}
			}
			
			//imprimimos el total
			//sumamos todo lo que hay en aSt
			$sumatot = 0;
			foreach((array) $aSt[$lineh->ref] AS $l => $sumapar)
			{
				$sumatot+= $sumapar;
			}
			//$html.= '<tr class="total">';
			//$html.= '<td colspan="5">&nbsp;</td>';
			//$html.= '<td>'.$langs->trans('Total').' '.$lineh->detail.'</td>';
			//$html.= '<td align="right">'.number_format($sumatot,$general_->decimal_total).'</td>';
			//$html.= '</tr>';
			$order++;
		}
	}
	//almacenamos en el item
	$aData = $_SESSION['cItem'][$idr];
	$suma = 0;
	echo '<pre>';
	print_r($_SESSION['cItem'][$idr]);
	echo '</pre>';
	exit;
	foreach ((array) $aData as $i => $value)
		$suma += $value;
	$_SESSION['sumaitem'][$idr]=price2num($suma,$general_->decimal_total);
	//$html.= '<tr class="total">';
	//$html.= '<td colspan="5">&nbsp;</td>';
	//$html.= '<td>'.$langs->trans('Total Precio Unitario').'</td>';
	//$html.= '<td align="right">'.number_format($suma,$general_->decimal_total).'</td>';
	//$html.= '</tr>';
	if ($rep)
		return array($html,price2num($suma,$general_->decimal_total));
	else
		return price2num($suma,$general_->decimal_total);
}

?>