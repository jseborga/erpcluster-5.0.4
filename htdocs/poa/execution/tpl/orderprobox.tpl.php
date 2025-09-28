<?php

//Contrato
/////////////////////////////
if ($idProcess > 0)
{
	$a = true;
	$lAddcontrat = false;
	$lAdvance = false;
	$sumacont = array();
	$nSumacont = 0;
	$aContratpay = array();
	if (count($aContrat) <= 0)
	{
		if ($objproc->statut > 0) $lAddcontrat = true;
	}
	print '<div class="box-body">';
	print '<div class="direct-chat-messages">';

	foreach((array) $aContrat AS $i => $ni)
	{
		$idrcreg = $aProcesscontrat[$i];
		$objpcon->fetch($idrcreg);
		if ($objpcon->statut == 1)
		{
			print '<div class="direct-chat-msg">';
			print '<div class="direct-chat-info clearfix">';
			print '<span class="direct-chat-name pull-left">';
			print ' '.$aContratname[$i].' - '.$aSoclabel[$i].'</span>';
			print '<span class="direct-chat-timestamp pull-right">'.dol_print_date($objd->date_dev,'day').'</span>';
			print '</div>';

			$tagid = 'ficheorderpro'.$aProcesscontrat[$i];
			$tagidp = 'ficheorderprop'.$aProcesscontrat[$i];
			$tagidd = 'ficheorderprod'.$aProcesscontrat[$i];
			$objcont->fetch($i);
			$objcont->fetch_lines();

			//buscamos los pagos para este contrato
			//$objdev->getlist2($object->id,$i);

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
			if (!$objcont->array_options['options_order_proced'])
			{
				//necesita orden de proceder
				$aContratpay[$i] = true;
			}

			$aContratname[$i] = $objcont->array_options['options_ref_contrato'];
			$aContratlabel[$i] = $objcont->nom;

			if ($objcont->id == $i)
			{
				$cod_plazo = $objcont->array_options['options_cod_plazo'];
				$total_plazo = $objcont->array_options['options_plazo'];

				$datecontrat = $objcont->date_contrat;
				//buscamos si tiene addendum
				if ($conf->addendum->enabled)
				{
					$objadden = new Addendum($db);
					$res = $objadden->getlist($i);
					if ($res>0)
					{
						//verificamos los plazos adicionales
						foreach ((array) $objadden->array AS $j1 => $obja)
						{
							$objcontade = new Contrat($db);
							$objcontade->fetch($obja->fk_contrat_son);
							if ($objcontade->id == $obja->fk_contrat_son)
								$total_plazo += $objcontade->array_options['options_plazo'];
						}
					}
				}
			}
			//fecha orden de proceder
			if ($objcont->array_options['options_order_proced'])
			{
				//direct-chat-info
				print '<div class="direct-chat-text">';
				print ' <span>'.$langs->trans('Orderproceed').'</span>';
				print '<button class="btn btn-default btn-lg" href="#'.$tagid.'" role="button" data-toggle="modal">'.dol_print_date($objpcon->date_order_proceed,'day').'</button>';

				if ($objpcon->date_order_proceed > 0)
				{
					$datefinal = date_end((!empty($objpcon->date_order_proceed)?$objpcon->date_order_proceed:$datecontrat),$cod_plazo,$total_plazo);

					print ' <span>'.$langs->trans('Finish').'</span>';
					print ' '.dol_print_date($datefinal,'day');
				}
				print '</div>';
			}
			else
			{
				//la fecha orden de proceder es siguiente dia habil
				$aDatec = dol_getdate($datecontrat);
				$aDater = dol_get_next_day((strlen($aDatec['mday'])==1?'0'.$aDatec['mday']:$aDatec['mday']), (strlen($aDatec['mon'])==1?'0'.$aDatec['mon']:$aDatec['mon']), $aDatec['year']);
				$lLoop = true;
				while ($lLoop == true)
				{
					$string = $aDater['year'].(strlen($aDater['month'])==1?'0'.$aDater['month']:$aDater['month']).(strlen($aDater['day'])==1?'0'.$aDater['day']:$aDater['day']).'120000';
					$date_order_proceed = dol_stringtotime($string,1);
					$aDater = dol_getdate($date_order_proceed);
					if ($aDater['wday'] == 6 || $aDater['wday'] == 0)
						$lLoop = true;
					else
						$lLoop = false;
				}
				//actualizamos la fecha de orden de proceder
				if (empty($objpcon->date_order_proceed))
				{
					$objpcon->date_order_proceed = $date_order_proceed;
					$objpcon->update($user);
				}
				print '<div class="direct-chat-text">';
				print ' <span>'.$langs->trans('Orderproceed').':</span> ';
				print dol_print_date($date_order_proceed,'day');
				print '</div>';
			}
			//acta recepcion provisional
			if ($objcont->array_options['options_order_proced'])
			{
				if ($objpcon->date_order_proceed > 0)
				{
					print '<div class="direct-chat-text">';
					print ' <span>'.$langs->trans('Provisional').'</span> ';
					if ($user->rights->poa->op->crear || $user->rights->poa->op->mod)
						print '<button class="btn btn-default btn-lg" href="#'.$tagidp.'" role="button" data-toggle="modal">'.dol_print_date($objpcon->date_provisional,'day').'</button>';
					else
						print dol_print_date($objpcon->date_provisional,'day');
					print '</div>';
					//acta recepcion definitiva
					if ($objpcon->date_provisional > 0)
					{
						print '<div class="direct-chat-text">';
						print ' <span>'.$langs->trans('Definitive').'</span> ';
						if ($user->rights->poa->op->rp)
							print '<button class="btn btn-default btn-lg" href="#'.$tagidd.'" role="button" data-toggle="modal">'.dol_print_date($objpcon->date_final,'day').'</button>';
						else
							print dol_print_date($objpcon->date_final,'day');
						print '</div>';
					}
				}

				print '</div>';
			}
			else
			{
				//la orden de inicio es siguiente dia habil de la firma de contrato
				print '<div class="direct-chat-text">';
				print ' <span>'.$langs->trans('Definitiva').'</span> ';
				if ($user->rights->poa->op->rd)
					print '<button class="btn btn-default btn-lg" href="#'.$tagidd.'" role="button" data-toggle="modal">'.(!empty($objpcon->date_final)?dol_print_date($objpcon->date_final,'day'):$langs->trans('New')).'</button>';
				else
					print dol_print_date($objpcon->date_final,'day');
				print '</div>';
			}
			print '</div>';
		}
	}
	print '</div>';
	print '</div>';
}
?>