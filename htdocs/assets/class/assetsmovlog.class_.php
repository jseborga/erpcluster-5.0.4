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
 * \file    assets/assetsmovlog.class.php
 * \ingroup assets
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Assetsmovlog
 *
 * Put here description of your class
 *
 * @see CommonObject
 */
class Assetsmovlog extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'assetsmovlog';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'assets_mov_log';

	/**
	 * @var AssetsmovlogLine[] Lines
	 */
	public $lines = array();

	/**
	 */

	public $entity;
	public $fk_asset;
	public $ref;
	public $type_group;
	public $date_ini = '';
	public $date_end = '';
	public $factor_update;
	public $time_consumed;
	public $tcini;
	public $tcend;
	public $month_depr;
	public $coste;
	public $coste_residual;
	public $amount_base;
	public $amount_update;
	public $amount_depr;
	public $amount_depr_acum;
	public $amount_depr_acum_update;
	public $amount_balance;
	public $amount_balance_depr;
	public $amount_sale;
	public $movement_type;
	public $fk_user_create;
	public $fk_user_mod;
	public $datec = '';
	public $dateu = '';
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
		if (isset($this->fk_asset)) {
			 $this->fk_asset = trim($this->fk_asset);
		}
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->type_group)) {
			 $this->type_group = trim($this->type_group);
		}
		if (isset($this->factor_update)) {
			 $this->factor_update = trim($this->factor_update);
		}
		if (isset($this->time_consumed)) {
			 $this->time_consumed = trim($this->time_consumed);
		}
		if (isset($this->tcini)) {
			 $this->tcini = trim($this->tcini);
		}
		if (isset($this->tcend)) {
			 $this->tcend = trim($this->tcend);
		}
		if (isset($this->month_depr)) {
			 $this->month_depr = trim($this->month_depr);
		}
		if (isset($this->coste)) {
			 $this->coste = trim($this->coste);
		}
		if (isset($this->coste_residual)) {
			 $this->coste_residual = trim($this->coste_residual);
		}
		if (isset($this->amount_base)) {
			 $this->amount_base = trim($this->amount_base);
		}
		if (isset($this->amount_update)) {
			 $this->amount_update = trim($this->amount_update);
		}
		if (isset($this->amount_depr)) {
			 $this->amount_depr = trim($this->amount_depr);
		}
		if (isset($this->amount_depr_acum)) {
			 $this->amount_depr_acum = trim($this->amount_depr_acum);
		}
		if (isset($this->amount_depr_acum_update)) {
			 $this->amount_depr_acum_update = trim($this->amount_depr_acum_update);
		}
		if (isset($this->amount_balance)) {
			 $this->amount_balance = trim($this->amount_balance);
		}
		if (isset($this->amount_balance_depr)) {
			 $this->amount_balance_depr = trim($this->amount_balance_depr);
		}
		if (isset($this->amount_sale)) {
			 $this->amount_sale = trim($this->amount_sale);
		}
		if (isset($this->movement_type)) {
			 $this->movement_type = trim($this->movement_type);
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
		$sql.= 'fk_asset,';
		$sql.= 'ref,';
		$sql.= 'type_group,';
		$sql.= 'date_ini,';
		$sql.= 'date_end,';
		$sql.= 'factor_update,';
		$sql.= 'time_consumed,';
		$sql.= 'tcini,';
		$sql.= 'tcend,';
		$sql.= 'month_depr,';
		$sql.= 'coste,';
		$sql.= 'coste_residual,';
		$sql.= 'amount_base,';
		$sql.= 'amount_update,';
		$sql.= 'amount_depr,';
		$sql.= 'amount_depr_acum,';
		$sql.= 'amount_depr_acum_update,';
		$sql.= 'amount_balance,';
		$sql.= 'amount_balance_depr,';
		$sql.= 'amount_sale,';
		$sql.= 'movement_type,';
		$sql.= 'fk_user_create,';
		$sql.= 'fk_user_mod,';
		$sql.= 'datec,';
		$sql.= 'dateu,';
		$sql.= 'status';


		$sql .= ') VALUES (';

		$sql .= ' '.(! isset($this->entity)?'NULL':$this->entity).',';
		$sql .= ' '.(! isset($this->fk_asset)?'NULL':$this->fk_asset).',';
		$sql .= ' '.(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql .= ' '.(! isset($this->type_group)?'NULL':"'".$this->db->escape($this->type_group)."'").',';
		$sql .= ' '.(! isset($this->date_ini) || dol_strlen($this->date_ini)==0?'NULL':"'".$this->db->idate($this->date_ini)."'").',';
		$sql .= ' '.(! isset($this->date_end) || dol_strlen($this->date_end)==0?'NULL':"'".$this->db->idate($this->date_end)."'").',';
		$sql .= ' '.(! isset($this->factor_update)?'NULL':"'".$this->factor_update."'").',';
		$sql .= ' '.(! isset($this->time_consumed)?'NULL':"'".$this->time_consumed."'").',';
		$sql .= ' '.(! isset($this->tcini)?'NULL':"'".$this->tcini."'").',';
		$sql .= ' '.(! isset($this->tcend)?'NULL':"'".$this->tcend."'").',';
		$sql .= ' '.(! isset($this->month_depr)?'NULL':"'".$this->month_depr."'").',';
		$sql .= ' '.(! isset($this->coste)?'NULL':"'".$this->coste."'").',';
		$sql .= ' '.(! isset($this->coste_residual)?'NULL':"'".$this->coste_residual."'").',';
		$sql .= ' '.(! isset($this->amount_base)?'NULL':"'".$this->amount_base."'").',';
		$sql .= ' '.(! isset($this->amount_update)?'NULL':"'".$this->amount_update."'").',';
		$sql .= ' '.(! isset($this->amount_depr)?'NULL':"'".$this->amount_depr."'").',';
		$sql .= ' '.(! isset($this->amount_depr_acum)?'NULL':"'".$this->amount_depr_acum."'").',';
		$sql .= ' '.(! isset($this->amount_depr_acum_update)?'NULL':"'".$this->amount_depr_acum_update."'").',';
		$sql .= ' '.(! isset($this->amount_balance)?'NULL':"'".$this->amount_balance."'").',';
		$sql .= ' '.(! isset($this->amount_balance_depr)?'NULL':"'".$this->amount_balance_depr."'").',';
		$sql .= ' '.(! isset($this->amount_sale)?'NULL':"'".$this->amount_sale."'").',';
		$sql .= ' '.(! isset($this->movement_type)?'NULL':"'".$this->db->escape($this->movement_type)."'").',';
		$sql .= ' '.(! isset($this->fk_user_create)?'NULL':$this->fk_user_create).',';
		$sql .= ' '.$user->id.',';
		$sql .= ' '."'".$this->db->idate(dol_now())."'".',';
		$sql .= ' '.(! isset($this->dateu) || dol_strlen($this->dateu)==0?'NULL':"'".$this->db->idate($this->dateu)."'").',';
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
	public function fetch($id, $ref = null, $fk=0)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.entity,";
		$sql .= " t.fk_asset,";
		$sql .= " t.ref,";
		$sql .= " t.type_group,";
		$sql .= " t.date_ini,";
		$sql .= " t.date_end,";
		$sql .= " t.factor_update,";
		$sql .= " t.time_consumed,";
		$sql .= " t.tcini,";
		$sql .= " t.tcend,";
		$sql .= " t.month_depr,";
		$sql .= " t.coste,";
		$sql .= " t.coste_residual,";
		$sql .= " t.amount_base,";
		$sql .= " t.amount_update,";
		$sql .= " t.amount_depr,";
		$sql .= " t.amount_depr_acum,";
		$sql .= " t.amount_depr_acum_update,";
		$sql .= " t.amount_balance,";
		$sql .= " t.amount_balance_depr,";
		$sql .= " t.amount_sale,";
		$sql .= " t.movement_type,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.datec,";
		$sql .= " t.dateu,";
		$sql .= " t.tms,";
		$sql .= " t.status";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
		    $sql .= " AND entity IN (" . getEntity("assetsmovlog", 1) . ")";
		}
		if (null !== $ref && $fk > 0) {
			$sql .= ' AND t.ref = ' . '\'' . $ref . '\'';
			$sql .= ' AND t.fk_asset = ' . $fk;
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
				$this->ref = $obj->ref;
				$this->type_group = $obj->type_group;
				$this->date_ini = $this->db->jdate($obj->date_ini);
				$this->date_end = $this->db->jdate($obj->date_end);
				$this->factor_update = $obj->factor_update;
				$this->time_consumed = $obj->time_consumed;
				$this->tcini = $obj->tcini;
				$this->tcend = $obj->tcend;
				$this->month_depr = $obj->month_depr;
				$this->coste = $obj->coste;
				$this->coste_residual = $obj->coste_residual;
				$this->amount_base = $obj->amount_base;
				$this->amount_update = $obj->amount_update;
				$this->amount_depr = $obj->amount_depr;
				$this->amount_depr_acum = $obj->amount_depr_acum;
				$this->amount_depr_acum_update = $obj->amount_depr_acum_update;
				$this->amount_balance = $obj->amount_balance;
				$this->amount_balance_depr = $obj->amount_balance_depr;
				$this->amount_sale = $obj->amount_sale;
				$this->movement_type = $obj->movement_type;
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_mod = $obj->fk_user_mod;
				$this->datec = $this->db->jdate($obj->datec);
				$this->dateu = $this->db->jdate($obj->dateu);
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
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND', $filterstatic='',$lView=false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.entity,";
		$sql .= " t.fk_asset,";
		$sql .= " t.ref,";
		$sql .= " t.type_group,";
		$sql .= " t.date_ini,";
		$sql .= " t.date_end,";
		$sql .= " t.factor_update,";
		$sql .= " t.time_consumed,";
		$sql .= " t.tcini,";
		$sql .= " t.tcend,";
		$sql .= " t.month_depr,";
		$sql .= " t.coste,";
		$sql .= " t.coste_residual,";
		$sql .= " t.amount_base,";
		$sql .= " t.amount_update,";
		$sql .= " t.amount_depr,";
		$sql .= " t.amount_depr_acum,";
		$sql .= " t.amount_depr_acum_update,";
		$sql .= " t.amount_balance,";
		$sql .= " t.amount_balance_depr,";
		$sql .= " t.amount_sale,";
		$sql .= " t.movement_type,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.datec,";
		$sql .= " t.dateu,";
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
		    $sql .= " AND entity IN (" . getEntity("assetsmovlog", 1) . ")";
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
				$line = new AssetsmovlogLine();

				$line->id = $obj->rowid;

				$line->entity = $obj->entity;
				$line->fk_asset = $obj->fk_asset;
				$line->ref = $obj->ref;
				$line->type_group = $obj->type_group;
				$line->date_ini = $this->db->jdate($obj->date_ini);
				$line->date_end = $this->db->jdate($obj->date_end);
				$line->factor_update = $obj->factor_update;
				$line->time_consumed = $obj->time_consumed;
				$line->tcini = $obj->tcini;
				$line->tcend = $obj->tcend;
				$line->month_depr = $obj->month_depr;
				$line->coste = $obj->coste;
				$line->coste_residual = $obj->coste_residual;
				$line->amount_base = $obj->amount_base;
				$line->amount_update = $obj->amount_update;
				$line->amount_depr = $obj->amount_depr;
				$line->amount_depr_acum = $obj->amount_depr_acum;
				$line->amount_depr_acum_update = $obj->amount_depr_acum_update;
				$line->amount_balance = $obj->amount_balance;
				$line->amount_balance_depr = $obj->amount_balance_depr;
				$line->amount_sale = $obj->amount_sale;
				$line->movement_type = $obj->movement_type;
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->datec = $this->db->jdate($obj->datec);
				$line->dateu = $this->db->jdate($obj->dateu);
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

		if (isset($this->entity)) {
			 $this->entity = trim($this->entity);
		}
		if (isset($this->fk_asset)) {
			 $this->fk_asset = trim($this->fk_asset);
		}
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->type_group)) {
			 $this->type_group = trim($this->type_group);
		}
		if (isset($this->factor_update)) {
			 $this->factor_update = trim($this->factor_update);
		}
		if (isset($this->time_consumed)) {
			 $this->time_consumed = trim($this->time_consumed);
		}
		if (isset($this->tcini)) {
			 $this->tcini = trim($this->tcini);
		}
		if (isset($this->tcend)) {
			 $this->tcend = trim($this->tcend);
		}
		if (isset($this->month_depr)) {
			 $this->month_depr = trim($this->month_depr);
		}
		if (isset($this->coste)) {
			 $this->coste = trim($this->coste);
		}
		if (isset($this->coste_residual)) {
			 $this->coste_residual = trim($this->coste_residual);
		}
		if (isset($this->amount_base)) {
			 $this->amount_base = trim($this->amount_base);
		}
		if (isset($this->amount_update)) {
			 $this->amount_update = trim($this->amount_update);
		}
		if (isset($this->amount_depr)) {
			 $this->amount_depr = trim($this->amount_depr);
		}
		if (isset($this->amount_depr_acum)) {
			 $this->amount_depr_acum = trim($this->amount_depr_acum);
		}
		if (isset($this->amount_depr_acum_update)) {
			 $this->amount_depr_acum_update = trim($this->amount_depr_acum_update);
		}
		if (isset($this->amount_balance)) {
			 $this->amount_balance = trim($this->amount_balance);
		}
		if (isset($this->amount_balance_depr)) {
			 $this->amount_balance_depr = trim($this->amount_balance_depr);
		}
		if (isset($this->amount_sale)) {
			 $this->amount_sale = trim($this->amount_sale);
		}
		if (isset($this->movement_type)) {
			 $this->movement_type = trim($this->movement_type);
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
		$sql .= ' fk_asset = '.(isset($this->fk_asset)?$this->fk_asset:"null").',';
		$sql .= ' ref = '.(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").',';
		$sql .= ' type_group = '.(isset($this->type_group)?"'".$this->db->escape($this->type_group)."'":"null").',';
		$sql .= ' date_ini = '.(! isset($this->date_ini) || dol_strlen($this->date_ini) != 0 ? "'".$this->db->idate($this->date_ini)."'" : 'null').',';
		$sql .= ' date_end = '.(! isset($this->date_end) || dol_strlen($this->date_end) != 0 ? "'".$this->db->idate($this->date_end)."'" : 'null').',';
		$sql .= ' factor_update = '.(isset($this->factor_update)?$this->factor_update:"null").',';
		$sql .= ' time_consumed = '.(isset($this->time_consumed)?$this->time_consumed:"null").',';
		$sql .= ' tcini = '.(isset($this->tcini)?$this->tcini:"null").',';
		$sql .= ' tcend = '.(isset($this->tcend)?$this->tcend:"null").',';
		$sql .= ' month_depr = '.(isset($this->month_depr)?$this->month_depr:"null").',';
		$sql .= ' coste = '.(isset($this->coste)?$this->coste:"null").',';
		$sql .= ' coste_residual = '.(isset($this->coste_residual)?$this->coste_residual:"null").',';
		$sql .= ' amount_base = '.(isset($this->amount_base)?$this->amount_base:"null").',';
		$sql .= ' amount_update = '.(isset($this->amount_update)?$this->amount_update:"null").',';
		$sql .= ' amount_depr = '.(isset($this->amount_depr)?$this->amount_depr:"null").',';
		$sql .= ' amount_depr_acum = '.(isset($this->amount_depr_acum)?$this->amount_depr_acum:"null").',';
		$sql .= ' amount_depr_acum_update = '.(isset($this->amount_depr_acum_update)?$this->amount_depr_acum_update:"null").',';
		$sql .= ' amount_balance = '.(isset($this->amount_balance)?$this->amount_balance:"null").',';
		$sql .= ' amount_balance_depr = '.(isset($this->amount_balance_depr)?$this->amount_balance_depr:"null").',';
		$sql .= ' amount_sale = '.(isset($this->amount_sale)?$this->amount_sale:"null").',';
		$sql .= ' movement_type = '.(isset($this->movement_type)?"'".$this->db->escape($this->movement_type)."'":"null").',';
		$sql .= ' fk_user_create = '.(isset($this->fk_user_create)?$this->fk_user_create:"null").',';
		$sql .= ' fk_user_mod = '.$user->id.',';
		$sql .= ' datec = '.(! isset($this->datec) || dol_strlen($this->datec) != 0 ? "'".$this->db->idate($this->datec)."'" : 'null').',';
		$sql .= ' dateu = '.(! isset($this->dateu) || dol_strlen($this->dateu) != 0 ? "'".$this->db->idate($this->dateu)."'" : 'null').',';
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
		$object = new Assetsmovlog($this->db);

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
		$this->ref = '';
		$this->type_group = '';
		$this->date_ini = '';
		$this->date_end = '';
		$this->factor_update = '';
		$this->time_consumed = '';
		$this->tcini = '';
		$this->tcend = '';
		$this->month_depr = '';
		$this->coste = '';
		$this->coste_residual = '';
		$this->amount_base = '';
		$this->amount_update = '';
		$this->amount_depr = '';
		$this->amount_depr_acum = '';
		$this->amount_depr_acum_update = '';
		$this->amount_balance = '';
		$this->amount_balance_depr = '';
		$this->amount_sale = '';
		$this->movement_type = '';
		$this->fk_user_create = '';
		$this->fk_user_mod = '';
		$this->datec = '';
		$this->dateu = '';
		$this->tms = '';
		$this->status = '';


	}

}

/**
 * Class AssetsmovlogLine
 */
class AssetsmovlogLine
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
	public $ref;
	public $type_group;
	public $date_ini = '';
	public $date_end = '';
	public $factor_update;
	public $time_consumed;
	public $tcini;
	public $tcend;
	public $month_depr;
	public $coste;
	public $coste_residual;
	public $amount_base;
	public $amount_update;
	public $amount_depr;
	public $amount_depr_acum;
	public $amount_depr_acum_update;
	public $amount_balance;
	public $amount_balance_depr;
	public $amount_sale;
	public $movement_type;
	public $fk_user_create;
	public $fk_user_mod;
	public $datec = '';
	public $dateu = '';
	public $tms = '';
	public $status;

	/**
	 * @var mixed Sample line property 2
	 */

}
