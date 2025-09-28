<?php
require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettask.class.php';

class Budgettaskext extends Budgettask
{

	/**
	 *  Return a link to the object card (with optionaly the picto)
	 *
	 *	@param	int		$withpicto			Include picto in link (0=No picto, 1=Include picto into link, 2=Only picto)
	 *	@param	string	$option				On what the link point to
     *  @param	int  	$notooltip			1=Disable tooltip
     *  @param	int		$maxlen				Max length of visible user name
     *  @param  string  $morecss            Add more css on link
	 *	@return	string						String with URL
	 */
	function getNomUrladd($withpicto=0, $option='', $notooltip=0, $maxlen=24, $morecss='')
	{
		global $db, $conf, $langs;
        global $dolibarr_main_authentication, $dolibarr_main_demo;
        global $menumanager;

        if (! empty($conf->dol_no_mouse_hover)) $notooltip=1;   // Force disable tooltips

        $result = '';
        $companylink = '';

        $label = '<u>' . $langs->trans("Budgettask") . '</u>';
        $label.= '<br>';
        $label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

        $url = DOL_URL_ROOT.'/budget/budget/'.'task.php?id='.$this->fk_budget.'&idr='.$this->id.'&action=viewtask';

        $linkclose='';
        if (empty($notooltip))
        {
            if (! empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER))
            {
                $label=$langs->trans("ShowProject");
                $linkclose.=' alt="'.dol_escape_htmltag($label, 1).'"';
            }
            $linkclose.=' title="'.dol_escape_htmltag($label, 1).'"';
            $linkclose.=' class="classfortooltip'.($morecss?' '.$morecss:'').'"';
        }
        else $linkclose = ($morecss?' class="'.$morecss.'"':'');

		$linkstart = '<a href="'.$url.'"';
		$linkstart.=$linkclose.'>';
		$linkend='</a>';

        if ($withpicto)
        {
            $result.=($linkstart.img_object(($notooltip?'':$label), 'label', ($notooltip?'':'class="classfortooltip"')).$linkend);
            if ($withpicto != 2) $result.=' ';
		}
		$result.= $linkstart . $this->ref . $linkend;
		return $result;
	}

	/**
	 * Load object in memory from the database
	 *
	 * @param string $sortorder Sort Order
	 * @param string $sortfield Sort field
	 * @param int    $limit     offset limit
	 * @param int    $offset    offset limit
	 * @param array  $filter    filter array
	 * @param string $filtermode filter mode (AND or OR)
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function fetchItems($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lView=false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.entity,";
		$sql .= " t.ref,";
		$sql .= " t.fk_budget,";
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
		$sql .= " t.model_pdf";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element. ' as t';
		$sql .= ' INNER JOIN ' . MAIN_DB_PREFIX . 'budget_task_add'. ' as e ON e.fk_budget_task = t.rowid';
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

		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield,$sortorder);
		}
		if (!empty($limit)) {
			$sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
		}
		$this->lines = array();

		$resql = $this->db->query($sql);
		//print_r($resql);
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new BudgettaskLine();

				$line->id = $obj->rowid;

				$line->entity = $obj->entity;
				$line->ref = $obj->ref;
				$line->fk_budget = $obj->fk_budget;
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

				$this->lines[$line->id] = $line;
			}
			$this->db->free($resql);

			return $num;
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return - 1;
		}
	}

	function select_group($id,$selected='',$htmlname='fk_father',$htmloption='',$showempty=0,$campoid='rowid')
	{
		global $db, $langs, $conf;
		$sql = "SELECT f.rowid, f.ref AS code, f.label AS libelle FROM ".MAIN_DB_PREFIX."budget_task AS f ";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."budget_task_add AS t ON t.fk_budget_task = f.rowid";
		$sql.= " WHERE ";
		$sql.= " t.c_grupo = 1";
		$sql.= " AND f.fk_budget = ".$id;
		$sql.= " ORDER BY f.label";
		$resql = $this->db->query($sql);
		$html = '';
		if ($resql)
			$html = htmlselect($resql,$selected,$htmlname,$htmloption,$showempty,$campoid);
		return $html;
	}
	function max_group($id,$idg=0,$group=1)
	{
		global $db, $langs, $conf;
		$max = 1;
		$sql = "SELECT f.rowid, f.ref, t.level FROM ".MAIN_DB_PREFIX."budget_task AS f ";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."budget_task_add AS t ON t.fk_budget_task = f.rowid";
		$sql.= " WHERE f.fk_budget = ".$id;
		if ($group) $sql.= " AND t.c_grupo = 1";
		if ($idg) $sql.= " AND f.fk_task_parent = ".$idg;
		else
			$sql.= " AND t.level = 0";
		$sql.= " ORDER BY f.ref";

		$resql = $this->db->query($sql);
		//vamos a obtener el maximo numoro registrado en ref
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num > 0)
			{
				while ($obj = $this->db->fetch_object($resql))
				{
					$level = $obj->level;
					if (empty($level))
					{
						$ref = $obj->ref;
					}
					else
					{

						$aRef = explode('.',$obj->ref);
						$ref = $aRef[$level];
					}

					if ($max <= $ref) $max = $ref;
				}
				$max++;
			}
			else
			{
				$max = 1;
			}
		}
		return $max;
	}

	function max_task($id)
	{
		global $db, $langs, $conf;
		$prefix = $conf->global->BUDGET_DEFAULT_PREFIX_TASK;
		$nprefix = ($conf->global->BUDGET_DEFAULT_NCHARACTER_TASK?$conf->global->BUDGET_DEFAULT_NCHARACTER_TASK:3);
		$numprefix = STRLEN($prefix);

		$max = 1;
		$sql = "SELECT f.rowid, f.ref, t.level FROM ".MAIN_DB_PREFIX."budget_task AS f ";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."budget_task_add AS t ON t.fk_budget_task = f.rowid";
		$sql.= " WHERE f.fk_budget = ".$id;
		$sql.= " AND t.c_grupo = 0";
		$sql.= " ORDER BY f.ref";

		$resql = $this->db->query($sql);
		//vamos a obtener el maximo numoro registrado en ref
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num > 0)
			{
				while ($obj = $this->db->fetch_object($resql))
				{
					$ref = $obj->ref;
					$lastref = substr($ref,$numprefix,100);

					if ($max <= $lastref) $max = $lastref;

				}
				$max++;
			}
			else
			{
				$max = 1;
			}
		}
		$max = str_pad($max, $nprefix, "0", STR_PAD_LEFT);
		return $prefix.$max;
	}

	function next_ref($id,$idg)
	{
		global $db, $langs, $conf;
		$ref = $this->ref;
		$aRef = explode('.',$this->ref);

		$num = count($aRef);
		$newnum = $num - 1;
		$newref = '';
		for($i=0; $i < $newnum; $i++)
		{
			if ($newref) $newref.='.';
			$newref.=$aRef[$i];
		}
		if ($num >0)
		{
			$numtmp = $num-1;
			$nref = (is_numeric($aRef[$numtmp])?$aRef[$numtmp]:0)+1;
			$newref.= '.'.$nref;
			//buscamos
			$obj = new Budgettask($this->db);
			$filterstatic = " AND t.fk_task_parent = ".$idg;
			$filterstatic = " AND t.ref = '".$newref."'";
			$res = $obj->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic,true);
			if ($res == 1) return $obj->id;
		}
		return -1;
	}

	function previous_ref($id,$idg)
	{
		global $db, $langs, $conf;
		$ref = $this->ref;
		$aRef = explode('.',$this->ref);
		$num = count($aRef);
		$newnum = $num - 1;
		$newref = '';
		for($i=0; $i < $newnum; $i++)
		{
			if ($newref) $newref.='.';
			$newref.=$aRef[$i];
		}
		if ($num >0)
		{
			$numtmp = $num-1;
			$nref = (is_numeric($aRef[$numtmp])?$aRef[$numtmp]:0)-1;
			//$nref = $aRef[$num-1]-1;
			if ($nref>0)
			{
				$newref.= '.'.$nref;
				//buscamos
				$obj = new Budgettask($this->db);
				$filterstatic = " AND t.fk_task_parent = ".$idg;
				$filterstatic = " AND t.ref = '".$newref."'";
				$res = $obj->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic,true);
				if ($res == 1) return $obj->id;
			}
		}
		return -1;
	}

	function calculate_performance($fk_budget_task)
	{
		global $db,$conf;
		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.entity,";
		$sql .= " t.ref,";
		$sql .= " t.fk_budget,";
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
		$sql .= " t.model_pdf";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element. ' as t';
		$sql .= ' INNER JOIN ' . MAIN_DB_PREFIX . 'budget_task_add'. ' as e ON e.fk_budget_task = t.rowid';
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

		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield,$sortorder);
		}
		if (!empty($limit)) {
			$sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
		}
		$this->lines = array();

		$resql = $this->db->query($sql);
		//print_r($resql);
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new BudgettaskLine();

				$line->id = $obj->rowid;

				$line->entity = $obj->entity;
				$line->ref = $obj->ref;
				$line->fk_budget = $obj->fk_budget;
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

				$this->lines[$line->id] = $line;
			}
			$this->db->free($resql);

			return $num;
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return - 1;
		}
	}

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
		$sql = "UPDATE ".MAIN_DB_PREFIX.$this->element." SET";
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
		$sql.= " FROM " . MAIN_DB_PREFIX . "budget_task as p";
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
			$sql.= " AND ( ( ctc.rowid = ec.fk_c_type_contact";
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
				$objet = new Budgettaskext($this->db);
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
}

?>