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
 * \file    contab/contabseatdet.class.php
 * \ingroup contab
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Contabseatdet
 *
 * Put here description of your class
 *
 * @see CommonObject
 */
class Contabseatdet extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'contabseatdet';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'contab_seat_det';

	/**
	 * @var ContabseatdetLine[] Lines
	 */
	public $lines = array();

	/**
	 */

	public $fk_contab_seat;
	public $debit_account;
	public $debit_detail;
	public $credit_account;
	public $credit_detail;
	public $fk_parent_auxd;
	public $ref_ext_auxd;
	public $fk_parent_auxc;
	public $ref_ext_auxc;
	public $codtr;
	public $codtr1;
	public $gareco;
	public $oec;
	public $fuefin;
	public $otherfin;
	public $dcd;
	public $dcc;
	public $amount;
	public $history;
	public $sequence;
	public $fk_standard_seat;
	public $type_seat;
	public $routines;
	public $value02;
	public $value03;
	public $value04;
	public $date_rate = '';
	public $rate;
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

		if (isset($this->fk_contab_seat)) {
			 $this->fk_contab_seat = trim($this->fk_contab_seat);
		}
		if (isset($this->debit_account)) {
			 $this->debit_account = trim($this->debit_account);
		}
		if (isset($this->debit_detail)) {
			 $this->debit_detail = trim($this->debit_detail);
		}
		if (isset($this->credit_account)) {
			 $this->credit_account = trim($this->credit_account);
		}
		if (isset($this->credit_detail)) {
			 $this->credit_detail = trim($this->credit_detail);
		}
		if (isset($this->fk_parent_auxd)) {
			 $this->fk_parent_auxd = trim($this->fk_parent_auxd);
		}
		if (isset($this->ref_ext_auxd)) {
			 $this->ref_ext_auxd = trim($this->ref_ext_auxd);
		}
		if (isset($this->fk_parent_auxc)) {
			 $this->fk_parent_auxc = trim($this->fk_parent_auxc);
		}
		if (isset($this->ref_ext_auxc)) {
			 $this->ref_ext_auxc = trim($this->ref_ext_auxc);
		}
		if (isset($this->codtr)) {
			 $this->codtr = trim($this->codtr);
		}
		if (isset($this->codtr1)) {
			 $this->codtr1 = trim($this->codtr1);
		}
		if (isset($this->gareco)) {
			 $this->gareco = trim($this->gareco);
		}
		if (isset($this->oec)) {
			 $this->oec = trim($this->oec);
		}
		if (isset($this->fuefin)) {
			 $this->fuefin = trim($this->fuefin);
		}
		if (isset($this->otherfin)) {
			 $this->otherfin = trim($this->otherfin);
		}
		if (isset($this->dcd)) {
			 $this->dcd = trim($this->dcd);
		}
		if (isset($this->dcc)) {
			 $this->dcc = trim($this->dcc);
		}
		if (isset($this->amount)) {
			 $this->amount = trim($this->amount);
		}
		if (isset($this->history)) {
			 $this->history = trim($this->history);
		}
		if (isset($this->sequence)) {
			 $this->sequence = trim($this->sequence);
		}
		if (isset($this->fk_standard_seat)) {
			 $this->fk_standard_seat = trim($this->fk_standard_seat);
		}
		if (isset($this->type_seat)) {
			 $this->type_seat = trim($this->type_seat);
		}
		if (isset($this->routines)) {
			 $this->routines = trim($this->routines);
		}
		if (isset($this->value02)) {
			 $this->value02 = trim($this->value02);
		}
		if (isset($this->value03)) {
			 $this->value03 = trim($this->value03);
		}
		if (isset($this->value04)) {
			 $this->value04 = trim($this->value04);
		}
		if (isset($this->rate)) {
			 $this->rate = trim($this->rate);
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

		$sql.= 'fk_contab_seat,';
		$sql.= 'debit_account,';
		$sql.= 'debit_detail,';
		$sql.= 'credit_account,';
		$sql.= 'credit_detail,';
		$sql.= 'fk_parent_auxd,';
		$sql.= 'ref_ext_auxd,';
		$sql.= 'fk_parent_auxc,';
		$sql.= 'ref_ext_auxc,';
		$sql.= 'codtr,';
		$sql.= 'codtr1,';
		$sql.= 'gareco,';
		$sql.= 'oec,';
		$sql.= 'fuefin,';
		$sql.= 'otherfin,';
		$sql.= 'dcd,';
		$sql.= 'dcc,';
		$sql.= 'amount,';
		$sql.= 'history,';
		$sql.= 'sequence,';
		$sql.= 'fk_standard_seat,';
		$sql.= 'type_seat,';
		$sql.= 'routines,';
		$sql.= 'value02,';
		$sql.= 'value03,';
		$sql.= 'value04,';
		$sql.= 'date_rate,';
		$sql.= 'rate,';
		$sql.= 'fk_user_create,';
		$sql.= 'fk_user_mod,';
		$sql.= 'datec,';
		$sql.= 'datem,';
		$sql.= 'status';


		$sql .= ') VALUES (';

		$sql .= ' '.(! isset($this->fk_contab_seat)?'NULL':$this->fk_contab_seat).',';
		$sql .= ' '.(! isset($this->debit_account)?'NULL':"'".$this->db->escape($this->debit_account)."'").',';
		$sql .= ' '.(! isset($this->debit_detail)?'NULL':"'".$this->db->escape($this->debit_detail)."'").',';
		$sql .= ' '.(! isset($this->credit_account)?'NULL':"'".$this->db->escape($this->credit_account)."'").',';
		$sql .= ' '.(! isset($this->credit_detail)?'NULL':"'".$this->db->escape($this->credit_detail)."'").',';
		$sql .= ' '.(! isset($this->fk_parent_auxd)?'NULL':$this->fk_parent_auxd).',';
		$sql .= ' '.(! isset($this->ref_ext_auxd)?'NULL':"'".$this->db->escape($this->ref_ext_auxd)."'").',';
		$sql .= ' '.(! isset($this->fk_parent_auxc)?'NULL':$this->fk_parent_auxc).',';
		$sql .= ' '.(! isset($this->ref_ext_auxc)?'NULL':"'".$this->db->escape($this->ref_ext_auxc)."'").',';
		$sql .= ' '.(! isset($this->codtr)?'NULL':"'".$this->db->escape($this->codtr)."'").',';
		$sql .= ' '.(! isset($this->codtr1)?'NULL':"'".$this->db->escape($this->codtr1)."'").',';
		$sql .= ' '.(! isset($this->gareco)?'NULL':"'".$this->db->escape($this->gareco)."'").',';
		$sql .= ' '.(! isset($this->oec)?'NULL':"'".$this->db->escape($this->oec)."'").',';
		$sql .= ' '.(! isset($this->fuefin)?'NULL':"'".$this->db->escape($this->fuefin)."'").',';
		$sql .= ' '.(! isset($this->otherfin)?'NULL':"'".$this->db->escape($this->otherfin)."'").',';
		$sql .= ' '.(! isset($this->dcd)?'NULL':$this->dcd).',';
		$sql .= ' '.(! isset($this->dcc)?'NULL':$this->dcc).',';
		$sql .= ' '.(! isset($this->amount)?'NULL':"'".$this->amount."'").',';
		$sql .= ' '.(! isset($this->history)?'NULL':"'".$this->db->escape($this->history)."'").',';
		$sql .= ' '.(! isset($this->sequence)?'NULL':"'".$this->db->escape($this->sequence)."'").',';
		$sql .= ' '.(! isset($this->fk_standard_seat)?'NULL':$this->fk_standard_seat).',';
		$sql .= ' '.(! isset($this->type_seat)?'NULL':$this->type_seat).',';
		$sql .= ' '.(! isset($this->routines)?'NULL':"'".$this->db->escape($this->routines)."'").',';
		$sql .= ' '.(! isset($this->value02)?'NULL':"'".$this->value02."'").',';
		$sql .= ' '.(! isset($this->value03)?'NULL':"'".$this->value03."'").',';
		$sql .= ' '.(! isset($this->value04)?'NULL':"'".$this->value04."'").',';
		$sql .= ' '.(! isset($this->date_rate) || dol_strlen($this->date_rate)==0?'NULL':"'".$this->db->idate($this->date_rate)."'").',';
		$sql .= ' '.(! isset($this->rate)?'NULL':"'".$this->rate."'").',';
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

		$sql .= " t.fk_contab_seat,";
		$sql .= " t.debit_account,";
		$sql .= " t.debit_detail,";
		$sql .= " t.credit_account,";
		$sql .= " t.credit_detail,";
		$sql .= " t.fk_parent_auxd,";
		$sql .= " t.ref_ext_auxd,";
		$sql .= " t.fk_parent_auxc,";
		$sql .= " t.ref_ext_auxc,";
		$sql .= " t.codtr,";
		$sql .= " t.codtr1,";
		$sql .= " t.gareco,";
		$sql .= " t.oec,";
		$sql .= " t.fuefin,";
		$sql .= " t.otherfin,";
		$sql .= " t.dcd,";
		$sql .= " t.dcc,";
		$sql .= " t.amount,";
		$sql .= " t.history,";
		$sql .= " t.sequence,";
		$sql .= " t.fk_standard_seat,";
		$sql .= " t.type_seat,";
		$sql .= " t.routines,";
		$sql .= " t.value02,";
		$sql .= " t.value03,";
		$sql .= " t.value04,";
		$sql .= " t.date_rate,";
		$sql .= " t.rate,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.datec,";
		$sql .= " t.datem,";
		$sql .= " t.tms,";
		$sql .= " t.status";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
		    $sql .= " AND entity IN (" . getEntity("contabseatdet", 1) . ")";
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

				$this->fk_contab_seat = $obj->fk_contab_seat;
				$this->debit_account = $obj->debit_account;
				$this->debit_detail = $obj->debit_detail;
				$this->credit_account = $obj->credit_account;
				$this->credit_detail = $obj->credit_detail;
				$this->fk_parent_auxd = $obj->fk_parent_auxd;
				$this->ref_ext_auxd = $obj->ref_ext_auxd;
				$this->fk_parent_auxc = $obj->fk_parent_auxc;
				$this->ref_ext_auxc = $obj->ref_ext_auxc;
				$this->codtr = $obj->codtr;
				$this->codtr1 = $obj->codtr1;
				$this->gareco = $obj->gareco;
				$this->oec = $obj->oec;
				$this->fuefin = $obj->fuefin;
				$this->otherfin = $obj->otherfin;
				$this->dcd = $obj->dcd;
				$this->dcc = $obj->dcc;
				$this->amount = $obj->amount;
				$this->history = $obj->history;
				$this->sequence = $obj->sequence;
				$this->fk_standard_seat = $obj->fk_standard_seat;
				$this->type_seat = $obj->type_seat;
				$this->routines = $obj->routines;
				$this->value02 = $obj->value02;
				$this->value03 = $obj->value03;
				$this->value04 = $obj->value04;
				$this->date_rate = $this->db->jdate($obj->date_rate);
				$this->rate = $obj->rate;
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
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND', $filterstatic='', $lView=false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.fk_contab_seat,";
		$sql .= " t.debit_account,";
		$sql .= " t.debit_detail,";
		$sql .= " t.credit_account,";
		$sql .= " t.credit_detail,";
		$sql .= " t.fk_parent_auxd,";
		$sql .= " t.ref_ext_auxd,";
		$sql .= " t.fk_parent_auxc,";
		$sql .= " t.ref_ext_auxc,";
		$sql .= " t.codtr,";
		$sql .= " t.codtr1,";
		$sql .= " t.gareco,";
		$sql .= " t.oec,";
		$sql .= " t.fuefin,";
		$sql .= " t.otherfin,";
		$sql .= " t.dcd,";
		$sql .= " t.dcc,";
		$sql .= " t.amount,";
		$sql .= " t.history,";
		$sql .= " t.sequence,";
		$sql .= " t.fk_standard_seat,";
		$sql .= " t.type_seat,";
		$sql .= " t.routines,";
		$sql .= " t.value02,";
		$sql .= " t.value03,";
		$sql .= " t.value04,";
		$sql .= " t.date_rate,";
		$sql .= " t.rate,";
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
		    $sql .= " AND entity IN (" . getEntity("contabseatdet", 1) . ")";
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
				$line = new ContabseatdetLine();

				$line->id = $obj->rowid;

				$line->fk_contab_seat = $obj->fk_contab_seat;
				$line->debit_account = $obj->debit_account;
				$line->debit_detail = $obj->debit_detail;
				$line->credit_account = $obj->credit_account;
				$line->credit_detail = $obj->credit_detail;
				$line->fk_parent_auxd = $obj->fk_parent_auxd;
				$line->ref_ext_auxd = $obj->ref_ext_auxd;
				$line->fk_parent_auxc = $obj->fk_parent_auxc;
				$line->ref_ext_auxc = $obj->ref_ext_auxc;
				$line->codtr = $obj->codtr;
				$line->codtr1 = $obj->codtr1;
				$line->gareco = $obj->gareco;
				$line->oec = $obj->oec;
				$line->fuefin = $obj->fuefin;
				$line->otherfin = $obj->otherfin;
				$line->dcd = $obj->dcd;
				$line->dcc = $obj->dcc;
				$line->amount = $obj->amount;
				$line->history = $obj->history;
				$line->sequence = $obj->sequence;
				$line->fk_standard_seat = $obj->fk_standard_seat;
				$line->type_seat = $obj->type_seat;
				$line->routines = $obj->routines;
				$line->value02 = $obj->value02;
				$line->value03 = $obj->value03;
				$line->value04 = $obj->value04;
				$line->date_rate = $this->db->jdate($obj->date_rate);
				$line->rate = $obj->rate;
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

		if (isset($this->fk_contab_seat)) {
			 $this->fk_contab_seat = trim($this->fk_contab_seat);
		}
		if (isset($this->debit_account)) {
			 $this->debit_account = trim($this->debit_account);
		}
		if (isset($this->debit_detail)) {
			 $this->debit_detail = trim($this->debit_detail);
		}
		if (isset($this->credit_account)) {
			 $this->credit_account = trim($this->credit_account);
		}
		if (isset($this->credit_detail)) {
			 $this->credit_detail = trim($this->credit_detail);
		}
		if (isset($this->fk_parent_auxd)) {
			 $this->fk_parent_auxd = trim($this->fk_parent_auxd);
		}
		if (isset($this->ref_ext_auxd)) {
			 $this->ref_ext_auxd = trim($this->ref_ext_auxd);
		}
		if (isset($this->fk_parent_auxc)) {
			 $this->fk_parent_auxc = trim($this->fk_parent_auxc);
		}
		if (isset($this->ref_ext_auxc)) {
			 $this->ref_ext_auxc = trim($this->ref_ext_auxc);
		}
		if (isset($this->codtr)) {
			 $this->codtr = trim($this->codtr);
		}
		if (isset($this->codtr1)) {
			 $this->codtr1 = trim($this->codtr1);
		}
		if (isset($this->gareco)) {
			 $this->gareco = trim($this->gareco);
		}
		if (isset($this->oec)) {
			 $this->oec = trim($this->oec);
		}
		if (isset($this->fuefin)) {
			 $this->fuefin = trim($this->fuefin);
		}
		if (isset($this->otherfin)) {
			 $this->otherfin = trim($this->otherfin);
		}
		if (isset($this->dcd)) {
			 $this->dcd = trim($this->dcd);
		}
		if (isset($this->dcc)) {
			 $this->dcc = trim($this->dcc);
		}
		if (isset($this->amount)) {
			 $this->amount = trim($this->amount);
		}
		if (isset($this->history)) {
			 $this->history = trim($this->history);
		}
		if (isset($this->sequence)) {
			 $this->sequence = trim($this->sequence);
		}
		if (isset($this->fk_standard_seat)) {
			 $this->fk_standard_seat = trim($this->fk_standard_seat);
		}
		if (isset($this->type_seat)) {
			 $this->type_seat = trim($this->type_seat);
		}
		if (isset($this->routines)) {
			 $this->routines = trim($this->routines);
		}
		if (isset($this->value02)) {
			 $this->value02 = trim($this->value02);
		}
		if (isset($this->value03)) {
			 $this->value03 = trim($this->value03);
		}
		if (isset($this->value04)) {
			 $this->value04 = trim($this->value04);
		}
		if (isset($this->rate)) {
			 $this->rate = trim($this->rate);
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

		$sql .= ' fk_contab_seat = '.(isset($this->fk_contab_seat)?$this->fk_contab_seat:"null").',';
		$sql .= ' debit_account = '.(isset($this->debit_account)?"'".$this->db->escape($this->debit_account)."'":"null").',';
		$sql .= ' debit_detail = '.(isset($this->debit_detail)?"'".$this->db->escape($this->debit_detail)."'":"null").',';
		$sql .= ' credit_account = '.(isset($this->credit_account)?"'".$this->db->escape($this->credit_account)."'":"null").',';
		$sql .= ' credit_detail = '.(isset($this->credit_detail)?"'".$this->db->escape($this->credit_detail)."'":"null").',';
		$sql .= ' fk_parent_auxd = '.(isset($this->fk_parent_auxd)?$this->fk_parent_auxd:"null").',';
		$sql .= ' ref_ext_auxd = '.(isset($this->ref_ext_auxd)?"'".$this->db->escape($this->ref_ext_auxd)."'":"null").',';
		$sql .= ' fk_parent_auxc = '.(isset($this->fk_parent_auxc)?$this->fk_parent_auxc:"null").',';
		$sql .= ' ref_ext_auxc = '.(isset($this->ref_ext_auxc)?"'".$this->db->escape($this->ref_ext_auxc)."'":"null").',';
		$sql .= ' codtr = '.(isset($this->codtr)?"'".$this->db->escape($this->codtr)."'":"null").',';
		$sql .= ' codtr1 = '.(isset($this->codtr1)?"'".$this->db->escape($this->codtr1)."'":"null").',';
		$sql .= ' gareco = '.(isset($this->gareco)?"'".$this->db->escape($this->gareco)."'":"null").',';
		$sql .= ' oec = '.(isset($this->oec)?"'".$this->db->escape($this->oec)."'":"null").',';
		$sql .= ' fuefin = '.(isset($this->fuefin)?"'".$this->db->escape($this->fuefin)."'":"null").',';
		$sql .= ' otherfin = '.(isset($this->otherfin)?"'".$this->db->escape($this->otherfin)."'":"null").',';
		$sql .= ' dcd = '.(isset($this->dcd)?$this->dcd:"null").',';
		$sql .= ' dcc = '.(isset($this->dcc)?$this->dcc:"null").',';
		$sql .= ' amount = '.(isset($this->amount)?$this->amount:"null").',';
		$sql .= ' history = '.(isset($this->history)?"'".$this->db->escape($this->history)."'":"null").',';
		$sql .= ' sequence = '.(isset($this->sequence)?"'".$this->db->escape($this->sequence)."'":"null").',';
		$sql .= ' fk_standard_seat = '.(isset($this->fk_standard_seat)?$this->fk_standard_seat:"null").',';
		$sql .= ' type_seat = '.(isset($this->type_seat)?$this->type_seat:"null").',';
		$sql .= ' routines = '.(isset($this->routines)?"'".$this->db->escape($this->routines)."'":"null").',';
		$sql .= ' value02 = '.(isset($this->value02)?$this->value02:"null").',';
		$sql .= ' value03 = '.(isset($this->value03)?$this->value03:"null").',';
		$sql .= ' value04 = '.(isset($this->value04)?$this->value04:"null").',';
		$sql .= ' date_rate = '.(! isset($this->date_rate) || dol_strlen($this->date_rate) != 0 ? "'".$this->db->idate($this->date_rate)."'" : 'null').',';
		$sql .= ' rate = '.(isset($this->rate)?$this->rate:"null").',';
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
		$object = new Contabseatdet($this->db);

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

        $url = DOL_URL_ROOT.'/contab/'.$this->table_name.'_card.php?id='.$this->id;

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

		$this->fk_contab_seat = '';
		$this->debit_account = '';
		$this->debit_detail = '';
		$this->credit_account = '';
		$this->credit_detail = '';
		$this->fk_parent_auxd = '';
		$this->ref_ext_auxd = '';
		$this->fk_parent_auxc = '';
		$this->ref_ext_auxc = '';
		$this->codtr = '';
		$this->codtr1 = '';
		$this->gareco = '';
		$this->oec = '';
		$this->fuefin = '';
		$this->otherfin = '';
		$this->dcd = '';
		$this->dcc = '';
		$this->amount = '';
		$this->history = '';
		$this->sequence = '';
		$this->fk_standard_seat = '';
		$this->type_seat = '';
		$this->routines = '';
		$this->value02 = '';
		$this->value03 = '';
		$this->value04 = '';
		$this->date_rate = '';
		$this->rate = '';
		$this->fk_user_create = '';
		$this->fk_user_mod = '';
		$this->datec = '';
		$this->datem = '';
		$this->tms = '';
		$this->status = '';


	}

}

/**
 * Class ContabseatdetLine
 */
class ContabseatdetLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */

	public $fk_contab_seat;
	public $debit_account;
	public $debit_detail;
	public $credit_account;
	public $credit_detail;
	public $fk_parent_auxd;
	public $ref_ext_auxd;
	public $fk_parent_auxc;
	public $ref_ext_auxc;
	public $codtr;
	public $codtr1;
	public $gareco;
	public $oec;
	public $fuefin;
	public $otherfin;
	public $dcd;
	public $dcc;
	public $amount;
	public $history;
	public $sequence;
	public $fk_standard_seat;
	public $type_seat;
	public $routines;
	public $value02;
	public $value03;
	public $value04;
	public $date_rate = '';
	public $rate;
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
