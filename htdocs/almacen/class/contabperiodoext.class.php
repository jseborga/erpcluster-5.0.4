<?php
require_once DOL_DOCUMENT_ROOT.'/almacen/class/contabperiodo.class.php';

class Contabperiodoext extends Contabperiodo
{
	function fetchyear($year)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.entity,";
		$sql .= " t.period_month,";
		$sql .= " t.period_year,";
		$sql .= " t.date_ini,";
		$sql .= " t.date_fin,";
		$sql .= " t.statut,";
		$sql .= " t.status_af,";
		$sql .= " t.status_al";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element. ' as t';

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

		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield,$sortorder);
		}
		if (!empty($limit)) {
		 $sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
		}
		$this->lines = array();

		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new ContabperiodoLine();

				$line->id = $obj->rowid;

				$line->entity = $obj->entity;
				$line->period_month = $obj->period_month;
				$line->period_year = $obj->period_year;
				$line->date_ini = $this->db->jdate($obj->date_ini);
				$line->date_fin = $this->db->jdate($obj->date_fin);
				$line->statut = $obj->statut;
				$line->status_af = $obj->status_af;
				$line->status_al = $obj->status_al;



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
			if ($status == 1) return $langs->trans('Opened');
			if ($status == 0) return $langs->trans('Closed');
		}
		if ($mode == 1)
		{
			if ($status == 1) return $langs->trans('Opened');
			if ($status == 0) return $langs->trans('Closed');
		}
		if ($mode == 2)
		{
			if ($status == 1) return img_picto($langs->trans('Opened'),'statut4').' '.$langs->trans('Opened');
			if ($status == 0) return img_picto($langs->trans('Closed'),'statut5').' '.$langs->trans('Closed');
		}
		if ($mode == 3)
		{
			if ($status == 1) return img_picto($langs->trans('Opened'),'statut4');
			if ($status == 0) return img_picto($langs->trans('Closed'),'statut5');
		}
		if ($mode == 4)
		{
			if ($status == 1) return img_picto($langs->trans('Opened'),'statut4').' '.$langs->trans('Opened');
			if ($status == 0) return img_picto($langs->trans('Closed'),'statut5').' '.$langs->trans('Closed');
		}
		if ($mode == 5)
		{
			if ($status == 1) return $langs->trans('Opened').' '.img_picto($langs->trans('Opened'),'statut4');
			if ($status == 0) return $langs->trans('Closed').' '.img_picto($langs->trans('Closed'),'statut5');
		}
	}
}
?>