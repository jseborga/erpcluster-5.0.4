<?php
if ($user->rights->monprojet->con->crear)
{
	include_once(DOL_DOCUMENT_ROOT.'/monprojet/tpl/framesprojet.tpl.php');
	if (empty($fk_soc)) $fk_soc = 0;
	if ($action == 'createaddr')
	{
		$type_resource 	= GETPOST('type_resource');
		$fk_soc 		= GETPOST('fk_soc')+0;
		$fk_member 		= GETPOST('fk_member')+0;
		$fk_commande	= GETPOST('fk_commande')+0;
		$fk_asset_det   = GETPOST('fk_asset_det')+0;
		$fk_product_task= GETPOST('product');
	}
	$lCommande 	= false;
	$lMember 	= false;
	$lAssets 	= false;
	$lProduct   = false;
	if ($conf->assets->enabled)
		require_once DOL_DOCUMENT_ROOT.'/assets/assets/class/assetsext.class.php';

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
		$lCommande = true;
		print '<td id="tagsociete" style="display:block;">';
		$filtertype = 's.fournisseur = 1';
		print $form->select_company_v($fk_soc,'fk_soc',$filtertype,0, 0, -1,-1,'',1, array(),0,'',$filtercompany);
		print '</td>';
	}
	elseif ($group_resource == 'MA' && $type_resource == 'MAT')
	{
		$fk_entrepot = 0;
		if ($conf->almacen->enabled)
		{
			$objrel = new Entrepotrelationext($db);
			$objrel->fetchAll('','',0,0,array(1=>1),'AND'," AND fk_projet = ".$projectstatic->id,true); 
			$fk_entrepot=$objrel->rowid;
		}
		print '<td id="tagproduct" style="display:block;">';
		$ajaxOptions = '';
		print $form->select_produits_projet($projectstatic->id,$fk_product_task,'product','',$conf->product->limit_size,0,-1,2,'',1,$ajaxOptions,'','','resource',$fk_entrepot,false);
		print '</td>';
		if ($fk_product_task>0)
		{
			$lProduct = true;
			$product->fetch($fk_product_task);
			$fk_unit = $product->fk_unit;
		}
		
	}
	elseif ($group_resource == 'MO' && $type_resource == 'MOD')
	{
		$lMember = true;
		print '<td id="tagmember" style="display:block;">';
		print $form->select_member($fk_member,'fk_member','',1, 0, 0,'','',0, 0);
		print '</td>';
	}
	elseif ($group_resource == 'MQ' && $type_resource == 'MAQ')
	{
		$lAssets = true;
		print '<td id="tagassets" style="display:block;">';
		if ($conf->assets->enabled)
		{
			$assetsext = new Assetsext($db);
			print $assetsext->select_assets_line($fk_asset_det, 'fk_asset_det', '', 0, 0, 1, 2, '', 1, array(),$projectstatic->id);
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
	print '<td id="ref_exttd">';
	if ($lCommande || $lAssets)
	{
		if (empty($fk_commande))
			$fk_commande = 0;
		if ($lCommande)
		{
			require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.commande.class.php';
			$commandedet = new CommandeFournisseurLigne($db);
			$commandedet->fetch($fk_commande);
			$filtercommande = " AND t.fk_projet =".$projectstatic->id;
			if ($commandedet->rowid == $fk_commande)
				$fk_unit = $commandedet->fk_unit;
			print $form->select_commande_fourn_v($fk_commande, 'fk_commande', '', 0, 0, 1, 2, '', 1, '',$fk_soc,'',$filtercommande);
		}
		if ($lAssets)
		{
			require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignmentdetext.class.php';
			$assignmentdet = new Assetsassignmentdetext($db);
			$assets = new Assetsext($db);
			$assignmentdet->fetch($fk_asset_det);

			if ($assignmentdet->id == $fk_asset_det)
			{
				$assets->fetch($assignmentdet->fk_asset);
				$fk_unit = $assets->fk_unit;
			}
		}
		
	}
	if ($lMember)
	{
		if ($fk_member>0)
		{
			require_once DOL_DOCUMENT_ROOT.'/salary/class/pcontractext.class.php';
			require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
			$pcontract = new Pcontractext($db);
			$adherent = new Adherent($db);
		//$pcontract->fetch($fk_pcontrat);
			$pcontract->fetch_vigent($fk_member,1);
			$adherent->fetch($fk_member);
			print $pcontract->ref .' - '.$adherent->lastname.' '.$adherent->firstname;	
			print '<input type="hidden" name="fk_pcontrat" value="'.$pcontract->id.'">';
			$fk_unit = $pcontract->fk_unit;
		//print $form->select_contrat_member_v($fk_pcontrat, 'fk_pcontrat', '', 20, 1, '', 1,array(),$fk_member,$action,0);

		}
	}
	//if ($lAssets)
	//{
	//	if (empty($fk_commande))$fk_commande = 0;
	//	print $form->select_commande_fourn_v($fk_commande, 'fk_commande', '', 0, 0, 1, 2, '', 1, '',$fk_soc,'');
	//}

	print '</td>';

	print '<td>';
	if (($lMember && $fk_unit))
	{
		$unit = $pcontract->getLabelOfUnit();
		print $langs->trans($unit);
		print '<input type="hidden" name="fk_unit" value="'.$fk_unit.'">';
	}
	elseif(($lCommande||$lAssets) && $fk_unit)
	{
		if ($lCommande)
			$unit = $commandedet->getLabelOfUnit();
		if ($lAssets)
		{
			$unit = $assets->getLabelOfUnit();
		}
		print $langs->trans($unit);
		print '<input type="hidden" name="fk_unit" value="'.$fk_unit.'">';		
	}
	elseif($lProduct && $fk_unit)
	{
		$unit = $product->getLabelOfUnit();
		print $langs->trans($unit);
		print '<input type="hidden" name="fk_unit" value="'.$fk_unit.'">';				
	}
	else
		print $form->selectUnits($fk_unit,'fk_unit',1);
	//select_cunit($newdata->fk_unit,'fk_unit','',1,'rowid','short_label');
	print '</td>';
	print '<td>'.'<input type="number" id="quant" class="flat len80" step="any" name="quant" value="'.$newdata->quant.'">'.'</td>';

	if ($action == 'createaddr')
	{
		if (GETPOST('optsel') == 's')
		{
			print '<script type="text/javascript">';
			print ' window.parent.document.getElementById('."'fk_commande'".').focus();';
			print '</script>';	
		}
		if (GETPOST('optsel') == 'c' || GETPOST('optsel') == 'p')
		{
			print '<script type="text/javascript">';
			print ' window.parent.document.getElementById('."'quant'".').focus();';
			print '</script>';	
		}
		if (GETPOST('optsel') == 'm')
		{
			print '<script type="text/javascript">';
			if (empty($fk_unit))
				print ' window.parent.document.getElementById('."'fk_unit'".').focus();';
			else
				print ' window.parent.document.getElementById('."'quant'".').focus();';
			print '</script>';	
		}
	}
	//print '<td>'.'<input type="number" id="percent_prod" class="flat len80" step="any" name="percent_prod" value="'.$newdata->percent_prod.'">'.'</td>';
	//print '<td>'.'<input type="number" id="amount_noprod" class="flat len80" step="any" name="amount_noprod" value="'.$newdata->amount_noprod.'">'.'</td>';
	//print '<td>'.'<input type="number" id="price" class="flat len80" step="any" name="price" value="'.$newdata->amount.'">'.'</td>';
	//print '<td></td>';
	print '<td>'.'<input type="submit" class="btn btn-primary" name="save" value="'.$langs->trans('Save').'">'.'</td>';
	print '</tr>';
}

?>