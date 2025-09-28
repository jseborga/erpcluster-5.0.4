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
 *  \file       dev/skeletons/productlist.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2013-04-23 13:32
 */

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Productlist extends CommonObject_
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	//var $element='productlist';			//!< Id that identify managed objects
	//var $table_element='productlist';	//!< Name of table without prefix where object is stored

    var $id;
    
	var $entity;
	var $fk_product_father;
	var $fk_unit_father;
	var $fk_product_son;
	var $fk_unit_son;
	var $qty_father;
	var $qty_son;
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
        
		if (isset($this->entity)) $this->entity=trim($this->entity);
		if (isset($this->fk_product_father)) $this->fk_product_father=trim($this->fk_product_father);
		if (isset($this->fk_unit_father)) $this->fk_unit_father=trim($this->fk_unit_father);
		if (isset($this->fk_product_son)) $this->fk_product_son=trim($this->fk_product_son);
		if (isset($this->fk_unit_son)) $this->fk_unit_son=trim($this->fk_unit_son);
		if (isset($this->qty_father)) $this->qty_father=trim($this->qty_father);
		if (isset($this->qty_son)) $this->qty_son=trim($this->qty_son);
		if (isset($this->statut)) $this->statut=trim($this->statut);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."product_list(";
		
		$sql.= "entity,";
		$sql.= "fk_product_father,";
		$sql.= "fk_unit_father,";
		$sql.= "fk_product_son,";
		$sql.= "fk_unit_son,";
		$sql.= "qty_father,";
		$sql.= "qty_son,";
		$sql.= "statut";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->entity)?'NULL':"'".$this->entity."'").",";
		$sql.= " ".(! isset($this->fk_product_father)?'NULL':"'".$this->fk_product_father."'").",";
		$sql.= " ".(! isset($this->fk_unit_father)?'NULL':"'".$this->fk_unit_father."'").",";
		$sql.= " ".(! isset($this->fk_product_son)?'NULL':"'".$this->fk_product_son."'").",";
		$sql.= " ".(! isset($this->fk_unit_son)?'NULL':"'".$this->fk_unit_son."'").",";
		$sql.= " ".(! isset($this->qty_father)?'NULL':"'".$this->qty_father."'").",";
		$sql.= " ".(! isset($this->qty_son)?'NULL':"'".$this->qty_son."'").",";
		$sql.= " ".(! isset($this->statut)?'NULL':"'".$this->statut."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."product_list");

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
		$sql.= " t.fk_product_father,";
		$sql.= " t.fk_unit_father,";
		$sql.= " t.fk_product_son,";
		$sql.= " t.fk_unit_son,";
		$sql.= " t.qty_father,";
		$sql.= " t.qty_son,";
		$sql.= " t.statut";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."product_list as t";
        $sql.= " WHERE t.rowid = ".$id;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;
                
				$this->entity = $obj->entity;
				$this->fk_product_father = $obj->fk_product_father;
				$this->fk_unit_father = $obj->fk_unit_father;
				$this->fk_product_son = $obj->fk_product_son;
				$this->fk_unit_son = $obj->fk_unit_son;
				$this->qty_father = $obj->qty_father;
				$this->qty_son = $obj->qty_son;
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
        
		if (isset($this->entity)) $this->entity=trim($this->entity);
		if (isset($this->fk_product_father)) $this->fk_product_father=trim($this->fk_product_father);
		if (isset($this->fk_unit_father)) $this->fk_unit_father=trim($this->fk_unit_father);
		if (isset($this->fk_product_son)) $this->fk_product_son=trim($this->fk_product_son);
		if (isset($this->fk_unit_son)) $this->fk_unit_son=trim($this->fk_unit_son);
		if (isset($this->qty_father)) $this->qty_father=trim($this->qty_father);
		if (isset($this->qty_son)) $this->qty_son=trim($this->qty_son);
		if (isset($this->statut)) $this->statut=trim($this->statut);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."product_list SET";
        
		$sql.= " entity=".(isset($this->entity)?$this->entity:"null").",";
		$sql.= " fk_product_father=".(isset($this->fk_product_father)?$this->fk_product_father:"null").",";
		$sql.= " fk_unit_father=".(isset($this->fk_unit_father)?$this->fk_unit_father:"null").",";
		$sql.= " fk_product_son=".(isset($this->fk_product_son)?$this->fk_product_son:"null").",";
		$sql.= " fk_unit_son=".(isset($this->fk_unit_son)?$this->fk_unit_son:"null").",";
		$sql.= " qty_father=".(isset($this->qty_father)?$this->qty_father:"null").",";
		$sql.= " qty_son=".(isset($this->qty_son)?$this->qty_son:"null").",";
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."product_list";
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

		$object=new Productlist($this->db);

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
		$this->fk_product_father='';
		$this->fk_unit_father='';
		$this->fk_product_son='';
		$this->fk_unit_son='';
		$this->qty_father='';
		$this->qty_son='';
		$this->statut='';

		
	}

    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetch_product($id)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		$sql.= " t.entity,";
		$sql.= " t.fk_product_father,";
		$sql.= " t.fk_unit_father,";
		$sql.= " t.fk_product_son,";
		$sql.= " t.fk_unit_son,";
		$sql.= " t.qty_father,";
		$sql.= " t.qty_son,";
		$sql.= " t.statut";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."product_list as t";
	$sql.= " WHERE t.fk_product_father = ".$id;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;
                
				$this->entity = $obj->entity;
				$this->fk_product_father = $obj->fk_product_father;
				$this->fk_unit_father = $obj->fk_unit_father;
				$this->fk_product_son = $obj->fk_product_son;
				$this->fk_unit_son = $obj->fk_unit_son;
				$this->qty_father = $obj->qty_father;
				$this->qty_son = $obj->qty_son;
				$this->statut = $obj->statut;

                
            }
            $this->db->free($resql);

            return 1;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::fetch_product ".$this->error, LOG_ERR);
            return -1;
        }
    }

    /**
     *  Load object in memory from the database
     *
     *  @param	int		$fk_product_father    Id object
     *  @return array          	list product son
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetch_list($fk_product_father,$fk_product_son=0)
    {
    	global $langs;
	if (empty($fk_product_father) && empty($fk_product_son))
	  return -1;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		$sql.= " t.entity,";
		$sql.= " t.fk_product_father,";
		$sql.= " t.fk_unit_father,";
		$sql.= " t.fk_product_son,";
		$sql.= " t.fk_unit_son,";
		$sql.= " t.qty_father,";
		$sql.= " t.qty_son,";
		$sql.= " t.statut";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."product_list as t";
	if ($fk_product_father)
	  $sql.= " WHERE t.fk_product_father = ".$fk_product_father;
	elseif ($fk_product_son)
	  $sql.= " WHERE t.fk_product_son = ".$fk_product_son;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
	  {
            if ($this->db->num_rows($resql))
	      {
		$i = 0;
		$num = $this->db->num_rows($resql);
		while ($i < $num)
		  {
		    $obj = $this->db->fetch_object($resql);
		    $this->array[$obj->rowid] = 
		      array(
			    'id'    => $obj->rowid,
			    'entity' => $obj->entity,
			    'fk_product_father' => $obj->fk_product_father,
			    'fk_unit_father' => $obj->fk_unit_father,
			    'fk_product_son' => $obj->fk_product_son,
			    'fk_unit_son' => $obj->fk_unit_son,
			    'qty_father' => $obj->qty_father,
			    'qty_son' => $obj->qty_son,
			    'statut' => $obj->statut
			    );
			    $i++;
		  }
		return $this->array;
            }
            $this->db->free($resql);

            return 1;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::fetch_product ".$this->error, LOG_ERR);
            return -1;
        }
    }

}
?>
