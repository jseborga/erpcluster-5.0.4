<?php

print '<tr>';
print '<td class="width10">';
include_once(DOL_DOCUMENT_ROOT.'/budget/tpl/framesitem.tpl.php');

print '<input type="hidden" name="fk_task_parent" value="'.$idg.'">';

print '<input type="text" class="flat" size="3" name="ref" value="'.($newgroup->ref?$newgroup->ref:'').'" disabled>';
print '</td>';
print '<td>';
print $form->select_items_v($newgroup->fk_task, 'itemid', '', 30, 0, 1, 2, '', 1, array(),0,'','');

print '<input id="refsearch" type="hidden" name="refsearch" value="'.($newgroup->label?$newgroup->label:'').'">';
print '<input type="hidden" name="fk_task" value="'.$newgroup->fk_task.'">';
print '<input type="hidden" name="unitid" value="0">';
print '<input type="hidden" name="quant" value="0">';
print '<input type="hidden" name="amount" value="0">';
print '</td>';
print '<td>';
print $objectdet->select_group($object->id,($newgroup->fk_task_parent?$newgroup->fk_task_parent:$fk_father),'fk_father','',1,'rowid');
print '</td>';

print '<td align="right" colspan="2">'.'<input class="butAction" type="submit" value="'.$langs->trans('Save').'"'.'</td>';
print '</tr>';
if ($action =='editgroup')
{
	print '<script type="text/javascript">';
	print ' window.parent.document.getElementById('."'search_itemid'".').value = "'. $newgroup->label.'"';
	print '</script>';
}

?>