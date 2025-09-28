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
 * \file    finint/requestcashdeplacement.class.php
 * \ingroup finint
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Requestcashdeplacement
 *
 * Put here description of your class
 * @see CommonObject
 */
class Requestcashdeplacement extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'requestcashdeplacement';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'request_cash_deplacement';

	/**
	 * @var RequestcashdeplacementLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	
	public $entity;
	public $ref;
	public $fk_request_cash;
	public $fk_request_cash_dest;
	public $fk_projet_dest;
	public $fk_account_from;
	public $fk_account_dest;
	public $url_id;
	public $fk_bank;
	public $fk_user_from;
	public $fk_user_to;
	public $fk_type;
	public $fk_categorie;
	public $dateo = '';
	public $quant;
	public $fk_unit;
	public $nro_chq;
	public $amount;
	public $concept;
	public $detail;
	public $fk_user_create;
	public $fk_user_approved;
	public $date_dest = '';
	public $date_create = '';
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
		if (isset($this->fk_request_cash)) {
			 $this->fk_request_cash = trim($this->fk_request_cash);
		}
		if (isset($this->fk_request_cash_dest)) {
			 $this->fk_request_cash_dest = trim($this->fk_request_cash_dest);
		}
		if (isset($this->fk_projet_dest)) {
			 $this->fk_projet_dest = trim($this->fk_projet_dest);
		}
		if (isset($this->fk_account_from)) {
			 $this->fk_account_from = trim($this->fk_account_from);
		}
		if (isset($this->fk_account_dest)) {
			 $this->fk_account_dest = trim($this->fk_account_dest);
		}
		if (isset($this->url_id)) {
			 $this->url_id = trim($this->url_id);
		}
		if (isset($this->fk_bank)) {
			 $this->fk_bank = trim($this->fk_bank);
		}
		if (isset($this->fk_user_from)) {
			 $this->fk_user_from = trim($this->fk_user_from);
		}
		if (isset($this->fk_user_to)) {
			 $this->fk_user_to = trim($this->fk_user_to);
		}
		if (isset($this->fk_type)) {
			 $this->fk_type = trim($this->fk_type);
		}
		if (isset($this->fk_categorie)) {
			 $this->fk_categorie = trim($this->fk_categorie);
		}
		if (isset($this->quant)) {
			 $this->quant = trim($this->quant);
		}
		if (isset($this->fk_unit)) {
			 $this->fk_unit = trim($this->fk_unit);
		}
		if (isset($this->nro_chq)) {
			 $this->nro_chq = trim($this->nro_chq);
		}
		if (isset($this->amount)) {
			 $this->amount = trim($this->amount);
		}
		if (isset($this->concept)) {
			 $this->concept = trim($this->concept);
		}
		if (isset($this->detail)) {
			 $this->detail = trim($this->detail);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_approved)) {
			 $this->fk_user_approved = trim($this->fk_user_approved);
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
		$sql.= 'fk_request_cash,';
		$sql.= 'fk_request_cash_dest,';
		$sql.= 'fk_projet_dest,';
		$sql.= 'fk_account_from,';
		$sql.= 'fk_account_dest,';
		$sql.= 'url_id,';
		$sql.= 'fk_bank,';
		$sql.= 'fk_user_from,';
		$sql.= 'fk_user_to,';
		$sql.= 'fk_type,';
		$sql.= 'fk_categorie,';
		$sql.= 'dateo,';
		$sql.= 'quant,';
		$sql.= 'fk_unit,';
		$sql.= 'nro_chq,';
		$sql.= 'amount,';
		$sql.= 'concept,';
		$sql.= 'detail,';
		$sql.= 'fk_user_create,';
		$sql.= 'fk_user_approved,';
		$sql.= 'date_dest,';
		$sql.= 'date_create,';
		$sql.= 'status';

		
		$sql .= ') VALUES (';
		
		$sql .= ' '.(! isset($this->entity)?'NULL':$this->entity).',';
		$sql .= ' '.(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql .= ' '.(! isset($this->fk_request_cash)?'NULL':$this->fk_request_cash).',';
		$sql .= ' '.(! isset($this->fk_request_cash_dest)?'NULL':$this->fk_request_cash_dest).',';
		$sql .= ' '.(! isset($this->fk_projet_dest)?'NULL':$this->fk_projet_dest).',';
		$sql .= ' '.(! isset($this->fk_account_from)?'NULL':$this->fk_account_from).',';
		$sql .= ' '.(! isset($this->fk_account_dest)?'NULL':$this->fk_account_dest).',';
		$sql .= ' '.(! isset($this->url_id)?'NULL':$this->url_id).',';
		$sql .= ' '.(! isset($this->fk_bank)?'NULL':$this->fk_bank).',';
		$sql .= ' '.(! isset($this->fk_user_from)?'NULL':$this->fk_user_from).',';
		$sql .= ' '.(! isset($this->fk_user_to)?'NULL':$this->fk_user_to).',';
		$sql .= ' '.(! isset($this->fk_type)?'NULL':"'".$this->db->escape($this->fk_type)."'").',';
		$sql .= ' '.(! isset($this->fk_categorie)?'NULL':$this->fk_categorie).',';
		$sql .= ' '.(! isset($this->dateo) || dol_strlen($this->dateo)==0?'NULL':"'".$this->db->idate($this->dateo)."'").',';
		$sql .= ' '.(! isset($this->quant)?'NULL':"'".$this->quant."'").',';
		$sql .= ' '.(! isset($this->fk_unit)?'NULL':$this->fk_unit).',';
		$sql .= ' '.(! isset($this->nro_chq)?'NULL':"'".$this->db->escape($this->nro_chq)."'").',';
		$sql .= ' '.(! isset($this->amount)?'NULL':"'".$this->amount."'").',';
		$sql .= ' '.(! isset($this->concept)?'NULL':"'".$this->db->escape($this->concept)."'").',';
		$sql .= ' '.(! isset($this->detail)?'NULL':"'".$this->db->escape($this->detail)."'").',';
		$sql .= ' '.(! isset($this->fk_user_create)?'NULL':$this->fk_user_create).',';
		$sql .= ' '.(! isset($this->fk_user_approved)?'NULL':$this->fk_user_approved).',';
		$sql .= ' '.(! isset($this->date_dest) || dol_strlen($this->date_dest)==0?'NULL':"'".$this->db->idate($this->date_dest)."'").',';
		$sql .= ' '.(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':"'".$this->db->idate($this->date_create)."'").',';
		$sql .= ' '.(! isset($this->status)?'NULL':$this->status);

		
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
		$sql .= " t.fk_request_cash,";
		$sql .= " t.fk_request_cash_dest,";
		$sql .= " t.fk_projet_dest,";
		$sql .= " t.fk_account_from,";
		$sql .= " t.fk_account_dest,";
		$sql .= " t.url_id,";
		$sql .= " t.fk_bank,";
		$sql .= " t.fk_user_from,";
		$sql .= " t.fk_user_to,";
		$sql .= " t.fk_type,";
		$sql .= " t.fk_categorie,";
		$sql .= " t.dateo,";
		$sql .= " t.quant,";
		$sql .= " t.fk_unit,";
		$sql .= " t.nro_chq,";
		$sql .= " t.amount,";
		$sql .= " t.concept,";
		$sql .= " t.detail,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_approved,";
		$sql .= " t.date_dest,";
		$sql .= " t.date_create,";
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
				$this->fk_request_cash = $obj->fk_request_cash;
				$this->fk_request_cash_dest = $obj->fk_request_cash_dest;
				$this->fk_projet_dest = $obj->fk_projet_dest;
				$this->fk_account_from = $obj->fk_account_from;
				$this->fk_account_dest = $obj->fk_account_dest;
				$this->url_id = $obj->url_id;
				$this->fk_bank = $obj->fk_bank;
				$this->fk_user_from = $obj->fk_user_from;
				$this->fk_user_to = $obj->fk_user_to;
				$this->fk_type = $obj->fk_type;
				$this->fk_categorie = $obj->fk_categorie;
				$this->dateo = $this->db->jdate($obj->dateo);
				$this->quant = $obj->quant;
				$this->fk_unit = $obj->fk_unit;
				$this->nro_chq = $obj->nro_chq;
				$this->amount = $obj->amount;
				$this->concept = $obj->concept;
				$this->detail = $obj->detail;
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_approved = $obj->fk_user_approved;
				$this->date_dest = $this->db->jdate($obj->date_dest);
				$this->date_create = $this->db->jdate($obj->date_create);
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
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lView=false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		
		$sql .= " t.entity,";
		$sql .= " t.ref,";
		$sql .= " t.fk_request_cash,";
		$sql .= " t.fk_request_cash_dest,";
		$sql .= " t.fk_projet_dest,";
		$sql .= " t.fk_account_from,";
		$sql .= " t.fk_account_dest,";
		$sql .= " t.url_id,";
		$sql .= " t.fk_bank,";
		$sql .= " t.fk_user_from,";
		$sql .= " t.fk_user_to,";
		$sql .= " t.fk_type,";
		$sql .= " t.fk_categorie,";
		$sql .= " t.dateo,";
		$sql .= " t.quant,";
		$sql .= " t.fk_unit,";
		$sql .= " t.nro_chq,";
		$sql .= " t.amount,";
		$sql .= " t.concept,";
		$sql .= " t.detail,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_approved,";
		$sql .= " t.date_dest,";
		$sql .= " t.date_create,";
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
				$line = new RequestcashdeplacementLine();

				$line->id = $obj->rowid;
				
				$line->entity = $obj->entity;
				$line->ref = $obj->ref;
				$line->fk_request_cash = $obj->fk_request_cash;
				$line->fk_request_cash_dest = $obj->fk_request_cash_dest;
				$line->fk_projet_dest = $obj->fk_projet_dest;
				$line->fk_account_from = $obj->fk_account_from;
				$line->fk_account_dest = $obj->fk_account_dest;
				$line->url_id = $obj->url_id;
				$line->fk_bank = $obj->fk_bank;
				$line->fk_user_from = $obj->fk_user_from;
				$line->fk_user_to = $obj->fk_user_to;
				$line->fk_type = $obj->fk_type;
				$line->fk_categorie = $obj->fk_categorie;
				$line->dateo = $this->db->jdate($obj->dateo);
				$line->quant = $obj->quant;
				$line->fk_unit = $obj->fk_unit;
				$line->nro_chq = $obj->nro_chq;
				$line->amount = $obj->amount;
				$line->concept = $obj->concept;
				$line->detail = $obj->detail;
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_user_approved = $obj->fk_user_approved;
				$line->date_dest = $this->db->jdate($obj->date_dest);
				$line->date_create = $this->db->jdate($obj->date_create);
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
		if (isset($this->fk_request_cash)) {
			 $this->fk_request_cash = trim($this->fk_request_cash);
		}
		if (isset($this->fk_request_cash_dest)) {
			 $this->fk_request_cash_dest = trim($this->fk_request_cash_dest);
		}
		if (isset($this->fk_projet_dest)) {
			 $this->fk_projet_dest = trim($this->fk_projet_dest);
		}
		if (isset($this->fk_account_from)) {
			 $this->fk_account_from = trim($this->fk_account_from);
		}
		if (isset($this->fk_account_dest)) {
			 $this->fk_account_dest = trim($this->fk_account_dest);
		}
		if (isset($this->url_id)) {
			 $this->url_id = trim($this->url_id);
		}
		if (isset($this->fk_bank)) {
			 $this->fk_bank = trim($this->fk_bank);
		}
		if (isset($this->fk_user_from)) {
			 $this->fk_user_from = trim($this->fk_user_from);
		}
		if (isset($this->fk_user_to)) {
			 $this->fk_user_to = trim($this->fk_user_to);
		}
		if (isset($this->fk_type)) {
			 $this->fk_type = trim($this->fk_type);
		}
		if (isset($this->fk_categorie)) {
			 $this->fk_categorie = trim($this->fk_categorie);
		}
		if (isset($this->quant)) {
			 $this->quant = trim($this->quant);
		}
		if (isset($this->fk_unit)) {
			 $this->fk_unit = trim($this->fk_unit);
		}
		if (isset($this->nro_chq)) {
			 $this->nro_chq = trim($this->nro_chq);
		}
		if (isset($this->amount)) {
			 $this->amount = trim($this->amount);
		}
		if (isset($this->concept)) {
			 $this->concept = trim($this->concept);
		}
		if (isset($this->detail)) {
			 $this->detail = trim($this->detail);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_approved)) {
			 $this->fk_user_approved = trim($this->fk_user_approved);
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
		$sql .= ' fk_request_cash = '.(isset($this->fk_request_cash)?$this->fk_request_cash:"null").',';
		$sql .= ' fk_request_cash_dest = '.(isset($this->fk_request_cash_dest)?$this->fk_request_cash_dest:"null").',';
		$sql .= ' fk_projet_dest = '.(isset($this->fk_projet_dest)?$this->fk_projet_dest:"null").',';
		$sql .= ' fk_account_from = '.(isset($this->fk_account_from)?$this->fk_account_from:"null").',';
		$sql .= ' fk_account_dest = '.(isset($this->fk_account_dest)?$this->fk_account_dest:"null").',';
		$sql .= ' url_id = '.(isset($this->url_id)?$this->url_id:"null").',';
		$sql .= ' fk_bank = '.(isset($this->fk_bank)?$this->fk_bank:"null").',';
		$sql .= ' fk_user_from = '.(isset($this->fk_user_from)?$this->fk_user_from:"null").',';
		$sql .= ' fk_user_to = '.(isset($this->fk_user_to)?$this->fk_user_to:"null").',';
		$sql .= ' fk_type = '.(isset($this->fk_type)?"'".$this->db->escape($this->fk_type)."'":"null").',';
		$sql .= ' fk_categorie = '.(isset($this->fk_categorie)?$this->fk_categorie:"null").',';
		$sql .= ' dateo = '.(! isset($this->dateo) || dol_strlen($this->dateo) != 0 ? "'".$this->db->idate($this->dateo)."'" : 'null').',';
		$sql .= ' quant = '.(isset($this->quant)?$this->quant:"null").',';
		$sql .= ' fk_unit = '.(isset($this->fk_unit)?$this->fk_unit:"null").',';
		$sql .= ' nro_chq = '.(isset($this->nro_chq)?"'".$this->db->escape($this->nro_chq)."'":"null").',';
		$sql .= ' amount = '.(isset($this->amount)?$this->amount:"null").',';
		$sql .= ' concept = '.(isset($this->concept)?"'".$this->db->escape($this->concept)."'":"null").',';
		$sql .= ' detail = '.(isset($this->detail)?"'".$this->db->escape($this->detail)."'":"null").',';
		$sql .= ' fk_user_create = '.(isset($this->fk_user_create)?$this->fk_user_create:"null").',';
		$sql .= ' fk_user_approved = '.(isset($this->fk_user_approved)?$this->fk_user_approved:"null").',';
		$sql .= ' date_dest = '.(! isset($this->date_dest) || dol_strlen($this->date_dest) != 0 ? "'".$this->db->idate($this->date_dest)."'" : 'null').',';
		$sql .= ' date_create = '.(! isset($this->date_create) || dol_strlen($this->date_create) != 0 ? "'".$this->db->idate($this->date_create)."'" : 'null').',';
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
		$sql .= ' status = '.(isset($this->status)?$this->status:"null");

        
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
		$object = new Requestcashdeplacement($this->db);

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

        if ($conf->project->enabled)
        {
        	require_once DOL_DOCUMENT_ROOT.'/project/class/projet.class.php';
        }
        $result = '';
        $companylink = '';

        $label = '<u>' . $langs->trans("Discharges") . '</u>';
        $label.= '<div width="100%">';
        $label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

        if ($conf->projet->enabled && $this->fk_projet_dest > 0)
        {
        	$objproj = new Project($this->db);
        	$objproj->fetch($this->fk_projet_dest);
        	$label.='<br><b>'.$langs->trans('Project') .':</b> '.$objproj->ref;
        }
        $link = '<a href="'.DOL_URL_ROOT.'/finint/card.php?id='.$this->id.'"';
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
		$this->fk_request_cash = '';
		$this->fk_request_cash_dest = '';
		$this->fk_projet_dest = '';
		$this->fk_account_from = '';
		$this->fk_account_dest = '';
		$this->url_id = '';
		$this->fk_bank = '';
		$this->fk_user_from = '';
		$this->fk_user_to = '';
		$this->fk_type = '';
		$this->fk_categorie = '';
		$this->dateo = '';
		$this->quant = '';
		$this->fk_unit = '';
		$this->nro_chq = '';
		$this->amount = '';
		$this->concept = '';
		$this->detail = '';
		$this->fk_user_create = '';
		$this->fk_user_approved = '';
		$this->date_dest = '';
		$this->date_create = '';
		$this->tms = '';
		$this->status = '';

		
	}

}

/**
 * Class RequestcashdeplacementLine
 */
class RequestcashdeplacementLine
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
	public $fk_request_cash;
	public $fk_request_cash_dest;
	public $fk_projet_dest;
	public $fk_account_from;
	public $fk_account_dest;
	public $url_id;
	public $fk_bank;
	public $fk_user_from;
	public $fk_user_to;
	public $fk_type;
	public $fk_categorie;
	public $dateo = '';
	public $quant;
	public $fk_unit;
	public $nro_chq;
	public $amount;
	public $concept;
	public $detail;
	public $fk_user_create;
	public $fk_user_approved;
	public $date_dest = '';
	public $date_create = '';
	public $tms = '';
	public $status;

	/**
	 * @var mixed Sample line property 2
	 */
	
}
