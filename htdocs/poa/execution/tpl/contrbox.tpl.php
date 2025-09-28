<?php

//Contrato
/////////////////////////////
if ($idProcess > 0)
{
	$a = true;
	$sumapre = $objproc->amount;
	$lAddcontrat = false;
	$lAdvance = false;
	$sumacont = array();
	$nSumacont = 0;
	$aContratpay = array();
	$aSumcontratpay = array();
	$aContratvalid = array();
	if (count($aContrat) <= 0)
	{
		if ($objproc->statut > 0) $lAddcontrat = true;
	}
	foreach((array) $aContrat AS $i => $ni)
	{
		$idrcreg = $aProcesscontrat[$i];
		$objpcon->fetch($idrcreg);
		if ($objpcon->statut == 1)
			$aContratvalid[$idrcreg] = $idrcreg;
		$tagid = 'fichecontrat'.$aProcesscontrat[$i];
		$objcont->fetch($i);
		$objcont->fetch_lines();

		//buscamos los pagos para este contrato
		$objdev->getlist2($object->id,$i);

		$a = !$a;
		$contratAdd = '';
		$aContratAdd = array();
		$total_ht = 0;
		$total_tva = 0;
		$total_localtax1 = 0;
		$total_localtax2 = 0;
		$total_ttc = 0;
		//revisamos el contrato
		$res=$objcont->fetch_optionals($i,$extralabels);
		if ($objcont->array_options['options_advance']) $lAdvance = true;
		if (!$objcont->array_options['options_order_proced']) $aContratpay[$i] = true;

		$contratAdd.= $objcont->array_options['options_ref_contrato'];
		$aContratname[$i] = $objcont->array_options['options_ref_contrato'];
		$aContratlabel[$i] = $objcont->nom;

		if ($objcont->id == $i)
		{
			$total_plazo += $objcont->array_options['options_plazo'];
			//recuperamos el valor de contrato
			foreach ($objcont->lines AS $olines)
			{
				if (empty($olines->qty)) $lAddcontrat = true;
				$total_ttc += $olines->$total_ttc;
			}

			$datecontrat= $objcont->date_contrat;
			//buscamos si tiene addendum
			if ($conf->addendum->enabled)
			{
				$objadden = new Addendum($db);
				$res = $objadden->getlist($i);
				if ($res>0)
				{
					$total_ht += $objadden->aSuma['total_ht'];
					$total_tva += $objadden->aSuma['total_tva'];
					$total_localtax1 += $objadden->aSuma['total_localtax1'];
					$total_localtax2 += $objadden->aSuma['total_localtax2'];

					$total_ttc += $objadden->aSuma['total_ttc'];
					$aContratAdd[$objcont->id] = array('ref' => $objcont->array_options['options_ref_contrato'], 'note' => $objcont->note_private, 'amount' => $objadden->aSuma['parcial_ttc'][$i]);

						//verificamos los plazos adicionales
					foreach ((array) $objadden->array AS $j1 => $obja)
					{
						$objcontade = new Contrat($db);
						$objcontade->fetch($obja->fk_contrat_son);
						if ($objcontade->id == $obja->fk_contrat_son)
							$total_plazo += $objcontade->array_options['options_plazo'];
						$aContratAdd[$objcontade->id] = array('ref' => $objcontade->array_options['options_ref_contrato'],'note' => $objcontade->note_private,'amount' => $objadden->aSuma['parcial_ttc'][$obja->fk_contrat_son]);
						if (!empty($contratAdd))$contratAdd.=', ';
						$contratAdd.= $objcontade->array_options['options_ref_contrato'];
					}
				}
				else
				{
					//recuperamos el valor de contrato
					foreach ($objcont->lines AS $olines)
					{
						$total_ht += $olines->total_ht;
						$total_tva += $olines->total_tva;
						$total_localtax1 += $olines->total_localtax1;
						$total_localtax2 += $olines->total_localtax2;
						$total_ttc += $olines->total_ttc;
					}
				}
			}
			else
			{
				//recuperamos el valor de contrato
				foreach ($objcont->lines AS $olines)
				{
					$total_ht += $olines->total_ht;
					$total_tva += $olines->total_tva;
					$total_localtax1 += $olines->total_localtax1;
					$total_localtax2 += $olines->total_localtax2;
					$total_ttc += $olines->total_ttc;
				}
			}
		}
		$nSumacont += $total_ttc;
		$aSumcontratpay[$i] = $total_ttc;
		$aContratcode[$i] = $contratAdd;

		$objsoc->fetch($objcont->fk_soc);
		$aSocname[$objcont->fk_soc] = $objsoc->nom;
		$aSoclabel[$i] = $objsoc->nom;

		$objcom->get_sum_pcp2($id,$i);
		$total_ttc = $objcom->total;

		if ($objcont->array_options['options_order_proced'] || count($objdev->array)>0)
		{
			//print '<section class="col-lg-6">';
		}
		print '<!-- Apply any bg-* class to to the info-box to color it -->';
		print '<div class="info-box bg-green">';
		print '<span class="info-box-icon">';
		if ($objpcon->statut > 0)
			print '<i class="fa fa-check-square"></i>';
		else
			print '<i class="fa fa-comments-o"></i>';
		print '</span>';

		print '<div class="info-box-content">';
		print '<span class="info-box-text">';
		print '<button class="btn btn-primary btn-lg bg-green" href="#'.$tagid.'" role="button" data-toggle="modal">'.(!empty($aContratname[$i])?$aContratname[$i]:$obj->ref).'</button>';
		print ' <a class="btn btn-primary btn-sm bg-green" href="'.DOL_URL_ROOT.'/contrat/fiche.php?id='.$i.'" title="'.$langs->trans('Contrat').'" target="blank_">'.$objsoc->nom.'</a>';
		print ' '.$langs->trans('Date').' '.dol_print_date($datecontrat,'day');
		print '</span>';

		//print '<span class="info-box-text">'.$objsoc->nom.'</span>';
		print '<span class="info-box-number">'.price($total_ttc).'</span>';

		print '<!-- The progress section is optional -->';
		print '<div class="progress">';
		$progressactprev = 0;
		if ($objproc->amount>0)
			$progressactprev = $total_ttc / $objproc->amount * 100;
		print '<div class="progress-bar" style="width: '.$progressactprev.'%"></div>';
		print '</div>';

		print '<span class="progress-description">';
		print price2num($progressactprev,'MT').'% '.$langs->trans('The').' '.price($objproc->amount);
		print '</span>';
		//si existe designaciones o pagos listamos
		if ($objcont->array_options['options_order_proced'] || count($objdev->array)>0)
		{
			//print '<div class="col-xs-6">';
			//insertamos las designaciones
		//	include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/desigbox.tpl.php';
			//print '</div>';
			//print '<div class="col-xs-6">';
			//pagos
		//	include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/paybox.tpl.php';
			//print '</div>';
		}

		print '</div><!-- /.info-box-content -->';
		print '</div><!-- /.info-box -->';


		$sumacont[$i] += $total_ttc;

			//agregamos tema de comprometido
		$objcom->get_sum_pcp2($id,$i);
		$total_ttc = $objcom->total;
		$sumacom[$i] += $total_ttc;
		//$action = 'selcon';
		//insertamos el formulario para cargar contratos
		include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/fiche_contrat_mod.tpl.php';

		//fin comprometido
	}
	//revisamos el contrato con el proceso

	if ($nSumacont >= $sumapre) $lAddcontrat = false;
	else $lAddcontrat = true;

		//se tiene que agregar para nuevo
	if ($lAddcontrat)
	{
			//print '<li>';
			//print '<div class="timeline-item">';
			//print '<div class="box box-solid bg-green">';
			//print '<h3>'.$langs->trans('Newcontrat').'&nbsp;';
			//print '</h3>';

			//link para crear uno nuevo
		if ($user->rights->poa->comp->crear )
		{
			if ($user->admin || ($user->id == $object->fk_user_create && $objact->statut > 0 && $objact->statut != 9))
			{
				//$action = 'create';
				print '<!-- Apply any bg-* class to to the info-box to color it -->';
				print '<div class="info-box bg-green">';
				print '<span class="info-box-icon"><i class="fa fa-cog fa-spin fa-1x fa-fw"></i>';
				print '</span>';
				print '<div class="info-box-content">';
				print '<span class="info-box-text">';
				print '<button class="btn btn-primary btn-lg bg-green" href="#fichecontrat" role="button" data-toggle="modal">'.$langs->trans('Newcontrat').'</button>';
				print '</span>';
				print '</div>';
				print '</div>';
					//insertamos el formulario para cargar contratos
				include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/fiche_contrat.tpl.php';
			}
			else
				echo 'noentra ';
		}
	}
}
?>