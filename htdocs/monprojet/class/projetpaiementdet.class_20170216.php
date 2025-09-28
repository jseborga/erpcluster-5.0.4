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
 * \file    monprojet/projetpaiementdet.class.php
 * \ingroup monprojet
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Projetpaiementdet
 *
 * Put here description of your class
 * @see CommonObject
 */
class Projetpaiementdet extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'projetpaiementdet';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'projet_paiementdet';

	/**
	 * @var ProjetpaiementdetLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	
	public $fk_projet_paiement;
	public $ref;
	public $date_paiement = '';
	public $fk_projet_task;
	public $fk_object;
	public $object;
	public $fk_user_create;
	public $fk_user_mod;
	public $fk_product;
	public $fk_facture_fourn;
	public $detail;
	public $fk_unit;
	public $qty_ant;
	public $qty;
	public $subprice;
	public $price;
	public $total_ht;
	public $total_ttc;
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
		
		if (isset($this->fk_projet_paiement)) {
			 $this->fk_projet_paiement = trim($this->fk_projet_paiement);
		}
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->fk_projet_task)) {
			 $this->fk_projet_task = trim($this->fk_projet_task);
		}
		if (isset($this->fk_object)) {
			 $this->fk_object = trim($this->fk_object);
		}
		if (isset($this->object)) {
			 $this->object = trim($this->object);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_mod)) {
			 $this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->fk_product)) {
			 $this->fk_product = trim($this->fk_product);
		}
		if (isset($this->fk_facture_fourn)) {
			 $this->fk_facture_fourn = trim($this->fk_facture_fourn);
		}
		if (isset($this->detail)) {
			 $this->detail = trim($this->detail);
		}
		if (isset($this->fk_unit)) {
			 $this->fk_unit = trim($this->fk_unit);
		}
		if (isset($this->qty_ant)) {
			 $this->qty_ant = trim($this->qty_ant);
		}
		if (isset($this->qty)) {
			 $this->qty = trim($this->qty);
		}
		if (isset($this->subprice)) {
			 $this->subprice = trim($this->subprice);
		}
		if (isset($this->price)) {
			 $this->price = trim($this->price);
		}
		if (isset($this->total_ht)) {
			 $this->total_ht = trim($this->total_ht);
		}
		if (isset($this->total_ttc)) {
			 $this->total_ttc = trim($this->total_ttc);
		}
		if (isset($this->status)) {
			 $this->status = trim($this->status);
		}

		

		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';
		
		$sql.= 'fk_projet_paiement,';
		$sql.= 'ref,';
		$sql.= 'date_paiement,';
		$sql.= 'fk_projet_task,';
		$sql.= 'fk_object,';
		$sql.= 'object,';
		$sql.= 'fk_user_create,';
		$sql.= 'fk_user_mod,';
		$sql.= 'fk_product,';
		$sql.= 'fk_facture_fourn,';
		$sql.= 'detail,';
		$sql.= 'fk_unit,';
		$sql.= 'qty_ant,';
		$sql.= 'qty,';
		$sql.= 'subprice,';
		$sql.= 'price,';
		$sql.= 'total_ht,';
		$sql.= 'total_ttc,';
		$sql.= 'datec,';
		$sql.= 'datem,';
		$sql.= 'status';

		
		$sql .= ') VALUES (';
		
		$sql .= ' '.(! isset($this->fk_projet_paiement)?'NULL':$this->fk_projet_paiement).',';
		$sql .= ' '.(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql .= ' '.(! isset($this->date_paiement) || dol_strlen($this->date_paiement)==0?'NULL':"'".$this->db->idate($this->date_paiement)."'").',';
		$sql .= ' '.(! isset($this->fk_projet_task)?'NULL':$this->fk_projet_task).',';
		$sql .= ' '.(! isset($this->fk_object)?'NULL':$this->fk_object).',';
		$sql .= ' '.(! isset($this->object)?'NULL':"'".$this->db->escape($this->object)."'").',';
		$sql .= ' '.(! isset($this->fk_user_create)?'NULL':$this->fk_user_create).',';
		$sql .= ' '.$user->id.',';
		$sql .= ' '.(! isset($this->fk_product)?'NULL':$this->fk_product).',';
		$sql .= ' '.(! isset($this->fk_facture_fourn)?'NULL':$this->fk_facture_fourn).',';
		$sql .= ' '.(! isset($this->detail)?'NULL':"'".$this->db->escape($this->detail)."'").',';
		$sql .= ' '.(! isset($this->fk_unit)?'NULL':$this->fk_unit).',';
		$sql .= ' '.(! isset($this->qty_ant)?'NULL':"'".$this->qty_ant."'").',';
		$sql .= ' '.(! isset($this->qty)?'NULL':"'".$this->qty."'").',';
		$sql .= ' '.(! isset($this->subprice)?'NULL':"'".$this->subprice."'").',';
		$sql .= ' '.(! isset($this->price)?'NULL':"'".$this->price."'").',';
		$sql .= ' '.(! isset($this->total_ht)?'NULL':"'".$this->total_ht."'").',';
		$sql .= ' '.(! isset($this->total_ttc)?'NULL':"'".$this->total_ttc."'").',';
		$sql .= ' '."'".$this->db->idate(dol_now())."'".',';
		$sql .= ' '.(! isset($this->datem) || dol_strlen($this->datem)==0?'NULL':"'".$this->db->idate($this->datem)."'").',';
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
	public function fetch($id, $ref = null,$fk=0,$fkprojettask=0,$fkobject=0,$nameobject='')
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		
		$sql .= " t.fk_projet_paiement,";
		$sql .= " t.ref,";
		$sql .= " t.date_paiement,";
		$sql .= " t.fk_projet_task,";
		$sql .= " t.fk_object,";
		$sql .= " t.object,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.fk_product,";
		$sql .= " t.fk_facture_fourn,";
		$sql .= " t.detail,";
		$sql .= " t.fk_unit,";
		$sql .= " t.qty_ant,";
		$sql .= " t.qty,";
		$sql .= " t.subprice,";
		$sql .= " t.price,";
		$sql .= " t.total_ht,";
		$sql .= " t.total_ttc,";
		$sql .= " t.datec,";
		$sql .= " t.datem,";
		$sql .= " t.tms,";
		$sql .= " t.status";

		
		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		if($fk>0 && $fkprojettask>0 && $fkobject>0 && !empty($nameobject)){
			$sql.= " WHERE t.fk_projet_paiement = ".$fk;
			$sql.= " AND t.fk_projet_task = ".$fkprojettask;
			$sql.= " AND t.fk_object = ".$fkobject;
			$sql.= " AND t.object = '".trim($nameobject)."'";
		}elseif (null !== $ref && $fk>0) {
			$sql .= ' WHERE t.ref = ' . '\'' . $ref . '\'';
			$sql.= " AND t.fk_projet_paiement = ".$fk;
		}
		else {
			$sql .= ' WHERE t.rowid = ' . $id;
		}

		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			if ($numrows) {
				$obj = $this->db->fetch_object($resql);

				$this->id = $obj->rowid;
				
				$this->fk_projet_paiement = $obj->fk_projet_paiement;
				$this->ref = $obj->ref;
				$this->date_paiement = $this->db->jdate($obj->date_paiement);
				$this->fk_projet_task = $obj->fk_projet_task;
				$this->fk_object = $obj->fk_object;
				$this->object = $obj->object;
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_mod = $obj->fk_user_mod;
				$this->fk_product = $obj->fk_product;
				$this->fk_facture_fourn = $obj->fk_facture_fourn;
				$this->detail = $obj->detail;
				$this->fk_unit = $obj->fk_unit;
				$this->qty_ant = $obj->qty_ant;
				$this->qty = $obj->qty;
				$this->subprice = $obj->subprice;
				$this->price = $obj->price;
				$this->total_ht = $obj->total_ht;
				$this->total_ttc = $obj->total_ttc;
				$this->datec = $this->db->jdate($obj->datec);
				$this->datem = $this->db->jdate($obj->datem);
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
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic="",$lView=false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		
		$sql .= " t.fk_projet_paiement,";
		$sql .= " t.ref,";
		$sql .= " t.date_paiement,";
		$sql .= " t.fk_projet_task,";
		$sql .= " t.fk_object,";
		$sql .= " t.object,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.fk_product,";
		$sql .= " t.fk_facture_fourn,";
		$sql .= " t.detail,";
		$sql .= " t.fk_unit,";
		$sql .= " t.qty_ant,";
		$sql .= " t.qty,";
		$sql .= " t.subprice,";
		$sql .= " t.price,";
		$sql .= " t.total_ht,";
		$sql .= " t.total_ttc,";
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
				$line = new ProjetpaiementdetLine();

				$line->id = $obj->rowid;
				
				$line->fk_projet_paiement = $obj->fk_projet_paiement;
				$line->ref = $obj->ref;
				$line->date_paiement = $this->db->jdate($obj->date_paiement);
				$line->fk_projet_task = $obj->fk_projet_task;
				$line->fk_object = $obj->fk_object;
				$line->object = $obj->object;
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->fk_product = $obj->fk_product;
				$line->fk_facture_fourn = $obj->fk_facture_fourn;
				$line->detail = $obj->detail;
				$line->fk_unit = $obj->fk_unit;
				$line->qty_ant = $obj->qty_ant;
				$line->qty = $obj->qty;
				$line->subprice = $obj->subprice;
				$line->price = $obj->price;
				$line->total_ht = $obj->total_ht;
				$line->total_ttc = $obj->total_ttc;
				$line->datec = $this->db->jdate($obj->datec);
				$line->datem = $this->db->jdate($obj->datem);
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
		
		if (isset($this->fk_projet_paiement)) {
			 $this->fk_projet_paiement = trim($this->fk_projet_paiement);
		}
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->fk_projet_task)) {
			 $this->fk_projet_task = trim($this->fk_projet_task);
		}
		if (isset($this->fk_object)) {
			 $this->fk_object = trim($this->fk_object);
		}
		if (isset($this->object)) {
			 $this->object = trim($this->object);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_mod)) {
			 $this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->fk_product)) {
			 $this->fk_product = trim($this->fk_product);
		}
		if (isset($this->fk_facture_fourn)) {
			 $this->fk_facture_fourn = trim($this->fk_facture_fourn);
		}
		if (isset($this->detail)) {
			 $this->detail = trim($this->detail);
		}
		if (isset($this->fk_unit)) {
			 $this->fk_unit = trim($this->fk_unit);
		}
		if (isset($this->qty_ant)) {
			 $this->qty_ant = trim($this->qty_ant);
		}
		if (isset($this->qty)) {
			 $this->qty = trim($this->qty);
		}
		if (isset($this->subprice)) {
			 $this->subprice = trim($this->subprice);
		}
		if (isset($this->price)) {
			 $this->price = trim($this->price);
		}
		if (isset($this->total_ht)) {
			 $this->total_ht = trim($this->total_ht);
		}
		if (isset($this->total_ttc)) {
			 $this->total_ttc = trim($this->total_ttc);
		}
		if (isset($this->status)) {
			 $this->status = trim($this->status);
		}

		

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';
		
		$sql .= ' fk_projet_paiement = '.(isset($this->fk_projet_paiement)?$this->fk_projet_paiement:"null").',';
		$sql .= ' ref = '.(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").',';
		$sql .= ' date_paiement = '.(! isset($this->date_paiement) || dol_strlen($this->date_paiement) != 0 ? "'".$this->db->idate($this->date_paiement)."'" : 'null').',';
		$sql .= ' fk_projet_task = '.(isset($this->fk_projet_task)?$this->fk_projet_task:"null").',';
		$sql .= ' fk_object = '.(isset($this->fk_object)?$this->fk_object:"null").',';
		$sql .= ' object = '.(isset($this->object)?"'".$this->db->escape($this->object)."'":"null").',';
		$sql .= ' fk_user_create = '.(isset($this->fk_user_create)?$this->fk_user_create:"null").',';
		$sql .= ' fk_user_mod = '.$user->id.',';
		$sql .= ' fk_product = '.(isset($this->fk_product)?$this->fk_product:"null").',';
		$sql .= ' fk_facture_fourn = '.(isset($this->fk_facture_fourn)?$this->fk_facture_fourn:"null").',';
		$sql .= ' detail = '.(isset($this->detail)?"'".$this->db->escape($this->detail)."'":"null").',';
		$sql .= ' fk_unit = '.(isset($this->fk_unit)?$this->fk_unit:"null").',';
		$sql .= ' qty_ant = '.(isset($this->qty_ant)?$this->qty_ant:"null").',';
		$sql .= ' qty = '.(isset($this->qty)?$this->qty:"null").',';
		$sql .= ' subprice = '.(isset($this->subprice)?$this->subprice:"null").',';
		$sql .= ' price = '.(isset($this->price)?$this->price:"null").',';
		$sql .= ' total_ht = '.(isset($this->total_ht)?$this->total_ht:"null").',';
		$sql .= ' total_ttc = '.(isset($this->total_ttc)?$this->total_ttc:"null").',';
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
		$object = new Projetpaiementdet($this->db);

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
		
		$this->fk_projet_paiement = '';
		$this->ref = '';
		$this->date_paiement = '';
		$this->fk_projet_task = '';
		$this->fk_object = '';
		$this->object = '';
		$this->fk_user_create = '';
		$this->fk_user_mod = '';
		$this->fk_product = '';
		$this->fk_facture_fourn = '';
		$this->detail = '';
		$this->fk_unit = '';
		$this->qty_ant = '';
		$this->qty = '';
		$this->subprice = '';
		$this->price = '';
		$this->total_ht = '';
		$this->total_ttc = '';
		$this->datec = '';
		$this->datem = '';
		$this->tms = '';
		$this->status = '';

		
	}

}

/**
 * Class ProjetpaiementdetLine
 */
class ProjetpaiementdetLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	
	public $fk_projet_paiement;
	public $ref;
	public $date_paiement = '';
	public $fk_projet_task;
	public $fk_object;
	public $object;
	public $fk_user_create;
	public $fk_user_mod;
	public $fk_product;
	public $fk_facture_fourn;
	public $detail;
	public $fk_unit;
	public $qty_ant;
	public $qty;
	public $subprice;
	public $price;
	public $total_ht;
	public $total_ttc;
	public $datec = '';
	public $datem = '';
	public $tms = '';
	public $status;

	/**
	 * @var mixed Sample line property 2
	 */
	
}
