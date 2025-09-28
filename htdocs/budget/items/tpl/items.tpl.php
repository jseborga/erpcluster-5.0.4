<?php

print '<div class="box">';
print '<h2>'.$pustr->detail.'</h2>';
if ($user->rights->priceunits->ite->crear)
{
	print '<form action="'.$url.'" method="POST">';
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
//print_liste_field_titre($langs->trans("Label"),"", "","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Unit"),"", "","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Quant"),"", "","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Price"),"", "","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Total"),"", "","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("Action"),"", "","","","",$sortfield,$sortorder);
print '</tr>';
print '</thead>';
print '<tbody>';
$filter = array(1=>1);
$filterstatic = " AND t.fk_pu_structure = ".$fk_pu_structure;
$filterstatic.= " AND t.fk_item = ".$id;
$objectdet->fetchAll($sortorder, $sortfield, 0, 0, $filter, 'AND',$filterstatic);
foreach((array) $objectdet->lines AS $i => $line)
{
	print '<tr>';
	//print '<td>'.$line->ref.'</td>';
	print '<td>'.$line->detail.'</td>';
	print '<td>'.$line->fk_unit.'</td>';
	print '<td>'.$line->quant.'</td>';
	print '<td>'.$line->price.'</td>';
	print '<td>'.price(price2num($line->quant * $line->price,'MT')).'</td>';
	print '<td>'.$langs->trans('??').'</td>';
	print '</tr>';
}
if ($user->rights->priceunits->ite->crear)
{
	print '<tr>';
	print '<td>';
	
	if ($pustr->ref == 'MAT')
	{
		print $form->select_produits_v('','product','',$conf->product->limit_size,0,-1,2,'',1,'','');
	}
	if ($pustr->ref == 'MDO')
	{
		print select_reshuman($selected,'product','',1);
		//print $form->select_produits_v('','product','',$conf->product->limit_size,0,-1,2,'',1,'','');
	}
	if ($pustr->ref == 'EMH')
	{
		print $form->select_produits_v('','product','',$conf->product->limit_size,0,-1,2,'',1,'','');
	}
	print '<br>'.'<input type="text" class="flat" name="detail" value="" >';

	print '</td>';
	//print '<td>'.$line->ref.'</td>';
	print '<td>';
	print select_cunit($fk_unit,'fk_unit','',1,'fk_unit','short_label');
	print '</td>';
	print '<td>'.'<input type="number" class="flat len80" step="any" name="quant" value="">'.'</td>';
	print '<td>'.'<input type="number" class="flat len80" step="any" name="price" value="">'.'</td>';
	print '<td></td>';
	print '<td>'.'<input type="submit" class="butAction" name="save" value="'.$langs->trans('Save').'">'.'</td>';
	print '</tr>';
}
print '</tbody>';
print '</table>';
if ($user->rights->priceunits->ite->crear)
{
	print '</form>';
}
print '</div>';
?>