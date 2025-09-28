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
 *  \file       dev/skeletons/poapac.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2014-04-02 13:53
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Poapac extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='poa_pac';			//!< Id that identify managed objects
	var $table_element='poa_pac';		//!< Name of table without prefix where object is stored

	var $id;

	var $entity;
	var $fk_poa;
	var $gestion;
	var $fk_type_modality;
	var $fk_type_object;
	var $ref;
	var $nom;
	var $fk_financer;
	var $month_init;
	var $month_public;
	var $partida;
	var $amount;
	var $fk_user_resp;
	var $responsible;
	var $tms='';
	var $statut;
	var $array;
	var $aCount;
	var $aSum;
	var $aCountfin;
	var $aSumfin;
	var $aPacm;
	var $aPacg;

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
		if (isset($this->fk_poa)) $this->fk_poa=trim($this->fk_poa);
		if (isset($this->gestion)) $this->gestion=trim($this->gestion);
		if (isset($this->fk_type_modality)) $this->fk_type_modality=trim($this->fk_type_modality);
		if (isset($this->fk_type_object)) $this->fk_type_object=trim($this->fk_type_object);
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->nom)) $this->nom=trim($this->nom);
		if (isset($this->fk_financer)) $this->fk_financer=trim($this->fk_financer);
		if (isset($this->month_init)) $this->month_init=trim($this->month_init);
		if (isset($this->month_public)) $this->month_public=trim($this->month_public);
		if (isset($this->partida)) $this->partida=trim($this->partida);
		if (isset($this->amount)) $this->amount=trim($this->amount);
		if (isset($this->fk_user_resp)) $this->fk_user_resp=trim($this->fk_user_resp);
		if (isset($this->responsible)) $this->responsible=trim($this->responsible);
		if (isset($this->statut)) $this->statut=trim($this->statut);



		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."poa_pac(";

		$sql.= "entity,";
		$sql.= "fk_poa,";
		$sql.= "gestion,";
		$sql.= "fk_type_modality,";
		$sql.= "fk_type_object,";
		$sql.= "ref,";
		$sql.= "nom,";
		$sql.= "fk_financer,";
		$sql.= "month_init,";
		$sql.= "month_public,";
		$sql.= "partida,";
		$sql.= "amount,";
		$sql.= "fk_user_resp,";
		$sql.= "responsible,";
		$sql.= "statut";


		$sql.= ") VALUES (";

		$sql.= " ".(! isset($this->entity)?'NULL':"'".$this->entity."'").",";
		$sql.= " ".(! isset($this->fk_poa)?'NULL':"'".$this->fk_poa."'").",";
		$sql.= " ".(! isset($this->gestion)?'NULL':"'".$this->gestion."'").",";
		$sql.= " ".(! isset($this->fk_type_modality)?'NULL':"'".$this->fk_type_modality."'").",";
		$sql.= " ".(! isset($this->fk_type_object)?'NULL':"'".$this->fk_type_object."'").",";
		$sql.= " ".(! isset($this->ref)?'NULL':"'".$this->ref."'").",";
		$sql.= " ".(! isset($this->nom)?'NULL':"'".$this->db->escape($this->nom)."'").",";
		$sql.= " ".(! isset($this->fk_financer)?'NULL':"'".$this->fk_financer."'").",";
		$sql.= " ".(! isset($this->month_init)?'NULL':"'".$this->month_init."'").",";
		$sql.= " ".(! isset($this->month_public)?'NULL':"'".$this->month_public."'").",";
		$sql.= " ".(! isset($this->partida)?'NULL':"'".$this->db->escape($this->partida)."'").",";
		$sql.= " ".(! isset($this->amount)?'NULL':"'".$this->amount."'").",";
		$sql.= " ".(! isset($this->fk_user_resp)?'NULL':"'".$this->fk_user_resp."'").",";
		$sql.= " ".(! isset($this->responsible)?'NULL':"'".$this->db->escape($this->responsible)."'").",";
		$sql.= " ".(! isset($this->statut)?'NULL':"'".$this->statut."'")."";


		$sql.= ")";

		$this->db->begin();

		dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
		{
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."poa_pac");

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
		$sql.= " t.fk_poa,";
		$sql.= " t.gestion,";
		$sql.= " t.fk_type_modality,";
		$sql.= " t.fk_type_object,";
		$sql.= " t.ref,";
		$sql.= " t.nom,";
		$sql.= " t.fk_financer,";
		$sql.= " t.month_init,";
		$sql.= " t.month_public,";
		$sql.= " t.partida,";
		$sql.= " t.amount,";
		$sql.= " t.fk_user_resp,";
		$sql.= " t.responsible,";
		$sql.= " t.tms,";
		$sql.= " t.statut";


		$sql.= " FROM ".MAIN_DB_PREFIX."poa_pac as t";
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
				$this->fk_poa = $obj->fk_poa;
				$this->gestion = $obj->gestion;
				$this->fk_type_modality = $obj->fk_type_modality;
				$this->fk_type_object = $obj->fk_type_object;
				$this->ref = $obj->ref;
				$this->nom = $obj->nom;
				$this->fk_financer = $obj->fk_financer;
				$this->month_init = $obj->month_init;
				$this->month_public = $obj->month_public;
				$this->partida = $obj->partida;
				$this->amount = $obj->amount;
				$this->fk_user_resp = $obj->fk_user_resp;
				$this->responsible = $obj->responsible;
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
		if (isset($this->fk_poa)) $this->fk_poa=trim($this->fk_poa);
		if (isset($this->gestion)) $this->gestion=trim($this->gestion);
		if (isset($this->fk_type_modality)) $this->fk_type_modality=trim($this->fk_type_modality);
		if (isset($this->fk_type_object)) $this->fk_type_object=trim($this->fk_type_object);
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->nom)) $this->nom=trim($this->nom);
		if (isset($this->fk_financer)) $this->fk_financer=trim($this->fk_financer);
		if (isset($this->month_init)) $this->month_init=trim($this->month_init);
		if (isset($this->month_public)) $this->month_public=trim($this->month_public);
		if (isset($this->partida)) $this->partida=trim($this->partida);
		if (isset($this->amount)) $this->amount=trim($this->amount);
		if (isset($this->fk_user_resp)) $this->fk_user_resp=trim($this->fk_user_resp);
		if (isset($this->responsible)) $this->responsible=trim($this->responsible);
		if (isset($this->statut)) $this->statut=trim($this->statut);



		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = "UPDATE ".MAIN_DB_PREFIX."poa_pac SET";

		$sql.= " entity=".(isset($this->entity)?$this->entity:"null").",";
		$sql.= " fk_poa=".(isset($this->fk_poa)?$this->fk_poa:"null").",";
		$sql.= " gestion=".(isset($this->gestion)?$this->gestion:"null").",";
		$sql.= " fk_type_modality=".(isset($this->fk_type_modality)?$this->fk_type_modality:"null").",";
		$sql.= " fk_type_object=".(isset($this->fk_type_object)?$this->fk_type_object:"null").",";
		$sql.= " ref=".(isset($this->ref)?$this->ref:"null").",";
		$sql.= " nom=".(isset($this->nom)?"'".$this->db->escape($this->nom)."'":"null").",";
		$sql.= " fk_financer=".(isset($this->fk_financer)?$this->fk_financer:"null").",";
		$sql.= " month_init=".(isset($this->month_init)?$this->month_init:"null").",";
		$sql.= " month_public=".(isset($this->month_public)?$this->month_public:"null").",";
		$sql.= " partida=".(isset($this->partida)?"'".$this->db->escape($this->partida)."'":"null").",";
		$sql.= " amount=".(isset($this->amount)?$this->amount:"null").",";
		$sql.= " fk_user_resp=".(isset($this->fk_user_resp)?$this->fk_user_resp:"null").",";
		$sql.= " responsible=".(isset($this->responsible)?"'".$this->db->escape($this->responsible)."'":"null").",";
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
			$sql = "DELETE FROM ".MAIN_DB_PREFIX."poa_pac";
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

		$object=new Poapac($this->db);

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
		$this->fk_poa='';
		$this->gestion='';
		$this->fk_type_modality='';
		$this->fk_type_object='';
		$this->ref='';
		$this->nom='';
		$this->fk_financer='';
		$this->month_init='';
		$this->month_public='';
		$this->partida='';
		$this->amount='';
		$this->fk_user_resp='';
		$this->responsible='';
		$this->tms='';
		$this->statut='';


	}

	//modificado
	/**
	 *  Return combo list of activated countries, into language of user
	 *
	 *  @param	string	$selected       Id or Code or Label of preselected country
	 *  @param  string	$htmlname       Name of html select object
	 *  @param  string	$htmloption     Options html on select object
	 *  @param	string	$maxlength	Max length for labels (0=no limit)
	 *  @param	string	$showempty	View space labels (0=no view)

	 *  @return string           		HTML string with select
	 */
	function select_pac($selected='',$htmlname='fk_pac',$htmloption='',$maxlength=0,$showempty=0,$gestion='',$userresp='')
	{
		global $conf,$langs;
		if (empty($gestion)) $gestion = date('Y');
		$filter = '';
		if ($userresp)
			$filter = " AND c.fk_user_resp = ".$userresp;
		$langs->load("poa@poa");

		$out='';
		$countryArray=array();
		$label=array();

		$sql = "SELECT c.rowid, c.nom as label, c.rowid AS code_iso ";
		$sql.= " FROM ".MAIN_DB_PREFIX."poa_pac AS c ";

		$sql.= " WHERE c.entity = ".$conf->entity;
		$sql.= " AND c.gestion = ".$gestion;
		$sql.= $filter;
		$sql.= " AND c.statut = 1 ";
		$sql.= " ORDER BY c.nom ASC";

		dol_syslog(get_class($this)."::select_pac sql=".$sql);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$out.= '<select id="select'.$htmlname.'" class="flat selectpays" name="'.$htmlname.'" '.$htmloption.'>';
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
					$countryArray[$i]['rowid'] 		= $obj->rowid;
					$countryArray[$i]['code_iso'] 	= $obj->code_iso;

					$countryArray[$i]['label']		= ($obj->code_iso && $langs->transnoentitiesnoconv("Pac".$obj->code_iso)!="Pac".$obj->code_iso?$langs->transnoentitiesnoconv("Pac".$obj->code_iso):($obj->label!='-'?$obj->label:''));
					$label[$i] 	= $countryArray[$i]['label'];
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
			if ($status == 0) return img_picto($langs->trans('Notapproved'),'statut5').' '.($type==0 ? $langs->trans('Notapproved'):$langs->trans('Reformulation unapproved'));
			if ($status == 1) return img_picto($langs->trans('Approved'),'statut4').' '.($type==0 ? $langs->trans('Approved'):$langs->trans('Reformulation approved'));
			if ($status == 2) return img_picto($langs->trans('Canceled'),'statut8').' '.($type==0 ? $langs->trans('Canceled'):$langs->trans('Canceled'));
		}

		if ($mode == 2)
		{
			if ($status == 0) return img_picto($langs->trans('Notapproved'),'statut5').' '.($type==0 ? $langs->trans('Notapproved'):$langs->trans('Reformulation unapproved'));
			if ($status == 1) return img_picto($langs->trans('Approved'),'statut4').' '.($type==0 ? $langs->trans('Approved'):$langs->trans('Reformulation approved'));
			if ($status == 2) return img_picto($langs->trans('Canceled'),'statut8').' '.($type==0 ? $langs->trans('Canceled'):$langs->trans('Canceled'));
		}

		return $langs->trans('Unknown');
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$fk_poa    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function fetch_poa($fk_poa=0,$fk_user=0,$gestion=0)
	{
		global $langs, $conf;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.fk_poa,";
		$sql.= " t.gestion,";
		$sql.= " t.fk_type_modality,";
		$sql.= " t.fk_type_object,";
		$sql.= " t.ref,";
		$sql.= " t.nom,";
		$sql.= " t.fk_financer,";
		$sql.= " t.month_init,";
		$sql.= " t.month_public,";
		$sql.= " t.partida,";
		$sql.= " t.amount,";
		$sql.= " t.fk_user_resp,";
		$sql.= " t.responsible,";
		$sql.= " t.tms,";
		$sql.= " t.statut";


		$sql.= " FROM ".MAIN_DB_PREFIX."poa_pac as t";
		$sql.= " WHERE t.entity = ".$conf->entity;
		if ($fk_poa>0) $sql.= " AND t.fk_poa = ".$fk_poa;
		if ($fk_user>0) $sql.= " AND t.fk_user_resp = ".$fk_user;
		if ($gestion>0) $sql.= " AND t.gestion = ".$gestion;
		$sql.= " AND t.statut = 1";
		$sql.= " ORDER BY t.month_init ASC";
		dol_syslog(get_class($this)."::fetch_poa sql=".$sql, LOG_DEBUG);
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
					$objnew = new Poapac($this->db);

					$objnew->id    = $obj->rowid;
					$objnew->entity = $obj->entity;
					$objnew->fk_poa = $obj->fk_poa;
					$objnew->gestion = $obj->gestion;
					$objnew->fk_type_modality = $obj->fk_type_modality;
					$objnew->fk_type_object = $obj->fk_type_object;
					$objnew->ref = $obj->ref;
					$objnew->nom = $obj->nom;
					$objnew->fk_financer = $obj->fk_financer;
					$objnew->month_init = $obj->month_init;
					$objnew->month_public = $obj->month_public;
					$objnew->partida = $obj->partida;
					$objnew->amount = $obj->amount;
					$objnew->fk_user_resp = $obj->fk_user_resp;
					$objnew->responsible = $obj->responsible;
					$objnew->tms = $this->db->jdate($obj->tms);
					$objnew->statut = $obj->statut;

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
			dol_syslog(get_class($this)."::fetch_poa ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/*
	* //resumen de pac por usuario
	*/
	function resume_pac_user($fk_poa=0,$fk_user=0,$gestion=0)
	{
		global $langs,$conf;
		$this->aCount = array();
		$this->aSum = array();
		$this->aPacm = array();
		$this->aPacg = array();
		$res = $this->fetch_poa($fk_poa,$fk_user,$gestion);

		if ($res>0)
		{
			foreach ((array) $this->array AS $i => $obj)
			{
				$this->aCount[$fk_user]++;
				$amount = $obj->amount;
				$this->aSum[$fk_user]+=$amount;
				//recuperamos que mes tiene
				$this->aPacm[$fk_user][$obj->id] = $obj->month_init;
				$this->aPacg[$fk_user][$obj->id] = $obj->gestion;
			}
			return count($this->array);
		}
		return $res;
	}
}
?>
