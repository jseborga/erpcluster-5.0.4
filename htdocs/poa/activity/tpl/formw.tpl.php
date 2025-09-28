<?php

print '<tr>';
//date
print '<td align="left">';
if ($user->admin)
{
    //convertimos la fecha
    $aDate = dol_getdate(dol_now());
    if (!empty($objectw_->date_tracking))
        $aDate = dol_getdate($objectw_->date_tracking);
    $date_tracking = $aDate['year'].'-'.(strlen($aDate['mon'])==1?'0'.$aDate['mon']:$aDate['mon']).'-'.(strlen($aDate['mday'])==1?'0'.$aDate['mday']:$aDate['mday']);
    print '<div class="well well-sm col-sm-6" style="width:150px;">';
    print '          <div class="input-group date" id="divMiCalendario">
                      <input type="text" name="di_" id="txtFecha" class="form-control" value="'.$date_tracking.'" readonly/>
                      <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                      </span>
                  </div>';
    print '</div>';
}
else
	print dol_print_date($objectw_->date_tracking);
print '</td>';
//code_area_next
print '<td align="left">';
print '<div class="well well-sm col-sm-6" style="width:100px;">';
print $objarea->select_area($objectw_->code_area_next,'code_area_next','',15,1,0,1,'','code_iso');
print '</div>';
print '</td>';
//doc_verif
print '<td align="left">';
print '<div class="well well-sm col-sm-6" style="width:100px;">';
print '<textarea class="form-control" rows="1" name="doc_verif">'.$objectw_->doc_verif.'</textarea>';
print '</div>';
print '</td>';
//followup
print '<td align="left">';
print '<textarea class="form-control" rows="1" name="followup">'.$objectw_->followup.'</textarea>';
print '</td>';
//followto
print '<td align="left">';
print '<textarea class="form-control" rows="1"  name="followto">'.$objectw_->followto.'</textarea>';
print '</td>';

print '<td align="center">';
print '<input type="image" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/poa/img/save.png" width="14" height="14">';
if ($action=='editmon')
{
	print '&nbsp;&nbsp;';
	print '<input type="image" alt="'.$langs->trans('Cancel').'" name="'.$langs->trans('Cancel').'"src="'.DOL_URL_ROOT.'/poa/img/cancel.png" width="14" height="14">';
}
print '</td>';
print '</tr>';


?>