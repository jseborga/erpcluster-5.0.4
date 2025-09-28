<?php
if ($user->rights->assets->ass->read)
{
	$useful_life = ($object->useful_life>0?$object->useful_life:$objgroup->useful_life);
	print '<table class="border" style="min-width=1000px" width="100%">';

	print '<tr><td width="15%">'.$langs->trans('Code').'</td>';
	print '<td colspan="2">';
	print $form->showrefnav($object, 'ref', $linkback, 1, 'ref', 'ref','');
	print '</td>';
	print '</tr>';
	//ref_ext
	print '<tr><td width="15%">'.$langs->trans('Refext').'</td>';
	print '<td colspan="2">';
	print $object->ref_ext;
	print '</td>';
	print '</tr>';
		 //detail
	print '<tr><td width="15%">'.$langs->trans('Detail').'</td><td colspan="2">';
	print $object->descrip;
	print '</td></tr>';

		 //ref item
	print '<tr><td width="15%">'.$langs->trans('Useful_life').'</td><td colspan="2">';
	print $useful_life;
	print '</td></tr>';

		 //percent
	print '<tr><td width="15%">'.$langs->trans('Percent').'</td><td colspan="2">';
	if ($useful_life>0)
		print price(100/$useful_life);
	print '</td></tr>';

	//date adq
	print '<tr><td width="15%">'.$langs->trans('Dateacquisition').'</td><td colspan="2">';
	print dol_print_date($object->date_adq,'day');
	print '</td></tr>';

	//date active
	print '<tr><td width="15%">'.$langs->trans('Dateactivation').'</td><td colspan="2">';
	print dol_print_date($object->date_active,'day');
	print '</td></tr>';

	//date baja
	print '<tr><td width="15%">'.$langs->trans('Lowdate').'</td><td colspan="2">';
	print dol_print_date($object->date_baja,'day');
	print '</td></tr>';

	print '</table>';

}
?>