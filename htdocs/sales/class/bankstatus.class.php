<?php
/* Copyright (C) 2007-2012  Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) 2014       Juanjo Menent       <jmenent@2byte.es>
 * Copyright (C) 2015       Florian Henry       <florian.henry@open-concept.pro>
 * Copyright (C) 2015       Raphaël Doursenaud  <rdoursenaud@gpcsolutions.fr>
 * Copyright (C) ---Put here your own copyright and developer email---
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    sales/bankstatus.class.php
 * \ingroup sales
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Bankstatus
 *
 * Put here description of your class
 * @see CommonObject
 */
class Bankstatus extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'bankstatus';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'bank_status';

	/**
	 * @var BankstatusLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	
	public $fk_bank;
	public $fk_user;
	public $fk_subsidiary;
	public $fk_bank_historial;
	public $date_register = '';
	public $date_close = '';
	public $exchange;
	public $previus_balance;
	public $amount;
	public $text_amount;
	public $amount_open;
	public $text_amount_open;
	public $amount_balance;
	public $amount_income;
	public $amount_input;
	public $amount_sale;
	public $amount_null;
	public $amount_advance;
	public $amount_transf_input;
	public $amount_transf_output;
	public $amount_spending;
	public $amount_expense;
	public $amount_close;
	public $missing_money;
	public $leftover_money;
	public $amount_exchange;
	public $invoice_annulled;
	public $text_exchange;
	public $text_close;
	public $detail;
	public $var_detail;
	public $typecash;
	public $model_pdf;
	public $fk_user_create;
	public $fk_user_close;
	public $fk_user_mod;
	public $date_create = '';
	public $date_mod = '';
	public $tms = '';
	public $statut;

	/**
	 */
	

	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $db)
	{
		$this->db = $db;
	}

	/**
	 * Create object into database
	 *
	 * @param  User $user      User that creates
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 *
	 * @return int <0 if KO, Id of created object if OK
	 */
	public function create(User $user, $notrigger = false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$error = 0;

		// Clean parameters
		
		if (isset($this->fk_bank)) {
			 $this->fk_bank = trim($this->fk_bank);
		}
		if (isset($this->fk_user)) {
			 $this->fk_user = trim($this->fk_user);
		}
		if (isset($this->fk_subsidiary)) {
			 $this->fk_subsidiary = trim($this->fk_subsidiary);
		}
		if (isset($this->fk_bank_historial)) {
			 $this->fk_bank_historial = trim($this->fk_bank_historial);
		}
		if (isset($this->exchange)) {
			 $this->exchange = trim($this->exchange);
		}
		if (isset($this->previus_balance)) {
			 $this->previus_balance = trim($this->previus_balance);
		}
		if (isset($this->amount)) {
			 $this->amount = trim($this->amount);
		}
		if (isset($this->text_amount)) {
			 $this->text_amount = trim($this->text_amount);
		}
		if (isset($this->amount_open)) {
			 $this->amount_open = trim($this->amount_open);
		}
		if (isset($this->text_amount_open)) {
			 $this->text_amount_open = trim($this->text_amount_open);
		}
		if (isset($this->amount_balance)) {
			 $this->amount_balance = trim($this->amount_balance);
		}
		if (isset($this->amount_income)) {
			 $this->amount_income = trim($this->amount_income);
		}
		if (isset($this->amount_input)) {
			 $this->amount_input = trim($this->amount_input);
		}
		if (isset($this->amount_sale)) {
			 $this->amount_sale = trim($this->amount_sale);
		}
		if (isset($this->amount_null)) {
			 $this->amount_null = trim($this->amount_null);
		}
		if (isset($this->amount_advance)) {
			 $this->amount_advance = trim($this->amount_advance);
		}
		if (isset($this->amount_transf_input)) {
			 $this->amount_transf_input = trim($this->amount_transf_input);
		}
		if (isset($this->amount_transf_output)) {
			 $this->amount_transf_output = trim($this->amount_transf_output);
		}
		if (isset($this->amount_spending)) {
			 $this->amount_spending = trim($this->amount_spending);
		}
		if (isset($this->amount_expense)) {
			 $this->amount_expense = trim($this->amount_expense);
		}
		if (isset($this->amount_close)) {
			 $this->amount_close = trim($this->amount_close);
		}
		if (isset($this->missing_money)) {
			 $this->missing_money = trim($this->missing_money);
		}
		if (isset($this->leftover_money)) {
			 $this->leftover_money = trim($this->leftover_money);
		}
		if (isset($this->amount_exchange)) {
			 $this->amount_exchange = trim($this->amount_exchange);
		}
		if (isset($this->invoice_annulled)) {
			 $this->invoice_annulled = trim($this->invoice_annulled);
		}
		if (isset($this->text_exchange)) {
			 $this->text_exchange = trim($this->text_exchange);
		}
		if (isset($this->text_close)) {
			 $this->text_close = trim($this->text_close);
		}
		if (isset($this->detail)) {
			 $this->detail = trim($this->detail);
		}
		if (isset($this->var_detail)) {
			 $this->var_detail = trim($this->var_detail);
		}
		if (isset($this->typecash)) {
			 $this->typecash = trim($this->typecash);
		}
		if (isset($this->model_pdf)) {
			 $this->model_pdf = trim($this->model_pdf);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_close)) {
			 $this->fk_user_close = trim($this->fk_user_close);
		}
		if (isset($this->fk_user_mod)) {
			 $this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->statut)) {
			 $this->statut = trim($this->statut);
		}

		

		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';
		
		$sql.= 'fk_bank,';
		$sql.= 'fk_user,';
		$sql.= 'fk_subsidiary,';
		$sql.= 'fk_bank_historial,';
		$sql.= 'date_register,';
		$sql.= 'date_close,';
		$sql.= 'exchange,';
		$sql.= 'previus_balance,';
		$sql.= 'amount,';
		$sql.= 'text_amount,';
		$sql.= 'amount_open,';
		$sql.= 'text_amount_open,';
		$sql.= 'amount_balance,';
		$sql.= 'amount_income,';
		$sql.= 'amount_input,';
		$sql.= 'amount_sale,';
		$sql.= 'amount_null,';
		$sql.= 'amount_advance,';
		$sql.= 'amount_transf_input,';
		$sql.= 'amount_transf_output,';
		$sql.= 'amount_spending,';
		$sql.= 'amount_expense,';
		$sql.= 'amount_close,';
		$sql.= 'missing_money,';
		$sql.= 'leftover_money,';
		$sql.= 'amount_exchange,';
		$sql.= 'invoice_annulled,';
		$sql.= 'text_exchange,';
		$sql.= 'text_close,';
		$sql.= 'detail,';
		$sql.= 'var_detail,';
		$sql.= 'typecash,';
		$sql.= 'model_pdf,';
		$sql.= 'fk_user_create,';
		$sql.= 'fk_user_close,';
		$sql.= 'fk_user_mod,';
		$sql.= 'date_create,';
		$sql.= 'date_mod';
		$sql.= 'statut';

		
		$sql .= ') VALUES (';
		
		$sql .= ' '.(! isset($this->fk_bank)?'NULL':$this->fk_bank).',';
		$sql .= ' '.(! isset($this->fk_user)?'NULL':$this->fk_user).',';
		$sql .= ' '.(! isset($this->fk_subsidiary)?'NULL':$this->fk_subsidiary).',';
		$sql .= ' '.(! isset($this->fk_bank_historial)?'NULL':$this->fk_bank_historial).',';
		$sql .= ' '.(! isset($this->date_register) || dol_strlen($this->date_register)==0?'NULL':"'".$this->db->idate($this->date_register)."'").',';
		$sql .= ' '.(! isset($this->date_close) || dol_strlen($this->date_close)==0?'NULL':"'".$this->db->idate($this->date_close)."'").',';
		$sql .= ' '.(! isset($this->exchange)?'NULL':"'".$this->exchange."'").',';
		$sql .= ' '.(! isset($this->previus_balance)?'NULL':"'".$this->previus_balance."'").',';
		$sql .= ' '.(! isset($this->amount)?'NULL':"'".$this->amount."'").',';
		$sql .= ' '.(! isset($this->text_amount)?'NULL':"'".$this->db->escape($this->text_amount)."'").',';
		$sql .= ' '.(! isset($this->amount_open)?'NULL':"'".$this->amount_open."'").',';
		$sql .= ' '.(! isset($this->text_amount_open)?'NULL':"'".$this->db->escape($this->text_amount_open)."'").',';
		$sql .= ' '.(! isset($this->amount_balance)?'NULL':"'".$this->amount_balance."'").',';
		$sql .= ' '.(! isset($this->amount_income)?'NULL':"'".$this->amount_income."'").',';
		$sql .= ' '.(! isset($this->amount_input)?'NULL':"'".$this->amount_input."'").',';
		$sql .= ' '.(! isset($this->amount_sale)?'NULL':"'".$this->amount_sale."'").',';
		$sql .= ' '.(! isset($this->amount_null)?'NULL':"'".$this->amount_null."'").',';
		$sql .= ' '.(! isset($this->amount_advance)?'NULL':"'".$this->amount_advance."'").',';
		$sql .= ' '.(! isset($this->amount_transf_input)?'NULL':"'".$this->amount_transf_input."'").',';
		$sql .= ' '.(! isset($this->amount_transf_output)?'NULL':"'".$this->amount_transf_output."'").',';
		$sql .= ' '.(! isset($this->amount_spending)?'NULL':"'".$this->amount_spending."'").',';
		$sql .= ' '.(! isset($this->amount_expense)?'NULL':"'".$this->amount_expense."'").',';
		$sql .= ' '.(! isset($this->amount_close)?'NULL':"'".$this->amount_close."'").',';
		$sql .= ' '.(! isset($this->missing_money)?'NULL':"'".$this->missing_money."'").',';
		$sql .= ' '.(! isset($this->leftover_money)?'NULL':"'".$this->leftover_money."'").',';
		$sql .= ' '.(! isset($this->amount_exchange)?'NULL':"'".$this->amount_exchange."'").',';
		$sql .= ' '.(! isset($this->invoice_annulled)?'NULL':"'".$this->invoice_annulled."'").',';
		$sql .= ' '.(! isset($this->text_exchange)?'NULL':"'".$this->db->escape($this->text_exchange)."'").',';
		$sql .= ' '.(! isset($this->text_close)?'NULL':"'".$this->db->escape($this->text_close)."'").',';
		$sql .= ' '.(! isset($this->detail)?'NULL':"'".$this->db->escape($this->detail)."'").',';
		$sql .= ' '.(! isset($this->var_detail)?'NULL':"'".$this->db->escape($this->var_detail)."'").',';
		$sql .= ' '.(! isset($this->typecash)?'NULL':$this->typecash).',';
		$sql .= ' '.(! isset($this->model_pdf)?'NULL':"'".$this->db->escape($this->model_pdf)."'").',';
		$sql .= ' '.(! isset($this->fk_user_create)?'NULL':$this->fk_user_create).',';
		$sql .= ' '.(! isset($this->fk_user_close)?'NULL':$this->fk_user_close).',';
		$sql .= ' '.$user->id.',';
		$sql .= ' '.(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':"'".$this->db->idate($this->date_create)."'").',';
		$sql .= ' '.(! isset($this->date_mod) || dol_strlen($this->date_mod)==0?'NULL':"'".$this->db->idate($this->date_mod)."'").',';
		$sql .= ' '.(! isset($this->statut)?'NULL':$this->statut);

		
		$sql .= ')';

		$this->db->begin();

		$resql = $this->db->query($sql);
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
		}

		if (!$error) {
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX . $this->table_element);

			if (!$notrigger) {
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action to call a trigger.

				//// Call triggers
				//$result=$this->call_trigger('MYOBJECT_CREATE',$user);
				//if ($result < 0) $error++;
				//// End call triggers
			}
		}

		// Commit or rollback
		if ($error) {
			$this->db->rollback();

			return - 1 * $error;
		} else {
			$this->db->commit();

			return $this->id;
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
	public function fetch($id, $ref = null)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		
		$sql .= " t.fk_bank,";
		$sql .= " t.fk_user,";
		$sql .= " t.fk_subsidiary,";
		$sql .= " t.fk_bank_historial,";
		$sql .= " t.date_register,";
		$sql .= " t.date_close,";
		$sql .= " t.exchange,";
		$sql .= " t.previus_balance,";
		$sql .= " t.amount,";
		$sql .= " t.text_amount,";
		$sql .= " t.amount_open,";
		$sql .= " t.text_amount_open,";
		$sql .= " t.amount_balance,";
		$sql .= " t.amount_income,";
		$sql .= " t.amount_input,";
		$sql .= " t.amount_sale,";
		$sql .= " t.amount_null,";
		$sql .= " t.amount_advance,";
		$sql .= " t.amount_transf_input,";
		$sql .= " t.amount_transf_output,";
		$sql .= " t.amount_spending,";
		$sql .= " t.amount_expense,";
		$sql .= " t.amount_close,";
		$sql .= " t.missing_money,";
		$sql .= " t.leftover_money,";
		$sql .= " t.amount_exchange,";
		$sql .= " t.invoice_annulled,";
		$sql .= " t.text_exchange,";
		$sql .= " t.text_close,";
		$sql .= " t.detail,";
		$sql .= " t.var_detail,";
		$sql .= " t.typecash,";
		$sql .= " t.model_pdf,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_close,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.date_create,";
		$sql .= " t.date_mod,";
		$sql .= " t.tms,";
		$sql .= " t.statut";

		
		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		if (null !== $ref) {
			$sql .= ' WHERE t.ref = ' . '\'' . $ref . '\'';
		} else {
			$sql .= ' WHERE t.rowid = ' . $id;
		}

		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			if ($numrows) {
				$obj = $this->db->fetch_object($resql);

				$this->id = $obj->rowid;
				
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
				$this->model_pdf = $obj->model_pdf;
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_close = $obj->fk_user_close;
				$this->fk_user_mod = $obj->fk_user_mod;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->date_mod = $this->db->jdate($obj->date_mod);
				$this->tms = $this->db->jdate($obj->tms);
				$this->statut = $obj->statut;

				
			}
			$this->db->free($resql);

			if ($numrows) {
				return 1;
			} else {
				return 0;
			}
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
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND')
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		
		$sql .= " t.fk_bank,";
		$sql .= " t.fk_user,";
		$sql .= " t.fk_subsidiary,";
		$sql .= " t.fk_bank_historial,";
		$sql .= " t.date_register,";
		$sql .= " t.date_close,";
		$sql .= " t.exchange,";
		$sql .= " t.previus_balance,";
		$sql .= " t.amount,";
		$sql .= " t.text_amount,";
		$sql .= " t.amount_open,";
		$sql .= " t.text_amount_open,";
		$sql .= " t.amount_balance,";
		$sql .= " t.amount_income,";
		$sql .= " t.amount_input,";
		$sql .= " t.amount_sale,";
		$sql .= " t.amount_null,";
		$sql .= " t.amount_advance,";
		$sql .= " t.amount_transf_input,";
		$sql .= " t.amount_transf_output,";
		$sql .= " t.amount_spending,";
		$sql .= " t.amount_expense,";
		$sql .= " t.amount_close,";
		$sql .= " t.missing_money,";
		$sql .= " t.leftover_money,";
		$sql .= " t.amount_exchange,";
		$sql .= " t.invoice_annulled,";
		$sql .= " t.text_exchange,";
		$sql .= " t.text_close,";
		$sql .= " t.detail,";
		$sql .= " t.var_detail,";
		$sql .= " t.typecash,";
		$sql .= " t.model_pdf,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_close,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.date_create,";
		$sql .= " t.date_mod,";
		$sql .= " t.tms,";
		$sql .= " t.statut";

		
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
		
		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield,$sortorder);
		}
		if (!empty($limit)) {
		 $sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
		}
		$this->lines = array();

		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new BankstatusLine();

				$line->id = $obj->rowid;
				
				$line->fk_bank = $obj->fk_bank;
				$line->fk_user = $obj->fk_user;
				$line->fk_subsidiary = $obj->fk_subsidiary;
				$line->fk_bank_historial = $obj->fk_bank_historial;
				$line->date_register = $this->db->jdate($obj->date_register);
				$line->date_close = $this->db->jdate($obj->date_close);
				$line->exchange = $obj->exchange;
				$line->previus_balance = $obj->previus_balance;
				$line->amount = $obj->amount;
				$line->text_amount = $obj->text_amount;
				$line->amount_open = $obj->amount_open;
				$line->text_amount_open = $obj->text_amount_open;
				$line->amount_balance = $obj->amount_balance;
				$line->amount_income = $obj->amount_income;
				$line->amount_input = $obj->amount_input;
				$line->amount_sale = $obj->amount_sale;
				$line->amount_null = $obj->amount_null;
				$line->amount_advance = $obj->amount_advance;
				$line->amount_transf_input = $obj->amount_transf_input;
				$line->amount_transf_output = $obj->amount_transf_output;
				$line->amount_spending = $obj->amount_spending;
				$line->amount_expense = $obj->amount_expense;
				$line->amount_close = $obj->amount_close;
				$line->missing_money = $obj->missing_money;
				$line->leftover_money = $obj->leftover_money;
				$line->amount_exchange = $obj->amount_exchange;
				$line->invoice_annulled = $obj->invoice_annulled;
				$line->text_exchange = $obj->text_exchange;
				$line->text_close = $obj->text_close;
				$line->detail = $obj->detail;
				$line->var_detail = $obj->var_detail;
				$line->typecash = $obj->typecash;
				$line->model_pdf = $obj->model_pdf;
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_user_close = $obj->fk_user_close;
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->date_mod = $this->db->jdate($obj->date_mod);
				$line->tms = $this->db->jdate($obj->tms);
				$line->statut = $obj->statut;

				

				$this->lines[$line->id] = $line;
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
	 * Update object into database
	 *
	 * @param  User $user      User that modifies
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function update(User $user, $notrigger = false)
	{
		$error = 0;

		dol_syslog(__METHOD__, LOG_DEBUG);

		// Clean parameters
		
		if (isset($this->fk_bank)) {
			 $this->fk_bank = trim($this->fk_bank);
		}
		if (isset($this->fk_user)) {
			 $this->fk_user = trim($this->fk_user);
		}
		if (isset($this->fk_subsidiary)) {
			 $this->fk_subsidiary = trim($this->fk_subsidiary);
		}
		if (isset($this->fk_bank_historial)) {
			 $this->fk_bank_historial = trim($this->fk_bank_historial);
		}
		if (isset($this->exchange)) {
			 $this->exchange = trim($this->exchange);
		}
		if (isset($this->previus_balance)) {
			 $this->previus_balance = trim($this->previus_balance);
		}
		if (isset($this->amount)) {
			 $this->amount = trim($this->amount);
		}
		if (isset($this->text_amount)) {
			 $this->text_amount = trim($this->text_amount);
		}
		if (isset($this->amount_open)) {
			 $this->amount_open = trim($this->amount_open);
		}
		if (isset($this->text_amount_open)) {
			 $this->text_amount_open = trim($this->text_amount_open);
		}
		if (isset($this->amount_balance)) {
			 $this->amount_balance = trim($this->amount_balance);
		}
		if (isset($this->amount_income)) {
			 $this->amount_income = trim($this->amount_income);
		}
		if (isset($this->amount_input)) {
			 $this->amount_input = trim($this->amount_input);
		}
		if (isset($this->amount_sale)) {
			 $this->amount_sale = trim($this->amount_sale);
		}
		if (isset($this->amount_null)) {
			 $this->amount_null = trim($this->amount_null);
		}
		if (isset($this->amount_advance)) {
			 $this->amount_advance = trim($this->amount_advance);
		}
		if (isset($this->amount_transf_input)) {
			 $this->amount_transf_input = trim($this->amount_transf_input);
		}
		if (isset($this->amount_transf_output)) {
			 $this->amount_transf_output = trim($this->amount_transf_output);
		}
		if (isset($this->amount_spending)) {
			 $this->amount_spending = trim($this->amount_spending);
		}
		if (isset($this->amount_expense)) {
			 $this->amount_expense = trim($this->amount_expense);
		}
		if (isset($this->amount_close)) {
			 $this->amount_close = trim($this->amount_close);
		}
		if (isset($this->missing_money)) {
			 $this->missing_money = trim($this->missing_money);
		}
		if (isset($this->leftover_money)) {
			 $this->leftover_money = trim($this->leftover_money);
		}
		if (isset($this->amount_exchange)) {
			 $this->amount_exchange = trim($this->amount_exchange);
		}
		if (isset($this->invoice_annulled)) {
			 $this->invoice_annulled = trim($this->invoice_annulled);
		}
		if (isset($this->text_exchange)) {
			 $this->text_exchange = trim($this->text_exchange);
		}
		if (isset($this->text_close)) {
			 $this->text_close = trim($this->text_close);
		}
		if (isset($this->detail)) {
			 $this->detail = trim($this->detail);
		}
		if (isset($this->var_detail)) {
			 $this->var_detail = trim($this->var_detail);
		}
		if (isset($this->typecash)) {
			 $this->typecash = trim($this->typecash);
		}
		if (isset($this->model_pdf)) {
			 $this->model_pdf = trim($this->model_pdf);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_close)) {
			 $this->fk_user_close = trim($this->fk_user_close);
		}
		if (isset($this->fk_user_mod)) {
			 $this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->statut)) {
			 $this->statut = trim($this->statut);
		}

		

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';
		
		$sql .= ' fk_bank = '.(isset($this->fk_bank)?$this->fk_bank:"null").',';
		$sql .= ' fk_user = '.(isset($this->fk_user)?$this->fk_user:"null").',';
		$sql .= ' fk_subsidiary = '.(isset($this->fk_subsidiary)?$this->fk_subsidiary:"null").',';
		$sql .= ' fk_bank_historial = '.(isset($this->fk_bank_historial)?$this->fk_bank_historial:"null").',';
		$sql .= ' date_register = '.(! isset($this->date_register) || dol_strlen($this->date_register) != 0 ? "'".$this->db->idate($this->date_register)."'" : 'null').',';
		$sql .= ' date_close = '.(! isset($this->date_close) || dol_strlen($this->date_close) != 0 ? "'".$this->db->idate($this->date_close)."'" : 'null').',';
		$sql .= ' exchange = '.(isset($this->exchange)?$this->exchange:"null").',';
		$sql .= ' previus_balance = '.(isset($this->previus_balance)?$this->previus_balance:"null").',';
		$sql .= ' amount = '.(isset($this->amount)?$this->amount:"null").',';
		$sql .= ' text_amount = '.(isset($this->text_amount)?"'".$this->db->escape($this->text_amount)."'":"null").',';
		$sql .= ' amount_open = '.(isset($this->amount_open)?$this->amount_open:"null").',';
		$sql .= ' text_amount_open = '.(isset($this->text_amount_open)?"'".$this->db->escape($this->text_amount_open)."'":"null").',';
		$sql .= ' amount_balance = '.(isset($this->amount_balance)?$this->amount_balance:"null").',';
		$sql .= ' amount_income = '.(isset($this->amount_income)?$this->amount_income:"null").',';
		$sql .= ' amount_input = '.(isset($this->amount_input)?$this->amount_input:"null").',';
		$sql .= ' amount_sale = '.(isset($this->amount_sale)?$this->amount_sale:"null").',';
		$sql .= ' amount_null = '.(isset($this->amount_null)?$this->amount_null:"null").',';
		$sql .= ' amount_advance = '.(isset($this->amount_advance)?$this->amount_advance:"null").',';
		$sql .= ' amount_transf_input = '.(isset($this->amount_transf_input)?$this->amount_transf_input:"null").',';
		$sql .= ' amount_transf_output = '.(isset($this->amount_transf_output)?$this->amount_transf_output:"null").',';
		$sql .= ' amount_spending = '.(isset($this->amount_spending)?$this->amount_spending:"null").',';
		$sql .= ' amount_expense = '.(isset($this->amount_expense)?$this->amount_expense:"null").',';
		$sql .= ' amount_close = '.(isset($this->amount_close)?$this->amount_close:"null").',';
		$sql .= ' missing_money = '.(isset($this->missing_money)?$this->missing_money:"null").',';
		$sql .= ' leftover_money = '.(isset($this->leftover_money)?$this->leftover_money:"null").',';
		$sql .= ' amount_exchange = '.(isset($this->amount_exchange)?$this->amount_exchange:"null").',';
		$sql .= ' invoice_annulled = '.(isset($this->invoice_annulled)?$this->invoice_annulled:"null").',';
		$sql .= ' text_exchange = '.(isset($this->text_exchange)?"'".$this->db->escape($this->text_exchange)."'":"null").',';
		$sql .= ' text_close = '.(isset($this->text_close)?"'".$this->db->escape($this->text_close)."'":"null").',';
		$sql .= ' detail = '.(isset($this->detail)?"'".$this->db->escape($this->detail)."'":"null").',';
		$sql .= ' var_detail = '.(isset($this->var_detail)?"'".$this->db->escape($this->var_detail)."'":"null").',';
		$sql .= ' typecash = '.(isset($this->typecash)?$this->typecash:"null").',';
		$sql .= ' model_pdf = '.(isset($this->model_pdf)?"'".$this->db->escape($this->model_pdf)."'":"null").',';
		$sql .= ' fk_user_create = '.(isset($this->fk_user_create)?$this->fk_user_create:"null").',';
		$sql .= ' fk_user_close = '.(isset($this->fk_user_close)?$this->fk_user_close:"null").',';
		$sql .= ' fk_user_mod = '.$user->id.',';
		$sql .= ' date_create = '.(! isset($this->date_create) || dol_strlen($this->date_create) != 0 ? "'".$this->db->idate($this->date_create)."'" : 'null').',';
		$sql .= ' date_mod = '.(! isset($this->date_mod) || dol_strlen($this->date_mod) != 0 ? "'".$this->db->idate($this->date_mod)."'" : 'null').',';
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
		$sql .= ' statut = '.(isset($this->statut)?$this->statut:"null");

        
		$sql .= ' WHERE rowid=' . $this->id;

		$this->db->begin();

		$resql = $this->db->query($sql);
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
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
	 * Delete object in database
	 *
	 * @param User $user      User that deletes
	 * @param bool $notrigger false=launch triggers after, true=disable triggers
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function delete(User $user, $notrigger = false)
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

		if (!$error) {
			$sql = 'DELETE FROM ' . MAIN_DB_PREFIX . $this->table_element;
			$sql .= ' WHERE rowid=' . $this->id;

			$resql = $this->db->query($sql);
			if (!$resql) {
				$error ++;
				$this->errors[] = 'Error ' . $this->db->lasterror();
				dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
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

	/**
	 * Load an object from its id and create a new one in database
	 *
	 * @param int $fromid Id of object to clone
	 *
	 * @return int New id of clone
	 */
	public function createFromClone($fromid)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		global $user;
		$error = 0;
		$object = new Bankstatus($this->db);

		$this->db->begin();

		// Load source object
		$object->fetch($fromid);
		// Reset object
		$object->id = 0;

		// Clear fields
		// ...

		// Create clone
		$result = $object->create($user);

		// Other options
		if ($result < 0) {
			$error ++;
			$this->errors = $object->errors;
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
		}

		// End
		if (!$error) {
			$this->db->commit();

			return $object->id;
		} else {
			$this->db->rollback();

			return - 1;
		}
	}

	/**
	 *  Return a link to the user card (with optionaly the picto)
	 * 	Use this->id,this->lastname, this->firstname
	 *
	 *	@param	int		$withpicto			Include picto in link (0=No picto, 1=Include picto into link, 2=Only picto)
	 *	@param	string	$option				On what the link point to
     *  @param	integer	$notooltip			1=Disable tooltip
     *  @param	int		$maxlen				Max length of visible user name
     *  @param  string  $morecss            Add more css on link
	 *	@return	string						String with URL
	 */
	function getNomUrl($withpicto=0, $option='', $notooltip=0, $maxlen=24, $morecss='')
	{
		global $langs, $conf, $db;
        global $dolibarr_main_authentication, $dolibarr_main_demo;
        global $menumanager;


        $result = '';
        $companylink = '';

        $label = '<u>' . $langs->trans("MyModule") . '</u>';
        $label.= '<div width="100%">';
        $label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

        $link = '<a href="'.DOL_URL_ROOT.'/sales/card.php?id='.$this->id.'"';
        $link.= ($notooltip?'':' title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip'.($morecss?' '.$morecss:'').'"');
        $link.= '>';
		$linkend='</a>';

        if ($withpicto)
        {
            $result.=($link.img_object(($notooltip?'':$label), 'label', ($notooltip?'':'class="classfortooltip"')).$linkend);
            if ($withpicto != 2) $result.=' ';
		}
		$result.= $link . $this->ref . $linkend;
		return $result;
	}
	
	/**
	 *  Retourne le libelle du status d'un user (actif, inactif)
	 *
	 *  @param	int		$mode          0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return	string 			       Label of status
	 */
	function getLibStatut($mode=0)
	{
		return $this->LibStatut($this->status,$mode);
	}

	/**
	 *  Renvoi le libelle d'un status donne
	 *
	 *  @param	int		$status        	Id status
	 *  @param  int		$mode          	0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return string 			       	Label of status
	 */
	function LibStatut($status,$mode=0)
	{
		global $langs;

		if ($mode == 0)
		{
			$prefix='';
			if ($status == 1) return $langs->trans('Enabled');
			if ($status == 0) return $langs->trans('Disabled');
		}
		if ($mode == 1)
		{
			if ($status == 1) return $langs->trans('Enabled');
			if ($status == 0) return $langs->trans('Disabled');
		}
		if ($mode == 2)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5').' '.$langs->trans('Disabled');
		}
		if ($mode == 3)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5');
		}
		if ($mode == 4)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5').' '.$langs->trans('Disabled');
		}
		if ($mode == 5)
		{
			if ($status == 1) return $langs->trans('Enabled').' '.img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return $langs->trans('Disabled').' '.img_picto($langs->trans('Disabled'),'statut5');
		}
	}
	
	
	/**
	 * Initialise object with example values
	 * Id must be 0 if object instance is a specimen
	 *
	 * @return void
	 */
	public function initAsSpecimen()
	{
		$this->id = 0;
		
		$this->fk_bank = '';
		$this->fk_user = '';
		$this->fk_subsidiary = '';
		$this->fk_bank_historial = '';
		$this->date_register = '';
		$this->date_close = '';
		$this->exchange = '';
		$this->previus_balance = '';
		$this->amount = '';
		$this->text_amount = '';
		$this->amount_open = '';
		$this->text_amount_open = '';
		$this->amount_balance = '';
		$this->amount_income = '';
		$this->amount_input = '';
		$this->amount_sale = '';
		$this->amount_null = '';
		$this->amount_advance = '';
		$this->amount_transf_input = '';
		$this->amount_transf_output = '';
		$this->amount_spending = '';
		$this->amount_expense = '';
		$this->amount_close = '';
		$this->missing_money = '';
		$this->leftover_money = '';
		$this->amount_exchange = '';
		$this->invoice_annulled = '';
		$this->text_exchange = '';
		$this->text_close = '';
		$this->detail = '';
		$this->var_detail = '';
		$this->typecash = '';
		$this->model_pdf = '';
		$this->fk_user_create = '';
		$this->fk_user_close = '';
		$this->fk_user_mod = '';
		$this->date_create = '';
		$this->date_mod = '';
		$this->tms = '';
		$this->statut = '';

		
	}

}

/**
 * Class BankstatusLine
 */
class BankstatusLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	
	public $fk_bank;
	public $fk_user;
	public $fk_subsidiary;
	public $fk_bank_historial;
	public $date_register = '';
	public $date_close = '';
	public $exchange;
	public $previus_balance;
	public $amount;
	public $text_amount;
	public $amount_open;
	public $text_amount_open;
	public $amount_balance;
	public $amount_income;
	public $amount_input;
	public $amount_sale;
	public $amount_null;
	public $amount_advance;
	public $amount_transf_input;
	public $amount_transf_output;
	public $amount_spending;
	public $amount_expense;
	public $amount_close;
	public $missing_money;
	public $leftover_money;
	public $amount_exchange;
	public $invoice_annulled;
	public $text_exchange;
	public $text_close;
	public $detail;
	public $var_detail;
	public $typecash;
	public $model_pdf;
	public $fk_user_create;
	public $fk_user_close;
	public $fk_user_mod;
	public $date_create = '';
	public $date_mod = '';
	public $tms = '';
	public $statut;

	/**
	 * @var mixed Sample line property 2
	 */
	
}
