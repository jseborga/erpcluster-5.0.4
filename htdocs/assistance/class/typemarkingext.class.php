<?php
require_once DOL_DOCUMENT_ROOT.'/assistance/class/typemarking.class.php';

class Typemarkingext extends Typemarking
{
	//modificado

	/**
	*  Return status label of object
	*
	*  @param  int			$mode       0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto
	* 	@return string      			Label
	*/
	function getLibStatutx($mode=0)
	{
		return $this->LibStatut($this->statut, $mode);
	}

	/**
	*  Renvoi status label for a status
	*
	*  @param	int		$statut     id statut
	*  @param  int		$mode       0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto
	* 	@return string				Label
	*/
	function LibStatutx($statut, $mode=0)
	{
		global $langs;

		if ($mode == 0)
		{
			return $langs->trans($this->statuts_long[$statut]);
		}
	}
	/**
	*  Return combo list of activated countries, into language of user
	*
	*  @param	string	$selected       Id or Code or Label of preselected country
	*  @param  string	$htmlname       Name of html select object
	*  @param  string	$htmloption     Options html on select object
	*  @param	string	$maxlength		Max length for labels (0=no limit)
	*  @return string           		HTML string with select
	*/
	function select_typemarking($selected='',$htmlname='type_marking',$htmloption='',$maxlength=0,$showempty=0,$id=0,$required='',$filterarray='',$code='rowid',$campo='ref')
	{
		global $conf,$langs;
		$langs->load("assistance@assistance");
		if ($required)
			$required = 'required';
		$out='';
		$countryArray=array();
		$label=array();

		$sql = "SELECT c.rowid, c.ref AS ref, c.detail AS detail";
		$sql.= " FROM ".MAIN_DB_PREFIX."type_marking AS c ";
		$sql.= " WHERE c.entity = ".$conf->entity;
		if ($id)
			$sql.= " AND c.rowid NOT IN (".$id.")";
		//filtramos los que tiene fecha fixed
		$sql.= " AND (c.fixed_date <=0 OR c.fixed_date IS Null)";
		$sql.= " ORDER BY c.ref ASC";

		dol_syslog(get_class($this)."::select_typemarking sql=".$sql);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$out.= '<select id="select'.$htmlname.'" class="flat selectpays" '.$required.' name="'.$htmlname.'" '.$htmloption.'>';
			if ($showempty)
			{
				$out.= '<option value="-1"';
				if ($selected == -1) $out.= ' selected="selected"';
				$out.= '>&nbsp;</option>';
			}

			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($num)
			{
				$foundselected=false;

				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$lAdd = true;
					if (!empty($filterarray) && count($filterarray)>0)
					{
						if ($filterarray[$obj->$code])
							$lAdd = true;
						else
							$lAdd = false;
					}
					if ($lAdd)
					{
						$countryArray[$i]['rowid'] 	= $obj->$code;
						$countryArray[$i]['label']	= $obj->$campo;
						$label[$i] 	= $countryArray[$i]['label'];
					}
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
//if ($row['code_iso']) $out.= ' ('.$row['code_iso'] . ')';
					$out.= '</option>';
				}
			}
			$out.= '</select>';
		}
		else
		{
			dol_print_error($this->db);
		}

		return $out;
	}

}
?>