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
 *  \file       dev/skeletons/poareformulateddet.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2014-04-28 13:30
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Poareformulateddet extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='poareformulateddet';			//!< Id that identify managed objects
	var $table_element='poareformulateddet';		//!< Name of table without prefix where object is stored

    var $id;
    
	var $fk_poa_reformulated;
	var $fk_structure;
	var $fk_poa_poa;
	var $partida;
	var $amount;
	var $reform;
	var $date_create='';
	var $fk_user_create;
	var $tms='';
	var $statut;
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
        
		if (isset($this->fk_poa_reformulated)) $this->fk_poa_reformulated=trim($this->fk_poa_reformulated);
		if (isset($this->fk_structure)) $this->fk_structure=trim($this->fk_structure);
		if (isset($this->fk_poa_poa)) $this->fk_poa_poa=trim($this->fk_poa_poa);
		if (isset($this->partida)) $this->partida=trim($this->partida);
		if (isset($this->amount)) $this->amount=trim($this->amount);
		if (isset($this->reform)) $this->reform=trim($this->reform);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->statut)) $this->statut=trim($this->statut);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."poa_reformulated_det(";
		
		$sql.= "fk_poa_reformulated,";
		$sql.= "fk_structure,";
		$sql.= "fk_poa_poa,";
		$sql.= "partida,";
		$sql.= "amount,";
		$sql.= "reform,";
		$sql.= "date_create,";
		$sql.= "fk_user_create,";
		$sql.= "statut";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->fk_poa_reformulated)?'NULL':"'".$this->fk_poa_reformulated."'").",";
		$sql.= " ".(! isset($this->fk_structure)?'NULL':"'".$this->fk_structure."'").",";
		$sql.= " ".(! isset($this->fk_poa_poa)?'NULL':"'".$this->fk_poa_poa."'").",";
		$sql.= " ".(! isset($this->partida)?'NULL':"'".$this->db->escape($this->partida)."'").",";
		$sql.= " ".(! isset($this->amount)?'NULL':"'".$this->amount."'").",";
		$sql.= " ".(! isset($this->reform)?'NULL':"'".$this->db->escape($this->reform)."'").",";
		$sql.= " ".(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':$this->db->idate($this->date_create)).",";
		$sql.= " ".(! isset($this->fk_user_create)?'NULL':"'".$this->fk_user_create."'").",";
		$sql.= " ".(! isset($this->statut)?'NULL':"'".$this->statut."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."poa_reformulated_det");

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
		
		$sql.= " t.fk_poa_reformulated,";
		$sql.= " t.fk_structure,";
		$sql.= " t.fk_poa_poa,";
		$sql.= " t.partida,";
		$sql.= " t.amount,";
		$sql.= " t.reform,";
		$sql.= " t.date_create,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."poa_reformulated_det as t";
        $sql.= " WHERE t.rowid = ".$id;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;
                
				$this->fk_poa_reformulated = $obj->fk_poa_reformulated;
				$this->fk_structure = $obj->fk_structure;
				$this->fk_poa_poa = $obj->fk_poa_poa;
				$this->partida = $obj->partida;
				$this->amount = $obj->amount;
				$this->reform = $obj->reform;
				$this->date_create = $this->db->jdate($obj->date_create);
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
        
		if (isset($this->fk_poa_reformulated)) $this->fk_poa_reformulated=trim($this->fk_poa_reformulated);
		if (isset($this->fk_structure)) $this->fk_structure=trim($this->fk_structure);
		if (isset($this->fk_poa_poa)) $this->fk_poa_poa=trim($this->fk_poa_poa);
		if (isset($this->partida)) $this->partida=trim($this->partida);
		if (isset($this->amount)) $this->amount=trim($this->amount);
		if (isset($this->reform)) $this->reform=trim($this->reform);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->statut)) $this->statut=trim($this->statut);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."poa_reformulated_det SET";
        
		$sql.= " fk_poa_reformulated=".(isset($this->fk_poa_reformulated)?$this->fk_poa_reformulated:"null").",";
		$sql.= " fk_structure=".(isset($this->fk_structure)?$this->fk_structure:"null").",";
		$sql.= " fk_poa_poa=".(isset($this->fk_poa_poa)?$this->fk_poa_poa:"null").",";
		$sql.= " partida=".(isset($this->partida)?"'".$this->db->escape($this->partida)."'":"null").",";
		$sql.= " amount=".(isset($this->amount)?$this->amount:"null").",";
		$sql.= " reform=".(isset($this->reform)?"'".$this->db->escape($this->reform)."'":"null").",";
		$sql.= " date_create=".(dol_strlen($this->date_create)!=0 ? "'".$this->db->idate($this->date_create)."'" : 'null').",";
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."poa_reformulated_det";
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

		$object=new Poareformulateddet($this->db);

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
		
		$this->fk_poa_reformulated='';
		$this->fk_structure='';
		$this->fk_poa_poa='';
		$this->partida='';
		$this->amount='';
		$this->reform='';
		$this->date_create='';
		$this->fk_user_create='';
		$this->tms='';
		$this->statut='';

		
	}

	//modificado
    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
	function getlist($fk_poa_reformulated,$idp=0)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.fk_poa_reformulated,";
		$sql.= " t.fk_structure,";
		$sql.= " t.fk_poa_poa,";
		$sql.= " t.partida,";
		$sql.= " t.amount,";
		$sql.= " t.reform,";
		$sql.= " t.date_create,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."poa_reformulated_det as t";
        $sql.= " WHERE t.fk_poa_reformulated = ".$fk_poa_reformulated;
	if ($idp)
	  $sql.= " AND t.fk_poa_poa = ".$idp;
	
    	dol_syslog(get_class($this)."::getlist sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
	$this->array = array();
        if ($resql)
	  {
	    $num = $this->db->num_rows($resql);
            if ($num)
	      {
		$i = 0;
		while ($i < $num)
		  {
		    $obj = $this->db->fetch_object($resql);
		    $objnew = new Poareformulateddet($this->db);
		    
		    $objnew->id    = $obj->rowid;
		    
		    $objnew->fk_poa_reformulated = $obj->fk_poa_reformulated;
		    $objnew->fk_structure = $obj->fk_structure;
		    $objnew->fk_poa_poa = $obj->fk_poa_poa;
		    $objnew->partida = $obj->partida;
		    $objnew->amount = $obj->amount;
		    $objnew->reform = $obj->reform;
		    $objnew->date_create = $this->db->jdate($obj->date_create);
		    $objnew->fk_user_create = $obj->fk_user_create;
		    $objnew->tms = $this->db->jdate($obj->tms);
		    $objnew->statut = $obj->statut;
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
            dol_syslog(get_class($this)."::getlist ".$this->error, LOG_ERR);
            return -1;
	  }
    }

	function get_sumaref($aReform)
	{
	  global $langs,$conf;
	  $aOf = array();
	  $aOfone = array();
	  $aOfref = array();
	  foreach((array) $aReform AS $id)
	    {
	      $this->getlist($id);
	      foreach((array) $this->array AS $i => $objnew)
		{
		  $aOf[$objnew->fk_structure][$objnew->fk_poa_poa][$objnew->partida]+= $objnew->amount;
		  $aOfone[$objnew->fk_structure][$objnew->fk_poa_poa][$objnew->partida] = $i;
		  $aOfref[$objnew->fk_structure][$objnew->fk_poa_poa][$objnew->partida] = $objnew->reform;
		}
	    }
	  return array($aOf,$aOfone,$aOfref);
	}

}
?>
