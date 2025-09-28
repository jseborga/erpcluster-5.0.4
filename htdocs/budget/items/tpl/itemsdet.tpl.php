<?php

$filter = array(1=>1);
$filterstatic = " AND t.fk_pu_structure = ".$fk_pu_structure;
$filterstatic.= " AND t.fk_item = ".$id;
$objectdet->fetchAll($sortorder, $sortfield, 0, 0, $filter, 'AND',$filterstatic);
foreach((array) $objectdet->lines AS $i => $line)
{
	print '<tr>';
	//print '<td>'.$line->ref.'</td>';
	print '<td>'.$line->detail.'</td>';
	print '<td>'.$line->fk_unit.'</td>';
	print '<td>'.$line->quant.'</td>';
	print '<td>'.$line->price.'</td>';
	print '<td>'.price(price2num($line->quant * $line->price,'MT')).'</td>';
	print '<td>'.$langs->trans('??').'</td>';
	print '</tr>';
}
?>