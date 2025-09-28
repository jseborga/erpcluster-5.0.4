<?php
$var = !$var;
//$arrayresult = $form->select_items_list_v("", 'itemid', 0, "", 0, 'AGO', 0, 2, 2, 0);
//print json_encode($arrayresult);
print "<tr $bc[$var]>";
print '<td class="width10">';
include_once(DOL_DOCUMENT_ROOT.'/budget/tpl/framesitem.tpl.php');
print '<input type="hidden" name="c_grupo" value="0">';
print '<input type="hidden" name="fk_task_parent" value="'.$idg.'">';
print '<input type="hidden" name="fk_father" value="'.$idg.'">';
print '<input type="hidden" name="subaction" value="item">';
print '<input type="text" class="flat" size="3" name="ref" value="'.(GETPOST('ref')?GETPOST('ref'):$newdata->ref).'" disabled>';
print '</td>';
print '<td>';
if ($newdata->fk_task || (GETPOST('itemid') && $action =='viewit'))
{
	if (!is_object($newdata)) $newdata = new stdClass();
	 //buscamos
	$itemstmp = new Itemsext($db);
	$resitems = $itemstmp->fetch(($newdata->fk_task?$newdata->fk_task:GETPOST('itemid')));
	if ($resitems==1)
	{
		$newdata->fk_unit = $itemstmp->fk_unit;
		$newdata->fk_task = $itemstmp->id;
	}
}
if ($action !='edititem')
{
	//print $form->select_items_v(($resitems?$itemsgrouptmp->id:$newdata->label), 'itemid', '', 30, 0, 1, 2, '', 1, array(),0,'','');
	print $form->select_items_v(GETPOST('itemid'), 'itemid', ($action=='viewit'?'I':''), 30, 0, 1, 2, '', 1, array(),0,'','');
}
else
{
	print '<input type="hidden" name="itemid" value="'.$newdata->fk_task.'">';
	print $newdata->label;
}
if ($action == 'edititem')
{
	//print '<script type="text/javascript">';
	//print ' window.parent.document.getElementById('."'search_itemid'".').value = "'.$newdata->label.'"';
	//print '</script>';
}
//print '<input id="refsearch" type="hidden" name="refsearch" value="'.($newdata->fk_task>0?$newdata->label:'').'">';
print '</td>';
print '<td>';
//print select_cunit($newdata->fk_unit,'unitid','',1,'rowid','short_label');
print $form->selectUnits($newdata->fk_unit, 'unitid', 1);
print
print '</td>';
print '<td>';
print '<input id="quant" type="number" class="len100"  step="any" min="0" size="3" name="quant" value="'.$newdata->unit_budget.'">';
print '</td>';
print '<td>';
if ($action == 'viewit')
	print $form->selectyesno('complementary',$newdata->complementary,1);
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