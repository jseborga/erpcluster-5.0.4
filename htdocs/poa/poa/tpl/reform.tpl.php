<?php
if ($filtromenu['f1']==True)
{
	if ($numCol[71])
	{
		if ($lVersion)
		{
		//buscamos que suma y que resta		
		// $nReform = $aOf[$obj->fk_structure][$obj->id][$obj->partida] - 
		//   $aTo[$obj->fk_structure][$obj->id][$obj->partida];
			$nReform = $aOf[$obj->fk_structure][$obj->id][$obj->partida];
			$reformtext = $aOfref[$obj->fk_structure][$obj->id][$obj->partida];
			$idReform = $aOfone[$obj->fk_structure][$obj->id][$obj->partida];
			$idReform+=0;
			$idTag1 = $obj->id;
			$idTag2 = $obj->id * 100000;
			
			print '<td '.$newClase.'">';
			if ($user->rights->poa->poa->mod || $user->admin)
			{
				print '<span id="'.$idTag1.'" style="visibility:hidden; display:none;">'.'<input id="'.$obj->id.'_am" type="number" name="reform['.$idReform.']['.$obj->fk_structure.']['.$obj->id.']['.$obj->partida.']" value="'.price2num($nReform).'" size="7">'.'<input id="'.$obj->id.'_ap" type="hidden" name="reformx" value="'.price2num($nReform).'">'.'</span>';
				
				print '<span  id="'.$idTag2.'" style="visibility:visible; display:block;" onclick="visual_one('.$idTag1.' , '.$idTag2.')">'.price(price2num($nReform,'MT')).'</span>';
			}
			else
				print '<span  id="'.$idTag2.'" style="visibility:visible; display:block;">'.price(price2num($nReform,'MT')).'</span>';
			
			print '</td>';
			$sumaRef1 += $nReform;
			
		//numero de reformulado
			print '<td '.$newClase.'">';
			
			print '<span id="'.$idTag1.'_'.'" style="visibility:hidden; display:none;">'.'<input type="text" name="reformtext" size="7" onblur="CambiarURLFrame('.$idReform.','.$obj->fk_structure.','.$obj->id.','.$obj->partida.','.$gestion.','.$obj->id.','.'this.value);" value="'.$reformtext.'">'.'</span>';
			
			print '<span id="'.$idTag2.'_'.'" style="visibility:visible; display:block;" onclick="visual_one('.$idTag1.' , '.$idTag2.')">'.(empty($reformtext)?'&nbsp;':$reformtext).'</span>';
			print '</td>';
		}
		else
		{
			print '<td '.$newClase.'">&nbsp;</td>';
			print '<td '.$newClase.'">&nbsp;</td>';
		}
	}
	
	if ($numCol[72])
	{
		$nReform = $aOf[$obj->fk_structure][$obj->id][$obj->partida];
		$aHtml[$i]['nTotalAppen'] = $nTotalAp+$nReform;		
		print '<td '.$newClase.'">'.price(price2num($nTotalAp+$nReform,'MT')).'</td>';
	}
	if ($numCol[73])
	{
	//total reformulado pendiente
		$nReform = $aOf[$obj->fk_structure][$obj->id][$obj->partida];
		print '<td '.$newClase.'">'.($nTotalAp>0?price(price2num($nReform/$nTotalAp*100,'MT')):'').'</td>';
	}
	$aHtml[$i]['reform'] = $nReform;	    
	$aHtml[$i]['reformtext'] = $reformtext;
}

?>