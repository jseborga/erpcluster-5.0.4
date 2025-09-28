<?php
$now = dol_now();
if ($action == 'confirm_validate' && $_REQUEST['confirm'] == 'yes' && $user->rights->budget->val)
{
	if ($id == $object->id)
	{
		$db->begin();
		$object->version = 0;
		$object->fk_statut = 1;
		$res = $object->update($user);
		if ($res <=0)
		{
			$error++;
			setEventMessages($object->error,$object->errors,'errors');
		}
		if (!$error)
		{
			$db->commit();
		}
		else
		{
			$db->rollback();
		}
		$action = '';
	}
}

if ($action == 'priori' && $user->rights->budget->budi->prod)
{
	$idr = GETPOST('idr');
	$idreg = GETPOST('idreg');
	$objectbtr->fetch($idreg);
	$code_structure = $objectbtr->code_structure;
	$error = 0;
	$db->begin();
	//cambiamos todos a 0 del item
	$res = $objectbtr->update_priority($user, $idr);
	if ($res<=0)
	{
		$error++;
		setEventMessages($objectbtr->error,$objectbtr->errors,'errors');
	}
	//cambiamos todos a 0 del item
	$res = $objectbtr->update_priority($user, $idr,1,$idreg);
	if ($res<=0)
	{
		$error++;
		setEventMessages($objectbtr->error,$objectbtr->errors,'errors');
	}
	if (!$error)
	{
		$res = $objectbtr->calculate_performance($user, $idr,$code_structure);
		if ($res<=0)
		{
			$error++;
		}
	}
	if (!$error)
	{
		$db->commit();
		setEventMessages($langs->trans('Satisfactoryupdate'),null,'mesgs');
		header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$idr.'&action=viewit');
		exit;
	}
	else
	{
		$db->rollback();
		$action = 'viewit';
	}
}

if ($action == 'add_productivity' && $user->rights->budget->budi->prod)
{
	$idr = GETPOST('idr');
	$idreg = GETPOST('idreg');
	$aQuant = GETPOST('quant');
	$formula = GETPOST('formula','alpha');
	$db->begin();
	$objectbtr->fetch($idreg);
	$code_structure = $objectbtr->code_structure;
	$aCode = array();
	$newFormula = $formula;
	foreach ((array) $aQuant AS $code => $value)
	{
		$newFormula = str_replace($code, $value, $newFormula);
	}
	eval('$formula_res = '.$newFormula.';');
	if ($objectbtr->id == $idreg)
	{
		$objectbtr->formula = $formula;
		$objectbtr->formula_res = $formula_res;
		$res = $objectbtr->update_formula($user);
		if ($res<=0)
		{
			$error++;
			setEventMessages($objectbtr->error,$objectbtr->errors,'errors');
		}
	}
	else
	{
		$error++;
		setEventMessages($objectbtr->error,$objectbtr->errors,'errors');
	}
	foreach ((array) $aQuant AS $code => $value)
	{
		if ($value > 0)
		{
			//verificamos si existe o no
			$resp = $objproductivity->fetch($id, $idreg,$code);
			if ($resp>0)
			{
				$objproductivity->quant = $value;
				$objproductivity->fk_user_mod = $user->id;
				$objproductivity->date_mod = dol_now();
				$objproductivity->tms = dol_now();
				$objproductivity->status = 1;
				$res = $objproductivity->update($user);
			}
			else
			{
				$objproductivity->fk_budget_task_resource = $idreg;
				$objproductivity->code_parameter = $code;
				$objproductivity->quant = $value;
				$objproductivity->fk_user_create = $user->id;
				$objproductivity->fk_user_mod = $user->id;
				$objproductivity->date_create = dol_now();
				$objproductivity->date_mod = dol_now();
				$objproductivity->tms = dol_now();
				$objproductivity->status = 1;
				$res = $objproductivity->create($user);
			}
			if ($res <=0)
			{
				$error++;
				setEventMessages($objproductivity->error,$objproductivity->errors,'errors');
			}
		}
	}
	if (!$error)
	{
		$res = $objectbtr->calculate_performance($user, $idr,$code_structure);
		if ($res<=0)
		{
			$error++;
		}
	}

	if (!$error)
	{
		$db->commit();
		header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$idr.'&action=viewit');
		exit;
	}
	else
		$db->rollback();

}

if ($action == 'confirm_clonitem' && $_REQUEST['confirm'] == 'yes' && $user->rights->budget->budi->clon)
{
	//clonamos
	$label = GETPOST('label');
	$objectdet->fetch(GETPOST('idr'));
	$idr = GETPOST('idr');
	if ($objectdet->fk_budget == $id)
	{
		//numeracion de item
		$max = $objectdet->max_task($id);
		$db->begin();

		$objectdet->id = 0;
		$objectdet->ref = $max;
		$objectdet->label = $label;
		$objectdet->fk_user_create = $user->id;
		$objectdet->fk_user_mod = $user->id;
		$objectdet->datec = dol_now();
		$objectdet->tms = dol_now();
		$nidr = $objectdet->create($user);
		if ($nidr>0)
		{
			$objectdetadd->fetch(0,$idr);
			//guardamos en la tabla adicional
			$objectdetadd->id = 0;
			$objectdetadd->fk_budget_task = $nidr;
			$objectdetadd->fk_user_create = $user->id;
			$objectdetadd->fk_user_mod = $user->id;
			$objectdetadd->date_create = dol_now();
			$objectdetadd->tms = dol_now();
			$objectdetadd->status = 0;
			$resdetadd=$objectdetadd->create($user);

			//recuperamos de budget task resource
			$filterstatic = " AND t.fk_budget_task = ".$idr;
			$res = $objectbtr->fetchAll('ASC', 't.code_structure',0,0,array(1=>1),'AND',$filterstatic);
			$lines = $objectbtr->lines;
			foreach ((array) $lines AS $j => $line)
			{
				$objectbtr->fetch($line->id);
				$objectbtr->id = 0;
				$objectbtr->ref = '(PROV)';
				$objectbtr->fk_budget_task = $nidr;
				$objectbtr->date_create = dol_now();
				$objectbtr->date_mod = dol_now();
				$objectbtr->tms = dol_now();
				$objectbtr->fk_user_create = $user->id;
				$objectbtr->fk_user_mod = $user->id;
				$objectbtr->status = 1;
				$resbtrc=$objectbtr->create($user);
				if ($resbtrc<=0)
				{
					$error++;
					// Creation KO
					setEventMessages($objectbtr->error, $objectbtr->errors, 'errors');
				}

				if (! $error)
				{
					//actualizamos el ref
					$objectbtr->ref = '(PROV)'.$objectbtr->id;
					$resup = $objectbtr->update($user);
					if ($resup<=0)
					{
						$error++;
						setEventMessages($objectbtr->error, $objectbtr->errors, 'errors');
					}
				}
				if (!$error)
				{
					$resprod = $objproductivity->import_productivity($user,$line->id,$resbtrc);
					if ($resprod <=0) $error++;
				}
			}
		}
		else
		{
			$error++;
			setEventMessages($objectdet->error,$objectdet->errors,'errors');
		}
		if (!$error)
		{
			$db->commit();
			header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id.'&idr='.$nidr.'&action=viewit');
			exit;
		}
		else
		{
			$db->rollback();
			$action = '';
		}
	}
}


if ($action == 'updateresource')
{
	$aStruct = $aStrbudget[$id];

	if (GETPOST('cancel'))
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/budget/list.php',1);
		header("Location: ".$urltogo);
		exit;
	}
	$error=0;
	$price = GETPOST('price');
	$res = $objectbtr->fetch(GETPOST('idreg'));
	if ($res<=0)
	{
		$error++;
		setEventMessages($objectbtr->error,$objectbtr->errors,'errors');
	}
		//revisar la desactivacion de la siguiente linea rqc
	//$db->begin();
	$fk_unit = GETPOST('fk_unit');
	$code_structure = GETPOST('code_structure');
		//$price = GETPOST('price');
	if (GETPOST('fk_product_budget'))
	{
		//se selecciono un producto del budget
		$fk_product_budget = GETPOST('fk_product_budget');
		$objprodb->fetch(GETPOST('fk_product_budget'));
		$ref_product = $objprodb->ref;
		$fk_product = $objprodb->fk_product;
		$fk_unit = $objprodb->fk_unit;
		$detail = $objprodb->label;
		$price = $objprodb->amount;
		$code_structure = $objprodb->code_structure;
		$price = $objprodb->amount;
	}
	else
	{
		$fk_product = GETPOST('product');
		$detail = GETPOST('refsearch');
		if ($fk_product>0 && $product->fetch($fk_product)>0)
		{
			//buscamos la categoria a la que pertenece
			$detail = $product->label;
			$ref_product = $product->ref;
			$fk_unit = $product->fk_unit;
			$aCat = $categorie->containing($fk_product, 'product', 'id');
			foreach ((array) $aCat AS $i => $fk)
			{
				if ($aStruct['aStrcatcode'][$fk])
					$code_structure = $aStruct['aStrcatcode'][$fk];
			}
		}
		else
		{
			$fk_product = 0;
			$ref_product = '';
			$code_structure = GETPOST('code_structure');
			$detail = GETPOST('search_product');
		}
		$detail = STRTOUPPER($detail);
		//verificamos si existe en producto budget
		$filter = array('UPPER(ref)'=>$detail,'UPPER(label)'=>$detail);
		$filter = array(1=>1);
		$filterstatic = '';
		if ($ref_product)
		{
			$filterstatic = ' AND (';
			$filterstatic.= " UPPER(ref) = '".$ref_product."'";
		}
		if (!empty($filterstatic))
		{
			$filterstatic.= " OR ";
		}
		else
			$filterstatic.= " AND (";
		$filterstatic.= " UPPER(label) = '".$detail."'";
		$filterstatic.= ")";
		$filterstatic.= " AND t.fk_budget =".$object->id;
		$res = $objprodb->fetchAll('','',0,0,$filter,'OR',$filterstatic,true);
		if (empty($res))
		{
			$lUpdate = false;
			if (empty($ref_product)) $lUpdate = true;
			$objprodb->fk_product = $fk_product+0;
			$objprodb->fk_budget = $object->id;
			$objprodb->ref = $object->id.'|'.$ref_product;
			$objprodb->label = $detail;
			$objprodb->fk_unit = $fk_unit;
			$objprodb->code_structure = $code_structure;
			$objprodb->quant = GETPOST('quant')+0;
			$objprodb->percent_prod = (GETPOST('percent_prod')?GETPOST('percent_prod'):100);
			$objprodb->amount_noprod = price2num(GETPOST('amount_noprod')+0,$general->decimal_pu);
			$objprodb->amount = $price;
			$objprodb->fk_user_create = $user->id;
			$objprodb->fk_user_mod = $user->id;
			$objprodb->date_create = dol_now();
			$objprodb->date_mod = dol_now();
			$objprodb->tms = dol_now();
			$objprodb->status = 1;
			$fk_product_budget = $objprodb->create($user);
			if ($fk_product_budget<=0)
			{
				setEventMessages($objprodb->error,$objprodb->errors,'errors');
				$error++;
			}
			//actualizamos si lUpdate == true
			if ($lUpdate == true)
			{
				$objprodbtmp->fetch($fk_product_budget);
				$objprodbtmp->ref .= '(PROV)'.$objprodb->id;
				$res = $objprodbtmp->update($user);
				if ($res <=0)
				{
					setEventMessages($objprodbtmp->error,$objprodbtmp->errors,'errors');
					$error++;
				}
			}
		}
		elseif($res==1)
		{
			$fk_product_budget = $objprodb->id;
			$ref_product = $objprodb->ref;
			$fk_product = $objprodb->fk_product;
			$fk_unit = $objprodb->fk_unit;
			$price = $objprodb->amount;
			$detail = $objprodb->label;
			$code_structure = $objprodb->code_structure;
		}
		else
		{
			$error++;
			if ($res<0)
				setEventMessages($objprodb->error,$objprodb->errors,'errors');
			else
				setEventMessages($langs->trans('Existe muchos registros'),null,'errors');
		}
	}
	//fin nueva opcion
	if (!$error)
	{
		$objectbtr->fk_budget_task = GETPOST('idr');
		$objectbtr->code_structure = $code_structure;
		$objectbtr->fk_product = $fk_product;
		$objectbtr->detail = $detail;
		$objectbtr->fk_unit = $fk_unit+0;
		$objectbtr->quant = price2num(GETPOST('quant')+0,$general->decimal_quant);
		$objectbtr->percent_prod = price2num(GETPOST('percent_prod'),'MU')+0;
		$objectbtr->amount_noprod = price2num(GETPOST('amount_noprod'),$general->decimal_pu)+0;
		$objectbtr->amount = price2num($price,$general->decimal_pu)+0;
			//$objectbtr->rang = 1;
		$objectbtr->date_mod = dol_now();
		$objectbtr->tms = dol_now();
		$objectbtr->fk_user_mod = $user->id;
		$objectbtr->status = 1;
		$result=$objectbtr->update($user);
		if ($result<=0)
		{
			$error++;
			setEventMessages($objectbtr->error, $objectbtr->errors, 'errors');
		}
	}

	if (!$error)
	{
		$sumaunit = $objectdetaddtmp->procedure_calculo($user,$id,GETPOST('idr'),false);
		$objectdetaddtmp->fetch(0,GETPOST('idr'));
		$objectdettmp->fetch(GETPOST('idr'));
		$objectdetaddtmp->unit_amount = $sumaunit;
		$objectdetaddtmp->total_amount = price2num($sumaunit * $objectdetaddtmp->unit_budget,$general->decimal_total);
		$resdet = $objectdetaddtmp->update_unit_amount($user);
		if ($resdet<=0)
		{
			$error++;
			setEventMessages($objectdetaddtmp->error,$objectdetaddtmp->errors,'errors');
		}
		//procedemos a actualizar el grupo
		//hasta que el task_parent este en 0
		$loop = true;
		$fk_task_parent = $objectdettmp->fk_task_parent;
		while ($loop == true)
		{
			if ($fk_task_parent > 0)
			{
				$total = $objectdetaddtmp->procedure_calculo_group($user,$id,$fk_task_parent);
				$res = $objectdetaddtmp->fetch(0,$fk_task_parent);
				if ($res == 1)
				{
					$objectdetaddtmp->total_amount = $total;
					$objectdetaddtmp->update($user);
				}
				//buscamos nuevamente en budget_task
				$res =$objectdettmp->fetch($fk_task_parent);
				if ($res == 1) $fk_task_parent = $objectdettmp->fk_task_parent;
			}
			else
				$loop = false;
		}
		//procedemos a actualizar el presupuesto
		$total = $objectdetaddtmp->procedure_calculo_budget($id);
		$res = $objecttmp->fetch($id);
		if ($res == 1)
		{
			$objecttmp->budget_amount = $total;
			$objecttmp->update($user);
		}

		//$db->commit();
		setEventMessages($langs->trans('Saverecord').' con valor de '.number_format($sumaunit,$general->decimal_total),null,'mesgs');
		// Creation OK
		unset($_POST['detail']);
		unset($_POST['product']);
		unset($_POST['refsearch']);
		$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/budget/card.php?id='.$id.'&idr='.GETPOST('idr').'&action=viewit',1);
		header("Location: ".$urltogo);
		exit;
	}
	else
	{
		//$db->rollback();
		$action='viewit';
		$_GET['idr'] = $_POST['idr'];
	}
}






















if ($action == 'confirm_clon')
{
	//creando una copia del presupuesto
	//verificamos el maximo numero de version existente
	$filterstatic = " AND t.ref = '".$object->ref."'";
	$filterstatic.= " AND t.version = '".GETPOST('version')."'";
	$res = $object->fetchAll('','', 0, 0, array(1=>1), 'AND',$filterstatic);
	$aStant= array();
	$aSt = array();
	$aStnew = array();
	$new = dol_now();
	if ($res>0)
	{
		$error++;
		setEventMessages($langs->trans('The selected version exists'),null,'errors');
	}
	elseif($res<0)
	{
		$error++;
		setEventMessages($object->error,$object->errors,'errors');
	}
	if (!$error)
	{
		//recuperamos contactos de budget
		for ($loop = 0; $loop < 2; $loop++)
		{
			$source = ($loop==0?'internal':'external');
			$aContact[$source] = $object->liste_contact($statut=-1,$source,0,'');
		}
	}
	if (!$error)
	{
		$db->begin();
		$version = GETPOST('version');
		$object->id = 0;
		$object->datec = $new;
		$object->tms = $new;
		$object->version = $version;
		$object->fk_user_creat = $user->id;
		$nid = $object->create($user);
		//nid es el nuevo budget
		if ($nid<=0)
		{
			$error++;
			setEventMessages($object->error,$object->errors,'errors');
		}
		else
		{
			$idBudget = $nid;
			//general
			$filterstatic = " AND t.fk_budget = ".$id;
			$general->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic);
			$lines = $general->lines;
			foreach ((array) $lines AS $j => $objtmp)
			{
				$general->fetch($objtmp->id);
				$general->id = 0;
				$general->fk_budget = $nid;
				$general->fk_user_create = $user->id;
				$general->fk_user_mod = $user->id;
				$general->date_create = $new;
				$general->date_mod = $new;
				$general->tms = $new;
				$res = $general->create($user);
				if ($res <= 0)
				{
					$error++;
					setEventMessages($general->error,$general->errors,'errors');
				}
			}
			//contactos de budget cambiamos el objeto a budget

			$objtmp = clone $object;
			include DOL_DOCUMENT_ROOT.'/budget/include/add_contact.inc.php';

			//conceptos
			include DOL_DOCUMENT_ROOT.'/budget/include/add_concept.inc.php';

				//recuperamos todos los grupos e items
			$filterstatic = " AND t.fk_budget = ".$id;
				//$filterstatic.= " AND t.fk_task_parent = ".$idg;
			$res = $objectdet->fetchAll('ASC', 'ref', 0, 0, array(1=>1), 'AND',$filterstatic);
			$tasks = $objectdet->lines;
			foreach ((array) $tasks AS $t => $task)
			{
				$aStant[$task->ref] = $task->id;
				$fk = $task->id;
					//recuperamos el item/tarea
				$objectdet->fetch($fk);
				$objectdetadd->fetch(0,$fk);
				$aContact = array();
				if (!$error)
				{
					//recuperamos contactos de la tarea(item)
					for ($loop = 0; $loop < 2; $loop++)
					{
						$source = ($loop==0?'internal':'external');
						$aContact[$source] = $objectdet->liste_contact(-1,$source,0,'');
					}
				}

					//recorremos cada grupo o item
				$idg = $task->fk_task_parent;
				if ($objectdetadd->c_grupo)
				{
					//numeracion de grupo
					$max = $objectdet->max_group($nid,($idg>0?$idg:0),1);
				}
				else
				{
					//numeracion de item
					$max = $objectdet->max_task($nid);
				}

				if ($idg>0)
				{
					$objectdettmp->fetch($idg);
					$objectdetaddtmp->fetch(0,$idg);
					$level = $objectdetaddtmp->level+1;
					//$max = $objectdettmp->ref.'.'.$max;
				}
				$objectdet->id 			= 0;
				$objectdet->ref 		= $max;
				$objectdet->fk_budget 	= $nid;
				$objectdet->fk_task_parent = $aStnew[$idg]+0;
				$objectdet->datec 		= $new;
				$objectdet->tms 		= $new;
				$fknew = $objectdet->create($user);
				$aSt[$objectdet->ref] = $fknew;
				$aStnew[$task->id] = $fknew;
				if ($fknew<=0)
				{
					$error++;
					setEventMessages($objectdet->error,$objectdet->errors,'errors');
				}
				//contactos de budget cambiamos el objeto a budget
				$objtmp = clone $objectdet;
				include DOL_DOCUMENT_ROOT.'/budget/include/add_contact.inc.php';

				if (!$error)
				{
					//agregamos en la tabla adicional
					$objectdetadd->id = 0;
					$objectdetadd->fk_budget_task = $fknew;
					$objectdetadd->level = $level+0;
					$residadd = $objectdetadd->create($user);
					if ($residadd<=0)
					{
						$error++;
						setEventMessages($objectdetadd->error,$objectdetadd->errors,'errors');
					}
					if (!$error)
					{
						//registramos todos los recursos del item
						//recuperamos los recursos del item original
						$filtertmp = " AND t.fk_budget_task = ".$fk;
						$resbtr = $objectbtrtmp->fetchAll('ASC','rang',0,0,array(1=>1),'AND',$filtertmp);
						if ($resbtr>0)
						{
							$rang = 1;
							foreach ($objectbtrtmp->lines AS $j => $line)
							{
								//recuperamos el registro para crear como nuevo
								$objectbtr->fetch($line->id);
								//buscamos el producto en product_budget
								$respb = $objprodb->fetch($line->fk_product_budget);
								if ($respb>0)
								{
									//convertimos el ref para el nuevo budget
									$aRef = explode('|',$objprodb->ref);
									$ref = $id.'|'.$aRef[1];
									//buscamos el producto con ref y el fk_budget
									$filterdb = " AND t.fk_budget = ".$nid;
									$filterdb.= " AND t.ref = '".trim($ref)."'";
									$resdb = $objprodbtmp->fetchAll('','',0,0,array(1=>1),'AND',$filterdb,true);
									if ($resdb>0)
									{
										$objectbtr->fk_product_budget = $objprodbtmp->id;
									}
									else
									{
										//creamos el nuevo registro de product_budget
										$objprodb->id = 0;
										$objprodb->fk_budget = $nid;
										$objprodb->ref = $ref;
										$objprodb->fk_user_create = $user->id;
										$objprodb->fk_user_mod = $user->id;
										$objprodb->date_create = $new;
										$objprodb->date_mod = $new;
										$objprodb->tms = $new;
										$resdb = $objprodb->create($user);
										if ($resdb<=0)
										{
											$error++;
											setEventMessages($objprodb->error,$objprodb->errors,'errors');
										}
										$objectbtr->fk_product_budget = $resdb;
									}
								}
									//armamos el nuevo
								$objectbtr->id = 0;
								$objectbtr->fk_budget_task = $fknew;
								$objectbtr->ref = '(PROV)';
								$objectbtr->fk_user_create = $user->id;
								$objectbtr->fk_user_mod = $user->id;
								$objectbtr->rang = $rang;
								$objectbtr->date_create = $new;
								$objectbtr->date_mod = $new;
								$objectbtr->tms = $new;
								$resbtrc = $objectbtr->create($user);
								if ($resbtrc<=0)
								{
									$error++;
									setEventMessages($objectbtr->error,$objectbtr->errors,'errors');
								}
									//actualizamos el registro para cambiar el ref
								$objectbtr->ref.= $objectbtr->id;
								$resup = $objectbtr->update($user);
								if ($resup<=0)
								{
									$error++;
									setEventMessages($objectbtr->error,$objectbtr->errors,'errors');
								}
								if (!$error)
								{
									$resprod = $objproductivity->import_productivity($user,$line->id,$resbtrc);
									if ($resprod <=0) $error++;
								}
								$rang++;
							}
						}
					}
				}
			}
		}

		if (!$error)
		{
			$db->commit();
			setEventMessages($langs->trans('Clonsucessfull'),null,'mesgs');
			header("Location: ".dol_buildpath('/budget/budget/card.php?id='.$nid,1));
			exit;
		}
		else
		{
			$db->rollback();
			$action = 'viewit';
		}
	}
}



	//import-group
if ($action == 'import_group')
{
	$sel = GETPOST('sel');
	$fk_budget = GETPOST('fk_budget');
	$idg = GETPOST('idg','int');
	$newsel = $_SESSION['upsel'][$id];
	$db->begin();
	foreach ((array) $newsel AS $fk => $value)
	{
		//recuperamos el item/tarea
		$objectdet->fetch($fk);
		$objectdetadd->fetch(0,$fk);
			//recuperamos los items que tiene el grupo seleccionado
		$filterstatic = " AND t.fk_budget = ".$id;
		$filterstatic.= " AND t.fk_task_parent = ".$idg;
		$res = $objectdet->fetchAll($sortorder, $sortfield, 0, 0, array(1=>1), 'AND',$filterstatic);
		if ($objectdetadd->c_grupo)
		{
			//numeracion de grupo
			$max = $objectdet->max_group($id,($idg>0?$idg:0),1);
		}
		else
		{
			//numeracion de item
			$max = $objectdet->max_task($id);
		}

		if ($idg>0)
		{
			$objectdettmp->fetch($idg);
			$objectdetaddtmp->fetch(0,$idg);
			$level = $objectdetaddtmp->level+1;
			//$max = $objectdettmp->ref.'.'.$max;
		}
		$objectdet->id = 0;
		$objectdet->fk_budget = $id;
		$objectdet->ref = $max;
		$objectdet->fk_task_parent = $idg;
		$objectdet->datec = dol_now();
		$objectdet->tms = dol_now();
		$fknew = $objectdet->create($user);
		if ($fknew<=0)
		{
			$error++;
			setEventMessages($objectdet->error,$objectdet->errors,'errors');
		}
		if (!$error)
		{
			//agregamos en la tabla adicional
			$objectdetadd->id = 0;
			$objectdetadd->fk_budget_task = $fknew;
			$objectdetadd->level = $level+0;
			$residadd = $objectdetadd->create($user);
			if ($residadd<=0)
			{
				$error++;
				setEventMessages($objectdetadd->error,$objectdetadd->errors,'errors');
			}
			if (!$error)
			{
				//registramos todos los recursos del item
				//recuperamos los recursos del item original
				$filtertmp = " AND t.fk_budget_task = ".$fk;
				$resbtr = $objectbtrtmp->fetchAll('ASC','rang',0,0,array(1=>1),'AND',$filtertmp);
				if ($resbtr>0)
				{
					$rang = 1;
					foreach ($objectbtrtmp->lines AS $j => $line)
					{
						//recuperamos el registro para crear como nuevo
						$objectbtr->fetch($line->id);
						//buscamos el producto en product_budget
						$respb = $objprodb->fetch($line->fk_product_budget);
						if ($respb>0)
						{
							//convertimos el ref para el nuevo budget
							$aRef = explode('|',$objprodb->ref);
							$ref = $id.'|'.$aRef[1];
								//buscamos el producto con ref y el fk_budget
							$filterdb = " AND t.fk_budget = ".$id;
							$filterdb.= " AND t.ref = '".trim($ref)."'";
							$resdb = $objprodbtmp->fetchAll('','',0,0,array(1=>1),'AND',$filterdb,true);
							if ($resdb>0)
							{
								$objectbtr->fk_product_budget = $objprodbtmp->id;
							}
							else
							{
								//creamos el nuevo registro de product_budget
								$objprodb->id = 0;
								$objprodb->fk_budget = $id;
								$objprodb->ref = $ref;
								$objprodb->fk_user_create = $user->id;
								$objprodb->fk_user_mod = $user->id;
								$objprodb->date_create = dol_now();
								$objprodb->date_mod = dol_now();
								$objprodb->tms = dol_now();
								$resdb = $objprodb->create($user);
								if ($resdb<=0)
								{
									$error++;
									setEventMessages($objprodb->error,$objprodb->errors,'errors');
								}
								$objectbtr->fk_product_budget = $resdb;
							}
						}
								//armamos el nuevo
						$objectbtr->id = 0;
						$objectbtr->fk_budget_task = $fknew;
						$objectbtr->ref = '(PROV)';
						$objectbtr->fk_user_create = $user->id;
						$objectbtr->fk_user_mod = $user->id;
						$objectbtr->rang = $rang;
						$objectbtr->date_create = dol_now();
						$objectbtr->date_mod = dol_now();
						$objectbtr->tms = dol_now();
						$resbtrc = $objectbtr->create($user);
						if ($resbtrc<=0)
						{
							$error++;
							setEventMessages($objectbtr->error,$objectbtr->errors,'errors');
						}
							//actualizamos el registro para cambiar el ref
						$objectbtr->ref.= $objectbtr->id;
						$resup = $objectbtr->update($user);
						if ($resup<=0)
						{
							$error++;
							setEventMessages($objectbtr->error,$objectbtr->errors,'errors');
						}
						if (!$error)
						{
							$resprod = $objproductivity->import_productivity($user,$line->id,$resbtrc);
							if ($resprod <=0) $error++;
						}
						$rang++;
					}
				}
			}
		}
	}
	if (!$error)
	{
		$db->commit();
		setEventMessages($langs->trans('Importsucessfull'),null,'mesgs');
		header("Location: ".dol_buildpath('/budget/budget/card.php?id='.$id.'&action=viewit',1));
		exit;
	}
	else
	{
		$db->rollback();
		$action = 'viewit';
	}
}

	//importresurce
if ($action == 'import_resource')
{
	$sel = GETPOST('sel');
	$ord = GETPOST('ord');
	$fk_budget = GETPOST('fk_budget');
	$newsel = $_SESSION['upsel'][$id];

	$db->begin();

	foreach ((array) $newsel AS $fk => $value)
	{
		$res = $objprodb->fetch($fk);
		if ($res>0)
		{
			$objprodb->id = 0;
			$objprodb->fk_budget = $id;
			$aRef = explode('|',$objprodb->ref);
			$objprodb->ref = $id.'|'.$aRef[1];
			$nid = $objprodb->create($user);
			if ($nid <= 0)
			{
				$error++;
				setEventMessages($objprodb->error,$objprodb->errors,'errors');
			}
		}
		else
		{
			$error++;
			setEventMessages($objprodb->error,$objprodb->errors,'errors');
		}
	}

	if (!$error)
	{
		$db->commit();
		setEventMessages($langs->trans('Importsucessfull'),null,'mesgs');
		header("Location: ".dol_buildpath('/budget/budget/card.php?id='.$id.'&action=viewre',1));
		exit;
	}
	else
	{
		$db->rollback();
		$action = 'viewre';
	}
}

	//importresurce
if ($action == 'import_product')
{
	$aStruct = $aStrbudget[$id];

	$sel = GETPOST('sel');
	$ord = GETPOST('ord');
	$fk_budget = GETPOST('fk_budget');
	$newsel = $_SESSION['upsel'][$id];
	$db->begin();
	foreach ((array) $newsel AS $fk => $value)
	{
		$res = $product->fetch($fk);
		$aCat = $categorie->containing($fk, 'product', 'id');
		foreach ((array) $aCat AS $i => $k)
		{
			if ($aStruct['aStrcatcode'][$k])
			{
				$code_structure = $aStruct['aStrcatcode'][$k];
				//cambiamos para que guarde la categoria del producto
				$code_structure = $k;
			}
		}
		//buscamos en product_budget
		$filterstatic = " AND t.fk_budget = ".$id;
		$filterstatic.= " AND t.ref = '".$id.'|'.$product->ref."'";
		$filterstatic.= " AND t.code_structure = '".$code_structure."'";
		$ress = $objprodb->fetchAll('','',0,0,array(1=>1),'AND',$filterstatic,true);
		if ($ress==0)
		{
			$objprodb->initAsSpecimen();
			$objprodb->ref = $id.'|'.$product->ref;
			$objprodb->id = 0;
			$objprodb->fk_budget = $id;
			$objprodb->fk_product = $fk;
			$objprodb->label = $product->label;
			$objprodb->fk_unit = $product->fk_unit+0;
			$objprodb->code_structure = $code_structure;
			$objprodb->quant = 1;
			$objprodb->percent_prod = 100;
			$objprodb->amount_noprod = 0;
			$objprodb->amount = $product->pmp;
			$objprodb->fk_user_create = $user->id;
			$objprodb->fk_user_mod = $user->id;
			$objprodb->date_create = dol_now();
			$objprodb->date_mod = dol_now();
			$objprodb->tms = dol_now();
			$objprodb->status = 1;
			$resdb = $objprodb->create($user);
			if ($resdb<=0)
			{
				$error++;
				setEventMessages($objprodb->error,$objprodb->errors,'errors');
			}
		}
		else
		{
			$error++;
			setEventMessages($objprodb->error,$objprodb->errors,'errors');
		}
	}
	if (!$error)
	{
		$db->commit();
		setEventMessages($langs->trans('Importsucessfull'),null,'mesgs');
		header("Location: ".dol_buildpath('/budget/budget/card.php?id='.$id.'&action=viewre',1));
		exit;
	}
	else
	{
		$db->rollback();
		$action = 'viewre';
	}
}
	// Action to add record
if ($action == 'add')
{
	if (GETPOST('cancel'))
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/budget/list.php',1);
		header("Location: ".$urltogo);
		exit;
	}
	$new = dol_now();
	$error=0;
	$labelcity = GETPOST('labelcity');
	$fk_city = GETPOST('fk_city');
	$refcity = GETPOST('refcity');

	if (!empty($labelcity) && empty($fk_city))
	{
		$filtercity = " AND t.fk_country = ".GETPOST('country_id');
		$filtercity.= " AND t.fk_departament = ".GETPOST('state_id');
		$filtercity.= " AND t.label = '".trim($labelcity)."'";
		$rescity = $objcity->fetchAll('ASC', 't.label', 0,0,array(1=>1), 'AND',$filtercity);
		if (!$rescity)
		{
			$objcity->fk_country = GETPOST('country_id');
			$objcity->fk_departament = GETPOST('state_id');
			$objcity->ref = $refcity;
			$objcity->label = $labelcity;
			$objcity->fk_user_create = $user->id;
			$objcity->fk_user_mod = $user->id;
			$objcity->datec = $new;
			$objcity->datem = $new;
			$objcity->tms = $new;
			$objcity->active = 1;
			$objcity->status = 1;
			$fk_city = $objcity->create($user);
			if ($fk_city <=0)
			{
				$error++;
				setEventMessages($objcity->error,$objcity->errors,'errors');
			}
		}
	}
	/* object_prop_getpost_prop */
	if (!$error)
	{
		$dateo  = dol_mktime(12, 0, 0, GETPOST('do_month'), GETPOST('do_day'), GETPOST('do_year'));
		$object->fk_soc=GETPOST('fk_soc','int');
		if(empty($object->fk_soc)) $object->fk_soc = 0;
		$object->fk_budget_parent = -1;
		$object->ref=GETPOST('ref','alpha');
		$object->entity=$conf->entity;
		$object->title=GETPOST('title','alpha');
		$object->type_structure=GETPOST('type_structure','alpha');
		$object->description=GETPOST('description','alpha');
		$object->fk_user_creat=$user->id;
		$object->fk_user_valid=0;
		$object->fk_calendar = GETPOST('fk_calendar')+0;
		$object->fk_country = GETPOST('country_id');
		$object->fk_departament = GETPOST('state_id');
		$object->fk_city = $fk_city;
		$object->data_type = GETPOST('data_type')+0;
		$object->fk_calendar = GETPOST('fk_calendar')+0;
		$object->manual_performance = 0;
		
		$object->public=0;
		$object->version = 1;
		$object->fk_statut=0;
		$object->fk_opp_status=0;
		$object->opp_percent=0;
		$object->fk_user_close=0;
		$object->note_private='';
		$object->note_public='';
		$object->opp_amount=0;
		$object->budget_amount=0;
		$object->model_pdf='budget';
		$object->rang=0;
		$object->datec = dol_now();
		$object->dateo = $dateo;
		$object->tms = dol_now();

		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}
		if (! $error)
		{
			$result=$object->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/budget/card.php?id='.$result.'&action=gen',1);
				header("Location: ".$urltogo);
				exit;
			}
			{
				// Creation KO
				if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else  setEventMessages($object->error, null, 'errors');
				$action='create';
			}
		}
		else
		{
			$action='create';
		}
	}
}

	// Cancel
if ($action == 'update' && GETPOST('cancel')) $action='view';

	// Action to update record
if ($action == 'update' && ! GETPOST('cancel'))
{
	$error=0;
	$new = dol_now();
	$error=0;
	$labelcity = GETPOST('labelcity');
	$refcity = GETPOST('refcity');
	$fk_city = GETPOST('fk_city');
	$lAddcity = false;
	$db->begin();
	if (empty($fk_city))
	{
		$lAddcity = true;
		if (!empty($labelcity) && empty($refcity))
		{
			$lAddcity = false;
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Codecity")), null, 'errors');
		}
		if (empty($labelcity) && !empty($refcity))
		{
			$lAddcity = false;
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Labelcity")), null, 'errors');
		}
	}
	if ($lAddcity)
	{
		$filtercity = " AND t.fk_country = ".GETPOST('country_id');
		$filtercity.= " AND t.fk_departament = ".GETPOST('state_id');
		$filtercity.= " AND t.label = '".trim($labelcity)."'";
		$rescity = $objcity->fetchAll('ASC', 't.label', 0,0,array(1=>1), 'AND',$filtercity);
		if (!$rescity)
		{
			$objcity->fk_country = GETPOST('country_id');
			$objcity->fk_departament = GETPOST('state_id');
			$objcity->ref = $refcity;
			$objcity->label = $labelcity;
			$objcity->fk_user_create = $user->id;
			$objcity->fk_user_mod = $user->id;
			$objcity->datec = $new;
			$objcity->datem = $new;
			$objcity->tms = $new;
			$objcity->active = 1;
			$objcity->status = 1;
			$fk_city = $objcity->create($user);
			if ($fk_city <=0)
			{
				$error++;
				setEventMessages($objcity->error,$objcity->errors,'errors');
			}
		}
	}
	//verificamos si se cambia la estructura
	$lUpdate = false;
	$dateo  = dol_mktime(12, 0, 0, GETPOST('do_month'), GETPOST('do_day'), GETPOST('do_year'));
	$type_structure = $object->type_structure;
	$newtype_structure = GETPOST('type_structure','alpha');
	if ($type_structure != $newtype_structure) $lUpdate = true;
	$object->fk_calendar = GETPOST('fk_calendar')+0;
	$object->fk_country = GETPOST('country_id');
	$object->fk_departament = GETPOST('state_id');
	$object->version = GETPOST('version');
	$object->fk_city = $fk_city;
	$object->data_type = GETPOST('data_type')+0;
	$object->fk_calendar = GETPOST('fk_calendar')+0;
	$object->ref=GETPOST('ref','alpha');
	$object->title=GETPOST('title','alpha');
	$object->description=GETPOST('description','alpha');
	$object->type_structure=GETPOST('type_structure','alpha');
	$object->dateo=$dateo;

	if (empty($object->ref))
	{
		$error++;
		setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
	}
	if (empty($object->title))
	{
		$error++;
		setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Title")), null, 'errors');
	}
	if (empty($object->type_structure))
	{
		$error++;
		setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Typestructure")), null, 'errors');
	}

	if (! $error)
	{
		$result=$object->update($user);
		if ($result <= 0)
		{
			$error++;
			setEventMessages($object->error,$object->errors,'errors');
			$action = 'edit';
		}

		if (!$error)
		{
			if ($lUpdate)
			{
				$res = $object->update_pu_all($user,$aStrbudget,'general');
				if ($res < 0)
				{
					$error++;
					setEventMessages($object->error,$object->errors,'errors');
					$action = 'edit';
				}
			}
		}
	}
	if (!$error)
	{
		$db->commit();
		if (count($_SESSION['aConcept'])>0)
		{
			header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id.'&action=gen&subaction=addconcept&add=1');
			exit;
		}
		header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id);
		exit;
	}
	else
	{
		$db->rollback();
		$action='edit';
	}
}

	// Action to delete
if ($action == 'confirm_delete')
{
	$result=$object->delete($user);
	if ($result > 0)
	{
			// Delete OK
		setEventMessages("RecordDeleted", null, 'mesgs');
		header("Location: ".dol_buildpath('/budget/budget/list.php',1));
		exit;
	}
	else
	{
		if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
		else setEventMessages($object->error, null, 'errors');
	}
}

if ($action == 'confirm_deletegroup' && $_REQUEST['confirm']=='yes' && $user->rights->budget->budi->del)
{
	$objectdet->fetch(GETPOST('idr','int'));
	if ($objectdet->id == GETPOST('idr','int'))
	{
		$db->begin();
		$objectdetadd->fetch(0,$objectdet->id);
		$res = $objectdet->delete($user);
		if ($res<=0)
		{
			$error++;
			setEventMessages($objectdet->error,$objectdet->errors,'errors');
		}
		else
		{
			$res = $objectdetadd->delete($user);
			if ($res<=0)
			{
				$error++;
				setEventMessages($objectdet->error,$objectdet->errors,'errors');
			}
			if (!$error)
			{
				//busco si tiene dependientes como grupo
				$filterstatic = " AND t.fk_task_parent = ".GETPOST('idr');
				$ng = $objectdet->fetchAll('','',0,0,array(1=>1), 'AND',$filterstatic);
				$linesg = $objectdet->lines;
				if ($ng>0)
				{
					foreach ($linesg AS $i => $lineg)
					{
						$filterstatic = " AND t.fk_budget_task = ".$lineg->id;
						$nb = $objectbtr->fetchAll('','',0,0,array(1=>1), 'AND',$filterstatic);
						$lines = $objectbtr->lines;
						if ($nb>0)
						{
							foreach ($lines AS $i => $line)
							{
								if ($objectbtr->fetch($line->id)>0)
								{
									$ress = $objectbtr->delete($user);
									if ($ress<=0)
									{
										$error++;
										setEventMessages($objectbtr->error,$objectbtr->errors,'errors');
									}
								}
								else
									$error++;
							}
						}
						if (!$error)
						{
							$objectdettmp->fetch($lineg->id);
							$resg = $objectdettmp->delete($user);
							if ($res<=0)
							{
								$error++;
								setEventMessages($objectdettmp->error,$objectdettmp->errors,'errors');
							}
							$objectdetaddtmp->fetch(0,$lineg->id);
							$resg = $objectdetaddtmp->delete($user);
							if ($res<=0)
							{
								$error++;
								setEventMessages($objectdetaddtmp->error,$objectdetaddtmp->errors,'errors');
							}
						}
					}
				}
			}
		}
		if (!$error)
		{
			$db->commit();
			setEventMessages($langs->trans('Deletesuccesfull'),null,'mesgs');
			header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id.'&action=viewgr');
			exit;
		}
		$db->rollback();
		$action = '';
	}
}


if ($action == 'confirm_deleteitem' && $_REQUEST['confirm']=='yes' && $user->rights->budget->budi->del)
{
	$objectdet->fetch(GETPOST('idr','int'));
	if ($objectdet->id == GETPOST('idr','int'))
	{
		$db->begin();
		$objectdetadd->fetch(0,$objectdet->id);
		$fk_father = $objectdet->fk_task_parent;
		$res = $objectdet->delete($user);
		if ($res<=0)
		{
			$error++;
			setEventMessages($objectdet->error,$objectdet->errors,'errors');
		}
		else
		{
			$res = $objectdetadd->delete($user);
			if ($res<=0)
			{
				$error++;
				setEventMessages($objectdet->error,$objectdet->errors,'errors');
			}
			if (!$error)
			{
				$filterstatic = " AND t.fk_budget_task = ".GETPOST('idr');
				$nb = $objectbtr->fetchAll('','',0,0,array(1=>1), 'AND',$filterstatic);
				$lines = $objectbtr->lines;
				if ($nb>0)
				{
					foreach ($lines AS $i => $line)
					{
						if ($objectbtr->fetch($line->id)>0)
						{
							$ress = $objectbtr->delete($user);
							if ($ress<=0)
							{
								$error++;
								setEventMessages($objectbtr->error,$objectbtr->errors,'errors');
							}
						}
						else
							$error++;
					}
				}
			}
		}
		if (!$error)
		{
			$db->commit();
			setEventMessages($langs->trans('Deletesuccesfull'),null,'mesgs');
			header('Location: '.$_SERVER['PHP_SELF'].'?id='.$id.'&idg='.$fk_father.'&action=viewit');
			exit;
		}
		$db->rollback();
		$action = '';
	}
}


if ($action == 'additem' && empty(GETPOST('refsearch'))&& empty(GETPOST('search_itemid','alpha')))
{
	if ($subaction == 'creategr')
	{
		$action = 'viewgr';
		//print_r($_POST);//exit;
	}
	if ($subaction == 'item')
	{
		$action = 'viewit';
		$_GET['idg'] = GETPOST('fk_task_parent');
		//print_r($_POST);//exit;
	}
}


if ($action == 'additem')
{

	$aStruct = $aStrbudget[$id];
	$fk_item 	 	= GETPOST('itemid','int');
	$search_itemid 	= GETPOST('search_itemid','alpha');
	$refsearch 	 	= GETPOST('refsearch');
	$subaction 	 	= GETPOST('subaction');
	$ref 			= GETPOST('ref');
	$complementary 	= GETPOST('complementary');
		//verificamos la numeracion que se dara
	$fk_father		= GETPOST('fk_father');
	$subaction   	= GETPOST('subaction');
	$level = 0;
	$lGroup = false;
	if (GETPOST('c_grupo'))
	{
		$lGroup = true;
		//numeracion de grupo
		$max = $objectdet->max_group($id,($fk_father>0?$fk_father:0),1);
	}
	else
	{
		//numeracion de item
		$max = $objectdet->max_task($id);
	}
	if ($fk_father>0 && GETPOST('c_grupo'))
	{
		$objectdettmp->fetch($fk_father);
		$objectdetaddtmp->fetch(0,$fk_father);
		$level = $objectdetaddtmp->level+1;
		$max = $objectdettmp->ref.'.'.$max;
	}

	$db->begin();
	if (empty($fk_item))
	{
		$items->fetch(0,trim($search_itemid));
		if ($items->ref == $search_itemid)
		{
			$fk_item = $items->id;
			$label = $items->detail;
		}
		if (empty($fk_item) && (!empty($refsearch) || !empty(GETPOST('search_itemid'))))
		{
			//vamos a buscar el item por el nombre
			$label = trim($refsearch?STRTOUPPER($refsearch):STRTOUPPER(GETPOST('search_itemid')));
			$res = $items->fetch(0,null,null,$label);
			if ($res ==1)
			{
				$fk_item = $items->id;
				$label = $items->detail;
			}
			elseif (empty($res))
			{
				//agregamos como item nuevo
				$items->entity=$conf->entity;
				$items->ref='(PROV)';
				$items->ref_ext='';
				$items->fk_user_create=$user->id;
				$items->fk_user_mod=$user->id;
				$items->fk_type_item=GETPOST('fk_type_item','int');
				if (empty($items->fk_type_item))$items->fk_type_item=0;
				$items->type=0;
				if ($lGroup) $items->type=1;
				$items->detail=$label;
				$items->fk_unit=GETPOST('unitid')+0;
				$items->especification='';
				$items->plane='';
				$items->amount=GETPOST('amount','int');
				if (empty($items->amount))$items->amount=0;
				$items->manual_performance = 0;
				$items->datec = $now;
				$items->status=0;
				$fk_item = $items->create($user);
				if ($fk_item>0)
				{
					//$itemstmp->fetch($fk_item);
					$res =$items->fetch($fk_item);
					$items->ref = '(PROV)'.$items->id;
					$resup = $items->update($user);
					if ($resup <=0)
					{
						$error++;
						$action = 'viewit';
						setEventMessages($itemstmp->error,$itemstmp->errors,'errors');
					}
				}
				else
				{
					$error++;
					$action = 'viewit';
					setEventMessages($items->error,$items->errors,'errors');
				}
			}
			else
			{
				$error++;
				setEventMessages($items->error,$items->errors,'errors');
			}

			if ($fk_item<=0) $fk_item = 0;
		}
		else
		{
			$label = ($refsearch?STRTOUPPER($refsearch):STRTOUPPER(GETPOST('search_itemid')));
		}
	}
	else
	{
		$res = $items->fetch($fk_item);
		if ($res==1)
		{
			$label = $items->detail;
		}
		else
		{
			setEventMessages($langs->trans('Noexistitem'),null,'warnings');
			$label = ($refsearch?STRTOUPPER($refsearch):STRTOUPPER(GETPOST('search_itemid')));
		}
	}
	if (!$error)
	{
		$objectdet->fk_budget = $object->id;
		$objectdet->fk_task = $fk_item+0;
		$objectdet->entity = $object->entity;
		$objectdet->ref = $max;
		$objectdet->fk_task_parent = GETPOST('fk_father');
		if (empty($objectdet->fk_task_parent))$objectdet->fk_task_parent=0;
		$objectdet->fk_product_budget = GETPOST('fk_product_budget');
		if (empty($objectdet->fk_product_budget))$objectdet->fk_product_budget=0;
		$objectdet->manual_performance = 0;
		$objectdet->amount = 0;
		$objectdet->datec = dol_now();
		$objectdet->tms = dol_now();
		$objectdet->label = $label;
		$objectdet->fk_statut = 0;
		$resdet = $objectdet->create($user);
		if ($resdet<=0)
		{
			$error++;
			$action = 'viewit';
			setEventMessages($objectdet->error,$objectdet->errors,'errors');
		}
	}
	if (!$error)
	{
		if ($fk_item>0)
		{
			$items->fetch($fk_item);
			if (!$items->type)
			{
				$resitem = $items->clone_items($user,$id,$resdet);
				if ($resitem<=0)
				{
					$error++;
					setEventMessages($langs->trans('Errorinclone'),null,'errors');
				}
			}
		}
	}
	if (!$error)
	{
		$objectdetadd->fk_budget_task = $resdet;
		$objectdetadd->level = $level+0;
		$objectdetadd->c_grupo = GETPOST('c_grupo');
		$objectdetadd->fk_unit = GETPOST('unitid')+0;
		$objectdetadd->complementary = $complementary;
		$objectdetadd->unit_budget = GETPOST('quant','int');
		if(empty($objectdetadd->unit_budget)) $objectdetadd->unit_budget=0;
		$objectdetadd->unit_amount = GETPOST('amount','int');
		if(empty($objectdetadd->unit_amount)) $objectdetadd->unit_amount=0;
		$objectdetadd->total_amount = 0;
		$objectdetadd->fk_user_create = $user->id;
		$objectdetadd->fk_user_mod = $user->id;
		$objectdetadd->date_create = dol_now();
		$objectdetadd->tms = dol_now();
		$objectdetadd->status = 0;
		$resdetadd=$objectdetadd->create($user);
		if ($resdetadd<=0)
		{
			$error++;
			setEventMessages($objectdetadd->error,$objectdetadd->errors,'errors');
			$action = (GETPOST('c_grupo')?'creategr':'viewit');
		}
	}
	
	if (!$error)
	{
		$db->commit();
		setEventMessages($langs->trans('Saverecord'),null,'mesgs');
		unset($_POST['ref']);
		unset($_POST['label']);
		unset($_POST['refsearch']);
		unset($_POST['itemid']);
		unset($_POST['search_itemid']);
		$_GET['idg']=$fk_father;
		$action = (GETPOST('c_grupo')?'viewgr':'viewit');
	}
	else
		$db->rollback();
	$action = 'viewit';
}
if ($action == 'updateitem' && $user->rights->budget->budi->mod)
{
	$error = 0;
	$res = $objectdet->fetch(GETPOST('idr'));
	if ($res <= 0)
	{
		$error++;
		setEventMessages($objectdet->error,$objectdet->errors,'errors');
	}
	$res = $objectdetadd->fetch(0,$objectdet->id);
	if ($res <= 0)
	{
		$error++;
		setEventMessages($objectdetadd->error,$objectdetadd->errors,'errors');
	}

	$fk_item 	 	= GETPOST('itemid','int');
	$search_itemid 	= GETPOST('search_itemid','alpha');
	$refsearch 	 	= GETPOST('refsearch');
	$subaction 	 	= GETPOST('subaction');
	$ref 			= GETPOST('ref');
		//verificamos la numeracion que se dara
	$fk_father		= GETPOST('fk_father');
	$subaction   	= GETPOST('subaction');

	$db->begin();
	if (empty($fk_item))
	{
		$items->fetch(0,trim($search_itemid));
		if ($items->ref == $search_itemid)
		{
			$fk_item = $items->id;
			$label = $items->detail;
		}
		if (empty($fk_item) && !empty($refsearch))
		{
				//agregamos como item nuevo
			$label = STRTOUPPER($refsearch);
			$items->entity=$conf->entity;
			$items->ref='(PROV)';
			$items->ref_ext='';
			$items->fk_user_create=$user->id;
			$items->fk_user_mod=$user->id;
			$items->fk_type_item=GETPOST('fk_type_item','int');
			if (empty($items->fk_type_item))$items->fk_type_item=0;
			$items->detail=$refsearch;
			$items->fk_unit=GETPOST('unitid');
			if (empty($items->fk_unit))$items->fk_unit=0;
			$items->especification='';
			$items->plane='';
			$items->amount=GETPOST('amount','int');
			if (empty($items->amount))$items->amount=0;
			$items->date_create = dol_now();
			$items->status=0;
			$fk_item = $items->create($user);
			if ($fk_item>0)
			{
				$itemstmp->fetch($fk_item);
				$itemstmp->ref = '(PROV)'.$itemstmp->id;
				$resup = $itemstmp->update($user);
				if ($resup <=0)
				{
					$error++;
					$action = 'viewit';
					setEventMessages($itemstmp->error,$itemstmp->errors,'errors');
				}
			}
			else
			{
				$error++;
				$action = 'viewit';
				setEventMessages($items->error,$items->errors,'errors');
			}
			$fk_item = 0;
		}
	}
	else
	{
		$items->fetch($fk_item);
		if ($items->id == $fk_item)
			$label = $items->detail;
	}
	if (empty($fk_item)) $label = GETPOST('search_itemid');

	if (!$error)
	{
		$max = $objectdet->max_group($id,($fk_father>0?$fk_father:0),($subaction?0:1));

		if ($fk_father>0 && $fk_father != $objectdet->fk_task_parent)
		{
			$objectdettmp->fetch($fk_father);
			$objectdetaddtmp->fetch(0,$fk_father);
			$level = $objectdetaddtmp->level+1;
			$max = $objectdettmp->ref.'.'.$max;
			$objectdet->ref = $max;
			//exit;
		}

		$objectdet->fk_budget = $object->id;
		$objectdet->fk_task = $fk_item+0;
		$objectdet->fk_task_parent = $fk_father+0;
		$objectdet->tms = dol_now();
		$objectdet->label = $label;
			//$objectdet->fk_statut = 0;

		$resdet = $objectdet->update($user);
		if ($resdet<=0)
		{
			$error++;
			$action = 'viewit';
			setEventMessages($objectdet->error,$objectdet->errors,'errors');
		}
			//actualizamos en la tabla adicional
			//$objectdetadd->c_grupo = GETPOST('c_grupo');
		$objectdetadd->fk_unit = GETPOST('unitid');
		if (empty($objectdetadd->fk_unit))$objectdetadd->fk_unit=0;
		$objectdetadd->unit_budget = GETPOST('quant','int');
		if (empty($objectdetadd->unit_budget))$objectdetadd->unit_budget=0;
		$objectdetadd->unit_amount = GETPOST('amount','int');
		if (empty($objectdetadd->unit_amount))$objectdetadd->unit_amount=0;
		$objectdetadd->fk_user_mod = $user->id;
		$objectdetadd->tms = dol_now();
			//$objectdetadd->status = 0;
		$resdetadd=$objectdetadd->update($user);
		if ($resdetadd>0)
		{
			unset($_POST['ref']);
			unset($_POST['label']);
				//setEventMessages($langs->trans('Saverecord'),null,'mesgs');
			$action = (GETPOST('c_grupo')?'viewgr':'viewit');
		}
		else
		{
			$error++;
			setEventMessages($objectdetadd->error,$objectdetadd->errors,'errors');
			$action = (GETPOST('c_grupo')?'creategr':'viewit');
		}
	}
	if (!$error)
	{
		$db->commit();
		setEventMessages($langs->trans('Saverecord'),null,'mesgs');
		unset($_POST['refsearch']);
		unset($_POST['itemid']);
		unset($_POST['search_itemid']);
	}
	else
		$db->rollback();
}

if ($action == 'addresource')
{
	$fk_budget_task_comple = GETPOST('fk_budget_task_comple');
	if ($fk_budget_task_comple>0) $action = 'addresourcecomple';
}
if ($action == 'addresource')
{

	//parametros generales
	$general->fetch(0,$id);
	$aStruct = $aStrbudget[$id];
	if (GETPOST('cancel'))
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/budget/list.php',1);
		header("Location: ".$urltogo);
		exit;
	}

	$error=0;
		//revisar la activacion de la siguiente linea rqc
	$db->begin();
		//revisamos el producto
	$fk_unit = GETPOST('fk_unit');
	$code_structure = GETPOST('code_structure');
	$price = GETPOST('price');
	if (GETPOST('fk_product_budget'))
	{
			//se selecciono un producto del budget
		$fk_product_budget = GETPOST('fk_product_budget');
		$objprodb->fetch(GETPOST('fk_product_budget'));
		$ref_product = $objprodb->ref;
		$fk_product = $objprodb->fk_product;
		$fk_unit = $objprodb->fk_unit;
		$percent_prod = $objprodb->percent_prod;
		$amount_noprod = $objprodb->amount_noprod;
		$detail = $objprodb->label;
		$price = $objprodb->amount;
		$code_structure = $objprodb->code_structure;
		$price = $objprodb->amount;
	}
	else
	{
		/* object_prop_getpost_prop */
		$fk_product = GETPOST('product');
		$detail = GETPOST('refsearch');
		if ($fk_product>0)
		{
			$ref_product = GETPOST('search_product');
			if($product->fetch($fk_product)>0)
			{
				//buscamos la categoria a la que pertenece
				$detail = $product->label;
				$ref_product = $product->ref;
				$fk_unit = $product->fk_unit;
				$aCat = $categorie->containing($fk_product, 'product', 'id');
				foreach ((array) $aCat AS $i => $fk)
				{
					if ($aStruct['aStrcatcode'][$fk])
					{
						$code_structure = $aStruct['aStrcatcode'][$fk];
					//cambiamos para que guarde la categoria del producto
						$code_structure = $fk;
					}
				}
				if (empty($code_structure) || $code_structure <0)
				{
					setEventMessages($langs->trans('Error, el producto no tiene categoría'),null,'errors');
					$error++;
				}
			}
		}
		else
		{
			$fk_product = 0;
			$ref_product = '';
			$code_structure = GETPOST('code_structure');
			$detail = GETPOST('search_product');
		}
		$detail = STRTOUPPER($detail);
			//verificamos si existe en producto budget
		$filter = array('UPPER(ref)'=>$detail,'UPPER(label)'=>$detail);
		$filter = array(1=>1);
		$filterstatic = '';
		if ($ref_product)
		{
			$filterstatic = ' AND (';
			$filterstatic.= " UPPER(ref) = '".$ref_product."'";
		}
		if (!empty($filterstatic))
		{
			$filterstatic.= " OR ";
		}
		else
			$filterstatic.= " AND (";
		$filterstatic.= " UPPER(label) = '".$detail."'";
		$filterstatic.= ")";
		$filterstatic.= " AND t.fk_budget =".$object->id;
		$res = $objprodb->fetchAll('','',0,0,$filter,'OR',$filterstatic,true);
		if (empty($res))
		{
			if (empty($code_structure) || $code_structure <0)
			{
				setEventMessages($langs->trans('Error, el producto no tiene categoría'),null,'errors');
				$error++;
			}
			if (!$error)
			{
				$lUpdate = false;
				if (empty($ref_product)) $lUpdate = true;
				$objprodb->fk_product = $fk_product+0;
				$objprodb->fk_budget = $object->id;
				$objprodb->ref = $object->id.'|'.$ref_product;
				$objprodb->label = $detail;
				$objprodb->fk_unit = $fk_unit;
				$objprodb->code_structure = $code_structure;
				$aGroup = $aStrbudget[$id]['aStrcatgroup'];
				$group_structure = $aGroup[$code_structure];
				$objprodb->group_structure = $group_structure;
				$objprodb->quant = price2num(GETPOST('quant')+0,$general->decimal_quant);
				$objprodb->percent_prod = (GETPOST('percent_prod')?GETPOST('percent_prod'):100);
				$objprodb->amount_noprod = price2num(GETPOST('amount_noprod')+0,$general->decimal_pu);
				$objprodb->amount = $price;
				$objprodb->fk_user_create = $user->id;
				$objprodb->fk_user_mod = $user->id;
				$objprodb->date_create = $now;
				$objprodb->date_mod = $now;
				$objprodb->tms = $now;
				$objprodb->status = 1;
				$fk_product_budget = $objprodb->create($user);
				if ($fk_product_budget<=0)
				{
					setEventMessages($objprodb->error,$objprodb->errors,'errors');
					$error++;
				}
					//actualizamos si lUpdate == true
				if ($lUpdate == true)
				{
					$objprodbtmp->fetch($fk_product_budget);
					$objprodbtmp->ref .= '(PROV)'.$objprodb->id;
					$res = $objprodbtmp->update($user);
					if ($res <=0)
					{
						setEventMessages($objprodbtmp->error,$objprodbtmp->errors,'errors');
						$error++;
					}
				}
				//si se crea o se actualiza en product_budget, debemos verifica si existe en budget_task_product
				//y en budget_task_production

			}
		}
		elseif($res==1)
		{
			$fk_product_budget = $objprodb->id;
			$ref_product = $objprodb->ref;
			$fk_product = $objprodb->fk_product;
			$fk_unit = $objprodb->fk_unit;
			$detail = $objprodb->label;
			$price = $objprodb->amount;
			$code_structure = $objprodb->code_structure;
		}
		else
		{
			$error++;
			if ($res<0)
				setEventMessages($objprodb->error,$objprodb->errors,'errors');
			else
				setEventMessages($langs->trans('Existe muchos registros'),null,'errors');
		}
	}

	//vamos a agregar el insumo a items budget si no esta definido
	if (!$error)
	{
		$resd = $objectdet->fetch(GETPOST('idr'));
		if ($resd==1)
		{
			//buscamos al item
			$resitem = $items->fetch($objectdet->fk_task);
			//solo si el item esta en estado borrador 0
			if ($resitem==1 && empty($items->status))
			{
				//verificamos si existe registro en itemsproduct
				if ($objectdet->fk_task && $fk_product)
				{
					$resdet = $objItemsproduct->fetch(0,null,$objectdet->fk_task,$fk_product);
				}
				elseif($objectdet->fk_task && empty($fk_product) && !empty($detail))
				{
					$resdet = $objItemsproduct->fetch(0,null,$objectdet->fk_task,$fk_product,trim($detail));
				}
				if ($resdet==1)
				{
					//existe no se registra nada
					if ($objItemsproduct->fk_product>0 && empty($fk_product))
						$fk_product = $objitemsproduct->fk_product;
				}
				elseif(empty($resdet))
				{
					//creamos el registro
					$objItemsproduct->initAsSpecimen();
					$reftmp = generarcodigo(7);
					if(empty($fk_product)) $objItemsproduct->ref=$reftmp;
					else $objItemsproduct->ref = $ref_product;
					$objItemsproduct->fk_item=$objectdet->fk_task;
					$aGroup = $aStrbudget[$id]['aStrcatgroup'];

					$group_structure = $aGroup[$code_structure];

					$objItemsproduct->group_structure=$group_structure;
					$objItemsproduct->fk_product=$fk_product;
					if ($objItemsproduct->fk_product <=0)$objItemsproduct->fk_product=0;
					$objItemsproduct->label=$detail;
					$objItemsproduct->units=0;
					$objItemsproduct->commander=0;
					$objItemsproduct->price_productive=0;
					$objItemsproduct->price_improductive=0;
					//$objItemsproduct->formula=GETPOST('formula','alpha');
					$objItemsproduct->active=1;
					$objItemsproduct->fk_user_create=$user->id;
					$objItemsproduct->fk_user_mod=$user->id;
					$objItemsproduct->datec = $now;
					$objItemsproduct->datem = $now;
					$objItemsproduct->tms = $now;
					$objItemsproduct->status=1;

					if (empty($objItemsproduct->group_structure))
					{
						$error++;
						setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldgroup_structure")), null, 'errors');
					}
					if (empty($objItemsproduct->ref))
					{
						$error++;
						setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
					}
					if ($objItemsproduct->fk_product <=0)
					{
						//validamos que tenga valor el label
						if (empty($objItemsproduct->label))
						{
							$error++;
							setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Fieldlabel")), null, 'errors');
						}
					}
					if (! $error)
					{
						$resulttmp=$objItemsproduct->create($user);
						if ($resulttmp<= 0)
						{
							// Creation KO
							if (! empty($objItemsproduct->errors)) setEventMessages(null, $objItemsproduct->errors, 'errors');
							else  setEventMessages($objItemsproduct->error, null, 'errors');
						}
					}
					else
					{
						$action='create';
					}
				}
			}
		}
		else
		{
			$error++;
			setEventMessages($objectdet->error,$objectdet->errors,'errors');
		}
	}
	//echo '<hr>res '.$error;exit;
	if (!$error)
	{
		$objectbtr->fk_budget_task = GETPOST('idr');
		$objectbtr->ref = '(PROV)';
		$objectbtr->code_structure = $code_structure;
		$objectbtr->fk_product = $fk_product;
		$objectbtr->detail = $detail;
		$objectbtr->fk_product_budget = $fk_product_budget+0;
		$objectbtr->fk_unit = $fk_unit+0;
		$objectbtr->quant = price2num(GETPOST('quant'),$general->decimal_quant);
		$objectbtr->percent_prod = (GETPOST('percent_prod')?GETPOST('percent_prod'):100);
		$objectbtr->amount_noprod = price2num(GETPOST('amount_noprod')+0,$general->decimal_pu);
		$objectbtr->amount = price2num($price+0,$general->decimal_pu);
		$objectbtr->rang = 1;
		$objectbtr->priority = 0;
		$objectbtr->date_create = dol_now();
		$objectbtr->date_mod = dol_now();
		$objectbtr->tms = dol_now();
		$objectbtr->fk_user_create = $user->id;
		$objectbtr->fk_user_mod = $user->id;
		$objectbtr->status = 1;
		$result=$objectbtr->create($user);
		if ($result<=0)
		{
			$error++;
				// Creation KO
			setEventMessages($objectbtr->error, $objectbtr->errors, 'errors');
			$action='viewit';
			$_GET['idr'] = $_POST['idr'];
		}
	}
	if (! $error)
	{
			//actualizamos el ref
		$objectbtr->ref = '(PROV)'.$objectbtr->id;
		$resup = $objectbtr->update($user);
		if ($resup<=0)
		{
			$error++;
				// Creation KO
			setEventMessages($objectbtr->error, $objectbtr->errors, 'errors');
			$action='viewit';
			$_GET['idr'] = $_POST['idr'];
		}
			//procedemos al calculo del costo unitario
		//$sumaunit = procedure_calc($id,GETPOST('idr'));
		//$objectdetadd->fetch(0,GETPOST('idr'));
		//$objectdetadd->unit_amount = $sumaunit * $objectdetadd->unit_budget;
		//$resdet = $objectdetadd->update($user);
		//if ($resdet<=0)
		//{
		//	$error++;
		//	setEventMessages($objectdetadd->error,$objectdetadd->errors,'errors');
		//}
		if (!$error)
		{
			setEventMessages($langs->trans('Saverecord').' con valor de '.$sumaunit,null,'mesgs');
				// Creation OK
			unset($_POST['detail']);
			unset($_POST['product']);
			unset($_POST['refsearch']);
			unset($_POST['fk_product_budget']);
		}
		else
		{
			$action='viewit';
			$_GET['idr'] = $_POST['idr'];
		}
	}
	else
	{
		$error++;
		setEventMessages($objectdet->error, null, 'errors');
		$action='viewit';
		$_GET['idr'] = $_POST['idr'];
	}

	if (!$error)
	{
		$db->commit();
			//procedemos al calculo del costo unitario
		$sumaunit = $objectdetaddtmp->procedure_calculo($user,$id,GETPOST('idr'),false);
		$objectdettmp->fetch(GETPOST('idr'));
		$objectdetaddtmp->fetch(0,GETPOST('idr'));
		$objectdetaddtmp->unit_amount = $sumaunit;
		$objectdetaddtmp->total_amount = price2num($sumaunit * $objectdetaddtmp->unit_budget,$general->decimal_total);
		$resdet = $objectdetaddtmp->update_unit_amount($user);
		if ($resdet<=0)
		{
			$error++;
			setEventMessages($objectdetaddtmp->error,$objectdetaddtmp->errors,'errors');
		}
		//procedemos a actualizar el grupo
		//hasta que el task_parent este en 0
		$loop = true;
		$fk_task_parent = $objectdettmp->fk_task_parent;
		while ($loop == true)
		{
			if ($fk_task_parent > 0)
			{
				$total = $objectdetaddtmp->procedure_calculo_group($user,$id,$fk_task_parent);
				$res = $objectdetaddtmp->fetch(0,$fk_task_parent);
				if ($res == 1)
				{
					$objectdetaddtmp->total_amount = $total;
					$objectdetaddtmp->update($user);
				}
				//buscamos nuevamente en budget_task
				$res =$objectdettmp->fetch($fk_task_parent);
				if ($res == 1) $fk_task_parent = $objectdettmp->fk_task_parent;
			}
			else
				$loop = false;
		}
		//procedemos a actualizar el presupuesto
		$total = $objectdetaddtmp->procedure_calculo_budget($id);
		$res = $objecttmp->fetch($id);
		if ($res == 1)
		{
			$objecttmp->budget_amount = $total;
			$objecttmp->update($user);
		}






		setEventMessages($langs->trans('Saverecord').' con valor de '.number_format($sumaunit,$general->decimal_total),null,'mesgs');
		$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/budget/card.php?id='.$id.'&idr='.GETPOST('idr').'&action=viewit',1);
		header("Location: ".$urltogo);
		exit;
	}
	else
	{
		$db->rollback();
		$action = 'viewit';
	}
}

if ($action == 'addresourcecomple')
{
	$error =0;
	$fk_budget_task_comple = GETPOST('fk_budget_task_comple');
	//busco el item complementario
	$objectdet->fetch($fk_budget_task_comple);
	$objectdetadd->fetch(0,$fk_budget_task_comple);
	//parametros generales
	$general->fetch(0,$id);
	$aStruct = $aStrbudget[$id];
	if (GETPOST('cancel'))
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/budget/list.php',1);
		header("Location: ".$urltogo);
		exit;
	}

	$error=0;
	$fk_unit = $objectdet->fk_unit;
	$detail = $objectdet->label;
	$code_structure = -9;
	$price = $objectdetadd->unit_amount;
	$db->begin();
	if (!$error)
	{
		$objectbtr->fk_budget_task = GETPOST('idr');
		$objectbtr->ref = '(PROV)';
		$objectbtr->code_structure = $code_structure;
		$objectbtr->fk_product = 0;
		$objectbtr->detail = $detail;
		$objectbtr->fk_product_budget = 0;
		$objectbtr->fk_unit = $fk_unit+0;
		$objectbtr->quant = price2num(GETPOST('quantcomple'),$general->decimal_quant);
		$objectbtr->percent_prod = (GETPOST('percent_prod')?GETPOST('percent_prod'):100);
		$objectbtr->amount_noprod = price2num(GETPOST('amount_noprod')+0,$general->decimal_pu);
		$objectbtr->amount = price2num($price+0,$general->decimal_pu);
		$objectbtr->rang = 1;
		$objectbtr->priority = 0;
		$objectbtr->date_create = dol_now();
		$objectbtr->date_mod = dol_now();
		$objectbtr->tms = dol_now();
		$objectbtr->fk_user_create = $user->id;
		$objectbtr->fk_user_mod = $user->id;
		$objectbtr->status = 1;
		$result=$objectbtr->create($user);
		if ($result<=0)
		{
			$error++;
				// Creation KO
			setEventMessages($objectbtr->error, $objectbtr->errors, 'errors');
			$action='viewit';
			$_GET['idr'] = $_POST['idr'];
		}
	}
	if (! $error)
	{
		//actualizamos el ref
		$objectbtr->ref = '(PROV)'.$objectbtr->id;
		$resup = $objectbtr->update($user);
		if ($resup<=0)
		{
			$error++;
				// Creation KO
			setEventMessages($objectbtr->error, $objectbtr->errors, 'errors');
			$action='viewit';
			$_GET['idr'] = $_POST['idr'];
		}
		if (!$error)
		{
			setEventMessages($langs->trans('Saverecord').' con valor de '.$sumaunit,null,'mesgs');
				// Creation OK
			unset($_POST['detail']);
			unset($_POST['product']);
			unset($_POST['refsearch']);
			unset($_POST['fk_product_budget']);
		}
		else
		{
			$action='viewit';
			$_GET['idr'] = $_POST['idr'];
		}
	}
	else
	{
		$error++;
		setEventMessages($objectdet->error, null, 'errors');
		$action='viewit';
		$_GET['idr'] = $_POST['idr'];
	}

	if (!$error)
	{
		$db->commit();
			//procedemos al calculo del costo unitario
		$sumaunit = $objectdetaddtmp->procedure_calculo($user,$id,GETPOST('idr'),false);
		$objectdetaddtmp->fetch(0,GETPOST('idr'));
		$objectdetaddtmp->unit_amount = $sumaunit;
		$resdet = $objectdetaddtmp->update_unit_amount($user);
		if ($resdet<=0)
		{
			$error++;
			setEventMessages($objectdetaddtmp->error,$objectdetaddtmp->errors,'errors');
		}
		$urltogo=$backtopage?$backtopage:dol_buildpath('/budget/budget/card.php?id='.$id.'&idr='.GETPOST('idr').'&action=viewit',1);
		header("Location: ".$urltogo);
		exit;
	}
	else
	{
		$db->rollback();
		$action = 'viewit';
	}
}

if ($action == 'deleteres' && $user->rights->budget->budr->del)
{
	$objectbtr->fetch(GETPOST('idreg'));
	if (GETPOST('idr') == $objectbtr->fk_budget_task)
	{
		$error=0;
		$db->begin();
		$res = $objectbtr->delete($user);
		if ($res>0)
		{
			setEventMessages($langs->trans('Recorddeleted'),null,'mesgs');
		}
		else
		{
			$error++;
			setEventMessages($objectbtr->error,$objectbtr->errors,'errors');
		}
		if (!$error) $db->commit();
		else $db->rollback();
		if (!$error)
		{
			//procedemos al calculo del costo unitario
			$sumaunit = $objectdetaddtmp->procedure_calculo($user,$id,GETPOST('idr'),false);
			$objectdetaddtmp->fetch(0,GETPOST('idr'));
			$objectdetaddtmp->unit_amount = $sumaunit;
			$resdet = $objectdetaddtmp->update_unit_amount($user);
			if ($resdet<=0)
			{
				$error++;
				setEventMessages($objectdetaddtmp->error,$objectdetaddtmp->errors,'errors');
			}
		}
	}
	unset($_GET['idreg']);
	$action = 'viewit';
}



?>