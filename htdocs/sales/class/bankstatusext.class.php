<?php 
require_once DOL_DOCUMENT_ROOT.'/sales/class/bankstatus.class.php';

class Bankstatusext extends Bankstatus
{
		//MODIFICADO
	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK =0 if null
	 */
	function fetch_banklast($fk_bank,$statut=1)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";		
		$sql.= " t.fk_bank,";
		$sql.= " t.fk_user,";
		$sql.= " t.fk_subsidiary,";
		$sql.= " t.fk_bank_historial,";
		$sql.= " t.date_register,";
		$sql.= " t.date_close,";
		$sql.= " t.exchange,";
		$sql.= " t.previus_balance,";
		$sql.= " t.amount,";
		$sql.= " t.text_amount,";
		$sql.= " t.amount_open,";
		$sql.= " t.text_amount_open,";
		$sql.= " t.amount_balance,";
		$sql.= " t.amount_income,";
		$sql.= " t.amount_input,";
		$sql.= " t.amount_sale,";
		$sql.= " t.amount_null,";
		$sql.= " t.amount_advance,";
		$sql.= " t.amount_transf_input,";
		$sql.= " t.amount_transf_output,";
		$sql.= " t.amount_spending,";
		$sql.= " t.amount_expense,";
		$sql.= " t.amount_close,";
		$sql.= " t.missing_money,";
		$sql.= " t.leftover_money,";
		$sql.= " t.amount_exchange,";
		$sql.= " t.invoice_annulled,";
		$sql.= " t.text_exchange,";
		$sql.= " t.text_close,";
		$sql.= " t.detail,";
		$sql.= " t.var_detail,";
		$sql.= " t.typecash,";
		$sql.= " t.model_pdf,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.fk_user_close,";
		$sql.= " t.tms,";
		$sql.= " t.statut";
		$sql.= " FROM ".MAIN_DB_PREFIX."bank_status as t";
		$sql.= " WHERE t.fk_bank = ".$fk_bank;
		if ($statut)
			$sql.= " AND t.statut IN (".$statut.")";
		$sql.= " ORDER BY t.date_register DESC ";
		dol_syslog(get_class($this)."::fetch_banklast sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);
				
				$this->id    = $obj->rowid;		  
				$this->fk_bank = $obj->fk_bank;
				$this->fk_user = $obj->fk_user;
				$this->fk_subsidiary = $obj->fk_subsidiary;
				$this->fk_bank_historial = $obj->fk_bank_historial;
				$this->date_register = $this->db->jdate($obj->date_register);
				$this->date_close = $this->db->jdate($obj->date_close);
				$this->exchange = $obj->exchange;
				$this->previus_balance = $obj->previus_balance;
				$this->amount = $obj->amount;
				$this->text_amount = $obj->text_amount;
				$this->amount_open = $obj->amount_open;
				$this->text_amount_open = $obj->text_amount_open;
				$this->amount_balance = $obj->amount_balance;
				$this->amount_income = $obj->amount_income;
				$this->amount_sale = $obj->amount_sale;
				$this->amount_null = $obj->amount_null;
				$this->amount_advance = $obj->amount_advance;
				$this->amount_transf_input = $obj->amount_transf_input;
				$this->amount_transf_output = $obj->amount_transf_output;
				$this->amount_spending = $obj->amount_spending;
				$this->amount_input = $obj->amount_input;
				$this->amount_expense = $obj->amount_expense;
				$this->amount_close = $obj->amount_close;
				$this->missing_money = $obj->missing_money;
				$this->leftover_money = $obj->leftover_money;
				$this->amount_exchange = $obj->amount_exchange;
				$this->invoice_annulled = $obj->invoice_annulled;
				$this->text_exchange = $obj->text_exchange;
				$this->text_close = $obj->text_close;
				$this->detail = $obj->detail;
				$this->var_detail = $obj->var_detail;
				$this->typecash = $obj->typecash;
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_close = $obj->fk_user_close;
				$this->tms = $this->db->jdate($obj->tms);
				$this->statut = $obj->statut;
				return 1;
			}
			$this->db->free($resql);
			return 0;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch_banklast ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK =0 if null
	 */
	function getlist($objuser,$statut = 1,$fk_subsidiary=0)
	{
		global $langs, $conf;
		$sql = "SELECT";
		$sql.= " t.rowid,";		
		$sql.= " t.fk_bank,";
		$sql.= " t.fk_user,";
		$sql.= " t.fk_subsidiary,";
		$sql.= " t.fk_bank_historial,";
		$sql.= " t.date_register,";
		$sql.= " t.date_close,";
		$sql.= " t.exchange,";
		$sql.= " t.previus_balance,";
		$sql.= " t.amount,";
		$sql.= " t.text_amount,";
		$sql.= " t.amount_open,";
		$sql.= " t.text_amount_open,";		
		$sql.= " t.amount_balance,";
		$sql.= " t.amount_income,";
		$sql.= " t.amount_input,";
		$sql.= " t.amount_sale,";
		$sql.= " t.amount_null,";
		$sql.= " t.amount_advance,";
		$sql.= " t.amount_transf_input,";
		$sql.= " t.amount_transf_output,";
		$sql.= " t.amount_spending,";
		$sql.= " t.amount_expense,";
		$sql.= " t.amount_close,";
		$sql.= " t.missing_money,";
		$sql.= " t.leftover_money,";
		$sql.= " t.amount_exchange,";
		$sql.= " t.invoice_annulled,";
		$sql.= " t.text_exchange,";
		$sql.= " t.text_close,";
		$sql.= " t.detail,";
		$sql.= " t.var_detail,";
		$sql.= " t.typecash,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.fk_user_close,";
		$sql.= " t.tms,";
		$sql.= " t.statut";
		$sql.= " FROM ".MAIN_DB_PREFIX."bank_status as t";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."bank_account AS b ON t.fk_bank = b.rowid ";
		$sql.= " WHERE b.entity = ".$conf->entity;
		$sql.= " AND t.statut IN (".$statut.")";
	  //	  if (!$user->admin)
		$sql.= " AND t.fk_user = ".$objuser->id;
		if ($fk_subsidiary>0)
			$sql.= " AND t.fk_subsidiary = ".$fk_subsidiary;
		$sql.= " ORDER BY t.date_register DESC ";
		dol_syslog(get_class($this)."::getlist sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->array = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($this->db->num_rows($resql))
			{
				$i  = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objnew = new Bankstatus($this->db);
					
					$objnew->id    = $obj->rowid;		  
					$objnew->fk_bank = $obj->fk_bank;
					$objnew->fk_user = $obj->fk_user;
					$objnew->fk_subsidiary = $obj->fk_subsidiary;
					$objnew->fk_bank_historial = $obj->fk_bank_historial;
					$objnew->date_register = $this->db->jdate($obj->date_register);
					$objnew->date_close = $this->db->jdate($obj->date_close);
					$objnew->exchange = $obj->exchange;
					$objnew->previus_balance = $obj->previus_balance;
					$objnew->amount = $obj->amount;
					$objnew->text_amount = $obj->text_amount;
					$objnew->amount_open = $obj->amount_open;
					$objnew->text_amount_open = $obj->text_amount_open;
					$objnew->amount_balance = $obj->amount_balance;
					$objnew->amount_income = $obj->amount_income;
					$objnew->amount_input = $obj->amount_input;
					$objnew->amount_sale = $obj->amount_sale;
					$objnew->amount_null = $obj->amount_null;
					$objnew->amount_advance = $obj->amount_advance;
					$objnew->amount_transf_input = $obj->amount_transf_input;
					$objnew->amount_transf_output = $obj->amount_transf_output;
					$objnew->amount_spending = $obj->amount_spending;
					$objnew->amount_expense = $obj->amount_expense;
					$objnew->amount_close = $obj->amount_close;
					$objnew->missing_money = $obj->missing_money;
					$objnew->leftover_money = $obj->leftover_money;
					$objnew->amount_exchange = $obj->amount_exchange;
					$objnew->invoice_annulled = $obj->invoice_annulled;
					$objnew->text_exchange = $obj->text_exchange;
					$objnew->text_close = $obj->text_close;
					$objnew->detail = $obj->detail;
					$objnew->var_detail = $obj->var_detail;
					$objnew->typecash = $obj->typecash;
					$objnew->fk_user_create = $obj->fk_user_create;
					$objnew->fk_user_close = $obj->fk_user_close;
					$objnew->tms = $this->db->jdate($obj->tms);
					$objnew->statut = $obj->statut;
					$this->array[$obj->rowid] = $objnew;
					$i++;
				}
			}
			$this->db->free($resql);
			return $num;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getlist ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id subsidiary
	 *  @return int          	<0 if KO, >0 if OK =0 if null
	 */
	function getlist_subsidiary($id)
	{
		global $langs, $conf;
		$sql = "SELECT";
		$sql.= " t.rowid,";		
		$sql.= " t.fk_bank,";
		$sql.= " t.fk_user,";
		$sql.= " t.fk_subsidiary,";
		$sql.= " t.fk_bank_historial,";
		$sql.= " t.date_register,";
		$sql.= " t.date_close,";
		$sql.= " t.exchange,";
		$sql.= " t.previus_balance,";
		$sql.= " t.amount,";
		$sql.= " t.text_amount,";
		$sql.= " t.amount_open,";
		$sql.= " t.text_amount_open,";
		$sql.= " t.amount_balance,";
		$sql.= " t.amount_income,";
		$sql.= " t.amount_input,";
		$sql.= " t.amount_sale,";
		$sql.= " t.amount_null,";
		$sql.= " t.amount_advance,";
		$sql.= " t.amount_transf_input,";
		$sql.= " t.amount_transf_output,";
		$sql.= " t.amount_spending,";
		$sql.= " t.amount_expense,";
		$sql.= " t.amount_close,";
		$sql.= " t.missing_money,";
		$sql.= " t.leftover_money,";
		$sql.= " t.amount_exchange,";
		$sql.= " t.invoice_annulled,";
		$sql.= " t.text_exchange,";
		$sql.= " t.text_close,";
		$sql.= " t.detail,";
		$sql.= " t.var_detail,";
		$sql.= " t.typecash,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.fk_user_close,";
		$sql.= " t.tms,";
		$sql.= " t.statut";
		$sql.= " FROM ".MAIN_DB_PREFIX."bank_status as t";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."bank_account AS b ON t.fk_bank = b.rowid ";
		$sql.= " WHERE b.entity = ".$conf->entity;
		$sql.= " AND t.fk_subsidiary = ".$id;
		dol_syslog(get_class($this)."::getlist_subsidiary sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->array = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($this->db->num_rows($resql))
			{
				$i  = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objnew = new Bankstatus($this->db);
					
					$objnew->id    = $obj->rowid;		  
					$objnew->fk_bank = $obj->fk_bank;
					$objnew->fk_user = $obj->fk_user;
					$objnew->fk_subsidiary = $obj->fk_subsidiary;
					$objnew->fk_bank_historial = $obj->fk_bank_historial;
					$objnew->date_register = $this->db->jdate($obj->date_register);
					$objnew->date_close = $this->db->jdate($obj->date_close);
					$objnew->exchange = $obj->exchange;
					$objnew->previus_balance = $obj->previus_balance;
					$objnew->amount = $obj->amount;
					$objnew->text_amount = $obj->text_amount;
					$objnew->amount_open = $obj->amount_open;
					$objnew->text_amount_open = $obj->text_amount_open;
					$objnew->amount_balance = $obj->amount_balance;
					$objnew->amount_income = $obj->amount_income;
					$objnew->amount_input = $obj->amount_input;
					$objnew->amount_sale = $obj->amount_sale;
					$objnew->amount_null = $obj->amount_null;
					$objnew->amount_advance = $obj->amount_advance;
					$objnew->amount_transf_input = $obj->amount_transf_input;
					$objnew->amount_transf_output = $obj->amount_transf_output;
					$objnew->amount_spending = $obj->amount_spending;
					$objnew->amount_expense = $obj->amount_expense;
					$objnew->amount_close = $obj->amount_close;
					$objnew->missing_money = $obj->missing_money;
					$objnew->leftover_money = $obj->leftover_money;
					$objnew->amount_exchange = $obj->amount_exchange;
					$objnew->invoice_annulled = $obj->invoice_annulled;
					$objnew->text_exchange = $obj->text_exchange;
					$objnew->text_close = $obj->text_close;
					$objnew->detail = $obj->detail;
					$objnew->var_detail = $obj->var_detail;
					$objnew->typecash = $obj->typecash;
					$objnew->fk_user_create = $obj->fk_user_create;
					$objnew->fk_user_close = $obj->fk_user_close;
					$objnew->tms = $this->db->jdate($obj->tms);
					$objnew->statut = $obj->statut;
					$this->array[$obj->rowid] = $objnew;
					$i++;
				}
			}
			$this->db->free($resql);
			return $num;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getlist_subsidiary ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK =0 if null
	 */
	function getlast($fk_bank,$fk_user=0,$statut='')
	{
		global $langs, $conf;
		$sql = "SELECT";
		$sql.= " t.rowid,";		
		$sql.= " t.fk_bank,";
		$sql.= " t.fk_user,";
		$sql.= " t.fk_subsidiary,";
		$sql.= " t.fk_bank_historial,";
		$sql.= " t.date_register,";
		$sql.= " t.date_close,";
		$sql.= " t.exchange,";
		$sql.= " t.previus_balance,";
		$sql.= " t.amount,";
		$sql.= " t.text_amount,";
		$sql.= " t.amount_open,";
		$sql.= " t.text_amount_open,";
		$sql.= " t.amount_balance,";
		$sql.= " t.amount_income,";
		$sql.= " t.amount_input,";
		$sql.= " t.amount_sale,";
		$sql.= " t.amount_null,";
		$sql.= " t.amount_advance,";
		$sql.= " t.amount_transf_input,";
		$sql.= " t.amount_transf_output,";
		$sql.= " t.amount_spending,";
		$sql.= " t.amount_expense,";
		$sql.= " t.amount_close,";
		$sql.= " t.missing_money,";
		$sql.= " t.leftover_money,";
		$sql.= " t.amount_exchange,";
		$sql.= " t.invoice_annulled,";
		$sql.= " t.text_exchange,";
		$sql.= " t.text_close,";
		$sql.= " t.detail,";
		$sql.= " t.var_detail,";
		$sql.= " t.typecash,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.fk_user_close,";
		$sql.= " t.tms,";
		$sql.= " t.statut";
		$sql.= " FROM ".MAIN_DB_PREFIX."bank_status as t";
		$sql.= " WHERE t.fk_bank = ".$fk_bank;
		if ($fk_user)
			$sql.= " AND t.fk_user = ".$fk_user;
		if ($statut)
			$sql.= " AND t.statut = ".$statut;

		$sql.= " ORDER BY t.date_register DESC ";
		$sql.= $this->db->plimit(0,2);
		dol_syslog(get_class($this)."::getlast sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);
				$this->id      = $obj->rowid;		  
				$this->fk_bank = $obj->fk_bank;
				$this->fk_user = $obj->fk_user;
				$this->fk_subsidiary = $obj->fk_subsidiary;
				$this->fk_bank_historial = $obj->fk_bank_historial;
				$this->date_register = $this->db->jdate($obj->date_register);
				$this->date_close = $this->db->jdate($obj->date_close);
				$this->exchange = $obj->exchange;
				$this->previus_balance = $obj->previus_balance;
				$this->amount = $obj->amount;
				$this->text_amount = $obj->text_amount;
				$this->amount_open = $obj->amount_open;
				$this->text_amount_open = $obj->text_amount_open;
				$this->amount_balance = $obj->amount_balance;
				$this->amount_income = $obj->amount_income;
				$this->amount_input = $obj->amount_input;
				$this->amount_sale = $obj->amount_sale;
				$this->amount_null = $obj->amount_null;
				$this->amount_advance = $obj->amount_advance;
				$this->amount_transf_input = $obj->amount_transf_input;
				$this->amount_transf_output = $obj->amount_transf_output;
				$this->amount_spending = $obj->amount_spending;
				$this->amount_expense = $obj->amount_expense;
				$this->amount_close = $obj->amount_close;
				$this->missing_money = $obj->missing_money;
				$this->leftover_money = $obj->leftover_money;
				$this->amount_exchange = $obj->amount_exchange;
				$this->invoice_annulled = $obj->invoice_annulled;
				$this->text_exchange = $obj->text_exchange;
				$this->text_close = $obj->text_close;
				$this->detail = $obj->detail;
				$this->var_detail = $obj->var_detail;
				$this->typecash = $obj->typecash;
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_close = $obj->fk_user_close;
				$this->tms = $this->db->jdate($obj->tms);
				$this->statut = $obj->statut;
			}
			$this->db->free($resql);
			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getlast ".$this->error, LOG_ERR);
			return -1;
		}
	}

	function set_report($array)
	{
		global $conf,$langs;
		if (count($array)>0)
		{
			$this->array = $array;
			return 1;
		}
		return -1;
	}
	function set_parameter($var,$array)
	{
		global $conf,$langs;
		
		if ($var)
		{
			$this->aParameter[$var] = $array;
			return 1;
		}
		return -1;
	}
	function set_closebank($var,$value)
	{
		global $conf,$langs;
		
		if (!empty($var))
		{
			$this->aClose[$var] = $value;
			return 1;
		}
		return -1;
	}

	function set_sales($var,$valuemode1,$valuemode2,$valueconvert)
	{
		global $conf,$langs;

		if (!empty($var))
		{
			
		  //$this->aSales[$var] = array($valuemode1,$valuemode2,$valueconvert);
			$this->aSales[$var]['local'] += $valuemode1;
			$this->aSales[$var]['ext'] += $valuemode2;
			$this->aSales[$var]['convert'] += $valueconvert;
			return 1;
		}
		return -1;
	}

}
?>