<?php

print '<div class="table-responsive">';
print '<form name="fiche_dev" action="'.$_SERVER['PHP_SELF'].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="adddev">';
print '<input type="hidden" name="id" value="'.$object->id.'">';
			// print '<input type="hidden" name="idc" value="'.$idc.'">';
print '<input type="hidden" name="fk_contrat" value="'.$idc.'">';
print '<input type="hidden" name="idrc" value="'.$idrc.'">';
print '<input type="hidden" name="modal" value="ficheaccrued">';

print '<table class="table table-hover" style="min-width=1000px" width="100%">';
print '<thead>';
print '<tr>';
print '<th>'.$langs->trans("Date").'</th>';
print '<th>'.$langs->trans("Amount").'</th>';
print '<th>'.$langs->trans("Nro.Aut.").'</th>';
print '<th>'.$langs->trans("Invoice").'</th>';
print '<th>'.$langs->trans("Action").'</th>';
print '</tr>';

			//detalle de partidas a pagar
if (count($aObjcomp[$gestion]->array) > 0)
{
	print '<tr>';
	print '<td colspan="5">'.$langs->trans('Details partidas').'</td>';
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
			print  '<td align="right">'.'<input class="form-control" type="number" step="any" name="amount['.$icomp.']" value="'.$saldoPartida.'" ></td>';
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
			//fecha autorizacion
print '<td>';
if ($user->admin)
{
	//print $formadd->select_dateboot('di_');
	//$formadd->select_dateadd($objectw_->date_tracking,'di_','','','',"date",1,1);
    //convertimos la fecha
    $aDate = dol_getdate(dol_now());
    if (!empty($objdev->date_dev))
        $aDate = dol_getdate($objdev->date_dev);
    $date_tracking = $aDate['year'].'-'.(strlen($aDate['mon'])==1?'0'.$aDate['mon']:$aDate['mon']).'-'.(strlen($aDate['mday'])==1?'0'.$aDate['mday']:$aDate['mday']);
    print '<div class="well well-sm col-sm-6" style="width:150px;">';
    print '          <div class="input-group date" id="divMiCalendario">
                      <input type="text" name="di_" id="datepay'.$lfpay.'" class="form-control" value="'.$date_tracking.'" readonly/>
                      <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                      </span>
                  </div>';
    print '</div>';
}
else
	print dol_print_date($objdev->date_dev);
print '</td>';

			//monto autorizado
print '<td align="right">';
if ($objcompr->lPartidadif == false)
	print '<input type="number" step="any" class="form-control" name="amount['.$icomp.']" value="'.(empty($objdev->amount)?price2num($saldo,'MT'):$objdev->amount).'">';
else
	print (empty($objdev->amount)?price(price2num($saldo,'MT')):$objdev->amount);
print '</td>';

			//recuperamos el ultimo numero de autorizacion
$objectdev = new Poapartidadev($db);
if ($objectdev->get_maxref($gestion,$objact->fk_area))
	$objdev->nro_dev = $objectdev->maximo;
			//nro autorizacion
print '<td>';
print '<div class="col-xs-5">';
print '<input type="text" class="form-control" name="nro_dev" value="'.$objdev->nro_dev.'" maxlength="5">';
print '</div>';
print '<div class="col-xs-6">';
print '<input type="text" class="form-control" name="gestion" value="'.(empty($objdev->gestion)?date('Y'):$objdev->gestion).'"  maxlength="4">';
print '</div>';
print '</td>';

			//nro documento respaldo
print '<td>';
print '<input type="text" class="form-control" name="invoice" value="'.$objdev->invoice.'" maxlength="30">';
print '</td>';

print '<td align="center">';
print '<input type="image" alt="'.$langs->trans('Save').'" src="'.DOL_URL_ROOT.'/mant/img/save.png" width="14" height="14">';

print '</td>';
print '</tr>';

print '</table>';
print '</form>';
print '</div>';

?>