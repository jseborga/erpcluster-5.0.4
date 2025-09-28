<?php

print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST" name="form_index">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="addcontact">';
print '<input type="hidden" name="id" value="'.$object->id.'">';

//registro nuevo
print '<tr>';
print '<td colspan="2">';
print $form->select_contacts($object->fk_soc,'','fk_contact',1);
print '</td>';
print '<td align="right">';
print '<input type="image" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/mant/img/save.png" width="20" height="20">';
print '</td>';
print '</tr>';

print '</form>';

?>