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
 * \file    poa/poaareauser.class.php
 * \ingroup poa
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Poaareauser
 *
 * Put here description of your class
 * @see CommonObject
 */
class Poaareauser extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'poaareauser';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'poa_area_user';

	/**
	 * @var PoaareauserLine[] Lines
	 */
	public $lines = array();

	/**
	 */

	public $fk_area;
	public $fk_user;
	public $date_create = '';
	public $tms = '';
	public $active;
	public $privilege;
	public $array;

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

		if (isset($this->fk_area)) {
			$this->fk_area = trim($this->fk_area);
		}
		if (isset($this->fk_user)) {
			$this->fk_user = trim($this->fk_user);
		}
		if (isset($this->active)) {
			$this->active = trim($this->active);
		}
		if (isset($this->privilege)) {
			$this->privilege = trim($this->privilege);
		}



		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';

		$sql.= 'fk_area,';
		$sql.= 'fk_user,';
		$sql.= 'date_create,';
		$sql.= 'active,';
		$sql.= 'privilege';


		$sql .= ') VALUES (';

		$sql .= ' '.(! isset($this->fk_area)?'NULL':$this->fk_area).',';
		$sql .= ' '.(! isset($this->fk_user)?'NULL':$this->fk_user).',';
		$sql .= ' '.(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':"'".$this->db->idate($this->date_create)."'").',';
		$sql .= ' '.(! isset($this->active)?'NULL':$this->active).',';
		$sql .= ' '.(! isset($this->privilege)?'NULL':$this->privilege);


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

		$sql .= " t.fk_area,";
		$sql .= " t.fk_user,";
		$sql .= " t.date_create,";
		$sql .= " t.tms,";
		$sql .= " t.active,";
		$sql .= " t.privilege";


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

				$this->fk_area = $obj->fk_area;
				$this->fk_user = $obj->fk_user;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->tms = $this->db->jdate($obj->tms);
				$this->active = $obj->active;
				$this->privilege = $obj->privilege;


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
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='')
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.fk_area,";
		$sql .= " t.fk_user,";
		$sql .= " t.date_create,";
		$sql .= " t.tms,";
		$sql .= " t.active,";
		$sql .= " t.privilege";


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
				$line = new PoaareauserLine();

				$line->id = $obj->rowid;

				$line->fk_area = $obj->fk_area;
				$line->fk_user = $obj->fk_user;
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->tms = $this->db->jdate($obj->tms);
				$line->active = $obj->active;
				$line->privilege = $obj->privilege;



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

		if (isset($this->fk_area)) {
			$this->fk_area = trim($this->fk_area);
		}
		if (isset($this->fk_user)) {
			$this->fk_user = trim($this->fk_user);
		}
		if (isset($this->active)) {
			$this->active = trim($this->active);
		}
		if (isset($this->privilege)) {
			$this->privilege = trim($this->privilege);
		}



		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';

		$sql .= ' fk_area = '.(isset($this->fk_area)?$this->fk_area:"null").',';
		$sql .= ' fk_user = '.(isset($this->fk_user)?$this->fk_user:"null").',';
		$sql .= ' date_create = '.(! isset($this->date_create) || dol_strlen($this->date_create) != 0 ? "'".$this->db->idate($this->date_create)."'" : 'null').',';
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
		$sql .= ' active = '.(isset($this->active)?$this->active:"null").',';
		$sql .= ' privilege = '.(isset($this->privilege)?$this->privilege:"null");


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
		$object = new Poaareauser($this->db);

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
			if ($status == 3) return img_picto($langs->trans('Visitante'),'statut4').' '.$langs->trans('Visitante');
			if ($status == 2) return img_picto($langs->trans('User'),'statut4').' '.$langs->trans('User');
			if ($status == 1) return img_picto($langs->trans('Administrator'),'statut4').' '.$langs->trans('Administrator');
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

		$this->fk_area = '';
		$this->fk_user = '';
		$this->date_create = '';
		$this->tms = '';
		$this->active = '';
		$this->privilege = '';
	}

	//modificado
	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getlist($fk_area,$fk_user=0)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.fk_area,";
		$sql.= " t.fk_user,";
		$sql.= " t.date_create,";
		$sql.= " t.tms,";
		$sql.= " t.active,";
		$sql.= " t.privilege";

		$sql.= " FROM ".MAIN_DB_PREFIX."poa_area_user as t";
		$sql.= " WHERE t.fk_area = ".$fk_area;
		if ($fk_user>0)
			$sql.= " AND t.fk_user = ".$fk_user;
		dol_syslog(get_class($this)."::getlist sql=".$sql, LOG_DEBUG);
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
					$objnew = new Poaareauser($this->db);
					$objnew->id      = $obj->rowid;
					$objnew->fk_area = $obj->fk_area;
					$objnew->fk_user = $obj->fk_user;
					$objnew->date_create = $this->db->jdate($obj->date_create);
					$objnew->tms         = $this->db->jdate($obj->tms);
					$objnew->active      = $obj->active;
					$objnew->privilege   = $obj->privilege;

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
	 *  Areas a las que pertenece el usuario
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getuserarea($fk_user,$lSon=true)
	{
		global $langs;
		$aArea = array();
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.fk_area,";
		$sql.= " t.fk_user,";
		$sql.= " t.date_create,";
		$sql.= " t.tms,";
		$sql.= " t.active,";
		$sql.= " t.privilege";
		$sql.= " FROM ".MAIN_DB_PREFIX."poa_area_user as t";
		$sql.= " WHERE t.fk_user = ".$fk_user;
		$sql.= " AND t.active = 1";
		dol_syslog(get_class($this)."::getuserarea sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					include_once DOL_DOCUMENT_ROOT.'/poa/area/class/poaarea.class.php';
					$obja = new Poaarea($this->db);
					if ($obja->fetch($obj->fk_area)>0)
					{
						if ($obja->id == $obj->fk_area)
						{
							$obja->privilege = $obj->privilege;
							$aArea[$obj->fk_area] = $obja;
						}
					}
					if ($lSon)
					{
						$obja->getlist_son($obj->fk_area);
						if (count($obja->array) > 0)
						{
							foreach ((array) $obja->array AS $j => $objar)
							{
								$objar->privilege = $obj->privilege;
								$aArea[$objar->id] = $objar;
				  				//nuevamente buscamos si tiene hijos del hijo
								$objaa = new Poaarea($this->db);
								$objaa->getlist_son($objar->id);
								if (count($objaa->array) > 0)
								{
									foreach ((array) $objaa->array AS $k => $objara)
									{
										$objara->privilege = $obj->privilege;
										$aArea[$objara->id] = $objara;

									}
								}

							}
						}
					}
					$i++;
				}
				return $aArea;
			}
			$this->db->free($resql);

			return array();
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getuserarea ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *  Areas a las que pertenece el usuario
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getareauser($fk_area,$order='')
	{
		global $langs;
		$aArea = array();
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.fk_area,";
		$sql.= " t.fk_user,";
		$sql.= " t.date_create,";
		$sql.= " t.tms,";
		$sql.= " t.active,";
		$sql.= " t.privilege";
		$sql.= " FROM ".MAIN_DB_PREFIX."poa_area_user as t";
		if ($order == 'user')
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."user AS u ON t.fk_user = u.rowid ";
		$sql.= " WHERE t.fk_area = ".$fk_area;
		$sql.= " AND t.active = 1";
		if ($order == 'user')
			$sql.= " ORDER BY u.lastname, u.firstname";
		dol_syslog(get_class($this)."::getareauser sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->lines = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$num = $this->db->num_rows($resql);
				while ($obj = $this->db->fetch_object($resql))
				{
					$line = new PoaareauserLine();

					$line->id = $obj->rowid;

					$line->fk_area = $obj->fk_area;
					$line->fk_user = $obj->fk_user;
					$line->date_create = $this->db->jdate($obj->date_create);
					$line->tms = $this->db->jdate($obj->tms);
					$line->active = $obj->active;
					$line->privilege = $obj->privilege;

					$this->lines[] = $line;
				}
				$this->db->free($resql);
				return $num;
			}
			$this->db->free($resql);
			return $num;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getareauser ".$this->error, LOG_ERR);
			return -1;
		}
	}

}

/**
 * Class PoaareauserLine
 */
class PoaareauserLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */

	public $fk_area;
	public $fk_user;
	public $date_create = '';
	public $tms = '';
	public $active;
	public $privilege;

	/**
	 * @var mixed Sample line property 2
	 */

}
