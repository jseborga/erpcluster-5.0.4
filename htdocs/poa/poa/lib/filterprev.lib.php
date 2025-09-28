<?php
  //filtro secundario
if ($search_sigla)
{
	$filters = STRPOS(STRTOUPPER($objppl->nro_activity),STRTOUPPER($search_sigla));
	if ($filters===false) $lViewprev = false;
	else $lViewprev = true;
	$aActivity[$obj->id][$objppl->id]['search_sigla']=$lViewprev;
}
if ($search_label)
{
	$filters = STRPOS(STRTOUPPER($objppl->label),STRTOUPPER($search_label));
	if ($filters===false) $lViewprev = false;
	else $lViewprev = true;      
	$aActivity[$obj->id][$objppl->id]['search_label']=$lViewprev;
}
if ($search_pseudonym)
{
	$filters = STRPOS(STRTOUPPER($objppl->pseudonym),STRTOUPPER($search_pseudonym));
	if ($filters===false) $lViewprev = false;
	else $lViewprev = true;
	$aActivity[$obj->id][$objppl->id]['search_pseudonym']=$lViewprev;
}
if ($search_partida)
{
	$filters = STRPOS(STRTOUPPER($objppl->partida),STRTOUPPER($search_partida));
	if ($filters===false) $lViewprev = false;
	else $lViewprev = true;
	$aActivity[$obj->id][$objppl->id]['search_partida']=$lViewprev;
}
//search_user
if (!empty($search_user))
{
	$filteruser = false;
	if ($objppl->fk_user_create &&
		($objuser->fetch($objppl->fk_user_create) > 0))
	{
		$newNombre_ = $objuser->login;
		$nombre_ = $objuser->firstname;
	//echo ' |'.
		$nombreslog_ = $objuser->firstname.' '.$objuser->lastname.' '.$objuser->login;
		if (!empty($search_user)) $filteruser = STRPOS(STRTOUPPER($nombreslog_),STRTOUPPER($search_user));
	}		
	if ($filteruser===false) $lViewprev = false;
	else $lViewprev = true;
	$aActivity[$obj->id][$objppl->id]['search_user']=$lViewprev;
}
if ($filtrosearch['search_all'])
{
	//echo ' en: '.$text = $obj->sigla.' '.$objppl->nro_activity.' '.$objppl->pseudonym.' '.$objppl->label.' '.$objppl->partida;
	$filtertext = STRPOS(STRTOUPPER($text),STRTOUPPER($filtrosearch['search_all']));
	//echo ' ||'.$filtertext.'|| ';
	if ($filtertext===false) $lViewprev = false;
	else 
	{
		$lViewprev = true;
		$aActivityf[$obj->id][$objppl->id]['search_all'] = $lViewprev;
	}
}

// if ($filtrosearch['search_priority'] && $filtrosearch['search->priority'] >= 0)
//   {
//     if ($filtrosearch['search_priority'] == $objppl->priority)
//       $lContinue = true;
//     else
//       $lContinue = false;
//     $aActivity[$obj->id]['search_priority'] = $lContinue;
//   }

?>