<?php


	//arreglamos los datos
	//buscamos la seleccion
$ref = $object->ref;
$res = $objStocktemp->getlist($object->ref,"1,2");
$aListe = array();
$cLabel = '';
$lStatut = true;
$fk_type_mov = $object->fk_type_mov;

$newReporth[$id]['ref'] = $object->ref;
$newReporth[$id]['date'] = $object->datem;
$newReporth[$id]['label'] = $object->label;
foreach ((array) $objStocktemp->array AS $i => $obj)
{
	if (empty($fk_type_mov)) $fk_type_mov = $obj->fk_type_mov+0;
	if ($obj->statut > 1) $lStatut = false;

	if ($obj->type_mouvement == 0)
	{

		$aListef[$obj->ref][$obj->fk_product]['from'] = array('id'=>$obj->id,'fk_entrepot'=>$obj->fk_entrepot,'value'=>$obj->value,'quant'=>$obj->quant,'price'=> $obj->price);
		$aListeentrepot[$obj->fk_entrepot][$obj->id] = $obj->id;
	}
	if ($obj->type_mouvement == 1)
	{
		$aListet[$obj->ref][$obj->fk_product]['to'] = array('id'=>$obj->id,'fk_entrepot'=>$obj->fk_entrepot,'value'=>$obj->value,'quant'=>$obj->quant);
		$aListeentrepot[$obj->fk_entrepot][$obj->id] = $obj->id;
	}
}
	//recuperamos el type_mouvement
$typemov = '';
$typemov = $objTypemov->type;

	//revisamos que tipo de transaccion es
	//$lTrans = 0 salida y entrada
	//$lTrans = 1 entrada
	//$lTrans = 2 salida
$lTrans = 0;
if (count($aListet[$ref])>0 && count($aListef[$ref])>0)
{
	$LTrans = 0;
	$aListeff = $aListef[$ref];
}
if ((count($aListef[$ref])<=0 || empty($aListef[$ref])) && count($aListet[$ref])>0)
{
	$lTrans = 2;
	$aListeff = $aListet[$ref];
}
if ((count($aListet[$ref])<=0 || empty($aListet[$ref])) && count($aListef[$ref])>0)
{
	$lTrans = 1;
	$aListeff = $aListef[$ref];
}

		//buscamos la seleccion
		//$res = $object->getlist($ref,"1,2");
$lOut = false;
if (count($aListef[$ref])<=0 || empty($aListef[$ref]))
{
	$lOut = true;
	$aListeff = $aListet[$ref];
}
else
	$aListeff = $aListef[$ref];

if ($action == 'editline' && $user->rights->almacen->transfin->mod)
{
	print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">'."\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="updateline">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	print '<input type="hidden" name="idr" value="'.$idr.'">';
}
elseif ($action == 'createline' && $user->rights->almacen->transfin->mod)
{
	print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">'."\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="addline">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	print '<input type="hidden" name="fk_type_mov" value="'.$fk_type_mov.'">';
}

dol_fiche_head();
print '<table class="border" width="100%">';
print "<tr class=\"liste_titre\">";
print '<td width="9%">'.$langs->trans("Product").'</td>';
print '<td >'.$langs->trans("Description").'</td>';

if ($object->fk_entrepot_from && !$object->fk_entrepot_to)
{
	print '<td width="10%">'.$langs->trans("Towharehouse").$lOut.'</td>';
}
elseif($object->fk_entrepot_from && $object->fk_entrepot_to)
{
	print '<td width="10%">'.$langs->trans("Fromwharehouse").'</td>';
	print '<td width="10%">'.$langs->trans("Towharehouse").'</td>';
}
elseif (!$object->fk_entrepot_from && $object->fk_entrepot_to)
{
	print '<td width="10%">'.$langs->trans("Fromwharehouse").'</td>';
}

if ($object->fk_entrepot_from && !$object->fk_entrepot_to)
{
	print '<td align="right" width="10%">'.$langs->trans("Enters").'</td>';
	if ($conf->global->ALMACEN_MOUVEMENT_INPUT_VALUE_FOR_PRODUCT)
		print '<td align="right" width="10%">'.$langs->trans("Total").'</td>';
	else
		print '<td align="right" width="10%">'.$langs->trans("P.U.").$lOut.'</td>';
}
elseif($object->fk_entrepot_from && $object->fk_entrepot_to)
{
	print '<td align="right" width="5%">'.$langs->trans("Sent").'</td>';
	print '<td align="right" width="5%">'.$langs->trans("Received").'</td>';
}
elseif (!$object->fk_entrepot_from && $object->fk_entrepot_to)
	print '<td align="right" width="5%">'.$langs->trans("Output").'</td>';

if ($user->rights->almacen->transfin->mod && $object->fk_entrepot_from && $object->fk_entrepot_to)
	print '<td align="center">'.$langs->trans("Action").'</td>';
print '</tr>';
$var = true;
if ($action == 'createline' && $user->rights->almacen->transfin->write)
{
	print "<tr $bc[$var]>";
	print '<td>';
	print $form->select_produits_v(GETPOST('idprod'),'idprod','',$conf->product->limit_size,0,-1,2,'',1,'','');
	print '</td>';
	print '<td>';
	print '<input type="text" style="border:none;" id="labelproduct" name="labelproduct" value="'.GETPOST('labelproduct').'" readonly>';
	print '<input type="text" style="border:none;" id="unit" name="unit" value="" readonly>';
	print '<input type="hidden" name="fk_entrepot_from" value="'.$object->fk_entrepot_from.'">';
	print '<input type="hidden" name="fk_entrepot_to" value="'.$object->fk_entrepot_to.'">';
	print '</td>';
	$entrepot->fetch($object->fk_entrepot_from);
	$entrepot_from = $entrepot->lieu;

	print '<td>'.$entrepot_from.'</td>';

	print '<td>';
	print '<input id="nbpiece" type="number" min="0" step="any" name="nbpiece" size="10" value="'.GETPOST('value').'" required>';
	print '</td>';
	print '<td>';
	print '<input id="price" type="number" min="0" step="any" name="price" value="'.GETPOST('price').'" required>';
	print '</td>';
	print '<td nowrap>';
	print '<input type="submit" name="submit" value="'.$langs->trans('Save').'">';
	print '&nbsp;<a class="button" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'">'.$langs->trans('Return').'</a>';
	print '</td>';
	print '</tr>';
}
$aReport = array();
$k = 0;
foreach ((array) $aListeff AS $fk_product => $aData)
{
	foreach ((array) $aData AS $type => $aValue)
	{
		//determinamos quien envia (si existe)
		$entrepot_to = '';
				//if (!$lOut)
				//{
		$fk_entrepott = $aListet[$ref][$fk_product]['to']['fk_entrepot'];
		$idto = $aListet[$ref][$fk_product]['to']['id'];
		if ($fk_entrepott>0)
		{
			$objEntrepot->fetch($fk_entrepott);
			$entrepot_to = $objEntrepot->lieu;
		}
				//}
				//quien recibe
		$objEntrepot->fetch($aValue['fk_entrepot']);
		$entrepot_from = $objEntrepot->lieu;
				//producto
		$objProduct->fetch($fk_product);
		$var=!$var;
		print "<tr $bc[$var]>";
		if ($action == 'editline' && $idr == $aValue['id'])
		{
			print '<td>';
			print $form->select_produits_v($fk_product,'idprod','',$conf->product->limit_size,0,-1,2,'',1,'','');
			//enviamos oculto el id to
			print '<input type="hidden" name="idto" value="'.$idto.'">';
			print '';
			print '</td>';
			print '<td>';
			print '<input type="text" style="border:none;" id="labelproduct" name="labelproduct" value="'.$objProduct->label.'" readonly>';
			print '<input type="text" style="border:none;" id="unit" name="unit" value="'.$objProduct->getLabelOfUnit('short').'" readonly>';
			print '</td>';
			if ($object->fk_entrepot_from && !$object->fk_entrepot_to)
				print '<td>'.$entrepot_from.'</td>';
			elseif($object->fk_entrepot_from && $object->fk_entrepot_to)
			{
				print '<td>'.$entrepot_to.'</td>';
				print '<td>'.$entrepot_from.'</td>';
			}
			elseif (!$object->fk_entrepot_from && $object->fk_entrepot_to)
			{
				print '<td>'.$entrepot_to.'</td>';
			}

			print '<td align="right">';
			print '<input id="nbpiece" type="number" min="0" step="any" name="nbpiece" size="10" value="'.$aValue['value'].'" required>';
			print '</td>';
			print '<td></td>';
			//if ($conf->global->ALMACEN_MOUVEMENT_INPUT_VALUE_FOR_PRODUCT)
			//	print '<td align="right" width="10%">'.'<input type="number" min="0" step="any" name="price" value="'.price2num($aValue['price']*$aValue['value'],'MT').'" required></td>';
			//else
			//	print '<td align="right" width="10%">'.'<input type="number" min="0" step="any" name="price" value="'.price2num($aValue['price'],'MU').'" required></td>';
		}
		else
		{
			print '<td>'.$objProduct->getNomUrl(1).'</td>';
			print '<td>'.$objProduct->label.' - '.$objProduct->getLabelOfUnit('short').'</td>';
			if ($object->fk_entrepot_from && !$object->fk_entrepot_to)
			{
				print '<td>'.$entrepot_from.'</td>';
			}
			elseif($object->fk_entrepot_from && $object->fk_entrepot_to)
			{
				print '<td>'.$entrepot_to.'</td>';
				print '<td>'.$entrepot_from.'</td>';
			}
			elseif (!$object->fk_entrepot_from && $object->fk_entrepot_to)
			{
				print '<td>'.$entrepot_to.'</td>';
			}

			if ($object->fk_entrepot_from && !$object->fk_entrepot_to)
			{
				if ($object->statut == 2)
				{
					print '<td align="right">'.$aValue['quant'].'</td>';
				}
				else
				{
					print '<td align="right">'.$aValue['value'].'</td>';
				}
				if ($conf->global->ALMACEN_MOUVEMENT_INPUT_VALUE_FOR_PRODUCT)
				{
					print '<td align="right" width="10%">'.price(price2num($aValue['value']*$aValue['price'],'MT')).'</td>';
				}
				else
				{
					print '<td align="right" width="10%">'.price(price2num($aValue['price'],'MU')).'</td>';
				}
			}
			elseif($object->fk_entrepot_from && $object->fk_entrepot_to)
			{
				print '<td align="right">'.$aValue['value'].'</td>';
				print '<td align="right">'.$aValue['quant'].'</td>';
			}
			elseif (!$object->fk_entrepot_from && $object->fk_entrepot_to)
			{
				print '<td align="right">'.$aValue['value'].'</td>';
			}

		}

		if ($user->rights->almacen->transfin->mod && $object->fk_entrepot_from && $object->fk_entrepot_to)
		{
			print '<td align="center" nowrap>';
			if ($action == 'editline' && $idr == $aValue['id'])
			{
				print '<input type="submit" name="submit" value="'.$langs->trans('Save').'">';
				print '&nbsp;<a class="button" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'">'.$langs->trans('Return').'</a>';
			}
			else
			{
				if ($object->statut == 0 && $user->rights->almacen->transfin->mod)
					print '<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&idr='.$aValue['id'].'&action=editline">'.img_picto($langs->trans('Edit'),'edit').'</a>';
				if ($object->statut == 0 && $user->rights->almacen->transfin->mod)
					print '&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&idr='.$aValue['id'].'&action=deleteline">'.img_picto($langs->trans('Delete'),'delete').'</a>';
			}
			print '</td>';
		}
		$k++;
		print '</tr>';
	}
}
print '</table>';
dol_fiche_end();
if ($user->rights->almacen->transfin->mod && ($action == 'editline' || $action == 'createline'))
{
	print '</form>';
}

if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE) && $abc)
{
	$outputlangs = $langs;
	$newlang = '';
	if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
	if ($conf->global->MAIN_MULTILANGS && empty($newlang))  $newlang = $object->thirdparty->default_lang;
	if (! empty($newlang)) {
		$outputlangs = new Translate("", $conf);
		$outputlangs->setDefaultLang($newlang);
	}
	$model=$object->model_pdf;
			//echo '<hr>ret '.$ret = $object->fetch($id);
			// Reload to get new records
	$result=$object->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);
	if ($result < 0) dol_print_error($db,$result);
}

/* ********************************************* */
/*                                               */
/* Barre d'action                                */
/*                                               */
/* ********************************************* */

if (empty($action))
{
	print "<div class=\"tabsAction\">\n";
	if ($object->statut == 1 && !empty($object->fk_entrepot_from) && !empty($object->fk_entrepot_to))
	{
		if ($user->rights->almacen->transf->mod)
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&action=draft&ref='.$ref.'">'.$langs->trans("Backdraft").'</a>';
	}
	if ($object->statut == 0)
	{
		if ($user->rights->almacen->transf->write)
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&action=validate&ref='.$ref.'">'.$langs->trans("Validate").'</a>';
	}

			//if ($user->rights->almacen->transf->write && $lStatut)
	if (empty($object->statut)) $lStatut = false;


	if ($lStatut )
	{
		$lEdit = false;
		foreach ((array) $aListeentrepot AS $j1 => $aJ2)
		{
			if ($aFilterent[$j1]) $lEdit = true;
		}

		if ($user->admin || $lEdit)
		{
					////////////////////////////////////////
			if ($user->rights->almacen->transf->app && $typemov=='T' || $user->rights->almacen->transfin->app && $typemov=='E' || $user->rights->almacen->transfout->app && $typemov=='O')
				print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=recep&ref='.$ref.'">'.$langs->trans("Approve").'</a>';
			if ($user->rights->almacen->transf->del)
				print '<a class="butActionDelete" href="'.$_SERVER['PHP_SELF'].'?action=delete&ref='.$ref.'">'.$langs->trans("Delete").'</a>';
		}
	}
	print '</div>';
}

/* Accion donde armo mi mensaje*/

/* fin de donde armo el mensaje*/





	/*
		print '<div class="tabsAction">';
		//documents
		print '<table width="100%"><tr><td width="50%" valign="top">';
		print '<a name="builddoc"></a>'; // ancre
		$object->fetch($id);
		// Documents generes

		$filename=dol_sanitizeFileName($object->ref);
		$filename= 'almacen';
		//cambiando de nombre al reporte
		$filedir=$conf->almacen->dir_output . '/' . dol_sanitizeFileName($object->ref);
		$urlsource=$_SERVER['PHP_SELF'].'?id='.$id;
		$genallowed=$user->rights->almacen->crearpedido;
		$delallowed=$user->rights->almacen->delpedido;
		$genallowed = 1;
		$delallowed = 1;
		$object->modelpdf = 's_alidaalm';
		print '<br>';
		print $formfile->showdocuments('almacen',$filename,$filedir,$urlsource,$genallowed,$delallowed,$object->modelpdf,1,0,0,28,0,'','','',$soc->default_lang);
		$somethingshown=$formfile->numoffiles;
		print '</td></tr></table>';
		print "</div>";
*/
		if ($resdoc>0 && $object->statut >= 1)
		{
			$newReport[$id] = $aReport;
			$_SESSION['reporttransf'] = serialize($newReport);
			$_SESSION['reporttransfh'] = serialize($newReporth);
			print '<div class="tabsAction">';
			//documents
			print '<table width="100%"><tr><td width="50%" valign="top">';
			print '<a name="builddoc"></a>';
			$filename=dol_sanitizeFileName($object->ref);
			//cambiando de nombre al reporte
			$filedir   =$conf->almacen->dir_output . '/' . dol_sanitizeFileName($object->ref);
			$urlsource =$_SERVER['PHP_SELF'].'?id='.$id;
			$genallowed=$user->rights->almacen->creardoc;
			$delallowed=$user->rights->almacen->deldoc;
			$object->modelpdf = $object->model_pdf;
			//$genallowed=false;
			$delallowed=false;
			//$object->modelpdf = 'notaingalm';
			print '<br>';
			print $formfile->showdocuments('almacen',$filename,$filedir,$urlsource,$genallowed,$delallowed,$object->modelpdf,1,0,0,28,0,'','','',$soc->default_lang);
			$somethingshown=$formfile->numoffiles;
			print '</td></tr></table>';

			print "</div>";



		}
		?>