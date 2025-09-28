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
 * \file    fiscal/vdosinghistory.class.php
 * \ingroup fiscal
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Vdosinghistory
 *
 * Put here description of your class
 *
 * @see CommonObject
 */
class Vdosinghistory extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'vdosinghistory';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'v_dosing_history';

	/**
	 * @var VdosinghistoryLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	
	public $fk_dosing;
	public $entity;
	public $fk_subsidiaryid;
	public $series;
	public $num_ini;
	public $num_fin;
	public $num_ult;
	public $num_aprob;
	public $type;
	public $active;
	public $date_val = '';
	public $num_autoriz;
	public $cod_control;
	public $lote;
	public $chave;
	public $descrip;
	public $activity;
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
		
		if (isset($this->fk_dosing)) {
			 $this->fk_dosing = trim($this->fk_dosing);
		}
		if (isset($this->entity)) {
			 $this->entity = trim($this->entity);
		}
		if (isset($this->fk_subsidiaryid)) {
			 $this->fk_subsidiaryid = trim($this->fk_subsidiaryid);
		}
		if (isset($this->series)) {
			 $this->series = trim($this->series);
		}
		if (isset($this->num_ini)) {
			 $this->num_ini = trim($this->num_ini);
		}
		if (isset($this->num_fin)) {
			 $this->num_fin = trim($this->num_fin);
		}
		if (isset($this->num_ult)) {
			 $this->num_ult = trim($this->num_ult);
		}
		if (isset($this->num_aprob)) {
			 $this->num_aprob = trim($this->num_aprob);
		}
		if (isset($this->type)) {
			 $this->type = trim($this->type);
		}
		if (isset($this->active)) {
			 $this->active = trim($this->active);
		}
		if (isset($this->num_autoriz)) {
			 $this->num_autoriz = trim($this->num_autoriz);
		}
		if (isset($this->cod_control)) {
			 $this->cod_control = trim($this->cod_control);
		}
		if (isset($this->lote)) {
			 $this->lote = trim($this->lote);
		}
		if (isset($this->chave)) {
			 $this->chave = trim($this->chave);
		}
		if (isset($this->descrip)) {
			 $this->descrip = trim($this->descrip);
		}
		if (isset($this->activity)) {
			 $this->activity = trim($this->activity);
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
		
		$sql.= 'fk_dosing,';
		$sql.= 'entity,';
		$sql.= 'fk_subsidiaryid,';
		$sql.= 'series,';
		$sql.= 'num_ini,';
		$sql.= 'num_fin,';
		$sql.= 'num_ult,';
		$sql.= 'num_aprob,';
		$sql.= 'type,';
		$sql.= 'active,';
		$sql.= 'date_val,';
		$sql.= 'num_autoriz,';
		$sql.= 'cod_control,';
		$sql.= 'lote,';
		$sql.= 'chave,';
		$sql.= 'descrip,';
		$sql.= 'activity,';
		$sql.= 'fk_user_create,';
		$sql.= 'fk_user_mod,';
		$sql.= 'datec,';
		$sql.= 'datem,';
		$sql.= 'status';

		
		$sql .= ') VALUES (';
		
		$sql .= ' '.(! isset($this->fk_dosing)?'NULL':$this->fk_dosing).',';
		$sql .= ' '.(! isset($this->entity)?'NULL':$this->entity).',';
		$sql .= ' '.(! isset($this->fk_subsidiaryid)?'NULL':$this->fk_subsidiaryid).',';
		$sql .= ' '.(! isset($this->series)?'NULL':"'".$this->db->escape($this->series)."'").',';
		$sql .= ' '.(! isset($this->num_ini)?'NULL':$this->num_ini).',';
		$sql .= ' '.(! isset($this->num_fin)?'NULL':$this->num_fin).',';
		$sql .= ' '.(! isset($this->num_ult)?'NULL':$this->num_ult).',';
		$sql .= ' '.(! isset($this->num_aprob)?'NULL':"'".$this->db->escape($this->num_aprob)."'").',';
		$sql .= ' '.(! isset($this->type)?'NULL':$this->type).',';
		$sql .= ' '.(! isset($this->active)?'NULL':$this->active).',';
		$sql .= ' '.(! isset($this->date_val) || dol_strlen($this->date_val)==0?'NULL':"'".$this->db->idate($this->date_val)."'").',';
		$sql .= ' '.(! isset($this->num_autoriz)?'NULL':"'".$this->db->escape($this->num_autoriz)."'").',';
		$sql .= ' '.(! isset($this->cod_control)?'NULL':"'".$this->db->escape($this->cod_control)."'").',';
		$sql .= ' '.(! isset($this->lote)?'NULL':$this->lote).',';
		$sql .= ' '.(! isset($this->chave)?'NULL':"'".$this->db->escape($this->chave)."'").',';
		$sql .= ' '.(! isset($this->descrip)?'NULL':"'".$this->db->escape($this->descrip)."'").',';
		$sql .= ' '.(! isset($this->activity)?'NULL':"'".$this->db->escape($this->activity)."'").',';
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
	public function fetch($id, $ref = null)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		
		$sql .= " t.fk_dosing,";
		$sql .= " t.entity,";
		$sql .= " t.fk_subsidiaryid,";
		$sql .= " t.series,";
		$sql .= " t.num_ini,";
		$sql .= " t.num_fin,";
		$sql .= " t.num_ult,";
		$sql .= " t.num_aprob,";
		$sql .= " t.type,";
		$sql .= " t.active,";
		$sql .= " t.date_val,";
		$sql .= " t.num_autoriz,";
		$sql .= " t.cod_control,";
		$sql .= " t.lote,";
		$sql .= " t.chave,";
		$sql .= " t.descrip,";
		$sql .= " t.activity,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.datec,";
		$sql .= " t.datem,";
		$sql .= " t.tms,";
		$sql .= " t.status";

		
		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
		    $sql .= " AND entity IN (" . getEntity("vdosinghistory", 1) . ")";
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
				
				$this->fk_dosing = $obj->fk_dosing;
				$this->entity = $obj->entity;
				$this->fk_subsidiaryid = $obj->fk_subsidiaryid;
				$this->series = $obj->series;
				$this->num_ini = $obj->num_ini;
				$this->num_fin = $obj->num_fin;
				$this->num_ult = $obj->num_ult;
				$this->num_aprob = $obj->num_aprob;
				$this->type = $obj->type;
				$this->active = $obj->active;
				$this->date_val = $this->db->jdate($obj->date_val);
				$this->num_autoriz = $obj->num_autoriz;
				$this->cod_control = $obj->cod_control;
				$this->lote = $obj->lote;
				$this->chave = $obj->chave;
				$this->descrip = $obj->descrip;
				$this->activity = $obj->activity;
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
		
		$sql .= " t.fk_dosing,";
		$sql .= " t.entity,";
		$sql .= " t.fk_subsidiaryid,";
		$sql .= " t.series,";
		$sql .= " t.num_ini,";
		$sql .= " t.num_fin,";
		$sql .= " t.num_ult,";
		$sql .= " t.num_aprob,";
		$sql .= " t.type,";
		$sql .= " t.active,";
		$sql .= " t.date_val,";
		$sql .= " t.num_autoriz,";
		$sql .= " t.cod_control,";
		$sql .= " t.lote,";
		$sql .= " t.chave,";
		$sql .= " t.descrip,";
		$sql .= " t.activity,";
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
		    $sql .= " AND entity IN (" . getEntity("vdosinghistory", 1) . ")";
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
				$line = new VdosinghistoryLine();

				$line->id = $obj->rowid;
				
				$line->fk_dosing = $obj->fk_dosing;
				$line->entity = $obj->entity;
				$line->fk_subsidiaryid = $obj->fk_subsidiaryid;
				$line->series = $obj->series;
				$line->num_ini = $obj->num_ini;
				$line->num_fin = $obj->num_fin;
				$line->num_ult = $obj->num_ult;
				$line->num_aprob = $obj->num_aprob;
				$line->type = $obj->type;
				$line->active = $obj->active;
				$line->date_val = $this->db->jdate($obj->date_val);
				$line->num_autoriz = $obj->num_autoriz;
				$line->cod_control = $obj->cod_control;
				$line->lote = $obj->lote;
				$line->chave = $obj->chave;
				$line->descrip = $obj->descrip;
				$line->activity = $obj->activity;
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
		
		if (isset($this->fk_dosing)) {
			 $this->fk_dosing = trim($this->fk_dosing);
		}
		if (isset($this->entity)) {
			 $this->entity = trim($this->entity);
		}
		if (isset($this->fk_subsidiaryid)) {
			 $this->fk_subsidiaryid = trim($this->fk_subsidiaryid);
		}
		if (isset($this->series)) {
			 $this->series = trim($this->series);
		}
		if (isset($this->num_ini)) {
			 $this->num_ini = trim($this->num_ini);
		}
		if (isset($this->num_fin)) {
			 $this->num_fin = trim($this->num_fin);
		}
		if (isset($this->num_ult)) {
			 $this->num_ult = trim($this->num_ult);
		}
		if (isset($this->num_aprob)) {
			 $this->num_aprob = trim($this->num_aprob);
		}
		if (isset($this->type)) {
			 $this->type = trim($this->type);
		}
		if (isset($this->active)) {
			 $this->active = trim($this->active);
		}
		if (isset($this->num_autoriz)) {
			 $this->num_autoriz = trim($this->num_autoriz);
		}
		if (isset($this->cod_control)) {
			 $this->cod_control = trim($this->cod_control);
		}
		if (isset($this->lote)) {
			 $this->lote = trim($this->lote);
		}
		if (isset($this->chave)) {
			 $this->chave = trim($this->chave);
		}
		if (isset($this->descrip)) {
			 $this->descrip = trim($this->descrip);
		}
		if (isset($this->activity)) {
			 $this->activity = trim($this->activity);
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
		
		$sql .= ' fk_dosing = '.(isset($this->fk_dosing)?$this->fk_dosing:"null").',';
		$sql .= ' entity = '.(isset($this->entity)?$this->entity:"null").',';
		$sql .= ' fk_subsidiaryid = '.(isset($this->fk_subsidiaryid)?$this->fk_subsidiaryid:"null").',';
		$sql .= ' series = '.(isset($this->series)?"'".$this->db->escape($this->series)."'":"null").',';
		$sql .= ' num_ini = '.(isset($this->num_ini)?$this->num_ini:"null").',';
		$sql .= ' num_fin = '.(isset($this->num_fin)?$this->num_fin:"null").',';
		$sql .= ' num_ult = '.(isset($this->num_ult)?$this->num_ult:"null").',';
		$sql .= ' num_aprob = '.(isset($this->num_aprob)?"'".$this->db->escape($this->num_aprob)."'":"null").',';
		$sql .= ' type = '.(isset($this->type)?$this->type:"null").',';
		$sql .= ' active = '.(isset($this->active)?$this->active:"null").',';
		$sql .= ' date_val = '.(! isset($this->date_val) || dol_strlen($this->date_val) != 0 ? "'".$this->db->idate($this->date_val)."'" : 'null').',';
		$sql .= ' num_autoriz = '.(isset($this->num_autoriz)?"'".$this->db->escape($this->num_autoriz)."'":"null").',';
		$sql .= ' cod_control = '.(isset($this->cod_control)?"'".$this->db->escape($this->cod_control)."'":"null").',';
		$sql .= ' lote = '.(isset($this->lote)?$this->lote:"null").',';
		$sql .= ' chave = '.(isset($this->chave)?"'".$this->db->escape($this->chave)."'":"null").',';
		$sql .= ' descrip = '.(isset($this->descrip)?"'".$this->db->escape($this->descrip)."'":"null").',';
		$sql .= ' activity = '.(isset($this->activity)?"'".$this->db->escape($this->activity)."'":"null").',';
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
		$object = new Vdosinghistory($this->db);

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

        $url = DOL_URL_ROOT.'/fiscal/'.$this->table_name.'_card.php?id='.$this->id;

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
		
		$this->fk_dosing = '';
		$this->entity = '';
		$this->fk_subsidiaryid = '';
		$this->series = '';
		$this->num_ini = '';
		$this->num_fin = '';
		$this->num_ult = '';
		$this->num_aprob = '';
		$this->type = '';
		$this->active = '';
		$this->date_val = '';
		$this->num_autoriz = '';
		$this->cod_control = '';
		$this->lote = '';
		$this->chave = '';
		$this->descrip = '';
		$this->activity = '';
		$this->fk_user_create = '';
		$this->fk_user_mod = '';
		$this->datec = '';
		$this->datem = '';
		$this->tms = '';
		$this->status = '';

		
	}

}

/**
 * Class VdosinghistoryLine
 */
class VdosinghistoryLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	
	public $fk_dosing;
	public $entity;
	public $fk_subsidiaryid;
	public $series;
	public $num_ini;
	public $num_fin;
	public $num_ult;
	public $num_aprob;
	public $type;
	public $active;
	public $date_val = '';
	public $num_autoriz;
	public $cod_control;
	public $lote;
	public $chave;
	public $descrip;
	public $activity;
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
