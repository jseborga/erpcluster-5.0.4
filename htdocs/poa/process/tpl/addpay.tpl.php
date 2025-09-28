<?php

//verificamos saldo nuevamente
foreach ((array) $aPrev AS $fk_poaprev => $gest)
{
	$objcompr = new Poapartidacom($db);
	if ($objcompr->get_sum_pcp2($fk_poaprev,$i))
	{
		//total comprometido
		$totalcomp = $objcompr->total;
		$aTotalcomp[$gest] = $objcompr->aTotal[$gest];
		//array de comprom
		$aObjcomp[$gest] = $objcompr;
	}
	$objdeveng = new Poapartidadev($db);
	//$lAdvance = false;
	//if ($advance>0) $lAdvance = true;
	if ($objdeveng->get_sum_pcp2($fk_poaprev,$i,$lAdvance))
	{
		//total devengado
		$totaldev += $objdeveng->total;
		$aTotaldev[$gest]+= $objdeveng->aTotal[$gest];
		$aObjdev[$gest] = $objdeveng;
	}
}
$saldo = price2num($totalcomp - $totaldev,'MT');


print '<form name="fiche_dev" action="'.DOL_URL_ROOT.'/poa/process/fiche_pas2.php" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="adddev">';
print '<input type="hidden" name="lastlink" value="'.$_SERVER['PHP_SELF'].'?ida='.$ida.'">';
print '<input type="hidden" name="id" value="'.$idProcess.'">';
print '<input type="hidden" name="fk_contrat" value="'.$i.'">';
print '<input type="hidden" name="idrc" value="'.$idrc.'">';
print '<input type="hidden" name="dol_hide_leftmenu" value="1">';

print '<table class="table table-condensed">';
print '<thead>';
print '<tr>';
print '<th>'.$langs->trans('Contract').'</th>';
print '<th>'.$langs->trans('Company').'</th>';
print '<th>'.$langs->trans('Date').'</th>';
print '<th>'.$langs->trans('Amount').'</th>';
print '<th>'.$langs->trans('Nro.Aut.').'</th>';
print '<th>'.$langs->trans('Invoice').'</th>';
print '<th>'.$langs->trans('Action').'</th>';
print '</tr>';
print '</thead>';
print '<tbody>';
			//detalle de partidas a pagar
if (count($aObjcomp[$gestion]->array) > 0)
{
	print '<tr>';
	print '<td colspan="7"><br>'.$langs->trans('Details partidas').'</td>';
	print '</tr>';
	print '<tr>';
	print '<th>'.$langs->trans('Partida').'</th>';
	print '<th align="right">'.$langs->trans('Committed').'</th>';
	print '<th align="right">'.$langs->trans('Accrued').'</th>';
	print '<th align="right">'.$langs->trans('Balance to be paid').'</th>';
	print '</tr>';

	foreach ($aObjcomp[$gestion]->array AS $icomp => $objComppart)
	{
		print '<tr>';
		print '<td>'.$objComppart->partida.'</td>';
		print '<td align="right">'.price($objComppart->amount).'</td>';
		$sumaPartidaDeve = 0;
		if (count($aObjdev[$gestion]->array)>0)
		{
			foreach($aObjdev[$gestion]->array AS $ideve => $objDevepart)
			{
				if ($objDevepart->partida == $objComppart->partida &&
					$objDevepart->fk_poa_partida_com == $objComppart->rowid)
				{
					$sumaPartidaDeve+= $objDevepart->amount;
				}
			}
		}
		print '<td align="right"> '.price($sumaPartidaDeve).'</td>';
		$saldoPartida = $objComppart->amount - $sumaPartidaDeve;
		if ($objcompr->lPartidadif && $saldoPartida <> 0)
		{
			print  '<td align="right">'.'<input id="amount" type="number" step="any" name="amount['.$icomp.']" value="'.$saldoPartida.'" size="8" ></td>';
			print  '<input id="partida" type="hidden" name="partida['.$icomp.']" value="'.$objComppart->partida.'">';
			print '<input type="hidden" name="fk_structure" value="'.$objComppart->fk_structure.'">';
			print '<input type="hidden" name="fk_poa" value="'.$objComppart->fk_poa.'">';

		}
		else
		{
			print  '<td align="right"> '.price($saldoPartida).'</td>';
					//print  '<input id="amount" type="hidden" name="amount['.$icomp.']" value="'.$saldoPartida.'">';
			print  '<input id="amount" type="hidden" name="partida['.$icomp.']" value="'.$objComppart->partida.'">';
			print '<input type="hidden" name="fk_structure" value="'.$objComppart->fk_structure.'">';
			print '<input type="hidden" name="fk_poa" value="'.$objComppart->fk_poa.'">';
		}
		print '</tr>';

	}
}
			// contratos
print '<tr>';
print '<td>'.$objcont->array_options['options_ref_contrato'].'</td>';
print '<td>'.$objsoc->nom.'</td>';

//fecha autorizacion
print '<td>';
print $formadd->select_dateadd((empty($object->date_dev)?dol_now():$object->date_dev),'di_','','','','date_dev',1,1);
print '</td>';

			//monto autorizado
print '<td align="right">';
if ($objcompr->lPartidadif == false)
	print '<input type="number" class="form-control" step="any" id="amount" name="amount['.$icomp.']" value="'.(empty($objdev->amount)?price2num($saldo,'MT'):$objdev->amount).'">';
else
	print (empty($objdev->amount)?price(price2num($saldo,'MT')):$objdev->amount);
print '</td>';

//recuperamos el ultimo numero de autorizacion
$objectdev = new Poapartidadev($db);
if ($objectdev->get_maxref($object->gestion,$object->fk_area))
	$objdev->nro_dev = $objectdev->maximo;
//nro autorizacion
print '<td>';
if ($user->admin)
{
	print '<input type="text" id="nro_dev" class="form-control" name="nro_dev" value="'.$objdev->nro_dev.'" maxlength="5">'.' / '.'<input type="text" id="gestion" class="form-control" name="gestion" value="'.(empty($objdev->gestion)?date('Y'):$objdev->gestion).'"  maxlength="4">';
}
else
{
	print $objdev->nro_dev.'/'.($objdev->gestion?$objdev->gestion:$object->gestion);
	print '<input type="hidden" name="nro_dev" value="'.$objdev->nro_dev.'" maxlength="5">';
	print '<input type="hidden" name="gestion" value="'.(empty($objdev->gestion)?$object->gestion:$objdev->gestion).'">';
}
print '</td>';


//nro documento respaldo
print '<td>';
print '<input type="text" id="invoice" class="form-control" name="invoice" value="'.$objdev->invoice.'" maxlength="30">';
print '</td>';

print '<td align="center">';
print '<input type="image" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/mant/img/save.png" width="20" height="20">';

print '</td>';
print '</tr>';
print '</tbody>';
print '</table>';
print '</form>';

?>