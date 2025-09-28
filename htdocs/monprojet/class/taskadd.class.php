<?php
dol_include_once('/projet/class/task.class.php');

class TaskAdd extends Task
{
	public $date_ini;
	public $date_end;
	public $date_dailya;
	public $date_dailyb;
	public $date_weeklya;
	public $date_weeklyb;
	public $date_fortnightlya;
	public $date_fortnightlyb;
	public $date_bimonthlya;
	public $date_bimonthlyb;
	public $date_biannuala;
	public $date_biannualb;
	public $date_annuala;
	public $date_annualb;
	public $lines;
	public $unit_declared;
	public $date_startr;
	public $date_endr;
	public $quantr;
	public $declaredr;

  	/*
   	* fetch all busqueda por diferentes variables
   	*/

	/**
	 *  Load object in memory from database
	 *
	 *  @param	int		$id			Id object
	 *  @param	int		$ref		ref object
	 *  @return int 		        <0 if KO, >0 if OK
	 */
	function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lRow=false,$filteradd='')
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		global $langs;

		$sql = "SELECT";
		$sql.= " t.rowid,";
		$sql.= " t.ref,";
		$sql.= " t.fk_projet,";
		$sql.= " t.fk_task_parent,";
		$sql.= " t.label,";
		$sql.= " t.description,";
		$sql.= " t.duration_effective,";
		$sql.= " t.planned_workload,";
		$sql.= " t.datec,";
		$sql.= " t.dateo,";
		$sql.= " t.datee,";
		$sql.= " t.fk_user_creat,";
		$sql.= " t.fk_user_valid,";
		$sql.= " t.fk_statut,";
		$sql.= " t.progress,";
		$sql.= " t.priority,";
		$sql.= " t.note_private,";
		$sql.= " t.note_public,";
		$sql.= " t.rang";
		if ($filteradd)
		{
			$sql.= ", u.c_grupo, u.fk_unit, u.fk_type, u.fk_item, u.unit_budget, u.unit_program, u.unit_declared, u.unit_ejecuted, u.unit_amount ";
		}
		$sql.= " FROM ".MAIN_DB_PREFIX."projet_task as t";
		if ($filteradd)
		{
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."projet_task_add AS u ON u.fk_task = t.rowid";
		}
		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				$sqlwhere [] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
			}
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' WHERE ' . implode(' '.$filtermode.' ', $sqlwhere);
		}
		if ($filterstatic) $sql.= $filterstatic;
		if ($filteradd) $sql.= $filteradd;

		if (!empty($sortfield)) {
			$sql .= ' ORDER BY ' . $sortfield . ' ' . $sortorder;
		}
		if (!empty($limit)) {
			$sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
		}
		$this->lines = array();
		//echo '<hr>sql '.$sql;
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql))
			{
				$line = new TaskLine();

				$line->id			= $obj->rowid;
				$line->ref		= $obj->ref;
				$line->fk_project		= $obj->fk_projet;
				$line->fk_task_parent	= $obj->fk_task_parent;
				$line->label		= $obj->label;
				$line->description	= $obj->description;
				$line->duration_effective	= $obj->duration_effective;
				$line->planned_workload	= $obj->planned_workload;
				$line->date_c		= $this->db->jdate($obj->datec);
				$line->date_start		= $this->db->jdate($obj->dateo);
				$line->date_end		= $this->db->jdate($obj->datee);
				$line->fk_user_creat	= $obj->fk_user_creat;
				$line->fk_user_valid	= $obj->fk_user_valid;
				$line->fk_statut		= $obj->fk_statut;
				$line->progress		= $obj->progress;
				$line->priority		= $obj->priority;
				$line->note_private	= $obj->note_private;
				$line->note_public	= $obj->note_public;
				$line->rang		= $obj->rang;
				if ($lRow)
				{
					$this->id			      = $obj->rowid;
					$this->ref			      = $obj->ref;
					$this->fk_project		  = $obj->fk_projet;
					$this->fk_task_parent	  = $obj->fk_task_parent;
					$this->label			  = $obj->label;
					$this->description		  = $obj->description;
					$this->duration_effective = $obj->duration_effective;
					$this->planned_workload	  = $obj->planned_workload;
					$this->date_c			  = $this->db->jdate($obj->datec);
					$this->date_start		  = $this->db->jdate($obj->dateo);
					$this->date_end		      = $this->db->jdate($obj->datee);
					$this->fk_user_creat	  = $obj->fk_user_creat;
					$this->fk_user_valid	  = $obj->fk_user_valid;
					$this->fk_statut		  = $obj->fk_statut;
					$this->progress		      = $obj->progress;
					$this->priority		      = $obj->priority;
					$this->note_private		  = $obj->note_private;
					$this->note_public		  = $obj->note_public;
					$this->rang			      = $obj->rang;

				}
				$this->lines[] = $line;
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

  	/**
	 * Return list of tasks for all projects or for one particular project
	 * Sort order is on project, then on position of task, and last on start date of first level task
	 *
	 * @param	User	$usert				Object user to limit tasks affected to a particular user
	 * @param	User	$userp				Object user to limit projects of a particular user and public projects
	 * @param	int		$projectid			Project id
	 * @param	int		$socid				Third party id
	 * @param	int		$mode				0=Return list of tasks and their projects, 1=Return projects and tasks if exists
	 * @param	string	$filteronprojref	Filter on project ref
	 * @param	string	$filteronprojstatus	Filter on project status
	 * @param	string	$morewherefilter	Add more filter into where SQL request
	 * @param	string	$filteronprojuser	Filter on user that is a contact of project
	 * @param	string	$filterontaskuse	Filter on user assigned to task
	 * @return 	array						Array of tasks
	 */
  	function getTasksArray($usert=0, $userp=0, $projectid=0, $socid=0, $mode=0, $filteronprojref='', $filteronprojstatus=-1, $morewherefilter='',$filteronprojuser=0,$filterontaskuse=0,$limit=0,$ofset=1,$filterdate=0,$idpay='',$modepay=0)
  	{
  		global $conf,$langs;
  		global $request,$requestitem;

  		require_once DOL_DOCUMENT_ROOT . '/monprojet/class/projettaskadd.class.php';
  		$objecttaskadd = new Projettaskadd($this->db);
  		$tasks = array();
		//echo $usert.'-'.$userp.'-'.$projectid.'-'.$socid.'-'.$mode.'<br>';

		// List of tasks (does not care about permissions. Filtering will be done later)
  		if ($mode != 4 && $mode != 5)
  		{
  			$sql = "SELECT p.rowid as projectid, p.ref, p.title as plabel, p.public, p.fk_statut,";
  			$sql.= " t.rowid as taskid, t.ref as taskref, t.label, t.description, t.fk_task_parent, t.duration_effective, t.progress,";
  			$sql.= " t.dateo as date_start, t.datee as date_end, t.planned_workload, t.rang, t.fk_statut AS taskstatut, ";
  			$sql.= " g.fk_contrat, g.c_grupo, g.level, g.c_view, g.fk_unit, g.fk_type, g.fk_item, g.unit_program, g.unit_declared, g.unit_ejecuted, g.unit_amount, g.detail_close, g.order_ref";
  		}
  		elseif($mode == 5)
  		{
  			$sql = "SELECT p.rowid as projectid, p.ref, p.title as plabel, p.public, p.fk_statut,";
  			$sql.= " t.rowid as taskid, t.ref as taskref, t.label, t.description, t.fk_task_parent, t.duration_effective, t.progress,";
  			$sql.= " t.dateo as date_start, t.datee as date_end, t.planned_workload, t.rang, t.fk_statut AS taskstatut,";
  			$sql.= " g.fk_contrat, g.c_grupo, g.level, g.c_view, g.fk_unit, g.fk_type, g.fk_item, g.unit_program, g.unit_declared, g.unit_ejecuted, g.unit_amount, g.detail_close, g.order_ref ";
  			$sql.= ", SUM(ttd.unit_declared) AS advance ";
  			$sql.= ", py.statut AS statutpay , ttd.statut AS statutttd ";
  		}
  		else
  		{
  			$sql = "SELECT p.rowid as projectid, p.ref, p.title as plabel, p.public, p.fk_statut,";
  			$sql.= " t.rowid as taskid, t.ref as taskref, t.label, t.description, t.fk_task_parent, t.duration_effective, t.progress,";
  			$sql.= " t.dateo as date_start, t.datee as date_end, t.planned_workload, t.rang, t.fk_statut AS taskstatut,";
  			$sql.= " g.fk_contrat, g.c_grupo, g.level, g.c_view, g.fk_unit, g.fk_type, g.fk_item, g.unit_program, g.unit_declared, g.unit_ejecuted, g.unit_amount, g.detail_close, g.order_ref,";
  			$sql.= " py.rowid AS idpay, py.unit_declared AS advance";
  		}
  		if ($mode == 2 || $mode == 3)
  		{
  			$sql.= ", ri.dateo AS date_startr, ri.datee AS date_endr, ri.quant AS quantr, ri.rowid AS requestitemid, ";
  			$sql.= " r.rowid AS requestid ";
  		}
		// if ($mode == 4)
		//   $sql.= ", SUM(ttd.unit_declared) AS advance ";

  		if ($mode == 0)
  		{
  			$sql.= " FROM ".MAIN_DB_PREFIX."projet as p";
  			$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."projet_task as t ON t.fk_projet = p.rowid" ;
  			$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."projet_task_add as g ON g.fk_task = t.rowid" ;

  			$sql.= " WHERE p.entity = ".$conf->entity;
  		}
  		elseif ($mode == 1)
  		{
  			$sql.= " FROM ".MAIN_DB_PREFIX."projet as p";
  			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."projet_task as t ON t.fk_projet = p.rowid" ;
  			$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."projet_task_add as g ON g.fk_task = t.rowid" ;
  			$sql.= " WHERE p.entity = ".$conf->entity;
  		}
		elseif ($mode == 2 || $mode == 3)//lista relacionada con los requerimientos aprobados
		{
			$sql.= " FROM ".MAIN_DB_PREFIX."projet as p";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."projet_task as t on t.fk_projet = p.rowid";
			$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."projet_task_add as g ON g.fk_task = t.rowid" ;
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."request_item AS ri ON t.ref = ri.ref";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."request AS r ON ri.fk_request = r.rowid AND r.fk_projet = p.rowid ";

			$sql.= " WHERE p.entity = ".$conf->entity;
			if ($mode == 2)
				$sql.= " AND ri.fk_statut = 2";
			if ($mode == 3)
				$sql.= " AND ri.fk_statut = 3";
		}
		elseif ($mode == 4)//lista relacionada para pagos task_payment, projet_payment
		{
			$sql.= " FROM ".MAIN_DB_PREFIX."projet as p";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."projet_task as t on t.fk_projet = p.rowid";
			$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."projet_task_add as g ON g.fk_task = t.rowid" ;
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."projet_task_payment AS py ON py.fk_task = t.rowid";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."projet_payment AS pty ON py.fk_projet_payment = pty.rowid";

			$sql.= " WHERE p.entity = ".$conf->entity;
			if ($idpay)
				$sql.= " AND pty.rowid = ".$idpay;
		//if ($modepay)
			$sql.= " AND pty.statut = ".$modepay;
		}
		elseif ($mode == 5)
		{
			$sql.= " FROM ".MAIN_DB_PREFIX."projet as p";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."projet_task as t on t.fk_projet = p.rowid";
			$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."projet_task_add as g ON g.fk_task = t.rowid" ;
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."projet_task_time AS tt ON tt.fk_task = t.rowid";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."projet_task_time_doc AS ttd ON ttd.fk_task_time = tt.rowid";
			$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."projet_task_payment AS py ON py.fk_task = t.rowid";

			$sql.= " WHERE p.entity = ".$conf->entity;
			if ($modepay>0)
				$sql.= " AND ttd.statut = ".$modepay;
		}
		else return 'BadValueForParameterMode';

		if ($filteronprojuser)
		{
		// TODO
		}
		if ($filterontaskuser)
		{
		// TODO
		}
		//if ($socid)	$sql.= " AND p.fk_soc = ".$socid;
		if ($projectid) $sql.= " AND p.rowid in (".$projectid.")";
		if ($filteronprojref) $sql.= " AND p.ref LIKE '%".$filteronprojref."%'";
		if ($filteronprojstatus > -1) $sql.= " AND p.fk_statut = ".$filteronprojstatus;
		if ($morewherefilter) $sql.=$morewherefilter;
		if ($mode == 5)
		{
		//agrupamos
			$sql.= " GROUP BY p.rowid, p.ref, p.title, p.public, p.fk_statut, ";
			$sql.= " t.rowid, t.ref, t.label, t.description, t.fk_task_parent, t.duration_effective, t.progress, ";
			$sql.= " t.dateo, t.datee, t.planned_workload, t.rang, py.statut, ttd.statut ";
		}

		$sql.= " ORDER BY p.ref, t.rang, t.dateo";
		//$sql.= $this->db->plimit($limit+1, $offset);
		//echo '<hr>'.$sql;
		$this->date_ini = 0;
		$this->date_end = 0;
		dol_syslog(get_class($this)."::getTasksArray", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			$i = 0;
			// Loop on each record found, so each couple (project id, task id)
			while ($i < $num)
			{
				$error=0;

				$obj = $this->db->fetch_object($resql);

				if ((! $obj->public) && (is_object($userp)))	// If not public project and we ask a filter on project owned by a user
				{
					if (! $this->getUserRolesForProjectsOrTasks($userp, 0, $obj->projectid, 0))
					{
						$error++;
					}
				}
				if (is_object($usert)) // If we ask a filter on a user affected to a task
				{
					if (! $this->getUserRolesForProjectsOrTasks(0, $usert, $obj->projectid, $obj->taskid))
					{
						$error++;
					}
				}

				if (! $error)
				{
					$lAdd = true;
					if ($filterdate)
					{
						dol_include_once('/core/lib/date.lib.php');
						$nWeek = $conf->global->REQUEST_NUMBER_WEEK_FILTER_TASK;
						if (empty($nWeek)) $lFilter = false;
						else
							$lFilter = true;

						if ($lFilter)
						{
							$aDate=dol_getdate(dol_now());
							$dHoy = $aDate['mday'];
							$wHoy = date('W',dol_now());
							$mHoy = $aDate['mon'];
							$yHoy = $aDate['year'];

							for ($nLoop = 1; $nLoop <= $nWeek; $nLoop++)
							{
								$aPrev = dol_get_prev_week($dHoy,$wHoy,$mHoy,$yHoy);
								$dHoy = $aPrev['day'];
								$wHoy = date('W',dol_mktime(23,59,59,$aPrev['month'],$aPrev['day'],$aPrev['year'],'user'));
								$mHoy = $aPrev['month'];
								$yHoy = $aPrev['year'];
							}
							$dateo = dol_mktime(0,0,1,$aPrev['month'],$aPrev['day'],$aPrev['year'],'user');

							$aDate=dol_getdate(dol_now());
							$dHoy = $aDate['mday'];
							$wHoy = date('W',dol_now());
							$mHoy = $aDate['mon'];
							$yHoy = $aDate['year'];
							for ($nLoop = 1; $nLoop <= $nWeek; $nLoop++)
							{
								$aNext = dol_get_next_week($dHoy,$wHoy,$mHoy,$yHoy);
								$dHoy = $aNext['day'];
								$wHoy = date('W',dol_mktime(23,59,59,$aNext['month'],$aNext['day'],$aNext['year'],'user'));
								$mHoy = $aNext['month'];
								$yHoy = $aNext['year'];
							}

							$datee = dol_mktime(23,59,59,$aNext['month'],$aNext['day'],$aNext['year'],'user');

							if (
								$this->db->jdate($obj->date_start) <= $datee
								)
								$lAdd = true;
							else
								$lAdd = false;
						}
					}
					if ($lAdd )
					{
						//verificamos la fecha minima
						if (empty($this->date_ini))
							$this->date_ini = $this->db->jdate($obj->date_start);
						else
							if ($this->db->jdate($obj->date_start) > 0 && $this->db->jdate($obj->date_start) <= $this->date_ini)
								$this->date_ini = $this->db->jdate($obj->date_start);
						//verificamos la fecha maxima
							if (empty($this->date_end))
								$this->date_end = $this->db->jdate($obj->date_end);
							else
								if ($this->db->jdate($obj->date_end) > 0 && $this->db->jdate($obj->date_end) >= $this->date_end)
									$this->date_end = $this->db->jdate($obj->date_end);
						//verificamos la semana inicial
								$aDate=dol_getdate($this->db->jdate($obj->date_start));
								$dHoy = $aDate['mday'];
								$mHoy = $aDate['mon'];
								$yHoy = $aDate['year'];
								$wHoy = 0;
								if (!empty($this->db->jdate($obj->date_start)))
									$wHoy = date('W',$this->db->jdate($obj->date_start));
								if (empty($this->date_weeklya))
									$this->date_weeklya = $wHoy;
								else
									if ($wHoy <= $this->date_weeklya)
										$this->date_weeklya = $wHoy;
						//verificamos la semana final
									$aDate=dol_getdate($this->db->jdate($obj->date_end));

									$dHoy = $aDate['mday'];
									$mHoy = $aDate['mon'];
									$yHoy = $aDate['year'];
									$wHoy = 0;
									if (!empty($this->db->jdate($obj->date_end)))
										$wHoy = date('W',$this->db->jdate($obj->date_end));
									if (empty($this->date_weeklyb))
										$this->date_weeklyb = $wHoy;
									else
										if ($wHoy >= $this->date_weeklyb)
											$this->date_weeklyb = $wHoy;
						// //buscamos en objecttaskadd
						// $objecttaskadd->fetch('',$obj->taskid);
						// $objecttaskadd->id .' '.$obj->taskid;
										$newtask = new TaskAdd($this->db);
										$newtask->fetch($obj->taskid);

						// $extrafields_task = new ExtraFields($this->db);
						// $extralabels_task=$extrafields_task->fetch_name_optionals_label($newtask->table_element);
						// $res=$newtask->fetch_optionals($newtask->id,$extralabels_task);
										$tasks[$i] = new Task($this->db);
										$tasks[$i]->id				= $obj->taskid;
										$tasks[$i]->ref				= $obj->taskref;
										$tasks[$i]->fk_project		= $obj->projectid;
										$tasks[$i]->projectref		= $obj->ref;
										$tasks[$i]->projectlabel	= $obj->plabel;
										$tasks[$i]->projectstatus	= $obj->fk_statut;
										$tasks[$i]->taskstatut	= $obj->taskstatut;
										$tasks[$i]->label		= $obj->label;
										$tasks[$i]->description		= $obj->description;
										$tasks[$i]->fk_parent		= $obj->fk_task_parent;
										$tasks[$i]->duration		= $obj->duration_effective;
										$tasks[$i]->planned_workload= $obj->planned_workload;
										$tasks[$i]->progress		= $obj->progress;
										$tasks[$i]->public			= $obj->public;
										$tasks[$i]->date_start	= $this->db->jdate($obj->date_start);
										$tasks[$i]->date_end		= $this->db->jdate($obj->date_end);
										$tasks[$i]->datestart   = $obj->date_start;
										$tasks[$i]->dateend     = $obj->date_end;
										$tasks[$i]->rang	   		= $obj->rang;

										//datos adicionales de la tarea
										$tasks[$i]->array_options['options_c_grupo'] = $obj->c_grupo;
										$tasks[$i]->array_options['options_level'] = $obj->level;
										$tasks[$i]->array_options['options_c_view'] = $obj->c_view;
										$tasks[$i]->array_options['options_fk_contrat'] = $obj->fk_contrat;
										$tasks[$i]->array_options['options_unit_program'] = $obj->unit_program;
										$tasks[$i]->array_options['options_unit_amount'] = $obj->unit_amount;
										$tasks[$i]->array_options['options_unit_declared'] = $obj->unit_declared;
										$tasks[$i]->array_options['options_fk_item'] = $obj->fk_item;
										$tasks[$i]->array_options['options_fk_type'] = $obj->fk_type;
										$tasks[$i]->array_options['options_fk_unit'] = $obj->fk_unit;
										$tasks[$i]->order_ref = $obj->order_ref;

										$unit = $newtask->getLabelOfUnit($conf->global->REQUEST_USE_SHORT?'short':'',$obj->fk_unit);
										if ($unit !== '') {
											$tasks[$i]->array_options['options_unit'] = $langs->trans($unit);
										}
										$unitc = $newtask->getLabelOfUnit('code',$obj->fk_unit);
										if ($unitc !== '') {
											$tasks[$i]->array_options['options_unitc'] = $unitc;
										}

										if ($mode == 2 || $mode == 3)
										{
											$tasks[$i]->requestid     = $obj->requestid;
											$tasks[$i]->requestitemid = $obj->requestitemid;
											$tasks[$i]->date_startr   = $this->db->jdate($obj->date_startr);
											$tasks[$i]->date_endr     = $this->db->jdate($obj->date_endr);
											$tasks[$i]->quantr        = $obj->quantr;
											require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettasktimedoc.class.php';
											$objtimedoc = new Projettasktimedoc($this->db);
											$objtimedoc->getsum($obj->taskid,0,1,$obj->requestitemid);
				//(idTask,fk_payment,$statut)
											$tasks[$i]->declaredr = $objtimedoc->total;
										}
										if ($mode == 4)
											$tasks[$i]->idpay = $obj->idpay;
										if ($mode == 5)
											$tasks[$i]->statutpay = $obj->statutpay;
									}
								}
								$i++;
							}
							$this->db->free($resql);
						}
						else
						{
							dol_print_error($this->db);
						}
						return $tasks;
					}

	/**
	 *	Returns the text label from units dictionnary
	 *
	 * 	@param	string $type Label type (long or short)
	 *	@return	string|int <0 if ko, label if ok
	 */
	function getLabelOfUnit($type='long',$fk_unit='')
	{
		global $langs;

		if (!empty($fk_unit))
			$this->array_options['options_fk_unit'] = $fk_unit;

		if (!$this->array_options['options_fk_unit']) {

			return '';
		}

		$langs->load('products');
		$langs->load('monprojet@monprojet');

		$this->db->begin();

		$label_type = 'label';

		if ($type == 'short')
		{
			$label_type = 'short_label';
		}
		if ($type == 'code')
		{
			$label_type = 'code';
		}

		$sql = 'select '.$label_type.' from '.MAIN_DB_PREFIX.'c_units where rowid='.$this->array_options['options_fk_unit'];
		$resql = $this->db->query($sql);
		if($resql && $this->db->num_rows($resql) > 0)
		{
			$res = $this->db->fetch_array($resql);
			$label = $res[$label_type];
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

	/**
	 *	Return clicable name (with picto eventually)
	 *
	 *	@param	int		$withpicto		0=No picto, 1=Include picto into link, 2=Only picto
	 *	@param	string	$option			'withproject' or ''
	 *  @param	string	$mode			Mode 'task', 'time', 'contact', 'note', document' define page to link to.
	 * 	@param	int		$addlabel		0=Default, 1=Add label into string, >1=Add first chars into string
	 *  @param	string	$sep			Separator between ref and label if option addlabel is set
	 *	@return	string					Chaine avec URL
	 */
	function getNomUrlAdd($withpicto=0,$option='',$mode='task', $addlabel=0, $sep=' - ')
	{
		global $langs;

		$result='';
		$label = '<u>' . $langs->trans("ShowTask") . '</u>';
		if (! empty($this->ref))
			$label .= '<br><b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;
		if (! empty($this->label))
			$label .= '<br><b>' . $langs->trans('LabelTask') . ':</b> ' . $this->label;
		if ($this->date_start || $this->date_end)
		{
			$label .= "<br>".get_date_range($this->date_start,$this->date_end,'',$langs,0);
		}
		$linkclose = '" title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip">';

		$link = '<a href="'.DOL_URL_ROOT.'/monprojet/task/'.$mode.'.php?id='.$this->id.($option=='withproject'?'&withproject=1':'').$linkclose;
		$linkend='</a>';

		$picto='projecttask';


		if ($withpicto) $result.=($link.img_object($label, $picto, 'class="classfortooltip"').$linkend);
		if ($withpicto && $withpicto != 2) $result.=' ';
		if ($withpicto != 2) $result.=$link.$this->ref.$linkend . (($addlabel && $this->label) ? $sep . dol_trunc($this->label, ($addlabel > 1 ? $addlabel : 0)) : '');
		return $result;
	}

	/**
	 *  Load object in memory from database
	 *
	 *  @param	int		$id 	Id object
	 *  @return int		        <0 if KO, >0 if OK
	 */
	function getTimeSpent($id)
	{
		global $langs;

		$sql = "SELECT";
		$sql.= " t.rowid,";
		$sql.= " t.fk_task,";
		$sql.= " t.task_date,";
		$sql.= " t.task_datehour,";
		$sql.= " t.task_date_withhour,";
		$sql.= " t.task_duration,";
		$sql.= " t.fk_user,";
		$sql.= " t.note, ";
		$sql.= " d.unit_declared ";
		$sql.= " FROM ".MAIN_DB_PREFIX."projet_task_time as t";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."projet_task_time_doc as d ON d.fk_task_time = t.rowid";

		$sql.= " WHERE t.fk_task = ".$id;

		dol_syslog(get_class($this)."::getTimeSpent", LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$i=0;
				$num = $this->db->num_rows($resql);
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$this->lines[$i] = new Task($this->db);

					$this->lines[$i]->timespent_id	= $obj->rowid;
					$this->lines[$i]->id			= $obj->fk_task;
					$this->lines[$i]->timespent_date	= $this->db->jdate($obj->task_date);
					$this->lines[$i]->timespent_datehour  = $this->db->jdate($obj->task_datehour);
					$this->lines[$i]->timespent_withhour  = $obj->task_date_withhour;
					$this->lines[$i]->timespent_duration	= $obj->task_duration;
					$this->lines[$i]->timespent_fk_user	= $obj->fk_user;
					$this->lines[$i]->timespent_note	= $obj->note;
					$this->lines[$i]->unit_declared	= $obj->unit_declared;
					$i++;
				}
			}

			$this->db->free($resql);

			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			return -1;
		}
	}

	/*cambia de estado la tarea*/
	function update_statut($id,$statut=2)
	{
		$sql = "UPDATE ".MAIN_DB_PREFIX."projet_task SET";
		$sql.= " fk_statut=".$statut;
		$sql.= " WHERE rowid=".$id;

		$this->db->begin();

		dol_syslog(get_class($this)."::update_statut", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
	  // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
				dol_syslog(get_class($this)."::update_statut ".$errmsg, LOG_ERR);
				$this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}

	}

	/**
	 * Return list of roles for a user for each projects or each tasks (or a particular project or task).
	 *
	 * @param	User	$userp			Return roles on project for this internal user (task id can't be defined)
	 * @param	User	$usert			Return roles on task for this internal user
	 * @param 	int		$projectid		Project id list separated with , to filter on project
	 * @param 	int		$taskid			Task id to filter on a task
	 * @return 	array					Array (projectid => 'list of roles for project' or taskid => 'list of roles for task')
	 */
	function mongetUserRolesForProjectsOrTasks($userp,$usert,$projectid='',$taskid=0)
	{
		$arrayroles = array();

		dol_syslog(get_class($this)."::getUserRolesForProjectsOrTasks userp=".is_object($userp)." usert=".is_object($usert)." projectid=".$projectid." taskid=".$taskid);

		// We want role of user for a projet or role of user for a task. Both are not possible.
		if (empty($userp) && empty($usert))
		{
			$this->error="CallWithWrongParameters";
			return -1;
		}
		if (! empty($userp) && ! empty($usert))
		{
			$this->error="CallWithWrongParameters";
			return -1;
		}

		/* Liste des taches et role sur les projets ou taches */
		$sql = "SELECT pt.rowid as pid, ec.element_id, ctc.code, ctc.source";
		if ($userp) $sql.= " FROM ".MAIN_DB_PREFIX."projet as pt";
		if ($usert) $sql.= " FROM ".MAIN_DB_PREFIX."projet_task as pt";
		$sql.= ", ".MAIN_DB_PREFIX."element_contact as ec";
		$sql.= ", ".MAIN_DB_PREFIX."c_type_contact as ctc";
		$sql.= " WHERE pt.rowid = ec.element_id";
		if ($userp) $sql.= " AND ctc.element = 'project'";
		if ($usert) $sql.= " AND ctc.element = 'project_task'";
		$sql.= " AND ctc.rowid = ec.fk_c_type_contact";
		if ($userp) $sql.= " AND ec.fk_socpeople = ".$userp->id;
		if ($usert) $sql.= " AND ec.fk_socpeople = ".$usert->id;
		$sql.= " AND ec.statut = 4";
		$sql.= " AND ctc.source = 'internal'";
		if ($projectid)
		{
			if ($userp) $sql.= " AND pt.rowid in (".$projectid.")";
			if ($usert) $sql.= " AND pt.fk_projet in (".$projectid.")";
		}
		if ($taskid)
		{
			if ($userp) $sql.= " ERROR SHOULD NOT HAPPENS";
			if ($usert) $sql.= " AND pt.rowid = ".$taskid;
		}
		//print $sql;

		dol_syslog(get_class($this)."::getUserRolesForProjectsOrTasks", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			$i = 0;
			while ($i < $num)
			{
				$obj = $this->db->fetch_object($resql);
				if (empty($arrayroles[$obj->pid])) $arrayroles[$obj->pid] = $obj->code;
				else $arrayroles[$obj->pid].=','.$obj->code;
				$i++;
			}
			$this->db->free($resql);
		}
		else
		{
			dol_print_error($this->db);
		}

		return $arrayroles;
	}

	/**
	 *      Load properties id_previous and id_next
	 *
	 *      @param	string	$filter		Optional filter
	 *	 	@param  int		$fieldid   	Name of field to use for the select MAX and MIN
	 *		@param	int		$nodbprefix	Do not include DB prefix to forge table name
	 *      @return int         		<0 if KO, >0 if OK
	 */
	function load_previous_next_refadd($filter,$fieldid,$obj,$nodbprefix=0)
	{
		global $user;
		if (! $this->table_element)
		{
			dol_print_error('',get_class($this)."::load_previous_next_ref was called on objet with property table_element not defined");
			return -1;
		}
		$ref = $this->ref;
		// this->ismultientitymanaged contains
		// 0=No test on entity, 1=Test with field entity, 2=Test with link by societe
		$alias = 's';
		if ($this->element == 'societe') $alias = 'te';
		$sql = "SELECT MAX(te.".$fieldid.")";
		$sql.= " FROM ".(empty($nodbprefix)?MAIN_DB_PREFIX:'').$this->table_element." as te";
		if (isset($this->ismultientitymanaged) && $this->ismultientitymanaged == 2 || ($this->element != 'societe' && empty($this->isnolinkedbythird) && empty($user->rights->societe->client->voir))) $sql.= ", ".MAIN_DB_PREFIX."societe as s";	// If we need to link to societe to limit select to entity
		if (empty($this->isnolinkedbythird) && !$user->rights->societe->client->voir) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe_commerciaux as sc ON ".$alias.".rowid = sc.fk_soc";
		$sql.= " WHERE te.".$fieldid." < '".$this->db->escape($ref)."'";
		if (empty($this->isnolinkedbythird) && !$user->rights->societe->client->voir) $sql.= " AND sc.fk_user = " .$user->id;
		if (! empty($filter)) $sql.=" AND ".$filter;
		if (isset($this->ismultientitymanaged) && $this->ismultientitymanaged == 2 || ($this->element != 'societe' && empty($this->isnolinkedbythird) && !$user->rights->societe->client->voir)) $sql.= ' AND te.fk_soc = s.rowid';			// If we need to link to societe to limit select to entity
		if (isset($this->ismultientitymanaged) && $this->ismultientitymanaged == 1) $sql.= ' AND te.entity IN ('.getEntity($this->element, 1).')';

		//print $sql."<br>";
		$result = $this->db->query($sql);
		if (! $result)
		{
			$this->error=$this->db->lasterror();
			return -1;
		}
		$row = $this->db->fetch_row($result);
	//obtenemos el id del projecto
		$fk_projet = $obj->fk_project;
	//buscamos el id de la tarea resultante
		$filter_ = array('ref'=> $row[0]);
		$filterstatic = " AND t.fk_projet = ".$fk_projet;
		$res = $obj->fetchall('','',0,0,$filter_,'AND',$filterstatic,true);
		if ($res==1)
			$this->ref_previous = $obj->id;
		else
			$this->ref_previous = $row[0];
		$sql = "SELECT MIN(te.".$fieldid.")";
		$sql.= " FROM ".(empty($nodbprefix)?MAIN_DB_PREFIX:'').$this->table_element." as te";
		if (isset($this->ismultientitymanaged) && $this->ismultientitymanaged == 2 || ($this->element != 'societe' && empty($this->isnolinkedbythird) && !$user->rights->societe->client->voir)) $sql.= ", ".MAIN_DB_PREFIX."societe as s";	// If we need to link to societe to limit select to entity
		if (empty($this->isnolinkedbythird) && !$user->rights->societe->client->voir) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe_commerciaux as sc ON ".$alias.".rowid = sc.fk_soc";
		$sql.= " WHERE te.".$fieldid." > '".$this->db->escape($ref)."'";
		if (empty($this->isnolinkedbythird) && !$user->rights->societe->client->voir) $sql.= " AND sc.fk_user = " .$user->id;
		if (! empty($filter)) $sql.=" AND ".$filter;
		if (isset($this->ismultientitymanaged) && $this->ismultientitymanaged == 2 || ($this->element != 'societe' && empty($this->isnolinkedbythird) && !$user->rights->societe->client->voir)) $sql.= ' AND te.fk_soc = s.rowid';			// If we need to link to societe to limit select to entity
		if (isset($this->ismultientitymanaged) && $this->ismultientitymanaged == 1) $sql.= ' AND te.entity IN ('.getEntity($this->element, 1).')';
		// Rem: Bug in some mysql version: SELECT MIN(rowid) FROM llx_socpeople WHERE rowid > 1 when one row in database with rowid=1, returns 1 instead of null

		//print $sql."<br>";
		$result = $this->db->query($sql);
		if (! $result)
		{
			$this->error=$this->db->lasterror();
			return -2;
		}
		$row = $this->db->fetch_row($result);

	//buscamos el id de la tarea resultante
		$filter = array('ref'=> $row[0]);
		$filterstatic = " AND t.fk_projet = ".$fk_projet;
		$res = $obj->fetchall('','',0,0,$filter,'AND',$filterstatic,true);
		if ($res==1)
			$this->ref_next = $obj->id;
		else
			$this->ref_next = $row[0];

		return 1;
	}

	/**
	 *  Load object in memory from database
	 *
	 *  @param	int		$id			Id object
	 *  @param	int		$ref		ref object
	 *  @return int 		        <0 if KO, >0 if OK
	 */
	public function get_counttask($fk_parent,$fk_projet,$id=0,$statut=0)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		global $langs;

		$sql = "SELECT";
		$sql.= " t.rowid,";
		$sql.= " t.fk_statut ";
		$sql.= " FROM ".MAIN_DB_PREFIX."projet_task as t";
		//filtros
		$sql.= " WHERE t.fk_task_parent = ".$fk_parent;
		$sql.= " AND fk_projet = ".$fk_projet;
		if ($id) $sql.= " AND t.rowid != ".$id;
		if ($statut)
			$sql.= " AND t.fk_statut = ".$statut;
		$this->lines = array();
	  	//echo '<hr>sql '.$sql;
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			while ($obj = $this->db->fetch_object($resql))
			{
				$line = new TaskLine();
				$line->id			= $obj->rowid;
				$line->fk_statut		= $obj->fk_statut;
				$this->lines[] = $line;
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

	/**
	 *  Load object in memory from database
	 *	for order_ref
	 *  @param	int		$id			Id object
	 *  @param	int		$ref		ref object
	 *  @return int 		        <0 if KO, >0 if OK
	 */
	public function get_ordertask($fk_projet,$statut=0)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		global $langs;

		$sql = "SELECT ";
		$sql.= " t.rowid,";
		$sql.= " t.fk_statut, ";
		$sql.= " ta.order_ref ";
		$sql.= " FROM ".MAIN_DB_PREFIX."projet_task as t";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."projet_task_add AS ta ON ta.fk_task = t.rowid ";
		//filtros
		$sql.= " WHERE t.fk_projet = ".$fk_projet;
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
				$line = new TaskLine();
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

    /**
     *  Update database
     *
     *  @param	User	$user        	User that modify
     *  @param  int		$notrigger	    0=launch triggers after, 1=disable triggers
     *  @return int			         	<0 if KO, >0 if OK
     */
    function update_rang($user=null)
    {
    	global $conf, $langs;
    	$error=0;

        // Clean parameters

    	if (isset($this->rang)) $this->rang=trim($this->rang);

        // Check parameters
        // Put here code to add control on parameters values

        // Update request
    	$sql = "UPDATE ".MAIN_DB_PREFIX."projet_task SET";
    	$sql.= " rang=".((!empty($this->rang))?$this->rang:"0");
    	$sql.= " WHERE rowid=".$this->id;

    	$this->db->begin();

    	dol_syslog(get_class($this)."::update_rang", LOG_DEBUG);
    	$resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
        // Commit or rollback
    	if ($error)
    	{
    		foreach($this->errors as $errmsg)
    		{
    			dol_syslog(get_class($this)."::update ".$errmsg, LOG_ERR);
    			$this->error.=($this->error?', '.$errmsg:$errmsg);
    		}
    		$this->db->rollback();
    		return -1*$error;
    	}
    	else
    	{
    		$this->db->commit();
    		return 1;
    	}
    }

    /**
     *  Update database
     *
     *  @param	User	$user        	User that modify
     *  @param  int		$notrigger	    0=launch triggers after, 1=disable triggers
     *  @return int			         	<0 if KO, >0 if OK
     */
    function update_dategroup($user=null)
    {
    	global $conf, $langs;
    	$error=0;

        // Update request
    	$sql = "UPDATE ".MAIN_DB_PREFIX."projet_task SET";
    	$sql.= " dateo=".($this->date_start!=''?"'".$this->db->idate($this->date_start)."'":'null').",";
    	$sql.= " datee=".($this->date_end!=''?"'".$this->db->idate($this->date_end)."'":'null');
    	$sql.= " WHERE rowid=".$this->id;
		//echo '<hr>'.$sql;
    	$this->db->begin();

    	dol_syslog(get_class($this)."::update_dategroup", LOG_DEBUG);
    	$resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

        // Commit or rollback
    	if ($error)
    	{
    		foreach($this->errors as $errmsg)
    		{
    			dol_syslog(get_class($this)."::update_dategroup ".$errmsg, LOG_ERR);
    			$this->error.=($this->error?', '.$errmsg:$errmsg);
    		}
    		$this->db->rollback();
    		return -1*$error;
    	}
    	else
    	{
    		$this->db->commit();
    		return 1;
    	}
    }

}

/**
 * Class TypeitemLine
*/
class TaskLine
{

	public $id;

	public $ref;

	public $fk_project;
	public $fk_task_parent;
	public $label;
	public $description;
	public $duration_effective;		// total of time spent on this task
	public $planned_workload;
	public $date_c;
	public $date_start;
	public $date_end;
	public $progress;
	public $priority;
	public $fk_user_creat;
	public $fk_user_valid;
	public $statut;
	public $note_private;
	public $note_public;
	public $rang;

	public $timespent_id;
	public $timespent_duration;
	public $timespent_old_duration;
	public $timespent_date;
	public $timespent_datehour;		// More accurate start date (same than timespent_date but includes hours, minutes and seconds)
	public $timespent_withhour;		// 1 = we entered also start hours for timesheet line
	public $timespent_fk_user;
	public $timespent_note;


	/**
	 * @var mixed Sample line property 2
	 */

}

?>