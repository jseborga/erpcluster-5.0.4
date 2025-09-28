<?php
/* Copyright (C) 2007-2012  Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) 2014-2016  Juanjo Menent       <jmenent@2byte.es>
 * Copyright (C) 2015       Florian Henry       <florian.henry@open-concept.pro>
 * Copyright (C) 2015       Raphaël Doursenaud  <rdoursenaud@gpcsolutions.fr>
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
 * \file    mant/mworkrequest.class.php
 * \ingroup mant
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Mworkrequest
 *
 * Put here description of your class
 *
 * @see CommonObject
 */
class Mworkrequest extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'mworkrequest';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'm_work_request';

	/**
	 * @var MworkrequestLine[] Lines
	 */
	public $lines = array();

	/**
	 */

	public $entity;
	public $ref;
	public $date_create = '';
	public $fk_member;
	public $fk_departament;
	public $fk_equipment;
	public $fk_property;
	public $fk_location;
	public $fk_soc;
	public $fk_type_repair;
	public $email;
	public $internal;
	public $detail_problem;
	public $address_ip;
	public $fk_user_assign;
	public $date_assign = '';
	public $speciality;
	public $tokenreg;
	public $description_prog;
	public $date_ini_prog = '';
	public $date_fin_prog = '';
	public $speciality_prog;
	public $fk_equipment_prog;
	public $fk_property_prog;
	public $fk_location_prog;
	public $typemant_prog;
	public $fk_user_prog;
	public $image_ini;
	public $fk_user_create;
	public $fk_user_mod;
	public $datec = '';
	public $datem = '';
	public $tms = '';
	public $status;
	public $description_confirm;
	public $statut_job;

	/**
	 */


	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $db)
	{
		$this->db = $db;
	}

	/**
	 * Create object into database
	 *
	 * @param  User $user      User that creates
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 *
	 * @return int <0 if KO, Id of created object if OK
	 */
	public function create(User $user, $notrigger = false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$error = 0;

		// Clean parameters

		if (isset($this->entity)) {
			$this->entity = trim($this->entity);
		}
		if (isset($this->ref)) {
			$this->ref = trim($this->ref);
		}
		if (isset($this->fk_member)) {
			$this->fk_member = trim($this->fk_member);
		}
		if (isset($this->fk_departament)) {
			$this->fk_departament = trim($this->fk_departament);
		}
		if (isset($this->fk_equipment)) {
			$this->fk_equipment = trim($this->fk_equipment);
		}
		if (isset($this->fk_property)) {
			$this->fk_property = trim($this->fk_property);
		}
		if (isset($this->fk_location)) {
			$this->fk_location = trim($this->fk_location);
		}
		if (isset($this->fk_soc)) {
			$this->fk_soc = trim($this->fk_soc);
		}
		if (isset($this->fk_type_repair)) {
			$this->fk_type_repair = trim($this->fk_type_repair);
		}
		if (isset($this->email)) {
			$this->email = trim($this->email);
		}
		if (isset($this->internal)) {
			$this->internal = trim($this->internal);
		}
		if (isset($this->detail_problem)) {
			$this->detail_problem = trim($this->detail_problem);
		}
		if (isset($this->address_ip)) {
			$this->address_ip = trim($this->address_ip);
		}
		if (isset($this->fk_user_assign)) {
			$this->fk_user_assign = trim($this->fk_user_assign);
		}
		if (isset($this->speciality)) {
			$this->speciality = trim($this->speciality);
		}
		if (isset($this->tokenreg)) {
			$this->tokenreg = trim($this->tokenreg);
		}
		if (isset($this->description_prog)) {
			$this->description_prog = trim($this->description_prog);
		}
		if (isset($this->speciality_prog)) {
			$this->speciality_prog = trim($this->speciality_prog);
		}
		if (isset($this->fk_equipment_prog)) {
			$this->fk_equipment_prog = trim($this->fk_equipment_prog);
		}
		if (isset($this->fk_property_prog)) {
			$this->fk_property_prog = trim($this->fk_property_prog);
		}
		if (isset($this->fk_location_prog)) {
			$this->fk_location_prog = trim($this->fk_location_prog);
		}
		if (isset($this->typemant_prog)) {
			$this->typemant_prog = trim($this->typemant_prog);
		}
		if (isset($this->fk_user_prog)) {
			$this->fk_user_prog = trim($this->fk_user_prog);
		}
		if (isset($this->image_ini)) {
			$this->image_ini = trim($this->image_ini);
		}
		if (isset($this->fk_user_create)) {
			$this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_mod)) {
			$this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->status)) {
			$this->status = trim($this->status);
		}
		if (isset($this->description_confirm)) {
			$this->description_confirm = trim($this->description_confirm);
		}
		if (isset($this->statut_job)) {
			$this->statut_job = trim($this->statut_job);
		}



		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';

		$sql.= 'entity,';
		$sql.= 'ref,';
		$sql.= 'date_create,';
		$sql.= 'fk_member,';
		$sql.= 'fk_departament,';
		$sql.= 'fk_equipment,';
		$sql.= 'fk_property,';
		$sql.= 'fk_location,';
		$sql.= 'fk_soc,';
		$sql.= 'fk_type_repair,';
		$sql.= 'email,';
		$sql.= 'internal,';
		$sql.= 'detail_problem,';
		$sql.= 'address_ip,';
		$sql.= 'fk_user_assign,';
		$sql.= 'date_assign,';
		$sql.= 'speciality,';
		$sql.= 'tokenreg,';
		$sql.= 'description_prog,';
		$sql.= 'date_ini_prog,';
		$sql.= 'date_fin_prog,';
		$sql.= 'speciality_prog,';
		$sql.= 'fk_equipment_prog,';
		$sql.= 'fk_property_prog,';
		$sql.= 'fk_location_prog,';
		$sql.= 'typemant_prog,';
		$sql.= 'fk_user_prog,';
		$sql.= 'image_ini,';
		$sql.= 'fk_user_create,';
		$sql.= 'fk_user_mod,';
		$sql.= 'datec,';
		$sql.= 'datem,';
		$sql.= 'status,';
		$sql.= 'description_confirm,';
		$sql.= 'statut_job';


		$sql .= ') VALUES (';

		$sql .= ' '.(! isset($this->entity)?'NULL':$this->entity).',';
		$sql .= ' '.(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql .= ' '.(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':"'".$this->db->idate($this->date_create)."'").',';
		$sql .= ' '.(! isset($this->fk_member)?'NULL':$this->fk_member).',';
		$sql .= ' '.(! isset($this->fk_departament)?'NULL':$this->fk_departament).',';
		$sql .= ' '.(! isset($this->fk_equipment)?'NULL':$this->fk_equipment).',';
		$sql .= ' '.(! isset($this->fk_property)?'NULL':$this->fk_property).',';
		$sql .= ' '.(! isset($this->fk_location)?'NULL':$this->fk_location).',';
		$sql .= ' '.(! isset($this->fk_soc)?'NULL':$this->fk_soc).',';
		$sql .= ' '.(! isset($this->fk_type_repair)?'NULL':$this->fk_type_repair).',';
		$sql .= ' '.(! isset($this->email)?'NULL':"'".$this->db->escape($this->email)."'").',';
		$sql .= ' '.(! isset($this->internal)?'NULL':$this->internal).',';
		$sql .= ' '.(! isset($this->detail_problem)?'NULL':"'".$this->db->escape($this->detail_problem)."'").',';
		$sql .= ' '.(! isset($this->address_ip)?'NULL':"'".$this->db->escape($this->address_ip)."'").',';
		$sql .= ' '.(! isset($this->fk_user_assign)?'NULL':$this->fk_user_assign).',';
		$sql .= ' '.(! isset($this->date_assign) || dol_strlen($this->date_assign)==0?'NULL':"'".$this->db->idate($this->date_assign)."'").',';
		$sql .= ' '.(! isset($this->speciality)?'NULL':"'".$this->db->escape($this->speciality)."'").',';
		$sql .= ' '.(! isset($this->tokenreg)?'NULL':"'".$this->db->escape($this->tokenreg)."'").',';
		$sql .= ' '.(! isset($this->description_prog)?'NULL':"'".$this->db->escape($this->description_prog)."'").',';
		$sql .= ' '.(! isset($this->date_ini_prog) || dol_strlen($this->date_ini_prog)==0?'NULL':"'".$this->db->idate($this->date_ini_prog)."'").',';
		$sql .= ' '.(! isset($this->date_fin_prog) || dol_strlen($this->date_fin_prog)==0?'NULL':"'".$this->db->idate($this->date_fin_prog)."'").',';
		$sql .= ' '.(! isset($this->speciality_prog)?'NULL':"'".$this->db->escape($this->speciality_prog)."'").',';
		$sql .= ' '.(! isset($this->fk_equipment_prog)?'NULL':$this->fk_equipment_prog).',';
		$sql .= ' '.(! isset($this->fk_property_prog)?'NULL':$this->fk_property_prog).',';
		$sql .= ' '.(! isset($this->fk_location_prog)?'NULL':$this->fk_location_prog).',';
		$sql .= ' '.(! isset($this->typemant_prog)?'NULL':"'".$this->db->escape($this->typemant_prog)."'").',';
		$sql .= ' '.(! isset($this->fk_user_prog)?'NULL':$this->fk_user_prog).',';
		$sql .= ' '.(! isset($this->image_ini)?'NULL':"'".$this->db->escape($this->image_ini)."'").',';
		$sql .= ' '.(! isset($this->fk_user_create)?'NULL':$this->fk_user_create).',';
		$sql .= ' '.$user->id.',';
		$sql .= ' '."'".$this->db->idate(dol_now())."'".',';
		$sql .= ' '.(! isset($this->datem) || dol_strlen($this->datem)==0?'NULL':"'".$this->db->idate($this->datem)."'").',';
		$sql .= ' '.(! isset($this->status)?'NULL':$this->status).',';
		$sql .= ' '.(! isset($this->description_confirm)?'NULL':"'".$this->db->escape($this->description_confirm)."'").',';
		$sql .= ' '.(! isset($this->statut_job)?'NULL':$this->statut_job);


		$sql .= ')';

		$this->db->begin();
		echo $sql;
		$resql = $this->db->query($sql);
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . implode(',', $this->errors), LOG_ERR);
		}

		if (!$error) {
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX . $this->table_element);

			if (!$notrigger) {
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action to call a trigger.

				//// Call triggers
				//$result=$this->call_trigger('MYOBJECT_CREATE',$user);
				//if ($result < 0) $error++;
				//// End call triggers
			}
		}

		// Commit or rollback
		if ($error) {
			$this->db->rollback();

			return - 1 * $error;
		} else {
			$this->db->commit();

			return $this->id;
		}
	}

	/**
	 * Load object in memory from the database
	 *
	 * @param int    $id  Id object
	 * @param string $ref Ref
	 *
	 * @return int <0 if KO, 0 if not found, >0 if OK
	 */
	public function fetch($id, $ref = null)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.entity,";
		$sql .= " t.ref,";
		$sql .= " t.date_create,";
		$sql .= " t.fk_member,";
		$sql .= " t.fk_departament,";
		$sql .= " t.fk_equipment,";
		$sql .= " t.fk_property,";
		$sql .= " t.fk_location,";
		$sql .= " t.fk_soc,";
		$sql .= " t.fk_type_repair,";
		$sql .= " t.email,";
		$sql .= " t.internal,";
		$sql .= " t.detail_problem,";
		$sql .= " t.address_ip,";
		$sql .= " t.fk_user_assign,";
		$sql .= " t.date_assign,";
		$sql .= " t.speciality,";
		$sql .= " t.tokenreg,";
		$sql .= " t.description_prog,";
		$sql .= " t.date_ini_prog,";
		$sql .= " t.date_fin_prog,";
		$sql .= " t.speciality_prog,";
		$sql .= " t.fk_equipment_prog,";
		$sql .= " t.fk_property_prog,";
		$sql .= " t.fk_location_prog,";
		$sql .= " t.typemant_prog,";
		$sql .= " t.fk_user_prog,";
		$sql .= " t.image_ini,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.datec,";
		$sql .= " t.datem,";
		$sql .= " t.tms,";
		$sql .= " t.status,";
		$sql .= " t.description_confirm,";
		$sql .= " t.statut_job";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
			$sql .= " AND entity IN (" . getEntity("mworkrequest", 1) . ")";
		}
		if (null !== $ref) {
			$sql .= ' AND t.ref = ' . '\'' . $ref . '\'';
		} else {
			$sql .= ' AND t.rowid = ' . $id;
		}

		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			if ($numrows) {
				$obj = $this->db->fetch_object($resql);

				$this->id = $obj->rowid;

				$this->entity = $obj->entity;
				$this->ref = $obj->ref;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->fk_member = $obj->fk_member;
				$this->fk_departament = $obj->fk_departament;
				$this->fk_equipment = $obj->fk_equipment;
				$this->fk_property = $obj->fk_property;
				$this->fk_location = $obj->fk_location;
				$this->fk_soc = $obj->fk_soc;
				$this->fk_type_repair = $obj->fk_type_repair;
				$this->email = $obj->email;
				$this->internal = $obj->internal;
				$this->detail_problem = $obj->detail_problem;
				$this->address_ip = $obj->address_ip;
				$this->fk_user_assign = $obj->fk_user_assign;
				$this->date_assign = $this->db->jdate($obj->date_assign);
				$this->speciality = $obj->speciality;
				$this->tokenreg = $obj->tokenreg;
				$this->description_prog = $obj->description_prog;
				$this->date_ini_prog = $this->db->jdate($obj->date_ini_prog);
				$this->date_fin_prog = $this->db->jdate($obj->date_fin_prog);
				$this->speciality_prog = $obj->speciality_prog;
				$this->fk_equipment_prog = $obj->fk_equipment_prog;
				$this->fk_property_prog = $obj->fk_property_prog;
				$this->fk_location_prog = $obj->fk_location_prog;
				$this->typemant_prog = $obj->typemant_prog;
				$this->fk_user_prog = $obj->fk_user_prog;
				$this->image_ini = $obj->image_ini;
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_mod = $obj->fk_user_mod;
				$this->datec = $this->db->jdate($obj->datec);
				$this->datem = $this->db->jdate($obj->datem);
				$this->tms = $this->db->jdate($obj->tms);
				$this->status = $obj->status;
				$this->description_confirm = $obj->description_confirm;
				$this->statut_job = $obj->statut_job;


			}

			// Retrieve all extrafields for invoice
			// fetch optionals attributes and labels
			require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
			$extrafields=new ExtraFields($this->db);
			$extralabels=$extrafields->fetch_name_optionals_label($this->table_element,true);
			$this->fetch_optionals($this->id,$extralabels);

			// $this->fetch_lines();

			$this->db->free($resql);

			if ($numrows) {
				return 1;
			} else {
				return 0;
			}
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . implode(',', $this->errors), LOG_ERR);

			return - 1;
		}
	}

	/**
	 * Load object in memory from the database
	 *
	 * @param string $sortorder Sort Order
	 * @param string $sortfield Sort field
	 * @param int    $limit     offset limit
	 * @param int    $offset    offset limit
	 * @param array  $filter    filter array
	 * @param string $filtermode filter mode (AND or OR)
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND', $filterstatic='', $lView=false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.entity,";
		$sql .= " t.ref,";
		$sql .= " t.date_create,";
		$sql .= " t.fk_member,";
		$sql .= " t.fk_departament,";
		$sql .= " t.fk_equipment,";
		$sql .= " t.fk_property,";
		$sql .= " t.fk_location,";
		$sql .= " t.fk_soc,";
		$sql .= " t.fk_type_repair,";
		$sql .= " t.email,";
		$sql .= " t.internal,";
		$sql .= " t.detail_problem,";
		$sql .= " t.address_ip,";
		$sql .= " t.fk_user_assign,";
		$sql .= " t.date_assign,";
		$sql .= " t.speciality,";
		$sql .= " t.tokenreg,";
		$sql .= " t.description_prog,";
		$sql .= " t.date_ini_prog,";
		$sql .= " t.date_fin_prog,";
		$sql .= " t.speciality_prog,";
		$sql .= " t.fk_equipment_prog,";
		$sql .= " t.fk_property_prog,";
		$sql .= " t.fk_location_prog,";
		$sql .= " t.typemant_prog,";
		$sql .= " t.fk_user_prog,";
		$sql .= " t.image_ini,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.datec,";
		$sql .= " t.datem,";
		$sql .= " t.tms,";
		$sql .= " t.status,";
		$sql .= " t.description_confirm,";
		$sql .= " t.statut_job";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element. ' as t';

		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				$sqlwhere [] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
			}
		}
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
			$sql .= " AND entity IN (" . getEntity("mworkrequest", 1) . ")";
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' AND ' . implode(' '.$filtermode.' ', $sqlwhere);
		}
		if ($filterstatic) $sql.= $filterstatic;

		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield,$sortorder);
		}
		if (!empty($limit)) {
			$sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
		}

		$this->lines = array();

		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new MworkrequestLine();

				$line->id = $obj->rowid;

				$line->entity = $obj->entity;
				$line->ref = $obj->ref;
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->fk_member = $obj->fk_member;
				$line->fk_departament = $obj->fk_departament;
				$line->fk_equipment = $obj->fk_equipment;
				$line->fk_property = $obj->fk_property;
				$line->fk_location = $obj->fk_location;
				$line->fk_soc = $obj->fk_soc;
				$line->fk_type_repair = $obj->fk_type_repair;
				$line->email = $obj->email;
				$line->internal = $obj->internal;
				$line->detail_problem = $obj->detail_problem;
				$line->address_ip = $obj->address_ip;
				$line->fk_user_assign = $obj->fk_user_assign;
				$line->date_assign = $this->db->jdate($obj->date_assign);
				$line->speciality = $obj->speciality;
				$line->tokenreg = $obj->tokenreg;
				$line->description_prog = $obj->description_prog;
				$line->date_ini_prog = $this->db->jdate($obj->date_ini_prog);
				$line->date_fin_prog = $this->db->jdate($obj->date_fin_prog);
				$line->speciality_prog = $obj->speciality_prog;
				$line->fk_equipment_prog = $obj->fk_equipment_prog;
				$line->fk_property_prog = $obj->fk_property_prog;
				$line->fk_location_prog = $obj->fk_location_prog;
				$line->typemant_prog = $obj->typemant_prog;
				$line->fk_user_prog = $obj->fk_user_prog;
				$line->image_ini = $obj->image_ini;
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->datec = $this->db->jdate($obj->datec);
				$line->datem = $this->db->jdate($obj->datem);
				$line->tms = $this->db->jdate($obj->tms);
				$line->status = $obj->status;
				$line->description_confirm = $obj->description_confirm;
				$line->statut_job = $obj->statut_job;



				if ($lView && $num == 1) $this->fetch($obj->rowid);

				$this->lines[$line->id] = $line;
			}
			$this->db->free($resql);

			return $num;
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . implode(',', $this->errors), LOG_ERR);

			return - 1;
		}
	}

	/**
	 * Update object into database
	 *
	 * @param  User $user      User that modifies
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function update(User $user, $notrigger = false)
	{
		$error = 0;

		dol_syslog(__METHOD__, LOG_DEBUG);

		// Clean parameters

		if (isset($this->entity)) {
			$this->entity = trim($this->entity);
		}
		if (isset($this->ref)) {
			$this->ref = trim($this->ref);
		}
		if (isset($this->fk_member)) {
			$this->fk_member = trim($this->fk_member);
		}
		if (isset($this->fk_departament)) {
			$this->fk_departament = trim($this->fk_departament);
		}
		if (isset($this->fk_equipment)) {
			$this->fk_equipment = trim($this->fk_equipment);
		}
		if (isset($this->fk_property)) {
			$this->fk_property = trim($this->fk_property);
		}
		if (isset($this->fk_location)) {
			$this->fk_location = trim($this->fk_location);
		}
		if (isset($this->fk_soc)) {
			$this->fk_soc = trim($this->fk_soc);
		}
		if (isset($this->fk_type_repair)) {
			$this->fk_type_repair = trim($this->fk_type_repair);
		}
		if (isset($this->email)) {
			$this->email = trim($this->email);
		}
		if (isset($this->internal)) {
			$this->internal = trim($this->internal);
		}
		if (isset($this->detail_problem)) {
			$this->detail_problem = trim($this->detail_problem);
		}
		if (isset($this->address_ip)) {
			$this->address_ip = trim($this->address_ip);
		}
		if (isset($this->fk_user_assign)) {
			$this->fk_user_assign = trim($this->fk_user_assign);
		}
		if (isset($this->speciality)) {
			$this->speciality = trim($this->speciality);
		}
		if (isset($this->tokenreg)) {
			$this->tokenreg = trim($this->tokenreg);
		}
		if (isset($this->description_prog)) {
			$this->description_prog = trim($this->description_prog);
		}
		if (isset($this->speciality_prog)) {
			$this->speciality_prog = trim($this->speciality_prog);
		}
		if (isset($this->fk_equipment_prog)) {
			$this->fk_equipment_prog = trim($this->fk_equipment_prog);
		}
		if (isset($this->fk_property_prog)) {
			$this->fk_property_prog = trim($this->fk_property_prog);
		}
		if (isset($this->fk_location_prog)) {
			$this->fk_location_prog = trim($this->fk_location_prog);
		}
		if (isset($this->typemant_prog)) {
			$this->typemant_prog = trim($this->typemant_prog);
		}
		if (isset($this->fk_user_prog)) {
			$this->fk_user_prog = trim($this->fk_user_prog);
		}
		if (isset($this->image_ini)) {
			$this->image_ini = trim($this->image_ini);
		}
		if (isset($this->fk_user_create)) {
			$this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_mod)) {
			$this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->status)) {
			$this->status = trim($this->status);
		}
		if (isset($this->description_confirm)) {
			$this->description_confirm = trim($this->description_confirm);
		}
		if (isset($this->statut_job)) {
			$this->statut_job = trim($this->statut_job);
		}



		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';

		$sql .= ' entity = '.(isset($this->entity)?$this->entity:"null").',';
		$sql .= ' ref = '.(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").',';
		$sql .= ' date_create = '.(! isset($this->date_create) || dol_strlen($this->date_create) != 0 ? "'".$this->db->idate($this->date_create)."'" : 'null').',';
		$sql .= ' fk_member = '.(isset($this->fk_member)?$this->fk_member:"null").',';
		$sql .= ' fk_departament = '.(isset($this->fk_departament)?$this->fk_departament:"null").',';
		$sql .= ' fk_equipment = '.(isset($this->fk_equipment)?$this->fk_equipment:"null").',';
		$sql .= ' fk_property = '.(isset($this->fk_property)?$this->fk_property:"null").',';
		$sql .= ' fk_location = '.(isset($this->fk_location)?$this->fk_location:"null").',';
		$sql .= ' fk_soc = '.(isset($this->fk_soc)?$this->fk_soc:"null").',';
		$sql .= ' fk_type_repair = '.(isset($this->fk_type_repair)?$this->fk_type_repair:"null").',';
		$sql .= ' email = '.(isset($this->email)?"'".$this->db->escape($this->email)."'":"null").',';
		$sql .= ' internal = '.(isset($this->internal)?$this->internal:"null").',';
		$sql .= ' detail_problem = '.(isset($this->detail_problem)?"'".$this->db->escape($this->detail_problem)."'":"null").',';
		$sql .= ' address_ip = '.(isset($this->address_ip)?"'".$this->db->escape($this->address_ip)."'":"null").',';
		$sql .= ' fk_user_assign = '.(isset($this->fk_user_assign)?$this->fk_user_assign:"null").',';
		$sql .= ' date_assign = '.(! isset($this->date_assign) || dol_strlen($this->date_assign) != 0 ? "'".$this->db->idate($this->date_assign)."'" : 'null').',';
		$sql .= ' speciality = '.(isset($this->speciality)?"'".$this->db->escape($this->speciality)."'":"null").',';
		$sql .= ' tokenreg = '.(isset($this->tokenreg)?"'".$this->db->escape($this->tokenreg)."'":"null").',';
		$sql .= ' description_prog = '.(isset($this->description_prog)?"'".$this->db->escape($this->description_prog)."'":"null").',';
		$sql .= ' date_ini_prog = '.(! isset($this->date_ini_prog) || dol_strlen($this->date_ini_prog) != 0 ? "'".$this->db->idate($this->date_ini_prog)."'" : 'null').',';
		$sql .= ' date_fin_prog = '.(! isset($this->date_fin_prog) || dol_strlen($this->date_fin_prog) != 0 ? "'".$this->db->idate($this->date_fin_prog)."'" : 'null').',';
		$sql .= ' speciality_prog = '.(isset($this->speciality_prog)?"'".$this->db->escape($this->speciality_prog)."'":"null").',';
		$sql .= ' fk_equipment_prog = '.(isset($this->fk_equipment_prog)?$this->fk_equipment_prog:"null").',';
		$sql .= ' fk_property_prog = '.(isset($this->fk_property_prog)?$this->fk_property_prog:"null").',';
		$sql .= ' fk_location_prog = '.(isset($this->fk_location_prog)?$this->fk_location_prog:"null").',';
		$sql .= ' typemant_prog = '.(isset($this->typemant_prog)?"'".$this->db->escape($this->typemant_prog)."'":"null").',';
		$sql .= ' fk_user_prog = '.(isset($this->fk_user_prog)?$this->fk_user_prog:"null").',';
		$sql .= ' image_ini = '.(isset($this->image_ini)?"'".$this->db->escape($this->image_ini)."'":"null").',';
		$sql .= ' fk_user_create = '.(isset($this->fk_user_create)?$this->fk_user_create:"null").',';
		$sql .= ' fk_user_mod = '.$user->id.',';
		$sql .= ' datec = '.(! isset($this->datec) || dol_strlen($this->datec) != 0 ? "'".$this->db->idate($this->datec)."'" : 'null').',';
		$sql .= ' datem = '.(! isset($this->datem) || dol_strlen($this->datem) != 0 ? "'".$this->db->idate($this->datem)."'" : 'null').',';
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
		$sql .= ' status = '.(isset($this->status)?$this->status:"null").',';
		$sql .= ' description_confirm = '.(isset($this->description_confirm)?"'".$this->db->escape($this->description_confirm)."'":"null").',';
		$sql .= ' statut_job = '.(isset($this->statut_job)?$this->statut_job:"null");


		$sql .= ' WHERE rowid=' . $this->id;

		$this->db->begin();

		$resql = $this->db->query($sql);
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . implode(',', $this->errors), LOG_ERR);
		}

		if (!$error && !$notrigger) {
			// Uncomment this and change MYOBJECT to your own tag if you
			// want this action calls a trigger.

			//// Call triggers
			//$result=$this->call_trigger('MYOBJECT_MODIFY',$user);
			//if ($result < 0) { $error++; //Do also what you must do to rollback action if trigger fail}
			//// End call triggers
		}

		// Commit or rollback
		if ($error) {
			$this->db->rollback();

			return - 1 * $error;
		} else {
			$this->db->commit();

			return 1;
		}
	}

	/**
	 * Delete object in database
	 *
	 * @param User $user      User that deletes
	 * @param bool $notrigger false=launch triggers after, true=disable triggers
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function delete(User $user, $notrigger = false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$error = 0;

		$this->db->begin();

		if (!$error) {
			if (!$notrigger) {
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action calls a trigger.

				//// Call triggers
				//$result=$this->call_trigger('MYOBJECT_DELETE',$user);
				//if ($result < 0) { $error++; //Do also what you must do to rollback action if trigger fail}
				//// End call triggers
			}
		}

		// If you need to delete child tables to, you can insert them here

		if (!$error) {
			$sql = 'DELETE FROM ' . MAIN_DB_PREFIX . $this->table_element;
			$sql .= ' WHERE rowid=' . $this->id;

			$resql = $this->db->query($sql);
			if (!$resql) {
				$error ++;
				$this->errors[] = 'Error ' . $this->db->lasterror();
				dol_syslog(__METHOD__ . ' ' . implode(',', $this->errors), LOG_ERR);
			}
		}

		// Commit or rollback
		if ($error) {
			$this->db->rollback();

			return - 1 * $error;
		} else {
			$this->db->commit();

			return 1;
		}
	}

	/**
	 * Load an object from its id and create a new one in database
	 *
	 * @param int $fromid Id of object to clone
	 *
	 * @return int New id of clone
	 */
	public function createFromClone($fromid)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		global $user;
		$error = 0;
		$object = new Mworkrequest($this->db);

		$this->db->begin();

		// Load source object
		$object->fetch($fromid);
		// Reset object
		$object->id = 0;

		// Clear fields
		// ...

		// Create clone
		$result = $object->create($user);

		// Other options
		if ($result < 0) {
			$error ++;
			$this->errors = $object->errors;
			dol_syslog(__METHOD__ . ' ' . implode(',', $this->errors), LOG_ERR);
		}

		// End
		if (!$error) {
			$this->db->commit();

			return $object->id;
		} else {
			$this->db->rollback();

			return - 1;
		}
	}

	/**
	 *  Return a link to the object card (with optionaly the picto)
	 *
	 *	@param	int		$withpicto			Include picto in link (0=No picto, 1=Include picto into link, 2=Only picto)
	 *	@param	string	$option				On what the link point to
	 *  @param	int  	$notooltip			1=Disable tooltip
	 *  @param	int		$maxlen				Max length of visible user name
	 *  @param  string  $morecss            Add more css on link
	 *	@return	string						String with URL
	 */
	function getNomUrl($withpicto=0, $option='', $notooltip=0, $maxlen=24, $morecss='')
	{
		global $db, $conf, $langs;
		global $dolibarr_main_authentication, $dolibarr_main_demo;
		global $menumanager;

		if (! empty($conf->dol_no_mouse_hover)) $notooltip=1;   // Force disable tooltips

		$result = '';
		$companylink = '';

		$label = '<u>' . $langs->trans("Ticket work") . '</u>';
		$label.= '<br>';
		$label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

		$url = DOL_URL_ROOT.'/mant/request/'.'card.php?id='.$this->id;

		$linkclose='';
		if (empty($notooltip))
		{
			if (! empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER))
			{
				$label=$langs->trans("ShowProject");
				$linkclose.=' alt="'.dol_escape_htmltag($label, 1).'"';
			}
			$linkclose.=' title="'.dol_escape_htmltag($label, 1).'"';
			$linkclose.=' class="classfortooltip'.($morecss?' '.$morecss:'').'"';
		}
		else $linkclose = ($morecss?' class="'.$morecss.'"':'');

			$linkstart = '<a href="'.$url.'"';
			$linkstart.=$linkclose.'>';
			$linkend='</a>';

			if ($withpicto)
			{
				$result.=($linkstart.img_picto(($notooltip?'':$label),DOL_URL_ROOT.'/mant/img/ticket','',1).$linkend);
				if ($withpicto != 2) $result.=' ';
			}
			$result.= $linkstart . $this->ref . $linkend;
			return $result;
		}

	/**
	 *  Retourne le libelle du status d'un user (actif, inactif)
	 *
	 *  @param	int		$mode          0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return	string 			       Label of status
	 */
	function getLibStatutx($mode=0)
	{
		return $this->LibStatut($this->status,$mode);
	}

	/**
	 *  Return the status
	 *
	 *  @param	int		$status        	Id status
	 *  @param  int		$mode          	0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 5=Long label + Picto
	 *  @return string 			       	Label of status
	 */
	static function LibStatutx($status,$mode=0)
	{
		global $langs;

		if ($mode == 0)
		{
			$prefix='';
			if ($status == 1) return $langs->trans('Enabled');
			if ($status == 0) return $langs->trans('Disabled');
		}
		if ($mode == 1)
		{
			if ($status == 1) return $langs->trans('Enabled');
			if ($status == 0) return $langs->trans('Disabled');
		}
		if ($mode == 2)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5').' '.$langs->trans('Disabled');
		}
		if ($mode == 3)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5');
		}
		if ($mode == 4)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5').' '.$langs->trans('Disabled');
		}
		if ($mode == 5)
		{
			if ($status == 1) return $langs->trans('Enabled').' '.img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return $langs->trans('Disabled').' '.img_picto($langs->trans('Disabled'),'statut5');
		}
		if ($mode == 6)
		{
			if ($status == 1) return $langs->trans('Enabled').' '.img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return $langs->trans('Disabled').' '.img_picto($langs->trans('Disabled'),'statut5');
		}
	}


	/**
	 * Initialise object with example values
	 * Id must be 0 if object instance is a specimen
	 *
	 * @return void
	 */
	public function initAsSpecimen()
	{
		$this->id = 0;

		$this->entity = '';
		$this->ref = '';
		$this->date_create = '';
		$this->fk_member = '';
		$this->fk_departament = '';
		$this->fk_equipment = '';
		$this->fk_property = '';
		$this->fk_location = '';
		$this->fk_soc = '';
		$this->fk_type_repair = '';
		$this->email = '';
		$this->internal = '';
		$this->detail_problem = '';
		$this->address_ip = '';
		$this->fk_user_assign = '';
		$this->date_assign = '';
		$this->speciality = '';
		$this->tokenreg = '';
		$this->description_prog = '';
		$this->date_ini_prog = '';
		$this->date_fin_prog = '';
		$this->speciality_prog = '';
		$this->fk_equipment_prog = '';
		$this->fk_property_prog = '';
		$this->fk_location_prog = '';
		$this->typemant_prog = '';
		$this->fk_user_prog = '';
		$this->image_ini = '';
		$this->fk_user_create = '';
		$this->fk_user_mod = '';
		$this->datec = '';
		$this->datem = '';
		$this->tms = '';
		$this->status = '';
		$this->description_confirm = '';
		$this->statut_job = '';


	}

}

/**
 * Class MworkrequestLine
 */
class MworkrequestLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */

	public $entity;
	public $ref;
	public $date_create = '';
	public $fk_member;
	public $fk_departament;
	public $fk_equipment;
	public $fk_property;
	public $fk_location;
	public $fk_soc;
	public $fk_type_repair;
	public $email;
	public $internal;
	public $detail_problem;
	public $address_ip;
	public $fk_user_assign;
	public $date_assign = '';
	public $speciality;
	public $tokenreg;
	public $description_prog;
	public $date_ini_prog = '';
	public $date_fin_prog = '';
	public $speciality_prog;
	public $fk_equipment_prog;
	public $fk_property_prog;
	public $fk_location_prog;
	public $typemant_prog;
	public $fk_user_prog;
	public $image_ini;
	public $fk_user_create;
	public $fk_user_mod;
	public $datec = '';
	public $datem = '';
	public $tms = '';
	public $status;
	public $description_confirm;
	public $statut_job;

	/**
	 * @var mixed Sample line property 2
	 */

}
