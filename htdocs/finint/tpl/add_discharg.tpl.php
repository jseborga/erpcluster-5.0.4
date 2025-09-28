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
$aTypeOperation = array(1=>$langs->trans('Invoice'),-1=>$langs->trans('Receipt'));

if (!$conf->monprojet->enabled)
{
	$lViewproj = false;
	$lViewtask = false;

}
if (!empty($object->fk_account))
{
	if ($user->rights->finint->desc->crear)
	{
		$k = $_SESSION['aK'][$object->id];
		$loop = $k;
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
			print_fiche_titre($langs->trans("NewDischarge"));

			$modeaction = 'createrefr';
			include DOL_DOCUMENT_ROOT.'/finint/tpl/script.tpl.php';

			print '<form id="addpc" class="form-inline" role="form" name="addpc" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="addfourn">';
			print '<input type="hidden" name="id" value="'.$object->id.'">';
			print '<input type="hidden" name="balance" value="'.$saldoBankUser.'">';
			print '<input type="hidden" name="k" value="'.$k.'">';
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
				print '<div class="fichehalfdleft">';
				print '<div class="box">';
				print '<table>';
				print '<tr><td>'.$langs->trans('Typedocument').'</td></tr>';
				print '<tr><td>';
				print $form->selectarray('type_operation',$aTypeOperation,GETPOST('type_operation'));
				//print select_type_facture($code_facture,'code_f',0,'',0,1,'code_iso');
				print '</td></tr>';
				print '</table>';
				print '</div>';
				print '</div>';
			}
			if ($conf->monprojet->enabled)
			{
				if ($fk_projetsel>0) $lViewproj = false;
				print '<div class="fichehalfdleft">';
				print '<div class="box">';
				print '<table>';
				print '<tr><td>'.$langs->trans('Project').'</td></tr>';
				print '<tr><td>';
				$filterkey = '';
				$numprojet = $formproject->select_projects(($user->societe_id>0?$user->societe_id:-1), $fk_projetsel, 'fk_proj', 0,0,1,0,0,0,0,$filterkey);
				print '</td></tr>';
				print '</table>';

				print '</div>';
				print '</div>';
				if ($fk_projetsel > 0)
				{
					if ($fk_tasksel>0) $lViewtask = false;
					print '<div class="fichehalfdleft">';
					print '<div class="box">';
					print '<table>';
					print '<tr><td>'.$langs->trans('Task').'</td></tr>';
					print '<tr><td>';
					$filtertask = " t.fk_projet = ".$fk_projetsel;
					print $formtask->select_task($fk_tasksel,'fk_tas', $filtertask,1,0,0,array(),'',0,0,'','','','','rowid');
					print '</td></tr>';
					print '</table>';
					print '</div>';
					print '</div>';
				}
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
				$aUpload = array();
				if (isset($_SESSION['aUpload']))
					$aUpload = unserialize($_SESSION['aUpload']);
				$lButtonadd = true;
				$sumaup = 0;
				$k1 = $k;
				$aDel = $_SESSION['aDel'][$object->id];
				if ($k >= 0)
				{
					$_POST = $_SESSION['aPostr'][$object->id];
					for ($k = 0; $k <=$k1; $k++)
					{
						if (!$aDel[$k])
						{
							include DOL_DOCUMENT_ROOT.'/finint/tpl/add_discharg_line.tpl.php';
						}
					}
				}
				if (count($aUpload)>0 && $abc)
				{
					foreach ((array) $aUpload AS $k => $myclass)
					{
						//verificamos la carga del proveedor
						$filter = array(1=>1);
						$filterstatic = " AND t.tva_intra = ".trim($myclass->nit);
						$res = $societe->fetchAll('','',0,0,$filter,'AND',$filterstatic,true);
						if ($res>0)
						{
							$myclass->socid=$societe->id;
						}
						else
						{
			            	// Load object modCodeTiers
							$module=(! empty($conf->global->SOCIETE_CODECLIENT_ADDON)?$conf->global->SOCIETE_CODECLIENT_ADDON:'mod_codeclient_leopard');
							if (substr($module, 0, 15) == 'mod_codeclient_' && substr($module, -3) == 'php')
							{
								$module = substr($module, 0, dol_strlen($module)-4);
							}
							$dirsociete=array_merge(array('/core/modules/societe/'),$conf->modules_parts['societe']);
							foreach ($dirsociete as $dirroot)
							{
								$res=dol_include_once($dirroot.$module.'.php');
								if ($res) break;
							}
							$modCodeClient = new $module($db);
            				// We verified if the tag prefix is used
							if ($modCodeClient->code_auto)
							{
								$prefixCustomerIsUsed = $modCodeClient->verif_prefixIsUsed();
							}
							$module=$conf->global->SOCIETE_CODECLIENT_ADDON;
							if (substr($module, 0, 15) == 'mod_codeclient_' && substr($module, -3) == 'php')
							{
								$module = substr($module, 0, dol_strlen($module)-4);
							}
							$dirsociete=array_merge(array('/core/modules/societe/'),$conf->modules_parts['societe']);
							foreach ($dirsociete as $dirroot)
							{
								$res=dol_include_once($dirroot.$module.'.php');
								if ($res) break;
							}
							$modCodeFournisseur = new $module($db);
            				// On verifie si la balise prefix est utilisee
							if ($modCodeFournisseur->code_auto)
							{
								$prefixSupplierIsUsed = $modCodeFournisseur->verif_prefixIsUsed();
							}

							$tmpcode=$modCodeClient->getNextValue($societe,0);

							//registramos uno nuevo
							if (!empty($myclass->nit))
							{
								$societe->name = $myclass->nit;
								$societe->code_client = $tmpcode;
								$tmpcode=$modCodeFournisseur->getNextValue($object,1);
								$societe->code_fournisseur = $tmpcode;
								$societe->client = 0;
								$societe->fournisseur =1;
								$societe->tva_intra = $nit;
								$myclass->socid = $societe->create($user);
							}
						}
						include DOL_DOCUMENT_ROOT.'/finint/tpl/add_discharg_line.tpl.php';
						if ($lButtonadd) $lButtonadd = false;
					}
				}
				else
				{
					//include DOL_DOCUMENT_ROOT.'/finint/tpl/add_discharg_line.tpl.php';
				}
				print '</tbody>';
			//armamos el total
			//print '<tr class="liste_total">';
			//print '<td colspan="8">'.$langs->trans('Total').'</td>';
			//print '<td align="right">'.price($sumaup).'</td>';
			//print '<td align="right"></td>';
			//print '<td align="right"></td>';
			//print '</tr>';

				print '</table>';
				dol_fiche_end();

				print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Save").'"></div>';
			}
			else
			{
				$k = 0;
				dol_fiche_head();
				//qr
				//print '<div class="form-group">';
				//print '<label for="codeqr" class="col-lg-2 control-label">'.$langs->trans('QR').'</label>';
				//print '<div class="col-lg-10">';
				//print '<input id="codeqr" class="flat form-control" type="text" name="codeqr[]" value="'.$myclass->codeqr.'">';
				//print '</div>';
				//print '</div>';
				//nit
				//print '<div class="form-group">';
				//print '<label for="codeqr" class="col-lg-2 control-label">'.$langs->trans('NIT').'</label>';
				//print '<div class="col-lg-10">';
				//print '<input id="nit" class="flat form-control" type="text" name="nit[]" value="'.$myclass->nit.'">';
				//print '</div>';
				//print '</div>';
				//nit
				print '<div class="form-group">';
				print '<label for="codeqr" class="col-lg-2 control-label">'.$langs->trans('Date').'</label>';
				print '<div class="col-lg-10">';
				print $form->select_date(dol_now(),'do__'.$k.'__','','','','transaction',1,0,0,0,'','','',$k);

				//$form->select_date(($myclass->fourn_date?$myclass->fourn_date:$myclass->dateo),'do[]','','','','transaction',1,1,0,0);
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
				print '<input id="num_chq" class="flat form-control" type="text" name="num_chq__'.$k.'" value="'.$myclass->num_chq.'" required>';
				print '</div>';
				print '</div>';
				//nit
				if ($lViewproj)
				{
					print '<div class="form-group">';
					print '<label for="codeqr" class="col-lg-2 control-label">'.$langs->trans('Project').'</label>';
					print '<div class="col-lg-10">';
					$filterkey = '';
					$numprojet = $formproject->select_projects(($user->societe_id>0?$user->societe_id:-1), $fk_projet, 'fk_projet__'.$k, 0,0,1,0,0,0,0,$filterkey);
					print '</div>';
					print '</div>';
				}
				else
				{
					print '<input id="fk_projet'.$k.'" type="hidden" name="fk_projet__'.$k.'" value="'.$fk_projetsel.'">';
				}
				if ($lViewtask)
				{
					$fk_proj = GETPOST('fk_projet__'.$k);
					if ($fk_proj>0)
						$filtertask = " t.fk_projet = ".$fk_proj;
					if (empty($filtertask))
					{
						$fk_tasksel = 0;
						$filtertask = " t.fk_projet = 0";
					}
					print '<div class="form-group">';
					print '<label for="codeqr" class="col-lg-2 control-label">'.$langs->trans('Task').'</label>';
					print '<div class="col-lg-10">';
					print $formtask->select_task(GETPOST('fk_task__'.$k), 'fk_task__'.$k, $filtertask, 1,0,0,array(),'',0,0,'','','','','rowid');
					print '</div>';
					print '</div>';
				}
				else
				{
					print '<input id="fk_task'.$k.'" type="hidden" name="fk_task__'.$k.'" value="'.$fk_tasksel.'">';
				}

				//desc
				print '<div class="form-group">';
				print '<label for="codeqr" class="col-lg-2 control-label">'.$langs->trans('Description').'</label>';
				print '<div class="col-lg-10">';
				print '<input name="dp_desc__'.$k.'" class="flat form-control" type="text" size="24"  value="'.$myclass->label.'">';
				print '</div>';
				print '</div>';
				//amount
				print '<div class="form-group">';
				print '<label for="codeqr" class="col-lg-2 control-label">'.$langs->trans('Amount').'</label>';
				print '<div class="col-lg-10">';
				print '<input name="amount__'.$k.'" class="flat form-control" type="number" step="any" min="0" value="" >';

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