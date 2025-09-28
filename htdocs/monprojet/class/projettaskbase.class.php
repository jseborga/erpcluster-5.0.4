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
 *  \file       dev/skeletons/projettaskbase.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2015-09-25 11:50
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Projettaskbase extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='projet_task_base';			//!< Id that identify managed objects
	var $table_element='projet_task_base';		//!< Name of table without prefix where object is stored

    var $id;
    
	var $ref;
	var $entity;
	var $fk_projet;
	var $fk_projet_task;
	var $fk_task_parent;
	var $datec='';
	var $tms='';
	var $dateo='';
	var $datee='';
	var $datev='';
	var $label;
	var $description;
	var $duration_effective;
	var $planned_workload;
	var $progress;
	var $priority;
	var $fk_user_creat;
	var $fk_user_valid;
	var $fk_statut;
	var $note_private;
	var $note_public;
	var $rang;
	var $model_pdf;
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
        
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->entity)) $this->entity=trim($this->entity);
		if (isset($this->fk_projet)) $this->fk_projet=trim($this->fk_projet);
		if (isset($this->fk_projet_task)) $this->fk_projet_task=trim($this->fk_projet_task);
		if (isset($this->fk_task_parent)) $this->fk_task_parent=trim($this->fk_task_parent);
		if (isset($this->label)) $this->label=trim($this->label);
		if (isset($this->description)) $this->description=trim($this->description);
		if (isset($this->duration_effective)) $this->duration_effective=trim($this->duration_effective);
		if (isset($this->planned_workload)) $this->planned_workload=trim($this->planned_workload);
		if (isset($this->progress)) $this->progress=trim($this->progress);
		if (isset($this->priority)) $this->priority=trim($this->priority);
		if (isset($this->fk_user_creat)) $this->fk_user_creat=trim($this->fk_user_creat);
		if (isset($this->fk_user_valid)) $this->fk_user_valid=trim($this->fk_user_valid);
		if (isset($this->fk_statut)) $this->fk_statut=trim($this->fk_statut);
		if (isset($this->note_private)) $this->note_private=trim($this->note_private);
		if (isset($this->note_public)) $this->note_public=trim($this->note_public);
		if (isset($this->rang)) $this->rang=trim($this->rang);
		if (isset($this->model_pdf)) $this->model_pdf=trim($this->model_pdf);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX.$this->table_element."(";
		
		$sql.= "ref,";
		$sql.= "entity,";
		$sql.= "fk_projet,";
		$sql.= "fk_projet_task,";
		$sql.= "fk_task_parent,";
		$sql.= "datec,";
		$sql.= "dateo,";
		$sql.= "datee,";
		$sql.= "datev,";
		$sql.= "label,";
		$sql.= "description,";
		$sql.= "duration_effective,";
		$sql.= "planned_workload,";
		$sql.= "progress,";
		$sql.= "priority,";
		$sql.= "fk_user_creat,";
		$sql.= "fk_user_valid,";
		$sql.= "fk_statut,";
		$sql.= "note_private,";
		$sql.= "note_public,";
		$sql.= "rang,";
		$sql.= "model_pdf";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").",";
		$sql.= " ".(! isset($this->entity)?'NULL':"'".$this->entity."'").",";
		$sql.= " ".(! isset($this->fk_projet)?'NULL':"'".$this->fk_projet."'").",";
		$sql.= " ".(! isset($this->fk_projet_task)?'NULL':"'".$this->fk_projet_task."'").",";
		$sql.= " ".(! isset($this->fk_task_parent)?'NULL':"'".$this->fk_task_parent."'").",";
		$sql.= " ".(! isset($this->datec) || dol_strlen($this->datec)==0?'NULL':"'".$this->db->idate($this->datec)."'").",";
		$sql.= " ".(! isset($this->dateo) || dol_strlen($this->dateo)==0?'NULL':"'".$this->db->idate($this->dateo)."'").",";
		$sql.= " ".(! isset($this->datee) || dol_strlen($this->datee)==0?'NULL':"'".$this->db->idate($this->datee)."'").",";
		$sql.= " ".(! isset($this->datev) || dol_strlen($this->datev)==0?'NULL':"'".$this->db->idate($this->datev)."'").",";
		$sql.= " ".(! isset($this->label)?'NULL':"'".$this->db->escape($this->label)."'").",";
		$sql.= " ".(! isset($this->description)?'NULL':"'".$this->db->escape($this->description)."'").",";
		$sql.= " ".(! isset($this->duration_effective)?'NULL':"'".$this->duration_effective."'").",";
		$sql.= " ".(! isset($this->planned_workload)?'NULL':"'".$this->planned_workload."'").",";
		$sql.= " ".(! isset($this->progress)?'NULL':"'".$this->progress."'").",";
		$sql.= " ".(! isset($this->priority)?'NULL':"'".$this->priority."'").",";
		$sql.= " ".(! isset($this->fk_user_creat)?'NULL':"'".$this->fk_user_creat."'").",";
		$sql.= " ".(! isset($this->fk_user_valid)?'NULL':"'".$this->fk_user_valid."'").",";
		$sql.= " ".(! isset($this->fk_statut)?'NULL':"'".$this->fk_statut."'").",";
		$sql.= " ".(! isset($this->note_private)?'NULL':"'".$this->db->escape($this->note_private)."'").",";
		$sql.= " ".(! isset($this->note_public)?'NULL':"'".$this->db->escape($this->note_public)."'").",";
		$sql.= " ".(! isset($this->rang)?'NULL':"'".$this->rang."'").",";
		$sql.= " ".(! isset($this->model_pdf)?'NULL':"'".$this->db->escape($this->model_pdf)."'")."";

        
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
    function fetch($id,$fk_projet_task='')
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.ref,";
		$sql.= " t.entity,";
		$sql.= " t.fk_projet,";
		$sql.= " t.fk_projet_task,";
		$sql.= " t.fk_task_parent,";
		$sql.= " t.datec,";
		$sql.= " t.tms,";
		$sql.= " t.dateo,";
		$sql.= " t.datee,";
		$sql.= " t.datev,";
		$sql.= " t.label,";
		$sql.= " t.description,";
		$sql.= " t.duration_effective,";
		$sql.= " t.planned_workload,";
		$sql.= " t.progress,";
		$sql.= " t.priority,";
		$sql.= " t.fk_user_creat,";
		$sql.= " t.fk_user_valid,";
		$sql.= " t.fk_statut,";
		$sql.= " t.note_private,";
		$sql.= " t.note_public,";
		$sql.= " t.rang,";
		$sql.= " t.model_pdf";

		
        $sql.= " FROM ".MAIN_DB_PREFIX.$this->table_element." as t";
        if ($fk_projet_task) $sql.= " WHERE t.fk_projet_task = ".$fk_projet_task;
        else $sql.= " WHERE t.rowid = ".$id;

    	dol_syslog(get_class($this)."::fetch");
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;
                
				$this->ref = $obj->ref;
				$this->entity = $obj->entity;
				$this->fk_projet = $obj->fk_projet;
				$this->fk_projet_task = $obj->fk_projet_task;
				$this->fk_task_parent = $obj->fk_task_parent;
				$this->datec = $this->db->jdate($obj->datec);
				$this->tms = $this->db->jdate($obj->tms);
				$this->dateo = $this->db->jdate($obj->dateo);
				$this->datee = $this->db->jdate($obj->datee);
				$this->datev = $this->db->jdate($obj->datev);
				$this->label = $obj->label;
				$this->description = $obj->description;
				$this->duration_effective = $obj->duration_effective;
				$this->planned_workload = $obj->planned_workload;
				$this->progress = $obj->progress;
				$this->priority = $obj->priority;
				$this->fk_user_creat = $obj->fk_user_creat;
				$this->fk_user_valid = $obj->fk_user_valid;
				$this->fk_statut = $obj->fk_statut;
				$this->note_private = $obj->note_private;
				$this->note_public = $obj->note_public;
				$this->rang = $obj->rang;
				$this->model_pdf = $obj->model_pdf;

                
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
        
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->entity)) $this->entity=trim($this->entity);
		if (isset($this->fk_projet)) $this->fk_projet=trim($this->fk_projet);
		if (isset($this->fk_projet_task)) $this->fk_projet_task=trim($this->fk_projet_task);
		if (isset($this->fk_task_parent)) $this->fk_task_parent=trim($this->fk_task_parent);
		if (isset($this->label)) $this->label=trim($this->label);
		if (isset($this->description)) $this->description=trim($this->description);
		if (isset($this->duration_effective)) $this->duration_effective=trim($this->duration_effective);
		if (isset($this->planned_workload)) $this->planned_workload=trim($this->planned_workload);
		if (isset($this->progress)) $this->progress=trim($this->progress);
		if (isset($this->priority)) $this->priority=trim($this->priority);
		if (isset($this->fk_user_creat)) $this->fk_user_creat=trim($this->fk_user_creat);
		if (isset($this->fk_user_valid)) $this->fk_user_valid=trim($this->fk_user_valid);
		if (isset($this->fk_statut)) $this->fk_statut=trim($this->fk_statut);
		if (isset($this->note_private)) $this->note_private=trim($this->note_private);
		if (isset($this->note_public)) $this->note_public=trim($this->note_public);
		if (isset($this->rang)) $this->rang=trim($this->rang);
		if (isset($this->model_pdf)) $this->model_pdf=trim($this->model_pdf);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX.$this->table_element." SET";
        
		$sql.= " ref=".(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").",";
		$sql.= " entity=".(isset($this->entity)?$this->entity:"null").",";
		$sql.= " fk_projet=".(isset($this->fk_projet)?$this->fk_projet:"null").",";
		$sql.= " fk_projet_task=".(isset($this->fk_projet_task)?$this->fk_projet_task:"null").",";
		$sql.= " fk_task_parent=".(isset($this->fk_task_parent)?$this->fk_task_parent:"null").",";
		$sql.= " datec=".(dol_strlen($this->datec)!=0 ? "'".$this->db->idate($this->datec)."'" : 'null').",";
		$sql.= " tms=".(dol_strlen($this->tms)!=0 ? "'".$this->db->idate($this->tms)."'" : 'null').",";
		$sql.= " dateo=".(dol_strlen($this->dateo)!=0 ? "'".$this->db->idate($this->dateo)."'" : 'null').",";
		$sql.= " datee=".(dol_strlen($this->datee)!=0 ? "'".$this->db->idate($this->datee)."'" : 'null').",";
		$sql.= " datev=".(dol_strlen($this->datev)!=0 ? "'".$this->db->idate($this->datev)."'" : 'null').",";
		$sql.= " label=".(isset($this->label)?"'".$this->db->escape($this->label)."'":"null").",";
		$sql.= " description=".(isset($this->description)?"'".$this->db->escape($this->description)."'":"null").",";
		$sql.= " duration_effective=".(isset($this->duration_effective)?$this->duration_effective:"null").",";
		$sql.= " planned_workload=".(isset($this->planned_workload)?$this->planned_workload:"null").",";
		$sql.= " progress=".(isset($this->progress)?$this->progress:"null").",";
		$sql.= " priority=".(isset($this->priority)?$this->priority:"null").",";
		$sql.= " fk_user_creat=".(isset($this->fk_user_creat)?$this->fk_user_creat:"null").",";
		$sql.= " fk_user_valid=".(isset($this->fk_user_valid)?$this->fk_user_valid:"null").",";
		$sql.= " fk_statut=".(isset($this->fk_statut)?$this->fk_statut:"null").",";
		$sql.= " note_private=".(isset($this->note_private)?"'".$this->db->escape($this->note_private)."'":"null").",";
		$sql.= " note_public=".(isset($this->note_public)?"'".$this->db->escape($this->note_public)."'":"null").",";
		$sql.= " rang=".(isset($this->rang)?$this->rang:"null").",";
		$sql.= " model_pdf=".(isset($this->model_pdf)?"'".$this->db->escape($this->model_pdf)."'":"null")."";

        
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

		$object=new Projettaskbase($this->db);

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
		
		$this->ref='';
		$this->entity='';
		$this->fk_projet='';
		$this->fk_projet_task='';
		$this->fk_task_parent='';
		$this->datec='';
		$this->tms='';
		$this->dateo='';
		$this->datee='';
		$this->datev='';
		$this->label='';
		$this->description='';
		$this->duration_effective='';
		$this->planned_workload='';
		$this->progress='';
		$this->priority='';
		$this->fk_user_creat='';
		$this->fk_user_valid='';
		$this->fk_statut='';
		$this->note_private='';
		$this->note_public='';
		$this->rang='';
		$this->model_pdf='';

		
	}

	//MODIFICADO
    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function getlisttask($id)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.ref,";
		$sql.= " t.entity,";
		$sql.= " t.fk_projet,";
		$sql.= " t.fk_task_parent,";
		$sql.= " t.datec,";
		$sql.= " t.tms,";
		$sql.= " t.dateo,";
		$sql.= " t.datee,";
		$sql.= " t.datev,";
		$sql.= " t.label,";
		$sql.= " t.description,";
		$sql.= " t.duration_effective,";
		$sql.= " t.planned_workload,";
		$sql.= " t.progress,";
		$sql.= " t.priority,";
		$sql.= " t.fk_user_creat,";
		$sql.= " t.fk_user_valid,";
		$sql.= " t.fk_statut,";
		$sql.= " t.note_private,";
		$sql.= " t.note_public,";
		$sql.= " t.rang,";
		$sql.= " t.model_pdf";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."projet_task as t";
        $sql.= " WHERE t.fk_projet = ".$id;

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
		  $objnew = new Projettaskbase($this->db);

		  $objnew->id    = $obj->rowid;
                
		  $objnew->ref = $obj->ref;
		  $objnew->entity = $obj->entity;
		  $objnew->fk_projet = $obj->fk_projet;
		  $objnew->fk_task_parent = $obj->fk_task_parent;
		  $objnew->datec = $this->db->jdate($obj->datec);
		  $objnew->tms = $this->db->jdate($obj->tms);
		  $objnew->dateo = $this->db->jdate($obj->dateo);
		  $objnew->datee = $this->db->jdate($obj->datee);
		  $objnew->datev = $this->db->jdate($obj->datev);
		  $objnew->label = $obj->label;
		  $objnew->description = $obj->description;
		  $objnew->duration_effective = $obj->duration_effective;
		  $objnew->planned_workload = $obj->planned_workload;
		  $objnew->progress = $obj->progress;
		  $objnew->priority = $obj->priority;
		  $objnew->fk_user_creat = $obj->fk_user_creat;
		  $objnew->fk_user_valid = $obj->fk_user_valid;
		  $objnew->fk_statut = $obj->fk_statut;
		  $objnew->note_private = $obj->note_private;
		  $objnew->note_public = $obj->note_public;
		  $objnew->rang = $obj->rang;
		  $objnew->model_pdf = $obj->model_pdf;
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
            dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
            return -1;
        }
    }
	
	
}
