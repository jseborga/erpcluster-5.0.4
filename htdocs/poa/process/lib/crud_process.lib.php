<?php
//crud process
if ($modal == 'ficheprocess')
{
	$fk_poa_prev = $id; //idpreventivo
	// Add
	if ($action == 'add' && $user->rights->poa->proc->crear)
	{
		$error = 0;
		//$objproc->date_process = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
		$aDate = dol_getdate(dol_now());
		$objproc->date_process = dol_stringtotime(substr(GETPOST('di_'),0,10).' '.$aDate['hours'].':'.$aDate['minutes']);
		if (!$user->admin) $objproc->date_process = dol_now();
		$objproc->gestion     = GETPOST('gestion');
		$objproc->ref         = 0;
		$objproc->fk_poa_prev = $object->id;
		$objproc->fk_area     = $object->fk_area;
		$objproc->amount      = GETPOST('amount');
		$objproc->fk_type_con = GETPOST('fk_type_con');
		//tipo contrato
		$objproc->fk_type_adj = GETPOST('fk_type_adj');
		$objproc->label       = GETPOST('label');
		$objproc->justification   = GETPOST('justification');
		$objproc->term        = GETPOST('term')+0;
		$objproc->ref_pac     = GETPOST('ref_pac');


		$objproc->doc_precio_referencial   = GETPOST('doc_precio_referencial')+0;
		$objproc->doc_certif_presupuestaria   = GETPOST('doc_certif_presupuestaria')+0;
		$objproc->doc_especific_tecnica   = GETPOST('doc_especific_tecnica')+0;
		$objproc->doc_modelo_contrato   = GETPOST('doc_modelo_contrato')+0;
		$objproc->doc_informe_lega   = GETPOST('doc_informe_lega')+0;
		$objproc->doc_precio_referencial   = GETPOST('doc_precio_referencial')+0;
		$objproc->doc_pac   = GETPOST('doc_pac')+0;
		$objproc->doc_prop  = GETPOST('doc_prop')+0;
		$objproc->fk_soc    = GETPOST('fk_soc')+0;

		$objproc->metodo_sel_anpe   = GETPOST('metodo_sel_anpe')+0;
		$objproc->metodo_sel_lpni   = GETPOST('metodo_sel_lpni')+0;
		$objproc->metodo_sel_cae    = GETPOST('metodo_sel_pemb')+0;
		//$objproc->metodo_sel_cae    = GETPOST('metodo_sel_cae')+0;

		$objproc->entity = $conf->entity;
		$objproc->date_create = dol_now();
		$objproc->fk_user_create = $user->id;
		$objproc->tms = dol_now();
		$objproc->statut = 0;
		if (empty($objproc->fk_area))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("Errorareaisrequired").'</div>';
		}
		if (empty($objproc->label))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("Errorlabelrequired").'</div>';
		}
		if (empty($objproc->justification))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("Errorjustificationrequired").'</div>';
		}
		//analizamos el tipo de contrato
		$aTable = fetch_tables($objproc->fk_type_con);
		if ($aTable['id'] == $objproc->fk_type_con)
		{
			if ($objproc->amount >= $aTable['range_ini'] &&
				$objproc->amount <= $aTable['range_fin'])
			{
				//esta en los rangos
			}
			else
			{
				$error++;
				$mesg.='<div class="error">'.$langs->trans("Errortypecontratnotvalid").'</div>';
			}
		}
		//revisamos si el tipo contrato del pac esta idem al seleccionado
		$objprev = new Poaprev($db);
		if ($objprev->fetch($fk_poa_prev))
		{
			if ($objprev->fk_poa > 0)
			{
				//buscamos el pac
				if ($objpac->fetch($objprev->fk_poa))
				{
					//verificamos
					if ($objpac->fk_type_modality != $objproc->fk_type_con)
					{
						$error++;
						$mesg.='<div class="error">'.$langs->trans("Errorthetypeofcontratrequiresupdateingpac").'</div>';
					}
				}
				else
				{
					$error++;
					$mesg.='<div class="error">'.$langs->trans("Errorpacnotexist").'</div>';
				}
			}
		}
		if (empty($error))
		{
			$idpr = $objproc->create($user);
			if ($idpr > 0)
			{
				$_SESSION['aListip'][$id]['idPrev'] = $fk_poa_prev;
				$_SESSION['aListip'][$id]['idAct'] = $ida;
				header("Location: ".$_SERVER['PHP_SELF']."?ida=".$ida.'&modal=ficheprocess');
				exit;
			}
			$action = 'create';
			$mesg='<div class="error">'.$objproc->error.'</div>';
		}
		else
		{
			if ($error) $action="create";
		// Force retour sur page creation
		}
	}

	//uppdf
	if ($action == 'uppdf')
	{
		$linklast = GETPOST('linklast','alpha');
		//echo 'id '.GETPOST('id');exit;
		$idreg = GETPOST('idreg');
		$res = $objproc->fetch($idreg);
		if ($res > 0)
		{
		// Logo/Photo save
			$dir = $conf->poa->dir_output.'/process/pdf';
			$file_OKfin = is_uploaded_file($_FILES['docpdf']['tmp_name']);
			if ($file_OKfin)
			{
				if (doc_format_supported($_FILES['docpdf']['name']) > 0)
				{
					dol_mkdir($dir);
					if (@is_dir($dir))
					{
						$newfile=$dir.'/'.dol_sanitizeFileName($_FILES['docpdf']['name']);
						$newfile=$dir.'/'.dol_sanitizeFileName($idreg.'.pdf');
						$result = dol_move_uploaded_file($_FILES['docpdf']['tmp_name'], $newfile, 1);
						if (! $result > 0) $errors[] = "ErrorFailedToSaveFile";
						else
						{
							$imgThumbSmall = vignette($newfile, $maxwidthsmall, $maxheightsmall, '_small', $quality);
						// Create mini thumbs for company (Ratio is near 16/9)
						// Used on menu or for setup page for example
							$imgThumbMini = vignette($newfile, $maxwidthmini, $maxheightmini, '_mini', $quality);
						}
						if ($linklast)
						{
							header('Location: '.$linklast);
							exit;
						}
						header("Location: ".$_SERVER['PHP_SELF']."?ida=".$ida);
						exit;
					}
				}
				else
					$errors[] = "ErrorBadImageFormat";
			}
			else
			{
				switch($_FILES['docpdf']['error'])
				{
					case 1:
				//uploaded file exceeds the upload_max_filesize directive in php.ini
					case 2:
				//uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
					$errors[] = "ErrorFileSizeTooLarge";
					break;
					case 3:
				//uploaded file was only partially uploaded
					$errors[] = "ErrorFilePartiallyUploaded";
					break;
				}
			}
		}
		if ($linklast)
		{
			header('Location: '.$linklast);
			exit;
		}
	}

	// Delete process
	if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->poa->proc->del)
	{
		$objproc->fetch($idprocess);
		if ($objproc->id == $idprocess)
		{
			$result=$objproc->delete($user);
			if ($result > 0)
			{
				header("Location: ".DOL_URL_ROOT.'/poa/process/liste.php');
				exit;
			}
			else
			{
				$mesg='<div class="error">'.$object->error.'</div>';
				$action='';
			}
		}
	}

	// Cancel process
	if ($action == 'confirm_cancel' && $_REQUEST["confirm"] == 'yes' && $user->rights->poa->proc->nul)
	{
		$objprev = new Poaprev($db);
		$objproc->fetch($idprocess);
		$objproc->statut = -1;
		$result=$objproc->update($user);
		if ($result > 0)
		{
			header("Location: ".$_SERVER['PHP_SELF'].'?ida='.$ida);
			exit;
		}
		else
		{
			$mesg='<div class="error">'.$object->error.'</div>';
			$action='';
		}
	}

	// Modification entrepot
	if ($action == 'update' && $_POST["cancel"] <> $langs->trans("Cancel"))
	{
		if ($objproc->fetch($idProcess)>0)
		{
			$error = 0;
			//$objproc->date_process = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
			$aDate = dol_getdate(dol_now());
			$objproc->date_process = dol_stringtotime(substr(GETPOST('di_'),0,10).' '.$aDate['hours'].':'.$aDate['minutes']);

			$objproc->gestion   = GETPOST('gestion');
			$objproc->ref   = GETPOST('ref');
			$objproc->fk_area   = GETPOST('fk_area');
			$objproc->fk_poa_prev   = GETPOST('fk_poa_prev');
			$objproc->fk_area   = GETPOST('fk_area');
			$objproc->amount   = GETPOST('amount');
			$objproc->fk_type_con  = GETPOST('fk_type_con');
			$objproc->fk_type_adj   = GETPOST('fk_type_adj');
			$objproc->label   = GETPOST('label');
			$objproc->justification   = GETPOST('justification');
			$objproc->term    = GETPOST('term')+0;
			$objproc->ref_pac    = GETPOST('ref_pac');


			$objproc->doc_precio_referencial   = GETPOST('doc_precio_referencial')+0;
			$objproc->doc_certif_presupuestaria   = GETPOST('doc_certif_presupuestaria')+0;
			$objproc->doc_especific_tecnica   = GETPOST('doc_especific_tecnica')+0;
			$objproc->doc_modelo_contrato   = GETPOST('doc_modelo_contrato')+0;
			$objproc->doc_informe_lega   = GETPOST('doc_informe_lega')+0;
			$objproc->doc_precio_referencial   = GETPOST('doc_precio_referencial')+0;
			$objproc->doc_pac   = GETPOST('doc_pac')+0;
			$objproc->doc_prop  = GETPOST('doc_prop')+0;
			$objproc->fk_soc    = GETPOST('fk_soc')+0;

			$objproc->metodo_sel_anpe   = GETPOST('metodo_sel_anpe')+0;
			$objproc->metodo_sel_lpni   = GETPOST('metodo_sel_lpni')+0;
			$objproc->metodo_sel_cae    = GETPOST('metodo_sel_pemb')+0;
			$objproc->metodo_sel_cae    = GETPOST('metodo_sel_cae')+0;

			$objproc->tms = dol_now();
			$objproc->statut = 0;
			if (empty($objproc->fk_area))
			{
				$error++;
				$mesg.='<div class="error">'.$langs->trans("Errorareaisrequired").'</div>';
			}
			if (empty($objproc->label))
			{
				$error++;
				$mesg.='<div class="error">'.$langs->trans("Errorlabelrequired").'</div>';
			}
			if (empty($objproc->justification))
			{
				$error++;
				$mesg.='<div class="error">'.$langs->trans("Errorjustificationrequired").'</div>';
			}
			//revisamos si el tipo contrato del pac esta idem al seleccionado
			if ($object->fetch($fk_poa_prev))
			{
				if ($object->fk_pac > 0)
				{
					//buscamos el pac
					if ($objpac->fetch($object->fk_pac))
					{
					//verificamos
						if ($objpac->fk_type_modality != $objproc->fk_type_con)
						{
							$error++;
							$mesg.='<div class="error">'.$langs->trans("Errorrequiresupdatingthepac").'</div>';
						}
					}
					else
					{
						$error++;
						$mesg.='<div class="error">'.$langs->trans("Errorpacnotexist").'</div>';
					}
				}
			}
			if (empty($error))
			{
				if ( $objproc->update($idProcess, $user) > 0)
				{
					$action = '';
					$_GET["id"] = $_POST["id"];
					//$mesg = '<div class="ok">Fiche mise a jour</div>';
					header ('Location: '.$_SERVER['PHP_SELF'].'?ida='.$objact->id.'&modal=ficheprocess');
					exit;
				}
				else
				{
					$action = 'edit';
					$_GET["id"] = $_POST["id"];
					$mesg = '<div class="error">'.$objproc->error.'</div>';
				}
			}
			else
			{
				$action = 'edit';
				$_GET["id"] = $_POST["id"];
			}
		}
		else
		{
			$action = 'edit';
			$_GET["id"] = $_POST["id"];
			$mesg = '<div class="error">'.$objproc->error.'</div>';
		}
	}
}
?>