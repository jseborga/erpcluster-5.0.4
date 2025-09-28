<?php
require_once DOL_DOCUMENT_ROOT.'/budget/class/budgetext.class.php';
require_once DOL_DOCUMENT_ROOT.'/budget/class/productbudgetext.class.php';

$budget = new Budgetext($db);
$productbudget = new Productbudgetext($db);

$fk_selbudget = $object->id;
$filter = array (1=>1);
$filterstatic = " AND t.rowid != ".$fk_selbudget;
$budget->fetchAll('ASC', 't.title', 0, 0, $filter, 'AND',$filterstatic);
//$lines = $budgettemp->lines;
//$num = count($lines);

//print '<div class="col-md-12">';
//seleccionamos el presupuesto
//print '<span>'.$langs->trans('Selectbudget').'</span>';
//print '<span>'.$budget->form_select('','fk_budget','onchange="import_product(this,'.$object->id.')"',1,'id').'</span>';

print '<div id="listres" class="col-md-12">';
include DOL_DOCUMENT_ROOT.'/budget/budget/import_product.php';
print '</div>';

//print '</div>';
?>