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
 *  \file       dev/skeletons/fabricationdet.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2013-12-24 15:56
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Fabricationdet extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='fabricationdet';			//!< Id that identify managed objects
	var $table_element='fabricationdet';		//!< Name of table without prefix where object is stored

    var $id;
    
	var $fk_fabrication;
	var $fk_product;
	var $qty;
	var $qty_decrease;
	var $qty_first;
	var $qty_second;
	var $price_total;
	var $date_end='';
	var $date_shipping='';
	var $array;
	var $total;


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
        
		if (isset($this->fk_fabrication)) $this->fk_fabrication=trim($this->fk_fabrication);
		if (isset($this->fk_product)) $this->fk_product=trim($this->fk_product);
		if (isset($this->qty)) $this->qty=trim($this->qty);
		if (isset($this->qty_decrease)) $this->qty_decrease=trim($this->qty_decrease);
		if (isset($this->qty_first)) $this->qty_first=trim($this->qty_first);
		if (isset($this->qty_second)) $this->qty_second=trim($this->qty_second);
		if (isset($this->price_total)) $this->price_total=trim($this->price_total);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."fabricationdet(";
		
		$sql.= "fk_fabrication,";
		$sql.= "fk_product,";
		$sql.= "qty,";
		$sql.= "qty_decrease,";
		$sql.= "qty_first,";
		$sql.= "qty_second,";
		$sql.= "price_total,";
		$sql.= "date_end,";
		$sql.= "date_shipping";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->fk_fabrication)?'NULL':"'".$this->fk_fabrication."'").",";
		$sql.= " ".(! isset($this->fk_product)?'NULL':"'".$this->fk_product."'").",";
		$sql.= " ".(! isset($this->qty)?'NULL':"'".$this->qty."'").",";
		$sql.= " ".(! isset($this->qty_decrease)?'NULL':"'".$this->qty_decrease."'").",";
		$sql.= " ".(! isset($this->qty_first)?'NULL':"'".$this->qty_first."'").",";
		$sql.= " ".(! isset($this->qty_second)?'NULL':"'".$this->qty_second."'").",";
		$sql.= " ".(! isset($this->price_total)?'NULL':"'".$this->price_total."'").",";
		$sql.= " ".(! isset($this->date_end) || dol_strlen($this->date_end)==0?'NULL':$this->db->idate($this->date_end)).",";
		$sql.= " ".(! isset($this->date_shipping) || dol_strlen($this->date_shipping)==0?'NULL':$this->db->idate($this->date_shipping))."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."fabricationdet");

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
		
		$sql.= " t.fk_fabrication,";
		$sql.= " t.fk_product,";
		$sql.= " t.qty,";
		$sql.= " t.qty_decrease,";
		$sql.= " t.qty_first,";
		$sql.= " t.qty_second,";
		$sql.= " t.price_total,";
		$sql.= " t.date_end,";
		$sql.= " t.date_shipping";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."fabricationdet as t";
        $sql.= " WHERE t.rowid = ".$id;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;
                
				$this->fk_fabrication = $obj->fk_fabrication;
				$this->fk_product = $obj->fk_product;
				$this->qty = $obj->qty;
				$this->qty_decrease = $obj->qty_decrease;
				$this->qty_first = $obj->qty_first;
				$this->qty_second = $obj->qty_second;
				$this->price_total = $obj->price_total;
				$this->date_end = $this->db->jdate($obj->date_end);
				$this->date_shipping = $this->db->jdate($obj->date_shipping);

                
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
        
		if (isset($this->fk_fabrication)) $this->fk_fabrication=trim($this->fk_fabrication);
		if (isset($this->fk_product)) $this->fk_product=trim($this->fk_product);
		if (isset($this->qty)) $this->qty=trim($this->qty);
		if (isset($this->qty_decrease)) $this->qty_decrease=trim($this->qty_decrease);
		if (isset($this->qty_first)) $this->qty_first=trim($this->qty_first);
		if (isset($this->qty_second)) $this->qty_second=trim($this->qty_second);
		if (isset($this->price_total)) $this->price_total=trim($this->price_total);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."fabricationdet SET";
        
		$sql.= " fk_fabrication=".(isset($this->fk_fabrication)?$this->fk_fabrication:"null").",";
		$sql.= " fk_product=".(isset($this->fk_product)?$this->fk_product:"null").",";
		$sql.= " qty=".(isset($this->qty)?$this->qty:"null").",";
		$sql.= " qty_decrease=".(isset($this->qty_decrease)?$this->qty_decrease:"null").",";
		$sql.= " qty_first=".(isset($this->qty_first)?$this->qty_first:"null").",";
		$sql.= " qty_second=".(isset($this->qty_second)?$this->qty_second:"null").",";
		$sql.= " price_total=".(isset($this->price_total)?$this->price_total:"null").",";
		$sql.= " date_end=".(dol_strlen($this->date_end)!=0 ? "'".$this->db->idate($this->date_end)."'" : 'null').",";
		$sql.= " date_shipping=".(dol_strlen($this->date_shipping)!=0 ? "'".$this->db->idate($this->date_shipping)."'" : 'null')."";

        
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."fabricationdet";
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

		$object=new Fabricationdet($this->db);

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
		
		$this->fk_fabrication='';
		$this->fk_product='';
		$this->qty='';
		$this->qty_decrease='';
		$this->qty_first='';
		$this->qty_second='';
		$this->price_total='';
		$this->date_end='';
		$this->date_shipping='';

		
	}

    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetch_search($id)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.fk_fabrication,";
		$sql.= " t.fk_product,";
		$sql.= " t.qty,";
		$sql.= " t.qty_decrease,";
		$sql.= " t.qty_first,";
		$sql.= " t.qty_second,";
		$sql.= " t.price_total,";
		$sql.= " t.date_end,";
		$sql.= " t.date_shipping";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."fabricationdet as t";
	$sql.= " WHERE t.fk_fabrication = ".$id;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
	      return 1;
	      $obj = $this->db->fetch_object($resql);
            }
            $this->db->free($resql);
            return 0;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
            return -1;
        }
    }

        /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function getlist($id)
    {
    	global $langs;
        $sql = "SELECT";
	$sql.= " t.rowid,";	
	$sql.= " t.fk_fabrication,";
	$sql.= " t.fk_product,";
	$sql.= " t.qty,";
	$sql.= " t.qty_decrease,";
	$sql.= " t.qty_first,";
	$sql.= " t.qty_second,";
	$sql.= " t.price_total,";
	$sql.= " t.date_end,";
	$sql.= " t.date_shipping";	
        $sql.= " FROM ".MAIN_DB_PREFIX."fabricationdet as t";
	$sql.= " WHERE t.fk_fabrication = ".$id;

    	dol_syslog(get_class($this)."::getlist sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
	$this->array = array();
	$this->total = 0;
        if ($resql)
	  {
	    $num = $this->db->num_rows($resql);
            if ($this->db->num_rows($resql))
	      {
		$i = 0;
		while ($i < $num)
		  {
		    $obj = $this->db->fetch_object($resql);
		    $objnew = new Fabricationdet($this->db);
		    $objnew->id    = $obj->rowid;		    
		    $objnew->fk_fabrication = $obj->fk_fabrication;
		    $objnew->fk_product = $obj->fk_product;
		    $objnew->qty = $obj->qty;
		    $objnew->qty_decrease = $obj->qty_decrease;
		    $objnew->qty_first = $obj->qty_first;
		    $objnew->qty_second = $obj->qty_second;
		    $objnew->price_total = $obj->price_total;
		    $objnew->date_end = $this->db->jdate($obj->date_end);
		    $objnew->date_shipping = $this->db->jdate($obj->date_shipping);
		    $this->array[$obj->rowid] = $objnew;
		    $this->total += $obj->price_total;
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
