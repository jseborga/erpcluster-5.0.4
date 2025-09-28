<?php

$filter = array(1=>1);
$filterstatic = " AND t.code_structure = '".$code_structure."'";
$filterstatic.= " AND t.fk_budget_task = ".$fk_task_parent;
$resbtr = $objectbtr->fetchAll($sortorder, $sortfield, 0, 0, $filter, 'AND',$filterstatic);
if ($action == 'viewit' && $object->fk_statut == 0)
{
	if ($lWriteitem)
		include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/add_complementary.tpl.php';
}
$sumacat = 0;
foreach((array) $objectbtr->lines AS $i => $lineb)
{
	$lProduct = false;
	if ($lineb->fk_product>0)
	{
		$product->fetch($lineb->fk_product);
		$lProduct= true;
	}
	$var = !$var;
	if (GETPOST('idreg') == $lineb->id && $action == 'editres')
	{
		$newdata = $lineb;
		if ($lWriteitem)
			include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/add_complementary.tpl.php';
	}
	else
	{
		$objprodb->fetch($lineb->fk_product_budget);
		print "<tr $bc[$var] id=".'"'.$lineb->id.'"'.">";
		print '<td align="center" class="none">'.($lProduct?$product->getNomUrl(1):img_picto($langs->trans('Product or service not registered'),DOL_URL_ROOT.'/budget/img/interrogacion.png','',1)).'</td>';
		//print '<td class="detail">'.$lineb->detail.'</td>';
		print '<td><div class="text" id="detail-'.$lineb->id.'">'.$lineb->detail.'</div></td>';
		print '<td class="fk_unit">'.$objprodb->getLabelOfUnit().'</td>';
		print '<td align="right"><div class="text" id="quant-'.$lineb->id.'">'.number_format($lineb->quant,$general->decimal_quant).'</div></td>';
		print '<td align="right"><div class="text" id="percent_prod-'.$lineb->id.'">'.$lineb->percent_prod.' %</div></td>';
		print '<td align="right"><div class="text" id="amount_noprod-'.$lineb->id.'">'.number_format($lineb->amount_noprod,$general->decimal_pu).'</div></td>';
		print '<td align="right"><div class="text" id="amount-'.$lineb->id.'">'.number_format($lineb->amount,$general->decimal_pu).'</div></td>';
		$nprod = price2num($lineb->quant * $lineb->percent_prod * $lineb->amount / 100,$general->decimal_total);
		$nnprod = price2num($lineb->quant * (100-$lineb->percent_prod) * $lineb->amount_noprod / 100,$general->decimal_total);
		$ntotal = $nprod + $nnprod;
		print '<td align="right">'.price(price2num($ntotal,$general->decimal_total)).'</td>';
		$sumacat+=$ntotal;
		print '<td>';
		if ($lWriteitem)
		{
			if ($user->rights->budget->budr->mod && $object->fk_statut == 0)
				print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$fk_task_parent.'&idreg='.$lineb->id.'&action=editres">'.img_picto($langs->trans('Edit'),'edit').'</a>';
			if ($user->rights->budget->budr->del && $object->fk_statut == 0)
				print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$fk_task_parent.'&idreg='.$lineb->id.'&action=deleteres">'.img_picto($langs->trans('Delete'),'delete').'</a>';
		}
		print '</td>';
		print '</tr>';
	}
}

$nsumacat = price(price2num($sumacat,$general->decimal_total));
print '<script type="text/javascript">';
//print ' window.parent.document.getElementById('."'t_fk_unit'".').value = "'. $fk_unit.'"';
print ' window.parent.document.getElementById('."'c_'".').innerHTML = "'. $nsumacat.'"';
print '</script>';
?>