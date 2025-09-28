<?php

print '<div id="addprod'.$fk_task_parent.$lineid.'" class="modal fade " style="display: none;">';

print '<div class="modal-dialog">';

print '<div class="modal-content">';
print '<form id="form_'.$fk_task_parent.$lineid.'" name="form_'.$fk_task_parent.$lineid.'" class="form-horizontal" role="form" action="'.DOL_URL_ROOT.'/budget/budget/card.php'.'" method="POST">';
print '<input type="hidden" name="id" value="'.$id.'">';
print '<input type="hidden" name="idr" value="'.$fk_task_parent.'">';
print '<input type="hidden" name="idreg" value="'.$lineid.'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="add_productivity">';

$objectbtrtmp->fetch($lineid);
print '<div class="modal-header">';
print '<a data-dismiss="modal" class="close">×</a>';
print '<h3>'.$langs->trans('Productivity').' '.$objectbtrtmp->detail.'</h3>';
print '</div>';

print '<div class="modal-body">';
include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/productivity.tpl.php';
print '</div>';

print '<div class="modal-footer">';
print '<input type="submit" class="btn btn-primary pull-left" value="'.$langs->trans('Save').'"/>';
print '<a href="#" data-dismiss="modal" class="btn btn-warning">Cerrar</a>';
print '</div>';

print '</form>';
print '</div>';

print '</div>';

print '</div>';

?>