<?php
require_once DOL_DOCUMENT_ROOT.'/poa/class/poapartidapre.class.php';

class Poapartidapreext extends Poapartidapre
{

	//modificado
	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getlist($fk_poa_prev, $lvalor='S')
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.fk_poa_prev,";
		$sql.= " t.fk_structure,";
		$sql.= " t.fk_poa,";
		$sql.= " t.partida,";
		$sql.= " t.amount,";
		$sql.= " t.tms,";
		$sql.= " t.statut,";
		$sql.= " t.active";


		$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_pre as t";
		$sql.= " WHERE t.fk_poa_prev = ".$fk_poa_prev;
		$sql.= " AND t.statut = 1 ";
		//lvalor == S   para listar los valores positivos
		//lvalor == N   para listar los valores negativos (modificaciones al preventivo)
		if ($lvalor == 'S')
			$sql.= " AND t.amount >= 0";
		else
			$sql.= " AND t.amount < 0";

		dol_syslog(get_class($this)."::getlist sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->array = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objnew = new Poapartidapre($this->db);

					$objnew->id    = $obj->rowid;

					$objnew->fk_poa_prev = $obj->fk_poa_prev;
					$objnew->fk_structure = $obj->fk_structure;
					$objnew->fk_poa = $obj->fk_poa;
					$objnew->partida = $obj->partida;
					$objnew->amount = $obj->amount;
					$objnew->tms = $this->db->jdate($obj->tms);
					$objnew->statut = $obj->statut;
					$objnew->active = $obj->active;
					$this->array[$obj->rowid] = $objnew;
					$i++;
				}
			}
			$this->db->free($resql);
			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getlist ".$this->error, LOG_ERR);
			return -1;
		}
	}


	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 * $statut      int             1  mayor a 0; 0 mayor o igual a 0
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getlist_poa($fk_poa,$statut=1)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.fk_poa_prev,";
		$sql.= " t.fk_structure,";
		$sql.= " t.fk_poa,";
		$sql.= " t.partida,";
		$sql.= " t.amount,";
		$sql.= " t.tms,";
		$sql.= " t.statut,";
		$sql.= " t.active,";
		$sql.= " p.rowid AS previd,";
		$sql.= " p.fk_pac,";
		$sql.= " p.nro_preventive,";
		$sql.= " p.label,";
		$sql.= " p.fk_user_create";

		$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_pre as t";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_prev AS p ON t.fk_poa_prev = p.rowid";
		$sql.= " WHERE t.fk_poa = ".$fk_poa;
		if ($statut == 1)
			$sql.= " AND p.statut > 0 ";
		if ($statut == 0)
			$sql.= " AND p.statut >= 0 ";

		dol_syslog(get_class($this)."::getlist_poa sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->array = array();

		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objnew = new Poapartidapre($this->db);

					$objnew->id    = $obj->rowid;

					$objnew->fk_poa_prev = $obj->fk_poa_prev;
					$objnew->fk_structure = $obj->fk_structure;
					$objnew->fk_poa = $obj->fk_poa;
					$objnew->partida = $obj->partida;
					$objnew->amount = $obj->amount;
					$objnew->tms = $this->db->jdate($obj->tms);
					$objnew->statut = $obj->statut;
					$objnew->active = $obj->active;
					$objnew->fk_user_create = $obj->fk_user_create;
					$objnew->label = $obj->label;
					$objnew->nro_preventive = $obj->nro_preventive;
					$objnew->previd = $obj->previd;
					$objnew->fk_pac = $obj->fk_pac;
					$this->array[$obj->rowid] = $objnew;
					$i++;
				}
			}
			$this->db->free($resql);
			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getlist_poa ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getsum($fk_poa_prev)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.fk_poa_prev,";
		$sql.= " SUM(t.amount) AS total ";

		$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_pre as t";
		$sql.= " WHERE t.fk_poa_prev = ".$fk_poa_prev;
		$sql.= " AND t.statut = 1";
		$sql.= " GROUP BY t.fk_poa_prev ";
		dol_syslog(get_class($this)."::getsum sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$obj = $this->db->fetch_object($resql);
				return $obj->total;
			}
			$this->db->free($resql);
			return 0;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getsum ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getsumpartida($fk_poa_prev)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.fk_structure,";
		$sql.= " t.fk_poa,";
		$sql.= " t.partida,";
		$sql.= " SUM(t.amount) AS total ";

		$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_pre as t";
		$sql.= " WHERE t.fk_poa_prev = ".$fk_poa_prev;
		$sql.= " AND t.statut = 1";
		$sql.= " GROUP BY t.fk_structure, t.fk_poa, t.partida ";
		dol_syslog(get_class($this)."::getsumpartida sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->arraysum = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i  = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$this->arraysum[$i]['fk_structure'] = $obj->fk_structure;
					$this->arraysum[$i]['fk_poa'] = $obj->fk_poa;
					$this->arraysum[$i]['partida'] = $obj->partida;
					$this->arraysum[$i]['amount'] += $obj->total;
					$i++;
				}
				$this->db->free($resql);
				return 1;
			}
			$this->db->free($resql);
			return 0;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getsumpartida ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getsum_str_part($gestion,$fk_structure,$fk_poa,$partida)
	{
		global $langs,$conf;
		$sql = "SELECT";
		$sql.= " r.gestion, t.fk_structure, t.partida,";
		$sql.= " SUM(t.amount) AS total ";

		$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_pre as t";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_prev AS r ON t.fk_poa_prev = r.rowid ";
		$sql.= " WHERE r.gestion = ".$gestion;
		$sql.= " AND t.fk_structure = ".$fk_structure;
		$sql.= " AND t.fk_poa = ".$fk_poa;
		$sql.= " AND t.partida = '".$partida."' ";
		$sql.= " AND t.statut = 1";
		$sql.= " AND r.statut > 0";
		$sql.= " AND r.entity = ".$conf->entity;
		$sql.= " GROUP BY r.gestion, t.fk_structure, t.partida ";
		dol_syslog(get_class($this)."::getsum_str_part sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);

		$this->total = 0;
		if ($resql)
		{
			$this->total = 0;
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$obj = $this->db->fetch_object($resql);
				$this->total = $obj->total;
			}
			$this->db->free($resql);
			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getsum_str_part ".$this->error, LOG_ERR);
			return -1;
		}
	}

		/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
		function getsum_str_part_prev($fk_prev,$gestion,$fk_structure,$fk_poa,$partida)
		{
			global $langs;
			$this->total = 0;
	//obtenemos los hijos
			$total = 0;
			$totalpadre = 0;
	//echo '<hr>fk_prev_father '.$fk_prev.' ges '.$gestion.' str '.$fk_structure.' poa '.$fk_poa.' part '.$partida;

	//buscamos y recuperamos si tiene hijos
			$total = $this->getsum_son($fk_prev,$gestion,$fk_structure,$fk_poa,$partida);
			$totalpadre = $this->getsum_strpartprev($fk_prev,$gestion,$fk_structure,$fk_poa,$partida);
			$this->total = $total + $totalpadre;
			return 1;
		}

		/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
		function getsum_strpartprev($fk_prev,$gestion,$fk_structure,$fk_poa,$partida)
		{
			global $langs;
			$sql = "SELECT";
			$sql.= " r.gestion, t.fk_structure, t.partida,";
			$sql.= " SUM(t.amount) AS total ";

			$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_pre as t";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_prev AS r ON t.fk_poa_prev = r.rowid ";
			$sql.= " WHERE r.gestion = ".$gestion;
			$sql.= " AND t.fk_poa = ".$fk_poa;
			$sql.= " AND t.partida = '".$partida."' ";
			$sql.= " AND t.fk_poa_prev = ".$fk_prev;
			$sql.= " AND t.statut = 1";
			$sql.= " AND r.statut > 0";
			$sql.= " GROUP BY r.gestion, t.fk_structure, t.partida ";
			dol_syslog(get_class($this)."::getsum_strpartprev sql=".$sql, LOG_DEBUG);
			$resql=$this->db->query($sql);
			$total = 0;
			if ($resql)
			{
				$num = $this->db->num_rows($resql);
				if ($num)
				{
					$obj = $this->db->fetch_object($resql);
					$total = $obj->total;

				}
				$this->db->free($resql);
				return $total;
			}
			else
			{
				$this->error="Error ".$this->db->lasterror();
				dol_syslog(get_class($this)."::getsum_strpartprev ".$this->error, LOG_ERR);
				return 0;
			}
		}


		/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
		function getsum_pac_str_part($gestion,$fk_pac,$fk_structure,$fk_poa,$partida)
		{
			global $langs;
			$sql = "SELECT";
			$sql.= " r.gestion, t.fk_structure, t.partida,";
			$sql.= " SUM(t.amount) AS total ";

			$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_pre as t";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_prev AS r ON t.fk_poa_prev = r.rowid ";
			$sql.= " WHERE r.gestion = ".$gestion;
			$sql.= " AND r.fk_pac = ".$fk_pac;
			$sql.= " AND t.fk_poa = ".$fk_poa;
			$sql.= " AND t.partida = '".$partida."' ";
			$sql.= " AND t.statut = 1";
			$sql.= " AND r.statut > 0";
			$sql.= " GROUP BY r.gestion, t.fk_structure, t.partida ";
			dol_syslog(get_class($this)."::getsum_str_part sql=".$sql, LOG_DEBUG);
			$resql=$this->db->query($sql);
			if ($resql)
			{
				$this->total = 0;
				$num = $this->db->num_rows($resql);
				if ($num)
				{
					$obj = $this->db->fetch_object($resql);
					$this->total = $obj->total;
				}
				$this->db->free($resql);
				return 1;
			}
			else
			{
				$this->error="Error ".$this->db->lasterror();
				dol_syslog(get_class($this)."::getsum_str_part ".$this->error, LOG_ERR);
				return -1;
			}
		}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getsum_str_part_det($gestion,$fk_structure,$fk_poa,$id,$fk_contrat,$partida)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " r.gestion, t.fk_structure, t.partida,";
		$sql.= " td.fk_contrat, ";
		$sql.= " SUM(td.amount) AS total ";

		$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_pre_det as td";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_partida_pre AS t ON td.fk_poa_partida_pre = t.rowid ";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_prev AS r ON t.fk_poa_prev = r.rowid ";
		$sql.= " WHERE r.gestion = ".$gestion;
		$sql.= " AND t.fk_poa = ".$fk_poa;
		$sql.= " AND t.partida = '".$partida."' ";
		$sql.= " AND t.rowid = ".$id;
		$sql.= " AND td.fk_contrat = ".$fk_contrat;
		$sql.= " AND t.statut = 1";
		$sql.= " GROUP BY r.gestion, t.fk_structure, t.partida, td.fk_contrat ";
	//echo '<hr>getsum_str_part_det <br>'.$sql;
		dol_syslog(get_class($this)."::getsum_str_part_det sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->total = 0;
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$obj = $this->db->fetch_object($resql);
				$this->total = $obj->total;
			}
			$this->db->free($resql);
			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getsum_str_part_det ".$this->error, LOG_ERR);
			return -1;
		}
	}

		/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
		function getsum_str_part_det2($gestion,$fk_structure,$fk_poa,$id,$fk_contrat,$partida)
		{
			global $langs;
			$sql = "SELECT";
			$sql.= " r.gestion, t.fk_structure, t.partida,";
			$sql.= " td.fk_contrato, ";
			$sql.= " SUM(td.amount) AS total ";

			$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_pre_det as td";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_partida_pre AS t ON td.fk_poa_partida_pre = t.rowid ";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_prev AS r ON t.fk_poa_prev = r.rowid ";
			$sql.= " WHERE r.gestion = ".$gestion;
			$sql.= " AND t.fk_poa = ".$fk_poa;
			$sql.= " AND t.partida = '".$partida."' ";
			$sql.= " AND t.rowid = ".$id;
			$sql.= " AND td.fk_contrato = ".$fk_contrat;
			$sql.= " AND t.statut = 1";
			$sql.= " GROUP BY r.gestion, t.fk_structure, t.partida, td.fk_contrato ";
			echo '<hr>getsum_str_part_det <br>'.$sql;
	//exit;
			dol_syslog(get_class($this)."::getsum_str_part_det sql=".$sql, LOG_DEBUG);
			$resql=$this->db->query($sql);
			$this->total = 0;
			if ($resql)
			{
				$num = $this->db->num_rows($resql);
				if ($num)
				{
					$obj = $this->db->fetch_object($resql);
					$this->total = $obj->total;
				}
				$this->db->free($resql);
				return 1;
			}
			else
			{
				$this->error="Error ".$this->db->lasterror();
				dol_syslog(get_class($this)."::getsum_str_part_det ".$this->error, LOG_ERR);
				return -1;
			}
		}


	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */

	//modificado
	function getlist_user($gestion,$fk_user=0,$fk_area=0,$userpoa=0)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.fk_poa_prev,";
		$sql.= " t.fk_structure,";
		$sql.= " t.fk_poa,";
		$sql.= " t.partida,";
		$sql.= " t.amount,";
		$sql.= " t.tms,";
		$sql.= " t.statut,";
		$sql.= " t.active,";
		$sql.= " p.fk_user_create,";
		$sql.= " p.date_preventive";
		$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_pre as t";
	  //modificado
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_prev AS p ON t.fk_poa_prev = p.rowid";
		if ($userpoa)
		{
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_poa AS po ON t.fk_poa = po.rowid";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_poa_user AS pu ON t.fk_poa = pu.fk_poa_poa";
		}
		$sql.= " WHERE p.gestion = ".$gestion;
		if ($userpoa)
		{
			if ($fk_user)
				$sql.= " AND pu.fk_user = ".$fk_user;
			if ($fk_area)
				$sql.= " AND po.fk_area = ".$fk_area;
			$sql.= " AND pu.active = 1";
			$sql.= " AND pu.statut = 1";
		}
		else
		{
			if ($fk_user)
				$sql.= " AND p.fk_user_create = ".$fk_user;
			if ($fk_area)
				$sql.= " AND p.fk_area = ".$fk_area;
		}
		$sql.= " AND p.statut > 0 ";
		$sql.= " AND t.statut > 0 ";
		dol_syslog(get_class($this)."::getlist_user sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->array = array();

		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i = 0;
		  //modificado
		  //include_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidapredet.class.php';

				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objnew = new Poapartidapre($this->db);
		  //modificado
		  //$objdet = new Poapartidapredet($this->db);
		  //$total = $objdet->getsum($obj->rowid);
					$objnew->id    = $obj->rowid;

					$objnew->fk_poa_prev = $obj->fk_poa_prev;
					$objnew->fk_structure = $obj->fk_structure;
					$objnew->fk_poa = $obj->fk_poa;
					$objnew->partida = $obj->partida;
		  //modificado
		  //$objnew->amount = $total;
					$objnew->amount = $obj->amount;
					$objnew->tms = $this->db->jdate($obj->tms);
					$objnew->statut = $obj->statut;
					$objnew->active = $obj->active;
					$objnew->fk_user_create = $obj->fk_user_create;
					$objnew->date_preventive = $obj->date_preventive;
					$this->array[$obj->rowid] = $objnew;
					$i++;
				}
			}
			$this->db->free($resql);
			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getlist_user ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/*
	* sumar y contar los preventivos por usuario
	*/
	function resume_prev_user($fk_poa,$gestion,$fk_user=0)
	{
		global $langs,$conf;
		$res = $this->getlist_user($gestion,$fk_user);
		if ($res>0)
		{
			$aCount = array();
			$aCountfin = array();
			foreach ((array) $this->array AS $i => $obj)
			{
				if ($obj->fk_poa == $fk_poa)
				{
					$aCount[$fk_user][$obj->nro_preventive]=$obj->nro_preventive;
					$this->aSum[$fk_user]+=$obj->amount;
					if ($obj->statut == 9)
					{
						$this->aCountfin[$fk_user]++;
						$this->aSumfin[$fk_user]+= $obj->amount;
					}
				}
			}
			//armamos el resumen de conteo
			return $res;
		}
		return $res;
	}


	//funcion que suma los preventivos hijos
	function getsum_son($fk_prev,$gestion,$fk_structure,$fk_poa,$partida)
	{
		global $db, $conf,$objpre;
		$total = 0;
		if ($fk_prev > 0)
		{
			require_once DOL_DOCUMENT_ROOT.'/poa/class/poaprevext.class.php';
			$objpre = new Poaprevext($db);
			$objpre->getlistfather($fk_prev);
			foreach ((array) $objpre->arrayf AS $i => $objd)
			{
		  		//obtenemos la suma de cada preventivo
				$total += $this->getsum_strpartprev($objd->id,$gestion,$fk_structure,$fk_poa,$partida);
			}
		}
	  	//echo '<br>total_hijo '.$total;
		return $total;
	}	
}