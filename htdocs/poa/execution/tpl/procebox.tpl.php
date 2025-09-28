<?php
/* impresion de proceso
*/
print '<!-- Apply any bg-* class to to the info-box to color it -->';
print '<div class="info-box bg-yellow">';
print '<span class="info-box-icon">';
if ($objproc->id>0)
{
	if ($objproc->statut > 0)
		print '<i class="fa fa-check-square"></i>';
	else
		print '<i class="fa fa-adjust"></i>';
}
else
	print '<i class="fa fa-cog fa-spin fa-1x fa-fw"></i>';
print '</span>';

print '<div class="info-box-content">';
print '<span class="info-box-text">';
if ($object->statut >0)
{
	print '<button class="btn btn-primary btn-lg bg-yellow" href="#ficheprocess" role="button" data-toggle="modal">'.(empty($objproc->ref)?$langs->trans('New'):$objproc->ref.'/'.$objproc->gestion).'</button>';
}
print ' <i>'.$langs->trans('Date').'</i> '.dol_print_date(($objproc->id>0?$objproc->date_process:dol_now()),'day') ;
print '; '.$langs->trans('Inicio Proceso');
print '</span>';

print '<span class="info-box-number">'.price($objproc->amount).'</span>';

print '<!-- The progress section is optional -->';
print '<div class="progress">';
$progressactprev = 0;
if ($object->amount>0)
	$progressactprev = $objproc->amount / $object->amount * 100;
print '<div class="progress-bar" style="width: '.$progressactprev.'%"></div>';
print '</div>';
print '<span class="progress-description">';
print $progressactprev.'% '.$langs->trans('The').' '.price($object->amount);
print '</span>';

print '</div><!-- /.info-box-content -->';
print '</div><!-- /.info-box -->';

include DOL_DOCUMENT_ROOT.'/poa/process/tpl/fiche_process.tpl.php';

?>