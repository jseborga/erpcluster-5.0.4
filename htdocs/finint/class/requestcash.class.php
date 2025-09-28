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
 * \file    finint/requestcash.class.php
 * \ingroup finint
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Requestcash
 *
 * Put here description of your class
 *
 * @see CommonObject
 */
class Requestcash extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'requestcash';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'request_cash';

	/**
	 * @var RequestcashLine[] Lines
	 */
	public $lines = array();

	/**
	 */

	public $entity;
	public $ref;
	public $fk_projet;
	public $fk_account;
	public $fk_account_from;
	public $fk_user_create;
	public $fk_user_assigned;
	public $fk_user_authorized;
	public $fk_user_approved;
	public $fk_user_mod;
	public $fk_type_cash;
	public $fk_type;
	public $fk_categorie;
	public $detail;
	public $description;
	public $document;
	public $document_discharg;
	public $amount;
	public $amount_approved;
	public $amount_authorized;
	public $amount_out;
	public $amount_close;
	public $model_pdf;
	public $nro_chq;
	public $date_create = '';
	public $date_approved = '';
	public $date_authorized = '';
	public $date_delete = '';
	public $tms = '';
	public $status_trans;
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
		if (isset($this->fk_projet)) {
			 $this->fk_projet = trim($this->fk_projet);
		}
		if (isset($this->fk_account)) {
			 $this->fk_account = trim($this->fk_account);
		}
		if (isset($this->fk_account_from)) {
			 $this->fk_account_from = trim($this->fk_account_from);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_assigned)) {
			 $this->fk_user_assigned = trim($this->fk_user_assigned);
		}
		if (isset($this->fk_user_authorized)) {
			 $this->fk_user_authorized = trim($this->fk_user_authorized);
		}
		if (isset($this->fk_user_approved)) {
			 $this->fk_user_approved = trim($this->fk_user_approved);
		}
		if (isset($this->fk_user_mod)) {
			 $this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->fk_type_cash)) {
			 $this->fk_type_cash = trim($this->fk_type_cash);
		}
		if (isset($this->fk_type)) {
			 $this->fk_type = trim($this->fk_type);
		}
		if (isset($this->fk_categorie)) {
			 $this->fk_categorie = trim($this->fk_categorie);
		}
		if (isset($this->detail)) {
			 $this->detail = trim($this->detail);
		}
		if (isset($this->description)) {
			 $this->description = trim($this->description);
		}
		if (isset($this->document)) {
			 $this->document = trim($this->document);
		}
		if (isset($this->document_discharg)) {
			 $this->document_discharg = trim($this->document_discharg);
		}
		if (isset($this->amount)) {
			 $this->amount = trim($this->amount);
		}
		if (isset($this->amount_approved)) {
			 $this->amount_approved = trim($this->amount_approved);
		}
		if (isset($this->amount_authorized)) {
			 $this->amount_authorized = trim($this->amount_authorized);
		}
		if (isset($this->amount_out)) {
			 $this->amount_out = trim($this->amount_out);
		}
		if (isset($this->amount_close)) {
			 $this->amount_close = trim($this->amount_close);
		}
		if (isset($this->model_pdf)) {
			 $this->model_pdf = trim($this->model_pdf);
		}
		if (isset($this->nro_chq)) {
			 $this->nro_chq = trim($this->nro_chq);
		}
		if (isset($this->status_trans)) {
			 $this->status_trans = trim($this->status_trans);
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
		$sql.= 'fk_projet,';
		$sql.= 'fk_account,';
		$sql.= 'fk_account_from,';
		$sql.= 'fk_user_create,';
		$sql.= 'fk_user_assigned,';
		$sql.= 'fk_user_authorized,';
		$sql.= 'fk_user_approved,';
		$sql.= 'fk_user_mod,';
		$sql.= 'fk_type_cash,';
		$sql.= 'fk_type,';
		$sql.= 'fk_categorie,';
		$sql.= 'detail,';
		$sql.= 'description,';
		$sql.= 'document,';
		$sql.= 'document_discharg,';
		$sql.= 'amount,';
		$sql.= 'amount_approved,';
		$sql.= 'amount_authorized,';
		$sql.= 'amount_out,';
		$sql.= 'amount_close,';
		$sql.= 'model_pdf,';
		$sql.= 'nro_chq,';
		$sql.= 'date_create,';
		$sql.= 'date_approved,';
		$sql.= 'date_authorized,';
		$sql.= 'date_delete,';
		$sql.= 'status_trans,';
		$sql.= 'status';


		$sql .= ') VALUES (';

		$sql .= ' '.(! isset($this->entity)?'NULL':$this->entity).',';
		$sql .= ' '.(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql .= ' '.(! isset($this->fk_projet)?'NULL':$this->fk_projet).',';
		$sql .= ' '.(! isset($this->fk_account)?'NULL':$this->fk_account).',';
		$sql .= ' '.(! isset($this->fk_account_from)?'NULL':$this->fk_account_from).',';
		$sql .= ' '.(! isset($this->fk_user_create)?'NULL':$this->fk_user_create).',';
		$sql .= ' '.(! isset($this->fk_user_assigned)?'NULL':$this->fk_user_assigned).',';
		$sql .= ' '.(! isset($this->fk_user_authorized)?'NULL':$this->fk_user_authorized).',';
		$sql .= ' '.(! isset($this->fk_user_approved)?'NULL':$this->fk_user_approved).',';
		$sql .= ' '.$user->id.',';
		$sql .= ' '.(! isset($this->fk_type_cash)?'NULL':$this->fk_type_cash).',';
		$sql .= ' '.(! isset($this->fk_type)?'NULL':"'".$this->db->escape($this->fk_type)."'").',';
		$sql .= ' '.(! isset($this->fk_categorie)?'NULL':$this->fk_categorie).',';
		$sql .= ' '.(! isset($this->detail)?'NULL':"'".$this->db->escape($this->detail)."'").',';
		$sql .= ' '.(! isset($this->description)?'NULL':"'".$this->db->escape($this->description)."'").',';
		$sql .= ' '.(! isset($this->document)?'NULL':"'".$this->db->escape($this->document)."'").',';
		$sql .= ' '.(! isset($this->document_discharg)?'NULL':"'".$this->db->escape($this->document_discharg)."'").',';
		$sql .= ' '.(! isset($this->amount)?'NULL':"'".$this->amount."'").',';
		$sql .= ' '.(! isset($this->amount_approved)?'NULL':"'".$this->amount_approved."'").',';
		$sql .= ' '.(! isset($this->amount_authorized)?'NULL':"'".$this->amount_authorized."'").',';
		$sql .= ' '.(! isset($this->amount_out)?'NULL':"'".$this->amount_out."'").',';
		$sql .= ' '.(! isset($this->amount_close)?'NULL':"'".$this->amount_close."'").',';
		$sql .= ' '.(! isset($this->model_pdf)?'NULL':"'".$this->db->escape($this->model_pdf)."'").',';
		$sql .= ' '.(! isset($this->nro_chq)?'NULL':"'".$this->db->escape($this->nro_chq)."'").',';
		$sql .= ' '.(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':"'".$this->db->idate($this->date_create)."'").',';
		$sql .= ' '.(! isset($this->date_approved) || dol_strlen($this->date_approved)==0?'NULL':"'".$this->db->idate($this->date_approved)."'").',';
		$sql .= ' '.(! isset($this->date_authorized) || dol_strlen($this->date_authorized)==0?'NULL':"'".$this->db->idate($this->date_authorized)."'").',';
		$sql .= ' '.(! isset($this->date_delete) || dol_strlen($this->date_delete)==0?'NULL':"'".$this->db->idate($this->date_delete)."'").',';
		$sql .= ' '.(! isset($this->status_trans)?'NULL':$this->status_trans).',';
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
		$sql .= " t.fk_projet,";
		$sql .= " t.fk_account,";
		$sql .= " t.fk_account_from,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_assigned,";
		$sql .= " t.fk_user_authorized,";
		$sql .= " t.fk_user_approved,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.fk_type_cash,";
		$sql .= " t.fk_type,";
		$sql .= " t.fk_categorie,";
		$sql .= " t.detail,";
		$sql .= " t.description,";
		$sql .= " t.document,";
		$sql .= " t.document_discharg,";
		$sql .= " t.amount,";
		$sql .= " t.amount_approved,";
		$sql .= " t.amount_authorized,";
		$sql .= " t.amount_out,";
		$sql .= " t.amount_close,";
		$sql .= " t.model_pdf,";
		$sql .= " t.nro_chq,";
		$sql .= " t.date_create,";
		$sql .= " t.date_approved,";
		$sql .= " t.date_authorized,";
		$sql .= " t.date_delete,";
		$sql .= " t.tms,";
		$sql .= " t.status_trans,";
		$sql .= " t.status";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
		    $sql .= " AND entity IN (" . getEntity("requestcash", 1) . ")";
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
				$this->fk_projet = $obj->fk_projet;
				$this->fk_account = $obj->fk_account;
				$this->fk_account_from = $obj->fk_account_from;
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_assigned = $obj->fk_user_assigned;
				$this->fk_user_authorized = $obj->fk_user_authorized;
				$this->fk_user_approved = $obj->fk_user_approved;
				$this->fk_user_mod = $obj->fk_user_mod;
				$this->fk_type_cash = $obj->fk_type_cash;
				$this->fk_type = $obj->fk_type;
				$this->fk_categorie = $obj->fk_categorie;
				$this->detail = $obj->detail;
				$this->description = $obj->description;
				$this->document = $obj->document;
				$this->document_discharg = $obj->document_discharg;
				$this->amount = $obj->amount;
				$this->amount_approved = $obj->amount_approved;
				$this->amount_authorized = $obj->amount_authorized;
				$this->amount_out = $obj->amount_out;
				$this->amount_close = $obj->amount_close;
				$this->model_pdf = $obj->model_pdf;
				$this->nro_chq = $obj->nro_chq;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->date_approved = $this->db->jdate($obj->date_approved);
				$this->date_authorized = $this->db->jdate($obj->date_authorized);
				$this->date_delete = $this->db->jdate($obj->date_delete);
				$this->tms = $this->db->jdate($obj->tms);
				$this->status_trans = $obj->status_trans;
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
		$sql .= " t.fk_projet,";
		$sql .= " t.fk_account,";
		$sql .= " t.fk_account_from,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_assigned,";
		$sql .= " t.fk_user_authorized,";
		$sql .= " t.fk_user_approved,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.fk_type_cash,";
		$sql .= " t.fk_type,";
		$sql .= " t.fk_categorie,";
		$sql .= " t.detail,";
		$sql .= " t.description,";
		$sql .= " t.document,";
		$sql .= " t.document_discharg,";
		$sql .= " t.amount,";
		$sql .= " t.amount_approved,";
		$sql .= " t.amount_authorized,";
		$sql .= " t.amount_out,";
		$sql .= " t.amount_close,";
		$sql .= " t.model_pdf,";
		$sql .= " t.nro_chq,";
		$sql .= " t.date_create,";
		$sql .= " t.date_approved,";
		$sql .= " t.date_authorized,";
		$sql .= " t.date_delete,";
		$sql .= " t.tms,";
		$sql .= " t.status_trans,";
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
		    $sql .= " AND entity IN (" . getEntity("requestcash", 1) . ")";
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
				$line = new RequestcashLine();

				$line->id = $obj->rowid;

				$line->entity = $obj->entity;
				$line->ref = $obj->ref;
				$line->fk_projet = $obj->fk_projet;
				$line->fk_account = $obj->fk_account;
				$line->fk_account_from = $obj->fk_account_from;
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_user_assigned = $obj->fk_user_assigned;
				$line->fk_user_authorized = $obj->fk_user_authorized;
				$line->fk_user_approved = $obj->fk_user_approved;
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->fk_type_cash = $obj->fk_type_cash;
				$line->fk_type = $obj->fk_type;
				$line->fk_categorie = $obj->fk_categorie;
				$line->detail = $obj->detail;
				$line->description = $obj->description;
				$line->document = $obj->document;
				$line->document_discharg = $obj->document_discharg;
				$line->amount = $obj->amount;
				$line->amount_approved = $obj->amount_approved;
				$line->amount_authorized = $obj->amount_authorized;
				$line->amount_out = $obj->amount_out;
				$line->amount_close = $obj->amount_close;
				$line->model_pdf = $obj->model_pdf;
				$line->nro_chq = $obj->nro_chq;
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->date_approved = $this->db->jdate($obj->date_approved);
				$line->date_authorized = $this->db->jdate($obj->date_authorized);
				$line->date_delete = $this->db->jdate($obj->date_delete);
				$line->tms = $this->db->jdate($obj->tms);
				$line->status_trans = $obj->status_trans;
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
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->fk_projet)) {
			 $this->fk_projet = trim($this->fk_projet);
		}
		if (isset($this->fk_account)) {
			 $this->fk_account = trim($this->fk_account);
		}
		if (isset($this->fk_account_from)) {
			 $this->fk_account_from = trim($this->fk_account_from);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_assigned)) {
			 $this->fk_user_assigned = trim($this->fk_user_assigned);
		}
		if (isset($this->fk_user_authorized)) {
			 $this->fk_user_authorized = trim($this->fk_user_authorized);
		}
		if (isset($this->fk_user_approved)) {
			 $this->fk_user_approved = trim($this->fk_user_approved);
		}
		if (isset($this->fk_user_mod)) {
			 $this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->fk_type_cash)) {
			 $this->fk_type_cash = trim($this->fk_type_cash);
		}
		if (isset($this->fk_type)) {
			 $this->fk_type = trim($this->fk_type);
		}
		if (isset($this->fk_categorie)) {
			 $this->fk_categorie = trim($this->fk_categorie);
		}
		if (isset($this->detail)) {
			 $this->detail = trim($this->detail);
		}
		if (isset($this->description)) {
			 $this->description = trim($this->description);
		}
		if (isset($this->document)) {
			 $this->document = trim($this->document);
		}
		if (isset($this->document_discharg)) {
			 $this->document_discharg = trim($this->document_discharg);
		}
		if (isset($this->amount)) {
			 $this->amount = trim($this->amount);
		}
		if (isset($this->amount_approved)) {
			 $this->amount_approved = trim($this->amount_approved);
		}
		if (isset($this->amount_authorized)) {
			 $this->amount_authorized = trim($this->amount_authorized);
		}
		if (isset($this->amount_out)) {
			 $this->amount_out = trim($this->amount_out);
		}
		if (isset($this->amount_close)) {
			 $this->amount_close = trim($this->amount_close);
		}
		if (isset($this->model_pdf)) {
			 $this->model_pdf = trim($this->model_pdf);
		}
		if (isset($this->nro_chq)) {
			 $this->nro_chq = trim($this->nro_chq);
		}
		if (isset($this->status_trans)) {
			 $this->status_trans = trim($this->status_trans);
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
		$sql .= ' fk_projet = '.(isset($this->fk_projet)?$this->fk_projet:"null").',';
		$sql .= ' fk_account = '.(isset($this->fk_account)?$this->fk_account:"null").',';
		$sql .= ' fk_account_from = '.(isset($this->fk_account_from)?$this->fk_account_from:"null").',';
		$sql .= ' fk_user_create = '.(isset($this->fk_user_create)?$this->fk_user_create:"null").',';
		$sql .= ' fk_user_assigned = '.(isset($this->fk_user_assigned)?$this->fk_user_assigned:"null").',';
		$sql .= ' fk_user_authorized = '.(isset($this->fk_user_authorized)?$this->fk_user_authorized:"null").',';
		$sql .= ' fk_user_approved = '.(isset($this->fk_user_approved)?$this->fk_user_approved:"null").',';
		$sql .= ' fk_user_mod = '.$user->id.',';
		$sql .= ' fk_type_cash = '.(isset($this->fk_type_cash)?$this->fk_type_cash:"null").',';
		$sql .= ' fk_type = '.(isset($this->fk_type)?"'".$this->db->escape($this->fk_type)."'":"null").',';
		$sql .= ' fk_categorie = '.(isset($this->fk_categorie)?$this->fk_categorie:"null").',';
		$sql .= ' detail = '.(isset($this->detail)?"'".$this->db->escape($this->detail)."'":"null").',';
		$sql .= ' description = '.(isset($this->description)?"'".$this->db->escape($this->description)."'":"null").',';
		$sql .= ' document = '.(isset($this->document)?"'".$this->db->escape($this->document)."'":"null").',';
		$sql .= ' document_discharg = '.(isset($this->document_discharg)?"'".$this->db->escape($this->document_discharg)."'":"null").',';
		$sql .= ' amount = '.(isset($this->amount)?$this->amount:"null").',';
		$sql .= ' amount_approved = '.(isset($this->amount_approved)?$this->amount_approved:"null").',';
		$sql .= ' amount_authorized = '.(isset($this->amount_authorized)?$this->amount_authorized:"null").',';
		$sql .= ' amount_out = '.(isset($this->amount_out)?$this->amount_out:"null").',';
		$sql .= ' amount_close = '.(isset($this->amount_close)?$this->amount_close:"null").',';
		$sql .= ' model_pdf = '.(isset($this->model_pdf)?"'".$this->db->escape($this->model_pdf)."'":"null").',';
		$sql .= ' nro_chq = '.(isset($this->nro_chq)?"'".$this->db->escape($this->nro_chq)."'":"null").',';
		$sql .= ' date_create = '.(! isset($this->date_create) || dol_strlen($this->date_create) != 0 ? "'".$this->db->idate($this->date_create)."'" : 'null').',';
		$sql .= ' date_approved = '.(! isset($this->date_approved) || dol_strlen($this->date_approved) != 0 ? "'".$this->db->idate($this->date_approved)."'" : 'null').',';
		$sql .= ' date_authorized = '.(! isset($this->date_authorized) || dol_strlen($this->date_authorized) != 0 ? "'".$this->db->idate($this->date_authorized)."'" : 'null').',';
		$sql .= ' date_delete = '.(! isset($this->date_delete) || dol_strlen($this->date_delete) != 0 ? "'".$this->db->idate($this->date_delete)."'" : 'null').',';
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
		$sql .= ' status_trans = '.(isset($this->status_trans)?$this->status_trans:"null").',';
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
		$object = new Requestcash($this->db);

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

        $label = '<u>' . $langs->trans("Requestcash") . '</u>';
        $label.= '<br>';
        $label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

        $url = DOL_URL_ROOT.'/finint/request/'.'card.php?id='.$this->id;

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
		$this->fk_projet = '';
		$this->fk_account = '';
		$this->fk_account_from = '';
		$this->fk_user_create = '';
		$this->fk_user_assigned = '';
		$this->fk_user_authorized = '';
		$this->fk_user_approved = '';
		$this->fk_user_mod = '';
		$this->fk_type_cash = '';
		$this->fk_type = '';
		$this->fk_categorie = '';
		$this->detail = '';
		$this->description = '';
		$this->document = '';
		$this->document_discharg = '';
		$this->amount = '';
		$this->amount_approved = '';
		$this->amount_authorized = '';
		$this->amount_out = '';
		$this->amount_close = '';
		$this->model_pdf = '';
		$this->nro_chq = '';
		$this->date_create = '';
		$this->date_approved = '';
		$this->date_authorized = '';
		$this->date_delete = '';
		$this->tms = '';
		$this->status_trans = '';
		$this->status = '';


	}

}

/**
 * Class RequestcashLine
 */
class RequestcashLine
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
	public $fk_projet;
	public $fk_account;
	public $fk_account_from;
	public $fk_user_create;
	public $fk_user_assigned;
	public $fk_user_authorized;
	public $fk_user_approved;
	public $fk_user_mod;
	public $fk_type_cash;
	public $fk_type;
	public $fk_categorie;
	public $detail;
	public $description;
	public $document;
	public $document_discharg;
	public $amount;
	public $amount_approved;
	public $amount_authorized;
	public $amount_out;
	public $amount_close;
	public $model_pdf;
	public $nro_chq;
	public $date_create = '';
	public $date_approved = '';
	public $date_authorized = '';
	public $date_delete = '';
	public $tms = '';
	public $status_trans;
	public $status;

	/**
	 * @var mixed Sample line property 2
	 */

}
