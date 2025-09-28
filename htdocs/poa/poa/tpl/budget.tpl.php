<?php
if ($filtromenu['f1']==True)
{
	if ($numCol[91])
	{
	//titlepara budget
		$titlebudget = $langs->trans('Totalbudget').': '.price($obj->amount);		
		$titlebudget.= '&#13;'.$langs->trans('Totalactivity').': '.price($aTotalAct['budget']);
		$titlebudget.= '&#13;'.$langs->trans('Balance').': '.price($obj->amount-$aTotalAct['budget']);
		if ($aTotalActpen['budget']>0)
			$titlebudget.= '&#13;'.$langs->trans('Pending').': '.price($aTotalActpen['budget']);
		
		if ($obj->version == 0)
		{
			print '<td align="right" '.$newClasen.'">';
			print '<a id="miEnlace'.$obj->id.'" href="javascript:toggleEnlace('."'".'mostrar'."'".', '.$obj->id.','.$obj->amount.')" title="'.$titlebudget.'">';
			print price(price2num($obj->amount,'MT'));
			print '</a>';
			print '</td>';
		}
		else
			print '<td '.$newClase.'">&nbsp;</td>';
	}
}
$nReformap = $aOfa[$obj->fk_structure][$obj->id][$obj->partida];
$nTotalAp = $nPresup+$nReformap;
//$nTotalAp = $nPresup;
$sumaAprob+=$nTotalAp;
$aHtml[$i]['nTotalAp'] = $nTotalAp;
if ($filtromenu['f1']==True)
{
	if ($numCol[92])
	{
	//titlepara budget
		$titlebudget = $langs->trans('Budget').': '.price($nTotalAp);		
		$titlebudget.= '&#13;'.$langs->trans('Approbedactivities').': '.price($aTotalAct['budget']);
		$titlebudget.= '&#13;'.$langs->trans('Balance').': '.price($nTotalAp-$aTotalAct['budget']);
		if ($aTotalActpen['budget']>0)
			$titlebudget.= '&#13;'.$langs->trans('Pendingactivities').': '.price($aTotalActpen['budget']);
		
		print '<td align="right" '.$newClasen.'">';
		print '<a id="miEnlace'.$obj->id.'" href="javascript:toggleEnlace('."'".'mostrar'."'".', '.$obj->id.','.$nTotalAp.')" title="'.$titlebudget.'">';
		print price(price2num($nTotalAp,'MT'));
		print '</a>';
		
		print '</td>';
	}
}
?>