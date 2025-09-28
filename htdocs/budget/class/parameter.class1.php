<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2014	   Juanjo Menent		<jmenent@2byte.es>
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
 *  \file       dev/skeletons/parameter.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2015-10-05 12:06
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Parameter extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='parameter';			//!< Id that identify managed objects
	var $table_element='parameter';		//!< Name of table without prefix where object is stored

    var $id;
    
	var $entity;
	var $fk_user_create;
	var $fk_user_mod;
	var $fk_city;
	var $social_benefit;
	var $tax_labor;
	var $tools;
	var $overhead;
	var $utility;
	var $tax_transaction;
	var $exchange_rate;
	var $decimal_number;
	var $global_item;
	var $date_create='';
	var $date_delete='';
	var $tms='';
	var $by_default;
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
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->fk_user_mod)) $this->fk_user_mod=trim($this->fk_user_mod);
		if (isset($this->fk_city)) $this->fk_city=trim($this->fk_city);
		if (isset($this->social_benefit)) $this->social_benefit=trim($this->social_benefit);
		if (isset($this->tax_labor)) $this->tax_labor=trim($this->tax_labor);
		if (isset($this->tools)) $this->tools=trim($this->tools);
		if (isset($this->overhead)) $this->overhead=trim($this->overhead);
		if (isset($this->utility)) $this->utility=trim($this->utility);
		if (isset($this->tax_transaction)) $this->tax_transaction=trim($this->tax_transaction);
		if (isset($this->exchange_rate)) $this->exchange_rate=trim($this->exchange_rate);
		if (isset($this->decimal_number)) $this->decimal_number=trim($this->decimal_number);
		if (isset($this->global_item)) $this->global_item=trim($this->global_item);
		if (isset($this->by_default)) $this->by_default=trim($this->by_default);
		if (isset($this->statut)) $this->statut=trim($this->statut);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX.$this->table_element."(";
		
		$sql.= "entity,";
		$sql.= "fk_user_create,";
		$sql.= "fk_user_mod,";
		$sql.= "fk_city,";
		$sql.= "social_benefit,";
		$sql.= "tax_labor,";
		$sql.= "tools,";
		$sql.= "overhead,";
		$sql.= "utility,";
		$sql.= "tax_transaction,";
		$sql.= "exchange_rate,";
		$sql.= "decimal_number,";
		$sql.= "global_item,";
		$sql.= "date_create,";
		$sql.= "date_delete,";
		$sql.= "by_default,";
		$sql.= "statut";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->entity)?'NULL':"'".$this->entity."'").",";
		$sql.= " ".(! isset($this->fk_user_create)?'NULL':"'".$this->fk_user_create."'").",";
		$sql.= " ".(! isset($this->fk_user_mod)?'NULL':"'".$this->fk_user_mod."'").",";
		$sql.= " ".(! isset($this->fk_city)?'NULL':"'".$this->fk_city."'").",";
		$sql.= " ".(! isset($this->social_benefit)?'NULL':"'".$this->social_benefit."'").",";
		$sql.= " ".(! isset($this->tax_labor)?'NULL':"'".$this->tax_labor."'").",";
		$sql.= " ".(! isset($this->tools)?'NULL':"'".$this->tools."'").",";
		$sql.= " ".(! isset($this->overhead)?'NULL':"'".$this->overhead."'").",";
		$sql.= " ".(! isset($this->utility)?'NULL':"'".$this->utility."'").",";
		$sql.= " ".(! isset($this->tax_transaction)?'NULL':"'".$this->tax_transaction."'").",";
		$sql.= " ".(! isset($this->exchange_rate)?'NULL':"'".$this->exchange_rate."'").",";
		$sql.= " ".(! isset($this->decimal_number)?'NULL':"'".$this->decimal_number."'").",";
		$sql.= " ".(! isset($this->global_item)?'NULL':"'".$this->global_item."'").",";
		$sql.= " ".(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':"'".$this->db->idate($this->date_create)."'").",";
		$sql.= " ".(! isset($this->date_delete) || dol_strlen($this->date_delete)==0?'NULL':"'".$this->db->idate($this->date_delete)."'").",";
		$sql.= " ".(! isset($this->by_default)?'NULL':"'".$this->db->escape($this->by_default)."'").",";
		$sql.= " ".(! isset($this->statut)?'NULL':"'".$this->statut."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(__METHOD__, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX.$this->table_element);

			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action calls a trigger.

	            //// Call triggers
	            //$result=$this->call_trigger('MYOBJECT_CREATE',$user);
	            //if ($result < 0) { $error++; //Do also what you must do to rollback action if trigger fail}
	            //// End call triggers
			}
        }

        // Commit or rollback
        if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(__METHOD__." ".$errmsg, LOG_ERR);
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
     *  @param	int		$id    	Id object
     *  @param	string	$ref	Ref
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetch($id,$user,$bydefault='')
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.entity,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.fk_user_mod,";
		$sql.= " t.fk_city,";
		$sql.= " t.social_benefit,";
		$sql.= " t.tax_labor,";
		$sql.= " t.tools,";
		$sql.= " t.overhead,";
		$sql.= " t.utility,";
		$sql.= " t.tax_transaction,";
		$sql.= " t.exchange_rate,";
		$sql.= " t.decimal_number,";
		$sql.= " t.global_item,";
		$sql.= " t.date_create,";
		$sql.= " t.date_delete,";
		$sql.= " t.tms,";
		$sql.= " t.by_default,";
		$sql.= " t.statut";

		
        $sql.= " FROM ".MAIN_DB_PREFIX.$this->table_element." as t";
        if($id) $sql.= " WHERE t.rowid = ".$id;
	elseif(!empty($bydefault)) $sql.= " WHERE t.by_default = '1'";
        elseif ($user->admin) $sql.= " WHERE t.by_default = '1'";
	else $sql.= " WHERE t.fk_user_create = ".$user->id;
    	dol_syslog(get_class($this)."::fetch");
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;
                
				$this->entity = $obj->entity;
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_mod = $obj->fk_user_mod;
				$this->fk_city = $obj->fk_city;
				$this->social_benefit = $obj->social_benefit;
				$this->tax_labor = $obj->tax_labor;
				$this->tools = $obj->tools;
				$this->overhead = $obj->overhead;
				$this->utility = $obj->utility;
				$this->tax_transaction = $obj->tax_transaction;
				$this->exchange_rate = $obj->exchange_rate;
				$this->decimal_number = $obj->decimal_number;
				$this->global_item = $obj->global_item;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->date_delete = $this->db->jdate($obj->date_delete);
				$this->tms = $this->db->jdate($obj->tms);
				$this->by_default = $obj->by_default;
				$this->statut = $obj->statut;

                
            }
            $this->db->free($resql);

            return 1;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
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
    function update($user, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;

		// Clean parameters
        
		if (isset($this->entity)) $this->entity=trim($this->entity);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->fk_user_mod)) $this->fk_user_mod=trim($this->fk_user_mod);
		if (isset($this->fk_city)) $this->fk_city=trim($this->fk_city);
		if (isset($this->social_benefit)) $this->social_benefit=trim($this->social_benefit);
		if (isset($this->tax_labor)) $this->tax_labor=trim($this->tax_labor);
		if (isset($this->tools)) $this->tools=trim($this->tools);
		if (isset($this->overhead)) $this->overhead=trim($this->overhead);
		if (isset($this->utility)) $this->utility=trim($this->utility);
		if (isset($this->tax_transaction)) $this->tax_transaction=trim($this->tax_transaction);
		if (isset($this->exchange_rate)) $this->exchange_rate=trim($this->exchange_rate);
		if (isset($this->decimal_number)) $this->decimal_number=trim($this->decimal_number);
		if (isset($this->global_item)) $this->global_item=trim($this->global_item);
		if (isset($this->by_default)) $this->by_default=trim($this->by_default);
		if (isset($this->statut)) $this->statut=trim($this->statut);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX.$this->table_element." SET";
        
		$sql.= " entity=".(isset($this->entity)?$this->entity:"null").",";
		$sql.= " fk_user_create=".(isset($this->fk_user_create)?$this->fk_user_create:"null").",";
		$sql.= " fk_user_mod=".(isset($this->fk_user_mod)?$this->fk_user_mod:"null").",";
		$sql.= " fk_city=".(isset($this->fk_city)?$this->fk_city:"null").",";
		$sql.= " social_benefit=".(isset($this->social_benefit)?$this->social_benefit:"null").",";
		$sql.= " tax_labor=".(isset($this->tax_labor)?$this->tax_labor:"null").",";
		$sql.= " tools=".(isset($this->tools)?$this->tools:"null").",";
		$sql.= " overhead=".(isset($this->overhead)?$this->overhead:"null").",";
		$sql.= " utility=".(isset($this->utility)?$this->utility:"null").",";
		$sql.= " tax_transaction=".(isset($this->tax_transaction)?$this->tax_transaction:"null").",";
		$sql.= " exchange_rate=".(isset($this->exchange_rate)?$this->exchange_rate:"null").",";
		$sql.= " decimal_number=".(isset($this->decimal_number)?$this->decimal_number:"null").",";
		$sql.= " global_item=".(isset($this->global_item)?$this->global_item:"null").",";
		$sql.= " date_create=".(dol_strlen($this->date_create)!=0 ? "'".$this->db->idate($this->date_create)."'" : 'null').",";
		$sql.= " date_delete=".(dol_strlen($this->date_delete)!=0 ? "'".$this->db->idate($this->date_delete)."'" : 'null').",";
		$sql.= " tms=".(dol_strlen($this->tms)!=0 ? "'".$this->db->idate($this->tms)."'" : 'null').",";
		$sql.= " by_default=".(isset($this->by_default)?"'".$this->db->escape($this->by_default)."'":"null").",";
		$sql.= " statut=".(isset($this->statut)?$this->statut:"null")."";

        
		$sql.= " WHERE rowid=".$this->id;

		$this->db->begin();

		dol_syslog(__METHOD__);
        $resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
		{
			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action calls a trigger.

	            //// Call triggers
	            //$result=$this->call_trigger('MYOBJECT_MODIFY',$user);
	            //if ($result < 0) { $error++; //Do also what you must do to rollback action if trigger fail}
	            //// End call triggers
			 }
		}

        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(__METHOD__." ".$errmsg, LOG_ERR);
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
	            //$result=$this->call_trigger('MYOBJECT_DELETE',$user);
	            //if ($result < 0) { $error++; //Do also what you must do to rollback action if trigger fail}
	            //// End call triggers
			}
		}

		if (! $error)
		{
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX.$this->table_element;
    		$sql.= " WHERE rowid=".$this->id;

    		dol_syslog(__METHOD__);
    		$resql = $this->db->query($sql);
        	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
		}

        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(__METHOD__." ".$errmsg, LOG_ERR);
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

		$object=new Parameter($this->db);

		$this->db->begin();

		// Load source object
		$object->fetch($fromid);
		$object->id=0;
		$object->fk_user_create = $user->id;
		$object->statut=1;

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
		$this->fk_user_create='';
		$this->fk_user_mod='';
		$this->fk_city='';
		$this->social_benefit='';
		$this->tax_labor='';
		$this->tools='';
		$this->overhead='';
		$this->utility='';
		$this->tax_transaction='';
		$this->exchange_rate='';
		$this->decimal_number='';
		$this->global_item='';
		$this->date_create='';
		$this->date_delete='';
		$this->tms='';
		$this->by_default='';
		$this->statut='';

		
	}

}
