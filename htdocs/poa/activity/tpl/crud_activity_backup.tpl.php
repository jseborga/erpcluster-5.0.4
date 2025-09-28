<?php


/*
 * Actions
 */
//uppdf
if ($action == 'updoc')
{
	if ($object->fetch($_POST["id"])>0)
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
			case 1: //uploaded file exceeds the upload_max_filesize directive in php.ini
			case 2: //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
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
	//obtenemos el ultimo numero
	$nro_activity = $object->fetch_next_nro($gestion);

	$object->initAsSpecimen();
	$date_activity = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));

	$object->gestion         = GETPOST('gestion');
	$object->fk_prev         = GETPOST('fk_prev')+0;
	$object->fk_father       = GETPOST('fk_father')+0;
	if (!empty($object->fk_father))
	{
		$objnew = new Poaprev($db);
		if ($objnew->fetch('',$object->fk_father)>0)
			if ($objnew->nro_preventive == $object->fk_father)
				$object->fk_father = $objnew->id;
			else
				$object->fk_father = 0;
			else
				$object->fk_father = 0;
		}
		else
			$object->fk_father = 0;
	//preventive gestion pasada
		$nro_preventive_ant = GETPOST('nro_preventive_ant');
		$gestion_ant = GETPOST('gestion_ant');
		if (!empty($nro_preventive_ant) && !empty($gestion_ant))
		{
			$objnew = new Poaprev($db);
			if ($objnew->fetch('',$nro_preventive_ant,$gestion_ant)>0)
				if ($objnew->nro_preventive == $nro_preventive_ant && $objnew->gestion == $gestion_ant)
					$object->fk_prev_ant = $objnew->id;
				else
					$object->fk_prev_ant = 0;
				else
					$object->fk_prev_ant = 0;
			}
			else
				$object->fk_prev_ant = 0;

			$object->fk_poa          = GETPOST('fk_poa');
			$object->fk_pac          = GETPOST('fk_pac')+0;
			$object->fk_area         = GETPOST('fk_area');
			if ($user->admin)
				$object->nro_activity  = GETPOST('nro_activity');
			else
				$object->nro_activity  = $nro_activity;
			$object->priority        = GETPOST('priority');
			$object->code_requirement= GETPOST('code_requirement');
			$object->date_activity   = $date_activity;
			$object->fk_user_create  = GETPOST('fk_user_create');
			$object->label           = GETPOST('label');
			$object->partida         = GETPOST('partida');
			$object->amount          = GETPOST('amount');
			$object->pseudonym       = GETPOST('pseudonym');

			if (empty($object->label))
			{
				$error++;
				$mesg.='<div class="error">'.$langs->trans("Errorlabelisrequired").'</div>';
			}
			$object->date_create = dol_now();
			$object->tms = date('YmdHis');
			if ($object->fk_user_create <= 0)
				$object->fk_user_create = $user->id;
			$object->entity = $conf->entity;
			$object->statut     = 0;
			$object->active = 1;

			if (empty($object->nro_activity))
			{
				$error++;
				$mesg.='<div class="error">'.$langs->trans("Errornroactivityisrequired").'</div>';
			}
			if (empty($error))
			{
				$id = $object->create($user);
				if ($id > 0)
				{
					header("Location: ".$_SERVER['PHP_SELF']."?id=".$id.'&fk_poa='.$fk_poa.'&dol_hide_leftmenu=1');
					exit;
				}
				$action = 'create';
				$mesg='<div class="error">'.$object->error.'</div>';
			}
			else
			{
				if ($error)
	  $action="create";   // Force retour sur page creation
}
}

// Addpro
if ($action == 'addpro' && $user->rights->poa->act->adds)
{
	$error = 0;
	if ($object->fetch($id)>0)
	{
		$date_procedure = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));

		$objectd->fk_activity = $id;
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
				header("Location: ".$_SERVER['PHP_SELF']."?id=".$id.'&dol_hide_leftmenu=1');
				exit;
			}
			$action = '';
			$mesg='<div class="error">'.$objectd->error.'</div>';
		}
		else
	  $action="";   // Force retour sur page creation
}
}


// updatepro
if ($action == 'updatepro' && $user->rights->poa->act->mods)
{
	$error = 0;
	if ($object->fetch($id)>0)
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
					header("Location: ".$_SERVER['PHP_SELF']."?id=".$id.'&dol_hide_leftmenu=1');
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
	if ($object->fetch($id)>0)
	{
		$date_tracking = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
		if (!$user->admin)
			$date_tracking = dol_now();
		$objectw->fk_activity = $id;
		$objectw->followup = GETPOST('followup');
		$objectw->followto = GETPOST('followto');
		$objectw->date_tracking = $date_tracking;
		$objectw->date_create = dol_now();
		$objectw->fk_user_create  = $user->id;
		$objectw->tms     = date('YmdHis');
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
				header("Location: ".$_SERVER['PHP_SELF']."?id=".$id.'&dol_hide_leftmenu=1');
				exit;
			}
			$action = '';
			$mesg='<div class="error">'.$objectw->error.'</div>';
		}
		else
	  $action="";   // Force retour sur page creation
}
}

// updatemon
if ($action == 'updatemon' && $user->rights->poa->act->modm)
{
	$error = 0;
	if ($object->fetch($id)>0)
	{
		if ($objectw->fetch($idr)>0)
		{
			$date_tracking = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
			$objectw->followup = GETPOST('followup');
			$objectw->followto = GETPOST('followto');
			if ($user->admin)
				$objectw->date_tracking = $date_tracking;
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
					header("Location: ".$_SERVER['PHP_SELF']."?id=".$id.'&dol_hide_leftmenu=1');
					exit;
				}
				$action = '';
				$mesg='<div class="error">'.$objectw->error.'</div>';
			}
			else
		  $action="";   // Force retour sur page creation
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
	if ($object->fetch($id)>0)
	{
		$aChecklist = GETPOST('checklist');

		foreach ((array) $aChecklist AS $code => $value)
		{
		//buscamos el registro
			if ($objectc->fetch_code($object->id,$code)>0)
			{
				if ($objectc->fk_activity == $object->id &&
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
					$objectc->fk_activity = $object->id;
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
if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->poa->act->del)
{
	$error = 0;
	$object->fetch($_REQUEST["id"]);
	$db->begin();
	if (empty($error))
	{
		$result=$object->delete($user);
		if ($result > 0)
		{
			$db->commit();
			header("Location: ".DOL_URL_ROOT.'/poa/poa/liste.php?dol_hide_leftmenu=1&dol_hide_topmenu=1');
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
if ($action == 'confirm_delete_mon' && $_REQUEST["confirm"] == 'yes' && $user->rights->poa->act->delm)
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
			header("Location: ".DOL_URL_ROOT.'/poa/poa/liste.php?dol_hide_leftmenu=1');
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



// Modification entrepot
if ($action == 'update' && $_POST["cancel"] <> $langs->trans("Cancel"))
{
	$error = 0;

	if ($object->fetch($_POST["id"]))
	{
		$date_preventive = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));

		$object->gestion         = GETPOST('gestion');
		$object->fk_poa          = GETPOST('fk_poa');
	//buscamos el poa
		$objpoa->fetch($object->fk_poa);
		if ($objpoa->id == $object->fk_poa)
		{
			$object->partida = $objpoa->partida;
		}
		$object->fk_pac          = GETPOST('fk_pac');
	//buscamos el pac
		if ($object->fk_pac > 0)
		{
			$objpac->fetch($object->fk_pac);
			if ($objpac->id == $object->fk_pac)
			{
				if ($objpac->fk_poa != $object->fk_poa)
				{
					$error++;
					$mesg = '<div class="error">'.$langs->trans('Error, el pac no pertenece al poa').'</div>';
				}
			}
		}
	// $object->fk_father       = GETPOST('fk_father')+0;
	// if (!empty($object->fk_father))
	//   {
	//     $objnew = new Poaprev($db);
	//     if ($objnew->fetch('',$object->fk_father)>0)
	//       if ($objnew->nro_preventive == $object->fk_father)
	//  $object->fk_father = $objnew->id;
	//       else
	//  $object->fk_father = 0;
	//     else
	//       $object->fk_father = 0;
	//   }
	// else
	//   $object->fk_father = 0;

	//preventive gestion pasada

		$nro_preventive_ant = GETPOST('nro_preventive_ant');
		$gestion_ant = GETPOST('gestion_ant');
		if (!empty($nro_preventive_ant) && !empty($gestion_ant))
		{
			$objnew = new Poaprev($db);
			if ($objnew->fetch('',$nro_preventive_ant,$gestion_ant)>0)
				if ($objnew->nro_preventive == $nro_preventive_ant && $objnew->gestion == $gestion_ant)
					$object->fk_prev_ant = $objnew->id;
				else
					$object->fk_prev_ant = 0;
				else
					$object->fk_prev_ant = 0;
			}
			else
				$object->fk_prev_ant = 0;

			$object->fk_prev         = GETPOST('fk_prev');
			$object->fk_area         = GETPOST('fk_area');
			$object->nro_activity    = GETPOST('nro_activity');
			$object->priority        = GETPOST('priority');
			$object->code_requirement= GETPOST('code_requirement');
			$object->date_activity   = $date_preventive;
			$object->fk_user_create  = GETPOST('fk_user_create');
			$object->label           = GETPOST('label');
			$object->partida         = GETPOST('partida');
			$object->amount          = GETPOST('amount');
			$object->pseudonym       = GETPOST('pseudonym');
			if (empty($error))
			{
				if ( $object->update($_POST["id"], $user) > 0)
				{
					$action = '';
					$_GET["id"] = $_POST["id"];
		//$mesg = '<div class="ok">Fiche mise a jour</div>';
				}
				else
				{
					$action = 'edit';
					$_GET["id"] = $_POST["id"];
					$mesg = '<div class="error">'.$object->error.'</div>';
				}
			}
		}
		else
		{
			$action = 'edit';
			$_GET["id"] = $_POST["id"];
			$mesg = '<div class="error">'.$object->error.'</div>';
		}
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
			$object->fk_pac = $tmparray['fk_pac'];
			$objpac->fetch($object->fk_pac);
			$object->gestion = $tmparray['gestion'];
			$object->fk_father = $tmparray['fk_father'];
			$object->nom = $tmparray['nom'];
			$object->nro_activity = $tmparray['nro_activity'];
			$object->date_activity = $tmparray['date_activity'];
			if ($objpac->id == $object->fk_pac)
			{
				$object->label  = $objpac->nom;
				$object->amount = $objpac->amount;

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
	}


	?>