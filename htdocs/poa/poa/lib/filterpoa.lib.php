<?php
  //filtro secundario

  //filtro secundario
if ($search_sigla)
{
	$filters = STRPOS(STRTOUPPER($obj->sigla),STRTOUPPER($search_sigla));
	if ($filters===false)
		$lContinue = false;
	else
		$lContinue = true;
	if (!$lContinue && $filtrosearch['search_all'])
	{
		$filters = STRPOS(STRTOUPPER($obj->sigla),STRTOUPPER($filtrosearch['search_all']));
		if ($filters===false)
			$lContinue = false;
		else
			$lContinue = true;
	}
	$aPoa[$obj->id]['search_sigla'] = $lContinue;
}
if ($search_label)
{
	$filters = STRPOS(STRTOUPPER($obj->label),STRTOUPPER($search_label));
	if ($filters===false)
		$lContinue = false;
	else
		$lContinue = true;
	 if (!$lContinue && $filtrosearch['search_all'])
	   {
	 	$filters = STRPOS(STRTOUPPER($obj->label),STRTOUPPER($filtrosearch['search_all']));
	 	if ($filters===false)
	 	  $lContinue = false;
	 	else
	 	  $lContinue = true;
	   }
	$aPoa[$obj->id]['search_label'] = $lContinue;
}
if ($search_pseudonym)
{
	$filters = STRPOS(STRTOUPPER($obj->pseudonym),STRTOUPPER($search_pseudonym));
	if ($filters===false)
		$lContinue = false;
	else
		$lContinue = true;
	 if (!$lContinue && $filtrosearch['search_all'])
	   {
	 	$filters = STRPOS(STRTOUPPER($obj->pseudonym),STRTOUPPER($filtrosearch['search_all']));
	 	if ($filters===false)
	 	  $lContinue = false;
	 	else
	 	  $lContinue = true;
	   }
	$aPoa[$obj->id]['search_pseudonym'] = $lContinue;
}
if ($search_partida)
{
	$filters = STRPOS(STRTOUPPER($obj->partida),STRTOUPPER($search_partida));
	if ($filters===false)
		$lContinue = false;
	else
		$lContinue = true;
	 if (!$lContinue && $filtrosearch['search_all'])
	   {
	 	$filters = STRPOS(STRTOUPPER($obj->partida),STRTOUPPER($filtrosearch['search_all']));
	 	if ($filters===false)
	 	  $lContinue = false;
	 	else
	 	  $lContinue = true;
	   }
	$aPoa[$obj->id]['search_partida'] = $lContinue;
}
//search_user
if (!empty($search_user))
{
	$filteruser = '';
	$idUser = userid_active_poa($obj); //poa.lib.php
	if ($idUser && $objuser->fetch($idUser) > 0)
	{
		$newNombre = $objuser->login;
		$nombre = $objuser->firstname;
		$nombreslog = $objuser->firstname.' '.$objuser->lastname.' '.$objuser->login;
		$filteruser = STRPOS(STRTOUPPER($nombreslog),STRTOUPPER($search_user));
	}
	if ($filteruser===false)
		$lContinue = false;
	else
		$lContinue = true;
	$aPoa[$obj->id]['search_user'] = $lContinue;
}

?>