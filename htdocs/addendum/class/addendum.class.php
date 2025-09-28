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
 *  \file       dev/skeletons/addendum.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2015-04-07 12:12
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Addendum extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='addendum';			//!< Id that identify managed objects
	var $table_element='addendum';		//!< Name of table without prefix where object is stored

	var $id;

	var $fk_contrat_father;
	var $fk_contrat_son;
	var $date_create='';
	var $fk_user_create;
	var $tms='';
	var $statut;
	var $array;
	var $total_ht;
	var $total_tva;
	var $total_localtax1;
	var $total_localtax2;
	var $total_ttc;
	var $aSuma;


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

    	if (isset($this->fk_contrat_father)) $this->fk_contrat_father=trim($this->fk_contrat_father);
    	if (isset($this->fk_contrat_son)) $this->fk_contrat_son=trim($this->fk_contrat_son);
    	if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
    	if (isset($this->statut)) $this->statut=trim($this->statut);



		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
    	$sql = "INSERT INTO ".MAIN_DB_PREFIX."addendum(";

    	$sql.= "fk_contrat_father,";
    	$sql.= "fk_contrat_son,";
    	$sql.= "date_create,";
    	$sql.= "fk_user_create,";
    	$sql.= "statut";


    	$sql.= ") VALUES (";

    	$sql.= " ".(! isset($this->fk_contrat_father)?'NULL':"'".$this->fk_contrat_father."'").",";
    	$sql.= " ".(! isset($this->fk_contrat_son)?'NULL':"'".$this->fk_contrat_son."'").",";
    	$sql.= " ".(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':$this->db->idate($this->date_create)).",";
    	$sql.= " ".(! isset($this->fk_user_create)?'NULL':"'".$this->fk_user_create."'").",";
    	$sql.= " ".(! isset($this->statut)?'NULL':"'".$this->statut."'")."";


    	$sql.= ")";

    	$this->db->begin();

    	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
    	$resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

    	if (! $error)
    	{
    		$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."addendum");

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

    	$sql.= " t.fk_contrat_father,";
    	$sql.= " t.fk_contrat_son,";
    	$sql.= " t.date_create,";
    	$sql.= " t.fk_user_create,";
    	$sql.= " t.tms,";
    	$sql.= " t.statut";


    	$sql.= " FROM ".MAIN_DB_PREFIX."addendum as t";
    	$sql.= " WHERE t.rowid = ".$id;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
    	$resql=$this->db->query($sql);
    	if ($resql)
    	{
    		if ($this->db->num_rows($resql))
    		{
    			$obj = $this->db->fetch_object($resql);

    			$this->id    = $obj->rowid;

    			$this->fk_contrat_father = $obj->fk_contrat_father;
    			$this->fk_contrat_son = $obj->fk_contrat_son;
    			$this->date_create = $this->db->jdate($obj->date_create);
    			$this->fk_user_create = $obj->fk_user_create;
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

    	if (isset($this->fk_contrat_father)) $this->fk_contrat_father=trim($this->fk_contrat_father);
    	if (isset($this->fk_contrat_son)) $this->fk_contrat_son=trim($this->fk_contrat_son);
    	if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
    	if (isset($this->statut)) $this->statut=trim($this->statut);



		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
    	$sql = "UPDATE ".MAIN_DB_PREFIX."addendum SET";

    	$sql.= " fk_contrat_father=".(isset($this->fk_contrat_father)?$this->fk_contrat_father:"null").",";
    	$sql.= " fk_contrat_son=".(isset($this->fk_contrat_son)?$this->fk_contrat_son:"null").",";
    	$sql.= " date_create=".(dol_strlen($this->date_create)!=0 ? "'".$this->db->idate($this->date_create)."'" : 'null').",";
    	$sql.= " fk_user_create=".(isset($this->fk_user_create)?$this->fk_user_create:"null").",";
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
 			$sql = "DELETE FROM ".MAIN_DB_PREFIX."addendum";
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

		$object=new Addendum($this->db);

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
		
		$this->fk_contrat_father='';
		$this->fk_contrat_son='';
		$this->date_create='';
		$this->fk_user_create='';
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
	function getlist($id)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.fk_contrat_father,";
		$sql.= " t.fk_contrat_son,";
		$sql.= " t.date_create,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut";


		$sql.= " FROM ".MAIN_DB_PREFIX."addendum as t";
		$sql.= " WHERE t.fk_contrat_father = ".$id;

		dol_syslog(get_class($this)."::getlist sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->array = array();
		$this->aSuma = array();
		if ($resql)
		{
	      //obteniendo la suma del principal
			$this->get_suma_contratdet($id);
			$this->aSuma['total_ht'] = $this->total_ht;
			$this->aSuma['total_tva'] = $this->total_tva;
			$this->aSuma['total_localtax1'] = $this->total_localtax1;
			$this->aSuma['total_localtax2'] = $this->total_localtax2;
			$this->aSuma['total_ttc'] = $this->total_ttc;
			$this->aSuma['parcial_ttc'][$id] = $this->total_ttc;

			$num = $this->db->num_rows($resql);
			if ($this->db->num_rows($resql))
			{
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objnew = new Addendum($this->db);

					$objnew->id    = $obj->rowid;
					$objnew->fk_contrat_father = $obj->fk_contrat_father;
					$objnew->fk_contrat_son = $obj->fk_contrat_son;
					$objnew->date_create = $this->db->jdate($obj->date_create);
					$objnew->fk_user_create = $obj->fk_user_create;
					$objnew->tms = $this->db->jdate($obj->tms);
					$objnew->statut = $obj->statut;

		      //obteniendo la suma del principal
					$this->get_suma_contratdet($obj->fk_contrat_son);
					$this->aSuma['total_ht'] += $this->total_ht;
					$this->aSuma['total_tva'] += $this->total_tva;
					$this->aSuma['total_localtax1'] += $this->total_localtax1;
					$this->aSuma['total_localtax2'] += $this->total_localtax2;
					$this->aSuma['total_ttc'] += $this->total_ttc;
					$this->aSuma['parcial_ttc'][$obj->fk_contrat_son] = $this->total_ttc;
					$this->array[$obj->rowid] = $objnew;
					$i++;
				}		      
				$this->db->free($resql);
				return $num;

			}
			$this->db->free($resql);
			return 0;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getlist ".$this->error, LOG_ERR);
			return -1;
		}
	}

		/**
	 *    	Load object in memory from database
	 *
	 *    	@param	int		$id         Id object
	 * 		@param	string	$ref		Ref of contract
	 *    	@return int         		<0 if KO, >0 if OK
	 */
		function get_suma_contratdet($fk_contrat)
		{
			global $langs,$user;

			$sql = "SELECT";
			$sql.= " SUM(t.total_ht) as total_ht,";
			$sql.= " SUM(t.total_tva) as total_tva,";
			$sql.= " SUM(t.total_localtax1) as total_localtax1,";
			$sql.= " SUM(t.total_localtax2) as total_localtax2,";
			$sql.= " SUM(t.total_ttc) as total_ttc";

			$sql.= " FROM ".MAIN_DB_PREFIX."contratdet as t";
			$sql.= " WHERE t.fk_contrat = ".$fk_contrat;

			dol_syslog(get_class($this)."::get_suma_contratdet sql=".$sql, LOG_DEBUG);
			$resql=$this->db->query($sql);

			$this->total_ht = 0;
			$this->total_tva = 0;
			$this->total_localtax1 = 0;
			$this->total_localtax2 = 0;
			$this->total_ttc = 0;

			if ($resql)
			{
				if ($this->db->num_rows($resql))
				{
					$obj = $this->db->fetch_object($resql);

					$this->total_ht = $obj->total_ht;
					$this->total_tva = $obj->total_tva;
					$this->total_localtax1 = $obj->total_localtax1;
					$this->total_localtax2 = $obj->total_localtax2;
					$this->total_ttc = $obj->total_ttc;

				}
	      //$this->db->free($resql);

				return 1;
			}
			else
			{
				$this->error="Error ".$this->db->lasterror();
				dol_syslog(get_class($this)."::get_suma_contratdet ".$this->error, LOG_ERR);
				return -1;
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
			if ($status == 0) return ($type==0 ? $langs->trans('Draft'):img_picto($langs->trans('Draft'),'state0').' '.$langs->trans('Draft'));
			if ($status == 1) return ($type==0 ? $langs->trans('Approved'):img_picto($langs->trans('Approved'),'state1').' '.$langs->trans('Approved'));
		}

		if ($mode == 1)
		{
			if ($status == 0) return img_picto($langs->trans('Notapproved'),'statut5').' '.($type==0 ? $langs->trans('Notapproved'):$langs->trans('Reformulation unapproved'));
			if ($status == 1) return img_picto($langs->trans('Approved'),'statut4').' '.($type==0 ? $langs->trans('Approved'):$langs->trans('Reformulation approved'));
		}

		return $langs->trans('Unknown');
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getlist_son($id,$campo='fk_contrat_son')
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.fk_contrat_father,";
		$sql.= " t.fk_contrat_son,";
		$sql.= " t.date_create,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut";

		$sql.= " FROM ".MAIN_DB_PREFIX."addendum as t";
		$sql.= " WHERE t.".$campo." = ".$id;

		dol_syslog(get_class($this)."::getlist_son sql=".$sql, LOG_DEBUG);
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
					$objnew = new Addendum($this->db);
					$objnew->id    = $obj->rowid;

					$objnew->fk_contrat_father = $obj->fk_contrat_father;
					$objnew->fk_contrat_son = $obj->fk_contrat_son;
					$objnew->date_create = $this->db->jdate($obj->date_create);
					$objnew->fk_user_create = $obj->fk_user_create;
					$objnew->tms = $this->db->jdate($obj->tms);
					$objnew->statut = $obj->statut;
					$this->array[$obj->rowid] = $objnew;
					$i++;
				}
				$this->db->free($resql);
				return 1;
			}
			$this->db->free($resql);
			return 0;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getlist_son ".$this->error, LOG_ERR);
			return -1;
		}
	}

}
?>
