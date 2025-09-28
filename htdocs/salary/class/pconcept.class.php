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
 * \file    salary/pconcept.class.php
 * \ingroup salary
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Pconcept
 *
 * Put here description of your class
 *
 * @see CommonObject
 */
class Pconcept extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'pconcept';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'p_concept';

	/**
	 * @var PconceptLine[] Lines
	 */
	public $lines = array();

	/**
	 */

	public $entity;
	public $ref;
	public $detail;
	public $details;
	public $type_cod;
	public $type_mov;
	public $ref_formula;
	public $wage_inf;
	public $calc_oblig;
	public $calc_afp;
	public $calc_rciva;
	public $calc_agui;
	public $calc_vac;
	public $calc_indem;
	public $calc_afpvejez;
	public $calc_contrpat;
	public $calc_afpriesgo;
	public $calc_aportsol;
	public $calc_quin;
	public $print;
	public $print_input;
	public $fk_codfol;
	public $contab_account_ref;
	public $income_tax;
	public $percent;

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
		if (isset($this->detail)) {
			 $this->detail = trim($this->detail);
		}
		if (isset($this->details)) {
			 $this->details = trim($this->details);
		}
		if (isset($this->type_cod)) {
			 $this->type_cod = trim($this->type_cod);
		}
		if (isset($this->type_mov)) {
			 $this->type_mov = trim($this->type_mov);
		}
		if (isset($this->ref_formula)) {
			 $this->ref_formula = trim($this->ref_formula);
		}
		if (isset($this->wage_inf)) {
			 $this->wage_inf = trim($this->wage_inf);
		}
		if (isset($this->calc_oblig)) {
			 $this->calc_oblig = trim($this->calc_oblig);
		}
		if (isset($this->calc_afp)) {
			 $this->calc_afp = trim($this->calc_afp);
		}
		if (isset($this->calc_rciva)) {
			 $this->calc_rciva = trim($this->calc_rciva);
		}
		if (isset($this->calc_agui)) {
			 $this->calc_agui = trim($this->calc_agui);
		}
		if (isset($this->calc_vac)) {
			 $this->calc_vac = trim($this->calc_vac);
		}
		if (isset($this->calc_indem)) {
			 $this->calc_indem = trim($this->calc_indem);
		}
		if (isset($this->calc_afpvejez)) {
			 $this->calc_afpvejez = trim($this->calc_afpvejez);
		}
		if (isset($this->calc_contrpat)) {
			 $this->calc_contrpat = trim($this->calc_contrpat);
		}
		if (isset($this->calc_afpriesgo)) {
			 $this->calc_afpriesgo = trim($this->calc_afpriesgo);
		}
		if (isset($this->calc_aportsol)) {
			 $this->calc_aportsol = trim($this->calc_aportsol);
		}
		if (isset($this->calc_quin)) {
			 $this->calc_quin = trim($this->calc_quin);
		}
		if (isset($this->print)) {
			 $this->print = trim($this->print);
		}
		if (isset($this->print_input)) {
			 $this->print_input = trim($this->print_input);
		}
		if (isset($this->fk_codfol)) {
			 $this->fk_codfol = trim($this->fk_codfol);
		}
		if (isset($this->contab_account_ref)) {
			 $this->contab_account_ref = trim($this->contab_account_ref);
		}
		if (isset($this->income_tax)) {
			 $this->income_tax = trim($this->income_tax);
		}
		if (isset($this->percent)) {
			 $this->percent = trim($this->percent);
		}



		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';

		$sql.= 'entity,';
		$sql.= 'ref,';
		$sql.= 'detail,';
		$sql.= 'details,';
		$sql.= 'type_cod,';
		$sql.= 'type_mov,';
		$sql.= 'ref_formula,';
		$sql.= 'wage_inf,';
		$sql.= 'calc_oblig,';
		$sql.= 'calc_afp,';
		$sql.= 'calc_rciva,';
		$sql.= 'calc_agui,';
		$sql.= 'calc_vac,';
		$sql.= 'calc_indem,';
		$sql.= 'calc_afpvejez,';
		$sql.= 'calc_contrpat,';
		$sql.= 'calc_afpriesgo,';
		$sql.= 'calc_aportsol,';
		$sql.= 'calc_quin,';
		$sql.= 'print,';
		$sql.= 'print_input,';
		$sql.= 'fk_codfol,';
		$sql.= 'contab_account_ref,';
		$sql.= 'income_tax,';
		$sql.= 'percent';


		$sql .= ') VALUES (';

		$sql .= ' '.(! isset($this->entity)?'NULL':$this->entity).',';
		$sql .= ' '.(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql .= ' '.(! isset($this->detail)?'NULL':"'".$this->db->escape($this->detail)."'").',';
		$sql .= ' '.(! isset($this->details)?'NULL':"'".$this->db->escape($this->details)."'").',';
		$sql .= ' '.(! isset($this->type_cod)?'NULL':$this->type_cod).',';
		$sql .= ' '.(! isset($this->type_mov)?'NULL':$this->type_mov).',';
		$sql .= ' '.(! isset($this->ref_formula)?'NULL':"'".$this->db->escape($this->ref_formula)."'").',';
		$sql .= ' '.(! isset($this->wage_inf)?'NULL':"'".$this->db->escape($this->wage_inf)."'").',';
		$sql .= ' '.(! isset($this->calc_oblig)?'NULL':$this->calc_oblig).',';
		$sql .= ' '.(! isset($this->calc_afp)?'NULL':$this->calc_afp).',';
		$sql .= ' '.(! isset($this->calc_rciva)?'NULL':$this->calc_rciva).',';
		$sql .= ' '.(! isset($this->calc_agui)?'NULL':$this->calc_agui).',';
		$sql .= ' '.(! isset($this->calc_vac)?'NULL':$this->calc_vac).',';
		$sql .= ' '.(! isset($this->calc_indem)?'NULL':$this->calc_indem).',';
		$sql .= ' '.(! isset($this->calc_afpvejez)?'NULL':$this->calc_afpvejez).',';
		$sql .= ' '.(! isset($this->calc_contrpat)?'NULL':$this->calc_contrpat).',';
		$sql .= ' '.(! isset($this->calc_afpriesgo)?'NULL':$this->calc_afpriesgo).',';
		$sql .= ' '.(! isset($this->calc_aportsol)?'NULL':$this->calc_aportsol).',';
		$sql .= ' '.(! isset($this->calc_quin)?'NULL':$this->calc_quin).',';
		$sql .= ' '.(! isset($this->print)?'NULL':$this->print).',';
		$sql .= ' '.(! isset($this->print_input)?'NULL':$this->print_input).',';
		$sql .= ' '.(! isset($this->fk_codfol)?'NULL':$this->fk_codfol).',';
		$sql .= ' '.(! isset($this->contab_account_ref)?'NULL':"'".$this->db->escape($this->contab_account_ref)."'").',';
		$sql .= ' '.(! isset($this->income_tax)?'NULL':$this->income_tax).',';
		$sql .= ' '.(! isset($this->percent)?'NULL':"'".$this->percent."'");


		$sql .= ')';

		$this->db->begin();

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
		$sql .= " t.detail,";
		$sql .= " t.details,";
		$sql .= " t.type_cod,";
		$sql .= " t.type_mov,";
		$sql .= " t.ref_formula,";
		$sql .= " t.wage_inf,";
		$sql .= " t.calc_oblig,";
		$sql .= " t.calc_afp,";
		$sql .= " t.calc_rciva,";
		$sql .= " t.calc_agui,";
		$sql .= " t.calc_vac,";
		$sql .= " t.calc_indem,";
		$sql .= " t.calc_afpvejez,";
		$sql .= " t.calc_contrpat,";
		$sql .= " t.calc_afpriesgo,";
		$sql .= " t.calc_aportsol,";
		$sql .= " t.calc_quin,";
		$sql .= " t.print,";
		$sql .= " t.print_input,";
		$sql .= " t.fk_codfol,";
		$sql .= " t.contab_account_ref,";
		$sql .= " t.income_tax,";
		$sql .= " t.percent";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
		    $sql .= " AND entity IN (" . getEntity("pconcept", 1) . ")";
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
				$this->detail = $obj->detail;
				$this->details = $obj->details;
				$this->type_cod = $obj->type_cod;
				$this->type_mov = $obj->type_mov;
				$this->ref_formula = $obj->ref_formula;
				$this->wage_inf = $obj->wage_inf;
				$this->calc_oblig = $obj->calc_oblig;
				$this->calc_afp = $obj->calc_afp;
				$this->calc_rciva = $obj->calc_rciva;
				$this->calc_agui = $obj->calc_agui;
				$this->calc_vac = $obj->calc_vac;
				$this->calc_indem = $obj->calc_indem;
				$this->calc_afpvejez = $obj->calc_afpvejez;
				$this->calc_contrpat = $obj->calc_contrpat;
				$this->calc_afpriesgo = $obj->calc_afpriesgo;
				$this->calc_aportsol = $obj->calc_aportsol;
				$this->calc_quin = $obj->calc_quin;
				$this->print = $obj->print;
				$this->print_input = $obj->print_input;
				$this->fk_codfol = $obj->fk_codfol;
				$this->contab_account_ref = $obj->contab_account_ref;
				$this->income_tax = $obj->income_tax;
				$this->percent = $obj->percent;


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
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lView=false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.entity,";
		$sql .= " t.ref,";
		$sql .= " t.detail,";
		$sql .= " t.details,";
		$sql .= " t.type_cod,";
		$sql .= " t.type_mov,";
		$sql .= " t.ref_formula,";
		$sql .= " t.wage_inf,";
		$sql .= " t.calc_oblig,";
		$sql .= " t.calc_afp,";
		$sql .= " t.calc_rciva,";
		$sql .= " t.calc_agui,";
		$sql .= " t.calc_vac,";
		$sql .= " t.calc_indem,";
		$sql .= " t.calc_afpvejez,";
		$sql .= " t.calc_contrpat,";
		$sql .= " t.calc_afpriesgo,";
		$sql .= " t.calc_aportsol,";
		$sql .= " t.calc_quin,";
		$sql .= " t.print,";
		$sql .= " t.print_input,";
		$sql .= " t.fk_codfol,";
		$sql .= " t.contab_account_ref,";
		$sql .= " t.income_tax,";
		$sql .= " t.percent";


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
		    $sql .= " AND entity IN (" . getEntity("pconcept", 1) . ")";
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' AND ' . implode(' '.$filtermode.' ', $sqlwhere);
		}
		if ($filterstatic){
			$sql.= $filterstatic;
		}
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
				$line = new PconceptLine();

				$line->id = $obj->rowid;

				$line->entity = $obj->entity;
				$line->ref = $obj->ref;
				$line->detail = $obj->detail;
				$line->details = $obj->details;
				$line->type_cod = $obj->type_cod;
				$line->type_mov = $obj->type_mov;
				$line->ref_formula = $obj->ref_formula;
				$line->wage_inf = $obj->wage_inf;
				$line->calc_oblig = $obj->calc_oblig;
				$line->calc_afp = $obj->calc_afp;
				$line->calc_rciva = $obj->calc_rciva;
				$line->calc_agui = $obj->calc_agui;
				$line->calc_vac = $obj->calc_vac;
				$line->calc_indem = $obj->calc_indem;
				$line->calc_afpvejez = $obj->calc_afpvejez;
				$line->calc_contrpat = $obj->calc_contrpat;
				$line->calc_afpriesgo = $obj->calc_afpriesgo;
				$line->calc_aportsol = $obj->calc_aportsol;
				$line->calc_quin = $obj->calc_quin;
				$line->print = $obj->print;
				$line->print_input = $obj->print_input;
				$line->fk_codfol = $obj->fk_codfol;
				$line->contab_account_ref = $obj->contab_account_ref;
				$line->income_tax = $obj->income_tax;
				$line->percent = $obj->percent;



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
		if (isset($this->detail)) {
			 $this->detail = trim($this->detail);
		}
		if (isset($this->details)) {
			 $this->details = trim($this->details);
		}
		if (isset($this->type_cod)) {
			 $this->type_cod = trim($this->type_cod);
		}
		if (isset($this->type_mov)) {
			 $this->type_mov = trim($this->type_mov);
		}
		if (isset($this->ref_formula)) {
			 $this->ref_formula = trim($this->ref_formula);
		}
		if (isset($this->wage_inf)) {
			 $this->wage_inf = trim($this->wage_inf);
		}
		if (isset($this->calc_oblig)) {
			 $this->calc_oblig = trim($this->calc_oblig);
		}
		if (isset($this->calc_afp)) {
			 $this->calc_afp = trim($this->calc_afp);
		}
		if (isset($this->calc_rciva)) {
			 $this->calc_rciva = trim($this->calc_rciva);
		}
		if (isset($this->calc_agui)) {
			 $this->calc_agui = trim($this->calc_agui);
		}
		if (isset($this->calc_vac)) {
			 $this->calc_vac = trim($this->calc_vac);
		}
		if (isset($this->calc_indem)) {
			 $this->calc_indem = trim($this->calc_indem);
		}
		if (isset($this->calc_afpvejez)) {
			 $this->calc_afpvejez = trim($this->calc_afpvejez);
		}
		if (isset($this->calc_contrpat)) {
			 $this->calc_contrpat = trim($this->calc_contrpat);
		}
		if (isset($this->calc_afpriesgo)) {
			 $this->calc_afpriesgo = trim($this->calc_afpriesgo);
		}
		if (isset($this->calc_aportsol)) {
			 $this->calc_aportsol = trim($this->calc_aportsol);
		}
		if (isset($this->calc_quin)) {
			 $this->calc_quin = trim($this->calc_quin);
		}
		if (isset($this->print)) {
			 $this->print = trim($this->print);
		}
		if (isset($this->print_input)) {
			 $this->print_input = trim($this->print_input);
		}
		if (isset($this->fk_codfol)) {
			 $this->fk_codfol = trim($this->fk_codfol);
		}
		if (isset($this->contab_account_ref)) {
			 $this->contab_account_ref = trim($this->contab_account_ref);
		}
		if (isset($this->income_tax)) {
			 $this->income_tax = trim($this->income_tax);
		}
		if (isset($this->percent)) {
			 $this->percent = trim($this->percent);
		}



		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';

		$sql .= ' entity = '.(isset($this->entity)?$this->entity:"null").',';
		$sql .= ' ref = '.(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").',';
		$sql .= ' detail = '.(isset($this->detail)?"'".$this->db->escape($this->detail)."'":"null").',';
		$sql .= ' details = '.(isset($this->details)?"'".$this->db->escape($this->details)."'":"null").',';
		$sql .= ' type_cod = '.(isset($this->type_cod)?$this->type_cod:"null").',';
		$sql .= ' type_mov = '.(isset($this->type_mov)?$this->type_mov:"null").',';
		$sql .= ' ref_formula = '.(isset($this->ref_formula)?"'".$this->db->escape($this->ref_formula)."'":"null").',';
		$sql .= ' wage_inf = '.(isset($this->wage_inf)?"'".$this->db->escape($this->wage_inf)."'":"null").',';
		$sql .= ' calc_oblig = '.(isset($this->calc_oblig)?$this->calc_oblig:"null").',';
		$sql .= ' calc_afp = '.(isset($this->calc_afp)?$this->calc_afp:"null").',';
		$sql .= ' calc_rciva = '.(isset($this->calc_rciva)?$this->calc_rciva:"null").',';
		$sql .= ' calc_agui = '.(isset($this->calc_agui)?$this->calc_agui:"null").',';
		$sql .= ' calc_vac = '.(isset($this->calc_vac)?$this->calc_vac:"null").',';
		$sql .= ' calc_indem = '.(isset($this->calc_indem)?$this->calc_indem:"null").',';
		$sql .= ' calc_afpvejez = '.(isset($this->calc_afpvejez)?$this->calc_afpvejez:"null").',';
		$sql .= ' calc_contrpat = '.(isset($this->calc_contrpat)?$this->calc_contrpat:"null").',';
		$sql .= ' calc_afpriesgo = '.(isset($this->calc_afpriesgo)?$this->calc_afpriesgo:"null").',';
		$sql .= ' calc_aportsol = '.(isset($this->calc_aportsol)?$this->calc_aportsol:"null").',';
		$sql .= ' calc_quin = '.(isset($this->calc_quin)?$this->calc_quin:"null").',';
		$sql .= ' print = '.(isset($this->print)?$this->print:"null").',';
		$sql .= ' print_input = '.(isset($this->print_input)?$this->print_input:"null").',';
		$sql .= ' fk_codfol = '.(isset($this->fk_codfol)?$this->fk_codfol:"null").',';
		$sql .= ' contab_account_ref = '.(isset($this->contab_account_ref)?"'".$this->db->escape($this->contab_account_ref)."'":"null").',';
		$sql .= ' income_tax = '.(isset($this->income_tax)?$this->income_tax:"null").',';
		$sql .= ' percent = '.(isset($this->percent)?$this->percent:"null");


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
		$object = new Pconcept($this->db);

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

        $label = '<u>' . $langs->trans("MyModule") . '</u>';
        $label.= '<br>';
        $label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

        $url = DOL_URL_ROOT.'/salary/'.$this->table_name.'_card.php?id='.$this->id;

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
            $result.=($linkstart.img_object(($notooltip?'':$label), 'label', ($notooltip?'':'class="classfortooltip"')).$linkend);
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
	function getLibStatut($mode=0)
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
	static function LibStatut($status,$mode=0)
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
		$this->detail = '';
		$this->details = '';
		$this->type_cod = '';
		$this->type_mov = '';
		$this->ref_formula = '';
		$this->wage_inf = '';
		$this->calc_oblig = '';
		$this->calc_afp = '';
		$this->calc_rciva = '';
		$this->calc_agui = '';
		$this->calc_vac = '';
		$this->calc_indem = '';
		$this->calc_afpvejez = '';
		$this->calc_contrpat = '';
		$this->calc_afpriesgo = '';
		$this->calc_aportsol = '';
		$this->calc_quin = '';
		$this->print = '';
		$this->print_input = '';
		$this->fk_codfol = '';
		$this->contab_account_ref = '';
		$this->income_tax = '';
		$this->percent = '';


	}

}

/**
 * Class PconceptLine
 */
class PconceptLine
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
	public $detail;
	public $details;
	public $type_cod;
	public $type_mov;
	public $ref_formula;
	public $wage_inf;
	public $calc_oblig;
	public $calc_afp;
	public $calc_rciva;
	public $calc_agui;
	public $calc_vac;
	public $calc_indem;
	public $calc_afpvejez;
	public $calc_contrpat;
	public $calc_afpriesgo;
	public $calc_aportsol;
	public $calc_quin;
	public $print;
	public $print_input;
	public $fk_codfol;
	public $contab_account_ref;
	public $income_tax;
	public $percent;

	/**
	 * @var mixed Sample line property 2
	 */

}
