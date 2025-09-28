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
 * \file    /projettaskadd.class.php
 * \ingroup
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Projettaskadd
 *
 * Put here description of your class
 */
class Projettaskadd extends CommonObject
{
	/**
	 * @var string Error code (or message)
	 * @deprecated
	 * @see Projettaskadd::errors
	 */
	public $error;
	/**
	 * @var string[] Error codes (or messages)
	 */
	public $errors = array();
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'projettaskadd';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'projet_task_add';

	/**
	 * @var ProjettaskaddLine[] Lines
	 */
	public $lines = array();

	/**
	 * @var int ID
	 */
	public $id;
	/**
	 */

	public $fk_task;
	public $fk_contrat;
	public $c_grupo;
	public $c_view;
	public $fk_unit;
	public $fk_type;
	public $fk_item;
	public $unit_program;
	public $unit_declared;
	public $unit_ejecuted;
	public $unit_amount;
	public $detail_close;
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

		if (isset($this->fk_task)) {
			 $this->fk_task = trim($this->fk_task);
		}
		if (isset($this->fk_contrat)) {
			 $this->fk_contrat = trim($this->fk_contrat);
		}
		if (isset($this->c_grupo)) {
			 $this->c_grupo = trim($this->c_grupo);
		}
		if (isset($this->c_view)) {
			 $this->c_view = trim($this->c_view);
		}
		if (isset($this->fk_unit)) {
			 $this->fk_unit = trim($this->fk_unit);
		}
		if (isset($this->fk_type)) {
			 $this->fk_type = trim($this->fk_type);
		}
		if (isset($this->fk_item)) {
			 $this->fk_item = trim($this->fk_item);
		}
		if (isset($this->unit_program)) {
			 $this->unit_program = trim($this->unit_program);
		}
		if (isset($this->unit_declared)) {
			 $this->unit_declared = trim($this->unit_declared);
		}
		if (isset($this->unit_ejecuted)) {
			 $this->unit_ejecuted = trim($this->unit_ejecuted);
		}
		if (isset($this->unit_amount)) {
			 $this->unit_amount = trim($this->unit_amount);
		}
		if (isset($this->detail_close)) {
			 $this->detail_close = trim($this->detail_close);
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

		$sql.= 'fk_task,';
		$sql.= 'fk_contrat,';
		$sql.= 'c_grupo,';
		$sql.= 'c_view,';
		$sql.= 'fk_unit,';
		$sql.= 'fk_type,';
		$sql.= 'fk_item,';
		$sql.= 'unit_program,';
		$sql.= 'unit_declared,';
		$sql.= 'unit_ejecuted,';
		$sql.= 'unit_amount,';
		$sql.= 'detail_close,';
		$sql.= 'fk_user_create,';
		$sql.= 'fk_user_mod,';
		$sql.= 'date_create,';
		$sql.= 'statut';


		$sql .= ') VALUES (';

		$sql .= ' '.(! isset($this->fk_task)?'NULL':$this->fk_task).',';
		$sql .= ' '.(! isset($this->fk_contrat)?'NULL':$this->fk_contrat).',';
		$sql .= ' '.(! isset($this->c_grupo)?'NULL':$this->c_grupo).',';
		$sql .= ' '.(! isset($this->c_view)?'NULL':$this->c_view).',';
		$sql .= ' '.(! isset($this->fk_unit)?'NULL':$this->fk_unit).',';
		$sql .= ' '.(! isset($this->fk_type)?'NULL':$this->fk_type).',';
		$sql .= ' '.(! isset($this->fk_item)?'NULL':$this->fk_item).',';
		$sql .= ' '.(! isset($this->unit_program)?'NULL':"'".$this->unit_program."'").',';
		$sql .= ' '.(! isset($this->unit_declared)?'NULL':"'".$this->unit_declared."'").',';
		$sql .= ' '.(! isset($this->unit_ejecuted)?'NULL':"'".$this->unit_ejecuted."'").',';
		$sql .= ' '.(! isset($this->unit_amount)?'NULL':"'".$this->unit_amount."'").',';
		$sql .= ' '.(! isset($this->detail_close)?'NULL':"'".$this->db->escape($this->detail_close)."'").',';
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
	public function fetch($id, $fk_task = 0)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.fk_task,";
		$sql .= " t.fk_contrat,";
		$sql .= " t.c_grupo,";
		$sql .= " t.c_view,";
		$sql .= " t.fk_unit,";
		$sql .= " t.fk_type,";
		$sql .= " t.fk_item,";
		$sql .= " t.unit_program,";
		$sql .= " t.unit_declared,";
		$sql .= " t.unit_ejecuted,";
		$sql .= " t.unit_amount,";
		$sql .= " t.detail_close,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.date_create,";
		$sql .= " t.tms,";
		$sql .= " t.statut";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		if ($fk_task > 0) {
			$sql .= ' WHERE t.fk_task = ' . $fk_task;
		} else {
			$sql .= ' WHERE t.rowid = ' . $id;
		}

		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			if ($numrows) {
				$obj = $this->db->fetch_object($resql);

				$this->id = $obj->rowid;

				$this->fk_task = $obj->fk_task;
				$this->fk_contrat = $obj->fk_contrat;
				$this->c_grupo = $obj->c_grupo;
				$this->c_view = $obj->c_view;
				$this->fk_unit = $obj->fk_unit;
				$this->fk_type = $obj->fk_type;
				$this->fk_item = $obj->fk_item;
				$this->unit_program = $obj->unit_program;
				$this->unit_declared = $obj->unit_declared;
				$this->unit_ejecuted = $obj->unit_ejecuted;
				$this->unit_amount = $obj->unit_amount;
				$this->detail_close = $obj->detail_close;
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_mod = $obj->fk_user_mod;
				$this->date_create = $this->db->jdate($obj->date_create);
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
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lRow=false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.fk_task,";
		$sql .= " t.fk_contrat,";
		$sql .= " t.c_grupo,";
		$sql .= " t.c_view,";
		$sql .= " t.fk_unit,";
		$sql .= " t.fk_type,";
		$sql .= " t.fk_item,";
		$sql .= " t.unit_program,";
		$sql .= " t.unit_declared,";
		$sql .= " t.unit_ejecuted,";
		$sql .= " t.unit_amount,";
		$sql .= " t.detail_close,";
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
				$line = new ProjettaskaddLine();

				$line->id = $obj->rowid;

				$line->fk_task = $obj->fk_task;
				$line->fk_contrat = $obj->fk_contrat;
				$line->c_grupo = $obj->c_grupo;
				$line->c_view = $obj->c_view;
				$line->fk_unit = $obj->fk_unit;
				$line->fk_type = $obj->fk_type;
				$line->fk_item = $obj->fk_item;
				$line->unit_program = $obj->unit_program;
				$line->unit_declared = $obj->unit_declared;
				$line->unit_ejecuted = $obj->unit_ejecuted;
				$line->unit_amount = $obj->unit_amount;
				$line->detail_close = $obj->detail_close;
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->tms = $this->db->jdate($obj->tms);
				$line->statut = $obj->statut;

				if ($lRow)
				  {
				    $this->id = $obj->rowid;

				    $this->fk_task = $obj->fk_task;
				    $this->fk_contrat = $obj->fk_contrat;
				    $this->c_grupo = $obj->c_grupo;
				    $this->c_view = $obj->c_view;
				    $this->fk_unit = $obj->fk_unit;
				    $this->fk_type = $obj->fk_type;
				    $this->fk_item = $obj->fk_item;
				    $this->unit_program = $obj->unit_program;
				    $this->unit_declared = $obj->unit_declared;
				    $this->unit_ejecuted = $obj->unit_ejecuted;
				    $this->unit_amount = $obj->unit_amount;
				    $this->detail_close = $obj->detail_close;
				    $this->fk_user_create = $obj->fk_user_create;
				    $this->fk_user_mod = $obj->fk_user_mod;
				    $this->date_create = $this->db->jdate($obj->date_create);
				    $this->tms = $this->db->jdate($obj->tms);
				    $this->statut = $obj->statut;

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

		if (isset($this->fk_task)) {
			 $this->fk_task = trim($this->fk_task);
		}
		if (isset($this->fk_contrat)) {
			 $this->fk_contrat = trim($this->fk_contrat);
		}
		if (isset($this->c_grupo)) {
			 $this->c_grupo = trim($this->c_grupo);
		}
		if (isset($this->c_view)) {
			 $this->c_view = trim($this->c_view);
		}
		if (isset($this->fk_unit)) {
			 $this->fk_unit = trim($this->fk_unit);
		}
		if (isset($this->fk_type)) {
			 $this->fk_type = trim($this->fk_type);
		}
		if (isset($this->fk_item)) {
			 $this->fk_item = trim($this->fk_item);
		}
		if (isset($this->unit_program)) {
			 $this->unit_program = trim($this->unit_program);
		}
		if (isset($this->unit_declared)) {
			 $this->unit_declared = trim($this->unit_declared);
		}
		if (isset($this->unit_ejecuted)) {
			 $this->unit_ejecuted = trim($this->unit_ejecuted);
		}
		if (isset($this->unit_amount)) {
			 $this->unit_amount = trim($this->unit_amount);
		}
		if (isset($this->detail_close)) {
			 $this->detail_close = trim($this->detail_close);
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

		$sql .= ' fk_task = '.(isset($this->fk_task)?$this->fk_task:"null").',';
		$sql .= ' fk_contrat = '.(isset($this->fk_contrat)?$this->fk_contrat:"null").',';
		$sql .= ' c_grupo = '.(isset($this->c_grupo)?$this->c_grupo:"null").',';
		$sql .= ' c_view = '.(isset($this->c_view)?$this->c_view:"null").',';
		$sql .= ' fk_unit = '.(isset($this->fk_unit)?$this->fk_unit:"null").',';
		$sql .= ' fk_type = '.(isset($this->fk_type)?$this->fk_type:"null").',';
		$sql .= ' fk_item = '.(isset($this->fk_item)?$this->fk_item:"null").',';
		$sql .= ' unit_program = '.(isset($this->unit_program)?$this->unit_program:"null").',';
		$sql .= ' unit_declared = '.(isset($this->unit_declared)?$this->unit_declared:"null").',';
		$sql .= ' unit_ejecuted = '.(isset($this->unit_ejecuted)?$this->unit_ejecuted:"null").',';
		$sql .= ' unit_amount = '.(isset($this->unit_amount)?$this->unit_amount:"null").',';
		$sql .= ' detail_close = '.(isset($this->detail_close)?"'".$this->db->escape($this->detail_close)."'":"null").',';
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
		$object = new Projettaskadd($this->db);

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

		$this->fk_task = '';
		$this->fk_contrat = '';
		$this->c_grupo = '';
		$this->c_view = '';
		$this->fk_unit = '';
		$this->fk_type = '';
		$this->fk_item = '';
		$this->unit_program = '';
		$this->unit_declared = '';
		$this->unit_ejecuted = '';
		$this->unit_amount = '';
		$this->detail_close = '';
		$this->fk_user_create = '';
		$this->fk_user_mod = '';
		$this->date_create = '';
		$this->tms = '';
		$this->statut = '';


	}

}

/**
 * Class ProjettaskaddLine
 */
class ProjettaskaddLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */

	public $fk_task;
	public $fk_contrat;
	public $c_grupo;
	public $c_view;
	public $fk_unit;
	public $fk_type;
	public $fk_item;
	public $unit_program;
	public $unit_declared;
	public $unit_ejecuted;
	public $unit_amount;
	public $detail_close;
	public $fk_user_create;
	public $fk_user_mod;
	public $date_create = '';
	public $tms = '';
	public $statut;

	/**
	 * @var mixed Sample line property 2
	 */

}
