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
 *  \file       dev/skeletons/poapartidapre.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2014-04-10 10:21
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Poapartidapre extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='poa_partida_pre';			//!< Id that identify managed objects
	var $table_element='poa_partida_pre';		//!< Name of table without prefix where object is stored

	var $id;

	var $fk_poa_prev;
	var $fk_structure;
	var $fk_poa;
	var $partida;
	var $amount;
	var $tms='';
	var $statut;
	var $active;
	var $total;
	var $array;
	var $arraysum;
	var $aCount;
	var $aCountfin;
	var $aSum;
	var $aSumfin;

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

		if (isset($this->fk_poa_prev)) $this->fk_poa_prev=trim($this->fk_poa_prev);
		if (isset($this->fk_structure)) $this->fk_structure=trim($this->fk_structure);
		if (isset($this->fk_poa)) $this->fk_poa=trim($this->fk_poa);
		if (isset($this->partida)) $this->partida=trim($this->partida);
		if (isset($this->amount)) $this->amount=trim($this->amount);
		if (isset($this->statut)) $this->statut=trim($this->statut);
		if (isset($this->active)) $this->active=trim($this->active);



		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."poa_partida_pre(";

		$sql.= "fk_poa_prev,";
		$sql.= "fk_structure,";
		$sql.= "fk_poa,";
		$sql.= "partida,";
		$sql.= "amount,";
		$sql.= "statut,";
		$sql.= "active";


		$sql.= ") VALUES (";

		$sql.= " ".(! isset($this->fk_poa_prev)?'NULL':"'".$this->fk_poa_prev."'").",";
		$sql.= " ".(! isset($this->fk_structure)?'NULL':"'".$this->fk_structure."'").",";
		$sql.= " ".(! isset($this->fk_poa)?'NULL':"'".$this->fk_poa."'").",";
		$sql.= " ".(! isset($this->partida)?'NULL':"'".$this->db->escape($this->partida)."'").",";
		$sql.= " ".(! isset($this->amount)?'NULL':"'".$this->amount."'").",";
		$sql.= " ".(! isset($this->statut)?'NULL':"'".$this->statut."'").",";
		$sql.= " ".(! isset($this->active)?'NULL':"'".$this->active."'")."";


		$sql.= ")";

		$this->db->begin();

		dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
		{
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."poa_partida_pre");

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

		$sql.= " t.fk_poa_prev,";
		$sql.= " t.fk_structure,";
		$sql.= " t.fk_poa,";
		$sql.= " t.partida,";
		$sql.= " t.amount,";
		$sql.= " t.tms,";
		$sql.= " t.statut,";
		$sql.= " t.active";


		$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_pre as t";
		$sql.= " WHERE t.rowid = ".$id;

		dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);

				$this->id    = $obj->rowid;

				$this->fk_poa_prev = $obj->fk_poa_prev;
				$this->fk_structure = $obj->fk_structure;
				$this->fk_poa = $obj->fk_poa;
				$this->partida = $obj->partida;
				$this->amount = $obj->amount;
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

		if (isset($this->fk_poa_prev)) $this->fk_poa_prev=trim($this->fk_poa_prev);
		if (isset($this->fk_structure)) $this->fk_structure=trim($this->fk_structure);
		if (isset($this->fk_poa)) $this->fk_poa=trim($this->fk_poa);
		if (isset($this->partida)) $this->partida=trim($this->partida);
		if (isset($this->amount)) $this->amount=trim($this->amount);
		if (isset($this->statut)) $this->statut=trim($this->statut);
		if (isset($this->active)) $this->active=trim($this->active);



		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = "UPDATE ".MAIN_DB_PREFIX."poa_partida_pre SET";

		$sql.= " fk_poa_prev=".(isset($this->fk_poa_prev)?$this->fk_poa_prev:"null").",";
		$sql.= " fk_structure=".(isset($this->fk_structure)?$this->fk_structure:"null").",";
		$sql.= " fk_poa=".(isset($this->fk_poa)?$this->fk_poa:"null").",";
		$sql.= " partida=".(isset($this->partida)?"'".$this->db->escape($this->partida)."'":"null").",";
		$sql.= " amount=".(isset($this->amount)?$this->amount:"null").",";
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
			$sql = "DELETE FROM ".MAIN_DB_PREFIX."poa_partida_pre";
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

		$object=new Poapartidapre($this->db);

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

		$this->fk_poa_prev='';
		$this->fk_structure='';
		$this->fk_poa='';
		$this->partida='';
		$this->amount='';
		$this->tms='';
		$this->statut='';
		$this->active='';


	}

	//modificado
	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getlist($fk_poa_prev, $lvalor='S')
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.fk_poa_prev,";
		$sql.= " t.fk_structure,";
		$sql.= " t.fk_poa,";
		$sql.= " t.partida,";
		$sql.= " t.amount,";
		$sql.= " t.tms,";
		$sql.= " t.statut,";
		$sql.= " t.active";


		$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_pre as t";
		$sql.= " WHERE t.fk_poa_prev = ".$fk_poa_prev;
		$sql.= " AND t.statut = 1 ";
		//lvalor == S   para listar los valores positivos
		//lvalor == N   para listar los valores negativos (modificaciones al preventivo)
		if ($lvalor == 'S')
			$sql.= " AND t.amount >= 0";
		else
			$sql.= " AND t.amount < 0";

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
					$objnew = new Poapartidapre($this->db);

					$objnew->id    = $obj->rowid;

					$objnew->fk_poa_prev = $obj->fk_poa_prev;
					$objnew->fk_structure = $obj->fk_structure;
					$objnew->fk_poa = $obj->fk_poa;
					$objnew->partida = $obj->partida;
					$objnew->amount = $obj->amount;
					$objnew->tms = $this->db->jdate($obj->tms);
					$objnew->statut = $obj->statut;
					$objnew->active = $obj->active;
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
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 * $statut      int             1  mayor a 0; 0 mayor o igual a 0
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getlist_poa($fk_poa,$statut=1)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.fk_poa_prev,";
		$sql.= " t.fk_structure,";
		$sql.= " t.fk_poa,";
		$sql.= " t.partida,";
		$sql.= " t.amount,";
		$sql.= " t.tms,";
		$sql.= " t.statut,";
		$sql.= " t.active,";
		$sql.= " p.rowid AS previd,";
		$sql.= " p.fk_pac,";
		$sql.= " p.nro_preventive,";
		$sql.= " p.label,";
		$sql.= " p.fk_user_create";

		$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_pre as t";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_prev AS p ON t.fk_poa_prev = p.rowid";
		$sql.= " WHERE t.fk_poa = ".$fk_poa;
		if ($statut == 1)
			$sql.= " AND p.statut > 0 ";
		if ($statut == 0)
			$sql.= " AND p.statut >= 0 ";

		dol_syslog(get_class($this)."::getlist_poa sql=".$sql, LOG_DEBUG);
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
					$objnew = new Poapartidapre($this->db);

					$objnew->id    = $obj->rowid;

					$objnew->fk_poa_prev = $obj->fk_poa_prev;
					$objnew->fk_structure = $obj->fk_structure;
					$objnew->fk_poa = $obj->fk_poa;
					$objnew->partida = $obj->partida;
					$objnew->amount = $obj->amount;
					$objnew->tms = $this->db->jdate($obj->tms);
					$objnew->statut = $obj->statut;
					$objnew->active = $obj->active;
					$objnew->fk_user_create = $obj->fk_user_create;
					$objnew->label = $obj->label;
					$objnew->nro_preventive = $obj->nro_preventive;
					$objnew->previd = $obj->previd;
					$objnew->fk_pac = $obj->fk_pac;
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
			dol_syslog(get_class($this)."::getlist_poa ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getsum($fk_poa_prev)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.fk_poa_prev,";
		$sql.= " SUM(t.amount) AS total ";

		$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_pre as t";
		$sql.= " WHERE t.fk_poa_prev = ".$fk_poa_prev;
		$sql.= " AND t.statut = 1";
		$sql.= " GROUP BY t.fk_poa_prev ";
		dol_syslog(get_class($this)."::getsum sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$obj = $this->db->fetch_object($resql);
				return $obj->total;
			}
			$this->db->free($resql);
			return 0;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getsum ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getsumpartida($fk_poa_prev)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.fk_structure,";
		$sql.= " t.fk_poa,";
		$sql.= " t.partida,";
		$sql.= " SUM(t.amount) AS total ";

		$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_pre as t";
		$sql.= " WHERE t.fk_poa_prev = ".$fk_poa_prev;
		$sql.= " AND t.statut = 1";
		$sql.= " GROUP BY t.fk_structure, t.fk_poa, t.partida ";
		dol_syslog(get_class($this)."::getsumpartida sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->arraysum = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i  = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$this->arraysum[$i]['fk_structure'] = $obj->fk_structure;
					$this->arraysum[$i]['fk_poa'] = $obj->fk_poa;
					$this->arraysum[$i]['partida'] = $obj->partida;
					$this->arraysum[$i]['amount'] += $obj->total;
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
			dol_syslog(get_class($this)."::getsumpartida ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getsum_str_part($gestion,$fk_structure,$fk_poa,$partida)
	{
		global $langs,$conf;
		$sql = "SELECT";
		$sql.= " r.gestion, t.fk_structure, t.partida,";
		$sql.= " SUM(t.amount) AS total ";

		$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_pre as t";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_prev AS r ON t.fk_poa_prev = r.rowid ";
		$sql.= " WHERE r.gestion = ".$gestion;
		$sql.= " AND t.fk_structure = ".$fk_structure;
		$sql.= " AND t.fk_poa = ".$fk_poa;
		$sql.= " AND t.partida = '".$partida."' ";
		$sql.= " AND t.statut = 1";
		$sql.= " AND r.statut > 0";
		$sql.= " AND r.entity = ".$conf->entity;
		$sql.= " GROUP BY r.gestion, t.fk_structure, t.partida ";
		dol_syslog(get_class($this)."::getsum_str_part sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->total = 0;
		if ($resql)
		{
			$this->total = 0;
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$obj = $this->db->fetch_object($resql);
				$this->total = $obj->total;
			}
			$this->db->free($resql);
			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getsum_str_part ".$this->error, LOG_ERR);
			return -1;
		}
	}

		/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
		function getsum_str_part_prev($fk_prev,$gestion,$fk_structure,$fk_poa,$partida)
		{
			global $langs;
			$this->total = 0;
	//obtenemos los hijos
			$total = 0;
			$totalpadre = 0;
	//echo '<hr>fk_prev_father '.$fk_prev.' ges '.$gestion.' str '.$fk_structure.' poa '.$fk_poa.' part '.$partida;

	//buscamos y recuperamos si tiene hijos
			$total = $this->getsum_son($fk_prev,$gestion,$fk_structure,$fk_poa,$partida);
			$totalpadre = $this->getsum_strpartprev($fk_prev,$gestion,$fk_structure,$fk_poa,$partida);
			$this->total = $total + $totalpadre;
			return 1;
		}

		/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
		function getsum_strpartprev($fk_prev,$gestion,$fk_structure,$fk_poa,$partida)
		{
			global $langs;
			$sql = "SELECT";
			$sql.= " r.gestion, t.fk_structure, t.partida,";
			$sql.= " SUM(t.amount) AS total ";

			$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_pre as t";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_prev AS r ON t.fk_poa_prev = r.rowid ";
			$sql.= " WHERE r.gestion = ".$gestion;
			$sql.= " AND t.fk_poa = ".$fk_poa;
			$sql.= " AND t.partida = '".$partida."' ";
			$sql.= " AND t.fk_poa_prev = ".$fk_prev;
			$sql.= " AND t.statut = 1";
			$sql.= " AND r.statut > 0";
			$sql.= " GROUP BY r.gestion, t.fk_structure, t.partida ";
			dol_syslog(get_class($this)."::getsum_strpartprev sql=".$sql, LOG_DEBUG);
			$resql=$this->db->query($sql);
			$total = 0;
			if ($resql)
			{
				$num = $this->db->num_rows($resql);
				if ($num)
				{
					$obj = $this->db->fetch_object($resql);
					$total = $obj->total;

				}
				$this->db->free($resql);
				return $total;
			}
			else
			{
				$this->error="Error ".$this->db->lasterror();
				dol_syslog(get_class($this)."::getsum_strpartprev ".$this->error, LOG_ERR);
				return 0;
			}
		}


		/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
		function getsum_pac_str_part($gestion,$fk_pac,$fk_structure,$fk_poa,$partida)
		{
			global $langs;
			$sql = "SELECT";
			$sql.= " r.gestion, t.fk_structure, t.partida,";
			$sql.= " SUM(t.amount) AS total ";

			$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_pre as t";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_prev AS r ON t.fk_poa_prev = r.rowid ";
			$sql.= " WHERE r.gestion = ".$gestion;
			$sql.= " AND r.fk_pac = ".$fk_pac;
			$sql.= " AND t.fk_poa = ".$fk_poa;
			$sql.= " AND t.partida = '".$partida."' ";
			$sql.= " AND t.statut = 1";
			$sql.= " AND r.statut > 0";
			$sql.= " GROUP BY r.gestion, t.fk_structure, t.partida ";
			dol_syslog(get_class($this)."::getsum_str_part sql=".$sql, LOG_DEBUG);
			$resql=$this->db->query($sql);
			if ($resql)
			{
				$this->total = 0;
				$num = $this->db->num_rows($resql);
				if ($num)
				{
					$obj = $this->db->fetch_object($resql);
					$this->total = $obj->total;
				}
				$this->db->free($resql);
				return 1;
			}
			else
			{
				$this->error="Error ".$this->db->lasterror();
				dol_syslog(get_class($this)."::getsum_str_part ".$this->error, LOG_ERR);
				return -1;
			}
		}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getsum_str_part_det($gestion,$fk_structure,$fk_poa,$id,$fk_contrat,$partida)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " r.gestion, t.fk_structure, t.partida,";
		$sql.= " td.fk_contrat, ";
		$sql.= " SUM(td.amount) AS total ";

		$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_pre_det as td";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_partida_pre AS t ON td.fk_poa_partida_pre = t.rowid ";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_prev AS r ON t.fk_poa_prev = r.rowid ";
		$sql.= " WHERE r.gestion = ".$gestion;
		$sql.= " AND t.fk_poa = ".$fk_poa;
		$sql.= " AND t.partida = '".$partida."' ";
		$sql.= " AND t.rowid = ".$id;
		$sql.= " AND td.fk_contrat = ".$fk_contrat;
		$sql.= " AND t.statut = 1";
		$sql.= " GROUP BY r.gestion, t.fk_structure, t.partida, td.fk_contrat ";
	//echo '<hr>getsum_str_part_det <br>'.$sql;
		dol_syslog(get_class($this)."::getsum_str_part_det sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->total = 0;
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$obj = $this->db->fetch_object($resql);
				$this->total = $obj->total;
			}
			$this->db->free($resql);
			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getsum_str_part_det ".$this->error, LOG_ERR);
			return -1;
		}
	}

		/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
		function getsum_str_part_det2($gestion,$fk_structure,$fk_poa,$id,$fk_contrat,$partida)
		{
			global $langs;
			$sql = "SELECT";
			$sql.= " r.gestion, t.fk_structure, t.partida,";
			$sql.= " td.fk_contrato, ";
			$sql.= " SUM(td.amount) AS total ";

			$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_pre_det as td";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_partida_pre AS t ON td.fk_poa_partida_pre = t.rowid ";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_prev AS r ON t.fk_poa_prev = r.rowid ";
			$sql.= " WHERE r.gestion = ".$gestion;
			$sql.= " AND t.fk_poa = ".$fk_poa;
			$sql.= " AND t.partida = '".$partida."' ";
			$sql.= " AND t.rowid = ".$id;
			$sql.= " AND td.fk_contrato = ".$fk_contrat;
			$sql.= " AND t.statut = 1";
			$sql.= " GROUP BY r.gestion, t.fk_structure, t.partida, td.fk_contrato ";
			echo '<hr>getsum_str_part_det <br>'.$sql;
	//exit;
			dol_syslog(get_class($this)."::getsum_str_part_det sql=".$sql, LOG_DEBUG);
			$resql=$this->db->query($sql);
			$this->total = 0;
			if ($resql)
			{
				$num = $this->db->num_rows($resql);
				if ($num)
				{
					$obj = $this->db->fetch_object($resql);
					$this->total = $obj->total;
				}
				$this->db->free($resql);
				return 1;
			}
			else
			{
				$this->error="Error ".$this->db->lasterror();
				dol_syslog(get_class($this)."::getsum_str_part_det ".$this->error, LOG_ERR);
				return -1;
			}
		}


	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */

	//modificado
	function getlist_user($gestion,$fk_user=0,$fk_area=0,$userpoa=0)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.fk_poa_prev,";
		$sql.= " t.fk_structure,";
		$sql.= " t.fk_poa,";
		$sql.= " t.partida,";
		$sql.= " t.amount,";
		$sql.= " t.tms,";
		$sql.= " t.statut,";
		$sql.= " t.active,";
		$sql.= " p.fk_user_create,";
		$sql.= " p.date_preventive";
		$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_pre as t";
	  //modificado
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_prev AS p ON t.fk_poa_prev = p.rowid";
		if ($userpoa)
		{
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_poa AS po ON t.fk_poa = po.rowid";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_poa_user AS pu ON t.fk_poa = pu.fk_poa_poa";
		}
		$sql.= " WHERE p.gestion = ".$gestion;
		if ($userpoa)
		{
			if ($fk_user)
				$sql.= " AND pu.fk_user = ".$fk_user;
			if ($fk_area)
				$sql.= " AND po.fk_area = ".$fk_area;
			$sql.= " AND pu.active = 1";
			$sql.= " AND pu.statut = 1";
		}
		else
		{
			if ($fk_user)
				$sql.= " AND p.fk_user_create = ".$fk_user;
			if ($fk_area)
				$sql.= " AND p.fk_area = ".$fk_area;
		}
		$sql.= " AND p.statut > 0 ";
		$sql.= " AND t.statut > 0 ";
		dol_syslog(get_class($this)."::getlist_user sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->array = array();

		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i = 0;
		  //modificado
		  //include_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidapredet.class.php';

				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objnew = new Poapartidapre($this->db);
		  //modificado
		  //$objdet = new Poapartidapredet($this->db);
		  //$total = $objdet->getsum($obj->rowid);
					$objnew->id    = $obj->rowid;

					$objnew->fk_poa_prev = $obj->fk_poa_prev;
					$objnew->fk_structure = $obj->fk_structure;
					$objnew->fk_poa = $obj->fk_poa;
					$objnew->partida = $obj->partida;
		  //modificado
		  //$objnew->amount = $total;
					$objnew->amount = $obj->amount;
					$objnew->tms = $this->db->jdate($obj->tms);
					$objnew->statut = $obj->statut;
					$objnew->active = $obj->active;
					$objnew->fk_user_create = $obj->fk_user_create;
					$objnew->date_preventive = $obj->date_preventive;
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
			dol_syslog(get_class($this)."::getlist_user ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/*
	* sumar y contar los preventivos por usuario
	*/
	function resume_prev_user($fk_poa,$gestion,$fk_user=0)
	{
		global $langs,$conf;
		$res = $this->getlist_user($gestion,$fk_user);
		if ($res>0)
		{
			$aCount = array();
			$aCountfin = array();
			foreach ((array) $this->array AS $i => $obj)
			{
				if ($obj->fk_poa == $fk_poa)
				{
					$aCount[$fk_user][$obj->nro_preventive]=$obj->nro_preventive;
					$this->aSum[$fk_user]+=$obj->amount;
					if ($obj->statut == 9)
					{
						$this->aCountfin[$fk_user]++;
						$this->aSumfin[$fk_user]+= $obj->amount;
					}
				}
			}
			//armamos el resumen de conteo
			return $res;
		}
		return $res;
	}


	//funcion que suma los preventivos hijos
	function getsum_son($fk_prev,$gestion,$fk_structure,$fk_poa,$partida)
	{
		global $db,$conf;
		$total = 0;
		if ($fk_prev > 0)
		{
			include_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poaprev.class.php';
			$objp = new Poaprev($db);
			$objp->getlistfather($fk_prev);
			foreach ((array) $objp->arrayf AS $i => $objd)
			{
		  		//obtenemos la suma de cada preventivo
				$total += $this->getsum_strpartprev($objd->id,$gestion,$fk_structure,$fk_poa,$partida);
			}
		}
	  	//echo '<br>total_hijo '.$total;
		return $total;
	}
}
?>
