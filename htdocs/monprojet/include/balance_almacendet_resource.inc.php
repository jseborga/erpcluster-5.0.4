<?php 
//procedimiento de verificación para registro en la tabla projet_task_resource_almacendet
//
$fk_entrepot = 0;
if ($conf->almacen->enabled)
{
	require_once DOL_DOCUMENT_ROOT.'/almacen/local/class/entrepotrelationext.class.php';
	$objrel = new Entrepotrelationext($db);
	$res = $objrel->fetchAll('','',0,0,array(1=>1),'AND'," AND fk_projet = ".$projectstatic->id,true); 
	$fk_entrepot=$objrel->id;
}
else
{
	setEventMessages($langs->trans('Error, no esta activado modulo de almacen'),null,'errors');
	exit;
}
require_once DOL_DOCUMENT_ROOT.'/almacen/class/solalmacenext.class.php';
require_once DOL_DOCUMENT_ROOT.'/almacen/class/solalmacendetext.class.php';
$solalmacen    = new Solalmacenext($db);
$filterstatic = " AND t.fk_entrepot = ".$fk_entrepot;
$filterstatic.= " AND t.statut = 2";
$res = $solalmacen->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic);
$idsAlmacen = '';
if ($res>0)
{
	foreach ($solalmacen->lines AS $j => $line)
	{
		if (!empty($idsAlmacen)) $idsAlmacen.=',';
		$idsAlmacen.= $line->id;
	}
}
if (!empty($idsAlmacen))
{
	$solalmacendet = new Solalmacendetext($db);
	$filterstatic = " AND t.fk_almacen IN (".$idsAlmacen.")";
	$filterstatic .= " AND t.fk_product = ".$fk_product;
	$res = $solalmacendet->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic,true);

	if ($res == 1)
	{
		//verificamos la cantidad a descargar
		$objectptralmd->get_sum_product($solalmacendet->fk_product,$solalmacendet->id,0,0);
		$saldo = price2num($solalmacendet->qty_livree-$objectptralmd->total);
		if ($saldo>0 && $saldo >= GETPOST('quant'))
		{
			$aDischargalmdet[$solalmacendet->id] = array('subprice'=>$solalmacendet->price,'quant'=>GETPOST('quant'));
		}
		else
		{
			$error++;
			setEventMessages($langs->trans('Error, no existe saldo suficiente para hacer uso del recurso'),null,'errors');
		}
	}						
	elseif ($res > 0)
	{
		$quantsol = GETPOST('quant');
		$aReg = array();
		$lSum = true;
		foreach ($solalmacendet->lines AS $j => $line)
		{
			if ($lSum)
			{
				//verificamos la cantidad a descargar
				$objectptralmd->get_sum_product($line->fk_product,$line->id,0,0);
				$saldo = $line->qty_livree-$objectptralmd->total-$aReg[$line->id];
				if ($saldo>0)
				{
					if ($saldo >= $quantsol)
					{
						$aDischargalmdet[$line->id] = array('subprice'=>$line->price,'quant'=>$quantsol);
						$aReg[$line->id]+=$quantsol;
						$lSum = false;
						$quantsol=0;
					}
					else
					{
						$aDischargalmdet[$line->id] = array('subprice'=>$line->price,'quant'=>$saldo);
						$aReg[$line->id]+=$saldo;
						$quantsol= $quantsol-$saldo;
					}
				}
			}
		}
		if ($quantsol>0)
		{
			$error++;
			setEventMessages($langs->trans('Error, no existe saldo suficiente para hacer uso del recurso'),null,'errors');			
		}
	}
	else
	{
		setEventMessages($langs->trans('Error, no existe solicitud del producto '),null,'errors');
		$error++;
	}
}
else
{
	setEventMessages($langs->trans('Error, no cuenta con pedidos al almacen'),null,'errors');
	$error++;
}


