<?php
require_once DOL_DOCUMENT_ROOT.'/budget/class/pustructure.class.php';

class Pustructureext extends Pustructure
{
	public $lines;
	
	public function fetch_lines($limit,$offset)
	{
		require_once DOL_DOCUMENT_ROOT.'/budget/class/pustructuredet.class.php';
		$obj = new Pustructuredet($this->db);
		$filter = array(1=>1);
		$filterstatic = " AND entity = ".$this->entity;
		$filterstatic.= " AND ref_structure = '".$this->ref."'";
		$filterstatic.= " AND type_structure = '".$this->type_structure."'";
		$res = $obj->fetchAll('ASC', 'sequen', $limit, $offset, $filter, 'AND',$filterstatic);
		if ($res>0)
		{
			$this->lines = $obj->lines;
			return $res;
		}
		else
			return -1; 
	}

	public function pu_select($selected='',$htmlname='fk_pu_structure',$htmloption='',$showempty=0,$campo='id',array $aData=array())
	{
		global $user;
		if (count($aData)>0) $this->lines = $aData;
		if (count($this->lines)>0)
		{
			$html.= '<select id="'.$htmlname.'" class="flat" name="'.$htmlname.'">';
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