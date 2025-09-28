<?php
//actualizamos en facture_fourn
//$objtmp = new FactureFournisseur($db);
$objtmp->fetch($id);
$objtmpadd->fetch ('',$id);
$objTypefacture->fetch(0,$objtmpadd->code_facture);
//recuperamos la suma total para actualizar en cabecera
$objectdetfiscal->get_sum_taxes($id);

$restmp = $objtmp->fetch_lines();
//sumamos el detalle de la factura fourn
$total_ttc = 0;
$total_ht = 0;
if ($restmp>0)
{
	$linestmp = $objtmp->lines;
	foreach ($linestmp AS $j => $linetmp)
	{
		$total_ttc+= $linetmp->total_ttc;
		$total_ht+= $linetmp->total_ht;
	}
}
$x = 1;
if (count($objectdetfiscal->aData)>0)
{
	$total_ht=0;
	foreach ((array) $objectdetfiscal->aData AS $code_tva => $data)
	{
		if ($code_tva == 'IVA')
		{
			$objtmp->tva = $data['tva_tx'];
			if ($conf->global->PURCHASE_ROUND_DOWN)
				$objtmp->total_tva = redondea_menor($data['total_tva']);
			else
				$objtmp->total_tva = $data['total_tva'];
		}
		else
		{
			$campo = 'localtax'.$x;
			$objtmp->$campo = $data['total_tva'];
			$objtmpadd->$campo = $data['total_tva'];
			$x++;
			if (empty($objTypefacture->nit_required))
				$total_ht += $data['total_tva'];
		}
		$objtmp->total_ttc = price2num($data['total_ttc'],'MT');
		if (empty($objTypefacture->nit_required))
			$objtmp->total_ht = price2num($objtmp->total_ttc-$total_ht,'MT');
		else
			$objtmp->total_ht = price2num($objtmp->total_ttc - $objtmp->total_tva,'MT');
		//$objtmp->remise = price2num($objtmp->total_ttc - $objtmp->total_tva,'MT');
		$objtmpadd->amountfiscal = price2num($data['total_amountbase'],'MT')+0;
		$objtmpadd->amountnofiscal = price2num($objtmp->total_ttc-$objtmpadd->amountfiscal,'MT')+0;
		//$objtmpadd->amount_ice = price2num($data['total_amountice'],'MT')+0;
		//$objtmpadd->discount = price2num($data['total_discount'],'MT')+0;
	}
}
else
{
	//debemos vaciar los valores
	$objtmp->tva = 0;
	$objtmp->total_ttc = $total_ttc;
	$objtmp->total_ht = $total_ht;
	$objtmpadd->amountfiscal = 0;
	$objtmpadd->amountnofiscal = 0;
	$objtmpadd->discount = 0;
}
//recuperamos de objectdetadd la suma de descuento y ice
$filter = array(1=>1);
$ids = implode(',',$objectdetfiscal->aDataid);
$filterstatic = " AND t.fk_facture_fourn_det IN (".$ids.")";
$resdadd = $objectdetadd->fetchAll('','',0,0,$filter,'AND',$filterstatic);
foreach ((array) $objectdetadd->lines AS $j => $line)
{
	$objtmp->total_ttc+=$line->amount_ice;
	$objtmp->remise+=$line->discount;
	$objtmpadd->total_ttc+=$line->amount_ice;
	$objtmpadd->discount+=$line->discount;
	$objtmpadd->amount_ice+=$line->amount_ice;
}

$resup = $objtmp->updatetot($user);
if ($resup <=0)
{
	setEventMessages($langs->trans('Error de updatetot'), null, 'errors');
	$error++;
}
$resupadd = $objtmpadd->update($user);
if ($resupadd <=0)
{
	setEventMessages($langs->trans('Error de resupadd'), null, 'errors');
	$error++;
}

function redondea_menor($value)
{
	$num = floor($value * 100);
	$num = $num / 100;
	return $num;
}
?>