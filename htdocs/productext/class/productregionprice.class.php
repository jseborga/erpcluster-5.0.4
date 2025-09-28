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
 * \file    productext/productregionprice.class.php
 * \ingroup productext
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Productregionprice
 *
 * Put here description of your class
 *
 * @see CommonObject
 */
class Productregionprice extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'productregionprice';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'product_region_price';

	/**
	 * @var ProductregionpriceLine[] Lines
	 */
	public $lines = array();

	/**
	 */

	public $entity;
	public $fk_region_geographic;
	public $fk_soc;
	public $fk_supplier_proposal_det;
	public $date_create = '';
	public $datec = '';
	public $tms = '';
	public $fk_product;
	public $price;
	public $quantity;
	public $remise_percent;
	public $remise;
	public $tva_tx;
	public $default_vat_code;
	public $info_bits;
	public $fk_user;
	public $fk_user_create;
	public $fk_user_mod;
	public $datem = '';
	public $import_key;

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
		if (isset($this->fk_region_geographic)) {
			 $this->fk_region_geographic = trim($this->fk_region_geographic);
		}
		if (isset($this->fk_soc)) {
			 $this->fk_soc = trim($this->fk_soc);
		}
		if (isset($this->fk_supplier_proposal_det)) {
			 $this->fk_supplier_proposal_det = trim($this->fk_supplier_proposal_det);
		}
		if (isset($this->fk_product)) {
			 $this->fk_product = trim($this->fk_product);
		}
		if (isset($this->price)) {
			 $this->price = trim($this->price);
		}
		if (isset($this->quantity)) {
			 $this->quantity = trim($this->quantity);
		}
		if (isset($this->remise_percent)) {
			 $this->remise_percent = trim($this->remise_percent);
		}
		if (isset($this->remise)) {
			 $this->remise = trim($this->remise);
		}
		if (isset($this->tva_tx)) {
			 $this->tva_tx = trim($this->tva_tx);
		}
		if (isset($this->default_vat_code)) {
			 $this->default_vat_code = trim($this->default_vat_code);
		}
		if (isset($this->info_bits)) {
			 $this->info_bits = trim($this->info_bits);
		}
		if (isset($this->fk_user)) {
			 $this->fk_user = trim($this->fk_user);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_mod)) {
			 $this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->import_key)) {
			 $this->import_key = trim($this->import_key);
		}



		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';

		$sql.= 'entity,';
		$sql.= 'fk_region_geographic,';
		$sql.= 'fk_soc,';
		$sql.= 'fk_supplier_proposal_det,';
		$sql.= 'date_create,';
		$sql.= 'datec,';
		$sql.= 'fk_product,';
		$sql.= 'price,';
		$sql.= 'quantity,';
		$sql.= 'remise_percent,';
		$sql.= 'remise,';
		$sql.= 'tva_tx,';
		$sql.= 'default_vat_code,';
		$sql.= 'info_bits,';
		$sql.= 'fk_user,';
		$sql.= 'fk_user_create,';
		$sql.= 'fk_user_mod,';
		$sql.= 'datem,';
		$sql.= 'import_key';


		$sql .= ') VALUES (';

		$sql .= ' '.(! isset($this->entity)?'NULL':$this->entity).',';
		$sql .= ' '.(! isset($this->fk_region_geographic)?'NULL':$this->fk_region_geographic).',';
		$sql .= ' '.(! isset($this->fk_soc)?'NULL':$this->fk_soc).',';
		$sql .= ' '.(! isset($this->fk_supplier_proposal_det)?'NULL':$this->fk_supplier_proposal_det).',';
		$sql .= ' '.(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':"'".$this->db->idate($this->date_create)."'").',';
		$sql .= ' '."'".$this->db->idate(dol_now())."'".',';
		$sql .= ' '.(! isset($this->fk_product)?'NULL':$this->fk_product).',';
		$sql .= ' '.(! isset($this->price)?'NULL':"'".$this->price."'").',';
		$sql .= ' '.(! isset($this->quantity)?'NULL':"'".$this->quantity."'").',';
		$sql .= ' '.(! isset($this->remise_percent)?'NULL':"'".$this->remise_percent."'").',';
		$sql .= ' '.(! isset($this->remise)?'NULL':"'".$this->remise."'").',';
		$sql .= ' '.(! isset($this->tva_tx)?'NULL':"'".$this->tva_tx."'").',';
		$sql .= ' '.(! isset($this->default_vat_code)?'NULL':"'".$this->db->escape($this->default_vat_code)."'").',';
		$sql .= ' '.(! isset($this->info_bits)?'NULL':$this->info_bits).',';
		$sql .= ' '.(! isset($this->fk_user)?'NULL':$this->fk_user).',';
		$sql .= ' '.(! isset($this->fk_user_create)?'NULL':$this->fk_user_create).',';
		$sql .= ' '.$user->id.',';
		$sql .= ' '.(! isset($this->datem) || dol_strlen($this->datem)==0?'NULL':"'".$this->db->idate($this->datem)."'").',';
		$sql .= ' '.(! isset($this->import_key)?'NULL':"'".$this->db->escape($this->import_key)."'");


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
		$sql .= " t.fk_region_geographic,";
		$sql .= " t.fk_soc,";
		$sql .= " t.fk_supplier_proposal_det,";
		$sql .= " t.date_create,";
		$sql .= " t.datec,";
		$sql .= " t.tms,";
		$sql .= " t.fk_product,";
		$sql .= " t.price,";
		$sql .= " t.quantity,";
		$sql .= " t.remise_percent,";
		$sql .= " t.remise,";
		$sql .= " t.tva_tx,";
		$sql .= " t.default_vat_code,";
		$sql .= " t.info_bits,";
		$sql .= " t.fk_user,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.datem,";
		$sql .= " t.import_key";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
		    $sql .= " AND entity IN (" . getEntity("productregionprice", 1) . ")";
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
				$this->fk_region_geographic = $obj->fk_region_geographic;
				$this->fk_soc = $obj->fk_soc;
				$this->fk_supplier_proposal_det = $obj->fk_supplier_proposal_det;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->datec = $this->db->jdate($obj->datec);
				$this->tms = $this->db->jdate($obj->tms);
				$this->fk_product = $obj->fk_product;
				$this->price = $obj->price;
				$this->quantity = $obj->quantity;
				$this->remise_percent = $obj->remise_percent;
				$this->remise = $obj->remise;
				$this->tva_tx = $obj->tva_tx;
				$this->default_vat_code = $obj->default_vat_code;
				$this->info_bits = $obj->info_bits;
				$this->fk_user = $obj->fk_user;
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_mod = $obj->fk_user_mod;
				$this->datem = $this->db->jdate($obj->datem);
				$this->import_key = $obj->import_key;


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
		$sql .= " t.fk_region_geographic,";
		$sql .= " t.fk_soc,";
		$sql .= " t.fk_supplier_proposal_det,";
		$sql .= " t.date_create,";
		$sql .= " t.datec,";
		$sql .= " t.tms,";
		$sql .= " t.fk_product,";
		$sql .= " t.price,";
		$sql .= " t.quantity,";
		$sql .= " t.remise_percent,";
		$sql .= " t.remise,";
		$sql .= " t.tva_tx,";
		$sql .= " t.default_vat_code,";
		$sql .= " t.info_bits,";
		$sql .= " t.fk_user,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.datem,";
		$sql .= " t.import_key";


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
		    $sql .= " AND entity IN (" . getEntity("productregionprice", 1) . ")";
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
				$line = new ProductregionpriceLine();

				$line->id = $obj->rowid;

				$line->entity = $obj->entity;
				$line->fk_region_geographic = $obj->fk_region_geographic;
				$line->fk_soc = $obj->fk_soc;
				$line->fk_supplier_proposal_det = $obj->fk_supplier_proposal_det;
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->datec = $this->db->jdate($obj->datec);
				$line->tms = $this->db->jdate($obj->tms);
				$line->fk_product = $obj->fk_product;
				$line->price = $obj->price;
				$line->quantity = $obj->quantity;
				$line->remise_percent = $obj->remise_percent;
				$line->remise = $obj->remise;
				$line->tva_tx = $obj->tva_tx;
				$line->default_vat_code = $obj->default_vat_code;
				$line->info_bits = $obj->info_bits;
				$line->fk_user = $obj->fk_user;
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->datem = $this->db->jdate($obj->datem);
				$line->import_key = $obj->import_key;



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
		if (isset($this->fk_region_geographic)) {
			 $this->fk_region_geographic = trim($this->fk_region_geographic);
		}
		if (isset($this->fk_soc)) {
			 $this->fk_soc = trim($this->fk_soc);
		}
		if (isset($this->fk_supplier_proposal_det)) {
			 $this->fk_supplier_proposal_det = trim($this->fk_supplier_proposal_det);
		}
		if (isset($this->fk_product)) {
			 $this->fk_product = trim($this->fk_product);
		}
		if (isset($this->price)) {
			 $this->price = trim($this->price);
		}
		if (isset($this->quantity)) {
			 $this->quantity = trim($this->quantity);
		}
		if (isset($this->remise_percent)) {
			 $this->remise_percent = trim($this->remise_percent);
		}
		if (isset($this->remise)) {
			 $this->remise = trim($this->remise);
		}
		if (isset($this->tva_tx)) {
			 $this->tva_tx = trim($this->tva_tx);
		}
		if (isset($this->default_vat_code)) {
			 $this->default_vat_code = trim($this->default_vat_code);
		}
		if (isset($this->info_bits)) {
			 $this->info_bits = trim($this->info_bits);
		}
		if (isset($this->fk_user)) {
			 $this->fk_user = trim($this->fk_user);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_mod)) {
			 $this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->import_key)) {
			 $this->import_key = trim($this->import_key);
		}



		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';

		$sql .= ' entity = '.(isset($this->entity)?$this->entity:"null").',';
		$sql .= ' fk_region_geographic = '.(isset($this->fk_region_geographic)?$this->fk_region_geographic:"null").',';
		$sql .= ' fk_soc = '.(isset($this->fk_soc)?$this->fk_soc:"null").',';
		$sql .= ' fk_supplier_proposal_det = '.(isset($this->fk_supplier_proposal_det)?$this->fk_supplier_proposal_det:"null").',';
		$sql .= ' date_create = '.(! isset($this->date_create) || dol_strlen($this->date_create) != 0 ? "'".$this->db->idate($this->date_create)."'" : 'null').',';
		$sql .= ' datec = '.(! isset($this->datec) || dol_strlen($this->datec) != 0 ? "'".$this->db->idate($this->datec)."'" : 'null').',';
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
		$sql .= ' fk_product = '.(isset($this->fk_product)?$this->fk_product:"null").',';
		$sql .= ' price = '.(isset($this->price)?$this->price:"null").',';
		$sql .= ' quantity = '.(isset($this->quantity)?$this->quantity:"null").',';
		$sql .= ' remise_percent = '.(isset($this->remise_percent)?$this->remise_percent:"null").',';
		$sql .= ' remise = '.(isset($this->remise)?$this->remise:"null").',';
		$sql .= ' tva_tx = '.(isset($this->tva_tx)?$this->tva_tx:"null").',';
		$sql .= ' default_vat_code = '.(isset($this->default_vat_code)?"'".$this->db->escape($this->default_vat_code)."'":"null").',';
		$sql .= ' info_bits = '.(isset($this->info_bits)?$this->info_bits:"null").',';
		$sql .= ' fk_user = '.(isset($this->fk_user)?$this->fk_user:"null").',';
		$sql .= ' fk_user_create = '.(isset($this->fk_user_create)?$this->fk_user_create:"null").',';
		$sql .= ' fk_user_mod = '.$user->id.',';
		$sql .= ' datem = '.(! isset($this->datem) || dol_strlen($this->datem) != 0 ? "'".$this->db->idate($this->datem)."'" : 'null').',';
		$sql .= ' import_key = '.(isset($this->import_key)?"'".$this->db->escape($this->import_key)."'":"null");


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
		$object = new Productregionprice($this->db);

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

        $url = DOL_URL_ROOT.'/productext/'.$this->table_name.'_card.php?id='.$this->id;

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
		$this->fk_region_geographic = '';
		$this->fk_soc = '';
		$this->fk_supplier_proposal_det = '';
		$this->date_create = '';
		$this->datec = '';
		$this->tms = '';
		$this->fk_product = '';
		$this->price = '';
		$this->quantity = '';
		$this->remise_percent = '';
		$this->remise = '';
		$this->tva_tx = '';
		$this->default_vat_code = '';
		$this->info_bits = '';
		$this->fk_user = '';
		$this->fk_user_create = '';
		$this->fk_user_mod = '';
		$this->datem = '';
		$this->import_key = '';


	}

}

/**
 * Class ProductregionpriceLine
 */
class ProductregionpriceLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */

	public $entity;
	public $fk_region_geographic;
	public $fk_soc;
	public $fk_supplier_proposal_det;
	public $date_create = '';
	public $datec = '';
	public $tms = '';
	public $fk_product;
	public $price;
	public $quantity;
	public $remise_percent;
	public $remise;
	public $tva_tx;
	public $default_vat_code;
	public $info_bits;
	public $fk_user;
	public $fk_user_create;
	public $fk_user_mod;
	public $datem = '';
	public $import_key;

	/**
	 * @var mixed Sample line property 2
	 */

}
