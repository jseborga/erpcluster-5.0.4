<?php
  //filtro principal poa
$lContinue = false;
if ($filtrosearch['search_all'])
{
	$text = $obj->sigla.' '.$obj->pseudonym.' '.$obj->label.' '.$obj->partida;
	$filtertext = STRPOS(STRTOUPPER($text),STRTOUPPER($filtrosearch['search_all']));
	if ($filtertext===false)
		$lContinue = false;
	else
		$lContinue = true;

	$aPoa[$obj->id]['search_all'] = $lContinue;
	//revisamos si se encuentra la partida
	if (!$lContinue)
	{
		$text = $obj->partida;
		$filtertext = STRPOS(STRTOUPPER($text),STRTOUPPER($filtrosearch['search_all']));
		if ($filtertext===false)
			$lContinue = false;
		else
			$lContinue = true;
		$aPoa[$obj->id]['search_partida'] = $lContinue;
	}
	//$lContinue.'|';
}

//verifica usuario activo
//$newNombre = user_active_poa($obj); //poa.lib.php
if ($filtrosearch['search_login'])
{
	$idUser     = userid_active_poa($obj); //poa.lib.php
	if ($idUser != $filtrosearch['search_login'])
		$lContinue = false;
	else
		$lContinue = true;
	$aPoa[$obj->id]['search_login'] = $lContinue;
}
?>