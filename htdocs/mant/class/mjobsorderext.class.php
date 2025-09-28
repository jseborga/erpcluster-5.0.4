<?php
require_once DOL_DOCUMENT_ROOT.'/mant/class/mjobsorder.class.php';
class Mjobsorderext extends Mjobsorder
{
	//modificaciones
	function list_order($id)
	{
	  global $langs;
	  $aArray = array();
	  $sql = "SELECT";
	  $sql.= " t.rowid,";

	  $sql.= " t.fk_jobs,";
	  $sql.= " t.fk_product,";
	  $sql.= " t.order_number,";
	  $sql.= " t.date_order,";
	  $sql.= " t.description,";
	  $sql.= " t.quant,";
	  $sql.= " t.unit,";
	  $sql.= " t.tms,";
	  $sql.= " t.statut";

	  $sql.= " FROM ".MAIN_DB_PREFIX."m_jobs_order as t";
	  $sql.= " WHERE t.fk_jobs = ".$id;
	  $sql.= " ORDER BY t.rowid ";
	  dol_syslog(get_class($this)."::list_order sql=".$sql, LOG_DEBUG);
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
		      $objord = new Mjobsorder($this->db);

		      $objord->id    = $obj->rowid;

		      $objord->fk_jobs = $obj->fk_jobs;
		      $objord->fk_product = $obj->fk_product;
		      $objord->order_number = $obj->order_number;
		      $objord->date_order = $this->db->jdate($obj->date_order);
		      $objord->description = $obj->description;
		      $objord->quant = $obj->quant;
		      $objord->unit = $obj->unit;
		      $objord->tms = $this->db->jdate($obj->tms);
		      $objord->statut = $obj->statut;

		      $aArray[$obj->rowid] = $objord;
		      $i++;
		    }
		}
	      $this->db->free($resql);

	      return $aArray;
	    }
	  else
	    {
	      $this->error="Error ".$this->db->lasterror();
	      dol_syslog(get_class($this)."::list_order ".$this->error, LOG_ERR);
	      return -1;
	    }
	}


}
?>