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
 * \file    salary/pcontract.class.php
 * \ingroup salary
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Pcontract
 *
 * Put here description of your class
 *
 * @see CommonObject
 */
class Pcontract extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'pcontract';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'p_contract';

	/**
	 * @var PcontractLine[] Lines
	 */
	public $lines = array();

	/**
	 */

	public $ref;
	public $fk_user;
	public $fk_departament;
	public $fk_charge;
	public $fk_regional;
	public $fk_proces;
	public $fk_cc;
	public $fk_account;
	public $fk_unit;
	public $number_item;
	public $date_ini = '';
	public $date_fin = '';
	public $basic;
	public $basic_fixed;
	public $unit_cost;
	public $nivel;
	public $bonus_old;
	public $hours;
	public $nua_afp;
	public $afp;
	public $fk_user_create;
	public $fk_user_mod;
	public $date_create = '';
	public $date_mod = '';
	public $tms = '';
	public $state;

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

		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->fk_user)) {
			 $this->fk_user = trim($this->fk_user);
		}
		if (isset($this->fk_departament)) {
			 $this->fk_departament = trim($this->fk_departament);
		}
		if (isset($this->fk_charge)) {
			 $this->fk_charge = trim($this->fk_charge);
		}
		if (isset($this->fk_regional)) {
			 $this->fk_regional = trim($this->fk_regional);
		}
		if (isset($this->fk_proces)) {
			 $this->fk_proces = trim($this->fk_proces);
		}
		if (isset($this->fk_cc)) {
			 $this->fk_cc = trim($this->fk_cc);
		}
		if (isset($this->fk_account)) {
			 $this->fk_account = trim($this->fk_account);
		}
		if (isset($this->fk_unit)) {
			 $this->fk_unit = trim($this->fk_unit);
		}
		if (isset($this->number_item)) {
			 $this->number_item = trim($this->number_item);
		}
		if (isset($this->basic)) {
			 $this->basic = trim($this->basic);
		}
		if (isset($this->basic_fixed)) {
			 $this->basic_fixed = trim($this->basic_fixed);
		}
		if (isset($this->unit_cost)) {
			 $this->unit_cost = trim($this->unit_cost);
		}
		if (isset($this->nivel)) {
			 $this->nivel = trim($this->nivel);
		}
		if (isset($this->bonus_old)) {
			 $this->bonus_old = trim($this->bonus_old);
		}
		if (isset($this->hours)) {
			 $this->hours = trim($this->hours);
		}
		if (isset($this->nua_afp)) {
			 $this->nua_afp = trim($this->nua_afp);
		}
		if (isset($this->afp)) {
			 $this->afp = trim($this->afp);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_mod)) {
			 $this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->state)) {
			 $this->state = trim($this->state);
		}



		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';

		$sql.= 'ref,';
		$sql.= 'fk_user,';
		$sql.= 'fk_departament,';
		$sql.= 'fk_charge,';
		$sql.= 'fk_regional,';
		$sql.= 'fk_proces,';
		$sql.= 'fk_cc,';
		$sql.= 'fk_account,';
		$sql.= 'fk_unit,';
		$sql.= 'number_item,';
		$sql.= 'date_ini,';
		$sql.= 'date_fin,';
		$sql.= 'basic,';
		$sql.= 'basic_fixed,';
		$sql.= 'unit_cost,';
		$sql.= 'nivel,';
		$sql.= 'bonus_old,';
		$sql.= 'hours,';
		$sql.= 'nua_afp,';
		$sql.= 'afp,';
		$sql.= 'fk_user_create,';
		$sql.= 'fk_user_mod,';
		$sql.= 'date_create,';
		$sql.= 'date_mod,';
		$sql.= 'state';


		$sql .= ') VALUES (';

		$sql .= ' '.(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql .= ' '.(! isset($this->fk_user)?'NULL':$this->fk_user).',';
		$sql .= ' '.(! isset($this->fk_departament)?'NULL':$this->fk_departament).',';
		$sql .= ' '.(! isset($this->fk_charge)?'NULL':$this->fk_charge).',';
		$sql .= ' '.(! isset($this->fk_regional)?'NULL':$this->fk_regional).',';
		$sql .= ' '.(! isset($this->fk_proces)?'NULL':$this->fk_proces).',';
		$sql .= ' '.(! isset($this->fk_cc)?'NULL':$this->fk_cc).',';
		$sql .= ' '.(! isset($this->fk_account)?'NULL':$this->fk_account).',';
		$sql .= ' '.(! isset($this->fk_unit)?'NULL':$this->fk_unit).',';
		$sql .= ' '.(! isset($this->number_item)?'NULL':"'".$this->db->escape($this->number_item)."'").',';
		$sql .= ' '.(! isset($this->date_ini) || dol_strlen($this->date_ini)==0?'NULL':"'".$this->db->idate($this->date_ini)."'").',';
		$sql .= ' '.(! isset($this->date_fin) || dol_strlen($this->date_fin)==0?'NULL':"'".$this->db->idate($this->date_fin)."'").',';
		$sql .= ' '.(! isset($this->basic)?'NULL':"'".$this->basic."'").',';
		$sql .= ' '.(! isset($this->basic_fixed)?'NULL':"'".$this->basic_fixed."'").',';
		$sql .= ' '.(! isset($this->unit_cost)?'NULL':"'".$this->unit_cost."'").',';
		$sql .= ' '.(! isset($this->nivel)?'NULL':"'".$this->db->escape($this->nivel)."'").',';
		$sql .= ' '.(! isset($this->bonus_old)?'NULL':$this->bonus_old).',';
		$sql .= ' '.(! isset($this->hours)?'NULL':$this->hours).',';
		$sql .= ' '.(! isset($this->nua_afp)?'NULL':$this->nua_afp).',';
		$sql .= ' '.(! isset($this->afp)?'NULL':"'".$this->db->escape($this->afp)."'").',';
		$sql .= ' '.(! isset($this->fk_user_create)?'NULL':$this->fk_user_create).',';
		$sql .= ' '.$user->id.',';
		$sql .= ' '.(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':"'".$this->db->idate($this->date_create)."'").',';
		$sql .= ' '.(! isset($this->date_mod) || dol_strlen($this->date_mod)==0?'NULL':"'".$this->db->idate($this->date_mod)."'").',';
		$sql .= ' '.(! isset($this->state)?'NULL':$this->state);


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

		$sql .= " t.ref,";
		$sql .= " t.fk_user,";
		$sql .= " t.fk_departament,";
		$sql .= " t.fk_charge,";
		$sql .= " t.fk_regional,";
		$sql .= " t.fk_proces,";
		$sql .= " t.fk_cc,";
		$sql .= " t.fk_account,";
		$sql .= " t.fk_unit,";
		$sql .= " t.number_item,";
		$sql .= " t.date_ini,";
		$sql .= " t.date_fin,";
		$sql .= " t.basic,";
		$sql .= " t.basic_fixed,";
		$sql .= " t.unit_cost,";
		$sql .= " t.nivel,";
		$sql .= " t.bonus_old,";
		$sql .= " t.hours,";
		$sql .= " t.nua_afp,";
		$sql .= " t.afp,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.date_create,";
		$sql .= " t.date_mod,";
		$sql .= " t.tms,";
		$sql .= " t.state";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
		    $sql .= " AND entity IN (" . getEntity("pcontract", 1) . ")";
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

				$this->ref = $obj->ref;
				$this->fk_user = $obj->fk_user;
				$this->fk_departament = $obj->fk_departament;
				$this->fk_charge = $obj->fk_charge;
				$this->fk_regional = $obj->fk_regional;
				$this->fk_proces = $obj->fk_proces;
				$this->fk_cc = $obj->fk_cc;
				$this->fk_account = $obj->fk_account;
				$this->fk_unit = $obj->fk_unit;
				$this->number_item = $obj->number_item;
				$this->date_ini = $this->db->jdate($obj->date_ini);
				$this->date_fin = $this->db->jdate($obj->date_fin);
				$this->basic = $obj->basic;
				$this->basic_fixed = $obj->basic_fixed;
				$this->unit_cost = $obj->unit_cost;
				$this->nivel = $obj->nivel;
				$this->bonus_old = $obj->bonus_old;
				$this->hours = $obj->hours;
				$this->nua_afp = $obj->nua_afp;
				$this->afp = $obj->afp;
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_mod = $obj->fk_user_mod;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->date_mod = $this->db->jdate($obj->date_mod);
				$this->tms = $this->db->jdate($obj->tms);
				$this->state = $obj->state;


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
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND', $filterstatic='', $lView=false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.ref,";
		$sql .= " t.fk_user,";
		$sql .= " t.fk_departament,";
		$sql .= " t.fk_charge,";
		$sql .= " t.fk_regional,";
		$sql .= " t.fk_proces,";
		$sql .= " t.fk_cc,";
		$sql .= " t.fk_account,";
		$sql .= " t.fk_unit,";
		$sql .= " t.number_item,";
		$sql .= " t.date_ini,";
		$sql .= " t.date_fin,";
		$sql .= " t.basic,";
		$sql .= " t.basic_fixed,";
		$sql .= " t.unit_cost,";
		$sql .= " t.nivel,";
		$sql .= " t.bonus_old,";
		$sql .= " t.hours,";
		$sql .= " t.nua_afp,";
		$sql .= " t.afp,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.date_create,";
		$sql .= " t.date_mod,";
		$sql .= " t.tms,";
		$sql .= " t.state";


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
		    $sql .= " AND entity IN (" . getEntity("pcontract", 1) . ")";
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
				$line = new PcontractLine();

				$line->id = $obj->rowid;

				$line->ref = $obj->ref;
				$line->fk_user = $obj->fk_user;
				$line->fk_departament = $obj->fk_departament;
				$line->fk_charge = $obj->fk_charge;
				$line->fk_regional = $obj->fk_regional;
				$line->fk_proces = $obj->fk_proces;
				$line->fk_cc = $obj->fk_cc;
				$line->fk_account = $obj->fk_account;
				$line->fk_unit = $obj->fk_unit;
				$line->number_item = $obj->number_item;
				$line->date_ini = $this->db->jdate($obj->date_ini);
				$line->date_fin = $this->db->jdate($obj->date_fin);
				$line->basic = $obj->basic;
				$line->basic_fixed = $obj->basic_fixed;
				$line->unit_cost = $obj->unit_cost;
				$line->nivel = $obj->nivel;
				$line->bonus_old = $obj->bonus_old;
				$line->hours = $obj->hours;
				$line->nua_afp = $obj->nua_afp;
				$line->afp = $obj->afp;
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->date_mod = $this->db->jdate($obj->date_mod);
				$line->tms = $this->db->jdate($obj->tms);
				$line->state = $obj->state;



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

		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->fk_user)) {
			 $this->fk_user = trim($this->fk_user);
		}
		if (isset($this->fk_departament)) {
			 $this->fk_departament = trim($this->fk_departament);
		}
		if (isset($this->fk_charge)) {
			 $this->fk_charge = trim($this->fk_charge);
		}
		if (isset($this->fk_regional)) {
			 $this->fk_regional = trim($this->fk_regional);
		}
		if (isset($this->fk_proces)) {
			 $this->fk_proces = trim($this->fk_proces);
		}
		if (isset($this->fk_cc)) {
			 $this->fk_cc = trim($this->fk_cc);
		}
		if (isset($this->fk_account)) {
			 $this->fk_account = trim($this->fk_account);
		}
		if (isset($this->fk_unit)) {
			 $this->fk_unit = trim($this->fk_unit);
		}
		if (isset($this->number_item)) {
			 $this->number_item = trim($this->number_item);
		}
		if (isset($this->basic)) {
			 $this->basic = trim($this->basic);
		}
		if (isset($this->basic_fixed)) {
			 $this->basic_fixed = trim($this->basic_fixed);
		}
		if (isset($this->unit_cost)) {
			 $this->unit_cost = trim($this->unit_cost);
		}
		if (isset($this->nivel)) {
			 $this->nivel = trim($this->nivel);
		}
		if (isset($this->bonus_old)) {
			 $this->bonus_old = trim($this->bonus_old);
		}
		if (isset($this->hours)) {
			 $this->hours = trim($this->hours);
		}
		if (isset($this->nua_afp)) {
			 $this->nua_afp = trim($this->nua_afp);
		}
		if (isset($this->afp)) {
			 $this->afp = trim($this->afp);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_mod)) {
			 $this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->state)) {
			 $this->state = trim($this->state);
		}



		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';

		$sql .= ' ref = '.(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").',';
		$sql .= ' fk_user = '.(isset($this->fk_user)?$this->fk_user:"null").',';
		$sql .= ' fk_departament = '.(isset($this->fk_departament)?$this->fk_departament:"null").',';
		$sql .= ' fk_charge = '.(isset($this->fk_charge)?$this->fk_charge:"null").',';
		$sql .= ' fk_regional = '.(isset($this->fk_regional)?$this->fk_regional:"null").',';
		$sql .= ' fk_proces = '.(isset($this->fk_proces)?$this->fk_proces:"null").',';
		$sql .= ' fk_cc = '.(isset($this->fk_cc)?$this->fk_cc:"null").',';
		$sql .= ' fk_account = '.(isset($this->fk_account)?$this->fk_account:"null").',';
		$sql .= ' fk_unit = '.(isset($this->fk_unit)?$this->fk_unit:"null").',';
		$sql .= ' number_item = '.(isset($this->number_item)?"'".$this->db->escape($this->number_item)."'":"null").',';
		$sql .= ' date_ini = '.(! isset($this->date_ini) || dol_strlen($this->date_ini) != 0 ? "'".$this->db->idate($this->date_ini)."'" : 'null').',';
		$sql .= ' date_fin = '.(! isset($this->date_fin) || dol_strlen($this->date_fin) != 0 ? "'".$this->db->idate($this->date_fin)."'" : 'null').',';
		$sql .= ' basic = '.(isset($this->basic)?$this->basic:"null").',';
		$sql .= ' basic_fixed = '.(isset($this->basic_fixed)?$this->basic_fixed:"null").',';
		$sql .= ' unit_cost = '.(isset($this->unit_cost)?$this->unit_cost:"null").',';
		$sql .= ' nivel = '.(isset($this->nivel)?"'".$this->db->escape($this->nivel)."'":"null").',';
		$sql .= ' bonus_old = '.(isset($this->bonus_old)?$this->bonus_old:"null").',';
		$sql .= ' hours = '.(isset($this->hours)?$this->hours:"null").',';
		$sql .= ' nua_afp = '.(isset($this->nua_afp)?$this->nua_afp:"null").',';
		$sql .= ' afp = '.(isset($this->afp)?"'".$this->db->escape($this->afp)."'":"null").',';
		$sql .= ' fk_user_create = '.(isset($this->fk_user_create)?$this->fk_user_create:"null").',';
		$sql .= ' fk_user_mod = '.$user->id.',';
		$sql .= ' date_create = '.(! isset($this->date_create) || dol_strlen($this->date_create) != 0 ? "'".$this->db->idate($this->date_create)."'" : 'null').',';
		$sql .= ' date_mod = '.(! isset($this->date_mod) || dol_strlen($this->date_mod) != 0 ? "'".$this->db->idate($this->date_mod)."'" : 'null').',';
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
		$sql .= ' state = '.(isset($this->state)?$this->state:"null");


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
		$object = new Pcontract($this->db);

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

        $url = DOL_URL_ROOT.'/salary/'.$this->table_name.'_card.php?id='.$this->id;

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

		$this->ref = '';
		$this->fk_user = '';
		$this->fk_departament = '';
		$this->fk_charge = '';
		$this->fk_regional = '';
		$this->fk_proces = '';
		$this->fk_cc = '';
		$this->fk_account = '';
		$this->fk_unit = '';
		$this->number_item = '';
		$this->date_ini = '';
		$this->date_fin = '';
		$this->basic = '';
		$this->basic_fixed = '';
		$this->unit_cost = '';
		$this->nivel = '';
		$this->bonus_old = '';
		$this->hours = '';
		$this->nua_afp = '';
		$this->afp = '';
		$this->fk_user_create = '';
		$this->fk_user_mod = '';
		$this->date_create = '';
		$this->date_mod = '';
		$this->tms = '';
		$this->state = '';


	}

}

/**
 * Class PcontractLine
 */
class PcontractLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */

	public $ref;
	public $fk_user;
	public $fk_departament;
	public $fk_charge;
	public $fk_regional;
	public $fk_proces;
	public $fk_cc;
	public $fk_account;
	public $fk_unit;
	public $number_item;
	public $date_ini = '';
	public $date_fin = '';
	public $basic;
	public $basic_fixed;
	public $unit_cost;
	public $nivel;
	public $bonus_old;
	public $hours;
	public $nua_afp;
	public $afp;
	public $fk_user_create;
	public $fk_user_mod;
	public $date_create = '';
	public $date_mod = '';
	public $tms = '';
	public $state;

	/**
	 * @var mixed Sample line property 2
	 */

}
