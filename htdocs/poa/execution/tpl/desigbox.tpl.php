<?php
/* impresion de preventivo
*/
if ($resprev>0)
{
	print '<div class="box-body">';
	print '<div class="direct-chat-messages">';

	$objapp  = new Poacontratappoint($db);
	foreach((array) $aContrat AS $i => $ni)
	{
		if ($aContratvalid[$i])
		{
			$objapp->getlist($i);
			if (count($objapp->array)>0)
			{
				print '<div class="direct-chat-msg">';
				print '<div class="direct-chat-info clearfix">';
				print '<span class="direct-chat-name pull-left">';
				print ' '.$aContratname[$i].' - '.$aSoclabel[$i].'</span>';
				print '<span class="direct-chat-timestamp pull-right">'.dol_print_date($objd->date_dev,'day').'</span>';
				print '</div>';

				foreach ((array) $objapp->array AS $j=> $objg)
				{
					//print '<div class="callout callout-info">';
					$tagid = 'ficheappoint'.$objg->id;
					$idapp = $objg->id;
					$res = $objuser->fetch($objg->fk_user);

					print '<div class="direct-chat-text">';
					print '<button class="btn btn-default btn-lg" href="#'.$tagid.'" role="button" data-toggle="modal">'.$objuser->lastname.' '.$objuser->firstname.'</button>';
					print ' <span><i>'.$langs->trans('Date').':</i> '.dol_print_date($objg->date_appoint,'day').'</span>';
					print '<span>'.select_code_appoint($objg->code_appoint,'code_appoint','',0,1).'</span>';
					print '</div>';
					//incluimos el registro para ver
					$action = '';
					include DOL_DOCUMENT_ROOT.'/poa/appoint/tpl/fiche_appoint.tpl.php';
				}
				if ($objact->statut < 9 && $user->rights->poa->appoint->crear && $action == 'createdesign')
				{
					if ($user->admin || $objact->statut>0 && $objact->statut < 9)
					{
						//	$action = create;
						//	include DOL_DOCUMENT_ROOT.'/poa/appoint/tpl/fiche.tpl.php';
						print '<div class="callout callout-info">';
						print '<span> ';
						print '<button class="btn btn-default btn-lg" href="#designew" role="button" data-toggle="modal">'.$langs->trans('New').'</button>';
						print '</span>';
						print '</div>';
						$action = 'create';
						$tagid = 'designew';
						include DOL_DOCUMENT_ROOT.'/poa/appoint/tpl/fiche_appoint.tpl.php';

					}
				}
				print '</div>';
			}

		}
	}
	if ($objact->statut < 9 && $user->rights->poa->appoint->crear && $abc)
	{
		print '<div class="callout callout-info">';
		print '<span> ';
		print '<button class="btn btn-default btn-lg" href="#designew" role="button" data-toggle="modal">'.$langs->trans('New').'</button>';
		print '</span>';
		print '</div>';
		$action = 'create';
		$tagid = 'designew';
		include DOL_DOCUMENT_ROOT.'/poa/appoint/tpl/fiche_appoint.tpl.php';
	}
	print '</div>';
	print '</div>';

}
?>