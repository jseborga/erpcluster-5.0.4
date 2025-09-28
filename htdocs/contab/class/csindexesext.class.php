<?php
require_once DOL_DOCUMENT_ROOT.'/contab/class/csindexes.class.php';

class Csindexesext extends Csindexes
{
	   /**
     *  Load object in memory from the database
     *
     *  @param	int		$countryd    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetch_last($country)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.country,";
		$sql.= " t.date_ind,";
		$sql.= " t.currency1,";
		$sql.= " t.currency2,";
		$sql.= " t.currency3,";
		$sql.= " t.currency4,";
		$sql.= " t.currency5,";
		$sql.= " t.currency6";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."cs_indexes as t";
        $sql.= " WHERE t.country = ".$country;
	$sql.= " ORDER BY t.date_ind DESC ";
	$sql.= $this->db->plimit(0, 1);

    	dol_syslog(get_class($this)."::fetch_last sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;
                
		$this->country = $obj->country;
		$this->date_ind = $this->db->jdate($obj->date_ind);
		$this->currency1 = $obj->currency1;
		$this->currency2 = $obj->currency2;
		$this->currency3 = $obj->currency3;
		$this->currency4 = $obj->currency4;
		$this->currency5 = $obj->currency5;
		$this->currency6 = $obj->currency6;
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