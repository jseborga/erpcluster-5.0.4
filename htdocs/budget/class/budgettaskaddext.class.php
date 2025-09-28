<?php
require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettaskadd.class.php';

class Budgettaskaddext extends Budgettaskadd
{

	var $aLine;
	var $aLinetot;
	var $viewhtml;
	var $aStr;
	var $aStrref;
	var $aConcept;
	var $aStracum;
	var $aSumitem;
	var $aSpread;
	var $aGroup;
	/**
	 *	Returns the text label from units dictionary
	 *
	 * 	@param	string $type Label type (long or short)
	 *	@return	string|int <0 if ko, label if ok
	*/
	function getLabelOfUnit($type='long')
	{
		global $langs;

		if (!$this->fk_unit) {
			return '';
		}

		$langs->load('products');

		$this->db->begin();

		$label_type = 'label';

		if ($type == 'short')
		{
			$label_type = 'short_label';
		}

		$sql = 'select '.$label_type.' from '.MAIN_DB_PREFIX.'c_units where rowid='.$this->fk_unit;
		$resql = $this->db->query($sql);
		if($resql && $this->db->num_rows($resql) > 0)
		{
			$res = $this->db->fetch_array($resql);
			$label = $langs->trans($res[$label_type]);
			$this->db->free($resql);
			return $label;
		}
		else
		{
			$this->error=$this->db->error().' sql='.$sql;
			dol_syslog(get_class($this)."::getLabelOfUnit Error ".$this->error, LOG_ERR);
			return -1;
		}
	}
	public function search_array_task(User $user,$id,$idgroup,array $aGroup=array(), $level=1,$aLevel=array(),$nLoop=0)
	{
		//vamos a agrupar los grupos y sus tareas
		require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettaskext.class.php';
		$objtask = new Budgettaskext($this->db);
		$objtask->fetch($idgroup);
		$this->fetch(0,$idgroup);
		$aGroup[$level][$this->c_grupo][$objtask->fk_task_parent][$idgroup] = $idgroup;
		$lLoop = true;
		$nLoop++;
		$aLevel[$nLoop]=$level;
		while ($lLoop==true)
		{
			$sql = " SELECT j.rowid FROM ".MAIN_DB_PREFIX."budget_task AS j ";
			$sql.= " WHERE j.fk_task_parent = ".$idgroup;

			//$filterstatic = " AND t.fk_task_parent = ".$idgroup;
			$filterstatic= " AND t.fk_budget_task IN (".$sql.")";
			$res = $this->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic);
			if ($res >0)
			{
				//$level++;
				$leveltmp = $level+1;
				$aLevel[$nLoop]=$leveltmp;
				$lines = $this->lines;
				foreach ($lines AS $j => $line)
				{
					$res1 = $objtask->fetch($line->fk_budget_task);
					//echo  ' <hr>line  '.$line->c_grupo .' '.$line->fk_budget_task.' '.$objtask->fk_task_parent;
					$aGroup[$aLevel[$nLoop]][$line->c_grupo][$objtask->fk_task_parent][$line->fk_budget_task] = $line->fk_budget_task;

					if ($line->c_grupo)
					{
						list($aGroup,$aLevel) = $this->search_array_task($user,$id,$line->fk_budget_task,$aGroup,$leveltmp,$aLevel,$nLoop);
					}
				}
				$lLoop=false;
			}
			else $lLoop=false;
		}
		if (is_array($aGroup))
		{
			return array($aGroup,$aLevel);
		}
		return array();
	}

	public function procedure_calculo_group(User $user,$id,$idgroup,$lCalc=false,$level=1)
	{
		global $conf,$langs;
		$aGroup = array();
		list($aGroup,$aLevel) = $this->search_array_task($user,$id,$idgroup,$aGroup,$level);
		krsort($aGroup);
		$total = 0;
		$this->aStracum=array();
		$res = count($aGroup);
		if ($res>0)
		{
			foreach ($aGroup AS $lev => $aGr)
			{
				foreach ($aGr AS $c_grupo => $data)
				{
					foreach ($data AS $fk_parent => $row)
					{
						foreach ($row AS $fk => $fkval)
						{
							if (empty($c_grupo))
							{
								if ($lCalc)
								{
									$this->fetch($fk);
								//$this->aStr = array();
									$sum =$this->procedure_calculo($user,$id,$fk,$lCalc);
									$aStr = $this->aStr;
									$sumVal=0;
									foreach ($aStr AS $label => $value)
									{
										$val = $this->unit_budget*$value;
										$aStracum[$label]+=$val;
										$sumVal+=$val;
									}
									$this->aSumitem[$id][$fk]=$sumVal;
									$this->aSumitem[$id][$fk_parent]+=$sumVal;
								}
								else
								{

								}
							}
							else
							{
								$this->aSumitem[$id][$fk_parent]+=$this->aSumitem[$id][$fk];
							}
						}
					}
				}
			}
		}
		$this->aStracum = $aStracum;
		$total = $this->aSumitem[$id][$idgroup];
		return $total;
	}




	public function procedure_sum_task($user,$id,$fk_budget_task,$unit_budget=0)
	{
		global $conf,$langs;
		$sum =$this->procedure_calculo($user,$id,$fk_budget_task,false);
		$sumVal=0;
		foreach ($this->aStr AS $label => $value)
		{
			$val = $unit_budget*$value;
			$this->aStracum[$label]+=$val;
			$sumVal+=$value;
		}
		$this->aSumitem[$id][$fk_budget_task]=$sum;
		return 1;
	}

	public function procedure_calculo_budget($id)
	{
		global $conf,$langs;
		require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettaskext.class.php';
		$sql = " SELECT j.rowid FROM ".MAIN_DB_PREFIX."budget_task AS j ";
		$sql.= " WHERE j.fk_budget = ".$id;
		$sql.= " AND j.fk_task_parent = 0 ";
		$objtask = new Budgettaskext($this->db);
		$filterstatic = " AND t.c_grupo  = 1";
		$filterstatic.= " AND t.fk_budget_task IN (".$sql.")";
		$res = $this->fetchAll('','',0,0,array(),'AND',$filterstatic);
		$total = 0;
		if ($res>0)
		{
			$lines = $this->lines;
			foreach ($lines AS $j => $line)
			{
				$total+= $line->total_amount;
			}
		}
		return $total;
	}

	public function procedure_calculo(User $user,$id,$idr,$ret=false)
	{
		//global $conf,$langs,$object,$objstr,$objstrdet;
		global $conf,$langs,$object;
		require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettaskext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettaskaddext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettaskresource.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/pustructureext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/pustructuredetext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/puoperatorext.class.php';

		require_once DOL_DOCUMENT_ROOT.'/budget/class/puformulasdetext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/productbudgetext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/budgetgeneral.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/budgetconcept.class.php';
		require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';

		$objformdet = new Puformulasdetext($this->db);
		$objop		= new Puoperatorext($this->db);
		$productbtr = new Productbudgetext($this->db);
		$general_   = new Budgetgeneral($this->db);
		$categorie  = new Categorie($this->db);
		$concept 	= new Budgetconcept($this->db);
		$objectdet_ = new Budgettaskext($this->db);
		$objectdetadd_ = new Budgettaskaddext($this->db);
		$objectbtr_ = new Budgettaskresource($this->db);
		$objstr_	= new Pustructureext($this->db);
		$objstrdet_	= new Pustructuredetext($this->db);
		$aSt = array();
		$_SESSION['sumaitem'][$idr]=0;
		//recuperamos el item
		$objectdet_->fetch($idr);
		$objectdetadd_->fetch(0,$idr);
		$taskcomple = $objectdetadd_->complementary;
		//recuperamos parametros generales
		$general_->fetch(0,$id);
		$filterstatic = " AND t.fk_budget_task = ".$idr;
		$objectbtr_->fetchAll('ASC', 't.code_structure',0,0,array(1=>1),'AND',$filterstatic);
		$this->aStr=array();
		//recuperamos la estrcutura
		$_SESSION['cItem'][$idr] = array();
		$filterstatic = " AND t.type_structure = '".$object->type_structure."'";
		$filterstatic.= " AND t.status = 1";
		$res = $objstr_->fetchAll('ASC', 'ordby', 0, 0, array(1=>1), 'AND',$filterstatic);
		unset($_SESSION['cItem'][$idr]);
		$order = 1;
		foreach ((array) $objstr_->lines AS $h => $lineh)
		{
			$strcomple = $lineh->complementary;
			$lPrint = true;
			if ($taskcomple && !$strcomple) $lPrint = false;
			if ($lPrint)
			{
				$filterstatic = " AND t.entity = ".$lineh->entity;
				$filterstatic.= " AND t.ref_structure = '".$lineh->ref."'";
				$filterstatic.= " AND t.type_structure = '".$lineh->type_structure."'";
				$filterstatic.= " AND t.status = 1";
				$res1 = $objstrdet_->fetchAll('ASC', 'sequen', 0, 0, array(1=>1), 'AND',$filterstatic);
				foreach ((array) $objstrdet_->lines AS $i => $linei)
				{
					$filterstatic = " AND t.ref_formula = '".$linei->formula."'";
					$filterstatic.= " AND t.status = 1";
					$resfd = $objformdet->fetchAll('ASC', 'sequen', 0,0,array(1=>1),'AND', $filterstatic);
					$resop = 0;
					foreach((array) $objformdet->lines AS $j => $linej)
					{
						$type = $linej->type;

						$objop->fetch($linej->fk_operator);
						if ($linej->type != 'valor')
						{
							$aChange = explode('|',$linej->changefull);
						}
						else
							$value = $linej->changefull;
						if ($objop->operator == 'sum()')
						{
							if ($type == 'pu_structure')
							{
								$fk = $aChange[0];
								$code = $aChange[1];
								foreach((array) $objectbtr_->lines AS $k => $linek)
								{
									if ($linek->code_structure == $code)
									{
										$productbtr->fetch($linek->fk_product_budget);
										$nprod = price2num(($linek->quant*$linek->percent_prod*$linek->amount/100),$general_->decimal_total);
										$nnprod = price2num(($linek->quant*(100-$linek->percent_prod)*$linek->amount_noprod/100),$general_->decimal_total);
										$ntotal = $nprod + $nnprod;
										//echo 'line '.$idr.' '.$linei->formula;
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
									{
										require_once DOL_DOCUMENT_ROOT.'/budget/class/parametercalculation.class.php';
										$parameters = new Parametercalculation($this->db);
										$parameters->fetch(0,$code);
										$_SESSION['aConcept'][$id][$code] = $code;

										//setEventMessages($concept->error,$concept->errors,'errors');
										//setEventMessages($langs->trans('No existe el concepto').' '.$code.' '.$langs->trans('con nombre').': '.$parameters->label.', '.$langs->trans('Consulte con su administrador'),null,'warnings');
									}
								}
								if ($type == 'valor')
								{
									if ($value>0)
										$resop += $value;
								}
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
									{
										require_once DOL_DOCUMENT_ROOT.'/budget/class/parametercalculation.class.php';
										$parameters = new Parametercalculation($this->db);
										$parameters->fetch(0,$code);
										$_SESSION['aConcept'][$id][$code] = $code;

										//setEventMessages($concept->error,$concept->errors,'errors');
										//setEventMessages($langs->trans('No existe el concepto').' '.$code.' '.$langs->trans('con nombre').': '.$parameters->label.', '.$langs->trans('Consulte con su administrador'),null,'warnings');
									}
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
								if ($type == 'parameter_calculation')
								{
									$fk = $aChange[0];
									$code = $aChange[1];
									//buscamos en la tabla budget_concept
									$concept->fetch(0,$code,$id);
									if ($concept->ref == $code && $concept->fk_budget == $id)
									{
										$resop = price2num($resop * $concept->amount,$general_->decimal_total);
										if ($concept->amount <=0)
											setEventMessages($langs->trans('El concepto ').' '.$concept->label.' '.$langs->trans('Esta con valor 0, revise'),null,'warnings');
									}
									else
									{

										require_once DOL_DOCUMENT_ROOT.'/budget/class/parametercalculation.class.php';
										$parameters = new Parametercalculation($this->db);
										$parameters->fetch(0,$code);
										$_SESSION['aConcept'][$id][$code] = $code;

										//setEventMessages($concept->error,$concept->errors,'errors');
										//setEventMessages($langs->trans('No existe el concepto').' '.$code.' '.$langs->trans('con nombre').': '.$parameters->label.', '.$langs->trans('Consulte con su administrador'),null,'warnings');
									}
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
								if ($type == 'parameter_calculation')
								{
									$fk = $aChange[0];
									$code = $aChange[1];
									//buscamos en la tabla budget_concept
									$concept->fetch(0,$code,$id);
									if ($concept->ref == $code && $concept->fk_budget == $id)
										$resop = price2num($resop / $concept->amount,$general_->decimal_total);
									else
									{
										require_once DOL_DOCUMENT_ROOT.'/budget/class/parametercalculation.class.php';
										$parameters = new Parametercalculation($this->db);
										$parameters->fetch(0,$code);
										$_SESSION['aConcept'][$id][$code] = $code;

										//setEventMessages($concept->error,$concept->errors,'errors');
										//setEventMessages($langs->trans('No existe el concepto').' '.$code.' '.$langs->trans('con nombre').': '.$parameters->label.', '.$langs->trans('Consulte con su administrador'),null,'warnings');
									}
								}
								if ($type == 'valor') $resop = price2num($resop / $value,$general_->decimal_total);
							}
						}
					}
					$_SESSION['cItem'][$idr][$linei->formula] = $resop;
					$aSt[$lineh->ref][$linei->formula]+=price2num($resop,$general_->decimal_total);
					$this->aStr[$lineh->detail] += price2num($resop,$general_->decimal_total);
					$this->aStrref[$lineh->ref] += price2num($resop,$general_->decimal_total);
					$this->aConcept=$_SESSION['cItem'][$idr];
				}
				$order++;
			}
		}
		$aData = $_SESSION['cItem'][$idr];
		$suma = 0;
		//echo '<hr>';
		//echo '<pre>';
		//print_r($aData);
		//print_r($this->aStr);
		//echo '</pre>';
		//exit;
		foreach ((array) $aData as $i => $value)
			$suma += $value;
		//vamoa actualizar en la tabla
		//echo '<hr>actualizaen '.$idr;
		$res = $this->fetch(0,$idr);
		if ($res==1)
		{
			$this->unit_amount=price2num($suma,$general_->decimal_total);
			$res = $this->update($user);
		}
		$_SESSION['sumaitem'][$idr]=price2num($suma,$general_->decimal_total);

		return price2num($suma,$general_->decimal_total);
	}


	//procedimiento de calculo unitario
	function procedure_calc($id,$idr,$rep=false)
	{
		global $conf,$langs;
		//global $objectdetadd;
		global $object;
		require_once DOL_DOCUMENT_ROOT.'/budget/class/puformulasdetext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/productbudgetext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/budgetgeneral.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/budgetconcept.class.php';
		require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettaskext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettaskresourceext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/pustructureext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/pustructuredetext.class.php';

		$objectbtr 	= new Budgettaskresourceext($this->db);
		$objectdet 	= new Budgettaskext($this->db);
		$objformdet = new Puformulasdetext($this->db);
		$objop		= new Puoperatorext($this->db);
		$productbtr = new Productbudgetext($this->db);
		$general    = new Budgetgeneral($this->db);
		$concept 	= new Budgetconcept($this->db);
		$categorie  = new Categorie($this->db);
		$objstr		= new Pustructureext($this->db);
		$objstrdet	= new Pustructuredetext($this->db);
		$html = '';
		$aLine = array();
		$aLinetot = array();
		$aSt = array();
		$_SESSION['sumaitem'][$idr]=0;
		$this->aSpread=array();
		$this->aConcept=array();
		$n=0;
		//recuperamos el item
		$objectdet->fetch($idr);
		$this->fetch(0,$idr);
		$taskcomple = $this->complementary;
		//recuperamos parametros generales
		$general->fetch(0,$id);
		$filterstatic = " AND t.fk_budget_task = ".$idr;
		$resbtr = $objectbtr->fetchAll('ASC', 't.code_structure',0,0,array(),'AND',$filterstatic);

		//recuperamos la estrcutura
		$_SESSION['cItem'][$idr] = array();
		$filterstatic = " AND t.type_structure = '".$object->type_structure."'";
		$objstr->fetchAll('ASC', 'ordby', 0, 0, array(1=>1), 'AND',$filterstatic);
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
		foreach ((array) $objstr->lines AS $h => $lineh)
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
				//$filterstatic = " AND t.fk_pu_structure = ".$lineh->id;
				$filterstatic = " AND t.entity = ".$lineh->entity;
				$filterstatic.= " AND t.ref_structure = '".$lineh->ref."'";
				$filterstatic.= " AND t.type_structure = '".$lineh->type_structure."'";
				$res1 = $objstrdet->fetchAll('ASC', 'sequen', 0, 0, array(1=>1), 'AND',$filterstatic);
				if ($res1 > 0)
				{
					foreach ((array) $objstrdet->lines AS $i => $linei)
					{
						//procesamos el calculo de la formula
						$filterstatic = " AND t.ref_formula = '".$linei->formula."'";
						$filterstatic.= " AND t.status = 1";
						$resfd = $objformdet->fetchAll('ASC', 'sequen', 0,0,array(1=>1),'AND', $filterstatic);
						//if ($order == 4 ) echo '<hr>resfd '.$resfd;
						$resop = 0;
						if ($resfd>0)
						{
							foreach((array) $objformdet->lines AS $j => $linej)
							{
								//buscamos el operator
								$type = $linej->type;

								$resope = $objop->fetch($linej->fk_operator);
								if ($linej->type != 'valor')
								{
									$aChange = explode('|',$linej->changefull);
								}
								else
									$value = $linej->changefull;
								//segun el operador
								//if ($order == 4) echo '<br>type'.$type.' '.$objop->operator;
								if ($objop->operator == 'sum()')
								{
									//$resop = 0;
									//utilizamos $aChange

									if ($type == 'pu_structure')
									{
										$fk = $aChange[0];
										$code = $aChange[1];

										//procedemos a la suma de la tabla
										if ($resbtr>0)
										{
											foreach((array) $objectbtr->lines AS $k => $linek)
											{
												if ($linek->code_structure == $code)
												{
													$myclass = new stdClass();
													$myclass->label = $linek->detail;
													$myclass->format = 'det';

													if ($linei->status_print_det)
													{
														$html.= '<tr>';
														$html.= '<td>'.$linek->detail.'</td>';
														$this->aSpread[$idr][$n]['data'][1]=$linek->detail;
													}

													$productbtr->fetch($linek->fk_product_budget);
													if ($productbtr->id == $linek->fk_product_budget)
													{
														if ($linei->status_print_det)
														{
															$html.= '<td>'.$productbtr->getLabelOfUnit('short').'</td>';

															$this->aSpread[$idr][$n]['data'][2]=$langs->trans($productbtr->getLabelOfUnit('short'));
														}
														$myclass->unit = $langs->trans($productbtr->getLabelOfUnit('short'));
													}
													else
													{
														if ($linei->status_print_det)
															$html.= '<td>nn</td>';
													}
													$nprod = price2num(($linek->quant*$linek->percent_prod*$linek->amount/100),$general->decimal_total);
													$nnprod = price2num(($linek->quant*(100-$linek->percent_prod)*$linek->amount_noprod/100),$general->decimal_total);
													$ntotal = $nprod + $nnprod;
													//echo ' <hr>NTOTAL '.$ntotal.' '.$nprod.' '.$nnprod;
													if ($linei->status_print_det)
													{
														$html.= '<td align="right">'.number_format($linek->quant,$general->decimal_quant).'</td>';
														$this->aSpread[$idr][$n]['data'][3]=price2num($linek->quant,$general->decimal_quant);
													}

													$myclass->unit_budget = $linek->quant;
													if ($linei->status_print_det)
													{
														$html.= '<td align="right">'.price($linek->percent_prod).' %</td>';
														$this->aSpread[$idr][$n]['data'][4]=price2num($linek->percent_prod);
													}
													$myclass->percent_prod = $linek->percent_prod;
													if ($linei->status_print_det)
													{
														$html.= '<td align="right">'.number_format($linek->amount_noprod,$general->decimal_pu).'</td>';
														$this->aSpread[$idr][$n]['data'][5]=price2num($linek->amount_noprod,$general->decimal_pu);
													}
													$myclass->amount_noprod = $linek->amount_noprod;
													if ($linei->status_print_det)
													{
														$html.= '<td align="right">'.number_format($linek->amount,$general->decimal_pu).'</td>';
														$this->aSpread[$idr][$n]['data'][6]=price2num($linek->amount,$general->decimal_pu);
													}
													$myclass->unit_amount = $linek->amount;
													if ($linei->status_print_det)
													{
														$html.= '<td align="right">'.number_format($ntotal,$general->decimal_total).'</td>';
														$this->aSpread[$idr][$n]['data'][7]=price2num($ntotal,$general->decimal_total);
													}
													$myclass->total = $ntotal;
													if ($linei->status_print_det)
														$html.= '</tr>';
													//$aSt[$lineh->ref][$linei->formula]+=$ntotal;
													//$linek->code_structure.' '.$code.' '.$linek->quant;
													$_SESSION['cItem'][$idr][$linei->formula]+= $ntotal;
													//echo ' <br>sumaresop == '.
													$resop += $ntotal;
													$linei->formula.' = '.$_SESSION['cItem'][$idr][$linei->formula];
													$this->aLine[]=$myclass;
													$n++;
												}
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
											//if ($order==4)
											//{
											//	echo '<hr>sesssss '.$idr.' '.$code;
											//	print_r($_SESSION['cItem']);
											//	echo ' <br>rsultvalor '.$result;
											//}
											$resop += $result;
										}
										if ($type == 'parameter_calculation')
										{
											$fk = $aChange[0];
											$code = $aChange[1];
											//buscamos en la tabla budget_concept
											$concept->fetch(0,$code,$id);
											if ($concept->ref == $code && $concept->fk_budget == $id)
											{
												$resop += $concept->amount;
											}
											else
												setEventMessages($concept->error,$concept->errors,'errors');
										}
										if ($value>0)
										{
											if ($type == 'valor') $resop += $value;
										}
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
						}
						else
						{
							$myclass = new stdClass();
							$myclass->label = '';
							$myclass->total = 0;
							$this->aLine[]=$myclass;
						}
						//echo '<hr>RESOP== '.$resop;
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
							$n++;
							$this->aSpread[$idr][$n]['total'][1]=$linei->detail;
							$this->aSpread[$idr][$n]['total'][7]=price2num($resop,$decimal_total);
							$n++;
							$myclass = new stdClass();

							$myclass->label = $linei->detail;
							$myclass->total = $resop;
							$myclass->format = 'partial';
							$this->aLine[]=$myclass;
						}
					}
				}
				else
				{
					$myclass = new stdClass();
					$myclass->label = '';
					$myclass->total = 0;

					$this->aLine[]=$myclass;
				}
				//echo '<pre>';
				//print_r($aSt);
				//echo '</pre>';
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
				$n++;
				$this->aSpread[$idr][$n]['total'][1]=$langs->trans('Total').' '.$lineh->detail;
				$this->aSpread[$idr][$n]['total'][7]=price2num($sumatot,$general->decimal_total);
				$n++;
				$myclass = new stdClass();
				$myclass->label = $langs->trans('Total').' '.$lineh->detail;
				$myclass->total = $sumatot;
				$myclass->format = 'total';

				$this->aLine[]=$myclass;
			}
		}
		//almacenamos en el item
		$aData = $_SESSION['cItem'][$idr];
		$suma = 0;

		//echo '<hr><pre>';
		//print_r($aData);
		//print_r($this->aSpread);
		//echo '</pre>';

		foreach ((array) $aData as $i => $value)
			$suma += $value;
		$_SESSION['sumaitem'][$idr]=price2num($suma,$general->decimal_total);
		$html.= '<tr class="total">';
		$html.= '<td colspan="5">&nbsp;</td>';
		$html.= '<td>'.$langs->trans('Total Precio Unitario').'</td>';
		$html.= '<td align="right">'.number_format($suma,$general->decimal_total).'</td>';
		$html.= '</tr>';
		$n++;
		$this->aSpread[$idr][$n]['totalf'][1]=$langs->trans('Total Precio Unitario');
		$this->aSpread[$idr][$n]['totalf'][7]=price2num($suma,$general->decimal_total);
		$n++;
		$myclass = new stdClass();
		$myclass->label = $langs->trans('Total Precio Unitario');
		$myclass->total = $suma;
		$myclass->format = 'total';
		$this->aLine[]=$myclass;
		$this->viewhtml = $html;

		if ($rep)
			return array($html,price2num($suma,$general->decimal_total));
		else
			return price2num($suma,$general->decimal_total);
	}

	/**
	 * Update object into database
	 *
	 * @param  User $user      User that modifies
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function update_unit_amount(User $user)
	{
		$error = 0;

		dol_syslog(__METHOD__, LOG_DEBUG);

		// Clean parameters

		if (isset($this->unit_amount)) {
			$this->unit_amount = trim($this->unit_amount);
		}
		if (isset($this->total_amount)) {
			$this->total_amount = trim($this->total_amount);
		}

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';

		$sql .= ' unit_amount = '.(isset($this->unit_amount)?$this->unit_amount:0).' ,';
		$sql .= ' total_amount = '.(isset($this->total_amount)?$this->total_amount:0).' ';
		$sql .= ' WHERE rowid=' . $this->id;

		$this->db->begin();

		$resql = $this->db->query($sql);
		// Commit or rollback
		if ($error) {
			$this->db->rollback();
			return - 1 * $error;
		} else {
			$this->db->commit();
			return 1;
		}
	}

	/**
	 * Update object into database
	 *
	 * @param  User $user      User that modifies
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function update_orderref()
	{
		$error = 0;

		dol_syslog(__METHOD__, LOG_DEBUG);

		// Clean parameters

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';
		//$sql.= " detail_close = 'qwrwqer', ";
		$sql .= ' order_ref = '.(isset($this->order_ref)?$this->order_ref:"null");

		$sql .= ' WHERE rowid=' . $this->id;

		//$this->db->begin();

		$resql = $this->db->query($sql);
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
		}

		if (!$error && !$notrigger) {
		}

		// Commit or rollback
		if ($error)
		{
			//$this->db->rollback();
			return - 1 * $error;
		} else {
			//$this->db->commit();
			return 1;
		}
	}

	/**
	 *  Load object in memory from database
	 *	for order_ref
	 *  @param	int		$id			Id object
	 *  @param	int		$ref		ref object
	 *  @return int 		        <0 if KO, >0 if OK
	 */
	public function get_ordertask($fk,$statut=0)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		global $langs;

		$sql = "SELECT ";
		$sql.= " t.rowid,";
		$sql.= " t.fk_statut, ";
		$sql.= " ta.order_ref ";
		$sql.= " FROM ".MAIN_DB_PREFIX."budget_task as t";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."budget_task_add AS ta ON ta.fk_budget_task = t.rowid ";
		//filtros
		$sql.= " WHERE t.fk_budget = ".$fk;
		if ($statut)
			$sql.= " AND t.fk_statut = ".$statut;
		//order
		$sql.= " ORDER BY ta.order_ref ASC ";
		$this->lines = array();
		//echo '<hr>sql '.$sql;
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			while ($obj = $this->db->fetch_object($resql))
			{
				$line = new stdClass();
				$line->id		= $obj->rowid;
				$line->fk_statut= $obj->fk_statut;
				$this->lines[]  = $line;
			}

			$this->db->free($resql);
			return $num;
		}
		else
		{
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
			return -1;
		}
	}
}

class BudgettaskaddLineext extends CommonObjectLine
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