<?php
if ($lWriteitem)
{
	if ($user->rights->budget->budr->crear)
	{
		$var = !$var;
		include_once DOL_DOCUMENT_ROOT.'/budget/tpl/framesbudget.tpl.php';
		$aCat = array ('MAT'=>$langs->trans('Material'),'MDO'=>$langs->trans('Mano de Obra'),'EMH'=>$langs->trans('Equipo y Maquinaria'));
		$aCat = $aStrbudget[$id]['aStrref'];
		$aCat = $aStrbudget[$id]['aStrlabel'];
		$form = new Formv($db);
		print "<tr $bc[$var]>";
		print '<td nowrap>';
		$ajaxOptions = '';
		//$ayaxOptions = array('onkeyup="javascript:this.value=this.value.toUpperCase();"');
		if ($action == 'editres')
		{
			print '<input type="hidden" name="product" value="'.$newdata->fk_product.'">';
			print $newdata->detail;
		}
		else
			print $form->select_produits_budget($object->id,$newdata->fk_product,'product','',$conf->product->limit_size,0,1,2,'',1,$ajaxOptions,'','','resource');
		print '<input id="refsearch" type="hidden" name="refsearch" value="">';
		print '<input type="hidden" name="type" value="product">';
		if ($action == 'editres')
			print '<input type="hidden" name="fk_product_budget" value="'.$newdata->fk_product_budget.'">';
		else
			print '<input id="fk_product_budget" type="hidden" name="fk_product_budget" value="">';
		print '</td>';
		print '<td id="refcatn" style="display:table-cell;">&nbsp;</td>';
		print '<td id="refcat" style="display:none;">'.$form->selectarray('code_structure',$aCat,(GETPOST('code_categorie')?GETPOST('code_categorie'):$newdata->code_structure),1);
		print '</td>';
		print '<td>';
		if ($action == 'editres')
		{
			print $objectdetadd->getLabelOfUnit('short');
		}
		else
		{
			print $form->selectUnits($newdata->fk_unit,'fk_unit',0);
		}
		print '</td>';
		print '<td>'.'<input type="number" id="quant" class="flat len80" step="any" name="quant" value="'.$newdata->quant.'">'.'</td>';
		//echo $newdata->code_structure.' '.$aStrgroupcat['MA'].' '.$aStrgroupcat['MO'];
		if ($action == 'editres' && ($aStrgroupcat['MA'] == $newdata->code_structure || $aStrgroupcat['MO']  == $newdata->code_structure))
		{
			print '<td>'.'<input type="hidden" name="percent_prod" value="'.($newdata->percent_prod?$newdata->percent_prod:100).'">'.'</td>';
			print '<td>'.'<input type="hidden" name="amount_noprod" value="'.($newdata->amount_noprod?$newdata->amount_noprod:0).'">'.'</td>';
		}
		elseif ($action == 'editres' && $aStrgroupcat['MQ'] == $newdata->code_structure)
		{
			print '<td>'.'<input type="number" id="percent_prod" class="flat len80" step="any" name="percent_prod" value="'.($newdata->percent_prod?$newdata->percent_prod:100).'">'.'</td>';
			print '<td>'.'<input type="number" id="amount_noprod" class="flat len80" step="any" name="amount_noprod" value="'.($newdata->amount_noprod?$newdata->amount_noprod:0).'">'.'</td>';
		}
		else
		{
			print '<td>'.'<input type="number" id="percent_prod" class="flat len80" step="any" name="percent_prod" value="'.($newdata->percent_prod?$newdata->percent_prod:100).'">'.'</td>';
			print '<td>'.'<input type="number" id="amount_noprod" class="flat len80" step="any" name="amount_noprod" value="'.($newdata->amount_noprod?$newdata->amount_noprod:0).'">'.'</td>';
		}
		if ($action == 'editres')
		{
			print '<input type="hidden" id="price" name="price" value="'.$newdata->amount.'">';
			print '<td align="right">'.$newdata->amount.'</td>';
		}
		else
		{
			print '<td>'.'<input type="number" id="price" class="flat len80" step="any" name="price" value="'.$newdata->amount.'">'.'</td>';
		}
		print '<td></td>';
		print '<td>';
		print '<button type="submit" name="save" value="'.$langs->trans('Save').'">'.img_picto('',DOL_URL_ROOT.'/budget/img/save','',1).'</button>';
		if ($action == 'editres')
			print '&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$idr.'&action=viewit" title="'.$langs->trans('Return').'">'.img_picto('',DOL_URL_ROOT.'/budget/img/return','',1).'</a>';
		print '</td>';
		print '</tr>';
	}
}
?>