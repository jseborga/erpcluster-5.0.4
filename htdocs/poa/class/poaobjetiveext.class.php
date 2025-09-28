<?php
require_once DOL_DOCUMENT_ROOT.'/poa/class/poaobjetive.class.php';

class Poaobjetiveext extends Poaobjetive
{
	var $max='';

	public function get_objetivenext($filter='')
	{
		$sql = 'SELECT';
		$sql .= " MAX(t.ref) AS max";
		
		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql.= ' WHERE 1 = 1';		
		$sql.= " AND t.fk_father = ".$this->id;
		if ($filter) $sql.= $filter;
		$sql.= " GROUP BY t.fk_father";

		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			if ($numrows) {
				$obj = $this->db->fetch_object($resql);

				$this->max = $obj->max;
			}

			$this->db->free($resql);

			if ($numrows) {
				return $numrows;
			} else {
				return 0;
			}
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . implode(',', $this->errors), LOG_ERR);
			return - 1;
		}
	}
}
?>