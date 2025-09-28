<?php
if ($user->rights->monprojet->con->crear)
{
	include_once(DOL_DOCUMENT_ROOT.'/monprojet/tpl/framesprojet.tpl.php');

	if ($action == 'createaddr')
	{
		$type_resource = GETPOST('type_resource');
	}

	if ($conf->assets->enabled)
		require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsext.class.php';

	foreach ($aTypeResource AS $i => $data)
		$aTr[$data['code']] = $data['label'];
	$aTable = array('sol_almacen'=>$langs->trans('Wharehouserequest'),'facture_fourn'=>$langs->trans('Supplierinvoice'));
	$var = !$var;
	//include_once DOL_DOCUMENT_ROOT.'/monprojet/tpl/framesresource.tpl.php';
	//$aCat = array ('MAT'=>$langs->trans('Material'),'MDO'=>$langs->trans('Mano de Obra'),'EMH'=>$langs->trans('Equipo y Maquinaria'));
	//$aCat = $aStrbudget[$id]['aStrref'];
	//$aCat = $aStrbudget[$id]['aStrlabel'];
	$form = new Formv($db);
	print "<tr $bc[$var]>";
	print '<td>';
	print $form->selectarray('type_resource',$aTr,$type_resource,1,0,0,' autofocus onblur="javascript: recargasearch(this)"');
	print '</td>';

	//print '<td id="productnone" nowrap style="display:block;">';
	//print '</td>';

	if($type_resource == 'MAText' || $type_resource == 'MODext' || $type_resource == 'MAQext')
	{
		print '<td id="tagsociete" style="display:block;">';
		$filtertype = 's.fournisseur = 1';
		print $form->select_company_v('','fk_soc',$filtertype,0, 0, -1,-1,'',1, array(),0,'','');
		print '</td>';
	}
	elseif ($tresource == 'MA' && $type_resource == 'MAT')
	{
		print '<td id="tagproduct" style="display:block;">';
		print $form->select_produits_projet($projectstatic->id,$fk_product_task,'product_projet','',$conf->product->limit_size,0,-1,2,'',1,$ajaxOptions,'','','resource');
		//print $form->select_produits_v('','product','',0, 0, -1,-1,'',1, array(),0,'','');
		print '</td>';
	}
	elseif ($tresource == 'MO' && $type_resource == 'MOD')
	{
		print '<td id="tagmember" style="display:block;">';
		print $form->select_member('','fk_member','',1, 0, 0,'','',0, 0);
		print '</td>';
	}
	elseif ($tresource == 'MQ' && $type_resource == 'MAQ')
	{
		print '<td id="tagassets" style="display:block;">';
		if ($conf->assets->enabled)
		{
			$assetsext = new Assetsext($db);
			print $assetsext->select_assets_line('', 'fk_asset', '', 0, 0, 1, 2, '', 1, array());
		}
		else
		{
			print '<input id="assets" type="text" name="name_asset" value="">';
		}
		print '</td>';
	}
	else
		print '<td></td>';	
	print '<td id="refcatnx" style="display:none;"><input id="catnom" type="catnom" name="catnom" value="" ></td>';

	//print '<td id="refcat" style="display:none;">'.$form->selectarray('code_structure',$aCat,(GETPOST('code_categorie')?GETPOST('code_categorie'):$newdata->code_structure),1);
	//print '</td>';
	print '<td id="ref_exttd">'.'<input id="ref_ext" type="text" name="ref_ext" value="" size="7">';
	print '</td>';

	if(($type_resource == 'MAText' || $type_resource == 'MODext' || $type_resource == 'MAQext') || $tresource == 'MO' && $type_resource == 'MOD')
	{
		print '<td>'.$projectstatic->id;
		//buscamos el insumo a aplicar
		//print $form->select_produits_projet($projectstatic->id,$fk_product_task,'product_projet','',$conf->product->limit_size,0,-1,2,'',1,$ajaxOptions,'','','resource');

		print $form->select_produits_projet($projectstatic->id,$newdata->fk_product,'product','',$conf->product->limit_size,0,-1,2,'',1,$ajaxOptions,'','','resource');

		print '</td>';
	}
	else
		print '<td></td>';

	print '<td>';
	print $form->selectUnits($fk_unit,'fk_unit',1);
	//select_cunit($newdata->fk_unit,'fk_unit','',1,'rowid','short_label');
	print '</td>';
	print '<td>'.'<input type="number" id="quant" class="flat len80" step="any" name="quant" value="'.$newdata->quant.'">'.'</td>';
	//print '<td>'.'<input type="number" id="percent_prod" class="flat len80" step="any" name="percent_prod" value="'.$newdata->percent_prod.'">'.'</td>';
	//print '<td>'.'<input type="number" id="amount_noprod" class="flat len80" step="any" name="amount_noprod" value="'.$newdata->amount_noprod.'">'.'</td>';
	//print '<td>'.'<input type="number" id="price" class="flat len80" step="any" name="price" value="'.$newdata->amount.'">'.'</td>';
	//print '<td></td>';
	print '<td>'.'<input type="submit" class="btn btn-primary" name="save" value="'.$langs->trans('Save').'">'.'</td>';
	print '</tr>';
}

?>