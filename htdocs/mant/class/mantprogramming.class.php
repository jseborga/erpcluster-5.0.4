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
 * \file    mant/mantprogramming.class.php
 * \ingroup mant
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Mantprogramming
 *
 * Put here description of your class
 *
 * @see CommonObject
 */
class Mantprogramming extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'mantprogramming';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'mant_programming';

	/**
	 * @var MantprogrammingLine[] Lines
	 */
	public $lines = array();

	/**
	 */

	public $entity;
	public $fk_asset;
	public $fk_equipment;
	public $fk_soc;
	public $fk_member;
	public $internal;
	public $speciality;
	public $typemant;
	public $frequency;
	public $detail_value;
	public $description;
	public $date_ini = '';
	public $date_last = '';
	public $date_next = '';
	public $date_create = '';
	public $fk_user_create;
	public $fk_user_mod;
	public $datec = '';
	public $datem = '';
	public $active;
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
		if (isset($this->fk_asset)) {
			 $this->fk_asset = trim($this->fk_asset);
		}
		if (isset($this->fk_equipment)) {
			 $this->fk_equipment = trim($this->fk_equipment);
		}
		if (isset($this->fk_soc)) {
			 $this->fk_soc = trim($this->fk_soc);
		}
		if (isset($this->fk_member)) {
			 $this->fk_member = trim($this->fk_member);
		}
		if (isset($this->internal)) {
			 $this->internal = trim($this->internal);
		}
		if (isset($this->speciality)) {
			 $this->speciality = trim($this->speciality);
		}
		if (isset($this->typemant)) {
			 $this->typemant = trim($this->typemant);
		}
		if (isset($this->frequency)) {
			 $this->frequency = trim($this->frequency);
		}
		if (isset($this->detail_value)) {
			 $this->detail_value = trim($this->detail_value);
		}
		if (isset($this->description)) {
			 $this->description = trim($this->description);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_mod)) {
			 $this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->active)) {
			 $this->active = trim($this->active);
		}
		if (isset($this->statut)) {
			 $this->statut = trim($this->statut);
		}



		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';

		$sql.= 'entity,';
		$sql.= 'fk_asset,';
		$sql.= 'fk_equipment,';
		$sql.= 'fk_soc,';
		$sql.= 'fk_member,';
		$sql.= 'internal,';
		$sql.= 'speciality,';
		$sql.= 'typemant,';
		$sql.= 'frequency,';
		$sql.= 'detail_value,';
		$sql.= 'description,';
		$sql.= 'date_ini,';
		$sql.= 'date_last,';
		$sql.= 'date_next,';
		$sql.= 'date_create,';
		$sql.= 'fk_user_create,';
		$sql.= 'fk_user_mod,';
		$sql.= 'datec,';
		$sql.= 'datem,';
		$sql.= 'active,';
		$sql.= 'statut';


		$sql .= ') VALUES (';

		$sql .= ' '.(! isset($this->entity)?'NULL':$this->entity).',';
		$sql .= ' '.(! isset($this->fk_asset)?'NULL':$this->fk_asset).',';
		$sql .= ' '.(! isset($this->fk_equipment)?'NULL':$this->fk_equipment).',';
		$sql .= ' '.(! isset($this->fk_soc)?'NULL':$this->fk_soc).',';
		$sql .= ' '.(! isset($this->fk_member)?'NULL':$this->fk_member).',';
		$sql .= ' '.(! isset($this->internal)?'NULL':$this->internal).',';
		$sql .= ' '.(! isset($this->speciality)?'NULL':"'".$this->db->escape($this->speciality)."'").',';
		$sql .= ' '.(! isset($this->typemant)?'NULL':"'".$this->db->escape($this->typemant)."'").',';
		$sql .= ' '.(! isset($this->frequency)?'NULL':"'".$this->db->escape($this->frequency)."'").',';
		$sql .= ' '.(! isset($this->detail_value)?'NULL':"'".$this->db->escape($this->detail_value)."'").',';
		$sql .= ' '.(! isset($this->description)?'NULL':"'".$this->db->escape($this->description)."'").',';
		$sql .= ' '.(! isset($this->date_ini) || dol_strlen($this->date_ini)==0?'NULL':"'".$this->db->idate($this->date_ini)."'").',';
		$sql .= ' '.(! isset($this->date_last) || dol_strlen($this->date_last)==0?'NULL':"'".$this->db->idate($this->date_last)."'").',';
		$sql .= ' '.(! isset($this->date_next) || dol_strlen($this->date_next)==0?'NULL':"'".$this->db->idate($this->date_next)."'").',';
		$sql .= ' '.(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':"'".$this->db->idate($this->date_create)."'").',';
		$sql .= ' '.(! isset($this->fk_user_create)?'NULL':$this->fk_user_create).',';
		$sql .= ' '.$user->id.',';
		$sql .= ' '."'".$this->db->idate(dol_now())."'".',';
		$sql .= ' '.(! isset($this->datem) || dol_strlen($this->datem)==0?'NULL':"'".$this->db->idate($this->datem)."'").',';
		$sql .= ' '.(! isset($this->active)?'NULL':$this->active).',';
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
		$sql .= " t.fk_asset,";
		$sql .= " t.fk_equipment,";
		$sql .= " t.fk_soc,";
		$sql .= " t.fk_member,";
		$sql .= " t.internal,";
		$sql .= " t.speciality,";
		$sql .= " t.typemant,";
		$sql .= " t.frequency,";
		$sql .= " t.detail_value,";
		$sql .= " t.description,";
		$sql .= " t.date_ini,";
		$sql .= " t.date_last,";
		$sql .= " t.date_next,";
		$sql .= " t.date_create,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.datec,";
		$sql .= " t.datem,";
		$sql .= " t.active,";
		$sql .= " t.tms,";
		$sql .= " t.statut";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
		    $sql .= " AND entity IN (" . getEntity("mantprogramming", 1) . ")";
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
				$this->fk_asset = $obj->fk_asset;
				$this->fk_equipment = $obj->fk_equipment;
				$this->fk_soc = $obj->fk_soc;
				$this->fk_member = $obj->fk_member;
				$this->internal = $obj->internal;
				$this->speciality = $obj->speciality;
				$this->typemant = $obj->typemant;
				$this->frequency = $obj->frequency;
				$this->detail_value = $obj->detail_value;
				$this->description = $obj->description;
				$this->date_ini = $this->db->jdate($obj->date_ini);
				$this->date_last = $this->db->jdate($obj->date_last);
				$this->date_next = $this->db->jdate($obj->date_next);
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_mod = $obj->fk_user_mod;
				$this->datec = $this->db->jdate($obj->datec);
				$this->datem = $this->db->jdate($obj->datem);
				$this->active = $obj->active;
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
		$sql .= " t.fk_asset,";
		$sql .= " t.fk_equipment,";
		$sql .= " t.fk_soc,";
		$sql .= " t.fk_member,";
		$sql .= " t.internal,";
		$sql .= " t.speciality,";
		$sql .= " t.typemant,";
		$sql .= " t.frequency,";
		$sql .= " t.detail_value,";
		$sql .= " t.description,";
		$sql .= " t.date_ini,";
		$sql .= " t.date_last,";
		$sql .= " t.date_next,";
		$sql .= " t.date_create,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.datec,";
		$sql .= " t.datem,";
		$sql .= " t.active,";
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
		    $sql .= " AND entity IN (" . getEntity("mantprogramming", 1) . ")";
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
				$line = new MantprogrammingLine();

				$line->id = $obj->rowid;

				$line->entity = $obj->entity;
				$line->fk_asset = $obj->fk_asset;
				$line->fk_equipment = $obj->fk_equipment;
				$line->fk_soc = $obj->fk_soc;
				$line->fk_member = $obj->fk_member;
				$line->internal = $obj->internal;
				$line->speciality = $obj->speciality;
				$line->typemant = $obj->typemant;
				$line->frequency = $obj->frequency;
				$line->detail_value = $obj->detail_value;
				$line->description = $obj->description;
				$line->date_ini = $this->db->jdate($obj->date_ini);
				$line->date_last = $this->db->jdate($obj->date_last);
				$line->date_next = $this->db->jdate($obj->date_next);
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->datec = $this->db->jdate($obj->datec);
				$line->datem = $this->db->jdate($obj->datem);
				$line->active = $obj->active;
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
		if (isset($this->fk_asset)) {
			 $this->fk_asset = trim($this->fk_asset);
		}
		if (isset($this->fk_equipment)) {
			 $this->fk_equipment = trim($this->fk_equipment);
		}
		if (isset($this->fk_soc)) {
			 $this->fk_soc = trim($this->fk_soc);
		}
		if (isset($this->fk_member)) {
			 $this->fk_member = trim($this->fk_member);
		}
		if (isset($this->internal)) {
			 $this->internal = trim($this->internal);
		}
		if (isset($this->speciality)) {
			 $this->speciality = trim($this->speciality);
		}
		if (isset($this->typemant)) {
			 $this->typemant = trim($this->typemant);
		}
		if (isset($this->frequency)) {
			 $this->frequency = trim($this->frequency);
		}
		if (isset($this->detail_value)) {
			 $this->detail_value = trim($this->detail_value);
		}
		if (isset($this->description)) {
			 $this->description = trim($this->description);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_mod)) {
			 $this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->active)) {
			 $this->active = trim($this->active);
		}
		if (isset($this->statut)) {
			 $this->statut = trim($this->statut);
		}



		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';

		$sql .= ' entity = '.(isset($this->entity)?$this->entity:"null").',';
		$sql .= ' fk_asset = '.(isset($this->fk_asset)?$this->fk_asset:"null").',';
		$sql .= ' fk_equipment = '.(isset($this->fk_equipment)?$this->fk_equipment:"null").',';
		$sql .= ' fk_soc = '.(isset($this->fk_soc)?$this->fk_soc:"null").',';
		$sql .= ' fk_member = '.(isset($this->fk_member)?$this->fk_member:"null").',';
		$sql .= ' internal = '.(isset($this->internal)?$this->internal:"null").',';
		$sql .= ' speciality = '.(isset($this->speciality)?"'".$this->db->escape($this->speciality)."'":"null").',';
		$sql .= ' typemant = '.(isset($this->typemant)?"'".$this->db->escape($this->typemant)."'":"null").',';
		$sql .= ' frequency = '.(isset($this->frequency)?"'".$this->db->escape($this->frequency)."'":"null").',';
		$sql .= ' detail_value = '.(isset($this->detail_value)?"'".$this->db->escape($this->detail_value)."'":"null").',';
		$sql .= ' description = '.(isset($this->description)?"'".$this->db->escape($this->description)."'":"null").',';
		$sql .= ' date_ini = '.(! isset($this->date_ini) || dol_strlen($this->date_ini) != 0 ? "'".$this->db->idate($this->date_ini)."'" : 'null').',';
		$sql .= ' date_last = '.(! isset($this->date_last) || dol_strlen($this->date_last) != 0 ? "'".$this->db->idate($this->date_last)."'" : 'null').',';
		$sql .= ' date_next = '.(! isset($this->date_next) || dol_strlen($this->date_next) != 0 ? "'".$this->db->idate($this->date_next)."'" : 'null').',';
		$sql .= ' date_create = '.(! isset($this->date_create) || dol_strlen($this->date_create) != 0 ? "'".$this->db->idate($this->date_create)."'" : 'null').',';
		$sql .= ' fk_user_create = '.(isset($this->fk_user_create)?$this->fk_user_create:"null").',';
		$sql .= ' fk_user_mod = '.$user->id.',';
		$sql .= ' datec = '.(! isset($this->datec) || dol_strlen($this->datec) != 0 ? "'".$this->db->idate($this->datec)."'" : 'null').',';
		$sql .= ' datem = '.(! isset($this->datem) || dol_strlen($this->datem) != 0 ? "'".$this->db->idate($this->datem)."'" : 'null').',';
		$sql .= ' active = '.(isset($this->active)?$this->active:"null").',';
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
		$object = new Mantprogramming($this->db);

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

        $url = DOL_URL_ROOT.'/mant/'.$this->table_name.'_card.php?id='.$this->id;

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
		$this->fk_asset = '';
		$this->fk_equipment = '';
		$this->fk_soc = '';
		$this->fk_member = '';
		$this->internal = '';
		$this->speciality = '';
		$this->typemant = '';
		$this->frequency = '';
		$this->detail_value = '';
		$this->description = '';
		$this->date_ini = '';
		$this->date_last = '';
		$this->date_next = '';
		$this->date_create = '';
		$this->fk_user_create = '';
		$this->fk_user_mod = '';
		$this->datec = '';
		$this->datem = '';
		$this->active = '';
		$this->tms = '';
		$this->statut = '';


	}

}

/**
 * Class MantprogrammingLine
 */
class MantprogrammingLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */

	public $entity;
	public $fk_asset;
	public $fk_equipment;
	public $fk_soc;
	public $fk_member;
	public $internal;
	public $speciality;
	public $typemant;
	public $frequency;
	public $detail_value;
	public $description;
	public $date_ini = '';
	public $date_last = '';
	public $date_next = '';
	public $date_create = '';
	public $fk_user_create;
	public $fk_user_mod;
	public $datec = '';
	public $datem = '';
	public $active;
	public $tms = '';
	public $statut;

	/**
	 * @var mixed Sample line property 2
	 */

}
