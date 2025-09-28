<?php

if ($modal == 'fichecommitted')
{
	//deletecomp
	if ($action=='deletecomp' &&
		$user->rights->poa->comp->crear &&
		$_POST["cancel"] <> $langs->trans("Cancel")
		)
	{
		$error = 0;
		//buscamos en poapartidapredet
		$objppd->fetch($idpd);
		if ($idpd == $objppd->id)
		{
			if ($objppd->fk_contrat == $idrc)
			{
				$db->begin();
				$fk_poa_partida_comp = $objppd->fk_poa_partida_comp;
				$objppd->fk_contrat = 0;
				$objppd->fk_contrato = 0;
				$objppd->fk_poa_partida_com = 0;
				$objppd->quant_adj = 0;
				$objppd->amount = 0;
				$objppd->statut = 0;
				$res = $objppd->update($user);
				if (!$res >0) $error++;
				if (!$error)
				{
					//buscamos el comprometido
					$objcom->fetch($fk_poa_partida_comp);
					if ($objcom->id == $fk_poa_partida_comp)
					{
						$res = $objcom->delete($user);
						if (!$res >0) $error++;
					}
				}
				if (!$error)
				{
					$db->commit();
					header("Location: fiche_pas1.php?id=".$id.'&idrc='.$idrc.'&action=selcon');
					exit;
				}
				else
					$db->rollback();
			}
		}
		$action = '';
	}

	// Addcontrat
	if ($action == 'add' && $user->rights->poa->comp->crear && $_POST["cancel"] <> $langs->trans("Cancel"))
	{
		$error = 0;
		$objpcon->fk_poa_process = $idProcess;
		$objpcon->fk_contrat = GETPOST('fk_contrat');
		$fk_contrat_exist    = GETPOST('fk_contrat_exist');
		if (empty($objpcon->fk_contrat))
		{
			$objpcon->fk_contrat = $fk_contrat_exist;
		}
		$objpcon->date_create = dol_now();
		$objpcon->fk_user_create = $user->id;
		$objpcon->tms = dol_now();
		$objpcon->statut = 0;
		if (empty($objpcon->fk_contrat))
		{
			$error++;
			$mesg.='<div class="error">'.$langs->trans("Errorcontratisrequired").'</div>';
		}
		if (empty($error))
		{
			//buscamos si existe poa_process y contrat con estado 0
			$db->begin();
			$objprocon = new Poaprocesscontrat($db);
			if ($objprocon->fetch('',$objpcon->fk_poa_process,$objpcon->fk_contrat)>0)
			{
				if ($objprocon->fk_poa_process == $objpcon->fk_poa_process &&
					$objprocon->fk_contrat == $objpcon->fk_contrat)
				{
					$idrc = $objprocon->id;
				}
				else
				{
					//registro nuevo
					$idrc = $objpcon->create($user);
				}
			}
			else
			{
				//registro nuevo
				$idrc = $objpcon->create($user);
			}
			if ($idrc > 0)
			{
				$_SESSION['aListcont'][$idrc] = $objpcon->fk_contrat;
				//buscamos el proceso
				if ($object->fetch($objact->fk_prev))
				{
					$object->statut = 2;
						//2 comprometido
					$res = $object->update($user);
					if ($res<=0) $error++;
				}
					//registramos las cantidades y valores adjudicados
				$aQuant_adj = GETPOST('quant_adj');
				$aAmount = GETPOST('amount');
				$aAmountPart = GETPOST('amountPart');
				if (is_array($aQuant_adj))
				{
					foreach((array) $aQuant_adj AS $idreg => $value)
					{
						//buscamos el registro
						if ($objppd->fetch($idreg))
						{
							if ($aAmount[$idreg]>0)
							{
								if ($aAmount[$idreg] <= $aAmountPart[$idreg])
								{
									$objppd->quant_adj = $value;
									$objppd->amount = $aAmount[$idreg];
									//$objppd->fk_contrat = $idc;
									$resppd = $objppd->update($user);
									if ($resppd<=0)
										$error++;
								}
								else
									$error++;
							}
						}
						else
							$error++;
					}
				}

				if (empty($error))
				{
					$db->commit();
					//echo '<hr>antes de salir con '.$id.' '.$idrc;
					//exit;
					header("Location: ".$_SERVER['PHP_SELF']."?ida=".$ida.'&id='.$id.'&idrc='.$idrc.'&action=selcon&modal=fichecommitted');
					exit;
				}
			}
			//echo '<hr>errr '.$error;
			//exit;
			$db->rollback();
			$action = 'create';
			$mesg='<div class="error">'.$obpcon->error.'</div>';
		}
		else
		{
			if ($error)
				$action="create";
				// Force retour sur page creation
		}
	}

	//uppdf
	if ($action == 'uppdf')
	{
		if ($objcont->fetch($_POST["cid"])>0)
		{
			// Logo/Photo save
			$dir     = $conf->contrat->dir_output.'/'.$objcont->ref;

			$file_OKfin = is_uploaded_file($_FILES['docpdf']['tmp_name']);
			if ($file_OKfin)
			{

				if (doc_format_supported($_FILES['docpdf']['name']) > 0)
				{
					dol_mkdir($dir);
					if (@is_dir($dir))
					{
						$newfile=$dir.'/'.dol_sanitizeFileName($_FILES['docpdf']['name']);
						$newfile=$dir.'/'.dol_sanitizeFileName($cid.'.pdf');
						$result = dol_move_uploaded_file($_FILES['docpdf']['tmp_name'], $newfile, 1);
						if (! $result > 0)
						{
							$errors[] = "ErrorFailedToSaveFile";
						}
						else
						{
							$imgThumbSmall = vignette($newfile, $maxwidthsmall, $maxheightsmall, '_small', $quality);
							$imgThumbMini = vignette($newfile, $maxwidthmini, $maxheightmini, '_mini', $quality);
						}
						header("Location: ".$_SERVER['PHP_SELF']."?id=".$id.'&idc='.$idc.'&idrc='.$idrc.'&action=selcon&dol_hide_leftmenu=1');
						exit;
					}
				}
				else
				{
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
					case 3:
						//uploaded file was only partially uploaded
					$errors[] = "ErrorFilePartiallyUploaded";
					break;
				}
			}
		}
	}
	// updateorder
	if ($action == 'updateorder' && $user->rights->poa->comp->crear && $_POST["cancel"] <> $langs->trans("Cancel"))
	{
		$error = 0;
		if($objpcon->fetch(GETPOST('idc')))
		{
			$objpcon->date_order_proceed = dol_mktime(12, 0, 0, GETPOST('di_month'),GETPOST('di_day'),GETPOST('di_year'));
			$objpcon->tms = date('YmdHis');
			//$objpcon->statut = 0;
			if (empty($error))
			{
				$res = $objpcon->update($user);
				if ($res > 0)
				{
					header("Location: fiche_pas1.php?id=".$id);
					exit;
				}
				$action = 'editop';
				$mesg='<div class="error">'.$objpcon->error.'</div>';
			}
			else
			{
				$mesg='<div class="error">'.$objpcon->error.'</div>';
				if ($error)
					$action="editop";
			 // Force retour sur page creation
			}
		}
		else
		{
			$mesg='<div class="error">'.$objpcon->error.'</div>';
			if ($error)
				$action="editop";
		 // Force retour sur page creation
		}
	}

	// Cancel process
	if ($action == 'confirm_cancel' && $_REQUEST["confirm"] == 'yes' && $user->rights->poa->comp->nul)
	{
		if ($object->fetch($_REQUEST["id"])>0)
		{
			if ($objprev->fetch($idp)>0)
			{
				$objcom->getlist($idp);

				if (count($objcom->array) > 0)
				{
					$array = $objcom->array;
					$db->begin();
					$error = 0;
					if ($objpcon->fetch($idrc)>0)
					{
						//cambiamos de estado tabla poa_process_contrat
						$objpcon->statut = 2;
						//anulado
						$result = $objpcon->update($user);
						if ($result <= 0) $error++;
					}
		  //buscamos los items del preventivo para actualizar el contrato registrado
					$objpp->getlist($idp,'S');
		  //que tenga valor en amount

					if (count($objpp->array) > 0)
					{
						foreach((array) $objpp->array AS $l => $objpp_)
						{
			  //buscamos los productos de la partida preventivo
							$objppd->getlist($objpp_->id,$idc,1);
							if (count($objppd->array)>0)
							{
								foreach((array) $objppd->array AS $k => $objppd_)
								{
				  //vamos actualizando cada registro quitando el fk_contrat, quant_adj, amount, statut = 1
									$objppdc = new Poapartidapredet($db);
									if ($objppdc->fetch($objppd_->id)>0)
									{
										if ($objppdc->id == $objppd_->id)
										{
											$objppdc->fk_contrat = 0;
											$objppdc->fk_contrato = 0;
											$objppdc->quant_adj = 0;
											$objppdc->amount = 0;
											$objppdc->statut = 1;
											$result = $objppdc->update($user);
											if ($result <= 0)
												$error++;
										}
										else
											$error++;
									}
									else
										$error++;
								}
							}
						}
					}
		  //actualizamos los comprometidos a anulados
					foreach ((array) $array AS $i => $objcomp)
					{
						if ($objcomp->fk_contrat == GETPOST('idrc'))
						{
							$objcom->fetch($objcomp->id);
							$objcom->statut = -1;
							$result = $objcom->update($user);
							if ($result <=0)
								$error++;
						}
					}
					if (empty($error))
					{
						$db->commit();
						header("Location: ".DOL_URL_ROOT.'/poa/process/fiche_pas1.php?id='.$id);
						exit;
					}
					else
					{
						$db->rollback();
						$mesg='<div class="error">'.$langs->trans('Error, no complete process').'</div>';
						$action='';
					}

				}
			}
		}
	}

//confirm updateadvance
	if ($action == 'confirm_updateadvance' && $_REQUEST["confirm"] == 'yes' && $user->rights->poa->comp->mod)
	{
		if ($object->fetch($_REQUEST["id"])>0)
		{
			if ($objpcon->fetch($_REQUEST['idrc'])>0)
			{
				if ($objcont->fetch($objpcon->fk_contrat)>0)
				{
					$_POST['options_advance'] = $_GET['options_advance'];
					$extralabels = $extrafields->fetch_name_optionals_label($objcont->table_element);
					$ret = $extrafields->setOptionalsFromPost($extralabels, $objcont, 'advance');
					if ($ret < 0)
						$error ++;
					if (! $error)
					{
						$result = $objcont->insertExtraFields();
						if ($result < 0) {
							$error ++;
						}
					}
					else if ($reshook < 0)
						$error ++;
				}

				$action='';
				if ($error) {
					$action = 'edit_extras';
					setEventMessage($object->error,'errors');
				}
			}
		}
	}
		// updateprod
	if ($action == 'updateprod' && $user->rights->poa->comp->mod && $_POST["cancel"] <> $langs->trans("Cancel") && ($_POST["approved"] <> $langs->trans("Save and approve") || empty($_POST['approved'])))
	{
		$error = 0;
		$aQuant_adj = GETPOST('quant_adj');
		$aAmount = GETPOST('amount');
		$aAmountPart = GETPOST('amountPart');
		$idrc = GETPOST('idrc','int');
		if (is_array($aQuant_adj))
		{
			$db->begin();
			foreach((array) $aQuant_adj AS $idreg => $value)
			{
				//buscamos el registro
				if ($objppd->fetch($idreg))
				{
					if ($aAmount[$idreg]>0)
					{
						if ($aAmount[$idreg] <= $aAmountPart[$idreg])
						{
							$objppd->quant_adj = $value;
							$objppd->amount = $aAmount[$idreg];
							//$objppd->fk_contrat = $idc;
							$objppd->update($user);
						}
						else
							$error++;
					}
				}
				else
					$error++;
			}
			if (empty($error))
				$db->commit();
			else
				$db->rollback();
		}
		$action = 'selcon';
	}

	// updateprod aprobacion
	if ($action == 'updateprod' && $user->rights->poa->comp->crear && $_POST["approved"] == $langs->trans("Save and approve"))
	{
		$object->fetch($id);
		$idp = $_SESSION['aListip'][$idProcess]['idPrev'];
		//$idc = $_SESSION['aListcont'][$id];
		$objprev = new Poaprev($db);
		$objprev->fetch($idp);
		$db->begin();
		$error = 0;
		$aQuant_adj = GETPOST('quant_adj');
		$aAmount = GETPOST('amount');
		$aAmountPart = GETPOST('amountPart');
		if($objpcon->fetch(GETPOST('idrc')))
		{
			$idrc = $objpcon->id;
			$idc = $objpcon->fk_contrat;
			$objpcon->statut = 1;
			if ($objpcon->update($user) > 0)
			{
				if (is_array($aAmount))
				{
					foreach((array) $aAmount AS $idreg => $value)
					{
					//buscamos el registro
						if ($objppd->fetch($idreg))
						{
							if ($value > 0)
							{
								if ($value <= $aAmountPart[$idreg])
								{
									$objppd->quant_adj = $aQuant_adj[$idreg];
									$objppd->amount = $value;
									$objppd->fk_contrat = $idrc;
									$objppd->fk_contrato = $idc;
									$objppd->statut=2;
									if($objppd->update($user) <= 0) $error++;
									//buscamos la partida para registro en comprometido
									if ($objpre->fetch($objppd->fk_poa_partida_pre))
									{
										$objpre->getsum_str_part_det2($objprev->gestion,
											$objpre->fk_structure,
											$objpre->fk_poa,
											$objpre->id,
											$idc,
											$objpre->partida);
										if ($objpre->fk_poa > 0)
										{
								   			//esta correcto
										}
										else
										{
											$error++;
											$mesg.='<div class="error">'.$langs->trans("Error, corregir el preventivo").'</div>';
										}

										$total = $objpre->total;
										//buscamos en comprometido
										if ($objcom->fetch_pcp2($objppd->fk_poa_partida_pre,$idp,$idc,$objpp->partida) )
										{
											if ($objcom->fk_poa_partida_pre == $objppd->fk_poa_partida_pre && $objcom->fk_poa_prev == $idp && $objcom->fk_contrato == $idc && $objcom->partida == $objpre->partida )
											{
									   			//existe y actualizamos
												$objcom->fk_poa = $objpre->fk_poa;
												$objcom->amount = $total;
												$objcom->fk_contrat = $idrc;
												$objcom->fk_contrato = $idc;
												if(!$objcom->update($user)>0) $error++;
											}
											else
											{
									   			//registramos en comprometido
												$objcom->fk_poa_partida_pre = $objppd->fk_poa_partida_pre;
												$objcom->fk_poa_prev = $objpre->fk_poa_prev;
												$objcom->fk_structure = $objpre->fk_structure;
												$objcom->fk_poa = $objpre->fk_poa;
												$objcom->fk_contrat = $idrc;
												$objcom->fk_contrato = $idc;
												$objcom->partida = $objpre->partida;
												$objcom->amount = $total;
												$objcom->date_create = dol_now();
												$objcom->tms = dol_now();
												$objcom->statut = 1;
												$objcom->active = 1;
												$idcom = $objcom->create($user);
												if ($idcom > 0)
												{
													$objppd->fk_poa_partida_com=$idcom;
													if($objppd->update($user) <= 0)
														$error++;
												}
												else
													$error++;
											}
										}
										else
											$error++;
									}
									else
										$error++;
								}
							}
						}
						else
							$error++;
					}
				}
			}
			else
				$error++;
		}
		if (empty($error))
		{
			$db->commit();
			header("Location: ".$_SERVER['PHP_SELF']."?ida=".$ida);
			exit;
		}
		else
		{
			$db->rollback();
			$action = 'selcon';
		}
	}

		// updatecomp modificacion del comprometido
	if ($action == 'update' && $user->rights->poa->comp->mod)
	{
	$object->fetch($id);//process
	$objprev->fetch($idp);
	$objpcon->fetch($idrc); //poaprocescontrat
	if ($objpcon->id == $idrc)
		$idc = $objpcon->fk_contrat;
	$db->begin();
	$error = 0;
	$aQuant_adj = GETPOST('quant_adj');
	$aAmount = GETPOST('amount');
	$aAmountPart = GETPOST('amountPart');
	if (is_array($aAmount))
	{
		foreach((array) $aAmount AS $idreg => $value)
		{
			//buscamos el registro
			if ($objppd->fetch($idreg)>0)
			{
				if ($value > 0)
				{
					if ($value <= $aAmountPart[$idreg])
					{
						$objppd->quant_adj = $aQuant_adj[$idreg];
						$objppd->amount = $value;
						$objppd->fk_contrat = $idrc;
						$objppd->fk_contrato = $idc;
						$objppd->statut=2;
						if($objppd->update($user) <= 0) $error++;


						//buscamos la partida para registro en comprometido
						//echo '<hr>'.$objppd->fk_poa_partida_pre;
						if ($objpp->fetch($objppd->fk_poa_partida_pre)>0)
						{
							// echo '<hr>'.$objppd->fk_poa_partida_pre;
							// print_r($objpp);
							$objpp->getsum_str_part_det2($objprev->gestion,
								$objpp->fk_structure,
								$objpp->fk_poa,
								$objpp->id,
								$idc,
								$objpp->partida);
							// echo '<hr>';
							// print_r($objpp);
							if ($objpp->fk_poa > 0)
							{
								//esta correcto
							}
							else
							{
								$error++;
								$mesg.='<div class="error">'.$langs->trans("Error, corregir el preventivo").'</div>';
							}

							$total = $objpp->total;
							//buscamos en comprometido
							if ($objcom->fetch_pcp2($objppd->fk_poa_partida_pre,
								$idp,
								$idc,
								$objpp->partida) )
							{
								// echo '<hr>comp ';
								// print_r($objcom);
								if ($objcom->fk_poa_partida_pre == $objppd->fk_poa_partida_pre &&
									$objcom->fk_poa_prev == $idp &&
									$objcom->fk_contrato == $idc &&
									$objcom->partida == $objpp->partida )
								{
									// echo '<br>existe';
									//existe y actualizamos
									$objcom->fk_poa = $objpp->fk_poa;
									$objcom->amount = $total;
									$objcom->fk_contrat = $idc;
									if($objcom->update($user)>0)
									{
										$objppd->fk_poa_partida_com=$objcom->id;
										if($objppd->update($user) <= 0) $error++;
									}
									else
										$error++;
								}
								else
								{
									//es necesario agregar el registro
									$objcom->fk_poa_partida_pre = $objppd->fk_poa_partida_pre;
									$objcom->fk_poa_prev = $idp;
									$objcom->fk_structure = $objpp->fk_structure;
									$objcom->fk_poa = $objpp->fk_poa;
									$objcom->fk_contrat = $idrc;
									$objcom->fk_contrato = $idc;
									$objcom->partida = $objpp->partida;
									$objcom->amount = $value;
									$objcom->date_create = dol_now();
									$objcom->statut = 1;
									$objcom->active = 1;
									$res = $objcom->create($user);
									// echo '<hr>res '.$res;print_r($objcom);
									if (!$res > 0) $error++;
								}
							}
							else
								$error++;
						}
						else
						{
							$error++;
						}
					}
				}
			}
			else
				$error++;
		}
	}
	if (empty($error))
	{
		$db->commit();
		header("Location: fiche_pas1.php?id=".$id.'&dol_hide_leftmenu=1');
		exit;
	}
	else
	{
		$db->rollback();
		$action = 'selcon';
	}
}


}
?>