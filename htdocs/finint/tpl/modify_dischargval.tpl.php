<?php

if ($conf->fiscal->enabled) require_once DOL_DOCUMENT_ROOT.'/fiscal/lib/fiscal.lib.php';
if ($conf->monprojet->enabled)
{
	$projectstatic=new Projectext($db);
	$projectsListId = $projectstatic->getMonProjectsAuthorizedForUser($user,($mine?$mine:(empty($user->rights->projet->all->lire)?0:2)),1);
}
$nProject = 0;
if (!empty($projectsListId))
{
	$aProject = explode(',',$projectsListId);
	$nProject = count($aProject);
}
//proceso para crear gastos
//buscamos la cuenta del que desembolsa
$accountuser = new Accountuser($db);
$lViewqr = false;
$lViewproj = true;
$lViewtask = true;
if (!$conf->monprojet->enabled)
{
	$lViewproj = false;
	$lViewtask = false;

}
$aTypeOperation = array(1=>$langs->trans('Invoice'),-1=>$langs->trans('Receipt'));

$myclass = $deplacement;

if (!empty($object->fk_account))
{
	if ($user->rights->finint->desc->val)
	{
		if ($vline)
		{
			$viewline = $vline;
		}
		else
		{
			$viewline = empty($conf->global->MAIN_SIZE_LISTE_LIMIT)?20:$conf->global->MAIN_SIZE_LISTE_LIMIT;
		}

		dol_htmloutput_errors($mesg);

		$aDatanew = unserialize($_SESSION['aDatanew']);
		$aData = $aDatanew[$object->id];
		print_fiche_titre($langs->trans("ModifyDischarge"));
		$nProject = 1;

		$modeaction='modifyvalrefr';
		include DOL_DOCUMENT_ROOT.'/finint/tpl/script.tpl.php';

		if ($conf->fiscal->enabled)
		{
			if (!empty($myclass->code_facture))
			{
				//$objcfact = fetch_type_facture(0,$myclass->code_facture);
				$objcfact->fetch(0,$myclass->code_facture);
				if ($objcfact->nit_required) $myclass->type_operation = 1;
				else $myclass->type_operation = -1;
				if ($myclass->type_operation== -1 && $objcfact->retention) $myclass->type_operation = 1;
			}
		}
		print '<form id="addpc" class="form-inline" role="form" name="addpc" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="modifyfourn">';
		print '<input type="hidden" name="id" value="'.$object->id.'">';
		print '<input type="hidden" name="idrcd" value="'.GETPOST('idrcd').'">';
		print '<input type="hidden" name="balance" value="'.$saldoBankUser.'">';
		print '<input type="hidden" name="operation" value="'.($myclass->operation?$myclass->operation:($object->courant == 2 ? 'LIQ' : 'LIQ')).'">';
		print '<input type="hidden" name="quant" value="1">';

			//informacion oculta
		dol_fiche_head();

		print '<table class="noborder centpercent table table-reflow">';

		print '<tr class="liste_titre">';
		print '<th><label for="code_facture" class="sr_only">'.$langs->trans('Type').'</label></th>';
		if ($conf->fiscal->enabled)
			print '<th><label for="code_facture" class="sr_only">'.$langs->trans('Typefacture').'</label></th>';
		print '<th><label for="code_facture" class="sr_only">'.$langs->trans('Purchasedestination').'</label></th>';
		print '<th>'.$langs->trans('Nitcompany').'</th>';
		print '</tr>';

		print '<tr>';
		print '<td>';
		print $form->selectarray('type_operation',$aTypeOperation,$myclass->type_operation);
		print '</td>';
		//armamos select para filtrar registros
		if ($conf->fiscal->enabled)
		{
			if (!empty($myclass->code_facture))
			{
				$objcfact = fetch_type_facture(0,$myclass->code_facture);
				if ($objcfact->nit_required) $lViewqr = true;
			}
			//print '<div class="form-group">';
			//print '<label for="code_facture" class="sr_only">'.$langs->trans('Typefacture').'</label>';
			print '<td>';
			print select_type_facture($myclass->code_facture,'code_f',0,' required ',0,1,'code_iso');
			print '</td>';
		}

		//print '<div class="form-group">';
		//print '<label for="code_facture" class="sr_only">'.$langs->trans('Purchasedestination').'</label>';
			//code type facture
		print '<td>';
		print $form->load_type_purchase('code_type_purchase',(GETPOST('code_type_purchase')?GETPOST('code_type_purchase'):$myclass->code_type_purchase),0, 'code', false);
		print ' '.info_admin($langs->trans("Seleccione un clasificador para poder relacionar la información posteriormente segun “para que” se realizo la compra” (Ejemplo: Activo Fijo realizara una marca sobre esta factura para poder dar de alta el activo en el módulo de Activo Fijo"),1);
		print '</td>';

		if ($conf->fiscal->enabled)
		{
			require_once DOL_DOCUMENT_ROOT.'/fiscal/class/entity.class.php';
			require_once DOL_DOCUMENT_ROOT.'/fiscal/class/entityaddext.class.php';
			$objentity = new Entity($db);
			$objentityadd = new Entityaddext($db);
			$res = $objentity->fetchAll('','',0,0,array(),'AND');
			$showempty = false;
			if ($res>0) $showempty = true;
			list($nb,$options) = $objentityadd->select_entity('nit',(GETPOST('nit_company')?GETPOST('nit_company'):$conf->global->MAIN_INFO_TVAINTRA),1,$showempty);
			if ($nb<0) $lViewedit = false;
			print '<td>';
			print '<select name="nit_company">'.$options.'</select>';
			print '</td>';
		}

		print '</tr>';
		print '</table>';


		print '<table id="tablac" class="noborder centpercent table table-reflow">';
			//encabezado
		print '<thead>';
		print '<tr class="liste_titre">';

		print_liste_field_titre($langs->trans('Date'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
		if (!$lViewqr)
			print_liste_field_titre($langs->trans('Nro'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
		else
			print '<td></td>';
		print_liste_field_titre($langs->trans('Categorie'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
		$colspan= 0;
		if ($lViewproj)
			print_liste_field_titre($langs->trans('Project'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
		else $colspan++;
		if ($lViewtask)
			print_liste_field_titre($langs->trans('Task'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
		else $colspan++;
		if ($colspan>0)
			print '<th class="liste_titre" colspan="'.$colspan.'"></th>';
		print_liste_field_titre($langs->trans('Description'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Amount'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
		print '</tr>';
		print '</thead>';

		print '<tbody>';

		//registro visible
		$k = 0;
		print '<input type="hidden" name="fk_soc" value="'.$myclass->fk_soc.'">';
		print '<input type="hidden" name="fourn_reg1" value="'.$myclass->fourn_reg1.'">';
		print '<input type="hidden" name="fourn_reg2" value="'.$myclass->fourn_reg2.'">';
		print '<input type="hidden" name="fourn_reg3" value="'.$myclass->fourn_reg3.'">';
		print '<input type="hidden" name="fourn_reg4" value="'.$myclass->fourn_reg4.'">';
		print '<input type="hidden" name="fourn_reg5" value="'.$myclass->fourn_reg5.'">';
		print '<input type="hidden" name="socid" value="'.$myclass->socid.'">';
		print '<input type="hidden" name="operation" value="'.($myclass->operation?$myclass->operation:($object->courant == 2 ? 'LIQ' : 'LIQ')).'">';
		print '<input type="hidden" name="quant" value="'.$myclass->quant.'">';

		print '<tr>';

		//date
		print '<td>';
		$newdate = dol_print_date($myclass->dateo, "%Y-%m-%d");
		//print '<input type="date" name="do_" value="'.date($newdate).'">';
		print $form->select_date($myclass->dateo,'do_','','','','transaction',1,0,0,0,'','','',$k);
		print '</td>';

		//num_chq
		print '<td>';
		if (!$lViewqr)
			print '<input id="num_chq" class="flat" size="4" type="text" name="num_chq" value="'.($myclass->nro_chq?$myclass->nro_chq:$myclass->facture).'">';
		print '</td>';

		print '<td class="nowrap">';
		list($nbcategories,$options) = getselcategorie($myclass->fk_categorie);
		if ($nbcategories)
		{
			print '<select class="flat" name="cat1">'.$options.'</select>';
		}
		//print $form->select_all_categories('5',$myclass->fk_categorie,'cat1');
		print '</td>';

		if ($lViewproj)
		{
			print '<td>';
			$filterkey = '';
			$numprojet = $formproject->select_projects(($user->societe_id>0?$user->societe_id:-1), $fk_projetsel, 'fk_projet__0', 0,0,1,0,0,0,0,$filterkey);
			print '</td>';
		}
		if ($lViewtask)
		{
			if (empty($filtertask))
			{
				$filtertask = " t.fk_projet = 0";
			}
			print '<td>';
			print $formtask->select_task($fk_tasksel, 'fk_task', $filtertask, 1,0,0,array(),'',0,0,'','','','','rowid');
			print '</td>';
		}
		//detail
		print '<td>';
		print '<input name="dp_desc" class="flat" size="14" type="text" value="'.$myclass->detail.'" required>';
		print '</td>';
		print '<td>';
		print '<input name="amount" class="flat len80" type="number" step="any" min="0" value="'.price2num($myclass->amount).'" required>';
		print '</td>';
		print '</tr>';


			//segunda linea
		print '<thead>';
		print '<tr class="liste_titre">';
		print_liste_field_titre($langs->trans('NIT'),'','','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Name'),'','','',$param,(!$lViewqr?'colspan="6"':''),$sortfield,$sortorder);
		if ($lViewqr)
		{
			//print '<thead>';
			//print '<tr class="liste_titre">';
			print_liste_field_titre($langs->trans('QR'),'','','',$param,'',$sortfield,$sortorder);
			print_liste_field_titre($langs->trans('Datefacture'),'','','',$param,'',$sortfield,$sortorder);
			print_liste_field_titre($langs->trans('Nrofacture'),'','','',$param,'',$sortfield,$sortorder);
			print_liste_field_titre($langs->trans('Numaut'),'','','',$param,'',$sortfield,$sortorder);
			print_liste_field_titre($langs->trans('Codecontrol'),'','','',$param,'',$sortfield,$sortorder);
		}
		print '</tr>';
		print '</thead>';

		print '<tr>';
			//nit
		print '<td>';
		print '<input id="nit" class="flat" size="8" type="text" name="fourn_nit" value="'.$myclass->fourn_nit.'" required>';
		print '</td>';
			//name
		print '<td '.(!$lViewqr?'colspan="6"':'').'>';
		print '<input id="fourn_soc" class="flat" size="10" type="text" name="fourn_soc" value="'.$myclass->fourn_soc.'">';
		print '</td>';
		if ($lViewqr)
		{
			print '<td>';
			print '<input id="codeqr" class="flat" size="8" type="text" name="codeqr" value="'.(GETPOST('codeqr')?GETPOST('codeqr'):$myclass->codeqr).'">';
			print '</td>';

			print '<td>';
			$newdate = dol_print_date($myclass->fourn_date, "%Y-%m-%d");
			//print '<input id="fourn_date" class="flat" size="5" type="date" name="fourn_date" value="'.$newdate.'">';
			print $form->select_date($myclass->fourn_date,'fdo_','','','','transaction',1,0,0,0,'','','',$k);
			print '</td>';

			print '<td>';
			print '<input id="fourn_facture" class="flat" type="number" min="0" name="fourn_facture" value="'.$myclass->fourn_facture.'">';
			print '</td>';
			print '<td>';
			print '<input id="fourn_numaut" class="flat" size="10" type="text" name="fourn_numaut" value="'.$myclass->fourn_numaut.'" required>';
			print '</td>';
			print '<td>';
			print '<input id="fourn_codecont" class="flat" size="10" type="text" name="fourn_codecont" value="'.$myclass->fourn_codecont.'">';
			print '</td>';
		}
		print '</tr>';
		print '</tbody>';

		print '</table>';
		dol_fiche_end();
		print '<div class="center">';
		if ($lViewqr)
		{
			print '<input type="submit" class="button" name="add" value="'.$langs->trans("Update").'">';
			//para crear factura primero verificamos que esten todos los datos necesarios
			$lFacture = true;
			if (empty($myclass->fourn_nit) || is_null($myclass->fourn_nit)) $lFacture = false;
			if (empty($myclass->fourn_soc) || is_null($myclass->fourn_soc)) $lFacture = false;
			if (empty($myclass->fourn_facture) || is_null($myclass->fourn_facture)) $lFacture = false;
			if (empty($myclass->fourn_numaut) || is_null($myclass->fourn_numaut)) $lFacture = false;
			if ($conf->purchase->enabled)
			{
				if ($myclass->fk_facture_fourn)
				{
					require_once DOL_DOCUMENT_ROOT.'/purchase/class/fournisseur.factureext.class.php';
					$facturefourn = new FactureFournisseurext($db);
					$res = $facturefourn->fetch($myclass->fk_facture_fourn);
					if ($res> 0)
					{
						$facturefourn->ref_ext = $myclass->fourn_facture;
						//verificamos los montos
						$classerr='';
						if ($facturefourn->total_ttc != $myclass->amount)
							$classerr = 'errmark';
						print '<div class="button">';
						print '&nbsp;'.$facturefourn->getNomUrladd(0,'',0,1,$classerr);
						print '</div>';
					}
					else
					{
						if ($lFacture)
							print '<a class="button" href="'.DOL_URL_ROOT.'/purchase/facture/card.php'.'?origin=requestcashdeplacement&originid='.$myclass->id.'&action=create">'.$langs->trans('Createfacture').'</a>';
					}
				}
				else
				{
					if ($lFacture)
						print '<a class="button" href="'.DOL_URL_ROOT.'/purchase/facture/card.php'.'?origin=requestcashdeplacement&originid='.$myclass->id.'&action=create">'.$langs->trans('Createfacture').'</a>';
				}
			}
		}
		else
			print '<input type="submit" class="button" name="add" value="'.$langs->trans("Save").'">';
		print '&nbsp;<a class="button" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=discharg">'.$langs->trans('Return').'</a>';
		print '</div>';
		print "</form>";


		if ($lViewqr && $ABC)
		{
			print_fiche_titre($langs->trans("Subir archivo QR"));


			print '<form class="form-horizontal"  enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
			print '<input type="hidden" name="action" value="veriffile">';
			print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
			print '<input type="hidden" name="id" value="'.$id.'">';

			dol_fiche_head();

			print '<table class="border centpercent">'."\n";
			print '<tr><td width="15%" class="fieldrequired">'.$langs->trans("File").'</td><td>';
			print '<span class="btn btn-default btn-file">';
			print $langs->trans('Browse').' <input type="file"  name="archivo" id="archivo" required>';
			print '</span>';
			print '</td></tr>';

			print '<tr><td>';
			print $langs->trans('Separator');
			print '</td>';
			print '<td>';
			print '<input type="text" name="separator" size="2" required>';
			print '</td></tr>';

			print '</table>'."\n";
			dol_fiche_end();

			print '<center><input type="submit" class="button" name="add" value="'.$langs->trans("Upload").'">';
			print '</center>';

			print '</form>';
		}


	}
}
?>