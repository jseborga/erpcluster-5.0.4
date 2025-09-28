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
 * \file    poa/poapoa.class.php
 * \ingroup poa
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Poapoa
 *
 * Put here description of your class
 *
 * @see CommonObject
 */
class Poapoa extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'poapoa';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'poa_poa';

	/**
	 * @var PoapoaLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	
	public $entity;
	public $gestion;
	public $fk_structure;
	public $ref;
	public $sigla;
	public $label;
	public $pseudonym;
	public $partida;
	public $amount;
	public $classification;
	public $source_verification;
	public $unit;
	public $responsible_one;
	public $responsible_two;
	public $responsible;
	public $m_jan;
	public $m_feb;
	public $m_mar;
	public $m_apr;
	public $m_may;
	public $m_jun;
	public $m_jul;
	public $m_aug;
	public $m_sep;
	public $m_oct;
	public $m_nov;
	public $m_dec;
	public $p_jan;
	public $p_feb;
	public $p_mar;
	public $p_apr;
	public $p_may;
	public $p_jun;
	public $p_jul;
	public $p_aug;
	public $p_sep;
	public $p_oct;
	public $p_nov;
	public $p_dec;
	public $fk_area;
	public $weighting;
	public $fk_poa_reformulated;
	public $version;
	public $fk_user_create;
	public $fk_user_mod;
	public $datec = '';
	public $datem = '';
	public $tms = '';
	public $statut;
	public $statut_ref;

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
		if (isset($this->gestion)) {
			 $this->gestion = trim($this->gestion);
		}
		if (isset($this->fk_structure)) {
			 $this->fk_structure = trim($this->fk_structure);
		}
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->sigla)) {
			 $this->sigla = trim($this->sigla);
		}
		if (isset($this->label)) {
			 $this->label = trim($this->label);
		}
		if (isset($this->pseudonym)) {
			 $this->pseudonym = trim($this->pseudonym);
		}
		if (isset($this->partida)) {
			 $this->partida = trim($this->partida);
		}
		if (isset($this->amount)) {
			 $this->amount = trim($this->amount);
		}
		if (isset($this->classification)) {
			 $this->classification = trim($this->classification);
		}
		if (isset($this->source_verification)) {
			 $this->source_verification = trim($this->source_verification);
		}
		if (isset($this->unit)) {
			 $this->unit = trim($this->unit);
		}
		if (isset($this->responsible_one)) {
			 $this->responsible_one = trim($this->responsible_one);
		}
		if (isset($this->responsible_two)) {
			 $this->responsible_two = trim($this->responsible_two);
		}
		if (isset($this->responsible)) {
			 $this->responsible = trim($this->responsible);
		}
		if (isset($this->m_jan)) {
			 $this->m_jan = trim($this->m_jan);
		}
		if (isset($this->m_feb)) {
			 $this->m_feb = trim($this->m_feb);
		}
		if (isset($this->m_mar)) {
			 $this->m_mar = trim($this->m_mar);
		}
		if (isset($this->m_apr)) {
			 $this->m_apr = trim($this->m_apr);
		}
		if (isset($this->m_may)) {
			 $this->m_may = trim($this->m_may);
		}
		if (isset($this->m_jun)) {
			 $this->m_jun = trim($this->m_jun);
		}
		if (isset($this->m_jul)) {
			 $this->m_jul = trim($this->m_jul);
		}
		if (isset($this->m_aug)) {
			 $this->m_aug = trim($this->m_aug);
		}
		if (isset($this->m_sep)) {
			 $this->m_sep = trim($this->m_sep);
		}
		if (isset($this->m_oct)) {
			 $this->m_oct = trim($this->m_oct);
		}
		if (isset($this->m_nov)) {
			 $this->m_nov = trim($this->m_nov);
		}
		if (isset($this->m_dec)) {
			 $this->m_dec = trim($this->m_dec);
		}
		if (isset($this->p_jan)) {
			 $this->p_jan = trim($this->p_jan);
		}
		if (isset($this->p_feb)) {
			 $this->p_feb = trim($this->p_feb);
		}
		if (isset($this->p_mar)) {
			 $this->p_mar = trim($this->p_mar);
		}
		if (isset($this->p_apr)) {
			 $this->p_apr = trim($this->p_apr);
		}
		if (isset($this->p_may)) {
			 $this->p_may = trim($this->p_may);
		}
		if (isset($this->p_jun)) {
			 $this->p_jun = trim($this->p_jun);
		}
		if (isset($this->p_jul)) {
			 $this->p_jul = trim($this->p_jul);
		}
		if (isset($this->p_aug)) {
			 $this->p_aug = trim($this->p_aug);
		}
		if (isset($this->p_sep)) {
			 $this->p_sep = trim($this->p_sep);
		}
		if (isset($this->p_oct)) {
			 $this->p_oct = trim($this->p_oct);
		}
		if (isset($this->p_nov)) {
			 $this->p_nov = trim($this->p_nov);
		}
		if (isset($this->p_dec)) {
			 $this->p_dec = trim($this->p_dec);
		}
		if (isset($this->fk_area)) {
			 $this->fk_area = trim($this->fk_area);
		}
		if (isset($this->weighting)) {
			 $this->weighting = trim($this->weighting);
		}
		if (isset($this->fk_poa_reformulated)) {
			 $this->fk_poa_reformulated = trim($this->fk_poa_reformulated);
		}
		if (isset($this->version)) {
			 $this->version = trim($this->version);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_mod)) {
			 $this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->statut)) {
			 $this->statut = trim($this->statut);
		}
		if (isset($this->statut_ref)) {
			 $this->statut_ref = trim($this->statut_ref);
		}

		

		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';
		
		$sql.= 'entity,';
		$sql.= 'gestion,';
		$sql.= 'fk_structure,';
		$sql.= 'ref,';
		$sql.= 'sigla,';
		$sql.= 'label,';
		$sql.= 'pseudonym,';
		$sql.= 'partida,';
		$sql.= 'amount,';
		$sql.= 'classification,';
		$sql.= 'source_verification,';
		$sql.= 'unit,';
		$sql.= 'responsible_one,';
		$sql.= 'responsible_two,';
		$sql.= 'responsible,';
		$sql.= 'm_jan,';
		$sql.= 'm_feb,';
		$sql.= 'm_mar,';
		$sql.= 'm_apr,';
		$sql.= 'm_may,';
		$sql.= 'm_jun,';
		$sql.= 'm_jul,';
		$sql.= 'm_aug,';
		$sql.= 'm_sep,';
		$sql.= 'm_oct,';
		$sql.= 'm_nov,';
		$sql.= 'm_dec,';
		$sql.= 'p_jan,';
		$sql.= 'p_feb,';
		$sql.= 'p_mar,';
		$sql.= 'p_apr,';
		$sql.= 'p_may,';
		$sql.= 'p_jun,';
		$sql.= 'p_jul,';
		$sql.= 'p_aug,';
		$sql.= 'p_sep,';
		$sql.= 'p_oct,';
		$sql.= 'p_nov,';
		$sql.= 'p_dec,';
		$sql.= 'fk_area,';
		$sql.= 'weighting,';
		$sql.= 'fk_poa_reformulated,';
		$sql.= 'version,';
		$sql.= 'fk_user_create,';
		$sql.= 'fk_user_mod,';
		$sql.= 'datec,';
		$sql.= 'datem,';
		$sql.= 'statut,';
		$sql.= 'statut_ref';

		
		$sql .= ') VALUES (';
		
		$sql .= ' '.(! isset($this->entity)?'NULL':$this->entity).',';
		$sql .= ' '.(! isset($this->gestion)?'NULL':$this->gestion).',';
		$sql .= ' '.(! isset($this->fk_structure)?'NULL':$this->fk_structure).',';
		$sql .= ' '.(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql .= ' '.(! isset($this->sigla)?'NULL':"'".$this->db->escape($this->sigla)."'").',';
		$sql .= ' '.(! isset($this->label)?'NULL':"'".$this->db->escape($this->label)."'").',';
		$sql .= ' '.(! isset($this->pseudonym)?'NULL':"'".$this->db->escape($this->pseudonym)."'").',';
		$sql .= ' '.(! isset($this->partida)?'NULL':"'".$this->db->escape($this->partida)."'").',';
		$sql .= ' '.(! isset($this->amount)?'NULL':"'".$this->amount."'").',';
		$sql .= ' '.(! isset($this->classification)?'NULL':"'".$this->db->escape($this->classification)."'").',';
		$sql .= ' '.(! isset($this->source_verification)?'NULL':"'".$this->db->escape($this->source_verification)."'").',';
		$sql .= ' '.(! isset($this->unit)?'NULL':"'".$this->db->escape($this->unit)."'").',';
		$sql .= ' '.(! isset($this->responsible_one)?'NULL':"'".$this->db->escape($this->responsible_one)."'").',';
		$sql .= ' '.(! isset($this->responsible_two)?'NULL':"'".$this->db->escape($this->responsible_two)."'").',';
		$sql .= ' '.(! isset($this->responsible)?'NULL':"'".$this->db->escape($this->responsible)."'").',';
		$sql .= ' '.(! isset($this->m_jan)?'NULL':"'".$this->m_jan."'").',';
		$sql .= ' '.(! isset($this->m_feb)?'NULL':"'".$this->m_feb."'").',';
		$sql .= ' '.(! isset($this->m_mar)?'NULL':"'".$this->m_mar."'").',';
		$sql .= ' '.(! isset($this->m_apr)?'NULL':"'".$this->m_apr."'").',';
		$sql .= ' '.(! isset($this->m_may)?'NULL':"'".$this->m_may."'").',';
		$sql .= ' '.(! isset($this->m_jun)?'NULL':"'".$this->m_jun."'").',';
		$sql .= ' '.(! isset($this->m_jul)?'NULL':"'".$this->m_jul."'").',';
		$sql .= ' '.(! isset($this->m_aug)?'NULL':"'".$this->m_aug."'").',';
		$sql .= ' '.(! isset($this->m_sep)?'NULL':"'".$this->m_sep."'").',';
		$sql .= ' '.(! isset($this->m_oct)?'NULL':"'".$this->m_oct."'").',';
		$sql .= ' '.(! isset($this->m_nov)?'NULL':"'".$this->m_nov."'").',';
		$sql .= ' '.(! isset($this->m_dec)?'NULL':"'".$this->m_dec."'").',';
		$sql .= ' '.(! isset($this->p_jan)?'NULL':"'".$this->p_jan."'").',';
		$sql .= ' '.(! isset($this->p_feb)?'NULL':"'".$this->p_feb."'").',';
		$sql .= ' '.(! isset($this->p_mar)?'NULL':"'".$this->p_mar."'").',';
		$sql .= ' '.(! isset($this->p_apr)?'NULL':"'".$this->p_apr."'").',';
		$sql .= ' '.(! isset($this->p_may)?'NULL':"'".$this->p_may."'").',';
		$sql .= ' '.(! isset($this->p_jun)?'NULL':"'".$this->p_jun."'").',';
		$sql .= ' '.(! isset($this->p_jul)?'NULL':"'".$this->p_jul."'").',';
		$sql .= ' '.(! isset($this->p_aug)?'NULL':"'".$this->p_aug."'").',';
		$sql .= ' '.(! isset($this->p_sep)?'NULL':"'".$this->p_sep."'").',';
		$sql .= ' '.(! isset($this->p_oct)?'NULL':"'".$this->p_oct."'").',';
		$sql .= ' '.(! isset($this->p_nov)?'NULL':"'".$this->p_nov."'").',';
		$sql .= ' '.(! isset($this->p_dec)?'NULL':"'".$this->p_dec."'").',';
		$sql .= ' '.(! isset($this->fk_area)?'NULL':$this->fk_area).',';
		$sql .= ' '.(! isset($this->weighting)?'NULL':"'".$this->weighting."'").',';
		$sql .= ' '.(! isset($this->fk_poa_reformulated)?'NULL':$this->fk_poa_reformulated).',';
		$sql .= ' '.(! isset($this->version)?'NULL':$this->version).',';
		$sql .= ' '.(! isset($this->fk_user_create)?'NULL':$this->fk_user_create).',';
		$sql .= ' '.$user->id.',';
		$sql .= ' '."'".$this->db->idate(dol_now())."'".',';
		$sql .= ' '.(! isset($this->datem) || dol_strlen($this->datem)==0?'NULL':"'".$this->db->idate($this->datem)."'").',';
		$sql .= ' '.(! isset($this->statut)?'NULL':$this->statut).',';
		$sql .= ' '.(! isset($this->statut_ref)?'NULL':$this->statut_ref);

		
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
		$sql .= " t.gestion,";
		$sql .= " t.fk_structure,";
		$sql .= " t.ref,";
		$sql .= " t.sigla,";
		$sql .= " t.label,";
		$sql .= " t.pseudonym,";
		$sql .= " t.partida,";
		$sql .= " t.amount,";
		$sql .= " t.classification,";
		$sql .= " t.source_verification,";
		$sql .= " t.unit,";
		$sql .= " t.responsible_one,";
		$sql .= " t.responsible_two,";
		$sql .= " t.responsible,";
		$sql .= " t.m_jan,";
		$sql .= " t.m_feb,";
		$sql .= " t.m_mar,";
		$sql .= " t.m_apr,";
		$sql .= " t.m_may,";
		$sql .= " t.m_jun,";
		$sql .= " t.m_jul,";
		$sql .= " t.m_aug,";
		$sql .= " t.m_sep,";
		$sql .= " t.m_oct,";
		$sql .= " t.m_nov,";
		$sql .= " t.m_dec,";
		$sql .= " t.p_jan,";
		$sql .= " t.p_feb,";
		$sql .= " t.p_mar,";
		$sql .= " t.p_apr,";
		$sql .= " t.p_may,";
		$sql .= " t.p_jun,";
		$sql .= " t.p_jul,";
		$sql .= " t.p_aug,";
		$sql .= " t.p_sep,";
		$sql .= " t.p_oct,";
		$sql .= " t.p_nov,";
		$sql .= " t.p_dec,";
		$sql .= " t.fk_area,";
		$sql .= " t.weighting,";
		$sql .= " t.fk_poa_reformulated,";
		$sql .= " t.version,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.datec,";
		$sql .= " t.datem,";
		$sql .= " t.tms,";
		$sql .= " t.statut,";
		$sql .= " t.statut_ref";

		
		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
		    $sql .= " AND entity IN (" . getEntity("poapoa", 1) . ")";
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
				$this->gestion = $obj->gestion;
				$this->fk_structure = $obj->fk_structure;
				$this->ref = $obj->ref;
				$this->sigla = $obj->sigla;
				$this->label = $obj->label;
				$this->pseudonym = $obj->pseudonym;
				$this->partida = $obj->partida;
				$this->amount = $obj->amount;
				$this->classification = $obj->classification;
				$this->source_verification = $obj->source_verification;
				$this->unit = $obj->unit;
				$this->responsible_one = $obj->responsible_one;
				$this->responsible_two = $obj->responsible_two;
				$this->responsible = $obj->responsible;
				$this->m_jan = $obj->m_jan;
				$this->m_feb = $obj->m_feb;
				$this->m_mar = $obj->m_mar;
				$this->m_apr = $obj->m_apr;
				$this->m_may = $obj->m_may;
				$this->m_jun = $obj->m_jun;
				$this->m_jul = $obj->m_jul;
				$this->m_aug = $obj->m_aug;
				$this->m_sep = $obj->m_sep;
				$this->m_oct = $obj->m_oct;
				$this->m_nov = $obj->m_nov;
				$this->m_dec = $obj->m_dec;
				$this->p_jan = $obj->p_jan;
				$this->p_feb = $obj->p_feb;
				$this->p_mar = $obj->p_mar;
				$this->p_apr = $obj->p_apr;
				$this->p_may = $obj->p_may;
				$this->p_jun = $obj->p_jun;
				$this->p_jul = $obj->p_jul;
				$this->p_aug = $obj->p_aug;
				$this->p_sep = $obj->p_sep;
				$this->p_oct = $obj->p_oct;
				$this->p_nov = $obj->p_nov;
				$this->p_dec = $obj->p_dec;
				$this->fk_area = $obj->fk_area;
				$this->weighting = $obj->weighting;
				$this->fk_poa_reformulated = $obj->fk_poa_reformulated;
				$this->version = $obj->version;
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_mod = $obj->fk_user_mod;
				$this->datec = $this->db->jdate($obj->datec);
				$this->datem = $this->db->jdate($obj->datem);
				$this->tms = $this->db->jdate($obj->tms);
				$this->statut = $obj->statut;
				$this->statut_ref = $obj->statut_ref;

				
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
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND')
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		
		$sql .= " t.entity,";
		$sql .= " t.gestion,";
		$sql .= " t.fk_structure,";
		$sql .= " t.ref,";
		$sql .= " t.sigla,";
		$sql .= " t.label,";
		$sql .= " t.pseudonym,";
		$sql .= " t.partida,";
		$sql .= " t.amount,";
		$sql .= " t.classification,";
		$sql .= " t.source_verification,";
		$sql .= " t.unit,";
		$sql .= " t.responsible_one,";
		$sql .= " t.responsible_two,";
		$sql .= " t.responsible,";
		$sql .= " t.m_jan,";
		$sql .= " t.m_feb,";
		$sql .= " t.m_mar,";
		$sql .= " t.m_apr,";
		$sql .= " t.m_may,";
		$sql .= " t.m_jun,";
		$sql .= " t.m_jul,";
		$sql .= " t.m_aug,";
		$sql .= " t.m_sep,";
		$sql .= " t.m_oct,";
		$sql .= " t.m_nov,";
		$sql .= " t.m_dec,";
		$sql .= " t.p_jan,";
		$sql .= " t.p_feb,";
		$sql .= " t.p_mar,";
		$sql .= " t.p_apr,";
		$sql .= " t.p_may,";
		$sql .= " t.p_jun,";
		$sql .= " t.p_jul,";
		$sql .= " t.p_aug,";
		$sql .= " t.p_sep,";
		$sql .= " t.p_oct,";
		$sql .= " t.p_nov,";
		$sql .= " t.p_dec,";
		$sql .= " t.fk_area,";
		$sql .= " t.weighting,";
		$sql .= " t.fk_poa_reformulated,";
		$sql .= " t.version,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.datec,";
		$sql .= " t.datem,";
		$sql .= " t.tms,";
		$sql .= " t.statut,";
		$sql .= " t.statut_ref";

		
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
		    $sql .= " AND entity IN (" . getEntity("poapoa", 1) . ")";
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' AND ' . implode(' '.$filtermode.' ', $sqlwhere);
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
				$line = new PoapoaLine();

				$line->id = $obj->rowid;
				
				$line->entity = $obj->entity;
				$line->gestion = $obj->gestion;
				$line->fk_structure = $obj->fk_structure;
				$line->ref = $obj->ref;
				$line->sigla = $obj->sigla;
				$line->label = $obj->label;
				$line->pseudonym = $obj->pseudonym;
				$line->partida = $obj->partida;
				$line->amount = $obj->amount;
				$line->classification = $obj->classification;
				$line->source_verification = $obj->source_verification;
				$line->unit = $obj->unit;
				$line->responsible_one = $obj->responsible_one;
				$line->responsible_two = $obj->responsible_two;
				$line->responsible = $obj->responsible;
				$line->m_jan = $obj->m_jan;
				$line->m_feb = $obj->m_feb;
				$line->m_mar = $obj->m_mar;
				$line->m_apr = $obj->m_apr;
				$line->m_may = $obj->m_may;
				$line->m_jun = $obj->m_jun;
				$line->m_jul = $obj->m_jul;
				$line->m_aug = $obj->m_aug;
				$line->m_sep = $obj->m_sep;
				$line->m_oct = $obj->m_oct;
				$line->m_nov = $obj->m_nov;
				$line->m_dec = $obj->m_dec;
				$line->p_jan = $obj->p_jan;
				$line->p_feb = $obj->p_feb;
				$line->p_mar = $obj->p_mar;
				$line->p_apr = $obj->p_apr;
				$line->p_may = $obj->p_may;
				$line->p_jun = $obj->p_jun;
				$line->p_jul = $obj->p_jul;
				$line->p_aug = $obj->p_aug;
				$line->p_sep = $obj->p_sep;
				$line->p_oct = $obj->p_oct;
				$line->p_nov = $obj->p_nov;
				$line->p_dec = $obj->p_dec;
				$line->fk_area = $obj->fk_area;
				$line->weighting = $obj->weighting;
				$line->fk_poa_reformulated = $obj->fk_poa_reformulated;
				$line->version = $obj->version;
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->datec = $this->db->jdate($obj->datec);
				$line->datem = $this->db->jdate($obj->datem);
				$line->tms = $this->db->jdate($obj->tms);
				$line->statut = $obj->statut;
				$line->statut_ref = $obj->statut_ref;

				

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
		if (isset($this->gestion)) {
			 $this->gestion = trim($this->gestion);
		}
		if (isset($this->fk_structure)) {
			 $this->fk_structure = trim($this->fk_structure);
		}
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->sigla)) {
			 $this->sigla = trim($this->sigla);
		}
		if (isset($this->label)) {
			 $this->label = trim($this->label);
		}
		if (isset($this->pseudonym)) {
			 $this->pseudonym = trim($this->pseudonym);
		}
		if (isset($this->partida)) {
			 $this->partida = trim($this->partida);
		}
		if (isset($this->amount)) {
			 $this->amount = trim($this->amount);
		}
		if (isset($this->classification)) {
			 $this->classification = trim($this->classification);
		}
		if (isset($this->source_verification)) {
			 $this->source_verification = trim($this->source_verification);
		}
		if (isset($this->unit)) {
			 $this->unit = trim($this->unit);
		}
		if (isset($this->responsible_one)) {
			 $this->responsible_one = trim($this->responsible_one);
		}
		if (isset($this->responsible_two)) {
			 $this->responsible_two = trim($this->responsible_two);
		}
		if (isset($this->responsible)) {
			 $this->responsible = trim($this->responsible);
		}
		if (isset($this->m_jan)) {
			 $this->m_jan = trim($this->m_jan);
		}
		if (isset($this->m_feb)) {
			 $this->m_feb = trim($this->m_feb);
		}
		if (isset($this->m_mar)) {
			 $this->m_mar = trim($this->m_mar);
		}
		if (isset($this->m_apr)) {
			 $this->m_apr = trim($this->m_apr);
		}
		if (isset($this->m_may)) {
			 $this->m_may = trim($this->m_may);
		}
		if (isset($this->m_jun)) {
			 $this->m_jun = trim($this->m_jun);
		}
		if (isset($this->m_jul)) {
			 $this->m_jul = trim($this->m_jul);
		}
		if (isset($this->m_aug)) {
			 $this->m_aug = trim($this->m_aug);
		}
		if (isset($this->m_sep)) {
			 $this->m_sep = trim($this->m_sep);
		}
		if (isset($this->m_oct)) {
			 $this->m_oct = trim($this->m_oct);
		}
		if (isset($this->m_nov)) {
			 $this->m_nov = trim($this->m_nov);
		}
		if (isset($this->m_dec)) {
			 $this->m_dec = trim($this->m_dec);
		}
		if (isset($this->p_jan)) {
			 $this->p_jan = trim($this->p_jan);
		}
		if (isset($this->p_feb)) {
			 $this->p_feb = trim($this->p_feb);
		}
		if (isset($this->p_mar)) {
			 $this->p_mar = trim($this->p_mar);
		}
		if (isset($this->p_apr)) {
			 $this->p_apr = trim($this->p_apr);
		}
		if (isset($this->p_may)) {
			 $this->p_may = trim($this->p_may);
		}
		if (isset($this->p_jun)) {
			 $this->p_jun = trim($this->p_jun);
		}
		if (isset($this->p_jul)) {
			 $this->p_jul = trim($this->p_jul);
		}
		if (isset($this->p_aug)) {
			 $this->p_aug = trim($this->p_aug);
		}
		if (isset($this->p_sep)) {
			 $this->p_sep = trim($this->p_sep);
		}
		if (isset($this->p_oct)) {
			 $this->p_oct = trim($this->p_oct);
		}
		if (isset($this->p_nov)) {
			 $this->p_nov = trim($this->p_nov);
		}
		if (isset($this->p_dec)) {
			 $this->p_dec = trim($this->p_dec);
		}
		if (isset($this->fk_area)) {
			 $this->fk_area = trim($this->fk_area);
		}
		if (isset($this->weighting)) {
			 $this->weighting = trim($this->weighting);
		}
		if (isset($this->fk_poa_reformulated)) {
			 $this->fk_poa_reformulated = trim($this->fk_poa_reformulated);
		}
		if (isset($this->version)) {
			 $this->version = trim($this->version);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->fk_user_mod)) {
			 $this->fk_user_mod = trim($this->fk_user_mod);
		}
		if (isset($this->statut)) {
			 $this->statut = trim($this->statut);
		}
		if (isset($this->statut_ref)) {
			 $this->statut_ref = trim($this->statut_ref);
		}

		

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';
		
		$sql .= ' entity = '.(isset($this->entity)?$this->entity:"null").',';
		$sql .= ' gestion = '.(isset($this->gestion)?$this->gestion:"null").',';
		$sql .= ' fk_structure = '.(isset($this->fk_structure)?$this->fk_structure:"null").',';
		$sql .= ' ref = '.(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").',';
		$sql .= ' sigla = '.(isset($this->sigla)?"'".$this->db->escape($this->sigla)."'":"null").',';
		$sql .= ' label = '.(isset($this->label)?"'".$this->db->escape($this->label)."'":"null").',';
		$sql .= ' pseudonym = '.(isset($this->pseudonym)?"'".$this->db->escape($this->pseudonym)."'":"null").',';
		$sql .= ' partida = '.(isset($this->partida)?"'".$this->db->escape($this->partida)."'":"null").',';
		$sql .= ' amount = '.(isset($this->amount)?$this->amount:"null").',';
		$sql .= ' classification = '.(isset($this->classification)?"'".$this->db->escape($this->classification)."'":"null").',';
		$sql .= ' source_verification = '.(isset($this->source_verification)?"'".$this->db->escape($this->source_verification)."'":"null").',';
		$sql .= ' unit = '.(isset($this->unit)?"'".$this->db->escape($this->unit)."'":"null").',';
		$sql .= ' responsible_one = '.(isset($this->responsible_one)?"'".$this->db->escape($this->responsible_one)."'":"null").',';
		$sql .= ' responsible_two = '.(isset($this->responsible_two)?"'".$this->db->escape($this->responsible_two)."'":"null").',';
		$sql .= ' responsible = '.(isset($this->responsible)?"'".$this->db->escape($this->responsible)."'":"null").',';
		$sql .= ' m_jan = '.(isset($this->m_jan)?$this->m_jan:"null").',';
		$sql .= ' m_feb = '.(isset($this->m_feb)?$this->m_feb:"null").',';
		$sql .= ' m_mar = '.(isset($this->m_mar)?$this->m_mar:"null").',';
		$sql .= ' m_apr = '.(isset($this->m_apr)?$this->m_apr:"null").',';
		$sql .= ' m_may = '.(isset($this->m_may)?$this->m_may:"null").',';
		$sql .= ' m_jun = '.(isset($this->m_jun)?$this->m_jun:"null").',';
		$sql .= ' m_jul = '.(isset($this->m_jul)?$this->m_jul:"null").',';
		$sql .= ' m_aug = '.(isset($this->m_aug)?$this->m_aug:"null").',';
		$sql .= ' m_sep = '.(isset($this->m_sep)?$this->m_sep:"null").',';
		$sql .= ' m_oct = '.(isset($this->m_oct)?$this->m_oct:"null").',';
		$sql .= ' m_nov = '.(isset($this->m_nov)?$this->m_nov:"null").',';
		$sql .= ' m_dec = '.(isset($this->m_dec)?$this->m_dec:"null").',';
		$sql .= ' p_jan = '.(isset($this->p_jan)?$this->p_jan:"null").',';
		$sql .= ' p_feb = '.(isset($this->p_feb)?$this->p_feb:"null").',';
		$sql .= ' p_mar = '.(isset($this->p_mar)?$this->p_mar:"null").',';
		$sql .= ' p_apr = '.(isset($this->p_apr)?$this->p_apr:"null").',';
		$sql .= ' p_may = '.(isset($this->p_may)?$this->p_may:"null").',';
		$sql .= ' p_jun = '.(isset($this->p_jun)?$this->p_jun:"null").',';
		$sql .= ' p_jul = '.(isset($this->p_jul)?$this->p_jul:"null").',';
		$sql .= ' p_aug = '.(isset($this->p_aug)?$this->p_aug:"null").',';
		$sql .= ' p_sep = '.(isset($this->p_sep)?$this->p_sep:"null").',';
		$sql .= ' p_oct = '.(isset($this->p_oct)?$this->p_oct:"null").',';
		$sql .= ' p_nov = '.(isset($this->p_nov)?$this->p_nov:"null").',';
		$sql .= ' p_dec = '.(isset($this->p_dec)?$this->p_dec:"null").',';
		$sql .= ' fk_area = '.(isset($this->fk_area)?$this->fk_area:"null").',';
		$sql .= ' weighting = '.(isset($this->weighting)?$this->weighting:"null").',';
		$sql .= ' fk_poa_reformulated = '.(isset($this->fk_poa_reformulated)?$this->fk_poa_reformulated:"null").',';
		$sql .= ' version = '.(isset($this->version)?$this->version:"null").',';
		$sql .= ' fk_user_create = '.(isset($this->fk_user_create)?$this->fk_user_create:"null").',';
		$sql .= ' fk_user_mod = '.$user->id.',';
		$sql .= ' datec = '.(! isset($this->datec) || dol_strlen($this->datec) != 0 ? "'".$this->db->idate($this->datec)."'" : 'null').',';
		$sql .= ' datem = '.(! isset($this->datem) || dol_strlen($this->datem) != 0 ? "'".$this->db->idate($this->datem)."'" : 'null').',';
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
		$sql .= ' statut = '.(isset($this->statut)?$this->statut:"null").',';
		$sql .= ' statut_ref = '.(isset($this->statut_ref)?$this->statut_ref:"null");

        
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
		$object = new Poapoa($this->db);

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

        $url = DOL_URL_ROOT.'/poa/'.$this->table_name.'_card.php?id='.$this->id;
        
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
		$this->gestion = '';
		$this->fk_structure = '';
		$this->ref = '';
		$this->sigla = '';
		$this->label = '';
		$this->pseudonym = '';
		$this->partida = '';
		$this->amount = '';
		$this->classification = '';
		$this->source_verification = '';
		$this->unit = '';
		$this->responsible_one = '';
		$this->responsible_two = '';
		$this->responsible = '';
		$this->m_jan = '';
		$this->m_feb = '';
		$this->m_mar = '';
		$this->m_apr = '';
		$this->m_may = '';
		$this->m_jun = '';
		$this->m_jul = '';
		$this->m_aug = '';
		$this->m_sep = '';
		$this->m_oct = '';
		$this->m_nov = '';
		$this->m_dec = '';
		$this->p_jan = '';
		$this->p_feb = '';
		$this->p_mar = '';
		$this->p_apr = '';
		$this->p_may = '';
		$this->p_jun = '';
		$this->p_jul = '';
		$this->p_aug = '';
		$this->p_sep = '';
		$this->p_oct = '';
		$this->p_nov = '';
		$this->p_dec = '';
		$this->fk_area = '';
		$this->weighting = '';
		$this->fk_poa_reformulated = '';
		$this->version = '';
		$this->fk_user_create = '';
		$this->fk_user_mod = '';
		$this->datec = '';
		$this->datem = '';
		$this->tms = '';
		$this->statut = '';
		$this->statut_ref = '';

		
	}

}

/**
 * Class PoapoaLine
 */
class PoapoaLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	
	public $entity;
	public $gestion;
	public $fk_structure;
	public $ref;
	public $sigla;
	public $label;
	public $pseudonym;
	public $partida;
	public $amount;
	public $classification;
	public $source_verification;
	public $unit;
	public $responsible_one;
	public $responsible_two;
	public $responsible;
	public $m_jan;
	public $m_feb;
	public $m_mar;
	public $m_apr;
	public $m_may;
	public $m_jun;
	public $m_jul;
	public $m_aug;
	public $m_sep;
	public $m_oct;
	public $m_nov;
	public $m_dec;
	public $p_jan;
	public $p_feb;
	public $p_mar;
	public $p_apr;
	public $p_may;
	public $p_jun;
	public $p_jul;
	public $p_aug;
	public $p_sep;
	public $p_oct;
	public $p_nov;
	public $p_dec;
	public $fk_area;
	public $weighting;
	public $fk_poa_reformulated;
	public $version;
	public $fk_user_create;
	public $fk_user_mod;
	public $datec = '';
	public $datem = '';
	public $tms = '';
	public $statut;
	public $statut_ref;

	/**
	 * @var mixed Sample line property 2
	 */
	
}
