<?php

//procesa el calculo del iva y otros impuestos
//localtax1,2,3 son impuestos diferentes al IVA
	       //definimos el tipo de impuesto
$filter = array(1=>1);
$filterstatic = " AND t.code_facture = '".trim($objectadd->code_facture)."'";
$restva = $tvadef->fetchAll('','',0,0,$filter,'AND',$filterstatic,false);		
$tvaline = $tvadef->lines;
if ($restva <= 0)
{
	setEventMessages($langs->trans('NO esta definido fiscal'), null, 'errors');
	$error++;
}
foreach ((array) $tvaline AS $j => $data)
{
	$nbase = 1;
	$tvatx[$data->code_tva] = $data->taux;
	$amount_ice = GETPOST('amount_ice')+0;
	if ($lines[$i]->fk_product > 0 && $productadd->fk_product == $lines[$i]->fk_product)
	{
		if ($productadd->percent_base > 0)
			$nbase = $productadd->percent_base/100;
		if (!$productadd->sel_iva && $data->code_tva == 'IVA')
		{
			$tvatx[$data->code_tva]=0;
			$data->taux = 0;
		}
						//revisamos si requiere registro de ice
		if ($productadd->sel_ice)
		{
			if (GETPOST('amount_ice') <=0)
			{
				$langs->load("errors");
				setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Amountice")), 'errors');
			}
		}
		else $amount_ice = 0;
	}
	if (trim(strtoupper($price_base_type)) == 'TTC')
	{
						//incluye iva
		if ($remise_percent>0)
		{
			$pricetot  = $qty * $pu;
			$discounttmp  = $pricetot * $remise_percent / 100;
			$pricetmp  = $qty * $pu;
			$pricebase = ($pricetot-$discounttmp) * $nbase;
			$pricetot  = $pricetot - $discounttmp;
			$lines[$i]->remise = $discounttmp;
			$lines[$i]->remise_percent = $remise_percent;
		}
		elseif ($discount>0)
		{
			$pricetot = $qty * $pu;
			$pricetot = $pricetot - $discount;
			$pricebase = $pricetot * $nbase;
			$lines[$i]->remise = $discount;
			$lines[$i]->remise_percent = $discount / $pricetot * 100;
		}
		else
		{
			$pricetot = $qty * $pu;
			$pricebase = $pricetot * $nbase;
			$lines[$i]->remise = 0;
			$lines[$i]->remise_percent = 0;
		}
		//$pricebase = $qty * $pu * $nbase;
		$tvacalc[$data->code_tva] = $pricebase * $data->taux / 100;
		$tvaht[$data->code_tva] = $pricetot - $tvacalc[$data->code_tva];
		$tvattc[$data->code_tva] = $pricetot;
		$tvabase[$data->code_tva] = $pricebase;

		$lines[$i]->total_ht = $tvaht[$data->code_tva];
		$lines[$i]->total_ttc = $pricetot;

		
		$lines[$i]->subprice = $lines[$i]->total_ht/$lines[$i]->qty;
		$lines[$i]->pricebase = $pricebase;
		$lines[$i]->amount_base = $pricebase;
		if (empty($data->taux))
		{
			$lines[$i]->pricebase = 0;
			$pricebase = 0;
		}
	}
	else
	{
		$tax = 1/(1-($data->taux/100));
		$priceht = $qty * $pu;
		$pricebaseht = $qty * $pu * $nbase;
		$pricebase = $pricebaseht * $tax / 100;
		$price = $priceht * $tax / 100;
		$tvacalc[$data->code_tva] = $pricebase-$pricebaseht;
		$tvattc[$data->code_tva] = $price;
		$tvaht[$data->code_tva] = $priceht;
		$lines[$i]->total_ht = $priceht;
		$lines[$i]->total_ttc = $price;
		$lines[$i]->subprice = $pu;
		$lines[$i]->price = price2num($price / $qty);
		$lines[$i]->pricebase = $pricebase;
		if (empty($data->taux))
		{
			$lines[$i]->pricebase = 0;
			$pricebase = 0;
		}
	}
							//vamos condicionando
	if ($data->code_tva == 'IVA')
	{
		$lines[$i]->tva_tx = $data->taux;
		$lines[$i]->total_tva = $tvacalc[$data->code_tva];
	}
	else
	{
		$lines[$i]->tva_tx = 0;
		$campotx = 'localtax'.$k.'_tx';
		$campottx = 'localtax'.$k.'_type';
		$campotot = 'total_localtax'.$k;
		$campotfx = 'localtax'.$k;

		//$lines->$campotx = $tvacalc[$data->code_tva];
		$lines[$i]->$campotx = $data->taux;
		$lines[$i]->$campotfx = $tvalc[$data->code_tva];

		$lines[$i]->$campottx = $data->code_tva;
		$lines[$i]->$campotot = $tvacalc[$data->code_tva];
		$aTotaltav[$id][$campotfx]+=$tvalc[$data->code_tva];
		$k++;
	}
}
?>