<?php

foreach((array) $aDet AS $detail => $row)
{
	$var = !$var;
	print "<tr $bc[$var]>";
	print '<td><div class="text">'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&group='.$group.'&idr='.$row['fk_product'].'">'.'&nbsp;&nbsp;&nbsp;&nbsp;'.img_picto('','edit_add').' '.$detail.'</a></div></td>';
	print '<td class="fk_unit">'.$row['unit'].'</td>';
	print '<td align="right"><div class="text" id="quant-'.$lineb->id.'">'.$row['quant'].'</div></td>';
	print '<td align="right"><div class="text" id="quant-'.$lineb->id.'">'.price(price2num($row['amount'],'MT')).'</div></td>';
	print '</tr>';
	if ($idr == $row['fk_product'])
	{
		$aDetl = $aGroupdetl[$group][$idr];
		include DOL_DOCUMENT_ROOT.'/monprojet/task/tpl/resourcegl.tpl.php';
	}
}
?>