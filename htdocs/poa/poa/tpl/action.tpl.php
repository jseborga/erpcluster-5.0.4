<?php
  //boton action
switch ($nAction)
{
	case 1:
	print '<th>';
	if ($numCol[190]==true)
	{
		print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="200">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/left.png','',1).'</button>';
		print '&nbsp;';
		print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="191">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/right.png','',1).'</button>';
	}
	if ($numCol[191]==true)
	{
		print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="190">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/left.png','',1).'</button>';
		print '&nbsp;';
		print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="192">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/right.png','',1).'</button>';
	}
	if ($numCol[192]==true)
	{
		print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="191">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/left.png','',1).'</button>';
		print '&nbsp;';
		print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="193">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/right.png','',1).'</button>';
	}
	if ($numCol[193]==true)
	{
		print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="192">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/left.png','',1).'</button>';
		print '&nbsp;';
		print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="194">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/right.png','',1).'</button>';
	}
	if ($numCol[194]==true)
	{
		print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="193">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/left.png','',1).'</button>';
		print '&nbsp;';
		print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="195">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/right.png','',1).'</button>';
	}
	if ($numCol[195]==true)
	{
		print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="194">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/left.png','',1).'</button>';
		print '&nbsp;';
		print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="196">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/right.png','',1).'</button>';
	}
	if ($numCol[196]==true)
	{
		print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="195">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/left.png','',1).'</button>';
		print '&nbsp;';
		print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="197">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/right.png','',1).'</button>';
	}
	if ($numCol[197]==true)
	{
		print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="196">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/left.png','',1).'</button>';
		print '&nbsp;';
		print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="198">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/right.png','',1).'</button>';
	}
	if ($numCol[198]==true)
	{
		print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="197">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/left.png','',1).'</button>';
		print '&nbsp;';
		print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="199">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/right.png','',1).'</button>';
	}
	if ($numCol[199]==true)
	{
		print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="198">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/left.png','',1).'</button>';
		print '&nbsp;';
		print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="200">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/right.png','',1).'</button>';
	}
	if ($numCol[200]==true)
	{
		print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="199">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/left.png','',1).'</button>';
		print '&nbsp;';
		print '<button class="btn_trans" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="190">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/right.png','',1).'</button>';
	}
	print '</th>';
	break;
	case 2:
	print '<th>';
	if ($numCol[190]==true) print $langs->trans('Action');
	if ($numCol[191]==true) print $langs->trans('Trab.1');
	if ($numCol[192]==true) print $langs->trans('Trab.2');
	if ($numCol[193]==true) print $langs->trans('Trab.3');
	if ($numCol[194]==true) print $langs->trans('Trab.4');
	if ($numCol[195]==true) print $langs->trans('Trab.5');
	if ($numCol[196]==true) print $langs->trans('Trab.6');
	if ($numCol[197]==true) print $langs->trans('Trab.7');
	if ($numCol[198]==true) print $langs->trans('Trab.8');
	if ($numCol[199]==true) print $langs->trans('Trab.9');
	if ($numCol[200]==true) print $langs->trans('Priority');
	print '</th>';
	break;
	case 3:
	print '<th>';
	print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
	print '&nbsp;';
	print '<input class="liste_titre" type="image" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Searchclear")).'" name="nosearch" title="'.dol_escape_htmltag($langs->trans("Searchclear")).'">';
	print '</th>';
	break;
	case 4:
	$lisPrev.= '<td '.$newClase.'>';
	if ($numCol[191]==true) $lisPrev = viewwork(1,$user->id,$objppl,$objactwork,$lisPrev);
	if ($numCol[192]==true) $lisPrev = viewwork(2,$user->id,$objppl,$objactwork,$lisPrev);
	if ($numCol[193]==true) $lisPrev = viewwork(3,$user->id,$objppl,$objactwork,$lisPrev);
	if ($numCol[194]==true) $lisPrev = viewwork(4,$user->id,$objppl,$objactwork,$lisPrev);
	if ($numCol[195]==true) $lisPrev = viewwork(5,$user->id,$objppl,$objactwork,$lisPrev);
	if ($numCol[196]==true) $lisPrev = viewwork(6,$user->id,$objppl,$objactwork,$lisPrev);
	if ($numCol[197]==true) $lisPrev = viewwork(7,$user->id,$objppl,$objactwork,$lisPrev);
	if ($numCol[198]==true) $lisPrev = viewwork(8,$user->id,$objppl,$objactwork,$lisPrev);
	if ($numCol[199]==true) $lisPrev = viewwork(9,$user->id,$objppl,$objactwork,$lisPrev);
	if ($numCol[200]==true) $lisPrev = viewpriority($user->id,$objppl,$lisPrev);
	$lisPrev.= '</td>';
	break;
}


?>