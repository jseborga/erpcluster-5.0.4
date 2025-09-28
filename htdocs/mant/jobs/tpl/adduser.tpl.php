<?php
print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST" name="form_index">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="adduser">';
print '<input type="hidden" name="id" value="'.$object->id.'">';

if ($object->status == 2)
{

	print '<tr>';
	print '<tr>';
	print '<td>';
	//$formadd = new Formadd($db);
	$filter = " d.fk_soc <= 0 OR d.fk_soc IS NULL ";
	//print $form->select_use('','fk_user',$filter,1,0,0,'',0);
	print $form->select_member('','fk_user', " d.statut = 1 ",1,0,0,array(),0);
	//select_use($selected='', $htmlname='userid', $filter='', $showempty=0, $showtype=0, $forcecombo=0, $events=array(), $limit=0,$required='')
	print '</td>';
	print '<td>';
	print '</td>';
	print '<td align="right">';
	print '<input type="image" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/mant/img/save.png" width="20" height="20">';
	print '</td>';
	print '</tr>';
}


print '</form>';
?>