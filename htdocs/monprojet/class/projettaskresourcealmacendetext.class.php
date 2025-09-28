<?php
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskresourcealmacendet.class.php';

class Projettaskresourcealmacendetext extends Projettaskresourcealmacendet
{
	var $total;

	function get_sum_product($fk_product,$fk_sol_ad=0,$fk_pt=0,$fk_ptr=0)
	{
		global $langs;
		$this->total = 0;
		$sql = "SELECT sum(t.quant) AS total ";
		$sql.= " FROM ".MAIN_DB_PREFIX."projet_task_resource_almacendet AS t ";
		$sql.= " WHERE t.fk_product = ".$fk_product;
		if ($fk_sol_ad>0)
			$sql.= " AND t.fk_sol_almacen_det = ".$fk_sol_ad;
		if ($fk_pt>0)
			$sql.= " AND t.fk_projet_task = ".$fk_pt;
		if ($fk_ptr>0)
			$sql.= " AND t.fk_projet_task_resource = ".$fk_ptr;
		$resql = $this->db->query($sql);
		
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			if ($numrows) {
				$obj = $this->db->fetch_object($resql);
				$this->total = $obj->total;
			}
		}
		return $total;
	}
}
?>