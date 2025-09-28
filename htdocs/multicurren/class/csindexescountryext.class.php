<?php
require_once DOL_DOCUMENT_ROOT.'/multicurren/class/csindexescountry.class.php';

class Csindexescountryext extends Csindexescountry
{
		/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$countryd    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
		function fetch_last($country,$date_ind='')
		{
			global $langs,$conf;
			$sql = 'SELECT';
			$sql .= ' t.rowid,';
			
			$sql .= " t.entity,";
			$sql .= " t.ref,";
			$sql .= " t.date_ind,";
			$sql .= " t.amount,";
			$sql .= " t.fk_user_create,";
			$sql .= " t.fk_user_mod,";
			$sql .= " t.datec,";
			$sql .= " t.dateu,";
			$sql .= " t.tms,";
			$sql .= " t.status";

			
			$sql.= " FROM ".MAIN_DB_PREFIX."cs_indexes_country as t";
			$sql.= " WHERE t.entity = $conf->entity ";
			$sql.= " AND t.ref = '".$country."'";
			if ($date_ind)
			{
				//filtramos por fecha
				$aDate = dol_getdate($date_ind);
				$sql.= " AND YEAR(t.date_ind) = ".$aDate['year'];
				$sql.= " AND MONTH(t.date_ind) = ".$aDate['mon'];
				$sql.= " AND DAY(t.date_ind) = ".$aDate['mday'];
			}
			$sql.= " ORDER BY t.date_ind DESC ";
			$sql.= $this->db->plimit(0, 1);

			dol_syslog(get_class($this)."::fetch_last sql=".$sql, LOG_DEBUG);
			$resql=$this->db->query($sql);
			if ($resql)
			{	
				$num = $this->db->num_rows($resql);
				if ($this->db->num_rows($resql))
				{
					$obj = $this->db->fetch_object($resql);

					$this->id = $obj->rowid;
					
					$this->entity = $obj->entity;
					$this->ref = $obj->ref;
					$this->date_ind = $this->db->jdate($obj->date_ind);
					$this->amount = $obj->amount;
					$this->fk_user_create = $obj->fk_user_create;
					$this->fk_user_mod = $obj->fk_user_mod;
					$this->datec = $this->db->jdate($obj->datec);
					$this->dateu = $this->db->jdate($obj->dateu);
					$this->tms = $this->db->jdate($obj->tms);
					$this->status = $obj->status;
				}
				$this->db->free($resql);

				return $num;
			}
			else
			{
				$this->error="Error ".$this->db->lasterror();
				dol_syslog(get_class($this)."::fetch_last ".$this->error, LOG_ERR);
				return -1;
			}
		}

	}
	?>