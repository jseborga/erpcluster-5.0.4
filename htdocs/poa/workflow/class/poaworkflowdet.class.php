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
 *  \file       dev/skeletons/poaworkflowdet.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2014-08-22 11:19
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Poaworkflowdet extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='poaworkflowdet';			//!< Id that identify managed objects
	var $table_element='poaworkflowdet';		//!< Name of table without prefix where object is stored

    var $id;
    
	var $fk_poa_workflow;
	var $code_area_last;
	var $code_area_next;
	var $code_procedure;
	var $date_tracking='';
	var $date_read='';
	var $detail;
	var $sequen;
	var $fk_user_create;
	var $tms='';
	var $statut;
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
        
		if (isset($this->fk_poa_workflow)) $this->fk_poa_workflow=trim($this->fk_poa_workflow);
		if (isset($this->code_area_last)) $this->code_area_last=trim($this->code_area_last);
		if (isset($this->code_area_next)) $this->code_area_next=trim($this->code_area_next);
		if (isset($this->code_procedure)) $this->code_procedure=trim($this->code_procedure);
		if (isset($this->detail)) $this->detail=trim($this->detail);
		if (isset($this->sequen)) $this->sequen=trim($this->sequen);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->statut)) $this->statut=trim($this->statut);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."poa_workflow_det(";
		
		$sql.= "fk_poa_workflow,";
		$sql.= "code_area_last,";
		$sql.= "code_area_next,";
		$sql.= "code_procedure,";
		$sql.= "date_tracking,";
		$sql.= "date_read,";
		$sql.= "detail,";
		$sql.= "sequen,";
		$sql.= "fk_user_create,";
		$sql.= "statut";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->fk_poa_workflow)?'NULL':"'".$this->fk_poa_workflow."'").",";
		$sql.= " ".(! isset($this->code_area_last)?'NULL':"'".$this->db->escape($this->code_area_last)."'").",";
		$sql.= " ".(! isset($this->code_area_next)?'NULL':"'".$this->db->escape($this->code_area_next)."'").",";
		$sql.= " ".(! isset($this->code_procedure)?'NULL':"'".$this->db->escape($this->code_procedure)."'").",";
		$sql.= " ".(! isset($this->date_tracking) || dol_strlen($this->date_tracking)==0?'NULL':$this->db->idate($this->date_tracking)).",";
		$sql.= " ".(! isset($this->date_read) || dol_strlen($this->date_read)==0?'NULL':$this->db->idate($this->date_read)).",";
		$sql.= " ".(! isset($this->detail)?'NULL':"'".$this->db->escape($this->detail)."'").",";
		$sql.= " ".(! isset($this->sequen)?'NULL':"'".$this->sequen."'").",";
		$sql.= " ".(! isset($this->fk_user_create)?'NULL':"'".$this->fk_user_create."'").",";
		$sql.= " ".(! isset($this->statut)?'NULL':"'".$this->statut."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."poa_workflow_det");

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
		
		$sql.= " t.fk_poa_workflow,";
		$sql.= " t.code_area_last,";
		$sql.= " t.code_area_next,";
		$sql.= " t.code_procedure,";
		$sql.= " t.date_tracking,";
		$sql.= " t.date_read,";
		$sql.= " t.detail,";
		$sql.= " t.sequen,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."poa_workflow_det as t";
        $sql.= " WHERE t.rowid = ".$id;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;
                
				$this->fk_poa_workflow = $obj->fk_poa_workflow;
				$this->code_area_last = $obj->code_area_last;
				$this->code_area_next = $obj->code_area_next;
				$this->code_procedure = $obj->code_procedure;
				$this->date_tracking = $this->db->jdate($obj->date_tracking);
				$this->date_read = $this->db->jdate($obj->date_read);
				$this->detail = $obj->detail;
				$this->sequen = $obj->sequen;
				$this->fk_user_create = $obj->fk_user_create;
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
        
		if (isset($this->fk_poa_workflow)) $this->fk_poa_workflow=trim($this->fk_poa_workflow);
		if (isset($this->code_area_last)) $this->code_area_last=trim($this->code_area_last);
		if (isset($this->code_area_next)) $this->code_area_next=trim($this->code_area_next);
		if (isset($this->code_procedure)) $this->code_procedure=trim($this->code_procedure);
		if (isset($this->detail)) $this->detail=trim($this->detail);
		if (isset($this->sequen)) $this->sequen=trim($this->sequen);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->statut)) $this->statut=trim($this->statut);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."poa_workflow_det SET";
        
		$sql.= " fk_poa_workflow=".(isset($this->fk_poa_workflow)?$this->fk_poa_workflow:"null").",";
		$sql.= " code_area_last=".(isset($this->code_area_last)?"'".$this->db->escape($this->code_area_last)."'":"null").",";
		$sql.= " code_area_next=".(isset($this->code_area_next)?"'".$this->db->escape($this->code_area_next)."'":"null").",";
		$sql.= " code_procedure=".(isset($this->code_procedure)?"'".$this->db->escape($this->code_procedure)."'":"null").",";
		$sql.= " date_tracking=".(dol_strlen($this->date_tracking)!=0 ? "'".$this->db->idate($this->date_tracking)."'" : 'null').",";
		$sql.= " date_read=".(dol_strlen($this->date_read)!=0 ? "'".$this->db->idate($this->date_read)."'" : 'null').",";
		$sql.= " detail=".(isset($this->detail)?"'".$this->db->escape($this->detail)."'":"null").",";
		$sql.= " sequen=".(isset($this->sequen)?$this->sequen:"null").",";
		$sql.= " fk_user_create=".(isset($this->fk_user_create)?$this->fk_user_create:"null").",";
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."poa_workflow_det";
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

		$object=new Poaworkflowdet($this->db);

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
		
		$this->fk_poa_workflow='';
		$this->code_area_last='';
		$this->code_area_next='';
		$this->code_procedure='';
		$this->date_tracking='';
		$this->date_read='';
		$this->detail='';
		$this->sequen='';
		$this->fk_user_create='';
		$this->tms='';
		$this->statut='';

		
	}

	//modificado rqc
    /**
     *  Load object in memory from the database
     *
     *  @param	int		$fk_poa_workflow    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
	function getlist($fk_poa_workflow,$last=0,$statut=0)
    {
      global $langs;
      $sql = "SELECT";
      $sql.= " t.rowid,";
      
      $sql.= " t.fk_poa_workflow,";
      $sql.= " t.code_area_last,";
      $sql.= " t.code_area_next,";
      $sql.= " t.code_procedure,";
      $sql.= " t.date_tracking,";
      $sql.= " t.date_read,";
      $sql.= " t.detail,";
      $sql.= " t.sequen,";
      $sql.= " t.fk_user_create,";
      $sql.= " t.tms,";
      $sql.= " t.statut";
      
      
      $sql.= " FROM ".MAIN_DB_PREFIX."poa_workflow_det as t";
      $sql.= " WHERE t.fk_poa_workflow = ".$fk_poa_workflow;
      if ($statut)
	$sql.= " AND t.statut = ".$statut;
      if ($last)
	$sql.= " ORDER BY t.sequen DESC";
      else
	$sql.= " ORDER BY t.sequen ASC";
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
		  $objnew = new Poaworkflowdet($this->id);
		  
		  $objnew->id    = $obj->rowid;
		  
		  $objnew->fk_poa_workflow = $obj->fk_poa_workflow;
		  $objnew->code_area_last = $obj->code_area_last;
		  $objnew->code_area_next = $obj->code_area_next;
		  $objnew->code_procedure = $obj->code_procedure;
		  $objnew->date_tracking = $this->db->jdate($obj->date_tracking);
		  $objnew->date_read = $this->db->jdate($obj->date_read);
		  $objnew->detail = $obj->detail;
		  $objnew->sequen = $obj->sequen;
		  $objnew->fk_user_create = $obj->fk_user_create;
		  $objnew->tms = $this->db->jdate($obj->tms);
		  $objnew->statut = $obj->statut;
		  
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
     *  Update object into database
     *
     *  @param	fk_poa_workflow	$fk_poa_workflow        Id poa workflow
     *  @param  statut		$statut	 value por update
     *  @return int     		   	 <0 if KO, >0 if OK
     */
	function update_statut($fk_poa_workflow, $statut)
	{
	  global $conf, $langs;
	  $error=0;
	  
	  if (empty($statut) || empty($fk_poa_workflow)) return -1;
	  // Update request
	  $sql = "UPDATE ".MAIN_DB_PREFIX."poa_workflow_det SET";
	  $sql.= " statut = ".$statut;
	  $sql.= " WHERE fk_poa_workflow=".$fk_poa_workflow;

	  $this->db->begin();

	  dol_syslog(get_class($this)."::update_statut sql=".$sql, LOG_DEBUG);
	  $resql = $this->db->query($sql);
	  if (! $resql) 
	    {
	      $error++; $this->errors[]="Error ".$this->db->lasterror();
	    }

        // Commit or rollback
	  if ($error)
	    {
	      foreach($this->errors as $errmsg)
		{
		  dol_syslog(get_class($this)."::update_statut ".$errmsg, LOG_ERR);
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
}
?>
