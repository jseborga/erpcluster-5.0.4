<?php
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabseatdet.class.php';

class Contabseatdetext extends Contabseatdet
{
	var $aArray;
	var $aArrayDet;

		/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
		function fetch_sequence($id)
		{
			global $langs;
			$sql = "SELECT";
			$sql.= " t.fk_contab_seat,";
			$sql.= " MAX(t.sequence) AS sequence ";

			$sql.= " FROM ".MAIN_DB_PREFIX."contab_seat_det as t";
			$sql.= " WHERE t.fk_contab_seat = ".$id;
			$sql.= " GROUP BY fk_contab_seat ";

			dol_syslog(get_class($this)."::fetch_sequence sql=".$sql, LOG_DEBUG);
			$resql=$this->db->query($sql);
			if ($resql)
			{
				if ($this->db->num_rows($resql))
				{
					$obj = $this->db->fetch_object($resql);
					return $obj->sequence + 1;
				}
				$this->db->free($resql);
				return 1;
			}
			else
			{
				return 1;
			}
		}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function double_entry($id)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.type_seat,";
		$sql.= " SUM(t.amount) AS amount ";

		$sql.= " FROM ".MAIN_DB_PREFIX."contab_seat_det as t";
		$sql.= " WHERE t.fk_contab_seat = ".$id;
		$sql.= " GROUP BY t.type_seat ";

		dol_syslog(get_class($this)."::double_entry sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i = 0;
				$amountDebit  = 0;
				$amountCredit = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					if ($obj->type_seat == 1)
						$amountDebit += price2num($obj->amount,'MT');
					if ($obj->type_seat == 2)
						$amountCredit += price2num($obj->amount,'MT');
					if ($obj->type_seat == 3)
					{
						$amountCredit += price2num($obj->amount,'MT');
						$amountDebit  += price2num($obj->amount,'MT');
					}
					$i++;
				}
				if ($amountDebit != $amountCredit)
					return -1;
				else
					return 1;
			}
			$this->db->free($resql);
			return 1;
		}
		else
		{
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	varchar		$ref    Ref object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function get_list_account($ref,$dateini='',$datefin='')
	{
		global $langs,$conf;
		$sql = "SELECT";
		$sql.= " t.rowid,";
		$sql.= " t.fk_contab_seat,";
		$sql.= " t.debit_account,";
		$sql.= " t.debit_detail,";
		$sql.= " t.credit_account,";
		$sql.= " t.credit_detail,";
		$sql.= " t.dcd,";
		$sql.= " t.dcc,";
		$sql.= " t.amount,";
		$sql.= " t.history,";
		$sql.= " t.sequence,";
		$sql.= " t.fk_standard_seat,";
		$sql.= " t.type_seat,";
		$sql.= " t.routines,";
		$sql.= " t.value02,";
		$sql.= " t.value03,";
		$sql.= " t.value04,";
		$sql.= " t.date_rate,";
		$sql.= " t.rate,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.datec,";
		$sql.= " t.status, ";
		$sql.= " s.date_seat";

		$sql.= " FROM ".MAIN_DB_PREFIX."contab_seat_det as t";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."contab_seat AS s ON s.rowid = t.fk_contab_seat ";
		$sql.= " WHERE (t.debit_account = '".$ref."' ";
		$sql.= " OR t.credit_account = '".$ref."') ";
		$sql.= " AND s.entity = ".$conf->entity;
		if (!empty($dateini) && !empty($datefin))
			$sql.= " AND s.date_seat BETWEEN '".$this->db->idate($dateini)."' AND '".$this->db->idate($datefin)."' ";

		dol_syslog(get_class($this)."::get_list_account sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		echo '<br>'.$sql;
		$aArray = array();
		$aArrayDet = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					if (!empty($obj->debit_account) && empty($obj->credit_account))
					{
						$aArrayDet[$obj->fk_contab_seat]['debit_account']=$obj->amount;

						$aArray['debit_amount'] += $obj->amount;
					}
					elseif (empty($obj->debit_account) && !empty($obj->credit_account))
					{
						$aArrayDet[$obj->fk_contab_seat]['credit_account']=$obj->amount;
						$aArray['credit_amount'] += $obj->amount;
					}
					elseif (!empty($obj->debit_account) && !empty($obj->credit_account))
					{
						$aArray['debit_amount'] += $obj->amount;
						$aArray['credit_amount'] += $obj->amount;
					}
					$i++;
				}
			}
			$this->db->free($resql);

			return array($aArray,$aArrayDet);
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::get_list_account ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	varchar		$ref    Ref object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function fetch_list_account($ref,$dateini='',$datefin='',$closing_code='')
	{
		global $langs,$conf;
		$sql = "SELECT";
		$sql.= " t.rowid,";
		$sql.= " t.fk_contab_seat,";
		$sql.= " t.debit_account,";
		$sql.= " t.debit_detail,";
		$sql.= " t.credit_account,";
		$sql.= " t.credit_detail,";
		$sql.= " t.dcd,";
		$sql.= " t.dcc,";
		$sql.= " t.amount,";
		$sql.= " t.history,";
		$sql.= " t.sequence,";
		$sql.= " t.fk_standard_seat,";
		$sql.= " t.type_seat,";
		$sql.= " t.routines,";
		$sql.= " t.value02,";
		$sql.= " t.value03,";
		$sql.= " t.value04,";
		$sql.= " t.date_rate,";
		$sql.= " t.rate,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.datec,";
		$sql.= " t.status, ";
		$sql.= " s.date_seat,";
		$sql.= " s.ref";

		$sql.= " FROM ".MAIN_DB_PREFIX."contab_seat_det as t";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."contab_seat AS s ON s.rowid = t.fk_contab_seat ";
		$sql.= " WHERE s.entity = ".$conf->entity;
		$sql.= " AND (t.debit_account = '".$ref."' ";
		$sql.= " OR t.credit_account = '".$ref."') ";
		$sql.= " AND s.entity = ".$conf->entity;
		if (!empty($dateini) && !empty($datefin))
			$sql.= " AND s.date_seat BETWEEN '".$this->db->idate($dateini)."' AND '".$this->db->idate($datefin)."' ";
		if ($closing_code)
			$sql.= " AND t.codtr != '".trim($closing_code)."'";
		//vamos a ordenar por asiento
		$sql.= " ORDER BY s.ref ASC, s.date_seat ASC";
		dol_syslog(get_class($this)."::fetch_list_account sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);

		$this->aArray = array();
		$this->aArrayDet = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					if (!empty($obj->debit_account) && (empty($obj->credit_account) || is_null($obj->credit_account)))
					{
						$this->aArrayDet[$obj->fk_contab_seat]['debit_account']+=$obj->amount;
						$this->aArray['debit_amount']+= $obj->amount;
					}
					elseif ((is_null($obj->debit_account) || empty($obj->debit_account)) && !empty($obj->credit_account))
					{
						$this->aArrayDet[$obj->fk_contab_seat]['credit_account']+=$obj->amount;
						$this->aArray['credit_amount']+= $obj->amount;
					}
					elseif (!empty($obj->debit_account) && !empty($obj->credit_account))
					{
						$this->aArray['debit_amount']+= $obj->amount;
						$this->aArray['credit_amount']+= $obj->amount;
						$this->aArrayDet[$obj->fk_contab_seat]['debit_account']+=$obj->amount;
						$this->aArrayDet[$obj->fk_contab_seat]['credit_account']+=$obj->amount;
					}
					$i++;
				}
			}
			$this->db->free($resql);
			return $num;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fecth_list_account ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	varchar		$ref    Ref object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function fetch_list_account_group($ref,$dateini='',$datefin='')
	{
		global $langs,$conf;
		$sql = "SELECT";
		$sql.= " t.debit_account,";
		$sql.= " t.credit_account,";
		$sql.= " SUM(t.amount) AS amount";

		$sql.= " FROM ".MAIN_DB_PREFIX."contab_seat_det as t";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."contab_seat AS s ON s.rowid = t.fk_contab_seat ";
		$sql.= " WHERE (t.debit_account = '".$ref."' ";
		$sql.= " OR t.credit_account = '".$ref."') ";
		$sql.= " AND s.entity = ".$conf->entity;
		if (!empty($dateini) && !empty($datefin))
			$sql.= " AND s.date_seat BETWEEN '".$this->db->idate($dateini)."' AND '".$this->db->idate($datefin)."' ";
		$sql.= " GROUP BY t.debit_account, t.credit_account";
		dol_syslog(get_class($this)."::fetch_list_account sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);

		$this->aArray = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					if (!empty($obj->debit_account) && (empty($obj->credit_account) || is_null($obj->credit_account)))
					{
						$this->aArray['debit_amount']+= $obj->amount;
					}
					elseif ((is_null($obj->debit_account) || empty($obj->debit_account)) && !empty($obj->credit_account))
					{
						$this->aArray['credit_amount']+= $obj->amount;
					}
					elseif (!empty($obj->debit_account) && !empty($obj->credit_account))
					{
						$this->aArray['debit_amount']+= $obj->amount;
						$this->aArray['credit_amount']+= $obj->amount;
					}
					$i++;
				}
			}
			$this->db->free($resql);
			return $num;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch_list_account_group ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 * Delete object in database
	 *
	 * @param User $user      User that deletes
	 * @param bool $notrigger false=launch triggers after, true=disable triggers
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function delete_block($id)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$error = 0;

		$this->db->begin();

		if (!$error) {
			if (!$notrigger) {
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action calls a trigger.

				//// Call triggers
				//$result=$this->call_trigger('MYOBJECT_DELETE',$user);
				//if ($result < 0) { $error++; //Do also what you must do to rollback action if trigger fail}
				//// End call triggers
			}
		}

		// If you need to delete child tables to, you can insert them here

		if (!$error) {
			$sql = 'DELETE FROM ' . MAIN_DB_PREFIX . $this->table_element;
			$sql .= ' WHERE fk_contab_seat=' . $id;

			$resql = $this->db->query($sql);
			if (!$resql) {
				$error ++;
				$this->errors[] = 'Error ' . $this->db->lasterror();
				dol_syslog(__METHOD__ . ' ' . implode(',', $this->errors), LOG_ERR);
			}
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