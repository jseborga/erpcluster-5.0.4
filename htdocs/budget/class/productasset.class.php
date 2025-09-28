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
 * \file    budget/productasset.class.php
 * \ingroup budget
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Productasset
 *
 * Put here description of your class
 *
 * @see CommonObject
 */
class Productasset extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'productasset';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'product_asset';

	/**
	 * @var ProductassetLine[] Lines
	 */
	public $lines = array();

	/**
	 */

	public $fk_product;
	public $cost_hour_productive;
	public $cost_hour_improductive;
	public $cost_acquisition;
	public $engine_power;
	public $fk_type_engine;
	public $cost_tires;
	public $useful_life_tires;
	public $useful_life_year;
	public $useful_life_hours;
	public $percent_residual_value;
	public $percent_repair;
	public $percent_interest;
	public $diesel_consumption;
	public $diesel_lubricants;
	public $gasoline_consumption;
	public $gasoline_lubricants;
	public $cost_diesel;
	public $cost_gasoline;
	public $energy_kw;
	public $cost_depreciation;
	public $cost_interest;
	public $cost_fuel_consumption;
	public $cost_lubricants;
	public $cost_tires_replacement;
	public $cost_repair;
	public $cost_pu_improductive;
	public $cost_pu_productive;
	public $formula;
	public $type;
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

		if (isset($this->fk_product)) {
			 $this->fk_product = trim($this->fk_product);
		}
		if (isset($this->cost_hour_productive)) {
			 $this->cost_hour_productive = trim($this->cost_hour_productive);
		}
		if (isset($this->cost_hour_improductive)) {
			 $this->cost_hour_improductive = trim($this->cost_hour_improductive);
		}
		if (isset($this->cost_acquisition)) {
			 $this->cost_acquisition = trim($this->cost_acquisition);
		}
		if (isset($this->engine_power)) {
			 $this->engine_power = trim($this->engine_power);
		}
		if (isset($this->fk_type_engine)) {
			 $this->fk_type_engine = trim($this->fk_type_engine);
		}
		if (isset($this->cost_tires)) {
			 $this->cost_tires = trim($this->cost_tires);
		}
		if (isset($this->useful_life_tires)) {
			 $this->useful_life_tires = trim($this->useful_life_tires);
		}
		if (isset($this->useful_life_year)) {
			 $this->useful_life_year = trim($this->useful_life_year);
		}
		if (isset($this->useful_life_hours)) {
			 $this->useful_life_hours = trim($this->useful_life_hours);
		}
		if (isset($this->percent_residual_value)) {
			 $this->percent_residual_value = trim($this->percent_residual_value);
		}
		if (isset($this->percent_repair)) {
			 $this->percent_repair = trim($this->percent_repair);
		}
		if (isset($this->percent_interest)) {
			 $this->percent_interest = trim($this->percent_interest);
		}
		if (isset($this->diesel_consumption)) {
			 $this->diesel_consumption = trim($this->diesel_consumption);
		}
		if (isset($this->diesel_lubricants)) {
			 $this->diesel_lubricants = trim($this->diesel_lubricants);
		}
		if (isset($this->gasoline_consumption)) {
			 $this->gasoline_consumption = trim($this->gasoline_consumption);
		}
		if (isset($this->gasoline_lubricants)) {
			 $this->gasoline_lubricants = trim($this->gasoline_lubricants);
		}
		if (isset($this->cost_diesel)) {
			 $this->cost_diesel = trim($this->cost_diesel);
		}
		if (isset($this->cost_gasoline)) {
			 $this->cost_gasoline = trim($this->cost_gasoline);
		}
		if (isset($this->energy_kw)) {
			 $this->energy_kw = trim($this->energy_kw);
		}
		if (isset($this->cost_depreciation)) {
			 $this->cost_depreciation = trim($this->cost_depreciation);
		}
		if (isset($this->cost_interest)) {
			 $this->cost_interest = trim($this->cost_interest);
		}
		if (isset($this->cost_fuel_consumption)) {
			 $this->cost_fuel_consumption = trim($this->cost_fuel_consumption);
		}
		if (isset($this->cost_lubricants)) {
			 $this->cost_lubricants = trim($this->cost_lubricants);
		}
		if (isset($this->cost_tires_replacement)) {
			 $this->cost_tires_replacement = trim($this->cost_tires_replacement);
		}
		if (isset($this->cost_repair)) {
			 $this->cost_repair = trim($this->cost_repair);
		}
		if (isset($this->cost_pu_improductive)) {
			 $this->cost_pu_improductive = trim($this->cost_pu_improductive);
		}
		if (isset($this->cost_pu_productive)) {
			 $this->cost_pu_productive = trim($this->cost_pu_productive);
		}
		if (isset($this->formula)) {
			 $this->formula = trim($this->formula);
		}
		if (isset($this->type)) {
			 $this->type = trim($this->type);
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

		$sql.= 'fk_product,';
		$sql.= 'cost_hour_productive,';
		$sql.= 'cost_hour_improductive,';
		$sql.= 'cost_acquisition,';
		$sql.= 'engine_power,';
		$sql.= 'fk_type_engine,';
		$sql.= 'cost_tires,';
		$sql.= 'useful_life_tires,';
		$sql.= 'useful_life_year,';
		$sql.= 'useful_life_hours,';
		$sql.= 'percent_residual_value,';
		$sql.= 'percent_repair,';
		$sql.= 'percent_interest,';
		$sql.= 'diesel_consumption,';
		$sql.= 'diesel_lubricants,';
		$sql.= 'gasoline_consumption,';
		$sql.= 'gasoline_lubricants,';
		$sql.= 'cost_diesel,';
		$sql.= 'cost_gasoline,';
		$sql.= 'energy_kw,';
		$sql.= 'cost_depreciation,';
		$sql.= 'cost_interest,';
		$sql.= 'cost_fuel_consumption,';
		$sql.= 'cost_lubricants,';
		$sql.= 'cost_tires_replacement,';
		$sql.= 'cost_repair,';
		$sql.= 'cost_pu_improductive,';
		$sql.= 'cost_pu_productive,';
		$sql.= 'formula,';
		$sql.= 'type,';
		$sql.= 'fk_user_create,';
		$sql.= 'fk_user_mod,';
		$sql.= 'datec,';
		$sql.= 'datem,';
		$sql.= 'status';


		$sql .= ') VALUES (';

		$sql .= ' '.(! isset($this->fk_product)?'NULL':$this->fk_product).',';
		$sql .= ' '.(! isset($this->cost_hour_productive)?'NULL':"'".$this->cost_hour_productive."'").',';
		$sql .= ' '.(! isset($this->cost_hour_improductive)?'NULL':"'".$this->cost_hour_improductive."'").',';
		$sql .= ' '.(! isset($this->cost_acquisition)?'NULL':"'".$this->cost_acquisition."'").',';
		$sql .= ' '.(! isset($this->engine_power)?'NULL':"'".$this->engine_power."'").',';
		$sql .= ' '.(! isset($this->fk_type_engine)?'NULL':$this->fk_type_engine).',';
		$sql .= ' '.(! isset($this->cost_tires)?'NULL':"'".$this->cost_tires."'").',';
		$sql .= ' '.(! isset($this->useful_life_tires)?'NULL':"'".$this->useful_life_tires."'").',';
		$sql .= ' '.(! isset($this->useful_life_year)?'NULL':"'".$this->useful_life_year."'").',';
		$sql .= ' '.(! isset($this->useful_life_hours)?'NULL':"'".$this->useful_life_hours."'").',';
		$sql .= ' '.(! isset($this->percent_residual_value)?'NULL':"'".$this->percent_residual_value."'").',';
		$sql .= ' '.(! isset($this->percent_repair)?'NULL':"'".$this->percent_repair."'").',';
		$sql .= ' '.(! isset($this->percent_interest)?'NULL':"'".$this->percent_interest."'").',';
		$sql .= ' '.(! isset($this->diesel_consumption)?'NULL':"'".$this->diesel_consumption."'").',';
		$sql .= ' '.(! isset($this->diesel_lubricants)?'NULL':"'".$this->diesel_lubricants."'").',';
		$sql .= ' '.(! isset($this->gasoline_consumption)?'NULL':"'".$this->gasoline_consumption."'").',';
		$sql .= ' '.(! isset($this->gasoline_lubricants)?'NULL':"'".$this->gasoline_lubricants."'").',';
		$sql .= ' '.(! isset($this->cost_diesel)?'NULL':"'".$this->cost_diesel."'").',';
		$sql .= ' '.(! isset($this->cost_gasoline)?'NULL':"'".$this->cost_gasoline."'").',';
		$sql .= ' '.(! isset($this->energy_kw)?'NULL':"'".$this->energy_kw."'").',';
		$sql .= ' '.(! isset($this->cost_depreciation)?'NULL':"'".$this->cost_depreciation."'").',';
		$sql .= ' '.(! isset($this->cost_interest)?'NULL':"'".$this->cost_interest."'").',';
		$sql .= ' '.(! isset($this->cost_fuel_consumption)?'NULL':"'".$this->cost_fuel_consumption."'").',';
		$sql .= ' '.(! isset($this->cost_lubricants)?'NULL':"'".$this->cost_lubricants."'").',';
		$sql .= ' '.(! isset($this->cost_tires_replacement)?'NULL':"'".$this->cost_tires_replacement."'").',';
		$sql .= ' '.(! isset($this->cost_repair)?'NULL':"'".$this->cost_repair."'").',';
		$sql .= ' '.(! isset($this->cost_pu_improductive)?'NULL':"'".$this->cost_pu_improductive."'").',';
		$sql .= ' '.(! isset($this->cost_pu_productive)?'NULL':"'".$this->cost_pu_productive."'").',';
		$sql .= ' '.(! isset($this->formula)?'NULL':"'".$this->db->escape($this->formula)."'").',';
		$sql .= ' '.(! isset($this->type)?'NULL':"'".$this->db->escape($this->type)."'").',';
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
	public function fetch($id, $fk=0)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.fk_product,";
		$sql .= " t.cost_hour_productive,";
		$sql .= " t.cost_hour_improductive,";
		$sql .= " t.cost_acquisition,";
		$sql .= " t.engine_power,";
		$sql .= " t.fk_type_engine,";
		$sql .= " t.cost_tires,";
		$sql .= " t.useful_life_tires,";
		$sql .= " t.useful_life_year,";
		$sql .= " t.useful_life_hours,";
		$sql .= " t.percent_residual_value,";
		$sql .= " t.percent_repair,";
		$sql .= " t.percent_interest,";
		$sql .= " t.diesel_consumption,";
		$sql .= " t.diesel_lubricants,";
		$sql .= " t.gasoline_consumption,";
		$sql .= " t.gasoline_lubricants,";
		$sql .= " t.cost_diesel,";
		$sql .= " t.cost_gasoline,";
		$sql .= " t.energy_kw,";
		$sql .= " t.cost_depreciation,";
		$sql .= " t.cost_interest,";
		$sql .= " t.cost_fuel_consumption,";
		$sql .= " t.cost_lubricants,";
		$sql .= " t.cost_tires_replacement,";
		$sql .= " t.cost_repair,";
		$sql .= " t.cost_pu_improductive,";
		$sql .= " t.cost_pu_productive,";
		$sql .= " t.formula,";
		$sql .= " t.type,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.datec,";
		$sql .= " t.datem,";
		$sql .= " t.tms,";
		$sql .= " t.status";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql.= ' WHERE 1 = 1';
		if ($fk>0) {
			$sql .= ' AND t.fk_product = ' . $fk;
		} else {
			$sql .= ' AND t.rowid = ' . $id;
		}

		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			if ($numrows) {
				$obj = $this->db->fetch_object($resql);

				$this->id = $obj->rowid;

				$this->fk_product = $obj->fk_product;
				$this->cost_hour_productive = $obj->cost_hour_productive;
				$this->cost_hour_improductive = $obj->cost_hour_improductive;
				$this->cost_acquisition = $obj->cost_acquisition;
				$this->engine_power = $obj->engine_power;
				$this->fk_type_engine = $obj->fk_type_engine;
				$this->cost_tires = $obj->cost_tires;
				$this->useful_life_tires = $obj->useful_life_tires;
				$this->useful_life_year = $obj->useful_life_year;
				$this->useful_life_hours = $obj->useful_life_hours;
				$this->percent_residual_value = $obj->percent_residual_value;
				$this->percent_repair = $obj->percent_repair;
				$this->percent_interest = $obj->percent_interest;
				$this->diesel_consumption = $obj->diesel_consumption;
				$this->diesel_lubricants = $obj->diesel_lubricants;
				$this->gasoline_consumption = $obj->gasoline_consumption;
				$this->gasoline_lubricants = $obj->gasoline_lubricants;
				$this->cost_diesel = $obj->cost_diesel;
				$this->cost_gasoline = $obj->cost_gasoline;
				$this->energy_kw = $obj->energy_kw;
				$this->cost_depreciation = $obj->cost_depreciation;
				$this->cost_interest = $obj->cost_interest;
				$this->cost_fuel_consumption = $obj->cost_fuel_consumption;
				$this->cost_lubricants = $obj->cost_lubricants;
				$this->cost_tires_replacement = $obj->cost_tires_replacement;
				$this->cost_repair = $obj->cost_repair;
				$this->cost_pu_improductive = $obj->cost_pu_improductive;
				$this->cost_pu_productive = $obj->cost_pu_productive;
				$this->formula = $obj->formula;
				$this->type = $obj->type;
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

			return $numrows;
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

		$sql .= " t.fk_product,";
		$sql .= " t.cost_hour_productive,";
		$sql .= " t.cost_hour_improductive,";
		$sql .= " t.cost_acquisition,";
		$sql .= " t.engine_power,";
		$sql .= " t.fk_type_engine,";
		$sql .= " t.cost_tires,";
		$sql .= " t.useful_life_tires,";
		$sql .= " t.useful_life_year,";
		$sql .= " t.useful_life_hours,";
		$sql .= " t.percent_residual_value,";
		$sql .= " t.percent_repair,";
		$sql .= " t.percent_interest,";
		$sql .= " t.diesel_consumption,";
		$sql .= " t.diesel_lubricants,";
		$sql .= " t.gasoline_consumption,";
		$sql .= " t.gasoline_lubricants,";
		$sql .= " t.cost_diesel,";
		$sql .= " t.cost_gasoline,";
		$sql .= " t.energy_kw,";
		$sql .= " t.cost_depreciation,";
		$sql .= " t.cost_interest,";
		$sql .= " t.cost_fuel_consumption,";
		$sql .= " t.cost_lubricants,";
		$sql .= " t.cost_tires_replacement,";
		$sql .= " t.cost_repair,";
		$sql .= " t.cost_pu_improductive,";
		$sql .= " t.cost_pu_productive,";
		$sql .= " t.formula,";
		$sql .= " t.type,";
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
		    $sql .= " AND entity IN (" . getEntity("productasset", 1) . ")";
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
				$line = new ProductassetLine();

				$line->id = $obj->rowid;

				$line->fk_product = $obj->fk_product;
				$line->cost_hour_productive = $obj->cost_hour_productive;
				$line->cost_hour_improductive = $obj->cost_hour_improductive;
				$line->cost_acquisition = $obj->cost_acquisition;
				$line->engine_power = $obj->engine_power;
				$line->fk_type_engine = $obj->fk_type_engine;
				$line->cost_tires = $obj->cost_tires;
				$line->useful_life_tires = $obj->useful_life_tires;
				$line->useful_life_year = $obj->useful_life_year;
				$line->useful_life_hours = $obj->useful_life_hours;
				$line->percent_residual_value = $obj->percent_residual_value;
				$line->percent_repair = $obj->percent_repair;
				$line->percent_interest = $obj->percent_interest;
				$line->diesel_consumption = $obj->diesel_consumption;
				$line->diesel_lubricants = $obj->diesel_lubricants;
				$line->gasoline_consumption = $obj->gasoline_consumption;
				$line->gasoline_lubricants = $obj->gasoline_lubricants;
				$line->cost_diesel = $obj->cost_diesel;
				$line->cost_gasoline = $obj->cost_gasoline;
				$line->energy_kw = $obj->energy_kw;
				$line->cost_depreciation = $obj->cost_depreciation;
				$line->cost_interest = $obj->cost_interest;
				$line->cost_fuel_consumption = $obj->cost_fuel_consumption;
				$line->cost_lubricants = $obj->cost_lubricants;
				$line->cost_tires_replacement = $obj->cost_tires_replacement;
				$line->cost_repair = $obj->cost_repair;
				$line->cost_pu_improductive = $obj->cost_pu_improductive;
				$line->cost_pu_productive = $obj->cost_pu_productive;
				$line->formula = $obj->formula;
				$line->type = $obj->type;
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

		if (isset($this->fk_product)) {
			 $this->fk_product = trim($this->fk_product);
		}
		if (isset($this->cost_hour_productive)) {
			 $this->cost_hour_productive = trim($this->cost_hour_productive);
		}
		if (isset($this->cost_hour_improductive)) {
			 $this->cost_hour_improductive = trim($this->cost_hour_improductive);
		}
		if (isset($this->cost_acquisition)) {
			 $this->cost_acquisition = trim($this->cost_acquisition);
		}
		if (isset($this->engine_power)) {
			 $this->engine_power = trim($this->engine_power);
		}
		if (isset($this->fk_type_engine)) {
			 $this->fk_type_engine = trim($this->fk_type_engine);
		}
		if (isset($this->cost_tires)) {
			 $this->cost_tires = trim($this->cost_tires);
		}
		if (isset($this->useful_life_tires)) {
			 $this->useful_life_tires = trim($this->useful_life_tires);
		}
		if (isset($this->useful_life_year)) {
			 $this->useful_life_year = trim($this->useful_life_year);
		}
		if (isset($this->useful_life_hours)) {
			 $this->useful_life_hours = trim($this->useful_life_hours);
		}
		if (isset($this->percent_residual_value)) {
			 $this->percent_residual_value = trim($this->percent_residual_value);
		}
		if (isset($this->percent_repair)) {
			 $this->percent_repair = trim($this->percent_repair);
		}
		if (isset($this->percent_interest)) {
			 $this->percent_interest = trim($this->percent_interest);
		}
		if (isset($this->diesel_consumption)) {
			 $this->diesel_consumption = trim($this->diesel_consumption);
		}
		if (isset($this->diesel_lubricants)) {
			 $this->diesel_lubricants = trim($this->diesel_lubricants);
		}
		if (isset($this->gasoline_consumption)) {
			 $this->gasoline_consumption = trim($this->gasoline_consumption);
		}
		if (isset($this->gasoline_lubricants)) {
			 $this->gasoline_lubricants = trim($this->gasoline_lubricants);
		}
		if (isset($this->cost_diesel)) {
			 $this->cost_diesel = trim($this->cost_diesel);
		}
		if (isset($this->cost_gasoline)) {
			 $this->cost_gasoline = trim($this->cost_gasoline);
		}
		if (isset($this->energy_kw)) {
			 $this->energy_kw = trim($this->energy_kw);
		}
		if (isset($this->cost_depreciation)) {
			 $this->cost_depreciation = trim($this->cost_depreciation);
		}
		if (isset($this->cost_interest)) {
			 $this->cost_interest = trim($this->cost_interest);
		}
		if (isset($this->cost_fuel_consumption)) {
			 $this->cost_fuel_consumption = trim($this->cost_fuel_consumption);
		}
		if (isset($this->cost_lubricants)) {
			 $this->cost_lubricants = trim($this->cost_lubricants);
		}
		if (isset($this->cost_tires_replacement)) {
			 $this->cost_tires_replacement = trim($this->cost_tires_replacement);
		}
		if (isset($this->cost_repair)) {
			 $this->cost_repair = trim($this->cost_repair);
		}
		if (isset($this->cost_pu_improductive)) {
			 $this->cost_pu_improductive = trim($this->cost_pu_improductive);
		}
		if (isset($this->cost_pu_productive)) {
			 $this->cost_pu_productive = trim($this->cost_pu_productive);
		}
		if (isset($this->formula)) {
			 $this->formula = trim($this->formula);
		}
		if (isset($this->type)) {
			 $this->type = trim($this->type);
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

		$sql .= ' fk_product = '.(isset($this->fk_product)?$this->fk_product:"null").',';
		$sql .= ' cost_hour_productive = '.(isset($this->cost_hour_productive)?$this->cost_hour_productive:"null").',';
		$sql .= ' cost_hour_improductive = '.(isset($this->cost_hour_improductive)?$this->cost_hour_improductive:"null").',';
		$sql .= ' cost_acquisition = '.(isset($this->cost_acquisition)?$this->cost_acquisition:"null").',';
		$sql .= ' engine_power = '.(isset($this->engine_power)?$this->engine_power:"null").',';
		$sql .= ' fk_type_engine = '.(isset($this->fk_type_engine)?$this->fk_type_engine:"null").',';
		$sql .= ' cost_tires = '.(isset($this->cost_tires)?$this->cost_tires:"null").',';
		$sql .= ' useful_life_tires = '.(isset($this->useful_life_tires)?$this->useful_life_tires:"null").',';
		$sql .= ' useful_life_year = '.(isset($this->useful_life_year)?$this->useful_life_year:"null").',';
		$sql .= ' useful_life_hours = '.(isset($this->useful_life_hours)?$this->useful_life_hours:"null").',';
		$sql .= ' percent_residual_value = '.(isset($this->percent_residual_value)?$this->percent_residual_value:"null").',';
		$sql .= ' percent_repair = '.(isset($this->percent_repair)?$this->percent_repair:"null").',';
		$sql .= ' percent_interest = '.(isset($this->percent_interest)?$this->percent_interest:"null").',';
		$sql .= ' diesel_consumption = '.(isset($this->diesel_consumption)?$this->diesel_consumption:"null").',';
		$sql .= ' diesel_lubricants = '.(isset($this->diesel_lubricants)?$this->diesel_lubricants:"null").',';
		$sql .= ' gasoline_consumption = '.(isset($this->gasoline_consumption)?$this->gasoline_consumption:"null").',';
		$sql .= ' gasoline_lubricants = '.(isset($this->gasoline_lubricants)?$this->gasoline_lubricants:"null").',';
		$sql .= ' cost_diesel = '.(isset($this->cost_diesel)?$this->cost_diesel:"null").',';
		$sql .= ' cost_gasoline = '.(isset($this->cost_gasoline)?$this->cost_gasoline:"null").',';
		$sql .= ' energy_kw = '.(isset($this->energy_kw)?$this->energy_kw:"null").',';
		$sql .= ' cost_depreciation = '.(isset($this->cost_depreciation)?$this->cost_depreciation:"null").',';
		$sql .= ' cost_interest = '.(isset($this->cost_interest)?$this->cost_interest:"null").',';
		$sql .= ' cost_fuel_consumption = '.(isset($this->cost_fuel_consumption)?$this->cost_fuel_consumption:"null").',';
		$sql .= ' cost_lubricants = '.(isset($this->cost_lubricants)?$this->cost_lubricants:"null").',';
		$sql .= ' cost_tires_replacement = '.(isset($this->cost_tires_replacement)?$this->cost_tires_replacement:"null").',';
		$sql .= ' cost_repair = '.(isset($this->cost_repair)?$this->cost_repair:"null").',';
		$sql .= ' cost_pu_improductive = '.(isset($this->cost_pu_improductive)?$this->cost_pu_improductive:"null").',';
		$sql .= ' cost_pu_productive = '.(isset($this->cost_pu_productive)?$this->cost_pu_productive:"null").',';
		$sql .= ' formula = '.(isset($this->formula)?"'".$this->db->escape($this->formula)."'":"null").',';
		$sql .= ' type = '.(isset($this->type)?"'".$this->db->escape($this->type)."'":"null").',';
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
			echo $sql;
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
		$object = new Productasset($this->db);

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

		$this->fk_product = '';
		$this->cost_hour_productive = '';
		$this->cost_hour_improductive = '';
		$this->cost_acquisition = '';
		$this->engine_power = '';
		$this->fk_type_engine = '';
		$this->cost_tires = '';
		$this->useful_life_tires = '';
		$this->useful_life_year = '';
		$this->useful_life_hours = '';
		$this->percent_residual_value = '';
		$this->percent_repair = '';
		$this->percent_interest = '';
		$this->diesel_consumption = '';
		$this->diesel_lubricants = '';
		$this->gasoline_consumption = '';
		$this->gasoline_lubricants = '';
		$this->cost_diesel = '';
		$this->cost_gasoline = '';
		$this->energy_kw = '';
		$this->cost_depreciation = '';
		$this->cost_interest = '';
		$this->cost_fuel_consumption = '';
		$this->cost_lubricants = '';
		$this->cost_tires_replacement = '';
		$this->cost_repair = '';
		$this->cost_pu_improductive = '';
		$this->cost_pu_productive = '';
		$this->formula = '';
		$this->type = '';
		$this->fk_user_create = '';
		$this->fk_user_mod = '';
		$this->datec = '';
		$this->datem = '';
		$this->tms = '';
		$this->status = '';


	}

}

/**
 * Class ProductassetLine
 */
class ProductassetLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */

	public $fk_product;
	public $cost_hour_productive;
	public $cost_hour_improductive;
	public $cost_acquisition;
	public $engine_power;
	public $fk_type_engine;
	public $cost_tires;
	public $useful_life_tires;
	public $useful_life_year;
	public $useful_life_hours;
	public $percent_residual_value;
	public $percent_repair;
	public $percent_interest;
	public $diesel_consumption;
	public $diesel_lubricants;
	public $gasoline_consumption;
	public $gasoline_lubricants;
	public $cost_diesel;
	public $cost_gasoline;
	public $energy_kw;
	public $cost_depreciation;
	public $cost_interest;
	public $cost_fuel_consumption;
	public $cost_lubricants;
	public $cost_tires_replacement;
	public $cost_repair;
	public $cost_pu_improductive;
	public $cost_pu_productive;
	public $formula;
	public $type;
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
