<?php

require_once(DOL_DOCUMENT_ROOT."/compta/deplacement/class/deplacement.class.php");

class Deplacementext extends Deplacement
{
	public $lines;
	public $total;
  	/**
   	* Constructor
   	*
   	* @param DoliDb $db Database handler
   	*/
   	public function __construct(DoliDB $db)
   	{
   		$this->statuts_short = array(-1=> 'Torefused',0 => 'Draft', 1 => 'Validated', 2 => 'Approved',3=>'Addtask',4=>'Closed');
   		$this->statuts_long = array(-1=> 'Torefused', 0 => 'Draft', 1 => 'Validated', 2 => 'Approved',3=>'Addtask',4=>'Closed');
   		$this->db = $db;
   		return 1;
   	}

   	/**
	* Load an object from database
	*
	* @param	int		$id		Id of record to load
	* @param	string	$ref	Ref of record
	* @return	int				<0 if KO, >0 if OK
	*/
	function getlist($fk_user=0, $filter='')
	{
		global $user;	
		$sql = "SELECT t.rowid, t.fk_user, r.fk_user_from, t.fk_projet, t.type, t.fk_statut, t.km, t.fk_soc, t.dated, t.note_private, t.note_public, t.extraparams, ";
		$sql.= " k.num_chq, ";
		$sql.= " r.rowid AS rcd_id, r.amount ";
		$sql.= " FROM ".MAIN_DB_PREFIX."deplacement AS t";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."bank_url AS b ON t.rowid = b.url_id ";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."bank AS k ON b.fk_bank = k.rowid ";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."request_cash_deplacement AS r ON r.url_id = t.rowid ";

		$sql.= " WHERE t.entity IN (".getEntity('deplacement').")";
		$sql.= " AND b.type = 'deplacement'";
		$sql.= " AND r.concept = 'deplacement'";
		if ($fk_user>0)
			$sql.= " AND fk_user = ".$fk_user;
		if ($filter)
			$sql.= $filter;
		//if ($user->admin) echo '<hr>'.$sql;
		dol_syslog(get_class($this)."::getlist", LOG_DEBUG);
		$rsql = $this->db->query($sql);
		$this->lines = array();
		$this->total = 0;
		if ( $rsql )
		{
			$num = $this->db->num_rows($rsql);
			//if ($user->admin) echo '<hr>num '.$num;
			while ($obj = $this->db->fetch_object($resql))
			{
				$line = new Deplacement($this->db);
				$line->id			= $obj->rowid;
				$line->ref			= $obj->rowid;
				$line->rcd_id               = $obj->rcd_id;
				$line->date			= $this->db->jdate($obj->dated);
				$line->fk_user		= $obj->fk_user;
				$line->fk_user_from = $obj->fk_user_from;
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_project		= $obj->fk_projet;
				$line->socid		= $obj->fk_soc;
				$line->km			= $obj->km;
				$line->type			= $obj->type;
				$line->statut	    = $obj->fk_statut;
				$line->note_private	= $obj->note_private;
				$line->note_public	= $obj->note_public;
				$line->fk_project	= $obj->fk_projet;
				$line->amount       = $obj->amount;
				$line->num_chq      = $obj->num_chq;
				$line->extraparams	= (array) json_decode($obj->extraparams, true);
				$this->lines[$obj->rowid] = $line;
				$this->total += $obj->amount;
			}
			$this->db->free($resql);
			return 1;
		}
		else
		{
			$this->error=$this->db->error();
			return -1;
		}
	}

}
?>