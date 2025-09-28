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
 * \file    /projetpayment.class.php
 * \ingroup
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Projetpayment
 *
 * Put here description of your class
 */
class Projetpayment extends CommonObject
{
	/**
	 * @var string Error code (or message)
	 * @deprecated
	 * @see Projetpayment::errors
	 */
	public $error;
	/**
	 * @var string[] Error codes (or messages)
	 */
	public $errors = array();
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'projetpayment';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'projet_payment';

	/**
	 * @var ProjetpaymentLine[] Lines
	 */
	public $lines = array();

	/**
	 * @var int ID
	 */
	public $id;
	/**
	 */

	public $fk_projet;
	public $ref;
	public $date_payment = '';
	public $date_request = '';
	public $amount;
	public $document;
	public $fk_user_create;
	public $fk_user_mod;
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

		if (isset($this->fk_projet)) {
			 $this->fk_projet = trim($this->fk_projet);
		}
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->amount)) {
			 $this->amount = trim($this->amount);
		}
		if (isset($this->document)) {
			 $this->document = trim($this->document);
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

		$sql.= 'fk_projet,';
		$sql.= 'ref,';
		$sql.= 'date_payment,';
		$sql.= 'date_request,';
		$sql.= 'amount,';
		$sql.= 'document,';
		$sql.= 'fk_user_create,';
		$sql.= 'fk_user_mod,';
		$sql.= 'date_create,';
		$sql.= 'statut';


		$sql .= ') VALUES (';

		$sql .= ' '.(! isset($this->fk_projet)?'NULL':$this->fk_projet).',';
		$sql .= ' '.(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql .= ' '.(! isset($this->date_payment) || dol_strlen($this->date_payment)==0?'NULL':"'".$this->db->idate($this->date_payment)."'").',';
		$sql .= ' '.(! isset($this->date_request) || dol_strlen($this->date_request)==0?'NULL':"'".$this->db->idate($this->date_request)."'").',';
		$sql .= ' '.(! isset($this->amount)?'NULL':"'".$this->amount."'").',';
		$sql .= ' '.(! isset($this->document)?'NULL':"'".$this->db->escape($this->document)."'").',';
		$sql .= ' '.(! isset($this->fk_user_create)?'NULL':$this->fk_user_create).',';
		$sql .= ' '.$user->id.',';
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

		$sql .= " t.fk_projet,";
		$sql .= " t.ref,";
		$sql .= " t.date_payment,";
		$sql .= " t.date_request,";
		$sql .= " t.amount,";
		$sql .= " t.document,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
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

				$this->fk_projet = $obj->fk_projet;
				$this->ref = $obj->ref;
				$this->date_payment = $this->db->jdate($obj->date_payment);
				$this->date_request = $this->db->jdate($obj->date_request);
				$this->amount = $obj->amount;
				$this->document = $obj->document;
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_mod = $obj->fk_user_mod;
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
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lRow=false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.fk_projet,";
		$sql .= " t.ref,";
		$sql .= " t.date_payment,";
		$sql .= " t.date_request,";
		$sql .= " t.amount,";
		$sql .= " t.document,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
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
			$sql .= ' ORDER BY ' . $sortfield . ' ' . $sortorder;
		}
		if (!empty($limit)) {
		 $sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
		}
		$this->lines = array();
		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new ProjetpaymentLine();

				$line->id = $obj->rowid;

				$line->fk_projet = $obj->fk_projet;
				$line->ref = $obj->ref;
				$line->date_payment = $this->db->jdate($obj->date_payment);
				$line->date_request = $this->db->jdate($obj->date_request);
				$line->amount = $obj->amount;
				$line->document = $obj->document;
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->tms = $this->db->jdate($obj->tms);
				$line->statut = $obj->statut;

				if ($lRow)
				{
					$this->id = $obj->rowid;
					$this->fk_projet = $obj->fk_projet;
					$this->ref = $obj->ref;
					$this->date_payment = $this->db->jdate($obj->date_payment);
					$this->date_request = $this->db->jdate($obj->date_request);
					$this->amount = $obj->amount;
					$this->document = $obj->document;
					$this->fk_user_create = $obj->fk_user_create;
					$this->fk_user_mod = $obj->fk_user_mod;
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

		if (isset($this->fk_projet)) {
			 $this->fk_projet = trim($this->fk_projet);
		}
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->amount)) {
			 $this->amount = trim($this->amount);
		}
		if (isset($this->document)) {
			 $this->document = trim($this->document);
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

		$sql .= ' fk_projet = '.(isset($this->fk_projet)?$this->fk_projet:"null").',';
		$sql .= ' ref = '.(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").',';
		$sql .= ' date_payment = '.(! isset($this->date_payment) || dol_strlen($this->date_payment) != 0 ? "'".$this->db->idate($this->date_payment)."'" : 'null').',';
		$sql .= ' date_request = '.(! isset($this->date_request) || dol_strlen($this->date_request) != 0 ? "'".$this->db->idate($this->date_request)."'" : 'null').',';
		$sql .= ' amount = '.(isset($this->amount)?$this->amount:"null").',';
		$sql .= ' document = '.(isset($this->document)?"'".$this->db->escape($this->document)."'":"null").',';
		$sql .= ' fk_user_create = '.(isset($this->fk_user_create)?$this->fk_user_create:"null").',';
		$sql .= ' fk_user_mod = '.$user->id.',';
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
		$object = new Projetpayment($this->db);

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

		$this->fk_projet = '';
		$this->ref = '';
		$this->date_payment = '';
		$this->date_request = '';
		$this->amount = '';
		$this->document = '';
		$this->fk_user_create = '';
		$this->fk_user_mod = '';
		$this->date_create = '';
		$this->tms = '';
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
	function showphoto($imageview,$obj,$document,$object,$projectstatic,$width=100,$docext='',$target=0)
    {
        global $conf;
		$modulepart = 'monprojet';
		$entity = (! empty($projectstatic->entity) ? $projectstatic->entity : $conf->entity);
        $id = (! empty($obj->id) ? $obj->id : $obj->rowid);

        $ret='';$dir='';$file='';$altfile='';$email='';
        if ($target)
        	$target = 'target="_blank"';
        if ($imageview == 'ini')
	  	{
        	$dir=$conf->monprojet->multidir_output[$entity];
	    	$dir.= '/'.$projectstatic->ref.'/'.'pay/';
	    	$dirfile = $projectstatic->ref.'/'.'pay/';
	    	$info_fichero = pathinfo($document);
	    	if (isset($info_fichero['extension']) && $info_fichero['extension']!=strtolower($info_fichero['extension']))
	      		$file=$info_fichero['filename'].'.'.strtolower($info_fichero['extension']);
	    	else
	      		$file= $document;
            $file=preg_replace('/(\.png|\.gif|\.jpg|\.jpeg|\.bmp)/i','_mini\\1',$file);
            if ($id) $file=$id.'/images/thumbs/'.$file;
	    	$namephoto = 'photoini';
	  	}
        if ($imageview == 'doc')
	  	{
	    	$dir=$conf->monprojet->multidir_output[$entity];
	    	$dir.= '/'.$projectstatic->ref.'/'.'pay/';
	    	$dirfile = $projectstatic->ref.'/'.'pay/';
	    	$info_fichero = pathinfo($document);
	    	if (isset($info_fichero['extension']) && $info_fichero['extension']!=strtolower($info_fichero['extension']))
	      		$file=$info_fichero['filename'].'.'.strtolower($info_fichero['extension']);
	    	else
	      		$file= $document;
            //$file=preg_replace('/(\.png|\.gif|\.jpg|\.jpeg|\.bmp)/i','_small\\1',$file);
            if ($id) $file=$id.'/'.$file;
	    	$namephoto = ($docext?$docext:$imageview);
	  	}
        if ($imageview == 'fin')
	  	{
            $dir=$conf->monprojet->multidir_output[$entity];
	    	$dir.= '/'.$projectstatic->ref.'/'.'pay/';
	    	$dirfile = $projectstatic->ref.'/'.'pay/';
	    	$info_fichero = pathinfo($document);
	    	if (isset($info_fichero['extension']) && $info_fichero['extension']!=strtolower($info_fichero['extension']))
	      		$file=$info_fichero['filename'].'.'.strtolower($info_fichero['extension']);
	    	else
	      		$file= $document;
	    	$file=preg_replace('/(\.png|\.gif|\.jpg|\.jpeg|\.bmp)/i','_small\\1',$file);
            if ($id) $file=$id.'/thumbs/'.$file;
            $namephoto = 'photofin';
	  	}
	    if ($dir)
	  	{
            $cache='0';
            if ($file && file_exists($dir.$file))
	      	{
                // TODO Link to large image
               	$dirfile.=$file;
                $ret.='<a href="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$modulepart.'&entity='.$entity.'&file='.urlencode($dirfile).'&cache='.$cache.'" '.$target.'>';
                $ret.='<img alt="'.$namephoto.'" id="photologo'.(preg_replace('/[^a-z]/i','_',$dirfile)).'" class="photologo" border="0" width="'.$width.'" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$modulepart.'&entity='.$entity.'&file='.urlencode($dirfile).'&cache='.$cache.'">';
                $ret.='</a>';
	      	}
            else if ($altfile && file_exists($dir."/".$altfile))
	      	{
                $ret.='<a href="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$modulepart.'&entity='.$entity.'&file='.urlencode($file).'&cache='.$cache.'" '.$target.'>';
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
        else
        	dol_print_error('','Call of showphoto with wrong parameters');
        return $ret;
    }

    /**
     *    	Return HTML code to output a photo
     *
     *    	@param	string		$modulepart			Key to define module concerned ('societe', 'userphoto', 'memberphoto')
     *     	@param  object		$object				Object containing data to retrieve file name
     * 		@param	int			$width				Width of photo
     * 		@param	int			$height				Height of photo (auto if 0)
     * 		@param	int			$caneditfield		Add edit fields
     * 		@param	string		$cssclass			CSS name to use on img for photo
     * 		@param	string		$imagesize		    'mini', 'small' or '' (original)
     *      @param  int         $addlinktofullsize  Add link to fullsize image
     *      @param  int         $cache              1=Accept to use image in cache
     * 	  	@return string    						HTML code to output photo
     */

    function showphotos($imageview,$document,$task_time,$modulepart, $object, $projectstatic,$width=100, $height=0, $caneditfield=0, $cssclass='photowithmargin', $imagesize='', $addlinktofullsize=1, $cache=0,$docext='')
    {
    	global $conf,$langs;
    	$entity = (! empty($object->entity) ? $object->entity : $conf->entity);
    	$id = (! empty($object->id) ? $object->id : $object->rowid);

    	$ret='';$dir='';$file='';$originalfile='';$altfile='';$email='';
    	$id = (! empty($object->id) ? $object->id : $object->rowid);
    	$id = (! empty($task_time->id) ? $task_time->id : $task_time->rowid);

		//$entity = (! empty($projectstatic->entity) ? $projectstatic->entity : $conf->entity);
        //$id = (! empty($obj->id) ? $obj->id : $obj->rowid);
        ////
        $ret='';$dir='';$file='';$altfile='';$email='';
        if ($target)
        	$target = 'target="_blank"';

    	$dir=$conf->$modulepart->dir_output;
    	$dir=$conf->monprojet->multidir_output[$entity];
    	$file = $projectstatic->ref.'/pay/'.$id;
	  		//$dirfile= '/'.$projectstatic->ref.'/'.$object->ref.'/'.$id;
    	$originalfile = $projectstatic->ref.'/pay/';
    	$origdocument = $document;
    	$info_fichero = pathinfo($document);
    	if (isset($info_fichero['extension']) && $info_fichero['extension']!=strtolower($info_fichero['extension']))
    		$document=$info_fichero['filename'].'.'.strtolower($info_fichero['extension']);
    	$object->photo = $document;
    	if (! empty($object->photo)) 
    	{
    		if ((string) $imagesize == 'mini') 
    			$file.=get_exdir($id, 2, 0, 0, $object, $modulepart).'thumbs/'.getImageFileNameForSize($object->photo, '_mini');
    		elseif ((string) $imagesize == 'small') 
    			$file.=get_exdir($id, 2, 0, 0, $object, $modulepart).getImageFileNameForSize($object->photo, '_small');
    		else 
    			$file.=get_exdir($id, 2, 0, 0, $object, $modulepart).'/'.$object->photo;
    		$originalfile.=get_exdir($id, 2, 0, 0, $object, $modulepart).$id.'/'.$origdocument;
    	}
    	if (! empty($conf->global->MAIN_OLD_IMAGE_LINKS)) $altfile=$object->id.".jpg";
        		// For backward compatibility
    	$email=$object->email;
    	//echo '<hr>dir '.$dir;
    	//echo '<hr>file '.$file;
    	if ($dir)
    	{
    		$modulepart = 'monprojet';
    		if ($file && file_exists($dir."/".$file))
    		{
    			if ($addlinktofullsize) $ret.='<a href="'.DOL_URL_ROOT.'/monprojet/viewimage.php?modulepart='.$modulepart.'&entity='.$entity.'&file='.urlencode($originalfile).'&cache='.$cache.'" target="_blank">';
    			$ret.='<img alt="'.$docext.'" id="photologo'.(preg_replace('/[^a-z]/i','_',$file)).'" class="'.$cssclass.'" '.($width?' width="'.$width.'"':'').($height?' height="'.$height.'"':'').' src="'.DOL_URL_ROOT.'/monprojet/viewimage.php?modulepart='.$modulepart.'&entity='.$entity.'&file='.urlencode($file).'&cache='.$cache.'">';
    			if ($addlinktofullsize) $ret.='</a>';
    		}
    		else if ($altfile && file_exists($dir."/".$altfile))
    		{
    			if ($addlinktofullsize) $ret.='<a href="'.DOL_URL_ROOT.'/monprojet/viewimage.php?modulepart='.$modulepart.'&entity='.$entity.'&file='.urlencode($originalfile).'&cache='.$cache.'" target="_blank">';
    			$ret.='<img alt="Photo alt" id="photologo'.(preg_replace('/[^a-z]/i','_',$file)).'" class="'.$cssclass.'" '.($width?' width="'.$width.'"':'').($height?' height="'.$height.'"':'').' src="'.DOL_URL_ROOT.'/monprojet/viewimage.php?modulepart='.$modulepart.'&entity='.$entity.'&file='.urlencode($altfile).'&cache='.$cache.'">';
    			if ($addlinktofullsize) $ret.='</a>';
    		}
    		else
    		{
    			$nophoto='/public/theme/common/nophoto.png';
				if (in_array($modulepart,array('userphoto','contact')))	// For module that are "physical" users
				{
					$nophoto='/public/theme/common/user_anonymous.png';
					if ($object->gender == 'man') $nophoto='/public/theme/common/user_man.png';
					if ($object->gender == 'woman') $nophoto='/public/theme/common/user_woman.png';
				}

				if (! empty($conf->gravatar->enabled) && $email)
				{
	                /**
	                 * @see https://gravatar.com/site/implement/images/php/
	                 */
	                global $dolibarr_main_url_root;
	                $ret.='<!-- Put link to gravatar -->';
                    $ret.='<img class="photo'.$modulepart.($cssclass?' '.$cssclass:'').'" alt="Gravatar avatar" title="'.$email.' Gravatar avatar" border="0"'.($width?' width="'.$width.'"':'').($height?' height="'.$height.'"':'').' src="https://www.gravatar.com/avatar/'.dol_hash(strtolower(trim($email)),3).'?s='.$width.'&d='.urlencode(dol_buildpath($nophoto,2)).'">';	// gravatar need md5 hash
                }
                else
                {
                	$ret.='<img class="photo'.$modulepart.($cssclass?' '.$cssclass:'').'" alt="No photo" border="0"'.($width?' width="'.$width.'"':'').($height?' height="'.$height.'"':'').' src="'.DOL_URL_ROOT.$nophoto.'">';
                }
            }

            if ($caneditfield)
            {
            	if ($object->photo) $ret.="<br>\n";
            	$ret.='<table class="nobordernopadding hideonsmartphone">';
            	if ($object->photo) $ret.='<tr><td align="center"><input type="checkbox" class="flat photodelete" name="deletephoto" id="photodelete"> '.$langs->trans("Delete").'<br><br></td></tr>';
            	$ret.='<tr><td>'.$langs->trans("PhotoFile1").'</td></tr>';
            	$ret.='<tr><td><input type="file" class="flat" name="photo" id="photoinput"></td></tr>';
            	$ret.='</table>';
            }

        }
        else dol_print_error('','Call of showphotos with wrong parameters');

        return $ret;
    }

}

/**
 * Class ProjetpaymentLine
 */
class ProjetpaymentLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */

	public $fk_projet;
	public $ref;
	public $date_payment = '';
	public $date_request = '';
	public $amount;
	public $document;
	public $fk_user_create;
	public $fk_user_mod;
	public $date_create = '';
	public $tms = '';
	public $statut;

	/**
	 * @var mixed Sample line property 2
	 */

}
