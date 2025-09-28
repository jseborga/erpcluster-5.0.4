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
 *  \file       dev/skeletons/cflowmodels.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2015-03-25 17:40
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Cflowmodels extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='c_flow_models';			//!< Id that identify managed objects
	var $table_element='c_flow_models';		//!< Name of table without prefix where object is stored

    var $id;
    
	var $entity;
	var $groups;
	var $code;
	var $code0;
	var $code1;
	var $code2;
	var $code3;
	var $code4;
	var $code_actor_last;
	var $deadlines;
	var $label;
	var $label1;
	var $label2;
	var $label3;
	var $label4;
	var $quant;
	var $sequen;
	var $active;

    


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
		if (isset($this->groups)) $this->groups=trim($this->groups);
		if (isset($this->code)) $this->code=trim($this->code);
		if (isset($this->code0)) $this->code0=trim($this->code0);
		if (isset($this->code1)) $this->code1=trim($this->code1);
		if (isset($this->code2)) $this->code2=trim($this->code2);
		if (isset($this->code3)) $this->code3=trim($this->code3);
		if (isset($this->code4)) $this->code4=trim($this->code4);
		if (isset($this->code_actor_last)) $this->code_actor_last=trim($this->code_actor_last);
		if (isset($this->deadlines)) $this->deadlines=trim($this->deadlines);
		if (isset($this->label)) $this->label=trim($this->label);
		if (isset($this->label1)) $this->label1=trim($this->label1);
		if (isset($this->label2)) $this->label2=trim($this->label2);
		if (isset($this->label3)) $this->label3=trim($this->label3);
		if (isset($this->label4)) $this->label4=trim($this->label4);
		if (isset($this->quant)) $this->quant=trim($this->quant);
		if (isset($this->sequen)) $this->sequen=trim($this->sequen);
		if (isset($this->active)) $this->active=trim($this->active);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."c_flow_models(";
		
		$sql.= "entity,";
		$sql.= "groups,";
		$sql.= "code,";
		$sql.= "code0,";
		$sql.= "code1,";
		$sql.= "code2,";
		$sql.= "code3,";
		$sql.= "code4,";
		$sql.= "code_actor_last,";
		$sql.= "deadlines,";
		$sql.= "label,";
		$sql.= "label1,";
		$sql.= "label2,";
		$sql.= "label3,";
		$sql.= "label4,";
		$sql.= "quant,";
		$sql.= "sequen,";
		$sql.= "active";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->entity)?'NULL':"'".$this->entity."'").",";
		$sql.= " ".(! isset($this->groups)?'NULL':"'".$this->db->escape($this->groups)."'").",";
		$sql.= " ".(! isset($this->code)?'NULL':"'".$this->db->escape($this->code)."'").",";
		$sql.= " ".(! isset($this->code0)?'NULL':"'".$this->db->escape($this->code0)."'").",";
		$sql.= " ".(! isset($this->code1)?'NULL':"'".$this->db->escape($this->code1)."'").",";
		$sql.= " ".(! isset($this->code2)?'NULL':"'".$this->db->escape($this->code2)."'").",";
		$sql.= " ".(! isset($this->code3)?'NULL':"'".$this->db->escape($this->code3)."'").",";
		$sql.= " ".(! isset($this->code4)?'NULL':"'".$this->db->escape($this->code4)."'").",";
		$sql.= " ".(! isset($this->code_actor_last)?'NULL':"'".$this->db->escape($this->code_actor_last)."'").",";
		$sql.= " ".(! isset($this->deadlines)?'NULL':"'".$this->deadlines."'").",";
		$sql.= " ".(! isset($this->label)?'NULL':"'".$this->db->escape($this->label)."'").",";
		$sql.= " ".(! isset($this->label1)?'NULL':"'".$this->db->escape($this->label1)."'").",";
		$sql.= " ".(! isset($this->label2)?'NULL':"'".$this->db->escape($this->label2)."'").",";
		$sql.= " ".(! isset($this->label3)?'NULL':"'".$this->db->escape($this->label3)."'").",";
		$sql.= " ".(! isset($this->label4)?'NULL':"'".$this->db->escape($this->label4)."'").",";
		$sql.= " ".(! isset($this->quant)?'NULL':"'".$this->quant."'").",";
		$sql.= " ".(! isset($this->sequen)?'NULL':"'".$this->sequen."'").",";
		$sql.= " ".(! isset($this->active)?'NULL':"'".$this->active."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."c_flow_models");

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
		$sql.= " t.groups,";
		$sql.= " t.code,";
		$sql.= " t.code0,";
		$sql.= " t.code1,";
		$sql.= " t.code2,";
		$sql.= " t.code3,";
		$sql.= " t.code4,";
		$sql.= " t.code_actor_last,";
		$sql.= " t.deadlines,";
		$sql.= " t.label,";
		$sql.= " t.label1,";
		$sql.= " t.label2,";
		$sql.= " t.label3,";
		$sql.= " t.label4,";
		$sql.= " t.quant,";
		$sql.= " t.sequen,";
		$sql.= " t.active";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."c_flow_models as t";
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
				$this->groups = $obj->groups;
				$this->code = $obj->code;
				$this->code0 = $obj->code0;
				$this->code1 = $obj->code1;
				$this->code2 = $obj->code2;
				$this->code3 = $obj->code3;
				$this->code4 = $obj->code4;
				$this->code_actor_last = $obj->code_actor_last;
				$this->deadlines = $obj->deadlines;
				$this->label = $obj->label;
				$this->label1 = $obj->label1;
				$this->label2 = $obj->label2;
				$this->label3 = $obj->label3;
				$this->label4 = $obj->label4;
				$this->quant = $obj->quant;
				$this->sequen = $obj->sequen;
				$this->active = $obj->active;

                
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
		if (isset($this->groups)) $this->groups=trim($this->groups);
		if (isset($this->code)) $this->code=trim($this->code);
		if (isset($this->code0)) $this->code0=trim($this->code0);
		if (isset($this->code1)) $this->code1=trim($this->code1);
		if (isset($this->code2)) $this->code2=trim($this->code2);
		if (isset($this->code3)) $this->code3=trim($this->code3);
		if (isset($this->code4)) $this->code4=trim($this->code4);
		if (isset($this->code_actor_last)) $this->code_actor_last=trim($this->code_actor_last);
		if (isset($this->deadlines)) $this->deadlines=trim($this->deadlines);
		if (isset($this->label)) $this->label=trim($this->label);
		if (isset($this->label1)) $this->label1=trim($this->label1);
		if (isset($this->label2)) $this->label2=trim($this->label2);
		if (isset($this->label3)) $this->label3=trim($this->label3);
		if (isset($this->label4)) $this->label4=trim($this->label4);
		if (isset($this->quant)) $this->quant=trim($this->quant);
		if (isset($this->sequen)) $this->sequen=trim($this->sequen);
		if (isset($this->active)) $this->active=trim($this->active);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."c_flow_models SET";
        
		$sql.= " entity=".(isset($this->entity)?$this->entity:"null").",";
		$sql.= " groups=".(isset($this->groups)?"'".$this->db->escape($this->groups)."'":"null").",";
		$sql.= " code=".(isset($this->code)?"'".$this->db->escape($this->code)."'":"null").",";
		$sql.= " code0=".(isset($this->code0)?"'".$this->db->escape($this->code0)."'":"null").",";
		$sql.= " code1=".(isset($this->code1)?"'".$this->db->escape($this->code1)."'":"null").",";
		$sql.= " code2=".(isset($this->code2)?"'".$this->db->escape($this->code2)."'":"null").",";
		$sql.= " code3=".(isset($this->code3)?"'".$this->db->escape($this->code3)."'":"null").",";
		$sql.= " code4=".(isset($this->code4)?"'".$this->db->escape($this->code4)."'":"null").",";
		$sql.= " code_actor_last=".(isset($this->code_actor_last)?"'".$this->db->escape($this->code_actor_last)."'":"null").",";
		$sql.= " deadlines=".(isset($this->deadlines)?$this->deadlines:"null").",";
		$sql.= " label=".(isset($this->label)?"'".$this->db->escape($this->label)."'":"null").",";
		$sql.= " label1=".(isset($this->label1)?"'".$this->db->escape($this->label1)."'":"null").",";
		$sql.= " label2=".(isset($this->label2)?"'".$this->db->escape($this->label2)."'":"null").",";
		$sql.= " label3=".(isset($this->label3)?"'".$this->db->escape($this->label3)."'":"null").",";
		$sql.= " label4=".(isset($this->label4)?"'".$this->db->escape($this->label4)."'":"null").",";
		$sql.= " quant=".(isset($this->quant)?$this->quant:"null").",";
		$sql.= " sequen=".(isset($this->sequen)?$this->sequen:"null").",";
		$sql.= " active=".(isset($this->active)?$this->active:"null")."";

        
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."c_flow_models";
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

		$object=new Cflowmodels($this->db);

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
		$this->groups='';
		$this->code='';
		$this->code0='';
		$this->code1='';
		$this->code2='';
		$this->code3='';
		$this->code4='';
		$this->code_actor_last='';
		$this->deadlines='';
		$this->label='';
		$this->label1='';
		$this->label2='';
		$this->label3='';
		$this->label4='';
		$this->quant='';
		$this->sequen='';
		$this->active='';

		
	}

			//modificado
    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
	function getlist($groups='',$limit='',$offset='',$filter='',$orders='')
	{
	  global $langs,$conf;
	  $sql = "SELECT";
	  $sql.= " t.rowid,";
	  
	  $sql.= " t.entity,";
	  $sql.= " t.groups,";
	  $sql.= " t.code,";
	  $sql.= " t.code0,";
	  $sql.= " t.code1,";
	  $sql.= " t.code2,";
	  $sql.= " t.code3,";
	  $sql.= " t.code4,";
	  $sql.= " t.code_actor_last,";
	  $sql.= " t.deadlines,";
	  $sql.= " t.label,";
	  $sql.= " t.label1,";
	  $sql.= " t.label2,";
	  $sql.= " t.label3,";
	  $sql.= " t.label4,";
	  $sql.= " t.quant,";
	  $sql.= " t.sequen,";
	  $sql.= " t.active";
	  
	  $sql.= " FROM ".MAIN_DB_PREFIX."c_flow_models as t";
	  if ($groups)
	    {
	      $sql.= " WHERE t.groups = '".$groups."' ";
	      $sql.= " AND t.active = 1";
	    }
	  else
	    $sql.= " WHERE t.active = 1 ";
	  if ($filter)
	    $sql.= $filter;
	  if (empty($orders))
	    $sql.= " ORDER BY t.groups, t.sequen ";
	  else
	    $sql.= " ORDER BY ".$orders;
	  if (!empty($limit) && !empty($offset))
	    $sql.= $this->db->plimit($limit+1, $offset);
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
		    $objnew = new Cflowmodels($this->db);
		    
		    $objnew->id    = $obj->rowid;
		    
		    $objnew->entity = $obj->entity;
		    $objnew->groups = $obj->groups;
		    $objnew->code = $obj->code;
		    $objnew->code0 = $obj->code0;
		    $objnew->code1 = $obj->code1;
		    $objnew->code2 = $obj->code2;
		    $objnew->code3 = $obj->code3;
		    $objnew->code4 = $obj->code4;
		    $objnew->code_actor_last = $obj->code_actor_last;
		    $objnew->deadlines = $obj->deadlines;
		    $objnew->label = $obj->label;
		    $objnew->quant = $obj->quant;
		    $objnew->sequen = $obj->sequen;
		    $objnew->active = $obj->active;
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
    
	function select_flow_models($groups,$selected='',$htmlname='fk_tables',$htmloption='',$showempty=0,$campo='rowid')
	{
	  global $db, $langs, $conf;
	  if (empty($groups)) return -1;
	  $sql = " SELECT ";
	  $sql.= " t.".$campo." AS code,";
	  $sql.= " t.".$campo." AS libelle ";
	  $sql.= " FROM ".MAIN_DB_PREFIX."c_flow_models AS t ";
	  $sql.= " WHERE ";
	  $sql.= " t.active = 1";
	  $sql.= " AND t.groups = '".$groups."'";
	  $sql.= " GROUP BY ";
	  $sql.= " t.".$campo;
	  $sql.= " ORDER BY t.".$campo;
	  $resql = $db->query($sql);
	  $html = '';
	  
	  if ($resql)
	    {
	      $html.= '<select class="flat" name="'.$htmlname.'" id="select'.$htmlname.'" '.(!empty($htmloptions)?$htmloptions:'').'>';
	      if ($showempty) 
		{
		  $html.= '<option value="0">&nbsp;</option>';
		}
	      
	      $num = $db->num_rows($resql);
	      $i = 0;
	      if ($num)
		{;
		  while ($i < $num)
		    {
		      $obj = $db->fetch_object($resql);
		      if (!empty($selected) && $selected == $obj->rowid)
			{
			  $html.= '<option value="'.$obj->code.'" selected="selected">'.$obj->libelle.'</option>';
			}
		      else
			{
			  $html.= '<option value="'.$obj->code.'">'.$obj->libelle.'</option>';
			}
		      $i++;
		    }
		}
	      $html.= '</select>';
	      return $html;
	    }
	}

	    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
	function fetch_code($group,$code)
	{
	  global $langs,$conf;
	  if (empty($group) || empty($code))
	    return -1;
	  $sql = "SELECT";
	  $sql.= " t.rowid,";
	  
	  $sql.= " t.entity,";
	  $sql.= " t.groups,";
	  $sql.= " t.code,";
	  $sql.= " t.code0,";
	  $sql.= " t.code1,";
	  $sql.= " t.code2,";
	  $sql.= " t.code3,";
	  $sql.= " t.code4,";
	  $sql.= " t.code_actor_last,";
	  $sql.= " t.deadlines,";
	  $sql.= " t.label,";
	  $sql.= " t.label1,";
	  $sql.= " t.label2,";
	  $sql.= " t.label3,";
	  $sql.= " t.label4,";
	  $sql.= " t.quant,";
	  $sql.= " t.sequen,";
	  $sql.= " t.active";
	  
	  
	  $sql.= " FROM ".MAIN_DB_PREFIX."c_flow_models as t";
	  $sql.= " WHERE t.entity = ".$conf->entity;
	  $sql.= " AND t.groups = ".$group;
	  $sql.= " AND t.code = '".$code."'";
	  
	  dol_syslog(get_class($this)."::fetch_code sql=".$sql, LOG_DEBUG);
	  $resql=$this->db->query($sql);
	  if ($resql)
	    {
	      if ($this->db->num_rows($resql))
		{
		  $obj = $this->db->fetch_object($resql);
		  
		  $this->id    = $obj->rowid;
		  
		  $this->entity = $obj->entity;
		  $this->groups = $obj->groups;
		  $this->code = $obj->code;
		  $this->code0 = $obj->code0;
		  $this->code1 = $obj->code1;
		  $this->code2 = $obj->code2;
		  $this->code3 = $obj->code3;
		  $this->code4 = $obj->code4;
		  $this->code_actor_last = $obj->code_actor_last;
		  $this->deadlines = $obj->deadlines;
		  $this->label = $obj->label;
		  $this->label1 = $obj->label1;
		  $this->label2 = $obj->label2;
		  $this->label3 = $obj->label3;
		  $this->label4 = $obj->label4;
		  $this->quant = $obj->quant;
		  $this->sequen = $obj->sequen;
		  $this->active = $obj->active;
		  
		  
		}
	      $this->db->free($resql);
	      
	      return 1;
	    }
	  else
	    {
	      $this->error="Error ".$this->db->lasterror();
	      dol_syslog(get_class($this)."::fetch_code ".$this->error, LOG_ERR);
	      return -1;
	    }
	}

}
?>
