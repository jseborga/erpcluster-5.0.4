<?php
print '<form name="fiche_process" action="'.$_SERVER['PHP_SELF'].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="updatelink">';
print '<input type="hidden" name="id" value="'.$object->id.'">';

print '<input type="text" id="doclink" name="doclink" value="'.$object->doclink.'" size="70">';

print '<input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';

print '</form>';

?>