<?php
print "\n".'<script type="text/javascript" language="javascript">';
print '$(document).ready(function () {
              $("#selectfk_poa").change(function() {
                document.form_meta.action.value="createeditpar";
                document.form_meta.submit();
              });
          });';
print '</script>'."\n";

print '<form name="form_meta" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
if ($action=='editpro')
  {  
    print '<input type="hidden" name="action" value="updatepro">';
    print '<input type="hidden" name="idr" value="'.$idr.'">';
  }
 else
   print '<input type="hidden" name="action" value="addpro">';
print '<input type="hidden" name="id" value="'.$object->id.'">';
print '<input type="hidden" name="dol_hide_leftmenu" value="1">';

print '<tr>';

// typeprocedure
print '<td>';
print select_typeprocedure($objectd_->code_procedure,'code_procedure','',1,0,'code');

print '</td>';
//date
print '<td align="center">';
$form->select_date($objectd_->date_procedure,'di_','','','',"date",1,1);
print '</td>';

print '<td align="right">';
print '<input type="image" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/poa/img/save.png" width="14" height="14">';
print '</td>';
print '</tr>';

print '</form>';

?>