<?php
require_once DOL_DOCUMENT_ROOT.'/mant/class/mwcts.class.php';

class Mwctsext extends Mwcts
{
	//get working class
	/**
	 *  Load object in memory from the database
	 *
	 *  @param  int     $id    Id object
	 *  @return int             <0 if KO, >0 if OK
	 */
	function fetch_working_class($typemant,$speciality)
	{
		global $langs;
		if (empty($typemant) || empty($speciality))
			return -1;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.working_class,";
		$sql.= " t.typemant,";
		$sql.= " t.speciality,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.date_create,";
		$sql.= " t.tms,";
		$sql.= " t.statut";


		$sql.= " FROM ".MAIN_DB_PREFIX."m_wcts as t";
		$sql.= " WHERE t.typemant = '".$typemant."'";
		$sql.= " AND t.speciality = '".$speciality."'";
		dol_syslog(get_class($this)."::fetch_working_class sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);

				$this->id    = $obj->rowid;

				$this->entity = $obj->entity;
				$this->working_class = $obj->working_class;
				$this->typemant = $obj->typemant;
				$this->speciality = $obj->speciality;
				$this->fk_user_create = $obj->fk_user_create;
				$this->date_create = $this->db->jdate($obj->date_create);
				$this->tms = $this->db->jdate($obj->tms);
				$this->statut = $obj->statut;


			}
			$this->db->free($resql);

			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch_working_class ".$this->error, LOG_ERR);
			return -1;
		}
	}
}
?>