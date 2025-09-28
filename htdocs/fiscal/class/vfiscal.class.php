<?php
/* Copyright (C) 2007-2012  Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) 2014       Juanjo Menent       <jmenent@2byte.es>
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
 * \file    fiscal/vfiscal.class.php
 * \ingroup fiscal
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Vfiscal
 *
 * Put here description of your class
 * @see CommonObject
 */
class Vfiscal extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'vfiscal';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'v_fiscal';

	/**
	 * @var VfiscalLine[] Lines
	 */
	public $lines = array();

	/**
	 */

	public $entity;
	public $nfiscal;
	public $serie;
	public $fk_dosing;
	public $fk_facture;
	public $fk_cliepro;
	public $nit;
	public $razsoc;
	public $date_exp = '';
	public $type_op;
	public $num_autoriz;
	public $cod_control;
	public $baseimp1;
	public $baseimp2;
	public $baseimp3;
	public $baseimp4;
	public $baseimp5;
	public $aliqimp1;
	public $aliqimp2;
	public $aliqimp3;
	public $aliqimp4;
	public $aliqimp5;
	public $valimp1;
	public $valimp2;
	public $valimp3;
	public $valimp4;
	public $valimp5;
	public $valret1;
	public $valret2;
	public $valret3;
	public $valret4;
	public $valret5;
	public $amount_payment;
	public $amount_balance;
	public $date_create = '';
	public $date_mod = '';
	public $tms = '';
	public $fk_user_create;
	public $fk_user_mod = '';
	public $statut_print;
	public $status;

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
		if (isset($this->nfiscal)) {
			 $this->nfiscal = trim($this->nfiscal);
		}
		if (isset($this->serie)) {
			 $this->serie = trim($this->serie);
		}
		if (isset($this->fk_dosing)) {
			 $this->fk_dosing = trim($this->fk_dosing);
		}
		if (isset($this->fk_facture)) {
			 $this->fk_facture = trim($this->fk_facture);
		}
		if (isset($this->fk_cliepro)) {
			 $this->fk_cliepro = trim($this->fk_cliepro);
		}
		if (isset($this->nit)) {
			 $this->nit = trim($this->nit);
		}
		if (isset($this->razsoc)) {
			 $this->razsoc = trim($this->razsoc);
		}
		if (isset($this->type_op)) {
			 $this->type_op = trim($this->type_op);
		}
		if (isset($this->num_autoriz)) {
			 $this->num_autoriz = trim($this->num_autoriz);
		}
		if (isset($this->cod_control)) {
			 $this->cod_control = trim($this->cod_control);
		}
		if (isset($this->baseimp1)) {
			 $this->baseimp1 = trim($this->baseimp1);
		}
		if (isset($this->baseimp2)) {
			 $this->baseimp2 = trim($this->baseimp2);
		}
		if (isset($this->baseimp3)) {
			 $this->baseimp3 = trim($this->baseimp3);
		}
		if (isset($this->baseimp4)) {
			 $this->baseimp4 = trim($this->baseimp4);
		}
		if (isset($this->baseimp5)) {
			 $this->baseimp5 = trim($this->baseimp5);
		}
		if (isset($this->aliqimp1)) {
			 $this->aliqimp1 = trim($this->aliqimp1);
		}
		if (isset($this->aliqimp2)) {
			 $this->aliqimp2 = trim($this->aliqimp2);
		}
		if (isset($this->aliqimp3)) {
			 $this->aliqimp3 = trim($this->aliqimp3);
		}
		if (isset($this->aliqimp4)) {
			 $this->aliqimp4 = trim($this->aliqimp4);
		}
		if (isset($this->aliqimp5)) {
			 $this->aliqimp5 = trim($this->aliqimp5);
		}
		if (isset($this->valimp1)) {
			 $this->valimp1 = trim($this->valimp1);
		}
		if (isset($this->valimp2)) {
			 $this->valimp2 = trim($this->valimp2);
		}
		if (isset($this->valimp3)) {
			 $this->valimp3 = trim($this->valimp3);
		}
		if (isset($this->valimp4)) {
			 $this->valimp4 = trim($this->valimp4);
		}
		if (isset($this->valimp5)) {
			 $this->valimp5 = trim($this->valimp5);
		}
		if (isset($this->valret1)) {
			 $this->valret1 = trim($this->valret1);
		}
		if (isset($this->valret2)) {
			 $this->valret2 = trim($this->valret2);
		}
		if (isset($this->valret3)) {
			 $this->valret3 = trim($this->valret3);
		}
		if (isset($this->valret4)) {
			 $this->valret4 = trim($this->valret4);
		}
		if (isset($this->valret5)) {
			 $this->valret5 = trim($this->valret5);
		}
		if (isset($this->amount_payment)) {
			 $this->amount_payment = trim($this->amount_payment);
		}
		if (isset($this->amount_balance)) {
			 $this->amount_balance = trim($this->amount_balance);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->statut_print)) {
			 $this->statut_print = trim($this->statut_print);
		}
		if (isset($this->status)) {
			 $this->status = trim($this->status);
		}



		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';

		$sql.= 'entity,';
		$sql.= 'nfiscal,';
		$sql.= 'serie,';
		$sql.= 'fk_dosing,';
		$sql.= 'fk_facture,';
		$sql.= 'fk_cliepro,';
		$sql.= 'nit,';
		$sql.= 'razsoc,';
		$sql.= 'date_exp,';
		$sql.= 'type_op,';
		$sql.= 'num_autoriz,';
		$sql.= 'cod_control,';
		$sql.= 'baseimp1,';
		$sql.= 'baseimp2,';
		$sql.= 'baseimp3,';
		$sql.= 'baseimp4,';
		$sql.= 'baseimp5,';
		$sql.= 'aliqimp1,';
		$sql.= 'aliqimp2,';
		$sql.= 'aliqimp3,';
		$sql.= 'aliqimp4,';
		$sql.= 'aliqimp5,';
		$sql.= 'valimp1,';
		$sql.= 'valimp2,';
		$sql.= 'valimp3,';
		$sql.= 'valimp4,';
		$sql.= 'valimp5,';
		$sql.= 'valret1,';
		$sql.= 'valret2,';
		$sql.= 'valret3,';
		$sql.= 'valret4,';
		$sql.= 'valret5,';
		$sql.= 'amount_payment,';
		$sql.= 'amount_balance,';
		$sql.= 'date_create,';
		$sql.= 'date_mod,';
		$sql.= 'fk_user_create,';
		$sql.= 'fk_user_mod,';
		$sql.= 'statut_print,';
		$sql.= 'status';


		$sql .= ') VALUES (';

		$sql .= ' '.(! isset($this->entity)?'NULL':$this->entity).',';
		$sql .= ' '.(! isset($this->nfiscal)?'NULL':"'".$this->db->escape($this->nfiscal)."'").',';
		$sql .= ' '.(! isset($this->serie)?'NULL':"'".$this->db->escape($this->serie)."'").',';
		$sql .= ' '.(! isset($this->fk_dosing)?'NULL':$this->fk_dosing).',';
		$sql .= ' '.(! isset($this->fk_facture)?'NULL':$this->fk_facture).',';
		$sql .= ' '.(! isset($this->fk_cliepro)?'NULL':$this->fk_cliepro).',';
		$sql .= ' '.(! isset($this->nit)?'NULL':"'".$this->db->escape($this->nit)."'").',';
		$sql .= ' '.(! isset($this->razsoc)?'NULL':"'".$this->db->escape($this->razsoc)."'").',';
		$sql .= ' '.(! isset($this->date_exp) || dol_strlen($this->date_exp)==0?'NULL':"'".$this->db->idate($this->date_exp)."'").',';
		$sql .= ' '.(! isset($this->type_op)?'NULL':"'".$this->db->escape($this->type_op)."'").',';
		$sql .= ' '.(! isset($this->num_autoriz)?'NULL':"'".$this->db->escape($this->num_autoriz)."'").',';
		$sql .= ' '.(! isset($this->cod_control)?'NULL':"'".$this->db->escape($this->cod_control)."'").',';
		$sql .= ' '.(! isset($this->baseimp1)?'NULL':"'".$this->baseimp1."'").',';
		$sql .= ' '.(! isset($this->baseimp2)?'NULL':"'".$this->baseimp2."'").',';
		$sql .= ' '.(! isset($this->baseimp3)?'NULL':"'".$this->baseimp3."'").',';
		$sql .= ' '.(! isset($this->baseimp4)?'NULL':"'".$this->baseimp4."'").',';
		$sql .= ' '.(! isset($this->baseimp5)?'NULL':"'".$this->baseimp5."'").',';
		$sql .= ' '.(! isset($this->aliqimp1)?'NULL':"'".$this->aliqimp1."'").',';
		$sql .= ' '.(! isset($this->aliqimp2)?'NULL':"'".$this->aliqimp2."'").',';
		$sql .= ' '.(! isset($this->aliqimp3)?'NULL':"'".$this->aliqimp3."'").',';
		$sql .= ' '.(! isset($this->aliqimp4)?'NULL':"'".$this->aliqimp4."'").',';
		$sql .= ' '.(! isset($this->aliqimp5)?'NULL':"'".$this->aliqimp5."'").',';
		$sql .= ' '.(! isset($this->valimp1)?'NULL':"'".$this->valimp1."'").',';
		$sql .= ' '.(! isset($this->valimp2)?'NULL':"'".$this->valimp2."'").',';
		$sql .= ' '.(! isset($this->valimp3)?'NULL':"'".$this->valimp3."'").',';
		$sql .= ' '.(! isset($this->valimp4)?'NULL':"'".$this->valimp4."'").',';
		$sql .= ' '.(! isset($this->valimp5)?'NULL':"'".$this->valimp5."'").',';
		$sql .= ' '.(! isset($this->valret1)?'NULL':"'".$this->valret1."'").',';
		$sql .= ' '.(! isset($this->valret2)?'NULL':"'".$this->valret2."'").',';
		$sql .= ' '.(! isset($this->valret3)?'NULL':"'".$this->valret3."'").',';
		$sql .= ' '.(! isset($this->valret4)?'NULL':"'".$this->valret4."'").',';
		$sql .= ' '.(! isset($this->valret5)?'NULL':"'".$this->valret5."'").',';
		$sql .= ' '.(! isset($this->amount_payment)?'NULL':"'".$this->amount_payment."'").',';
		$sql .= ' '.(! isset($this->amount_balance)?'NULL':"'".$this->amount_balance."'").',';
		$sql .= ' '.(! isset($this->date_create) || dol_strlen($this->date_create)==0?'NULL':"'".$this->db->idate($this->date_create)."'").',';
		$sql .= ' '.(! isset($this->date_mod) || dol_strlen($this->date_mod)==0?'NULL':"'".$this->db->idate($this->date_mod)."'").',';
		$sql .= ' '.(! isset($this->fk_user_create)?'NULL':$this->fk_user_create).',';
		$sql .= ' '.(! isset($this->fk_user_mod)?'NULL':$this->fk_user_mod).',';
		$sql .= ' '.(! isset($this->statut_print)?'NULL':$this->statut_print).',';
		$sql .= ' '.(! isset($this->status)?'NULL':$this->status);


		$sql .= ')';

		$this->db->begin();

		$resql = $this->db->query($sql);
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
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
	public function fetch($id, $fk = 0)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.entity,";
		$sql .= " t.nfiscal,";
		$sql .= " t.serie,";
		$sql .= " t.fk_dosing,";
		$sql .= " t.fk_facture,";
		$sql .= " t.fk_cliepro,";
		$sql .= " t.nit,";
		$sql .= " t.razsoc,";
		$sql .= " t.date_exp,";
		$sql .= " t.type_op,";
		$sql .= " t.num_autoriz,";
		$sql .= " t.cod_control,";
		$sql .= " t.baseimp1,";
		$sql .= " t.baseimp2,";
		$sql .= " t.baseimp3,";
		$sql .= " t.baseimp4,";
		$sql .= " t.baseimp5,";
		$sql .= " t.aliqimp1,";
		$sql .= " t.aliqimp2,";
		$sql .= " t.aliqimp3,";
		$sql .= " t.aliqimp4,";
		$sql .= " t.aliqimp5,";
		$sql .= " t.valimp1,";
		$sql .= " t.valimp2,";
		$sql .= " t.valimp3,";
		$sql .= " t.valimp4,";
		$sql .= " t.valimp5,";
		$sql .= " t.valret1,";
		$sql .= " t.valret2,";
		$sql .= " t.valret3,";
		$sql .= " t.valret4,";
		$sql .= " t.valret5,";
		$sql .= " t.amount_payment,";
		$sql .= " t.amount_balance,";
		$sql .= " t.date_create,";
		$sql .= " t.date_mod,";
		$sql .= " t.tms,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.statut_print,";
		$sql .= " t.status";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		if ($fk>0) {
			$sql .= ' WHERE t.fk_facture = ' . $fk;
		} else {
			$sql .= ' WHERE t.rowid = ' . $id;
		}

		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			if ($numrows) {
				$obj = $this->db->fetch_object($resql);

				$this->id = $obj->rowid;

				$this->entity = $obj->entity;
				$this->nfiscal = $obj->nfiscal;
				$this->serie = $obj->serie;
				$this->fk_dosing = $obj->fk_dosing;
				$this->fk_facture = $obj->fk_facture;
				$this->fk_cliepro = $obj->fk_cliepro;
				$this->nit = $obj->nit;
				$this->razsoc = $obj->razsoc;
				$this->date_exp = $this->db->jdate($obj->date_exp);
				$this->type_op = $obj->type_op;
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
				$this->amount_payment = $obj->amount_payment;
				$this->amount_balance = $obj->amount_balance;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->date_mod = $this->db->jdate($obj->date_mod);
				$this->tms = $this->db->jdate($obj->tms);
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_mod = $obj->fk_user_mod;
				$this->statut_print = $obj->statut_print;
				$this->status = $obj->status;


			}
			$this->db->free($resql);

			if ($numrows) {
				return 1;
			} else {
				return 0;
			}
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

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
		$sql .= " t.nfiscal,";
		$sql .= " t.serie,";
		$sql .= " t.fk_dosing,";
		$sql .= " t.fk_facture,";
		$sql .= " t.fk_cliepro,";
		$sql .= " t.nit,";
		$sql .= " t.razsoc,";
		$sql .= " t.date_exp,";
		$sql .= " t.type_op,";
		$sql .= " t.num_autoriz,";
		$sql .= " t.cod_control,";
		$sql .= " t.baseimp1,";
		$sql .= " t.baseimp2,";
		$sql .= " t.baseimp3,";
		$sql .= " t.baseimp4,";
		$sql .= " t.baseimp5,";
		$sql .= " t.aliqimp1,";
		$sql .= " t.aliqimp2,";
		$sql .= " t.aliqimp3,";
		$sql .= " t.aliqimp4,";
		$sql .= " t.aliqimp5,";
		$sql .= " t.valimp1,";
		$sql .= " t.valimp2,";
		$sql .= " t.valimp3,";
		$sql .= " t.valimp4,";
		$sql .= " t.valimp5,";
		$sql .= " t.valret1,";
		$sql .= " t.valret2,";
		$sql .= " t.valret3,";
		$sql .= " t.valret4,";
		$sql .= " t.valret5,";
		$sql .= " t.amount_payment,";
		$sql .= " t.amount_balance,";
		$sql .= " t.date_create,";
		$sql .= " t.date_mod,";
		$sql .= " t.tms,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.statut_print,";
		$sql .= " t.status";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element. ' as t';

		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				$sqlwhere [] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
			}
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' WHERE ' . implode(' '.$filtermode.' ', $sqlwhere);
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
				$line = new VfiscalLine();

				$line->id = $obj->rowid;

				$line->entity = $obj->entity;
				$line->nfiscal = $obj->nfiscal;
				$line->serie = $obj->serie;
				$line->fk_dosing = $obj->fk_dosing;
				$line->fk_facture = $obj->fk_facture;
				$line->fk_cliepro = $obj->fk_cliepro;
				$line->nit = $obj->nit;
				$line->razsoc = $obj->razsoc;
				$line->date_exp = $this->db->jdate($obj->date_exp);
				$line->type_op = $obj->type_op;
				$line->num_autoriz = $obj->num_autoriz;
				$line->cod_control = $obj->cod_control;
				$line->baseimp1 = $obj->baseimp1;
				$line->baseimp2 = $obj->baseimp2;
				$line->baseimp3 = $obj->baseimp3;
				$line->baseimp4 = $obj->baseimp4;
				$line->baseimp5 = $obj->baseimp5;
				$line->aliqimp1 = $obj->aliqimp1;
				$line->aliqimp2 = $obj->aliqimp2;
				$line->aliqimp3 = $obj->aliqimp3;
				$line->aliqimp4 = $obj->aliqimp4;
				$line->aliqimp5 = $obj->aliqimp5;
				$line->valimp1 = $obj->valimp1;
				$line->valimp2 = $obj->valimp2;
				$line->valimp3 = $obj->valimp3;
				$line->valimp4 = $obj->valimp4;
				$line->valimp5 = $obj->valimp5;
				$line->valret1 = $obj->valret1;
				$line->valret2 = $obj->valret2;
				$line->valret3 = $obj->valret3;
				$line->valret4 = $obj->valret4;
				$line->valret5 = $obj->valret5;
				$line->amount_payment = $obj->amount_payment;
				$line->amount_balance = $obj->amount_balance;
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->date_mod = $this->db->jdate($obj->date_mod);
				$line->tms = $this->db->jdate($obj->tms);
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->statut_print = $obj->statut_print;
				$line->status = $obj->status;

				if ($lView)
				{
					if ($num == 1) $this->fetch($obj->rowid);
				}

				$this->lines[$line->id] = $line;
			}
			$this->db->free($resql);

			return $num;
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

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
		if (isset($this->nfiscal)) {
			 $this->nfiscal = trim($this->nfiscal);
		}
		if (isset($this->serie)) {
			 $this->serie = trim($this->serie);
		}
		if (isset($this->fk_dosing)) {
			 $this->fk_dosing = trim($this->fk_dosing);
		}
		if (isset($this->fk_facture)) {
			 $this->fk_facture = trim($this->fk_facture);
		}
		if (isset($this->fk_cliepro)) {
			 $this->fk_cliepro = trim($this->fk_cliepro);
		}
		if (isset($this->nit)) {
			 $this->nit = trim($this->nit);
		}
		if (isset($this->razsoc)) {
			 $this->razsoc = trim($this->razsoc);
		}
		if (isset($this->type_op)) {
			 $this->type_op = trim($this->type_op);
		}
		if (isset($this->num_autoriz)) {
			 $this->num_autoriz = trim($this->num_autoriz);
		}
		if (isset($this->cod_control)) {
			 $this->cod_control = trim($this->cod_control);
		}
		if (isset($this->baseimp1)) {
			 $this->baseimp1 = trim($this->baseimp1);
		}
		if (isset($this->baseimp2)) {
			 $this->baseimp2 = trim($this->baseimp2);
		}
		if (isset($this->baseimp3)) {
			 $this->baseimp3 = trim($this->baseimp3);
		}
		if (isset($this->baseimp4)) {
			 $this->baseimp4 = trim($this->baseimp4);
		}
		if (isset($this->baseimp5)) {
			 $this->baseimp5 = trim($this->baseimp5);
		}
		if (isset($this->aliqimp1)) {
			 $this->aliqimp1 = trim($this->aliqimp1);
		}
		if (isset($this->aliqimp2)) {
			 $this->aliqimp2 = trim($this->aliqimp2);
		}
		if (isset($this->aliqimp3)) {
			 $this->aliqimp3 = trim($this->aliqimp3);
		}
		if (isset($this->aliqimp4)) {
			 $this->aliqimp4 = trim($this->aliqimp4);
		}
		if (isset($this->aliqimp5)) {
			 $this->aliqimp5 = trim($this->aliqimp5);
		}
		if (isset($this->valimp1)) {
			 $this->valimp1 = trim($this->valimp1);
		}
		if (isset($this->valimp2)) {
			 $this->valimp2 = trim($this->valimp2);
		}
		if (isset($this->valimp3)) {
			 $this->valimp3 = trim($this->valimp3);
		}
		if (isset($this->valimp4)) {
			 $this->valimp4 = trim($this->valimp4);
		}
		if (isset($this->valimp5)) {
			 $this->valimp5 = trim($this->valimp5);
		}
		if (isset($this->valret1)) {
			 $this->valret1 = trim($this->valret1);
		}
		if (isset($this->valret2)) {
			 $this->valret2 = trim($this->valret2);
		}
		if (isset($this->valret3)) {
			 $this->valret3 = trim($this->valret3);
		}
		if (isset($this->valret4)) {
			 $this->valret4 = trim($this->valret4);
		}
		if (isset($this->valret5)) {
			 $this->valret5 = trim($this->valret5);
		}
		if (isset($this->amount_payment)) {
			 $this->amount_payment = trim($this->amount_payment);
		}
		if (isset($this->amount_balance)) {
			 $this->amount_balance = trim($this->amount_balance);
		}
		if (isset($this->fk_user_create)) {
			 $this->fk_user_create = trim($this->fk_user_create);
		}
		if (isset($this->statut_print)) {
			 $this->statut_print = trim($this->statut_print);
		}
		if (isset($this->status)) {
			 $this->status = trim($this->status);
		}



		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';

		$sql .= ' entity = '.(isset($this->entity)?$this->entity:"null").',';
		$sql .= ' nfiscal = '.(isset($this->nfiscal)?"'".$this->db->escape($this->nfiscal)."'":"null").',';
		$sql .= ' serie = '.(isset($this->serie)?"'".$this->db->escape($this->serie)."'":"null").',';
		$sql .= ' fk_dosing = '.(isset($this->fk_dosing)?$this->fk_dosing:"null").',';
		$sql .= ' fk_facture = '.(isset($this->fk_facture)?$this->fk_facture:"null").',';
		$sql .= ' fk_cliepro = '.(isset($this->fk_cliepro)?$this->fk_cliepro:"null").',';
		$sql .= ' nit = '.(isset($this->nit)?"'".$this->db->escape($this->nit)."'":"null").',';
		$sql .= ' razsoc = '.(isset($this->razsoc)?"'".$this->db->escape($this->razsoc)."'":"null").',';
		$sql .= ' date_exp = '.(! isset($this->date_exp) || dol_strlen($this->date_exp) != 0 ? "'".$this->db->idate($this->date_exp)."'" : 'null').',';
		$sql .= ' type_op = '.(isset($this->type_op)?"'".$this->db->escape($this->type_op)."'":"null").',';
		$sql .= ' num_autoriz = '.(isset($this->num_autoriz)?"'".$this->db->escape($this->num_autoriz)."'":"null").',';
		$sql .= ' cod_control = '.(isset($this->cod_control)?"'".$this->db->escape($this->cod_control)."'":"null").',';
		$sql .= ' baseimp1 = '.(isset($this->baseimp1)?$this->baseimp1:"null").',';
		$sql .= ' baseimp2 = '.(isset($this->baseimp2)?$this->baseimp2:"null").',';
		$sql .= ' baseimp3 = '.(isset($this->baseimp3)?$this->baseimp3:"null").',';
		$sql .= ' baseimp4 = '.(isset($this->baseimp4)?$this->baseimp4:"null").',';
		$sql .= ' baseimp5 = '.(isset($this->baseimp5)?$this->baseimp5:"null").',';
		$sql .= ' aliqimp1 = '.(isset($this->aliqimp1)?$this->aliqimp1:"null").',';
		$sql .= ' aliqimp2 = '.(isset($this->aliqimp2)?$this->aliqimp2:"null").',';
		$sql .= ' aliqimp3 = '.(isset($this->aliqimp3)?$this->aliqimp3:"null").',';
		$sql .= ' aliqimp4 = '.(isset($this->aliqimp4)?$this->aliqimp4:"null").',';
		$sql .= ' aliqimp5 = '.(isset($this->aliqimp5)?$this->aliqimp5:"null").',';
		$sql .= ' valimp1 = '.(isset($this->valimp1)?$this->valimp1:"null").',';
		$sql .= ' valimp2 = '.(isset($this->valimp2)?$this->valimp2:"null").',';
		$sql .= ' valimp3 = '.(isset($this->valimp3)?$this->valimp3:"null").',';
		$sql .= ' valimp4 = '.(isset($this->valimp4)?$this->valimp4:"null").',';
		$sql .= ' valimp5 = '.(isset($this->valimp5)?$this->valimp5:"null").',';
		$sql .= ' valret1 = '.(isset($this->valret1)?$this->valret1:"null").',';
		$sql .= ' valret2 = '.(isset($this->valret2)?$this->valret2:"null").',';
		$sql .= ' valret3 = '.(isset($this->valret3)?$this->valret3:"null").',';
		$sql .= ' valret4 = '.(isset($this->valret4)?$this->valret4:"null").',';
		$sql .= ' valret5 = '.(isset($this->valret5)?$this->valret5:"null").',';
		$sql .= ' amount_payment = '.(isset($this->amount_payment)?$this->amount_payment:"null").',';
		$sql .= ' amount_balance = '.(isset($this->amount_balance)?$this->amount_balance:"null").',';
		$sql .= ' date_create = '.(! isset($this->date_create) || dol_strlen($this->date_create) != 0 ? "'".$this->db->idate($this->date_create)."'" : 'null').',';
		$sql .= ' date_mod = '.(! isset($this->date_mod) || dol_strlen($this->date_mod) != 0 ? "'".$this->db->idate($this->date_mod)."'" : 'null').',';
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
		$sql .= ' fk_user_create = '.(isset($this->fk_user_create)?$this->fk_user_create:"null").',';
		$sql .= ' fk_user_mod = '.(isset($this->fk_user_mod)?$this->fk_user_mod:"null").',';
		$sql .= ' statut_print = '.(isset($this->statut_print)?$this->statut_print:"null").',';
		$sql .= ' status = '.(isset($this->status)?$this->status:"null");


		$sql .= ' WHERE rowid=' . $this->id;

		$this->db->begin();

		$resql = $this->db->query($sql);
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
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

		if (!$error) {
			$sql = 'DELETE FROM ' . MAIN_DB_PREFIX . $this->table_element;
			$sql .= ' WHERE rowid=' . $this->id;

			$resql = $this->db->query($sql);
			if (!$resql) {
				$error ++;
				$this->errors[] = 'Error ' . $this->db->lasterror();
				dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
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
		$object = new Vfiscal($this->db);

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
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
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
	 *  Return a link to the user card (with optionaly the picto)
	 * 	Use this->id,this->lastname, this->firstname
	 *
	 *	@param	int		$withpicto			Include picto in link (0=No picto, 1=Include picto into link, 2=Only picto)
	 *	@param	string	$option				On what the link point to
     *  @param	integer	$notooltip			1=Disable tooltip
     *  @param	int		$maxlen				Max length of visible user name
     *  @param  string  $morecss            Add more css on link
	 *	@return	string						String with URL
	 */
	function getNomUrl($withpicto=0, $option='', $notooltip=0, $maxlen=24, $morecss='')
	{
		global $langs, $conf, $db;
        global $dolibarr_main_authentication, $dolibarr_main_demo;
        global $menumanager;


        $result = '';
        $companylink = '';

        $label = '<u>' . $langs->trans("MyModule") . '</u>';
        $label.= '<div width="100%">';
        $label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

        $link = '<a href="'.DOL_URL_ROOT.'/fiscal/card.php?id='.$this->id.'"';
        $link.= ($notooltip?'':' title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip'.($morecss?' '.$morecss:'').'"');
        $link.= '>';
		$linkend='</a>';

        if ($withpicto)
        {
            $result.=($link.img_object(($notooltip?'':$label), 'label', ($notooltip?'':'class="classfortooltip"')).$linkend);
            if ($withpicto != 2) $result.=' ';
		}
		$result.= $link . $this->ref . $linkend;
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
	 *  Renvoi le libelle d'un status donne
	 *
	 *  @param	int		$status        	Id status
	 *  @param  int		$mode          	0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return string 			       	Label of status
	 */
	function LibStatut($status,$mode=0)
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
		$this->nfiscal = '';
		$this->serie = '';
		$this->fk_dosing = '';
		$this->fk_facture = '';
		$this->fk_cliepro = '';
		$this->nit = '';
		$this->razsoc = '';
		$this->date_exp = '';
		$this->type_op = '';
		$this->num_autoriz = '';
		$this->cod_control = '';
		$this->baseimp1 = '';
		$this->baseimp2 = '';
		$this->baseimp3 = '';
		$this->baseimp4 = '';
		$this->baseimp5 = '';
		$this->aliqimp1 = '';
		$this->aliqimp2 = '';
		$this->aliqimp3 = '';
		$this->aliqimp4 = '';
		$this->aliqimp5 = '';
		$this->valimp1 = '';
		$this->valimp2 = '';
		$this->valimp3 = '';
		$this->valimp4 = '';
		$this->valimp5 = '';
		$this->valret1 = '';
		$this->valret2 = '';
		$this->valret3 = '';
		$this->valret4 = '';
		$this->valret5 = '';
		$this->amount_payment = '';
		$this->amount_balance = '';
		$this->date_create = '';
		$this->date_mod = '';
		$this->tms = '';
		$this->fk_user_create = '';
		$this->fk_user_mod = '';
		$this->statut_print = '';
		$this->status = '';


	}

}

/**
 * Class VfiscalLine
 */
class VfiscalLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */

	public $entity;
	public $nfiscal;
	public $serie;
	public $fk_dosing;
	public $fk_facture;
	public $fk_cliepro;
	public $nit;
	public $razsoc;
	public $date_exp = '';
	public $type_op;
	public $num_autoriz;
	public $cod_control;
	public $baseimp1;
	public $baseimp2;
	public $baseimp3;
	public $baseimp4;
	public $baseimp5;
	public $aliqimp1;
	public $aliqimp2;
	public $aliqimp3;
	public $aliqimp4;
	public $aliqimp5;
	public $valimp1;
	public $valimp2;
	public $valimp3;
	public $valimp4;
	public $valimp5;
	public $valret1;
	public $valret2;
	public $valret3;
	public $valret4;
	public $valret5;
	public $amount_payment;
	public $amount_balance;
	public $date_create = '';
	public $date_mod = '';
	public $tms = '';
	public $fk_user_create;
	public $fk_user_mod = '';
	public $statut_print;
	public $status;

	/**
	 * @var mixed Sample line property 2
	 */

}
