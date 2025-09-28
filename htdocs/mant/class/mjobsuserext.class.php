<?php
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsuser.class.php';
class Mjobsuserext extends Mjobsuser
{

	//modificado
	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function list_jobsuser($fk_jobs)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.fk_jobs,";
		$sql.= " t.fk_user,";
		$sql.= " t.fk_level,";
		$sql.= " t.detail,";
		$sql.= " t.tms,";
		$sql.= " t.status";


		$sql.= " FROM ".MAIN_DB_PREFIX."m_jobs_user as t";
		$sql.= " WHERE t.fk_jobs = ".$fk_jobs;

		dol_syslog(get_class($this)."::list_jobsuser sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$aArray = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objnew = new Mjobsuser($this->db);

					$objnew->id    = $obj->rowid;

					$objnew->fk_jobs = $obj->fk_jobs;
					$objnew->fk_user = $obj->fk_user;
					$objnew->fk_level = $obj->fk_level;
					$objnew->detail = $obj->detail;
					$objnew->tms = $this->db->jdate($obj->tms);
					$objnew->statut = $obj->statut;
					$aArray[$obj->rowid] = $objnew;
					$i++;
				}
				return $aArray;
			}
			$this->db->free($resql);

			return $aArray;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::list_jobsuser ".$this->error, LOG_ERR);
			return -1;
		}
	}

}
?>