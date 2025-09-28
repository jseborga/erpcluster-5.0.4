<?php
require_once DOL_DOCUMENT_ROOT.'/budget/class/budget.class.php';

class Budgetext extends Budget
{
	var $linesres;

	public function form_select($selected='',$htmlname='fk_budget',$htmloption='',$showempty=0,$campo='rowid')
	{
		global $user;
		if (count($this->lines)>0)
		{
			$html.= '<select class="flat" name="'.$htmlname.'" '.$htmloption.'>';
			if ($showempty)
			{
				$html.= '<option value="0">&nbsp;</option>';
			}
			if ($selected <> 0 && $selected == '-1')
			{
				$html.= '<option value="-1" selected="selected">'.$langs->trans('To be defined').'</option>';
			}
			$num = count($this->lines);
			$i = 0;
			if ($num)
			{
				foreach ($this->lines AS $j => $obj)
				{
					if (!empty($selected) && $selected == $obj->$campo)
					{
						$html.= '<option value="'.$obj->$campo.'" selected="selected">'.$obj->ref.' v.'.$obj->version.' - '.$obj->title.'</option>';
					}
					else
					{
						$html.= '<option value="'.$obj->$campo.'">'.$obj->ref.' v.'.$obj->version.' - '.$obj->title.'</option>';
					}
					$i++;
				}
			}
			$html.= '</select>';
			return $html;
		}
	}

	//public $element = 'budget';

	/**
	 * Return array of projects a user has permission on, is affected to, or all projects
	 *
	 * @param 	User	$user			User object
	 * @param 	int		$mode			0=All project I have permission on, 1=Projects affected to me only, 2=Will return list of all projects with no test on contacts
	 * @param 	int		$list			0=Return array,1=Return string list
	 * @param	int		$socid			0=No filter on third party, id of third party
	 * @return 	array or string			Array of projects id, or string with projects id separated with ","
	 */
	function getBudgetAuthorizedForUser($user, $mode=0, $list=0, $socid=0)
	{
		$projects = array();
		$temp = array();
		if ($user->array_options['options_view_projet'])
		{
		//vamos a cambiar ciertos atributos
			$mode = 2;
			$socid = 0;
		}
		$sql = "SELECT ".(($mode == 0 || $mode == 1) ? "DISTINCT " : "")."p.rowid, p.ref";
		$sql.= " FROM " . MAIN_DB_PREFIX . "budget as p";
		if ($mode == 0 || $mode == 1 || $mode == 3)
		{
			$sql.= ", " . MAIN_DB_PREFIX . "element_contact as ec";
			$sql.= ", " . MAIN_DB_PREFIX . "c_type_contact as ctc";
		}
		$sql.= " WHERE p.entity IN (".getEntity('budget',1).")";
		// Internal users must see project he is contact to even if project linked to a third party he can't see.
		//if ($socid || ! $user->rights->societe->client->voir)	$sql.= " AND (p.fk_soc IS NULL OR p.fk_soc = 0 OR p.fk_soc = ".$socid.")";
		//if ($socid > 0) $sql.= " AND (p.fk_soc IS NULL OR p.fk_soc = 0 OR p.fk_soc = " . $socid . ")";

		if ($mode == 0)
		{
			$sql.= " AND ec.element_id = p.rowid";
			$sql.= " AND ( p.public = 1";
			$sql.= " OR ( ctc.rowid = ec.fk_c_type_contact";
			$sql.= " AND ctc.element = '" . $this->element . "'";
			$sql.= " AND ( (ctc.source = 'internal' AND ec.fk_socpeople = ".$user->id.")";
			$sql.= " )";
			$sql.= " ))";
		}
		if ($mode == 1)
		{
			$sql.= " AND ec.element_id = p.rowid";
			$sql.= " AND ctc.rowid = ec.fk_c_type_contact";
			$sql.= " AND ctc.element = '" . $this->element . "'";
			$sql.= " AND ( (ctc.source = 'internal' AND ec.fk_socpeople = ".$user->id.")";
			$sql.= " )";
		}
		if ($mode == 2)
		{
			// No filter. Use this if user has permission to see all project
			if (!$user->admin)
			{
		  // if ($socid)
		  // 	$sql.= " AND p.fk_soc = ".$socid;
		  // else
		  // 	{

		  // 	}
		  //$sql.= ")";
			}
		}
		if ($mode == 3)
		{
			$sql.= " AND ec.element_id = p.rowid";
			$sql.= " AND ctc.rowid = ec.fk_c_type_contact";
			$sql.= " AND ctc.element = '" . $this->element . "'";
			$sql.= " AND ( (ctc.source = 'external' AND ec.fk_socpeople = ".$user->id.")";
			$sql.= " )";
		}
		
		$resql = $this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			$i = 0;
			while ($i < $num)
			{
				$row = $this->db->fetch_row($resql);
				$objet = new Budget($this->db);
				$objet->fetch($row[0]);
				//verificamos los permisos segun el usuario
				$aRes = verifcontactbudget($user,$objet,$ret='res');
				$res = $aRes[0];
				if ($user->admin)
				{
					$projects[$row[0]] = $row[1];
					$temp[] = $row[0];
				}
				else
				{
					if ($res >0 && !empty($aRes[5]))
					{
						$projects[$row[0]] = $row[1];
						$temp[] = $row[0];
					}
				}
				$i++;
			}
			$this->db->free($resql);
			if ($list)
			{
				if (empty($temp)) return '0';
				$result = implode(',', $temp);
				return $result;
			}
		}
		else
		{
			dol_print_error($this->db);
		}

		return $projects;
	}

	/**
	 * 	Check if user has permission on current project
	 *
	 * 	@param	User	$user		Object user to evaluate
	 * 	@param  string	$mode		Type of permission we want to know: 'read', 'write'
	 * 	@return	int					>0 if user has permission, <0 if user has no permission
	 */
	function restrictedBudgetArea($user, $mode='read')
	{
		// To verify role of users
		$userAccess = 0;
		if (($mode == 'read' && ! empty($user->rights->budget->bud->leer)) || ($mode == 'write' && ! empty($user->rights->budget->bud->crear)) || ($mode == 'delete' && ! empty($user->rights->budget->bud->del)))
		{
			$userAccess = 1;
		}
		else
		{
			foreach (array('internal', 'external') as $source)
			{
				$userRole = $this->liste_contact(4, $source);
				$num = count($userRole);

				$nblinks = 0;
				while ($nblinks < $num)
				{
					if ($source == 'internal' && preg_match('/^BUDGET/', $userRole[$nblinks]['code']) && $user->id == $userRole[$nblinks]['id'])
					{
						if ($mode == 'read'   && $user->rights->budget->bud->leer)  $userAccess++;
						if ($mode == 'write'  && $user->rights->budget->bud->crear) $userAccess++;
						if ($mode == 'mod'    && $user->rights->budget->bud->mod)   $userAccess++;
						if ($mode == 'delete' && $user->rights->budget->bud->del) 	$userAccess++;
					}
					$nblinks++;
				}
			}
			//if (empty($nblinks))	// If nobody has permission, we grant creator
			//{
			//	if ((!empty($this->user_author_id) && $this->user_author_id == $user->id))
			//	{
			//		$userAccess = 1;
			//	}
			//}
		}

		return ($userAccess?$userAccess:-1);
	}

	public function fetch_lines($grupo=0,array $aStrbudget=array(),$seltype='general')
	{
		global $conf,$langs;

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.entity,";
		$sql .= " t.ref,";
		$sql .= " t.fk_budget,";
		$sql .= " t.fk_task,";
		$sql .= " t.fk_task_parent,";
		$sql .= " t.datec,";
		$sql .= " t.tms,";
		$sql .= " t.dateo,";
		$sql .= " t.datee,";
		$sql .= " t.datev,";
		$sql .= " t.label,";
		$sql .= " t.description,";
		$sql .= " t.duration_effective,";
		$sql .= " t.planned_workload,";
		$sql .= " t.progress,";
		$sql .= " t.priority,";
		$sql .= " t.fk_user_creat,";
		$sql .= " t.fk_user_valid,";
		$sql .= " t.fk_statut,";
		$sql .= " t.note_private,";
		$sql .= " t.note_public,";
		$sql .= " t.rang,";
		$sql .= " t.model_pdf,";
		$sql .= " b.c_grupo,";
		$sql .= " b.level,";
		$sql .= " b.c_view,";
		$sql .= " b.fk_unit,";
		$sql .= " b.fk_type,";
		$sql .= " b.complementary,";
		$sql .= " b.unit_budget,";
		$sql .= " b.unit_amount,";
		$sql .= " b.order_ref";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . 'budget_task' . ' as t';
		$sql .= ' INNER JOIN ' . MAIN_DB_PREFIX . 'budget_task_add AS b ON b.fk_budget_task = t.rowid';

		$sql .= ' WHERE t.fk_budget = ' . $this->id;
		$sql.= " AND t.fk_statut != -1 ";
		if ($grupo == 1)
			$sql.= " AND b.c_grupo != 1";
		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettaskext.class.php';
				require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettaskaddext.class.php';
				require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettaskresourceext.class.php';
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$line = new Budgettaskext($this->db);
					$linesearch = new Budgettaskext($this->db);
					$linesearch->fetch($obj->rowid);
					$unit = $langs->trans($linesearch->getLabelOfUnit('short'));
					$line->id = $obj->rowid;

					$line->entity = $obj->entity;
					$line->ref = $obj->ref;
					$line->fk_budget = $obj->fk_budget;
					$line->fk_task = $obj->fk_task;
					$line->fk_task_parent = $obj->fk_task_parent;
					$line->datec = $this->db->jdate($obj->datec);
					$line->tms = $this->db->jdate($obj->tms);
					$line->dateo = $this->db->jdate($obj->dateo);
					$line->datee = $this->db->jdate($obj->datee);
					$line->datev = $this->db->jdate($obj->datev);
					$line->label = $obj->label;
					$line->description = $obj->description;
					$line->duration_effective = $obj->duration_effective;
					$line->planned_workload = $obj->planned_workload;
					$line->progress = $obj->progress;
					$line->priority = $obj->priority;
					$line->fk_user_creat = $obj->fk_user_creat;
					$line->fk_user_valid = $obj->fk_user_valid;
					$line->fk_statut = $obj->fk_statut;
					$line->note_private = $obj->note_private;
					$line->note_public = $obj->note_public;
					$line->rang = $obj->rang;
					$line->model_pdf = $obj->model_pdf;

					$line->c_grupo = $obj->c_grupo;
					$line->level = $obj->level;
					$line->c_view = $obj->c_view;
					$line->fk_unit = $obj->fk_unit;
					$line->unit = $unit;
					$line->fk_type = $obj->fk_type;
					$line->complementary = $obj->complementary;
					$line->unit_budget = $obj->unit_budget;
					$line->unit_amount = $obj->unit_amount;
					$line->order_ref = $obj->order_ref;

					if ($seltype=='RUB' || $seltype=='MA' || $seltype=='MO' ||$seltype=='MQ' )
					{
						$aStrgroupcat = $aStrbudget[$this->id]['aStrgroupcat'];
						if ($seltype == 'RUB')
						{
							//obtenemos todos las categorias definidias
							$fk_categorie = $aStrgroupcat['MA'];
							$fk_categorie.= ','.$aStrgroupcat['MO'];
							$fk_categorie.= ','.$aStrgroupcat['MQ'];
						}
						else $fk_categorie = $aStrgroupcat[$seltype];
						$objbtr = new Budgettaskresourceext($this->db);
						$filterstatic = " AND t.fk_budget_task = ".$obj->rowid;
						$filterstatic.= " AND t.code_structure IN (".$fk_categorie.")";
						$res = $objbtr->fetchAll('ASC', 't.fk_product', 0,0,array(1=>1),'AND',$filterstatic);
						$this->linesres[$i] = $objbtr->lines;
					}
					$this->lines[$i] = $line;
					$i++;
				}
			}
			$this->db->free($resql);

			if ($num) {
				return $num;
			} else {
				return 0;
			}
		}
		else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return - 1;
		}
	}

	public function update_pu_all(User $user,$aStrbudget,$type='general')
	{
		global $conf,$langs;
		if ($this->fk_statut == 0 && $user->rights->budget->bud->write)
		{
			require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettaskaddext.class.php';
			require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettaskext.class.php';
			$objectdetaddtmp 	= new Budgettaskaddext($this->db);
			$objecttmp 			= new Budgetext($this->db);
			$objectdettmp 		= new Budgettaskext($this->db);
			//actualización de todos los items por cualquier cambio
			//recuperamos todos los items del presupuesto
			$res = $this->fetch_lines(0,$aStrbudget,$type);
			$sumbudget = 0;

			if ($res > 0)
			{
				$this->db->begin();
				$lines = $this->lines;
				foreach ($lines AS $j => $obj)
				{
					if (!$obj->c_grupo)
					{
						if (!$error)
						{
							$sumaunit = $objectdetaddtmp->procedure_calculo($user,$this->id,$obj->id,false);
							$objectdetaddtmp->fetch(0,$obj->id);
							$valant = $obj->unit_amount;
							$objectdetaddtmp->unit_amount = $sumaunit;
							$objectdetaddtmp->total_amount = $sumaunit*$objectdetaddtmp->unit_budget;
							$resdet = $objectdetaddtmp->update_unit_amount($user);
							$sumbudget+=$sumaunit*$objectdetaddtmp->unit_budget;
							if ($resdet<=0)
							{
								$error++;
								setEventMessages($objectdetaddtmp->error,$objectdetaddtmp->errors,'errors');
							}
							setEventMessages($langs->trans('Actualización del item ').$obj->ref.' de '.price($valant).' a '.price($sumaunit),null,'mesgs');

							//procedemos a actualizar el grupo
							//hasta que el task_parent este en 0
							$loop = true;
							$fk_task_parent = $obj->fk_task_parent;

							while ($loop == true)
							{
								if ($fk_task_parent > 0)
								{
									$total = $objectdetaddtmp->procedure_calculo_group($user,$this->id,$fk_task_parent);
									$resd = $objectdetaddtmp->fetch(0,$fk_task_parent);
									if (empty($total)) $total = 0;
									if ($resd == 1)
									{
										$objectdetaddtmp->total_amount = $total;
										$resup = $objectdetaddtmp->update($user);
										if ($resup<=0)
										{
											$error++;
											setEventMessages($objectdetaddtmp->error,$objectdetaddtmp->errors,'errors');
										}
									}
									//buscamos nuevamente en budget_task
									$resd = $objectdettmp->fetch($fk_task_parent);
									if ($resd == 1) $fk_task_parent = $objectdettmp->fk_task_parent;
									else
									{
										$loop = false;
										$objectdettmp->fetch($obj->id);
										$objectdettmp->fk_statut = -1;
										$resup = $objectdettmp->update($user);
										if ($resup<=0)
										{
											$error++;
											setEventMessages($objectdettmp->error,$objectdettmp->errors,'errors');
										}
										setEventMessages($langs->trans('Se cambio de estado al item').' '.$obj->ref.' '.$langs->trans('No tiene relación con uno superior').' '.$fk_task_parent,null,'mesgs');
									}
								}
								else
									$loop = false;
							}
							//procedemos a actualizar el presupuesto
							$total = $objectdetaddtmp->procedure_calculo_budget($this->id);
							$restmp = $objecttmp->fetch($this->id);
							if ($restmp == 1)
							{
								$objecttmp->budget_amount = $total;
								$objecttmp->update($user);
							}
						}
					}
				}

				if (!$error)
				{
					//actualizamos el valor total en budget
					//$this->budget_amount = $sumbudget;
					//$res = $this->update($user);
					//if ($res <=0)
					//{
					//	$error++;
					//	setEventMessages($this->error,$this->errors,'errors');
					//}
				}
				if (!$error)
				{
					$this->db->commit();
					setEventMessages($langs->trans('Actualización satisfactoria de las tareas'),null,'mesgs');
					return 1;
				}
				else
				{
					$this->db->rollback();
					return $error*-1;
				}
			}
			return $res;
		}
		return 0;
	}

	/**
	 *  Retourne le libelle du status d'un user (actif, inactif)
	 *
	 *  @param	int		$mode          0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return	string 			       Label of status
	 */
	function getLibStatut($mode=0)
	{
		return $this->LibStatut($this->status,$mode);
	}

	/**
	 *  Renvoi le libelle d'un status donne
	 *
	 *  @param	int		$status        	Id status
	 *  @param  int		$mode          	0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return string 			       	Label of status
	 */
	function LibStatut($status,$mode=0)
	{
		global $langs;

		if ($mode == 0)
		{
			$prefix='';
			if ($status == -1) return $langs->trans('Discarted');
			if ($status == 0) return $langs->trans('Draft');
			if ($status == 1) return $langs->trans('In Validation');
			//if ($status == 2) return $langs->trans('Validated');
			if ($status == 2) return $langs->trans('Approved');
		}
		if ($mode == 1)
		{
			if ($status == -1) return $langs->trans('Discarted');
			if ($status == 0) return $langs->trans('Draft');
			if ($status == 1) return $langs->trans('In Validation');
			//if ($status == 2) return $langs->trans('Validated');
			if ($status == 2) return $langs->trans('Approved');
		}
		if ($mode == 2)
		{
			if ($status == -1) return img_picto($langs->trans('Discarted'),'statut5').' '.$langs->trans('Discarted');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut0').' '.$langs->trans('Draft');
			if ($status == 1) return img_picto($langs->trans('In Validation'),'statut1').' '.$langs->trans('In Validation');
			//if ($status == 2) return img_picto($langs->trans('Validated'),'statut3').' '.$langs->trans('Validated');
			if ($status == 2) return img_picto($langs->trans('Approved'),'statut4').' '.$langs->trans('Approved');
		}
		if ($mode == 3)
		{
			if ($status == -1) return img_picto($langs->trans('Discarted'),'statut5');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut0');
			if ($status == 1) return img_picto($langs->trans('In Validation'),'statut1');
			//if ($status == 2) return img_picto($langs->trans('Validated'),'statut3');
			if ($status == 2) return img_picto($langs->trans('Approved'),'statut4');
		}
		if ($mode == 4)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5').' '.$langs->trans('Disabled');
		}
		if ($mode == 5)
		{
			if ($status == 1) return $langs->trans('Enabled').' '.img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return $langs->trans('Disabled').' '.img_picto($langs->trans('Disabled'),'statut5');
		}
	}
}
?>