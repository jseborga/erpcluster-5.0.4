<?php
require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementadd.class.php';

class StockMouvementaddext extends Stockmouvementadd
{
	/**
	 * Update object into database
	 *
	 * @param  User $user      User that modifies
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function update_close_period(User $user)
	{
		$error = 0;

		dol_syslog(__METHOD__, LOG_DEBUG);

		// Clean parameters

		if (isset($this->value_peps_adq)) {
			 $this->value_peps_adq = trim($this->value_peps_adq);
		}
		if (isset($this->value_ueps_adq)) {
			 $this->value_ueps_adq = trim($this->value_ueps_adq);
		}

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';

		$sql .= ' value_peps_adq = '.(isset($this->value_peps_adq)?$this->value_peps_adq:"null").',';
		$sql .= ' value_ueps_adq = '.(isset($this->value_ueps_adq)?$this->value_ueps_adq:"null").',';
		$sql .= ' date_mod = '.(! isset($this->date_mod) || dol_strlen($this->date_mod) != 0 ? "'".$this->db->idate($this->date_mod)."'" : 'null').',';
		$sql .= ' fk_user_mod = '.$user->id.',';
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'");

		$sql .= ' WHERE rowid=' . $this->id;

		$this->db->begin();

		$resql = $this->db->query($sql);
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . implode(',', $this->errors), LOG_ERR);
		}

		if (!$error && !$notrigger) {
			// Uncomment this and change MYOBJECT to your own tag if you
			// want this action calls a trigger.

			//// Call triggers
			//$result=$this->call_trigger('MYOBJECT_MODIFY',$user);
			//if ($result < 0) { $error++; //Do also what you must do to rollback action if trigger fail}
			//// End call triggers
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

	/**
	 * Load object in memory from the database
	 *
	 * @param int    $id  Id object
	 * @param string $ref Ref
	 *
	 * @return int <0 if KO, 0 if not found, >0 if OK
	 */
	//VERIFICARRRRR
	function getlist()
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.fk_stock_mouvement,";
		$sql .= " t.period_year,";
		$sql .= " t.month_year,";
		$sql .= " t.fk_stock_mouvement_doc,";
		$sql .= " t.fk_facture,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.fk_parent_line,";
		$sql .= " t.qty,";
		$sql .= " t.balance_peps,";
		$sql .= " t.balance_ueps,";
		$sql .= " t.value_peps,";
		$sql .= " t.value_ueps,";
		$sql .= " t.date_create,";
		$sql .= " t.date_mod,";
		$sql .= " t.tms,";
		$sql .= " t.status";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . 'stock_mouvement_add' . ' as t';
		$sql.= ' WHERE 1 = 1';
		if ($fk>0) {
			$sql .= ' AND t.fk_stock_mouvement = ' . $fk;
		} else {
			$sql .= ' AND t.rowid = ' . $id;
		}

		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			if ($numrows) {
				$obj = $this->db->fetch_object($resql);

				$this->id = $obj->rowid;

				$this->fk_stock_mouvement = $obj->fk_stock_mouvement;
				$this->period_year = $obj->period_year;
				$this->month_year = $obj->month_year;
				$this->fk_stock_mouvement_doc = $obj->fk_stock_mouvement_doc;
				$this->fk_facture = $obj->fk_facture;
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_mod = $obj->fk_user_mod;
				$this->fk_parent_line = $obj->fk_parent_line;
				$this->qty = $obj->qty;
				$this->balance_peps = $obj->balance_peps;
				$this->balance_ueps = $obj->balance_ueps;
				$this->value_peps = $obj->value_peps;
				$this->value_ueps = $obj->value_ueps;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->date_mod = $this->db->jdate($obj->date_mod);
				$this->tms = $this->db->jdate($obj->tms);
				$this->status = $obj->status;


			}

			// Retrieve all extrafields for invoice
			// fetch optionals attributes and labels
			require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
			$extrafields=new ExtraFields($this->db);
			$extralabels=$extrafields->fetch_name_optionals_label($this->table_element,true);
			$this->fetch_optionals($this->id,$extralabels);

			// $this->fetch_lines();

			$this->db->free($resql);

			if ($numrows) {
				return 1;
			} else {
				return 0;
			}
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . implode(',', $this->errors), LOG_ERR);

			return - 1;
		}

	}
}
?>