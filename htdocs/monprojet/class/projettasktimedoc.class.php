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
 * \file    /projettasktimedoc.class.php
 * \ingroup
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Projettasktimedoc
 *
 * Put here description of your class
 */
class Projettasktimedoc extends CommonObject
{
	/**
	 * @var string Error code (or message)
	 * @deprecated
	 * @see Projettasktimedoc::errors
	 */
	public $error;
	/**
	 * @var string[] Error codes (or messages)
	 */
	public $errors = array();
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'projettasktimedoc';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'projet_task_time_doc';
	public $table_element_sup = 'projet_task_time';

	/**
	 * @var ProjettasktimedocLine[] Lines
	 */
	public $lines = array();

	/**
	 * @var int ID
	 */
	public $id;
	/**
	 */

	public $fk_task_time;
	public $fk_task_payment;
	public $fk_request_item;
	public $document;
	public $unit_declared;
	public $fk_user_create;
	public $date_create = '';
	public $fk_user_mod;
	public $tms = '';
	public $date_mod = '';
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

		if (isset($this->fk_task_time)) {
			$this->fk_task_time = trim($this->fk_task_time);
		}
		if (isset($this->fk_task_payment)) {
			$this->fk_task_payment = trim($this->fk_task_payment);
		}
		if (isset($this->fk_request_item)) {
			$this->fk_request_item = trim($this->fk_request_item);
		}
		if (isset($this->document)) {
			$this->document = trim($this->document);
		}
		if (isset($this->unit_declared)) {
			$this->unit_declared = trim($this->unit_declared);
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

		$sql.= 'fk_task_time,';
		$sql.= 'fk_task_payment,';
		$sql.= 'fk_request_item,';
		$sql.= 'document,';
		$sql.= 'unit_declared,';
		$sql.= 'fk_user_create,';
		$sql.= 'date_create,';
		$sql.= 'fk_user_mod,';
		$sql.= 'date_mod,';
		$sql.= 'statut';


		$sql .= ') VALUES (';

		$sql .= ' '.(! isset($this->fk_task_time)?'NULL':$this->fk_task_time).',';
		$sql .= ' '.(! isset($this->fk_task_payment)?'NULL':$this->fk_task_payment).',';
		$sql .= ' '.(! isset($this->fk_request_item)?'NULL':$this->fk_request_item).',';
		$sql .= ' '.(! isset($this->document)?'NULL':"'".$this->db->escape($this->document)."'").',';
		$sql .= ' '.(! isset($this->unit_declared)?'NULL':"'".$this->unit_declared."'").',';
		$sql .= ' '.(! isset($this->fk_user_create)?'NULL':$this->fk_user_create).',';
		$sql .= ' '.(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':"'".$this->db->idate($this->date_create)."'").',';
		$sql .= ' '.$user->id.',';
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

		$sql .= " t.fk_task_time,";
		$sql .= " t.fk_task_payment,";
		$sql .= " t.fk_request_item,";
		$sql .= " t.document,";
		$sql .= " t.unit_declared,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.date_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.tms,";
		$sql .= " t.date_mod,";
		$sql .= " t.statut";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		if (null !== $ref) {
			$sql .= ' WHERE t.fk_task_time = ' . $ref;
		} else {
			$sql .= ' WHERE t.rowid = ' . $id;
		}

		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			if ($numrows) {
				$obj = $this->db->fetch_object($resql);

				$this->id = $obj->rowid;

				$this->fk_task_time = $obj->fk_task_time;
				$this->fk_task_payment = $obj->fk_task_payment;
				$this->fk_request_item = $obj->fk_request_item;
				$this->document = $obj->document;
				$this->unit_declared = $obj->unit_declared;
				$this->fk_user_create = $obj->fk_user_create;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->fk_user_mod = $obj->fk_user_mod;
				$this->tms = $this->db->jdate($obj->tms);
				$this->date_mod = $this->db->jdate($obj->date_mod);
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

		$sql .= " t.fk_task_time,";
		$sql .= " t.fk_task_payment,";
		$sql .= " t.fk_request_item,";
		$sql .= " t.document,";
		$sql .= " t.unit_declared,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.date_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.tms,";
		$sql .= " t.date_mod,";
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
				$line = new ProjettasktimedocLine();

				$line->id = $obj->rowid;

				$line->fk_task_time = $obj->fk_task_time;
				$line->fk_task_payment = $obj->fk_task_payment;
				$line->fk_request_item = $obj->fk_request_item;
				$line->document = $obj->document;
				$line->unit_declared = $obj->unit_declared;
				$line->fk_user_create = $obj->fk_user_create;
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->tms = $this->db->jdate($obj->tms);
				$line->date_mod = $this->db->jdate($obj->date_mod);
				$line->statut = $obj->statut;

				if ($lRow)
				{
					$this->id = $obj->rowid;

					$this->fk_task_time = $obj->fk_task_time;
					$this->fk_task_payment = $obj->fk_task_payment;
					$this->fk_request_item = $obj->fk_request_item;
					$this->document = $obj->document;
					$this->unit_declared = $obj->unit_declared;
					$this->fk_user_create = $obj->fk_user_create;
					$this->date_create = $this->db->jdate($obj->date_create);
					$this->fk_user_mod = $obj->fk_user_mod;
					$this->tms = $this->db->jdate($obj->tms);
					$this->date_mod = $this->db->jdate($obj->date_mod);
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

		if (isset($this->fk_task_time)) {
			$this->fk_task_time = trim($this->fk_task_time);
		}
		if (isset($this->fk_task_payment)) {
			$this->fk_task_payment = trim($this->fk_task_payment);
		}
		if (isset($this->fk_request_item)) {
			$this->fk_request_item = trim($this->fk_request_item);
		}
		if (isset($this->document)) {
			$this->document = trim($this->document);
		}
		if (isset($this->unit_declared)) {
			$this->unit_declared = trim($this->unit_declared);
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

		$sql .= ' fk_task_time = '.(isset($this->fk_task_time)?$this->fk_task_time:"null").',';
		$sql .= ' fk_task_payment = '.(isset($this->fk_task_payment)?$this->fk_task_payment:"null").',';
		$sql .= ' fk_request_item = '.(isset($this->fk_request_item)?$this->fk_request_item:"null").',';
		$sql .= ' document = '.(isset($this->document)?"'".$this->db->escape($this->document)."'":"null").',';
		$sql .= ' unit_declared = '.(isset($this->unit_declared)?$this->unit_declared:"null").',';
		$sql .= ' fk_user_create = '.(isset($this->fk_user_create)?$this->fk_user_create:"null").',';
		$sql .= ' date_create = '.(! isset($this->date_create) || dol_strlen($this->date_create) != 0 ? "'".$this->db->idate($this->date_create)."'" : 'null').',';
		$sql .= ' fk_user_mod = '.$user->id.',';
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
		$sql .= ' date_mod = '.(! isset($this->date_mod) || dol_strlen($this->date_mod) != 0 ? "'".$this->db->idate($this->date_mod)."'" : 'null').',';
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
		$object = new Projettasktimedoc($this->db);

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

		$this->fk_task_time = '';
		$this->fk_task_payment = '';
		$this->fk_request_item = '';
		$this->document = '';
		$this->unit_declared = '';
		$this->fk_user_create = '';
		$this->date_create = '';
		$this->fk_user_mod = '';
		$this->tms = '';
		$this->date_mod = '';
		$this->statut = '';


	}

	//MODIFICADO
	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    	Id object
	 *  @param	string	$ref	Ref
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getsum($id,$fk_payment=0,$statut=0,$fk_requestitem=0,$date='')
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " SUM(t.unit_declared) as total";
		$sql.= " FROM ".MAIN_DB_PREFIX.$this->table_element." as t";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."projet_task_time AS p ON t.fk_task_time = p.rowid";
		$sql.= " WHERE p.fk_task = ".$id;
		if ($fk_payment)
			$sql.= " AND t.fk_task_payment = ".$fk_payment;
		if ($fk_requestitem)
			$sql.= " AND t.fk_request_item = ".$fk_requestitem;
		if ($statut >0)
	  //caso contrario se suma todo
			$sql.= " AND t.statut = ".$statut;
		if (!empty($date))
	  //hasta fecha, caso contrario todo
			$sql.= " AND t.date_create <= ".$date;
	  //echo '<hr>'.$sql;
		dol_syslog(get_class($this)."::getsum");
		$resql=$this->db->query($sql);
		$this->total = 0;
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);

				$this->total = $obj->total;
			}
			$this->db->free($resql);

			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    	Id object
	 *  @param	string	$ref	Ref
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function last_advance($id,$fk_payment=0,$statut=1,$order=' ORDER BY t.date_create DESC')
	{
		global $langs;
		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.fk_task_time,";
		$sql .= " t.fk_task_payment,";
		$sql .= " t.fk_request_item,";
		$sql .= " t.document,";
		$sql .= " t.unit_declared,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.date_create,";
		$sql .= " t.tms,";
		$sql .= " t.statut, ";
		$sql .= " p.task_date ";
		$sql .= " FROM ".MAIN_DB_PREFIX.$this->table_element." as t";
		$sql .= " INNER JOIN ".MAIN_DB_PREFIX."projet_task_time AS p ON t.fk_task_time = p.rowid";
		$sql.= " WHERE p.fk_task = ".$id;
		if ($fk_payment)
			$sql.= " AND t.fk_payment = ".$fk_payment;
	  if ($statut >=0) //caso contrario se suma todo
	  $sql.= " AND t.statut = ".$statut;
	  $sql .= $order;
	  dol_syslog(get_class($this)."::last_advance");
	  $resql=$this->db->query($sql);
	  $this->total = 0;
	  if ($resql)
	  {
	  	if ($this->db->num_rows($resql))
	  	{
	  		$obj = $this->db->fetch_object($resql);

	  		$this->id = $obj->rowid;

	  		$this->fk_task_time = $obj->fk_task_time;
	  		$this->fk_task_payment = $obj->fk_task_payment;
	  		$this->fk_request_item = $obj->fk_request_item;
	  		$this->document = $obj->document;
	  		$this->unit_declared = $obj->unit_declared;
	  		$this->fk_user_create = $obj->fk_user_create;
	  		$this->date_create = $this->db->jdate($obj->date_create);
	  		$this->tms = $this->db->jdate($obj->tms);
	  		$this->statut = $obj->statut;
	  		$this->task_date = $this->db->jdate($obj->task_date);
	  	}
	  	$this->db->free($resql);
	  	return 1;
	  }
	  else
	  {
	  	$this->error="Error ".$this->db->lasterror();
	  	return -1;
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
	public function getadvance($id, $filter='')
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.statut,';

		$sql .= " SUM(t.unit_declared) AS advance";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql .= ' INNER JOIN ' . MAIN_DB_PREFIX . $this->table_element_sup . ' as r ON t.fk_task_time = r.rowid';
		$sql .= " WHERE r.fk_task = " . $id;
		$sql .= " GROUP BY t.statut";

		$resql = $this->db->query($sql);
		$this->aArray = array();
		if ($resql) 
		{
			$numrows = $this->db->num_rows($resql);
			if ($numrows) {
				while ($obj = $this->db->fetch_object($resql))
				{
					$this->aArray[$obj->statut] = $obj->advance;
				}
			}
			$this->db->free($resql);

			if ($numrows) {
				return 1;
			} else {
				return 0;
			}
		} 
		else 
		{
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return - 1;
		}
	}

	/**
	 * Load object in memory from the database
	 *
	 * @param int    $id  fk_task
	 * @param string $filter Filter
	 *
	 * @return int <0 if KO, 0 if not found, >0 if OK
	 */
	public function update_approve($id, $statut, $filter='')
	{
		global $conf,$user;
		dol_syslog(__METHOD__, LOG_DEBUG);
		if (empty($id) && empty($statut) || (empty($id) || empty($statut)))
			return -1;

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.fk_task_time,";
		$sql .= " t.fk_task_payment,";
		$sql .= " t.document,";
		$sql .= " t.unit_declared,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.date_create,";
		$sql .= " t.tms,";
		$sql .= " t.statut ";
		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql .= ' INNER JOIN ' . MAIN_DB_PREFIX . $this->table_element_sup . ' as r ON t.fk_task_time = r.rowid';
		$sql .= " WHERE r.fk_task = " . $id;

		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			if ($numrows) {
				$this->db->begin();
				while ($obj = $this->db->fetch_object($resql))
				{
		  //actualizamos
					$newobj = new Projettasktimedoc($this->db);
					$newobj->fetch($obj->rowid);
					if ($newobj->id == $obj->rowid)
					{
						$newobj->statut = $statut;
						$newobj->fk_user_mod = $user->id;
						$newobj->date_mod = dol_now();
						$res = $newobj->update($user);
						if (!$res>0)
							$error++;
					}
					else
						$error++;
				}
				if (empty($error))
					$this->db->commit();
				else
					$this->db->rollback();
			}
			$this->db->free($resql);

			if (empty($error)) {
				return 1;
			} else {
				return -1;
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
	public function fetchAlltime($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lRow=false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.fk_task_time,";
		$sql .= " t.fk_task_payment,";
		$sql .= " t.fk_request_item,";
		$sql .= " t.document,";
		$sql .= " t.unit_declared,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.date_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.tms,";
		$sql .= " t.date_mod,";
		$sql .= " t.statut";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element. ' as t';
		$sql .= ' INNER JOIN ' . MAIN_DB_PREFIX . 'projet_task_time'. ' as p ON t.fk_task_time = p.rowid';

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
		//echo $sql;
		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new ProjettasktimedocLine();

				$line->id = $obj->rowid;

				$line->fk_task_time = $obj->fk_task_time;
				$line->fk_task_payment = $obj->fk_task_payment;
				$line->fk_request_item = $obj->fk_request_item;
				$line->document = $obj->document;
				$line->unit_declared = $obj->unit_declared;
				$line->fk_user_create = $obj->fk_user_create;
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->tms = $this->db->jdate($obj->tms);
				$line->date_mod = $this->db->jdate($obj->date_mod);
				$line->statut = $obj->statut;

				if ($lRow)
				{
					$this->id = $obj->rowid;

					$this->fk_task_time = $obj->fk_task_time;
					$this->fk_task_payment = $obj->fk_task_payment;
					$this->fk_request_item = $obj->fk_request_item;
					$this->document = $obj->document;
					$this->unit_declared = $obj->unit_declared;
					$this->fk_user_create = $obj->fk_user_create;
					$this->date_create = $this->db->jdate($obj->date_create);
					$this->fk_user_mod = $obj->fk_user_mod;
					$this->tms = $this->db->jdate($obj->tms);
					$this->date_mod = $this->db->jdate($obj->date_mod);
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

}

/**
 * Class ProjettasktimedocLine
 */
class ProjettasktimedocLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */

	public $fk_task_time;
	public $fk_task_payment;
	public $fk_request_item;
	public $document;
	public $unit_declared;
	public $fk_user_create;
	public $date_create = '';
	public $fk_user_mod;
	public $tms = '';
	public $date_mod = '';
	public $statut;

	/**
	 * @var mixed Sample line property 2
	 */

}
