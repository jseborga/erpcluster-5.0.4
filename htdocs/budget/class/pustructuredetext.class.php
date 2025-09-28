<?php
require_once DOL_DOCUMENT_ROOT.'/budget/class/pustructuredet.class.php';

class Pustructuredetext extends Pustructuredet
{
	public $lines;
	public $max;
	public function max_ref($id)
	{
		$filter = array(1=>1);
		$filterstatic = " AND fk_pu_structure = ".$id;
		$res = $this->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic);
		$this->max = 0;
		if ($res>0)
		{
			if (count($this->lines))
			{
				foreach ($this->lines AS $i => $line)
				{
					if ($this->max <= $line->sequen) $this->max = $line->sequen;
				}
				$this->max+=5;
			}
			else
			{
				$this->max = 5;
			}
			return 1;
		}
		elseif($res == 0)
		{
			$this->max = 5;
			return 0;
		}
		else
			return -1;
	}

	public function pu_select($selected='',$htmlname='fk_pu_structure',$htmloption='',$showempty=0,$campo='id')
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
						$html.= '<option value="'.$obj->$campo.'" selected="selected">'.$obj->ref.' - '.$obj->detail.'</option>';
					}
					else
					{
						$html.= '<option value="'.$obj->$campo.'">'.$obj->ref.' - '.$obj->detail.'</option>';
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