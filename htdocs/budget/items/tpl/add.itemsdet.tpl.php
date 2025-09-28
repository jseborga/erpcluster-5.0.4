<?php
if ($user->rights->priceunits->ite->crear)
{
	print '<tr>';
	print '<td>';
	
	if ($line->ref == 'MAT')
	{
		print $form->select_produits_v('','product','',$conf->product->limit_size,0,-1,2,'',1,'','');
		print '<input type="hidden" name="type" value="product">';
	}
	if ($line->ref == 'MDO')
	{
		print select_reshuman($selected,'product','',1);
		print '<input type="hidden" name="type" value="planilla">';
		//print $form->select_produits_v('','product','',$conf->product->limit_size,0,-1,2,'',1,'','');
	}
	if ($line->ref == 'EMH')
	{
		print $form->select_produits_v('','product','',$conf->product->limit_size,0,-1,2,'',1,'','');
		print '<input type="hidden" name="type" value="assets">';
	}
	print '<input type="hidden" name="fk_pu_structure" value="'.$line->id.'" >';
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

?>