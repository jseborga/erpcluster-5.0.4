<?php

if ($action == 'viewgr' && $user->rights->budget->budi->up && ($subaction == 'createup' || $subaction == 'createuptask'))
{
	print_fiche_titre($langs->trans("Upload"));

	dol_fiche_head();

	print '<form  enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
	print '<input type="hidden" name="action" value="veriffile">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$id.'">';
	if ($subaction == 'createuptask')
		print '<input type="hidden" name="idg" value="'.$idg.'">';


	print '<table class="border centpercent">'."\n";
	print '<tr><td width="15%" class="fieldrequired">'.$langs->trans("File").'</td><td>';
	print '<input type="file" class="flat" name="archivo" id="archivo" required>';
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Dateformat');
	print '</td>';
	print '<td>';
	print $form->selectarray('seldate',$aDatef,'',1);
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Campos date');
	print '</td>';
	print '<td>';
	print '<input type="text" name="camposdate" size="50">';
	print '</td></tr>';

	print '<tr><td>';
	print $langs->trans('Separator');
	print '</td>';
	print '<td>';
	print '<input type="text" name="separator" size="2" required>';
	print '</td></tr>';

	print '</table>'."\n";

	print '<br>';
	print '<div>';
	print '<span>';
	print $langs->trans('Es necesario un archivo CSV con las siguientes columnas').':';
	print '</span>';
	print '<div>'.'ref'.' =>  <span>'.$langs->trans('Codigo de la tarea').'</span>'.'</div>';
	print '<div>'.'label'.' => <span>'.$langs->trans('Descripcion de la tarea').'</span>'.'</div>';
	print '<div>'.'hilo'.' => <span>'.$langs->trans('Hilo de la tarea').'</span>'.'</div>';
	print '<div>'.'item'.' => <span>'.$langs->trans('Registre el codigo del item').'</span>'.'</div>';
	print '<div>'.'login'.' => <span>'.$langs->trans('Asignado a, registre el login del usuario').'</span>'.'</div>';
	print '<div>'.'fechaini'.' => <span>'.$langs->trans('Fecha inicio de la tarea').'</span>'.'</div>';
	print '<div>'.'fechafin'.' => <span>'.$langs->trans('Fecha final de la tarea').'</span>'.'</div>';
	print '<div>'.'detail'.' => <span>'.$langs->trans('Descripcion de la tarea').'</span>'.'</div>';
	print '<div>'.'group'.' => <span>'.$langs->trans('0=No grupo; 1=Si grupo').'</span>'.'</div>';
	print '<div>'.'type'.' => <span>'.$langs->trans('Codigo tipo de item').'</span>'.'</div>';
	print '<div>'.'typename'.' => <span>'.$langs->trans('Nombre tipo de item').'</span>'.'</div>';
	print '<div>'.'unitprogram'.' => <span>'.$langs->trans('Unidades programadas').'</span>'.'</div>';
	print '<div>'.'unit'.' => <span>'.$langs->trans('Unidad de medida').'</span>'.'</div>';
	print '<div>'.'price'.' => <span>'.$langs->trans('Precio unitario').'</span>'.'</div>';
	print '</div>';
	print '<br>';
	print '<center><input type="submit" class="button" name="add" value="'.$langs->trans("Upload").'">';
	print '&nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</center>';

	print '</form>';

	dol_fiche_end();

}
