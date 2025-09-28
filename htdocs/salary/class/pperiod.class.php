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
 * \file    salary/pperiod.class.php
 * \ingroup salary
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Pperiod
 *
 * Put here description of your class
 *
 * @see CommonObject
 */
class Pperiod extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'pperiod';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'p_period';

	/**
	 * @var PperiodLine[] Lines
	 */
	public $lines = array();

	/**
	 */

	public $entity;
	public $fk_proces;
	public $fk_type_fol;
	public $ref;
	public $mes;
	public $anio;
	public $model_pdf;
	public $date_ini = '';
	public $date_fin = '';
	public $date_pay = '';
	public $date_court = '';
	public $date_close = '';
	public $status_app;
	public $state;

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
		if (isset($this->fk_proces)) {
			 $this->fk_proces = trim($this->fk_proces);
		}
		if (isset($this->fk_type_fol)) {
			 $this->fk_type_fol = trim($this->fk_type_fol);
		}
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->mes)) {
			 $this->mes = trim($this->mes);
		}
		if (isset($this->anio)) {
			 $this->anio = trim($this->anio);
		}
		if (isset($this->model_pdf)) {
			 $this->model_pdf = trim($this->model_pdf);
		}
		if (isset($this->status_app)) {
			 $this->status_app = trim($this->status_app);
		}
		if (isset($this->state)) {
			 $this->state = trim($this->state);
		}



		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';

		$sql.= 'entity,';
		$sql.= 'fk_proces,';
		$sql.= 'fk_type_fol,';
		$sql.= 'ref,';
		$sql.= 'mes,';
		$sql.= 'anio,';
		$sql.= 'model_pdf,';
		$sql.= 'date_ini,';
		$sql.= 'date_fin,';
		$sql.= 'date_pay,';
		$sql.= 'date_court,';
		$sql.= 'date_close,';
		$sql.= 'status_app,';
		$sql.= 'state';


		$sql .= ') VALUES (';

		$sql .= ' '.(! isset($this->entity)?'NULL':$this->entity).',';
		$sql .= ' '.(! isset($this->fk_proces)?'NULL':$this->fk_proces).',';
		$sql .= ' '.(! isset($this->fk_type_fol)?'NULL':$this->fk_type_fol).',';
		$sql .= ' '.(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql .= ' '.(! isset($this->mes)?'NULL':$this->mes).',';
		$sql .= ' '.(! isset($this->anio)?'NULL':$this->anio).',';
		$sql .= ' '.(! isset($this->model_pdf)?'NULL':"'".$this->db->escape($this->model_pdf)."'").',';
		$sql .= ' '.(! isset($this->date_ini) || dol_strlen($this->date_ini)==0?'NULL':"'".$this->db->idate($this->date_ini)."'").',';
		$sql .= ' '.(! isset($this->date_fin) || dol_strlen($this->date_fin)==0?'NULL':"'".$this->db->idate($this->date_fin)."'").',';
		$sql .= ' '.(! isset($this->date_pay) || dol_strlen($this->date_pay)==0?'NULL':"'".$this->db->idate($this->date_pay)."'").',';
		$sql .= ' '.(! isset($this->date_court) || dol_strlen($this->date_court)==0?'NULL':"'".$this->db->idate($this->date_court)."'").',';
		$sql .= ' '.(! isset($this->date_close) || dol_strlen($this->date_close)==0?'NULL':"'".$this->db->idate($this->date_close)."'").',';
		$sql .= ' '.(! isset($this->status_app)?'NULL':$this->status_app).',';
		$sql .= ' '.(! isset($this->state)?'NULL':$this->state);


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
		$sql .= " t.fk_proces,";
		$sql .= " t.fk_type_fol,";
		$sql .= " t.ref,";
		$sql .= " t.mes,";
		$sql .= " t.anio,";
		$sql .= " t.model_pdf,";
		$sql .= " t.date_ini,";
		$sql .= " t.date_fin,";
		$sql .= " t.date_pay,";
		$sql .= " t.date_court,";
		$sql .= " t.date_close,";
		$sql .= " t.status_app,";
		$sql .= " t.state";


		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
		    $sql .= " AND entity IN (" . getEntity("pperiod", 1) . ")";
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
				$this->fk_proces = $obj->fk_proces;
				$this->fk_type_fol = $obj->fk_type_fol;
				$this->ref = $obj->ref;
				$this->mes = $obj->mes;
				$this->anio = $obj->anio;
				$this->model_pdf = $obj->model_pdf;
				$this->date_ini = $this->db->jdate($obj->date_ini);
				$this->date_fin = $this->db->jdate($obj->date_fin);
				$this->date_pay = $this->db->jdate($obj->date_pay);
				$this->date_court = $this->db->jdate($obj->date_court);
				$this->date_close = $this->db->jdate($obj->date_close);
				$this->status_app = $obj->status_app;
				$this->state = $obj->state;


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
		$sql .= " t.fk_proces,";
		$sql .= " t.fk_type_fol,";
		$sql .= " t.ref,";
		$sql .= " t.mes,";
		$sql .= " t.anio,";
		$sql .= " t.model_pdf,";
		$sql .= " t.date_ini,";
		$sql .= " t.date_fin,";
		$sql .= " t.date_pay,";
		$sql .= " t.date_court,";
		$sql .= " t.date_close,";
		$sql .= " t.status_app,";
		$sql .= " t.state";


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
		    $sql .= " AND entity IN (" . getEntity("pperiod", 1) . ")";
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
				$line = new PperiodLine();

				$line->id = $obj->rowid;

				$line->entity = $obj->entity;
				$line->fk_proces = $obj->fk_proces;
				$line->fk_type_fol = $obj->fk_type_fol;
				$line->ref = $obj->ref;
				$line->mes = $obj->mes;
				$line->anio = $obj->anio;
				$line->model_pdf = $obj->model_pdf;
				$line->date_ini = $this->db->jdate($obj->date_ini);
				$line->date_fin = $this->db->jdate($obj->date_fin);
				$line->date_pay = $this->db->jdate($obj->date_pay);
				$line->date_court = $this->db->jdate($obj->date_court);
				$line->date_close = $this->db->jdate($obj->date_close);
				$line->status_app = $obj->status_app;
				$line->state = $obj->state;



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
		if (isset($this->fk_proces)) {
			 $this->fk_proces = trim($this->fk_proces);
		}
		if (isset($this->fk_type_fol)) {
			 $this->fk_type_fol = trim($this->fk_type_fol);
		}
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->mes)) {
			 $this->mes = trim($this->mes);
		}
		if (isset($this->anio)) {
			 $this->anio = trim($this->anio);
		}
		if (isset($this->model_pdf)) {
			 $this->model_pdf = trim($this->model_pdf);
		}
		if (isset($this->status_app)) {
			 $this->status_app = trim($this->status_app);
		}
		if (isset($this->state)) {
			 $this->state = trim($this->state);
		}



		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';

		$sql .= ' entity = '.(isset($this->entity)?$this->entity:"null").',';
		$sql .= ' fk_proces = '.(isset($this->fk_proces)?$this->fk_proces:"null").',';
		$sql .= ' fk_type_fol = '.(isset($this->fk_type_fol)?$this->fk_type_fol:"null").',';
		$sql .= ' ref = '.(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").',';
		$sql .= ' mes = '.(isset($this->mes)?$this->mes:"null").',';
		$sql .= ' anio = '.(isset($this->anio)?$this->anio:"null").',';
		$sql .= ' model_pdf = '.(isset($this->model_pdf)?"'".$this->db->escape($this->model_pdf)."'":"null").',';
		$sql .= ' date_ini = '.(! isset($this->date_ini) || dol_strlen($this->date_ini) != 0 ? "'".$this->db->idate($this->date_ini)."'" : 'null').',';
		$sql .= ' date_fin = '.(! isset($this->date_fin) || dol_strlen($this->date_fin) != 0 ? "'".$this->db->idate($this->date_fin)."'" : 'null').',';
		$sql .= ' date_pay = '.(! isset($this->date_pay) || dol_strlen($this->date_pay) != 0 ? "'".$this->db->idate($this->date_pay)."'" : 'null').',';
		$sql .= ' date_court = '.(! isset($this->date_court) || dol_strlen($this->date_court) != 0 ? "'".$this->db->idate($this->date_court)."'" : 'null').',';
		$sql .= ' date_close = '.(! isset($this->date_close) || dol_strlen($this->date_close) != 0 ? "'".$this->db->idate($this->date_close)."'" : 'null').',';
		$sql .= ' status_app = '.(isset($this->status_app)?$this->status_app:"null").',';
		$sql .= ' state = '.(isset($this->state)?$this->state:"null");


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
		$object = new Pperiod($this->db);

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
		$this->fk_proces = '';
		$this->fk_type_fol = '';
		$this->ref = '';
		$this->mes = '';
		$this->anio = '';
		$this->model_pdf = '';
		$this->date_ini = '';
		$this->date_fin = '';
		$this->date_pay = '';
		$this->date_court = '';
		$this->date_close = '';
		$this->status_app = '';
		$this->state = '';


	}

}

/**
 * Class PperiodLine
 */
class PperiodLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */

	public $entity;
	public $fk_proces;
	public $fk_type_fol;
	public $ref;
	public $mes;
	public $anio;
	public $model_pdf;
	public $date_ini = '';
	public $date_fin = '';
	public $date_pay = '';
	public $date_court = '';
	public $date_close = '';
	public $status_app;
	public $state;

	/**
	 * @var mixed Sample line property 2
	 */

}
