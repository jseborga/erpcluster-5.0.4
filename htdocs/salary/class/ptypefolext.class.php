<?php
require_once DOL_DOCUMENT_ROOT.'/salary/class/ptypefol.class.php';

class Ptypefolext extends Ptypefol
{

	/**
	 *  Return combo list of activated countries, into language of user
	 *
	 *  @param  string  $selected       Id or Code or Label of preselected country
	 *  @param  string  $htmlname       Name of html select object
	 *  @param  string  $htmloption     Options html on select object
	 *  @param  string  $maxlength      Max length for labels (0=no limit)
	 *  @return string                  HTML string with select
	 */
	function select_typefol($selected='',$htmlname='fk_type_fol',$htmloption='',$maxlength=0,$showempty=0)
	{
		global $conf,$langs;

		$langs->load("salary@salary");

		$out='';
		$countryArray=array();
		$label=array();

		$sql = "SELECT rowid, ref as code_iso, detail as label";
		$sql.= " FROM ".MAIN_DB_PREFIX."p_type_fol";
		$sql.= " WHERE entity = ".$conf->entity;
		$sql.= " ORDER BY ref ASC";

		dol_syslog(get_class($this)."::select_typefol sql=".$sql);
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
					$countryArray[$i]['rowid']      = $obj->rowid;
					$countryArray[$i]['code_iso']   = $obj->code_iso;
					$countryArray[$i]['label']      = ($obj->code_iso && $langs->transnoentitiesnoconv("Typefol".$obj->code_iso)!="Typefol".$obj->code_iso?$langs->transnoentitiesnoconv("Typefol".$obj->code_iso):($obj->label!='-'?$obj->label:''));
					$label[$i]  = $countryArray[$i]['label'];
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
			dol_print_error($this->db);
		}

		return $out;
	}

}
?>