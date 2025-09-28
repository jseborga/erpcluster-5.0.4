<?php
require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projettaskadd.class.php';

class Projettaskaddext extends Projettaskadd
{
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
?>