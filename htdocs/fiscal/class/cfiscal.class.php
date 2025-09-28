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
 *  \file       dev/skeletons/cfiscal.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2016-10-07 12:27
 */

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Cfiscal // extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	//var $element='cfiscal';			//!< Id that identify managed objects
	//var $table_element='cfiscal';	//!< Name of table without prefix where object is stored

    var $id;
    
	var $entity;
	var $nfiscal;
	var $serie;
	var $fk_facture_fourn;
	var $fk_soc;
	var $nit;
	var $razsoc;
	var $date_exp='';
	var $num_autoriz;
	var $cod_control;
	var $baseimp1;
	var $baseimp2;
	var $baseimp3;
	var $baseimp4;
	var $baseimp5;
	var $aliqimp1;
	var $aliqimp2;
	var $aliqimp3;
	var $aliqimp4;
	var $aliqimp5;
	var $valimp1;
	var $valimp2;
	var $valimp3;
	var $valimp4;
	var $valimp5;
	var $valret1;
	var $valret2;
	var $valret3;
	var $valret4;
	var $valret5;
	var $date_create='';
	var $fk_user_create;
	var $statut_print;
	var $status;

    


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
		if (isset($this->nfiscal)) $this->nfiscal=trim($this->nfiscal);
		if (isset($this->serie)) $this->serie=trim($this->serie);
		if (isset($this->fk_facture_fourn)) $this->fk_facture_fourn=trim($this->fk_facture_fourn);
		if (isset($this->fk_soc)) $this->fk_soc=trim($this->fk_soc);
		if (isset($this->nit)) $this->nit=trim($this->nit);
		if (isset($this->razsoc)) $this->razsoc=trim($this->razsoc);
		if (isset($this->num_autoriz)) $this->num_autoriz=trim($this->num_autoriz);
		if (isset($this->cod_control)) $this->cod_control=trim($this->cod_control);
		if (isset($this->baseimp1)) $this->baseimp1=trim($this->baseimp1);
		if (isset($this->baseimp2)) $this->baseimp2=trim($this->baseimp2);
		if (isset($this->baseimp3)) $this->baseimp3=trim($this->baseimp3);
		if (isset($this->baseimp4)) $this->baseimp4=trim($this->baseimp4);
		if (isset($this->baseimp5)) $this->baseimp5=trim($this->baseimp5);
		if (isset($this->aliqimp1)) $this->aliqimp1=trim($this->aliqimp1);
		if (isset($this->aliqimp2)) $this->aliqimp2=trim($this->aliqimp2);
		if (isset($this->aliqimp3)) $this->aliqimp3=trim($this->aliqimp3);
		if (isset($this->aliqimp4)) $this->aliqimp4=trim($this->aliqimp4);
		if (isset($this->aliqimp5)) $this->aliqimp5=trim($this->aliqimp5);
		if (isset($this->valimp1)) $this->valimp1=trim($this->valimp1);
		if (isset($this->valimp2)) $this->valimp2=trim($this->valimp2);
		if (isset($this->valimp3)) $this->valimp3=trim($this->valimp3);
		if (isset($this->valimp4)) $this->valimp4=trim($this->valimp4);
		if (isset($this->valimp5)) $this->valimp5=trim($this->valimp5);
		if (isset($this->valret1)) $this->valret1=trim($this->valret1);
		if (isset($this->valret2)) $this->valret2=trim($this->valret2);
		if (isset($this->valret3)) $this->valret3=trim($this->valret3);
		if (isset($this->valret4)) $this->valret4=trim($this->valret4);
		if (isset($this->valret5)) $this->valret5=trim($this->valret5);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->statut_print)) $this->statut_print=trim($this->statut_print);
		if (isset($this->status)) $this->status=trim($this->status);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."c_fiscal(";
		
		$sql.= "entity,";
		$sql.= "nfiscal,";
		$sql.= "serie,";
		$sql.= "fk_facture_fourn,";
		$sql.= "fk_soc,";
		$sql.= "nit,";
		$sql.= "razsoc,";
		$sql.= "date_exp,";
		$sql.= "num_autoriz,";
		$sql.= "cod_control,";
		$sql.= "baseimp1,";
		$sql.= "baseimp2,";
		$sql.= "baseimp3,";
		$sql.= "baseimp4,";
		$sql.= "baseimp5,";
		$sql.= "aliqimp1,";
		$sql.= "aliqimp2,";
		$sql.= "aliqimp3,";
		$sql.= "aliqimp4,";
		$sql.= "aliqimp5,";
		$sql.= "valimp1,";
		$sql.= "valimp2,";
		$sql.= "valimp3,";
		$sql.= "valimp4,";
		$sql.= "valimp5,";
		$sql.= "valret1,";
		$sql.= "valret2,";
		$sql.= "valret3,";
		$sql.= "valret4,";
		$sql.= "valret5,";
		$sql.= "date_create,";
		$sql.= "fk_user_create,";
		$sql.= "statut_print,";
		$sql.= "status";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->entity)?'NULL':"'".$this->entity."'").",";
		$sql.= " ".(! isset($this->nfiscal)?'NULL':"'".$this->db->escape($this->nfiscal)."'").",";
		$sql.= " ".(! isset($this->serie)?'NULL':"'".$this->db->escape($this->serie)."'").",";
		$sql.= " ".(! isset($this->fk_facture_fourn)?'NULL':"'".$this->fk_facture_fourn."'").",";
		$sql.= " ".(! isset($this->fk_soc)?'NULL':"'".$this->fk_soc."'").",";
		$sql.= " ".(! isset($this->nit)?'NULL':"'".$this->db->escape($this->nit)."'").",";
		$sql.= " ".(! isset($this->razsoc)?'NULL':"'".$this->db->escape($this->razsoc)."'").",";
		$sql.= " ".(! isset($this->date_exp) || dol_strlen($this->date_exp)==0?'NULL':$this->db->idate($this->date_exp)).",";
		$sql.= " ".(! isset($this->num_autoriz)?'NULL':"'".$this->db->escape($this->num_autoriz)."'").",";
		$sql.= " ".(! isset($this->cod_control)?'NULL':"'".$this->db->escape($this->cod_control)."'").",";
		$sql.= " ".(! isset($this->baseimp1)?'NULL':"'".$this->baseimp1."'").",";
		$sql.= " ".(! isset($this->baseimp2)?'NULL':"'".$this->baseimp2."'").",";
		$sql.= " ".(! isset($this->baseimp3)?'NULL':"'".$this->baseimp3."'").",";
		$sql.= " ".(! isset($this->baseimp4)?'NULL':"'".$this->baseimp4."'").",";
		$sql.= " ".(! isset($this->baseimp5)?'NULL':"'".$this->baseimp5."'").",";
		$sql.= " ".(! isset($this->aliqimp1)?'NULL':"'".$this->aliqimp1."'").",";
		$sql.= " ".(! isset($this->aliqimp2)?'NULL':"'".$this->aliqimp2."'").",";
		$sql.= " ".(! isset($this->aliqimp3)?'NULL':"'".$this->aliqimp3."'").",";
		$sql.= " ".(! isset($this->aliqimp4)?'NULL':"'".$this->aliqimp4."'").",";
		$sql.= " ".(! isset($this->aliqimp5)?'NULL':"'".$this->aliqimp5."'").",";
		$sql.= " ".(! isset($this->valimp1)?'NULL':"'".$this->valimp1."'").",";
		$sql.= " ".(! isset($this->valimp2)?'NULL':"'".$this->valimp2."'").",";
		$sql.= " ".(! isset($this->valimp3)?'NULL':"'".$this->valimp3."'").",";
		$sql.= " ".(! isset($this->valimp4)?'NULL':"'".$this->valimp4."'").",";
		$sql.= " ".(! isset($this->valimp5)?'NULL':"'".$this->valimp5."'").",";
		$sql.= " ".(! isset($this->valret1)?'NULL':"'".$this->valret1."'").",";
		$sql.= " ".(! isset($this->valret2)?'NULL':"'".$this->valret2."'").",";
		$sql.= " ".(! isset($this->valret3)?'NULL':"'".$this->valret3."'").",";
		$sql.= " ".(! isset($this->valret4)?'NULL':"'".$this->valret4."'").",";
		$sql.= " ".(! isset($this->valret5)?'NULL':"'".$this->valret5."'").",";
		$sql.= " ".(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':$this->db->idate($this->date_create)).",";
		$sql.= " ".(! isset($this->fk_user_create)?'NULL':"'".$this->fk_user_create."'").",";
		$sql.= " ".(! isset($this->statut_print)?'NULL':"'".$this->statut_print."'").",";
		$sql.= " ".(! isset($this->status)?'NULL':"'".$this->status."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."c_fiscal");

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
		$sql.= " t.nfiscal,";
		$sql.= " t.serie,";
		$sql.= " t.fk_facture_fourn,";
		$sql.= " t.fk_soc,";
		$sql.= " t.nit,";
		$sql.= " t.razsoc,";
		$sql.= " t.date_exp,";
		$sql.= " t.num_autoriz,";
		$sql.= " t.cod_control,";
		$sql.= " t.baseimp1,";
		$sql.= " t.baseimp2,";
		$sql.= " t.baseimp3,";
		$sql.= " t.baseimp4,";
		$sql.= " t.baseimp5,";
		$sql.= " t.aliqimp1,";
		$sql.= " t.aliqimp2,";
		$sql.= " t.aliqimp3,";
		$sql.= " t.aliqimp4,";
		$sql.= " t.aliqimp5,";
		$sql.= " t.valimp1,";
		$sql.= " t.valimp2,";
		$sql.= " t.valimp3,";
		$sql.= " t.valimp4,";
		$sql.= " t.valimp5,";
		$sql.= " t.valret1,";
		$sql.= " t.valret2,";
		$sql.= " t.valret3,";
		$sql.= " t.valret4,";
		$sql.= " t.valret5,";
		$sql.= " t.date_create,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.statut_print,";
		$sql.= " t.status";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."c_fiscal as t";
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
				$this->nfiscal = $obj->nfiscal;
				$this->serie = $obj->serie;
				$this->fk_facture_fourn = $obj->fk_facture_fourn;
				$this->fk_soc = $obj->fk_soc;
				$this->nit = $obj->nit;
				$this->razsoc = $obj->razsoc;
				$this->date_exp = $this->db->jdate($obj->date_exp);
				$this->num_autoriz = $obj->num_autoriz;
				$this->cod_control = $obj->cod_control;
				$this->baseimp1 = $obj->baseimp1;
				$this->baseimp2 = $obj->baseimp2;
				$this->baseimp3 = $obj->baseimp3;
				$this->baseimp4 = $obj->baseimp4;
				$this->baseimp5 = $obj->baseimp5;
				$this->aliqimp1 = $obj->aliqimp1;
				$this->aliqimp2 = $obj->aliqimp2;
				$this->aliqimp3 = $obj->aliqimp3;
				$this->aliqimp4 = $obj->aliqimp4;
				$this->aliqimp5 = $obj->aliqimp5;
				$this->valimp1 = $obj->valimp1;
				$this->valimp2 = $obj->valimp2;
				$this->valimp3 = $obj->valimp3;
				$this->valimp4 = $obj->valimp4;
				$this->valimp5 = $obj->valimp5;
				$this->valret1 = $obj->valret1;
				$this->valret2 = $obj->valret2;
				$this->valret3 = $obj->valret3;
				$this->valret4 = $obj->valret4;
				$this->valret5 = $obj->valret5;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->fk_user_create = $obj->fk_user_create;
				$this->statut_print = $obj->statut_print;
				$this->status = $obj->status;

                
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
		if (isset($this->nfiscal)) $this->nfiscal=trim($this->nfiscal);
		if (isset($this->serie)) $this->serie=trim($this->serie);
		if (isset($this->fk_facture_fourn)) $this->fk_facture_fourn=trim($this->fk_facture_fourn);
		if (isset($this->fk_soc)) $this->fk_soc=trim($this->fk_soc);
		if (isset($this->nit)) $this->nit=trim($this->nit);
		if (isset($this->razsoc)) $this->razsoc=trim($this->razsoc);
		if (isset($this->num_autoriz)) $this->num_autoriz=trim($this->num_autoriz);
		if (isset($this->cod_control)) $this->cod_control=trim($this->cod_control);
		if (isset($this->baseimp1)) $this->baseimp1=trim($this->baseimp1);
		if (isset($this->baseimp2)) $this->baseimp2=trim($this->baseimp2);
		if (isset($this->baseimp3)) $this->baseimp3=trim($this->baseimp3);
		if (isset($this->baseimp4)) $this->baseimp4=trim($this->baseimp4);
		if (isset($this->baseimp5)) $this->baseimp5=trim($this->baseimp5);
		if (isset($this->aliqimp1)) $this->aliqimp1=trim($this->aliqimp1);
		if (isset($this->aliqimp2)) $this->aliqimp2=trim($this->aliqimp2);
		if (isset($this->aliqimp3)) $this->aliqimp3=trim($this->aliqimp3);
		if (isset($this->aliqimp4)) $this->aliqimp4=trim($this->aliqimp4);
		if (isset($this->aliqimp5)) $this->aliqimp5=trim($this->aliqimp5);
		if (isset($this->valimp1)) $this->valimp1=trim($this->valimp1);
		if (isset($this->valimp2)) $this->valimp2=trim($this->valimp2);
		if (isset($this->valimp3)) $this->valimp3=trim($this->valimp3);
		if (isset($this->valimp4)) $this->valimp4=trim($this->valimp4);
		if (isset($this->valimp5)) $this->valimp5=trim($this->valimp5);
		if (isset($this->valret1)) $this->valret1=trim($this->valret1);
		if (isset($this->valret2)) $this->valret2=trim($this->valret2);
		if (isset($this->valret3)) $this->valret3=trim($this->valret3);
		if (isset($this->valret4)) $this->valret4=trim($this->valret4);
		if (isset($this->valret5)) $this->valret5=trim($this->valret5);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->statut_print)) $this->statut_print=trim($this->statut_print);
		if (isset($this->status)) $this->status=trim($this->status);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."c_fiscal SET";
        
		$sql.= " entity=".(isset($this->entity)?$this->entity:"null").",";
		$sql.= " nfiscal=".(isset($this->nfiscal)?"'".$this->db->escape($this->nfiscal)."'":"null").",";
		$sql.= " serie=".(isset($this->serie)?"'".$this->db->escape($this->serie)."'":"null").",";
		$sql.= " fk_facture_fourn=".(isset($this->fk_facture_fourn)?$this->fk_facture_fourn:"null").",";
		$sql.= " fk_soc=".(isset($this->fk_soc)?$this->fk_soc:"null").",";
		$sql.= " nit=".(isset($this->nit)?"'".$this->db->escape($this->nit)."'":"null").",";
		$sql.= " razsoc=".(isset($this->razsoc)?"'".$this->db->escape($this->razsoc)."'":"null").",";
		$sql.= " date_exp=".(dol_strlen($this->date_exp)!=0 ? "'".$this->db->idate($this->date_exp)."'" : 'null').",";
		$sql.= " num_autoriz=".(isset($this->num_autoriz)?"'".$this->db->escape($this->num_autoriz)."'":"null").",";
		$sql.= " cod_control=".(isset($this->cod_control)?"'".$this->db->escape($this->cod_control)."'":"null").",";
		$sql.= " baseimp1=".(isset($this->baseimp1)?$this->baseimp1:"null").",";
		$sql.= " baseimp2=".(isset($this->baseimp2)?$this->baseimp2:"null").",";
		$sql.= " baseimp3=".(isset($this->baseimp3)?$this->baseimp3:"null").",";
		$sql.= " baseimp4=".(isset($this->baseimp4)?$this->baseimp4:"null").",";
		$sql.= " baseimp5=".(isset($this->baseimp5)?$this->baseimp5:"null").",";
		$sql.= " aliqimp1=".(isset($this->aliqimp1)?$this->aliqimp1:"null").",";
		$sql.= " aliqimp2=".(isset($this->aliqimp2)?$this->aliqimp2:"null").",";
		$sql.= " aliqimp3=".(isset($this->aliqimp3)?$this->aliqimp3:"null").",";
		$sql.= " aliqimp4=".(isset($this->aliqimp4)?$this->aliqimp4:"null").",";
		$sql.= " aliqimp5=".(isset($this->aliqimp5)?$this->aliqimp5:"null").",";
		$sql.= " valimp1=".(isset($this->valimp1)?$this->valimp1:"null").",";
		$sql.= " valimp2=".(isset($this->valimp2)?$this->valimp2:"null").",";
		$sql.= " valimp3=".(isset($this->valimp3)?$this->valimp3:"null").",";
		$sql.= " valimp4=".(isset($this->valimp4)?$this->valimp4:"null").",";
		$sql.= " valimp5=".(isset($this->valimp5)?$this->valimp5:"null").",";
		$sql.= " valret1=".(isset($this->valret1)?$this->valret1:"null").",";
		$sql.= " valret2=".(isset($this->valret2)?$this->valret2:"null").",";
		$sql.= " valret3=".(isset($this->valret3)?$this->valret3:"null").",";
		$sql.= " valret4=".(isset($this->valret4)?$this->valret4:"null").",";
		$sql.= " valret5=".(isset($this->valret5)?$this->valret5:"null").",";
		$sql.= " date_create=".(dol_strlen($this->date_create)!=0 ? "'".$this->db->idate($this->date_create)."'" : 'null').",";
		$sql.= " fk_user_create=".(isset($this->fk_user_create)?$this->fk_user_create:"null").",";
		$sql.= " statut_print=".(isset($this->statut_print)?$this->statut_print:"null").",";
		$sql.= " status=".(isset($this->status)?$this->status:"null")."";

        
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
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."c_fiscal";
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

		$object=new Cfiscal($this->db);

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
		$this->nfiscal='';
		$this->serie='';
		$this->fk_facture_fourn='';
		$this->fk_soc='';
		$this->nit='';
		$this->razsoc='';
		$this->date_exp='';
		$this->num_autoriz='';
		$this->cod_control='';
		$this->baseimp1='';
		$this->baseimp2='';
		$this->baseimp3='';
		$this->baseimp4='';
		$this->baseimp5='';
		$this->aliqimp1='';
		$this->aliqimp2='';
		$this->aliqimp3='';
		$this->aliqimp4='';
		$this->aliqimp5='';
		$this->valimp1='';
		$this->valimp2='';
		$this->valimp3='';
		$this->valimp4='';
		$this->valimp5='';
		$this->valret1='';
		$this->valret2='';
		$this->valret3='';
		$this->valret4='';
		$this->valret5='';
		$this->date_create='';
		$this->fk_user_create='';
		$this->statut_print='';
		$this->status='';

		
	}

}
?>
