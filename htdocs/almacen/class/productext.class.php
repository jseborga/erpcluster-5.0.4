<?php
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
class Productext extends Product
{
	/**
	 *	Update a record into database.
	 *  If batch flag is set to on, we create records into llx_product_batch
	 *
	 *	@param	int		$id         Id of product
	 *	@param  User	$user       Object user making update
	 *	@param	int		$notrigger	Disable triggers
	 *	@param	string	$action		Current action for hookmanager ('add' or 'update')
	 *	@return int         		1 if OK, -1 if ref already exists, -2 if other error
	 */
	function update_add($id, $user, $notrigger=false, $action='update')
	{
		global $langs, $conf, $hookmanager;

		$error=0;

		// Check parameters


		$this->db->begin();


			// For automatic creation
		if ($this->barcode == -1) $this->barcode = $this->get_barcode($this,$this->barcode_type_code);

		$sql = "UPDATE ".MAIN_DB_PREFIX."product";
		$sql.= " SET ";
		$sql.= " tosell = " . $this->status;
		$sql.= ", tobuy = " . $this->status_buy;
		if (!empty($this->label))
			$sql.= ", label = '".$this->label."'";
		$sql.= ", cost_price = " . ($this->cost_price != '' ? $this->db->escape($this->cost_price) : 'null');
		$sql.= ", fk_unit= " . (!$this->fk_unit ? 'NULL' : $this->fk_unit);
			// stock field is not here because it is a denormalized value from product_stock.
		$sql.= " WHERE rowid = " . $id;

		dol_syslog(get_class($this)."::update", LOG_DEBUG);

		$resql=$this->db->query($sql);
		if ($resql)
		{
			$this->id = $id;
			if (! $error)
			{
				$this->db->commit();
				return 1;
			}
			else
			{
				$this->db->rollback();
				return -$error;
			}
		}
		else
		{
			if ($this->db->errno() == 'DB_ERROR_RECORD_ALREADY_EXISTS')
			{
				if (empty($conf->barcode->enabled)) $this->error=$langs->trans("Error")." : ".$langs->trans("ErrorProductAlreadyExists",$this->ref);
				else $this->error=$langs->trans("Error")." : ".$langs->trans("ErrorProductBarCodeAlreadyExists",$this->barcode);
				$this->errors[]=$this->error;
				$this->db->rollback();
				return -1;
			}
			else
			{
				$this->error=$langs->trans("Error")." : ".$this->db->error()." - ".$sql;
				$this->errors[]=$this->error;
				$this->db->rollback();
				return -2;
			}
		}
	}
}
?>