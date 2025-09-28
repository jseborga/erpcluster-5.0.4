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
 * \file    poa/poastructure.class.php
 * \ingroup poa
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Poastructure
 *
 * Put here description of your class
 * @see CommonObject
 */
class Poastructure extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'poastructure';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'poa_structure';

	/**
	 * @var PoastructureLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	
	public $entity;
	public $gestion;
	public $type;
	public $fk_father;
	public $fk_area;
	public $ref;
	public $sigla;
	public $label;
	public $pseudonym;
	public $pos;
	public $version;
	public $statut;
	public $unit;

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
		
		if (isset($this->entity)) {
			 $this->entity = trim($this->entity);
		}
		if (isset($this->gestion)) {
			 $this->gestion = trim($this->gestion);
		}
		if (isset($this->type)) {
			 $this->type = trim($this->type);
		}
		if (isset($this->fk_father)) {
			 $this->fk_father = trim($this->fk_father);
		}
		if (isset($this->fk_area)) {
			 $this->fk_area = trim($this->fk_area);
		}
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->sigla)) {
			 $this->sigla = trim($this->sigla);
		}
		if (isset($this->label)) {
			 $this->label = trim($this->label);
		}
		if (isset($this->pseudonym)) {
			 $this->pseudonym = trim($this->pseudonym);
		}
		if (isset($this->pos)) {
			 $this->pos = trim($this->pos);
		}
		if (isset($this->version)) {
			 $this->version = trim($this->version);
		}
		if (isset($this->statut)) {
			 $this->statut = trim($this->statut);
		}
		if (isset($this->unit)) {
			 $this->unit = trim($this->unit);
		}

		

		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';
		
		$sql.= 'entity,';
		$sql.= 'gestion,';
		$sql.= 'type,';
		$sql.= 'fk_father,';
		$sql.= 'fk_area,';
		$sql.= 'ref,';
		$sql.= 'sigla,';
		$sql.= 'label,';
		$sql.= 'pseudonym,';
		$sql.= 'pos,';
		$sql.= 'version,';
		$sql.= 'statut,';
		$sql.= 'unit';

		
		$sql .= ') VALUES (';
		
		$sql .= ' '.(! isset($this->entity)?'NULL':$this->entity).',';
		$sql .= ' '.(! isset($this->gestion)?'NULL':$this->gestion).',';
		$sql .= ' '.(! isset($this->type)?'NULL':$this->type).',';
		$sql .= ' '.(! isset($this->fk_father)?'NULL':$this->fk_father).',';
		$sql .= ' '.(! isset($this->fk_area)?'NULL':$this->fk_area).',';
		$sql .= ' '.(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql .= ' '.(! isset($this->sigla)?'NULL':"'".$this->db->escape($this->sigla)."'").',';
		$sql .= ' '.(! isset($this->label)?'NULL':"'".$this->db->escape($this->label)."'").',';
		$sql .= ' '.(! isset($this->pseudonym)?'NULL':"'".$this->db->escape($this->pseudonym)."'").',';
		$sql .= ' '.(! isset($this->pos)?'NULL':$this->pos).',';
		$sql .= ' '.(! isset($this->version)?'NULL':$this->version).',';
		$sql .= ' '.(! isset($this->statut)?'NULL':$this->statut).',';
		$sql .= ' '.(! isset($this->unit)?'NULL':"'".$this->db->escape($this->unit)."'");

		
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
		$sql .= " t.gestion,";
		$sql .= " t.type,";
		$sql .= " t.fk_father,";
		$sql .= " t.fk_area,";
		$sql .= " t.ref,";
		$sql .= " t.sigla,";
		$sql .= " t.label,";
		$sql .= " t.pseudonym,";
		$sql .= " t.pos,";
		$sql .= " t.version,";
		$sql .= " t.statut,";
		$sql .= " t.unit";

		
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
				$this->gestion = $obj->gestion;
				$this->type = $obj->type;
				$this->fk_father = $obj->fk_father;
				$this->fk_area = $obj->fk_area;
				$this->ref = $obj->ref;
				$this->sigla = $obj->sigla;
				$this->label = $obj->label;
				$this->pseudonym = $obj->pseudonym;
				$this->pos = $obj->pos;
				$this->version = $obj->version;
				$this->statut = $obj->statut;
				$this->unit = $obj->unit;

				
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
		$sql .= " t.gestion,";
		$sql .= " t.type,";
		$sql .= " t.fk_father,";
		$sql .= " t.fk_area,";
		$sql .= " t.ref,";
		$sql .= " t.sigla,";
		$sql .= " t.label,";
		$sql .= " t.pseudonym,";
		$sql .= " t.pos,";
		$sql .= " t.version,";
		$sql .= " t.statut,";
		$sql .= " t.unit";

		
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
				$line = new PoastructureLine();

				$line->id = $obj->rowid;
				
				$line->entity = $obj->entity;
				$line->gestion = $obj->gestion;
				$line->type = $obj->type;
				$line->fk_father = $obj->fk_father;
				$line->fk_area = $obj->fk_area;
				$line->ref = $obj->ref;
				$line->sigla = $obj->sigla;
				$line->label = $obj->label;
				$line->pseudonym = $obj->pseudonym;
				$line->pos = $obj->pos;
				$line->version = $obj->version;
				$line->statut = $obj->statut;
				$line->unit = $obj->unit;

				

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
		
		if (isset($this->entity)) {
			 $this->entity = trim($this->entity);
		}
		if (isset($this->gestion)) {
			 $this->gestion = trim($this->gestion);
		}
		if (isset($this->type)) {
			 $this->type = trim($this->type);
		}
		if (isset($this->fk_father)) {
			 $this->fk_father = trim($this->fk_father);
		}
		if (isset($this->fk_area)) {
			 $this->fk_area = trim($this->fk_area);
		}
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->sigla)) {
			 $this->sigla = trim($this->sigla);
		}
		if (isset($this->label)) {
			 $this->label = trim($this->label);
		}
		if (isset($this->pseudonym)) {
			 $this->pseudonym = trim($this->pseudonym);
		}
		if (isset($this->pos)) {
			 $this->pos = trim($this->pos);
		}
		if (isset($this->version)) {
			 $this->version = trim($this->version);
		}
		if (isset($this->statut)) {
			 $this->statut = trim($this->statut);
		}
		if (isset($this->unit)) {
			 $this->unit = trim($this->unit);
		}

		

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';
		
		$sql .= ' entity = '.(isset($this->entity)?$this->entity:"null").',';
		$sql .= ' gestion = '.(isset($this->gestion)?$this->gestion:"null").',';
		$sql .= ' type = '.(isset($this->type)?$this->type:"null").',';
		$sql .= ' fk_father = '.(isset($this->fk_father)?$this->fk_father:"null").',';
		$sql .= ' fk_area = '.(isset($this->fk_area)?$this->fk_area:"null").',';
		$sql .= ' ref = '.(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").',';
		$sql .= ' sigla = '.(isset($this->sigla)?"'".$this->db->escape($this->sigla)."'":"null").',';
		$sql .= ' label = '.(isset($this->label)?"'".$this->db->escape($this->label)."'":"null").',';
		$sql .= ' pseudonym = '.(isset($this->pseudonym)?"'".$this->db->escape($this->pseudonym)."'":"null").',';
		$sql .= ' pos = '.(isset($this->pos)?$this->pos:"null").',';
		$sql .= ' version = '.(isset($this->version)?$this->version:"null").',';
		$sql .= ' statut = '.(isset($this->statut)?$this->statut:"null").',';
		$sql .= ' unit = '.(isset($this->unit)?"'".$this->db->escape($this->unit)."'":"null");

        
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
		$object = new Poastructure($this->db);

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
		
		$this->entity = '';
		$this->gestion = '';
		$this->type = '';
		$this->fk_father = '';
		$this->fk_area = '';
		$this->ref = '';
		$this->sigla = '';
		$this->label = '';
		$this->pseudonym = '';
		$this->pos = '';
		$this->version = '';
		$this->statut = '';
		$this->unit = '';

		
	}

	//MODIFICADO
	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getlist($fk_father)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.gestion,";
		$sql.= " t.type,";
		$sql.= " t.fk_father,";
		$sql.= " t.fk_area,";
		$sql.= " t.sigla,";
		$sql.= " t.ref,";
		$sql.= " t.label,";
		$sql.= " t.pseudonym,";
		$sql.= " t.unit,";
		$sql.= " t.pos,";
		$sql.= " t.version,";
		$sql.= " t.statut";


		$sql.= " FROM ".MAIN_DB_PREFIX."poa_structure as t";
		$sql.= " WHERE t.fk_father = ".$fk_father;

		dol_syslog(get_class($this)."::getlist sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i = 0;
				$array = array();
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objNew = new Poastructure($this->db);
					$objNew->id    = $obj->rowid;

					$objNew->entity = $obj->entity;
					$objNew->gestion = $obj->gestion;
					$objNew->type = $obj->type;
					$objNew->fk_father = $obj->fk_father;
					$objNew->fk_area = $obj->fk_area;
					$objNew->ref = $obj->ref;
					$objNew->sigla = $obj->sigla;
					$objNew->label = $obj->label;
					$objNew->pseudonym = $obj->pseudonym;
					$objNew->unit = $obj->unit;
					$objNew->pos = $obj->pos;
					$objNew->version = $obj->version;
					$objNew->statut = $obj->statut;

					$array[$obj->rowid] = $objNew;
					$i++;
				}
				return $array;
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
	 *  @param  int     $id    Id object
	 *  @return int             <0 if KO, >0 if OK
	 */
	function getlist_area($fk_area,$gestion)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.gestion,";
		$sql.= " t.type,";
		$sql.= " t.fk_father,";
		$sql.= " t.fk_area,";
		$sql.= " t.sigla,";
		$sql.= " t.ref,";
		$sql.= " t.label,";
		$sql.= " t.pseudonym,";
		$sql.= " t.unit,";
		$sql.= " t.pos,";
		$sql.= " t.version,";
		$sql.= " t.statut";


		$sql.= " FROM ".MAIN_DB_PREFIX."poa_structure as t";
		$sql.= " WHERE t.fk_area = ".$fk_area;
		$sql.= " AND t.gestion = ".$gestion;
		dol_syslog(get_class($this)."::getlist_area sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->array = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i = 0;
				$array = array();
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objNew = new Poastructure($this->db);
					$objNew->id    = $obj->rowid;

					$objNew->entity = $obj->entity;
					$objNew->gestion = $obj->gestion;
					$objNew->type = $obj->type;
					$objNew->fk_father = $obj->fk_father;
					$objNew->fk_area = $obj->fk_area;
					$objNew->ref = $obj->ref;
					$objNew->sigla = $obj->sigla;
					$objNew->label = $obj->label;
					$objNew->pseudonym = $obj->pseudonym;
					$objNew->unit = $obj->unit;
					$objNew->pos = $obj->pos;
					$objNew->version = $obj->version;
					$objNew->statut = $obj->statut;

					$this->array[$obj->rowid] = $objNew;
					$i++;
				}
				$this->db->free($resql);
				return count($this->array);
			}
			$this->db->free($resql);
			return 0;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getlist ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Return combo list of activated countries, into language of user
	 *
	 *  @param	string	$selected       Id or Code or Label of preselected country
	 *  @param  string	$htmlname       Name of html select object
	 *  @param  string	$htmloption     Options html on select object
	 *  @param	string	$maxlength	Max length for labels (0=no limit)
	 *  @param	string	$showempty	View space labels (0=no view)

	 *  @return string           		HTML string with select
	 */
	function select_structure($selected='',$htmlname='fk_father',$htmloption='',$maxlength=0,$showempty=0,$pos=3)
	{
		global $conf,$langs;

		$langs->load("poa@poa");

		$out='';
		$countryArray=array();
		$label=array();

		$sql = "SELECT c.rowid, c.sigla as label, c.label as code_iso, c.fk_father";
		$sql.= " FROM ".MAIN_DB_PREFIX."poa_structure AS c ";
		$sql.= " WHERE c.entity = ".$conf->entity;
		$sql.= " AND c.gestion = ".$_SESSION['gestion'];
		if (!empty($pos))
			$sql.= " AND c.pos = ".$pos;
		$sql.= " ORDER BY c.sigla ASC";
		dol_syslog(get_class($this)."::select_structure sql=".$sql);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$out.= '<select id="select'.$htmlname.'" class="form-control" name="'.$htmlname.'" '.$htmloption.'>';
			if ($showempty)
			{
				$out.= '<option value="-1"';
				if ($selected == -1) $out.= ' selected="selected"';
				$out.= '>&nbsp;</option>';
			}

			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($num)
			{
				$foundselected=false;

				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$countryArray[$i]['rowid'] 		= $obj->rowid;
					$countryArray[$i]['code_iso'] 	= $obj->code_iso;

					$countryArray[$i]['label']		= ($obj->code_iso && $langs->transnoentitiesnoconv("Structure".$obj->code_iso)!="Structure".$obj->code_iso?$langs->transnoentitiesnoconv("Structure".$obj->code_iso):($obj->label!='-'?$obj->label:''));
					$label[$i] 	= $countryArray[$i]['label'];
					$i++;
				}

				array_multisort($label, SORT_ASC, $countryArray);

				foreach ($countryArray as $row)
				{
					//print 'rr'.$selected.'-'.$row['label'].'-'.$row['code_iso'].'<br>';
					if ($selected && $selected != '-1' && ($selected == $row['rowid'] || $selected == $row['code_iso'] || $selected == $row['label']) )
					{
						$foundselected=true;
						$out.= '<option value="'.$row['rowid'].'" selected="selected">';
					}
					else
					{
						$out.= '<option value="'.$row['rowid'].'">';
					}
					//$out.= dol_trunc($row['label'],$maxlength,'middle');
					$out.= $row['label'];
					if ($row['code_iso']) $out.= ' ('.dol_trunc($row['code_iso'],$maxlength,'middle') . ')';
					$out.= '</option>';
				}
			}
			$out.= '</select>';
		}
		else
		{
			dol_print_error($this->db);
		}

		return $out;
	}

	//MODIFICADO
	/**
	 *	Return label of status of object
	 *
	 *	@param      int	$mode       0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto
	 *	@param      int	$type       0=Shell, 1=Buy
	 *	@return     string      	Label of status
	 */
	function getLibStatutx($mode=0, $type=0)
	{
		if($type==0)
			return $this->LibStatut($this->statut,$mode,$type);
		else
			return $this->LibStatut($this->statut_ref,$mode,$type);
	}

	/**
	 *	Return label of a given status
	 *
	 *	@param      int		$status     Statut
	 *	@param      int		$mode       0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto
	 *	@param      int		$type       0=Status "to sell", 1=Status "to buy"
	 *	@return     string      		Label of status
	 */
	function LibStatutx($status,$mode=0,$type=0)
	{
		global $langs;
		$langs->load('poa@poa');

		if ($mode == 0)
		{
			if ($status == 0) return img_picto($langs->trans('Notapproved'),'statut5').' '.($type==0 ? $langs->trans('Notapproved'):$langs->trans('Reformulation unapproved'));
			if ($status == 1) return img_picto($langs->trans('Approved'),'statut4').' '.($type==0 ? $langs->trans('Approved'):$langs->trans('Reformulation approved'));
		}

		if ($mode == 2)
		{
			if ($status == 0) return img_picto($langs->trans('Notapproved'),'statut5').' '.($type==0 ? $langs->trans('Notapproved'):$langs->trans('Reformulation unapproved'));
			if ($status == 1) return img_picto($langs->trans('Approved'),'statut4').' '.($type==0 ? $langs->trans('Approved'):$langs->trans('Reformulation approved'));
		}

		return $langs->trans('Unknown');
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getstructure($fk_structure)
	{
		global $langs;
		$lRet = true;
		$fk_str = $fk_structure;
		$this->aList = array();
		while ($lRet == true)
		{
			$this->fetch($fk_str);
			if ($this->id == $fk_str)
			{
				if ($this->fk_father > 0)
				{
					$this->aList[$fk_str][$this->fk_father] = $fk_str;
					$fk_str = $this->fk_father;
				}
				else
				{
					$this->aList[$fk_str][$this->fk_father] = $fk_str;
					$lRet = false;
				}
			}
			else
			{
				$this->error="Error ".$this->db->lasterror();
				dol_syslog(get_class($this)."::getstructure ".$this->error, LOG_ERR);
				$lRet = false;
				return -1;
			}
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 * date 06/01/2015
	 */
	function fetch_sigla($sigla,$gestion)
	{
		global $langs,$conf;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.gestion,";
		$sql.= " t.type,";
		$sql.= " t.fk_father,";
		$sql.= " t.fk_area,";
		$sql.= " t.ref,";
		$sql.= " t.sigla,";
		$sql.= " t.label,";
		$sql.= " t.pseudonym,";
		$sql.= " t.unit,";
		$sql.= " t.pos,";
		$sql.= " t.version,";
		$sql.= " t.statut";


		$sql.= " FROM ".MAIN_DB_PREFIX."poa_structure as t";
		$sql.= " WHERE t.sigla = '".$sigla."'";
		$sql.= " AND t.gestion = ".$gestion;
		$sql.= " AND t.entity = ".$conf->entity;
		dol_syslog(get_class($this)."::fetch_sigla sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);

				$this->id    = $obj->rowid;

				$this->entity = $obj->entity;
				$this->gestion = $obj->gestion;
				$this->type = $obj->type;
				$this->fk_father = $obj->fk_father;
				$this->fk_area = $obj->fk_area;
				$this->ref = $obj->ref;
				$this->sigla = $obj->sigla;
				$this->label = $obj->label;
				$this->pseudonym = $obj->pseudonym;
				$this->unit = $obj->unit;
				$this->pos = $obj->pos;
				$this->version = $obj->version;
				$this->statut = $obj->statut;


			}
			$this->db->free($resql);

			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getliststr($gestion)
	{
		global $langs;
		$lRet = true;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.gestion,";
		$sql.= " t.type,";
		$sql.= " t.fk_father,";
		$sql.= " t.fk_area,";
		$sql.= " t.ref,";
		$sql.= " t.sigla,";
		$sql.= " t.label,";
		$sql.= " t.pseudonym,";
		$sql.= " t.unit,";
		$sql.= " t.pos,";
		$sql.= " t.version,";
		$sql.= " t.statut";


		$sql.= " FROM ".MAIN_DB_PREFIX."poa_structure as t";
		$sql.= " WHERE t.gestion = ".$gestion;
	  //$sql.= " AND t.statut = 1";

		dol_syslog(get_class($this)."::getliststr sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->aList = array();
		$array = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($this->db->num_rows($resql))
			{
				$j = 0;
				while ($j < $num)
				{
					$array = array();
					$obj = $this->db->fetch_object($resql);
					$lRet = true;
			  //		      $this->aList[$obj->fk_father][$obj->rowid] = $obj->rowid;
					$this->aList[$obj->rowid] = array('father' =>$obj->fk_father,
						'pos' => $obj->pos,
						'obj' => $obj);
					$j++;
				}
				return 1;
			}
			return 0;
		}
		return -1;
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function fetch_son($fk_father)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

	  // $sql.= " t.entity,";
	  // $sql.= " t.gestion,";
		$sql.= " t.fk_father";
	  // $sql.= " t.fk_area,";
	  // $sql.= " t.ref,";
	  // $sql.= " t.sigla,";
	  // $sql.= " t.label,";
	  // $sql.= " t.pseudonym,";
	  // $sql.= " t.pos,";
	  // $sql.= " t.version,";
	  // $sql.= " t.statut";


		$sql.= " FROM ".MAIN_DB_PREFIX."poa_structure as t";
		$sql.= " WHERE t.fk_father = ".$fk_father;
		echo '<hr>'.$sql.= " ORDER BY t.rowid";
		dol_syslog(get_class($this)."::fetch_son sql=".$sql, LOG_DEBUG);
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
			  // $objnew = new Poastructure($this->db);

			  // $objnew->id    = $obj->rowid;

			  // $objnew->entity = $obj->entity;
			  // $objnew->gestion = $obj->gestion;
			  // $objnew->fk_father = $obj->fk_father;
			  // $objnew->fk_area = $obj->fk_area;
			  // $objnew->ref = $obj->ref;
			  // $objnew->sigla = $obj->sigla;
			  // $objnew->label = $obj->label;
			  // $objnew->pseudonym = $obj->pseudonym;
			  // $objnew->pos = $obj->pos;
			  // $objnew->version = $obj->version;
			  // $objnew->statut = $obj->statut;
					$this->array[$obj->rowid] = $obj->fk_father;
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
			dol_syslog(get_class($this)."::fetch_son ".$this->error, LOG_ERR);
			return -1;
		}
	}

	function get_search($rowid,$id,$aArray)
	{
		$res1 = True;
		while ($res1 == True)
		{
			$this->array = array();
			$this->fetch_son($id);
			if (count($this->array)>0)
			{
				foreach ((array) $this->array AS $k => $fk_father)
				{
					$aArray[$fk_father][$k] = $k;
					$resx = $this->get_search($rowid,$k,$aArray);
				}
				$res1 = 0;
			}
			else
				$res1 = 0;
		}
		$this->aList[$rowid] = $aArray;
		return $res1;
		  //		}
	}

	//function lines pl
	function getlinespl()
	{
		global $langs;
		include_once DOL_DOCUMENT_ROOT.'/poa/structure/class/poastructurepl.class.php';
		$objstrpl = new Poastructurepl($this->db);
		$objstrpl->getlist_yearmonth($this->id);
		return $objstrpl->array;
	}
	//function lines ej
	function getlinesej()
	{
		global $langs;
		include_once DOL_DOCUMENT_ROOT.'/poa/structure/class/poastructureej.class.php';
		$objstrej = new Poastructureej($this->db);
		$objstrej->getlist_yearmonth($this->id);
		return $objstrej->array;
	}

}

/**
 * Class PoastructureLine
 */
class PoastructureLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	
	public $entity;
	public $gestion;
	public $type;
	public $fk_father;
	public $fk_area;
	public $ref;
	public $sigla;
	public $label;
	public $pseudonym;
	public $pos;
	public $version;
	public $statut;
	public $unit;

	/**
	 * @var mixed Sample line property 2
	 */
	
}
