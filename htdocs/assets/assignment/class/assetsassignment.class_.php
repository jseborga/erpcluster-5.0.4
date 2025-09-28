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
 *  \file       dev/skeletons/assetsassignment.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2014-07-29 15:41
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Assetsassignment extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='assetsassignment';			//!< Id that identify managed objects
	var $table_element='assetsassignment';		//!< Name of table without prefix where object is stored

    var $id;
    
	var $entity;
	var $ref;
	var $fk_adherent;
	var $fk_property;
	var $fk_location;
	var $detail;
	var $date_assignment='';
	var $type_assignment;
	var $date_create='';
	var $mark;
	var $fk_user_create;
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
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->fk_adherent)) $this->fk_adherent=trim($this->fk_adherent);
		if (isset($this->fk_property)) $this->fk_property=trim($this->fk_property);
		if (isset($this->fk_location)) $this->fk_location=trim($this->fk_location);
		if (isset($this->detail)) $this->detail=trim($this->detail);
		if (isset($this->type_assignment)) $this->type_assignment=trim($this->type_assignment);
		if (isset($this->mark)) $this->mark=trim($this->mark);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->statut)) $this->statut=trim($this->statut);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."assets_assignment(";
		
		$sql.= "entity,";
		$sql.= "ref,";
		$sql.= "fk_adherent,";
		$sql.= "fk_property,";
		$sql.= "fk_location,";
		$sql.= "detail,";
		$sql.= "date_assignment,";
		$sql.= "type_assignment,";
		$sql.= "date_create,";
		$sql.= "mark,";
		$sql.= "fk_user_create,";
		$sql.= "statut";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->entity)?'NULL':"'".$this->entity."'").",";
		$sql.= " ".(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").",";
		$sql.= " ".(! isset($this->fk_adherent)?'NULL':"'".$this->fk_adherent."'").",";
		$sql.= " ".(! isset($this->fk_property)?'NULL':"'".$this->fk_property."'").",";
		$sql.= " ".(! isset($this->fk_location)?'NULL':"'".$this->fk_location."'").",";
		$sql.= " ".(! isset($this->detail)?'NULL':"'".$this->db->escape($this->detail)."'").",";
		$sql.= " ".(! isset($this->date_assignment) || dol_strlen($this->date_assignment)==0?'NULL':$this->db->idate($this->date_assignment)).",";
		$sql.= " ".(! isset($this->type_assignment)?'NULL':"'".$this->db->escape($this->type_assignment)."'").",";
		$sql.= " ".(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':$this->db->idate($this->date_create)).",";
		$sql.= " ".(! isset($this->mark)?'NULL':"'".$this->db->escape($this->mark)."'").",";
		$sql.= " ".(! isset($this->fk_user_create)?'NULL':"'".$this->fk_user_create."'").",";
		$sql.= " ".(! isset($this->statut)?'NULL':"'".$this->statut."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."assets_assignment");

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
		$sql.= " t.ref,";
		$sql.= " t.fk_adherent,";
		$sql.= " t.fk_property,";
		$sql.= " t.fk_location,";
		$sql.= " t.detail,";
		$sql.= " t.date_assignment,";
		$sql.= " t.type_assignment,";
		$sql.= " t.date_create,";
		$sql.= " t.mark,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."assets_assignment as t";
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
				$this->ref = $obj->rowid;
				$this->ref = $obj->ref;
				$this->fk_adherent = $obj->fk_adherent;
				$this->fk_property = $obj->fk_property;
				$this->fk_location = $obj->fk_location;
				$this->detail = $obj->detail;
				$this->date_assignment = $this->db->jdate($obj->date_assignment);
				$this->type_assignment = $obj->type_assignment;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->mark = $obj->mark;
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
        
		if (isset($this->entity)) $this->entity=trim($this->entity);
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->fk_adherent)) $this->fk_adherent=trim($this->fk_adherent);
		if (isset($this->fk_property)) $this->fk_property=trim($this->fk_property);
		if (isset($this->fk_location)) $this->fk_location=trim($this->fk_location);
		if (isset($this->detail)) $this->detail=trim($this->detail);
		if (isset($this->type_assignment)) $this->type_assignment=trim($this->type_assignment);
		if (isset($this->mark)) $this->mark=trim($this->mark);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->statut)) $this->statut=trim($this->statut);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."assets_assignment SET";
        
		$sql.= " entity=".(isset($this->entity)?$this->entity:"null").",";
		$sql.= " ref=".(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").",";
		$sql.= " fk_adherent=".(isset($this->fk_adherent)?$this->fk_adherent:"null").",";
		$sql.= " fk_property=".(isset($this->fk_property)?$this->fk_property:"null").",";
		$sql.= " fk_location=".(isset($this->fk_location)?$this->fk_location:"null").",";
		$sql.= " detail=".(isset($this->detail)?"'".$this->db->escape($this->detail)."'":"null").",";
		$sql.= " date_assignment=".(dol_strlen($this->date_assignment)!=0 ? "'".$this->db->idate($this->date_assignment)."'" : 'null').",";
		$sql.= " type_assignment=".(isset($this->type_assignment)?"'".$this->db->escape($this->type_assignment)."'":"null").",";
		$sql.= " date_create=".(dol_strlen($this->date_create)!=0 ? "'".$this->db->idate($this->date_create)."'" : 'null').",";
		$sql.= " mark=".(isset($this->mark)?"'".$this->db->escape($this->mark)."'":"null").",";
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."assets_assignment";
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

		$object=new Assetsassignment($this->db);

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
		$this->ref='';
		$this->fk_adherent='';
		$this->fk_property='';
		$this->fk_location='';
		$this->detail='';
		$this->date_assignment='';
		$this->type_assignment='';
		$this->date_create='';
		$this->mark='';
		$this->fk_user_create='';
		$this->tms='';
		$this->statut='';

		
	}


	//MODIFICADO
    /**
	 *  Returns the reference to the following non used Order depending on the active numbering module
	 *  defined into ALMACEN_ADDON
	 *
	 *  @param	Societe		$soc  	Object thirdparty
	 *  @return string      		Order free reference
	 */
    function getNextNumRef($soc)
    {
        global $db, $langs, $conf;
        $langs->load("assets@assets");
	//modelo fijo de numeracion;
	$modelnum = 'mod_assets_ubuntubo_assign';
        $dir = DOL_DOCUMENT_ROOT . "/assets/core/modules";

        //if (! empty($conf->global->ASSETS_ADDON))
	if (! empty($modelnum))
	  {
            //$file = $conf->global->ASSETS_ADDON.".php";
	    $file = $modelnum.".php";
            // Chargement de la classe de numerotation
	    //$classname = $conf->global->ASSETS_ADDON;
	    $classname = $modelnum;
            $result=include_once $dir.'/'.$file;
            if ($result)
	      {
                $obj = new $classname();
                $numref = "";
                $numref = $obj->getNextValue($soc,$this);
                if ( $numref != "")
		  {
                    return $numref;
		  }
                else
		  {
                    dol_print_error($db,"Assetsassignment::getNextNumRef ".$obj->error);
                    return "";
		  }
	      }
            else
	      {
                print $langs->trans("Error")." ".$langs->trans("Error_ASSETS_ADDON_NotDefined");
                return "";
	      }
	  }
        else
	  {
            print $langs->trans("Error")." ".$langs->trans("Error_ASSETS_ADDON_NotDefined");
            return "";
	  }
    }


}
?>
