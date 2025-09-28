<?php
require_once DOL_DOCUMENT_ROOT.'/budget/class/budgettask.class.php';
$budgettemp = new Budgettask($db);
$fk_selbudget = 2;
$filter = array (1=>1);
$filterstatic = " AND t.fk_budget = ".$fk_selbudget;
$budgettemp->fetchAll('ASC', 't.label', 0, 0, $filter, 'AND',$filterstatic);
$lines = $budgettemp->lines;
$num = count($lines);

print '<div class="col-md-6">';
print '<div class="table-responsive">';
print '<table class="table no-margin">';
print '<thead>';
print '<tr class="liste_titre">';
print '<th>'.$langs->trans('Sel').'</th>';
print '<th>'.$langs->trans('Items').'</th>';
print '<th>'.$langs->trans('Unit').'</th>';
print '<th>'.$langs->trans('Amount').'</th>';
print '</tr>';
print '</thead>';
print '<tbody>';
for ($j=0;$j<$num;$j++)
{
	print '<tr>';
	print '<th>'.'<input type="checkbox" name="sel['.$lines[$i]->id.']">'.'</th>';
	print '<th>'.$lines[$j]->label.'</th>';
	print '<th>'.$lines[$j]->fk_unit.'</th>';
	print '<th>'.$lines[$j]->amount.'</th>';
	print '</tr>';
}
print '</tbody>';
print '</table>';
print '</div>';
print '</div>';
print '<div id="resource" class="col-md-6">';
print '</div>';

?>