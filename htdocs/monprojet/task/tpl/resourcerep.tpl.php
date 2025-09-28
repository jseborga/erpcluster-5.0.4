<?php

$filter = array(1=>1);
$filterstatic = " AND t.group_resource = '".$group."'";
$resbtr = $objectptr->fetchAll($sortorder, $sortfield, 0, 0, $filter, 'AND',$filterstatic);
foreach((array) $objectptr->lines AS $l => $lineb)
{
	$objectpp->fetch($lineb->fk_product_projet);

	$lProduct = false;
	if ($lineb->fk_product>0)
	{
		$product->fetch($lineb->fk_product);
		$lProduct= true;
	}
	$var = !$var;
	if (GETPOST('idreg') == $lineb->id && ($action == 'editres' || $action == 'createaddr'))
	{
		//es modificacion
		$lModif = true;
		$newdata = $lineb;
		$type_resource 	= $newdata->type_resource;
		$fk_soc 		= $newdata->fk_object;
		$fk_member 		= $newdata->fk_object;
		$fk_product     = $newdata->fk_object;
		$group_resource = $newdata->group_resource;
		if ($newdata->objectdet == 'commande_fournisseurdet')
			$fk_commande = $newdata->fk_objectdet;
		if ($newdata->objectdet == 'pcontrat')
			$fk_pcontract = $newdata->fk_objectdet;
		if ($newdata->objectdet == 'product')
			$fk_product = $newdata->fk_objectdet;
		include DOL_DOCUMENT_ROOT.'/monprojet/task/tpl/add_resource.tpl.php';
	}
	else
	{
		print "<tr $bc[$var] id=".'"'.$lineb->id.'"'.">";
		if ($lineb->object == 'product')
		{
			$product->fetch($lineb->fk_object);
			print '<td>'.$product->getNomUrl(1).'</td>';
		}
		elseif ($lineb->object == 'societe')
		{
			$societe->fetch($lineb->fk_object);
			print '<td>'.$societe->getNomUrl(1).'</td>';
		}
		elseif ($lineb->object == 'adherent')
		{
			$adherent->fetch($lineb->fk_object);
			print '<td>'.$adherent->getNomUrl(1).' '.$adherent->getFullName($langs).'</td>';
		}
		elseif ($lineb->object == 'assignment')
		{
			$assignment->fetch($lineb->fk_object);
			print '<td>'.$assignment->getNomurl(1).'</td>';
		}
		else
		{
			print '<td></td>';
		}
		//print '<td>X'.$lineb->ref_ext.'</td>';
		print '<td><div class="text" id="detail-'.$lineb->id.'">'.$lineb->detail.'</div></td>';
		if ($objectpp->id == $lineb->fk_product_projet)
			print '<td class="fk_unit">'.$objectpp->getLabelOfUnit().'</td>';
		else
			print '<td>no definido</td>';
		print '<td align="right"><div class="text" id="quant-'.$lineb->id.'">'.number_format($lineb->quant,$general->decimal_quant).'</div></td>';
		//print '<td align="right"><div class="text" id="percent_prod-'.$lineb->id.'">'.$lineb->percent_prod.' %</div></td>';
		//print '<td align="right"><div class="text" id="amount_noprod-'.$lineb->id.'">'.number_format($lineb->amount_noprod,$general->decimal_pu).'</div></td>';
		//print '<td align="right"><div class="text" id="amount-'.$lineb->id.'">'.number_format($lineb->amount,$general->decimal_pu).'</div></td>';
		$nprod = price2num($lineb->quant * $lineb->percent_prod * $lineb->amount / 100,$general->decimal_total);
		$nnprod = price2num($lineb->quant * (100-$lineb->percent_prod) * $lineb->amount_noprod / 100,$general->decimal_total);
		$ntotal = $nprod + $nnprod;
		//print '<td align="right">'.number_format($ntotal,$general->decimal_total).'</td>';

		print '<td align="right">';
		if ($user->rights->budget->budr->mod)
			print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$fk_task_parent.'&idreg='.$lineb->id.'&action=editres&withproject=1">'.img_picto($langs->trans('Edit'),'edit').'</a>';
		if ($user->rights->budget->budr->del)
			print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$fk_task_parent.'&idreg='.$lineb->id.'&action=deleteres&withproject=1">'.img_picto($langs->trans('Delete'),'delete').'</a>';
		print '</td>';
		print '</tr>';
	}
}
?>