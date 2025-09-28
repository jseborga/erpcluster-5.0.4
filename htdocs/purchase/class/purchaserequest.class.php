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
 * \file    purchase/purchaserequest.class.php
 * \ingroup purchase
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Purchaserequest
 *
 * Put here description of your class
 *
 * @see CommonObject
 */
class Purchaserequest extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'purchaserequest';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'purchase_request';

	/**
	 * @var PurchaserequestLine[] Lines
	 */
	public $lines = array();

	/**
	 */

	public $entity;
	public $ref;
	public $ref_ext;
	public $ref_int;
	public $fk_projet;
	public $fk_poa_prev;
	public $fk_departament;
	public $tms = '';
	public $datec = '';
	public $date_valid = '';
	public $date_cloture = '';
	public $fk_user_author;
	public $fk_user_modif;
	public $fk_user_valid;
	public $fk_user_cloture;
	public $note_private;
	public $note_public;
	public $model_pdf;
	public $origin;
	public $originid;
	public $date_delivery = '';
	public $date_livraison = '';
	public $fk_shipping_method;
	public $import_key;
	public $extraparams;
	public $datem = '';
	public $status;
	public $status_process;
	public $status_purchase;

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
		if (isset($this->ref_ext)) {
			 $this->ref_ext = trim($this->ref_ext);
		}
		if (isset($this->ref_int)) {
			 $this->ref_int = trim($this->ref_int);
		}
		if (isset($this->fk_projet)) {
			 $this->fk_projet = trim($this->fk_projet);
		}
		if (isset($this->fk_poa_prev)) {
			 $this->fk_poa_prev = trim($this->fk_poa_prev);
		}
		if (isset($this->fk_departament)) {
			 $this->fk_departament = trim($this->fk_departament);
		}
		if (isset($this->fk_user_author)) {
			 $this->fk_user_author = trim($this->fk_user_author);
		}
		if (isset($this->fk_user_modif)) {
			 $this->fk_user_modif = trim($this->fk_user_modif);
		}
		if (isset($this->fk_user_valid)) {
			 $this->fk_user_valid = trim($this->fk_user_valid);
		}
		if (isset($this->fk_user_cloture)) {
			 $this->fk_user_cloture = trim($this->fk_user_cloture);
		}
		if (isset($this->note_private)) {
			 $this->note_private = trim($this->note_private);
		}
		if (isset($this->note_public)) {
			 $this->note_public = trim($this->note_public);
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
		if (isset($this->fk_shipping_method)) {
			 $this->fk_shipping_method = trim($this->fk_shipping_method);
		}
		if (isset($this->import_key)) {
			 $this->import_key = trim($this->import_key);
		}
		if (isset($this->extraparams)) {
			 $this->extraparams = trim($this->extraparams);
		}
		if (isset($this->status)) {
			 $this->status = trim($this->status);
		}
		if (isset($this->status_process)) {
			 $this->status_process = trim($this->status_process);
		}
		if (isset($this->status_purchase)) {
			 $this->status_purchase = trim($this->status_purchase);
		}



		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';

		$sql.= 'entity,';
		$sql.= 'ref,';
		$sql.= 'ref_ext,';
		$sql.= 'ref_int,';
		$sql.= 'fk_projet,';
		$sql.= 'fk_poa_prev,';
		$sql.= 'fk_departament,';
		$sql.= 'datec,';
		$sql.= 'date_valid,';
		$sql.= 'date_cloture,';
		$sql.= 'fk_user_author,';
		$sql.= 'fk_user_modif,';
		$sql.= 'fk_user_valid,';
		$sql.= 'fk_user_cloture,';
		$sql.= 'note_private,';
		$sql.= 'note_public,';
		$sql.= 'model_pdf,';
		$sql.= 'origin,';
		$sql.= 'originid,';
		$sql.= 'date_delivery,';
		$sql.= 'date_livraison,';
		$sql.= 'fk_shipping_method,';
		$sql.= 'import_key,';
		$sql.= 'extraparams,';
		$sql.= 'datem,';
		$sql.= 'status,';
		$sql.= 'status_process,';
		$sql.= 'status_purchase';


		$sql .= ') VALUES (';

		$sql .= ' '.(! isset($this->entity)?'NULL':$this->entity).',';
		$sql .= ' '.(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql .= ' '.(! isset($this->ref_ext)?'NULL':"'".$this->db->escape($this->ref_ext)."'").',';
		$sql .= ' '.(! isset($this->ref_int)?'NULL':"'".$this->db->escape($this->ref_int)."'").',';
		$sql .= ' '.(! isset($this->fk_projet)?'NULL':$this->fk_projet).',';
		$sql .= ' '.(! isset($this->fk_poa_prev)?'NULL':$this->fk_poa_prev).',';
		$sql .= ' '.(! isset($this->fk_departament)?'NULL':$this->fk_departament).',';
		$sql .= ' '."'".$this->db->idate(dol_now())."'".',';
		$sql .= ' '.(! isset($this->date_valid) || dol_strlen($this->date_valid)==0?'NULL':"'".$this->db->idate($this->date_valid)."'").',';
		$sql .= ' '.(! isset($this->date_cloture) || dol_strlen($this->date_cloture)==0?'NULL':"'".$this->db->idate($this->date_cloture)."'").',';
		$sql .= ' '.$user->id.',';
		$sql .= ' '.(! isset($this->fk_user_modif)?'NULL':$this->fk_user_modif).',';
		$sql .= ' '.(! isset($this->fk_user_valid)?'NULL':$this->fk_user_valid).',';
		$sql .= ' '.(! isset($this->fk_user_cloture)?'NULL':$this->fk_user_cloture).',';
		$sql .= ' '.(! isset($this->note_private)?'NULL':"'".$this->db->escape($this->note_private)."'").',';
		$sql .= ' '.(! isset($this->note_public)?'NULL':"'".$this->db->escape($this->note_public)."'").',';
		$sql .= ' '.(! isset($this->model_pdf)?'NULL':"'".$this->db->escape($this->model_pdf)."'").',';
		$sql .= ' '.(! isset($this->origin)?'NULL':"'".$this->db->escape($this->origin)."'").',';
		$sql .= ' '.(! isset($this->originid)?'NULL':$this->originid).',';
		$sql .= ' '.(! isset($this->date_delivery) || dol_strlen($this->date_delivery)==0?'NULL':"'".$this->db->idate($this->date_delivery)."'").',';
		$sql .= ' '.(! isset($this->date_livraison) || dol_strlen($this->date_livraison)==0?'NULL':"'".$this->db->idate($this->date_livraison)."'").',';
		$sql .= ' '.(! isset($this->fk_shipping_method)?'NULL':$this->fk_shipping_method).',';
		$sql .= ' '.(! isset($this->import_key)?'NULL':"'".$this->db->escape($this->import_key)."'").',';
		$sql .= ' '.(! isset($this->extraparams)?'NULL':"'".$this->db->escape($this->extraparams)."'").',';
		$sql .= ' '.(! isset($this->datem) || dol_strlen($this->datem)==0?'NULL':"'".$this->db->idate($this->datem)."'").',';
		$sql .= ' '.(! isset($this->status)?'NULL':$this->status).',';
		$sql .= ' '.(! isset($this->status_process)?'NULL':$this->status_process).',';
		$sql .= ' '.(! isset($this->status_purchase)?'NULL':$this->status_purchase);


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
		$sql .= " t.ref_ext,";
		$sql .= " t.ref_int,";
		$sql .= " t.fk_projet,";
		$sql .= " t.fk_poa_prev,";
		$sql .= " t.fk_departament,";
		$sql .= " t.tms,";
		$sql .= " t.datec,";
		$sql .= " t.date_valid,";
		$sql .= " t.date_cloture,";
		$sql .= " t.fk_user_author,";
		$sql .= " t.fk_user_modif,";
		$sql .= " t.fk_user_valid,";
		$sql .= " t.fk_user_cloture,";
		$sql .= " t.note_private,";
		$sql .= " t.note_public,";
		$sql .= " t.model_pdf,";
		$sql .= " t.origin,";
		$sql .= " t.originid,";
		$sql .= " t.date_delivery,";
		$sql .= " t.date_livraison,";
		$sql .= " t.fk_shipping_method,";
		$sql .= " t.import_key,";
		$sql .= " t.extraparams,";
		$sql .= " t.datem,";
		$sql .= " t.status,";
		$sql .= " t.status_process,";
		$sql .= " t.status_purchase";

		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
		    $sql .= " AND entity IN (" . getEntity("purchaserequest", 1) . ")";
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
				$this->ref_ext = $obj->ref_ext;
				$this->ref_int = $obj->ref_int;
				$this->fk_projet = $obj->fk_projet;
				$this->fk_poa_prev = $obj->fk_poa_prev;
				$this->fk_departament = $obj->fk_departament;
				$this->tms = $this->db->jdate($obj->tms);
				$this->datec = $this->db->jdate($obj->datec);
				$this->date_valid = $this->db->jdate($obj->date_valid);
				$this->date_cloture = $this->db->jdate($obj->date_cloture);
				$this->fk_user_author = $obj->fk_user_author;
				$this->fk_user_modif = $obj->fk_user_modif;
				$this->fk_user_valid = $obj->fk_user_valid;
				$this->fk_user_cloture = $obj->fk_user_cloture;
				$this->note_private = $obj->note_private;
				$this->note_public = $obj->note_public;
				$this->model_pdf = $obj->model_pdf;
				$this->origin = $obj->origin;
				$this->originid = $obj->originid;
				$this->date_delivery = $this->db->jdate($obj->date_delivery);
				$this->date_livraison = $this->db->jdate($obj->date_livraison);
				$this->fk_shipping_method = $obj->fk_shipping_method;
				$this->import_key = $obj->import_key;
				$this->extraparams = $obj->extraparams;
				$this->datem = $this->db->jdate($obj->datem);
				$this->status = $obj->status;
				$this->status_process = $obj->status_process;
				$this->status_purchase = $obj->status_purchase;


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
		$sql .= " t.ref_ext,";
		$sql .= " t.ref_int,";
		$sql .= " t.fk_projet,";
		$sql .= " t.fk_poa_prev,";
		$sql .= " t.fk_departament,";
		$sql .= " t.tms,";
		$sql .= " t.datec,";
		$sql .= " t.date_valid,";
		$sql .= " t.date_cloture,";
		$sql .= " t.fk_user_author,";
		$sql .= " t.fk_user_modif,";
		$sql .= " t.fk_user_valid,";
		$sql .= " t.fk_user_cloture,";
		$sql .= " t.note_private,";
		$sql .= " t.note_public,";
		$sql .= " t.model_pdf,";
		$sql .= " t.origin,";
		$sql .= " t.originid,";
		$sql .= " t.date_delivery,";
		$sql .= " t.date_livraison,";
		$sql .= " t.fk_shipping_method,";
		$sql .= " t.import_key,";
		$sql .= " t.extraparams,";
		$sql .= " t.datem,";
		$sql .= " t.status,";
		$sql .= " t.status_process,";
		$sql .= " t.status_purchase";


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
		    $sql .= " AND entity IN (" . getEntity("purchaserequest", 1) . ")";
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
				$line = new PurchaserequestLine();

				$line->id = $obj->rowid;

				$line->entity = $obj->entity;
				$line->ref = $obj->ref;
				$line->ref_ext = $obj->ref_ext;
				$line->ref_int = $obj->ref_int;
				$line->fk_projet = $obj->fk_projet;
				$line->fk_poa_prev = $obj->fk_poa_prev;
				$line->fk_departament = $obj->fk_departament;
				$line->tms = $this->db->jdate($obj->tms);
				$line->datec = $this->db->jdate($obj->datec);
				$line->date_valid = $this->db->jdate($obj->date_valid);
				$line->date_cloture = $this->db->jdate($obj->date_cloture);
				$line->fk_user_author = $obj->fk_user_author;
				$line->fk_user_modif = $obj->fk_user_modif;
				$line->fk_user_valid = $obj->fk_user_valid;
				$line->fk_user_cloture = $obj->fk_user_cloture;
				$line->note_private = $obj->note_private;
				$line->note_public = $obj->note_public;
				$line->model_pdf = $obj->model_pdf;
				$line->origin = $obj->origin;
				$line->originid = $obj->originid;
				$line->date_delivery = $this->db->jdate($obj->date_delivery);
				$line->date_livraison = $this->db->jdate($obj->date_livraison);
				$line->fk_shipping_method = $obj->fk_shipping_method;
				$line->import_key = $obj->import_key;
				$line->extraparams = $obj->extraparams;
				$line->datem = $this->db->jdate($obj->datem);
				$line->status = $obj->status;
				$line->status_process = $obj->status_process;
				$line->status_purchase = $obj->status_purchase;



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
		if (isset($this->ref_ext)) {
			 $this->ref_ext = trim($this->ref_ext);
		}
		if (isset($this->ref_int)) {
			 $this->ref_int = trim($this->ref_int);
		}
		if (isset($this->fk_projet)) {
			 $this->fk_projet = trim($this->fk_projet);
		}
		if (isset($this->fk_poa_prev)) {
			 $this->fk_poa_prev = trim($this->fk_poa_prev);
		}
		if (isset($this->fk_departament)) {
			 $this->fk_departament = trim($this->fk_departament);
		}
		if (isset($this->fk_user_author)) {
			 $this->fk_user_author = trim($this->fk_user_author);
		}
		if (isset($this->fk_user_modif)) {
			 $this->fk_user_modif = trim($this->fk_user_modif);
		}
		if (isset($this->fk_user_valid)) {
			 $this->fk_user_valid = trim($this->fk_user_valid);
		}
		if (isset($this->fk_user_cloture)) {
			 $this->fk_user_cloture = trim($this->fk_user_cloture);
		}
		if (isset($this->note_private)) {
			 $this->note_private = trim($this->note_private);
		}
		if (isset($this->note_public)) {
			 $this->note_public = trim($this->note_public);
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
		if (isset($this->fk_shipping_method)) {
			 $this->fk_shipping_method = trim($this->fk_shipping_method);
		}
		if (isset($this->import_key)) {
			 $this->import_key = trim($this->import_key);
		}
		if (isset($this->extraparams)) {
			 $this->extraparams = trim($this->extraparams);
		}
		if (isset($this->status)) {
			 $this->status = trim($this->status);
		}
		if (isset($this->status_process)) {
			 $this->status_process = trim($this->status_process);
		}
		if (isset($this->status_purchase)) {
			 $this->status_purchase = trim($this->status_purchase);
		}



		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';

		$sql .= ' entity = '.(isset($this->entity)?$this->entity:"null").',';
		$sql .= ' ref = '.(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").',';
		$sql .= ' ref_ext = '.(isset($this->ref_ext)?"'".$this->db->escape($this->ref_ext)."'":"null").',';
		$sql .= ' ref_int = '.(isset($this->ref_int)?"'".$this->db->escape($this->ref_int)."'":"null").',';
		$sql .= ' fk_projet = '.(isset($this->fk_projet)?$this->fk_projet:"null").',';
		$sql .= ' fk_poa_prev = '.(isset($this->fk_poa_prev)?$this->fk_poa_prev:"null").',';
		$sql .= ' fk_departament = '.(isset($this->fk_departament)?$this->fk_departament:"null").',';
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
		$sql .= ' datec = '.(! isset($this->datec) || dol_strlen($this->datec) != 0 ? "'".$this->db->idate($this->datec)."'" : 'null').',';
		$sql .= ' date_valid = '.(! isset($this->date_valid) || dol_strlen($this->date_valid) != 0 ? "'".$this->db->idate($this->date_valid)."'" : 'null').',';
		$sql .= ' date_cloture = '.(! isset($this->date_cloture) || dol_strlen($this->date_cloture) != 0 ? "'".$this->db->idate($this->date_cloture)."'" : 'null').',';
		$sql .= ' fk_user_author = '.(isset($this->fk_user_author)?$this->fk_user_author:"null").',';
		$sql .= ' fk_user_modif = '.(isset($this->fk_user_modif)?$this->fk_user_modif:"null").',';
		$sql .= ' fk_user_valid = '.(isset($this->fk_user_valid)?$this->fk_user_valid:"null").',';
		$sql .= ' fk_user_cloture = '.(isset($this->fk_user_cloture)?$this->fk_user_cloture:"null").',';
		$sql .= ' note_private = '.(isset($this->note_private)?"'".$this->db->escape($this->note_private)."'":"null").',';
		$sql .= ' note_public = '.(isset($this->note_public)?"'".$this->db->escape($this->note_public)."'":"null").',';
		$sql .= ' model_pdf = '.(isset($this->model_pdf)?"'".$this->db->escape($this->model_pdf)."'":"null").',';
		$sql .= ' origin = '.(isset($this->origin)?"'".$this->db->escape($this->origin)."'":"null").',';
		$sql .= ' originid = '.(isset($this->originid)?$this->originid:"null").',';
		$sql .= ' date_delivery = '.(! isset($this->date_delivery) || dol_strlen($this->date_delivery) != 0 ? "'".$this->db->idate($this->date_delivery)."'" : 'null').',';
		$sql .= ' date_livraison = '.(! isset($this->date_livraison) || dol_strlen($this->date_livraison) != 0 ? "'".$this->db->idate($this->date_livraison)."'" : 'null').',';
		$sql .= ' fk_shipping_method = '.(isset($this->fk_shipping_method)?$this->fk_shipping_method:"null").',';
		$sql .= ' import_key = '.(isset($this->import_key)?"'".$this->db->escape($this->import_key)."'":"null").',';
		$sql .= ' extraparams = '.(isset($this->extraparams)?"'".$this->db->escape($this->extraparams)."'":"null").',';
		$sql .= ' datem = '.(! isset($this->datem) || dol_strlen($this->datem) != 0 ? "'".$this->db->idate($this->datem)."'" : 'null').',';
		$sql .= ' status = '.(isset($this->status)?$this->status:"null").',';
		$sql .= ' status_process = '.(isset($this->status_process)?$this->status_process:"null").',';
		$sql .= ' status_purchase = '.(isset($this->status_purchase)?$this->status_purchase:"null");


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
		$object = new Purchaserequest($this->db);

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

        $label = '<u>' . $langs->trans("Purchaserequest") . '</u>';
        $label.= '<br>';
        $label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

        $url = DOL_URL_ROOT.'/purchase/request/'.'card.php?id='.$this->id;

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
			if ($status == 1) return $langs->trans('Validated');
			if ($status == 0) return $langs->trans('Draft');
		}
		if ($mode == 1)
		{
			if ($status == 1) return $langs->trans('Validated');
			if ($status == 0) return $langs->trans('Draft');
		}
		if ($mode == 2)
		{
			if ($status == 1) return img_picto($langs->trans('Validated'),'statut4').' '.$langs->trans('Validated');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut5').' '.$langs->trans('Draft');
		}
		if ($mode == 3)
		{
			if ($status == 1) return img_picto($langs->trans('Validated'),'statut4');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut5');
		}
		if ($mode == 4)
		{
			if ($status == 1) return img_picto($langs->trans('Validated'),'statut4').' '.$langs->trans('Validated');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut5').' '.$langs->trans('Draft');
		}
		if ($mode == 5)
		{
			if ($status == 1) return $langs->trans('Validated').' '.img_picto($langs->trans('Validated'),'statut4');
			if ($status == 0) return $langs->trans('Draft').' '.img_picto($langs->trans('Draft'),'statut5');
		}
		if ($mode == 6)
		{
			if ($status == 1) return $langs->trans('Validated').' '.img_picto($langs->trans('Validated'),'statut4');
			if ($status == 0) return $langs->trans('Draft').' '.img_picto($langs->trans('Draft'),'statut5');
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
		$this->ref_ext = '';
		$this->ref_int = '';
		$this->fk_projet = '';
		$this->fk_poa_prev = '';
		$this->fk_departament = '';
		$this->tms = '';
		$this->datec = '';
		$this->date_valid = '';
		$this->date_cloture = '';
		$this->fk_user_author = '';
		$this->fk_user_modif = '';
		$this->fk_user_valid = '';
		$this->fk_user_cloture = '';
		$this->note_private = '';
		$this->note_public = '';
		$this->model_pdf = '';
		$this->origin = '';
		$this->originid = '';
		$this->date_delivery = '';
		$this->date_livraison = '';
		$this->fk_shipping_method = '';
		$this->import_key = '';
		$this->extraparams = '';
		$this->datem = '';
		$this->status = '';
		$this->status_process = '';
		$this->status_purchase = '';


	}

}

/**
 * Class PurchaserequestLine
 */
class PurchaserequestLine
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
	public $ref_ext;
	public $ref_int;
	public $fk_projet;
	public $fk_poa_prev;
	public $fk_departament;
	public $tms = '';
	public $datec = '';
	public $date_valid = '';
	public $date_cloture = '';
	public $fk_user_author;
	public $fk_user_modif;
	public $fk_user_valid;
	public $fk_user_cloture;
	public $note_private;
	public $note_public;
	public $model_pdf;
	public $origin;
	public $originid;
	public $date_delivery = '';
	public $date_livraison = '';
	public $fk_shipping_method;
	public $import_key;
	public $extraparams;
	public $datem = '';
	public $status;
	public $status_process;
	public $status_purchase;

	/**
	 * @var mixed Sample line property 2
	 */

}
