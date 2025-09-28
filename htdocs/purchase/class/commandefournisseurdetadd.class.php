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
 * \file    purchase/commandefournisseurdetadd.class.php
 * \ingroup purchase
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Commandefournisseurdetadd
 *
 * Put here description of your class
 *
 * @see CommonObject
 */
class Commandefournisseurdetadd extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'commandefournisseurdetadd';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'commande_fournisseurdet_add';

	/**
	 * @var CommandefournisseurdetaddLine[] Lines
	 */
	public $lines = array();

	/**
	 */

	public $fk_commande_fournisseurdet;
	public $fk_object;
	public $object;
	public $fk_fabrication;
	public $fk_fabricationdet;
	public $fk_projet;
	public $fk_projet_task;
	public $fk_jobs;
	public $fk_jobsdet;
	public $fk_structure;
	public $fk_poa;
	public $partida;
	public $amount_ice;
	public $discount;
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

		if (isset($this->fk_commande_fournisseurdet)) {
			 $this->fk_commande_fournisseurdet = trim($this->fk_commande_fournisseurdet);
		}
		if (isset($this->fk_object)) {
			 $this->fk_object = trim($this->fk_object);
		}
		if (isset($this->object)) {
			 $this->object = trim($this->object);
		}
		if (isset($this->fk_fabrication)) {
			 $this->fk_fabrication = trim($this->fk_fabrication);
		}
		if (isset($this->fk_fabricationdet)) {
			 $this->fk_fabricationdet = trim($this->fk_fabricationdet);
		}
		if (isset($this->fk_projet)) {
			 $this->fk_projet = trim($this->fk_projet);
		}
		if (isset($this->fk_projet_task)) {
			 $this->fk_projet_task = trim($this->fk_projet_task);
		}
		if (isset($this->fk_jobs)) {
			 $this->fk_jobs = trim($this->fk_jobs);
		}
		if (isset($this->fk_jobsdet)) {
			 $this->fk_jobsdet = trim($this->fk_jobsdet);
		}
		if (isset($this->fk_structure)) {
			 $this->fk_structure = trim($this->fk_structure);
		}
		if (isset($this->fk_poa)) {
			 $this->fk_poa = trim($this->fk_poa);
		}
		if (isset($this->partida)) {
			 $this->partida = trim($this->partida);
		}
		if (isset($this->amount_ice)) {
			 $this->amount_ice = trim($this->amount_ice);
		}
		if (isset($this->discount)) {
			 $this->discount = trim($this->discount);
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

		$sql.= 'fk_commande_fournisseurdet,';
		$sql.= 'fk_object,';
		$sql.= 'object,';
		$sql.= 'fk_fabrication,';
		$sql.= 'fk_fabricationdet,';
		$sql.= 'fk_projet,';
		$sql.= 'fk_projet_task,';
		$sql.= 'fk_jobs,';
		$sql.= 'fk_jobsdet,';
		$sql.= 'fk_structure,';
		$sql.= 'fk_poa,';
		$sql.= 'partida,';
		$sql.= 'amount_ice,';
		$sql.= 'discount,';
		$sql.= 'fk_user_create,';
		$sql.= 'fk_user_mod,';
		$sql.= 'datec,';
		$sql.= 'datem,';
		$sql.= 'status';


		$sql .= ') VALUES (';

		$sql .= ' '.(! isset($this->fk_commande_fournisseurdet)?'NULL':$this->fk_commande_fournisseurdet).',';
		$sql .= ' '.(! isset($this->fk_object)?'NULL':$this->fk_object).',';
		$sql .= ' '.(! isset($this->object)?'NULL':"'".$this->db->escape($this->object)."'").',';
		$sql .= ' '.(! isset($this->fk_fabrication)?'NULL':$this->fk_fabrication).',';
		$sql .= ' '.(! isset($this->fk_fabricationdet)?'NULL':$this->fk_fabricationdet).',';
		$sql .= ' '.(! isset($this->fk_projet)?'NULL':$this->fk_projet).',';
		$sql .= ' '.(! isset($this->fk_projet_task)?'NULL':$this->fk_projet_task).',';
		$sql .= ' '.(! isset($this->fk_jobs)?'NULL':$this->fk_jobs).',';
		$sql .= ' '.(! isset($this->fk_jobsdet)?'NULL':$this->fk_jobsdet).',';
		$sql .= ' '.(! isset($this->fk_structure)?'NULL':$this->fk_structure).',';
		$sql .= ' '.(! isset($this->fk_poa)?'NULL':$this->fk_poa).',';
		$sql .= ' '.(! isset($this->partida)?'NULL':"'".$this->db->escape($this->partida)."'").',';
		$sql .= ' '.(! isset($this->amount_ice)?'NULL':"'".$this->amount_ice."'").',';
		$sql .= ' '.(! isset($this->discount)?'NULL':"'".$this->discount."'").',';
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
	public function fetch($id, $fk=0)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.fk_commande_fournisseurdet,";
		$sql .= " t.fk_object,";
		$sql .= " t.object,";
		$sql .= " t.fk_fabrication,";
		$sql .= " t.fk_fabricationdet,";
		$sql .= " t.fk_projet,";
		$sql .= " t.fk_projet_task,";
		$sql .= " t.fk_jobs,";
		$sql .= " t.fk_jobsdet,";
		$sql .= " t.fk_structure,";
		$sql .= " t.fk_poa,";
		$sql .= " t.partida,";
		$sql .= " t.amount_ice,";
		$sql .= " t.discount,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.datec,";
		$sql .= " t.datem,";
		$sql .= " t.tms,";
		$sql .= " t.status";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql.= ' WHERE 1 = 1';
		if ($fk>0) {
			$sql .= ' AND t.fk_commande_fournisseurdet = ' . $fk;
		} else {
			$sql .= ' AND t.rowid = ' . $id;
		}

		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			if ($numrows) {
				$obj = $this->db->fetch_object($resql);

				$this->id = $obj->rowid;

				$this->fk_commande_fournisseurdet = $obj->fk_commande_fournisseurdet;
				$this->fk_object = $obj->fk_object;
				$this->object = $obj->object;
				$this->fk_fabrication = $obj->fk_fabrication;
				$this->fk_fabricationdet = $obj->fk_fabricationdet;
				$this->fk_projet = $obj->fk_projet;
				$this->fk_projet_task = $obj->fk_projet_task;
				$this->fk_jobs = $obj->fk_jobs;
				$this->fk_jobsdet = $obj->fk_jobsdet;
				$this->fk_structure = $obj->fk_structure;
				$this->fk_poa = $obj->fk_poa;
				$this->partida = $obj->partida;
				$this->amount_ice = $obj->amount_ice;
				$this->discount = $obj->discount;
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

		$sql .= " t.fk_commande_fournisseurdet,";
		$sql .= " t.fk_object,";
		$sql .= " t.object,";
		$sql .= " t.fk_fabrication,";
		$sql .= " t.fk_fabricationdet,";
		$sql .= " t.fk_projet,";
		$sql .= " t.fk_projet_task,";
		$sql .= " t.fk_jobs,";
		$sql .= " t.fk_jobsdet,";
		$sql .= " t.fk_structure,";
		$sql .= " t.fk_poa,";
		$sql .= " t.partida,";
		$sql .= " t.amount_ice,";
		$sql .= " t.discount,";
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
		    $sql .= " AND entity IN (" . getEntity("commandefournisseurdetadd", 1) . ")";
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
				$line = new CommandefournisseurdetaddLine();

				$line->id = $obj->rowid;

				$line->fk_commande_fournisseurdet = $obj->fk_commande_fournisseurdet;
				$line->fk_object = $obj->fk_object;
				$line->object = $obj->object;
				$line->fk_fabrication = $obj->fk_fabrication;
				$line->fk_fabricationdet = $obj->fk_fabricationdet;
				$line->fk_projet = $obj->fk_projet;
				$line->fk_projet_task = $obj->fk_projet_task;
				$line->fk_jobs = $obj->fk_jobs;
				$line->fk_jobsdet = $obj->fk_jobsdet;
				$line->fk_structure = $obj->fk_structure;
				$line->fk_poa = $obj->fk_poa;
				$line->partida = $obj->partida;
				$line->amount_ice = $obj->amount_ice;
				$line->discount = $obj->discount;
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

		if (isset($this->fk_commande_fournisseurdet)) {
			 $this->fk_commande_fournisseurdet = trim($this->fk_commande_fournisseurdet);
		}
		if (isset($this->fk_object)) {
			 $this->fk_object = trim($this->fk_object);
		}
		if (isset($this->object)) {
			 $this->object = trim($this->object);
		}
		if (isset($this->fk_fabrication)) {
			 $this->fk_fabrication = trim($this->fk_fabrication);
		}
		if (isset($this->fk_fabricationdet)) {
			 $this->fk_fabricationdet = trim($this->fk_fabricationdet);
		}
		if (isset($this->fk_projet)) {
			 $this->fk_projet = trim($this->fk_projet);
		}
		if (isset($this->fk_projet_task)) {
			 $this->fk_projet_task = trim($this->fk_projet_task);
		}
		if (isset($this->fk_jobs)) {
			 $this->fk_jobs = trim($this->fk_jobs);
		}
		if (isset($this->fk_jobsdet)) {
			 $this->fk_jobsdet = trim($this->fk_jobsdet);
		}
		if (isset($this->fk_structure)) {
			 $this->fk_structure = trim($this->fk_structure);
		}
		if (isset($this->fk_poa)) {
			 $this->fk_poa = trim($this->fk_poa);
		}
		if (isset($this->partida)) {
			 $this->partida = trim($this->partida);
		}
		if (isset($this->amount_ice)) {
			 $this->amount_ice = trim($this->amount_ice);
		}
		if (isset($this->discount)) {
			 $this->discount = trim($this->discount);
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

		$sql .= ' fk_commande_fournisseurdet = '.(isset($this->fk_commande_fournisseurdet)?$this->fk_commande_fournisseurdet:"null").',';
		$sql .= ' fk_object = '.(isset($this->fk_object)?$this->fk_object:"null").',';
		$sql .= ' object = '.(isset($this->object)?"'".$this->db->escape($this->object)."'":"null").',';
		$sql .= ' fk_fabrication = '.(isset($this->fk_fabrication)?$this->fk_fabrication:"null").',';
		$sql .= ' fk_fabricationdet = '.(isset($this->fk_fabricationdet)?$this->fk_fabricationdet:"null").',';
		$sql .= ' fk_projet = '.(isset($this->fk_projet)?$this->fk_projet:"null").',';
		$sql .= ' fk_projet_task = '.(isset($this->fk_projet_task)?$this->fk_projet_task:"null").',';
		$sql .= ' fk_jobs = '.(isset($this->fk_jobs)?$this->fk_jobs:"null").',';
		$sql .= ' fk_jobsdet = '.(isset($this->fk_jobsdet)?$this->fk_jobsdet:"null").',';
		$sql .= ' fk_structure = '.(isset($this->fk_structure)?$this->fk_structure:"null").',';
		$sql .= ' fk_poa = '.(isset($this->fk_poa)?$this->fk_poa:"null").',';
		$sql .= ' partida = '.(isset($this->partida)?"'".$this->db->escape($this->partida)."'":"null").',';
		$sql .= ' amount_ice = '.(isset($this->amount_ice)?$this->amount_ice:"null").',';
		$sql .= ' discount = '.(isset($this->discount)?$this->discount:"null").',';
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
		$object = new Commandefournisseurdetadd($this->db);

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

        $url = DOL_URL_ROOT.'/purchase/'.$this->table_name.'_card.php?id='.$this->id;

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

		$this->fk_commande_fournisseurdet = '';
		$this->fk_object = '';
		$this->object = '';
		$this->fk_fabrication = '';
		$this->fk_fabricationdet = '';
		$this->fk_projet = '';
		$this->fk_projet_task = '';
		$this->fk_jobs = '';
		$this->fk_jobsdet = '';
		$this->fk_structure = '';
		$this->fk_poa = '';
		$this->partida = '';
		$this->amount_ice = '';
		$this->discount = '';
		$this->fk_user_create = '';
		$this->fk_user_mod = '';
		$this->datec = '';
		$this->datem = '';
		$this->tms = '';
		$this->status = '';


	}

}

/**
 * Class CommandefournisseurdetaddLine
 */
class CommandefournisseurdetaddLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */

	public $fk_commande_fournisseurdet;
	public $fk_object;
	public $object;
	public $fk_fabrication;
	public $fk_fabricationdet;
	public $fk_projet;
	public $fk_projet_task;
	public $fk_jobs;
	public $fk_jobsdet;
	public $fk_structure;
	public $fk_poa;
	public $partida;
	public $amount_ice;
	public $discount;
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
