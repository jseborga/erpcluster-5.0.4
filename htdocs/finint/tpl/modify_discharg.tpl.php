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

if (!empty($object->fk_account))
{
	if ($user->rights->finint->desc->crear)
	{
		if ($vline)
		{
			$viewline = $vline;
		}
		else
		{
			$viewline = empty($conf->global->MAIN_SIZE_LISTE_LIMIT)?20:$conf->global->MAIN_SIZE_LISTE_LIMIT;
		}
		//$result=$object->fetch($id, $ref);

		dol_htmloutput_errors($mesg);
		if ($lAdd)
		{
			$aDatanew = unserialize($_SESSION['aDatanew']);
			$aData = $aDatanew[$object->id];
			print_fiche_titre($langs->trans("ModifyDischarge"));
			$nProject = 1;
			$modeaction='modifyrefr';
			include DOL_DOCUMENT_ROOT.'/finint/tpl/script.tpl.php';


			print '<form id="addpc" class="form-inline" role="form" name="addpc" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="modifyfourn">';
			print '<input type="hidden" name="id" value="'.$object->id.'">';
			print '<input type="hidden" name="idrcd" value="'.GETPOST('idrcd').'">';
			print '<input type="hidden" name="balance" value="'.$saldoBankUser.'">';
			print '<input type="hidden" name="fk_transfert_ult" value="'.$fk_transfert_ult.'">';
			print '<input type="hidden" name="operation" value="'.($myclass->operation?$myclass->operation:($object->courant == 2 ? 'LIQ' : 'LIQ')).'">';
			print '<input type="hidden" name="quant" value="1">';

			//informacion oculta

			//armamos select para filtrar registros
			if ($conf->fiscal->enabled)
			{
				if (!empty($code_facture))
				{
					$objcfact = fetch_type_facture(0,$code_facture);
					if ($objcfact->nit_required) $lViewqr = true;
				}
				print '<div class="form-group">';
				print '<label for="code_facture" class="sr_only">'.$langs->trans('Typedocument').'</label>';
				print $form->selectarray('type_operation',$aTypeOperation,(GETPOST('type_operation')?GETPOST('type_operation'):$myclass->type_operation));
				//print select_type_facture($myclass->code_facture,'code_f',0,'',0,1,'code_iso');
				print '</div>';
			}
			if($conf->browser->layout=='classic')
			{
				dol_fiche_head();

				print '<table id="tablac" class="noborder centpercent table table-reflow">';
					//encabezado
				print '<thead>';
				print '<tr>';
				if ($lViewqr)
				{
					print '<th>'.$langs->trans("QR").'</th>';
					print '<th>'.$langs->trans("NIT").'</th>';
				}
				print '<th>'.$langs->trans("Date").'</th>';
				print '<th>'.$langs->trans("Nro").'</th>';
				if ($lViewproj)
					print '<th>'.$langs->trans("Project").'</th>';
				if ($lViewtask)
					print '<th>'.$langs->trans("Item").'</th>';

				print '<th>'.$langs->trans('Description').'</th>';
				print '<th>'.$langs->trans('Amount').'</th>';
				print '<th>'.$langs->trans('Action').'</th>';
				print '</tr>';
				print '</thead>';
				print '<tbody>';

				//registro visible
				include DOL_DOCUMENT_ROOT.'/finint/tpl/modify_discharg_line.tpl.php';
				print '</tbody>';

				print '</table>';
				dol_fiche_end();

				print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Save").'"></div>';
			}
			else
			{
				dol_fiche_head();
				//qr
				print '<div class="form-group">';
				print '<label for="codeqr" class="col-lg-2 control-label">'.$langs->trans('QR').'</label>';
				print '<div class="col-lg-10">';
				print '<input id="codeqr" class="flat form-control" type="text" name="codeqr[]" value="'.$myclass->codeqr.'">';
				print '</div>';
				print '</div>';
				//nit
				print '<div class="form-group">';
				print '<label for="codeqr" class="col-lg-2 control-label">'.$langs->trans('NIT').'</label>';
				print '<div class="col-lg-10">';
				print '<input id="nit" class="flat form-control" type="text" name="nit[]" value="'.$myclass->nit.'">';
				print '</div>';
				print '</div>';
				//nit
				print '<div class="form-group">';
				print '<label for="codeqr" class="col-lg-2 control-label">'.$langs->trans('Date').'</label>';
				print '<div class="col-lg-10">';
				$form->select_date(($myclass->fourn_date?$myclass->fourn_date:$myclass->dateo),'do[]','','','','transaction',1,1,0,0);
				print '</div>';
				print '</div>';
				//nit
				//if ($conf->fiscal->enabled)
				//{
				//	print '<div class="form-group">';
				//	print '<label for="codeqr" class="col-lg-2 control-label">'.$langs->trans('Codefacture').'</label>';
				//	print '<div class="col-lg-10">';
				//	print select_type_facture($selected,'code_facture',0,'',0,1,'code_iso');

				//print '<input id="code_facture" class="flat form-control" type="text" name="code_facture[]" value="'.$myclass->code_facture.'">';
				//	print '</div>';
				//	print '</div>';
				//}
				//nit
				print '<div class="form-group">';
				print '<label for="codeqr" class="col-lg-2 control-label">'.$langs->trans('Doc').'</label>';
				print '<div class="col-lg-10">';
				print '<input id="num_chq" class="flat form-control" type="text" name="num_chq[]" value="'.$myclass->num_chq.'" required>';
				print '</div>';
				print '</div>';
				//nit
				print '<div class="form-group">';
				print '<label for="codeqr" class="col-lg-2 control-label">'.$langs->trans('Project').'</label>';
				print '<div class="col-lg-10">';
				if ($conf->monprojet->enabled)
				{
				$filterkey = '';
				$numprojet = $formproject->select_projects(($user->societe_id>0?$user->societe_id:-1), $fk_projet, 'fk_projet[]', 0,0,1,0,0,0,0,$filterkey);
				}
				print '</div>';
				print '</div>';
				//nit
				print '<div class="form-group">';
				print '<label for="codeqr" class="col-lg-2 control-label">'.$langs->trans('Description').'</label>';
				print '<div class="col-lg-10">';
				print '<input name="dp_desc[]" class="flat form-control" type="text" size="24"  value="'.$myclass->label.'">';
				print '</div>';
				print '</div>';
				//nit
				print '<div class="form-group">';
				print '<label for="codeqr" class="col-lg-2 control-label">'.$langs->trans('Amount').'</label>';
				print '<div class="col-lg-10">';
				print '<input name="amount[]" class="flat form-control" type="number" step="any" min="0" value="" required>';

				print '</div>';
				print '</div>';

				print '<div class="form-group">';
				print '<div class="col-lg-offset-2 col-lg-10">';
				print '<button type="submit" class="button" name="add">'.$langs->trans("Save").'</button>';
				print '</div>';
				print '</div>';

				dol_fiche_end();

			}
			print "</form>";


			if ($lViewqr)
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
		else
		{
			print '<div class="center">'.$langs->trans('Requestconfirmationoftransfer').'</div>';
		}
	}
}
?>