<?php

if ($action == 'formapprecharge')
{
	$amount = GETPOST('amount','int');
	//buscamos la cuenta del que desembolsa
	$accountuserf = new Accountuser($db);
	$filterfrom = '';
	$filter = array(1=>1);

	//$filterstatic = " AND t.fk_user = ".$user->id;
	$filterstatic = " AND t.fk_user = ".$user->id;
	$accountuserf->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic);
	$ids = '';
	foreach($accountuserf->lines AS $j => $line)
	{
		if(!empty($ids)) $ids.= ',';
		$ids.=$line->fk_account;
	}
	if (!empty($ids))
		$filterfrom = " rowid IN (".$ids.")";

	$accountusert = new Accountuser($db);
	$filterto = '';
	$filter = array(1=>1);
	$filterstatic = " AND t.fk_user = ".$object->fk_user_create;
	$accountusert->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic);
	$ids = '';
	foreach($accountusert->lines AS $j => $line)
	{
		if(!empty($ids)) $ids.= ',';
		$ids.=$line->fk_account;
	}
	if (!empty($ids))
		$filterto = " rowid IN (".$ids.")";

	if ($user->admin)
	{
//		$filterfrom = '';
//		$filterto = '';
	}

	$account_to = $object->fk_account;
	$account_from = (GETPOST('account_from')?GETPOST('account_from'):$object->fk_account_from);
	print_fiche_titre($langs->trans("Approverecharge"));

	print '<form name="updateclose" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$id.'">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="apprecharge">';
	print '<input type="hidden" name="idLasttransfer" value="'.$idLasttransfer.'">';
	dol_fiche_head();

	print '<table class="border centpercent">';

	print '<tr><td class="fieldrequired">'.$langs->trans('TransferForm').'</td>';
	print '<td>';
	print $form->select_comptes($account_from,'account_from',0,$filterfrom,0);
	print "</td>";
	print '</tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans('TransferTo').'</td>';
	print "<td>";
	print $form->select_comptes($account_to,'account_to',0,$filterto,0);
	print "</td>";
	print '</tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans('Type').'</td>';
	print '<td class="nowrap">';
	$form->select_types_paiements((GETPOST('fk_type')?GETPOST('fk_type'):($object->fk_type == 2 ? 'LIQ' : '')),'fk_type','1,2',2,1);
	print '</td>';
	print '</tr>';
	print '<tr><td>'.$langs->trans('Number').'</td>';
	print "<td>";
	print '<input type="text" name="nro_chq" value="">';
	print "</td>";
	print '</tr>';

	print '<tr><td class="fieldrequired">'.$langs->trans('Date').'</td>';
	print "<td>";
	$form->select_date((empty($dateo)?dol_now():$dateo),'do','','',1,'add',1,0,0,0);
	print "</td>\n";
	print '</tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans('Label').'</td>';
	print '<td><input name="label" class="flat" type="text" size="40" value="" required></td>';
	print '</tr>';

	if (!empty(price2num($amount)))
	{
		print '<tr><td class="fieldrequired">'.$langs->trans('Amount').'</td>';
		print '<td>';
		print '<input name="amount" type="number" min="0" step="any" max="'.$amount.'" value="'.$amount.'" required>';
		print '</td>';
		print '</tr>';
	}
	print "</table>";
	dol_fiche_end();

	print '<br><center><input type="submit" class="button" value="'.$langs->trans("Approve").'"></center>';
	print "</form>";

}

?>