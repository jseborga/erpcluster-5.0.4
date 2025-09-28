<?php
require_once DOL_DOCUMENT_ROOT.'/contab/class/cscurrencytype.class.php';

class Cscurrencytypeext extends Cscurrencytype
{
		/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function get_currency_type_array($ref='')
	{
	  global $langs,$conf;
	  $sql = "SELECT";
	  $sql.= " t.rowid,";
	  $sql.= " t.entity,";
	  $sql.= " t.ref,";
	  $sql.= " t.label,";
	  $sql.= " t.registry,";
	  $sql.= " t.state";
	  
	  
	  $sql.= " FROM ".MAIN_DB_PREFIX."cs_currency_type as t";
	  $sql.= " WHERE t.entity = ".$conf->entity;
	  
	  if (!empty($ref))
	    $sql.= " AND t.ref = '".$ref."'";
	  dol_syslog(get_class($this)."::get_currency_type_array sql=".$sql, LOG_DEBUG);
	  $resql=$this->db->query($sql);
	  if ($resql)
	    {
	      $num = $this->db->num_rows($resql);
	      if ($num)
		{
		  $i = 0;
		    while ($i < $num)
		      {
			$obj = $this->db->fetch_object($resql);
			$currency_array[$obj->rowid] = 
			array('id'       => $obj->rowid,
			      'entity'   => $obj->entity,
			      'ref'      => $obj->ref,
			      'label'    => $obj->label,
			      'registry' => $obj->registry,
			      'state'    => $obj->state
			      );
			$i++;
		      }
		  return $currency_array;
		}
	      $this->db->free($resql);
	      return array();
	    }
	  else
	    {
	      $this->error="Error ".$this->db->lasterror();
	      dol_syslog(get_class($this)."::get_array ".$this->error, LOG_ERR);
	  return -1;
	    }
	}

}
?>