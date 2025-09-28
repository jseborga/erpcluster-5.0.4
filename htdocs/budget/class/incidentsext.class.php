<?php
require_once DOL_DOCUMENT_ROOT.'/budget/class/incidents.class.php';

class Incidentsext extends Incidents
{

	public function clone_item(User $user,$fk_incident,$fk_region)
	{
		//recuperamos las classes a utilizarse
		require_once DOL_DOCUMENT_ROOT.'/budget/class/incidentsdetext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/incidentsres.class.php';

		$objIncidents = new Incidentsext($this->db);
		$objIncidentstmp = new Incidentsext($this->db);
		$objIncidentsdet = new Incidentsdetext($this->db);
		$objIncidentsres = new Incidentsres($this->db);
		$res = $objIncidentstmp->fetch($fk_incident);
		if ($res == 1)
		{
			$this->db->begin();
			$now=dol_now();
			//buscamos si existe
			$resbi = $objIncidents->fetch(0,null,$this->code_parameter,$fk_region);
			if ($resbi==0)
			{
				$objIncidents = $objIncidentstmp;
				$objIncidents->id=0;
				//creamos
				$objIncidents->fk_region = $fk_region;

				$objIncidents->fk_user_create = $user->id;
				$objIncidents->fk_user_mod = $user->id;
				$objIncidents->datec = $now;
				$objIncidents->datem = $now;
				$objIncidents->tms = $now;
				$objIncidents->status = 0;
				$fk_incident_new = $objIncidents->create($user);
				if ($fk_incident_new<=0)
				{
					$error++;
					setEventMessages($objIncidents->error,$objIncidents->errors,'errors');
				}
				if (!$error)
				{
					//buscamos det
					$filter = " AND t.fk_incident = ".$fk_incident;
					$res = $objIncidentsdet->fetchAll('','',0,0,array(),'AND',$filter);
					if ($res >0)
					{
						$linesdet = $objIncidentsdet->lines;
						foreach ($linesdet AS $k => $linedet)
						{
							//vamos a buscar si no existe
							$filterdet = " AND t.fk_incident = ".$fk_incident_new;
							$filterdet.= " AND t.type= '".$linedet->type."' AND t.label = '".$linedet->label."'";
							$resdet = $objIncidentsdet->fetchAll('','',0,0,array(),'AND',$filterdet,true);
							if ($resdet==0)
							{
								//vamos a cargar
								foreach ($linedet AS $campo => $value)
								{
									$objIncidentsdet->$campo = $value;
								}
								$objIncidentsdet->fk_incident = $fk_incident_new;
								$objIncidentsdet->fk_user_create = $user->id;
								$objIncidentsdet->fk_user_mod = $user->id;
								$objIncidentsdet->datec = $now;
								$objIncidentsdet->datem = $now;
								$objIncidentsdet->tms = $now;
								$objIncidentsdet->status = 1;
								$resdet = $objIncidentsdet->create($user);
								if ($resdet<=0)
								{
									$error++;
									setEventMessages($objIncidentsdet->error,$objIncidentsdet->errors,'errors');
								}

							}
						}
					}
				}
				if (!$error)
				{
					//buscamos det
					$filter = " AND t.fk_incident = ".$fk_incident;
					$res = $objIncidentsres->fetchAll('','',0,0,array(),'AND',$filter);
					if ($res >0)
					{
						$linesdet = $objIncidentsres->lines;
						foreach ($linesdet AS $k => $linedet)
						{
							//vamos a buscar si no existe
							$filterdet = " AND t.fk_incident = ".$fk_incident_new;
							$filterdet.= " AND t.type= '".$linedet->type."' AND t.group_det = '".$linedet->group_det."'";
							$resdet = $objIncidentsres->fetchAll('','',0,0,array(),'AND',$filterdet,true);
							if ($resdet==0)
							{
								//vamos a cargar
								foreach ($linedet AS $campo => $value)
								{
									$objIncidentsres->$campo = $value;
								}
								$objIncidentsres->fk_incident = $fk_incident_new;
								$objIncidentsres->fk_user_create = $user->id;
								$objIncidentsres->fk_user_mod = $user->id;
								$objIncidentsres->datec = $now;
								$objIncidentsres->datem = $now;
								$objIncidentsres->tms = $now;
								$objIncidentsres->status = 1;
								$resdet = $objIncidentsres->create($user);
								if ($resdet<=0)
								{
									$error++;
									setEventMessages($objIncidentsres->error,$objIncidentsres->errors,'errors');
								}

							}
						}
					}
				}
			}
			if (!$error)
			{
				$this->db->commit();
				return $fk_incident_new;
			}
			else
			{
				$this->db->rollback();
				return -1;
			}

		}

	}
	public function clone_budget(User $user,$fk_budget,$fk_incident)
	{
		//recuperamos las classes a utilizarse
		require_once DOL_DOCUMENT_ROOT.'/budget/class/budgetincidents.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/budgetincidentsdet.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/budgetincidentsres.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/incidentsdetext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/budget/class/incidentsres.class.php';
		$res = $this->fetch($fk_incident);
		$objBudgetincidents = new Budgetincidents($this->db);
		$objBudgetincidentsdet = new Budgetincidentsdet($this->db);
		$objBudgetincidentsres = new Budgetincidentsres($this->db);
		$objIncidentsdet = new Incidentsdetext($this->db);
		$objIncidentsres = new Incidentsres($this->db);
		if ($res == 1)
		{
			$this->db->begin();
			$now=dol_now();
			//buscamos si existe
			//$resbi = $objBudgetincidents->fetch(0,$fk_incident);
			$resbi = $objBudgetincidents->fetch(0, $fk_budget,$this->code_parameter,$this->fk_region);
			if ($resbi==0)
			{
				//echo '<pre>';
				//print_r($this->lines[$fk_incident]);
				//echo '</pre>';
				$aData = $this->lines[$fk_incident];
				//print_r($aData);
				foreach ($aData AS $campo => $value)
				{
					//echo ' <hr>'.$campo .' '.$value;
					$objBudgetincidents->$campo = $value;
				}
				//creamos
				//$objBudgetincidents->entity = $conf->entity;
				$objBudgetincidents->fk_budget = $fk_budget;

				$objBudgetincidents->fk_user_create = $user->id;
				$objBudgetincidents->fk_user_mod = $user->id;
				$objBudgetincidents->datec = $now;
				$objBudgetincidents->datem = $now;
				$objBudgetincidents->tms = $now;
				$objBudgetincidents->status = 0;
				$fk_budget_incident = $objBudgetincidents->create($user);
				if ($fk_budget_incident<=0)
				{
					$error++;
					setEventMessages($objBudgetincidents->error,$objBudgetincidents->errors,'errors');
				}
				if (!$error)
				{
					//buscamos det
					$filter = " AND t.fk_incident = ".$fk_incident;
					$res = $objIncidentsdet->fetchAll('','',0,0,array(),'AND',$filter);
					if ($res >0)
					{
						$linesdet = $objIncidentsdet->lines;
						foreach ($linesdet AS $k => $linedet)
						{
							//vamos a buscar si no existe
							$filterdet = " AND t.fk_budget_incident = ".$fk_budget_incident;
							$filterdet.= " AND t.type= '".$linedet->type."' AND t.label = '".$linedet->label."'";
							$resdet = $objBudgetincidentsdet->fetchAll('','',0,0,array(),'AND',$filterdet,true);
							if ($resdet==0)
							{
								//vamos a cargar
								foreach ($linedet AS $campo => $value)
								{
									$objBudgetincidentsdet->$campo = $value;
								}
								$objBudgetincidentsdet->fk_budget_incident = $fk_budget_incident;
								$objBudgetincidentsdet->fk_user_create = $user->id;
								$objBudgetincidentsdet->fk_user_mod = $user->id;
								$objBudgetincidentsdet->datec = $now;
								$objBudgetincidentsdet->datem = $now;
								$objBudgetincidentsdet->tms = $now;
								$objBudgetincidentsdet->status = 1;
								$resdet = $objBudgetincidentsdet->create($user);
								if ($resdet<=0)
								{
									$error++;
									setEventMessages($objBudgetincidentsdet->error,$objBudgetincidentsdet->errors,'errors');
								}

							}
						}
					}
				}
				if (!$error)
				{
					//buscamos det
					$filter = " AND t.fk_incident = ".$fk_incident;
					$res = $objIncidentsres->fetchAll('','',0,0,array(),'AND',$filter);
					if ($res >0)
					{
						$linesdet = $objIncidentsres->lines;
						foreach ($linesdet AS $k => $linedet)
						{
							//vamos a buscar si no existe
							$filterdet = " AND t.fk_budget_incident = ".$fk_budget_incident;
							$filterdet.= " AND t.type= '".$linedet->type."' AND t.group_det = '".$linedet->group_det."'";
							$resdet = $objBudgetincidentsres->fetchAll('','',0,0,array(),'AND',$filterdet,true);
							if ($resdet==0)
							{
								//vamos a cargar
								foreach ($linedet AS $campo => $value)
								{
									$objBudgetincidentsres->$campo = $value;
								}
								$objBudgetincidentsres->fk_budget_incident = $fk_budget_incident;
								$objBudgetincidentsres->fk_user_create = $user->id;
								$objBudgetincidentsres->fk_user_mod = $user->id;
								$objBudgetincidentsres->datec = $now;
								$objBudgetincidentsres->datem = $now;
								$objBudgetincidentsres->tms = $now;
								$objBudgetincidentsres->status = 1;
								$resdet = $objBudgetincidentsres->create($user);
								if ($resdet<=0)
								{
									$error++;
									setEventMessages($objBudgetincidentsres->error,$objBudgetincidentsres->errors,'errors');
								}

							}
						}
					}
				}
			}
			if (!$error)
			{
				$this->db->commit();
				return 1;
			}
			else
			{
				$this->db->rollback();
				return -1;
			}

		}

	}
}
?>