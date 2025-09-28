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
 *  \file       dev/skeletons/assets.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2014-09-03 10:09
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Assets extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='assets';			//!< Id that identify managed objects
	var $table_element='assets';		//!< Name of table without prefix where object is stored

    var $id;
    
	var $entity;
	var $type_group;
	var $type_patrim;
	var $ref_ext;
	var $item_asset;
	var $date_adq='';
	var $quant;
	var $date_baja='';
	var $descrip;
	var $number_plaque;
	var $trademark;
	var $model;
	var $anio;
	var $fk_asset_sup;
	var $fk_location;
	var $code_bar;
	var $fk_method_dep;
	var $type_property;
	var $code_bim;
	var $fk_product;
	var $fk_user_create;
	var $date_create='';
	var $mark;
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
        
		if (isset($this->entity)) $this->entity=trim($this->entity);
		if (isset($this->type_group)) $this->type_group=trim($this->type_group);
		if (isset($this->type_patrim)) $this->type_patrim=trim($this->type_patrim);
		if (isset($this->ref_ext)) $this->ref_ext=trim($this->ref_ext);
		if (isset($this->item_asset)) $this->item_asset=trim($this->item_asset);
		if (isset($this->quant)) $this->quant=trim($this->quant);
		if (isset($this->descrip)) $this->descrip=trim($this->descrip);
		if (isset($this->number_plaque)) $this->number_plaque=trim($this->number_plaque);
		if (isset($this->trademark)) $this->trademark=trim($this->trademark);
		if (isset($this->model)) $this->model=trim($this->model);
		if (isset($this->anio)) $this->anio=trim($this->anio);
		if (isset($this->fk_asset_sup)) $this->fk_asset_sup=trim($this->fk_asset_sup);
		if (isset($this->fk_location)) $this->fk_location=trim($this->fk_location);
		if (isset($this->code_bar)) $this->code_bar=trim($this->code_bar);
		if (isset($this->fk_method_dep)) $this->fk_method_dep=trim($this->fk_method_dep);
		if (isset($this->type_property)) $this->type_property=trim($this->type_property);
		if (isset($this->code_bim)) $this->code_bim=trim($this->code_bim);
		if (isset($this->fk_product)) $this->fk_product=trim($this->fk_product);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->mark)) $this->mark=trim($this->mark);
		if (isset($this->statut)) $this->statut=trim($this->statut);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."assets(";
		
		$sql.= "entity,";
		$sql.= "type_group,";
		$sql.= "type_patrim,";
		$sql.= "ref_ext,";
		$sql.= "item_asset,";
		$sql.= "date_adq,";
		$sql.= "quant,";
		$sql.= "date_baja,";
		$sql.= "descrip,";
		$sql.= "number_plaque,";
		$sql.= "trademark,";
		$sql.= "model,";
		$sql.= "anio,";
		$sql.= "fk_asset_sup,";
		$sql.= "fk_location,";
		$sql.= "code_bar,";
		$sql.= "fk_method_dep,";
		$sql.= "type_property,";
		$sql.= "code_bim,";
		$sql.= "fk_product,";
		$sql.= "fk_user_create,";
		$sql.= "date_create,";
		$sql.= "mark,";
		$sql.= "statut";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->entity)?'NULL':"'".$this->entity."'").",";
		$sql.= " ".(! isset($this->type_group)?'NULL':"'".$this->db->escape($this->type_group)."'").",";
		$sql.= " ".(! isset($this->type_patrim)?'NULL':"'".$this->db->escape($this->type_patrim)."'").",";
		$sql.= " ".(! isset($this->ref_ext)?'NULL':"'".$this->db->escape($this->ref_ext)."'").",";
		$sql.= " ".(! isset($this->item_asset)?'NULL':"'".$this->item_asset."'").",";
		$sql.= " ".(! isset($this->date_adq) || dol_strlen($this->date_adq)==0?'NULL':$this->db->idate($this->date_adq)).",";
		$sql.= " ".(! isset($this->quant)?'NULL':"'".$this->quant."'").",";
		$sql.= " ".(! isset($this->date_baja) || dol_strlen($this->date_baja)==0?'NULL':$this->db->idate($this->date_baja)).",";
		$sql.= " ".(! isset($this->descrip)?'NULL':"'".$this->db->escape($this->descrip)."'").",";
		$sql.= " ".(! isset($this->number_plaque)?'NULL':"'".$this->db->escape($this->number_plaque)."'").",";
		$sql.= " ".(! isset($this->trademark)?'NULL':"'".$this->db->escape($this->trademark)."'").",";
		$sql.= " ".(! isset($this->model)?'NULL':"'".$this->db->escape($this->model)."'").",";
		$sql.= " ".(! isset($this->anio)?'NULL':"'".$this->anio."'").",";
		$sql.= " ".(! isset($this->fk_asset_sup)?'NULL':"'".$this->fk_asset_sup."'").",";
		$sql.= " ".(! isset($this->fk_location)?'NULL':"'".$this->fk_location."'").",";
		$sql.= " ".(! isset($this->code_bar)?'NULL':"'".$this->db->escape($this->code_bar)."'").",";
		$sql.= " ".(! isset($this->fk_method_dep)?'NULL':"'".$this->fk_method_dep."'").",";
		$sql.= " ".(! isset($this->type_property)?'NULL':"'".$this->db->escape($this->type_property)."'").",";
		$sql.= " ".(! isset($this->code_bim)?'NULL':"'".$this->db->escape($this->code_bim)."'").",";
		$sql.= " ".(! isset($this->fk_product)?'NULL':"'".$this->fk_product."'").",";
		$sql.= " ".(! isset($this->fk_user_create)?'NULL':"'".$this->fk_user_create."'").",";
		$sql.= " ".(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':$this->db->idate($this->date_create)).",";
		$sql.= " ".(! isset($this->mark)?'NULL':"'".$this->db->escape($this->mark)."'").",";
		$sql.= " ".(! isset($this->statut)?'NULL':"'".$this->statut."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."assets");

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
		$sql.= " t.type_group,";
		$sql.= " t.type_patrim,";
		$sql.= " t.ref_ext,";
		$sql.= " t.item_asset,";
		$sql.= " t.date_adq,";
		$sql.= " t.quant,";
		$sql.= " t.date_baja,";
		$sql.= " t.descrip,";
		$sql.= " t.number_plaque,";
		$sql.= " t.trademark,";
		$sql.= " t.model,";
		$sql.= " t.anio,";
		$sql.= " t.fk_asset_sup,";
		$sql.= " t.fk_location,";
		$sql.= " t.code_bar,";
		$sql.= " t.fk_method_dep,";
		$sql.= " t.type_property,";
		$sql.= " t.code_bim,";
		$sql.= " t.fk_product,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.date_create,";
		$sql.= " t.mark,";
		$sql.= " t.tms,";
		$sql.= " t.statut";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."assets as t";
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
				$this->type_group = $obj->type_group;
				$this->type_patrim = $obj->type_patrim;
				$this->ref_ext = $obj->ref_ext;
				$this->item_asset = $obj->item_asset;
				$this->date_adq = $this->db->jdate($obj->date_adq);
				$this->quant = $obj->quant;
				$this->date_baja = $this->db->jdate($obj->date_baja);
				$this->descrip = $obj->descrip;
				$this->number_plaque = $obj->number_plaque;
				$this->trademark = $obj->trademark;
				$this->model = $obj->model;
				$this->anio = $obj->anio;
				$this->fk_asset_sup = $obj->fk_asset_sup;
				$this->fk_location = $obj->fk_location;
				$this->code_bar = $obj->code_bar;
				$this->fk_method_dep = $obj->fk_method_dep;
				$this->type_property = $obj->type_property;
				$this->code_bim = $obj->code_bim;
				$this->fk_product = $obj->fk_product;
				$this->fk_user_create = $obj->fk_user_create;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->mark = $obj->mark;
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
        
		if (isset($this->entity)) $this->entity=trim($this->entity);
		if (isset($this->type_group)) $this->type_group=trim($this->type_group);
		if (isset($this->type_patrim)) $this->type_patrim=trim($this->type_patrim);
		if (isset($this->ref_ext)) $this->ref_ext=trim($this->ref_ext);
		if (isset($this->item_asset)) $this->item_asset=trim($this->item_asset);
		if (isset($this->quant)) $this->quant=trim($this->quant);
		if (isset($this->descrip)) $this->descrip=trim($this->descrip);
		if (isset($this->number_plaque)) $this->number_plaque=trim($this->number_plaque);
		if (isset($this->trademark)) $this->trademark=trim($this->trademark);
		if (isset($this->model)) $this->model=trim($this->model);
		if (isset($this->anio)) $this->anio=trim($this->anio);
		if (isset($this->fk_asset_sup)) $this->fk_asset_sup=trim($this->fk_asset_sup);
		if (isset($this->fk_location)) $this->fk_location=trim($this->fk_location);
		if (isset($this->code_bar)) $this->code_bar=trim($this->code_bar);
		if (isset($this->fk_method_dep)) $this->fk_method_dep=trim($this->fk_method_dep);
		if (isset($this->type_property)) $this->type_property=trim($this->type_property);
		if (isset($this->code_bim)) $this->code_bim=trim($this->code_bim);
		if (isset($this->fk_product)) $this->fk_product=trim($this->fk_product);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->mark)) $this->mark=trim($this->mark);
		if (isset($this->statut)) $this->statut=trim($this->statut);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."assets SET";
        
		$sql.= " entity=".(isset($this->entity)?$this->entity:"null").",";
		$sql.= " type_group=".(isset($this->type_group)?"'".$this->db->escape($this->type_group)."'":"null").",";
		$sql.= " type_patrim=".(isset($this->type_patrim)?"'".$this->db->escape($this->type_patrim)."'":"null").",";
		$sql.= " ref_ext=".(isset($this->ref_ext)?"'".$this->db->escape($this->ref_ext)."'":"null").",";
		$sql.= " item_asset=".(isset($this->item_asset)?$this->item_asset:"null").",";
		$sql.= " date_adq=".(dol_strlen($this->date_adq)!=0 ? "'".$this->db->idate($this->date_adq)."'" : 'null').",";
		$sql.= " quant=".(isset($this->quant)?$this->quant:"null").",";
		$sql.= " date_baja=".(dol_strlen($this->date_baja)!=0 ? "'".$this->db->idate($this->date_baja)."'" : 'null').",";
		$sql.= " descrip=".(isset($this->descrip)?"'".$this->db->escape($this->descrip)."'":"null").",";
		$sql.= " number_plaque=".(isset($this->number_plaque)?"'".$this->db->escape($this->number_plaque)."'":"null").",";
		$sql.= " trademark=".(isset($this->trademark)?"'".$this->db->escape($this->trademark)."'":"null").",";
		$sql.= " model=".(isset($this->model)?"'".$this->db->escape($this->model)."'":"null").",";
		$sql.= " anio=".(isset($this->anio)?$this->anio:"null").",";
		$sql.= " fk_asset_sup=".(isset($this->fk_asset_sup)?$this->fk_asset_sup:"null").",";
		$sql.= " fk_location=".(isset($this->fk_location)?$this->fk_location:"null").",";
		$sql.= " code_bar=".(isset($this->code_bar)?"'".$this->db->escape($this->code_bar)."'":"null").",";
		$sql.= " fk_method_dep=".(isset($this->fk_method_dep)?$this->fk_method_dep:"null").",";
		$sql.= " type_property=".(isset($this->type_property)?"'".$this->db->escape($this->type_property)."'":"null").",";
		$sql.= " code_bim=".(isset($this->code_bim)?"'".$this->db->escape($this->code_bim)."'":"null").",";
		$sql.= " fk_product=".(isset($this->fk_product)?$this->fk_product:"null").",";
		$sql.= " fk_user_create=".(isset($this->fk_user_create)?$this->fk_user_create:"null").",";
		$sql.= " date_create=".(dol_strlen($this->date_create)!=0 ? "'".$this->db->idate($this->date_create)."'" : 'null').",";
		$sql.= " mark=".(isset($this->mark)?"'".$this->db->escape($this->mark)."'":"null").",";
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."assets";
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

		$object=new Assets($this->db);

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
		$this->type_group='';
		$this->type_patrim='';
		$this->ref_ext='';
		$this->item_asset='';
		$this->date_adq='';
		$this->quant='';
		$this->date_baja='';
		$this->descrip='';
		$this->number_plaque='';
		$this->trademark='';
		$this->model='';
		$this->anio='';
		$this->fk_asset_sup='';
		$this->fk_location='';
		$this->code_bar='';
		$this->fk_method_dep='';
		$this->type_property='';
		$this->code_bim='';
		$this->fk_product='';
		$this->fk_user_create='';
		$this->date_create='';
		$this->mark='';
		$this->tms='';
		$this->statut='';

		
	}

	//MODIFICADO
    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetch_max($type)
    {
      global $langs,$conf;
        $sql = "SELECT";
	$sql.= " MAX(t.item_asset) AS item_asset";
		
        $sql.= " FROM ".MAIN_DB_PREFIX."assets as t";
        $sql.= " WHERE t.entity = ".$conf->entity;
	$sql.= " AND t.type_group = '".$type."'";

    	dol_syslog(get_class($this)."::fetch_max sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
	$this->maximo = 1;
        if ($resql)
	  {
            if ($this->db->num_rows($resql))
	      {
                $obj = $this->db->fetch_object($resql);
		$this->maximo = $obj->item_asset + 1;
	      }
            $this->db->free($resql);
            return 1;
	  }
        else
	  {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::fetch_max ".$this->error, LOG_ERR);
            return -1;
	  }
    }

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
        $langs->load("assets@assets");

        $dir = DOL_DOCUMENT_ROOT . "/assets/core/modules";

        if (! empty($conf->global->ASSETS_ADDON))
	  {
            $file = $conf->global->ASSETS_ADDON.".php";
            // Chargement de la classe de numerotation
             $classname = $conf->global->ASSETS_ADDON;
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
                    dol_print_error($db,"Assets::getNextNumRef ".$obj->error);
                    return "";
		  }
	      }
            else
	      {
                print $langs->trans("Error")." ".$langs->trans("Error_ASSETS_ADDON_NotDefined");
                return "";
	      }
	  }
        else
	  {
            print $langs->trans("Error")." ".$langs->trans("Error_ASSETS_ADDON_NotDefined");
            return "";
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
    function select_assets($selected='',$htmlname='fk_asset',$htmloption='',$maxlength=0,$showempty=0,$idnot=0,$required='',$exclude='',$mark='')
    {
        global $conf,$langs;

        $langs->load("mant@mant");
	if ($required)
	  $required = 'required="required"';
        $out='';
        $countryArray=array();
        $label=array();

        $sql = "SELECT c.rowid, c.ref_ext as code_iso, c.descrip as label";
        $sql.= " FROM ".MAIN_DB_PREFIX."assets AS c ";
        $sql.= " WHERE c.entity = ".$conf->entity;
	$sql.= " AND c.statut = 1";
	if ($idnot) $sql.= " AND c.rowid NOT IN (".$idnot.")";
	if ($mark) $sql.= " AND (c.mark iS NULL OR c.mark = '' OR c.mark = ' ')";
        $sql.= " ORDER BY c.ref_ext ASC";

        dol_syslog(get_class($this)."::select_assets sql=".$sql);
        $resql=$this->db->query($sql);
        if ($resql)
	  {
            $out.= '<select id="select'.$htmlname.'" class="flat selectpays" '.$required.' name="'.$htmlname.'" '.$htmloption.'>';
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
		    if (empty($exclude[$obj->rowid]))
		      {
			$countryArray[$i]['rowid'] 		= $obj->rowid;
			$countryArray[$i]['code_iso'] 	= $obj->code_iso;
			$countryArray[$i]['label']		= ($obj->code_iso && $langs->transnoentitiesnoconv("Area".$obj->code_iso)!="Area".$obj->code_iso?$langs->transnoentitiesnoconv("Area".$obj->code_iso):($obj->label!='-'?$obj->label:''));
			$label[$i] 	= $countryArray[$i]['label'];
		      }
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
