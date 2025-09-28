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
 *  \file       dev/skeletons/poaprev.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2014-04-08 14:23
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Poaprev extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='poaprev';			//!< Id that identify managed objects
	var $table_element='poa_prev';		//!< Name of table without prefix where object is stored

    var $id;
    
	var $entity;
	var $gestion;
	var $fk_pac;
	var $fk_area;
	var $label;
	var $nro_preventive;
	var $date_preventive='';
	var $amount;
	var $date_create='';
	var $fk_user_create;
	var $tms='';
	var $statut;
	var $active;
	var $aArray;
	var $array;
	var $ref;


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
		if (isset($this->gestion)) $this->gestion=trim($this->gestion);
		if (isset($this->fk_pac)) $this->fk_pac=trim($this->fk_pac);
		if (isset($this->fk_area)) $this->fk_area=trim($this->fk_area);
		if (isset($this->label)) $this->label=trim($this->label);
		if (isset($this->nro_preventive)) $this->nro_preventive=trim($this->nro_preventive);
		if (isset($this->amount)) $this->amount=trim($this->amount);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->statut)) $this->statut=trim($this->statut);
		if (isset($this->active)) $this->active=trim($this->active);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."poa_prev(";
		
		$sql.= "entity,";
		$sql.= "gestion,";
		$sql.= "fk_pac,";
		$sql.= "fk_area,";
		$sql.= "label,";
		$sql.= "nro_preventive,";
		$sql.= "date_preventive,";
		$sql.= "amount,";
		$sql.= "date_create,";
		$sql.= "fk_user_create,";
		$sql.= "statut,";
		$sql.= "active";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->entity)?'NULL':"'".$this->entity."'").",";
		$sql.= " ".(! isset($this->gestion)?'NULL':"'".$this->gestion."'").",";
		$sql.= " ".(! isset($this->fk_pac)?'NULL':"'".$this->fk_pac."'").",";
		$sql.= " ".(! isset($this->fk_area)?'NULL':"'".$this->fk_area."'").",";
		$sql.= " ".(! isset($this->label)?'NULL':"'".$this->db->escape($this->label)."'").",";
		$sql.= " ".(! isset($this->nro_preventive)?'NULL':"'".$this->nro_preventive."'").",";
		$sql.= " ".(! isset($this->date_preventive) || dol_strlen($this->date_preventive)==0?'NULL':$this->db->idate($this->date_preventive)).",";
		$sql.= " ".(! isset($this->amount)?'NULL':"'".$this->amount."'").",";
		$sql.= " ".(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':$this->db->idate($this->date_create)).",";
		$sql.= " ".(! isset($this->fk_user_create)?'NULL':"'".$this->fk_user_create."'").",";
		$sql.= " ".(! isset($this->statut)?'NULL':"'".$this->statut."'").",";
		$sql.= " ".(! isset($this->active)?'NULL':"'".$this->active."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."poa_prev");

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
		$sql.= " t.gestion,";
		$sql.= " t.fk_pac,";
		$sql.= " t.fk_area,";
		$sql.= " t.label,";
		$sql.= " t.nro_preventive,";
		$sql.= " t.date_preventive,";
		$sql.= " t.amount,";
		$sql.= " t.date_create,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut,";
		$sql.= " t.active";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."poa_prev as t";
        $sql.= " WHERE t.rowid = ".$id;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;
		$this->ref   = $obj->rowid;
				$this->entity = $obj->entity;
				$this->gestion = $obj->gestion;
				$this->fk_pac = $obj->fk_pac;
				$this->fk_area = $obj->fk_area;
				$this->label = $obj->label;
				$this->nro_preventive = $obj->nro_preventive;
				$this->date_preventive = $this->db->jdate($obj->date_preventive);
				$this->amount = $obj->amount;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->fk_user_create = $obj->fk_user_create;
				$this->tms = $this->db->jdate($obj->tms);
				$this->statut = $obj->statut;
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
		if (isset($this->gestion)) $this->gestion=trim($this->gestion);
		if (isset($this->fk_pac)) $this->fk_pac=trim($this->fk_pac);
		if (isset($this->fk_area)) $this->fk_area=trim($this->fk_area);
		if (isset($this->label)) $this->label=trim($this->label);
		if (isset($this->nro_preventive)) $this->nro_preventive=trim($this->nro_preventive);
		if (isset($this->amount)) $this->amount=trim($this->amount);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->statut)) $this->statut=trim($this->statut);
		if (isset($this->active)) $this->active=trim($this->active);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."poa_prev SET";
        
		$sql.= " entity=".(isset($this->entity)?$this->entity:"null").",";
		$sql.= " gestion=".(isset($this->gestion)?$this->gestion:"null").",";
		$sql.= " fk_pac=".(isset($this->fk_pac)?$this->fk_pac:"null").",";
		$sql.= " fk_area=".(isset($this->fk_area)?$this->fk_area:"null").",";
		$sql.= " label=".(isset($this->label)?"'".$this->db->escape($this->label)."'":"null").",";
		$sql.= " nro_preventive=".(isset($this->nro_preventive)?$this->nro_preventive:"null").",";
		$sql.= " date_preventive=".(dol_strlen($this->date_preventive)!=0 ? "'".$this->db->idate($this->date_preventive)."'" : 'null').",";
		$sql.= " amount=".(isset($this->amount)?$this->amount:"null").",";
		$sql.= " date_create=".(dol_strlen($this->date_create)!=0 ? "'".$this->db->idate($this->date_create)."'" : 'null').",";
		$sql.= " fk_user_create=".(isset($this->fk_user_create)?$this->fk_user_create:"null").",";
		$sql.= " tms=".(dol_strlen($this->tms)!=0 ? "'".$this->db->idate($this->tms)."'" : 'null').",";
		$sql.= " statut=".(isset($this->statut)?$this->statut:"null").",";
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."poa_prev";
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

		$object=new Poaprev($this->db);

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
		$this->gestion='';
		$this->fk_pac='';
		$this->fk_area='';
		$this->label='';
		$this->nro_preventive='';
		$this->date_preventive='';
		$this->amount='';
		$this->date_create='';
		$this->fk_user_create='';
		$this->tms='';
		$this->statut='';
		$this->active='';

		
	}

	//MODIFICADO
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
	      if ($status == -1) return ($type==0 ? $langs->trans('Annulled'):img_picto($langs->trans('Anulled'),DOL_URL_ROOT.'/poa/img/anu.png','',true));
	      if ($status == 0) return ($type==0 ? $langs->trans('Draft'):img_picto($langs->trans('Pending'),DOL_URL_ROOT.'/poa/img/pen.png','',true));
	      if ($status == 1) return ($type==0 ? $langs->trans('Preventive'):img_picto($langs->trans('Preventive'),DOL_URL_ROOT.'/poa/img/pre.png','',true));
	      if ($status == 2) return ($type==0 ? $langs->trans('Committed'):img_picto($langs->trans('Committed'),DOL_URL_ROOT.'/poa/img/com.png','',true));
	      if ($status == 3) return ($type==0 ? $langs->trans('Accrued'):img_picto($langs->trans('Accrued'),DOL_URL_ROOT.'/poa/img/dev.png','',true));
	      if ($status == 4) return ($type==0 ? $langs->trans('Paid'):img_picto($langs->trans('Paid'),DOL_URL_ROOT.'/poa/img/pag.png','',true));
	    }
	  if ($mode == 1)
	    {
	      if ($status == -1) return ($type==0 ? $langs->trans('Annulled'):img_picto($langs->trans('Anulled'),DOL_URL_ROOT.'/poa/img/anu.png','',true).' '.$langs->trans('Anulled'));
	      if ($status == 0) return ($type==0 ? $langs->trans('Draft'):img_picto($langs->trans('Pending'),DOL_URL_ROOT.'/poa/img/pen.png','',true).' '.$langs->trans('Pending'));
	      if ($status == 1) return ($type==0 ? $langs->trans('Preventive'):img_picto($langs->trans('Preventive'),DOL_URL_ROOT.'/poa/img/pre.png','',true).' '.$langs->trans('Preventive'));
	      if ($status == 2) return ($type==0 ? $langs->trans('Committed'):img_picto($langs->trans('Committed'),DOL_URL_ROOT.'/poa/img/com.png','',true).' '.$langs->trans('Committed'));
	      if ($status == 3) return ($type==0 ? $langs->trans('Accrued'):img_picto($langs->trans('Accrued'),DOL_URL_ROOT.'/poa/img/dev.png','',true).' '.$langs->trans('Accrued'));
	      if ($status == 4) return ($type==0 ? $langs->trans('Paid'):img_picto($langs->trans('Paid'),DOL_URL_ROOT.'/poa/img/pag.png','',true).' '.$langs->trans('Paid'));
	    }

	  if ($mode == 9)
	    {
	      if ($status == -1) return ($type==0 ? $langs->trans('Annulled'):img_picto($langs->trans('Anulled'),DOL_URL_ROOT.'/poa/img/statenul','',true).' '.$langs->trans('Anulled'));
	      if ($status == 0) return ($type==0 ? $langs->trans('Draft'):img_picto($langs->trans('Pending'),DOL_URL_ROOT.'/poa/img/state0.png','',true).' '.$langs->trans('Pending'));
	      if ($status == 1) return ($type==0 ? $langs->trans('Preventive'):img_picto($langs->trans('Preventive'),DOL_URL_ROOT.'/poa/img/state1.png','',true).' '.$langs->trans('Preventive'));
	      if ($status == 2) return ($type==0 ? $langs->trans('Committed'):img_picto($langs->trans('Committed'),DOL_URL_ROOT.'/poa/img/state2.png','',true).' '.$langs->trans('Committed'));
	      if ($status == 3) return ($type==0 ? $langs->trans('Accrued'):img_picto($langs->trans('Accrued'),DOL_URL_ROOT.'/poa/img/state3.png','',true).' '.$langs->trans('Accrued'));
	      if ($status == 4) return ($type==0 ? $langs->trans('Paid'):img_picto($langs->trans('Paid'),DOL_URL_ROOT.'/poa/img/state4.png','',true).' '.$langs->trans('Paid'));
	    }

	  if ($mode == 2)
	    {
	      if ($status == 0) return img_picto($langs->trans('Notapproved'),'statut5').' '.($type==0 ? $langs->trans('Notapproved'):$langs->trans('Reformulation unapproved'));
	      if ($status == 1) return img_picto($langs->trans('Approved'),'statut4').' '.($type==0 ? $langs->trans('Approved'):$langs->trans('Reformulation approved'));
	    }
	  
	  if ($mode == 3)
	    { //si proceso o no
	      if ($status == 1) return img_picto($langs->trans('Not'),'switch_off');

	      if ($status == 2) return img_picto($langs->trans('Yes'),'switch_on');
	    }
	  return $langs->trans('Unknown');
	}

    /**
     *  Return combo list of activated countries, into language of user
     *
     *  @param	string	$selected       Id or Code or Label of preselected country
     *  @param  string	$htmlname       Name of html select object
     *  @param  string	$htmloption     Options html on select object
     *  @param	string	$maxlength	Max length for labels (0=no limit)
     *  @param	string	$showempty	View space labels (0=no view)

     *  @return string           		HTML string with select
     */
	function select_poa_prev($selected='',$htmlname='fk_poa_prev',$htmloption='',$maxlength=0,$showempty=0,$gestion='',$fk_area='',$statut=1)
	{
	  global $conf,$langs;
	  
	  $langs->load("poa@poa");
	  if (empty($gestion)) $gestion = date('Y');
	  $out='';
	  $countryArray=array();
	  $label=array();
	  
	  $sql = "SELECT c.rowid, c.nro_preventive AS label, c.label as code_iso ";
	  $sql.= " FROM ".MAIN_DB_PREFIX."poa_prev AS c ";
	  
	  $sql.= " WHERE c.entity = ".$conf->entity;
	  $sql.= " AND c.statut = ".$statut;
	  $sql.= " AND c.gestion = ".$gestion;

	  if ($fk_area)
	    $sql.= " AND c.fk_area = ".$fk_area;
	  dol_syslog(get_class($this)."::select_poa_prev sql=".$sql);
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
		      
		      $countryArray[$i]['label']		= ($obj->code_iso && $langs->transnoentitiesnoconv("Preventive".$obj->code_iso)!="Preventive".$obj->code_iso?$langs->transnoentitiesnoconv("Preventive".$obj->code_iso):($obj->label!='-'?$obj->label:''));
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
		      //$out.= dol_trunc($row['label'],$maxlength,'middle');
		      $out.= $row['label'];
		      if ($row['code_iso']) $out.= ' ('.dol_trunc($row['code_iso'],$maxlength,'middle') . ')';
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
	

    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
	function search($search,$gestion,$active='')
    {
      global $langs,$conf;
      if (empty($search))
	return -1;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.entity,";
		$sql.= " t.gestion,";
		$sql.= " t.fk_pac,";
		$sql.= " t.fk_area,";
		$sql.= " t.label,";
		$sql.= " t.nro_preventive,";
		$sql.= " t.date_preventive,";
		$sql.= " t.amount,";
		$sql.= " t.date_create,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut,";
		$sql.= " t.active";
		
        $sql.= " FROM ".MAIN_DB_PREFIX."poa_prev as t";
        $sql.= " WHERE t.entity = ".$conf->entity;
	$sql.= " AND t.gestion = ".$gestion;
	if (!empty($statut))
	  $sql.= " AND t.active = ".$active;
	if ($search)
	  {
	    $sql.= " AND (t.nro_preventive like '%".$search."%' OR t.label like '%".$search."%')";
	  }

    	dol_syslog(get_class($this)."::search sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
	  $num = $this->db->num_rows($resql);
            if ($num)
            {
	      $i = 0;
	      $this->aArray = array();
	      while ($i < $num)
		{
		  $obj = $this->db->fetch_object($resql);
		  
		  $this->aArray[$obj->rowid] = $obj->rowid;
		  $i++;
		}               
            }
            $this->db->free($resql);
            return 1;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::search ".$this->error, LOG_ERR);
            return -1;
        }
    }


    /**
     *  Load object in memory from the database
     * lista los preventivos segun pac
     *  @param	int		$fk_pac    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetch_pac($fk_pac)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.entity,";
		$sql.= " t.gestion,";
		$sql.= " t.fk_pac,";
		$sql.= " t.fk_area,";
		$sql.= " t.label,";
		$sql.= " t.nro_preventive,";
		$sql.= " t.date_preventive,";
		$sql.= " t.amount,";
		$sql.= " t.date_create,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut,";
		$sql.= " t.active";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."poa_prev as t";
        $sql.= " WHERE t.fk_pac = ".$fk_pac;

    	dol_syslog(get_class($this)."::fetch_pac sql=".$sql, LOG_DEBUG);
	$this->array = array();
        $resql=$this->db->query($sql);
        if ($resql)
	  {
	    $num = $this->db->num_rows($resql);
            if ($this->db->num_rows($resql))
	      {
		$i = 0;
		while ($i < $num)
		  {
		    $obj = $this->db->fetch_object($resql);
		    $objnew = new Poaprev($this->db);

		    $objnew->id    = $obj->rowid;                
		    $objnew->entity = $obj->entity;
		    $objnew->gestion = $obj->gestion;
		    $objnew->fk_pac = $obj->fk_pac;
		    $objnew->fk_area = $obj->fk_area;
		    $objnew->label = $obj->label;
		    $objnew->nro_preventive = $obj->nro_preventive;
		    $objnew->date_preventive = $this->db->jdate($obj->date_preventive);
		    $objnew->amount = $obj->amount;
		    $objnew->date_create = $this->db->jdate($obj->date_create);
		    $objnew->fk_user_create = $obj->fk_user_create;
		    $objnew->tms = $this->db->jdate($obj->tms);
		    $objnew->statut = $obj->statut;
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
            dol_syslog(get_class($this)."::fetch_pac ".$this->error, LOG_ERR);
            return -1;
	  }
    }


    /**
     *  Load object in memory from the database
     *
     *  @param	int		$gestion    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function getlist($gestion,$fk_user='',$fk_area=0)
    {
      global $langs;
      $sql = "SELECT";
      $sql.= " t.rowid,";
      
      $sql.= " t.entity,";
      $sql.= " t.gestion,";
      $sql.= " t.fk_pac,";
      $sql.= " t.fk_area,";
      $sql.= " t.label,";
      $sql.= " t.nro_preventive,";
      $sql.= " t.date_preventive,";
      $sql.= " t.amount,";
      $sql.= " t.date_create,";
      $sql.= " t.fk_user_create,";
      $sql.= " t.tms,";
      $sql.= " t.statut,";
      $sql.= " t.active";
      
      $sql.= " FROM ".MAIN_DB_PREFIX."poa_prev as t";
      $sql.= " WHERE t.gestion = ".$gestion;
      if ($fk_user>0)
	$sql.= " AND t.fk_user_create = ".$fk_user;
      if ($fk_area>0)
	$sql.= " AND t.fk_area = ".$fk_area;

      $sql.= " AND t.statut > 0";
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
		    $objnew = new Poaprev($this->db);
		    $objnew->id    = $obj->rowid;
		    $objnew->ref   = $obj->rowid;
		    $objnew->entity = $obj->entity;
		    $objnew->gestion = $obj->gestion;
		    $objnew->fk_pac = $obj->fk_pac;
		    $objnew->fk_area = $obj->fk_area;
		    $objnew->label = $obj->label;
		    $objnew->nro_preventive = $obj->nro_preventive;
		    $objnew->date_preventive = $this->db->jdate($obj->date_preventive);
		    $objnew->amount = $obj->amount;
		    $objnew->date_create = $this->db->jdate($obj->date_create);
		    $objnew->fk_user_create = $obj->fk_user_create;
		    $objnew->tms = $this->db->jdate($obj->tms);
		    $objnew->statut = $obj->statut;
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

}
?>
