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
 *  \file       dev/skeletons/pdepartament.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2013-06-14 22:37
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Pdepartament extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='pdepartament';			//!< Id that identify managed objects
	var $table_element='p_departament';	//!< Name of table without prefix where object is stored
	protected $ismultientitymanaged = 1;
    var $id;
    
	var $entity;
	var $fk_father;
	var $ref;
	var $fk_user_resp;

    


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
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->fk_user_resp)) $this->fk_user_resp=trim($this->fk_user_resp);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."p_departament(";
		
		$sql.= "entity,";
		$sql.= "fk_father,";
		$sql.= "ref,";
		$sql.= "fk_user_resp";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->entity)?'NULL':"'".$this->entity."'").",";
		$sql.= " ".(! isset($this->fk_father)?'NULL':"'".$this->fk_father."'").",";
		$sql.= " ".(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").",";
		$sql.= " ".(! isset($this->fk_user_resp)?'NULL':"'".$this->fk_user_resp."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."p_departament");

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
		$sql.= " t.fk_father,";
		$sql.= " t.ref,";
		$sql.= " t.fk_user_resp";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."p_departament as t";
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
		$this->fk_father = $obj->fk_father;
		$this->ref = $obj->ref;
		$this->fk_user_resp = $obj->fk_user_resp;

                
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
		if (isset($this->fk_father)) $this->fk_father=trim($this->fk_father);
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->fk_user_resp)) $this->fk_user_resp=trim($this->fk_user_resp);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."p_departament SET";
        
		$sql.= " entity=".(isset($this->entity)?$this->entity:"null").",";
		$sql.= " fk_father=".(isset($this->fk_father)?$this->fk_father:"null").",";
		$sql.= " ref=".(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").",";
		$sql.= " fk_user_resp=".(isset($this->fk_user_resp)?$this->fk_user_resp:"null")."";

        
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."p_departament";
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

		$object=new Pdepartament($this->db);

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
		$this->ref='';
		$this->fk_user_resp='';

		
	}

    /**
     *  Return list of orders (eventuelly filtered on a user) into an array
     *
     *  @param      int		$brouillon      0=non brouillon, 1=brouillon
     *  @param      User	$user           Objet user de filtre
     *  @return     int             		-1 if KO, array with result if OK
     */
    function liste_array($empty="")
    {
      global $conf,$langs;
      
      $ga = array();
      if ($empty == 1)
	$ga[0] = $langs->trans("Select");
      $sql = "SELECT p.rowid, p.ref ";
      $sql.= " FROM ".MAIN_DB_PREFIX."p_departament AS p ";
      $sql.= " WHERE p.entity =".$conf->entity;
      $sql.= " ORDER BY p.ref ";
      
      $result=$this->db->query($sql);
      if ($result)
	{
	  $numc = $this->db->num_rows($result);
	  if ($numc)
	    {
	      $i = 0;
	      while ($i < $numc)
		{
		  $obj = $this->db->fetch_object($result);
		  $ga[$obj->rowid] = $obj->ref.' ';
		  $i++;
		}
	    }
	  return $ga;
	}
      else
	{
	  dol_print_error($this->db);
	  return -1;
	}
    }

    /**
     *  Return combo list of activated countries, into language of user
     *
     *  @param	string	$selected       Id or Code or Label of preselected country
     *  @param  string	$htmlname       Name of html select object
     *  @param  string	$htmloption     Options html on select object
     *  @param	string	$maxlength		Max length for labels (0=no limit)
     *  @return string           		HTML string with select
     */
	function select_departament($selected='',$htmlname='fk_father',$htmloption='',$maxlength=0,$showempty=0)
    {
        global $conf,$langs;

        $langs->load("salary@salary");

        $out='';
        $countryArray=array();
        $label=array();

        $sql = "SELECT rowid, ref as code_iso, ref as label";
        $sql.= " FROM ".MAIN_DB_PREFIX."p_departament";
        $sql.= " WHERE entity = ".$conf->entity;
        $sql.= " ORDER BY ref ASC";

        dol_syslog(get_class($this)."::select_departament sql=".$sql);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            $out.= '<select id="select'.$htmlname.'" class="flat selectpays" name="'.$htmlname.'" '.$htmloption.'>';
	    if ($showempty)
	      {
		$out.= '<option value="-1"';
		if ($selected == -1) $out.= ' selected="selected"';
		$out.= '>&nbsp;</option>';
	      }
	    
            $num = $this->db->num_rows($resql);
            $i = 0;
            if ($num)
            {
                $foundselected=false;

                while ($i < $num)
                {
                    $obj = $this->db->fetch_object($resql);
                    $countryArray[$i]['rowid'] 		= $obj->rowid;
                    $countryArray[$i]['code_iso'] 	= $obj->code_iso;
                    $countryArray[$i]['label']		= ($obj->code_iso && $langs->transnoentitiesnoconv("Departament".$obj->code_iso)!="Departament".$obj->code_iso?$langs->transnoentitiesnoconv("Departament".$obj->code_iso):($obj->label!='-'?$obj->label:''));
                    $label[$i] 	= $countryArray[$i]['label'];
                    $i++;
                }

                array_multisort($label, SORT_ASC, $countryArray);

                foreach ($countryArray as $row)
                {
                    //print 'rr'.$selected.'-'.$row['label'].'-'.$row['code_iso'].'<br>';
                    if ($selected && $selected != '-1' && ($selected == $row['rowid'] || $selected == $row['code_iso'] || $selected == $row['label']) )
                    {
                        $foundselected=true;
                        $out.= '<option value="'.$row['rowid'].'" selected="selected">';
                    }
                    else
                    {
                        $out.= '<option value="'.$row['rowid'].'">';
                    }
                    $out.= dol_trunc($row['label'],$maxlength,'middle');
                    if ($row['code_iso']) $out.= ' ('.$row['code_iso'] . ')';
                    $out.= '</option>';
                }
            }
            $out.= '</select>';
        }
        else
        {
            dol_print_error($this->db);
        }

        return $out;
    }

}
?>
