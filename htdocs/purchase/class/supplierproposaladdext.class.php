<?php
require_once DOL_DOCUMENT_ROOT.'/purchase/class/supplierproposaladd.class.php';

class Supplierproposaladdext extends Supplierproposaladd
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
	public function fetchAlladd($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lView=false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.fk_supplier_proposal,";
		$sql .= " t.fk_purchase_request,";
		$sql .= " t.fk_pays,";
		$sql .= " t.fk_province,";
		$sql .= " t.code_facture,";
		$sql .= " t.code_type_purchase,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.datec,";
		$sql .= " t.datem,";
		$sql .= " t.tms,";
		$sql .= " t.status, ";
		$sql.= " d.fk_region_geographic ";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element. ' as t';
		$sql .= ' INNER JOIN ' . MAIN_DB_PREFIX . 'supplier_proposal'. ' as s ON t.fk_supplier_proposal = s.rowid';
		$sql .= ' INNER JOIN ' . MAIN_DB_PREFIX . 'c_departements_region'. ' as d ON t.fk_province = d.fk_departement';

		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				$sqlwhere [] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
			}
		}
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
		    $sql .= " AND entity IN (" . getEntity("supplierproposaladd", 1) . ")";
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' AND ' . implode(' '.$filtermode.' ', $sqlwhere);
		}
		if ($filterstatic){
			$sql.= $filterstatic;
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
				$line = new SupplierproposaladdLine();

				$line->id = $obj->rowid;

				$line->fk_supplier_proposal = $obj->fk_supplier_proposal;
				$line->fk_purchase_request = $obj->fk_purchase_request;
				$line->fk_pays = $obj->fk_pays;
				$line->fk_province = $obj->fk_province;
				$line->fk_region_geographic = $obj->fk_region_geographic;
				$line->code_facture = $obj->code_facture;
				$line->code_type_purchase = $obj->code_type_purchase;
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->datec = $this->db->jdate($obj->datec);
				$line->datem = $this->db->jdate($obj->datem);
				$line->tms = $this->db->jdate($obj->tms);
				$line->status = $obj->status;



				if ($lView && $num == 1) $this->fetch($obj->rowid);

				$this->lines[$line->id] = $line;
			}
			$this->db->free($resql);

			return $num;
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . implode(',', $this->errors), LOG_ERR);

			return - 1;
		}
	}
}
?>