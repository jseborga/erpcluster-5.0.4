<?php
//estado
print '<thead>';
print '<tr role="row">';

//print '<th>';
//print '<br/>';
//print $langs->trans('E');
//print '</th>';
//structure
print '<th>';
print '<br/>';
print $langs->trans("Meta");
//print '<br/>';
//print '<input type="text" class="flat" size="3" name="search_sigla" value="'.$search_sigla.'">';
print '</th>';

if ($numCol[1]==true)
{
	print '<th>';
	print '<button class="btn_trans" form="fo3b" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="2">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';
	print '<br/>';
	print $langs->trans("Label");
//    print '<br/>';
//    print '<input type="text" class="flat" name="search_label" value="'.$search_label.'" size="10">';
	print '</th>';
}
if ($numCol[2]==true)
{
	print '<th class="yellowblack">';
	print '<button class="btn_trans" form="fo3b" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="1">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/sal.png','',1).'</button>';
	print '<br/>';
	print $langs->trans("Pseudonym");
//    print '<br/>';
//    print '<input type="text" class="flat" name="search_pseudonym" value="'.$search_pseudonym.'" size="10">';

	print '</th>';
}
//partida
print '<th>';
if ($numCol[61])
	print '<button class="btn_trans" form="fo3b" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="62">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';
if ($numCol[62])
	print '<button class="btn_trans" form="fo3b" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="61">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';
print '<br/>';
print $langs->trans('Partida');
//print '<br/>';
//print '<input class="flat" type="text" size="3" name="search_partida" value="'.$search_partida.'">';
print '</th>';

//presupuesto
print '<th>';//presup
if ($numCol[91])
{
	print '<button class="btn_trans" form="fo3b" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="92">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';
	print '<br/>';
	print $langs->trans('Initialbudget');
}
if ($numCol[92])
{
	print '<button class="btn_trans" form="fo3b" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="91">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';
	print '<br/>';
	print $langs->trans('Approved budget');
}

//botones calendario
//7 total presup
if ($numCol[7])
{
	print '<button class="btn_trans" form="fo3b" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="8">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/sal.png','',1).'</button>';
	print '<br/>';
	print $langs->trans('A1');
}
if ($numCol[8])
{
//  print '<button class="btn_trans" form="fo3b" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="7">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/por.png','',1).'</button>';
//  print '<br/>';
//  print $langs->trans('A2');
}


print '</th>';//presup fin

//reformulacion y total aprobado

if ($numCol[71])
{
	print '<th class="title yellowblack">';
	print '<button class="btn_trans" form="fo3b" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="72">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';
	print '<br/>';
	if ($lVersion)
	{
		print $langs->trans('Reformulated').' '.$nVersion;
	}
	else
	{
		print $langs->trans('Reformulated').' '.$nVersion;
	}
	print '</th>';
	if ($lVersion)
	{
		print '<th class="yellowblack">';
		print $langs->trans('N. Reform');
		print '</th>';
	}
	else
	{
		print '<th class="yellowblack">';
		print $langs->trans('N. Reform');
		print '</th>';
	}

}
if ($numCol[72])
{
	print '<th>';
	print '<button class="btn_trans" form="fo3b" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="73">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';
	print '<br/>';
	if ($numCol[7]==true)
	{
		print $langs->trans("Pending approval").' '.$nVersion;
	}
	else
	{
		print $langs->trans("Approved budget").' '.$nVersion;
	}
	print '</th>';
}
if ($numCol[73])
{
	print '<th>';
	print '<button class="btn_trans" form="fo3b" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="71">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';
	print '<br/>';
	print $langs->trans("Porcent");
	print '</th>';
}

if ($numCol[9]==true)
{
	print '<th>';
	print '<button class="btn_trans" form="fo3b" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="10">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';
	print '<br/>';
	print $langs->trans("Prev.");
	print '</th>';
}
if ($numCol[10]==true)
{
	print '<th>';
	print '<button class="btn_trans" form="fo3b" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="15">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/por.png','',1).'</button>';
	print '<br/>';
	print $langs->trans("Prev.").'<br>'.'%';
	print '</th>';
}
if ($numCol[15]==true)
{
	print '<th>';
	print '<button class="btn_trans" form="fo3b" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="9">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/sal.png','',1).'</button>';
	print '<br/>';
	print $langs->trans("Balance");
	print '</th>';
}
if ($numCol[11]==true)
{
	print '<th>';
	print '<button class="btn_trans" form="fo3b" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="12">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';
	print '<br/>';
	print $langs->trans("Comm.");
	print '</th>';
}
if ($numCol[12]==true)
{
	print '<th>';
	print '<button class="btn_trans" form="fo3b" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="16">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/por.png','',1).'</button>';
	print '<br/>';
	print $langs->trans("Comm.").'<br>'.'%';
	print '</th>';
}
if ($numCol[16]==true)
{
	print '<th>';
	print '<button class="btn_trans" form="fo3b" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="11">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/sal.png','',1).'</button>';
	print '<br/>';
	print $langs->trans("Balancecommitted");
	print '</th>';
}

if ($numCol[13]==true)
{
	print '<th>';
	print '<button class="btn_trans" form="fo3b" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="14">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';
	print '<br/>';
	print $langs->trans("Accr.");
	print '</th>';
}
if ($numCol[14]==true)
{
	print '<th>';
	print '<button class="btn_trans" form="fo3b" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="17">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/por.png','',1).'</button>';
	print '<br/>';
	print $langs->trans("Accr.").'<br>'.'%';
	print '</th>';
}
if ($numCol[17]==true)
{
	print '<th>';
	print '<button class="btn_trans" form="fo3b" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="13">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/sal.png','',1).'</button>';
	print '<br/>';
	print $langs->trans("Balanceaccrued");
	print '</th>';
}
if ($opver == true && !$lMobile)
{
	$aMes = array(1=>'En',2=>'Fe',3=>'Ma',4=>'Ap',5=>'My',6=>'Ju',7=>'Jl',8=>'Au',9=>'Se',10=>'Oc',11=>'No',12=>'De');
	$textclass = 'classmes';
	for($nMes =1; $nMes <= 12; $nMes++)
	{
		$tmes = $aMes[$nMes];
		if ($nMes == $monActual)
			print '<th class="markmes">'.$langs->trans($tmes).'</th>';
		else
			print '<th>'.$langs->trans($tmes).'</th>';
	}
}

//user
print '<th>';
if ($numCol[51])
{
	print '<button class="btn_trans" form="fo3b" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="52">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';
}
if ($numCol[52])
{
	print '<button class="btn_trans" form="fo3b" title="'.$langs->trans('Interchange').'" type="submit" name="vercol" value="51">'.img_picto($langs->trans('valor'),DOL_URL_ROOT.'/poa/img/val.png','',1).'</button>';
}
print '</th>';

//seguimiento
if ($numCol[321])
	print '<th>'.$langs->trans('Date').'</th>';
if ($numCol[322])
	print '<th>'.$langs->trans('Seguimiento').'</th>';
if ($numCol[323])
	print '<th>'.$langs->trans('Acciones a seguir').'</th>';

//instruction
if ($conf->poai->enabled)
{
	// if ($numCol[93])
	//   print '<div id="instruction" class="left title"></div>';
}
	//action
$nAction = 1;
include DOL_DOCUMENT_ROOT.'/poa/poa/tpl/action.tpl.php';
print '</tr>';
print '</thead>';
?>