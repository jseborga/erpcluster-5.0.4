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
 * \file    purchase/facturefournadd.class.php
 * \ingroup purchase
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Facturefournadd
 *
 * Put here description of your class
 * @see CommonObject
 */
class Facturefournadd extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'facturefournadd';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'facture_fourn_add';

	/**
	 * @var FacturefournaddLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	
	public $fk_facture_fourn;
	public $code_facture;
	public $code_type_purchase;
	public $nit_company;
	public $nfiscal;
	public $ndui;
	public $num_autoriz;
	public $nit;
	public $razsoc;
	public $cod_control;
	public $codqr;
	public $amountfiscal;
	public $amountnofiscal;
	public $amount_ice;
	public $discount;
	public $datec = '';
	public $tms = '';
	public $localtax3;
	public $localtax4;
	public $localtax5;
	public $localtax6;
	public $localtax7;
	public $localtax8;
	public $localtax9;

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
		
		if (isset($this->fk_facture_fourn)) {
			 $this->fk_facture_fourn = trim($this->fk_facture_fourn);
		}
		if (isset($this->code_facture)) {
			 $this->code_facture = trim($this->code_facture);
		}
		if (isset($this->code_type_purchase)) {
			 $this->code_type_purchase = trim($this->code_type_purchase);
		}
		if (isset($this->nit_company)) {
			 $this->nit_company = trim($this->nit_company);
		}
		if (isset($this->nfiscal)) {
			 $this->nfiscal = trim($this->nfiscal);
		}
		if (isset($this->ndui)) {
			 $this->ndui = trim($this->ndui);
		}
		if (isset($this->num_autoriz)) {
			 $this->num_autoriz = trim($this->num_autoriz);
		}
		if (isset($this->nit)) {
			 $this->nit = trim($this->nit);
		}
		if (isset($this->razsoc)) {
			 $this->razsoc = trim($this->razsoc);
		}
		if (isset($this->cod_control)) {
			 $this->cod_control = trim($this->cod_control);
		}
		if (isset($this->codqr)) {
			 $this->codqr = trim($this->codqr);
		}
		if (isset($this->amountfiscal)) {
			 $this->amountfiscal = trim($this->amountfiscal);
		}
		if (isset($this->amountnofiscal)) {
			 $this->amountnofiscal = trim($this->amountnofiscal);
		}
		if (isset($this->amount_ice)) {
			 $this->amount_ice = trim($this->amount_ice);
		}
		if (isset($this->discount)) {
			 $this->discount = trim($this->discount);
		}
		if (isset($this->localtax3)) {
			 $this->localtax3 = trim($this->localtax3);
		}
		if (isset($this->localtax4)) {
			 $this->localtax4 = trim($this->localtax4);
		}
		if (isset($this->localtax5)) {
			 $this->localtax5 = trim($this->localtax5);
		}
		if (isset($this->localtax6)) {
			 $this->localtax6 = trim($this->localtax6);
		}
		if (isset($this->localtax7)) {
			 $this->localtax7 = trim($this->localtax7);
		}
		if (isset($this->localtax8)) {
			 $this->localtax8 = trim($this->localtax8);
		}
		if (isset($this->localtax9)) {
			 $this->localtax9 = trim($this->localtax9);
		}

		

		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';
		
		$sql.= 'fk_facture_fourn,';
		$sql.= 'code_facture,';
		$sql.= 'code_type_purchase,';
		$sql.= 'nit_company,';
		$sql.= 'nfiscal,';
		$sql.= 'ndui,';
		$sql.= 'num_autoriz,';
		$sql.= 'nit,';
		$sql.= 'razsoc,';
		$sql.= 'cod_control,';
		$sql.= 'codqr,';
		$sql.= 'amountfiscal,';
		$sql.= 'amountnofiscal,';
		$sql.= 'amount_ice,';
		$sql.= 'discount,';
		$sql.= 'datec,';
		$sql.= 'localtax3,';
		$sql.= 'localtax4,';
		$sql.= 'localtax5,';
		$sql.= 'localtax6,';
		$sql.= 'localtax7';
		$sql.= 'localtax8';
		$sql.= 'localtax9';

		
		$sql .= ') VALUES (';
		
		$sql .= ' '.(! isset($this->fk_facture_fourn)?'NULL':$this->fk_facture_fourn).',';
		$sql .= ' '.(! isset($this->code_facture)?'NULL':"'".$this->db->escape($this->code_facture)."'").',';
		$sql .= ' '.(! isset($this->code_type_purchase)?'NULL':"'".$this->db->escape($this->code_type_purchase)."'").',';
		$sql .= ' '.(! isset($this->nit_company)?'NULL':"'".$this->db->escape($this->nit_company)."'").',';
		$sql .= ' '.(! isset($this->nfiscal)?'NULL':$this->nfiscal).',';
		$sql .= ' '.(! isset($this->ndui)?'NULL':"'".$this->db->escape($this->ndui)."'").',';
		$sql .= ' '.(! isset($this->num_autoriz)?'NULL':"'".$this->db->escape($this->num_autoriz)."'").',';
		$sql .= ' '.(! isset($this->nit)?'NULL':"'".$this->db->escape($this->nit)."'").',';
		$sql .= ' '.(! isset($this->razsoc)?'NULL':"'".$this->db->escape($this->razsoc)."'").',';
		$sql .= ' '.(! isset($this->cod_control)?'NULL':"'".$this->db->escape($this->cod_control)."'").',';
		$sql .= ' '.(! isset($this->codqr)?'NULL':"'".$this->db->escape($this->codqr)."'").',';
		$sql .= ' '.(! isset($this->amountfiscal)?'NULL':"'".$this->amountfiscal."'").',';
		$sql .= ' '.(! isset($this->amountnofiscal)?'NULL':"'".$this->amountnofiscal."'").',';
		$sql .= ' '.(! isset($this->amount_ice)?'NULL':"'".$this->amount_ice."'").',';
		$sql .= ' '.(! isset($this->discount)?'NULL':"'".$this->discount."'").',';
		$sql .= ' '."'".$this->db->idate(dol_now())."'".',';
		$sql .= ' '.(! isset($this->localtax3)?'NULL':"'".$this->localtax3."'").',';
		$sql .= ' '.(! isset($this->localtax4)?'NULL':"'".$this->localtax4."'").',';
		$sql .= ' '.(! isset($this->localtax5)?'NULL':"'".$this->localtax5."'").',';
		$sql .= ' '.(! isset($this->localtax6)?'NULL':"'".$this->localtax6."'").',';
		$sql .= ' '.(! isset($this->localtax7)?'NULL':"'".$this->localtax7."'").',';
		$sql .= ' '.(! isset($this->localtax8)?'NULL':"'".$this->localtax8."'").',';
		$sql .= ' '.(! isset($this->localtax9)?'NULL':"'".$this->localtax9."'");

		
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
		
		$sql .= " t.fk_facture_fourn,";
		$sql .= " t.code_facture,";
		$sql .= " t.code_type_purchase,";
		$sql .= " t.nit_company,";
		$sql .= " t.nfiscal,";
		$sql .= " t.ndui,";
		$sql .= " t.num_autoriz,";
		$sql .= " t.nit,";
		$sql .= " t.razsoc,";
		$sql .= " t.cod_control,";
		$sql .= " t.codqr,";
		$sql .= " t.amountfiscal,";
		$sql .= " t.amountnofiscal,";
		$sql .= " t.amount_ice,";
		$sql .= " t.discount,";
		$sql .= " t.datec,";
		$sql .= " t.tms,";
		$sql .= " t.localtax3,";
		$sql .= " t.localtax4,";
		$sql .= " t.localtax5,";
		$sql .= " t.localtax6,";
		$sql .= " t.localtax7,";
		$sql .= " t.localtax8,";
		$sql .= " t.localtax9";

		
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
				
				$this->fk_facture_fourn = $obj->fk_facture_fourn;
				$this->code_facture = $obj->code_facture;
				$this->code_type_purchase = $obj->code_type_purchase;
				$this->nit_company = $obj->nit_company;
				$this->nfiscal = $obj->nfiscal;
				$this->ndui = $obj->ndui;
				$this->num_autoriz = $obj->num_autoriz;
				$this->nit = $obj->nit;
				$this->razsoc = $obj->razsoc;
				$this->cod_control = $obj->cod_control;
				$this->codqr = $obj->codqr;
				$this->amountfiscal = $obj->amountfiscal;
				$this->amountnofiscal = $obj->amountnofiscal;
				$this->amount_ice = $obj->amount_ice;
				$this->discount = $obj->discount;
				$this->datec = $this->db->jdate($obj->datec);
				$this->tms = $this->db->jdate($obj->tms);
				$this->localtax3 = $obj->localtax3;
				$this->localtax4 = $obj->localtax4;
				$this->localtax5 = $obj->localtax5;
				$this->localtax6 = $obj->localtax6;
				$this->localtax7 = $obj->localtax7;
				$this->localtax8 = $obj->localtax8;
				$this->localtax9 = $obj->localtax9;

				
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
		
		$sql .= " t.fk_facture_fourn,";
		$sql .= " t.code_facture,";
		$sql .= " t.code_type_purchase,";
		$sql .= " t.nit_company,";
		$sql .= " t.nfiscal,";
		$sql .= " t.ndui,";
		$sql .= " t.num_autoriz,";
		$sql .= " t.nit,";
		$sql .= " t.razsoc,";
		$sql .= " t.cod_control,";
		$sql .= " t.codqr,";
		$sql .= " t.amountfiscal,";
		$sql .= " t.amountnofiscal,";
		$sql .= " t.amount_ice,";
		$sql .= " t.discount,";
		$sql .= " t.datec,";
		$sql .= " t.tms,";
		$sql .= " t.localtax3,";
		$sql .= " t.localtax4,";
		$sql .= " t.localtax5,";
		$sql .= " t.localtax6,";
		$sql .= " t.localtax7,";
		$sql .= " t.localtax8,";
		$sql .= " t.localtax9";

		
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
				$line = new FacturefournaddLine();

				$line->id = $obj->rowid;
				
				$line->fk_facture_fourn = $obj->fk_facture_fourn;
				$line->code_facture = $obj->code_facture;
				$line->code_type_purchase = $obj->code_type_purchase;
				$line->nit_company = $obj->nit_company;
				$line->nfiscal = $obj->nfiscal;
				$line->ndui = $obj->ndui;
				$line->num_autoriz = $obj->num_autoriz;
				$line->nit = $obj->nit;
				$line->razsoc = $obj->razsoc;
				$line->cod_control = $obj->cod_control;
				$line->codqr = $obj->codqr;
				$line->amountfiscal = $obj->amountfiscal;
				$line->amountnofiscal = $obj->amountnofiscal;
				$line->amount_ice = $obj->amount_ice;
				$line->discount = $obj->discount;
				$line->datec = $this->db->jdate($obj->datec);
				$line->tms = $this->db->jdate($obj->tms);
				$line->localtax3 = $obj->localtax3;
				$line->localtax4 = $obj->localtax4;
				$line->localtax5 = $obj->localtax5;
				$line->localtax6 = $obj->localtax6;
				$line->localtax7 = $obj->localtax7;
				$line->localtax8 = $obj->localtax8;
				$line->localtax9 = $obj->localtax9;

				

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
		
		if (isset($this->fk_facture_fourn)) {
			 $this->fk_facture_fourn = trim($this->fk_facture_fourn);
		}
		if (isset($this->code_facture)) {
			 $this->code_facture = trim($this->code_facture);
		}
		if (isset($this->code_type_purchase)) {
			 $this->code_type_purchase = trim($this->code_type_purchase);
		}
		if (isset($this->nit_company)) {
			 $this->nit_company = trim($this->nit_company);
		}
		if (isset($this->nfiscal)) {
			 $this->nfiscal = trim($this->nfiscal);
		}
		if (isset($this->ndui)) {
			 $this->ndui = trim($this->ndui);
		}
		if (isset($this->num_autoriz)) {
			 $this->num_autoriz = trim($this->num_autoriz);
		}
		if (isset($this->nit)) {
			 $this->nit = trim($this->nit);
		}
		if (isset($this->razsoc)) {
			 $this->razsoc = trim($this->razsoc);
		}
		if (isset($this->cod_control)) {
			 $this->cod_control = trim($this->cod_control);
		}
		if (isset($this->codqr)) {
			 $this->codqr = trim($this->codqr);
		}
		if (isset($this->amountfiscal)) {
			 $this->amountfiscal = trim($this->amountfiscal);
		}
		if (isset($this->amountnofiscal)) {
			 $this->amountnofiscal = trim($this->amountnofiscal);
		}
		if (isset($this->amount_ice)) {
			 $this->amount_ice = trim($this->amount_ice);
		}
		if (isset($this->discount)) {
			 $this->discount = trim($this->discount);
		}
		if (isset($this->localtax3)) {
			 $this->localtax3 = trim($this->localtax3);
		}
		if (isset($this->localtax4)) {
			 $this->localtax4 = trim($this->localtax4);
		}
		if (isset($this->localtax5)) {
			 $this->localtax5 = trim($this->localtax5);
		}
		if (isset($this->localtax6)) {
			 $this->localtax6 = trim($this->localtax6);
		}
		if (isset($this->localtax7)) {
			 $this->localtax7 = trim($this->localtax7);
		}
		if (isset($this->localtax8)) {
			 $this->localtax8 = trim($this->localtax8);
		}
		if (isset($this->localtax9)) {
			 $this->localtax9 = trim($this->localtax9);
		}

		

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';
		
		$sql .= ' fk_facture_fourn = '.(isset($this->fk_facture_fourn)?$this->fk_facture_fourn:"null").',';
		$sql .= ' code_facture = '.(isset($this->code_facture)?"'".$this->db->escape($this->code_facture)."'":"null").',';
		$sql .= ' code_type_purchase = '.(isset($this->code_type_purchase)?"'".$this->db->escape($this->code_type_purchase)."'":"null").',';
		$sql .= ' nit_company = '.(isset($this->nit_company)?"'".$this->db->escape($this->nit_company)."'":"null").',';
		$sql .= ' nfiscal = '.(isset($this->nfiscal)?$this->nfiscal:"null").',';
		$sql .= ' ndui = '.(isset($this->ndui)?"'".$this->db->escape($this->ndui)."'":"null").',';
		$sql .= ' num_autoriz = '.(isset($this->num_autoriz)?"'".$this->db->escape($this->num_autoriz)."'":"null").',';
		$sql .= ' nit = '.(isset($this->nit)?"'".$this->db->escape($this->nit)."'":"null").',';
		$sql .= ' razsoc = '.(isset($this->razsoc)?"'".$this->db->escape($this->razsoc)."'":"null").',';
		$sql .= ' cod_control = '.(isset($this->cod_control)?"'".$this->db->escape($this->cod_control)."'":"null").',';
		$sql .= ' codqr = '.(isset($this->codqr)?"'".$this->db->escape($this->codqr)."'":"null").',';
		$sql .= ' amountfiscal = '.(isset($this->amountfiscal)?$this->amountfiscal:"null").',';
		$sql .= ' amountnofiscal = '.(isset($this->amountnofiscal)?$this->amountnofiscal:"null").',';
		$sql .= ' amount_ice = '.(isset($this->amount_ice)?$this->amount_ice:"null").',';
		$sql .= ' discount = '.(isset($this->discount)?$this->discount:"null").',';
		$sql .= ' datec = '.(! isset($this->datec) || dol_strlen($this->datec) != 0 ? "'".$this->db->idate($this->datec)."'" : 'null').',';
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
		$sql .= ' localtax3 = '.(isset($this->localtax3)?$this->localtax3:"null").',';
		$sql .= ' localtax4 = '.(isset($this->localtax4)?$this->localtax4:"null").',';
		$sql .= ' localtax5 = '.(isset($this->localtax5)?$this->localtax5:"null").',';
		$sql .= ' localtax6 = '.(isset($this->localtax6)?$this->localtax6:"null").',';
		$sql .= ' localtax7 = '.(isset($this->localtax7)?$this->localtax7:"null").',';
		$sql .= ' localtax8 = '.(isset($this->localtax8)?$this->localtax8:"null").',';
		$sql .= ' localtax9 = '.(isset($this->localtax9)?$this->localtax9:"null");

        
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
		$object = new Facturefournadd($this->db);

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

        $link = '<a href="'.DOL_URL_ROOT.'/purchase/card.php?id='.$this->id.'"';
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
		
		$this->fk_facture_fourn = '';
		$this->code_facture = '';
		$this->code_type_purchase = '';
		$this->nit_company = '';
		$this->nfiscal = '';
		$this->ndui = '';
		$this->num_autoriz = '';
		$this->nit = '';
		$this->razsoc = '';
		$this->cod_control = '';
		$this->codqr = '';
		$this->amountfiscal = '';
		$this->amountnofiscal = '';
		$this->amount_ice = '';
		$this->discount = '';
		$this->datec = '';
		$this->tms = '';
		$this->localtax3 = '';
		$this->localtax4 = '';
		$this->localtax5 = '';
		$this->localtax6 = '';
		$this->localtax7 = '';
		$this->localtax8 = '';
		$this->localtax9 = '';

		
	}

}

/**
 * Class FacturefournaddLine
 */
class FacturefournaddLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	
	public $fk_facture_fourn;
	public $code_facture;
	public $code_type_purchase;
	public $nit_company;
	public $nfiscal;
	public $ndui;
	public $num_autoriz;
	public $nit;
	public $razsoc;
	public $cod_control;
	public $codqr;
	public $amountfiscal;
	public $amountnofiscal;
	public $amount_ice;
	public $discount;
	public $datec = '';
	public $tms = '';
	public $localtax3;
	public $localtax4;
	public $localtax5;
	public $localtax6;
	public $localtax7;
	public $localtax8;
	public $localtax9;

	/**
	 * @var mixed Sample line property 2
	 */
	
}
