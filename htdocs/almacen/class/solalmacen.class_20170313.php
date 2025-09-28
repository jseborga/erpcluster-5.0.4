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
 * \file    almacen/solalmacen.class.php
 * \ingroup almacen
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Solalmacen
 *
 * Put here description of your class
 * @see CommonObject
 */
class Solalmacen extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'solalmacen';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'sol_almacen';

	/**
	 * @var SolalmacenLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	
	public $entity;
	public $ref;
	public $fk_entrepot_from;
	public $fk_entrepot;
	public $fk_fabrication;
	public $fk_projet;
	public $fk_user;
	public $date_creation = '';
	public $date_delivery = '';
	public $description;
	public $model_pdf;
	public $fk_user_create;
	public $fk_user_mod;
	public $tms = '';
	public $statut;

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
		if (isset($this->fk_entrepot_from)) {
			 $this->fk_entrepot_from = trim($this->fk_entrepot_from);
		}
		if (isset($this->fk_entrepot)) {
			 $this->fk_entrepot = trim($this->fk_entrepot);
		}
		if (isset($this->fk_fabrication)) {
			 $this->fk_fabrication = trim($this->fk_fabrication);
		}
		if (isset($this->fk_projet)) {
			 $this->fk_projet = trim($this->fk_projet);
		}
		if (isset($this->fk_user)) {
			 $this->fk_user = trim($this->fk_user);
		}
		if (isset($this->description)) {
			 $this->description = trim($this->description);
		}
		if (isset($this->model_pdf)) {
			 $this->model_pdf = trim($this->model_pdf);
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

		

		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';
		
		$sql.= 'entity,';
		$sql.= 'ref,';
		$sql.= 'fk_entrepot_from,';
		$sql.= 'fk_entrepot,';
		$sql.= 'fk_fabrication,';
		$sql.= 'fk_projet,';
		$sql.= 'fk_user,';
		$sql.= 'date_creation,';
		$sql.= 'date_delivery,';
		$sql.= 'description,';
		$sql.= 'model_pdf,';
		$sql.= 'fk_user_create,';
		$sql.= 'fk_user_mod,';
		$sql.= 'statut';

		
		$sql .= ') VALUES (';
		
		$sql .= ' '.(! isset($this->entity)?'NULL':$this->entity).',';
		$sql .= ' '.(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql .= ' '.(! isset($this->fk_entrepot_from)?'NULL':$this->fk_entrepot_from).',';
		$sql .= ' '.(! isset($this->fk_entrepot)?'NULL':$this->fk_entrepot).',';
		$sql .= ' '.(! isset($this->fk_fabrication)?'NULL':$this->fk_fabrication).',';
		$sql .= ' '.(! isset($this->fk_projet)?'NULL':$this->fk_projet).',';
		$sql .= ' '.(! isset($this->fk_user)?'NULL':$this->fk_user).',';
		$sql .= ' '.(! isset($this->date_creation) || dol_strlen($this->date_creation)==0?'NULL':"'".$this->db->idate($this->date_creation)."'").',';
		$sql .= ' '.(! isset($this->date_delivery) || dol_strlen($this->date_delivery)==0?'NULL':"'".$this->db->idate($this->date_delivery)."'").',';
		$sql .= ' '.(! isset($this->description)?'NULL':"'".$this->db->escape($this->description)."'").',';
		$sql .= ' '.(! isset($this->model_pdf)?'NULL':"'".$this->db->escape($this->model_pdf)."'").',';
		$sql .= ' '.(! isset($this->fk_user_create)?'NULL':$this->fk_user_create).',';
		$sql .= ' '.$user->id.',';
		$sql .= ' '.(! isset($this->statut)?'NULL':$this->statut);

		
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
	public function fetch($id, $ref = null)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		
		$sql .= " t.entity,";
		$sql .= " t.ref,";
		$sql .= " t.fk_entrepot_from,";
		$sql .= " t.fk_entrepot,";
		$sql .= " t.fk_fabrication,";
		$sql .= " t.fk_projet,";
		$sql .= " t.fk_user,";
		$sql .= " t.date_creation,";
		$sql .= " t.date_delivery,";
		$sql .= " t.description,";
		$sql .= " t.model_pdf,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.tms,";
		$sql .= " t.statut";

		
		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		if (null !== $ref) {
			$sql .= ' WHERE t.ref = ' . '\'' . $ref . '\'';
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
				$this->ref = $obj->ref;
				$this->fk_entrepot_from = $obj->fk_entrepot_from;
				$this->fk_entrepot = $obj->fk_entrepot;
				$this->fk_fabrication = $obj->fk_fabrication;
				$this->fk_projet = $obj->fk_projet;
				$this->fk_user = $obj->fk_user;
				$this->date_creation = $this->db->jdate($obj->date_creation);
				$this->date_delivery = $this->db->jdate($obj->date_delivery);
				$this->description = $obj->description;
				$this->model_pdf = $obj->model_pdf;
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_mod = $obj->fk_user_mod;
				$this->tms = $this->db->jdate($obj->tms);
				$this->statut = $obj->statut;

				
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
		$sql .= " t.ref,";
		$sql .= " t.fk_entrepot_from,";
		$sql .= " t.fk_entrepot,";
		$sql .= " t.fk_fabrication,";
		$sql .= " t.fk_projet,";
		$sql .= " t.fk_user,";
		$sql .= " t.date_creation,";
		$sql .= " t.date_delivery,";
		$sql .= " t.description,";
		$sql .= " t.model_pdf,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.tms,";
		$sql .= " t.statut";

		
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
				$line = new SolalmacenLine();

				$line->id = $obj->rowid;
				
				$line->entity = $obj->entity;
				$line->ref = $obj->ref;
				$line->fk_entrepot_from = $obj->fk_entrepot_from;
				$line->fk_entrepot = $obj->fk_entrepot;
				$line->fk_fabrication = $obj->fk_fabrication;
				$line->fk_projet = $obj->fk_projet;
				$line->fk_user = $obj->fk_user;
				$line->date_creation = $this->db->jdate($obj->date_creation);
				$line->date_delivery = $this->db->jdate($obj->date_delivery);
				$line->description = $obj->description;
				$line->model_pdf = $obj->model_pdf;
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->tms = $this->db->jdate($obj->tms);
				$line->statut = $obj->statut;

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
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->fk_entrepot_from)) {
			 $this->fk_entrepot_from = trim($this->fk_entrepot_from);
		}
		if (isset($this->fk_entrepot)) {
			 $this->fk_entrepot = trim($this->fk_entrepot);
		}
		if (isset($this->fk_fabrication)) {
			 $this->fk_fabrication = trim($this->fk_fabrication);
		}
		if (isset($this->fk_projet)) {
			 $this->fk_projet = trim($this->fk_projet);
		}
		if (isset($this->fk_user)) {
			 $this->fk_user = trim($this->fk_user);
		}
		if (isset($this->description)) {
			 $this->description = trim($this->description);
		}
		if (isset($this->model_pdf)) {
			 $this->model_pdf = trim($this->model_pdf);
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

		

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';
		
		$sql .= ' entity = '.(isset($this->entity)?$this->entity:"null").',';
		$sql .= ' ref = '.(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").',';
		$sql .= ' fk_entrepot_from = '.(isset($this->fk_entrepot_from)?$this->fk_entrepot_from:"null").',';
		$sql .= ' fk_entrepot = '.(isset($this->fk_entrepot)?$this->fk_entrepot:"null").',';
		$sql .= ' fk_fabrication = '.(isset($this->fk_fabrication)?$this->fk_fabrication:"null").',';
		$sql .= ' fk_projet = '.(isset($this->fk_projet)?$this->fk_projet:"null").',';
		$sql .= ' fk_user = '.(isset($this->fk_user)?$this->fk_user:"null").',';
		$sql .= ' date_creation = '.(! isset($this->date_creation) || dol_strlen($this->date_creation) != 0 ? "'".$this->db->idate($this->date_creation)."'" : 'null').',';
		$sql .= ' date_delivery = '.(! isset($this->date_delivery) || dol_strlen($this->date_delivery) != 0 ? "'".$this->db->idate($this->date_delivery)."'" : 'null').',';
		$sql .= ' description = '.(isset($this->description)?"'".$this->db->escape($this->description)."'":"null").',';
		$sql .= ' model_pdf = '.(isset($this->model_pdf)?"'".$this->db->escape($this->model_pdf)."'":"null").',';
		$sql .= ' fk_user_create = '.(isset($this->fk_user_create)?$this->fk_user_create:"null").',';
		$sql .= ' fk_user_mod = '.$user->id.',';
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
		$sql .= ' statut = '.(isset($this->statut)?$this->statut:"null");

        
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
		$object = new Solalmacen($this->db);

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

        $link = '<a href="'.DOL_URL_ROOT.'/almacen/card.php?id='.$this->id.'"';
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
		return $this->LibStatut($this->statut,$mode);
	}

	/**
	 *  Renvoi le libelle d'un status donne
	 *
	 *  @param	int		$status        	Id status
	 *  @param  int		$mode          	0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return string 			       	Label of status
	 */
	function LibStatut($statut,$mode=0)
	{
		global $langs;

       if ($mode == 0)
        {
            if ($statut==-1) return $langs->trans('StatusOrderCanceled');
            if ($statut==0) return $langs->trans('StatusOrderDraft');
            if ($statut==1) return $langs->trans('StatusOrderValidated');
            if ($statut==2) return $langs->trans('StatusOrderSent');
            if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('StatusOrderToBill');
            if ($statut==3 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('StatusOrderProcessed');
        }
        elseif ($mode == 1)
        {
            if ($statut==-1) return $langs->trans('StatusOrderCanceledShort');
            if ($statut==0) return $langs->trans('StatusOrderDraftShort');
            if ($statut==1) return $langs->trans('StatusOrderValidatedShort');
            if ($statut==2) return $langs->trans('StatusOrderSentShort');
            if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('StatusOrderToBillShort');
            if ($statut==3 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('StatusOrderProcessed');
        }
        elseif ($mode == 2)
        {
            if ($statut==-1) return img_picto($langs->trans('StatusOrderCanceled'),'statut5').' '.$langs->trans('StatusOrderCanceledShort');
            if ($statut==0) return img_picto($langs->trans('StatusOrderDraft'),'statut0').' '.$langs->trans('StatusOrderDraftShort');
            if ($statut==1) return img_picto($langs->trans('StatusOrderValidated'),'statut1').' '.$langs->trans('StatusOrderValidatedShort');
            if ($statut==2) return img_picto($langs->trans('StatusOrderSent'),'statut3').' '.$langs->trans('StatusOrderSentShort');
            if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderToBill'),'statut7').' '.$langs->trans('StatusOrderToBillShort');
            if ($statut==3 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderProcessed'),'statut6').' '.$langs->trans('StatusOrderProcessedShort');
        }
        elseif ($mode == 3)
        {
            if ($statut==-1) return img_picto($langs->trans('StatusOrderCanceled'),'statut5');
            if ($statut==0) return img_picto($langs->trans('StatusOrderDraft'),'statut0');
            if ($statut==1) return img_picto($langs->trans('StatusOrderValidated'),'statut1');
            if ($statut==2) return img_picto($langs->trans('StatusOrderSentShort'),'statut3');
            if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderToBill'),'statut7');
            if ($statut==3 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderProcessed'),'statut6');
        }
        elseif ($mode == 4)
        {
            if ($statut==-1) return img_picto($langs->trans('StatusOrderCanceled'),'statut5').' '.$langs->trans('StatusOrderCanceled');
            if ($statut==0) return img_picto($langs->trans('StatusOrderDraft'),'statut0').' '.$langs->trans('StatusOrderDraft');
            if ($statut==1) return img_picto($langs->trans('StatusOrderValidated'),'statut1').' '.$langs->trans('StatusOrderValidated');
            if ($statut==2) return img_picto($langs->trans('StatusOrderSentShort'),'statut3').' '.$langs->trans('StatusOrderSent');
            if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderToBill'),'statut7').' '.$langs->trans('StatusOrderToBill');
            if ($statut==3 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderProcessed'),'statut6').' '.$langs->trans('StatusOrderProcessed');
        }
        elseif ($mode == 5)
        {
            if ($statut==-1) return $langs->trans('StatusOrderCanceledShort').' '.img_picto($langs->trans('StatusOrderCanceled'),'statut5');
            if ($statut==0) return $langs->trans('StatusOrderDraftShort').' '.img_picto($langs->trans('StatusOrderDraft'),'statut0');
            if ($statut==1) return $langs->trans('StatusOrderValidatedShort').' '.img_picto($langs->trans('StatusOrderValidated'),'statut1');
            if ($statut==2) return $langs->trans('StatusOrderSentShort').' '.img_picto($langs->trans('StatusOrderSent'),'statut3');
            if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('StatusOrderToBillShort').' '.img_picto($langs->trans('StatusOrderToBill'),'statut7');
            if ($statut==3 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('StatusOrderProcessedShort').' '.img_picto($langs->trans('StatusOrderProcessed'),'statut6');
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
		$this->fk_entrepot_from = '';
		$this->fk_entrepot = '';
		$this->fk_fabrication = '';
		$this->fk_projet = '';
		$this->fk_user = '';
		$this->date_creation = '';
		$this->date_delivery = '';
		$this->description = '';
		$this->model_pdf = '';
		$this->fk_user_create = '';
		$this->fk_user_mod = '';
		$this->tms = '';
		$this->statut = '';

		
	}

}

/**
 * Class SolalmacenLine
 */
class SolalmacenLine
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
	public $fk_entrepot_from;
	public $fk_entrepot;
	public $fk_fabrication;
	public $fk_projet;
	public $fk_user;
	public $date_creation = '';
	public $date_delivery = '';
	public $description;
	public $model_pdf;
	public $fk_user_create;
	public $fk_user_mod;
	public $tms = '';
	public $statut;

	/**
	 * @var mixed Sample line property 2
	 */
	
}
