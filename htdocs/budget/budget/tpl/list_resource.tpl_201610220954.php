<?php
	//mostramos el resumen de los insumos
	//recuperamos de 
$filter = array(1=>1);
$filterstatic = " AND t.fk_budget = ".$id;
$filterstatic.= " AND t.fk_categorie > 0";
	//$filterstatic.= " AND t.ordby = 1";
$pustr->fetchAll('ASC', 'ordby', 0, 0, $filter, 'AND',$filterstatic,false);
foreach((array) $pustr->lines AS $i => $linestr)
{
	$aStr[$linestr->ref] = $linestr->ref;
	$aStrref[$linestr->ref] = $linestr->detail;
	$aStrlabel[$linestr->fk_categorie] = $linestr->detail;
}
$_SESSION['aStrref'] = serialize($aStrref);
$_SESSION['aStrlabel'] = serialize($aStrlabel);
$aDatamat = unserialize($_SESSION['aDatamat']);
if ($action == 'createres')
{

}
else
{
		//mostramos el resumen del consumo global
	print '<div class="fichecenter fichecenterbis">';
	if ($user->rights->priceunits->ite->crear)
	{
		print '<form action="'.$_SERVER['PHP_SELFT'].'?id='.$object->id.'" method="POST">';
		print '<input type="hidden" name="id" value="'.$id.'">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="addresource">';
		print '<input type="hidden" name="fk_pu_structure" value="'.$fk_pu_structure.'">';
		print '<input type="hidden" name="opt" value="'.$pustr->ref.'">';
	}

	print '<table class="noborder boxtable" width="100%">';
	print '<thead>';
	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans("Ref"),"", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Unit"),"", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Quant"),"", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("P.U."),"", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Amount"),"", "","","","",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Action"),"", "","","","",$sortfield,$sortorder);

	print '</tr>';
	print '</thead>';


	include DOL_DOCUMENT_ROOT.'/priceunits/budget/tpl/add_resource.tpl.php';


	print '<tbody>';
	foreach((array) $pustr->lines AS $i => $linestr)
	{
		print '<tr>';
		print '<td>'.$linestr->detail.'</td>';
		print '<td>'.'&nbsp:'.'</td>';
		print '<td>'.'&nbsp:'.'</td>';
		print '<td>'.'&nbsp:'.'</td>';
		print '<td>'.price(0).'</td>';
		print '<td>'.'<a href="'.$_SERVER['PHP_SELF'].'?action=viewit&id='.$id.'&idr='.$linestr->id.'" class="butAction" data="'.$linestr->id.'" id="show'.$linestr->ref.'" >'.' + '.'</a>'.'</td>';
		print '</tr>';
		$opt = $linestr->ref;
		$fk_pu_structure = $linestr->id;
		//if (GETPOST('idr')>0 && GETPOST('idr') == $line->id)
		//{

				//include DOL_DOCUMENT_ROOT.'/priceunits/budget/tpl/add_resource.tpl.php';
			include DOL_DOCUMENT_ROOT.'/priceunits/budget/tpl/resource.tpl.php';
		//}
	}
	print '</tbody>';
	print '</table>';
	if ($user->rights->priceunits->ite->crear)
	{
		print '</form>';
	}
	print '</div>';


}
?>