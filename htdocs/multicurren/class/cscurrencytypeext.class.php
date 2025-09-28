<?php
require_once DOL_DOCUMENT_ROOT.'/multicurren/class/cscurrencytype.class.php';

class Cscurrencytypeext extends Cscurrencytype
{
		//modificado
    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function getmax()
    {
      global $langs,$conf;
      $sql = "SELECT";
      $sql.= " MAX(t.order_currency) AS maximo";
		
      $sql.= " FROM ".MAIN_DB_PREFIX."cs_currency_type as t";
      $sql.= " WHERE t.entity = ".$conf->entity;
      dol_syslog(get_class($this)."::getmax sql=".$sql, LOG_DEBUG);
      $resql=$this->db->query($sql);
      if ($resql)
        {
	  if ($this->db->num_rows($resql))
            {
	      $obj = $this->db->fetch_object($resql);
	      if (empty($obj->maximo))
		$this->maximo = 1;
	      else
		$this->maximo = $obj->maximo+1;
            }
	  $this->db->free($resql);
	  
	  return 1;
        }
      else
        {
	  $this->error="Error ".$this->db->lasterror();
	  dol_syslog(get_class($this)."::getmax ".$this->error, LOG_ERR);
	  return -1;
        }
    }

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function get_currency_type_array($ref='')
	{
	  global $langs,$conf;
		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		
		$sql .= " t.entity,";
		$sql .= " t.ref,";
		$sql .= " t.label,";
		$sql .= " t.registry,";
		$sql .= " t.order_currency,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.datec,";
		$sql .= " t.dateu,";
		$sql .= " t.tms,";
		$sql .= " t.status";
	  
	  $sql.= " FROM ".MAIN_DB_PREFIX."cs_currency_type as t";
	  $sql.= " WHERE t.entity = ".$conf->entity;
	  $sql.= " ORDER BY t.order_currency ";
	  
	  if (!empty($ref))
	    $sql.= " AND t.ref = '".$ref."'";
	  dol_syslog(get_class($this)."::get_currency_type_array sql=".$sql, LOG_DEBUG);
	  $resql=$this->db->query($sql);
	  $this->array = array();
	  
	  if ($resql)
	    {
	      $num = $this->db->num_rows($resql);
	      if ($num)
		{
		  $i = 0;
		    while ($i < $num)
		      {
			$obj = $this->db->fetch_object($resql);
			$objnew = new Cscurrencytype($this->db);
			
			$objnew->id    = $obj->rowid;
			$objnew->entity = $obj->entity;
			$objnew->ref = $obj->ref;
			$objnew->registry = $obj->registry;
			$objnew->order_currency = $obj->order_currency;
			$objnew->state = $obj->state;
			$this->array[$obj->rowid] = $objnew;
			$i++;
		      }
		  return 1;
		}
	      $this->db->free($resql);
	      return array();
	    }
	  else
	    {
	      $this->error="Error ".$this->db->lasterror();
	      dol_syslog(get_class($this)."::get_currency_type_array ".$this->error, LOG_ERR);
	  return -1;
	    }
	}

	/**
	 *	Return label of status of object
	 *
	 *	@param      int	$mode       0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto
	 *	@param      int	$type       0=Shell, 1=Buy
	 *	@return     string      	Label of status
	 */
	function getLibStatutx($mode=0, $type=0)
	{
	  if($type==0)
	    return $this->LibStatut($this->statut,$mode,$type);
	  else
	    return $this->LibStatut($this->statut_ref,$mode,$type);
	}

	/**
	 *	Return label of a given status
	 *
	 *	@param      int		$status     Statut
	 *	@param      int		$mode       0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto
	 *	@param      int		$type       0=Status "to sell", 1=Status "to buy"
	 *	@return     string      		Label of status
	 */
	function LibStatutx($status,$mode=0,$type=0)
	{
	  global $langs;
	  $langs->load('poa@poa');
	  
	  if ($mode == 0)
	    {
	      if ($status == 0) return img_picto($langs->trans('Notapproved'),'statut5').' '.($type==0 ? $langs->trans('Notapproved'):$langs->trans('Reformulation unapproved'));
	      if ($status == 1) return img_picto($langs->trans('Approved'),'statut4').' '.($type==0 ? $langs->trans('Approved'):$langs->trans('Reformulation approved'));
	      if ($status == 2) return img_picto($langs->trans('Canceled'),'statut8').' '.($type==0 ? $langs->trans('Canceled'):$langs->trans('Canceled'));
	    }

	  if ($mode == 2)
	    {
	      if ($status == 0) return img_picto($langs->trans('Notapproved'),'statut5').' '.($type==0 ? $langs->trans('Notapproved'):$langs->trans('Reformulation unapproved'));
	      if ($status == 1) return img_picto($langs->trans('Approved'),'statut4').' '.($type==0 ? $langs->trans('Approved'):$langs->trans('Reformulation approved'));
	      if ($status == 2) return img_picto($langs->trans('Canceled'),'statut8').' '.($type==0 ? $langs->trans('Canceled'):$langs->trans('Canceled'));
	    }
	  
	  return $langs->trans('Unknown');
	}
}
?>