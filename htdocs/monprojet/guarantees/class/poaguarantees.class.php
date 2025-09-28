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
 *  \file       dev/skeletons/poaguarantees.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2015-02-04 09:28
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Poaguarantees extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='poaguarantees';			//!< Id that identify managed objects
	var $table_element='poaguarantees';		//!< Name of table without prefix where object is stored

    var $id;
    
	var $fk_contrat;
	var $code_guarantee;
	var $date_ini='';
	var $date_fin='';
	var $ref;
	var $issuer;
	var $concept;
	var $amount;
	var $fk_user_create;
	var $date_create='';
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
        
		if (isset($this->fk_contrat)) $this->fk_contrat=trim($this->fk_contrat);
		if (isset($this->code_guarantee)) $this->code_guarantee=trim($this->code_guarantee);
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->issuer)) $this->issuer=trim($this->issuer);
		if (isset($this->concept)) $this->concept=trim($this->concept);
		if (isset($this->amount)) $this->amount=trim($this->amount);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->statut)) $this->statut=trim($this->statut);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."poa_guarantees(";
		
		$sql.= "fk_contrat,";
		$sql.= "code_guarantee,";
		$sql.= "date_ini,";
		$sql.= "date_fin,";
		$sql.= "ref,";
		$sql.= "issuer,";
		$sql.= "concept,";
		$sql.= "amount,";
		$sql.= "fk_user_create,";
		$sql.= "date_create,";
		$sql.= "statut";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->fk_contrat)?'NULL':"'".$this->fk_contrat."'").",";
		$sql.= " ".(! isset($this->code_guarantee)?'NULL':"'".$this->db->escape($this->code_guarantee)."'").",";
		$sql.= " ".(! isset($this->date_ini) || dol_strlen($this->date_ini)==0?'NULL':$this->db->idate($this->date_ini)).",";
		$sql.= " ".(! isset($this->date_fin) || dol_strlen($this->date_fin)==0?'NULL':$this->db->idate($this->date_fin)).",";
		$sql.= " ".(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").",";
		$sql.= " ".(! isset($this->issuer)?'NULL':"'".$this->db->escape($this->issuer)."'").",";
		$sql.= " ".(! isset($this->concept)?'NULL':"'".$this->db->escape($this->concept)."'").",";
		$sql.= " ".(! isset($this->amount)?'NULL':"'".$this->amount."'").",";
		$sql.= " ".(! isset($this->fk_user_create)?'NULL':"'".$this->fk_user_create."'").",";
		$sql.= " ".(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':$this->db->idate($this->date_create)).",";
		$sql.= " ".(! isset($this->statut)?'NULL':"'".$this->statut."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."poa_guarantees");

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
		
		$sql.= " t.fk_contrat,";
		$sql.= " t.code_guarantee,";
		$sql.= " t.date_ini,";
		$sql.= " t.date_fin,";
		$sql.= " t.ref,";
		$sql.= " t.issuer,";
		$sql.= " t.concept,";
		$sql.= " t.amount,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.date_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."poa_guarantees as t";
        $sql.= " WHERE t.rowid = ".$id;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;
                
				$this->fk_contrat = $obj->fk_contrat;
				$this->code_guarantee = $obj->code_guarantee;
				$this->date_ini = $this->db->jdate($obj->date_ini);
				$this->date_fin = $this->db->jdate($obj->date_fin);
				$this->ref = $obj->ref;
				$this->issuer = $obj->issuer;
				$this->concept = $obj->concept;
				$this->amount = $obj->amount;
				$this->fk_user_create = $obj->fk_user_create;
				$this->date_create = $this->db->jdate($obj->date_create);
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
        
		if (isset($this->fk_contrat)) $this->fk_contrat=trim($this->fk_contrat);
		if (isset($this->code_guarantee)) $this->code_guarantee=trim($this->code_guarantee);
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->issuer)) $this->issuer=trim($this->issuer);
		if (isset($this->concept)) $this->concept=trim($this->concept);
		if (isset($this->amount)) $this->amount=trim($this->amount);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->statut)) $this->statut=trim($this->statut);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."poa_guarantees SET";
        
		$sql.= " fk_contrat=".(isset($this->fk_contrat)?$this->fk_contrat:"null").",";
		$sql.= " code_guarantee=".(isset($this->code_guarantee)?"'".$this->db->escape($this->code_guarantee)."'":"null").",";
		$sql.= " date_ini=".(dol_strlen($this->date_ini)!=0 ? "'".$this->db->idate($this->date_ini)."'" : 'null').",";
		$sql.= " date_fin=".(dol_strlen($this->date_fin)!=0 ? "'".$this->db->idate($this->date_fin)."'" : 'null').",";
		$sql.= " ref=".(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").",";
		$sql.= " issuer=".(isset($this->issuer)?"'".$this->db->escape($this->issuer)."'":"null").",";
		$sql.= " concept=".(isset($this->concept)?"'".$this->db->escape($this->concept)."'":"null").",";
		$sql.= " amount=".(isset($this->amount)?$this->amount:"null").",";
		$sql.= " fk_user_create=".(isset($this->fk_user_create)?$this->fk_user_create:"null").",";
		$sql.= " date_create=".(dol_strlen($this->date_create)!=0 ? "'".$this->db->idate($this->date_create)."'" : 'null').",";
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."poa_guarantees";
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

		$object=new Poaguarantees($this->db);

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
		
		$this->fk_contrat='';
		$this->code_guarantee='';
		$this->date_ini='';
		$this->date_fin='';
		$this->ref='';
		$this->issuer='';
		$this->concept='';
		$this->amount='';
		$this->fk_user_create='';
		$this->date_create='';
		$this->tms='';
		$this->statut='';

		
	}

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
			if ($status == -1) return img_picto($langs->trans('Anulled'),'statut8').' '.($type==0 ? $langs->trans('Anulled'):$langs->trans('Reformulation Anulled'));
			if ($status == 0) return img_picto($langs->trans('Notapproved'),'statut5').' '.($type==0 ? $langs->trans('Notapproved'):$langs->trans('Reformulation unapproved'));
			if ($status == 1) return img_picto($langs->trans('Approved'),'statut4').' '.($type==0 ? $langs->trans('Approved'):$langs->trans('Reformulation approved'));
		}
	
		if ($mode == 2)
		{
			if ($status == -1) return img_picto($langs->trans('Anulled'),'statut8').' '.($type==0 ? $langs->trans('Anulled'):$langs->trans('Reformulation Anulled'));
			if ($status == 0) return img_picto($langs->trans('Notvalidated'),'statut0').' '.($type==0 ? $langs->trans('Notvalidated'):$langs->trans('Notvalidated'));
			if ($status == 1) return img_picto($langs->trans('Validated'),'statut1').' '.($type==0 ? $langs->trans('Validated'):$langs->trans('Validated'));
		}
		 
		if ($mode == 3)
		{ //si proceso o no	
			if ($status == 1) return img_picto($langs->trans('Not'),'switch_off');
			if ($status == 2) return img_picto($langs->trans('Yes'),'switch_on');
		}
		return $langs->trans('Unknown');
	}
	
	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$fk_contrat    Id object
	 *  @return int          	<0 if KO, >0 if OK =0 vacio
	 */
	function getlist($fk_contrat)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";
	
		$sql.= " t.fk_contrat,";
		$sql.= " t.code_guarantee,";
		$sql.= " t.date_ini,";
		$sql.= " t.date_fin,";
		$sql.= " t.ref,";
		$sql.= " t.issuer,";
		$sql.= " t.concept,";
		$sql.= " t.amount,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.date_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut";
	
	
		$sql.= " FROM ".MAIN_DB_PREFIX."poa_guarantees as t";
		$sql.= " WHERE t.fk_contrat = ".$fk_contrat;
	
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
					$objnew = new Poaguarantees($this->db);
					
					$objnew->id    = $obj->rowid;
	
					$objnew->fk_contrat = $obj->fk_contrat;
					$objnew->code_guarantee = $obj->code_guarantee;
					$objnew->date_ini = $this->db->jdate($obj->date_ini);
					$objnew->date_fin = $this->db->jdate($obj->date_fin);
					$objnew->ref = $obj->ref;
					$objnew->issuer = $obj->issuer;
					$objnew->concept = $obj->concept;
					$objnew->amount = $obj->amount;
					$objnew->fk_user_create = $obj->fk_user_create;
					$objnew->date_create = $this->db->jdate($obj->date_create);
					$objnew->tms = $this->db->jdate($obj->tms);
					$objnew->statut = $obj->statut;
					$this->array[$obj->rowid] = $objnew;
					$i++;
				}
				$this->db->free($resql);
				return 1;
			}
			else {
				$this->db->free($resql);
				return 0;
			}
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getlist ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$fk_contrat    Id object
	 *  @return int          	<0 if KO, >0 if OK =0 vacio
	 */
	function getlistuser($statut=1)
	{
		global $langs,$conf;
		$sql = "SELECT";
		$sql.= " t.rowid,";
	
		$sql.= " t.fk_contrat,";
		$sql.= " t.code_guarantee,";
		$sql.= " t.date_ini,";
		$sql.= " t.date_fin,";
		$sql.= " t.ref,";
		$sql.= " t.issuer,";
		$sql.= " t.concept,";
		$sql.= " t.amount,";
		$sql.= " t.date_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut,";
	
		$sql.= " pr.nro_preventive, pr.label, pr.fk_user_create ";

		$sql.= " FROM ".MAIN_DB_PREFIX."poa_guarantees as t";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_process_contrat AS p ON t.fk_contrat = p.fk_contrat";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_process AS pp ON p.fk_poa_process = pp.rowid";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_prev AS pr ON pp.fk_poa_prev = pr.rowid";

		$sql.= " WHERE t.statut = ".$statut;
		$sql.= " AND pr.entity = ".$conf->entity;

		dol_syslog(get_class($this)."::getlistuser sql=".$sql, LOG_DEBUG);
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
					$objnew = new Poaguarantees($this->db);
						
					$this->id    = $obj->rowid;
	
					$objnew->fk_contrat = $obj->fk_contrat;
					$objnew->code_guarantee = $obj->code_guarantee;
					$objnew->date_ini = $this->db->jdate($obj->date_ini);
					$objnew->date_fin = $this->db->jdate($obj->date_fin);
					$objnew->ref = $obj->ref;
					$objnew->issuer = $obj->issuer;
					$objnew->concept = $obj->concept;
					$objnew->amount = $obj->amount;
					$objnew->fk_user_create = $obj->fk_user_create;
					$objnew->date_create = $this->db->jdate($obj->date_create);
					$objnew->tms = $this->db->jdate($obj->tms);
					$objnew->statut = $obj->statut;
					$objnew->fk_user_create = $obj->fk_user_create; //preventivo
					$objnew->nro_preventive = $obj->nro_preventive;
					$this->array[$obj->rowid] = $objnew;
					$i++;
				}
				$this->db->free($resql);
				return 1;
			}
			else {
				$this->db->free($resql);
				return 0;
			}
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
