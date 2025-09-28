<?php
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsmaterialused.class.php';
class Mjobsmaterialusedext extends Mjobsmaterialused
{

		//MODIFICADO
	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$fk_jobs    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getlist($fk_jobs)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.fk_jobs,";
		$sql.= " t.ref,";
		$sql.= " t.date_return,";
		$sql.= " t.description,";
		$sql.= " t.quant,";
		$sql.= " t.unit,";
		$sql.= " t.tms,";
		$sql.= " t.statut";


		$sql.= " FROM ".MAIN_DB_PREFIX."m_jobs_material_used as t";
		$sql.= " WHERE t.fk_jobs = ".$fk_jobs;

		dol_syslog(get_class($this)."::getlist sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->array = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($this->db->num_rows($resql))
			{
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objnew = new Mjobsmaterialused($this->db);
					$objnew->id    = $obj->rowid;
					$objnew->fk_jobs = $obj->fk_jobs;
					$objnew->ref = $obj->ref;
					$objnew->date_return = $this->db->jdate($obj->date_return);
					$objnew->description = $obj->description;
					$objnew->quant = $obj->quant;
					$objnew->unit = $obj->unit;
					$objnew->tms = $this->db->jdate($obj->tms);
					$objnew->statut = $obj->statut;

					$this->array[$obj->rowid] = $objnew;
					$i++;
				}
			}
			$this->db->free($resql);

			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
			return -1;
		}
	}


}
?>