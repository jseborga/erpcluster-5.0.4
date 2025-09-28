<?php

	//buscamos el item
$res2 = $objectdet->fetch(GETPOST('idr'));
$nextid = $objectdet->next_ref($id,$idg);
$previd = $objectdet->previous_ref($id,$idg);

if ($res2>0)
{
	$resitem = $items->fetch($objectdet->fk_task);
	$res3 = $objectdetadd->fetch(0,$objectdet->id);
	if ($action == 'edititem')
	{
		$newdata = $objectdet;
		$newdata->fk_unit = $objectdetadd->fk_unit;
		$newdata->unit_budget = $objectdetadd->unit_budget;
		$newdata->unit_amount = $objectdetadd->unit_amount;
		include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/add_field.tpl.php';
	}
	else
	{
		print '<table class="table border centpercent">'."\n";
		print '<thead>';
		print '<tr>';
		print_liste_field_titre($langs->trans('Fieldref'),$_SERVER['PHP_SELF'],'t.ref','',$params,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Fielditem'),$_SERVER['PHP_SELF'],'t.fk_task','',$params,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Fieldunit'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Fieldquant'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('P.U.'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Amount'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Action'),$_SERVER['PHP_SELF'],'','',$params,'align="right"',$sortfield,$sortorder);

		print '</tr>';
		print '</thead>';

		print '<tbody>';
			if (!$objectdetadd->complementary)
				print "<tr $bc[$var]>";
			else
				print '<tr class="complementary">';


		print '<td>'.($items->ref?'':img_picto($langs->trans('ItemNoregistered'),'warning')).' '.$objectdet->ref.'</td>';
		print '<td>'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&idr='.$objectdet->id.'&action=viewit" id="a'.$items->id.'">'.$objectdet->label.'</a></td>';
		print '<td>'.$objectdetadd->getLabelOfUnit().'</td>';
		print '<td>'.$objectdetadd->unit_budget.'</td>';
		print '<td align="right">'.price2num($objectdetadd->unit_amount,'MU').'</td>';
		print '<td align="right">'.price2num($objectdetadd->unit_budget * $objectdetadd->unit_amount,'MT').'</td>';
		print '<td nowrap>';
		if ($user->rights->budget->budi->mod)
			print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$objectdet->id.'&action=edititem">'.img_picto($langs->trans('Edit'),'edit').'</a>';
		if ($user->rights->budget->budi->clon)
			print '&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$line->id.'&action=clonitem">'.img_picto($langs->trans('Clone'),DOL_URL_ROOT.'/budget/img/clone1','',1).'</a>';
		if ($user->rights->budget->budi->rep)
		{
			print '&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$objectdet->id.'&action=process&seltype=PU">'.img_picto($langs->trans('Pdf'),'pdf2').'</a>';
			print '&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$objectdet->id.'&action=pdfitem">'.img_picto($langs->trans('Detail'),'detail').'</a>';
		}
		if ($user->rights->budget->budi->del)
			print '&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$objectdet->id.'&action=deleteitem">'.img_picto($langs->trans('Delete'),'delete').'</a>';
		//para recorrer por items
		print '&nbsp;';
		print ($previd>0?'<a class="btn btn-default" href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$previd.'&action=viewit"'.'>'.img_picto('','previous').'</a>':'');
		print '&nbsp;';
		print ($nextid>0?'<a class="btn btn-default" href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$nextid.'&action=viewit"'.'>'.img_picto('','next').'</a>':'');

		print '</td>';
		print '</tr>';
		print '</tbody>';
		print '</table>';
		//mostramos los insumos del item
		$fk_task_parent = $objectdet->id;
	}
}
//vemos la impresion
$html = '<table class="table centpercent">';
//list($htm,$total)= procedure_calc($id,GETPOST('idr'),1);
$objectdetadd->procedure_calc($id,GETPOST('idr'),1);
$htm = $objectdetadd->viewhtml;
$html.= $htm;
$html.= '</table>';
print $html;

?>