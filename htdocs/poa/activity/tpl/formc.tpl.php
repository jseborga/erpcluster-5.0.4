<?php
print "\n".'<script type="text/javascript" language="javascript">';
print '$(document).ready(function () {
              $("#selectfk_poa").change(function() {
                document.form_check.action.value="createeditpar";
                document.form_check.submit();
              });
          });';
print '</script>'."\n";

print '<form name="form_check" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="dol_hide_leftmenu" value="1">';
if ($action=='editcheck')
  {  
    print '<input type="hidden" name="action" value="updatecheck">';
    print '<input type="hidden" name="idr" value="'.$idr.'">';
  }
 else
   print '<input type="hidden" name="action" value="addcheck">';
print '<input type="hidden" name="id" value="'.$object->id.'">';

print '<tr>';

//date
print '<td align="left">';
if ($user->admin)
  $form->select_date($objectw_->date_tracking,'di_','','','',"date",1,1);
 else
   print dol_print_date($objectw_->date_tracking);
print '</td>';
//followup
print '<td align="left">';
print '<textarea class="flat" cols="60" rows="2" name="followup">'.$objectw_->followup.'</textarea>';
print '</td>';
//followto
print '<td align="left">';
print '<textarea class="flat" cols="60" rows="2"  name="followto">'.$objectw_->followto.'</textarea>';
print '</td>';

print '<td align="right">';
print '<input type="image" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/poa/img/save.png" width="14" height="14">';
if ($action=='editmon')
  {
    print '&nbsp;&nbsp;';
    print '<input type="image" alt="'.$langs->trans('Cancel').'" name="'.$langs->trans('Cancel').'"src="'.DOL_URL_ROOT.'/poa/img/cancel.png" width="14" height="14">';
  }
print '</td>';
print '</tr>';

print '</form>';

?>