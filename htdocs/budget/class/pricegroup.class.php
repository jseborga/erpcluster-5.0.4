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
 *  \file       dev/skeletons/pricegroup.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2015-10-05 22:04
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Pricegroup extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='price_group';			//!< Id that identify managed objects
	var $table_element='price_group';		//!< Name of table without prefix where object is stored

    var $id;
    
	var $entity;
	var $fk_father;
	var $fk_user_mod;
	var $fk_user_create;
	var $fk_category;
	var $ref;
	var $coderef;
	var $sequence;
	var $detail;
	var $detail_title;
	var $ref_name;
	var $percentage;
	var $operations;
	var $date_delete='';
	var $gestion;
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
        
		if (isset($this->entity)) $this->entity=trim($this->entity);
		if (isset($this->fk_father)) $this->fk_father=trim($this->fk_father);
		if (isset($this->fk_user_mod)) $this->fk_user_mod=trim($this->fk_user_mod);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->fk_category)) $this->fk_category=trim($this->fk_category);
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->coderef)) $this->coderef=trim($this->coderef);
		if (isset($this->sequence)) $this->sequence=trim($this->sequence);
		if (isset($this->detail)) $this->detail=trim($this->detail);
		if (isset($this->detail_title)) $this->detail_title=trim($this->detail_title);
		if (isset($this->ref_name)) $this->ref_name=trim($this->ref_name);
		if (isset($this->percentage)) $this->percentage=trim($this->percentage);
		if (isset($this->operations)) $this->operations=trim($this->operations);
		if (isset($this->gestion)) $this->gestion=trim($this->gestion);
		if (isset($this->statut)) $this->statut=trim($this->statut);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX.$this->table_element."(";
		
		$sql.= "entity,";
		$sql.= "fk_father,";
		$sql.= "fk_user_mod,";
		$sql.= "fk_user_create,";
		$sql.= "fk_category,";
		$sql.= "ref,";
		$sql.= "coderef,";
		$sql.= "sequence,";
		$sql.= "detail,";
		$sql.= "detail_title,";
		$sql.= "ref_name,";
		$sql.= "percentage,";
		$sql.= "operations,";
		$sql.= "date_delete,";
		$sql.= "gestion,";
		$sql.= "date_create,";
		$sql.= "statut";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->entity)?'NULL':"'".$this->entity."'").",";
		$sql.= " ".(! isset($this->fk_father)?'NULL':"'".$this->fk_father."'").",";
		$sql.= " ".(! isset($this->fk_user_mod)?'NULL':"'".$this->fk_user_mod."'").",";
		$sql.= " ".(! isset($this->fk_user_create)?'NULL':"'".$this->fk_user_create."'").",";
		$sql.= " ".(! isset($this->fk_category)?'NULL':"'".$this->fk_category."'").",";
		$sql.= " ".(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").",";
		$sql.= " ".(! isset($this->coderef)?'NULL':"'".$this->db->escape($this->coderef)."'").",";
		$sql.= " ".(! isset($this->sequence)?'NULL':"'".$this->sequence."'").",";
		$sql.= " ".(! isset($this->detail)?'NULL':"'".$this->db->escape($this->detail)."'").",";
		$sql.= " ".(! isset($this->detail_title)?'NULL':"'".$this->db->escape($this->detail_title)."'").",";
		$sql.= " ".(! isset($this->ref_name)?'NULL':"'".$this->db->escape($this->ref_name)."'").",";
		$sql.= " ".(! isset($this->percentage)?'NULL':"'".$this->percentage."'").",";
		$sql.= " ".(! isset($this->operations)?'NULL':"'".$this->db->escape($this->operations)."'").",";
		$sql.= " ".(! isset($this->date_delete) || dol_strlen($this->date_delete)==0?'NULL':"'".$this->db->idate($this->date_delete)."'").",";
		$sql.= " ".(! isset($this->gestion)?'NULL':"'".$this->gestion."'").",";
		$sql.= " ".(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':"'".$this->db->idate($this->date_create)."'").",";
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
    function fetch($id,$ref='')
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.entity,";
		$sql.= " t.fk_father,";
		$sql.= " t.fk_user_mod,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.fk_category,";
		$sql.= " t.ref,";
		$sql.= " t.coderef,";
		$sql.= " t.sequence,";
		$sql.= " t.detail,";
		$sql.= " t.detail_title,";
		$sql.= " t.ref_name,";
		$sql.= " t.percentage,";
		$sql.= " t.operations,";
		$sql.= " t.date_delete,";
		$sql.= " t.gestion,";
		$sql.= " t.date_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut";

		
        $sql.= " FROM ".MAIN_DB_PREFIX.$this->table_element." as t";
        if ($ref) $sql.= " WHERE t.ref = '".$ref."'";
        else $sql.= " WHERE t.rowid = ".$id;

    	dol_syslog(get_class($this)."::fetch");
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;
                
				$this->entity = $obj->entity;
				$this->fk_father = $obj->fk_father;
				$this->fk_user_mod = $obj->fk_user_mod;
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_category = $obj->fk_category;
				$this->ref = $obj->ref;
				$this->coderef = $obj->coderef;
				$this->sequence = $obj->sequence;
				$this->detail = $obj->detail;
				$this->detail_title = $obj->detail_title;
				$this->ref_name = $obj->ref_name;
				$this->percentage = $obj->percentage;
				$this->operations = $obj->operations;
				$this->date_delete = $this->db->jdate($obj->date_delete);
				$this->gestion = $obj->gestion;
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
		if (isset($this->fk_father)) $this->fk_father=trim($this->fk_father);
		if (isset($this->fk_user_mod)) $this->fk_user_mod=trim($this->fk_user_mod);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->fk_category)) $this->fk_category=trim($this->fk_category);
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->coderef)) $this->coderef=trim($this->coderef);
		if (isset($this->sequence)) $this->sequence=trim($this->sequence);
		if (isset($this->detail)) $this->detail=trim($this->detail);
		if (isset($this->detail_title)) $this->detail_title=trim($this->detail_title);
		if (isset($this->ref_name)) $this->ref_name=trim($this->ref_name);
		if (isset($this->percentage)) $this->percentage=trim($this->percentage);
		if (isset($this->operations)) $this->operations=trim($this->operations);
		if (isset($this->gestion)) $this->gestion=trim($this->gestion);
		if (isset($this->statut)) $this->statut=trim($this->statut);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX.$this->table_element." SET";
        
		$sql.= " entity=".(isset($this->entity)?$this->entity:"null").",";
		$sql.= " fk_father=".(isset($this->fk_father)?$this->fk_father:"null").",";
		$sql.= " fk_user_mod=".(isset($this->fk_user_mod)?$this->fk_user_mod:"null").",";
		$sql.= " fk_user_create=".(isset($this->fk_user_create)?$this->fk_user_create:"null").",";
		$sql.= " fk_category=".(isset($this->fk_category)?$this->fk_category:"null").",";
		$sql.= " ref=".(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").",";
		$sql.= " coderef=".(isset($this->coderef)?"'".$this->db->escape($this->coderef)."'":"null").",";
		$sql.= " sequence=".(isset($this->sequence)?$this->sequence:"null").",";
		$sql.= " detail=".(isset($this->detail)?"'".$this->db->escape($this->detail)."'":"null").",";
		$sql.= " detail_title=".(isset($this->detail_title)?"'".$this->db->escape($this->detail_title)."'":"null").",";
		$sql.= " ref_name=".(isset($this->ref_name)?"'".$this->db->escape($this->ref_name)."'":"null").",";
		$sql.= " percentage=".(isset($this->percentage)?$this->percentage:"null").",";
		$sql.= " operations=".(isset($this->operations)?"'".$this->db->escape($this->operations)."'":"null").",";
		$sql.= " date_delete=".(dol_strlen($this->date_delete)!=0 ? "'".$this->db->idate($this->date_delete)."'" : 'null').",";
		$sql.= " gestion=".(isset($this->gestion)?$this->gestion:"null").",";
		$sql.= " date_create=".(dol_strlen($this->date_create)!=0 ? "'".$this->db->idate($this->date_create)."'" : 'null').",";
		$sql.= " tms=".(dol_strlen($this->tms)!=0 ? "'".$this->db->idate($this->tms)."'" : 'null').",";
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

		$object=new Pricegroup($this->db);

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
		$this->fk_father='';
		$this->fk_user_mod='';
		$this->fk_user_create='';
		$this->fk_category='';
		$this->ref='';
		$this->coderef='';
		$this->sequence='';
		$this->detail='';
		$this->detail_title='';
		$this->ref_name='';
		$this->percentage='';
		$this->operations='';
		$this->date_delete='';
		$this->gestion='';
		$this->date_create='';
		$this->tms='';
		$this->statut='';

		
	}

	//MODIFICADO
    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    	Id object
     *  @param	string	$ref	Ref
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetch_cat($id)
    {
    	global $langs;
        $sql = "SELECT";
	$sql.= " t.rowid,";
	
	$sql.= " t.entity,";
	$sql.= " t.fk_father,";
	$sql.= " t.fk_user_mod,";
	$sql.= " t.fk_user_create,";
	$sql.= " t.fk_category,";
	$sql.= " t.ref,";
	$sql.= " t.coderef,";
	$sql.= " t.sequence,";
	$sql.= " t.detail,";
	$sql.= " t.detail_title,";
	$sql.= " t.ref_name,";
	$sql.= " t.percentage,";
	$sql.= " t.operations,";
	$sql.= " t.date_delete,";
	$sql.= " t.gestion,";
	$sql.= " t.date_create,";
	$sql.= " t.tms,";
	$sql.= " t.statut";
		
        $sql.= " FROM ".MAIN_DB_PREFIX.$this->table_element." as t";
	$sql.= " WHERE t.fk_category = ".$id;

    	dol_syslog(get_class($this)."::fetch_cat");
        $resql=$this->db->query($sql);
        if ($resql)
	  {
            if ($this->db->num_rows($resql))
	      {
                $obj = $this->db->fetch_object($resql);
		
                $this->id    = $obj->rowid;
                
		$this->entity = $obj->entity;
		$this->fk_father = $obj->fk_father;
		$this->fk_user_mod = $obj->fk_user_mod;
		$this->fk_user_create = $obj->fk_user_create;
		$this->fk_category = $obj->fk_category;
		$this->ref = $obj->ref;
		$this->coderef = $obj->coderef;
		$this->sequence = $obj->sequence;
		$this->detail = $obj->detail;
		$this->detail_title = $obj->detail_title;
		$this->ref_name = $obj->ref_name;
		$this->percentage = $obj->percentage;
		$this->operations = $obj->operations;
		$this->date_delete = $this->db->jdate($obj->date_delete);
		$this->gestion = $obj->gestion;
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
            return -1;
        }
    }
	
}
