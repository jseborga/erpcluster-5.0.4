<?php
print "<form action=\"fiche.php\" method=\"post\">\n";
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
if ($action == 'editdet')
{
	print '<input type="hidden" name="rid" value="'.$rid.'">';
	print '<input type="hidden" name="action" value="updatedet">';
}
else
	print '<input type="hidden" name="action" value="adddet">';

print '<input type="hidden" name="id" value="'.$object->id.'">';

dol_htmloutput_mesg($mesg);

print '<tr>';

print '<td>';
print '<input id="sequen" type="number" value="'.$objnew->sequen.'" name="sequen" size="3" maxlength="4">';
print '</td>';
// ref
print '<td>';
$idConcept=0;
if ($objnew->ref_concept)
{
	$objconcept->fetch_ref($objnew->ref_concept);
	$idConcept = $objconcept->id;
}
$filterstr = " AND t.fk_budget = ".$object->id;
$objstr->fetchAll('ASC', 't.ordby', 0, 0, array(1=>1), 'AND',$filterstr='');
print $objstr->pu_select($objnew->ref_structure,'ref_structure','',1,'ref');
print '</td>';

// //detail
// print '<td>';
// //print '<input id="detail" type="text" value="'.$objectd->detail.'" name="detail" size="35" maxlength="40">';
// print '</td>';

//details
print '<td>';
print '<textarea class="flat" name="details" id="details" cols="30" rows="'.ROWS_3.'">';
print $objnew->details;
print '</textarea>';
print '</td>';

//state
print '<td>';
print select_yesno($objnew->state,'state','',0,1);
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