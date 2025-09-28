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
 *  \file       dev/skeletons/poaactivitywork.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2015-04-15 16:22
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Poaactivitywork extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='poaactivitywork';			//!< Id that identify managed objects
	var $table_element='poaactivitywork';		//!< Name of table without prefix where object is stored

    var $id;

	var $fk_activity;
	var $fk_user;
	var $t1;
	var $t2;
	var $t3;
	var $t4;
	var $t5;
	var $t6;
	var $t7;
	var $t8;
	var $t9;
	var $fk_user_create;
	var $date_create='';
	var $tms='';
	var $statut;




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

		if (isset($this->fk_activity)) $this->fk_activity=trim($this->fk_activity);
		if (isset($this->fk_user)) $this->fk_user=trim($this->fk_user);
		if (isset($this->t1)) $this->t1=trim($this->t1);
		if (isset($this->t2)) $this->t2=trim($this->t2);
		if (isset($this->t3)) $this->t3=trim($this->t3);
		if (isset($this->t4)) $this->t4=trim($this->t4);
		if (isset($this->t5)) $this->t5=trim($this->t5);
		if (isset($this->t6)) $this->t6=trim($this->t6);
		if (isset($this->t7)) $this->t7=trim($this->t7);
		if (isset($this->t8)) $this->t8=trim($this->t8);
		if (isset($this->t9)) $this->t9=trim($this->t9);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->statut)) $this->statut=trim($this->statut);



		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."poa_activity_work(";

		$sql.= "fk_activity,";
		$sql.= "fk_user,";
		$sql.= "t1,";
		$sql.= "t2,";
		$sql.= "t3,";
		$sql.= "t4,";
		$sql.= "t5,";
		$sql.= "t6,";
		$sql.= "t7,";
		$sql.= "t8,";
		$sql.= "t9,";
		$sql.= "fk_user_create,";
		$sql.= "date_create,";
		$sql.= "statut";


        $sql.= ") VALUES (";

		$sql.= " ".(! isset($this->fk_activity)?'NULL':"'".$this->fk_activity."'").",";
		$sql.= " ".(! isset($this->fk_user)?'NULL':"'".$this->fk_user."'").",";
		$sql.= " ".(! isset($this->t1)?'NULL':"'".$this->db->escape($this->t1)."'").",";
		$sql.= " ".(! isset($this->t2)?'NULL':"'".$this->db->escape($this->t2)."'").",";
		$sql.= " ".(! isset($this->t3)?'NULL':"'".$this->db->escape($this->t3)."'").",";
		$sql.= " ".(! isset($this->t4)?'NULL':"'".$this->db->escape($this->t4)."'").",";
		$sql.= " ".(! isset($this->t5)?'NULL':"'".$this->db->escape($this->t5)."'").",";
		$sql.= " ".(! isset($this->t6)?'NULL':"'".$this->db->escape($this->t6)."'").",";
		$sql.= " ".(! isset($this->t7)?'NULL':"'".$this->db->escape($this->t7)."'").",";
		$sql.= " ".(! isset($this->t8)?'NULL':"'".$this->db->escape($this->t8)."'").",";
		$sql.= " ".(! isset($this->t9)?'NULL':"'".$this->db->escape($this->t9)."'").",";
		$sql.= " ".(! isset($this->fk_user_create)?'NULL':"'".$this->fk_user_create."'").",";
		$sql.= " ".(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':$this->db->idate($this->date_create)).",";
		$sql.= " ".(! isset($this->statut)?'NULL':"'".$this->statut."'")."";


		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."poa_activity_work");

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

		$sql.= " t.fk_activity,";
		$sql.= " t.fk_user,";
		$sql.= " t.t1,";
		$sql.= " t.t2,";
		$sql.= " t.t3,";
		$sql.= " t.t4,";
		$sql.= " t.t5,";
		$sql.= " t.t6,";
		$sql.= " t.t7,";
		$sql.= " t.t8,";
		$sql.= " t.t9,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.date_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut";


        $sql.= " FROM ".MAIN_DB_PREFIX."poa_activity_work as t";
        $sql.= " WHERE t.rowid = ".$id;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;

				$this->fk_activity = $obj->fk_activity;
				$this->fk_user = $obj->fk_user;
				$this->t1 = $obj->t1;
				$this->t2 = $obj->t2;
				$this->t3 = $obj->t3;
				$this->t4 = $obj->t4;
				$this->t5 = $obj->t5;
				$this->t6 = $obj->t6;
				$this->t7 = $obj->t7;
				$this->t8 = $obj->t8;
				$this->t9 = $obj->t9;
				$this->fk_user_create = $obj->fk_user_create;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->tms = $this->db->jdate($obj->tms);
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

		if (isset($this->fk_activity)) $this->fk_activity=trim($this->fk_activity);
		if (isset($this->fk_user)) $this->fk_user=trim($this->fk_user);
		if (isset($this->t1)) $this->t1=trim($this->t1);
		if (isset($this->t2)) $this->t2=trim($this->t2);
		if (isset($this->t3)) $this->t3=trim($this->t3);
		if (isset($this->t4)) $this->t4=trim($this->t4);
		if (isset($this->t5)) $this->t5=trim($this->t5);
		if (isset($this->t6)) $this->t6=trim($this->t6);
		if (isset($this->t7)) $this->t7=trim($this->t7);
		if (isset($this->t8)) $this->t8=trim($this->t8);
		if (isset($this->t9)) $this->t9=trim($this->t9);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->statut)) $this->statut=trim($this->statut);



		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."poa_activity_work SET";

		$sql.= " fk_activity=".(isset($this->fk_activity)?$this->fk_activity:"null").",";
		$sql.= " fk_user=".(isset($this->fk_user)?$this->fk_user:"null").",";
		$sql.= " t1=".(isset($this->t1)?"'".$this->db->escape($this->t1)."'":"null").",";
		$sql.= " t2=".(isset($this->t2)?"'".$this->db->escape($this->t2)."'":"null").",";
		$sql.= " t3=".(isset($this->t3)?"'".$this->db->escape($this->t3)."'":"null").",";
		$sql.= " t4=".(isset($this->t4)?"'".$this->db->escape($this->t4)."'":"null").",";
		$sql.= " t5=".(isset($this->t5)?"'".$this->db->escape($this->t5)."'":"null").",";
		$sql.= " t6=".(isset($this->t6)?"'".$this->db->escape($this->t6)."'":"null").",";
		$sql.= " t7=".(isset($this->t7)?"'".$this->db->escape($this->t7)."'":"null").",";
		$sql.= " t8=".(isset($this->t8)?"'".$this->db->escape($this->t8)."'":"null").",";
		$sql.= " t9=".(isset($this->t9)?"'".$this->db->escape($this->t9)."'":"null").",";
		$sql.= " fk_user_create=".(isset($this->fk_user_create)?$this->fk_user_create:"null").",";
		$sql.= " date_create=".(dol_strlen($this->date_create)!=0 ? "'".$this->db->idate($this->date_create)."'" : 'null').",";
		$sql.= " tms=".(dol_strlen($this->tms)!=0 ? "'".$this->db->idate($this->tms)."'" : 'null').",";
		$sql.= " statut=".(isset($this->statut)?$this->statut:"null")."";


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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."poa_activity_work";
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

		$object=new Poaactivitywork($this->db);

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

		$this->fk_activity='';
		$this->fk_user='';
		$this->t1='';
		$this->t2='';
		$this->t3='';
		$this->t4='';
		$this->t5='';
		$this->t6='';
		$this->t7='';
		$this->t8='';
		$this->t9='';
		$this->fk_user_create='';
		$this->date_create='';
		$this->tms='';
		$this->statut='';


	}

	//MODIFICADO
	    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK =0 empty
     */
	function fetch_users($fk_activity,$fk_user)
	{
	  global $langs;
	  $sql = "SELECT";
	  $sql.= " t.rowid,";

	  $sql.= " t.fk_activity,";
	  $sql.= " t.fk_user,";
	  $sql.= " t.t1,";
	  $sql.= " t.t2,";
	  $sql.= " t.t3,";
	  $sql.= " t.t4,";
	  $sql.= " t.t5,";
	  $sql.= " t.t6,";
	  $sql.= " t.t7,";
	  $sql.= " t.t8,";
	  $sql.= " t.t9,";
	  $sql.= " t.fk_user_create,";
	  $sql.= " t.date_create,";
	  $sql.= " t.tms,";
	  $sql.= " t.statut";


	  $sql.= " FROM ".MAIN_DB_PREFIX."poa_activity_work as t";
	  $sql.= " WHERE t.fk_activity = ".$fk_activity;
	  $sql.= " AND t.fk_user = ".$fk_user;
	  dol_syslog(get_class($this)."::fetch_users sql=".$sql, LOG_DEBUG);
	  $resql=$this->db->query($sql);
	  if ($resql)
	    {
	      if ($this->db->num_rows($resql))
		{
		  $obj = $this->db->fetch_object($resql);

		  $this->id    = $obj->rowid;

		  $this->fk_activity = $obj->fk_activity;
		  $this->fk_user = $obj->fk_user;
		  $this->t1 = $obj->t1;
		  $this->t2 = $obj->t2;
		  $this->t3 = $obj->t3;
		  $this->t4 = $obj->t4;
		  $this->t5 = $obj->t5;
		  $this->t6 = $obj->t6;
		  $this->t7 = $obj->t7;
		  $this->t8 = $obj->t8;
		  $this->t9 = $obj->t9;
		  $this->fk_user_create = $obj->fk_user_create;
		  $this->date_create = $this->db->jdate($obj->date_create);
		  $this->tms = $this->db->jdate($obj->tms);
		  $this->statut = $obj->statut;

		  $this->db->free($resql);
		  return 1;
		}
	      $this->db->free($resql);
	      return 0;
	    }
	  else
	    {
	      $this->error="Error ".$this->db->lasterror();
	      dol_syslog(get_class($this)."::fetch_users ".$this->error, LOG_ERR);
	      return -1;
	    }
	}

}
?>
