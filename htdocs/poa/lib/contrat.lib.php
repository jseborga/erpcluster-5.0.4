<?php
  /**
   *  Return list of other contracts for same company than current contract
   *
   *	@param	string		$option		'all' or 'others'
   *  @return array   				Array of contracts id
   */
require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';

function getListOfContracts($fk_soc=0,$option='all',$statut=0)
{
  $tab=array();
  global $db,$extrafields,$conf;
  $sql = "SELECT c.rowid, c.ref";
  $sql.= " FROM ".MAIN_DB_PREFIX."contrat as c";
  $sql.= " WHERE c.entity =".$conf->entity;
  if (!empty($fk_soc)) $sql.= " AND c.fk_soc =".$fk_soc;
  if ($option == 'others') $sql.= " AND c.rowid != ".$fk_soc;
  if ($statut) $sql.= " AND c.statut = ".$statut;
  //dol_syslog(get_class($this)."::getOtherContracts() sql=".$sql,LOG_DEBUG);
  
  $resql=$db->query($sql);
  if ($resql)
  {
      $num=$db->num_rows($resql);
      $i=0;
      while ($i < $num)
      {
		$obj = $db->fetch_object($resql);
	  	$contrat=new Contrat($db);
	  $contrat->fetch($obj->rowid);
	  $extralabels=$extrafields->fetch_name_optionals_label($contrat->table_element);
	  
	  $res=$contrat->fetch_optionals($contrat->id,$extralabels);
	  
	  $tab[]=$contrat;
	  $i++;
	}
      return $tab;
    }
  else
    {
      $error=$db->error();
      return -1;
    }
}

?>