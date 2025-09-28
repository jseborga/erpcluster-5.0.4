<?php
//require ("../../main.inc.php");
$id = GETPOST('id');
$idsel = GETPOST('idsel');
dol_include_once('/budget/class/productext.class.php');

$product = new Productext($db);
$res = $product->listAll();

unset($_SESSION['upsel'][$id]);

$html = '';
if ($res>0)
{
	$html.='<div class="box">';
	$html.='<div class="box-header">
	<h3 class="box-title">'.$langs->trans('Product').'</h3>';
	$html.= '<input type="hidden" id="tselect" name="tselect" value="">';
	$html.= '<input type="hidden" id="listp" name="listp" value="">';
	$html.= '</div>
	<!-- /.box-header -->
	<div id="lresource" class="box-body">';
		$html.= '<table id="example1" class="table table-bordered table-striped">';
		$html.= '<thead>';
		$html.= '<tr>';
		$html.= '<th>'.'<input type="checkbox" name="marcarTodo" onclick="checkTodos()" />'.$langs->trans('Sel').'</th>';
	//$html.= '<th>'.$langs->trans('Pos').'</th>';
		$html.= '<th>'.$langs->trans('Label').'</th>';
		$html.= '<th>'.$langs->trans('Unit').'</th>';
		$html.= '<th>'.$langs->trans('Amount').'</th>';
		$html.= '</tr>';
		$html.= '</thead>';
		$html.= '<tbody>';
		$lines = $product->lines;
		foreach ((array) $lines AS $i => $line)
		{
			$html.= '<tr>';
			$html.= '<td>'.'<input id="'.$line->id.'" class="check" type="checkbox" name="sel['.$line->id.']" onclick="marcar_sel(this,'.$id.');">';
			$html.= '<input id="x'.$line->id.'" type="hidden" name="ord['.$line->id.']" value="">';
			$html.='</td>';
			
			$html.= '<td>'.$line->label.'</td>';
			$html.= '<td>'.$line->fk_unit.'</td>';
			$html.= '<td align="right">'.$line->pmp.'</td>';
			$html.= '</tr>';
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