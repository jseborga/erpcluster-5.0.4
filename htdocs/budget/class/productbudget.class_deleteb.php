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
 * \file    budget/productbudget.class.php
 * \ingroup budget
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Productbudget
 *
 * Put here description of your class
 *
 * @see CommonObject
 */
class Productbudget extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'productbudget';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'product_budget';

	/**
	 * @var ProductbudgetLine[] Lines
	 */
	public $lines = array();

	/**
	 */

	public $fk_product;
	public $fk_budget;
	public $ref;
	public $label;
	public $fk_unit;
	public $code_structure;
	public $group_structure;
	public $formula;
	public $units;
	public $commander;
	public $performance;
	public $price_productive;
	public $price_improductive;
	public $active;
	public $fk_object;
	public $quant;
	public $percent_prod;
	public $amount_noprod;
	public $amount;
	public $work_hours;
	public $fk_user_create;
	public $fk_user_mod;
	public $date_create = '';
	public $date_mod = '';
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

		if (isset($this->fk_product)) {
			 $this->fk_product = trim($this->fk_product);
		}
		if (isset($this->fk_budget)) {
			 $this->fk_budget = trim($this->fk_budget);
		}
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->label)) {
			 $this->label = trim($this->label);
		}
		if (isset($this->fk_unit)) {
			 $this->fk_unit = trim($this->fk_unit);
		}
		if (isset($this->code_structure)) {
			 $this->code_structure = trim($this->code_structure);
		}
		if (isset($this->group_structure)) {
			 $this->group_structure = trim($this->group_structure);
		}
		if (isset($this->formula)) {
			 $this->formula = trim($this->formula);
		}
		if (isset($this->units)) {
			 $this->units = trim($this->units);
		}
		if (isset($this->commander)) {
			 $this->commander = trim($this->commander);
		}
		if (isset($this->performance)) {
			 $this->performance = trim($this->performance);
		}
		if (isset($this->price_productive)) {
			 $this->price_productive = trim($this->price_productive);
		}
		if (isset($this->price_improductive)) {
			 $this->price_improductive = trim($this->price_improductive);
		}
		if (isset($this->active)) {
			 $this->active = trim($this->active);
		}
		if (isset($this->fk_object)) {
			 $this->fk_object = trim($this->fk_object);
		}
		if (isset($this->quant)) {
			 $this->quant = trim($this->quant);
		}
		if (isset($this->percent_prod)) {
			 $this->percent_prod = trim($this->percent_prod);
		}
		if (isset($this->amount_noprod)) {
			 $this->amount_noprod = trim($this->amount_noprod);
		}
		if (isset($this->amount)) {
			 $this->amount = trim($this->amount);
		}
		if (isset($this->work_hours)) {
			 $this->work_hours = trim($this->work_hours);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_mod)) {
			 $this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->status)) {
			 $this->status = trim($this->status);
		}



		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';

		$sql.= 'fk_product,';
		$sql.= 'fk_budget,';
		$sql.= 'ref,';
		$sql.= 'label,';
		$sql.= 'fk_unit,';
		$sql.= 'code_structure,';
		$sql.= 'group_structure,';
		$sql.= 'formula,';
		$sql.= 'units,';
		$sql.= 'commander,';
		$sql.= 'performance,';
		$sql.= 'price_productive,';
		$sql.= 'price_improductive,';
		$sql.= 'active,';
		$sql.= 'fk_object,';
		$sql.= 'quant,';
		$sql.= 'percent_prod,';
		$sql.= 'amount_noprod,';
		$sql.= 'amount,';
		$sql.= 'work_hours,';
		$sql.= 'fk_user_create,';
		$sql.= 'fk_user_mod,';
		$sql.= 'date_create,';
		$sql.= 'date_mod,';
		$sql.= 'status';


		$sql .= ') VALUES (';

		$sql .= ' '.(! isset($this->fk_product)?'NULL':$this->fk_product).',';
		$sql .= ' '.(! isset($this->fk_budget)?'NULL':$this->fk_budget).',';
		$sql .= ' '.(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql .= ' '.(! isset($this->label)?'NULL':"'".$this->db->escape($this->label)."'").',';
		$sql .= ' '.(! isset($this->fk_unit)?'NULL':$this->fk_unit).',';
		$sql .= ' '.(! isset($this->code_structure)?'NULL':"'".$this->db->escape($this->code_structure)."'").',';
		$sql .= ' '.(! isset($this->group_structure)?'NULL':"'".$this->db->escape($this->group_structure)."'").',';
		$sql .= ' '.(! isset($this->formula)?'NULL':"'".$this->db->escape($this->formula)."'").',';
		$sql .= ' '.(! isset($this->units)?'NULL':$this->units).',';
		$sql .= ' '.(! isset($this->commander)?'NULL':$this->commander).',';
		$sql .= ' '.(! isset($this->performance)?'NULL':"'".$this->performance."'").',';
		$sql .= ' '.(! isset($this->price_productive)?'NULL':"'".$this->price_productive."'").',';
		$sql .= ' '.(! isset($this->price_improductive)?'NULL':"'".$this->price_improductive."'").',';
		$sql .= ' '.(! isset($this->active)?'NULL':$this->active).',';
		$sql .= ' '.(! isset($this->fk_object)?'NULL':$this->fk_object).',';
		$sql .= ' '.(! isset($this->quant)?'NULL':"'".$this->quant."'").',';
		$sql .= ' '.(! isset($this->percent_prod)?'NULL':"'".$this->percent_prod."'").',';
		$sql .= ' '.(! isset($this->amount_noprod)?'NULL':"'".$this->amount_noprod."'").',';
		$sql .= ' '.(! isset($this->amount)?'NULL':"'".$this->amount."'").',';
		$sql .= ' '.(! isset($this->work_hours)?'NULL':"'".$this->work_hours."'").',';
		$sql .= ' '.(! isset($this->fk_user_create)?'NULL':$this->fk_user_create).',';
		$sql .= ' '.$user->id.',';
		$sql .= ' '.(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':"'".$this->db->idate($this->date_create)."'").',';
		$sql .= ' '.(! isset($this->date_mod) || dol_strlen($this->date_mod)==0?'NULL':"'".$this->db->idate($this->date_mod)."'").',';
		$sql .= ' '.(! isset($this->status)?'NULL':$this->status);


		$sql .= ')';
echo '<hr>'.$sql;
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
	public function fetch($id, $ref = null, $fk=0)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.fk_product,";
		$sql .= " t.fk_budget,";
		$sql .= " t.ref,";
		$sql .= " t.label,";
		$sql .= " t.fk_unit,";
		$sql .= " t.code_structure,";
		$sql .= " t.group_structure,";
		$sql .= " t.formula,";
		$sql .= " t.units,";
		$sql .= " t.commander,";
		$sql .= " t.performance,";
		$sql .= " t.price_productive,";
		$sql .= " t.price_improductive,";
		$sql .= " t.active,";
		$sql .= " t.fk_object,";
		$sql .= " t.quant,";
		$sql .= " t.percent_prod,";
		$sql .= " t.amount_noprod,";
		$sql .= " t.amount,";
		$sql .= " t.work_hours,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.date_create,";
		$sql .= " t.date_mod,";
		$sql .= " t.tms,";
		$sql .= " t.status";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
		    $sql .= " AND entity IN (" . getEntity("productbudget", 1) . ")";
		}
		if (null !== $ref && $fk>0) {
			$sql .= ' AND t.ref = ' . '\'' . $ref . '\'';
			$sql .= ' AND t.fk_budget = ' . $fk;
		} else {
			$sql .= ' AND t.rowid = ' . $id;
		}

		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			if ($numrows) {
				$obj = $this->db->fetch_object($resql);

				$this->id = $obj->rowid;

				$this->fk_product = $obj->fk_product;
				$this->fk_budget = $obj->fk_budget;
				$this->ref = $obj->ref;
				$this->label = $obj->label;
				$this->fk_unit = $obj->fk_unit;
				$this->code_structure = $obj->code_structure;
				$this->group_structure = $obj->group_structure;
				$this->formula = $obj->formula;
				$this->units = $obj->units;
				$this->commander = $obj->commander;
				$this->performance = $obj->performance;
				$this->price_productive = $obj->price_productive;
				$this->price_improductive = $obj->price_improductive;
				$this->active = $obj->active;
				$this->fk_object = $obj->fk_object;
				$this->quant = $obj->quant;
				$this->percent_prod = $obj->percent_prod;
				$this->amount_noprod = $obj->amount_noprod;
				$this->amount = $obj->amount;
				$this->work_hours = $obj->work_hours;
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_mod = $obj->fk_user_mod;
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

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.fk_product,";
		$sql .= " t.fk_budget,";
		$sql .= " t.ref,";
		$sql .= " t.label,";
		$sql .= " t.fk_unit,";
		$sql .= " t.code_structure,";
		$sql .= " t.group_structure,";
		$sql .= " t.formula,";
		$sql .= " t.units,";
		$sql .= " t.commander,";
		$sql .= " t.performance,";
		$sql .= " t.price_productive,";
		$sql .= " t.price_improductive,";
		$sql .= " t.active,";
		$sql .= " t.fk_object,";
		$sql .= " t.quant,";
		$sql .= " t.percent_prod,";
		$sql .= " t.amount_noprod,";
		$sql .= " t.amount,";
		$sql .= " t.work_hours,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.date_create,";
		$sql .= " t.date_mod,";
		$sql .= " t.tms,";
		$sql .= " t.status";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element. ' as t';

		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				$sqlwhere [] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
			}
		}
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
		    $sql .= " AND entity IN (" . getEntity("productbudget", 1) . ")";
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' AND ' . implode(' '.$filtermode.' ', $sqlwhere);
		}
		if ($filterstatic){
			$sql.= $filterstatic;
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
				$line = new ProductbudgetLine();

				$line->id = $obj->rowid;

				$line->fk_product = $obj->fk_product;
				$line->fk_budget = $obj->fk_budget;
				$line->ref = $obj->ref;
				$line->label = $obj->label;
				$line->fk_unit = $obj->fk_unit;
				$line->code_structure = $obj->code_structure;
				$line->group_structure = $obj->group_structure;
				$line->formula = $obj->formula;
				$line->units = $obj->units;
				$line->commander = $obj->commander;
				$line->performance = $obj->performance;
				$line->price_productive = $obj->price_productive;
				$line->price_improductive = $obj->price_improductive;
				$line->active = $obj->active;
				$line->fk_object = $obj->fk_object;
				$line->quant = $obj->quant;
				$line->percent_prod = $obj->percent_prod;
				$line->amount_noprod = $obj->amount_noprod;
				$line->amount = $obj->amount;
				$line->work_hours = $obj->work_hours;
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->date_mod = $this->db->jdate($obj->date_mod);
				$line->tms = $this->db->jdate($obj->tms);
				$line->status = $obj->status;



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

		if (isset($this->fk_product)) {
			 $this->fk_product = trim($this->fk_product);
		}
		if (isset($this->fk_budget)) {
			 $this->fk_budget = trim($this->fk_budget);
		}
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->label)) {
			 $this->label = trim($this->label);
		}
		if (isset($this->fk_unit)) {
			 $this->fk_unit = trim($this->fk_unit);
		}
		if (isset($this->code_structure)) {
			 $this->code_structure = trim($this->code_structure);
		}
		if (isset($this->group_structure)) {
			 $this->group_structure = trim($this->group_structure);
		}
		if (isset($this->formula)) {
			 $this->formula = trim($this->formula);
		}
		if (isset($this->units)) {
			 $this->units = trim($this->units);
		}
		if (isset($this->commander)) {
			 $this->commander = trim($this->commander);
		}
		if (isset($this->performance)) {
			 $this->performance = trim($this->performance);
		}
		if (isset($this->price_productive)) {
			 $this->price_productive = trim($this->price_productive);
		}
		if (isset($this->price_improductive)) {
			 $this->price_improductive = trim($this->price_improductive);
		}
		if (isset($this->active)) {
			 $this->active = trim($this->active);
		}
		if (isset($this->fk_object)) {
			 $this->fk_object = trim($this->fk_object);
		}
		if (isset($this->quant)) {
			 $this->quant = trim($this->quant);
		}
		if (isset($this->percent_prod)) {
			 $this->percent_prod = trim($this->percent_prod);
		}
		if (isset($this->amount_noprod)) {
			 $this->amount_noprod = trim($this->amount_noprod);
		}
		if (isset($this->amount)) {
			 $this->amount = trim($this->amount);
		}
		if (isset($this->work_hours)) {
			 $this->work_hours = trim($this->work_hours);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_mod)) {
			 $this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->status)) {
			 $this->status = trim($this->status);
		}



		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';

		$sql .= ' fk_product = '.(isset($this->fk_product)?$this->fk_product:"null").',';
		$sql .= ' fk_budget = '.(isset($this->fk_budget)?$this->fk_budget:"null").',';
		$sql .= ' ref = '.(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").',';
		$sql .= ' label = '.(isset($this->label)?"'".$this->db->escape($this->label)."'":"null").',';
		$sql .= ' fk_unit = '.(isset($this->fk_unit)?$this->fk_unit:"null").',';
		$sql .= ' code_structure = '.(isset($this->code_structure)?"'".$this->db->escape($this->code_structure)."'":"null").',';
		$sql .= ' group_structure = '.(isset($this->group_structure)?"'".$this->db->escape($this->group_structure)."'":"null").',';
		$sql .= ' formula = '.(isset($this->formula)?"'".$this->db->escape($this->formula)."'":"null").',';
		$sql .= ' units = '.(isset($this->units)?$this->units:"null").',';
		$sql .= ' commander = '.(isset($this->commander)?$this->commander:"null").',';
		$sql .= ' performance = '.(isset($this->performance)?$this->performance:"null").',';
		$sql .= ' price_productive = '.(isset($this->price_productive)?$this->price_productive:"null").',';
		$sql .= ' price_improductive = '.(isset($this->price_improductive)?$this->price_improductive:"null").',';
		$sql .= ' active = '.(isset($this->active)?$this->active:"null").',';
		$sql .= ' fk_object = '.(isset($this->fk_object)?$this->fk_object:"null").',';
		$sql .= ' quant = '.(isset($this->quant)?$this->quant:"null").',';
		$sql .= ' percent_prod = '.(isset($this->percent_prod)?$this->percent_prod:"null").',';
		$sql .= ' amount_noprod = '.(isset($this->amount_noprod)?$this->amount_noprod:"null").',';
		$sql .= ' amount = '.(isset($this->amount)?$this->amount:"null").',';
		$sql .= ' work_hours = '.(isset($this->work_hours)?$this->work_hours:"null").',';
		$sql .= ' fk_user_create = '.(isset($this->fk_user_create)?$this->fk_user_create:"null").',';
		$sql .= ' fk_user_mod = '.$user->id.',';
		$sql .= ' date_create = '.(! isset($this->date_create) || dol_strlen($this->date_create) != 0 ? "'".$this->db->idate($this->date_create)."'" : 'null').',';
		$sql .= ' date_mod = '.(! isset($this->date_mod) || dol_strlen($this->date_mod) != 0 ? "'".$this->db->idate($this->date_mod)."'" : 'null').',';
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
		$object = new Productbudget($this->db);

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

        $label = '<u>' . $langs->trans("Productbudget") . '</u>';
        $label.= '<br>';
        $label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

        $url = DOL_URL_ROOT.'/budget/productbudget/'.'card.php?id='.$this->id;

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

		$this->fk_product = '';
		$this->fk_budget = '';
		$this->ref = '';
		$this->label = '';
		$this->fk_unit = '';
		$this->code_structure = '';
		$this->group_structure = '';
		$this->formula = '';
		$this->units = '';
		$this->commander = '';
		$this->performance = '';
		$this->price_productive = '';
		$this->price_improductive = '';
		$this->active = '';
		$this->fk_object = '';
		$this->quant = '';
		$this->percent_prod = '';
		$this->amount_noprod = '';
		$this->amount = '';
		$this->work_hours = '';
		$this->fk_user_create = '';
		$this->fk_user_mod = '';
		$this->date_create = '';
		$this->date_mod = '';
		$this->tms = '';
		$this->status = '';


	}

}

/**
 * Class ProductbudgetLine
 */
class ProductbudgetLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */

	public $fk_product;
	public $fk_budget;
	public $ref;
	public $label;
	public $fk_unit;
	public $code_structure;
	public $group_structure;
	public $formula;
	public $units;
	public $commander;
	public $performance;
	public $price_productive;
	public $price_improductive;
	public $active;
	public $fk_object;
	public $quant;
	public $percent_prod;
	public $amount_noprod;
	public $amount;
	public $work_hours;
	public $fk_user_create;
	public $fk_user_mod;
	public $date_create = '';
	public $date_mod = '';
	public $tms = '';
	public $status;

	/**
	 * @var mixed Sample line property 2
	 */

}
