<?php
require_once DOL_DOCUMENT_ROOT.'/fiscal/class/subsidiary.class.php';

class Subsidiaryext extends Subsidiary
{
	function select_subsidiary($selected='',$htmlname='fk_subsidiary',$htmloption='',$maxlength=0,$showempty=0)
	{
		global $conf,$langs;

		$langs->load("ventas@ventas");

		$out='';
		$countryArray=array();
		$label=array();

		$sql = "SELECT c.rowid, c.ref as code_iso, c.label as label";
		$sql.= " FROM ".MAIN_DB_PREFIX."subsidiary AS c ";
		$sql.= " WHERE c.entity = ".$conf->entity;
		$sql.= " ORDER BY c.ref ASC";

		dol_syslog(get_class($this)."::select_subsidiary sql=".$sql);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$out.= '<select id="select'.$htmlname.'" class="flat selectsubsidiary" name="'.$htmlname.'" '.$htmloption.'>';
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
					$countryArray[$i]['rowid']        = $obj->rowid;
					$countryArray[$i]['code_iso']     = $obj->code_iso;
					$countryArray[$i]['label']        = ($obj->code_iso && $langs->transnoentitiesnoconv("Course".$obj->code_iso)!="Course".$obj->code_iso?$langs->transnoentitiesnoconv("Course".$obj->code_iso):($obj->label!='-'?$obj->label:''));
					$label[$i]    = $countryArray[$i]['label'];
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