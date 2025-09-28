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
 * \file    budget/budgetincidentsdet.class.php
 * \ingroup budget
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Budgetincidentsdet
 *
 * Put here description of your class
 *
 * @see CommonObject
 */
class Budgetincidentsdet extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'budgetincidentsdet';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'budget_incidents_det';

	/**
	 * @var BudgetincidentsdetLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	
	public $fk_budget_incident;
	public $type;
	public $fk_object;
	public $object;
	public $label;
	public $fk_unit;
	public $sequen;
	public $value_one;
	public $value_two;
	public $value_three;
	public $value_four;
	public $value_five;
	public $value_six;
	public $value_seven;
	public $res_one;
	public $res_two;
	public $res_three;
	public $res_four;
	public $res_five;
	public $quantity;
	public $fk_user_create;
	public $fk_user_mod;
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
		
		if (isset($this->fk_budget_incident)) {
			 $this->fk_budget_incident = trim($this->fk_budget_incident);
		}
		if (isset($this->type)) {
			 $this->type = trim($this->type);
		}
		if (isset($this->fk_object)) {
			 $this->fk_object = trim($this->fk_object);
		}
		if (isset($this->object)) {
			 $this->object = trim($this->object);
		}
		if (isset($this->label)) {
			 $this->label = trim($this->label);
		}
		if (isset($this->fk_unit)) {
			 $this->fk_unit = trim($this->fk_unit);
		}
		if (isset($this->sequen)) {
			 $this->sequen = trim($this->sequen);
		}
		if (isset($this->value_one)) {
			 $this->value_one = trim($this->value_one);
		}
		if (isset($this->value_two)) {
			 $this->value_two = trim($this->value_two);
		}
		if (isset($this->value_three)) {
			 $this->value_three = trim($this->value_three);
		}
		if (isset($this->value_four)) {
			 $this->value_four = trim($this->value_four);
		}
		if (isset($this->value_five)) {
			 $this->value_five = trim($this->value_five);
		}
		if (isset($this->value_six)) {
			 $this->value_six = trim($this->value_six);
		}
		if (isset($this->value_seven)) {
			 $this->value_seven = trim($this->value_seven);
		}
		if (isset($this->res_one)) {
			 $this->res_one = trim($this->res_one);
		}
		if (isset($this->res_two)) {
			 $this->res_two = trim($this->res_two);
		}
		if (isset($this->res_three)) {
			 $this->res_three = trim($this->res_three);
		}
		if (isset($this->res_four)) {
			 $this->res_four = trim($this->res_four);
		}
		if (isset($this->res_five)) {
			 $this->res_five = trim($this->res_five);
		}
		if (isset($this->quantity)) {
			 $this->quantity = trim($this->quantity);
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
		
		$sql.= 'fk_budget_incident,';
		$sql.= 'type,';
		$sql.= 'fk_object,';
		$sql.= 'object,';
		$sql.= 'label,';
		$sql.= 'fk_unit,';
		$sql.= 'sequen,';
		$sql.= 'value_one,';
		$sql.= 'value_two,';
		$sql.= 'value_three,';
		$sql.= 'value_four,';
		$sql.= 'value_five,';
		$sql.= 'value_six,';
		$sql.= 'value_seven,';
		$sql.= 'res_one,';
		$sql.= 'res_two,';
		$sql.= 'res_three,';
		$sql.= 'res_four,';
		$sql.= 'res_five,';
		$sql.= 'quantity,';
		$sql.= 'fk_user_create,';
		$sql.= 'fk_user_mod,';
		$sql.= 'datec,';
		$sql.= 'datem,';
		$sql.= 'status';

		
		$sql .= ') VALUES (';
		
		$sql .= ' '.(! isset($this->fk_budget_incident)?'NULL':$this->fk_budget_incident).',';
		$sql .= ' '.(! isset($this->type)?'NULL':"'".$this->db->escape($this->type)."'").',';
		$sql .= ' '.(! isset($this->fk_object)?'NULL':$this->fk_object).',';
		$sql .= ' '.(! isset($this->object)?'NULL':"'".$this->db->escape($this->object)."'").',';
		$sql .= ' '.(! isset($this->label)?'NULL':"'".$this->db->escape($this->label)."'").',';
		$sql .= ' '.(! isset($this->fk_unit)?'NULL':$this->fk_unit).',';
		$sql .= ' '.(! isset($this->sequen)?'NULL':$this->sequen).',';
		$sql .= ' '.(! isset($this->value_one)?'NULL':"'".$this->value_one."'").',';
		$sql .= ' '.(! isset($this->value_two)?'NULL':"'".$this->value_two."'").',';
		$sql .= ' '.(! isset($this->value_three)?'NULL':"'".$this->value_three."'").',';
		$sql .= ' '.(! isset($this->value_four)?'NULL':"'".$this->value_four."'").',';
		$sql .= ' '.(! isset($this->value_five)?'NULL':"'".$this->value_five."'").',';
		$sql .= ' '.(! isset($this->value_six)?'NULL':"'".$this->value_six."'").',';
		$sql .= ' '.(! isset($this->value_seven)?'NULL':"'".$this->value_seven."'").',';
		$sql .= ' '.(! isset($this->res_one)?'NULL':"'".$this->res_one."'").',';
		$sql .= ' '.(! isset($this->res_two)?'NULL':"'".$this->res_two."'").',';
		$sql .= ' '.(! isset($this->res_three)?'NULL':"'".$this->res_three."'").',';
		$sql .= ' '.(! isset($this->res_four)?'NULL':"'".$this->res_four."'").',';
		$sql .= ' '.(! isset($this->res_five)?'NULL':"'".$this->res_five."'").',';
		$sql .= ' '.(! isset($this->quantity)?'NULL':$this->quantity).',';
		$sql .= ' '.(! isset($this->fk_user_create)?'NULL':$this->fk_user_create).',';
		$sql .= ' '.$user->id.',';
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
		
		$sql .= " t.fk_budget_incident,";
		$sql .= " t.type,";
		$sql .= " t.fk_object,";
		$sql .= " t.object,";
		$sql .= " t.label,";
		$sql .= " t.fk_unit,";
		$sql .= " t.sequen,";
		$sql .= " t.value_one,";
		$sql .= " t.value_two,";
		$sql .= " t.value_three,";
		$sql .= " t.value_four,";
		$sql .= " t.value_five,";
		$sql .= " t.value_six,";
		$sql .= " t.value_seven,";
		$sql .= " t.res_one,";
		$sql .= " t.res_two,";
		$sql .= " t.res_three,";
		$sql .= " t.res_four,";
		$sql .= " t.res_five,";
		$sql .= " t.quantity,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.datec,";
		$sql .= " t.datem,";
		$sql .= " t.tms,";
		$sql .= " t.status";

		
		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
		    $sql .= " AND entity IN (" . getEntity("budgetincidentsdet", 1) . ")";
		}
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
				
				$this->fk_budget_incident = $obj->fk_budget_incident;
				$this->type = $obj->type;
				$this->fk_object = $obj->fk_object;
				$this->object = $obj->object;
				$this->label = $obj->label;
				$this->fk_unit = $obj->fk_unit;
				$this->sequen = $obj->sequen;
				$this->value_one = $obj->value_one;
				$this->value_two = $obj->value_two;
				$this->value_three = $obj->value_three;
				$this->value_four = $obj->value_four;
				$this->value_five = $obj->value_five;
				$this->value_six = $obj->value_six;
				$this->value_seven = $obj->value_seven;
				$this->res_one = $obj->res_one;
				$this->res_two = $obj->res_two;
				$this->res_three = $obj->res_three;
				$this->res_four = $obj->res_four;
				$this->res_five = $obj->res_five;
				$this->quantity = $obj->quantity;
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_mod = $obj->fk_user_mod;
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
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lView=false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		
		$sql .= " t.fk_budget_incident,";
		$sql .= " t.type,";
		$sql .= " t.fk_object,";
		$sql .= " t.object,";
		$sql .= " t.label,";
		$sql .= " t.fk_unit,";
		$sql .= " t.sequen,";
		$sql .= " t.value_one,";
		$sql .= " t.value_two,";
		$sql .= " t.value_three,";
		$sql .= " t.value_four,";
		$sql .= " t.value_five,";
		$sql .= " t.value_six,";
		$sql .= " t.value_seven,";
		$sql .= " t.res_one,";
		$sql .= " t.res_two,";
		$sql .= " t.res_three,";
		$sql .= " t.res_four,";
		$sql .= " t.res_five,";
		$sql .= " t.quantity,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.datec,";
		$sql .= " t.datem,";
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
		    $sql .= " AND entity IN (" . getEntity("budgetincidentsdet", 1) . ")";
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
				$line = new BudgetincidentsdetLine();

				$line->id = $obj->rowid;
				
				$line->fk_budget_incident = $obj->fk_budget_incident;
				$line->type = $obj->type;
				$line->fk_object = $obj->fk_object;
				$line->object = $obj->object;
				$line->label = $obj->label;
				$line->fk_unit = $obj->fk_unit;
				$line->sequen = $obj->sequen;
				$line->value_one = $obj->value_one;
				$line->value_two = $obj->value_two;
				$line->value_three = $obj->value_three;
				$line->value_four = $obj->value_four;
				$line->value_five = $obj->value_five;
				$line->value_six = $obj->value_six;
				$line->value_seven = $obj->value_seven;
				$line->res_one = $obj->res_one;
				$line->res_two = $obj->res_two;
				$line->res_three = $obj->res_three;
				$line->res_four = $obj->res_four;
				$line->res_five = $obj->res_five;
				$line->quantity = $obj->quantity;
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->datec = $this->db->jdate($obj->datec);
				$line->datem = $this->db->jdate($obj->datem);
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
		
		if (isset($this->fk_budget_incident)) {
			 $this->fk_budget_incident = trim($this->fk_budget_incident);
		}
		if (isset($this->type)) {
			 $this->type = trim($this->type);
		}
		if (isset($this->fk_object)) {
			 $this->fk_object = trim($this->fk_object);
		}
		if (isset($this->object)) {
			 $this->object = trim($this->object);
		}
		if (isset($this->label)) {
			 $this->label = trim($this->label);
		}
		if (isset($this->fk_unit)) {
			 $this->fk_unit = trim($this->fk_unit);
		}
		if (isset($this->sequen)) {
			 $this->sequen = trim($this->sequen);
		}
		if (isset($this->value_one)) {
			 $this->value_one = trim($this->value_one);
		}
		if (isset($this->value_two)) {
			 $this->value_two = trim($this->value_two);
		}
		if (isset($this->value_three)) {
			 $this->value_three = trim($this->value_three);
		}
		if (isset($this->value_four)) {
			 $this->value_four = trim($this->value_four);
		}
		if (isset($this->value_five)) {
			 $this->value_five = trim($this->value_five);
		}
		if (isset($this->value_six)) {
			 $this->value_six = trim($this->value_six);
		}
		if (isset($this->value_seven)) {
			 $this->value_seven = trim($this->value_seven);
		}
		if (isset($this->res_one)) {
			 $this->res_one = trim($this->res_one);
		}
		if (isset($this->res_two)) {
			 $this->res_two = trim($this->res_two);
		}
		if (isset($this->res_three)) {
			 $this->res_three = trim($this->res_three);
		}
		if (isset($this->res_four)) {
			 $this->res_four = trim($this->res_four);
		}
		if (isset($this->res_five)) {
			 $this->res_five = trim($this->res_five);
		}
		if (isset($this->quantity)) {
			 $this->quantity = trim($this->quantity);
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
		
		$sql .= ' fk_budget_incident = '.(isset($this->fk_budget_incident)?$this->fk_budget_incident:"null").',';
		$sql .= ' type = '.(isset($this->type)?"'".$this->db->escape($this->type)."'":"null").',';
		$sql .= ' fk_object = '.(isset($this->fk_object)?$this->fk_object:"null").',';
		$sql .= ' object = '.(isset($this->object)?"'".$this->db->escape($this->object)."'":"null").',';
		$sql .= ' label = '.(isset($this->label)?"'".$this->db->escape($this->label)."'":"null").',';
		$sql .= ' fk_unit = '.(isset($this->fk_unit)?$this->fk_unit:"null").',';
		$sql .= ' sequen = '.(isset($this->sequen)?$this->sequen:"null").',';
		$sql .= ' value_one = '.(isset($this->value_one)?$this->value_one:"null").',';
		$sql .= ' value_two = '.(isset($this->value_two)?$this->value_two:"null").',';
		$sql .= ' value_three = '.(isset($this->value_three)?$this->value_three:"null").',';
		$sql .= ' value_four = '.(isset($this->value_four)?$this->value_four:"null").',';
		$sql .= ' value_five = '.(isset($this->value_five)?$this->value_five:"null").',';
		$sql .= ' value_six = '.(isset($this->value_six)?$this->value_six:"null").',';
		$sql .= ' value_seven = '.(isset($this->value_seven)?$this->value_seven:"null").',';
		$sql .= ' res_one = '.(isset($this->res_one)?$this->res_one:"null").',';
		$sql .= ' res_two = '.(isset($this->res_two)?$this->res_two:"null").',';
		$sql .= ' res_three = '.(isset($this->res_three)?$this->res_three:"null").',';
		$sql .= ' res_four = '.(isset($this->res_four)?$this->res_four:"null").',';
		$sql .= ' res_five = '.(isset($this->res_five)?$this->res_five:"null").',';
		$sql .= ' quantity = '.(isset($this->quantity)?$this->quantity:"null").',';
		$sql .= ' fk_user_create = '.(isset($this->fk_user_create)?$this->fk_user_create:"null").',';
		$sql .= ' fk_user_mod = '.$user->id.',';
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
		$object = new Budgetincidentsdet($this->db);

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

        $url = DOL_URL_ROOT.'/budget/'.$this->table_name.'_card.php?id='.$this->id;

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
		
		$this->fk_budget_incident = '';
		$this->type = '';
		$this->fk_object = '';
		$this->object = '';
		$this->label = '';
		$this->fk_unit = '';
		$this->sequen = '';
		$this->value_one = '';
		$this->value_two = '';
		$this->value_three = '';
		$this->value_four = '';
		$this->value_five = '';
		$this->value_six = '';
		$this->value_seven = '';
		$this->res_one = '';
		$this->res_two = '';
		$this->res_three = '';
		$this->res_four = '';
		$this->res_five = '';
		$this->quantity = '';
		$this->fk_user_create = '';
		$this->fk_user_mod = '';
		$this->datec = '';
		$this->datem = '';
		$this->tms = '';
		$this->status = '';

		
	}

}

/**
 * Class BudgetincidentsdetLine
 */
class BudgetincidentsdetLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	
	public $fk_budget_incident;
	public $type;
	public $fk_object;
	public $object;
	public $label;
	public $fk_unit;
	public $sequen;
	public $value_one;
	public $value_two;
	public $value_three;
	public $value_four;
	public $value_five;
	public $value_six;
	public $value_seven;
	public $res_one;
	public $res_two;
	public $res_three;
	public $res_four;
	public $res_five;
	public $quantity;
	public $fk_user_create;
	public $fk_user_mod;
	public $datec = '';
	public $datem = '';
	public $tms = '';
	public $status;

	/**
	 * @var mixed Sample line property 2
	 */
	
}
