<?php
require_once DOL_DOCUMENT_ROOT.'/budget/class/itemsproduct.class.php';

class Itemsproductext extends Itemsproduct

{
	var $aSpread;
	var $aSpreadformula;
	var $aSpreadformulapartial;
	var $aCel;
	var $aSpreadf;
	var $aFormuladet;

	//procedimiento de calculo unitario
	function procedure_calc($idr=0,$type_structure, $fk_region,$fk_sector,$rep=false)
	{
		global $conf,$langs;
		//global $objectdetadd;
		global $object;
		require_once DOL_DOCUMENT_ROOT.'/budget/class/puformulasdetext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/productbudgetext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/budgetgeneral.class.php';
		//require_once DOL_DOCUMENT_ROOT.'/budget/class/budgetconcept.class.php'; //reemplaza
		require_once DOL_DOCUMENT_ROOT.'/budget/class/parametercalculation.class.php';

		require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';

		require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettaskext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettaskresourceext.class.php';

		require_once DOL_DOCUMENT_ROOT.'/budget/class/pustructureext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/pustructuredetext.class.php';

		require_once DOL_DOCUMENT_ROOT.'/budget/class/itemsext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/itemsproductext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/itemsproduction.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/itemsproductregion.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/puoperatorext.class.php';


		$objectbtr  = new Budgettaskresourceext($this->db);
		$objectdet  = new Budgettaskext($this->db);
		$objformdet = new Puformulasdetext($this->db);
		$objop      = new Puoperatorext($this->db);
		$productbtr = new Productbudgetext($this->db);
		//$general    = new Budgetgeneral($this->db);
		//$concept    = new Budgetconcept($this->db);
		$categorie  = new Categorie($this->db);
		$objPustructure = new Pustructureext($this->db);
		$objPustructuredet = new Pustructuredetext($this->db);
		//$objstr
		$objstrdet  = new Pustructuredetext($this->db);

		$objItems = new Itemsext($this->db);
		$objItemsproduct = new Itemsproductext($this->db);
		$objItemsproduction = new Itemsproduction($this->db);
		$objItemsproductregion = new Itemsproductregion($this->db);
		$objParametercalculation = new Parametercalculation($this->db);

		$html = '';
		$aLine = array();
		$aLinetot = array();
		$aSt = array();
		$_SESSION['sumaitem'][$idr]=0;
		$this->aSpread=array();
		$this->aCel = array();
		$n=0;

		$taskcomple = $this->complementary;

		//recuperamos parametros generales
		//$general->fetch(0,$id);
		$nDecimal = $conf->global->ITEMS_DEFAULT_NUMBER_DECIMAL;
		$nProductdecimal = $conf->global->ITEMS_DEFAULT_PRODUCT_NUMBER_DECIMAL;
		if (empty($nProductdecimal)) $nProductdecimal = 5;
		if (empty($nDecimal)) $nDecimal = 2;
		$base_currency = $conf->global->ITEMS_DEFAULT_BASE_CURRENCY;
		$second_currency = $conf->global->ITEMS_DEFAULT_SECOND_CURRENCY;
		$exchange_rate = $conf->global->ITEMS_DEFAULT_EXCHANGE_RATE;

		//recuperamos el item
		$filterstatic='';
		if ($idr>0)
		$filterstatic.= " AND t.fk_item = ".$idr;

		$resbtr = $this->fetchAll('ASC', 't.label',0,0,array(),'AND',$filterstatic);
		if ($resbtr>0) $linesbtr = $this->lines;
		else {
			return 0;
		}
		//recuperamos la estrcutura
		$_SESSION['cItem'][$idr] = array();
		$aSpreadformulapartial=array();
		$filter=" AND t.type_structure = '".$type_structure."'";
		$res = $objPustructure->fetchAll('ASC','ordby',0,0,array(),'AND',$filter);
		$aGroupCategorie=array();
		$aCategorieGroup=array();
		if ($res>0)
		{
			$linesstr = $objPustructure->lines;
			//vamos a recuperar la relación de grupo struc con categorie
			foreach ($linesstr AS $j => $line)
			{
				$aGroupCategorie[$line->group_structure] = $line->fk_categorie;
				$aCategorieGroup[$line->fk_categorie] = $line->group_structure;
			}
		}
		else
		{
			setEventMessages($langs->trans('Noexiststructure'),null,'warnings');
			return -1;
		}
		$this->viewhtml = '';
		$html.= '<tr>';
		$html.= '<td>'.$langs->trans('Description').'</td>';
		$html.= '<td>'.$langs->trans('Unit').'</td>';
		$html.= '<td>'.$langs->trans('Quant').'</td>';
		$html.= '<td>'.$langs->trans('% Prod.').'</td>';
		$html.= '<td>'.$langs->trans('Amountnoprod').'</td>';
		$html.= '<td>'.$langs->trans('P.U.').'</td>';
		$html.= '<td>'.$langs->trans('Total').'</td>';
		$html.= '</tr>';
		//$this->aSpread[$idr][$n]['title']=array(1=>$langs->trans('Description'),2=>$langs->trans('Unit'),3=>$langs->trans('Quant'),4=>$langs->trans('Porcproductivity'),5=>$langs->trans('Amountnoprod'),6=>$langs->trans('P.U.'),7=>$langs->trans('Total'));
		$n++;
		$order = 1;
		$aTmpsum=array();
		//echo '<hr>idr '.$idr;
		$aTotal = array();
		foreach ((array) $linesstr AS $h => $lineh)
		{
			$strcomple = $lineh->complementary;
			$lPrint = true;
			if ($taskcomple && !$strcomple) $lPrint = false;
			if ($lPrint)
			{
				$myclass = new stdClass();
				$myclass->label = $lineh->detail;
				$myclass->format = 'group';
				$this->aLine[] = $myclass;
				$html.= '<tr>';
				$html.= '<td colspan="5">'.$order.'. '.$lineh->detail.'</td>';

				$this->aSpread[$idr][$n]['datag'][1]=$order.'. '.$lineh->detail;
				$n++;
				$html.= '</tr>';
				//recuperamos el struct_det
				$filterstatic = " AND t.entity = ".$lineh->entity;
				$filterstatic.= " AND t.ref_structure = '".$lineh->ref."'";
				//echo '<hr>STR '.
				$filterstatic.= " AND t.type_structure = '".$lineh->type_structure."'";
				$res1 = $objPustructuredet->fetchAll('ASC', 'sequen', 0, 0, array(1=>1), 'AND',$filterstatic);

				if ($res1 > 0)
				{
					$formula = '';
					$aFormula=array();
					$linesi = $objPustructuredet->lines;
					foreach ((array) $linesi AS $i => $linei)
					{
						//procesamos el calculo de la formula
						//echo '<br>NNN '.$n;
						$filterstatic = " AND t.ref_formula = '".$linei->formula."'";
						//echo '<br>formula '.
						//echo '<hr>filter '.
						$filterstatic.= " AND t.status = 1";
						//echo ' resfd '.
						$resfd = $objformdet->fetchAll('ASC', 'sequen', 0,0,array(1=>1),'AND', $filterstatic);
						$resop = 0;
						if ($resfd>0)
						{
							$formuladet = '';
							$formuladetnew = array();
							//vamos a enviar el tipo de formula que se procesamos
							$operator = '';
							foreach((array) $objformdet->lines AS $j => $linej)
							{
								//buscamos el operator
								$type = $linej->type;
								//echo ' <br>empieza con formula '.$linej->ref_formula;
								$resope = $objop->fetch($linej->fk_operator);
								//echo ' operator '.$objop->operator;
								if ($linej->type != 'valor')
								{
									$aChange = explode('|',$linej->changefull);
									//$this->aFormuladet[$linej->ref_formula][$linej->fk_operator] = $aChange[1];
								}
								else
								{
									$value = $linej->changefull;
									//$this->aFormuladet[$linej->ref_formula][$linej->fk_operator] = $value;
								}

								//segun el operador
								$fk = $aChange[0];
								$code = $aChange[1];

								if ($objop->operator == 'sum()')
								{
									$formuladet.= '+SUM('.$lineh->group_structure.')';
									//$resop = 0;
									//utilizamos $aChange
									//para iniciar la columna
									if ($type == 'pu_structure')
									{
										//vamos a resumir para imprimir

										//procedemos a la suma de la tabla
										//echo '<hr>resbtr '.$resbtr;
										if ($resbtr>0)
										{
											$aContenido = array();
											$aContenidover = array();
											$linsum=1;
											foreach((array) $linesbtr AS $k => $linek)
											{
												//buscamos en el de region
												$resipr = $objItemsproductregion->fetch(0,$linek->id,$fk_region,$fk_sector);
												if ($resipr)
												{
													$linek->quant = $objItemsproductregion->performance;
													$linek->percent_prod = $objItemsproductregion->price_productive;
													$linek->amount_noprod = $objItemsproductregion->amount_noprod;
													$linek->amount = $objItemsproductregion->amount;
												}

												//echo ' <br>compara '.$lineh->fk_categorie .'=='. $code .'&&'. $linek->group_structure .'=='. $lineh->group_structure.') || '.$aCategorieGroup[$code].' == '.$linek->group_structure.' enlinea '.$n;
												if (($lineh->fk_categorie == $code && $linek->group_structure == $lineh->group_structure) || $aCategorieGroup[$code] == $linek->group_structure)
												{
													//echo '<hr>dentro '.
													$formuladetnew[$linej->ref_formula][$linej->id]= '+SUM('.$linek->group_structure.')';
													//echo '<br>esoperator '.
													$operator = 'sum';
													//echo  ' para '.$lineh->group_structure.' conlinea '.$n;

													$aContenidover[$linsum]=$linei->status_print;
													$aContenido[$linsum]['A']=$linek->label;
													if ($linek->fk_unit)
													{
														$productbtr->fk_unit = $linek->fk_unit;
														$aContenido[$linsum]['B']=$langs->trans($productbtr->getLabelOfUnit('short'));
													}
													else
													{
														$aContenido[$linsum]['B']='';
													}
													$nprod = price2num(($linek->quant*$linek->percent_prod*$linek->amount/100),$nDecimal);
													$nnprod = price2num(($linek->quant*(100-$linek->percent_prod)*$linek->amount_noprod/100),$nDecimal);
													$ntotal = $nprod + $nnprod;

													$aContenido[$linsum]['C']=price2num($linek->quant,$nProductdecimal);

													if($linek->group_structure == 'MQ')
													{
														$aContenido[$linsum]['D']=price2num($linek->percent_prod);
														$aContenido[$linsum]['E']=price2num($linek->amount_noprod,$conf->global->ITEMS_DEFAULT_NUMBER_DECIMAL);
													}
													else
													{
														$aContenido[$linsum]['D']='';
														$aContenido[$linsum]['E']='';
													}
													$aContenido[$linsum]['F']=price2num($linek->amount,$conf->global->ITEMS_DEFAULT_NUMBER_DECIMAL);
													$aContenido[$linsum]['G']=price2num($ntotal,$conf->global->ITEMS_DEFAULT_NUMBER_DECIMAL);
													$linsum++;
												}
											}

											//echo '<hr>luego de revisar ';
											//print_r($formuladetnew);
											if (is_array($formuladetnew) && count($formuladetnew)==0)
											{
												//echo 'secuple ';
												$formuladetnew[$linej->ref_formula][$linej->id]=0;
											}
												//echo '<hr>luego de validar ';
												//print_r($formuladetnew);
											if (empty($colini[$j]))
											$colini[$j]=$n;

											if (is_array($aContenido) && count($aContenido)>0)
											{
												$lAddtmp = false;
												if (empty($aTmpsum[$lineh->group_structure]))
												{
													$lAddtmp = true;
													//echo ' seagrega para '.$n;
												}
												//echo '<hr>CONTENDIDO ';
												//print_r($aContenido);
												//print_r($aContenidover);
												//listamos
												foreach ($aContenido AS $linsum => $aValue)
												{
													if ($aContenidover[$linsum])
													{
														//vamos a imprimir solo lo que esta permitido
														$myclass->unit = $aValue['B'];
														$myclass->unit_budget = $aValue['C'];
														$myclass->percent_prod = $aValue['D'];
														$myclass->amount_noprod = $aValue['E'];
														$myclass->unit_amount = $aValue['F'];
														//echo '<hr>aValueFG '.
														$myclass->total = $aValue['G'];
														if($lineh->group_structure == 'MQ')
														$this->aCel[$idr][$n]= '(C_'.$n.'_*D_'.$n.'_*F_'.$n.'_/100)+(C_'.$n.'_*(100-D_'.$n.'_)*E_'.$n.'_/100)';
														else
														$this->aCel[$idr][$n]= 'C_'.$n.'_*F_'.$n.'_';
														//echo ' <hr>N '.$n.' formlinea '.$this->aCel[$idr][$n];
														if ($lAddtmp) $aTmpsum[$lineh->group_structure][$n]=$n;
														if ($linei->status_print_det)
														{
															$html.= '<tr>';
															$html.= '<td>'.$aValue['A'].'</td>';
															$this->aSpread[$idr][$n]['data'][1]=$aValue['A'];
															$html.= '<td>'.$aValue['B'].'</td>';
															$this->aSpread[$idr][$n]['data'][2]=$aValue['B'];
															$html.= '<td align="right">'.number_format($aValue['C'],$nProductdecimal).'</td>';
															$this->aSpread[$idr][$n]['data'][3]=price2num($aValue['C'],$nProductdecimal);
															$html.= '<td align="right">'.$aValue['D'].' %</td>';
															if($linek->group_structure == 'MQ')
															$this->aSpread[$idr][$n]['data'][4]=$aValue['D'];
															else
															$this->aSpread[$idr][$n]['data'][4]='';
															$nVal=0;
															if (isset($aValue['E'])) $nVal = $aValue['E'];
															if ($nVal>0) $nVal = $nVal*1;
															else $nVal = 0;
															$html.= '<td align="right">'.number_format($nVal,$conf->global->ITEMS_DEFAULT_NUMBER_DECIMAL).'</td>';
															if($linek->group_structure == 'MQ')
															$this->aSpread[$idr][$n]['data'][5]=$aValue['E'];
															else
															$this->aSpread[$idr][$n]['data'][5]='';
															$html.= '<td align="right">'.number_format($aValue['F'],$conf->global->ITEMS_DEFAULT_NUMBER_DECIMAL).'</td>';
															$this->aSpread[$idr][$n]['data'][6]=$aValue['F'];
															$html.= '<td align="right">'.number_format($aValue['G'],$conf->global->ITEMS_DEFAULT_NUMBER_DECIMAL).'</td>';
															$this->aSpread[$idr][$n]['data'][7]=$aValue['G'];
															$html.= '</tr>';
														}
														$this->aSpreadformula[$idr][$n]['formula'] = 'sum()';
														//$aSt[$lineh->ref][$linei->formula]+=$ntotal;
														//$linek->code_structure.' '.$code.' '.$linek->quant;
														$_SESSION['cItem'][$idr][$linei->formula]+= $aValue['G'];
														$_SESSION['cFila'][$idr][$linei->formula]='G_'.$n.'_';
														$aTmp[$idr]='G'.$colini[$j].':G';
														$resop += $aValue['G'];
														$this->aLine[]=$myclass;
														$n++;
													}
												}
											}
											else
											{
												$html.= '<tr>';
												$html.= '<td>'.'</td>';
												$this->aSpread[$idr][$n]['data'][1]='';
												$html.= '<td>'.'</td>';
												$this->aSpread[$idr][$n]['data'][2]='';
												$html.= '<td align="right">'.number_format(0,$nProductdecimal).'</td>';
												$this->aSpread[$idr][$n]['data'][3]=price2num(0,$nProductdecimal);
												$html.= '<td align="right">'.'</td>';
												$this->aSpread[$idr][$n]['data'][4]='';
												$html.= '<td align="right">'.'</td>';
												$this->aSpread[$idr][$n]['data'][5]='';
												$html.= '<td align="right">'.'</td>';
												$this->aSpread[$idr][$n]['data'][6]='';
												$html.= '<td align="right">'.'</td>';
												$this->aSpread[$idr][$n]['data'][7]=0;
												$html.= '</tr>';

												$n++;
												//echo '<hr>vacio '.$n;
											}
										}
										else
										{
											$myclass = new stdClass();
											$myclass->label = '';
											$myclass->total = 0;
											$this->aLine[]=$myclass;
										}
									}
									if ($type == 'pu_formulas')
									{
										//$fk = $aChange[0];
										//$code = $aChange[1];
										$_SESSION['cItem'][$idr][$linei->formula]+= $_SESSION['cItem'][$idr][$code];
										//echo '<br>pu_formula '.
										$linei->formula.' = '.$_SESSION['cItem'][$idr][$linei->formula];
										$resop+= $_SESSION['cItem'][$idr][$code];
										//echo '<br>type '.$type;
										//print_r($_SESSION['cItem'][$idr]);
									}
									//echo '<br>resultado '.$resop.' para '.$lineh->group_structure.' enlalinea= '.$n;
									//echo '<br>';
									//print_r($aTmpsum[$lineh->group_structure]);
									//echo '<br>finaTmpsum';
									$filaini=0;
									$filafin=0;
									if($aTmpsum[$lineh->group_structure])
									{
										foreach ((array) $aTmpsum[$lineh->group_structure] AS $fila)
										{
											if (empty($filaini)) $filaini = $fila;
											$filafin=$fila;
										}
										$aMgroup['('.$lineh->group_structure.')'] = 'G_'.$filaini.'_:G_'.$filafin.'_';
									}
									else
									{
										$nTmp = $n-1;
										$aMgroup['('.$lineh->group_structure.')'] = 'G_'.$nTmp.'_:G_'.$nTmp.'_';
									}

									//return $_SESSION['cItem'][$idr][$linei->formula];
									//$this->aCel[$idr][$n]['G'] = 'G'.$colini.':G'.$colfin;
								}
								else
								{
									//vamos a armar la formula para la linea
									$formula.= $resop;
									//echo '<br>';
									//print_r($aTmpsum);
									//$this->aFormuladet[$linej->ref_formula][$linej->fk_operator]['typeo'] = $type;
									$this->aSpreadformula[$idr][$n]['formula'] = $objop->operator;

									//echo ' <hr>Operator '.$objop->operator.' code '.$code.' celda ';
									//echo '  resopparaotro '.$resop;
									$formuladetnew[$linej->ref_formula][$linej->id]= $objop->operator;
									if ($objop->operator == '+')
									{
										$operator = 'mas';
										if ($type == 'pu_formulas')
										{
											//$fk = $aChange[0];
											//$code = $aChange[1];
											$result = $_SESSION['cItem'][$idr][$code];
											//$this->aCel[$idr][$n][]
											$formuladet.='+'.$result;
											$resop += $result;
											$value_post=$resop;
											$formuladetnew[$linej->ref_formula][$linej->id].=$_SESSION['cFila'][$idr][$code];
										}
										if ($type == 'parameter_calculation')
										{
											//$fk = $aChange[0];
											//$code = $aChange[1];
											//buscamos en la tabla budget_concept
											$objParametercalculation->fetch(0,$code);
											if ($objParametercalculation->ref == $code)
											{
												if (!empty($objParametercalculation->type))
												{
													$value_post = GETPOST($objParametercalculation->type);
													$resop += $value_post*1;
													$formuladet.='+G_'.$n.'_';
													$this->aFormuladet[$linei->ref_structure][$linei->formula] = $value_post;
												}
												else
												{
													$resop += $objParametercalculation->amount*1;
													$value_post = $objParametercalculation->amount;
													$formuladet.='+G_'.$n.'_';
													$this->aFormuladet[$linei->ref_structure][$linei->formula] = $value_post;
												}
											}
											else
											setEventMessages($objParametercalculation->error,$objParametercalculation->errors,'errors');
										}
										if ($type == 'valor')
										{
											$resop += $value;
											$value_post = $value;
											$formuladet.='+G_'.$n.'_';
										}
									}
									if ($objop->operator == '-')
									{
										$operator = 'menos';
										if ($type == 'pu_formulas')
										{
											//$fk = $aChange[0];
											//	$code = $aChange[1];
											$result = $_SESSION['cItem'][$idr][$code];
											$resop -= $result;
											$value_post = $result;
											$formuladet.='-G_'.$n.'_';
										}
										if ($type == 'parameter_calculation')
										{
											//$fk = $aChange[0];
											//$code = $aChange[1];
											//buscamos en la tabla budget_concept
											$objParametercalculation->fetch(0,$code);
											if ($objParametercalculation->ref == $code)
											{
												if (!empty($objParametercalculation->type))
												{
													$value_post = GETPOST($objParametercalculation->type);
													$resop -= $value_post*1;
													$formuladet.='-G_'.$n.'_';
													$this->aFormuladet[$linei->ref_structure][$linei->formula] = $value_post;
												}
												else
												{
													$resop -= $objParametercalculation->amount;
													$value_post = $objParametercalculation->amount;
													$formuladet.='-G_'.$n.'_';
													$this->aFormuladet[$linei->ref_structure][$linei->formula] = $value_post;
												}
											}
											else
											setEventMessages($objParametercalculation->error,$objParametercalculation->errors,'errors');
										}
										if ($type == 'valor')
										{
											$resop -= $value;
											$value_post = $value;
											$formuladet.='-G_'.$n.'_';
										}
									}
									if ($objop->operator == '*')
									{
										$operator = 'por';
										if ($type == 'pu_formulas')
										{
											//$fk = $aChange[0];
											//$code = $aChange[1];
											$result = $_SESSION['cItem'][$idr][$code];
											$resop = price2num($resop * $result,$conf->global->ITEMS_DEFAULT_NUMBER_DECIMAL);
											$value_post = $resop;
											$formuladet.=$_SESSION['cFila'][$idr][$code];
										}
										if ($type == 'parameter_calculation')
										{
											//$fk = $aChange[0];
											//$code = $aChange[1];
											//buscamos en la tabla budget_concept
											$respc = $objParametercalculation->fetch(0,$code);
											if ($objParametercalculation->code == $code)
											{
												if (!empty($objParametercalculation->type))
												{
													$value_post = GETPOST($objParametercalculation->type);
													$resop = price2num($resop * $value_post ,$conf->global->ITEMS_DEFAULT_NUMBER_DECIMAL);
													$this->aFormuladet[$linei->ref_structure][$linei->formula] = $value_post;
													$formuladet= '+('.$formuladet.')'.'*F_'.$n.'_';
													$formuladetnew[$linej->ref_formula][$linej->id].='F_'.$n.'_';
												}
												else
												{
													$resop = price2num($resop * $objParametercalculation->amount,$conf->global->ITEMS_DEFAULT_NUMBER_DECIMAL);
													$value_post = $objParametercalculation->amount;
													$this->aFormuladet[$linei->ref_structure][$linei->formula] = $value_post;
													$formuladet= '+('.$formuladet.')'.'*F_'.$n.'_';
													$formuladetnew[$linej->ref_formula][$linej->id].='F_'.$n.'_';
												}

											}
											else
											setEventMessages($objParametercalculation->error,$objParametercalculation->errors,'errors');
										}
										if ($type == 'valor')
										{
											$resop = price2num($resop * $value,$conf->global->ITEMS_DEFAULT_NUMBER_DECIMAL);
											$value_post = $value;
											$formuladet.='*'.$value_post;
											$formuladet= '+('.$formuladet.')'.'*'.$value_post;
											$formuladetnew[$linej->ref_formula][$linej->id].=$value_post;
										}
									}
									if ($objop->operator == '/')
									{
										$operator = 'div';
										if ($type == 'pu_formulas')
										{
											//$fk = $aChange[0];
											//$code = $aChange[1];
											$result = $_SESSION['cItem'][$idr][$code];
											$resop = price2num($resop / $result,$conf->global->ITEMS_DEFAULT_NUMBER_DECIMAL);
											$value_post = $resop;
											//$formuladet='+('.$formuladet.')'.'/'.$value_post;
											$formuladet.=$_SESSION['cFila'][$idr][$code];
										}
										if ($type == 'parameter_calculation')
										{
											//$fk = $aChange[0];
											//$code = $aChange[1];
											//buscamos en la tabla budget_concept
											$objParametercalculation->fetch(0,$code);
											if ($objParametercalculation->ref == $code)
											{
												if (!empty($objParametercalculation->type))
												{
													$value_post = GETPOST($objParametercalculation->type);
													$resop = price2num($resop/$value_post , $conf->global->ITEMS_DEFAULT_NUMBER_DECIMAL);
													$this->aFormuladet[$linei->ref_structure][$linei->formula] = $value_post;
													//$formuladet.='/'.$value_post;
													$formuladet= '+('.$formuladet.')'.'/F_'.$n.'_';

												}
												else
												{
													$resop = price2num($resop / $objParametercalculation->amount,$conf->global->ITEMS_DEFAULT_NUMBER_DECIMAL);
													$value_post = $objParametercalculation->amount;
													$this->aFormuladet[$linei->ref_structure][$linei->formula] = $value_post;
													//$formuladet.='/'.$value_post;
													$formuladet= '+('.$formuladet.')'.'/F_'.$n.'_';

												}
												$formuladetnew[$linej->ref_formula][$linej->id].='F_'.$n.'_';
											}
											else
											setEventMessages($concept->error,$concept->errors,'errors');

										}
										if ($type == 'valor')
										{
											$resop = price2num($resop / $value,$conf->global->ITEMS_DEFAULT_NUMBER_DECIMAL);
											$value_post = $value;
											//$formuladet.= '/'.$value_post;
											$formuladet= '+('.$formuladet.')'.'/'.$value_post;
											$formuladetnew[$linej->ref_formula][$linej->id].=$value_post;
										}
									}

								}
							}
							//echo '<br>operator '.$operator.' linea '.$n.' formula '.$formuladet;
							$aReemplaza = array('(MA)','(MO)','(MQ)');
							//print_r($aMgroup);
							//echo '<hr>antes '.$formuladet;
							foreach ($aReemplaza AS $i => $newparam)
							{
								//vamos a reemplazar valores a laformula
								$formuladet = str_replace($newparam,'('.$aMgroup[$newparam].')',$formuladet);
							}
							//$_SESSION['cFila'][$idr][$linei->formula]='G'.$n;
							//echo '<br> FORMULAdet '.$formuladet;
							//echo '<pre>';
							//print_r($formuladetnew);
							unset($aLineform);
							//echo '<hr>formuladetnet ';
							//print_r($formuladetnew);

							foreach($formuladetnew AS $l1 => $aVal)
							{
								//vamos a contar cuantas veces se reproducen
								$nLoop=0;
								$nLoopmas = 0;
								foreach ($aVal AS $l2 => $val2)
								{
									//echo ' <br>val2== '.$val2;
									if (substr($val2,0,1)=='*' || substr($val2,0,1)=='/') $nLoop++;
									if (substr($val2,0,1)=='+' || substr($val2,0,1)=='-') $nLoopmas++;
								}
								if ($nLoopmas>0)
								{
									//echo ' <hr>nLoop '.$nLoop;
									if ($nLoop>0)
									{
										for ($a = 1; $a<=$nLoop; $a++)
										{
											//echo ' <hr>agrega '.
											$aLineform[$n].='+(';
										}
										//$aLineform[$n].='0';
									}
									foreach ($aVal AS $l2 => $val2)
									{
										//echo ' <br>val2 '.$val2;
										if (substr($val2,0,1)=='*' || substr($val2,0,1)=='/') $aLineform[$n].=')';
										$aLineform[$n].= $val2;
									}
								}
								else {
									foreach ($aVal AS $l2 => $val2)
									{
										$aLineform[$n].= $val2;
									}

								}
								//echo ' resultado '.$aLineform[$n];
							}
							//echo '</pre>';
							foreach ($aReemplaza AS $i => $newparam)
							{
								//vamos a reemplazar valores a laformula
								$aLineform[$n] = str_replace($newparam,'('.$aMgroup[$newparam].')',$aLineform[$n]);
							}
							//echo '<br>lineformula '.$n.' => '.$aLineform[$n];
							//la formula de la agrupacion
							//$formula.='+'.$
						}
						else
						{
							$myclass = new stdClass();
							$myclass->label = '';
							$myclass->total = 0;
							$this->aLine[]=$myclass;
						}
						$_SESSION['cItem'][$idr][$linei->formula] = $resop;
						$aSt[$lineh->ref][$linei->formula]+=price2num($resop,$conf->global->ITEMS_DEFAULT_NUMBER_DECIMAL);
						//echo ' <hr>status_print |'.$linei->status_print.'|'.' '.$resop;
						if ($linei->status_print)
						{
							//ECHO '<hr>resoptotal '.$resop.' '.$linei->detail;
							$html.= '<tr class="total">';
							$html.= '<td>'.$linei->detail.'</td>';
							$html.= '<td colspan="4">&nbsp;</td>';
							$html.= '<td></td>';
							$html.= '<td align="right">'.number_format(price2num($resop,$decimal_total),$conf->global->ITEMS_DEFAULT_NUMBER_DECIMAL).'</td>';
							$html.= '</tr>';
							$this->aSpread[$idr][$n]['partial'][1]=$linei->detail;
							if ($this->aFormuladet[$linei->ref_structure][$linei->formula]>0)
							$this->aSpread[$idr][$n]['partial'][6]=price2num($this->aFormuladet[$linei->ref_structure][$linei->formula],'MT').' %';

							$this->aSpread[$idr][$n]['partial'][7]=price2num($resop,$decimal_total);
							//ECHO '<hr>parciales';

							$nAnt=$n-1;
							//echo '<br>nant '.$nAnt.' n '.$n;
							if ($operator=='sum')
							{
								$this->aCel[$idr][$n]='+SUM(G_'.$colini[$j].'_:G_'.$nAnt.'_)';
							}
							else
							{
								//echo '<br>otro '.
								$this->aCel[$idr][$n]=$aLineform[$n];
							}

							//echo '<br>guardadoen '.
							$_SESSION['cFila'][$idr][$linei->formula]='G_'.$n.'_';
							//$this->aSpreadf[$idr][$n]['total'][8]=$linej->ref_formula;
							$n++;
							$myclass = new stdClass();

							$myclass->label = $linei->detail;
							$myclass->total = $resop;
							$myclass->format = 'partial';
							$this->aLine[]=$myclass;
							//echo '<br>resultadogruadardos';
							//print_r($_SESSION['cItem'][$idr]);
							//echo '<br>';
							//print_r($_SESSION['cFila'][$idr]);

						}
						else {
							$nAnt=$n-1;
							if ($operator=='sum')
							{
								$this->aCel[$idr][$n]='+SUM(G_'.$colini[$j].'_:G_'.$nAnt.'_)';
							}
							else
							{
								//echo '<hr>xotro '.
								$this->aCel[$idr][$n]=$aLineform[$n];
							}
						}
						//echo '<br>FINALIZANDO CON '.$linei->formula;
						$aFormula[$lineh->ref][$linei->formula]=(!empty($_SESSION['cFila'][$idr][$linei->formula])?$_SESSION['cFila'][$idr][$linei->formula]:0);
						$formula.='+'.(!empty($_SESSION['cFila'][$idr][$linei->formula])?$_SESSION['cFila'][$idr][$linei->formula]:0);
						//echo '<br>FORMULAPRIN '.$formula;
						//echo '<br>';
						//print_r($aFormula);
					}


				}
				else
				{
					$myclass = new stdClass();
					$myclass->label = '';
					$myclass->total = 0;

					$this->aLine[]=$myclass;
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
				$html.= '<td align="right">'.number_format($sumatot,$conf->global->ITEMS_DEFAULT_NUMBER_DECIMAL).'</td>';
				$html.= '</tr>';
				$order++;
				$this->aSpread[$idr][$n]['total'][1]=$langs->trans('TOTAL').' '.DOL_STRTOUPPER($lineh->detail);
				$this->aSpread[$idr][$n]['total'][7]=price2num($sumatot,$conf->global->ITEMS_DEFAULT_NUMBER_DECIMAL);
				//ECHO  '<br>NNNNN '.$n;
				//echo '<pre>';
				//print_r($aFormula);
				foreach ($aFormula[$lineh->ref] AS $ja => $val1)
				{
					if (!empty($val1))
					{
						if($this->aCel[$idr][$n]) $this->aCel[$idr][$n].='+';
						$this->aCel[$idr][$n].=$val1;
						if($formulaTotal) $formulaTotal.='+';
						$formulaTotal.=$val1;
					}
					$aTotal[$lineh->ref][$ja]=$val1;
				}

				$n++;
				$myclass = new stdClass();
				$myclass->label = $langs->trans('TOTAL').' '.$lineh->detail;
				$myclass->total = $sumatot;
				$myclass->format = 'total';

				$this->aLine[]=$myclass;
			}
		}
		//echo '<hr />formula';
		//print_r($aFormula);
		//almacenamos en el item
		$aData = $_SESSION['cItem'][$idr];
		$suma = 0;
		foreach ((array) $aData as $i => $value)
		$suma += $value;
		$_SESSION['sumaitem'][$idr]=price2num($suma,$conf->global->ITEMS_DEFAULT_NUMBER_DECIMAL);
		$html.= '<tr class="total">';
		$html.= '<td colspan="5">&nbsp;</td>';
		$html.= '<td>'.$langs->trans('TOTAL PRECIO UNITARIO').'</td>';
		$html.= '<td align="right">'.number_format($suma,$conf->global->ITEMS_DEFAULT_NUMBER_DECIMAL).'</td>';
		$html.= '</tr>';
		$this->aSpread[$idr][$n]['totalpu'][1]=$langs->trans('TOTAL PRECIO UNITARIO');
		$this->aSpread[$idr][$n]['totalpu'][7]=price2num($suma,$conf->global->ITEMS_DEFAULT_NUMBER_DECIMAL);
		$this->aSpreadformula[$idr][$n]['formula'] = '+';
		$this->aCel[$idr][$n].=$formulaTotal;
		$n++;

		$myclass = new stdClass();
		$myclass->label = $langs->trans('Total Precio Unitario');
		$myclass->total = $suma;
		$myclass->format = 'total';
		$this->aLine[]=$myclass;
		$this->viewhtml = $html;
		//echo '<table>'.$html.'</table>';
		if ($rep)
			return array($html,price2num($suma,$conf->global->ITEMS_DEFAULT_NUMBER_DECIMAL));
		else
			return price2num($suma,$conf->global->ITEMS_DEFAULT_NUMBER_DECIMAL);
	}

	public function fetchGroupregion($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lView=false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= " t.ref,";
		$sql .= " t.group_structure,";
		$sql .= " t.fk_unit,";
		$sql .= " t.label,";

		$sql .= " SUM(r.units) AS units,";
		$sql .= " MAX(r.commander) AS commander,";
		$sql .= " MIN(r.performance) AS performance,";
		$sql .= " MIN(r.price_productive) AS price_productive,";
		$sql .= " MIN(r.price_improductive) AS price_improductive,";
		$sql .= " MIN(r.amount_noprod) AS amount_noprod,";
		$sql .= " MIN(r.amount) AS amount,";
		$sql .= " MIN(r.cost_direct) AS cost_direct ";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element. ' as t';
		$sql .= ' INNER JOIN ' . MAIN_DB_PREFIX . 'items_product_region'. ' as r ON r.fk_item_product = t.rowid';

		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				$sqlwhere [] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
			}
		}
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
			$sql .= " AND entity IN (" . getEntity("itemsproduct", 1) . ")";
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' AND ' . implode(' '.$filtermode.' ', $sqlwhere);
		}
		if ($filterstatic){
			$sql.= $filterstatic;
		}
		//vamos a agrupar
		$sql.= " GROUP BY t.ref, t.group_structure, t.fk_unit, t.label ";
		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield,$sortorder);
		}
		if (!empty($limit)) {
			$sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
		}

		$this->lines = array();

		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new ItemsproductLine();

				$line->id = $obj->rowid;

				$line->ref = $obj->ref;
				$line->fk_item = $obj->fk_item;
				$line->group_structure = $obj->group_structure;
				$line->fk_product = $obj->fk_product;
				$line->fk_unit = $obj->fk_unit;
				$line->label = $obj->label;
				$line->formula = $obj->formula;
				$line->active = $obj->active;
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->datec = $this->db->jdate($obj->datec);
				$line->datem = $this->db->jdate($obj->datem);
				$line->tms = $this->db->jdate($obj->tms);
				$line->status = $obj->status;

				$line->units = $obj->uinits;
				$line->commander = $obj->commander;
				$line->performance = $obj->performance;
				$line->price_productive = $obj->price_productive;
				$line->price_improductive = $obj->price_improductive;
				$line->amount_noprod = $obj->amount_noprod;
				$line->amount = $obj->amount;
				$line->cost_direct = $obj->cost_direct;


				if ($lView && $num == 1) $this->fetch($obj->rowid);

				$this->lines[] = $line;
			}
			$this->db->free($resql);

			return $num;
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . implode(',', $this->errors), LOG_ERR);

			return - 1;
		}
	}
}

class ItemsproductLineext extends CommonObjectLine
{
	/**
	* Constructor
	*
	* @param DoliDb $db Database handler
	*/
	public function __construct(DoliDB $db)
	{
		$this->db = $db;
	}
}
?>
