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
 *  \file       dev/skeletons/entrepotbanksoc.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2016-05-06 11:07
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Entrepotbanksoc extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='entrepotbanksoc';			//!< Id that identify managed objects
	var $table_element='entrepot_bank_soc';	//!< Name of table without prefix where object is stored

    var $id;

	var $entity;
	var $numero_ip;
	var $fk_user;
	var $fk_entrepotid;
	var $fk_socid;
	var $fk_cajaid;
	var $fk_bankid;
	var $fk_banktcid;
	var $fk_subsidiaryid;
	var $series;
	var $status;




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
		if (isset($this->numero_ip)) $this->numero_ip=trim($this->numero_ip);
		if (isset($this->fk_user)) $this->fk_user=trim($this->fk_user);
		if (isset($this->fk_entrepotid)) $this->fk_entrepotid=trim($this->fk_entrepotid);
		if (isset($this->fk_socid)) $this->fk_socid=trim($this->fk_socid);
		if (isset($this->fk_cajaid)) $this->fk_cajaid=trim($this->fk_cajaid);
		if (isset($this->fk_bankid)) $this->fk_bankid=trim($this->fk_bankid);
		if (isset($this->fk_banktcid)) $this->fk_banktcid=trim($this->fk_banktcid);
		if (isset($this->fk_subsidiaryid)) $this->fk_subsidiaryid=trim($this->fk_subsidiaryid);
		if (isset($this->series)) $this->series=trim($this->series);
		if (isset($this->status)) $this->status=trim($this->status);



		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."entrepot_bank_soc(";

		$sql.= "entity,";
		$sql.= "numero_ip,";
		$sql.= "fk_user,";
		$sql.= "fk_entrepotid,";
		$sql.= "fk_socid,";
		$sql.= "fk_cajaid,";
		$sql.= "fk_bankid,";
		$sql.= "fk_banktcid,";
		$sql.= "fk_subsidiaryid,";
		$sql.= "series,";
		$sql.= "status";


        $sql.= ") VALUES (";

		$sql.= " ".(! isset($this->entity)?'NULL':"'".$this->entity."'").",";
		$sql.= " ".(! isset($this->numero_ip)?'NULL':"'".$this->db->escape($this->numero_ip)."'").",";
		$sql.= " ".(! isset($this->fk_user)?'NULL':"'".$this->fk_user."'").",";
		$sql.= " ".(! isset($this->fk_entrepotid)?'NULL':"'".$this->fk_entrepotid."'").",";
		$sql.= " ".(! isset($this->fk_socid)?'NULL':"'".$this->fk_socid."'").",";
		$sql.= " ".(! isset($this->fk_cajaid)?'NULL':"'".$this->fk_cajaid."'").",";
		$sql.= " ".(! isset($this->fk_bankid)?'NULL':"'".$this->fk_bankid."'").",";
		$sql.= " ".(! isset($this->fk_banktcid)?'NULL':"'".$this->fk_banktcid."'").",";
		$sql.= " ".(! isset($this->fk_subsidiaryid)?'NULL':"'".$this->fk_subsidiaryid."'").",";
		$sql.= " ".(! isset($this->series)?'NULL':"'".$this->db->escape($this->series)."'").",";
		$sql.= " ".(! isset($this->status)?'NULL':"'".$this->db->escape($this->status)."'")."";


		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."entrepot_bank_soc");

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
		$sql.= " t.numero_ip,";
		$sql.= " t.fk_user,";
		$sql.= " t.fk_entrepotid,";
		$sql.= " t.fk_socid,";
		$sql.= " t.fk_cajaid,";
		$sql.= " t.fk_bankid,";
		$sql.= " t.fk_banktcid,";
		$sql.= " t.fk_subsidiaryid,";
		$sql.= " t.series,";
		$sql.= " t.status";


        $sql.= " FROM ".MAIN_DB_PREFIX."entrepot_bank_soc as t";
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
				$this->numero_ip = $obj->numero_ip;
				$this->fk_user = $obj->fk_user;
				$this->fk_entrepotid = $obj->fk_entrepotid;
				$this->fk_socid = $obj->fk_socid;
				$this->fk_cajaid = $obj->fk_cajaid;
				$this->fk_bankid = $obj->fk_bankid;
				$this->fk_banktcid = $obj->fk_banktcid;
				$this->fk_subsidiaryid = $obj->fk_subsidiaryid;
				$this->series = $obj->series;
				$this->status = $obj->status;


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
		if (isset($this->numero_ip)) $this->numero_ip=trim($this->numero_ip);
		if (isset($this->fk_user)) $this->fk_user=trim($this->fk_user);
		if (isset($this->fk_entrepotid)) $this->fk_entrepotid=trim($this->fk_entrepotid);
		if (isset($this->fk_socid)) $this->fk_socid=trim($this->fk_socid);
		if (isset($this->fk_cajaid)) $this->fk_cajaid=trim($this->fk_cajaid);
		if (isset($this->fk_bankid)) $this->fk_bankid=trim($this->fk_bankid);
		if (isset($this->fk_banktcid)) $this->fk_banktcid=trim($this->fk_banktcid);
		if (isset($this->fk_subsidiaryid)) $this->fk_subsidiaryid=trim($this->fk_subsidiaryid);
		if (isset($this->series)) $this->series=trim($this->series);
		if (isset($this->status)) $this->status=trim($this->status);



		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."entrepot_bank_soc SET";

		$sql.= " entity=".(isset($this->entity)?$this->entity:"null").",";
		$sql.= " numero_ip=".(isset($this->numero_ip)?"'".$this->db->escape($this->numero_ip)."'":"null").",";
		$sql.= " fk_user=".(isset($this->fk_user)?$this->fk_user:"null").",";
		$sql.= " fk_entrepotid=".(isset($this->fk_entrepotid)?$this->fk_entrepotid:"null").",";
		$sql.= " fk_socid=".(isset($this->fk_socid)?$this->fk_socid:"null").",";
		$sql.= " fk_cajaid=".(isset($this->fk_cajaid)?$this->fk_cajaid:"null").",";
		$sql.= " fk_bankid=".(isset($this->fk_bankid)?$this->fk_bankid:"null").",";
		$sql.= " fk_banktcid=".(isset($this->fk_banktcid)?$this->fk_banktcid:"null").",";
		$sql.= " fk_subsidiaryid=".(isset($this->fk_subsidiaryid)?$this->fk_subsidiaryid:"null").",";
		$sql.= " series=".(isset($this->series)?"'".$this->db->escape($this->series)."'":"null").",";
		$sql.= " status=".(isset($this->status)?"'".$this->db->escape($this->status)."'":"null")."";


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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."entrepot_bank_soc";
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

		$object=new Entrepotbanksoc($this->db);

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
		$this->numero_ip='';
		$this->fk_user='';
		$this->fk_entrepotid='';
		$this->fk_socid='';
		$this->fk_cajaid='';
		$this->fk_bankid='';
		$this->fk_banktcid='';
		$this->fk_subsidiaryid='';
		$this->series='';
		$this->status='';


	}

    //MODIFICADO
    function getlistuser($id)
    {
      global $langs;
      $sql = "SELECT";
      $sql.= " t.rowid,";

      $sql.= " t.entity,";
      $sql.= " t.numero_ip,";
      $sql.= " t.fk_user,";
      $sql.= " t.fk_entrepotid,";
      $sql.= " t.fk_socid,";
      $sql.= " t.fk_cajaid,";
      $sql.= " t.fk_bankid,";
      $sql.= " t.fk_banktcid,";
      $sql.= " t.fk_subsidiaryid,";
      $sql.= " t.series,";
      $sql.= " t.status";
      $sql.= " FROM ".MAIN_DB_PREFIX."entrepot_bank_soc as t";
      $sql.= " WHERE t.fk_user = ".$id;

      dol_syslog(get_class($this)."::getlistuser sql=".$sql, LOG_DEBUG);
      $resql=$this->db->query($sql);
      $this->array = array();
      if ($resql)
        {
          if ($this->db->num_rows($resql))
        {
          $num = $this->db->num_rows($resql);
          $i = 0;
          while ($i < $num)
            {
              $obj = $this->db->fetch_object($resql);
              $objnew = new Entrepotbanksoc($this->db);
              $this->array[$obj->rowid] = $obj;
              $i++;
            }
        }
          $this->db->free($resql);
          return 1;
        }
      else
        {
          $this->error="Error ".$this->db->lasterror();
          dol_syslog(get_class($this)."::getlistuser ".$this->error, LOG_ERR);
          return -1;
        }
    }

}
?>
