<?php


$lines = fetchAll_parameter_equipment('ASC', 'label', 0, 0, array(1=>1), 'AND','AND active=1');
//buscamos en la tabla productivity
$cData = '';
foreach ($lines AS $j => $objp)
{
	if ($cData) $cData.= '|';
	$cData.= $objp->code;
}
$_SESSION['cFormula'] = serialize($cData);
$filterstatic = " AND fk_budget_task_resource = ".$lineid;
$objproductivity->fetchAll('','',0,0,array(1=>1), 'AND',$filterstatic);
$aProd = array();
foreach ((array) $objproductivity->lines AS $k => $linep)
	$aProd[$linep->code_parameter] = $linep->quant;
//para guardar la formula
print '<div class="form-group">';
print '<label class="col-lg-2" for="formula">'.$langs->trans('Formula').':</label>';
print '<div class="col-lg-6">';
print '<input type="text" name="formula" class="form-control" id="formula'.$fk_task_parent.$lineid.'" value="'.$objectbtrtmp->formula.'" onblur="enviarDatos('."'".$cData."'".','.$fk_task_parent.','.$lineid.');" required>';
print '</div>';
print '<div id="resultado'.$fk_task_parent.$lineid.'" class="col-lg-4">xxx</div>';
print '</div>';

foreach ($lines AS $j => $objp)
{
	print '<div class="form-group">';
	print '<label class="col-lg-8" for="email">'.$objp->code.' - '.$objp->label.':</label>';
	print '<div class="col-lg-4">';
	print '<input id="'.$objp->code.'_'.$fk_task_parent.$lineid.'" type="number" step="any" min="0" name="quant['.$objp->code.']" class="form-control" id="quant'.$objp->id.'" value="'.$aProd[$objp->code].'" onblur="enviarDatos('."'".$cData."'".','.$fk_task_parent.','.$lineid.');">';
	print '</div>';
	print '</div>';
}
?>