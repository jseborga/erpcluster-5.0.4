<?php
//template ind
print '<div class="col-md-4">';
print '<!-- Widget: user widget style 1 -->';
print '<div class="box box-widget widget-user-2">';
print '<!-- Add the bg color to the header using any of the bg-* classes -->';
print '<div class="widget-user-header bg-yellow">';
print '<div class="widget-user-image">';
print '<img class="img-circle" src="./img/office.png" alt="'.$obj->ref.'">';
print '</div><!-- /.widget-user-image -->';
print '<h3 class="widget-user-username">'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$obj->id.'" title="'.$obj->label.'">'.$obj->ref.'</a>'.'</h3>';
//print '<h5 class="widget-user-desc">Lead Developer</h5>';
print '</div>';
print '<div class="box-footer no-padding">';
print '<ul class="nav nav-stacked">';
foreach ((array) $aStr AS $typeid =>$datavalue)
{
	print '<li><a href="#">'.$langs->trans('Objetivo').' '.$aType[$typeid];
	print '<span class="pull-right badge bg-green">'.price($datavalue['ejecuted']).'</span>';
	print '<span class="pull-right badge bg-blue">'.$aTypestructure[$typeid]['count'].'</span>&nbsp;';
	print '<span class="pull-right badge bg-blue">'.price($datavalue['amount']).'</span>';
	print '</a></li>';
}

print '<li><a href="#">'.$langs->trans('Metas');
print '&nbsp;<span class="pull-right badge bg-green data-toogle="tooltip"  title="'.$langs->trans('Accrued').'">'.price($aSumapay[$obj->id]).'</span>';
print ' <span class="pull-right badge bg-blue">'.$aCountmeta[$obj->id].'</span>';
print '&nbsp;<span class="pull-right badge bg-blue data-toogle="tooltip" title="'.$langs->trans('Programmed').'">'.price($aSummeta[$obj->id]).'</span>';
print '</a></li>';
print '<li><a href="#">'.$langs->trans('Tasks');
print ' <span class="pull-right badge bg-green">'.$aCuentatareafin[$obj->id].'</span>';
print '&nbsp;<span class="pull-right badge bg-green data-toogle="tooltip"  title="'.$langs->trans('Tareas concluidas').'">'.price($aSumatareafin[$obj->id]).'</span>';
print ' <span class="pull-right badge bg-aqua">'.$aCuentatarea[$obj->id].'</span>';
print '&nbsp;<span class="pull-right badge bg-aqua data-toogle="tooltip"  title="'.$langs->trans('Tareas programadas').'">'.price($aSumatarea[$obj->id]).'</span>';
print '</a></li>';

print '<li><a href="#">'.$langs->trans('Pac');
print '<span class="pull-right badge bg-default data-toogle="tooltip"  title="'.$langs->trans('Pendiente').'">'.$aCuentapacpen[$obj->id].'</span>';
print '<span class="pull-right badge bg-red data-toogle="tooltip"  title="'.$langs->trans('No Iniciado a la fecha').'">'.$aCuentapacse[$obj->id].'</span>';
print ' <span class="pull-right badge bg-orange data-toogle="tooltip"  title="'.$langs->trans('Iniciado fuera de tiempo').'">'.$aCuentapacne[$obj->id].'</span>';
print ' <span class="pull-right badge bg-green data-toogle="tooltip"  title="'.$langs->trans('Iniciado a tiempo').'">'.$aCuentapacej[$obj->id].'</span>';
print '<span class="pull-right badge bg-blue data-toogle="tooltip"  title="'.$langs->trans('Programmed').'">'.$aCuentapac[$obj->id].'</span>';
print '&nbsp;<span class="pull-right badge bg-blue data-toogle="tooltip"  title="'.$langs->trans('Programmed').'">'.price($aSumapac[$obj->id]).'</span>';
print '</a></li>';

print '</ul>';
print '</div>';
print '</div><!-- /.widget-user -->';
print '</div><!-- /.col -->';';'

?>