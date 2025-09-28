<?php
require_once DOL_DOCUMENT_ROOT.'/budget/class/puprocedure.class.php';

class Puprocedureext extends Puprocedure
{
	public $lines;
	function fetch_lines($limit,$offset)
	{
		require_once DOL_DOCUMENT_ROOT.'/budget/class/puproceduredet.class.php';
		$obj = new Puproceduredet($this->db);
		$filter = array(1=>1);
		$filterstatic = " AND fk_pu_structure = ".$this->id;
		$res = $obj->fetchAll('ASC', 'sequen', $limit, $offset, $filter, 'AND',$filterstatic);
		if ($res>0)
		{
			$this->lines = $obj->lines;
			return $res;
		}
		else
			return -1; 
	}
}
?>