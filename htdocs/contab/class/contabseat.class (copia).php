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
 *  \file       dev/skeletons/contabseat.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2013-06-02 23:54
 */

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Contabseat // extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	//var $element='contabseat';			//!< Id that identify managed objects
	//var $table_element='contabseat';	//!< Name of table without prefix where object is stored

    var $id;
    
	var $entity;
	var $date_seat='';
	var $lote;
	var $sblote;
	var $doc;
	var $currency;
	var $type_seat;
	var $debit_total;
	var $credit_total;
	var $history;
	var $manual;
	var $fk_user_creator;
	var $date_creator='';
	var $state;

    


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
		if (isset($this->lote)) $this->lote=trim($this->lote);
		if (isset($this->sblote)) $this->sblote=trim($this->sblote);
		if (isset($this->doc)) $this->doc=trim($this->doc);
		if (isset($this->currency)) $this->currency=trim($this->currency);
		if (isset($this->type_seat)) $this->type_seat=trim($this->type_seat);
		if (isset($this->debit_total)) $this->debit_total=trim($this->debit_total);
		if (isset($this->credit_total)) $this->credit_total=trim($this->credit_total);
		if (isset($this->history)) $this->history=trim($this->history);
		if (isset($this->manual)) $this->manual=trim($this->manual);
		if (isset($this->fk_user_creator)) $this->fk_user_creator=trim($this->fk_user_creator);
		if (isset($this->state)) $this->state=trim($this->state);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."contab_seat(";
		
		$sql.= "entity,";
		$sql.= "date_seat,";
		$sql.= "lote,";
		$sql.= "sblote,";
		$sql.= "doc,";
		$sql.= "currency,";
		$sql.= "type_seat,";
		$sql.= "debit_total,";
		$sql.= "credit_total,";
		$sql.= "history,";
		$sql.= "manual,";
		$sql.= "fk_user_creator,";
		$sql.= "date_creator,";
		$sql.= "state";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->entity)?'NULL':"'".$this->entity."'").",";
		$sql.= " ".(! isset($this->date_seat) || dol_strlen($this->date_seat)==0?'NULL':$this->db->idate($this->date_seat)).",";
		$sql.= " ".(! isset($this->lote)?'NULL':"'".$this->db->escape($this->lote)."'").",";
		$sql.= " ".(! isset($this->sblote)?'NULL':"'".$this->db->escape($this->sblote)."'").",";
		$sql.= " ".(! isset($this->doc)?'NULL':"'".$this->db->escape($this->doc)."'").",";
		$sql.= " ".(! isset($this->currency)?'NULL':"'".$this->currency."'").",";
		$sql.= " ".(! isset($this->type_seat)?'NULL':"'".$this->type_seat."'").",";
		$sql.= " ".(! isset($this->debit_total)?'NULL':"'".$this->debit_total."'").",";
		$sql.= " ".(! isset($this->credit_total)?'NULL':"'".$this->credit_total."'").",";
		$sql.= " ".(! isset($this->history)?'NULL':"'".$this->db->escape($this->history)."'").",";
		$sql.= " ".(! isset($this->manual)?'NULL':"'".$this->manual."'").",";
		$sql.= " ".(! isset($this->fk_user_creator)?'NULL':"'".$this->fk_user_creator."'").",";
		$sql.= " ".(! isset($this->date_creator) || dol_strlen($this->date_creator)==0?'NULL':$this->db->idate($this->date_creator)).",";
		$sql.= " ".(! isset($this->state)?'NULL':"'".$this->state."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."contab_seat");

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
		$sql.= " t.date_seat,";
		$sql.= " t.lote,";
		$sql.= " t.sblote,";
		$sql.= " t.doc,";
		$sql.= " t.currency,";
		$sql.= " t.type_seat,";
		$sql.= " t.debit_total,";
		$sql.= " t.credit_total,";
		$sql.= " t.history,";
		$sql.= " t.manual,";
		$sql.= " t.fk_user_creator,";
		$sql.= " t.date_creator,";
		$sql.= " t.state";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."contab_seat as t";
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
				$this->date_seat = $this->db->jdate($obj->date_seat);
				$this->lote = $obj->lote;
				$this->sblote = $obj->sblote;
				$this->doc = $obj->doc;
				$this->currency = $obj->currency;
				$this->type_seat = $obj->type_seat;
				$this->debit_total = $obj->debit_total;
				$this->credit_total = $obj->credit_total;
				$this->history = $obj->history;
				$this->manual = $obj->manual;
				$this->fk_user_creator = $obj->fk_user_creator;
				$this->date_creator = $this->db->jdate($obj->date_creator);
				$this->state = $obj->state;

                
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
		if (isset($this->lote)) $this->lote=trim($this->lote);
		if (isset($this->sblote)) $this->sblote=trim($this->sblote);
		if (isset($this->doc)) $this->doc=trim($this->doc);
		if (isset($this->currency)) $this->currency=trim($this->currency);
		if (isset($this->type_seat)) $this->type_seat=trim($this->type_seat);
		if (isset($this->debit_total)) $this->debit_total=trim($this->debit_total);
		if (isset($this->credit_total)) $this->credit_total=trim($this->credit_total);
		if (isset($this->history)) $this->history=trim($this->history);
		if (isset($this->manual)) $this->manual=trim($this->manual);
		if (isset($this->fk_user_creator)) $this->fk_user_creator=trim($this->fk_user_creator);
		if (isset($this->state)) $this->state=trim($this->state);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."contab_seat SET";
        
		$sql.= " entity=".(isset($this->entity)?$this->entity:"null").",";
		$sql.= " date_seat=".(dol_strlen($this->date_seat)!=0 ? "'".$this->db->idate($this->date_seat)."'" : 'null').",";
		$sql.= " lote=".(isset($this->lote)?"'".$this->db->escape($this->lote)."'":"null").",";
		$sql.= " sblote=".(isset($this->sblote)?"'".$this->db->escape($this->sblote)."'":"null").",";
		$sql.= " doc=".(isset($this->doc)?"'".$this->db->escape($this->doc)."'":"null").",";
		$sql.= " currency=".(isset($this->currency)?$this->currency:"null").",";
		$sql.= " type_seat=".(isset($this->type_seat)?$this->type_seat:"null").",";
		$sql.= " debit_total=".(isset($this->debit_total)?$this->debit_total:"null").",";
		$sql.= " credit_total=".(isset($this->credit_total)?$this->credit_total:"null").",";
		$sql.= " history=".(isset($this->history)?"'".$this->db->escape($this->history)."'":"null").",";
		$sql.= " manual=".(isset($this->manual)?$this->manual:"null").",";
		$sql.= " fk_user_creator=".(isset($this->fk_user_creator)?$this->fk_user_creator:"null").",";
		$sql.= " date_creator=".(dol_strlen($this->date_creator)!=0 ? "'".$this->db->idate($this->date_creator)."'" : 'null').",";
		$sql.= " state=".(isset($this->state)?$this->state:"null")."";

        
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."contab_seat";
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

		$object=new Contabseat($this->db);

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
		$this->date_seat='';
		$this->lote='';
		$this->sblote='';
		$this->doc='';
		$this->currency='';
		$this->type_seat='';
		$this->debit_total='';
		$this->credit_total='';
		$this->history='';
		$this->manual='';
		$this->fk_user_creator='';
		$this->date_creator='';
		$this->state='';

		
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
        $langs->load("contab@contab");

        $dir = DOL_DOCUMENT_ROOT . "/contab/core/modules";

        if (! empty($conf->global->CONTAB_ADDON))
        {
            $file = $conf->global->CONTAB_ADDON.".php";
            // Chargement de la classe de numerotation
             $classname = $conf->global->CONTAB_ADDON;
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
                    dol_print_error($db,"Contabseat::getNextNumRef ".$obj->error);
                    return "";
                }
            }
            else
            {
                print $langs->trans("Error")." ".$langs->trans("Error_CONTAB_ADDON_NotDefined");
                return "";
            }
        }
        else
        {
            print $langs->trans("Error")." ".$langs->trans("Error_CONTAB_ADDON_NotDefined");
            return "";
        }
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

}
?>
