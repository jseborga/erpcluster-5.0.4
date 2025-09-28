<?php
require_once DOL_DOCUMENT_ROOT.'/orgman/class/cpartida.class.php';

class Cpartidaext extends Cpartida
{
	var $aArray;
	function getElement()
	{

	}

	public function get_son($partida,$year,array $aArray=array())
	{
		global $langs,$conf;
		$this->aArray = $aArray;
		$filter = " AND t.code_father = '".$partida."'";
		$filter.= " AND t.period_year = ".$year;
		$res = $this->fetchAll('','',0,0,array(1=>1),'AND',$filter);
		if ($res>0)
		{
			$lines = $this->lines;
			foreach ($lines AS $j => $line)
			{
				$this->aArray[$line->code_father][$line->code]=$line->label;
					//buscamos si tiene hijos
				$this->aArray = $this->get_son($line->code,$year,$this->aArray);
			}
		}
		return $this->aArray;
	}
}
?>