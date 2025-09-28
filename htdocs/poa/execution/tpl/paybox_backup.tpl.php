<?php

//payment
$aPayment     = array();
$aPaymentdate = array();
$ticks = '0';
$ticks_label = '"$b 0"';
foreach((array) $aContrat AS $i => $ni)
{
	if ($aContratpay[$i])
	{
		$objcont->fetch($i);
		$objcont->fetch_lines();
		$array = $arraypay[$obj->id];
			//obtenemos la suma de acuerdo al tipo de factura
			//payment
		if (count($data['idprev'])>0) ksort($data['idprev']);
		$a = true;
		foreach ($data['idprev'] AS $k)
		{
				//buscamos el preventivo para obtener la gestion
			$obj_prev  = new Poaprev($db);
			$obj_prev->fetch($k);
			$aPrev[$k] = $obj_prev->gestion;
			$a = !$a;
			$apay = listpayment($k,$i);

				//agrupamos los pagos por el id
			foreach ((array) $apay AS $l => $objp)
			{
				if (!empty($objp->invoice))
				{
					$type = 'Payments';
					if (($objp->date_dev*1) >= ($aFactdoc['Payments']*1))
						$aFactdoc['Payments'] = $objp->date_dev;
					$aFact['Payments']+= $objp->amount;
					$aPayment[$i][$objp->fk_poa_prev][$objp->nro_dev]+=$objp->amount;
					$aPaymentdate[$i][$objp->fk_poa_prev][$objp->nro_dev]=$objp->date_dev;
				}
				else
				{
					if ($lAdvance)
					{
						$_SESSION['aListip'][$idProcess]['anticipo'] = true;
						$type = 'anticipo';
						if ($objp->date_dev >= $aFactdoc['anticipo'])
							$aFactdoc['anticipo'] = $objp->date_dev;
						$aFact['anticipo']+= $objp->amount;
						$aPayment[$i][$objp->fk_poa_prev][$objp->nro_dev]+=$objp->amount;
						$aPaymentdate[$i][$objp->fk_poa_prev][$objp->nro_dev]=$objp->date_dev;
					}
					else
					{
						$_SESSION['aListip'][$idProcess]['Payments'] = true;
						$type = 'Payments';
						if ($objp->date_dev >= $aFactdoc['Payments'])
							$aFactdoc['Payments'] = $objp->date_dev;
						$aFact['Payments']+= $objp->amount;
						$aPayment[$i][$objp->fk_poa_prev][$objp->nro_dev]+=$objp->amount;
						$aPaymentdate[$i][$objp->fk_poa_prev][$objp->nro_dev]=$objp->date_dev;
					}
				}
				if ($type == 'Payments' && $object->gestion == $objp->gestion)
					$sumapay[$i] += $objp->amount;
				if ($type == 'Payments')
					$sumapayt[$i] += $objp->amount;
			}
			$aFilterpay = $aPayment[$i][$k];
			foreach ((array) $aFilterpay AS $nrodev =>$value)
			{
				$date_dev = $aPaymentdate[$i][$k][$nrodev];
				$aDatedev = dol_getdate($date_dev);


/*				print '<li class="time-label">';
				print '<span class="bg-aqua">'.dol_print_date($date_dev,'day') .'</span>';
				print '</li>';
				print '<li>';
				print '<div class="timeline-item">';
				print '<div class="box box-solid bg-aqua">';
				print '<h3>'.$langs->trans('Payments').'</h3>';
				print '<div class="inner">';
				print '<table class="table">';
				print '<tbody>';
				print "<tr>";
				print '<td>';
				if (!empty($aContratcode[$i])) print $aContratcode[$i];
				else print $objcont->ref;
				print '&nbsp;';
				print $aSocname[$objcont->fk_soc];
				print '</td>';
				print '<td>';
				print '<a class="btn btn-primary btn-sm bg-aqua" href="'.DOL_URL_ROOT.'/poa/process/fiche_pas2.php?id='.$idProcess.'&dol_hide_leftmenu=1" title="'.$langs->trans('Viewpayment').'">';
				print $nrodev.'/'.$aDatedev['year'];
				print '</a>';
				print '</td>';
				print '<td align="right">';
				print price($value);
				*/
				if (!empty($ticks) || !is_null($ticks)) $ticks.=', ';
				$ticks .= $value;
				if (!empty($ticks_label)) $ticks_label.=', ';
				$ticks_label .= '"$b '.$value.'"';

/*				print '</td>';
				print '<td align="right">';
				print '<a href="'.DOL_URL_ROOT.'/poa/process/fiche_autpay.php?id='.$idProcess.'&idr='.$objp->id.'" title="'.$langs->trans('Excel').'">';
				print '&nbsp;'.img_picto($langs->trans('Exportexcel'),DOL_URL_ROOT.'/poa/img/excel-icon','',true);
				print '</a>';
				print '</td>';
				print '</tr>';
				print '</tbody>';
				print '</table>';
				print '</div>';
				print '</div>';
				print '</div>';
				print '</li>';
				*/
			}
		}
		$saldo = $sumacom[$i] - $sumapay[$i];

		if ($saldo > 0 && $abc)
		{
				//saldo final gestion actual
			print '<li>';
			print '<div class="timeline-item">';
			print '<div class="box box-solid bg-aqua">';
			print '<h3>'.$langs->trans('Balance').'</h3>';
			print '<div class="inner">';
			print '<table width="100%">';
			print '<tr>';
			print '<td>';
			if (!empty($aContratname[$i]))
				print '<a  class="btn btn-primary btn-sm bg-aqua" href="'.DOL_URL_ROOT.'/contrat/fiche.php?id='.$i.'" target="blank_">'.$aContratname[$i].'</a>';
			else
				print '<a  class="btn btn-primary btn-sm bg-aqua" href="'.DOL_URL_ROOT.'/contrat/fiche.php?id='.$i.'" target="blank_">'.$obj->ref.'</a>';
			print '&nbsp;';
			print $aSocname[$objcont->fk_soc];
			print '</td>';

			print '<td colspan="2">';
			$saldo = $sumacom[$i] - $sumapay[$i];
			if ($saldo > 0)
			{
				print '<a  class="btn btn-primary btn-sm bg-aqua" href="'.$_SERVER['PHP_SELF'].'?ida='.$ida.'&i='.$i.'&id='.$idProcess.'&idrc='.$idrc.'&idp='.$id.'&dol_hide_leftmenu=1&action=createpayment" title="'.$langs->trans('Createpayment').'">';
				print $langs->trans('Createpayment');
				print '&nbsp;'.img_picto($langs->trans('Createpayment'),DOL_URL_ROOT.'/poa/img/deve','',true);
				print '</a>';
			}
			else
				print $langs->trans('Balance');

			print '</td>';
			print '<td align="right">';
			print price(price2num($saldo,'MT'));
			print '</td>';
			print '</tr>';

			print '<tr>';
			print '<td colspan="3">';
			print $langs->trans('Balance contract');
			print '</td>';
			print '<td align="right">';
			print price($sumacont[$i] - $sumapayt[$i]);
			print '</td>';
			print '</tr>';
			print '</table>';

			if ($saldo > 0)
			{
				if ($action=='createpayment' && $i == GETPOST('i','int'))
					include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/addpay.tpl.php';
			}
			print '</div>';
			print '</div>';
			print '</div>';
			print '</li>';
		}
	}
	$sumapaytotal = 500000;
	print '<div class="col-sm-6">';
	print '<input id="ex13" type="text" data-slider-max="140000" data-slider-min="0" data-slider-ticks="['.$ticks.']" data-slider-ticks-snap-bounds="30" data-slider-ticks-labels='."'[".$ticks_label."]'".'/>';
	//print '<div data-slider-id="red" class="slider slider-horizontal"></div>';
//	print '<input value="" data-slider-id="red" class="slider form-control" type="text" data-slider-tooltip="show" data-slider-selection="before" data-slider-orientation="horizontal" data-slider-value="[-100,100]" data-slider-step="5" data-slider-max="140000" data-slider-min="0" >';
	//print '<input id="range_4" type="text" value="'.$sumapaytotal.'" name="range_4" style="display: none"/>';
	print '</div>';

	print '<div class="col-sm-6">';
	print '<input class="slider form-control" type="text" data-slider-id="red" data-slider-tooltip="show" data-slider-selection="before" data-slider-orientation="horizontal" data-slider-value="[-100,100]" data-slider-step="5" data-slider-max="200" data-slider-min="-200" value="-180,50" style="display: none;" data="value: '."-180,50'".'">';
	print '</div>';

	print '<script src="../css/plugins/jQuery/jQuery-2.1.4.min.js"></script>';
	print '<script src="../css/plugins/ionslider/ion.rangeSlider.min.js"></script>';
	print '<script src="../css/plugins/fastclick/fastclick.min.js"></script>';
	print '<script src="../css/dist/js/app.min.js"></script>';
	print '<script src="../css/slider/js/bootstrap-slider.js"></script>';
	print '<br>';
	include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/javascriptpayslider.tpl.php';
	include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/js.ion.tpl.php';

}

?>