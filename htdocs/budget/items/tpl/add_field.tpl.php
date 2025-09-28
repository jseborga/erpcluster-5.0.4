<?php
			print '<tr>';
			print '<td class="width10">';
			print '<input type="hidden" name="c_grupo" value="1">';
			print '<input type="text" class="flat" size="2" name="ref" value="'.GETPOST('ref').'" disabled>';
			print '</td>';
			print '<td>'.'<input type="text" class="flat" name="label" value="'.GETPOST('label').'" required>'.'</td>';
			print '<td align="right">'.'<input type="submit" value="'.$langs->trans('Save').'"'.'</td>';
			print '</tr>';

?>