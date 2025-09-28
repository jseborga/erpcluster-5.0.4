<?php
require ("../../main.inc.php");
$id = GETPOST('id');
$idsel = GETPOST('idsel');
dol_include_once('/budget/class/budgettaskext.class.php');
dol_include_once('/budget/class/budgettaskaddext.class.php');

$object = new Budgettaskext($db);
$objectadd = new Budgettaskaddext($db);
$filterstatic = " AND t.fk_budget =".$idsel;
$res = $object->fetchAll('ASC', 'label',0,0,array(1=>1),'AND',$filterstatic);
$html = '';
unset($_SESSION['upsel'][$id]);

if ($res>0)
{
	$html.='<div class="box">';
	$html.='<div class="box-header">
	<h3 class="box-title">'.$langs->trans('Items').'</h3>';
	$html.= '<input type="hidden" id="tselectt" name="tselectt" value="">';
	$html.= '<input type="hidden" id="listt" name="listr" value="">';
	$html.='</div>
<!-- /.box-header -->
<div id="lresource" class="box-body">';
	$html.= '<table id="example1" class="table table-bordered table-striped">';
	$html.= '<thead>';
	$html.= '<tr>';
	$html.= '<th>'.'<input type="checkbox" name="marcarTodo" onclick="checkTodos()" />'.$langs->trans('Sel').'</th>';
	$html.= '<th>'.$langs->trans('Label').'</th>';
	$html.= '<th>'.$langs->trans('Unit').'</th>';
	$html.= '<th>'.$langs->trans('Quant').'</th>';
	$html.= '</tr>';
	$html.= '</thead>';
	$html.= '<tbody>';
	$lines = $object->lines;
	foreach ((array) $lines AS $i => $line)
	{
		$objectadd->fetch(0,$line->id);
		if ($objectadd->c_grupo == 0)
		{
			$html.= '<tr>';
			$html.= '<td>'.'<input id="'.$line->id.'" class="check" type="checkbox" name="sel['.$line->id.']" onclick="marcar_selt(this,'.$id.');">';
			$html.= '<input id="t'.$line->id.'" type="hidden" name="ord['.$line->id.']" value="">';
			$html.= '<td>'.$line->label.'</td>';
			$html.= '<td>'.$objectadd->getLabelOfUnit().'</td>';
			$html.= '<td>'.price2num($objectadd->unit_budget).'</td>';
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