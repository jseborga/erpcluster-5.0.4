<?php

print '<tr>';
	//
if (! empty($arrayfields['t.ref']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="ref" value="'.(GETPOST('ref')?GETPOST('ref'):$object->ref).'" size="10"></td>';
if (! empty($arrayfields['t.code']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="code" value="'.(GETPOST('code')?GETPOST('code'):$object->code).'" size="10"></td>';
if (! empty($arrayfields['t.label']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="label" value="'.(GETPOST('label')?GETPOST('label'):$object->label).'" size="10"></td>';
if (! empty($arrayfields['t.useful_life']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="useful_life" value="'.(GETPOST('useful_life')?GETPOST('useful_life'):$object->useful_life).'" size="10"></td>';
if (! empty($arrayfields['t.fk_method_dep']['checked']))
{
	print '<td class="liste_titre">';
	print '<select class="flat" name="fk_method_dep">'.$options.'</select>';
	print '</td>';
}
if (! empty($arrayfields['t.depreciate']['checked']))
{
	print '<td class="liste_titre">';
	print $form->selectyesno('depreciate',$object->depreciate,1);
	print '</td>';
}
if (! empty($arrayfields['t.toupdate']['checked']))
{
	print '<td class="liste_titre">';
	print $form->selectyesno('toupdate',$object->toupdate,1);
	print '</td>';
}
if (! empty($arrayfields['t.account_accounting']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="account_accounting" value="'.(GETPOST('account_accounting')?GETPOST('account_accounting'):$object->account_accounting).'" size="10"></td>';
if (! empty($arrayfields['t.account_spending']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="account_spending" value="'.(GETPOST('account_spending')?GETPOST('account_spending'):$object->account_spending).'" size="10"></td>';

if (! empty($arrayfields['t.active']['checked']))
{	print '<td class="liste_titre">';
	print $form->selectyesno('active',(GETPOST('active')?GETPOST('active'):$object->active),1);
	print '</td>';
}
	// Extra fields
if (is_array($extrafields->attribute_label) && count($extrafields->attribute_label))
{
	foreach($extrafields->attribute_label as $key => $val)
	{
		if (! empty($arrayfields["ef.".$key]['checked']))
		{
			$align=$extrafields->getAlignFlag($key);
			$typeofextrafield=$extrafields->attribute_type[$key];
			print '<td class="liste_titre'.($align?' '.$align:'').'">';
			if (in_array($typeofextrafield, array('varchar', 'int', 'double', 'select')))
			{
				$crit=$val;
				$tmpkey=preg_replace('/search_options_/','',$key);
				$searchclass='';
				if (in_array($typeofextrafield, array('varchar', 'select'))) $searchclass='searchstring';
				if (in_array($typeofextrafield, array('int', 'double'))) $searchclass='searchnum';
				print '<input class="flat'.($searchclass?' '.$searchclass:'').'" size="4" type="text" name="search_options_'.$tmpkey.'" value="'.dol_escape_htmltag($search_array_options['search_options_'.$tmpkey]).'">';
			}
			print '</td>';
		}
	}
}
    // Fields from hook
$parameters=array('arrayfields'=>$arrayfields);
$reshook=$hookmanager->executeHooks('printFieldListOption',$parameters);
    // Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;
if (! empty($arrayfields['t.datec']['checked']))
{
        // Date creation
	print '<td class="liste_titre">';
	print '</td>';
}
if (! empty($arrayfields['t.tms']['checked']))
{
        // Date modification
	print '<td class="liste_titre">';
	print '</td>';
}
if (! empty($arrayfields['u.statut']['checked']))
{
        // Status
	print '<td class="liste_titre" align="center">';
	print $form->selectarray('search_statut', array('-1'=>'','0'=>$langs->trans('Disabled'),'1'=>$langs->trans('Enabled')),$search_statut);
	print '</td>';
}

// Action column
print '<td class="liste_titre" align="right">';
print '<input type="submit" name="submit" value="'.$langs->trans('Save').'">';
print '</td>';
print '</tr>'."\n";

?>