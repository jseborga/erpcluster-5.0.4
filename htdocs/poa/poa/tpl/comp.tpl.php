<?php
  //comprometido
$totalc = 0;
$aHtml[$i]['comprometido'] = 0;

if ($filtromenu['f1'] == True)
{
	if ($objcomp->getsum_str_part($obj->gestion,$obj->fk_structure,$obj->id, $obj->partida))
	{
		$aHtml[$i]['comprometido'] = $objcomp->total;
		if($numCol[11])
		{
		   	$totalc = $objcomp->total;

		   	$newClaseor = $newClase;
		   	if ($totalc > 0.00) $nFondo = porcGrafico($aVal,$totalc);
			if ($nFondo)
			{
				if ($lStyle)
		   		{
					if ($_SESSION['colorUser'] == true)
						$newClase = 'class="left '.'imgval'.$nFondo.'" style="background-color:'.$aColorUser[$idUser].';';
					if ($_SESSION['colorPartida'] == true)
						$newClase = 'class="left '.'imgval'.$nFondo.'" style="background-color:'.$aColorUser[$obj->partida].';';
		  		}
		  		else
					$newClase.= ' '.'imgval'.$nFondo;
	  		}
	  		$aHtml[$i]['comprometido'] = $objcomp->total;
	  		if ($filtromenu['f1'])
				print '<td align="right" '.$newClasen.'">'.($objcomp->total?price(price2num($objcomp->total,'MT')):price(0)).'</td>';
	 		$newClase = $newClaseor;

 		}
 		if($numCol[12])
 		{
			//porcent compro
   			if ($nTotalAp > 0)
	 			$totalc = $objcomp->total / $nTotalAp * 100;
 			else
	 			$totalc = 0;

 			$nFondo = 0;
 			$newClaseor = $newClase;
			if ($totalc >=0)
	 			$nFondo = porcGrafico($aBalance,$totalc);
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
			if ($filtromenu['f1'])
 				print '<td align="right" '.$newClasen.'">'.price(price2num($totalc,'MT')).' %'.'</td>';
			$newClase = $newClaseor;
		}

		$balancec = $nTotalAp - $objcomp->total;
		$newClaseor = $newClase;
		if ($balancec < 0)
  			if ($lStyle)
   				$newClase.= ' color:#ff0000;';
			else
				$newClase.= '" style="color:#ff0000;';
		if ($numCol[16])
  			if ($filtromenu['f1'])
   				print '<td align="right" '.$newClasen.'">'.price(price2num($balancec,'MT')).'</td>';
	}
	else
	{
   		$totalc = 0;
   		if ($filtromenu['f1'])
	 		print '<td align="right" '.$newClasen.'">'.price(0).'</td>';
	}
}

?>