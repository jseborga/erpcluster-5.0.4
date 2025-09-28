<?php
require_once DOL_DOCUMENT_ROOT.'/poa/class/poaactivity.class.php';

class Poaactivityext extends Poaactivity
{
			//MODIFICADO
	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function fetch_next_nro($period_year)
	{
		global $langs,$conf;
		$sql = "SELECT";
		$sql.= " MAX(t.nro_activity) AS maxnro ";

		$sql.= " FROM ".MAIN_DB_PREFIX."poa_activity as t";
		$sql.= " WHERE t.entity = ".$conf->entity;
		$sql.= " AND t.gestion = ".$period_year;

		dol_syslog(get_class($this)."::fetch_next_nro sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);
				return $obj->maxnro + 1;
			}
			else
				return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch_next_nro ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getlist_poa($fk_poa,$fk_user=0)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.gestion,";
		$sql.= " t.fk_poa,";
		$sql.= " t.fk_pac,";
		$sql.= " t.fk_prev_ant,";
		$sql.= " t.fk_prev,";
		$sql.= " t.fk_area,";
		$sql.= " t.code_requirement,";
		$sql.= " t.label,";
		$sql.= " t.pseudonym,";
		$sql.= " t.nro_activity,";
		$sql.= " t.date_activity,";
		$sql.= " t.partida,";
		$sql.= " t.amount,";
		$sql.= " t.priority,";
		$sql.= " t.datec,";
		$sql.= " t.datem,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.fk_user_mod,";
		$sql.= " t.tms,";
		$sql.= " t.statut,";
		$sql.= " t.active";

		$sql.= " FROM ".MAIN_DB_PREFIX."poa_activity as t";
		$sql.= " WHERE t.fk_poa = ".$fk_poa;
		$sql.= " AND t.statut != -1";
		if ($fk_user>0)
			$sql.= " AND t.fk_user_create = ".$fk_user;
		dol_syslog(get_class($this)."::getlist_poa sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);

		$this->array = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($this->db->num_rows($resql))
			{
				include_once DOL_DOCUMENT_ROOT.'/poa/activity/class/poaactivitydet.class.php';
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objnew = new Poaactivity($this->db);

					$objnew->id    = $obj->rowid;

					$objnew->entity = $obj->entity;
					$objnew->gestion = $obj->gestion;
					$objnew->fk_poa = $obj->fk_poa;
					$objnew->fk_pac = $obj->fk_pac;
					$objnew->fk_prev_ant = $obj->fk_prev_ant;
					$objnew->fk_prev = $obj->fk_prev;
					$objnew->fk_area = $obj->fk_area;
					$objnew->code_requirement = $obj->code_requirement;
					$objnew->label = $obj->label;
					$objnew->pseudonym = $obj->pseudonym;
					$objnew->nro_activity = $obj->nro_activity;
					$objnew->date_activity = $this->db->jdate($obj->date_activity);
					$objnew->partida = $obj->partida;
					$objnew->amount = $obj->amount;
					$objnew->priority = $obj->priority;
					$objnew->datec = $this->db->jdate($obj->datec);
					$objnew->fk_user_create = $obj->fk_user_create;
					$objnew->tms = $this->db->jdate($obj->tms);
					$objnew->statut = $obj->statut;
					$objnew->active = $obj->active;
					$objnewdet = new Poaactivitydet($this->db);
					$objnewdet->getlist($obj->rowid);
					if (count($objnewdet->array))
						$objnew->array_options = $objnewdet->array;
					else
						$objnew->array_options = array();
					$this->array[$obj->rowid] = $objnew;
					$i++;
				}
				$this->db->free($resql);
				return $num;
			}
			$this->db->free($resql);
			return 0;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param  int     $id    Id object
	 *  @return int             <0 if KO, >0 if OK
	 */
	function getlist_poa_user($fk_poa=0,$fk_user=0,$period_year=0)
	{
		global $langs,$conf;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.gestion,";
		$sql.= " t.fk_poa,";
		$sql.= " t.fk_pac,";
		$sql.= " t.fk_prev_ant,";
		$sql.= " t.fk_prev,";
		$sql.= " t.fk_area,";
		$sql.= " t.code_requirement,";
		$sql.= " t.label,";
		$sql.= " t.pseudonym,";
		$sql.= " t.nro_activity,";
		$sql.= " t.date_activity,";
		$sql.= " t.partida,";
		$sql.= " t.amount,";
		$sql.= " t.priority,";
		$sql.= " t.date_create,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut,";
		$sql.= " t.active";

		$sql.= " FROM ".MAIN_DB_PREFIX."poa_activity as t";
		$sql.= " WHERE t.entity = ".$conf->entity;
		if ($fk_poa>0) $sql.= " AND t.fk_poa = ".$fk_poa;
		if ($fk_user>0) $sql.= " AND t.fk_user_create = ".$fk_user;
		if ($period_year >0 ) $sql.= " AND t.gestion = ".$period_year;
		$sql.= " AND t.statut != -1";
		dol_syslog(get_class($this)."::getlist_poa sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->array = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($this->db->num_rows($resql))
			{
				include_once DOL_DOCUMENT_ROOT.'/poa/activity/class/poaactivitydet.class.php';
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objnew = new Poaactivity($this->db);

					$objnew->id    = $obj->rowid;

					$objnew->entity = $obj->entity;
					$objnew->gestion = $obj->gestion;
					$objnew->fk_poa = $obj->fk_poa;
					$objnew->fk_pac = $obj->fk_pac;
					$objnew->fk_prev_ant = $obj->fk_prev_ant;
					$objnew->fk_prev = $obj->fk_prev;
					$objnew->fk_area = $obj->fk_area;
					$objnew->code_requirement = $obj->code_requirement;
					$objnew->label = $obj->label;
					$objnew->pseudonym = $obj->pseudonym;
					$objnew->nro_activity = $obj->nro_activity;
					$objnew->date_activity = $this->db->jdate($obj->date_activity);
					$objnew->partida = $obj->partida;
					$objnew->amount = $obj->amount;
					$objnew->priority = $obj->priority;
					$objnew->date_create = $this->db->jdate($obj->date_create);
					$objnew->fk_user_create = $obj->fk_user_create;
					$objnew->tms = $this->db->jdate($obj->tms);
					$objnew->statut = $obj->statut;
					$objnew->active = $obj->active;
					$objnewdet = new Poaactivitydet($this->db);
					$objnewdet->getlist($obj->rowid);
					if (count($objnewdet->array))
						$objnew->array_options = $objnewdet->array;
					else
						$objnew->array_options = array();
					$this->array[$fk_user][$obj->rowid] = $objnew;
					$i++;
				}
				$this->db->free($resql);
				return $num;
			}
			$this->db->free($resql);
			return 0;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/*
	* sumar y contar las actividades por usuario
	*/
	function resume_activity_user($fk_poa=0,$fk_user=0,$period_year=0)
	{
		global $langs,$conf;
		//agregamos poapartidapre
		require_once DOL_DOCUMENT_ROOT.'/poa/class/poapartidapreext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/poa/process/class/poaprocess.class.php';
		$objpp = new Poapartidapre($this->db);
		$objproc = new Poaprocess($this->db);
		$res = $this->getlist_poa_user($fk_poa,$fk_user,$period_year);
		$this->aPac = array();
		$this->aPacne = array();
		$this->aCount = array();
		$this->aCountfin = array();
		$this->aSum = array();
		$this->aSumfin = array();
		if ($res>0)
		{
			foreach ((array) $this->array AS $userid => $aData)
			{
				foreach ((array) $aData AS $poaid => $obj)
				{
					$this->aCount[$fk_user]++;
					$amount = $obj->amount;
					if ($obj->fk_prev>0)
					{
						//revisamos otros datos
						$aLisprev = prev_ant($obj->fk_prev,$aLisprev,'0,1');
						$data = $aLisprev[$obj->fk_prev];
						//proceso
						if ($data['idprocessant'])
							$idProcess = $data['idprocessant'];
						else
							$idProcess = $data['idprocess'];
						//revisamos el proceso
						$objproc->fetch($idProcess);
						if ($idProcess == $objproc->id)
							$this->aPac[$fk_user][$obj->fk_pac] = dol_getdate($objproc->date_process);
						$amount = $objpp->getsum($obj->fk_prev);
					}
					else
					{
						if ($obj->fk_pac > 0)
							$this->aPacne[$fk_user][$obj->fk_pac] = $obj->fk_pac;
					}
					$this->aSum[$fk_user]+=$amount;
					if ($obj->statut == 9)
					{
						$this->aCountfin[$fk_user]++;
						$this->aSumfin[$fk_user]+= $amount;
					}
				}
			}
			return $res;
		}
		return $res;
	}

	/**
	 *  Return combo list of activated countries, into language of user
	 *
	 *  @param	string	$selected       Id or Code or Label of preselected country
	 *  @param  string	$htmlname       Name of html select object
	 *  @param  string	$htmloption     Options html on select object
	 *  @param	string	$maxlength		Max length for labels (0=no limit)
	 *  @return string           		HTML string with select
	 */
	function select_activity($selected='',$htmlname='fk_activity',$htmloption='',$maxlength=0,$showempty=0,$id=0,$required='',$filterarray='',$filter='')
	{
		global $conf,$langs;

		$langs->load("poa@poa");
		if ($required)
			$required = 'required';
		$out='';
		$countryArray=array();
		$label=array();

		$sql = "SELECT c.rowid, c.nro_activity as code_iso, c.label as label";
		$sql.= " FROM ".MAIN_DB_PREFIX."poa_activity AS c ";
		$sql.= " WHERE c.entity = ".$conf->entity;
		if ($id)
			$sql.= " AND c.rowid NOT IN (".$id.")";
		if ($filter)
			$sql.= $filter;
		$sql.= " ORDER BY c.label ASC";

		dol_syslog(get_class($this)."::select_activity sql=".$sql);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$out.= '<select id="select'.$htmlname.'" class="flat selectpays" '.$required.' name="'.$htmlname.'" '.$htmloption.'>';
			if ($showempty)
			{
				$out.= '<option value="-1"';
				if ($selected == -1) $out.= ' selected="selected"';
				$out.= '>&nbsp;</option>';
			}

			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($num)
			{
				$foundselected=false;

				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$lAdd = true;
					if (!empty($filterarray) && count($filterarray)>0)
					{
						if ($filterarray[$obj->rowid])
							$lAdd = true;
						else
							$lAdd = false;
					}
					if ($lAdd)
					{
						$countryArray[$i]['rowid'] 		= $obj->rowid;
						$countryArray[$i]['code_iso'] 	= $obj->code_iso;
						$countryArray[$i]['label']		= ($obj->code_iso && $langs->transnoentitiesnoconv("Area".$obj->code_iso)!="Area".$obj->code_iso?$langs->transnoentitiesnoconv("Area".$obj->code_iso):($obj->label!='-'?$obj->label:''));
						$label[$i] 	= $countryArray[$i]['label'];
					}
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
			dol_print_error($this->db);
		}

		return $out;
	}

}
?>