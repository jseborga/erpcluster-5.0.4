<?php
require_once DOL_DOCUMENT_ROOT.'/salary/class/pcontract.class.php';

class Pcontractext extends Pcontract
{
		//MODIFICACIONES

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
			$sql.= " t.fk_unit,";
			$sql.= " t.date_ini,";
			$sql.= " t.date_fin,";
			$sql.= " t.basic,";
			$sql.= " t.basic_fixed,";
			$sql.= " t.nivel,";
			$sql.= " t.bonus_old,";
			$sql.= " t.hours,";
			$sql.= " t.nua_afp,";
			$sql.= " t.afp,";
			$sql.= " t.fk_user_create,";
			$sql.= " t.fk_user_mod,";
			$sql.= " t.date_create,";
			$sql.= " t.date_mod,";
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
					$line->fk_unit = $obj->fk_unit;
					$line->date_ini = $this->db->jdate($obj->date_ini);
					$line->date_fin = $this->db->jdate($obj->date_fin);
					$line->basic = $obj->basic;
					$line->basic_fixed = $obj->basic_fixed;
					$line->nivel = $obj->nivel;
					$line->bonus_old = $obj->bonus_old;
					$line->hours = $obj->hours;
					$line->nua_afp = $obj->nua_afp;
					$line->afp = $obj->afp;
					$line->fk_user_create = $obj->fk_user_create;
					$line->fk_user_mod = $obj->fk_user_mod;
					$line->date_create = $obj->date_create;
					$line->date_mod = $obj->date_mod;
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
	 *  Load object in memory from the database
	 *
	 *  @param	int		$idUser    Id adherent
	 *  @param	int		$state state
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function fetch_vigent($idUser,$state=1)
	{
		global $langs;
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
		$sql.= " t.fk_unit,";
		$sql.= " t.number_item,";
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

		$sql.= " FROM ".MAIN_DB_PREFIX."p_contract as t";
		$sql.= " WHERE t.fk_user = ".$idUser;
		$sql.= " AND state =".$state;

		dol_syslog(get_class($this)."::fetch_vigent sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$obj = $this->db->fetch_object($resql);

				$this->id    = $obj->rowid;

				$this->ref = $obj->ref;
				$this->fk_user = $obj->fk_user;
				$this->fk_departament = $obj->fk_departament;
				$this->fk_charge = $obj->fk_charge;
				$this->fk_proces = $obj->fk_proces;
				$this->fk_cc = $obj->fk_cc;
				$this->fk_account = $obj->fk_account;
				$this->fk_unit = $obj->fk_unit;
				$this->number_item = $obj->number_item;
				$this->date_ini = $this->db->jdate($obj->date_ini);
				$this->date_fin = $this->db->jdate($obj->date_fin);
				$this->basic = $obj->basic;
				$this->state = $obj->state;

				$this->fk_regional=$obj->regional;
				$this->basic_fixed=$obj->basic_fixed;
				$this->nivel=$obj->nivel;
				$this->bonus_old=$obj->bonus_old;
				$this->hours=$obj->hours;
				$this->nua_afp=$obj->nua_afp;
				$this->afp=$obj->afp;

			}
			$this->db->free($resql);

			return $num;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch_vigent ".$this->error, LOG_ERR);
			return -1;
		}
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