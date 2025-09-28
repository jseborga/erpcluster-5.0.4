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
 *  \file       dev/skeletons/fabrication.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2015-05-04 13:58
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Fabrication extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='fabrication';			//!< Id that identify managed objects
	var $table_element='fabrication';		//!< Name of table without prefix where object is stored

	var $id;

	var $entity;
	var $ref;
	var $fk_commande;
	var $date_creation='';
	var $date_delivery='';
	var $date_init='';
	var $date_finish='';
	var $description;
	var $model_pdf;
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
    	if (isset($this->fk_commande)) $this->fk_commande=trim($this->fk_commande);
    	if (isset($this->description)) $this->description=trim($this->description);
    	if (isset($this->model_pdf)) $this->model_pdf=trim($this->model_pdf);
    	if (isset($this->statut)) $this->statut=trim($this->statut);



		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
    	$sql = "INSERT INTO ".MAIN_DB_PREFIX."fabrication(";

    	$sql.= "entity,";
    	$sql.= "ref,";
    	$sql.= "fk_commande,";
    	$sql.= "date_creation,";
    	$sql.= "date_delivery,";
    	$sql.= "date_init,";
    	$sql.= "date_finish,";
    	$sql.= "description,";
    	$sql.= "model_pdf,";
    	$sql.= "statut";


    	$sql.= ") VALUES (";

    	$sql.= " ".(! isset($this->entity)?'NULL':"'".$this->entity."'").",";
    	$sql.= " ".(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").",";
    	$sql.= " ".(! isset($this->fk_commande)?'NULL':"'".$this->fk_commande."'").",";
    	$sql.= " ".(! isset($this->date_creation) || dol_strlen($this->date_creation)==0?'NULL':$this->db->idate($this->date_creation)).",";
    	$sql.= " ".(! isset($this->date_delivery) || dol_strlen($this->date_delivery)==0?'NULL':$this->db->idate($this->date_delivery)).",";
    	$sql.= " ".(! isset($this->date_init) || dol_strlen($this->date_init)==0?'NULL':$this->db->idate($this->date_init)).",";
    	$sql.= " ".(! isset($this->date_finish) || dol_strlen($this->date_finish)==0?'NULL':$this->db->idate($this->date_finish)).",";
    	$sql.= " ".(! isset($this->description)?'NULL':"'".$this->db->escape($this->description)."'").",";
    	$sql.= " ".(! isset($this->model_pdf)?'NULL':"'".$this->db->escape($this->model_pdf)."'").",";
    	$sql.= " ".(! isset($this->statut)?'NULL':"'".$this->statut."'")."";


    	$sql.= ")";

    	$this->db->begin();

    	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
    	$resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

    	if (! $error)
    	{
    		$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."fabrication");

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
    	$sql.= " t.fk_commande,";
    	$sql.= " t.date_creation,";
    	$sql.= " t.date_delivery,";
    	$sql.= " t.date_init,";
    	$sql.= " t.date_finish,";
    	$sql.= " t.description,";
    	$sql.= " t.model_pdf,";
    	$sql.= " t.statut";


    	$sql.= " FROM ".MAIN_DB_PREFIX."fabrication as t";
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
    			$this->fk_commande = $obj->fk_commande;
    			$this->date_creation = $this->db->jdate($obj->date_creation);
    			$this->date_delivery = $this->db->jdate($obj->date_delivery);
    			$this->date_init = $this->db->jdate($obj->date_init);
    			$this->date_finish = $this->db->jdate($obj->date_finish);
    			$this->description = $obj->description;
    			$this->model_pdf = $obj->model_pdf;
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
    	if (isset($this->fk_commande)) $this->fk_commande=trim($this->fk_commande);
    	if (isset($this->description)) $this->description=trim($this->description);
    	if (isset($this->model_pdf)) $this->model_pdf=trim($this->model_pdf);
    	if (isset($this->statut)) $this->statut=trim($this->statut);



		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
    	$sql = "UPDATE ".MAIN_DB_PREFIX."fabrication SET";

    	$sql.= " entity=".(isset($this->entity)?$this->entity:"null").",";
    	$sql.= " ref=".(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").",";
    	$sql.= " fk_commande=".(isset($this->fk_commande)?$this->fk_commande:"null").",";
    	$sql.= " date_creation=".(dol_strlen($this->date_creation)!=0 ? "'".$this->db->idate($this->date_creation)."'" : 'null').",";
    	$sql.= " date_delivery=".(dol_strlen($this->date_delivery)!=0 ? "'".$this->db->idate($this->date_delivery)."'" : 'null').",";
    	$sql.= " date_init=".(dol_strlen($this->date_init)!=0 ? "'".$this->db->idate($this->date_init)."'" : 'null').",";
    	$sql.= " date_finish=".(dol_strlen($this->date_finish)!=0 ? "'".$this->db->idate($this->date_finish)."'" : 'null').",";
    	$sql.= " description=".(isset($this->description)?"'".$this->db->escape($this->description)."'":"null").",";
    	$sql.= " model_pdf=".(isset($this->model_pdf)?"'".$this->db->escape($this->model_pdf)."'":"null").",";
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
 			$sql = "DELETE FROM ".MAIN_DB_PREFIX."fabrication";
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

		$object=new Fabrication($this->db);

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
		$this->fk_commande='';
		$this->date_creation='';
		$this->date_delivery='';
		$this->date_init='';
		$this->date_finish='';
		$this->description='';
		$this->model_pdf='';
		$this->statut='';


	}

		//MODIFICADO
    /**
     *	Return statut label of Order
     *
     *	@param      int		$mode       0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
     *	@return     string      		Libelle
     */
    function getLibStatut($mode,$facturee)
    {
    	return $this->LibStatut($this->statut,$facturee,$mode);
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
        	//echo 'alm '.$facturee;
    		if ($statut==-1) return img_picto($langs->trans('StatusOrderCanceled'),'statut5').' '.$langs->trans('StatusOrderCanceled');
    		if ($statut==0) return img_picto($langs->trans('StatusOrderDraft'),'statut0').' '.$langs->trans('StatusOrderDraft');
            //if ($statut==1) return img_picto($langs->trans('StatusOrderValidated'),'statut1').' '.$langs->trans('StatusOrderValidated');
    		if ($statut==2) return img_picto($langs->trans('StatusOrderSentShort'),'statut3').' '.$langs->trans('StatusOrderSent');
    		if ($statut==1 && (is_null($facturee) || empty($facturee))) return img_picto($langs->trans('StatusOrderPending'),'statut6').' '.$langs->trans('StatusOrderPending');
    		if ($statut==1 && $facturee ==1) return img_picto($langs->trans('Por recibir'),'statut6').' '.$langs->trans('Por recibir');
    		if ($statut==1 && $facturee ==2) return img_picto($langs->trans('StatusOrderProcess'),'statut7').' '.$langs->trans('StatusOrderProcess');

    		if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderToBill'),'statut7').' '.$langs->trans('Entregado a cliente');
    		if ($statut==3 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderProcessed'),'statut6').' '.$langs->trans('Entregado a CLiente');
    	}
    	elseif ($mode == 5)
    	{
    		if ($statut==-1) return $langs->trans('StatusOrderCanceledShort').' '.img_picto($langs->trans('StatusOrderCanceled'),'statut5');
    		if ($statut==0) return $langs->trans('StatusOrderDraftShort').' '.img_picto($langs->trans('StatusOrderDraft'),'statut0');
    		if ($statut==2) return $langs->trans('StatusOrderSentShort').' '.img_picto($langs->trans('StatusOrderSent'),'statut3');
    		if ($statut==1 && $facturee == -2) return img_picto($langs->trans('StatusOrderPending'),'statut0').' '.$langs->trans('StatusOrderPending');
    		if ($statut==1 && $facturee ==0) return img_picto($langs->trans('StatusOrderDraft'),'statut0').' '.$langs->trans('StatusOrderDraft');
    		if ($statut==1 && $facturee == 1) return img_picto($langs->trans('StatusOrderProcess'),'statut4').' '.$langs->trans('StatusOrderProcess');
    		if ($statut==1 && $facturee ==2) return img_picto($langs->trans('StatusOrderDelivered'),'statut7').' '.$langs->trans('StatusOrderDelivered');
    		if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('Entregado a cliente').' '.img_picto($langs->trans('Entregado a cliente'),'statut7');
    		if ($statut==3 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('Entregado a Cliente').' '.img_picto($langs->trans('Entregado a Cliente'),'statut6');
    	}
    }

    /**
     *	Return a array with the pending commande
     *
     *	@param      int		$filtre_statut      Filtre sur statut
     *	@return     int                 		0 si OK, <0 si KO
     *
     *	TODO		FONCTION NON FINIE A FINIR
     */
    function commande_array($filtre_statut=-1)
    {
    	$this->commande = array();

        // Tableau des id de produit de la commande
    	$array_of_commande=array();

        // Recherche total en stock pour chaque produit
        // TODO $array_of_product est défini vide juste au dessus !!
    	if (count($array_of_commande))
    	{
    		$sql = "SELECT rowid, ref";
    		$sql.= " FROM ".MAIN_DB_PREFIX."commande as c";
    		$sql.= " WHERE c.entity IN (".$conf->entity.")";
    		$sql.= ' ORDER BY ref ';
    		$result = $this->db->query($sql);
    		if ($result)
    		{
    			$num = $this->db->num_rows($result);
    			$i = 0;
    			while ($i < $num)
    			{
    				$obj = $this->db->fetch_object($result);
    				$this->commande[$obj->rowid] = $obj->ref;
    				$i++;
    			}
    			$this->db->free();
    		}
    	}
    	return 0;
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
    function select_commande($selected='',$htmlname='pays_id',$htmloption='',$maxlength=0,$vacio=0)
    {
    	global $conf,$langs;

    	$langs->load("fabricationlang@fabrication");

    	$out='';
    	$countryArray=array();
    	$label=array();

    	$sql = "SELECT rowid, ref";
    	$sql.= " FROM ".MAIN_DB_PREFIX."commande as c";
    	$sql.= " WHERE c.entity IN (".$conf->entity.")";
    	$sql.= " AND c.fk_statut=1";
    	$sql.= ' ORDER BY ref ASC ';

    	dol_syslog(get_class($this)."::select_commande sql=".$sql);
    	$resql=$this->db->query($sql);
    	if ($resql)
    	{
    		$out.= '<select id="select'.$htmlname.'" class="flat selectpays" name="'.$htmlname.'" '.$htmloption.'>';
    		$num = $this->db->num_rows($resql);
    		$i = 0;
    		if ($num)
    		{
    			$foundselected=false;

    			while ($i < $num)
    			{
    				$obj = $this->db->fetch_object($resql);
    				$countryArray[$i]['rowid'] 		= $obj->rowid;
    				$countryArray[$i]['ref'] 	= $obj->ref;
    				$i++;
    			}

                //array_multisort($label, SORT_ASC, $countryArray);
    			if ($vacio)
    			{
    				$out.= '<option value="0" ></option>';

    			}
    			foreach ($countryArray as $row)
    			{
                    //print 'rr'.$selected.'-'.$row['label'].'-'.$row['code_iso'].'<br>';
    				if ($selected && $selected != '-1' && ($selected == $row['rowid'] || $selected == $row['ref']) )
    				{
    					$foundselected=true;
    					$out.= '<option value="'.$row['rowid'].'" selected="selected">';
    				}
    				else
    				{
    					$out.= '<option value="'.$row['rowid'].'">';
    				}
                    //$out.= dol_trunc($row['ref'],$maxlength,'middle');
    				$out.= $row['ref'];
                    //if ($row['code_iso']) $out.= ' ('.$row['code_iso'] . ')';
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
     *  Return combo list of activated countries, into language of user
     *
     *  @param	string	$selected       Id or Code or Label of preselected country
     *  @param  string	$htmlname       Name of html select object
     *  @param  string	$htmloption     Options html on select object
     *  @param	string	$maxlength		Max length for labels (0=no limit)
     *  @return string           		HTML string with select
     */
    function select_fabrication($selected='',$htmlname='fk_fabrication',$htmloption='',$maxlength=0, $showempty=0,$state='')
    {
    	global $conf,$langs;

    	$langs->load("fabrication@fabrication");

    	$out='';
    	$countryArray=array();
    	$label=array();

    	$sql = "SELECT rowid, ref";
    	$sql.= " FROM ".MAIN_DB_PREFIX."fabrication as c";
    	$sql.= " WHERE c.entity IN (".$conf->entity.")";
    	if ($state)
    		$sql.= " AND c.statut IN (".$state.")";

    	$sql.= ' ORDER BY ref ASC ';

    	dol_syslog(get_class($this)."::select_fabrication sql=".$sql);
    	$resql=$this->db->query($sql);
    	if ($resql)
    	{
    		$out.= '<select id="select'.$htmlname.'" class="flat selectpays" name="'.$htmlname.'" '.$htmloption.'>';
    		$num = $this->db->num_rows($resql);
    		$i = 0;
    		if ($num)
    		{
    			$foundselected=false;

    			while ($i < $num)
    			{
    				$obj = $this->db->fetch_object($resql);
    				$countryArray[$i]['rowid'] 		= $obj->rowid;
    				$countryArray[$i]['ref'] 	= $obj->ref;
    				$i++;
    			}

                //array_multisort($label, SORT_ASC, $countryArray);
    			if ($showempty)
    			{
    				$out.= '<option value="-1"';
    				if ($selected == -1) $out.= ' selected="selected"';
    				$out.= '>&nbsp;</option>';
    			}

    			foreach ($countryArray as $row)
    			{
                    //print 'rr'.$selected.'-'.$row['label'].'-'.$row['code_iso'].'<br>';
    				if ($selected && $selected != '-1' && ($selected == $row['rowid'] || $selected == $row['ref']) )
    				{
    					$foundselected=true;
    					$out.= '<option value="'.$row['rowid'].'" selected="selected">';
    				}
    				else
    				{
    					$out.= '<option value="'.$row['rowid'].'">';
    				}
                    //$out.= dol_trunc($row['ref'],$maxlength,'middle');
    				$out.= $row['ref'];
                    //if ($row['code_iso']) $out.= ' ('.$row['code_iso'] . ')';
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
     *	Load all detailed lines into this->lines
     *
     *	@return     int         1 if OK, < 0 if KO
     */
    function fetch_lines()
    {
    	$this->lines=array();

    	$sql = 'SELECT l.rowid, l.fk_product, l.qty, l.qty_decrease, l.qty_first, l.date_end, l.price, l.price_total, ';
    	$sql.= ' p.ref as product_ref, p.fk_product_type as fk_product_type, p.label as product_label, p.description as product_desc';
    	$sql.= ' FROM '.MAIN_DB_PREFIX.'fabricationdet as l';
    	$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'product as p ON l.fk_product = p.rowid';
    	$sql.= ' WHERE l.fk_fabrication = '.$this->id;
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
    			$line = new Fabricationdet($this->db);

    			$line->rowid	        = $objp->rowid;
                $line->product_type     = $objp->product_type;		// Type of line
                $line->product_ref      = $objp->product_ref;		// Ref product
                $line->libelle          = $objp->product_label;		// TODO deprecated
                $line->product_label	= $objp->product_label;		// Label product
                $line->product_desc     = $objp->product_desc;		// Description product
                $line->qty              = $objp->qty;
                $line->qty_decrease     = $objp->qty_decrease;
                $line->qty_first        = $objp->qty_first;
                $line->price            = $objp->price;
                $line->price_total      = $objp->price_total;
                $line->fk_product       = $objp->fk_product;
                $line->date_end    = $this->db->jdate($objp->date_end);

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
	 *  Returns the reference to the following non used Order depending on the active numbering module
	 *  defined into FABRICATION_ADDON
	 *
	 *  @param	Societe		$soc  	Object thirdparty
	 *  @return string      		Order free reference
	 */
        function getNextNumRef($soc)
        {
        	global $db, $langs, $conf;
        	$langs->load("fabrication@fabrication");

        	$dir = DOL_DOCUMENT_ROOT . "/fabrication/core/modules";

        	if (! empty($conf->global->FABRICATION_ADDON))
        	{
        		$file = $conf->global->FABRICATION_ADDON.".php";
            // Chargement de la classe de numerotation
        		$classname = $conf->global->FABRICATION_ADDON;
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
        				dol_print_error($db,"Fabrication::getNextNumRef ".$obj->error);
        				return "";
        			}
        		}
        		else
        		{
        			print $langs->trans("Error")." ".$langs->trans("Error_FABRICATION_ADDON_NotDefined");
        			return "";
        		}
        	}
        	else
        	{
        		print $langs->trans("Error")." ".$langs->trans("Error_FABRICATION_ADDON_NotDefined");
        		return "";
        	}
        }

    }
    ?>
