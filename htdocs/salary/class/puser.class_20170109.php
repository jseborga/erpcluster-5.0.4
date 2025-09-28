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
 *  \file       dev/skeletons/puser.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2013-08-22 15:03
 */

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Puser // extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	//var $element='puser';			//!< Id that identify managed objects
	//var $table_element='puser';	//!< Name of table without prefix where object is stored

    var $id;
    
	var $fk_user;
	var $firstname;
	var $lastname;
	var $lastnametwo;
	var $docum;
	var $sex;
	var $state_marital;
	var $issued_in;
	var $phone_emergency;
	var $blood_type;
	var $dependents;

    


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
        
		if (isset($this->fk_user)) $this->fk_user=trim($this->fk_user);
		if (isset($this->firstname)) $this->firstname=trim($this->firstname);
		if (isset($this->lastname)) $this->lastname=trim($this->lastname);
		if (isset($this->lastnametwo)) $this->lastnametwo=trim($this->lastnametwo);
		if (isset($this->docum)) $this->docum=trim($this->docum);
		if (isset($this->sex)) $this->sex=trim($this->sex);
		if (isset($this->state_marital)) $this->state_marital=trim($this->state_marital);
		if (isset($this->issued_in)) $this->issued_in=trim($this->issued_in);
		if (isset($this->phone_emergency)) $this->emergency_phone=trim($this->emergency_phone);
		if (isset($this->blood_type)) $this->blood_type=trim($this->blood_type);
		if (isset($this->dependents)) $this->dependents=trim($this->dependents);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."p_user(";
		
		$sql.= "fk_user,";
		$sql.= "firstname,";
		$sql.= "lastname,";
		$sql.= "lastnametwo,";
		$sql.= "docum,";
		$sql.= "sex,";
		$sql.= "state_marital,";
		$sql.= "issued_in,";
		$sql.= "phone_emergency,";
		$sql.= "blood_type,";
		$sql.= "dependents";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->fk_user)?'NULL':"'".$this->fk_user."'").",";
		$sql.= " ".(! isset($this->firstname)?'NULL':"'".$this->db->escape($this->firstname)."'").",";
		$sql.= " ".(! isset($this->lastname)?'NULL':"'".$this->db->escape($this->lastname)."'").",";
		$sql.= " ".(! isset($this->lastnametwo)?'NULL':"'".$this->db->escape($this->lastnametwo)."'").",";
		$sql.= " ".(! isset($this->docum)?'NULL':"'".$this->db->escape($this->docum)."'").",";
		$sql.= " ".(! isset($this->sex)?'NULL':"'".$this->db->escape($this->sex)."'").",";
		$sql.= " ".(! isset($this->state_marital)?'NULL':"'".$this->state_marital."'").",";
		$sql.= " ".(! isset($this->issued_in)?'NULL':"'".$this->issued_in."'").",";
		$sql.= " ".(! isset($this->emergency_phone)?'NULL':"'".$this->emergency_phone."'").",";
		$sql.= " ".(! isset($this->blood_type)?'NULL':"'".$this->blood_type."'").",";
		$sql.= " ".(! isset($this->dependents)?'NULL':"'".$this->dependents."'")." ";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."p_user");

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
		
		$sql.= " t.fk_user,";
		$sql.= " t.firstname,";
		$sql.= " t.lastname,";
		$sql.= " t.lastnametwo,";
		$sql.= " t.docum,";
		$sql.= " t.sex,";
		$sql.= " t.state_marital,";
		$sql.= " t.issued_in,";
		$sql.= " t.phone_emergency,";
		$sql.= " t.blood_type,";
		$sql.= " t.dependents";


		
        $sql.= " FROM ".MAIN_DB_PREFIX."p_user as t";
	$sql.= " WHERE t.rowid = ".$id;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;
                
				$this->fk_user = $obj->fk_user;
				$this->firstname = $obj->firstname;
				$this->lastname = $obj->lastname;
				$this->lastnametwo = $obj->lastnametwo;
				$this->docum = $obj->docum;
				$this->sex = $obj->sex;
				$this->state_marital = $obj->state_marital;
				$this->issued_in = $obj->issued_in;
				$this->phone_emergency = $obj->phone_emergency;
				$this->blood_type = $obj->blood_type;
				$this->dependents = $obj->dependents;

                
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
        
		if (isset($this->fk_user)) $this->fk_user=trim($this->fk_user);
		if (isset($this->firstname)) $this->firstname=trim($this->firstname);
		if (isset($this->lastname)) $this->lastname=trim($this->lastname);
		if (isset($this->lastnametwo)) $this->lastnametwo=trim($this->lastnametwo);
		if (isset($this->docum)) $this->docum=trim($this->docum);
		if (isset($this->sex)) $this->sex=trim($this->sex);
		if (isset($this->state_marital)) $this->state_marital=trim($this->state_marital);
		if (isset($this->issued_in)) $this->issued_in=trim($this->issued_in);
		if (isset($this->phone_emergency)) $this->phone_emergency=trim($this->phone_emergency);
		if (isset($this->blood_type)) $this->blood_type=trim($this->blood_type);
		if (isset($this->dependents)) $this->dependents=trim($this->dependents);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."p_user SET";
        
		$sql.= " fk_user=".(isset($this->fk_user)?$this->fk_user:"null").",";
		$sql.= " firstname=".(isset($this->firstname)?"'".$this->db->escape($this->firstname)."'":"null").",";
		$sql.= " lastname=".(isset($this->lastname)?"'".$this->db->escape($this->lastname)."'":"null").",";
		$sql.= " lastnametwo=".(isset($this->lastnametwo)?"'".$this->db->escape($this->lastnametwo)."'":"null").",";
		$sql.= " docum=".(isset($this->docum)?"'".$this->db->escape($this->docum)."'":"null").",";
		$sql.= " sex=".(isset($this->sex)?"'".$this->db->escape($this->sex)."'":"null").",";
		$sql.= " state_marital=".(isset($this->state_marital)?"'".$this->state_marital."'":"null").",";
		$sql.= " issued_in=".(isset($this->issued_in)?$this->issued_in:"null").",";
		$sql.= " phone_emergency=".(isset($this->phone_emergency)?"'".$this->phone_emergency."'":"null").",";
		$sql.= " blood_type=".(isset($this->blood_type)?"'".$this->blood_type."'":"null").",";
		$sql.= " dependents=".(isset($this->dependents)?$this->dependents:"null");

        
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."p_user";
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

		$object=new Puser($this->db);

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
		
		$this->fk_user='';
		$this->firstname='';
		$this->lastname='';
		$this->lastnametwo='';
		$this->docum='';
		$this->sex='';
		$this->state_marital='';
		$this->issued_in='';
		$this->phone_emergency='';
		$this->blood_type='';
		$this->dependents='';

		
	}



}


?>
