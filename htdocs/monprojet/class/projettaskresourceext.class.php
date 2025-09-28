<?php
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskresource.class.php';

class Projettaskresourceext extends Projettaskresource
{
	function get_sum($fk_projet_task,$group)
	{
		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		
		$sql .= " t.fk_projet_task,";
		$sql .= " t.ref,";
		$sql .= " t.ref_ext,";
		$sql .= " t.fk_object,";
		$sql .= " t.object,";
		$sql .= " t.fk_objectdet,";
		$sql .= " t.objectdet,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.group_resource,";
		$sql .= " t.type_resource,";
		$sql .= " t.fk_product,";
		$sql .= " t.fk_product_projet,";
		$sql .= " t.fk_product_budget,";
		$sql .= " t.detail,";
		$sql .= " t.fk_unit,";
		$sql .= " t.quant,";
		$sql .= " t.percent_prod,";
		$sql .= " t.amount_noprod,";
		$sql .= " t.amount,";
		$sql .= " t.rang,";
		$sql .= " t.date_create,";
		$sql .= " t.date_mod,";
		$sql .= " t.tms,";
		$sql .= " t.status";

		
		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql .= ' WHERE t.fk_projet_task = ' . $fk_projet_task;
		if ($group)
			$sql .= " AND t.group_resource = '" . $group."'";

	}

		/**
	 *	Returns the text label from units dictionary
	 *
	 * 	@param	string $type Label type (long or short)
	 *	@return	string|int <0 if ko, label if ok
	 */
	function getLabelOfUnit($type='long')
	{
		global $langs;

		if (!$this->fk_unit) {
			return '';
		}

		$langs->load('products');

		$this->db->begin();

		$label_type = 'label';

		if ($type == 'short')
		{
			$label_type = 'short_label';
		}

		$sql = 'select '.$label_type.' from '.MAIN_DB_PREFIX.'c_units where rowid='.$this->fk_unit;
		$resql = $this->db->query($sql);
		if($resql && $this->db->num_rows($resql) > 0)
		{
			$res = $this->db->fetch_array($resql);
			$label = $langs->trans($res[$label_type]);
			$this->db->free($resql);
			return $label;
		}
		else
		{
			$this->error=$this->db->error().' sql='.$sql;
			dol_syslog(get_class($this)."::getLabelOfUnit Error ".$this->error, LOG_ERR);
			return -1;
		}
	}	
} 