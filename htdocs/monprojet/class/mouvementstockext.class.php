<?php
require_once DOL_DOCUMENT_ROOT.'/product/stock/class/mouvementstock.class.php';

class Mouvementstockext extends Mouvementstock
{
	var $tms;
	var $datem;
	var $fk_product;
	var $batch;
	var $eatby;
	var $selby;
	var $fk_entrepot;
	var $value;

	function _delete($id,$entrepot_id,$fk_product,$qty=0)
	{
		global $conf,$langs;
		$this->db->begin();
		$error=0;
		$sql = "UPDATE ".MAIN_DB_PREFIX."product_stock SET reel = reel + ".$qty;
		$sql.= " WHERE fk_entrepot = ".$entrepot_id." AND fk_product = ".$fk_product;
		$resql = $this->db->query($sql);
		if (!$resql)
		{
			$error++;
			$this->errors[]=$this->db->lasterror();
		}

		$sql = " DELETE FROM ".MAIN_DB_PREFIX."stock_mouvement";
		$sql.= " WHERE rowid = ".$id;
		$resql = $this->db->query($sql);
		if (!$resql)
		{
			$error++;
			$this->errors[]=$this->db->lasterror();
			$error++;
		}
		if (!$error)
		{
			$this->db->commit();
			return 1;
		}
		else
		{
			$this->db->rollback();
			return -1;
		}
	}
}

?>