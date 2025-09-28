<?php
	//add revaluation
if ($action == 'reval')
{
	//primero se debe tener depreciado y actualizado un dia antes de la fecha de revaluacion el activo
	//recuperamos información a la fecha de la tabla asset_balance
	$resr = $objAssetbalance->fetch(0,$id);

}

print '<form name="fiche_asset" action="'.$_SERVER['PHP_SELF'].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="addreval">';
print '<input type="hidden" name="id" value="'.$id.'">';
print '<input type="hidden" name="idreval" value="'.$objAssetbalance->id.'">';

dol_htmloutput_mesg($mesg);

print '<table class="border" style="min-width=1000px" width="100%">';

//informacion de al ultima depreciación
print '<tr><td width="15%">'.$langs->trans('Datedepreciation').'</td><td colspan="2">';
print dol_print_date($objAssetbalance->date_end,'day');
print '</td></tr>';
//period depreciation
print '<tr><td width="15%">'.$langs->trans('Perioddepreciation').'</td><td colspan="2">';
$yearDepreciation = substr($objAssetbalance->ref,0,4);
$monthDepreciation = substr($objAssetbalance->ref,4,2);
print $yearDepreciation.'-'.$monthDepreciation;
print '</td></tr>';

print '<tr><td width="15%">'.$langs->trans('Balanceamount').'</td><td colspan="2">';
print price($objAssetbalance->amount_balance);
print '</td></tr>';

print '<tr><td width="15%">'.$langs->trans('Balancedepr').'</td><td colspan="2">';
print price($objAssetbalance->amount_balance_depr);
print '</td></tr>';

print '<tr><td width="15%">'.$langs->trans('Balance').'</td><td colspan="2">';
$balance = $objAssetbalance->amount_balance - $objAssetbalance->amount_balance_depr;
print price($balance);
print '<input type="hidden" name="amount_base" value="'.$balance.'">';
print '</td></tr>';

//date reval
print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Daterevalued').'</td><td colspan="2">';
$form->select_date(GETPOST('date_revalidation'),'dr_','','',1,"date",1,1);
print '</td></tr>';

//new value
print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Newcoste').'</td><td colspan="2">';
print '<input type="number" min="0" step="any" name="coste_reval" value="'.GETPOST('coste_reval','int').'" >';
print '</td></tr>';

//new value residual
print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Newcosteresidual').'</td><td colspan="2">';
print '<input type="number" min="0" step="any" name="coste_residual_reval" value="'.GETPOST('coste_residual_reval','int').'" >';
print '</td></tr>';

//new time residual
print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Residual life time').'</td><td colspan="2">';
print '<input type="number" min="0" name="useful_life_residual" value="'.GETPOST('useful_life_residual','int').'" >';
print '</td></tr>';

//detail
print '<tr><td width="15%" class="fieldrequired">'.$langs->trans('Description').'</td><td colspan="2">';
print '<textarea name="detail" cols="40" rows="2">'.GETPOST('detail').'</textarea>';
print '</td></tr>';

print '</table>';

print '<center><br><input type="submit" class="button" value="'.$langs->trans("Save").'">';
print '&nbsp;<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">'.'</center>';
print '</form>';

?>