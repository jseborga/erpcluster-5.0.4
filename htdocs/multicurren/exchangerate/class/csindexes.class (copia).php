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
 *  \file       dev/skeletons/csindexes.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2014-07-06 18:17
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Csindexes extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='csindexes';			//!< Id that identify managed objects
	var $table_element='csindexes';		//!< Name of table without prefix where object is stored

    var $id;
    
	var $country;
	var $date_ind='';
	var $currency1;
	var $currency2;
	var $currency3;
	var $currency4;
	var $currency5;
	var $currency6;

    


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
        
		if (isset($this->country)) $this->country=trim($this->country);
		if (isset($this->currency1)) $this->currency1=trim($this->currency1);
		if (isset($this->currency2)) $this->currency2=trim($this->currency2);
		if (isset($this->currency3)) $this->currency3=trim($this->currency3);
		if (isset($this->currency4)) $this->currency4=trim($this->currency4);
		if (isset($this->currency5)) $this->currency5=trim($this->currency5);
		if (isset($this->currency6)) $this->currency6=trim($this->currency6);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."cs_indexes(";
		
		$sql.= "country,";
		$sql.= "date_ind,";
		$sql.= "currency1,";
		$sql.= "currency2,";
		$sql.= "currency3,";
		$sql.= "currency4,";
		$sql.= "currency5,";
		$sql.= "currency6";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->country)?'NULL':"'".$this->country."'").",";
		$sql.= " ".(! isset($this->date_ind) || dol_strlen($this->date_ind)==0?'NULL':$this->db->idate($this->date_ind)).",";
		$sql.= " ".(! isset($this->currency1)?'NULL':"'".$this->currency1."'").",";
		$sql.= " ".(! isset($this->currency2)?'NULL':"'".$this->currency2."'").",";
		$sql.= " ".(! isset($this->currency3)?'NULL':"'".$this->currency3."'").",";
		$sql.= " ".(! isset($this->currency4)?'NULL':"'".$this->currency4."'").",";
		$sql.= " ".(! isset($this->currency5)?'NULL':"'".$this->currency5."'").",";
		$sql.= " ".(! isset($this->currency6)?'NULL':"'".$this->currency6."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."cs_indexes");

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
		
		$sql.= " t.country,";
		$sql.= " t.date_ind,";
		$sql.= " t.currency1,";
		$sql.= " t.currency2,";
		$sql.= " t.currency3,";
		$sql.= " t.currency4,";
		$sql.= " t.currency5,";
		$sql.= " t.currency6";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."cs_indexes as t";
        $sql.= " WHERE t.rowid = ".$id;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;
                
				$this->country = $obj->country;
				$this->date_ind = $this->db->jdate($obj->date_ind);
				$this->currency1 = $obj->currency1;
				$this->currency2 = $obj->currency2;
				$this->currency3 = $obj->currency3;
				$this->currency4 = $obj->currency4;
				$this->currency5 = $obj->currency5;
				$this->currency6 = $obj->currency6;

                
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
        
		if (isset($this->country)) $this->country=trim($this->country);
		if (isset($this->currency1)) $this->currency1=trim($this->currency1);
		if (isset($this->currency2)) $this->currency2=trim($this->currency2);
		if (isset($this->currency3)) $this->currency3=trim($this->currency3);
		if (isset($this->currency4)) $this->currency4=trim($this->currency4);
		if (isset($this->currency5)) $this->currency5=trim($this->currency5);
		if (isset($this->currency6)) $this->currency6=trim($this->currency6);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."cs_indexes SET";
        
		$sql.= " country=".(isset($this->country)?$this->country:"null").",";
		$sql.= " date_ind=".(dol_strlen($this->date_ind)!=0 ? "'".$this->db->idate($this->date_ind)."'" : 'null').",";
		$sql.= " currency1=".(isset($this->currency1)?$this->currency1:"null").",";
		$sql.= " currency2=".(isset($this->currency2)?$this->currency2:"null").",";
		$sql.= " currency3=".(isset($this->currency3)?$this->currency3:"null").",";
		$sql.= " currency4=".(isset($this->currency4)?$this->currency4:"null").",";
		$sql.= " currency5=".(isset($this->currency5)?$this->currency5:"null").",";
		$sql.= " currency6=".(isset($this->currency6)?$this->currency6:"null")."";

        
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."cs_indexes";
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

		$object=new Csindexes($this->db);

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
		
		$this->country='';
		$this->date_ind='';
		$this->currency1='';
		$this->currency2='';
		$this->currency3='';
		$this->currency4='';
		$this->currency5='';
		$this->currency6='';

		
	}

    /**
     *  Load object in memory from the database
     *
     *  @param	int		$countryd    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetch_last($country)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.country,";
		$sql.= " t.date_ind,";
		$sql.= " t.currency1,";
		$sql.= " t.currency2,";
		$sql.= " t.currency3,";
		$sql.= " t.currency4,";
		$sql.= " t.currency5,";
		$sql.= " t.currency6";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."cs_indexes as t";
        $sql.= " WHERE t.country = ".$country;
	$sql.= " ORDER BY t.date_ind DESC ";
	$sql.= $this->db->plimit(0, 1);

    	dol_syslog(get_class($this)."::fetch_last sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;
                
		$this->country = $obj->country;
		$this->date_ind = $this->db->jdate($obj->date_ind);
		$this->currency1 = $obj->currency1;
		$this->currency2 = $obj->currency2;
		$this->currency3 = $obj->currency3;
		$this->currency4 = $obj->currency4;
		$this->currency5 = $obj->currency5;
		$this->currency6 = $obj->currency6;
            }
            $this->db->free($resql);

            return 1;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::fetch_last ".$this->error, LOG_ERR);
            return -1;
        }
    }

}
?>
