<?php
if ($filtrosearch['search_all'])
{
	$objsoc->fetch($objcon->socid);
	$text = $objcon->ref.' '.$objcon->array_options['options_ref_contrato'];
	if ($objsoc->id == $objcon->socid)
	{
		$text.= ' '.$objsoc->nom;
	}
	//$text = $obj->sigla.' '.$objppl->nro_activity.' '.$objppl->pseudonym.' '.$objppl->label.' '.$objppl->partida;
	$filtertext = STRPOS(STRTOUPPER($text),STRTOUPPER($filtrosearch['search_all']));
	//echo ' ||'.$filtertext.'|| ';
	if ($filtertext===false)
		$lViewprev = false;
	else
	{
		$lViewprev = true;
	}
	$aActivity[$obj->id][$objppl->id]['search_all'] = $lViewprev;
}

?>