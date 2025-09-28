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
 *  \file       dev/skeletons/items.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2015-10-13 11:35
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Items extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='items';			//!< Id that identify managed objects
	var $table_element='items';		//!< Name of table without prefix where object is stored

    var $id;
    
	var $entity;
	var $ref;
	var $fk_user_create;
	var $fk_user_mod;
	var $fk_type_item;
	var $fk_unit;
	var $detail;
	var $especification;
	var $plane;
	var $amount;
	var $date_create='';
	var $gestion;
	var $date_delete='';
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
        
		if (isset($this->entity)) $this->entity=trim($this->entity);
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->fk_user_mod)) $this->fk_user_mod=trim($this->fk_user_mod);
		if (isset($this->fk_type_item)) $this->fk_type_item=trim($this->fk_type_item);
		if (isset($this->fk_unit)) $this->fk_unit=trim($this->fk_unit);
		if (isset($this->detail)) $this->detail=trim($this->detail);
		if (isset($this->especification)) $this->especification=trim($this->especification);
		if (isset($this->plane)) $this->plane=trim($this->plane);
		if (isset($this->amount)) $this->amount=trim($this->amount);
		if (isset($this->gestion)) $this->gestion=trim($this->gestion);
		if (isset($this->statut)) $this->statut=trim($this->statut);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX.$this->table_element."(";
		
		$sql.= "entity,";
		$sql.= "ref,";
		$sql.= "fk_user_create,";
		$sql.= "fk_user_mod,";
		$sql.= "fk_type_item,";
		$sql.= "fk_unit,";
		$sql.= "detail,";
		$sql.= "especification,";
		$sql.= "plane,";
		$sql.= "amount,";
		$sql.= "date_create,";
		$sql.= "gestion,";
		$sql.= "date_delete,";
		$sql.= "statut";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->entity)?'NULL':"'".$this->entity."'").",";
		$sql.= " ".(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").",";
		$sql.= " ".(! isset($this->fk_user_create)?'NULL':"'".$this->fk_user_create."'").",";
		$sql.= " ".(! isset($this->fk_user_mod)?'NULL':"'".$this->fk_user_mod."'").",";
		$sql.= " ".(! isset($this->fk_type_item)?'NULL':"'".$this->fk_type_item."'").",";
		$sql.= " ".(! isset($this->fk_unit)?'NULL':"'".$this->fk_unit."'").",";
		$sql.= " ".(! isset($this->detail)?'NULL':"'".$this->db->escape($this->detail)."'").",";
		$sql.= " ".(! isset($this->especification)?'NULL':"'".$this->db->escape($this->especification)."'").",";
		$sql.= " ".(! isset($this->plane)?'NULL':"'".$this->db->escape($this->plane)."'").",";
		$sql.= " ".(! isset($this->amount)?'NULL':"'".$this->amount."'").",";
		$sql.= " ".(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':"'".$this->db->idate($this->date_create)."'").",";
		$sql.= " ".(! isset($this->gestion)?'NULL':"'".$this->gestion."'").",";
		$sql.= " ".(! isset($this->date_delete) || dol_strlen($this->date_delete)==0?'NULL':"'".$this->db->idate($this->date_delete)."'").",";
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
		$sql.= " t.ref,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.fk_user_mod,";
		$sql.= " t.fk_type_item,";
		$sql.= " t.fk_unit,";
		$sql.= " t.detail,";
		$sql.= " t.especification,";
		$sql.= " t.plane,";
		$sql.= " t.amount,";
		$sql.= " t.date_create,";
		$sql.= " t.gestion,";
		$sql.= " t.date_delete,";
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
				$this->ref = $obj->ref;
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_mod = $obj->fk_user_mod;
				$this->fk_type_item = $obj->fk_type_item;
				$this->fk_unit = $obj->fk_unit;
				$this->detail = $obj->detail;
				$this->especification = $obj->especification;
				$this->plane = $obj->plane;
				$this->amount = $obj->amount;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->gestion = $obj->gestion;
				$this->date_delete = $this->db->jdate($obj->date_delete);
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
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->fk_user_mod)) $this->fk_user_mod=trim($this->fk_user_mod);
		if (isset($this->fk_type_item)) $this->fk_type_item=trim($this->fk_type_item);
		if (isset($this->fk_unit)) $this->fk_unit=trim($this->fk_unit);
		if (isset($this->detail)) $this->detail=trim($this->detail);
		if (isset($this->especification)) $this->especification=trim($this->especification);
		if (isset($this->plane)) $this->plane=trim($this->plane);
		if (isset($this->amount)) $this->amount=trim($this->amount);
		if (isset($this->gestion)) $this->gestion=trim($this->gestion);
		if (isset($this->statut)) $this->statut=trim($this->statut);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX.$this->table_element." SET";
        
		$sql.= " entity=".(isset($this->entity)?$this->entity:"null").",";
		$sql.= " ref=".(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").",";
		$sql.= " fk_user_create=".(isset($this->fk_user_create)?$this->fk_user_create:"null").",";
		$sql.= " fk_user_mod=".(isset($this->fk_user_mod)?$this->fk_user_mod:"null").",";
		$sql.= " fk_type_item=".(isset($this->fk_type_item)?$this->fk_type_item:"null").",";
		$sql.= " fk_unit=".(isset($this->fk_unit)?$this->fk_unit:"null").",";
		$sql.= " detail=".(isset($this->detail)?"'".$this->db->escape($this->detail)."'":"null").",";
		$sql.= " especification=".(isset($this->especification)?"'".$this->db->escape($this->especification)."'":"null").",";
		$sql.= " plane=".(isset($this->plane)?"'".$this->db->escape($this->plane)."'":"null").",";
		$sql.= " amount=".(isset($this->amount)?$this->amount:"null").",";
		$sql.= " date_create=".(dol_strlen($this->date_create)!=0 ? "'".$this->db->idate($this->date_create)."'" : 'null').",";
		$sql.= " gestion=".(isset($this->gestion)?$this->gestion:"null").",";
		$sql.= " date_delete=".(dol_strlen($this->date_delete)!=0 ? "'".$this->db->idate($this->date_delete)."'" : 'null').",";
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

		$object=new Items($this->db);

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
		$this->fk_user_create='';
		$this->fk_user_mod='';
		$this->fk_type_item='';
		$this->fk_unit='';
		$this->detail='';
		$this->especification='';
		$this->plane='';
		$this->amount='';
		$this->date_create='';
		$this->gestion='';
		$this->date_delete='';
		$this->tms='';
		$this->statut='';

		
	}

	//MODIFICADO
	/**
     *  Load object in memory from the database
     *  whit fk_type_item
     *  @param	int		$id    	Id object
     *  @param	string	$ref	Ref
     *  @return int          	<0 if KO, >0 if OK
     */
	function getlist($id,$sortfield='t.ref',$sortorder='ASC',$limit=25,$offset=25)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.entity,";
		$sql.= " t.ref,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.fk_user_mod,";
		$sql.= " t.fk_type_item,";
		$sql.= " t.fk_unit,";
		$sql.= " t.detail,";
		$sql.= " t.especification,";
		$sql.= " t.plane,";
		$sql.= " t.amount,";
		$sql.= " t.date_create,";
		$sql.= " t.gestion,";
		$sql.= " t.date_delete,";
		$sql.= " t.tms,";
		$sql.= " t.statut";

		
        $sql.= " FROM ".MAIN_DB_PREFIX.$this->table_element." as t";
        $sql.= " WHERE t.fk_type_item = ".$id;
	$sql.= " ORDER BY $sortfield $sortorder";
	$sql.= $this->db->plimit($limit+1, $offset);

    	dol_syslog(get_class($this)."::getlist");
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
		    $objnew = new Items($this->db);
		    $objnew->id    = $obj->rowid;
		    
		    $objnew->entity = $obj->entity;
		    $objnew->ref = $obj->ref;
		    $objnew->fk_user_create = $obj->fk_user_create;
		    $objnew->fk_user_mod = $obj->fk_user_mod;
		    $objnew->fk_type_item = $obj->fk_type_item;
		    $objnew->fk_unit = $obj->fk_unit;
		    $objnew->detail = $obj->detail;
		    $objnew->especification = $obj->especification;
		    $objnew->plane = $obj->plane;
		    $objnew->amount = $obj->amount;
		    $objnew->date_create = $this->db->jdate($obj->date_create);
		    $objnew->gestion = $obj->gestion;
		    $objnew->date_delete = $this->db->jdate($obj->date_delete);
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
            return -1;
        }
    }

	/**
	 *	Returns the text label from units dictionnary
	 *
	 * 	@param	string $type Label type (long or short)
	 *	@return	string|int <0 if ko, label if ok
	 */
	function getLabelOfUnit($type='long')
	{
	  global $langs;
	  
	  if (!$this->fk_unit) {
	    return '';
	  }
	  
	  $langs->load('products');
	  $langs->load('monprojet@monprojet');
	  
	  $this->db->begin();
	  
	  $label_type = 'label';
	  
	  if ($type == 'short')
	    {
	      $label_type = 'short_label';
	    }
	  
	  $sql = 'select '.$label_type.' from '.MAIN_DB_PREFIX.'c_units where rowid='.$this->fk_unit;
	  $resql = $this->db->query($sql);
	  if($resql && $this->db->num_rows($resql) > 0)
	    {
	      $res = $this->db->fetch_array($resql);
	      $label = $res[$label_type];
	      $this->db->free($resql);
	      return $label;
	    }
	  else
	    {
	      $this->error=$this->db->error().' sql='.$sql;
	      dol_syslog(get_class($this)."::getLabelOfUnit Error ".$this->error, LOG_ERR);
	      return -1;
	    }
	}
	
}
