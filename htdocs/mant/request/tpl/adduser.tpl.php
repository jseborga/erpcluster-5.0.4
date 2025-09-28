<?php
print '<div>';
print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST" name="form_index">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="adduser">';
print '<input type="hidden" name="id" value="'.$object->id.'">';

print '<table class="border" width="100%">';
print '<tr class="liste_titre">';
print_liste_field_titre($langs->trans("Name"),"", "","","","");
print_liste_field_titre($langs->trans("Charge"),"", "","","","");
print_liste_field_titre($langs->trans("Action"),"", "",'','','align="right"');
print "</tr>\n";
print '<tr>';
//registro nuevo
print '<tr>';
print '<td>';
//recuperamos solo los que son miembros
//$aArrayMember = list_user_member('fk_member > 0');
$formadd = new Formadd($db);
//print $formadd->select_use('','fk_user'," admin=0 AND (fk_socpeople <= 0 OR fk_socpeople IS NULL)",1,0,0,'',0);
print $form->select_member('','fk_user', " d.statut = 1 ",1,0,0,array(),0);
print '</td>';
print '<td>';
print '</td>';
print '<td align="right">';
print '<input type="image" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/mant/img/save.png" width="20" height="20">';
print '</td>';
print '</tr>';

//$objRequser = new Mworkrequestuser($db);
$filter = " AND t.fk_work_request = ".$id;
$res = $objRequser->fetchAll('','',0,0,array(1=>1),'AND',$filter);
$numberuser = 0;
if ($res >0)
{
	$lines = $objRequser->lines;
foreach ((array) $lines AS $i => $line)
{
	$objAdherent->fetch($line->fk_user);
	$numberuser++;
	print '<tr>';
	print '<td>';
	if ($objAdherent->id == $line->fk_user)
		print $objAdherent->lastname.' '.$objAdherent->firstname.'</td>';
	else
		print '&nbsp;';
	print '</td>';
	print '<td>'.$objAdherent->job.'</td>';
	print '<td align="right">'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&amp;idu='.$line->id.'&amp;action=deladh'.'">'.img_picto($langs->trans('Delete'),'delete').'</a>'.'</td>';
	print '</tr>';
}
}
print "</table>";
print '</form>';
print '</div>';
//si existe registro de tecnico interno se envia a correo
if (count($lines)>0)
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