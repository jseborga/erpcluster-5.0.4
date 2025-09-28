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
 *  \file       dev/skeletons/poaworkflow.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2014-10-23 14:58
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Poaworkflow extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='poa_workflow';			//!< Id that identify managed objects
	var $table_element='poa_workflow';		//!< Name of table without prefix where object is stored

    var $id;
    
	var $fk_poa_prev;
	var $deadlines;
	var $contrat;
	var $date_workflow='';
	var $doclink;
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
        
		if (isset($this->fk_poa_prev)) $this->fk_poa_prev=trim($this->fk_poa_prev);
		if (isset($this->deadlines)) $this->deadlines=trim($this->deadlines);
		if (isset($this->contrat)) $this->contrat=trim($this->contrat);
		if (isset($this->doclink)) $this->doclink=trim($this->doclink);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->statut)) $this->statut=trim($this->statut);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."poa_workflow(";
		
		$sql.= "fk_poa_prev,";
		$sql.= "deadlines,";
		$sql.= "contrat,";
		$sql.= "date_workflow,";
		$sql.= "doclink,";
		$sql.= "fk_user_create,";
		$sql.= "statut";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->fk_poa_prev)?'NULL':"'".$this->fk_poa_prev."'").",";
		$sql.= " ".(! isset($this->deadlines)?'NULL':"'".$this->deadlines."'").",";
		$sql.= " ".(! isset($this->contrat)?'NULL':"'".$this->db->escape($this->contrat)."'").",";
		$sql.= " ".(! isset($this->date_workflow) || dol_strlen($this->date_workflow)==0?'NULL':$this->db->idate($this->date_workflow)).",";
		$sql.= " ".(! isset($this->doclink)?'NULL':"'".$this->db->escape($this->doclink)."'").",";
		$sql.= " ".(! isset($this->fk_user_create)?'NULL':"'".$this->fk_user_create."'").",";
		$sql.= " ".(! isset($this->statut)?'NULL':"'".$this->statut."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."poa_workflow");

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
		
		$sql.= " t.fk_poa_prev,";
		$sql.= " t.deadlines,";
		$sql.= " t.contrat,";
		$sql.= " t.date_workflow,";
		$sql.= " t.doclink,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."poa_workflow as t";
	$sql.= " WHERE t.rowid = ".$id;
    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);
		
                $this->id    = $obj->rowid;
                
		$this->fk_poa_prev = $obj->fk_poa_prev;
		$this->deadlines = $obj->deadlines;
		$this->contrat = $obj->contrat;
		$this->date_workflow = $this->db->jdate($obj->date_workflow);
		$this->doclink = $obj->doclink;
		$this->fk_user_create = $obj->fk_user_create;
		$this->tms = $this->db->jdate($obj->tms);
		$this->statut = $obj->statut;
		
		//recuperando la lista detallada
		$this->array_options = $this->getlistdet($obj->rowid);

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
        
		if (isset($this->fk_poa_prev)) $this->fk_poa_prev=trim($this->fk_poa_prev);
		if (isset($this->deadlines)) $this->deadlines=trim($this->deadlines);
		if (isset($this->contrat)) $this->contrat=trim($this->contrat);
		if (isset($this->doclink)) $this->doclink=trim($this->doclink);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->statut)) $this->statut=trim($this->statut);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."poa_workflow SET";
        
		$sql.= " fk_poa_prev=".(isset($this->fk_poa_prev)?$this->fk_poa_prev:"null").",";
		$sql.= " deadlines=".(isset($this->deadlines)?$this->deadlines:"null").",";
		$sql.= " contrat=".(isset($this->contrat)?"'".$this->db->escape($this->contrat)."'":"null").",";
		$sql.= " date_workflow=".(dol_strlen($this->date_workflow)!=0 ? "'".$this->db->idate($this->date_workflow)."'" : 'null').",";
		$sql.= " doclink=".(isset($this->doclink)?"'".$this->db->escape($this->doclink)."'":"null").",";
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."poa_workflow";
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

		$object=new Poaworkflow($this->db);

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
		
		$this->fk_poa_prev='';
		$this->deadlines='';
		$this->contrat='';
		$this->date_workflow='';
		$this->doclink='';
		$this->fk_user_create='';
		$this->tms='';
		$this->statut='';

		
	}

	//modificado
    /**
     *  Load object in memory from the database
     *
     *  @param	int		$fk_poa_prev    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetch_prev($fk_poa_prev)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.fk_poa_prev,";
		$sql.= " t.deadlines,";
		$sql.= " t.contrat,";
		$sql.= " t.date_workflow,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."poa_workflow as t";
        $sql.= " WHERE t.fk_poa_prev = ".$fk_poa_prev;

    	dol_syslog(get_class($this)."::fetch_prev sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
	  {
            if ($this->db->num_rows($resql))
	      {
                $obj = $this->db->fetch_object($resql);
		
                $this->id    = $obj->rowid;
                
		$this->fk_poa_prev = $obj->fk_poa_prev;
		$this->deadlines = $obj->deadlines;
		$this->contrat = $obj->contrat;
		$this->date_workflow = $this->db->jdate($obj->date_workflow);
		$this->doclink = $obj->doclink;
		$this->fk_user_create = $obj->fk_user_create;
		$this->tms = $this->db->jdate($obj->tms);
		$this->statut = $obj->statut;
		//recuperando la lista detallada
		$this->array_options = $this->getlistdet($obj->rowid);
		
	      }
            $this->db->free($resql);
	    
            return 1;
	  }
        else
	  {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::fetch_prev ".$this->error, LOG_ERR);
            return -1;
	  }
    }

    function getlistdet($id)
    {
      global $langs;
      require_once(DOL_DOCUMENT_ROOT."/poa/workflow/class/poaworkflowdet.class.php");
      $objdet = new Poaworkflowdet($this->db);
      
      $objdet->getlist($id);
      if (count($objdet->array)>0)
	return $objdet->array;
      else
	return array();
    }
    
    // /**
    //  *  Load object in memory from the database
    //  *
    //  *  @param	int		$gestion    gestion poa_prev
    //  * @param text              $code      ''; 'next'; 'last'
    //  *  @return int          	<0 if KO, array if OK
    //  */
    // function getlist($gestion,$code='')
    // {
    //   global $langs,$conf;
    //   $sql = "SELECT";
    //   $sql.= " t.rowid,";
      
    //   $sql.= " t.fk_poa_prev,";
    //   $sql.= " t.contrat,";
    //   $sql.= " t.date_workflow,";
    //   $sql.= " t.fk_user_create,";
    //   $sql.= " t.tms,";
    //   $sql.= " t.statut,";

    //   $sql.= " d.code_area_last,";
    //   $sql.= " d.code_area_next,";
    //   $sql.= " d.date_tracking,";
    //   $sql.= " d.detail,";
    //   $sql.= " d.sequen";

    //   $sql.= " FROM ".MAIN_DB_PREFIX."poa_workflow as t";
    //   $sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_prev AS p ON t.fk_poa_prev = p.rowid";
    //   $sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_workflow_det AS d ON d.fk_poa_workflow = t.rowid";
    //   $sql.= " WHERE p.gestion = ".$gestion;
    //   $sql.= " AND p.statut >= 1";
    //   $sql.= " AND d.active = 1";
    //   if ($code == 'next')
    // 	$sql.= " ORDER BY d.code_area_next, d.date_tracking";
    //   elseif ($code == 'last')
    // 	$sql.= " ORDER BY d.code_area_last, d.date_tracking";
    //   else
    // 	$sql.= " ORDER BY d.code_area_next, d.date_tracking";
	
    //   dol_syslog(get_class($this)."::getlist sql=".$sql, LOG_DEBUG);
    //   $resql=$this->db->query($sql);
    //   $aArray = array();
    //   $aArraynext = array();
    //   $aArraylast = array();
    //   if ($resql)
    // 	{
    // 	  $num = $this->db->num_rows($resql);
    // 	  if ($this->db->num_rows($resql))
    // 	    {
    // 	      $i = 0;
    // 	      while ($i < $num)
    // 		{
    // 		  $obj = $this->db->fetch_object($resql);
    // 		  $adata = array();
    // 		  $adata['id']    = $obj->rowid;
		  
    // 		  $adata['fk_poa_prev'] = $obj->fk_poa_prev;
    // 		  $adata['contrat'] = $obj->contrat;
    // 		  $adata['date_workflow'] = $this->db->jdate($obj->date_workflow);
    // 		  $adata['doclink'] = $obj->doclink;
    // 		  $adata['fk_user_create'] = $obj->fk_user_create;
    // 		  $adata['tms'] = $this->db->jdate($obj->tms);
    // 		  $adata['statut'] = $obj->statut;
    // 		  $adata['code_area_last'] = $obj->code_area_last;
    // 		  $adata['code_area_next'] = $obj->code_area_next;
    // 		  $adata['date_tracking'] = $this->db->jdate($obj->date_tracking);
    // 		  $adata['detail'] = $obj->detail;
    // 		  $adata['sequen'] = $obj->sequen;
    // 		  $aArray[$obj->rowid] = $adata;
    // 		  $aArraynext[$obj->code_area_next][$obj->rowid] = $adata;
    // 		  $aArraylast[$obj->code_area_last][$obj->rowid] = $adata;
    // 		  $i++;
    // 		}
    // 	    }
    // 	  $this->db->free($resql);
    // 	  if ($code == 'next')
    // 	    return $aArraynext;
    // 	  elseif ($code == 'last')
    // 	    return $aArraylast;
    // 	  else
    // 	    return $aArray;
    // 	}
    //   else
    // 	{
    // 	  $this->error="Error ".$this->db->lasterror();
    // 	  dol_syslog(get_class($this)."::getlist ".$this->error, LOG_ERR);
    // 	  return -1;
    // 	}
    // }

    	/**
    	 *	Return label of status of object
    	 *
    	 *	@param      int	$mode       0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto
    	 *	@param      int	$type       0=Shell, 1=Buy
    	 *	@return     string      	Label of status
    	 */
    	function getLibStatut($mode=0, $type=0)
    	{
    	  if($type==0)
    	    return $this->LibStatut($this->statut,$mode,$type);
    	  else
    	    return $this->LibStatut($this->statut_ref,$mode,$type);
    	}

    	/**
    	 *	Return label of a given status
    	 *
    	 *	@param      int		$status     Statut
    	 *	@param      int		$mode       0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto
    	 *	@param      int		$type       0=Status "to sell", 1=Status "to buy"
    	 *	@return     string      		Label of status
    	 */
    	function LibStatut($status,$mode=0,$type=0)
    	{
    	  global $langs;
    	  $langs->load('poa@poa');
	  
    	  if ($mode == 0)
    	    {
    	      if ($status == -1) return ($type==0 ? $langs->trans('Annulled'):img_picto($langs->trans('Anulled'),DOL_URL_ROOT.'/poa/img/statenul','',true));
    	      if ($status == 0) return ($type==0 ? $langs->trans('Draft'):img_picto($langs->trans('Pending'),DOL_URL_ROOT.'/poa/img/state0.png','',true));
    	      if ($status == 1) return ($type==0 ? $langs->trans('Approved'):img_picto($langs->trans('Approved'),DOL_URL_ROOT.'/poa/img/state1.png','',true));
    	      if ($status == 2) return ($type==0 ? $langs->trans('Processcompleted'):img_picto($langs->trans('Processcompleted'),DOL_URL_ROOT.'/poa/img/state2.png','',true));
    	    }
    	  return $langs->trans('Unknown');
    	}

}
?>
