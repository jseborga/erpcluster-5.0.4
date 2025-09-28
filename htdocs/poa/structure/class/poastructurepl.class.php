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
 *  \file       dev/skeletons/poastructurepl.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2015-04-29 15:29
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Poastructurepl extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='poastructurepl';			//!< Id that identify managed objects
	var $table_element='poastructurepl';		//!< Name of table without prefix where object is stored

    var $id;
    
	var $fk_structure;
	var $kind;
	var $gestion;
	var $tmonth;
	var $quant;
	var $fk_user_create;
	var $date_create='';
	var $tms='';
	var $statut;
	var $active;
	var $array;
    


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
        
		if (isset($this->fk_structure)) $this->fk_structure=trim($this->fk_structure);
		if (isset($this->kind)) $this->kind=trim($this->kind);
		if (isset($this->gestion)) $this->gestion=trim($this->gestion);
		if (isset($this->tmonth)) $this->tmonth=trim($this->tmonth);
		if (isset($this->quant)) $this->quant=trim($this->quant);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->statut)) $this->statut=trim($this->statut);
		if (isset($this->active)) $this->active=trim($this->active);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."poa_structure_pl(";
		
		$sql.= "fk_structure,";
		$sql.= "kind,";
		$sql.= "gestion,";
		$sql.= "tmonth,";
		$sql.= "quant,";
		$sql.= "fk_user_create,";
		$sql.= "date_create,";
		$sql.= "statut,";
		$sql.= "active";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->fk_structure)?'NULL':"'".$this->fk_structure."'").",";
		$sql.= " ".(! isset($this->kind)?'NULL':"'".$this->db->escape($this->kind)."'").",";
		$sql.= " ".(! isset($this->gestion)?'NULL':"'".$this->gestion."'").",";
		$sql.= " ".(! isset($this->tmonth)?'NULL':"'".$this->tmonth."'").",";
		$sql.= " ".(! isset($this->quant)?'NULL':"'".$this->quant."'").",";
		$sql.= " ".(! isset($this->fk_user_create)?'NULL':"'".$this->fk_user_create."'").",";
		$sql.= " ".(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':$this->db->idate($this->date_create)).",";
		$sql.= " ".(! isset($this->statut)?'NULL':"'".$this->statut."'").",";
		$sql.= " ".(! isset($this->active)?'NULL':"'".$this->active."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."poa_structure_pl");

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
		
		$sql.= " t.fk_structure,";
		$sql.= " t.kind,";
		$sql.= " t.gestion,";
		$sql.= " t.tmonth,";
		$sql.= " t.quant,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.date_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut,";
		$sql.= " t.active";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."poa_structure_pl as t";
        $sql.= " WHERE t.rowid = ".$id;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;
                
				$this->fk_structure = $obj->fk_structure;
				$this->kind = $obj->kind;
				$this->gestion = $obj->gestion;
				$this->tmonth = $obj->tmonth;
				$this->quant = $obj->quant;
				$this->fk_user_create = $obj->fk_user_create;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->tms = $this->db->jdate($obj->tms);
				$this->statut = $obj->statut;
				$this->active = $obj->active;

                
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
        
		if (isset($this->fk_structure)) $this->fk_structure=trim($this->fk_structure);
		if (isset($this->kind)) $this->kind=trim($this->kind);
		if (isset($this->gestion)) $this->gestion=trim($this->gestion);
		if (isset($this->tmonth)) $this->tmonth=trim($this->tmonth);
		if (isset($this->quant)) $this->quant=trim($this->quant);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->statut)) $this->statut=trim($this->statut);
		if (isset($this->active)) $this->active=trim($this->active);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."poa_structure_pl SET";
        
		$sql.= " fk_structure=".(isset($this->fk_structure)?$this->fk_structure:"null").",";
		$sql.= " kind=".(isset($this->kind)?"'".$this->db->escape($this->kind)."'":"null").",";
		$sql.= " gestion=".(isset($this->gestion)?$this->gestion:"null").",";
		$sql.= " tmonth=".(isset($this->tmonth)?$this->tmonth:"null").",";
		$sql.= " quant=".(isset($this->quant)?$this->quant:"null").",";
		$sql.= " fk_user_create=".(isset($this->fk_user_create)?$this->fk_user_create:"null").",";
		$sql.= " date_create=".(dol_strlen($this->date_create)!=0 ? "'".$this->db->idate($this->date_create)."'" : 'null').",";
		$sql.= " tms=".(dol_strlen($this->tms)!=0 ? "'".$this->db->idate($this->tms)."'" : 'null').",";
		$sql.= " statut=".(isset($this->statut)?$this->statut:"null").",";
		$sql.= " active=".(isset($this->active)?$this->active:"null")."";

        
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."poa_structure_pl";
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

		$object=new Poastructurepl($this->db);

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
		
		$this->fk_structure='';
		$this->kind='';
		$this->gestion='';
		$this->tmonth='';
		$this->quant='';
		$this->fk_user_create='';
		$this->date_create='';
		$this->tms='';
		$this->statut='';
		$this->active='';

		
	}

	//MODIFICADO
    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function getlist_yearmonth($id)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.fk_structure,";
		$sql.= " t.kind,";
		$sql.= " t.gestion,";
		$sql.= " t.tmonth,";
		$sql.= " t.quant,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.date_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut,";
		$sql.= " t.active";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."poa_structure_pl as t";
        $sql.= " WHERE t.fk_structure = ".$id;

    	dol_syslog(get_class($this)."::getlist sql=".$sql, LOG_DEBUG);
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
		    $objnew = new Poastructurepl($this->db);
		    
		    $objnew->id    = $obj->rowid;		    
		    $objnew->fk_structure = $obj->fk_structure;
		    $objnew->kind = $obj->kind;
		    $objnew->gestion = $obj->gestion;
		    $objnew->tmonth = $obj->tmonth;
		    $objnew->quant = $obj->quant;
		    $objnew->fk_user_create = $obj->fk_user_create;
		    $objnew->date_create = $this->db->jdate($obj->date_create);
		    $objnew->tms = $this->db->jdate($obj->tms);
		    $objnew->statut = $obj->statut;
		    $objnew->active = $obj->active;
		    
		    $this->array[$obj->gestion][$obj->tmonth][$obj->kind] = $objnew;
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
            dol_syslog(get_class($this)."::getlist ".$this->error, LOG_ERR);
            return -1;
        }
    }
	
}
?>
