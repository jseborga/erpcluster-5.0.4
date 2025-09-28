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
 * \file    almacen/stockmouvementtemp.class.php
 * \ingroup almacen
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Stockmouvementtemp
 *
 * Put here description of your class
 * @see CommonObject
 */
class Stockmouvementtemp extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'stockmouvementtemp';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'stock_mouvement_temp';

	/**
	 * @var StockmouvementtempLine[] Lines
	 */
	public $lines = array();

	/**
	 */

	public $entity;
	public $ref;
	public $tms = '';
	public $datem = '';
	public $fk_product;
	public $fk_entrepot;
	public $fk_type_mov;
	public $value;
	public $quant;
	public $price;
	public $balance_peps;
	public $balance_ueps;
	public $price_peps;
	public $price_ueps;
	public $type_mouvement;
	public $fk_user_author;
	public $label;
	public $fk_origin;
	public $origintype;
	public $inventorycode;
	public $batch;
	public $eatby = '';
	public $sellby = '';
	public $statut;

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
		if (isset($this->fk_product)) {
			 $this->fk_product = trim($this->fk_product);
		}
		if (isset($this->fk_entrepot)) {
			 $this->fk_entrepot = trim($this->fk_entrepot);
		}
		if (isset($this->fk_type_mov)) {
			 $this->fk_type_mov = trim($this->fk_type_mov);
		}
		if (isset($this->value)) {
			 $this->value = trim($this->value);
		}
		if (isset($this->quant)) {
			 $this->quant = trim($this->quant);
		}
		if (isset($this->price)) {
			 $this->price = trim($this->price);
		}
		if (isset($this->balance_peps)) {
			 $this->balance_peps = trim($this->balance_peps);
		}
		if (isset($this->balance_ueps)) {
			 $this->balance_ueps = trim($this->balance_ueps);
		}
		if (isset($this->price_peps)) {
			 $this->price_peps = trim($this->price_peps);
		}
		if (isset($this->price_ueps)) {
			 $this->price_ueps = trim($this->price_ueps);
		}
		if (isset($this->type_mouvement)) {
			 $this->type_mouvement = trim($this->type_mouvement);
		}
		if (isset($this->fk_user_author)) {
			 $this->fk_user_author = trim($this->fk_user_author);
		}
		if (isset($this->label)) {
			 $this->label = trim($this->label);
		}
		if (isset($this->fk_origin)) {
			 $this->fk_origin = trim($this->fk_origin);
		}
		if (isset($this->origintype)) {
			 $this->origintype = trim($this->origintype);
		}
		if (isset($this->inventorycode)) {
			 $this->inventorycode = trim($this->inventorycode);
		}
		if (isset($this->batch)) {
			 $this->batch = trim($this->batch);
		}
		if (isset($this->statut)) {
			 $this->statut = trim($this->statut);
		}



		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';

		$sql.= 'entity,';
		$sql.= 'ref,';
		$sql.= 'datem,';
		$sql.= 'fk_product,';
		$sql.= 'fk_entrepot,';
		$sql.= 'fk_type_mov,';
		$sql.= 'value,';
		$sql.= 'quant,';
		$sql.= 'price,';
		$sql.= 'balance_peps,';
		$sql.= 'balance_ueps,';
		$sql.= 'price_peps,';
		$sql.= 'price_ueps,';
		$sql.= 'type_mouvement,';
		$sql.= 'fk_user_author,';
		$sql.= 'label,';
		$sql.= 'fk_origin,';
		$sql.= 'origintype,';
		$sql.= 'inventorycode,';
		$sql.= 'batch,';
		$sql.= 'eatby,';
		$sql.= 'sellby,';
		$sql.= 'statut';


		$sql .= ') VALUES (';

		$sql .= ' '.(! isset($this->entity)?'NULL':$this->entity).',';
		$sql .= ' '.(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql .= ' '.(! isset($this->datem) || dol_strlen($this->datem)==0?'NULL':"'".$this->db->idate($this->datem)."'").',';
		$sql .= ' '.(! isset($this->fk_product)?'NULL':$this->fk_product).',';
		$sql .= ' '.(! isset($this->fk_entrepot)?'NULL':$this->fk_entrepot).',';
		$sql .= ' '.(! isset($this->fk_type_mov)?'NULL':$this->fk_type_mov).',';
		$sql .= ' '.(! isset($this->value)?'NULL':"'".$this->value."'").',';
		$sql .= ' '.(! isset($this->quant)?'NULL':"'".$this->quant."'").',';
		$sql .= ' '.(! isset($this->price)?'NULL':"'".$this->price."'").',';
		$sql .= ' '.(! isset($this->balance_peps)?'NULL':"'".$this->balance_peps."'").',';
		$sql .= ' '.(! isset($this->balance_ueps)?'NULL':"'".$this->balance_ueps."'").',';
		$sql .= ' '.(! isset($this->price_peps)?'NULL':"'".$this->price_peps."'").',';
		$sql .= ' '.(! isset($this->price_ueps)?'NULL':"'".$this->price_ueps."'").',';
		$sql .= ' '.(! isset($this->type_mouvement)?'NULL':$this->type_mouvement).',';
		$sql .= ' '.$user->id.',';
		$sql .= ' '.(! isset($this->label)?'NULL':"'".$this->db->escape($this->label)."'").',';
		$sql .= ' '.(! isset($this->fk_origin)?'NULL':$this->fk_origin).',';
		$sql .= ' '.(! isset($this->origintype)?'NULL':"'".$this->db->escape($this->origintype)."'").',';
		$sql .= ' '.(! isset($this->inventorycode)?'NULL':"'".$this->db->escape($this->inventorycode)."'").',';
		$sql .= ' '.(! isset($this->batch)?'NULL':"'".$this->db->escape($this->batch)."'").',';
		$sql .= ' '.(! isset($this->eatby) || dol_strlen($this->eatby)==0?'NULL':"'".$this->db->idate($this->eatby)."'").',';
		$sql .= ' '.(! isset($this->sellby) || dol_strlen($this->sellby)==0?'NULL':"'".$this->db->idate($this->sellby)."'").',';
		$sql .= ' '.(! isset($this->statut)?'NULL':$this->statut);


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
		$sql .= " t.tms,";
		$sql .= " t.datem,";
		$sql .= " t.fk_product,";
		$sql .= " t.fk_entrepot,";
		$sql .= " t.fk_type_mov,";
		$sql .= " t.value,";
		$sql .= " t.quant,";
		$sql .= " t.price,";
		$sql .= " t.balance_peps,";
		$sql .= " t.balance_ueps,";
		$sql .= " t.price_peps,";
		$sql .= " t.price_ueps,";
		$sql .= " t.type_mouvement,";
		$sql .= " t.fk_user_author,";
		$sql .= " t.label,";
		$sql .= " t.fk_origin,";
		$sql .= " t.origintype,";
		$sql .= " t.inventorycode,";
		$sql .= " t.batch,";
		$sql .= " t.eatby,";
		$sql .= " t.sellby,";
		$sql .= " t.statut";


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
				$this->tms = $this->db->jdate($obj->tms);
				$this->datem = $this->db->jdate($obj->datem);
				$this->fk_product = $obj->fk_product;
				$this->fk_entrepot = $obj->fk_entrepot;
				$this->fk_type_mov = $obj->fk_type_mov;
				$this->value = $obj->value;
				$this->quant = $obj->quant;
				$this->price = $obj->price;
				$this->balance_peps = $obj->balance_peps;
				$this->balance_ueps = $obj->balance_ueps;
				$this->price_peps = $obj->price_peps;
				$this->price_ueps = $obj->price_ueps;
				$this->type_mouvement = $obj->type_mouvement;
				$this->fk_user_author = $obj->fk_user_author;
				$this->label = $obj->label;
				$this->fk_origin = $obj->fk_origin;
				$this->origintype = $obj->origintype;
				$this->inventorycode = $obj->inventorycode;
				$this->batch = $obj->batch;
				$this->eatby = $this->db->jdate($obj->eatby);
				$this->sellby = $this->db->jdate($obj->sellby);
				$this->statut = $obj->statut;


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
		$sql .= " t.tms,";
		$sql .= " t.datem,";
		$sql .= " t.fk_product,";
		$sql .= " t.fk_entrepot,";
		$sql .= " t.fk_type_mov,";
		$sql .= " t.value,";
		$sql .= " t.quant,";
		$sql .= " t.price,";
		$sql .= " t.balance_peps,";
		$sql .= " t.balance_ueps,";
		$sql .= " t.price_peps,";
		$sql .= " t.price_ueps,";
		$sql .= " t.type_mouvement,";
		$sql .= " t.fk_user_author,";
		$sql .= " t.label,";
		$sql .= " t.fk_origin,";
		$sql .= " t.origintype,";
		$sql .= " t.inventorycode,";
		$sql .= " t.batch,";
		$sql .= " t.eatby,";
		$sql .= " t.sellby,";
		$sql .= " t.statut";


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
		    $sql .= " AND entity IN (" . getEntity("stockprogram", 1) . ")";
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
				$line = new StockmouvementtempLine();

				$line->id = $obj->rowid;

				$line->entity = $obj->entity;
				$line->ref = $obj->ref;
				$line->tms = $this->db->jdate($obj->tms);
				$line->datem = $this->db->jdate($obj->datem);
				$line->fk_product = $obj->fk_product;
				$line->fk_entrepot = $obj->fk_entrepot;
				$line->fk_type_mov = $obj->fk_type_mov;
				$line->value = $obj->value;
				$line->quant = $obj->quant;
				$line->price = $obj->price;
				$line->balance_peps = $obj->balance_peps;
				$line->balance_ueps = $obj->balance_ueps;
				$line->price_peps = $obj->price_peps;
				$line->price_ueps = $obj->price_ueps;
				$line->type_mouvement = $obj->type_mouvement;
				$line->fk_user_author = $obj->fk_user_author;
				$line->label = $obj->label;
				$line->fk_origin = $obj->fk_origin;
				$line->origintype = $obj->origintype;
				$line->inventorycode = $obj->inventorycode;
				$line->batch = $obj->batch;
				$line->eatby = $this->db->jdate($obj->eatby);
				$line->sellby = $this->db->jdate($obj->sellby);
				$line->statut = $obj->statut;

				if ($lView && $num == 1) $this->fetch($obj->rowid);

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
		if (isset($this->fk_product)) {
			 $this->fk_product = trim($this->fk_product);
		}
		if (isset($this->fk_entrepot)) {
			 $this->fk_entrepot = trim($this->fk_entrepot);
		}
		if (isset($this->fk_type_mov)) {
			 $this->fk_type_mov = trim($this->fk_type_mov);
		}
		if (isset($this->value)) {
			 $this->value = trim($this->value);
		}
		if (isset($this->quant)) {
			 $this->quant = trim($this->quant);
		}
		if (isset($this->price)) {
			 $this->price = trim($this->price);
		}
		if (isset($this->balance_peps)) {
			 $this->balance_peps = trim($this->balance_peps);
		}
		if (isset($this->balance_ueps)) {
			 $this->balance_ueps = trim($this->balance_ueps);
		}
		if (isset($this->price_peps)) {
			 $this->price_peps = trim($this->price_peps);
		}
		if (isset($this->price_ueps)) {
			 $this->price_ueps = trim($this->price_ueps);
		}
		if (isset($this->type_mouvement)) {
			 $this->type_mouvement = trim($this->type_mouvement);
		}
		if (isset($this->fk_user_author)) {
			 $this->fk_user_author = trim($this->fk_user_author);
		}
		if (isset($this->label)) {
			 $this->label = trim($this->label);
		}
		if (isset($this->fk_origin)) {
			 $this->fk_origin = trim($this->fk_origin);
		}
		if (isset($this->origintype)) {
			 $this->origintype = trim($this->origintype);
		}
		if (isset($this->inventorycode)) {
			 $this->inventorycode = trim($this->inventorycode);
		}
		if (isset($this->batch)) {
			 $this->batch = trim($this->batch);
		}
		if (isset($this->statut)) {
			 $this->statut = trim($this->statut);
		}



		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';

		$sql .= ' entity = '.(isset($this->entity)?$this->entity:"null").',';
		$sql .= ' ref = '.(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").',';
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
		$sql .= ' datem = '.(! isset($this->datem) || dol_strlen($this->datem) != 0 ? "'".$this->db->idate($this->datem)."'" : 'null').',';
		$sql .= ' fk_product = '.(isset($this->fk_product)?$this->fk_product:"null").',';
		$sql .= ' fk_entrepot = '.(isset($this->fk_entrepot)?$this->fk_entrepot:"null").',';
		$sql .= ' fk_type_mov = '.(isset($this->fk_type_mov)?$this->fk_type_mov:"null").',';
		$sql .= ' value = '.(isset($this->value)?$this->value:"null").',';
		$sql .= ' quant = '.(isset($this->quant)?$this->quant:"null").',';
		$sql .= ' price = '.(isset($this->price)?$this->price:"null").',';
		$sql .= ' balance_peps = '.(isset($this->balance_peps)?$this->balance_peps:"null").',';
		$sql .= ' balance_ueps = '.(isset($this->balance_ueps)?$this->balance_ueps:"null").',';
		$sql .= ' price_peps = '.(isset($this->price_peps)?$this->price_peps:"null").',';
		$sql .= ' price_ueps = '.(isset($this->price_ueps)?$this->price_ueps:"null").',';
		$sql .= ' type_mouvement = '.(isset($this->type_mouvement)?$this->type_mouvement:"null").',';
		$sql .= ' fk_user_author = '.(isset($this->fk_user_author)?$this->fk_user_author:"null").',';
		$sql .= ' label = '.(isset($this->label)?"'".$this->db->escape($this->label)."'":"null").',';
		$sql .= ' fk_origin = '.(isset($this->fk_origin)?$this->fk_origin:"null").',';
		$sql .= ' origintype = '.(isset($this->origintype)?"'".$this->db->escape($this->origintype)."'":"null").',';
		$sql .= ' inventorycode = '.(isset($this->inventorycode)?"'".$this->db->escape($this->inventorycode)."'":"null").',';
		$sql .= ' batch = '.(isset($this->batch)?"'".$this->db->escape($this->batch)."'":"null").',';
		$sql .= ' eatby = '.(! isset($this->eatby) || dol_strlen($this->eatby) != 0 ? "'".$this->db->idate($this->eatby)."'" : 'null').',';
		$sql .= ' sellby = '.(! isset($this->sellby) || dol_strlen($this->sellby) != 0 ? "'".$this->db->idate($this->sellby)."'" : 'null').',';
		$sql .= ' statut = '.(isset($this->statut)?$this->statut:"null");


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
		$object = new Stockmouvementtemp($this->db);

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

        $label = '<u>' . $langs->trans("Almacen") . '</u>';
        $label.= '<div width="100%">';
        $label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

        $link = '<a href="'.DOL_URL_ROOT.'/almacen/transferenecia/fiche.php?id='.$this->id.'"';
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
		return $this->LibStatut($this->statut,$mode);
	}

	/**
	 *  Renvoi le libelle d'un status donne
	 *
	 *  @param	int		$status        	Id status
	 *  @param  int		$mode          	0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return string 			       	Label of status
	 */
	function LibStatut($statut,$mode=0)
	{
		global $langs;
		if ($mode == 0)
		{
			if ($statut==-1) return $langs->trans('StatusTransfCanceled');
			if ($statut==0) return $langs->trans('StatusTransfDraft');
			if ($statut==1) return $langs->trans('StatusTransfPending');
			if ($statut==2) return $langs->trans('StatusTransfAccepted');
		}
		elseif ($mode == 1)
		{
			if ($statut==-1) return $langs->trans('StatusOrderCanceledShort');
			if ($statut==0) return $langs->trans('StatusOrderDraftShort');
			if ($statut==1) return $langs->trans('StatusOrderPendingShort');
			if ($statut==2) return $langs->trans('StatusOrderSentShort');
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
		$this->tms = '';
		$this->datem = '';
		$this->fk_product = '';
		$this->fk_entrepot = '';
		$this->fk_type_mov = '';
		$this->value = '';
		$this->quant = '';
		$this->price = '';
		$this->balance_peps = '';
		$this->balance_ueps = '';
		$this->price_peps = '';
		$this->price_ueps = '';
		$this->type_mouvement = '';
		$this->fk_user_author = '';
		$this->label = '';
		$this->fk_origin = '';
		$this->origintype = '';
		$this->inventorycode = '';
		$this->batch = '';
		$this->eatby = '';
		$this->sellby = '';
		$this->statut = '';


	}

}

/**
 * Class StockmouvementtempLine
 */
class StockmouvementtempLine
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
	public $tms = '';
	public $datem = '';
	public $fk_product;
	public $fk_entrepot;
	public $fk_type_mov;
	public $value;
	public $quant;
	public $price;
	public $balance_peps;
	public $balance_ueps;
	public $price_peps;
	public $price_ueps;
	public $type_mouvement;
	public $fk_user_author;
	public $label;
	public $fk_origin;
	public $origintype;
	public $inventorycode;
	public $batch;
	public $eatby = '';
	public $sellby = '';
	public $statut;

	/**
	 * @var mixed Sample line property 2
	 */

}
