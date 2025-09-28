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
 * \file    contab/contabvision.class.php
 * \ingroup contab
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Contabvision
 *
 * Put here description of your class
 * @see CommonObject
 */
class Contabvision extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'contabvision';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'contab_vision';

	/**
	 * @var ContabvisionLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	
	public $entity;
	public $ref;
	public $sequence;
	public $account;
	public $account_sup;
	public $detail_managment;
	public $cta_normal;
	public $cta_column;
	public $cta_class;
	public $cta_identifier;
	public $cta_operation;
	public $cta_balances;
	public $cta_totalvis;
	public $name_vision;
	public $line;
	public $fk_accountini;
	public $fk_accountfin;
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
		if (isset($this->sequence)) {
			 $this->sequence = trim($this->sequence);
		}
		if (isset($this->account)) {
			 $this->account = trim($this->account);
		}
		if (isset($this->account_sup)) {
			 $this->account_sup = trim($this->account_sup);
		}
		if (isset($this->detail_managment)) {
			 $this->detail_managment = trim($this->detail_managment);
		}
		if (isset($this->cta_normal)) {
			 $this->cta_normal = trim($this->cta_normal);
		}
		if (isset($this->cta_column)) {
			 $this->cta_column = trim($this->cta_column);
		}
		if (isset($this->cta_class)) {
			 $this->cta_class = trim($this->cta_class);
		}
		if (isset($this->cta_identifier)) {
			 $this->cta_identifier = trim($this->cta_identifier);
		}
		if (isset($this->cta_operation)) {
			 $this->cta_operation = trim($this->cta_operation);
		}
		if (isset($this->cta_balances)) {
			 $this->cta_balances = trim($this->cta_balances);
		}
		if (isset($this->cta_totalvis)) {
			 $this->cta_totalvis = trim($this->cta_totalvis);
		}
		if (isset($this->name_vision)) {
			 $this->name_vision = trim($this->name_vision);
		}
		if (isset($this->line)) {
			 $this->line = trim($this->line);
		}
		if (isset($this->fk_accountini)) {
			 $this->fk_accountini = trim($this->fk_accountini);
		}
		if (isset($this->fk_accountfin)) {
			 $this->fk_accountfin = trim($this->fk_accountfin);
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
		$sql.= 'sequence,';
		$sql.= 'account,';
		$sql.= 'account_sup,';
		$sql.= 'detail_managment,';
		$sql.= 'cta_normal,';
		$sql.= 'cta_column,';
		$sql.= 'cta_class,';
		$sql.= 'cta_identifier,';
		$sql.= 'cta_operation,';
		$sql.= 'cta_balances,';
		$sql.= 'cta_totalvis,';
		$sql.= 'name_vision,';
		$sql.= 'line,';
		$sql.= 'fk_accountini,';
		$sql.= 'fk_accountfin';
		$sql.= 'statut';

		
		$sql .= ') VALUES (';
		
		$sql .= ' '.(! isset($this->entity)?'NULL':$this->entity).',';
		$sql .= ' '.(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql .= ' '.(! isset($this->sequence)?'NULL':$this->sequence).',';
		$sql .= ' '.(! isset($this->account)?'NULL':"'".$this->db->escape($this->account)."'").',';
		$sql .= ' '.(! isset($this->account_sup)?'NULL':"'".$this->db->escape($this->account_sup)."'").',';
		$sql .= ' '.(! isset($this->detail_managment)?'NULL':"'".$this->db->escape($this->detail_managment)."'").',';
		$sql .= ' '.(! isset($this->cta_normal)?'NULL':"'".$this->db->escape($this->cta_normal)."'").',';
		$sql .= ' '.(! isset($this->cta_column)?'NULL':$this->cta_column).',';
		$sql .= ' '.(! isset($this->cta_class)?'NULL':$this->cta_class).',';
		$sql .= ' '.(! isset($this->cta_identifier)?'NULL':"'".$this->db->escape($this->cta_identifier)."'").',';
		$sql .= ' '.(! isset($this->cta_operation)?'NULL':$this->cta_operation).',';
		$sql .= ' '.(! isset($this->cta_balances)?'NULL':$this->cta_balances).',';
		$sql .= ' '.(! isset($this->cta_totalvis)?'NULL':$this->cta_totalvis).',';
		$sql .= ' '.(! isset($this->name_vision)?'NULL':"'".$this->db->escape($this->name_vision)."'").',';
		$sql .= ' '.(! isset($this->line)?'NULL':"'".$this->db->escape($this->line)."'").',';
		$sql .= ' '.(! isset($this->fk_accountini)?'NULL':$this->fk_accountini).',';
		$sql .= ' '.(! isset($this->fk_accountfin)?'NULL':$this->fk_accountfin).',';
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
		$sql .= " t.sequence,";
		$sql .= " t.account,";
		$sql .= " t.account_sup,";
		$sql .= " t.detail_managment,";
		$sql .= " t.cta_normal,";
		$sql .= " t.cta_column,";
		$sql .= " t.cta_class,";
		$sql .= " t.cta_identifier,";
		$sql .= " t.cta_operation,";
		$sql .= " t.cta_balances,";
		$sql .= " t.cta_totalvis,";
		$sql .= " t.name_vision,";
		$sql .= " t.line,";
		$sql .= " t.fk_accountini,";
		$sql .= " t.fk_accountfin,";
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
				$this->sequence = $obj->sequence;
				$this->account = $obj->account;
				$this->account_sup = $obj->account_sup;
				$this->detail_managment = $obj->detail_managment;
				$this->cta_normal = $obj->cta_normal;
				$this->cta_column = $obj->cta_column;
				$this->cta_class = $obj->cta_class;
				$this->cta_identifier = $obj->cta_identifier;
				$this->cta_operation = $obj->cta_operation;
				$this->cta_balances = $obj->cta_balances;
				$this->cta_totalvis = $obj->cta_totalvis;
				$this->name_vision = $obj->name_vision;
				$this->line = $obj->line;
				$this->fk_accountini = $obj->fk_accountini;
				$this->fk_accountfin = $obj->fk_accountfin;
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
		$sql .= " t.ref,";
		$sql .= " t.sequence,";
		$sql .= " t.account,";
		$sql .= " t.account_sup,";
		$sql .= " t.detail_managment,";
		$sql .= " t.cta_normal,";
		$sql .= " t.cta_column,";
		$sql .= " t.cta_class,";
		$sql .= " t.cta_identifier,";
		$sql .= " t.cta_operation,";
		$sql .= " t.cta_balances,";
		$sql .= " t.cta_totalvis,";
		$sql .= " t.name_vision,";
		$sql .= " t.line,";
		$sql .= " t.fk_accountini,";
		$sql .= " t.fk_accountfin,";
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
				$line = new ContabvisionLine();

				$line->id = $obj->rowid;
				
				$line->entity = $obj->entity;
				$line->ref = $obj->ref;
				$line->sequence = $obj->sequence;
				$line->account = $obj->account;
				$line->account_sup = $obj->account_sup;
				$line->detail_managment = $obj->detail_managment;
				$line->cta_normal = $obj->cta_normal;
				$line->cta_column = $obj->cta_column;
				$line->cta_class = $obj->cta_class;
				$line->cta_identifier = $obj->cta_identifier;
				$line->cta_operation = $obj->cta_operation;
				$line->cta_balances = $obj->cta_balances;
				$line->cta_totalvis = $obj->cta_totalvis;
				$line->name_vision = $obj->name_vision;
				$line->line = $obj->line;
				$line->fk_accountini = $obj->fk_accountini;
				$line->fk_accountfin = $obj->fk_accountfin;
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
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->sequence)) {
			 $this->sequence = trim($this->sequence);
		}
		if (isset($this->account)) {
			 $this->account = trim($this->account);
		}
		if (isset($this->account_sup)) {
			 $this->account_sup = trim($this->account_sup);
		}
		if (isset($this->detail_managment)) {
			 $this->detail_managment = trim($this->detail_managment);
		}
		if (isset($this->cta_normal)) {
			 $this->cta_normal = trim($this->cta_normal);
		}
		if (isset($this->cta_column)) {
			 $this->cta_column = trim($this->cta_column);
		}
		if (isset($this->cta_class)) {
			 $this->cta_class = trim($this->cta_class);
		}
		if (isset($this->cta_identifier)) {
			 $this->cta_identifier = trim($this->cta_identifier);
		}
		if (isset($this->cta_operation)) {
			 $this->cta_operation = trim($this->cta_operation);
		}
		if (isset($this->cta_balances)) {
			 $this->cta_balances = trim($this->cta_balances);
		}
		if (isset($this->cta_totalvis)) {
			 $this->cta_totalvis = trim($this->cta_totalvis);
		}
		if (isset($this->name_vision)) {
			 $this->name_vision = trim($this->name_vision);
		}
		if (isset($this->line)) {
			 $this->line = trim($this->line);
		}
		if (isset($this->fk_accountini)) {
			 $this->fk_accountini = trim($this->fk_accountini);
		}
		if (isset($this->fk_accountfin)) {
			 $this->fk_accountfin = trim($this->fk_accountfin);
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
		$sql .= ' sequence = '.(isset($this->sequence)?$this->sequence:"null").',';
		$sql .= ' account = '.(isset($this->account)?"'".$this->db->escape($this->account)."'":"null").',';
		$sql .= ' account_sup = '.(isset($this->account_sup)?"'".$this->db->escape($this->account_sup)."'":"null").',';
		$sql .= ' detail_managment = '.(isset($this->detail_managment)?"'".$this->db->escape($this->detail_managment)."'":"null").',';
		$sql .= ' cta_normal = '.(isset($this->cta_normal)?"'".$this->db->escape($this->cta_normal)."'":"null").',';
		$sql .= ' cta_column = '.(isset($this->cta_column)?$this->cta_column:"null").',';
		$sql .= ' cta_class = '.(isset($this->cta_class)?$this->cta_class:"null").',';
		$sql .= ' cta_identifier = '.(isset($this->cta_identifier)?"'".$this->db->escape($this->cta_identifier)."'":"null").',';
		$sql .= ' cta_operation = '.(isset($this->cta_operation)?$this->cta_operation:"null").',';
		$sql .= ' cta_balances = '.(isset($this->cta_balances)?$this->cta_balances:"null").',';
		$sql .= ' cta_totalvis = '.(isset($this->cta_totalvis)?$this->cta_totalvis:"null").',';
		$sql .= ' name_vision = '.(isset($this->name_vision)?"'".$this->db->escape($this->name_vision)."'":"null").',';
		$sql .= ' line = '.(isset($this->line)?"'".$this->db->escape($this->line)."'":"null").',';
		$sql .= ' fk_accountini = '.(isset($this->fk_accountini)?$this->fk_accountini:"null").',';
		$sql .= ' fk_accountfin = '.(isset($this->fk_accountfin)?$this->fk_accountfin:"null").',';
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
		$object = new Contabvision($this->db);

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

        $link = '<a href="'.DOL_URL_ROOT.'/contab/card.php?id='.$this->id.'"';
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
		$this->ref = '';
		$this->sequence = '';
		$this->account = '';
		$this->account_sup = '';
		$this->detail_managment = '';
		$this->cta_normal = '';
		$this->cta_column = '';
		$this->cta_class = '';
		$this->cta_identifier = '';
		$this->cta_operation = '';
		$this->cta_balances = '';
		$this->cta_totalvis = '';
		$this->name_vision = '';
		$this->line = '';
		$this->fk_accountini = '';
		$this->fk_accountfin = '';
		$this->statut = '';

		
	}

}

/**
 * Class ContabvisionLine
 */
class ContabvisionLine
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
	public $sequence;
	public $account;
	public $account_sup;
	public $detail_managment;
	public $cta_normal;
	public $cta_column;
	public $cta_class;
	public $cta_identifier;
	public $cta_operation;
	public $cta_balances;
	public $cta_totalvis;
	public $name_vision;
	public $line;
	public $fk_accountini;
	public $fk_accountfin;
	public $statut;

	/**
	 * @var mixed Sample line property 2
	 */
	
}
