<?php
if ($resact>0)
{
	print '<div class="col-md-12">';
	print '<div class="box box-default direct-chat direct-chat-default">';
	print '<div class="box-header with-border">';
	print '<h3 class="box-title">'.$langs->trans('Activity').'</h3>';
	print '<div class="box-tools pull-right">';
	print '<span data-toggle="tooltip" title="'.count($objectw->array).' '.$langs->trans('Newmessages').'" class="badge bg-red">'.count($objectw->array).'</span>
	<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	<button class="btn btn-box-tool" data-toggle="tooltip" title="'.$langs->trans('Messages').'" data-widget="chat-pane-toggle"><i class="fa fa-comments"></i></button>
	<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>';
	print '</div>';
	print '</div>';
	print '<div class="box-body" style="display: block;">';

	print '<!-- Apply any bg-* class to to the info-box to color it -->';
	print '<div class="info-box bg-gray">';
	print '<span class="info-box-icon">';
	if ($objact->statut == 0)
		print '<i class="fa fa-comments-o"></i>';
	else
		print '<i class="fa fa-check-square"></i>';
	print '</span>';

	print '<div class="info-box-content">';
	print '<span class="info-box-text">';
	print '<button class="btn btn-primary btn-lg bg-gray" href="#ficheactivity" role="button" data-toggle="modal">'.$objact->nro_activity.'/'.$objact->gestion.' '.'</button>';
	print ' <i>'.$langs->trans('Date').'</i> '.dol_print_date($objact->date_activity,'day');
	print '; '.$langs->trans('Activity').' ';
	if ($objact->statut == 1)
		print '<button class="btn btn-primary btn-lg bg-gray" href="#ficheseg" role="button" data-toggle="modal">'.$langs->trans('Tracing').' '.'</button>';

	//	print '<a class="btn btn-primary btn-sm bg-gray" href="'.DOL_URL_ROOT.'/poa/activity/fiche.php?id='.$objact->id.'">'.$objact->nro_activity.' '.$langs->trans('Date').' '.dol_print_date($objact->date_activity,'day').'</a>';
	//print ' '.$objact->label;
	print '</span>';
	//	print '<span class="info-box-text">'.$objact->label.'</span>';
	print '<span class="info-box-number">'.price($objact->amount).'</span>';
	if (count($objectw->array)>0)
	{
		$lLoop = true;
		foreach ($objectw->array AS $j => $objectw__)
		{
			if ($lLoop)
			{
				print '<span class="info-box-text">'.dol_print_date($objectw__->date_tracking,'day').': '.$objectw__->code_area_next.' - ';
				print $objectw__->followup;
				print '</span>';
				$lLoop = false;
			}
		}
	}

	print '</div><!-- /.info-box-content -->';
	print '</div><!-- /.info-box -->';
	if (count($objectw->array)>0)
	{
		$htmlseg = "<ul>";
		foreach ($objectw->array AS $j => $objectw__)
		{
			$htmlseg.= '<li><span class="contacts-list-name">'.dol_print_date($objectw__->date_tracking,'day').': '.$objectw__->code_area_next.' - '.$objectw__->followup.'</span></li>';
		}

		print '<!-- Contacts are loaded here -->';
		print '    <div class="direct-chat-contacts" style="height:100px;">';
		print $htmlseg;
		print '</ul><!-- /.contatcts-list -->';
		print '</div><!-- /.direct-chat-pane -->';
	}

	print '</div>';
 //box-body
	print '</div>';
 //box
	print '</div>';
 //col-md-12

	include DOL_DOCUMENT_ROOT.'/poa/activity/tpl/fiche_activityseg.tpl.php';
	include DOL_DOCUMENT_ROOT.'/poa/activity/tpl/fiche_activity.tpl.php';

}
if (empty($ida) && $resact==0)
{
	//nueva actividad

	print '<div class="col-md-12">';
	print '<div class="box box-default direct-chat direct-chat-default">';
	print '<div class="box-header with-border">';
	print '<h3 class="box-title">'.$langs->trans('Activities').'</h3>';
	print '<div class="box-tools pull-right">';
	print '<span data-toggle="tooltip" title="0 '.$langs->trans('Messages').'" class="badge bg-red">0</span>
	<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	<button class="btn btn-box-tool" data-toggle="tooltip" title="'.$langs->trans('Messages').'" data-widget="chat-pane-toggle"><i class="fa fa-comments"></i></button>
	<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>';
	print '</div>';
	print '</div>';
	print '<div class="box-body" style="display: block;">';

	print '<!-- Apply any bg-* class to to the info-box to color it -->';
	print '<div class="info-box bg-gray">';
	print '<span class="info-box-icon">';
	print '<i class="fa fa-comments-o"></i>';
	print '</span>';

	print '<div class="info-box-content">';
	print '<span class="info-box-text">';
	print '<button class="btn btn-primary btn-lg bg-gray" href="#ficheactivity" role="button" data-toggle="modal">'.$langs->trans('New').'</button>';
	print ' <i>'.$langs->trans('Date').'</i> '.dol_print_date(dol_now(),'day');
	print '; '.$langs->trans('Activity').' ';
	print '<button class="btn btn-primary btn-lg bg-gray" href="#ficheactivity'.$fk_poa.'" role="button" data-toggle="modal">'.$langs->trans('Tracing').' '.'</button>';
	print '</span>';
	//	print '<span class="info-box-text">'.$objact->label.'</span>';
	print '<span class="info-box-number">'.price($objact->amount).'</span>';
	print '</div><!-- /.info-box-content -->';
	print '</div><!-- /.info-box -->';

	print '</div>';
 //box-body
	print '</div>';
 //box
	print '</div>';
	//insertamos el formulario para cargar contratos
	include DOL_DOCUMENT_ROOT.'/poa/activity/tpl/fiche_activity_new.tpl.php';

}

?>