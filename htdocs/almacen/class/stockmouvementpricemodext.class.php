<?php
require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementpricemod.class.php';

class StockMouvementpricemodext extends Stockmouvementpricemod
{

	public function fetch_groupperiod($period_year)
	{
		global $conf,$langs;
		$sql = " SELECT ";
		$sql.= " t.period_year, t.month_year ";
		$sql.= " FROM ".MAIN_DB_PREFIX."stock_mouvement_pricemod AS t ";
		$sql.= " WHERE t.period_year = ".$period_year;
		$sql.= " GROUP BY t.period_year, t.month_year ";
		$sql.= " ORDER BY t.period_year, t.month_year DESC";

		$resql = $this->db->query($sql);
		$this->lines = array();
		if ($resql) 
		{
			$num = $this->db->num_rows($resql);
			if ($num) 
			{
				$obj = $this->db->fetch_object($resql);
				$line = new stdClass();
				$line->period_year = $obj->period_year;
				$line->month_year = $obj->month_year;
				$this->lines[] = $line;
			}

			$this->db->free($resql);

			if ($num) {
				return $num;
			} else {
				return 0;
			}
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . implode(',', $this->errors), LOG_ERR);

			return -1;
		}
	}
}
?>