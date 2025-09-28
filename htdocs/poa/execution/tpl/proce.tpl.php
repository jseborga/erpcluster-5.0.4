<?php
/* impresion de proceso
*/
	//process
print '<li class="time-label">';
print '<span class="bg-yellow">'.dol_print_date(($objproc->id>0?$objproc->date_process:dol_now()),'day') .'</span>';
print '</li>';

print '<li>';
print '<div class="timeline-item">';
print '<div class="box box-solid bg-yellow">';
print '<h3>'.$langs->trans('Inicio Proceso').'</h3>';
print '<div class="inner">';

if ($objproc->id)
{
	print '<table class="table">';
	print '<tr>';
	print '<td>';
	print $langs->trans('Nro. Process');
	if ($lForm)
	{
		if ($objproc->id > 0 && $objproc->statut >=1)
		{
			print '&nbsp;<a class="btn btn-primary btn-sm bg-yellow" href="'.DOL_URL_ROOT.'/poa/process/fiche_iniproc.php?id='.$idProcess.'&dol_hide_leftmenu=1" title="'.$langs->trans('Excel').'">';
			print '&nbsp;'.img_picto($langs->trans('Exportexcel'),DOL_URL_ROOT.'/poa/img/excel-icon','',true);
			print '</a>';
		}
	}
	else
	{
		print '&nbsp;<a class="btn btn-primary btn-sm bg-yellow" href="'.DOL_URL_ROOT.'/poa/process/fiche_iniproc_20150901.php?id='.$idProcess.'&dol_hide_leftmenu=1" title="'.$langs->trans('Excel').'">';
		print '&nbsp;'.img_picto($langs->trans('Exportexcel'),DOL_URL_ROOT.'/poa/img/excel-icon','',true);
		print '</a>';
	}
	print '</td>';
		//adjunto
	print '<td>';
	$dir = $conf->poa->dir_output."/process/pdf/".$objproc->id.'.pdf';
	$url = DOL_URL_ROOT.'/documents/poa'."/process/pdf/".$objproc->id.'.pdf';
	if ($user->rights->poa->prev->mod)
		if ($action != 'uploadproc')
		{
			print '<a href="'.$_SERVER['PHP_SELF'].'?ida='.$ida.'&id='.$id.'&action=uploadproc'.'">'.img_picto($langs->trans('Uploaddoc'),DOL_URL_ROOT.'/poa/img/subir.png','',1).'</a>';
				//mostramos el archivo
			if (file_exists($dir))
			{
				print '&nbsp;&nbsp;';
				print '<a href="'.$url.'" target="_blank">'.img_picto($langs->trans('PDF'),'pdf2').'</a>';
			}
		}
		else
		{
			$linklast = $_SERVER['PHP_SELF'].'?ida='.$ida;
			$idreg = $objproc->id;
			$actionvalue = 'uppdfprocess';
			include DOL_DOCUMENT_ROOT.'/poa/execution/tpl/uppdf.tpl.php';
		}

	print '</td>';
	print '<td align="right">';
	print '<button class="btn btn-primary btn-lg" href="#ficheprocess" role="button" data-toggle="modal">'.$objproc->ref.'</button>';
	print '</td>';
	print '</tr>';
	print '</table>';
}
else
{
	//link para crear uno nuevo
	print '<button class="btn btn-primary btn-lg" href="#ficheprocess" role="button" data-toggle="modal">'.$langs->trans('Nuevo').'</button>';
}
print '</div>';//inner
print '</div>';
print '</div>';

include DOL_DOCUMENT_ROOT.'/poa/process/tpl/fiche_process.tpl.php';

print '</li>';

?>