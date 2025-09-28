<?php
  //calendario
if ($opver == 1 && !$lMobile)// && $numCol[73]  RQC QUITADO
  {
	$aLinespl = $objstr->linespl;
	$newClaseor = $newClase;
	$newClaseor__ = $newClase;
	$iGrafico = tipo_grafico($aLimite,$obj->m_jan);
	print '<div id="amountone" '.$newClaseor__.'">';
	print '<div '.($obj->m_jan?'class="left '.$backg.' '.($filtromenu['a1']?'product':'').'"':'class="left"').' style="height:18px;width:39px;">';
	print (!empty($obj->m_jan)?'<a href="#">'.img_picto(price($obj->m_jan),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'');
	print '</div>';

	print '</div>';
	print '<div id="amountone" '.$newClaseor__.'">';
	$iGrafico = tipo_grafico($aLimite,$obj->m_feb);
	print '<div '.($obj->m_feb?'class="left '.($filtromenu['a1']?'product':'').'"':'class="left"').' style="height:12px;width:39px;">';
	print (!empty($obj->m_feb)?'<a href="#">'.img_picto(price($obj->m_feb),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'');
	print '</div>';
	print '</div>';
	print '<div id="amountone" '.$newClaseor__.'">';
	$iGrafico = tipo_grafico($aLimite,$obj->m_mar);
	print '<div '.($obj->m_mar?'class="left '.$backg.' '.($filtromenu['a1']?'product':'').'"':'class="left"').' style="height:12px;width:39px;">';
	print (!empty($obj->m_mar)?'<a href="#">'.img_picto(price($obj->m_mar),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'');
	print '</div>';
	print '</div>';
	print '<div id="amountone" '.$newClaseor__.'">';
	$iGrafico = tipo_grafico($aLimite,$obj->m_apr);
	print '<div '.($obj->m_apr?'class="left '.$backg.' '.($filtromenu['a1']?'product':'').'"':'class="left"').' style="height:12px;width:39px;">';
	print (!empty($obj->m_apr)?'<a href="#">'.img_picto(price($obj->m_apr),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'');
	print '</div>';
	print '</div>';
	print '<div id="amountone" '.$newClaseor__.'">';
	$iGrafico = tipo_grafico($aLimite,$obj->m_may);
	print '<div '.($obj->m_may?'class="left '.$backg.' '.($filtromenu['a1']?'product':'').'"':'class="left"').' style="height:12px;width:39px;">';
	print (!empty($obj->m_may)?'<a href="#">'.img_picto(price($obj->m_may),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'');
	print '</div>';
	print '</div>';
	print '<div id="amountone" '.$newClaseor__.'">';
	$iGrafico = tipo_grafico($aLimite,$obj->m_jun);
	print '<div '.($obj->m_jun?'class="left '.$backg.' '.($filtromenu['a1']?'product':'').'"':'class="left"').' style="height:12px;width:39px;">';
	print (!empty($obj->m_jun)?'<a href="#">'.img_picto(price($obj->m_jun),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'');
	print '</div>';
	print '</div>';
	print '<div id="amountone" '.$newClaseor__.'">';
	$iGrafico = tipo_grafico($aLimite,$obj->m_jul);
	print '<div '.($obj->m_jul?'class="left '.$backg.' '.($filtromenu['a1']?'product':'').'"':'class="left"').' style="height:12px;width:39px;">';
	print (!empty($obj->m_jul)?'<a href="#">'.img_picto(price($obj->m_jul),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'');
	print '</div>';
	print '</div>';
	print '<div id="amountone" '.$newClaseor__.'">';
	$iGrafico = tipo_grafico($aLimite,$obj->m_aug);
	print '<div '.($obj->m_aug?'class="left '.$backg.' '.($filtromenu['a1']?'product':'').'"':'class="left"').' style="height:12px;width:39px;">';
	print (!empty($obj->m_aug)?'<a href="#">'.img_picto(price($obj->m_aug),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'');
	print '</div>';
	print '</div>';
	print '<div id="amountone" '.$newClaseor__.'">';
	$iGrafico = tipo_grafico($aLimite,$obj->m_sep);
	print '<div '.($obj->m_sep?'class="left '.$backg.' '.($filtromenu['a1']?'product':'').'"':'class="left"').' style="height:12px;width:39px;">';
	print (!empty($obj->m_sep)?'<a href="#">'.img_picto(price($obj->m_sep),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'');
	print '</div>';
	print '</div>';
	print '<div id="amountone" '.$newClaseor__.'">';
	$iGrafico = tipo_grafico($aLimite,$obj->m_oct);
	print '<div '.($obj->m_oct?'class="left '.$backg.' '.($filtromenu['a1']?'product':'').'"':'class="left"').' style="height:12px;width:39px;">';
	print (!empty($obj->m_oct)?'<a href="#">'.img_picto(price($obj->m_oct),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'');
	print '</div>';
	print '</div>';
	print '<div id="amountone" '.$newClaseor__.'">';
	$iGrafico = tipo_grafico($aLimite,$obj->m_nov);
	print '<div '.($obj->m_nov?'class="left '.$backg.' '.($filtromenu['a1']?'product':'').'"':'class="left"').' style="height:12px;width:39px;">';
	print (!empty($obj->m_nov)?'<a href="#">'.img_picto(price($obj->m_nov),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'');
	print '</div>';
	print '</div>';

	print '<div id="amountone" '.$newClaseor__.'">';
	$d = 12;
	$idpl = 'pl'.$objstr->id.'_'.($aLinespl[$gestion][12]['q']->id?$aLinespl[$gestion][12]['q']->id.'_':0).$d;
	$idpla = $objstr->id;
	$idplb = ($aLinespl[$gestion][12]['PLAN']->id?$aLinespl[$gestion][12]['q']->id:0);
	$idplc = $d;

	//$idpl = $objstr->id+($aLinespl[$gestion][12]['q']->id?$aLinespl[$gestion][12]['q']->id:0)+$d;
	$idpl1 = $idpl.'p';
	if ($user->rights->poa->poa->mod || $user->admin)
	  {
	    print '<div id="'.$idpl.'" style="visibility:hidden; display:none;">'.'<input id="'.$idpl.'pp" type="text" name="montha" value="'.$aLinespl[$gestion][12]['PLAN']->quant
	      .'" onblur="CambiarURLFrameplan('.$idpla.','.$idplb.','.$idplc.','.$gestion.','.$idplb.','.'this.value);" size="2">'.'</div>';

	    print '<div  id="'.$idpl1.'" style="visibility:visible; display:block;" onclick="visual_str('.$idpla.','.$idplb.','.$idplc.',1)">';
	    print '<a href="#" title="'.$aLinespl[$gestion][12]['PLAN']->quant.'">'.$aLinespl[$gestion][12]['PLAN']->quant.'&nbsp;</a>';
	    print '</div>';
	  }
	else
	  {
	    print '<span  id="'.$idpl1.'" style="visibility:visible; display:block;">';
	    print '<a href="#" title="'.$aLinespl[$gestion][12]['PLAN']->quant.'">'.$aLinespl[$gestion][12]['PLAN']->quant.'&nbsp;</a>';
	    print '</span>';
	  }
	$iGrafico = tipo_grafico($aLimite,$obj->m_dec);
	print '<div '.($obj->m_dec?'class="left '.$backg.' '.($filtromenu['a1']?'product':'').'"':' class="left"').' style="height:12px;width:39px;">';
	print (!empty($obj->m_dec)?'<a href="#">'.img_picto(price($obj->m_dec),DOL_URL_ROOT.'/poa/img/'.$aGrafico[$iGrafico],'',1).'</a>':'');
	print '</div>';

	print '</div>';
    $newClase = $newClaseor;
  }

?>