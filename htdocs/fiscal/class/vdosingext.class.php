<?php
include_once DOL_DOCUMENT_ROOT.'/fiscal/class/vdosing.class.php';

class Vdosingext extends Vdosing
{
		//modificado
	/**
	 *  Load object in memory from the database
	 *
	 *  @param  int     $id    Id object
	 *  @return int             <0 if KO, >0 if OK
	 */
	function fetch_sub_serie($fk_subsidiaryid,$series,$statut=1)
	{
		global $langs;
		if (empty($fk_subsidiaryid) || empty($series))
			return -1;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.fk_subsidiaryid,";
		$sql.= " t.series,";
		$sql.= " t.num_ini,";
		$sql.= " t.num_fin,";
		$sql.= " t.num_ult,";
		$sql.= " t.num_aprob,";
		$sql.= " t.type,";
		$sql.= " t.active,";
		$sql.= " t.date_val,";
		$sql.= " t.num_autoriz,";
		$sql.= " t.cod_control,";
		$sql.= " t.lote,";
		$sql.= " t.chave,";
		$sql.= " t.descrip,";
		$sql.= " t.status";


		$sql.= " FROM ".MAIN_DB_PREFIX."v_dosing as t";
		$sql.= " WHERE t.fk_subsidiaryid = ".$fk_subsidiaryid;
		$sql.= " AND t.series = '".$series."'";
		$sql.= " AND t.status = ".$statut;
		dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->array = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($this->db->num_rows($resql))
			{
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objnew = new Vdosing($this->db);

					$objnew->id    = $obj->rowid;
					$objnew->entity = $obj->entity;
					$objnew->fk_subsidiaryid = $obj->fk_subsidiaryid;
					$objnew->series = $obj->series;
					$objnew->num_ini = $obj->num_ini;
					$objnew->num_fin = $obj->num_fin;
					$objnew->num_ult = $obj->num_ult;
					$objnew->num_aprob = $obj->num_aprob;
					$objnew->type = $obj->type;
					$objnew->active = $obj->active;
					$objnew->date_val = $objnew->db->jdate($obj->date_val);
					$objnew->num_autoriz = $obj->num_autoriz;
					$objnew->cod_control = $obj->cod_control;
					$objnew->lote = $obj->lote;
					$objnew->chave = $obj->chave;
					$objnew->descrip = $obj->descrip;
					$objnew->status = $obj->status;
					$this->array[$obj->rowid] = $objnew;
					$i++;
				}
			}
			$this->db->free($resql);

			return $num;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
			return -1;
		}
	}

	function set_facturation_man_anterior($now,$id,$thirdpartyid,$nit,$razsoc,$fkSubsidiaryid,$series,$nTotalTtc,$nTotalTva,$tva_tx,$fk_tva,$tab_tva,$sum_payment,$balance,$numautsel,$numfactsel)
	{
		global $conf,$user;
		//solo para modseller = 1
		$sql = "SELECT t.rowid, t.series, t.num_ini, t.num_fin, t.num_ult, ";
		$sql.= " num_autoriz, t.chave ";
		$sql.= " FROM ".MAIN_DB_PREFIX."v_dosing AS t ";
		$sql.= " WHERE ";
		$sql.= " t.entity = ".$conf->entity;
		$sql.= " AND t.fk_subsidiaryid = ".$_SESSION['fkSubsidiaryid'];
		$sql.= " AND t.lote = 1 ";
		$sql.= " AND active = 1 ";
		$res1=$this->db->query($sql);
		if ($res1)
		{
			if ($this->db->num_rows($res1))
			{
				$this->db->begin();
				$objd = $this->db->fetch_object($res1);
				$llave = trim($objd->chave);
				$numaut = $objd->num_autoriz;

				$numaut    = $numautsel;
				$newnumfac = $numfactsel;

				// actualizando el valor
				$objdosing = new Vdosingext($this->db);
				$objdosing->fetch($objd->rowid);

				if ($objdosing->id == $objd->rowid && $nTotalTtc > 0)
				{
					if ($objdosing->num_ult == $newnumfac)
					{
						$error++;
						$lInvoicechek = true;
						$mesg = $langs->trans('Duplicateinvoicepleasecheck');
					}

					if ($objdosing->num_ult < $newnumfac)
						$objdosing->num_ult = $newnumfac;

					$resultdosing = $objdosing->update($user);
					if ($resultdosing <= 0)
						$error++;

					$codigocontrol = '';

						//agregando a libros fiscales
					$objvfis = new Vfiscalext($this->db);
					//buscamos el registro
					$filterstatic = " AND t.fk_dosing = ".$objd->rowid;
					$filterstatic.= " AND t.nfiscal = ".$newnumfac;
					$resfis = $objvfis->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic,true);
					if ($resfis == 0)
					{

						$objvfis->entity = $conf->entity;
						$objvfis->nfiscal = $newnumfac;
						$objvfis->serie   = $objd->series;
						$objvfis->fk_dosing = $objd->rowid;
						$objvfis->fk_facture = $id;
						$objvfis->fk_cliepro = $thirdpartyid;
						$objvfis->nit = $nit;
						$objvfis->razsoc = $razsoc;
						$objvfis->date_exp = $now;
						$objvfis->type_op = 1;
						// venta
						$objvfis->num_autoriz = $numaut;
						$objvfis->cod_control = $codigocontrol;

						//reemplazo

						//$objvfis->baseimp1 = $obj_facturation->prixTotalTtc();
						//$objvfis->valimp1 = $obj_facturation->montantTva();
						$objvfis->baseimp1 = price2num($nTotalTtc,'MT');
						$objvfis->valimp1 = $nTotalTva;


						//$objvfis->aliqimp1 = $fk_tva;
						$objvfis->aliqimp1 = $tva_tx;
						if (empty($tva_tx))
							$objvfis->aliqimp1 = empty($tab_tva['taux'])?$fk_tva:$tab_tva['taux'];
						//agregando cambio y pago
						$objvfis->amount_payment = $sum_payment+0;
						$objvfis->amount_balance = $balance+0;
						$objvfis->date_create=dol_now();
						$objvfis->date_mod=dol_now();
						$objvfis->tms=dol_now();
						$objvfis->fk_user_create = $user->id;
						$objvfis->fk_user_mod = $user->id;
						$objvfis->status = 1;
						$objvfis->status_print = 0;
						$idvfiscal = $objvfis->create($user);
						if ($idvfiscal <= 0)
						{
							$error++;
							setEventMessages($objvfis->error,$objvfis->errors,'errors');
						}
					}
					else
					{
						$error++;
						setEventMessages($langs->trans('Error, existe registro de la factura manual'),null,'errors');
					}
				}
				else
				{
					$error++;
					setEventMessages($langs->trans('No existe la dosificación para actualización, notificar al administrador'),null,'errors');
				}
				if (!$error)
				{
					$this->db->commit();
					return $idvfiscal;
				}
				else
				{
					$this->db->rollback();
					return -1;
				}
			}
			else
			{
				$error++;
				setEventMessages($langs->trans('No existe registro de dosificación, revise'),null,'errors');
				return -1;
			}
		}
		else
		{
			setEventMessages($this->db->error,null,'errors');
			return -1;
		}
	}

	function set_facturation_aut($now,$id,$thirdpartyid,$nit,$razsoc,$fkSubsidiaryid,$series,$nTotalTtc,$nTotalTva,$tva_tx,$fk_tva,$tab_tva,$sum_payment,$balance,$tva_txdef='')
	{
		global $conf,$user,$langs;
		//todo con modseller == 2
		//repetimos si existe el numero de factura registrado en el libro fiscal
		$nSleep = ($conf->global->VENTAS_SLEEP_SECONDS?$conf->global->VENTAS_SLEEP_SECONDS:1);
		$nLoop=0;
		$lLoop = true;
		$new = dol_now();
		$this->db->begin();
		while ($lLoop==true)
		{
			//vamos a recuperar el numero de factura caundo la tabla este disponible
			//
			$filterdosing = " AND t.entity = ".$conf->entity;
			$filterdosing.= " AND t.lote = 2";
			$filterdosing.= " AND t.active = 1";
			$filterdosing.= " AND t.status = 1";
			$filterdosing.= " AND t.series = '".$series."'";
			$filterdosing.= " AND t.fk_subsidiaryid = ".$fkSubsidiaryid;
			$resdosing = $this->fetchAll('','',0,0,array(),'AND',$filterdosing,true);
			if ($resdosing <=0)
			{
				$nLoop++;
				if ($nLoop > 10)
				{
					$lLoop=false;
					$error=400;
					setEventMessages($this->error,$this->errors,'errrors');
				}
			}
			else
			{
				if ($resdosing == 1)
				{
					//$objd = $this->db->fetch_object($result1);
					$llave = trim($this->chave);
					$numaut = $this->num_autoriz;
					if ($this->num_ult) $newnumfac = $this->num_ult + 1;
					else $newnumfac = $this->num_ini;
						// actualizando el valor
					$objdosing = new Vdosingext($this->db);
					//echo ' busca  º'.$this->rowid.'| '.$this->id.'|amount '.$nTotalTtc;
					$resupdosing = $objdosing->fetch($this->id);

					if ($objdosing->id == $this->id && $nTotalTtc > 0)
					{
						$objdosing->num_ult = $newnumfac;

						if ($objdosing->num_ult < $newnumfac)
							$objdosing->num_ult = $newnumfac;

						$resultdosing = $objdosing->update($user);
						if ($resultdosing <= 0)
						{
							$error=301;
							setEventMessages($objdosing->error,$objdosing->errors,'errors');
						}
							//llamando el codigo para generar codigo control
						require_once DOL_DOCUMENT_ROOT.'/ventas/factura/cc.php';
							//la fecha es la actual
						$nowtext = date('Y').date('m').date('d');
						if (empty($nit))
						{
							$nit = 0;
						}
						$CodContr = new CodigoControl(trim($numaut),trim($newnumfac),trim($nit),$nowtext,price2num($nTotalTtc,'MT'),trim($llave));
						$codigocontrol = $CodContr->generar();
						if (strlen($codigocontrol) > 15)
						{
							unset($_SESSION['lastidvfiscal']);
							$error=401;
							$errcc = 1;
							setEventMessages($langs->trans('ErrorCodigodecontrol'),null,'errors');
						}

							//agregando a libros fiscales
						$objvfis = new Vfiscalext($this->db);

								//buscamos el registro mismo que no debe existir
						$filterstatic = " AND t.fk_dosing = ".$this->id;
						$filterstatic.= " AND t.nfiscal = ".$newnumfac;
						$resfis = $objvfis->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic,true);

						if ($resfis == 0)
						{
							$objvfis->entity = $conf->entity;
							$objvfis->nfiscal = $newnumfac;
							$objvfis->serie   = $this->series;
							$objvfis->fk_dosing = $this->id;
							$objvfis->fk_facture = $id;
							$objvfis->fk_cliepro = $thirdpartyid;
							$objvfis->nit = $nit;
							$objvfis->razsoc = STRTOUPPER($razsoc);
							$objvfis->date_exp = $now;
							$objvfis->type_op = 1;
									// venta
							$objvfis->num_autoriz = $numaut;
							$objvfis->cod_control = $codigocontrol;

									//reemplazo

									//$objvfis->baseimp1 = $obj_facturation->prixTotalTtc();
									//$objvfis->valimp1 = $obj_facturation->montantTva();
							$objvfis->baseimp1 = price2num($nTotalTtc,'MT');
							$objvfis->valimp1 = $nTotalTva;


									//$objvfis->aliqimp1 = $fk_tva;
							$objvfis->aliqimp1 = $tva_tx;
							if (empty($tva_tx))
							{
								if (!empty($tva_txdef)) $objvfis->aliqimp1 = $tva_txdef;
								else $objvfis->aliqimp1 = empty($tab_tva['taux'])?$fk_tva:$tab_tva['taux'];
									//agregando cambio y pago
							}


							$objvfis->amount_payment = $sum_payment+0;
							$objvfis->amount_balance = $balance+0;
							$objvfis->date_create=$new;
							$objvfis->date_mod=$new;
							$objvfis->tms=$new;
							$objvfis->fk_user_create = $user->id;
							$objvfis->fk_user_mod = $user->id;
							$objvfis->status = 1;
							$idvfiscal = $objvfis->create($user);

							if ($idvfiscal <= 0)
							{
								$nLoop++;
								if ($nLoop>10)
								{
									$lLoop = false;
									$error=501;
									setEventMessages($objvfis->error,$objvfis->errors,'errors');
								}
							}
							else
							{
								$lLoop = false;
							}
						}
						else
						{
							$nLoop++;
							if ($nLoop>10)
							{
								$lLoop = false;
								$error = 502;
								setEventMessages($objvfis->error,$objvfis->errors,'errors');
							}
						}

					}
					else
					{
						$lLoop = false;
						$error=601;
						$this->db->rollback();
						setEventMessages($langs->trans('No existe la dosificación para actualización, notificar al administrador'),null,'errors');
						return $error*-1;
					}
				}
				elseif ($resdosing > 1)
				{
					$error=501;
					$this->db->rollback();
					$lLoop = false;
					setEventMessages($langs->trans('Mas de una dosificación activa, comunique al Administrador'),null,'errors');
					return -501;
				}
				if ($idvfiscal>0) $lLoop=false;
				else $nLoop++;
			}
			if ($idvfiscal<=0 && $nLoop>10)
			{
				$lLoop=false;
				$error=701;
				setEventMessages($langs->trans('No se pudo ejecutar la accion'),null,'errors');
			}
			if ($lLoop) sleep($nSleep);
		}
		if (!$error)
		{
			$this->db->commit();
			return $idvfiscal;
		}
		else
		{
			$this->db->rollback();
			return $error*-1;
		}
	}

	function set_facturation_man($now,$id,$thirdpartyid,$nit,$razsoc,$fkSubsidiaryid,$series,$nTotalTtc,$nTotalTva,$tva_tx,$fk_tva,$tab_tva,$sum_payment,$balance,$numautsel,$numfactsel,$tva_txdef='')
	{
		global $conf,$user,$langs;

		$nSleep = ($conf->global->VENTAS_SLEEP_SECONDS?$conf->global->VENTAS_SLEEP_SECONDS:1);
		$nLoop=0;
		$lLoop = true;
		$new = dol_now();
		$this->db->begin();
		while ($lLoop==true)
		{
			//solo para modseller = 1
			//vamos a recuperar el numero de factura caundo la tabla este disponible
			//
			$filterdosing = " AND t.entity = ".$conf->entity;
			$filterdosing.= " AND t.lote = 1";
			$filterdosing.= " AND t.active = 1";
			$filterdosing.= " AND t.status = 1";
			$filterdosing.= " AND t.fk_subsidiaryid = ".$fkSubsidiaryid;
			$resdosing = $this->fetchAll('','',0,0,array(),'AND',$filterdosing,true);
			if ($resdosing <=0)
			{
				$nLoop++;
				if ($nLoop > 10)
				{
					$lLoop=false;
					$error=400;
					setEventMessages($this->error,$this->errors,'errrors');
				}
			}
			else
			{
				if ($resdosing == 1)
				{

					$llave = trim($this->chave);
					$numaut = $this->num_autoriz;

					$numaut    = $numautsel;
					$newnumfac = $numfactsel;

					// actualizando el valor
					$objdosing = new Vdosingext($this->db);
					$resupdosing = $objdosing->fetch($this->id);

					if ($objdosing->id == $this->id && $nTotalTtc > 0)
					{
						if ($objdosing->num_ult == $newnumfac)
						{
							$error++;
							$lInvoicechek = true;
							setEventMessages($langs->trans('Duplicateinvoicepleasecheck'),null,'errors');
						}

						if ($objdosing->num_ult < $newnumfac)
							$objdosing->num_ult = $newnumfac;

						$resultdosing = $objdosing->update($user);
						if ($resultdosing <= 0)
						{
							$error++;
							setEventMessages($objdosing->error,$objdosing->errors,'errors');
						}

						$codigocontrol = '';

						//agregando a libros fiscales
						$objvfis = new Vfiscalext($this->db);
						//buscamos el registro
						$filterstatic = " AND t.fk_dosing = ".$this->id;
						$filterstatic.= " AND t.nfiscal = ".$newnumfac;
						$resfis = $objvfis->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic,true);
						if ($resfis == 0)
						{

							$objvfis->entity = $conf->entity;
							$objvfis->nfiscal = $newnumfac;
							$objvfis->serie   = $this->series;
							$objvfis->fk_dosing = $this->id;
							$objvfis->fk_facture = $id;
							$objvfis->fk_cliepro = $thirdpartyid;
							$objvfis->nit = $nit;
							$objvfis->razsoc = STRTOUPPER($razsoc);
							$objvfis->date_exp = $now;
							$objvfis->type_op = 1;
							// venta
							$objvfis->num_autoriz = $numaut;
							$objvfis->cod_control = $codigocontrol;

							//reemplazo

							//$objvfis->baseimp1 = $obj_facturation->prixTotalTtc();
							//$objvfis->valimp1 = $obj_facturation->montantTva();
							$objvfis->baseimp1 = price2num($nTotalTtc,'MT');
							$objvfis->valimp1 = $nTotalTva;


							//$objvfis->aliqimp1 = $fk_tva;
							$objvfis->aliqimp1 = $tva_tx;
							if (empty($tva_tx))
							{
								if (!empty($tva_txdef)) $objvfis->aliqimp1 = $tva_txdef;
								else $objvfis->aliqimp1 = empty($tab_tva['taux'])?$fk_tva:$tab_tva['taux'];
							}
							//agregando cambio y pago
							$objvfis->amount_payment = $sum_payment+0;
							$objvfis->amount_balance = $balance+0;
							$objvfis->date_create=$new;
							$objvfis->date_mod=$new;
							$objvfis->tms=$new;
							$objvfis->fk_user_create = $user->id;
							$objvfis->fk_user_mod = $user->id;
							$objvfis->status = 1;
							$objvfis->status_print = 0;
							echo ' id '.$idvfiscal = $objvfis->create($user);
							if ($idvfiscal <= 0)
							{
								$nLoop++;
								if ($nLoop>10)
								{
									$lLoop = false;
									$error=501;
									setEventMessages($objvfis->error,$objvfis->errors,'errors');
								}
							}
							else $lLoop=false;
						}
						else
						{
							$error++;
							setEventMessages($langs->trans('Error, existe registro de la factura manual'),null,'errors');
						}
					}
					else
					{
						$nLoop++;
						if ($nLoop>10)
						{
							$lLoop = false;
							$error = 502;
							setEventMessages($objvfis->error,$objvfis->errors,'errors');
						}
					}
					if (!$error)
					{
						$this->db->commit();
						return $idvfiscal;
					}
					else
					{
						$this->db->rollback();
						return -1;
					}
				}
				elseif ($resdosing > 1)
				{
					$error=501;
					$this->db->rollback();
					$lLoop = false;
					setEventMessages($langs->trans('Mas de una dosificación activa, comunique al Administrador'),null,'errors');
					return -501;
				}
				if ($idvfiscal>0) $lLoop=false;
				else $nLoop++;
			}
			if ($idvfiscal<=0 && $nLoop>10)
			{
				$lLoop=false;
				$error=701;
				setEventMessages($langs->trans('No se pudo ejecutar la accion'),null,'errors');
			}
			if ($lLoop) sleep($nSleep);
		}

		if (!$error)
		{
			$this->db->commit();
			return $idvfiscal;
		}
		else
		{
			$this->db->rollback();
			return $error*-1;
		}
	}
}
?>