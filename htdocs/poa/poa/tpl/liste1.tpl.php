<?php
if ($filtromenu['f1']==true)
{
	//para exportacion a excel
	$aHtml[$i]['filameta'] = 1;
	$aHtml[$i]['sigla'] = $obj->sigla;
	$aHtml[$i]['label'] = $obj->label;
	$aHtml[$i]['pseudonym'] = $obj->pseudonym;
	$aHtml[$i]['partida'] = $obj->partida;

	//liste 1
	//print '<div class="height36 ractivity">';//inicio de la fila
	//Estado
	////rqc print '<td '.$newClase.'">&nbsp;</td>';
	//structure
	$sigla = $obj->sigla;
	print '<td '.$newClase.'">';
	if ($user->admin || $user->rights->poa->poa->mod)
		print '<button class="btn btn-info" title="'.$obj->labelstructure.'" href="#fichepoa'.$obj->id.'" role="button" data-toggle="modal">'.$obj->sigla.'</button>';
	else
		print $obj->sigla;
//	print '<a href="#fichepoa'.$obj->id.'" title="'.$obj->labelstructure.'">'.$obj->sigla.'</a>';
	print '</td>';
	//label
	if ($numCol[1])
	{
		print '<td '.$newClase.'">';
		print '<a href="#" title="'.STRTOUPPER($obj->pseudonym).'">';
		print (strlen(trim($obj->label))>50?substr(STRTOUPPER($obj->label),0,48).'..':STRTOUPPER($obj->label));
		print '</a>';
		print '</td>';
	}
	//pseudonym
	if ($numCol[2])
	{
		print '<td '.$newClase.'">';

		$idTagps = $obj->id+100000;
		$idTagps2 = $idTagps+100500;
		if ($user->rights->poa->poa->mod || $user->admin)
		{
			print '<span id="'.$idTagps.'" style="visibility:hidden; display:none;">'.'<input id="'.$idTagps.'_poaa" type="text" name="pseudonym" value="'.$obj->pseudonym.'" onblur="CambiarURLFrametwo('.$obj->id.','.$idTagps.','.'this.value);" size="36">'.'</span>';

			print '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;" onclick="visual_tree('.$idTagps.' , '.$idTagps2.')">';
			print (strlen(trim($obj->pseudonym))>60?'<a href="#" title="'.$obj->label.'">'.substr(trim($obj->pseudonym),0,60).'..xx.</a>':'<a href="#" title="'.$obj->label.'">'.trim($obj->pseudonym).'</a>');
			print '</span>';
		}
		else
		{
			print '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;">';
			print (strlen(trim($obj->pseudonym))>60?'<a href="#" title="'.$obj->label.'">'.substr(trim($obj->pseudonym),0,60).'..xx.</a>':'<a href="#" title="'.$obj->label.'">'.trim($obj->pseudonym).'</a>');
			print '</span>';
		}

		print '</td>';
	}
	//partida
	print '<td '.$newClase.'">'.'<a href="#" title="'.$aPartida[$obj->partida].'">'.$obj->partida.'</a>'.'</td>';
}

?>