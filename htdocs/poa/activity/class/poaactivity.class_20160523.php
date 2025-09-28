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
 *  \file       dev/skeletons/poaactivity.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2015-04-06 11:01
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Poaactivity extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='poa_activity';			//!< Id that identify managed objects
	var $table_element='poa_activity';		//!< Name of table without prefix where object is stored

	var $id;

	var $entity;
	var $gestion;
	var $fk_poa;
	var $fk_pac;
	var $fk_prev_ant;
	var $fk_prev;
	var $fk_area;
	var $code_requirement;
	var $label;
	var $pseudonym;
	var $nro_activity;
	var $date_activity='';
	var $partida;
	var $amount;
	var $priority;
	var $date_create='';
	var $fk_user_create;
	var $tms='';
	var $statut;
	var $active;
	var $array;
	var $aCount;
	var $aCountfin;
	var $aSum;
	var $aSumfin;
	var $aPac;
	var $aPacne;
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
		if (isset($this->gestion)) $this->gestion=trim($this->gestion);
		if (isset($this->fk_poa)) $this->fk_poa=trim($this->fk_poa);
		if (isset($this->fk_pac)) $this->fk_pac=trim($this->fk_pac);
		if (isset($this->fk_prev_ant)) $this->fk_prev_ant=trim($this->fk_prev_ant);
		if (isset($this->fk_prev)) $this->fk_prev=trim($this->fk_prev);
		if (isset($this->fk_area)) $this->fk_area=trim($this->fk_area);
		if (isset($this->code_requirement)) $this->code_requirement=trim($this->code_requirement);
		if (isset($this->label)) $this->label=trim($this->label);
		if (isset($this->pseudonym)) $this->pseudonym=trim($this->pseudonym);
		if (isset($this->nro_activity)) $this->nro_activity=trim($this->nro_activity);
		if (isset($this->partida)) $this->partida=trim($this->partida);
		if (isset($this->amount)) $this->amount=trim($this->amount);
		if (isset($this->priority)) $this->priority=trim($this->priority);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->statut)) $this->statut=trim($this->statut);
		if (isset($this->active)) $this->active=trim($this->active);



		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."poa_activity(";

		$sql.= "entity,";
		$sql.= "gestion,";
		$sql.= "fk_poa,";
		$sql.= "fk_pac,";
		$sql.= "fk_prev_ant,";
		$sql.= "fk_prev,";
		$sql.= "fk_area,";
		$sql.= "code_requirement,";
		$sql.= "label,";
		$sql.= "pseudonym,";
		$sql.= "nro_activity,";
		$sql.= "date_activity,";
		$sql.= "partida,";
		$sql.= "amount,";
		$sql.= "priority,";
		$sql.= "date_create,";
		$sql.= "fk_user_create,";
		$sql.= "statut,";
		$sql.= "active";


		$sql.= ") VALUES (";

		$sql.= " ".(! isset($this->entity)?'NULL':"'".$this->entity."'").",";
		$sql.= " ".(! isset($this->gestion)?'NULL':"'".$this->gestion."'").",";
		$sql.= " ".(! isset($this->fk_poa)?'NULL':"'".$this->fk_poa."'").",";
		$sql.= " ".(! isset($this->fk_pac)?'NULL':"'".$this->fk_pac."'").",";
		$sql.= " ".(! isset($this->fk_prev_ant)?'NULL':"'".$this->fk_prev_ant."'").",";
		$sql.= " ".(! isset($this->fk_prev)?'NULL':"'".$this->fk_prev."'").",";
		$sql.= " ".(! isset($this->fk_area)?'NULL':"'".$this->fk_area."'").",";
		$sql.= " ".(! isset($this->code_requirement)?'NULL':"'".$this->db->escape($this->code_requirement)."'").",";
		$sql.= " ".(! isset($this->label)?'NULL':"'".$this->db->escape($this->label)."'").",";
		$sql.= " ".(! isset($this->pseudonym)?'NULL':"'".$this->db->escape($this->pseudonym)."'").",";
		$sql.= " ".(! isset($this->nro_activity)?'NULL':"'".$this->nro_activity."'").",";
		$sql.= " ".(! isset($this->date_activity) || dol_strlen($this->date_activity)==0?'NULL':$this->db->idate($this->date_activity)).",";
		$sql.= " ".(! isset($this->partida)?'NULL':"'".$this->db->escape($this->partida)."'").",";
		$sql.= " ".(! isset($this->amount)?'NULL':"'".$this->amount."'").",";
		$sql.= " ".(! isset($this->priority)?'NULL':"'".$this->priority."'").",";
		$sql.= " ".(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':$this->db->idate($this->date_create)).",";
		$sql.= " ".(! isset($this->fk_user_create)?'NULL':"'".$this->fk_user_create."'").",";
		$sql.= " ".(! isset($this->statut)?'NULL':"'".$this->statut."'").",";
		$sql.= " ".(! isset($this->active)?'NULL':"'".$this->active."'")."";


		$sql.= ")";

		$this->db->begin();

		dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
		{
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."poa_activity");

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
	function fetch($id,$fk_prev='')
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.gestion,";
		$sql.= " t.fk_poa,";
		$sql.= " t.fk_pac,";
		$sql.= " t.fk_prev_ant,";
		$sql.= " t.fk_prev,";
		$sql.= " t.fk_area,";
		$sql.= " t.code_requirement,";
		$sql.= " t.label,";
		$sql.= " t.pseudonym,";
		$sql.= " t.nro_activity,";
		$sql.= " t.date_activity,";
		$sql.= " t.partida,";
		$sql.= " t.amount,";
		$sql.= " t.priority,";
		$sql.= " t.date_create,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut,";
		$sql.= " t.active";


		$sql.= " FROM ".MAIN_DB_PREFIX."poa_activity as t";
		if ($id)
			$sql.= " WHERE t.rowid = ".$id;
		if ($fk_prev)
			$sql.= " WHERE t.fk_prev = ".$fk_prev;

		dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);

				$this->id    = $obj->rowid;

				$this->entity = $obj->entity;
				$this->gestion = $obj->gestion;
				$this->fk_poa = $obj->fk_poa;
				$this->fk_pac = $obj->fk_pac;
				$this->fk_prev_ant = $obj->fk_prev_ant;
				$this->fk_prev = $obj->fk_prev;
				$this->fk_area = $obj->fk_area;
				$this->code_requirement = $obj->code_requirement;
				$this->label = $obj->label;
				$this->pseudonym = $obj->pseudonym;
				$this->nro_activity = $obj->nro_activity;
				$this->date_activity = $this->db->jdate($obj->date_activity);
				$this->partida = $obj->partida;
				$this->amount = $obj->amount;
				$this->priority = $obj->priority;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->fk_user_create = $obj->fk_user_create;
				$this->tms = $this->db->jdate($obj->tms);
				$this->statut = $obj->statut;
				$this->active = $obj->active;


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
		if (isset($this->gestion)) $this->gestion=trim($this->gestion);
		if (isset($this->fk_poa)) $this->fk_poa=trim($this->fk_poa);
		if (isset($this->fk_pac)) $this->fk_pac=trim($this->fk_pac);
		if (isset($this->fk_prev_ant)) $this->fk_prev_ant=trim($this->fk_prev_ant);
		if (isset($this->fk_prev)) $this->fk_prev=trim($this->fk_prev);
		if (isset($this->fk_area)) $this->fk_area=trim($this->fk_area);
		if (isset($this->code_requirement)) $this->code_requirement=trim($this->code_requirement);
		if (isset($this->label)) $this->label=trim($this->label);
		if (isset($this->pseudonym)) $this->pseudonym=trim($this->pseudonym);
		if (isset($this->nro_activity)) $this->nro_activity=trim($this->nro_activity);
		if (isset($this->partida)) $this->partida=trim($this->partida);
		if (isset($this->amount)) $this->amount=trim($this->amount);
		if (isset($this->priority)) $this->priority=trim($this->priority);
		if (isset($this->fk_user_create)) $this->fk_user_create=trim($this->fk_user_create);
		if (isset($this->statut)) $this->statut=trim($this->statut);
		if (isset($this->active)) $this->active=trim($this->active);



		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = "UPDATE ".MAIN_DB_PREFIX."poa_activity SET";

		$sql.= " entity=".(isset($this->entity)?$this->entity:"null").",";
		$sql.= " gestion=".(isset($this->gestion)?$this->gestion:"null").",";
		$sql.= " fk_poa=".(isset($this->fk_poa)?$this->fk_poa:"null").",";
		$sql.= " fk_pac=".(isset($this->fk_pac)?$this->fk_pac:"null").",";
		$sql.= " fk_prev_ant=".(isset($this->fk_prev_ant)?$this->fk_prev_ant:"null").",";
		$sql.= " fk_prev=".(isset($this->fk_prev)?$this->fk_prev:"null").",";
		$sql.= " fk_area=".(isset($this->fk_area)?$this->fk_area:"null").",";
		$sql.= " code_requirement=".(isset($this->code_requirement)?"'".$this->db->escape($this->code_requirement)."'":"null").",";
		$sql.= " label=".(isset($this->label)?"'".$this->db->escape($this->label)."'":"null").",";
		$sql.= " pseudonym=".(isset($this->pseudonym)?"'".$this->db->escape($this->pseudonym)."'":"null").",";
		$sql.= " nro_activity=".(isset($this->nro_activity)?$this->nro_activity:"null").",";
		$sql.= " date_activity=".(dol_strlen($this->date_activity)!=0 ? "'".$this->db->idate($this->date_activity)."'" : 'null').",";
		$sql.= " partida=".(isset($this->partida)?"'".$this->db->escape($this->partida)."'":"null").",";
		$sql.= " amount=".(isset($this->amount)?$this->amount:"null").",";
		$sql.= " priority=".(isset($this->priority)?$this->priority:"null").",";
		$sql.= " date_create=".(dol_strlen($this->date_create)!=0 ? "'".$this->db->idate($this->date_create)."'" : 'null').",";
		$sql.= " fk_user_create=".(isset($this->fk_user_create)?$this->fk_user_create:"null").",";
		$sql.= " tms=".(dol_strlen($this->tms)!=0 ? "'".$this->db->idate($this->tms)."'" : 'null').",";
		$sql.= " statut=".(isset($this->statut)?$this->statut:"null").",";
		$sql.= " active=".(isset($this->active)?$this->active:"null")."";


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
			$sql = "DELETE FROM ".MAIN_DB_PREFIX."poa_activity";
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

		$object=new Poaactivity($this->db);

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
		$this->gestion='';
		$this->fk_poa='';
		$this->fk_pac='';
		$this->fk_prev_ant='';
		$this->fk_prev='';
		$this->fk_area='';
		$this->code_requirement='';
		$this->label='';
		$this->pseudonym='';
		$this->nro_activity='';
		$this->date_activity='';
		$this->partida='';
		$this->amount='';
		$this->priority='';
		$this->date_create='';
		$this->fk_user_create='';
		$this->tms='';
		$this->statut='';
		$this->active='';


	}

		//MODIFICADO
	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function fetch_next_nro($gestion)
	{
		global $langs,$conf;
		$sql = "SELECT";
		$sql.= " MAX(t.nro_activity) AS maxnro ";

		$sql.= " FROM ".MAIN_DB_PREFIX."poa_activity as t";
		$sql.= " WHERE t.entity = ".$conf->entity;
		$sql.= " AND t.gestion = ".$gestion;

		dol_syslog(get_class($this)."::fetch_next_nro sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);
				return $obj->maxnro + 1;
			}
			else
				return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch_next_nro ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getlist_poa($fk_poa,$fk_user=0)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.gestion,";
		$sql.= " t.fk_poa,";
		$sql.= " t.fk_pac,";
		$sql.= " t.fk_prev_ant,";
		$sql.= " t.fk_prev,";
		$sql.= " t.fk_area,";
		$sql.= " t.code_requirement,";
		$sql.= " t.label,";
		$sql.= " t.pseudonym,";
		$sql.= " t.nro_activity,";
		$sql.= " t.date_activity,";
		$sql.= " t.partida,";
		$sql.= " t.amount,";
		$sql.= " t.priority,";
		$sql.= " t.date_create,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut,";
		$sql.= " t.active";

		$sql.= " FROM ".MAIN_DB_PREFIX."poa_activity as t";
		$sql.= " WHERE t.fk_poa = ".$fk_poa;
		$sql.= " AND t.statut != -1";
		if ($fk_user>0)
			$sql.= " AND t.fk_user_create = ".$fk_user;
		dol_syslog(get_class($this)."::getlist_poa sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->array = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($this->db->num_rows($resql))
			{
				include_once DOL_DOCUMENT_ROOT.'/poa/activity/class/poaactivitydet.class.php';
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objnew = new Poaactivity($this->db);

					$objnew->id    = $obj->rowid;

					$objnew->entity = $obj->entity;
					$objnew->gestion = $obj->gestion;
					$objnew->fk_poa = $obj->fk_poa;
					$objnew->fk_pac = $obj->fk_pac;
					$objnew->fk_prev_ant = $obj->fk_prev_ant;
					$objnew->fk_prev = $obj->fk_prev;
					$objnew->fk_area = $obj->fk_area;
					$objnew->code_requirement = $obj->code_requirement;
					$objnew->label = $obj->label;
					$objnew->pseudonym = $obj->pseudonym;
					$objnew->nro_activity = $obj->nro_activity;
					$objnew->date_activity = $this->db->jdate($obj->date_activity);
					$objnew->partida = $obj->partida;
					$objnew->amount = $obj->amount;
					$objnew->priority = $obj->priority;
					$objnew->date_create = $this->db->jdate($obj->date_create);
					$objnew->fk_user_create = $obj->fk_user_create;
					$objnew->tms = $this->db->jdate($obj->tms);
					$objnew->statut = $obj->statut;
					$objnew->active = $obj->active;
					$objnewdet = new Poaactivitydet($this->db);
					$objnewdet->getlist($obj->rowid);
					if (count($objnewdet->array))
						$objnew->array_options = $objnewdet->array;
					else
						$objnew->array_options = array();
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
			dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param  int     $id    Id object
	 *  @return int             <0 if KO, >0 if OK
	 */
	function getlist_poa_user($fk_poa,$fk_user=0)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.gestion,";
		$sql.= " t.fk_poa,";
		$sql.= " t.fk_pac,";
		$sql.= " t.fk_prev_ant,";
		$sql.= " t.fk_prev,";
		$sql.= " t.fk_area,";
		$sql.= " t.code_requirement,";
		$sql.= " t.label,";
		$sql.= " t.pseudonym,";
		$sql.= " t.nro_activity,";
		$sql.= " t.date_activity,";
		$sql.= " t.partida,";
		$sql.= " t.amount,";
		$sql.= " t.priority,";
		$sql.= " t.date_create,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut,";
		$sql.= " t.active";

		$sql.= " FROM ".MAIN_DB_PREFIX."poa_activity as t";
		$sql.= " WHERE t.fk_poa = ".$fk_poa;
		$sql.= " AND t.statut != -1";
		if ($fk_user>0)
			$sql.= " AND t.fk_user_create = ".$fk_user;
		dol_syslog(get_class($this)."::getlist_poa sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->array = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($this->db->num_rows($resql))
			{
				include_once DOL_DOCUMENT_ROOT.'/poa/activity/class/poaactivitydet.class.php';
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objnew = new Poaactivity($this->db);

					$objnew->id    = $obj->rowid;

					$objnew->entity = $obj->entity;
					$objnew->gestion = $obj->gestion;
					$objnew->fk_poa = $obj->fk_poa;
					$objnew->fk_pac = $obj->fk_pac;
					$objnew->fk_prev_ant = $obj->fk_prev_ant;
					$objnew->fk_prev = $obj->fk_prev;
					$objnew->fk_area = $obj->fk_area;
					$objnew->code_requirement = $obj->code_requirement;
					$objnew->label = $obj->label;
					$objnew->pseudonym = $obj->pseudonym;
					$objnew->nro_activity = $obj->nro_activity;
					$objnew->date_activity = $this->db->jdate($obj->date_activity);
					$objnew->partida = $obj->partida;
					$objnew->amount = $obj->amount;
					$objnew->priority = $obj->priority;
					$objnew->date_create = $this->db->jdate($obj->date_create);
					$objnew->fk_user_create = $obj->fk_user_create;
					$objnew->tms = $this->db->jdate($obj->tms);
					$objnew->statut = $obj->statut;
					$objnew->active = $obj->active;
					$objnewdet = new Poaactivitydet($this->db);
					$objnewdet->getlist($obj->rowid);
					if (count($objnewdet->array))
						$objnew->array_options = $objnewdet->array;
					else
						$objnew->array_options = array();
					$this->array[$fk_user][$obj->rowid] = $objnew;
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
			dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/*
	* sumar y contar las actividades por usuario
	*/
	function resume_activity_user($fk_poa,$fk_user=0)
	{
		global $langs,$conf;
		//agregamos poapartidapre
		include_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidapre.class.php';
		include_once DOL_DOCUMENT_ROOT.'/poa/process/class/poaprocess.class.php';
		$objpp = new Poapartidapre($this->db);
		$objproc = new Poaprocess($this->db);
		$res = $this->getlist_poa_user($fk_poa,$fk_user);
		$this->aPac = array();
		$this->aPacne = array();
		if ($res>0)
		{
			foreach ((array) $this->array AS $userid => $aData)
			{
				foreach ((array) $aData AS $poaid => $obj)
				{
					$this->aCount[$fk_user]++;
					$amount = $obj->amount;
					if ($obj->fk_prev>0)
					{
						//revisamos otros datos
						$aLisprev = prev_ant($obj->fk_prev,$aLisprev,'0,1');
						$data = $aLisprev[$obj->fk_prev];
						//proceso
						if ($data['idprocessant']) $idProcess = $data['idprocessant'];
						else $idProcess = $data['idprocess'];
						//revisamos el proceso
						$objproc->fetch($idProcess);
						$this->aPac[$fk_user][$obj->fk_pac] = dol_getdate($objproc->date_process);
						$amount = $objpp->getsum($obj->fk_prev);
					}
					else
					{
						$this->aPacne[$fk_user][$obj->fk_pac] = $obj->fk_pac;
					}
					$this->aSum[$fk_user]+=$amount;
					if ($obj->statut == 9)
					{
						$this->aCountfin[$fk_user]++;
						$this->aSumfin[$fk_user]+= $amount;
					}
				}
			}
			return $res;
		}
		return $res;
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
	function select_activity($selected='',$htmlname='fk_activity',$htmloption='',$maxlength=0,$showempty=0,$id=0,$required='',$filterarray='',$filter='')
	{
		global $conf,$langs;

		$langs->load("poa@poa");
		if ($required)
			$required = 'required';
		$out='';
		$countryArray=array();
		$label=array();

		$sql = "SELECT c.rowid, c.nro_activity as code_iso, c.label as label";
		$sql.= " FROM ".MAIN_DB_PREFIX."poa_activity AS c ";
		$sql.= " WHERE c.entity = ".$conf->entity;
		if ($id)
			$sql.= " AND c.rowid NOT IN (".$id.")";
		if ($filter)
			$sql.= $filter;
		$sql.= " ORDER BY c.label ASC";

		dol_syslog(get_class($this)."::select_activity sql=".$sql);
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
					$lAdd = true;
					if (!empty($filterarray) && count($filterarray)>0)
					{
						if ($filterarray[$obj->rowid])
							$lAdd = true;
						else
							$lAdd = false;
					}
					if ($lAdd)
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
			if ($status == -1) return ($type==0 ? $langs->trans('Annulled'):img_picto($langs->trans('Anulled'),DOL_URL_ROOT.'/poa/img/anu.png','',true));
			if ($status == 0) return ($type==0 ? $langs->trans('Draft'):img_picto($langs->trans('Pending'),DOL_URL_ROOT.'/poa/img/pen.png','',true));
			if ($status == 1) return ($type==0 ? $langs->trans('Preventive'):img_picto($langs->trans('Preventive'),DOL_URL_ROOT.'/poa/img/pre.png','',true));
			if ($status == 2) return ($type==0 ? $langs->trans('Committed'):img_picto($langs->trans('Committed'),DOL_URL_ROOT.'/poa/img/com.png','',true));
			if ($status == 3) return ($type==0 ? $langs->trans('Accrued'):img_picto($langs->trans('Accrued'),DOL_URL_ROOT.'/poa/img/dev.png','',true));
			if ($status == 4) return ($type==0 ? $langs->trans('Paid'):img_picto($langs->trans('Paid'),DOL_URL_ROOT.'/poa/img/pag.png','',true));
		}
		if ($mode == 1)
		{
			if ($status == -1) return ($type==0 ? $langs->trans('Annulled'):img_picto($langs->trans('Anulled'),DOL_URL_ROOT.'/poa/img/anu.png','',true).' '.$langs->trans('Anulled'));
			if ($status == 0) return ($type==0 ? $langs->trans('Draft'):img_picto($langs->trans('Pending'),DOL_URL_ROOT.'/poa/img/pen.png','',true).' '.$langs->trans('Pending'));
			if ($status == 1) return ($type==0 ? $langs->trans('Preventive'):img_picto($langs->trans('Preventive'),DOL_URL_ROOT.'/poa/img/pre.png','',true).' '.$langs->trans('Preventive'));
			if ($status == 2) return ($type==0 ? $langs->trans('Committed'):img_picto($langs->trans('Committed'),DOL_URL_ROOT.'/poa/img/com.png','',true).' '.$langs->trans('Committed'));
			if ($status == 3) return ($type==0 ? $langs->trans('Accrued'):img_picto($langs->trans('Accrued'),DOL_URL_ROOT.'/poa/img/dev.png','',true).' '.$langs->trans('Accrued'));
			if ($status == 4) return ($type==0 ? $langs->trans('Paid'):img_picto($langs->trans('Paid'),DOL_URL_ROOT.'/poa/img/pag.png','',true).' '.$langs->trans('Paid'));
		}

		if ($mode == 9)
		{
			if ($status == -1) return ($type==0 ? $langs->trans('Annulled'):img_picto($langs->trans('Anulled'),DOL_URL_ROOT.'/poa/img/statenul','',true).' '.$langs->trans('Anulled'));
			if ($status == 0) return ($type==0 ? $langs->trans('Draft'):img_picto($langs->trans('Pending'),DOL_URL_ROOT.'/poa/img/state0.png','',true).' '.$langs->trans('Pending'));
			if ($status == 1) return ($type==0 ? $langs->trans('Preventive'):img_picto($langs->trans('Preventive'),DOL_URL_ROOT.'/poa/img/state1.png','',true).' '.$langs->trans('Preventive'));
			if ($status == 2) return ($type==0 ? $langs->trans('Committed'):img_picto($langs->trans('Committed'),DOL_URL_ROOT.'/poa/img/state2.png','',true).' '.$langs->trans('Committed'));
			if ($status == 3) return ($type==0 ? $langs->trans('Accrued'):img_picto($langs->trans('Accrued'),DOL_URL_ROOT.'/poa/img/state3.png','',true).' '.$langs->trans('Accrued'));
			if ($status == 4) return ($type==0 ? $langs->trans('Paid'):img_picto($langs->trans('Paid'),DOL_URL_ROOT.'/poa/img/state4.png','',true).' '.$langs->trans('Paid'));
		}

		if ($mode == 2)
		{
			if ($status == 0) return img_picto($langs->trans('Notapproved'),'statut5').' '.($type==0 ? $langs->trans('Notapproved'):$langs->trans('Reformulation unapproved'));
			if ($status == 1) return img_picto($langs->trans('Approved'),'statut4').' '.($type==0 ? $langs->trans('Approved'):$langs->trans('Reformulation approved'));
		}

		if ($mode == 3)
		{ //si proceso o no
			if ($status == 1) return img_picto($langs->trans('Not'),'switch_off');

			if ($status == 2) return img_picto($langs->trans('Yes'),'switch_on');
		}
		return $langs->trans('Unknown');
	}

}
?>
