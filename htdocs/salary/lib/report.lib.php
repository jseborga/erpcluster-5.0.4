<?php
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

/* Copyright (C) 2013 Ramiro Queso  <ramiro@ubuntu-bo.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 * or see http://www.gnu.org/
 */

/**
 *	\file       salary/lib/report.lib.php
 *	\brief      Ensemble de fonctions de base pour le module Report salary
 * 	\ingroup	salary
 */

  //function para la carga de todos los miembros con contrato

  //carga de miembros para el periodo dado
function s_cargamie($id=0,$fk_process=0)
{
	global $db,$conf,$langs,$objectsp,$object,$objectpe,$objectC,$user,$fk_period,$fk_proces,$fk_type_fol;
	$langs->load("salary@salary");

	if (!empty($fk_process)) $fk_proces = $fk_process;
	//agregamos el calculo del cas
	require_once DOL_DOCUMENT_ROOT.'/assistance/class/membercas.class.php';
	$objMembercas = new Membercas($db);

	$sql = "SELECT p.rowid, p.lastname AS lastname, p.firstname AS firstname, ";
	$sql.= " pu.lastnametwo AS lastnametwo, pu.docum, ";
	$sql.= " pt.date_ini, pt.date_fin, pt.basic, pt.fk_cc ";
	$sql.= " FROM ".MAIN_DB_PREFIX."adherent AS p ";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."p_user AS pu ON p.rowid = pu.fk_user ";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_contract AS pt ON pt.fk_user = p.rowid ";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_charge AS pc ON pc.rowid = pt.fk_charge ";
	$sql.= " WHERE p.entity = ".$conf->entity;
	$sql.= " AND pt.state = 1 ";
	if ($id) $sql.= " AND p.rowid = ".$id;
	$sql.= " AND pt.fk_proces =".$fk_proces;
	$sql.= " ORDER BY p.lastname, pu.lastnametwo, p.firstname ";

	$resql=$db->query($sql);
	$lRet = false;

	unset($_SESSION['aPlanilla']);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				$dif = 0;
				$lRet = true;
				//verificamos datos de si el empleado esta activo
				$lReg = True;
				$obj->date_ini = $db->jdate($obj->date_ini);
				$obj->date_fin = $db->jdate($obj->date_fin);
				$lFin = false;
				if (empty($obj->date_fin) ||is_null($obj->date_fin)) $lFin = true;
				if (!empty($obj->date_fin)) $dif = $objectpe->date_fin - $obj->date_fin;

				if ($dif<>0) $lFin = true;
				if (!empty($obj->date_fin) && $obj->date_fin < $objectpe->date_ini) $lFin = false;
				if ($lFin)
				{
					if ($obj->date_ini <= $objectpe->date_ini || ($obj->date_ini > $objectpe->date_ini && $obj->date_ini <= $objectpe->date_fin) )
					{
						//verificamos si existe registro del cass
						$filtercas = " AND t.fk_member = ".$obj->rowid." AND t.status = 1";
						$rescas = $objMembercas->fetchAll('','',0,0,array(1=>1),'AND',$filtercas,true);

						//calculamos los dias trabajados
						if ($obj->date_ini <= $objectpe->date_ini)
						{
							//verificamos que no tenga fecha fin
							if (empty($obj->date_fin) || is_null($obj->date_fin))
							{
								$nDiasTrab = $conf->global->SALARY_NRO_DIAS_LABORAL;
								$nTotalDias = $conf->global->SALARY_NRO_DIAS_LABORAL;
								$nTotalDias = totaldiastrab($obj->date_ini, $objectpe->date_fin);
							}
							else
							{
								if ($obj->date_fin <= $objectpe->date_fin)
								{
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
									$nDiasTrab = $conf->global->SALARY_NRO_DIAS_LABORAL;
									$nTotalDias = $conf->global->SALARY_NRO_DIAS_LABORAL;
									$nTotalDias = totaldiastrab($obj->date_ini, $objectpe->date_fin);
								}
							}
						}
						else if ($obj->date_ini > $objectpe->date_ini && $obj->date_ini <= $objectpe->date_fin)
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
						elseif ($obj->date_ini > $objectpe->date_ini)
						{
						}

						$nTotalAnio = $nTotalDias / 360;

						$_SESSION['aPlanilla'][$obj->rowid]['id']         	= $obj->rowid;
						$_SESSION['aPlanilla'][$obj->rowid]['sequen'] 		= $i+1;
						$_SESSION['aPlanilla'][$obj->rowid]['basic']      	= $obj->basic;
						$_SESSION['aPlanilla'][$obj->rowid]['docum']      	= $obj->docum;
						$_SESSION['aPlanilla'][$obj->rowid]['fk_cc'] 		= $obj->fk_cc;
						$_SESSION['aPlanilla'][$obj->rowid]['date_ini']   	= $obj->date_ini;
						$_SESSION['aPlanilla'][$obj->rowid]['date_fin']   	= $obj->date_fin;
						$_SESSION['aPlanilla'][$obj->rowid]['date_ini_p'] 	= $objectpe->date_ini;
						$_SESSION['aPlanilla'][$obj->rowid]['date_fin_p'] 	= $objectpe->date_fin;

						$_SESSION['aPlanilla'][$obj->rowid]['nDiasTrab'] 	+= $nDiasTrab;
						$_SESSION['aPlanilla'][$obj->rowid]['nTotalDias'] 	+= $nTotalDias;
						if ($rescas==1)
						{
							$nDaydef = ($conf->global->ASSISTANCE_NUMBER_DAY_FOR_GESTION?$conf->global->ASSISTANCE_NUMBER_DAY_FOR_GESTION:365);

							$nDaycas = $objMembercas->number_year*$nDaydef;
							$nDaycas+= $objMembercas->number_month*30;
							$nDaycas+= $objMembercas->number_day;
							$_SESSION['aPlanilla'][$obj->rowid]['nTotalCas'] 	= $nDaycas;
						}
					}
				}
				$i++;
			}
		}
	}

	return $lRet;
}

//movimientos de basico adicional
function s_cargamov()
{
	global $db,$conf,$langs,$objectsp,$object,$user,$fk_period,$fk_proces,$fk_type_fol;
	return;
	//recuperando proceso anterior
	$aPlanilla = $_SESSION['aPlanilla'];
	foreach($aPlanilla AS $idUser => $data)
	{
		$langs->load("salary@salary");
		$sql = "SELECT p.rowid, p.nom, p.prenom, pu.state, ";
		$sql.= " pu.date_ini, pu.date_fin, pu.basic ";
		$sql.= " FROM ".MAIN_DB_PREFIX."adherent AS p ";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_user AS pu ON p.rowid = pu.fk_user ";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_charge AS pc ON pc.rowid = pu.fk_charge ";
		$sql.= " WHERE p.entity = ".$conf->entity;
		$sql.= " AND pu.state = 1 ";
		$sql.= " AND pu.fk_proces =".$fk_proces;
		$sql.=" ORDER BY p.nom, p.prenom ";
		$resql=$db->query($sql);
		if ($resql)
		{
			$num = $db->num_rows($resql);
			$i = 0;
			$now = dol_now();
			if ($num)
			{
				while ($i < $num)
				{
					$obj = $db->fetch_object($resql);
					//verificamos datos de si el empleado esta activo
					$lReg = True;
					if (empty($obj->datafin) || (!empty($obj->date_fin) && $obj->date_fin > $objectpe->date_fin))
					{
						if (empty($fk_concept)) $fk_concept = 0;
						//buscamos si existe registro
						$result = $objectsp->fetch_salary_p($obj->rowid,
							$fk_period,
							$fk_proces,
							$fk_type_fol,
							$fk_concept,
							$state);
						if ($result <=0)
						{
							$objectsp->initAsSpecimen();
							$objectsp->entity = $conf->entity;
							$objectsp->fk_proces = $fk_proces;
							$objectsp->fk_type_fol = $fk_type_fol;
							$objectsp->fk_concept = $fk_concept;
							$objectsp->fk_period = $fk_period;
							$objectsp->fk_user = $obj->rowid;
							$objectsp->fk_cc = $data['fk_cc']+0;
							$objectsp->type = $obj->type_mov;
							$objectsp->cuota = 1;
							$objectsp->semana = 1;
							$objectsp->amount_inf = 0;
							$objectsp->amount = 0;
							$objectsp->hours_info = 0;
							$objectsp->hours = 0;
							$objectsp->date_reg = $now;
							$objectsp->date_create = $now;
							$objectsp->tms = $now;
							$objectsp->fk_user_create = $user->id;
							$objectsp->state = 0;
							$res  = $objectsp->create($user);
							$_SESSION['planilla'][$obj->rowid] = $obj->rowid;
						}
						else
						{
							$id = $objectsp->id;
							$objectsp->fetch($id);
							//actualizamos
							$objectsp->fk_cc = $dataUser['fk_cc']+0;
							$res = $objectsp->update($user);
							$_SESSION['planilla'][$obj->rowid] = $obj->rowid;
						}
					}
					$i++;
				}
			}
		}
	}
	return $lRet;
}


//movimientos de basico adicional
function s_calcbase()
{
	global $db,$conf,$langs,$objectsp,$object,
	$objectf,$objecto,$objectU,$objectCo,$user,
	$fk_period,$fk_proces,$fk_type_fol;
	$langs->load("salary@salary");
	$sql = "SELECT p.rowid, p.ref, p.detail, p.type_cod, p.type_mov, p.wage_inf, p.ref_formula, ";
	$sql.= " p.calc_oblig, p.calc_afp, p.calc_rciva, p.calc_agui, p.calc_vac, p.calc_indem, ";
	$sql.= " p.calc_afpvejez, p.calc_contrpat, p.calc_afpriesgo, p.calc_aportsol, p.calc_quin, ";
	$sql.= " p.print ";
	$sql.= " FROM ".MAIN_DB_PREFIX."p_concept AS p ";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_type_fol AS tf ";
	$sql.= " ON tf.rowid = p.fk_codfol ";
	$sql.= " WHERE tf.entity = ".$conf->entity;
	$sql.= " AND p.entity = ".$conf->entity;
	$sql.= " AND tf.state = 1 ";
	$sql.= " AND p.type_cod IN(3,4)";
	$sql.= " AND tf.rowid =".$fk_type_fol;
	$sql.=" ORDER BY p.ref ";
	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
	  //recuperando proceso anterior
		$aPlanilla = $_SESSION['aPlanilla'];
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				$fk_concept = $obj->rowid;
		  //verificamos datos de si el empleado esta activo
				$lReg = True;
				$ref_formula = $obj->ref_formula;
		  //$objectf->fetch_ref($ref_formula);
				//echo '<hr>concept '.$fk_concept.' '.$obj->detail.' typemov '.$obj->type_mov;
				//echo ' form. '.$ref_formula;
				if (!empty($ref_formula))
				{
					foreach ((array) $aPlanilla AS $idUser => $dataUser)
					{
						//buscamos si existe registro
						//echo '<br>idUser '.$idUser;
						$result = $objectsp->fetch_salary_p($idUser,
							$fk_period,
							$fk_proces,
							$fk_type_fol,
							$fk_concept,
							5);

						$calc_amount = 0;
						$calc_hour   = 0;
						$res = proc_formula($ref_formula,$idUser,$fk_period,false);
						//echo '<br>res '.$res;
						//echo '<br>type_mov '.$obj->type_mov;
						//echo '<br>result '.$result;
						//echo '<br>idpresent '.$objectsp->id;
						//echo '<br>wage_inf '.$obj->wage_inf.' '.$dataUser['nDiasTrab'];
						if ($res < 0) $res = 0;
						// exit;
						$res+=0;

						if ($obj->type_mov == 2)
						{
							//verificamos el tiempo trabajado
							$ndiastrab = formula_S_diastrab();
							$nRes        = $ndiastrab / $_SESSION['param']['nDiasTrab'];
							$calc_hour   = $dataUser['nDiasTrab'];
							$calc_amount = $res * $nRes;
						}
						elseif($obj->type_mov == 1 || $obj->type_mov == 3)
						{
							$calc_amount = $res+0;
							$calc_hour = $res+0;
						}


						if ($result <=0)
						{
							$objectsp->initAsSpecimen();
							$objectsp->entity = $conf->entity;
							$objectsp->fk_proces = $fk_proces;
							$objectsp->fk_type_fol = $fk_type_fol;
							$objectsp->fk_concept = $fk_concept;
							$objectsp->fk_period = $fk_period;
							$objectsp->fk_user = $idUser;
							$objectsp->fk_cc = $obj->cc+0;
							$objectsp->fk_cc = $dataUser['fk_cc']+0;
							$objectsp->type = $obj->type_mov;
							$objectsp->cuota = 1;
							$objectsp->semana = 1;
							$objectsp->amount_inf = 0;
							$objectsp->amount = $calc_amount;
							$objectsp->hours_info = 0;
							$objectsp->hours = $calc_hour;
							$objectsp->date_reg = date('Y-m-d');
							$objectsp->date_create = date('Y-m-d');
							$objectsp->fk_user_create = $user->id;
							$objectsp->state = 0;
							$res = $objectsp->create($user);
						}
						else
						{
							$id = $objectsp->id;
							$objectsp->fetch($id);
							//actualizamos
							$objectsp->fk_cc = $dataUser['fk_cc']+0;
							$objectsp->amount = $calc_amount;
							$objectsp->hours  = $calc_hour;
							$res = $objectsp->update($user);
						}
					}
				}
				//almacenando
				$i++;
			}
		}
	}

	return $lRet;
}

//movimientos de basico adicional
function s_calcall()
{
	global $db,$conf,$langs,$objectsp,$object,
	$objectf,$objecto,$objectU,$user,
	$fk_period,$fk_proces,$fk_type_fol;
	$langs->load("salary@salary");
	//recuperando proceso anterior
	$aPlanilla = $_SESSION['aPlanilla'];

	$sql = "SELECT p.rowid, p.ref, p.detail, p.type_cod, p.type_mov, p.wage_inf,p.ref_formula, ";
	$sql.= " p.calc_oblig, p.calc_afp, p.calc_rciva, p.calc_agui, p.calc_vac, p.calc_indem, ";
	$sql.= " p.calc_afpvejez, p.calc_contrpat, p.calc_afpriesgo, p.calc_aportsol, p.calc_quin, ";
	$sql.= " p.print ";
	$sql.= " FROM ".MAIN_DB_PREFIX."p_concept AS p ";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_type_fol AS tf ";
	$sql.= " ON tf.rowid = p.fk_codfol ";
	$sql.= " WHERE tf.entity = ".$conf->entity;
	$sql.= " AND p.entity = ".$conf->entity;
	$sql.= " AND tf.state = 1 ";
	$sql.= " AND p.type_cod IN (1,2,3,4)";
	$sql.= " AND tf.rowid =".$fk_type_fol;
	$sql.=" ORDER BY p.ref ";
	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				//verificamos datos de si el empleado esta activo
				$lReg = True;
				$ref_formula = $obj->ref_formula;
				if (!empty($ref_formula))
				{
					$fk_concept = $obj->rowid;
					foreach ((array) $aPlanilla AS $idUser => $dataUser)
					{
						//buscamos si existe registro
						$result = $objectsp->fetch_salary_p($idUser,
							$fk_period,
							$fk_proces,
							$fk_type_fol,
							$fk_concept,
							5);

						$calc_amount = 0;
						$calc_hour   = 0;
						$res = proc_formula($ref_formula,$idUser,$fk_period,false);
						if ($obj->type_cod == 3 || $obj->type_cod == 4)
						{
							//calbase
							if ($res < 0)
								$res = 0;
							$res+=0;
							if ($obj->type_mov == 2)
							{
								$calc_amount = $res + 0;
							}
							elseif($obj->type_mov == 1 || $obj->type_mov == 3)
								$calc_hour = $res+0;
						}
						if ($obj->type_cod == 1)
						{
							//calrend
							if ($obj->type_mov == 2)
							{
								$nRes = $res / $_SESSION['param']['nDiasTrab'];
								$calc_hour   = $dataUser['nDiasTrab'];
								$calc_amount = $nRes * $dataUser['nDiasTrab'];
							}
							elseif($obj->type_mov == 1)
								$calc_hour = $res+0;
						}

						if ($obj->type_cod == 2)
						{
							//caldesc
							if ($obj->type_mov == 2)
								$calc_amount = $res+0;
							elseif($obj->type_mov == 1)
								$calc_hour = $res+0;
						}
						if ($result <=0)
						{
							$objectsp->initAsSpecimen();
							$objectsp->entity      = $conf->entity;
							$objectsp->fk_proces   = $fk_proces;
							$objectsp->fk_type_fol = $fk_type_fol;
							$objectsp->fk_concept  = $fk_concept;
							$objectsp->fk_period   = $fk_period;
							$objectsp->fk_user     = $idUser;
							$objectsp->fk_cc       = $obj->cc+0;
							$objectsp->fk_cc = $dataUser['fk_cc']+0;
							$objectsp->type        = $obj->type_mov;
							$objectsp->cuota       = 1;
							$objectsp->semana      = 1;
							$objectsp->amount_inf  = 0;
							$objectsp->amount      = $calc_amount;
							$objectsp->hours_info  = 0;
							$objectsp->hours       = $calc_hour;
							$objectsp->date_reg    = dol_now();
							$objectsp->date_create = dol_now();
							$objectsp->fk_user_create = $user->id;
							$objectsp->state = 0;
							$objectsp->create($user);
						}
						else
						{
							$id = $objectsp->id;
							$objectsp->fetch($id);
							  //actualizamos
							$objectsp->fk_cc = $dataUser['fk_cc']+0;
							if ($obj->type_cod == 3 || $obj->type_cod == 4)
							{
								$objectsp->amount = $calc_amount;
								$objectsp->hours  = $calc_hour;
							}
							if ($obj->type_cod == 1 || $obj->type_cod == 2)
							{
								$objectsp->amount = $objectsp->amount_inf + $calc_amount;
								$objectsp->hours  = $objectsp->hours_info + $calc_hour;
							}
							$objectsp->update($user);
						}
					}
				}
				$i++;
			}
		}
	}
	return $lRet;
}


//movimientos de basico adicional
function s_calcrend()
{
	global $db,$conf,$langs,$objectsp,$object,$objectf,$objecto,$objectU,$user,$fk_period,$fk_proces,$fk_type_fol;
	$langs->load("salary@salary");

  //recuperando proceso anterior
	$aPlanilla = $_SESSION['aPlanilla'];

	$sql = "SELECT p.rowid, p.ref, p.detail, p.type_cod, p.type_mov, p.ref_formula, ";
	$sql.= " p.calc_oblig, p.calc_afp, p.calc_rciva, p.calc_agui, p.calc_vac, p.calc_indem, ";
	$sql.= " p.calc_afpvejez, p.calc_contrpat, p.calc_afpriesgo, p.calc_aportsol, p.calc_quin, ";
	$sql.= " p.print ";
	$sql.= " FROM ".MAIN_DB_PREFIX."p_concept AS p ";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_type_fol AS tf ";
	$sql.= " ON tf.rowid = p.fk_codfol ";
	$sql.= " WHERE tf.entity = ".$conf->entity;
	$sql.= " AND p.entity = ".$conf->entity;
	$sql.= " AND tf.state = 1 ";
	$sql.= " AND p.type_cod IN (1,2)";
	$sql.= " AND tf.rowid =".$fk_type_fol;
	$sql.=" ORDER BY p.ref ";
	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				//verificamos datos de si el empleado esta activo
				$fk_concept = $obj->rowid;
				$lReg = True;
				$ref_formula = $obj->ref_formula;

				if (!empty($ref_formula))
				{
					foreach ((array) $aPlanilla AS $idUser => $dataUser)
					{
						//buscamos si existe registro
						$result = $objectsp->fetch_salary_p($idUser,
							$fk_period,
							$fk_proces,
							$fk_type_fol,
							$fk_concept,
							5);

						$calc_amount = 0;
						$calc_hour   = 0;
						$res = proc_formula($ref_formula,$idUser,$fk_period,false);

						if ($obj->type_mov == 2)
						{
							$calc_amount = $res+0;
						}
						elseif($obj->type_mov == 1 || $obj->type_mov == 3)
						{
							$calc_amount = $res+0;
							$calc_hour = $res+0;
						}
						if ($result <=0)
						{
							$objectsp->initAsSpecimen();
							$objectsp->entity      = $conf->entity;
							$objectsp->fk_proces   = $fk_proces;
							$objectsp->fk_type_fol = $fk_type_fol;
							$objectsp->fk_concept  = $fk_concept;
							$objectsp->fk_period   = $fk_period;
							$objectsp->fk_user     = $idUser;
							$objectsp->fk_cc       = $obj->cc+0;
							$objectsp->fk_cc       = $dataUser['fk_cc']+0;
							$objectsp->type        = $obj->type_mov;
							$objectsp->cuota       = 1;
							$objectsp->semana      = 1;
							$objectsp->amount_inf  = 0;
							$objectsp->amount      = $calc_amount;
							$objectsp->hours_info  = 0;
							$objectsp->hours       = $calc_hour;
							$objectsp->date_reg    = dol_now();
							$objectsp->date_create = dol_now();
							$objectsp->fk_user_create = $user->id;
							$objectsp->state = 0;
							$objectsp->create($user);
						}
						else
						{

							$id = $objectsp->id;
							$objectsp->fetch($id);
							//actualizamos
							$objectsp->fk_cc       = $dataUser['fk_cc']+0;
							$objectsp->amount = $objectsp->amount_inf + $calc_amount;
							$objectsp->hours  = $objectsp->hours_info + $calc_hour;

							$objectsp->update($user);
						}
					}
				}
				$i++;
			}
		}
	}
	return $lRet;
}

//movimientos de basico adicional
function s_calcdesc()
{
	global $db,$conf,$langs,$objectsp,$object,$objectf,$objecto,$objectU,$user,$fk_period,$fk_proces,$fk_type_fol;
	$langs->load("salary@salary");
  //recuperando proceso anterior
	$aPlanilla = $_SESSION['aPlanilla'];

	$sql = "SELECT p.rowid, p.ref, p.detail, p.type_cod, p.type_mov, p.ref_formula, ";
	$sql.= " p.calc_oblig, p.calc_afp, p.calc_rciva, p.calc_agui, p.calc_vac, p.calc_indem, ";
	$sql.= " p.calc_afpvejez, p.calc_contrpat, p.calc_afpriesgo, p.calc_aportsol, p.calc_quin, ";
	$sql.= " p.print ";
	$sql.= " FROM ".MAIN_DB_PREFIX."p_concept AS p ";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_type_fol AS tf ";
	$sql.= " ON tf.rowid = p.fk_codfol ";
	$sql.= " WHERE tf.entity = ".$conf->entity;
	$sql.= " AND p.entity = ".$conf->entity;
	$sql.= " AND tf.state = 1 ";
	$sql.= " AND p.type_cod = 2";
	$sql.= " AND tf.rowid =".$fk_type_fol;
	$sql.=" ORDER BY p.ref ";
	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				//verificamos datos de si el empleado esta activo
				$fk_concept = $obj->rowid;
				$lReg = True;
				$ref_formula = $obj->ref_formula;
				if (!empty($ref_formula))
				{
					foreach ((array) $aPlanilla AS $idUser => $dataUser)
					{
						//buscamos si existe registro
						$result = $objectsp->fetch_salary_p($idUser,
							$fk_period,
							$fk_proces,
							$fk_type_fol,
							$fk_concept,
							5);

						$calc_amount = 0;
						$calc_hour   = 0;
						$res = proc_formula($ref_formula,$idUser,$fk_period,false);

						if ($obj->type_mov == 2)
						{
							$calc_amount = $res+0;
						}
						elseif($obj->type_mov == 1 || $obj->type_mov == 3)
						{
							$calc_amount = $res+0;
							$calc_hour = $res+0;
						}

						if ($result <=0)
						{
							$objectsp->initAsSpecimen();
							$objectsp->entity      = $conf->entity;
							$objectsp->fk_proces   = $fk_proces;
							$objectsp->fk_type_fol = $fk_type_fol;
							$objectsp->fk_concept  = $fk_concept;
							$objectsp->fk_period   = $fk_period;
							$objectsp->fk_user     = $idUser;
							$objectsp->fk_cc 	 = $obj->cc+0;
							$objectsp->fk_cc = $dataUser['fk_cc']+0;
							$objectsp->type        = $obj->type_mov;
							$objectsp->cuota       = 1;
							$objectsp->semana      = 1;
							$objectsp->amount_inf  = 0;
							$objectsp->amount      = $calc_amount;
							$objectsp->hours_info  = 0;
							$objectsp->hours       = $calc_hour;
							$objectsp->date_reg    = date('Y-m-d');
							$objectsp->date_create = date('Y-m-d');
							$objectsp->fk_user_create = $user->id;
							$objectsp->state = 0;
							$objectsp->create($user);
						}
						else
						{
							$id = $objectsp->id;
							$objectsp->fetch($id);
							//actualizamos
							$objectsp->fk_cc = $dataUser['fk_cc']+0;
							$objectsp->amount = $objectsp->amount_inf + $calc_amount;
							$objectsp->hours  = $objectsp->hours_info + $calc_hour;
							$objectsp->update($user);
						}
					}
				}
				$i++;
			}
		}
	}
	return $lRet;
}

//bono antiguedad
function s_calbonan()
{
	global $db,$conf,$langs,$objectsp,$object,$objectf,$objecto,$objectU,$user,$fk_period,$fk_proces,$fk_type_fol;
	$langs->load("salary@salary");
	return;
  // //calculo del valor base
  // $cFormula = 'formulaS005';
  // //eval('$res = '.$cFormula.';');
  // $numDiasTrab = formuladiastrab();
  // $numAnioTrab = $numDiasTrab / 360;
  // calc_generic();



  // $proc_formula($idFormula,$idUser,$fk_period,$impres=False);

	$sql = "SELECT p.rowid, p.ref, p.detail, p.type_cod, p.type_mov, p.ref_formula, ";
	$sql.= " p.calc_oblig, p.calc_afp, p.calc_rciva, p.calc_agui, p.calc_vac, p.calc_indem, ";
	$sql.= " p.calc_afpvejez, p.calc_contrpat, p.calc_afpriesgo, p.calc_aportsol, p.calc_quin, ";
	$sql.= " p.print ";
	$sql.= " FROM ".MAIN_DB_PREFIX."p_concept AS p ";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_type_fol AS tf ";
	$sql.= " ON tf.rowid = p.fk_codfol ";
	$sql.= " WHERE tf.entity = ".$conf->entity;
	$sql.= " AND tf.state = 1 ";
	$sql.= " AND p.type_cod IN(3,4)";
	$sql.= " AND tf.rowid =".$fk_type_fol;
	$sql.=" ORDER BY p.ref ";

	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
	  //recuperando proceso anterior
		$aPlanilla = $_SESSION['aPlanilla'];
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				//verificamos datos de si el empleado esta activo
				$lReg = True;
				$ref_formula = $obj->ref_formula;
				//$objectf->fetch($fk_formula);
				if (!empty($ref_formula))
				{
					$fk_concept = $obj->rowid;
					foreach ((array) $aPlanilla AS $idUser => $dataUser)
					{
						//buscamos si existe registro
						$result = $objectsp->fetch_salary_p($idUser,
							$fk_period,
							$fk_proces,
							$fk_type_fol,
							$fk_concept,
							5);
						$calc_amount = 0;
						$calc_hour   = 0;
						$res = proc_formula($ref_formula,$idUser,$fk_period,false);
						$res+=0;
						if ($obj->type_mov == 2)
						{
							$nRes = $res / $_SESSION['param']['nDiasTrab'];
							$calc_hour   = $dataUser['nDiasTrab'];
							$calc_amount = $nRes * $dataUser['nDiasTrab'];
						}
						elseif($obj->type_mov == 1)
							$calc_hour = $res+0;
						if ($result <=0)
						{
							$objectsp->initAsSpecimen();
							$objectsp->entity = $conf->entity;
							$objectsp->fk_proces = $fk_proces;
							$objectsp->fk_type_fol = $fk_type_fol;
							$objectsp->fk_concept = $fk_concept;
							$objectsp->fk_period = $fk_period;
							$objectsp->fk_user = $idUser;
							$objectsp->fk_cc = $obj->cc+0;
							$objectsp->fk_cc = $dataUser['fk_cc']+0;
							$objectsp->type = $obj->type_mov;
							$objectsp->cuota = 1;
							$objectsp->semana = 1;
							$objectsp->amount_inf = 0;
							$objectsp->amount = $calc_amount;
							$objectsp->hours_info = 0;
							$objectsp->hours = $calc_hour;
							$objectsp->date_reg = date('Y-m-d');
							$objectsp->date_create = date('Y-m-d');
							$objectsp->fk_user_create = $user->id;
							$objectsp->state = 0;
							$objectsp->create($user);
						}
						else
						{
							$id = $objectsp->id;
							$objectsp->fetch($id);
							//actualizamos
							$objectsp->fk_cc = $dataUser['fk_cc']+0;
							$objectsp->amount = $calc_amount;
							$objectsp->hours  = $calc_hour;
							$objectsp->update($user);
						}
					}
				}
				//almacenando
				$i++;
			}
		}
	}
	return $lRet;
}


function search_planilla($idUser,$ref_formula,$fk_period,$fk_proces,$fk_type_fol,$nNumber=5,$lPeriodClose=false)
{
	global $db,$conf,$objectsp,$objectsh;
	$sql = "SELECT rowid AS id ";
	$sql.= " FROM ".MAIN_DB_PREFIX."p_concept ";
	$sql.= " WHERE ref_formula = '".$ref_formula."' ";
	$sql.= " AND entity = ".$conf->entity;

	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$objconcept = $db->fetch_object($resql);
				$fk_concept = $objconcept->id;
				$i++;
			}
		}
	}
	if ($fk_concept)
	{
		if (!$lPeriodClose)
		{
			$result = $objectsp->fetch_salary_p($idUser,
				$fk_period,
				$fk_proces,
				$fk_type_fol,
				$fk_concept,
				$nNumber);
			return $objectsp;
		}
		else
		{
			$result = $objectsh->fetch_salary_p($idUser,
				$fk_period,
				$fk_proces,
				$fk_type_fol,
				$fk_concept,
				$nNumber);
			return $objectsh;
		}
	}
	return array();
}

function month_literal($mes)
{
	if ($mes == 1)
		$cMes = 'Enero';
	elseif ($mes == 2)
		$cMes = 'Febrero';
	elseif ($mes == 3)
		$cMes = 'Marzo';
	elseif ($mes == 4)
		$cMes = 'Abril';
	elseif ($mes == 5)
		$cMes = 'Mayo';
	elseif ($mes == 6)
		$cMes = 'Junio';
	elseif ($mes == 7)
		$cMes = 'Julio';
	elseif ($mes == 8)
		$cMes = 'Agosto';
	elseif ($mes == 9)
		$cMes = 'Septiembre';
	elseif ($mes == 10)
		$cMes = 'Octubre';
	elseif ($mes == 11)
		$cMes = 'Noviembre';
	else
		$cMes = 'Diciembre';
	return $cMes;
}

function registry_end($fk_period, $fk_proces,$fk_type_fol,$state)
{
	global $conf,$user,$lang,$db,$objectsh,$objectsp;
	$lOk = false;
	//verificamos el estado
	$sql = "SELECT";
	$sql.= " t.rowid,";

	$sql.= " t.entity,";
	$sql.= " t.fk_proces,";
	$sql.= " t.fk_type_fol,";
	$sql.= " t.fk_concept,";
	$sql.= " t.fk_period,";
	$sql.= " t.fk_user,";
	$sql.= " t.fk_cc,";
	$sql.= " t.sequen,";
	$sql.= " t.type,";
	$sql.= " t.cuota,";
	$sql.= " t.semana,";
	$sql.= " t.amount_inf,";
	$sql.= " t.amount,";
	$sql.= " t.hours_info,";
	$sql.= " t.hours,";
	$sql.= " t.date_reg,";
	$sql.= " t.date_create,";
	$sql.= " t.date_mod,";
	$sql.= " t.fk_user_create,";
	$sql.= " t.fk_user_mod,";
	$sql.= " t.state";

	$sql.= " FROM ".MAIN_DB_PREFIX."p_salary_present AS t WHERE ";
	$sql.= " fk_period = ".$fk_period." AND ";
	$sql.= " fk_proces = ".$fk_proces." AND ";
	$sql.= " fk_type_fol = ".$fk_type_fol." AND ";
	$sql.= " state = ".$state;

	$resql = $db->query($sql);
	if ($resql)
	{
		$now = dol_now();
		$db->begin();
		$num = $db->num_rows($resql);
		if ($num > 0)
		{
			$i = 0;
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				//buscamos si existe el registro en history
				$objectsh->fetch_salary_p($obj->fk_user,$fk_period,$fk_proces,$fk_type_fol,$obj->fk_concept,$state);
				if ($objectsh->fk_user == $obj->fk_user &&
					$objectsh->fk_period == $obj->fk_period &&
					$objectsh->fk_proces == $obj->fk_proces &&
					$objectsh->fk_type_fol == $obj->fk_type_fol &&
					$objectsh->fk_concept == $obj->fk_concept)
				{
					//actualizacion
					$objectsh->entity = $conf->entity;
					$objectsh->fk_salary_present = $obj->fk_salary_present;
					$objectsh->fk_proces = $fk_proces;
					$objectsh->fk_type_fol = $fk_type_fol;
					$objectsh->fk_concept = $obj->fk_concept;
					$objectsh->fk_period = $fk_period;
					$objectsh->fk_user = $obj->fk_user;
					$objectsh->fk_cc = $obj->cc+0;
					$objectsh->sequen = $obj->sequen+0;
					$objectsh->type = $obj->type;
					//revisar
					$objectsh->cuota = $obj->cuota;
					$objectsh->semana = $obj->semana;
					$objectsh->amount_inf = $obj->amount_inf;
					$objectsh->amount = $obj->amount;
					$objectsh->hours_info = $obj->hours_info;
					$objectsh->fk_account = $obj->fk_account +0;
					$objectsh->payment_state = $obj->payment_state +0;
					$objectsh->hours = $obj->hours;
					$objectsh->date_reg = $obj->data_reg;
					//$objectsh->date_create = $now;
					$objectsh->date_mod = $now;
					//$objectsh->fk_user_create = $user->id;
					$objectsh->fk_user_mod = $user->id;
					$objectsh->state = $obj->state;
					$res = $objectsh->update($user);
					if ($res <=0) $error++;
				}
				else
				{
					//registro nuevo
					$objectsh->initAsSpecimen();
					$objectsh->entity             = $conf->entity;
					$objectsh->fk_salary_present  = $obj->rowid;
					$objectsh->fk_proces   	= $fk_proces;
					$objectsh->fk_type_fol 	= $fk_type_fol;
					$objectsh->fk_concept  	= $obj->fk_concept;
					$objectsh->fk_period   	= $fk_period;
					$objectsh->fk_user     	= $obj->fk_user;
					$objectsh->fk_cc  		= $obj->cc+0;
					$objectsh->sequen  		= $obj->sequen+0;
					$objectsh->type   		= $obj->type;
					$objectsh->fk_account   = $obj->fk_account + 0;
					$objectsh->payment_state = $obj->payment_state +0;
					//revisar
					$objectsh->cuota  		= $obj->cuota;
					$objectsh->semana 		= $obj->semana;
					$objectsh->amount_inf 	= $obj->amount_inf;
					$objectsh->amount     	= $obj->amount;
					$objectsh->hours_info 	= $obj->hours_info;
					$objectsh->hours      	= $obj->hours;
					$objectsh->date_reg   	= $obj->date_reg;
					$objectsh->date_create 	= $now;
					$objectsh->date_mod    	= $now;
					$objectsh->fk_user_create 	= $user->id;
					$objectsh->fk_user_mod 	= $user->id;
					$objectsh->state       	= $obj->state;
					$res = $objectsh->create($user);
					if ($res <= 0) $error++;
				}
				$i++;
			}
		}
		if (empty($error))
		{
			$lOk = true;
			$db->commit();
		}
		else
		{
			$lOk = false;
			$db->rollback();
		}
	}
	else
	{
		return false;
	}
	exit;
	return $lOk;
}


//action final
function s_calcula($ref_concept,$aPlanilla)
{
	global $db,$conf,$langs,$objectsp,$object,$objectf,$objecto,$objectU,$objectCo,$user,$fk_period,$fk_proces,$fk_type_fol;
	$langs->load("salary");

	$sql = "SELECT p.rowid, p.ref, p.detail, p.type_cod, p.type_mov, p.wage_inf, p.ref_formula, ";
	$sql.= " p.calc_oblig, p.calc_afp, p.calc_rciva, p.calc_agui, p.calc_vac, p.calc_indem, ";
	$sql.= " p.calc_afpvejez, p.calc_contrpat, p.calc_afpriesgo, p.calc_aportsol, p.calc_quin, ";
	$sql.= " p.print ";
	$sql.= " FROM ".MAIN_DB_PREFIX."p_concept AS p ";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_type_fol AS tf ";
	$sql.= " ON tf.rowid = p.fk_codfol ";
	$sql.= " WHERE tf.entity = ".$conf->entity;
	$sql.= " AND p.entity = ".$conf->entity;
	$sql.= " AND tf.state = 1 ";
	//$sql.= " AND p.type_cod IN(3,4)";
	$sql.= " AND tf.rowid =".$fk_type_fol;
	$sql.= " AND p.ref = '".$ref_concept."'";
	//$sql.=" ORDER BY p.ref ";

	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		//recuperando proceso anterior
		$aPlanilla = $_SESSION['aPlanilla'];
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				$fk_concept = $obj->rowid;
				//verificamos datos de si el empleado esta activo
				$lReg = True;
				$ref_formula = $obj->ref_formula;
				if (!empty($ref_formula))
				{
					foreach ((array) $aPlanilla AS $idUser => $dataUser)
					{
						//buscamos si existe registro
						$result = $objectsp->fetch_salary_p($idUser,
							$fk_period,
							$fk_proces,
							$fk_type_fol,
							$fk_concept,
							5);
						$calc_amount = 0;
						$calc_hour   = 0;
						$res = proc_formula($ref_formula,$idUser,$fk_period,false);
						if ($res < 0) $res = 0;
						$res+=0;
						$calc_amount = $res+0;
						$calc_hour = $res+0;
						$now = dol_now();
						if ($result <=0)
						{
							$objectsp->initAsSpecimen();
							$objectsp->entity      	= $conf->entity;
							$objectsp->fk_proces   	= $fk_proces;
							$objectsp->fk_type_fol 	= $fk_type_fol;
							$objectsp->fk_concept  	= $fk_concept;
							$objectsp->fk_period   	= $fk_period;
							$objectsp->fk_user     	= $idUser;
							$objectsp->fk_cc       	= $obj->cc+0;
							$objectsp->fk_cc = $dataUser['fk_cc']+0;
							$objectsp->sequen 		= $_SESSION['aPlanilla'][$idUser]['sequen'];
							$objectsp->type   	 	= $obj->type_mov;
							$objectsp->cuota  	 	= 1;
							$objectsp->semana 	 	= 1;
							$objectsp->amount_inf  	= 0;
							$objectsp->amount      	= $calc_amount;
							$objectsp->hours_info  	= 0;
							$objectsp->hours       	= $calc_hour;
							$objectsp->date_reg    	= $now;
							$objectsp->date_create 	= $now;
							$objectsp->date_mod 	= $now;
							$objectsp->fk_user_create = $user->id;
							$objectsp->fk_user_mod 	= $user->id;
							$objectsp->fk_account  	= 0;
							$objectsp->payment_state = 0;
							$objectsp->state 		= 0;
							$res1 = $objectsp->create($user);
							if ($res1 <=0)
							{
								$error++;
								setEventmessages($objectsp->error,$objectsp->errors,'errors');
							}
						}
						else
						{
							$id = $objectsp->id;
							$objectsp->fetch($id);

							//actualizamos
							$objectsp->fk_cc = $dataUser['fk_cc']+0;
							$objectsp->sequen 		= $_SESSION['aPlanilla'][$idUser]['sequen'];
							$objectsp->date_mod 	= $now;
							$objectsp->fk_user_mod 	= $user->id;
							if ($obj->type_cod == 1 || $obj->type_cod == 2)
							{
								$objectsp->amount = $objectsp->amount_inf + $calc_amount;
								$objectsp->hours  = $objectsp->hours_info + $calc_hour;
							}
							else
							{
								$objectsp->amount = $calc_amount;
								$objectsp->hours  = $calc_hour;
							}
							$resup = $objectsp->update($user);
							if ($res1 <=0)
							{
								$error++;
								setEventmessages($objectsp->error,$objectsp->errors,'errors');
							}

						}
					}
				}
				$i++;
			}
		}
	}
	return $lRet;
}

function totaldiastrab($date_ini,$date_fin,$op=0)
{
	//echo '<hr>'.$date_ini.' '.$date_fin;exit;
	$diFin    = dol_print_date($date_fin,'%d');
	$meFin    = dol_print_date($date_fin,'%m');
	$anFin    = dol_print_date($date_fin,'%Y');
	//echo '<br>datefin '.$diFin.' '.$meFin.' '.$anFin;
	$nDiasFin = ($op?30:($diFin>30?30:$diFin)*1) + $meFin*30 + $anFin *360;
	$diIni    = dol_print_date($date_ini,'%d');
	$meIni    = dol_print_date($date_ini,'%m');
	$anIni    = dol_print_date($date_ini,'%Y');
	//echo '<br>dateini '.$diIni.' '.$meIni.' '.$anIni;
	$nDiasIni = $diIni*1 + $meIni*30 + $anIni *360;

	$nTotalDias  = $nDiasFin-$nDiasIni+1;
	return $nTotalDias;
}
?>