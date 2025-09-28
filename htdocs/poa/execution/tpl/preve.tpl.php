<?php
/* impresion de preventivo
*/
	if ($resprev>0)
	{
		print '<li class="time-label">';
		print '<span class="bg-red">'.dol_print_date($object->date_preventive,'day') .'</span>';
		print '</li>';
		print '<li>';
		print '<i class="fa fa-arrow-circle-down -blue"></i>';
		print '<div class="timeline-item" >';
		print '<div class="box box-solid bg-maroon">';
		print '<div class="inner">';
		print '<h3 class="box-title">'.$langs->trans('Preventive').'</h3>';

		print '<table class="table">';
		//buscamos la suma del preventivo
		$totalp = $objpre->getsum($id);
		print '<tr>';
		print '<td>';
		//print '<a class="btn btn-primary btn-sm bg-maroon" href="'.DOL_URL_ROOT.'/poa/execution/fiche.php?id='.$object->id.'&dol_hide_leftmenu=1">'.$object->nro_preventive.'/'.$object->gestion.'</a>';
		print $object->label;
		print '<button class="btn btn-primary btn-lg" href="#fichepreventive" role="button" data-toggle="modal">'.$object->nro_preventive.'/'.$object->gestion.'</button>';


		print '</td>';
		//adjunto
		print '<td>';
		$dir = $conf->poa->dir_output."/execution/pdf/".$id.'.pdf';
		$url = DOL_URL_ROOT.'/documents/poa'."/execution/pdf/".$id.'.pdf';
		if ($user->rights->poa->prev->mod)
			if ($action != 'upload')
			{
				print '<a href="'.$_SERVER['PHP_SELF'].'?ida='.$ida.'&id='.$id.'&action=upload'.'">'.img_picto($langs->trans('Uploaddoc'),DOL_URL_ROOT.'/poa/img/subir.png','',1).'</a>';
				//mostramos el archivo
				if (file_exists($dir))
				{
					print '&nbsp;&nbsp;';
					print '<a href="'.$url.'" target="_blank">'.img_picto($langs->trans('PDF'),'pdf2').'</a>';
				}
			}
			else
			{
				if ((GETPOST('id') == $object->id))
				{
					$linklast = $_SERVER['PHP_SELF'].'?ida='.$ida;
					$idreg = $id; //preventivo
					$actionvalue = 'uppdfprev';
					include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/uppdf.tpl.php';
				}
			}
		print '</td>';
		print '<td align="right">'.price($totalp).'</td>';
		//print '<td align="left">'.$langs->trans($object->getLibStatut(0)).'</td>';
		print '<td align="left">'.$objuser->login.'</td>';
		print '</tr>';

		$sumapre += $totalp;
		//verificamos si tiene hijos
		$objprevh = new Poaprev($db);
		$objprevh->getlistfather($id);
		foreach ((array) $objprevh->arrayf AS $j => $objp)
		{
			$dir = $conf->poa->dir_output."/execution/pdf/".$objp->id.'.pdf';
			$url = DOL_URL_ROOT.'/documents/poa'."/execution/pdf/".$objp->id.'.pdf';
			print '<tr>';
			print '<td>'.'<a class="btn btn-primary btn-sm bg-maroon" href="'.DOL_URL_ROOT.'/poa/execution/fiche.php?id='.$objp->id.'&dol_hide_leftmenu=1">'.$objp->nro_preventive.'/'.$objp->gestion.'</a>';
			print '&nbsp;';
			print $objp->label.'</td>';
			print '<td>';

			if ($user->rights->poa->prev->mod)
			{
				if ($action == 'upload' && (GETPOST('idreg_') == $objp->id))
				{
					$linklast = $_SERVER['PHP_SELF'].'?ida='.$ida;
					$idreg = $objp->id; //preventivo
					$actionvalue = 'uppdfprev';
					include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/uppdf.tpl.php';
				}
				if ($action != 'upload')
				{
					print '<a href="'.$_SERVER['PHP_SELF'].'?ida='.$ida.'&idreg_='.$objp->id.'&action=upload'.'">'.img_picto($langs->trans('Uploaddoc'),DOL_URL_ROOT.'/poa/img/subir.png','',1).'</a>';
					//mostramos el archivo
					if (file_exists($dir))
					{
						print '&nbsp;&nbsp;';
						print '<a href="'.$url.'" target="_blank">'.img_picto($langs->trans('PDF'),'pdf2').'</a>';
					}
				}
			}
			print '</td>';
			print '<td align="right">'.price($objp->amount).'</td>';
			print '<td></td>';
			print '</tr>';
			$sumapre += $objp->amount;
		}
		//total
		print '<tr>';
		print '<td>';
		print $langs->trans('Total');
		print '</td>';
		print '<td>';
		print '</td>';
		print '<td align="right">';
		print price($sumapre);
		print '</td>';
		print '<td align="right">';
		print '</td>';
		print '</tr>';
		print '</table>';

		print '</div>';
		print '</div>';
		print '</div>';
		print '</li>';

		//insertamos el formulario para cargar contratos
		include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/fiche_preventive.tpl.php';
	}
?>