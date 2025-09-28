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
 * \file    purchase/supplierproposaldet.class.php
 * \ingroup purchase
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Supplierproposaldet
 *
 * Put here description of your class
 *
 * @see CommonObject
 */
class Supplierproposaldet extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'supplierproposaldet';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'supplier_proposaldet';

	/**
	 * @var SupplierproposaldetLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	
	public $fk_supplier_proposal;
	public $fk_parent_line;
	public $fk_product;
	public $label;
	public $description;
	public $fk_remise_except;
	public $vat_src_code;
	public $tva_tx;
	public $localtax1_tx;
	public $localtax1_type;
	public $localtax2_tx;
	public $localtax2_type;
	public $qty;
	public $fk_unit;
	public $remise_percent;
	public $remise;
	public $price;
	public $subprice;
	public $total_ht;
	public $total_tva;
	public $total_localtax1;
	public $total_localtax2;
	public $total_ttc;
	public $product_type;
	public $info_bits;
	public $buy_price_ht;
	public $fk_product_fournisseur_price;
	public $special_code;
	public $rang;
	public $ref_fourn;
	public $fk_multicurrency;
	public $multicurrency_code;
	public $multicurrency_subprice;
	public $multicurrency_total_ht;
	public $multicurrency_total_tva;
	public $multicurrency_total_ttc;

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
		
		if (isset($this->fk_supplier_proposal)) {
			 $this->fk_supplier_proposal = trim($this->fk_supplier_proposal);
		}
		if (isset($this->fk_parent_line)) {
			 $this->fk_parent_line = trim($this->fk_parent_line);
		}
		if (isset($this->fk_product)) {
			 $this->fk_product = trim($this->fk_product);
		}
		if (isset($this->label)) {
			 $this->label = trim($this->label);
		}
		if (isset($this->description)) {
			 $this->description = trim($this->description);
		}
		if (isset($this->fk_remise_except)) {
			 $this->fk_remise_except = trim($this->fk_remise_except);
		}
		if (isset($this->vat_src_code)) {
			 $this->vat_src_code = trim($this->vat_src_code);
		}
		if (isset($this->tva_tx)) {
			 $this->tva_tx = trim($this->tva_tx);
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
		if (isset($this->qty)) {
			 $this->qty = trim($this->qty);
		}
		if (isset($this->fk_unit)) {
			 $this->fk_unit = trim($this->fk_unit);
		}
		if (isset($this->remise_percent)) {
			 $this->remise_percent = trim($this->remise_percent);
		}
		if (isset($this->remise)) {
			 $this->remise = trim($this->remise);
		}
		if (isset($this->price)) {
			 $this->price = trim($this->price);
		}
		if (isset($this->subprice)) {
			 $this->subprice = trim($this->subprice);
		}
		if (isset($this->total_ht)) {
			 $this->total_ht = trim($this->total_ht);
		}
		if (isset($this->total_tva)) {
			 $this->total_tva = trim($this->total_tva);
		}
		if (isset($this->total_localtax1)) {
			 $this->total_localtax1 = trim($this->total_localtax1);
		}
		if (isset($this->total_localtax2)) {
			 $this->total_localtax2 = trim($this->total_localtax2);
		}
		if (isset($this->total_ttc)) {
			 $this->total_ttc = trim($this->total_ttc);
		}
		if (isset($this->product_type)) {
			 $this->product_type = trim($this->product_type);
		}
		if (isset($this->info_bits)) {
			 $this->info_bits = trim($this->info_bits);
		}
		if (isset($this->buy_price_ht)) {
			 $this->buy_price_ht = trim($this->buy_price_ht);
		}
		if (isset($this->fk_product_fournisseur_price)) {
			 $this->fk_product_fournisseur_price = trim($this->fk_product_fournisseur_price);
		}
		if (isset($this->special_code)) {
			 $this->special_code = trim($this->special_code);
		}
		if (isset($this->rang)) {
			 $this->rang = trim($this->rang);
		}
		if (isset($this->ref_fourn)) {
			 $this->ref_fourn = trim($this->ref_fourn);
		}
		if (isset($this->fk_multicurrency)) {
			 $this->fk_multicurrency = trim($this->fk_multicurrency);
		}
		if (isset($this->multicurrency_code)) {
			 $this->multicurrency_code = trim($this->multicurrency_code);
		}
		if (isset($this->multicurrency_subprice)) {
			 $this->multicurrency_subprice = trim($this->multicurrency_subprice);
		}
		if (isset($this->multicurrency_total_ht)) {
			 $this->multicurrency_total_ht = trim($this->multicurrency_total_ht);
		}
		if (isset($this->multicurrency_total_tva)) {
			 $this->multicurrency_total_tva = trim($this->multicurrency_total_tva);
		}
		if (isset($this->multicurrency_total_ttc)) {
			 $this->multicurrency_total_ttc = trim($this->multicurrency_total_ttc);
		}

		

		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';
		
		$sql.= 'fk_supplier_proposal,';
		$sql.= 'fk_parent_line,';
		$sql.= 'fk_product,';
		$sql.= 'label,';
		$sql.= 'description,';
		$sql.= 'fk_remise_except,';
		$sql.= 'vat_src_code,';
		$sql.= 'tva_tx,';
		$sql.= 'localtax1_tx,';
		$sql.= 'localtax1_type,';
		$sql.= 'localtax2_tx,';
		$sql.= 'localtax2_type,';
		$sql.= 'qty,';
		$sql.= 'fk_unit,';
		$sql.= 'remise_percent,';
		$sql.= 'remise,';
		$sql.= 'price,';
		$sql.= 'subprice,';
		$sql.= 'total_ht,';
		$sql.= 'total_tva,';
		$sql.= 'total_localtax1,';
		$sql.= 'total_localtax2,';
		$sql.= 'total_ttc,';
		$sql.= 'product_type,';
		$sql.= 'info_bits,';
		$sql.= 'buy_price_ht,';
		$sql.= 'fk_product_fournisseur_price,';
		$sql.= 'special_code,';
		$sql.= 'rang,';
		$sql.= 'ref_fourn,';
		$sql.= 'fk_multicurrency,';
		$sql.= 'multicurrency_code,';
		$sql.= 'multicurrency_subprice,';
		$sql.= 'multicurrency_total_ht,';
		$sql.= 'multicurrency_total_tva';
		$sql.= 'multicurrency_total_ttc';

		
		$sql .= ') VALUES (';
		
		$sql .= ' '.(! isset($this->fk_supplier_proposal)?'NULL':$this->fk_supplier_proposal).',';
		$sql .= ' '.(! isset($this->fk_parent_line)?'NULL':$this->fk_parent_line).',';
		$sql .= ' '.(! isset($this->fk_product)?'NULL':$this->fk_product).',';
		$sql .= ' '.(! isset($this->label)?'NULL':"'".$this->db->escape($this->label)."'").',';
		$sql .= ' '.(! isset($this->description)?'NULL':"'".$this->db->escape($this->description)."'").',';
		$sql .= ' '.(! isset($this->fk_remise_except)?'NULL':$this->fk_remise_except).',';
		$sql .= ' '.(! isset($this->vat_src_code)?'NULL':"'".$this->db->escape($this->vat_src_code)."'").',';
		$sql .= ' '.(! isset($this->tva_tx)?'NULL':"'".$this->tva_tx."'").',';
		$sql .= ' '.(! isset($this->localtax1_tx)?'NULL':"'".$this->localtax1_tx."'").',';
		$sql .= ' '.(! isset($this->localtax1_type)?'NULL':"'".$this->db->escape($this->localtax1_type)."'").',';
		$sql .= ' '.(! isset($this->localtax2_tx)?'NULL':"'".$this->localtax2_tx."'").',';
		$sql .= ' '.(! isset($this->localtax2_type)?'NULL':"'".$this->db->escape($this->localtax2_type)."'").',';
		$sql .= ' '.(! isset($this->qty)?'NULL':"'".$this->qty."'").',';
		$sql .= ' '.(! isset($this->fk_unit)?'NULL':$this->fk_unit).',';
		$sql .= ' '.(! isset($this->remise_percent)?'NULL':"'".$this->remise_percent."'").',';
		$sql .= ' '.(! isset($this->remise)?'NULL':"'".$this->remise."'").',';
		$sql .= ' '.(! isset($this->price)?'NULL':"'".$this->price."'").',';
		$sql .= ' '.(! isset($this->subprice)?'NULL':"'".$this->subprice."'").',';
		$sql .= ' '.(! isset($this->total_ht)?'NULL':"'".$this->total_ht."'").',';
		$sql .= ' '.(! isset($this->total_tva)?'NULL':"'".$this->total_tva."'").',';
		$sql .= ' '.(! isset($this->total_localtax1)?'NULL':"'".$this->total_localtax1."'").',';
		$sql .= ' '.(! isset($this->total_localtax2)?'NULL':"'".$this->total_localtax2."'").',';
		$sql .= ' '.(! isset($this->total_ttc)?'NULL':"'".$this->total_ttc."'").',';
		$sql .= ' '.(! isset($this->product_type)?'NULL':$this->product_type).',';
		$sql .= ' '.(! isset($this->info_bits)?'NULL':$this->info_bits).',';
		$sql .= ' '.(! isset($this->buy_price_ht)?'NULL':"'".$this->buy_price_ht."'").',';
		$sql .= ' '.(! isset($this->fk_product_fournisseur_price)?'NULL':$this->fk_product_fournisseur_price).',';
		$sql .= ' '.(! isset($this->special_code)?'NULL':$this->special_code).',';
		$sql .= ' '.(! isset($this->rang)?'NULL':$this->rang).',';
		$sql .= ' '.(! isset($this->ref_fourn)?'NULL':"'".$this->db->escape($this->ref_fourn)."'").',';
		$sql .= ' '.(! isset($this->fk_multicurrency)?'NULL':$this->fk_multicurrency).',';
		$sql .= ' '.(! isset($this->multicurrency_code)?'NULL':"'".$this->db->escape($this->multicurrency_code)."'").',';
		$sql .= ' '.(! isset($this->multicurrency_subprice)?'NULL':"'".$this->multicurrency_subprice."'").',';
		$sql .= ' '.(! isset($this->multicurrency_total_ht)?'NULL':"'".$this->multicurrency_total_ht."'").',';
		$sql .= ' '.(! isset($this->multicurrency_total_tva)?'NULL':"'".$this->multicurrency_total_tva."'").',';
		$sql .= ' '.(! isset($this->multicurrency_total_ttc)?'NULL':"'".$this->multicurrency_total_ttc."'");

		
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
		
		$sql .= " t.fk_supplier_proposal,";
		$sql .= " t.fk_parent_line,";
		$sql .= " t.fk_product,";
		$sql .= " t.label,";
		$sql .= " t.description,";
		$sql .= " t.fk_remise_except,";
		$sql .= " t.vat_src_code,";
		$sql .= " t.tva_tx,";
		$sql .= " t.localtax1_tx,";
		$sql .= " t.localtax1_type,";
		$sql .= " t.localtax2_tx,";
		$sql .= " t.localtax2_type,";
		$sql .= " t.qty,";
		$sql .= " t.fk_unit,";
		$sql .= " t.remise_percent,";
		$sql .= " t.remise,";
		$sql .= " t.price,";
		$sql .= " t.subprice,";
		$sql .= " t.total_ht,";
		$sql .= " t.total_tva,";
		$sql .= " t.total_localtax1,";
		$sql .= " t.total_localtax2,";
		$sql .= " t.total_ttc,";
		$sql .= " t.product_type,";
		$sql .= " t.info_bits,";
		$sql .= " t.buy_price_ht,";
		$sql .= " t.fk_product_fournisseur_price,";
		$sql .= " t.special_code,";
		$sql .= " t.rang,";
		$sql .= " t.ref_fourn,";
		$sql .= " t.fk_multicurrency,";
		$sql .= " t.multicurrency_code,";
		$sql .= " t.multicurrency_subprice,";
		$sql .= " t.multicurrency_total_ht,";
		$sql .= " t.multicurrency_total_tva,";
		$sql .= " t.multicurrency_total_ttc";

		
		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
		    $sql .= " AND entity IN (" . getEntity("supplierproposaldet", 1) . ")";
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
				
				$this->fk_supplier_proposal = $obj->fk_supplier_proposal;
				$this->fk_parent_line = $obj->fk_parent_line;
				$this->fk_product = $obj->fk_product;
				$this->label = $obj->label;
				$this->description = $obj->description;
				$this->fk_remise_except = $obj->fk_remise_except;
				$this->vat_src_code = $obj->vat_src_code;
				$this->tva_tx = $obj->tva_tx;
				$this->localtax1_tx = $obj->localtax1_tx;
				$this->localtax1_type = $obj->localtax1_type;
				$this->localtax2_tx = $obj->localtax2_tx;
				$this->localtax2_type = $obj->localtax2_type;
				$this->qty = $obj->qty;
				$this->fk_unit = $obj->fk_unit;
				$this->remise_percent = $obj->remise_percent;
				$this->remise = $obj->remise;
				$this->price = $obj->price;
				$this->subprice = $obj->subprice;
				$this->total_ht = $obj->total_ht;
				$this->total_tva = $obj->total_tva;
				$this->total_localtax1 = $obj->total_localtax1;
				$this->total_localtax2 = $obj->total_localtax2;
				$this->total_ttc = $obj->total_ttc;
				$this->product_type = $obj->product_type;
				$this->info_bits = $obj->info_bits;
				$this->buy_price_ht = $obj->buy_price_ht;
				$this->fk_product_fournisseur_price = $obj->fk_product_fournisseur_price;
				$this->special_code = $obj->special_code;
				$this->rang = $obj->rang;
				$this->ref_fourn = $obj->ref_fourn;
				$this->fk_multicurrency = $obj->fk_multicurrency;
				$this->multicurrency_code = $obj->multicurrency_code;
				$this->multicurrency_subprice = $obj->multicurrency_subprice;
				$this->multicurrency_total_ht = $obj->multicurrency_total_ht;
				$this->multicurrency_total_tva = $obj->multicurrency_total_tva;
				$this->multicurrency_total_ttc = $obj->multicurrency_total_ttc;

				
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
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND')
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		
		$sql .= " t.fk_supplier_proposal,";
		$sql .= " t.fk_parent_line,";
		$sql .= " t.fk_product,";
		$sql .= " t.label,";
		$sql .= " t.description,";
		$sql .= " t.fk_remise_except,";
		$sql .= " t.vat_src_code,";
		$sql .= " t.tva_tx,";
		$sql .= " t.localtax1_tx,";
		$sql .= " t.localtax1_type,";
		$sql .= " t.localtax2_tx,";
		$sql .= " t.localtax2_type,";
		$sql .= " t.qty,";
		$sql .= " t.fk_unit,";
		$sql .= " t.remise_percent,";
		$sql .= " t.remise,";
		$sql .= " t.price,";
		$sql .= " t.subprice,";
		$sql .= " t.total_ht,";
		$sql .= " t.total_tva,";
		$sql .= " t.total_localtax1,";
		$sql .= " t.total_localtax2,";
		$sql .= " t.total_ttc,";
		$sql .= " t.product_type,";
		$sql .= " t.info_bits,";
		$sql .= " t.buy_price_ht,";
		$sql .= " t.fk_product_fournisseur_price,";
		$sql .= " t.special_code,";
		$sql .= " t.rang,";
		$sql .= " t.ref_fourn,";
		$sql .= " t.fk_multicurrency,";
		$sql .= " t.multicurrency_code,";
		$sql .= " t.multicurrency_subprice,";
		$sql .= " t.multicurrency_total_ht,";
		$sql .= " t.multicurrency_total_tva,";
		$sql .= " t.multicurrency_total_ttc";

		
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
		    $sql .= " AND entity IN (" . getEntity("supplierproposaldet", 1) . ")";
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' AND ' . implode(' '.$filtermode.' ', $sqlwhere);
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
				$line = new SupplierproposaldetLine();

				$line->id = $obj->rowid;
				
				$line->fk_supplier_proposal = $obj->fk_supplier_proposal;
				$line->fk_parent_line = $obj->fk_parent_line;
				$line->fk_product = $obj->fk_product;
				$line->label = $obj->label;
				$line->description = $obj->description;
				$line->fk_remise_except = $obj->fk_remise_except;
				$line->vat_src_code = $obj->vat_src_code;
				$line->tva_tx = $obj->tva_tx;
				$line->localtax1_tx = $obj->localtax1_tx;
				$line->localtax1_type = $obj->localtax1_type;
				$line->localtax2_tx = $obj->localtax2_tx;
				$line->localtax2_type = $obj->localtax2_type;
				$line->qty = $obj->qty;
				$line->fk_unit = $obj->fk_unit;
				$line->remise_percent = $obj->remise_percent;
				$line->remise = $obj->remise;
				$line->price = $obj->price;
				$line->subprice = $obj->subprice;
				$line->total_ht = $obj->total_ht;
				$line->total_tva = $obj->total_tva;
				$line->total_localtax1 = $obj->total_localtax1;
				$line->total_localtax2 = $obj->total_localtax2;
				$line->total_ttc = $obj->total_ttc;
				$line->product_type = $obj->product_type;
				$line->info_bits = $obj->info_bits;
				$line->buy_price_ht = $obj->buy_price_ht;
				$line->fk_product_fournisseur_price = $obj->fk_product_fournisseur_price;
				$line->special_code = $obj->special_code;
				$line->rang = $obj->rang;
				$line->ref_fourn = $obj->ref_fourn;
				$line->fk_multicurrency = $obj->fk_multicurrency;
				$line->multicurrency_code = $obj->multicurrency_code;
				$line->multicurrency_subprice = $obj->multicurrency_subprice;
				$line->multicurrency_total_ht = $obj->multicurrency_total_ht;
				$line->multicurrency_total_tva = $obj->multicurrency_total_tva;
				$line->multicurrency_total_ttc = $obj->multicurrency_total_ttc;

				

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
		
		if (isset($this->fk_supplier_proposal)) {
			 $this->fk_supplier_proposal = trim($this->fk_supplier_proposal);
		}
		if (isset($this->fk_parent_line)) {
			 $this->fk_parent_line = trim($this->fk_parent_line);
		}
		if (isset($this->fk_product)) {
			 $this->fk_product = trim($this->fk_product);
		}
		if (isset($this->label)) {
			 $this->label = trim($this->label);
		}
		if (isset($this->description)) {
			 $this->description = trim($this->description);
		}
		if (isset($this->fk_remise_except)) {
			 $this->fk_remise_except = trim($this->fk_remise_except);
		}
		if (isset($this->vat_src_code)) {
			 $this->vat_src_code = trim($this->vat_src_code);
		}
		if (isset($this->tva_tx)) {
			 $this->tva_tx = trim($this->tva_tx);
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
		if (isset($this->qty)) {
			 $this->qty = trim($this->qty);
		}
		if (isset($this->fk_unit)) {
			 $this->fk_unit = trim($this->fk_unit);
		}
		if (isset($this->remise_percent)) {
			 $this->remise_percent = trim($this->remise_percent);
		}
		if (isset($this->remise)) {
			 $this->remise = trim($this->remise);
		}
		if (isset($this->price)) {
			 $this->price = trim($this->price);
		}
		if (isset($this->subprice)) {
			 $this->subprice = trim($this->subprice);
		}
		if (isset($this->total_ht)) {
			 $this->total_ht = trim($this->total_ht);
		}
		if (isset($this->total_tva)) {
			 $this->total_tva = trim($this->total_tva);
		}
		if (isset($this->total_localtax1)) {
			 $this->total_localtax1 = trim($this->total_localtax1);
		}
		if (isset($this->total_localtax2)) {
			 $this->total_localtax2 = trim($this->total_localtax2);
		}
		if (isset($this->total_ttc)) {
			 $this->total_ttc = trim($this->total_ttc);
		}
		if (isset($this->product_type)) {
			 $this->product_type = trim($this->product_type);
		}
		if (isset($this->info_bits)) {
			 $this->info_bits = trim($this->info_bits);
		}
		if (isset($this->buy_price_ht)) {
			 $this->buy_price_ht = trim($this->buy_price_ht);
		}
		if (isset($this->fk_product_fournisseur_price)) {
			 $this->fk_product_fournisseur_price = trim($this->fk_product_fournisseur_price);
		}
		if (isset($this->special_code)) {
			 $this->special_code = trim($this->special_code);
		}
		if (isset($this->rang)) {
			 $this->rang = trim($this->rang);
		}
		if (isset($this->ref_fourn)) {
			 $this->ref_fourn = trim($this->ref_fourn);
		}
		if (isset($this->fk_multicurrency)) {
			 $this->fk_multicurrency = trim($this->fk_multicurrency);
		}
		if (isset($this->multicurrency_code)) {
			 $this->multicurrency_code = trim($this->multicurrency_code);
		}
		if (isset($this->multicurrency_subprice)) {
			 $this->multicurrency_subprice = trim($this->multicurrency_subprice);
		}
		if (isset($this->multicurrency_total_ht)) {
			 $this->multicurrency_total_ht = trim($this->multicurrency_total_ht);
		}
		if (isset($this->multicurrency_total_tva)) {
			 $this->multicurrency_total_tva = trim($this->multicurrency_total_tva);
		}
		if (isset($this->multicurrency_total_ttc)) {
			 $this->multicurrency_total_ttc = trim($this->multicurrency_total_ttc);
		}

		

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';
		
		$sql .= ' fk_supplier_proposal = '.(isset($this->fk_supplier_proposal)?$this->fk_supplier_proposal:"null").',';
		$sql .= ' fk_parent_line = '.(isset($this->fk_parent_line)?$this->fk_parent_line:"null").',';
		$sql .= ' fk_product = '.(isset($this->fk_product)?$this->fk_product:"null").',';
		$sql .= ' label = '.(isset($this->label)?"'".$this->db->escape($this->label)."'":"null").',';
		$sql .= ' description = '.(isset($this->description)?"'".$this->db->escape($this->description)."'":"null").',';
		$sql .= ' fk_remise_except = '.(isset($this->fk_remise_except)?$this->fk_remise_except:"null").',';
		$sql .= ' vat_src_code = '.(isset($this->vat_src_code)?"'".$this->db->escape($this->vat_src_code)."'":"null").',';
		$sql .= ' tva_tx = '.(isset($this->tva_tx)?$this->tva_tx:"null").',';
		$sql .= ' localtax1_tx = '.(isset($this->localtax1_tx)?$this->localtax1_tx:"null").',';
		$sql .= ' localtax1_type = '.(isset($this->localtax1_type)?"'".$this->db->escape($this->localtax1_type)."'":"null").',';
		$sql .= ' localtax2_tx = '.(isset($this->localtax2_tx)?$this->localtax2_tx:"null").',';
		$sql .= ' localtax2_type = '.(isset($this->localtax2_type)?"'".$this->db->escape($this->localtax2_type)."'":"null").',';
		$sql .= ' qty = '.(isset($this->qty)?$this->qty:"null").',';
		$sql .= ' fk_unit = '.(isset($this->fk_unit)?$this->fk_unit:"null").',';
		$sql .= ' remise_percent = '.(isset($this->remise_percent)?$this->remise_percent:"null").',';
		$sql .= ' remise = '.(isset($this->remise)?$this->remise:"null").',';
		$sql .= ' price = '.(isset($this->price)?$this->price:"null").',';
		$sql .= ' subprice = '.(isset($this->subprice)?$this->subprice:"null").',';
		$sql .= ' total_ht = '.(isset($this->total_ht)?$this->total_ht:"null").',';
		$sql .= ' total_tva = '.(isset($this->total_tva)?$this->total_tva:"null").',';
		$sql .= ' total_localtax1 = '.(isset($this->total_localtax1)?$this->total_localtax1:"null").',';
		$sql .= ' total_localtax2 = '.(isset($this->total_localtax2)?$this->total_localtax2:"null").',';
		$sql .= ' total_ttc = '.(isset($this->total_ttc)?$this->total_ttc:"null").',';
		$sql .= ' product_type = '.(isset($this->product_type)?$this->product_type:"null").',';
		$sql .= ' info_bits = '.(isset($this->info_bits)?$this->info_bits:"null").',';
		$sql .= ' buy_price_ht = '.(isset($this->buy_price_ht)?$this->buy_price_ht:"null").',';
		$sql .= ' fk_product_fournisseur_price = '.(isset($this->fk_product_fournisseur_price)?$this->fk_product_fournisseur_price:"null").',';
		$sql .= ' special_code = '.(isset($this->special_code)?$this->special_code:"null").',';
		$sql .= ' rang = '.(isset($this->rang)?$this->rang:"null").',';
		$sql .= ' ref_fourn = '.(isset($this->ref_fourn)?"'".$this->db->escape($this->ref_fourn)."'":"null").',';
		$sql .= ' fk_multicurrency = '.(isset($this->fk_multicurrency)?$this->fk_multicurrency:"null").',';
		$sql .= ' multicurrency_code = '.(isset($this->multicurrency_code)?"'".$this->db->escape($this->multicurrency_code)."'":"null").',';
		$sql .= ' multicurrency_subprice = '.(isset($this->multicurrency_subprice)?$this->multicurrency_subprice:"null").',';
		$sql .= ' multicurrency_total_ht = '.(isset($this->multicurrency_total_ht)?$this->multicurrency_total_ht:"null").',';
		$sql .= ' multicurrency_total_tva = '.(isset($this->multicurrency_total_tva)?$this->multicurrency_total_tva:"null").',';
		$sql .= ' multicurrency_total_ttc = '.(isset($this->multicurrency_total_ttc)?$this->multicurrency_total_ttc:"null");

        
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
		$object = new Supplierproposaldet($this->db);

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

        $url = DOL_URL_ROOT.'/purchase/'.$this->table_name.'_card.php?id='.$this->id;
        
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
		
		$this->fk_supplier_proposal = '';
		$this->fk_parent_line = '';
		$this->fk_product = '';
		$this->label = '';
		$this->description = '';
		$this->fk_remise_except = '';
		$this->vat_src_code = '';
		$this->tva_tx = '';
		$this->localtax1_tx = '';
		$this->localtax1_type = '';
		$this->localtax2_tx = '';
		$this->localtax2_type = '';
		$this->qty = '';
		$this->fk_unit = '';
		$this->remise_percent = '';
		$this->remise = '';
		$this->price = '';
		$this->subprice = '';
		$this->total_ht = '';
		$this->total_tva = '';
		$this->total_localtax1 = '';
		$this->total_localtax2 = '';
		$this->total_ttc = '';
		$this->product_type = '';
		$this->info_bits = '';
		$this->buy_price_ht = '';
		$this->fk_product_fournisseur_price = '';
		$this->special_code = '';
		$this->rang = '';
		$this->ref_fourn = '';
		$this->fk_multicurrency = '';
		$this->multicurrency_code = '';
		$this->multicurrency_subprice = '';
		$this->multicurrency_total_ht = '';
		$this->multicurrency_total_tva = '';
		$this->multicurrency_total_ttc = '';

		
	}

}

/**
 * Class SupplierproposaldetLine
 */
class SupplierproposaldetLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	
	public $fk_supplier_proposal;
	public $fk_parent_line;
	public $fk_product;
	public $label;
	public $description;
	public $fk_remise_except;
	public $vat_src_code;
	public $tva_tx;
	public $localtax1_tx;
	public $localtax1_type;
	public $localtax2_tx;
	public $localtax2_type;
	public $qty;
	public $fk_unit;
	public $remise_percent;
	public $remise;
	public $price;
	public $subprice;
	public $total_ht;
	public $total_tva;
	public $total_localtax1;
	public $total_localtax2;
	public $total_ttc;
	public $product_type;
	public $info_bits;
	public $buy_price_ht;
	public $fk_product_fournisseur_price;
	public $special_code;
	public $rang;
	public $ref_fourn;
	public $fk_multicurrency;
	public $multicurrency_code;
	public $multicurrency_subprice;
	public $multicurrency_total_ht;
	public $multicurrency_total_tva;
	public $multicurrency_total_ttc;

	/**
	 * @var mixed Sample line property 2
	 */
	
}
