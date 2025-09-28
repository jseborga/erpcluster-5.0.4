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
 * \file    salary/psalaryhistory.class.php
 * \ingroup salary
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Psalaryhistory
 *
 * Put here description of your class
 *
 * @see CommonObject
 */
class Psalaryhistory extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'psalaryhistory';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'p_salary_history';

	/**
	 * @var PsalaryhistoryLine[] Lines
	 */
	public $lines = array();

	/**
	 */

	public $entity;
	public $ref;
	public $period_year;
	public $fk_salary_present;
	public $fk_proces;
	public $fk_type_fol;
	public $fk_concept;
	public $fk_period;
	public $fk_user;
	public $fk_cc;
	public $sequen;
	public $type;
	public $cuota;
	public $semana;
	public $amount_inf;
	public $amount;
	public $hours_info;
	public $hours;
	public $date_reg = '';
	public $date_create = '';
	public $date_mod = '';
	public $fk_user_create;
	public $fk_user_mod;
	public $fk_account;
	public $payment_state;
	public $tms = '';
	public $state;

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

		if (isset($this->entity)) {
			 $this->entity = trim($this->entity);
		}
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->period_year)) {
			 $this->period_year = trim($this->period_year);
		}
		if (isset($this->fk_salary_present)) {
			 $this->fk_salary_present = trim($this->fk_salary_present);
		}
		if (isset($this->fk_proces)) {
			 $this->fk_proces = trim($this->fk_proces);
		}
		if (isset($this->fk_type_fol)) {
			 $this->fk_type_fol = trim($this->fk_type_fol);
		}
		if (isset($this->fk_concept)) {
			 $this->fk_concept = trim($this->fk_concept);
		}
		if (isset($this->fk_period)) {
			 $this->fk_period = trim($this->fk_period);
		}
		if (isset($this->fk_user)) {
			 $this->fk_user = trim($this->fk_user);
		}
		if (isset($this->fk_cc)) {
			 $this->fk_cc = trim($this->fk_cc);
		}
		if (isset($this->sequen)) {
			 $this->sequen = trim($this->sequen);
		}
		if (isset($this->type)) {
			 $this->type = trim($this->type);
		}
		if (isset($this->cuota)) {
			 $this->cuota = trim($this->cuota);
		}
		if (isset($this->semana)) {
			 $this->semana = trim($this->semana);
		}
		if (isset($this->amount_inf)) {
			 $this->amount_inf = trim($this->amount_inf);
		}
		if (isset($this->amount)) {
			 $this->amount = trim($this->amount);
		}
		if (isset($this->hours_info)) {
			 $this->hours_info = trim($this->hours_info);
		}
		if (isset($this->hours)) {
			 $this->hours = trim($this->hours);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_mod)) {
			 $this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->fk_account)) {
			 $this->fk_account = trim($this->fk_account);
		}
		if (isset($this->payment_state)) {
			 $this->payment_state = trim($this->payment_state);
		}
		if (isset($this->state)) {
			 $this->state = trim($this->state);
		}



		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';

		$sql.= 'entity,';
		$sql.= 'ref,';
		$sql.= 'period_year,';
		$sql.= 'fk_salary_present,';
		$sql.= 'fk_proces,';
		$sql.= 'fk_type_fol,';
		$sql.= 'fk_concept,';
		$sql.= 'fk_period,';
		$sql.= 'fk_user,';
		$sql.= 'fk_cc,';
		$sql.= 'sequen,';
		$sql.= 'type,';
		$sql.= 'cuota,';
		$sql.= 'semana,';
		$sql.= 'amount_inf,';
		$sql.= 'amount,';
		$sql.= 'hours_info,';
		$sql.= 'hours,';
		$sql.= 'date_reg,';
		$sql.= 'date_create,';
		$sql.= 'date_mod,';
		$sql.= 'fk_user_create,';
		$sql.= 'fk_user_mod,';
		$sql.= 'fk_account,';
		$sql.= 'payment_state,';
		$sql.= 'state';


		$sql .= ') VALUES (';

		$sql .= ' '.(! isset($this->entity)?'NULL':$this->entity).',';
		$sql .= ' '.(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql .= ' '.(! isset($this->period_year)?'NULL':$this->period_year).',';
		$sql .= ' '.(! isset($this->fk_salary_present)?'NULL':$this->fk_salary_present).',';
		$sql .= ' '.(! isset($this->fk_proces)?'NULL':$this->fk_proces).',';
		$sql .= ' '.(! isset($this->fk_type_fol)?'NULL':$this->fk_type_fol).',';
		$sql .= ' '.(! isset($this->fk_concept)?'NULL':$this->fk_concept).',';
		$sql .= ' '.(! isset($this->fk_period)?'NULL':$this->fk_period).',';
		$sql .= ' '.(! isset($this->fk_user)?'NULL':$this->fk_user).',';
		$sql .= ' '.(! isset($this->fk_cc)?'NULL':$this->fk_cc).',';
		$sql .= ' '.(! isset($this->sequen)?'NULL':$this->sequen).',';
		$sql .= ' '.(! isset($this->type)?'NULL':$this->type).',';
		$sql .= ' '.(! isset($this->cuota)?'NULL':$this->cuota).',';
		$sql .= ' '.(! isset($this->semana)?'NULL':$this->semana).',';
		$sql .= ' '.(! isset($this->amount_inf)?'NULL':"'".$this->amount_inf."'").',';
		$sql .= ' '.(! isset($this->amount)?'NULL':"'".$this->amount."'").',';
		$sql .= ' '.(! isset($this->hours_info)?'NULL':$this->hours_info).',';
		$sql .= ' '.(! isset($this->hours)?'NULL':$this->hours).',';
		$sql .= ' '.(! isset($this->date_reg) || dol_strlen($this->date_reg)==0?'NULL':"'".$this->db->idate($this->date_reg)."'").',';
		$sql .= ' '.(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':"'".$this->db->idate($this->date_create)."'").',';
		$sql .= ' '.(! isset($this->date_mod) || dol_strlen($this->date_mod)==0?'NULL':"'".$this->db->idate($this->date_mod)."'").',';
		$sql .= ' '.(! isset($this->fk_user_create)?'NULL':$this->fk_user_create).',';
		$sql .= ' '.$user->id.',';
		$sql .= ' '.(! isset($this->fk_account)?'NULL':$this->fk_account).',';
		$sql .= ' '.(! isset($this->payment_state)?'NULL':$this->payment_state).',';
		$sql .= ' '.(! isset($this->state)?'NULL':$this->state);


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

		$sql .= " t.entity,";
		$sql .= " t.ref,";
		$sql .= " t.period_year,";
		$sql .= " t.fk_salary_present,";
		$sql .= " t.fk_proces,";
		$sql .= " t.fk_type_fol,";
		$sql .= " t.fk_concept,";
		$sql .= " t.fk_period,";
		$sql .= " t.fk_user,";
		$sql .= " t.fk_cc,";
		$sql .= " t.sequen,";
		$sql .= " t.type,";
		$sql .= " t.cuota,";
		$sql .= " t.semana,";
		$sql .= " t.amount_inf,";
		$sql .= " t.amount,";
		$sql .= " t.hours_info,";
		$sql .= " t.hours,";
		$sql .= " t.date_reg,";
		$sql .= " t.date_create,";
		$sql .= " t.date_mod,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.fk_account,";
		$sql .= " t.payment_state,";
		$sql .= " t.tms,";
		$sql .= " t.state";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
		    $sql .= " AND entity IN (" . getEntity("psalaryhistory", 1) . ")";
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

				$this->entity = $obj->entity;
				$this->ref = $obj->ref;
				$this->period_year = $obj->period_year;
				$this->fk_salary_present = $obj->fk_salary_present;
				$this->fk_proces = $obj->fk_proces;
				$this->fk_type_fol = $obj->fk_type_fol;
				$this->fk_concept = $obj->fk_concept;
				$this->fk_period = $obj->fk_period;
				$this->fk_user = $obj->fk_user;
				$this->fk_cc = $obj->fk_cc;
				$this->sequen = $obj->sequen;
				$this->type = $obj->type;
				$this->cuota = $obj->cuota;
				$this->semana = $obj->semana;
				$this->amount_inf = $obj->amount_inf;
				$this->amount = $obj->amount;
				$this->hours_info = $obj->hours_info;
				$this->hours = $obj->hours;
				$this->date_reg = $this->db->jdate($obj->date_reg);
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->date_mod = $this->db->jdate($obj->date_mod);
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_mod = $obj->fk_user_mod;
				$this->fk_account = $obj->fk_account;
				$this->payment_state = $obj->payment_state;
				$this->tms = $this->db->jdate($obj->tms);
				$this->state = $obj->state;


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

		$sql .= " t.entity,";
		$sql .= " t.ref,";
		$sql .= " t.period_year,";
		$sql .= " t.fk_salary_present,";
		$sql .= " t.fk_proces,";
		$sql .= " t.fk_type_fol,";
		$sql .= " t.fk_concept,";
		$sql .= " t.fk_period,";
		$sql .= " t.fk_user,";
		$sql .= " t.fk_cc,";
		$sql .= " t.sequen,";
		$sql .= " t.type,";
		$sql .= " t.cuota,";
		$sql .= " t.semana,";
		$sql .= " t.amount_inf,";
		$sql .= " t.amount,";
		$sql .= " t.hours_info,";
		$sql .= " t.hours,";
		$sql .= " t.date_reg,";
		$sql .= " t.date_create,";
		$sql .= " t.date_mod,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.fk_account,";
		$sql .= " t.payment_state,";
		$sql .= " t.tms,";
		$sql .= " t.state";


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
		    $sql .= " AND entity IN (" . getEntity("psalaryhistory", 1) . ")";
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
				$line = new PsalaryhistoryLine();

				$line->id = $obj->rowid;

				$line->entity = $obj->entity;
				$line->ref = $obj->ref;
				$line->period_year = $obj->period_year;
				$line->fk_salary_present = $obj->fk_salary_present;
				$line->fk_proces = $obj->fk_proces;
				$line->fk_type_fol = $obj->fk_type_fol;
				$line->fk_concept = $obj->fk_concept;
				$line->fk_period = $obj->fk_period;
				$line->fk_user = $obj->fk_user;
				$line->fk_cc = $obj->fk_cc;
				$line->sequen = $obj->sequen;
				$line->type = $obj->type;
				$line->cuota = $obj->cuota;
				$line->semana = $obj->semana;
				$line->amount_inf = $obj->amount_inf;
				$line->amount = $obj->amount;
				$line->hours_info = $obj->hours_info;
				$line->hours = $obj->hours;
				$line->date_reg = $this->db->jdate($obj->date_reg);
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->date_mod = $this->db->jdate($obj->date_mod);
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->fk_account = $obj->fk_account;
				$line->payment_state = $obj->payment_state;
				$line->tms = $this->db->jdate($obj->tms);
				$line->state = $obj->state;

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

		if (isset($this->entity)) {
			 $this->entity = trim($this->entity);
		}
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->period_year)) {
			 $this->period_year = trim($this->period_year);
		}
		if (isset($this->fk_salary_present)) {
			 $this->fk_salary_present = trim($this->fk_salary_present);
		}
		if (isset($this->fk_proces)) {
			 $this->fk_proces = trim($this->fk_proces);
		}
		if (isset($this->fk_type_fol)) {
			 $this->fk_type_fol = trim($this->fk_type_fol);
		}
		if (isset($this->fk_concept)) {
			 $this->fk_concept = trim($this->fk_concept);
		}
		if (isset($this->fk_period)) {
			 $this->fk_period = trim($this->fk_period);
		}
		if (isset($this->fk_user)) {
			 $this->fk_user = trim($this->fk_user);
		}
		if (isset($this->fk_cc)) {
			 $this->fk_cc = trim($this->fk_cc);
		}
		if (isset($this->sequen)) {
			 $this->sequen = trim($this->sequen);
		}
		if (isset($this->type)) {
			 $this->type = trim($this->type);
		}
		if (isset($this->cuota)) {
			 $this->cuota = trim($this->cuota);
		}
		if (isset($this->semana)) {
			 $this->semana = trim($this->semana);
		}
		if (isset($this->amount_inf)) {
			 $this->amount_inf = trim($this->amount_inf);
		}
		if (isset($this->amount)) {
			 $this->amount = trim($this->amount);
		}
		if (isset($this->hours_info)) {
			 $this->hours_info = trim($this->hours_info);
		}
		if (isset($this->hours)) {
			 $this->hours = trim($this->hours);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_mod)) {
			 $this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->fk_account)) {
			 $this->fk_account = trim($this->fk_account);
		}
		if (isset($this->payment_state)) {
			 $this->payment_state = trim($this->payment_state);
		}
		if (isset($this->state)) {
			 $this->state = trim($this->state);
		}



		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';

		$sql .= ' entity = '.(isset($this->entity)?$this->entity:"null").',';
		$sql .= ' ref = '.(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").',';
		$sql .= ' period_year = '.(isset($this->period_year)?$this->period_year:"null").',';
		$sql .= ' fk_salary_present = '.(isset($this->fk_salary_present)?$this->fk_salary_present:"null").',';
		$sql .= ' fk_proces = '.(isset($this->fk_proces)?$this->fk_proces:"null").',';
		$sql .= ' fk_type_fol = '.(isset($this->fk_type_fol)?$this->fk_type_fol:"null").',';
		$sql .= ' fk_concept = '.(isset($this->fk_concept)?$this->fk_concept:"null").',';
		$sql .= ' fk_period = '.(isset($this->fk_period)?$this->fk_period:"null").',';
		$sql .= ' fk_user = '.(isset($this->fk_user)?$this->fk_user:"null").',';
		$sql .= ' fk_cc = '.(isset($this->fk_cc)?$this->fk_cc:"null").',';
		$sql .= ' sequen = '.(isset($this->sequen)?$this->sequen:"null").',';
		$sql .= ' type = '.(isset($this->type)?$this->type:"null").',';
		$sql .= ' cuota = '.(isset($this->cuota)?$this->cuota:"null").',';
		$sql .= ' semana = '.(isset($this->semana)?$this->semana:"null").',';
		$sql .= ' amount_inf = '.(isset($this->amount_inf)?$this->amount_inf:"null").',';
		$sql .= ' amount = '.(isset($this->amount)?$this->amount:"null").',';
		$sql .= ' hours_info = '.(isset($this->hours_info)?$this->hours_info:"null").',';
		$sql .= ' hours = '.(isset($this->hours)?$this->hours:"null").',';
		$sql .= ' date_reg = '.(! isset($this->date_reg) || dol_strlen($this->date_reg) != 0 ? "'".$this->db->idate($this->date_reg)."'" : 'null').',';
		$sql .= ' date_create = '.(! isset($this->date_create) || dol_strlen($this->date_create) != 0 ? "'".$this->db->idate($this->date_create)."'" : 'null').',';
		$sql .= ' date_mod = '.(! isset($this->date_mod) || dol_strlen($this->date_mod) != 0 ? "'".$this->db->idate($this->date_mod)."'" : 'null').',';
		$sql .= ' fk_user_create = '.(isset($this->fk_user_create)?$this->fk_user_create:"null").',';
		$sql .= ' fk_user_mod = '.$user->id.',';
		$sql .= ' fk_account = '.(isset($this->fk_account)?$this->fk_account:"null").',';
		$sql .= ' payment_state = '.(isset($this->payment_state)?$this->payment_state:"null").',';
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
		$sql .= ' state = '.(isset($this->state)?$this->state:"null");


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
		$object = new Psalaryhistory($this->db);

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

        $url = DOL_URL_ROOT.'/salary/'.$this->table_name.'_card.php?id='.$this->id;

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

		$this->entity = '';
		$this->ref = '';
		$this->period_year = '';
		$this->fk_salary_present = '';
		$this->fk_proces = '';
		$this->fk_type_fol = '';
		$this->fk_concept = '';
		$this->fk_period = '';
		$this->fk_user = '';
		$this->fk_cc = '';
		$this->sequen = '';
		$this->type = '';
		$this->cuota = '';
		$this->semana = '';
		$this->amount_inf = '';
		$this->amount = '';
		$this->hours_info = '';
		$this->hours = '';
		$this->date_reg = '';
		$this->date_create = '';
		$this->date_mod = '';
		$this->fk_user_create = '';
		$this->fk_user_mod = '';
		$this->fk_account = '';
		$this->payment_state = '';
		$this->tms = '';
		$this->state = '';


	}

}

/**
 * Class PsalaryhistoryLine
 */
class PsalaryhistoryLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */

	public $entity;
	public $ref;
	public $period_year;
	public $fk_salary_present;
	public $fk_proces;
	public $fk_type_fol;
	public $fk_concept;
	public $fk_period;
	public $fk_user;
	public $fk_cc;
	public $sequen;
	public $type;
	public $cuota;
	public $semana;
	public $amount_inf;
	public $amount;
	public $hours_info;
	public $hours;
	public $date_reg = '';
	public $date_create = '';
	public $date_mod = '';
	public $fk_user_create;
	public $fk_user_mod;
	public $fk_account;
	public $payment_state;
	public $tms = '';
	public $state;

	/**
	 * @var mixed Sample line property 2
	 */

}
