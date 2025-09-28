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
function select_aprob($selected='',$htmlname='fk_aprobsup',$htmloption='',$maxlength=0,$showempty=0)
{
  global $db,$conf,$langs,$objectad,$objectch;
  
  $langs->load("salary@salary");
  
  $out='';
  $countryArray=array();
  $label=array();
  $sql = "SELECT d.rowid, d.type, d.fk_value";
  $sql.= " FROM ".MAIN_DB_PREFIX."p_salary_aprob AS d ";
  $sql.= " WHERE d.entity = ".$conf->entity;
  $sql.= " ORDER BY d.type ASC";
  
  $resql=$db->query($sql);
  if ($resql)
    {
      $out.= '<select id="select'.$htmlname.'" class="flat selectpays" name="'.$htmlname.'" '.$htmloption.'>';
      if ($showempty)
	{
	  $out.= '<option value="-1"';
	  if ($selected == -1) $out.= ' selected="selected"';
	  $out.= '>&nbsp;</option>';
	}
      
      $num = $db->num_rows($resql);
      $i = 0;
      if ($num)
	{
	  $foundselected=false;
	  
	  while ($i < $num)
	    {
	      $obj = $db->fetch_object($resql);
	      if ($obj->type == 1)
		{
		  //buscamos persona
		  $objectad->fetch($obj->fk_value);
		  $obj->code_iso = $langs->trans('Name').' '.$objectad->firstname.' '.$objectad->lastname;
		}
	      if ($obj->type == 2)
		{
		  //buscamos cargo
		  $objectch->fetch($obj->fk_value);
		  $obj->code_iso = $langs->trans('Charge').' '.$objectad->firstname.' '.$objectad->lastname;
		}

	      $countryArray[$i]['rowid'] 	= $obj->rowid;
	      $countryArray[$i]['code_iso'] 	= $obj->code_iso;
	      $countryArray[$i]['label']	= ($obj->code_iso && $langs->transnoentitiesnoconv("Approver".$obj->code_iso)!="Approver".$obj->code_iso?$langs->transnoentitiesnoconv("Approver".$obj->code_iso):($obj->label!='-'?$obj->label:''));
	      $label[$i] 	= $countryArray[$i]['label'];
	      $i++;
	    }
	  
	  array_multisort($label, SORT_ASC, $countryArray);
	  
	  foreach ($countryArray as $row)
	    {
	      if ($selected && $selected != '-1' && ($selected == $row['rowid'] || $selected == $row['code_iso'] || $selected == $row['label']) )
		{
		  $foundselected=true;
		  $out.= '<option value="'.$row['rowid'].'" selected="selected">';
		}
	      else
		{
		  $out.= '<option value="'.$row['rowid'].'">';
		}
	      $out.= dol_trunc($row['label'],$maxlength,'middle');
	      if ($row['code_iso']) $out.= ' ('.$row['code_iso'] . ')';
	      $out.= '</option>';
	    }
	}
      $out.= '</select>';
    }
  else
    {
      dol_print_error($db);
    }
  
  return $out;
}
?>