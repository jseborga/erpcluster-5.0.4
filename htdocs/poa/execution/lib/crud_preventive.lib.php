<?php
$idp = GETPOST('idp','int');

if ($modal == 'fichepreventive')
{
	$id = GETPOST('id','int');
	require_once DOL_DOCUMENT_ROOT.'/poa/execution/class/poaprevseg.class.php';
	$object_s = new Poaprevseg($db);
	// Add
	if ($action == 'add' && $user->rights->poa->prev->crear)
	{
		//poa prev seguimiento

		$error = 0;
		//$date_preventive = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
		$aDate = dol_getdate(dol_now());
		$date_preventive = dol_stringtotime(substr(GETPOST('di_'),0,10).' '.(strlen($aDate['hours'])==1?'0'.$aDate['hours']:$aDate['hours']).':'.(strlen($aDate['minutes'])==1?'0'.$aDate['minutes']:$aDate['minutes']));

		$object->gestion = GETPOST('gestion');
		$fk_father       = GETPOST('fk_father')+0;
		//nro_preventivo

		//agrego nuevo seguimiento
		$object_s->initAsSpecimen();
		$object_s->fk_father = 0;
		$object_s->date_create = dol_now();
		$object_s->fk_user_create = $user->id;
		$object_s->tms = dol_now();
		$object_s->statut = 1;

		if (!empty($fk_father))
		{
			$objnew = new Poaprev($db);
			if ($objnew->fetch('',$fk_father,$object->gestion)>0)
			{
				//busco
				if ($objnew->nro_preventive == $fk_father)
				//verifico
					$object_s->fk_father = $objnew->id;
				else
					$object_s->fk_father = 0;
			}
			else
				$object_s->fk_father = 0;
		}
		else
			$object_s->fk_father = 0;
		//preventive gestion pasada
		$nro_preventive_ant = GETPOST('nro_preventive_ant');
		$gestion_ant = GETPOST('gestion_ant');
		if (!empty($nro_preventive_ant) && !empty($gestion_ant))
		{
			$objnew = new Poaprev($db);
			if ($objnew->fetch('',$nro_preventive_ant,$gestion_ant)>0)
			{
				if ($objnew->nro_preventive == $nro_preventive_ant && $objnew->gestion == $gestion_ant)
					$object_s->fk_prev_ant = $objnew->id;
				else
					$object_s->fk_prev_ant = 0;
			}
			else
				$object_s->fk_prev_ant = 0;
		}
		else
			$object_s->fk_prev_ant = 0;
		//armamos el preventivo nuevo
		$object->fk_pac          = GETPOST('fk_pac')+0;
		$object->fk_area         = GETPOST('fk_area');
		$object->nro_preventive  = GETPOST('nro_preventive');
		$object->priority        = GETPOST('priority');
		$object->code_requirement= GETPOST('code_requirement');
		$object->date_preventive = $date_preventive;
		$object->fk_user_create  = GETPOST('fk_user_create');
		$object->label           = GETPOST('label');
		$object->pseudonym       = GETPOST('pseudonym');

		if (empty($object->label))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("Errorlabelisrequired").'</div>';
		}
		$object->date_create = dol_now();
		$object->tms = dol_now();
		if ($object->fk_user_create <= 0)
			$object->fk_user_create = $user->id;
		$object->amount = 0;
		$object->entity = $conf->entity;
		$object->statut = 0;
		$object->active = 1;

		if (empty($object->nro_preventive))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("Errornropreventiveisrequired").'</div>';
		}
		if (empty($error))
		{
			$db->begin();
			$id = $object->create($user);
			if ($id > 0)
			{
				//agregamos al preventivo seguimiento
				$object_s->fk_prev = $id;
				$res = $object_s->create($user);
				//actualizando en activity
				if ($objact->fetch(GETPOST('fk_activity'))>0)
				{
					$objact->fk_prev = $id;
					$objact->nro_activity = $object->nro_preventive;
					$idr = $objact->update($user);

					if ($idr>0)
					{
						$db->commit();
						header("Location: ".$_SERVER['PHP_SELF']."?ida=".$objact->id.'&modal=fichepreventive'.'&fk_poa='.$fk_poa);
						exit;
					}
				}
				else
				{
					//$action = 'create';
					$mesg='<div class="error">'.$objact->error.'</div>';
				}
			}
			$db->rollback();
			//$action = 'create';
			$mesg='<div class="error">'.$object->error.'</div>';
		}
		else
		{
		//if ($error) $action="create";   // Force retour sur page creation
		}
	}
	// Modification preventive
	if ($action == 'update' && $_POST["cancel"] <> $langs->trans("Cancel"))
	{
		$error = 0;
		if ($object->fetch($id)>0)
		{
			//busco el preventivo seguimiento
			$object_s->fetch('',$id);
			$lPrevseg = false;
			//no existe
			if ($object_s->fk_prev == $object->id) $lPrevseg = true;
	  		//si existe
			//actualizamos la actividad
			//buscamos si ya se seleccion la actividad
			//si no existe guardamos
			$fk_activity = $ida;
			$db->begin();
			if ($objact->fetch($ida)>0 && $fk_activity > 0)
			{
				if (empty($objact->fk_prev) || $objact->fk_prev == $id)
				{
					$objact->fk_prev = $id;
					$res = $objact->update($user);
					if ($res <=0)
					{
						$error++;
						$mesg.='<div class="error">'.$objact->error.'</div>';
						$action = 'edit';
					}
				}
				else
				{
					$error++;
					$mesg.='<div class="error">'.$langs->trans('Errortheactivityalreadyhaspreventive').'</div>';
					$action = 'edit';
				}
			}
			//$date_preventive = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
			$aDate = dol_getdate(dol_now());
			$date_preventive = dol_stringtotime(substr(GETPOST('di_'),0,10).' '.(strlen($aDate['hours'])==1?'0'.$aDate['hours']:$aDate['hours']).':'.(strlen($aDate['minutes'])==1?'0'.$aDate['minutes']:$aDate['minutes']));

			$object->gestion = GETPOST('gestion');
			$fk_father       = GETPOST('fk_father')+0;
			//$lPrevseg = false;
			//$lPrevsegnew = false;
			if (!empty($fk_father))
			{
				$objnew = new Poaprev($db);
				if ($objnew->fetch('',$fk_father,$object->gestion)>0)
				{
					if ($objnew->nro_preventive == $fk_father)
					{
						if ($lPrevseg)
						{
			  				//actualizo
							$object_s->fk_father = $objnew->id;
							$object_s->tms = dol_now();
						}
						else
						{
			  				//agrego nuevo
			  				// $lPrevsegnew = true;
							$object_s->initAsSpecimen();
							$object_s->fk_prev = $object->id;
							$object_s->fk_father = $objnew->id;
							$object_s->date_create = dol_now();
							$object_s->fk_user_create = $user->id;
							$object_s->tms = dol_now();
							$object_s->statut = 1;
						}
		  				//$object->fk_father = $objnew->id;
					}
					else
						$object_s->fk_father = 0;
				}
				else
					$object_s->fk_father = 0;
			}
			else
			{
				$object_s->fk_father = 0;
				$object_s->fk_prev = $object->id;
				$object_s->date_create = dol_now();
				$object_s->tms = dol_now();
				$object_s->fk_user_create = $user->id;
				$object_s->statut = 1;
			}
				//preventive gestion pasada
			$nro_preventive_ant = GETPOST('nro_preventive_ant');
			$gestion_ant = GETPOST('gestion_ant');
			if (!empty($nro_preventive_ant) && !empty($gestion_ant))
			{
				if (!$lPrevseg)
				{
					//se debe crear
					$object_s->initAsSpecimen();
					$object_s->fk_prev = $object->id;
					$object_s->fk_father = 0;
					$object_s->date_create = dol_now();
					$object_s->fk_user_create = $user->id;
					$object_s->tms = dol_now();
					$object_s->statut = 1;
				}
				$objnew = new Poaprev($db);
				if ($objnew->fetch('',$nro_preventive_ant,$gestion_ant)>0)
				{
					if ($objnew->nro_preventive == $nro_preventive_ant && $objnew->gestion == $gestion_ant)
						$object_s->fk_prev_ant = $objnew->id;
					else
						$object_s->fk_prev_ant = 0;
				}
				else
					$object_s->fk_prev_ant = 0;
			}
			else
				$object_s->fk_prev_ant = 0;

				//	exit;
			$object->fk_pac          = GETPOST('fk_pac')+0;
			$object->fk_area         = GETPOST('fk_area');
			$object->nro_preventive  = GETPOST('nro_preventive');
			$object->priority        = GETPOST('priority');
			$object->code_requirement= GETPOST('code_requirement');
			$object->date_preventive = $date_preventive;
			$object->fk_user_create  = GETPOST('fk_user_create');
			$object->label           = GETPOST('label');
			$object->pseudonym       = GETPOST('pseudonym');
			if (empty($error))
			{
				$res = $object->update($_POST["id"], $user);
				if ( $res > 0)
				{
					//actualizamos seguimiento
					if ($lPrevseg)
						$res = $object_s->update($user);
					else
						$res = $object_s->create($user);
					if ($res > 0)
					{
						$db->commit();
						$action = '';
						$_GET["id"] = $_POST["id"];
						header("Location: ".$_SERVER['PHP_SELF'].'?ida='.$ida.'&modal=fichepreventive&id='.$id);
						exit;
					}
					else
					{
						$db->rollback();
						$action = 'edit';
						$_GET["id"] = $_POST["id"];
						$mesg = '<div class="error">'.$object_s->error.'</div>';
					}
				}
				else
				{
					$db->rollback();
					$action = 'edit';
					$_GET["id"] = $_POST["id"];
					$mesg = '<div class="error">'.$object->error.'</div>';
				}
			}
			else
				$db->rollback();
		}
		else
		{
			$action = 'edit';
			$_GET["id"] = $_POST["id"];
			$mesg = '<div class="error">'.$object->error.'</div>';
		}
	}

		// Addpartida
	if ($action == 'addpartida' && $user->rights->poa->prev->crear)
	{
		$id = $objact->fk_prev;
		$error = 0;
		$objpre->fk_poa_prev = $id;
		$objpre->fk_poa       = GETPOST('fk_poa');
		$objpre->fk_structure = GETPOST('fk_structure');
		$objpre->partida = GETPOST('partida');
		$objpre->amount  = GETPOST('amount');
		$objpre->tms     = dol_now();
		$objpre->statut  = 1;
		$objpre->active  = 0;

		if (empty($objpre->fk_structure))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("Errorpoaisrequired").'</div>';
		}
		if (empty($objpre->partida))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("Errorpartidaisrequired").'</div>';
		}
		if (empty($objpre->amount))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("Erroramountisrequired").'</div>';
		}
		if (empty($error))
		{
			$idp = $objpre->create($user);
			if ($idp > 0)
			{
				header("Location: ".$_SERVER['PHP_SELF']."?ida=".$ida.'&modal=fichepreventive');
				exit;
			}
			$action = '';
			$mesg='<div class="error">'.$objprev->error.'</div>';
		}
		else
		{
			if ($error) $action="";
		// Force retour sur page creation
		}
	}

// updatepartida
	if ($action == 'updatepartida' && $user->rights->poa->prev->mod)
	{
		$error = 0;
	//buscamos
		if ($objpre->fetch($idp)>0)
		{
			$objpre->fk_poa_prev = $id;
			$objpre->fk_poa       = GETPOST('fk_poa');
			$objpre->fk_structure = GETPOST('fk_structure');
			$objpre->partida = GETPOST('partida');
			$objpre->amount  = GETPOST('amount');
			$objpre->statut  = 1;
			$objpre->active  = 0;

			if (empty($objpre->fk_structure))
			{
				$error++;
				$mesg.='<div class="error">'.$langs->trans("Errorpoaisrequired").'</div>';
			}
			if (empty($objpre->partida))
			{
				$error++;
				$mesg.='<div class="error">'.$langs->trans("Errorpartidaisrequired").'</div>';
			}
			if (empty($objpre->amount))
			{
				$error++;
				$mesg.='<div class="error">'.$langs->trans("Erroramountisrequired").'</div>';
			}

			if (empty($error))
			{
				$resp = $objpre->update($user);
				if ($resp > 0)
				{
					header("Location: ".$_SERVER['PHP_SELF']."?ida=".$ida.'&modal=fichepreventive');
					exit;
				}
				$action = 'editpartida';
				$mesg='<div class="error">'.$objpre->error.'</div>';
			}
			else
			{
				if ($error) $action="";
			// Force retour sur page creation
			}
		}
		else
		{
			$action = 'editpartida';
			$mesg='<div class="error">'.$objpre->error.'</div>';
		}
	}


// Addmodify //disminuciones del preventivo
	if ($action == 'addmodify' && $user->rights->poa->prev->dis)
	{
		$error = 0;
	//recibimos

		$aPartidaPre = GETPOST('amount');
		foreach ((array) $aPartidaPre AS $idp => $value)
		{
			$objpartidapre = new Poapartidapre($db);
			if ($objpartidapre->fetch($idp) && $value > 0)
			{
				$objpre->fk_poa       = $objpartidapre->fk_poa;
				$objpre->fk_structure = $objpartidapre->fk_structure;
				$objpre->fk_poa_prev  = $objpartidapre->fk_poa_prev;
				$objpre->partida = $objpartidapre->partida;
				$objpre->amount  = $value * -1;
				$objpre->tms     = dol_now();
				$objpre->statut  = 1;
				$objpre->active  = 1;
				$result = $objpre->create($user);
			}
		}
		header("Location: ".$_SERVER['PHP_SELF']."?ida=".$ida.'&modal=fichepreventive');
		exit;
	}

// Addmodify //disminuciones del preventivo
	if ($action == 'addmodifyr' && $user->rights->poa->prev->dis)
	{
		$error = 0;
	//recibimos
	//$objprev = new Poapartidapre($db);
		$res = $objpre->fetch(GETPOST('idpp'));
		if ($res>0 && $objpre->id == GETPOST('idpp'))
		{
			$objpre->amount = GETPOST('amount') * -1;
			$objpre->tms = dol_now();
			$result = $objpre->update($user);

		}
		header("Location: ".$_SERVER['PHP_SELF']."?ida=".$ida.'&modal=fichepreventive');
		exit;
	}

// Addpartidaprod
	if ($action == 'addpartidaprod' && $user->rights->poa->prev->crear)
	{
		$error = 0;
		$objppd->fk_poa_partida_pre = GETPOST('idp');
		$objppd->detail       = GETPOST('detail');
		$objppd->quant = GETPOST('quant');
		$objppd->amount_base = GETPOST('amount_base');
		$objppd->fk_product = 0;
		$objppd->fk_contrato = 0;
		$objppd->amount = 0;
		$objppd->tms     = dol_now();
		$objppd->statut  = 1;
		if (empty($objppd->detail))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("Errordetailisrequired").'</div>';
		}
		if (empty($objppd->quant))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("Errorquantisrequired").'</div>';
		}
		if (empty($error))
		{
			$idpd = $objppd->create($user);
			if ($idpd > 0)
			{
				header("Location: ".$_SERVER['PHP_SELF']."?ida=".$ida
					."&idp=".$_POST['idp'].
					"&action=eproduct&modal=fichepreventive");
				exit;
			}
			$action = 'eproduct';
			$_GET['idp'] = $_POST['idp'];
			$mesg='<div class="error">'.$objppd->error.'</div>';
		}
		else
		{
			if ($error)
			{
				$action="eproduct";
			// Force retour sur page creation
				$_GET['idp'] = $_POST['idp'];
			}
		}
	}

// Updatepartidaprod
	if ($action == 'updatepartidaprod' && $user->rights->poa->prev->crear)
	{
		$idppp = GETPOST('idppp');
		if ($objppd->fetch($idppp)>0)
		{
			$error = 0;
			$objppd->fk_poa_partida_pre = GETPOST('idp');
			$objppd->detail       = GETPOST('detail');
			$objppd->quant = GETPOST('quant');
			$objppd->amount_base = GETPOST('amount_base');
			$objppd->fk_product = 0;
			//$objprevdet->amount = 0;
			$objppd->tms     = dol_now();
			$objppd->statut  = 1;
			if (empty($objppd->detail))
			{
				$error++;
				$mesg.='<div class="error">'.$langs->trans("Errordetailisrequired").'</div>';
			}
			if (empty($objppd->quant))
			{
				$error++;
				$mesg.='<div class="error">'.$langs->trans("Errorquantisrequired").'</div>';
			}
			if (empty($error))
			{
				$res = $objppd->update($user);
				if ($res > 0)
				{
					header("Location: ".$_SERVER['PHP_SELF']."?ida=".$ida
						."&idp=".$_POST['idp'].
						"&action=eproduct&modal=fichepreventive");
					exit;
				}
				$action = 'eproduct';
				$_GET['idp'] = $_POST['idp'];
				$_GET['idppp'] = $_POST['idppp'];
				$mesg='<div class="error">'.$objppd->error.'</div>';
			}
			else
			{
				if ($error)
				{
					$action="eproduct";
				// Force retour sur page creation
					$_GET['idp'] = $_POST['idp'];
					$_GET['idppp'] = $_POST['idppp'];
				}
			}
		}
	}


//uppdf
	if ($action == 'uppdf')
	{
		$idreg = GETPOST('idreg');
		$linklast = GETPOST('linklast','alpha');
		if ($object->fetch($idreg)>0)
		{
		// Logo/Photo save
			$dir     = $conf->poa->dir_output.'/execution/pdf';
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
						// Create small thumbs for company (Ratio is near 16/9)
						// Used on logon for example
							$imgThumbSmall = vignette($newfile, $maxwidthsmall, $maxheightsmall, '_small', $quality);
						// Create mini thumbs for company (Ratio is near 16/9)
						// Used on menu or for setup page for example
							$imgThumbMini = vignette($newfile, $maxwidthmini, $maxheightmini, '_mini', $quality);
						}
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

// validate prev //opcion nueva
	if ($action == 'confirm_validate_prev' && $object->id == $id && $user->rights->poa->prev->val)
	{
		$error = 0;
		//	$res = $object->fetch($id]);
		$fk_type_con = $_REQUEST["fk_type_con"];
		$db->begin();

		//obtener la suma
		$total = $objpre->getsum($id);
		//cambiando a validado
		$object->amount = $total;
		$object->statut = 1;
		//$object->ref = $object->codref;
		//update
		$res1 = $object->update($user);
		if ($res1<=0)
		{
			$error++;
			$mesg.='<div class="error">'.$object->error.'</div>';
		}
		if (empty($error))
		{
			$db->commit();
			header("Location: ".$_SERVER['PHP_SELF']."?ida=".$ida);
			exit;
		}
		else
			$db->rollback();
		$action='';
	}
	// Delete prev modification
	if ($action == 'confirm_delete_product' && $user->rights->poa->prev->del)
	{
		$error = 0;
		if ($objprevdet->fetch($_REQUEST["idppp"])>0)
		{
			$idp = $objprevdet->fk_poa_partida_pre;
			$res = $objprevdet->delete($user);
			if ($res > 0)
			{
				header("Location: ".$SERVER['PHP_SELF'].'?ida='.$ida.'&idp='.$_REQUEST['idp'].'&action=eproduct&modal=fichepreventive');
				exit;
			}
			else
			{
				$mesg='<div class="error">'.$objprevdet->error.'</div>';
				$action='';
			}
		}
		else
		{
			$mesg='<div class="error">'.$objprevdet->error.'</div>';
			$action = '';
		}
	}

}
?>