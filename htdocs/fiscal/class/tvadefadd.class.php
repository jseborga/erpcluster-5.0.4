<?php
require_once DOL_DOCUMENT_ROOT.'/fiscal/class/tvadef.class.php';

class Tvadefadd extends Tvadef
{
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
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lView=false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

        $sql = "SELECT";
		$sql.= " t.rowid,";
		$sql.= " t.entity,";

		$sql.= " t.fk_pays,";
		$sql.= " t.code_facture,";
		$sql.= " t.code_tva,";
		$sql.= " t.taux,";
		$sql.= " t.register_mode,";
		$sql.= " t.note,";
		$sql.= " t.active,";
		$sql.= " t.accountancy_code";
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

		if ($filterstatic) $sql.= $filterstatic;

		if (!empty($sortfield)) {
			$sql .= ' ORDER BY ' . $sortfield . ' ' . $sortorder;
		}
		if (!empty($limit)) {
		 $sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
		}
		$this->lines = array();

		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql))
			{
				$line = new Tvadef($this->db);

                $line->id    = $obj->rowid;

                $line->entity    = $obj->entity;
				$line->fk_pays = $obj->fk_pays;
				$line->code_facture = $obj->code_facture;
				$line->code_tva = $obj->code_tva;
				$line->taux = $obj->taux;
				$line->register_mode = $obj->register_mode;
				$line->note = $obj->note;
				$line->active = $obj->active;
				$line->accountancy_code = $obj->accountancy_code;
				$this->lines[] = $line;
				if ($lView) $this->fetch($obj->rowid);
			}
			$this->db->free($resql);
			return $num;
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return - 1;
		}
	}

}
?>