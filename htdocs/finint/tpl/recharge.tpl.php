<?php
echo '<hr>'.$sumaappdep.'+'.$sumanoappdep.'+'.$sumapar.'+'.$sumapar0;
$sumaactual = ($sumaappdep+$sumanoappdep+$sumapar+$sumapar0)*-1;
$sumaactual = ($sumanoappdep)*-1;
    //action close
    //action outlay
if ($action == 'recharge')
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
	// $ids = '';
	// foreach($accountusert->lines AS $j => $line)
	//   {
	//     if(!empty($ids)) $ids.= ',';
	//     $ids.=$line->fk_account;
	//   }
	// if (!empty($ids))
	//     $filterto = " rowid IN (".$ids.")";

	if ($user->admin)
	{
		$filterfrom = '';
		$filterto = '';
	}
	print_fiche_titre($langs->trans("Requestrecharge"));

	print '<form enctype="multipart/form-data" name="updateclose" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$id.'">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="rechargeconf">';
	print '<input type="hidden" name="amount_out" value="'.$sumadep.'">';
	print '<input type="hidden" name="amount_close" value="'.$saldoBankUser.'">';
	dol_fiche_head();

	print '<table class="border centpercent">';

	print '<tr><td class="fieldrequired">'.$langs->trans("Attachment").'</td><td>';
	print '<input type="file" class="file" name="docpdf" id="docpdf"/>';
	print '</td></tr>';

	print '<tr><td>'.$langs->trans('Date').'</td>';
	print "<td>";
	$form->select_date((empty($dateo)?dol_now():$dateo),'do','','',1,'add',1,0,0,0);
	print "</td>\n";
	print '</tr>';
	print '<tr><td>'.$langs->trans('Label').'</td>';
	print '<td><input name="label" class="flat" type="text" size="40" value=""></td>';
	print '</tr>';

	if (!empty(price2num($sumaactual)))
	{
		print '<tr><td>'.$langs->trans('Amount').'</td>';
		print '<td>';
		print price($sumaactual);
		print '<input name="amount" type="hidden" value="'.$sumaactual.'">';
		print '</td>';
		print '</tr>';
	}
	else
		print '<input type="hidden" name="amount" value="0">';
	print "</table>";
	dol_fiche_end();

	print '<br><center><input type="submit" class="button" value="'.$langs->trans("Requestrecharge").'"></center>';
	print "</form>";
}

?>