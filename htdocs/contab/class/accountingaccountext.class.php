<?php
require_once DOL_DOCUMENT_ROOT.'/accountancy/class/accountingaccount.class.php';

class AccountingAccountext extends AccountingAccount
{
	var $array;
	var $aArray;
	var $lines;

	/**
   *  Create a document onto disk according to template module.
   *
   *  @param      string    $modele     Force model to use ('' to not force)
   *  @param    Translate $outputlangs  Object langs to use for output
   *  @param      int     $hidedetails    Hide details of lines
   *  @param      int     $hidedesc       Hide description
   *  @param      int     $hideref        Hide ref
   *  @return     int                 0 if KO, 1 if OK
   */
	public function generateDocument($modele, $outputlangs, $hidedetails=0, $hidedesc=0, $hideref=0)
	{
		global $conf,$user,$langs;

		$langs->load("contab");

		// Positionne le modele sur le nom du modele a utiliser
		if (! dol_strlen($modele))
		{
			if (! empty($conf->global->CONTAB_ADDON_PDF))
			{
				$modele = $conf->global->CONTAB_ADDON_PDF;
			}
			else
			{
				$modele = 'bg';
			}
		}

		$modelpath = "contab/core/modules/doc/";

		return $this->commonGenerateDocument($modelpath, $modele, $outputlangs, $hidedetails, $hidedesc, $hideref);
	}

	/**
	 * Return clicable name (with picto eventually)
	 *
	 * @param int $withpicto 0=No picto, 1=Include picto into link, 2=Only picto
	 * @return string Chaine avec URL
	 */
	function getNomUrladd($withpicto=0, $option='', $notooltip=0, $maxlen=24, $morecss='')
	{
		global $langs, $conf, $db;
		global $dolibarr_main_authentication, $dolibarr_main_demo;
		global $menumanager;


		$result = '';
		$companylink = '';

		$label = '<u>' . $langs->trans("Account") . '</u>';
		$label.= '<div width="100%">';
		$label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->account_number;
		$label.= '<div width="100%">';
		$label.= '<b>' . $langs->trans('Label') . ':</b> ' . $this->label;

		$link = '<a href="'.DOL_URL_ROOT.'/contab/accounts/card.php?id='.$this->id.'"';
		$link.= ($notooltip?'':' title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip'.($morecss?' '.$morecss:'').'"');
		$link.= '>';
		$linkend='</a>';

		if ($withpicto)
		{
			$result.=($link.img_object(($notooltip?'':$label), 'label', ($notooltip?'':'class="classfortooltip"')).$linkend);
			if ($withpicto != 2) $result.=' ';
		}
		$result.= $link . $this->account_number . $linkend;
		return $result;

	}

	function liste_array($lAdd=true,$ctaclass='',$empty="")
	{
		global $conf,$langs;

		$this->array = array();
		if ($empty)
			$this->array[0] = $langs->trans("Select");
		$sql = "SELECT ca.rowid, ca.account_number, ca.label";
		$sql.= " FROM ".MAIN_DB_PREFIX."accounting_account AS ca ";
		if ($lAdd)
			$sql.=" LEFT JOIN ".MAIN_DB_PREFIX."accounting_account_add AS caa ON caa.fk_accounting_account = ca.rowid";
		if ($lAdd && $ctaclass)
			$sql.=" WHERE caa.cta_class = '".trim($ctaclass)."'";

		$sql.= " ORDER BY ca.account_number ";

		$result=$this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			if ($num)
			{
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($result);
					$this->array[$obj->rowid] = $obj->account_number.' '.$obj->label;
					$i++;
				}

			}
			return $num;
		}
		else
		{
			dol_print_error($this->db);
			return -1;
		}
	}

	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND', $filterstatic='', $lView=false,$limittocurrentchart=1)
	{
		global $conf;
		$sql  = "SELECT t.rowid as rowid, t.datec, t.tms, t.fk_pcg_version, t.pcg_type, t.pcg_subtype, t.account_number, t.account_parent, t.label, t.fk_accounting_category, t.fk_user_author, t.fk_user_modif, t.active";
		$sql.= " , ta.cta_class, ta.cta_normal, ta.level ";
		$sql .= ", ca.label as category_label";
		$sql .= " FROM " . MAIN_DB_PREFIX . "accounting_account as t";
		$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."accounting_account_add as ta ON ta.fk_accounting_account = t.rowid";
		$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."c_accounting_category as ca ON t.fk_accounting_category = ca.rowid";

		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				$sqlwhere [] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
			}
		}
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
			$sql .= " AND a.entity IN (" . getEntity("contabseat", 1) . ")";
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' AND ' . implode(' '.$filtermode.' ', $sqlwhere);
		}
		if ($filterstatic) $sql.= $filterstatic;
		if (! empty($limittocurrentchart)) {
			$sql .= ' AND t.fk_pcg_version IN (SELECT pcg_version FROM ' . MAIN_DB_PREFIX . 'accounting_system WHERE rowid=' . $conf->global->CHARTOFACCOUNTS . ')';
		}

		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield,$sortorder);
		}
		if (!empty($limit)) {
			$sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
		}
		$this->lines = array();

		$resql = $this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql))
			{
				$line = new AccountingAccountLines();

				$line->id = $obj->rowid;
				$line->rowid = $obj->rowid;
				$line->datec = $obj->datec;
				$line->tms = $obj->tms;
				$line->fk_pcg_version = $obj->fk_pcg_version;
				$line->pcg_type = $obj->pcg_type;
				$line->pcg_subtype = $obj->pcg_subtype;
				$line->account_number = $obj->account_number;
				$line->account_parent = $obj->account_parent;
				$line->label = $obj->label;
				$line->account_category = $obj->fk_accounting_category;
				$line->account_category_label = $obj->category_label;
				$line->fk_user_author = $obj->fk_user_author;
				$line->fk_user_modif = $obj->fk_user_modif;
				$line->active = $obj->active;
				$line->status = $obj->active;
				$line->cta_class = $obj->cta_class;
				$line->cta_normal = $obj->cta_normal;

				$this->lines[$obj->rowid] = $line;
			}
			$this->db->free($resql);

			return $num;

		} else {
			$this->error = "Error " . $this->db->lasterror();
			$this->errors[] = "Error " . $this->db->lasterror();
		}
		return -1;
	}
	function list_account($accountini,$accountfin)
	{
		global $conf;
		$filter = "";
		$filter = " AND t.entity = ".$conf->entity;
		$filter.= " AND t.account_number BETWEEN '".$accountini."' AND '".$accountfin."' ";
		$num = $this->fetchAll('ASC','account_number',0,0,array(1=>1),'AND',$filter);
		$this->aArray = array();
		//$result=$this->db->query($sql);
		if ($num>0)
		{
			$lines = $this->lines;
			foreach ($lines AS $j => $line)
			{
				$this->aArray[$line->account_number] = $line->cta_normal;
			}
		}
		return $num;
	}

}

class AccountingAccountLines
{
	var $db;
	var $error;
	var $errors;
	var $id;
	var $rowid;
	var $datec; // Creation date
	var $fk_pcg_version;
	var $pcg_type;
	var $pcg_subtype;
	var $account_number;
	var $account_parent;
	var $account_category;
	var $label;
	var $fk_user_author;
	var $fk_user_modif;
	var $active;       // duplicate with status
	var $status;
	var $cta_class;
	var $cta_normal;
}
?>