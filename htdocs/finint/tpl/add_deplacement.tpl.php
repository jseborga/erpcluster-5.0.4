<?php
print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="adddeplacement">';
print '<input type="hidden" name="id" value="'.$object->id.'">';
print '<input type="hidden" name="balance" value="'.$saldoBankUser.'">';

dol_fiche_head();

print '<table class="border centpercent">'."\n";
	    		// Ref
print '<tr><td class="fieldrequired" valign="top" width="15%">'.$langs->trans("Balance").'</td>';
print '<td>';
$account->fetch($object->fk_account);
				//print $account->label;
print price($saldoBankUser + $sumapar0);
print '</td></tr>';
print '<tr>';
print '<td class="fieldrequired">'.$langs->trans('Qty').'</td><td>';
print '<input type="number" class="form-control" name="quant" value="'.$objectdet->quant.'" size="5" required autofocus>';
print '</td>';
print '</tr>';
print '<tr>';
print '<td class="fieldrequired">'.$langs->trans('Unit').'</td><td>';
print $form->selectUnits('','fk_unit');
print '</td>';
print '</tr>';
print '<tr>';
print '<td class="fieldrequired">'.$langs->trans("Date").'</td>';
print '<td>';
$form->select_date('','do','','','','transaction',1,1,0,0);
print '</td>';
print '</tr>';
print '<tr>';
print '<td class="fieldrequired">'.$langs->trans("Type").'</td>';
print '<td>';
$form->select_types_paiements((GETPOST('operation')?GETPOST('operation'):($object->courant == 2 ? 'LIQ' : 'LIQ')),'operation','2',2,1);
print '</td></tr>';
print '<tr>';
print '<td>'.$langs->trans("Numero").'</td>';
print '<td>';
print '<input name="num_chq" class="flat" type="text" size="4" value="'.GETPOST("num_chq").'"></td>';
print '</tr>';
print '<tr>';
print '<td class="fieldrequired">'.$langs->trans("Description").'</td>';
print '<td>';
print '<input name="label" class="flat" type="text" size="24"  value="'.GETPOST("label").'" required>';
print '<td>';
print '</tr>';
print '<tr>';	
print '<td class="fieldrequired">'.$langs->trans("Concept").'</td>';
print '<td>';
print $form->select_type_fees(GETPOST('type','int'),'type',1);
print '</td>';
print '</tr>';
print '<tr>';		
print '<td class="fieldrequired">'.$langs->trans("Amount").'</td>';
print '<td><input name="debit" class="flat" type="number" min="0" max="'.$saldoBankUser.'"step="any" value="'.GETPOST("debit").'" required></td>';
print '</tr>';
print '</table>';

dol_fiche_end();
print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Save").'"></div>';
print "</form>";

?>