<?php
/* impresion de actividad
*/
	if ($resact>0)
	{
		print '<li class="time-label">';
		print '<span class="bg-gray">'.dol_print_date($objact->date_activity,'day') .'</span>';
		print '</li>';
		print '<li>';
		print '<i class="fa fa-arrow-circle-down -blue"></i>';
		print '<div class="timeline-item" >';
		print '<div class="box box-solid bg-gray">';
		print '<div class="inner">';
		print '<h3 class="box-title">'.$langs->trans('Actividad').'</h3>';
		print '<table class="table">';

		print '<tr>';
		print '<td>'.'<a class="btn btn-primary btn-sm bg-gray" href="'.DOL_URL_ROOT.'/poa/activity/fiche.php?id='.$objact->id.'">'.$objact->nro_activity.'/'.$objact->gestion.'</a>';
		print $objact->label;
		print '</td>';
		//adjunto
		print '<td align="right">'.price($objact->amount).'</td>';
		//print '<td align="left">'.$langs->trans($object->getLibStatut(0)).'</td>';
		print '<td align="right">'.$objuser->login.'</td>';
		print '<td class="text-right">';
		print '<button class="btn btn-app" href="#activityseg" role="button" data-toggle="modal">';
		print $langs->trans('Seguimiento');
		print '<i class="fa fa-edit"></i>';
		print '</button>';
		print '</td>';

		print '</tr>';
		print '</table>';

		print '</div>';
		print '</div>';
		print '</div>';
		print '</li>';

        include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/fiche_activityseg.tpl.php';

	}
?>