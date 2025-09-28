<?php
print '<form name="form_doc" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
if ($action=='editdoc')
  {  
    print '<input type="hidden" name="action" value="updatedoc">';
    print '<input type="hidden" name="idr" value="'.$idr.'">';
  }
 else
   print '<input type="hidden" name="action" value="adddoc">';
print '<input type="hidden" name="id" value="'.$object->id.'">';
print '<input type="hidden" name="fk_type_con" value="'.$fk_type_con.'">';
print '<input type="hidden" name="dol_hide_leftmenu" value="1">';

// code
print '<tr><td>';
print select_typeprocedure($objdoc->code,'code','',1,0,"code");
print '</td>';
print '<td align="right">';
print '<input type="image" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/poa/img/save.png" width="14" height="14">';
print '&nbsp;&nbsp;';
print '<input type="submit" name="cancel" value="'.$langs->trans('Cancel').'">';
print '</td>';
print '</tr>';
print '</form>';

?>