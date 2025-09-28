<?php
require ("../../main.inc.php");
$id = GETPOST('id');
$idsel = GETPOST('idsel');
$fk_region = GETPOST('fk_region');
$fk_sector = GETPOST('fk_sector');
dol_include_once('/budget/class/itemsext.class.php');
dol_include_once('/budget/class/itemsgroupext.class.php');
dol_include_once('/budget/class/itemsproduct.class.php');
dol_include_once('/budget/class/itemsproduction.class.php');
dol_include_once('/budget/class/itemsproductregion.class.php');

$objItems = new Itemsext($db);
$object = new Itemsgroupext($db);
$objItemsproduct = new Itemsproduct($db);
$objItemsproductregion = new Itemsproductregion($db);
$objItemsproduction = new Itemsproduction($db);
$filterstatic = " AND t.fk_parent = ".$idsel;
$res = $object->fetchAll('ASC', 'detail',0,0,array(),'AND',$filterstatic);
$html = '';
unset($_SESSION['upsel'][$id]);

if ($res>0)
{
	$html.='<div class="box">';
	$html.='<div class="box-header">
	<h3 class="box-title">'.$langs->trans('Items').'</h3>';
	$html.= '<input type="hidden" id="tselectt" name="tselectt" value="">';
	$html.= '<input type="hidden" id="listt" name="listr" value="">';
	$html.= '<input type="hidden" id="subaction" name="subaction" value="items">';
	$html.='</div>
<!-- /.box-header -->
<div id="lresource" class="box-body">';
	$html.= '<table id="example1" class="table table-bordered table-striped">';
	$html.= '<thead>';
	$html.= '<tr>';
	$html.= '<th>'.'<input type="checkbox" name="marcarTodo" onclick="checkTodos()" />'.$langs->trans('Sel').'</th>';
	$html.= '<th>'.$langs->trans('Label').'</th>';
	$html.= '<th>'.$langs->trans('Unit').'</th>';
	//$html.= '<th>'.$langs->trans('Quant').'</th>';
	$html.= '</tr>';
	$html.= '</thead>';
	$html.= '<tbody>';
	$lines = $object->lines;
	foreach ((array) $lines AS $i => $line)
	{
		if ($line->type == 0)
		{
			$objItemsLine = new ItemsgroupLineext($db);
			$objItemsLine->fk_unit = $line->fk_unit;
			$html.= '<tr>';
			$html.= '<td>'.'<input id="'.$line->id.'" class="check" type="checkbox" name="sel['.$line->fk_item.']" onclick="marcar_selt(this,'.$id.');">';
			$html.= '<input id="t'.$line->fk_item.'" type="hidden" name="ord['.$line->fk_item.']" value="">';
			$html.= '<td>'.$line->detail.'</td>';
			$html.= '<td>'.$langs->trans($objItemsLine->getLabelOfUnit('short')).'</td>';
			$html.= '</tr>';
		}
	}
	$html.= '</tbody>';
	$html.= '</table>';
	$html.= '</div>';
	$html.= '</div>';
}
$html.='<!-- DataTables -->
<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables/dataTables.bootstrap.min.js"></script>
';
$html.='<script>
$(function () {
	$("#example1").DataTable();
	$("#example2").DataTable({
		"paging": true,
		"lengthChange": false,
		"searching": false,
		"ordering": true,
		"info": true,
		"autoWidth": false
	});
});
</script>';
print $html;
?>