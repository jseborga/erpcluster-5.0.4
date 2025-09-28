<?php
$form = new Form($db);
print '<form action="'.$_SERVER["PHP_SELF"].'?id='.$id.'" method="post">'."\n";
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
if ($action == 'createuser')
  print '<input type="hidden" name="action" value="adduser">';
if ($action == 'edituser')
  {
    print '<input type="hidden" name="action" value="updateuser">';
    print '<input type="hidden" name="idr" value="'.$objnew->id.'">';
  }
print '<input type="hidden" name="id" value="'.$id.'">';

print '<tr>';
print '<td>';
print $form->select_users($objnew->fk_user,'fk_user',1,$aFilter,0);
print '</td>';
print '<td>&nbsp;</td>';
print '<td align="right">';
print '<input class="butAction" type="submit" value="'.$langs->trans('Save').'">';
print '&nbsp;';
print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'">'.img_picto($langs->trans('Cancel'),DOL_URL_ROOT.'/finint/img/left','',true).'</a>';
print '</td>';
print '</tr>';
print '</form>';

?>
