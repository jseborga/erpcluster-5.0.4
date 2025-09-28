<?php
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabseat.class.php';

class Contabseatext extends Contabseat
{

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
	 *  Return the status
	 *
	 *  @param	int		$status        	Id status
	 *  @param  int		$mode          	0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 5=Long label + Picto
	 *  @return string 			       	Label of status
	 */
	static function LibStatut($status,$mode=0)
	{
		global $langs;

		if ($mode == 0)
		{
			$prefix='';
			if ($status == 1) return $langs->trans('Validated');
			if ($status == 0) return $langs->trans('Draft');
		}
		if ($mode == 1)
		{
			if ($status == 1) return $langs->trans('Validated');
			if ($status == 0) return $langs->trans('Draft');
		}
		if ($mode == 2)
		{
			if ($status == 1) return img_picto($langs->trans('Validated'),'statut4').' '.$langs->trans('Validated');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut5').' '.$langs->trans('Draft');
		}
		if ($mode == 3)
		{
			if ($status == 1) return img_picto($langs->trans('Validated'),'statut4');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut5');
		}
		if ($mode == 4)
		{
			if ($status == 1) return img_picto($langs->trans('Validated'),'statut4').' '.$langs->trans('Validated');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut5').' '.$langs->trans('Draft');
		}
		if ($mode == 5)
		{
			if ($status == 1) return $langs->trans('Validated').' '.img_picto($langs->trans('Validated'),'statut4');
			if ($status == 0) return $langs->trans('Draft').' '.img_picto($langs->trans('Draft'),'statut5');
		}
		if ($mode == 6)
		{
			if ($status == 1) return $langs->trans('Validated').' '.img_picto($langs->trans('Validated'),'statut4');
			if ($status == 0) return $langs->trans('Draft').' '.img_picto($langs->trans('Draft'),'statut5');
		}
	}
		/**
	 *  Returns the reference to the following non used Order depending on the active numbering module
	 *  defined into ALMACEN_ADDON
	 *
	 *  @param	Societe		$soc  	Object thirdparty
	 *  @return string      		Order free reference
	 */
		function getNextNumRef($soc)
		{
			global $db, $langs, $conf;
			$langs->load("contab@contab");

			$dir = DOL_DOCUMENT_ROOT . "/contab/core/modules";

			if (! empty($conf->global->CONTAB_ADDON))
			{
				$file = $conf->global->CONTAB_ADDON.".php";
			// Chargement de la classe de numerotation
				$classname = $conf->global->CONTAB_ADDON;
				$result=include_once $dir.'/'.$file;
				if ($result)
				{
					$obj = new $classname();
					$numref = "";
					$numref = $obj->getNextValue($soc,$this);

					if ( $numref != "")
					{
						return $numref;
					}
					else
					{
						dol_print_error($db,"Contabseat::getNextNumRef ".$obj->error);
						return "";
					}
				}
				else
				{
					print $langs->trans("Error")." ".$langs->trans("Error_CONTAB_ADDON_NotDefined");
					return "";
				}
			}
			else
			{
				print $langs->trans("Error")." ".$langs->trans("Error_CONTAB_ADDON_NotDefined");
				return "";
			}
		}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function get_next_typenumeric($type_seat,$month,$year)
	{
		global $langs,$conf;
		$type_numeric  = $conf->global->CONTAB_TSE_TYPENUMERIC;
		$code_ingreso  = $conf->global->CONTAB_TSE_INGRESO;
		$code_egreso   = $conf->global->CONTAB_TSE_EGRESO;
		$code_traspaso = $conf->global->CONTAB_TSE_TRASPASO;

		$sql = "SELECT";
		$sql.= " MAX(t.sequential) AS sequential ";

		$sql.= " FROM ".MAIN_DB_PREFIX."contab_seat as t";
		$sql.= " WHERE t.entity = ".$conf->entity;
		if ($type_numeric == '2')
			$sql.= " AND t.seat_month = '".$month."'";
		$sql.= " AND t.seat_year = ".$year;
		$sql.= " AND t.type_seat = ".$type_seat;
		$sql.= " ORDER BY t.sequential DESC";


		dol_syslog(get_class($this)."::get_next_typenumeric sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);
				if (empty($obj->sequential))
					$this->sequential = 1;
				else
					$this->sequential = ($obj->sequential*1) + 1;
			}
			else
				$this->sequential = 1;
			$this->db->free($resql);
			$this->sequential;
			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::get_next_typenumeric ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	varchar		$lote    Lote object
	 *  @param	varchar		$sblote    SbLote object
	 *  @param	year		$year    Year object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function get_next_lote($lote,$sblote,$year)
	{
		global $langs,$conf;

		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.date_seat,";
		$sql.= " t.lote,";
		$sql.= " t.sblote,";
		$sql.= " t.doc,";
		$sql.= " t.currency,";
		$sql.= " t.type_seat,";
		$sql.= " t.type_numeric,";
		$sql.= " t.sequential,";
		$sql.= " t.seat_month,";
		$sql.= " t.seat_year,";
		$sql.= " t.debit_total,";
		$sql.= " t.credit_total,";
		$sql.= " t.history,";
		$sql.= " t.manual,";
		$sql.= " t.fk_user_creator,";
		$sql.= " t.date_creator,";
		$sql.= " t.state";


		$sql.= " FROM ".MAIN_DB_PREFIX."contab_seat as t";
		$sql.= " WHERE t.entity = ".$conf->entity;
		$sql.= " AND t.lote = '".$lote."'";
		$sql.= " AND t.sblote = '".$sblote."'";
		$sql.= " AND t.seat_year = ".$year;
		$sql.= " ORDER BY t.doc DESC";
		$sql.= $this->db->plimit(0, 1);

		dol_syslog(get_class($this)."::get_next_lote sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);
				if (empty($obj->doc))
					$this->doc = 1;
				else
					$this->doc = ($obj->doc*1) + 1;
			}
			else
				$this->doc = 1;
			$this->db->free($resql);
			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::get_next_lote ".$this->error, LOG_ERR);
			return -1;
		}
	}

}
?>