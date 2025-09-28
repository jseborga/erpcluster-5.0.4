<?php

//fiche_structure
//structure
print '<div class="row">';
print '<div class="col-md-12">';
print '<div class="box">';
print '<div class="box-header with-border">';
print '<h3 class="box-title">';
print $langs->trans('Structure POA');
print '</h3>';
print '<div class="box-tools pull-right">';
print '<button class="btn btn-box-tool" data-widget="collapse">';
print '<i class="fa fa-plus"></i>';
print '</button>';
print '<button class="btn btn-box-tool" data-widget="remove">';
print '<i class="fa fa-times"></i>';
print '</button>';
print '</div>';
print '</div>';
print '<div class="box-body small-box bg-aqua" style="display: none;">';
print '<div class="inner">';

print '<table class="table" id="tabla">';

//detalla la actividad
$loop = true;
$objpoa->fetch($objact->fk_poa);
$fk_str = $objpoa->fk_structure;
$aStr = array();
while ($loop == true)
{
    $objstr->fetch($fk_str);
    $aStr[$objstr->pos]['label'] = $objstr->label;
    $aStr[$objstr->pos]['sigla'] = $objstr->sigla;
    if ($objstr->fk_father >0) $fk_str = $objstr->fk_father;
    else $loop = false;
}
ksort($aStr);
foreach ((array) $aStr AS $pos => $aData)
{
    print '<tr>';
    print '<td align="left">';
    print $aData['sigla'];
    print '</td>';
    print '<td>';
    print $aData['label'];
    print '</td>';
    print '</tr>';
}
print '</table>';

print '</div>';
print '</div>';
print '</div>';
print '</div>';
print '</div>';//row
?>