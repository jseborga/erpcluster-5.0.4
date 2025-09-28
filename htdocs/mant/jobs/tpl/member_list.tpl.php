<?php

dol_fiche_head();

print '<table class="border" width="100%">';
print '<tr class="liste_titre">';
print_liste_field_titre($langs->trans("Name"),"", "","","","");
print_liste_field_titre($langs->trans("Charge"),"", "","","","");
print_liste_field_titre($langs->trans("Action"),"", "",'','','align="right"');
print "</tr>\n";
if ($object->status == 2 && $action == 'asignjobs')
{
	if (!$lTechnic)
	{
					//registro de tecnicos internos
		include DOL_DOCUMENT_ROOT.'/mant/jobs/tpl/adduser.tpl.php';
	}
}
$filterstatic = " AND t.fk_jobs = ".$object->id;
$objJobsuser = new Mjobsuser($db);
$objJobsuser->fetchAll('ASC', 'datec', 0,0,array(1=>1),'AND',$filterstatic);
$aArray = $objJobsuser->lines;
$numberuser = 0;
foreach ((array) $aArray AS $i => $objJuser)
{
	$objAdherent->fetch($objJuser->fk_user);
	$numberuser++;
	print '<tr>';
	print '<td>';
	if ($objAdherent->id == $objJuser->fk_user)
		print $objAdherent->lastname.' '.$objAdherent->firstname.'</td>';
	else
		print '&nbsp;';
	print '</td>';
	print '<td>'.$objAdherent->job.'</td>';

	print '<td align="right">';
	if ($user->rights->mant->jobs->assignjobs && $object->status == 2)
		print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&amp;idu='.$objJuser->id.'&amp;action=deladh'.'">'.img_picto($langs->trans('Delete'),'delete').'</a>';
	print '</td>';
	print '</tr>';
}
print "</table>";
dol_fiche_end();

//si existe registro de tecnico interno se envia a correo
if ($object->status == 2 && count($aArray)>0)
{
	print '<div>';
	print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="upassignreq">';
	print '<input type="hidden" name="actionant" value="asignjobs">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Sendassignuser").'"></center>';

	print '</form>';
	print '</div>';
}
?>