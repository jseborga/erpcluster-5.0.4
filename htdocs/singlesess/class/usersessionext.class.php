<?php
require_once DOL_DOCUMENT_ROOT.'/singlesess/class/usersession.class.php';

class Usersessionext extends Usersession
{
	function update_code(User $user)
	{
		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';
		
		$sql .= ' ccode = '.(isset($this->ccode)?"'".$this->db->escape($this->ccode)."'":"null");
       
		$sql .= ' WHERE rowid=' . $this->id;

		//$this->db->begin();

		$resql = $this->db->query($sql);
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
		}

		// Commit or rollback
		//if ($error) {
		//	$this->db->rollback();

		//	return - 1 * $error;
		//} else {
		//	$this->db->commit();

			return 1;
		//}
	}
	function update_dateu(User $user)
	{
		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';
		$sql .= ' dateu = '.(isset($this->dateu)?"'".$this->db->idate($this->db->escape($this->dateu))."'":"null");
		$sql .= ' WHERE rowid=' . $this->id;

		//$this->db->begin();

		$resql = $this->db->query($sql);
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
		}

		// Commit or rollback
		//if ($error) {
		//	$this->db->rollback();

		//	return - 1 * $error;
		//} else {
		//	$this->db->commit();

			return 1;
		//}
	}

}

?>