<?php
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabseatflag.class.php';

class Contabseatflagext extends Contabseatflag
{
	    /**
     *  Load object in memory from the database
     *
     *  @param	int		$fk_seat    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function get_list($fk_seat)
    {
      global $langs,$conf;
      $aArray = array();
      if (empty($fk_seat))
	return $aArray;
      $sql = "SELECT";
      $sql.= " t.rowid ";
		
      $sql.= " FROM ".MAIN_DB_PREFIX."contab_seat_flag as t";
      $sql.= " WHERE t.fk_seat = ".$fk_seat;
      $sql.= " AND entity = ".$conf->entity;
      dol_syslog(get_class($this)."::get_list sql=".$sql, LOG_DEBUG);
      $resql=$this->db->query($sql);
      if ($resql)
        {
	  if ($this->db->num_rows($resql))
            {
	      $num = $this->db->num_rows($resql);
	      $i = 0;
	      while ($i < $num)
		{
		  $obj = $this->db->fetch_object($resql);	      
		  $aArray[$obj->rowid] = $obj->rowid;
		  $i++;
		}
            }
	  $this->db->free($resql);
	  return $aArray;
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