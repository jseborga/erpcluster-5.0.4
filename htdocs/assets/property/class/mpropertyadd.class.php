<?php
require_once DOL_DOCUMENT_ROOT.'/assets/property/class/mproperty.class.php';

class Mpropertyadd extends Mproperty
{
		//MODIFICACION
	/**
	 *  Return combo list of activated countries, into language of user
	 *
	 *  @param	string	$selected       Id or Code or Label of preselected country
	 *  @param  string	$htmlname       Name of html select object
	 *  @param  string	$htmloption     Options html on select object
	 *  @param	string	$maxlength		Max length for labels (0=no limit)
	 *  @return string           		HTML string with select
	 */
	function select_property($selected='',$htmlname='fk_property',$htmloption='',$maxlength=0,$showempty=0,$aExclude='',$aInclude='')
	{
		global $conf,$langs;

		$langs->load("mant@mant");

		$out='';
		$countryArray=array();
		$label=array();

		$sql = "SELECT c.rowid, c.ref as label";
		$sql.= " FROM ".MAIN_DB_PREFIX."m_property AS c ";
		$sql.= " WHERE c.entity = ".$conf->entity;
		$sql.= " ORDER BY c.ref ASC";

		dol_syslog(get_class($this)."::select_property sql=".$sql);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$out.= '<select id="select'.$htmlname.'" class="flat selectpays" name="'.$htmlname.'" '.$htmloption.'>';
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
					if (empty($aExclude[$obj->rowid]))
					{
						if (!empty($aInclude))
						{
							if ($aInclude[$obj->rowid])
							{
								$countryArray[$i]['rowid'] 		= $obj->rowid;
								$countryArray[$i]['code_iso'] 	= $obj->code_iso;
								$countryArray[$i]['label']		= ($obj->code_iso && $langs->transnoentitiesnoconv("Property".$obj->code_iso)!="Property".$obj->code_iso?$langs->transnoentitiesnoconv("Property".$obj->code_iso):($obj->label!='-'?$obj->label:''));
								$label[$i] 	= $countryArray[$i]['label'];
							}
						}
						else
						{
							$countryArray[$i]['rowid'] 		= $obj->rowid;
							$countryArray[$i]['code_iso'] 	= $obj->code_iso;
							$countryArray[$i]['label']		= ($obj->code_iso && $langs->transnoentitiesnoconv("Property".$obj->code_iso)!="Property".$obj->code_iso?$langs->transnoentitiesnoconv("Property".$obj->code_iso):($obj->label!='-'?$obj->label:''));
							$label[$i] 	= $countryArray[$i]['label'];							
						}

					}
					$i++;
				}

				array_multisort($label, SORT_ASC, $countryArray);

				foreach ($countryArray as $row)
				{
					//print 'rr'.$selected.'-'.$row['label'].'-'.$row['code_iso'].'<br>';
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
			dol_print_error($this->db);
		}

		return $out;
	}

}
?>