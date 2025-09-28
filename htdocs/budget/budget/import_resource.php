<?php
require ("../../main.inc.php");
$id = GETPOST('id');
$idsel = GETPOST('idsel');
dol_include_once('/budget/class/productbudgetext.class.php');

$object = new Productbudgetext($db);
$filterstatic = " AND t.fk_budget =".$idsel;
$res = $object->fetchAll('ASC', 'label',0,0,array(1=>1),'AND',$filterstatic);
$html = '';
unset($_SESSION['upsel'][$id]);
if ($res>0)
{
	$html.='<div class="box">';
	$html.='<div class="box-header">
              <h3 class="box-title">'.$langs->trans('Resources').'</h3>';
	$html.= '<input type="hidden" id="tselectr" name="tselectr" value="">';
	$html.= '<input type="hidden" id="listr" name="listr" value="">';
    $html.= '</div>
            <!-- /.box-header -->
            <div id="lresource" class="box-body">';
	$html.= '<table id="example1" class="table table-bordered table-striped">';
	$html.= '<thead>';
	$html.= '<tr>';
	$html.= '<th>'.'<input type="checkbox" name="marcarTodo" onclick="checkTodos()" />'.$langs->trans('Sel').'</th>';
	$html.= '<th>'.$langs->trans('Label').'</th>';
	$html.= '<th>'.$langs->trans('Unit').'</th>';
	$html.= '<th>'.$langs->trans('Amount').'</th>';
	$html.= '</tr>';
	$html.= '</thead>';
	$html.= '<tbody>';
	$lines = $object->lines;
	foreach ((array) $lines AS $i => $line)
	{
		$html.= '<tr>';
		$html.= '<td>'.'<input id="'.$line->id.'" class="check" type="checkbox" name="sel['.$line->id.']" onclick="marcar_selr(this,'.$id.');">';
		$html.= '<input id="r'.$line->id.'" type="hidden" name="ord['.$line->id.']" value="">';


		$html.= '<td>'.$line->label.'</td>';
		$html.= '<td>'.$line->fk_unit.'</td>';
		$html.= '<td>'.$line->amount.'</td>';
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