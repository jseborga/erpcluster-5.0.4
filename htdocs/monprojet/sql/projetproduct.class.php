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
 * \file    monprojet/projetproduct.class.php
 * \ingroup monprojet
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Projetproduct
 *
 * Put here description of your class
 * @see CommonObject
 */
class Projetproduct extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'projetproduct';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'projet_product';

	/**
	 * @var ProjetproductLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	
	public $fk_projet;
	public $fk_product;
	public $ref;
	public $ref_ext;
	public $datec = '';
	public $tms = '';
	public $fk_parent;
	public $fk_categorie;
	public $label;
	public $description;
	public $fk_country;
	public $price;
	public $price_ttc;
	public $price_min;
	public $price_min_ttc;
	public $price_base_type;
	public $tva_tx;
	public $recuperableonly;
	public $localtax1_tx;
	public $localtax1_type;
	public $localtax2_tx;
	public $localtax2_type;
	public $fk_user_author;
	public $fk_user_modif;
	public $fk_product_type;
	public $pmp;
	public $finished;
	public $fk_unit;
	public $cost_price;
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
		
		if (isset($this->fk_projet)) {
			 $this->fk_projet = trim($this->fk_projet);
		}
		if (isset($this->fk_product)) {
			 $this->fk_product = trim($this->fk_product);
		}
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->ref_ext)) {
			 $this->ref_ext = trim($this->ref_ext);
		}
		if (isset($this->fk_parent)) {
			 $this->fk_parent = trim($this->fk_parent);
		}
		if (isset($this->fk_categorie)) {
			 $this->fk_categorie = trim($this->fk_categorie);
		}
		if (isset($this->label)) {
			 $this->label = trim($this->label);
		}
		if (isset($this->description)) {
			 $this->description = trim($this->description);
		}
		if (isset($this->fk_country)) {
			 $this->fk_country = trim($this->fk_country);
		}
		if (isset($this->price)) {
			 $this->price = trim($this->price);
		}
		if (isset($this->price_ttc)) {
			 $this->price_ttc = trim($this->price_ttc);
		}
		if (isset($this->price_min)) {
			 $this->price_min = trim($this->price_min);
		}
		if (isset($this->price_min_ttc)) {
			 $this->price_min_ttc = trim($this->price_min_ttc);
		}
		if (isset($this->price_base_type)) {
			 $this->price_base_type = trim($this->price_base_type);
		}
		if (isset($this->tva_tx)) {
			 $this->tva_tx = trim($this->tva_tx);
		}
		if (isset($this->recuperableonly)) {
			 $this->recuperableonly = trim($this->recuperableonly);
		}
		if (isset($this->localtax1_tx)) {
			 $this->localtax1_tx = trim($this->localtax1_tx);
		}
		if (isset($this->localtax1_type)) {
			 $this->localtax1_type = trim($this->localtax1_type);
		}
		if (isset($this->localtax2_tx)) {
			 $this->localtax2_tx = trim($this->localtax2_tx);
		}
		if (isset($this->localtax2_type)) {
			 $this->localtax2_type = trim($this->localtax2_type);
		}
		if (isset($this->fk_user_author)) {
			 $this->fk_user_author = trim($this->fk_user_author);
		}
		if (isset($this->fk_user_modif)) {
			 $this->fk_user_modif = trim($this->fk_user_modif);
		}
		if (isset($this->fk_product_type)) {
			 $this->fk_product_type = trim($this->fk_product_type);
		}
		if (isset($this->pmp)) {
			 $this->pmp = trim($this->pmp);
		}
		if (isset($this->finished)) {
			 $this->finished = trim($this->finished);
		}
		if (isset($this->fk_unit)) {
			 $this->fk_unit = trim($this->fk_unit);
		}
		if (isset($this->cost_price)) {
			 $this->cost_price = trim($this->cost_price);
		}
		if (isset($this->status)) {
			 $this->status = trim($this->status);
		}

		

		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';
		
		$sql.= 'fk_projet,';
		$sql.= 'fk_product,';
		$sql.= 'ref,';
		$sql.= 'ref_ext,';
		$sql.= 'datec,';
		$sql.= 'fk_parent,';
		$sql.= 'fk_categorie,';
		$sql.= 'label,';
		$sql.= 'description,';
		$sql.= 'fk_country,';
		$sql.= 'price,';
		$sql.= 'price_ttc,';
		$sql.= 'price_min,';
		$sql.= 'price_min_ttc,';
		$sql.= 'price_base_type,';
		$sql.= 'tva_tx,';
		$sql.= 'recuperableonly,';
		$sql.= 'localtax1_tx,';
		$sql.= 'localtax1_type,';
		$sql.= 'localtax2_tx,';
		$sql.= 'localtax2_type,';
		$sql.= 'fk_user_author,';
		$sql.= 'fk_user_modif,';
		$sql.= 'fk_product_type,';
		$sql.= 'pmp,';
		$sql.= 'finished,';
		$sql.= 'fk_unit';
		$sql.= 'cost_price';
		$sql.= 'status';

		
		$sql .= ') VALUES (';
		
		$sql .= ' '.(! isset($this->fk_projet)?'NULL':$this->fk_projet).',';
		$sql .= ' '.(! isset($this->fk_product)?'NULL':$this->fk_product).',';
		$sql .= ' '.(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql .= ' '.(! isset($this->ref_ext)?'NULL':"'".$this->db->escape($this->ref_ext)."'").',';
		$sql .= ' '."'".$this->db->idate(dol_now())."'".',';
		$sql .= ' '.(! isset($this->fk_parent)?'NULL':$this->fk_parent).',';
		$sql .= ' '.(! isset($this->fk_categorie)?'NULL':$this->fk_categorie).',';
		$sql .= ' '.(! isset($this->label)?'NULL':"'".$this->db->escape($this->label)."'").',';
		$sql .= ' '.(! isset($this->description)?'NULL':"'".$this->db->escape($this->description)."'").',';
		$sql .= ' '.(! isset($this->fk_country)?'NULL':$this->fk_country).',';
		$sql .= ' '.(! isset($this->price)?'NULL':"'".$this->price."'").',';
		$sql .= ' '.(! isset($this->price_ttc)?'NULL':"'".$this->price_ttc."'").',';
		$sql .= ' '.(! isset($this->price_min)?'NULL':"'".$this->price_min."'").',';
		$sql .= ' '.(! isset($this->price_min_ttc)?'NULL':"'".$this->price_min_ttc."'").',';
		$sql .= ' '.(! isset($this->price_base_type)?'NULL':"'".$this->db->escape($this->price_base_type)."'").',';
		$sql .= ' '.(! isset($this->tva_tx)?'NULL':"'".$this->tva_tx."'").',';
		$sql .= ' '.(! isset($this->recuperableonly)?'NULL':$this->recuperableonly).',';
		$sql .= ' '.(! isset($this->localtax1_tx)?'NULL':"'".$this->localtax1_tx."'").',';
		$sql .= ' '.(! isset($this->localtax1_type)?'NULL':"'".$this->db->escape($this->localtax1_type)."'").',';
		$sql .= ' '.(! isset($this->localtax2_tx)?'NULL':"'".$this->localtax2_tx."'").',';
		$sql .= ' '.(! isset($this->localtax2_type)?'NULL':"'".$this->db->escape($this->localtax2_type)."'").',';
		$sql .= ' '.$user->id.',';
		$sql .= ' '.(! isset($this->fk_user_modif)?'NULL':$this->fk_user_modif).',';
		$sql .= ' '.(! isset($this->fk_product_type)?'NULL':$this->fk_product_type).',';
		$sql .= ' '.(! isset($this->pmp)?'NULL':"'".$this->pmp."'").',';
		$sql .= ' '.(! isset($this->finished)?'NULL':$this->finished).',';
		$sql .= ' '.(! isset($this->fk_unit)?'NULL':$this->fk_unit).',';
		$sql .= ' '.(! isset($this->cost_price)?'NULL':"'".$this->cost_price."'").',';
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
		
		$sql .= " t.fk_projet,";
		$sql .= " t.fk_product,";
		$sql .= " t.ref,";
		$sql .= " t.ref_ext,";
		$sql .= " t.datec,";
		$sql .= " t.tms,";
		$sql .= " t.fk_parent,";
		$sql .= " t.fk_categorie,";
		$sql .= " t.label,";
		$sql .= " t.description,";
		$sql .= " t.fk_country,";
		$sql .= " t.price,";
		$sql .= " t.price_ttc,";
		$sql .= " t.price_min,";
		$sql .= " t.price_min_ttc,";
		$sql .= " t.price_base_type,";
		$sql .= " t.tva_tx,";
		$sql .= " t.recuperableonly,";
		$sql .= " t.localtax1_tx,";
		$sql .= " t.localtax1_type,";
		$sql .= " t.localtax2_tx,";
		$sql .= " t.localtax2_type,";
		$sql .= " t.fk_user_author,";
		$sql .= " t.fk_user_modif,";
		$sql .= " t.fk_product_type,";
		$sql .= " t.pmp,";
		$sql .= " t.finished,";
		$sql .= " t.fk_unit,";
		$sql .= " t.cost_price,";
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
				
				$this->fk_projet = $obj->fk_projet;
				$this->fk_product = $obj->fk_product;
				$this->ref = $obj->ref;
				$this->ref_ext = $obj->ref_ext;
				$this->datec = $this->db->jdate($obj->datec);
				$this->tms = $this->db->jdate($obj->tms);
				$this->fk_parent = $obj->fk_parent;
				$this->fk_categorie = $obj->fk_categorie;
				$this->label = $obj->label;
				$this->description = $obj->description;
				$this->fk_country = $obj->fk_country;
				$this->price = $obj->price;
				$this->price_ttc = $obj->price_ttc;
				$this->price_min = $obj->price_min;
				$this->price_min_ttc = $obj->price_min_ttc;
				$this->price_base_type = $obj->price_base_type;
				$this->tva_tx = $obj->tva_tx;
				$this->recuperableonly = $obj->recuperableonly;
				$this->localtax1_tx = $obj->localtax1_tx;
				$this->localtax1_type = $obj->localtax1_type;
				$this->localtax2_tx = $obj->localtax2_tx;
				$this->localtax2_type = $obj->localtax2_type;
				$this->fk_user_author = $obj->fk_user_author;
				$this->fk_user_modif = $obj->fk_user_modif;
				$this->fk_product_type = $obj->fk_product_type;
				$this->pmp = $obj->pmp;
				$this->finished = $obj->finished;
				$this->fk_unit = $obj->fk_unit;
				$this->cost_price = $obj->cost_price;
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
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND')
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		
		$sql .= " t.fk_projet,";
		$sql .= " t.fk_product,";
		$sql .= " t.ref,";
		$sql .= " t.ref_ext,";
		$sql .= " t.datec,";
		$sql .= " t.tms,";
		$sql .= " t.fk_parent,";
		$sql .= " t.fk_categorie,";
		$sql .= " t.label,";
		$sql .= " t.description,";
		$sql .= " t.fk_country,";
		$sql .= " t.price,";
		$sql .= " t.price_ttc,";
		$sql .= " t.price_min,";
		$sql .= " t.price_min_ttc,";
		$sql .= " t.price_base_type,";
		$sql .= " t.tva_tx,";
		$sql .= " t.recuperableonly,";
		$sql .= " t.localtax1_tx,";
		$sql .= " t.localtax1_type,";
		$sql .= " t.localtax2_tx,";
		$sql .= " t.localtax2_type,";
		$sql .= " t.fk_user_author,";
		$sql .= " t.fk_user_modif,";
		$sql .= " t.fk_product_type,";
		$sql .= " t.pmp,";
		$sql .= " t.finished,";
		$sql .= " t.fk_unit,";
		$sql .= " t.cost_price,";
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
				$line = new ProjetproductLine();

				$line->id = $obj->rowid;
				
				$line->fk_projet = $obj->fk_projet;
				$line->fk_product = $obj->fk_product;
				$line->ref = $obj->ref;
				$line->ref_ext = $obj->ref_ext;
				$line->datec = $this->db->jdate($obj->datec);
				$line->tms = $this->db->jdate($obj->tms);
				$line->fk_parent = $obj->fk_parent;
				$line->fk_categorie = $obj->fk_categorie;
				$line->label = $obj->label;
				$line->description = $obj->description;
				$line->fk_country = $obj->fk_country;
				$line->price = $obj->price;
				$line->price_ttc = $obj->price_ttc;
				$line->price_min = $obj->price_min;
				$line->price_min_ttc = $obj->price_min_ttc;
				$line->price_base_type = $obj->price_base_type;
				$line->tva_tx = $obj->tva_tx;
				$line->recuperableonly = $obj->recuperableonly;
				$line->localtax1_tx = $obj->localtax1_tx;
				$line->localtax1_type = $obj->localtax1_type;
				$line->localtax2_tx = $obj->localtax2_tx;
				$line->localtax2_type = $obj->localtax2_type;
				$line->fk_user_author = $obj->fk_user_author;
				$line->fk_user_modif = $obj->fk_user_modif;
				$line->fk_product_type = $obj->fk_product_type;
				$line->pmp = $obj->pmp;
				$line->finished = $obj->finished;
				$line->fk_unit = $obj->fk_unit;
				$line->cost_price = $obj->cost_price;
				$line->status = $obj->status;

				

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
		
		if (isset($this->fk_projet)) {
			 $this->fk_projet = trim($this->fk_projet);
		}
		if (isset($this->fk_product)) {
			 $this->fk_product = trim($this->fk_product);
		}
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->ref_ext)) {
			 $this->ref_ext = trim($this->ref_ext);
		}
		if (isset($this->fk_parent)) {
			 $this->fk_parent = trim($this->fk_parent);
		}
		if (isset($this->fk_categorie)) {
			 $this->fk_categorie = trim($this->fk_categorie);
		}
		if (isset($this->label)) {
			 $this->label = trim($this->label);
		}
		if (isset($this->description)) {
			 $this->description = trim($this->description);
		}
		if (isset($this->fk_country)) {
			 $this->fk_country = trim($this->fk_country);
		}
		if (isset($this->price)) {
			 $this->price = trim($this->price);
		}
		if (isset($this->price_ttc)) {
			 $this->price_ttc = trim($this->price_ttc);
		}
		if (isset($this->price_min)) {
			 $this->price_min = trim($this->price_min);
		}
		if (isset($this->price_min_ttc)) {
			 $this->price_min_ttc = trim($this->price_min_ttc);
		}
		if (isset($this->price_base_type)) {
			 $this->price_base_type = trim($this->price_base_type);
		}
		if (isset($this->tva_tx)) {
			 $this->tva_tx = trim($this->tva_tx);
		}
		if (isset($this->recuperableonly)) {
			 $this->recuperableonly = trim($this->recuperableonly);
		}
		if (isset($this->localtax1_tx)) {
			 $this->localtax1_tx = trim($this->localtax1_tx);
		}
		if (isset($this->localtax1_type)) {
			 $this->localtax1_type = trim($this->localtax1_type);
		}
		if (isset($this->localtax2_tx)) {
			 $this->localtax2_tx = trim($this->localtax2_tx);
		}
		if (isset($this->localtax2_type)) {
			 $this->localtax2_type = trim($this->localtax2_type);
		}
		if (isset($this->fk_user_author)) {
			 $this->fk_user_author = trim($this->fk_user_author);
		}
		if (isset($this->fk_user_modif)) {
			 $this->fk_user_modif = trim($this->fk_user_modif);
		}
		if (isset($this->fk_product_type)) {
			 $this->fk_product_type = trim($this->fk_product_type);
		}
		if (isset($this->pmp)) {
			 $this->pmp = trim($this->pmp);
		}
		if (isset($this->finished)) {
			 $this->finished = trim($this->finished);
		}
		if (isset($this->fk_unit)) {
			 $this->fk_unit = trim($this->fk_unit);
		}
		if (isset($this->cost_price)) {
			 $this->cost_price = trim($this->cost_price);
		}
		if (isset($this->status)) {
			 $this->status = trim($this->status);
		}

		

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';
		
		$sql .= ' fk_projet = '.(isset($this->fk_projet)?$this->fk_projet:"null").',';
		$sql .= ' fk_product = '.(isset($this->fk_product)?$this->fk_product:"null").',';
		$sql .= ' ref = '.(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").',';
		$sql .= ' ref_ext = '.(isset($this->ref_ext)?"'".$this->db->escape($this->ref_ext)."'":"null").',';
		$sql .= ' datec = '.(! isset($this->datec) || dol_strlen($this->datec) != 0 ? "'".$this->db->idate($this->datec)."'" : 'null').',';
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
		$sql .= ' fk_parent = '.(isset($this->fk_parent)?$this->fk_parent:"null").',';
		$sql .= ' fk_categorie = '.(isset($this->fk_categorie)?$this->fk_categorie:"null").',';
		$sql .= ' label = '.(isset($this->label)?"'".$this->db->escape($this->label)."'":"null").',';
		$sql .= ' description = '.(isset($this->description)?"'".$this->db->escape($this->description)."'":"null").',';
		$sql .= ' fk_country = '.(isset($this->fk_country)?$this->fk_country:"null").',';
		$sql .= ' price = '.(isset($this->price)?$this->price:"null").',';
		$sql .= ' price_ttc = '.(isset($this->price_ttc)?$this->price_ttc:"null").',';
		$sql .= ' price_min = '.(isset($this->price_min)?$this->price_min:"null").',';
		$sql .= ' price_min_ttc = '.(isset($this->price_min_ttc)?$this->price_min_ttc:"null").',';
		$sql .= ' price_base_type = '.(isset($this->price_base_type)?"'".$this->db->escape($this->price_base_type)."'":"null").',';
		$sql .= ' tva_tx = '.(isset($this->tva_tx)?$this->tva_tx:"null").',';
		$sql .= ' recuperableonly = '.(isset($this->recuperableonly)?$this->recuperableonly:"null").',';
		$sql .= ' localtax1_tx = '.(isset($this->localtax1_tx)?$this->localtax1_tx:"null").',';
		$sql .= ' localtax1_type = '.(isset($this->localtax1_type)?"'".$this->db->escape($this->localtax1_type)."'":"null").',';
		$sql .= ' localtax2_tx = '.(isset($this->localtax2_tx)?$this->localtax2_tx:"null").',';
		$sql .= ' localtax2_type = '.(isset($this->localtax2_type)?"'".$this->db->escape($this->localtax2_type)."'":"null").',';
		$sql .= ' fk_user_author = '.(isset($this->fk_user_author)?$this->fk_user_author:"null").',';
		$sql .= ' fk_user_modif = '.(isset($this->fk_user_modif)?$this->fk_user_modif:"null").',';
		$sql .= ' fk_product_type = '.(isset($this->fk_product_type)?$this->fk_product_type:"null").',';
		$sql .= ' pmp = '.(isset($this->pmp)?$this->pmp:"null").',';
		$sql .= ' finished = '.(isset($this->finished)?$this->finished:"null").',';
		$sql .= ' fk_unit = '.(isset($this->fk_unit)?$this->fk_unit:"null").',';
		$sql .= ' cost_price = '.(isset($this->cost_price)?$this->cost_price:"null").',';
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
		$object = new Projetproduct($this->db);

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

        $label = '<u>' . $langs->trans("MyModule") . '</u>';
        $label.= '<div width="100%">';
        $label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

        $link = '<a href="'.DOL_URL_ROOT.'/monprojet/card.php?id='.$this->id.'"';
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
		
		$this->fk_projet = '';
		$this->fk_product = '';
		$this->ref = '';
		$this->ref_ext = '';
		$this->datec = '';
		$this->tms = '';
		$this->fk_parent = '';
		$this->fk_categorie = '';
		$this->label = '';
		$this->description = '';
		$this->fk_country = '';
		$this->price = '';
		$this->price_ttc = '';
		$this->price_min = '';
		$this->price_min_ttc = '';
		$this->price_base_type = '';
		$this->tva_tx = '';
		$this->recuperableonly = '';
		$this->localtax1_tx = '';
		$this->localtax1_type = '';
		$this->localtax2_tx = '';
		$this->localtax2_type = '';
		$this->fk_user_author = '';
		$this->fk_user_modif = '';
		$this->fk_product_type = '';
		$this->pmp = '';
		$this->finished = '';
		$this->fk_unit = '';
		$this->cost_price = '';
		$this->status = '';

		
	}

}

/**
 * Class ProjetproductLine
 */
class ProjetproductLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	
	public $fk_projet;
	public $fk_product;
	public $ref;
	public $ref_ext;
	public $datec = '';
	public $tms = '';
	public $fk_parent;
	public $fk_categorie;
	public $label;
	public $description;
	public $fk_country;
	public $price;
	public $price_ttc;
	public $price_min;
	public $price_min_ttc;
	public $price_base_type;
	public $tva_tx;
	public $recuperableonly;
	public $localtax1_tx;
	public $localtax1_type;
	public $localtax2_tx;
	public $localtax2_type;
	public $fk_user_author;
	public $fk_user_modif;
	public $fk_product_type;
	public $pmp;
	public $finished;
	public $fk_unit;
	public $cost_price;
	public $status;

	/**
	 * @var mixed Sample line property 2
	 */
	
}
