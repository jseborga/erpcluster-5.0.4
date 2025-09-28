<?php
if ($filtrosearch['search_all'])
{
	$text = $obj->sigla.' '.$objppl->nro_activity.' '.$objppl->pseudonym.' '.$objppl->label.' '.$objppl->partida;
	$filtertext = STRPOS(STRTOUPPER($text),STRTOUPPER($filtrosearch['search_all']));
	//echo ' ||'.$filtertext.'|| ';
	if ($filtertext===false) $lViewprev = false;
	else
	{
		$lViewprev = true;
		$aActivity[$obj->id][$objppl->id]['search_all'] = $lViewprev;
	}
	if (!$lViewprev)
	{
		$text = $objppl->partida;
		$filtertext = STRPOS(STRTOUPPER($text),STRTOUPPER($filtrosearch['search_all']));
		//echo ' ||'.$filtertext.'|| ';
		if ($filtertext===false) $lViewprev = false;
		else
		{
			$lViewprev = true;
			$aActivity[$obj->id][$objppl->id]['search_partida'] = $lViewprev;
		}
	}
}
//filtro usuario
if ($filtrosearch['search_login'])
{
	//echo '<br>iduser '.
	//$idUser     = userid_active_poa($obj); //poa.lib.php
	if ($objppl->fk_user_create != $filtrosearch['search_login']) $lViewprev = false;
	else
	{
		$lViewprev = true;
		$aActivity[$obj->id][$objppl->id]['search_logint'] = $lViewprev;
	//echo '<hr>reslogin |'.$lViewprev.'|';
	}
}
//filtro priority
if ($filtrosearch['search_priority'] >= 0)
{
	if ($filtrosearch['search_priority'] == $objppl->priority)
	{
		$lViewprev = true;
		$aActivity[$obj->id][$objppl->id]['search_priority'] = $lViewprev;
	}
	else
		$lViewprev = false;

	//echo '<hr>'.$obj->id.' resprio |'.$lViewprev.'| '.$filtromenu['search_priority'].' == '.$objppl->priority;
}

?>