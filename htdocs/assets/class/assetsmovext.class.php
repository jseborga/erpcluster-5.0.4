<?php
require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsmov.class.php';

class Assetsmovext extends Assetsmov
{
	var $array = array();

	public function process_depr($month,$year,$country='',$type_group='',$day=0, $fk_asset=0)
	{
		global $langs,$conf;
		require_once DOL_DOCUMENT_ROOT.'/assets/class/cassetsgroup.class.php';
		require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsbalanceext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/assets/lib/assets.lib.php';
		require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
		if ($conf->multicurren->enabled)
		{
			require_once DOL_DOCUMENT_ROOT.'/multicurren/class/cscurrencytypeext.class.php';
			require_once DOL_DOCUMENT_ROOT.'/multicurren/class/csindexescountryext.class.php';
			$csindexes = new Csindexescountryext($this->db);
		}
		$assets = new Assetsext($this->db);
		$objGroup = new Cassetsgroup($this->db);
		$assetsbalance = new Assetsbalanceext($this->db);
		$filterstatic = '';
		if ($fk_asset>0)
		{
			$filterstatic.= " AND t.rowid = ".$fk_asset;
		}
		else
		{
			if ($type_group)
			{
				$filterstatic = " AND t.type_group = '".$type_group."'";
			}
			$date_act = dol_now();
			$filterstatic.= " AND t.entity = ".$conf->entity;
			$filterstatic.= " AND t.statut >= 1";
			$filterstatic.= " AND t.date_active > 0";

		}

		$nb = $assets->fetchAll('ASC', 'rowid', 0, 0, array(1=>1), 'AND', $filterstatic);
		//variables construidas
		//$date_ini = dol_get_first_day($year,$month);
		$date_end = dol_get_last_day($year,$month);
		if ($day >0)
		{
			$date_end = dol_mktime(0,0,0,$month,$day,$year);
		}
		$date_end_real = $date_end;

		//recuperamos los parametros definidos en global
		$base_month = 30;
		$base_year = 365;
		if ($conf->global->ASSETS_DEPR_YEAR_COMERCIAL)
		{
			$base_year = 360;
			$base_month = 30;
		}
		if ($nb>0)
		{
			$lines = $assets->lines;
			foreach ($lines AS $j => $line)
			{
				if (empty($error))
				{
					$filtergroup = " AND t.code = '".$line->type_group."'";
					$resgroup = $objGroup->fetchAll('','',0,0,array(1=>1),'AND',$filtergroup,true);
					//echo '<hr>ref '.$line->ref;
					$lProcess = true;
					$lView = true;
					//primero vemos el tiempo de vida
					$useful_life = $line->useful_life;
					if ($line->status_reval ==1) $useful_life = $line->useful_life_reval;
					if (!$useful_life)
					{
					//recuperamos del grupo
					//$objGroup = fetch_group(0,$line->type_group);
						if ($resgroup==1) $useful_life = $objGroup->useful_life;
					//if (is_object($objgroup)) $useful_life = $objgroup->useful_life;
					}

					if ($conf->global->ASSETS_DEPR_DAY_CALC==0)
					{
						//anual
						$month_depr = $useful_life;
					}
					elseif ($conf->global->ASSETS_DEPR_DAY_CALC==1)
					{
						//mensual
						$month_depr = $useful_life * 12;
					}
					else
					{
						//diario
						$month_depr = ceil($useful_life * $base_year);
					}


					if (empty($useful_life) && $objGroup->depreciate)
					{
						$error++;
						setEventMessages($langs->trans('No esta definido la vida util'),null,'errors');
					}

					if ($useful_life>0)
						$percent = 100/$useful_life;
					else
						$percent = 0;
				//verificamos el saldo del activo en la tabla assetsbalance
					$resb = $assetsbalance->fetch(0,$line->id);
					$balance = $line->coste - $line->coste_residual;
					$balance = $line->coste;

					$date_active = $line->date_active;
					$date_active_initial = $line->date_active;
					$date_factor = $line->date_active;
				//$aDateActive = dol_getdate($date_active);
				//$date_factor = dol_get_first_day($aDateActive['year'],$aDateActive['mon']);
					$amount_depr_acum = 0;
				//revisar si corresponde aqui
					$aDateActive = dol_getdate($date_active);
					//if (empty($fk_asset))
						//$date_factor = dol_get_first_day($aDateActive['year'],$aDateActive['mon']);
					$lAddday =1;
					if ($resb>0 && empty($day))
					{
						$balance = $assetsbalance->amount_balance;
						$date_active = $assetsbalance->date_end;
						$date_factor = $assetsbalance->date_end;
						$amount_depr_acum = $assetsbalance->amount_balance_depr;
						$lAddday = 0;
					}
				//verificamos si corresponde depreciar por la fecha de activacion
					if ($line->date_active > $date_end) $lView = false;
					if ($line->status_reval ==1 && $line->date_reval > 0)
					{
						$date_active = $line->date_reval;
						if ($line->date_reval > $date_end) $lView = false;
					}


				//verificamos si tiene fecha baja
					if ($line->date_baja>0)
					{
						if ($line->date_baja < $date_end)
						{
							if ($line->date_baja > $date_active)
							{
								$date_end_real = $line->date_baja;
							}
							else
								$lView = false;
						}
					}
				//calculamos el tiempo para la depreciacion
				//echo '<hr>'.$line->id.' '.$line->ref;
				//echo '<br>'.dol_print_date($date_active,'day').' | '.dol_print_date($date_end_real,'day');
				//echo '<br>nro meses '.
				//$conf->global->ASSETS_DEPR_DAY_CALC=0 anual
				//$conf->global->ASSETS_DEPR_DAY_CALC=1 mensual
				//$conf->global->ASSETS_DEPR_DAY_CALC=2 diario
				//
					$number_month_depr = ($conf->global->ASSETS_DEPR_NUMBER_MONTH?$conf->global->ASSETS_DEPR_NUMBER_MONTH:30);
					if ($conf->global->ASSETS_DEPR_DAY_CALC==0)
					{
					//echo '<hr>resanual '.$date_active.', '.$date_end_real.', 1)/'.$base_year.'*1';
						//echo '<hr>resanual '.dol_print_date($date_active,'day').', '.dol_print_date($date_end_real,'day').', 1)/'.$base_year.'*1';
						if ($date_active && $date_end_real)
							$numdef = num_between_day($date_active, $date_end_real, 1);
						if ($conf->global->ASSETS_DEPR_YEAR_COMERCIAL  && $numdef>$base_year)
							$numdef = $base_year;
						$month_depr_actual = ceil($numdef/$base_year*1);
						//echo '<hr>numref '.$numdef.' /'.$base_year.' *1 '.$month_depr_actual;
					}
					elseif ($conf->global->ASSETS_DEPR_DAY_CALC==1)
					{
					//$month_depr_actual = ceil(num_between_day($date_active, $date_end_real, 1)/$number_month_depr);
					//echo '<hr>resmes '. $date_active.', '.$date_end_real.', 1)/'.$base_year.'*12';
						$month_depr_actual = round(num_between_day($date_active, $date_end_real, 1)/$base_year*12);
						$month_depr_actual = num_between_day($date_active, $date_end_real, 1)/$base_year*12;
					//echo '<br>newsindiv  '.$numdays = num_between_day($date_active, $date_end_real, 1);
					//echo '<br>new '.num_between_day($date_active, $date_end_real, 1)/$base_year*12;
					//echo '<br>fechas '.$month_depr_actual .dol_print_date($date_active,'day').', '.dol_print_date($date_end_real,'day').', 1)/';
						if ($month_depr_actual < 1) $month_depr_actual = 1;
						if ($month_depr_actual >= 1 && $month_depr_actual < 1.051) 	$month_depr_actual = 1;
						else $month_depr_actual = round($month_depr_actual);
					//echo '<hr>resultadofinalo '.$month_depr_actual;
					}
					else
					{
						$month_depr_actual = num_between_day($date_active, $date_end_real, $lAddday);
						$month_depr_total = num_between_day($date_active_initial, $date_end_real, 1);
						//echo '<hr>resultadodia '.$month_depr_actual.' '.$date_active.', '.$date_end_real.', 1)/';
						//echo '<br>fechas  '.dol_print_date($date_active,'day').', '.dol_print_date($date_end_real,'day').', 1)/'.dol_print_date($date_end);
					}
					//echo '<hr>mesesdepr '.$month_depr_actual;
					$lDateend = false;

					if ($month_depr_total > $month_depr)
					{
						$difmonth = $month_depr_total-$month_depr;
						//echo $difmonth.'> ' .$base_year;
						if ($difmonth < $base_year)
						{
							//echo dol_print_date($date_factor,'day').' '.$month_depr_actual;
							$month_depr_actual = $month_depr_actual - $difmonth;

							//echo '<hr>'. dol_print_date($date_factor,'day').' '.$month_depr_actual;
							$date_end_ = dol_time_plus_duree($date_factor, $month_depr_actual, 'd');
							//recorremos un dia anterior
							$aDateend = dol_getdate($date_end_);
							$aDatelast = dol_get_prev_day($aDateend['mday'],$aDateend['mon'],$aDateend['year']);
							$date_end_ = dol_mktime(0,0,0,$aDatelast['month'],$aDatelast['day'],$aDatelast['year']);
							//echo ' end '.dol_print_date($date_end_);
							$lDateend = true;
						}
						else
							$lProcess = false;
						setEventMessages($langs->trans('El activo').' '.$line->ref.' '.$langs->trans('Debe darse de baja o actualizar su valor en vista de que vencio su tiempo de vida util'),null,'warnings');
					}
				//si meses consumido es 0 no se procesa
				//if ($month_depr_actual == 0) $lView = false;
				//buscamos la moneda para el calculo de la actualización
					$tcini = 0;
					$tcend = 0;
					$factor = 0;
					if ($conf->multicurren->enabled)
					{
						//echo '<br>fechasini '.dol_print_date($date_factor,'day').' '.dol_print_date($date_end,'day').' '.$country.' |'.$line->id.'|';
						$rescs = $csindexes->fetch_last($country,$date_factor);
						if ($rescs>0)
							$tcini = $csindexes->amount;
						else
						{
						//echo '<br>fechas '.dol_print_date($date_factor,'day').' '.dol_print_date($date_end,'day').' '.$country.' |'.$line->id.'|';exit;
							$error++;
							setEventMessages($langs->trans('No existe registro de tipo de cambio para fecha ').' '.dol_print_date($date_factor,'day'),null,'errors');
						}
						//echo '<hr>dateend '.$lDateend.' '.dol_print_date($date_end_,'day');
						$rescs = $csindexes->fetch_last($country,($lDateend?$date_end_:$date_end));
						if ($rescs>0)
							$tcend = $csindexes->amount;
						else
						{
							$error++;
							setEventMessages($langs->trans('No existe registro de tipo de cambio para fecha ').' '.dol_print_date($date_end,'day'),null,'errors');
						}
					}
//echo $tcend;

					//echo '<hr>month_deprt '.$month_depr;
				//obtenemos el factor de actualizacion
					if ($tcini > 0) $factor = $tcend/$tcini - 1;
					if (!$tcini || !$tcend)
						setEventMessages($langs->trans('No existe uno de los factores').' '.$tcini.'|'.$tcend,null,'errors');
				//monto base actualizacion del periodo
					$amount_update = $balance * $factor;
				//monto base actualizado
					$balance_new = $balance + $amount_update;
				//actualizacion de la depreciacion acumulada
					$amount_depr_acum_update = $amount_depr_acum * $factor;
				//depreciacion del periodo
					$amount_depr = 0;
					if ($month_depr>0)
						$amount_depr = $balance_new / $month_depr * $month_depr_actual;

				//saldo actual depreciacion acumulada
					$balance_depr = $amount_depr_acum+$amount_depr_acum_update + $amount_depr;
					$balance = price2num($balance_new - $balance_depr,'MT');
					if ($balance == 0)
						$balance_depr -=1;
					if ($lView)
					{
						if ($lProcess)
						{
							$this->array[$line->id] = array(
								'ref'=> $line->ref,
								'type_group'=> $line->type_group,
								'detail' => $line->detail,
								'date_adq' => $line->date_adq,
								'date_active'=> $date_active,
								'date_end' => $date_end_real,
								'date_adq_d' => dol_print_date($line->date_adq,'day'),
								'date_active_d'=> dol_print_date($date_active,'day'),
								'date_end_d' => dol_print_date($date_end_real,'day'),
								'amount_balance' => ($objGroup->toupdate?$balance_new:$line->coste),
								'tcini' => $tcini,
								'tcend' => $tcend,
								'factor_update' => $factor,
								'useful_life'=>$useful_life,
								'percent'=> $percent,
								'coste'=> ($line->status_reval?$line->coste_reval:$line->coste),
								'coste_residual'=> ($line->status_reval==1?$line->coste_residual_reval:$line->coste_residual)+0,
								'amount_base' => $balance+0,
								'amount_update' => ($objGroup->toupdate?$amount_update:$balance),
								'amount_depr_acum_ant'=> ($objGroup->depreciate?$amount_depr_acum:0),
								'amount_depr_acum_update' => ($objGroup->depreciate?$amount_depr_acum_update:0),
								'month_depr' => ($objGroup->depreciate?($conf->global->ASSETS_DEPR_DAY_CALC==0?$month_depr*12:$month_depr):0),
								'time_consumed' => ($objGroup->depreciate?($conf->global->ASSETS_DEPR_DAY_CALC==0?$month_depr_actual*12:$month_depr_actual):0),
								'amount_depr' => ($objGroup->depreciate?$amount_depr:0),
								'amount_balance_depr' => ($objGroup->depreciate?$balance_depr:0),
								'movement_type'=>'DEPR',
								'depreciate'=>$objGroup->depreciate,
							);
						}
						else
						{
							$this->array[$line->id] = array(
								'ref'=> $line->ref,
								'type_group'=> $line->type_group,
								'detail' => $line->detail,
								'date_adq' => $line->date_adq,
								'date_active'=> $date_active,
								'date_end' => $date_end_real,
								'date_adq_d' => dol_print_date($line->date_adq,'day'),
								'date_active_d'=> dol_print_date($date_active,'day'),
								'date_end_d' => dol_print_date($date_end_real,'day'),
								'amount_balance' => $balance_new,
								'tcini' => $tcini,
								'tcend' => $tcend,
								'factor_update' => $factor,
								'useful_life'=>$useful_life,
								'percent'=> $percent,
								'coste'=> ($line->status_reval?$line->coste_reval:$line->coste),
								'coste_residual'=> ($line->status_reval==1?$line->coste_residual_reval:$line->coste_residual)+0,
								'amount_base' => $balance+0,
								'amount_update'=> ($objGroup->toupdate?($line->status_reval==1?$line->coste_residual_reval:$line->coste_residual)+0:0),
								'amount_depr_acum_ant'=> 0,
								'amount_depr_acum_update' => 0,
								'month_depr' => 0,
								'time_consumed' => $month_depr_actual,
								'amount_depr' => 0,
								'amount_balance_depr' => 0,
								'movement_type'=>'DEPR',
							);
						}
					}
				}
			}
		}
		//print_r($this->array);exit;
		if (!$error) return $nb;
		else return $error*-1;
	}
}
?>