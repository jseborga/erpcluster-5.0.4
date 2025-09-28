<?php

foreach((array) $aDetl AS $idreg => $row)
{
	$var = !$var;
	print "<tr $bc[$var]>";
	print '<td><div class="text">'.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.' '.$row['object'].'</div></td>';
	print '<td class="fk_unit">'.$row['unit'].'</td>';
	print '<td align="right"><div class="text" id="quant-'.$lineb->id.'">'.$row['quant'].'</div></td>';
	print '<td align="right"><div class="text" id="quant-'.$lineb->id.'">'.price(price2num($row['amount'],'MT')).'</div></td>';
	print '</tr>';
}
?>