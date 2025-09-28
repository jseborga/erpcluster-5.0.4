<?php

// Load variable for pagination
$limit = GETPOST("limit")?GETPOST("limit","int"):$conf->liste_limit;
$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha');
$page = GETPOST('page','int');
if (empty($page) || $page == -1) { $page = 0; }
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortfield) $sortfield="t.rowid";
if (! $sortorder) $sortorder="ASC";
$params='';
if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;
$params.='&id='.$object->id;
$params.='&idg='.$idg;
$params.='&action='.$action;
$params.='&limit='.$limit;
$params.='&subaction='.$subaction;

$filter = array(1=>1);
$filterstatic = " AND t.fk_budget = ".$id;
$filterstatic.= " AND t.fk_task_parent = ".$fk_task_parent;
$objectdet->fetchAll($sortorder, $sortfield, 0, $offset, $filter, 'AND',$filterstatic);
$nbtotalofrecords = count($objectdet->lines);

$num = $objectdet->fetchAll($sortorder, $sortfield, $limit, $offset, $filter, 'AND',$filterstatic);

$var = false;
$lViewtask = true;
if (GETPOST('idr')) $lViewtask = false;


if ($user->rights->budget->budr->crear && !$lViewtask && $action != 'edititem')
{
	print '<form action="'.$_SERVER['PHP_SELFT'].'?id='.$object->id.'" method="POST">';
	print '<input type="hidden" name="id" value="'.$id.'">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	if ($action == 'editres')
	{
		print '<input type="hidden" name="action" value="updateresource">';
		print '<input type="hidden" name="idreg" value="'.GETPOST('idreg').'">';
	}
	else
		print '<input type="hidden" name="action" value="addresource">';
	print '<input type="hidden" name="idr" value="'.GETPOST('idr').'">';

}

if ($lViewtask)
{
	print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $params, $sortfield, $sortorder, '', $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);
}
print '<table class="table border centpercent">'."\n";
print '<thead>';
print '<tr>';
print_liste_field_titre($langs->trans('Fieldref'),$_SERVER['PHP_SELF'],'t.ref','',$params,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('Fielditem'),$_SERVER['PHP_SELF'],'t.fk_task','',$params,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('Fieldunit'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('Fieldquant'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
if ($action == 'viewit')
	print_liste_field_titre($langs->trans('Complementary'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
else
	print_liste_field_titre($langs->trans('Fieldpercentprod'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('Fieldamountnoprod'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('Fieldamountprod'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('Pricetotal'),$_SERVER['PHP_SELF'],'','',$params,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('Action'),$_SERVER['PHP_SELF'],'','',$params,'align="right"',$sortfield,$sortorder);

print '</tr>';
print '</thead>';

print '<tbody>';

if ($lViewtask)
{
	if ( $action != 'editres' && $action != 'createit' && !GETPOST('idr') && $object->fk_statut == 0)
	{
		//items
		if ($lWriteitem)
			include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/add_field.tpl.php';
	}
	$lines = $objectdet->lines;

	foreach((array) $lines AS $i => $line)
	{
		$var = !$var;
		$res2 = $objectdetadd->fetch(0,$line->id);
		if (empty($objectdetadd->c_grupo))
		{
			//asigno variables para el objectdet
			$objectdet->id=$line->id;
			$objectdet->fk_budget=$line->fk_budget;
			$objectdet->ref = $line->ref;
			$objectdet->label = $line->label;

			if (!$objectdetadd->complementary)
				print "<tr $bc[$var]>";
			else
				print '<tr class="complementary">';
			print '<td>'.$objectdet->getNomUrl().'</td>';
			print '<td>'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&idr='.$line->id.'&action=viewit" id="a'.$line->id.'" title="'.$line->label.'">'.dol_trunc($line->label,20).'</a></td>';
			print '<td>'.$objectdetadd->getLabelOfUnit('short').'</td>';
			print '<td>'.$objectdetadd->unit_budget.'</td>';
			print '<td>&nbsp;</td>';
			print '<td>&nbsp;</td>';
			print '<td align="right">'.price(price2num($objectdetadd->unit_amount,$general->decimal_pu)).'</td>';
			print '<td align="right">'.price(price2num($objectdetadd->unit_budget * $objectdetadd->unit_amount,$general->decimal_total)).'</td>';
			print '<td nowrap>';
			if ($lWriteitem)
			{
				if ($user->rights->budget->budi->mod && $object->fk_statut == 0)
					print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$line->id.'&action=edititem">'.img_picto($langs->trans('Edit'),'edit').'</a>';
				if ($user->rights->budget->budi->clon && $object->fk_statut == 0)
					print '&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$line->id.'&action=clonitem">'.img_picto($langs->trans('Clone'),DOL_URL_ROOT.'/budget/img/clone1','',1).'</a>';
				if ($user->rights->budget->budi->del && $object->fk_statut == 0)
					print '&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idg='.$idg.'&idr='.$line->id.'&action=deleteitem">'.img_picto($langs->trans('Delete'),'delete').'</a>';
			}
			if ($user->rights->budget->budi->rep)
			{
				print '&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$line->id.'&action=process&seltype=PU">'.img_picto($langs->trans('Pdf'),'pdf2').'</a>';
				print '&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$line->id.'&action=pdfitem">'.img_picto($langs->trans('Detail'),'detail').'</a>';
			}
			print '</td>';
			print '</tr>';
		}
	}
}
else
{
	//buscamos el item
	$res2 = $objectdet->fetch(GETPOST('idr'));
	$nextid = $objectdet->next_ref($id,$idg);
	$previd = $objectdet->previous_ref($id,$idg);

	if ($res2>0)
	{
		$resitem = $items->fetch($objectdet->fk_task);
		$res3 = $objectdetadd->fetch(0,$objectdet->id);
		if ($action == 'edititem' && $object->fk_statut == 0)
		{
			$newdata = $objectdet;
			$newdata->fk_unit = $objectdetadd->fk_unit;
			$newdata->unit_budget = $objectdetadd->unit_budget;
			$newdata->unit_amount = $objectdetadd->unit_amount;
			include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/add_field.tpl.php';
		}
		else
		{
			if (!$objectdetadd->complementary)
				print "<tr $bc[$var]>";
			else
				print '<tr class="complementary">';
			print '<td>'.($items->ref?'':img_picto($langs->trans('ItemNoregistered'),'warning')).' '.$objectdet->ref.'</td>';
			print '<td>'.'<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&idr='.$objectdet->id.'&action=viewit" id="a'.$items->id.'">'.$objectdet->label.'</a></td>';
			print '<td>'.$objectdetadd->getLabelOfUnit().'</td>';
			print '<td>'.$objectdetadd->unit_budget.'</td>';
			print '<td></td>';
			print '<td></td>';
			print '<td align="right">'.price(price2num($objectdetadd->unit_amount,$general->decimal_pu)).'</td>';
			print '<td align="right">'.price(price2num($objectdetadd->unit_budget * $objectdetadd->unit_amount,$general->decimal_total)).'</td>';
			print '<td nowrap>';
			if ($lWriteitem)
			{
				if ($user->rights->budget->budi->mod && $object->fk_statut == 0)
					print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$objectdet->id.'&action=edititem">'.img_picto($langs->trans('Edit'),'edit').'</a>';
				if ($user->rights->budget->budi->clon && $object->fk_statut == 0)
					print '&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$objectdet->id.'&action=clonitem">'.img_picto($langs->trans('Clone'),DOL_URL_ROOT.'/budget/img/clone1','',1).'</a>';
				if ($user->rights->budget->budi->del && $object->fk_statut == 0)
					print '&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idg='.$idg.'&idr='.$objectdet->id.'&action=deleteitem">'.img_picto($langs->trans('Delete'),'delete').'</a>';
			}
			if ($user->rights->budget->budi->rep)
				print '&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$objectdet->id.'&action=pdfitem">'.img_picto($langs->trans('Pdf'),'pdf2').'</a>';

			//para recorrer por items
			print '&nbsp;';
			print ($previd>0?'<a class="btn btn-default" href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$previd.'&action=viewit"'.'>'.img_picto('','previous').'</a>':'');
			print '&nbsp;';
			print ($nextid>0?'<a class="btn btn-default" href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$nextid.'&action=viewit"'.'>'.img_picto('','next').'</a>':'');

			print '</td>';
			print '</tr>';
			//mostramos los insumos del item
			$fk_task_parent = $objectdet->id;
			if ($action != 'deleteitem')
			{
				include DOL_DOCUMENT_ROOT.'/budget/budget/tpl/list_resource.tpl.php';
			}
		}
	}
}

$idsearch = GETPOST('idr');
if (!$object->data_type)
{
	if ($idsearch)
	{
		$suma = $objectdetaddtmp->procedure_calculo($user,$id,$idsearch,false);
		$aStr = $objectdetaddtmp->aStr;
	}
	$idsearch = GETPOST('idg');
	if ($idsearch)
	{
		$objectdetaddtmp->procedure_calculo_group($user,$id,$idsearch,true);
		$aStr = $objectdetaddtmp->aStracum;
	}
}
print '</tbody>';
print '</table>';

if (!$object->data_type)
{
	print '<table class="table border centpercent">';
	$htmltitle = '';
	$htmlbody = '';
	$nacum = 0;
	foreach ((array) $aStr AS $label => $value)
	{
		$htmltitle.= '<th align="right">'.$label.'</th>';
		$htmlbody.= '<td align="right">'.price(price2num($value,$general->decimal_total)).'</td>';
		$nacum+=$value;
	}
	$htmltitle.= '<th align="right">'.$langs->trans('Total').'</th>';
	$htmlbody.= '<td align="right">'.price(price2num($nacum,$general->decimal_total)).'</td>';
	print '<tr>'.$htmltitle.'</tr>';
	print '<tr>'.$htmlbody.'</tr>';
	print '</table>';
}
if ($user->rights->budget->budr->crear && !$lViewtask && $action != 'edititem')
{
	print '</form>';
}
if ($aLinkprod)
{
	foreach ((array) $aLinkprod AS $j => $data)
	{
		$lineid = $data['lineb'];
		$fk_task_parent = $data['fk_task_parent'];
		include DOL_DOCUMENT_ROOT.'/budget/budget/include/budget_task_productivity.inc.php';
	}

}

?>