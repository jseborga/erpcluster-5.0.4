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
 *  \file       dev/skeletons/psalarypresent.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2014-02-10 10:42
 */

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Psalarypresent // extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	//var $element='psalarypresent';			//!< Id that identify managed objects
	//var $table_element='psalarypresent';	//!< Name of table without prefix where object is stored

    var $id;
    
	var $entity;
	var $fk_salary_present;
	var $fk_proces;
	var $fk_type_fol;
	var $fk_concept;
	var $fk_period;
	var $fk_user;
	var $fk_cc;
	var $type;
	var $cuota;
	var $semana;
	var $amount_inf;
	var $amount;
	var $hours_info;
	var $hours;
	var $date_reg='';
	var $date_create='';
	var $fk_user_create;
	var $fk_account;
	var $payment_state;
	var $state;

    


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
		if (isset($this->fk_salary_present)) $this->fk_salary_present=trim($this->fk_salary_present);
		if (isset($this->fk_proces)) $this->fk_proces=trim($this->fk_proces);
		if (isset($this->fk_type_fol)) $this->fk_type_fol=trim($this->fk_type_fol);
		if (isset($this->fk_concept)) $this->fk_concept=trim($this->fk_concept);
		if (isset($this->fk_period)) $this->fk_period=trim($this->fk_period);
		if (isset($this->fk_user)) $this->fk_user=trim($this->fk_user);
		if (isset($this->fk_cc)) $this->fk_cc=trim($this->fk_cc);
		if (isset($this->type)) $this->type=trim($this->type);
		if (isset($this->cuota)) $this->cuota=trim($this->cuota);
		if (isset($this->semana)) $this->semana=trim($this->semana);
		if (isset($this->amount_inf)) $this->amount_inf=trim($this->amount_inf);
		if (isset($this->amount)) $this->amount=trim($this->amount);
		if (isset($this->hours_info)) $this->hours_info=trim($this->hours_info);
		if (isset($this->hours)) $this->hours=trim($this->hours);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->fk_account)) $this->fk_account=trim($this->fk_account);
		if (isset($this->payment_state)) $this->payment_state=trim($this->payment_state);
		if (isset($this->state)) $this->state=trim($this->state);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."p_salary_present(";
		
		$sql.= "entity,";
		$sql.= "fk_salary_present,";
		$sql.= "fk_proces,";
		$sql.= "fk_type_fol,";
		$sql.= "fk_concept,";
		$sql.= "fk_period,";
		$sql.= "fk_user,";
		$sql.= "fk_cc,";
		$sql.= "type,";
		$sql.= "cuota,";
		$sql.= "semana,";
		$sql.= "amount_inf,";
		$sql.= "amount,";
		$sql.= "hours_info,";
		$sql.= "hours,";
		$sql.= "date_reg,";
		$sql.= "date_create,";
		$sql.= "fk_user_create,";
		$sql.= "fk_account,";
		$sql.= "payment_state,";
		$sql.= "state";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->entity)?'NULL':"'".$this->entity."'").",";
		$sql.= " ".(! isset($this->fk_salary_present)?'NULL':"'".$this->fk_salary_present."'").",";
		$sql.= " ".(! isset($this->fk_proces)?'NULL':"'".$this->fk_proces."'").",";
		$sql.= " ".(! isset($this->fk_type_fol)?'NULL':"'".$this->fk_type_fol."'").",";
		$sql.= " ".(! isset($this->fk_concept)?'NULL':"'".$this->fk_concept."'").",";
		$sql.= " ".(! isset($this->fk_period)?'NULL':"'".$this->fk_period."'").",";
		$sql.= " ".(! isset($this->fk_user)?'NULL':"'".$this->fk_user."'").",";
		$sql.= " ".(! isset($this->fk_cc)?'NULL':"'".$this->fk_cc."'").",";
		$sql.= " ".(! isset($this->type)?'NULL':"'".$this->type."'").",";
		$sql.= " ".(! isset($this->cuota)?'NULL':"'".$this->cuota."'").",";
		$sql.= " ".(! isset($this->semana)?'NULL':"'".$this->semana."'").",";
		$sql.= " ".(! isset($this->amount_inf)?'NULL':"'".$this->amount_inf."'").",";
		$sql.= " ".(! isset($this->amount)?'NULL':"'".$this->amount."'").",";
		$sql.= " ".(! isset($this->hours_info)?'NULL':"'".$this->hours_info."'").",";
		$sql.= " ".(! isset($this->hours)?'NULL':"'".$this->hours."'").",";
		$sql.= " ".(! isset($this->date_reg) || dol_strlen($this->date_reg)==0?'NULL':$this->db->idate($this->date_reg)).",";
		$sql.= " ".(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':$this->db->idate($this->date_create)).",";
		$sql.= " ".(! isset($this->fk_user_create)?'NULL':"'".$this->fk_user_create."'").",";
		$sql.= " ".(! isset($this->fk_account)?'NULL':"'".$this->fk_account."'").",";
		$sql.= " ".(! isset($this->payment_state)?'NULL':"'".$this->payment_state."'").",";
		$sql.= " ".(! isset($this->state)?'NULL':"'".$this->state."'")."";

        
echo '<br>insert '.		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."p_salary_present");

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
		$sql.= " t.fk_salary_present,";
		$sql.= " t.fk_proces,";
		$sql.= " t.fk_type_fol,";
		$sql.= " t.fk_concept,";
		$sql.= " t.fk_period,";
		$sql.= " t.fk_user,";
		$sql.= " t.fk_cc,";
		$sql.= " t.type,";
		$sql.= " t.cuota,";
		$sql.= " t.semana,";
		$sql.= " t.amount_inf,";
		$sql.= " t.amount,";
		$sql.= " t.hours_info,";
		$sql.= " t.hours,";
		$sql.= " t.date_reg,";
		$sql.= " t.date_create,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.fk_account,";
		$sql.= " t.payment_state,";
		$sql.= " t.state";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."p_salary_present as t";
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
				$this->fk_salary_present = $obj->fk_salary_present;
				$this->fk_proces = $obj->fk_proces;
				$this->fk_type_fol = $obj->fk_type_fol;
				$this->fk_concept = $obj->fk_concept;
				$this->fk_period = $obj->fk_period;
				$this->fk_user = $obj->fk_user;
				$this->fk_cc = $obj->fk_cc;
				$this->type = $obj->type;
				$this->cuota = $obj->cuota;
				$this->semana = $obj->semana;
				$this->amount_inf = $obj->amount_inf;
				$this->amount = $obj->amount;
				$this->hours_info = $obj->hours_info;
				$this->hours = $obj->hours;
				$this->date_reg = $this->db->jdate($obj->date_reg);
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_account = $obj->fk_account;
				$this->payment_state = $obj->payment_state;
				$this->state = $obj->state;

                
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
		if (isset($this->fk_salary_present)) $this->fk_salary_present=trim($this->fk_salary_present);
		if (isset($this->fk_proces)) $this->fk_proces=trim($this->fk_proces);
		if (isset($this->fk_type_fol)) $this->fk_type_fol=trim($this->fk_type_fol);
		if (isset($this->fk_concept)) $this->fk_concept=trim($this->fk_concept);
		if (isset($this->fk_period)) $this->fk_period=trim($this->fk_period);
		if (isset($this->fk_user)) $this->fk_user=trim($this->fk_user);
		if (isset($this->fk_cc)) $this->fk_cc=trim($this->fk_cc);
		if (isset($this->type)) $this->type=trim($this->type);
		if (isset($this->cuota)) $this->cuota=trim($this->cuota);
		if (isset($this->semana)) $this->semana=trim($this->semana);
		if (isset($this->amount_inf)) $this->amount_inf=trim($this->amount_inf);
		if (isset($this->amount)) $this->amount=trim($this->amount);
		if (isset($this->hours_info)) $this->hours_info=trim($this->hours_info);
		if (isset($this->hours)) $this->hours=trim($this->hours);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->fk_account)) $this->fk_account=trim($this->fk_account);
		if (isset($this->payment_state)) $this->payment_state=trim($this->payment_state);
		if (isset($this->state)) $this->state=trim($this->state);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."p_salary_present SET";
        
		$sql.= " entity=".(isset($this->entity)?$this->entity:"null").",";
		$sql.= " fk_salary_present=".(isset($this->fk_salary_present)?$this->fk_salary_present:"null").",";
		$sql.= " fk_proces=".(isset($this->fk_proces)?$this->fk_proces:"null").",";
		$sql.= " fk_type_fol=".(isset($this->fk_type_fol)?$this->fk_type_fol:"null").",";
		$sql.= " fk_concept=".(isset($this->fk_concept)?$this->fk_concept:"null").",";
		$sql.= " fk_period=".(isset($this->fk_period)?$this->fk_period:"null").",";
		$sql.= " fk_user=".(isset($this->fk_user)?$this->fk_user:"null").",";
		$sql.= " fk_cc=".(isset($this->fk_cc)?$this->fk_cc:"null").",";
		$sql.= " type=".(isset($this->type)?$this->type:"null").",";
		$sql.= " cuota=".(isset($this->cuota)?$this->cuota:"null").",";
		$sql.= " semana=".(isset($this->semana)?$this->semana:"null").",";
		$sql.= " amount_inf=".(isset($this->amount_inf)?$this->amount_inf:"null").",";
		$sql.= " amount=".(isset($this->amount)?$this->amount:"null").",";
		$sql.= " hours_info=".(isset($this->hours_info)?$this->hours_info:"null").",";
		$sql.= " hours=".(isset($this->hours)?$this->hours:"null").",";
		$sql.= " date_reg=".(dol_strlen($this->date_reg)!=0 ? "'".$this->db->idate($this->date_reg)."'" : 'null').",";
		$sql.= " date_create=".(dol_strlen($this->date_create)!=0 ? "'".$this->db->idate($this->date_create)."'" : 'null').",";
		$sql.= " fk_user_create=".(isset($this->fk_user_create)?$this->fk_user_create:"null").",";
		$sql.= " fk_account=".(isset($this->fk_account)?$this->fk_account:"null").",";
		$sql.= " payment_state=".(isset($this->payment_state)?$this->payment_state:"null").",";
		$sql.= " state=".(isset($this->state)?$this->state:"null")."";

        
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."p_salary_present";
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

		$object=new Psalarypresent($this->db);

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
		$this->fk_salary_present='';
		$this->fk_proces='';
		$this->fk_type_fol='';
		$this->fk_concept='';
		$this->fk_period='';
		$this->fk_user='';
		$this->fk_cc='';
		$this->type='';
		$this->cuota='';
		$this->semana='';
		$this->amount_inf='';
		$this->amount='';
		$this->hours_info='';
		$this->hours='';
		$this->date_reg='';
		$this->date_create='';
		$this->fk_user_create='';
		$this->fk_account='';
		$this->payment_state='';
		$this->state='';

		
	}
	//MODIFICACIONES
    /**
     *  Load object in memory from the database
     *
     *  @param	int 		$user    Id user
     *  @param  int 		$fk_period    Id period
     *  @param  int 		$fk_proces    Id proces
     *  @param  int 		$fk_type_fol  Id type fol
     *  @param  int 		$fk_concept   Id concept

     *  @return int          	<0 if KO, >0 if OK
     */
	function fetch_salary_p($fk_user,$fk_period,$fk_proces,$fk_type_fol,$fk_concept,$state)
	{
	  global $langs;

	  $sql = "SELECT";
	  $sql.= " t.rowid,";
	  
	  $sql.= " t.entity,";
	  $sql.= " t.fk_proces,";
	  $sql.= " t.fk_type_fol,";
	  $sql.= " t.fk_concept,";
	  $sql.= " t.fk_period,";
	  $sql.= " t.fk_user,";
	  $sql.= " t.fk_cc,";
	  $sql.= " t.fk_account,";
	  $sql.= " t.type,";
	  $sql.= " t.cuota,";
	  $sql.= " t.semana,";
	  $sql.= " t.amount_inf,";
	  $sql.= " t.amount,";
	  $sql.= " t.hours_info,";
	  $sql.= " t.hours,";
	  $sql.= " t.date_reg,";
	  $sql.= " t.date_create,";
	  $sql.= " t.fk_user_create,";
	  $sql.= " t.state";
	  
	  
	  $sql.= " FROM ".MAIN_DB_PREFIX."p_salary_present as t";
	  $sql.= " WHERE t.fk_user = ".$fk_user;
	  $sql.= " AND t.fk_period = ".$fk_period;
	  $sql.= " AND t.fk_proces = ".$fk_proces;
	  $sql.= " AND t.fk_type_fol = ".$fk_type_fol;
	  $sql.= " AND t.fk_concept = ".$fk_concept;
	  //$sql.= " AND t.state <> ".$state; //revisar el state
	  
	  dol_syslog(get_class($this)."::fetch_salary_p sql=".$sql, LOG_DEBUG);
	  $resql=$this->db->query($sql);
	  if ($resql)
	    {
	      if ($this->db->num_rows($resql))
		{
		  $obj = $this->db->fetch_object($resql);
		  
		  $this->id    = $obj->rowid;
		  
		  $this->entity = $obj->entity;
		  $this->fk_proces = $obj->fk_proces;
		  $this->fk_type_fol = $obj->fk_type_fol;
		  $this->fk_concept = $obj->fk_concept;
		  $this->fk_period = $obj->fk_period;
		  $this->fk_user = $obj->fk_user;
		  $this->fk_cc = $obj->fk_cc;
		  $this->fk_account = $obj->fk_account;
		  $this->type = $obj->type;
		  $this->cuota = $obj->cuota;
		  $this->semana = $obj->semana;
		  $this->amount_inf = $obj->amount_inf;
		  $this->amount = $obj->amount;
		  $this->hours_info = $obj->hours_info;
		  $this->hours = $obj->hours;
		  $this->date_reg = $this->db->jdate($obj->date_reg);
		  $this->date_create = $this->db->jdate($obj->date_create);
		  $this->fk_user_create = $obj->fk_user_create;
		  $this->state = $obj->state;
		}
	      else
		{
		  $this->db->free($resql);
		  return 0;
		}
	      $this->db->free($resql);
	      return 1;
	    }
	  else
	    {
	      $this->error="Error ".$this->db->lasterror();
	      dol_syslog(get_class($this)."::fetch_salary_p ".$this->error, LOG_ERR);
	      return -1;
	    }
	}

}?>
