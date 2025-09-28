<?php
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobscontact.class.php';
class Mjobscontactext extends Mjobscontact
{
	//modificaciones
	function list_contact($id)
	{
		global $langs;
		$aArray = array();
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.fk_jobs,";
		$sql.= " t.fk_contact,";
		$sql.= " t.fk_charge,";
		$sql.= " t.detail,";
		$sql.= " t.tms,";
		$sql.= " t.statut";


		$sql.= " FROM ".MAIN_DB_PREFIX."m_jobs_contact as t";
		$sql.= " WHERE t.fk_jobs = ".$id;
		$sql.= " ORDER BY t.rowid ";
		dol_syslog(get_class($this)."::list_contact sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i = 0;
				while($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objcont = new Mjobscontact($this->db);

					$objcont->id    = $obj->rowid;

					$objcont->fk_jobs = $obj->fk_jobs;
					$objcont->fk_contact = $obj->fk_contact;
					$objcont->fk_charge = $obj->fk_charge;
					$objcont->detail = $obj->detail;
					$objcont->tms = $this->db->jdate($obj->tms);
					$objcont->statut = $obj->statut;

					$aArray[$obj->rowid] = $objcont;
					$i++;
				}
			}
			$this->db->free($resql);

			return $aArray;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::list_contact ".$this->error, LOG_ERR);
			return -1;
		}
	}
}
?>