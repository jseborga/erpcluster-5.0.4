<?php

require_once DOL_DOCUMENT_ROOT.'/salary/class/pgenericfield.class.php';

class Pgenericfieldext extends Pgenericfield
{

	 /**
	 *  Load object in memory from the database
	 *
	 *  @param  int     $id    Id object
	 *  @param  boleano     $cond  true=sumar false=nosumar
	 *  @return int             <0 if KO, >0 if OK
	 */
	 function fetch_sequen_max($generic_table_ref,$cond=true)
	 {
	 	global $langs;
	 	$sql = "SELECT";
	 	$sql.= " generic_table_ref, MAX(t.sequen) AS sequen ";

	 	$sql.= " FROM ".MAIN_DB_PREFIX."p_generic_field as t";
	 	$sql.= " WHERE t.generic_table_ref = '".$generic_table_ref."'";
	 	$sql.= " GROUP BY generic_table_ref ";

	 	dol_syslog(get_class($this)."::fetch_sequen_max sql=".$sql, LOG_DEBUG);
	 	$resql=$this->db->query($sql);
	 	if ($resql)
	 	{
	 		if ($this->db->num_rows($resql))
	 		{
	 			$obj = $this->db->fetch_object($resql);
	 			if ($cond)
	 				$this->sequen = $obj->sequen + 1;
	 			else
	 				$this->sequen = $obj->sequen;

	 		}
	 		$this->db->free($resql);
	 		if ($cond)
	 			return 1;
	 		else
	 			return 0;
	 	}
	 	else
	 	{
	 		$this->error="Error ".$this->db->lasterror();
	 		dol_syslog(get_class($this)."::fetch_sequen_max ".$this->error, LOG_ERR);
	 		return -1;
	 	}
	 }

	/**
	 *  Load object in memory from the database
	 *
	 *  @param  int     $id    Id object
	 *  @return int             <0 if KO, >0 if OK
	 */
	function fetch_line($generic_table_ref,$sequen)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.generic_table_ref,";
		$sql.= " t.sequen,";
		$sql.= " t.field_value";


		$sql.= " FROM ".MAIN_DB_PREFIX."p_generic_field as t";
		$sql.= " WHERE t.generic_table_ref = '".$generic_table_ref."'";
		$sql.= " AND sequen = ".$sequen;

		dol_syslog(get_class($this)."::fetch_line sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);

				$this->id    = $obj->rowid;

				$this->generic_table_ref = $obj->generic_table_ref;
				$this->sequen = $obj->sequen;
				$this->field_value = $obj->field_value;


			}
			$this->db->free($resql);

			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch_line ".$this->error, LOG_ERR);
			return -1;
		}
	}
}
?>