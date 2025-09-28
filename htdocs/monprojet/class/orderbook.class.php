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
 * \file    monprojet/orderbook.class.php
 * \ingroup monprojet
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Orderbook
 *
 * Put here description of your class
 * @see CommonObject
 */
class Orderbook extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'orderbook';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'order_book';

	/**
	 * @var OrderbookLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	
	public $fk_parent;
	public $fk_projet;
	public $fk_contrat;
	public $ref;
	public $date_order = '';
	public $detail;
	public $document;
	public $fk_user_create;
	public $fk_user_validate;
	public $date_create = '';
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
		$this->statuts_short = array(0 => 'Draft', 1 => 'Validated', 2 => 'Approved',3=>'Payment');
		$this->statuts_long  = array(0 => 'Draft', 1 => 'Validated', 2 => 'Approved',3=>'Payment');
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
		
		if (isset($this->fk_parent)) {
			$this->fk_parent = trim($this->fk_parent);
		}
		if (isset($this->fk_projet)) {
			$this->fk_projet = trim($this->fk_projet);
		}
		if (isset($this->fk_contrat)) {
			$this->fk_contrat = trim($this->fk_contrat);
		}
		if (isset($this->ref)) {
			$this->ref = trim($this->ref);
		}
		if (isset($this->detail)) {
			$this->detail = trim($this->detail);
		}
		if (isset($this->document)) {
			$this->document = trim($this->document);
		}
		if (isset($this->fk_user_create)) {
			$this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_validate)) {
			$this->fk_user_validate = trim($this->fk_user_validate);
		}
		if (isset($this->statut)) {
			$this->statut = trim($this->statut);
		}

		

		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';
		
		$sql.= 'fk_parent,';
		$sql.= 'fk_projet,';
		$sql.= 'fk_contrat,';
		$sql.= 'ref,';
		$sql.= 'date_order,';
		$sql.= 'detail,';
		$sql.= 'document,';
		$sql.= 'fk_user_create,';
		$sql.= 'fk_user_validate,';
		$sql.= 'date_create,';
		$sql.= 'statut';

		
		$sql .= ') VALUES (';
		
		$sql .= ' '.(! isset($this->fk_parent)?'NULL':$this->fk_parent).',';
		$sql .= ' '.(! isset($this->fk_projet)?'NULL':$this->fk_projet).',';
		$sql .= ' '.(! isset($this->fk_contrat)?'NULL':$this->fk_contrat).',';
		$sql .= ' '.(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql .= ' '.(! isset($this->date_order) || dol_strlen($this->date_order)==0?'NULL':"'".$this->db->idate($this->date_order)."'").',';
		$sql .= ' '.(! isset($this->detail)?'NULL':"'".$this->db->escape($this->detail)."'").',';
		$sql .= ' '.(! isset($this->document)?'NULL':"'".$this->db->escape($this->document)."'").',';
		$sql .= ' '.(! isset($this->fk_user_create)?'NULL':$this->fk_user_create).',';
		$sql .= ' '.(! isset($this->fk_user_validate)?'NULL':$this->fk_user_validate).',';
		$sql .= ' '.(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':"'".$this->db->idate($this->date_create)."'").',';
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
		
		$sql .= " t.fk_parent,";
		$sql .= " t.fk_projet,";
		$sql .= " t.fk_contrat,";
		$sql .= " t.ref,";
		$sql .= " t.date_order,";
		$sql .= " t.detail,";
		$sql .= " t.document,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_validate,";
		$sql .= " t.date_create,";
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
				
				$this->fk_parent = $obj->fk_parent;
				$this->fk_projet = $obj->fk_projet;
				$this->fk_contrat = $obj->fk_contrat;
				$this->ref = $obj->ref;
				$this->date_order = $this->db->jdate($obj->date_order);
				$this->detail = $obj->detail;
				$this->document = $obj->document;
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_validate = $obj->fk_user_validate;
				$this->date_create = $this->db->jdate($obj->date_create);
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
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lView=false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		
		$sql .= " t.fk_parent,";
		$sql .= " t.fk_projet,";
		$sql .= " t.fk_contrat,";
		$sql .= " t.ref,";
		$sql .= " t.date_order,";
		$sql .= " t.detail,";
		$sql .= " t.document,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_validate,";
		$sql .= " t.date_create,";
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
				$line = new OrderbookLine();

				$line->id = $obj->rowid;
				
				$line->fk_parent = $obj->fk_parent;
				$line->fk_projet = $obj->fk_projet;
				$line->fk_contrat = $obj->fk_contrat;
				$line->ref = $obj->ref;
				$line->date_order = $this->db->jdate($obj->date_order);
				$line->detail = $obj->detail;
				$line->document = $obj->document;
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_user_validate = $obj->fk_user_validate;
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->tms = $this->db->jdate($obj->tms);
				$line->statut = $obj->statut;

				if ($lView)
				{
					$this->id = $obj->rowid;

					$this->fk_parent = $obj->fk_parent;
					$this->fk_projet = $obj->fk_projet;
					$this->fk_contrat = $obj->fk_contrat;
					$this->ref = $obj->ref;
					$this->date_order = $this->db->jdate($obj->date_order);
					$this->detail = $obj->detail;
					$this->document = $obj->document;
					$this->fk_user_create = $obj->fk_user_create;
					$this->fk_user_validate = $obj->fk_user_validate;
					$this->date_create = $this->db->jdate($obj->date_create);
					$this->tms = $this->db->jdate($obj->tms);
					$this->statut = $obj->statut;

				}

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
		
		if (isset($this->fk_parent)) {
			$this->fk_parent = trim($this->fk_parent);
		}
		if (isset($this->fk_projet)) {
			$this->fk_projet = trim($this->fk_projet);
		}
		if (isset($this->fk_contrat)) {
			$this->fk_contrat = trim($this->fk_contrat);
		}
		if (isset($this->ref)) {
			$this->ref = trim($this->ref);
		}
		if (isset($this->detail)) {
			$this->detail = trim($this->detail);
		}
		if (isset($this->document)) {
			$this->document = trim($this->document);
		}
		if (isset($this->fk_user_create)) {
			$this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_validate)) {
			$this->fk_user_validate = trim($this->fk_user_validate);
		}
		if (isset($this->statut)) {
			$this->statut = trim($this->statut);
		}

		

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';
		
		$sql .= ' fk_parent = '.(isset($this->fk_parent)?$this->fk_parent:"null").',';
		$sql .= ' fk_projet = '.(isset($this->fk_projet)?$this->fk_projet:"null").',';
		$sql .= ' fk_contrat = '.(isset($this->fk_contrat)?$this->fk_contrat:"null").',';
		$sql .= ' ref = '.(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").',';
		$sql .= ' date_order = '.(! isset($this->date_order) || dol_strlen($this->date_order) != 0 ? "'".$this->db->idate($this->date_order)."'" : 'null').',';
		$sql .= ' detail = '.(isset($this->detail)?"'".$this->db->escape($this->detail)."'":"null").',';
		$sql .= ' document = '.(isset($this->document)?"'".$this->db->escape($this->document)."'":"null").',';
		$sql .= ' fk_user_create = '.(isset($this->fk_user_create)?$this->fk_user_create:"null").',';
		$sql .= ' fk_user_validate = '.(isset($this->fk_user_validate)?$this->fk_user_validate:"null").',';
		$sql .= ' date_create = '.(! isset($this->date_create) || dol_strlen($this->date_create) != 0 ? "'".$this->db->idate($this->date_create)."'" : 'null').',';
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
		$object = new Orderbook($this->db);

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
	 *  Renvoi status label for a status
	 *
	 *  @param	int		$statut     id statut
	 *  @param  int		$mode       0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto
	 * 	@return string				Label
	 */
	function LibStatut($statut, $mode=0)
	{
	  global $langs;

	  if ($mode == 0)
	    {
	      return $langs->trans($this->statuts_long[$statut]);
	    }
	  if ($mode == 1)
	    {
	      return $langs->trans($this->statuts_short[$statut]);
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
		
		$this->fk_parent = '';
		$this->fk_projet = '';
		$this->fk_contrat = '';
		$this->ref = '';
		$this->date_order = '';
		$this->detail = '';
		$this->document = '';
		$this->fk_user_create = '';
		$this->fk_user_validate = '';
		$this->date_create = '';
		$this->tms = '';
		$this->statut = '';

		
	}

}

/**
 * Class OrderbookLine
 */
class OrderbookLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	
	public $fk_parent;
	public $fk_projet;
	public $fk_contrat;
	public $ref;
	public $date_order = '';
	public $detail;
	public $document;
	public $fk_user_create;
	public $fk_user_validate;
	public $date_create = '';
	public $tms = '';
	public $statut;

	/**
	 * @var mixed Sample line property 2
	 */
	
}
