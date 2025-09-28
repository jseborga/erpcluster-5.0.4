<?php
$sql = "SELECT p.rowid, p.ref as product_ref, p.label as produit, p.fk_product_type as type,";
$sql.= " e.label as stock, e.rowid as entrepot_id,";
$sql.= " m.rowid as mid, m.value, m.datem, m.fk_user_author, m.label, m.fk_origin, m.origintype,m.datem,";
$sql.= " u.login";
$sql.= " FROM (".MAIN_DB_PREFIX."entrepot as e,";
$sql.= " ".MAIN_DB_PREFIX."product as p,";
$sql.= " ".MAIN_DB_PREFIX."stock_mouvement as m)";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."user as u ON m.fk_user_author = u.rowid";
$sql.= " WHERE m.fk_product = p.rowid";
$sql.= " AND m.fk_entrepot = e.rowid";
$sql.= " AND e.entity = ".$conf->entity;
if (empty($conf->global->STOCK_SUPPORTS_SERVICES)) $sql.= " AND p.fk_product_type = 0";
if ($id)
{
	$sql.= " AND e.rowid ='".$id."'";
}
if ($obj->rowid)
{
	$sql.= " AND p.rowid ='".$obj->rowid."'";
}

$sql.= " AND m.datem BETWEEN '".$db->idate($dateini)."' AND '".$db->idate($datefin)."'";

$resql = $db->query($sql);
$productidselected = $obj->rowid;
$arrayofuniqueproduct=array();
$arrayofuniqueproduct[$obj->rowid]=$obj->rowid;
$input = 0;
$output = 0;
if ($resql)
{
	$j = 0;
	$nume = $db->num_rows($resql);
	$var=True;
	while ($j < $nume)
	{
		$objp = $db->fetch_object($resql);
		if(!empty($objp->fk_origin))
		{
			$origin = $movement->get_origin($objp->fk_origin, $objp->origintype);
		}
		else
		{
			$origin = '';
		}

		if ($objp->value > 0)
			$input+=$objp->value;
		if ($objp->value <=0)
			$output+= $objp->value;
		$j++;
	}
}
if (count($arrayofuniqueproduct) == 1)
{
	// $productidselected=0;
	// foreach ($arrayofuniqueproduct as $key => $val)
	//   {
	// 	$productidselected=$key;
	// 	$productlabelselected=$val;
	//   }
	$year = 2015;
	$month = 4;
	$datebefore=dol_get_first_day($year?$year:strftime("%Y",time()), $month?$month:1, true);
	$datebefore=$dateini;
	$dateafter=dol_get_last_day($year?$year:strftime("%Y",time()), $month?$month:12, true);
	$dateafter=$datefin;

	$balancebefore=$objinv->calculateBalanceForProductEntrepotBefore($id,$productidselected, $datebefore);
	$balanceafter=$objinv->calculateBalanceForProductEntrepotBefore($id,$productidselected, $dateafter);
}
