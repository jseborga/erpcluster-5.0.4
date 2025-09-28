<?php
/*
 * Actions
 */

if ($modal == 'ficheactivity')
{
	//uppdf
	if ($action == 'updoc')
	{
		if ($objact->fetch($_POST["ida"])>0)
		{
			$db->begin();
			// Logo/Photo save
			$dir     = $conf->poa->dir_output.'/activity/doc';
			$file_OKfin = is_uploaded_file($_FILES['docpdf']['tmp_name']);
			if ($file_OKfin)
			{
				if (GETPOST('deletedocfin'))
				{
					$fileimg=$dir.'/'.$object->image_fin;
					$dirthumbs=$dir.'/thumbs';
					dol_delete_file($fileimg);
					dol_delete_dir_recursive($dirthumbs);
				}

				if (doc_format_supported_activity($_FILES['docpdf']['name']) > 0)
				{
					dol_mkdir($dir);
					if (@is_dir($dir))
					{
						//agregamos el archivo
						$objdoca->fk_activity = $object->id;
						$objdoca->name_doc = dol_sanitizeFileName($_FILES['docpdf']['name']);
						$objdoca->detail = GETPOST('detail','alpha');
						$objdoca->fk_user_create = $user->id;
						$objdoca->date_create = dol_now();
						$objdoca->tms = dol_now();
						$objdoca->statut = 1;
						$iddoc = $objdoca->create($user);
						if (! $iddoc >0) $error++;
						$aFile = explode('.',dol_sanitizeFileName($_FILES['docpdf']['name']));
						$file = '';
						foreach ((array) $aFile AS $j => $val)
						{
							if (empty($file)) $file = $iddoc;
							else $file.= '.'.$val;
						}
						//actualizamos
						$objdocatmp = new Poaactivitydoc($db);
						$objdocatmp->fetch($iddoc);
						if ($objdocatmp->id == $iddoc)
						{
							$objdocatmp->name_doc = $file;
							$res = $objdocatmp->update($user);
							if (!$res>0) $error++;
						}
						$newfile = $dir.'/'.$file;

						$result = dol_move_uploaded_file($_FILES['docpdf']['tmp_name'], $newfile, 1);
						if (! $result > 0)
						{
							$error++;
							$errors[] = "ErrorFailedToSaveFile";
						}
						else
						{
							$imgThumbSmall = vignette($newfile, $maxwidthsmall, $maxheightsmall, '_small', $quality);
							$imgThumbMini = vignette($newfile, $maxwidthmini, $maxheightmini, '_mini', $quality);
						}
					}
				}
				else
				{
					$error++;
					$errors[] = "ErrorBadImageFormat";
				}
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
					case 3: //uploaded file was only partially uploaded
					$errors[] = "ErrorFilePartiallyUploaded";
					break;
				}
			}
			if (empty($error)) $db->commit();
			else $db->rollback();
			$action = '';
		}
	}

	// Add
	if ($action == 'add' && $user->rights->poa->act->crear)
	{
		$error = 0;
		$gestion = GETPOST('gestion');
		//obtenemos el ultimo numero
		$nro_activity = $objact->fetch_next_nro($gestion);

		$objact->initAsSpecimen();
		//$date_activity = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
		$aDate = dol_getdate(dol_now());
		$date_activity = dol_stringtotime(substr(GETPOST('di_'),0,10).' '.$aDate['hours'].':'.$aDate['minutes']);

		$objact->gestion         = GETPOST('gestion');
		$objact->fk_prev         = GETPOST('fk_prev')+0;
		$objact->fk_father       = GETPOST('fk_father')+0;
		if (!empty($object->fk_father))
		{
			$objnew = new Poaprev($db);
			if ($objnew->fetch('',$objact->fk_father)>0)
			{
				if ($objnew->nro_preventive == $objact->fk_father)
					$objact->fk_father = $objnew->id;
				else
					$objact->fk_father = 0;
			}
			else
				$objact->fk_father = 0;
		}
		else
			$objact->fk_father = 0;
		//preventive gestion pasada
		$nro_preventive_ant = GETPOST('nro_preventive_ant');
		$gestion_ant = GETPOST('gestion_ant');
		if (!empty($nro_preventive_ant) && !empty($gestion_ant))
		{
			$objnew = new Poaprev($db);
			if ($objnew->fetch('',$nro_preventive_ant,$gestion_ant)>0)
			{
				if ($objnew->nro_preventive == $nro_preventive_ant && $objnew->gestion == $gestion_ant)
					$objact->fk_prev_ant = $objnew->id;
				else
					$objact->fk_prev_ant = 0;
			}
			else
				$objact->fk_prev_ant = 0;
		}
		else
			$objact->fk_prev_ant = 0;

		$objact->fk_poa          = GETPOST('fk_poa');
		$objact->fk_pac          = GETPOST('fk_pac')+0;
		$objact->fk_area         = GETPOST('fk_area');
		if ($user->admin) $objact->nro_activity  = GETPOST('nro_activity');
		else $objact->nro_activity  = $nro_activity;
		$objact->priority        = GETPOST('priority');
		$objact->code_requirement= GETPOST('code_requirement');
		$objact->date_activity   = $date_activity;
		$objact->fk_user_create  = GETPOST('fk_user_create');
		$objact->label           = GETPOST('label');
		$objact->partida         = GETPOST('partida');
		$objact->amount          = GETPOST('amount');
		$objact->pseudonym       = GETPOST('pseudonym');

		if (empty($objact->label))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("Errorlabelisrequired").'</div>';
		}
		$objact->date_create = dol_now();
		$objact->tms = dol_now();
		if ($objact->fk_user_create <= 0) $objact->fk_user_create = $user->id;
		$objact->entity = $conf->entity;
		$objact->statut = 0;
		$objact->active = 1;

		if (empty($objact->nro_activity))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("Errornroactivityisrequired").'</div>';
		}
		if (empty($error))
		{
			$ida = $objact->create($user);
			if ($ida > 0)
			{
				header("Location: ".$_SERVER['PHP_SELF']."?ida=".$ida.'&modal=ficheactivity&fk_poa='.$fk_poa);
				exit;
			}
			$action = 'create';
			$mesg='<div class="error">'.$objact->error.'</div>';
		}
		else
		{
			if ($error) $action="create";   // Force retour sur page creation
		}
	}

		// Addpro
	if ($action == 'addpro' && $user->rights->poa->act->adds)
	{
		$error = 0;
		if ($objact->fetch($ida)>0)
		{
			$date_procedure = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));

			$objectd->fk_activity = $ida;
			$objectd->code_procedure = GETPOST('code_procedure');
			$objectd->date_procedure = $date_procedure;
			$objectd->date_create = dol_now();
			$objectd->fk_user_create  = $user->id;
			$objectd->tms     = date('YmdHis');
			$objectd->statut  = 1;

			if (empty($objectd->code_procedure))
			{
				$error++;
				$mesg.='<div class="error">'.$langs->trans("Errorcodeprocedureisrequired").'</div>';
			}
			if (empty($error))
			{
				$idp = $objectd->create($user);
				if ($idp > 0)
				{
					header("Location: ".$_SERVER['PHP_SELF']."?ida=".$ida);
					exit;
				}
				$action = '';
				$mesg='<div class="error">'.$objectd->error.'</div>';
			}
			else
				$action="";
			// Force retour sur page creation
		}
	}


		// updatepro
	if ($action == 'updatepro' && $user->rights->poa->act->mods)
	{
		$idr = GETPOST('idr','int');
		$error = 0;
		if ($objact->fetch($ida)>0)
		{
			if ($objectd->fetch($idr)>0)
			{
				$date_procedure = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));

				$objectd->code_procedure = GETPOST('code_procedure');
				$objectd->date_procedure = $date_procedure;
				$objectd->tms     = date('YmdHis');
				$objectd->statut  = 1;

				if (empty($objectd->code_procedure))
				{
					$error++;
					$mesg.='<div class="error">'.$langs->trans("Errorcodeprocedureisrequired").'</div>';
				}
				if (empty($error))
				{
					$res = $objectd->update($user);
					if ($res > 0)
					{
						header("Location: ".$_SERVER['PHP_SELF']."?ida=".$ida);
						exit;
					}
					$action = 'editpro';
					$mesg='<div class="error">'.$objectd->error.'</div>';
				}
			}
		}
		$action='editpro';
	}

	// Addmon
	if ($action == 'addmon' && $user->rights->poa->act->addm)
	{
		$error = 0;
		if ($objact->fetch($ida)>0)
		{
			//$date_tracking = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
			$date_tracking = dol_stringtotime(GETPOST('di_'));
			if (!$user->admin)
				$date_tracking = dol_now();
			$objectw->fk_activity = $ida;
			$objectw->followup = GETPOST('followup');
			$objectw->followto = GETPOST('followto');
			$objectw->date_tracking = $date_tracking;
			$objectw->date_create = dol_now();
			$objectw->fk_user_create  = $user->id;
			$objectw->tms     = dol_now();
			$objectw->statut  = 1;

			if (empty($objectw->followup))
			{
				$error++;
				$mesg.='<div class="error">'.$langs->trans("Errorfollowupisrequired").'</div>';
			}
			if (empty($error))
			{
				$res = $objectw->create($user);
				if ($res > 0)
				{
					header("Location: ".$_SERVER['PHP_SELF']."?ida=".$ida);
					exit;
				}
				$action = '';
				$mesg='<div class="error">'.$objectw->error.'</div>';
			}
			else
				$action="";
			// Force retour sur page creation
		}
	}

	// updatemon
	if ($action == 'updatemon' && $user->rights->poa->act->modm)
	{
		$idr = GETPOST('idr','int');
		$error = 0;
		if ($objact->fetch($ida)>0)
		{
			if ($objectw->fetch($idr)>0)
			{
				//$date_tracking = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
				$date_tracking = dol_stringtotime(GETPOST('di_'));
				$objectw->followup = GETPOST('followup');
				$objectw->followto = GETPOST('followto');
				if ($user->admin)
					$objectw->date_tracking = $date_tracking;
				$objectw->tms     = dol_now();
				$objectw->statut  = 1;
				if (empty($objectw->followup))
				{
					$error++;
					$mesg.='<div class="error">'.$langs->trans("Errorfollowupisrequired").'</div>';
				}
				if (empty($error))
				{
					$res = $objectw->update($user);
					if ($res > 0)
					{
						header("Location: ".$_SERVER['PHP_SELF']."?ida=".$ida);
						exit;
					}
					$action = '';
					$mesg='<div class="error">'.$objectw->error.'</div>';
				}
				else
					$action="";
			// Force retour sur page creation
			}
		}
	}

	// Adddoc
	if ($action == 'adddoc' && $user->rights->poa->doc->crear && $_POST["cancel"] <> $langs->trans("Cancel"))
	{
		$error = 0;
		$objdoc->entity = $conf->entity;
		$objdoc->fk_type_con = GETPOST('fk_type_con');
		$objdoc->code = GETPOST('code');
		$objdoc->fk_user_create = $user->id;
		$objdoc->date_create = dol_now();
		$objdoc->tms = dol_now();
		$objdoc->active = 1;
		if (empty($objdoc->code))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("Errordocumentisrequired").'</div>';
		}
		if (empty($error))
		{
			$res = $objdoc->create($user);
			if ($res > 0)
			{
				$mesg.='<div class="ok">'.$langs->trans("Savedsuccesfully").'</div>';
				$action = '';
			}
			else
			{
				$mesg.='<div class="error">'.$objdoc->error.'</div>';
				$action = 'createdoc';
			}
		}
		else
			$action='createdoc';
	}

	//addcheck
	if ($action == 'addcheck' && $user->rights->poa->act->addc)
	{
		$error = 0;
		if ($objact->fetch($ida)>0)
		{
			$aChecklist = GETPOST('checklist');

			foreach ((array) $aChecklist AS $code => $value)
			{
		//buscamos el registro
				if ($objectc->fetch_code($objact->id,$code)>0)
				{
					if ($objectc->fk_activity == $objact->id &&
						$objectc->code == $code)
					{
			//actualizamos
						$objectc->checklist = $value;
						$objectc->tms = dol_now();
						$res = $objectc->update($user);
						if ($res <= 0)
						{
							$error++;
							$mesg.='<div class="error">'.$objectc->error.'</div>';
						}
					}
					else
					{
			//creamos el registro
						$objectc->fk_activity = $objact->id;
						$objectc->code = $code;
						$objectc->checklist = $value;
						$objectc->fk_user_create = $user->id;
						$objectc->date_create = dol_now();
						$objectc->tms = dol_now();
						$objectc->statut = 1;
						$objectc->create($user);
						if ($res <= 0)
						{
							$error++;
							$mesg.='<div class="error">'.$objectc->error.'</div>';
						}
					}
				}
			}
		}
		else
		{
			$error++;
			$mesg.='<div class="error">'.$object->error.'</div>';
		}
		if (empty($error))
			$mesg.='<div class="ok">'.$langs->trans("Savedsuccesfully").'</div>';

		$action = '';
	}

	// Delete prev
	if ($action == 'confirm_delete' && $user->rights->poa->act->del)
	{
		$error = 0;
		$objact->fetch($_REQUEST["ida"]);
		$fk_poa = $objact->fk_poa;
		$db->begin();
		if (empty($error))
		{
			$result=$objact->delete($user);
			if ($result > 0)
			{
				$db->commit();
				header("Location: ".$_SERVER['PHP_SELF'].'?fk_poa='.$fk_poa);
				exit;
			}
			else
			{
				$db->rollback();
				$mesg='<div class="error">'.$object->error.'</div>';
				$action='';
			}
		}
		else
		{
			$db->rollback();
			$mesg='<div class="error">'.$langs->trans('Error delete').'</div>';
			$action = '';
		}
	}

	// Delete monitoreo
	if ($action == 'confirm_delete_mon' && $user->rights->poa->act->delm)
	{
		$error = 0;
		$objectw->fetch($_REQUEST["idr"]);
		$db->begin();
		if (empty($error))
		{
			$result=$objectw->delete($user);
			if ($result > 0)
			{
				$db->commit();
				header("Location: ".$_SERVER['PHP_SELF'].'?ida='.$ida);
				exit;
			}
			else
			{
				$db->rollback();
				$mesg='<div class="error">'.$objectw->error.'</div>';
				$action='';
			}
		}
		else
		{
			$db->rollback();
			$mesg='<div class="error">'.$langs->trans('Error delete').'</div>';
			$action = '';
		}
	}



	// Modify activity
	if ($action == 'update' && $user->rights->poa->act->mod && $_POST["cancel"] <> $langs->trans("Cancel"))
	{
		$error = 0;

		if ($objact->fetch($_POST["ida"]))
		{
			print_r($_POST);
			//$date_preventive = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
			$aDate = dol_getdate(dol_now());
			$date_activity = dol_stringtotime(substr(GETPOST('di_'),0,10).' '.(strlen($aDate['hours'])==1?'0'.$aDate['hours']:$aDate['hours']).':'.(strlen($aDate['minutes'])==1?'0'.$aDate['minutes']:$aDate['minutes']));

			$objact->gestion         = GETPOST('gestion');
			$objact->fk_poa          = GETPOST('fk_poa');
			//buscamos el poa
			$objpoa->fetch($objact->fk_poa);
			if ($objpoa->id == $objact->fk_poa)
			{
				$objact->partida = $objpoa->partida;
			}
			$objact->fk_pac          = GETPOST('fk_pac');
			//buscamos el pac
			if ($objact->fk_pac > 0)
			{
				$objpac->fetch($objact->fk_pac);
				if ($objpac->id == $objact->fk_pac)
				{
					if ($objpac->fk_poa != $objact->fk_poa)
					{
						$error++;
						$mesg = '<div class="error">'.$langs->trans('Error, el pac no pertenece al poa').'</div>';
					}
				}
			}

			//preventive gestion pasada
			$nro_preventive_ant = GETPOST('nro_preventive_ant');
			$gestion_ant = GETPOST('gestion_ant');
			if (!empty($nro_preventive_ant) && !empty($gestion_ant))
			{
				$objnew = new Poaprev($db);
				if ($objnew->fetch('',$nro_preventive_ant,$gestion_ant)>0)
				{
					if ($objnew->nro_preventive == $nro_preventive_ant && $objnew->gestion == $gestion_ant)
						$objact->fk_prev_ant = $objnew->id;
					else
						$objact->fk_prev_ant = 0;
				}
				else
					$objact->fk_prev_ant = 0;
			}
			else
				$objact->fk_prev_ant = 0;

			$objact->fk_prev         = GETPOST('fk_prev');
			$objact->fk_area         = GETPOST('fk_area');
			$objact->nro_activity    = GETPOST('nro_activity');
			$objact->priority        = GETPOST('priority');
			$objact->code_requirement= GETPOST('code_requirement');
			$objact->date_activity   = $date_activity;
			$objact->fk_user_create  = GETPOST('fk_user_create');
			$objact->label           = GETPOST('label');
			$objact->partida         = GETPOST('partida');
			$objact->amount          = GETPOST('amount');
			$objact->pseudonym       = GETPOST('pseudonym');
			$db->begin();
			if (empty($error))
			{
				echo $res = $objact->update($user);
				if ( $res > 0)
				{
					$db->commit();
					$action = '';
					$_GET["id"] = $_POST["id"];
					header('Location: '.$_SERVER['PHP_SELF'].'?ida='.$ida.'&modal=ficheactivity');
					exit;
						//$mesg = '<div class="ok">Fiche mise a jour</div>';
				}
				else
				{
					$action = 'edit';
					$_GET["id"] = $_POST["id"];
					$mesg = '<div class="error">'.$objact->error.'</div>';
				}
			}
			echo '<hr>err '.$error;
			$db->rollback();
			$action= 'edit';
		}
		else
		{
			$action = 'edit';
			$_GET["id"] = $_POST["id"];
			$mesg = '<div class="error">'.$objact->error.'</div>';
		}
		echo $error;exit;
	}

	if ( ($action == 'createedit') )
	{
		require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
			//$tmparray=getProperty(GETPOST('country_id','int'),'all',$db,$langs,0);
		$tmparray['fk_pac'] = GETPOST('fk_pac');
		$tmparray['idp'] = GETPOST('idp');
		$tmparray['gestion'] = GETPOST('gestion');
		$tmparray['fk_father'] = GETPOST('fk_father');
		$tmparray['nom'] = GETPOST('nom');
		$tmparray['nro_activity'] = GETPOST('nro_activity');
		$tmparray['date_activity'] = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));

		if (! empty($tmparray['fk_pac']))
		{
			$objact->fk_pac = $tmparray['fk_pac'];
			$objpac->fetch($objact->fk_pac);
			$objact->gestion = $tmparray['gestion'];
			$objact->fk_father = $tmparray['fk_father'];
			$objact->nom = $tmparray['nom'];
			$objact->nro_activity = $tmparray['nro_activity'];
			$objact->date_activity = $tmparray['date_activity'];
			if ($objpac->id == $objact->fk_pac)
			{
				$objact->label  = $objpac->nom;
				$objact->amount = $objpac->amount;

			}
			$action='create';
		}
	}

	if ( ($action == 'createeditpar') )
	{
		require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
			//$tmparray=getProperty(GETPOST('country_id','int'),'all',$db,$langs,0);
		$tmparray['fk_structure'] = GETPOST('fk_structure');
		$tmparray['fk_poa'] = GETPOST('fk_poa');
		$tmparray['partida'] = GETPOST('partida');
		$tmparray['amount'] = GETPOST('amount');
		if (! empty($tmparray['fk_poa']))
		{
			$objpre->fk_structure = $tmparray['fk_structure'];
			$objpre->fk_poa = $tmparray['fk_poa'];
			$objpre->partida = $tmparray['partida'];
			$objpre->amount = $tmparray['amount'];
			$action='';
		}
	}
	if ($_POST["cancel"] == $langs->trans("Cancel"))
	{
		$action = '';
		$_GET["id"] = $_POST["id"];
		header('Location: '.$_SERVER['PHP_SELF']);
		exit;
	}


	// Addmon
	if ($action == 'addmon' && $user->rights->poa->act->addm)
	{
		$error = 0;
		if ($objact->fetch($ida)>0)
		{
			$aDate = dol_getdate(dol_now());
			$date_tracking = dol_mktime($aDate['hours'], $aDate['minutes'], $aDate['seconds'], GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
			if (!$user->admin) $date_tracking = dol_now();
			$objectw->fk_activity = $ida;
			$objectw->followup = GETPOST('followup');
			$objectw->followto = GETPOST('followto');
			$objectw->date_tracking = $date_tracking;
			$objectw->date_create = dol_now();
			$objectw->fk_user_create  = $user->id;
			$objectw->tms     = dol_now();
			$objectw->statut  = 1;

			if (empty($objectw->followup))
			{
				$error++;
				$mesg.='<div class="error">'.$langs->trans("Errorfollowupisrequired").'</div>';
			}
			if (empty($error))
			{
				$res = $objectw->create($user);
				if ($res > 0)
				{
					header("Location: ".$_SERVER['PHP_SELF']."?ida=".$ida.'&dol_hide_leftmenu=1');
					exit;
				}
				$action = '';
				$mesg='<div class="error">'.$objectw->error.'</div>';
			}
			else
				$action="";
			// Force retour sur page creation
		}
	}

	// updatemon
	if ($action == 'updatemon' && $user->rights->poa->act->modm)
	{
		$error = 0;
		if ($objact->fetch($ida)>0)
		{
			if ($objectw->fetch($idr)>0)
			{
				$date_tracking = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
				$objectw->followup = GETPOST('followup');
				$objectw->followto = GETPOST('followto');
				if ($user->admin) $objectw->date_tracking = $date_tracking;
				$objectw->tms     = date('YmdHis');
				$objectw->statut  = 1;
				if (empty($objectw->followup))
				{
					$error++;
					$mesg.='<div class="error">'.$langs->trans("Errorfollowupisrequired").'</div>';
				}
				if (empty($error))
				{
					$res = $objectw->update($user);
					if ($res > 0)
					{
						header("Location: ".$_SERVER['PHP_SELF']."?ida=".$ida.'&dol_hide_leftmenu=1');
						exit;
					}
					$action = '';
					$mesg='<div class="error">'.$objectw->error.'</div>';
				}
				else
					$action="";
			// Force retour sur page creation
			}
		}
	}

	// Delete monitoreo
	if ($action == 'deletemon' && $user->rights->poa->act->delm)
	{
		$error = 0;
		$objectw->fetch($_REQUEST["idr"]);
		$db->begin();
		if ($objectw->id == $_REQUEST['idr'])
		{
			$result=$objectw->delete($user);
			if ($result > 0)
			{
				$db->commit();
				header("Location: ".$_SERVER['PHP_SELF'].'?ida='.$ida);
				exit;
			}
			else
			{
				$db->rollback();
				$mesg='<div class="error">'.$objectw->error.'</div>';
				$action='';
			}
		}
		else
		{
			$mesg='<div class="error">'.$langs->trans('Error delete').'</div>';
			$action = '';
		}
	}
	//noclose
	if ($action == 'noclose')
	{
		if ($objact->fetch(GETPOST('ida'))>0)
		{
					//cambiando a validado
			$objact->statut = 1;
					//update
			$objact->update($user);
			$action = '';
			header('Location: '.$_SERVER['PHP_SELF'].'?ida='.$ida);
		}
	}

	if ($action == 'validate')
	{
		if ($objact->fetch($ida)>0)
		{
					//cambiando a validado
			$objact->statut = 1;
					//update
			$objact->update($user);
			$action = '';
			header('Location: '.$_SERVER['PHP_SELF'].'?ida='.$ida);
		}
	}
			// Confirmation de la no  validation
	if ($action == 'novalidate')
	{
		if ($objact->fetch($ida)>0)
		{
					//cambiando a validado
			$objact->statut = 0;
					//update
			$objact->update($user);
			$action = '';
			header('Location: '.$_SERVER['PHP_SELF'].'?ida='.$ida);
		}
	}
			// Confirmation de la no  validation
	if ($action == 'close')
	{
		if ($objact->fetch($ida)>0)
		{
					//cambiando a validado
			$objact->statut = 9;
					//update
			$objact->update($user);
			$action = '';
			header('Location: '.$_SERVER['PHP_SELF'].'?ida='.$ida);
		}
	}



			// Confirm delete partida producto
	if ($action == 'validateprev')
	{
				//buscamos la seleccion de la modalidad
		$aTable = fetch_tables($_GET['fk_type_con']);
		$form = new Form($db);
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?ida=".$objact->id.'&fk_type_con='.$_GET['fk_type_con'],$langs->trans("Validatepreventive"),$langs->trans("Confirmvalidatepreventive").' : '.$langs->trans('Modality').': '.$aTable['label'].' '.$langs->trans('Of').' '.price($aTable['range_ini']).' '.$langs->trans('To').' '.price($aTable['range_fin']),"confirm_validate_prev",'',0,2);
		if ($ret == 'html') print '<br>';
	}
}

?>