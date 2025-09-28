<?php

class FormTask extends Form
{

	/**
   *  Output html form to select a members
   *
   *	@param	string	$selected       Preselected type
   *	@param  string	$htmlname       Name of field in form
   *  @param  string	$filter         optional filters criteras (example: 's.rowid <> x')
   *	@param	int		$showempty		Add an empty field
   * 	@param	int		$showtype		Show third party type in combolist (customer, prospect or supplier)
   * 	@param	int		$forcecombo		Force to use combo box
   *  @param	array	$events			Event options to run on change. Example: array(array('method'=>'getContacts', 'url'=>dol_buildpath('/core/ajax/contacts.php',1), 'htmlname'=>'contactid', 'params'=>array('add-customer-contact'=>'disabled')))
   *	@param	int		$limit			Maximum number of elements
   * 	@return	string					HTML string with
   *  @deprecated						Use select_thirdparty instead
	*/
	function select_task($selected='', $htmlname='ref', $filter='', $showempty=0, $showtype=0, $forcecombo=0, $events=array(), $filterkey='',$outputmode=0,$limit=0,$required='',$filtergroup=0,$morecss='minwidth100',$moreparam='',$campo='ref')
	{
		return $this->select_task_list($selected, $htmlname, $filter, $showempty, $showtype, $forcecombo, $events, $filterkey,$outputmode,$limit,$required,$filtergroup,$morecss,$moreparam,$campo);
	}

	/**
   *  Output html form to select a members
   *
   *	@param	string	$selected       Preselected type
   *	@param  string	$htmlname       Name of field in form
   *  @param  string	$filter         optional filters criteras (example: 's.rowid <> x')
   *	@param	int		$showempty		Add an empty field
   * 	@param	int		$showtype		Show third party type in combolist (customer, prospect or supplier)
   * 	@param	int		$forcecombo		Force to use combo box
   *  @param	array	$events			Event options. Example: array(array('method'=>'getContacts', 'url'=>dol_buildpath('/core/ajax/contacts.php',1), 'htmlname'=>'contactid', 'params'=>array('add-customer-contact'=>'disabled')))
   *  @param	string	$filterkey		Filter on key value
   *  @param	int		$outputmode		0=HTML select string, 1=Array
   *  @param	int		$limit			Limit number of answers
   * 	@return	string					HTML string with
	*/
	function select_task_list($selected='',$htmlname='ref',$filter='',$showempty=0, $showtype=0, $forcecombo=0, $events=array(), $filterkey='', $outputmode=0, $limit=0, $required='',$filtergroup=0,$morecss='minwidth100',$moreparam='',$campo='ref')
	{
		global $conf,$user,$langs;
		if (!empty($required))
		{
			$forcefocus = 1;
			$required = ' required="required"';
		}
		if (!empty($selected)) 
			$selected = trim($selected);
		$out=''; $num=0;
		$outarray=array();

		$sql = "SELECT t.rowid, t.rowid as taskid, t.ref as ref, t.label as detail, t.description, t.fk_task_parent, ";
		$sql.= "t.duration_effective, t.progress,";
		$sql.= " t.dateo as date_start, t.datee as date_end, t.planned_workload, t.rang";
		$sql.= " FROM ".MAIN_DB_PREFIX."projet_task as t";
		if ($filtergroup)
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."projet_task_add AS ta ON ta.fk_task = t.rowid ";
		$sql.= " WHERE t.entity = ".$conf->entity;
		if ($filter) $sql.= " AND (".$filter.")";
		if ($filtergroup)
		{
			$sql.= " AND ta.c_grupo <> 1";
			$sql.= " AND t.fk_statut < 2";
		}
		//if (! empty($conf->global->MONPROJET_HIDE_INACTIVE_IN_COMBOBOX)) $sql.= " AND t.fk_statut<>0 ";
	// Add criteria
		if ($filterkey && $filterkey != '')
		{
			$sql.=" AND (";
			if (! empty($conf->global->MONPROJET_DONOTSEARCH_ANYWHERE))  
	 // Can use index
			{
				$sql.="(t.ref LIKE '".$this->db->escape($filterkey)."%')";
			}
			else
			{
		// For natural search
				$scrit = explode(' ', $filterkey);
				foreach ($scrit as $crit)
				{
					$sql.=" AND (";
					$sql.=" t.ref LIKE '%".$this->db->escape($crit)."%'";
					$sql.= " OR t.label LIKE '%".$this->db->escape($crit)."%'";
					$sql.=")";
				}
			}
			$sql.=")";
		}
		//fin sql
		$sql.=$this->db->order("t.label","ASC");
		if ($limit > 0) $sql.=$this->db->plimit($limit);

		dol_syslog(get_class($this)."::select_task_list", LOG_DEBUG);
		$resql=$this->db->query($sql);

		if ($resql)
		{
			if ($conf->use_javascript_ajax  && ! $forcecombo)
			{
				include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
				$comboenhancement = ajax_combobox($htmlname, $events, $conf->global->MONPROJET_USE_SEARCH_TO_SELECT);
				$out.= $comboenhancement;
				$nodatarole=($comboenhancement?' data-role="none"':'');

			}

			// Construct $out and $outarray
			$maxlength = '';
			$size = 600;
			if ($size>0) $maxlength = ' style=" width:'.$size.'px;" ';
			$out.= '<select id="'.$htmlname.'" class="flat'.($morecss?' '.$morecss:'').'"'.($moreparam?' '.$moreparam:'').' name="'.$htmlname.'"'.$nodatarole.'>'."\n";

			$textifempty='';
	// Do not use textempty = ' ' or '&nbsp;' here, or search on key will search on ' key'.
	//$textifempty=' ';
	//if (! empty($conf->use_javascript_ajax) || $forcecombo) $textifempty='';
			//if ($showempty) $out.= '<option value="-1">'.$textifempty.'</option>'."\n";
            if (! empty($conf->global->MONPROJET_USE_SEARCH_TO_SELECT)) 
            {
                if ($showempty && ! is_numeric($showempty)) $textifempty=$langs->trans($showempty); 
                else $textifempty.=$langs->trans("All");
            }
            if ($showempty) $out.= '<option value="-1">'.$textifempty.'</option>'."\n";


			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($num)
			{
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$label='';
					$label=dol_trunc($obj->ref.' '.$obj->detail,90);

					if ($selected && $selected == $obj->$campo)
					{
						$out.= '<option value="'.$obj->$campo.'" selected="selected">'.$label.'</option>';
					}
					else
					{
						$out.= '<option value="'.$obj->$campo.'">'.dol_trunc($label,48).'</option>';
					}
					array_push($outarray, array('key'=>$obj->$campo, 'value'=>$obj->$campo, 'label'=>dol_trunc($label,70)));

					$i++;
					if (($i % 10) == 0) $out.="\n";
				}
			}
			$out.= '</select>'."\n";
		}
		else
		{
			dol_print_error($this->db);
		}

		$this->result=array('nbofitem'=>$num);

		if ($outputmode) return $outarray;
		return $out;
	}

}
?>