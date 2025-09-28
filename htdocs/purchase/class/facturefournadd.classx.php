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
 *  \file       dev/skeletons/facturefournadd.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2016-10-13 16:28
 */

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Facturefournadd // extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='facturefournadd';			//!< Id that identify managed objects
	var $table_element='facture_fourn_add';	//!< Name of table without prefix where object is stored

    var $id;
    
	var $fk_facture_fourn;
	var $code_facture;
	var $code_type_purchase;
	var $nfiscal;
	var $ndui;
	var $num_autoriz;
	var $nit;
	var $razsoc;
	var $cod_control;
	var $codqr;
	var $amountfiscal;
	var $amountnofiscal;
	var $discount;
	var $datec='';
	var $tms='';
	var $localtax3;
	var $localtax4;
	var $localtax5;
	var $localtax6;
	var $localtax7;
	var $localtax8;
	var $localtax9;

    


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
        
		if (isset($this->fk_facture_fourn)) $this->fk_facture_fourn=trim($this->fk_facture_fourn);
		if (isset($this->code_facture)) $this->code_facture=trim($this->code_facture);
		if (isset($this->code_type_purchase)) $this->code_type_purchase=trim($this->code_type_purchase);
		if (isset($this->nfiscal)) $this->nfiscal=trim($this->nfiscal);
		if (isset($this->ndui)) $this->ndui=trim($this->ndui);
		if (isset($this->num_autoriz)) $this->num_autoriz=trim($this->num_autoriz);
		if (isset($this->nit)) $this->nit=trim($this->nit);
		if (isset($this->razsoc)) $this->razsoc=trim($this->razsoc);
		if (isset($this->cod_control)) $this->cod_control=trim($this->cod_control);
		if (isset($this->codqr)) $this->codqr=trim($this->codqr);
		if (isset($this->amountfiscal)) $this->amountfiscal=trim($this->amountfiscal);
		if (isset($this->amountnofiscal)) $this->amountnofiscal=trim($this->amountnofiscal);
		if (isset($this->discount)) $this->discount=trim($this->discount);
		if (isset($this->localtax3)) $this->localtax3=trim($this->localtax3);
		if (isset($this->localtax4)) $this->localtax4=trim($this->localtax4);
		if (isset($this->localtax5)) $this->localtax5=trim($this->localtax5);
		if (isset($this->localtax6)) $this->localtax6=trim($this->localtax6);
		if (isset($this->localtax7)) $this->localtax7=trim($this->localtax7);
		if (isset($this->localtax8)) $this->localtax8=trim($this->localtax8);
		if (isset($this->localtax9)) $this->localtax9=trim($this->localtax9);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."facture_fourn_add(";
		
		$sql.= "fk_facture_fourn,";
		$sql.= "code_facture,";
		$sql.= "code_type_purchase,";
		$sql.= "nfiscal,";
		$sql.= "ndui,";
		$sql.= "num_autoriz,";
		$sql.= "nit,";
		$sql.= "razsoc,";
		$sql.= "cod_control,";
		$sql.= "codqr,";
		$sql.= "amountfiscal,";
		$sql.= "amountnofiscal,";
		$sql.= "discount,";
		$sql.= "datec,";
		$sql.= "localtax3,";
		$sql.= "localtax4,";
		$sql.= "localtax5,";
		$sql.= "localtax6,";
		$sql.= "localtax7,";
		$sql.= "localtax8,";
		$sql.= "localtax9";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->fk_facture_fourn)?'NULL':"'".$this->fk_facture_fourn."'").",";
		$sql.= " ".(! isset($this->code_facture)?'NULL':"'".$this->db->escape($this->code_facture)."'").",";
		$sql.= " ".(! isset($this->code_type_purchase)?'NULL':"'".$this->db->escape($this->code_type_purchase)."'").",";
		$sql.= " ".(! isset($this->nfiscal)?'NULL':"'".$this->nfiscal."'").",";
		$sql.= " ".(! isset($this->ndui)?'NULL':"'".$this->db->escape($this->ndui)."'").",";
		$sql.= " ".(! isset($this->num_autoriz)?'NULL':"'".$this->db->escape($this->num_autoriz)."'").",";
		$sql.= " ".(! isset($this->nit)?'NULL':"'".$this->db->escape($this->nit)."'").",";
		$sql.= " ".(! isset($this->razsoc)?'NULL':"'".$this->db->escape($this->razsoc)."'").",";
		$sql.= " ".(! isset($this->cod_control)?'NULL':"'".$this->db->escape($this->cod_control)."'").",";
		$sql.= " ".(! isset($this->codqr)?'NULL':"'".$this->db->escape($this->codqr)."'").",";
		$sql.= " ".(! isset($this->amountfiscal)?'NULL':"'".$this->amountfiscal."'").",";
		$sql.= " ".(! isset($this->amountnofiscal)?'NULL':"'".$this->amountnofiscal."'").",";
		$sql.= " ".(! isset($this->discount)?'NULL':"'".$this->discount."'").",";
		$sql.= " ".(! isset($this->datec) || dol_strlen($this->datec)==0?'NULL':$this->db->idate($this->datec)).",";
		$sql.= " ".(! isset($this->localtax3)?'NULL':"'".$this->localtax3."'").",";
		$sql.= " ".(! isset($this->localtax4)?'NULL':"'".$this->localtax4."'").",";
		$sql.= " ".(! isset($this->localtax5)?'NULL':"'".$this->localtax5."'").",";
		$sql.= " ".(! isset($this->localtax6)?'NULL':"'".$this->localtax6."'").",";
		$sql.= " ".(! isset($this->localtax7)?'NULL':"'".$this->localtax7."'").",";
		$sql.= " ".(! isset($this->localtax8)?'NULL':"'".$this->localtax8."'").",";
		$sql.= " ".(! isset($this->localtax9)?'NULL':"'".$this->localtax9."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."facture_fourn_add");

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
    function fetch($id,$fk_facture_fourn=0)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.fk_facture_fourn,";
		$sql.= " t.code_facture,";
		$sql.= " t.code_type_purchase,";
		$sql.= " t.nfiscal,";
		$sql.= " t.ndui,";
		$sql.= " t.num_autoriz,";
		$sql.= " t.nit,";
		$sql.= " t.razsoc,";
		$sql.= " t.cod_control,";
		$sql.= " t.codqr,";
		$sql.= " t.amountfiscal,";
		$sql.= " t.amountnofiscal,";
		$sql.= " t.discount,";
		$sql.= " t.datec,";
		$sql.= " t.tms,";
		$sql.= " t.localtax3,";
		$sql.= " t.localtax4,";
		$sql.= " t.localtax5,";
		$sql.= " t.localtax6,";
		$sql.= " t.localtax7,";
		$sql.= " t.localtax8,";
		$sql.= " t.localtax9";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."facture_fourn_add as t";
        if ($fk_facture_fourn>0)
        	$sql.= " WHERE t.fk_facture_fourn = ".$fk_facture_fourn;
        else
	        $sql.= " WHERE t.rowid = ".$id;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;
                
				$this->fk_facture_fourn = $obj->fk_facture_fourn;
				$this->code_facture = $obj->code_facture;
				$this->code_type_purchase = $obj->code_type_purchase;
				$this->nfiscal = $obj->nfiscal;
				$this->ndui = $obj->ndui;
				$this->num_autoriz = $obj->num_autoriz;
				$this->nit = $obj->nit;
				$this->razsoc = $obj->razsoc;
				$this->cod_control = $obj->cod_control;
				$this->codqr = $obj->codqr;
				$this->amountfiscal = $obj->amountfiscal;
				$this->amountnofiscal = $obj->amountnofiscal;
				$this->discount = $obj->discount;
				$this->datec = $this->db->jdate($obj->datec);
				$this->tms = $this->db->jdate($obj->tms);
				$this->localtax3 = $obj->localtax3;
				$this->localtax4 = $obj->localtax4;
				$this->localtax5 = $obj->localtax5;
				$this->localtax6 = $obj->localtax6;
				$this->localtax7 = $obj->localtax7;
				$this->localtax8 = $obj->localtax8;
				$this->localtax9 = $obj->localtax9;

                
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
        
		if (isset($this->fk_facture_fourn)) $this->fk_facture_fourn=trim($this->fk_facture_fourn);
		if (isset($this->code_facture)) $this->code_facture=trim($this->code_facture);
		if (isset($this->code_type_purchase)) $this->code_type_purchase=trim($this->code_type_purchase);
		if (isset($this->nfiscal)) $this->nfiscal=trim($this->nfiscal);
		if (isset($this->ndui)) $this->ndui=trim($this->ndui);
		if (isset($this->num_autoriz)) $this->num_autoriz=trim($this->num_autoriz);
		if (isset($this->nit)) $this->nit=trim($this->nit);
		if (isset($this->razsoc)) $this->razsoc=trim($this->razsoc);
		if (isset($this->cod_control)) $this->cod_control=trim($this->cod_control);
		if (isset($this->codqr)) $this->codqr=trim($this->codqr);
		if (isset($this->amountfiscal)) $this->amountfiscal=trim($this->amountfiscal);
		if (isset($this->amountnofiscal)) $this->amountnofiscal=trim($this->amountnofiscal);
		if (isset($this->discount)) $this->discount=trim($this->discount);
		if (isset($this->localtax3)) $this->localtax3=trim($this->localtax3);
		if (isset($this->localtax4)) $this->localtax4=trim($this->localtax4);
		if (isset($this->localtax5)) $this->localtax5=trim($this->localtax5);
		if (isset($this->localtax6)) $this->localtax6=trim($this->localtax6);
		if (isset($this->localtax7)) $this->localtax7=trim($this->localtax7);
		if (isset($this->localtax8)) $this->localtax8=trim($this->localtax8);
		if (isset($this->localtax9)) $this->localtax9=trim($this->localtax9);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."facture_fourn_add SET";
        
		$sql.= " fk_facture_fourn=".(isset($this->fk_facture_fourn)?$this->fk_facture_fourn:"null").",";
		$sql.= " code_facture=".(isset($this->code_facture)?"'".$this->db->escape($this->code_facture)."'":"null").",";
		$sql.= " code_type_purchase=".(isset($this->code_type_purchase)?"'".$this->db->escape($this->code_type_purchase)."'":"null").",";
		$sql.= " nfiscal=".(isset($this->nfiscal)?$this->nfiscal:"null").",";
		$sql.= " ndui=".(isset($this->ndui)?"'".$this->db->escape($this->ndui)."'":"null").",";
		$sql.= " num_autoriz=".(isset($this->num_autoriz)?"'".$this->db->escape($this->num_autoriz)."'":"null").",";
		$sql.= " nit=".(isset($this->nit)?"'".$this->db->escape($this->nit)."'":"null").",";
		$sql.= " razsoc=".(isset($this->razsoc)?"'".$this->db->escape($this->razsoc)."'":"null").",";
		$sql.= " cod_control=".(isset($this->cod_control)?"'".$this->db->escape($this->cod_control)."'":"null").",";
		$sql.= " codqr=".(isset($this->codqr)?"'".$this->db->escape($this->codqr)."'":"null").",";
		$sql.= " amountfiscal=".(isset($this->amountfiscal)?$this->amountfiscal:"null").",";
		$sql.= " amountnofiscal=".(isset($this->amountnofiscal)?$this->amountnofiscal:"null").",";
		$sql.= " discount=".(isset($this->discount)?$this->discount:"null").",";
		$sql.= " datec=".(dol_strlen($this->datec)!=0 ? "'".$this->db->idate($this->datec)."'" : 'null').",";
		$sql.= " tms=".(dol_strlen($this->tms)!=0 ? "'".$this->db->idate($this->tms)."'" : 'null').",";
		$sql.= " localtax3=".(isset($this->localtax3)?$this->localtax3:"null").",";
		$sql.= " localtax4=".(isset($this->localtax4)?$this->localtax4:"null").",";
		$sql.= " localtax5=".(isset($this->localtax5)?$this->localtax5:"null").",";
		$sql.= " localtax6=".(isset($this->localtax6)?$this->localtax6:"null").",";
		$sql.= " localtax7=".(isset($this->localtax7)?$this->localtax7:"null").",";
		$sql.= " localtax8=".(isset($this->localtax8)?$this->localtax8:"null").",";
		$sql.= " localtax9=".(isset($this->localtax9)?$this->localtax9:"null")."";

        
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."facture_fourn_add";
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

		$object=new Facturefournadd($this->db);

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
		
		$this->fk_facture_fourn='';
		$this->code_facture='';
		$this->code_type_purchase='';
		$this->nfiscal='';
		$this->ndui='';
		$this->num_autoriz='';
		$this->nit='';
		$this->razsoc='';
		$this->cod_control='';
		$this->codqr='';
		$this->amountfiscal='';
		$this->amountnofiscal='';
		$this->discount='';
		$this->datec='';
		$this->tms='';
		$this->localtax3='';
		$this->localtax4='';
		$this->localtax5='';
		$this->localtax6='';
		$this->localtax7='';
		$this->localtax8='';
		$this->localtax9='';

		
	}

}
?>
