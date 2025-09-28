<?php

$var = !$var;
print "<tr $b[$var]>";
print '<td>';
print '&nbsp;';
print '</td>';
print '<td>';
print $form->select_date(empty($objbook->date_order)?dol_now():$objbook->date_order,'dateo',1,1);
print '</td>';
print '<td>';
print $user->login;
print '</td>';
print '<td>';
print '<textarea class="flat" name="detail" rows="1" cols="40">'.$objbook->detail.'</textarea>';
print '</td>';
print '<td nowrap align="right" class="SI-FILES-STYLIZED">';
print '<label class="cabinet">';
include DOL_DOCUMENT_ROOT.'/monprojet/tpl/adddoc.tpl.php';
print '</label>';
print '</td>';
print '<td></td>';
print '<td align="right">';
 print '<input type="image" class="liste_titre" name="button_save" src="'.img_picto($langs->trans("Save"),DOL_URL_ROOT.'/monprojet/img/save.png','',1,1).'" value="'.dol_escape_htmltag($langs->trans("Save")).'" title="'.dol_escape_htmltag($langs->trans("Save")).'">';
print '</td>';
print '</tr>';

?>