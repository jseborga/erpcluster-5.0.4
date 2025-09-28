<?php
require_once DOL_DOCUMENT_ROOT.'/finint/class/requestcashdeplacement.class.php';

class Requestcashdeplacementext extends Requestcashdeplacement
{
	public $fk_type;
	public $num_chq;
	public $fk_account;
	public $lines;

	public $total_ht;
	public $total_ttc;


	/**
	 *  Create a document onto disk according to template model.
	 *
	 *  @param	    string		$modele			Force template to use ('' to not force)
	 *  @param		Translate	$outputlangs	Object lang to use for traduction
	 *  @param      int			$hidedetails    Hide details of lines
	 *  @param      int			$hidedesc       Hide description
	 *  @param      int			$hideref        Hide ref
	 *  @return     int          				0 if KO, 1 if OK
	 */
	public function generateDocument($modele, $outputlangs, $hidedetails=0, $hidedesc=0, $hideref=0)
	{
		global $conf, $user, $langs;

		$langs->load("finint");
		echo $modele;
		// Sets the model on the model name to use
		if (! dol_strlen($modele))
		{
			if (! empty($conf->global->FININT_ADDON_PDF))
			{
				$modele = $conf->global->FININT_ADDON_PDF;
			}
			else
			{
				$modele = 'requestcashrecharge';
			}
		}

		echo 'modelpath '.$modelpath = "finint/core/modules/doc/";

		return $this->commonGenerateDocument($modelpath, $modele, $outputlangs, $hidedetails, $hidedesc, $hideref);
	}


  	//lista las transferencias
	function getlisttransfer($fk_request_cash=0,$fk_request_cash_dest=0,$lBank=false,$filter='')
	{
		global $conf, $langs,$user;

		if ($user->societe_id) return -1;
	  // protection pour eviter appel par utilisateur externe

		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		$sql .= 't.entity,';
		$sql .= 't.ref,';
		$sql .= " t.fk_request_cash,";
		$sql .= " t.fk_request_cash_dest,";
		$sql .= " t.fk_projet_dest,";
		$sql .= " t.fk_account_from,";
		$sql .= " t.fk_account_dest,";
		$sql .= " t.url_id,";
		$sql .= " t.fk_bank,";
		$sql .= " t.fk_user_from,";
		$sql .= " t.fk_user_to,";
		$sql .= " t.fk_type,";
		$sql .= " t.fk_categorie,";
		$sql .= " t.dateo,";
		$sql .= " t.quant,";
		$sql .= " t.fk_unit,";
		$sql .= 'code_facture,';
		$sql .= 'code_type_purchase,';
		$sql .= 'type_operation,';
		$sql .= " t.nro_chq,";
		$sql .= " t.amount,";
		$sql .= " t.concept,";
		$sql .= " t.detail,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_approved,";
		$sql .= " t.date_dest,";
		$sql .= " t.date_create,";
		$sql .= " t.tms,";
		$sql .= " t.status";

		if ($lBank)
			$sql.= ", b.fk_account, b.fk_type, b.num_chq ";
		$sql.= " FROM ".MAIN_DB_PREFIX."request_cash_deplacement as t ";
		if ($lBank ==true)
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."bank AS b ON t.fk_bank = b.rowid";
		$sql.= " WHERE 1 " ;
		if (!empty($fk_request_cash))
			$sql.= " AND  t.fk_request_cash=".$fk_request_cash;
		if (!empty($fk_request_cash_dest))
			$sql.= " AND  t.fk_request_cash_dest=".$fk_request_cash_dest;
		$sql.= " AND t.concept = 'banktransfert'";
		if (!empty($filter)) $sql.= $filter;
		//$sql.= " AND ba.entity IN (".getEntity('bank_account', 1).")";
		//if ($user->admin) echo '<hr>rda '.$sql;
		$resql=$this->db->query($sql);

		$this->lines = array();
		if ($resql)
		{
			while ($obj=$this->db->fetch_object($resql))
			{
				$line = new Requestcashdeplacement($this->db);
				$line->id = $obj->rowid;
				$line->entity = $obj->entity;
				$line->ref = $obj->ref;
				$line->fk_request_cash = $obj->fk_request_cash;
				$line->fk_request_cash_dest = $obj->fk_request_cash_dest;
				$line->fk_projet_dest = $obj->fk_projet_dest;
				$line->fk_account_from = $obj->fk_account_from;
				$line->fk_account_dest = $obj->fk_account_dest;
				$line->fk_account = $obj->fk_account;
				$line->url_id = $obj->url_id;
				$line->fk_bank = $obj->fk_bank;
				$line->fk_user_from = $obj->fk_user_from;
				$line->fk_user_to = $obj->fk_user_to;
				$line->dateo = $this->db->jdate($obj->dateo);
				$line->quant = $obj->quant;
				$line->fk_unit = $obj->fk_unit;
				$line->code_facture = $obj->code_facture;
				$line->code_type_purchase = $obj->code_type_purchase;
				$line->type_operation = $obj->type_operation;
				$line->nro_chq = $obj->nro_chq;
				$line->amount = $obj->amount;
				$line->concept = $obj->concept;
				$line->detail = $obj->detail;
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_user_approved = $obj->fk_user_approved;
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->tms = $this->db->jdate($obj->tms);
				$line->status = $obj->status;
				$line->fk_type = $obj->fk_type;
				$line->num_chq = $obj->num_chq;
				$this->lines[] = $line;
			}

			return 1;
		}
		else
		{
			dol_print_error($this->db);
			$this->error=$this->db->error();
			return -1;
		}
	}

	/**
	 *	Returns the text label from units dictionary
	 *
	 * 	@param	string $type Label type (long or short)
	 *	@return	string|int <0 if ko, label if ok
	 */
	function getLabelOfUnit($type='long')
	{
		global $langs;

		if (!$this->fk_unit) {
			return '';
		}

		$langs->load('products');

		$this->db->begin();

		$label_type = 'label';

		if ($type == 'short')
		{
			$label_type = 'short_label';
		}

		$sql = 'select '.$label_type.' from '.MAIN_DB_PREFIX.'c_units where rowid='.$this->fk_unit;
		$resql = $this->db->query($sql);
		if($resql && $this->db->num_rows($resql) > 0)
		{
			$res = $this->db->fetch_array($resql);
			$label = $res[$label_type];
			$this->db->free($resql);
			return $label;
		}
		else
		{
			$this->error=$this->db->error().' sql='.$sql;
			dol_syslog(get_class($this)."::getLabelOfUnit Error ".$this->error, LOG_ERR);
			return -1;
		}
	}

		//MODIFICADO
	/**
	 *  Returns the reference to the following non used Order depending on the active numbering module
	 *  defined into ALMACEN_ADDON
	 *
	 *  @param	Societe		$soc  	Object thirdparty
	 *  @return string      		Order free reference
	 */
	function getNextNumRef($soc)
	{
		global $db, $langs, $conf;
		$langs->load("finint@finint");

		$dir = DOL_DOCUMENT_ROOT . "/finint/core/modules";
		$conf->global->FININT_REND_CASH_ADDON = 'mod_finint_ubuntuborendcash';
		if (! empty($conf->global->FININT_REND_CASH_ADDON))
		{
			$file = $conf->global->FININT_REND_CASH_ADDON.'.php';
		  	// Chargement de la classe de numerotation
			$classname = $conf->global->FININT_REND_CASH_ADDON;
			$result=include_once $dir.'/'.$file;
			if ($result)
			{
				$obj = new $classname();
				$numref = "";
				$numref = $obj->getNextValue($soc,$this);

				if ( $numref != "")
				{
					return $numref;
				}
				else
				{
					dol_print_error($db,"Requestcashdeplacementext::getNextNumRef ".$obj->error);
					return "";
				}
			}
			else
			{
				print $langs->trans("Error")." ".$langs->trans("Error_FININT_REND_CASH_ADDON_NotDefined");
				return "";
			}
		}
		else
		{
			print $langs->trans("Error")." ".$langs->trans("Error_FININT_REND_CASH_ADDON_NotDefined");
			return "";
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
	public function update_status(User $user)
	{
		$error = 0;

		dol_syslog(__METHOD__, LOG_DEBUG);

		// Clean parameters

		if (isset($this->status)) {
			$this->status = trim($this->status);
		}



		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';

		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
		$sql .= ' status = '.(isset($this->status)?$this->status:"null");

		$sql .= ' WHERE rowid=' . $this->id;

		$this->db->begin();

		$resql = $this->db->query($sql);
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
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
	 * Update object into database
	 *
	 * @param  User $user      User that modifies
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function update_fk_parent(User $user)
	{
		$error = 0;

		dol_syslog(__METHOD__, LOG_DEBUG);

		// Clean parameters

		if (isset($this->fk_parent)) {
			$this->fk_parent = trim($this->fk_parent);
		}

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';

		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'").',';
		$sql .= ' fk_parent = '.(isset($this->fk_parent)?$this->fk_parent:"null");

		$sql .= ' WHERE rowid=' . $this->id;

		$this->db->begin();

		$resql = $this->db->query($sql);
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
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
	function fetch_lines()
	{
		global $langs;
		//recuperamos todo de la misma linea
		$line = new stdClass();

		$line->rowid = $this->id;
		$line->desc = $this->detail;
		$line->qty = $this->quant;
		$line->subprice = $this->amount;
		$line->price = $this->amount;
		$line->fk_unit = $this->fk_unit?$this->fk_unit:1;
		$this->lines[] = $line;
		return 1;
	}

	/**
	 *  Retourne le libelle du status d'un user (actif, inactif)
	 *
	 *  @param	int		$mode          0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return	string 			       Label of status
	 */
	function getLibStatutext($mode=0)
	{
		return $this->LibStatutext($this->status,$mode);
	}

	/**
	 *  Return the status
	 *
	 *  @param	int		$status        	Id status
	 *  @param  int		$mode          	0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 5=Long label + Picto
	 *  @return string 			       	Label of status
	 */
	static function LibStatutext($status,$mode=0)
	{
		global $langs;

		if ($mode == 0)
		{
			$prefix='';
			if ($status == 3) return $langs->trans('Discharged');
			if ($status == 2) return $langs->trans('Inreview');
			if ($status == 1) return $langs->trans('Enabled');
			if ($status == 0) return $langs->trans('Draft');
		}
		if ($mode == 1)
		{
			if ($status == 3) return $langs->trans('Discharged');
			if ($status == 2) return $langs->trans('Inreview');
			if ($status == 1) return $langs->trans('Enabled');
			if ($status == 0) return $langs->trans('Draft');
		}
		if ($mode == 2)
		{
			if ($status == 3) return img_picto($langs->trans('Discharged'),'statut4').' '.$langs->trans('Discharged');
			if ($status == 2) return img_picto($langs->trans('Inreview'),'statut3').' '.$langs->trans('Inreview');
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut1').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut0').' '.$langs->trans('Draft');
		}
		if ($mode == 3)
		{
			if ($status == 3) return img_picto($langs->trans('Discharged'),'statut4');
			if ($status == 2) return img_picto($langs->trans('Inreview'),'statut3');
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut1');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut0');
		}
		if ($mode == 4)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut5').' '.$langs->trans('Draft');
		}
		if ($mode == 5)
		{
			if ($status == 3) return $langs->trans('Discharged').' '.img_picto($langs->trans('Discharged'),'statut4');
			if ($status == 2) return $langs->trans('Inreview').' '.img_picto($langs->trans('Inreview'),'statut3');
			if ($status == 1) return $langs->trans('Enabled').' '.img_picto($langs->trans('Enabled'),'statut1');
			if ($status == 0) return $langs->trans('Draft').' '.img_picto($langs->trans('Draft'),'statut0');
		}
		if ($mode == 6)
		{
			if ($status == 3) return $langs->trans('Discharged').' '.img_picto($langs->trans('Discharged'),'statut4');
			if ($status == 2) return $langs->trans('Inreview').' '.img_picto($langs->trans('Inreview'),'statut3');
			if ($status == 1) return $langs->trans('Enabled').' '.img_picto($langs->trans('Enabled'),'statut1');
			if ($status == 0) return $langs->trans('Draft').' '.img_picto($langs->trans('Draft'),'statut0');
		}
	}
}
?>