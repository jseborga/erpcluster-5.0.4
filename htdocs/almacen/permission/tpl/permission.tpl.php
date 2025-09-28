<?php
$form = new Form($db);
print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">'."\n";
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
if ($action == 'createuser')
	print '<input type="hidden" name="action" value="adduser">';
if ($action == 'edituser')
{
	unset($aFilter[$objnew->fk_user]);
	print '<input type="hidden" name="action" value="updateuser">';
	print '<input type="hidden" name="idr" value="'.$idr.'">';
}
print '<input type="hidden" name="id" value="'.$object->id.'">';

print '<tr>';
print '<td>';
print $form->select_users(($fk_user?$fk_user:$objnew->fk_user),'fk_user',1,$aFilter,0);
print '</td>';
print '<td>&nbsp;</td>';
print '<td>';
print $form->selectyesno('type',(GETPOST('type')?GETPOST('type'):$objnew->type),1,false,1);
print '</td>';
print '<td>';
print $form->selectyesno('typeapp',(GETPOST('typeapp')?GETPOST('typeapp'):$objnew->typeapp),1,false,1);
//print $form->selectarray('type',$aType,(GETPOST('type')?GETPOST('type'):2),0);
print '</td>';
print '<td align="right">';
print '<input type="image" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/almacen/img/save.png" width="14" height="14">';
print '&nbsp;';
print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'">'.img_picto($langs->trans('Cancel'),DOL_URL_ROOT.'/almacen/img/left','',true).'</a>';
print '</td>';
print '</tr>';
print '</form>';

?>
