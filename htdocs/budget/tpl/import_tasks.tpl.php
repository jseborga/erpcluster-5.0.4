<?php

require_once DOL_DOCUMENT_ROOT.'/budget/class/budgetext.class.php';
require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettaskext.class.php';
require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettaskaddext.class.php';

require_once DOL_DOCUMENT_ROOT.'/budget/class/itemsext.class.php';

$budget = new Budgetext($db);
$budgetdet = new Budgettaskext($db);
$budgetdetadd = new Budgettaskaddext($db);

$objItems = new Itemsext($db);
$objItemsgroup = new Itemsgroupext($db);

$fk_selbudget = $object->id;
$filter = array ();
$filterstatic = " AND t.rowid != ".$fk_selbudget;

$budget->fetchAll('ASC', 't.title', 0, 0, $filter, 'AND',$filterstatic);
//$lines = $budgettemp->lines;
$num = count($lines);

$fk_selitem = $items->id;
$filterstatic = " AND t.fk_parent >=0 ";
$filterstatic.= " AND t.type= 1";
$resitem = $objItemsgroup->fetchAll('ASC', 't.ref', 0, 0,array(), 'AND',$filterstatic);
//$lines = $budgettemp->lines;
//$num = count($lines);

print '<div class="col-md-12">';
//seleccionamos el presupuesto
print '<span>'.$langs->trans('Selectbudget').'</span>';
print '<span>'.$budget->form_select('','fk_budget','onchange="import_tasks(this,'.$object->id.')"',1,'id').'</span>';

//seleccionamos items
print '<br><br>';
print '<span>'.$langs->trans('Selectitems').'</span>';
print '<span>'.$objItemsgroup->form_select('','fk_item','onchange="import_items(this,'.$object->id.','.$fk_region.','.$fk_sector.')"',1,'id').'</span>';

print '<div id="listres" class="col-md-12"></div>';

print '</div>';
?>