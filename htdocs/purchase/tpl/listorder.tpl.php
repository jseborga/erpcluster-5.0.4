<?php 
require_once DOL_DOCUMENT_ROOT.'/purchase/class/fournisseurcommandeext.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/purchase/lib/units.lib.php';
$objorder = new FournisseurCommandeext($db);
$product = new Product($db);
//listamos las ordenes a proveedor 
$filterstatic = " AND c.fk_soc = ".$object->fk_soc;
$filterstatic.= " AND c.fk_statut > 0 AND c.fk_statut < 5";
$res = $objorder->fetchOrderlines('','',0,0,array(1=>1),'AND',$filterstatic);
if ($res > 0)
{
	$num = count($objorder->linesdet);
	$lines = $objorder->linesdet;

	print '<div class="col-md-12">';
	print '<table class="table no-margin">';
	print '<thead>';
	print '<tr class="liste_titre">';
	print '<th>'.$langs->trans('Sel').'</th>';
	print '<th>'.$langs->trans('Order').'</th>';
	print '<th>'.$langs->trans('Product').'</th>';
	print '<th>'.$langs->trans('Unit').'</th>';
	print '<th>'.$langs->trans('Quant').'</th>';
	print '</tr>';
	print '</thead>';
	print '<tbody>';
	for ($j=0;$j<$num;$j++)
	{
		if ($lines[$j]->fk_unit)
		{
			$objunit = fetch_unit($lines[$j]->fk_unit,'label');
		}
		print '<tr>';
		print '<th>'.'<input type="checkbox" name="sel['.$lines[$j]->id.']">'.'</th>';
		print '<th>'.$lines[$j]->reforder.'</th>';
		print '<th>'.($lines[$j]->libelle?$lines[$j]->libelle:$lines[$j]->desc).'</th>';
		print '<th>'.($lines[$j]->fk_unit && $objunit->rowid == $lines[$j]->fk_unit?$objunit->label:'').'</th>';
		print '<th>'.$lines[$j]->qty.'</th>';
		print '</tr>';
	}
	print '</tbody>';
	print '</table>';
	print '</div>';
}

?>