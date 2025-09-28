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
 * \file    /projettaskcontrat.class.php
 * \ingroup
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Projettaskcontrat
 *
 * Put here description of your class
 */
class Projettaskcontrat extends CommonObject
{
	/**
	 * @var string Error code (or message)
	 * @deprecated
	 * @see Projettaskcontrat::errors
	 */
	public $error;
	/**
	 * @var string[] Error codes (or messages)
	 */
	public $errors = array();
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'projettaskcontrat';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'projet_task_contrat';

	/**
	 * @var ProjettaskcontratLine[] Lines
	 */
	public $lines = array();

	/**
	 * @var int ID
	 */
	public $id;
	/**
	 */

	public $ref;
	public $entity;
	public $fk_projet;
	public $fk_contrat;
	public $datec = '';
	public $tms = '';
	public $dateo = '';
	public $datee = '';
	public $datev = '';
	public $label;
	public $description;
	public $priority;
	public $fk_user_creat;
	public $fk_user_valid;
	public $c_grupo;
	public $fk_type;
	public $unit_program;
	public $fk_unit;
	public $unit_amount;
	public $fk_statut;

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
		return 1;
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

		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->entity)) {
			 $this->entity = trim($this->entity);
		}
		if (isset($this->fk_projet)) {
			 $this->fk_projet = trim($this->fk_projet);
		}
		if (isset($this->fk_contrat)) {
			 $this->fk_contrat = trim($this->fk_contrat);
		}
		if (isset($this->label)) {
			 $this->label = trim($this->label);
		}
		if (isset($this->description)) {
			 $this->description = trim($this->description);
		}
		if (isset($this->priority)) {
			 $this->priority = trim($this->priority);
		}
		if (isset($this->fk_user_creat)) {
			 $this->fk_user_creat = trim($this->fk_user_creat);
		}
		if (isset($this->fk_user_valid)) {
			 $this->fk_user_valid = trim($this->fk_user_valid);
		}
		if (isset($this->c_grupo)) {
			 $this->c_grupo = trim($this->c_grupo);
		}
		if (isset($this->fk_type)) {
			 $this->fk_type = trim($this->fk_type);
		}
		if (isset($this->unit_program)) {
			 $this->unit_program = trim($this->unit_program);
		}
		if (isset($this->fk_unit)) {
			 $this->fk_unit = trim($this->fk_unit);
		}
		if (isset($this->unit_amount)) {
			 $this->unit_amount = trim($this->unit_amount);
		}
		if (isset($this->fk_statut)) {
			 $this->fk_statut = trim($this->fk_statut);
		}



		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';

		$sql.= 'ref,';
		$sql.= 'entity,';
		$sql.= 'fk_projet,';
		$sql.= 'fk_contrat,';
		$sql.= 'datec,';
		$sql.= 'dateo,';
		$sql.= 'datee,';
		$sql.= 'datev,';
		$sql.= 'label,';
		$sql.= 'description,';
		$sql.= 'priority,';
		$sql.= 'fk_user_creat,';
		$sql.= 'fk_user_valid,';
		$sql.= 'c_grupo,';
		$sql.= 'fk_type,';
		$sql.= 'unit_program,';
		$sql.= 'fk_unit,';
		$sql.= 'unit_amount,';
		$sql.= 'fk_statut';


		$sql .= ') VALUES (';

		$sql .= ' '.(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql .= ' '.(! isset($this->entity)?'NULL':$this->entity).',';
		$sql .= ' '.(! isset($this->fk_projet)?'NULL':$this->fk_projet).',';
		$sql .= ' '.(! isset($this->fk_contrat)?'NULL':$this->fk_contrat).',';
		$sql .= ' '."'".$this->db->idate(dol_now())."'".',';
		$sql .= ' '.(! isset($this->dateo) || dol_strlen($this->dateo)==0?'NULL':"'".$this->db->idate($this->dateo)."'").',';
		$sql .= ' '.(! isset($this->datee) || dol_strlen($this->datee)==0?'NULL':"'".$this->db->idate($this->datee)."'").',';
		$sql .= ' '.(! isset($this->datev) || dol_strlen($this->datev)==0?'NULL':"'".$this->db->idate($this->datev)."'").',';
		$sql .= ' '.(! isset($this->label)?'NULL':"'".$this->db->escape($this->label)."'").',';
		$sql .= ' '.(! isset($this->description)?'NULL':"'".$this->db->escape($this->description)."'").',';
		$sql .= ' '.(! isset($this->priority)?'NULL':$this->priority).',';
		$sql .= ' '.(! isset($this->fk_user_creat)?'NULL':$this->fk_user_creat).',';
		$sql .= ' '.(! isset($this->fk_user_valid)?'NULL':$this->fk_user_valid).',';
		$sql .= ' '.(! isset($this->c_grupo)?'NULL':$this->c_grupo).',';
		$sql .= ' '.(! isset($this->fk_type)?'NULL':$this->fk_type).',';
		$sql .= ' '.(! isset($this->unit_program)?'NULL':"'".$this->unit_program."'").',';
		$sql .= ' '.(! isset($this->fk_unit)?'NULL':$this->fk_unit).',';
		$sql .= ' '.(! isset($this->unit_amount)?'NULL':"'".$this->unit_amount."'").',';
		$sql .= ' '.(! isset($this->fk_statut)?'NULL':$this->fk_statut);


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

		$sql .= " t.ref,";
		$sql .= " t.entity,";
		$sql .= " t.fk_projet,";
		$sql .= " t.fk_contrat,";
		$sql .= " t.datec,";
		$sql .= " t.tms,";
		$sql .= " t.dateo,";
		$sql .= " t.datee,";
		$sql .= " t.datev,";
		$sql .= " t.label,";
		$sql .= " t.description,";
		$sql .= " t.priority,";
		$sql .= " t.fk_user_creat,";
		$sql .= " t.fk_user_valid,";
		$sql .= " t.c_grupo,";
		$sql .= " t.fk_type,";
		$sql .= " t.unit_program,";
		$sql .= " t.fk_unit,";
		$sql .= " t.unit_amount,";
		$sql .= " t.fk_statut";


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

				$this->ref = $obj->ref;
				$this->entity = $obj->entity;
				$this->fk_projet = $obj->fk_projet;
				$this->fk_contrat = $obj->fk_contrat;
				$this->datec = $this->db->jdate($obj->datec);
				$this->tms = $this->db->jdate($obj->tms);
				$this->dateo = $this->db->jdate($obj->dateo);
				$this->datee = $this->db->jdate($obj->datee);
				$this->datev = $this->db->jdate($obj->datev);
				$this->label = $obj->label;
				$this->description = $obj->description;
				$this->priority = $obj->priority;
				$this->fk_user_creat = $obj->fk_user_creat;
				$this->fk_user_valid = $obj->fk_user_valid;
				$this->c_grupo = $obj->c_grupo;
				$this->fk_type = $obj->fk_type;
				$this->unit_program = $obj->unit_program;
				$this->fk_unit = $obj->fk_unit;
				$this->unit_amount = $obj->unit_amount;
				$this->fk_statut = $obj->fk_statut;


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
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lRow=false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.ref,";
		$sql .= " t.entity,";
		$sql .= " t.fk_projet,";
		$sql .= " t.fk_contrat,";
		$sql .= " t.datec,";
		$sql .= " t.tms,";
		$sql .= " t.dateo,";
		$sql .= " t.datee,";
		$sql .= " t.datev,";
		$sql .= " t.label,";
		$sql .= " t.description,";
		$sql .= " t.priority,";
		$sql .= " t.fk_user_creat,";
		$sql .= " t.fk_user_valid,";
		$sql .= " t.c_grupo,";
		$sql .= " t.fk_type,";
		$sql .= " t.unit_program,";
		$sql .= " t.fk_unit,";
		$sql .= " t.unit_amount,";
		$sql .= " t.fk_statut";


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
		if ($filterstatic) $sql.= $filterstatic;
		if (!empty($sortfield)) {
			$sql .= ' ORDER BY ' . $sortfield . ' ' . $sortorder;
		}
		if (!empty($limit)) {
		 $sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
		}

		$this->lines = array();

		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new ProjettaskcontratLine();

				$line->id = $obj->rowid;

				$line->ref = $obj->ref;
				$line->entity = $obj->entity;
				$line->fk_projet = $obj->fk_projet;
				$line->fk_contrat = $obj->fk_contrat;
				$line->datec = $this->db->jdate($obj->datec);
				$line->tms = $this->db->jdate($obj->tms);
				$line->dateo = $this->db->jdate($obj->dateo);
				$line->datee = $this->db->jdate($obj->datee);
				$line->datev = $this->db->jdate($obj->datev);
				$line->label = $obj->label;
				$line->description = $obj->description;
				$line->priority = $obj->priority;
				$line->fk_user_creat = $obj->fk_user_creat;
				$line->fk_user_valid = $obj->fk_user_valid;
				$line->c_grupo = $obj->c_grupo;
				$line->fk_type = $obj->fk_type;
				$line->unit_program = $obj->unit_program;
				$line->fk_unit = $obj->fk_unit;
				$line->unit_amount = $obj->unit_amount;
				$line->fk_statut = $obj->fk_statut;

				if ($lRow)
				  {
				    $this->id = $obj->rowid;

				    $this->ref = $obj->ref;
				    $this->entity = $obj->entity;
				    $this->fk_projet = $obj->fk_projet;
				    $this->fk_contrat = $obj->fk_contrat;
				    $this->datec = $this->db->jdate($obj->datec);
				    $this->tms = $this->db->jdate($obj->tms);
				    $this->dateo = $this->db->jdate($obj->dateo);
				    $this->datee = $this->db->jdate($obj->datee);
				    $this->datev = $this->db->jdate($obj->datev);
				    $this->label = $obj->label;
				    $this->description = $obj->description;
				    $this->priority = $obj->priority;
				    $this->fk_user_creat = $obj->fk_user_creat;
				    $this->fk_user_valid = $obj->fk_user_valid;
				    $this->c_grupo = $obj->c_grupo;
				    $this->fk_type = $obj->fk_type;
				    $this->unit_program = $obj->unit_program;
				    $this->fk_unit = $obj->fk_unit;
				    $this->unit_amount = $obj->unit_amount;
				    $this->fk_statut = $obj->fk_statut;

				  }

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

		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->entity)) {
			 $this->entity = trim($this->entity);
		}
		if (isset($this->fk_projet)) {
			 $this->fk_projet = trim($this->fk_projet);
		}
		if (isset($this->fk_contrat)) {
			 $this->fk_contrat = trim($this->fk_contrat);
		}
		if (isset($this->label)) {
			 $this->label = trim($this->label);
		}
		if (isset($this->description)) {
			 $this->description = trim($this->description);
		}
		if (isset($this->priority)) {
			 $this->priority = trim($this->priority);
		}
		if (isset($this->fk_user_creat)) {
			 $this->fk_user_creat = trim($this->fk_user_creat);
		}
		if (isset($this->fk_user_valid)) {
			 $this->fk_user_valid = trim($this->fk_user_valid);
		}
		if (isset($this->c_grupo)) {
			 $this->c_grupo = trim($this->c_grupo);
		}
		if (isset($this->fk_type)) {
			 $this->fk_type = trim($this->fk_type);
		}
		if (isset($this->unit_program)) {
			 $this->unit_program = trim($this->unit_program);
		}
		if (isset($this->fk_unit)) {
			 $this->fk_unit = trim($this->fk_unit);
		}
		if (isset($this->unit_amount)) {
			 $this->unit_amount = trim($this->unit_amount);
		}
		if (isset($this->fk_statut)) {
			 $this->fk_statut = trim($this->fk_statut);
		}



		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';

		$sql .= ' ref = '.(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").',';
		$sql .= ' entity = '.(isset($this->entity)?$this->entity:"null").',';
		$sql .= ' fk_projet = '.(isset($this->fk_projet)?$this->fk_projet:"null").',';
		$sql .= ' fk_contrat = '.(isset($this->fk_contrat)?$this->fk_contrat:"null").',';
		$sql .= ' datec = '.(! isset($this->datec) || dol_strlen($this->datec) != 0 ? "'".$this->db->idate($this->datec)."'" : 'null').',';
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
		$sql .= ' dateo = '.(! isset($this->dateo) || dol_strlen($this->dateo) != 0 ? "'".$this->db->idate($this->dateo)."'" : 'null').',';
		$sql .= ' datee = '.(! isset($this->datee) || dol_strlen($this->datee) != 0 ? "'".$this->db->idate($this->datee)."'" : 'null').',';
		$sql .= ' datev = '.(! isset($this->datev) || dol_strlen($this->datev) != 0 ? "'".$this->db->idate($this->datev)."'" : 'null').',';
		$sql .= ' label = '.(isset($this->label)?"'".$this->db->escape($this->label)."'":"null").',';
		$sql .= ' description = '.(isset($this->description)?"'".$this->db->escape($this->description)."'":"null").',';
		$sql .= ' priority = '.(isset($this->priority)?$this->priority:"null").',';
		$sql .= ' fk_user_creat = '.(isset($this->fk_user_creat)?$this->fk_user_creat:"null").',';
		$sql .= ' fk_user_valid = '.(isset($this->fk_user_valid)?$this->fk_user_valid:"null").',';
		$sql .= ' c_grupo = '.(isset($this->c_grupo)?$this->c_grupo:"null").',';
		$sql .= ' fk_type = '.(isset($this->fk_type)?$this->fk_type:"null").',';
		$sql .= ' unit_program = '.(isset($this->unit_program)?$this->unit_program:"null").',';
		$sql .= ' fk_unit = '.(isset($this->fk_unit)?$this->fk_unit:"null").',';
		$sql .= ' unit_amount = '.(isset($this->unit_amount)?$this->unit_amount:"null").',';
		$sql .= ' fk_statut = '.(isset($this->fk_statut)?$this->fk_statut:"null");


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
		$object = new Projettaskcontrat($this->db);

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
	 * Initialise object with example values
	 * Id must be 0 if object instance is a specimen
	 *
	 * @return void
	 */
	public function initAsSpecimen()
	{
		$this->id = 0;

		$this->ref = '';
		$this->entity = '';
		$this->fk_projet = '';
		$this->fk_contrat = '';
		$this->datec = '';
		$this->tms = '';
		$this->dateo = '';
		$this->datee = '';
		$this->datev = '';
		$this->label = '';
		$this->description = '';
		$this->priority = '';
		$this->fk_user_creat = '';
		$this->fk_user_valid = '';
		$this->c_grupo = '';
		$this->fk_type = '';
		$this->unit_program = '';
		$this->fk_unit = '';
		$this->unit_amount = '';
		$this->fk_statut = '';


	}

}

/**
 * Class ProjettaskcontratLine
 */
class ProjettaskcontratLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */

	public $ref;
	public $entity;
	public $fk_projet;
	public $fk_contrat;
	public $datec = '';
	public $tms = '';
	public $dateo = '';
	public $datee = '';
	public $datev = '';
	public $label;
	public $description;
	public $priority;
	public $fk_user_creat;
	public $fk_user_valid;
	public $c_grupo;
	public $fk_type;
	public $unit_program;
	public $fk_unit;
	public $unit_amount;
	public $fk_statut;

	/**
	 * @var mixed Sample line property 2
	 */

}
