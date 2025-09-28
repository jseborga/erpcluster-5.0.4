<?php

require_once DOL_DOCUMENT_ROOT.'/salary/class/pcontract.class.php';

class Pcontractadd extends Pcontract
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
	public function get_dayjob($fk_user)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.ref,";
		$sql.= " t.fk_user,";
		$sql.= " t.fk_departament,";
		$sql.= " t.fk_charge,";
		$sql.= " t.fk_regional,";
		$sql.= " t.fk_proces,";
		$sql.= " t.fk_cc,";
		$sql.= " t.fk_account,";
		$sql.= " t.date_ini,";
		$sql.= " t.date_fin,";
		$sql.= " t.basic,";
		$sql.= " t.basic_fixed,";
		$sql.= " t.nivel,";
		$sql.= " t.bonus_old,";
		$sql.= " t.hours,";
		$sql.= " t.nua_afp,";
		$sql.= " t.afp,";
		$sql.= " t.state";
		
		$sql.= ' FROM ' . MAIN_DB_PREFIX . $this->table_element. ' as t';
		$sql.= " WHERE t.fk_user = ".$fk_user;

		$this->lines = array();

		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql))
			{
				$line = new PcontratLine();

                $line->id  = $obj->rowid;                
				$line->ref = $obj->ref;
				$line->fk_user = $obj->fk_user;
				$line->fk_departament = $obj->fk_departament;
				$line->fk_charge = $obj->fk_charge;
				$line->fk_regional = $obj->fk_regional;
				$line->fk_proces = $obj->fk_proces;
				$line->fk_cc = $obj->fk_cc;
				$line->fk_account = $obj->fk_account;
				$line->date_ini = $this->db->jdate($obj->date_ini);
				$line->date_fin = $this->db->jdate($obj->date_fin);
				$line->basic = $obj->basic;
				$line->basic_fixed = $obj->basic_fixed;
				$line->nivel = $obj->nivel;
				$line->bonus_old = $obj->bonus_old;
				$line->hours = $obj->hours;
				$line->nua_afp = $obj->nua_afp;
				$line->afp = $obj->afp;
				$line->state = $obj->state;

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
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lView=false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.ref,";
		$sql.= " t.fk_user,";
		$sql.= " t.fk_departament,";
		$sql.= " t.fk_charge,";
		$sql.= " t.fk_regional,";
		$sql.= " t.fk_proces,";
		$sql.= " t.fk_cc,";
		$sql.= " t.fk_account,";
		$sql.= " t.date_ini,";
		$sql.= " t.date_fin,";
		$sql.= " t.basic,";
		$sql.= " t.basic_fixed,";
		$sql.= " t.nivel,";
		$sql.= " t.bonus_old,";
		$sql.= " t.hours,";
		$sql.= " t.nua_afp,";
		$sql.= " t.afp,";
		$sql.= " t.state";
		
		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element. ' as t';

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
		if ($filterstatic) $sql.= $filterstatic;
		if (!empty($sortfield)) {
			$sql .= ' ORDER BY ' . $sortfield . ' ' . $sortorder;
		}
		if (!empty($limit)) {
		 $sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
		}
		$this->lines = array();

		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql))
			{
				$line = new PcontratLine();

                $line->id    = $obj->rowid;
                
				$line->ref = $obj->ref;
				$line->fk_user = $obj->fk_user;
				$line->fk_departament = $obj->fk_departament;
				$line->fk_charge = $obj->fk_charge;
				$line->fk_regional = $obj->fk_regional;
				$line->fk_proces = $obj->fk_proces;
				$line->fk_cc = $obj->fk_cc;
				$line->fk_account = $obj->fk_account;
				$line->date_ini = $this->db->jdate($obj->date_ini);
				$line->date_fin = $this->db->jdate($obj->date_fin);
				$line->basic = $obj->basic;
				$line->basic_fixed = $obj->basic_fixed;
				$line->nivel = $obj->nivel;
				$line->bonus_old = $obj->bonus_old;
				$line->hours = $obj->hours;
				$line->nua_afp = $obj->nua_afp;
				$line->afp = $obj->afp;
				$line->state = $obj->state;

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

class PcontratLine
{
	var $id;
	var $ref;
	var $fk_user;
	var $fk_departament;
	var $fk_charge;
	var $fk_regional;
	var $fk_proces;
	var $fk_cc;
	var $fk_account;
	var $date_ini='';
	var $date_fin='';
	var $basic;
	var $basic_fixed;
	var $nivel;
	var $bonus_old;
	var $hours;
	var $nua_afp;
	var $afp;
	var $state;
}
?>