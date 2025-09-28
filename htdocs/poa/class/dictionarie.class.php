<?php

class Dictionarie
{

	var $rowid;
	var $code;
	var $label;
	var $active;
	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $db)
	{
		$this->db = $db;
	}

	//$typecampo: i = integer; t=text;
	function fecth_dictionarie($selected, $table,$entity=1,$campoid='rowid',$campolabel='label',$camposearch="rowid",$typecampo='i',$filter='')
	{
		global $langs, $conf;
		$sql = "SELECT ".$campoid.",".$campolabel." FROM ".MAIN_DB_PREFIX.$table;
		$sql.= " WHERE ";
		if ($typecampo == 'i')
			$sql.= $camposearch ." = ".$selected;
		else
			$sql.= $camposearch ." = '".$selected."'";
		if ($entity>0)
			$sql.= " AND entity = ".$entity;

		$resql = $this->db->query($sql);

		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($num)
			{
				$obj = $this->db->fetch_object($resql);
				$this->rowid = $obj->$campoid;
				$this->label = $obj->$campolabel;
				$this->active = $obj->active;

				$this->db->free($resql);

			}
			return $num;
		}
		else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return - 1;
		}

	}
}
?>