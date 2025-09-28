<?php
$var = !$var;
//$arrayresult = $form->select_items_list_v("", 'itemid', 0, "", 0, 'AGO', 0, 2, 2, 0);
//print_r($asrrayresult);
//print json_encode($arrayresult);
print "<tr $bc[$var]>";
//include_once(DOL_DOCUMENT_ROOT.'/budget/tpl/framesitem.tpl.php');
print '<input type="hidden" name="subaction" value="item">';

//print '<td class="width10">';
//print '<input type="text" class="flat" size="3" name="ref" value="'.(GETPOST('ref')?GETPOST('ref'):$newdata->ref).'" disabled>';
//print '</td>';
$filtermode = " AND t.fk_budget = ".$object->id;
$filtermode.= " AND e.c_grupo = 0";
$filtermode.= " AND e.complementary = 1";
$res = $objectdettmp->fetchItems('ASC', 't.label', 0, 0,array(1=>1),'AND',$filtermode);
$options = '<option value="0">'.$langs->trans('Select').'</option>';
foreach ($objectdettmp->lines AS $j => $line)
{
	if ($line->id != $idr)
		$options.= '<option value="'.$line->id.'">'.$line->label.'</option>';
}
print '<td colspan="3">';
print '<select name="fk_budget_task_comple">'.$options.'</select>';
//print $form->select_items_v(($newdata->fk_task>0?$newdata->fk_task:$newdata->label), 'itemid', '', 30, 0, 1, 2, '', 1, array(),0,'','');
if ($action == 'edititem')
{
	print '<script type="text/javascript">';
	print ' window.parent.document.getElementById('."'search_itemid'".').value = "'.$newdata->label.'"';
	print '</script>';	
}
print '<input id="refsearch" type="hidden" name="refsearch" value="'.($newdata->fk_task>0?$newdata->label:'').'">';
print '</td>';
//print '<td class="width10">';
//print '</td>';

//print '<td>';
//print select_cunit($newdata->fk_unit,'unitid','',1,'rowid','short_label');
//print '</td>';
print '<td>';
print '<input id="quantcomple" class="flat len80" type="number" step="any" min="0" name="quantcomple" value="'.$newdata->unit_budget.'">';
print '</td>';
print '<td>';
//print '<input id="amount" type="text" size="3" name="amount" value="'.$newdata->unit_amount.'">';
print '</td>';
print '<td>';
print '</td>';
print '<td>';
print '</td>';
print '<td>';
print '</td>';
print '<td align="right" colspan="2">'.'<input class="butAction" type="submit" value="'.$langs->trans('Save').'"'.'</td>';
print '</tr>';

?>