<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *  \file       dev/skeletons/psalaryaprob.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2013-11-02 15:19
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/salary/class/commonobject_.class.php");

//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");

/**
 *	Put here description of your class
 */
class Psalaryaprob extends CommonObject_
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='psalaryaprob';			//!< Id that identify managed objects
	var $table_element='p_salary_aprob';	//!< Name of table without prefix where object is stored

	var $id;
	
	var $entity;
	var $ref;
	var $type;
	var $fk_value;
	var $fk_aprobsup;
	var $state;

	


	/**
	 *  Constructor
	 *
	 *  @param	DoliDb		$db      Database handler
	 */
	function __construct($db)
	{
		$this->db = $db;
		return 1;
	}


	/**
	 *  Create object into database
	 *
	 *  @param	User	$user        User that creates
	 *  @param  int		$notrigger   0=launch triggers after, 1=disable triggers
	 *  @return int      		   	 <0 if KO, Id of created object if OK
	 */
	function create($user, $notrigger=0)
	{
		global $conf, $langs;
		$error=0;

		// Clean parameters
		
		if (isset($this->entity)) $this->entity=trim($this->entity);
		if (isset($this->type)) $this->type=trim($this->type);
		if (isset($this->fk_value)) $this->fk_value=trim($this->fk_value);
		if (isset($this->fk_aprobsup)) $this->fk_aprobsup=trim($this->fk_aprobsup);
		if (isset($this->state)) $this->state=trim($this->state);

		

		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."p_salary_aprob(";
		
		$sql.= "entity,";
		$sql.= "type,";
		$sql.= "fk_value,";
		$sql.= "fk_aprobsup,";
		$sql.= "state";

		
		$sql.= ") VALUES (";
		
		$sql.= " ".(! isset($this->entity)?'NULL':"'".$this->entity."'").",";
		$sql.= " ".(! isset($this->type)?'NULL':"'".$this->type."'").",";
		$sql.= " ".(! isset($this->fk_value)?'NULL':"'".$this->fk_value."'").",";
		$sql.= " ".(! isset($this->fk_aprobsup)?'NULL':"'".$this->fk_aprobsup."'").",";
		$sql.= " ".(! isset($this->state)?'NULL':"'".$this->state."'")."";

		
		$sql.= ")";

		$this->db->begin();

		dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
		{
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."p_salary_aprob");

			if (! $notrigger)
			{
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action calls a trigger.

				//// Call triggers
				//include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
				//$interface=new Interfaces($this->db);
				//$result=$interface->run_triggers('MYOBJECT_CREATE',$this,$user,$langs,$conf);
				//if ($result < 0) { $error++; $this->errors=$interface->errors; }
				//// End call triggers
			}
		}

		// Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
				dol_syslog(get_class($this)."::create ".$errmsg, LOG_ERR);
				$this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return $this->id;
		}
	}


	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function fetch($id)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.entity,";
		$sql.= " t.type,";
		$sql.= " t.fk_value,";
		$sql.= " t.fk_aprobsup,";
		$sql.= " t.state";

		
		$sql.= " FROM ".MAIN_DB_PREFIX."p_salary_aprob as t";
		$sql.= " WHERE t.rowid = ".$id;

		dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);

				$this->id    = $obj->rowid;
				$this->ref   = $obj->rowid;
				$this->entity = $obj->entity;
				$this->type = $obj->type;
				$this->fk_value = $obj->fk_value;
				$this->fk_aprobsup = $obj->fk_aprobsup;
				$this->state = $obj->state;

				
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
	 *  Update object into database
	 *
	 *  @param	User	$user        User that modifies
	 *  @param  int		$notrigger	 0=launch triggers after, 1=disable triggers
	 *  @return int     		   	 <0 if KO, >0 if OK
	 */
	function update($user=0, $notrigger=0)
	{
		global $conf, $langs;
		$error=0;

		// Clean parameters
		
		if (isset($this->entity)) $this->entity=trim($this->entity);
		if (isset($this->type)) $this->type=trim($this->type);
		if (isset($this->fk_value)) $this->fk_value=trim($this->fk_value);
		if (isset($this->fk_aprobsup)) $this->fk_aprobsup=trim($this->fk_aprobsup);
		if (isset($this->state)) $this->state=trim($this->state);

		

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = "UPDATE ".MAIN_DB_PREFIX."p_salary_aprob SET";
		
		$sql.= " entity=".(isset($this->entity)?$this->entity:"null").",";
		$sql.= " type=".(isset($this->type)?$this->type:"null").",";
		$sql.= " fk_value=".(isset($this->fk_value)?$this->fk_value:"null").",";
		$sql.= " fk_aprobsup=".(isset($this->fk_aprobsup)?$this->fk_aprobsup:"null").",";
		$sql.= " state=".(isset($this->state)?$this->state:"null")."";

		
		$sql.= " WHERE rowid=".$this->id;

		$this->db->begin();

		dol_syslog(get_class($this)."::update sql=".$sql, LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
		{
			if (! $notrigger)
			{
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action calls a trigger.

				//// Call triggers
				//include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
				//$interface=new Interfaces($this->db);
				//$result=$interface->run_triggers('MYOBJECT_MODIFY',$this,$user,$langs,$conf);
				//if ($result < 0) { $error++; $this->errors=$interface->errors; }
				//// End call triggers
			}
		}

		// Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
				dol_syslog(get_class($this)."::update ".$errmsg, LOG_ERR);
				$this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}
	}


	/**
	 *  Delete object in database
	 *
	 *	@param  User	$user        User that deletes
	 *  @param  int		$notrigger	 0=launch triggers after, 1=disable triggers
	 *  @return	int					 <0 if KO, >0 if OK
	 */
	function delete($user, $notrigger=0)
	{
		global $conf, $langs;
		$error=0;

		$this->db->begin();

		if (! $error)
		{
			if (! $notrigger)
			{
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action calls a trigger.

				//// Call triggers
				//include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
				//$interface=new Interfaces($this->db);
				//$result=$interface->run_triggers('MYOBJECT_DELETE',$this,$user,$langs,$conf);
				//if ($result < 0) { $error++; $this->errors=$interface->errors; }
				//// End call triggers
			}
		}

		if (! $error)
		{
			$sql = "DELETE FROM ".MAIN_DB_PREFIX."p_salary_aprob";
			$sql.= " WHERE rowid=".$this->id;

			dol_syslog(get_class($this)."::delete sql=".$sql);
			$resql = $this->db->query($sql);
			if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
		}

		// Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
				dol_syslog(get_class($this)."::delete ".$errmsg, LOG_ERR);
				$this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}
	}



	/**
	 *	Load an object from its id and create a new one in database
	 *
	 *	@param	int		$fromid     Id of object to clone
	 * 	@return	int					New id of clone
	 */
	function createFromClone($fromid)
	{
		global $user,$langs;

		$error=0;

		$object=new Psalaryaprob($this->db);

		$this->db->begin();

		// Load source object
		$object->fetch($fromid);
		$object->id=0;
		$object->statut=0;

		// Clear fields
		// ...

		// Create clone
		$result=$object->create($user);

		// Other options
		if ($result < 0)
		{
			$this->error=$object->error;
			$error++;
		}

		if (! $error)
		{


		}

		// End
		if (! $error)
		{
			$this->db->commit();
			return $object->id;
		}
		else
		{
			$this->db->rollback();
			return -1;
		}
	}


	/**
	 *	Initialise object with example values
	 *	Id must be 0 if object instance is a specimen
	 *
	 *	@return	void
	 */
	function initAsSpecimen()
	{
		$this->id=0;
		
		$this->entity='';
		$this->type='';
		$this->fk_value='';
		$this->fk_aprobsup='';
		$this->state='';

		
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getArray($id=0)
	{
		global $langs,$conf;
		$aArray = array();

		$sql = "SELECT";
		$sql.= " t.rowid,";
		$sql.= " t.entity,";
		$sql.= " t.type,";
		$sql.= " t.fk_value,";
		$sql.= " t.fk_aprobsup,";
		$sql.= " t.state";
		$sql.= " FROM ".MAIN_DB_PREFIX."p_salary_aprob as t";
		$sql.= " WHERE t.entity = ".$conf->entity;
		if (!empty($id))
			$sql.= " AND t.rowid = ".$id;
		$sql.= " ORDER BY t.sequen";

		dol_syslog(get_class($this)."::getArray sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);
				$aArray[$obj->sequen] = array(
					'id'          => $obj->rowid,
					'ref'         => $obj->rowid,
					'entity'      => $obj->entity,
					'type'        => $obj->type,
					'fk_value'    => $obj->fk_value,
					'fk_aprobsup' => $obj->aprobsup,
					'state'       => $obj->state
					);
			}
			$this->db->free($resql);
			
			return $aArray;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getArray ".$this->error, LOG_ERR);
			return -1;
		}

	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getArrayAprob()
	{
		global $langs,$conf;
		$aArray = array();

		$sql = "SELECT";
		$sql.= " t.rowid,";
		$sql.= " t.entity,";
		$sql.= " t.type,";
		$sql.= " t.fk_value,";
		$sql.= " t.fk_aprobsup,";
		$sql.= " t.state";
		$sql.= " FROM ".MAIN_DB_PREFIX."p_salary_aprob as t";
		$sql.= " WHERE t.entity = ".$conf->entity;
		$sql.= " AND (t.fk_aprobsup = 0 OR t.fk_aprobsup IS NULL OR t.fk_aprobsup = -1)";

		dol_syslog(get_class($this)."::getArrayAprob sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$i = 0;
				$obj = $this->db->fetch_object($resql);
				$aArray[$i] = array('id'          => $obj->rowid,
					'ref'         => $obj->rowid,
					'entity'      => $obj->entity,
					'type'        => $obj->type,
					'fk_value'    => $obj->fk_value,
					'fk_aprobsup' => $obj->fk_aprobsup,
					'state'       => $obj->state
					);
				$i++;
				$id = $obj->rowid;
				$lOk = true;
				While ($lOk == true)
				{
			  		//buscamos al siguiente
					$sql = "SELECT";
					$sql.= " t.rowid,";
					$sql.= " t.entity,";
					$sql.= " t.type,";
					$sql.= " t.fk_value,";
					$sql.= " t.fk_aprobsup,";
					$sql.= " t.state";
					$sql.= " FROM ".MAIN_DB_PREFIX."p_salary_aprob as t";
					$sql.= " WHERE t.entity = ".$conf->entity;
					$sql.= " AND t.fk_aprobsup = ".$id;

					dol_syslog(get_class($this)."::getArrayAprob sql=".$sql, LOG_DEBUG);
					$resql1=$this->db->query($sql);
					if ($resql1)
					{
						if ($this->db->num_rows($resql1))
						{
							$obj1 = $this->db->fetch_object($resql1);
							$aArray[$i] = array('id'          => $obj1->rowid,
								'ref'         => $obj1->rowid,
								'entity'      => $obj1->entity,
								'type'        => $obj1->type,
								'fk_value'    => $obj1->fk_value,
								'fk_aprobsup' => $obj1->fk_aprobsup,
								'state'       => $obj1->state
								);
							$id = $obj1->rowid;
							$i++;
						}
						else
							$lOk = false;
					}
					else
						$lOk = false;
				}
			}
			$this->db->free($resql);
		  // echo '<pre>';
		  // print_r($aArray);
		  // echo '</pre>';
			return $aArray;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getArray ".$this->error, LOG_ERR);
			return -1;
		}

	}
	

}
?>
