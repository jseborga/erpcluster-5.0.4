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
 * \file    advancepayment/paiementfournadvance.class.php
 * \ingroup advancepayment
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Paiementfournadvance
 *
 * Put here description of your class
 * @see CommonObject
 */
class Paiementfournadvance extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'paiementfournadvance';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'paiementfourn_advance';

	/**
	 * @var PaiementfournadvanceLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	
	public $ref;
	public $entity;
	public $tms = '';
	public $datec = '';
	public $datep = '';
	public $amount;
	public $fk_user_author;
	public $fk_soc;
	public $fk_facture;
	public $origin;
	public $originid;
	public $fk_paiement;
	public $num_paiement;
	public $note;
	public $fk_bank;
	public $model_pdf;
	public $statut;
	public $multicurrency_amount;

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
		
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->entity)) {
			 $this->entity = trim($this->entity);
		}
		if (isset($this->amount)) {
			 $this->amount = trim($this->amount);
		}
		if (isset($this->fk_user_author)) {
			 $this->fk_user_author = trim($this->fk_user_author);
		}
		if (isset($this->fk_soc)) {
			 $this->fk_soc = trim($this->fk_soc);
		}
		if (isset($this->fk_facture)) {
			 $this->fk_facture = trim($this->fk_facture);
		}
		if (isset($this->origin)) {
			 $this->origin = trim($this->origin);
		}
		if (isset($this->originid)) {
			 $this->originid = trim($this->originid);
		}
		if (isset($this->fk_paiement)) {
			 $this->fk_paiement = trim($this->fk_paiement);
		}
		if (isset($this->num_paiement)) {
			 $this->num_paiement = trim($this->num_paiement);
		}
		if (isset($this->note)) {
			 $this->note = trim($this->note);
		}
		if (isset($this->fk_bank)) {
			 $this->fk_bank = trim($this->fk_bank);
		}
		if (isset($this->model_pdf)) {
			 $this->model_pdf = trim($this->model_pdf);
		}
		if (isset($this->statut)) {
			 $this->statut = trim($this->statut);
		}
		if (isset($this->multicurrency_amount)) {
			 $this->multicurrency_amount = trim($this->multicurrency_amount);
		}

		

		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';
		
		$sql.= 'ref,';
		$sql.= 'entity,';
		$sql.= 'datec,';
		$sql.= 'datep,';
		$sql.= 'amount,';
		$sql.= 'fk_user_author,';
		$sql.= 'fk_soc,';
		$sql.= 'fk_facture,';
		$sql.= 'origin,';
		$sql.= 'originid,';
		$sql.= 'fk_paiement,';
		$sql.= 'num_paiement,';
		$sql.= 'note,';
		$sql.= 'fk_bank,';
		$sql.= 'model_pdf';
		$sql.= 'statut';
		$sql.= 'multicurrency_amount';

		
		$sql .= ') VALUES (';
		
		$sql .= ' '.(! isset($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql .= ' '.(! isset($this->entity)?'NULL':$this->entity).',';
		$sql .= ' '."'".$this->db->idate(dol_now())."'".',';
		$sql .= ' '.(! isset($this->datep) || dol_strlen($this->datep)==0?'NULL':"'".$this->db->idate($this->datep)."'").',';
		$sql .= ' '.(! isset($this->amount)?'NULL':"'".$this->amount."'").',';
		$sql .= ' '.$user->id.',';
		$sql .= ' '.(! isset($this->fk_soc)?'NULL':$this->fk_soc).',';
		$sql .= ' '.(! isset($this->fk_facture)?'NULL':$this->fk_facture).',';
		$sql .= ' '.(! isset($this->origin)?'NULL':"'".$this->db->escape($this->origin)."'").',';
		$sql .= ' '.(! isset($this->originid)?'NULL':$this->originid).',';
		$sql .= ' '.(! isset($this->fk_paiement)?'NULL':$this->fk_paiement).',';
		$sql .= ' '.(! isset($this->num_paiement)?'NULL':"'".$this->db->escape($this->num_paiement)."'").',';
		$sql .= ' '.(! isset($this->note)?'NULL':"'".$this->db->escape($this->note)."'").',';
		$sql .= ' '.(! isset($this->fk_bank)?'NULL':$this->fk_bank).',';
		$sql .= ' '.(! isset($this->model_pdf)?'NULL':"'".$this->db->escape($this->model_pdf)."'").',';
		$sql .= ' '.(! isset($this->statut)?'NULL':$this->statut).',';
		$sql .= ' '.(! isset($this->multicurrency_amount)?'NULL':"'".$this->multicurrency_amount."'");

		
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
		
		$sql .= " t.ref,";
		$sql .= " t.entity,";
		$sql .= " t.tms,";
		$sql .= " t.datec,";
		$sql .= " t.datep,";
		$sql .= " t.amount,";
		$sql .= " t.fk_user_author,";
		$sql .= " t.fk_soc,";
		$sql .= " t.fk_facture,";
		$sql .= " t.origin,";
		$sql .= " t.originid,";
		$sql .= " t.fk_paiement,";
		$sql .= " t.num_paiement,";
		$sql .= " t.note,";
		$sql .= " t.fk_bank,";
		$sql .= " t.model_pdf,";
		$sql .= " t.statut,";
		$sql .= " t.multicurrency_amount";

		
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
				
				$this->ref = $obj->ref;
				$this->entity = $obj->entity;
				$this->tms = $this->db->jdate($obj->tms);
				$this->datec = $this->db->jdate($obj->datec);
				$this->datep = $this->db->jdate($obj->datep);
				$this->amount = $obj->amount;
				$this->fk_user_author = $obj->fk_user_author;
				$this->fk_soc = $obj->fk_soc;
				$this->fk_facture = $obj->fk_facture;
				$this->origin = $obj->origin;
				$this->originid = $obj->originid;
				$this->fk_paiement = $obj->fk_paiement;
				$this->num_paiement = $obj->num_paiement;
				$this->note = $obj->note;
				$this->fk_bank = $obj->fk_bank;
				$this->model_pdf = $obj->model_pdf;
				$this->statut = $obj->statut;
				$this->multicurrency_amount = $obj->multicurrency_amount;

				
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
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND')
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		
		$sql .= " t.ref,";
		$sql .= " t.entity,";
		$sql .= " t.tms,";
		$sql .= " t.datec,";
		$sql .= " t.datep,";
		$sql .= " t.amount,";
		$sql .= " t.fk_user_author,";
		$sql .= " t.fk_soc,";
		$sql .= " t.fk_facture,";
		$sql .= " t.origin,";
		$sql .= " t.originid,";
		$sql .= " t.fk_paiement,";
		$sql .= " t.num_paiement,";
		$sql .= " t.note,";
		$sql .= " t.fk_bank,";
		$sql .= " t.model_pdf,";
		$sql .= " t.statut,";
		$sql .= " t.multicurrency_amount";

		
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
				$line = new PaiementfournadvanceLine();

				$line->id = $obj->rowid;
				
				$line->ref = $obj->ref;
				$line->entity = $obj->entity;
				$line->tms = $this->db->jdate($obj->tms);
				$line->datec = $this->db->jdate($obj->datec);
				$line->datep = $this->db->jdate($obj->datep);
				$line->amount = $obj->amount;
				$line->fk_user_author = $obj->fk_user_author;
				$line->fk_soc = $obj->fk_soc;
				$line->fk_facture = $obj->fk_facture;
				$line->origin = $obj->origin;
				$line->originid = $obj->originid;
				$line->fk_paiement = $obj->fk_paiement;
				$line->num_paiement = $obj->num_paiement;
				$line->note = $obj->note;
				$line->fk_bank = $obj->fk_bank;
				$line->model_pdf = $obj->model_pdf;
				$line->statut = $obj->statut;
				$line->multicurrency_amount = $obj->multicurrency_amount;

				

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
		
		if (isset($this->ref)) {
			 $this->ref = trim($this->ref);
		}
		if (isset($this->entity)) {
			 $this->entity = trim($this->entity);
		}
		if (isset($this->amount)) {
			 $this->amount = trim($this->amount);
		}
		if (isset($this->fk_user_author)) {
			 $this->fk_user_author = trim($this->fk_user_author);
		}
		if (isset($this->fk_soc)) {
			 $this->fk_soc = trim($this->fk_soc);
		}
		if (isset($this->fk_facture)) {
			 $this->fk_facture = trim($this->fk_facture);
		}
		if (isset($this->origin)) {
			 $this->origin = trim($this->origin);
		}
		if (isset($this->originid)) {
			 $this->originid = trim($this->originid);
		}
		if (isset($this->fk_paiement)) {
			 $this->fk_paiement = trim($this->fk_paiement);
		}
		if (isset($this->num_paiement)) {
			 $this->num_paiement = trim($this->num_paiement);
		}
		if (isset($this->note)) {
			 $this->note = trim($this->note);
		}
		if (isset($this->fk_bank)) {
			 $this->fk_bank = trim($this->fk_bank);
		}
		if (isset($this->model_pdf)) {
			 $this->model_pdf = trim($this->model_pdf);
		}
		if (isset($this->statut)) {
			 $this->statut = trim($this->statut);
		}
		if (isset($this->multicurrency_amount)) {
			 $this->multicurrency_amount = trim($this->multicurrency_amount);
		}

		

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';
		
		$sql .= ' ref = '.(isset($this->ref)?"'".$this->db->escape($this->ref)."'":"null").',';
		$sql .= ' entity = '.(isset($this->entity)?$this->entity:"null").',';
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
		$sql .= ' datec = '.(! isset($this->datec) || dol_strlen($this->datec) != 0 ? "'".$this->db->idate($this->datec)."'" : 'null').',';
		$sql .= ' datep = '.(! isset($this->datep) || dol_strlen($this->datep) != 0 ? "'".$this->db->idate($this->datep)."'" : 'null').',';
		$sql .= ' amount = '.(isset($this->amount)?$this->amount:"null").',';
		$sql .= ' fk_user_author = '.(isset($this->fk_user_author)?$this->fk_user_author:"null").',';
		$sql .= ' fk_soc = '.(isset($this->fk_soc)?$this->fk_soc:"null").',';
		$sql .= ' fk_facture = '.(isset($this->fk_facture)?$this->fk_facture:"null").',';
		$sql .= ' origin = '.(isset($this->origin)?"'".$this->db->escape($this->origin)."'":"null").',';
		$sql .= ' originid = '.(isset($this->originid)?$this->originid:"null").',';
		$sql .= ' fk_paiement = '.(isset($this->fk_paiement)?$this->fk_paiement:"null").',';
		$sql .= ' num_paiement = '.(isset($this->num_paiement)?"'".$this->db->escape($this->num_paiement)."'":"null").',';
		$sql .= ' note = '.(isset($this->note)?"'".$this->db->escape($this->note)."'":"null").',';
		$sql .= ' fk_bank = '.(isset($this->fk_bank)?$this->fk_bank:"null").',';
		$sql .= ' model_pdf = '.(isset($this->model_pdf)?"'".$this->db->escape($this->model_pdf)."'":"null").',';
		$sql .= ' statut = '.(isset($this->statut)?$this->statut:"null").',';
		$sql .= ' multicurrency_amount = '.(isset($this->multicurrency_amount)?$this->multicurrency_amount:"null");

        
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
		$object = new Paiementfournadvance($this->db);

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

        $link = '<a href="'.DOL_URL_ROOT.'/advancepayment/card.php?id='.$this->id.'"';
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
		
		$this->ref = '';
		$this->entity = '';
		$this->tms = '';
		$this->datec = '';
		$this->datep = '';
		$this->amount = '';
		$this->fk_user_author = '';
		$this->fk_soc = '';
		$this->fk_facture = '';
		$this->origin = '';
		$this->originid = '';
		$this->fk_paiement = '';
		$this->num_paiement = '';
		$this->note = '';
		$this->fk_bank = '';
		$this->model_pdf = '';
		$this->statut = '';
		$this->multicurrency_amount = '';

		
	}

}

/**
 * Class PaiementfournadvanceLine
 */
class PaiementfournadvanceLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	
	public $ref;
	public $entity;
	public $tms = '';
	public $datec = '';
	public $datep = '';
	public $amount;
	public $fk_user_author;
	public $fk_soc;
	public $fk_facture;
	public $origin;
	public $originid;
	public $fk_paiement;
	public $num_paiement;
	public $note;
	public $fk_bank;
	public $model_pdf;
	public $statut;
	public $multicurrency_amount;

	/**
	 * @var mixed Sample line property 2
	 */
	
}
