<?php
require_once DOL_DOCUMENT_ROOT.'/orgman/class/partidaproduct.class.php';

class Partidaproductext extends Partidaproduct
{
	
	public function checkingPro(User $user, $notrigger = false)
	{
		
		$error = 0;
		dol_syslog(__METHOD__, LOG_DEBUG);

		if (isset($this->active)) {
			$this->active = trim($this->active);
		}

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';

		$sql .= ' active = '.(isset($this->active)?$this->active:"null");

		$sql .= ' WHERE fk_product=' . $this->fk_product;
         //echo $sql;
		$this->db->begin();

		$resql = $this->db->query($sql);
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . implode(',', $this->errors), LOG_ERR);
		}

		// Commit or rollback
		if ($error) {
			$this->db->rollback();

			return - 1 * $error;
		} else {
			$this->db->commit();

			return 1;
		}
	}
}
?>