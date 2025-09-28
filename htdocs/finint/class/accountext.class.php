<?php
require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';

class Accountext extends Account
{
	public $lines;

  //lista las transferencias
	function getlist($fk_account,$fk_request_cash,$dest=0)
	{
		global $conf, $langs;

		if ($user->societe_id) return -1; 
	  // protection pour eviter appel par utilisateur externe

		$sql = "SELECT b.rowid, b.datev as datefin, b.amount, b.label, b.fk_user_author, r.fk_user_create, r.fk_user_to, r.amount AS amountdep ";
		$sql.= " FROM ".MAIN_DB_PREFIX."bank as b ";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."bank_account as ba ON b.fk_account = ba.rowid ";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."request_cash_deplacement as r ON r.url_id = b.rowid ";

		$sql.= " WHERE b.fk_account=".$fk_account;
		if (empty($dest))
			$sql.= " AND  r.fk_request_cash=".$fk_request_cash;
		else
			$sql.= " AND  r.fk_request_cash_dest=".$fk_request_cash;

		$sql.= " AND r.concept = 'banktransfert'";
		$sql.= " AND ba.entity IN (".getEntity('bank_account', 1).")";
	//echo $sql;
		$resql=$this->db->query($sql);
		$this->lines = array();
		if ($resql)
		{
			while ($obj=$this->db->fetch_object($resql))
			{
				$line = new Account($this->db);
				$line->id = $obj->rowid;
				$line->datefin = $this->db->jdate($obj->datefin);
				$line->amount = $obj->amount;
				$line->amountdep = $obj->amountdep;
				$line->label = $obj->label;
				$line->fk_user_author = $obj->fk_user_author;
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_user_to = $obj->fk_user_to;
				$this->lines[$obj->rowid] = $line;
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

  //lista las transferencias por destino
	function getlistuser($fk_account,$fk_user)
	{
		global $conf, $langs;

	if ($user->societe_id) return -1;   // protection pour eviter appel par utilisateur externe

	$sql = "SELECT b.rowid, b.datev as datefin, b.amount, b.label, b.fk_user_author ";
	$sql.= " , r.fk_user_create ";
	$sql.= " FROM ".MAIN_DB_PREFIX."bank as b ";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."bank_account as ba ON b.fk_account = ba.rowid ";
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."request_cash_deplacement as r ON r.url_id = b.rowid ";
	
	$sql.= " WHERE b.fk_account=".$fk_account;
	$sql.= " AND  r.fk_user_to=".$fk_user;
	$sql.= " AND r.concept = 'banktransfert'";
	$sql.= " AND ba.entity IN (".getEntity('bank_account', 1).")";
	
	$resql=$this->db->query($sql);
	$this->lines = array();
	if ($resql)
	{
		while ($obj=$this->db->fetch_object($resql))
		{
			$line = new Account($this->db);
			$line->id = $obj->rowid;
			$line->datefin = $this->db->jdate($obj->datefin);
			$line->amount = $obj->amount;
			$line->label = $obj->label;
			$line->fk_user_create = $obj->fk_user_create;
			$line->fk_user_author = $obj->fk_user_author;
			$this->lines[$obj->rowid] = $line;
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

}
?>