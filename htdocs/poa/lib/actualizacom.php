<?php

$sql = "";
$sql = "SELECT";
$sql.= " t.rowid AS id,";
$sql.= " t.fk_poa_partida_pre,";
$sql.= " t.fk_poa_prev,";
$sql.= " t.fk_structure,";
$sql.= " t.fk_poa,";
$sql.= " t.fk_contrat,";
$sql.= " t.fk_contrato,";
$sql.= " t.partida,";
$sql.= " t.amount,";
$sql.= " t.date_create,";
$sql.= " t.tms,";
$sql.= " t.statut,";
$sql.= " t.active";


$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_com as t";
$sql.= " WHERE t.fk_contrato = 0 OR t.fk_contrato IS NULL";
$resql=$db->query($sql);
if ($resql)
{
	$num = $db->num_rows($resql);
	if ($db->num_rows($resql))
	{
		$i = 0;
		while ($i < $num)
		{
			$obj = $db->fetch_object($resql);
	    //obtenemos el id del proceso fk_contrat
			$fkcontrat = $obj->fk_contrat;
			$objppc = new Poaprocesscontrat($db);
			$objppc->fetch($fkcontrat);
			if ($objppc->id == $fkcontrat)
			{
				$objcomp->fetch($obj->id);
				if ($objcomp->id == $obj->id)
				{
					$objcomp->fk_contrato = $objppc->fk_contrat;
					$objcomp->update($user);
				}
			}
			$i++;
		}
	}
}

$sql = "";
$sql = "SELECT";
$sql.= " t.rowid as id,";

$sql.= " t.gestion,";
$sql.= " t.fk_poa_prev,";
$sql.= " t.fk_structure,";
$sql.= " t.fk_poa,";
$sql.= " t.fk_contrat,";
$sql.= " t.fk_contrato,";
$sql.= " t.nro_dev,";
$sql.= " t.date_dev,";
$sql.= " t.partida,";
$sql.= " t.invoice,";
$sql.= " t.amount,";
$sql.= " t.date_create,";
$sql.= " t.tms,";
$sql.= " t.statut,";
$sql.= " t.active";


$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_dev as t";
$sql.= " WHERE t.fk_contrato = 0 OR t.fk_contrato IS NULL";
$resql=$db->query($sql);
if ($resql)
{
	$num = $db->num_rows($resql);
	if ($db->num_rows($resql))
	{
		$i = 0;
		while ($i < $num)
		{
			$obj = $db->fetch_object($resql);
	    //obtenemos el id del proceso fk_contrat
			$fkcontrat = $obj->fk_contrat;
			$objppc = new Poaprocesscontrat($db);
			$objppc->fetch($fkcontrat);
			if ($objppc->id == $fkcontrat)
			{
				$objdeve->fetch($obj->id);
				if ($objdeve->id == $obj->id)
				{
					$objdeve->fk_contrato = $objppc->fk_contrat;
					$objdeve->update($user);
				}
			}
			$i++;
		}
	}
}
//$db->free($resql);

//actualizacion del poa_partida_pre_det
$sql = "";
$sql = "SELECT";
$sql.= " t.rowid as id,";

$sql.= " t.fk_poa_partida_pre,";
$sql.= " t.fk_product,";
$sql.= " t.fk_contrat,";
$sql.= " t.fk_contrato,";
$sql.= " t.fk_poa_partida_com,";
$sql.= " t.quant,";
$sql.= " t.amount_base,";
$sql.= " t.detail,";
$sql.= " t.quant_adj,";
$sql.= " t.amount,";
$sql.= " t.tms,";
$sql.= " t.statut";


$sql.= " FROM ".MAIN_DB_PREFIX."poa_partida_pre_det as t";
$sql.= " WHERE t.fk_contrato = 0 OR t.fk_contrato IS NULL";
$resql=$db->query($sql);
if ($resql)
{
	$num = $db->num_rows($resql);
	if ($db->num_rows($resql))
	{
		$i = 0;
		require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poapartidapredet.class.php';
		$objppd = new Poapartidapredet($db);
		while ($i < $num)
		{
			$obj = $db->fetch_object($resql);
	    //obtenemos el id del proceso fk_contrat
			$fkcontrat = $obj->fk_contrat;
			if ($fkcontrat>0)
			{
				$objppc = new Poaprocesscontrat($db);
				$objppc->fetch($fkcontrat);
				if ($objppc->id == $fkcontrat)
				{
					$objppd->fetch($obj->id);
					if ($objppd->id == $obj->id)
					{
						$objppd->fk_contrato = $objppc->fk_contrat;
						$objppd->update($user);
					}
				}
			}
			$i++;
		}
	}
}

$db->free($resql);

?>