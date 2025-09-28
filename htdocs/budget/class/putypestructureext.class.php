<?php
require_once DOL_DOCUMENT_ROOT.'/budget/class/putypestructure.class.php';

class Putypestructureext extends Putypestructure
{

	public function putype_select($selected='',$htmlname='type_structure',$htmloption='',$showempty=0,$campo='id')
	{
		global $user;
		if (count($this->lines)>0)
		{
			$html.= '<select class="flat" name="'.$htmlname.'">';
			if ($showempty) 
			{
				$html.= '<option value="0">&nbsp;</option>';
			}
			if ($selected <> 0 && $selected == '-1')
			{
				$html.= '<option value="-1" selected="selected">'.$langs->trans('To be defined').'</option>';
			}
			$num = count($this->lines);
			$i = 0;
			if ($num)
			{
				foreach ($this->lines AS $j => $obj)
				{
					if (!empty($selected) && $selected == $obj->$campo)
					{
						$html.= '<option value="'.$obj->$campo.'" selected="selected">'.$obj->code.' - '.$obj->label.'</option>';
					}
					else
					{
						$html.= '<option value="'.$obj->$campo.'">'.$obj->code.' - '.$obj->label.'</option>';
					}
					$i++;
				}
			}
			$html.= '</select>';
			return $html;
		}
	}

}
?>