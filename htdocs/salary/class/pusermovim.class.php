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
 *  \file       dev/skeletons/pusermovim.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2013-06-17 13:26
 */

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Pusermovim // extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	//var $element='pusermovim';			//!< Id that identify managed objects
	//var $table_element='pusermovim';	//!< Name of table without prefix where object is stored

    var $id;
    
	var $fk_user;
	var $fk_concept;
	var $fk_period;
	var $fk_type_fol;
	var $fk_cc;
	var $time_unfo;
	var $amount;
	var $amount_base;
	var $date_pay='';
	var $fk_user_creator;
	var $date_creator='';
	var $sequen;

    


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
		if (isset($this->fk_concept)) $this->fk_concept=trim($this->fk_concept);
		if (isset($this->fk_period)) $this->fk_period=trim($this->fk_period);
		if (isset($this->fk_type_fol)) $this->fk_type_fol=trim($this->fk_type_fol);
		if (isset($this->fk_cc)) $this->fk_cc=trim($this->fk_cc);
		if (isset($this->time_unfo)) $this->time_unfo=trim($this->time_unfo);
		if (isset($this->amount)) $this->amount=trim($this->amount);
		if (isset($this->amount_base)) $this->amount_base=trim($this->amount_base);
		if (isset($this->fk_user_creator)) $this->fk_user_creator=trim($this->fk_user_creator);
		if (isset($this->sequen)) $this->sequen=trim($this->sequen);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."p_user_movim(";
		
		$sql.= "fk_user,";
		$sql.= "fk_concept,";
		$sql.= "fk_period,";
		$sql.= "fk_type_fol,";
		$sql.= "fk_cc,";
		$sql.= "time_unfo,";
		$sql.= "amount,";
		$sql.= "amount_base,";
		$sql.= "date_pay,";
		$sql.= "fk_user_creator,";
		$sql.= "date_creator,";
		$sql.= "sequen";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->fk_user)?'NULL':"'".$this->fk_user."'").",";
		$sql.= " ".(! isset($this->fk_concept)?'NULL':"'".$this->fk_concept."'").",";
		$sql.= " ".(! isset($this->fk_period)?'NULL':"'".$this->fk_period."'").",";
		$sql.= " ".(! isset($this->fk_type_fol)?'NULL':"'".$this->fk_type_fol."'").",";
		$sql.= " ".(! isset($this->fk_cc)?'NULL':"'".$this->fk_cc."'").",";
		$sql.= " ".(! isset($this->time_unfo)?'NULL':"'".$this->time_unfo."'").",";
		$sql.= " ".(! isset($this->amount)?'NULL':"'".$this->amount."'").",";
		$sql.= " ".(! isset($this->amount_base)?'NULL':"'".$this->amount_base."'").",";
		$sql.= " ".(! isset($this->date_pay) || dol_strlen($this->date_pay)==0?'NULL':$this->db->idate($this->date_pay)).",";
		$sql.= " ".(! isset($this->fk_user_creator)?'NULL':"'".$this->fk_user_creator."'").",";
		$sql.= " ".(! isset($this->date_creator) || dol_strlen($this->date_creator)==0?'NULL':$this->db->idate($this->date_creator)).",";
		$sql.= " ".(! isset($this->sequen)?'NULL':"'".$this->sequen."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."p_user_movim");

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
		$sql.= " t.fk_concept,";
		$sql.= " t.fk_period,";
		$sql.= " t.fk_type_fol,";
		$sql.= " t.fk_cc,";
		$sql.= " t.time_unfo,";
		$sql.= " t.amount,";
		$sql.= " t.amount_base,";
		$sql.= " t.date_pay,";
		$sql.= " t.fk_user_creator,";
		$sql.= " t.date_creator,";
		$sql.= " t.sequen";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."p_user_movim as t";
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
				$this->fk_concept = $obj->fk_concept;
				$this->fk_period = $obj->fk_period;
				$this->fk_type_fol = $obj->fk_type_fol;
				$this->fk_cc = $obj->fk_cc;
				$this->time_unfo = $obj->time_unfo;
				$this->amount = $obj->amount;
				$this->amount_base = $obj->amount_base;
				$this->date_pay = $this->db->jdate($obj->date_pay);
				$this->fk_user_creator = $obj->fk_user_creator;
				$this->date_creator = $this->db->jdate($obj->date_creator);
				$this->sequen = $obj->sequen;

                
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
		if (isset($this->fk_concept)) $this->fk_concept=trim($this->fk_concept);
		if (isset($this->fk_period)) $this->fk_period=trim($this->fk_period);
		if (isset($this->fk_type_fol)) $this->fk_type_fol=trim($this->fk_type_fol);
		if (isset($this->fk_cc)) $this->fk_cc=trim($this->fk_cc);
		if (isset($this->time_unfo)) $this->time_unfo=trim($this->time_unfo);
		if (isset($this->amount)) $this->amount=trim($this->amount);
		if (isset($this->amount_base)) $this->amount_base=trim($this->amount_base);
		if (isset($this->fk_user_creator)) $this->fk_user_creator=trim($this->fk_user_creator);
		if (isset($this->sequen)) $this->sequen=trim($this->sequen);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."p_user_movim SET";
        
		$sql.= " fk_user=".(isset($this->fk_user)?$this->fk_user:"null").",";
		$sql.= " fk_concept=".(isset($this->fk_concept)?$this->fk_concept:"null").",";
		$sql.= " fk_period=".(isset($this->fk_period)?$this->fk_period:"null").",";
		$sql.= " fk_type_fol=".(isset($this->fk_type_fol)?$this->fk_type_fol:"null").",";
		$sql.= " fk_cc=".(isset($this->fk_cc)?$this->fk_cc:"null").",";
		$sql.= " time_unfo=".(isset($this->time_unfo)?$this->time_unfo:"null").",";
		$sql.= " amount=".(isset($this->amount)?$this->amount:"null").",";
		$sql.= " amount_base=".(isset($this->amount_base)?$this->amount_base:"null").",";
		$sql.= " date_pay=".(dol_strlen($this->date_pay)!=0 ? "'".$this->db->idate($this->date_pay)."'" : 'null').",";
		$sql.= " fk_user_creator=".(isset($this->fk_user_creator)?$this->fk_user_creator:"null").",";
		$sql.= " date_creator=".(dol_strlen($this->date_creator)!=0 ? "'".$this->db->idate($this->date_creator)."'" : 'null').",";
		$sql.= " sequen=".(isset($this->sequen)?$this->sequen:"null")."";

        
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."p_user_movim";
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

		$object=new Pusermovim($this->db);

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
		$this->fk_concept='';
		$this->fk_period='';
		$this->fk_type_fol='';
		$this->fk_cc='';
		$this->time_unfo='';
		$this->amount='';
		$this->amount_base='';
		$this->date_pay='';
		$this->fk_user_creator='';
		$this->date_creator='';
		$this->sequen='';

		
	}

}
?>
