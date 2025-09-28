<?php
dol_fiche_head();
print '<table class="border" width="100%">';
print '<tr class="liste_titre">';
print_liste_field_titre($langs->trans("Name"),"", "","","","");
print_liste_field_titre($langs->trans("Charge"),"", "","","","");
print_liste_field_titre($langs->trans("Action"),"", "",'','','align="right"');
print "</tr>\n";
//registro nuevo
if ($object->status == 2 && $action == 'asignjobs')
{
	$aContact = $objSoc->contact_array();
	include DOL_DOCUMENT_ROOT.'/mant/jobs/tpl/addcontact.tpl.php';
}
//$aArray = $objJobsContact->list_contact($object->id);
$filterstatic = " AND t.fk_jobs = ".$object->id;
$objJobsContact = new MJobscontact($db);
$res = $objJobsContact->fetchAll('ASC','rowid',0,0,array(1=>1),'AND',$filterstatic);
if ($res > 0)
	$lines = $objJobsContact->lines;
$numberContact = 0;
$var = true;
$objcontact = new Contact($db);
foreach ((array) $lines AS $i => $data)
{
	$res = $objcontact->fetch($data->fk_contact);
	$var=!$var;
	$numberContact++;
	print "<tr $bc[$var]>";
	print '<td>'.$objcontact->getNomUrl(1).'</td>';
	print '<td>';
	print $objcontact->poste;
	print '</td>';
	print '<td align="right">'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$data->id.'&action=deletec">'.img_picto('','delete').'</a>'.'</td>';
	print '</tr>';
}
print "</table>";
dol_fiche_end();
if ($object->status == 2 && count($lines)>0)
{
	print '<div>';
	print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="upassignreq">';
	print '<input type="hidden" name="actionant" value="asignjobs">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';

	print '<center><br><input type="submit" class="button" value="'.$langs->trans("Register programming").'"></center>';

	print '</form>';
	print '</div>';
}
?>