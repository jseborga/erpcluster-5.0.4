<?php

print "<tr $bc[$var]>";

if (! empty($arrayfields['t.code']['checked'])) 
{
	print '<td>'.'<input type="text" name="code" value="'.$newdata->code.'"></td>';
	if (! $i) $totalarray['nbfield']++;
}
if (! empty($arrayfields['t.label']['checked'])) 
{
	print '<td>'.'<input type="text" name="label" value="'.$newdata->label.'"></td>';
	if (! $i) $totalarray['nbfield']++;
}
if (! empty($arrayfields['t.fk_categorie']['checked'])) 
{
	print '<td>';
	print $form->select_all_categories('product',GETPOST('fk_categorie'),'fk_categorie',64,0,0);
	print '</td>';
	if (! $i) $totalarray['nbfield']++;
}
print '<td></td>';
print '<td>'.'<input type="submit" name="save" value="'.$langs->trans('Save').'"></td>';
print '</tr>';
?>