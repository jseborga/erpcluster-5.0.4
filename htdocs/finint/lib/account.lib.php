<?php

  //lista las cuentas de banco
function account_list($courant=2,$clos=0)
{
	global $langs,$conf,$db;
	$sql = "SELECT p.rowid, p.ref, p.label ";
	$sql.= " FROM ".MAIN_DB_PREFIX."bank_account as p ";
	$sql.= " WHERE p.entity = ".$conf->entity;
	$sql.= " AND p.courant = ".$courant;
  if ($clos < 2)//todos
  $sql.= " AND p.clos = ".$clos;
  $resql=$db->query($sql);
  $aArray = array();
  if ($resql)
  {
  	$num = $db->num_rows($resql);
  	$i = 0;
  	while ($i < $num)
  	{
  		$row = $db->fetch_object($resql);
  		$aArray[$row->rowid] = $row;
  		$i++;
  	}
  	return $aArray;
  }
  else
  {
  	dol_print_error($db);
  }
  return $aArray;
}

  //muestra los saldos por cada cuenta y usuario
function saldoAccount($id,$userid=0)
{
	global $langs,$conf,$db;
	if (!empty($userid)){
		$filtro = " AND b.fk_user_author = '$userid'";
	}
  // Ce rapport de tresorerie est base sur llx_bank (car doit inclure les transactions sans facture)
  // plutot que sur llx_paiement + llx_paiementfourn

	$sql = "SELECT SUM(b.amount)";
	$sql.= ", date_format(b.dateo,'%Y-%m') as dm";
	$sql.= " FROM ".MAIN_DB_PREFIX."bank as b";
	$sql.= ", ".MAIN_DB_PREFIX."bank_account as ba";
	$sql.= " WHERE b.fk_account = ba.rowid";
	$sql.= " AND ba.entity = ".$conf->entity;
	$sql.= " AND b.amount >= 0";
	$sql.= $filtro;
	if (! empty($id))
		$sql .= " AND b.fk_account IN (".$db->escape($id).")";
	$sql.= " GROUP BY dm";
	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		while ($i < $num)
		{
			$row = $db->fetch_row($resql);
			$encaiss[$row[1]] = $row[0];
			$i++;
		}
	}
	else
	{
		dol_print_error($db);
	}
	$sql = "SELECT SUM(b.amount)";
	$sql.= ", date_format(b.dateo,'%Y-%m') as dm";
	$sql.= " FROM ".MAIN_DB_PREFIX."bank as b";
	$sql.= ", ".MAIN_DB_PREFIX."bank_account as ba";
	$sql.= " WHERE b.fk_account = ba.rowid";
	$sql.= " AND ba.entity = ".$conf->entity;
	$sql.= $filtro;
	$sql.= " AND b.amount <= 0";
	if (! empty($id))
		$sql .= " AND b.fk_account IN (".$db->escape($id).")";
	$sql.= " GROUP BY dm";

	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		while ($i < $num)
		{
			$row = $db->fetch_row($resql);
			$decaiss[$row[1]] = -$row[0];
			$i++;
		}
	}
	else
	{
		dol_print_error($db);
	}

	$saldo = 0;
	foreach ((array) $encaiss AS $data1 => $valor)
	{
		$saldo += $valor;
	}
	foreach ((array) $decaiss AS $data1 => $valor)
	{
		$saldo -= $valor;
	}
	return $saldo;
}

function saldoreq(&$object)
{
	global $langs,$db;
		//gastos
	require_once DOL_DOCUMENT_ROOT.'/finint/class/requestcashdeplacementext.class.php';
	$deplacement = new Requestcashdeplacementext($db);

	$filter = " AND k.fk_account = ".$object->fk_account;
		//$filter.= " AND t.fk_projet = ".$object->fk_projet;
	$filter.= " AND r.fk_finint_cash = ".$object->id;
	$filterstatic.= " AND t.entity = ".$object->entity;
	$filterstatic.= " AND t.concept = 'deplacement'";
	$filterstatic.= " AND t.fk_request_cash = ".$object->id;
	$deplacement->fetchAll('ASC','ref',0,0,array(1=>1),'AND',$filterstatic,false);
	$sumadep = 0;
	$sumaappdep = 0;
	$sumanoappdep = 0;
	foreach ((array) $deplacement->lines AS $j => $line)
	{
		$sumadep -= $line->amount;
		if ($line->status == 2)
		{
			$sumaappdep-= $line->amount;
		}
		elseif($line->status == 1)
			$sumanoappdep-=$line->amount;
	}
	$objdeplac = new Requestcashdeplacementext($db);
	$objdeplac->getlisttransfer(0,$object->id);
	$sumapar  = 0;
	$sumapar0 = 0;
	$sumaparcierre = 0;
	$lCierre = false;
	foreach ((array) $objdeplac->lines AS $j => $line)
	{
		if ($line->status == 1)
		{
			//$sumapar += $line->amount*-1;
			$sumapar += $line->amount;
		}
		if ($line->status == 0)
		{
			$sumapar0 += $line->amount*-1;
		}
		if ($line->status == 4)
		{
			$lCierre = true;
			$sumaparcierre += $line->amount*-1;
		}
	}
	$sumexpense = $sumadep;
	$array = array('sumadep'=>$sumadep,'sumaappdep'=>$sumaappdep,'sumanoappdep'=>$sumanoappdep,'sumapar'=>$sumapar,'sumapar0'=>$sumapar0,'sumaparcierre'=>$sumaparcierre,'lCierre'=>$lCierre);
	return $array;
}
?>