<?php

print '<form action="fiche.php" method="POST" name="form_index">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="addcontact">';
print '<input type="hidden" name="id" value="'.$object->id.'">';


print '<table class="border" width="100%">';
print '<tr class="liste_titre">';
print_liste_field_titre($langs->trans("Name"),"", "","","","");
print_liste_field_titre($langs->trans("Charge"),"", "","","","");
print_liste_field_titre($langs->trans("Action"),"", "",'','','align="right"');
print "</tr>\n";
//registro nuevo
print '<tr>';
print '<td colspan="2">';
print $form->select_contacts($object->fk_soc,'','fk_contact',1);
//print $form->selectarray('fk_contact',$aContact,GETPOST('fk_contact'),1);
print '</td>';
print '<td align="right">';
print '<input type="image" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/mant/img/save.png" width="20" height="20">';
print '</td>';
print '</tr>';

$aArray = $objJobsContact->list_contact($object->id);

$numberContact = 0;
$var = true;
$objcontact = new Contact($db);
foreach ((array) $aArray AS $i => $data)
{
  $res = $objcontact->fetch($data->fk_contact);
  $var=!$var;
  $numberContact++;
  print "<tr $bc[$var]>";
  print '<td>'.$aContact[$data->fk_contact].'</td>';
  print '<td>';
  if ($res>0 && $objcontact->id == $data->fk_contact)
    print $objcontact->poste;
  else
    print '';
  print '</td>';
  print '<td align="right">'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$data->id.'&action=deletec">'.img_picto('','delete').'</a>'.'</td>';
  print '</tr>';
}
print "</table>";

if (count($aArray)>0)
  print '<center><br><input type="submit" class="button" name="close" value="'.$langs->trans("Close").'"></center>';

print '</form>';

?>