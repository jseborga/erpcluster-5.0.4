<?php
$form = new Form($db);
print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">'."\n";
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
if ($action == 'createuser')
	print '<input type="hidden" name="action" value="adduser">';
if ($action == 'edituser')
	print '<input type="hidden" name="action" value="updateuser">';
print '<input type="hidden" name="id" value="'.$object->id.'">';

print '<tr>';
print '<td>';
print $form->select_users($fk_user,'fk_user',1,$aFilter,0);
print '</td>';
print '<td>&nbsp;</td>';
print '<td align="right">';
print '<input type="image" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/orgman/img/save.png" width="14" height="14">';
print '&nbsp;';
print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'">'.img_picto($langs->trans('Cancel'),DOL_URL_ROOT.'/orgman/img/left','',true).'</a>';
print '</td>';
print '</tr>';
print '</form>';

?>
