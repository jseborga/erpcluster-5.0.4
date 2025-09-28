<?php

print '<form name="fiche_process" action="'.$_SERVER['PHP_SELF'].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="addassign">';
print '<input type="hidden" name="id" value="'.$object->id.'">';
print '<input type="hidden" name="code_area" value="'.$codearea.'">';
print $form->select_users($objworku->fk_user,'fk_user',1,'',0,$aAreaUserId);
print '<input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';

print '</form>';

?>