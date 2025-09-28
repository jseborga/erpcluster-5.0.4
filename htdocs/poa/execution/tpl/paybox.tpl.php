<?php
/* impresion de pagos
*/
print '<div class="box-body">';
print '<div class="direct-chat-messages">';
foreach((array) $aContrat AS $i => $ni)
{
	$nSumcontrat = $aSumcontratpay[$i];
	$nSumapay = 0;
	$lNewpay = true;
	$objcont->fetch($i);
	$res=$objcont->fetch_optionals($i,$extralabels);
	$lAdvance = false;
	if ($objcont->array_options['options_advance']) $lAdvance = true;

	$objdev->getlist2($object->id,$i);
	if (count($objdev->array)>0)
	{
		print '<div class="direct-chat-msg">';
		print '<div class="direct-chat-info clearfix">';
		print '<span class="direct-chat-name pull-left">';
		print ' '.$aContratname[$i].' - '.$aSoclabel[$i].'</span>';
		print '<span class="direct-chat-timestamp pull-right">'.dol_print_date($objd->date_dev,'day').'</span>';
		print '</div>';
			 //direct-chat-info

		foreach ((array) $objdev->array AS $j=> $objd)
		{
			$tagid = 'fichepay'.$objd->id;
			$iddev = $objd->id;
			//datos del pago
			//verificamos si es anticipo
			print '<div class="direct-chat-text">';
			print '<button class="btn btn-default btn-lg" href="#'.$tagid.'" role="button" data-toggle="modal">'.$objd->nro_dev.'</button>';
			if ($lAdvance)
			{
				if(empty($objd->invoice))
				{
					print ' <span><i>'.$langs->trans('Advance').':</i> '.$langs->trans('Amount').':</i> '.price($objd->amount).'</span>';
				}
				else
				{
					print ' <span><i>'.$langs->trans('Invoice').':</i> '.$objd->invoice.' <i>'.$langs->trans('Amount').':</i> '.price($objd->amount).'</span>';
					$nSumapay += $objd->amount;
				}
			}
			else
			{
				print ' <span><i>'.$langs->trans('Invoice').':</i> '.$objd->invoice.' <i>'.$langs->trans('Amount').':</i> '.price($objd->amount).'</span>';
				$nSumapay += $objd->amount;
			}
			print '</div>';
			//incluimos el registro para ver
			//$action = '';
			//include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/fiche_payment.tpl.php';

		}
		//agregamos para realizar el pago
		if ($user->rights->poa->deve->crear && $nSumapay < $nSumcontrat)
		{
			$tagid = 'fichepay0_'.$i;
			print '<div class="direct-chat-text">';
			print '<button class="btn btn-default btn-lg" href="#'.$tagid.'" role="button" data-toggle="modal">'.$langs->trans('New').'</button>';
			print '</div>';
		}

		print '</div>';
/*		if ($user->rights->poa->dev->crear && $action == 'createpay')
		{
			if ($user->admin || $objact->statut>0 && $objact->statut < 9)
			{
				$action = create;
				include DOL_DOCUMENT_ROOT.'/poa/appoint/tpl/fiche.tpl.php';
			}
		}
		*/
	}
	else
	{
		//agregamos para realizar el pago
		if ($user->rights->poa->deve->crear && $nSumapay < $nSumcontrat)
		{
			$tagid = 'fichepay0_'.$i;
			print '<div class="direct-chat-msg">';
			print '<div class="direct-chat-info clearfix">';
			print '<span class="direct-chat-name pull-left">';
			print ' '.$aContratname[$i].' - '.$aSoclabel[$i].'</span>';
			print '<span class="direct-chat-timestamp pull-right">'.dol_print_date($objd->date_dev,'day').'</span>';
			print '</div>';
			print '<div class="direct-chat-text">';
			print '<button class="btn btn-default btn-lg" href="#'.$tagid.'" role="button" data-toggle="modal">'.$langs->trans('New').'</button>';
			print '</div>';
			print '</div>';
		}
	}
}
print '</div>';
print '</div>';

?>