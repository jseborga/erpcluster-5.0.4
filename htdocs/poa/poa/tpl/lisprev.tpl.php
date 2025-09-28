<?php

$aTotalAct       = array();
$aTotalActpen    = array();
$aResumpoaplan   = array();
$aResumpoaejec   = array();
$aResumpoacolor  = array();
$aGraphice_      = array();
$aGraphicecode_  = array();
$aGraphicetitle_ = array();
$claserow = '';

foreach ((array) $objactl->array AS $jl => $objppl)
{
  	$lViewprev = false;
  	//principal
  	$nnSearch = 0;
  	if ($aActivityf[$obj->id][$objppl->id])	$lViewprev = true;
  	//ACTIVIDAD
	$titleact = $objppl->label;

	//verificamos los filtros de fila
  	if (empty($filtromenu['f4'])) //ver en curso
    	if ($aEstado[$objppl->id] == 1)
      		$lViewprev = false;
  	if (empty($filtromenu['f5'])) //ver demorados
    	if ($aEstado[$objppl->id] == 3)
      		$lViewprev = false;
  	if (empty($filtromenu['f6'])) //ver sin cronograma
    	if ($aEstado[$objppl->id] == -1)
      		$lViewprev = false;
  	if (empty($filtromenu['f3'])) //ver sin cronograma
    	if ($aEstado[$objppl->id] == 9)
      		$lViewprev = false;

  	if ($objppl->statut > 0)
    {
      	$aTotalAct['budget']+=$objppl->amount;
      	$aTotalSAct['budget']+=$objppl->amount;
    }
  	else
    	$aTotalActpen['budget']+= $objppl->amount;

  	$idPreventivo = 0;
  	$lViewprev= true;
  	if ($lViewprev)
    {
      	$a = !$a;
      	//buscamos el ultimo reporte
      	$objactw->getlist($objppl->id);
      	if (count($objactw->array)>0)
		{
	  		$a1 =1;
	  		foreach ($objactw->array AS $j1 => $objaw)
	    	{
	      		if ($a1 == 1)
				{
		  			$aHtml['a'.$objppl->id]['datetracking'] = $objaw->date_tracking;
		  			$aHtml['a'.$objppl->id]['followup'] = $objaw->followup;
		  			$aHtml['a'.$objppl->id]['followto'] = $objaw->followto;
                    $aHtml['a'.$objppl->id]['code_area_next'] = $objaw->code_area_next;
                    $aHtml['a'.$objppl->id]['doc_verif'] = $objaw->doc_verif;
				}
	      		$a1++;
	    	}
		}
      	//buscamos el preventivo de la actividad actual
      	$idPreventivo = $objppl->fk_prev;
      	$idPac = ($objppl->fk_pac?$objppl->fk_pac:0);
      	$monthPac = 0;
      	//buscamos el pac para conocer el mes de inicio
      	if ($objpac->fetch($idPac)>0)
			if ($objpac->id == $idPac)
	  			$monthPac = $objpac->month_init;

      	//obtenemos la ultima actuacion de seguimiento de la actividad
      	$objactw->getlast($objppl->id);
      	$followup = '';
      	$followto = '';
      	$idworkflow = 0;
      	$titlew = '';
      	$aDatework = array();
      	$meswork = 0;
      	if ($objactw->fk_activity == $objppl->id)
		{
	  		$followup = $objactw->followup;
	  		$followto = $objactw->followto;
	  		$aDatework = dol_getdate($objactw->date_tracking);
	  		$meswork = $aDatework['mon'];
	  		$idworkflow = $objactw->id;
	  		$titlew = '<p><b>'.$langs->trans('Title').':</b> <i>'.$objppl->label.'</i>';
	  		$titlew.= '<br><b>'.$langs->trans('Date').':</b> '.dol_print_date($objactw->date_tracking,'day');
	  		$titlew.= '<br><b>'.$langs->trans('Followup').':</b> '.$objactw->followup;
	  		$titlew.= '<br><b>'.$langs->trans('Followto').':</b> '.$objactw->followto;
	  		$titlew.='</p>';
		}
      	$lCreateprev = True;
      	if ($objppl->fk_prev>0)
			$lCreateprev = False;

      	if ($a == true)
		{
	  		$newClase = ' class=""';
	  		$claserow = ' ';
	  		if ($obj->partida != $objppl->partida)
	    		$newClase = ' class=" err"';
	  		$newClase_ = ' class=" ';
		}
      	else
		{
	  		$claserow = '  ';
	  		$newClase = ' class=""';
	  		if ($obj->partida != $objppl->partida)
	    		$newClase = ' class=" err"';
	  			//$newClase_ = ' style="background-color:#fffef0;" class="left ';

	  		$newClase_ = ' class=" ';
		}
		/*
		***********************
		//armando la fila
		***********************
		*/
      	$lisPrev.= '<tr>';//inicio fila producto

        $lisPrev.= '<td>'.'<a href="'.DOL_URL_ROOT.'/poa/execution/ficheprev.php?ida='.$objppl->id.'" >'.$sigla.'<br>'.zerofill($objppl->nro_activity,4).'</a>'.'</td>';

        $aHtml['a'.$objppl->id]['sigla'] = 'A-'.$objppl->nro_activity;
        $aHtml['a'.$objppl->id]['estadoact'] = $objppl->statut;

      	if ($numCol[1])
		{
	  		$lisPrev.= '<td>';
	  		$lisPrev.=  '<a href="'.DOL_URL_ROOT.'/poa/execution/ficheprev.php?ida='.$objppl->id.'" title="'.STRTOUPPER($objppl->pseudonym).'">';
	  		$lisPrev.= (strlen(trim($objppl->label))>58?substr(trim(STRTOUPPER($objppl->label)),0,56).'..':STRTOUPPER($objppl->label));
	  		$lisPrev.='</a>';
	  		$lisPrev.=  '</td>';
		}
      	if ($numCol[2])
		{
	  		$lisPrev.= '<td>';
	  		$lisPrev.=  '<a href="#" title="'.STRTOUPPER($objppl->label).'">';
	  		$lisPrev.= (strlen(trim($objppl->pseudonym))>68?substr(trim(STRTOUPPER($objppl->pseudonym)),0,66).'..':STRTOUPPER($objppl->pseudonym));
	  		$lisPrev.= '</a>';
	  		$lisPrev.=  '</td>';
		}
      	$aHtml['a'.$objppl->id]['label'] = $objppl->label;
      	$aHtml['a'.$objppl->id]['pseudonym'] = $objppl->pseudonym;
      //closed
      	if ($objppl->statut == 9)
			$aHtml['a'.$objppl->id]['CLOSED'] = $objppl->tms;
      	$lisPrev.= '<td >'.$objppl->partida.'</td>'; //partida
      	$aHtml['a'.$objppl->id]['partida'] = $objppl->partida;
      	$lisPrev.= '<td align="right">'.price(price2num($objppl->amount,'MT')).'</td>';//presupuesto
      	$aHtml['a'.$objppl->id]['presupuesto'] = $objppl->amount;
      // $aTotalAct['budget']+=$objppl->amount;
      // $aTotalSAct['budget']+=$objppl->amount;
      	if ($numCol[71])
		{
	  		$lisPrev.= '<td>&nbsp;</td>';
	  		$lisPrev.= '<td>&nbsp;</td>';
		}

      	if ($numCol[72])
		{
	  		$lisPrev.= '<td>&nbsp;</td>';
		}
      	if ($numCol[73])
		{
	  		$lisPrev.= '<td>&nbsp;</td>';
		}

      	//resumen de la actividad
      	$aDatawork = array();
      	$aGraphice_ = array();
      	$aGraphicecode_ = array();
      	$aGraphicetitle_ = array();

      	//preventivos
      	// $aGraphice_ = $aActivityres[$objppl->id]['resumcolor']['aGraphice_'];
      	// $aGraphicecode_ = $aActivityres[$objppl->id]['resumcolor']['aGraphicecode_'];
      	// $aGraphicetitle_ = $aActivityres[$objppl->id]['resumcolor']['aGraphicetitle_'];
      	// $aResumpoacolor = $aActivityres[$objppl->id]['resumcolor']['aResumpoacolor'];
      	// $aResumpoaejec = $aActivityres[$objppl->id]['resumcolor']['aResumpoaejec'];
      	// $aDatawork = $aActivityres[$objppl->id]['aDatawork'];

      	//obtenemos el preventivo de la gestion
      	//$objpre->fetch($idPreventivo);
      	$objpre = $_SESSION['aActivityreg'][$objppl->id]['objpre'];
      	$aDatepre = dol_getdate($objpre->date_preventive);
      	$aHtml['a'.$objppl->id]['PREVENTIVE'] = $objpre->date_preventive;

      	$aDatawork[$objpre->gestion][$aDatepre['mon']]['PREVENTIVE'][$idPreventivo] = $objpre;
      	//resumen color
      	if ($objpre->id == $idPreventivo && $idPreventivo > 0)
      	{
	  		$total = $objprev->getsum($idPreventivo);

      	  	list($aGraphice_,$aGraphicecode_,$aGraphicetitle_,$aResumpoacolor,$aResumpoaejec) = resumcolor($aDatepre,$aGraphice_,$aGraphicecode_,$aGraphicetitle_,$aResumpoacolor,$aResumpoaejec,$objpre,'PREVENTIVE',$objpre->id,'date_preventive','label',$titleact,$total);
      	  	//FIN RESUMEN
      	}


      	///////////////////
      	//obtenemos el proceso del preventivo
      	$aProcess = $_SESSION['aActivityreg'][$objppl->id]['aProcess'];
	    // if ($idPreventivo>0)
    	// 	$aProcess = getlist_process($idPreventivo);

      	/*
       	*procedimiento provisional para
       	*actualizacion del historial workflow
       	*/

      	// if (count($aProcess)>0)
      	// 	actualiza_workflow($aProcess);

      	$fk_prev_pri = $idPreventivo;
      	$fk_proc_pri = 0;
      	//verificamos si tiene un preventivo principal
      	if (count($aProcess['pri'])>0)
      		foreach ((array) $aProcess['pri'] AS $fkProcess => $fk_prev_)
      	  	{
      	    	$fk_proc_pri = $fkProcess;
      	    	$fk_prev_pri = $fk_prev_;
      	  	}
      	//recuperamos el proceso
      	$objproc->fetch($fk_proc_pri);
      	//$objproc = $_SESSION['aActivityreg'][$objppl->id]['objproc'][$fk_proc_pri];
      	//$objproc = $_SESSION['aActivityprocess'][$fk_proc_pri];

      	$aDatepro = dol_getdate($objproc->date_process);
      	$aHtml['a'.$objppl->id]['INI_PROCES'] = $objproc->date_process;

      	$aDatawork[$objproc->gestion][$aDatepro['mon']]['INI_PROCES'][$fk_proc_pri] = $objproc;
      	//resumen color
      	if ($objproc->id == $fk_proc_pri)
      	{
      		list($aGraphice_,$aGraphicecode_,$aGraphicetitle_,$aResumpoacolor,$aResumpoaejec) = resumcolor($aDatepro,$aGraphice_,$aGraphicecode_,$aGraphicetitle_,$aResumpoacolor,$aResumpoaejec,$objproc,'INI_PROCES',$objproc->id,'date_process','label',$titleact,$objproc->amount);
      	  	//FIN RESUMEN
      	}

      	//CONTRATOS
      	//$objprocc->getlist_contrat($fk_proc_pri);
      	//$objprocc = $_SESSION['aActivityreg'][$objppl->id]['objprocc'];
      	$aContrat = $_SESSION['aActivityreg'][$objppl->id]['objprocc'];
      	//obtenemos los contratos
      	if (count($aContrat)>0 && $fk_proc_pri >0)
      	{
      	  	foreach ((array) $aContrat AS $fk_c => $value)
      	    {
	      		$objcon = $_SESSION['aActivityreg'][$objppl->id]['objcon'][$fk_c];
      	      	//$objcon->fetch($fk_c);
      	      	if ($objcon->id == $fk_c && $fk_c>0)
      			{
		  			$aDatecon = dol_getdate($objcon->date_contrat);
		  			$aHtml['a'.$objppl->id]['RECEP_PRODUCTS'] = $objcon->date_contrat;

      		  		$aDatawork[$aDatecon['year']][$aDatecon['mon']]['RECEP_PRODUCTS'][$fk_c] = $objcon;
      		  		//resumen color

      		  		list($aGraphice_,$aGraphicecode_,$aGraphicetitle_,$aResumpoacolor,$aResumpoaejec) = resumcolor($aDatecon,$aGraphice_,$aGraphicecode_,$aGraphicetitle_,$aResumpoacolor,$aResumpoaejec,$objcon,'RECEP_PRODUCTS',$objcon->id,'date_contrat',"array_options['options_ref_contrato']",$titleact,$objcon->total_ttc);
      		  		//FIN RESUMEN
		  			//verificamos la recepcion
		  			//$objcon->fetch_lines();
					$lClosecontrat = true;
		  			$date_cloture = '';
				  	foreach ((array) $objcon->lines AS $k => $objl)
		    		{
		      			$objcontline = new Contratligne($db);
		      			$objcontline->fetch($objl->id);
		      			if ($objcontline->id == $objl->id)
						{
			  				$fk_cl = $objcontline->id;
			  				$date_cloture = $objcontline->date_cloture;
			  				if (empty($objcontline->date_cloture) ||
			      			is_null($objcontline->date_cloture))
			    			{
			      				$lClosecontrat = false;
			    			}
						}
		      			else
							$lClosecontrat = false;
		    		}
		  			if ($lClosecontrat)
		    		{
		      			$aDaterecep = dol_getdate($date_cloture);
		      			$aHtml['a'.$objppl->id]['PARTIAL_REPORT_ACCORDANCE'] = $date_cloture;
		      			$aDatawork[$aDaterecep['year']][$aDaterecep['mon']]['PARTIAL_REPORT_ACCORDANCE'][$fk_cl] = $objcontline;
		      			//resumen color
		      			list($aGraphice_,$aGraphicecode_,$aGraphicetitle_,$aResumpoacolor,$aResumpoaejec) = resumcolor($aDaterecep,$aGraphice_,$aGraphicecode_,$aGraphicetitle_,$aResumpoacolor,$aResumpoaejec,$objcontline,'PARTIAL_REPORT_ACCORDANCE',$objcontline->id,'date_cloture',"commentaire",$titleact);
		    		}
      			}
      	    }
      	}

      	if($numCol[9] || $numCol[10] || $numCol[15])
		{
	  		if ($objprev->getsum_str_part_prev($idPreventivo,$obj->gestion,$obj->fk_structure,$obj->id, $obj->partida))
	    	{
	      		$aHtml['a'.$objppl->id]['preventivo'] = $objprev->total;
	      		$aTotalAct['preventive']+=$objprev->total;
	      		$aTotalSAct['preventive']+=$objprev->total;
	      		if($numCol[9])
					$lisPrev.= '<td align="right">'.price(price2num($objprev->total,'MT')).'</td>';
	      		if($numCol[10])
					$lisPrev.= '<td align="right">'.price(($objppl->amount>0?price2num($objprev->total/$objppl->amount*100,'MT'):0)).' %</td>';
	      		if($numCol[15])
					$lisPrev.= '<td align="right" >'.price(price2num($objppl->amount - $objprev->total,'MT')).'</td>';
	    	}
	  		else
	    		$lisPrev.= '<td align="right" >'.price(price2num(0,'MT')).'</td>';
		}

      	//comprometidos
      	//rqcc
      	$objcomp->getsum_prev_str_part($idPreventivo,$obj->fk_structure,$obj->id, $objppl->partida);
      	if ($numCol[11] || $numCol[12] || $numCol[16])
		{
	  		$aHtml['a'.$objppl->id]['comprometido'] = $objcomp->total;
	  		$aTotalAct['committed']+=$objcomp->total;
	  		$aTotalSAct['committed']+=$objcomp->total;
		}

      	if ($numCol[11])
		{
	  		$lisPrev.= '<td align="right" >'.price(price2num($objcomp->total,'MT')).'</td>';
		}
      	if ($numCol[12])
		{
	  		$lisPrev.= '<td align="right" >'.price(($objprev->total>0?price2num($objcomp->total/$objprev->total*100,'MT'):0)).' %</td>';
		}
      	if ($numCol[16])
		{
	  		$lisPrev.= '<td align="right" >'.price(price2num($objprev->total -$objcomp->total,'MT')).'</td>';
		}

      	//devengados
      	//$objdeve->getlist($idPreventivo);
      	$objdeve = $_SESSION['aActivityreg'][$objppl->id]['objdeve'];
      	if (count($objdeve->array)>0)
      	{
	  		$nro_dev = 0;
      	  	foreach ((array) $objdeve->array AS $o => $objd)
      	    {
      	      	$aDatedev = dol_getdate($objd->date_dev);
	      		$aHtml['a'.$objppl->id]['AUT_PAYMENT'] = $objd->date_dev;

	      		if ($objd->nro_dev != $nro_dev)
				{
		  			$nro_dev = $objd->nro_dev;
		  			$aDatawork[$aDatedev['year']][$aDatedev['mon']]['AUT_PAYMENT'][$objd->id] = $objd;
		  			//resumen color
		  			list($aGraphice_,$aGraphicecode_,$aGraphicetitle_,$aResumpoacolor,$aResumpoaejec) = resumcolor($aDatedev,$aGraphice_,$aGraphicecode_,$aGraphicetitle_,$aResumpoacolor,$aResumpoaejec,$objd,'AUT_PAYMENT',$objd->id,'date_dev',"nro_dev",$titleact,$objd->amount);
				}
		      	//FIN RESUMEN
      	    }
      	}
      	$objdeve->getsum_prev_str_part($idPreventivo,$obj->fk_structure,$obj->id, $objppl->partida);
      	//total dev
      	if ($numCol[13] || $numCol[14] || $numCol[17])
		{
	  		$aHtml['a'.$objppl->id]['devengado'] = $objdeve->total;
	  		$aTotalAct['accrued']+=$objdeve->total;
	  		$aTotalSAct['accrued']+=$objdeve->total;
		}
      	if ($numCol[13])
		{
	  		$lisPrev.= '<td align="right" >'.price(price2num($objdeve->total,'MT')).'</td>';
		}
      	if ($numCol[14])
		{
	  		$lisPrev.= '<td align="right" >'.price(($objprev->total>0?price2num($objdeve->total/$objprev->total*100,'MT'):0)).' %</td>';
		}
      	//saldo
      	if ($numCol[17])
		{
	  		$lisPrev.= '<td align="right" >'.price(price2num($objcomp->total - $objdeve->total,'MT')).'</td>';
		}

      	//lista el cronograma por mes
      	if ($opver == 1 && !$lMobile)
		{
	  		$aGraphic = array();
			$aGraphiccode = array();
	  		$aGraphictitle = array();
	  		if (count($objppl->array_options)>0)
	    	{
	      		foreach((array)$objppl->array_options AS $p => $objActd)
				{
		  			//armamos array de fechas
		  			$aDateact = dol_getdate($objActd->date_procedure);
		  			$aHtml['a'.$objppl->id]['programed'][$objActd->code_procedure] = $objActd->date_procedure;
		  			if ($aDateact['year'] == $_SESSION['gestion'])
		    		{
		      			$nWeek = ceil($aDateact['mday']/30*100);
		      			//$value = (($nWeek>0 && $nWeek <=20)?1:(($nWeek>20 && $nWeek <=40)?2:(($nWeek>41 && $nWeek <=60)?3:(($nWeek>61 && $nWeek <=80)?4:5))));
		      			//primera opcion
					    $aGraphic[$aDateact['mon']][$objActd->code_procedure] = $objActd->code_procedure;
		      			$aResumpoaplan[$aDateact['mon']][$objActd->code_procedure] = $objActd->code_procedure;
		      			//buscamos el color del procedimiento
		      			$objColor = fetch_typeprocedure($objActd->code_procedure,'code');
		      			$aGraphiccode[$aDateact['mon']][$objActd->code_procedure] = 'style="background:#'.$objColor->colour.'; float:left; width:8px; text-align:right; height:15px;"';
		      			if (empty($aResumpoacolor[$aDateact['mon']][$objActd->code_procedure]))
							$aResumpoacolor[$aDateact['mon']][$objActd->code_procedure] = 'style="background:#'.$objColor->colour.'; float:left; width:8px; text-align:right; height:15px;"';
		      			$title = '<p><b>'.$langs->trans('Title').':</b> '.$objppl->label;
		      			$title.= '<br><b>'.$langs->trans('Procedure').':</b> '.$objColor->label;
		      			$title.= '<br><b>'.$langs->trans('Datetracking').':</b> '.dol_print_date($objActd->date_procedure,'day');
		      			$title.= '<br><b>'.$langs->trans('Detail').':</b> '.$objActd->id.'</p>';
					    $aGraphictitle[$aDateact['mon']][$objActd->code_procedure] = $title;
		    		}
				}
	    	}
	  		//recoerremos calendario
	  		for ($d = 1; $d <= 12; $d++)
	    	{
	      		//div mespac
   	      		$lisPrev.= '<td class="'.$claserow.' '.(($d==3 ||$d==6||$d==9|$d==12)?' trimestre':'').'">';
	      		//div para agrupar
	      		if ($monthPac && $d == $monthPac)
				{
		  			$newClasepac = ' class="fondomespac"';
		  			$lisPrev.= '<div '.$newClasepac.'>'; //mespac interno
				}
	      		else
				{
		  			$lisPrev.= '<div id="amountone_">'; //mespac interno
				}

	      		//$lisPrev.= '<div>';
	      		$aGraphicnew = $aGraphic[$d];
	      		if (count($aGraphicnew)>0)
				{
		  			foreach ((array) $aGraphicnew AS $cpa => $val)
		    		{
		      			$titleerr = '';
			      		if ($cpa == 'INI_PROCES' && $objppl->amount > $conf->global->POA_PAC_MINIMUM)
							$titleerr .= '<br><p><b>'.$langs->trans('Error').':</b> '.$langs->trans('Itisnecessarytoregisterinthepac').'</p>';

			      		$idspan = ($cpa=='PREVENTIVE'?'tprev':($cpa=='INI_PROCES'?'tproc':($cpa=='RECEP_PRODUCTS'?'tcont':($cpa=='AUT_PAYMENT'?'tdeve':($cpa=='PARTIAL_REPORT_ACCORDANCE'?'trece':'tclos')))));
		    	  		$title = '<span id="'.$idspan.'">'.(($aGraphictitle[$d][$cpa])?$aGraphictitle[$d][$cpa].$titleerr:'').'</span>';
		      			$lisPrev.= '<a class="mylinka" href="#" >';
		      			$lisPrev.= $title;
			      		$lisPrev.= '<div '.(($aGraphiccode[$d][$cpa])?$aGraphiccode[$d][$cpa]:'id="amountplan"').'">'.($cpa=='INI_PROCES'?(($objppl->fk_pac<=0 && $objppl->amount > $conf->global->POA_PAC_MINIMUM)?img_picto('',DOL_URL_ROOT.'/poa/img/pac1','',1):'&nbsp;'):'&nbsp;').'</div>';
			      		$lisPrev.= '</a>';
			    	}
				}
	      		else
				{
		  			$lisPrev.= '<div id="amountplan_">&nbsp;</div>';
				}
	      		//mostramos seguimiento a la actividad
	      		if ($meswork == $d)
				{
		  			$titlew = '<span id="tseg">'.$titlew.'</span>';
					$lisPrev.='<a class="mylink" href="'.DOL_URL_ROOT.'/poa/activity/fiche.php?id='.$objppl->id.'&tabs=moni'.'" >';
		  			$lisPrev.=$titlew;
		  			$lisPrev.= '<div style="float:left; width:8px; height:15px;">';
		  			$lisPrev.='<img src="'.DOL_URL_ROOT.'/poa/img/sactivity.png'.'">';
		  			$lisPrev.='</div>';
		  			$lisPrev.='</a>';
				}

		      	$lisPrev.= '<div class="clear"></div>';

	    	  	$lisPrev.= '</div>'; //mespac interno

	      		if (count($aDatawork)>0)
				{
		  			$lisPrev.= '<div id="amountone_">';//datawork
		  			$aGraphicnew = $aDatawork[$_SESSION['gestion']][$d];
			  		foreach ((array) $aGraphicnew AS $cpa => $aData)
			    	{
				  		foreach($aData AS $j => $objn)
						{
							if ($cpa == 'PREVENTIVE')
			    			{
			      				$title = '<span id="tprev">'.(($aGraphicetitle_[$d][$cpa])?$aGraphicetitle_[$d][$cpa][$j]:'').'</span>';
				      			//mostramos el archivo
				      			$dir = DOL_DOCUMENT_ROOT.'/documents/poa/execution/pdf/'.$idPreventivo.'.pdf';
				      			$url = '';
				      			if (is_file($dir))
								{
				  					$url = DOL_URL_ROOT.'/documents/poa'."/execution/pdf/".$idPreventivo.'.pdf';
								}
			      				$lisPrev.= '<a class="mylink" href="'.($url?$url:'#').'" '.($url?'target="_blank"':'').'>';
				      			$lisPrev.=$title;
				      			$lisPrev.= '<div '.(($aGraphicecode_[$d][$cpa][$j])?$aGraphicecode_[$d][$cpa][$j]:'id="amountplan" class="left"').'>&nbsp;';
				      			$lisPrev.= '</div></a>';
				    		}
			  				if ($cpa == 'INI_PROCES')//inicio proceso
			    			{
			      				$title = '<span id="tproc">'.(($aGraphicetitle_[$d][$cpa])?$aGraphicetitle_[$d][$cpa][$j]:'').'</span>';
			      				//mostramos el archivo
				      			$dir = DOL_DOCUMENT_ROOT.'/documents/poa'."/process/pdf/".$idPreventivo.'.pdf';
				      			$url = '';
				      			if (is_file($dir))
								{
							  		$url = DOL_URL_ROOT.'/documents/poa'."/process/pdf/".$idPreventivo.'.pdf';
								}
			      				$lisPrev.= '<a class="mylink" href="'.($url?$url:'#').'" '.($url?'target="_blank"':'').' >';
			      				$lisPrev.=$title;
				      			$lisPrev.= '<div '.(($aGraphicecode_[$d][$cpa][$j])?$aGraphicecode_[$d][$cpa][$j]:'id="amountplan" class="left"').'>&nbsp;';
				      			$lisPrev.= '</div></a>';
				    		}
		  	  				if ($cpa == 'RECEP_PRODUCTS')
		  	    			{
			      				$title = '<span id="tcont">'.(($aGraphicetitle_[$d][$cpa][$j])?$aGraphicetitle_[$d][$cpa][$j]:'').'</span>';
				      			//mostramos el archivo
				      			$dir = DOL_DOCUMENT_ROOT.'/documents/contracts/'.$objn->ref.'/'.$objn->id.'.pdf';
				      			$url = '';
				      			if (is_file($dir))
								{
				  					$url = DOL_URL_ROOT.'/documents/contracts/'.$objn->ref.'/'.$objn->id.'.pdf';
								}
			      				$lisPrev.= '<a class="mylink" href="'.($url?$url:'#').'" '.($url?'target="_blank"':'').' >';
				      			$lisPrev.=$title;
				      			$lisPrev.= '<div '.(($aGraphice_[$d][$cpa][$j])?$aGraphicecode_[$d][$cpa][$j]:'id="amountplan" class="left"').'>&nbsp;';
				      			$lisPrev.= '</div></a>';
		  		    		}
		  	  				if ($cpa == 'AUT_PAYMENT')
		  	    			{
			      				$title = '<span id="tdeve">'.(($aGraphicetitle_[$d][$cpa][$j])?$aGraphicetitle_[$d][$cpa][$j]:'').'</span>';
			      				//mostramos el archivo
				      			$url = '';
				      			$dir = DOL_DOCUMENT_ROOT.'/documents/payment/pdf/'.$objn->id.'.pdf';
				      			if (is_file($dir))
								{
					  				$url = DOL_URL_ROOT.'/documents/payment/pdf/'.$objn->id.'.pdf';
								}
			      				$lisPrev.= '<a class="mylink" href="'.($url?$url:'#').'" '.($url?'target="_blank"':'').'>';
			      				$lisPrev.=$title;
				      			$lisPrev.= '<div style="float:left; width:8px">';
				      			$lisPrev.= img_picto($langs->trans('Viewpayment'),DOL_URL_ROOT.'/poa/img/pay','',1);
				      			$lisPrev.= '</div>';
				      			$lisPrev.= '</a>';
			      			}
		  	  				if ($cpa == 'PARTIAL_REPORT_ACCORDANCE')
		  	    			{
			      				$title = '<span id="trece">'.(($aGraphicetitle_[$d][$cpa][$j])?$aGraphicetitle_[$d][$cpa][$j]:'Err').'</span>';
							      //mostramos el archivo
				      			$url = '';
				      			$dir = '';
								//DOL_DOCUMENT_ROOT.'/documents/payment/pdf/'.$objn->id.'.pdf';
			      				if (is_file($dir))
								{
				  					$url = DOL_URL_ROOT.'/documents/payment/pdf/'.$objn->id.'.pdf';
								}
				      			$lisPrev.= '<a class="mylink" href="'.($url?$url:'#').'" '.($url?'target="_blank"':'').'>';
				      			$lisPrev.= $title;
				      			$lisPrev.= '<div '.(($aGraphice_[$d][$cpa][$j])?$aGraphicecode_[$d][$cpa][$j]:'id="amountplan" class="left"').'>&nbsp;';
				      			$lisPrev.= '</div>';
			    	  			$lisPrev.= '</a>';
			  	    		}
						}
			    	}
		  			$lisPrev.= '<div class="clear"></div>';
		  			$lisPrev.='</div>'; //datawork
				}
	      		$lisPrev.= '</td>';
	    	}
		}
      	//usuario
    	if ($objuser->fetch($objppl->fk_user_create) > 0)
			$lisPrev.= '<td >'.$objuser->login.'</td>';
    	else
			$lisPrev.= '<td >&nbsp;</td>';
    	$aHtml['a'.$objppl->id]['user'] = $objuser->login;

      	//seguimiento
      	if ($numCol[321])
			$lisPrev.= '<td >'.dol_print_date($aHtml['a'.$objppl->id]['datetracking'],'day').'</td>';
      	if ($numCol[322])
			$lisPrev.= '<td >'.(strlen($aHtml['a'.$objppl->id]['followup'])>50?'<a href="#" title="'.$aHtml['a'.$objppl->id]['followup'].'">'.SUBSTR($aHtml['a'.$objppl->id]['followup'],0,50).'...</a>':$aHtml['a'.$objppl->id]['followup']).'</td>';
      	if ($numCol[323])
			$lisPrev.= '<td >'.(strlen($aHtml['a'.$objppl->id]['followto'])>50?'<a href="#" title="'.$aHtml['a'.$objppl->id]['followto'].'">'.SUBSTR($aHtml['a'.$objppl->id]['followto'],0,50).'...</a>':$aHtml['a'.$objppl->id]['followto']).'</td>';

      	//instruccion
      	if ($numCol[93])
		{
	  		// $lisPrev.= '<div id="instruction" >';
	  		// $lisPrev.= '&nbsp;';
	  		// $lisPrev.= '</div>';
		}
      	//pac
      	if ($objppl->fk_pac>0 && $objpac->fetch($objppl->fk_pac)>0)
		{
	  		$aHtml['a'.$objppl->id]['pac'] = $objpac->amount;
            $aHtml['a'.$objppl->id]['mespac'] = $objpac->month_init;
		}
        //revision si corresponde pac o no
            //verificamos si requiere o no pac
            if ($objpre->amount >0)
            {
                if ($objpre->amount > 20000)
                    $aHtml['a'.$objppl->id]['pacreq'] = 1;
                else
                    $aHtml['a'.$objppl->id]['pacreq'] = 0;
            }
            else
            {
                if ($objppl->amount > 0)
                {
                    if ($objppl->amount > 20000)
                        $aHtml['a'.$objppl->id]['pacreq'] = 1;
                    else
                        $aHtml['a'.$objppl->id]['pacreq'] = 0;
                }
            }

      	if ($objppl->fk_pac && $objpac->fetch($objppl->fk_pac)>0 && $xy)
		{
	  		if ($objpac->id == $objppl->fk_pac)
	    	{
	      		if (empty($aColorpac[$objppl->fk_pac]))
					$aColorpac[$objppl->fk_pac] = randomColor();
	      		$newClase3 = $newClase;
	      		$newClase = ' class="left" style="background: '.$aColorpac[$objppl->fk_pac].'"';
	      		if ($numCol[81])
				{
				  	$lisPrev.= '<td >';
					$lisPrev.= '<a href="'.DOL_URL_ROOT.'/poa/pac/liste.php?idp='.$objppl->fk_pac.'" title="'.$objpac->nom.'">'.$objpac->ref.'</a>';
		  			$lisPrev.= '</td>';
				}
	      		if ($numCol[82])
				{
		  			$lisPrev.= '<td >';
		  			$lisPrev.= '<a href="'.DOL_URL_ROOT.'/poa/pac/liste.php?idp='.$objppl->fk_pac.'" title="'.$objpac->nom.'">'.substr($monthArray[$objpac->month_init],0,3).'</a>';
		  			$lisPrev.= '</td>';
				}
	      		if ($numCol[84]) // pac total
				{
		  			$lisPrev.= '<td >';
		  			$lisPrev.= '<a href="'.DOL_URL_ROOT.'/poa/pac/liste.php?idp='.$objppl->fk_pac.'" title="'.$title.'">'.price(price2num($objpac->amount,'MT')).'</a>';
		  			$lisPrev.= '</td>';
				}
	      		if ($numCol[85]) // pac total
				{
		  			$lisPrev.= '<td >';
		  			$lisPrev.= '<a href="'.DOL_URL_ROOT.'/poa/pac/liste.php?idp='.$objppl->fk_pac.'" title="'.$objpac->nom.'">---</a>';
		  			$lisPrev.= '</td>';
				}
	      		$newClase = $newClase3;
	    	}
	  		else
	    	{
	      		$lisPrev.= '<td >';
			    $lisPrev.= '<a href="'.DOL_URL_ROOT.'/poa/pac/liste.php?idp='.$obj->id.'" title="'.$title.'">&nbsp;</a>';
	    		$lisPrev.= '</td>';
	    	}
		}
      	elseif($xy)
		{
	  		$lisPrev.= '<td >';
			$lisPrev.= '<a href="'.DOL_URL_ROOT.'/poa/pac/liste.php?idp='.$obj->id.'" title="'.$title.'">&nbsp;</a>';
	  		$lisPrev.= '</td>';
		}

      	//action
      	if ($numCol[190])
		{
	  		$lisPrev.= '<td >';
		  	if ($lCreateprev)
	    	{
	      		//if ($objppl->statut > 0)
				//	if ($user->rights->poa->act->crear)
		  		//		$lisPrev.= '<a href="'.DOL_URL_ROOT.'/poa/execution/fiche.php'.'?fk_poa='.$obj->id.'&fk_activity='.$objppl->id.'&action=create&dol_hide_leftmenu=1">'.img_picto($langs->trans('Createpreventive'),DOL_URL_ROOT.'/poa/img/p','',1).'</a>';
	    	}
	  		else
	    	{
	      		//if ($user->admin || ($objppl->statut > 0 && $user->rights->poa->prev->leer))
				//{
		  		//	$lisPrev.= '<a href="'.DOL_URL_ROOT.'/poa/execution/ficheprev.php?ida='.$objppl->id.'&dol_hide_leftmenu=1">'.img_picto($langs->trans('Seepreventive'),'view').'</a>';
				//}
	    	}

	      	$lisPrev.= img_picto(($aEstado[$objppl->id]==1?$langs->trans('Interm'):($aEstado[$objppl->id]==3?$langs->trans('Delayed'):($aEstado[$objppl->id]==9?$langs->trans('Finished'):$langs->trans('Andschedule')))), DOL_URL_ROOT.'/poa/img/'.($aEstado[$objppl->id]==1?'ok1':($aEstado[$objppl->id]==3?'demora':($aEstado[$objppl->id]==9?'finished':'pend'))),
			'',
			1);

	  		$lisPrev.= '</td>'; //action
		}
      	else
		{
	  		$nAction=4;
	  		include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/action.tpl.php';
		}

      	//finalizar la lista
      	$lisPrev.= '</tr>';//fin fila product
      	//$lisPrev.= '<div style="clear:both"></div>';
    }
}
?>