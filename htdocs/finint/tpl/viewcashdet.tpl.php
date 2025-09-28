<?php
$filter = array(1=>1);
$filterstatic = " AND t.fk_request = ".$id;
$res = $objectdet->fetchAll('ASC', 'detail', 0, 0, $filter, 'AND',$filterstatic,false);
$lines = $objectdet->lines;
dol_fiche_head();

if ($action != 'outlay')
{
	print '<form enctype="multipart/form-data" method="POST" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'">';

	if ($action == 'approval')
		print '<input type="hidden" name="action" value="approvalend">';
	else
		print '<input type="hidden" name="action" value="addline">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
}
print '<table class="noborder">'."\n";

		// Fields title
print '<thead>';
print '<tr class="liste_titre">';
print_liste_field_titre($langs->trans('Quant'),$_SERVER['PHP_SELF'],'','',$param,'width="3%"');
print_liste_field_titre($langs->trans('Unit'),$_SERVER['PHP_SELF'],'','',$param,'width="3%"');

print_liste_field_titre($langs->trans('Detail'),$_SERVER['PHP_SELF'],'','',$param,'width="54%"');
if ($action == 'outlay')
	print_liste_field_titre($langs->trans('Amountapproved'),$_SERVER['PHP_SELF'],'','',$param,'width="5%" align="right"',$sortfield,$sortorder);
else
	print_liste_field_titre($langs->trans('Amount'),$_SERVER['PHP_SELF'],'','',$param,'width="5%" align="right"',$sortfield,$sortorder);
if ($action =='approval')
	print_liste_field_titre($langs->trans('Amountapproved'),$_SERVER['PHP_SELF'],'','',$param,'align="right"',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('Action'),$_SERVER['PHP_SELF'],'','',$param,'align="right"',$sortfield,$sortorder);

print '</tr>'."\n";
print '</thead>';
print '<tbody>';
if ($object->statut == 0)
	include DOL_DOCUMENT_ROOT.'/request/tpl/addcashdet.tpl.php';
$num = count($lines);
$nTotal = 0;
$var = true;
for ($i=0; $i < $num; $i++)
{
	$var = !$var;
	$line = $lines[$i];
	print '<tr '.$bc[$var].'>';
	print '<td>';
	print $line->quant;
	print '</td>';
	print '<td>';
	$objectdet->fetch($line->id);
	$unit = $objectdet->getLabelOfUnit();
	if ($unit !== '') 
	{
		print $langs->trans($unit);
	}
	print '</td>';
	print '<td>';
	print $line->detail;
	print '</td>';
	print '<td align="right">';
	if ($action != 'outlay')
		print price($line->amount);
	else
		print price($line->amount_approved);
	print '</td>';
	if ($object->statut == 1 && $action == 'approval')
	{
		print '<td align="right">';
		print '<input type="number" min="0" step="any" name="amount_approved['.$line->id.']" value="'.price2num($line->amount,'MU').'">';
		print '</td>';
	}	
	print '<td align="right">';
	if ($object->statut == 1 && $action == 'approval')
	{
		print '<input type="checkbox" name="sel['.$line->id.']" checked>';
	}
	else
	{
		if ($object->statut == 0)
			print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&lineid='.$line->id.'&action=delline'.'">'.img_picto($langs->trans('Delete'),'delete').'</a>';
	}
	print '</td>';
	print '</tr>';
	$nTotal += $line->amount;
	$nTotalapp += 0;
}
print '<tr class="liste_total">';
print '<td class="liste_total">';
print $langs->trans('Total');
print '</td>';
print '<td class="liste_total" align="right">';
print price(price2num($nTotal,'MT'));
print '</td>';
if ($action =='approval')
{
	print '<td class="liste_total" align="right">';
	print price(price2num($nTotalapp,'MT'));
	print '</td>';
}
print '<td class="liste_total" align="right">';
print '&nbsp;';
print '</td>';
print '</tr>';
print '</tbody>';
print '</table>';
if ($object->statut == 1 && $action == 'approval')
{
	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Approve").'"> &nbsp; <input type="submit" class="butActionDelete" name="cancel" value="'.$langs->trans("Cancel").'"></div>';
}
if ($action != 'outlay')
	print '</form>';
dol_fiche_end();
?>