<?php
require_once DOL_DOCUMENT_ROOT.'/sales/class/entrepotbanksoc.class.php';

class Entrepotbanksocext extends Entrepotbanksoc
{
	    //MODIFICADO
    function getlistuser($id)
    {
      global $langs;
      $sql = "SELECT";
      $sql.= " t.rowid,";

      $sql.= " t.entity,";
      $sql.= " t.numero_ip,";
      $sql.= " t.fk_user,";
      $sql.= " t.fk_entrepotid,";
      $sql.= " t.fk_socid,";
      $sql.= " t.fk_cajaid,";
      $sql.= " t.fk_bankid,";
      $sql.= " t.fk_banktcid,";
      $sql.= " t.fk_subsidiaryid,";
      $sql.= " t.series,";
      $sql.= " t.status";
      $sql.= " FROM ".MAIN_DB_PREFIX."entrepot_bank_soc as t";
      $sql.= " WHERE t.fk_user = ".$id;

      dol_syslog(get_class($this)."::getlistuser sql=".$sql, LOG_DEBUG);
      $resql=$this->db->query($sql);
      $this->array = array();
      if ($resql)
        {
          if ($this->db->num_rows($resql))
        {
          $num = $this->db->num_rows($resql);
          $i = 0;
          while ($i < $num)
            {
              $obj = $this->db->fetch_object($resql);
              $objnew = new Entrepotbanksoc($this->db);
              $this->array[$obj->rowid] = $obj;
              $i++;
            }
        }
          $this->db->free($resql);
          return 1;
        }
      else
        {
          $this->error="Error ".$this->db->lasterror();
          dol_syslog(get_class($this)."::getlistuser ".$this->error, LOG_ERR);
          return -1;
        }
    }

}
?>