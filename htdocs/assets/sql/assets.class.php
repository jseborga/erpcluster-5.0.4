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
 * \file    assets/assets.class.php
 * \ingroup assets
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Assets
 *
 * Put here description of your class
 * @see CommonObject
 */
class Assets extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'assets';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'assets';

	/**
	 * @var AssetsLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	
	public $entity;
	public $fk_father;
	public $fk_facture;
	public $type_group;
	public $type_patrim;
	public $ref;
	public $item_asset;
	public $date_adq = '';
	public $date_active = '';
	public $quant;
	public $coste;
	public $coste_residual;
	public $date_baja = '';
	public $descrip;
	public $number_plaque;
	public $trademark;
	public $model;
	public $anio;
	public $fk_asset_sup;
	public $fk_location;
	public $code_bar;
	public $fk_method_dep;
	public $type_property;
	public $code_bim;
	public $fk_product;
	public $useful_life;
	public $percent;
	public $account_accounting;
	public $fk_unit;
	public $fk_user_create;
	public $fk_user_mod;
	public $date_create = '';
	public $date_mod = '';
	public $mark;
	public $been;
	public $tms = '';
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
		if (isset($this->fk_father)) {
			 $this->fk_father = trim($this->fk_father);
		}
		if (isset($this->fk_facture)) {
			 $this->fk_facture = trim($this->fk_facture);
		}
		if (isset($this->type_group)) {
			 $this->type_group = trim($this->type_group);
		}
		if (isset($this->type_patrim)) {
			 $this->type_patrim = trim($this->type_patrim);
		}
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->item_asset)) {
			 $this->item_asset = trim($this->item_asset);
		}
		if (isset($this->quant)) {
			 $this->quant = trim($this->quant);
		}
		if (isset($this->coste)) {
			 $this->coste = trim($this->coste);
		}
		if (isset($this->coste_residual)) {
			 $this->coste_residual = trim($this->coste_residual);
		}
		if (isset($this->descrip)) {
			 $this->descrip = trim($this->descrip);
		}
		if (isset($this->number_plaque)) {
			 $this->number_plaque = trim($this->number_plaque);
		}
		if (isset($this->trademark)) {
			 $this->trademark = trim($this->trademark);
		}
		if (isset($this->model)) {
			 $this->model = trim($this->model);
		}
		if (isset($this->anio)) {
			 $this->anio = trim($this->anio);
		}
		if (isset($this->fk_asset_sup)) {
			 $this->fk_asset_sup = trim($this->fk_asset_sup);
		}
		if (isset($this->fk_location)) {
			 $this->fk_location = trim($this->fk_location);
		}
		if (isset($this->code_bar)) {
			 $this->code_bar = trim($this->code_bar);
		}
		if (isset($this->fk_method_dep)) {
			 $this->fk_method_dep = trim($this->fk_method_dep);
		}
		if (isset($this->type_property)) {
			 $this->type_property = trim($this->type_property);
		}
		if (isset($this->code_bim)) {
			 $this->code_bim = trim($this->code_bim);
		}
		if (isset($this->fk_product)) {
			 $this->fk_product = trim($this->fk_product);
		}
		if (isset($this->useful_life)) {
			 $this->useful_life = trim($this->useful_life);
		}
		if (isset($this->percent)) {
			 $this->percent = trim($this->percent);
		}
		if (isset($this->account_accounting)) {
			 $this->account_accounting = trim($this->account_accounting);
		}
		if (isset($this->fk_unit)) {
			 $this->fk_unit = trim($this->fk_unit);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_mod)) {
			 $this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->mark)) {
			 $this->mark = trim($this->mark);
		}
		if (isset($this->been)) {
			 $this->been = trim($this->been);
		}
		if (isset($this->statut)) {
			 $this->statut = trim($this->statut);
		}

		

		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';
		
		$sql.= 'entity,';
		$sql.= 'fk_father,';
		$sql.= 'fk_facture,';
		$sql.= 'type_group,';
		$sql.= 'type_patrim,';
		$sql.= 'ref,';
		$sql.= 'item_asset,';
		$sql.= 'date_adq,';
		$sql.= 'date_active,';
		$sql.= 'quant,';
		$sql.= 'coste,';
		$sql.= 'coste_residual,';
		$sql.= 'date_baja,';
		$sql.= 'descrip,';
		$sql.= 'number_plaque,';
		$sql.= 'trademark,';
		$sql.= 'model,';
		$sql.= 'anio,';
		$sql.= 'fk_asset_sup,';
		$sql.= 'fk_location,';
		$sql.= 'code_bar,';
		$sql.= 'fk_method_dep,';
		$sql.= 'type_property,';
		$sql.= 'code_bim,';
		$sql.= 'fk_product,';
		$sql.= 'useful_life,';
		$sql.= 'percent,';
		$sql.= 'account_accounting,';
		$sql.= 'fk_unit,';
		$sql.= 'fk_user_create,';
		$sql.= 'fk_user_mod,';
		$sql.= 'date_create,';
		$sql.= 'date_mod,';
		$sql.= 'mark,';
		$sql.= 'been';
		$sql.= 'statut';

		
		$sql .= ') VALUES (';
		
		$sql .= ' '.(! isset($this->entity)?'NULL':$this->entity).',';
		$sql .= ' '.(! isset($this->fk_father)?'NULL':$this->fk_father).',';
		$sql .= ' '.(! isset($this->fk_facture)?'NULL':$this->fk_facture).',';
		$sql .= ' '.(! isset($this->type_group)?'NULL':"'".$this->db->escape($this->type_group)."'").',';
		$sql .= ' '.(! isset($this->type_patrim)?'NULL':"'".$this->db->escape($this->type_patrim)."'").',';
		$sql .= ' '.(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql .= ' '.(! isset($this->item_asset)?'NULL':$this->item_asset).',';
		$sql .= ' '.(! isset($this->date_adq) || dol_strlen($this->date_adq)==0?'NULL':"'".$this->db->idate($this->date_adq)."'").',';
		$sql .= ' '.(! isset($this->date_active) || dol_strlen($this->date_active)==0?'NULL':"'".$this->db->idate($this->date_active)."'").',';
		$sql .= ' '.(! isset($this->quant)?'NULL':"'".$this->quant."'").',';
		$sql .= ' '.(! isset($this->coste)?'NULL':"'".$this->coste."'").',';
		$sql .= ' '.(! isset($this->coste_residual)?'NULL':"'".$this->coste_residual."'").',';
		$sql .= ' '.(! isset($this->date_baja) || dol_strlen($this->date_baja)==0?'NULL':"'".$this->db->idate($this->date_baja)."'").',';
		$sql .= ' '.(! isset($this->descrip)?'NULL':"'".$this->db->escape($this->descrip)."'").',';
		$sql .= ' '.(! isset($this->number_plaque)?'NULL':"'".$this->db->escape($this->number_plaque)."'").',';
		$sql .= ' '.(! isset($this->trademark)?'NULL':"'".$this->db->escape($this->trademark)."'").',';
		$sql .= ' '.(! isset($this->model)?'NULL':"'".$this->db->escape($this->model)."'").',';
		$sql .= ' '.(! isset($this->anio)?'NULL':$this->anio).',';
		$sql .= ' '.(! isset($this->fk_asset_sup)?'NULL':$this->fk_asset_sup).',';
		$sql .= ' '.(! isset($this->fk_location)?'NULL':$this->fk_location).',';
		$sql .= ' '.(! isset($this->code_bar)?'NULL':"'".$this->db->escape($this->code_bar)."'").',';
		$sql .= ' '.(! isset($this->fk_method_dep)?'NULL':$this->fk_method_dep).',';
		$sql .= ' '.(! isset($this->type_property)?'NULL':"'".$this->db->escape($this->type_property)."'").',';
		$sql .= ' '.(! isset($this->code_bim)?'NULL':"'".$this->db->escape($this->code_bim)."'").',';
		$sql .= ' '.(! isset($this->fk_product)?'NULL':$this->fk_product).',';
		$sql .= ' '.(! isset($this->useful_life)?'NULL':"'".$this->useful_life."'").',';
		$sql .= ' '.(! isset($this->percent)?'NULL':"'".$this->percent."'").',';
		$sql .= ' '.(! isset($this->account_accounting)?'NULL':"'".$this->db->escape($this->account_accounting)."'").',';
		$sql .= ' '.(! isset($this->fk_unit)?'NULL':$this->fk_unit).',';
		$sql .= ' '.(! isset($this->fk_user_create)?'NULL':$this->fk_user_create).',';
		$sql .= ' '.$user->id.',';
		$sql .= ' '.(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':"'".$this->db->idate($this->date_create)."'").',';
		$sql .= ' '.(! isset($this->date_mod) || dol_strlen($this->date_mod)==0?'NULL':"'".$this->db->idate($this->date_mod)."'").',';
		$sql .= ' '.(! isset($this->mark)?'NULL':"'".$this->db->escape($this->mark)."'").',';
		$sql .= ' '.(! isset($this->been)?'NULL':$this->been).',';
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
		$sql .= " t.fk_father,";
		$sql .= " t.fk_facture,";
		$sql .= " t.type_group,";
		$sql .= " t.type_patrim,";
		$sql .= " t.ref,";
		$sql .= " t.item_asset,";
		$sql .= " t.date_adq,";
		$sql .= " t.date_active,";
		$sql .= " t.quant,";
		$sql .= " t.coste,";
		$sql .= " t.coste_residual,";
		$sql .= " t.date_baja,";
		$sql .= " t.descrip,";
		$sql .= " t.number_plaque,";
		$sql .= " t.trademark,";
		$sql .= " t.model,";
		$sql .= " t.anio,";
		$sql .= " t.fk_asset_sup,";
		$sql .= " t.fk_location,";
		$sql .= " t.code_bar,";
		$sql .= " t.fk_method_dep,";
		$sql .= " t.type_property,";
		$sql .= " t.code_bim,";
		$sql .= " t.fk_product,";
		$sql .= " t.useful_life,";
		$sql .= " t.percent,";
		$sql .= " t.account_accounting,";
		$sql .= " t.fk_unit,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.date_create,";
		$sql .= " t.date_mod,";
		$sql .= " t.mark,";
		$sql .= " t.been,";
		$sql .= " t.tms,";
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
				$this->fk_father = $obj->fk_father;
				$this->fk_facture = $obj->fk_facture;
				$this->type_group = $obj->type_group;
				$this->type_patrim = $obj->type_patrim;
				$this->ref = $obj->ref;
				$this->item_asset = $obj->item_asset;
				$this->date_adq = $this->db->jdate($obj->date_adq);
				$this->date_active = $this->db->jdate($obj->date_active);
				$this->quant = $obj->quant;
				$this->coste = $obj->coste;
				$this->coste_residual = $obj->coste_residual;
				$this->date_baja = $this->db->jdate($obj->date_baja);
				$this->descrip = $obj->descrip;
				$this->number_plaque = $obj->number_plaque;
				$this->trademark = $obj->trademark;
				$this->model = $obj->model;
				$this->anio = $obj->anio;
				$this->fk_asset_sup = $obj->fk_asset_sup;
				$this->fk_location = $obj->fk_location;
				$this->code_bar = $obj->code_bar;
				$this->fk_method_dep = $obj->fk_method_dep;
				$this->type_property = $obj->type_property;
				$this->code_bim = $obj->code_bim;
				$this->fk_product = $obj->fk_product;
				$this->useful_life = $obj->useful_life;
				$this->percent = $obj->percent;
				$this->account_accounting = $obj->account_accounting;
				$this->fk_unit = $obj->fk_unit;
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_mod = $obj->fk_user_mod;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->date_mod = $this->db->jdate($obj->date_mod);
				$this->mark = $obj->mark;
				$this->been = $obj->been;
				$this->tms = $this->db->jdate($obj->tms);
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
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND')
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		
		$sql .= " t.entity,";
		$sql .= " t.fk_father,";
		$sql .= " t.fk_facture,";
		$sql .= " t.type_group,";
		$sql .= " t.type_patrim,";
		$sql .= " t.ref,";
		$sql .= " t.item_asset,";
		$sql .= " t.date_adq,";
		$sql .= " t.date_active,";
		$sql .= " t.quant,";
		$sql .= " t.coste,";
		$sql .= " t.coste_residual,";
		$sql .= " t.date_baja,";
		$sql .= " t.descrip,";
		$sql .= " t.number_plaque,";
		$sql .= " t.trademark,";
		$sql .= " t.model,";
		$sql .= " t.anio,";
		$sql .= " t.fk_asset_sup,";
		$sql .= " t.fk_location,";
		$sql .= " t.code_bar,";
		$sql .= " t.fk_method_dep,";
		$sql .= " t.type_property,";
		$sql .= " t.code_bim,";
		$sql .= " t.fk_product,";
		$sql .= " t.useful_life,";
		$sql .= " t.percent,";
		$sql .= " t.account_accounting,";
		$sql .= " t.fk_unit,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.date_create,";
		$sql .= " t.date_mod,";
		$sql .= " t.mark,";
		$sql .= " t.been,";
		$sql .= " t.tms,";
		$sql .= " t.statut";

		
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
				$line = new AssetsLine();

				$line->id = $obj->rowid;
				
				$line->entity = $obj->entity;
				$line->fk_father = $obj->fk_father;
				$line->fk_facture = $obj->fk_facture;
				$line->type_group = $obj->type_group;
				$line->type_patrim = $obj->type_patrim;
				$line->ref = $obj->ref;
				$line->item_asset = $obj->item_asset;
				$line->date_adq = $this->db->jdate($obj->date_adq);
				$line->date_active = $this->db->jdate($obj->date_active);
				$line->quant = $obj->quant;
				$line->coste = $obj->coste;
				$line->coste_residual = $obj->coste_residual;
				$line->date_baja = $this->db->jdate($obj->date_baja);
				$line->descrip = $obj->descrip;
				$line->number_plaque = $obj->number_plaque;
				$line->trademark = $obj->trademark;
				$line->model = $obj->model;
				$line->anio = $obj->anio;
				$line->fk_asset_sup = $obj->fk_asset_sup;
				$line->fk_location = $obj->fk_location;
				$line->code_bar = $obj->code_bar;
				$line->fk_method_dep = $obj->fk_method_dep;
				$line->type_property = $obj->type_property;
				$line->code_bim = $obj->code_bim;
				$line->fk_product = $obj->fk_product;
				$line->useful_life = $obj->useful_life;
				$line->percent = $obj->percent;
				$line->account_accounting = $obj->account_accounting;
				$line->fk_unit = $obj->fk_unit;
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->date_mod = $this->db->jdate($obj->date_mod);
				$line->mark = $obj->mark;
				$line->been = $obj->been;
				$line->tms = $this->db->jdate($obj->tms);
				$line->statut = $obj->statut;

				

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
		if (isset($this->fk_father)) {
			 $this->fk_father = trim($this->fk_father);
		}
		if (isset($this->fk_facture)) {
			 $this->fk_facture = trim($this->fk_facture);
		}
		if (isset($this->type_group)) {
			 $this->type_group = trim($this->type_group);
		}
		if (isset($this->type_patrim)) {
			 $this->type_patrim = trim($this->type_patrim);
		}
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->item_asset)) {
			 $this->item_asset = trim($this->item_asset);
		}
		if (isset($this->quant)) {
			 $this->quant = trim($this->quant);
		}
		if (isset($this->coste)) {
			 $this->coste = trim($this->coste);
		}
		if (isset($this->coste_residual)) {
			 $this->coste_residual = trim($this->coste_residual);
		}
		if (isset($this->descrip)) {
			 $this->descrip = trim($this->descrip);
		}
		if (isset($this->number_plaque)) {
			 $this->number_plaque = trim($this->number_plaque);
		}
		if (isset($this->trademark)) {
			 $this->trademark = trim($this->trademark);
		}
		if (isset($this->model)) {
			 $this->model = trim($this->model);
		}
		if (isset($this->anio)) {
			 $this->anio = trim($this->anio);
		}
		if (isset($this->fk_asset_sup)) {
			 $this->fk_asset_sup = trim($this->fk_asset_sup);
		}
		if (isset($this->fk_location)) {
			 $this->fk_location = trim($this->fk_location);
		}
		if (isset($this->code_bar)) {
			 $this->code_bar = trim($this->code_bar);
		}
		if (isset($this->fk_method_dep)) {
			 $this->fk_method_dep = trim($this->fk_method_dep);
		}
		if (isset($this->type_property)) {
			 $this->type_property = trim($this->type_property);
		}
		if (isset($this->code_bim)) {
			 $this->code_bim = trim($this->code_bim);
		}
		if (isset($this->fk_product)) {
			 $this->fk_product = trim($this->fk_product);
		}
		if (isset($this->useful_life)) {
			 $this->useful_life = trim($this->useful_life);
		}
		if (isset($this->percent)) {
			 $this->percent = trim($this->percent);
		}
		if (isset($this->account_accounting)) {
			 $this->account_accounting = trim($this->account_accounting);
		}
		if (isset($this->fk_unit)) {
			 $this->fk_unit = trim($this->fk_unit);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_mod)) {
			 $this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->mark)) {
			 $this->mark = trim($this->mark);
		}
		if (isset($this->been)) {
			 $this->been = trim($this->been);
		}
		if (isset($this->statut)) {
			 $this->statut = trim($this->statut);
		}

		

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';
		
		$sql .= ' entity = '.(isset($this->entity)?$this->entity:"null").',';
		$sql .= ' fk_father = '.(isset($this->fk_father)?$this->fk_father:"null").',';
		$sql .= ' fk_facture = '.(isset($this->fk_facture)?$this->fk_facture:"null").',';
		$sql .= ' type_group = '.(isset($this->type_group)?"'".$this->db->escape($this->type_group)."'":"null").',';
		$sql .= ' type_patrim = '.(isset($this->type_patrim)?"'".$this->db->escape($this->type_patrim)."'":"null").',';
		$sql .= ' ref = '.(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").',';
		$sql .= ' item_asset = '.(isset($this->item_asset)?$this->item_asset:"null").',';
		$sql .= ' date_adq = '.(! isset($this->date_adq) || dol_strlen($this->date_adq) != 0 ? "'".$this->db->idate($this->date_adq)."'" : 'null').',';
		$sql .= ' date_active = '.(! isset($this->date_active) || dol_strlen($this->date_active) != 0 ? "'".$this->db->idate($this->date_active)."'" : 'null').',';
		$sql .= ' quant = '.(isset($this->quant)?$this->quant:"null").',';
		$sql .= ' coste = '.(isset($this->coste)?$this->coste:"null").',';
		$sql .= ' coste_residual = '.(isset($this->coste_residual)?$this->coste_residual:"null").',';
		$sql .= ' date_baja = '.(! isset($this->date_baja) || dol_strlen($this->date_baja) != 0 ? "'".$this->db->idate($this->date_baja)."'" : 'null').',';
		$sql .= ' descrip = '.(isset($this->descrip)?"'".$this->db->escape($this->descrip)."'":"null").',';
		$sql .= ' number_plaque = '.(isset($this->number_plaque)?"'".$this->db->escape($this->number_plaque)."'":"null").',';
		$sql .= ' trademark = '.(isset($this->trademark)?"'".$this->db->escape($this->trademark)."'":"null").',';
		$sql .= ' model = '.(isset($this->model)?"'".$this->db->escape($this->model)."'":"null").',';
		$sql .= ' anio = '.(isset($this->anio)?$this->anio:"null").',';
		$sql .= ' fk_asset_sup = '.(isset($this->fk_asset_sup)?$this->fk_asset_sup:"null").',';
		$sql .= ' fk_location = '.(isset($this->fk_location)?$this->fk_location:"null").',';
		$sql .= ' code_bar = '.(isset($this->code_bar)?"'".$this->db->escape($this->code_bar)."'":"null").',';
		$sql .= ' fk_method_dep = '.(isset($this->fk_method_dep)?$this->fk_method_dep:"null").',';
		$sql .= ' type_property = '.(isset($this->type_property)?"'".$this->db->escape($this->type_property)."'":"null").',';
		$sql .= ' code_bim = '.(isset($this->code_bim)?"'".$this->db->escape($this->code_bim)."'":"null").',';
		$sql .= ' fk_product = '.(isset($this->fk_product)?$this->fk_product:"null").',';
		$sql .= ' useful_life = '.(isset($this->useful_life)?$this->useful_life:"null").',';
		$sql .= ' percent = '.(isset($this->percent)?$this->percent:"null").',';
		$sql .= ' account_accounting = '.(isset($this->account_accounting)?"'".$this->db->escape($this->account_accounting)."'":"null").',';
		$sql .= ' fk_unit = '.(isset($this->fk_unit)?$this->fk_unit:"null").',';
		$sql .= ' fk_user_create = '.(isset($this->fk_user_create)?$this->fk_user_create:"null").',';
		$sql .= ' fk_user_mod = '.$user->id.',';
		$sql .= ' date_create = '.(! isset($this->date_create) || dol_strlen($this->date_create) != 0 ? "'".$this->db->idate($this->date_create)."'" : 'null').',';
		$sql .= ' date_mod = '.(! isset($this->date_mod) || dol_strlen($this->date_mod) != 0 ? "'".$this->db->idate($this->date_mod)."'" : 'null').',';
		$sql .= ' mark = '.(isset($this->mark)?"'".$this->db->escape($this->mark)."'":"null").',';
		$sql .= ' been = '.(isset($this->been)?$this->been:"null").',';
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
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
		$object = new Assets($this->db);

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

        $link = '<a href="'.DOL_URL_ROOT.'/assets/card.php?id='.$this->id.'"';
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
		
		$this->entity = '';
		$this->fk_father = '';
		$this->fk_facture = '';
		$this->type_group = '';
		$this->type_patrim = '';
		$this->ref = '';
		$this->item_asset = '';
		$this->date_adq = '';
		$this->date_active = '';
		$this->quant = '';
		$this->coste = '';
		$this->coste_residual = '';
		$this->date_baja = '';
		$this->descrip = '';
		$this->number_plaque = '';
		$this->trademark = '';
		$this->model = '';
		$this->anio = '';
		$this->fk_asset_sup = '';
		$this->fk_location = '';
		$this->code_bar = '';
		$this->fk_method_dep = '';
		$this->type_property = '';
		$this->code_bim = '';
		$this->fk_product = '';
		$this->useful_life = '';
		$this->percent = '';
		$this->account_accounting = '';
		$this->fk_unit = '';
		$this->fk_user_create = '';
		$this->fk_user_mod = '';
		$this->date_create = '';
		$this->date_mod = '';
		$this->mark = '';
		$this->been = '';
		$this->tms = '';
		$this->statut = '';

		
	}

}

/**
 * Class AssetsLine
 */
class AssetsLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	
	public $entity;
	public $fk_father;
	public $fk_facture;
	public $type_group;
	public $type_patrim;
	public $ref;
	public $item_asset;
	public $date_adq = '';
	public $date_active = '';
	public $quant;
	public $coste;
	public $coste_residual;
	public $date_baja = '';
	public $descrip;
	public $number_plaque;
	public $trademark;
	public $model;
	public $anio;
	public $fk_asset_sup;
	public $fk_location;
	public $code_bar;
	public $fk_method_dep;
	public $type_property;
	public $code_bim;
	public $fk_product;
	public $useful_life;
	public $percent;
	public $account_accounting;
	public $fk_unit;
	public $fk_user_create;
	public $fk_user_mod;
	public $date_create = '';
	public $date_mod = '';
	public $mark;
	public $been;
	public $tms = '';
	public $statut;

	/**
	 * @var mixed Sample line property 2
	 */
	
}
