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
 * \file    /projettaskpayment.class.php
 * \ingroup
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Projettaskpayment
 *
 * Put here description of your class
 */
class Projettaskpayment extends CommonObject
{
	/**
	 * @var string Error code (or message)
	 * @deprecated
	 * @see Projettaskpayment::errors
	 */
	public $error;
	/**
	 * @var string[] Error codes (or messages)
	 */
	public $errors = array();
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'projettaskpayment';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'projet_task_payment';

	/**
	 * @var ProjettaskpaymentLine[] Lines
	 */
	public $lines = array();

	/**
	 * @var int ID
	 */
	public $id;
	/**
	 */

	public $fk_task;
	public $fk_projet_payment;
	public $document;
	public $detail;
	public $unit_declared;
	public $fk_user_create;
	public $date_create = '';
	public $fk_user_mod;
	public $tms = '';
	public $date_mod = '';
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

		if (isset($this->fk_task)) {
			 $this->fk_task = trim($this->fk_task);
		}
		if (isset($this->fk_projet_payment)) {
			 $this->fk_projet_payment = trim($this->fk_projet_payment);
		}
		if (isset($this->document)) {
			 $this->document = trim($this->document);
		}
		if (isset($this->detail)) {
			 $this->detail = trim($this->detail);
		}
		if (isset($this->unit_declared)) {
			 $this->unit_declared = trim($this->unit_declared);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_mod)) {
			 $this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->statut)) {
			 $this->statut = trim($this->statut);
		}



		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';

		$sql.= 'fk_task,';
		$sql.= 'fk_projet_payment,';
		$sql.= 'document,';
		$sql.= 'detail,';
		$sql.= 'unit_declared,';
		$sql.= 'fk_user_create,';
		$sql.= 'date_create,';
		$sql.= 'fk_user_mod,';
		$sql.= 'date_mod,';
		$sql.= 'statut';


		$sql .= ') VALUES (';

		$sql .= ' '.(! isset($this->fk_task)?'NULL':$this->fk_task).',';
		$sql .= ' '.(! isset($this->fk_projet_payment)?'NULL':$this->fk_projet_payment).',';
		$sql .= ' '.(! isset($this->document)?'NULL':"'".$this->db->escape($this->document)."'").',';
		$sql .= ' '.(! isset($this->detail)?'NULL':"'".$this->db->escape($this->detail)."'").',';
		$sql .= ' '.(! isset($this->unit_declared)?'NULL':"'".$this->unit_declared."'").',';
		$sql .= ' '.(! isset($this->fk_user_create)?'NULL':$this->fk_user_create).',';
		$sql .= ' '.(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':"'".$this->db->idate($this->date_create)."'").',';
		$sql .= ' '.$user->id.',';
		$sql .= ' '.(! isset($this->date_mod) || dol_strlen($this->date_mod)==0?'NULL':"'".$this->db->idate($this->date_mod)."'").',';
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

		$sql .= " t.fk_task,";
		$sql .= " t.fk_projet_payment,";
		$sql .= " t.document,";
		$sql .= " t.detail,";
		$sql .= " t.unit_declared,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.date_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.tms,";
		$sql .= " t.date_mod,";
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

				$this->fk_task = $obj->fk_task;
				$this->fk_projet_payment = $obj->fk_projet_payment;
				$this->document = $obj->document;
				$this->detail = $obj->detail;
				$this->unit_declared = $obj->unit_declared;
				$this->fk_user_create = $obj->fk_user_create;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->fk_user_mod = $obj->fk_user_mod;
				$this->tms = $this->db->jdate($obj->tms);
				$this->date_mod = $this->db->jdate($obj->date_mod);
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
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lRow=false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.fk_task,";
		$sql .= " t.fk_projet_payment,";
		$sql .= " t.document,";
		$sql .= " t.detail,";
		$sql .= " t.unit_declared,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.date_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.tms,";
		$sql .= " t.date_mod,";
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
			$sql .= ' ORDER BY ' . $sortfield . ' ' . $sortorder;
		}
		if (!empty($limit)) {
		 $sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
		}
		$this->lines = array();
		//echo '<hr>'.$sql;
		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new ProjettaskpaymentLine();

				$line->id = $obj->rowid;

				$line->fk_task = $obj->fk_task;
				$line->fk_projet_payment = $obj->fk_projet_payment;
				$line->document = $obj->document;
				$line->detail = $obj->detail;
				$line->unit_declared = $obj->unit_declared;
				$line->fk_user_create = $obj->fk_user_create;
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->tms = $this->db->jdate($obj->tms);
				$line->date_mod = $this->db->jdate($obj->date_mod);
				$line->statut = $obj->statut;

				if ($lRow)
				  {
				    $this->id = $obj->rowid;

				    $this->fk_task = $obj->fk_task;
				    $this->fk_projet_payment = $obj->fk_projet_payment;
				    $this->document = $obj->document;
				    $this->detail = $obj->detail;
				    $this->unit_declared = $obj->unit_declared;
				    $this->fk_user_create = $obj->fk_user_create;
				    $this->date_create = $this->db->jdate($obj->date_create);
				    $this->fk_user_mod = $obj->fk_user_mod;
				    $this->tms = $this->db->jdate($obj->tms);
				    $this->date_mod = $this->db->jdate($obj->date_mod);
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

		if (isset($this->fk_task)) {
			 $this->fk_task = trim($this->fk_task);
		}
		if (isset($this->fk_projet_payment)) {
			 $this->fk_projet_payment = trim($this->fk_projet_payment);
		}
		if (isset($this->document)) {
			 $this->document = trim($this->document);
		}
		if (isset($this->detail)) {
			 $this->detail = trim($this->detail);
		}
		if (isset($this->unit_declared)) {
			 $this->unit_declared = trim($this->unit_declared);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_mod)) {
			 $this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->statut)) {
			 $this->statut = trim($this->statut);
		}



		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';

		$sql .= ' fk_task = '.(isset($this->fk_task)?$this->fk_task:"null").',';
		$sql .= ' fk_projet_payment = '.(isset($this->fk_projet_payment)?$this->fk_projet_payment:"null").',';
		$sql .= ' document = '.(isset($this->document)?"'".$this->db->escape($this->document)."'":"null").',';
		$sql .= ' detail = '.(isset($this->detail)?"'".$this->db->escape($this->detail)."'":"null").',';
		$sql .= ' unit_declared = '.(isset($this->unit_declared)?$this->unit_declared:"null").',';
		$sql .= ' fk_user_create = '.(isset($this->fk_user_create)?$this->fk_user_create:"null").',';
		$sql .= ' date_create = '.(! isset($this->date_create) || dol_strlen($this->date_create) != 0 ? "'".$this->db->idate($this->date_create)."'" : 'null').',';
		$sql .= ' fk_user_mod = '.$user->id.',';
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
		$sql .= ' date_mod = '.(! isset($this->date_mod) || dol_strlen($this->date_mod) != 0 ? "'".$this->db->idate($this->date_mod)."'" : 'null').',';
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
		$object = new Projettaskpayment($this->db);

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
	 * Initialise object with example values
	 * Id must be 0 if object instance is a specimen
	 *
	 * @return void
	 */
	public function initAsSpecimen()
	{
		$this->id = 0;

		$this->fk_task = '';
		$this->fk_projet_payment = '';
		$this->document = '';
		$this->detail = '';
		$this->unit_declared = '';
		$this->fk_user_create = '';
		$this->date_create = '';
		$this->fk_user_mod = '';
		$this->tms = '';
		$this->date_mod = '';
		$this->statut = '';


	}

	/**
	 *  Return status label of object
	 *
	 *  @param  int			$mode       0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto
	 * 	@return string      			Label
	 */
	function getLibStatut($mode=0)
	{
	  return $this->LibStatut($this->statut, $mode);
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
	 *    	Return HTML code to output a photo
	 *
	 *    	@param	string		$modulepart		Key to define module concerned ('societe', 'userphoto', 'memberphoto')
	 *     	@param  Object		$object			Object containing data to retrieve file name
	 * 		@param	int			$width			Width of photo
	 * 	  	@return string    					HTML code to output photo
	 */
	function showphoto($imageview,$task_time,$document,$object,$projectstatic,$width=100,$docext='')
	{
	  global $conf;
	  $modulepart = 'project_task';
	  $entity = (! empty($projectstatic->entity) ? $projectstatic->entity : $conf->entity);
	  $id = (! empty($task_time->id) ? $task_time->id : $task_time->rowid);

	  $ret='';$dir='';$file='';$altfile='';$email='';
	  if ($imageview == 'ini')
	    {
	      $dir=$conf->projet->multidir_output[$entity];
	      $dir.= '/'.$projectstatic->ref.'/'.$object->ref.'/pay/';
	      $dirfile = $projectstatic->ref.'/'.$object->ref.'/pay/';
	      $info_fichero = pathinfo($document);
	      if (isset($info_fichero['extension']) && $info_fichero['extension']!=strtolower($info_fichero['extension']))
		$file=$info_fichero['filename'].'.'.strtolower($info_fichero['extension']);
	      else
		$file= $document;
	      $file=preg_replace('/(\.png|\.gif|\.jpg|\.jpeg|\.bmp)/i','_small\\1',$file);
	      if ($id) $file=$id.'/images/thumbs/'.$file;
	      $namephoto = 'photoini';
	    }
	  if ($imageview == 'doc')
	    {
	      $dir=$conf->projet->multidir_output[$entity];
	      $dir.= '/'.$projectstatic->ref.'/'.$object->ref.'/pay/';
	      $dirfile = $projectstatic->ref.'/'.$object->ref.'/pay/';
	      $info_fichero = pathinfo($document);
	      if (isset($info_fichero['extension']) && $info_fichero['extension']!=strtolower($info_fichero['extension']))
		$file=$info_fichero['filename'].'.'.strtolower($info_fichero['extension']);
	      else
		$file= $document;
	      //$file=preg_replace('/(\.png|\.gif|\.jpg|\.jpeg|\.bmp)/i','_small\\1',$file);
	      //	      if ($id) $file=/'.$file;
	      $namephoto = ($docext?$docext:$imageview);
	    }
	  if ($imageview == 'fin')
	    {
	      $dir=$conf->projet->multidir_output[$entity];
	      $dir.= '/'.$projectstatic->ref.'/'.$object->ref.'/pay/';
	      $dirfile = $projectstatic->ref.'/'.$object->ref.'/pay/';
	      $info_fichero = pathinfo($document);
	      if (isset($info_fichero['extension']) && $info_fichero['extension']!=strtolower($info_fichero['extension']))
		$file=$info_fichero['filename'].'.'.strtolower($info_fichero['extension']);
	      else
		$file= $document;
	      $file=preg_replace('/(\.png|\.gif|\.jpg|\.jpeg|\.bmp)/i','_small\\1',$file);
	      if ($id) $file=$id.'/thumbs/'.$file;
	      $namephoto = 'photofin';
	    }
	  //  echo '<hr>'.$file;
	  // echo '<hr>exit '.file_exists($dir."/".$file);
	  if ($dir)
	    {
	      $cache='0';
	      if ($file && file_exists($dir.$file))
		{
		  $dirfile.= $file;
		  // TODO Link to large image
		  $ret.='<a href="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$modulepart.'&entity='.$entity.'&file='.urlencode($dirfile).'&cache='.$cache.'" target="_blank">';
		  $ret.='<img alt="'.$namephoto.'" id="photologo'.(preg_replace('/[^a-z]/i','_',$dirfile)).'" class="photologo" border="0" width="'.$width.'" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$modulepart.'&entity='.$entity.'&file='.urlencode($dirfile).'&cache='.$cache.'">';
		  $ret.='</a>';
		}
	      else if ($altfile && file_exists($dir."/".$altfile))
		{
		  $ret.='<a href="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$modulepart.'&entity='.$entity.'&file='.urlencode($file).'&cache='.$cache.'" target="_blank">';
		  $ret.='<img alt="Photo alt" id="photologo'.(preg_replace('/[^a-z]/i','_',$file)).'" class="photologo" border="0" width="'.$width.'" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$modulepart.'&entity='.$entity.'&file='.urlencode($altfile).'&cache='.$cache.'">';
		  $ret.='</a>';
		}
	      else
		{
		  if (! empty($conf->gravatar->enabled) && $email)
		    {
		      global $dolibarr_main_url_root;
		      $ret.='<!-- Put link to gravatar -->';
		      $ret.='<img alt="Photo found on Gravatar" title="Photo Gravatar.com - email '.$email.'" border="0" width="'.$width.'" src="http://www.gravatar.com/avatar/'.dol_hash($email).'?s='.$width.'&d='.urlencode(dol_buildpath('/theme/common/nophoto.jpg',2)).'">';
		    }
		  else
		    {
		      $ret.='<img alt="No photo" border="0" width="'.$width.'" src="'.DOL_URL_ROOT.'/theme/common/nophoto.jpg">';
		    }
		}
	    }
	  else dol_print_error('','Call of showphoto with wrong parameters');

	  return $ret;
	}

	/**
	 * Load object in memory from the database
	 *
	 * @param int    $id  Id object
	 * @param string $ref Ref
	 *
	 * @return int <0 if KO, 0 if not found, >0 if OK
	 */
	public function getadvance($id, $filter='')
	{
	  dol_syslog(__METHOD__, LOG_DEBUG);

	  $sql = 'SELECT';
	  $sql .= ' t.statut,';

	  $sql .= " SUM(t.unit_declared) AS advance";


	  $sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
	  $sql .= " WHERE t.fk_task = " . $id;
	  $sql .= " GROUP BY t.statut";

	  $resql = $this->db->query($sql);
	  $this->aArray = array();
	  if ($resql)
	    {
	      $numrows = $this->db->num_rows($resql);
	      if ($numrows) {
		while ($obj = $this->db->fetch_object($resql))
		  {
		    $this->aArray[$obj->statut] = $obj->advance;
		  }
	      }
	      $this->db->free($resql);

	      if ($numrows) {
		return 1;
	      } else {
		return 0;
	      }
	    }
	  else
	    {
	      $this->errors[] = 'Error ' . $this->db->lasterror();
	      dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

	      return - 1;
	    }
	}

	/**
	 * Load object in memory from the database
	 *
	 * @param int    $id  fk_task
	 * @param string $filter Filter
	 *
	 * @return int <0 if KO, 0 if not found, >0 if OK
	 */
	public function update_approve($id, $statut, $filter='')
	{
	  global $conf,$user;
	  dol_syslog(__METHOD__, LOG_DEBUG);
	  if (empty($id) && empty($statut) || (empty($id) || empty($statut)))
	    return -1;

	  $sql = 'SELECT';
	  $sql .= ' t.rowid,';

	  $sql .= " t.fk_task_time,";
	  $sql .= " t.fk_task_payment,";
	  $sql .= " t.document,";
	  $sql .= " t.unit_declared,";
	  $sql .= " t.fk_user_create,";
	  $sql .= " t.date_create,";
	  $sql .= " t.tms,";
	  $sql .= " t.statut ";
	  $sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
	  $sql .= ' INNER JOIN ' . MAIN_DB_PREFIX . $this->table_element_sup . ' as r ON t.fk_task_time = r.rowid';
	  $sql .= " WHERE r.fk_task = " . $id;

	  $resql = $this->db->query($sql);
	  if ($resql)
	    {
	      $numrows = $this->db->num_rows($resql);
	      if ($numrows)
		{
		  $this->db->begin();
		  while ($obj = $this->db->fetch_object($resql))
		    {
		      //actualizamos
		      $newobj = new Projettasktimedoc($this->db);
		      $newobj->fetch($obj->rowid);
		      if ($newobj->id == $obj->rowid)
			{
			  $newobj->statut = $statut;
			  $newobj->fk_user_mod = $user->id;
			  $newobj->date_mod = dol_now();
			  $res = $newobj->update($user);
			  if (!$res>0)
			    $error++;
			}
		      else
			$error++;
		    }
		  if (empty($error))
		    $this->db->commit();
		  else
		    $this->db->rollback();
		}
	      $this->db->free($resql);

	      if (empty($error)) {
		return 1;
	      } else {
		return -1;
	      }
	    }
	  else
	    {
	      $this->errors[] = 'Error ' . $this->db->lasterror();
	      dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

	      return - 1;
	    }
	}

}

/**
 * Class ProjettaskpaymentLine
 */
class ProjettaskpaymentLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */

	public $fk_task;
	public $fk_projet_payment;
	public $document;
	public $detail;
	public $unit_declared;
	public $fk_user_create;
	public $date_create = '';
	public $fk_user_mod;
	public $tms = '';
	public $date_mod = '';
	public $statut;

	/**
	 * @var mixed Sample line property 2
	 */

}
