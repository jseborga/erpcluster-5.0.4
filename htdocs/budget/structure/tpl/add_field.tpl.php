<?php
print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
if ($action == 'editdet')
{
	print '<input type="hidden" name="idr" value="'.$idr.'">';
	print '<input type="hidden" name="action" value="updatedet">';
}
else
	print '<input type="hidden" name="action" value="adddet">';

print '<input type="hidden" name="id" value="'.$object->id.'">';

dol_htmloutput_mesg($mesg);

print '<tr>';

print '<td>';
$objectdet->max_ref($object->id);
print '<input id="sequen" type="number" value="'.($action=='editdet'?$objectdet->sequen:$objectdet->max).'" name="sequen" size="3" maxlength="4">';
print '</td>';
// ref
print '<td>';
$idConcept=0;
if ($objnew->ref_concept)
{
	$objconcept->fetch_ref($objnew->ref_concept);
	$idConcept = $objconcept->id;
}
$filterform = " AND t.entity = ".$conf->entity;
$filterform = " AND t.statut = 1";
$objform->fetchAll('ASC', 'ref', 0, 0, array(1=>1), 'AND',$filterform);
print $objform->form_select($objnew->formula,'formula','',1,'ref');
print '</td>';

//details
print '<td>';
print '<input type="text" name="detail" value="'.$objnew->detail.'" >';
print '</td>';

//statusprint
print '<td>';
print $form->selectyesno('status_print',$objnew->status_print,1);
print '</td>';

//statusprintdet
print '<td>';
print $form->selectyesno('status_print_det',$objnew->status_print_det,1);
print '</td>';

//state
print '<td>';
print '&nbsp;';
print '</td>';

print '<td>';
print '<center>';
print '<input type="submit" class="button" value="'.$langs->trans("Save").'">';
if ($action == 'editdet' || $action == 'createdet')
{
	print '&nbsp;';
	print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
}
print '</center>';
print '</td>';
print '</tr>';

print '</form>';

?>