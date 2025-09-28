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
 * \file    assets/assetsassignment.class.php
 * \ingroup assets
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Assetsassignment
 *
 * Put here description of your class
 *
 * @see CommonObject
 */
class Assetsassignment extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'assetsassignment';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'assets_assignment';

	/**
	 * @var AssetsassignmentLine[] Lines
	 */
	public $lines = array();

	/**
	 */

	public $entity;
	public $ref;
	public $fk_user;
	public $fk_projet;
	public $fk_projet_from;
	public $fk_user_from;
	public $fk_property_from;
	public $fk_user_to;
	public $fk_property;
	public $fk_location;
	public $detail;
	public $date_assignment = '';
	public $type_assignment;
	public $date_create = '';
	public $date_mod = '';
	public $mark;
	public $fk_user_create;
	public $fk_user_mod;
	public $fk_user_approved;
	public $date_approved = '';
	public $model_pdf;
	public $origin;
	public $originid;
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
		if (isset($this->fk_user)) {
			 $this->fk_user = trim($this->fk_user);
		}
		if (isset($this->fk_projet)) {
			 $this->fk_projet = trim($this->fk_projet);
		}
		if (isset($this->fk_projet_from)) {
			 $this->fk_projet_from = trim($this->fk_projet_from);
		}
		if (isset($this->fk_user_from)) {
			 $this->fk_user_from = trim($this->fk_user_from);
		}
		if (isset($this->fk_property_from)) {
			 $this->fk_property_from = trim($this->fk_property_from);
		}
		if (isset($this->fk_user_to)) {
			 $this->fk_user_to = trim($this->fk_user_to);
		}
		if (isset($this->fk_property)) {
			 $this->fk_property = trim($this->fk_property);
		}
		if (isset($this->fk_location)) {
			 $this->fk_location = trim($this->fk_location);
		}
		if (isset($this->detail)) {
			 $this->detail = trim($this->detail);
		}
		if (isset($this->type_assignment)) {
			 $this->type_assignment = trim($this->type_assignment);
		}
		if (isset($this->mark)) {
			 $this->mark = trim($this->mark);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_mod)) {
			 $this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->fk_user_approved)) {
			 $this->fk_user_approved = trim($this->fk_user_approved);
		}
		if (isset($this->model_pdf)) {
			 $this->model_pdf = trim($this->model_pdf);
		}
		if (isset($this->origin)) {
			 $this->origin = trim($this->origin);
		}
		if (isset($this->originid)) {
			 $this->originid = trim($this->originid);
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
		$sql.= 'fk_user,';
		$sql.= 'fk_projet,';
		$sql.= 'fk_projet_from,';
		$sql.= 'fk_user_from,';
		$sql.= 'fk_property_from,';
		$sql.= 'fk_user_to,';
		$sql.= 'fk_property,';
		$sql.= 'fk_location,';
		$sql.= 'detail,';
		$sql.= 'date_assignment,';
		$sql.= 'type_assignment,';
		$sql.= 'date_create,';
		$sql.= 'date_mod,';
		$sql.= 'mark,';
		$sql.= 'fk_user_create,';
		$sql.= 'fk_user_mod,';
		$sql.= 'fk_user_approved,';
		$sql.= 'date_approved,';
		$sql.= 'model_pdf,';
		$sql.= 'origin,';
		$sql.= 'originid,';
		$sql.= 'status';


		$sql .= ') VALUES (';

		$sql .= ' '.(! isset($this->entity)?'NULL':$this->entity).',';
		$sql .= ' '.(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql .= ' '.(! isset($this->fk_user)?'NULL':$this->fk_user).',';
		$sql .= ' '.(! isset($this->fk_projet)?'NULL':$this->fk_projet).',';
		$sql .= ' '.(! isset($this->fk_projet_from)?'NULL':$this->fk_projet_from).',';
		$sql .= ' '.(! isset($this->fk_user_from)?'NULL':$this->fk_user_from).',';
		$sql .= ' '.(! isset($this->fk_property_from)?'NULL':$this->fk_property_from).',';
		$sql .= ' '.(! isset($this->fk_user_to)?'NULL':$this->fk_user_to).',';
		$sql .= ' '.(! isset($this->fk_property)?'NULL':$this->fk_property).',';
		$sql .= ' '.(! isset($this->fk_location)?'NULL':$this->fk_location).',';
		$sql .= ' '.(! isset($this->detail)?'NULL':"'".$this->db->escape($this->detail)."'").',';
		$sql .= ' '.(! isset($this->date_assignment) || dol_strlen($this->date_assignment)==0?'NULL':"'".$this->db->idate($this->date_assignment)."'").',';
		$sql .= ' '.(! isset($this->type_assignment)?'NULL':"'".$this->db->escape($this->type_assignment)."'").',';
		$sql .= ' '.(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':"'".$this->db->idate($this->date_create)."'").',';
		$sql .= ' '.(! isset($this->date_mod) || dol_strlen($this->date_mod)==0?'NULL':"'".$this->db->idate($this->date_mod)."'").',';
		$sql .= ' '.(! isset($this->mark)?'NULL':"'".$this->db->escape($this->mark)."'").',';
		$sql .= ' '.(! isset($this->fk_user_create)?'NULL':$this->fk_user_create).',';
		$sql .= ' '.$user->id.',';
		$sql .= ' '.(! isset($this->fk_user_approved)?'NULL':$this->fk_user_approved).',';
		$sql .= ' '.(! isset($this->date_approved) || dol_strlen($this->date_approved)==0?'NULL':"'".$this->db->idate($this->date_approved)."'").',';
		$sql .= ' '.(! isset($this->model_pdf)?'NULL':"'".$this->db->escape($this->model_pdf)."'").',';
		$sql .= ' '.(! isset($this->origin)?'NULL':"'".$this->db->escape($this->origin)."'").',';
		$sql .= ' '.(! isset($this->originid)?'NULL':$this->originid).',';
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

		$sql .= " t.entity,";
		$sql .= " t.ref,";
		$sql .= " t.fk_user,";
		$sql .= " t.fk_projet,";
		$sql .= " t.fk_projet_from,";
		$sql .= " t.fk_user_from,";
		$sql .= " t.fk_property_from,";
		$sql .= " t.fk_user_to,";
		$sql .= " t.fk_property,";
		$sql .= " t.fk_location,";
		$sql .= " t.detail,";
		$sql .= " t.date_assignment,";
		$sql .= " t.type_assignment,";
		$sql .= " t.date_create,";
		$sql .= " t.date_mod,";
		$sql .= " t.mark,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.fk_user_approved,";
		$sql .= " t.date_approved,";
		$sql .= " t.model_pdf,";
		$sql .= " t.origin,";
		$sql .= " t.originid,";
		$sql .= " t.tms,";
		$sql .= " t.status";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
		    $sql .= " AND entity IN (" . getEntity("assetsassignment", 1) . ")";
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
				$this->fk_user = $obj->fk_user;
				$this->fk_projet = $obj->fk_projet;
				$this->fk_projet_from = $obj->fk_projet_from;
				$this->fk_user_from = $obj->fk_user_from;
				$this->fk_property_from = $obj->fk_property_from;
				$this->fk_user_to = $obj->fk_user_to;
				$this->fk_property = $obj->fk_property;
				$this->fk_location = $obj->fk_location;
				$this->detail = $obj->detail;
				$this->date_assignment = $this->db->jdate($obj->date_assignment);
				$this->type_assignment = $obj->type_assignment;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->date_mod = $this->db->jdate($obj->date_mod);
				$this->mark = $obj->mark;
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_mod = $obj->fk_user_mod;
				$this->fk_user_approved = $obj->fk_user_approved;
				$this->date_approved = $this->db->jdate($obj->date_approved);
				$this->model_pdf = $obj->model_pdf;
				$this->origin = $obj->origin;
				$this->originid = $obj->originid;
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

		$sql .= " t.entity,";
		$sql .= " t.ref,";
		$sql .= " t.fk_user,";
		$sql .= " t.fk_projet,";
		$sql .= " t.fk_projet_from,";
		$sql .= " t.fk_user_from,";
		$sql .= " t.fk_property_from,";
		$sql .= " t.fk_user_to,";
		$sql .= " t.fk_property,";
		$sql .= " t.fk_location,";
		$sql .= " t.detail,";
		$sql .= " t.date_assignment,";
		$sql .= " t.type_assignment,";
		$sql .= " t.date_create,";
		$sql .= " t.date_mod,";
		$sql .= " t.mark,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.fk_user_approved,";
		$sql .= " t.date_approved,";
		$sql .= " t.model_pdf,";
		$sql .= " t.origin,";
		$sql .= " t.originid,";
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
		    $sql .= " AND entity IN (" . getEntity("assetsassignment", 1) . ")";
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
				$line = new AssetsassignmentLine();

				$line->id = $obj->rowid;

				$line->entity = $obj->entity;
				$line->ref = $obj->ref;
				$line->fk_user = $obj->fk_user;
				$line->fk_projet = $obj->fk_projet;
				$line->fk_projet_from = $obj->fk_projet_from;
				$line->fk_user_from = $obj->fk_user_from;
				$line->fk_property_from = $obj->fk_property_from;
				$line->fk_user_to = $obj->fk_user_to;
				$line->fk_property = $obj->fk_property;
				$line->fk_location = $obj->fk_location;
				$line->detail = $obj->detail;
				$line->date_assignment = $this->db->jdate($obj->date_assignment);
				$line->type_assignment = $obj->type_assignment;
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->date_mod = $this->db->jdate($obj->date_mod);
				$line->mark = $obj->mark;
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->fk_user_approved = $obj->fk_user_approved;
				$line->date_approved = $this->db->jdate($obj->date_approved);
				$line->model_pdf = $obj->model_pdf;
				$line->origin = $obj->origin;
				$line->originid = $obj->originid;
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
		if (isset($this->fk_user)) {
			 $this->fk_user = trim($this->fk_user);
		}
		if (isset($this->fk_projet)) {
			 $this->fk_projet = trim($this->fk_projet);
		}
		if (isset($this->fk_projet_from)) {
			 $this->fk_projet_from = trim($this->fk_projet_from);
		}
		if (isset($this->fk_user_from)) {
			 $this->fk_user_from = trim($this->fk_user_from);
		}
		if (isset($this->fk_property_from)) {
			 $this->fk_property_from = trim($this->fk_property_from);
		}
		if (isset($this->fk_user_to)) {
			 $this->fk_user_to = trim($this->fk_user_to);
		}
		if (isset($this->fk_property)) {
			 $this->fk_property = trim($this->fk_property);
		}
		if (isset($this->fk_location)) {
			 $this->fk_location = trim($this->fk_location);
		}
		if (isset($this->detail)) {
			 $this->detail = trim($this->detail);
		}
		if (isset($this->type_assignment)) {
			 $this->type_assignment = trim($this->type_assignment);
		}
		if (isset($this->mark)) {
			 $this->mark = trim($this->mark);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_mod)) {
			 $this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->fk_user_approved)) {
			 $this->fk_user_approved = trim($this->fk_user_approved);
		}
		if (isset($this->model_pdf)) {
			 $this->model_pdf = trim($this->model_pdf);
		}
		if (isset($this->origin)) {
			 $this->origin = trim($this->origin);
		}
		if (isset($this->originid)) {
			 $this->originid = trim($this->originid);
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
		$sql .= ' fk_user = '.(isset($this->fk_user)?$this->fk_user:"null").',';
		$sql .= ' fk_projet = '.(isset($this->fk_projet)?$this->fk_projet:"null").',';
		$sql .= ' fk_projet_from = '.(isset($this->fk_projet_from)?$this->fk_projet_from:"null").',';
		$sql .= ' fk_user_from = '.(isset($this->fk_user_from)?$this->fk_user_from:"null").',';
		$sql .= ' fk_property_from = '.(isset($this->fk_property_from)?$this->fk_property_from:"null").',';
		$sql .= ' fk_user_to = '.(isset($this->fk_user_to)?$this->fk_user_to:"null").',';
		$sql .= ' fk_property = '.(isset($this->fk_property)?$this->fk_property:"null").',';
		$sql .= ' fk_location = '.(isset($this->fk_location)?$this->fk_location:"null").',';
		$sql .= ' detail = '.(isset($this->detail)?"'".$this->db->escape($this->detail)."'":"null").',';
		$sql .= ' date_assignment = '.(! isset($this->date_assignment) || dol_strlen($this->date_assignment) != 0 ? "'".$this->db->idate($this->date_assignment)."'" : 'null').',';
		$sql .= ' type_assignment = '.(isset($this->type_assignment)?"'".$this->db->escape($this->type_assignment)."'":"null").',';
		$sql .= ' date_create = '.(! isset($this->date_create) || dol_strlen($this->date_create) != 0 ? "'".$this->db->idate($this->date_create)."'" : 'null').',';
		$sql .= ' date_mod = '.(! isset($this->date_mod) || dol_strlen($this->date_mod) != 0 ? "'".$this->db->idate($this->date_mod)."'" : 'null').',';
		$sql .= ' mark = '.(isset($this->mark)?"'".$this->db->escape($this->mark)."'":"null").',';
		$sql .= ' fk_user_create = '.(isset($this->fk_user_create)?$this->fk_user_create:"null").',';
		$sql .= ' fk_user_mod = '.$user->id.',';
		$sql .= ' fk_user_approved = '.(isset($this->fk_user_approved)?$this->fk_user_approved:"null").',';
		$sql .= ' date_approved = '.(! isset($this->date_approved) || dol_strlen($this->date_approved) != 0 ? "'".$this->db->idate($this->date_approved)."'" : 'null').',';
		$sql .= ' model_pdf = '.(isset($this->model_pdf)?"'".$this->db->escape($this->model_pdf)."'":"null").',';
		$sql .= ' origin = '.(isset($this->origin)?"'".$this->db->escape($this->origin)."'":"null").',';
		$sql .= ' originid = '.(isset($this->originid)?$this->originid:"null").',';
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
		$object = new Assetsassignment($this->db);

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

        $url = DOL_URL_ROOT.'/assets/'.$this->table_name.'_card.php?id='.$this->id;

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
	function getLibStatutx($mode=0)
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
	static function LibStatutx($status,$mode=0)
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
		$this->fk_user = '';
		$this->fk_projet = '';
		$this->fk_projet_from = '';
		$this->fk_user_from = '';
		$this->fk_property_from = '';
		$this->fk_user_to = '';
		$this->fk_property = '';
		$this->fk_location = '';
		$this->detail = '';
		$this->date_assignment = '';
		$this->type_assignment = '';
		$this->date_create = '';
		$this->date_mod = '';
		$this->mark = '';
		$this->fk_user_create = '';
		$this->fk_user_mod = '';
		$this->fk_user_approved = '';
		$this->date_approved = '';
		$this->model_pdf = '';
		$this->origin = '';
		$this->originid = '';
		$this->tms = '';
		$this->status = '';


	}

}

/**
 * Class AssetsassignmentLine
 */
class AssetsassignmentLine
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
	public $fk_user;
	public $fk_projet;
	public $fk_projet_from;
	public $fk_user_from;
	public $fk_property_from;
	public $fk_user_to;
	public $fk_property;
	public $fk_location;
	public $detail;
	public $date_assignment = '';
	public $type_assignment;
	public $date_create = '';
	public $date_mod = '';
	public $mark;
	public $fk_user_create;
	public $fk_user_mod;
	public $fk_user_approved;
	public $date_approved = '';
	public $model_pdf;
	public $origin;
	public $originid;
	public $tms = '';
	public $status;

	/**
	 * @var mixed Sample line property 2
	 */

}
