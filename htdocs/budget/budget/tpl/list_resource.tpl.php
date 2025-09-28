<?php
	//mostramos el resumen de los insumos
	//recuperamos de
$aDatamat = unserialize($_SESSION['aDatamat']);

if ($action != 'editres' && $object->fk_statut == 0)
	include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/add_resource.tpl.php';
$aCatcolor 		= $aStrbudget[$id]['aStrcatcolor'];
$aStrcatid 		= $aStrbudget[$id]['aStrcatid'];
$aStrcatgroup 	= $aStrbudget[$id]['aStrcatgroup'];
$aStrgroupcat 	= $aStrbudget[$id]['aStrgroupcat'];

foreach((array) $pustr->lines AS $i => $linestr)
{
	print '<tr style="background-color:#'.$aCatcolor[$linestr->fk_categorie].'; !important;">';
	print '<td colspan="7">'.$linestr->detail.'</td>';
	print '<td align="right"><div id="t_'.$linestr->fk_categorie.'">x</div></td>';
	print '<td></td>';
	print '</tr>';
	$opt = $linestr->ref;
	$code_structure = $linestr->ref;
	$code_structure = $linestr->fk_categorie;
	include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/resource.tpl.php';
}
//mostramos una linea para complementarios
print '<tr style="background-color:#000ffg; !important;">';
print '<td colspan="7">'.$langs->trans('Complementary activities').'</td>';
print '<td align="right"><span id="c_">0</span></td>';
print '<td></td>';
print '</tr>';
$opt = $linestr->ref;
$code_structure = -9;
include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/complementary.tpl.php';

?>