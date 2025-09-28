<?php
/* Copyright (C) 2007-2012  Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) 2014-2016  Juanjo Menent       <jmenent@2byte.es>
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
 * \file    almacen/stockmouvementpricemod.class.php
 * \ingroup almacen
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Stockmouvementpricemod
 *
 * Put here description of your class
 *
 * @see CommonObject
 */
class Stockmouvementpricemod extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'stockmouvementpricemod';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'stock_mouvement_pricemod';

	/**
	 * @var StockmouvementpricemodLine[] Lines
	 */
	public $lines = array();

	/**
	 */

	public $fk_stock_mouvement;
	public $period_year;
	public $month_year;
	public $fk_user_create;
	public $fk_user_mod;
	public $fk_parent_line;
	public $qty;
	public $price;
	public $balance_peps;
	public $balance_ueps;
	public $value_peps;
	public $value_ueps;
	public $balance_peps_new;
	public $balance_ueps_new;
	public $value_peps_new;
	public $value_ueps_new;
	public $datec = '';
	public $datem = '';
	public $tms = '';
	public $status;

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

		if (isset($this->fk_stock_mouvement)) {
			 $this->fk_stock_mouvement = trim($this->fk_stock_mouvement);
		}
		if (isset($this->period_year)) {
			 $this->period_year = trim($this->period_year);
		}
		if (isset($this->month_year)) {
			 $this->month_year = trim($this->month_year);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_mod)) {
			 $this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->fk_parent_line)) {
			 $this->fk_parent_line = trim($this->fk_parent_line);
		}
		if (isset($this->qty)) {
			 $this->qty = trim($this->qty);
		}
		if (isset($this->price)) {
			 $this->price = trim($this->price);
		}
		if (isset($this->balance_peps)) {
			 $this->balance_peps = trim($this->balance_peps);
		}
		if (isset($this->balance_ueps)) {
			 $this->balance_ueps = trim($this->balance_ueps);
		}
		if (isset($this->value_peps)) {
			 $this->value_peps = trim($this->value_peps);
		}
		if (isset($this->value_ueps)) {
			 $this->value_ueps = trim($this->value_ueps);
		}
		if (isset($this->balance_peps_new)) {
			 $this->balance_peps_new = trim($this->balance_peps_new);
		}
		if (isset($this->balance_ueps_new)) {
			 $this->balance_ueps_new = trim($this->balance_ueps_new);
		}
		if (isset($this->value_peps_new)) {
			 $this->value_peps_new = trim($this->value_peps_new);
		}
		if (isset($this->value_ueps_new)) {
			 $this->value_ueps_new = trim($this->value_ueps_new);
		}
		if (isset($this->status)) {
			 $this->status = trim($this->status);
		}



		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';

		$sql.= 'fk_stock_mouvement,';
		$sql.= 'period_year,';
		$sql.= 'month_year,';
		$sql.= 'fk_user_create,';
		$sql.= 'fk_user_mod,';
		$sql.= 'fk_parent_line,';
		$sql.= 'qty,';
		$sql.= 'price,';
		$sql.= 'balance_peps,';
		$sql.= 'balance_ueps,';
		$sql.= 'value_peps,';
		$sql.= 'value_ueps,';
		$sql.= 'balance_peps_new,';
		$sql.= 'balance_ueps_new,';
		$sql.= 'value_peps_new,';
		$sql.= 'value_ueps_new,';
		$sql.= 'datec,';
		$sql.= 'datem,';
		$sql.= 'status';


		$sql .= ') VALUES (';

		$sql .= ' '.(! isset($this->fk_stock_mouvement)?'NULL':$this->fk_stock_mouvement).',';
		$sql .= ' '.(! isset($this->period_year)?'NULL':$this->period_year).',';
		$sql .= ' '.(! isset($this->month_year)?'NULL':$this->month_year).',';
		$sql .= ' '.(! isset($this->fk_user_create)?'NULL':$this->fk_user_create).',';
		$sql .= ' '.$user->id.',';
		$sql .= ' '.(! isset($this->fk_parent_line)?'NULL':$this->fk_parent_line).',';
		$sql .= ' '.(! isset($this->qty)?'NULL':"'".$this->qty."'").',';
		$sql .= ' '.(! isset($this->price)?'NULL':"'".$this->price."'").',';
		$sql .= ' '.(! isset($this->balance_peps)?'NULL':"'".$this->balance_peps."'").',';
		$sql .= ' '.(! isset($this->balance_ueps)?'NULL':"'".$this->balance_ueps."'").',';
		$sql .= ' '.(! isset($this->value_peps)?'NULL':"'".$this->value_peps."'").',';
		$sql .= ' '.(! isset($this->value_ueps)?'NULL':"'".$this->value_ueps."'").',';
		$sql .= ' '.(! isset($this->balance_peps_new)?'NULL':"'".$this->balance_peps_new."'").',';
		$sql .= ' '.(! isset($this->balance_ueps_new)?'NULL':"'".$this->balance_ueps_new."'").',';
		$sql .= ' '.(! isset($this->value_peps_new)?'NULL':"'".$this->value_peps_new."'").',';
		$sql .= ' '.(! isset($this->value_ueps_new)?'NULL':"'".$this->value_ueps_new."'").',';
		$sql .= ' '."'".$this->db->idate(dol_now())."'".',';
		$sql .= ' '.(! isset($this->datem) || dol_strlen($this->datem)==0?'NULL':"'".$this->db->idate($this->datem)."'").',';
		$sql .= ' '.(! isset($this->status)?'NULL':$this->status);


		$sql .= ')';

		$this->db->begin();

		$resql = $this->db->query($sql);
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . implode(',', $this->errors), LOG_ERR);
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

		$sql .= " t.fk_stock_mouvement,";
		$sql .= " t.period_year,";
		$sql .= " t.month_year,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.fk_parent_line,";
		$sql .= " t.qty,";
		$sql .= " t.price,";
		$sql .= " t.balance_peps,";
		$sql .= " t.balance_ueps,";
		$sql .= " t.value_peps,";
		$sql .= " t.value_ueps,";
		$sql .= " t.balance_peps_new,";
		$sql .= " t.balance_ueps_new,";
		$sql .= " t.value_peps_new,";
		$sql .= " t.value_ueps_new,";
		$sql .= " t.datec,";
		$sql .= " t.datem,";
		$sql .= " t.tms,";
		$sql .= " t.status";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql.= ' WHERE 1 = 1';
		if (null !== $ref) {
			$sql .= ' AND t.ref = ' . '\'' . $ref . '\'';
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
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_mod = $obj->fk_user_mod;
				$this->fk_parent_line = $obj->fk_parent_line;
				$this->qty = $obj->qty;
				$this->price = $obj->price;
				$this->balance_peps = $obj->balance_peps;
				$this->balance_ueps = $obj->balance_ueps;
				$this->value_peps = $obj->value_peps;
				$this->value_ueps = $obj->value_ueps;
				$this->balance_peps_new = $obj->balance_peps_new;
				$this->balance_ueps_new = $obj->balance_ueps_new;
				$this->value_peps_new = $obj->value_peps_new;
				$this->value_ueps_new = $obj->value_ueps_new;
				$this->datec = $this->db->jdate($obj->datec);
				$this->datem = $this->db->jdate($obj->datem);
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
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND', $filterstatic='', $lView=false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.fk_stock_mouvement,";
		$sql .= " t.period_year,";
		$sql .= " t.month_year,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.fk_parent_line,";
		$sql .= " t.qty,";
		$sql .= " t.price,";
		$sql .= " t.balance_peps,";
		$sql .= " t.balance_ueps,";
		$sql .= " t.value_peps,";
		$sql .= " t.value_ueps,";
		$sql .= " t.balance_peps_new,";
		$sql .= " t.balance_ueps_new,";
		$sql .= " t.value_peps_new,";
		$sql .= " t.value_ueps_new,";
		$sql .= " t.datec,";
		$sql .= " t.datem,";
		$sql .= " t.tms,";
		$sql .= " t.status";
		$sql.= " , s.fk_product ";
		$sql.= " , p.label AS labelproduct, p.ref AS refproduct, p.ref_ext AS ref_extproduct, p.fk_unit ";

		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element. ' as t';
		$sql .= ' INNER JOIN ' . MAIN_DB_PREFIX . 'stock_mouvement AS s ON t.fk_stock_mouvement = s.rowid';
		$sql .= ' INNER JOIN ' . MAIN_DB_PREFIX . 'product'. ' AS p ON s.fk_product = p.rowid';

		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				$sqlwhere [] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
			}
		}
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
		    $sql .= " AND entity IN (" . getEntity("stockmouvementpricemod", 1) . ")";
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' AND ' . implode(' '.$filtermode.' ', $sqlwhere);
		}
		if ($filterstatic) $sql.= $filterstatic;

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
				$line = new StockmouvementpricemodLine();

				$line->id = $obj->rowid;

				$line->fk_stock_mouvement = $obj->fk_stock_mouvement;
				$line->period_year = $obj->period_year;
				$line->month_year = $obj->month_year;
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->fk_parent_line = $obj->fk_parent_line;
				$line->qty = $obj->qty;
				$line->price = $obj->price;
				$line->balance_peps = $obj->balance_peps;
				$line->balance_ueps = $obj->balance_ueps;
				$line->value_peps = $obj->value_peps;
				$line->value_ueps = $obj->value_ueps;
				$line->balance_peps_new = $obj->balance_peps_new;
				$line->balance_ueps_new = $obj->balance_ueps_new;
				$line->value_peps_new = $obj->value_peps_new;
				$line->value_ueps_new = $obj->value_ueps_new;
				$line->datec = $this->db->jdate($obj->datec);
				$line->datem = $this->db->jdate($obj->datem);
				$line->tms = $this->db->jdate($obj->tms);
				$line->status = $obj->status;

				$line->fk_product = $obj->fk_product;
				$line->labelproduct = $obj->labelproduct;
				$line->refproduct = $obj->refproduct;
				$line->ref_extproduct = $obj->ref_extproduct;
				$line->fk_unit = $obj->fk_unit;

				if ($lView && $num == 1) $this->fetch($obj->rowid);

				$this->lines[$line->id] = $line;
			}
			$this->db->free($resql);

			return $num;
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . implode(',', $this->errors), LOG_ERR);

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

		if (isset($this->fk_stock_mouvement)) {
			 $this->fk_stock_mouvement = trim($this->fk_stock_mouvement);
		}
		if (isset($this->period_year)) {
			 $this->period_year = trim($this->period_year);
		}
		if (isset($this->month_year)) {
			 $this->month_year = trim($this->month_year);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_mod)) {
			 $this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->fk_parent_line)) {
			 $this->fk_parent_line = trim($this->fk_parent_line);
		}
		if (isset($this->qty)) {
			 $this->qty = trim($this->qty);
		}
		if (isset($this->price)) {
			 $this->price = trim($this->price);
		}
		if (isset($this->balance_peps)) {
			 $this->balance_peps = trim($this->balance_peps);
		}
		if (isset($this->balance_ueps)) {
			 $this->balance_ueps = trim($this->balance_ueps);
		}
		if (isset($this->value_peps)) {
			 $this->value_peps = trim($this->value_peps);
		}
		if (isset($this->value_ueps)) {
			 $this->value_ueps = trim($this->value_ueps);
		}
		if (isset($this->balance_peps_new)) {
			 $this->balance_peps_new = trim($this->balance_peps_new);
		}
		if (isset($this->balance_ueps_new)) {
			 $this->balance_ueps_new = trim($this->balance_ueps_new);
		}
		if (isset($this->value_peps_new)) {
			 $this->value_peps_new = trim($this->value_peps_new);
		}
		if (isset($this->value_ueps_new)) {
			 $this->value_ueps_new = trim($this->value_ueps_new);
		}
		if (isset($this->status)) {
			 $this->status = trim($this->status);
		}



		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';

		$sql .= ' fk_stock_mouvement = '.(isset($this->fk_stock_mouvement)?$this->fk_stock_mouvement:"null").',';
		$sql .= ' period_year = '.(isset($this->period_year)?$this->period_year:"null").',';
		$sql .= ' month_year = '.(isset($this->month_year)?$this->month_year:"null").',';
		$sql .= ' fk_user_create = '.(isset($this->fk_user_create)?$this->fk_user_create:"null").',';
		$sql .= ' fk_user_mod = '.$user->id.',';
		$sql .= ' fk_parent_line = '.(isset($this->fk_parent_line)?$this->fk_parent_line:"null").',';
		$sql .= ' qty = '.(isset($this->qty)?$this->qty:"null").',';
		$sql .= ' price = '.(isset($this->price)?$this->price:"null").',';
		$sql .= ' balance_peps = '.(isset($this->balance_peps)?$this->balance_peps:"null").',';
		$sql .= ' balance_ueps = '.(isset($this->balance_ueps)?$this->balance_ueps:"null").',';
		$sql .= ' value_peps = '.(isset($this->value_peps)?$this->value_peps:"null").',';
		$sql .= ' value_ueps = '.(isset($this->value_ueps)?$this->value_ueps:"null").',';
		$sql .= ' balance_peps_new = '.(isset($this->balance_peps_new)?$this->balance_peps_new:"null").',';
		$sql .= ' balance_ueps_new = '.(isset($this->balance_ueps_new)?$this->balance_ueps_new:"null").',';
		$sql .= ' value_peps_new = '.(isset($this->value_peps_new)?$this->value_peps_new:"null").',';
		$sql .= ' value_ueps_new = '.(isset($this->value_ueps_new)?$this->value_ueps_new:"null").',';
		$sql .= ' datec = '.(! isset($this->datec) || dol_strlen($this->datec) != 0 ? "'".$this->db->idate($this->datec)."'" : 'null').',';
		$sql .= ' datem = '.(! isset($this->datem) || dol_strlen($this->datem) != 0 ? "'".$this->db->idate($this->datem)."'" : 'null').',';
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
		$sql .= ' status = '.(isset($this->status)?$this->status:"null");


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

		// If you need to delete child tables to, you can insert them here

		if (!$error) {
			$sql = 'DELETE FROM ' . MAIN_DB_PREFIX . $this->table_element;
			$sql .= ' WHERE rowid=' . $this->id;

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
		$object = new Stockmouvementpricemod($this->db);

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
			dol_syslog(__METHOD__ . ' ' . implode(',', $this->errors), LOG_ERR);
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
	 *  Return a link to the object card (with optionaly the picto)
	 *
	 *	@param	int		$withpicto			Include picto in link (0=No picto, 1=Include picto into link, 2=Only picto)
	 *	@param	string	$option				On what the link point to
     *  @param	int  	$notooltip			1=Disable tooltip
     *  @param	int		$maxlen				Max length of visible user name
     *  @param  string  $morecss            Add more css on link
	 *	@return	string						String with URL
	 */
	function getNomUrl($withpicto=0, $option='', $notooltip=0, $maxlen=24, $morecss='')
	{
		global $db, $conf, $langs;
        global $dolibarr_main_authentication, $dolibarr_main_demo;
        global $menumanager;

        if (! empty($conf->dol_no_mouse_hover)) $notooltip=1;   // Force disable tooltips

        $result = '';
        $companylink = '';

        $label = '<u>' . $langs->trans("MyModule") . '</u>';
        $label.= '<br>';
        $label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

        $url = DOL_URL_ROOT.'/almacen/'.$this->table_name.'_card.php?id='.$this->id;

        $linkclose='';
        if (empty($notooltip))
        {
            if (! empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER))
            {
                $label=$langs->trans("ShowProject");
                $linkclose.=' alt="'.dol_escape_htmltag($label, 1).'"';
            }
            $linkclose.=' title="'.dol_escape_htmltag($label, 1).'"';
            $linkclose.=' class="classfortooltip'.($morecss?' '.$morecss:'').'"';
        }
        else $linkclose = ($morecss?' class="'.$morecss.'"':'');

		$linkstart = '<a href="'.$url.'"';
		$linkstart.=$linkclose.'>';
		$linkend='</a>';

        if ($withpicto)
        {
            $result.=($linkstart.img_object(($notooltip?'':$label), 'label', ($notooltip?'':'class="classfortooltip"')).$linkend);
            if ($withpicto != 2) $result.=' ';
		}
		$result.= $linkstart . $this->ref . $linkend;
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
	 *  Return the status
	 *
	 *  @param	int		$status        	Id status
	 *  @param  int		$mode          	0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 5=Long label + Picto
	 *  @return string 			       	Label of status
	 */
	static function LibStatut($status,$mode=0)
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
		if ($mode == 6)
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

		$this->fk_stock_mouvement = '';
		$this->period_year = '';
		$this->month_year = '';
		$this->fk_user_create = '';
		$this->fk_user_mod = '';
		$this->fk_parent_line = '';
		$this->qty = '';
		$this->price = '';
		$this->balance_peps = '';
		$this->balance_ueps = '';
		$this->value_peps = '';
		$this->value_ueps = '';
		$this->balance_peps_new = '';
		$this->balance_ueps_new = '';
		$this->value_peps_new = '';
		$this->value_ueps_new = '';
		$this->datec = '';
		$this->datem = '';
		$this->tms = '';
		$this->status = '';


	}

}

/**
 * Class StockmouvementpricemodLine
 */
class StockmouvementpricemodLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */

	public $fk_stock_mouvement;
	public $period_year;
	public $month_year;
	public $fk_user_create;
	public $fk_user_mod;
	public $fk_parent_line;
	public $qty;
	public $price;
	public $balance_peps;
	public $balance_ueps;
	public $value_peps;
	public $value_ueps;
	public $balance_peps_new;
	public $balance_ueps_new;
	public $value_peps_new;
	public $value_ueps_new;
	public $datec = '';
	public $datem = '';
	public $tms = '';
	public $status;

	/**
	 * @var mixed Sample line property 2
	 */

}
