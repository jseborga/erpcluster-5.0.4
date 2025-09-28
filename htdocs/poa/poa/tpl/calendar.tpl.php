<?php

if ($filtromenu['f1'] == True)
  {
	
    if ($opver == 1)// && $numCol[73]  RQC QUITADO
    {
		$newClaseor = $newClase;
		$iGrafico = tipo_grafico($aLimite,$obj->m_jan);
		$newClaseor__ = $newClase__;
		if ($obj->p_jan) 
			$newClaseor__ .= $backg.' ';
		else 
			$newClaseor__ .= $backg.' ';
		print '<td '.$newClaseor__.'">';
	// print '<div '.($obj->m_jan?'class="left '.$backg.' '.($filtromenu['a1']?'product':'').'"':'class="left"').' style="height:12px;width:39px;">';
	// print (!empty($obj->m_jan)?'<a href="#">'.img_picto(price($obj->m_jan),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'');
	// print '</div>';
		print '<div class="clear"></div>';
      
		$aGraphicnew = $aResumpoaplan[1];//jan
		if (count($aGraphicnew)>0)
	  	{
	    	foreach ((array) $aGraphicnew AS $cpa => $val)
	      	{
				$title = '';
				print '<div '.(($aResumpoacolor[1][$cpa])?$aResumpoacolor[1][$cpa]:'id="amountplan"').'">&nbsp;';
				print '</div>';
	      	}
	  	}
		else
	  		print '<div id="amountplan">&nbsp;</div>';
	
		print '<div class="clear"></div>';
		$aGraphicnew = $aResumpoaejec[1];//jan
		if (count($aGraphicnew)>0)
	  	{
	    	foreach ((array) $aGraphicnew AS $cpa => $val)
	      	{
				$title = '';
				if ($cpa == 'AUT_PAYMENT')
		  		{
		    		print '<div style="float:left">'.img_picto($langs->trans('Viewpayment'),DOL_URL_ROOT.'/poa/img/pay','',1).'</div>';
		  		}
				else
		  		{
		    		print '<div '.(($aResumpoacolor[1][$cpa])?$aResumpoacolor[1][$cpa]:'id="amountplan"').'">&nbsp;'.'</div>';
		  		}
	      	}
	  	}
    
		print '</td>';

		$newClase = $newClaseor;
		$iGrafico = tipo_grafico($aLimite,$obj->m_feb);
		$newClaseor__ = $newClase__;
		if ($obj->p_feb)
	  		$newClaseor__ .= ' ';
		else
	  		$newClaseor__ .= $backg.' ';
		print '<td '.$newClaseor__.'">';
	// print '<div '.($obj->m_feb?'class="left '.($filtromenu['a1']?'product':'').'"':'class="left"').' style="height:12px;width:39px;">';
	// print (!empty($obj->m_feb)?'<a href="#">'.img_picto(price($obj->m_feb),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'');
	// print '</div>';
		print '<div class="clear"></div>';

		$d = 2;
		$aGraphicnew = $aResumpoaplan[$d];//jan
		if (count($aGraphicnew)>0)
	  	{
	    	foreach ((array) $aGraphicnew AS $cpa => $val)
	      	{
				$title = '';
				print '<div '.(($aResumpoacolor[$d][$cpa])?$aResumpoacolor[$d][$cpa]:'id="amountplan"').'">&nbsp;';
				print '</div>';
	      	}
	  	}
		else
	  		print '<div id="amountplan">&nbsp;</div>';
		print '<div class="clear"></div>';
		$aGraphicnew = $aResumpoaejec[$d];//jan
		if (count($aGraphicnew)>0)
	  	{
	    	foreach ((array) $aGraphicnew AS $cpa => $val)
	      	{
				$title = '';
				if ($cpa == 'AUT_PAYMENT')
		  			print '<div style="float:left">'.img_picto($langs->trans('Viewpayment'),DOL_URL_ROOT.'/poa/img/pay','',1);
				else				  
		  			print '<div '.(($aResumpoacolor[$d][$cpa])?$aResumpoacolor[$d][$cpa]:'id="amountplan"').'">&nbsp;';
		    	print '</div>';
	      	}
	  	}
		print '</td>';
		$newClase = $newClaseor;
		$iGrafico = tipo_grafico($aLimite,$obj->m_mar);
		$newClaseor__ = $newClase__;
		if ($obj->p_mar)
	  		$newClaseor__ .= 'trimestre';
		else
	  		$newClaseor__ .= $backg.' trimestre';
		print '<td '.$newClaseor__.'">';
	// print '<div '.($obj->m_mar?'class="left '.$backg.' '.($filtromenu['a1']?'product':'').'"':'class="left"').' style="height:12px;width:39px;">';
	// print (!empty($obj->m_mar)?'<a href="#">'.img_picto(price($obj->m_mar),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'');
	// print '</div>';
		print '<div class="clear"></div>';
	
		$d = 3;
		$aGraphicnew = $aResumpoaplan[$d];//jan
		if (count($aGraphicnew)>0)
	  	{
	    	foreach ((array) $aGraphicnew AS $cpa => $val)
	      	{
				$title = '';
				print '<div '.(($aResumpoacolor[$d][$cpa])?$aResumpoacolor[$d][$cpa]:'id="amountplan"').'>&nbsp;';
				print '</div>';
	      	}
	  	}
		else
	  		print '<div id="amountplan">&nbsp;</div>';
		print '<div class="clear"></div>';
		$aGraphicnew = $aResumpoaejec[$d];//jan
		if (count($aGraphicnew)>0)
	  	{
	    	foreach ((array) $aGraphicnew AS $cpa => $val)
	      	{
				$title = '';
				if ($cpa == 'AUT_PAYMENT')
		  			print '<div style="float:left">'.img_picto($langs->trans('Viewpayment'),DOL_URL_ROOT.'/poa/img/pay','',1);
				else
		  			print '<div '.(($aResumpoacolor[$d][$cpa])?$aResumpoacolor[$d][$cpa]:'id="amountplan"').'>&nbsp;';
				print '</div>';
	      	}
	  	}
    
		print '</td>';
		$newClase = $newClaseor;
		$iGrafico = tipo_grafico($aLimite,$obj->m_apr);
		$newClaseor__ = $newClase__;
		if ($obj->p_apr)
	  		$newClaseor__ .= '';
		else
	  		$newClaseor__ .= $backg.' ';
	
		print '<td '.$newClaseor__.'">';
	// print '<div '.($obj->m_apr?'class="left '.$backg.' '.($filtromenu['a1']?'product':'').'"':'class="left"').' style="height:12px;width:39px;">';
	// print (!empty($obj->m_apr)?'<a href="#">'.img_picto(price($obj->m_apr),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'');
	// print '</div>';
		print '<div class="clear"></div>';

		$d = 4;
		$aGraphicnew = $aResumpoaplan[$d];//jan
		if (count($aGraphicnew)>0)
	  	{
	    	foreach ((array) $aGraphicnew AS $cpa => $val)
	      	{
				$title = '';
				print '<div '.(($aResumpoacolor[$d][$cpa])?$aResumpoacolor[$d][$cpa]:'id="amountplan"').'>&nbsp;';
				print '</div>';
	      	}
	  	}
		else
	  		print '<div id="amountplan">&nbsp;</div>';
		print '<div class="clear"></div>';
		$aGraphicnew = $aResumpoaejec[$d];//jan
		if (count($aGraphicnew)>0)
	  	{
	    	foreach ((array) $aGraphicnew AS $cpa => $val)
	      	{
				$title = '';
				if ($cpa == 'AUT_PAYMENT')
		  			print '<div style="float:left">'.img_picto($langs->trans('Viewpayment'),DOL_URL_ROOT.'/poa/img/pay','',1);
				else
		  			print '<div '.(($aResumpoacolor[$d][$cpa])?$aResumpoacolor[$d][$cpa]:'id="amountplan"').'>&nbsp;';
				print '</div>';
	      	}
	  	}
		print '</td>';
		$newClase = $newClaseor;
		$iGrafico = tipo_grafico($aLimite,$obj->m_may);
		$newClaseor__ = $newClase__;
		if ($obj->p_may)
	  		$newClaseor__ .= '';
		else
	  		$newClaseor__ .= $backg.' ';
		print '<td '.$newClaseor__.'">';
	// print '<div '.($obj->m_may?'class="left '.$backg.' '.($filtromenu['a1']?'product':'').'"':'class="left"').' style="height:12px;width:39px;">';
	// print (!empty($obj->m_may)?'<a href="#">'.img_picto(price($obj->m_may),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'');
	// print '</div>';
		print '<div class="clear"></div>';
		$d = 5;
		$aGraphicnew = $aResumpoaplan[$d];//jan
		if (count($aGraphicnew)>0)
	  	{
	    	foreach ((array) $aGraphicnew AS $cpa => $val)
	      	{
				$title = '';
				print '<div '.(($aResumpoacolor[$d][$cpa])?$aResumpoacolor[$d][$cpa]:'id="amountplan"').'>&nbsp;';
				print '</div>';
	      	}
	  	}
		else
	  		print '<div id="amountplan">&nbsp;</div>';
		print '<div class="clear"></div>';
		$aGraphicnew = $aResumpoaejec[$d];//jan
		if (count($aGraphicnew)>0)
	  	{
	    	foreach ((array) $aGraphicnew AS $cpa => $val)
	      	{
				$title = '';
				if ($cpa == 'AUT_PAYMENT')
		  			print '<div style="float:left">'.img_picto($langs->trans('Viewpayment'),DOL_URL_ROOT.'/poa/img/pay','',1);
				else
		  			print '<div '.(($aResumpoacolor[$d][$cpa])?$aResumpoacolor[$d][$cpa]:'id="amountplan"').'>&nbsp;';
				print '</div>';
	      	}
	  	}
	
		print '</td>';
		$newClase = $newClaseor;
		$iGrafico = tipo_grafico($aLimite,$obj->m_jun);
		$newClaseor__ = $newClase__;
		if ($obj->p_jun)
	  		$newClaseor__ .= $backg.' trimestre';
		else
	  		$newClaseor__ .= $backg.' trimestre';
		print '<td '.$newClaseor__.'">';
	// print '<div '.($obj->m_jun?'class="left '.$backg.' '.($filtromenu['a1']?'product':'').'"':'class="left"').' style="height:12px;width:39px;">';
	// print (!empty($obj->m_jun)?'<a href="#">'.img_picto(price($obj->m_jun),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'');
	// print '</div>';
		print '<div class="clear"></div>';
		$d = 6;
		$aGraphicnew = $aResumpoaplan[$d];//jan
		if (count($aGraphicnew)>0)
	  	{
	    	foreach ((array) $aGraphicnew AS $cpa => $val)
	      	{
				$title = '';
				print '<div '.(($aResumpoacolor[$d][$cpa])?$aResumpoacolor[$d][$cpa]:'id="amountplan"').'>&nbsp;';
				print '</div>';
	      	}
	  	}
		else
	  		print '<div id="amountplan">&nbsp;</div>';
		print '<div class="clear"></div>';
		$aGraphicnew = $aResumpoaejec[$d];//jan
		if (count($aGraphicnew)>0)
	  	{
	    	foreach ((array) $aGraphicnew AS $cpa => $val)
	      	{
				$title = '';
				if ($cpa == 'AUT_PAYMENT')
		  			print '<div style="float:left">'.img_picto($langs->trans('Viewpayment'),DOL_URL_ROOT.'/poa/img/pay','',1);
				else
		  			print '<div '.(($aResumpoacolor[$d][$cpa])?$aResumpoacolor[$d][$cpa]:'id="amountplan"').'>&nbsp;';
				print '</div>';
	      	}
	  	}
		print '</td>';
		$newClase = $newClaseor;
		$iGrafico = tipo_grafico($aLimite,$obj->m_jul);
		$newClaseor__ = $newClase__;
		if ($obj->p_jul)
	  		$newClaseor__ .= '';
		else
	  		$newClaseor__ .= $backg.' ';
		print '<td '.$newClaseor__.'">';
	// print '<div '.($obj->m_jul?'class="left '.$backg.' '.($filtromenu['a1']?'product':'').'"':'class="left"').' style="height:12px;width:39px;">';
	// print (!empty($obj->m_jul)?'<a href="#">'.img_picto(price($obj->m_jul),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'');
	// print '</div>';
		print '<div class="clear"></div>';
		$d = 7;
		$aGraphicnew = $aResumpoaplan[$d];//jan
		if (count($aGraphicnew)>0)
	  	{
	    	foreach ((array) $aGraphicnew AS $cpa => $val)
	      	{
				$title = '';
				print '<div '.(($aResumpoacolor[$d][$cpa])?$aResumpoacolor[$d][$cpa]:'id="amountplan"').'>&nbsp;';
				print '</div>';
	      	}
	  	}
		else
	  		print '<div id="amountplan">&nbsp;</div>';
		print '<div class="clear"></div>';
		$aGraphicnew = $aResumpoaejec[$d];//jan
		if (count($aGraphicnew)>0)
	  	{
	    	foreach ((array) $aGraphicnew AS $cpa => $val)
	      	{
				$title = '';
				if ($cpa == 'AUT_PAYMENT')
		  			print '<div style="float:left">'.img_picto($langs->trans('Viewpayment'),DOL_URL_ROOT.'/poa/img/pay','',1);
				else
		  			print '<div '.(($aResumpoacolor[$d][$cpa])?$aResumpoacolor[$d][$cpa]:'id="amountplan"').'>&nbsp;';
				print '</div>';
	      	}
	  	}
		print '</td>';
		$newClase = $newClaseor;
		$iGrafico = tipo_grafico($aLimite,$obj->m_aug);
		$newClaseor__ = $newClase__;
		if ($obj->p_aug)
	  		$newClaseor__ .= ' ';
		else
	  		$newClaseor__ .= $backg.' ';
		print '<td '.$newClaseor__.'">';
	// print '<div '.($obj->m_aug?'class="left '.$backg.' '.($filtromenu['a1']?'product':'').'"':'class="left"').' style="height:12px;width:39px;">';
	// print (!empty($obj->m_aug)?'<a href="#">'.img_picto(price($obj->m_aug),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'');
	// print '</div>';
		print '<div class="clear"></div>';
		$d = 8;
		$aGraphicnew = $aResumpoaplan[$d];//jan
		if (count($aGraphicnew)>0)
	  	{
	    	foreach ((array) $aGraphicnew AS $cpa => $val)
	      	{
				$title = '';
				print '<div '.(($aResumpoacolor[$d][$cpa])?$aResumpoacolor[$d][$cpa]:'id="amountplan"').'>&nbsp;';
				print '</div>';
	      	}
	  	}
		else
	  		print '<div id="amountplan">&nbsp;</div>';
		print '<div class="clear"></div>';
		$aGraphicnew = $aResumpoaejec[$d];//jan
		if (count($aGraphicnew)>0)
	  	{
	    	foreach ((array) $aGraphicnew AS $cpa => $val)
	      	{
				$title = '';
				if ($cpa == 'AUT_PAYMENT')
		  			print '<div style="float:left">'.img_picto($langs->trans('Viewpayment'),DOL_URL_ROOT.'/poa/img/pay','',1);
				else
		  			print '<div '.(($aResumpoacolor[$d][$cpa])?$aResumpoacolor[$d][$cpa]:'id="amountplan"').'>&nbsp;';
				print '</div>';
	      	}
	  	}
		print '</td>';
		$newClase = $newClaseor;
		$iGrafico = tipo_grafico($aLimite,$obj->m_sep);
		$newClaseor__ = $newClase__;
		if ($obj->p_sep)
	  		$newClaseor__ .= 'trimestre';
		else
	  		$newClaseor__ .= $backg.' trimestre';
		print '<td '.$newClaseor__.'">';
	// print '<div '.($obj->m_sep?'class="left '.$backg.' '.($filtromenu['a1']?'product':'').'"':'class="left"').' style="height:18px;width:39px;">';
	// print (!empty($obj->m_sep)?'<a href="#">'.img_picto(price($obj->m_sep),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'');
	// print '</div>';
		print '<div class="clear"></div>';
		$d = 9;
		$aGraphicnew = $aResumpoaplan[$d];//jan
		if (count($aGraphicnew)>0)
	  	{
	    	foreach ((array) $aGraphicnew AS $cpa => $val)
	      	{
				$title = '';
				print '<div '.(($aResumpoacolor[$d][$cpa])?$aResumpoacolor[$d][$cpa]:'id="amountplan"').'>&nbsp;';
				print '</div>';
	      	}
	  	}
		else
	  		print '<div id="amountplan">&nbsp;</div>';
		print '<div class="clear"></div>';
		$aGraphicnew = $aResumpoaejec[$d];//jan
		if (count($aGraphicnew)>0)
	  	{
	    	foreach ((array) $aGraphicnew AS $cpa => $val)
	      	{
				$title = '';
				if ($cpa == 'AUT_PAYMENT')
		  			print '<div style="float:left">'.img_picto($langs->trans('Viewpayment'),DOL_URL_ROOT.'/poa/img/pay','',1);
				else
		  			print '<div '.(($aResumpoacolor[$d][$cpa])?$aResumpoacolor[$d][$cpa]:'id="amountplan"').'>&nbsp;';
				print '</div>';
	      	}
	  	}
		print '</td>';
		$newClase = $newClaseor;
		$iGrafico = tipo_grafico($aLimite,$obj->m_oct);
		$newClaseor__ = $newClase__;
		if ($obj->p_oct)
	  		$newClaseor__ .= ' ';
		else
	  		$newClaseor__ .= $backg.' ';
		print '<td '.$newClaseor__.'">';
	// print '<div '.($obj->m_oct?'class="left '.$backg.' '.($filtromenu['a1']?'product':'').'"':'class="left"').' style="height:12px;width:39px;">';
	// print (!empty($obj->m_oct)?'<a href="#">'.img_picto(price($obj->m_oct),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'');
	// print '</div>';
		print '<div class="clear"></div>';
		$d = 10;
		$aGraphicnew = $aResumpoaplan[$d];//jan
		if (count($aGraphicnew)>0)
	  	{
	    	foreach ((array) $aGraphicnew AS $cpa => $val)
	      	{
				$title = '';
				print '<div '.(($aResumpoacolor[$d][$cpa])?$aResumpoacolor[$d][$cpa]:'id="amountplan"').'>&nbsp;';
				print '</div>';
	      	}
	  	}
		else
	  		print '<div id="amountplan">&nbsp;</div>';
		print '<div class="clear"></div>';
		$aGraphicnew = $aResumpoaejec[$d];//jan
		if (count($aGraphicnew)>0)
	  	{
	    	foreach ((array) $aGraphicnew AS $cpa => $val)
	      	{
				$title = '';
				if ($cpa == 'AUT_PAYMENT')
		  			print '<div style="float:left">'.img_picto($langs->trans('Viewpayment'),DOL_URL_ROOT.'/poa/img/pay','',1);
				else
		  			print '<div '.(($aResumpoacolor[$d][$cpa])?$aResumpoacolor[$d][$cpa]:'id="amountplan"').'>&nbsp;';
				print '</div>';
	      	}
	  	}
		print '</td>';
		$newClase = $newClaseor;
		$iGrafico = tipo_grafico($aLimite,$obj->m_nov);
		$newClaseor__ = $newClase__;
		if ($obj->p_nov)
	  		$newClaseor__ .= ' ';
		else
	  		$newClaseor__ .= $backg.' ';
		print '<td '.$newClaseor__.'">';
	// print '<div '.($obj->m_nov?'class="left '.$backg.' '.($filtromenu['a1']?'product':'').'"':'class="left"').' style="height:12px;width:39px;">';
	// print (!empty($obj->m_nov)?'<a href="#">'.img_picto(price($obj->m_nov),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'');
	// print '</div>';
		print '<div class="clear"></div>';
	// if (empty($obj->p_nov))
	//   {
		$d = 11;
		$aGraphicnew = $aResumpoaplan[$d];//jan
		if (count($aGraphicnew)>0)
	  	{
	    	foreach ((array) $aGraphicnew AS $cpa => $val)
	      	{
				$title = '';
				print '<div '.(($aResumpoacolor[$d][$cpa])?$aResumpoacolor[$d][$cpa]:'id="amountplan"').'>&nbsp;';
				print '</div>';
	      	}
	  	}
		else
	  		print '<div id="amountplan">&nbsp;</div>';
		print '<div class="clear"></div>';
		$aGraphicnew = $aResumpoaejec[$d];//jan
		if (count($aGraphicnew)>0)
	  	{
	    	foreach ((array) $aGraphicnew AS $cpa => $val)
	      	{
				$title = '';
				if ($cpa == 'AUT_PAYMENT')
		  			print '<div style="float:left">'.img_picto($langs->trans('Viewpayment'),DOL_URL_ROOT.'/poa/img/pay','',1);
				else
		  			print '<div '.(($aResumpoacolor[$d][$cpa])?$aResumpoacolor[$d][$cpa]:'id="amountplan"').'>&nbsp;';
				print '</div>';
	      	}
	  	}
	//}
	
		print '</td>';
		$newClase = $newClaseor;
		$iGrafico = tipo_grafico($aLimite,$obj->m_dec);
		$newClaseor__ = $newClase__;
		if ($obj->p_dec)
	  		$newClaseor__ .= $backg.' trimestre';
		else
	  		$newClaseor__ .= $backg.' trimestre';
	//GRAFICO META
		print '<td '.$newClaseor__.'">';
	// print '<div '.($obj->m_dec?'class="left '.$backg.' '.($filtromenu['a1']?'product':'').'"':' class="left"').' style="height:12px;width:39px;">';
	// print (!empty($obj->m_dec)?'<a href="#">'.img_picto(price($obj->m_dec),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'');
	// print '</div>';
		print '<div class="clear"></div>';
	// if (empty($obj->p_dec))
	//   {
		$d = 12;
		$aGraphicnew = $aResumpoaplan[$d];//dec
	
		if (count($aGraphicnew)>0)
	  	{
	    	foreach ((array) $aGraphicnew AS $cpa => $val)
	      	{
				$title = '';
				print '<div '.(($aResumpoacolor[$d][$cpa])?$aResumpoacolor[$d][$cpa]:'id="amountplan"').'>&nbsp;';
				print '</div>';
	      	}
	  	}
		else
	  		print '<div id="amountplan">&nbsp;</div>';
		print '<div class="clear"></div>';
		$aGraphicnew = $aResumpoaejec[$d];//jan
		if (count($aGraphicnew)>0)
	  	{
	    	foreach ((array) $aGraphicnew AS $cpa => $val)
	      	{
				$title = '';
				if ($cpa == 'AUT_PAYMENT')
		  			print '<div style="float:left">'.img_picto($langs->trans('Viewpayment'),DOL_URL_ROOT.'/poa/img/pay','',1);
				else
		  			print '<div '.(($aResumpoacolor[$d][$cpa])?$aResumpoacolor[$d][$cpa]:'id="amountplan"').'>&nbsp;';
				print '</div>';
	      	}
	  	}
	// }
		print '</td>';
    }
    $newClase = $newClaseor;
    
    $sumaEne+=$obj->m_ene;
    $sumaFeb+=$obj->m_feb;
    $sumaMar+=$obj->m_mar;
    $sumaApr+=$obj->m_apr;
    $sumaMay+=$obj->m_may;
    $sumaJun+=$obj->m_jun;
    $sumaJul+=$obj->m_jul;
    $sumaAug+=$obj->m_aug;
    $sumaSep+=$obj->m_sep;
    $sumaOct+=$obj->m_oct;
    $sumaNov+=$obj->m_nov;
    $sumaDec+=$obj->m_dec;
    
}
?>