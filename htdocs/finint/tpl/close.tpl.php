<?php

    //action close
    //action outlay
if ($action == 'close')
{
	//buscamos la cuenta del que desembolsa
	$accountuserf = new Accountuser($db);
	$filterfrom = '';
	$filter = array(1=>1);

	//$filterstatic = " AND t.fk_user = ".$user->id;
	$filterstatic = " AND t.fk_user = ".$object->fk_user_create;
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

	if ($user->admin)
	{
		$filterfrom = '';
		$filterto = '';
	}
	if (!$lApptransfer)
	{
		if ($sumbalance >= 0)
		{
			print_fiche_titre($langs->trans("Close"));
			dol_fiche_head();
			print '<form enctype="multipart/form-data" name="updateclose" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$id.'">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="closeconf">';
			print '<input type="hidden" name="amount_out" value="'.$sumadep.'">';
			print '<input type="hidden" name="amount_close" value="'.$sumbalance.'">';
			print '<table class="border centpercent">';

			print '<tr><td class="fieldrequired">'.$langs->trans("Attachment").'</td><td>';
			print '<input type="file" class="file" name="docpdf" id="docpdf"/>';
			print '</td></tr>';
			if (!empty(price2num($sumbalance)))
			{
				print '<tr><td>'.$langs->trans('TransferForm').'</td>';
				print '<td>';
				print $form->select_comptes($object->fk_account,'account_from',0,$filterfrom,0);
				print "</td>";
				print '</tr>';
				print '<tr><td>'.$langs->trans('TransferTo').'</td>';
				print "<td>";
				print $form->select_comptes($object->fk_account_from,'account_to',0,$filterto,0);
				print "</td>";
				print '</tr>';
				print '<tr><td>'.$langs->trans('Number').'</td>';
				print "<td>";
				print '<input type="text" name="nro_chq" value="">';
				print "</td>";
				print '</tr>';
			}
			print '<tr><td>'.$langs->trans('Date').'</td>';
			print "<td>";
			$form->select_date((empty($dateo)?dol_now():$dateo),'do','','',1,'add',1,0,0,0);
			print "</td>\n";
			print '</tr>';
			print '<tr><td>'.$langs->trans('Label').'</td>';
			print '<td><input name="label" class="flat" type="text" size="40" value=""></td>';
			print '</tr>';

			if (!empty(price2num($sumbalance)))
			{
				print '<tr><td>'.$langs->trans('Amount').'</td>';
				print '<td>';
				print price($sumbalance);
				print '<input name="amount" type="hidden" value="'.$sumbalance.'">';
			//$langs->trans('Ifthebalancerecordedvalue0toregisterasanewdisbursementfortheproject').
				print '</td>';
				print '</tr>';
			}
			else
				print '<input type="hidden" name="amount" value="0">';
			print "</table>";

			print '<br><center><input type="submit" class="button" value="'.$langs->trans("Close").'"></center>';

			print "</form>";
		}
		else
		{
			setEventMessages($langs->trans('No es posible cerrar con valores negativos'),null,'warnings');
		}
	}
	else
	{
		setEventMessages($langs->trans('Pendingcashtransfersmustbeacceptedorrejectedbeforeclosing'),null,'warnings');
	}
	dol_fiche_end();
}

?>