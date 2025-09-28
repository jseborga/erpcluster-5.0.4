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
 *  \file       dev/skeletons/solalmacen.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2013-03-20 13:50
 */

// Put here all includes required by your class file
  //require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/commonobject_.class.php");

//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
require_once(DOL_DOCUMENT_ROOT."/almacen/class/solalmacendet.class.php");


/**
 *	Put here description of your class
 */
class Solalmacen  extends CommonObject_
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	//var $element='solalmacen';			//!< Id that identify managed objects
	//var $table_element='solalmacen';	//!< Name of table without prefix where object is stored

    var $id;
    
	var $entity;
	var $ref;
	var $fk_entrepot;
	var $fk_fabrication;
	var $date_creation='';
	var $date_delivery='';
	var $description;
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
	 *  Returns the reference to the following non used Order depending on the active numbering module
	 *  defined into ALMACEN_ADDON
	 *
	 *  @param	Societe		$soc  	Object thirdparty
	 *  @return string      		Order free reference
	 */
    function getNextNumRef($soc)
    {
        global $db, $langs, $conf;
        $langs->load("almacen@almacen");

        $dir = DOL_DOCUMENT_ROOT . "/almacen/core/modules";
	
        if (! empty($conf->global->ALMACEN_ADDON))
        {
            $file = $conf->global->ALMACEN_ADDON.".php";
            // Chargement de la classe de numerotation
             $classname = $conf->global->ALMACEN_ADDON;
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
                    dol_print_error($db,"Solalmacen::getNextNumRef ".$obj->error);
                    return "";
                }
            }
            else
            {
                print $langs->trans("Error")." ".$langs->trans("Error_ALMACEN_ADDON_NotDefined");
                return "";
            }
        }
        else
        {
            print $langs->trans("Error")." ".$langs->trans("Error_ALMACEN_ADDON_NotDefined");
            return "";
        }
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
		if (isset($this->fk_entrepot)) $this->fk_entrepot=trim($this->fk_entrepot);
		if (isset($this->fk_fabrication)) $this->fk_fabrication=trim($this->fk_fabrication);
		if (isset($this->description)) $this->description=trim($this->description);
		if (isset($this->statut)) $this->statut=trim($this->statut);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."sol_almacen(";
		
		$sql.= "entity,";
		$sql.= "ref,";
		$sql.= "fk_entrepot,";
		$sql.= "fk_fabrication,";
		$sql.= "date_creation,";
		$sql.= "date_delivery,";
		$sql.= "description,";
		$sql.= "statut";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->entity)?'NULL':"'".$this->entity."'").",";
		$sql.= " '(PROV)', ";
		  //$sql.= " ".(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").",";
		$sql.= " ".(! isset($this->fk_entrepot)?'NULL':"'".$this->fk_entrepot."'").",";
		$sql.= " ".(! isset($this->fk_fabrication)?'NULL':"'".$this->fk_fabrication."'").",";
		$sql.= " ".(! isset($this->date_creation) || dol_strlen($this->date_creation)==0?'NULL':$this->db->idate($this->date_creation)).",";
		$sql.= " ".(! isset($this->date_delivery) || dol_strlen($this->date_delivery)==0?'NULL':$this->db->idate($this->date_delivery)).",";
		$sql.= " ".(! isset($this->description)?'NULL':"'".$this->db->escape($this->description)."'").",";
		$sql.= " ".(! isset($this->statut)?'NULL':"'".$this->statut."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."sol_almacen");

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
		$sql.= " t.fk_entrepot,";
		$sql.= " t.fk_fabrication,";
		$sql.= " t.date_creation,";
		$sql.= " t.date_delivery,";
		$sql.= " t.description,";
		$sql.= " t.statut";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."sol_almacen as t";
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
		$this->fk_entrepot = $obj->fk_entrepot;
		$this->fk_fabrication = $obj->fk_fabrication;
		$this->date_creation = $this->db->jdate($obj->date_creation);
		$this->date_delivery = $this->db->jdate($obj->date_delivery);
		$this->description = $obj->description;
		$this->statut = $obj->statut;
		
                
            }
            $this->db->free($resql);
	    $this->fetch_lines();
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
		if (isset($this->fk_entrepot)) $this->fk_entrepot=trim($this->fk_entrepot);
		if (isset($this->fk_fabrication)) $this->fk_fabrication=trim($this->fk_fabrication);
		if (isset($this->description)) $this->description=trim($this->description);
		if (isset($this->statut)) $this->statut=trim($this->statut);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."sol_almacen SET";
        
		$sql.= " entity=".(isset($this->entity)?$this->entity:"null").",";
		$sql.= " ref=".(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").",";
		$sql.= " fk_entrepot=".(isset($this->fk_entrepot)?$this->fk_entrepot:"null").",";
		$sql.= " fk_fabrication=".(isset($this->fk_fabrication)?$this->fk_fabrication:"null").",";
		$sql.= " date_creation=".(dol_strlen($this->date_creation)!=0 ? "'".$this->db->idate($this->date_creation)."'" : 'null').",";
		$sql.= " date_delivery=".(dol_strlen($this->date_delivery)!=0 ? "'".$this->db->idate($this->date_delivery)."'" : 'null').",";
		$sql.= " description=".(isset($this->description)?"'".$this->db->escape($this->description)."'":"null").",";
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."sol_almacen";
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

		$object=new Solalmacen($this->db);

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
		$this->fk_entrepot='';
		$this->fk_fabrication='';
		$this->date_creation='';
		$this->date_delivery='';
		$this->description='';
		$this->statut='';

		
	}

	/**
     *	Return statut label of Order
     *
     *	@param      int		$mode       0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
     *	@return     string      		Libelle
     */
    function getLibStatut($mode)
    {
        return $this->LibStatut($this->statut,$this->facturee,$mode);
    }

    /**
     *	Return label of statut
     *
     *	@param		int		$statut      	Id statut
     *  @param      int		$facturee    	if invoiced
     *	@param      int		$mode        	0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
     *  @return     string					Label of statut
     */
    function LibStatut($statut,$facturee,$mode)
    {
        global $langs;
        //print 'x'.$statut.'-'.$facturee;
        if ($mode == 0)
        {
            if ($statut==-1) return $langs->trans('StatusOrderCanceled');
            if ($statut==0) return $langs->trans('StatusOrderDraft');
            if ($statut==1) return $langs->trans('StatusOrderValidated');
            if ($statut==2) return $langs->trans('StatusOrderSentShort');
            if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('StatusOrderToBill');
            if ($statut==3 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('StatusOrderProcessed');
        }
        elseif ($mode == 1)
        {
            if ($statut==-1) return $langs->trans('StatusOrderCanceledShort');
            if ($statut==0) return $langs->trans('StatusOrderDraftShort');
            if ($statut==1) return $langs->trans('StatusOrderValidatedShort');
            if ($statut==2) return $langs->trans('StatusOrderSentShort');
            if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('StatusOrderToBillShort');
            if ($statut==3 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('StatusOrderProcessed');
        }
        elseif ($mode == 2)
        {
            if ($statut==-1) return img_picto($langs->trans('StatusOrderCanceled'),'statut5').' '.$langs->trans('StatusOrderCanceledShort');
            if ($statut==0) return img_picto($langs->trans('StatusOrderDraft'),'statut0').' '.$langs->trans('StatusOrderDraftShort');
            if ($statut==1) return img_picto($langs->trans('StatusOrderValidated'),'statut1').' '.$langs->trans('StatusOrderValidatedShort');
            if ($statut==2) return img_picto($langs->trans('StatusOrderSent'),'statut3').' '.$langs->trans('StatusOrderSentShort');
            if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderToBill'),'statut7').' '.$langs->trans('StatusOrderToBillShort');
            if ($statut==3 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderProcessed'),'statut6').' '.$langs->trans('StatusOrderProcessedShort');
        }
        elseif ($mode == 3)
        {
            if ($statut==-1) return img_picto($langs->trans('StatusOrderCanceled'),'statut5');
            if ($statut==0) return img_picto($langs->trans('StatusOrderDraft'),'statut0');
            if ($statut==1) return img_picto($langs->trans('StatusOrderValidated'),'statut1');
            if ($statut==2) return img_picto($langs->trans('StatusOrderSentShort'),'statut3');
            if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderToBill'),'statut7');
            if ($statut==3 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderProcessed'),'statut6');
        }
        elseif ($mode == 4)
        {
            if ($statut==-1) return img_picto($langs->trans('StatusOrderCanceled'),'statut5').' '.$langs->trans('StatusOrderCanceled');
            if ($statut==0) return img_picto($langs->trans('StatusOrderDraft'),'statut0').' '.$langs->trans('StatusOrderDraft');
            if ($statut==1) return img_picto($langs->trans('StatusOrderValidated'),'statut1').' '.$langs->trans('StatusOrderValidated');
            if ($statut==2) return img_picto($langs->trans('StatusOrderSentShort'),'statut3').' '.$langs->trans('StatusOrderSent');
            if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderToBill'),'statut7').' '.$langs->trans('StatusOrderToBill');
            if ($statut==3 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderProcessed'),'statut6').' '.$langs->trans('StatusOrderProcessed');
        }
        elseif ($mode == 5)
        {
            if ($statut==-1) return $langs->trans('StatusOrderCanceledShort').' '.img_picto($langs->trans('StatusOrderCanceled'),'statut5');
            if ($statut==0) return $langs->trans('StatusOrderDraftShort').' '.img_picto($langs->trans('StatusOrderDraft'),'statut0');
            if ($statut==1) return $langs->trans('StatusOrderValidatedShort').' '.img_picto($langs->trans('StatusOrderValidated'),'statut1');
            if ($statut==2) return $langs->trans('StatusOrderSentShort').' '.img_picto($langs->trans('StatusOrderSent'),'statut3');
            if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('StatusOrderToBillShort').' '.img_picto($langs->trans('StatusOrderToBill'),'statut7');
            if ($statut==3 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('StatusOrderProcessedShort').' '.img_picto($langs->trans('StatusOrderProcessed'),'statut6');
        }
    }

    /**
     *	Load all detailed lines into this->lines
     *
     *	@return     int         1 if OK, < 0 if KO
     */
    function fetch_lines()
    {
        $this->lines=array();

        $sql = 'SELECT l.rowid, l.fk_product, l.qty, l.qty_livree, ';
        $sql.= ' l.date_shipping,';
        $sql.= ' p.ref as product_ref, p.fk_product_type as fk_product_type, p.label as product_label, p.description as product_desc';
        $sql.= ' FROM '.MAIN_DB_PREFIX.'sol_almacendet as l';
        $sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'product as p ON l.fk_product = p.rowid';
        $sql.= ' WHERE l.fk_almacen = '.$this->id;
        $sql.= ' ORDER BY p.ref';

        dol_syslog(get_class($this).'::fetch_lines sql='.$sql, LOG_DEBUG);
        $result = $this->db->query($sql);
        if ($result)
        {
            $num = $this->db->num_rows($result);
            $i = 0;
            while ($i < $num)
            {
                $objp = $this->db->fetch_object($result);
                $line = new Solalmacendet($this->db);

                $line->rowid	        = $objp->rowid;
                $line->product_type     = $objp->product_type;		// Type of line
                $line->product_ref      = $objp->product_ref;		// Ref product
                $line->libelle          = $objp->product_label;		// TODO deprecated
                $line->product_label	= $objp->product_label;		// Label product
                $line->product_desc     = $objp->product_desc;		// Description product
                $line->qty              = $objp->qty;
                $line->qty_livree       = $objp->qty_livree;
                $line->fk_product       = $objp->fk_product;
                $line->date_shipping    = $this->db->jdate($objp->date_shipping);
		
                // Ne plus utiliser
                //$line->price            = $objp->price;
                //$line->remise           = $objp->remise;

                $this->lines[$i] = $line;

                $i++;
            }
            $this->db->free($result);
            return 1;
        }
        else
        {
            $this->error=$this->db->error();
            dol_syslog(get_class($this).'::fetch_lines '.$this->error,LOG_ERR);
            return -3;
        }
    }

    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetch_fabrication($fk_fabrication)
    {
    	global $langs;
        $sql = "SELECT";
	$sql.= " t.rowid,";
	$sql.= " t.entity,";
	$sql.= " t.ref,";
	$sql.= " t.fk_entrepot,";
	$sql.= " t.fk_fabrication,";
	$sql.= " t.date_creation,";
	$sql.= " t.date_delivery,";
	$sql.= " t.description,";
	$sql.= " t.statut";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."sol_almacen as t";
        $sql.= " WHERE t.fk_fabrication = ".$fk_fabrication;

    	dol_syslog(get_class($this)."::fetch_fabrication sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
	$this->array = array();
        if ($resql)
	  {
            if ($this->db->num_rows($resql))
	      {
		$i = 0;
		$num = $this->db->num_rows($resql);
		while ($i < $num)
		  {
		    $obj = $this->db->fetch_object($resql);
		
		    $this->array[$obj->rowid] = 
		      array (
			     'id' => $obj->rowid,
			     'entity' => $obj->entity,
			     'ref' => $obj->ref,
			     'fk_entrepot' => $obj->fk_entrepot,
			     'fk_fabrication' => $obj->fk_fabrication,
			     'date_creation' => $this->db->jdate($obj->date_creation),
			     'date_delivery' => $this->db->jdate($obj->date_delivery),
			     'description' => $obj->description,
			     'statut' => $obj->statut
			     );
		    $i++;
		  }
		return $this->array;
            }
            $this->db->free($resql);
	    $this->fetch_lines();
            return 1;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
            return -1;
        }
    }

}
?>
