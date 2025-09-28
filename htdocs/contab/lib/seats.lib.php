<?php
    /**
     *  Return combo list of activated countries, into language of user
     *
     *  @param	string	$selected       Id or Code or Label of preselected country
     *  @param  string	$htmlname       Name of html select object
     *  @param  string	$htmloption     Options html on select object
     *  @param	string	$maxlength		Max length for labels (0=no limit)
     *  @return string           		HTML string with select
     */


function seat_bank()
{
  global $conf,$langs,$db;

  $langs->load("contab@contab");
  
  $out='';
  $countryArray=array();
  $label=array();
  $i = 1;
  //abrimos la tabla llx_bank
  $sql = "SELECT b.rowid, b.datec, b.amount, b.label, b.fk_account, b.fk_user_author, b.fk_type, b.num_chq, b.note, b.banque FROM llx_bank AS b ";
  $sql.= " INNER JOIN llx_bank_account AS ba ON ba.rowid = b.fk_account ";
  $sql.= " LEFT JOIN llx_contab_bank AS cb ON b.rowid = cb.fk_bank ";
  $sql.= " WHERE cb.rowid IS NULL ";
  $sql.= " AND ba.entity = ".$conf->entity;
  $sql.= " ORDER BY b.datec, b.rowid ";
  $result = $db->query($sql);
  if ($result)
    {
      $num = $db->num_rows($result);
      $i = 0;
      if ($num) 
	{
	  $var=True;
	  $grupo = 0;
	  $datec = '';
	  while ($i < $num)
	    {
	      $objp = $db->fetch_object($result);
	      if ($objp->datec != $datec)
		{
		  $datec = $objp->datec;
		  $grupo++;
		}
	      $objp->group = $grupo;
	      $aArraySeat[$objp->group][$objp->rowid] = $objp;
	      $i++;
	    }
	}
      
      $db->free($result);
      
    }
  return $aArraySeat;
}
?>