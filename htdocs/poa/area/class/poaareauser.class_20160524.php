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
 *  \file       dev/skeletons/poaareauser.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2014-08-21 14:15
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Poaareauser extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='poaareauser';			//!< Id that identify managed objects
	var $table_element='poaareauser';		//!< Name of table without prefix where object is stored

	var $id;

	var $fk_area;
	var $fk_user;
	var $date_create='';
	var $tms='';
	var $active;
	var $privilege;
	var $array;



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

		if (isset($this->fk_area)) $this->fk_area=trim($this->fk_area);
		if (isset($this->fk_user)) $this->fk_user=trim($this->fk_user);
		if (isset($this->active)) $this->active=trim($this->active);
		if (isset($this->privilege)) $this->privilege=trim($this->privilege);



		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."poa_area_user(";

		$sql.= "fk_area,";
		$sql.= "fk_user,";
		$sql.= "date_create,";
		$sql.= "active,";
		$sql.= "privilege";


		$sql.= ") VALUES (";

		$sql.= " ".(! isset($this->fk_area)?'NULL':"'".$this->fk_area."'").",";
		$sql.= " ".(! isset($this->fk_user)?'NULL':"'".$this->fk_user."'").",";
		$sql.= " ".(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':$this->db->idate($this->date_create)).",";
		$sql.= " ".(! isset($this->active)?'NULL':"'".$this->active."'").",";
		$sql.= " ".(! isset($this->privilege)?'NULL':"'".$this->privilege."'")."";


		$sql.= ")";

		$this->db->begin();

		dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
		{
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."poa_area_user");

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

		$sql.= " t.fk_area,";
		$sql.= " t.fk_user,";
		$sql.= " t.date_create,";
		$sql.= " t.tms,";
		$sql.= " t.active,";
		$sql.= " t.privilege";


		$sql.= " FROM ".MAIN_DB_PREFIX."poa_area_user as t";
		$sql.= " WHERE t.rowid = ".$id;

		dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);

				$this->id    = $obj->rowid;

				$this->fk_area = $obj->fk_area;
				$this->fk_user = $obj->fk_user;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->tms = $this->db->jdate($obj->tms);
				$this->active = $obj->active;
				$this->privilege = $obj->privilege;


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

		if (isset($this->fk_area)) $this->fk_area=trim($this->fk_area);
		if (isset($this->fk_user)) $this->fk_user=trim($this->fk_user);
		if (isset($this->active)) $this->active=trim($this->active);
		if (isset($this->privilege)) $this->privilege=trim($this->privilege);



		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = "UPDATE ".MAIN_DB_PREFIX."poa_area_user SET";

		$sql.= " fk_area=".(isset($this->fk_area)?$this->fk_area:"null").",";
		$sql.= " fk_user=".(isset($this->fk_user)?$this->fk_user:"null").",";
		$sql.= " date_create=".(dol_strlen($this->date_create)!=0 ? "'".$this->db->idate($this->date_create)."'" : 'null').",";
		$sql.= " tms=".(dol_strlen($this->tms)!=0 ? "'".$this->db->idate($this->tms)."'" : 'null').",";
		$sql.= " active=".(isset($this->active)?$this->active:"null").",";
		$sql.= " privilege=".(isset($this->privilege)?$this->privilege:"null")."";


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
			$sql = "DELETE FROM ".MAIN_DB_PREFIX."poa_area_user";
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

		$object=new Poaareauser($this->db);

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

		$this->fk_area='';
		$this->fk_user='';
		$this->date_create='';
		$this->tms='';
		$this->active='';
		$this->privilege='';


	}


	//modificado
	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getlist($fk_area,$fk_user=0)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.fk_area,";
		$sql.= " t.fk_user,";
		$sql.= " t.date_create,";
		$sql.= " t.tms,";
		$sql.= " t.active,";
		$sql.= " t.privilege";

		$sql.= " FROM ".MAIN_DB_PREFIX."poa_area_user as t";
		$sql.= " WHERE t.fk_area = ".$fk_area;
		if ($fk_user>0)
			$sql.= " AND t.fk_user = ".$fk_user;
		dol_syslog(get_class($this)."::getlist sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->array = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objnew = new Poaareauser($this->db);
					$objnew->id      = $obj->rowid;
					$objnew->fk_area = $obj->fk_area;
					$objnew->fk_user = $obj->fk_user;
					$objnew->date_create = $this->db->jdate($obj->date_create);
					$objnew->tms         = $this->db->jdate($obj->tms);
					$objnew->active      = $obj->active;
					$objnew->privilege   = $obj->privilege;

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

	/**
	 *	Return label of status of object
	 *
	 *	@param      int	$mode       0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto
	 *	@param      int	$type       0=Shell, 1=Buy
	 *	@return     string      	Label of status
	 */
	function getLibStatut($mode=0, $type=0)
	{
		return $this->LibStatut($this->active,$mode,$type);
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
			if ($status == 0) return img_picto($langs->trans('Inactive'),'switch_off');
			if ($status == 1) return img_picto($langs->trans('Active'),'switch_on');
		}
		if ($mode == 3)
		{
			if ($status == 0) return img_picto($langs->trans('Inactive'),'switch_off1');
			if ($status == 1) return img_picto($langs->trans('Active'),'switch_on1');
		}
		if ($mode == 4)
		{
			if ($status == 0) return $langs->trans('No permissions');
			if ($status == 1) return $langs->trans('Adminarea');
			if ($status == 2) return $langs->trans('User');
			if ($status == 3) return $langs->trans('Away');
		}

		return $langs->trans('Unknown');
	}


	/**
	 *  Load object in memory from the database
	 *  Areas a las que pertenece el usuario
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getuserarea($fk_user)
	{
		global $langs;
		$aArea = array();
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.fk_area,";
		$sql.= " t.fk_user,";
		$sql.= " t.date_create,";
		$sql.= " t.tms,";
		$sql.= " t.active,";
		$sql.= " t.privilege";
		$sql.= " FROM ".MAIN_DB_PREFIX."poa_area_user as t";
		$sql.= " WHERE t.fk_user = ".$fk_user;
		$sql.= " AND t.active = 1";
		dol_syslog(get_class($this)."::getuserarea sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					include_once DOL_DOCUMENT_ROOT.'/poa/area/class/poaarea.class.php';
					$obja = new Poaarea($this->db);
					if ($obja->fetch($obj->fk_area)>0)
					{
						if ($obja->id == $obj->fk_area)
						{
							$obja->privilege = $obj->privilege;
							$aArea[$obj->fk_area] = $obja;
						}
					}
					$obja->getlist_son($obj->fk_area);
					if (count($obja->array) > 0)
					{
						foreach ((array) $obja->array AS $j => $objar)
						{
							$objar->privilege = $obj->privilege;
							$aArea[$objar->id] = $objar;
				  				//nuevamente buscamos si tiene hijos del hijo
							$objaa = new Poaarea($this->db);
							$objaa->getlist_son($objar->id);
							if (count($objaa->array) > 0)
							{
								foreach ((array) $objaa->array AS $k => $objara)
								{
									$objara->privilege = $obj->privilege;
									$aArea[$objara->id] = $objara;

								}
							}

						}
					}
					$i++;
				}
				return $aArea;
			}
			$this->db->free($resql);

			return array();
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getuserarea ".$this->error, LOG_ERR);
			return -1;
		}
	}


}
?>
