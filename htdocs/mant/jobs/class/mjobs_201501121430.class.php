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
 *  \file       dev/skeletons/mjobs.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2014-07-24 10:36
 */

// Put here all includes required by your class file
  //require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
require_once(DOL_DOCUMENT_ROOT."/assets/class/commonobject_.class.php");

//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Mjobs extends CommonObject_
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='m_jobs';			//!< Id that identify managed objects
	var $table_element='m_jobs';		//!< Name of table without prefix where object is stored

    var $id;
    
	var $entity;
	var $ref;
	var $fk_work_request;
	var $date_create='';
	var $fk_soc;
	var $fk_member;
	var $fk_charge;
	var $fk_departament;
	var $fk_equipment;
	var $fk_property;
	var $fk_location;
	var $email;
	var $internal;
	var $speciality;
	var $detail_problem;
	var $address_ip;
	var $fk_user_assign;
	var $date_assign='';
	var $speciality_assign;
	var $description_assign;
	var $description_prog;
	var $date_ini_prog='';
	var $date_fin_prog='';
	var $speciality_prog;
	var $fk_equipment_prog;
	var $fk_property_prog;
	var $fk_location_prog;
	var $typemant_prog;
	var $fk_user_prog;
	var $date_ini='';
	var $date_fin='';
	var $speciality_job;
	var $typemant;
	var $description_job;
	var $image_ini;
	var $image_fin;
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
		if (isset($this->fk_work_request)) $this->fk_work_request=trim($this->fk_work_request);
		if (isset($this->fk_soc)) $this->fk_soc=trim($this->fk_soc);
		if (isset($this->fk_member)) $this->fk_member=trim($this->fk_member);
		if (isset($this->fk_charge)) $this->fk_charge=trim($this->fk_charge);
		if (isset($this->fk_departament)) $this->fk_departament=trim($this->fk_departament);
		if (isset($this->fk_equipment)) $this->fk_equipment=trim($this->fk_equipment);
		if (isset($this->fk_property)) $this->fk_property=trim($this->fk_property);
		if (isset($this->fk_location)) $this->fk_location=trim($this->fk_location);
		if (isset($this->email)) $this->email=trim($this->email);
		if (isset($this->internal)) $this->internal=trim($this->internal);
		if (isset($this->speciality)) $this->speciality=trim($this->speciality);
		if (isset($this->detail_problem)) $this->detail_problem=trim($this->detail_problem);
		if (isset($this->address_ip)) $this->address_ip=trim($this->address_ip);
		if (isset($this->fk_user_assign)) $this->fk_user_assign=trim($this->fk_user_assign);
		if (isset($this->speciality_assign)) $this->speciality_assign=trim($this->speciality_assign);
		if (isset($this->description_assign)) $this->description_assign=trim($this->description_assign);
		if (isset($this->description_prog)) $this->description_prog=trim($this->description_prog);
		if (isset($this->speciality_prog)) $this->speciality_prog=trim($this->speciality_prog);
		if (isset($this->fk_equipment_prog)) $this->fk_equipment_prog=trim($this->fk_equipment_prog);
		if (isset($this->fk_property_prog)) $this->fk_property_prog=trim($this->fk_property_prog);
		if (isset($this->fk_location_prog)) $this->fk_location_prog=trim($this->fk_location_prog);
		if (isset($this->typemant_prog)) $this->typemant_prog=trim($this->typemant_prog);
		if (isset($this->fk_user_prog)) $this->fk_user_prog=trim($this->fk_user_prog);
		if (isset($this->speciality_job)) $this->speciality_job=trim($this->speciality_job);
		if (isset($this->typemant)) $this->typemant=trim($this->typemant);
		if (isset($this->description_job)) $this->description_job=trim($this->description_job);
		if (isset($this->image_ini)) $this->image_ini=trim($this->image_ini);
		if (isset($this->image_fin)) $this->image_fin=trim($this->image_fin);
		if (isset($this->tokenreg)) $this->tokenreg=trim($this->tokenreg);
		if (isset($this->statut)) $this->statut=trim($this->statut);
		if (isset($this->description_confirm)) $this->description_confirm=trim($this->description_confirm);
		if (isset($this->statut_job)) $this->statut_job=trim($this->statut_job);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."m_jobs(";
		
		$sql.= "entity,";
		$sql.= "ref,";
		$sql.= "fk_work_request,";
		$sql.= "date_create,";
		$sql.= "fk_soc,";
		$sql.= "fk_member,";
		$sql.= "fk_charge,";
		$sql.= "fk_departament,";
		$sql.= "fk_equipment,";
		$sql.= "fk_property,";
		$sql.= "fk_location,";
		$sql.= "email,";
		$sql.= "internal,";
		$sql.= "speciality,";
		$sql.= "detail_problem,";
		$sql.= "address_ip,";
		$sql.= "fk_user_assign,";
		$sql.= "date_assign,";
		$sql.= "speciality_assign,";
		$sql.= "description_assign,";
		$sql.= "description_prog,";
		$sql.= "date_ini_prog,";
		$sql.= "date_fin_prog,";
		$sql.= "speciality_prog,";
		$sql.= "fk_equipment_prog,";
		$sql.= "fk_property_prog,";
		$sql.= "fk_location_prog,";
		$sql.= "typemant_prog,";
		$sql.= "fk_user_prog,";
		$sql.= "date_ini,";
		$sql.= "date_fin,";
		$sql.= "speciality_job,";
		$sql.= "typemant,";
		$sql.= "description_job,";
		$sql.= "image_ini,";
		$sql.= "image_fin,";
		$sql.= "tokenreg,";
		$sql.= "statut,";
		$sql.= "description_confirm,";
		$sql.= "statut_job";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->entity)?'NULL':"'".$this->entity."'").",";
		$sql.= " ".(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").",";
		$sql.= " ".(! isset($this->fk_work_request)?'NULL':"'".$this->fk_work_request."'").",";
		$sql.= " ".(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':$this->db->idate($this->date_create)).",";
		$sql.= " ".(! isset($this->fk_soc)?'NULL':"'".$this->fk_soc."'").",";
		$sql.= " ".(! isset($this->fk_member)?'NULL':"'".$this->fk_member."'").",";
		$sql.= " ".(! isset($this->fk_charge)?'NULL':"'".$this->fk_charge."'").",";
		$sql.= " ".(! isset($this->fk_departament)?'NULL':"'".$this->fk_departament."'").",";
		$sql.= " ".(! isset($this->fk_equipment)?'NULL':"'".$this->fk_equipment."'").",";
		$sql.= " ".(! isset($this->fk_property)?'NULL':"'".$this->fk_property."'").",";
		$sql.= " ".(! isset($this->fk_location)?'NULL':"'".$this->fk_location."'").",";
		$sql.= " ".(! isset($this->email)?'NULL':"'".$this->db->escape($this->email)."'").",";
		$sql.= " ".(! isset($this->internal)?'NULL':"'".$this->internal."'").",";
		$sql.= " ".(! isset($this->speciality)?'NULL':"'".$this->db->escape($this->speciality)."'").",";
		$sql.= " ".(! isset($this->detail_problem)?'NULL':"'".$this->db->escape($this->detail_problem)."'").",";
		$sql.= " ".(! isset($this->address_ip)?'NULL':"'".$this->db->escape($this->address_ip)."'").",";
		$sql.= " ".(! isset($this->fk_user_assign)?'NULL':"'".$this->fk_user_assign."'").",";
		$sql.= " ".(! isset($this->date_assign) || dol_strlen($this->date_assign)==0?'NULL':$this->db->idate($this->date_assign)).",";
		$sql.= " ".(! isset($this->speciality_assign)?'NULL':"'".$this->db->escape($this->speciality_assign)."'").",";
		$sql.= " ".(! isset($this->description_assign)?'NULL':"'".$this->db->escape($this->description_assign)."'").",";
		$sql.= " ".(! isset($this->description_prog)?'NULL':"'".$this->db->escape($this->description_prog)."'").",";
		$sql.= " ".(! isset($this->date_ini_prog) || dol_strlen($this->date_ini_prog)==0?'NULL':$this->db->idate($this->date_ini_prog)).",";
		$sql.= " ".(! isset($this->date_fin_prog) || dol_strlen($this->date_fin_prog)==0?'NULL':$this->db->idate($this->date_fin_prog)).",";
		$sql.= " ".(! isset($this->speciality_prog)?'NULL':"'".$this->db->escape($this->speciality_prog)."'").",";
		$sql.= " ".(! isset($this->fk_equipment_prog)?'NULL':"'".$this->fk_equipment_prog."'").",";
		$sql.= " ".(! isset($this->fk_property_prog)?'NULL':"'".$this->fk_property_prog."'").",";
		$sql.= " ".(! isset($this->fk_location_prog)?'NULL':"'".$this->fk_location_prog."'").",";
		$sql.= " ".(! isset($this->typemant_prog)?'NULL':"'".$this->db->escape($this->typemant_prog)."'").",";
		$sql.= " ".(! isset($this->fk_user_prog)?'NULL':"'".$this->fk_user_prog."'").",";
		$sql.= " ".(! isset($this->date_ini) || dol_strlen($this->date_ini)==0?'NULL':$this->db->idate($this->date_ini)).",";
		$sql.= " ".(! isset($this->date_fin) || dol_strlen($this->date_fin)==0?'NULL':$this->db->idate($this->date_fin)).",";
		$sql.= " ".(! isset($this->speciality_job)?'NULL':"'".$this->db->escape($this->speciality_job)."'").",";
		$sql.= " ".(! isset($this->typemant)?'NULL':"'".$this->db->escape($this->typemant)."'").",";
		$sql.= " ".(! isset($this->description_job)?'NULL':"'".$this->db->escape($this->description_job)."'").",";
		$sql.= " ".(! isset($this->image_ini)?'NULL':"'".$this->db->escape($this->image_ini)."'").",";
		$sql.= " ".(! isset($this->image_fin)?'NULL':"'".$this->db->escape($this->image_fin)."'").",";
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
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."m_jobs");

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
		$sql.= " t.fk_work_request,";
		$sql.= " t.date_create,";
		$sql.= " t.fk_soc,";
		$sql.= " t.fk_member,";
		$sql.= " t.fk_charge,";
		$sql.= " t.fk_departament,";
		$sql.= " t.fk_equipment,";
		$sql.= " t.fk_property,";
		$sql.= " t.fk_location,";
		$sql.= " t.email,";
		$sql.= " t.internal,";
		$sql.= " t.speciality,";
		$sql.= " t.detail_problem,";
		$sql.= " t.address_ip,";
		$sql.= " t.fk_user_assign,";
		$sql.= " t.date_assign,";
		$sql.= " t.speciality_assign,";
		$sql.= " t.description_assign,";
		$sql.= " t.description_prog,";
		$sql.= " t.date_ini_prog,";
		$sql.= " t.date_fin_prog,";
		$sql.= " t.speciality_prog,";
		$sql.= " t.fk_equipment_prog,";
		$sql.= " t.fk_property_prog,";
		$sql.= " t.fk_location_prog,";
		$sql.= " t.typemant_prog,";
		$sql.= " t.fk_user_prog,";
		$sql.= " t.date_ini,";
		$sql.= " t.date_fin,";
		$sql.= " t.speciality_job,";
		$sql.= " t.typemant,";
		$sql.= " t.description_job,";
		$sql.= " t.image_ini,";
		$sql.= " t.image_fin,";
		$sql.= " t.tokenreg,";
		$sql.= " t.tms,";
		$sql.= " t.statut,";
		$sql.= " t.description_confirm,";
		$sql.= " t.statut_job";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."m_jobs as t";
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
				$this->fk_work_request = $obj->fk_work_request;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->fk_soc = $obj->fk_soc;
				$this->fk_member = $obj->fk_member;
				$this->fk_charge = $obj->fk_charge;
				$this->fk_departament = $obj->fk_departament;
				$this->fk_equipment = $obj->fk_equipment;
				$this->fk_property = $obj->fk_property;
				$this->fk_location = $obj->fk_location;
				$this->email = $obj->email;
				$this->internal = $obj->internal;
				$this->speciality = $obj->speciality;
				$this->detail_problem = $obj->detail_problem;
				$this->address_ip = $obj->address_ip;
				$this->fk_user_assign = $obj->fk_user_assign;
				$this->date_assign = $this->db->jdate($obj->date_assign);
				$this->speciality_assign = $obj->speciality_assign;
				$this->description_assign = $obj->description_assign;
				$this->description_prog = $obj->description_prog;
				$this->date_ini_prog = $this->db->jdate($obj->date_ini_prog);
				$this->date_fin_prog = $this->db->jdate($obj->date_fin_prog);
				$this->speciality_prog = $obj->speciality_prog;
				$this->fk_equipment_prog = $obj->fk_equipment_prog;
				$this->fk_property_prog = $obj->fk_property_prog;
				$this->fk_location_prog = $obj->fk_location_prog;
				$this->typemant_prog = $obj->typemant_prog;
				$this->fk_user_prog = $obj->fk_user_prog;
				$this->date_ini = $this->db->jdate($obj->date_ini);
				$this->date_fin = $this->db->jdate($obj->date_fin);
				$this->speciality_job = $obj->speciality_job;
				$this->typemant = $obj->typemant;
				$this->description_job = $obj->description_job;
				$this->image_ini = $obj->image_ini;
				$this->image_fin = $obj->image_fin;
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
		if (isset($this->fk_work_request)) $this->fk_work_request=trim($this->fk_work_request);
		if (isset($this->fk_soc)) $this->fk_soc=trim($this->fk_soc);
		if (isset($this->fk_member)) $this->fk_member=trim($this->fk_member);
		if (isset($this->fk_charge)) $this->fk_charge=trim($this->fk_charge);
		if (isset($this->fk_departament)) $this->fk_departament=trim($this->fk_departament);
		if (isset($this->fk_equipment)) $this->fk_equipment=trim($this->fk_equipment);
		if (isset($this->fk_property)) $this->fk_property=trim($this->fk_property);
		if (isset($this->fk_location)) $this->fk_location=trim($this->fk_location);
		if (isset($this->email)) $this->email=trim($this->email);
		if (isset($this->internal)) $this->internal=trim($this->internal);
		if (isset($this->speciality)) $this->speciality=trim($this->speciality);
		if (isset($this->detail_problem)) $this->detail_problem=trim($this->detail_problem);
		if (isset($this->address_ip)) $this->address_ip=trim($this->address_ip);
		if (isset($this->fk_user_assign)) $this->fk_user_assign=trim($this->fk_user_assign);
		if (isset($this->speciality_assign)) $this->speciality_assign=trim($this->speciality_assign);
		if (isset($this->description_assign)) $this->description_assign=trim($this->description_assign);
		if (isset($this->description_prog)) $this->description_prog=trim($this->description_prog);
		if (isset($this->speciality_prog)) $this->speciality_prog=trim($this->speciality_prog);
		if (isset($this->fk_equipment_prog)) $this->fk_equipment_prog=trim($this->fk_equipment_prog);
		if (isset($this->fk_property_prog)) $this->fk_property_prog=trim($this->fk_property_prog);
		if (isset($this->fk_location_prog)) $this->fk_location_prog=trim($this->fk_location_prog);
		if (isset($this->typemant_prog)) $this->typemant_prog=trim($this->typemant_prog);
		if (isset($this->fk_user_prog)) $this->fk_user_prog=trim($this->fk_user_prog);
		if (isset($this->speciality_job)) $this->speciality_job=trim($this->speciality_job);
		if (isset($this->typemant)) $this->typemant=trim($this->typemant);
		if (isset($this->description_job)) $this->description_job=trim($this->description_job);
		if (isset($this->image_ini)) $this->image_ini=trim($this->image_ini);
		if (isset($this->image_fin)) $this->image_fin=trim($this->image_fin);
		if (isset($this->tokenreg)) $this->tokenreg=trim($this->tokenreg);
		if (isset($this->statut)) $this->statut=trim($this->statut);
		if (isset($this->description_confirm)) $this->description_confirm=trim($this->description_confirm);
		if (isset($this->statut_job)) $this->statut_job=trim($this->statut_job);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."m_jobs SET";
        
		$sql.= " entity=".(isset($this->entity)?$this->entity:"null").",";
		$sql.= " ref=".(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").",";
		$sql.= " fk_work_request=".(isset($this->fk_work_request)?$this->fk_work_request:"null").",";
		$sql.= " date_create=".(dol_strlen($this->date_create)!=0 ? "'".$this->db->idate($this->date_create)."'" : 'null').",";
		$sql.= " fk_soc=".(isset($this->fk_soc)?$this->fk_soc:"null").",";
		$sql.= " fk_member=".(isset($this->fk_member)?$this->fk_member:"null").",";
		$sql.= " fk_charge=".(isset($this->fk_charge)?$this->fk_charge:"null").",";
		$sql.= " fk_departament=".(isset($this->fk_departament)?$this->fk_departament:"null").",";
		$sql.= " fk_equipment=".(isset($this->fk_equipment)?$this->fk_equipment:"null").",";
		$sql.= " fk_property=".(isset($this->fk_property)?$this->fk_property:"null").",";
		$sql.= " fk_location=".(isset($this->fk_location)?$this->fk_location:"null").",";
		$sql.= " email=".(isset($this->email)?"'".$this->db->escape($this->email)."'":"null").",";
		$sql.= " internal=".(isset($this->internal)?$this->internal:"null").",";
		$sql.= " speciality=".(isset($this->speciality)?"'".$this->db->escape($this->speciality)."'":"null").",";
		$sql.= " detail_problem=".(isset($this->detail_problem)?"'".$this->db->escape($this->detail_problem)."'":"null").",";
		$sql.= " address_ip=".(isset($this->address_ip)?"'".$this->db->escape($this->address_ip)."'":"null").",";
		$sql.= " fk_user_assign=".(isset($this->fk_user_assign)?$this->fk_user_assign:"null").",";
		$sql.= " date_assign=".(dol_strlen($this->date_assign)!=0 ? "'".$this->db->idate($this->date_assign)."'" : 'null').",";
		$sql.= " speciality_assign=".(isset($this->speciality_assign)?"'".$this->db->escape($this->speciality_assign)."'":"null").",";
		$sql.= " description_assign=".(isset($this->description_assign)?"'".$this->db->escape($this->description_assign)."'":"null").",";
		$sql.= " description_prog=".(isset($this->description_prog)?"'".$this->db->escape($this->description_prog)."'":"null").",";
		$sql.= " date_ini_prog=".(dol_strlen($this->date_ini_prog)!=0 ? "'".$this->db->idate($this->date_ini_prog)."'" : 'null').",";
		$sql.= " date_fin_prog=".(dol_strlen($this->date_fin_prog)!=0 ? "'".$this->db->idate($this->date_fin_prog)."'" : 'null').",";
		$sql.= " speciality_prog=".(isset($this->speciality_prog)?"'".$this->db->escape($this->speciality_prog)."'":"null").",";
		$sql.= " fk_equipment_prog=".(isset($this->fk_equipment_prog)?$this->fk_equipment_prog:"null").",";
		$sql.= " fk_property_prog=".(isset($this->fk_property_prog)?$this->fk_property_prog:"null").",";
		$sql.= " fk_location_prog=".(isset($this->fk_location_prog)?$this->fk_location_prog:"null").",";
		$sql.= " typemant_prog=".(isset($this->typemant_prog)?"'".$this->db->escape($this->typemant_prog)."'":"null").",";
		$sql.= " fk_user_prog=".(isset($this->fk_user_prog)?$this->fk_user_prog:"null").",";
		$sql.= " date_ini=".(dol_strlen($this->date_ini)!=0 ? "'".$this->db->idate($this->date_ini)."'" : 'null').",";
		$sql.= " date_fin=".(dol_strlen($this->date_fin)!=0 ? "'".$this->db->idate($this->date_fin)."'" : 'null').",";
		$sql.= " speciality_job=".(isset($this->speciality_job)?"'".$this->db->escape($this->speciality_job)."'":"null").",";
		$sql.= " typemant=".(isset($this->typemant)?"'".$this->db->escape($this->typemant)."'":"null").",";
		$sql.= " description_job=".(isset($this->description_job)?"'".$this->db->escape($this->description_job)."'":"null").",";
		$sql.= " image_ini=".(isset($this->image_ini)?"'".$this->db->escape($this->image_ini)."'":"null").",";
		$sql.= " image_fin=".(isset($this->image_fin)?"'".$this->db->escape($this->image_fin)."'":"null").",";
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."m_jobs";
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

		$object=new Mjobs($this->db);

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
		$this->fk_work_request='';
		$this->date_create='';
		$this->fk_soc='';
		$this->fk_member='';
		$this->fk_charge='';
		$this->fk_departament='';
		$this->fk_equipment='';
		$this->fk_property='';
		$this->fk_location='';
		$this->email='';
		$this->internal='';
		$this->speciality='';
		$this->detail_problem='';
		$this->address_ip='';
		$this->fk_user_assign='';
		$this->date_assign='';
		$this->speciality_assign='';
		$this->description_assign='';
		$this->description_prog='';
		$this->date_ini_prog='';
		$this->date_fin_prog='';
		$this->speciality_prog='';
		$this->fk_equipment_prog='';
		$this->fk_property_prog='';
		$this->fk_location_prog='';
		$this->typemant_prog='';
		$this->fk_user_prog='';
		$this->date_ini='';
		$this->date_fin='';
		$this->speciality_job='';
		$this->typemant='';
		$this->description_job='';
		$this->image_ini='';
		$this->image_fin='';
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
	  //especifico para registro de ordenes de trabajo
	  $namenum = 'mod_mant_numbertwo';

	  //if (! empty($conf->global->MANT_ADDON))
	  if (! empty($namenum))
	    {
	      //$file = $conf->global->MANT_ADDON.".php";
	      $file = $namenum.".php";
	      // Chargement de la classe de numerotation
	      //$classname = $conf->global->MANT_ADDON;
	      $classname = $namenum;

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
		      dol_print_error($db,"Mjobs::getNextNumRef ".$obj->error);
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
		    if ($status == 1) return ($type==0 ? $langs->trans('Byassigning'):$langs->trans('Byassigning')).' '.img_picto(($type==0 ? $langs->trans('Byassigningjobs'):$langs->trans('Validated jobs order')),'statut1');
		    if ($status == 2) return ($type==0 ? $langs->trans('Assigned'):$langs->trans('Assigned')).' '.img_picto(($type==0 ? $langs->trans('Assigned jobs'):$langs->trans('Assigned jobs order')),'statut3');
		    if ($status == 3) return ($type==0 ? $langs->trans('Programmed'):$langs->trans('Programmed')).' '.img_picto(($type==0 ? $langs->trans('Programmed jobs'):$langs->trans('Programmed jobs order')),'statut4');

		    if ($status == 4) return ($type==0 ? $langs->trans('Terminated'):$langs->trans('Terminated')).' '.img_picto(($type==0 ? $langs->trans('Terminated jobs'):$langs->trans('Terminated jobs order')),'statut4');
		    if ($status == 5) return ($type==0 ? $langs->trans('Terminated'):$langs->trans('Terminated')).' '.img_picto(($type==0 ? $langs->trans('Work terminated'):$langs->trans('Order jobs terminated')),'statut4');
		    if ($status == 8) return ($type==0 ? $langs->trans('Rejected for other reasons'):$langs->trans('Rejected for other reasons')).' '.img_picto(($type==0 ? $langs->trans('Work rejected'):$langs->trans('Order jobs rejected')),'statut6');
		    if ($status == 9) return ($type==0 ? $langs->trans('Refused'):$langs->trans('Refused')).' '.img_picto(($type==0 ? $langs->trans('Work refused'):$langs->trans('Order jobs refused')),'statut8');
		  }

		return $langs->trans('Unknown');
	}


    /**
     *    	Return HTML code to output a photo
     *
     *    	@param	string		$modulepart		Key to define module concerned ('societe', 'userphoto', 'memberphoto')
     *     	@param  Object		$object			Object containing data to retrieve file name
     * 		@param	int			$width			Width of photo
     * 	  	@return string    					HTML code to output photo
     */
	function showphoto($imageview,$object,$width=100)
    {
        global $conf;
	$modulepart = 'mant';
        $entity = (! empty($object->entity) ? $object->entity : $conf->entity);
        $id = (! empty($object->id) ? $object->id : $object->rowid);

        $ret='';$dir='';$file='';$altfile='';$email='';

        if ($imageview == 'ini')
	  {
            $dir=$conf->mant->multidir_output[$entity];
	    $info_fichero = pathinfo($object->image_ini);
	    if (isset($info_fichero['extension']) && $info_fichero['extension']!=strtolower($info_fichero['extension']))
	      $file=$info_fichero['filename'].'.'.strtolower($info_fichero['extension']);
	    else
	      $file= $object->image_ini;
            $file=preg_replace('/(\.png|\.gif|\.jpg|\.jpeg|\.bmp)/i','_small\\1',$file);
            if ($object->image_ini) $file=$id.'/images/thumbs/'.$file;
	    $namephoto = 'photoini';
	  }
        if ($imageview == 'fin')
	  {
            $dir=$conf->mant->multidir_output[$entity];
	    $info_fichero = pathinfo($object->image_fin);
	    if (isset($info_fichero['extension']) && $info_fichero['extension']!=strtolower($info_fichero['extension']))
	      $file=$info_fichero['filename'].'.'.strtolower($info_fichero['extension']);
	    else
	      $file= $object->image_fin;
            $file=preg_replace('/(\.png|\.gif|\.jpg|\.jpeg|\.bmp)/i','_small\\1',$file);
            if ($object->image_fin) $file=$id.'/images/thumbs/'.$file;
	    $namephoto = 'photofin';
	  }
        if ($dir)
	  {
            $cache='0';
            if ($file && file_exists($dir."/".$file))
	      {
                // TODO Link to large image
                $ret.='<a href="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$modulepart.'&entity='.$entity.'&file='.urlencode($file).'&cache='.$cache.'">';
                $ret.='<img alt="'.$namephoto.'" id="photologo'.(preg_replace('/[^a-z]/i','_',$file)).'" class="photologo" border="0" width="'.$width.'" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$modulepart.'&entity='.$entity.'&file='.urlencode($file).'&cache='.$cache.'">';
                $ret.='</a>';
	      }
            else if ($altfile && file_exists($dir."/".$altfile))
	      {
                $ret.='<a href="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$modulepart.'&entity='.$entity.'&file='.urlencode($file).'&cache='.$cache.'">';
                $ret.='<img alt="Photo alt" id="photologo'.(preg_replace('/[^a-z]/i','_',$file)).'" class="photologo" border="0" width="'.$width.'" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$modulepart.'&entity='.$entity.'&file='.urlencode($altfile).'&cache='.$cache.'">';
                $ret.='</a>';
	      }
            else
	      {
                if (! empty($conf->gravatar->enabled) && $email)
		  {
                    global $dolibarr_main_url_root;
                    $ret.='<!-- Put link to gravatar -->';
                    $ret.='<img alt="Photo found on Gravatar" title="Photo Gravatar.com - email '.$email.'" border="0" width="'.$width.'" src="http://www.gravatar.com/avatar/'.dol_hash($email).'?s='.$width.'&d='.urlencode(dol_buildpath('/theme/common/nophoto.jpg',2)).'">';
		  }
                else
		  {
                    $ret.='<img alt="No photo" border="0" width="'.$width.'" src="'.DOL_URL_ROOT.'/theme/common/nophoto.jpg">';
		  }
	      }
	  }
        else dol_print_error('','Call of showphoto with wrong parameters');
	
        return $ret;
    }

    /**
     *  Load object in memory from the database
     *
     *  @param	int		$fk_work_request    Id object work_request
     *  @param date             $dateini default ''
     *  @param date             $datefin default ''
     *  @level int              $level default '' 0=Todos; 2=Validado; 3=Programado; 4=Concluido; 5=Todos los validados con statut >=2
     *  @return int          	<0 if KO, >0 if OK

     */
	function getlist($fk_work_request,$dateini='',$datefin='',$level='')
    {
      global $langs;
      $sql = "SELECT";
      $sql.= " t.rowid,";
      
      $sql.= " t.entity,";
      $sql.= " t.ref,";
      $sql.= " t.date_create,";
      $sql.= " t.fk_work_request,";
      $sql.= " t.fk_soc,";
      $sql.= " t.fk_member,";
      $sql.= " t.fk_charge,";
      $sql.= " t.fk_departament,";
      $sql.= " t.fk_equipment,";
      $sql.= " t.fk_property,";
      $sql.= " t.fk_location,";
      $sql.= " t.email,";
      $sql.= " t.internal,";
      $sql.= " t.speciality,";
      $sql.= " t.detail_problem,";
      $sql.= " t.address_ip,";
      $sql.= " t.fk_user_assign,";
      $sql.= " t.date_assign,";
      $sql.= " t.speciality_assign,";
      $sql.= " t.description_assign,";
      $sql.= " t.description_prog,";
      $sql.= " t.date_ini_prog,";
      $sql.= " t.date_fin_prog,";
      $sql.= " t.speciality_prog,";
      $sql.= " t.fk_equipment_prog,";
      $sql.= " t.fk_property_prog,";
      $sql.= " t.fk_location_prog,";
      $sql.= " t.typemant_prog,";
      $sql.= " t.fk_user_prog,";
      $sql.= " t.date_ini,";
      $sql.= " t.date_fin,";
      $sql.= " t.speciality_job,";
      $sql.= " t.typemant,";
      $sql.= " t.description_job,";
      $sql.= " t.image_ini,";
      $sql.= " t.image_fin,";
      $sql.= " t.tokenreg,";
      $sql.= " t.tms,";
      $sql.= " t.statut,";
      $sql.= " t.description_confirm,";
      $sql.= " t.statut_job";
      
      $sql.= " FROM ".MAIN_DB_PREFIX."m_jobs as t";
      if ($fk_work_request)
	$sql.= " WHERE t.fk_work_request = ".$fk_work_request;
      elseif ($dateini && $datefin)
	{
	  if (empty($level))
	    $sql.= " WHERE t.date_create BETWEEN '".$dateini."' AND '".$datefin."'";
	  if ($level == 2)
	    {
	      $sql.= " WHERE t.date_create BETWEEN '".$dateini."' AND '".$datefin."'";
	      $sql.= " AND t.statut = ".$level;
	    }
	  if ($level == 3)
	    {
	      //	      $sql.= " WHERE t.date_ini_prog >= '".$dateini."' AND t.date_fin_prog <= '".$datefin."'";
	      $sql.= " WHERE t.date_ini_prog BETWEEN  '".$dateini."' AND  '".$datefin."'";
	      $sql.= " AND t.date_fin_prog BETWEEN  '".$dateini."' AND  '".$datefin."'";
	      $sql.= " AND t.statut = ".$level;
	    }
	  if ($level == 4)
	    {
	      //	      $sql.= " WHERE t.date_ini >= '".$dateini."' AND t.date_fin <= '".$datefin."'";
	      $sql.= " WHERE t.date_ini BETWEEN  '".$dateini."' AND  '".$datefin."'";
	      $sql.= " AND t.date_fin BETWEEN  '".$dateini."' AND  '".$datefin."'";

	      $sql.= " AND t.statut = ".$level;
	    }

	  if ($level == 5)
	    {
	      $sql.= " WHERE t.date_create BETWEEN '".$dateini."' AND '".$datefin."'";
	      $sql.= " AND t.statut >= 2";
	    }
	}
      else
	return -1;
      dol_syslog(get_class($this)."::getlist sql=".$sql, LOG_DEBUG);
      $resql=$this->db->query($sql);
      $this->array = array();
      if ($resql)
        {
	  $num = $this->db->num_rows($resql);
	  if ($this->db->num_rows($resql))
            {
	      $i = 0;
	      while ( $i < $num)
		{
		  $obj = $this->db->fetch_object($resql);
		  $objnew = new Mjobs($this->db);

		  $objnew->id    = $obj->rowid;		  
		  $objnew->entity = $obj->entity;
		  $objnew->ref = $obj->ref;
		  $objnew->date_create = $this->db->jdate($obj->date_create);
		  $objnew->fk_work_request = $obj->fk_work_request;
		  $objnew->fk_soc = $obj->fk_soc;
		  $objnew->fk_member = $obj->fk_member;
		  $objnew->fk_charge = $obj->fk_charge;
		  $objnew->fk_departament = $obj->fk_departament;
		  $objnew->fk_equipment = $obj->fk_equipment;
		  $objnew->fk_property = $obj->fk_property;
		  $objnew->fk_location = $obj->fk_location;
		  $objnew->email = $obj->email;
		  $objnew->internal = $obj->internal;
		  $objnew->speciality = $obj->speciality;
		  $objnew->detail_problem = $obj->detail_problem;
		  $objnew->address_ip = $obj->address_ip;
		  $objnew->fk_user_assign = $obj->fk_user_assign;
		  $objnew->date_assign = $this->db->jdate($obj->date_assign);
		  $objnew->speciality_assign = $obj->speciality_assign;
		  $objnew->description_assign = $obj->description_assign;
		  $objnew->description_prog = $obj->description_prog;
		  $objnew->date_ini_prog = $this->db->jdate($obj->date_ini_prog);
		  $objnew->date_fin_prog = $this->db->jdate($obj->date_fin_prog);
		  $objnew->speciality_prog = $obj->speciality_prog;
		  $objnew->fk_equipment_prog = $obj->fk_equipment_prog;
		  $objnew->fk_property_prog = $obj->fk_property_prog;
		  $objnew->fk_location_prog = $obj->fk_location_prog;
		  $objnew->typemant_prog = $obj->typemant_prog;
		  $objnew->fk_user_prog = $obj->fk_user_prog;
		  $objnew->date_ini = $this->db->jdate($obj->date_ini);
		  $objnew->date_fin = $this->db->jdate($obj->date_fin);
		  $objnew->speciality_job = $obj->speciality_job;
		  $objnew->typemant = $obj->typemant;
		  $objnew->description_job = $obj->description_job;
		  $objnew->image_ini = $obj->image_ini;
		  $objnew->image_fin = $obj->image_fin;
		  $objnew->tokenreg = $obj->tokenreg;
		  $objnew->tms = $this->db->jdate($obj->tms);
		  $objnew->statut = $obj->statut;
		  $objnew->description_confirm = $obj->description_confirm;
		  $objnew->statut_job = $obj->statut_job;
		  
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
