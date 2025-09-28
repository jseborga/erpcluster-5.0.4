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
 * \file    assistance/typemarking.class.php
 * \ingroup assistance
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Typemarking
 *
 * Put here description of your class
 *
 * @see CommonObject
 */
class Typemarking extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'typemarking';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'type_marking';

	/**
	 * @var TypemarkingLine[] Lines
	 */
	public $lines = array();

	/**
	 */

	public $entity;
	public $ref;
	public $detail;
	public $mark;
	public $fixed_date = '';
	public $sex;
	public $day_def;
	public $primary_entry = '';
	public $primary_exit = '';
	public $secundary_entry = '';
	public $secundary_exit = '';
	public $third_entry = '';
	public $third_exit = '';
	public $fourth_entry = '';
	public $fourth_exit = '';
	public $fifth_entry = '';
	public $fifth_exit = '';
	public $sixth_entry = '';
	public $sixth_exit = '';
	public $additional_time;
	public $fk_user_create;
	public $fk_user_mod;
	public $date_create = '';
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

		if (isset($this->entity)) {
			 $this->entity = trim($this->entity);
		}
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->detail)) {
			 $this->detail = trim($this->detail);
		}
		if (isset($this->mark)) {
			 $this->mark = trim($this->mark);
		}
		if (isset($this->sex)) {
			 $this->sex = trim($this->sex);
		}
		if (isset($this->day_def)) {
			 $this->day_def = trim($this->day_def);
		}
		if (isset($this->additional_time)) {
			 $this->additional_time = trim($this->additional_time);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
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

		$sql.= 'entity,';
		$sql.= 'ref,';
		$sql.= 'detail,';
		$sql.= 'mark,';
		$sql.= 'fixed_date,';
		$sql.= 'sex,';
		$sql.= 'day_def,';
		$sql.= 'primary_entry,';
		$sql.= 'primary_exit,';
		$sql.= 'secundary_entry,';
		$sql.= 'secundary_exit,';
		$sql.= 'third_entry,';
		$sql.= 'third_exit,';
		$sql.= 'fourth_entry,';
		$sql.= 'fourth_exit,';
		$sql.= 'fifth_entry,';
		$sql.= 'fifth_exit,';
		$sql.= 'sixth_entry,';
		$sql.= 'sixth_exit,';
		$sql.= 'additional_time,';
		$sql.= 'fk_user_create,';
		$sql.= 'fk_user_mod,';
		$sql.= 'date_create,';
		$sql.= 'statut';


		$sql .= ') VALUES (';

		$sql .= ' '.(! isset($this->entity)?'NULL':$this->entity).',';
		$sql .= ' '.(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql .= ' '.(! isset($this->detail)?'NULL':"'".$this->db->escape($this->detail)."'").',';
		$sql .= ' '.(! isset($this->mark)?'NULL':$this->mark).',';
		$sql .= ' '.(! isset($this->fixed_date) || dol_strlen($this->fixed_date)==0?'NULL':"'".$this->db->idate($this->fixed_date)."'").',';
		$sql .= ' '.(! isset($this->sex)?'NULL':"'".$this->db->escape($this->sex)."'").',';
		$sql .= ' '.(! isset($this->day_def)?'NULL':"'".$this->db->escape($this->day_def)."'").',';
		$sql .= ' '.(! isset($this->primary_entry) || dol_strlen($this->primary_entry)==0?'NULL':"'".$this->db->idate($this->primary_entry)."'").',';
		$sql .= ' '.(! isset($this->primary_exit) || dol_strlen($this->primary_exit)==0?'NULL':"'".$this->db->idate($this->primary_exit)."'").',';
		$sql .= ' '.(! isset($this->secundary_entry) || dol_strlen($this->secundary_entry)==0?'NULL':"'".$this->db->idate($this->secundary_entry)."'").',';
		$sql .= ' '.(! isset($this->secundary_exit) || dol_strlen($this->secundary_exit)==0?'NULL':"'".$this->db->idate($this->secundary_exit)."'").',';
		$sql .= ' '.(! isset($this->third_entry) || dol_strlen($this->third_entry)==0?'NULL':"'".$this->db->idate($this->third_entry)."'").',';
		$sql .= ' '.(! isset($this->third_exit) || dol_strlen($this->third_exit)==0?'NULL':"'".$this->db->idate($this->third_exit)."'").',';
		$sql .= ' '.(! isset($this->fourth_entry) || dol_strlen($this->fourth_entry)==0?'NULL':"'".$this->db->idate($this->fourth_entry)."'").',';
		$sql .= ' '.(! isset($this->fourth_exit) || dol_strlen($this->fourth_exit)==0?'NULL':"'".$this->db->idate($this->fourth_exit)."'").',';
		$sql .= ' '.(! isset($this->fifth_entry) || dol_strlen($this->fifth_entry)==0?'NULL':"'".$this->db->idate($this->fifth_entry)."'").',';
		$sql .= ' '.(! isset($this->fifth_exit) || dol_strlen($this->fifth_exit)==0?'NULL':"'".$this->db->idate($this->fifth_exit)."'").',';
		$sql .= ' '.(! isset($this->sixth_entry) || dol_strlen($this->sixth_entry)==0?'NULL':"'".$this->db->idate($this->sixth_entry)."'").',';
		$sql .= ' '.(! isset($this->sixth_exit) || dol_strlen($this->sixth_exit)==0?'NULL':"'".$this->db->idate($this->sixth_exit)."'").',';
		$sql .= ' '.(! isset($this->additional_time)?'NULL':$this->additional_time).',';
		$sql .= ' '.(! isset($this->fk_user_create)?'NULL':$this->fk_user_create).',';
		$sql .= ' '.$user->id.',';
		$sql .= ' '.(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':"'".$this->db->idate($this->date_create)."'").',';
		$sql .= ' '.(! isset($this->statut)?'NULL':$this->statut);


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
		$sql .= " t.detail,";
		$sql .= " t.mark,";
		$sql .= " t.fixed_date,";
		$sql .= " t.sex,";
		$sql .= " t.day_def,";
		$sql .= " t.primary_entry,";
		$sql .= " t.primary_exit,";
		$sql .= " t.secundary_entry,";
		$sql .= " t.secundary_exit,";
		$sql .= " t.third_entry,";
		$sql .= " t.third_exit,";
		$sql .= " t.fourth_entry,";
		$sql .= " t.fourth_exit,";
		$sql .= " t.fifth_entry,";
		$sql .= " t.fifth_exit,";
		$sql .= " t.sixth_entry,";
		$sql .= " t.sixth_exit,";
		$sql .= " t.additional_time,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.date_create,";
		$sql .= " t.tms,";
		$sql .= " t.statut";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
		    $sql .= " AND entity IN (" . getEntity("typemarking", 1) . ")";
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
				$this->detail = $obj->detail;
				$this->mark = $obj->mark;
				$this->fixed_date = $this->db->jdate($obj->fixed_date);
				$this->sex = $obj->sex;
				$this->day_def = $obj->day_def;
				$this->primary_entry = $this->db->jdate($obj->primary_entry);
				$this->primary_exit = $this->db->jdate($obj->primary_exit);
				$this->secundary_entry = $this->db->jdate($obj->secundary_entry);
				$this->secundary_exit = $this->db->jdate($obj->secundary_exit);
				$this->third_entry = $this->db->jdate($obj->third_entry);
				$this->third_exit = $this->db->jdate($obj->third_exit);
				$this->fourth_entry = $this->db->jdate($obj->fourth_entry);
				$this->fourth_exit = $this->db->jdate($obj->fourth_exit);
				$this->fifth_entry = $this->db->jdate($obj->fifth_entry);
				$this->fifth_exit = $this->db->jdate($obj->fifth_exit);
				$this->sixth_entry = $this->db->jdate($obj->sixth_entry);
				$this->sixth_exit = $this->db->jdate($obj->sixth_exit);
				$this->additional_time = $obj->additional_time;
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_mod = $obj->fk_user_mod;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->tms = $this->db->jdate($obj->tms);
				$this->statut = $obj->statut;


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
		$sql .= " t.detail,";
		$sql .= " t.mark,";
		$sql .= " t.fixed_date,";
		$sql .= " t.sex,";
		$sql .= " t.day_def,";
		$sql .= " t.primary_entry,";
		$sql .= " t.primary_exit,";
		$sql .= " t.secundary_entry,";
		$sql .= " t.secundary_exit,";
		$sql .= " t.third_entry,";
		$sql .= " t.third_exit,";
		$sql .= " t.fourth_entry,";
		$sql .= " t.fourth_exit,";
		$sql .= " t.fifth_entry,";
		$sql .= " t.fifth_exit,";
		$sql .= " t.sixth_entry,";
		$sql .= " t.sixth_exit,";
		$sql .= " t.additional_time,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.date_create,";
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
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
		    $sql .= " AND entity IN (" . getEntity("typemarking", 1) . ")";
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
				$line = new TypemarkingLine();

				$line->id = $obj->rowid;

				$line->entity = $obj->entity;
				$line->ref = $obj->ref;
				$line->detail = $obj->detail;
				$line->mark = $obj->mark;
				$line->fixed_date = $this->db->jdate($obj->fixed_date);
				$line->sex = $obj->sex;
				$line->day_def = $obj->day_def;
				$line->primary_entry = $this->db->jdate($obj->primary_entry);
				$line->primary_exit = $this->db->jdate($obj->primary_exit);
				$line->secundary_entry = $this->db->jdate($obj->secundary_entry);
				$line->secundary_exit = $this->db->jdate($obj->secundary_exit);
				$line->third_entry = $this->db->jdate($obj->third_entry);
				$line->third_exit = $this->db->jdate($obj->third_exit);
				$line->fourth_entry = $this->db->jdate($obj->fourth_entry);
				$line->fourth_exit = $this->db->jdate($obj->fourth_exit);
				$line->fifth_entry = $this->db->jdate($obj->fifth_entry);
				$line->fifth_exit = $this->db->jdate($obj->fifth_exit);
				$line->sixth_entry = $this->db->jdate($obj->sixth_entry);
				$line->sixth_exit = $this->db->jdate($obj->sixth_exit);
				$line->additional_time = $obj->additional_time;
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->tms = $this->db->jdate($obj->tms);
				$line->statut = $obj->statut;



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
		if (isset($this->detail)) {
			 $this->detail = trim($this->detail);
		}
		if (isset($this->mark)) {
			 $this->mark = trim($this->mark);
		}
		if (isset($this->sex)) {
			 $this->sex = trim($this->sex);
		}
		if (isset($this->day_def)) {
			 $this->day_def = trim($this->day_def);
		}
		if (isset($this->additional_time)) {
			 $this->additional_time = trim($this->additional_time);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
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

		$sql .= ' entity = '.(isset($this->entity)?$this->entity:"null").',';
		$sql .= ' ref = '.(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").',';
		$sql .= ' detail = '.(isset($this->detail)?"'".$this->db->escape($this->detail)."'":"null").',';
		$sql .= ' mark = '.(isset($this->mark)?$this->mark:"null").',';
		$sql .= ' fixed_date = '.(! isset($this->fixed_date) || dol_strlen($this->fixed_date) != 0 ? "'".$this->db->idate($this->fixed_date)."'" : 'null').',';
		$sql .= ' sex = '.(isset($this->sex)?"'".$this->db->escape($this->sex)."'":"null").',';
		$sql .= ' day_def = '.(isset($this->day_def)?"'".$this->db->escape($this->day_def)."'":"null").',';
		$sql .= ' primary_entry = '.(! isset($this->primary_entry) || dol_strlen($this->primary_entry) != 0 ? "'".$this->db->idate($this->primary_entry)."'" : 'null').',';
		$sql .= ' primary_exit = '.(! isset($this->primary_exit) || dol_strlen($this->primary_exit) != 0 ? "'".$this->db->idate($this->primary_exit)."'" : 'null').',';
		$sql .= ' secundary_entry = '.(! isset($this->secundary_entry) || dol_strlen($this->secundary_entry) != 0 ? "'".$this->db->idate($this->secundary_entry)."'" : 'null').',';
		$sql .= ' secundary_exit = '.(! isset($this->secundary_exit) || dol_strlen($this->secundary_exit) != 0 ? "'".$this->db->idate($this->secundary_exit)."'" : 'null').',';
		$sql .= ' third_entry = '.(! isset($this->third_entry) || dol_strlen($this->third_entry) != 0 ? "'".$this->db->idate($this->third_entry)."'" : 'null').',';
		$sql .= ' third_exit = '.(! isset($this->third_exit) || dol_strlen($this->third_exit) != 0 ? "'".$this->db->idate($this->third_exit)."'" : 'null').',';
		$sql .= ' fourth_entry = '.(! isset($this->fourth_entry) || dol_strlen($this->fourth_entry) != 0 ? "'".$this->db->idate($this->fourth_entry)."'" : 'null').',';
		$sql .= ' fourth_exit = '.(! isset($this->fourth_exit) || dol_strlen($this->fourth_exit) != 0 ? "'".$this->db->idate($this->fourth_exit)."'" : 'null').',';
		$sql .= ' fifth_entry = '.(! isset($this->fifth_entry) || dol_strlen($this->fifth_entry) != 0 ? "'".$this->db->idate($this->fifth_entry)."'" : 'null').',';
		$sql .= ' fifth_exit = '.(! isset($this->fifth_exit) || dol_strlen($this->fifth_exit) != 0 ? "'".$this->db->idate($this->fifth_exit)."'" : 'null').',';
		$sql .= ' sixth_entry = '.(! isset($this->sixth_entry) || dol_strlen($this->sixth_entry) != 0 ? "'".$this->db->idate($this->sixth_entry)."'" : 'null').',';
		$sql .= ' sixth_exit = '.(! isset($this->sixth_exit) || dol_strlen($this->sixth_exit) != 0 ? "'".$this->db->idate($this->sixth_exit)."'" : 'null').',';
		$sql .= ' additional_time = '.(isset($this->additional_time)?$this->additional_time:"null").',';
		$sql .= ' fk_user_create = '.(isset($this->fk_user_create)?$this->fk_user_create:"null").',';
		$sql .= ' fk_user_mod = '.$user->id.',';
		$sql .= ' date_create = '.(! isset($this->date_create) || dol_strlen($this->date_create) != 0 ? "'".$this->db->idate($this->date_create)."'" : 'null').',';
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
		$sql .= ' statut = '.(isset($this->statut)?$this->statut:"null");


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
		$object = new Typemarking($this->db);

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

        $url = DOL_URL_ROOT.'/assistance/'.$this->table_name.'_card.php?id='.$this->id;

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
		$this->detail = '';
		$this->mark = '';
		$this->fixed_date = '';
		$this->sex = '';
		$this->day_def = '';
		$this->primary_entry = '';
		$this->primary_exit = '';
		$this->secundary_entry = '';
		$this->secundary_exit = '';
		$this->third_entry = '';
		$this->third_exit = '';
		$this->fourth_entry = '';
		$this->fourth_exit = '';
		$this->fifth_entry = '';
		$this->fifth_exit = '';
		$this->sixth_entry = '';
		$this->sixth_exit = '';
		$this->additional_time = '';
		$this->fk_user_create = '';
		$this->fk_user_mod = '';
		$this->date_create = '';
		$this->tms = '';
		$this->statut = '';


	}

}

/**
 * Class TypemarkingLine
 */
class TypemarkingLine
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
	public $detail;
	public $mark;
	public $fixed_date = '';
	public $sex;
	public $day_def;
	public $primary_entry = '';
	public $primary_exit = '';
	public $secundary_entry = '';
	public $secundary_exit = '';
	public $third_entry = '';
	public $third_exit = '';
	public $fourth_entry = '';
	public $fourth_exit = '';
	public $fifth_entry = '';
	public $fifth_exit = '';
	public $sixth_entry = '';
	public $sixth_exit = '';
	public $additional_time;
	public $fk_user_create;
	public $fk_user_mod;
	public $date_create = '';
	public $tms = '';
	public $statut;

	/**
	 * @var mixed Sample line property 2
	 */

}
