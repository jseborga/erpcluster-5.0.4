<?php
/**
*  Return combo list of activated countries, into language of user
*
*  @param	string	$selected       Id or Code or Label of preselected country
*  @param  string	$htmlname       Name of html select object
*  @param  string	$htmloption     Options html on select object
*  @param	string	$maxlength		Max length for labels (0=no limit)
*  @return string           		HTML string with select
*/


function select_month($selected='',$htmlname='mes',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
{
	global $conf,$langs;

	$langs->load("almacen@almacen");

	$out='';
	$countryArray=array();
	$label=array();
	$i = 1;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('January');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('February');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('March');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('April');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('May');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('June');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('July');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('August');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('September');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('October');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('November');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('December');
	$label[$i] = $countryArray[$i]['rowid'];

	if ($showLabel)
		return $countryArray[$selected]['label'];
	$out = print_select($selected,$htmlname,$htmloption,$maxlength,
		$showempty,$showLabel,$countryArray,$label);

	return $out;
}


function select_currency($selected='',$htmlname='currency',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
{
	global $conf,$langs;

	$langs->load("almacen@almacen");

	$out='';
	$countryArray=array();
	$label=array();
	$i = 1;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Sus');
	$label[$i] = $countryArray[$i]['label'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Bs');
	$label[$i] = $countryArray[$i]['label'];
	if ($showLabel)
		return $countryArray[$selected]['label'];
	$out = print_select($selected,$htmlname,$htmloption,$maxlength,
		$showempty,$showLabel,$countryArray,$label);

	return $out;
}


function select_yesno($selected='',$htmlname='yesno',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
{
	global $conf,$langs;
	$langs->load("almacen@almacen");

	$out='';
	$countryArray=array();
	$label=array();
	$i = 1;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Yes');
	$label[$i] = $countryArray[$i]['label'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Not');
	$label[$i] = $countryArray[$i]['label'];

	if ($showLabel)
		return $countryArray[$selected]['label'];

	$out = print_select($selected,$htmlname,$htmloption,$maxlength,
		$showempty,$showLabel,$countryArray,$label);
	return $out;
}

function select_status($selected='',$htmlname='status',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
{
	global $conf,$langs;
	$langs->load("contab@contab");

	$out='';
	$countryArray=array();
	$label=array();
	$i = 1;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Active');
	$label[$i] = $countryArray[$i]['label'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Notactive');
	$label[$i] = $countryArray[$i]['label'];

	if ($showLabel)
		return $countryArray[$selected]['label'];

	$out = print_select($selected,$htmlname,$htmloption,$maxlength,
		$showempty,$showLabel,$countryArray,$label);

	return $out;
}


function print_select($selected='',$htmlname='status',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0,$countryArray,$label,$loked=0)
{
	if ($loked)
		$htmlloked = 'disabled="disabled"';
	$out.= '<select id="select'.$htmlname.'" class="flat selectpays" name="'.$htmlname.'" '.$htmloption.' '.$htmlloked.'>';
	if ($showempty)
	{
		$out.= '<option value="-1"';
		if ($selected == -1) $out.= ' selected="selected"';
		$out.= '>&nbsp;</option>';
	}

	array_multisort($label, SORT_ASC, $countryArray);

	foreach ($countryArray as $row)
	{
	  //print 'rr'.$selected.'-'.$row['label'].'-'.$row['code_iso'].'<br>';
		if ($selected && $selected != '-1' && ($selected == $row['rowid'] || $selected == $row['label']) )
		{
			$foundselected=true;
			$out.= '<option value="'.$row['rowid'].'" selected="selected">';
		}
		else
		{
			$out.= '<option value="'.$row['rowid'].'">';
		}
		$out.= dol_trunc($row['label'],$maxlength,'middle');
		$out.= '</option>';
	}
	$out.= '</select>';

	return $out;
}

/**
 *	Return label of statut generico /validate/no validate
 *
 *	@param		int		$state      	Id state
 *	@param      int		$mode        	0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
 *  @return     string					Label of statut
 */
function LibState($statut,$mode)
{
	global $langs;
  //print 'x'.$statut.'-'.$facturee;
	if ($mode == 0)
	{
		if ($statut==-1) return $langs->trans('StatusCanceled');
		if ($statut==0) return $langs->trans('StatusDraft');
		if ($statut==1) return $langs->trans('StatusValidated');
	}
	elseif ($mode == 1)
	{
		if ($statut==-1) return $langs->trans('StatusCanceled');
		if ($statut==0) return $langs->trans('StatusDraft');
		if ($statut==1) return $langs->trans('StatusValidated');
	}
	elseif ($mode == 2)
	{
		if ($statut==-1) return img_picto($langs->trans('StatusCanceled'),'statut5').' '.$langs->trans('StatusOrderCanceledShort');
		if ($statut==0) return img_picto($langs->trans('StatusDraft'),'statut0').' '.$langs->trans('StatusOrderDraftShort');
		if ($statut==1) return img_picto($langs->trans('StatusValidated'),'statut1').' '.$langs->trans('StatusOrderValidatedShort');
		if ($statut==2) return img_picto($langs->trans('StatusSent'),'statut3').' '.$langs->trans('StatusOrderSentShort');
		if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderToBill'),'statut7').' '.$langs->trans('StatusOrderToBillShort');
		if ($statut==3 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderProcessed'),'statut6').' '.$langs->trans('StatusOrderProcessedShort');
	}
	elseif ($mode == 3)
	{
		if ($statut==-1) return img_picto($langs->trans('StatusCanceled'),'statut5');
		if ($statut==0) return img_picto($langs->trans('StatusDraft'),'statut0');
		if ($statut==1) return img_picto($langs->trans('StatusValidated'),'statut1');
		if ($statut==2) return img_picto($langs->trans('StatusSentShort'),'statut3');
		if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderToBill'),'statut7');
		if ($statut==3 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderProcessed'),'statut6');
	}
	elseif ($mode == 4)
	{
		if ($statut==-1) return $langs->trans('StatusCanceled').' '.img_picto($langs->trans('StatusCanceled'),'statut5');
		if ($statut==0) return $langs->trans('StatusDraft').' '.img_picto($langs->trans('StatusDraft'),'interrog');
		if ($statut==1) return $langs->trans('StatusValidated').' '.img_picto($langs->trans('StatusValidated'),'tick');
	}
	elseif ($mode == 5)
	{
		if ($statut==-1) return $langs->trans('StatusCanceled').' '.img_picto($langs->trans('StatusCanceled'),'statut5');
		if ($statut==0) return $langs->trans('StatusDraft').' '.img_picto($langs->trans('StatusDraft'),'statut0');
		if ($statut==1) return $langs->trans('StatusValidated').' '.img_picto($langs->trans('StatusValidated'),'statut1');
	}
}

/**
	 *  Return combo list of activated countries, into language of user
	 *
	 *  @param  string  $selected       Id or Code or Label of preselected country
	 *  @param  string  $htmlname       Name of html select object
	 *  @param  string  $htmloption     Options html on select object
	 *  @param  string  $maxlength    Max length for labels (0=no limit)
	 *  @return string              HTML string with select
	 */
function select_type_mouvement($selected='',$htmlname='fk_type_mouvement',$htmloption='',$maxlength=0,$showempty=0)
{
	global $conf,$langs,$db;
	$langs->load("almacen@almacen");

	$out='';
	$countryArray=array();
	$label=array();

	$sql = "SELECT rowid, code as code_iso, label as label";
	$sql.= " FROM ".MAIN_DB_PREFIX."c_type_mouvement";
	$sql.= " WHERE active = 1";
	$sql.= " ORDER BY label ASC";
	//dol_syslog(get_class($this)."::select_type_mouvemen sql=".$sql);
	$resql=$db->query($sql);
	if ($resql)
	{
		$out.= '<select id="select'.$htmlname.'" class="flat selectpays" name="'.$htmlname.'" '.$htmloption.'>';
		if ($showempty)
		{
			$out.= '<option value="-1"';
			if ($selected == -1) $out.= ' selected="selected"';
			$out.= '>&nbsp;</option>';
		}

		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			$foundselected=false;

			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				$countryArray[$i]['rowid']    = $obj->rowid;
				$countryArray[$i]['code_iso']   = $obj->code_iso;
				$countryArray[$i]['label']    = ($obj->code_iso && $langs->transnoentitiesnoconv("Accounting".$obj->code_iso)!="Accounting".$obj->code_iso?$langs->transnoentitiesnoconv("Accounting".$obj->code_iso):($obj->label!='-'?$obj->label:''));
				$label[$i]  = $countryArray[$i]['label'];
				$i++;
			}

			array_multisort($label, SORT_ASC, $countryArray);

			foreach ($countryArray as $row)
			{
					//print 'rr'.$selected.'-'.$row['label'].'-'.$row['code_iso'].'<br>';
				if ($selected && $selected != '-1' && ($selected == $row['rowid'] || $selected == $row['code_iso'] || $selected == $row['label']) )
				{
					$foundselected=true;
					$out.= '<option value="'.$row['rowid'].'" selected="selected">';
				}
				else
				{
					$out.= '<option value="'.$row['rowid'].'">';
				}
				$out.= dol_trunc($row['label'],$maxlength,'middle');
				if ($row['code_iso']) $out.= ' ('.$row['code_iso'] . ')';
				$out.= '</option>';
			}
		}
		$out.= '</select>';
	}
	else
	{
		dol_print_error($db);
	}

	return $out;
}

/**
	 *  Return combo list of type entrepot, into language of user
	 *
	 *  @param  string  $selected       Id or Code or Label of preselected country
	 *  @param  string  $htmlname       Name of html select object
	 *  @param  string  $htmloption     Options html on select object
	 *  @param  string  $maxlength    Max length for labels (0=no limit)
	 *  @return string              HTML string with select
	 */
function select_type_entrepot($selected='',$htmlname='fk_type_entrepot',$htmloption='',$maxlength=0,$showempty=0,$showselect=0)
{
	global $conf,$langs,$db;
	$langs->load("almacen@almacen");

	$out='';
	$countryArray=array();
	$label=array();

	$sql = "SELECT rowid, code as code_iso, label as label";
	$sql.= " FROM ".MAIN_DB_PREFIX."c_type_entrepot";
	$sql.= " WHERE active = 1";
	$sql.= " ORDER BY label ASC";

	$resql=$db->query($sql);
	$array = array();
	if ($resql)
	{
		$out.= '<select id="select'.$htmlname.'" class="flat selectpays" name="'.$htmlname.'" '.$htmloption.'>';
		if ($showempty)
		{
			$out.= '<option value=""';
			if ($selected == -1) $out.= ' selected="selected"';
			$out.= '>&nbsp;</option>';
		}

		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			$foundselected=false;

			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				$array[$obj->rowid] = $obj->label;
				$countryArray[$i]['rowid']    = $obj->rowid;
				$countryArray[$i]['code_iso']   = $obj->code_iso;
				$countryArray[$i]['label']    = ($obj->code_iso && $langs->transnoentitiesnoconv("Accounting".$obj->code_iso)!="Accounting".$obj->code_iso?$langs->transnoentitiesnoconv("Accounting".$obj->code_iso):($obj->label!='-'?$obj->label:''));
				$label[$i]  = $countryArray[$i]['label'];
				$i++;
			}

			array_multisort($label, SORT_ASC, $countryArray);

			foreach ($countryArray as $row)
			{
					//print 'rr'.$selected.'-'.$row['label'].'-'.$row['code_iso'].'<br>';
				if ($selected && $selected != '-1' && ($selected == $row['rowid'] || $selected == $row['code_iso'] || $selected == $row['label']) )
				{
					$foundselected=true;
					$out.= '<option value="'.$row['rowid'].'" selected="selected">';
				}
				else
				{
					$out.= '<option value="'.$row['rowid'].'">';
				}
				$out.= dol_trunc($row['label'],$maxlength,'middle');
				if ($row['code_iso']) $out.= ' ('.$row['code_iso'] . ')';
				$out.= '</option>';
			}
		}
		$out.= '</select>';
	}
	else
	{
		dol_print_error($db);
	}
	if ($showselect)
		return $array[$selected];
	else
		return $out;
}

/**
	 *  Return combo list of activated countries, into language of user
	 *
	 *  @param  string  $selected       Id or Code or Label of preselected country
	 *  @param  string  $htmlname       Name of html select object
	 *  @param  string  $htmloption     Options html on select object
	 *  @param  string  $maxlength    Max length for labels (0=no limit)
	 *  @return string              HTML string with select
	 */
function get_type_mouvement($id,$code='')
{
	global $conf,$langs,$db;
	$langs->load("almacen@almacen");

	$out='';
	$countryArray=array();
	$label=array();

	$sql = "SELECT rowid, code as code_iso, label as label, type";
	$sql.= " FROM ".MAIN_DB_PREFIX."c_type_mouvement";
	if (!empty($code))
		$sql.= " WHERE code = '".$code."'";
	else
		$sql.= " WHERE rowid = ".$id;
	//dol_syslog(get_class($this)."::select_type_mouvemen sql=".$sql);
	$resql=$db->query($sql);

	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		if ($num>0)
		{
			$obj = $db->fetch_object($resql);
			return $obj;
		}
		return 0;
	}
	else
	{
		dol_print_error($db);
	}
	return -1;
}

function generarcodigoalm($longitud)
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

function saldoanterior($entrepotid,$fechaIni,$fk_product=0)
{
	global $db,$conf;
	//saldos anterior
	$sql  = "SELECT p.rowid, SUM(sm.value) AS saldo ";
	$sql.= " FROM ".MAIN_DB_PREFIX."stock_mouvement AS sm";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product AS p";
	$sql.= " ON sm.fk_product = p.rowid ";
	$sql.= " WHERE ";
	$sql.= " fk_entrepot = ".$entrepotid;
	if (! empty($fechaIni))
	{
		//$sql .= " AND UNIX_TIMESTAMP(sm.datem) <= ".$fechaIni;
		$sql .= " AND sm.datem <= ".$fechaIni;
	}
	if (!empty($fk_product))
		$sql.= " AND sm.fk_product = ".$fk_product;
	$sql.= " GROUP BY p.rowid";
	$result = $db->query($sql);
	if ($result)
	{
		$num = $db->num_rows($result);
		$i = 0;
		if ($num)
		{
			$var=True;
			while ($i < $num)
			{
					//actualizando totales
				$objp = $db->fetch_object($result);
				$var=!$var;
				$aSaldo[$objp->rowid] += $objp->saldo;
				$i++;
			}
		}
		else
		{
			$aSaldo = array();
		}
		$db->free($result);
	}
	else
	{
		dol_print_error($db);
	}
	return $aSaldo;
}

function saldorange($entrepotid,$fechaIni,$fechaFin,$fk_product=0)
{
	global $db, $conf;
		//creamos un array para operaciones de la fecha
	$aProductmouv =array();
		//movimiento de las fechas
	$sql  = "SELECT p.rowid, sm.type_mouvement, SUM(sm.value) AS saldo ";
	$sql.= " FROM ".MAIN_DB_PREFIX."stock_mouvement AS sm";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product AS p";
	$sql.= " ON sm.fk_product = p.rowid ";
	$sql.= " WHERE ";
	$sql.= " fk_entrepot = ".$entrepotid;
	if (! empty($fechaIni) && !empty($fechaFin))
	{
		//$sql .= " AND UNIX_TIMESTAMP(sm.datem) BETWEEN '".$fechaIni."' AND '".$fechaFin."'";
		$sql .= " AND sm.datem BETWEEN '".$fechaIni."' AND '".$fechaFin."'";
	}
	if (!empty($fk_product))
		$sql.= " AND sm.fk_product = ".$fk_product;
	$sql.= " GROUP BY p.rowid, sm.type_mouvement ";
	$result = $db->query($sql);
	if ($result)
	{
		$num = $db->num_rows($result);
		$i = 0;
		if ($num)
		{
			$var=True;
			while ($i < $num)
			{
					//actualizando totales
				$objp = $db->fetch_object($result);
				$aInvent[$objp->rowid][$objp->type_mouvement] += $objp->saldo;
				$i++;
			}
		}
		else
		{
			$aInvent = array();
		}
		$db->free($result);
	}
	else
	{
		dol_print_error($db);
	}
	return $aInvent;
}

//verifica que gestion y mes esta activa para registros nuevos
function verif_year($verif=false)
{
	global $langs,$conf,$db, $user;
	$_SESSION['lAlmacennew'] = true;

	if ($conf->global->ALMACEN_FILTER_YEAR)
	{
		require_once DOL_DOCUMENT_ROOT.'/almacen/class/contabperiodo.class.php';
		$periodo = new Contabperiodo($db);
		//$date = '01/01/2018';
		$date = dol_now();
		$aDate= dol_getdate(dol_stringtotime($date),1);
		$datehoy = dol_mktime(12,0,0,$aDate['mon'],$aDate['mday'],$aDate['year']);
		//variable valida
		$datehoy = dol_now();
		$aDate = dol_getdate($datehoy);
		$aPeriod = array();
		$aPeriodid=array();
		$year = 0;
		$filteryear.= " AND t.status_al = 1";
		$filteryear.= " AND t.period_year <= ".date('Y');
		$res = $periodo->fetchAll('ASC','period_year,period_month',0,0,array(1=>1),'AND',$filteryear);
		if ($res > 0)
		{
			$month = 0;
				//verificamos el mes minimo abierto
			foreach($periodo->lines AS $j => $line)
			{
				$aPeriod[$line->period_year][$line->period_month] = $line->status_al;
				$aPeriodid[$line->period_year][$line->period_month] = $line->id;
				$aPeriodyear[$line->period_year] = $line->status_al;
				$aPeriodyearid[$line->period_year] = $line->id;

				//$year = $line->period_year;
				if (empty($month))
				{
					$month = $line->period_month;
					$lineactual = $line;
				}
				else
				{
					if ($line->period_month < $month)
					{
						$month = $line->period_month;
						$lineactual = $line;
					}
				}
				if (empty($year))
				{
					$year = $line->period_year;
				}
				else
				{
					if ($line->period_year < $year)
					{
						$year = $line->period_year;
					}
				}

			}

			$_SESSION['period_month'] = $month;
			if (empty($_SESSION['period_year']))
				$_SESSION['period_year'] = $aDate['year'];
			else
			{
				if ($_SESSION['period_year'] != $year)
					$_SESSION['lAlmacennew'] = false;
			}

			//analizamos para las alertas de cierre periodo
			$nDayalert = $conf->global->ALMACEN_ALERT_DAY_NUMBER_CLOSE_PERIOD;
			if (empty($nDayalert)) $nDayalert = 5;

			if ($conf->global->ALMACEN_CLOSE_PERIOD == 1)
			{
				$dayClose = $line->date_fin;
				if ($verif)
				{
					if ($aDate['mon'] <> $month)
					{
						setEventMessages($langs->trans('Alerta: debe cerrar el periodo antes de continuar'),null,'errors');
						$_SESSION['lAlmacennew'] = false;
						if($user->rights->almacen->close->write)
						{
							//header('Location: '.DOL_URL_ROOT.'/almacen/closeperiod.php?action=create');
							//exit;

							if ($aPeriod[$_SESSION['period_year']][$_SESSION['period_month']]==1)
							{
								header('Location: '.DOL_URL_ROOT.'/almacen/closeperiod.php?action=create&fk_period='.$aPeriodid[$_SESSION['period_year']][$_SESSION['period_month']] );
							}
							else
							{
								if ($user->rights->almacen->gest->write)
								{
									header('Location: '.DOL_URL_ROOT.'/almacen/index.php?action=modify');
								}
								else
								{
									setEventMessages($langs->trans('Solicite permisos para cambiar gestión'),null,'warnings');
									header('Location: '.DOL_URL_ROOT.'/almacen/index.php');
								}
							}
							exit;
						}
					}
				}
			}
			else
			{
				$dayClose = dol_get_last_day($year,12);
				if ($aDate['year']!= $year)
				{
					if ($aPeriodyear[$_SESSION['period_year']]==1)
					{
						header('Location: '.DOL_URL_ROOT.'/almacen/closeperiod.php?action=create&fk_period='.$aPeriodyearid[$_SESSION['period_year']] );
					}
					else
					{
						if ($user->rights->almacen->gest->write)
						{
							header('Location: '.DOL_URL_ROOT.'/almacen/index.php?action=modify');
						}
						else
						{
							setEventMessages($langs->trans('Solicite permisos para cambiar gestión'),null,'warnings');
							header('Location: '.DOL_URL_ROOT.'/almacen/index.php');
						}
					}
				}
			}

			$nDayalert = $nDayalert * -1;
			$newdate = dol_time_plus_duree($dayClose, $nDayalert, 'd');
			//echo '|'.dol_print_date($datehoy,'dayhour') .' '.dol_print_date($newdate,'dayhour');
			$day = num_between_day($datehoy,$dayClose,  0);
			//echo ' '.dol_print_date($datehoy).' '.dol_print_date($dayClose);
			//echo 'day '.$day.' verif '.$verif;exit;
			if ($day >0)
			{
				if ($datehoy>=$newdate)
				{
					setEventMessages($langs->trans('Alerta, debe revisar sus pendientes para cierre de periodo').' '.$langs->trans('Faltan').' '.$day.' '.$langs->trans('Days'),null,'warnings');
				}
			}
			else
			{
				if ($verif)
				{
					setEventMessages($langs->trans('Alerta, debe cerrar el periodo antes de continuar'),null,'errors');
					$_SESSION['lAlmacennew'] = false;

					if($user->rights->almacen->close->write)
					{
						if ($aPeriod[$_SESSION['period_year']][$_SESSION['period_month']]==1)
						{
							header('Location: '.DOL_URL_ROOT.'/almacen/closeperiod.php?action=create&fk_period='.$aPeriodid[$_SESSION['period_year']][$_SESSION['period_month']] );
						}
						else
						{
							if ($user->rights->almacen->gest->write)
							{
								header('Location: '.DOL_URL_ROOT.'/almacen/index.php?action=modify');
							}
							else
							{
								setEventMessages($langs->trans('Solicite permisos para cambiar gestión'),null,'warnings');
								header('Location: '.DOL_URL_ROOT.'/almacen/index.php');
							}
						}
						exit;
					}
				}
			}
		}
		else
		{

			if (empty($_SESSION['period_year']))
			{
				header('Location: '.DOL_URL_ROOT.'/almacen/index.php?action=create');
				exit;
			}
			$_SESSION['lAlmacennew'] = false;
		}
	}
}

function solalmacen_prepare_head($object)
{
	global $langs, $conf;
	$langs->load('almacen');
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/almacen/fiche.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Fiche");
	$head[$h][2] = 'card';
	$h++;

	$head[$h][0] = dol_buildpath("/almacen/log.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Log");
	$head[$h][2] = 'log';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'solalmacen');

	return $head;
}
function transfer_prepare_head($object)
{
	global $langs, $conf;
	$langs->load('almacen');
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/almacen/transferencia/card.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Card");
	$head[$h][2] = 'card';
	$h++;

	$head[$h][0] = dol_buildpath("/almacen/transferencia/cardproduct.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Cardproduct");
	$head[$h][2] = 'cardproduct';
	$h++;

	//$head[$h][0] = dol_buildpath("/almacen/transferencia/log.php?id=".$object->id,1);
	//$head[$h][1] = $langs->trans("Log");
	//$head[$h][2] = 'log';
	//$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'transferalmacen');

	return $head;
}
function almacen_prepare_head($object)
{
	global $langs, $conf;
	$langs->load('almacen');
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/almacen/local/fiche.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Fiche");
	$head[$h][2] = 'card';
	$h++;

	$head[$h][0] = dol_buildpath("/almacen/permission/permission.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Permission");
	$head[$h][2] = 'Permission';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'almacen');

	return $head;
}


function verif_accessalm()
{
	global $db,$langs,$user;

	require_once DOL_DOCUMENT_ROOT.'/almacen/class/entrepotext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/almacen/class/entrepotuserext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentuserext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentext.class.php';

	$objentrepot = new Entrepotext($db);
	$objentrepotuser = new Entrepotuserext($db);
	$objDepartamentuser = new Pdepartamentuserext($db);
	$objDepartament = new Pdepartamentext($db);

	//recuperamos todos los almacenes
	$res = $objentrepot->fetchAll('','',0,0,array(1=>1),'AND'," AND t.statut = 1");
	if ($res >0)
	{
		foreach ($objentrepot->lines AS $j => $line)
		{
			$aEntrepot[$line->id] = $line->id;
		}
	}
	$filter = array(1=>1);
	$filterstatic = " AND t.fk_user = ".$user->id;
	$filterstatic.= " AND t.active = 1";
	$res = $objentrepotuser->fetchAll('','',0,0,$filter,'AND',$filterstatic,false);
	//$res = $objentrepot->getlistuser($user->id);
	//
	if ($res > 0)
	{
		$num = count($objentrepotuser->lines);
		$i = 0;
		$lines = $objentrepotuser->lines;
		foreach ($lines AS $i => $line)
		{
			if ($line->type == 1)
			{
				if (!empty($filteruser))$filteruser.= ',';
				$filteruser.= $line->fk_entrepot;
				$aFilterent[$line->fk_entrepot] = $line->fk_entrepot;
				//sacamos de la lista aEntrepot para excluidos
				unset($aEntrepot[$line->fk_entrepot]);
			}
			if ($line->typeapp==1)
			{
				if (!empty($filterusersol))$filterusersol.= ',';
				$filterusersol.= $line->fk_entrepot;
				$aFilterentsol[$line->fk_entrepot] = $line->fk_entrepot;
			}
		}
	}
	if (empty(count($aFilterent)))
	{
		//$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';
		//llxHeader("",$langs->trans("ApplicationsWarehouseCard"),$help_url);
		//print $mesg='<div class="error">'.$langs->trans("ErrorWharehousenotexist").'</div>';
		//exit;
	}
	$aAreadirect = array();
	//verificamos que departamentos puede ver
	$res = $objDepartamentuser->getuserarea($user->id,true);
	$res = $objDepartament->fetch($objDepartamentuser->fk_areaasign);
	//vemos el responsable directo del area
	$fk_user_resp = '';
	if ($res > 0) $fk_user_resp = $objDepartament->fk_user_resp;
	$filterarea = '';
	$aFilterarea = array();
	$fk_areaasign = 0;
	if ($res > 0)
	{
		$aAreadirect = $objDepartamentuser->aAreadirect;
		$fk_areaasign = $objDepartamentuser->fk_areaasign;
		foreach ($objDepartamentuser->aArea AS $j => $data)
		{
			if ($filterarea) $filterarea.= ',';
			$filterarea.= $j;
			$aFilterarea[$j]=$j;
		}
		//solo utilizamos a las areas directas asignadas
		$filterarea = '';
		foreach ((array) $aAreadirect AS $j)
		{
			if ($filterarea) $filterarea.= ',';
			$filterarea.= $j;
			$aFilterarea[$j] = $j;
		}
	}
	//verificamos que departamentos es responsable
	$filter = " AND t.fk_user_resp = ".$user->fk_member." AND t.active = 1 AND t.status = 1";
	$res = $objDepartament->fetchAll('','',0,0,array(1=>1),'AND',$filter);
	if ($res > 0)
	{
		foreach ($objDepartament->lines AS $j => $line)
			$aAreadirect[$line->id] = $line->id;
	}
	return array($aFilterent, $filteruser,$aFilterentsol, $filterusersol,$aAreadirect,$fk_areaasign,$filterarea,$aFilterarea, $fk_user_resp,$aEntrepot);
}

?>