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
 *  \file       dev/skeletons/contabvision.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2013-12-18 18:32
 */

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Contabvision // extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	//var $element='contabvision';			//!< Id that identify managed objects
	//var $table_element='contabvision';	//!< Name of table without prefix where object is stored

    var $id;
    
	var $entity;
	var $ref;
	var $sequence;
	var $account;
	var $account_sup;
	var $detail_managment;
	var $cta_normal;
	var $cta_column;
	var $cta_class;
	var $cta_identifier;
	var $cta_operation;
	var $cta_balances;
	var $cta_totalvis;
	var $name_vision;
	var $line;
	var $fk_accountini;
	var $fk_accountfin;
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
		if (isset($this->sequence)) $this->sequence=trim($this->sequence);
		if (isset($this->account)) $this->account=trim($this->account);
		if (isset($this->account_sup)) $this->account_sup=trim($this->account_sup);
		if (isset($this->detail_managment)) $this->detail_managment=trim($this->detail_managment);
		if (isset($this->cta_normal)) $this->cta_normal=trim($this->cta_normal);
		if (isset($this->cta_column)) $this->cta_column=trim($this->cta_column);
		if (isset($this->cta_class)) $this->cta_class=trim($this->cta_class);
		if (isset($this->cta_identifier)) $this->cta_identifier=trim($this->cta_identifier);
		if (isset($this->cta_operation)) $this->cta_operation=trim($this->cta_operation);
		if (isset($this->cta_balances)) $this->cta_balances=trim($this->cta_balances);
		if (isset($this->cta_totalvis)) $this->cta_totalvis=trim($this->cta_totalvis);
		if (isset($this->name_vision)) $this->name_vision=trim($this->name_vision);
		if (isset($this->line)) $this->line=trim($this->line);
		if (isset($this->fk_accountini)) $this->fk_accountini=trim($this->fk_accountini);
		if (isset($this->fk_accountfin)) $this->fk_accountfin=trim($this->fk_accountfin);
		if (isset($this->statut)) $this->statut=trim($this->statut);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."contab_vision(";
		
		$sql.= "entity,";
		$sql.= "ref,";
		$sql.= "sequence,";
		$sql.= "account,";
		$sql.= "account_sup,";
		$sql.= "detail_managment,";
		$sql.= "cta_normal,";
		$sql.= "cta_column,";
		$sql.= "cta_class,";
		$sql.= "cta_identifier,";
		$sql.= "cta_operation,";
		$sql.= "cta_balances,";
		$sql.= "cta_totalvis,";
		$sql.= "name_vision,";
		$sql.= "line,";
		$sql.= "fk_accountini,";
		$sql.= "fk_accountfin,";
		$sql.= "statut";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->entity)?'NULL':"'".$this->entity."'").",";
		$sql.= " ".(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").",";
		$sql.= " ".(! isset($this->sequence)?'NULL':"'".$this->sequence."'").",";
		$sql.= " ".(! isset($this->account)?'NULL':"'".$this->db->escape($this->account)."'").",";
		$sql.= " ".(! isset($this->account_sup)?'NULL':"'".$this->db->escape($this->account_sup)."'").",";
		$sql.= " ".(! isset($this->detail_managment)?'NULL':"'".$this->db->escape($this->detail_managment)."'").",";
		$sql.= " ".(! isset($this->cta_normal)?'NULL':"'".$this->db->escape($this->cta_normal)."'").",";
		$sql.= " ".(! isset($this->cta_column)?'NULL':"'".$this->cta_column."'").",";
		$sql.= " ".(! isset($this->cta_class)?'NULL':"'".$this->cta_class."'").",";
		$sql.= " ".(! isset($this->cta_identifier)?'NULL':"'".$this->db->escape($this->cta_identifier)."'").",";
		$sql.= " ".(! isset($this->cta_operation)?'NULL':"'".$this->cta_operation."'").",";
		$sql.= " ".(! isset($this->cta_balances)?'NULL':"'".$this->cta_balances."'").",";
		$sql.= " ".(! isset($this->cta_totalvis)?'NULL':"'".$this->cta_totalvis."'").",";
		$sql.= " ".(! isset($this->name_vision)?'NULL':"'".$this->db->escape($this->name_vision)."'").",";
		$sql.= " ".(! isset($this->line)?'NULL':"'".$this->db->escape($this->line)."'").",";
		$sql.= " ".(! isset($this->fk_accountini)?'NULL':"'".$this->fk_accountini."'").",";
		$sql.= " ".(! isset($this->fk_accountfin)?'NULL':"'".$this->fk_accountfin."'").",";
		$sql.= " ".(! isset($this->statut)?'NULL':"'".$this->statut."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."contab_vision");

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
		$sql.= " t.sequence,";
		$sql.= " t.account,";
		$sql.= " t.account_sup,";
		$sql.= " t.detail_managment,";
		$sql.= " t.cta_normal,";
		$sql.= " t.cta_column,";
		$sql.= " t.cta_class,";
		$sql.= " t.cta_identifier,";
		$sql.= " t.cta_operation,";
		$sql.= " t.cta_balances,";
		$sql.= " t.cta_totalvis,";
		$sql.= " t.name_vision,";
		$sql.= " t.line,";
		$sql.= " t.fk_accountini,";
		$sql.= " t.fk_accountfin,";
		$sql.= " t.statut";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."contab_vision as t";
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
				$this->ref = $obj->ref;
				$this->sequence = $obj->sequence;
				$this->account = $obj->account;
				$this->account_sup = $obj->account_sup;
				$this->detail_managment = $obj->detail_managment;
				$this->cta_normal = $obj->cta_normal;
				$this->cta_column = $obj->cta_column;
				$this->cta_class = $obj->cta_class;
				$this->cta_identifier = $obj->cta_identifier;
				$this->cta_operation = $obj->cta_operation;
				$this->cta_balances = $obj->cta_balances;
				$this->cta_totalvis = $obj->cta_totalvis;
				$this->name_vision = $obj->name_vision;
				$this->line = $obj->line;
				$this->fk_accountini = $obj->fk_accountini;
				$this->fk_accountfin = $obj->fk_accountfin;
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
		if (isset($this->sequence)) $this->sequence=trim($this->sequence);
		if (isset($this->account)) $this->account=trim($this->account);
		if (isset($this->account_sup)) $this->account_sup=trim($this->account_sup);
		if (isset($this->detail_managment)) $this->detail_managment=trim($this->detail_managment);
		if (isset($this->cta_normal)) $this->cta_normal=trim($this->cta_normal);
		if (isset($this->cta_column)) $this->cta_column=trim($this->cta_column);
		if (isset($this->cta_class)) $this->cta_class=trim($this->cta_class);
		if (isset($this->cta_identifier)) $this->cta_identifier=trim($this->cta_identifier);
		if (isset($this->cta_operation)) $this->cta_operation=trim($this->cta_operation);
		if (isset($this->cta_balances)) $this->cta_balances=trim($this->cta_balances);
		if (isset($this->cta_totalvis)) $this->cta_totalvis=trim($this->cta_totalvis);
		if (isset($this->name_vision)) $this->name_vision=trim($this->name_vision);
		if (isset($this->line)) $this->line=trim($this->line);
		if (isset($this->fk_accountini)) $this->fk_accountini=trim($this->fk_accountini);
		if (isset($this->fk_accountfin)) $this->fk_accountfin=trim($this->fk_accountfin);
		if (isset($this->statut)) $this->statut=trim($this->statut);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."contab_vision SET";
        
		$sql.= " entity=".(isset($this->entity)?$this->entity:"null").",";
		$sql.= " ref=".(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").",";
		$sql.= " sequence=".(isset($this->sequence)?$this->sequence:"null").",";
		$sql.= " account=".(isset($this->account)?"'".$this->db->escape($this->account)."'":"null").",";
		$sql.= " account_sup=".(isset($this->account_sup)?"'".$this->db->escape($this->account_sup)."'":"null").",";
		$sql.= " detail_managment=".(isset($this->detail_managment)?"'".$this->db->escape($this->detail_managment)."'":"null").",";
		$sql.= " cta_normal=".(isset($this->cta_normal)?"'".$this->db->escape($this->cta_normal)."'":"null").",";
		$sql.= " cta_column=".(isset($this->cta_column)?$this->cta_column:"null").",";
		$sql.= " cta_class=".(isset($this->cta_class)?$this->cta_class:"null").",";
		$sql.= " cta_identifier=".(isset($this->cta_identifier)?"'".$this->db->escape($this->cta_identifier)."'":"null").",";
		$sql.= " cta_operation=".(isset($this->cta_operation)?$this->cta_operation:"null").",";
		$sql.= " cta_balances=".(isset($this->cta_balances)?$this->cta_balances:"null").",";
		$sql.= " cta_totalvis=".(isset($this->cta_totalvis)?$this->cta_totalvis:"null").",";
		$sql.= " name_vision=".(isset($this->name_vision)?"'".$this->db->escape($this->name_vision)."'":"null").",";
		$sql.= " line=".(isset($this->line)?"'".$this->db->escape($this->line)."'":"null").",";
		$sql.= " fk_accountini=".(isset($this->fk_accountini)?$this->fk_accountini:"null").",";
		$sql.= " fk_accountfin=".(isset($this->fk_accountfin)?$this->fk_accountfin:"null").",";
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."contab_vision";
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

		$object=new Contabvision($this->db);

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
		$this->sequence='';
		$this->account='';
		$this->account_sup='';
		$this->detail_managment='';
		$this->cta_normal='';
		$this->cta_column='';
		$this->cta_class='';
		$this->cta_identifier='';
		$this->cta_operation='';
		$this->cta_balances='';
		$this->cta_totalvis='';
		$this->name_vision='';
		$this->line='';
		$this->fk_accountini='';
		$this->fk_accountfin='';
		$this->statut='';

		
	}

    /**
     *    	Return label of status of proposal (draft, validated, ...)
     *
     *    	@param      int			$mode        0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto
     *    	@return     string		Label
     */
    function getLibStatut($mode=0)
    {
        return $this->LibStatut($this->statut,$mode);
    }

    /**
     *    	Return label of a status (draft, validated, ...)
     *
     *    	@param      int			$statut		id statut
     *    	@param      int			$mode      	0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto
     *    	@return     string		Label
     */
    function LibStatut($statut,$mode=1)
    {
        global $langs;
        $langs->load("propal");

        if ($mode == 0)
        {
            return $this->labelstatut[$statut];
        }
        if ($mode == 1)
        {
            return $this->labelstatut_short[$statut];
        }
        if ($mode == 2)
        {
            if ($statut==0) return img_picto($langs->trans('PropalStatusDraftShort'),'statut0').' '.$this->labelstatut_short[$statut];
            if ($statut==1) return img_picto($langs->trans('PropalStatusOpenedShort'),'statut1').' '.$this->labelstatut_short[$statut];
            if ($statut==2) return img_picto($langs->trans('PropalStatusSignedShort'),'statut3').' '.$this->labelstatut_short[$statut];
            if ($statut==3) return img_picto($langs->trans('PropalStatusNotSignedShort'),'statut5').' '.$this->labelstatut_short[$statut];
            if ($statut==4) return img_picto($langs->trans('PropalStatusBilledShort'),'statut6').' '.$this->labelstatut_short[$statut];
        }
        if ($mode == 3)
        {
            if ($statut==0) return img_picto($langs->trans('PropalStatusDraftShort'),'statut0');
            if ($statut==1) return img_picto($langs->trans('PropalStatusOpenedShort'),'statut1');
            if ($statut==2) return img_picto($langs->trans('PropalStatusSignedShort'),'statut3');
            if ($statut==3) return img_picto($langs->trans('PropalStatusNotSignedShort'),'statut5');
            if ($statut==4) return img_picto($langs->trans('PropalStatusBilledShort'),'statut6');
        }
        if ($mode == 4)
        {
            if ($statut==0) return img_picto($langs->trans('PropalStatusDraft'),'statut0').' '.$this->labelstatut[$statut];
            if ($statut==1) return img_picto($langs->trans('PropalStatusOpened'),'statut1').' '.$this->labelstatut[$statut];
            if ($statut==2) return img_picto($langs->trans('PropalStatusSigned'),'statut3').' '.$this->labelstatut[$statut];
            if ($statut==3) return img_picto($langs->trans('PropalStatusNotSigned'),'statut5').' '.$this->labelstatut[$statut];
            if ($statut==4) return img_picto($langs->trans('PropalStatusBilled'),'statut6').' '.$this->labelstatut[$statut];
        }
        if ($mode == 5)
        {
            if ($statut==0) return '<span class="hideonsmartphone">'.$this->labelstatut_short[$statut].' </span>'.img_picto($langs->trans('PropalStatusDraftShort'),'statut0');
            if ($statut==1) return '<span class="hideonsmartphone">'.$this->labelstatut_short[$statut].' </span>'.img_picto($langs->trans('PropalStatusOpenedShort'),'statut1');
            if ($statut==2) return '<span class="hideonsmartphone">'.$this->labelstatut_short[$statut].' </span>'.img_picto($langs->trans('PropalStatusSignedShort'),'statut3');
            if ($statut==3) return '<span class="hideonsmartphone">'.$this->labelstatut_short[$statut].' </span>'.img_picto($langs->trans('PropalStatusNotSignedShort'),'statut5');
            if ($statut==4) return '<span class="hideonsmartphone">'.$this->labelstatut_short[$statut].' </span>'.img_picto($langs->trans('PropalStatusBilledShort'),'statut6');
        }
    }

    function sequence_ult($ref)
    {
      global $conf;
      $sql = "SELECT s.rowid, s.name_vision, s.sequence, s.account ";
      $sql.= " FROM ".MAIN_DB_PREFIX."contab_vision as s";
      $sql.= " WHERE s.ref = '".$ref."'";
      $sql.= " AND entity = ".$conf->entity;
      $sql.= " ORDER BY sequence DESC ";
      $result=$this->db->query($sql);
      if ($result)
	{
	  $num = $this->db->num_rows($result);
	  if ($this->db->num_rows($result))
	    {
	      $obj = $this->db->fetch_object($result);
	      $valor = $obj->name_vision;
	      $sequence = $obj->sequence + 10;
	      $account = $obj->account + 1;
	    }
	  else
	    {
	      $valor = '';
	      $sequence= '';
	      $account = '';
	    }
	}
      else
	{
	  $valor = '';
	  $sequence= '';
	  $account = '';
	}
      return array($valor,$sequence,$account);
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
	function select_vision($selected='',$htmlname='fk_vision',$htmloption='',$maxlength=0,$showempty=0)
    {
        global $conf,$langs;
        $langs->load("contab@contab");

        $out='';
        $countryArray=array();
        $label=array();
	
	$sql = "SELECT rowid, ref as code_iso, name_vision as label";
        $sql.= " FROM ".MAIN_DB_PREFIX."contab_vision";
        $sql.= " WHERE statut = 1";
        $sql.= " ORDER BY ref ASC";
        dol_syslog(get_class($this)."::select_vision sql=".$sql);
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
                    $countryArray[$i]['label']		= ($obj->code_iso && $langs->transnoentitiesnoconv("Accounting".$obj->code_iso)!="Accounting".$obj->code_iso?$langs->transnoentitiesnoconv("Accounting".$obj->code_iso):($obj->label!='-'?$obj->label:''));
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
