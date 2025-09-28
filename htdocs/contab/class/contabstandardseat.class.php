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
 *  \file       dev/skeletons/contabstandardseat.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2013-05-13 13:44
 */

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Contabstandardseat // extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	//var $element='contabstandardseat';			//!< Id that identify managed objects
	//var $table_element='contabstandardseat';	//!< Name of table without prefix where object is stored

    var $id;
    
	var $entity;
	var $fk_point_entry;
	var $sequence;
	var $status;
	var $description;
	var $type_seat;
	var $type_balance;
	var $debit_account;
	var $credit_account;
	var $currency;
	var $currency_value1;
	var $currency_value2;
	var $history;
	var $history_group;
	var $origin;

    


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
		if (isset($this->fk_point_entry)) $this->fk_point_entry=trim($this->fk_point_entry);
		if (isset($this->sequence)) $this->sequence=trim($this->sequence);
		if (isset($this->status)) $this->status=trim($this->status);
		if (isset($this->description)) $this->description=trim($this->description);
		if (isset($this->type_seat)) $this->type_seat=trim($this->type_seat);
		if (isset($this->type_balance)) $this->type_balance=trim($this->type_balance);
		if (isset($this->debit_account)) $this->debit_account=trim($this->debit_account);
		if (isset($this->credit_account)) $this->credit_account=trim($this->credit_account);
		if (isset($this->currency)) $this->currency=trim($this->currency);
		if (isset($this->currency_value1)) $this->currency_value1=trim($this->currency_value1);
		if (isset($this->currency_value2)) $this->currency_value2=trim($this->currency_value2);
		if (isset($this->history)) $this->history=trim($this->history);
		if (isset($this->history_group)) $this->history_group=trim($this->history_group);
		if (isset($this->origin)) $this->origin=trim($this->origin);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."contab_standard_seat(";
		
		$sql.= "entity,";
		$sql.= "fk_point_entry,";
		$sql.= "sequence,";
		$sql.= "status,";
		$sql.= "description,";
		$sql.= "type_seat,";
		$sql.= "type_balance,";
		$sql.= "debit_account,";
		$sql.= "credit_account,";
		$sql.= "currency,";
		$sql.= "currency_value1,";
		$sql.= "currency_value2,";
		$sql.= "history,";
		$sql.= "history_group,";
		$sql.= "origin";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->entity)?'NULL':"'".$this->entity."'").",";
		$sql.= " ".(! isset($this->fk_point_entry)?'NULL':"'".$this->fk_point_entry."'").",";
		$sql.= " ".(! isset($this->sequence)?'NULL':"'".$this->sequence."'").",";
		$sql.= " ".(! isset($this->status)?'NULL':"'".$this->status."'").",";
		$sql.= " ".(! isset($this->description)?'NULL':"'".$this->db->escape($this->description)."'").",";
		$sql.= " ".(! isset($this->type_seat)?'NULL':"'".$this->type_seat."'").",";
		$sql.= " ".(! isset($this->type_balance)?'NULL':"'".$this->type_balance."'").",";
		$sql.= " ".(! isset($this->debit_account)?'NULL':"'".$this->db->escape($this->debit_account)."'").",";
		$sql.= " ".(! isset($this->credit_account)?'NULL':"'".$this->db->escape($this->credit_account)."'").",";
		$sql.= " ".(! isset($this->currency)?'NULL':"'".$this->db->escape($this->currency)."'").",";
		$sql.= " ".(! isset($this->currency_value1)?'NULL':"'".$this->db->escape($this->currency_value1)."'").",";
		$sql.= " ".(! isset($this->currency_value2)?'NULL':"'".$this->db->escape($this->currency_value2)."'").",";
		$sql.= " ".(! isset($this->history)?'NULL':"'".$this->db->escape($this->history)."'").",";
		$sql.= " ".(! isset($this->history_group)?'NULL':"'".$this->db->escape($this->history_group)."'").",";
		$sql.= " ".(! isset($this->origin)?'NULL':"'".$this->db->escape($this->origin)."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."contab_standard_seat");

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
		$sql.= " t.fk_point_entry,";
		$sql.= " t.sequence,";
		$sql.= " t.status,";
		$sql.= " t.description,";
		$sql.= " t.type_seat,";
		$sql.= " t.type_balance,";
		$sql.= " t.debit_account,";
		$sql.= " t.credit_account,";
		$sql.= " t.currency,";
		$sql.= " t.currency_value1,";
		$sql.= " t.currency_value2,";
		$sql.= " t.history,";
		$sql.= " t.history_group,";
		$sql.= " t.origin";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."contab_standard_seat as t";
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
				$this->fk_point_entry = $obj->fk_point_entry;
				$this->sequence = $obj->sequence;
				$this->status = $obj->status;
				$this->description = $obj->description;
				$this->type_seat = $obj->type_seat;
				$this->type_balance = $obj->type_balance;
				$this->debit_account = $obj->debit_account;
				$this->credit_account = $obj->credit_account;
				$this->currency = $obj->currency;
				$this->currency_value1 = $obj->currency_value1;
				$this->currency_value2 = $obj->currency_value2;
				$this->history = $obj->history;
				$this->history_group = $obj->history_group;
				$this->origin = $obj->origin;

                
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
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetch_max_sequence($id)
    {
      global $langs,$conf;
        $sql = "SELECT";
	$sql.= " t.entity, ";
	$sql.= " MAX(t.sequence) AS sequence ";
		
        $sql.= " FROM ".MAIN_DB_PREFIX."contab_standard_seat as t";
        $sql.= " WHERE t.fk_point_entry = ".$id;
	$sql.= " AND t.entity = ".$conf->entity;
	$sql.= " GROUP BY t.entity ";

    	dol_syslog(get_class($this)."::fetch_max_sequence sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                return $obj->sequence;
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
		if (isset($this->fk_point_entry)) $this->fk_point_entry=trim($this->fk_point_entry);
		if (isset($this->sequence)) $this->sequence=trim($this->sequence);
		if (isset($this->status)) $this->status=trim($this->status);
		if (isset($this->description)) $this->description=trim($this->description);
		if (isset($this->type_seat)) $this->type_seat=trim($this->type_seat);
		if (isset($this->type_balance)) $this->type_balance=trim($this->type_balance);
		if (isset($this->debit_account)) $this->debit_account=trim($this->debit_account);
		if (isset($this->credit_account)) $this->credit_account=trim($this->credit_account);
		if (isset($this->currency)) $this->currency=trim($this->currency);
		if (isset($this->currency_value1)) $this->currency_value1=trim($this->currency_value1);
		if (isset($this->currency_value2)) $this->currency_value2=trim($this->currency_value2);
		if (isset($this->history)) $this->history=trim($this->history);
		if (isset($this->history_group)) $this->history_group=trim($this->history_group);
		if (isset($this->origin)) $this->origin=trim($this->origin);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."contab_standard_seat SET";
        
		$sql.= " entity=".(isset($this->entity)?$this->entity:"null").",";
		$sql.= " fk_point_entry=".(isset($this->fk_point_entry)?$this->fk_point_entry:"null").",";
		$sql.= " sequence=".(isset($this->sequence)?$this->sequence:"null").",";
		$sql.= " status=".(isset($this->status)?$this->status:"null").",";
		$sql.= " description=".(isset($this->description)?"'".$this->db->escape($this->description)."'":"null").",";
		$sql.= " type_seat=".(isset($this->type_seat)?$this->type_seat:"null").",";
		$sql.= " type_balance=".(isset($this->type_balance)?$this->type_balance:"null").",";
		$sql.= " debit_account=".(isset($this->debit_account)?"'".$this->db->escape($this->debit_account)."'":"null").",";
		$sql.= " credit_account=".(isset($this->credit_account)?"'".$this->db->escape($this->credit_account)."'":"null").",";
		$sql.= " currency=".(isset($this->currency)?"'".$this->db->escape($this->currency)."'":"null").",";
		$sql.= " currency_value1=".(isset($this->currency_value1)?"'".$this->db->escape($this->currency_value1)."'":"null").",";
		$sql.= " currency_value2=".(isset($this->currency_value2)?"'".$this->db->escape($this->currency_value2)."'":"null").",";
		$sql.= " history=".(isset($this->history)?"'".$this->db->escape($this->history)."'":"null").",";
		$sql.= " history_group=".(isset($this->history_group)?"'".$this->db->escape($this->history_group)."'":"null").",";
		$sql.= " origin=".(isset($this->origin)?"'".$this->db->escape($this->origin)."'":"null")."";

        
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."contab_standard_seat";
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

		$object=new Contabstandardseat($this->db);

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
		$this->fk_point_entry='';
		$this->sequence='';
		$this->status='';
		$this->description='';
		$this->type_seat='';
		$this->type_balance='';
		$this->debit_account='';
		$this->credit_account='';
		$this->currency='';
		$this->currency_value1='';
		$this->currency_value2='';
		$this->history='';
		$this->history_group='';
		$this->origin='';

		
	}

}
?>
