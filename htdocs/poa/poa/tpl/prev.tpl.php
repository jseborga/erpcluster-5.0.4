<?php
$total = 0;
$aHtml[$i]['preventivo'] = 0;

if ($filtromenu['f1'] == True)
{
	if ($objprev->getsum_str_part($obj->gestion,$obj->fk_structure,$obj->id, $obj->partida))
	{
		$aHtml[$i]['preventivo'] = $objprev->total;
		if($numCol[9])
		{
			$newClaseor = $newClase;
			$nFondo = 0;
			$total = $objprev->total;
			if ($total >0)
				$nFondo = porcGrafico($aVal,$total);
			if ($nFondo)
			{
				if ($lStyle)
				{
					if ($_SESSION['colorUser'] == true)
					{
						$newClase = 'class="left '.'imgval'.$nFondo.'" style="background-color:'.$aColorUser[$idUser].';';
					}
					if ($_SESSION['colorPartida'] == true)
					{
						$newClase = 'class="left '.'imgval'.$nFondo.'" style="background-color:'.$aColorUser[$obj->partida].';';
					}

				}
				else
				{
					$newClase.= ' '.'imgval'.$nFondo;
				}
			}

		//$aHtml[$i]['preventivo'] = $objprev->total;

			print '<td align="right" '.$newClasen.'">';
			print '<a href="'.DOL_URL_ROOT.'/poa/execution/liste.php'.'?idp='.$obj->id.'&dol_hide_leftmenu=1">';
			print price(price2num($objprev->total,'MT'));
			print '</a>';
			print '</td>';
			$newClase = $newClaseor;			
		}
		if($numCol[10])
		{

			if ($nTotalAp > 0)
				$total = $objprev->total / $nTotalAp * 100;
			else
				$total = 0;

			$nFondo = 0;
			$newClaseor = $newClase;

			if ($total >=0)
				$nFondo = porcGrafico($aBalance,$total);
			if ($nFondo)
			{

				if ($lStyle)
				{
					if ($_SESSION['colorUser'] == true)
						$newClase = 'class="left '.'imgfondo'.$nFondo.'" style="background-color:'.$aColorUser[$idUser].';';
					if ($_SESSION['colorPartida'] == true)
						$newClase = 'class="left '.'imgfondo'.$nFondo.'" style="background-color:'.$aColorUser[$obj->partida].';';
				}
				else
					$newClase.= ' '.'imgfondo'.$nFondo;
			}

			print '<td align="right" '.$newClasen.'">';
			print price(price2num($total,'MT')).' %';
			print '</td>';
			$newClase = $newClaseor;
		}
		$balance = $nTotalAp - $objprev->total;
		$newClaseor = $newClase;
		$nFondo = '';
		if ($balance < 0)
		{
			if ($lStyle)
				$newClase.= ' color:#ff0000;';
			else
				$newClase.= '" style="color:#ff0000;';
		}
		if ($numCol[15])
		{
			print '<td align="right" '.$newClasen.'">';
			print price(price2num($balance,'MT'));
			print '</td>';
		}
		$newClase = $newClaseor;
		$total = $objprev->total;
	}
	else
	{
		$listaPrev = '';
		$total = 0;
		print '<td align="right" '.$newClasen.'">x'.price(0).'</td>';
	}    
}

?>