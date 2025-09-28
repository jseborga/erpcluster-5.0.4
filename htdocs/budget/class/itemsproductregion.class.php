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
 * \file    budget/itemsproductregion.class.php
 * \ingroup budget
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Itemsproductregion
 *
 * Put here description of your class
 *
 * @see CommonObject
 */
class Itemsproductregion extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'itemsproductregion';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'items_product_region';

	/**
	 * @var ItemsproductregionLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	
	public $fk_item_product;
	public $fk_region;
	public $fk_sector;
	public $fk_origin;
	public $percent_origin;
	public $units;
	public $commander;
	public $performance;
	public $hour_production;
	public $price_productive;
	public $price_improductive;
	public $amount_noprod;
	public $amount;
	public $cost_direct;
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
		
		if (isset($this->fk_item_product)) {
			 $this->fk_item_product = trim($this->fk_item_product);
		}
		if (isset($this->fk_region)) {
			 $this->fk_region = trim($this->fk_region);
		}
		if (isset($this->fk_sector)) {
			 $this->fk_sector = trim($this->fk_sector);
		}
		if (isset($this->fk_origin)) {
			 $this->fk_origin = trim($this->fk_origin);
		}
		if (isset($this->percent_origin)) {
			 $this->percent_origin = trim($this->percent_origin);
		}
		if (isset($this->units)) {
			 $this->units = trim($this->units);
		}
		if (isset($this->commander)) {
			 $this->commander = trim($this->commander);
		}
		if (isset($this->performance)) {
			 $this->performance = trim($this->performance);
		}
		if (isset($this->hour_production)) {
			 $this->hour_production = trim($this->hour_production);
		}
		if (isset($this->price_productive)) {
			 $this->price_productive = trim($this->price_productive);
		}
		if (isset($this->price_improductive)) {
			 $this->price_improductive = trim($this->price_improductive);
		}
		if (isset($this->amount_noprod)) {
			 $this->amount_noprod = trim($this->amount_noprod);
		}
		if (isset($this->amount)) {
			 $this->amount = trim($this->amount);
		}
		if (isset($this->cost_direct)) {
			 $this->cost_direct = trim($this->cost_direct);
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
		
		$sql.= 'fk_item_product,';
		$sql.= 'fk_region,';
		$sql.= 'fk_sector,';
		$sql.= 'fk_origin,';
		$sql.= 'percent_origin,';
		$sql.= 'units,';
		$sql.= 'commander,';
		$sql.= 'performance,';
		$sql.= 'hour_production,';
		$sql.= 'price_productive,';
		$sql.= 'price_improductive,';
		$sql.= 'amount_noprod,';
		$sql.= 'amount,';
		$sql.= 'cost_direct,';
		$sql.= 'fk_user_create,';
		$sql.= 'fk_user_mod,';
		$sql.= 'datec,';
		$sql.= 'datem,';
		$sql.= 'status';

		
		$sql .= ') VALUES (';
		
		$sql .= ' '.(! isset($this->fk_item_product)?'NULL':$this->fk_item_product).',';
		$sql .= ' '.(! isset($this->fk_region)?'NULL':$this->fk_region).',';
		$sql .= ' '.(! isset($this->fk_sector)?'NULL':$this->fk_sector).',';
		$sql .= ' '.(! isset($this->fk_origin)?'NULL':$this->fk_origin).',';
		$sql .= ' '.(! isset($this->percent_origin)?'NULL':"'".$this->percent_origin."'").',';
		$sql .= ' '.(! isset($this->units)?'NULL':"'".$this->units."'").',';
		$sql .= ' '.(! isset($this->commander)?'NULL':$this->commander).',';
		$sql .= ' '.(! isset($this->performance)?'NULL':"'".$this->performance."'").',';
		$sql .= ' '.(! isset($this->hour_production)?'NULL':"'".$this->hour_production."'").',';
		$sql .= ' '.(! isset($this->price_productive)?'NULL':"'".$this->price_productive."'").',';
		$sql .= ' '.(! isset($this->price_improductive)?'NULL':"'".$this->price_improductive."'").',';
		$sql .= ' '.(! isset($this->amount_noprod)?'NULL':"'".$this->amount_noprod."'").',';
		$sql .= ' '.(! isset($this->amount)?'NULL':"'".$this->amount."'").',';
		$sql .= ' '.(! isset($this->cost_direct)?'NULL':"'".$this->cost_direct."'").',';
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
	public function fetch($id, $fk_item_product=0,$fk_region=0,$fk_sector=0)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		
		$sql .= " t.fk_item_product,";
		$sql .= " t.fk_region,";
		$sql .= " t.fk_sector,";
		$sql .= " t.fk_origin,";
		$sql .= " t.percent_origin,";
		$sql .= " t.units,";
		$sql .= " t.commander,";
		$sql .= " t.performance,";
		$sql .= " t.hour_production,";
		$sql .= " t.price_productive,";
		$sql .= " t.price_improductive,";
		$sql .= " t.amount_noprod,";
		$sql .= " t.amount,";
		$sql .= " t.cost_direct,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.datec,";
		$sql .= " t.datem,";
		$sql .= " t.tms,";
		$sql .= " t.status";

		
		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
		    $sql .= " AND entity IN (" . getEntity("itemsproductregion", 1) . ")";
		}
		if ($fk_item_product>0 && $fk_region >0 && $fk_sector >0) {
			$sql .= ' AND t.fk_item_product = ' . $fk_item_product;
			$sql .= ' AND t.fk_region = ' . $fk_region;
			$sql .= ' AND t.fk_sector = ' . $fk_sector;
		} else {
			$sql .= ' AND t.rowid = ' . $id;
		}

		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			if ($numrows) {
				$obj = $this->db->fetch_object($resql);

				$this->id = $obj->rowid;
				
				$this->fk_item_product = $obj->fk_item_product;
				$this->fk_region = $obj->fk_region;
				$this->fk_sector = $obj->fk_sector;
				$this->fk_origin = $obj->fk_origin;
				$this->percent_origin = $obj->percent_origin;
				$this->units = $obj->units;
				$this->commander = $obj->commander;
				$this->performance = $obj->performance;
				$this->hour_production = $obj->hour_production;
				$this->price_productive = $obj->price_productive;
				$this->price_improductive = $obj->price_improductive;
				$this->amount_noprod = $obj->amount_noprod;
				$this->amount = $obj->amount;
				$this->cost_direct = $obj->cost_direct;
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
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lView=false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		
		$sql .= " t.fk_item_product,";
		$sql .= " t.fk_region,";
		$sql .= " t.fk_sector,";
		$sql .= " t.fk_origin,";
		$sql .= " t.percent_origin,";
		$sql .= " t.units,";
		$sql .= " t.commander,";
		$sql .= " t.performance,";
		$sql .= " t.hour_production,";
		$sql .= " t.price_productive,";
		$sql .= " t.price_improductive,";
		$sql .= " t.amount_noprod,";
		$sql .= " t.amount,";
		$sql .= " t.cost_direct,";
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
		    $sql .= " AND entity IN (" . getEntity("itemsproductregion", 1) . ")";
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
				$line = new ItemsproductregionLine();

				$line->id = $obj->rowid;
				
				$line->fk_item_product = $obj->fk_item_product;
				$line->fk_region = $obj->fk_region;
				$line->fk_sector = $obj->fk_sector;
				$line->fk_origin = $obj->fk_origin;
				$line->percent_origin = $obj->percent_origin;
				$line->units = $obj->units;
				$line->commander = $obj->commander;
				$line->performance = $obj->performance;
				$line->hour_production = $obj->hour_production;
				$line->price_productive = $obj->price_productive;
				$line->price_improductive = $obj->price_improductive;
				$line->amount_noprod = $obj->amount_noprod;
				$line->amount = $obj->amount;
				$line->cost_direct = $obj->cost_direct;
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
		
		if (isset($this->fk_item_product)) {
			 $this->fk_item_product = trim($this->fk_item_product);
		}
		if (isset($this->fk_region)) {
			 $this->fk_region = trim($this->fk_region);
		}
		if (isset($this->fk_sector)) {
			 $this->fk_sector = trim($this->fk_sector);
		}
		if (isset($this->fk_origin)) {
			 $this->fk_origin = trim($this->fk_origin);
		}
		if (isset($this->percent_origin)) {
			 $this->percent_origin = trim($this->percent_origin);
		}
		if (isset($this->units)) {
			 $this->units = trim($this->units);
		}
		if (isset($this->commander)) {
			 $this->commander = trim($this->commander);
		}
		if (isset($this->performance)) {
			 $this->performance = trim($this->performance);
		}
		if (isset($this->hour_production)) {
			 $this->hour_production = trim($this->hour_production);
		}
		if (isset($this->price_productive)) {
			 $this->price_productive = trim($this->price_productive);
		}
		if (isset($this->price_improductive)) {
			 $this->price_improductive = trim($this->price_improductive);
		}
		if (isset($this->amount_noprod)) {
			 $this->amount_noprod = trim($this->amount_noprod);
		}
		if (isset($this->amount)) {
			 $this->amount = trim($this->amount);
		}
		if (isset($this->cost_direct)) {
			 $this->cost_direct = trim($this->cost_direct);
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
		
		$sql .= ' fk_item_product = '.(isset($this->fk_item_product)?$this->fk_item_product:"null").',';
		$sql .= ' fk_region = '.(isset($this->fk_region)?$this->fk_region:"null").',';
		$sql .= ' fk_sector = '.(isset($this->fk_sector)?$this->fk_sector:"null").',';
		$sql .= ' fk_origin = '.(isset($this->fk_origin)?$this->fk_origin:"null").',';
		$sql .= ' percent_origin = '.(isset($this->percent_origin)?$this->percent_origin:"null").',';
		$sql .= ' units = '.(isset($this->units)?$this->units:"null").',';
		$sql .= ' commander = '.(isset($this->commander)?$this->commander:"null").',';
		$sql .= ' performance = '.(isset($this->performance)?$this->performance:"null").',';
		$sql .= ' hour_production = '.(isset($this->hour_production)?$this->hour_production:"null").',';
		$sql .= ' price_productive = '.(isset($this->price_productive)?$this->price_productive:"null").',';
		$sql .= ' price_improductive = '.(isset($this->price_improductive)?$this->price_improductive:"null").',';
		$sql .= ' amount_noprod = '.(isset($this->amount_noprod)?$this->amount_noprod:"null").',';
		$sql .= ' amount = '.(isset($this->amount)?$this->amount:"null").',';
		$sql .= ' cost_direct = '.(isset($this->cost_direct)?$this->cost_direct:"null").',';
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
		$object = new Itemsproductregion($this->db);

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

        $url = DOL_URL_ROOT.'/budget/'.$this->table_name.'_card.php?id='.$this->id;

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
		
		$this->fk_item_product = '';
		$this->fk_region = '';
		$this->fk_sector = '';
		$this->fk_origin = '';
		$this->percent_origin = '';
		$this->units = '';
		$this->commander = '';
		$this->performance = '';
		$this->hour_production = '';
		$this->price_productive = '';
		$this->price_improductive = '';
		$this->amount_noprod = '';
		$this->amount = '';
		$this->cost_direct = '';
		$this->fk_user_create = '';
		$this->fk_user_mod = '';
		$this->datec = '';
		$this->datem = '';
		$this->tms = '';
		$this->status = '';

		
	}

}

/**
 * Class ItemsproductregionLine
 */
class ItemsproductregionLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	
	public $fk_item_product;
	public $fk_region;
	public $fk_sector;
	public $fk_origin;
	public $percent_origin;
	public $units;
	public $commander;
	public $performance;
	public $hour_production;
	public $price_productive;
	public $price_improductive;
	public $amount_noprod;
	public $amount;
	public $cost_direct;
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
