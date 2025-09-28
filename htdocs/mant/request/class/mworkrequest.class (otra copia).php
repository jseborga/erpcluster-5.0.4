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
 *  \file       dev/skeletons/mworkrequest.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2015-08-27 16:08
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Mworkrequest extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='m_work_request';			//!< Id that identify managed objects
	var $table_element='m_work_request';		//!< Name of table without prefix where object is stored

    var $id;
    
	var $entity;
	var $ref;
	var $date_create='';
	var $fk_member;
	var $fk_departament;
	var $fk_equipment;
	var $fk_property;
	var $fk_location;
	var $fk_soc;
	var $email;
	var $internal;
	var $detail_problem;
	var $address_ip;
	var $fk_user_assign;
	var $date_assign='';
	var $tokenreg;
	var $tms='';
	var $statut;
	var $description_confirm;
	var $statut_job;

    


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
		if (isset($this->fk_member)) $this->fk_member=trim($this->fk_member);
		if (isset($this->fk_departament)) $this->fk_departament=trim($this->fk_departament);
		if (isset($this->fk_equipment)) $this->fk_equipment=trim($this->fk_equipment);
		if (isset($this->fk_property)) $this->fk_property=trim($this->fk_property);
		if (isset($this->fk_location)) $this->fk_location=trim($this->fk_location);
		if (isset($this->fk_soc)) $this->fk_soc=trim($this->fk_soc);
		if (isset($this->email)) $this->email=trim($this->email);
		if (isset($this->internal)) $this->internal=trim($this->internal);
		if (isset($this->detail_problem)) $this->detail_problem=trim($this->detail_problem);
		if (isset($this->address_ip)) $this->address_ip=trim($this->address_ip);
		if (isset($this->fk_user_assign)) $this->fk_user_assign=trim($this->fk_user_assign);
		if (isset($this->tokenreg)) $this->tokenreg=trim($this->tokenreg);
		if (isset($this->statut)) $this->statut=trim($this->statut);
		if (isset($this->description_confirm)) $this->description_confirm=trim($this->description_confirm);
		if (isset($this->statut_job)) $this->statut_job=trim($this->statut_job);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."m_work_request(";
		
		$sql.= "entity,";
		$sql.= "ref,";
		$sql.= "date_create,";
		$sql.= "fk_member,";
		$sql.= "fk_departament,";
		$sql.= "fk_equipment,";
		$sql.= "fk_property,";
		$sql.= "fk_location,";
		$sql.= "fk_soc,";
		$sql.= "email,";
		$sql.= "internal,";
		$sql.= "detail_problem,";
		$sql.= "address_ip,";
		$sql.= "fk_user_assign,";
		$sql.= "date_assign,";
		$sql.= "tokenreg,";
		$sql.= "statut,";
		$sql.= "description_confirm,";
		$sql.= "statut_job";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->entity)?'NULL':"'".$this->entity."'").",";
		$sql.= " ".(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").",";
		$sql.= " ".(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':$this->db->idate($this->date_create)).",";
		$sql.= " ".(! isset($this->fk_member)?'NULL':"'".$this->fk_member."'").",";
		$sql.= " ".(! isset($this->fk_departament)?'NULL':"'".$this->fk_departament."'").",";
		$sql.= " ".(! isset($this->fk_equipment)?'NULL':"'".$this->fk_equipment."'").",";
		$sql.= " ".(! isset($this->fk_property)?'NULL':"'".$this->fk_property."'").",";
		$sql.= " ".(! isset($this->fk_location)?'NULL':"'".$this->fk_location."'").",";
		$sql.= " ".(! isset($this->fk_soc)?'NULL':"'".$this->fk_soc."'").",";
		$sql.= " ".(! isset($this->email)?'NULL':"'".$this->db->escape($this->email)."'").",";
		$sql.= " ".(! isset($this->internal)?'NULL':"'".$this->internal."'").",";
		$sql.= " ".(! isset($this->detail_problem)?'NULL':"'".$this->db->escape($this->detail_problem)."'").",";
		$sql.= " ".(! isset($this->address_ip)?'NULL':"'".$this->db->escape($this->address_ip)."'").",";
		$sql.= " ".(! isset($this->fk_user_assign)?'NULL':"'".$this->fk_user_assign."'").",";
		$sql.= " ".(! isset($this->date_assign) || dol_strlen($this->date_assign)==0?'NULL':$this->db->idate($this->date_assign)).",";
		$sql.= " ".(! isset($this->tokenreg)?'NULL':"'".$this->db->escape($this->tokenreg)."'").",";
		$sql.= " ".(! isset($this->statut)?'NULL':"'".$this->statut."'").",";
		$sql.= " ".(! isset($this->description_confirm)?'NULL':"'".$this->db->escape($this->description_confirm)."'").",";
		$sql.= " ".(! isset($this->statut_job)?'NULL':"'".$this->statut_job."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."m_work_request");

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
		$sql.= " t.date_create,";
		$sql.= " t.fk_member,";
		$sql.= " t.fk_departament,";
		$sql.= " t.fk_equipment,";
		$sql.= " t.fk_property,";
		$sql.= " t.fk_location,";
		$sql.= " t.fk_soc,";
		$sql.= " t.email,";
		$sql.= " t.internal,";
		$sql.= " t.detail_problem,";
		$sql.= " t.address_ip,";
		$sql.= " t.fk_user_assign,";
		$sql.= " t.date_assign,";
		$sql.= " t.tokenreg,";
		$sql.= " t.tms,";
		$sql.= " t.statut,";
		$sql.= " t.description_confirm,";
		$sql.= " t.statut_job";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."m_work_request as t";
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
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->fk_member = $obj->fk_member;
				$this->fk_departament = $obj->fk_departament;
				$this->fk_equipment = $obj->fk_equipment;
				$this->fk_property = $obj->fk_property;
				$this->fk_location = $obj->fk_location;
				$this->fk_soc = $obj->fk_soc;
				$this->email = $obj->email;
				$this->internal = $obj->internal;
				$this->detail_problem = $obj->detail_problem;
				$this->address_ip = $obj->address_ip;
				$this->fk_user_assign = $obj->fk_user_assign;
				$this->date_assign = $this->db->jdate($obj->date_assign);
				$this->tokenreg = $obj->tokenreg;
				$this->tms = $this->db->jdate($obj->tms);
				$this->statut = $obj->statut;
				$this->description_confirm = $obj->description_confirm;
				$this->statut_job = $obj->statut_job;

                
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
		if (isset($this->fk_member)) $this->fk_member=trim($this->fk_member);
		if (isset($this->fk_departament)) $this->fk_departament=trim($this->fk_departament);
		if (isset($this->fk_equipment)) $this->fk_equipment=trim($this->fk_equipment);
		if (isset($this->fk_property)) $this->fk_property=trim($this->fk_property);
		if (isset($this->fk_location)) $this->fk_location=trim($this->fk_location);
		if (isset($this->fk_soc)) $this->fk_soc=trim($this->fk_soc);
		if (isset($this->email)) $this->email=trim($this->email);
		if (isset($this->internal)) $this->internal=trim($this->internal);
		if (isset($this->detail_problem)) $this->detail_problem=trim($this->detail_problem);
		if (isset($this->address_ip)) $this->address_ip=trim($this->address_ip);
		if (isset($this->fk_user_assign)) $this->fk_user_assign=trim($this->fk_user_assign);
		if (isset($this->tokenreg)) $this->tokenreg=trim($this->tokenreg);
		if (isset($this->statut)) $this->statut=trim($this->statut);
		if (isset($this->description_confirm)) $this->description_confirm=trim($this->description_confirm);
		if (isset($this->statut_job)) $this->statut_job=trim($this->statut_job);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."m_work_request SET";
        
		$sql.= " entity=".(isset($this->entity)?$this->entity:"null").",";
		$sql.= " ref=".(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").",";
		$sql.= " date_create=".(dol_strlen($this->date_create)!=0 ? "'".$this->db->idate($this->date_create)."'" : 'null').",";
		$sql.= " fk_member=".(isset($this->fk_member)?$this->fk_member:"null").",";
		$sql.= " fk_departament=".(isset($this->fk_departament)?$this->fk_departament:"null").",";
		$sql.= " fk_equipment=".(isset($this->fk_equipment)?$this->fk_equipment:"null").",";
		$sql.= " fk_property=".(isset($this->fk_property)?$this->fk_property:"null").",";
		$sql.= " fk_location=".(isset($this->fk_location)?$this->fk_location:"null").",";
		$sql.= " fk_soc=".(isset($this->fk_soc)?$this->fk_soc:"null").",";
		$sql.= " email=".(isset($this->email)?"'".$this->db->escape($this->email)."'":"null").",";
		$sql.= " internal=".(isset($this->internal)?$this->internal:"null").",";
		$sql.= " detail_problem=".(isset($this->detail_problem)?"'".$this->db->escape($this->detail_problem)."'":"null").",";
		$sql.= " address_ip=".(isset($this->address_ip)?"'".$this->db->escape($this->address_ip)."'":"null").",";
		$sql.= " fk_user_assign=".(isset($this->fk_user_assign)?$this->fk_user_assign:"null").",";
		$sql.= " date_assign=".(dol_strlen($this->date_assign)!=0 ? "'".$this->db->idate($this->date_assign)."'" : 'null').",";
		$sql.= " tokenreg=".(isset($this->tokenreg)?"'".$this->db->escape($this->tokenreg)."'":"null").",";
		$sql.= " tms=".(dol_strlen($this->tms)!=0 ? "'".$this->db->idate($this->tms)."'" : 'null').",";
		$sql.= " statut=".(isset($this->statut)?$this->statut:"null").",";
		$sql.= " description_confirm=".(isset($this->description_confirm)?"'".$this->db->escape($this->description_confirm)."'":"null").",";
		$sql.= " statut_job=".(isset($this->statut_job)?$this->statut_job:"null")."";

        
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."m_work_request";
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

		$object=new Mworkrequest($this->db);

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
		$this->date_create='';
		$this->fk_member='';
		$this->fk_departament='';
		$this->fk_equipment='';
		$this->fk_property='';
		$this->fk_location='';
		$this->fk_soc='';
		$this->email='';
		$this->internal='';
		$this->detail_problem='';
		$this->address_ip='';
		$this->fk_user_assign='';
		$this->date_assign='';
		$this->tokenreg='';
		$this->tms='';
		$this->statut='';
		$this->description_confirm='';
		$this->statut_job='';

		
	}

	//MODIFICACIONES
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
	  $langs->load("mant@mant");
	  
	  $dir = DOL_DOCUMENT_ROOT . "/mant/core/modules";
	  
	  if (! empty($conf->global->MANT_ADDON))
	    {
	      $file = $conf->global->MANT_ADDON.".php";
	      // Chargement de la classe de numerotation
	      $classname = $conf->global->MANT_ADDON;
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
		      dol_print_error($db,"Mworkrequest::getNextNumRef ".$obj->error);
		      return "";
		    }
		}
	      else
		{
		  print $langs->trans("Error")." ".$langs->trans("Error_MANT_ADDON_NotDefined");
		  return "";
		}
	    }
	  else
	    {
	      print $langs->trans("Error")." ".$langs->trans("Error_MANT_ADDON_NotDefined");
	      return "";
	    }
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
			return $this->LibStatut($this->statut_buy,$mode,$type);
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
		$langs->load('mant@mant');

		if ($mode == 0)
		{
			if ($status == 0) return ($type==0 ? $langs->trans('ProductStatusNotOnSellShort'):$langs->trans('ProductStatusNotOnBuyShort'));
			if ($status == 1) return ($type==0 ? $langs->trans('ProductStatusOnSellShort'):$langs->trans('ProductStatusOnBuyShort'));
		}
		if ($mode == 1)
		{
			if ($status == 0) return ($type==0 ? $langs->trans('ProductStatusNotOnSell'):$langs->trans('ProductStatusNotOnBuy'));
			if ($status == 1) return ($type==0 ? $langs->trans('ProductStatusOnSell'):$langs->trans('ProductStatusOnBuy'));
		}
		if ($mode == 2)
		{
			if ($status == 0) return img_picto($langs->trans('ProductStatusNotOnSell'),'statut5').' '.($type==0 ? $langs->trans('ProductStatusNotOnSellShort'):$langs->trans('ProductStatusNotOnBuyShort'));
			if ($status == 1) return img_picto($langs->trans('ProductStatusOnSell'),'statut4').' '.($type==0 ? $langs->trans('ProductStatusOnSellShort'):$langs->trans('ProductStatusOnBuyShort'));
		}
		if ($mode == 3)
		{
			if ($status == 0) return img_picto(($type==0 ? $langs->trans('ProductStatusNotOnSell') : $langs->trans('ProductStatusNotOnBuy')),'statut5');
			if ($status == 1) return img_picto(($type==0 ? $langs->trans('ProductStatusOnSell') : $langs->trans('ProductStatusOnBuy')),'statut4');
		}
		if ($mode == 4)
		{
			if ($status == 0) return img_picto($langs->trans('ProductStatusNotOnSell'),'statut5').' '.($type==0 ? $langs->trans('ProductStatusNotOnSell'):$langs->trans('ProductStatusNotOnBuy'));
			if ($status == 1) return img_picto($langs->trans('ProductStatusOnSell'),'statut4').' '.($type==0 ? $langs->trans('ProductStatusOnSell'):$langs->trans('ProductStatusOnBuy'));
		}
		if ($mode == 5)
		{
			if ($status == 0) return ($type==0 ? $langs->trans('ProductStatusNotOnSellShort'):$langs->trans('ProductStatusNotOnBuyShort')).' '.img_picto(($type==0 ? $langs->trans('ProductStatusNotOnSell'):$langs->trans('ProductStatusNotOnBuy')),'statut5');
			if ($status == 1) return ($type==0 ? $langs->trans('ProductStatusOnSellShort'):$langs->trans('ProductStatusOnBuyShort')).' '.img_picto(($type==0 ? $langs->trans('ProductStatusOnSell'):$langs->trans('ProductStatusOnBuy')),'statut4');
		}
		if ($mode == 6)
		  {
		    if ($status == 0) return ($type==0 ? $langs->trans('Pending'):$langs->trans('Pending')).' '.img_picto(($type==0 ? $langs->trans('Pending aprob'):$langs->trans('Pending aprobation')),'statut0');
		    if ($status == 1) return ($type==0 ? $langs->trans('Validated'):$langs->trans('Validated')).' '.img_picto(($type==0 ? $langs->trans('Validated jobs'):$langs->trans('Validated jobs order')),'statut1');
		    if ($status == 2) return ($type==0 ? $langs->trans('Assigned'):$langs->trans('Assigned')).' '.img_picto(($type==0 ? $langs->trans('Assigned jobs'):$langs->trans('Assigned jobs order')),'statut3');
		    if ($status == 3) return ($type==0 ? $langs->trans('Programmed'):$langs->trans('Programmed')).' '.img_picto(($type==0 ? $langs->trans('Programmed jobs'):$langs->trans('Programmed jobs order')),'statut4');

		    if ($status == 4) return ($type==0 ? $langs->trans('Terminated'):$langs->trans('Terminated')).' '.img_picto(($type==0 ? $langs->trans('Terminated jobs'):$langs->trans('Terminated jobs order')),'statut4');
		    if ($status == 5) return ($type==0 ? $langs->trans('Terminated'):$langs->trans('Terminated')).' '.img_picto(($type==0 ? $langs->trans('Work terminated'):$langs->trans('Order jobs terminated')),'statut4');
		    if ($status == 9) return ($type==0 ? $langs->trans('Refused'):$langs->trans('Refused')).' '.img_picto(($type==0 ? $langs->trans('Work refused'):$langs->trans('Order jobs refused')),'statut8');
		  }

		return $langs->trans('Unknown');
	}
	
}
?>
