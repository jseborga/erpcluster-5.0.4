<?php
require_once DOL_DOCUMENT_ROOT.'/budget/class/typeitem.class.php';

class Typeitemext extends Typeitem
{
		//MODIFICADO
	function select_typeitem($selected='',$htmlname='fk_type_item',$htmloption='',$showempty=0,$showlabel=0,$campo='rowid')
	{
		global $langs, $conf;
		$sql = "SELECT f.rowid, f.ref AS code, f.detail AS libelle FROM ".MAIN_DB_PREFIX."type_item AS f ";
		$sql.= " WHERE ";
		$sql.= " f.statut = 1";
		$sql.= " ORDER BY f.detail";
		$resql = $this->db->query($sql);
		$html = '';
		
		if ($selected <> 0 && $selected == '-1')
		{
			if ($showlabel > 0)
			{
				return $langs->trans('To be defined'); 
			}
		}
		
		if ($resql)
		{
			$html.= '<select class="flat" name="'.$htmlname.'">';
			if ($showempty) 
			{
				$html.= '<option value="0">&nbsp;</option>';
			}
			if ($selected <> 0 && $selected == '-1')
			{
				$html.= '<option value="-1" selected="selected">'.$langs->trans('To be defined').'</option>';
				if ($showlabel)
				{
					return $langs->trans('To be defined');
				}
			}
			if (empty($selected) && $showlabel)
				return '';
	      // else
	      // 	$html.= '<option value="-1">'.$langs->trans('To be defined').'</option>';
			
			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($num)
			{
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					if (!empty($selected) && $selected == $obj->$campo)
					{
						$html.= '<option value="'.$obj->$campo.'" selected="selected">'.$obj->libelle.'</option>';
						if ($showlabel)
						{
							return $obj->libelle;
						}
					}
					else
					{
						$html.= '<option value="'.$obj->$campo.'">'.$obj->libelle.'</option>';
					}
					$i++;
				}
			}
			$html.= '</select>';
			if ($showlabel)
				return $langs->trans('to be defined');
			return $html;
		}
	}
	
}

?>