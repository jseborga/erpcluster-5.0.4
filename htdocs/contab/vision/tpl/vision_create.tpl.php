<?php
$filteracc = '';
$res = $objAccounting->fetchAll('','',0,0,array(1=>1),'AND',$filteracc);
$optionsini = "";
$optionsfin = "";

$fk_accountini = (GETPOST('fk_accountini')?GETPOST('fk_accountini'):$obj->fk_accountini);
$fk_accountfin = (GETPOST('fk_accountfin')?GETPOST('fk_accountfin'):$obj->fk_accountfin);

if ($res >0)
{
	$lines = $objAccounting->lines;
	foreach ($lines AS $j => $line)
	{
		$selectini = '';
		$selectfin = '';
		if ($fk_accountini == $line->id) $selectini = ' selected';
		if ($fk_accountfin == $line->id) $selectfin = ' selected';
		$optionsini.= '<option value="'.$line->id.'" '.$selectini.'>'.$line->account_number.' '.$line->label.'</options>';
		$optionsfin.= '<option value="'.$line->id.'" '.$selectfin.'>'.$line->account_number.' '.$line->label.'</options>';
	}
  //edicion
}
print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

if ($action == 'new')
{
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="sequence" value="'.$obj->sequence.'">';
	print '<input type="hidden" name="fk_parent" value="'.$id.'">';
	$fk_accountini = '';
	$fk_accountfin = '';
	$cta_operation = '';
	$newAccount = $obj->account;
	$objtmp->line_ult($id);
	$line = str_pad($objtmp->line, 3, "0", STR_PAD_LEFT);
}
else
{
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="idr" value="'.$obj->rowid.'">';
	print '<input type="hidden" name="fk_parent" value="'.$obj->fk_parent.'">';
	print '<input type="hidden" name="sequence" value="'.$obj->sequence.'">';
	$fk_accountini = $obj->fk_accountini;
	$fk_accountfin = $obj->fk_accountfin;
	$cta_operation = $obj->cta_operation;
	$line = $obj->line;
	$newAccount = $obj->account;

}
print '<input type="hidden" name="id" value="'.$id.'">';
print '<input type="hidden" name="ref" value="'.$obj->ref.'">';
print '<input type="hidden" name="name_vision" value="'.$obj->name_vision.'">';
print '<input type="hidden" name="account" value="'.$newAccount.'">';
print '<input type="hidden" name="account_sup" value="'.$obj->account_sup.'">';
print '<input type="hidden" name="detail_managment" value="'.$obj->detail_managment.'">';
print '<input type="hidden" name="cta_normal" value="'.$obj->cta_normal.'">';
print '<input type="hidden" name="cta_class" value="'.$obj->cta_class.'">';
print '<input type="hidden" name="cta_column" value="'.$obj->cta_column.'">';
print '<input type="hidden" name="cta_balances" value="'.$obj->cta_balances.'">';
print '<input type="hidden" name="cta_totalvis" value="'.$obj->cta_totalvis.'">';

print '<tr>';
  //line
print '<td>';
print '<input id="" type="text" value="'.$line.'" name="line" size="2" maxlength="3">';
print '</td>';

  //account ini
print '<td>';
print '<select name="fk_accountini">'.$optionsini.'</select>';
//print $objectaccount->select_account($fk_accountini,'fk_accountini','',25,1,2,2);
print '</td>';

  //account fin
print '<td>';
print '<select name="fk_accountfin">'.$optionsfin.'</select>';
//print $objectaccount->select_account($fk_accountfin,'fk_accountfin','',25,1,2,2);
print '</td>';

  //operation
print '<td>';
print select_operation($cta_operation,'cta_operation','','',1);
print '</td>';
print '<td>';
print '<center><input type="submit" class="butAction" value="'.$langs->trans("Save").'">&nbsp;';
print '<input type="submit" class="butAction" name="cancel" value="'.$langs->trans("Cancel").'"></center>';
print '</td>';
print '</tr>';

print '</form>';
?>