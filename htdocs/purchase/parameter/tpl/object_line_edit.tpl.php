<?php

print "<tr $bc[$var]>";

if (! empty($arrayfields['t.code']['checked'])) 
{
	print '<td>'.'<input type="text" name="code" value="'.(GETPOST('code')?GETPOST('code'):$newdata->code).'"></td>';
	if (! $i) $totalarray['nbfield']++;
}
if (! empty($arrayfields['t.label']['checked'])) 
{
	print '<td>'.'<input type="text" name="label" value="'.(GETPOST('label')?GETPOST('label'):$newdata->label).'"></td>';
	if (! $i) $totalarray['nbfield']++;
}
if (! empty($arrayfields['t.fk_categorie']['checked'])) 
{
	print '<td>';
	print $form->select_all_categories('product',(GETPOST('fk_categorie')?GETPOST('fk_categorie'):$newdata->fk_categorie),'fk_categorie',64,0,0);
	print '</td>';
	if (! $i) $totalarray['nbfield']++;
}
if (! empty($arrayfields['t.active']['checked'])) 
{
	print '<td>';
	print $form->selectyesno('active',(GETPOST('active')?GETPOST('active'):$newdata->active),1);
	print '</td>';
	if (! $i) $totalarray['nbfield']++;
}
print '<td>';
print '<input type="submit" name="save" value="'.$langs->trans('Save').'">';
print '<input type="submit" name="cancel" value="'.$langs->trans('Return').'">';
print '</td>';
print '</tr>';
?>