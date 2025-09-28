<?php

require_once DOL_DOCUMENT_ROOT."/poa/class/poaprocesscontratext.class.php";
require_once DOL_DOCUMENT_ROOT."/poa/guarantees/class/poaguarantees.class.php";
require_once DOL_DOCUMENT_ROOT."/contrat/class/contrat.class.php";

$objpcon = new Poaprocesscontrat($db);
$objguar = new Poaguarantees($db);
$objcon =  new Contrat($db);

$datehoy = dol_now();

//obtenemos todas las garantias
$objguar->getlistuser(1);
foreach ((array) $objguar->array AS $i => $objdatag)
{
	if ($objdatag->fk_user_create == $user->id)
	{
		$codeContrat = '';
		$objcon->fetch($objdatag->fk_contrat);
		if ($objcon->id == $objdatag->fk_contrat)
			$codeContrat = $objcon->array_options['options_ref_contrato'];
		$datemax = $objdatag->date_fin;
		//validamos la fecha fin con la fecha actual y final
		//restamos las fechas
		$dif = ($datemax - $datehoy)/24/60/60 + 1;
		//validamos
		//					if ($dif <= 0)
			//					    $mesg .= '<div class="error">'.$langs->trans("Error, la garantia").' '.$objdatag->ref.' '.$langs->trans("del contrato").' '.$codeContrat.', '.$langs->trans('Preventive').' '.$objdata->nro_preventive.', '.$langs->trans("esta vencido").'</div>';
		if ($dif > 0 && $dif <= 20)
			$mesg .= '<div class="error">'.$langs->trans("Error, la garantia").' '.$objdatag->ref.' '.$langs->trans("del contrato").' '.$codeContrat.', '.$langs->trans('Preventive').' '.$objdatag->nro_preventive.', '.$langs->trans("vence en").' '.round($dif).' '.$langs->trans('Days').'</div>';

	}
}

/*
//verificamos que contratos tiene el usuario
$objprev->getlist($gestion,$user->id);
$array = $objprev->array;
//variable fechas
if (count($array)>0)
{
	foreach ((array) $array AS $i => $objdata)
	{
		//buscamos el compromiso
		$aComarray = array();
		$objcom->getlist($objdata->id);
		$aComarray = $objcom->array;
		foreach((array) $aComarray AS $j => $objdatacom)
		{
			//recorremos los contratos
			if($objpcon->fetch($objdatacom->fk_contrat)>0)
			{
				//buscamos el contrato
				$codeContrat = '';
				$objcon->fetch($objpcon->fk_contrat);
				if ($objcon->id == $objpcon->fk_contrat)
					$codeContrat = $objcon->array_options['options_ref_contrato'];
				//buscamos las garantias
				$objguar->getlist($objcon->id);
				foreach ((array) $objguar->array AS $k => $objdatag)
				{
					$datemax = $objdatag->date_fin;
					//validamos la fecha fin con la fecha actual y final
					//restamos las fechas
					$dif = ($datemax - $datehoy)/24/60/60 + 1;
					//validamos
//					if ($dif <= 0)
//					    $mesg .= '<div class="error">'.$langs->trans("Error, la garantia").' '.$objdatag->ref.' '.$langs->trans("del contrato").' '.$codeContrat.', '.$langs->trans('Preventive').' '.$objdata->nro_preventive.', '.$langs->trans("esta vencido").'</div>';
					if ($dif > 0 && $dif <= 20)
						$mesg .= '<div class="error">'.$langs->trans("Error, la garantia").' '.$objdatag->ref.' '.$langs->trans("del contrato").' '.$codeContrat.', '.$langs->trans('Preventive').' '.$objdata->nro_preventive.', '.$langs->trans("vence en").' '.round($dif).' '.$langs->trans('Days').'</div>';
				}
			}
		}
	}
}
*/
?>