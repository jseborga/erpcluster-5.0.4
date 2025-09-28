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
 * \file    poa/poapartidadev.class.php
 * \ingroup poa
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Poapartidadev
 *
 * Put here description of your class
 * @see CommonObject
 */
class Poapartidadev extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'poapartidadev';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'poa_partida_dev';

	/**
	 * @var PoapartidadevLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	
	public $fk_poa_partida_com;
	public $gestion;
	public $fk_poa_prev;
	public $fk_structure;
	public $fk_poa;
	public $fk_contrat;
	public $fk_contrato;
	public $type_pay;
	public $nro_dev;
	public $date_dev = '';
	public $partida;
	public $invoice;
	public $amount;
	public $date_create = '';
	public $tms = '';
	public $statut;
	public $active;
	public $total;
	public $aTotal;
	public $array;
	public $maximo;

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
		return 1;
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
		
		if (isset($this->fk_poa_partida_com)) {
			 $this->fk_poa_partida_com = trim($this->fk_poa_partida_com);
		}
		if (isset($this->gestion)) {
			 $this->gestion = trim($this->gestion);
		}
		if (isset($this->fk_poa_prev)) {
			 $this->fk_poa_prev = trim($this->fk_poa_prev);
		}
		if (isset($this->fk_structure)) {
			 $this->fk_structure = trim($this->fk_structure);
		}
		if (isset($this->fk_poa)) {
			 $this->fk_poa = trim($this->fk_poa);
		}
		if (isset($this->fk_contrat)) {
			 $this->fk_contrat = trim($this->fk_contrat);
		}
		if (isset($this->fk_contrato)) {
			 $this->fk_contrato = trim($this->fk_contrato);
		}
		if (isset($this->type_pay)) {
			 $this->type_pay = trim($this->type_pay);
		}
		if (isset($this->nro_dev)) {
			 $this->nro_dev = trim($this->nro_dev);
		}
		if (isset($this->partida)) {
			 $this->partida = trim($this->partida);
		}
		if (isset($this->invoice)) {
			 $this->invoice = trim($this->invoice);
		}
		if (isset($this->amount)) {
			 $this->amount = trim($this->amount);
		}
		if (isset($this->statut)) {
			 $this->statut = trim($this->statut);
		}
		if (isset($this->active)) {
			 $this->active = trim($this->active);
		}

		

		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';
		
		$sql.= 'fk_poa_partida_com,';
		$sql.= 'gestion,';
		$sql.= 'fk_poa_prev,';
		$sql.= 'fk_structure,';
		$sql.= 'fk_poa,';
		$sql.= 'fk_contrat,';
		$sql.= 'fk_contrato,';
		$sql.= 'type_pay,';
		$sql.= 'nro_dev,';
		$sql.= 'date_dev,';
		$sql.= 'partida,';
		$sql.= 'invoice,';
		$sql.= 'amount,';
		$sql.= 'date_create,';
		$sql.= 'statut';
		$sql.= 'active';

		
		$sql .= ') VALUES (';
		
		$sql .= ' '.(! isset($this->fk_poa_partida_com)?'NULL':$this->fk_poa_partida_com).',';
		$sql .= ' '.(! isset($this->gestion)?'NULL':$this->gestion).',';
		$sql .= ' '.(! isset($this->fk_poa_prev)?'NULL':$this->fk_poa_prev).',';
		$sql .= ' '.(! isset($this->fk_structure)?'NULL':$this->fk_structure).',';
		$sql .= ' '.(! isset($this->fk_poa)?'NULL':$this->fk_poa).',';
		$sql .= ' '.(! isset($this->fk_contrat)?'NULL':$this->fk_contrat).',';
		$sql .= ' '.(! isset($this->fk_contrato)?'NULL':$this->fk_contrato).',';
		$sql .= ' '.(! isset($this->type_pay)?'NULL':$this->type_pay).',';
		$sql .= ' '.(! isset($this->nro_dev)?'NULL':$this->nro_dev).',';
		$sql .= ' '.(! isset($this->date_dev) || dol_strlen($this->date_dev)==0?'NULL':"'".$this->db->idate($this->date_dev)."'").',';
		$sql .= ' '.(! isset($this->partida)?'NULL':"'".$this->db->escape($this->partida)."'").',';
		$sql .= ' '.(! isset($this->invoice)?'NULL':"'".$this->db->escape($this->invoice)."'").',';
		$sql .= ' '.(! isset($this->amount)?'NULL':"'".$this->amount."'").',';
		$sql .= ' '.(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':"'".$this->db->idate($this->date_create)."'").',';
		$sql .= ' '.(! isset($this->statut)?'NULL':$this->statut).',';
		$sql .= ' '.(! isset($this->active)?'NULL':$this->active);

		
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
		
		$sql .= " t.fk_poa_partida_com,";
		$sql .= " t.gestion,";
		$sql .= " t.fk_poa_prev,";
		$sql .= " t.fk_structure,";
		$sql .= " t.fk_poa,";
		$sql .= " t.fk_contrat,";
		$sql .= " t.fk_contrato,";
		$sql .= " t.type_pay,";
		$sql .= " t.nro_dev,";
		$sql .= " t.date_dev,";
		$sql .= " t.partida,";
		$sql .= " t.invoice,";
		$sql .= " t.amount,";
		$sql .= " t.date_create,";
		$sql .= " t.tms,";
		$sql .= " t.statut,";
		$sql .= " t.active";

		
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
				
				$this->fk_poa_partida_com = $obj->fk_poa_partida_com;
				$this->gestion = $obj->gestion;
				$this->fk_poa_prev = $obj->fk_poa_prev;
				$this->fk_structure = $obj->fk_structure;
				$this->fk_poa = $obj->fk_poa;
				$this->fk_contrat = $obj->fk_contrat;
				$this->fk_contrato = $obj->fk_contrato;
				$this->type_pay = $obj->type_pay;
				$this->nro_dev = $obj->nro_dev;
				$this->date_dev = $this->db->jdate($obj->date_dev);
				$this->partida = $obj->partida;
				$this->invoice = $obj->invoice;
				$this->amount = $obj->amount;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->tms = $this->db->jdate($obj->tms);
				$this->statut = $obj->statut;
				$this->active = $obj->active;

				
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
		
		$sql .= " t.fk_poa_partida_com,";
		$sql .= " t.gestion,";
		$sql .= " t.fk_poa_prev,";
		$sql .= " t.fk_structure,";
		$sql .= " t.fk_poa,";
		$sql .= " t.fk_contrat,";
		$sql .= " t.fk_contrato,";
		$sql .= " t.type_pay,";
		$sql .= " t.nro_dev,";
		$sql .= " t.date_dev,";
		$sql .= " t.partida,";
		$sql .= " t.invoice,";
		$sql .= " t.amount,";
		$sql .= " t.date_create,";
		$sql .= " t.tms,";
		$sql .= " t.statut,";
		$sql .= " t.active";

		
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
				$line = new PoapartidadevLine();

				$line->id = $obj->rowid;
				
				$line->fk_poa_partida_com = $obj->fk_poa_partida_com;
				$line->gestion = $obj->gestion;
				$line->fk_poa_prev = $obj->fk_poa_prev;
				$line->fk_structure = $obj->fk_structure;
				$line->fk_poa = $obj->fk_poa;
				$line->fk_contrat = $obj->fk_contrat;
				$line->fk_contrato = $obj->fk_contrato;
				$line->type_pay = $obj->type_pay;
				$line->nro_dev = $obj->nro_dev;
				$line->date_dev = $this->db->jdate($obj->date_dev);
				$line->partida = $obj->partida;
				$line->invoice = $obj->invoice;
				$line->amount = $obj->amount;
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->tms = $this->db->jdate($obj->tms);
				$line->statut = $obj->statut;
				$line->active = $obj->active;

				

				$this->lines[] = $line;
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
		
		if (isset($this->fk_poa_partida_com)) {
			 $this->fk_poa_partida_com = trim($this->fk_poa_partida_com);
		}
		if (isset($this->gestion)) {
			 $this->gestion = trim($this->gestion);
		}
		if (isset($this->fk_poa_prev)) {
			 $this->fk_poa_prev = trim($this->fk_poa_prev);
		}
		if (isset($this->fk_structure)) {
			 $this->fk_structure = trim($this->fk_structure);
		}
		if (isset($this->fk_poa)) {
			 $this->fk_poa = trim($this->fk_poa);
		}
		if (isset($this->fk_contrat)) {
			 $this->fk_contrat = trim($this->fk_contrat);
		}
		if (isset($this->fk_contrato)) {
			 $this->fk_contrato = trim($this->fk_contrato);
		}
		if (isset($this->type_pay)) {
			 $this->type_pay = trim($this->type_pay);
		}
		if (isset($this->nro_dev)) {
			 $this->nro_dev = trim($this->nro_dev);
		}
		if (isset($this->partida)) {
			 $this->partida = trim($this->partida);
		}
		if (isset($this->invoice)) {
			 $this->invoice = trim($this->invoice);
		}
		if (isset($this->amount)) {
			 $this->amount = trim($this->amount);
		}
		if (isset($this->statut)) {
			 $this->statut = trim($this->statut);
		}
		if (isset($this->active)) {
			 $this->active = trim($this->active);
		}

		

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';
		
		$sql .= ' fk_poa_partida_com = '.(isset($this->fk_poa_partida_com)?$this->fk_poa_partida_com:"null").',';
		$sql .= ' gestion = '.(isset($this->gestion)?$this->gestion:"null").',';
		$sql .= ' fk_poa_prev = '.(isset($this->fk_poa_prev)?$this->fk_poa_prev:"null").',';
		$sql .= ' fk_structure = '.(isset($this->fk_structure)?$this->fk_structure:"null").',';
		$sql .= ' fk_poa = '.(isset($this->fk_poa)?$this->fk_poa:"null").',';
		$sql .= ' fk_contrat = '.(isset($this->fk_contrat)?$this->fk_contrat:"null").',';
		$sql .= ' fk_contrato = '.(isset($this->fk_contrato)?$this->fk_contrato:"null").',';
		$sql .= ' type_pay = '.(isset($this->type_pay)?$this->type_pay:"null").',';
		$sql .= ' nro_dev = '.(isset($this->nro_dev)?$this->nro_dev:"null").',';
		$sql .= ' date_dev = '.(! isset($this->date_dev) || dol_strlen($this->date_dev) != 0 ? "'".$this->db->idate($this->date_dev)."'" : 'null').',';
		$sql .= ' partida = '.(isset($this->partida)?"'".$this->db->escape($this->partida)."'":"null").',';
		$sql .= ' invoice = '.(isset($this->invoice)?"'".$this->db->escape($this->invoice)."'":"null").',';
		$sql .= ' amount = '.(isset($this->amount)?$this->amount:"null").',';
		$sql .= ' date_create = '.(! isset($this->date_create) || dol_strlen($this->date_create) != 0 ? "'".$this->db->idate($this->date_create)."'" : 'null').',';
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
		$sql .= ' statut = '.(isset($this->statut)?$this->statut:"null").',';
		$sql .= ' active = '.(isset($this->active)?$this->active:"null");

        
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
		$object = new Poapartidadev($this->db);

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

        $link = '<a href="'.DOL_URL_ROOT.'/poa/card.php?id='.$this->id.'"';
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
		
		$this->fk_poa_partida_com = '';
		$this->gestion = '';
		$this->fk_poa_prev = '';
		$this->fk_structure = '';
		$this->fk_poa = '';
		$this->fk_contrat = '';
		$this->fk_contrato = '';
		$this->type_pay = '';
		$this->nro_dev = '';
		$this->date_dev = '';
		$this->partida = '';
		$this->invoice = '';
		$this->amount = '';
		$this->date_create = '';
		$this->tms = '';
		$this->statut = '';
		$this->active = '';

		
	}

	//modificado
	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$fk_poa_prev    Id object
	 *  @param	int		$fk_contrat    Id object

	 *  @return int          	<0 if KO, >0 if OK
	 */
	function get_sum_pcp($fk_poa_prev,$fk_contrat)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";
		$sql.= " t.fk_poa_partida_com,";
		$sql.= " t.fk_structure,";
		$sql.= " t.fk_poa,";
		$sql.= " t.type_pay,";
		$sql.= " t.partida,";
		$sql.= " t.amount ";


		$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_dev as t";
		$sql.= " WHERE t.fk_poa_prev = ".$fk_poa_prev;
		$sql.= " AND t.fk_contrat = ".$fk_contrat;
		$sql.= " AND t.statut = 1";
		$sql.= " ORDER BY t.date_dev";
		dol_syslog(get_class($this)."::get_sum_pcp sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->total = 0;
		$this->array = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objnew = new Poapartidadev($this->db);
					$objnew->rowid = $obj->rowid;
					$objnew->fk_poa_partida_com = $obj->fk_poa_partida_com;
					$objnew->fk_structure = $obj->fk_structure;
					$objnew->fk_poa = $obj->fk_poa;
					$objnew->type_pay = $obj->type_pay;
					$objnew->partida = $obj->partida;
					$objnew->amount = $obj->amount;
					$this->array[$obj->rowid] = $objnew;
					$this->total += $obj->amount;
					$i++;
				}
			}
			$this->db->free($resql);

			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::get_sum_pcp ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$fk_poa_prev    Id object
	 *  @param	int		$fk_contrat    Id object
	 *  @param  int             $invoice       false=ninguno; true= con factura
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function get_sum_pcp2($fk_poa_prev,$fk_contrat,$invoice=0)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.fk_poa_partida_com,";
		$sql.= " t.gestion,";
		$sql.= " t.fk_poa_prev,";
		$sql.= " t.fk_structure,";
		$sql.= " t.fk_poa,";
		$sql.= " t.fk_contrat,";
		$sql.= " t.fk_contrato,";
		$sql.= " t.type_pay,";
		$sql.= " t.nro_dev,";
		$sql.= " t.date_dev,";
		$sql.= " t.partida,";
		$sql.= " t.invoice,";
		$sql.= " t.amount,";
		$sql.= " t.date_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut,";
		$sql.= " t.active";


		$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_dev as t";
	// $sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_prev AS p ON t.fk_poa_prev = p.rowid";
		$sql.= " WHERE t.fk_poa_prev = ".$fk_poa_prev;
		$sql.= " AND t.fk_contrato = ".$fk_contrat;
		$sql.= " AND t.statut = 1";
		if ($invoice)
			$sql.= " AND (t.invoice != '' AND t.invoice != '0')";
	//echo '<br>dev '.$sql;
		dol_syslog(get_class($this)."::get_sum_pcp2 sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->total = 0;
		$this->array = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objnew = new Poapartidadev($this->db);

					$objnew->id    = $obj->rowid;
					$objnew->fk_poa_partida_com = $obj->fk_poa_partida_com;
					$objnew->gestion = $obj->gestion;
					$objnew->fk_poa_prev = $obj->fk_poa_prev;
					$objnew->fk_structure = $obj->fk_structure;
					$objnew->fk_poa = $obj->fk_poa;
					$objnew->fk_contrat = $obj->fk_contrat;
					$objnew->fk_contrato = $obj->fk_contrato;
					$objnew->type_pay = $obj->type_pay;
					$objnew->nro_dev = $obj->nro_dev;
					$objnew->date_dev = $this->db->jdate($obj->date_dev);
					$objnew->partida = $obj->partida;
					$objnew->invoice = $obj->invoice;
					$objnew->amount = $obj->amount;
					$objnew->date_create = $this->db->jdate($obj->date_create);
					$objnew->tms = $this->db->jdate($obj->tms);
					$objnew->statut = $obj->statut;
					$objnew->active = $obj->active;

					$this->array[$obj->rowid] = $objnew;
					$this->aTotal[$obj->gestion] += $obj->amount;
					$this->total += $obj->amount;
					$i++;
				}
			}
			$this->db->free($resql);

			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::get_sum_pcp2 ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$fk_poa_prev    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getlist($fk_poa_prev,$idc=0)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.gestion,";
		$sql.= " t.fk_poa_prev,";
		$sql.= " t.fk_structure,";
		$sql.= " t.fk_poa,";
		$sql.= " t.fk_contrat,";
		$sql.= " t.fk_contrato,";
		$sql.= " t.type_pay,";
		$sql.= " t.nro_dev,";
		$sql.= " t.date_dev,";
		$sql.= " t.partida,";
		$sql.= " t.invoice,";
		$sql.= " t.amount,";
		$sql.= " t.date_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut,";
		$sql.= " t.active";


		$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_dev as t";
		$sql.= " WHERE t.fk_poa_prev = ".$fk_poa_prev;
		if ($idc)
			$sql.= " AND t.fk_contrat = ".$idc;
		$sql.= " AND t.statut = 1";
		dol_syslog(get_class($this)."::getlist sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->array = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i = 0;
				$this->array = array();
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objnew = new Poapartidadev($this->db);
					$objnew->id    = $obj->rowid;

					$objnew->gestion = $obj->gestion;
					$objnew->fk_poa_prev = $obj->fk_poa_prev;
					$objnew->fk_structure = $obj->fk_structure;
					$objnew->fk_poa = $obj->fk_poa;
					$objnew->fk_contrat = $obj->fk_contrat;
					$objnew->fk_contrato = $obj->fk_contrato;
					$objnew->type_pay = $obj->type_pay;
					$objnew->nro_dev = $obj->nro_dev;
					$objnew->date_dev = $this->db->jdate($obj->date_dev);
					$objnew->partida = $obj->partida;
					$objnew->invoice = $obj->invoice;
					$objnew->amount = $obj->amount;
					$objnew->date_create = $this->db->jdate($obj->date_create);
					$objnew->tms = $this->db->jdate($obj->tms);
					$objnew->statut = $obj->statut;
					$objnew->active = $obj->active;
					$this->array[$obj->rowid] = $objnew;
					$i++;

				}

			}
			$this->db->free($resql);

			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getlist ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$fk_poa_prev    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getlist2($fk_poa_prev,$idc=0)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.gestion,";
		$sql.= " t.fk_poa_prev,";
		$sql.= " t.fk_structure,";
		$sql.= " t.fk_poa,";
		$sql.= " t.fk_contrat,";
		$sql.= " t.fk_contrato,";
		$sql.= " t.type_pay,";
		$sql.= " t.nro_dev,";
		$sql.= " t.date_dev,";
		$sql.= " t.partida,";
		$sql.= " t.invoice,";
		$sql.= " t.amount,";
		$sql.= " t.date_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut,";
		$sql.= " t.active";


		$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_dev as t";
		$sql.= " WHERE t.fk_poa_prev = ".$fk_poa_prev;
		if ($idc)
			$sql.= " AND t.fk_contrato = ".$idc;
		$sql.= " AND t.statut = 1";
		dol_syslog(get_class($this)."::getlist2 sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->array = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objnew = new Poapartidadev($this->db);
					$objnew->id    = $obj->rowid;

					$objnew->gestion = $obj->gestion;
					$objnew->fk_poa_prev = $obj->fk_poa_prev;
					$objnew->fk_structure = $obj->fk_structure;
					$objnew->fk_poa = $obj->fk_poa;
					$objnew->fk_contrat = $obj->fk_contrat;
					$objnew->fk_contrato = $obj->fk_contrato;
					$objnew->type_pay = $obj->type_pay;
					$objnew->nro_dev = $obj->nro_dev;
					$objnew->date_dev = $this->db->jdate($obj->date_dev);
					$objnew->partida = $obj->partida;
					$objnew->invoice = $obj->invoice;
					$objnew->amount = $obj->amount;
					$objnew->date_create = $this->db->jdate($obj->date_create);
					$objnew->tms = $this->db->jdate($obj->tms);
					$objnew->statut = $obj->statut;
					$objnew->active = $obj->active;
					$this->array[$obj->rowid] = $objnew;
					$i++;

				}

			}
			$this->db->free($resql);

			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getlist2 ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/*
	* lista los pagos segun fk_preventivo, numero y gestion
	*/
	/**
	 *  Load object in memory from the database
	 *
	 *  @param  int     $fk_poa_prev    Id object
	 *  @return int             <0 if KO, >0 if OK
	 */
	function get_list_nrodev($id,$nro_dev,$gestion=0)
	{
		global $langs;
		if (empty($id) || empty($nro_dev))
			return -1;
		$sql = "SELECT";
		$sql.= " t.rowid,";
		$sql.= " t.gestion,";
		$sql.= " t.fk_poa_prev,";
		$sql.= " t.fk_structure,";
		$sql.= " t.fk_poa,";
		$sql.= " t.fk_contrat,";
		$sql.= " t.fk_contrato,";
		$sql.= " t.type_pay,";
		$sql.= " t.nro_dev,";
		$sql.= " t.date_dev,";
		$sql.= " t.partida,";
		$sql.= " t.invoice,";
		$sql.= " t.amount,";
		$sql.= " t.date_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut,";
		$sql.= " t.active";

		$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_dev as t";
		$sql.= " WHERE t.fk_poa_prev = ".$id;
		$sql.= " AND t.nro_dev = ".$nro_dev;
		if ($gestion)
			$sql.= " AND t.gestion = ".$gestion;
		dol_syslog(get_class($this)."::get_list_nrodev sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->array = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i = 0;
				$this->array = array();
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objnew = new Poapartidadev($this->db);
					$objnew->id    = $obj->rowid;

					$objnew->gestion = $obj->gestion;
					$objnew->fk_poa_prev = $obj->fk_poa_prev;
					$objnew->fk_structure = $obj->fk_structure;
					$objnew->fk_poa = $obj->fk_poa;
					$objnew->fk_contrat = $obj->fk_contrat;
					$objnew->fk_contrato = $obj->fk_contrato;
					$objnew->type_pay = $obj->type_pay;
					$objnew->nro_dev = $obj->nro_dev;
					$objnew->date_dev = $this->db->jdate($obj->date_dev);
					$objnew->partida = $obj->partida;
					$objnew->invoice = $obj->invoice;
					$objnew->amount = $obj->amount;
					$objnew->date_create = $this->db->jdate($obj->date_create);
					$objnew->tms = $this->db->jdate($obj->tms);
					$objnew->statut = $obj->statut;
					$objnew->active = $obj->active;
					$this->array[$obj->rowid] = $objnew;
					$i++;
				}
			}
			$this->db->free($resql);
			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getlist2 ".$this->error, LOG_ERR);
			return -1;
		}
	}


	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getsum_str_part($gestion,$fk_structure='',$fk_poa,$partida,$fk_user=0,$viewtype=0)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " r.gestion, t.fk_structure, t.partida, ";
		if ($viewtype)
			$sql.= " t.type_pay, ";
		$sql.= " SUM(t.amount) AS total ";

		$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_dev as t";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_prev AS r ON t.fk_poa_prev = r.rowid ";
		$sql.= " WHERE r.gestion = ".$gestion;
		if ($fk_poa)
			$sql.= " AND t.fk_poa = ".$fk_poa;
		if ($partida)
			$sql.= " AND t.partida = '".$partida."' ";
		if ($fk_user>0)
			$sql.= " AND r.fk_user_create = ".$fk_user;
		$sql.= " AND t.statut = 1";
		$sql.= " GROUP BY r.gestion, t.fk_structure, t.partida ";
		if ($viewtype)
			$sql.= " , t.type_pay ";
		dol_syslog(get_class($this)."::getsum_str_part sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->total = 0;
		$this->aTotal = array();
		if ($resql)
		{
			$i = 0;
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$this->total += $obj->total;
					if ($viewtype)
						$this->aTotal[$obj->type_pay] += $obj->total;
					$i++;
				}
				$this->db->free($resql);
				return 1;
			}
			$this->db->free($resql);
			return 0;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getsum_str_part ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *	Realiza la suma de pagos segun el usuario de la actividad
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getsum_str_actpart($gestion,$fk_structure='',$fk_poa,$partida,$fk_user=0,$viewtype=0)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " r.gestion, t.fk_structure, t.partida, ";
		if ($viewtype)
			$sql.= " t.type_pay, ";
		$sql.= " SUM(t.amount) AS total ";

		$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_dev as t";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_prev AS r ON t.fk_poa_prev = r.rowid ";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_activity AS a ON t.fk_poa_prev = a.fk_prev ";
		$sql.= " WHERE r.gestion = ".$gestion;
		if ($fk_poa)
			$sql.= " AND t.fk_poa = ".$fk_poa;
		if ($partida)
			$sql.= " AND t.partida = '".$partida."' ";
		if ($fk_user>0)
			$sql.= " AND r.fk_user_create = ".$fk_user;
		$sql.= " AND t.statut = 1";
		$sql.= " GROUP BY r.gestion, t.fk_structure, t.partida ";
		if ($viewtype)
			$sql.= " , t.type_pay ";
		dol_syslog(get_class($this)."::getsum_str_part sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->total = 0;
		$this->aTotal = array();
		if ($resql)
		{
			$i = 0;
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$this->total += $obj->total;
					if ($viewtype)
						$this->aTotal[$obj->type_pay] += $obj->total;
					$i++;
				}
				$this->db->free($resql);
				return 1;
			}
			$this->db->free($resql);
			return 0;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getsum_str_part ".$this->error, LOG_ERR);
			return -1;
		}
	}
	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getsum_prev_str_part($fk_prev,$fk_structure,$fk_poa,$partida,$fkuser=0,$viewtype=0)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.fk_structure, t.partida,";
		if ($viewtype)
			$sql.= " t.type_pay,";
		$sql.= " SUM(t.amount) AS total ";

		$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_dev as t";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_prev AS r ON t.fk_poa_prev = r.rowid ";
		$sql.= " WHERE r.rowid = ".$fk_prev;
		$sql.= " AND t.fk_poa = ".$fk_poa;
		$sql.= " AND t.partida = '".$partida."' ";
		$sql.= " AND t.statut = 1";
		$sql.= " GROUP BY t.fk_structure, t.partida ";
		if ($viewtype)
			$sql.= " , t.type_pay ";
		dol_syslog(get_class($this)."::getsum_prev_str_part sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->total = 0;
		$this->aTotal = array();
		if ($resql)
		{
			$i = 0;
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$this->total += $obj->total;
					if ($viewtype)
						$this->aTotal[$obj->type_pay] += $obj->total;
					$i++;
				}
				$this->db->free($resql);
				return 1;
			}
			$this->db->free($resql);
			return 0;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getsum_prev_str_part ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function get_maxref($gestion,$fk_area)
	{
		global $langs,$conf;
		$sql = "SELECT";
		$sql.= " MAX(t.nro_dev) AS maximo";

		$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_dev as t";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_prev AS r ON t.fk_poa_prev = r.rowid ";
		$sql.= " WHERE t.gestion = ".$gestion;
		$sql.= " AND r.fk_area = ".$fk_area;
		$sql.= " AND r.entity = ".$conf->entity;
		dol_syslog(get_class($this)."::get_maxref sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		//echo $sql;
		$this->maximo = 1;
		if ($resql)
		{
			$this->total = 0;
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$obj = $this->db->fetch_object($resql);
				$this->maximo = $obj->maximo + 1;
			}
			$this->db->free($resql);
			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::get_maxref ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getlist_user($gestion,$fk_user=0,$fk_area=0,$userpoa=0)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.fk_poa_partida_com,";
		$sql.= " t.gestion,";
		$sql.= " t.fk_poa_prev,";
		$sql.= " t.fk_structure,";
		$sql.= " t.fk_poa,";
		$sql.= " t.fk_contrat,";
		$sql.= " t.fk_contrato,";
		$sql.= " t.type_pay,";
		$sql.= " t.nro_dev,";
		$sql.= " t.date_dev,";
		$sql.= " t.partida,";
		$sql.= " t.invoice,";
		$sql.= " t.amount,";
		$sql.= " t.date_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut,";
		$sql.= " t.active";

		$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_dev as t";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_prev AS p ON t.fk_poa_prev = p.rowid";
		if ($userpoa)
		{
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_poa AS po ON t.fk_poa = po.rowid";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_poa_user AS pu ON t.fk_poa = pu.fk_poa_poa";
		}
		$sql.= " WHERE p.gestion = ".$gestion;
		if ($userpoa)
		{
			if ($fk_user)
				$sql.= " AND pu.fk_user = ".$fk_user;
			if ($fk_area)
				$sql.= " AND po.fk_area = ".$fk_area;
			$sql.= " AND pu.active = 1";
			$sql.= " AND pu.statut = 1";
		}
		else
		{
			if ($fk_user)
				$sql.= " AND p.fk_user_create = ".$fk_user;
			if ($fk_area)
				$sql.= " AND p.fk_area = ".$fk_area;
		}
		$sql.= " AND p.statut > 0 ";
		$sql.= " AND t.statut > 0 ";

		dol_syslog(get_class($this)."::getlist_user sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->array = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($this->db->num_rows($resql))
			{
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objnew = new Poapartidadev($this->db);

					$objnew->id    = $obj->rowid;
					$objnew->fk_poa_partida_com = $obj->fk_poa_partida_com;
					$objnew->gestion = $obj->gestion;
					$objnew->fk_poa_prev = $obj->fk_poa_prev;
					$objnew->fk_structure = $obj->fk_structure;
					$objnew->fk_poa = $obj->fk_poa;
					$objnew->fk_contrat = $obj->fk_contrat;
					$objnew->fk_contrato = $obj->fk_contrato;
					$objnew->type_pay = $obj->type_pay;
					$objnew->nro_dev = $obj->nro_dev;
					$objnew->date_dev = $this->db->jdate($obj->date_dev);
					$objnew->partida = $obj->partida;
					$objnew->invoice = $obj->invoice;
					$objnew->amount = $obj->amount;
					$objnew->date_create = $this->db->jdate($obj->date_create);
					$objnew->tms = $this->db->jdate($obj->tms);
					$objnew->statut = $obj->statut;
					$objnew->active = $obj->active;

					$this->array[$obj->rowid] = $objnew;
					$i++;
				}
			}
			$this->db->free($resql);

			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getlist_user ".$this->error, LOG_ERR);
			return -1;
		}
	}

}

/**
 * Class PoapartidadevLine
 */
class PoapartidadevLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	
	public $fk_poa_partida_com;
	public $gestion;
	public $fk_poa_prev;
	public $fk_structure;
	public $fk_poa;
	public $fk_contrat;
	public $fk_contrato;
	public $type_pay;
	public $nro_dev;
	public $date_dev = '';
	public $partida;
	public $invoice;
	public $amount;
	public $date_create = '';
	public $tms = '';
	public $statut;
	public $active;

	/**
	 * @var mixed Sample line property 2
	 */
	
}
