<?php
require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockprogram.class.php';

class Stockprogramext extends Stockprogram
{
	function getNextValue($year)
	{
		global $langs, $conf;
		$prefix = '';
		$posindice=6;
		$sql = "SELECT MAX(SUBSTRING(ref FROM ".$posindice.")) as max"; // This is standard SQL
		$sql.= " FROM ".MAIN_DB_PREFIX."stock_program";
		$sql.= " WHERE ref LIKE '".$prefix."____-%'";
		$sql.= " AND entity = ".$conf->entity;

		$resql=$this->db->query($sql);
		dol_syslog("Stockprogramext::getNextValue sql=".$sql);
		if ($resql)
		{
			$obj = $this->db->fetch_object($resql);
			if ($obj) $max = intval($obj->max);
			else $max=0;
		}
		else
		{
			dol_syslog("Stockprogramext::getNextValue sql=".$sql, LOG_ERR);
			return -1;
		}

		$date=$facture->date_create;
				// This is invoice date (not creation date)
		if (empty($date)) $date = dol_now();
		$yy = $year;
		$num = sprintf("%04s",$max+1);

		dol_syslog("Stockprogramext::getNextValue return ".$prefix.$yy."-".$num);
		return $prefix.$yy."-".$num;
	}

	/**
	 * Load an object from its id and create a new one in database
	 *
	 * @param int $fromid Id of object to clone
	 *
	 * @return int New id of clone
	 */
	public function createFromCloneadd($fromid)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		global $user;
		$error = 0;
		$object = new Stockprogramext($this->db);
		$ref = $object->getNextValue(date('Y'));
		$this->db->begin();

		// Load source object
		$object->fetch($fromid);
		// Reset object
		$object->id = 0;
		$object->ref = $ref;
		$object->fk_user_val = 0;
		$object->datev = NULL;
		$object->status_print = 0;
		$object->status = 0;
		// Clear fields
		// ...

		// Create clone
		$result = $object->create($user);

		// Other options
		if ($result < 0) {
			$error ++;
			$this->errors = $object->errors;
			dol_syslog(__METHOD__ . ' ' . implode(',', $this->errors), LOG_ERR);
		}

		// End
		if (!$error) {
			$this->db->commit();

			return $object->id;
		} else {
			$this->db->rollback();

			return - 1;
		}
	}

	/**
	 *  Retourne le libelle du status d'un user (actif, inactif)
	 *
	 *  @param	int		$mode          0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return	string 			       Label of status
	 */
	function getLibStatutadd($mode=0)
	{
		return $this->LibStatutadd($this->status,$mode);
	}

	/**
	 *  Return the status
	 *
	 *  @param	int		$status        	Id status
	 *  @param  int		$mode          	0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 5=Long label + Picto
	 *  @return string 			       	Label of status
	 */
	static function LibStatutadd($status,$mode=0)
	{
		global $langs;

		if ($mode == 0)
		{
			$prefix='';
			if ($status == 2) return $langs->trans('Processed');
			if ($status == 1) return $langs->trans('Enabled');
			if ($status == 0) return $langs->trans('Draft');
		}
		if ($mode == 1)
		{
			if ($status == 2) return $langs->trans('Processed');
			if ($status == 1) return $langs->trans('Enabled');
			if ($status == 0) return $langs->trans('Draft');
		}
		if ($mode == 2)
		{
			if ($status == 2) return img_picto($langs->trans('Processed'),'statut7').' '.$langs->trans('Processed');
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut5').' '.$langs->trans('Draft');
		}
		if ($mode == 3)
		{
			if ($status == 2) return img_picto($langs->trans('Processed'),'statut7');
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut5');
		}
		if ($mode == 4)
		{
			if ($status == 2) return img_picto($langs->trans('Processed'),'statut7').' '.$langs->trans('Processed');
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut5').' '.$langs->trans('Draft');
		}
		if ($mode == 5)
		{
			if ($status == 2) return $langs->trans('Processed').' '.img_picto($langs->trans('Processed'),'statut7');
			if ($status == 1) return $langs->trans('Enabled').' '.img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return $langs->trans('Draft').' '.img_picto($langs->trans('Draft'),'statut5');
		}
		if ($mode == 6)
		{
			if ($status == 2) return $langs->trans('Processed').' '.img_picto($langs->trans('Processed'),'statut7');
			if ($status == 1) return $langs->trans('Enabled').' '.img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return $langs->trans('Draft').' '.img_picto($langs->trans('Draft'),'statut5');
		}
	}
}
?>