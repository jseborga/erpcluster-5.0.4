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
 * \file    fiscal/subsidiary.class.php
 * \ingroup fiscal
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Subsidiary
 *
 * Put here description of your class
 * @see CommonObject
 */
class Subsidiary extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'subsidiary';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'subsidiary';

	/**
	 * @var SubsidiaryLine[] Lines
	 */
	public $lines = array();

	/**
	 */

	public $entity;
	public $ref;
	public $label;
	public $subsidiary_number;
	public $subsidiary_matriz;
	public $socialreason;
	public $nit;
	public $activity;
	public $address;
	public $city;
	public $phone;
	public $serie;
	public $message;
	public $matriz_name;
	public $matriz_address;
	public $matriz_phone;
	public $matriz_city;
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

		if (isset($this->entity)) {
			 $this->entity = trim($this->entity);
		}
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->label)) {
			 $this->label = trim($this->label);
		}
		if (isset($this->subsidiary_number)) {
			 $this->subsidiary_number = trim($this->subsidiary_number);
		}
		if (isset($this->subsidiary_matriz)) {
			 $this->subsidiary_matriz = trim($this->subsidiary_matriz);
		}
		if (isset($this->socialreason)) {
			 $this->socialreason = trim($this->socialreason);
		}
		if (isset($this->nit)) {
			 $this->nit = trim($this->nit);
		}
		if (isset($this->activity)) {
			 $this->activity = trim($this->activity);
		}
		if (isset($this->address)) {
			 $this->address = trim($this->address);
		}
		if (isset($this->city)) {
			 $this->city = trim($this->city);
		}
		if (isset($this->phone)) {
			 $this->phone = trim($this->phone);
		}
		if (isset($this->serie)) {
			 $this->serie = trim($this->serie);
		}
		if (isset($this->message)) {
			 $this->message = trim($this->message);
		}
		if (isset($this->matriz_name)) {
			 $this->matriz_name = trim($this->matriz_name);
		}
		if (isset($this->matriz_address)) {
			 $this->matriz_address = trim($this->matriz_address);
		}
		if (isset($this->matriz_phone)) {
			 $this->matriz_phone = trim($this->matriz_phone);
		}
		if (isset($this->matriz_city)) {
			 $this->matriz_city = trim($this->matriz_city);
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

		$sql.= 'entity,';
		$sql.= 'ref,';
		$sql.= 'label,';
		$sql.= 'subsidiary_number,';
		$sql.= 'subsidiary_matriz,';
		$sql.= 'socialreason,';
		$sql.= 'nit,';
		$sql.= 'activity,';
		$sql.= 'address,';
		$sql.= 'city,';
		$sql.= 'phone,';
		$sql.= 'serie,';
		$sql.= 'message,';
		$sql.= 'matriz_name,';
		$sql.= 'matriz_address,';
		$sql.= 'matriz_phone,';
		$sql.= 'matriz_city,';
		$sql.= 'fk_user_create,';
		$sql.= 'fk_user_mod,';
		$sql.= 'date_create,';
		$sql.= 'date_mod,';
		$sql.= 'status';


		$sql .= ') VALUES (';

		$sql .= ' '.(! isset($this->entity)?'NULL':$this->entity).',';
		$sql .= ' '.(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql .= ' '.(! isset($this->label)?'NULL':"'".$this->db->escape($this->label)."'").',';
		$sql .= ' '.(! isset($this->subsidiary_number)?'NULL':"'".$this->db->escape($this->subsidiary_number)."'").',';
		$sql .= ' '.(! isset($this->subsidiary_matriz)?'NULL':$this->subsidiary_matriz).',';
		$sql .= ' '.(! isset($this->socialreason)?'NULL':"'".$this->db->escape($this->socialreason)."'").',';
		$sql .= ' '.(! isset($this->nit)?'NULL':"'".$this->db->escape($this->nit)."'").',';
		$sql .= ' '.(! isset($this->activity)?'NULL':"'".$this->db->escape($this->activity)."'").',';
		$sql .= ' '.(! isset($this->address)?'NULL':"'".$this->db->escape($this->address)."'").',';
		$sql .= ' '.(! isset($this->city)?'NULL':"'".$this->db->escape($this->city)."'").',';
		$sql .= ' '.(! isset($this->phone)?'NULL':"'".$this->db->escape($this->phone)."'").',';
		$sql .= ' '.(! isset($this->serie)?'NULL':"'".$this->db->escape($this->serie)."'").',';
		$sql .= ' '.(! isset($this->message)?'NULL':"'".$this->db->escape($this->message)."'").',';
		$sql .= ' '.(! isset($this->matriz_name)?'NULL':"'".$this->db->escape($this->matriz_name)."'").',';
		$sql .= ' '.(! isset($this->matriz_address)?'NULL':"'".$this->db->escape($this->matriz_address)."'").',';
		$sql .= ' '.(! isset($this->matriz_phone)?'NULL':$this->matriz_phone).',';
		$sql .= ' '.(! isset($this->matriz_city)?'NULL':"'".$this->db->escape($this->matriz_city)."'").',';
		$sql .= ' '.(! isset($this->fk_user_create)?'NULL':$this->fk_user_create).',';
		$sql .= ' '.$user->id.',';
		$sql .= ' '.(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':"'".$this->db->idate($this->date_create)."'").',';
		$sql .= ' '.(! isset($this->date_mod) || dol_strlen($this->date_mod)==0?'NULL':"'".$this->db->idate($this->date_mod)."'").',';
		$sql .= ' '.(! isset($this->status)?'NULL':"'".$this->db->escape($this->status)."'");


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

		$sql .= " t.entity,";
		$sql .= " t.ref,";
		$sql .= " t.label,";
		$sql .= " t.subsidiary_number,";
		$sql .= " t.subsidiary_matriz,";
		$sql .= " t.socialreason,";
		$sql .= " t.nit,";
		$sql .= " t.activity,";
		$sql .= " t.address,";
		$sql .= " t.city,";
		$sql .= " t.phone,";
		$sql .= " t.serie,";
		$sql .= " t.message,";
		$sql .= " t.matriz_name,";
		$sql .= " t.matriz_address,";
		$sql .= " t.matriz_phone,";
		$sql .= " t.matriz_city,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.date_create,";
		$sql .= " t.date_mod,";
		$sql .= " t.tms,";
		$sql .= " t.status";


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

				$this->entity = $obj->entity;
				$this->ref = $obj->ref;
				$this->label = $obj->label;
				$this->subsidiary_number = $obj->subsidiary_number;
				$this->subsidiary_matriz = $obj->subsidiary_matriz;
				$this->socialreason = $obj->socialreason;
				$this->nit = $obj->nit;
				$this->activity = $obj->activity;
				$this->address = $obj->address;
				$this->city = $obj->city;
				$this->phone = $obj->phone;
				$this->serie = $obj->serie;
				$this->message = $obj->message;
				$this->matriz_name = $obj->matriz_name;
				$this->matriz_address = $obj->matriz_address;
				$this->matriz_phone = $obj->matriz_phone;
				$this->matriz_city = $obj->matriz_city;
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_mod = $obj->fk_user_mod;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->date_mod = $this->db->jdate($obj->date_mod);
				$this->tms = $this->db->jdate($obj->tms);
				$this->status = $obj->status;


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
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND', $filterstatic='', $lView=false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.entity,";
		$sql .= " t.ref,";
		$sql .= " t.label,";
		$sql .= " t.subsidiary_number,";
		$sql .= " t.subsidiary_matriz,";
		$sql .= " t.socialreason,";
		$sql .= " t.nit,";
		$sql .= " t.activity,";
		$sql .= " t.address,";
		$sql .= " t.city,";
		$sql .= " t.phone,";
		$sql .= " t.serie,";
		$sql .= " t.message,";
		$sql .= " t.matriz_name,";
		$sql .= " t.matriz_address,";
		$sql .= " t.matriz_phone,";
		$sql .= " t.matriz_city,";
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
		if (count($sqlwhere) > 0) {
			$sql .= ' WHERE ' . implode(' '.$filtermode.' ', $sqlwhere);
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
				$line = new SubsidiaryLine();

				$line->id = $obj->rowid;

				$line->entity = $obj->entity;
				$line->ref = $obj->ref;
				$line->label = $obj->label;
				$line->subsidiary_number = $obj->subsidiary_number;
				$line->subsidiary_matriz = $obj->subsidiary_matriz;
				$line->socialreason = $obj->socialreason;
				$line->nit = $obj->nit;
				$line->activity = $obj->activity;
				$line->address = $obj->address;
				$line->city = $obj->city;
				$line->phone = $obj->phone;
				$line->serie = $obj->serie;
				$line->message = $obj->message;
				$line->matriz_name = $obj->matriz_name;
				$line->matriz_address = $obj->matriz_address;
				$line->matriz_phone = $obj->matriz_phone;
				$line->matriz_city = $obj->matriz_city;
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->date_mod = $this->db->jdate($obj->date_mod);
				$line->tms = $this->db->jdate($obj->tms);
				$line->status = $obj->status;

				if ($lView)
				{
					if ($num == 1) $this->fetch($obj->rowid);
				}

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

		if (isset($this->entity)) {
			 $this->entity = trim($this->entity);
		}
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->label)) {
			 $this->label = trim($this->label);
		}
		if (isset($this->subsidiary_number)) {
			 $this->subsidiary_number = trim($this->subsidiary_number);
		}
		if (isset($this->subsidiary_matriz)) {
			 $this->subsidiary_matriz = trim($this->subsidiary_matriz);
		}
		if (isset($this->socialreason)) {
			 $this->socialreason = trim($this->socialreason);
		}
		if (isset($this->nit)) {
			 $this->nit = trim($this->nit);
		}
		if (isset($this->activity)) {
			 $this->activity = trim($this->activity);
		}
		if (isset($this->address)) {
			 $this->address = trim($this->address);
		}
		if (isset($this->city)) {
			 $this->city = trim($this->city);
		}
		if (isset($this->phone)) {
			 $this->phone = trim($this->phone);
		}
		if (isset($this->serie)) {
			 $this->serie = trim($this->serie);
		}
		if (isset($this->message)) {
			 $this->message = trim($this->message);
		}
		if (isset($this->matriz_name)) {
			 $this->matriz_name = trim($this->matriz_name);
		}
		if (isset($this->matriz_address)) {
			 $this->matriz_address = trim($this->matriz_address);
		}
		if (isset($this->matriz_phone)) {
			 $this->matriz_phone = trim($this->matriz_phone);
		}
		if (isset($this->matriz_city)) {
			 $this->matriz_city = trim($this->matriz_city);
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

		$sql .= ' entity = '.(isset($this->entity)?$this->entity:"null").',';
		$sql .= ' ref = '.(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").',';
		$sql .= ' label = '.(isset($this->label)?"'".$this->db->escape($this->label)."'":"null").',';
		$sql .= ' subsidiary_number = '.(isset($this->subsidiary_number)?"'".$this->db->escape($this->subsidiary_number)."'":"null").',';
		$sql .= ' subsidiary_matriz = '.(isset($this->subsidiary_matriz)?$this->subsidiary_matriz:"null").',';
		$sql .= ' socialreason = '.(isset($this->socialreason)?"'".$this->db->escape($this->socialreason)."'":"null").',';
		$sql .= ' nit = '.(isset($this->nit)?"'".$this->db->escape($this->nit)."'":"null").',';
		$sql .= ' activity = '.(isset($this->activity)?"'".$this->db->escape($this->activity)."'":"null").',';
		$sql .= ' address = '.(isset($this->address)?"'".$this->db->escape($this->address)."'":"null").',';
		$sql .= ' city = '.(isset($this->city)?"'".$this->db->escape($this->city)."'":"null").',';
		$sql .= ' phone = '.(isset($this->phone)?"'".$this->db->escape($this->phone)."'":"null").',';
		$sql .= ' serie = '.(isset($this->serie)?"'".$this->db->escape($this->serie)."'":"null").',';
		$sql .= ' message = '.(isset($this->message)?"'".$this->db->escape($this->message)."'":"null").',';
		$sql .= ' matriz_name = '.(isset($this->matriz_name)?"'".$this->db->escape($this->matriz_name)."'":"null").',';
		$sql .= ' matriz_address = '.(isset($this->matriz_address)?"'".$this->db->escape($this->matriz_address)."'":"null").',';
		$sql .= ' matriz_phone = '.(isset($this->matriz_phone)?$this->matriz_phone:"null").',';
		$sql .= ' matriz_city = '.(isset($this->matriz_city)?"'".$this->db->escape($this->matriz_city)."'":"null").',';
		$sql .= ' fk_user_create = '.(isset($this->fk_user_create)?$this->fk_user_create:"null").',';
		$sql .= ' fk_user_mod = '.$user->id.',';
		$sql .= ' date_create = '.(! isset($this->date_create) || dol_strlen($this->date_create) != 0 ? "'".$this->db->idate($this->date_create)."'" : 'null').',';
		$sql .= ' date_mod = '.(! isset($this->date_mod) || dol_strlen($this->date_mod) != 0 ? "'".$this->db->idate($this->date_mod)."'" : 'null').',';
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
		$sql .= ' status = '.(isset($this->status)?"'".$this->db->escape($this->status)."'":"null");


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
		$object = new Subsidiary($this->db);

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

        $label = '<u>' . $langs->trans("Subsidiary") . '</u>';
        $label.= '<div width="100%">';
        $label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

        $link = '<a href="'.DOL_URL_ROOT.'/fiscal/subsidiary/card.php?id='.$this->id.'"';
        $link.= ($notooltip?'':' title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip'.($morecss?' '.$morecss:'').'"');
        $link.= '>';
		$linkend='</a>';

        if ($withpicto)
        {
            $result.=($link.img_picto(($notooltip?'':$label), DOL_URL_ROOT.'/fiscal/img/subsidiary.png', ($notooltip?'':'class="classfortooltip"'),1).$linkend);
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

		$this->entity = '';
		$this->ref = '';
		$this->label = '';
		$this->subsidiary_number = '';
		$this->subsidiary_matriz = '';
		$this->socialreason = '';
		$this->nit = '';
		$this->activity = '';
		$this->address = '';
		$this->city = '';
		$this->phone = '';
		$this->serie = '';
		$this->message = '';
		$this->matriz_name = '';
		$this->matriz_address = '';
		$this->matriz_phone = '';
		$this->matriz_city = '';
		$this->fk_user_create = '';
		$this->fk_user_mod = '';
		$this->date_create = '';
		$this->date_mod = '';
		$this->tms = '';
		$this->status = '';


	}

}

/**
 * Class SubsidiaryLine
 */
class SubsidiaryLine
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
	public $label;
	public $subsidiary_number;
	public $subsidiary_matriz;
	public $socialreason;
	public $nit;
	public $activity;
	public $address;
	public $city;
	public $phone;
	public $serie;
	public $message;
	public $matriz_name;
	public $matriz_address;
	public $matriz_phone;
	public $matriz_city;
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
