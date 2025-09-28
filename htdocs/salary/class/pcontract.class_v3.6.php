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
 *  \file       dev/skeletons/pcontract.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2014-02-10 10:34
 */

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Pcontract // extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='pcontract';			//!< Id that identify managed objects
	var $table_element='p_contract';	//!< Name of table without prefix where object is stored

    var $id;
    
	var $ref;
	var $fk_user;
	var $fk_departament;
	var $fk_charge;
	var $fk_regional;
	var $fk_proces;
	var $fk_cc;
	var $fk_account;
	var $date_ini='';
	var $date_fin='';
	var $basic;
	var $basic_fixed;
	var $nivel;
	var $bonus_old;
	var $hours;
	var $nua_afp;
	var $afp;
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
        
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->fk_user)) $this->fk_user=trim($this->fk_user);
		if (isset($this->fk_departament)) $this->fk_departament=trim($this->fk_departament);
		if (isset($this->fk_charge)) $this->fk_charge=trim($this->fk_charge);
		if (isset($this->fk_regional)) $this->fk_regional=trim($this->fk_regional);
		if (isset($this->fk_proces)) $this->fk_proces=trim($this->fk_proces);
		if (isset($this->fk_cc)) $this->fk_cc=trim($this->fk_cc);
		if (isset($this->fk_account)) $this->fk_account=trim($this->fk_account);
		if (isset($this->basic)) $this->basic=trim($this->basic);
		if (isset($this->basic_fixed)) $this->basic_fixed=trim($this->basic_fixed);
		if (isset($this->nivel)) $this->nivel=trim($this->nivel);
		if (isset($this->bonus_old)) $this->bonus_old=trim($this->bonus_old);
		if (isset($this->hours)) $this->hours=trim($this->hours);
		if (isset($this->nua_afp)) $this->nua_afp=trim($this->nua_afp);
		if (isset($this->afp)) $this->afp=trim($this->afp);
		if (isset($this->state)) $this->state=trim($this->state);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."p_contract(";
		
		$sql.= "ref,";
		$sql.= "fk_user,";
		$sql.= "fk_departament,";
		$sql.= "fk_charge,";
		$sql.= "fk_regional,";
		$sql.= "fk_proces,";
		$sql.= "fk_cc,";
		$sql.= "fk_account,";
		$sql.= "date_ini,";
		$sql.= "date_fin,";
		$sql.= "basic,";
		$sql.= "basic_fixed,";
		$sql.= "nivel,";
		$sql.= "bonus_old,";
		$sql.= "hours,";
		$sql.= "nua_afp,";
		$sql.= "afp,";
		$sql.= "state";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").",";
		$sql.= " ".(! isset($this->fk_user)?'NULL':"'".$this->fk_user."'").",";
		$sql.= " ".(! isset($this->fk_departament)?'NULL':"'".$this->fk_departament."'").",";
		$sql.= " ".(! isset($this->fk_charge)?'NULL':"'".$this->fk_charge."'").",";
		$sql.= " ".(! isset($this->fk_regional)?'NULL':"'".$this->fk_regional."'").",";
		$sql.= " ".(! isset($this->fk_proces)?'NULL':"'".$this->fk_proces."'").",";
		$sql.= " ".(! isset($this->fk_cc)?'NULL':"'".$this->fk_cc."'").",";
		$sql.= " ".(! isset($this->fk_account)?'NULL':"'".$this->fk_account."'").",";
		$sql.= " ".(! isset($this->date_ini) || dol_strlen($this->date_ini)==0?'NULL':$this->db->idate($this->date_ini)).",";
		$sql.= " ".(! isset($this->date_fin) || dol_strlen($this->date_fin)==0?'NULL':$this->db->idate($this->date_fin)).",";
		$sql.= " ".(! isset($this->basic)?'NULL':"'".$this->basic."'").",";
		$sql.= " ".(! isset($this->basic_fixed)?'NULL':"'".$this->basic_fixed."'").",";
		$sql.= " ".(! isset($this->nivel)?'NULL':"'".$this->db->escape($this->nivel)."'").",";
		$sql.= " ".(! isset($this->bonus_old)?'NULL':"'".$this->bonus_old."'").",";
		$sql.= " ".(! isset($this->hours)?'NULL':"'".$this->hours."'").",";
		$sql.= " ".(! isset($this->nua_afp)?'NULL':"'".$this->nua_afp."'").",";
		$sql.= " ".(! isset($this->afp)?'NULL':"'".$this->db->escape($this->afp)."'").",";
		$sql.= " ".(! isset($this->state)?'NULL':"'".$this->state."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."p_contract");

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
		
		$sql.= " t.ref,";
		$sql.= " t.fk_user,";
		$sql.= " t.fk_departament,";
		$sql.= " t.fk_charge,";
		$sql.= " t.fk_regional,";
		$sql.= " t.fk_proces,";
		$sql.= " t.fk_cc,";
		$sql.= " t.fk_account,";
		$sql.= " t.date_ini,";
		$sql.= " t.date_fin,";
		$sql.= " t.basic,";
		$sql.= " t.basic_fixed,";
		$sql.= " t.nivel,";
		$sql.= " t.bonus_old,";
		$sql.= " t.hours,";
		$sql.= " t.nua_afp,";
		$sql.= " t.afp,";
		$sql.= " t.state";
		
        $sql.= " FROM ".MAIN_DB_PREFIX."p_contract as t";
        $sql.= " WHERE t.rowid = ".$id;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;
                
				$this->ref = $obj->ref;
				$this->fk_user = $obj->fk_user;
				$this->fk_departament = $obj->fk_departament;
				$this->fk_charge = $obj->fk_charge;
				$this->fk_regional = $obj->fk_regional;
				$this->fk_proces = $obj->fk_proces;
				$this->fk_cc = $obj->fk_cc;
				$this->fk_account = $obj->fk_account;
				$this->date_ini = $this->db->jdate($obj->date_ini);
				$this->date_fin = $this->db->jdate($obj->date_fin);
				$this->basic = $obj->basic;
				$this->basic_fixed = $obj->basic_fixed;
				$this->nivel = $obj->nivel;
				$this->bonus_old = $obj->bonus_old;
				$this->hours = $obj->hours;
				$this->nua_afp = $obj->nua_afp;
				$this->afp = $obj->afp;
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
        
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->fk_user)) $this->fk_user=trim($this->fk_user);
		if (isset($this->fk_departament)) $this->fk_departament=trim($this->fk_departament);
		if (isset($this->fk_charge)) $this->fk_charge=trim($this->fk_charge);
		if (isset($this->fk_regional)) $this->fk_regional=trim($this->fk_regional);
		if (isset($this->fk_proces)) $this->fk_proces=trim($this->fk_proces);
		if (isset($this->fk_cc)) $this->fk_cc=trim($this->fk_cc);
		if (isset($this->fk_account)) $this->fk_account=trim($this->fk_account);
		if (isset($this->basic)) $this->basic=trim($this->basic);
		if (isset($this->basic_fixed)) $this->basic_fixed=trim($this->basic_fixed);
		if (isset($this->nivel)) $this->nivel=trim($this->nivel);
		if (isset($this->bonus_old)) $this->bonus_old=trim($this->bonus_old);
		if (isset($this->hours)) $this->hours=trim($this->hours);
		if (isset($this->nua_afp)) $this->nua_afp=trim($this->nua_afp);
		if (isset($this->afp)) $this->afp=trim($this->afp);
		if (isset($this->state)) $this->state=trim($this->state);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."p_contract SET";
        
		$sql.= " ref=".(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").",";
		$sql.= " fk_user=".(isset($this->fk_user)?$this->fk_user:"null").",";
		$sql.= " fk_departament=".(isset($this->fk_departament)?$this->fk_departament:"null").",";
		$sql.= " fk_charge=".(isset($this->fk_charge)?$this->fk_charge:"null").",";
		$sql.= " fk_regional=".(isset($this->fk_regional)?$this->fk_regional:"null").",";
		$sql.= " fk_proces=".(isset($this->fk_proces)?$this->fk_proces:"null").",";
		$sql.= " fk_cc=".(isset($this->fk_cc)?$this->fk_cc:"null").",";
		$sql.= " fk_account=".(isset($this->fk_account)?$this->fk_account:"null").",";
		$sql.= " date_ini=".(dol_strlen($this->date_ini)!=0 ? "'".$this->db->idate($this->date_ini)."'" : 'null').",";
		$sql.= " date_fin=".(dol_strlen($this->date_fin)!=0 ? "'".$this->db->idate($this->date_fin)."'" : 'null').",";
		$sql.= " basic=".(isset($this->basic)?$this->basic:"null").",";
		$sql.= " basic_fixed=".(isset($this->basic_fixed)?$this->basic_fixed:"null").",";
		$sql.= " nivel=".(isset($this->nivel)?"'".$this->db->escape($this->nivel)."'":"null").",";
		$sql.= " bonus_old=".(isset($this->bonus_old)?$this->bonus_old:"null").",";
		$sql.= " hours=".(isset($this->hours)?$this->hours:"null").",";
		$sql.= " nua_afp=".(isset($this->nua_afp)?$this->nua_afp:"null").",";
		$sql.= " afp=".(isset($this->afp)?"'".$this->db->escape($this->afp)."'":"null").",";
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."p_contract";
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

		$object=new Pcontract($this->db);

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
		$this->fk_user='';
		$this->fk_departament='';
		$this->fk_charge='';
		$this->fk_regional='';
		$this->fk_proces='';
		$this->fk_cc='';
		$this->fk_account='';
		$this->date_ini='';
		$this->date_fin='';
		$this->basic='';
		$this->basic_fixed='';
		$this->nivel='';
		$this->bonus_old='';
		$this->hours='';
		$this->nua_afp='';
		$this->afp='';
		$this->state='';

		
	}

	//MODIFICACIONES
    /**
     *  Load object in memory from the database
     *
     *  @param	int		$idUser    Id adherent
     *  @param	int		$state state
     *  @return int          	<0 if KO, >0 if OK
     */
	function fetch_vigent($idUser,$state=1)
	{
	  global $langs;
	  $sql = "SELECT";
	  $sql.= " t.rowid,";
	  
	  $sql.= " t.ref,";
	  $sql.= " t.fk_user,";
	  $sql.= " t.fk_departament,";
	  $sql.= " t.fk_charge,";
	  $sql.= " t.fk_regional,";
	  $sql.= " t.fk_proces,";
	  $sql.= " t.fk_cc,";
	  $sql.= " t.fk_account,";
	  $sql.= " t.date_ini,";
	  $sql.= " t.date_fin,";
	  $sql.= " t.basic,";
	  $sql.= " t.basic_fixed,";
	  $sql.= " t.nivel,";
	  $sql.= " t.bonus_old,";
	  $sql.= " t.hours,";
	  $sql.= " t.nua_afp,";
	  $sql.= " t.afp,";
	  $sql.= " t.state";
	  
	  $sql.= " FROM ".MAIN_DB_PREFIX."p_contract as t";
	  $sql.= " WHERE t.fk_user = ".$idUser;
	  $sql.= " AND state =".$state;
	  
    	dol_syslog(get_class($this)."::fetch_vigent sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
	  if ($this->db->num_rows($resql))
            {
	      $obj = $this->db->fetch_object($resql);
	      
	      $this->id    = $obj->rowid;
              
	      $this->ref = $obj->ref;
	      $this->fk_user = $obj->fk_user;
	      $this->fk_departament = $obj->fk_departament;
	      $this->fk_charge = $obj->fk_charge;
	      $this->fk_proces = $obj->fk_proces;
	      $this->fk_cc = $obj->fk_cc;
	      $this->fk_account = $obj->fk_account;
	      $this->date_ini = $this->db->jdate($obj->date_ini);
	      $this->date_fin = $this->db->jdate($obj->date_fin);
	      $this->basic = $obj->basic;
	      $this->state = $obj->state;

	      $this->fk_regional=$obj->regional;
	      $this->basic_fixed=$obj->basic_fixed;
	      $this->nivel=$obj->nivel;
	      $this->bonus_old=$obj->bonus_old;
	      $this->hours=$obj->hours;
	      $this->nua_afp=$obj->nua_afp;
	      $this->afp=$obj->afp;      
              
            }
	  $this->db->free($resql);
	  
	  return 1;
        }
        else
        {
	  $this->error="Error ".$this->db->lasterror();
	  dol_syslog(get_class($this)."::fetch_vigent ".$this->error, LOG_ERR);
	  return -1;
        }
    }

}
?>
