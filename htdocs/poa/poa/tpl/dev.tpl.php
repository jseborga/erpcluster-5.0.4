<?php
$totald = 0;
$aHtml[$i]['devengado'] = 0;
if ($filtromenu['f1'] == True)
 {
    if ($objdeve->getsum_str_part($obj->gestion,$obj->fk_structure,$obj->id,
				  $obj->partida))
    {
		$aHtml[$i]['devengado'] = $objdeve->total;
		if($numCol[13])
	  	{
	    	$totald = $objdeve->total;

	    	$newClaseor = $newClase;
	    	if ($totald >=0)
	      		$nFondo = porcGrafico($aVal,$totald);
	    	if ($nFondo)
	      	{

				if ($lStyle)
		  		{
		    		if ($_SESSION['colorUser'] == true)
		      		{
					//$newClase = 'class="left '.'imgval'.$nFondo.'" style="background-color:'.$aColorUser[$newNombre].';';
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
	    	$aHtml[$i]['devengado'] = $objdeve->total;

	    	if ($filtromenu['f1'])
	      		print '<td align="right" '.$newClasen.'">'.price(price2num($objdeve->total,'MT')).'</td>';
	    	$newClase = $newClaseor;
	  	}
		if($numCol[14])
	  	{

	    	if ($nTotalAp > 0)
	      		$totald = $objdeve->total / $nTotalAp * 100;
	    	else
	      		$totald = 0;

		    $nFondo = 0;
		    $newClaseor = $newClase;

	    	if ($totald >0)
	      		$nFondo = porcGrafico($aBalance,$totald);
	    	if ($nFondo)
	      	{

				if ($lStyle)
		  		{
		    		if ($_SESSION['colorUser'] == true)
		      		{
						// $newClase = 'class="left '.'imgfondo'.$nFondo.'" style="background-color:'.$aColorUser[$newNombre].';';
						$newClase = 'class="left '.'imgfondo'.$nFondo.'" style="background-color:'.$aColorUser[$idUser].';';
		      		}
		    		if ($_SESSION['colorPartida'] == true)
		      		{
						$newClase = 'class="left '.'imgfondo'.$nFondo.'" style="background-color:'.$aColorUser[$obj->partida].';';
		      		}
		  		}
				else
		  		{
		    		$newClase.= ' '.'imgfondo'.$nFondo;
		  		}
	      	}

	    	if ($filtromenu['f1'])
	      		print '<td align="right" '.$newClasen.'">'.($totald?price(price2num($totald,'MT')):price(0)).' %'.'</td>';
	    	$newClase = $newClaseor;
	  	}
		$balanced = $nTotalAp - $objdeve->total;
		$newClaseor = $newClase;
		if ($balanced < 0)
	  	{
	    	if ($lStyle)
	      		$newClase.= ' color:#ff0000;';
	    	else
	      		$newClase.= '" style="color:#ff0000;';
	  	}
		if ($numCol[17])
	  		if ($filtromenu['f1'])
	    		print '<td align="right" '.$newClasen.'">'.price(price2num($balanced,'MT')).'</td>';
		$newClase = $newClaseor;
    }
    else
    {
		$totald = 0;
		if ($filtromenu['f1'])
	  		print '<td align="right" '.$newClasen.'">'.price($totald).'</td>';
    }
}

?>
