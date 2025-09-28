<?php

    //action close
    //action outlay
if ($action == 'transfd')
{
	//buscamos la cuenta del que desembolsa
	$accountuserf = new Accountuser($db);
	$filterfrom = '';
	$filter = array(1=>1);
	
	$filterstatic = " AND t.fk_user = ".$user->id;
	$accountuserf->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic);
	$ids = '';
	foreach($accountuserf->lines AS $j => $line)
	{
		if(!empty($ids)) $ids.= ',';
		$ids.=$line->fk_account;
	}
	if (!empty($ids))
	{
		$filterfrom = " rowid IN (".$ids.")";
		$filterto   = " rowid IN (".$ids.")";
	}
	
	if ($user->admin)
	{
		$filterfrom = '';
		$filterto = '';
	}
	print_fiche_titre($langs->trans("Closeandtransfer"));

	dol_fiche_head();

	print '<form enctype="multipart/form-data" name="updateclose" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$id.'">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="transfdconf">';
	print '<input type="hidden" name="amount_out" value="'.$sumadep.'">';
	print '<input type="hidden" name="amount_close" value="'.$saldoBankUser.'">';
	print '<table class="border centpercent">';

	print '<tr><td class="fieldrequired">'.$langs->trans("Attachment").'</td><td>';
	print '<input type="file" class="file" name="docpdf" id="docpdf"/>';
	print '</td></tr>';

	print '<tr><td>'.$langs->trans('TransferForm').'</td>';
	print '<td>';
	print $form->select_comptes($account_from,'account_from',0,$filterfrom,0);
	print "</td>";
	print '</tr>';
	//print '<tr><td class="fieldrequired">'.$langs->trans("Project").'</td><td>';
	//$filterkey = '';
	//$numprojet = $formproject->select_projects(($user->societe_id>0?$user->societe_id:-1), $object->fk_projet, 'fk_projet', 0,0,1,0,0,0,0,$filterkey);

					//requerimiento
	//print '</tr>';
	print '<tr><td>'.$langs->trans('Request').'</td>';
	print "<td>";
	    			//revisamos
	$objectnew=new Requestcash($db);
	$filterto = '';
	$filter = array(1=>1);
	$filterstatic = " AND t.fk_user_create = ".$object->fk_user_create;
	$filterstatic.= " AND t.statut = 3 ";
	$objectnew->fetchAll('', '', 0, 0, $filter, 'AND',$filterstatic);
	$aRequest = array();
	$objprojnew =new Project($db);
	foreach($objectnew->lines AS $j => $line)
	{
		if ($line->id != $object->id)
		{
			$objprojnew->fetch($line->fk_projet);
			$aRequest[$line->id] = $line->ref.' '.$line->detail.' - '.$langs->trans('Project').': '.$objprojnew->ref;
		}
	}
	print $form->selectarray('fk_request_cash_to',$aRequest,$fk_request_cash_to,0);
	print "</td>";
	print '</tr>';

	print '<tr><td>'.$langs->trans('Date').'</td>';	
	print "<td>";	
	$form->select_date((empty($dateo)?dol_now():$dateo),'do','','',1,'add',1,0,0,0);
	print "</td>\n";
	print '</tr>';
	print '<tr><td>'.$langs->trans('Label').'</td>';
	print '<td><input name="label" class="flat" type="text" size="40" value=""></td>';
	print '</tr>';
	print '<tr><td>'.$langs->trans('Amount').'</td>';
	print '<td>';
	if (!$user->admin)
	{
		print price($saldoBankUser);
		print '<input type="hidden" name="amount" value="'.$saldoBankUser.'">';
	}
	else
	{
		print '<input name="amount" class="flat" type="number" min="0" step="any" value="'.$saldoBankUser.'" '.(!$user->admin?'disabled':'').'> '.$langs->trans('Ifthebalancerecordedvalue0toregisterasanewdisbursementfortheproject');
	}
	print '</td>';
	print '</tr>';
	print "</table>";
	
	print '<br><center><input type="submit" class="button" value="'.$langs->trans("Close").'"></center>';
	
	print "</form>";
	dol_fiche_end();

}

?>