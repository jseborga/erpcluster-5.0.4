<?php
require_once DOL_DOCUMENT_ROOT.'/fabrication/class/productportioning.class.php';

class Productportioningadd extends ProductPortioning
{
	public $lines;
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
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lRow=false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.fk_product,";
		$sql.= " t.fk_product_portion,";
		$sql.= " t.qty,";
		$sql.= " t.date_create,";
		$sql.= " t.tms,";
		$sql.= " t.active,";
		$sql.= " t.statut";

		
		$sql.= " FROM ".MAIN_DB_PREFIX."product_portioning as t";

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

		if ($filterstatic)
			$sql.= $filterstatic;

		if (!empty($sortfield)) {
			$sql .= ' ORDER BY ' . $sortfield . ' ' . $sortorder;
		}
		if (!empty($limit)) {
			$sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
		}
		//echo '<hr>'.$sql;
		$this->lines = array();
		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) 
			{
				$line = new PortioningLigne();

				$line->id    = $obj->rowid;

				$line->fk_product = $obj->fk_product;
				$line->fk_product_portion = $obj->fk_product_portion;
				$line->qty = $obj->qty;
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->tms = $this->db->jdate($obj->tms);
				$line->active = $obj->active;
				$line->statut = $obj->statut;
				if ($lRow)
				{
	                $this->id    = $obj->rowid;                
					$this->fk_product = $obj->fk_product;
					$this->fk_product_portion = $obj->fk_product_portion;
					$this->qty = $obj->qty;
					$this->date_create = $this->db->jdate($obj->date_create);
					$this->tms = $this->db->jdate($obj->tms);
					$this->active = $obj->active;
					$this->statut = $obj->statut;
				}
				$this->lines[] = $line;
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

/**
 *	Classe permettant la gestion des lignes de contrats
 */
class PortioningLigne
{

	var $id;

	var $fk_product;
	var $fk_product_portion;
	var $qty;
	var $date_create='';
	var $tms='';
	var $active;
	var $statut;

	public $element='product_portioning';
	public $table_element='product_portioning';

}
?>