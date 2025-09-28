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
 * \file    budget/budget.class.php
 * \ingroup budget
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Budget
 *
 * Put here description of your class
 * @see CommonObject
 */
class Budget extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'budget';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'budget';

	/**
	 * @var BudgetLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	
	public $entity;
	public $fk_soc;
	public $fk_projet;
	public $fk_calendar;
	public $fk_country;
	public $fk_departament;
	public $fk_city;
	public $data_type;
	public $location;
	public $datec = '';
	public $tms = '';
	public $dateo = '';
	public $datee = '';
	public $ref;
	public $version;
	public $type_structure;
	public $title;
	public $description;
	public $fk_user_creat;
	public $public;
	public $fk_statut;
	public $fk_opp_status;
	public $opp_percent;
	public $date_close = '';
	public $fk_user_close;
	public $note_private;
	public $note_public;
	public $opp_amount;
	public $budget_amount;
	public $model_pdf;

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
		if (isset($this->fk_soc)) {
			 $this->fk_soc = trim($this->fk_soc);
		}
		if (isset($this->fk_projet)) {
			 $this->fk_projet = trim($this->fk_projet);
		}
		if (isset($this->fk_calendar)) {
			 $this->fk_calendar = trim($this->fk_calendar);
		}
		if (isset($this->fk_country)) {
			 $this->fk_country = trim($this->fk_country);
		}
		if (isset($this->fk_departament)) {
			 $this->fk_departament = trim($this->fk_departament);
		}
		if (isset($this->fk_city)) {
			 $this->fk_city = trim($this->fk_city);
		}
		if (isset($this->data_type)) {
			 $this->data_type = trim($this->data_type);
		}
		if (isset($this->location)) {
			 $this->location = trim($this->location);
		}
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->version)) {
			 $this->version = trim($this->version);
		}
		if (isset($this->type_structure)) {
			 $this->type_structure = trim($this->type_structure);
		}
		if (isset($this->title)) {
			 $this->title = trim($this->title);
		}
		if (isset($this->description)) {
			 $this->description = trim($this->description);
		}
		if (isset($this->fk_user_creat)) {
			 $this->fk_user_creat = trim($this->fk_user_creat);
		}
		if (isset($this->public)) {
			 $this->public = trim($this->public);
		}
		if (isset($this->fk_statut)) {
			 $this->fk_statut = trim($this->fk_statut);
		}
		if (isset($this->fk_opp_status)) {
			 $this->fk_opp_status = trim($this->fk_opp_status);
		}
		if (isset($this->opp_percent)) {
			 $this->opp_percent = trim($this->opp_percent);
		}
		if (isset($this->fk_user_close)) {
			 $this->fk_user_close = trim($this->fk_user_close);
		}
		if (isset($this->note_private)) {
			 $this->note_private = trim($this->note_private);
		}
		if (isset($this->note_public)) {
			 $this->note_public = trim($this->note_public);
		}
		if (isset($this->opp_amount)) {
			 $this->opp_amount = trim($this->opp_amount);
		}
		if (isset($this->budget_amount)) {
			 $this->budget_amount = trim($this->budget_amount);
		}
		if (isset($this->model_pdf)) {
			 $this->model_pdf = trim($this->model_pdf);
		}

		

		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';
		
		$sql.= 'entity,';
		$sql.= 'fk_soc,';
		$sql.= 'fk_projet,';
		$sql.= 'fk_calendar,';
		$sql.= 'fk_country,';
		$sql.= 'fk_departament,';
		$sql.= 'fk_city,';
		$sql.= 'data_type,';
		$sql.= 'location,';
		$sql.= 'datec,';
		$sql.= 'dateo,';
		$sql.= 'datee,';
		$sql.= 'ref,';
		$sql.= 'version,';
		$sql.= 'type_structure,';
		$sql.= 'title,';
		$sql.= 'description,';
		$sql.= 'fk_user_creat,';
		$sql.= 'public,';
		$sql.= 'fk_statut,';
		$sql.= 'fk_opp_status,';
		$sql.= 'opp_percent,';
		$sql.= 'date_close,';
		$sql.= 'fk_user_close,';
		$sql.= 'note_private,';
		$sql.= 'note_public,';
		$sql.= 'opp_amount,';
		$sql.= 'budget_amount,';
		$sql.= 'model_pdf';

		
		$sql .= ') VALUES (';
		
		$sql .= ' '.(! isset($this->entity)?'NULL':$this->entity).',';
		$sql .= ' '.(! isset($this->fk_soc)?'NULL':$this->fk_soc).',';
		$sql .= ' '.(! isset($this->fk_projet)?'NULL':$this->fk_projet).',';
		$sql .= ' '.(! isset($this->fk_calendar)?'NULL':$this->fk_calendar).',';
		$sql .= ' '.(! isset($this->fk_country)?'NULL':$this->fk_country).',';
		$sql .= ' '.(! isset($this->fk_departament)?'NULL':$this->fk_departament).',';
		$sql .= ' '.(! isset($this->fk_city)?'NULL':$this->fk_city).',';
		$sql .= ' '.(! isset($this->data_type)?'NULL':"'".$this->db->escape($this->data_type)."'").',';
		$sql .= ' '.(! isset($this->location)?'NULL':"'".$this->db->escape($this->location)."'").',';
		$sql .= ' '."'".$this->db->idate(dol_now())."'".',';
		$sql .= ' '.(! isset($this->dateo) || dol_strlen($this->dateo)==0?'NULL':"'".$this->db->idate($this->dateo)."'").',';
		$sql .= ' '.(! isset($this->datee) || dol_strlen($this->datee)==0?'NULL':"'".$this->db->idate($this->datee)."'").',';
		$sql .= ' '.(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql .= ' '.(! isset($this->version)?'NULL':"'".$this->db->escape($this->version)."'").',';
		$sql .= ' '.(! isset($this->type_structure)?'NULL':"'".$this->db->escape($this->type_structure)."'").',';
		$sql .= ' '.(! isset($this->title)?'NULL':"'".$this->db->escape($this->title)."'").',';
		$sql .= ' '.(! isset($this->description)?'NULL':"'".$this->db->escape($this->description)."'").',';
		$sql .= ' '.(! isset($this->fk_user_creat)?'NULL':$this->fk_user_creat).',';
		$sql .= ' '.(! isset($this->public)?'NULL':$this->public).',';
		$sql .= ' '.(! isset($this->fk_statut)?'NULL':$this->fk_statut).',';
		$sql .= ' '.(! isset($this->fk_opp_status)?'NULL':$this->fk_opp_status).',';
		$sql .= ' '.(! isset($this->opp_percent)?'NULL':"'".$this->opp_percent."'").',';
		$sql .= ' '.(! isset($this->date_close) || dol_strlen($this->date_close)==0?'NULL':"'".$this->db->idate($this->date_close)."'").',';
		$sql .= ' '.(! isset($this->fk_user_close)?'NULL':$this->fk_user_close).',';
		$sql .= ' '.(! isset($this->note_private)?'NULL':"'".$this->db->escape($this->note_private)."'").',';
		$sql .= ' '.(! isset($this->note_public)?'NULL':"'".$this->db->escape($this->note_public)."'").',';
		$sql .= ' '.(! isset($this->opp_amount)?'NULL':"'".$this->opp_amount."'").',';
		$sql .= ' '.(! isset($this->budget_amount)?'NULL':"'".$this->budget_amount."'").',';
		$sql .= ' '.(! isset($this->model_pdf)?'NULL':"'".$this->db->escape($this->model_pdf)."'");

		
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
		global $conf;
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		
		$sql .= " t.entity,";
		$sql .= " t.fk_soc,";
		$sql .= " t.fk_projet,";
		$sql .= " t.fk_calendar,";
		$sql .= " t.fk_country,";
		$sql .= " t.fk_departament,";
		$sql .= " t.fk_city,";
		$sql .= " t.data_type,";
		$sql .= " t.location,";
		$sql .= " t.datec,";
		$sql .= " t.tms,";
		$sql .= " t.dateo,";
		$sql .= " t.datee,";
		$sql .= " t.ref,";
		$sql .= " t.version,";
		$sql .= " t.type_structure,";
		$sql .= " t.title,";
		$sql .= " t.description,";
		$sql .= " t.fk_user_creat,";
		$sql .= " t.public,";
		$sql .= " t.fk_statut,";
		$sql .= " t.fk_opp_status,";
		$sql .= " t.opp_percent,";
		$sql .= " t.date_close,";
		$sql .= " t.fk_user_close,";
		$sql .= " t.note_private,";
		$sql .= " t.note_public,";
		$sql .= " t.opp_amount,";
		$sql .= " t.budget_amount,";
		$sql .= " t.model_pdf";

		
		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		if (null !== $ref) {
			$sql .= ' WHERE t.ref = ' . '\'' . $ref . '\'';
			$sql.= " AND t.entity = ".$conf->entity;
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
				$this->fk_soc = $obj->fk_soc;
				$this->fk_projet = $obj->fk_projet;
				$this->fk_calendar = $obj->fk_calendar;
				$this->fk_country = $obj->fk_country;
				$this->fk_departament = $obj->fk_departament;
				$this->fk_city = $obj->fk_city;
				$this->data_type = $obj->data_type;
				$this->location = $obj->location;
				$this->datec = $this->db->jdate($obj->datec);
				$this->tms = $this->db->jdate($obj->tms);
				$this->dateo = $this->db->jdate($obj->dateo);
				$this->datee = $this->db->jdate($obj->datee);
				$this->ref = $obj->ref;
				$this->version = $obj->version;
				$this->type_structure = $obj->type_structure;
				$this->title = $obj->title;
				$this->description = $obj->description;
				$this->fk_user_creat = $obj->fk_user_creat;
				$this->public = $obj->public;
				$this->fk_statut = $obj->fk_statut;
				$this->fk_opp_status = $obj->fk_opp_status;
				$this->opp_percent = $obj->opp_percent;
				$this->date_close = $this->db->jdate($obj->date_close);
				$this->fk_user_close = $obj->fk_user_close;
				$this->note_private = $obj->note_private;
				$this->note_public = $obj->note_public;
				$this->opp_amount = $obj->opp_amount;
				$this->budget_amount = $obj->budget_amount;
				$this->model_pdf = $obj->model_pdf;

				
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
		$sql .= " t.fk_soc,";
		$sql .= " t.fk_projet,";
		$sql .= " t.fk_calendar,";
		$sql .= " t.fk_country,";
		$sql .= " t.fk_departament,";
		$sql .= " t.fk_city,";
		$sql .= " t.data_type,";
		$sql .= " t.location,";
		$sql .= " t.datec,";
		$sql .= " t.tms,";
		$sql .= " t.dateo,";
		$sql .= " t.datee,";
		$sql .= " t.ref,";
		$sql .= " t.version,";
		$sql .= " t.type_structure,";
		$sql .= " t.title,";
		$sql .= " t.description,";
		$sql .= " t.fk_user_creat,";
		$sql .= " t.public,";
		$sql .= " t.fk_statut,";
		$sql .= " t.fk_opp_status,";
		$sql .= " t.opp_percent,";
		$sql .= " t.date_close,";
		$sql .= " t.fk_user_close,";
		$sql .= " t.note_private,";
		$sql .= " t.note_public,";
		$sql .= " t.opp_amount,";
		$sql .= " t.budget_amount,";
		$sql .= " t.model_pdf";

		
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
				$line = new BudgetLine();

				$line->id = $obj->rowid;
				
				$line->entity = $obj->entity;
				$line->fk_soc = $obj->fk_soc;
				$line->fk_projet = $obj->fk_projet;
				$line->fk_calendar = $obj->fk_calendar;
				$line->fk_country = $obj->fk_country;
				$line->fk_departament = $obj->fk_departament;
				$line->fk_city = $obj->fk_city;
				$line->data_type = $obj->data_type;
				$line->location = $obj->location;
				$line->datec = $this->db->jdate($obj->datec);
				$line->tms = $this->db->jdate($obj->tms);
				$line->dateo = $this->db->jdate($obj->dateo);
				$line->datee = $this->db->jdate($obj->datee);
				$line->ref = $obj->ref;
				$line->version = $obj->version;
				$line->type_structure = $obj->type_structure;
				$line->title = $obj->title;
				$line->description = $obj->description;
				$line->fk_user_creat = $obj->fk_user_creat;
				$line->public = $obj->public;
				$line->fk_statut = $obj->fk_statut;
				$line->fk_opp_status = $obj->fk_opp_status;
				$line->opp_percent = $obj->opp_percent;
				$line->date_close = $this->db->jdate($obj->date_close);
				$line->fk_user_close = $obj->fk_user_close;
				$line->note_private = $obj->note_private;
				$line->note_public = $obj->note_public;
				$line->opp_amount = $obj->opp_amount;
				$line->budget_amount = $obj->budget_amount;
				$line->model_pdf = $obj->model_pdf;

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
		if (isset($this->fk_soc)) {
			 $this->fk_soc = trim($this->fk_soc);
		}
		if (isset($this->fk_projet)) {
			 $this->fk_projet = trim($this->fk_projet);
		}
		if (isset($this->fk_calendar)) {
			 $this->fk_calendar = trim($this->fk_calendar);
		}
		if (isset($this->fk_country)) {
			 $this->fk_country = trim($this->fk_country);
		}
		if (isset($this->fk_departament)) {
			 $this->fk_departament = trim($this->fk_departament);
		}
		if (isset($this->fk_city)) {
			 $this->fk_city = trim($this->fk_city);
		}
		if (isset($this->data_type)) {
			 $this->data_type = trim($this->data_type);
		}
		if (isset($this->location)) {
			 $this->location = trim($this->location);
		}
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->version)) {
			 $this->version = trim($this->version);
		}
		if (isset($this->type_structure)) {
			 $this->type_structure = trim($this->type_structure);
		}
		if (isset($this->title)) {
			 $this->title = trim($this->title);
		}
		if (isset($this->description)) {
			 $this->description = trim($this->description);
		}
		if (isset($this->fk_user_creat)) {
			 $this->fk_user_creat = trim($this->fk_user_creat);
		}
		if (isset($this->public)) {
			 $this->public = trim($this->public);
		}
		if (isset($this->fk_statut)) {
			 $this->fk_statut = trim($this->fk_statut);
		}
		if (isset($this->fk_opp_status)) {
			 $this->fk_opp_status = trim($this->fk_opp_status);
		}
		if (isset($this->opp_percent)) {
			 $this->opp_percent = trim($this->opp_percent);
		}
		if (isset($this->fk_user_close)) {
			 $this->fk_user_close = trim($this->fk_user_close);
		}
		if (isset($this->note_private)) {
			 $this->note_private = trim($this->note_private);
		}
		if (isset($this->note_public)) {
			 $this->note_public = trim($this->note_public);
		}
		if (isset($this->opp_amount)) {
			 $this->opp_amount = trim($this->opp_amount);
		}
		if (isset($this->budget_amount)) {
			 $this->budget_amount = trim($this->budget_amount);
		}
		if (isset($this->model_pdf)) {
			 $this->model_pdf = trim($this->model_pdf);
		}

		

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';
		
		$sql .= ' entity = '.(isset($this->entity)?$this->entity:"null").',';
		$sql .= ' fk_soc = '.(isset($this->fk_soc)?$this->fk_soc:"null").',';
		$sql .= ' fk_projet = '.(isset($this->fk_projet)?$this->fk_projet:"null").',';
		$sql .= ' fk_calendar = '.(isset($this->fk_calendar)?$this->fk_calendar:"null").',';
		$sql .= ' fk_country = '.(isset($this->fk_country)?$this->fk_country:"null").',';
		$sql .= ' fk_departament = '.(isset($this->fk_departament)?$this->fk_departament:"null").',';
		$sql .= ' fk_city = '.(isset($this->fk_city)?$this->fk_city:"null").',';
		$sql .= ' data_type = '.(isset($this->data_type)?"'".$this->db->escape($this->data_type)."'":"null").',';
		$sql .= ' location = '.(isset($this->location)?"'".$this->db->escape($this->location)."'":"null").',';
		$sql .= ' datec = '.(! isset($this->datec) || dol_strlen($this->datec) != 0 ? "'".$this->db->idate($this->datec)."'" : 'null').',';
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
		$sql .= ' dateo = '.(! isset($this->dateo) || dol_strlen($this->dateo) != 0 ? "'".$this->db->idate($this->dateo)."'" : 'null').',';
		$sql .= ' datee = '.(! isset($this->datee) || dol_strlen($this->datee) != 0 ? "'".$this->db->idate($this->datee)."'" : 'null').',';
		$sql .= ' ref = '.(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").',';
		$sql .= ' version = '.(isset($this->version)?"'".$this->db->escape($this->version)."'":"null").',';
		$sql .= ' type_structure = '.(isset($this->type_structure)?"'".$this->db->escape($this->type_structure)."'":"null").',';
		$sql .= ' title = '.(isset($this->title)?"'".$this->db->escape($this->title)."'":"null").',';
		$sql .= ' description = '.(isset($this->description)?"'".$this->db->escape($this->description)."'":"null").',';
		$sql .= ' fk_user_creat = '.(isset($this->fk_user_creat)?$this->fk_user_creat:"null").',';
		$sql .= ' public = '.(isset($this->public)?$this->public:"null").',';
		$sql .= ' fk_statut = '.(isset($this->fk_statut)?$this->fk_statut:"null").',';
		$sql .= ' fk_opp_status = '.(isset($this->fk_opp_status)?$this->fk_opp_status:"null").',';
		$sql .= ' opp_percent = '.(isset($this->opp_percent)?$this->opp_percent:"null").',';
		$sql .= ' date_close = '.(! isset($this->date_close) || dol_strlen($this->date_close) != 0 ? "'".$this->db->idate($this->date_close)."'" : 'null').',';
		$sql .= ' fk_user_close = '.(isset($this->fk_user_close)?$this->fk_user_close:"null").',';
		$sql .= ' note_private = '.(isset($this->note_private)?"'".$this->db->escape($this->note_private)."'":"null").',';
		$sql .= ' note_public = '.(isset($this->note_public)?"'".$this->db->escape($this->note_public)."'":"null").',';
		$sql .= ' opp_amount = '.(isset($this->opp_amount)?$this->opp_amount:"null").',';
		$sql .= ' budget_amount = '.(isset($this->budget_amount)?$this->budget_amount:"null").',';
		$sql .= ' model_pdf = '.(isset($this->model_pdf)?"'".$this->db->escape($this->model_pdf)."'":"null");

        
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
		$object = new Budget($this->db);

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

        $label = '<u>' . $langs->trans("Budget") . '</u>';
        $label.= '<div width="100%">';
        $label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

        $link = '<a href="'.DOL_URL_ROOT.'/budget/budget/card.php?id='.$this->id.'"';
        $link.= ($notooltip?'':' title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip'.($morecss?' '.$morecss:'').'"');
        $link.= '>';
		$linkend='</a>';

        if ($withpicto)
        {
            $result.=($link.img_object(($notooltip?'':$label), 'label', ($notooltip?'':'class="classfortooltip"')).$linkend);
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
			if ($status == -1) return $langs->trans('Discarted');
			if ($status == 0) return $langs->trans('Draft');
			if ($status == 1) return $langs->trans('In Validation');
			//if ($status == 2) return $langs->trans('Validated');
			if ($status == 2) return $langs->trans('Approved');
		}
		if ($mode == 1)
		{
			if ($status == -1) return $langs->trans('Discarted');
			if ($status == 0) return $langs->trans('Draft');
			if ($status == 1) return $langs->trans('In Validation');
			//if ($status == 2) return $langs->trans('Validated');
			if ($status == 2) return $langs->trans('Approved');
		}
		if ($mode == 2)
		{
			if ($status == -1) return img_picto($langs->trans('Discarted'),'statut5').' '.$langs->trans('Discarted');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut0').' '.$langs->trans('Draft');
			if ($status == 1) return img_picto($langs->trans('In Validation'),'statut1').' '.$langs->trans('In Validation');
			//if ($status == 2) return img_picto($langs->trans('Validated'),'statut3').' '.$langs->trans('Validated');
			if ($status == 2) return img_picto($langs->trans('Approved'),'statut4').' '.$langs->trans('Approved');
		}
		if ($mode == 3)
		{
			if ($status == -1) return img_picto($langs->trans('Discarted'),'statut5');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut0');
			if ($status == 1) return img_picto($langs->trans('In Validation'),'statut1');
			//if ($status == 2) return img_picto($langs->trans('Validated'),'statut3');
			if ($status == 2) return img_picto($langs->trans('Approved'),'statut4');
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
		$this->fk_soc = '';
		$this->fk_projet = '';
		$this->fk_calendar = '';
		$this->fk_country = '';
		$this->fk_departament = '';
		$this->fk_city = '';
		$this->data_type = '';
		$this->location = '';
		$this->datec = '';
		$this->tms = '';
		$this->dateo = '';
		$this->datee = '';
		$this->ref = '';
		$this->version = '';
		$this->type_structure = '';
		$this->title = '';
		$this->description = '';
		$this->fk_user_creat = '';
		$this->public = '';
		$this->fk_statut = '';
		$this->fk_opp_status = '';
		$this->opp_percent = '';
		$this->date_close = '';
		$this->fk_user_close = '';
		$this->note_private = '';
		$this->note_public = '';
		$this->opp_amount = '';
		$this->budget_amount = '';
		$this->model_pdf = '';

		
	}

}

/**
 * Class BudgetLine
 */
class BudgetLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	
	public $entity;
	public $fk_soc;
	public $fk_projet;
	public $fk_calendar;
	public $fk_country;
	public $fk_departament;
	public $fk_city;
	public $data_type;
	public $location;
	public $datec = '';
	public $tms = '';
	public $dateo = '';
	public $datee = '';
	public $ref;
	public $version;
	public $type_structure;
	public $title;
	public $description;
	public $fk_user_creat;
	public $public;
	public $fk_statut;
	public $fk_opp_status;
	public $opp_percent;
	public $date_close = '';
	public $fk_user_close;
	public $note_private;
	public $note_public;
	public $opp_amount;
	public $budget_amount;
	public $model_pdf;

	/**
	 * @var mixed Sample line property 2
	 */
	
}
