<?php
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabperiodo.class.php';

class Contabperiodoext extends Contabperiodo
{
	/**
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
	function fetch_open($month,$year,$date_seat)
	{
		global $langs,$conf;
		if (empty($month) || empty($year)) return -1;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.period_month,";
		$sql.= " t.period_year,";
		$sql.= " t.date_ini,";
		$sql.= " t.date_fin,";
		$sql.= " t.statut";


		$sql.= " FROM ".MAIN_DB_PREFIX."contab_periodo as t";
		$sql.= " WHERE t.entity = ".$conf->entity;
		$sql.= " AND t.period_month = ".$month." ";
		$sql.= " AND t.period_year = ".$year." ";
		dol_syslog(get_class($this)."::fetch_open sql=".$sql, LOG_DEBUG);

		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					if ($date_seat <= $this->db->jdate($obj->date_fin))
					{
						$this->id     = $obj->rowid;
						$this->entity = $obj->entity;
						$this->period_month = $obj->period_month;
						$this->period_year  = $obj->period_year;
						$this->date_ini = $this->db->jdate($obj->date_ini);
						$this->date_fin = $this->db->jdate($obj->date_fin);
						$this->statut   = $obj->statut;
						$this->db->free($resql);
						return $this->statut;
					}
					$i++;
				}
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
	 *  Retourne le libelle du status d'un user (actif, inactif)
	 *
	 *  @param	int		$mode          0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return	string 			       Label of status
	 */
	function getLibStatut_al($mode=0)
	{
		return $this->LibStatut_al($this->status_al,$mode);
	}

	/**
	 *  Renvoi le libelle d'un status donne
	 *
	 *  @param	int		$status        	Id status
	 *  @param  int		$mode          	0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return string 			       	Label of status
	 */
	function LibStatut_al($status,$mode=0)
	{
		global $langs;

		if ($mode == 0)
		{
			$prefix='';
			if ($status == 1) return $langs->trans('Open');
			if ($status == 0) return $langs->trans('Closed');
		}
		if ($mode == 1)
		{
			if ($status == 1) return $langs->trans('Open');
			if ($status == 0) return $langs->trans('Closed');
		}
		if ($mode == 2)
		{
			if ($status == 1) return img_picto($langs->trans('Open'),'statut4').' '.$langs->trans('Open');
			if ($status == 0) return img_picto($langs->trans('Closed'),'statut5').' '.$langs->trans('Closed');
		}
		if ($mode == 3)
		{
			if ($status == 1) return img_picto($langs->trans('Open'),'statut4');
			if ($status == 0) return img_picto($langs->trans('Closed'),'statut5');
		}
		if ($mode == 4)
		{
			if ($status == 1) return img_picto($langs->trans('Open'),'statut4').' '.$langs->trans('Open');
			if ($status == 0) return img_picto($langs->trans('Closed'),'statut5').' '.$langs->trans('Closed');
		}
		if ($mode == 5)
		{
			if ($status == 1) return $langs->trans('Open').' '.img_picto($langs->trans('Open'),'statut4');
			if ($status == 0) return $langs->trans('Closed').' '.img_picto($langs->trans('Closed'),'statut5');
		}
	}
	/**
	 *  Retourne le libelle du status d'un user (actif, inactif)
	 *
	 *  @param	int		$mode          0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return	string 			       Label of status
	 */
	function getLibStatut_af($mode=0)
	{
		return $this->LibStatut_af($this->status_af,$mode);
	}

	/**
	 *  Renvoi le libelle d'un status donne
	 *
	 *  @param	int		$status        	Id status
	 *  @param  int		$mode          	0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return string 			       	Label of status
	 */
	function LibStatut_af($status,$mode=0)
	{
		global $langs;
		if ($mode == 0)
		{
			$prefix='';
			if ($status == 1) return $langs->trans('Open');
			if ($status == 0) return $langs->trans('Closed');
		}
		if ($mode == 1)
		{
			if ($status == 1) return $langs->trans('Open');
			if ($status == 0) return $langs->trans('Closed');
		}
		if ($mode == 2)
		{
			if ($status == 1) return img_picto($langs->trans('Open'),'statut4').' '.$langs->trans('Open');
			if ($status == 0) return img_picto($langs->trans('Closed'),'statut5').' '.$langs->trans('Closed');
		}
		if ($mode == 3)
		{
			if ($status == 1) return img_picto($langs->trans('Open'),'statut4');
			if ($status == 0) return img_picto($langs->trans('Closed'),'statut5');
		}
		if ($mode == 4)
		{
			if ($status == 1) return img_picto($langs->trans('Open'),'statut4').' '.$langs->trans('Open');
			if ($status == 0) return img_picto($langs->trans('Closed'),'statut5').' '.$langs->trans('Closed');
		}
		if ($mode == 5)
		{
			if ($status == 1) return $langs->trans('Open').' '.img_picto($langs->trans('Open'),'statut4');
			if ($status == 0) return $langs->trans('Closed').' '.img_picto($langs->trans('Closed'),'statut5');
		}
	}
}
?>